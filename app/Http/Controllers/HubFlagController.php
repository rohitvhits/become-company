<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\HubRecordService;
use App\Services\HubLogsService;
use Illuminate\Support\Facades\Cache;
use App\Model\HubCompany;
use App\Services\AgencyWiseServiceService;
use App\Helpers\UserHelper;
use App\Services\HubTaskService as TaskService;
use App\Services\HubRecordNotesService;
use App\Services\HubRecordDocService;
use App\Services\DocumentUploadService;
use App\Services\NotificationUserService;
use App\Helpers\Utility;
use App\Services\HubFlagMarkedService;

class HubFlagController extends BaseController
{

	protected $hubRecordService, $agencyWiseServiceService, $taskService, $hubRecordNotesService, $hubRecordDocService, $documentUploadService, $notificationUserService, $flagMarkedService = "";

	public function __construct(HubRecordService $hubRecordService, AgencyWiseServiceService $agencyWiseServiceService, TaskService $taskService, HubRecordNotesService $hubRecordNotesService, HubRecordDocService $hubRecordDocService, DocumentUploadService $documentUploadService, NotificationUserService $notificationUserService, HubFlagMarkedService $flagMarkedService)
	{
		$this->middleware('permission:hub-record-flagged-menu', ['only' => ['flagList', 'flagAppointmentAjaxList']]);
		$this->hubRecordService = $hubRecordService;
		$this->agencyWiseServiceService = $agencyWiseServiceService;
		$this->taskService = $taskService;
		$this->hubRecordNotesService = $hubRecordNotesService;
		$this->hubRecordDocService = $hubRecordDocService;
		$this->documentUploadService = $documentUploadService;
		$this->notificationUserService = $notificationUserService;
		$this->flagMarkedService = $flagMarkedService;
	}

	function changeStatusFlag(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {

			$getOldResponse = $this->hubRecordService->getDetailById($request->id);
			if ($getOldResponse->flag == 1) {
				$this->hubRecordService->update(array('flag' => 0, 'reason' => request('reason')), array('id' => $request->id));
				$title = 'Flagged';
			} else {
				$this->hubRecordService->update(array('flag' => 1, 'reason' => request('reason')), array('id' => $request->id));
				$title = 'Flag';
			}
			$msg = 'Hub Record flag is changed to ' . $title;
			$getNewResponse = $this->hubRecordService->getDetailById($request->id);

			$insertLogs = [
				'title' => $title,
				'marked_at' => date('Y-m-d h:i:s'),
				'type' => 'Hub Record',
				'record_id' => $request->id,
				'reason' => request('reason')
			];
			$this->flagMarkedService->save($insertLogs);

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Hub Record flag is changed to ' . $title,
				'link' => url('/hub-record/view') . '/' . $request->id,
				'module' => 'Hub Record',
				'object_id' => $request->id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update appointment flag.',
				'old_response' => serialize($getOldResponse),
				'new_response' => serialize($getNewResponse),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);

			return response()->json(['error_msg' => $msg, 'status' => 1, 'data' => array()], 201);
		}
	}

	function flagList()
	{
		$data['user'] = $user = auth()->user();
		$data['agency_fk'] = request('agency_fk');
		if (auth()->user()->agency_fk != "") {
			abort(403);
		}
		if (in_array(auth()->user()->id, Utility::agencyPortalRolePermission())) {
			abort(404);
		}
		$angecyList = Cache::get('patient_master_locations', function () {
			return HubCompany::getAgencyListHub();
		}, 10);
		$data['agencyList'] = $angecyList;
		$data['agency_user_list'] =  Cache::get('agency_user_list', function ()  use ($user) {
			return UserHelper::getAgencyWiseUserList($user->agency_fk);
		}, 10 * 60);
		$data['statuses'] = Utility::getUniqueStatusDataNew();
		return view('flagHub.flag_list', $data);
	}

	function flagAppointmentAjaxList(Request $request)
	{
		$data['user'] = auth()->user();
		$data['query'] = $query = $this->flagMarkedService->getALLHubData($request->all(), 'Hub Record');

		$data['query'] = $query;
		return view('flagHub.flag_ajax_list', $data);
	}

	function changeDocStatusFlag(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getOldResponse =  $this->hubRecordDocService->getDetailsById($request->id);
			if ($getOldResponse->flag == 1) {
				$this->hubRecordDocService->update(array('flag' => 0, 'reason' => request('reason')), array('id' => $request->id));
				$title = 'Flag';
			} else {
				$this->hubRecordDocService->update(array('flag' => 1, 'reason' => request('reason')), array('id' => $request->id));
				$title = 'Flagged';
			}
			$getNewResponse =  $this->hubRecordDocService->getDetailsById($request->id);
			//get patient data
			$patientData = $this->hubRecordService->getDetailById($getNewResponse->hub_record_id);

			$insertLogs = [
				'title' => $title,
				'marked_at' => date('Y-m-d h:i:s'),
				'type' => 'Document',
				'record_id' => $request->id,
				'reason' => request('reason')
			];
			$this->flagMarkedService->save($insertLogs);

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Update Document flag is changed to ' . $title,
				'link' => url('/hub-record/view') . '/' . $patientData->id,
				'module' => 'Hub Record',
				'object_id' => $getOldResponse->hub_record_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update document flag',
				'old_response' => serialize($getOldResponse),
				'new_response' => serialize($getNewResponse),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);

			return response()->json(['error_msg' => "Document flag successfully updated", 'status' => 1, 'data' => array()], 200);
		}
	}

	function changeNotesStatusFlag(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getOldResponse = $this->hubRecordNotesService->getById($request->id);

			if ($getOldResponse->flag == 1) {
				$this->hubRecordNotesService->update(array('flag' => 0, 'reason' => request('reason')), array('id' => $request->id));
				$title = 'Flag';
			} else {
				$this->hubRecordNotesService->update(array('flag' => 1, 'reason' => request('reason')), array('id' => $request->id));
				$title = 'Flagged';
			}

			$getNewResponse = $this->hubRecordNotesService->getById($request->id);
			//get patient data
			$patientData = $this->hubRecordService->getDetailById($getOldResponse->hub_record_id);

			$insertLogs = [
				'title' => $title,
				'marked_at' => date('Y-m-d h:i:s'),
				'type' => 'Hub Record Notes',
				'record_id' => $request->id,
				'reason' => request('reason')
			];
			$this->flagMarkedService->save($insertLogs);

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Update Notes flag is changed to ' . $title,
				'link' => url('/hub-record/view') . '/' . $patientData->id,
				'module' => 'Hub Record',
				'object_id' => $getOldResponse->hub_record_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update notes flag',
				'old_response' => serialize($getOldResponse),
				'new_response' => serialize($getNewResponse),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);

			return response()->json(['error_msg' => "Notes flag successfully updated", 'status' => 1, 'data' => array('flag' => $getNewResponse->flag)], 200);
		}
	}

	function flagDocAjaxList(Request $request)
	{
		$data['user'] = auth()->user();

		$query = $this->flagMarkedService->getFlagAllDocumentByPatientId('Document');


		$data['query'] = $query;
		return view('flagHub._partial.flag_document_ajax_list', $data);
	}

	function flagNotesAjaxList(Request $request)
	{
		$data['user'] = auth()->user();
		$query = $this->flagMarkedService->getAllFlagData('Hub Record Notes');

		$data['query'] = $query;
		return view('flagHub._partial.flag_notes_ajax_list', $data);
	}

	function flagMarkAsRead(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {

			$this->flagMarkedService->update(array('is_flag_read' => 1), array('id' => $request->id));

			return response()->json(['error_msg' => "Mark as read successfully", 'status' => 1, 'data' => array()], 200);
		}
	}
	function changeTaskStatusFlag(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getOldResponse = $this->taskService->getDetailsByIdNew($request->id);
			if ($getOldResponse->flag == 1) {
				$this->taskService->update(array('flag' => 0, 'reason' => request('reason')), array('id' => $request->id));
				$title = 'Flag';
			} else {
				$this->taskService->update(array('flag' => 1, 'reason' => request('reason')), array('id' => $request->id));
				$title = 'Flagged';
			}
			$getNewResponse = $this->taskService->getDetailsByIdNew($request->id);

			$insertLogs = [
				'title' => $title,
				'marked_at' => date('Y-m-d h:i:s'),
				'type' => 'Task',
				'record_id' => $request->record_id ? $request->record_id : $request->id,
				'reason' => request('reason')
			];
			$this->flagMarkedService->save($insertLogs);

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Updated Task Flag',
				'link' => url('/hub-record/view') . '/' . $getOldResponse->record_id,
				'module' => 'Hub Record',
				'object_id' => $getOldResponse->record_id ? $getOldResponse->record_id : $request->id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update task flag',
				'old_response' => serialize($getOldResponse),
				'new_response' => serialize($getNewResponse),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);

			return response()->json(['error_msg' => "Task flag successfully updated", 'status' => 1, 'data' => array()], 200);
		}
	}

	function flagTaskAjaxList(Request $request)
	{
		$data['user'] = auth()->user();
		$query = $this->flagMarkedService->getFlagTaskData('Task');
		$data['query'] = $query;
		return view('flagHub._partial.flag_task_ajax_list', $data);
	}
}
