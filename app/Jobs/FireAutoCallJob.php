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

class FireAutoCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $logId;

    public function __construct(int $logId)
    {
        $this->logId = $logId;
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

        // Already resolved — skip
        if (in_array($log->call_status, ['self-booked','booked', 'no_response'])) return;

        $attemptNumber = (int)$log->call_attempts + 1;

        AutoCallService::fireCall($log);

        // ElevenLabs accepted the call — no retry needed (patient may or may not answer,
        // but the call was dispatched successfully). Only retry on API failure.
        $log->refresh();
        if ($log->call_status === 'called') return;

        // API failed — decide retry or give up
        if ($attemptNumber >= AutoCallService::MAX_CALL_ATTEMPTS) {
            $log->update(['call_status' => 'no_response']);
            return;
        }

        // Schedule next retry: delays are [1h, 2h] for attempts 1→2, 2→3
        $delayHours = AutoCallService::RETRY_DELAYS[$attemptNumber - 1] ?? 2;
        $fireAt     = AutoCallService::nextCallWindow(Carbon::now()->addHours($delayHours));

        static::dispatch($this->logId)->delay($fireAt);
    }
}
