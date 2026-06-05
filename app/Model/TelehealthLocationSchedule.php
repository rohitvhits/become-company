<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class TelehealthLocationSchedule extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'telehealth_location_schedule';
	protected $fillable = ['id','location_id','title','days', 'start_time','end_time','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'del_flag','slot','tele_config_type'];

	public function slots()
	{
		return $this->hasMany(TelehealthLocationScheduleSlot::class, 'loc_schedule_id','id')
			->where('del_flag', 'N');
	}

	public function days()
	{
		return $this->hasMany(TelehealthLocationScheduleDay::class, 'schedule_id','id')
			->where('del_flag', 'N');
	}
}
