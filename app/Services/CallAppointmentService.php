<?php

namespace App\Services;

use App\Model\CallAppointment;
use Illuminate\Support\Facades\DB;

class CallAppointmentService
{
    private function baseQuery()
    {
        return CallAppointment::leftJoin('location_master', 'location_master.id', '=', 'call_appointments.location_id')
            ->leftJoin('users', 'users.id', '=', 'call_appointments.nurse_id')
            ->select(
                'call_appointments.*',
                'location_master.location_name',
                DB::raw("TRIM(CONCAT(COALESCE(users.first_name,''), ' ', COALESCE(users.last_name,''))) as nurse_name")
            );
    }

    public function getByAutoCallLogId(int $autoCallLogId)
    {
        return $this->baseQuery()
            ->where('call_appointments.auto_call_log_id', $autoCallLogId)
            ->first();
    }

    public function getById(int $id)
    {
        return $this->baseQuery()
            ->where('call_appointments.id', $id)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return CallAppointment::create($data);
    }
}
