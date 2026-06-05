<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class HubTaskLog extends Model
{
    public $timestamps = false;
    protected $table = "hub_task_logs";
    protected $guarded = ["id"];

    public static function getTaskLoglistByTaskId($taskId)
    {
        return HubTaskLog::with(['getTask', 'users'])->where('hub_task_id', $taskId)->where('deleted_flag', 'N')->orderBy('created_at', 'desc')->paginate(10);
    }
    public static function getActivityLoglistByTaskId($taskId)
    {
        return HubTaskLog::with(['getTask', 'users'])->where('hub_task_id', $taskId)->where('deleted_flag', 'N')->orderBy('created_at', 'desc')->get();
    }
    public function getTask()
    {
        return $this->hasOne(HubTask::class, 'id', 'hub_task_id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
