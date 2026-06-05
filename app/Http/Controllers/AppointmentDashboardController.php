<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\AppointmentDashboardService;
use App\Services\LocationMasterService;
use App\Services\UserService;
use App\Master;
use Illuminate\Support\Facades\Cache;


class AppointmentDashboardController extends BaseController{
  
    protected $appointmentDashboardService,$patientService,$locationMasterService,$userService = '';
    public function __construct(AppointmentDashboardService $appointmentDashboardService, LocationMasterService $locationMasterService, UserService $userService)
    {
        $this->middleware('permission:appointment-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->appointmentDashboardService = $appointmentDashboardService;
        $this->locationMasterService = $locationMasterService;
        $this->userService = $userService;
    }

    public function index(){
        $data['yearData'] = Cache::remember('dynamic_year', 10, function () { 
            return $this->appointmentDashboardService->getDynamicYears();
        });
        $data['dateRange'] = date('m/d/Y', strtotime('monday -7 days')).'-'.date('m/d/Y', strtotime('sunday'));       
        return view('appointmentDashboard.index',$data);
    }

    public function statusAppointmentData(Request $request){
        $appointmentData = Cache::remember('total_status', 10, function () {
            return $this->appointmentDashboardService->getTotalStatusCountData();
		}, 10);

        $totalPending = $totalBokked = $totalCompleted = $totalNoshow = $totalCancelled = $totalArrived=$totalProcessing = $totalNotInterested = $totalHospitalized = $totalUnableTocontact = $totalRefused = $totalCheckIn = $totalPendingTermination = $totalOnhold = $totalTerminated = $totalOnLeave = $totalTerminated = 0;
        $total1stAttempt = 0;
        $total2ndAttempt = 0;
        $total3rdAttempt = 0;
        $totalTelehealthCompleted = 0;
        $totalDeceased = 0;
        $totalSigned = 0;
        $totalSignedSentBack = 0;
        $totalPendingForms = 0;
        $totalMissed = 0;
        $totalReschedule = 0;
        $totalAppointmentMissed = 0;
        $totalNewOrderReceived = 0;
        $totalNewFormRequested = 0;
        $totalNewFormCompleted = 0;
        $totalNewServiceProvided = 0;
        $totalClosedTemp = 0;
        foreach($appointmentData as $row){
            if(strtolower($row->status) == 'pending'){
                $totalPending++;
            }elseif(strtolower($row->status) == 'cancelled'){
                $totalCancelled++;
            }elseif(strtolower($row->status) == 'booked'){
                $totalBokked++;
            }elseif(strtolower($row->status) == 'completed'){
                $totalCompleted++;
            }elseif(strtolower($row->status) == 'noshow'){
                $totalNoshow++;
            }elseif(strtolower($row->status) == 'arrived'){
                $totalArrived++;
            }elseif(strtolower($row->status) == 'processing'){
                $totalProcessing++;
            }elseif(strtolower($row->status) == 'Not interested'){
                $totalNotInterested++;
            }elseif(strtolower($row->status) == 'hospitalized/rehab'){
                $totalHospitalized++;
            }elseif(strtolower($row->status) == 'unableToContact'){
                $totalUnableTocontact++;
            }elseif(strtolower($row->status) == 'refused'){
                $totalRefused++;
            }elseif(strtolower($row->status) == 'checkin'){
                $totalCheckIn++;
            }elseif(strtolower($row->status) == 'Pending Termination'){
                $totalPendingTermination++;
            }elseif(strtolower($row->status) == 'Onhold'){
                $totalOnhold++;
            }elseif(strtolower($row->status) == 'On Leave'){
                $totalOnLeave++;
            }else if(strtolower($row->status) == 'Terminated'){
                $totalTerminated++;
            }elseif (strtolower($row->status) == '1st attempt - unable to contact') {
                $total1stAttempt++;
            }elseif (strtolower($row->status) == '2nd attempt - unable to contact') {
                $total2ndAttempt++;
            } elseif (strtolower($row->status) == '3rd attempt - unable to contact') {
                $total3rdAttempt++;
            } elseif (strtolower($row->status) == 'telehealth completed') {
                $totalTelehealthCompleted++;
            } elseif (strtolower($row->status) == 'patient deceased') {
                $totalDeceased++;
            } else if (strtolower($row->status) == 'signed') {
                $totalSigned++;
            } elseif (strtolower($row->status) == 'signed & sent back to the agency') {
                $totalSignedSentBack++;
            } elseif (strtolower($row->status) == 'telehealth completed , pending forms') {
                $totalPendingForms++;
            } elseif (strtolower($row->status) == 'appointment was missed') {
                $totalMissed++;
            } elseif (strtolower($row->status) == 'patient asked to reschedule') {
                $totalReschedule++;
            } elseif (strtolower($row->status) == 'appointment missed') {
                $totalAppointmentMissed++;
            }
            elseif (strtolower($row->status) == 'new order received') {
                $totalNewOrderReceived++;
            }
            elseif (strtolower($row->status) == 'new form requested') {
                $totalNewFormRequested++;
            }
            elseif (strtolower($row->status) == 'form completed') {
                $totalNewFormCompleted++;
            }
            elseif (strtolower($row->status) == 'service provided') {
                $totalNewServiceProvided++;
            }
            elseif (strtolower($row->status) == 'closed temporarily') {
                $totalClosedTemp++;
            }
        }
        $data = array();
        if($totalPending != 0 || $totalBokked != 0 || $totalCompleted != 0 || $totalNoshow != 0 || $totalCancelled != 0 || $totalArrived!= 0 ||$totalProcessing != 0 || $totalNotInterested != 0 || $totalHospitalized != 0 || $totalUnableTocontact != 0 || $totalRefused != 0 || $totalCheckIn != 0 || $totalPendingTermination != 0 || $totalOnhold != 0 || $totalTerminated != 0 || $totalOnLeave != 0){
             $data = ['Pending'=>$totalPending,'Bokked'=>$totalBokked,'Completed'=>$totalCompleted,'Noshow'=>$totalNoshow,'Cancelled' => $totalCancelled, 'Arrived'=> $totalArrived,'NotInterested' => $totalNotInterested, 'Hospitalized' => $totalHospitalized, 'UnableTocontact' => $totalUnableTocontact, 'Refused' => $totalRefused, 'CheckIn' => $totalCheckIn,'PendingTermination' => $totalPendingTermination, 'Onhold' => $totalOnhold, 'Terminated' => $totalTerminated, 'OnLeave' => $totalOnLeave,'Processing' => $totalProcessing,'totalTerminated' => $totalTerminated,'1st Attempt - Unable to Contact' => $total1stAttempt,'2nd Attempt - Unable to Contact' => $total2ndAttempt,'3rd Attempt - Unable to Contact' => $total3rdAttempt,'Telehealth Completed' => $totalTelehealthCompleted,'Patient Deceased' => $totalDeceased,'Signed' => $totalSigned,'Signed & Sent Back to the Agency' => $totalSignedSentBack,'Telehealth Completed , Pending Forms' => $totalPendingForms,'Appointment was missed' => $totalMissed,'Patient Asked to Reschedule' => $totalReschedule,'Appointment Missed' => $totalAppointmentMissed,'New Order Received' => $totalNewOrderReceived,'New Form Requested' => $totalNewFormRequested,'Form Completed' => $totalNewFormCompleted,'Service Provided' => $totalNewServiceProvided, 'Closed Temporarily' => $totalClosedTemp ];
        }
        
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function agencyAppointmentData(Request $request){
        $data = array();
        $agency_from_date = $agency_to_date = '';         
        if($request->agency_range_date != ''){
            $case_date = explode('-',$request->agency_range_date);
            if(count($case_date) > 0){
                $agency_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $agency_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $appointmentData = $this->appointmentDashboardService->getAgencyWiseAppointmentData($agency_from_date,$agency_to_date);
        if(!empty($appointmentData)){
            $agency_ids = Agency::getAgencyListWithIds(array_column($appointmentData,'agency_id')); 
            foreach($agency_ids as $ag){
                $agency_data[$ag['id']] = $ag['agency_name']; 
            }
            foreach($appointmentData as $row){
                if(isset($row['agency_id']) && array_key_exists($row['agency_id'],$agency_data)){
                     $data[]  = array(
                                    'agency_id' => $row['agency_id'],
                                    'name' => $agency_data[$row['agency_id']],
                                    'count' => $row['count']
                                );
                }
            }                
        }
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function servicesAppointmentData(Request $request){
        $data = array();
        // Get Top 5 services 
        $appointmentData = Cache::remember('total_services', 20, function () {
            return $this->appointmentDashboardService->getServicesWiseAppointmentData();
		});
        if(!empty($appointmentData)){
            foreach($appointmentData as $apData){
                if(isset($data[$apData['service_id']]) && !empty($data[$apData['service_id']])){
                    $data[$apData['service_id']] = $data[$apData['service_id']] + 1;
                }else{
                    $data[$apData['service_id']] = 1;
                }    
            }
            arsort($data);
            $servicesIds = array_keys($data);
            $servicesData = Master::WhereIn('id',$servicesIds)->where('del_flag', 'N')->where('is_disable', 1)->pluck('name','id');
            $topFiveData = array_slice($data, 0, 5, true);
            foreach($topFiveData as $key => $row){
                $servicesArray[] = array(
                                            'name' => $servicesData[$key],
                                            'count' => $row,
                                            'id' => $key
                );
            }
        }        
        return response()->json(['success'=>true,'data'=>$servicesArray],200);
    }

    public function userAppointmentData(Request $request){
        $data = array();
        $user_from_date = $user_to_date = '';         
        if($request->user_range_date != ''){
            $case_date = explode('-',$request->user_range_date);
            if(count($case_date) > 0){
                $user_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $user_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $appointmentData = $this->appointmentDashboardService->getUserWiseAppointmentData($user_from_date, $user_to_date);
        if(!empty($appointmentData)){
            $user_ids = $this->userService->getUsersByIds(array_column($appointmentData,'created_by'));
            foreach($user_ids as $user){
                $user_data[$user['id']] = $user['first_name'].' '.$user['last_name']; 
            }
            foreach($appointmentData as $row){
                if(array_key_exists($row['created_by'],$user_data)){
                    $data[]  = array(
                        'created_by' => $row['created_by'],
                        'name' => $user_data[$row['created_by']],
                        'count' => $row['count']
                    );
                }
            }                
        }   
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function locationAppointmentData(Request $request){
        $data = array();
        $location_from_date = $location_to_date = '';         
        if($request->location_range_date != ''){
            $case_date = explode('-',$request->location_range_date);
            if(count($case_date) > 0){
                $location_from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $location_to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data = array();
        $appointmentData = $this->appointmentDashboardService->getLocationWiseAppointmentData($location_from_date,$location_to_date);
        if(!empty($appointmentData)){
            $location_ids = $this->locationMasterService->getDetailbyIds(array_column($appointmentData,'location_id'));
            foreach($location_ids as $lc){
                $location_data[$lc['id']] = $lc['location_name']; 
            }
            if(isset($location_data) && !empty($location_data)){
                foreach($appointmentData as $row){
                    if(array_key_exists($row['location_id'],$location_data)){
                        $data[]  = array(
                            'location_id' => $row['location_id'],
                            'name' => $location_data[$row['location_id']],
                            'count' => $row['count']
                        );
                    }
                }  
            }          
        }        
        return response()->json(['success'=>true,'data'=>$data],200);
    }
    
    public function monthlyWisePatientChartData(Request $request){
        $appointmentData = array();
        $search_data = self::getSearchData($request);  
        $search_datas = $search_data['search_data'];
        $group_by = $search_data['group_by'];
        $appointmentData = $this->appointmentDashboardService->getMonthWisePatientData($search_datas,$group_by);
        foreach($appointmentData as $key => $ap){
            if($request->type == 'yearly'){
                $appointmentData[$key]['name'] = (string) $ap['year'];
            }else if($request->type == 'monthly'){
                $appointmentData[$key]['name'] = $ap['month_name'];
            }else if($request->type == 'weekly'){
                $appointmentData[$key]['name'] = $ap['day'];
            }
        }            
        return response()->json(['success'=>true,'data'=>$appointmentData],200);
    }

    public function monthlyWiseComparisionChartData(Request $request){
        $finalCurrData = array();
        $currentYear = date('Y');
        $previousYear = date("Y",strtotime("-1 year"));
        $currentYearData = array('start_date' => $previousYear.'-01-01', 'end_date' => $currentYear.'-12-31');
        $appointmentData = $this->appointmentDashboardService->getMonthWisePatientData($currentYearData,'year');

        if(!empty($appointmentData)){
            foreach($appointmentData as $cdata){
                $finalCurrData[] = array('year' => (string)$cdata['year'],
                                     'count' => $cdata['total_records']
                                    );
            }
        } 
       
        return response()->json(['success'=>true,'data'=>$finalCurrData],200);
    }

    public function getTotalounts(){
        $appointmentData = array();
        $whereConidtion = Cache::remember('where_condition_agency', 10, function () {
            return $this->appointmentDashboardService->getWhereConditionForAgency();
        }, 10);
        
        $appointmentData['totalPatient'] = Cache::remember('total_patient', 10,function () use($whereConidtion){
            return count($this->appointmentDashboardService->getTotalCountData('Patient',$whereConidtion)); 
        }, 10);
        $appointmentData['totalCaregiver'] = Cache::remember('total_caregiver', 10, function () use($whereConidtion){
            return count($this->appointmentDashboardService->getTotalCountData('Caregiver',$whereConidtion));
        }, 10);
        $appointmentData['totalAgencies'] = Cache::remember('total_agency', 10, function (){
            return count($this->appointmentDashboardService->totalCountForAgencies());
        }, 10);
        $appointmentData['totalHHACaregiver'] = Cache::remember('total_hha_caregiver', 10, function () {
            return count($this->appointmentDashboardService->totalHHACaregiverCount()); 
        }, 10);
        $appointmentData['totalHHAPatient'] = Cache::remember('total_hha_patient', 10, function () {
            return count($this->appointmentDashboardService->totalHHAPatientCount());
        }, 10);
        $appointmentData['totalRemote'] = Cache::remember('total_patient', 10, function () {
            return count($this->appointmentDashboardService->totalRemoteClientCount());
        }, 10);
        $appointmentData['totalVisiting'] = Cache::remember('total_remote', 10, function (){
            return count($this->appointmentDashboardService->totalVisitingCounts()); 
        }, 10);
        $appointmentData['totalAppointment'] = Cache::remember('total_appointment', 10, function () use($whereConidtion){
            return count($this->appointmentDashboardService->getTotalCountData('',$whereConidtion));
        }, 10);       
        return response()->json(['success'=>true,'data'=>$appointmentData],200);
    }
    
    public function getAgencyData(Request $request){
        $data = array();
        $data['appoinmentData'] = Cache::remember('agency_data', 10, function () {
            return $this->appointmentDashboardService->getAgencyAppointmentData();//get Top 5 Agency Data
        }, 10);
        return view('appointmentDashboard.agency_data',$data);
    }

    public function getUserData(Request $request){
        $data = array();
        $data['appoinmentData'] = Cache::remember('user_data', 10, function () {
            return $this->appointmentDashboardService->getUserAppointmentData();//get Top 5 user Data
        }, 10);
        return view('appointmentDashboard.user_data',$data);
    }

    public function getLocationData(Request $request){
        $data = array();
        $data['appoinmentData'] = Cache::remember('location_data', 10, function () {
            return $this->appointmentDashboardService->getLocationAppointmentData();//get Top 5 user Data
        }, 10);
        return view('appointmentDashboard.locations_data',$data);
    }

    public function getSearchData($data){
        $year = $data['year'];
        $month = $data['month'];
        $week = $data['week'];
        $search_data = [];
        if($data['type'] == 'monthly'){
            $group_by = 'month';
            if(empty($year)){
                $search_data = array();
            }else if(empty($month)){
                $search_data = array(
                    'start_date' => $year.'-01-01',
                    'end_date' => $year.'-12-31'
                );     
            }else{
                $search_data = array(
                    'start_date' => $year.'-'.$month.'-01',
                    'end_date' => $year.'-'.$month.'-31'
                );
            }
        }else if($data['type'] == 'weekly'){
            $group_by = 'day';
            if(empty($week) && empty($year) && empty($month)){
                $search_data = array();
            }else{
                if(empty($week)){
                    if(empty($month)){
                        $search_data = array(
                            'start_date' => $year.'-01-01',
                            'end_date' => $year.'-12-31'
                        );     
                    }else{
                        $search_data = array(
                            'start_date' => $year.'-'.$month.'-01',
                            'end_date' => $year.'-'.$month.'-31'
                        ); 
                    }  
                }else{
                    $weeks = explode('-',$week);
                    $search_data = array(
                        'start_date' => $year.'-'.$month.'-'.$weeks[0],
                        'end_date' => $year.'-'.$month.'-'.$weeks[1]
                    );
                }
            }
        }else{
            $group_by = 'year';
            if(!empty($year)){
                $search_data = array(
                    'start_date' => $year.'-01-01',
                    'end_date' => $year.'-12-31'
                );
            }
        } 
        $searchData = array(
                'search_data' => $search_data,
                'group_by' => $group_by
            );
        return $searchData;     
    }
}