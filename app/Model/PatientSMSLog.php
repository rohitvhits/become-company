<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PatientSMSLog extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'patient_sms_log';
	protected $fillable = ['id','patient_id','mobile_no', 'message', 'key','del_flag','created_date'];

	
}
