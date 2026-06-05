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
use App\Services\LocationMasterService;
use App\Services\AnnouncementUserService;
use DB;

class AgencyDashboardController extends BaseController{
    protected $patientService,$userWiseAgencyService,$appointmentService,$taskService,$userService,$patientNotesService, $documentSentReport,$locationMasterService,$announcementUserService ="";
    public function __construct(PatientService $patientService, UserWiseAgencyService $userWiseAgencyService, AppointmentService $appointmentService,TaskService $taskService, UserService $userService, PatientNotesService $patientNotesService,DocumentSendService $documentSentReport, LocationMasterService $locationMasterService, AnnouncementUserService $announcementUserService)
    {
        //$this->middleware('permission:agency-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->patientService = $patientService;
        $this->userWiseAgencyService = $userWiseAgencyService;
        $this->appointmentService = $appointmentService;
        $this->taskService = $taskService;
        $this->userService = $userService;
        $this->patientNotesService = $patientNotesService;
        $this->documentSentReport = $documentSentReport;
        $this->locationMasterService = $locationMasterService;
        $this->announcementUserService = $announcementUserService;
    }

    public function index(){
        $auth = auth()->user();     
        $data['locationList'] = $this->locationMasterService->AllListWithoutPaginateFilter();  
        return view('agencyDashboard.index',$data);
    }

    public function totalCountForAgency(Request $request){
        $auth = auth()->user();
        $countData = $this->patientService->totalCountPatientCaregiver($auth->agency_fk);        
        $data = ['totalPatients'=>$countData[0]->patients,'totalCaregiver'=>$countData[0]->caregivers];
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function getTodayAppointmentData(Request $request){
        $data['todayAppoinmentData'] = array();
        $auth = auth()->user();
        $data['todayAppoinmentData'] = $this->appointmentService->getTodayAppointment([$auth->agency_fk]);        
        return view('agencyDashboard.today_appointment',$data);
    }

    public function getUpcommingAppointmentData(Request $request){
        $data['upcommingAppoinmentData'] = array();
        $auth = auth()->user();
        $data['upcommingAppoinmentData'] = $this->appointmentService->getUpcommingAppointment([$auth->agency_fk]); 
        return view('agencyDashboard.upcomming_appointment',$data);
    }

    public function getStatisticData(Request $request){
        $data['statisticData'] = array();
        $auth = auth()->user();
        $data['statisticData'] = $this->patientService->getStatisticAgencyData($auth->agency_fk,$request->type);        
        return view('agencyDashboard.statistic_data',$data);
    }

    public function getNotesData(Request $request){   
        $perPage = 10; // Number of items per page
        $page = $request->get('page', 1);      
        $auth = auth()->user(); 
        $data = $this->patientNotesService->getNotesDataOfAgency($auth->agency_fk,$perPage,$page);   
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function getNotesDataNyBestUser(Request $request){    
        $perPage = 10; // Number of items per page
        $page = $request->get('page', 1);       
        $auth = auth()->user(); 
        $data = $this->patientNotesService->getNotesDataOfNyBestUser($auth->agency_fk,$perPage,$page);   
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function getLocationData(Request $request){   
        $data['locationData'] = $this->locationMasterService->getLocationData($request->location);   
        return view('agencyDashboard.location_data', $data);
    }
    public function getAnnouncementData(Request $request){        
        $data = array();
        $perPage = 2; // Number of items per page
        $page = $request->get('page', 1);  
        $from_date = $to_date = '';         
        if($request->announcement_range_date != ''){
            $date = explode('-',$request->announcement_range_date);
            if(count($date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($date[1])))??'';
            }
        }
        $data = $this->announcementUserService->getAnnouncementOfUser($from_date,$to_date,$perPage,$page);                
        return response()->json(['success'=>true,'data'=>$data],200);
    }
}