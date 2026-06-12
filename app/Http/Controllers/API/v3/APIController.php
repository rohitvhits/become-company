<?php

namespace App\Http\Controllers\API\V3;

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
use App\Helpers\Common;
class APIController extends BaseController
{
	public $successStatus = 200;
	protected $patientService,$documentPatientService,$doctorService,$locationMasterService,$thirdPartyPatientMaster,$documentUploadService,$patientServicesRequest="";
	public function __construct(PatientService $patientService, DocumentPatientService $documentPatientService, DoctorService $doctorService,LocationMasterService $locationMasterService,ThirdPartyPatientMasterService $thirdPartyPatientMaster,DocumentUploadService $documentUploadService,PatientServicesRequest $patientServicesRequest)
	{
	
		$this->patientService = $patientService;
		$this->documentPatientService = $documentPatientService;
		$this->doctorService = $doctorService;
		$this->locationMasterService  = $locationMasterService ;
		$this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
		$this->documentUploadService = $documentUploadService;
		$this->patientServicesRequest = $patientServicesRequest;
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
			$user_post_data = array();
		}

		$user_post_data = json_encode($user_post_data);
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$remotehost = @getHostByAddr($ipaddress);
		$user_info = json_encode(array("Ip" => $ipaddress, "Page" => $page, "UserAgent" => $useragent, "RemoteHost" => $remotehost));
		$user_track_data = array("url" => $actual_link, 'api_key' => $apiKey, 'ip' => $ipaddress,'response'=>$user_info,'created_date'=>date('Y-m-d H:i:s'),'data'=>$user_post_data);

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
		
		$query = $this->thirdPartyPatientMaster->patientListForUsignThirdPartyApi($agency_fk,$request->first_name,$request->last_name);
		
		$finalArray = [];
		foreach ($query as $pt) {
			
			$assign_fname = '';
			$assign_lname = '';
			$temp = [];
			//$query = $this->patientService->getPatientDetailsById($pt->patient_id,$checkToken->agency_id);

			$status = $pt->status;
			//$temp = $pt;
            $getServiceRequestStatus = $this->patientServicesRequest->getDetailsById($pt->requested_service_id);
			if(isset($getServiceRequestStatus->id)){
                $status = $getServiceRequestStatus->status;
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
           
            $temp['availability_followup_date'] = $pt->availability_followup_date;
            
            $temp['email'] = $pt->email;
			
			if (isset($pt->patientDetails->assignToUser->first_name) && $pt->patientDetails->assignToUser->first_name != '') {
				$assign_fname = $pt->assignToUser->first_name;
			}
			if (isset($pt->patientDetails->assignToUser->last_name) && $pt->patientDetails->assignToUser->last_name != '') {
				$assign_lname = $pt->assignToUser->last_name;
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
					}
				}
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
			
		]);
		if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			
			$age = '';
			if (isset($request->dob) && $request->dob != '') {
				$age = date('Y-m-d', strtotime($request->dob));
			}
			$fuDate = '';
			if (isset($request->fu_date) && $request->fu_date != '') {
				$fuDate = date('Y-m-d', strtotime($request->fu_date));
			}
			$dueDate = '';
			if (isset($request->due_date) && $request->due_date != '') {
				$dueDate = date('Y-m-d', strtotime($request->due_date));
			}
			
			$serviceIds =  explode(',',$request->service_id);
			$serviceIdArray = [];
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $st){
					$details = Master::where('id',$st)->where('master_type_fk',11)->where('types',$request->type)->first();
					if(isset($details->id) && $details->id !=""){
						$serviceIdArray[] = $st;
					}
				}
			}
			
			$data = array(
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
				'language' => Common::getOrCreateLanguageId($request->language),
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
			);

			$insert = new ThirdPartyPatientMaster($data);
			$insert->save();
			$insertId = $insert->id;
			
			if ($insertId) {
				$insertLog = [
					'type' => 'Add Appointment',
					'link' => url('/patient/add'),
					'module' => 'Patient Appointment',
					'object_id' => $insertId,
					'message' => 'Third Party created a appointent',
					'new_response' => serialize($request->all()),
					
				];
				LogsService::save($insertLog);	
				return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => array(array('appoinment_id' => $insertId))], 200);
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
		$getPatientDetails = $this->thirdPartyPatientMaster->getPatientDetails($id,$checkToken->agency_id);
		$query = $this->patientService->getPatientDetailsById($getPatientDetails->patient_id,$checkToken->agency_id);
		
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
			if(isset($getServiceRequestStatus->id)){
                $record->status = $getServiceRequestStatus->status;
            }
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
	
		if(isset($getPatientDetails->patient_id)){
			$query = $this->patientService->getPatientDetailsById($getPatientDetails->patient_id,$checkToken->agency_id);
			
	
			if (isset($query->id)) {

                $getServices = $this->documentUploadService->getDocumentListByPatientId($query->id,$serviceId);
			
                if(!empty($getServices[0])){
                    foreach($getServices as $val){
                       
                        $documentList = $this->documentPatientService->getDetailsByIdAllWithRequestedServiceId($val->document_id,$getPatientDetails->requested_service_id);
						
						foreach ($documentList as $key => $value) {
							# code...
							$documents[]=$value;
						}
                    
                    }
                }
				
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
}