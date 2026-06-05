<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\DocumentPatientService;
use App\Services\PatientService;
use App\Services\DocApprovalQuestionsService;
use App\Services\AgencyWiseServiceService;
use App\Helpers\Utility;
use App\Master;
use Illuminate\Support\Facades\Cache;

class DocumentDashboardController extends BaseController{
    protected $documentPatientService,$patientService,$docApprovalQuestionsService,$agencyWiseServiceService;
    public function __construct( DocumentPatientService $documentPatientService, PatientService $patientService,DocApprovalQuestionsService $docApprovalQuestionsService, AgencyWiseServiceService $agencyWiseServiceService)
    {
        $this->middleware('permission:doc-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->documentPatientService = $documentPatientService;
        $this->patientService = $patientService;
        $this->docApprovalQuestionsService = $docApprovalQuestionsService;
        $this->agencyWiseServiceService = $agencyWiseServiceService;
    }

    public function index(){
        $user = auth()->user();
        $data['serviceList'] = Cache::get('patient_master_services', function ()  use ($user) {
			$agencyId = $user->agency_fk;
			$getAgencyWiseList = $this->agencyWiseServiceService->getServiceNew($agencyId, "");
			if (!empty($getAgencyWiseList[0])) {
				return  $getAgencyWiseList;
			} else {
				return  Master::getServiceRequest(1);
			}
		}, 10 * 60);
		
		 $data['agencyList'] = Cache::get('patient_agencies', function ()  use ($user) {
			return Agency::getAgencyListWithUserAgency();
		}, 10 * 60);
		
		$data['agencyCnt'] = count($data['agencyList']);
		
        return view('docDashboard.index',$data);
    }

    public function getPateintDocData(Request $request){
        $data['page'] = $request->page;
        $response = $this->documentPatientService->getPendingData($request->all());
      
        $data['document_list'] = $response;
        return view('docDashboard.doc_data',$data);
    }
    
    public function getQuesionsData (Request $request){
       $response = $this->docApprovalQuestionsService->getAllQuesions();
       return response()->json(['error_msg' => "Success", 'status' => 0, 'data' => $response], 200);
    }
}