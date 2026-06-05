<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class TaskAssignLog extends Model
{
	protected $table = 'task_assign_log';
	public $timestamps = false;
	protected $fillable = ['id','user_id','task_id','status','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by'];
	
}

?>