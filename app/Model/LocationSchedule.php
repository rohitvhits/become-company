<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class LocationSchedule extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'location_schedule';
	protected $fillable = ['id','location_id','day', 'start_time','end_time','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'del_flag','slot'];

	
}
