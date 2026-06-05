<?php

namespace App\Http\Controllers;
use App\Services\PatientService;
use App\Agency;
use Illuminate\Http\Request;
use App\Services\LocationMasterService;
class DashboardGraphController extends Controller
{
    protected $patientService,$locationMasterService="";
    public function __construct(PatientService $patientService,LocationMasterService $locationMasterService)
    {
        $this->middleware('permission:dashboard-graph', ['only' => ['dashboardGraph']]);

        $this->middleware('auth'); 
		$this->patientService =$patientService;
		$this->locationMasterService =$locationMasterService;
	
    } 

    public function dashboardGraph(){
        $user = Auth()->user();
        $data['agency_list'] =Agency::getAgencyList();
        $data['location_list'] = $this->locationMasterService->AllListWithoutPaginate();
        return view('dashboardGraph.dashboard-graph',$data);
    }

    public function DashBoardGraphAjax(Request $request){
        $data = $this->patientService->patientDashboardGraphStatusAjaxCount($request->agencyId,$request->record_type,$request->location_id);

        return response()->json($data);
    }

    public  function dashboardGraphAgency(Request $request){
        
        $allCounts = $this->patientService->patientDashboardGraphStatusCount($request->agency_id,$request->record_type,$request->location_id);
        $final = [];
        $final['total'] = array_sum($allCounts);
        foreach($allCounts as $key=>$val){
            $key = str_replace(' ','',$key);
            if($key =='hospitalized/rehab'){
                $key= 'hospitalized';
            }
            $final[$key] =$val;
        }
        return response()->json(['success' => "data", 'status' => 1, 'data' => $final], 200);
        
    }
}
