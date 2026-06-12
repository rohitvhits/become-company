<?php

namespace App\Jobs;

use App\Services\AgencyService;
use App\Services\AutoCallService;
use App\Services\PatientAutoCallLogService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Model\PatientAutoCallAttempt;

class FireAutoCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $logId;
    public bool $isRetryCheck; // true = this run is checking transcript, not firing

    public function __construct(int $logId, bool $isRetryCheck = false)
    {
        $this->logId        = $logId;
        $this->isRetryCheck = $isRetryCheck;
    }

    public function handle(PatientAutoCallLogService $autoCallLogService, AgencyService $agencyService): void
    {
        $log = $autoCallLogService->find($this->logId);

        if (!$log) return;

        // Skip if agency has AI Call Logs disabled
        if ($log->agency_id) {
            $agency = $agencyService->getDetailsById($log->agency_id);
            if (!$agency || empty($agency->ai_call_logs_enabled)) return;
        }

        // Already resolved — never retry
        if (in_array($log->call_status, ['self-booked', 'booked', 'no_response'])) return;

        // ── Retry-check mode ──────────────────────────────────────────────────
        // This run was scheduled after a previous call was fired. Check whether
        // ElevenLabs produced a transcript. No transcript = patient never answered.
        if ($this->isRetryCheck) {
            $latestAttempt = PatientAutoCallAttempt::where('auto_call_log_id', $log->id)
                ->orderByDesc('attempt_number')
                ->first();

            $hasTranscript = $latestAttempt
                && !empty($latestAttempt->transcript)
                && $latestAttempt->transcript !== 'null';

            if ($hasTranscript) {
                // Patient answered — nothing to do
                return;
            }

            // No transcript → patient did not answer. Decide retry or give up.
            $attempts = (int) $log->call_attempts;

            if ($attempts >= AutoCallService::MAX_CALL_ATTEMPTS) {
                $log->update(['call_status' => 'no_response']);
                return;
            }

            // Reset to pending so fireCall() can run
            $log->update(['call_status' => 'pending']);
        }

        // ── Fire mode ─────────────────────────────────────────────────────────
        if ($log->call_status !== 'pending') return;

        $attemptNumber = (int) $log->call_attempts + 1;

        AutoCallService::fireCall($log);

        $log->refresh();

        if ($log->call_status === 'failed') {
            // ElevenLabs API itself failed — retry immediately if attempts remain
            if ($attemptNumber >= AutoCallService::MAX_CALL_ATTEMPTS) {
                $log->update(['call_status' => 'no_response']);
            } else {
                $delayHours = AutoCallService::RETRY_DELAYS[$attemptNumber - 1] ?? 2;
                $fireAt     = AutoCallService::nextCallWindow(Carbon::now()->addHours($delayHours));
                static::dispatch($this->logId)->delay($fireAt);
            }
            return;
        }

        // Call was dispatched (status = 'called'). Schedule a transcript check
        // after 1 hour — enough time for ElevenLabs to complete and post the transcript.
        // If no transcript by then, the patient did not answer and we retry.
        $delayHours = AutoCallService::RETRY_DELAYS[$attemptNumber - 1] ?? 2;
        $checkAt    = AutoCallService::nextCallWindow(Carbon::now()->addHours($delayHours));

        static::dispatch($this->logId, true)->delay($checkAt);
    }
}
