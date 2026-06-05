<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    public $timestamps = false;
    protected $table = "task_log";
    protected $guarded = ["id"];

    public static function getTaskLoglistByTaskId($taskId){
        return TaskLog::with(['getTask','users'])->where('task_id',$taskId)->where('deleted_flag','N')->orderBy('created_at','desc')->paginate(10);
    }
    public static function getActivityLoglistByTaskId($taskId){
        return TaskLog::with(['getTask','users'])->where('task_id',$taskId)->where('deleted_flag','N')->orderBy('created_at','desc')->get();
    }
    public function getTask(){
        return $this->hasOne(Task::class, 'id','task_id');

    }

    public function users(){
        return $this->hasOne(User::class, 'id','created_by');
    }
}
