<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Model\Patient;
use App\Agency;

class InflowcarePatientLogs extends Model
{
    use Notifiable;

    protected $guarded = ["id"];

    public function userDetail(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function patient(){
        return $this->belongsTo(Patient::class,'patient_id','id');
    }

    public function agency(){
        return $this->hasOneThrough(Agency::class, Patient::class, 'id', 'id', 'patient_id', 'agency_id');
    }
}
