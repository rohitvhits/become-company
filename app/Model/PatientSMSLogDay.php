<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PatientSMSLogDay extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'patient_sms_log_day';
	protected $fillable = ['id','patient_id','del_flag','created_date','sms','mobile_no'];

	
}
