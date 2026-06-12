<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyEsignSmsReport extends Mailable
{
    use SerializesModels;

    public string $reportDate;
    public int $totalCount;
    public array $rows;

    public function __construct(string $reportDate, int $totalCount, array $rows)
    {
        $this->reportDate = $reportDate;
        $this->totalCount = $totalCount;
        $this->rows = $rows;
    }

    public function build()
    {
        return $this->subject('Daily eSign SMS Report (' . $this->reportDate . ')')
                    ->view('emails.daily_esign_sms_report');
    }
}
