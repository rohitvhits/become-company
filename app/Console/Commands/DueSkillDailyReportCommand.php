<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Services\AlayacareEmployeeDueSkillService;

class DueSkillDailyReportCommand extends Command
{
    protected $signature = 'due-skill-alayacare:daily-report';
    protected $description = 'Send daily email report of due skill records added/updated by agency';
    private $employeeDueSkillService;
    public function __construct(AlayacareEmployeeDueSkillService $employeeDueSkillService)
    {
        parent::__construct();
        $this->employeeDueSkillService = $employeeDueSkillService;
    }

    public function handle()
    {
        $today = date('Y-m-d');
        $addedByAgency = $this->employeeDueSkillService->getTotalSyncDueSkill();
        
        // Build HTML email
        $syncDate = date('Y-m-d H:i:s');
        $subject = "AlayaCare Due Skill Daily Report ({$today})";

        $htmlBody = '<html><body>';
        $htmlBody .= '<h2 style="color:#2c3e50;">AlayaCare Due Skill Daily Report</h2>';
        $htmlBody .= '<p style="color:#555;">Report generated on <strong>' . $syncDate . '</strong></p>';


        // Agency-wise breakdown
        if (!empty($addedByAgency)) {
            $htmlBody .= '<h3 style="color:#2c3e50;">Agency-wise Breakdown</h3>';
            $htmlBody .= '<table style="width:100%;border-collapse:collapse;margin:10px 0;">';
            $htmlBody .= '<tr style="background-color:#2c3e50;color:#fff;">';
            $htmlBody .= '<th style="padding:8px;text-align:left;border:1px solid #ddd;">Agency Name</th>';
            $htmlBody .= '<th style="padding:8px;text-align:center;border:1px solid #ddd;">Added</th>';
            $htmlBody .= '<th style="padding:8px;text-align:center;border:1px solid #ddd;">Updated</th>';
            $htmlBody .= '</tr>';

            $row = 0;
            foreach ($addedByAgency as  $summary) {
                
                $bgColor = $row % 2 === 0 ? '#fff' : '#f9f9f9';
                $added = $summary->new_records ?? 0;
                $updated = $summary->synced_records ?? 0;
             
                $name = $summary->agency_name ?? 'Unknown';

                $htmlBody .= '<tr style="background-color:' . $bgColor . ';">';
                $htmlBody .= '<td style="padding:8px;border:1px solid #ddd;">' . $name . '</td>';
                $htmlBody .= '<td style="padding:8px;text-align:center;border:1px solid #ddd;color:#28a745;">' . $added . '</td>';
                $htmlBody .= '<td style="padding:8px;text-align:center;border:1px solid #ddd;color:#007bff;">' . $updated . '</td>';
                
                $htmlBody .= '</tr>';
                $row++;
            }
            $htmlBody .= '</table>';
        } else {
            $htmlBody .= '<p style="color:#999;">No due skill records were added or updated today.</p>';
        }

        $htmlBody .= '<p style="color:#999;font-size:12px;margin-top:20px;">This is an automated email from NYBEST Medical - AlayaCare Due Skill Daily Report.</p>';
        $htmlBody .= '</body></html>';

        try {
            Mail::mailer('second')->send([], [], function ($message) use ($subject, $htmlBody) {
                $message->to('vishaldpatel.vhits@gmail.com')
                    ->subject($subject)
                    ->html($htmlBody);
            });
            $this->info("Daily report email sent successfully.");
        } catch (\Exception $e) {
            \Log::error("Failed to send Due Skill daily report email: " . $e->getMessage());
            $this->error("Failed to send daily report email: " . $e->getMessage());
        }
    }
}
