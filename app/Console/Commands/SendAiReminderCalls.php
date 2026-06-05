<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AutoCallService;
use App\Services\AgencyService;
use App\Services\PatientAutoCallLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\Utility;

class SendAiReminderCalls extends Command
{
    // Max reminder call attempts before marking no_response on the reminder
    const MAX_REMINDER_ATTEMPTS = 3;

    protected $signature = 'ai-calls:send-reminders
                            {--dry-run : Show what would be called without actually firing}';

    protected $description = 'Fire ElevenLabs reminder calls for AI-booked appointments coming within 3 days (10am–7pm only)';

    protected AgencyService $agencyService;
    protected PatientAutoCallLogService $autoCallLogService;

    public function __construct(AgencyService $agencyService, PatientAutoCallLogService $autoCallLogService)
    {
        parent::__construct();
        $this->agencyService      = $agencyService;
        $this->autoCallLogService = $autoCallLogService;
    }

    public function handle()
    {
        $now  = Carbon::now();
        $hour = (int)$now->format('G');

        if ($hour < 10 || $hour >= 19) {
            info('Outside call window (10am–7pm). Nothing to do.');
            return 0;
        }

        $dryRun  = $this->option('dry-run');
        $today   = Carbon::today();
        $inThree = Carbon::today()->addDays(3);

        if ($dryRun) {
            $this->info('DRY RUN — no calls will be fired');
        }

        // Agencies with AI Call Logs enabled
        $enabledAgencyIds = $this->agencyService->getAiCallEnabledAgencyIds();

        // Eligible: linked appointment within 3 days, not yet succeeded, under max attempts
        $logs = $this->autoCallLogService->getPendingReminderLogs(
            $enabledAgencyIds,
            $today,
            $inThree,
            self::MAX_REMINDER_ATTEMPTS
        );

        $this->info("Found {$logs->count()} eligible log(s) to call.");

        $fired  = 0;
        $failed = 0;

        foreach ($logs as $log) {
            $linked         = $log->callAppointment;
            $attemptNumber  = (int)$log->reminder_call_attempts + 1;

            $this->line("  Processing log #{$log->id} — {$log->patient_name} ({$log->mobile}) attempt #{$attemptNumber}");

            if ($dryRun) {
                $fired++;
                continue;
            }

            $data = [
                'agent_id'              => config('services.elevenlabs.reminder_agent_id'),
                'agent_phone_number_id' => config('services.elevenlabs.agent_phone_number_id'),
                'to_number'             => $log->mobile,
                'conversation_initiation_client_data' => [
                    'dynamic_variables' => [
                        'patient_name'     => (string)($log->patient_name ?? ''),
                        'Patients_Name'    => (string)($log->patient_name ?? ''),
                        'AgencyName'       => (string)($log->agency_name ?? ''),
                        'appointment_date' => Utility::convertMDY($linked->date),
                        'appointment_time' => (string)($linked->time_slot ?? ''),
                        'auto_call_log_id' => (string)$log->id,
                        'patient_mobile'   => (string)($log->mobile ?? ''),
                    ],
                ],
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => config('services.elevenlabs.outbound_call_url'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'xi-api-key: ' . config('services.elevenlabs.api_key'),
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_TIMEOUT    => 30,
            ]);

            $response  = curl_exec($curl);
            $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            $success = !$curlError && ($httpCode === 200 || $httpCode === 201);

            // Always increment attempt counter
            $log->increment('reminder_call_attempts');
            $log->refresh();

            if ($success) {
                $log->reminder_call_fired_at = Carbon::now();
                $log->save();
                $this->line("  FIRED #{$log->id} — {$log->patient_name}");
                Log::info("AiReminderCall FIRED log #{$log->id} — {$log->patient_name}");
                $fired++;
            } else {
                $this->error("  FAILED #{$log->id} (attempt {$attemptNumber}): " . ($curlError ?: "HTTP {$httpCode}"));
                Log::error("AiReminderCall FAILED log #{$log->id} attempt {$attemptNumber}: " . ($curlError ?: "HTTP {$httpCode}"));

                if ($log->reminder_call_attempts >= self::MAX_REMINDER_ATTEMPTS) {
                    // Mark no_response on the reminder — using notes to flag it
                    $log->notes = trim(($log->notes ?? '') . "\n[Reminder] No response after " . self::MAX_REMINDER_ATTEMPTS . " attempts.");
                    $log->save();
                    $this->warn("  → Max reminder attempts reached for log #{$log->id} — marked no response.");
                    Log::warning("AiReminderCall no_response for log #{$log->id} after " . self::MAX_REMINDER_ATTEMPTS . " attempts.");
                }

                $failed++;
            }
        }

        $this->info("Done. Fired: {$fired} | Failed/Retrying: {$failed}");
        Log::info("AiReminderCalls complete — Fired: {$fired}, Failed: {$failed}");

        return $failed > 0 ? 1 : 0;
    }
}
