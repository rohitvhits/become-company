<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
	protected $table = 'task_master';
	public $timestamps = false;
	protected $fillable = ['id','user_id','assign_id','task_name','task_description','task_status','due_date','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','record_id','start_date','clock_in','clock_out','task_hour','notes','priority','flag','reason','end_date','department_id'];
	

	public function assignUser()
	{
		return $this->belongsTo(User::class,'assign_id','id');
	}
}

?>