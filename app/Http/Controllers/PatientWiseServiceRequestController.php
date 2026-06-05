<?php

namespace App\Http\Controllers;

use App\Agency;
use App\Helpers\UserHelper;
use App\Model\Patient;
use App\Model\PatientWiseServiceEmail;
use Illuminate\Http\Request;
use App\Services\PatientWiseServicesRequests;
use App\Services\LocationMasterService;
use App\Services\LocationScheduleService;
use App\Services\AgencyWiseServiceService;
use App\Services\PatientServicesRequest;
use App\Services\LogsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\URL;

use App\Master;
use App\Services\AppointmentService;
use App\Services\PatientWiseEmailService;
use App\Services\PatientService;
use Illuminate\Support\Facades\Mail;
use App\Services\PatientSMSLogService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use App\Services\DoctorService;
use App\Services\PatientServiceRequestLog;
use App\User;
use Illuminate\Support\Facades\Cache;
use App\Model\Language;
use App\Jobs\ProcessWebhookJob;
use App\Services\AgencyWiseSMSNotificationService;
use App\Services\DocumentPatientService;
use Exception;
use App\Helpers\Common;
use App\Services\BranchListService;
use App\Services\AppointmentPortalMergeLogsService;
use App\Services\AgencyService;
use App\Helpers\MergeUtilityHelper;

class PatientWiseServiceRequestController extends Controller
{

	private const ERROR_MESSAGE = "Sorry, something went wrong. Please try again";
	private const MODULE_NAME = "Patient Appointment";
	protected $LocationMasterService, $LocationScheduleService, $patientServicesRequest, $patientWiseServicesRequests, $agencyWiseServiceService, $appointmentService, $patientWiseEmailService, $patientService, $patientSMSLogService, $smsService, $doctorService, $agencyWiseSMSNotificationService, $documentPatientService,$branchListService,$agencyService = "";
	protected $appointmentMergeLogsService;

	public function __construct(PatientWiseServicesRequests $patientWiseServicesRequests, LocationMasterService $LocationMasterService, LocationScheduleService $LocationScheduleService, AgencyWiseServiceService $agencyWiseServiceService, PatientServicesRequest $patientServicesRequest, AppointmentService $appointmentService, PatientWiseEmailService $patientWiseEmailService, PatientService $patientService, PatientSMSLogService $patientSMSLogService, SmsService $smsService, DoctorService $doctorService, AgencyWiseSMSNotificationService $agencyWiseSMSNotificationService, DocumentPatientService $documentPatientService, BranchListService $branchListService, AppointmentPortalMergeLogsService $appointmentMergeLogsService, AgencyService $agencyService)
	{
		$this->middleware('auth');
		$this->middleware('permission:service-request-list', ['only' => ['patientServiceRequestedList', 'patientServiceRequestedAjaxList', 'hubPatientServiceRequestedList', 'hubPatientServiceRequestedAjaxList']]);

		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
		$this->LocationMasterService = $LocationMasterService;
		$this->LocationScheduleService = $LocationScheduleService;
		$this->agencyWiseServiceService	= $agencyWiseServiceService;
		$this->patientServicesRequest = $patientServicesRequest;

		$this->appointmentService = $appointmentService;
		$this->patientWiseEmailService = $patientWiseEmailService;
		$this->patientService = $patientService;
		$this->patientSMSLogService = $patientSMSLogService;
		$this->smsService = $smsService;
		$this->doctorService = $doctorService;
		$this->agencyWiseSMSNotificationService = $agencyWiseSMSNotificationService;
		$this->documentPatientService = $documentPatientService;
		$this->appointmentMergeLogsService = $appointmentMergeLogsService;
		$this->branchListService = $branchListService;
		$this->agencyService = $agencyService;
	}

	public function savePatientTypeWiseServices(Request $request)
	{

		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'service_id' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$addServiceIds = $request->input('service_id');
			$serviceRequestStatus = 'Pending';
			if ($request->portal_type == 'Patient') {
				$serviceRequestStatus = Utility::getStatusFromServiceId($addServiceIds);
			}
			$patientServiceLastId = $this->patientServicesRequest->save([
				'patient_id' => $request->input('patient_id'),
				'due_date' => date('Y-m-d', strtotime($request->service_due_date)),
				'follow_up_date' => date('Y-m-d', strtotime($request->service_follow_date)),
				'status' => $serviceRequestStatus,
			]);

			if (is_array($addServiceIds)) {
				foreach ($addServiceIds as $serviceId) {
					$patientWiseServiceRequest = [
						'patient_id' => $request->input('patient_id'),
						'service_id' => $serviceId,
						'patient_service_request_id' => $patientServiceLastId,
					];

					$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
				}
			}

			if ($patientServiceLastId) {
				$getServiceName = Master::getRecordById($addServiceIds);
				$serviceName = "";
				if (count($getServiceName) > 0) {
					$serviceNameArray = [];
					foreach ($getServiceName as $mtname) {
						$serviceNameArray[] = $mtname->name;
					}
					$serviceName = implode(',', $serviceNameArray);
				}
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add Services',
					'link' => url('/save-patient-type-wise-services'),
					'module' => self::MODULE_NAME,
					'object_id' => $request->input('patient_id'),
					'message' => $user->first_name . ' ' . $user->last_name . ' has added new services',
					'new_response' => serialize($request->all()),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);

				$query = $this->patientService->getDetailByIdEncrypt(sha1($request->input('patient_id')));
				if (isset($query->id)) {

					$oldData = $query->toArray();
					$branch_name = "";
					if(isset($request->branch_id) && !empty($request->branch_id)){
						$branchData = $this->branchListService->getById($request->branch_id);
						$branch_name = $branchData->branch_name;
					}

					// Check if agency has restrict_service_request_update enabled (only applies to agency users)
					$restrictUpdate = false;
					if (Auth()->user()->agency_fk != "" && isset($query->agency_id) && !empty($query->agency_id)) {
						$agencyData = $this->agencyService->getDetailsByAgencyId($query->agency_id);
						if (isset($agencyData->restrict_service_request_update) && $agencyData->restrict_service_request_update == 1) {
							$restrictUpdate = true;
						}
					}

					if ($restrictUpdate) {
						// Only update branch info, do NOT update status or service_id
						$updateData = array('last_status_update' => date('Y-m-d H:i:s'), 'last_status_update_by' => $user->id, 'branch_id' => $request->branch_id, 'location_branch' => !empty($branch_name) ? $branch_name : NULL);
					} else {
						$updateData = array('status' => $serviceRequestStatus, 'service_id' => implode(',', $addServiceIds), 'last_status_update' => date('Y-m-d H:i:s'), 'last_status_update_by' => $user->id, 'branch_id' => $request->branch_id, 'location_branch' => !empty($branch_name) ? $branch_name : NULL);
					}

					$this->patientService->update($updateData, array('id' => $query->id));
					$newResponse = $this->patientService->getDetailByIdEncrypt(sha1($request->input('patient_id')));
					if (!$restrictUpdate) {
					$insertLog = [
						'type' => 'Change Appointment Status',
						'link' => url('/save-patient-type-wise-services'),
						'module' => self::MODULE_NAME,
						'object_id' => $request->input('patient_id'),
						'message' => $user->first_name . ' ' . $user->last_name . ' has added new services',
						'old_response' => serialize($oldData),
						'new_response' => serialize($newResponse->toArray()),
						'ip' => $ipaddress,
					];
					LogsService::save($insertLog);
					}
					try {
						if ($request->portal_type == 'Patient' && !$restrictUpdate) {
							Utility::saveResolutionLogForms($serviceRequestStatus, $patientServiceLastId, $query->id);
						}
					} catch (Exception $e) {
					}

					try {
					if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
							$agencyNotifyData = array(
								'agencyid' => $query->agency_id,
								'title' => 'Added Service request',
								'record_id' => $query->id,
								'record_type' => 'Appointment',
								'msg' => '',
								'res_data' => serialize($query)
							);
							Common::insertAgencyNotificationsOfUser($agencyNotifyData);
						}
					} catch (\Throwable $th) {}
				}
				return response()->json(['success' => true, 'data' => ['service_name' => $serviceName, 'status' => $serviceRequestStatus,'branch_name' => $branch_name,'branch_id' => $request->branch_id,'restrictUpdate' => $restrictUpdate], 'error_msg' => 'Patient Service successfully requested.'], 200);
			} else {
				return response()->json(['success' => false, 'data' => [], 'error_msg' => self::ERROR_MESSAGE, 500]);
			}
		}
	}
	public function serviceRequestedList(Request $request)
	{
		$patientIds[] = $request->patient_id;
	
		$data['record_md_orders'] =$this->patientService->getDetailByIdNew($request->patient_id);
		
		$mergeAppointmentid = '';
		if (isset($data['record_md_orders']->merge_appointment_id) && $data['record_md_orders']->merge_appointment_id != "") {
			$patientIds =$this->convertMergePatientArray($data['record_md_orders']->merge_appointment_id,$request->patient_id);
			$mergeAppointmentid = implode(',',$patientIds);
		}

		$response = $this->patientServicesRequest->getAllServiceListAssigned($patientIds);
		if (!empty($response[0])) {
			foreach ($response as $val) {
				$reasonName = "";
				if ($val->reason_id != "") {
					$details  = Master::select('name')->where('id', $val->reason_id)->first();
					$reasonName = $details->name ?? "";
				}
				$val->reason_name = $reasonName;

				$val->documents = '';
				$val->status = ucfirst($val->status);
				$val->created_date = date('m/d/Y h:i A', strtotime($val->created_at));
				$val->user_details = $val->userDetailsWithTrashed;
				$val->merge_flag = 0;
				if($mergeAppointmentid !=""){
					$explodeData = explode(',',$mergeAppointmentid);
					if(!empty($explodeData[0])){
						if($request->patient_id != $val->patient_id){
							if(in_array($val->patient_id,$explodeData)){
								$val->merge_flag = 1;
							}
						}
						
					}
				}
			}
		}

		return response()->json(['status' => true, 'data' => $response, 'merge_appointment_id' => $mergeAppointmentid]);
	}

	public function getPatientWiseServices(Request $request)
	{
		$response = $this->patientWiseServicesRequests->patientWiseServices($request->patient_id);

		return response()->json(['success' => true, 'data' => $response]);
	}

	public function saveServiceEmail(Request $request)
	{
		$response = $this->patientWiseServicesRequests->patientWiseServiceEmailSave($request);

		return response()->json(['success' => true, 'data' => $response]);
	}

	public function patientWiseServiceAppointments(Request $request, $key)
	{
		$location_list = $this->LocationMasterService->AllListWithoutPaginate();
		$linkQuery = PatientWiseServiceEmail::where('uniqid', $key)->where('del_flag', 'N')->first();
		if (isset($linkQuery->id)) {
			$query = Patient::where('id', $linkQuery->patient_id)->where('deleted_flag', 'N')->first();
			$locationname = '';
			if (count($location_list) > 0) {
				foreach ($location_list as $vsl) {
					if (isset($query->location_id) && $query->location_id != '') {
						if ($query->location_id == $vsl->id) {
							$locationname = $vsl->address1 . ' ' . $vsl->city . ' ' . $vsl->state;
						}
					}
				}
			}
			$times = $this->hoursRange(32400, 72000, 60 * 15);
			$locationname = $locationname;
			$agencyDetails = Agency::getDetailsByAgencyId($query->agency_id);
			$agencyName = '';
			if (isset($agencyDetails->agency_name) && $agencyDetails->agency_name != '') {
				$agencyName = $agencyDetails->agency_name;
			}
			// $data['query']->agency_name = $agencyName;

			$getDetailsAppoinment = $this->LocationScheduleService->getDetailbyId($query->appoinment_time_id);
			// dd($getDetailsAppoinment);
			$end_start_date = '';
			if (isset($getDetailsAppoinment->start_time) && $getDetailsAppoinment->start_time != '') {
				$end_start_date =  date('h:i A', strtotime($getDetailsAppoinment->start_time)) . ' to ' . date('h:i A', strtotime($getDetailsAppoinment->end_time));
			}

			$data['appointmentTimes'] = $end_start_date;
			return view('patient/patient_service_hospital_appointment', ['data' => $data, 'query' => $query, 'key' => $key, 'location_list' => $location_list, 'times' => $times, 'locationname' => $locationname]);
		} else {

			return redirect('link_expired');
		}
	}

	function hoursRange($lower = 0, $upper = 86400, $step = 3600, $format = '')
	{
		$times = array();

		if (empty($format)) {
			$format = 'g:i a';
		}

		foreach (range($lower, $upper, $step) as $increment) {
			$increment = gmdate('H:i', $increment);

			list($hour, $minutes) = explode(':', $increment);

			$date = new \DateTime($hour . ':' . $minutes);

			$times[(string) $increment] = $date->format($format);
		}

		return $times;
	}
	public function getPatientServices(Request $request)
	{
		$services = $this->patientWiseServicesRequests->getPatientServices($request);

		$htmls = '';
		if (count($services) > 0) {
			$htmls = '<option value="" disabled>Select Service</option>';
			foreach ($services as $service) {
				foreach ($service['services'] as $vs) {
					$htmls .= '<option value="' . $service->id . '">' . $vs->name . '</option>';
				}
			}
		}
		return $htmls;
	}

	public function ajaxRequestService(Request $request)
	{
		$services = $this->patientServicesRequest->getPatientService($request->id);
		if (isset($_GET['debug']) && $_GET['debug'] == 1) {
			echo "<pre>";
			print_r($services);
			die();
			echo 1;
			die();
		}
		$finalArray = [];
		$serviceIds = [];
		$htmls = "<option value=''>Select Request Service</option>";
		if (!empty($services[0])) {
			foreach ($services as $val) {
				$servicesArray = [];
				$servicesArray[$val->id] = [];
				if (!empty($val->patientServiceRequestRelationShip[0])) {


					foreach ($val->patientServiceRequestRelationShip as $sr) {
						if (isset($temp[$sr->patient_service_request_id])) {
							$temp[$sr->patient_service_request_id][] = $sr->services[0]->name;
						} else {
							$temp[$sr->patient_service_request_id] = [];
							$temp[$sr->patient_service_request_id][] = $sr->services[0]->name;
						}

						$servicesArray[$val->id] = $temp[$sr->patient_service_request_id];
					}
				}

				$implode = implode(',', $servicesArray[$val->id]);
				$selected = "";
				if ($request->jsonencode != null) {
					if (in_array($val->id, $request->jsonencode)) {
						$selected = "selected";
					}
				}

				$htmls .= '<option value="' . $val->id . '" ' . $selected . '>' . date('m/d/Y', strtotime($val->created_at)) . ' ( ' . $implode . ')</option>';
			}
		}

		return $htmls;
	}




	public function serviceRequestedView($id)
	{
		$data['menu'] = "Service Requested Details";
		$data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}

		$requestData = $this->patientServicesRequest->patientServiceRequestData($id);


		$recorddata = $this->patientServicesRequest->getByPatientDetails($requestData->patient_id);
		$data['record'] = $recorddata;
		$data['service_request_data'] = $requestData;



		if ($data) {
			return view('patient/_partial/service_requests/service_requests_view', $data);
		} else {
			abort(404);
		}
	}


	public function serviceWiseList(Request $request)
	{
		$data['user'] = $authId = auth()->user();
		$data['serviceList'] = $this->patientServicesRequest->serviceWiseList($request->id);
		return view('patient/_partial/service_requests/service_wise_ajax_view', $data);
	}


	public function uploadDocumentService(Request $request)
	{
		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'document' => 'required',

		]);
		if ($validator->fails()) {
		} else {
			$oldData = $this->patientServicesRequest->patientServiceRequestData($request->id);
			$image = request('document');

			if ($request->file('document') != '') {
				$destinationPath = public_path() . '/dosusinguploads/services/';
				$filename = date("Ymdhisa") . '.' . $image->getClientOriginalExtension();
				$imagePath = Storage::disk('public')->putFileAs('dosusinguploads/services', $image, $filename);
				$data = ['document_name' => $imagePath];
			}


			$serviceId = ['id' => $request->id];
			$serviceUpdate = $this->patientServicesRequest->updateServiceData($serviceId, $data);

			$patientId = $this->patientServicesRequest->patientServiceRequestData($request->id);

			$message = $auth->first_name . ' ' . $auth->last_name . ' upload document';
			$patientServiceRequestLogArray  = [
				'patient_id' => $patientId->patient_id,
				'service_request_id' => $request->id,
				'type' => 'sub',
				'message' => $message,
				'old_response' => serialize((array)$oldData),
				'new_response' => serialize((array)$patientId),
			];

			$this->patientServicesRequest->insertLogPatientReq($patientServiceRequestLogArray);

			if ($serviceUpdate) {
				return response()->json(['success' => true, 'data' => [], 'error_msg' => 'document upload successfully requested.'], 200);
			} else {
				return response()->json(['success' => false, 'data' => [], 'error_msg' => self::ERROR_MESSAGE, 500]);
			}
		}
	}

	public function getServiceRequestLog(Request $request)
	{
		$id = $request->id;
		$data['reqServiceLog'] = $this->patientServicesRequest->reqServiceLog($id);
		return view('patient/_partial/service_requests/service_request_log_ajax', $data);
	}

	public function AppointmentsSaveNew(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'key' => 'required',
			'location_id' => 'required',
			'start_date' => 'required',
			'time_id' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$appoimentKey = $request->input('key');
			$getDetails = $this->appointmentService->getDetailsByUniqid($appoimentKey);
			if (isset($getDetails->id) && $getDetails->id != '') {

				$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($getDetails->patient_id);
				$getAgencyName = $getPatientDetails->agencyDetail;
				$start_date = date('Y-m-d', strtotime($request->input('start_date')));

				$getAppointSchedule = $this->LocationScheduleService->getDetailbyId($request->input('time_id'));

				$totalAppointmentBookByTime = $this->patientService->totalAppointmentBookedByTimeSlot($request->input('time_id'), $start_date);
				$totalCount = count($totalAppointmentBookByTime);
				$slotRemaing = ($getAppointSchedule->slot - $totalCount);


				if ($slotRemaing > 0) {

					$updateData  = ['location_id' => $request['location_id'], 'appointment_date' => $start_date . ' ' . date('H:i:s', strtotime($getAppointSchedule->start_time)), "appointment_time" => date('H:i:s', strtotime($getAppointSchedule->start_time)), 'status' => 'booked'];
					$this->appointmentService->update($updateData, array('id' => $getDetails->id));


					$patientData = [
						'appointment_date' => $start_date,
						'location_id' => $request['location_id'],
						'appointment_added_created_date' => date('Y-m-d H:i:s'),
						'appoinment_time_id' => $request->time_id,
						'appointment_mode' => 'sms',
						'patient_sms_flag' => 1
					];
					$this->patientService->update($patientData, array('id' => $getPatientDetails->id));

					if (!empty($request->service_id[0])) {
						foreach ($request->service_id as $srv) {
							$serviceUpdate = [
								'status' => "Booked"
							];
							$this->patientWiseServicesRequests->update($serviceUpdate, array('patient_service_request_id' => $getDetails->patient_service_request_id, 'service_id' => $srv, 'patient_id' => $getDetails->patient_id));
						}
					}

					if (strtolower($getPatientDetails->type) == 'caregiver') {

						$unitId = $getDetails->uniqid;
						$url = URL::to('/') . '/ap-new/' . $unitId;

						if (isset($getPatientDetails->language) && strtolower($getPatientDetails->language) == 'spanish') {

							$htmlLanguage = $getAgencyName->appointment_send_book_spanish;
						} else {

							$htmlLanguage = $getAgencyName->appointment_send_book_eng;
						}

						$htmlStringReplace = $htmlLanguage;
						$htmlStringReplace = str_replace('{{appointment_date}}', date('m-d-Y', strtotime($getPatientDetails->appointment_date)), $htmlStringReplace);
						$htmlStringReplace = str_replace('{{start_date}}', date('m-d-Y', strtotime($start_date)), $htmlStringReplace);
						$htmlStringReplace = str_replace('{{start_time}}', date('h:i A', strtotime($getAppointSchedule->start_time)), $htmlStringReplace);
						$htmlStringReplace = str_replace('{{end_time}}', date('h:i A', strtotime($getAppointSchedule->end_time)), $htmlStringReplace);
						$htmlStringReplace = str_replace('{{url}}', $url, $htmlStringReplace);
						$htmlStringReplace = str_replace('{{link}}', $getAppointSchedule->link, $htmlStringReplace);
						$smsMessage = $htmlStringReplace;

						if ($getPatientDetails->agency_id != '366' && $getPatientDetails->agency_id != '373' && $getPatientDetails->agency_id != '331') {
							//$sendSmsAgency = $this->smsService->AgencyWiseSmsDynamic($getPatientDetails->id, $getPatientDetails->mobile, $smsMessage);
							//$this->patientSMSLogService->save(array('patient_id' => $getPatientDetails->id, 'mobile_no' => $getPatientDetails->mobile, 'message' => $smsMessage, 'key' => $unitId));
						}
					}

					Session::flash('success', 'Appointment successfully update.');
					return redirect('/thank-you');
				}
			} else {
				return redirect('expired');
			}
		}
	}
	function AjaxPatientRequestedService(Request $request)
	{
		$patient_id = $request->input('patient_id');
		$selected_services_id = $request->input('selected_services_id');
		$type = $request->input('type');

		$query = $this->patientServicesRequest->serviceWiseList($selected_services_id);
		$final  = [];
		if (!empty($query[0])) {
			foreach ($query as $vals) {
				$temp = [];
				$temp['id'] = $vals->service_id;
				$temp['name'] = $vals->services[0]->name;
				$final[] = $temp;
			}
		}

		return response()->json(['success' => true, 'data' => $final, 'type' => $type]);
	}

	function AddAppointmentServiceRequested(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'service_id' => 'required',
			'date' => "required",
			'time' => "required",
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {

			$unitId =   $uns = uniqid();
			$cambine = $request->input('date');
			$date = Utility::convertYMD($cambine);
			$requestTime = $request->input('time');

			$patient_wise_service_id = $request->patient_wise_service_id;
			$patientId = $request->id;
			$checkAppointmentDetails = $this->appointmentService->getAppointmentDetailsById($request['id'], $patient_wise_service_id);


			$query = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);
			$getAgencyName = $query->agencyDetail;

			$agency_name = '';
			if (isset($getAgencyName->agency_name) &&  $getAgencyName->agency_name != '') {
				$agency_name = $getAgencyName->agency_name;
			}

			$time = '';
			if ($query->type == 'Patient') {
				$time = date('H:i:s', strtotime($requestTime));
			}

			if ($query->type == 'Caregiver') {

				$getAppointSchedule = $this->LocationScheduleService->getDetailbyId($requestTime);
				$time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";
			}

			if (isset($checkAppointmentDetails->uniqid) && $checkAppointmentDetails->uniqid != "") {
				$unitId = $checkAppointmentDetails->uniqid;
			}

			if (isset($checkAppointmentDetails->id) && $checkAppointmentDetails->id != "") {
				$update = $this->appointmentService->update(['location_id' => $request['location_id'], "service_id" => implode(',', $request['service_id']), 'appointment_date' => $date . ' ' . $time, "appointment_time" => $time, 'status' => 'booked', 'uniqid' => $unitId], array('id' => $checkAppointmentDetails->id));
			} else {
				$addAppintment = ["patient_id" => $request['id'], "location_id" => $request['location_id'], "service_id" => implode(',', $request['service_id']), "appointment_date" => $date . ' ' . $time, "appointment_time" => $time, "status" => "booked", "created_at" => date('Y-m-d H:i:s'), 'patient_service_request_id' => $patient_wise_service_id, 'uniqid' => $unitId];
				$update = $this->appointmentService->save($addAppintment);
			}

			if ($update) {

				$message = '';


				if ($query->type == 'Caregiver') {

					$getAppointSchedule = $this->LocationScheduleService->getDetailbyId($requestTime);
					$time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";

					$url = URL::to('/') . '/ap-new/' . $unitId;
					$message = 'Notice from ' . $agency_name . ': Your Appointment is scheduled for ' . Utility::convertMDY($cambine) . ' ' . date('h:i A', strtotime($getAppointSchedule->start_time)) . ' to ' . date('h:i A', strtotime($getAppointSchedule->end_time)) . ' ' . $url . '.  Do not reply to this text message for any questions please call (718) 972-3693';
					if (isset($query->language) && strtolower($query->language) == 'spanish') {
						$message = 'Aviso de ' . $agency_name . ': Su cita está programada para el ' . Utility::convertMDY($cambine) . ' de ' . date('h:i A', strtotime($getAppointSchedule->start_time)) . ' A ' . date('h:i A', strtotime($getAppointSchedule->end_time)) . ' ' . $url . '.  No responda a este mensaje de texto y si usted tiene alguna pregunta, por favor llame al (718) 972-3693';
					}
				}


				$this->patientServicesRequest->update(array('status' => 'booked'), array('id' => $patient_wise_service_id));
				// $ipaddress = request()->getClientIp();
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Update Schedule Appointment',
					'patient_id' =>  $request->input('id'),
					'service_request_id' =>  $patient_wise_service_id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has Appointment Schedule',
					'new_response' => serialize(array('sms' => $message, 'key' => $unitId, 'location_id' => $request->input('location_id'), 'appointment_date' => $date . ' ' . $time, 'appointment_added_by' => $user['id'], 'status' => 'booked',  'appointment_added_created_date' => date('Y-m-d H:i:s'), 'appoinment_time_id' => $request->input('time'), 'appointment_mode' => 'Manual')),
				];
				//PatientServiceRequestLog::save($insertLog);
				return response()->json(['success' => true, 'data' => [], 'error_msg' => 'Appointment schedule successfully.'], 200);
			}
			return response()->json(['success' => false, 'data' => [], 'error_msg' => self::ERROR_MESSAGE, 500]);
		}
	}

	public function changeStatusPatientTypeWiseServices(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'status' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {

			$status = $request->status;
			if ($status == 'Pending') {
				$status = 'pending';
			}
			if ($status == 'Scheduled') {
				$status = 'booked';
			}
			if ($status == 'MarkAsCompleted') {
				$status = 'completed';
			}
			if ($status == 'missed') {
				$status = 'missed';
			}
			if ($status == 'MarkAsHospitalized/Rehab') {
				$status = 'hospitalized/rehab';
			}
			if ($status == 'UnableToContact' || $status == 'Unable To Contact') {
				$status = 'unableToContact';
			}
			if ($status == 'MarkAsCancel') {
				$status = 'cancelled';
			}
			if ($status == 'MarkAsNoShow') {
				$status = 'noshow';
			}
			if ($status == 'MarkAsCheckIn') {
				$status = 'arrived';
			}
			if ($status == 'MarkAsProcessing') {
				$status = 'processing';
			}
			if ($status == 'MarkAsRefused') {
				$status = 'refused';
			}

			if ($status == 'Undo') {
				$status = 'undo';
			}

			if ($status == 'PendingTermination') {
				$status = 'Pending Termination';
			}

			if ($status == 'OnHold') {
				$status = 'On Hold';
			}

			if ($status == 'OnLeave') {
				$status = 'On Leave';
			}

			if ($status == 'Terminated') {
				$status = 'Terminated';
			}

			if ($status == 'InService') {
				$status = 'InService';
			}

			if ($status == 'Inactive') {
				$status = 'Inactive';
			}

			$data['status'] = $status;
			if ($status == 'completed') {
				$data['completed_date'] = date('Y-m-d H:i:s');
				$data['completed_by'] = Auth()->user()->id;
			}
			$data['last_status_update'] = date('Y-m-d H:i:s');
			$data['last_status_update_by'] = Auth()->user()->id;
			$update = $this->patientServicesRequest->update($data, array('id' => $request->serviceId));

			$getDetails = $this->patientServicesRequest->getByPatientDetails($request->serviceId);
			/* End:*/
			/* Start: Update patient master field status in case of last service request status is updated otherwise not update  */
			//get last record id
			$statusUpdate = 0;

			if (isset($getDetails->id)) {
				$last_record_id = $this->patientServicesRequest->lastServiceRequestedByPatientId($getDetails->patient_id);
				if (isset($last_record_id->id)) {
					if ($last_record_id->id == $request->serviceId) {
						$this->updatePatientStatus($request->status, $getDetails->patient_id);
						$statusUpdate = 1;
					}
				}
			}

			$dataResponse = ['status' => $statusUpdate];

			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($getDetails->patient_id);
			if (isset($getPatientDetails->id)) {
				$agencyData = Agency::getDetailsByAgencyId($getPatientDetails->agency_id);
				$link = '';
				if (isset($agencyData->is_sms) && $agencyData->is_sms == 1) {
					if ($status == 'completed') {
						if (isset($request->serviceId)) {
							$link = URL::to('/review-feedback-form/') . '/' . sha1($request->serviceId);

							$smsMessage = "";
							if (isset($getPatientDetails->mobile) && $getPatientDetails->mobile != "") {
								$smsMessage = "Dear,<br>";
								$smsMessage .= 'we’re happy to inform you that your service is complete. Help us improve by sharing your feedback.<br/>';
								$smsMessage .= '<a href="' . $link . '" style="text-decoration: none;">Click Here to Provide Feedback</a>';
							}

							$this->smsService->AgencyWiseSmsDynamic($getPatientDetails->id, $getPatientDetails->mobile, $smsMessage);
						}
					}
				}
				$query  = User::select('id')->where('delete_flag', 'N')->where('agency_fk', $getPatientDetails->agency_id);
				if ($user['agency_fk'] != "") {
					$query->where('id', '!=', auth()->user()->id);
				}
				$userData = $query->pluck('id')->toArray();
				$msg = '';
				$userData = Utility::getGroupUsersData($getPatientDetails->agency_id, $getPatientDetails->type, 'Service Status', $userData);
			}
			/* End: */
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Service status updated',
				'link' => url('/change-service-status'),
				'module' => self::MODULE_NAME,
				'object_id' => $getDetails->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has updated service status to ' . $status,
				'new_response' => serialize($data),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			return response()->json(['success' => true, 'data' => $dataResponse, 'error_msg' => 'Patient Service Status Updated successfully.'], 200);
		}
	}

	public function patientServiceRequestedList(Request $request)
	{

		//ini_set('memory_limit', '4096M');
		$data['menu'] = "Patient List";
		$data['user'] = $user = auth()->user();

		if (!$user || $user == null) {
			return redirect('login');
		}

		$angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['agencyList'] = $angecyList;
		$agency_fk = $data['agency_fk'] = request('agency_fk');

		$isPastShow = request('is_past_show');
		$data['isPastShow'] = $isPastShow;
		$data['patient_code'] = $patient_code = request('patient_code');


		$data['doctor_list'] =  Cache::get('patient_doctor_list', function () {
			return $this->doctorService->getDoctorList();
		}, 10 * 60);


		$data['location_list'] = $locationList = Cache::get('patient_master_locations', function () {
			return $this->LocationMasterService->AllListWithoutPaginate();
		}, 10 * 60);
		$locationschedule = Cache::get('patient_master_locations_schedule', function () {
			return $this->LocationScheduleService->getAll();
		}, 10 * 60);

		$created_by = "";
		if ($user->agency_fk != "") {

			$created_by = ($request->created_by != "") ? $request->created_by : "";
		} else {
			$created_by = $request->created_by_ny_id;
		}
		$data['created_by'] = $created_by;
		$data['created_by_id'] = $request->created_by_ny_id;
		$data['created_by_name'] = $request->created_by_ny_name;

		$debug = $request->debug;

		$data['serviceList'] = Cache::get('patient_master_services', function ()  use ($user, $agency_fk) {
			if ($agency_fk != "") {
				$agencyId = $agency_fk;
			} else {
				$agencyId = $user->agency_fk;
			}
			$getAgencyWiseList = $this->agencyWiseServiceService->getServiceNew($agencyId, "");
			if (!empty($getAgencyWiseList[0])) {
				return  $getAgencyWiseList;
			} else {
				return  Master::getServiceRequest();
			}
		}, 10 * 60);


		$data['assign_user_list'] =  Cache::get('patient_master_nubest_user', function () {
			return  User::getNYBestUserData();
		}, 10 * 60);

		$data['agency_user_list'] =  Cache::get('agency_user_list', function ()  use ($user) {
			return UserHelper::getAgencyWiseUserList($user->agency_fk);
		}, 10 * 60);

		$searchQuery = $request->all();
		$data['search_param'] = $searchQuery;
		$data['language_list'] = Language::getLanguageList();
		$data['statuses'] = Utility::getUniqueStatusData();
		return view('patient_request_service/patient_and_service_request_list', $data);
	}

	public function patientServiceRequestedAjaxList(Request $request)
	{
	//	ini_set('memory_limit', '4096M');
		$data['menu'] = "Patient List";
		$data['user'] = auth()->user();
		$searchQuery = $request->all();

		$query = $this->patientServicesRequest->patientRequestedServiceList($searchQuery);
		if (count($query) > 0) {
			foreach ($query as $val) {
				$reasonName = "";
				if ($val->reason_id != "") {
					$details  = Master::select('name')->where('id', $val->reason_id)->first();
					$reasonName = $details->name ?? "";
				}
				$val->reason_name = $reasonName;
			}
		}
		$data['query'] = $query;
		return view("patient_request_service/patient_and_service_request_ajax_list", $data);
	}

	public function patientServiceRequestedExport(Request $request)
	{
		$user = auth()->user();

		$searchQuery = $request->all();
		$query = $this->patientServicesRequest->getDataExport($searchQuery);

		$filename = 'Patient' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

		if ($user->agency_fk == 106) {
			$columns = array('No', 'Patient Id', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob', 'Location', 'Appointment Date', 'Appointment Start Time', 'Service', 'Status', 'Notes', "Booked Via", "Assign NyBest User", "Created Date", "Created By", 'Due Date', 'FU Date', 'Is Archive', 'Training Status', 'Completed date', 'Follow Up Date', 'Traning Due Date', 'Location / Branch', 'Refuse Reason');
		} else {
			$columns = array('No', 'Patient Id', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob', 'Location', 'Appointment Date', 'Appointment Start Time', 'Service', 'Status', 'Notes', "Booked Via", "Assign NyBest User", "Created Date", "Created By", 'Due Date', 'FU Date', 'Is Archive', 'Completed date', 'Follow Up Date', 'Location / Branch', 'Refuse Reason');
			if ($user->user_type_fk == 184) {
				$columns[] = 'Training Date';
				$columns[] = 'Training Status';
				$columns[] = 'Last Status Update Date';
				$columns[] = 'Last Status Updated By';
				$columns[] = 'Referral Type';
			}
		}


		$newass  = array();
		$callback = function () use ($query, $columns, $user) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			$cnt = 1;
			foreach ($query as $list) {
				$getAssignNyUser = User::getDetailsById($list->patient->assign_user_id);
				$assign_fname = '';
				$assign_lname = '';
				if (isset($getAssignNyUser->first_name) && $getAssignNyUser->first_name != '') {
					$assign_fname = $getAssignNyUser->first_name;
				}
				if (isset($getAssignNyUser->last_name) && $getAssignNyUser->last_name != '') {
					$assign_lname = $getAssignNyUser->last_name;
				}

				$assignName = $assign_fname . ' ' . $assign_lname;
				$date = '';
				if ($list->patient->dob != '0000-00-00' && $list->patient->dob != '') {
					$date = Utility::convertMDY($list->patient->dob);
				}
				$Adate = '';
				if ($list->patient->appointment_date != '0000-00-00 00:00:00' && $list->patient->appointment_date != '') {
					$Adate = Utility::convertMDY($list->patient->appointment_date);
				}
				$ATime  = '';
				if ($list->patient->start_time != '') {
					$ATime = date('h:i A', strtotime($list->patient->start_time));
				}

				$eTime  = '';
				if ($list->patient->end_time != '') {
					$eTime = date('h:i A', strtotime($list->patient->end_time));
				}

				$servie_array = [];
				if (isset($list->patientServiceRequestRelationShip) && !empty($list->patientServiceRequestRelationShip)) {
					foreach ($list->patientServiceRequestRelationShip as $data) {
						if (isset($data->requestService) && is_object($data->requestService) && isset($data->requestService->name)) {
							$servie_array[$list->id][] = $data->requestService->name;
						}
					}
				}

				if (!empty($servie_array[$list->id][0])) {
					$servie = implode(',', $servie_array[$list->id]);
				}

				$created_by_username =  $list->userDetails ? ($list->userDetails->first_name . ' ' . $list->userDetails->last_name) : '';

				$created_date = '';
				if ($list->created_at != "" || $list->created_at != NULL) {
					$created_date = date('d/m/Y h:i A', strtotime($list->created_at));
				}

				$due_date = '';
				if ($list->patient->due_date != "" || $list->patient->due_date != NULL) {
					$due_date = date('m/d/Y', strtotime($list->patient->due_date));
				}

				$fu_date = '';
				if ($list->patient->fu_date != "" && $list->patient->fu_date != NULL) {
					if ($list->patient->fu_date != "1969-12-31") {
						$fu_date = date('m/d/Y', strtotime($list->patient->fu_date));
					}
				}

				$isArchive = false;
				if ($list->patient->archived_at != '') {
					$isArchive = true;
				}

				$completedDate  = '';
				if ($list->completed_date != '') {
					$completedDate = date('m/d/Y', strtotime($list->completed_date));
				}

				$followUpDate  = '';
				if ($list->patient->follow_date != '') {
					$followUpDate = date('m/d/Y', strtotime($list->patient->follow_date));
				}

				$trainingDate = "";
				$trainingDate = date('m/d/Y', strtotime($list->patient->traning_due_date));

				$lastStatusUpdated  = '';
				if ($list->last_status_update != '' && $list->last_status_update != "0000-00-00 00:00:00") {
					$lastStatusUpdated = date('m/d/Y h:i A', strtotime($list->last_status_update));
				}

				$statusFirstname = "";
				$statusLastName = "";
				if (isset($list->statusUserDetails->id)) {
					$statusFirstname = $list->statusUserDetails->first_name;
					$statusLastName = $list->statusUserDetails->last_name;
				}

				$reasonName = "";
				if ($list->reason_id != "") {
					$details  = Master::select('name')->where('id', $list->reason_id)->first();
					$reasonName = $details->name ?? "";
				}

				if ($user->agency_fk == 106) {

					fputcsv($file, array($list->id, $list->patient_id, $list->patient->agencyDetail->agency_name,  $list->patient->type, $list->patient->diciplin, $list->patient->patient_code, $list->patient->first_name . ' ' . $list->patient->middle_name . ' ' . $list->patient->last_name, $list->patient->mobile, $list->patient->gender, $date, $list->patient->location_id, $Adate, $ATime,  $servie, $list->status, $list->patient->remarks, $list->patient->appointment_mode, $assignName, $created_date, $created_by_username, $due_date, $fu_date, $isArchive, $list->patient->training_status, $completedDate, $followUpDate, $trainingDate, $list->location_id, $reasonName));
				} else {
					$data = array($list->id, $list->patient_id, $list->patient->agencyDetail->agency_name, $list->patient->type, $list->patient->diciplin, $list->patient->patient_code, $list->patient->first_name . ' ' . $list->patient->middle_name . ' ' . $list->patient->last_name, $list->patient->mobile, $list->patient->gender, $date, $list->patient->location_id, $Adate, $ATime, $servie, $list->status, $list->patient->remarks, $list->patient->appointment_mode, $assignName, $created_date, $created_by_username, $due_date, $fu_date, $isArchive, $completedDate, $followUpDate, $list->location_id, $reasonName);
					if ($user->user_type_fk == 184) {
						$data[] = $list->trainingDate;
						$data[] = $list->patient->training_status;
						$data[] = $lastStatusUpdated;
						$data[] = $statusFirstname . ' ' . $statusLastName;
						$referralSource = "";
						if ($list->patient->referral_type != "") {
							$referralSource = ucfirst($list->patient->referral_type);
						} else {
							if ($list->patient->hha_id != "" || $list->patient->link_hha_caregiver != "" || $list->patient->link_hha_patient != "") {
								$referralSource = "HHA Exchange";
							} elseif ($list->patient->alaycare_id != "") {
								$referralSource = "Alayacare";
							} elseif ($list->patient->robort_id != "") {
								$referralSource = "Remote Focus";
							} elseif ($list->patient->platform_type == "VA") {
								$referralSource = "Visiting Aid";
							}
						}

						$data[] = $referralSource;
					}
					fputcsv($file, $data);
				}
			}

			fclose($file);
		};
		return response()->stream($callback, 200, $headers);
	}

	function checkAllStatus()
	{
		$commonStatus = [];
		$notStatus = [];
		$query = Patient::select('id', 'status', 'completed_date', 'completed_by')->where('deleted_flag', 'N')->inRandomOrder()->limit(1000)->get();
		foreach ($query as $pt) {
			$getLastServiceId = $this->patientServicesRequest->lastServiceRequestedByPatientId($pt->id);
			if (isset($getLastServiceId->id)) {
				if (strtolower($getLastServiceId->status) == strtolower($pt->status)) {
					$commonStatus[] = $getLastServiceId->id;
				} else {

					if ($pt->status == 'completed') {
						$this->patientServicesRequest->update(array('status' => $pt->status, 'completed_date' => $pt->completed_date, 'completed_by' => $pt->completed_by), array('id' => $getLastServiceId->id));
					} else {
						$this->patientServicesRequest->update(array('status' => $pt->status), array('id' => $getLastServiceId->id));
					}
					$notStatus[] = $getLastServiceId->id;
				}
			}
		}
	}

	public function updatePatientStatus($status, $pt_id)
	{
		$user = auth()->user();
		$getOldResponse = $this->patientService->getDetailById($pt_id);

		if ($status == 'Pending') {
			$data_array = array(
				'status' => 'pending',
				'appointment_date' => '',
			);
		}
		if ($status == 'Scheduled') {
			$data_array = array(
				'status' => "Booked",

			);
		}
		if ($status == 'MarkAsCompleted') {
			$data_array = array(
				'status' => "completed",
				'patient_sms_flag' => 1,
				'completed_date' => date('Y-m-d H:i:s'),
				'completed_by' => $user['id'],
			);
		}
		if ($status == 'missed') {
			$data_array = array(
				'status' => 'missed',
			);
		}
		if ($status == 'MarkAsHospitalized/Rehab') {
			$data_array = array(
				'status' => 'hospitalized/rehab',
			);
		}
		if ($status == 'UnableToContact') {
			$data_array = array(
				'status' => 'unableToContact',
			);
		}
		if ($status == 'MarkAsCancel') {
			$data_array = array(
				'status' => 'cancelled',

				'cancel_date' => date('Y-m-d H:i:s'),
				'cancel_by' => $user['id']
			);
		}
		if ($status == 'MarkAsNoShow') {
			$data_array = array(
				'status' => 'noshow',
				'no_show_date' => date('Y-m-d H:i:s'),
				'no_show_by' => $user['id']
			);
		}
		if ($status == 'MarkAsCheckIn') {
			$data_array = array(
				'status' => 'arrived',
			);
		}
		if ($status == 'MarkAsProcessing') {
			$data_array = array(
				'status' => 'processing',
			);
		}
		if ($status == 'MarkAsRefused') {
			$data_array = array(
				'status' => 'refused',
			);
		}

		if ($status == 'Undo') {
			$data_array = array(
				'status' => 'undo',
			);
		}

		if ($status == 'PendingTermination') {
			$data_array = array(
				'status' => 'Pending Termination',
			);
		}

		if ($status == 'OnHold') {
			$data_array = array(
				'status' => 'On Hold',
			);
		}

		if ($status == 'OnLeave') {
			$data_array = array(
				'status' => 'On Leave',
			);
		}

		if ($status == 'Terminated') {
			$data_array = array(
				'status' => 'Terminated',
			);
		}

		if ($status == 'InService') {
			$data_array = array(
				'status' => 'InService',
			);
		}

		if ($status == 'Inactive') {
			$data_array = array(
				'status' => 'inactive',
			);
		}

		$data_array['prev_status'] = $getOldResponse->status;
		$data_array['last_status_update'] = date('Y-m-d H:i:s');
		$data_array['last_status_update_by'] = Auth()->user()->id;
		$update = $this->patientService->update($data_array, array('id' => $pt_id));
		$getNewResponse = $this->patientService->getDetailById($pt_id);
		// $ipaddress = request()->getClientIp();
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Portal status updated',
			'link' => url('/patient/statusUpdate/') . '/' . $pt_id,
			'module' => self::MODULE_NAME,
			'object_id' => $pt_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has ' . $data_array['status'] . ' Updated Appointment',
			'new_response' => serialize($getNewResponse),
			'old_response' => serialize($getOldResponse),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		return $update;
	}

	public function deleteServiceRequestedList(Request $request)
	{
		$patientIds = [];
		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
		$data = [];
		$data['first_name'] = $getPatientDetails->first_name;
		$data['last_name'] = $getPatientDetails->last_name;
		$data['dob'] = $getPatientDetails->dob;
		$data['gender'] = $getPatientDetails->gender;
		$patientIds = $this->patientService->getPatientWiseAgencyListById($data);
		$patientIds[] = $request->patient_id;
		
		$patients  = $this->convertMergePatientArray($getPatientDetails->merge_appointment_id,$request->patient_id,"Y");
		$data = array_unique(array_merge($patients,$patientIds->toArray()));
		$response = $this->patientServicesRequest->getAllServiceListAssigned($data);

		if (!empty($response[0])) {
			foreach ($response as $val) {
				$val->status = ucfirst($val->status);
				$val->merge_flag =0;
				if($val->patient_id != $request->patient_id){
					if(in_array($val->patient_id,$data)){
						$val->merge_flag =1;
					}
				}
				$val->created_date = date('m/d/Y h:i A', strtotime($val->created_at));
			}
		}


		return response()->json(['status' => true, 'data' => $response, 'merge_appointment_id' => $getPatientDetails->merge_appointment_id]);
	}

	public function resolutionAjaxServiceRequested(Request $request)
	{
		$services = $this->patientServicesRequest->getPatientService($request->id);
		$htmls = "<option value=''>Select Request Service</option>";
		if (!empty($services[0])) {
			foreach ($services as $val) {
				$servicesArray = [];
				$servicesArray[$val->id] = [];
				if (!empty($val->patientServiceRequestRelationShip[0])) {
					foreach ($val->patientServiceRequestRelationShip as $sr) {
						if (isset($temp[$sr->patient_service_request_id])) {
							$temp[$sr->patient_service_request_id][] = $sr->services[0]->name;
						} else {
							$temp[$sr->patient_service_request_id] = [];
							$temp[$sr->patient_service_request_id][] = $sr->services[0]->name;
						}
						$servicesArray[$val->id] = $temp[$sr->patient_service_request_id];
					}
				}

				$implode = implode(',', $servicesArray[$val->id]);
				$selected = "";
				if ($request->jsonencode != null) {
					if (in_array($val->id, $request->jsonencode)) {
						$selected = "selected";
					}
				}
				$status = $val->status;
				$htmls .= '<option value="' . $val->id . '" ' . $selected . '>' . date('m/d/Y', strtotime($val->created_at)) . ' ( ' . $implode . ')  (' . $status . ')</option>';
			}
		}

		return $htmls;
	}

	public function hubPatientServiceRequestedList(Request $request)
	{

	//	ini_set('memory_limit', '4096M');
		$data['menu'] = "Hub Patient List";
		$data['user'] = $user = auth()->user();

		if (!$user || $user == null) {
			return redirect('login');
		}

		$angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['agencyList'] = $angecyList;
		$agency_fk = $data['agency_fk'] = request('agency_fk');

		$isPastShow = request('is_past_show');
		$data['isPastShow'] = $isPastShow;
		$data['patient_code'] = $patient_code = request('patient_code');


		$data['doctor_list'] =  Cache::get('patient_doctor_list', function () {
			return $this->doctorService->getDoctorList();
		}, 10 * 60);


		$data['location_list'] = $locationList = Cache::get('patient_master_locations', function () {
			return $this->LocationMasterService->AllListWithoutPaginate();
		}, 10 * 60);
		$locationschedule = Cache::get('patient_master_locations_schedule', function () {
			return $this->LocationScheduleService->getAll();
		}, 10 * 60);

		$created_by = "";
		if ($user->agency_fk != "") {

			$created_by = ($request->created_by != "") ? $request->created_by : "";
		} else {
			$created_by = $request->created_by_ny_id;
		}
		$data['created_by'] = $created_by;
		$data['created_by_id'] = $request->created_by_ny_id;
		$data['created_by_name'] = $request->created_by_ny_name;

		$debug = $request->debug;

		$data['serviceList'] = Cache::get('patient_master_services', function ()  use ($user, $agency_fk) {
			if ($agency_fk != "") {
				$agencyId = $agency_fk;
			} else {
				$agencyId = $user->agency_fk;
			}
			$getAgencyWiseList = $this->agencyWiseServiceService->getServiceNew($agencyId, "");
			if (!empty($getAgencyWiseList[0])) {
				return  $getAgencyWiseList;
			} else {
				return  Master::getServiceRequest();
			}
		}, 10 * 60);


		$data['assign_user_list'] =  Cache::get('patient_master_nubest_user', function () {
			return  User::getNYBestUserData();
		}, 10 * 60);

		$data['agency_user_list'] =  Cache::get('agency_user_list', function ()  use ($user) {
			return UserHelper::getAgencyWiseUserList($user->agency_fk);
		}, 10 * 60);

		$searchQuery = $request->all();
		$data['search_param'] = $searchQuery;
		$data['language_list'] = Language::getLanguageList();
		$data['statuses'] = Utility::getUniqueStatusData();
		return view('patient_request_service/hub_patient_and_service_request_list', $data);
	}
	public function hubpatientServiceRequestedAjaxList(Request $request)
	{
		//ini_set('memory_limit', '4096M');
		$data['menu'] = "Patient List";
		$data['user'] = auth()->user();
		$searchQuery = $request->all();

		$query = $this->patientServicesRequest->hubPatientRequestedServiceList($searchQuery);
		if (count($query) > 0) {
			foreach ($query as $val) {
				$reasonName = "";
				if ($val->reason_id != "") {
					$details  = Master::select('name')->where('id', $val->reason_id)->first();
					$reasonName = $details->name ?? "";
				}
				$val->reason_name = $reasonName;
			}
		}
		$data['query'] = $query;
		return view("patient_request_service/hub_patient_and_service_request_ajax_list", $data);
	}
	public function hubPatientServiceRequestedExport(Request $request)
	{
		$user = auth()->user();

		$searchQuery = $request->all();
		$query = $this->patientServicesRequest->getHubDataExport($searchQuery);

		$filename = 'Patient' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

		$columns = array('Portal Id', 'Agency Name', 'Company Name', 'Type', 'Full Name', 'Phone', 'Gender', 'Dob', 'Booking Date', 'Service', "Created Date", "Created By", 'Remarks');


		$callback = function () use ($query, $columns, $user) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);

			foreach ($query as $list) {

				$date = '';
				if ($list->patient->dob != '0000-00-00' && $list->patient->dob != '') {
					$date = Utility::convertMDY($list->patient->dob);
				}
				$bookingdate = '';
				if ($list->booking_date != '0000-00-00' && $list->booking_date != '') {
					$bookingdate = Utility::convertMDY($list->booking_date);
				}
				$servie_array = [];
				if (isset($list->patientServiceRequestRelationShip) && !empty($list->patientServiceRequestRelationShip)) {
					foreach ($list->patientServiceRequestRelationShip as $data) {
						if (isset($data->requestService) && is_object($data->requestService) && isset($data->requestService->name)) {
							$servie_array[$list->id][] = $data->requestService->name;
						}
					}
				}

				if (!empty($servie_array[$list->id][0])) {
					$servie = implode(',', $servie_array[$list->id]);
				}

				$created_by_username =  $list->userDetails ? ($list->userDetails->first_name . ' ' . $list->userDetails->last_name) : '';

				$created_date = '';
				if ($list->created_at != "" || $list->created_at != NULL) {
					$created_date = date('d/m/Y h:i A', strtotime($list->created_at));
				}

				fputcsv($file, array($list->patient->hub_id, $list->patient->agencyDetail->agency_name, $list->patient->hubCompanyDetail->agency_name,  $list->patient->type, $list->patient->first_name . ' ' . $list->patient->middle_name . ' ' . $list->patient->last_name, $list->patient->mobile, $list->patient->gender, $date, $bookingdate,  $servie, $created_date, $created_by_username, $list->remarks));
			}

			fclose($file);
		};
		return response()->stream($callback, 200, $headers);
	}

	public function editService(Request $request){
		$user = auth()->user();
		$service_request_id = $request->service_request_id;
		$validator = Validator::make($request->all(), [
			'service' => $request->edit_services
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$data['patient'] = $this->patientService->getDetailById($request->id);
			if (isset($data['patient']->id)) {
				$service_data = array(
					'service_id' => implode(',', request('edit_services'))
				);
				
				$this->patientService->update($service_data, array('id' => $request->id));
				
				$edit_services = $request->edit_services;
				$this->patientWiseServicesRequests->SoftDelete(['del_flag' => 'Y'],['patient_service_request_id' => $service_request_id,'patient_id' => $request->id]);
				if (is_array($edit_services)) {
					foreach ($edit_services as $serviceId) {
						$patientWiseServiceRequest = [
							'patient_id' => $request->id,
							'service_id' => $serviceId,
							'patient_service_request_id' => $service_request_id,
						];
						$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
					}
				}
				try {
					if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
						$agencyNotifyData = array(
							'agencyid' => $data['patient']->agency_id,
							'title' => 'Updated Service request',
							'record_id' => $request->id,
							'record_type' => 'Appointment',
							'msg' => '',
							'res_data' => serialize($data)
						);
						Common::insertAgencyNotificationsOfUser($agencyNotifyData);
					}
				} catch (\Throwable $th) {}
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Update patient Service request',
					'link' => url('/service-edit'),
					'module' => 'Patient Appointment',
					'object_id' => $request->id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has updated service request from agency',
					'new_response' => serialize(['id'=> $request->id,'services' => $request->edit_services]),
					'old_response' => serialize($data['patient']),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				return response()->json(['success' => true, 'data' => [], 'error_msg' => 'Service requested updated successfully.'], 200);
			}
		}
	}

	private function convertMergePatientArray($mergeIds,$currentId,$flag="N"){
		$mergeData = ['merge_id'=>$mergeIds,'currentId'=>$currentId,'del_flag'=>$flag];
		return MergeUtilityHelper::convertData($mergeData);
	}
}
