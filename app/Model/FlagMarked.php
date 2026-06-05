<?php

namespace App\Model;

use App\Agency;
use App\Template;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlagMarked extends Model
{
    use SoftDeletes;
    protected $table = 'flag_marked';
    protected $guarded = ['id'];

    public function agencies()
    {
        return $this->hasOne(Agency::class, 'id', 'agency_id');
    }
    
    public function doctors()
    {
        return $this->hasOne(Doctor::class, 'id', 'doctor_id');
    }

    public function forms()
    {
        return $this->hasOne(FormSetup::class, 'id', 'form_id');
    }

    public function agencyMaster()
    {
        return $this->hasMany(AgencyMaster::class, 'form_id', 'form_id');
    }

    public function templateById()
    {
        return $this->hasOne(Template::class, 'custom_form_id', 'form_id');
    }

    public function getPatientData()
    {
        return $this->hasMany(PatientCustomData::class, 'agency_form_id', 'id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'id', 'patient_id');
    }

    public function userMarkAsComplatedDetails()
    {
        return $this->hasOne(User::class, 'id', 'mark_as_completed_by');
    }
}
