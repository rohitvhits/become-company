<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class HHAVisit extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'hha_visit';
	protected $fillable = ['id','visit_id','patient_id','visit_date','first_name','middle_name','last_name','admission_id','caregiver_id', 'caregiver_code', 'schedule_start_time', 'schedule_end_time', 'del_flag', 'created_date','demographic_update_flag'];

	public function patientDetails(){
		return $this->hasOne(HHAPatient::class,'patient_id','patient_id');
	}
}
