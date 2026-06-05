<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PatientTelehealthSchedule extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'patient_telehealth_schedule';
	protected $fillable = ['id','day','start_time','end_time', 'del_flag','slot','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'del_flag'];
}
