<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\PatientService;
use Illuminate\Support\Facades\Validator;
use App\Agency;
use App\Model\Appointment;
use App\Model\Patient;
use App\Services\LogsService;
use App\Services\LocationScheduleService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Master;
use App\Services\ThirdPartyPatientLogService;
use App\Services\DocumentUploadService;
use App\Services\DocumentPatientService;
use Storage;
use App\Helpers\Utility;
class ArlaController extends BaseController{

    protected $thirdPartyPatientMaster,$patientService,$LocationScheduleService,$patientServiceRequest,$patientWiseServiceRequests,$thirdPartyPatientLogService,$patientServicesRequest,$documentUploadService,$documentPatientService = "";
    public function __construct(ThirdPartyPatientMasterService $thirdPartyPatientMaster,PatientService $patientService,LocationScheduleService $LocationScheduleService,PatientServicesRequest $patientServiceRequest,PatientWiseServicesRequests $patientWiseServiceRequests,ThirdPartyPatientLogService $thirdPartyPatientLogService,PatientServicesRequest $patientServicesRequest,DocumentUploadService $documentUploadService,DocumentPatientService $documentPatientService)
    { 
   
        $this->middleware('auth');
        $this->middleware('permission:arla-list|arla-appointment-add', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:arla-appointment-add', ['only' => ['addAppointmentForThirdParty']]);
        $this->middleware('permission:arla-export', ['only' => ['thirdPartyPatientExport']]);
       

        $this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
        $this->patientService = $patientService;
        $this->LocationScheduleService = $LocationScheduleService;
        $this->patientServiceRequest = $patientServiceRequest;
        $this->patientWiseServiceRequests = $patientWiseServiceRequests;
        $this->thirdPartyPatientLogService = $thirdPartyPatientLogService;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->documentUploadService = $documentUploadService;
        $this->documentPatientService = $documentPatientService;
    }

    public function index(Request $request){
        $auth = auth()->user();
        $data['agencyList'] = Agency::getAgencyListByAgencyToken();
        return view('arla_appointment.list',$data);
    }

    public function ajaxList(Request $request){
        $data['searchData'] = $request->all();
    
        $patient_status_list= [];
        $data  = $request->all();
        $data['module_type'] = "arla";
        $query = $this->thirdPartyPatientMaster->getPatientList($data);
        
        if(!empty($query[0])){
            foreach($query as $val){
                $getDocumentAttach = $this->documentPatientService->getDocumentAttach($val->patient_id,$val->requested_service_id);
                $documentName="";
                $documentCompletedDate="";
                $doc_id = "";
                if(isset($getDocumentAttach->attachment)){
                    $documentName=$getDocumentAttach->document_name;
                    $documentCompletedDate=date('m/d/Y',strtotime($getDocumentAttach->document_completed_date));
                    $doc_id = $getDocumentAttach->id;
                }

                $val->documentName = $documentName;
                $val->documentCompletedDate = $documentCompletedDate;
                $val->doc_id = $doc_id;
                $explodeService = explode(',',$val->service_id);

                $serviceArray = [];
                foreach($explodeService as $sd){
                    $srv = Master::getDetailsById($sd);
                    if(isset($srv->name)){
                        $serviceArray[] = $srv->name;
                    }
                  
                }
                $val->serviceName = implode(',',$serviceArray);
                $statusDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($val->patient_id);

                $val->status = isset($statusDetails->status)?$statusDetails->status:$val->status;

                if(in_array($val->status,$patient_status_list)){

                }else{
                    $patient_status_list[] = $val->status;
                }
            }
        }
        $data['query'] = $query;
        $data['patient_status_list'] = $patient_status_list;
        return view('third_party_patient.ajax_list',$data);
    }

    public function addAppointmentForThirdParty(Request $request){
        $validator = Validator::make($request->all(), [
            'appointment_ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }else{

            $insertId = 0;
            if(!empty($request->appointment_ids[0])){
                foreach($request->appointment_ids as $val){
                    $query = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($val);
                    $final = [];
                    
                    $data = $query->toArray();

                    foreach($data as $key=>$vals){
                        if($key !='id' && $key !="patient_id"){
                            $final[$key] = $vals;
                        }
                    }
                    $saveDetails = new Patient($final);
                    $saveDetails->save();
                    $insertId = $saveDetails->id;
                    Patient::where('id',$insertId)->update(array('link_third_party'=>$val));
                    // $ipaddress = request()->getClientIp();
                    $ipaddress = Utility::getIP();
                    $insertLog = [
                        'type' => 'Add Third Party Appointment',
                        'link' => url('/third-party-patient/add-appointment-third-patient'),
                        'module' => 'Patient Appointment',
                        'object_id' => $insertId,
                        'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has added Appointment',
                        'new_response' => serialize($data),
                        'ip' => $ipaddress,
                    ];
                    LogsService::save($insertLog);
                  
                    /*******************Save Service for Patient Service Requested */
                    $service = explode(',',$query->service_id);
                    $patientServiceLastId="";
                    if(!empty($service[0])){
                        $patientServiceLastId = $this->patientServiceRequest->save([
                            'patient_id' => $insertId,
                            'from_api'=>1
                        ]);
                        foreach ($service as $serviceId) {
                            $patientWiseServiceRequest = [
                                'patient_id' => $insertId, 
                                'service_id' => $serviceId, 
                                'patient_service_request_id' => $patientServiceLastId,
                            ];
                            
                            $this->patientWiseServiceRequests->save($patientWiseServiceRequest);
                        }
                        
                        $this->thirdPartyPatientMaster->update(array('patient_id'=>$insertId,'requested_service_id'=>$patientServiceLastId),array('id'=>$val));
                 
                    }
                }
            }

            if($insertId){
                return response()->json(['error_msg' => "Appointment successfully added", 'data' => array()], 200);
            }else{
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
            }
        }
    }

    public function existingRecord(Request $request){
        $recordDetails = $this->thirdPartyPatientMaster->getPatientDetails($request->id,$request->agency_fk);
        $getExistingData = $this->patientService->getDetailsByThirdParty($recordDetails->toArray());
        return response()->json(['error_msg' => "Appointment successfully added", 'data' => $getExistingData], 200);
    }

    public function linkThirdPartyAppointment(Request $request){
       $update =  $this->thirdPartyPatientMaster->update(array('patient_id'=>$request->id),array('id'=>$request->third_party_id));
       $oldData = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->id);
        
       if(isset($oldData->id)){
            if ($oldData->type == 'Caregiver') {
                $getAppointSchedule = $this->LocationScheduleService->getDetailbyId($oldData->appoinment_time_id);
                $time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";
            }else{
                    $time=date('H:i:s',strtotime($oldData->appointment_date));
            }

            if($oldData->status =='completed'){
                    $patientData =[
                        'location_id'=>"",
                        'appointment_date' => null,
                        'appointment_added_by' => null, 
                        'status'=>'Pending',
                        'appointment_added_created_date' => null,
                        'appoinment_time_id' =>null,
                        'appointment_mode' => '',
                        'link_third_party'=>$request->third_party_id
                    ];
                    $update = $this->patientService->update($patientData,array('id'=>$request->id));
                    $addAppintment = ["patient_id" =>$request->id, "location_id" => $oldData->location_id, "service_id" => $oldData->service_id, "appointment_date" => $oldData->appointment_date, "appointment_time" => $time, "created_at" => date('Y-m-d H:i:s')];
                    Appointment::create($addAppintment);

            }else{
                    $update = $this->patientService->update(array('status'=>'Pending','link_third_party'=>$request->third_party_id),array('id'=>$request->id));
            }
    
            $data = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->id); 
       
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Link Third Party Appointment',
                'link' => url('/link-third-party-appointment'),
                'module' => 'Patient Appointment',
                'object_id' => $request->id,
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has added Appointment',
                'old_response' => serialize(array($oldData)),
                'new_response' => serialize(array($data)),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            $query = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->third_party_id);

            /*******************Save Service for Patient Service Requested */
            $service = explode(',',$query->service_id);
            $patientServiceLastId="";
            if(!empty($service[0])){
                $patientServiceLastId = $this->patientServiceRequest->save([
                    'patient_id' => $request->id,
                    'from_api'=>1
                ]);
                foreach ($service as $serviceId) {
                    $patientWiseServiceRequest = [
                        'patient_id' => $request->id, 
                        'service_id' => $serviceId, 
                        'patient_service_request_id' => $patientServiceLastId,
                    ];
                    
                    $this->patientWiseServiceRequests->save($patientWiseServiceRequest);
                }
            }

            $this->thirdPartyPatientMaster->update(array('requested_service_id'=>$patientServiceLastId),array('id'=>$request->third_party_id));
        
            return response()->json(['error_msg' => "Appointment successfully linked", 'data' => array()], 200);
       }else{
        return response()->json(['error_msg' => "Appointment not available", 'data' => array()], 500);

       }
       
    }

    public function searchThirdParty(Request $request){
        $query = $this->thirdPartyPatientMaster->searchData($request->all());
        return json_encode($query);
    }

    public function searchPatient(Request $request){
        $response = $this->patientService->searchPatients($request->all());
        return json_encode($response);
    }

    public function updateSearchThirdPartyLink(Request $request){
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'third_party_id' => 'required',
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }else{
            $oldResponse = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->third_party_id);
            $update =  $this->thirdPartyPatientMaster->update(array('patient_id'=>$request->id),array('id'=>$request->third_party_id));
            $update =  $this->patientService->update(array('link_third_party'=>$request->third_party_id),array('id'=>$request->id));
            $newResponse = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->third_party_id);
            $logData = [
                'patient_id'=>$request->third_party_id,
                'type'=>"Link Patient",
                'old_response'=>serialize($oldResponse->toArray()),
                'new_response'=>serialize($newResponse->toArray())
            ];
            $this->thirdPartyPatientLogService->save($logData);
            return response()->json(['error_msg' => "Patient successfully linked", 'data' => array()], 200);
        }
    }

    public function linkPatientServices(Request $request){
        $services = $this->patientServicesRequest->getPatientService($request->patientId);

		$finalArray = [];
		
		if(!empty($services[0])){
			foreach($services as $val){
				$servicesArray = [];
				$servicesArray[$val->id] = [];
				if(!empty($val->patientServiceRequestRelationShip[0])){
					

					foreach($val->patientServiceRequestRelationShip as $sr){
						if(isset($temp[$sr->patient_service_request_id])){
							$temp[$sr->patient_service_request_id][] = $sr->services[0]->name;

						}else{
							$temp[$sr->patient_service_request_id] = [];
							$temp[$sr->patient_service_request_id][] = $sr->services[0]->name;

						}
						
						$servicesArray[$val->id] = $temp[$sr->patient_service_request_id];
					}
				}

				$implode = implode(',',$servicesArray[$val->id]);
				$temp = [];
				$temp['id'] = $val->id;
				$temp['name'] = date('m/d/Y',strtotime($val->created_at)).' ( '.$implode.')';
				$finalArray[] = $temp;

			}

		}
        
        $static['id'] = "-1";
        $static['name'] = "Create A New";
        $staticArray[] = $static;
        $final = array_merge($finalArray,$staticArray);
      
       return response()->json(['error_msg' => "Patient successfully linked", 'data' => $final], 200);
    }

    public function updateLinkPatientService(Request $request){
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'third_party_id' => 'required',
            'patient_id' => 'required',
            'request_service_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }else{
            $oldResponse = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->third_party_id);
            if($request->request_service_id > 0){
                $patientServiceLastId =  $this->thirdPartyPatientMaster->update(array('requested_service_id'=>$request->request_service_id),array('id'=>$request->third_party_id));
            }else{

                $responseData = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->third_party_id);
                $patientServiceLastId = $this->patientServicesRequest->save(['patient_id' => $request->input('patient_id'),'from_api'=>1]);
                
                $addServiceIds = explode(',',$responseData->service_id);
                if (is_array($addServiceIds)) {
                    foreach ($addServiceIds as $serviceId) {
                        $patientWiseServiceRequest = [
                            'patient_id' => $request->input('patient_id'), 
                            'service_id' => $serviceId, 
                            'patient_service_request_id' => $patientServiceLastId,
                        ];
                        
                        $this->patientWiseServiceRequests->save($patientWiseServiceRequest);
                    }
                }

                $this->thirdPartyPatientMaster->update(array('requested_service_id'=>$patientServiceLastId),array('id'=>$request->third_party_id));
            }
            $newResponse = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->third_party_id);
            if($patientServiceLastId){

                $logData = [
                    'patient_id'=>$request->third_party_id,
                    'type'=>"Link Services",
                    'old_response'=>serialize($oldResponse->toArray()),
                    'new_response'=>serialize($newResponse->toArray())
                ];
                $this->thirdPartyPatientLogService->save($logData);
                return response()->json(['success' => true, 'data' => [],'error_msg'=>'Patient Service successfully requested.'], 200);
            }else{
                return response()->json(['success' => false, 'data' => [], 'error_msg' => 'Sorry, something went wrong. Please try again.', 500]);
            }
        }
    }

    public function patientDetailsGet(Request $request){

        $patientDetail = $this->thirdPartyPatientMaster->getPatientDetails($request->id,$request->agencyId);
        $getServices = Master::geServiceName($patientDetail->service_id);

        $serviceArray =[];
        foreach($getServices as $val){
            $serviceArray[] = $val->name;
        }
        $patientDetail->service_name = "";
        if(!empty($serviceArray[0])){
            $patientDetail->service_name = implode(',',$serviceArray);
        }
        
        return response()->json(['error_msg' => "Appointment successfully added", 'data' => $patientDetail], 200);
    }   
    
    public function linkVisitingAidService(Request $request){
        $validator = Validator::make($request->all(), [
            'third_party_id' => 'required',
            'serviceId' => 'required',
           
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }else{
            $oldResponse = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->third_party_id);
           
            $this->thirdPartyPatientMaster->update(array('requested_service_id'=>$request->serviceId),array('id'=>$request->third_party_id));
            $newResponse = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->third_party_id);
            $this->patientServiceRequest->update(array('from_api'=>1),array('id'=>$request->serviceId));
            $logData = [
                'patient_id'=>$request->third_party_id,
                'type'=>"Link Services",
                'old_response'=> $oldResponse !="" ? serialize($oldResponse->toArray()):"",
                'new_response'=> $newResponse !="" ? serialize($newResponse->toArray()):""
            ];
            $this->thirdPartyPatientLogService->save($logData);

            return response()->json(['error_msg' => "Service Requested successfully linked", 'data' => array()], 200);
        }
    }

    public function thirdPartyPatientExport(Request $request){
		$data['searchData'] = $request->all();
		$data = $request->all();
        $data['module_type'] = "arla";
        $patient_status_list= [];

        $query = $this->thirdPartyPatientMaster->getPatientList($data,'export');

        $filename = 'Third-Party-Patient' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        $columns = array('No', 'Agency Name', 'Patient ID', 'Requested ID', 'Name', 'Mobile', 'DOB', 'Services', 'Type', 'Discipline', 'Gender', 'Service Status',  'Portal', 'API Name', 'Due Date', "Created Date");

        $newass  = array();
        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $cnt = 1;
            foreach($query as $val){
            
                $dob = '';
                if(isset($val->dob)){
                $dob = date('m/d/Y', strtotime($val->dob));
                }

                $createdDate = '';
                if(isset($val->dob)){
                $createdDate = date('m/d/Y h:i A', strtotime($val->created_date));
                }
                $apiName = '';
                if(isset($val->agencyGenerateDetails->notes)){
                $apiName = $val->agencyGenerateDetails->notes;
                }
                                        
                $SName = "";
                if(isset($val->agencyGenerateDetails) && $val->agencyGenerateDetails->notes != ""){
                    $SName =$val->agencyGenerateDetails->notes;
                }

                $explodeService = explode(',',$val->service_id);

                $serviceArray = [];
                foreach($explodeService as $sd){
                    $srv = Master::getDetailsById($sd);
                    if(isset($srv->name)){
                        $serviceArray[] = $srv->name;
                    }
                  
                }
                $serviceName = implode(',',$serviceArray);
                $srvStatus="";
                 if(isset($val->serviceDetails) && $val->serviceDetails !=""){
                    $srvStatus =$val->serviceDetails->status;
                 }
                fputcsv($file, array($val->id,$val->agencyDetails->agency_name,$val->patient_id,$val->requested_service_id,$val->first_name . ' ' . $val->last_name, $val->mobile,$dob,$serviceName,$val->type,$val->diciplin,$val->gender,$srvStatus,$val->platform_type,$SName,date('m/d/Y',strtotime($val->due_date)),$createdDate));

            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);    
    }

    function showDocumentListLog(Request $request){
        $getPatientDetails = $this->thirdPartyPatientMaster->getPatientDetails($request->id,$request->agencyId);
        $documents=[];
        if(isset($getPatientDetails->patient_id) && $getPatientDetails->patient_id !=""){
            $getServices = $this->documentUploadService->getDocumentListByPatientId($getPatientDetails->patient_id);
            if(!empty($getServices[0])){
                foreach($getServices as $val){
                    $documentList = $this->documentPatientService->getDetailsByIdAllWithRequestedServiceIdOnlyBackend($val->document_id,$getPatientDetails->requested_service_id);
                    foreach ($documentList as $key => $value) {
        
                        $documents[]=$value;
                    }
                }
            }
        }
        $data['documents'] = $documents;
        return response()->json(['success'=>true,'data'=>$data]);
        
    }

    public function uploadDocumentThirdParty(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required',
            'id' => 'required',
            'document_id'=>'required',
            
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }else{
          
            $data['document_completed_date'] = date('Y-m-d',strtotime($request->document_completed_date));
            $data['request_service_id'] = $request->requestedServiceId;
          
            $update = $this->documentPatientService->update($data,array('id'=>$request->document_id));

            if($update){
                if(!empty($request->service_id[0])){
					foreach($request->service_id as $serviceId){
						$data =[
						'patient_id'=>$request->input('patient_id'),
						'document_id'=>$request->document_id,
						'service_id'=>$serviceId,
						];

						$this->documentUploadService->save($data);
					}
				}

				// $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'update Document From Third Party Patient',
					'link' =>  url('/patient/view/') . $request->input('patient_id'),
					'module' => 'Patient Appointment',
					'object_id' => $request->input('patient_id'),
					'message' => $user->first_name . ' ' . $user->last_name . ' has Add Document From Appointment',
					'new_response' => serialize($data),
					'ip' => $ipaddress,
				];
				
				LogsService::save($insertLog);
                return response()->json(['status'=>true,'error_msg'=>'Document  successfully updated'],200);
            }else{
                return response()->json(['status'=>true,'error_msg'=>'Sorry, something went wrong. Please try again'],500);
            }

        }
    }

    
    function getDocumentData(Request $request){
        $response = $this->documentPatientService->getDocumentByPatientId($request->id);        
   
        return response()->json(['success' => true, 'data' => $response]);
    }

    function getDocumentIdData(Request $request){
        $response = $this->documentPatientService->getDetailsByIdAll($request->doc_id); 
        
        $serviceArray = [];
        $services = $this->documentUploadService->getDocumentListByDocumentId($request->doc_id);
        $serviceArrayNe = [];

        if(!empty($services[0])){
          
            foreach($services as $ser){
                if(isset($ser->masterDetails) && $ser->masterDetails !=""){
                    $serviceArray[] = $ser->masterDetails;
                }
            }
        }else{
            if($request->document_type =='Caregiver'){
                
                $query = Master::select('id','name')->where('types','Caregiver')->get();
              
            }else{
                
                $query = Master::select('id','name')->where('types','Patient')->get();
            }
            $serviceArrayNe = $query;
        }

        $response['services'] = $serviceArray;
        $response['new_servies'] = $serviceArrayNe;
        return response()->json(['success' => true, 'data' => $response]);
    }
}