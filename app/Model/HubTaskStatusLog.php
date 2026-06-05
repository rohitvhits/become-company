<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class HubTaskStatusLog extends Model
{
	protected $table = 'hub_task_status_log';
	public $timestamps = false;
	protected $fillable = ['id','task_id','status','notes','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by'];
	
}

?>