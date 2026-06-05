<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class HHAMedical extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'hha_medical';
	protected $fillable = ['id','agency_id','office_id',"caregiver_id",'medical_id', 'del_flag', 'created_date','created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by', 'medical_name', 'status','result','due_date','date_performed','notes','caregiver_code','caregiver_first_name','caregiver_last_name','language','caregiver_dob','gender','mobile','phone'];

	
}
