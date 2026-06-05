<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PatientCronLog extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'patient_cron_log';
	protected $fillable = ['id','patient_id','type', 'old_response', 'appointment_date', 'created_date','updated_at'];
}