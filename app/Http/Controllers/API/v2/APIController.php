<?php

namespace App\Http\Controllers\API\V2;

use App\Agency;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Model\Patient;
use App\Model\ThirdPartyPatientMaster;
use App\Model\Language;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Validator;
use App\Helpers\GenerateAgencyTokenHelper;
use App\Services\PatientService;
use App\Services\DocumentPatientService;
use App\Services\DoctorService;
use URL;
use App\Master;
use App\Services\LocationMasterService;
use App\User;
use App\ZipCode;
use Illuminate\Support\Facades\Storage;
use PDO;
use App\Model\ThirdPartyPatientLog;
use App\Model\AssignEMCRecord;
use App\Model\TokenwiseApiCall;
use App\Services\LogsService;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\DocumentUploadService;
use App\Services\PatientServicesRequest;
use App\Services\SiteSettingServices;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Mail;
use App\Services\EmmacareWebhookService;
use App\Services\InsuranceMasterService;
use App\Services\PatientWiseServicesRequests;
use App\Model\PatientServiceRequest;
use App\Model\PatientWiseServiceRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\SendRNPadDocumentService;
use App\Services\PatientV2Service;
use App\Services\ThirdPartyPatientMasterDocumentDataService;

class APIController extends BaseController
{
	public $successStatus = 200;
	protected $patientService;
	protected $documentPatientService;
	protected $doctorService;
	protected $locationMasterService;
	protected $thirdPartyPatientMaster;
	protected $documentUploadService;
	protected $patientServicesRequest;
	protected $siteSettingService;
	protected $emmacareWebhookService;
	protected $insuranceMasterService;
	protected $patientWiseServiceRequests;
	protected $sendRNPadDocumentService;
	protected $patientV2Service;
	protected $thirdPartyPatientMasterDocumentDataService;

	public function __construct(PatientService $patientService, DocumentPatientService $documentPatientService, DoctorService $doctorService,LocationMasterService $locationMasterService,ThirdPartyPatientMasterService $thirdPartyPatientMaster,DocumentUploadService $documentUploadService,PatientServicesRequest $patientServicesRequest,SiteSettingServices $siteSettingService,EmmacareWebhookService $emmacareWebhookService,InsuranceMasterService $insuranceMasterService,PatientWiseServicesRequests $patientWiseServiceRequests,SendRNPadDocumentService $sendRNPadDocumentService,PatientV2Service $patientV2Service, ThirdPartyPatientMasterDocumentDataService $thirdPartyPatientMasterDocumentDataService)
	{

		$this->patientService = $patientService;
		$this->documentPatientService = $documentPatientService;
		$this->doctorService = $doctorService;
		$this->locationMasterService  = $locationMasterService ;
		$this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
		$this->documentUploadService = $documentUploadService;
		$this->patientServicesRequest = $patientServicesRequest;
		$this->siteSettingService = $siteSettingService;
		$this->emmacareWebhookService = $emmacareWebhookService;
		$this->insuranceMasterService = $insuranceMasterService;
		$this->patientWiseServiceRequests = $patientWiseServiceRequests;
		$this->sendRNPadDocumentService = $sendRNPadDocumentService;
		$this->patientV2Service = $patientV2Service;
		$this->thirdPartyPatientMasterDocumentDataService = $thirdPartyPatientMasterDocumentDataService;
	}

	function app_tarce($apiKey)
	{
		$auth = auth()->user();
		if ($auth) {
			$user_id = $auth->id;
		}


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
		} else {
			$user_post_data = $_GET;
		}
	

		$user_post_data = json_encode($user_post_data);
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$remotehost = @getHostByAddr($ipaddress);
		$user_info = json_encode(array("Ip" => $ipaddress, "Page" => $page, "UserAgent" => $useragent, "RemoteHost" => $remotehost));
		$urlPath = parse_url($actual_link, PHP_URL_PATH);
		$endpoint = basename($urlPath); 
		$type = ucwords(str_replace('-', ' ', $endpoint));
	
		$user_track_data = array("url" => $actual_link,'type'=>$type, 'api_key' => $apiKey, 'ip' => $ipaddress,'response'=>$user_info,'created_date'=>date('Y-m-d H:i:s'),'data'=>$user_post_data);

		$saveLog = new ThirdPartyPatientLog($user_track_data);
		$saveLog->save();
	}

	public function patientlist(Request $request)
	{

		$header = $request->header('authorization');
	
		$checkToken = GenerateAgencyTokenHelper::checkToken($header);
		
		if (empty($checkToken)) {
			return response()->json(['error_msg' => "Invalid token.", 'status' => 0, 'data' => array()], $this->successStatus);
			die();
		}

		$response = self::checkBlockIPAddress($checkToken->ip_block);
		// if($response ==0){
		// 	return response()->json(['error_msg' => "Your IP Address is Blocked.", 'status' => 0, 'data' => array()], $this->successStatus);
		// 	die();
		// }

		self::app_tarce($header);
		self::saveTokenWiseApiCall($checkToken->id);
		
		$agency_fk = $data['agency_fk'] = $checkToken->agency_id;
		
		$query = $this->thirdPartyPatientMaster->patientListForUsignThirdPartyApi($agency_fk,$request->first_name,$request->last_name,$request->patient_code,$request->offset);
		
		$finalArray = [];
		foreach ($query as $pt) {
			
			$assign_fname = '';
			$assign_lname = '';
			$temp = [];
			//$query = $this->patientService->getPatientDetailsById($pt->patient_id,$checkToken->agency_id);

			$status = $pt->status;
			//$temp = $pt;
            $getServiceRequestStatus = $this->patientServicesRequest->getDetailsById($pt->requested_service_id);
			
			if($pt->flag ==1){
				$status = "Pending";
			}else{
				if(isset($getServiceRequestStatus->id)){
					$status = $getServiceRequestStatus->status;
				}
			}
			
			$temp['id'] = $pt->id;
            $temp['first_name'] = $pt->first_name;
            $temp['middle_name'] = $pt->middle_name;
            $temp['last_name'] = $pt->last_name;
            $temp['dob'] = $pt->dob;
            $temp['gender'] = $pt->gender;
            $temp['remark'] = $pt->remark;
            $temp['status'] = $status;
            $temp['phone'] = $pt->phone;
            $temp['created_date'] = $pt->created_date;
            $temp['agency_id'] = $pt->agency_id;
            $temp['appointment_date'] = $pt->appointment_date;
            $temp['service_id'] = $pt->service_id;
            $temp['mobile'] = $pt->mobile;
            $temp['language'] = $pt->language;
            $temp['type'] = $pt->type;
            $temp['diciplin'] = $pt->diciplin;
            $temp['notes'] = $pt->notes;
            $temp['address1'] = $pt->address1;
            $temp['address2'] = $pt->address2;
            $temp['state'] = $pt->state;
            $temp['city'] = $pt->city;
            $temp['zip_code'] = $pt->zip_code;
            $temp['payment_type'] = $pt->payment_type;
            $temp['platform_type'] = $pt->platform_type;
            $temp['platform_id'] = $pt->platform_id;
            $temp['email'] = $pt->email;
            $temp['emergency_phone'] = $pt->emergency_phone;
            $temp['cin'] = $pt->cin;
            $temp['emergency_contact_name'] = $pt->emergency_contact_name;
            $temp['ssn'] = $pt->ssn;
			$service_start_date = NULL;
			if(isset($pt->service_start_date) && $pt->service_start_date !=""){
				$service_start_date = date('m/d/Y',strtotime($pt->service_start_date));
			}
            $temp['service_start_date'] = $service_start_date;
           
            $temp['availability_followup_date'] = $pt->availability_followup_date;
            
            $temp['email'] = $pt->email;
			
			if (isset($pt->patientDetails->assignToUser->first_name) && $pt->patientDetails->assignToUser->first_name != '') {
				$assign_fname = $pt->assignToUser->first_name??"";
			}
			if (isset($pt->patientDetails->assignToUser->last_name) && $pt->patientDetails->assignToUser->last_name != '') {
				$assign_lname = $pt->assignToUser->last_name??"";
			}
			
			$temp['assign_user_name'] = $assign_fname . ' ' . $assign_lname;
			$pt->assign_user_name = $assign_fname . ' ' . $assign_lname;
			$explode = explode(',', $pt->service_id);
			$newss = "" . $pt->service_id;
			if ($newss != '') {
				$sins = Master::select('name')->whereRaw('id  IN (' . $newss . ')')->where('del_flag', 'N')->get();

				$nrens = array();
				foreach ($sins as $names) {
					$nrens[$pt->id][] = $names->name;
				}
			}
			$pt->service_name = '';
			if (isset($nrens[$pt->id]) && $nrens[$pt->id] != '') {
				$pt->service_name = implode(',', $nrens[$pt->id]);
			}
		
			
			$temp['service_name'] = $pt->service_name;
		
			$finalArray[] = $temp;
		}
		$op = $finalArray;
		
		return response()->json(['success' => "data", 'status' => 1, 'data' => $op], $this->successStatus);
	}

	public function getDocumentList(Request $request)
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

		$id = $request->input('id');

		$getPatientDetails = $this->thirdPartyPatientMaster->getPatientDetails($id,$checkToken->agency_id);

		if(isset($getPatientDetails->patient_id)){
			$query = $this->patientService->getPatientDetailsById($getPatientDetails->patient_id,$checkToken->agency_id);
            $finalDocumentListArray =[];
			if (isset($query->id)) {
				$documentList = $this->documentPatientService->getAllDocumentListByServiceRequest($query->id,$getPatientDetails->requested_service_id);
				$docid = [];
				if(!empty($documentList[0])){
					foreach($documentList as $val){
                        $temp = [];
                        $temp['id'] = $val->id;
                        $temp['patient_id'] = $val->patient_id;
                        $temp['document_name'] = $val->document_name;
                        $temp['attachment'] = $val->attachment;
                        $temp['created_date'] = $val->created_date;
                        $temp['created_by'] = $val->created_by;
                        $temp['updated_date'] = $val->updated_date;
                        $temp['updated_by'] = $val->updated_by;
                        $temp['request_service_id'] = $val->request_service_id;
                        $temp['first_name'] = $val->first_name;
                        $temp['last_name'] = $val->last_name;
                        $temp['updated_first_name'] = $val->updated_first_name;
                        $temp['updated_last_name'] = $val->updated_last_name;
						$services = $this->documentUploadService->getDocumentListByDocumentId($val->id);
						$serviceArray = [];
						if(!empty($services[0])){
							foreach($services as $ser){
								if(isset($ser->masterDetails) && $ser->masterDetails !=""){
									$serviceArray[] = $ser->masterDetails;
								}
							}
						}
                        $temp['services'] =$serviceArray;
				        $finalDocumentListArray[] = $temp;
						$docid[] = $val->id;
					}
				}

				$thirdPartyDocs = $this->getThirdPartyDocumentData($id,$getPatientDetails->patient_id,$docid);
				$finalDocumentListArray = array_merge($finalDocumentListArray,$thirdPartyDocs);
				return response()->json(['success' => "data", 'status' => 1, 'data' => $finalDocumentListArray], $this->successStatus);
			} else {
				return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 500);
				die();
			}
		}else{
			return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 500);
				die();
		}
		
	}

	public function serviceList(Request $request)
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

		$serviceLists = Master::getServiceRequestNew("Caregiver");
		$serviceListn = Master::getServiceRequestNew("Patient");
		$serviceList = array_merge($serviceLists->toArray(), $serviceListn->toArray());

		$temparray = array();
		$final_array = array();

		foreach ($serviceList as $val) { 
			$temparray['id'] = $val['id'];
			$temparray['name'] = $val['name'];
			$temparray['types'] = $val['types'];
			$final_array[] = $temparray;
		}
		return response()->json(['success' => "data", 'status' => 1, 'data' => $final_array], $this->successStatus);
	}
	public function getSendDocument(Request $request)
	{
		$data['patientDetails'] = $request->patientDetails;
		$data['sendDocument'] = $request->sendDocument;
		$data['documentDetails'] = $request->documentDetails;

		return response()->json(['success' => "data", 'status' => 1, 'data' => $data], $this->successStatus);
	}

	public function addPatient(Request $request)
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

		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'type' => 'required',
			'last_name' => 'required',
			'mobile' => 'required|numeric|digits_between:10,15',
			'service_id' => 'required',
			'dob' => 'required',
			// 'insurance_id' => 'required',
			// 'insurance_name' => 'required',
			
		]);
		if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			
			$getExistingPatientDetails = $this->patientService->checkForThirdPartyExistingDataApi($request->all(),$checkToken->agency_id);
			
			$patientId = "";
			$created_by = env('API_USER_ID');

			$link_third_party = "";
			if(isset($getExistingPatientDetails->id)){
				$patientId = $getExistingPatientDetails->id;
				$link_third_party = $getExistingPatientDetails->link_third_party;
				$flag = 0;
			}else{
				$allDataSaveAppointment = $request->all();
				$allDataSaveAppointment['token_id'] = $checkToken->id;
				$allDataSaveAppointment['agency_id'] = $checkToken->agency_id;
				$patientId = $this->createNewPatient($allDataSaveAppointment);
				$flag = 1;
			}

			$age = NULL;
			if (isset($request->dob) && $request->dob != '') {
				//$age = date('Y-m-d', strtotime($request->dob));
				$age = Utility::convertMdyToYmdUsingCarbon($request->dob);
			}
			$fuDate =NULL;
			if (isset($request->fu_date) && $request->fu_date != '') {
				//$fuDate = date('Y-m-d', strtotime($request->fu_date));
				$fuDate = Utility::convertMdyToYmdUsingCarbon($request->fu_date);
			}
			$dueDate = NULL;
			if (isset($request->due_date) && $request->due_date != '') {
				
				// $dueDate = date('Y-m-d', strtotime($request->due_date));
				$dueDate = Utility::convertMdyToYmdUsingCarbon($request->due_date);
			}
			
			$service_start_date = NULL;
			if (isset($request->service_start_date) && $request->service_start_date != '') {
				$service_start_date = date('Y-m-d', strtotime($request->service_start_date));
			}

			$serviceIds =  explode(',',$request->service_id);
			$serviceIdArray = [];
			$invalidServiceIds = [];
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $st){
					$details = Master::where('id',$st)->where('master_type_fk',11)->where('del_flag','N')->where(DB::raw('LOWER(types)'), strtolower($request->type))->first();
					if(isset($details->id) && $details->id !=""){
						$serviceIdArray[] = $st;
					}
				}
			}
			
			if(count($serviceIdArray) == 0){
				return response()->json(['error_msg' => "Sorry, we couldn’t locate the service you requested.", 'status' => 0, 'data' => array()], 422);
			}
			$data = array(
				'patient_id' => $patientId,
				'first_name' => $request->first_name,
				'middle_name' =>  $request->middle_name,
				'last_name' =>  $request->last_name,
				'type' =>  $request->type,
				'dob' => $age,
				'fu_date' =>$fuDate,
				'due_date' => $dueDate,
				'phone' => $request->phone,
				'mobile' => $request->mobile,
				'agency_id' =>$checkToken->agency_id,
				'gender' => $request->gender,
				'remarks' => $request->message,
				'service_id' => implode(',',$serviceIdArray),
				'patient_code' => $request->patient_code,
				'diciplin' => $request->diciplin,
				'language' => $request->language,
				'address1' => $request->address1,
				'address2' => $request->address2,
				'state' => $request->state,
				'city' => $request->city,
				'zip_code' => $request->zipcode,
				'county' => $request->country,
				'payment_type' => $request->payment_type,
				'platform_type' => $request->platform_type,
				'platform_id' => $request->platform_id,
				'created_date'=>date('Y-m-d H:i:s'),
				'partner_agency'=>$request->partner_agency,
				'agency_token_id'=>$checkToken->id,
				'third_party_priority'=>$request->priority,
				'cin' => $request->cin,
				'ssn' => str_replace('-','',$request->ssn),
				'emergency_contact_name' => $request->emergency_contact_name,
				'emergency_phone' => $request->emergency_contact_number,
				'insurance_id' => $request->insurance_id,
				'insurance_name' => $request->insurance_name,
				'created_by' => $created_by,
				'service_start_date' => $service_start_date,
			);
		
			if($request->insurance_name =='other'){
				$data['other_insurance_name'] = $request->other_insurance_name;
				
			}
			if($request->type == 'Patient'){
				$data['status'] = Utility::getStatusFromServiceId($serviceIdArray);
			}
			if(isset($request->callback)){
				$data['third_party_callback_url'] = $request->callback??"";
			}
			$insert = new ThirdPartyPatientMaster($data);
			$insert->save();
			$insertId = $insert->id;
			
			if ($insertId) {
				$serviceRequestStatus = 'Pending';
				if($request->type == 'Patient'){
					$serviceRequestStatus = $data['status'];
				}
				if($patientId !=""){
					Patient::where('id',$patientId)->update(array('status'=>$serviceRequestStatus));
					if($link_third_party ==""){
						Patient::where('id',$patientId)->update(array('link_third_party'=>$insertId));
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
									'from_api' => 1
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
					
					$patientServiceLast = new PatientServiceRequest([
						'patient_id' => $patientId,
						'from_api'=>1,
						'created_at'=>date('Y-m-d H:i:s'),
						'created_by'=>$created_by,
						'follow_up_date'=>$fuDate,
						'due_date'=>$dueDate,
						'status' => $serviceRequestStatus
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

					ThirdPartyPatientMaster::where('id',$insertId)->update(array('requested_service_id'=>$patientServiceLastId));
					
					if($flag ==0){
						Patient::where('id',$getExistingPatientDetails->id)->update(array('status'=>$serviceRequestStatus,'fu_date'=>$fuDate,'due_date'=>$dueDate,'service_id'=>implode(',',$serviceIdArray)));
					}
				}

				$insertLog = [
					'type' => 'Add Appointment',
					'link' => url('/patient/add'),
					'module' => 'Patient Appointment',
					'object_id' => $insertId,
					'message' => 'Third Party created a appointment',
					'new_response' => serialize($request->all()),
					
				];
				LogsService::save($insertLog);
				if(strtolower($request->type) =='caregiver'){
					$email = ['jromero@nybestmedical.com','developer@nybestmedical.com'];
				}else{
					$email = ['tiline@nybestmedical.com','developer@nybestmedical.com'];
				}

				$getUserDetails = User::getDetailsById($created_by);
				$fname = isset($getUserDetails->first_name)?$getUserDetails->first_name:"";
				$lname = isset($getUserDetails->last_name)?$getUserDetails->last_name:"";

				$agencyDetails = Agency::getAllDetailsbyAgencyId($checkToken->agency_id);
				$agencyname = isset($agencyDetails->agency_name)?$agencyDetails->agency_name:"";
				$email_data = array(
					'username' => $fname.' '.$lname,
					'agencyname' => $agencyname,
					'insert' => $patientId,
					'first_name' => $request->first_name,
					'last_name' => $request->last_name,
					'dob' => $request->dob,
					'mobile' => $request->mobile,
					'gender' => $request->gender,
					'discipline' => $request->diciplin,
					'type' => $request->type,
				);
				$messages = Utility::getHtmlContent('email_template.email_create_patient_new', $email_data);
				$subject = "[" . $agencyname . "] NYBest Medical Care New record added";
				try {
					$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
						$message->to($email, "Ny Best Medicals")
							->subject($subject)->setBody($messages, 'text/html');
					});
				} catch (\Throwable $th) {
					//throw $th;
				}
				if($request->type == 'Patient'){
					try{
						Utility::saveResolutionLogForms($serviceRequestStatus,$patientServiceLastId,$patientId);
					}catch(Exception $e){

					}
				}
				return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array(array('appoinment_id' => $insertId))], 200);
			} else {
				return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
			}
		}
	}

	public function downloadDocument(Request $request){
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

		$id = $request->input('id');
		$getPatientDetails = $this->thirdPartyPatientMaster->getPatientDetails($id,$checkToken->agency_id);
		
		$pid1 = $getPatientDetails->patient_id ??"";
		$query = $this->patientService->getPatientDetailsById($pid1,$checkToken->agency_id);

		if(isset($query->id) && $query->id !=''){
			$documentDetails = $this->documentPatientService->getDetailsById($request->documentId);

			if(isset($documentDetails->id) && $documentDetails->id !=""){
				$headers = [];
				$file = public_path('/') . "/patientdocument/" . $documentDetails->attachment;
				if( str_contains($documentDetails->attachment,'patientdocument')){
					return   Storage::disk('s3')->download($documentDetails->attachment);
					  die();
				  }else{
					return   Storage::disk('s3')->download('patientdocument/'.$documentDetails->attachment);
					  die();
	
				  }

				  return response()->download($file, $documentDetails->attachment, $headers);
			}
		}
		
		
	}

	public function languageList(Request $request){
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

		$languageList = Language::getLanguageList();
		return response()->json(['success' => "data", 'status' => 1, 'data' => $languageList], $this->successStatus);
	}

	public function disciplineList(Request $request){
		
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

		$final_arra1= [];
		$valueArray = ['HHA','CDPAP','RN','LPN','Pre-HHA','Pre-CDPAP','OTHER'];
		foreach($valueArray as $val){
			$temparray = [];
			$temparray['id']=$val;
			$temparray['value']=$val;
			$final_arra1[] = $temparray;
		}
	
		return response()->json(['success' => "data", 'status' => 1, 'data' => $final_arra1], $this->successStatus);
	}

	public function paymentTypeList(Request $request){
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

		$serviceLists = Master::getAllDataByMasterTypeFk(array(17));
	
		return response()->json(['success' => "data", 'status' => 1, 'data' => $serviceLists], $this->successStatus);
	}

	public function patientDetail(Request $request){
		
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
		

		$getPatientDetails = $this->thirdPartyPatientMaster->getPatientDetails($request->id,$checkToken->agency_id);

		if(isset($getPatientDetails->id)){
			$records = $this->patientService->getPatientDetailsById($getPatientDetails->patient_id,$checkToken->agency_id);

				$record = $getPatientDetails;
				$asing = isset($records->assign_user_id)?$records->assign_user_id:"";
				$getAssignNyUser = User::getDetailsById($asing);
				$afname = '';
				$alname = '';
				$record->assign_user = '';
				if (isset($getAssignNyUser->first_name) && $getAssignNyUser->first_name != '') {
					$afname = $getAssignNyUser->first_name;
				}
				if (isset($getAssignNyUser->last_name) && $getAssignNyUser->last_name != '') {
					$alname = $getAssignNyUser->last_name;
					$record->assign_user = $afname . ' ' . $alname;
				}
	
				if ($record->created_by != '') {
					$getUserDetails = User::getDetailsById($record->created_by);
				}
				$fname = '';
				$lname = '';
				
				if (isset($getUserDetails->first_name) && $getUserDetails->first_name != '') {
					$fname = $getUserDetails->first_name;
				}
				if (isset($getUserDetails->last_name) && $getUserDetails->last_name != '') {
					$lname = $getUserDetails->last_name;
				}
				$record->createdBy = $fname . ' ' . $lname;
			
	
				$servie = '';
				if (isset($record->service_id) && $record->service_id != '') {
					$finalArray = [];
					$explode = explode(',', $record->service_id);
					foreach ($explode	as	$val) {
						if ($val	!= "") {
							$finalArray[] = $val;
						}
					}
					$services = Master::whereRaw('id IN (' . implode(',', $finalArray) . ')')->where('del_flag', 'N')->get();
						
					$newass  = array();
					foreach ($services as $kke) {
						$newass[] = $kke->name;
					}
	
					if (!empty($newass)) {
						$servie = implode(',', $newass);
					}
				}
	
				$record->service = $servie;
				$status = $record->status;
			//$temp = $pt;
            $getServiceRequestStatus = $this->patientServicesRequest->getDetailsById($record->requested_service_id);
			if($getPatientDetails->flag ==1){
				$record->status = "Pending";
			}else{
				if(isset($getServiceRequestStatus->id)){
					$record->status = $getServiceRequestStatus->status;
				}
			}
			
			// if(isset($getServiceRequestStatus->id)){
            //     $record->status = $getServiceRequestStatus->status;
            // }
			$temp=array();
			$temp['id'] = $record->id;
            $temp['first_name'] = $record->first_name;
            $temp['middle_name'] = $record->middle_name;
            $temp['last_name'] = $record->last_name;
            $temp['dob'] = $record->dob;
            $temp['gender'] = $record->gender;
            $temp['remark'] = $record->remark;
          
            $temp['phone'] = $record->phone;
            $temp['created_date'] = $record->created_date;
            $temp['agency_id'] = $record->agency_id;
            // $temp['appointment_date'] = $record->appointment_date;
            $temp['service_id'] = $record->service_id;
            $temp['mobile'] = $record->mobile;
            $temp['language'] = $record->language;
            $temp['type'] = $record->type;
            $temp['diciplin'] = $record->diciplin;
            $temp['notes'] = $record->notes;
            $temp['address1'] = $record->address1;
            $temp['address2'] = $record->address2;
            $temp['state'] = $record->state;
            $temp['city'] = $record->city;
            $temp['zip_code'] = $record->zip_code;
            $temp['payment_type'] = $record->payment_type;
            $temp['platform_type'] = $record->platform_type;
            $temp['platform_id'] = $record->platform_id;
            $temp['email'] = $record->email;
            $temp['emergency_phone'] = $record->emergency_phone;
            // $temp['availability_followup_date'] = $record->availability_followup_date;
			$status = $record->status;
			//$temp = $pt;
            // $getServiceRequestStatus = $this->patientServicesRequest->getDetailsById($record->id);
			// if(isset($getServiceRequestStatus->id)){
            //     $status = $getServiceRequestStatus->status;
            // }
			$temp['status'] = $status;
				
				return response()->json(['success' => "data", 'status' => 1, 'data' => $temp], $this->successStatus);
			
		}else{
			return response()->json(['error_msg' => 'No record available.', 'status' => 0, 'data' => array()], 401);
		}
		
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
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			//ip pass from proxy 
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		// echo "<pre>";print_r($ip);die();
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

	public function getDocumentListNew1asdasd(Request $request)
	{
	}

	public function documentsServiceList(Request $request){
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

		$id = $request->input('id');
		$serviceId = $request->input('serviceId');

		$getPatientDetails = $this->thirdPartyPatientMaster->getPatientDetails($id,$checkToken->agency_id);
		$documents=[];
		$documentIds=[];

		if(isset($getPatientDetails->patient_id)){
			$query = $this->patientService->getPatientDetailsById($getPatientDetails->patient_id,$checkToken->agency_id);
			
			
			if (isset($query->id)) {

                $getServices = $this->documentUploadService->getDocumentListByPatientId($query->id,$serviceId);
				
				$doc = [];
                if(!empty($getServices[0])){
                    foreach($getServices as $val){
                       
						$temp = [];
						$temp['id'] = $val->service_id;
						$temp['service_name'] = $val->masterDetails->name;

						if(isset($doc[$val->document_id])){
							$doc[$val->document_id][] =$temp;
						}else{
							$doc[$val->document_id] =[];
							$doc[$val->document_id][] = $temp;
						}
						if(!in_array($val->document_id,$documentIds)){
							$documentIds[] = $val->document_id;
						}
                    }
                }
				
				$documentList = $this->documentPatientService->getDetailsByIdAllWithRequestedServiceId($documentIds,$getPatientDetails->requested_service_id);
				foreach ($documentList as $key => $value) {
					
						$value->services = [];
						if(isset($doc[$value->id])){
							$value->services = $doc[$value->id];
						}
						$documents[]=$value;
					
					
				}
				$documents = $this->getThirdPartServiceWiseDocData($documents,$serviceId);
				if(!empty($documents[0])){
					return response()->json(['success' => "data", 'status' => 1, 'data' => $documents], $this->successStatus);
					die();
				}else{
					return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 200);
					die();
				}
				
			} else {
				return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 200);
				die();
			}
		}else{
			return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 200);
				die();
		}
	}

	public function saveDocument(Request $request){
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

		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'document_name' => 'required',
			'attachment' => 'required',			
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$id = $request->id; // portal id patient id
			$getPatientDetails = $this->thirdPartyPatientMaster->getPatientDetails($id,$checkToken->agency_id);			
			if(isset($getPatientDetails->patient_id)){

				$attachment = '';

				if ($request->file('attachment') != '') {
					$attachment = $request->file('attachment');
					$name = uniqid() . time() . '.' . $attachment->getClientOriginalExtension();

					$attachment = $filepath = Storage::disk('s3')->putFileAs('patientdocument', $attachment, $name);
					$attachment = $name;
				}

				$data = array(
					'patient_id' => $getPatientDetails->patient_id,
					'document_name' => $request->input('document_name'), // portal id patient id
					'attachment' => $attachment,
					
					'created_date' => date('Y-m-d H:i:s'),
					
				);

				if(isset($request->document_completed_date) && $request->document_completed_date !=""){
					$data['document_completed_date'] = date('Y-m-d',strtotime($request->document_completed_date));
				}

				$insertId = $this->documentPatientService->saveNew($data);				

				if(!empty($insertId)){
					return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array(array('document_id' => $insertId))], 200);
					die();
				}else{
					return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 200);
					die();
				}					
			}else{
				return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 200);
					die();
			}
		}
	}

	public function cancellationRequest(Request $request){
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

		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {

			$thirdPartyUserDetails = $this->thirdPartyPatientMaster->getPatientDetails($request->id,$checkToken->agency_id);
			if(!$thirdPartyUserDetails){
				return response()->json(['error_msg' => "No record found", 'status' => 0, 'data' => array()], 404);

			}
			
			$update = $this->thirdPartyPatientMaster->update(array('status'=>'cancelled','cancel_date'=>date('Y-m-d H:i:s')),array('id'=>$request->id));
			$insertLog = [
				'type' => 'Cancellation API',
				'link' => "",
				'module' => 'Patient Appointment',
				'object_id' => $request->id,
				'message' => 'Third Party appointment cancellation',
				'new_response' => serialize($request->all()),
				'created_by'=>env('API_USER_ID')
			];
			LogsService::save($insertLog);	
			// $thirdPartyUserDetails = $this->thirdPartyPatientMaster->getPatientDetails($request->id,$checkToken->agency_id);

			$agencyName = "";
			if(isset($thirdPartyUserDetails->agencyDetails->agency_name)){
				$agencyName = $thirdPartyUserDetails->agencyDetails->agency_name;
			}
			$emailData = array(
				'first_name' => $thirdPartyUserDetails->first_name,
				'last_name' => $thirdPartyUserDetails->last_name,
				'agencyname' => $agencyName,
				'insert' =>$request->id,
				'type' => $thirdPartyUserDetails->type,
			);
			$messages = Utility::getHtmlContent('email_template.cancellation_email',$emailData);
	
			$getSiteSettingDetails = $this->siteSettingService->getDetails();
			$subject="Appointment has been Cancelled";
			try {
				$explode = explode(',',$getSiteSettingDetails->cancellation_email);
				if(!empty($explode[0])){
					foreach($explode as $email){
						$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
							$message->to($email, "EMC Rep")
								->subject($subject)->setBody($messages, 'text/html');
							
							
						});
					}
					
				}
				
			} catch (\Throwable $th) {
				//throw $th;
			}
			return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array()], 200);
		}
	}

	public function emmacareWebhook(Request $request){
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

		// self::app_tarce($header);
		// self::saveTokenWiseApiCall($checkToken->id);

		$data = [
			'agency_id'=>$checkToken->id,
			'referral_id'=>$request->referral_id,
			'patient_id'=>$request->patient_id,
			'token'=>$header,
			'created_date'=>date('Y-m-d H:i:s'),
		];

		$save = $this->emmacareWebhookService->save($data);
		if($save){
			return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array()], 200);
		}else{
			return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
		}
	}

	public function insuranceList(Request $request){
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
		$data = $this->insuranceMasterService->getInsuranceMasterList();
		$final = [];
		if(!empty($data[0])){
			foreach($data as $ln){
				$final[] = [
					'id'=>$ln->id,
					'insurance_name'=>$ln->insurance_name,
				];
			}
		}
		$newInsurance = [
			'id' => "other",
			'insurance_name' => 'Other'
		];
		array_push($final, $newInsurance);
		return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => $final], 200);
	}

	public function createNewPatient($data){
		$age = null;
		if (isset($data['dob']) && $data['dob'] != '') {
			$age = Utility::convertMdyToYmdUsingCarbon($data['dob']);
		}
	
		$fuDate =null;
		if (isset($data['fu_date']) && $data['fu_date'] != '') {
			$fuDate = Utility::convertMdyToYmdUsingCarbon($data['fu_date']);
		}
		$dueDate = null;
		if (isset($data['due_date']) && $data['due_date'] != '') {
			$dueDate = Utility::convertMdyToYmdUsingCarbon($data['due_date']);
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
			return response()->json(['error_msg' => "Sorry, we couldn’t locate the service you requested.", 'status' => 0, 'data' => array()], 422);
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
			'phone' => str_replace(['(', ')', ' ', '-'], '', $data['phone'])??"",
			'mobile' => str_replace(['(', ')', ' ', '-'], '', $data['mobile'])??"",
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
			'created_by' => env('API_USER_ID'),
			'status'=>$status,
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

	public function updateServiceStartDate(Request $request){
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

		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$service_start_date = NULL;
			if (isset($request->service_start_date) && $request->service_start_date != '') {
				$service_start_date = date('Y-m-d', strtotime($request->service_start_date));
			}

			$getOldDetails = ThirdPartyPatientMaster::where('id',$request->id)->first();
			$data =['service_start_date' => $service_start_date];

			$update = ThirdPartyPatientMaster::where('id',$request->id)->update($data);
			
			$oldPatientDetails="";
			if(isset($getOldDetails->patient_id) && $getOldDetails->patient_id !=""){
				$oldPatientDetails = Patient::where('id',$getOldDetails->patient_id)->where('deleted_flag','N')->first();
				Patient::where('id',$getOldDetails->patient_id)->update($data);
			}
			
			$insertLog = [
				'type' => 'Update Service Start Date',
				'link' => "",
				'module' => 'Patient Appointment',
				'object_id' => $request->id,
				'message' => 'Third Party appointment update service start date',
				'new_response' => serialize($data),
				'old_response' => serialize($getOldDetails),
			];
			LogsService::save($insertLog);	

			if(isset($getOldDetails->patient_id) && $getOldDetails->patient_id !=""){
				$insertLog = [
					'type' => 'Update Service Start Date',
					'link' => "",
					'module' => 'Patient Appointment',
					'object_id' => $getOldDetails->patient_id,
					'message' => 'Third Party appointment update service start date',
					'new_response' => serialize($data),
					'old_response' => serialize($oldPatientDetails),
				];
				LogsService::save($insertLog);

			}
			return response()->json(['error_msg' => "Service Start Date successfully updated", 'status' => 1, 'data' => array(array('appoinment_id' => $request->id))], 200);
		}
	}

	/*******************RNPad */
	public function addPatientNew(Request $request)
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
		$decodePriority = $sendResponseData['priority'];

		$priceImage =$request->file;
      
		
		$validator = Validator::make($sendResponseData, [
			'first_name' => 'required',
			'type' => 'required',
			'last_name' => 'required',
			'mobile' => 'required|numeric|digits_between:10,15',
			'service_id' => 'required',
			'dob' => 'required',
			// 'insurance_id' => 'required',
			// 'insurance_name' => 'required',
			
		]);
		if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			
			$getExistingPatientDetails = $this->patientService->checkForThirdPartyExistingDataApi($sendResponseData,$checkToken->agency_id);
			$patientId = "";
			$created_by = env('API_USER_ID');
			$link_third_party = "";
			
			if(isset($getExistingPatientDetails->id)){
				$patientId = $getExistingPatientDetails->id;
				$link_third_party = $getExistingPatientDetails->link_third_party;
				$flag = 0;
                $sendResponseData['fu_date'] = $sendResponseData['followup_date'];
				$sendResponseData['due_Date'] = $sendResponseData['due_date'];
				$sendResponseData['diciplin'] = $sendResponseData['discipline'];
			
				
				$emergency_contact_name="";
				$emergency_contact_number = "";
				if(isset($sendResponseData['patient_emergency_contacts']) && count($sendResponseData['patient_emergency_contacts']) >0){
					$emergency_contact_name =$sendResponseData['patient_emergency_contacts'][0]['emergency_contact_name'];
					$emergency_contact_number =$sendResponseData['patient_emergency_contacts'][0]['emergency_contact_number'];
				}
				$sendResponseData['emergency_contact_name'] =$emergency_contact_name;
				$sendResponseData['emergency_contact_number']=$emergency_contact_number;
			}else{
				
				$sendResponseData['fu_date'] = $sendResponseData['followup_date'];
				$sendResponseData['due_Date'] = $sendResponseData['due_date'];
				$sendResponseData['diciplin'] = $sendResponseData['discipline'];
				
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
				$allDataSaveAppointment['agency_id'] = $checkToken->agency_id;
				$allDataSaveAppointment['third_party_callback_url'] = $request->callback;
				
				$patientId = $this->createNewPatient($allDataSaveAppointment);
				$flag = 1;
			}
    
			$age = null;
			if (isset($sendResponseData['dob']) && $sendResponseData['dob'] != '') {
				$age = Utility::convertMdyToYmdUsingCarbon($sendResponseData['dob']);
			}
			$fuDate =null;
		
			if (isset($sendResponseData['fu_date']) && $sendResponseData['fu_date'] != '') {
				$fuDate = Utility::convertMdyToYmdUsingCarbon($sendResponseData['fu_date']);
			}
			$dueDate = null;
			
			if (isset($sendResponseData['due_date']) && $sendResponseData['due_date'] != '') {
				$dueDate = Utility::convertMdyToYmdUsingCarbon($sendResponseData['due_date']);
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
				'middle_name' =>$sendResponseData['middle_name'],
				'last_name' =>$sendResponseData['last_name'],
				'full_name'=>$sendResponseData['first_name'].' '.$sendResponseData['last_name'],
				'type' => $sendResponseData['type'],
				'dob' => $age,
				'fu_date' =>$fuDate,
				'due_date' => $dueDate,
				'phone' =>str_replace(['(', ')', ' ', '-'], '', $sendResponseData['phone']),
				'mobile' =>str_replace(['(', ')', ' ', '-'], '', $sendResponseData['mobile']),
				'agency_id' =>$checkToken->agency_id,
				'gender' =>$sendResponseData['gender'],
				'remarks' =>$sendResponseData['message'],
				'service_id' => implode(',',$serviceIdArray),
				'patient_code' => $sendResponseData['patient_code'],
				'diciplin' =>$sendResponseData['diciplin'],
				'language' =>$sendResponseData['language'],
				'address1' =>$sendResponseData['address1'],
				'address2' =>$sendResponseData['address2'],
				'state' =>$sendResponseData['state'],
				'city' =>$sendResponseData['city'],
				'zip_code' =>$sendResponseData['zipcode'],
				'county' =>$sendResponseData['country'],
				'payment_type' =>$sendResponseData['payment_type'],
				'platform_type' =>$sendResponseData['platform_type'],
				'platform_id' =>$sendResponseData['platform_id'],
				'created_date'=>date('Y-m-d H:i:s'),
				'partner_agency'=>$sendResponseData['partner_agency'],
				'agency_token_id'=>$checkToken->id,
				'third_party_priority'=>$sendResponseData['priority'],
				'cin' =>$sendResponseData['cin'],
				'ssn' => str_replace('-','',$sendResponseData['ssn']),
				'emergency_contact_name' => $sendResponseData['patient_emergency_contacts']['emergency_contact_name']??"",
				'emergency_phone' =>$sendResponseData['patient_emergency_contacts']['emergency_contact_number']??"",
				'insurance_id' =>$sendResponseData['insurance_id'],
				'insurance_name' =>$sendResponseData['insurance_name'],
				'created_by' => $created_by,
				'service_start_date' => $service_start_date,
			
			);

			if($sendResponseData['insurance_name'] =='other'){
				$data['other_insurance_name'] = $sendResponseData['other_insurance_name'];
				
			}
			if(isset($request->callback)){
				$data['third_party_callback_url'] = $request->callback??"";
			}
			$insert = new ThirdPartyPatientMaster($data);
			$insert->save();
			$insertId = $insert->id;
			
			if ($insertId) {
				if($patientId !=""){
					if($link_third_party !=""){
						Patient::where('id',$patientId)->update(array('link_third_party'=>$link_third_party));
					}else{
						Patient::where('id',$patientId)->update(array('link_third_party'=>$insertId));
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
									'from_api' => 1
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

					ThirdPartyPatientMaster::where('id',$insertId)->update(array('requested_service_id'=>$patientServiceLastId));
					
					if($flag ==0){
						Patient::where('id',$getExistingPatientDetails->id)->update(array('status'=>'Pending','fu_date'=>$fuDate,'due_date'=>$dueDate,'service_id'=>implode(',',$serviceIdArray)));
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
					'object_id' => $insertId,
					'message' => 'Third Party created a appointent',
					'new_response' => serialize($sendResponseData),
					
				];
				LogsService::save($insertLog);

				$insertLog1 = [
					'type' => 'Add Appointment',
					'link' => url('/patient/add'),
					'module' => 'Patient Appointment',
					'object_id' => $patientId,
					'message' => 'Third Party created a appointent',
					'new_response' => serialize($sendResponseData),
					
				];
				LogsService::save($insertLog1);

				/*************************Document Upload Code Start *************************/
				$image = '';
                if ($request->hasFile('file')) {
					$file = $request->file('file');
					$ext = $file->getClientOriginalExtension();
					$fileType = $file->getMimeType();
					$file_path = $file->getPathName();
					$originalName = $file->getClientOriginalName();
                    $name = uniqid() . time() . '_' . $originalName;
					$destination1 = public_path('patientdocument');
					$destination2 = public_path('patientWriteDocument');
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
					
					$document_name = "Unsigned MDOrder";
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
						'type' => 'Add Document From Third Party Appointment',
						'link' =>  url('/api/lead/save-lead-appointment'),
						'module' => 'Patient Appointment',
						'object_id' => $patientId,
						'message' =>'Third Party Appointment has Add Document From Appointment',
						'new_response' => serialize($newResponse),
	
					];
					if (isset($getExistingRecord) && $getExistingRecord != "") {
						$insertLog['old_response'] = serialize($getExistingRecord->toArray());
					}
					LogsService::save($insertLog);

					$this->sendRNPadDocumentService->save([
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

				$agencyDetails = Agency::getAllDetailsbyAgencyId($checkToken->agency_id);
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
					'discipline' =>$sendResponseData['discipline'],
					'type' =>$sendResponseData['type'],
					'referral_by'=>'RNPAD'
				);
				$messages = Utility::getHtmlContent('email_template.email_create_patient_new', $email_data);
				
				$subject = "[" . $agencyname . "] ID# ".$patientId." - New MDO request from RNPAD";
				try {
					 Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
						$message->to($email, "Ny Best Medicals")
							->subject($subject)->setBody($messages, 'text/html');
					});
				} catch (\Throwable $th) {
					//throw $th;
				}
				
				return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array(array('appoinment_id' => $insertId))], 200);
			} else {
				return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
			}
		}
	}

	public function addPatientNewOld(Request $request)
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
		
		$sendResponseData = json_decode($request->patient_basic_data,true);

		$priceImage = $request->file('file');
		
		$validator = Validator::make($sendResponseData, [
			'first_name' => 'required',
			'type' => 'required',
			'last_name' => 'required',
			'mobile' => 'required|numeric|digits_between:10,15',
			'service_id' => 'required',
			'dob' => 'required',
			// 'insurance_id' => 'required',
			// 'insurance_name' => 'required',
			
		]);
		if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			
			$getExistingPatientDetails = $this->patientService->checkForThirdPartyExistingDataApi($sendResponseData,$checkToken->agency_id);
			$patientId = "";
			$created_by = env('API_USER_ID');
			$link_third_party = "";
			if(isset($getExistingPatientDetails->id)){
				$patientId = $getExistingPatientDetails->id;
				$link_third_party = $getExistingPatientDetails->link_third_party;
				$flag = 0;
			}else{
				$allDataSaveAppointment = $sendResponseData;
				$allDataSaveAppointment['token_id'] = $checkToken->id;
				$allDataSaveAppointment['agency_id'] = $checkToken->agency_id;
				$allDataSaveAppointment['third_party_callback_url'] = $request->callback;
				$patientId = $this->createNewPatient($allDataSaveAppointment);
				$flag = 1;
			}

			$age = NULL;
			if (isset($sendResponseData['dob']) && $sendResponseData['dob'] != '') {
				$age = date('Y-m-d', strtotime($sendResponseData['dob']));
			}
			$fuDate =NULL;
			
			if (isset($sendResponseData['fu_date']) && $sendResponseData['fu_date'] != '') {
				$fuDate = date('Y-m-d', strtotime($sendResponseData['fu_date']));
			}
			$dueDate = NULL;
			
			if (isset($sendResponseData['due_date']) && $sendResponseData['due_date'] != '') {
				$dueDate = date('Y-m-d', strtotime($sendResponseData['due_date']));
			}
			
			$service_start_date = NULL;
			
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
				'middle_name' =>$sendResponseData['middle_name'],
				'last_name' =>$sendResponseData['last_name'],
				
				'type' => $sendResponseData['type'],
				'dob' => $age,
				'fu_date' =>$fuDate,
				'due_date' => $dueDate,
				'phone' =>str_replace('-','',$sendResponseData['phone']),
				'mobile' =>str_replace('-','',$sendResponseData['mobile']),
				'agency_id' =>$checkToken->agency_id,
				'gender' =>$sendResponseData['gender'],
				'remarks' =>$sendResponseData['message'],
				'service_id' => implode(',',$serviceIdArray),
				'patient_code' => $sendResponseData['patient_code'],
				'diciplin' =>$sendResponseData['diciplin'],
				'language' =>$sendResponseData['language'],
				'address1' =>$sendResponseData['address1'],
				'address2' =>$sendResponseData['address2'],
				'state' =>$sendResponseData['state'],
				'city' =>$sendResponseData['city'],
				'zip_code' =>$sendResponseData['zipcode'],
				'county' =>$sendResponseData['country'],
				'payment_type' =>$sendResponseData['payment_type'],
				'platform_type' =>$sendResponseData['platform_type'],
				'platform_id' =>$sendResponseData['platform_id'],
				'created_date'=>date('Y-m-d H:i:s'),
				'partner_agency'=>$sendResponseData['partner_agency'],
				'agency_token_id'=>$checkToken->id,
				'third_party_priority'=>$sendResponseData['priority'],
				'cin' =>$sendResponseData['cin'],
				'ssn' => str_replace('-','',$sendResponseData['ssn']),
				'emergency_contact_name' => $sendResponseData['patient_emergency_contacts']['emergency_contact_name']??"",
				'emergency_phone' =>$sendResponseData['patient_emergency_contacts']['emergency_contact_number']??"",
				'insurance_id' =>$sendResponseData['insurance_id'],
				'insurance_name' =>$sendResponseData['insurance_name'],
				'created_by' => $created_by,
				'service_start_date' => $service_start_date,
			);

			if($sendResponseData['insurance_name'] =='other'){
				$data['other_insurance_name'] = $sendResponseData['other_insurance_name'];
				
			}
			
			$insert = new ThirdPartyPatientMaster($data);
			$insert->save();
			$insertId = $insert->id;
			
			if ($insertId) {
				if($patientId !=""){
					if($link_third_party !=""){
						Patient::where('id',$patientId)->update(array('link_third_party'=>$link_third_party));
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
									'from_api' => 1
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
					
					$patientServiceLast = new PatientServiceRequest([
						'patient_id' => $patientId,
						'from_api'=>1,
						'created_at'=>date('Y-m-d H:i:s'),
						'created_by'=>$created_by,
						'follow_up_date'=>$fuDate,
						'due_date'=>$dueDate,
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

					ThirdPartyPatientMaster::where('id',$insertId)->update(array('requested_service_id'=>$patientServiceLastId));
					
					if($flag ==0){
						Patient::where('id',$getExistingPatientDetails->id)->update(array('status'=>'Pending','fu_date'=>$fuDate,'due_date'=>$dueDate,'service_id'=>implode(',',$serviceIdArray)));
					}
				}

				
				$insertLog = [
					'type' => 'Add Appointment',
					'link' => url('/patient/add'),
					'module' => 'Patient Appointment',
					'object_id' => $insertId,
					'message' => 'Third Party created a appointent',
					'new_response' => serialize($sendResponseData),
					
				];
				LogsService::save($insertLog);	


				/*************************Document Upload Code Start *************************/
				$image = '';

				if ($request->file('file') != '') {
					$priceImage = $request->file('file');
					$name = uniqid() . time() . '.' . $priceImage->getClientOriginalExtension();
					//$destination = public_path('patientdocument');
					$destination1 = public_path('patientdocument');
					$destination2 = public_path('patientWriteDocument');
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$priceImage->move($destination1, $name);
						\File::copy($destination1 . '/' . $name, $destination2 . '/' . $name);

						$image = $name;
					} else {
						//$image = $filepath = Storage::disk('s3')->putFileAs('patientdocument', $priceImage, $name);
						Storage::disk('s3')->putFileAs('patientdocument', $priceImage, $name);
						Storage::disk('s3')->putFileAs('patientWriteDocument', $priceImage, $name);
						$image = $name;
					}

					$request_service =$patientServiceLastId;
					
					$internal_use =0;

					$assignDocumentUser = NULL;
					

					$data = array(
						'document_name' =>$priceImage->getClientOriginalName(),
						'attachment' => $image,
						'patient_id' => $patientId,
						'request_service_id' => $request_service,
						'is_checked' =>0,
						'internal_use' => $internal_use,
						'assign_document_review' => $assignDocumentUser,
						'created_date'=>date('Y-m-d H:i:s'),
						'created_by'=>$created_by,
						'document_review_status'=>"Approved",
						'extension'=>$priceImage->getClientOriginalExtension(),
						'size_in_bytes'=>$priceImage->getSize(),
					
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
						'type' => 'Add Document From Third Party Appointment',
						'link' =>  url('/api/lead/save-lead-appointment'),
						'module' => 'Patient Appointment',
						'object_id' => $patientId,
						'message' =>'Third Party Appointment has Add Document From Appointment',
						'new_response' => serialize($newResponse),
	
					];
					if (isset($getExistingRecord) && $getExistingRecord != "") {
						$insertLog['old_response'] = serialize($getExistingRecord->toArray());
					}
					LogsService::save($insertLog);
				}


				if(strtolower($request->type) =='caregiver'){
					
					$email = ['jromero@nybestmedical.com','developer@nybestmedical.com'];
				}else{
					$email = ['tiline@nybestmedical.com','developer@nybestmedical.com'];
				}

				$getUserDetails = User::getDetailsById($created_by);
				$fname = isset($getUserDetails->first_name)?$getUserDetails->first_name:"";
				$lname = isset($getUserDetails->last_name)?$getUserDetails->last_name:"";

				$agencyDetails = Agency::getAllDetailsbyAgencyId($checkToken->agency_id);
				$agencyname = isset($agencyDetails->agency_name)?$agencyDetails->agency_name:"";
				$email_data = array(
					'username' => $fname.' '.$lname,
					'agencyname' => $agencyname,
					'insert' => $patientId,
					'first_name' => $request->first_name,
					'last_name' => $request->last_name,
					'dob' => $request->dob,
					'mobile' => $request->mobile,
					'gender' => $request->gender,
					'discipline' => $request->diciplin,
					'type' => $request->type,
				);
				$messages = Utility::getHtmlContent('email_template.email_create_patient_new', $email_data);
				$subject = "[" . $agencyname . "] NYBest Medical Care New record added";
				try {
					$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
						$message->to($email, "Ny Best Medicals")
							->subject($subject)->setBody($messages, 'text/html');
					});
				} catch (\Throwable $th) {
					//throw $th;
				}
				
				return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array(array('appoinment_id' => $insertId))], 200);
			} else {
				return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
			}
		}
	}

	public function allPatientListByAgency(Request $request){
		
		$header = $request->header('authorization');
		$checkToken = GenerateAgencyTokenHelper::checkToken($header);
		if (empty($checkToken)) {
			return response()->json(['error_msg' => "Invalid token.", 'status' => 0, 'data' => array()], $this->successStatus);
		}

		$response = self::checkBlockIPAddress($checkToken->ip_block);
		if($response ==0){
			return response()->json(['error_msg' => "Your IP Address is Blocked.", 'status' => 0, 'data' => array()], $this->successStatus);
		}

		self::app_tarce($header);
		self::saveTokenWiseApiCall($checkToken->id);

		$allResponse = $this->patientV2Service->allPatientListByAgencyId($checkToken->agency_id,$request->all(),$request->offset);
		return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => $allResponse], 200);
	}

	public function getAllDocumentListbyAgency(Request $request){
		$header = $request->header('authorization');
		$checkToken = GenerateAgencyTokenHelper::checkToken($header);
		if (empty($checkToken)) {
			return response()->json(['error_msg' => "Invalid token.", 'status' => 0, 'data' => array()], $this->successStatus);
		}

		$response = self::checkBlockIPAddress($checkToken->ip_block);
		if($response ==0){
			return response()->json(['error_msg' => "Your IP Address is Blocked.", 'status' => 0, 'data' => array()], $this->successStatus);
		}

		self::app_tarce($header);
		self::saveTokenWiseApiCall($checkToken->id);

		$query = $this->patientService->getPatientDetailsById($request->id,$checkToken->agency_id);
		$final =[];
		if(isset($query->id)){
			$getAllDocumentList = $this->documentPatientService->getAllDocumentListByApi([$query->id],$request->all(),$request->offset);
			if(!empty($getAllDocumentList[0])){
				foreach($getAllDocumentList as $val){
					
					$services = $this->documentUploadService->getDocumentListByDocumentId($val->id);
					$serviceArray = [];
					if(!empty($services[0])){
						foreach($services as $ser){
							if(isset($ser->masterDetails) && $ser->masterDetails !=""){
								$serviceArray[] = $ser->service_id;
							}
						}
					}
					$getUserDetails = User::getDetailsById($val->created_by);
					$fname = isset($getUserDetails->first_name)?$getUserDetails->first_name:"";
					$lname = isset($getUserDetails->last_name)?$getUserDetails->last_name:"";
					$val->created_user_name = $fname.' '.$lname;
					$val->service_id = implode(',',$serviceArray);
				}
			}

			$final = $getAllDocumentList;
			return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => $final], 200);
		}else{
			return response()->json(['error_msg' => "No record available", 'status' => 1, 'data' => $final], 200);
		}
		
		
	}

	public function downloadPatientDocument(Request $request){
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

		$id = $request->input('id');
		$query = $this->patientService->getPatientDetailsById($id,$checkToken->agency_id);
		
		if(isset($query->id) && $query->id !=''){
			$documentDetails = $this->documentPatientService->getDetailsById($request->documentId);

			if(isset($documentDetails->id) && $documentDetails->id !=""){
				$headers = [];
				$file = public_path('/') . "/patientdocument/" . $documentDetails->attachment;
				if( str_contains($documentDetails->attachment,'patientdocument')){
				return   Storage::disk('s3')->download($documentDetails->attachment);
					die();
				}else{
				return   Storage::disk('s3')->download('patientdocument/'.$documentDetails->attachment);
					die();

				}

				return response()->download($file, $documentDetails->attachment, $headers);
			}
		}else{
			return response()->json(['error_msg' => "No record available.", 'status' => 0, 'data' => array()], $this->successStatus);

		}
	}

	public function getThirdPartyDocumentData($id, $patient_id, $not_in_array_id)
	{
		$finalDocumentListArray = [];
		$queryDocData = $this->thirdPartyPatientMasterDocumentDataService->getByThirdPartyDocId($id, $patient_id);
		if (empty($queryDocData)) {
			return $finalDocumentListArray;
		}
		// Find only missing document IDs
		$missingIds = array_diff($queryDocData, $not_in_array_id);
		if (empty($missingIds)) {
			return $finalDocumentListArray;
		}
		// Call DB only when needed
		$thirdPartyDocumentData = $this->documentPatientService->getAllDocumentListById($missingIds);
		foreach ($thirdPartyDocumentData as $val) {
			$temp = [
				'id' => $val->id,
				'patient_id' => $val->patient_id,
				'document_name' => $val->document_name,
				'attachment' => $val->attachment,
				'created_date' => $val->created_date,
				'created_by' => $val->created_by,
				'updated_date' => $val->updated_date,
				'updated_by' => $val->updated_by,
				'request_service_id' => $val->request_service_id,
				'first_name' => $val->first_name,
				'last_name' => $val->last_name,
				'updated_first_name' => $val->updated_first_name,
				'updated_last_name' => $val->updated_last_name,
				'services' => [],
			];

			$services = $this->documentUploadService->getDocumentListByDocumentId($val->id);

			foreach ($services ?? [] as $ser) {
				if (!empty($ser->masterDetails)) {
					$temp['services'][] = $ser->masterDetails;
				}
			}

			$finalDocumentListArray[] = $temp;
		}

		return $finalDocumentListArray;
	}
	
	public function getThirdPartServiceWiseDocData($documents, $serviceId)
	{
		foreach ($documents as $key => $document) {

			$thirdPartyDoc = $this->thirdPartyPatientMasterDocumentDataService
				->getByServiceId($document->id, $serviceId);

			if (empty($thirdPartyDoc)) {
				unset($documents[$key]); // remove document
				continue;
			}

			// keep existing services (already set earlier)
			$document->services = $document->services ?? [];
		}

		return array_values($documents); // reindex array
	}

}