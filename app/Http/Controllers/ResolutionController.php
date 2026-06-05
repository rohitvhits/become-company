<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use App\Helpers\ResolutionSmsHelper;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use App\Services\ResolutionService;
use App\Services\LogsService;
use App\Services\PatientServicesRequest;
use App\Services\PatientNotesService;
use App\Services\DocumentPatientService;
use App\Services\PatientWiseServicesRequests;
use App\Agency;
use App\Services\SendEmailNotificationSerivce;
use App\Services\SmsService;
class ResolutionController extends BaseController
{

	protected $patientService;
	protected $resolutionService;
	protected $patientServicesRequest;
	protected $patientNotesService;
	protected $documentPatientService;
	protected $patientWiseServicesRequests;
	protected $sendEmailNotificationSerivce;
    protected $smsService;

	public function __construct(PatientService $patientService, ResolutionService $resolutionService, PatientServicesRequest $patientServicesRequest, PatientNotesService $patientNotesService, DocumentPatientService $documentPatientService, PatientWiseServicesRequests $patientWiseServicesRequests, sendEmailNotificationSerivce $sendEmailNotificationSerivce,SmsService $smsService)
	{
		$this->middleware('auth');
		$this->middleware('permission:resolution-chart', ['only' => ['saveResolutionData', 'getResolutionData','statusUpdateResolution']]);
		$this->patientService = $patientService;
		$this->resolutionService = $resolutionService;
		$this->patientServicesRequest = $patientServicesRequest;
		$this->patientNotesService = $patientNotesService;
		$this->documentPatientService = $documentPatientService;
		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
		$this->sendEmailNotificationSerivce = $sendEmailNotificationSerivce;
        $this->smsService = $smsService;
	}

	public function saveResolutionData(Request $request){
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
		} else {
			$statusUpdate = '';
			if(!empty($request->resolution)){
				$status = Utility::getStatusData();
				if(in_array($request->resolution,$status)){
					$resolutionData = array(
						'status' => $request->resolution,
						'patient_id' => $request->id,
						'refuse_reason' => $request->refuse_reason,
						'cancel_reason' => $request->cancel_reason,
						'notes' => $request->notes,
						'service_request_id' => $request->services_requested_id,
						'services' => $request->services,
						'other_cancel_reason' => $request->other_cancel_reason,
						'other_refuse_reason' => $request->other_refuse_reason,
					);
					$statusUpdate = $this->statusUpdateResolution($resolutionData);
				}
			}
			$resolutionStatus = $request->resolution;
			if($resolutionStatus == 'Unable To Contact'){
				$resolutionStatus = "unableToContact";
			}
			$resData = array(
				'patient_id' => $request->id,
				'team' => $request->team,
				'resolution' => $resolutionStatus,
				'cancel_reason' => $request->cancel_reason,
				'refuse_reason' => $request->refuse_reason,
				'notes' => $request->notes,
				'service_request_id' => isset($request->services_requested_id) && $request->services_requested_id != "" ? $request->services_requested_id : $statusUpdate['requested_id'],
				'other_cancel_reason' => $request->other_cancel_reason,
				'other_refuse_reason' => $request->other_refuse_reason
			);
			$this->resolutionService->save($resData);
            $patientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->id);
			$service_ids = explode(',',$patientDetails->service_id);
			if( strtolower($request->resolution) != 'booked' || ResolutionSmsHelper::getMDOServiceIds($service_ids))
			{
				if($patientDetails->medication_count == 0 && ($patientDetails->no_medication_taken != 1 || is_null($patientDetails->no_medication_taken))){
					$statusWiseSmsSend = ResolutionSmsHelper::statusWiseSmsSend($request->resolution,$request->id);
					if(!empty($statusWiseSmsSend['message'])){
						$numbers = array_unique(array_filter([$statusWiseSmsSend['mobile'], $statusWiseSmsSend['phone']]));
						foreach($numbers as $number){
							$this->smsService->AgencyWiseSmsDynamic($request->id, $number, $statusWiseSmsSend['message']);
						}
					}
				}
			}

			$ipaddress = Utility::getIP();
			$data['id'] = $request->id;
			$insertLog = [
				'type' => 'Saved resolution data',
				'link' => url('/save-resolution-data'),
				'module' => 'Patient Appointment',
				'object_id' => $request->id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Saved Resolution data.',
				'new_response' => serialize($resData),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			return response()->json(['status' => true, 'error_msg' => 'Resolution data has been added successfully.','data' => array('status' => $statusUpdate['status'])], 200);
		}
	}

	public function getResolutionData(Request $request){
		$data['query'] = $this->resolutionService->getAllList($request->id);
		return view('patient._partial.resolution-log.resolution_log_ajax_list',$data);
	}

	public function statusUpdateResolution($data){
		$status = $data['status'];
		$user = auth()->user();
		$getOldResponse = $this->patientService->getDetailById($data['patient_id']);
		$oldArray = array();
		$oldArray['oldresponse'] = $getOldResponse;
		if ($status == 'Pending') {
			$data_array = array(
				'status' => $status,
				'appointment_date' => '',
			);
		}
		if ($status == 'Booked') {
			$data_array = array(
				'status' => "Booked",
				'appointment_added_by' => $user['id'],
				'appointment_added_created_date' => date('Y-m-d H:i:s'),
				'booked_date' => date('Y-m-d H:i:s'),
				'booked_by' => $user['id']
			);
		}
		if ($status == 'Telehealth Completed') {
			$data_array = array(
				'status' => "Telehealth Completed",
				'patient_sms_flag' => 1,
				'completed_date' => date('Y-m-d H:i:s'),
				'completed_by' => $user['id'],

			);
		}
		if ($status == 'Hospitalised / In Rehab') {
			$data_array = array(
				'status' => 'hospitalized/rehab',

			);
		}

		if ($status == 'Unable To Contact') {
			$data_array = array(
				'status' => 'unableToContact',

			);
		}

		if ($status == '1st Attempt - Unable to Contact' || $status == '2nd Attempt - Unable to Contact' || $status == '3rd Attempt - Unable to Contact' || $status == 'Patient Deceased' || $status == 'Signed' || $status == 'Signed & Sent Back to the Agency' || $status == 'Telehealth Completed , Pending Forms' || $status == 'Appointment was missed' || $status == 'Patient Asked to Reschedule' || $status == 'Appointment Missed' || $status == 'New Order Received' || $status == 'New Form Requested' || $status == 'Form Completed' || $status == 'Service Provided' || $status == 'Closed Temporarily') {
			$data_array = array(
				'status' => $status,
			);
		}
		if ($status == 'Cancelled') {
			$data_array = array(
				'status' => 'cancelled',
				'reason_id' => $data['cancel_reason'],
				'other_reason' => !empty($data['other_cancel_reason']) ? $data['other_cancel_reason']:null,
				'cancel_date' => date('Y-m-d H:i:s'),
				'cancel_by' => $user['id']
			);
		}

		if ($status == 'Processing') {
			$data_array = array(
				'status' => 'processing',

			);
		}
		if ($status == 'Refused') {
			$data_array = array(
				'status' => 'refused',
				'reason_id' => $data['refuse_reason'],
				'other_reason' => !empty($data['other_refuse_reason']) ? $data['other_refuse_reason']:null,
			);
		}

		if(isset($data['notes']) && !empty($data['notes'])){
			$data_array['notes'] = $data['notes'];
		}
		if(isset($data['services']) && !empty($data['services'])){
			$data_array['service_id'] = implode(',',$data['services']);
		}

		$data_array['reason_id'] = null;
		$data_array['other_reason'] = null;
		if(!empty($data['cancel_reason'])){
			$data_array['reason_id'] = $data['cancel_reason'];
			$data_array['other_reason'] = !empty($data['other_cancel_reason'])?$data['other_cancel_reason']:null;
		}elseif(!empty($data['refuse_reason'])){
			$data_array['reason_id'] = $data['refuse_reason'];
			$data_array['other_reason'] = !empty($data['other_refuse_reason'])?$data['other_refuse_reason']:null;
		}

		$data_array['prev_status'] = $getOldResponse->status ?? "";
		$data_array['last_status_update'] = date('Y-m-d H:i:s');
		$data_array['last_status_update_by'] = auth()->user()->id;

		$getExistingRecord = $this->patientService->getDetailById($data['patient_id']);
		$this->patientService->update($data_array, array('id' => $data['patient_id']));

		$res_service_request_id = $this->saveResolutionServiceRequest($data['patient_id'], $data_array['status'],$data['service_request_id'],$data['services']);
		// Store notes on notes section too,
		if(isset($data['notes']) && !empty($data['notes'])){
			$this->storeNotes($data);
		}
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Status Appointment',
			'link' => url('/patient/statusUpdate/"' . $data['patient_id'] . '"'),
			'module' => 'Patient Appointment',
			'object_id' => $data['patient_id'],
			'message' => $user->first_name . ' ' . $user->last_name . ' has ' . $data_array['status'] . ' Updated Appointment',
			'new_response' => serialize($data_array),
			'old_response' => serialize($getExistingRecord->toArray()),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		$statusArray = Utility::getPatientStatusData();
		$notiStatus = array_search($status, $statusArray);
		try {
			$this->sendEmailNotification($status,$getOldResponse,$notiStatus);
		} catch (\Exception $e) {

		}
		return array('status' => $data_array['status'], 'requested_id' => $res_service_request_id);
	}

	public function saveResolutionServiceRequest($id, $status,$service_requested_id,$services)
	{
		$res_service_request_id = "";
		$auth = auth()->user();
		$checkServices = $this->patientServicesRequest->getPatientService($id);

		if (count($checkServices) > 0) {
			$getExistingRecord = $this->patientService->getDetailByIdNew($id);
			$completedDate = NULL;
			$completed_by = "";
			$statusName = $status;

			if ($status == 'booked') {
				$statusName = "Booked";
			}
			if ($status == 'complete') {
				$statusName = "completed";
			}
			if ($status == 'missed') {
				$statusName = "missed";
			}
			if ($status == 'hospitalized') {
				$statusName = "hospitalized/rehab";
			}

			if ($status == 'unableToContact') {
				$statusName = "unableToContact";
			}
			if ($status == 'cancelled') {
				$statusName = "cancelled";
			}

			if ($status == 'noshow') {
				$statusName = "noshow";
			}

			if ($status == 'checkin') {
				$statusName = "arrived";
			}
			if ($status == 'processing') {
				$statusName = "processing";
			}
			if ($status == 'refused') {
				$statusName = "refused";
			}

			if ($status == 'pending') {
				$statusName = "pending";
			}

			if ($status == 'PendingTermination') {
				$statusName = "Pending Termination";
			}


			if ($status == 'Onhold') {
				$statusName = "On Hold";
			}

			if ($status == 'Onleave') {
				$statusName = "On Leave";
			}

			if ($status == 'Terminated') {
				$statusName = "Terminated";
			}
			if ($status == 'complete') {
				$completedDate = date('Y-m-d H:i:s');
				$completed_by = $auth->id;
			}
			if ($status == '1st Attempt - Unable to Contact' || $status == '2nd Attempt - Unable to Contact' || $status == '3rd Attempt - Unable to Contact' || $status == 'Patient Deceased' || $status == 'Signed' || $status == 'Signed & Sent Back to the Agency' || $status == 'Telehealth Completed , Pending Forms' || $status == 'Appointment was missed' || $status == 'Patient Asked to Reschedule'|| $status == 'Appointment Missed' || $status == 'New Order Received' || $status == 'New Form Requested' || $status == "Form Completed" || $status == "Service Provided" || $status == "Closed Temporarily") {
				$statusName = $status;
			}
			$updateStatus = array(
				'status' => $statusName,
				'completed_date' => $completedDate,
				'completed_by' => $completed_by,
				'last_status_update_by' => $auth->id,
				'last_status_update' => date('Y-m-d H:i:s')
			);

			if ($status == 'refused' || $status == 'cancelled') {
				$updateStatus['reason_id'] = $getExistingRecord->reason_id;
				$updateStatus['other_reason'] = !empty($getExistingRecord->other_reason)?$getExistingRecord->other_reason:null;
			}else{
				$updateStatus['reason_id'] = null;
				$updateStatus['other_reason'] = null;
			}
			if(isset($service_requested_id) && !empty($service_requested_id)){
				$getLastServiceId = $service_requested_id;
				$oldServiceRequestRes = $this->patientServicesRequest->patientServiceRequestData($getLastServiceId);
				$this->patientServicesRequest->update($updateStatus, array('id' => $getLastServiceId));
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'update service requested from resolution',
					'link' => url('/save-patient-type-wise-services'),
					'module' => 'Patient Appointment',
					'object_id' =>$id,
					'message' => $auth->first_name . ' ' . $auth->last_name . ' has updated service requested status from resolution',
					'old_response' => serialize($oldServiceRequestRes),
					'new_response' => serialize(array('service_id'=>$getLastServiceId,'status'=>$updateStatus)),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
			}
			if(isset($services) && !empty($services)){
				$res_service_request_id = $this->saveServiceRequested($services,$id,$updateStatus);
			}
		} else {
			$checkForDocument = $this->documentPatientService->getDetailsByPatientId(array($id));
			$getPatientServices = $this->patientService->getPatientId($id);
			$servicesId = explode(',', $getPatientServices->service_id);

			if (!empty($servicesId[0])) {
				$finalData = [
					'patient_id' => $id,
					'fu_date' => $getPatientServices->fu_date,
					'due_date' => $getPatientServices->due_date
				];

				if (!empty($checkForDocument[0])) {
					$finalData['created_at'] = date('Y-m-d H:i:s', strtotime('-1 day'));
					$finalData['updated_flag'] = "1";
					$finalData['flag'] = "1";
				} else {
					$finalData['flag'] = "2";
				}

				$patientServiceLastId = $this->patientServicesRequest->save($finalData);
				if ($patientServiceLastId) {

					foreach ($servicesId as $serviceId) {
						$patientWiseServiceRequest = [
							'patient_id' => $id,
							'service_id' => $serviceId,
							'patient_service_request_id' => $patientServiceLastId,
							'deleted_by' => 3
						];
						$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
					}
				}
			}
			$res_service_request_id = $patientServiceLastId;
		}
		return $res_service_request_id;
	}

	public function storeNotes($data){
		$message = $data['notes'];
		$auth = auth()->user();
		$type = 'Agency';
		$callFlags = 'Normal';
		$query = $this->patientService->getDetailById($data['patient_id']);
		$agency_id_main = $query->agency_id;
		$update = $this->patientNotesService->save(array('patient_id' => $data['patient_id'], 'created_by' => $auth['id'], 'type' => $type, 'message' => $message, 'receiver_id' => $agency_id_main, 'call_flag' => $callFlags));

		if ($update) {
			$pid = $data['patient_id'];
			$message = $data['notes'];
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Notes Added from Resolution chart',
				'link' => url('/patient/view/') . '/' . $pid,
				'module' => 'Patient Appointment',
				'object_id' => $pid,
				'message' => $auth->first_name . ' ' . $auth->last_name . ' has Notification Sent To Agency',
				'new_response' => serialize(array('patient_id' => $pid, 'created_by' => $auth['id'], 'type' => $type, 'message' => $message, 'receiver_id' => $agency_id_main)),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return 1;
		} else {
			return 0;
		}
	}

	public function saveServiceRequested($services,$patient_id,$status) {
		$user = auth()->user();
		$addServiceIds = $services;
		$patientServiceLastId = $this->patientServicesRequest->save([
			'patient_id' => $patient_id,
			'status' => $status['status'],
		]);
		if (is_array($addServiceIds)) {
			foreach ($addServiceIds as $serviceId) {
				$patientWiseServiceRequest = [
					'patient_id' => $patient_id,
					'service_id' => $serviceId,
					'patient_service_request_id' => $patientServiceLastId,
					'status' => $status['status'],
					'last_status_update_by' => $status['last_status_update_by'],
					'last_status_update' => $status['last_status_update']
				];
				$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
			}
		}
		if($patientServiceLastId){
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Add Services',
				'link' => url('/save-patient-type-wise-services'),
				'module' => 'Patient Appointment',
				'object_id' =>$patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has added new services',
				'new_response' => serialize($services),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return $patientServiceLastId;
		}
	}

	public function resolutionSaveServiceRequested(Request $request){
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'status' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else{
			$status = $request->status;
			if ($status == 'Cancelled') {
				$status = 'cancelled';
			}
			if ($status == 'Scheduled') {
				$status = 'booked';
			}

			if ($status == 'Refused') {
				$status = 'refused';

			}
			if ($status == '1st Attempt - Unable to Contact') {
				$status = '1st Attempt - Unable to Contact';

			}
			if ($status == '2nd Attempt - Unable to Contact') {
				$status = '2nd Attempt - Unable to Contact';

			}
			if ($status == '3rd Attempt - Unable to Contact') {
				$status = '3rd Attempt - Unable to Contact';

			}
			if ($status == 'Patient Deceased') {
				$status = 'Patient Deceased';

			}
			if ($status == 'Telehealth Completed') {
				$status = 'Telehealth Completed';

			}
			if ($status == 'Hospitalised / In Rehab') {
				$status = 'hospitalized/rehab';

			}
			if ($status == 'Processing') {
				$status = 'processing';

			}
			if ($status == 'Signed') {
				$status = 'Signed';

			}

			if ($status == 'Signed & Sent Back to the Agency') {
				$status = 'Signed & Sent Back to the Agency';

			}

			if ($status == 'Booked') {
				$status = 'Booked';

			}

			if ($status == 'Form Completed') {
				$status = 'Form Completed';
				$data['completed_date'] = date('Y-m-d H:i:s');
				$data['completed_by'] = Auth()->user()->id;
			}

			if ($status == 'Telehealth Completed , Pending Forms') {
				$status = 'Telehealth Completed , Pending Forms';

			}

			if ($status == 'Appointment Missed') {
				$status = 'Appointment Missed';

			}

			if($status == 'Patient Asked to Reschedule'){
				$status = 'Patient Asked to Reschedule';
			}

			if($status =='Service Provided'){
				$status = 'Service Provided';
			}

			if($status =='Unable To Contact'){
				$status = 'unableToContact';
			}

			if($status == 'Closed Temporarily'){
				$status = 'Closed Temporarily';
			}

			$data['status'] = $status;
			$data['last_status_update'] =date('Y-m-d H:i:s');
			$data['last_status_update_by'] = Auth()->user()->id;

			$data['reason_id'] = null;
			$data['other_reason'] = null;
			if(!empty($request->cancel_reason)){
				$data['reason_id'] = $request->cancel_reason;
				$data['other_reason'] = !empty($request->other_cancel_reason) ?$request->other_cancel_reason:null;
			}else if(!empty($request->refuse_reason)){
				$data['reason_id'] = $request->refuse_reason;
				$data['other_reason'] = !empty($request->other_refuse_reason) ?$request->other_refuse_reason:null;
			}
			$this->patientServicesRequest->update($data,array('id'=>$request->serviceId));

			$getDetails = $this->patientServicesRequest->getByPatientDetails($request->serviceId);
			$statusUpdate = 0;

			if(isset($getDetails->id)){
				$last_record_id = $this->patientServicesRequest->lastServiceRequestedByPatientId($getDetails->patient_id);
				if(isset($last_record_id->id)){
					if($last_record_id->id ==$request->serviceId){
						$getOldResponse = $this->patientService->getDetailById($getDetails->patient_id);
						$data_array = array(
							'status' => $status,
							'last_status_update' => date('Y-m-d H:i:s'),
							'last_status_update_by' => Auth()->user()->id,
						);
						$data_array['reason_id'] = null;
						$data_array['other_reason'] = null;
						if ($status == 'refused' || $status == 'cancelled') {
							$data_array['reason_id'] = $data['reason_id'];
							$data_array['other_reason'] = !empty($data['other_reason']) ? $data['other_reason'] : null;
						}
						$this->patientService->update($data_array, array('id' => $getDetails->patient_id));

						$getNewResponse = $this->patientService->getDetailById($getDetails->patient_id);
						$ipaddress = Utility::getIP();
						$insertLog = [
							'type' => 'Portal status updated from resolution',
							'link' => url('/patient/statusUpdate/').'/'.$getDetails->patient_id,
							'module' => 'Patient Appointment',
							'object_id' => $getDetails->patient_id,
							'message' => $user->first_name . ' ' . $user->last_name . ' has ' . $data_array['status'] . ' Updated Appointment from resolution',
							'new_response' => serialize($getNewResponse),
							'old_response' => serialize($getOldResponse),
							'ip' => $ipaddress,
						];
						LogsService::save($insertLog);

						$statusUpdate = 1;
					}
				}
			}

			$dataResponse = ['status'=>$statusUpdate];
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Service status updated from resolution',
				'link' => url('/save-pateint-service-requested'),
				'module' => 'Patient Appointment',
				'object_id' => $getDetails->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has updated service status to '.$status.' from resolution',
				'new_response' => serialize($data),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			$resolutionStatus = $request->status;
			if($resolutionStatus == 'Unable To Contact'){
				$resolutionStatus = "unableToContact";
			}
			$resData = array(
				'patient_id' => $getDetails->patient_id,
				'team' => $request->team,
				'resolution' => $resolutionStatus,
				'cancel_reason' => $request->cancel_reason??'',
				'refuse_reason' => $request->refuse_reason??'',
				'notes' => $request->notes??'',
				'service_request_id' => $request->serviceId,
				'other_cancel_reason' => $request->other_cancel_reason,
				'other_refuse_reason' => $request->other_refuse_reason,

			);
			$this->resolutionService->save($resData);
			$ipaddress = Utility::getIP();
			$data['id'] = $request->id;
			$insertLog = [
				'type' => 'Saved resolution data for services',
				'link' => url('/save-pateint-service-requested'),
				'module' => 'Patient Appointment',
				'object_id' => $request->id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Saved Resolution data for services.',
				'new_response' => serialize($resData),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return response()->json(['success' => true, 'data' => $dataResponse, 'error_msg' => 'Patient Service Status Updated successfully.'], 200);
		}
	}

	public function sendEmailNotification($status,$response,$notiStatus){
		$querys = Agency::getAllDetailsbyAgencyId($response->agency_id);
		$subject = 'Notification from Ny Best Patient status has changed to ' . $status;
		$notificationType = "Status Update";
		$email_data = array(
			'agency_name' => $querys->agency_name != '' ? $querys->agency_name : '-',
			'portal_id' => $response->id,
			'type' => $response->type,
			'localdetails' => '-',
			'first_name' => $response->first_name,
			'last_name' => $response->last_name,
			'status' => $status
		);
		$messages = Utility::getHtmlContent('email_template.status_update', $email_data);
		$this->sendEmailNotificationSerivce->sendPatientNotification($notificationType,$response->agency_id,$subject,$messages,"","",$response->id,$notiStatus);
	}
}