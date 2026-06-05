<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use App\Services\LogsService;
use Illuminate\Support\Facades\Cache;
use App\Agency;
use App\Master;
use App\Services\AgencyWiseServiceService;
use App\Helpers\UserHelper;
use App\Services\TaskService;
use App\Services\PatientNotesService;
use App\Services\DocumentPatientService;
use App\Services\DocumentUploadService;
use App\Services\NotificationUserService;
use App\Helpers\Common;
use App\Helpers\Utility;
use App\Services\FlagMarkedService;
use App\User;
use URL;

class FlagAppointmentController extends BaseController
{

	protected $PatientService,$agencyWiseServiceService,$taskService,$patientNotesService,$documentPatientService,$documentUploadService,$notificationUserService,$flagMarkedService= "";
	
	public function __construct( PatientService $PatientService,AgencyWiseServiceService $agencyWiseServiceService,TaskService $taskService, PatientNotesService $patientNotesService, DocumentPatientService $documentPatientService, DocumentUploadService $documentUploadService, NotificationUserService $notificationUserService,FlagMarkedService $flagMarkedService )
	{
		// $this->middleware('permission:flag-list', ['only' => ['flagList','flagAjaxList']]);
		$this->PatientService = $PatientService;
		$this->agencyWiseServiceService = $agencyWiseServiceService;
		$this->taskService = $taskService;
		$this->patientNotesService = $patientNotesService;
		$this->documentPatientService = $documentPatientService;
		$this->documentUploadService = $documentUploadService;
		$this->notificationUserService = $notificationUserService;
		$this->flagMarkedService = $flagMarkedService;
	}

    function changeStatusFlag(Request $request){
        $user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getOldResponse = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->id);
			if($getOldResponse->flag == 1){
				$this->PatientService->update(array('flag'=>0,'reason' => request('reason')),array('id'=>$request->id));
				$title = 'Flagged';
			}else{
				$this->PatientService->update(array('flag'=>1,'reason' => request('reason')),array('id'=>$request->id));
				$title = 'Flag';
			}
			$msg = 'Appointment flag is changed to '.$title;
            $getNewResponse = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->id);
            
			$insertLogs = [
				'title' => $title,
				'marked_at' => date('Y-m-d h:i:s'),
				'type' => 'Appointment',
				'record_id' => $request->id,
				'reason'=>request('reason')
			];
			$this->flagMarkedService->save($insertLogs);
			
			// $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();
            $insertLog = [
				'type' => 'Updated portal Flag',
				'link' => url('/patient/view') . '/' . $request->id,
				'module' => 'Patient Appointment',
				'object_id' => $request->id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update appointment flag.',
				'old_response' => serialize($getOldResponse),
				'new_response' => serialize($getNewResponse),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			// Add notification to user
			$full_name = $getNewResponse->first_name.' '.$getNewResponse->last_name;
			if($getNewResponse->assign_user_id != ''){
				$users = [$getNewResponse->assign_user_id];
				$agency_fk = '';
			}else{
				if ($user['agency_fk'] != '') {
					$agencyid = $user['agency_fk'];
				} else {
					$agencyid = $getNewResponse->agency_id;
				}
				$query  = User::select('id')->where('delete_flag','N')->where('agency_fk',$agencyid);
				if($user['agency_fk'] !=""){
					$query->where('id','!=',auth()->user()->id);
				}
				$agency_fk = $agencyid;
				$users = $query->pluck('id')->toArray();
			}
			// Get Group wise notification
			$users = Utility::getGroupUsersData($getNewResponse->agency_id,$getNewResponse->type,'Flag',$users);
			$notificationData = array(
				'users' => $users,
				'agency_fk' => $agency_fk,
				'record_id' => $request->id,
				'title' => 'Appointment Flagged for Attention',
				'msg' => '<b>Reason</b>: '.request('reason'),	
				'type' => 'Flag'
			);
			Utility::insertNotificationsType($notificationData);
			return response()->json(['error_msg' => $msg, 'status' => 1, 'data' => array()], 201);
		}
	}

    function flagList(){
		$data['user'] = $user = auth()->user();
		$agency_fk = $data['agency_fk'] = request('agency_fk');
		if(in_array(auth()->user()->id,Utility::agencyPortalRolePermission())){
            abort(404);
        }
		$angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['serviceList'] = Cache::get('patient_master_services', function ()  use ($user, $agency_fk) {
			if ($agency_fk != "") {
				$agencyId = $agency_fk;
			} else {
				$agencyId = $user->agency_fk;
			}
			// $getAgencyWiseList = $this->agencyWiseServiceService->getServiceNew($agencyId);
			$getAgencyWiseList = $this->agencyWiseServiceService->ServiceListNewFlagListNyBestUser($agencyId);
			if (!empty($getAgencyWiseList[0])) {
				return  $getAgencyWiseList;
			} else {
				return  Master::getServiceRequest();
			}
		}, 10 * 60);


		$data['agencyList'] = $angecyList;
		$data['agency_user_list'] =  Cache::get('agency_user_list', function ()  use ($user) {
			return UserHelper::getAgencyWiseUserList($user->agency_fk);
		}, 10 * 60);
		$data['statuses'] = Utility::getUniqueStatusDataNew();
		return view('flagAppointment.flag_list',$data);
	}

	function flagAppointmentAjaxList(Request $request){
		$data['user'] = auth()->user();
		$data['query'] = $query = $this->flagMarkedService->getALLAAppointmentData($request->all(),'Appointment');
		foreach ($query as $vsl) {
			$explode = explode(',', $vsl->service_id);
			$newss = $vsl->service_id;
			if ($newss != '') {
				$sins = Cache::get('patient_master_' . implode(",", $explode), function () use ($explode) {

					return Master::select('name')->whereIn('id', $explode)->where('del_flag', 'N')->get();
				}, 10 * 60); 

				$nrens = array();
				foreach ($sins as $names) {
					$nrens[$vsl->id][] = $names->name;
				}
			}
			$vsl->name = '';
			if (isset($nrens[$vsl->id]) && $nrens[$vsl->id] != '') {
				$vsl->name = implode(', ', $nrens[$vsl->id]);
			}
		}
		$data['query'] = $query;		
		return view('flagAppointment.flag_ajax_list',$data);
	}

	function changeDocStatusFlag(Request $request){
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getOldResponse = $this->documentPatientService->getDetailsById($request->id);
			if($getOldResponse->flag == 1){
				$this->documentPatientService->update(array('flag'=>0,'reason' => request('reason')),array('id'=>$request->id));
				$title = 'Flag';
			}else{
				$this->documentPatientService->update(array('flag'=>1,'reason' => request('reason')),array('id'=>$request->id));
				$title = 'Flagged';
			}
			$getNewResponse = $this->documentPatientService->getDetailsById($request->id);
			//get patient data
			$patientData = $this->PatientService->getPatientDetailsByIdWhitoutAgency($getNewResponse->patient_id);
            
			$insertLogs = [
				'title' => $title,
				'marked_at' => date('Y-m-d h:i:s'),
				'type' => 'Document',
				'record_id' => $request->id,
				'reason'=>request('reason')
			];
			$this->flagMarkedService->save($insertLogs);

			// $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();
            $insertLog = [
				'type' => 'Update Document Flag',
				'link' => url('/patient/view') . '/' . $getNewResponse->patient_id,
				'module' => 'Patient Appointment',
				'object_id' => $getNewResponse->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update document flag',
				'old_response' => serialize($getOldResponse),
				'new_response' => serialize($getNewResponse),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			// Add notification to user
			$full_name = $patientData->first_name.' '.$patientData->last_name;
			if($patientData->assign_user_id != ''){
				$users = [$patientData->assign_user_id];
				$agency_fk = '';
			}else{
				if ($user['agency_fk'] != '') {
					$agencyid = $user['agency_fk'];
				} else {
					$agencyid = $patientData->agency_id;
				}
				$query  = User::select('id')->where('delete_flag','N')->where('agency_fk',$agencyid);
				if($user['agency_fk'] !=""){
					$query->where('id','!=',auth()->user()->id);
				}
				$agency_fk = $agencyid;
				$users = $query->pluck('id')->toArray();
			}
			// Get Group wise notification
			$users = Utility::getGroupUsersData($patientData->agency_id,$patientData->type,'Flag',$users);
			$notificationData = array(
				'users' => $users,
				'agency_fk' => $agency_fk,
				'record_id' => $getNewResponse->patient_id,
				'title' => 'Document Flagged for Review',
				'msg' => $getNewResponse->document_name.'<br/><b>Reason</b>: '.request('reason'),
				'type' => 'Flag'
			);
			Utility::insertNotificationsType($notificationData);
			return response()->json(['error_msg' => "Document flag successfully updated", 'status' => 1, 'data' => array()], 200);
		}
	}

	function changeNotesStatusFlag(Request $request){
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getOldResponse = $this->patientNotesService->getNotesDetailById($request->id);
			if($getOldResponse->flag == 1){
				$this->patientNotesService->update(array('flag'=>0,'reason' => request('reason')),array('id'=>$request->id));
				$title = 'Flag';
			}else{
				$this->patientNotesService->update(array('flag'=>1,'reason' => request('reason')),array('id'=>$request->id));
				$title = 'Flagged';
			}

			$getNewResponse = $this->patientNotesService->getNotesDetailById($request->id);
			//get patient data
			$patientData = $this->PatientService->getPatientDetailsByIdWhitoutAgency($getNewResponse->patient_id);

			$insertLogs = [
				'title' => $title,
				'marked_at' => date('Y-m-d h:i:s'),
				'type' => 'Notes',
				'record_id' => $patientData->id,
				'reason'=>request('reason')
			];
			$this->flagMarkedService->save($insertLogs);
			
			// $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();
            $insertLog = [
				'type' => 'Update Notes Flag',
				'link' => url('/patient/view') . '/' . $patientData->id,
				'module' => 'Patient Appointment',
				'object_id' => $patientData->id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update notes flag',
				'old_response' => serialize($getOldResponse),
				'new_response' => serialize($getNewResponse),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			
			// Add notification to user
			if($getNewResponse->type != 'Agency'){
				$full_name = $patientData->first_name.' '.$patientData->last_name;
				if($patientData->assign_user_id != ''){
					$users = [$patientData->assign_user_id];
					$agency_fk = '';
				}else{
					if ($user['agency_fk'] != '') {
						$agencyid = $user['agency_fk'];
					} else {
						$agencyid = $patientData->agency_id;
					}
					$query  = User::select('id')->where('delete_flag','N')->where('agency_fk',$agencyid);
					if($user['agency_fk'] !=""){
						$query->where('id','!=',auth()->user()->id);
					}
					$agency_fk = $agencyid;
					$users = $query->pluck('id')->toArray();
				}
				// Get Group wise notification
				$users = Utility::getGroupUsersData($patientData->agency_id,$patientData->type,'Flag',$users);
				$notificationData = array(
					'users' => $users,
					'agency_fk' => $agency_fk,
					'record_id' => $patientData->id,
					'title' => 'Notes Flagged for Review',
					'msg' => '<b>Notes</b>: '.substr($getNewResponse->message, 0, 20).'... | <b>Reason</b>: '.request('reason'),	
					'type' => 'Flag'
				);
				Utility::insertNotificationsType($notificationData);
			}
			return response()->json(['error_msg' => "Notes flag successfully updated", 'status' => 1, 'data' => array('flag'=>$getNewResponse->flag)], 200);
		}
	}

	function changeTaskStatusFlag(Request $request){
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getOldResponse = $this->taskService->getDetailsByIdNew($request->id);
			if($getOldResponse->flag == 1){
				$this->taskService->update(array('flag'=>0,'reason' => request('reason')),array('id'=>$request->id));
				$title = 'Flag';
			}else{
				$this->taskService->update(array('flag'=>1,'reason' => request('reason')),array('id'=>$request->id));
				$title = 'Flagged';
			}
			$getNewResponse = $this->taskService->getDetailsByIdNew($request->id);
			
			$insertLogs = [
				'title' => $title,
				'marked_at' => date('Y-m-d h:i:s'),
				'type' => 'Task',
				'record_id' => $request->record_id ? $request->record_id : $request->id,
				'reason'=>request('reason')
			];
			$this->flagMarkedService->save($insertLogs);

            // $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();
            $insertLog = [
				'type' => 'Updated Task Flag',
				'link' => url('/patient/view') . '/' . $request->id,
				'module' => 'Patient Appointment',
				'object_id' =>$getOldResponse->record_id ? $getOldResponse->record_id : $request->id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update task flag',
				'old_response' => serialize($getOldResponse),
				'new_response' => serialize($getNewResponse),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			// Add notification to user
			$users = [$getNewResponse->assign_id];
			$user_full_name = $getNewResponse->assignFname." ".$getNewResponse->assignLnamae;
			$agency_fk = '';
			
			if(isset($request->record_id) && !empty($request->record_id)){
                $record_id = $request->record_id;
                $msg = $getNewResponse->task_name.' <br/> <b>Reason</b>: '.request('reason');
            }else{
                $msg = $getNewResponse->task_name.' | <b>Reason</b>: '.request('reason');
            }
			// Get Group wise notification
			$patientData = $this->PatientService->getPatientDetailsByIdWhitoutAgency($getNewResponse->record_id);
			if(isset($patientData->agency_id) && !empty($patientData->agency_id)){
				$users = Utility::getGroupUsersData($patientData->agency_id,$patientData->type,'Flag',$users);
			}			
			$notificationData = array(
				'users' => $users,
				'agency_fk' => $agency_fk,
				'record_id' => $record_id?? NULL,
				'title' => 'Task Flagged for Attention',
				'msg' => $msg,	
				'type' => 'Flag'
			);
			
			Utility::insertNotificationsType($notificationData);
			return response()->json(['error_msg' => "Task flag successfully updated", 'status' => 1, 'data' => array()], 200);
		}
	}

	function flagDocAjaxList(Request $request){
		$data['user'] = auth()->user();
		if (Common::checkAgencyLogin()) {
		
			$query = $this->flagMarkedService->getFlagAllDocumentByPatientIdAgency('Document');
		} else {
			$query = $this->flagMarkedService->getFlagAllDocumentByPatientId('Document');
		}

		if(!empty($query[0])){
			foreach($query as $val){
				$serviceArray = [];
				$services = $this->documentUploadService->getDocumentListByDocumentId($val->id);
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
		$data['query'] = $query;		
		return view('flagAppointment._partial.flag_document_ajax_list',$data);
	}

	function flagNotesAjaxList(Request $request){
		$data['user'] = auth()->user();
		$query = $this->flagMarkedService->getAllFlagData('Notes');		
		$data['query'] = $query;
		return view('flagAppointment._partial.flag_notes_ajax_list',$data);
	}

	function flagTaskAjaxList(Request $request){
		$data['user'] = auth()->user();
		$query = $this->flagMarkedService->getFlagTaskData('Task');		
		$data['query'] = $query;
		return view('flagAppointment._partial.flag_task_ajax_list',$data);
	}

	function flagMarkAsRead(Request $request){
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
		
			$this->flagMarkedService->update(array('is_flag_read'=>1,'reason' => request('reason')),array('id'=>$request->id));

			return response()->json(['error_msg' => "Mark as read successfully", 'status' => 1, 'data' => array()], 200);
		}
	}
}