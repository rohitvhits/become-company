<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Services\ThirdPartyPatientMasterService;
use Illuminate\Support\Facades\Cache;

class VisitingAidDashboardController extends BaseController{
    protected $patientService="";
    public function __construct(PatientService $patientService,ThirdPartyPatientMasterService $thirdPartyPatientMaster)
    {
        $this->middleware('permission:visiting-aid-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->patientService = $patientService;
        $this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
    }

    public function index(){
        return view('visitingAidDashboard.index');
    }

    public function visitingListData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data['vistingAidData'] = $this->thirdPartyPatientMaster->getPatientListDashboard($from_date,$to_date);
        return view('visitingAidDashboard.visiting_aid_list',$data);
    }

    public function visitingAgencyWiseChartData(Request $request){
        $data = array();
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $vistingAidData = $this->thirdPartyPatientMaster->getAgencyWiseChartData($from_date, $to_date);
        $agency_ids = array_column($vistingAidData,'agency_id');
        $agencyData = Cache::get('agency', function () use($agency_ids) {
            return Agency::getAgencyListWithIds($agency_ids);
		}, 10);
        foreach($agencyData as $aData){
            $agency[$aData['id']] = $aData['agency_name'];
        }
        foreach($vistingAidData as $vdata){
            $data[] = array(
                'name' => array_key_exists($vdata['agency_id'],$agency) ? trim($agency[$vdata['agency_id']]) : '',
                'count' => $vdata['count']
            );
        }
        return response()->json(['error_msg' => "Success", 'data' => $data], 200);
    }

    public function visitingTypeWiseChartData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data = $this->thirdPartyPatientMaster->getTypeWiseChartData($from_date, $to_date);
        return response()->json(['error_msg' => "Success", 'data' => $data], 200);
    }

    public function visitingServiceStatusWiseChartData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data = $this->thirdPartyPatientMaster->getServiceStatusWiseChartData($from_date, $to_date);
        return response()->json(['error_msg' => "Success", 'data' => $data], 200);
    }

    public function visitingCountData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data['visiting_aid'] = count($this->thirdPartyPatientMaster->getVisitingCountData($from_date, $to_date));
        $data['total_agencies'] = count($this->thirdPartyPatientMaster->getAgencyWiseChartData($from_date, $to_date));
        $data['total_patients'] = count($this->thirdPartyPatientMaster->getPatientData($from_date, $to_date));
        $data['pending'] = count($this->thirdPartyPatientMaster->getStatusWiseData($from_date, $to_date,'pending'));
        $data['completed'] = count($this->thirdPartyPatientMaster->getStatusWiseData($from_date, $to_date,'completed'));
        $data['overdue'] = count($this->thirdPartyPatientMaster->getOverdueData($from_date, $to_date));
        return response()->json(['error_msg' => "Success", 'data' => $data], 200);
    }

    public function visitingServicesWiseChartData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $case_date = explode('-',$request->range_date);
            if(count($case_date) > 0){
                $from_date = date('Y-m-d',strtotime(trim($case_date[0])))??'';
                $to_date = date('Y-m-d',strtotime(trim($case_date[1])))??'';
            }
        }
        $data['vistingAidData'] = count($this->thirdPartyPatientMaster->getServicesWiseChartData($from_date, $to_date));
        return response()->json(['error_msg' => "Success", 'data' => array()], 200);
    }
}