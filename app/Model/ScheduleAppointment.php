<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ScheduleAppointment extends Model
{
    use Notifiable; 
	public $timestamps =false;
	protected $table = 'schedule_appointment';
	protected $fillable = ['id','agency_id','patient_id', 'location_id','appointment_date','schedule_id','appointment_time','status', 'created_date', 'created_by','location_time_id_slot'];	

	public function users()
	{
		return $this->belongsTo(User::class,'created_by','id');
	}
}
