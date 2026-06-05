<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Services\LocationMasterService;
use App\Services\TaskService;
use App\Services\UserService;


class TaskDashboardController extends BaseController{
    protected $patientService,$locationMasterService,$taskService,$userService="";
    public function __construct(PatientService $patientService, LocationMasterService $locationMasterService, TaskService $taskService, UserService $userService)
    {
        $this->middleware('permission:task-dashboard', ['only' => ['index','getTotalCountData','getPriorityTaskData','getTaskData','getPatientWiseTaskCount','getAssigneeWiseTaskCount']]);
        $this->middleware('auth');
        $this->patientService = $patientService;
        $this->locationMasterService = $locationMasterService;
        $this->taskService = $taskService;
        $this->userService = $userService;
    }

    public function index(){
        return view('taskDashboard.index');
    }

    public function getTotalCountData(Request $request){

        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $taskData = $this->taskService->getTaskCountData($from_date,$to_date);
        $totalPending = $totalUrgent = $totalCompleted = $totalOutstanding = 0;         
        foreach($taskData as $task){
            if(strtolower($task->task_status) == 'pending'){
                $totalPending++;
            }elseif(strtolower($task->task_status) == 'urgent'){
                $totalUrgent++;
            }elseif(strtolower($task->task_status) == 'completed'){
                $totalCompleted++;
            }elseif(strtolower($task->task_status) == 'outstanding'){
                $totalOutstanding++;
            }
        }
        $data = ['totalTask'=>count($taskData),'totalPending'=>$totalPending,'totalUrgent'=>$totalUrgent,'totalCompleted'=>$totalCompleted,'totalOutstanding'=>$totalOutstanding];
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function getPriorityTaskData(Request $request){
        $data = array();
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $taskData = $this->taskService->getTaskCountData($from_date,$to_date);
        $totalHigh = $totalLow = $totalMedium = 0;         
        foreach($taskData as $task){
            if(strtolower($task->priority) == 'high'){
                $totalHigh++;
            }elseif(strtolower($task->priority) == 'low'){
                $totalLow++;
            }elseif(strtolower($task->priority) == 'medium'){
                $totalMedium++;
            }
        }
        if($totalHigh > 0 || $totalLow > 0 || $totalMedium > 0){
            $data = array(
                array(
                    'name' => 'High',
                    'total' => $totalHigh,
                ),array(
                    'name' => 'Low',
                    'total' => $totalLow,
                ),array(
                    'name' => 'Medium',
                    'total' => $totalMedium,
                ),
            );
        }
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function getTaskData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data['taskData'] = $this->taskService->getTaskData($from_date, $to_date);
        return view('taskDashboard.task_list',$data);
    }

    public function getPatientWiseTaskCount(Request $request){
        $data = $patientDeatilsCount = array();
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $taskData = $this->taskService->getTaskPatientWiseData($from_date,$to_date);
        $patientIds = [];
        foreach($taskData as $tData){
            $patientIds[] = $tData->record_id;
        }
        // Get all patients data
        $patientData = $this->patientService->getPatientDetails($patientIds);
        foreach($patientData as $pData){
            $patientDeatils[$pData->id] = $pData['first_name'].' '.$pData['last_name'];
        }
        foreach($taskData as $tData){
            if(isset($patientDeatilsCount[$tData->record_id])){
                $patientDeatilsCount[$tData->record_id] = $patientDeatilsCount[$tData->record_id] + 1;
            }else{
                $patientDeatilsCount[$tData->record_id] = 1;
            }
        }
        foreach($patientDeatilsCount as $key => $tot){
            if(array_key_exists($key,$patientDeatils)){
                $data[] = array(
                    'name' => $patientDeatils[$key],
                    'total' => $tot
                );
            }
        }
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function getAssigneeWiseTaskCount(Request $request){
        $data = $patientDeatilsCount = array();
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $taskData = $this->taskService->getTaskAssigneeWiseData($from_date,$to_date);
        $patientIds = [];
        foreach($taskData as $tData){
            $patientIds[] = $tData->assign_id;
        }
        // Get all patients data
        $patientData = $this->userService->getUsersByIds($patientIds);
        foreach($patientData as $pData){
            $patientDeatils[$pData->id] = $pData['first_name'].' '.$pData['last_name'];
        }
        foreach($taskData as $tData){
            if(isset($patientDeatilsCount[$tData->assign_id])){
                $patientDeatilsCount[$tData->assign_id] = $patientDeatilsCount[$tData->assign_id] + 1;
            }else{
                $patientDeatilsCount[$tData->assign_id] = 1;
            }
        }
        foreach($patientDeatilsCount as $key => $tot){
            if(array_key_exists($key,$patientDeatils)){
                $data[] = array(
                    'name' => $patientDeatils[$key],
                    'id' => $key,
                    'total' => $tot
                );
            }
        }
        return response()->json(['success'=>true,'data'=>$data],200);
    }
}