<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
class PatientServiceRequest extends Model
{
    protected $table = "patient_service_requests";
    protected $guarded = ["id"];

    public function patient(){
        return $this->belongsTo(Patient::class,'patient_id','id');
    }

    public function patientServiceRequestRelationShip(){
        return $this->hasMany(PatientWiseServiceRequest::class,'patient_service_request_id','id')->select('id','service_id','patient_service_request_id')->where('service_id','!=','')->where('del_flag','N');
    }

    public function userDetails(){
        return $this->hasOne(User::class,"id","created_by");
    }

    public function appointmentDetails(){
        return $this->hasOne(Appointment::class,"patient_service_request_id","id");
    }

    public function completedUserDetails(){
        return $this->hasOne(User::class,"id","completed_by");
    }

    public function statusUserDetails(){
        return $this->hasOne(User::class,"id","last_status_update_by");
    }

    public function userDetailsWithTrashed(){
        return $this->hasOne(User::class,"id","created_by")->withTrashed();
    }
}
