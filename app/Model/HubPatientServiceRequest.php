<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;

class HubPatientServiceRequest extends Model
{
    protected $table = "hub_patient_service_requests";
    protected $guarded = ["id"];

    public function patient()
    {
        return $this->belongsTo(HubPatient::class, 'patient_id', 'id');
    }

    public function patientServiceRequestRelationShip()
    {
        return $this->hasMany(HubPatientWiseServiceRequest::class, 'patient_service_request_id', 'id')->select('id', 'service_id', 'patient_service_request_id')->where('service_id', '!=', '');
    }

    public function userDetails()
    {
        return $this->hasOne(User::class, "id", "created_by");
    }

    public function appointmentDetails()
    {
        return $this->hasOne(Appointment::class, "patient_service_request_id", "id");
    }

    public function completedUserDetails()
    {
        return $this->hasOne(User::class, "id", "completed_by");
    }

    public function statusUserDetails()
    {
        return $this->hasOne(User::class, "id", "last_status_update_by");
    }

    public function userDetailsWithTrashed()
    {
        return $this->hasOne(User::class, "id", "created_by")->withTrashed();
    }
}
