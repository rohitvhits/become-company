<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PatientMobileVerificationLogs extends Model
{
	
	public $timestamps =false;
	protected $table = 'patient_mobile_verification_logs';
	protected $fillable = ['id','number','type','del_flag', 'created_at','created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];
}
