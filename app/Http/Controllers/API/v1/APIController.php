<?php

namespace App\Http\Controllers\API\V1;

use App\Agency;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Model\Patient;
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
use App\Model\TokenwiseApiCall;
use App\Services\LogsService;
use App\Services\PatientServicesRequest;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Mail;
use App\Model\PatientServiceRequest;
use App\Model\PatientWiseServiceRequest;
use Illuminate\Support\Facades\DB;
use Exception;
class APIController extends BaseController
{
	public $successStatus = 200;
	protected $patientService,$documentPatientService,$doctorService,$locationMasterService,$patientServicesRequest="";
	public function __construct(PatientService $patientService, DocumentPatientService $documentPatientService, DoctorService $doctorService,LocationMasterService $locationMasterService,PatientServicesRequest $patientServicesRequest )
	{
		
		$this->patientService = $patientService;
		$this->documentPatientService = $documentPatientService;
		$this->doctorService = $doctorService;
		$this->locationMasterService  = $locationMasterService ;
		$this->patientServicesRequest  = $patientServicesRequest ;
		
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

		$user_track_data = array("url" => $actual_link, 'type'=>$type, 'api_key' => $apiKey, 'ip' => $ipaddress,'response'=>$user_info,'created_date'=>date('Y-m-d H:i:s'),'data'=>$user_post_data);

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
		
		$query = $this->patientService->patientListForApi($agency_fk,$request->offset);
		$finalArray = [];
		foreach ($query as $vsl) {
			$tempArray = [];
			$getAssignNyUser = User::getDetailsById($vsl->assign_user_id);
			$assign_fname = '';
			$assign_lname = '';
			if (isset($getAssignNyUser->first_name) && $getAssignNyUser->first_name != '') {
				$assign_fname = $getAssignNyUser->first_name;
			}
			if (isset($getAssignNyUser->last_name) && $getAssignNyUser->last_name != '') {
				$assign_lname = $getAssignNyUser->last_name;
			}

			$vsl->assign_user_name = $assign_fname . ' ' . $assign_lname;
			$explode = explode(',', $vsl->service_id);
			$newss = "" . $vsl->service_id;
			if ($newss != '') {
				$sins = Master::select('name')->whereRaw('id  IN (' . $newss . ')')->where('del_flag', 'N')->get();

				$nrens = array();
				foreach ($sins as $names) {
					$nrens[$vsl->id][] = $names->name;
				}
			}
			$vsl->service_name = '';
			if (isset($nrens[$vsl->id]) && $nrens[$vsl->id] != '') {
				$vsl->service_name = implode(',', $nrens[$vsl->id]);
			}
			$tempArray['id']=$vsl->id;
			$tempArray['first_name']=$vsl->first_name;
			$tempArray['middle_name']=$vsl->middle_name;
			$tempArray['last_name']=$vsl->last_name;
			$tempArray['dob']=$vsl->dob;
			$tempArray['gender']=$vsl->gender;
			$tempArray['remarks']=$vsl->remarks;
			$tempArray['phone']=$vsl->phone;
			$tempArray['appointment_date']=$vsl->appointment_date;
			$tempArray['status']=$vsl->status;
			$tempArray['appointment_added_created_date']=$vsl->appointment_added_created_date;
			$tempArray['service_id']=$vsl->service_id;
			$tempArray['mobile']=$vsl->mobile;
			$tempArray['language']=$vsl->language;
			$tempArray['type']=$vsl->type;
			$tempArray['appoinment_time_id']=$vsl->appoinment_time_id;
			$tempArray['patient_code']=$vsl->patient_code;
			$tempArray['diciplin']=$vsl->diciplin;
			$tempArray['notes']=$vsl->notes;
			$tempArray['sms_send_date']=$vsl->sms_send_date;
			$tempArray['telehealth_date_time']=$vsl->telehealth_date_time;
			$tempArray['address1']=$vsl->address1;
			$tempArray['address2']=$vsl->address2;
			$tempArray['state']=$vsl->state;
			$tempArray['city']=$vsl->city;
			$tempArray['zip_code']=$vsl->zip_code;
			$tempArray['county']=$vsl->county;
			$tempArray['payment_type']=$vsl->payment_type;
			$tempArray['fu_date']=$vsl->fu_date;
			$tempArray['platform_type']=$vsl->platform_type;
			$tempArray['platform_id']=$vsl->platform_id;
			$tempArray['hha_other_id']=$vsl->hha_other_id;
			$tempArray['email']=$vsl->email;
			$tempArray['emergency_phone']=$vsl->emergency_phone;
			$tempArray['follow_date']=$vsl->follow_date;
			$tempArray['insurance_id']=$vsl->insurance_id;
			$tempArray['insurance_name']=$vsl->insurance_name;
			$tempArray['location_branch']=$vsl->location_branch;
			$tempArray['other_insurance_name']=$vsl->other_insurance_name;
			$tempArray['cin']=$vsl->cin;
			$tempArray['emergency_contact_name']=$vsl->emergency_contact_name;
			$tempArray['ssn']=$vsl->ssn;
			$tempArray['link_third_party']=$vsl->link_third_party;
			$tempArray['medicare_no']=$vsl->medicare_no;
			$tempArray['agency_token_id']=$vsl->agency_token_id;
			$tempArray['flag']=$vsl->flag;
			$tempArray['reason']=$vsl->reason;
			$tempArray['assign_user_name']=$vsl->assign_user_name;
			$tempArray['service_name']=$vsl->service_name;
			$tempArray['users']=$vsl->users;
			$tempArray['assign_to_user']=$vsl->assign_to_user;
			$finalArray[] = $tempArray;
		}
		$op = $query;
		return response()->json(['success' => "data", 'status' => 1, 'data' => $finalArray], $this->successStatus);
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
		$query = $this->patientService->getPatientDetailsById($id,$checkToken->agency_id);
		if (isset($query->id)) {
			$documentList = $this->documentPatientService->getAllDocumentByPatientIdApiSide([$query->id]);
			return response()->json(['success' => "data", 'status' => 1, 'data' => $documentList], $this->successStatus);
		} else {
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
		]);
		if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$getExistingPatientDetails = $this->patientService->checkForThirdPartyExistingDataApi($request->all(),$checkToken->agency_id);
			$patientId = "";
			$created_by = env('API_USER_ID');
			$flag = 1;

			$fuDate = '';
			if (isset($request->fu_date) && $request->fu_date != '') {
				
				$fuDate = Utility::convertMdyToYmdUsingCarbon($request->fu_date);
			}
			$dueDate = '';
			if (isset($request->due_date) && $request->due_date != '') {
				$dueDate =Utility::convertMdyToYmdUsingCarbon($request->due_date);
			}
			$portalType = strtolower($request->type);
			$serviceIds =  explode(',',$request->service_id);
			$serviceIdArray = [];
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $st){
					$details = Master::where('id',$st)->where('master_type_fk',11)->where('del_flag','N')->where(DB::raw('LOWER(types)'), $portalType)->first();
					if(isset($details->id) && $details->id !=""){
						$serviceIdArray[] = $st;
					}
				}
			}

			if(count($serviceIdArray) == 0){
				return response()->json(['error_msg' => "Sorry, we couldn’t locate the service you requested.", 'status' => 0, 'data' => array()], 422);
			}
			$serviceStatus = 'Pending';
			
			if($portalType == 'patient'){
				$serviceStatus = Utility::getStatusFromServiceId($serviceIdArray);
			}
			if(isset($getExistingPatientDetails->id)){
				$patientId = $getExistingPatientDetails->id;
				Patient::where('id',$patientId)->update(array('status'=>$serviceStatus));
				$flag = 0;
			}else{
				$age = '';
				if (isset($request->dob) && $request->dob != '') {
					$age = Utility::convertMdyToYmdUsingCarbon($request->dob);
				}
				
				
				$data = array(
					'first_name' => $request->first_name,
					'middle_name' =>  $request->middle_name,
					'last_name' =>  $request->last_name,
					'full_name' => $request->first_name.' '.$request->last_name,
					'type' =>  ucfirst($portalType),
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
					'created_by' => $created_by,
					'insurance_id' => isset($request->insurance_id)?$request->insurance_id:"",
            		'insurance_name' =>isset($request->insurance_name)?$request->insurance_name:""
				);
				
				if(isset($request->platform_type) && $request->platform_type !=""){
					if($request->platform_type =='VA'){
						$data['referral_type'] = "Visiting Aid";
					}else{
						$data['referral_type'] = $request->platform_type;
					}
				}

				if(isset($request->insurance_name) && $request->insurance_name =='other'){
					$data['other_insurance_name'] = $request->other_insurance_name??"";
				}
		
				if(isset($request->service_start_date) && $request->service_start_date !=''){
					$data['service_start_date'] = date('Y-m-d',strtotime($request->service_start_date));
				}
				if($portalType == 'patient'){
					$data['status'] = $serviceStatus;
				}

				if(isset($request->location_branch) && $request->location_branch !=''){
					$data['location_branch'] = $request->location_branch;
				}
				$insert = new Patient($data);
				$insert->save();
				$patientId = $insert->id;
			}
			
			$serviceRequestStatus = 'Pending';
			if ($patientId) {
				if($portalType == 'patient'){
					$serviceRequestStatus = $serviceStatus;
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
								'from_api' => 2
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
					'from_api'=>2,
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
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add Appointment',
					'link' => url('/third-party-add'),
					'module' => 'Patient Appointment',
					'object_id' => $patientId,
					'message' => 'Third Party created a appointment',
					'new_response' => serialize($request->all()),
					'ip_address' => $ipaddress,
					'created_by'=>$created_by,
				];
				LogsService::save($insertLog);

 
				if($portalType =='caregiver'){
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
					'type' => ucfirst($portalType),
				);
				$messages = Utility::getHtmlContent('email_template.email_create_patient_new', $email_data);
				$subject = "[" . $agencyname . "] NYBest Medical Care New record added";
				try {
					$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
						$message->to($email, "Ny Best Medicals")
							->subject($subject)->html($messages);
					});
				} catch (\Throwable $th) {
					//throw $th;
				}
				if($portalType == 'patient'){
					try{
						Utility::saveResolutionLogForms($serviceRequestStatus,$patientServiceLastId,$patientId);
					}catch(Exception $e){}
				}
				return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array(array('appoinment_id' => $patientId))], 200);
			} else {
				return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
			}
		}
	}

	public function uploadDocument($document_id, $file)
	{

	
		
	}
	public function getDueContactDocument(Request $request)
	{

	
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
		// $valueArray = ['HHA','CDPAP','RN','LPN','Pre-HHA','Pre-CDPAP','OTHER'];
		$masterDataDiscipline = Master::getAllDataByMasterTypeFk(array(26));
		foreach($masterDataDiscipline as $val){
			$temparray = [];
			$temparray['id']=$val->name;
			$temparray['value']=$val->name;
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

		$record = $this->patientService->getPatientDetailsById($request->id,$checkToken->agency_id);

		if(isset($record->id)){
			$getAssignNyUser = User::getDetailsById($record->assign_user_id);
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
			return response()->json(['success' => "data", 'status' => 1, 'data' => array($record)], $this->successStatus);
		}else{
			return response()->json(['error_msg' => 'Record not available.', 'status' => 0, 'data' => array()], 401);
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

	function help(){
		return response()->json(null, $this->successStatus);
	}
}
