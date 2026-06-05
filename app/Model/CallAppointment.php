<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\LocationMaster;
use App\Model\LocationSchedule;

class CallAppointment extends Model
{
    use SoftDeletes;
    protected $table = "call_appointments";
    protected $guarded = ["id"];

    protected $casts = [
        'admin_verified'           => 'boolean',
        'admin_verified_at'        => 'datetime',
        'converted_to_appointment' => 'boolean',
        'converted_at'             => 'datetime',
        'confirmation_sms_sent'    => 'boolean',
        'reminder_sms_sent'        => 'boolean',
        'called_at'                => 'datetime',
    ];

    public function autoCallLog()
    {
        return $this->belongsTo(PatientAutoCallLog::class, 'auto_call_log_id');
    }

    public function location()
    {
        return $this->belongsTo(LocationMaster::class, 'location_id');
    }

    public function getTimeSlotDisplayAttribute(): string
    {
        $slot = $this->time_slot;
        if (!$slot) return '-';
        if (!is_numeric($slot)) return $slot;

        $row = LocationSchedule::find((int) $slot);
        return ($row && $row->start_time) ? date('h:i A', strtotime($row->start_time)) : $slot;
    }
}
