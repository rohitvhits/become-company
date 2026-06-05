<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\Services\PatientService;
use App\Services\LocationMasterService;
use App\Services\DocumentPatientService;
use App\Services\DoctorService;
use App\Services\LocationScheduleService;
use App\Services\PatientSMSLogService;
use App\Services\NyBestReminderNotificationService;
use App\Services\AssignNyBestUserService;
use App\Services\RequestService;
use App\Master;
use App\Model\PatientNotes;
use App\Model\Language;
use App\User;
use App\Services\AppointmentImportFileService;
use App\Services\CommonLogService;
use App\Services\PatientNotesService;
use App\Template;
use App\Helpers\Common;
use App\Helpers\HHACaregiversHelper;
use App\Model\Appointment;
use Illuminate\Support\Facades\Cache;
use App\Services\HHACaregiverMedicalService;
use App\Helpers\HHAPatientHelper;
use App\Services\SendEmailNotificationSerivce;
use App\Model\SMSLogs;
use App\Services\SmsService;
use App\Services\AgencyWiseServiceService;
use App\Services\AlayacareService;
use App\Services\RobortService;
use App\Services\FormBuilderService;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\UserSendPatientDocumentLogService;
use App\Services\InsuranceMasterService;
use App\Services\DocumentUploadService;
use App\Services\UserWiseAgencyService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Services\DisableDateService;
use App\Services\NotificationUserService;
use App\Helpers\GenerateAgencyTokenHelper;
use App\Services\DynamicFormLogService;
use App\Services\TaskService;
use App\Services\AppointmentService;
use App\Services\RateCardService;
use App\Services\TelehealthLocationScheduleEventService;
use App\Services\AlayacareClientService;
use App\Services\AppointmentPortalMergeLogsService;
use App\Services\HHAPatientService;
use App\Services\PatientV2Service;
class DeletedPatientController extends BaseController
{

	protected $thirdPartyPatientMaster, $AgencyWiseServiceService, $SmsService, $SendEmailNotificationSerivce, $hhaCaregiverMedicalService, $requestService, $nyBestReminder, $CommonLogService, $AppointmentImportFileService, $PatientSMSLogService, $LocationScheduleService, $PatientService, $PatientNotesService, $DocumentPatientService, $DoctorService, $LocationMasterService, $AlayacareService, $robortService, $userSendPatientDocumentLogService, $insuranceMasterService, $FormBuilderService, $documentUploadService, $patientServicesRequest, $patientWiseServicesRequests  = "";
	protected $userWiseAgencyService, $disableDateService, $notificationUserService, $taskService,$dynamicFormLogService,$appoimentService = "";
	protected $rateCardService,$assignNyBestUserService,$telehealthLocationScheduleEventService,$alayaclientService;
	
	protected $appointmentMergeLogsService;
	protected $hhaPatientService;
	protected $patientV2Service;
	public function __construct(PatientNotesService $PatientNotesService, AppointmentImportFileService $AppointmentImportFileService, PatientService $PatientService, DocumentPatientService $DocumentPatientService, DoctorService $DoctorService, LocationMasterService $LocationMasterService, LocationScheduleService $LocationScheduleService, PatientSMSLogService $PatientSMSLogService, CommonLogService $CommonLogService, NyBestReminderNotificationService $nyBestReminder, RequestService $requestService, HHACaregiverMedicalService	$hhaCaregiverMedicalService, SendEmailNotificationSerivce $SendEmailNotificationSerivce, SmsService $SmsService, AgencyWiseServiceService	$AgencyWiseServiceService, AlayacareService $AlayacareService, RobortService $robortService, ThirdPartyPatientMasterService $thirdPartyPatientMaster, UserSendPatientDocumentLogService $userSendPatientDocumentLogService, InsuranceMasterService $insuranceMasterService, FormBuilderService $FormBuilderService, DocumentUploadService $documentUploadService, UserWiseAgencyService $userWiseAgencyService, PatientServicesRequest $patientServicesRequest, PatientWiseServicesRequests $patientWiseServicesRequests, DisableDateService $disableDateService, NotificationUserService $notificationUserService, TaskService $taskService, DynamicFormLogService $dynamicFormLogService, AppointmentService $appoimentService, AlayacareClientService $alayaclientService, RateCardService $rateCardService, TelehealthLocationScheduleEventService $telehealthLocationScheduleEventService,AssignNyBestUserService $assignNyBestUserService,AppointmentPortalMergeLogsService $appointmentMergeLogsService,HHAPatientService $hhaPatientService, PatientV2Service $patientV2Service)
	{
		$this->PatientService = $PatientService;
		$this->DocumentPatientService = $DocumentPatientService;
		$this->DoctorService = $DoctorService;
		$this->LocationMasterService = $LocationMasterService;
		$this->LocationScheduleService = $LocationScheduleService;
		$this->PatientSMSLogService = $PatientSMSLogService;
		$this->AppointmentImportFileService = $AppointmentImportFileService;
		$this->PatientNotesService = $PatientNotesService;
		$this->CommonLogService = $CommonLogService;
		$this->nyBestReminder = $nyBestReminder;
		$this->requestService = $requestService;
		$this->hhaCaregiverMedicalService = $hhaCaregiverMedicalService;
		$this->SendEmailNotificationSerivce = $SendEmailNotificationSerivce;
		$this->SmsService = $SmsService;
		$this->AgencyWiseServiceService	= $AgencyWiseServiceService;
		$this->AlayacareService	= $AlayacareService;
		$this->robortService = $robortService;
		$this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
		$this->FormBuilderService = $FormBuilderService;
		$this->userSendPatientDocumentLogService = $userSendPatientDocumentLogService;
		$this->insuranceMasterService = $insuranceMasterService;
		$this->documentUploadService = $documentUploadService;
		$this->userWiseAgencyService = $userWiseAgencyService;

		$this->patientServicesRequest = $patientServicesRequest;
		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
		$this->disableDateService = $disableDateService;
		$this->notificationUserService = $notificationUserService;
		$this->taskService = $taskService;
		$this->dynamicFormLogService = $dynamicFormLogService;
		$this->appoimentService = $appoimentService;
		$this->rateCardService = $rateCardService;
		$this->assignNyBestUserService = $assignNyBestUserService;
		$this->telehealthLocationScheduleEventService = $telehealthLocationScheduleEventService;
		$this->alayaclientService = $alayaclientService;
		$this->appointmentMergeLogsService = $appointmentMergeLogsService;
		$this->hhaPatientService = $hhaPatientService;
		$this->patientV2Service = $patientV2Service;
	}

	public function view($id)
	{
       
		$data['menu'] = "Patient Details";
		$data['auth'] = $data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}


		$record = $this->patientV2Service->getPatientDetById($id);

		if (isset($record->id) && $record->id != '') {

			//Advance Form
			$data['agencyWiseField'] = $this->FormBuilderService->getAgencyWiseField($record->agency_id);

			$allFieldsWithoutFormId = $this->FormBuilderService->getAgencyWiseFieldWithoutFormId($record->agency_id);
			$data['portalFields'] = $allFieldsWithoutFormId->filter(function ($item) {
				return isset($item->fields) && $item->fields->show_in_portal == 1;
			})->values();
			$data['agencyWiseFieldWithoutFormId'] = $allFieldsWithoutFormId->filter(function ($item) {
				return !isset($item->fields) || $item->fields->show_in_portal != 1;
			})->values();

			$patientAdvanceSubmitData = $this->FormBuilderService->getAdvanceSubmitDataByAgencyIdPatientID($record->agency_id, $id);

			$patientAdvanceSubmitDataGroupByFormId = [];
			foreach ($patientAdvanceSubmitData as $patientAdvanceSubmit) {
				$patientAdvanceSubmitDataGroupByFormId[$patientAdvanceSubmit->agency_id][$patientAdvanceSubmit->field_id] = $patientAdvanceSubmit->value;
			}
			$data['patientAdvanceSubmitData'] = $patientAdvanceSubmitDataGroupByFormId;

			$data['agencyAllFormList'] = $this->FormBuilderService->getTypeWiseAgencyAllForm($record->agency_id, $record->type);
			$data['formList'] = $this->FormBuilderService->getAgencyForm($record->agency_id, $record->id);
			foreach ($data['formList'] as $value) {
				$agencyFormId = $value->id;
				$patientSubmitData = $this->FormBuilderService->getSubmitDataByAgencyIdPatientID($record->agency_id, $id, $agencyFormId);
				$patientSubmitDataGroupByFormId = [];
				foreach ($patientSubmitData as $patientSubmit) {
					$patientSubmitDataGroupByFormId[$patientSubmit->form_id][$patientSubmit->agency_id][$patientSubmit->patient_id][$patientSubmit->field_id] = $patientSubmit->value;
				}
				$data['patientSubmitData'][$agencyFormId] = $patientSubmitDataGroupByFormId;
			}

			//Agency All Form
			// $data['agencyAllFormList'] = $this->FormBuilderService->getTypeWiseAgencyAllForm($record->agency_id,$record->type);

			$patientSubmitData = $this->FormBuilderService->getSubmitDataByAgencyIdPatientID($record->agency_id, $id);
			$patientSubmitDataGroupByFormId = [];
			foreach ($patientSubmitData as $patientSubmit) {
				$patientSubmitDataGroupByFormId[$patientSubmit->form_id][$patientSubmit->agency_id][$patientSubmit->patient_id][$patientSubmit->field_id] = $patientSubmit->value;
			}
			$data['patientSubmitData'] = $patientSubmitDataGroupByFormId;

			$data['doctorList'] = $this->FormBuilderService->getAllDoctor();



			if ($record->record_read == 0) {
				$this->PatientService->update(array('record_read' => '1'), array('id' => $record->id));
			}
			$alayaCareDetails  = $this->AlayacareService->getAllDetailsByAlayacreId($record->alaycare_id);

			$fullAlayaCareName = '';
			if (isset($alayaCareDetails->id)) {
				$fullAlayaCareName = $alayaCareDetails->first_name . ' ' . $alayaCareDetails->last_name;
			}
			$record->alaycare_name = $fullAlayaCareName;


			$remoteDetails  = $this->robortService->getDetailsById($record->robort_id);

			$fullRemoteName = '';
			$externalId = '';
			if (isset($remoteDetails->id)) {
				$fullRemoteName = $remoteDetails->firstName . ' ' . $remoteDetails->lastName;
				$externalId = ($remoteDetails->externalId != "") ? '(' . $remoteDetails->externalId . ')' : "";
			}
			$record->remote_name = $fullRemoteName . $externalId;
			$record->externalId = $externalId;

			$fullHHXCaregiverName = '';

			if (isset($_GET['debug']) && $_GET['debug'] == 123) {
				echo $record->link_hha_caregiver;
				die();
			}
			if ($record->type == 'Caregiver') {
				// if ($record->id == 45927) {
				// 	echo $record->link_hha_caregiver . ' ' . $record->agency_id;
				// }
				$getHHXCaregiverDetails = HHACaregiversHelper::getCaregiverDetailsByAgencyId($record->link_hha_caregiver, $record->agency_id);


				if (!$getHHXCaregiverDetails) {
					$getHHXCaregiverDetails = HHACaregiversHelper::getCaregiverDetails($record->link_hha_caregiver);
				}
				//$getHHXCaregiverDetails = HHACaregiversHelper::getCaregiverDetailsNew($record->link_hha_caregiver);

				if (isset($getHHXCaregiverDetails->id)) {
					$fullName = $getHHXCaregiverDetails->first_name . ' ' . $getHHXCaregiverDetails->last_name . ' ( ' . $getHHXCaregiverDetails->caregiver_code . ' ) ';
					$fullHHXCaregiverName = $fullName;
				}
			}

			$fullHHXPatientName = "";
			if ($record->type == 'Patient') {
				$getHHXPatientDetails = HHAPatientHelper::getPatientDetails($record->link_hha_patient);

				if (isset($getHHXPatientDetails->id)) {
					$fullName = $getHHXPatientDetails->first_name . ' ' . $getHHXPatientDetails->last_name . ' ( ' . $getHHXPatientDetails->admission_id . ' ) ';
					$fullHHXPatientName = $fullName;
				}
			}

			$record->hhx_caregiver_name = $fullHHXCaregiverName;
			$record->hhx_patient_name = $fullHHXPatientName;
			$data['hhaStatusList']	=	$this->hhaCaregiverMedicalService->getStatusList();
			$data['agencyDetails'] = Agency::getDetailsByAgencyId($record->agency_id);

			$data['user_list'] = User::getHospitalUser();

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


			$userTypes = "Nybest User";
			$fname = '';
			$lname = '';
			if ($record->created_by != '') {
				$getUserDetails = User::getDetailsById($record->created_by);
			} else {
				$getThirdPartyUserDetails = GenerateAgencyTokenHelper::getDetailsById($record->agency_token_id);
				$userTypes = "";
				$fname = isset($getThirdPartyUserDetails->notes) ? $getThirdPartyUserDetails->notes : "";
			}

			if (isset($getUserDetails->agency_fk) && $getUserDetails->agency_fk != '') {
				$userTypes = "Agency User";
			}
			if (isset($getUserDetails->first_name) && $getUserDetails->first_name != '') {
				$fname = $getUserDetails->first_name;
			}
			if (isset($getUserDetails->last_name) && $getUserDetails->last_name != '') {
				$lname = $getUserDetails->last_name;
			}
			$record->createdBy = $fname . ' ' . $lname;
			$record->userTypes = $userTypes;

			// if (Common::checkAgencyLogin()) {
			// 	$data['document_list'] = $this->DocumentPatientService->getAllDocumentByPatientIdAgency($id);
			// } else {
			// 	$data['document_list'] = $this->DocumentPatientService->getAllDocumentByPatientId($id);
			// }

			$data['doctor_list'] = array(); // $this->DoctorService->getDoctorList();
			$data['location_list'] = $this->LocationMasterService->AllListWithoutPaginate();
			$localdetails = '';


			foreach ($data['location_list'] as $val) {
				if (isset($record->location_id) && $record->location_id != '') {
					if ($record->location_id == $val->id) {
						$address1 = '';
						$address2 = '';
						$city = '';

						if ($val->address1 != '') {
							$address1 = $val->address1;
						}
						if ($val->address1 != '') {
							$address2 = $val->address2;
						}
						if ($val->city != '') {
							$city = $val->city;
						}
						$localdetails = $address1 . ' ' . $address2 . ' ' . $city . ' ' . $val->state . ' ' . $val->zip_code;
					}
				}
			}

			$new_location_list = $this->LocationMasterService->getDetailbyId($record->location_id);
			$localdetails = "";
			if (isset($new_location_list->address1) && $new_location_list->address1 != '') {
				$localdetails = $new_location_list->address1;
			}
			$record->location = $localdetails;
			$data['masterData'] = Master::getAllDataByMasterTypeFk(array(17));
			$data['masterData'] = $masterData = Master::getAllDataByMasterTypeFk(array(12, 17, 25));
			$servie = '';
			if (isset($record->service_id) && $record->service_id != '') {
				$explode = explode(',', $record->service_id);
				$finalArray = [];
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
			$reasonname = '';
			$payment_type = '';
			foreach ($masterData as $val) {
				if (isset($record->reason_id) && $record->reason_id != '') {
					if ($val->id == $record->reason_id) {
						$reasonname = $val->name;
					}
				}
				if (isset($record->payment_type) && $record->payment_type != '') {
					if ($val->id == $record->payment_type) {
						$payment_type = $val->name;
					}
				}
			}
			$record->payment_type_new = $payment_type;
			if ($reasonname != '') {
				$record->reasonname = $reasonname;
			}
			if ($servie != '') {
				$record->service = $servie;
			}

			$record->callCounter = count($this->PatientNotesService->patientNoteCallCounter($id));
			$thirdPartyid = $record->link_third_party;
			$data['record'] = $record;
			$data['times'] = $this->hoursRange(32400, 72000, 60 * 15);
			$data['assign_user_list'] = User::getNYBestUserData();
			$data['template_list'] = Template::getTemplatesList();
			$data['notes'] = PatientNotes::getPatientNotes($id);


			$data['nyBestUserList'] = User::getNyBestUsersList();
			$data['locations'] = $this->LocationMasterService->AllList();

			$ids = [$id];
			
			
			$data['pastAppointment'] = $pastAppointment = Appointment::getPastAppointmentListNewAll($ids);

			$servie = [];
			if (count($pastAppointment) > 0) {
				foreach ($pastAppointment as $record) {
					if (isset($record->service_id) && $record->service_id != '') {
						$servicesIds = explode(',', $record->service_id);


						$services = Master::whereIn('id', $servicesIds)->where('del_flag', 'N')->get();
						$newass  = array();
						foreach ($services as $kke) {
							$newass[] = $kke->name;
						}

						if (!empty($newass)) {
							$servie[] = implode(',', $newass);
						}
					}
				}
			}
			$data['servie'] = $servie;
			$data['serviceList'] = Master::getServiceRequest();

			$third_party_details = $this->thirdPartyPatientMaster->getPatientDetails($thirdPartyid, $data['record']->agency_id);

			$data['record']->link_third_party = $thirdPartyid;
			$name = "";
			if (isset($third_party_details->first_name)) {
				$name = $third_party_details->first_name . ' ' . $third_party_details->last_name;
			}
			$data['record']->link_third_party_name = $name;

			$insuranceDetails = $this->insuranceMasterService->getDetailById($data['record']->insurance_name);
			if (isset($_GET['debug']) && $_GET['debug'] == 2345) {
				echo "<pre>";
				print_r($insuranceDetails);
				die();
			}
			$insuranceName = "";
			if (isset($insuranceDetails->id)) {
				$insuranceName = $insuranceDetails->insurance_name;
			}

			if ($data['record']->insurance_name == 'other') {
				$insuranceName = 'Other';
			}

			$data['record']->insuranceName = $insuranceName;

			$debugMode = "";
			if (isset($_GET['vishal_mode']) && $_GET['vishal_mode'] == 1) {
				$debugMode = "debug";
			}

			$data['debugMode'] = $debugMode;
			$disable_date = $this->disableDateService->disableDateAllData($record->type)->toArray();
			$dateArray = explode(', ', implode(', ', $disable_date));
			$dateDetailArray = [];
			if (!empty($dateArray[0])) {
				foreach ($dateArray as $val) {
					$dateDetailArray[] = date('d-m-Y', strtotime($val));
				}
			}
			$data['disable_date']  = json_encode($dateDetailArray);

			$data['language_list'] =  Cache::get('language_list', function ()  use ($auth) {
				return Language::getLanguageList();
			}, 10 * 60);
            
			$data['addService'] = 0;
			return view('patient/deletd_patient_view_hospital', $data);
		} else {
			abort(404);
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
    function ajaxDocumentList(Request $request)
	{
		$id = $request->id;
		$data['user'] = auth()->user();
		$data['record'] = $record = $this->PatientService->getPatientDetailsByIdWhitoutAgency($id);
		$ids = [$id];

		if (isset($record->merge_appointment_id) && $record->merge_appointment_id != "") {
			$ids = $this->convertMergePatientArray($record->merge_appointment_id,$id);
		}
		
		$data['agencyDetails'] = Agency::getDetailsByAgencyId($record->agency_id);

		
		if (Common::checkAgencyLogin()) {

			$document_list = $this->DocumentPatientService->getAllDocumentByPatientIdAgency($id);
		} else {
			$document_list = $this->DocumentPatientService->getAllDocumentByPatientId($ids);
		}

		if (!empty($document_list[0])) {
			foreach ($document_list as $val) {
				$serviceArray = [];
				$services = $this->documentUploadService->getDocumentListByDocumentId($val->id);
				if (!empty($services[0])) {
					foreach ($services as $ser) {
						if (isset($ser->masterDetails) && $ser->masterDetails != "") {
							$serviceArray[] = $ser->masterDetails;
						}
					}
				}

				$val->services = $serviceArray;
			}
		}
		$data['document_list'] = $document_list;
		return view('deletedPatients/_partial/document_ajax_list', $data);
	}

    public function getNotes($id)
	{
   
		$data['user'] =  $user = auth()->user();
		$message = request('message');
		$readMessage = request('readMessage');
		$call_flag = '';
		if ($user['user_type_fk'] == 184) {
			$call_flag = request('call_flag');
		}

		$agency_id = request('agency_id');

		$allSMS = PatientNotes::getRecordALLNotesByRecordID($id, $readMessage, $user['id'], $call_flag);
		//echo "<pre>";print_r($allSMS);die;
		return response(json_encode($allSMS), 200)->header('Content-Type', 'application/json');
	}

    public function smsLogs($id)
	{
		$ids = [$id];	
		$smsLogs = SMSLogs::getlistWithIds($ids);
	
		return view('deletedPatients/sms-logs-ajax', ['data' => $smsLogs,'record_id'=>$id]);
	}

	private function convertMergePatientArray($mergeIds,$currentId){
		$finalAllMergeIds =[];
		$finalAllMergeIds[] = $currentId;
		$mergePortalId = $this->appointmentMergeLogsService->getMainPortalIds($currentId);
		$mergePortalIds = array_column($mergePortalId->toArray(), 'merge_patient_id');
		$finalAllMergeIds = array_merge($finalAllMergeIds, $mergePortalIds);
	
		return array_values(array_unique($finalAllMergeIds));
	}
}
