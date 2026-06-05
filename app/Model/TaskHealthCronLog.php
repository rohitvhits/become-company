<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Agency;
use App\Model\Patient;
use App\Model\TaskHealthMaster;

class TaskHealthCronLog extends Model
{
    protected $table = 'task_health_cron_log';
    protected $guarded = ['id'];

    public function agencyDetails()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function patientDetails()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function taskHealthMaster()
    {
        return $this->belongsTo(TaskHealthMaster::class, 'task_health_id', 'id');
    }
}
