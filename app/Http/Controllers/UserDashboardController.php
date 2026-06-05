<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Services\HHACaregiverService;
use App\Services\HHAPatientService;
use App\Services\AlayacareService;
use App\Services\AlayacareClientService;
use App\Services\RobortService;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\LocationMasterService;
use App\Services\AppointmentService;
use App\Services\PatientNotesService;
use App\Services\UserService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Master;
use DB;

class UserDashboardController extends BaseController{
    protected $patientService,$hhaCaregiverService,$hhaPatientService,$alayacareService,$alayacareClientService,$remoteService,$visitingAidService,$locationMasterService,$appointmentService,$patientNotesService,$userService,$patientServicesRequest="";
    public function __construct(PatientService $patientService,HHACaregiverService $hhaCaregiverService,HHAPatientService $hhaPatientService,ThirdPartyPatientMasterService $visitingAidService,AlayacareService $alayacareService,AlayacareClientService $alayacareClientService,RobortService $remoteService,LocationMasterService $locationMasterService,AppointmentService $appointmentService, PatientNotesService $patientNotesService, UserService $userService, PatientServicesRequest $patientServicesRequest, PatientWiseServicesRequests $patientWiseServicesRequest)
    {
        $this->middleware('permission:user-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->patientService = $patientService;
        $this->hhaPatientService = $hhaPatientService;
        $this->hhaCaregiverService = $hhaCaregiverService;
        $this->alayacareService = $alayacareService;
        $this->alayacareClientService = $alayacareClientService;
        $this->remoteService = $remoteService;
        $this->visitingAidService = $visitingAidService;
        $this->locationMasterService = $locationMasterService;
        $this->appointmentService = $appointmentService;
        $this->patientNotesService = $patientNotesService;
        $this->userService = $userService;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->patientWiseServicesRequest = $patientWiseServicesRequest;
    }

    public function index(){
        $auth = auth()->user();
        $data['agencyList'] = Agency::getAllAgencyListWithoutAnyCondition();
        $data['location_list'] = $this->locationMasterService->AllListWithoutPaginate();
        return view('userDashboard.index',$data);
    }

    public function totalCountForCaregiverPatientAgency(Request $request){

        $case_from_date = $case_to_date = '';         
        if($request->case_range_date != ''){
            $case_date = explode('-',$request->case_range_date);
            if(count($case_date) > 0){
                $case_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $case_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }

        $totalPatientCount = $this->patientServicesRequest->totalPatientCount('Patient',$case_from_date,$case_to_date);
        $totalCaregiverCount = $this->patientServicesRequest->totalPatientCount('Caregiver',$case_from_date,$case_to_date);

        $totalAgenciesCount = Agency::totalCountForAgenciesDateWise($case_from_date,$case_to_date);

        $totalHHACaregiverCount = $this->hhaCaregiverService->totalHHACaregiverCountDateWise($case_from_date,$case_to_date);
        $totalHHAPatientCount = $this->hhaPatientService->totalHHAPatientCountDateWise($case_from_date,$case_to_date);

        $totalRemoteCount = $this->remoteService->totalRemoteClientCountDateWise($case_from_date,$case_to_date);
        $totalVisitingAidsCount = $this->visitingAidService->totalVisitingCountsDateWise($case_from_date,$case_to_date);

        $totalPendingCount = $this->patientServicesRequest->totalCountPatientStatusServiceWise('Pending',$case_from_date,$case_to_date);
        $totalCompletedCount = $this->patientServicesRequest->totalCountPatientStatusServiceWise('completed',$case_from_date,$case_to_date);
        $totalBookedCount = $this->patientServicesRequest->totalCountPatientStatusServiceWise('Scheduled',$case_from_date,$case_to_date);
        $totalProcessingCount = $this->patientServicesRequest->totalCountPatientStatusServiceWise('MarkAsProcessing',$case_from_date,$case_to_date);
        $totalCasesCount = $this->patientServicesRequest->totalCountPatientStatusServiceWise('all',$case_from_date,$case_to_date);
        
        $data = ['totalPatient'=>$totalPatientCount,'totalCaregiver'=>$totalCaregiverCount,'totalAgencies'=>$totalAgenciesCount,'totalHHACaregiver'=>$totalHHACaregiverCount,'totalHHAPatientCount'=>$totalHHAPatientCount,'totalRemoteCount'=>$totalRemoteCount,'totalVisitingAidsCount'=>$totalVisitingAidsCount,'totalPendingCount'=>$totalPendingCount,'totalCompletedCount' => $totalCompletedCount, 'totalBookedCount' => $totalBookedCount,'totalProcessingCount' => $totalProcessingCount,'totalCasesCount' => $totalCasesCount];
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    /* Location Wise Appointment Data */
    public function agencyWisePatientCaregiverGraph(Request $request){
       
        $case_from_date = $case_to_date = '';         
        if($request->case_range_date != ''){
            $case_date = explode('-',$request->case_range_date);
            if(count($case_date) > 0){
                $case_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $case_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $getAllAgencyIds = Agency::getAllAgencyIds($request->agency_id);
 
        $locationsArray = [];
        $locations = $this->locationMasterService->getLocationCities();
        
        $locationsIds = [];
        foreach($locations as $key=>$lc){
            $locationsIds[] = $lc;
        }

        $response = $this->patientServicesRequest->getCaregiverCount($getAllAgencyIds->toArray(),$request->agency_type_id,$locationsIds,$case_from_date,$case_to_date);        
        $appointmentLocationIds = [];
        if(!empty($response[0])){
            foreach($response as $val){
                $caregiverCount = 0;
                $patientCount = 0;
               
                if(isset($appointmentLocationIds[$val->patient->location_id])){
                    if($val->patient->type =='Caregiver'){
                        $appointmentLocationIds[$val->patient->location_id]['caregiver'] = $appointmentLocationIds[$val->patient->location_id]['caregiver'] +1;
                    }
                    if($val->patient->type =='Patient'){
                        $appointmentLocationIds[$val->patient->location_id]['patient'] = $appointmentLocationIds[$val->patient->location_id]['patient'] +1;
                    }
                }else{
                    $appointmentLocationIds[$val->patient->location_id] = [];
                    if($val->patient->type =='Caregiver'){
                        $caregiverCount = 1;
                    }
                    if($val->patient->type =='Patient'){
                        $patientCount = 1;
                    }
                    $appointmentLocationIds[$val->patient->location_id]['caregiver'] = $caregiverCount;
                    $appointmentLocationIds[$val->patient->location_id]['patient'] = $patientCount;
                }
            }
        }

        foreach($locations as $key=>$lc){
            if((isset($appointmentLocationIds[$lc]['caregiver']) && $appointmentLocationIds[$lc]['caregiver'] > 0) || (isset($appointmentLocationIds[$lc]['patient']) && $appointmentLocationIds[$lc]['patient'] > 0)){
                $temp = [];
                $temp['id'] = $lc;
                $temp['city'] = $key;
                $temp['caregiver_count'] = isset($appointmentLocationIds[$lc]['caregiver'])?$appointmentLocationIds[$lc]['caregiver']:0;
                $temp['patient_count'] = isset($appointmentLocationIds[$lc]['patient'])?$appointmentLocationIds[$lc]['patient']:0;
                $temp['case_range_date'] = $request->case_range_date;
                $locationsArray[] = $temp; 
            }
        }
       
        return response()->json(['success'=>true,'data'=>$locationsArray],200);
    }

    /* Services Wise Appointment Data */
    public function serviceWiseGraph(Request $request){
      
        $response = Master::getServiceTypeBase($request->type,$request->service_id);
        $servicesIds = [];
        $finalArray = [];
        foreach($response as $res){
            $temp = [];
            $temp['id'] = $res->id;
            $temp['name'] = $res->name;
            $temp['total'] = 0;
            $servicesIds[] = $temp;
           
        }
        
        $case_from_date = $case_to_date = '';         
        if($request->case_range_date != ''){
            $case_date = explode('-',$request->case_range_date);
            if(count($case_date) > 0){
                $case_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $case_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $getPatientCount = $this->patientServicesRequest->getTotalAppointmentCountForService($request->agency_id,$request->location_id,$request->type,$case_from_date,$case_to_date);
        
        $totalServiceCount = [];
        foreach($getPatientCount as $res){
          
            $explode = explode(',',$res);
           
            foreach($explode as $key=>$vals){
               
                $cnt  =1;
            
                $srvId = trim($vals);
                if(isset($totalServiceCount[$srvId])){
                    $totalServiceCount[$srvId] =$totalServiceCount[$srvId] +1; 
                }else{
                    $totalServiceCount[$srvId] = $cnt;
                }
            }
        }
        $finalArray = [];
        foreach($servicesIds as $vas){
            $vas['total'] = isset($totalServiceCount[$vas['id']])?$totalServiceCount[$vas['id']]:0;
            if($vas['total'] !=0){
                $finalArray[] = $vas;
            }
        }

        return response()->json(['success'=>true,'data'=>$finalArray],200);
    }

    /* Agency Wise Data */
    public function locationWiseGraph(Request $request){   
        $case_from_date = $case_to_date = '';         
        if($request->case_range_date != ''){
            $case_date = explode('-',$request->case_range_date);
            if(count($case_date) > 0){
                $case_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $case_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $response = $this->patientServicesRequest->getPatientServiceCount($request->location_id,$request->agency_id,$request->location_type_id,$case_from_date,$case_to_date);
        return response()->json(['success'=>true,'data'=>$response],200);
    }

    public function getTodayAppointmentData(Request $request){
        $case_from_date = $case_to_date = '';         
        if($request->case_range_date != ''){
            $case_date = explode('-',$request->case_range_date);
            if(count($case_date) > 0){
                $case_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $case_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $appoitmentData = $this->appointmentService->getTodayAppointmentDateWise($case_from_date,$case_to_date);
        if(!empty($appoitmentData)){
            $data['todayAppoinmentData'] = $appoitmentData;    
        }
        return view('userDashboard.today_appointment',$data);
    }
    
    public function getUpcommingAppointmentData(Request $request){
        $case_from_date = $case_to_date = '';         
        if($request->case_range_date != ''){
            $case_date = explode('-',$request->case_range_date);
            if(count($case_date) > 0){
                $case_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $case_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data['upcommingAppoinmentData'] = [];
        $appoitmentData = $this->appointmentService->getUpcommingAppointmentDateWise($case_from_date,$case_to_date);
        if(!empty($appoitmentData)){
            $data['upcommingAppoinmentData'] = $appoitmentData;
        }
        return view('userDashboard.upcomming_appointment',$data);
    }

    /* Services Wise Status Data */
    public function statusWiseGraph(Request $request){
        $response = array();
        $count = 0;
        $case_from_date = $case_to_date = '';         
        if($request->case_range_date != ''){
            $case_date = explode('-',$request->case_range_date);
            if(count($case_date) > 0){
                $case_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $case_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data = $this->patientServicesRequest->getStatusWiseData($request->agency_id,$case_from_date,$case_to_date);        
        if(count($data) >0){
            foreach($data as $key => $statusData){
                if($key != 'patient'){
                    if($statusData == 0){
                        $count++;
                    }
                    $temp = [];
                    $temp["name"] = $key;
                    $temp["total"] = $statusData;
                    if($key){
                        $statusText = $this->getStatusData($key)??$key;
                        $temp["statusText"] = $statusText?$statusText:'';
                    }
                    $response[] = $temp;
                }
            }
        }
        if(count($data) == $count){
            $response = [];
        }
        return response()->json(['success'=>true,'data'=>$response],200);
    }

    /* Notes  */ 
    public function getNotesData(Request $request){
        $perPage = 10; // Number of items per page
        $page = $request->get('page', 1);
        $userList = $this->userService->getAgencyUserList();
        $case_from_date = $case_to_date = '';         
        if($request->case_range_date != ''){
            $case_date = explode('-',$request->case_range_date);
            if(count($case_date) > 0){
                $case_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $case_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        if($request->notes_type == 'Agency'){
            $data = $this->patientNotesService->getAllNotesOfAgencyDateWise($userList,$perPage,$page,$case_from_date,$case_to_date); 
        }else{
            $data = $this->patientNotesService->getAllNotesOfNyBestUserDateWise($userList,$perPage,$page,$case_from_date,$case_to_date); 
        }                        
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function getStatusData($status)
    {
        $statusText = "";
        if($status == 'Booked') { $statusText = 'Scheduled'; }
        if($status == 'Pending') { $statusText = 'Pending'; }
        if($status == 'Completed') { $statusText = 'MarkAsCompleted'; }
        if($status == 'Cancelled') { $statusText = 'MarkAsCancel'; }
        if($status == 'Cancelled') { $statusText = 'Cancelled'; }
        if($status == 'No Show') { $statusText = 'MarkAsNoShow'; }
        if($status == 'Missed') { $statusText = 'missed'; }
        if($status == 'Hospitalized / Rehab'){ $statusText = 'MarkAsHospitalized/Rehab'; }
        if($status == 'Unable To Contact') { $statusText = 'UnableToContact'; }
        if($status == 'Mark As Check In') { $statusText = 'MarkAsCheckIn'; }
        if($status == 'Processing') { $statusText = 'MarkAsProcessing'; }
        if($status == 'Refused') { $statusText = 'MarkAsRefused'; }
        if($status == 'Pending Termination') { $statusText = 'PendingTermination'; }
        if($status == 'On Hold') { $statusText = 'OnHold'; }
        if($status == 'On Leave') { $statusText = 'OnLeave'; }
        if($status == 'Terminated') { $statusText = 'Terminated'; }
        if($status == 'In Service') { $statusText = 'InService'; }
        if($status == 'Undo') { $statusText = 'Undo'; } 
        return $statusText;
    }
}