<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Services\UserWiseAgencyService;
use App\Services\AppointmentService;
use App\Services\TaskService;
use App\Services\UserService;
use App\Services\PatientNotesService;
use App\Services\DocumentSendService;
use App\Helpers\Utility;
use App\Services\AnnouncementUserService;
class EmployeeDashboardController extends BaseController{
    protected $patientService,$userWiseAgencyService,$appointmentService,$taskService,$userService,$patientNotesService, $documentSentReport,$announcementUserService ="";
    public function __construct(PatientService $patientService, UserWiseAgencyService $userWiseAgencyService, AppointmentService $appointmentService,TaskService $taskService, UserService $userService, PatientNotesService $patientNotesService,DocumentSendService $documentSentReport,AnnouncementUserService $announcementUserService)
    {
        $this->middleware('permission:employee-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->patientService = $patientService;
        $this->userWiseAgencyService = $userWiseAgencyService;
        $this->appointmentService = $appointmentService;
        $this->taskService = $taskService;
        $this->userService = $userService;
        $this->patientNotesService = $patientNotesService;
        $this->documentSentReport = $documentSentReport;
        $this->announcementUserService = $announcementUserService;
        $this->userService = $userService;
    }

    public function index(){
        $auth = auth()->user();
      
        $data['userList'] = $this->userService->getAllUserList();
        return view('employeeDashboard.index',$data);
    }

    public function totalCountForAgency(Request $request){

        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $date = explode('-',$request->range_date);
            if(count($date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($date[1])))??'';
            }
        }
        $user_id = $request->user_id;
        $agency = Utility::getUserIdWiseAgency($user_id);
        $agency = $agency;
        
        if($request->agency_id !=""){
            $agency = $request->agency_id;
        }else{
            if(empty($agency[0])){
                $agency = [0];
            }
        }
        

        $totalPending = $this->patientService->totalCountPatientStatusWise('Pending',$from_date,$to_date,$agency);
        
        $totalCompleted = $this->patientService->totalCountPatientStatusWise('completed',$from_date,$to_date,$agency);
        $totalBooked = $this->patientService->totalCountPatientStatusWise('booked',$from_date,$to_date,$agency);
        $totalInprogress = $this->patientService->totalCountPatientStatusWise('processing',$from_date,$to_date,$agency);
        
        $data = ['totalBooked'=>$totalBooked,'totalInprogress'=>$totalInprogress,'totalPending'=>$totalPending,'totalCompleted'=>$totalCompleted];
      
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function getTodayAppointmentData(Request $request){
        $data['todayAppoinmentData'] = array();
        $user_id = $request->user_id;
        $agency = Utility::getUserIdWiseAgency($user_id);
        if($request->agency_id !=""){
            $agency = $request->agency_id;
        }
      
        $data['todayAppoinmentData'] = $this->appointmentService->getTodayAppointment($agency);
        
        return view('employeeDashboard.today_appointment',$data);
    }

    public function getUpcommingAppointmentData(Request $request){
        $data['upcommingAppoinmentData'] = array();
      
        $user_id = $request->user_id;
        $agency = Utility::getUserIdWiseAgency($user_id);
        if($request->agency_id !=""){
            $agency = $request->agency_id;
        }
      
        $data['upcommingAppoinmentData'] = $this->appointmentService->getUpcommingAppointment($agency); 
             
        return view('employeeDashboard.upcomming_appointment',$data);
    }

    public function getStatisticData(Request $request){
        $data['statisticData'] = array();
        // $agencyids = $this->userWiseAgencyService->getUserWiseAgencyData($request->agency_id);        
        // if($request->agency_id !=""){
        //     $agencyids[] = $request->agency_id;
        // }
        $user_id = $request->user_id;
        
        $agency = Utility::getUserIdWiseAgency($user_id);
        if($request->agency_id !=""){
            $agency = $request->agency_id;
        }
   
      
        $data['statisticData'] = $this->patientService->getStatisticAgencyListData($request->type, $agency);
      
        return view('employeeDashboard.statistic_data',$data);
    }

    public function getTaskData(Request $request){

        $user_id = $request->user_id;
        $data['taskData'] = $this->taskService->getTaskListByUserId($request->status_type,$user_id)->paginate(50);
        return view('employeeDashboard.task_list',$data);
    }

    public function getNotesData(Request $request){     
        $perPage = 10; // Number of items per page
        $page = $request->get('page', 1);     
       
        $user_id = $request->user_id;
        $agencyids = $this->userWiseAgencyService->getUserWiseAgencyDataUserId($request->notes_agency_id,$user_id); 
        if($request->agency_id !=""){
            $agencyids = $request->agency_id;
        }
        $data = $this->patientNotesService->getNotesOfAgencyIdUser($agencyids,$perPage,$page,$user_id);
        return response()->json(['success'=>true,'data'=>$data],200);
    }


    public function getEsignData(Request $request){        
        $data['esignData'] = array();
        $user_id = $request->user_id;
        $agencyids = Utility::getUserIdWiseAgency($user_id);
        if($request->agency_id !=""){
            $agencyids = $request->agency_id;
        }
        $patientsIds = $this->patientService->getpatientIds($agencyids);
        $data['esignData'] = $this->documentSentReport->EsignTemplateList($patientsIds)->simplepaginate(10);
        return view('employeeDashboard.esign',$data);
    }

    public function getAnnouncementData(Request $request){        
        $data = array();
        $from_date = $to_date = '';       
        $perPage = 10; // Number of items per page
        $page = $request->get('page', 1);  
        if($request->announcement_range_date != ''){
            $date = explode('-',$request->announcement_range_date);
            if(count($date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($date[1])))??'';
            }
        }
        $user_id = $request->user_id;
        $data = $this->announcementUserService->getAnnouncementOfUserId($from_date,$to_date,$perPage,$page,$user_id);                
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function userWiseAgencyShow(Request $request){
        $agency = Utility::getUserIdWiseAgency($request->user_id);
        $agencyList = Agency::getAgencyListWithIds($agency); 
        return response()->json(['success'=>true,'data'=>$agencyList],200);
    }
}