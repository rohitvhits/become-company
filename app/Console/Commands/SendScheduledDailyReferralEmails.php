<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\DailyReferralEmailSchedule;
use App\Model\DailyReferralEmailLog;
use App\Agency;
use App\Master;
use App\Services\DailyReferralEmailReportService;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendScheduledDailyReferralEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-referral:send-scheduled
                          {--force : Force send even if already sent today}
                          {--schedule-id= : Send specific schedule by ID}
                          {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled daily referral emails';

    /**
     * @var DailyReferralEmailReportService
     */
    protected $reportService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DailyReferralEmailReportService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');
        $scheduleId = $this->option('schedule-id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('📋 DRY RUN MODE - No emails will actually be sent');
        }

        // Get schedules to process
        $query = DailyReferralEmailSchedule::shouldRunToday()->with('createdBy');

        if ($scheduleId) {
            $query->where('id', $scheduleId);
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {

            return 0;
        }

        $successCount = 0;
        $errorCount = 0;
   
        foreach ($schedules as $schedule) {
            try {
                if ($this->processSchedule($schedule, $force, $dryRun)) {
                    $successCount++;
                } else {
                    $this->warn("⏭️  Skipped schedule: {$schedule->name}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ Error processing schedule '{$schedule->name}': " . $e->getMessage());
            }
        }


        if ($errorCount > 0) {
            $this->error("❌ Errors: {$errorCount}");
        }

        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Process a single schedule
     */
    protected function processSchedule(DailyReferralEmailSchedule $schedule, $force = false, $dryRun = false)
    {
 
        // Check if already sent today (unless forced)
        if (!$force && $schedule->alreadySentToday()) {

            $this->warn("⏭️  Already sent today, skipping...");
            return false;
        }

        // Check if it's time to send
        $shouldRun = $schedule->shouldRunNow();

        if (!$force && !$shouldRun) {
            $currentTime = now()->format('H:i');
            $this->warn("⏰ Not time to send yet. Current: {$currentTime}, Scheduled: {$schedule->send_time}");
            return false;
        }

        if ($dryRun) {

            if ($schedule->cc_emails) {
                //info("📧 Would CC: " . implode(', ', $schedule->cc_emails));
            }

            return true;
        }

        // Generate report based on frequency
        $dates = $this->getReportDatesForFrequency($schedule->frequency);
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        $reportDateFormatted = $dates['formatted'];


        // Get filter parameters from schedule
        $agencyIds = $schedule->agency_ids ?? [];
        $serviceIds = $schedule->service_ids ?? [];
        $assignedTo = $schedule->assigned_to ?? [];
        $medicationList = $schedule->medication_list ?? '';
        $insuranceElg = $schedule->insurance_elg ?? '';
        $mdoTag = $schedule->mdo_tag ?? '';
        $branchIds = $schedule->branch_ids ?? [];

        // Generate report data with filters
        $reportData = $this->reportService->generateReportData(
            $startDate,
            $endDate,
            $agencyIds,
            $serviceIds,
            $assignedTo,
            $medicationList,
            $insuranceElg,
            $mdoTag,
            $branchIds
        );
        $formattedData = $this->reportService->formatReportForEmail($reportData, $reportDateFormatted);

        // Add filter information to email data if filters are applied
        if (!empty($agencyIds) || !empty($serviceIds)) {
            $formattedData['filters'] = [
                'agencies' => !empty($agencyIds) ? Agency::whereIn('id', $agencyIds)->pluck('agency_name')->toArray() : [],
                'services' => !empty($serviceIds) ? Master::whereIn('id', $serviceIds)->pluck('name')->toArray() : []
            ];
        }

        // Render email content
        $emailContent = view('emails.daily_referral_report', $formattedData)->render();

        // Prepare recipients
        $recipients = $schedule->recipients;
        $ccEmails = $schedule->cc_emails ?? [];

        // Always include pinak@nybestmedical.com in CC
        $defaultCcEmails = ['pinak@nybestmedical.com', 'developer@nybestmedical.com', 'marina@nybestmedical.com'];
        foreach ($defaultCcEmails as $defaultEmail) {
            if (!in_array($defaultEmail, $ccEmails)) {
                $ccEmails[] = $defaultEmail;
            }
        }
    
        // Create email log entry
        $emailLog = DailyReferralEmailLog::create([
            'report_date' => $reportDateFormatted,
            'email_subject' => $schedule->email_subject,
            'email_recipients' => array_merge($recipients, $ccEmails),
            'report_data' => array_merge($reportData, [
                'filters' => [
                    'agency_ids' => $agencyIds,
                    'service_ids' => $serviceIds,
                    'assigned_to' => $assignedTo,
                    'medication_list' => $medicationList,
                    'insurance_elg' => $insuranceElg,
                    'mdo_tag' => $mdoTag,
                    'branch_ids' => $branchIds
                ]
            ]),
            'email_content' => $emailContent,
            'status' => 'pending',
            'created_by' => $schedule->created_by,
            'created_date' => now()
        ]);


        try {
            // Send email
            Mail::send([], [], function ($message) use ($recipients, $ccEmails, $schedule, $emailContent) {
                $message->to($recipients)
                    ->cc($ccEmails)
                    ->subject($schedule->email_subject)
                    ->setBody($emailContent, 'text/html');
            });

            // Update log as sent
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            // Mark schedule as sent
            $schedule->markAsSent();

            if (!empty($ccEmails)) {
                // $this->info("📧 CC: " . implode(', ', $ccEmails));
            }

            return true;
        } catch (\Exception $mailException) {
            // Update log as failed
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $mailException->getMessage()
            ]);

            $this->error("❌ Failed to send email: " . $mailException->getMessage());
            return false;
        }
    }

    /**
     * Get report dates based on frequency
     */
    private function getReportDatesForFrequency($frequency)
    {
        switch ($frequency) {
            case 'weekly':
                $endDate = Carbon::yesterday();
                $startDate = $endDate->copy()->subDays(6); // Last 7 days
                return [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'formatted' => $startDate->format('m/d/Y') . ' - ' . $endDate->format('m/d/Y')
                ];
            case 'monthly':
                $endDate = Carbon::yesterday();
                $startDate = $endDate->copy()->subDays(29); // Last 30 days
                return [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'formatted' => $startDate->format('m/d/Y') . ' - ' . $endDate->format('m/d/Y')
                ];
            case 'daily':
            default:
                $reportDate = Carbon::yesterday();
                return [
                    'start' => $reportDate->format('Y-m-d'),
                    'end' => $reportDate->format('Y-m-d'),
                    'formatted' => $reportDate->format('m/d/Y')
                ];
        }
    }
}
