<?php

namespace App\Services;

use App\Model\HubTaskStatusLog as TaskStatusLog;

class HubTaskStatusLogService
{

	public static function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new TaskStatusLog($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
	public static function getDetails($id)
	{
		$query = TaskStatusLog::select('task_status_log.status', 'task_status_log.notes', 'task_status_log.created_date', 'users.first_name', 'users.last_name')
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'task_status_log.created_by');
				$join->where('users.delete_flag', 'N');
			})

			->where('task_status_log.task_id', $id)->where('task_status_log.del_flag', 'N')->get();
		return $query;
	}
}
