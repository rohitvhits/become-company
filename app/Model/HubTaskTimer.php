<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HubTaskTimer extends Model
{
    protected $table = "hub_task_timers";
    protected $guarded = ["id"];

    public static function getTaskTimerlistByTaskId($taskId)
    {
        return HubTaskTimer::where('hub_task_id', $taskId)->whereNotNull('start_date_time')->orderBy('created_at', 'asc')->paginate(10);
    }
}
