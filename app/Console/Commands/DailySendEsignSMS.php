<?php

namespace App\Console\Commands;

use App\Model\EsignImportDetail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\EsignImportDetailService;
use App\Helpers\Utility;

class DailySendEsignSMS extends Command
{
    protected $signature = 'esignSMSReport:daily-sms-report
                            {--date= : Override the report date (Y-m-d). Defaults to yesterday.}';

    protected $description = 'Send a daily email report of eSign SMS records with import_status Success.';
    protected $esignImportDetailService;

    public function __construct(
        EsignImportDetailService $esignImportDetailService
    ) {
        parent::__construct();
        $this->esignImportDetailService = $esignImportDetailService;
    }
    public function handle()
    {
        
        $recipient = "vishaldpatel.vhits@gmail.com";
        if (empty($recipient)) {
            return;
        }

        $dateOption = $this->option('date');
         if ($dateOption) {
            $reportDate = Carbon::createFromFormat('Y-m-d', $dateOption);
        } else {
            $reportDate = Carbon::yesterday();
        }

        $dateLabel = $reportDate->format('m/d/Y');

        $records = $this->esignImportDetailService->fetchCronSMSDetails($reportDate);
        
        $totalCount = $records->count();
       
        $rows = $this->buildRows($records);

        $formattedData = [
            'reportDate' => $dateLabel,
            'totalCount' => $totalCount,
            'rows' => $rows,
        ];

        $recipients = array_filter(array_map('trim', explode(',', $recipient)));
        $emailContent = view('emails.daily_esign_sms_report', $formattedData)->render();

        try {
            Mail::send([], [], function ($message) use ($recipients, $dateLabel, $emailContent) {
                $message->to($recipients)
                    ->subject('Daily eSign SMS Report (' . $dateLabel . ')')
                    ->html($emailContent);
            });

        } catch (\Exception $e) {
            info($e->getMessage());
        }
    }

    private function buildRows($records)
    {
        $rows = [];

        foreach ($records as $record) {
            $rows[] = [
                'id' => $record->patient_id,
                'name' => trim(($record->patientDetail->first_name ?? '') . ' ' . ($record->patientDetail->last_name ?? '')),
                'mobile' => $record->mobile ?? '-',
                'message' => $record->message ?? '-',
                'sms_status' => $record->sms_status ?? '-',
                'sms_date' => $record->sms_date
                    ? Utility::convertMDYTime($record->sms_date)
                    : '-',
                'created_date' => $record->created_at
                    ? Utility::convertMDYTime($record->created_at)
                    : '-',
                'error_message' => $record->error_message,
            ];
        }

        return $rows;
    }
}
