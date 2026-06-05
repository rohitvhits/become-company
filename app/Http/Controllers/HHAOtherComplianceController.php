<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Agency;
use App\Helpers\HHAAppointmentHelper;
use App\Helpers\HHACaregiversHelper;
use Excel;
use App\Helpers\UserHelper;
use App\Services\PatientService;
use App\Master;
use App\Services\DocumentPatientService;
use App\Services\HHAOtherComplianceService;
use App\Model\HHAOffice;
use Illuminate\Support\Facades\Cache;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Model\HHACaregivers;
use App\Model\HHAAppointment;
use App\Services\HHACaregiverService;
use App\Helpers\Utility;
use Storage;
use App\Services\LogsService;
use App\Services\HHALogService;
class HHAOtherComplianceController extends BaseController
{

    protected $hhaOtherComplianceService;
    protected $patientServicesRequest;
    protected $patientWiseServicesRequests;
    protected $patientService;
    protected $hhaCaregiverService;
    protected $documentPatientService;
    protected $hhaLogService;
    public function __construct(
        PatientService $patientService,
        DocumentPatientService $documentPatientService,
        HHAOtherComplianceService $hhaOtherComplianceService,
        PatientServicesRequest $patientServicesRequest,
        PatientWiseServicesRequests $patientWiseServicesRequests,
        HHACaregiverService $hhaCaregiverService,
        HHALogService $hhaLogService
    )
    {
        $this->middleware('auth');
        $this->middleware('permission:hha-other-compliance', ['only' => ['index','hhaAppoitmentAjax']]);
        $this->middleware('permission:hha-other-compliance-export', ['only' => ['exportCsv']]);
        $this->patientService = $patientService;
        $this->documentPatientService = $documentPatientService;
        $this->hhaOtherComplianceService=$hhaOtherComplianceService;
        $this->patientServicesRequest = $patientServicesRequest;
		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->hhaCaregiverService = $hhaCaregiverService;
        $this->hhaLogService = $hhaLogService;
    }

    public function index(Request $request)
    {

        $data['menu'] = "user";
        $data['auth'] = $data['user'] = $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        if (in_array($user['user_type_fk'], array(3, 4, 5, 6))) {
            abort(404);
        }
        $data['agency_list'] = Agency::getHHAAgencyList();
        $data['office_list'] =  Cache::get('hha_office_list_other', function () {
			return  HHAOffice::getOfficeList();
		}, 10 * 60);
        return view("hha_other_compliance.hha_other_compliance_list", $data);
    }


    public function hhaAppoitmentAjax(Request $request)
    {
       
        $data['agency_fk'] = $agency_fk = $request->input('agency_fk');
        $data['fname'] = $fname = $request->input('fname');
        $data['code'] = $code = $request->input('code');
        $data['caregiver_phone'] = $caregiver_phone = $request->input('caregiver_phone');
        $data['hha_code'] = $hha_code = $request->input('hha_code');
        $data['medical_name'] = $medical_name = $request->input('medical_name');
        $data['due_date'] = $due_date = $request->input('due_date');
        $data['status'] = $status = $request->input('status');
        $data['office_id'] = $office_id = $request->input('office_id');


        $data['query'] = $this->hhaOtherComplianceService->hhaAppoitnmentList($agency_fk, $fname, $code, $caregiver_phone, $hha_code, $medical_name, $due_date, $status,$office_id);
    
        
      
        return view("hha_other_compliance.hha_other_compliance_list_ajax", $data);
    }

    public function addOtherHHACompliance(Request $request)
    {
        $auth = auth()->user();
        $ids = $request->input('final_array');
        $update = 0;
       
        if (!empty($ids[0])) {
            foreach ($ids as $val) {

                $getDetails = $this->hhaOtherComplianceService->getDetailsById($val);
           
                $medicalId = '';
                if (isset($getDetails->medical_name) && $getDetails->medical_name != '') {
                    $getService = Master::getServiceName($getDetails->medical_name);
                    if (isset($getService->id) && $getService->id != '') {
                        $medicalId =  $getService->id;
                    } else {
                        $masters = array('name' => $getDetails->medical_name, 'master_type_fk' => 23, "types" => "Caregiver", 'del_flag' => 'N', 'user_id' => $auth['id'], 'created_at' => date('Y-m-d H:i:s'));
                        $inserty = new Master($masters);
                        $inserty->save();
                        $medicalId = $inserty->id;
                    }
                }

                $final_array = array(
                    'first_name' => $getDetails->caregiver_first_name,
                    'middle_name' => $getDetails->caregiver_middle_name,
                    'last_name' => $getDetails->caregiver_last_name,
                    'full_name' => $getDetails->caregiver_first_name.' '.$getDetails->caregiver_last_name,
                    'patient_code' => $getDetails->caregiver_code,
                    'agency_id' => $getDetails->agency_id,
                    'phone' => $getDetails->caregiver_phone,
                    'mobile' => $getDetails->caregiver_phone,
                    'type'=>'Caregiver',
                    'hha_other_id' => $getDetails->id,
                    'service_id' => $medicalId,
                    'link_hha_caregiver' => $getDetails->caregiver_id,
                    'dob' => $getDetails->dob,
                    'gender' => $getDetails->gender,
                    'referral_type'=>'HHA Exchange'
                );

             
                $update = $this->patientService->save($final_array);
                if($update){
                    $patientServiceCount = $this->patientServicesRequest->getServiceCountPatientId($update);

                        if (count($patientServiceCount) == 0) {

                            
                            if($medicalId !=""){
                                $patientServiceLastId = $this->patientServicesRequest->save([
                                    'patient_id' => $update,
                                    'follow_up_date' => null,
                                    'due_date' => null,
                                    'status' => "Pending",
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth()->user()->id,
                                    'completed_date' => null,
                                    'completed_by' => null,
                                    'flag'=>1
                                ]);
                                $patientWiseServiceRequest = [
                                    'patient_id' => $update,
                                    'service_id' => $medicalId,
                                    'patient_service_request_id' => $patientServiceLastId,
                                    
                                ];
                                $this->patientWiseServicesRequests->save($patientWiseServiceRequest);
                            }
                        }
                        $final_array = array(
                            'patient_id'=>$update,
                            'document_name'=>$getDetails->medical_name,
                            'hha_medical_doc_id'=>$getDetails->medical_id
                        );
                        $this->documentPatientService->save($final_array);
                }
                $this->hhaOtherComplianceService->update(array('patient_id' => $update), array('id' => $val));
            }
            return response()->json(['error_msg' => "Appointment successfully added", 'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
        }
    }

    public function getOtherCompliancebyCaregiverId(Request $request){
        $query = HHACaregiversHelper::GetCaregiverComplianceItemDueNew($request->id,$request->agency_fk,0);
        return response()->json(['error_msg' => "", 'data' => $query], 200);
    }

    public function exportCsv(Request $request){
        $data['agency_list'] = Agency::getHHAAgencyList();
        $data['agency_fk'] = $agency_fk = $request->input('agency_fk');
        $data['fname'] = $fname = $request->input('fname');
        $data['code'] = $code = $request->input('code');
        $data['caregiver_phone'] = $caregiver_phone = $request->input('caregiver_phone');
        $data['hha_code'] = $hha_code = $request->input('hha_code');
        $data['medical_name'] = $medical_name = $request->input('medical_name');
        $data['due_date'] = $due_date = $request->input('due_date');
        $data['status'] = $status = $request->input('status');
        $data['office_id'] = $office_id = $request->input('office_id');


        $query = $this->hhaOtherComplianceService->hhaAppoitnmentList($agency_fk, $fname, $code, $caregiver_phone, $hha_code, $medical_name, $due_date, $status,$office_id,'export');
        $filename = 'appointment' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        $columns = array( 'Agency Name','Office Name', 'Caregiver Full Name', 'Caregiver Code', 'Caregiver Phone', 'Medical Name', 'Due Date', 'DOB', 'Discipline', 'Team','Status');
        
		$callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
			fputcsv($file, $columns);
            
            foreach ($query as $row) {
                $due_date = "";
                if($row->due_date !="0000-00-00 00:00:00"){
                    $due_date = date('m/d/Y',strtotime($row->due_date));
                }
                fputcsv($file, array($row->agency_name,$row->office_name, $row->caregiver_first_name.' '.$row->caregiver_last_name, $row->office_code .' - '.$row->caregiver_code, $row->caregiver_phone,$row->medical_name,$due_date, ($row->dob=="")?"NA": date('m/d/Y',strtotime($row->dob)), $row->EmploymentTypesDiscipline, ($row->TeamName  !="")?$row->TeamName:"NA", ($row->patient_id !="")?"Added":"Pending"));
              
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function getHHAOtherComplienceData(Request $request)
	{
		$agencyId = $request->input('agencyId');
		$patientId = $request->input('patientId');

		$record = $this->patientService->getDetailByIdNew($patientId);
        $finalResponse = [];
        if(isset($record->id)){
            if ($record->link_hha_caregiver != '') {
                $cid= $record->link_hha_caregiver;
            }else{
                $getCaregiverDetails = HHAAppointment::getDetailsByAgencyIdAndId($agencyId,$record->hha_id);
                $cid= $getCaregiverDetails->caregiver_id??"";
            }

            $getDetails = HHACaregiversHelper::getCaregiverDetailsByAgencyId($cid,$agencyId);
            $getHHAOtherCompliance = HHACaregiversHelper::getCaregiverOtherCompliance($agencyId,$getDetails->officeId);
            $ids =[];
            if(!empty($getHHAOtherCompliance[0])){
                $ids = array_column($getHHAOtherCompliance, 'id');
            }
            
            $documents = HHACaregiversHelper::getCaregiverMedicalDetails($getDetails,$cid);
            $collection = collect($documents);
            $filtered = $collection
                ->whereIn('medical_id', $ids)
                ->whereNotIn('status', ["Completed"])
                ->values();
           $documentsResponse =$filtered->all();

          
          if(!empty($documentsResponse[0])){
            foreach($documentsResponse as $doc){
                $templ = [];

                $templ['id'] = $doc['medical_id'];
                $templ['medical_id'] = $doc['medical_id'];
                $templ['caregiver_medical_id'] = $doc['caregiver_medical_id'];
                $templ['medical_name'] = $doc['medical_name'];
                $templ['due_date'] = $doc['due_date'];
                $templ['status'] = $doc['status'];
                $finalResponse[] = $templ;
            }
          }
        }
		
		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $finalResponse, 'officeId' => $record->link_hha_caregiver]);
	}

    public function allOtherComplianceList(Request $request){
        $query =  $this->hhaCaregiverService->getDetailByIdWithAgencyFk($request->id,$request->agency_id);
        $data = [];
        if(isset($query->id)){
            $data = HHACaregiversHelper::getCaregiverOtherCompliance($request->agency_id,$query->officeId);
        }

        return response()->json(['data'=>$data],200);
    }
    
    public function updateHHAcomplienceDocument(Request $request)
	{
        $auth = auth()->user();
		$validationRequired = [];
		if(!empty($request->document_other_complience_type[0])){
			$validationRequired['document_other_complience_type']= "required";
            $validationRequired['hha_document_other_compliance_result'] = "required";
		}

		if(!empty($request->create_document_other_compliance_type[0])){
			$validationRequired['create_document_other_compliance_type']= "required";
            $validationRequired['hha_create_other_compliance_result'] = "required";
		}
        $validationRequired['agencyId']= "required";
        $validationRequired['document_type']= "required";
        $validationRequired['completed_date']= "required";
        $validationRequired['record-id']= "required";
        $validationRequired['other_complience_due_date']= "required";
       
		$validator = Validator::make($request->all(),$validationRequired);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}
		
		$agencyID = $request->input('agencyId');
		$getDetails = $this->documentPatientService->getDetailsById($request->input('id'));

		$record = $this->patientService->getDetailByIdNew($getDetails->patient_id);

		$caregiverId = '';
		if ($record->link_hha_caregiver != '') {
			$caregiverId = $record->link_hha_caregiver;
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyID)->where('id', $record->hha_id)->first();

			if (!$getCaregiverDetails) {

				return response()->json(['error_msg' => "Caregiver details not found in HHX", 'status' => 1, 'data' => array()], 400);
			} else {
				$caregiverId = $getCaregiverDetails->caregiver_id;
			}
		}

        $extension = '';
		if (isset($getDetails->attachment)) {
			$explode = explode('.', $getDetails->attachment);
			$extension = $explode[1];

		}

		$fileName = $getDetails->attachment;
		$fileName = str_replace("patientdocument/", "", $fileName);

		$image = "patientdocument/" . $fileName;

		$file = Storage::disk('s3')->get($image);
        $ipaddress = Utility::getIP();
        $commonResponse = [];
        if(!empty($request->document_other_complience_type[0])){
            foreach($request->document_other_complience_type  as $complianceId){

                $hhaOtherComplianceResponse = [
                    'agency_id'=>$request->agencyId,
                    'caregiver_id'=>$caregiverId,
                    'caregiver_other_compliance_id'=>$request['caregiver_other_compliance_id_'.$complianceId],
                    'compliance_id'=>$complianceId,
                    'result'=>request('hha_document_other_compliance_result')[$complianceId],
                    'due_date'=>Utility::convertYMD($request->other_complience_due_date),
                    'completed_date'=>Utility::convertYMD($request->completed_date),
                    'extension'=>$extension,
                    'medical_name'=>$fileName,
                    'file'=>$file
                ];
                $result = HHACaregiversHelper::updateCaregiverOtherCompliance($hhaOtherComplianceResponse);

                if(isset($request->auto_update_next_due_date) && $request->auto_update_next_due_date ==1){
                    HHACaregiversHelper::createCaregiverOtherCompliance($request->agencyId, $caregiverId, $request['caregiver_other_compliance_id_'.$complianceId],'','',date('Y-m-d', strtotime('+1 year', strtotime($request->completed_date))));
                }
            }

            if (isset($result['status']) && $result['status'] !== 1) {
                return response()->json(['error_msg' => $result['message'] . ' ', 'status' => 0, 'data' => array()], 500);
            }else{
                if(isset($result['data'])){
                    $commonResponse[] = $result['data'];
                    $data = $request->except('_token');

                    $hhaLogData = [
                        'patient_id'=>$getDetails->patient_id,
                        'hha_patient_id'=>$caregiverId,
                        'type'=>$record->type,
                        'hha_module_type'=>'Other Compliance',
                        'send_response'=>serialize($data),
                        'return_response'=>serialize($commonResponse),
                        'ip_address' => $ipaddress,
                        'action'=>'Update',
                        'document_id'=>$request->input('id')
                    ];
                    $this->hhaLogService->save($hhaLogData);

                    if(isset($request->auto_update_next_due_date) && $request->auto_update_next_due_date ==1){
                        $hhaLogData = [
                            'patient_id'=>$getDetails->patient_id,
                            'hha_patient_id'=>$caregiverId,
                            'type'=>$record->type,
                            'hha_module_type'=>'Other Compliance With Auto Update',
                            'send_response'=>serialize($data),
                            'return_response'=>serialize($commonResponse),
                            'ip_address' => $ipaddress,
                            'action'=>'Add',
                            'document_id'=>$request->input('id')
                        ];
                        $this->hhaLogService->save($hhaLogData);
                    }
                }
            }
        }
		
        /*********************Create a Other Compliance Module */
        if(!empty($request->create_document_other_compliance_type[0])){
            $flag = 0;
            foreach($request->create_document_other_compliance_type  as $complianceId){
                $result = HHACaregiversHelper::createCaregiverOtherCompliance($request->agencyId, $caregiverId, $complianceId, request('hha_create_other_compliance_result')[$complianceId], Utility::convertYMD($request->completed_date),$request->other_complience_due_date);

                if (isset($result['status']) &&  $result['status'] !== 1) {
                    return response()->json(['error_msg' => $result['message']. ' ', 'status' => 0, 'data' => array()], 500);
                }else{
                    if(isset($result['data'])){
                        $commonResponse[] = $result['data'];
                        $flag = 1;
                    }
                    
                }
            }

            if($flag ==1){
                 $data = $request->except('_token');
                $hhaLogData = [
                    'patient_id'=>$getDetails->patient_id,
                    'hha_patient_id'=>$caregiverId,
                    'type'=>$record->type,
                    'hha_module_type'=>'Other Compliance',
                    'send_response'=>serialize($data),
                    'return_response'=>serialize($commonResponse),
                    'ip_address' => $ipaddress,
                    'action'=>'Add',
                    'document_id'=>$request->input('id')
                ];
                $this->hhaLogService->save($hhaLogData);
            }
        }

        if ($request->input('agencyId') != 106) {

			$query = HHACaregiversHelper::getSendHHADocument($request->input('agencyId'), $getDetails->document_name, $extension, $request->input('document_type'), $caregiverId, $file, $request->input('id'));
		
        }
		if ($query) {
            $docus = $request->except('_token');
			$docus['document_name'] = $getDetails->document_name;
			$docus['attachment'] = $getDetails->attachment;
			$hhaLogData = [
				'patient_id'=>$getDetails->patient_id,
				'document_id'=>$getDetails->id??"",
				'hha_patient_id'=>$caregiverId,
				'type'=>$record->type,
				'hha_module_type'=>'Other Compliance',
				'send_response'=>serialize($docus),
				
				'action'=>'Document Add',
				'ip_address' => $ipaddress,
			];
			$this->hhaLogService->save($hhaLogData);

			$insertLog = [
				'type' => 'Update to HHX Other Compliance',
				'link' => url('/hha/hha-other-compliance/update-complience-document'),
				'module' => 'Patient Appointment',
				'object_id' =>$getDetails->patient_id,
				'message' => $auth->first_name . ' ' . $auth->last_name . ' has Update to HHX Other Compliance',
				'new_response' => serialize($data),
			
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			$this->documentPatientService->update(array('uploaded_complience_hha' => 1), array('id' => $request->input('id')));
			return response()->json(['error_msg' => "Document successfully updated", 'status' => 1, 'data' => array()], 201);
		}
		return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
	}

    public function getCompienceMedicalResults(Request $request)
	{
		$agencyId = $request->input('agencyId')??$request->input('agency_id');
		$id = $request->input('id');
		$medicaid_id = $request->input('medicaid_id');
	
		$record = $this->patientService->getDetailByIdNew($id);

		$officeID = 0;
		if ($record->link_hha_caregiver != '') {
			$caregiver = HHACaregivers::where("caregiver_id", $record->link_hha_caregiver)->where('agency_fk', $agencyId)->first();
			$officeID = $caregiver->officeId;
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyId)->where('id', $record->hha_id)->first();
			$officeID = $getCaregiverDetails->office_id;
		}

		$query = HHACaregiversHelper::getCaregiverOtherComplienceMedicalResults($agencyId, $medicaid_id, $officeID);
		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $query, "office" => $officeID]);
	}

    public function getHHAOtherComplience(Request $request)
	{
		$agencyId = $request->input('agencyId');
		$patientId = $request->input('patientId');

		$record = $this->patientService->getDetailByIdNew($patientId);

		$documents = [];

		if ($record->link_hha_caregiver != '') {
			$caregiver = HHACaregivers::select('officeId', 'agency_fk')->where("caregiver_id", $record->link_hha_caregiver)->where('agency_fk', $agencyId)->first();

			$documents = HHACaregiversHelper::getCaregiverOtherCompliance($agencyId, $caregiver->officeId);
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyId)->where('id', $record->hha_id)->first();
			$documents = HHACaregiversHelper::getCaregiverOtherCompliance($agencyId, $getCaregiverDetails->office_id);
		}
		
		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $documents, 'officeId' => $record->link_hha_caregiver]);
	}

    public function getMedicalResultByCaregiverId(Request $request)
	{
		$agencyId = $request->input('agencyId')??$request->input('agency_id');
		$id = $request->input('id');
		$medicaid_id = $request->input('medicaid_id');
	
        $caregiver = HHACaregivers::where("caregiver_id", $id)->where('agency_fk', $agencyId)->first();
        $officeID = $caregiver->officeId;

		$query = HHACaregiversHelper::getCaregiverOtherComplienceMedicalResults($agencyId, $medicaid_id, $officeID);
		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $query, "office" => $officeID]);
	}

    public function saveOtherMedicalData(Request $request){
        $this->validateRequest($request,'add');
        $agencyID = $request->agency_id;

        $commonResponse = $this->handleCompliance($request, $agencyID);
        $originalName = $this->handleFileUpload($request,$agencyID);

        $this->saveLog($request, $originalName,'Add');
        
        return response()->json([
            'status' => "1",
            'error_msg' => "Success",
            'data' => $commonResponse,
            "caregiver_id" => $request->caregiver_id
        ]);
    }

    public function updateOtherMedicalData(Request $request){
        $this->validateRequest($request,'edit');
       
        $fileData = $this->handleEditFileUpload($request,'edit_document_upload');
       
        $response = $this->updateOtherCompliance($request,$fileData);
    
        if(isset($response['status']) && $response['status'] ==1){
            $this->handleAutoDueDate($request,$request->agency_id,$request->medical_id);
            $this->saveLog($request,$fileData['fileName'],'Update');
            $this->sendHHADocumentOtherCompliance($request,$request->agency_id,$fileData);
            
            return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $response['data'], "caregiver_id" => $request->caregiver_id]);
        }else{
            $messages = "Sorry, something went wrong. Please try again.";
            if(isset($response['status']) && $response['status'] ==0 && $response['message'] !=""){
                $messages = $response['message'];
            }
        }

        return response()->json(['error_msg' =>$messages, 'data' => array()], 500);
    }

    private function validateRequest($request,$validationType){
        $validationRequired = [];
        if($validationType =='add'){
            if(!empty($request->create_document_other_compliance_type[0])){
                $validationRequired['created_medical_id']= "required";
            }
            $validationRequired['agency_id']= "required";
            $validationRequired['create_view_document_type']= "required";
            $validationRequired['caregiver_id']= "required";
            $validationRequired['due_date']= "required";
            $messages = [
                'created_medical_id.required' => 'Please select at least one Medical.',
                'agency_id.required' => 'Agency is required.',
                'create_view_document_type.required' => 'Document type is required.',
                'caregiver_id.required' => 'Caregiver is required.',
                'due_date.required' => 'Due Date is required.',
            ];
        }else{
            $validationRequired = [
                'edit_caregiver_medical_id' => 'required',
                'agency_id' => 'required',
                'caregiver_id' => 'required',
                'medical_id' => 'required',
                'due_date' => 'required',
                'date_perform' => 'required',
                'edit_document_type' => 'required',
                'edit_result' => 'required'
            ];

            $messages = [
                'edit_caregiver_medical_id.required' => 'Medical is required.',
                'medical_id.required' => 'Compliance Medical ID is required.',
                'due_date.required' => 'Due date is required.',
                'date_perform.required' => 'Date performed is required.',
                'edit_document_type.required' => 'Document type is required.',
                'edit_result.required' => 'Result is required.',
            ];
        }
        
        $validator = Validator::make($request->all(),$validationRequired, $messages);
		if ($validator->fails()) {
			abort(response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422));
		}
    }

    private function handleCompliance($request,$agencyID){
        $commonResponse = [];
        if (empty($request->created_medical_id[0])) {
            return $commonResponse;
        }
        /*********************Create a Other Compliance Module */
        if(!empty($request->created_medical_id[0])){
            foreach($request->created_medical_id  as $complianceId){
                $result = HHACaregiversHelper::createCaregiverOtherCompliance($agencyID, $request->caregiver_id, $complianceId, request('hha_create_other_compliance_result')[$complianceId], Utility::convertYMD($request->date_performed),$request->due_date);

                if (isset($result['status']) &&  $result['status'] !== 1) {
                    return response()->json(['error_msg' => $result['message']. ' ', 'status' => 0, 'data' => array()], 500);
                }else{
                    if(isset($result['data'])){
                        $commonResponse[] = $result['data'];
                    }
                }

                $this->handleAutoDueDate($request, $agencyID, $complianceId);
            }
        }

        return $commonResponse;
    }

    private function handleAutoDueDate($request, $agencyID, $complianceId)
    {
        
        if ($request->auto_update_next_due_date != 1) {
            return;
        }

        HHACaregiversHelper::createCaregiverOtherCompliance(
            $agencyID,
            $request->caregiver_id,
            $complianceId,
            '',
            '',
            date('Y-m-d', strtotime('+1 year', strtotime($request->date_performed)))
        );
    }

    private function handleFileUpload($request,$agencyID){
        if (!$request->hasFile('document_upload')) {
            return "";
        }

        $fileData = $this->handleEditFileUpload($request,'document_upload');
    
        $this->sendHHADocumentOtherCompliance($request,$agencyID,$fileData);
        return $fileData['fileName'];
    }

    private function saveLog($request,$originalName,$action){
        $ipaddress = Utility::getIP();
        $data = $request->except(['_token','document_upload','edit_document_upload']);
        $data['attachment_name'] = $originalName;
        $hhaLogData = [
            'hha_patient_id'=>$request->caregiver_id,
            'type'=>"Caregiver",
            'hha_module_type'=>'Other Compliance',
            'send_response'=>serialize($data),
            'ip_address' => $ipaddress,
            'action'=>$action
        ];
        $this->hhaLogService->save($hhaLogData);
    }

    private function handleEditFileUpload($request,$fileName){
        if (!$request->hasFile($fileName)) {
            return [
                'fileName' => '',
                'extension' => '',
                'fileContent' => ''
            ];
        }
    
        $file = $request->file($fileName);
    
        return [
            'fileName' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'extension' => $file->getClientOriginalExtension(),
            'fileContent' => file_get_contents($file->getRealPath())
        ];
    }

    private function updateOtherCompliance($request,$fileData){
        $hhaOtherComplianceResponse = [
            'agency_id'=>$request->agency_id,
            'caregiver_id'=>$request->caregiver_id,
            'caregiver_other_compliance_id'=>$request->medical_id,
            'compliance_id'=>$request->edit_caregiver_medical_id,
            'result'=>$request->edit_result,
            'due_date'=>Utility::convertYMD($request->due_date),
            'completed_date'=>Utility::convertYMD($request->date_perform),
            'extension'=>$fileData['extension'],
            'medical_name'=>$fileData['fileName'],
            'file'=>$fileData['fileContent'],
            'notes'=>$request->notes
        ];
        return HHACaregiversHelper::updateCaregiverOtherCompliance($hhaOtherComplianceResponse);
    }

    private function sendHHADocumentOtherCompliance($request,$agencyID,$fileData){
        return HHACaregiversHelper::getSendHHADocument($agencyID, $fileData['fileName'],$fileData['extension'], $request->input('create_view_document_type'), $request->caregiver_id, $fileData['fileContent']);
    }
}