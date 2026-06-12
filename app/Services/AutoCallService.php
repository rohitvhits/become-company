<?php

namespace App\Services;

use App\Jobs\FireAutoCallJob;
use App\Model\AgencyWiseService;
use App\Model\Patient;
use App\Services\LocationMasterService;
use App\Services\PatientAutoCallLogService;
use App\Model\PatientAutoCallLog;
use App\Model\PatientAutoCallAttempt;
use Carbon\Carbon;
use App\Helpers\Utility;

class AutoCallService
{
    // Max retries before marking no_response
    const MAX_CALL_ATTEMPTS = 3;

    // Retry delay in hours between each attempt: attempt 1→2 = 1h, attempt 2→3 = 2h, attempt 3→no_response = 3h
    const RETRY_DELAYS = [1, 2, 3];

    /**
     * Record the SMS send event and schedule the auto-call 4 hours later.
     */
    public static function scheduleAfterSmsSent(
        int    $patientId,
        string $mobile,
        string $patientName,
        string $agencyName,
        string $smsLink,
        string $triggeredBy = 'savePatient',
        string $unitId = null,
        int    $agencyId = null,
        array  $serviceIds = []
    ): PatientAutoCallLog {
        $now    = Carbon::now();
        $fireAt = self::nextCallWindow($now->copy()->addHours(2));

        $log = PatientAutoCallLog::create([
            'patient_id'           => $patientId,
            'mobile'               => '1'.$mobile,
            'patient_name'         => $patientName,
            'agency_name'          => $agencyName,
            'sms_link'             => $smsLink,
            'sms_sent_at'          => $now,
            'appointment_deadline' => $fireAt,
            'call_status'          => 'pending',
            'call_attempts'        => 0,
            'triggered_by'         => $triggeredBy,
            'unit_id'              => $unitId,
            'agency_id'            => $agencyId,
            'service_id'         =>  implode(',',$serviceIds)
        ]);

        FireAutoCallJob::dispatch($log->id)->delay($fireAt);

        return $log;
    }

        public static function markSelfBooked(string $unitId): void
    {
        PatientAutoCallLog::where('unit_id', $unitId)
            ->where('call_status', 'pending')
            ->update([
                'call_status' => 'self-booked',
                'booked_at'   => Carbon::now(),
            ]);
    }
    /**
     * Mark the log as booked using unit_id (the appointment key from the SMS link).
     */
    public static function markBooked(string $unitId): void
    {
        PatientAutoCallLog::where('unit_id', $unitId)
            ->where('call_status', 'pending')
            ->update([
                'call_status' => 'booked',
                'booked_at'   => Carbon::now(),
            ]);
    }

    /**
     * Fire the ElevenLabs outbound call and update the log.
     * Returns true if the API accepted the call, false on failure.
     */
    public static function fireCall(PatientAutoCallLog $log): bool
    {
        // Fetch patient DOB directly — no auth() context in queued jobs
        $patient = Patient::select('dob', 'first_name', 'last_name')
            ->where('id', $log->patient_id)
            ->first();

        $locationList = app(LocationMasterService::class)->getWalkInList(config('services.elevenlabs.exclude_location_ids')); 
        $data = [
            'agent_id'              => config('services.elevenlabs.agent_id'),
            'agent_phone_number_id' => config('services.elevenlabs.agent_phone_number_id'),
            'to_number'             => $log->mobile,
            'conversation_initiation_client_data' => [
                'dynamic_variables' => [
                    'AgencyName'        => (string)($log->agency_name ?? ''),
                    'Patients_Name'     => (string)($log->patient_name ?? ''),
                    'patient_full_name' => $patient ? (string)($patient->first_name . ' ' . $patient->last_name) : (string)($log->patient_name ?? ''),
                    'patient_dob'       => $patient ? Utility::convertMDY($patient->dob) : '',
                    'LocationList'      => json_encode($locationList),
                    'auto_call_log_id'  => $log->id,
                    'patient_mobile'    => (string)($log->mobile ?? ''),
                    'patient_name'      => (string)($log->patient_name ?? ''),
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

        $responseData   = json_decode($response, true);
        $conversationId = $responseData['conversation_id'] ?? null;
        $success        = !$curlError && ($httpCode === 200 || $httpCode === 201);

        $log->increment('call_attempts');
        $attemptNumber = (int) $log->fresh()->call_attempts;

        $log->update([
            'call_fired_at'   => Carbon::now(),
            'call_status'     => $success ? 'called' : 'failed',
            'call_response'   => $curlError ?: $response,
            'conversation_id' => $conversationId,
        ]);

        PatientAutoCallLogService::createAttemptStatic([
            'auto_call_log_id' => $log->id,
            'call_type'        => 'initial',
            'attempt_number'   => $attemptNumber,
            'conversation_id'  => $conversationId,
            'status'           => $success ? 'called' : 'failed',
            'call_response'    => $curlError ?: $response,
            'fired_at'         => Carbon::now(),
        ]);

        return $success;
    }

    /**
     * Given a proposed fire time, push it forward to the next valid 10am–7pm window.
     * If it already falls in window, return as-is.
     * If before 10am today, return 10am today.
     * If after 7pm, return 10am next day.
     */
    public static function nextCallWindow(Carbon $proposed): Carbon
    {
        $hour = (int)$proposed->format('G');

        if ($hour >= 10 && $hour < 19) {
            return $proposed;
        }

        if ($hour < 10) {
            return $proposed->copy()->startOfDay()->addHours(10);
        }

        // after 7pm → next day 10am
        return $proposed->copy()->addDay()->startOfDay()->addHours(10);
    }
}
