<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HHAPatientVisit extends Model
{

    public $timestamps = false;
    protected $table = 'hha_patient_visit';
    protected $fillable = ['id','visit_id','patient_id','visit_date','first_name','middle_name','last_name','admission_id','caregiver_id','caregiver_code','schedule_start_time','schedule_end_time','del_flag','created_date','demographic_update_flag','updated_at'];
	
    public function caregiverDetails()
    {
        return $this->hasOne(HHACaregivers::class, 'caregiver_id', 'caregiver_id');
    }
}
