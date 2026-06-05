<?php

namespace App\Console\Commands;

use App\Model\TaskHealthCronLog;
use App\SiteSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TaskHealthCronDailySummary extends Command
{
    protected $signature = 'report:task-health-cron-daily-summary
                            {--date= : Override the report date (Y-m-d). Defaults to yesterday.}';

    protected $description = 'Send a daily email summary of Task Health HHA Link cron results.';

    public function handle()
    {
        $setting = SiteSetting::where('del_flag', 'N')->orderBy('id', 'desc')->first();

        if (!$setting || $setting->task_health_cron_enabled == 0) {
            $this->info('Task Health cron is disabled — skipping daily summary.');
            return;
        }

        // Resolve recipient
        $recipient = 'riddhitrivedi.vhits@gmail.com';//env('TASK_HEALTH_REPORT_EMAIL');
        if (empty($recipient)) {
            $this->error('TASK_HEALTH_REPORT_EMAIL is not set in .env — cannot send summary.');
            return;
        }

        // Resolve report date (yesterday by default)
        $dateOption = $this->option('date');
        if ($dateOption) {
            $reportDate = Carbon::createFromFormat('Y-m-d', $dateOption);
        } else {
            $reportDate = Carbon::yesterday();
        }

        $dateLabel = $reportDate->format('m/d/Y');
        $start     = $reportDate->copy()->startOfDay();
        $end       = $reportDate->copy()->endOfDay();

        // Pull all logs for the period, eager-load relationships
        $logs = TaskHealthCronLog::with([
                'patientDetails:id,first_name,last_name',
                'agencyDetails:id,agency_name',
                'taskHealthMaster:id,task_id',
            ])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('id', 'asc')
            ->get();

        if ($logs->isEmpty()) {
            $this->info("No task health cron log entries found for {$dateLabel}. No email sent.");
            return;
        }

        // Aggregate counts
        $total   = $logs->count();
        $byType  = $logs->groupBy('type');
        $success = isset($byType['success']) ? $byType['success']->count() : 0;
        $error   = isset($byType['error'])   ? $byType['error']->count()   : 0;
        $skipped = isset($byType['skipped']) ? $byType['skipped']->count() : 0;
        $other   = $total - $success - $error - $skipped;

        $summary = [
            'total'   => $total,
            'success' => $success,
            'error'   => $error,
            'skipped' => $skipped,
            'other'   => $other,
        ];

        // Build row arrays for the email view (avoid passing Eloquent objects into the Mailable)
        $errorRows   = $this->buildRows($byType['error']   ?? collect());
        $successRows = $this->buildRows($byType['success'] ?? collect());
        $skippedRows = $this->buildRows($byType['skipped'] ?? collect());

        $formattedData = [
           'dateLabel' => $dateLabel,
            'summary' => $summary,
            'errorRows' => $errorRows,
            'successRows' => $successRows,
            'skippedRows' => $skippedRows,
            'reportDate' => $reportDate
        ];

        $recipients = array_filter(array_map('trim', explode(',', $recipient)));
        $emailContent = view('emails.task_health_cron_daily_summary', $formattedData)->render();
        
        foreach ($recipients as $to) {
             Mail::send([], [], function ($message) use ($recipients, $reportDate,$emailContent) {
                $message->to($recipients)
                    ->subject('Task Health HHA Link - Daily Summary (' . $reportDate . ')')
                    ->setBody($emailContent, 'text/html');
            });
        }

        $this->info("Daily summary for {$dateLabel} sent to: " . implode(', ', $recipients));
    }

    /**
     * Convert a collection of log records to plain associative arrays safe for mail views.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @return array
     */
    private function buildRows($collection)
    {
        $rows = [];

        foreach ($collection as $row) {
            $patient    = $row->patientDetails;
            $master     = $row->taskHealthMaster;
            $agency     = $row->agencyDetails;

            $patientName = $patient
                ? trim($patient->first_name . ' ' . $patient->last_name)
                : null;

            $rows[] = [
                'id'           => $row->id,
                'task_id'      => $master ? $master->task_id : null,
                'task_health_id' => $row->task_health_id,
                'patient_id'   => $row->patient_id,
                'patient_name' => $patientName,
                'agency_name'  => $agency ? $agency->agency_name : null,
                'message'      => $row->message,
                'created_at'   => $row->created_at
                    ? date('m/d/Y h:i A', strtotime($row->created_at))
                    : null,
            ];
        }

        return $rows;
    }
}
