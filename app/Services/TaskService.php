<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\Task;
use App\Model\TaskTimer;
use App\Helpers\Utility;

class TaskService
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

	public static function getList($task_name,$user_id,$created_user_id,$created_task_date,$task_due_date,$priority,$status, $pendingTask = "",$paginate = true,$department="",$visiblity_assign="")
	{
		$auth = auth()->user();
		$query = Task::select('task_master.id','task_master.record_id','assign_id','task_master.created_by','task_name','task_master.task_status','task_master.due_date','task_master.created_date','task_master.priority','task_master.start_date','task_master.flag','user_id','users.first_name as createdFname','users.last_name as createdLname','departments.name as dep_name')->where('task_master.del_flag','N')
		->leftjoin('patient_master', function ($join) {
			$join->on('patient_master.id', '=', 'task_master.record_id');
			$join->where('patient_master.deleted_flag', 'N');
		})
		->leftjoin('users', function ($join){
			$join->on('users.id', '=', 'task_master.user_id');
		})
		->leftjoin('departments', function ($join){
			$join->on('departments.id', '=', 'task_master.department_id');
		});
		if ($task_name != '') {
			$query->where('task_master.task_name', 'LIKE', '%' . $task_name . '%');
		}
		if ($user_id != 'all') {
			$query->where('task_master.assign_id', 'LIKE', '%' . $user_id . '%');
		}
		
		if($auth->view_all_task_access !=1){
			if(isset($visiblity_assign)){
				if($visiblity_assign == 'dept'){
					$userDepartments = DB::table('department_user')
						->where('user_id', $auth->id)
						->where('del_flag','N')
						->pluck('department_id');
					$query->where('departments.del_flag', 'N')->whereIn('task_master.department_id', $userDepartments);
				}else if($visiblity_assign == 'me'){
					$query->where(function($query) use ($auth){
						$query->where('task_master.assign_id', $auth->id)->orWhere('task_master.created_by', $auth->id);
					});
				}
			}else{
				$query->where(function($query) use ($auth){
					$query->where('task_master.assign_id', $auth->id)->orWhere('task_master.created_by', $auth->id);
				});
			}
		}

		if($status !=""){
			if($status !="all"){
				$query->where('task_master.task_status', $status);
			}
		}
		if ($pendingTask != '') {
			$query->where('task_master.task_status', 'Pending');
			$query->whereDate('task_master.due_date','<',date('Y-m-d'));
		}
		//aditional search
		if($created_user_id !=""){
				$query->where('task_master.created_by',$created_user_id );
	     }
		if ($created_task_date!="") {
			$explode = explode('-',$created_task_date);
			$query->whereDate('task_master.created_date','>=',date('Y-m-d', strtotime($explode[0])))->whereDate('task_master.created_date','<=',date('Y-m-d', strtotime($explode[1])));
		}
		if ($task_due_date!="") {
			$explode = explode('-',$task_due_date);
			$query->whereDate('task_master.due_date','>=',date('Y-m-d', strtotime($explode[0])))->whereDate('task_master.due_date','<=',date('Y-m-d', strtotime($explode[1])));
		}
		if ($priority!="") {
			$query->where('task_master.priority',$priority);
		}
		if ($department!="") {
			$query->where('task_master.department_id',$department);
		}
		// $query->where('departments.del_flag','N');
		if($paginate == true){
			$mysql = $query->orderBy('task_master.id', 'desc')->paginate(50);
		}else{			
			$mysql = $query->orderBy('task_master.id', 'desc')->get();
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
			$query->whereDate('task_master.due_date','<',date('Y-m-d 00:00:00', strtotime($currentDate)));
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
		$query = Task::select('task_master.id','task_master.assign_id','task_master.due_date', 'task_master.task_name', 'task_master.task_description', 'task_master.task_status', 'task_master.created_date','task_master.start_date', 'task_master.created_by','task_master.clock_in','task_master.clock_out','task_master.task_hour','task_master.notes','users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae','task_master.priority','task_master.record_id','task_master.flag','task_master.reason','task_master.end_date','task_master.department_id')
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'task_master.user_id');
				$join->where('users.delete_flag', 'N');
			})
			->leftjoin('users as us', function ($join) {
				$join->on('us.id', '=', 'task_master.assign_id');
				$join->where('us.delete_flag', 'N');
			})
			->leftjoin('patient_master', function ($join) {
				$join->on('patient_master.id', '=', 'task_master.record_id');
				$join->where('patient_master.deleted_flag', 'N');
			})
			->where('task_master.id', $id)->where('task_master.del_flag', 'N')->first();
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
		$query = Task::select('task_master.id','task_master.assign_id', 'task_master.task_name', 'task_master.task_description', 'task_master.task_status', 'task_master.due_date', 'task_master.created_date','task_master.start_date', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae','task_master.priority','task_master.flag')
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'task_master.user_id');
				$join->where('users.delete_flag', 'N');
			})
			->leftjoin('users as us', function ($join) {
				$join->on('us.id', '=', 'task_master.assign_id');
				$join->where('us.delete_flag', 'N');
			})
			->where('task_master.del_flag', 'N')
			->where('task_master.record_id', $record_id);
		if(auth()->user()->id != 482){
			$query->whereRaw('(task_master.assign_id="'.auth()->user()->id.'" OR task_master.created_by="'.auth()->user()->id.'")');
			
		}
		$query = $query->orderBy('task_master.id', 'desc')->paginate(50);
		return $query;
	}
	public static function getAssignTaskList($id, $task_name, $status, $created_date)
	{
		$query = Task::select('task_master.task_name', 'task_master.task_description', 'task_master.task_status', 'task_master.created_date', 'users.first_name', 'users.last_name')
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'task_master.created_by');
				$join->where('users.delete_flag', 'N');
			})
			->where('task_master.del_flag', 'N')
			->where('task_master.assign_id', $id);
		if ($task_name != '') {
			$query->where('task_master.task_name', 'LIKE', '%' . $task_name . '%');
		}
		if ($status != '') {
			$query->where('task_master.task_status', $status);
		}
		if ($created_date != '') {
			$explode = explode('-', $created_date);
			$query->whereDate('task_master.created_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('task_master.created_date', '<=', date('Y-m-d', strtotime($explode[1])));
		}
		$query = $query->orderBy('task_master.id', 'desc')->paginate(50);
		return $query;
	}

	public static function getAssignTaskListExport($id, $task_name, $status, $created_date)
	{
		$query = Task::select('task_master.task_name', 'task_master.task_description', 'task_master.task_status', 'task_master.created_date', 'task_master.created_by')

			->where('task_master.del_flag', 'N')
			->where('task_master.assign_id', $id);
		if ($task_name != '') {
			$query->where('task_master.task_name', 'LIKE', '%' . $task_name . '%');
		}
		if ($status != '') {
			$query->where('task_master.task_status', $status);
		}
		if ($created_date != '') {
			$explode = explode('-', $created_date);
			$query->whereDate('task_master.created_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('task_master.created_date', '<=', date('Y-m-d', strtotime($explode[1])));
		}
		$query = $query->orderBy('task_master.id', 'desc')->get();
		return $query;
	}

	public static function getTaskCalendar($fdate)
	{
		$query = Task::where('del_flag', 'N')->whereNotNull('due_date')->whereDate('due_date',$fdate)->get();
		return $query;
	}

	public function getOutstandingTaskList()
	{
		return Task::where('del_flag','N')->whereNotIn('task_status', ['Completed','Rejected'])->whereNotNull('record_id')->whereNotNull('due_date')->get();
	}

	public static function timeStore($data){
		$insert = new TaskTimer($data);
		$insert->save();
		return $insert;
	}	

	public static function updateTime($data,$where){
		return TaskTimer::where($where)->update($data);
	}	

	public function getMyDueTask(){
		$userDepartments = DB::table('department_user')->where('user_id', auth()->id())->where('del_flag','N')->pluck('department_id');
		return Task::where(function ($q) use ($userDepartments) {
				$q->whereIn('department_id', $userDepartments)
				->orWhere(function ($q2) {
					$q2->where('assign_id', auth()->id())
						->orWhere('created_by', auth()->id());
				});
			})
			->where('del_flag', 'N')
			->whereIn('task_status', ['Pending', 'In Progress'])
			->whereDate('due_date','<',date('Y-m-d'))
			->get();
	}

	public static function getTaskList($status)
	{
		$auth = auth()->user();
		$query = Task::with('assignUser:id,first_name,last_name')->where('del_flag','N');
		if($auth->id != 482){
			$query->whereRaw('(assign_id ="'.$auth->id.'" OR created_by="'.$auth->id.'")');
		}
		if($status !=""){
			if($status !="all"){
				$query->where('task_status', $status);
			}
		}
		return $query->select('id','user_id','assign_id','task_name','task_description','task_status')->orderBy('id', 'desc');
	}

	public static function getTaskListByUserId($status,$user_id)
	{
		$query = Task::with('assignUser:id,first_name,last_name')->where('del_flag','N');
		if(!empty($user_id)){
			$query->whereRaw('(assign_id ="'.$user_id.'" OR created_by="'.$user_id.'")');
		}
		if($status !=""){
			if($status !="all"){
				$query->where('task_status', $status);
			}
		}
		return $query->select('id','user_id','assign_id','task_name','task_description','task_status')->orderBy('id', 'desc');
	}

	public function getFlagTaskData(){
		$query = Task::select('task_master.id','task_master.assign_id', 'task_master.task_name', 'task_master.task_description', 'task_master.task_status', 'task_master.due_date', 'task_master.created_date','task_master.start_date', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae','task_master.priority','task_master.flag')
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'task_master.user_id');
				$join->where('users.delete_flag', 'N');
			})
			->leftjoin('users as us', function ($join) {
				$join->on('us.id', '=', 'task_master.assign_id');
				$join->where('us.delete_flag', 'N');
			})
			->where('task_master.del_flag', 'N')
			->where('task_master.flag', 1);
		if(auth()->user()->id != 482){
			$query->whereRaw('(task_master.assign_id="'.auth()->user()->id.'" OR task_master.created_by="'.auth()->user()->id.'")');
		}
		$query = $query->orderBy('task_master.id', 'desc')->paginate(50);
		return $query;
	}

	public function getTaskCountData($from_date,$to_date){
		$auth = auth()->user();
		$query = Task::select('task_master.id','task_status','priority')
			->where('task_master.del_flag', 'N');
		if($auth->id != 482){
			$query->whereRaw('(assign_id ="'.$auth->id.'" OR created_by="'.$auth->id.'")');
		}	
		if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('task_master.created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		$query = $query->get();
		return $query;
	}

	public function getTaskData($from_date,$to_date){
		$auth = auth()->user();
		$query = Task::select('task_master.id','task_name','task_master.created_date','due_date','task_master.created_by','task_status','priority','users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae')->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'task_master.user_id');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as us', function ($join) {
			$join->on('us.id', '=', 'task_master.assign_id');
			$join->where('us.delete_flag', 'N');
		})->where('task_master.del_flag', 'N');
		if($auth->id != 482){
			$query->whereRaw('(task_master.assign_id ="'.$auth->id.'" OR task_master.created_by="'.$auth->id.'")');
		}	
		if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('task_master.created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		$query = $query->orderBy('task_master.id', 'desc')->simplepaginate(10);
		return $query;
	}

	public function getTaskPatientWiseData($from_date,$to_date){
		$auth = auth()->user();
		$query = Task::select('task_master.id','task_name','record_id')->whereNotNull('record_id');
		if($auth->id != 482){
			$query->whereRaw('(assign_id ="'.$auth->id.'" OR created_by="'.$auth->id.'")');
		}	
		if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('task_master.created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		$query = $query->get();
		return $query;
	}

	public function getTaskAssigneeWiseData($from_date,$to_date){
		$auth = auth()->user();
		$query = Task::select('task_master.id','task_name','assign_id')->whereNotNull('assign_id');
		if($auth->id != 482){
			$query->whereRaw('(assign_id ="'.$auth->id.'" OR created_by="'.$auth->id.'")');
		}	
		if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		$query = $query->get();
		return $query;
	}

	public function getExistingPatientTask($recordId){
		return Task::select('id','record_id')->where('del_flag','N')->where('record_id',$recordId)->get();
	}

	public static function getTaskByRecordIdArray($record_id)
	{
		$auth = auth()->user();
		$query = Task::select('task_master.id','task_master.record_id','task_master.assign_id', 'task_master.task_name', 'task_master.task_description', 'task_master.task_status', 'task_master.due_date', 'task_master.created_date','task_master.start_date', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae','task_master.priority','task_master.flag','departments.name as dep_name'	)
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'task_master.user_id');
				$join->where('users.delete_flag', 'N');
			})
			->leftjoin('users as us', function ($join) {
				$join->on('us.id', '=', 'task_master.assign_id');
				$join->where('us.delete_flag', 'N');
			})
			->leftjoin('departments', function ($join){
				$join->on('departments.id', '=', 'task_master.department_id');
			})
			->where('task_master.del_flag', 'N')
			->whereIn('task_master.record_id', $record_id);
		if($auth->view_all_task_access !=1){
			$userDepartments = DB::table('department_user')->where('user_id', $auth->id)->where('del_flag','N')->pluck('department_id');
			$query->where(function ($q) use ($userDepartments) {
				$q->whereIn('department_id', $userDepartments)
				->orWhere(function ($q2) {
					$q2->where('task_master.assign_id', auth()->id())
						->orWhere('task_master.created_by', auth()->id());
				});
			});
		}
		$query = $query->orderBy('task_master.id', 'desc')->paginate(50);
		return $query;
	}
}