<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TaskTimer extends Model
{
    protected $table = "task_timer";
    protected $guarded = ["id"];

    public static function getTaskTimerlistByTaskId($taskId){
        return TaskTimer::where('task_id',$taskId)->whereNotNull('start_date_time')->orderBy('created_at','asc')->paginate(10);
    }
}
