<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class NyBestReminderNotification extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'nybest_reminder_notification';
	protected $fillable = ['id','patient_id','email', 'notes', 'type', 'date','created_date','del_flag', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by','every_month','mobile'];

	public static function getList(){
		
		$query  = NyBestReminderNotification::select('id','patient_id','email','notes','date')->where('del_flag','N')->where('status',0)->where('date',date('Y-m-d'))->get();
		return $query;
	}
}
