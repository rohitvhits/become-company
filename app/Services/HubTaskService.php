<?php

namespace App\Services;

use App\Model\HubTask as Task;
use App\Model\HubTaskTimer as TaskTimer;

class HubTaskService
{

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $insert = new Task($data);
        $insert->save();
        $insertId = $insert->id;
        return $insertId;
    }
    public static  function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        $update = Task::where($where)->update($data);
        return $update;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = Task::where($where)->update($data);
        return $update;
    }

    public static function getList($task_name, $user_id, $created_user_id, $created_task_date, $task_due_date, $priority, $status, $pendingTask = "", $paginate = true)
    {
        $auth = auth()->user();
        $query = Task::select('hub_task_master.id', 'hub_task_master.record_id', 'assign_id', 'hub_task_master.created_by', 'task_name', 'hub_task_master.task_status', 'hub_task_master.due_date', 'hub_task_master.created_date', 'hub_task_master.priority', 'hub_task_master.start_date', 'hub_task_master.flag', 'user_id', 'users.first_name as createdFname', 'users.last_name as createdLname')->where('del_flag', 'N')
            ->leftjoin('patient_master', function ($join) {
                $join->on('patient_master.id', '=', 'hub_task_master.record_id');
                $join->where('patient_master.deleted_flag', 'N');
            })
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'hub_task_master.user_id');
            });
        if ($task_name != '') {
            $query->where('hub_task_master.task_name', 'LIKE', '%' . $task_name . '%');
        }
        if ($user_id != 'all') {
            $query->where('hub_task_master.assign_id', 'LIKE', '%' . $user_id . '%');
        }

        if ($auth->id != 482) {

            $query->where(function ($query) use ($auth) {
                $query->where('hub_task_master.assign_id', $auth->id)->orWhere('hub_task_master.created_by', $auth->id);
            });
        }

        //add by rohit
        if ($status != "") {
            if ($status != "all") {
                $query->where('hub_task_master.task_status', $status);
            }
        }
        if ($pendingTask != '') {
            $query->where('hub_task_master.task_status', 'Pending');
            $query->where('hub_task_master.due_date', '<=', date('Y-m-d H:i:s'));
        }

        //aditional search
        if ($created_user_id != "") {
            $query->where('hub_task_master.created_by', $created_user_id);
        }
        if ($created_task_date != "") {
            $explode = explode('-', $created_task_date);
            $query->whereDate('hub_task_master.created_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('hub_task_master.created_date', '<=', date('Y-m-d', strtotime($explode[1])));
        }
        if ($task_due_date != "") {
            $explode = explode('-', $task_due_date);
            $query->whereDate('hub_task_master.due_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('hub_task_master.due_date', '<=', date('Y-m-d', strtotime($explode[1])));
        }
        if ($priority != "") {
            $query->where('hub_task_master.priority', $priority);
        }
        if ($paginate == true) {
            $mysql = $query->orderBy('hub_task_master.id', 'desc')->paginate(50);
        } else {
            $mysql = $query->orderBy('hub_task_master.id', 'desc')->get();
        }

        return $mysql;
    }
    public static function getListExport($task_name, $user_id, $status, $pendingTask = "")
    {
        $auth = auth()->user();
        $query = Task::with('assignUser')->where('user_id', $auth->id)->where('del_flag', 'N');

        if ($task_name != '') {
            $query->where('task_name', 'LIKE', '%' . $task_name . '%');
        }
        if ($user_id != "all") {
            if ($user_id != '') {
                $query->where('assign_id', $user_id);
            } else {
                $query->where('assign_id', $auth->id);
            }
        }
        if (($status != '' && $status != "all") || $pendingTask != '') {
            $pendingTasks = $pendingTask != '' ? 'Pending' : $status;
            $query->where('task_status', $pendingTasks);
        }
        if ($pendingTask != '') {
            $currentDate = date('Y-m-d', strtotime(now()));
            $query->whereDate('hub_task_master.due_date', '<=', date('Y-m-d 00:00:00', strtotime($currentDate)));
        }
        $mysql = $query->orderBy('id', 'asc')->get();
        return $mysql;
    }

    public static function getDetailsById($id)
    {
        $query = Task::where('id', $id)->where('del_flag', 'N')->first();
        return $query;
    }
    public static function getDetailsByIdNew($id)
    {
        $query = Task::select('hub_task_master.id', 'hub_task_master.assign_id', 'hub_task_master.due_date', 'hub_task_master.task_name', 'hub_task_master.task_description', 'hub_task_master.task_status', 'hub_task_master.created_date', 'hub_task_master.start_date', 'hub_task_master.created_by', 'hub_task_master.clock_in', 'hub_task_master.clock_out', 'hub_task_master.task_hour', 'hub_task_master.notes', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae', 'hub_task_master.priority', 'hub_task_master.record_id', 'hub_task_master.flag', 'hub_task_master.reason', 'hub_task_master.end_date', 'hub_task_master.record_id')
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'hub_task_master.user_id');
                $join->where('users.delete_flag', 'N');
            })
            ->leftjoin('users as us', function ($join) {
                $join->on('us.id', '=', 'hub_task_master.assign_id');
                $join->where('us.delete_flag', 'N');
            })
            ->leftjoin('patient_master', function ($join) {
                $join->on('patient_master.id', '=', 'hub_task_master.record_id');
                $join->where('patient_master.deleted_flag', 'N');
            })
            ->where('hub_task_master.id', $id)->where('hub_task_master.del_flag', 'N')->first();
        if ($query && $query->due_date) {
            $query->due_date = date('m/d/Y h:i A', strtotime($query->due_date));
        }
        if ($query && $query->created_date) {
            $query->created_date = date('m/d/Y h:i A', strtotime($query->created_date));
        }

        return $query;
    }
    public static function getTaskByRecordId($record_id)
    {
        $query = Task::select('hub_task_master.id', 'hub_task_master.assign_id', 'hub_task_master.task_name', 'hub_task_master.task_description', 'hub_task_master.task_status', 'hub_task_master.due_date', 'hub_task_master.created_date', 'hub_task_master.start_date', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae', 'hub_task_master.priority', 'hub_task_master.flag')
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'hub_task_master.user_id');
                $join->where('users.delete_flag', 'N');
            })
            ->leftjoin('users as us', function ($join) {
                $join->on('us.id', '=', 'hub_task_master.assign_id');
                $join->where('us.delete_flag', 'N');
            })
            ->where('hub_task_master.del_flag', 'N')
            ->where('hub_task_master.record_id', $record_id);
        if (auth()->user()->id != 482) {
            $query->whereRaw('(hub_task_master.assign_id="' . auth()->user()->id . '" OR hub_task_master.created_by="' . auth()->user()->id . '")');
        }
        $query = $query->orderBy('hub_task_master.id', 'desc')->paginate(50);
        return $query;
    }
    public static function getAssignTaskList($id, $task_name, $status, $created_date)
    {
        $query = Task::select('hub_task_master.task_name', 'hub_task_master.task_description', 'hub_task_master.task_status', 'hub_task_master.created_date', 'users.first_name', 'users.last_name')
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'hub_task_master.created_by');
                $join->where('users.delete_flag', 'N');
            })
            ->where('hub_task_master.del_flag', 'N')
            ->where('hub_task_master.assign_id', $id);
        if ($task_name != '') {
            $query->where('hub_task_master.task_name', 'LIKE', '%' . $task_name . '%');
        }
        if ($status != '') {
            $query->where('hub_task_master.task_status', $status);
        }
        if ($created_date != '') {
            $explode = explode('-', $created_date);
            $query->whereDate('hub_task_master.created_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('hub_task_master.created_date', '<=', date('Y-m-d', strtotime($explode[1])));
        }
        $query = $query->orderBy('hub_task_master.id', 'desc')->paginate(50);
        return $query;
    }

    public static function getAssignTaskListExport($id, $task_name, $status, $created_date)
    {
        $query = Task::select('hub_task_master.task_name', 'hub_task_master.task_description', 'hub_task_master.task_status', 'hub_task_master.created_date', 'hub_task_master.created_by')

            ->where('hub_task_master.del_flag', 'N')
            ->where('hub_task_master.assign_id', $id);
        if ($task_name != '') {
            $query->where('hub_task_master.task_name', 'LIKE', '%' . $task_name . '%');
        }
        if ($status != '') {
            $query->where('hub_task_master.task_status', $status);
        }
        if ($created_date != '') {
            $explode = explode('-', $created_date);
            $query->whereDate('hub_task_master.created_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('hub_task_master.created_date', '<=', date('Y-m-d', strtotime($explode[1])));
        }
        $query = $query->orderBy('hub_task_master.id', 'desc')->get();
        return $query;
    }

    public static function getTaskCalendar($fdate)
    {
        $query = Task::where('del_flag', 'N')->whereNotNull('due_date')->whereDate('due_date', $fdate)->get();
        return $query;
    }

    public function getOutstandingTaskList()
    {
        return Task::where('del_flag', 'N')->whereNotIn('task_status', ['Completed', 'Rejected'])->whereNotNull('record_id')->whereNotNull('due_date')->get();
    }

    public static function timeStore($data)
    {
        $insert = new TaskTimer($data);
        $insert->save();
        return $insert;
    }

    public static function updateTime($data, $where)
    {
        return TaskTimer::where($where)->update($data);
    }

    public function getMyDueTask()
    {
        return Task::where('assign_id', auth()->user()->id)->where('del_flag', 'N')->whereIn('task_status', ['Pending', 'In Progress'])->where('due_date', '<=', date('Y-m-d') . ' 23:59:59')->get();
    }

    public static function getTaskList($status)
    {
        $auth = auth()->user();
        $query = Task::with('assignUser:id,first_name,last_name')->where('del_flag', 'N');
        if ($auth->id != 482) {
            $query->whereRaw('(assign_id ="' . $auth->id . '" OR created_by="' . $auth->id . '")');
        }
        if ($status != "") {
            if ($status != "all") {
                $query->where('task_status', $status);
            }
        }
        return $query->select('id', 'user_id', 'assign_id', 'task_name', 'task_description', 'task_status')->orderBy('id', 'desc');
    }

    public static function getTaskListByUserId($status, $user_id)
    {
        $query = Task::with('assignUser:id,first_name,last_name')->where('del_flag', 'N');
        if (!empty($user_id)) {
            $query->whereRaw('(assign_id ="' . $user_id . '" OR created_by="' . $user_id . '")');
        }
        if ($status != "") {
            if ($status != "all") {
                $query->where('task_status', $status);
            }
        }
        return $query->select('id', 'user_id', 'assign_id', 'task_name', 'task_description', 'task_status')->orderBy('id', 'desc');
    }

    public function getFlagTaskData()
    {
        $query = Task::select('hub_task_master.id', 'hub_task_master.assign_id', 'hub_task_master.task_name', 'hub_task_master.task_description', 'hub_task_master.task_status', 'hub_task_master.due_date', 'hub_task_master.created_date', 'hub_task_master.start_date', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae', 'hub_task_master.priority', 'hub_task_master.flag')
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'hub_task_master.user_id');
                $join->where('users.delete_flag', 'N');
            })
            ->leftjoin('users as us', function ($join) {
                $join->on('us.id', '=', 'hub_task_master.assign_id');
                $join->where('us.delete_flag', 'N');
            })
            ->where('hub_task_master.del_flag', 'N')
            ->where('hub_task_master.flag', 1);
        if (auth()->user()->id != 482) {
            $query->whereRaw('(hub_task_master.assign_id="' . auth()->user()->id . '" OR hub_task_master.created_by="' . auth()->user()->id . '")');
        }
        $query = $query->orderBy('hub_task_master.id', 'desc')->paginate(50);
        return $query;
    }

    public function getTaskCountData($from_date, $to_date)
    {
        $auth = auth()->user();
        $query = Task::select('hub_task_master.id', 'task_status', 'priority')
            ->where('hub_task_master.del_flag', 'N');
        if ($auth->id != 482) {
            $query->whereRaw('(assign_id ="' . $auth->id . '" OR created_by="' . $auth->id . '")');
        }
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween('hub_task_master.created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        }
        $query = $query->get();
        return $query;
    }

    public function getTaskData($from_date, $to_date)
    {
        $auth = auth()->user();
        $query = Task::select('hub_task_master.id', 'task_name', 'hub_task_master.created_date', 'due_date', 'hub_task_master.created_by', 'task_status', 'priority', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae')->leftjoin('users', function ($join) {
            $join->on('users.id', '=', 'hub_task_master.user_id');
            $join->where('users.delete_flag', 'N');
        })
            ->leftjoin('users as us', function ($join) {
                $join->on('us.id', '=', 'hub_task_master.assign_id');
                $join->where('us.delete_flag', 'N');
            })->where('hub_task_master.del_flag', 'N');
        if ($auth->id != 482) {
            $query->whereRaw('(hub_task_master.assign_id ="' . $auth->id . '" OR hub_task_master.created_by="' . $auth->id . '")');
        }
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween('hub_task_master.created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        }
        $query = $query->orderBy('hub_task_master.id', 'desc')->simplepaginate(10);
        return $query;
    }

    public function getTaskPatientWiseData($from_date, $to_date)
    {
        $auth = auth()->user();
        $query = Task::select('hub_task_master.id', 'task_name', 'record_id')->whereNotNull('record_id');
        if ($auth->id != 482) {
            $query->whereRaw('(assign_id ="' . $auth->id . '" OR created_by="' . $auth->id . '")');
        }
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween('hub_task_master.created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        }
        $query = $query->get();
        return $query;
    }

    public function getTaskAssigneeWiseData($from_date, $to_date)
    {
        $auth = auth()->user();
        $query = Task::select('hub_task_master.id', 'task_name', 'assign_id')->whereNotNull('assign_id');
        if ($auth->id != 482) {
            $query->whereRaw('(assign_id ="' . $auth->id . '" OR created_by="' . $auth->id . '")');
        }
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        }
        $query = $query->get();
        return $query;
    }

    public function getExistingPatientTask($recordId)
    {
        return Task::select('id', 'record_id')->where('del_flag', 'N')->where('record_id', $recordId)->get();
    }

    public static function getTaskByRecordIdArray($record_id)
    {
        $query = Task::select('hub_task_master.id', 'hub_task_master.assign_id', 'hub_task_master.task_name', 'hub_task_master.task_description', 'hub_task_master.task_status', 'hub_task_master.due_date', 'hub_task_master.created_date', 'hub_task_master.start_date', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae', 'hub_task_master.priority', 'hub_task_master.flag')
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'hub_task_master.user_id');
                $join->where('users.delete_flag', 'N');
            })
            ->leftjoin('users as us', function ($join) {
                $join->on('us.id', '=', 'hub_task_master.assign_id');
                $join->where('us.delete_flag', 'N');
            })
            ->where('hub_task_master.del_flag', 'N')
            ->where('hub_task_master.record_id', $record_id);
        if (auth()->user()->id != 482) {
            $query->whereRaw('(hub_task_master.assign_id="' . auth()->user()->id . '" OR hub_task_master.created_by="' . auth()->user()->id . '")');
        }
        $query = $query->orderBy('hub_task_master.id', 'desc')->paginate(50);
        return $query;
    }
}
