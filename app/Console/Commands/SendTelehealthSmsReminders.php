<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Patient;
use App\Helpers\ResolutionSmsHelper;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendTelehealthSmsReminders extends Command
{
    protected $signature = 'telehealth:send-sms-reminders
                            {--dry-run : Show what would be sent without actually sending}
                            {--chunk=100 : Number of records to process per batch}';

    protected $description = 'Send SMS reminders to patients whose telehealth appointment is scheduled for today';

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $dryRun    = $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');
        $today     = Carbon::today()->format('Y-m-d');
        $status    = 'Appt reminder';

        if ($dryRun) {
            $this->info('DRY RUN MODE - No SMS will be sent');
        }

        $this->info("Processing telehealth SMS reminders for: {$today}");

        $sent    = 0;
        $failed  = 0;
        $skipped = 0;

        // chunk() avoids loading 7-8 lakh rows into memory at once
        Patient::select('id', 'first_name', 'last_name', 'mobile', 'phone')
            ->whereNotNull('telehealth_date_time')
            ->whereDate('telehealth_date_time', $today)
            ->where('deleted_flag', 'N')
            ->orderBy('id')
            ->chunk($chunkSize, function ($patients) use ($dryRun, $status, &$sent, &$failed, &$skipped) {

                foreach ($patients as $patient) {
                    $fullName = trim("{$patient->first_name} {$patient->last_name}");

                    try {
                        $statusWiseSmsSend = ResolutionSmsHelper::statusWiseSmsSend($status, $patient->id);

                        $numbers = array_unique(array_filter([$statusWiseSmsSend['mobile'] ?? '', $statusWiseSmsSend['phone'] ?? '']));

                        if (empty($statusWiseSmsSend['message']) || empty($numbers)) {
                            $this->warn("  SKIPPED: Patient #{$patient->id} ({$fullName}) — no mobile/phone or template.");
                            Log::info("TelehealthSMS SKIPPED: Patient #{$patient->id} — no mobile/phone or template.");
                            $skipped++;
                            continue;
                        }

                        if ($dryRun) {
                            $this->line("  [DRY RUN] #{$patient->id} ({$fullName}) → " . implode(', ', $numbers));
                            $this->line("            {$statusWiseSmsSend['message']}");
                            $sent++;
                            continue;
                        }

                        $anySent = false;
                        foreach ($numbers as $number) {
                            $result = $this->smsService->AgencyWiseSmsDynamic($patient->id, $number, $statusWiseSmsSend['message']);
                            if ($result) {
                                $this->line("  SENT: #{$patient->id} ({$fullName}) → {$number}");
                                Log::info("TelehealthSMS SENT: Patient #{$patient->id} → {$number}");
                                $anySent = true;
                            }
                        }

                        if ($anySent) {
                            $sent++;
                        } else {
                            $this->warn("  FAILED: #{$patient->id} ({$fullName}) — SMS not sent.");
                            Log::warning("TelehealthSMS FAILED: Patient #{$patient->id}");
                            $failed++;
                        }

                    } catch (\Exception $e) {
                        $this->error("  ERROR: #{$patient->id} ({$fullName}): " . $e->getMessage());
                        Log::error("TelehealthSMS ERROR: Patient #{$patient->id} — " . $e->getMessage());
                        $failed++;
                        // continue to next patient instead of crashing entire job
                    }
                }
            });

        $this->info("----------------------------------------");
        $this->info("Done. Sent: {$sent} | Skipped: {$skipped} | Failed: {$failed}");
        Log::info("TelehealthSMS complete — Sent: {$sent}, Skipped: {$skipped}, Failed: {$failed}");

        return $failed > 0 ? 1 : 0;
    }
}
