<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PatientAutoCallAttempt extends Model
{
    protected $table = 'patient_auto_call_attempts';

   protected $guarded = ["id"];

    protected $casts = [
        'fired_at' => 'datetime',
    ];

    public function callLog()
    {
        return $this->belongsTo(PatientAutoCallLog::class, 'auto_call_log_id');
    }
}
