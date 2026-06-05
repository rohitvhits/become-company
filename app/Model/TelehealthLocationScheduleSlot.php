<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class TelehealthLocationScheduleSlot extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'telehealth_location_schedule_slot';
	protected $fillable = ['id','location_id','loc_schedule_id', 'slot_start_time','slot_end_time','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'del_flag'];
}
