<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PatientAutoCallLog extends Model
{
    protected $table = 'patient_auto_call_logs';

  protected $guarded = ['id'];

    public function callAppointment()
    {
        return $this->hasOne(CallAppointment::class, 'auto_call_log_id');
    }

    public function attempts()
    {
        return $this->hasMany(PatientAutoCallAttempt::class, 'auto_call_log_id')->orderBy('call_type')->orderBy('attempt_number');
    }

    protected $casts = [
        'sms_sent_at'              => 'datetime',
        'appointment_deadline'     => 'datetime',
        'booked_at'                => 'datetime',
        'call_fired_at'            => 'datetime',
        'admin_verified'           => 'boolean',
        'admin_verified_at'        => 'datetime',
        'converted_to_appointment' => 'boolean',
        'converted_at'             => 'datetime',
        'confirmation_sms_sent'    => 'boolean',
        'reminder_sms_sent'        => 'boolean',
        'reminder_call_fired_at'   => 'datetime',
    ];
}
