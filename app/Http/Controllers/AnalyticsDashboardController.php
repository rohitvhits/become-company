<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\AnalyticsDashboardService;
use App\Services\UserService;
use App\Services\LocationMasterService;
use App\Helpers\Utility;

class AnalyticsDashboardController extends BaseController{
    protected $patientService,$visitingAidService,$analyticsDashboardService, $userService,$locationMasterService="";
    public function __construct(PatientService $patientService,ThirdPartyPatientMasterService $visitingAidService, AnalyticsDashboardService $analyticsDashboardService, UserService $userService, LocationMasterService $locationMasterService)
    {
        $this->middleware('permission:analytics-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->patientService = $patientService;
        $this->visitingAidService = $visitingAidService;
        $this->analyticsDashboardService = $analyticsDashboardService;
        $this->userService = $userService;
        $this->locationMasterService = $locationMasterService;
    }

    public function index(){
        $data['agencyList'] = Agency::getAllAgencyList();
        return view('analyticsDashboard.index',$data);
    }

    public function currentInprogressPatient(Request $request){
        $data = array();
        $data = $this->analyticsDashboardService->currentInprogressPatientData($request->agency_id,$request->record_type);
        if(isset($data[0]) && !empty($data[0])){
			foreach($data as $val){
				$val->last_status_update = Utility::timeAgo($val->last_status_update);
			}
		}
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function recentUpdateStatus(Request $request){
        $data = array();
        $data = $this->analyticsDashboardService->recentUpdateStatusData($request->agency_id,$request->record_type);
        if(isset($data[0]) && !empty($data[0])){
			foreach($data as $val){
				$val->last_status_update = Utility::timeAgo($val->last_status_update);
			}
		}
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function visitingAidPatient(Request $request){
        $data = array();
        $data = $this->analyticsDashboardService->visitingAidPatientData($request->agency_id);
        if(isset($data[0]) && !empty($data[0])){
			foreach($data as $val){
				$val->created_date = Utility::timeAgo($val->created_date);
			}
		}
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function visitingAidType(Request $request){
        $data = array();
        $data = $this->analyticsDashboardService->visitingAidTypeData($request->agency_id,$request->record_type);
        if(isset($data[0]) && !empty($data[0])){
			foreach($data as $val){
				$val->created_date = Utility::timeAgo($val->created_date);
			}
		}
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    /* Notes  */ 
    public function recentNotes(Request $request){
        // $userList = $this->userService->getAgencyUserList();
		$response =$this->analyticsDashboardService->getRecentNotesByAgencyUser($request->agency_id,$request->record_type);
		if(!empty($response[0])){
			foreach($response as $val){
				$val->created_date = Utility::timeAgo($val->created_date);
			}
		}
		return  response()->json(['status'=>true,'data'=>$response]);
    }

    public function visitingDueDate(Request $request){
        $data = array();
        $data = $this->analyticsDashboardService->visitingDueDateData($request->agency_id,$request->record_type);
        if(isset($data[0]) && !empty($data[0])){
			foreach($data as $val){
				$val->due_date_convert = Utility::timeAgo($val->due_date);
				$val->created_date = Utility::timeAgo($val->created_date);
			}
		}
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function locationWiseStatus(Request $request){
        $data = array();
        $locationWiseData = $this->analyticsDashboardService->locationWiseStatusData($request->agency_id,$request->record_type);
        
        foreach($locationWiseData as $ldata){
            if(isset($data[$ldata['location_id']][$ldata->status])){
                $data[$ldata['location_id']][$ldata->status] = $data[$ldata['location_id']][$ldata->status] + 1;
            }else{
                $data[$ldata['location_id']][$ldata->status] = 1;
            }
        }
        $locationdata = $this->locationMasterService->searchLocation();
        $ag_id = '';
        if(isset($request->agency_id )){
            foreach($request->agency_id as $id){
                $ag_id = $ag_id.'agency_fk[]='.$id.'&';
            }
        }        
        return view('analyticsDashboard.analytics_location_data',['agency_id'=>$ag_id,'type'=> $request->record_type,'data'=>$data,'locationdata' => $locationdata]);
    }
    public function countStatusData(Request $request){
        $data = array();
        $countData = $this->analyticsDashboardService->countStatusData($request->agency_id,$request->record_type);

        foreach($countData as $d){
            if($d['status'] == 'arrived'){
                $data['arrived'] = $d['count']??0;
            }else if($d['status'] == 'processing'){
                $data['processing'] = $d['count']??0;
            }else if($d['status'] == 'check in'){
                $data['check_in'] = $d['count']??0;
            }
        }
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function documentRecentData(Request $request){
        $data = array();
        $data = $this->analyticsDashboardService->documentRecentData($request->agency_id,$request->record_type);
        if(isset($data[0]) && !empty($data[0])){
			foreach($data as $val){
				$val->created_date = Utility::timeAgo($val->created_date);
			}
		}
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function agencyWiseStatus(Request $request){
        $data = array();
        $agencyWiseData = $this->analyticsDashboardService->agencyWiseStatusData($request->agency_id,$request->record_type);
        
        foreach($agencyWiseData as $ldata){
            if(isset($data[$ldata['agency_id']][$ldata->status])){
                $data[$ldata['agency_id']][$ldata->status] = $data[$ldata['agency_id']][$ldata->status] + 1;
            }else{
                $data[$ldata['agency_id']][$ldata->status] = 1;
            }
        }
        $agency_ids = array_keys($data);
        $agencyData = Agency::getAgencyListWithIds($agency_ids);
        $ag_id = '';
        if(isset($request->agency_id )){
            foreach($request->agency_id as $id){
                $ag_id = $ag_id.'agency_fk[]='.$id.'&';
            }
        }
        return view('analyticsDashboard.analytics_agency_data',['agency_id'=>$ag_id,'type'=> $request->record_type,'data'=>$data,'agencyData' => $agencyData]);
    }
}