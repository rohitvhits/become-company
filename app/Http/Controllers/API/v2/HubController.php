<?php

namespace App\Http\Controllers\API\v2;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\HubGenerateAgencyTokenHelper;
use App\Model\HubRecord;
use App\User;
use Illuminate\Support\Facades\Storage;
use App\Model\HubThirdPartyLog;
use App\Model\HubTokenWiseApiCall;
use App\Services\HubRecordService;
use App\Services\HubRecordAgencyService;
use App\Services\HubRecordDocService;
use App\Services\HubRecordNotesService;
use App\Services\LogsService;
use App\Services\HubRecordtextMessageService;
use App\Helpers\Utility;
use App\Master;

class HubController extends BaseController
{
	public $successStatus = 200;
	protected $hubRecordService,$hubRecordDocService,$hubRecordNotesService,$logsService,$hubRecordtextMessageService,$hubRecordAgencyService="";
	public function __construct(HubRecordService $hubRecordService, HubRecordDocService $hubRecordDocService, HubRecordNotesService $hubRecordNotesService, LogsService $logsService, HubRecordtextMessageService $hubRecordtextMessageService,HubRecordAgencyService $hubRecordAgencyService)
	{
		$this->hubRecordService = $hubRecordService;
		$this->hubRecordDocService = $hubRecordDocService;
		$this->hubRecordNotesService = $hubRecordNotesService;
		$this->logsService = $logsService;
		$this->hubRecordtextMessageService = $hubRecordtextMessageService;
		$this->hubRecordAgencyService = $hubRecordAgencyService;
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

		$saveLog = new HubThirdPartyLog($user_track_data);
		$saveLog->save();
	}

	public function hubList(Request $request)
	{
		$header = $request->header('authorization');
		$checkToken = HubGenerateAgencyTokenHelper::checkTokenAccess($header);
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
	
		$agency_fk = $data['agency_fk'] = $checkToken->agency_id;
		if(isset($request->offset) && $request->offset != ''){
            $query = $this->hubRecordAgencyService->fetchHubRecordsByAgency($agency_fk,$request->first_name,$request->last_name,$request->mobile,$request->status,$request->dob,$request->offset);
            $hubData = [];
            if(isset($query) && count($query) > 0)
            {
				
                foreach ($query as $hb)
                {
					$temp = [];
                    $temp = $hb;
                    // $temp['first_name'] = $hb->first_name;
                    // $temp['middle_name'] = $hb->middle_name;
                    // $temp['last_name'] = $hb->last_name;
                    // $temp['dob'] = $hb->dob;
                    // $temp['gender'] = $hb->gender;
                    // $temp['status'] = $hb->status;
                    // $temp['phone'] = $hb->phone;
                    // $temp['created_date'] = $hb->created_date;
                    // $temp['created_by'] = $hb->created_by;
                    // $temp['agency_id'] = $hb->agency_id;
                    // $temp['mobile'] = $hb->mobile;
                    // $temp['address1'] = $hb->address1;
                    // $temp['address2'] = $hb->address2;
                    // $temp['city'] = $hb->city;
                    // $temp['state'] = $hb->state;
                    // $temp['county'] = $hb->county;
                    // $temp['zip_code'] = $hb->zip_code;
                    // $temp['email'] = $hb->email;
                    $hubData[] = $temp;
                }
                return response()->json(['success' => "data", 'status' => 1, 'data' => $hubData], $this->successStatus);
            }else{
                return response()->json(['error_msg' => "No record available.", 'status' => 0, 'data' => array()], $this->successStatus);
			    die();
            }
        }else{
			return response()->json(['error_msg' => "Missing or empty parameter: offset.", 'status' => 0, 'data' => array()], 400);
			die();
		}
	}

	public function hubRecordDetail(Request $request){
		
		$header = $request->header('authorization');
		$checkToken = HubGenerateAgencyTokenHelper::checkTokenAccess($header);
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
		if(isset($request->id) && !empty($request->id)){
            $record = $this->hubRecordAgencyService->getBasicDetailsAPI($request->id,$checkToken->agency_id);
            if(isset($record->id)){
				$temp=array();
				$temp[] =$record;
                
                return response()->json(['success' => "data", 'status' => 1, 'data' => $temp], $this->successStatus);
            }else{
                return response()->json(['error_msg' => 'No record available.', 'status' => 0, 'data' => array()], $this->successStatus);
            }
        }else{
            return response()->json(['error_msg' => 'Missing or empty parameter: id.', 'status' => 0, 'data' => array()], 400);
        }
	}

    public function hubDocList(Request $request)
	{
		$header = $request->header('authorization');
		$checkToken = HubGenerateAgencyTokenHelper::checkTokenAccess($header);
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

		$id = $request->id;
        if( (isset($request->offset) && $request->offset != '') && (isset($id) && $id != '') ){
			$getHubAgencyDetails = $this->hubRecordAgencyService->getAgencyDetails($id,$checkToken->agency_id);
			
            $documentList = $this->hubRecordDocService->getAllDocListApi($id,$checkToken->agency_id,$getHubAgencyDetails->id,$request->offset);
            if(isset($documentList) && count($documentList)){
                $finalDocumentListArray =[];
                foreach($documentList as $val){
                    $temp = [];
                    $temp['id'] = $val->id;
                    $temp['hub_record_id'] = $val->hub_record_id;
                    $temp['document_name'] = $val->document_name;
                    $temp['attachment'] = $val->attachment;
                    $temp['created_date'] = $val->created_date;
                    $temp['created_by'] = $val->created_by;
                    $temp['updated_date'] = $val->updated_date;
                    $temp['updated_by'] = $val->updated_by;
                    $temp['first_name'] = $val->first_name;
                    $temp['last_name'] = $val->last_name;
                    $finalDocumentListArray[] = $temp;
                }
                return response()->json(['success' => "data", 'status' => 1, 'data' => $finalDocumentListArray], $this->successStatus);
            }else{
                return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], $this->successStatus);
                die();
            }
        }else{
			return response()->json(['error_msg' => "Missing or empty parameter: offset and id.", 'status' => 0, 'data' => array()], 400);
			die();
		}
	}

    public function hubNotesList(Request $request)
	{
		$header = $request->header('authorization');
		$checkToken = HubGenerateAgencyTokenHelper::checkTokenAccess($header);
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

		$id = $request->id;
        if( (isset($request->offset) && $request->offset != '') && (isset($id) && $id != '') ){
			$getHubAgencyDetails = $this->hubRecordAgencyService->getAgencyDetails($id,$checkToken->agency_id);
            $NotesList = $this->hubRecordNotesService->getAllNotesListApi($id,$checkToken->agency_id,$getHubAgencyDetails->id,$request->offset);
            if(isset($NotesList) && count($NotesList)){
                $finalNotesListArray =[];
                foreach($NotesList as $val){
                    $temp = [];
                    $temp['id'] = $val->id;
                    $temp['hub_record_id'] = $val->hub_record_id;
                    $temp['message'] = $val->message;
					$temp['subject_id'] = $val->subject;
                    $temp['created_date'] = $val->created_date;
                    $temp['created_by'] = $val->created_by;
                    $temp['updated_date'] = $val->updated_date;
                    $temp['updated_by'] = $val->updated_by;
                    $temp['first_name'] = $val->users->first_name;
                    $temp['last_name'] = $val->users->last_name;
                    $finalNotesListArray[] = $temp;
                }
                return response()->json(['success' => "data", 'status' => 1, 'data' => $finalNotesListArray], $this->successStatus);
            }else{
                return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], $this->successStatus);
                die();
            }
        }else{
			return response()->json(['error_msg' => "Missing or empty parameter: Offset and Hub Record Id.", 'status' => 0, 'data' => array()], 400);
			die();
		}
	}

    public function hubTextMessagesList(Request $request)
	{
		$header = $request->header('authorization');
		$checkToken = HubGenerateAgencyTokenHelper::checkTokenAccess($header);
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

		$id = $request->id;
        if( (isset($request->offset) && $request->offset != '') && (isset($id) && $id != '') ){
			$getHubAgencyDetails = $this->hubRecordAgencyService->getAgencyDetails($id,$checkToken->agency_id);
            $textMessage = $this->hubRecordtextMessageService->getAllMessageListApi($id,$checkToken->agency_id,$getHubAgencyDetails->id,$request->offset);
            if(isset($textMessage) && count($textMessage)){
                $finaltextMessageArray =[];
                foreach($textMessage as $val){
                    $temp = [];
                    $temp['id'] = $val->id;
                    $temp['hub_record_id'] = $val->hub_record_id;
                    $temp['message'] = $val->message;
                    $temp['mobile'] = $val->mobile;
                    $temp['created_date'] = $val->created_date;
                    $temp['created_by'] = $val->created_by;
                    $temp['updated_date'] = $val->updated_date;
                    $temp['updated_by'] = $val->updated_by;
                    $temp['created_first_name'] = $val->userDetails->first_name;
                    $temp['created_last_name'] = $val->userDetails->last_name;
                    $finaltextMessageArray[] = $temp;
                }
                return response()->json(['success' => "data", 'status' => 1, 'data' => $finaltextMessageArray], $this->successStatus);
            }else{
                return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], $this->successStatus);
                die();
            }
        }else{
			return response()->json(['error_msg' => "Missing or empty parameter: offset and id.", 'status' => 0, 'data' => array()], 400);
			die();
		}
	}

    public function statusUpdate(Request $request){
		$header = $request->header('authorization');
		$checkToken = HubGenerateAgencyTokenHelper::checkTokenAccess($header);
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
			'status' => 'required|in:active,deactivated',
		], [
            'status.in' => "The status must be either 'active' or 'deactivated'.",
        ]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$status = NULL;
            $agency_id = $checkToken->agency_id;
			if (isset($request->status) && $request->status != '') {
				$status = strtolower($request->status);
			}
			$getOldDetails = HubRecord::where('id',$request->id)->where('agency_id',$agency_id)->first();
            if(isset($getOldDetails) && !empty($getOldDetails)){
                $data =['status' => $status];
                HubRecord::where('id',$request->id)->update($data);
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Update Hub Record status From Third Party',
                    'link' => url('/api/lead/hub-record-status-update'),
                    'module' => 'Hub Record',
                    'ip'    =>$ipaddress,
                    'object_id' => $request->id,
                    'message' => 'Third Party updated hub record status to '.$status,
                    'new_response' => serialize($data),
                    'old_response' => serialize($getOldDetails),
                ];
                $this->logsService->save($insertLog);
                return response()->json(['error_msg' => "Hub Record status successfully updated", 'status' => 1, 'data' => array(array('hub_record_id' => $request->id))], 200);
            }else{
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again", 'status' => 1, 'data' => array()], 500);
            }
		}
	}

    public function hubDownloadDocument(Request $request){
		$header = $request->header('authorization');

		$checkToken = HubGenerateAgencyTokenHelper::checkTokenAccess($header);
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
		$record = $this->hubRecordAgencyService->getBasicDetailsAPI($id,$checkToken->agency_id);
		if(isset($record->id) && $record->id !=''){
			$documentDetails = $this->hubRecordDocService->getDetailsByIdNew($request->document_id,$record->id);
			if(isset($documentDetails->id) && $documentDetails->id !=""){
				$headers = [];
				$file = public_path('/') . "/hubdocument/" .$record->id.'/'. $documentDetails->attachment;
				if( str_contains($documentDetails->attachment,'hubdocument')){
					return   Storage::disk('s3')->download($documentDetails->attachment);
					die();
                }else{
                    return   Storage::disk('s3')->download('hubdocument/' .$record->id.'/'. $documentDetails->attachment);
                    die();
                }
				return response()->download($file, $documentDetails->attachment, $headers);
			}else{
                return response()->json(['error_msg' => "No Record Found", 'status' => 1, 'data' => array()], $this->successStatus);
            }
		}else{
            return response()->json(['error_msg' => "No Record Found", 'status' => 1, 'data' => array()], 200);
        }
		
		
	}

	public function hubSubjectAPI(Request $request)
	{
		$header = $request->header('authorization');
		$checkToken = HubGenerateAgencyTokenHelper::checkTokenAccess($header);
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

		$subjectData = Master::getAllDataByMasterTypeFk(array(30));
		if(isset($subjectData) && count($subjectData)){
			$finalSubjectArray =[];
			foreach($subjectData as $val){
				$temp = [];
				$temp['id'] = $val->id;
				$temp['name'] = $val->name;
				$finalSubjectArray[] = $temp;
			}
			return response()->json(['success' => "data", 'status' => 1, 'data' => $finalSubjectArray], $this->successStatus);
		}else{
			return response()->json(['error_msg' => "No record available", 'status' => 0, 'data' => array()], 200);
			die();
		}
	}

    public function saveTokenWiseApiCall($tokenId){
		$auth = auth()->user();
		$user_id ="";
		if ($auth) {
			$user_id = $auth->id;
		}
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
		$saveData = new HubTokenWiseApiCall(array('token_id'=>$tokenId,'api_call'=>$actual_link,'created_date'=>date('Y-m-d H:i:s'),'created_by'=>$user_id));
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
}