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
class API1Controller extends BaseController
{
	public $successStatus = 200;
	protected $patientService,$documentPatientService,$doctorService,$locationMasterService,$thirdPartyPatientMaster,$documentUploadService="";
	public function __construct(PatientService $patientService, DocumentPatientService $documentPatientService, DoctorService $doctorService,LocationMasterService $locationMasterService,ThirdPartyPatientMasterService $thirdPartyPatientMaster,DocumentUploadService $documentUploadService )
	{
		
		$this->patientService = $patientService;
		$this->documentPatientService = $documentPatientService;
		$this->doctorService = $doctorService;
		$this->locationMasterService  = $locationMasterService ;
		$this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
		$this->documentUploadService = $documentUploadService;
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
		$user_track_data = array("url" => $actual_link, 'api_key' => $apiKey, 'ip' => $ipaddress,'response'=>$user_info,'created_date'=>date('Y-m-d H:i:s'));

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
			$query = $this->patientService->getPatientDetailsById($pt->patient_id,$checkToken->agency_id);

			$status = isset($query->status)?$query->status:$pt->status;
			$temp = $pt;
			
			$temp['id'] = $pt->id;
			$temp['status'] = $status;
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
		
			$getUserDetails = User::where('id',$pt->created_by)->first();
			$getAssignUser = AssignEMCRecord::where('record_id',$pt->id)->first();

			$temp['service_name'] = $pt->service_name;
			$temp['users'] = $getUserDetails??"";
			$temp['assign_to_user'] = $getAssignUser??"";
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
	
			if (isset($query->id)) {
				$documentList = $this->documentPatientService->getAllDocumentByPatientId($query->id);
				
				if(!empty($documentList[0])){
					foreach($documentList as $val){
						
						$services = $this->documentUploadService->getDocumentListByDocumentId($val->id);
						$serviceArray = [];
						if(!empty($services[0])){
							foreach($services as $ser){
								if(isset($ser->masterDetails) && $ser->masterDetails !=""){
									$serviceArray[] = $ser->masterDetails;
								}
							}
						}

						$val->services = $serviceArray;
					}
				}
				return response()->json(['success' => "data", 'status' => 1, 'data' => $documentList], $this->successStatus);
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
				'language' => $request->language,
				'address1' => $request->address1,
				'address2' => $request->address2,
				'state' => $request->state,
				'city' => $request->city,
				'zip_code' => $request->zip_code,
				'county' => $request->county,
				'payment_type' => $request->payment_type,
				'platform_type' => $request->platform_type,
				'platform_id' => $request->platform_id,
				'created_date'=>date('Y-m-d H:i:s'),
				'partner_agency'=>$request->partner_agency,
				'agency_token_id'=>$checkToken->id
				
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
				return response()->json(['success' => "data", 'status' => 1, 'data' => array($record)], $this->successStatus);
			
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

	public function getDocumentListNew(Request $request)
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
	
			if (isset($query->id)) {

                $getServices = $this->documentUploadService->getDocumentListByPatientId($query->id);

                if(!empty($getServices[0])){
                    foreach($getServices as $val){
                        
                        $documentList = $this->documentPatientService->getDetailsByIdAll($val->document_id);
                        
                        if(!empty($documentUpload[$val->service_id][0])){
                            $documentUpload[$val->service_id] = $documentList;
                        }else{
                            $documentUpload = [];
                            $documentUpload[$val->service_id] = $documentList;
                        }
                       
                        $val->document= $documentUpload[$val->service_id]??"";
                        $val->service = $val->masterDetails->name;
                    }
                }
				
				return response()->json(['success' => "data", 'status' => 1, 'data' => $getServices], $this->successStatus);
			} else {
				return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 500);
				die();
			}
		}else{
			return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 500);
				die();
		}
		
	}
}