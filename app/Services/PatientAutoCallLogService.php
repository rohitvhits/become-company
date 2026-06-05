<?php

namespace App\Services;

use App\Model\PatientAutoCallAttempt;
use App\Model\PatientAutoCallLog;
use Carbon\Carbon;

class PatientAutoCallLogService
{
    public function getFilteredQuery(array $filters = [])
    {
        $query = PatientAutoCallLog::orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', '%' . $search . '%')
                  ->orWhere('mobile', 'like', '%' . $search . '%')
                  ->orWhere('agency_name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['call_status'])) {
            $query->where('call_status', $filters['call_status']);
        }

        if (isset($filters['verified']) && $filters['verified'] !== '') {
            $query->where('admin_verified', $filters['verified']);
        }

        if (isset($filters['converted']) && $filters['converted'] !== '') {
            $query->where('converted_to_appointment', $filters['converted']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('m/d/Y', $filters['date_from'])->format('Y-m-d'));
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('m/d/Y', $filters['date_to'])->format('Y-m-d'));
        }

        if (!empty($filters['agency_id'])) {
            $query->whereIn('agency_id', (array) $filters['agency_id']);
        }

        if (!empty($filters['location_id'])) {
            $query->whereHas('callAppointment', function ($q) use ($filters) {
                $q->whereIn('call_appointments.location_id', (array) $filters['location_id']);
            });
        }

        return $query;
    }

    public function find(int $id)
    {
        return PatientAutoCallLog::find($id);
    }

    public function findOrFail(int $id)
    {
        return PatientAutoCallLog::findOrFail($id);
    }

    public function findWithAppointment(int $id)
    {
        return PatientAutoCallLog::with('callAppointment')->findOrFail($id);
    }

    public function getDetailById(int $id)
    {
        return PatientAutoCallLog::leftJoin('patient_master', 'patient_master.id', '=', 'patient_auto_call_logs.patient_id')
            ->select(
                'patient_auto_call_logs.*',
                'patient_master.id as patient_db_id',
                'patient_master.first_name',
                'patient_master.last_name',
                'patient_master.mobile as patient_mobile_db',
                'patient_master.phone as patient_phone',
                'patient_master.dob'
            )
            ->where('patient_auto_call_logs.id', $id)
            ->firstOrFail();
    }

    public function createAttempt(array $data)
    {
        return PatientAutoCallAttempt::create($data);
    }

    public static function createAttemptStatic(array $data)
    {
        return PatientAutoCallAttempt::create($data);
    }

    public function getAttemptsByLogId(int $logId)
    {
        return PatientAutoCallAttempt::where('auto_call_log_id', $logId)
            ->orderBy('call_type')
            ->orderBy('attempt_number')
            ->get();
    }

    public function getByPatientId(int $patientId)
    {
        return PatientAutoCallLog::where('patient_id', $patientId)
            ->with(['callAppointment.location', 'attempts'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingReminderLogs(array $agencyIds, Carbon $today, Carbon $inThree, int $maxAttempts)
    {
        return PatientAutoCallLog::whereNotNull('mobile')
            ->whereIn('agency_id', $agencyIds)
            ->whereNull('reminder_call_fired_at')
            ->where('reminder_call_attempts', '<', $maxAttempts)
            ->whereHas('callAppointment', function ($q) use ($today, $inThree) {
                $q->whereDate('date', '>=', $today)
                  ->whereDate('date', '<=', $inThree);
            })
            ->with('callAppointment')
            ->get();
    }

    public function getStats(): array
    {
        return [
            'pending'     => PatientAutoCallLog::where('call_status', 'pending')->count(),
            'called'      => PatientAutoCallLog::where('call_status', 'called')->count(),
            'booked'      => PatientAutoCallLog::where('call_status', 'booked')->count(),
            'verified'    => PatientAutoCallLog::where('admin_verified', 1)->count(),
            'converted'   => PatientAutoCallLog::where('converted_to_appointment', 1)->count(),
            'failed'      => PatientAutoCallLog::where('call_status', 'failed')->count(),
            'no_response' => PatientAutoCallLog::where('call_status', 'no_response')->count(),
        ];
    }
}
