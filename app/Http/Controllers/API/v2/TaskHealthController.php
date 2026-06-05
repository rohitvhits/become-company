<?php

namespace App\Http\Controllers\API\V2;

use App\Agency;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Model\Patient;
use Illuminate\Support\Facades\Validator;
use App\Helpers\GenerateAgencyTokenHelper;
use App\Services\PatientService;
use App\Services\DocumentPatientService;
use App\Master;
use App\Services\LocationMasterService;
use App\User;
use Illuminate\Support\Facades\Storage;
use App\Model\TokenwiseApiCall;
use App\Services\LogsService;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\DocumentUploadService;
use App\Services\PatientServicesRequest;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Mail;
use App\Services\PatientWiseServicesRequests;
use App\Model\PatientServiceRequest;
use App\Model\PatientWiseServiceRequest;
use App\Model\TaskHealthLog;
use Illuminate\Support\Facades\DB;
use App\Services\AgencyTaskHealthService;
use App\Model\TaskHealthMaster;
use App\Services\SendTaskHealthDocumentService;
use App\Services\HHAPOCTaskService;
use App\Helpers\TaskHealthApiHelper;
use App\Services\PocMatchedTaskService;
use App\Services\VisitTaskHealthService;
use App\Services\AgencyTaskHealthSettingService;

class TaskHealthController extends BaseController
{
	public $successStatus = 200;
	protected $patientService;
	protected $documentPatientService;
	protected $locationMasterService;
	protected $thirdPartyPatientMaster;
	protected $documentUploadService;
	protected $patientServicesRequest;
	protected $patientWiseServiceRequests;
	protected $agencyTaskHealthService;
	protected $sendTaskHealthDocumentService;
	protected $hhaPOCTaskService;
	protected $pocMatchedTaskService;
	protected $visitTaskHealthService;
	protected $agencyTaskHealthSettingService;

	public function __construct(PatientService $patientService, DocumentPatientService $documentPatientService,LocationMasterService $locationMasterService,ThirdPartyPatientMasterService $thirdPartyPatientMaster,DocumentUploadService $documentUploadService,PatientServicesRequest $patientServicesRequest,PatientWiseServicesRequests $patientWiseServiceRequests, AgencyTaskHealthService $agencyTaskHealthService, SendTaskHealthDocumentService $sendTaskHealthDocumentService,HHAPOCTaskService $hhaPOCTaskService,PocMatchedTaskService $pocMatchedTaskService,VisitTaskHealthService $visitTaskHealthService, AgencyTaskHealthSettingService $agencyTaskHealthSettingService)
	{

		$this->patientService = $patientService;
		$this->documentPatientService = $documentPatientService;
		$this->locationMasterService  = $locationMasterService ;
		$this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
		$this->documentUploadService = $documentUploadService;
		$this->patientServicesRequest = $patientServicesRequest;
		$this->patientWiseServiceRequests = $patientWiseServiceRequests;
		$this->agencyTaskHealthService = $agencyTaskHealthService;
		$this->sendTaskHealthDocumentService = $sendTaskHealthDocumentService;
		$this->hhaPOCTaskService = $hhaPOCTaskService;
		$this->pocMatchedTaskService = $pocMatchedTaskService;
		$this->visitTaskHealthService = $visitTaskHealthService;
		$this->agencyTaskHealthSettingService = $agencyTaskHealthSettingService;
	}

	function app_tarce($apiKey)
	{
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$page = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
		if (!empty($_SERVER['QUERY_STRING'])) {
			$page = $_SERVER['QUERY_STRING'];
		} else {
			$page = "";
		}
		if (!empty($_POST)) {
			$user_post_data = $_POST;
			$agency_id = $_POST['patient_basic_data']['agency_id']??'';
		} else {
			$user_post_data = $_GET;
			$agency_id = $_GET['agency_id']??'';
		}
		$user_post_data = json_encode($user_post_data);
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$remotehost = @getHostByAddr($ipaddress);
		$user_info = json_encode(array("Ip" => $ipaddress, "Page" => $page, "UserAgent" => $useragent, "RemoteHost" => $remotehost));
		$urlPath = parse_url($actual_link, PHP_URL_PATH);
		$endpoint = basename($urlPath); 
		$type = ucwords(str_replace('-', ' ', $endpoint));
	
		$user_track_data = array("url" => $actual_link,'type'=>$type, 'api_key' => $apiKey, 'ip' => $ipaddress,'response'=>$user_info,'created_date'=>date('Y-m-d H:i:s'),'data'=>$user_post_data,'agency_id' =>$agency_id);
		$saveLog = new TaskHealthLog($user_track_data);
		$saveLog->save();
	}

	public function saveTokenWiseApiCall($tokenId){
		$auth = auth()->user();
		$user_id ="";
		if ($auth) {
			$user_id = $auth->id;
		}
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
		$saveData = new TokenwiseApiCall(array('token_id'=>$tokenId,'api_call'=>$actual_link,'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$user_id));
		$saveData->save();
	}

	public function checkBlockIPAddress($blockIpAddress){
		$ip="";
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$explode = explode(',',$blockIpAddress);
		$ipaddress =$ip;

		$flag = 1;
		if(!empty($explode[0])){
			if(in_array($ipaddress,$explode)){
				$flag = 0;
			}
		}
		return $flag;
	}

	public function createNewPatient($data){
		$age = null;
		if (isset($data['dob']) && $data['dob'] != '') {
			$age = Utility::convertYMD($data['dob']);
		}
	
		$fuDate =null;
		if (isset($data['fu_date']) && $data['fu_date'] != '') {
			$fuDate = Utility::convertYMD($data['fu_date']);
		}
		$dueDate = null;
		if (isset($data['due_date']) && $data['due_date'] != '') {
			$dueDate = Utility::convertYMD($data['due_date']);
		}
		
		$serviceIds =  explode(',',$data['service_id']);
		$serviceIdArray = [];
		if(!empty($serviceIds[0])){
			foreach($serviceIds as $st){
				$details = Master::where('id',$st)->where('master_type_fk',11)->where('del_flag','N')->where(DB::raw('LOWER(types)'), strtolower($data['type']))->first();
				if(isset($details->id) && $details->id !=""){
					$serviceIdArray[] = $st;
				}
			}
		}
		if(count($serviceIdArray) == 0){
            return ['error_msg' => "Sorry, we could not locate the service you requested.", 'status' => 0, 'data' => array()] ;
		}
		
		$ssn ="";
		if(isset($data['ssn'])){
			$ssn = str_replace('-','',$data['ssn']);
		}
		$patientType ="";
		if(isset($data['type']) && $data['type'] !=""){
			$patientType = ucfirst(strtolower($data['type']));
		}

		$status= "Pending";
		if($patientType =='Patient'){
			$status = Utility::getStatusFromServiceId($serviceIds);
		}
		
		$dataArray = array(
			'first_name' => $data['first_name'],
			'middle_name' =>  $data['middle_name']??"",
			'last_name' =>  $data['last_name']??"",
			'full_name' => $data['first_name'].' '.$data['last_name'],
			'type' =>  $patientType,
			'dob' => $age,
			'fu_date' =>$fuDate,
			'due_date' => $dueDate,
			'phone' => isset($data['phone'])
				? (strlen($num = preg_replace('/[^0-9]/', '', $data['phone'])) == 11 && $num[0] == '1' ? substr($num, 1) : $num)
				: "",

			'mobile' => isset($data['mobile'])
				? (strlen($num = preg_replace('/[^0-9]/', '', $data['mobile'])) == 11 && $num[0] == '1' ? substr($num, 1) : $num)
				: "",
			'agency_id' =>$data['agency_id'],
			'gender' => $data['gender']??"",
			'remarks' => $data['message']??"",
			'service_id' => implode(',',$serviceIdArray),
			'patient_code' => $data['patient_code']??"",
			'diciplin' => $data['diciplin']??"",
			'language' => $data['language']??"",
			'address1' => $data['address1']??"",
			'address2' => $data['address2']??"",
			'state' => $data['state']??"",
			'city' => $data['city']??"",
			'zip_code' => $data['zipcode']??"",
			'county' => $data['country']??"",
			'payment_type' => $data['payment_type']??"",
			'platform_type' => $data['platform_type']??"",
			'platform_id' => $data['platform_id']??"",
			'created_date'=>date('Y-m-d H:i:s'),
			'partner_agency'=>$data['partner_agency']??"",
			'agency_token_id'=>$data['token_id']??"",
			'third_party_priority'=>$data['priority']??"",
			'cin' => $data['cin']??"",
			'ssn' => $ssn,
			'emergency_contact_name' => $data['emergency_contact_name']??"",
			'emergency_phone' => $data['emergency_phone']??"",
			'insurance_id' => isset($data['insurance_id'])?$data['insurance_id']:"",
			'insurance_name' =>isset($data['insurance_name'])?$data['insurance_name']:"",
			'created_by' => env('TASK_API_USER_ID'),
			'status'=>$status,
            'referral_type' => 'Task Health'
		);
		
		if(isset($data['platform_type']) && $data['platform_type'] !=""){
			if($data['platform_type'] =='VA'){
				$dataArray['referral_type'] = "Visiting Aid";
			}else{
				$dataArray['referral_type'] = $data['platform_type'];
			}
		}
		if(isset($data['insurance_name']) && $data['insurance_name'] =='other'){
			$dataArray['other_insurance_name'] = $data['other_insurance_name']??"";
		}

		if(isset($data['service_start_date']) && $data['service_start_date'] !=''){
			$dataArray['service_start_date'] = date('Y-m-d',strtotime($data['service_start_date']));
		}

		if(isset($data['third_party_callback_url'])){
			$dataArray['third_party_callback_url'] = $data['third_party_callback_url']??"";
		}

		if(isset($data['location_branch']) && $data['location_branch'] !=''){
			$dataArray['location_branch'] = $data['location_branch']??"";
		}
		$savePatient = new Patient($dataArray);
		$savePatient->save();
		$insert_ids = $savePatient->id;
		return $insert_ids;
	}

	public function saveTaskHealthAppointment(Request $request)
	{
		$header = $request->header('authorization');	
		$checkToken = GenerateAgencyTokenHelper::checkToken($header);
		if (empty($checkToken)) {
			return response()->json(['error_msg' => "Invalid token.", 'status' => 0, 'data' => array()], $this->successStatus);
			die();
		}

		$response = self::checkBlockIPAddress($checkToken->ip_block);
		if($response ==0){
			return response()->json(['error_msg' => "Your IP Address is Blocked.", 'status' => 0, 'data' => array()], $this->successStatus);
			die();
		}

		self::app_tarce($header);
		self::saveTokenWiseApiCall($checkToken->id);

		$sendResponseData1 = $request->all();
        $sendResponseData =$sendResponseData1['patient_basic_data'];
		$validator = Validator::make($sendResponseData, [
			'first_name' => 'required',
			'type' => 'required',
			'last_name' => 'required',
			'mobile' => 'required|numeric|digits_between:10,15',
			'service_id' => 'required',
			'dob' => 'required',
            'agency_id' => 'required|numeric',
            'gender' => 'required'
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
            $getAgencyExist = Agency::leftjoin('agency_task_health',function($join){
				$join->on('agency.id','=','agency_task_health.agency_id');
			})->select('agency.id')->where('agency.id',$sendResponseData['agency_id'])->where('agency_task_health.status','!=',0)->where('agency.delete_flag', 'N')->first();
            if(empty($getAgencyExist)){
                return response()->json(['error_msg' => "Agency not found. Please verify the agency details.", 'status' => 0, 'data' => array()], $this->successStatus);
			    die();
            }
			// get agency setting data
			$agencySettingData = $this->agencyTaskHealthSettingService->getByAgencyId($sendResponseData['agency_id']);
			if(isset($sendResponseData['gender']) && !empty($sendResponseData['gender'])){
				$sendResponseData['gender'] = $this->convertGender($sendResponseData['gender']);
			}
			$getExistingPatientDetails = $this->patientService->checkForExistingTaskHealthDataApi($sendResponseData,$sendResponseData['agency_id']);
			$patientId = "";
			$created_by = env('TASK_API_USER_ID');
			$task_health_link = "";
			if(isset($getExistingPatientDetails->id)){
				$patientId = $getExistingPatientDetails->id;
				$task_health_link = $getExistingPatientDetails->task_health_link;
				$flag = 0;
                $sendResponseData['fu_date'] = $sendResponseData['followup_date']??NULL;
				$sendResponseData['due_Date'] = $sendResponseData['due_date']??NULL;
				$sendResponseData['diciplin'] = $sendResponseData['discipline']??NULL;
				
				$emergency_contact_name="";
				$emergency_contact_number = "";
				if(isset($sendResponseData['patient_emergency_contacts']) && count($sendResponseData['patient_emergency_contacts']) >0){
					$emergency_contact_name =$sendResponseData['patient_emergency_contacts'][0]['emergency_contact_name'];
					$emergency_contact_number =$sendResponseData['patient_emergency_contacts'][0]['emergency_contact_number'];
				}
				$sendResponseData['emergency_contact_name'] =$emergency_contact_name;
				$sendResponseData['emergency_contact_number']=$emergency_contact_number;
			}else{
				$sendResponseData['fu_date'] = $sendResponseData['followup_date']??NULL;
				$sendResponseData['due_Date'] = $sendResponseData['due_date']??NULL;
				$sendResponseData['diciplin'] = $sendResponseData['discipline']??NULL;
				$emergency_contact_name="";
				$emergency_contact_number = "";
				if(isset($sendResponseData['patient_emergency_contacts']) && count($sendResponseData['patient_emergency_contacts']) >0){
					$emergency_contact_name =$sendResponseData['patient_emergency_contacts'][0]['emergency_contact_name'];
					$emergency_contact_number =$sendResponseData['patient_emergency_contacts'][0]['emergency_contact_number'];
				}
				$sendResponseData['emergency_contact_name'] =$emergency_contact_name;
				$sendResponseData['emergency_contact_number']=$emergency_contact_number;
				
				$allDataSaveAppointment = $sendResponseData;
				$allDataSaveAppointment['token_id'] = $checkToken->id;
				$allDataSaveAppointment['agency_id'] = $sendResponseData['agency_id'];
				$allDataSaveAppointment['third_party_callback_url'] = $request->callback;
				$patientId = $this->createNewPatient($allDataSaveAppointment);
                if(isset($patientId['status']) && $patientId['status'] == 0){
                    return response()->json(['error_msg' => $patientId['error_msg'], 'status' => 0, 'data' => array()], $this->successStatus);
			        die();
                }
				$flag = 1;
			}
    
			$age = null;
			if (isset($sendResponseData['dob']) && $sendResponseData['dob'] != '') {
				$age = Utility::convertYMD($sendResponseData['dob']);
			}
			$fuDate =null;
		
			if (isset($sendResponseData['fu_date']) && $sendResponseData['fu_date'] != '') {
				$fuDate = Utility::convertYMD($sendResponseData['fu_date']);
			}
			$dueDate = null;
			
			if (isset($sendResponseData['due_date']) && $sendResponseData['due_date'] != '') {
				$dueDate = Utility::convertYMD($sendResponseData['due_date']);
			}
			
			$service_start_date = null;
			
			if (isset($sendResponseData['service_start_date']) && $sendResponseData['service_start_date'] != '') {
				$service_start_date = date('Y-m-d', strtotime($sendResponseData['service_start_date']));
			}
		
			$serviceIds =  explode(',',$sendResponseData['service_id']);
			$serviceIdArray = [];
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $st){
					$details = Master::where('id',$st)->where('master_type_fk',11)->where('types',$sendResponseData['type'])->first();
					if(isset($details->id) && $details->id !=""){
						$serviceIdArray[] = $st;
					}
				}
			}
			
			$data = array(
				'patient_id' => $patientId,
				'first_name' => $sendResponseData['first_name'],
				'middle_name' =>$sendResponseData['middle_name']??'',
				'last_name' =>$sendResponseData['last_name'],
				'full_name'=>$sendResponseData['first_name'].' '.$sendResponseData['last_name'],
				'type' => $sendResponseData['type'],
				'dob' => $age,
				'fu_date' =>$fuDate,
				'due_date' => $dueDate,
				'phone' => isset($sendResponseData['phone']) ? str_replace(['(', ')', ' ', '-'], '', $sendResponseData['phone']) : "",
				'mobile' => isset($sendResponseData['mobile']) ? str_replace(['(', ')', ' ', '-'], '', $sendResponseData['mobile']) : "",
				'agency_id' =>$sendResponseData['agency_id'],
				'gender' =>$sendResponseData['gender'],
				'remarks' =>$sendResponseData['message']??'',
				'service_id' => implode(',',$serviceIdArray),
				'patient_code' => $sendResponseData['patient_code']??'',
				'diciplin' =>$sendResponseData['diciplin']??'',
				'language' =>$sendResponseData['language']??'',
				'address1' =>$sendResponseData['address1']??'',
				'address2' =>$sendResponseData['address2']??'',
				'state' =>$sendResponseData['state']??'',
				'city' =>$sendResponseData['city']??'',
				'zip_code' =>$sendResponseData['zipcode']??'',
				'county' =>$sendResponseData['country']??'',
				'payment_type' =>$sendResponseData['payment_type']??'',
				'platform_type' =>$sendResponseData['platform_type']??'',
				'platform_id' =>$sendResponseData['platform_id']??'',
				'created_date'=>date('Y-m-d H:i:s'),
				'partner_agency'=>$sendResponseData['partner_agency']??'',
				'agency_token_id'=>$checkToken->id,
				'third_party_priority'=>$sendResponseData['priority']??'',
				'cin' =>$sendResponseData['cin']??'',
				'ssn' => isset($sendResponseData['ssn']) ? str_replace('-','',$sendResponseData['ssn']) : '',
				'emergency_contact_name' => $sendResponseData['patient_emergency_contacts']['emergency_contact_name']??"",
				'emergency_phone' =>$sendResponseData['patient_emergency_contacts']['emergency_contact_number']??"",
				'insurance_id' =>$sendResponseData['insurance_id']??'',
				'insurance_name' =>$sendResponseData['insurance_name']??'',
				'created_by' => $created_by,
				'service_start_date' => $service_start_date,
                'referral_type' => 'Task Health'
			);

			if(isset($sendResponseData['insurance_name']) && $sendResponseData['insurance_name'] =='other'){
				$data['other_insurance_name'] = $sendResponseData['other_insurance_name'];
				
			}
			if(isset($request->callback)){
				$data['third_party_callback_url'] = $request->callback??"";
			}
			if(isset($sendResponseData['patient_id'])){
				$data['task_health_patient_id'] = $sendResponseData['patient_id']??"";
			}
			if(isset($sendResponseData['task_id'])){
				$data['task_id'] = $sendResponseData['task_id']??"";
			}
			$isUpdateMaster = 0;
			if (isset($data['task_id']) && !empty($data['task_id'])) {
				$master = TaskHealthMaster::where('deleted_flag', 'N')
					->where('task_id', $data['task_id'])
					->orderBy('id','desc')
					->first();
				if(isset($master->id) && !empty($master->id)){
					$insertId = $master->id;
					$isUpdateMaster = 1;
				}
			}
			if ($isUpdateMaster == 0) {
				$insert = new TaskHealthMaster($data);
				$insert->save();
				$insertId = $insert->id;
			}

			$updateArr = [];
			if ($agencySettingData->hha_link == 1) {
				$updateArr['is_task_sync'] = 1;
			}

			if ($agencySettingData->send_poc == 1) {
				$updateArr['is_poc_sync'] = 1;
			}

			if ($agencySettingData->send_to_supervision == 1) {
				$updateArr['is_supervision_sync'] = 1;
			}

			if (!empty($updateArr)) {
				TaskHealthMaster::where('id', $insertId)->update($updateArr);
			}
			if($isUpdateMaster == 1){
				return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array(array('appointment_id' => $insertId))], 200);
			}
			if ($insertId) {
				if($patientId !=""){
					if($task_health_link !=""){
						Patient::where('id',$patientId)->update(array('task_health_link'=>$task_health_link,'updated_date' => now(),'updated_by' => $created_by,'third_party_callback_url' => $request->callback));
					}else{
						Patient::where('id',$patientId)->update(array('task_health_link'=>$insertId,'updated_date' => now(),'updated_by' => $created_by,'third_party_callback_url' => $request->callback));
					}
					if($flag ==0){
						$patientServiceCount = $this->patientServicesRequest->getServiceCountPatientId($patientId);
						if (count($patientServiceCount) == 0) {
							$services = explode(',', $getExistingPatientDetails->service_id);
							if (!empty($services[0])) {
								$patientServiceLastId = $this->patientServicesRequest->save([
									'patient_id' => $getExistingPatientDetails->id,
									'follow_up_date' => $getExistingPatientDetails->fu_date,
									'due_date' => $getExistingPatientDetails->due_date,
									'status' => $getExistingPatientDetails->status,
									'created_at' => $getExistingPatientDetails->created_date,
									'created_by' =>$created_by,
									'completed_date' => $getExistingPatientDetails->completed_date,
									'completed_by' => $getExistingPatientDetails->completed_by,
									'flag' => 1,
									'from_api' => 1,
								]);
								foreach ($services as $serviceId) {
									$patientWiseServiceRequest = [
										'patient_id' => $getExistingPatientDetails->id,
										'service_id' => $serviceId,
										'patient_service_request_id' => $patientServiceLastId,
										'created_date' =>$getExistingPatientDetails->created_date,
										'created_by' =>$created_by,
									];
									$saveServices = new PatientWiseServiceRequest($patientWiseServiceRequest);
									$saveServices->save();
								}
							}
						}
					}
					
					$statusServiceRequest = "Pending";
					if(strtolower($sendResponseData['type']) =='patient'){
						$statusServiceRequest = Utility::getStatusFromServiceId($serviceIdArray);
					}
					$patientServiceLast = new PatientServiceRequest([
						'patient_id' => $patientId,
						'from_api'=>1,
						'created_at'=>date('Y-m-d H:i:s'),
						'created_by'=>$created_by,
						'follow_up_date'=>$fuDate,
						'due_date'=>$dueDate,
						'status'=>$statusServiceRequest
					]);

					$patientServiceLast->save();
					$patientServiceLastId = $patientServiceLast->id;
				
					foreach ($serviceIdArray as $serviceId) {
						$patientWiseServiceRequest = [
							'patient_id' => $patientId,
							'service_id' => $serviceId,
							'patient_service_request_id' => $patientServiceLastId,
							'created_date'=>date('Y-m-d H:i:s'),
							'created_by'=>$created_by,
						];
						
						$saveServices = new PatientWiseServiceRequest($patientWiseServiceRequest);
						$saveServices->save();
					}

					TaskHealthMaster::where('id',$insertId)->update(array('requested_service_id'=>$patientServiceLastId));
					
					if($flag ==0){
						Patient::where('id',$getExistingPatientDetails->id)->update(array('status'=>$statusServiceRequest,'fu_date'=>$fuDate,'due_date'=>$dueDate,'service_id'=>implode(',',$serviceIdArray)));
					}
				}
				if(strtolower($sendResponseData['type']) =='patient'){
					try {
						Utility::saveResolutionLogForms($statusServiceRequest,$patientServiceLastId,$patientId);
					} catch (\Throwable $th) {
						//throw $th;
					}
				}
				
				if ($request->hasFile('file')) {
					$sendResponseData['file_data'] = $request->file->getClientOriginalName();
				}
				
				$insertLog = [
					'type' => 'Add Appointment',
					'link' => url('/patient/add'),
					'module' => 'Patient Appointment',
					'object_id' => $patientId,
					'message' => 'Task health created a appointent',
					'new_response' => serialize($sendResponseData),
					
				];
				LogsService::save($insertLog);
				/*************************Document Upload Code Start *************************/
				$image = '';
                if ($request->hasFile('file')) {
					$file = $request->file('file');
					$ext = $file->getClientOriginalExtension();
					$fileType = $file->getMimeType();
					$originalName = $file->getClientOriginalName();
                    $name = uniqid() . time() . '_' . $originalName;
					$fileSize = $file->getSize();

                    if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
                        Storage::disk('public')->putFileAs('patientdocument/', $file, $name);
						Storage::disk('public')->putFileAs('patientWriteDocument/', $file, $name);
						$image = $name;
                    }else{
						Storage::disk('s3')->putFileAs('patientdocument/', $file, $name);
						Storage::disk('s3')->putFileAs('patientWriteDocument/', $file, $name);
                        $image = $name;
                    }

                    $request_service =$patientServiceLastId;
					$internal_use =1;
					$assignDocumentUser = null;
					
					$document_name = "Task Health";
					$data = array(
						'document_name' =>$document_name,
						'attachment' => $image,
						'patient_id' => $patientId,
						'request_service_id' => $request_service,
						'is_checked' =>0,
						'internal_use' => $internal_use,
						'assign_document_review' => $assignDocumentUser,
						'created_date'=>date('Y-m-d H:i:s'),
						'created_by'=>$created_by,
						'document_review_status'=>"Approved",
						'extension'=>$ext,
						'size_in_bytes'=>$fileSize,
					    'pdf_type'=>$fileType,
                        'call_back_url' => $request->callback,
						'flag' => 1
					);
					
					$newResponse = $data;
					$insert = $this->documentPatientService->save($data);

					if (count($serviceIdArray) >0) {
						foreach ($serviceIdArray as $serviceId) {
							$data = [
								'patient_id' => $patientId,
								'document_id' => $insert,
								'service_id' => $serviceId,
							];
							$this->documentUploadService->save($data);
						}
					}

					$insertLog = [
						'type' => 'Add Document From Task Health Appointment',
						'link' =>  url('/api/lead/save-task-health-appointment'),
						'module' => 'Patient Appointment',
						'object_id' => $patientId,
						'message' =>'Task Health Appointment has Add Document From Appointment',
						'new_response' => serialize($newResponse),
					];
					if (isset($getExistingRecord) && $getExistingRecord != "") {
						$insertLog['old_response'] = serialize($getExistingRecord->toArray());
					}
					LogsService::save($insertLog);

					$this->sendTaskHealthDocumentService->save([
						'patient_id' => $patientId,
						'document_id'=>$insert,
						'document_name' =>$document_name,
						'attachment' => $image,
						'request_service_id' => $request_service,
						'is_checked' =>0,
						'internal_use' => $internal_use,
						'assign_document_review' => $assignDocumentUser,
						'created_date'=>date('Y-m-d H:i:s'),
						'created_by'=>$created_by,
						'document_review_status'=>"Approved",
						'extension'=>$ext,
						'size_in_bytes'=>$fileSize,
					    'pdf_type'=>$fileType,
						
					]);
                }
               
				if(strtolower($sendResponseData['type']) =='caregiver'){
					$email = ['jromero@nybestmedical.com','developer@nybestmedical.com'];
				}else{
					$email = ['Muhammadh@nybestmedical.com','developer@nybestmedical.com'];
				}

				$getUserDetails = User::getDetailsById($created_by);
				$fname = isset($getUserDetails->first_name)?$getUserDetails->first_name:"";
				$lname = isset($getUserDetails->last_name)?$getUserDetails->last_name:"";

				$agencyDetails = Agency::getAllDetailsbyAgencyId($sendResponseData['agency_id']);
				$agencyname = isset($agencyDetails->agency_name)?$agencyDetails->agency_name:"";
				$email_data = array(
					'username' => $fname.' '.$lname,
					'agencyname' => $agencyname,
					'insert' => $patientId,
					'first_name' => $sendResponseData['first_name'],
					'last_name' => $sendResponseData['last_name'],
					'dob' =>$age,
					'mobile' =>str_replace('-','',$sendResponseData['mobile']),
					'gender' => $sendResponseData['gender'],
					'discipline' =>$sendResponseData['discipline']??'N/A',
					'type' =>$sendResponseData['type'],
					'referral_by'=>'Task Health'
				);
				$messages = Utility::getHtmlContent('email_template.email_create_patient_new', $email_data);
				
				$subject = "[" . $agencyname . "] ID# ".$patientId." - New Task Health request";
				try {
					Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
					   $message->to($email, "Ny Best Medicals")
						   ->subject($subject)->setBody($messages, 'text/html');
				   });
			   } catch (\Throwable $th) {
				   //throw $th;
			   }
				if(isset($sendResponseData['task_id']) && $sendResponseData['task_id'] !=""){
					// $this->mapPOCTaskDetails($sendResponseData['task_id']??null);
				}
				return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array(array('appointment_id' => $insertId))], 200);
			} else {
				
				return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
			}
		}
	}

    public function getAllAgencyList(Request $request){
        $header = $request->header('authorization');
		$checkToken = GenerateAgencyTokenHelper::checkToken($header);
		if (empty($checkToken)) {
			return response()->json(['error_msg' => "Invalid token.", 'status' => 0, 'data' => array()], $this->successStatus);
			die();
		}

		$response = self::checkBlockIPAddress($checkToken->ip_block);
		if($response ==0){
			return response()->json(['error_msg' => "Your IP Address is Blocked.", 'status' => 0, 'data' => array()], $this->successStatus);
			die();
		}

		self::app_tarce($header);
		self::saveTokenWiseApiCall($checkToken->id);

		$agenycyList = $this->agencyTaskHealthService->getAllAgencyList();
		return response()->json(['success' => "data", 'status' => 1, 'data' => $agenycyList], $this->successStatus);
    }

	function convertGender($gender)
	{
		$gender = strtolower(trim($gender));

		if ($gender === 'male' || $gender === 'm') {
			return 'male';
		}

		if ($gender === 'female' || $gender === 'f') {
			return 'female';
		}

		if ($gender === 'other' || $gender === 'o') {
			return 'other';
		}
		return ucfirst($gender); // fallback
	}

	/*********Vishal Mapped POC Task */
	public function mapPOCTaskDetails($taskId){
	
		$visitTaskHealth = TaskHealthApiHelper::getVisitDetail($taskId);
		if(isset($visitTaskHealth['data']['task']['agencyId'])){
			$agencyId = $this->detectLocalAgency($visitTaskHealth['data']['task']['agencyId']);
			if(isset($agencyId['id']) && $agencyId['id'] !=""){
				$getAllPOCTask = $this->hhaPOCTaskService->getAllPOCTaskWithAgencyId($agencyId['id']);
			
				$pocTask = [];
				if(!empty($getAllPOCTask[0])){
					foreach($getAllPOCTask as $val){
						$tempPoc = [];
						$tempPoc['hha_task_id'] = $val->task_id;
						$tempPoc['hha_task_code'] = $val->task_code;
						$tempPoc['hha_task_name'] = $val->task_name;
						$pocTask[$val->task_code] = $tempPoc;
					}
				}
		
				$data = $visitTaskHealth['data'] ?? [];
		
				if (!empty($data['planOfCareItems'])) {
					foreach ($data['planOfCareItems'] as $item) {
						$this->visitTaskHealthService->save($item);
		
						if(isset($pocTask[$item['code']])){
							$this->pocMatchedTaskService->save($item,$pocTask);
						}
					}
				}
			}
		}
	}

	private function detectLocalAgency(int $agency_id): ?array
    {
        // Fallback: look up agencyName from the TH agencies API by agencyId
        if (isset($agency_id) && !empty($agency_id)) {
            $agencyApi = TaskHealthApiHelper::getAgencies();
            if ($agencyApi['status'] && !empty($agencyApi['data'])) {

				$result = collect($agencyApi['data'])->firstWhere('taskHealthAgencyId', $agency_id);
				return ['id' => $result['nyBestId']];
                
            }
        }
        return null;
    }
}