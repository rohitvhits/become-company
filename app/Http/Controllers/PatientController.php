<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use App\Agency;
use App\Model\HHACaregivers;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use App\Services\AutoCallService;
use App\Services\RecordReportService;
use App\Services\LocationMasterService;
use App\Services\DocumentPatientService;
use App\Services\DoctorService;
use App\Services\LocationScheduleService;
use App\Services\PatientSMSLogService;
use App\Services\NyBestReminderNotificationService;
use App\Services\AssignNyBestUserService;
use App\Services\RequestService;
use App\Master;
use App\Record;
use App\Model\Patient;
use App\Model\PatientNotes;
use App\Model\HhaAppointment;
use App\Model\Language;
use App\User;
use App\Model\DocumentPatient;

use Illuminate\Support\Facades\URL;
use App\Services\AppointmentImportFileService;
use App\Services\CommonLogService;
use App\Services\PatientNotesService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Helpers\EsignHelper;
use App\Services\PatientDocumentSentReportService;
use App\Template;
use App\DocumentSignerMaster;
use App\Helpers\Common;
use App\Helpers\DocumentHelper;
use App\Helpers\HHAAppointmentHelper;
use App\Helpers\HHACaregiversHelper;
use App\Model\Appointment;
use App\Model\AssignEMCRecord;
use App\Model\ScheduleAppointment;
use App\Notifications\MyFirstNotification;
use App\Services\LogsService;
use App\ZipCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\HHACaregiverMedicalService;
use App\Helpers\HHAPatientHelper;
use App\Model\HHAPatient;
use App\Services\SendEmailNotificationSerivce;
use App\Model\SMSLogs;
use App\Services\SmsService;
use App\Model\AlayacareEmployee;
use App\Services\AgencyWiseServiceService;
use App\Services\AlayacareService;
use App\Services\RobortService;
use App\Helpers\UserHelper;
use App\Model\PatientCustomData;
use App\Services\FormBuilderService;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\UserSendPatientDocumentLogService;
use Aws\S3\S3Client;
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
use App\Services\AgencyWiseSMSNotificationService;
use App\Services\AlayacareClientService;
use App\Services\PaymentLogService;
use App\Services\RateCardService;
use App\Services\DocumentSendService;
use App\Model\AgencyWiseNotifictionEmail;
use App\Services\AgencyWiseThirdPartyAPIService;
use App\Services\TelehealthLocationScheduleEventService;
use App\Helpers\ThirdPartyWebHookHelper;
use App\Services\PatientTelehealthScheduleService;
use App\Services\MultiplePatientDocApprovalService;
use App\Services\UserDocApprovalService;
use App\Services\UserDocQuestionMarkedService;
use App\Services\DocCompletedInternalUseLogService;
use App\Services\UserCreatorEmailNotificationService;
use Carbon\Carbon;
use Response;
use App\Model\InsuranceMaster;
use Illuminate\Support\Facades\File;
use App\Model\LogsCreateEmailCheck;
use App\Model\PDF;
use App\Helpers\DateWiseAgencyAccessHelper;
use App\Services\AppointmentPortalMergeLogsService;
use App\Services\HHAPatientService;
use App\Services\AgencyNoteService;
use App\Services\BranchListService;
use App\Services\PatientThirdPartyEmployeeService;
use App\Services\FieldMasterService;
use App\Helpers\MergeUtilityHelper;
use App\Services\TaskHealthMasterService;
use App\Services\UserService;
use App\Helpers\PatientModuleHelper;
use App\Services\AgencyService;
use App\Services\MasterService;
use App\Services\HHALogService;
use App\Services\AwsBedrockService;
use App\Services\AwsTextractService;
use Illuminate\Support\Facades\Log;

use App\Services\LanguageService;
class PatientController extends BaseController
{

	protected $thirdPartyPatientMaster, $AgencyWiseServiceService, $SmsService, $SendEmailNotificationSerivce, $hhaCaregiverMedicalService, $requestService, $nyBestReminder, $CommonLogService, $AppointmentImportFileService, $PatientSMSLogService, $LocationScheduleService, $PatientService, $PatientNotesService, $DocumentPatientService, $DoctorService, $LocationMasterService, $AlayacareService, $robortService, $userSendPatientDocumentLogService, $insuranceMasterService, $FormBuilderService, $documentUploadService, $patientServicesRequest, $patientWiseServicesRequests, $alayaclientService, $userDocApprovalService = "";
	protected $userWiseAgencyService, $disableDateService, $notificationUserService, $taskService, $dynamicFormLogService, $appoimentService, $agencyWiseSMSNotificationService, $paymentLogService, $rateCardService, $documentSendService, $agencyWiseThirdPartyAPIService, $telehealthLocationScheduleEventService, $patientTelehealthScheduleService, $multiplePatientDocApprovalService, $userDocQuestionMarkedService,$docCompletedInternalUseLogService,$assignNyBestUserService,$branchListService  = "";
	protected $hhaPatientService;
	protected $patientThirdPartyEmployeeService;
	protected $fieldMasterService;
	protected $taskHealthMasterService;
	protected $appointmentMergeLogsService;
	protected $userCreatorEmailNotificationService;
	protected $agencyNoteService;
	protected $userService;
	protected $hhaLogService;
	protected $agencyService;
	protected $languageService;
	protected const STATIC_EMAIL = "allstaff@nybestmedical.com";
	public function __construct(PatientNotesService $PatientNotesService, AppointmentImportFileService $AppointmentImportFileService, PatientService $PatientService, DocumentPatientService $DocumentPatientService, DoctorService $DoctorService, LocationMasterService $LocationMasterService, LocationScheduleService $LocationScheduleService, PatientSMSLogService $PatientSMSLogService, CommonLogService $CommonLogService, NyBestReminderNotificationService $nyBestReminder, RequestService $requestService, HHACaregiverMedicalService	$hhaCaregiverMedicalService, SendEmailNotificationSerivce $SendEmailNotificationSerivce, SmsService $SmsService, AgencyWiseServiceService	$AgencyWiseServiceService, AlayacareService $AlayacareService, RobortService $robortService, ThirdPartyPatientMasterService $thirdPartyPatientMaster, UserSendPatientDocumentLogService $userSendPatientDocumentLogService, InsuranceMasterService $insuranceMasterService, FormBuilderService $FormBuilderService, DocumentUploadService $documentUploadService, UserWiseAgencyService $userWiseAgencyService, PatientServicesRequest $patientServicesRequest, PatientWiseServicesRequests $patientWiseServicesRequests, DisableDateService $disableDateService, NotificationUserService $notificationUserService, TaskService $taskService, DynamicFormLogService $dynamicFormLogService, AppointmentService $appoimentService, AgencyWiseSMSNotificationService $agencyWiseSMSNotificationService, AlayacareClientService $alayaclientService, PaymentLogService $paymentLogService, RateCardService $rateCardService, DocumentSendService $documentSendService, AgencyWiseThirdPartyAPIService $agencyWiseThirdPartyAPIService, TelehealthLocationScheduleEventService $telehealthLocationScheduleEventService, PatientTelehealthScheduleService $patientTelehealthScheduleService, MultiplePatientDocApprovalService $multiplePatientDocApprovalService, UserDocApprovalService $userDocApprovalService, UserDocQuestionMarkedService $userDocQuestionMarkedService, DocCompletedInternalUseLogService $docCompletedInternalUseLogService,UserCreatorEmailNotificationService $userCreatorEmailNotificationService,AssignNyBestUserService $assignNyBestUserService,HHAPatientService $hhaPatientService, BranchListService $branchListService,PatientThirdPartyEmployeeService $patientThirdPartyEmployeeService,FieldMasterService $fieldMasterService,TaskHealthMasterService $taskHealthMasterService,AppointmentPortalMergeLogsService $appointmentMergeLogsService, AgencyNoteService $agencyNoteService,UserService $userService,HHALogService $hhaLogService,AgencyService $agencyService,MasterService $masterService, LanguageService $languageService)
	{
		// $this->middleware('permission:appointments-list|appointments-add|appointments-edit|appointments-delete|appointments-view', ['only' => ['index','save','view']]);
		// $this->middleware('permission:appointments-list', ['only' => ['index']]);
		// $this->middleware('permission:appointments-add', ['only' => ['add','save']]);
		// $this->middleware('permission:appointments-edit', ['only' => ['edit','update']]);
		// $this->middleware('permission:appointments-delete', ['only' => ['delete']]);
		// $this->middleware('permission:appointments-view', ['only' => ['view']]);
		// $this->middleware('permission:appointments-archived', ['only' => ['PatientArchiveList']]);
		// $this->middleware('permission:appointments-cancel', ['only' => ['StatusWiseRecord']]);
		// $this->middleware('permission:appointments-refused', ['only' => ['StatusWiseRecord']]);

		// $this->middleware('auth', ['except' => ['AppointmentsSave', 'documentView', 'GeneratePdf', 'documentInsertView', 'documentViewNews', 'thankyou', 'patientStatus', 'AppointmentsUpdate', 'patientAppointments', 'expired', 'nyThankyou']]);
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
		$this->AgencyWiseServiceService = $AgencyWiseServiceService;
		$this->AlayacareService = $AlayacareService;
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
		$this->agencyWiseSMSNotificationService = $agencyWiseSMSNotificationService;
		$this->alayaclientService = $alayaclientService;
		$this->paymentLogService = $paymentLogService;
		$this->rateCardService = $rateCardService;
		$this->documentSendService = $documentSendService;
		$this->agencyWiseThirdPartyAPIService = $agencyWiseThirdPartyAPIService;
		$this->telehealthLocationScheduleEventService = $telehealthLocationScheduleEventService;
		$this->patientTelehealthScheduleService = $patientTelehealthScheduleService;
		$this->multiplePatientDocApprovalService = $multiplePatientDocApprovalService;
		$this->userDocApprovalService = $userDocApprovalService;
		$this->userDocQuestionMarkedService = $userDocQuestionMarkedService;
		$this->docCompletedInternalUseLogService = $docCompletedInternalUseLogService;
		$this->userCreatorEmailNotificationService = $userCreatorEmailNotificationService;
		$this->assignNyBestUserService = $assignNyBestUserService;
		$this->appointmentMergeLogsService = $appointmentMergeLogsService;
		$this->hhaPatientService = $hhaPatientService;
		$this->branchListService = $branchListService;
		$this->patientThirdPartyEmployeeService = $patientThirdPartyEmployeeService;
		$this->fieldMasterService = $fieldMasterService;
		$this->taskHealthMasterService = $taskHealthMasterService;
		$this->agencyNoteService = $agencyNoteService;
		$this->userService = $userService;

		$this->masterService = $masterService;
		$this->hhaLogService = $hhaLogService;
		$this->agencyService = $agencyService;
		$this->languageService = $languageService;
	}


	public function index(Request $request)
	{
		ini_set('memory_limit', '4096M');

		$data['menu'] = "Patient List";
		$data['user'] = $user = auth()->user();

		if ($user->agency_fk != "") {
			$checkForAgencyDeteleted = Agency::getDetailsByAgencyId($user->agency_fk);
			if (!isset($checkForAgencyDeteleted->id)) {
				return redirect('support_error');
			}
		}

		$angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['agencyList'] = $angecyList;
		$agency_fk = $data['agency_fk'] = request('agency_fk');
		$full_name = $data['full_name'] = request('first_name');
		$mobile = $data['mobile'] = request('mobile');

		$status = $data['status'] = request('status');

		$appointment_date = $data['appointment_date'] = request('appointment_date');
		$location_id = $data['location_id'] = request('locationId');
		$service_id = $data['service_id'] = request('service_id');

		$type = $data['type'] = request('type');
		$created_date = $data['created_date'] = request('created_date');
		$due_date = $data['due_date'] = request('due_date');
		$sms_status = $data['sms_status'] = request('sms_status');

		$record_form = $data['record_form'] = request('record_form');
		$selected_discipline = $data['selected_discipline'] = request('dicipline');
		$traning_date = $data['traning_date'] = request('traning_date');

		$assign_user_id = $data['assign_user_id'] = request('assign_user_id');
		$is_archive = $data['is_archive'] = request('is_archive');
		$is_reviewed = $data['is_reviewed'] = request('is_reviewed');
		$isPastShow = request('is_past_show');
		$data['isPastShow'] = $isPastShow;
		$data['patient_code'] = $patient_code = request('patient_code');
		//get
		$traning_status = request('traning_status');
		$data['selected_sms_status'] = request('sms_status') != null ? explode(',', request('sms_status')) : [];
		$data['selected_status'] = explode(',', request('status'));
		$data['selected_agency_fk'] = explode(',', request('agency_fk'));
		$data['selected_service_id'] = explode(',', request('service_id'));
		$data['selected_assign_user_id'] = explode(',', request('assign_user_id'));
		$data['selected_location_id'] = explode(',', request('locationId'));
		$data['inservice_date'] = $inservice_date = request('inservice_date');
		$data['selected_training_status'] = explode(',', request('traning_status'));
		$data['completed_date'] = $completed_date = request('completed_date');
		$data['follow_up_date'] = $follow_up_date = request('follow_up_date');

		$data['selected_transition_aid'] = $transistion_aid = request('transition_aid');
		$data['selected_language_id'] = $language_id = request('language_id');
		$data['last_status_update'] = $last_status_update = request('last_status_update');
		$data['last_status_updated_by_id'] = $last_status_updated_by_id = request('last_status_updated_by_id');
		$data['last_status_updated_by_name'] = $last_status_updated_by_name = request('last_status_updated_by_name');

		$data['agency_updated_by'] = $agency_updated_by = request('agency_updated_by');
		$data['agency_updated_by_id'] = $agencyRepUserId = request('agency_updated_by_id');
		$data['agency_updated_by_name'] = $agency_updated_by_name = request('agency_updated_by_name');
		$data['token_input_agency_id'] = $request->token_input_agency_id;
		
		$data['doctor_list'] = Cache::get('patient_doctor_list', function () {
			return $this->DoctorService->getDoctorList();
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
		$data['dob'] = $dob = request('dob');
		$data['agency_filter_type'] = $agency_filter_type = request('agency_filter_type');
		$data['service_filter_type'] = $service_filter_type = request('service_filter_type');
		$data['branch_filter_type'] = $branch_filter_type = request('branch_filter_type');
		$data['medication_list'] = $medication_list = request('medication_list');
		$data['insurance_elg'] = $insurance_elg = request('insurance_elg');
		$data['debug']= $debug = $request->debug;
		$data['mdo_tag'] = $mdo_tag = request('mdo_tag');
		$data['filter_branch_id'] = $filter_branch_id = request('filter_branch_id');
		$data['state'] = $state = request('state');
		$data['agency_status'] = $agency_status = request('agency_status');
		$data['referral_type'] = $referral_type = request('referral_type');
		$data['record_read'] = $record_read = request('record_read');
		$data['appointmentPermission'] = DateWiseAgencyAccessHelper::getDateWiseAgencyAccess();
		$field_data = $this->fieldMasterService->getAgencyStatusData();
		$allStatuses = $allStatusIds = [];
		foreach ($field_data as $item) {
			$decoded = json_decode($item['options'], true);

			if (is_array($decoded)) {
				$allStatuses = array_merge($allStatuses, $decoded);
			}
			$allStatusIds[] = $item['id'];
		}
		$data['field_data'] = $allStatuses;

		$agency_enable_review = 0;
		if (!empty($user->agency_fk)) {
			$userAgency = $this->agencyService->getDetailsById($user->agency_fk);
			$agency_enable_review = $userAgency ? (int) $userAgency->enable_review : 0;
		}
		$data['agency_enable_review'] = $agency_enable_review;
		$query = $this->PatientService->getData("", $full_name, "", $mobile, $status, "", $appointment_date, $agency_fk, $location_id, $service_id, $type, $created_date, $sms_status, $record_form, $due_date, $assign_user_id, $is_archive, $isPastShow, $selected_discipline, $patient_code, $inservice_date, $completed_date, $follow_up_date, $traning_date, $created_by, $debug, $traning_status, $transistion_aid, $language_id, $dob, $last_status_update, $last_status_updated_by_id,$agency_filter_type,$service_filter_type, $medication_list, $insurance_elg,$mdo_tag,$filter_branch_id,$branch_filter_type,$state,$agency_status,$allStatusIds,$referral_type,$agencyRepUserId,$record_read,$is_reviewed,$agency_enable_review);

		$getAgencyRepyUser = Cache::get('patient_get_user_agency_rep', function () {
			return $this->userService->getAllUserListWithAgency();
			
		}, 10 * 60); //

		foreach ($query as $vsl) {
			$agencyId = $vsl->agency_id;
			$getAssignNyUser = Cache::get('patient_get_user_' . $vsl->assign_user_id, function () use ($vsl) {
				return User::getDetailsById($vsl->assign_user_id);
			}, 10 * 60); //
			$assign_fname = '';
			$assign_lname = '';
			if (isset($getAssignNyUser->first_name) && $getAssignNyUser->first_name != '') {
				$assign_fname = $getAssignNyUser->first_name;
			}
			if (isset($getAssignNyUser->last_name) && $getAssignNyUser->last_name != '') {
				$assign_lname = $getAssignNyUser->last_name;
			}
			$agency = $angecyList->firstWhere('id', $vsl->agency_id);
			$vsl->enable_portal_archive = $agency->enable_portal_archive;
			$vsl->agency_name = $agency->agency_name ?? "";

			$location = $locationList->firstWhere('id', $vsl->location_id);
			if (isset($_GET['debug']) && $_GET['debug'] == 12) {
				if (trim($vsl->location_id) != "") {
					echo "<pre>";
					print_r($vsl);
				}
			}
			$vsl->location_name = ($location) ? $location->address1 : "";
			$locationSchedule = $locationschedule->firstWhere('id', $vsl->appoinment_time_id);
			$vsl->start_time = ($locationSchedule) ? $locationSchedule->start_time : "";
			$vsl->end_time = ($locationSchedule) ? $locationSchedule->end_time : "";



			$vsl->assign_user_name = $assign_fname . ' ' . $assign_lname;
			$uFname = $vsl->uFname ?? "";
			$uLname = $vsl->uLname ?? "";
			$vsl->created_by_username = $uFname . ' ' . $uLname;
			$explode = explode(',', $vsl->service_id);
			$newss = $vsl->service_id;
			if ($newss != '') {
				$sins = Cache::get('patient_master_' . implode(",", $explode), function () use ($explode, $agencyId) {

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

			/******************Reason Query */
			$reasonName = "";
			if ($vsl->reason_id != "") {
				$reasonId = $vsl->reason_id;
				$getReasonsData = Cache::get('patient_master_' . $vsl->reason_id, function () use ($reasonId) {

					return Master::select('name')->where('id', $reasonId)->where('del_flag', 'N')->first();
				}, 10 * 60);
				$reasonName = $getReasonsData->name ?? "";
			}

			$vsl->reason_name = $reasonName;
			$vsl->otherreasonname = $vsl->other_reason??'';
			$nurse = User::getNurses();
			$langArray = array();
			foreach($nurse as $nurse){
				if(isset($nurse->nurseLanguages)){
					$languages = array();
					foreach($nurse->nurseLanguages as $nLang){
						if(isset($nLang->languages[0])){
							
						$languages[] = $nLang->languages[0]['name'];
						}
					}
					$langArray[$nurse['id']]['language'] = implode(',', $languages);
				}
			}
			$nurse = $langArray;
			if (!empty($vsl->telehealth_time_frame)) {
				$vsl->telehealth_time_slot = $vsl->telehealth_time_frame;
				$rawNurseId = $vsl->telehealth_nurse;
				if (!empty($rawNurseId) && isset($nurse[$rawNurseId])) {
					$vsl->telehealth_nurse = 'C#' . $rawNurseId . '(' . $nurse[$rawNurseId]['language'] . ')';
				}
			} elseif (isset($vsl->telehealth_time_slot)) {
				$telhealth = $this->telehealthLocationScheduleEventService->getTelehalthappointemntScheduledata($vsl->telehealth_time_slot);
				$vsl->telehealth_time_slot = isset($telhealth['start_time']) ? $telhealth['start_time'] . ' - ' . $telhealth['end_time'] : '';
				$nLanguage="";
				if(!empty($telhealth['nurse_id']) && isset($nurse[$telhealth['nurse_id']]) && array_key_exists($telhealth['nurse_id'],$nurse)){
					$nLanguage = isset($telhealth['nurse_id']) ? 'C#'.$telhealth['nurse_id'].'('.$nurse[$telhealth['nurse_id']]['language'].')' : '';
				}
				$vsl->telehealth_nurse = $nLanguage;
			}

			$vsl->agencyRepUser = $getAgencyRepyUser[$vsl->agency_user_id]??"";
		}
		$data['query'] = $query;

		if(isset($_GET['debug']) && $_GET['debug']==12345){
			echo "<pre>";print_r($data['query']);die();
		}


		$data['serviceList'] = Cache::get('patient_master_services', function () use ($user, $agency_fk) {
			if ($agency_fk != "") {
				$agencyId = $agency_fk;
			} else {
				$agencyId = $user->agency_fk;
			}
			if($user->agency_fk !=""){
				$getAgencyWiseList = $this->AgencyWiseServiceService->ServiceListNewWithoutNyBestUser("",$agencyId);
				if (!empty($getAgencyWiseList[0])) {
					return $getAgencyWiseList;
				} else {
					return Master::getServiceRequestWithDisabled(1);
				}
			}else{
				return Master::getServiceRequestWithDisabled(1);
			}

		}, 10 * 60);

		$data['assign_user_list'] = Cache::get('patient_master_nubest_user', function () {
			return User::getNYBestUserData();
		}, 10 * 60);

		// $total_record = $this->PatientService->getTotalCount("", $full_name, "", $mobile, $status, "", $appointment_date, $agency_fk, $location_id, $service_id, $type, $created_date, $sms_status, $record_form, $due_date, $assign_user_id, $is_archive, $isPastShow, $selected_discipline, $patient_code, $inservice_date, $completed_date, $follow_up_date, $traning_date, $created_by, $traning_status);
		// echo $user['id'];
		// if($user['id']==482)
		{
			$total_record = $this->PatientService->getTotalCount("", $full_name, "", $mobile, $status, "", $appointment_date, $agency_fk, $location_id, $service_id, $type, $created_date, $sms_status, $record_form, $due_date, $assign_user_id, $is_archive, $isPastShow, $selected_discipline, $patient_code, $inservice_date, $completed_date, $follow_up_date, $traning_date, $created_by, $traning_status, $transistion_aid, $language_id, $dob, $last_status_update, $last_status_updated_by_id,$agency_filter_type,$service_filter_type, $medication_list, $insurance_elg,$mdo_tag,$filter_branch_id, $branch_filter_type,$state,$agency_status,$allStatusIds,$referral_type,$agencyRepUserId,$record_read,$is_reviewed,$agency_enable_review);
			$total_record = $total_record[0]->count;
		}
		$data['total_record'] = $total_record;

		$data['agency_user_list'] = Cache::get('agency_user_list', function () use ($user) {
			return UserHelper::getAgencyWiseUserList($user->agency_fk);
		}, 10 * 60);

		$data['language_list'] = Cache::get('language_list', function () use ($user) {
			return Language::getLanguageList();
		}, 10 * 60);

		$data['masterData'] = Cache::get('masters_data', function () use ($user) {
			return Master::getAllDataByMasterTypeFk(array(17, 26));
		}, 10, 60);
		$data['referralTypeList'] = Master::getAllDataByMasterTypeFk(array(31));
		$data['statuses'] = Utility::getUniqueStatusDataNew();
		if (isset($_GET['hello1111']) && $_GET['hello1111'] == 112) {
			return view("patient/patient_list_new", $data);
		}

		$data['userAgencyList'] = Cache::get('user_agecy_list_auth', function () use ($user) {
			return $this->userWiseAgencyService->getAgencyListByUserId($user->id);
		}, 10 * 60);

		return view("patient/patient_list", $data);
	}


	public function add()
	{

		$data['menu'] = "Add Patient";
		$data['user'] = $user = auth()->user();
		$agencyObj = Common::getAgencyDetails();

		if (isset($agencyObj->service_md_appointment) && $agencyObj->service_md_appointment == 0) {

			return redirect('support_error');
		}

		return redirect('patient-add-new');
		$data['serviceList'] = Master::getServiceRequestWithDisabled();
		$data['agencyList'] = Agency::getAgencyList();
		$data['masterData'] = Master::getAllDataByMasterTypeFk(array(17, 26));
		$data['languages'] = Language::getLanguageList();
		$data['insuranceList'] = $this->insuranceMasterService->getInsuranceMasterList();

		$data['userAgencyList'] = $this->userWiseAgencyService->getAgencyListByUserId($user->id);
		return view("patient/patient_add", $data);
	}

	public function save(Request $request)
	{

		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'type' => 'required',
			'last_name' => 'required',
			'mobile' => 'required|numeric|digits_between:10,15',
			'agency_id' => 'required',
			'service_id' => 'required',

		]);
		if ($validator->fails()) {
			return redirect("/patient/add")
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {

			$first_name = request('first_name');
			$middle_name = request('middle_name');
			$last_name = request('last_name');
			$type = request('type');
			$age = '';
			if (request('dob') != '') {
				$age = date('Y-m-d', strtotime(request('dob')));
			}
			$phone = request('phone');
			$gender = request('gender');
			$message = request('message');

			$data = array(
				'first_name' => $first_name,
				'middle_name' => $middle_name,
				'last_name' => $last_name,
				'type' => $type,
				'dob' => $age,
				'fu_date' => date('Y-m-d', strtotime(request('fu_date'))),
				'due_date' => date('Y-m-d', strtotime(request('due_date'))),
				'phone' => $phone,
				'mobile' => $request->input('mobile'),
				'agency_id' => $request->input('agency_id') != null ? $request->input('agency_id') : $user['agency_fk'],
				'gender' => $gender,
				'remarks' => $message,
				'service_id' => implode(',', request('service_id')),
				//'agency_id' => $user['agency_fk'],
				'patient_code' => $request->input('patient_code'),
				'diciplin' => $request->input('diciplin'),
				'language' => Common::getOrCreateLanguageId($request->input('language')),
				'address1' => $request->input('address1'),
				'address2' => $request->input('address2'),
				'state' => $request->input('state'),
				'city' => $request->input('city'),
				'zip_code' => $request->input('zip_code'),
				'county' => $request->input('county'),
				'payment_type' => $request->input('payment_type'),
				'insurance_id' => $request->input('insurance_id'),
				'insurance_name' => $request->input('insurance_name'),
				'cin' => $request->input('cin'),
				'record_read' => 0,
				'emergency_contact_name' => $request->emergency_contact_name,
				'emergency_phone' => $request->emergency_phone,
				'location_branch' => $request->location_branch,
				'ssn' => $request->ssn,
				'email' => $request->email,
				'transition_aid' => $request->transition_aid,
				'medicare_no' => $request->medicare_no,

			);

			$other_name = "";
			if ($gender == 'other') {
				$other_name = $request->other_name;
			}
			$data['other_gender'] = $other_name;

			if ($request->input('insurance_name') == 'other') {
				$data['other_insurance_name'] = $request->other_insurance_name;
			}
			if ($type == 'Caregiver') {
				$data['link_hha_caregiver'] = request('caregiver_id');
			} else {
				$data['link_hha_patient'] = request('caregiver_id');
			}
			if ($user['user_type_fk'] == 184) {
				$data['agency_id'] = request('agency_id');
			}
			if ($user['agency_id'] == 106) {
				$data['hamaspik_payment'] = request('hamaspik_payment');
			}
			if ($user['agency_fk'] != '') {
				$agencyid = $user['agency_fk'];
			} else {
				$agencyid = request('agency_id');
			}
			$getAgencyName = Agency::getDetailsByAgencyId($agencyid);
			$agencyname = '';
			if (isset($getAgencyName->agency_name) && $getAgencyName->agency_name != '') {
				$agencyname = $getAgencyName->agency_name;
			}
			if ($type == 'Caregiver') {



				$unitId = uniqid();
				$url = URL::to('/') . '/ap/' . $unitId;
				$namearray = array();
				foreach (request('service_id') as $vdl) {
					$getMaster = Master::select('name')->where('id', $vdl)->where('del_flag', 'N')->first();
					$namearray[] = $getMaster->name;
				}



				if ($request->input('language') != '' && strtolower($request->input('language')) == 'spanish') {
					$message = 'Aviso de ' . $agencyname . ': Usted tiene prevista una cita con el médico. Su ' . implode(',', $namearray) . ' vencerán pronto. Haga clic en el enlace a continuación para programar su cita Enlace ' . $url . ' . No responda a este mensaje de texto, para cualquier pregunta, llame al (718) 972-3693';
				} else {
					$message = 'Notice from ' . $agencyname . ' : Your ' . implode(',', $namearray) . ' expiring soon and you will need to update it to continue employment and be active with ' . $agencyname . '. Please click the link below to schedule your appointment with NYBest Medical Care ' . $url . '. Do not reply to this text message, for any questions please call NYBest Medical @ 718-972-3693 or email appointment@nybestmedical.com ';
				}
				$data['sms'] = $message;
				$data['key'] = $unitId;
			}

			$insert = $this->PatientService->save($data);

			if ($insert) {
				if ($type == 'Caregiver') {


					if ($getAgencyName->is_sms == 1) {
						$getStopSMSServices = Utility::stopSMSService($data['agency_id']);
						$array = array_intersect($request->service_id, $getStopSMSServices);
						if (count($array) == 0) {
							$this->SmsService->AgencyWiseSmsDynamic($insert, $request->input('mobile'), $message);
						}
					}
				}
				$addAppintment = ["patient_id" => $insert, "location_id" => null, "doctor_id" => null, "service_id" => implode(',', request('service_id')), "appointment_date" => null, "appointment_time" => null, "status" => "Pending", "created_by" => $user->id,];
				Appointment::create($addAppintment);

				$patientServiceLastId = $this->patientServicesRequest->save([
					'patient_id' => $insert,
					'flag' => 3
				]);

				$addServiceIds = $request->input('service_id');

				if (is_array($addServiceIds)) {
					foreach ($addServiceIds as $serviceId) {
						$patientWiseServiceRequest = [
							'patient_id' => $insert,
							'service_id' => $serviceId,
							'patient_service_request_id' => $patientServiceLastId,
						];

						$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
					}
				}

				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add Appointment',
					'link' => url('/patient/add'),
					'module' => 'Patient Appointment',
					'object_id' => $insert,
					'message' => $user->first_name . ' ' . $user->last_name . ' has added Appointment',
					'new_response' => serialize($data),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);

				if (Common::checkAgencyLogin() && $user['agency_fk'] != null) {
					$agency = Agency::find($user['agency_fk']);
					$nybestUsers = User::getNyBestUsersList();
					if (count($nybestUsers) > 0) {
						foreach ($nybestUsers as $user) {
							$message = 'Hello ' . $user->first_name . ' ' . $user->last_name . ' Notification From ' . $agency->agency_name . ' Added Appointment';
							$details = [
								"greeting" => 'NyBestMedical',
								'actionText' => 'Add Appointment',
								'body' => $message,
								'actionURL' => url('/patient/view') . '/' . $insert,
								'record_id' => $user->id,
							];
							// $user->notify(new MyFirstNotification($details));
						}
					}
				}

				$portal_name = $first_name . ' ' . $last_name;
				Utility::insertNotificationsOfUser($agencyid, $insert, $portal_name, $type);
			}
			// $insert = Agency::insertGetId($data);

			$emails = isset($getAgencyName->notification_email) ? $getAgencyName->notification_email : "";
			$allemails = array();
			if ($emails != '') {
				$newsemail = explode(',', $emails);
				if (count($newsemail) > 0) {
					foreach ($newsemail as $vas) {
						if (trim($vas) != '') {

							if (trim($vas) != 'li@qualityny.com') {
								$allemails[] = $vas;
							}
						}
					}
				}
			}
			$username = $user['first_name'] . ' ' . $user['last_name'];

			$discipline = $request->input('diciplin');

			$subject = "[" . $agencyname . "] NYBest Medical Care New record added";
			
			$email = "";
			$emailData = array(
				'username' => $username,
				'agencyname' => $agencyname,
				'insert' => $insert,
				'type' => $type,
				'first_name' => $first_name,
				'last_name' => $last_name,
			);
			$messages = Utility::getHtmlContent('email_template.email_appointment_create', $emailData);
			try {

				//code...
				$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages, $user) {
					$message->to($email, "Ny Best Medicals")->cc($user->email)
						->subject($subject)->html($messages);
				});

				$sendEmailNotication = $this->SendEmailNotificationSerivce->generalAddAppointmentNotificationEmail($type, "Add New Record", request('service_id'), $subject, $messages);
				if ($type != "" && $agencyid != "") {
					$sendEmailNotication = $this->SendEmailNotificationSerivce->addAppointment($type, $agencyid, request('service_id'), $subject, $messages);
				}
			} catch (\Throwable $th) {
				//throw $th;
			}



			if ($insert) {
				Session::flash('success', 'Patient appointment successfully added.');
				return redirect('/appointment');
			} else {
				Session::flash('error', 'Sorry, something went wrong. Please try again.');
				return redirect('/patient/add');
			}
		}
	}

	public function edit($id)
	{
		$data['menu'] = "user";
		$data['user'] = $user = auth()->user();

		$data['id'] = $id;
		if(in_array($user->id,Utility::agencyPortalRolePermission())){
			abort(404);
		}

		if($user->agency_fk !=""){
			$getAgencyAccess = DateWiseAgencyAccessHelper::getDateWiseAgencyAccess();
			if(in_array('EditAppointment',$getAgencyAccess)){
				abort(404);
			}
		}
		$data['patient'] = $this->PatientService->getDetailById($id);
		if (isset($data['patient']->id)) {
			$data['agencyList'] = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
			$getAgencyWiseServiceList = $this->AgencyWiseServiceService->ServiceListNewWithoutNyBestUser($data['patient']->type, $data['patient']->agency_id);

			if (!empty($getAgencyWiseServiceList[0])) {

				foreach ($getAgencyWiseServiceList as $val) {
					$val->types = $val->type;
				}
				$data['serviceList'] = $getAgencyWiseServiceList;
			} else {
				$data['serviceList'] = Master::getServiceRequestWithDisabled();
			}

			$data['languages'] = Language::getLanguageList();
			$data['masterData'] = Master::getAllDataByMasterTypeFk(array(17, 26));
			$data['insuranceList'] = $this->insuranceMasterService->getInsuranceMasterList();
			$data['userAgencyList'] = $this->userWiseAgencyService->getAgencyListByUserId($user->id);
			return view('patient/patient_edit', $data);
		} else {
			abort(404);
		}
	}

	public function update(Request $request, $id)
	{
		$user = auth()->user();
		$data['id'] = $id;
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'type' => 'required',

			// 'payment_type' => 'required',
			// 'phone' => 'required|numeric|digits_between:10,15',
			//   'mobile' => 'required|numeric|digits_between:10,15',
			'gender' => 'required',
			'agency_id' => 'required',

		]);
		if ($validator->fails()) {
			return redirect("/patient/edit/$id")
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {

			$data['patient'] = $this->PatientService->getDetailById($id);
			if (isset($data['patient']->id)) {
				$first_name = request('first_name');
				$middle_name = request('middle_name');
				$last_name = request('last_name');
				$age = '';
				if (request('dob') != '') {
					$age = date('Y-m-d', strtotime(request('dob')));
				}

				$phone = request('phone');
				$gender = request('gender');
				$message = request('message');
				$type = request('type');
				$branch_name = "";
				if(isset($request->branch_id) && !empty($request->branch_id)){
					$branchData = $this->branchListService->getById($request->branch_id);
					$branch_name = $branchData->branch_name;
				}
				$data = array(
					'first_name' => $first_name,
					'middle_name' => $middle_name,
					'last_name' => $last_name,
					'full_name' => $first_name . ' ' . $last_name,
					'dob' => $age,
					'fu_date' => date('Y-m-d', strtotime(request('fu_date'))),
					'due_date' => date('Y-m-d', strtotime(request('due_date'))),
					'phone' => $phone,
					'mobile' => $request->input('mobile'),
					'type' => $type,
					'gender' => $gender,
					'agency_id' => $request->input('agency_id'),
					'remarks' => $message,
					'patient_code' => $request->input('patient_code'),
					'diciplin' => $request->input('diciplin'),
					'language' => Common::getOrCreateLanguageId($request->input('language')),
					'address1' => $request->input('address1'),
					'address2' => $request->input('address2'),
					'state' => $request->input('state'),
					'city' => $request->input('city'),
					'zip_code' => $request->input('zip_code'),
					'county' => $request->input('county') == "County not found" ? '' : $request->input('county'),

					'insurance_id' => $request->input('insurance_id'),
					'insurance_name' => $request->input('insurance_name'),
					'cin' => $request->input('cin'),
					'emergency_contact_name' => $request->emergency_contact_name,
					'emergency_phone' => $request->emergency_phone,
					'location_branch' => !empty($branch_name) ? $branch_name : $request->location_branch,
					'ssn' => $request->ssn,
					'email' => $request->email,
					'transition_aid' => $request->transition_aid,
					'medicare_no' => $request->medicare_no,
					'other_gender' => $request->other_name,
					'branch_id' => $request->branch_id??NULL,
				);

				if(auth()->user()->agency_fk ==""){
					$data['service_id']=implode(',', request('service_id'));
					$data['payment_type']=$request->input('payment_type');
				}
				$other_name = "";
				if ($gender == 'other') {
					$other_name = $request->other_name;
				}

				$data['other_gender'] = $other_name;
				if ($request->input('insurance_name') == 'other') {
					$data['other_insurance_name'] = $request->other_insurance_name;
				}
				if ($data['agency_id'] == 106) {
					$data['hamaspik_payment'] = request('hamaspik_payment');
				}

				$getExistingData = $this->PatientService->getDetailById($id);
				$update = $this->PatientService->update($data, array('id' => $id));
				$getNewData = $this->PatientService->getDetailById($id);
				$ipaddress = Utility::getIP();


				$insertLog = [
					'type' => 'Update Appointment',
					'link' => url('/patient/update'),
					'module' => 'Patient Appointment',
					'object_id' => $id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment',
					'new_response' => serialize($getNewData->toArray()),
					'old_response' => serialize($getExistingData->toArray()),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				try {
					if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
						$agencyNotifyData = array(
							'agencyid' => $request->input('agency_id'),
							'title' => 'Update Appointment',
							'record_id' => $id,
							'record_type' => 'Appointment',
							'msg' => '',
							'res_data' => serialize($data)
						);
						Common::insertAgencyNotificationsOfUser($agencyNotifyData);
					}
				} catch (\Throwable $th) {}
				Session::flash('success', 'Patient appointment successfully update.');
				return redirect('/patient/view/' . $id);
			}
		}
	}

	public function delete($id)
	{
		$user = auth()->user();

		$data['id'] = $id;
		$update = $this->PatientService->SoftDelete(array('deleted_flag' => 'Y'), array('id' => $id));

		if ($update) {
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Delete Appointment',
				'link' => url('/patient/delete'),
				'module' => 'Patient Appointment',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment',
				'new_response' => serialize($data),
				'old_response' => serialize($data),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			Session::flash('success', 'Patient successfully delete.');
			return redirect('/appointment');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
			return redirect('/appointment');
		}
	}

	public function agencyExport(Request $request)
	{

		// ini_set('memory_limit', '-1');
		$user = auth()->user();

		$full_name = $data['full_name'] = request('first_name');

		$mobile = $data['phone'] = request('phone');
		$age = $data['age'] = request('age');
		$agency_fk = $data['agency_fk'] = request('agency_fk');
		$status = $data['status'] = request('status');
		$doctor_id = $data['doctor_id'] = request('doctor_id');
		$appointment_date = $data['appointment_date'] = request('appointment_date');
		$location_id = $data['location_id'] = request('location_id');
		$service_id = $data['service_id'] = request('service_id');
		$type = $data['type'] = request('type');
		$assign_user_id = $data['assign_user_id'] = request('assign_user_id');
		$created_date = $data['created_date'] = request('created_date');
		$due_date = $data['due_date'] = request('due_date');

		$selected_discipline = $data['selected_discipline'] = request('dicipline');
		$sms_status = $data['sms_status'] = request('sms_status');

		$record_form = $data['record_form'] = request('record_form');

		$is_archive = $data['is_archive'] = request('is_archive');
		$isPastShow = request('is_past_show');
		$data['patient_code'] = $patient_code = request('patient_code');
		$data['inservice_date'] = $inservice_date = request('inservice_date');

		$data['completed_date'] = $completed_date = request('completed_date');
		$data['follow_up_date'] = $follow_up_date = request('follow_up_date');
		$traning_date = $data['traning_date'] = request('traning_date');
		$transistion_aid = $data['transition_aid'] = request('transition_aid');
		$data['language_id'] =$language_id = request('language_id');
		$traning_status = request('traning_status');
		$dob = $data['dob'] = request('dob');
		$last_status_update = $data['last_status_update'] = request('last_status_update');
		$last_status_updated_by_id = $data['last_status_updated_by_id'] = request('last_status_updated_by_id');
		$agency_filter_type = $data['agency_filter_type'] = request('agency_filter_type');
		$service_filter_type = $data['service_filter_type'] = request('service_filter_type');
		$medication_list = $data['medication_list'] = request('medication_list');
		$insurance_elg = $data['insurance_elg'] = request('insurance_elg');
		$debug = $request->debug;
		$mdo_tag = $data['mdo_tag'] = request('mdo_tag');
		$filter_branch_id = $data['filter_branch_id'] = request('filter_branch_id');
		$state = $data['state'] = request('state');
		$branch_filter_type = $data['branch_filter_type'] = request('branch_filter_type');

		// Get selected columns from request
		$selectedColumnsJson = request('columns');
		$selectedColumns = [];
		if ($selectedColumnsJson) {
			$selectedColumns = json_decode($selectedColumnsJson, true);
		}
		$agency_status = $data['agency_status'] = request('agency_status');
		$referral_type = $data['referral_type'] = request('referral_type');
		$record_read = $data['record_read'] = request('record_read');
		$field_data = $this->fieldMasterService->getAgencyStatusData();
		$allStatusIds = [];
		foreach ($field_data as $item) {
			$allStatusIds[] = $item['id'];
		}
		$users = $this->PatientService->getDataExport("", $full_name, "", $mobile, $status, "", $appointment_date, $agency_fk, $location_id, $service_id, $type, $created_date, $sms_status, $record_form, $due_date, $assign_user_id, $is_archive, $isPastShow, $selected_discipline, $patient_code, $inservice_date, $completed_date, $follow_up_date, $traning_date, $request->created_by, $traning_status, $transistion_aid, $language_id, $dob, $last_status_update, $last_status_updated_by_id,$agency_filter_type,$service_filter_type, $medication_list, $insurance_elg,$debug,$mdo_tag,$filter_branch_id,$branch_filter_type,$state,$agency_status,$allStatusIds,$referral_type,$record_read);

		$languageMap = $this->languageService->getAllLanguagesById();
		$slotNurseMap = $this->telehealthLocationScheduleEventService->getNurseTimeScheduleData();

		$filename = 'Patient' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

		// Define all possible columns
		$allColumns = [];
		if ($user->agency_fk == 106) {
			$allColumns = array('No', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob', 'Location', 'Appointment Date', 'Appointment Start Time', 'Service', 'Status', 'Notes', "Booked Via", "Assign NyBest User", "Created Date", "Created By", 'Due Date', 'FU Date', 'Is Archive', 'Training Status', 'Completed date', 'Follow Up Date', 'Traning Due Date', 'Location / Branch', 'Reason', 'Language', 'Clinician Code');
		} else {
			$allColumns = array('No', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob', 'Location', 'Appointment Date', 'Appointment Start Time', 'Service', 'Status', 'Notes', "Booked Via", "Assign NyBest User", "Created Date", "Created By", 'Due Date', 'FU Date', 'Is Archive', 'Completed date', 'Follow Up Date', 'Location / Branch', 'Reason', 'state', 'Language', 'Clinician Code');
			if ($user->user_type_fk == 184) {
				$allColumns[] = 'Training Date';
				$allColumns[] = 'Training Status';
				$allColumns[] = 'Last Status Update Date';
				$allColumns[] = 'Last Status Updated By';
				$allColumns[] = 'Referral Type';
			}
		}

		// Use selected columns if provided, otherwise use all columns
		$columns = !empty($selectedColumns) ? $selectedColumns : $allColumns;

		// Create column index map for filtering data
		$columnIndexMap = [];
		$useFiltering = !empty($selectedColumns) && count($selectedColumns) < count($allColumns);

		if ($useFiltering) {
			foreach ($columns as $column) {
				$index = array_search($column, $allColumns);
				if ($index !== false) {
					$columnIndexMap[$column] = $index;
				}
			}
		}

		$newass = array();
		$callback = function () use ($users, $columns, $allColumns, $columnIndexMap, $user,$languageMap,$slotNurseMap) {
			// Clean any output buffers to prevent empty lines
			if (ob_get_level() > 0) {
				ob_clean();
			}
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			$cnt = 1;
			foreach ($users as $list) {
				$getAssignNyUser = User::getDetailsById($list->assign_user_id);
				$assign_fname = '';
				$assign_lname = '';
				if (isset($getAssignNyUser->first_name) && $getAssignNyUser->first_name != '') {
					$assign_fname = $getAssignNyUser->first_name;
				}
				if (isset($getAssignNyUser->last_name) && $getAssignNyUser->last_name != '') {
					$assign_lname = $getAssignNyUser->last_name;
				}

				// Trim extra spaces from name concatenation
				$assignName = trim($assign_fname . ' ' . $assign_lname);
				$date = '';
				if ($list->dob != '0000-00-00' && $list->dob != '') {
					$date = Utility::convertMDY($list->dob);
				}
				$Adate = '';
				if ($list->appointment_date != '0000-00-00 00:00:00' && $list->appointment_date != '') {
					$Adate = Utility::convertMDY($list->appointment_date);
				} else if ($list->telehealth_date_time != '0000-00-00 00:00:00' && $list->telehealth_date_time != '') {
					$Adate = Utility::convertMDY($list->telehealth_date_time);
				}
				$ATime = '';
				if ($list->start_time != '') {
					$ATime = date('h:i A', strtotime($list->start_time));
				} else if (!empty($list->telehealth_time_frame)) {
					$ATime = $list->telehealth_time_frame;
				} else if ($list->telehealth_time_slot != "") {
					$telhealth = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($list->telehealth_time_slot);
					$ATime = date('h:i A', strtotime($telhealth['start_time'])) . '-' . date('h:i A', strtotime($telhealth['end_time']));
				}

				$servie = '';
				if (isset($list->service_id) && $list->service_id != '') {
					$services = explode(',', $list->service_id);
					$final = [];
					foreach ($services as $val) {
						if ($val != '') {
							$final[] = $val;
						}
					}
					if (!empty($final)) {

						$services = Master::whereRaw('id IN (' . implode(',', $final) . ')')->where('del_flag', 'N')->get();

						foreach ($services as $kke) {
							$newass[$list->id][] = $kke->name;
						}
					}


					if (!empty($newass[$list->id][0])) {
						$servie = implode(',', $newass[$list->id]);
					}
				}
				$created_by_username =$list->uFirstName . ' ' . $list->uLastName;

				$created_date = '';
				if ($list->created_date != "" || $list->created_date != NULL) {
					$created_date = date('d/m/Y h:i A', strtotime($list->created_date));
				}

				$due_date = '';
				if ($list->due_date != "" || $list->due_date != NULL) {
					if ($list->due_date != '1969-12-31') {
						$due_date = date('m/d/Y', strtotime($list->due_date));
					}
				}

				$fu_date = '';
				if ($list->fu_date != "" && $list->fu_date != NULL) {
					if ($list->fu_date != "1969-12-31") {
						$fu_date = date('m/d/Y', strtotime($list->fu_date));
					}
				}



				$isArchive = 'No';
				if ($list->archived_at != '') {
					$isArchive = 'Yes';
				}

				$completedDate = '';
				if ($list->completed_date != '') {
					$completedDate = date('m/d/Y', strtotime($list->completed_date));
				}

				$followUpDate = '';
				if ($list->follow_date != '') {
					$followUpDate = date('m/d/Y', strtotime($list->follow_date));
				}

				$trainingDate = "";
				$trainingDate = date('m/d/Y', strtotime($list->traning_due_date));

				$lastStatusUpdated = '';
				if ($list->last_status_update != '' && $list->last_status_update != "0000-00-00 00:00:00") {
					$lastStatusUpdated = date('m/d/Y h:i A', strtotime($list->last_status_update));
				}

				$reasonStatus = "";
				if ($list->reason_id != "") {
					$reasonData = Master::select('name')->where('id', $list->reason_id)->where('del_flag', 'N')->first();
					$reasonStatus = $reasonData->name ?? "";
				}

				// Prepare patient full name with proper spacing
				$patientFullName = trim($list->first_name . ' ' . $list->middle_name . ' ' . $list->last_name);
				// Remove extra spaces between words
				$patientFullName = preg_replace('/\s+/', ' ', $patientFullName);

				if(strtolower($list->status) =='inactive'){
					$list->status = 'Inactive';
				}
				$languageName  = $languageMap[$list->language] ?? '';
				$nurseId       = $slotNurseMap[$list->telehealth_time_slot] ?? null;
				$clinicianCode = "";
				if (!empty($list->telehealth_time_frame)) {
					$rawNurseId = $list->telehealth_nurse;
					if (!empty($rawNurseId)) {
						$clinicianCode = 'C#' . $rawNurseId;
					}
				} else {
					$clinicianCode = $nurseId ? 'C#' . $nurseId : '';
				}

				// Prepare all data fields based on agency
				if ($user->agency_fk == 106) {
					$allData = array($list->id, $list->agency_name, $list->type, $list->diciplin, $list->patient_code, $patientFullName, $list->phone, $list->gender, $date, $list->location_name, $Adate, $ATime, $servie, $list->status, $list->remarks, $list->appointment_mode, $assignName, $created_date, $created_by_username, $due_date, $fu_date, $isArchive, $list->training_status, $completedDate, $followUpDate, $trainingDate, $list->location_branch, $reasonStatus, $languageName, $clinicianCode);
				} else {
					$allData = array($list->id, $list->agency_name, $list->type, $list->diciplin, $list->patient_code, $patientFullName, $list->phone, $list->gender, $date, $list->location_name, $Adate, $ATime, $servie, $list->status, $list->remarks, $list->appointment_mode, $assignName, $created_date, $created_by_username, $due_date, $fu_date, $isArchive, $completedDate, $followUpDate, $list->location_branch, $reasonStatus, $list->state, $languageName, $clinicianCode);
					if ($user->user_type_fk == 184) {
						$allData[] = $list->trainingDate;
						$allData[] = $list->training_status;
						$allData[] = $lastStatusUpdated;
						// Trim extra spaces from status updated by name
						$statusUpdatedByName = trim($list->sFirstName . ' ' . $list->sLastName);
						$allData[] = $statusUpdatedByName;
						$allData[] = $list->referral_type;
					}
				}

				// Filter data based on selected columns
				$filteredData = [];
				if (!empty($columnIndexMap)) {
					// If columns are selected, filter the data
					foreach ($columns as $column) {
						if (isset($columnIndexMap[$column]) && isset($allData[$columnIndexMap[$column]])) {
							$filteredData[] = $allData[$columnIndexMap[$column]];
						} else {
							// Add empty value if column not found
							$filteredData[] = '';
						}
					}

					fputcsv($file, $filteredData);
				} else {
					// Use all data if no columns selected
					fputcsv($file, $allData);
				}

				$cnt++;
			}

			fclose($file);
		};
		return response()->stream($callback, 200, $headers);
	}

	public function view($id)
	{
		
	
		$data['auth'] = $data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}

		if (isset($_GET['debug']) && $_GET['debug'] == 123) {
			$this->PatientService->getDetailByIdNewDebug($id);
		}

		if($data['user']->agency_fk ==""){
			if(!auth()->user()->can('appointments-view')){
			abort(404);
			}
		}

		$record = $this->PatientService->getDetailByIdNew($id);

		$data['menu'] = "Patient Appointment";
		$data['id'] = $id;
		if (isset($record->id) && $record->id != '') {
			if($record->link_hha_caregiver == 0){
				$record->link_hha_caregiver = NULL;
			}
			if($record->link_hha_patient == 0){
				$record->link_hha_patient = NULL;
			}
			$checkForAgencyDeteleted = Agency::getDetailsByAgencyId($record->agency_id);
			if (empty($checkForAgencyDeteleted->id)) {
				abort(404);
			}
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

			if ($record->type == 'Caregiver') {
				$alayaCareDetails = $this->AlayacareService->getAllDetailsByAlayacreId($record->alaycare_id, $record->agency_id);

				$fullAlayaCareName = '';
				if (isset($alayaCareDetails->id)) {
					$fullAlayaCareName = $alayaCareDetails->first_name . ' ' . $alayaCareDetails->last_name;
				}
				$record->alaycare_name = $fullAlayaCareName;
			}

			if ($record->type == 'Patient') {
				$alayaCareDetails = $this->alayaclientService->getAllDetailsByAlayacreId($record->alaycare_id, $record->agency_id);

				$fullAlayaCareName = '';
				if (isset($alayaCareDetails->id)) {
					$fullAlayaCareName = $alayaCareDetails->first_name . ' ' . $alayaCareDetails->last_name;
				}
				$record->alaycare_name = $fullAlayaCareName;
			}

			$remoteDetails = $this->robortService->getDetailsByPatientWithAgencyId($record->robort_id,$record->agency_id);

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

				$getHHXCaregiverDetails = HHACaregiversHelper::getCaregiverDetailsByAgencyId($record->link_hha_caregiver, $record->agency_id);

				if (!$getHHXCaregiverDetails) {
					$getHHXCaregiverDetails = HHACaregiversHelper::getCaregiverDetails($record->link_hha_caregiver);
				}

				if (isset($getHHXCaregiverDetails->id)) {
					$fullName = $getHHXCaregiverDetails->first_name . ' ' . $getHHXCaregiverDetails->last_name . ' ( ' . $getHHXCaregiverDetails->caregiver_code . ' ) ';
					$fullHHXCaregiverName = $fullName;
				}
			}

			$fullHHXPatientName = "";
			$taskHealthPatientName = "";
			if ($record->type == 'Patient') {
				$getHHXPatientDetails = $this->hhaPatientService->getDetailsByPatientID($record->link_hha_patient,$record->agency_id);
				if (isset($getHHXPatientDetails->id)) {
					$fullName = $getHHXPatientDetails->first_name . ' ' . $getHHXPatientDetails->last_name . ' ( ' . $getHHXPatientDetails->admission_id . ' ) ';
					$fullHHXPatientName = $fullName;
				}

				if(!empty($record->task_health_link)){
					$taskHealthRecord = $this->taskHealthMasterService->getTaskHealthDetails($record->task_health_link);
					if (isset($taskHealthRecord->id)) {
						$taskHealthPatientName = $taskHealthRecord->first_name . ' ' . $taskHealthRecord->last_name . ' ( ' . $taskHealthRecord->id . ' ) ';
					}
				}
			}

			$record->hhx_caregiver_name = $fullHHXCaregiverName;
			$record->hhx_patient_name = $fullHHXPatientName;
			$record->task_health_patient_name = $taskHealthPatientName;
			$data['hhaStatusList'] = $this->hhaCaregiverMedicalService->getStatusList();
			$data['agencyDetails'] = Agency::getDetailsByAgencyId($record->agency_id);
			$data['agencyDetails']->enable_task_health = Agency::getStatusOfTaskHealth($record->agency_id);
			$data['agencyNotes'] = $this->agencyNoteService->getActiveByAgency($record->agency_id);

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
				$getUserDetails = User::getDetailsWithTrashById($record->created_by);
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

			$updateUserTypes = "Nybest User";
			$upfname = '';
			$uplname = '';
			if ($record->updated_by != '') {
				$getUserDetails = User::getDetailsById($record->updated_by);
			} else {
				$getThirdPartyUserDetails = GenerateAgencyTokenHelper::getDetailsById($record->agency_token_id);
				$userTypes = "";
				$upfname = isset($getThirdPartyUserDetails->notes) ? $getThirdPartyUserDetails->notes : "";
			}

			if (isset($getUserDetails->agency_fk) && $getUserDetails->agency_fk != '') {
				$updateUserTypes = "Agency User";
			}
			if (isset($getUserDetails->first_name) && $getUserDetails->first_name != '') {
				$upfname = $getUserDetails->first_name;
			}
			if (isset($getUserDetails->last_name) && $getUserDetails->last_name != '') {
				$uplname = $getUserDetails->last_name;
			}
			$record->updatedBy = $upfname . ' ' . $uplname;
			$record->updateUserTypes = $updateUserTypes;
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
			$data['masterData'] = $masterData = Master::getAllDataByMasterTypeFk(array(12, 17, 25, 26,31,29,32,33,35));
			$servie = '';
			if (isset($record->service_id) && $record->service_id != '') {
				$explode = explode(',', $record->service_id);
				$finalArray = [];
				foreach ($explode as $val) {
					if ($val != "") {
						$finalArray[] = $val;
					}
				}

				$services = Master::whereRaw('id IN (' . implode(',', $finalArray) . ')')->where('del_flag', 'N')->get();

				$newass = array();
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
				$record->otherreasonname = $record->other_reason??'';
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
			if ($record->merge_appointment_id != "") {
				$ids = $this->convertMergePatientArray($record->merge_appointment_id,$id);
			}
			$servie = [];
			$data['servie'] = $servie;
			//$getAgencyWiseServiceList = $this->AgencyWiseServiceService->ServiceListNew($data['record']->type, $data['record']->agency_id);
			$getAgencyWiseServiceList = $this->AgencyWiseServiceService->ServiceListNewWithoutNyBestUser($data['record']->type, $data['record']->agency_id);
			if(count($getAgencyWiseServiceList) >0){
				foreach($getAgencyWiseServiceList as $vals){
					$vals->types = $vals->type;
				}
				$data['serviceList'] =$getAgencyWiseServiceList;
			}else{
				$data['serviceList'] = Master::getServiceRequestNewWithCondition($data['record']->type);
			}

			$third_party_details = $this->thirdPartyPatientMaster->getPatientDetails($thirdPartyid, $data['record']->agency_id);

			$data['record']->link_third_party = $thirdPartyid;
			$name = "";
			if (isset($third_party_details->first_name)) {
				$name = $third_party_details->first_name . ' ' . $third_party_details->last_name;
			}
			$data['record']->link_third_party_name = $name;

			$insuranceDetails = $this->insuranceMasterService->getDetailById($data['record']->insurance_name);

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

			$disable_date = $this->disableDateService->disableDateAllData($data['record']->type)->toArray();
			$dateArray = explode(', ', implode(', ', $disable_date));
			$dateDetailArray = [];
			if (!empty($dateArray[0])) {
				foreach ($dateArray as $val) {
					$dateDetailArray[] = date('d-m-Y', strtotime($val));
				}
			}

			$data['disable_date'] = json_encode($dateDetailArray);

			$data['language_list'] = Cache::get('language_list', function () use ($auth) {
				return Language::getLanguageList();
			}, 10 * 60);

			$userDetails = User::getDataById($auth->id);
			$data['insuranceList'] = $this->insuranceMasterService->getInsuranceMasterList();
			$servicePaymentLog = $this->rateCardService->getServicePaymentData($record->agency_id);
			$serviceAmountData = array();
			foreach ($servicePaymentLog as $service) {
				if ($service['agency_id'] == $record->agency_id) {
					$serviceAmountData[$service['service_id']] = $service['amount'];
				} else {
					$serviceAmountData[$service['service_id']] = $service['amount'];
				}
			}

			$data['serviceAmountData'] = $serviceAmountData;
			$data['disciplineData'] = Master::getAllDataByMasterTypeFk(array(17, 26));
			$data['telehealth_time_frame'] = $data['record']->telehealth_time_frame ?? null;
			if (!$data['telehealth_time_frame'] && $data['record']->telehealth_time_slot) {
				$slot = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($data['record']->telehealth_time_slot);
				$data['telehealth_time_slot'] = $slot;
				if (!empty($slot['start_time']) && !empty($slot['end_time'])) {
					$data['telehealth_time_frame'] = date('h:i A', strtotime($slot['start_time'])) . ' - ' . date('h:i A', strtotime($slot['end_time']));
				}
			}
			$data['nurse'] = User::getNurses();
			$langArray = array();
			foreach($data['nurse'] as $nurse){
				if(isset($nurse->nurseLanguages)){
					$languages = array();
					foreach($nurse->nurseLanguages as $nLang){
						if(isset($nLang->languages[0])){
						$languages[] = $nLang->languages[0]['name'];
						}
					}
					$langArray[$nurse['id']]['language'] = implode(',', $languages);
				}
			}
			$data['nurse'] = $langArray;
			$data['dynamic_doc_approved_user'] = Utility::dynamicDocumentApproved();
			$data['team_resolution'] = Utility::getTeamArray();
			$data['resolution_array'] = Utility::getResolutionArray();
			$data['resolution_supervisor_access'] = Utility::resolutionSupervisorAccess();
			$nybestUserData = $this->assignNyBestUserService->getAssignNybestUser($data['record']->agency_id);
		    $data['nybestUserData'] = $nybestUserData;

			$data['appointmentPermission'] = DateWiseAgencyAccessHelper::getDateWiseAgencyAccess();
			$data['custom_esign_template'] = Template::select('id','template_name')->where('del_flag','N')->where('active_status','Active')->whereRaw("FIND_IN_SET(?, agency_id)", [$data['record']->agency_id])->where('lookup_fields',strtolower($data['record']->type))->where('custom_template',0)->get();
			$data['carePlanData'] = $this->testSampleData();
			$data['locationDisabledDates'] = Utility::getLocationDisableForSchedule();
			$link_visiting_data = $this->patientThirdPartyEmployeeService->getDetailsByPatientId($data['record']->id);

			$finalLinkVisitingAids = [];
			if(!empty($link_visiting_data[0])){
				foreach($link_visiting_data as $vs){
					$temp = [];
					$temp[$vs->type] = $vs;
					$finalLinkVisitingAids[$vs->type][] = $temp[$vs->type];
				}
			}

			$data['visiting_links'] = $finalLinkVisitingAids;
			$allUserList = $this->userService->getAllUserListWithAgency();
			$data['record']->agency_rep = $allUserList[$record->agency_user_id]??"";

			if (isset($userDetails->patient_page) && $userDetails->patient_page == 1) {

				return view('patient/patient_view_hospital_new_design', $data);
			} else {
				return view('patient/patient_view_hospital', $data);
			}
		} else {
			abort(404);
		}
	}

	public function updateAgencyUserRep(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'user_ids' => 'required',
			
		]);

		if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => 0,
                'data' => []
            ], 422);
        }

		$getAllDetails = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
		$this->PatientService->update(array('agency_user_id'=>$request->user_ids),array('id'=>$request->patient_id));
		$final = $request->except(['_token','patient_id']);
		$final['id'] = $request->patient_id;
		$final['created_date'] = date('Y-m-d H:i:s');
		$final['created_by'] = $user->id;
		
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Update Agency Rep',
			'link' => url('/patient/update-agency-user-rep'),
			'module' => 'Patient Appointment',
			'object_id' => $request->patient_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has update Agency Rep',
			'old_response' => serialize($getAllDetails),
			'new_response' => serialize($final),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		try {
			if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
				$agencyNotifyData = array(
					'agencyid' => $getAllDetails->agency_id,
					'title' => 'Update Agency Rep',
					'record_id' => $request->patient_id,
					'record_type' => 'Appointment',
					'msg' => '',
					'res_data' => serialize($final)
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);
			}
		} catch (\Throwable $th) {}
		return response()->json(['status' => 'success','error_msg'=>'Agency User Rep updated successfully']);
		
	}

	public function appointmentWiselogs(Request $request)
	{
		$id = request('id');
		$data['user'] = $authId = auth()->user();
		$data['logList'] = LogsService::getAllAppointmentLogs($id, 'Patient Appointment');
		return view("patient.appointment_view_log", $data);
	}

	function AppAppointment(Request $request)
	{
		$user = auth()->user();
		$cambine = $request->input('date');
		$date = Utility::convertYMD($cambine);

		$query = Patient::select('id', 'agency_id', 'type', 'key', 'language', 'mobile', 'phone')->where('id', $request->input('id'))->where('deleted_flag', 'N')->first();
		$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
		$time = '';
		if ($query->type == 'Patient') {
			$time = date('H:i:s', strtotime($request->input('time')));
		}

		$message = '';

		$unitId = "";
		if ($query->type == 'Caregiver') {
			$getAppointSchedule = $this->LocationScheduleService->getDetailbyId($request->input('time'));
			$agency_name = '';
			if (isset($getAgencyName->agency_name) && $getAgencyName->agency_name != '') {
				$agency_name = $getAgencyName->agency_name;
			}

			$time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";

			if ($query->key != "") {
				$unitId = $uns = $query->key;
			} else {
				$unitId = $uns = uniqid();
			}

			$url = URL::to('/') . '/ap/' . $unitId;
			if (isset($query->language) && strtolower($query->language) == 'spanish') {
				$message = 'Aviso de ' . $agency_name . ': Su cita está programada para el ' . Utility::convertMDY($cambine) . ' de ' . date('h:i A', strtotime($getAppointSchedule->start_time)) . ' A ' . date('h:i A', strtotime($getAppointSchedule->end_time)) . ' ' . $url . '.  No responda a este mensaje de texto y si usted tiene alguna pregunta, por favor llame al (718) 972-3693';
			} else {
				$message = 'Notice from ' . $agency_name . ': Your Appointment is scheduled for ' . Utility::convertMDY($cambine) . ' ' . date('h:i A', strtotime($getAppointSchedule->start_time)) . ' to ' . date('h:i A', strtotime($getAppointSchedule->end_time)) . ' ' . $url . '.  Do not reply to this text message for any questions please call (718) 972-3693';
			}
		}

		$this->PatientService->update(array("service_id" => implode(',', $request['service_id']), 'sms' => $message, 'key' => $unitId, 'location_id' => $request->input('location_id'), 'appointment_date' => $date . ' ' . $time, 'appointment_added_by' => $user['id'], 'status' => 'booked', 'appointment_added_created_date' => date('Y-m-d H:i:s'), 'appoinment_time_id' => $request->input('time'), 'appointment_mode' => 'Manual'), array('id' => $request->input('id')));
		if ($query->type == 'Caregiver') {

			if ($getAgencyName->is_sms == 1) {
				try {
					$getStopSMSServices = Utility::stopSMSService($query->agency_id);
					$array = array_intersect($request['service_id'], $getStopSMSServices);
					if (count($array) == 0) {
						$smsMobileArray = [];
						$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $query->mobile);
						if ($query->phone != "") {
							$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $query->phone);
						}

						if (count($smsMobileArray) > 0) {
							foreach ($smsMobileArray as $smb) {
								if ($smb != "") {
									$this->SmsService->AgencyWiseSmsDynamic($request->input('id'), $smb, $message);
								}
							}
						}
					}
				} catch (\Throwable $th) {
					//throw $th;
				}
			}
		}
		$checkAppointment = Appointment::where('patient_id', $request['id'])->where('appointment_time', null)->where('appointment_date', null)->WhereNull('telehealth_date')->where('doctor_id', null)->first();
		if ($checkAppointment) {
			$checkAppointment->update(['location_id' => $request['location_id'], "service_id" => implode(',', $request['service_id']), 'appointment_date' => $date . ' ' . $time, "appointment_time" => $time, 'status' => 'booked']);
		} else {
			$checkAppointment = Appointment::where('patient_id', $request['id'])->where('appointment_time', $time)->where('appointment_date', $date . ' ' . $time)->first();
			if ($checkAppointment) {
				$checkAppointment->update(['location_id' => $request['location_id'], "service_id" => implode(',', $request['service_id']), 'appointment_date' => $date . ' ' . $time, "appointment_time" => $time, 'status' => 'booked']);
			} else {
				$addAppintment = ["patient_id" => $request['id'], "location_id" => $request['location_id'], "service_id" => implode(',', $request['service_id']), "appointment_date" => $date . ' ' . $time, "appointment_time" => $time, "status" => "booked", "created_at" => date('Y-m-d H:i:s')];
				Appointment::create($addAppintment);
			}
		}
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Update Schedule Appointment',
			'link' => url('/patient/view') . '/' . $request->input('id'),
			'module' => 'Patient Appointment',
			'object_id' => $request->input('id'),
			'message' => $user->first_name . ' ' . $user->last_name . ' has schedule appointment via portal.',
			'new_response' => serialize(array('sms' => $message, 'key' => $unitId, 'location_id' => $request->input('location_id'), 'appointment_date' => $date . ' ' . $time, 'appointment_added_by' => $user['id'], 'status' => 'booked', 'appointment_added_created_date' => date('Y-m-d H:i:s'), 'appoinment_time_id' => $request->input('time'), 'appointment_mode' => 'Manual')),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		try {
			$agencyNotifyData = array(
				'agencyid' => $query->agency_id,
				'title' => 'Updated Schedule Appointment',
				'record_id' => $request['id'],
				'record_type' => 'Appointment',
				'msg' => '',
			);
			Common::insertAgencyNotificationsOfUser($agencyNotifyData);
		} catch (\Throwable $th) {}
		$this->saveServiceRequest($request->input('id'), 'booked');

		Session::flash('success', 'Appointment schedule successfully.');
		return redirect('/patient/view/' . $request->input('id'));
	}


	public function statusUpdate(Request $request, $id)
	{

		$user = auth()->user();
		$status = $request->input('status');
		$getOldResponse = $this->PatientService->getDetailById($id);
		$oldArray = array();
		$newArray = array();
		$oldArray['oldresponse'] = $getOldResponse;
		if ($status == 'Pending') {
			$data_array = array(
				'status' => $status,
				'appointment_date' => '',
			);
		}
		if ($status == 'booked') {
			$data_array = array(
				'status' => "Booked",
				'appointment_date' => date('Y-m-d H:i:s'),
				'appointment_added_by' => $user['id'],
				'appointment_mode' => 'Manual',
				'appointment_added_created_date' => date('Y-m-d H:i:s'),
				'booked_date' => date('Y-m-d H:i:s'),
				'booked_by' => $user['id']
			);
		}
		if ($status == 'complete') {
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
		if ($status == 'hospitalized') {
			$data_array = array(
				'status' => 'hospitalized/rehab',
			);
		}
		if ($status == 'unableToContact') {
			$data_array = array(
				'status' => 'unableToContact',
			);
		}
		if ($status == 'cancel') {
			$data_array = array(
				'status' => 'cancelled',
				'reason_id' => $request->input('reason_ids'),
				'cancel_date' => date('Y-m-d H:i:s'),
				'cancel_by' => $user['id']
			);
		}
		if ($status == 'noshow') {
			$data_array = array(
				'status' => 'noshow',
				'no_show_date' => date('Y-m-d H:i:s'),
				'no_show_by' => $user['id']
			);
		}
		if ($status == 'checkin') {
			$data_array = array(
				'status' => 'arrived',
			);
		}
		if ($status == 'processing') {
			$data_array = array(
				'status' => 'processing',
			);
		}
		if ($status == 'refused') {
			$data_array = array(
				'status' => 'refused',
				'reason_id' => $request->reason_ids,
			);
		}

		if ($status == 'pending') {
			$data_array = array(
				'status' => 'pending',
			);
		}

		if ($status == 'PendingTermination') {
			$data_array = array(
				'status' => 'Pending Termination',
			);
		}


		if ($status == 'Onhold') {
			$data_array = array(
				'status' => 'On Hold',
			);
		}

		if ($status == 'Onleave') {
			$data_array = array(
				'status' => 'On Leave',
			);
		}

		if ($status == 'Terminated') {
			$data_array = array(
				'status' => 'Terminated',
			);
		}

		if ($status == 'Inactive') {
			$data_array = array(
				'status' => 'inactive',
			);
		}
		$data_array['notes'] = $request->input('notes_id');
		$data_array['prev_status'] = $getOldResponse->status ?? "";
		$data_array['last_status_update'] = date('Y-m-d H:i:s');
		$data_array['last_status_update_by'] = auth()->user()->id;

		$getExistingRecord = $this->PatientService->getDetailById($id);

		$update = $this->PatientService->update($data_array, array('id' => $id));

		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Status Appointment',
			'link' => url('/patient/statusUpdate/"' . $id . '"'),
			'module' => 'Patient Appointment',
			'object_id' => $id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has ' . $data_array['status'] . ' Updated Appointment',
			'new_response' => serialize($data_array),
			'old_response' => serialize($getExistingRecord->toArray()),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		$agency = Agency::find(request('agency_id'));

		$getNewResponse = $this->PatientService->getDetailById($id);
		$newArray['newresponse'] = $getNewResponse;
		$this->CommonLogService->save(array('type' => 'Patient', 'common_fk' => $id, 'old_response' => serialize($oldArray), 'new_response' => serialize($newArray), 'main_type' => 'Ny Best Medicare'));

		if (isset($getNewResponse->record_id)) {
			$relatedatientId = Record::getDetailsByRecordid($getNewResponse->record_id);
			$masterDetails = Master::getDetailsById($relatedatientId->patient_status);
			$staus = isset($masterDetails->name) ? $masterDetails->name : "";
			RecordReportService::save(
				array('record_id' => $getNewResponse->record_id, 'type' => 'NyBest Medicare ', 'subject' => 'Update status Ny Best', 'notes' => ' Update status for ny best medicare', 'status' => $staus)
			);
		}

		$querys = Agency::getAllDetailsbyAgencyId($getNewResponse->agency_id);
		$emails = isset($querys->nybest_email_notification) ? $querys->nybest_email_notification : "";

		$getUserEmail =UserHelper::getUserDetails($getNewResponse->created_by);

		$allemails = array();
		$allemails[] = $user->email;

		if ($emails != '') {
			$newsemail = explode(',', $emails);
			if (count($newsemail) > 0) {
				foreach ($newsemail as $vas) {
					if (trim($vas) != '') {
						if (trim($vas) != 'li@qualityny.com') {
							$allemails[] = trim($vas);
						}
					}
				}
			}
		}
		$location_list = $this->LocationMasterService->getDetailbyId($getNewResponse->location_id);
		$address1 = isset($location_list->address1) ? $location_list->address1 : "";
		$address2 = isset($location_list->address2) ? $location_list->address2 : "";
		$city = isset($location_list->city) ? $location_list->city : "";
		$state = isset($location_list->state) ? $location_list->state : "";
		$zip_code = isset($location_list->zip_code) ? $location_list->zip_code : "";

		$localdetails = $address1 . ' ' . $address2 . ' ' . $city . ' ' . $state . ' ' . $zip_code;

		$flags = 0;
		$subject = "";

		$responseStatusArray = AgencyWiseNotifictionEmail::getStatusByAgencyId($getNewResponse->agency_id, $getNewResponse->type, $status, $getNewResponse->id);

		$created_user_mail="";
		if (count($responseStatusArray['email']) != 0) {
			if ($responseStatusArray['flag'] == 1) {
				if ($status == "noshow") {

					$created_user_mail ='Status No Show';
					if ($getNewResponse->type == 'Caregiver') {
						$subject = 'Notification from Ny Best Caregivers status has changed to No Show';
					} else {
						$subject = 'Notification from Ny Best Patient status has changed to No Show';
					}
					$flags = 1;
				}
				if ($status == 'checkin') {
					$created_user_mail ='Status Checkin';
					if ($getNewResponse->type == 'Caregiver') {
						$subject = 'Notification from Ny Best Medical Caregivers Arrived and Appointment is pending';
					} else {
						$subject = 'Notification from Ny Best Medical Patient Arrived and Appointment is pending';
					}
					$flags = 1;
				}

				if ($status == 'complete') {
					$created_user_mail ='Status Completed';

					if ($getNewResponse->type == 'Caregiver') {
						$subject = 'Notification from Ny Best Caregivers status has changed to Completed';
					} else {
						$subject = 'Notification from Ny Best Patient status has changed to Completed';
					}
					$flags = 1;
				}
			}
		} else {
			if ($status == 'complete') {

				$created_user_mail ='Status Completed';
				if ($getNewResponse->type == 'Caregiver') {
					$subject = 'Notification from Ny Best Caregivers status has changed to Completed';
				} else {
					$subject = 'Notification from Ny Best Patient status has changed to Completed';
				}
				$flags = 1;
			}
			if ($status == "noshow") {

				$created_user_mail ='Status No Show';
				if ($getNewResponse->type == 'Caregiver') {
					$subject = 'Notification from Ny Best Caregivers status has changed to No Show';
				} else {
					$subject = 'Notification from Ny Best Patient status has changed to No Show';
				}
				$flags = 1;
			}
			if ($status == 'checkin') {
				$created_user_mail ='Status Checkin';
				if ($getNewResponse->type == 'Caregiver') {
					$subject = 'Notification from Ny Best Medical Caregivers Arrived and Appointment is pending';
				} else {
					$subject = 'Notification from Ny Best Medical Patient Arrived and Appointment is pending';
				}
				$flags = 1;
			}
		}

		if ($subject == "") {
			if ($getNewResponse->type == 'Caregiver') {

				$subject = 'Notification from Ny Best Caregivers status has changed to ' . $status;
			} else {

				$subject = 'Notification from Ny Best Patient status has changed to ' . $status;
			}
		}

		$recordType = isset($getNewResponse->type) ? $getNewResponse->type : "";
		$notificationType = "Status Update";

		$email_data = array(
			'agency_name' => $querys->agency_name != '' ? $querys->agency_name : '-',
			'portal_id' => $id,
			'type' => $getNewResponse->type,
			'localdetails' => ($localdetails != '') ? $localdetails : '-',
			'first_name' => $getNewResponse->first_name,
			'last_name' => $getNewResponse->last_name,
			'status' => $status

		);
		$messages = Utility::getHtmlContent('email_template.status_update', $email_data);
		/*******Send Mail Notification for general user */
		//$this->SendEmailNotificationSerivce->sendGeneralNotification($recordType, $notificationType, $subject, $messages, "", "", $id);
		$generalNotificatinEmail = $this->SendEmailNotificationSerivce->sendGeneralNotificationWithEmail($recordType, $notificationType, $subject, $messages, "", "", $id);
		/*************************End Send Mail Notification for general user */
		$userEmail = [];
		$document_names="";
		$attachment="";
		if ($flags == 1) {

			//add send email agency module permission notification email

			$recordType = isset($getNewResponse->type) ? $getNewResponse->type : "";
			$notificationType = "Status Update";
			$sendEmailNotificationagencyId = isset($getNewResponse->agency_id) ? $getNewResponse->agency_id : "";


			if ($recordType != "" && $sendEmailNotificationagencyId != "") {
				$sendEmailNotication = $this->SendEmailNotificationSerivce->sendEmailNotificationServicesDiscipline($recordType, $notificationType, $sendEmailNotificationagencyId, $subject, $messages, "", "", $id);
			}

			$notificationTypeUser = "Status Update";
			$sendEmailNotificationUserId = isset($getNewResponse->created_by) ? $getNewResponse->created_by : "";
			$sendEmailNoticationNotification = [];
			if ($recordType != "" && $sendEmailNotificationUserId != "") {
				$sendEmailNoticationNotification = $this->SendEmailNotificationSerivce->sendEmailNotificationUserWithEmail($recordType, $notificationTypeUser, $sendEmailNotificationUserId, $subject, $messages);
			}
			LogsCreateEmailCheck::insert([
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => auth()->user()->id??'',
				'patient_id' => $id,
				'patient_created_by' => $getNewResponse->created_by,
				'type' => 'Status'
			]);
			if(isset($getUserEmail->creator_email_noti_toggle) && $getUserEmail->creator_email_noti_toggle == 1){
				$getCreatedUserEnabledOrNot = $this->userCreatorEmailNotificationService->getAddOrNotUserEmailNotification($getNewResponse->agency_id,$created_user_mail);

				if($getCreatedUserEnabledOrNot ==1){
					$userEmail = array($getUserEmail->email);

				}
			}

			if ($status == 'complete') {
				$getLastServiceId = $this->patientServicesRequest->lastServiceRequestedByPatientId($id);
				$getLastServiceDoc = $this->DocumentPatientService->getLastDocumentNameByServiceId($getLastServiceId->id,$id);
				if(isset($getLastServiceDoc) && !empty($getLastServiceDoc)){
					$attachment = $getLastServiceDoc->attachment;
				}
			}
			$assignAgencyMail = $this->SendEmailNotificationSerivce->getAssignNyUserAgencyMail($getNewResponse->agency_id);
			$finalEmail = array_unique(array_merge($allemails, $generalNotificatinEmail, $sendEmailNotication, $sendEmailNoticationNotification, $responseStatusArray['email'], $assignAgencyMail));
			if(empty($user->agency_fk)){
				$finalEmail = array_diff($finalEmail, [self::STATIC_EMAIL]);
			}
			$this->SendEmailNotificationSerivce->UserMailWithMultipleEmail($finalEmail, "", $subject, $messages,"");
		}

		if(count($userEmail) >0){
			if(empty($user->agency_fk)){
				$userEmail = array_diff($userEmail, [self::STATIC_EMAIL]);
			}
			$this->SendEmailNotificationSerivce->UserMailWithMultipleEmail($userEmail, $attachment, $subject, $messages,$document_names);
		}

		if ($status == 'processing') {
			if ($getNewResponse->type == 'Patient') {
				$this->sendBulkStatusProcessingMail($getNewResponse->id);
			}
		}
		$this->saveServiceRequest($id, $status);

		if ($status == 'complete') {
			if(isset($request['selectedAttrs']) && !empty($request['selectedAttrs'])){

				foreach($request['selectedAttrs'] as $doc){
					$Docdata = array('document_review_status'=>'Approved','internal_use' => 0);
					$where = array('id' => $doc);
					$this->DocumentPatientService->update($Docdata,$where);

					// Insert log into the system
					$docdata = array(
						'patient_id' => $id,
						'doc_id' => $doc,
					);
					$insertLog = [
						'type' => 'Update Internal Use Only Document from Appointment',
						'link' => url('/patient/view/') . '/' .$id,
						'module' => 'Patient Appointment',
						'object_id' =>$id,
						'message' => $user->first_name . ' ' . $user->last_name . ' has updated Document ID: #' . $doc . ' (Internal Use Only) from Appointment',
						'new_response' => serialize(array('document_review_status' => 'Pending', 'internal_use' => 0,'id'=>$doc)),
						'old_response' => serialize($Docdata),
						'ip' => $ipaddress,
					];
					if (isset($getExistingRecord) && $getExistingRecord != "") {
						$insertLog['old_response'] = serialize($getExistingRecord->toArray());
					}
					LogsService::save($insertLog);
					$this->docCompletedInternalUseLogService->save($docdata);
				}


			}
		}

		$identifier = env('INFLOWCARE_IDENTIFICATION');
		if (!empty($getExistingRecord->patient_code) && strpos($getExistingRecord->patient_code, $identifier) !== false) {
			if ($status === 'complete') {
				$this->sendInflowcareWebhook($getExistingRecord->id);
			}
		}

		echo $update;
	}

	public function aiAnalyseProxy(Request $request)
	{
		if (!$request->hasFile('file')) {
			return response()->json(['error' => 'No file provided'], 422);
		}

		$file      = $request->file('file');
		$extension = strtolower($file->getClientOriginalExtension());

		// Fetch fresh patient record from DB so mismatch check always uses current data
		$patientRecord = null;
		$recordId = $request->input('record_id');
		if ($recordId) {
			$rec = $this->PatientService->getPatientDetailsByIdWhitoutAgency($recordId);
			if ($rec) {
				$patientRecord = [
					'first_name' => $rec->first_name ?? '',
					'last_name'  => $rec->last_name  ?? '',
					'dob'        => $rec->dob        ?? '',
					'mobile'     => $rec->mobile     ?? '',
				];
			}
		}

		try {
			$textractService = new AwsTextractService();
			$extractedText   = $textractService->extractTextFromLocalFile($file->getRealPath(), $extension);

			if (empty(trim($extractedText))) {
				return response()->json(['error' => 'No text could be extracted from the document.'], 422);
			}

			$bedrockService = new AwsBedrockService();
			$result         = $bedrockService->analyzeMedicalDocument($extractedText);

			return response()->json([
				'ai'     => $result['parsed'],
				'patient'=> $patientRecord,
			], 200);
		} catch (\Throwable $e) {
			Log::error('[AI Analyse] ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 502);
		}
	}

	public function getDocumentAiSummary($docId)
	{
		$doc = $this->DocumentPatientService->findById($docId);
		if (!$doc) {
			return response()->json(['success' => false, 'message' => 'Document not found'], 404);
		}
		if (!empty($doc->ai_summary)) {
			$parsed = json_decode($doc->ai_summary, true);
			return response()->json(['success' => true, 'has_summary' => true, 'data' => $parsed ?? $doc->ai_summary]);
		}
		return response()->json(['success' => true, 'has_summary' => false]);
	}

	public function aiAnalyseByDocId(Request $request, $docId)
	{
		$doc = $this->DocumentPatientService->findById($docId);
		if (!$doc) {
			return response()->json(['error' => 'Document not found.'], 404);
		}

		if (empty($doc->attachment)) {
			return response()->json(['error' => 'No file attached to this document.'], 422);
		}

		try {
			$attachment  = $doc->attachment;
			$fileName    = str_replace('patientdocument/', '', $attachment);
			$extension   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
			$s3Path      = 'patientdocument/' . $fileName;
			$localFile   = public_path('/') . '/patientdocument/' . $fileName;

			if (env('FILE_UPLOAD_PERMISSION') != 'development') {
				$fileContents = Storage::disk('s3')->get($s3Path);
			} else {
				$fileContents = file_get_contents($localFile);
			}

			if (empty($fileContents)) {
				return response()->json(['error' => 'Could not retrieve file for analysis.'], 422);
			}

			$textractService = new AwsTextractService();
			$extractedText   = $textractService->extractTextFromContents($fileContents, $extension);

			if (empty(trim($extractedText))) {
				return response()->json(['error' => 'No text could be extracted from the document.'], 422);
			}

			$bedrockService = new AwsBedrockService();
			$result         = $bedrockService->analyzeMedicalDocument($extractedText);
			$parsed = $result['parsed'];

			$doc->ai_summary = is_string($parsed) ? $parsed : json_encode($parsed);
			$doc->save();
				$user = auth()->user();
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'View AI Analysis',
					'link' => url('/patient/view/') . '/' . $doc->patient_id,
					'module' => 'Patient Appointment',
					'object_id' => $doc->patient_id,
					'message' => $user->first_name . ' ' . $user->last_name . ' performed AI Document Analysis on Appointment Document ID #' . $docId . '.',
					'new_response' => serialize($parsed),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
			return response()->json($parsed, 200);
		} catch (\Throwable $e) {
			Log::error('[AI Analyse By Doc] ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 502);
		}
	}

	public function saveDocumentAiSummary(Request $request, $docId)
	{
		$doc = $this->DocumentPatientService->findById($docId);
		if (!$doc) {
			return response()->json(['success' => false], 404);
		}
		$doc->ai_summary = $request->input('ai_summary');
		$doc->save();
		return response()->json(['success' => true]);
	}

	public function DocumentUploadByPatientId(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'document_id' => 'required',
			'images' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$image = '';
			$getExistingRecordNew = $this->PatientService->getDetailByIdNew($request->input('id'));

			$priceImage = $request->file('images');

			$name = uniqid() . time() . '.' . $priceImage->getClientOriginalExtension();

			$destination1 = public_path('patientdocument');
			$destination2 = public_path('patientWriteDocument');

			$mimeType = $request->file('images')->getMimeType();

			if (env('FILE_UPLOAD_PERMISSION') == 'development') {


				$priceImage->move($destination1, $name);
				\File::copy($destination1 . '/' . $name, $destination2 . '/' . $name);
				$image = $name;
			} else {
				Storage::disk('s3')->putFileAs('patientdocument', $priceImage, $name);
				Storage::disk('s3')->putFileAs('patientWriteDocument', $priceImage, $name);

				$image = $name;
			}

			$getExistingRecord = '';
			if ($request->input('did') != '') {
				$getExistingRecord = $this->DocumentPatientService->getDetailsById($request->input('did'));
				$data = array(

					'attachment' => $image,

				);

				$insert = $this->DocumentPatientService->update($data, array('id' => $request->input('did')));

				app('App\Http\Controllers\API\v1\InflowcareCargiverController')->uploadDocument($request->input('did'), $image);
			} else {
				$request_service = '';
				if (!empty($request->request_service_id[0])) {
					$request_service = implode(", ", $request->request_service_id);
				}
				$internal_use = !empty($request->internal_use) && $request->internal_use == 1 ? 1 : 0;

				$assignDocumentUser = NULL;
				if ($request->document_review == 1) {
					$assignDocumentUser = $request->document_approval_user_id;
				}
				if (strtolower($request->type) == 'patient' && auth()->user()->agency_fk == '') {
					$internal_use = 1;
				}
				$medication_list = !empty($request->medication_list) && $request->medication_list == 1 ? 1 : 0;
				$insurance_elg = !empty($request->insurance_elg) && $request->insurance_elg == 1 ? 1 : 0;
				$mdo_tag = !empty($request->mdo_tag) && $request->mdo_tag == 1 ? 1 : 0;
				$mdo_source = !empty($request->mdo_tag) && $request->mdo_tag == 1 ? $request->mdo_source : NULL;
				$data = array(
					'document_name' => $request->input('document_id'),
					'attachment' => $image,
					'patient_id' => $request->input('id'),
					'request_service_id' => $request_service,
					'is_checked' => !empty($request->is_checked) && $request->is_checked != '' ? 1 : 0,
					'internal_use' => $internal_use,
					'assign_document_review' => $assignDocumentUser,
					'old_attachment'=>$image,
					'medication_list' => $medication_list,
					'insurance_elg' => $insurance_elg,
					'mdo_tag' => $mdo_tag,
					'mdo_source' => $mdo_source,
				);
				$data['document_review_status'] = "Approved";
				if (isset($request->document_review) && $request->document_review == 1) {
					$data['document_review_status'] = "Pending";
				}

				if (isset($request->document_completed_date) && $request->document_completed_date != "") {
					$data['document_completed_date'] = date('Y-m-d', strtotime($request->document_completed_date));
				}
				if (isset($request->upload_for_info_only) && $request->upload_for_info_only == 1) {
					$data['info_only'] = $request->upload_for_info_only;
				}
				$newResponse = $data;
				$data['ai_summary'] = '';
				if(isset($request->ai_summary) && !empty($request->ai_summary)){
					$data['ai_summary'] = $request->ai_summary;
				}
				$insert = $this->DocumentPatientService->save($data);
			}

			if ($insert) {

				if (!empty($request->document_service_id[0])) {
					foreach ($request->document_service_id as $serviceId) {
						$data1 = [
							'patient_id' => $request->input('id'),
							'document_id' => $insert,
							'service_id' => $serviceId,
						];

						$this->documentUploadService->save($data1);
					}
				}
				
				DocumentHelper::updatePatientDocumentCounts($request->input('id'),$medication_list,$insurance_elg, 0, 0,$mdo_tag,0);
				$messageForLog = $user->first_name . ' ' . $user->last_name . ' has Add Document From Appointment';
				if(isset($data['ai_summary']) && !empty($data['ai_summary'])){
					$messageForLog .= ' with AI Summary.';
				}
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add Document From Appointment',
					'link' => url('/patient/view/') . '/' . $request->input('id'),
					'module' => 'Patient Appointment',
					'object_id' => $request->input('id'),
					'message' => $messageForLog,
					'new_response' => serialize($data),

					'ip' => $ipaddress,
				];
				if (isset($getExistingRecord) && $getExistingRecord != "") {
					$insertLog['old_response'] = serialize($getExistingRecord->toArray());
				}
				LogsService::save($insertLog);
				if ($request->input('did') != '') {
				} else {
					$newDocument  = $this->DocumentPatientService->getDetailsById($insert);
					$documentApprovalUserIds = [];
					if ($request->document_review == 1) {
						$documentApprovalUserIds = explode(',', $request->document_approval_user_id);
					}

					if ($internal_use == 0) {
						$this->sendEmailNotificaiton($request->input('id'), $image, "", $newDocument->document_name, $internal_use, $documentApprovalUserIds);
					}

					$doc_data = array(
						'doc_name' => $request->input('document_id'),
						'doc_id' => $insert,
						'services' => $request->document_service_id
					);
					if ($internal_use == 0) {
						$this->sendNotificationToUser($newDocument->patient_id, "Document", "", $doc_data);
					}
				}

				//write document save
				$this->saveWriteDocumentData('Document', $insert, $image, $request->input('document_id'));
				$insertLog = [
					'type' => 'Add Document',
					'link' => url('/patient/document-send-patientId/') . '/' . $request->input('id'),
					'module' => 'Document Section',
					'module_id' => $insert,
					'new_response' => serialize($newResponse),
					'old_response' => '',
					'is_status' => 'Add Document'
				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

				$getExistingRecord = $this->PatientService->getDetailByIdNew($request->input('id'));
				if ($getExistingRecord->platform_type == 'dssdsd') {
					if ($getExistingRecord->link_third_party != "") {
						$data = ['id' => $getExistingRecord->link_third_party, 'first_name' => $getExistingRecord->first_name, 'last_name' => $getExistingRecord->last_name, 'document_id' => $insert, 'document_name' => $request->input('document_id')];
						$webhook = ['status' => 'Document Uploaded', "data" => $data];

						$response = ThirdPartyWebHookHelper::sendWebHook($webhook);
						$json = json_decode($response, true);
						if (isset($json['status']) && $json['status'] == 'success') {
							$createdBy = null;
							if (isset($user->id)) {
								$createdBy = $user->id;
							}
							$saveResponse = [
								'third_party_id' => $getExistingRecord->link_third_party,
								'send_response' => serialize($data),
								'return_response' => serialize(array($json['data'])),
								'created_date' => date('Y-m-d H:i:s'),
								'created_by' => $createdBy
							];

							$saveData = new ThirdPartyWebhookLog($saveResponse);
							$saveData->save();
						}
					}
				}
				if ($request->document_review == 1) {
					$users = explode(',', $request->document_approval_user_id);
					foreach ($users as $user) {
						$udata = array(
							'user_id' => $user,
							'patient_id' => $request->input('id'),
							'document_id' => $insert
						);
						$this->multiplePatientDocApprovalService->save($udata);
					}
				}
				if (isset($request->questions)) {
					$questions = explode(',', $request->questions);
					foreach ($questions as $que) {
						$docsData = array(
							'user_id' => auth()->user()->id,
							'question_id' => $que
						);
						$this->userDocQuestionMarkedService->save($docsData);
					}
				}
				try {
					if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
						$agencyNotifyData = array(
							'agencyid' => $getExistingRecord->agency_id,
							'title' => 'Uploaded new document',
							'record_id' => $getExistingRecord->id,
							'record_type' => 'Document',
							'msg' => '',
							'res_data' => serialize($data),
						);
						Common::insertAgencyNotificationsOfUser($agencyNotifyData);
					}
				} catch (\Throwable $th) {}
				return response()->json(['status' => true, 'error_msg' => 'Document  successfully uploaded'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	private function sendEmailNotificaiton($id, $image, $name, $documentName, $internal_use, $documentApprovalUserIds = [])
	{
		$authUser = auth()->user();
		$getNewResponse = $this->PatientService->getDetailById($id);
		if (isset($getNewResponse->agency_id) && $getNewResponse->agency_id != '') {

			$query = Agency::getAllDetailsbyAgencyId($getNewResponse->agency_id);

			$emails = isset($query->nybest_email_notification) ? $query->nybest_email_notification : "";

			$emailss = explode(',', $emails);
			$allemails = array();
			$allemails[] = $authUser->email;
			foreach ($emailss as $ems) {
				if (trim($ems) != '') {
					if (trim($ems) != 'li@qualityny.com') {
						$allemails[] = trim($ems);
					}
				}
			}
			$location_list = $this->LocationMasterService->getDetailbyId($getNewResponse->location_id);
			$address1 = isset($location_list->address1) ? $location_list->address1 : "";
			$address2 = isset($location_list->address2) ? $location_list->address2 : "";
			$city = isset($location_list->city) ? $location_list->city : "";
			$state = isset($location_list->state) ? $location_list->state : "";
			$zip_code = isset($location_list->zip_code) ? $location_list->zip_code : "";

			$localdetails = $address1 . ' ' . $address2 . ' ' . $city . ' ' . $state . ' ' . $zip_code;

			$name = isset($query->agency_name) ? $query->agency_name : "";
			//$messages = 'Hello ' .$name. '<br>';
			$discipline = $getNewResponse->diciplin;


			$subject = 'Notification from NY BEST MEDICAL RESULTS ARE UPLOADED';

			$emailData = array(
				'agencyname' => $name,
				'insert' => $id,
				'type' => $getNewResponse->type,
				'first_name' => $getNewResponse->first_name,
				'last_name' => $getNewResponse->last_name,
				'location' => $localdetails,
				'document_name' => $documentName,
				'discipline' => $discipline,
			);
			$messages = Utility::getHtmlContent('email_template.document_upload_patient', $emailData);

			/*Rohit Panchal code*/
			$notificationType = "Document Upload";
			$sendEmailNotication = [];
			if ($getNewResponse->type != "" && $getNewResponse->agency_id != "") {
				//$sendEmailNotication = $this->SendEmailNotificationSerivce->sendEmailNotificationNew($getNewResponse->type, $notificationType, $getNewResponse->agency_id, $subject, $messages, $name, $image, $id);
				if ($internal_use == 0) {
					$sendEmailNotication = $this->SendEmailNotificationSerivce->sendEmailNotificationServicesDiscipline($getNewResponse->type, $notificationType, $getNewResponse->agency_id, $subject, $messages, $name, $image, $id);
				}
			}

			/*******Send Mail Notification for general user */
			$generalEmail = $this->SendEmailNotificationSerivce->sendGeneralNotificationWithEmail($getNewResponse->type, $notificationType, $subject, $messages, $name, $image, $id);
			//$this->SendEmailNotificationSerivce->sendGeneralNotification($getNewResponse->type, $notificationType, $subject, $messages,  $name, $image, $id);
			/*************************End Send Mail Notification for general user */
			$sendUserEmailNotication = [];
			if ($getNewResponse->type != "" && $getNewResponse->created_by != "") {
				$sendUserEmailNotication = $this->SendEmailNotificationSerivce->sendEmailNotificationUserWithEmail($getNewResponse->type, $notificationType, $getNewResponse->created_by, $subject, $messages, $name, $image);
			}

			$assignDocumentUserMail = [];
			if (count($documentApprovalUserIds) > 0) {
				$assignUserEmail = UserHelper::getDetailsByUserids($documentApprovalUserIds);
				foreach ($assignUserEmail as $docApIds) {

					$assignDocumentUserMail[] = $docApIds->email;
				}
			}

			$userEmail = [];
			$getCreatedUserd = UserHelper::getUserDetails($getNewResponse->created_by);
			LogsCreateEmailCheck::insert([
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => $authUser->id??'',
				'patient_id' => $id,
				'patient_created_by' => $getNewResponse->created_by??'',
				'type' => 'Document'
			]);
			if(isset($getCreatedUserd->creator_email_noti_toggle) && $getCreatedUserd->creator_email_noti_toggle == 1)
			{

				$getCreatedUserEnabledOrNot = $this->userCreatorEmailNotificationService->getAddOrNotUserEmailNotification($getNewResponse->agency_id,'Document Upload');
				if($getCreatedUserEnabledOrNot ==1){
					$userEmail = array($getCreatedUserd->email);
				}
			}

			$assignAgencyMail = $this->SendEmailNotificationSerivce->getAssignNyUserAgencyMail($getNewResponse->agency_id);
			$finalArray = array_unique(array_merge($allemails, $sendEmailNotication, $generalEmail, $sendUserEmailNotication, $assignDocumentUserMail,$assignAgencyMail,$userEmail));

			if (!empty($finalArray[0])) {

				try {
					//$subject = 'Document Upload #'.$id.' Notification from NY BEST MEDICAL RESULTS ARE UPLOADED';
					$this->SendEmailNotificationSerivce->UserMailWithMultipleEmail($finalArray, $image, $subject, $messages, $documentName);
				} catch (\Throwable $th) {
					//throw $th;
				}
			}
		}
	}

	public function patientAppointments(Request $request, $key)
	{
		$data['key'] = $key;
		//$data['location_list'] = $this->LocationMasterService->AllListWithoutPaginate();
		$data['location_list'] = $this->LocationMasterService->AllListWithoutPaginateSMS();
		$data['query'] = $query = Patient::where('key', $key)->where('deleted_flag', 'N')->first();
		if (isset($query->id)) {


			$locationname = '';
			$latitude = "";
			$longitude = "";
			$getLocationDetails = $this->LocationMasterService->getDetailbyId($query->location_id);
			if (isset($getLocationDetails->id)) {
				$locationname = $getLocationDetails->location_name. ' '. $getLocationDetails->address2. ' '.$getLocationDetails->city. ' '. $getLocationDetails->state. ' '. $getLocationDetails->zip_code;
				$latitude = $getLocationDetails->latitude;
				$longitude = $getLocationDetails->longitude;
			}

			if (count($data['location_list']) > 0) {
				foreach ($data['location_list'] as $vsl) {
					if (isset($query->location_id) && $query->location_id != '') {
						if ($query->location_id == $vsl->id) {
							//	$locationname = $vsl->address1 . ' ' . $vsl->city . ' ' . $vsl->state;
						}
					}
				}
			}
			$data['times'] = $this->hoursRange(32400, 72000, 60 * 15);
			$data['locationname'] = $locationname;
			$data['latitude'] = $latitude;
			$data['longitude'] = $longitude;
			if (isset($getLocationDetails->id)) {
				$data['location_map_link'] = isset($getLocationDetails->link) && !empty($getLocationDetails->link) ? $getLocationDetails->link : 'https://www.google.com/maps/search/?api=1&query=' .$getLocationDetails->location_name. ' '. $getLocationDetails->address2. ' '.$getLocationDetails->city. ' '. $getLocationDetails->state. ' '. $getLocationDetails->zip_code;
			}
			$agencyDetails = Agency::getDetailsByAgencyId($query->agency_id);
			$agencyName = '';
			if (isset($agencyDetails->agency_name) && $agencyDetails->agency_name != '') {
				$agencyName = $agencyDetails->agency_name;
			}
			$data['query']->agency_name = $agencyName;

			$getDetailsAppoinment = $this->LocationScheduleService->getDetailbyId($query->appoinment_time_id);
			$end_start_date = '';
			if (isset($getDetailsAppoinment->start_time) && $getDetailsAppoinment->start_time != '') {
				$end_start_date = date('h:i A', strtotime($getDetailsAppoinment->start_time)) . ' to ' . date('h:i A', strtotime($getDetailsAppoinment->end_time));
			}

			$data['appointmentTimes'] = $end_start_date;

			$disable_date = $this->disableDateService->disableDateAllData($data['query']->type)->toArray();
			$dateArray = explode(', ', implode(', ', $disable_date));
			$dateDetailArray = [];
			if (!empty($dateArray[0])) {
				foreach ($dateArray as $val) {
					$dateDetailArray[] = date('d-m-Y', strtotime($val));
				}
			}
			$data['disable_date'] = json_encode($dateDetailArray);
			$data['locationDisabledDates'] = Utility::getLocationDisableForSchedule();
			return view('patient/patient_view_hospital_appointment', $data);
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
	function AppointmentsSave(Request $request)
	{

		$request->input('key');
		$query = Patient::select('id', 'type', 'key', 'agency_id', 'mobile', 'language', 'service_id', 'phone')->where('key', $request->input('key'))->where('deleted_flag', 'N')->first();


		if (isset($query->id) && $query->id != '') {
			$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
			$oldstat = $request->input('start_date');
			$start_date = date('Y-m-d', strtotime($oldstat));
			$getAppointSchedule = $this->LocationScheduleService->getDetailbyId($request->input('time_id'));

			$totalAppointmentBookByTime = $this->PatientService->totalAppointmentBookedByTimeSlot($request->input('time_id'), $start_date);

			$totalCount = count($totalAppointmentBookByTime);

			$slotRemaing = ($getAppointSchedule->slot - $totalCount);

			if ($slotRemaing > 0) {
				$data = array(
					'appointment_date' => $start_date,
					'location_id' => $request->input('location_id'),
					'status' => 'booked',
					'appointment_added_created_date' => date('Y-m-d H:i:s'),
					'appoinment_time_id' => $request->input('time_id'),
					'appointment_mode' => 'sms',
					'patient_sms_flag' => 1
				);
				$update = $this->PatientService->update($data, array('id' => $query->id));
				// Mark auto-call log as booked so the scheduled call is suppressed
				AutoCallService::markSelfBooked($query->key);
				$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
				if (strtolower($query->type) == 'caregiver') {
					//$message = 'Notice from '.$getAgencyName->agency_name.' Your Appointment is scheduled for ';

					$getAppointSchedule = $this->LocationScheduleService->getDetailbyId($request->input('time_id'));
					$cnt = 1;
					$unitId = $query->key;
					$url = URL::to('/') . '/ap/' . $unitId;
					if (isset($query->language) && strtolower($query->language) == 'spanish') {

						$htmlLanguage = $getAgencyName->appointment_send_book_spanish;
					} else {

						$htmlLanguage = $getAgencyName->appointment_send_book_eng;
					}

					$this->PatientService->update(array('key' => $unitId, 'patient_sms_flag' => 1), array('id' => $query->id));

					$htmlStringReplace = $htmlLanguage;
					$htmlStringReplace = str_replace('{{appointment_date}}', date('m-d-Y', strtotime($query->appointment_date)), $htmlStringReplace);
					$htmlStringReplace = str_replace('{{start_date}}', date('m-d-Y', strtotime($start_date)), $htmlStringReplace);
					$htmlStringReplace = str_replace('{{start_time}}', date('h:i A', strtotime($getAppointSchedule->start_time)), $htmlStringReplace);
					$htmlStringReplace = str_replace('{{end_time}}', date('h:i A', strtotime($getAppointSchedule->end_time)), $htmlStringReplace);
					$htmlStringReplace = str_replace('{{url}}', $url, $htmlStringReplace);
					$htmlStringReplace = str_replace('{{link}}', $getAppointSchedule->link, $htmlStringReplace);
					$smsMessage = $htmlStringReplace;


					if ($getAgencyName->is_sms == 1) {
						$explodeService = explode(',', $query->service_id);

						$getStopSMSServices = Utility::stopSMSService($query->agency_id);
						$array = array_intersect($explodeService, $getStopSMSServices);

						if (count($array) == 0) {
							$smsMobileArray = [];
							$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $query->mobile);
							if ($query->phone != "") {
								$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $query->phone);
							}

							if (count($smsMobileArray) > 0) {
								foreach ($smsMobileArray as $smb) {
									if ($smb != "") {
										$sendSmsAgency = $this->SmsService->AgencyWiseSmsDynamic($query->id, $smb, $smsMessage);
										$this->PatientSMSLogService->save(array('patient_id' => $query->id, 'mobile_no' => $smb, 'message' => $smsMessage, 'key' => $unitId));
									}
								}
							}
						}
					}

					$checkAppointment = Appointment::where('patient_id', $query->id)->where('appointment_time', null)->where('appointment_date', null)->WhereNull('telehealth_date')->where('doctor_id', null)->first();
					$time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";
					if ($checkAppointment) {
						$checkAppointment->update(['location_id' => $request->input('location_id'), "service_id" => $query->service_id, 'appointment_date' => $start_date . ' ' . $time, "appointment_time" => $time, 'status' => 'booked']);
					} else {
						$checkAppointment = Appointment::where('patient_id', $query->id)->where('appointment_time', $time)->where('appointment_date', $start_date . ' ' . $time)->first();
						if ($checkAppointment) {
							$checkAppointment->update(['location_id' => $request['location_id'], "service_id" => $query->service_id, 'appointment_date' => $start_date . ' ' . $time, "appointment_time" => $time, 'status' => 'booked']);
						} else {
							$addAppintment = ["patient_id" => $query->id, "location_id" => $request['location_id'], "service_id" => $query->service_id, "appointment_date" => $start_date . ' ' . $time, "appointment_time" => $time, "status" => "booked", "created_at" => date('Y-m-d H:i:s')];
							Appointment::create($addAppintment);
						}
					}
				}
				try {
					$agencyNotifyData = array(
						'agencyid' => $query->agency_id,
						'title' => 'Updated Schedule Appointment via sms',
						'record_id' => $query->id,
						'record_type' => 'Appointment',
						'msg' => '',
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				} catch (\Throwable $th) {}
						$insertLog = [
						'type' => 'Add Appointment',
						'link' => url('/ap/' . $query->key),
						'module' => 'Patient Appointment',
						'object_id' => $query->id,
						'message' =>'Appointment scheduled via SMS link.',
						'new_response' => serialize(array('appointment_date' => $start_date, 'location_id' => $request->input('location_id'), 'appoinment_time_id' => $request->input('time_id'), 'status' => 'booked')),
						'old_response' => serialize($query->toArray()),
						'ip' => Utility::getIP(),
					];
					LogsService::save($insertLog);
				$this->saveServiceRequest($query->id, 'booked');
				Session::flash('success', 'Appointment successfully update.');
				return redirect('/thank-you');
			} else {
				Session::flash('error', 'Slot is not available for this Date and Time.');
				return redirect('/ap/' . $request->input('key'));
			}
		} else {
			return redirect('expired');
		}
	}
	function AppointmentsUpdate(Request $request)
	{

		$request->input('key');
		$query = Patient::select('id', 'type', 'key', 'agency_id', 'mobile', 'first_name', 'last_name','service_id')->where('id', $request->input('id'))->where('deleted_flag', 'N')->first();


		if (isset($query->id) && $query->id != '') {
			$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
			$oldstat = $request->input('start_date_edit');
			$start_date = date('Y-m-d', strtotime($oldstat));
			$getAppointSchedule = $this->LocationScheduleService->getDetailbyId($request->input('time_id_edit'));
			$data = array(
				'appointment_date' => $start_date,
				'location_id' => $request->input('location_id_edit'),

				'appoinment_time_id' => $request->input('time_id_edit'),


			);
			$update = $this->PatientService->update($data, array('id' => $query->id));
			$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);

			$subject = "[" . $getAgencyName->agency_name . "] NYBest Medical Care Appointment Schedule update";
			$email = 'allstaff@nybestmedical.com';
			$locationname = $getAppointSchedule->address1 . ', ' . $getAppointSchedule->city . ', ' . $getAppointSchedule->state;
			$latitude = $getAppointSchedule->latitude;
			$longitude = $getAppointSchedule->longitude;
			$emailData = array(
				'agencyname' => $getAgencyName->agency_name,
				'insert' => $query->id,
				'first_name' => $query->first_name,
				'last_name' => $query->last_name,
				'mobile_no' => $query->mobile,
				'type' => $query->type,
				'schedule_date' => date('m-d-Y', strtotime($start_date)) . ' ' . date('h:i A', strtotime($getAppointSchedule->start_time)) . ' to ' . date('h:i A', strtotime($getAppointSchedule->end_time)),
				'address' => $locationname,
				'link' => isset($getAppointSchedule->link) && !empty($getAppointSchedule->link) ? $getAppointSchedule->link : 'https://www.google.com/maps/search/?api=1&query=' .$getAppointSchedule->location_name. ' '. $getAppointSchedule->address2. ' '.$getAppointSchedule->city. ' '. $getAppointSchedule->state. ' '. $getAppointSchedule->zip_code
			);
			$checkAppointment = Appointment::where('patient_id', $query->id)->where('appointment_time', null)->where('appointment_date', null)->WhereNull('telehealth_date')->where('doctor_id', null)->first();
			$time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";
			if ($checkAppointment) {
				$checkAppointment->update(['location_id' => $request->input('location_id_edit'), "service_id" => $query->service_id, 'appointment_date' => $start_date . ' ' . $time, "appointment_time" => $time, 'status' => 'booked']);
			} else {
				$checkAppointment = Appointment::where('patient_id', $query->id)->where('appointment_time', $time)->where('appointment_date', $start_date . ' ' . $time)->first();
				if ($checkAppointment) {
					$checkAppointment->update(['location_id' => $request['location_id_edit'], "service_id" => $query->service_id, 'appointment_date' => $start_date . ' ' . $time, "appointment_time" => $time, 'status' => 'booked']);
				} else {
					$addAppintment = ["patient_id" => $query->id, "location_id" => $request['location_id_edit'], "service_id" => $query->service_id, "appointment_date" => $start_date . ' ' . $time, "appointment_time" => $time, "status" => "booked", "created_at" => date('Y-m-d H:i:s')];
					Appointment::create($addAppintment);
				}
			}
			$messages = Utility::getHtmlContent('email_template.email_appointment_schedule_update', $emailData);
			try {
				//code...
				$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages, $query) {
					$message->to($email, "Ny Best Medicals")
						// $message->to($email, "Ny Best Medicals")->cc($query->email)
						->subject($subject)->html($messages);
				});

			} catch (\Throwable $th) {
				//throw $th;
			}
			try {
				$agencyNotifyData = array(
					'agencyid' => $query->agency_id,
					'title' => 'Updated Schedule Appointment via sms',
					'record_id' => $query->id,
					'record_type' => 'Appointment',
					'msg' => '',
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);

				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Appointment Reschedule',
					'link' => url('/patient/appointment-update/') . '/' . $query->id,
					'module' => 'Patient Appointment',
					'object_id' => $query->id,
					'message' => 'Appointment rescheduled via SMS link.',
					'new_response' => serialize(array('patient_id' => $query->id, 'message' => $emailData)),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
			} catch (\Throwable $th) {}
			Session::flash('success', 'Appointment successfully update.');
			return redirect('/thank-you');
		} else {
			return redirect('expired');
		}
	}
	function thankyou()
	{

		return view('thankyou');
	}

	function nyThankyou(Request $request)
	{
		$data['times'] = $request->input('time');
		return view('ny-thankyou', $data);
	}

	function expired()
	{

		return view('errorExpire');
	}

	public function getServices(Request $request)
	{
		$type = $request->type;
		$agencyId = $request->agency_id;
		$serviceList = Cache::get('patient_master_agencies_services', function () use ($type,$agencyId) {
			$query = $this->AgencyWiseServiceService->ServiceListNewWithoutNyBestUser($type, $agencyId);

			// $query = $this->AgencyWiseServiceService->ServiceListNew($type, $agencyId);
			if (!empty($query[0])) {
				return $query;
			} else {
				return Master::getServiceRequestTypeWise($type);
			}
		}, 10 * 60);


		echo json_encode($serviceList);
	}

	public function SendSMSBYPending(Request $request, $id)
	{

		$patient = $this->PatientService->getDetailById($id);
		$user = auth()->user();
		$mobile = str_replace('-', '', $patient->mobile);
		$agencyid = $patient->agency_id;
		$getAgencyName = Agency::getDetailsByAgencyId($agencyid);
		//Appointment is from telehealth, send sms
		$isFromTele = $this->appoimentService->getAppointmentFromTelehealth($patient->id);
		if ($isFromTele == 1) {
			$query = $this->PatientService->getPatientDetailsId($patient->id);
			$getServiceArray = $this->patientWiseServicesRequests->getExistingPatientServices($patient->id);
			$isSendSMS = Common::checkTeleAgencyService($getServiceArray, $query->agency_id);
			if ($isSendSMS == 1) {
				$getAppointSchedule = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($patient->telehealth_time_slot);
				if (!empty($patient->telehealth_time_frame) && $getAppointSchedule) {
					[$fs, $fe] = explode('-', $patient->telehealth_time_frame);
					$getAppointSchedule->start_time = trim($fs) . ':00';
					$getAppointSchedule->end_time   = trim($fe) . ':00';
				}
				$unitId = $patient->telehealth_key;
				$smsMessage = common::sendsmsAgencyTelehealth($getAppointSchedule, $unitId, $query, $getAgencyName, $patient->patient_id);
				if (isset($smsMessage) && !empty($smsMessage)) {
					try {
						$this->SmsService->AgencyWiseSmsDynamic($patient->id, $query->mobile, $smsMessage);
					} catch (\Throwable $th) {
						//throw $th;
					}
				}
				$this->PatientSMSLogService->save(array('patient_id' => $id, 'mobile_no' => $patient->mobile, 'message' => $smsMessage));
			}
		} else {
			if (strtolower($patient->type) == 'caregiver') {

				$agencyname = '';
				if (isset($getAgencyName->agency_name) && $getAgencyName->agency_name != '') {
					$agencyname = $getAgencyName->agency_name;
				}
				if ($patient->key != '') {
					$unitId = $patient->key;
				} else {
					$unitId = uniqid();
				}

				$url = URL::to('/') . '/ap/' . $unitId;
				$namearray = array();
				$serviceId = explode(',', $patient->service_id);
				if (!empty($serviceId[0])) {
					foreach ($serviceId as $vdl) {
						if ($vdl != '') {
							$getMaster = Master::select('name')->where('id', $vdl)->where('del_flag', 'N')->first();
							$namearray[] = $getMaster->name;
						}
					}
				}

				if (isset($patient->language) && strtolower($patient->language) == 'spanish') {
					$htmlLanguage = $getAgencyName->send_sms_spanish;
				} else {
					$htmlLanguage = $getAgencyName->send_sms_eng;
				}
				$htmlStringReplace = $htmlLanguage;

				$disabledServiceIds = Common::getServiceMesgdisable();
				$hasDisabledService = !empty(array_intersect($serviceId, $disabledServiceIds));

				if ($hasDisabledService) {
					$htmlStringReplace = preg_replace(
						"/and\s+you\s+will\s+need\s+to\s+update\s+it\s+to\s+continue\s+employment\s+and\s+be\s+active\s+with\s*(\{\{agency_name\}\}|[A-Za-z0-9 .,'-]+)\s*\.?/i",
						"",
						$htmlStringReplace
					);

					$htmlStringReplace = preg_replace(
						"/and\s+you\s+will\s+need\s+to\s+update\s+it\s+to\s+continue\s+employment\s+and\s+be\s+active\s+with\s*(\{\{agencyname\}\}|[A-Za-z0-9 .,'-]+)\s*\.?/i",
						"",
						$htmlStringReplace
					);

					// Spanish removal (optional)
					$htmlStringReplace = preg_replace(
						"/y\s+deberá\s+actualizarlo.*?activo\s+con\s*(\{\{agency_name\}\}|[A-Za-z0-9 .,'-]+)\s*\.?/i",
						"",
						$htmlStringReplace
					);

					// Spanish removal (optional)
					$htmlStringReplace = preg_replace(
						"/y\s+deberá\s+actualizarlo.*?activo\s+con\s*(\{\{agencyname\}\}|[A-Za-z0-9 .,'-]+)\s*\.?/i",
						"",
						$htmlStringReplace
					);

				} else {
					if (strtolower($patient->language) === 'spanish') {
						$htmlStringReplace .= ' y deberá actualizarlo para continuar trabajando y permanecer activo con ' . $agencyname;
					} else {
						$htmlStringReplace .= ' and you will need to update it to continue employment and be active with ' . $agencyname;
					}
				}
				$htmlStringReplace = str_replace('{{agencyname}}', $agencyname, $htmlStringReplace);
				$htmlStringReplace = str_replace('{{agency_name}}', $agencyname, $htmlStringReplace);
				$htmlStringReplace = str_replace('{{namearray}}', implode(',', $namearray), $htmlStringReplace);
				$htmlStringReplace = str_replace('{{url}}', $url, $htmlStringReplace);
				$smsMessage = $htmlStringReplace;
			}
			if (strtolower($patient->type) == 'caregiver' && $getAgencyName->is_sms == 1) {
				$explode = explode(',', $patient->service_id);

				$getStopSMSServices = Utility::stopSMSService($patient->agency_id);
				$array = array_intersect($explode, $getStopSMSServices);

				if (count($array) == 0) {
					$smsMobileArray = [];
					$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $patient->mobile);
					if ($patient->phone != "") {
						$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $patient->phone);
					}

					if (count($smsMobileArray) > 0) {
						$smsFlag = 0;
						foreach ($smsMobileArray as $smb) {
							if ($smb != "") {
								$sendSmsAgency = $this->SmsService->AgencyWiseSmsDynamic($patient->id, $smb, $smsMessage);
								if($sendSmsAgency){
									$smsFlag = 1;
								}
								$this->PatientSMSLogService->save(array('patient_id' => $id, 'mobile_no' => $smb, 'message' => $smsMessage));
							}
						}
						if($smsFlag == 1){
							$this->PatientService->update(array('patient_sms_flag' => 1, 'key' => $unitId), array('id' => $id));
							$ipaddress = Utility::getIP();
							$insertLog = [
								'type' => 'SMS sent',
								'link' => url('patient/send-sms') . '/' . $id,
								'module' => 'Patient Appointment',
								'object_id' => $id,
								'message' => $user->first_name . ' ' . $user->last_name . ' has Sent SMS notification for the portal.',
								'new_response' => serialize(array('patient_id' => $id, 'created_by' => $user->id, 'message' => $smsMessage, 'patient_sms_flag' => 1)),
								'ip' => $ipaddress,
							];
							LogsService::save($insertLog);
						}
					}
				}
			}
		}

		return 1;
	}

	function SendSMSBYBooked(Request $request, $id)
	{
		$patient = $this->PatientService->getDetailById($id);

		$getAgencyName = Agency::getDetailsByAgencyId($patient->agency_id);
		$isFromTele = $this->appoimentService->getAppointmentFromTelehealth($patient->id);
		if ($isFromTele == 1) {
			$query = $this->PatientService->getPatientDetailsId($patient->id);
			$getServiceArray = $this->patientWiseServicesRequests->getExistingPatientServices($patient->id);
			$isSendSMS = Common::checkTeleAgencyService($getServiceArray, $query->agency_id);
			if ($isSendSMS == 1) {
				$getAppointSchedule = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($patient->telehealth_time_slot);
				if (!empty($patient->telehealth_time_frame) && $getAppointSchedule) {
					[$fs, $fe] = explode('-', $patient->telehealth_time_frame);
					$getAppointSchedule->start_time = trim($fs) . ':00';
					$getAppointSchedule->end_time   = trim($fe) . ':00';
				}
				$unitId = $patient->telehealth_key;
				$smsMessage = common::sendsmsAgencyTelehealthReminder($getAppointSchedule, $unitId, $query, $getAgencyName, $patient->patient_id);
				if (isset($smsMessage) && !empty($smsMessage)) {
					try {
						$smsMobileArray = [];
						$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $patient->mobile);
						if ($patient->phone != "") {
							$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $patient->phone);
						}

						$this->SmsService->AgencyWiseSmsDynamic($patient->id, $query->mobile, $smsMessage);
					} catch (\Throwable $th) {
						//throw $th;
					}
				}
				$this->PatientService->update(array('telehealth_key' => $unitId, 'patient_sms_flag' => 1), array('id' => $id));
				$this->PatientSMSLogService->save(array('patient_id' => $id, 'mobile_no' => $patient->mobile, 'message' => $smsMessage, 'key' => $unitId));
			}
		} else {
			$getAppointSchedule = $this->LocationScheduleService->getDetailbyId($patient->appoinment_time_id);
			$cnt = 1;
			$unitId = $patient->key;
			$url = URL::to('/') . '/ap/' . $unitId;

			if (strtolower($patient->type) == 'caregiver') {
				if (isset($patient->language) && strtolower($patient->language) == 'spanish') {
					$htmlLanguage = $getAgencyName->appointment_send_book_spanish;
				} else {
					$htmlLanguage = $getAgencyName->appointment_send_book_eng;
				}

				$endDateTime = date('m-d-Y', strtotime($patient->appointment_date)) . ' ' . date('h:i A', strtotime($getAppointSchedule->end_time));
				$htmlStringReplace = $htmlLanguage;
				$htmlStringReplace = str_replace('{{appointment_date}}', date('m-d-Y', strtotime($patient->appointment_date)), $htmlStringReplace);
				$htmlStringReplace = str_replace('{{start_date}}', date('m-d-Y', strtotime($patient->appointment_date)), $htmlStringReplace);
				$htmlStringReplace = str_replace('{{start_time}}', date('h:i A', strtotime($getAppointSchedule->start_time)), $htmlStringReplace);
				$htmlStringReplace = str_replace('{{end_time}}', $endDateTime, $htmlStringReplace);
				$htmlStringReplace = str_replace('{{url}}', $url, $htmlStringReplace);
				$htmlStringReplace = str_replace('{{link}}', $getAppointSchedule->link, $htmlStringReplace);
				$smsMessage = $htmlStringReplace;
			}

			if (strtolower($patient->type) == 'caregiver' && $getAgencyName->is_sms == 1) {
				$explode = explode(',', $patient->service_id);
				$getStopSMSServices = Utility::stopSMSService($patient->agency_id);
				$array = array_intersect($explode, $getStopSMSServices);
				if (count($array) == 0) {
					$smsMobileArray = [];
					$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $patient->mobile);
					if ($patient->phone != "") {
						$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $patient->phone);
					}

					if (count($smsMobileArray) > 0) {
						$smsFlag = 0;
						foreach ($smsMobileArray as $smb) {
							if ($smb != "") {
								$sendSmsAgency = $this->SmsService->AgencyWiseSmsDynamic($patient->id, $smb, $smsMessage);
								$this->PatientSMSLogService->save(array('patient_id' => $id, 'mobile_no' => $smb, 'message' => $smsMessage, 'key' => $unitId));
								if($sendSmsAgency){
									$smsFlag = 1;
								}
							}
						}

						if($smsFlag == 1){
							$this->PatientService->update(array('key' => $unitId, 'patient_sms_flag' => 1), array('id' => $id));
							$ipaddress = Utility::getIP();
							$insertLog = [
								'type' => 'Reminder SMS sent',
								'link' => url('patient/send-remainder-sms/') . '/' . $id,
								'module' => 'Patient Appointment',
								'object_id' => $id,
								'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has sent Reminder SMS notification for the portal.',
								'new_response' => serialize(array('patient_id' => $id, 'created_by' => auth()->user()->id, 'message' => $smsMessage, 'patient_sms_flag' => 1)),
								'ip' => $ipaddress,
							];
							LogsService::save($insertLog);
						}
					}
				}
			}
		}

		return 1;
	}

	public function getNotes($id)
	{
		$data['user'] = $user = auth()->user();
		$message = request('message');
		$readMessage = request('readMessage');
		$call_flag = '';
		if ($user['user_type_fk'] == 184) {
			$call_flag = request('call_flag');
		}

		$agency_id = request('agency_id');
		$getPatientDetails = $patient = $this->PatientService->getDetailById($id);
		$ids = [$id];

		if (isset($getPatientDetails->merge_appointment_id) && $getPatientDetails->merge_appointment_id != "") {
			//$ids = [$id, $getPatientDetails->merge_appointment_id];
			$ids = $this->convertMergePatientArray($getPatientDetails->merge_appointment_id,$id);
		}

		$allSMS = PatientNotes::getRecordALLNotesByRecordIDWithArray($ids, $readMessage, $user['id'], $call_flag);
		//echo "<pre>";print_r($allSMS);die;
		return response(json_encode($allSMS), 200)->header('Content-Type', 'application/json');
	}

	function SendNotes(Request $request, $id)
	{

		$auth = auth()->user();
		$message = $request->input('msg-box');


		$type = $request->input('agency_id') == null ? 'Self' : $request->input('agency_id');

		$callFlag = $request->input('radioType') ?? 'Normal';
		$agency_id_main = $request->input('agency_id_main');
		$query = $this->PatientService->getDetailById($id);
		// if ($id == 427293) {

		// 	$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
		// 	$data = [];
		// 	$data['client_name'] = $getAgencyName->client_name;
		// 	$data['appointment_id'] = $query->link_third_party;
		// 	$data['notes'] = $request->input('msg-box');
		// 	$data['created_date'] = date('Y-m-d');
		// 	print_r($data);

		// 	$this->thirdPartyPatientMaster->sendArlaNotesCurl($data);
		// 	die();
		// }
		//$agency_id_main = 482;
		$callFlags = null;
		if ($auth['user_type_fk'] == 184) {
			$callFlags = $callFlag;
		} else {
			$callFlags = $callFlag;
		}


		$tags = $request->tags;
		$tagsArray = json_decode($tags, true);
		$emailArray = [];
		foreach ($tagsArray as $tag) {
			$emailArray[] = $tag['name'];
		}

		$arrayEmail = "";
		if (!empty($emailArray[0])) {
			$arrayEmail = implode(',', $emailArray);
		}

		$update = $this->PatientNotesService->save(array('patient_id' => $id, 'created_by' => $auth['id'], 'type' => $type, 'message' => $message, 'receiver_id' => $agency_id_main, 'call_flag' => $callFlags, 'note_email' => $arrayEmail, 'hha_notes' => $request->hha_notes));
		$lastSaveId = $update;

		$allemails = $emailArray;

		if ($update) {

			$messages = '';
			$pid = $id;
			if ($type != 'Self') {
				$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
				if ($auth['user_type_fk'] == 184) {
					$subject = 'Notification from NY Best Medical care New Notes Added';
					$messages = 'Hello NyBest Medical,<br>';


					if (isset($getAgencyName->nybest_email_notification) && $getAgencyName->nybest_email_notification != '') {
						$explode = explode(',', $getAgencyName->nybest_email_notification);
						$excludedEmails = [
							'li@qualityny.com',
							self::STATIC_EMAIL
						];
						if (count($explode) > 0) {
							foreach ($explode as $ks) {
								if ($ks != '') {
									$email = strtolower(trim($ks));
									if (!in_array($email, $excludedEmails)) {
										$allemails[] = trim($email);
									}
									
								}
							}
						}
					}


					$agency = Agency::find($agency_id_main);

					$message = $request->input('msg-box');
					$details = [
						"greeting" => 'NyBestMedical',
						'actionText' => 'View Record',
						'body' => $message,
						'actionURL' => url('/patient/view/') . $id,
						'record_id' => $id
					];
					// $agency->notify(new MyFirstNotification($details));

					$ipaddress = Utility::getIP();
					$insertLog = [
						'type' => 'Notes Added',
						'link' => url('/patient/view/') . '/' . $id,
						'module' => 'Patient Appointment',
						'object_id' => $id,
						'message' => $auth->first_name . ' ' . $auth->last_name . ' has Notification Sent To Agency',
						'new_response' => serialize(array('patient_id' => $id, 'created_by' => $auth['id'], 'type' => $type, 'message' => $message, 'receiver_id' => $agency_id_main)),
						'ip' => $ipaddress,
					];
					LogsService::save($insertLog);
					$agencyMailFlag = 1;
				} else {
					//$subject = 'Notification from NY Best Medical care New Notes Added';
					$subject = 'Notification from ' . $getAgencyName->agency_name . ' New Notes Added';
					$messages = 'Hello NY Best Medical,<br>';

					$allemails = array('jromero@nybestmedical.com');


					$getAssignUserDetails = UserHelper::getUserDetails($query->assign_user_id);
					if (isset($getAssignUserDetails->email) && $getAssignUserDetails->email != "") {
						$allemails[] = $getAssignUserDetails->email;
					}
					//$allemails = array();

					$agencyMailFlag = 1;
				}


				if ($agencyMailFlag == 1) {
					$username = $auth['first_name'] . ' ' . $auth['last_name'];
					$emailData = array(
						'username' => $username,
						'insert' => $id,
						'first_name' => $query->first_name,
						'last_name' => $query->last_name,
						'mobileNo' => $query->mobile,
						'gender' => $query->gender,
						'notes' => $message,
						'type' => $query->type,
					);

					$messages = Utility::getHtmlContent('email_template.email_create_notes', $emailData);

					//add send email agency module permission notification email
					$recordType = isset($query->type) ? $query->type : "";
					$notificationType = "Send Note";
					$sendEmailNotificationagencyId = isset($query->agency_id) ? $query->agency_id : "";

					/*******Send Mail Notification for general user */
					//$this->SendEmailNotificationSerivce->sendGeneralNotification($recordType, $notificationType, $subject, $messages, "", "", $id);
					$generalNotification = $this->SendEmailNotificationSerivce->sendGeneralNotificationWithEmail($recordType, $notificationType, $subject, $messages, "", "", $id);
					/*************************End Send Mail Notification for general user */
					$sendEmailNotication = [];
					if ($recordType != "" && $sendEmailNotificationagencyId != "") {
						$sendEmailNotication = $this->SendEmailNotificationSerivce->sendEmailNotificationServicesDiscipline($recordType, $notificationType, $sendEmailNotificationagencyId, $subject, $messages, "", "", $id);
					}
					//end code send email notification email

					//add send email user module permission notification email
					$recordTypeUser = $recordType;
					$notificationTypeUser = $notificationType;
					$sendEmailNotificationUserId = isset($query->created_by) ? $query->created_by : "";
					$sendNotificationUserEmailArray = [];

					if ($recordType != "" && $sendEmailNotificationUserId != "") {
						$sendNotificationUserEmailArray = $this->SendEmailNotificationSerivce->sendEmailNotificationUserWithEmail($recordTypeUser, $notificationTypeUser, $sendEmailNotificationUserId, $subject, $messages);
					}

					$userEmail = [];
					$getCreatedUserd = UserHelper::getUserDetails($query->created_by);
					LogsCreateEmailCheck::insert([
						'created_at' => date('Y-m-d H:i:s'),
						'created_by' => auth()->user()->id??'',
						'patient_id' => $id,
						'patient_created_by' => $query->created_by??'',
						'type' => 'Notes'

					]);
					if(isset($getCreatedUserd->creator_email_noti_toggle) && $getCreatedUserd->creator_email_noti_toggle == 1){
						$getCreatedUserEnabledOrNot = $this->userCreatorEmailNotificationService->getAddOrNotUserEmailNotification($query->agency_id,'Add Notes');
						if($getCreatedUserEnabledOrNot ==1){
							$userEmail = array($getCreatedUserd->email);
						}
					}
					$assignAgencyMail = $this->SendEmailNotificationSerivce->getAssignNyUserAgencyMail($sendEmailNotificationagencyId);
					$finalEmail = array_unique(array_merge($allemails, $generalNotification, $sendEmailNotication, $sendNotificationUserEmailArray,$userEmail,$assignAgencyMail));
					//end send email user module permission notification email
					if(empty($auth->agency_fk)){
						$finalEmail = array_diff($finalEmail, [self::STATIC_EMAIL]);
					}
					$this->SendEmailNotificationSerivce->UserMailWithMultipleEmail($finalEmail, "", $subject, $messages, "");
				}
			}

			if ($query->type == 'Caregiver') {
				if ($request->hha_notes == 1) {
					if ($query->link_hha_caregiver != "") {
						$id = $query->link_hha_caregiver;
					} else {
						$getAppointmentDetails = HHAAppointmentHelper::getById($query->hha_id);
						$id = $getAppointmentDetails->caregiver_id;
					}
					HHACaregiversHelper::sendNotes($id, array('subject_id' => $request->subjectNotesId, 'hha_caregivers_notes' => $request->input('msg-box')));
					$this->PatientNotesService->update(array('hha_notes'=>1),array('id'=>$lastSaveId));
					$hhaLogData = [
                        'patient_id'=>$query->id,
                        'hha_patient_id'=>$id,
                        'type'=>$query->type,
                        'hha_module_type'=>'Notes',
                        'send_response'=>serialize($request->except('_token')),
                        'ip_address' => $ipaddress,
                        'action'=>'Add',
                       
                    ];
                    $this->hhaLogService->save($hhaLogData);
				}
			}else{
				if ($request->hha_notes == 1) {

					if($query->link_hha_patient != "" || $query->hha_id !=""){
						if ($query->link_hha_patient != "") {
							$id = $query->link_hha_patient;
						} else {
							$id = $query->hha_id;
						}
						HHAPatientHelper::sendNotes($id, array('subject_id' => $request->subjectNotesId, 'notes' => $request->input('msg-box')));
						$this->PatientNotesService->update(array('hha_notes'=>1),array('id'=>$lastSaveId));
					}

					$hhaLogData = [
                        'patient_id'=>$query->id,
                        'hha_patient_id'=>$id,
                        'type'=>$query->type,
                        'hha_module_type'=>'Send Notes',
                        'send_response'=>serialize($request->except('_token')),
                        'ip_address' => $ipaddress,
                        'action'=>'Add',
                       
                    ];
                    $this->hhaLogService->save($hhaLogData);
				}
			}

			// Store notification data for show

			$this->sendNotificationToUser($pid, "Notes", $request->input('msg-box'));
			$getNotesDetails = $this->PatientNotesService->getAllDetailsById($update);

			if ($request->input('agency_id') == 'Agency') {
				if ($query->platform_type == "arla") {
					if ($query->link_third_party != "") {
						$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
						$data = [];
						$data['client_name'] = $getAgencyName->client_name;
						$data['appointment_id'] = $query->link_third_party;
						$data['notes'] = $request->input('msg-box');
						$data['created_date'] = date('Y-m-d');
						$this->thirdPartyPatientMaster->sendArlaNotesCurl($data);
					}
				}
			}
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $query->agency_id,
						'title' => 'Added new Notes',
						'record_id' => $query->id,
						'record_type' => 'Notes',
						'msg' => '',
						'res_data' => serialize(array('patient_id' => $id, 'created_by' => $auth['id'], 'type' => $type, 'message' => $message, 'receiver_id' => $agency_id_main)),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			return $getNotesDetails;
		} else {
			return 0;
		}
	}

	function patientStatus(Request $request)
	{
		$id = $request->input('id');

		$update = $this->PatientService->update(array('status' => "Not interested", 'last_status_update' => date('Y-m-d H:i:s'), 'last_status_update_by' => auth()->user()->id), array('id' => $id));
		$this->saveServiceRequest($id, 'Not interested');
		if ($update) {
			return 1;
		} else {
			return 0;
		}
	}
	function archive(Request $request)
	{
		$patientIds = $request->input('patient_id');
		$explode = explode(',', $patientIds);

		if (count($explode) > 0) {
			foreach ($explode as $vsl) {
				$patient = $this->PatientService->getDetailById($vsl);
				$user = $this->PatientService->archived($vsl);

				$ipaddress = Utility::getIP();
				$user = auth()->user();
				$insertLog = [
					'type' => 'Appointment Archived',
					'link' => url('/patient/patient-archive'),
					'module' => 'Patient Appointment',
					'object_id' => $vsl,
					'message' => $user->first_name . ' ' . $user->last_name . ' has Archived Appointment',
					'new_response' => serialize(array('archived_at' => date('Y-m-d H:i:s'),'is_archive' => 1)),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				try {
					if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
						$agencyNotifyData = array(
							'agencyid' => $patient->agency_id,
							'title' => 'Archive Appointment',
							'record_id' => $vsl,
							'record_type' => 'Appointment',
							'msg' => '',
							'res_data' => serialize(array('archived_at' => date('Y-m-d H:i:s'))),
						);
						Common::insertAgencyNotificationsOfUser($agencyNotifyData);
					}
				} catch (\Throwable $th) {}
			}
		}
		if ($user) {
			return 1;
		} else {
			return 0;
		}
	}

	function PatientArchiveList(Request $request)
	{

		$data['user'] = auth()->user();
		$data['listHeadingName'] = "Archived Appointments";
		$data['appointmentUrl'] = "archive-list";

		$agency_fk = $data['agency_fk'] = request('agency_fk');
		$full_name = $data['full_name'] = request('first_name');
		$mobile = $data['mobile'] = request('mobile');
		$status = $data['status'] = request('status');
		$appointment_date = $data['appointment_date'] = request('appointment_date');
		$location_id = $data['location_id'] = request('locationId');
		$service_id = $data['service_id'] = request('service_id');
		$type = $data['type'] = request('type');
		$created_date = $data['created_date'] = request('created_date');
		$due_date = $data['due_date'] = request('due_date');
		$sms_status = $data['sms_status'] = request('sms_status');
		$record_form = $data['record_form'] = request('record_form');
		$assign_user_id = $data['assign_user_id'] = request('assign_user_id');

		//get
		$data['selected_sms_status'] = request('sms_status') != null ? explode(',', request('sms_status')) : [];
		$data['selected_status'] = explode(',', request('status'));
		$data['selected_agency_fk'] = explode(',', request('agency_fk'));
		$data['selected_service_id'] = explode(',', request('service_id'));
		$data['selected_assign_user_id'] = explode(',', request('assign_user_id'));
		$data['selected_location_id'] = explode(',', request('locationId'));

		$query = $this->PatientService->PatientArchiveList("", $full_name, "", $mobile, $status, "", $appointment_date, $agency_fk, $location_id, $service_id, $type, $created_date, $sms_status, $record_form, $due_date, $assign_user_id);
		$data['agencyList'] = $agencyList = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();

		$agencyFinal = array();
		foreach ($agencyList as $lvs) {
			$agencyFinal[$lvs->id] = $lvs->agency_name;
		}

		$doctorList = $this->DoctorService->getDoctorList();
		$docselect = array();
		$doctorArray = array();
		if (count($doctorList) > 0) {
			foreach ($doctorList as $doc) {
				$doctorArray[$doc->id] = $doc->full_name;
				$docselect[] = $doc;
			}
		}
		$data['doctor_list'] = $docselect;

		foreach ($query as $vsl) {
			$explode = explode(',', $vsl->service_id);
			$services = [];
			foreach ($explode as $val) {
				if ($val != "") {
					$services[] = $val;
				}
			}

			$newss = "" . $vsl->service_id;
			$vsl->agency_name = isset($agencyFinal[$vsl->agency_id]) ? $agencyFinal[$vsl->agency_id] : "";
			$vsl->full_name = isset($doctorArray[$vsl->doctor_id]) ? $doctorArray[$vsl->doctor_id] : "";
			$getLocationDetails = $this->LocationMasterService->getDetailbyId($vsl->location_id);
			$address1 = '';
			$address2 = '';
			$city = '';
			$state = '';
			$zip_code = '';

			if (isset($getLocationDetails->address1) && $getLocationDetails->address1 != '') {
				$address1 = $getLocationDetails->address1;
			}
			if (isset($getLocationDetails->address2) && $getLocationDetails->address2 != '') {
				$address2 = ',' . $getLocationDetails->address2;
			}
			if (isset($getLocationDetails->city) && $getLocationDetails->city != '') {
				$city = ',' . $getLocationDetails->city;
			}
			if (isset($getLocationDetails->state) && $getLocationDetails->state != '') {
				$state = ',' . $getLocationDetails->state;
			}
			if (isset($getLocationDetails->zip_code) && $getLocationDetails->zip_code != '') {
				$zip_code = ',' . $getLocationDetails->zip_code;
			}

			$vsl->full_address = $address1 . ' ' . $address2 . ' ' . $city . ' ' . $state . ' ' . $zip_code;

			$getLocationSchedule = $this->LocationScheduleService->getDetailbyIdAll($vsl->appoinment_time_id);

			$vsl->start_time = isset($getLocationSchedule->start_time) ? $getLocationSchedule->start_time : "";

			$vsl->end_time = isset($getLocationSchedule->end_time) ? $getLocationSchedule->end_time : "";
			$masterListArray = array();
			if (count($services) > 0) {
				$masterList = Master::select('name')->whereRaw('id  IN (' . implode(',', $services) . ')')->where('del_flag', 'N')->get();


				foreach ($masterList as $names) {
					$masterListArray[$vsl->id][] = $names->name;
				}
			}

			$vsl->name = '';
			if (isset($masterListArray[$vsl->id]) && $masterListArray[$vsl->id] != '') {
				$vsl->name = implode(',', $masterListArray[$vsl->id]);
			}
		}
		$data['query'] = $query;
		$data['open_record_list'] = $query;

		$data['location_list'] = $this->LocationMasterService->AllListWithoutPaginate();
		$data['serviceList'] = Master::getServiceRequestWithDisabled();
		$data['assign_user_list'] = User::getNYBestUserData();

		return view("patient.patient_archive_list", $data);
	}

	function unarchive(Request $request)
	{
		$patientIds = $request->input('patient_id');
		$explode = explode(',', $patientIds);

		if (count($explode) > 0) {
			foreach ($explode as $vsl) {
				$getDetails = $this->PatientService->getPatientDetailsByIdWhitoutAgency($vsl);
				$user = $this->PatientService->unarchived($vsl);

				$ipaddress = Utility::getIP();
				$user = auth()->user();
				$insertLog = [
					'type' => 'Appointment Unarchived',
					'link' => url('/patient/patient-unarchive'),
					'module' => 'Patient Appointment',
					'object_id' => $vsl,
					'message' => $user->first_name . ' ' . $user->last_name . ' has Unarchived Appointment',
					'old_response' => serialize(array('archived_at' => $getDetails->archived_at)),
					'new_response' => serialize(array('archived_at' => NULL,'is_archive' => 0)),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				try {
					if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
						$agencyNotifyData = array(
							'agencyid' => $getDetails->agency_id,
							'title' => 'Unarchive Appointment',
							'record_id' => $vsl,
							'record_type' => 'Appointment',
							'msg' => '',
							'res_data' => serialize(array('unarchived_at' => date('Y-m-d H:i:s'))),
						);
						Common::insertAgencyNotificationsOfUser($agencyNotifyData);
					}
				} catch (\Throwable $th) {}
			}
		}

		if ($user) {
			return 1;
		} else {
			return 0;
		}
	}

	function patientDocumentDelete($recordId, $id)
	{
		$oldDocumentDetails = $this->DocumentPatientService->getDocumentDetailsById($id, $recordId);
		$insert = $this->DocumentPatientService->SoftDelete(array('deleted_flag' => 'Y'), array('id' => $id));
		if ($insert) {
			$ipaddress = Utility::getIP();
			$user = auth()->user();
			$insertLog = [
				'type' => 'Delete Document Appointment',
				'link' => url('/patient/payment-type'),
				'module' => 'Patient Appointment',
				'object_id' => $recordId,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Delete Document Appointment',
				'new_response' => serialize(array('deleted_flag' => 'Y','id' => $id,'deleted_date'=>date('Y-m-d H:i:s'),'deleted_by' => $user->id)),
				'ip' => $ipaddress,
			];

			$medicalList =0;
			if(isset($oldDocumentDetails->medication_list) && $oldDocumentDetails->medication_list !=""){
				$medicalList = $oldDocumentDetails->medication_list;
			}
			DocumentHelper::updatePatientDocumentCounts($recordId,0,0, $medicalList, $oldDocumentDetails->insurance_elg,0,$oldDocumentDetails->mdo_tag);
			LogsService::save($insertLog);
			Session::flash('success', 'Document successfully deleted');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
		}
		return redirect()->back();
	}

	function DueDateUpdate(Request $request)
	{
		$duedate = $request->input('due_date');
		$patient_id = $request->input('patient_id');
		$user = auth()->user();
		$update = $this->PatientService->update(array('due_date' => date('Y-m-d', strtotime($duedate))), array('id' => $patient_id));
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Due Date Appointment',
			'link' => url('/patient/due-date'),
			'module' => 'Patient Appointment',
			'object_id' => $patient_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Due Date Updated Appointment',
			'new_response' => serialize(array('due_date' => date('Y-m-d', strtotime($duedate)))),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		try {
			if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
				$getNewData = $this->PatientService->getDetailById($patient_id);
				$agencyNotifyData = array(
					'agencyid' => $getNewData->agency_id,
					'title' => 'Updated Medical due date of appointment',
					'record_id' => $patient_id,
					'record_type' => 'Appointment',
					'msg' => '',
					'res_data' => serialize(array('due_date' => date('Y-m-d', strtotime($duedate)))),
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);
			}
		} catch (\Throwable $th) {}
		return 1;
	}
	function undo($id)
	{
		$getOldResponse = $this->PatientService->getDetailById($id);
		$this->saveServiceRequest($id, $getOldResponse->prev_status);
		$update = $this->PatientService->update(array('status' => $getOldResponse->prev_status, 'last_status_update' => date('Y-m-d H:i:s'), 'last_status_update_by' => auth()->user()->id), array('id' => $id));
		return 1;
	}
	function ReminderAppointment(Request $request)
	{

		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'email' => 'required',
			'rtype' => 'required',
			'notes' => 'required',



		]);
		if ($validator->fails()) {
			return redirect("/patient/view/" . $request->input('patient_id'))
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {
			if ($request->input('rtype') == 'EveryDate') {
				$date = date('Y-m-d', strtotime($request->input('date')));
			} else {
				$date = date('Y-m-d');
				$date = date('Y-m-d', strtotime('+' . $request->input('every_month') . ' months', strtotime($date)));
			}

			$update = $this->nyBestReminder->save(array('patient_id' => $request->input('patient_id'), 'email' => $request->input('email'), 'notes' => $request->input('notes'), 'type' => $request->input('rtype'), 'date' => $date, 'every_month' => $request->input('every_month'), 'mobile' => $request->input('mobile')));

			if ($update) {
				$getpatientDetails = $this->PatientService->getDetailById($request->input('patient_id'));
				if (isset($getpatientDetails->record_id)) {
					$relatedatientId = Record::getDetailsByRecordid($getpatientDetails->record_id);
					$masterDetails = Master::getDetailsById($relatedatientId->patient_status);
					$staus = isset($masterDetails->name) ? $masterDetails->name : "";
					RecordReportService::save(
						array('record_id' => $getpatientDetails->record_id, 'type' => 'NyBest Medicare ', 'subject' => 'Reminder for Ny Best', 'notes' => ' Reminder for ny best medicare', 'status' => $staus)
					);
				}
				$ipaddress = Utility::getIP();
				$user = auth()->user();
				$insertLog = [
					'type' => 'Reminder Appointment',
					'link' => url('/patient/payment-type'),
					'module' => 'Patient Appointment',
					'object_id' => $request->input('patient_id'),
					'message' => $user->first_name . ' ' . $user->last_name . ' has Reminder Updated Appointment',
					'new_response' => serialize(array('patient_id' => $request->input('patient_id'), 'email' => $request->input('email'), 'notes' => $request->input('notes'), 'type' => $request->input('rtype'), 'date' => $date, 'every_month' => $request->input('every_month'), 'mobile' => $request->input('mobile'))),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				return 1;
			} else {
				return 0;
			}
		}
	}

	function ReminderAppointmentList($id)
	{
		$data['query'] = $this->nyBestReminder->getReminderAppoinment($id);
		return view('patient.reminder_appointment_view', $data);
	}
	function GarbaseCollection($id)
	{
		$insert = $this->PatientService->SoftDelete(array('deleted_flag' => 'Y', 'garbase_status' => 1), array('id' => $id));
		if ($insert) {
			Session::flash('success', 'Appointment successfully garbage');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
		}
		return redirect()->back();
	}
	function StatusWiseRecord(Request $request)
	{
		$data['user'] = auth()->user();

		$status = request('status');
		$data['all_status'] = $status;

		$data['listHeadingName'] = $status . " Appointments";
		if ($status == "cancel") {
			$data['appointmentUrl'] = "appointment/status?status=" . $status;
		} else {
			$data['appointmentUrl'] = "patient-refused?status=" . $status;
		}

		$data['agencyList'] = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
		$agency_fk = $data['agency_fk'] = request('agency_fk');
		$full_name = $data['full_name'] = request('first_name');
		$mobile = $data['mobile'] = request('mobile');
		$data['status'] = $status_new = $request->input('status');
		$appointment_date = $data['appointment_date'] = request('appointment_date');
		$location_id = $data['location_id'] = request('locationId');
		$service_id = $data['service_id'] = request('service_id');
		$type = $data['type'] = request('type');
		$created_date = $data['created_date'] = request('created_date');
		$due_date = $data['due_date'] = request('due_date');
		$sms_status = $data['sms_status'] = request('sms_status');
		$record_form = $data['record_form'] = request('record_form');
		$assign_user_id = $data['assign_user_id'] = request('assign_user_id');

		//get
		$data['selected_sms_status'] = request('sms_status') != null ? explode(',', request('sms_status')) : [];
		$data['selected_status'] = explode(',', request('status'));
		$data['selected_agency_fk'] = explode(',', request('agency_fk'));
		$data['selected_service_id'] = explode(',', request('service_id'));
		$data['selected_assign_user_id'] = explode(',', request('assign_user_id'));
		$data['selected_location_id'] = explode(',', request('locationId'));

		$data['query'] = $queryData = $this->PatientService->getData($status, $full_name, "", $mobile, "", $appointment_date, $agency_fk, $location_id, $service_id, $type, $created_date, $sms_status, $record_form, $due_date, $assign_user_id, "");
		$data['open_record_list'] = $queryData;

		$data['location_list'] = $this->LocationMasterService->AllListWithoutPaginate();
		$data['serviceList'] = Master::getServiceRequestWithDisabled();
		$data['assign_user_list'] = User::getNYBestUserData();

		return view('patient.patient_status_list', $data);
	}
	function NextAppoinment(Request $request)
	{
		$auth = auth()->user();
		$duedate = $request->input('appoinment_date');
		$patient_id = $request->input('patient_id');

		$update = $this->PatientService->update(array('next_appoinment_date' => date('Y-m-d', strtotime($duedate)), 'next_appoinment_by' => $auth['id']), array('id' => $patient_id));
		$ipaddress = Utility::getIP();
		$user = auth()->user();
		$insertLog = [
			'type' => 'Next Appointment',
			'link' => url('/patient/next-appoinment-date/' . $patient_id),
			'module' => 'Patient Appointment',
			'object_id' => $patient_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Next Appointment Updated',
			'new_response' => serialize(array('next_appoinment_date' => date('Y-m-d', strtotime($duedate)), 'next_appoinment_by' => $auth['id'])),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		try {
			if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
				$new_response = $this->PatientService->getDetailById($patient_id);
				$agencyNotifyData = array(
					'agencyid' => $new_response->agency_id,
					'title' => 'Updated Next Appointment date',
					'record_id' => $patient_id,
					'record_type' => 'Appointment',
					'msg' => '',
					'res_data' => serialize(array('next_appoinment_date' => date('Y-m-d', strtotime($duedate)), 'next_appoinment_by' => $auth['id'])),
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);
			}
		} catch (\Throwable $th) {}
		return 1;
	}
	public function CompletedDate(Request $request)
	{
		$auth = auth()->user();
		$duedate = $request->input('completed_date');
		$patient_id = $request->input('patient_id');

		$update = $this->PatientService->update(array('status' => 'completed', 'completed_date' => date('Y-m-d', strtotime($duedate)), 'completed_by' => $auth['id'], 'last_status_update' => date('Y-m-d H:i:s'), 'last_status_update_by' => $auth['id']), array('id' => $patient_id));

		$ipaddress = Utility::getIP();
		$user = auth()->user();
		$insertLog = [
			'type' => 'Completed Date Appointment',
			'link' => url('/patient/view/' . $patient_id),
			'module' => 'Patient Appointment',
			'object_id' => $patient_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Completed Date Appointment Updated',
			'new_response' => serialize(array('completed_date' => date('Y-m-d', strtotime($duedate)), 'completed_by' => $auth['id'])),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		try {
			if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
				$getExistingRecord = $this->PatientService->getDetailByIdNew($patient_id);
				$agencyNotifyData = array(
					'agencyid' => $getExistingRecord->agency_id,
					'title' => 'Updated Completed Date of Appointment',
					'record_id' => $patient_id,
					'record_type' => 'Appointment',
					'msg' => '',
					'res_data' => serialize(array('completed_date' => date('Y-m-d', strtotime($duedate)), 'completed_by' => $auth['id'])),
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);
			}
		} catch (\Throwable $th) {}
		$this->saveServiceRequest($patient_id, "complete");
		return 1;
	}

	public function AssignNyBestUser(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'assign_nybest_user' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], $this->successStatus);
		} else {
			$update = $this->PatientService->update(array('assign_user_id' => $request->input('assign_nybest_user')), array('id' => $request->input('patient_id')));
			if ($update) {
				$query = AssignNyBestUserService::checkRecordAssignORNot($request->input('patient_id'));
				if (isset($query->id) && $query->id != '') {
					AssignNyBestUserService::update(array('assign_user_id' => $request->input('assign_nybest_user'), 'notes' => $request->input('notes_ny_id')), array('id' => $query->id));
				} else {
					AssignNyBestUserService::save(array('patient_record_id' => $request->input('patient_id'), 'assign_user_id' => $request->input('assign_nybest_user'), 'notes' => $request->input('notes_ny_id')));
				}
				return 1;
			} else {
				return 0;
			}
		}
	}
	public function AttachmentPDF(Request $request)
	{

		$image = '';

		if ($request->file('attchment_pdf') != '') {
			$priceImage = $request->file('attchment_pdf');
			$name = uniqid() . time() . '.' . $priceImage->getClientOriginalExtension();
			if(env('FILE_UPLOAD_PERMISSION') =='development'){
				$destination = public_path('uploadedfiles/attachment');
				$priceImage->move($destination, $name);
			}else{
				$image = Storage::disk('s3')->putFileAs('patientattachment', $priceImage, $name);
			}
			$image = $name;
		}
		if ($image != '') {
			$getOldResponse = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->id);

			$update = $this->PatientService->update(array('attachment_document' => $image), array('id' => $request->input('id')));
			$ipaddress = Utility::getIP();
			$user = auth()->user();
			$insertLog = [
				'type' => 'Attachment Appointment',
				'link' => url('/patient/attachment-pdf'),
				'module' => 'Patient Appointment',
				'object_id' => $request->input('id'),
				'message' => $user->first_name . ' ' . $user->last_name . ' has Attachment Updated Appointment',
				'old_response' => serialize($getOldResponse->toArray()),
				'new_response' => serialize(array('id'=>$request->id,'attachment_document' => $image)),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$getNewData = $this->PatientService->getDetailById($request->input('id'));
					$agencyNotifyData = array(
						'agencyid' => $getNewData->agency_id,
						'title' => 'Uploaded Attachment from Appointment',
						'record_id' => $request->input('id'),
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => serialize(array('attachment_document' => $image)),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
		}
		return response()->json(['error_msg' => "Attachment updated successfully", 'status' => 1, 'data' => array('attachment' => $image)], 200);
	}
	public function paymentTypeStatus(Request $request)
	{
		$getExistingData = $this->PatientService->getDetailById($request->input('id'));
		$this->PatientService->update(array('payment_type' => $request->input('payment_type')), array('id' => $request->input('id')));
		$ipaddress = Utility::getIP();
		$user = auth()->user();
		$getMaster = Master::whereRaw('id ="' . $request->input('payment_type') . '"')->where('del_flag', 'N')->first();
		$getNewData = $request->except('_token');
		$insertLog = [
			'type' => 'Update Payment Type',
			'link' => url('/patient/payment-type'),
			'module' => 'Patient Appointment',
			'object_id' => $request->input('id'),
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Payment Type to '.$getMaster->name,
			'old_response' => serialize($getExistingData->toArray()),
			'new_response' => serialize($getNewData),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		try {
			if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
				$agencyNotifyData = array(
					'agencyid' => $getExistingData->agency_id,
					'title' => 'Update Payment Type',
					'record_id' => $request->input('id'),
					'record_type' => 'Appointment',
					'msg' => '',
					'res_data' => serialize($getNewData),
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);
			}
		} catch (\Throwable $th) {}
		return response()->json(['error_msg' => "Payment type updated successfully", 'status' => 1, 'data' => array()], 200);
	}
	public function sendDocusign(Request $request)
	{
		$user = auth()->user();
		$recordId = request('id');
		$template_id = request('template_id');
		$query = Template::getDetailsById($template_id);
		$querySigner = DocumentSignerMaster::select('name', 'id', 'user_id')->where('template_id', $template_id)->where('del_flag', 'N')->get();
		$relatedatientId = $record = $this->PatientService->getDetailByIdNew($request->input('patient_id'));
		$rand = uniqid();
		if (count($querySigner) > 0) {
			foreach ($querySigner as $key => $val) {
				$pending = '';
				$SourceFiles = '';
				if ($val->name == $querySigner[0]->name) {
					$pending = 'Pending';
					$SourceFiles = $query->upload_document;
				}
				if ((isset($relatedatientId->patient_related_id) && $relatedatientId->patient_related_id != '') && $val->name == 'RelatedPatient') {
					$recordIds = $relatedatientId->patient_related_id;
				} else if ($val->name == 'OfficeStaff' && $val->user_id != '') {
					$recordIds = $val->user_id;
				} else {
					$recordIds = $record->id;
				}
				$dataInsert = array(
					'caregiver_code' => $recordIds,
					'templete_id' => $template_id,
					'sender_name' => $record->first_name . ' ' . $record->last_name,
					'status' => $pending,
					'created_date' => date('Y-m-d H:i:s'),
					'created_by' => $user->id,
					'type' => 'Record',
					'sourceFile' => $SourceFiles,
					'main_intakeId' => $request->input('patient_id'),
					'sent_on' => $val->name,
					'groupId' => $rand


				);
				$ipaddress = Utility::getIP();
				$user = auth()->user();
				$insertLog = [
					'type' => 'Send Document Sign Appointment',
					'link' => url('/patient/send-docusign'),
					'module' => 'Patient Appointment',
					'object_id' => $request->input('patient_id'),
					'message' => $user->first_name . ' ' . $user->last_name . ' has Send Document Sign Updated Appointment',
					'new_response' => serialize($dataInsert),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				$documenId = PatientDocumentSentReportService::save($dataInsert);
			}
			return response()->json(['error_msg' => 'Template added successfully.', 'data' => array()], 200);
		}
		return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'data' => array()], 500);
	}

	function sendDocusignList(Request $request)
	{
		$patient_id = $request->input('record_id');
		$documentList = PatientDocumentSentReportService::getResponseListNew($patient_id);
		if (count($documentList) > 0) {
			foreach ($documentList as $kdd) {
				$tests = PatientDocumentSentReportService::getPdfGenerate($kdd->groupId);
				$kdd->pdf_generate = isset($tests->pdf_generate) ? $tests->pdf_generate : "";
				$totalPending = PatientDocumentSentReportService::pendingDocumentCount($kdd->groupId);

				$kdd->totalSigner = $totalPending;
			}
		}
		$data['document_list'] = $documentList;
		return view('patient.patient_doc_list', $data);
	}

	function sendDocusignRequest(Request $request)
	{
		$groupId = $request->input('groupId');
		$document_id = $request->input('document_id');
		$enrollment_id = $request->input('enrollment_id');


		$query = PatientDocumentSentReportService::getDocumentLsy($groupId);

		$response = '';
		if (!empty($query)) {

			foreach ($query as $keys) {


				if ($keys->status != 'Completed') {
					$status = '<label class="badge badge-warning">Pending<label>';
					$link = URL::to('/') . '/patient-sign/' . $keys->id . '/' . $keys->groupId;
					$spans = '<span class="col-md-3"><a target="_blank" href="' . $link . '" ><i class="fa fa-desktop" aria-hidden="true"></i></a></span>';
				} else {
					$status = '<label class="badge badge-success">Completed<label>';
					$spans = '';
				}
				$name = $keys->sent_on;
				if ($keys->sent_on == 'OfficeStaff') {
					$name = 'Admin';
				}
				$response .= '<div class="box-body no-padding"><ul class="nav nav-pills nav-stacked"><li class=""><span class="col-md-3" style="margin-right:30px;">' . $name . '&nbsp;&nbsp;&nbsp;' . $status . '</span>' . $spans . '</li></ul></div>';
			}
		}
		echo $response;
	}

	function documentView($newId, $groupId)
	{
		$data['querynew'] = PatientDocumentSentReportService::getDos($groupId);
		$data['document'] = $query = PatientDocumentSentReportService::getDetailsById($newId);
		$officeType = '';
		if ($query->sent_on == 'OfficeStaff') {
			$autoId = User::select('first_name', 'last_name')->where('id', $query->caregiver_code)->first();

			$officeType = ucfirst($autoId->first_name . ' ' . $autoId->last_name);
		}

		$data['officeStass'] = $officeType;
		if (strtolower($query->status) == 'completed') {
			return redirect('/thankyou');
		}
		$data['document_report_id'] = $query->id;
		$data['main_intakeId'] = $data['document']->main_intakeId;

		$data['document_pdf'] = $subrepory = Template::getDetailsById($query->templete_id);
		$data['id'] = $query->id;
		$data['groupId'] = $groupId;
		$data['rand'] = uniqid();
		$data['template_id'] = $query->templete_id;
		$data['sessionIds'] = $query->id;
		$data['sessionId'] = $query->id;
		$data['department'] = 'web';
		$response = '';
		if (isset($subrepory->response) && $subrepory->response != '') {
			$response = unserialize($subrepory->response);
		}
		$final_array = array();
		$Signinsert = array();
		if (isset($subrepory->docWidth) && $subrepory->docWidth != '') {
			$data['docWidth'] = $docWidth = $subrepory->docWidth;
		}
		$IntakeArray = array();
		$max = array();
		$SubIntakeArray = array();
		$data['sent_on'] = $query->sent_on;
		$maxs = '';
		if (isset($response) && $response != '') {
			$final_array[] = $docWidth;


			foreach ($response as $val) {
				$final_array[] = $val;
				$Signinsert[] = $val;
				$max[] = $val['page'];

				$maxs = max($max);

				if (isset($val['temp3']) && $val['temp3'] != '') {

					$subresponse = $this->RecordFieldsResponse($data['main_intakeId'], $val['temp3']);
					$SubIntakeArray[] = $subresponse;
				}
			}
		}

		$IntakeArray = $SubIntakeArray;

		$data['templateFields'] = json_encode($final_array, true);

		$data['Signinsert'] = json_encode($Signinsert, true);
		$data['device_type'] = request('device_type');
		$data['device_info'] = request('device_info');
		$data['removeScript'] = 'docusign';
		$data['LookUpResponses'] = json_encode($IntakeArray);
		$data['max'] = $maxs;
		$data['sent_on'] = isset($query->sent_on) ? $query->sent_on : "defualt";
		$checkSignerFirst = PatientDocumentSentReportService::pendingDocument($groupId);

		$data['errorSigner'] = isset($checkSignerFirst->sent_on) ? $checkSignerFirst->sent_on : "";

		return view('docusign.patient_docusign', $data);
	}

	function RecordFieldsResponse($id, $key)
	{
		$key = $key;
		$user_id = $id;
		$final = EsignHelper::getNyBestResponse($key, $user_id);
		return $final;
	}
	function documentInsertView()
	{

		$template_id = request('id');

		$document_report_id = request('document_report_id');
		$action = request('action');
		$groupId = request('groupId');
		$permission = request('permission');
		$response = EsignHelper::PatientdocumentInsert($template_id, $document_report_id, $action, $groupId, $permission);

		if ($response) {
			$data = array(
				'id' => $response
			);
			return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => array($data)]);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "No record available.", 'data' => array()]);
		}
	}

	function getHHADocument(Request $request)
	{
		$agencyId = $request->input('agencyId');
		$patientId = $request->input('patientId');
		$officeID = '2';
		$record = $this->PatientService->getDetailByIdNew($patientId);

		$documents = [];
		$caregiver = HHACaregivers::where("caregiver_id", $record->link_hha_caregiver)->where('agency_fk', $agencyId)->first();

		if (isset($caregiver->id) && $caregiver->id != '') {
			$documents = HHACaregiversHelper::getCaregiverMedicalDocument($agencyId, $caregiver->officeId);
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyId)->where('id', $record->hha_id)->first();
			$documents = HHACaregiversHelper::getCaregiverMedicalDocument($agencyId, $getCaregiverDetails->office_id);
		}

		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $documents, 'officeId' => $record->link_hha_caregiver]);
	}

	function getHHAOtherComplience(Request $request)
	{
		$agencyId = $request->input('agencyId');
		$patientId = $request->input('patientId');
		$officeID = '2';
		$record = $this->PatientService->getDetailByIdNew($patientId);

		$documents = [];

		if ($record->link_hha_caregiver != '') {
			$caregiver = HHACaregivers::select('officeId', 'agency_fk')->where("caregiver_id", $record->link_hha_caregiver)->where('agency_fk', $agencyId)->first();

			$documents = HHACaregiversHelper::getCaregiverOtherCompliance($agencyId, $caregiver->officeId);
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyId)->where('id', $record->hha_id)->first();
			$documents = HHACaregiversHelper::getCaregiverOtherCompliance($agencyId, $getCaregiverDetails->office_id);
		}
		// HHACaregivers::
		//hiten

		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $documents, 'officeId' => $record->link_hha_caregiver]);
	}


	function getCompienceMedicalResults(Request $request)
	{
		$agencyId = $request->input('agencyId');
		$id = $request->input('id');
		$medicaid_id = $request->input('medicaid_id');
		$record = $this->PatientService->getDetailByIdNew($id);

		$officeID = 0;
		if ($record->link_hha_caregiver != '') {
			$caregiver = HHACaregivers::where("caregiver_id", $record->link_hha_caregiver)->where('agency_fk', $agencyId)->first();
			$officeID = $caregiver->officeId;
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyId)->where('id', $record->hha_id)->first();
			$officeID = $getCaregiverDetails->office_id;
		}


		$query = HHACaregiversHelper::getCaregiverOtherComplienceMedicalResults($agencyId, $medicaid_id, $officeID);
		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $query, "office" => $officeID]);
	}
	function getCaregiverMedicalResults(Request $request)
	{
		$agencyId = $request->input('agencyId');
		$id = $request->input('id');
		$patientId = $request->input('patientId');
		$medicaid_id = $request->input('medicaid_id');
		$record = $this->PatientService->getDetailByIdNew($patientId);

		$officeID = 0;

		if (isset($record->link_hha_caregiver) && $record->link_hha_caregiver != '') {
			$caregiver = HHACaregivers::where("caregiver_id", $record->link_hha_caregiver)->where('agency_fk', $agencyId)->first();
			$officeID = $caregiver->officeId;
		} else {
			if (isset($record->hha_id)) {
				$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyId)->where('id', $record->hha_id)->first();
				$officeID = $getCaregiverDetails->office_id;
			}
		}


		$query = HHACaregiversHelper::getCaregiverMedicalResults($agencyId, $medicaid_id, $officeID);
		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $query, "dddd" => $officeID]);
	}
	function getHHADocumentType(Request $request)
	{
		$agencyId = $request->input('agencyId');

		$query = HHACaregiversHelper::getCaregiverDocumentType($agencyId);
		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $query]);
	}
	function updateHHADocument(Request $request)
	{


		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'agencyId' => 'required',
			'document_type' => "required",
			'document_medical_type' => "required",
			'hha_medical_result' => 'required',
			'completed_date' => 'required',
			'record-id' => 'required'
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		}
		$dateCompleted = date('Y-m-d', strtotime(request('completed_date')));
		$agencyID = $request->input('agencyId');
		$getDetails = $this->DocumentPatientService->getDetailsById($request->input('id'));

		$record = $this->PatientService->getDetailByIdNew($getDetails->patient_id);

		//	$getCaregiverDetails = HHAAppointmentHelper::getDetailsByPatientIdMedicalId($record->id, $getDetails->hha_medical_doc_id);
		$caregiverMedicalDocID = request('document_medical_type');
		$caregiverId = '';
		if (isset($record->link_hha_caregiver) && $record->link_hha_caregiver != '') {
			$caregiverId = $record->link_hha_caregiver;
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyID)->where('id', $record->hha_id)->first();

			if (!$getCaregiverDetails) {

				return response()->json(['error_msg' => "Caregiver details not found in HHX", 'status' => 1, 'data' => array()], 400);
			} else {
				$caregiverId = $getCaregiverDetails->caregiver_id;
			}
		}


		$annualHelath = '';


		foreach ($caregiverMedicalDocID as $key => $medicalId) {
			if ($medicalId == '80093') {
				$annualHelath = $medicalId;
			}

			if (isset(request('hha_medical_result')[$medicalId])) {
				$Result = HHACaregiversHelper::getUpdateHHADocument($request->input('agencyId'), $caregiverId, $medicalId, request('hha_medical_result')[$medicalId], $dateCompleted,$getDetails->patient_id,$getDetails->id);
			}
		}

		if ($request->input('agencyId') == 106) {
			if ($request->hha_due_date != "") {
				sleep(3);
				foreach ($caregiverMedicalDocID as $key => $medicalId) {
					$annualHelath = $medicalId;
					$Result = HHACaregiversHelper::createNewMedicalForHamaspik($request->input('agencyId'), $caregiverId, $annualHelath, request('hha_medical_result')[$annualHelath], $dateCompleted, date('Y-m-d', strtotime($request->hha_due_date)));
				}
			}
		}
		if ($Result !== 1) {
			return response()->json(['error_msg' => $Result . ' ', 'status' => 0, 'data' => array()], 500);
		}
		$url = '';
		$extension = '';
		if (isset($getDetails->attachment)) {
			$explode = explode('.', $getDetails->attachment);
			$extension = $explode[1];
			$url = URL::to('/') . '/patientdocument/' . $getDetails->attachment;
		}
		$fileName = $getDetails->attachment;
		$fileName = str_replace("patientdocument/", "", $fileName);

		$image = "patientdocument/" . $fileName;

		$file = Storage::disk('s3')->get($image);




		if ($request->input('agencyId') != 106) {
			$query = HHACaregiversHelper::getSendHHADocument($request->input('agencyId'), $getDetails->document_name, $extension, $request->input('document_type'), $caregiverId, $file, $request->input('id'));
			$this->DocumentPatientService->update(array('uploaded_to_hha' => 1), array('id' => $request->input('id')));
		}




		return response()->json(['error_msg' => "Document successfully updated!!", 'status' => 1, 'data' => array(), 'agency' => $request->input('agencyId')], 201);


	}


	function updateHHAcomplienceDocument(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'agencyId' => 'required',
			'document_type' => "required",
			'document_medical_type' => "required",
			'hha_medical_result' => 'required',
			'completed_date' => 'required',
			'record-id' => 'required'
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		}
		$dateCompleted = date('Y-m-d', strtotime(request('completed_date')));
		$agencyID = $request->input('agencyId');
		$getDetails = $this->DocumentPatientService->getDetailsById($request->input('id'));

		$record = $this->PatientService->getDetailByIdNew($getDetails->patient_id);

		//	$getCaregiverDetails = HHAAppointmentHelper::getDetailsByPatientIdMedicalId($record->id, $getDetails->hha_medical_doc_id);
		$caregiverMedicalDocID = request('document_medical_type');
		$caregiverId = '';
		if ($record->link_hha_caregiver != '') {
			$caregiverId = $record->link_hha_caregiver;
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyID)->where('id', $record->hha_id)->first();

			if (!$getCaregiverDetails) {

				return response()->json(['error_msg' => "Caregiver details not found in HHX", 'status' => 1, 'data' => array()], 400);
			} else {
				$caregiverId = $getCaregiverDetails->caregiver_id;
			}
		}


		foreach ($caregiverMedicalDocID as $complianceId) {
			$Result = HHACaregiversHelper::createCaregiverOtherCompliance($request->input('agencyId'), $caregiverId, $complianceId, request('hha_medical_result')[$complianceId], $dateCompleted, $request->other_complience_due_date);
		}


		if ($Result !== 1) {
			return response()->json(['error_msg' => $Result . ' ', 'status' => 0, 'data' => array()], 500);
		}
		$url = '';
		$extension = '';
		if (isset($getDetails->attachment)) {
			$explode = explode('.', $getDetails->attachment);
			$extension = $explode[1];
			$url = URL::to('/') . '/patientdocument/' . $getDetails->attachment;
		}
		$fileName = $getDetails->attachment;
		$fileName = str_replace("patientdocument/", "", $fileName);

		$image = "patientdocument/" . $fileName;

		$file = Storage::disk('s3')->get($image);



		//$getCaregiverDetails = HHAAppointmentHelper::getDetailsByIdWithoutJoin($record->id);

		if ($request->input('agencyId') != 106) {

			$query = HHACaregiversHelper::getSendHHADocument($request->input('agencyId'), $getDetails->document_name, $extension, $request->input('document_type'), $caregiverId, $file, $request->input('id'));
		}
		//return response()->json(['error_msg' => "Please wait for 15 min. This.", 'status' => 0, 'data' => array()], 500);



		if ($query) {
			$this->DocumentPatientService->update(array('uploaded_complience_hha' => 1), array('id' => $request->input('id')));
			return response()->json(['error_msg' => "Document successfully updated", 'status' => 1, 'data' => array()], 201);
		}
		return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
	}
	function updateHHADocument2(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'agencyId' => 'required',
			'document_type' => "required",
			'hha_medical_result' => 'required',
			'completed_date' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		}
		$dateCompleted = date('Y-m-d', strtotime(request('completed_date')));
		$getDetails = $this->DocumentPatientService->getDetailsById($request->input('id'));

		$record = $this->PatientService->getDetailByIdNew($getDetails->patient_id);

		$getCaregiverDetails = HHAAppointmentHelper::getDetailsByPatientIdMedicalId($record->id, $getDetails->hha_medical_doc_id);
		$query = HHACaregiversHelper::getUpdateHHADocument($request->input('agencyId'), $getCaregiverDetails->caregiver_id, $getDetails->hha_medical_doc_id, request('hha_medical_result'), $dateCompleted);
		$getDetails = $this->DocumentPatientService->getDetailsById($request->input('id'));
		$url = '';
		$extension = '';
		if (isset($getDetails->attachment)) {
			$explode = explode('.', $getDetails->attachment);
			$extension = $explode[1];
			$url = URL::to('/') . '/patientdocument/' . $getDetails->attachment;
		}


		$record = $this->PatientService->getDetailByIdNew($getDetails->patient_id);

		$getCaregiverDetails = HHAAppointmentHelper::getDetailsByIdWithoutJoin($record->id);
		if ($request->input('agencyId') != 106) {
			$query = HHACaregiversHelper::getSendHHADocument($request->input('agencyId'), $getDetails->document_name, $extension, $request->input('document_type'), $getCaregiverDetails->caregiver_id, $url, $request->input('id'));
		}



		if ($query) {
			$this->DocumentPatientService->update(array('uploaded_to_hha' => 1), array('id' => $request->input('id')));
			return response()->json(['error_msg' => "Document successfully updated", 'status' => 1, 'data' => array()], 200);
		}
		return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
	}

	public function DocumentUploadByPatient(Request $request)
	{
		$user = auth()->user();

		$validator = Validator::make($request->all(), [
			'upload_document_id' => 'required',
			'images' => 'required',


		]);
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {
			$image = '';

			if ($request->file('images') != '') {
				$priceImage = $request->file('images');
				$name = uniqid() . time() . '.' . $priceImage->getClientOriginalExtension();
				$image = Storage::disk('s3')->putFileAs('patientdocument', $priceImage, $name);
			}

			if ($request->input('upload_document_id') != '') {
				$data = array(
					'attachment' => $image,
				);
				$insert = $this->DocumentPatientService->update($data, array('id' => $request->input('upload_document_id')));
				$getDocumentName = $this->DocumentPatientService->getDetailsById($request->input('upload_document_id'));
				$documentName = isset($getDocumentName->document_name) ? $getDocumentName->document_name : "";

				$this->sendEmailNotificaiton($request->input('id'), $image, "", $documentName, $getDocumentName->internal_use);
			}
			if ($insert) {

				Session::flash('success', 'Document  successfully uploaded.');
				return redirect()->back();
			} else {
				Session::flash('error', 'Sorry, something went wrong. Please try again.');
				return redirect()->back();
			}
		}
	}

	public function getCountyByZipCode(Request $request)
	{
		$zip_code = $request['zip_code'];
		$data['getName'] = ZipCode::where('zip_code', $zip_code)->first();
		if ($data['getName'] != '' && !empty($data['getName'])) {
			echo $data['getName']->county;
		} else {
			echo "County not found";
		}
	}

	public function patientAssign(Request $request)
	{
		$appoimentId = $request['appoiment_id'];
		$data = array('assign_user_id' => $request->input('assign_id'),'dept_id' => $request->assign_department);
		$this->commonAssignUser($request->input('appoiment_id'),$data,url('/patient/view/' . $appoimentId));
		Session::flash('success', 'Assign appoinment successfully.');
		return redirect()->back();
	}
	public function AppointmentSchedule(Request $request)
	{
		$timeId = "";
		$time = "";
		if ($request->caregiver_type == 'Caregiver') {
			$timeId = $request->input('time');
		} else {
			$time = $request->input('time');
		}
		$user = auth()->user();
		$date = Utility::convertYMD($request->input('date'));

		$inserArray = [
			'patient_id' => $request->id,
			'location_id' => $request->location_id,
			'appointment_date' => $date,
			'appointment_time' => $time,
			'location_time_id_slot' => $timeId,
			'status' => "Pending",
			'created_date' => date('Y-m-d H:i:s'),
			'created_by' => $user->id
		];
		ScheduleAppointment::create($inserArray);
		try {
			if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
				$getExistingRecord = $this->PatientService->getDetailByIdNew($request->id);
				$agencyNotifyData = array(
					'agencyid' => $getExistingRecord->agency_id,
					'title' => 'Schedule appoinment via Request for appointment',
					'record_id' => $request->id,
					'record_type' => 'Appointment',
					'msg' => '',
					'res_data' => serialize($request->all()),
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);
			}
		} catch (\Throwable $th) {}
		session::flash('success', 'Schedule appoinment successfully.');
		return redirect()->back();
	}
	public function approveStatus(Request $request)
	{
		$res = ScheduleAppointment::where('id', $request->id)->update(array('status' => 'Approve'));
		if ($res) {
			$status = 1;
		} else {
			$status = 0;
		}
		return response()->json(['error_msg' => 'Status approved successfully.', 'data' => array('status' => $status, 'id' => $request->id)], 200);
	}

	public function linkToCaregiver(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'agency_id' => 'required',
			'hha_profile_id' => 'required',

		]);
		if ($validator->fails()) {
		} else {
			$type = $request->dataTypeId;
			$getDetails = HHACaregiversHelper::getCaregiverDetailsByCaregiverId($request->hha_profile_id, $request->agency_id);
			if ($type == 'hha') {
				if(!isset($getDetails->id)){
					$saveData = HHACaregiversHelper::saveData($request->hha_profile_id, $request->agency_id);
				}
			}

			$getExistingData = $this->PatientService->getDetailById($request->patient_id);
			$data = array(
				'link_hha_caregiver' => $request->hha_profile_id
			);

			$update = $this->PatientService->update($data, array('id' => $request->patient_id));
			$ipaddress = Utility::getIP();

			$insertLog = [
				'type' => 'Link To HHX Caregiver',
				'link' => url('/patient/link-to-caregiver'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment',
				'new_response' => serialize($data),
				'old_response' => serialize($getExistingData->toArray()),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $getExistingData->agency_id,
						'title' => 'Linked To HHX Caregiver of appointment',
						'record_id' => $request->patient_id,
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => serialize($data),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			$response = [
				'message' => "HHX Caregiver successfully linked.",
				'status' => 1,
				'data' => $getDetails,
			];
			return response()->json($response, 200);
		}
	}

	public function mergeAppointment(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'record_id' => 'required',
			'appointment_id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
		} else {

			$checkAppointmentIdMergeOrnot = $this->appointmentMergeLogsService->checkAnyExistingMergeAppointmentId($request->record_id,$request->appointment_id);

			//$checkAppointmentIdMergeOrnot = $this->PatientService->checkAnyExistingMergeAppointmentId($request->appointment_id);
			if(isset($checkAppointmentIdMergeOrnot->id)){
				return response()->json(['error_msg' => "Chart ID is already merged with the same appointment ID", 'status' => 0, 'data' => array()], 500);
			}
			$getDetailsForAppointment = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->appointment_id);

			if($request->appointment_id == $request->record_id){
				return response()->json(['error_msg' => "Cannot merge a record with itself",'data' => array()], 500);
			}
			if (isset($getDetailsForAppointment->id)) {
				$mergeRecords = $this->PatientService->getDetailById($request->record_id);
				if ($getDetailsForAppointment->agency_id == $mergeRecords->agency_id) {
					if (strtolower($getDetailsForAppointment->type) == strtolower($mergeRecords->type)) {
						$oldResponse = $getDetailsForAppointment->toArray();

						/*****Merge Services */
						$explodeAppointmentService = explode(',', $getDetailsForAppointment->service_id);

						$explodeMergeServices = explode(',', $mergeRecords->service_id);

						$final = array_unique(array_merge($explodeMergeServices, $explodeAppointmentService));

						/***** Record Deleted After */
						$deletedExisting = $this->PatientService->SoftDelete(array('deleted_flag' => 'Y', 'merge_appointment_id' => $request->record_id), array('id' => $request->appointment_id));

						$deletedPatient = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->appointment_id);
						$oldRecordDeteletedPatient = $deletedPatient->toArray();

						$oldRecordPatient = array();
						if ($mergeRecords) {
							$oldRecordPatient = $mergeRecords->toArray();
						}

						$deleted = $this->PatientService->update(array('merge_appointment_id' => $request->appointment_id, 'service_id' => implode(',', $final)), array('id' => $request->record_id));

						$getServiceDetails = Master::geServiceName(implode(',',$final));

						$serviceArray = [];
						if(count($getServiceDetails) >0){
							foreach($getServiceDetails as $vas){
								$serviceArray[] = $vas->name;
							}
						}
						$ipaddress = Utility::getIP();
						$insertLog = [
							'type' => 'Merge Appointment',
							'link' => url('/patient/combine-appointment'),
							'module' => 'Patient Appointment',
							'object_id' => $request->record_id,
							'message' => $user->first_name . ' ' . $user->last_name . ' has merge Record',
							'old_response' => serialize($oldRecordPatient),
							'new_response' => serialize(array('merge_appointment_id' => $request->appointment_id)),
							'ip' => $ipaddress,
						];
						LogsService::save($insertLog);

						$insertLog = [
							'type' => 'Merge Appointment',
							'link' => url('/patient/combine-appointment'),
							'module' => 'Patient Appointment',
							'object_id' => $request->appointment_id,
							'message' => $user->first_name . ' ' . $user->last_name . ' has merge Record',
							'old_response' => serialize($oldResponse),
							'new_response' => serialize(array('merge_appointment_id' => $request->record_id)),
							'ip' => $ipaddress,
						];
						LogsService::save($insertLog);

						$this->appointmentMergeLogsService->save(array('main_patient_id'=>$request->record_id,'merge_patient_id'=>$request->appointment_id));
						return response()->json(['error_msg' => "Record successfully merged", 'data' => array('service_name'=>$serviceArray)], 200);
					} else {
						return response()->json(['error_msg' => "Chart type must be same", 'data' => array()], 500);
					}
				} else {
					return response()->json(['error_msg' => "Please select records from the same agency to merge", 'status' => 0, 'data' => array()], 500);
				}
			} else {
				return response()->json(['error_msg' => "Chart ID does not exists", 'data' => array()], 500);
			}
		}
	}
	// function mergeAppointment(Request $request)
	// {
	// 	$user = auth()->user();
	// 	$validator = Validator::make($request->all(), [
	// 		'record_id' => 'required',
	// 		'appointment_id' => 'required',


	// 	]);
	// 	if ($validator->fails()) {
	// 		return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
	// 	} else {
	// 		$getDetailsForAppointment = $this->PatientService->getDetailById($request->appointment_id);
	// 		if (isset($getDetailsForAppointment->id)) {
	// 			$deleted = $this->PatientService->update(array('merge_appointment_id' => $request->record_id), array('id' => $request->appointment_id));
	// 			$deleted = $this->PatientService->update(array('merge_appointment_id' => $request->appointment_id), array('id' => $request->record_id));

	// 			if ($deleted) {
	// 				$this->DocumentPatientService->update(array('patient_id' => $request->record_id), array('patient_id' => $request->appointment_id));
	// 			}

	// 			$ipaddress = request()->getClientIp();


	// 			$insertLog = [
	// 				'type' => 'Merge Appointment',
	// 				'link' => url('/patient/link-to-patient'),
	// 				'module' => 'Patient Appointment',
	// 				'object_id' => $request->record_id,
	// 				'message' => $user->first_name . ' ' . $user->last_name . ' has merge Record',

	// 				'ip' => $ipaddress,
	// 			];
	// 			LogsService::save($insertLog);
	// 		} else {
	// 			return response()->json(['error_msg' => "Invalid Appointment Id", 'status' => 0, 'data' => array()], 500);
	// 		}
	// 	}
	// }

	function inserviceAppointment(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'record_id' => 'required',
			'inservice_id' => 'required',


		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {

			$updateData = array(
				'status' => 'In Service',
				'inservice_datetime' => date('Y-m-d	H:i:s', strtotime($request->inservice_id)),
				'last_status_update' => date('Y-m-d	H:i:s'),
				'last_status_update_by' => auth()->user()->id
			);
			$update = $this->PatientService->update($updateData, array('id' => $request->record_id));
			$updateData['inservice_datetime'] = date('m/d/Y	h:i	A', strtotime($request->inservice_id));
			$this->saveServiceRequest($request->record_id, 'In Service');
			return response()->json(['error_msg' => "Status	successfully updated", 'data' => $updateData], 200);
		}
	}

	function updateHHADocumentPatient(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'agencyId' => 'required',
			'document_type' => "required"
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all(), 'status' => 0, 'data' => array("test" => "stset")], 400);
		}


		$agencyID = $request->input('agencyId');
		$getDetails = $this->DocumentPatientService->getDetailsById($request->input('id'));

		$record = $this->PatientService->getDetailByIdNew($getDetails->patient_id);

		$patientId = '';
		if ($record->link_hha_patient != '') {
			$patientId = $record->link_hha_patient;
		} else {

			$getPatientDetails = HHAPatient::where('agency_fk', $agencyID)->where('id', $record->hha_id)->first();

			if (!$getPatientDetails) {

				return response()->json(['error_msg' => "Patient details not found in HHX", 'status' => 1, 'data' => array()], 400);
			} else {
				$patientId = $getPatientDetails->patient_id;
			}
		}

		$url = '';
		$extension = '';
		if (isset($getDetails->attachment)) {
			$explode = explode('.', $getDetails->attachment);
			$extension = $explode[1];
			$url = URL::to('/') . '/patientdocument/' . $getDetails->attachment;
		}
		$fileName = $getDetails->attachment;
		$fileName = str_replace("patientdocument/", "", $fileName);

		$image = "patientdocument/" . $fileName;

		$file = Storage::disk('s3')->get($image);

		if ($request->input('agencyId') != 106) {
			$query = HHAPatientHelper::getSendHHADocument($request->input('agencyId'), $getDetails->document_name, $extension, $request->input('document_type'), $patientId, $file, $request->input('id'));
		}

		if (isset($query['status'])) {

			if ($query['status'] == 1) {
				$this->DocumentPatientService->update(array('uploaded_to_hha' => 1), array('id' => $request->input('id')));
				$ipaddress = Utility::getIP();
				$hhaLogData = [
					'patient_id'=>$record->id,
					'hha_patient_id'=>$patientId,
					'type'=>$record->type,
					'hha_module_type'=>'Medical',
					'send_response'=>serialize($request->except('_token')),
					'ip_address' => $ipaddress,
					'action'=>'Document Add',
				];
				$this->hhaLogService->save($hhaLogData);
				return response()->json(['error_msg' => "Document successfully updated", 'status' => 1, 'data' => array()], 201);
			} else {
				return response()->json(['error_msg' => $query['message'][0], 'status' => 0, 'data' => array()], 500);
			}
		} else {
			return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
		}
		if ($query) {
		}
	}

	public function smsLogs($id)
	{
		$ids = [$id];
		$smsLogs = SMSLogs::getlistWithIds($ids);
		return view('patient/sms-logs-ajax', ['data' => $smsLogs, 'record_id' => $id]);
	}

	public function alaycareEmpData(Request $request)
	{

		$query = AlayacareEmployee::AlayacareIdgetData($request->q, $request->agency_id);
		$data = [];
		foreach ($query as $val) {
			$temp = [];
			$temp['emp_id'] = $val->emp_id;
			$temp['name'] = $val->first_name . ' ' . $val->last_name;

			$data[] = $temp;
		}

		return json_encode($data);
	}

	public function updateAlaycareId(Request $request)
	{
		$query = Patient::find($request->patient_id);
		$name = NULL;
		$alyacare_id = NULL;
		if ($request->alyacare_id != "") {
			$name = $request->name;
			$alyacare_id = $request->alyacare_id;
		}
		$data = [
			'alaycare_id' => $alyacare_id,
			'alaycare_name' => $name,
		];

		$query->update($data);
		return response()->json(['error_msg' => 'Alaycare Succesfully Added', 'status' => 0, 'data' => array($query)], 200);
	}

	public function appointmentlogsView($id)
	{
		$data['logList'] = LogsService::getDatByLogsId($id);
		$old_response = unserialize($data['logList']->old_response);

		$old_responses = gettype($old_response);
		$data['logList']->old_response = $old_response;
		if ($old_responses == 'object') {
			$data['logList']->old_response = $old_response->toArray();
		}
		$new_response = unserialize($data['logList']->new_response);

		$new_responses = gettype($new_response);
		$data['logList']->new_response = $new_response;
		if ($new_responses == 'object') {
			$data['logList']->new_response = $new_response->toArray();
		}
		return view('patient/appointment-logs-details', $data);
	}

	function updateTraining(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'training_status' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('training_status' => $request->training_status), array('id' => $request->patient_id));
			return response()->json(['error_msg' => "Training Status successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	function updateTraningDueDate(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'traning_due_date' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('traning_due_date' => date('Y-m-d', strtotime($request->traning_due_date))), array('id' => $request->patient_id));
			return response()->json(['error_msg' => "Traning Due Date successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	function updateInservice(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'inservice_status' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('inservice_status' => $request->inservice_status), array('id' => $request->patient_id));
			return response()->json(['error_msg' => "Inservice successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	function updateEmergencyPhone(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'emergency_phone' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('emergency_phone' => $request->emergency_phone), array('id' => $request->patient_id));
			return response()->json(['error_msg' => "Emergency Phone successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	function updateEmail(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'email' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('email' => $request->email), array('id' => $request->patient_id));
			return response()->json(['error_msg' => "Email successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	public function getCommonAgencyWiseServiceList($serviceIds, $agencyId)
	{
		return $this->AgencyWiseServiceService->getServiceListUsingId($serviceIds, $agencyId);
	}

	public function patientFollowupDate(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'follow_date' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('follow_date' => date('Y-m-d', strtotime($request->follow_date))), array('id' => $request->id));
			return response()->json(['error_msg' => "Followup Date Successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	public function autoCompleteEmail(Request $request)
	{
		$term = $request->input('term');
		$query = User::whereRaw('CONCAT_WS(" ", first_name, last_name) LIKE ?', ["%{$term}%"])
    ->where('delete_flag', 'N');
		if ($request->type === 'Agency') {
			$query->where(function ($q) use ($request) {
				$q->whereNull('agency_fk')
				->orWhere('agency_fk', $request->agency_id)
				->orWhereExists(function ($sub) use ($request) {
					$sub->select('user_wise_agency.user_id')
						->from('user_wise_agency')
						->whereColumn('user_wise_agency.user_id', 'users.id')
						->where('user_wise_agency.agency_id', $request->agency_id)
						->where('user_wise_agency.delete_flag', 'N');
				});
			});
		} else {
			$query->whereNull('agency_fk');
		}
		$final = [];
		$notAllowedApiUser = [4316,4979];
		$users = $query->get();
		foreach ($users as $val) {
			if(!in_array($val->id,$notAllowedApiUser)){
				$final[] = [
					'id' => $val->id,
					'email' => $val->email,
					'full_name' => $val->first_name . ' ' . $val->last_name,
					'agency_fk'=>$val->agency_fk
				];
			}
		}
		return response()->json([
			'data' => $final,
		]);
	}

	function updateInserviceTwo(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'inservice_status' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('inservice_status_two' => $request->inservice_status), array('id' => $request->patient_id));
			return response()->json(['error_msg' => "Inservice successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	public function patientAvaibilityFollowupDate(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'availibility_followup_date' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('availability_followup_date' => date('Y-m-d', strtotime($request->availibility_followup_date))), array('id' => $request->id));
			return response()->json(['error_msg' => "Avaibility Followup Date Successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	public function linkToPatient(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'agency_id' => 'required',
			'hha_profile_id' => 'required',

		]);
		if ($validator->fails()) {
		} else {
			$type = $request->dataTypeId;
			$getHHAPatientDetails = $this->hhaPatientService->getDetailsByPatientID($request->hha_profile_id, $request->agency_id);

			if (!isset($getHHAPatientDetails->id)) {
				$saveData = HHAPatientHelper::saveData($request->hha_profile_id, $request->agency_id);
			}

			$getExistingData = $this->PatientService->getDetailById($request->patient_id);
			$data = array(
				'link_hha_patient' => $request->hha_profile_id
			);

			$update = $this->PatientService->update($data, array('id' => $request->patient_id));
			$ipaddress = Utility::getIP();


			$insertLog = [
				'type' => 'Link To HHX Patient',
				'link' => url('/patient/link-to-patient'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment',
				'new_response' => serialize($data),
				'old_response' => serialize($getExistingData->toArray()),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			$getDetails = HHAPatientHelper::getPatientDetails($request->hha_profile_id);
			$response = [
				'message' => "HHX Patient successfully linked.",
				'status' => 1,
				'data' => $getDetails,
			];
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $getExistingData->agency_id,
						'title' => 'Linked To HHX Patient of appointment',
						'record_id' => $request->patient_id,
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => serialize($data),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			return response()->json($response, 200);
		}
	}

	function saveThirdPartyLink(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'agency_id' => 'required',
			'hha_profile_id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$record = $this->PatientService->update(array('link_third_party' => $request->hha_profile_id), array('id' => $request->patient_id, 'agency_id' => $request->agency_id));
			$getDetails = $this->thirdPartyPatientMaster->getPatientDetails($request->hha_profile_id, $request->agency_id);
			return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => $getDetails], 201);
		}
	}
	public function savePatientCustomData(Request $request)
	{
		return $this->FormBuilderService->savePatientCustomData($request);
	}

	public function patientCustomDataSave(Request $request)
	{
		return $this->FormBuilderService->storePatientCustomData($request);
	}

	public function sendDocumentMail(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'document_id' => 'required',
			'patient_id' => 'required',
			'email' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$explode = explode(',', $request->email);
			$email = array_unique($explode);
			$emailArray = [implode(',', $email), auth()->user()->email];
			$finalArray = [
				'patient_id' => $request->patient_id,
				'document_id' => $request->document_id,
				'email' => json_encode($emailArray, true),
				'send_back_to_agency' => !empty($request->send_back_to_agency) && $request->send_back_to_agency == 1 ? 1 : 0,
			];

			$save = $this->userSendPatientDocumentLogService->save($finalArray);

			$getDetails = $this->DocumentPatientService->getDetailsById($request->document_id);
			if (isset($getDetails->patient_id)) {
				$getPatientDetails = $this->PatientService->getDetailByIdNew($getDetails->patient_id);
				if (isset($getPatientDetails->agency_id)) {
					$pdfContent = Storage::disk('s3')->get('patientdocument/' . $getDetails->attachment);
					$mime = Storage::disk('s3')->mimeType('patientdocument/' . $getDetails->attachment);


					try {
						$locationDetails = $this->LocationMasterService->getDetailbyId($getPatientDetails->location_id);

						$address1 = '';
						$address2 = '';
						$city = '';
						$state = '';
						$zip_code = '';

						if (isset($locationDetails->address1) && $locationDetails->address1 != '') {
							$address1 = $locationDetails->address1;
						}
						if (isset($locationDetails->address2) && $locationDetails->address1 != '') {
							$address2 = $locationDetails->address2;
						}
						if (isset($locationDetails->city) && $locationDetails->city != '') {
							$city = $locationDetails->city;
						}
						if (isset($locationDetails->state) && $locationDetails->state != '') {
							$state = $locationDetails->state;
						}
						if (isset($locationDetails->zip_code) && $locationDetails->zip_code != '') {
							$zip_code = $locationDetails->zip_code;
						}
						$localdetails = $address1 . ' ' . $address2 . ' ' . $city . ' ' . $state . ' ' . $zip_code;
						$getPatientDetails->location = $localdetails;
						$getPatientDetails->doc_name = $getDetails->document_name;
						$data['details'] = $getPatientDetails;

						$subject = '#' . $getPatientDetails->id . ' Document Uploaded';
						// return view('email_template.document_upload_notificaiton_email',$data);
						// die();
						$dataMessage = array('to' => $email, 'bcc' => auth()->user()->email, 'pdfContent' => $pdfContent, 'subject' => $subject,'file_name'=>$getDetails->attachment,'mime'=>$mime);
						$sendStatus = Mail::send('email_template.document_upload_notificaiton_email', $data, function ($message) use ($dataMessage) {
							$message->subject($dataMessage['subject']);
							$message->to($dataMessage['to']);
							$message->bcc($dataMessage['bcc']);
							$message->attachData($dataMessage['pdfContent'], $dataMessage['file_name'], [
								'mime' => $dataMessage['mime'],
							]);
						});

					} catch (\Throwable $th) {
						//throw $th;
					}


					// unlink($newulr);
					$ipaddress = Utility::getIP();
					$user = auth()->user();
					$insertLog = [
						'type' => 'Document Sent Mail',
						'link' => url('patient/send-document-mail'),
						'module' => 'Patient Appointment',
						'object_id' => $getPatientDetails->id,
						'message' => 'Mail has been send successfully by ' . $user->first_name . ' ' . $user->last_name,
						'new_response' => serialize($getPatientDetails),
						'old_response' => serialize($getPatientDetails),
						'ip' => $ipaddress,
					];
					LogsService::save($insertLog);
				}
			}
			return response()->json(['error_msg' => "Mail successfully send", 'status' => 1, 'data' => array()], 201);
		}
	}

	public function markSendBackToAgency(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'document_id' => 'required|integer',
			'patient_id'  => 'required|integer',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 0, 'message' => $validator->errors()->all()[0]], 400);
		}

		$finalArray = [
			'patient_id'          => $request->patient_id,
			'document_id'         => $request->document_id,
			//'email'               => json_encode([auth()->user()->email]),
			'send_back_to_agency' => 1,
			'note'                => $request->input('note', ''),
		];

		$save = $this->userSendPatientDocumentLogService->save($finalArray);
		if($save){
		$ipaddress = Utility::getIP();
		$user = auth()->user();
		LogsService::save([
			'type'         => 'Mark Send Back to Agency',
			'link'         => url('patient/mark-send-back-to-agency'),
			'module'       => 'Patient Appointment',
			'object_id'    => $request->patient_id,
			'message'      => $user->first_name . ' ' . $user->last_name . ' marked document #' . $request->document_id . ' as Send Back to Agency',
			'ip'           => $ipaddress,
			'new_response' => serialize($finalArray),
		]);

		return response()->json(['status' => 1, 'message' => 'Marked as Send Back to Agency successfully'], 200);
		} else {
			return response()->json(['status' => 0, 'message' => 'Failed to mark as Send Back to Agency'], 500);
		}
	}

	public function HHAPatientCaregiverDetails(Request $request)
	{

		if ($request->type == 'Caregiver') {

			$response = HHACaregiversHelper::searchCaregiverForHHA($request->agency_id, $request->patient_code);
			$final = [];
			if (!empty($response[0])) {
				foreach ($response as $val) {

					$val['dob'] = date('m/d/Y',strtotime($val['dob']));
					$final[] =$val;
				}
			}
			$response = $final;
		}
		if ($request->type == 'Patient') {
			$response = HHAPatientHelper::searchPatientForHHA($request->agency_id, $request->patient_code, 'auto');
			$final = [];
			if (!empty($response[0])) {
				foreach ($response as $val) {
					$temp = [];

					$val['first_name'] = $val['firstName'];
					$val['middle_name'] = $val['middleName'];
					$val['last_name'] = $val['lastName'];
					$val['mobile_or_sms'] = $val['home_phone'];
					$val['phone'] = $val['phone2'];
					$val['State'] = $val['state'];
					$val['Zip5'] = $val['zip5'];
					$val['ssn'] = $val['ssn'];
					$val['discipline'] = $val['discipline'];
					$val['address1'] = $val['address1'];
					$val['address2'] = $val['address2'] . ' ' . $val['cross_street'];
					$val['emergencyName'] = $val['emergencyContactName'];
					$val['emergencyPhone1'] = str_replace('-', '', $val['emergencyContactPhone1']);
					$val['medicaid_number'] = $val['medicaid_number'];
					$temp = $val;
					$final[] = $val;
				}
			}

			$response = $final;
		}


		return response()->json(['status' => true, 'data' => $response]);
	}
	function ajaxDocumentList(Request $request)
	{
		$id = $request->id;
		$data['user'] = auth()->user();
		$data['record'] = $record = $this->PatientService->getDetailByIdNew($id);
		$ids = [$id];

		if (isset($record->merge_appointment_id) && $record->merge_appointment_id != "") {
			$ids = $this->convertMergePatientArray($record->merge_appointment_id,$id);
		}
		$data['agencyDetails'] = Agency::getDetailsByAgencyId($record->agency_id);
		$order = 'asc';
		if(strtolower($record->type) == 'patient'){
			$order = 'desc';
		}
		if (Common::checkAgencyLogin()) {
			$document_list = $this->DocumentPatientService->getAllDocumentByPatientIdsAgency($ids,$order);
		} else {
			$document_list = $this->DocumentPatientService->getAllDocumentByPatientId($ids,$order);
		}

		$data['appointmentPermission'] = DateWiseAgencyAccessHelper::getDateWiseAgencyAccess();
		// Get overall counts (not pagination-wise)
		$counts = DocumentPatient::whereIn('patient_id', $ids)
				->where('deleted_flag', 'N')
				->selectRaw("
					COUNT(CASE WHEN medication_list = 1 THEN 1 END) AS medication_list_count,
					COUNT(CASE WHEN insurance_elg = 1 THEN 1 END) AS insurance_elg_count,
					COUNT(CASE WHEN mdo_tag = 1 THEN 1 END) AS mdo_count
				")
				->first();
		$mdoSource = Master::getMdoSourceList()->toArray();
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

				$userIds = explode(',', $val->assign_document_review);
				$getAssignUserDetails = UserHelper::getDetailsByUserids($userIds);
				$assignUserName = [];
				foreach ($getAssignUserDetails as $user) {
					if (isset($user->id)) {
						$assignUserName[] = $user->first_name . ' ' . $user->last_name;
					}
				}
				$val->assignUserReviewDocument = implode(',', $assignUserName);

				$val->mdo_source_name = array_key_exists($val->mdo_source,$mdoSource) ? $mdoSource[$val->mdo_source] : '';
			}
		}
		$data['document_list'] = $document_list;

		// Build a map of document_id => latest send-mail log (with sender name)
		$docIds = $document_list->pluck('id')->toArray();
		$sendMailLogs = $this->userSendPatientDocumentLogService->getLatestSendBackByDocIDs($docIds);
		$data['sendMailLogs'] = $sendMailLogs;

		$data['thirdPartyApiList'] = [];
		$data['medication_list_count'] = $counts->medication_list_count??0;
		$data['insurance_elg_count'] = $counts->insurance_elg_count??0;
		$data['mdo_count'] = $counts->mdo_count??0;
		if (isset($record->link_third_party) && $record->link_third_party != "") {
			$data['thirdPartyApiList'] = $this->agencyWiseThirdPartyAPIService->getAPIListByAgencyID($record->agency_id);
		}
		return view('patient/_partial/document_ajax_list', $data);
	}

	public function updateDocumentService(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'document_id' => 'required',
			'patient_id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$oldDocumentDetails = $this->DocumentPatientService->getDocumentDetailsById($request->document_id, $request->patient_id);
			$data = ['request_service_id' => $request->edits_request_service_id];
			if (isset($request->edit_doc_name)) {
				$data['document_name'] = $request->edit_doc_name;
			}

			$documentCompletedData = NULL;
			if (isset($request->edit_document_completed_date) && $request->edit_document_completed_date != "") {
				$documentCompletedData = date('Y-m-d', strtotime($request->edit_document_completed_date));
			}
			$data['document_completed_date'] = $documentCompletedData;


			if (isset($request->updated_module_flag)) {
			} else {
				$internal_use = !empty($request->edit_internal_use) && $request->edit_internal_use == 1 ? 1 : 0;
				$data['internal_use'] = $internal_use;
				$medication_list = !empty($request->edit_medication_list) && $request->edit_medication_list == 1 ? 1 : 0;
				$data['medication_list'] = $medication_list;
				$insurance_elg = !empty($request->edit_insurance_elg) && $request->edit_insurance_elg == 1 ? 1 : 0;
				$data['insurance_elg'] = $insurance_elg;
				$mdo_tag = !empty($request->edit_mdo_tag) && $request->edit_mdo_tag == 1 ? 1 : 0;
				$data['mdo_source'] = $mdo_tag == 1 ? $request->edit_mdo_source : NULL;
				$data['mdo_tag'] = $mdo_tag;
				$assignDocumentUser = NULL;
				if ($request->edit_document_review == 1) {
					$assignDocumentUser = $request->edit_document_approval_user_id;
				}
				$data['assign_document_review'] = $assignDocumentUser;


				$document_review_status  = $oldDocumentDetails->document_review_status;
				if ($internal_use == 0 && $request->edit_document_review != 1) {
					$document_review_status = "Approved";
				} else {
					if ($request->edit_document_review == 1) {
						if ($request->edit_document_approval_user_id != $oldDocumentDetails->assign_document_review) {
							$document_review_status = "Pending";
						}
					} else {
						if ($internal_use == 0) {
							$document_review_status = $oldDocumentDetails->document_review_status;
						} else {
							$document_review_status = $oldDocumentDetails->document_review_status;
						}
					}
				}

				$data['document_review_status'] = $document_review_status;
				DocumentHelper::updatePatientDocumentCounts($request->patient_id,$request->edit_medication_list,$request->edit_insurance_elg, $oldDocumentDetails->medication_list, $oldDocumentDetails->insurance_elg, $request->edit_mdo_tag, $oldDocumentDetails->mdo_tag);
			}


			$save = $this->DocumentPatientService->update($data, array('id' => $request->document_id));
			$newDocumentDetails = $this->DocumentPatientService->getDocumentDetailsById($request->document_id, $request->patient_id);
			if (isset($request->edit_document_service_id[0]) && !empty($request->edit_document_service_id[0])) {
				$this->documentUploadService->SoftDelete(array('del_flag' => 'Y'), array('document_id' => $request->document_id,'patient_id' => $request->patient_id));
				foreach ($request->edit_document_service_id as $serviceId) {
					$data = [
						'patient_id' => $request->patient_id,
						'document_id' => $request->document_id,
						'service_id' => $serviceId,
					];

					$save = $this->documentUploadService->save($data);
				}
			}else{
				if(strtolower($request->type) == 'patient'){
					$this->documentUploadService->SoftDelete(array('del_flag' => 'Y'), array('document_id' => $request->document_id,'patient_id' => $request->patient_id));
				}
			}

			if ($save) {
				if (isset($oldDocumentDetails->internal_use)) {
					if (isset($request->document_report_flag) && $request->document_report_flag == "Yes") {
					} else {
						if ($oldDocumentDetails->internal_use != $internal_use) {
							$documentApprovalUserIds = [];
							if ($internal_use == 0) {
								if ($request->edit_document_review == 1) {
									if ($request->edit_document_approval_user_id !=  $oldDocumentDetails->assign_document_review) {
										$documentApprovalUserIds = explode(',', $request->edit_document_approval_user_id);
									}
								}
								$this->sendEmailNotificaiton($request->patient_id, $newDocumentDetails->attachment, "", $newDocumentDetails->document_name, $internal_use, $documentApprovalUserIds);
							}
						}
					}
				}
				$this->multiplePatientDocApprovalService->SoftDelete(array('del_flag' => 'Y'), array('document_id' => $request->document_id, 'patient_id' => $request->patient_id));
				if ($request->edit_document_review == 1) {
					$approvalUsers = explode(',', $request->edit_document_approval_user_id);
					foreach ($approvalUsers as $apusers) {
						$udata = array(
							'user_id' => $apusers,
							'patient_id' => $request->patient_id,
							'document_id' => $request->document_id
						);
						$this->multiplePatientDocApprovalService->save($udata);
					}
				}

				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Update Document From Appointment',
					'link' => url('/patient/view/') . '/' . $request->patient_id,
					'module' => 'Patient Appointment',
					'object_id' => $request->patient_id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has Add Document From Appointment',
					'old_response' => serialize($oldDocumentDetails),
					'new_response' => serialize($newDocumentDetails),

					'ip' => $ipaddress,
				];

				LogsService::save($insertLog);

				$insertLog = [
					'type' => 'Edit Document',
					'link' => url('/update-document-service'),
					'module' => 'Document Section',
					'module_id' => $request->document_id,
					'new_response' => serialize($newDocumentDetails),
					'old_response' => serialize($oldDocumentDetails),
					'is_status' => 'Edit Document'
				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

				return response()->json(['error_msg' => "Document service successfully updated", 'status' => 1, 'data' => array()], 200);
			} else {

				return response()->json(['error_msg' => "Sorry, something went wrong. please try again", 'status' => 1, 'data' => array()], 500);
			}
		}
	}


	public function viewNewDesign($id)
	{
		$data['menu'] = "Patient Details";
		$data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}


		$record = $this->PatientService->getDetailByIdNew($id);



		if (isset($_GET['testigd']) && $_GET['testigd'] == 1) {
			echo "<pre>";
			print_r($record);
			die();
		}

		$agencyids = Utility::getUserWiseAgency();
		$agencyids[] = $auth->agency_fk;

		if (!empty($agencyids[0])) {
			if (in_array($record->agency_id, $agencyids)) {
			} else {
				abort(404);
			}
		}

		if (isset($record->id) && $record->id != '') {

			$data['agencyWiseField'] = $this->FormBuilderService->getAgencyWiseField($record->agency_id);

			$data['agencyWiseFieldWithoutFormId'] = $this->FormBuilderService->getAgencyWiseFieldWithoutFormId($record->agency_id);


			$data['agencyAllFormList'] = $this->FormBuilderService->getAgencyAllForm($record->agency_id);

			$patientSubmitData = $this->FormBuilderService->getSubmitDataByAgencyIdPatientID($record->agency_id, $id);
			$patientSubmitDataGroupByFormId = [];
			foreach ($patientSubmitData as $patientSubmit) {
				$patientSubmitDataGroupByFormId[$patientSubmit->form_id][$patientSubmit->agency_id][$patientSubmit->patient_id][$patientSubmit->field_id] = $patientSubmit->value;
			}
			$data['patientSubmitData'] = $patientSubmitDataGroupByFormId;

			$patientAdvanceSubmitData = $this->FormBuilderService->getAdvanceSubmitDataByAgencyIdPatientID($record->agency_id, $id);

			$patientAdvanceSubmitDataGroupByFormId = [];
			foreach ($patientAdvanceSubmitData as $patientAdvanceSubmit) {
				$patientAdvanceSubmitDataGroupByFormId[$patientAdvanceSubmit->field_id] = $patientAdvanceSubmit->value;
			}
			$data['patientAdvanceSubmitData'] = $patientAdvanceSubmitDataGroupByFormId;

			$data['doctorList'] = $this->FormBuilderService->getAllDoctor();

			$data['formList'] = $this->FormBuilderService->getAgencyForm($record->agency_id, $record->id);

			if ($record->record_read == 0) {
				$this->PatientService->update(array('record_read' => '1'), array('id' => $record->id));
			}
			$alayaCareDetails = $this->AlayacareService->getAllDetailsByAlayacreId($record->alaycare_id);

			$fullAlayaCareName = '';
			if (isset($alayaCareDetails->id)) {
				$fullAlayaCareName = $alayaCareDetails->first_name . ' ' . $alayaCareDetails->last_name;
			}
			$record->alaycare_name = $fullAlayaCareName;


			$remoteDetails = $this->robortService->getDetailsById($record->robort_id);

			$fullRemoteName = '';
			$externalId = '';
			if (isset($remoteDetails->id)) {
				$fullRemoteName = $remoteDetails->firstName . ' ' . $remoteDetails->lastName;
				$externalId = ($remoteDetails->externalId != "") ? '(' . $remoteDetails->externalId . ')' : "";
			}
			$record->remote_name = $fullRemoteName . $externalId;
			$record->externalId = $externalId;

			$fullHHXCaregiverName = '';

			if ($record->type == 'Caregiver') {
				if ($record->id == 45927) {
					echo $record->link_hha_caregiver . ' ' . $record->agency_id;
				}
				$getHHXCaregiverDetails = HHACaregiversHelper::getCaregiverDetailsByAgencyId($record->link_hha_caregiver, $record->agency_id);


				if (!$getHHXCaregiverDetails) {
					$getHHXCaregiverDetails = HHACaregiversHelper::getCaregiverDetails($record->link_hha_caregiver);
				}


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
			$data['hhaStatusList'] = $this->hhaCaregiverMedicalService->getStatusList();
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



			if ($record->created_by != '') {
				$getUserDetails = User::getDetailsById($record->created_by);
			}

			$fname = '';
			$lname = '';
			$userTypes = "Nybest User";
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
			if ($localdetails != '') {
				$record->location = $localdetails;
			}
			$data['masterData'] = Master::getAllDataByMasterTypeFk(array(17));
			$data['masterData'] = $masterData = Master::getAllDataByMasterTypeFk(array(12, 17, 25, 26));
			$servie = '';
			if (isset($record->service_id) && $record->service_id != '') {
				$explode = explode(',', $record->service_id);
				$finalArray = [];
				foreach ($explode as $val) {
					if ($val != "") {
						$finalArray[] = $val;
					}
				}
				// $getAgencyWiseServiceList = $this->getCommonAgencywiseServiceList($finalArray,$record->agency_id);
				// if(!empty($getAgencyWiseServiceList[0])){
				// 	$services = $getAgencyWiseServiceList;
				// }else{
				// 	$services = Master::whereRaw('id IN (' . implode(',', $finalArray) . ')')->where('del_flag', 'N')->get();
				// }

				$services = Master::whereRaw('id IN (' . implode(',', $finalArray) . ')')->where('del_flag', 'N')->get();

				$newass = array();
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

			$data['auth'] = auth()->user();
			$data['nyBestUserList'] = User::getNyBestUsersList();
			$data['locations'] = $this->LocationMasterService->AllList();
			$data['pastAppointment'] = $pastAppointment = Appointment::getPastAppointmentListNew($id);

			$servie = [];
			if (count($pastAppointment) > 0) {
				foreach ($pastAppointment as $record) {
					if (isset($record->service_id) && $record->service_id != '') {
						$servicesIds = explode(',', $record->service_id);


						$services = Master::whereIn('id', $servicesIds)->where('del_flag', 'N')->get();
						$newass = array();
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
			$data['serviceList'] = Master::getServiceRequestWithDisabled();

			$third_party_details = $this->thirdPartyPatientMaster->getPatientDetails($thirdPartyid, $data['record']->agency_id);

			$data['record']->link_third_party = $thirdPartyid;
			$name = "";
			if (isset($third_party_details->first_name)) {
				$name = $third_party_details->first_name . ' ' . $third_party_details->last_name;
			}
			$data['record']->link_third_party_name = $name;

			$insuranceDetails = $this->insuranceMasterService->getDetailById($record->insurance_name);
			$insuranceName = "";
			if (isset($insuranceDetails->id)) {
				$insuranceName = $insuranceDetails->insurance_name;
			}

			if ($record->insurance_name == 'other') {
				$insuranceName = 'Other';
			}
			$data['record']->insuranceName = $insuranceName;

			$disable_date = $this->disableDateService->disableDateAllData($data['record']->type)->toArray();
			$dateArray = explode(', ', implode(', ', $disable_date));
			$dateDetailArray = [];
			if (!empty($dateArray[0])) {
				foreach ($dateArray as $val) {
					$dateDetailArray[] = date('d-m-Y', strtotime($val));
				}
			}
			$data['disable_date'] = json_encode($dateDetailArray);

			$data['language_list'] = Cache::get('language_list', function () use ($auth) {
				return Language::getLanguageList();
			}, 10 * 60);
			return view('patient/patient_view_hospital_new', $data);
		} else {
			abort(404);
		}
	}

	public function getAgencies(Request $request)
	{
		$getPatientDetails = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->id);
		$data = [];
		$data['first_name'] = $getPatientDetails->first_name;
		$data['last_name'] = $getPatientDetails->last_name;
		$data['dob'] = $getPatientDetails->dob;
		$data['gender'] = $getPatientDetails->gender;
		$response = $this->PatientService->getPatientWiseAgencyList($data);
		return response()->json(['success' => true, 'data' => $response]);
	}

	public function agencyAllFormTableList(Request $request)
	{
		$response = $this->FormBuilderService->getAgencyAllFormTableList($request->agency_id, $request->patient_id, $request->status);
		return response()->json(['status' => true, 'data' => $response]);
	}

	public function agencyAllFormTableView(Request $request, $id)
	{
		$type = $request->query('type');
		$data['status'] = $request->query('status');
		$data['menu'] = "Agency All Form  Details";
		$data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}

		$requestData = $this->FormBuilderService->agencyAllFormData($id);

		$agencyFormId = $id;

		$patientSubmitData = $this->FormBuilderService->getSubmitDataByAgencyIdPatientID($requestData->agency_id, $agencyFormId, $requestData->patient_id);

		$patientSubmitDataGroupByFormId = [];
		foreach ($patientSubmitData as $patientSubmit) {
			$patientSubmitDataGroupByFormId[$patientSubmit->form_id][$patientSubmit->agency_id][$patientSubmit->patient_id][$patientSubmit->field_id] = $patientSubmit->value;
		}

		$data['patientSubmitData'][$agencyFormId] = $patientSubmitDataGroupByFormId;

		$recorddata = $this->FormBuilderService->getByPatientDetails($requestData->patient_id);

		$data['record'] = $recorddata;
		$data['agency_all_form_data'] = $requestData;
		$data['doctorList'] = $this->FormBuilderService->getAllDoctor();

		if ($data) {
			if ($type) {
				return view('form_report/form_report_view', $data);
			} else {
				return view('patient/_partial/patient/agency_all_form_table_view', $data);
			}
		} else {
			abort(404);
		}
	}

	public function esignMoveDocumentStoreOld(Request $request)
	{
		$user = auth()->user();

		$validator = Validator::make($request->all(), [
			// 'request_service_id' => 'required',
			// 'document_service_id' => 'required',
			'template_id' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$writeDocumentData = $this->DocumentPatientService->getWriteDocData($request->input('esign_doc_id'));
			$template = $this->DocumentPatientService->getTemplateData($request->input('template_id'));

			if ($request->template_id == 0) {
				$documentName = $writeDocumentData->document_name ?? '';
			} else {
				$documentName = $template->template_name;
			}
			$documentReport = $this->DocumentPatientService->getDocumentSentReportData($request->input('group_id'));

			$pdfname = "";

			if ($request->template_id == 0) {

				if (!empty($writeDocumentData) && isset($writeDocumentData->file_upload) && $writeDocumentData->file_upload != "") {
					$pdfGenerate = public_path() . '/patientWriteDocument/' . $writeDocumentData->file_upload;

					if (file_exists($pdfGenerate)) {

						$moves = file_get_contents($pdfGenerate);
						$name = file_put_contents(public_path('patientdocument/' . $writeDocumentData->file_upload), $moves);

						$pdfname = $writeDocumentData->file_upload;
					} else {

						/*****Code S3 Bucket */
						$inputPath = Storage::disk('s3')->get('/patientWriteDocument/' . $writeDocumentData->file_upload);

						$test = Storage::disk('s3')->put('patientdocument/' . $writeDocumentData->file_upload, $inputPath);

						$pdfname = $writeDocumentData->file_upload;
					}
				}
			} else {
				if (!empty($documentReport) && isset($documentReport->pdf_generate) && $documentReport->pdf_generate != "") {
					$pdfGenerate = public_path() . '/dosusinguploads/docusign/' . $documentReport->pdf_generate;

					if (file_exists($pdfGenerate)) {

						$moves = file_get_contents($pdfGenerate);
						$name = file_put_contents(public_path('patientdocument/' . $documentReport->pdf_generate), $moves);

						$pdfname = $documentReport->pdf_generate;
					} else {
						/*****Code S3 Bucket */
						$inputPath = Storage::disk('s3')->get('/dosusinguploads/docusign/' . $documentReport->pdf_generate);

						$test = Storage::disk('s3')->put('patientdocument/' . $documentReport->pdf_generate, $inputPath);

						$pdfname = $documentReport->pdf_generate;
					}
				}
			}


			$request_service = '';
			if (!empty($request->request_service_id[0])) {
				$request_service = implode(", ", $request->request_service_id);
			}
			$data = array(
				'document_name' => $documentName,
				'attachment' => $pdfname,
				'patient_id' => $request->input('id'),
				'request_service_id' => $request_service,
				'templete_id' => $request->input('template_id'),
				'agency_form_id' => $request->input('agency_form_id'),
				'document_review_status' => "Approved",
			);

			$insert = $this->DocumentPatientService->save($data);

			if ($insert) {

				if (!empty($request->document_service_id[0])) {
					foreach ($request->document_service_id as $serviceId) {
						$data = [
							'patient_id' => $request->input('id'),
							'document_id' => $insert,
							'service_id' => $serviceId,
						];

						$this->documentUploadService->save($data);
					}
				}

				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add Document From Appointment',
					'link' => url('/patient/view/') . '/' . $request->input('id'),
					'module' => 'Patient Appointment',
					'object_id' => $request->input('id'),
					'message' => $user->first_name . ' ' . $user->last_name . ' has Add Document From Appointment',
					'new_response' => serialize($data),

					'ip' => $ipaddress,
				];

				LogsService::save($insertLog);


				$newResponse['move_to_esign_by_name'] = $user->full_name;
				$newResponse['move_to_esign_date'] = date('Y-m-d H:i:s');
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Move To Document',
					'link' => url('/esign-move-document'),
					'module' => 'Esign Section',
					'module_id' => $request->group_id,
					'new_response' => serialize($newResponse),
					'old_response' => '',
					'is_status' => 'Move To Document'
				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

				// Get Group wise notification
				$serviceData = $request->document_service_id ?? [];
				$documentReportData = $this->DocumentPatientService->getAllDocumentSentReportData($request->input('group_id'));
				$patientId = $request->input('id') ?? '';
				$getPatientDetails = $this->PatientService->getPatientDetailsByIdWhitoutAgency($patientId);
				$agencyFk = $getPatientDetails->agency_id ?? '';
				$userType = $getPatientDetails->type ?? '';

				$signerAction = 'Move To Document Done';
				$title = 'Esign ' . '(' . $signerAction . ')';
				$msg = '<br><b>Document Name : </b>' . ($documentName ?? '') . ' (' . date('m/d/Y h:i A', strtotime($documentReportData->created_date)) . ')';
				$patientId = $request->input('id') ?? '';
				$this->notificationSend('Esign', $title, $msg, $patientId, $agencyFk, $userType, $userData = [], $serviceData);
				// Get Group wise notification
				return response()->json(['status' => true, 'error_msg' => 'Document  successfully uploaded'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	function saveServiceRequest($id, $status)
	{
		$auth = auth()->user();
		$checkServices = $this->patientServicesRequest->getPatientService($id);

		if (count($checkServices) > 0) {
			$getExistingRecord = $this->PatientService->getPatientDetailsByIdWhitoutAgency($id);
			$getLastServiceId = $this->patientServicesRequest->lastServiceRequestedByPatientId($id);

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
			if ($status == 'cancel') {
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
if ($status == 'Inactive') {
				$statusName = "inactive";
			}
			$updateStatus = array(
				'status' => $statusName,
				'completed_date' => $completedDate,
				'completed_by' => $completed_by,
				'last_status_update_by' => $auth->id??'',
				'last_status_update' => date('Y-m-d H:i:s')
			);
			if ($status == 'refused') {
				$updateStatus['reason_id'] = $getExistingRecord->reason_id;
			}
			$this->patientServicesRequest->update($updateStatus, array('id' => $getLastServiceId->id));

			if ($status == 'complete') {
				$agencyData = Agency::getDetailsByAgencyId($getExistingRecord->agency_id);
				$link = '';
				if (isset($agencyData->is_portal_sms) && $agencyData->is_portal_sms == 1) {
					$getAssignFlags = $this->agencyWiseSMSNotificationService->getDetailsIdWithTypeAndFlag($getExistingRecord->agency_id, $getExistingRecord->type, 'Feed Back Review');
					if (isset($getAssignFlags->id)) {
						if (isset($getLastServiceId->id)) {
							$link = URL::to('/review-feedback-form/') . '/' . sha1($getLastServiceId->id);

							$smsMessage = "";
							if (isset($getExistingRecord->mobile) && $getExistingRecord->mobile != "") {
								$smsMessage = "Dear,<br>";
								$smsMessage .= 'we’re happy to inform you that your service is complete. Help us improve by sharing your feedback.<br/>';
								$smsMessage .= '<a href="' . $link . '" style="text-decoration: none;">Click Here to Provide Feedback</a>';
							}

							$smsMobileArray = [];
							$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $getExistingRecord->mobile);
							if ($getExistingRecord->phone != "") {
								$smsMobileArray[] = str_replace(["(", ")", '-', " "], "", $getExistingRecord->phone);
							}

							if (count($smsMobileArray) > 0) {
								foreach ($smsMobileArray as $smb) {
									if ($smb != "") {
										$this->SmsService->AgencyWiseSmsDynamic($id, $smb, $smsMessage);
									}
								}
							}
						}
					}
				}

				if ($getExistingRecord->platform_type == 'dssdsd') {
					if ($getExistingRecord->link_third_party != "") {
						$getServices = $this->patientWiseServicesRequests->getPatientServices($getLastServiceId->id);
						$servicesDetails = '';
						if (count($getServices) > 0) {
							$serviceArrays = [];
							foreach ($getServices as $vals) {
								if (isset($vals->services->id)) {
									$serviceArrays[] = $vals->services->name;
								}
							}
							$servicesDetails = implode(',', $serviceArrays);
						}
						$data = ['id' => $getExistingRecord->link_third_party, 'first_name' => $getExistingRecord->first_name, 'last_name' => $getExistingRecord->last_name, 'patient_code' => $getExistingRecord->patient_code, 'type' => $getExistingRecord->type, 'dob' => $getExistingRecord->dob, 'gender' => $getExistingRecord->gender, 'phone' => $getExistingRecord->phone, 'service' => $servicesDetails, 'status' => $statusName];
						$webhook = ['status' => 'Appointment Status', "data" => $data];

						$response = ThirdPartyWebHookHelper::sendWebHook($webhook);
						$json = json_decode($response, true);
						if (isset($json['status']) && $json['status'] == 'success') {
							$createdBy = null;
							if (isset($auth->id)) {
								$createdBy = $auth->id;
							}
							$saveResponse = [
								'third_party_id' => $getExistingRecord->link_third_party,
								'send_response' => serialize($data),
								'return_response' => serialize(array($json['data'])),
								'created_date' => date('Y-m-d H:i:s'),
								'created_by' => $createdBy
							];

							$saveData = new ThirdPartyWebhookLog($saveResponse);
							$saveData->save();
						}
					}
				}
			}
		} else {
			$checkForDocument = $this->DocumentPatientService->getDetailsByPatientId(array($id));
			$getPatientServices = $this->PatientService->getPatientId($id);
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
		}
	}

	public function updatePatientLanguage(Request $request)
	{

		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'language_id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$old_response = $this->PatientService->getDetailById($request->patient_id);
			$record = $this->PatientService->update(array('language' => $request->language_id), array('id' => $request->patient_id));
			$new_response = $this->PatientService->getDetailById($request->patient_id);
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Update Language',
				'link' => url('/patient/view') . '/' . $request->input('id'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update language',
				'old_response' => serialize($old_response->toArray()),
				'new_response' => serialize($new_response->toArray()),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $new_response->agency_id,
						'title' => 'Updated Language of Appointment',
						'record_id' => $request->patient_id,
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => serialize(array('unarchived_at' => date('Y-m-d H:i:s'))),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			return response()->json(['error_msg' => "Language successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	public function updatePatientMobile(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'mobile' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$old_response = $this->PatientService->getDetailById($request->patient_id);
			$record = $this->PatientService->update(array('mobile' => str_replace(["(", ")", '-', " "], "", $request->mobile)), array('id' => $request->patient_id));
			$new_response = $this->PatientService->getDetailById($request->patient_id);
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Mobile number updated',
				'link' => url('/patient/view') . '/' . $request->input('patient_id'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update mobile no',
				'old_response' => serialize($old_response->toArray()),
				'new_response' => serialize($new_response->toArray()),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $new_response->agency_id,
						'title' => 'Updated Mobile Number of Appointment',
						'record_id' => $request->patient_id,
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => serialize($new_response->toArray()),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			return response()->json(['error_msg' => "Mobile no successfully updated", 'status' => 1, 'data' => array('mobile' => $request->mobile)], 201);
		}
	}

	public function updatePatientPhone(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'phone' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$old_response = $this->PatientService->getDetailById($request->patient_id);
			$record = $this->PatientService->update(array('phone' => str_replace(["(", ")", '-', " "], "", $request->phone)), array('id' => $request->patient_id));
			$new_response = $this->PatientService->getDetailById($request->patient_id);
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Phone number updated',
				'link' => url('/patient/view') . '/' . $request->input('patient_id'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update phone no',
				'old_response' => serialize($old_response->toArray()),
				'new_response' => serialize($new_response->toArray()),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $new_response->agency_id,
						'title' => 'Update Phone number of Appointment',
						'record_id' => $request->patient_id,
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => serialize($new_response->toArray()),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			return response()->json(['error_msg' => "Phone no successfully updated", 'status' => 1, 'data' => array('phone' => $request->phone)], 201);
		}
	}

	function changeStatusFlag(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getOldResponse = $this->PatientService->getFlagData($request->id);
			if ($getOldResponse->flag == 1) {
				$this->PatientService->update(array('flag' => 0), array('id' => $request->id));
			} else {
				$this->PatientService->update(array('flag' => 1), array('id' => $request->id));
			}
			return response()->json(['error_msg' => "Flag successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	function updatePatientDob(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'dob' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$old_response = $this->PatientService->getDetailById($request->patient_id);
			$record = $this->PatientService->update(array('dob' => date('Y-m-d', strtotime(request('dob')))), array('id' => $request->patient_id));
			$new_response = $this->PatientService->getDetailById($request->patient_id);
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Update Dob',
				'link' => url('/patient/view') . '/' . $request->input('patient_id'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update date of birth',
				'old_response' => serialize($old_response->toArray()),
				'new_response' => serialize($new_response->toArray()),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $new_response->agency_id,
						'title' => 'Updated Date Of Birth on Appointment',
						'record_id' => $request->patient_id,
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => ''
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			return response()->json(['error_msg' => "Date Of Birth successfully updated", 'status' => 1, 'data' => array()], 201);
		}
	}

	function sendNotificationToUser($patientId, $type, $message = "", $data = "")
	{
		$user = auth()->user();
		$getPatientDetails = $this->PatientService->getPatientDetailsByIdWhitoutAgency($patientId);

		if ($getPatientDetails->assign_user_id != "") {
			$userData = [$getPatientDetails->assign_user_id];
		} else {
			$query = User::select('id')->where('delete_flag', 'N')->where('agency_fk', $getPatientDetails->agency_id);
			if ($user['agency_fk'] != "") {
				$query->where('id', '!=', auth()->user()->id);
			}

			$query->whereRaw('(record_access="All" or record_access="'.$getPatientDetails->type.'")');
			$userData = $query->pluck('id')->toArray();

		}

		$agency_fk = $getPatientDetails->agency_id;

		$title = "";
		$msg = "";
		$serviceData = array();
		if ($type == "Document") {
			$title = "New Document Added";
			$msg = $data['doc_name'];
			$serviceData = $data['services'];
		} else if ($type == "Assign Appointment") {
			$type = "Assign Appointment";
			$title = "Assign Appointment ";
			$assignUser = User::where('id', $getPatientDetails->assign_user_id)->first();

			$msg = 'Assigned To: ' . $assignUser->full_name;
		} else if ($type == "Notes") {
			$type = "Notes";
			$title = "New Note Added";
			$msg = $message;
		}

		// Get Group wise notification
		$userData = Utility::getGroupUsersData($agency_fk, $getPatientDetails->type, $type, $userData, $serviceData);

		$notificationData = array(
			'users' => $userData,
			'agency_fk' => $agency_fk,
			'record_id' => $patientId,
			'title' => $title,
			'msg' => $msg,
			'type' => $type
		);
		Utility::insertNotificationsType($notificationData);
	}

	public function viewV2($id)
	{
		$data['menu'] = "Patient Details";
		$data['auth'] = $data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}


		$record = $this->PatientService->getDetailByIdNew($id);

		if (isset($record->id) && $record->id != '') {

			//Advance Form
			$data['agencyWiseField'] = $this->FormBuilderService->getAgencyWiseField($record->agency_id);

			$data['agencyWiseFieldWithoutFormId'] = $this->FormBuilderService->getAgencyWiseFieldWithoutFormId($record->agency_id);

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
			$alayaCareDetails = $this->AlayacareService->getAllDetailsByAlayacreId($record->alaycare_id);

			$fullAlayaCareName = '';
			if (isset($alayaCareDetails->id)) {
				$fullAlayaCareName = $alayaCareDetails->first_name . ' ' . $alayaCareDetails->last_name;
			}
			$record->alaycare_name = $fullAlayaCareName;


			$remoteDetails = $this->robortService->getDetailsById($record->robort_id);

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
			$data['hhaStatusList'] = $this->hhaCaregiverMedicalService->getStatusList();
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
				foreach ($explode as $val) {
					if ($val != "") {
						$finalArray[] = $val;
					}
				}

				$services = Master::whereRaw('id IN (' . implode(',', $finalArray) . ')')->where('del_flag', 'N')->get();

				$newass = array();
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
			$data['pastAppointment'] = $pastAppointment = Appointment::getPastAppointmentListNew($id);

			$servie = [];
			if (count($pastAppointment) > 0) {
				foreach ($pastAppointment as $record) {
					if (isset($record->service_id) && $record->service_id != '') {
						$servicesIds = explode(',', $record->service_id);


						$services = Master::whereIn('id', $servicesIds)->where('del_flag', 'N')->get();
						$newass = array();
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
			$data['serviceList'] = Master::getServiceRequestWithDisabled();

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

			$disable_date = $this->disableDateService->disableDateAllData($data['record']->type)->toArray();
			$dateArray = explode(', ', implode(', ', $disable_date));
			$dateDetailArray = [];
			if (!empty($dateArray[0])) {
				foreach ($dateArray as $val) {
					$dateDetailArray[] = date('d-m-Y', strtotime($val));
				}
			}
			$data['disable_date'] = json_encode($dateDetailArray);

			$data['language_list'] = Cache::get('language_list', function () use ($auth) {
				return Language::getLanguageList();
			}, 10 * 60);

			return view('patient/patient_view_hospital_v2', $data);
		} else {
			abort(404);
		}
	}

	public function notificationSend($type, $title, $msg, $patientId, $agency_fk, $userType, $userData = [], $serviceData = [])
	{
		$userData = Utility::getGroupUsersData($agency_fk, $userType, $type, $userData, $serviceData);
		$notificationData = array(
			'users' => $userData,
			'agency_fk' => $agency_fk ?? '',
			'record_id' => $patientId ?? '',
			'title' => $title,
			'msg' => $msg,
			'type' => $type,
		);
		Utility::insertNotificationsType($notificationData);
	}

	public function getTotalAppointmentDetails(Request $request)
	{
		$query = $this->PatientService->getSearchPatientDetails($request->all());
		return response()->json(['status' => true, 'error_msg' => 'success', 'data' => $query], 200);
	}

	function saveBasicDetails(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->id;
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
		]);
		if ($validator->fails()) {
			return redirect("")
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {
			$first_name = $request->input('first_name');
			$middle_name = $request->input('middle_name');
			$last_name = $request->input('last_name');
			$age = '';
			if ($request->input('dob') != '') {
				$age = date('Y-m-d', strtotime($request->input('dob')));
			}

			$data = array(
				'first_name' => $first_name,
				'middle_name' => $middle_name,
				'last_name' => $last_name,
				'full_name' => $first_name . ' ' . $last_name,
				'dob' => $age,
				'patient_code' => $request->input('patient_code'),
				'diciplin' => $request->input('diciplin'),
				'insurance_id' => $request->input('insurance_id'),
				'insurance_name' => $request->input('insurance_name'),
				'ssn' => $request->ssn,
				'hamaspik_payment' => $request->input('hamaspik_payment'),
			);
			if ($request->input('insurance_name') == 'other') {
				$data['other_insurance_name'] = $request->other_insurance_name;
			} else {
				$data['other_insurance_name'] = '';
			}

			$getExistingData = $this->PatientService->getDetailById($id);
			$update = $this->PatientService->update($data, array('id' => $id));
			$getNewData = $this->PatientService->getDetailById($id);
			$ipaddress = Utility::getIP();
			$insuranceDetails = $this->insuranceMasterService->getDetailById($getNewData->insurance_name);
			$insuranceName = "";
			if (isset($insuranceDetails->id)) {
				$insuranceName = $insuranceDetails->insurance_name;
			}

			if ($getNewData->insurance_name == 'other') {
				$insuranceName = 'Other';
			}

			$getNewData->insuranceName = $insuranceName;
			$getNewData->hamaspik_payment = isset($getNewData->hamaspik_payment) && $getNewData->hamaspik_payment == 1 ? 'Hamaspik 1' : 'Hamaspik 2';

			$payment_type = '';
			$masterData = Master::getAllDataByMasterTypeFk(array(12, 17, 25));
			foreach ($masterData as $val) {
				if (isset($getNewData->payment_type) && $getNewData->payment_type != '') {
					if ($val->id == $getNewData->payment_type) {
						$payment_type = $val->name;
					}
				}
			}
			$getNewData->payment_type_new = $payment_type;

			$insertLog = [
				'type' => 'Update Appointment Basic details',
				'link' => url('/patient/view/') . $id,
				'module' => 'Patient Appointment',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment basic deatils',
				'new_response' => serialize($getNewData->toArray()),
				'old_response' => serialize($getExistingData->toArray()),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $getExistingData->agency_id,
						'title' => 'Updated Appointment Basic details',
						'record_id' => $id,
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => serialize($getNewData->toArray()),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			return response()->json(['status' => true, 'error_msg' => 'Basic detail saved successfully.', 'data' => $getNewData], 200);
		}
	}

	function saveAddressDetails(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->id;
		$data = array(
			'address1' => $request->input('address1'),
			'address2' => $request->input('address2'),
			'state' => $request->input('state'),
			'city' => $request->input('city'),
			'zip_code' => $request->input('zip_code'),
			'county' => $request->input('county') == "County not found" ? '' : $request->input('county'),
			'emergency_contact_name' => $request->emergency_contact_name,
			'emergency_phone' => $request->emergency_phone,
			'email' => $request->email,
		);

		$getExistingData = $this->PatientService->getDetailById($id);
		$update = $this->PatientService->update($data, array('id' => $id));
		$getNewData = $this->PatientService->getDetailById($id);
		$ipaddress = Utility::getIP();

		$insertLog = [
			'type' => 'Update Appointment',
			'link' => url('/patient/view/') . $id,
			'module' => 'Patient Appointment',
			'object_id' => $id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment Address details',
			'new_response' => serialize($getNewData->toArray()),
			'old_response' => serialize($getExistingData->toArray()),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		try {
			if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
				$agencyNotifyData = array(
					'agencyid' => $getExistingData->agency_id,
					'title' => 'Updated Appointment Address details',
					'record_id' => $id,
					'record_type' => 'Appointment',
					'msg' => '',
					'res_data' => serialize($getNewData->toArray()),
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);
			}
		} catch (\Throwable $th) {}
		return response()->json(['status' => true, 'error_msg' => 'Address detail saved successfully.', 'data' => $getNewData], 200);
	}

	function saveOtherDetails(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->id;
		$completed_date = $due_date = '';
		if ($request->input('completed_date') != '') {
			$completed_date = date('Y-m-d', strtotime($request->input('completed_date')));
		}
		if ($request->input('due_date') != '') {
			$due_date = date('Y-m-d', strtotime($request->input('due_date')));
		}
		$data = array(
			'cin' => $request->input('cin'),
			'due_date' => $due_date,
			'language' => $request->input('language'),
			'remarks' => $request->input('note'),
			'medicare_no' => $request->input('medicare_no'),
			'completed_date' => $completed_date,
		);
		$getExistingData = $this->PatientService->getDetailById($id);
		$update = $this->PatientService->update($data, array('id' => $id));
		$getNewData = $this->PatientService->getDetailById($id);
		$ipaddress = Utility::getIP();

		$language = Language::where('id', $request->input('language'))->first();

		$languageName = "";
		if (isset($language->id)) {
			$languageName = $language->name;
		}

		$getNewData->languageName = $languageName;
		$insertLog = [
			'type' => 'Update Appointment',
			'link' => url('/patient/view/') . $id,
			'module' => 'Patient Appointment',
			'object_id' => $id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment Other details',
			'new_response' => serialize($getNewData->toArray()),
			'old_response' => serialize($getExistingData->toArray()),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		try {
			$agencyNotifyData = array(
				'agencyid' => $getExistingData->agency_id,
				'title' => 'Updated Appointment Other details',
				'record_id' => $id,
				'record_type' => 'Appointment',
				'msg' => '',
				'res_data' => serialize($getNewData->toArray()),
			);
			Common::insertAgencyNotificationsOfUser($agencyNotifyData);
		} catch (\Throwable $th) {}
		return response()->json(['status' => true, 'error_msg' => 'Other detail saved successfully.', 'data' => $getNewData], 200);
	}

	public function updateAlaycareClientId(Request $request)
	{
		$user = auth()->user();
		$oldData = $this->PatientService->getPatientDetailsById($request->patient_id, $request->agency_id);
		$query = Patient::find($request->patient_id);
		$data = [

			'alaycare_id' => $request->alyacare_id,
			'alaycare_name' => $request->name,
		];

		$query->update($data);
		$getNewData = $this->PatientService->getPatientDetailsById($request->patient_id, $request->agency_id);

		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Update Alayacare Client',
			'link' => url('/patient/update-alaycare-client-id'),
			'module' => 'Patient Appointment',
			'object_id' => $request->patient_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Alayacare client',
			'new_response' => serialize($getNewData->toArray()),
			'old_response' => serialize($oldData->toArray()),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		return response()->json(['error_msg' => 'Alaycare Succesfully Added', 'status' => 0, 'data' => array($query)], 200);
	}

	public function documentDetailsById(Request $request)
	{

		$query = $this->DocumentPatientService->getDocumentDetailsByIdOrPatientId($request->document_id, $request->patient_id);
		if (isset($query->id)) {
			$assignUserName = array();
			$userIds = explode(',', $query->assign_document_review);
			$getAssignUserDetails = UserHelper::getDetailsByUserids($userIds);
			foreach ($getAssignUserDetails as $user) {
				if (isset($user->id)) {
					$assignUserName[] = $user->first_name . ' ' . $user->last_name;
				}
			}
			$query->assign_document_approval = $assignUserName;
		}
		return response()->json(['status' => 0, 'data' => array($query)], 200);
	}

	public function saveWriteDocumentData($type, $documentPatientId, $fileUpload, $documentName)
	{
		$data = [
			'document_name' => $documentName,
			'type' => $type,
			'document_patient_id' => $documentPatientId,
			'file_upload' => $fileUpload,
			'created_at' => date('Y-m-d H:i:s')
		];
		$this->documentSendService->saveWriteDocumentData($data);
	}

	public function fetchRefusedStatus(Request $request)
	{
		$type = $request->type;
		$masterData = Cache::get('masters_data', function () use ($type) {
			return Master::getAllDataByMasterTypeFk(array($type));
		}, 10, 60);

		$finalArray = [];
		if (count($masterData) > 0) {
			foreach ($masterData as $mt) {
				$temp = [];
				$temp['id'] = $mt->id;
				$temp['name'] = $mt->name;
				$finalArray[] = $temp;
			}
		}
		return response()->json(['status' => 0, 'data' => $finalArray], 200);
	}

	public function updatePatientNote(Request $request)
	{
		$user = auth()->user();
		$getDetails = $this->PatientService->getDetailById($request->record_id);
		$oldData = ['remarks' => $getDetails->remarks];
		$update = $this->PatientService->update(array('remarks' => $request->notes), array('id' => $request->record_id));


		$newData = ['remarks' => $request->notes];
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Update Patient Note',
			'link' => url('/patient/update-patient-notes'),
			'module' => 'Patient Appointment',
			'object_id' => $request->record_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Notes',
			'new_response' => serialize($newData),
			'old_response' => serialize($oldData),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		return response()->json(['error_msg' => 'Notes succesfully updated', 'status' => 0, 'data' => array()], 200);
	}

	public function saveBulkAssignUser(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'bulk_appointments_id' => 'required',
			'bulk_user_id' => 'required',

		]);
		if ($validator->fails()) {
			return redirect("/appointment")
				->withErrors($validator, 'error')
				->withInput();
		} else {
			$explode = explode(',', $request->bulk_appointments_id);
			foreach ($explode as $val) {
				$data = array('assign_user_id' => $request->bulk_user_id);
				if (!empty($request->bulk_assign_department)) {
					$data['dept_id'] = $request->bulk_assign_department;
				}
				$this->commonAssignUser($val,$data,url('/save-bulk-assign-user'),'bulk');
			}
			return redirect("/appointment")
				->with('success', 'Appointments assigned successfully');
		}
	}

	public function esignMoveDocumentStore(Request $request)
	{
		$user = auth()->user();

		$validator = Validator::make($request->all(), [
			// 'request_service_id' => 'required',
			// 'document_service_id' => 'required',
			'template_id' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$writeDocumentData = $this->DocumentPatientService->getWriteDocData($request->input('esign_doc_id'));
			$template = $this->DocumentPatientService->getTemplateData($request->input('template_id'));

			if ($request->template_id == 0) {
				$documentName = $writeDocumentData->document_name ?? '';
				$typeLog = 'Move document from Esign document';
				$messagesLog = $user->first_name . ' ' . $user->last_name . ' has moved document from Esign document';
			} else {
				$documentName = $template->template_name;
				$typeLog = 'Move document from template';
				$messagesLog = $user->first_name . ' ' . $user->last_name . ' has moved document from Esign template';
			}
			$documentReport = $this->DocumentPatientService->getDocumentSentReportData($request->input('group_id'));

			$pdfname = "";

			if ($request->template_id == 0) {

				if (!empty($writeDocumentData) && isset($writeDocumentData->file_upload) && $writeDocumentData->file_upload != "") {
					$pdfGenerate = public_path() . '/patientWriteDocument/' . $writeDocumentData->file_upload;

					if (file_exists($pdfGenerate)) {

						$moves = file_get_contents($pdfGenerate);
						$name = file_put_contents(public_path('patientdocument/' . $writeDocumentData->file_upload), $moves);

						$pdfname = $writeDocumentData->file_upload;
					} else {
						if(env('FILE_UPLOAD_PERMISSION') !="development"){
							/*****Code S3 Bucket */
							$inputPath = Storage::disk('s3')->get('/patientWriteDocument/' . $writeDocumentData->file_upload);

							$test = Storage::disk('s3')->put('patientdocument/' . $writeDocumentData->file_upload, $inputPath);

							$pdfname = $writeDocumentData->file_upload;
						}

					}
				}
			} else {
				if (!empty($documentReport) && isset($documentReport->pdf_generate) && $documentReport->pdf_generate != "") {
					$pdfGenerate = public_path() . '/dosusinguploads/docusign/' . $documentReport->pdf_generate;

					if (file_exists($pdfGenerate)) {

						$moves = file_get_contents($pdfGenerate);
						$name = file_put_contents(public_path('patientdocument/' . $documentReport->pdf_generate), $moves);

						$pdfname = $documentReport->pdf_generate;
					} else {
						if(env('FILE_UPLOAD_PERMISSION') !="development"){
							/*****Code S3 Bucket */
							$inputPath = Storage::disk('s3')->get('/dosusinguploads/docusign/' . $documentReport->pdf_generate);

							$test = Storage::disk('s3')->put('patientdocument/' . $documentReport->pdf_generate, $inputPath);

							$pdfname = $documentReport->pdf_generate;
						}

					}
				}
			}

			$request_service = '';
			if (!empty($request->request_service_id[0])) {
				$request_service = implode(", ", $request->request_service_id);
			}
			$getPatientDetails = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->input('id'));

			$data = array(
				'document_name' => $documentName,
				'attachment' => $pdfname,
				'patient_id' => $request->input('id'),
				'request_service_id' => $request_service,
				'templete_id' => $request->input('template_id'),
				'agency_form_id' => $request->input('agency_form_id'),
			);

			$udocArray = [];
			if (isset($getPatientDetails->type) && $getPatientDetails->type == "Patient") {
				$getUserDocs = $this->userDocApprovalService->getUserIdsByType("Patient");
				if (count($getUserDocs) > 0) {
					foreach ($getUserDocs as $udoc) {
						$udocArray[] = $udoc->user_id;
					}
				}
				$data['assign_document_review'] = implode(',', $udocArray);
				$data['document_review_status'] = "Pending";
				$internal_use = 1;
			} else {
				$status = "Approved";
				if(isset($request->internal_use_esign) && $request->internal_use_esign ==1){
					$status = "Pending";
				}
				$data['document_review_status'] = $status;
				$internal_use = isset($request->internal_use_esign)?$request->internal_use_esign:0;
			}
			$data['internal_use'] = $internal_use;
			$insert = $this->DocumentPatientService->save($data);

			if ($insert) {
				$allLog = $data;
				$allLog['document_service_id'] = $request->document_service_id;
				if (!empty($request->document_service_id[0])) {
					foreach ($request->document_service_id as $serviceId) {
						$data = [
							'patient_id' => $request->input('id'),
							'document_id' => $insert,
							'service_id' => $serviceId,
						];

						$this->documentUploadService->save($data);
					}
				}

				if (count($udocArray) > 0) {
					foreach ($udocArray as $suser) {
						$udata = array(
							'user_id' => $suser,
							'patient_id' => $request->input('id'),
							'document_id' => $insert
						);
						$this->multiplePatientDocApprovalService->save($udata);
					}
				}


				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => $typeLog??'',
					'link' => url('/patient/view/') . '/' . $request->input('id'),
					'module' => 'Patient Appointment',
					'object_id' => $request->input('id'),
					'message' => $user->first_name . ' ' . $user->last_name . ' has Add Document From Appointment',
					'new_response' => serialize($data),
					'message' => $messagesLog??'',
					'ip' => $ipaddress,
				];

				LogsService::save($insertLog);


				$newResponse['move_to_esign_by_name'] = $user->full_name;
				$newResponse['move_to_esign_date'] = date('Y-m-d H:i:s');
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => $typeLog??'',
					'link' => url('/esign-move-document'),
					'module' => 'Esign Section',
					'module_id' => $request->group_id,
					'new_response' => serialize($allLog),
					'old_response' => '',
					'is_status' => 'Move To Document',
					'message'=>$messagesLog
				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

				// Get Group wise notification
				$serviceData = $request->document_service_id ?? [];
				$documentReportData = $this->DocumentPatientService->getAllDocumentSentReportData($request->input('group_id'));
				$patientId = $request->input('id') ?? '';
				$getPatientDetails = $this->PatientService->getPatientDetailsByIdWhitoutAgency($patientId);
				$agencyFk = $getPatientDetails->agency_id ?? '';
				$userType = $getPatientDetails->type ?? '';

				$signerAction = 'Move To Document Done';
				$title = 'Esign ' . '(' . $signerAction . ')';
				$msg = '<br><b>Document Name : </b>' . ($documentName ?? '') . ' (' . date('m/d/Y h:i A', strtotime($documentReportData->created_date)) . ')';
				$patientId = $request->input('id') ?? '';
				$this->notificationSend('Esign', $title, $msg, $patientId, $agencyFk, $userType, $userData = [], $serviceData);
				// Get Group wise notification
				return response()->json(['status' => true, 'error_msg' => 'Document  successfully uploaded'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	public function sendBulkStatusProcessingMail($patientId)
	{
		$patientData = $this->PatientService->getDetailById($patientId);
		if (isset($patientData->id)) {
			$documentPendingData = $this->DocumentPatientService->getPendingDocData($patientData->id);

			$otherServicesData = $mdoServiceData = $globalServices = $docWiseServices = array();
			$approvedMap = Utility::dynamicDocumentApproved();
			$allApprovedReviewers = $approvedMap['All'] ?? [];
			foreach ($documentPendingData as $docData) {
				$assignData = explode(',', $docData->assign_document_review);
				if (array_intersect($assignData, $allApprovedReviewers)) {
					if (isset($docData->documentUploadServiceDetailsMany)) {
						$services = array();
						foreach ($docData->documentUploadServiceDetailsMany as $docService) {
							$services[] = $docService->service_id;
							$globalServices[] = $docService->service_id;
							$docWiseServices[$docData->id][] = $docService->service_id;
						}
						if (in_array('181', $services)) {
							$mdoServiceData[] = $docData;
						} else {
							$otherServicesData[] = $docData;
						}
					} else {
						$otherServicesData[] = $docData;
					}
				}
			}
			$serviceData = Master::getRecordById($globalServices)->pluck('name', 'id');
			$subject = 'Document Pending #' . $patientData->id . ' Notification from NY BEST MEDICAL';
			$querys = Agency::getAllDetailsbyAgencyId($patientData->agency_id);
			$jadaReviewerIds = [];
			$tilineReviewerIds = [];
			foreach ($approvedMap as $key => $reviewerGroups) {
				if($key == '181'){
					foreach ($reviewerGroups as $group) {
						if (is_array($group)) {
							$jadaReviewerIds = array_merge($jadaReviewerIds, array_keys($group));
						}
					}
				}else{
					foreach ($reviewerGroups as $group) {
						if (is_array($group)) {
							$tilineReviewerIds = array_merge($tilineReviewerIds, array_keys($group));
						}
					}
				}
			}

			if (count($otherServicesData) > 0) {

				foreach ($otherServicesData as $doc) {
					$docServices = array_key_exists($doc->id, $docWiseServices) ? $docWiseServices[$doc->id] : [];
					$serviceArray = array();
					foreach ($docServices as $ser) {
						$serviceArray[] = $serviceData[$ser];
					}
					$doc->services = implode(', ', $serviceArray);
				}
				$finalEmail = UserHelper::getDetailsByUserids($tilineReviewerIds)->pluck('email')->toArray();

				$email_data = array(
					'agency_name' => $querys->agency_name != '' ? $querys->agency_name : '-',
					'portal_id' => $patientData->id,
					'type' => $patientData->type,
					'first_name' => $patientData->first_name,
					'last_name' => $patientData->last_name,
					'docData' => $otherServicesData,
				);

				$messages = Utility::getHtmlContent('email_template.bulk_processing_doc_other_service', $email_data);

				$this->SendEmailNotificationSerivce->UserMailWithMultipleEmail($finalEmail, "", $subject, $messages, "");

			}
			if (count($mdoServiceData) > 0) {

				foreach ($mdoServiceData as $mdoc) {
					$mdocServices = array_key_exists($mdoc->id, $docWiseServices) ? $docWiseServices[$mdoc->id] : [];
					$mserviceArray = array();
					foreach ($mdocServices as $ser) {
						$mserviceArray[] = $serviceData[$ser];
					}
					$mdoc->services = implode(', ', $mserviceArray);
				}

				$finalEmail = UserHelper::getDetailsByUserids($jadaReviewerIds)->pluck('email')->toArray();
				$email_data = array(
					'agency_name' => $querys->agency_name != '' ? $querys->agency_name : '-',
					'portal_id' => $patientData->id,
					'type' => $patientData->type,
					'first_name' => $patientData->first_name,
					'last_name' => $patientData->last_name,
					'docData' => $mdoServiceData,
				);
				$messages = Utility::getHtmlContent('email_template.bulk_processing_doc_other_service', $email_data);
				$this->SendEmailNotificationSerivce->UserMailWithMultipleEmail($finalEmail, "", $subject, $messages, "");

			}
			return 1;
		}
	}

	public function bulkAppointmentDelete(Request $request)
	{
		$user = auth()->user();

		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$explode = explode(',', $request->patient_id);

			if (count($explode) > 0) {
				foreach ($explode as $pt) {
					$update = $this->PatientService->SoftDelete(array('deleted_flag' => 'Y', 'deleted_date' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id), array('id' => $pt));
					$ipaddress = Utility::getIP();
					$insertLog = [
						'link' => url('/bulk-appointments-delete'),
						'module' => 'Patient Appointment',
						'object_id' => $pt,
						'message' => $user->first_name . ' ' . $user->last_name . ' has Delete Appointment',

						'ip' => $ipaddress,
					];
					if (isset($request->type) && $request->type == 'Chart') {
						$insertLog['type'] = 'Delete Chart';
					} else {
						$insertLog['type'] = 'Delete Appointment';
					}
					LogsService::save($insertLog);
				}
			}

			return 1;
		}
	}

	public function noteDelete(Request $request){
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$update = $this->PatientNotesService->SoftDelete(array('delete_flag'=>'Y'),array('id'=>$request->id));

			if($update){
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type'=>'Notes Delete',
					'link' => url('/patient-notes-delete'),
					'module' => 'Patient Appointment',
					'object_id' => $request->patient_id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has deletes the note',

					'ip' => $ipaddress,
				];

				LogsService::save($insertLog);
				return response()->json(['status' => true, 'error_msg' => 'Note deleted successfully'], 200);

			}else{
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again.'], 500);
			}

		}
	}

	public function unlinkPatient(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'agency_id' => 'required',
			'hha_profile_id' => 'required',
		]);
		if ($validator->fails()) {

		} else {
			$getExistingData = $this->PatientService->getDetailById($request->patient_id);
			$data = array(
				'link_hha_patient' => NULL
			);
			$this->PatientService->update($data, array('id' => $request->patient_id));
			HHAPatient::where('patient_id',$getExistingData->link_hha_patient)->where('patient_record_id',$request->patient_id)->update(array('patient_record_id'=>NULL));
			$ipaddress = Utility::getIP();
			$data['id'] = $request->patient_id;
			$insertLog = [
				'type' => 'UnLink From HHX Patient',
				'link' => url('/unlink-hha-patient'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment',
				'new_response' => serialize($data),
				'old_response' => serialize(['id'=>$request->patient_id,'link_hha_patient'=>$getExistingData->link_hha_patient]),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'HHX Patient successfully unlinked.'], 200);
		}
	}

	public function unlinkCaregiver(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'agency_id' => 'required',
			'hha_profile_id' => 'required',
		]);
		if ($validator->fails()) {
		} else {
			$getExistingData = $this->PatientService->getDetailById($request->patient_id);
			$data = array(
				'link_hha_caregiver' =>NULL
			);
			$update = $this->PatientService->update($data, array('id' => $request->patient_id));
			$ipaddress = Utility::getIP();
			$data['id'] = $request->patient_id;
			$insertLog = [
				'type' => 'Unlink From HHX Caregiver',
				'link' => url('/unlink-hha-caregiver'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Appointment',
				'new_response' => serialize($data),
				'old_response' => serialize(['id'=>$request->patient_id,'link_hha_caregiver'=>$getExistingData->link_hha_caregiver]),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
				if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
					$agencyNotifyData = array(
						'agencyid' => $getExistingData->agency_id,
						'title' => 'Unlinked From To HHX Caregiver of appointment',
						'record_id' => $request->patient_id,
						'record_type' => 'Appointment',
						'msg' => '',
						'res_data' => serialize($data),
					);
					Common::insertAgencyNotificationsOfUser($agencyNotifyData);
				}
			} catch (\Throwable $th) {}
			return response()->json(['status' => true, 'error_msg' => 'HHX Caregiver successfully unlinked'], 200);
		}
	}

	function getRegenerate($file,$name){
		$content = file_get_contents($file->getRealPath());
		return response($content, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="'.$name.'"');
	}

	public function updateReferralSource(Request $request){
		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'referral_source_type' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$oldData = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->id);
			$data=array('referral_type'=>$request->referral_source_type);
			$this->PatientService->update($data,array('id'=>$request->id));
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Update Referral Source Type',
				'link' => url('/update-referral-source'),
				'module' => 'Patient Appointment',
				'object_id' => $request->id,
				'message' => $auth->first_name . ' ' . $auth->last_name . ' has update Referral Source type',
				'new_response' => serialize($data),
				'old_response' => serialize($oldData->toArray()),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Referral Source type has been successfully updated.','data'=>$data], 200);
		}
	}

	private function commonAssignUser($appoiment_id,$data,$link,$type=""){
		$user = auth()->user();
		$old_response = $this->PatientService->getDetailById($appoiment_id);
		$update = $this->PatientService->update($data, array('id' => $appoiment_id));
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $user->id;
		$assignUser = User::getDetailsById($data['assign_user_id']);
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Assign Appointment',
			'link' => $link,
			'module' => 'Patient Appointment',
			'object_id' => $appoiment_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' assigned appointment to ' . $assignUser->full_name . ($type == 'bulk' ? ' via bulk process' : ''),
			'new_response' => serialize($data),
			'old_response' => serialize($old_response),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		$this->sendNotificationToUser($appoiment_id, 'Assign Appointment');
		return $update;
	}

	public function unMergeAppointment(Request $request)
	{
		$user = auth()->user();

		$validator = Validator::make($request->all(), [
			'record_id' => 'required',
			'appointment_id' => 'required',
		]);

		if ($validator->fails()) {
			return $this->validationErrorResponse($validator);
		}

		$appointment = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->appointment_id);

		if (!$appointment?->id) {
			return $this->errorResponse("Chart ID does not exist");
		}

		$record = $this->PatientService->getDetailById($request->record_id);

		if ($appointment->agency_id !== $record->agency_id) {
			return $this->errorResponse("Chart ID is incorrect because both agencies must be different");
		}

		if (strtolower($appointment->type) !== strtolower($record->type)) {
			return $this->errorResponse("Chart type must be same");
		}


		$this->performUnmerge($request, $appointment, $record);

		return response()->json([
			'error_msg' => "Record successfully unmerged",
			'data' => []
		], 200);
	}

	private function validationErrorResponse($validator)
	{
		return response()->json([
			'error_msg' => $validator->errors()->all()[0],
			'data' => []
		], 422);
	}

	private function errorResponse(string $message)
	{
		return response()->json([
			'error_msg' => $message,
			'data' => []
		], 500);
	}

	private function performUnmerge($request,  $appointment, $record)
	{
		$user = auth()->user();
		$oldResponse = $appointment->toArray();

		$this->PatientService->update([
			'deleted_flag' => 'N',
			'merge_appointment_id' =>null
		], ['id' => $request->appointment_id]);

		$deletedPatient = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->appointment_id);
		$oldDeletedPatientData = $deletedPatient->toArray();
		$oldRecordData = $record ? $record->toArray() : [];

		$this->PatientService->update(['merge_appointment_id' => null], ['id' => $request->record_id]);

		$mergedData = $this->PatientService->getDetailById($request->record_id);
		$mergedData = $mergedData->toArray();
		$ipaddress = Utility::getIP();

		$this->appointmentMergeLogsService->softDelete(array('del_flag'=>'Y'),array('main_patient_id'=>$request->record_id,'merge_patient_id'=>$request->appointment_id));
		// Log unmerge for record
		LogsService::save([
			'type' => 'Unmerge Appointment',
			'link' => url('/patient/un-combine-appointment'),
			'module' => 'Patient Appointment',
			'object_id' => $request->record_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has unmerged Record',
			'old_response' => serialize($oldRecordData),
			'new_response' => serialize($mergedData),
			'ip' => $ipaddress,
		]);

		// Log unmerge for appointment
		LogsService::save([
			'type' => 'Unmerge Appointment',
			'link' => url('/patient/un-combine-appointment'),
			'module' => 'Patient Appointment',
			'object_id' => $request->appointment_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has unmerged Record',
			'old_response' => serialize($oldResponse),
			'new_response' => serialize($oldDeletedPatientData),
			'ip' => $ipaddress,
		]);

	}

	/*****************Copy and Paste by getHHADocument function  */
	public function getHHADocumentByMedicalList(Request $request)
	{
		$agencyId = $request->input('agencyId');
		$patientId = $request->input('patientId');
		$officeID = '2';
		$record = $this->PatientService->getDetailByIdNew($patientId);

		$documents = [];
		$caregiver = HHACaregivers::where("caregiver_id", $record->link_hha_caregiver)->where('agency_fk', $agencyId)->first();

		if (isset($caregiver->id) && $caregiver->id != '') {
			$documents = HHACaregiversHelper::getCaregiverMedicalDueList($agencyId, $caregiver->caregiver_id);
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyId)->where('id', $record->hha_id)->first();
			$documents = HHACaregiversHelper::getCaregiverMedicalDueList($agencyId, $getCaregiverDetails->caregiver_id);
		}

		return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $documents, 'officeId' => $record->link_hha_caregiver]);

	}

	/*************Copy and Paste updateHHADocument function */
	/*************Copy and Paste updateHHADocument function */
	public function updateHHADocumentWithMedicalResult(Request $request)
	{
		$auth = auth()->user();
		$validationRequired = [];
		if(!empty($request->document_medical_type)){
			$validationRequired['document_medical_type']= "required";
			$validationRequired['hha_medical_result']= "required";
		}

		if(!empty($request->create_document_medical_type[0])){
			$validationRequired['create_document_medical_type']= "required";
			$validationRequired['hha_create_medical_result']= "required";
		}
		$validator = Validator::make($request->all(),$validationRequired);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		}
		$dateCompleted = date('Y-m-d', strtotime(request('completed_date')));
		$agencyID = $request->input('agencyId');
		$getDetails = $this->DocumentPatientService->getDetailsById($request->input('id'));

		$record = $this->PatientService->getDetailByIdNew($getDetails->patient_id);

		$caregiverMedicalDocID = request('document_medical_type');
		$caregiverId = '';
		if (isset($record->link_hha_caregiver) && $record->link_hha_caregiver != '') {
			$caregiverId = $record->link_hha_caregiver;
		} else {
			$getCaregiverDetails = HHAAppointment::where('agency_id', $agencyID)->where('id', $record->hha_id)->first();

			if (!$getCaregiverDetails) {

				return response()->json(['error_msg' => "Caregiver details not found in HHX", 'status' => 1, 'data' => array()], 400);
			} else {
				$caregiverId = $getCaregiverDetails->caregiver_id;
			}
		}

		//$getHHACaregiverDetails = HHACaregivers::select('officeId')->where('caregiver_id',$caregiverId)->first();
		$getHHACaregiverDetails = HHACaregivers::getCaregiverDetailsByAgencyIdAndCaregiverId($caregiverId,$request->input('agencyId'));
		$annualHelath = '';
		$url = '';
		$extension = '';
		if (isset($getDetails->attachment)) {
			$explode = explode('.', $getDetails->attachment);
			$extension = $explode[1];
			$url = URL::to('/') . '/patientdocument/' . $getDetails->attachment;
		}
		$fileName = $getDetails->attachment;
		$fileName = str_replace("patientdocument/", "", $fileName);

		$image = "patientdocument/" . $fileName;

		$file = Storage::disk('s3')->get($image);
		$Result = "";
		$commonMedicalResponse = [];
		$ipaddress = Utility::getIP();
		if(!empty($caregiverMedicalDocID)){
			foreach ($caregiverMedicalDocID as $key => $medicalId) {
				if ($medicalId == '80093') {
					$annualHelath = $medicalId;
				}

				if (isset(request('hha_medical_result')[$medicalId])) {
					$updateMedicalDetails = [
						'agency_id'=>$request->input('agencyId'),
						'caregiver_id'=>$caregiverId,
						'office_id'=>$getHHACaregiverDetails->officeId,
						'caregiver_medical_id'=>$request->input('caregiver_medicals_item_'.$medicalId),
						'medical_id'=>$medicalId,
						'datePerform'=>$dateCompleted,
						'result'=>request('hha_medical_result')[$medicalId],
						'file_name'=>$fileName,
						'file_response'=>base64_encode($file)
					];
					// $Result = HHACaregiversHelper::getUpdateHHADocument($request->input('agencyId'), $caregiverId, $medicalId, request('hha_medical_result')[$medicalId], $dateCompleted,$getDetails->patient_id,$getDetails->id);
					$result = HHACaregiversHelper::updateHHAMedicalDetails($updateMedicalDetails);
					if (isset($result['status']) &&  $result['status'] !== 1) {
						return response()->json(['error_msg' => $result['message']. ' ', 'status' => 0, 'data' => array()], 500);
					}else{
						if(isset($result['data'])){
							$commonMedicalResponse[] = $result['data'];
						}
					}
				}
			}

			if(!empty($commonMedicalResponse[0])){

				$hhaLogData = [
					'patient_id'=>$getDetails->patient_id,
					'document_id'=>$getDetails->id,
					'hha_patient_id'=>$caregiverId,
					'type'=>$record->type,
					'hha_module_type'=>'Medical',
					'send_response'=>serialize($request->except('_token')),
					'return_response'=>serialize($commonMedicalResponse),
					'action'=>'Update',
					'ip_address' => $ipaddress,
				];
				$this->hhaLogService->save($hhaLogData);
			}
		}

		if ($request->input('agencyId') == 106) {
			if ($request->hha_due_date != "") {
				sleep(3);
				foreach ($caregiverMedicalDocID as $key => $medicalId) {
					$annualHelath = $medicalId;
					$Result = HHACaregiversHelper::createNewMedicalForHamaspik($request->input('agencyId'), $caregiverId, $annualHelath, request('hha_medical_result')[$annualHelath], $dateCompleted, date('Y-m-d', strtotime($request->hha_due_date)));
				}
			}
		}

		/**************Create Medical Code *******/
		$commonMedicalAddResponse = [];
		if(!empty($request->create_document_medical_type[0])){
			$flag = 0;
			foreach ($request->create_document_medical_type as $key => $cMedicalId) {
				if ($cMedicalId == '80093') {
					$annualHelath = $cMedicalId;
				}

				if (isset(request('hha_create_medical_result')[$cMedicalId])) {
					$updateMedicalDetails = [
						'agency_id'=>$request->input('agencyId'),
						'caregiver_id'=>$caregiverId,
						'hha_medical_document_medical_id'=>$cMedicalId,
						'hha_medical_document_date_perform'=>$dateCompleted,
						'hha_medical_document_result'=>request('hha_create_medical_result')[$cMedicalId],
						'hha_medical_document_name'=>$fileName,
						'image'=>base64_encode($file),
					];
					$result = HHACaregiversHelper::createNewCaregiverMedicalTest($updateMedicalDetails);
					if (isset($result['status']) &&  $result['status'] !== 1) {
						return response()->json(['error_msg' => $result['message']. ' ', 'status' => 0, 'data' => array()], 500);
					}else{
						if(isset($result['data'])){
							$commonMedicalAddResponse[] = $result['data'];
							$flag = 1;
						}
					}
				}
			}

			if($flag ==1){
				$hhaLogData = [
					'patient_id'=>$getDetails->patient_id,
					'document_id'=>$getDetails->id,
					'hha_patient_id'=>$caregiverId,
					'type'=>$record->type,
					'hha_module_type'=>'Medical',
					'send_response'=>serialize($request->except('_token')),
					'return_response'=>serialize($commonMedicalAddResponse),
					'action'=>'Add',
					'ip_address' => $ipaddress,
				];
				$this->hhaLogService->save($hhaLogData);
			}
		}

		$this->DocumentPatientService->update(array('uploaded_to_hha' => 1), array('id' => $request->input('id')));

		$insertLog = [
			'type' => 'Update to HHX Document',
			'link' => url('/update-hha-document'),
			'module' => 'Patient Appointment',
			'object_id' =>$getDetails->patient_id,
			'message' => $auth->first_name . ' ' . $auth->last_name . ' has Update to HHX Document',
			'new_response' => serialize($request->except('_token')),

			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		//$getCaregiverDetails = HHAAppointmentHelper::getDetailsByIdWithoutJoin($record->id);
		if ($request->input('agencyId') != 106) {
			$query = HHACaregiversHelper::getSendHHADocument($request->input('agencyId'), $getDetails->document_name, $extension, $request->input('document_type'), $caregiverId, $file, $request->input('id'));
			$docus = $request->except('_token');
			$docus['document_name'] = $getDetails->document_name;
			$docus['attachment'] = $getDetails->attachment;
			$hhaLogData = [
				'patient_id'=>$getDetails->patient_id,
				'document_id'=>$getDetails->id,
				'hha_patient_id'=>$caregiverId,
				'type'=>$record->type,
				'hha_module_type'=>'Medical',
				'send_response'=>serialize($docus),
				'return_response'=>serialize($commonMedicalResponse),
				'action'=>'Document Add',
				'ip_address' => $ipaddress,
			];
			$this->hhaLogService->save($hhaLogData);
		}

		return response()->json(['error_msg' => "Document successfully updated!!", 'status' => 1, 'data' => array(), 'agency' => $request->input('agencyId')], 201);

	}

	public function documentNormalPdfRegenerate($requestData){

		$priceImage = $requestData['images'];
		$name = uniqid() . time() . '.' . $priceImage->getClientOriginalExtension();

		$destination1 = public_path('patientdocument');
		$destination2 = public_path('patientWriteDocument');

		$mimeType = $priceImage->getMimeType();

		if (env('FILE_UPLOAD_PERMISSION') == 'development') {


			$priceImage->move($destination1, $name);
			\File::copy($destination1 . '/' . $name, $destination2 . '/' . $name);
			$image = $name;
		} else {
			Storage::disk('s3')->putFileAs('patientdocument', $priceImage, $name);
				Storage::disk('s3')->putFileAs('patientWriteDocument', $priceImage, $name);

			$image = $name;
		}

		return $image;
	}

	public function documentOtherPdfRegenerate($requestData){
		ini_set('memory_limit', '1024M');
		$imageData = $requestData['images'];
		$counter = new \Imagick();
		$counter->pingImage($imageData->getRealPath());
		$totalPages = $counter->getNumberImages();
		$counter->clear();
		$counter->destroy();
		if($totalPages ==1)
		{
			/****Trello Issue Server Error When Uploading Documents to Portal Date : (01-01-2026) Resolved */
			return $this->documentNormalPdfRegenerate($requestData);
		}

		$imagick = new \Imagick();

		$imagick->setResolution(300, 300);
		$imagick->setBackgroundColor(new \ImagickPixel('white'));
		$imagick->setResourceLimit(\Imagick::RESOURCETYPE_MAP, 4096);
		$imagick->readImage($imageData->getRealPath());

		$path = public_path('/tempPDFGenerate');
		if (!file_exists($path)) {
			mkdir($path, 0755, true); // true = recursive
		}

		$pdf = new PDF(null, 'mm','legel');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		$pdf->SetMargins(0, 0, 0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetAutoPageBreak(false, 0);

		$a4Width = 210;
		$a4Height = 297;
		$dpi = 300;

		foreach ($imagick as $i => $page) {
			// Transform colorspace to sRGB to prevent color inversion
			$page->transformImageColorspace(\Imagick::COLORSPACE_SRGB);

			// Set white background color before removing transparency
			$page->setImageBackgroundColor(new \ImagickPixel('white'));

			// Ensure image format is PNG with 8-bit depth
			$page->setImageFormat('png');
			// $page->setImageDepth(8);

			// Flatten alpha channel onto white background (removes transparency)
			// $page->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);

			// Create a new white canvas and composite the processed image
			$flattened = new \Imagick();
			$flattened->newImage($page->getImageWidth(), $page->getImageHeight(), new \ImagickPixel('white'));
			// $flattened->setImageColorspace(\Imagick::COLORSPACE_SRGB);
			// $flattened->setImageFormat('png');
			$flattened->compositeImage($page, \Imagick::COMPOSITE_DEFAULT, 0, 0);

			// Save to temp folder
			$uniqueId = uniqid();
			$tmpPath = $path . "/page_{$i}_{$uniqueId}.png";
			$flattened->writeImage($tmpPath);

			list($imgWidthPx, $imgHeightPx) = getimagesize($tmpPath);

			// Convert pixels to mm based on 300 DPI
			$imgWidthMm = ($imgWidthPx / $dpi) * 25.4;
			$imgHeightMm = ($imgHeightPx / $dpi) * 25.4;

			$imgRatio = $imgWidthMm / $imgHeightMm;
			$pageRatio = $a4Width / $a4Height;

			if ($imgRatio > $pageRatio) {
				$width = $a4Width;
				$height = $a4Width / $imgRatio;
			} else {
				$height = $a4Height;
				$width = $a4Height * $imgRatio;
			}

			$orientation = ($width > $height) ? 'L' : 'P';
			$pdf->AddPage($orientation);

			$x = ($a4Width - $width) / 2;
			$y = ($a4Height - $height) / 2;

			$pdf->Image($tmpPath, $x, $y, $width, $height, 'PNG', '', '', false, $dpi, '', false, false, 0, false, false, false);

			// Optionally delete temporary file after adding to PDF
			unlink($tmpPath);

			// Clear memory
			$flattened->clear();
			$flattened->destroy();
		}

		$imagick->clear();
		$imagick->destroy();
		$uniqueId = uniqid();

		$outputPath = public_path('/').'/tempPDFGenerate/generated_pdf_'.$uniqueId.'.pdf';

		$pdf->Output($outputPath, 'F');
		$name = uniqid() . time() . '.' . $imageData->getClientOriginalExtension();
		$destination1 = public_path('patientdocument');
		$destination2 = public_path('patientWriteDocument');

		$mimeType = $imageData->getMimeType();

		$contain = file_get_contents($outputPath);
		if (env('FILE_UPLOAD_PERMISSION') == 'development') {

			file_put_contents($destination1 . '/' . $name, $contain);
			\File::copy($destination1 . '/' . $name, $destination2 . '/' . $name);
			$image = $name;
		} else {
			Storage::disk('s3')->put('patientdocument/' . $name, $contain);
			Storage::disk('s3')->put('patientWriteDocument/' . $name, $contain);

			$image = $name;
		}

		unlink($outputPath);
		return $image;
	}

	public function documentOtherPdfRegenerateOld($requestData){
		ini_set('memory_limit', '1024M');
		$imageData = $requestData['images'];
		$imagick = new \Imagick();
		$imagick->setResolution(300, 300);
		$imagick->readImage($imageData->getRealPath());

		$path = public_path('/tempPDFGenerate');
		if (!file_exists($path)) {
			mkdir($path, 0755, true); // true = recursive
		}
		$pdf = new PDF(null, 'mm','legel');


		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		$pdf->SetMargins(0, 0, 0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetAutoPageBreak(false, 0);

		$a4Width = 210;
		$a4Height = 297;
		$dpi = 300;

		foreach ($imagick as $i => $page) {
			// Ensure image format is PNG
			$page->setImageFormat('png');

			// Flatten image onto white background to eliminate transparency
			$flattened = new \Imagick();
			$flattened->newImage($page->getImageWidth(), $page->getImageHeight(), 'white', 'png');
			$flattened->compositeImage($page, \Imagick::COMPOSITE_OVER, 0, 0);

			// Save to temp folder
			$uniqueId = uniqid();
			$tmpPath = $path . "/page_{$i}_{$uniqueId}.png";
			$flattened->writeImage($tmpPath);

			list($imgWidthPx, $imgHeightPx) = getimagesize($tmpPath);

			// Convert pixels to mm based on 300 DPI
			$imgWidthMm = ($imgWidthPx / $dpi) * 25.4;
			$imgHeightMm = ($imgHeightPx / $dpi) * 25.4;

			$imgRatio = $imgWidthMm / $imgHeightMm;
			$pageRatio = $a4Width / $a4Height;

			if ($imgRatio > $pageRatio) {
				$width = $a4Width;
				$height = $a4Width / $imgRatio;
			} else {
				$height = $a4Height;
				$width = $a4Height * $imgRatio;
			}

			$orientation = ($width > $height) ? 'L' : 'P';
			$pdf->AddPage($orientation);

			$x = ($a4Width - $width) / 2;
			$y = ($a4Height - $height) / 2;

			$pdf->Image($tmpPath, $x, $y, $width, $height, 'PNG', '', '', false, $dpi, '', false, false, 0, false, false, false);

			// Optionally delete temporary file after adding to PDF
			unlink($tmpPath);

			// Clear memory
			$flattened->clear();
			$flattened->destroy();
		}

		$imagick->clear();
		$imagick->destroy();
		$uniqueId = uniqid();



		$outputPath = public_path('/').'/tempPDFGenerate/generated_pdf_'.$uniqueId.'.pdf';

		$pdf->Output($outputPath, 'F');


		$name = uniqid() . time() . '.' . $imageData->getClientOriginalExtension();
		$destination1 = public_path('patientdocument');
		$destination2 = public_path('patientWriteDocument');

		$mimeType = $imageData->getMimeType();

		$contain = file_get_contents($outputPath);
		if (env('FILE_UPLOAD_PERMISSION') == 'development') {

			file_put_contents($destination1 . '/' . $name, $contain);
			\File::copy($destination1 . '/' . $name, $destination2 . '/' . $name);
			$image = $name;
		} else {
			Storage::disk('s3')->put('patientdocument/' . $name, $contain);
				Storage::disk('s3')->put('patientWriteDocument/' . $name, $contain);

			$image = $name;
		}

		unlink($outputPath);
		return $image;
	}

	public function testSampleData(){

		return [];
	}

	private function sendInflowcareWebhook($patientId, $documentId = ""){
		$user = auth()->user();
		$details = $this->PatientService->getDetailById($patientId);

		$final = [
			'appointment_id'=>$details->id,
			'medical_type'=>$details->type,
			'patient_code'=>$details->patient_code,
			'status'=>$details->status
		];

		$getDocument = $documentId
        ? $this->DocumentPatientService->getDocumentByDocIdAndPatientId($patientId, $documentId)
        : $this->DocumentPatientService->getLastDocumentByPatientId($patientId);

		if(env('FILE_UPLOAD_PERMISSION') !="development"){
			$file = Storage::disk('s3')->temporaryUrl($getDocument->attachment, now()->addMinutes(5));
		}else{
			$file = public_path('/').'/patientdocument/'.$getDocument->attachment;
		}

		$documentArray = [
			'document_id'=>$getDocument->id,
			'document_name'=>$getDocument->document_name,
			'document_status'=>"Completed",
			'document_link'=>$file
		];

		$final['documents'] = [$documentArray];
		ThirdPartyWebHookHelper::sendToInflowcareWebHook($final);
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Status Changed - Document Sent to Inflowcare',
			'link' => url('/patient/statusUpdate'),
			'module' => 'Patient Appointment',
			'object_id' => $details->id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has sent the document to Inflowcare successfully.',
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		return 1;
	}

	public function sendToInflowcare(Request $request){
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patientId' => 'required',
			'documentId' => 'required',
		]);

		if ($validator->fails()) {
			return $this->validationErrorResponse($validator);
		}

		$response = $this->sendInflowcareWebhook($request->patientId,$request->documentId);

		$errorMessage = $response['response']['detailed']??$response['response']['message']??"";
		if($response['statusCode'] ==400 && isset($response['response']['errors'])){
			$errors = $response['response']['errors'];
			if (!empty($errors)) {
				foreach ($errors as $field => $msgs) {
					$errorMessage .= $field . ': ' . implode(', ', $msgs) . ' ';
				}
			}
		}

		$isSuccess = $response['statusCode'] >= 200 && $response['statusCode'] < 300;
		if($isSuccess){
			$this->DocumentPatientService->update(array('send_third_party'=>1,'send_third_party_date'=>date('Y-m-d H:i:s')),array('id'=>$request->documentId,'patient_id'=>$request->patientId));
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Send Document To Inflowcare',
				'link' => url('/patient/send-document-inflowcare'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patientId,
				'message' => $user->first_name . ' ' . $user->last_name . ' has sent the document to Inflowcare successfully.',
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
		}
		return response()->json(['status' => $isSuccess, 'error_msg' => $errorMessage], $response['statusCode']);
	}

	private function convertMergePatientArray($mergeIds,$currentId){
		$mergeData = ['merge_id'=>$mergeIds,'currentId'=>$currentId,'del_flag'=>"N"];
		return MergeUtilityHelper::convertData($mergeData);
	}

	public function loadApointmentList(Request $request){
		$id = $request->id;
		$record = $this->PatientService->getDetailByIdNew($id);
		$ids = [$id];
		if ($record->merge_appointment_id != "") {
			$ids = $this->convertMergePatientArray($record->merge_appointment_id,$id);
		}

		$data['record'] = $record;
		$pastAppointment = Appointment::getPastAppointmentListNewAll($ids);
		$servie = [];
		if (count($pastAppointment) > 0) {
			foreach ($pastAppointment as $key => $record) {
				if (isset($record->service_id) && $record->service_id != '') {
					$servicesIds = explode(',', $record->service_id);


					$services = Master::whereIn('id', $servicesIds)->where('del_flag', 'N')->get();
					$newass = array();
					foreach ($services as $kke) {
						$newass[] = $kke->name;
					}

					if (!empty($newass)) {
						$servie[$key] = implode(',', $newass);
					}
				}

				if (!empty($record->telehealth_time_frame)) {
					$record->telehealth_time_slot = $record->telehealth_time_frame;
				} elseif (isset($record->telehealth_time_slot) && $record->telehealth_time_slot != "") {
					$telhealth = $this->telehealthLocationScheduleEventService->getTelehalthappointemntScheduledata($record->telehealth_time_slot);
					if (isset($telhealth['start_time']) && isset($telhealth['end_time'])) {
						$record->telehealth_time_slot = $telhealth['start_time'] . ' - ' . $telhealth['end_time'];
					}
				}
			}
		}
		$data['servie'] = $servie;
		$data['pastAppointment'] = $pastAppointment;
		return view("patient/_partial/appointments/appointments_ajax_list", $data);
	}

    public function updatePharmacy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => 0,
                'data' => []
            ], 422);
        }

        $user = auth()->user();
        // OLD DATA
        $oldData = $this->PatientService->getDetailById($request->patient_id);

        $updateData = [];
        // 👉 CHECK WHICH FIELD IS SENT

        if(isset($request->pharmacy_name)){
            $updateData['pharmacy_name'] = $request->pharmacy_name ?? null;
            $message = $user->first_name . ' ' . $user->last_name . ' has updated pharmacy name.';
            $type = 'Update Pharmacy Name';
            $msg = 'Updated Pharmacy Name.';
        }

        if(isset($request->pharmacy_no)){
            $updateData['pharmacy_no'] = $request->pharmacy_no ?? null;
            $message = $user->first_name . ' ' . $user->last_name . ' has updated pharmacy number.';
            $type = 'Update Pharmacy Number';
            $msg = 'Updated Pharmacy Number.';
        }

        $this->PatientService->update($updateData, ['id' => $request->patient_id]);
        $updateData['id'] = $request->patient_id;
        $old_response = $oldData ? $oldData->toArray() : [];

        // 👉 LOG
        $logData = [
            'type' => $type,
            'link' => url('/patient/updatePharmacy'),
            'module' => 'Patient Appointment',
            'object_id' => $request->patient_id,
            'message' => $message,
            'old_response' => serialize($old_response),
            'new_response' => serialize($updateData),
            'ip' => Utility::getIP(),
        ];

        LogsService::save($logData);

        try {
            if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                $agencyNotifyData = array(
                    'agencyid' => $oldData->agency_id,
                    'title' => $msg,
                    'record_id' => $request->patient_id,
                    'record_type' => 'Appointment',
                    'msg' => '',
                    'res_data' => serialize($updateData),
                );
                Common::insertAgencyNotificationsOfUser($agencyNotifyData);
            }
        } catch (\Throwable $th) {}

        return response()->json([
            'error_msg' => "Pharmacy details successfully updated",
            'status' => 1,
            'data' => []
        ], 201);
    }

	public function exportCsv(Request $request){
		$user = auth()->user();
		$selectedColumnsJson = $request->columns;
		$selectedColumns = [];
		if ($selectedColumnsJson) {
			$selectedColumns = json_decode($selectedColumnsJson, true);
		}
	
		[$columns, $dbFields, $allColumns, $columnIndexMap] = $this->optimizeFields($selectedColumns);

		$allStatusIds = [];
		if (!empty($request->agency_status)) {
			$field_data = $this->fieldMasterService->getAgencyStatusData();
			foreach ($field_data as $item) {
				$allStatusIds[] = $item['id'];
			}
		}

		// Returns query builder (not collection) so we can chunk
		$query = $this->PatientService->getDataExportLatest($request->all(), $dbFields, $allStatusIds);

		// Pre-load lookup data to avoid N+1 queries
		$allAgencyNames = $this->agencyService->getAllAgencyList();
		$allServiceNames = $this->masterService->getAllName();
		$userDetails = $this->userService->getAllUserUsingPluck();
		$locations = $this->LocationMasterService->getAllLocationUsingPluck();
		$locationSchedule = $this->LocationScheduleService->getAllLocationSchedule();
		$languageMap = $this->languageService->getAllLanguagesById();
		// Build column-name to db-field mapping
	
		$filename = 'Patient' .Utility::convertDateStaticUseExportOtherFile();
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);
	
		$allArrayData = [
			'agencies'=>$allAgencyNames,
			'services'=>$allServiceNames,
			'users'=>$userDetails,
			'locations'=>$locations,
			'locationSchedule'=>$locationSchedule,
			'languageMap'=>$languageMap
		];
		
		$callback = function () use ($query, $columns, $columnIndexMap, $user, $allArrayData) {
			if (ob_get_level() > 0) {
				ob_clean();
			}
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
		

			$query->chunk(1000, function ($patients) use ($file, $columns, $columnIndexMap, $user, $allArrayData) {
				
				foreach ($patients as $list) {
					
					$rowData = $this->buildExportRow($list,  $user,$allArrayData);

					if (!empty($columnIndexMap)) {
						$filteredData = [];
						foreach ($columns as $column) {
							if (isset($columnIndexMap[$column]) && isset($rowData[$columnIndexMap[$column]])) {
								$filteredData[] = $rowData[$columnIndexMap[$column]];
							} else {
							
								$filteredData[] = '';
							}
						}
						fputcsv($file, $filteredData);
					} else {
						fputcsv($file, $rowData);
					}
				}
				flush();
			});

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}

	private function buildExportRow($list, $user, $allArrayData){

		$allAgencyNames = $allArrayData['agencies'];
		$allServiceNames = $allArrayData['services'];
		$allUser = $allArrayData['users'];
		$languageMap = $allArrayData['languageMap'];
		$agencyName = $allAgencyNames[$list->agency_id] ?? "";

		$date = '';
		if ($list->dob != '0000-00-00' && $list->dob != '') {
			$date = Utility::convertMDY($list->dob);
		}

		$Adate = '';
		if ($list->appointment_date != '0000-00-00 00:00:00' && $list->appointment_date != '') {
			$Adate = Utility::convertMDY($list->appointment_date);
		} else if ($list->telehealth_date_time != '0000-00-00 00:00:00' && $list->telehealth_date_time != '') {
			$Adate = Utility::convertMDY($list->telehealth_date_time);
		}

		$ATime = '';
		$startTime = $allArrayData['locationSchedule'][$list->appoinment_time_id]??"";
		if ($startTime != '') {
			$ATime = date('h:i A', strtotime($startTime));
		} else if (!empty($list->telehealth_time_slot)) {
			$telhealth = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($list->telehealth_time_slot);
			if ($telhealth) {
				$ATime = date('h:i A', strtotime($telhealth['start_time'])) . '-' . date('h:i A', strtotime($telhealth['end_time']));
			}
		}

		$servie = '';
		if (isset($list->service_id) && $list->service_id != '') {
			$serviceIds = array_filter(explode(',', $list->service_id));
			$serviceNamesList = [];
			foreach ($serviceIds as $sid) {
				if (isset($allServiceNames[$sid])) {
					$serviceNamesList[] = $allServiceNames[$sid];
				}
			}
			$servie = implode(',', $serviceNamesList);
		}

		$assignName = '';
		if (!empty($list->assign_user_id)) {
			$assignName = $allUser[$list->assign_user_id]??"";
		}

		$created_by_username = $allUser[$list->created_by]??"";

		$created_date = '';
		if ($list->created_date != "" && $list->created_date != NULL) {
			$created_date = Utility::convertMDYTime($list->created_date);
		}

		$due_date = '';
		if ($list->due_date != "" && $list->due_date != NULL && $list->due_date != '1969-12-31') {
			$due_date = Utility::convertMDY($list->due_date);
		}

		$fu_date = '';
		if ($list->fu_date != "" && $list->fu_date != NULL && $list->fu_date != "1969-12-31") {
			$fu_date = Utility::convertMDY($list->fu_date);
		}

		$isArchive = ($list->archived_at != '') ? 'Yes' : 'No';

		$completedDate = '';
		if ($list->completed_date != '') {
			$completedDate = Utility::convertMDY($list->completed_date);
		}

		$followUpDate = '';
		if ($list->follow_date != '') {
			$followUpDate = Utility::convertMDY($list->follow_date);
		}

		$trainingDate = '';
		if (!empty($list->traning_due_date)) {
			$trainingDate = Utility::convertMDY($list->traning_due_date);
		}

		$lastStatusUpdated = '';
		if ($list->last_status_update != '' && $list->last_status_update != "0000-00-00 00:00:00") {
			$lastStatusUpdated = Utility::convertMDYTime($list->last_status_update);
		}

		$reasonStatus = "";
		if ($list->reason_id != "") {
			if(in_array(strtolower( $list->status),['cancelled','refused'])){
				$reasonStatus = $allServiceNames[$list->reason_id]??"";
			}
		}

		
		$patientFullName = trim($list->first_name . ' ' . ($list->middle_name ?? '') . ' ' . $list->last_name);
		$patientFullName = preg_replace('/\s+/', ' ', $patientFullName);

		$status = $list->status;
		if (strtolower($status) == 'inactive') {
			$status = 'Inactive';
		}

		$locationName = $allArrayData['locations'][$list->location_id] ?? '';

		if ($user->agency_fk == 106) {
			return array($list->id, $agencyName, $list->type, $list->diciplin, $list->patient_code, $patientFullName, $list->phone ?? $list->mobile, $list->gender, $date, $locationName, $Adate, $ATime, $servie, $status, $list->remarks, $list->appointment_mode, $assignName, $created_date, $created_by_username, $due_date, $fu_date, $isArchive, $list->training_status, $completedDate, $followUpDate, $trainingDate, $list->location_branch, $reasonStatus);
		}

		$allData = array($list->id, $agencyName, $list->type, $list->diciplin, $list->patient_code, $patientFullName, $list->phone ?? $list->mobile, $list->gender, $date, $locationName, $Adate, $ATime, $servie, $status, $list->remarks, $list->appointment_mode, $assignName, $created_date, $created_by_username, $due_date, $fu_date, $isArchive, $completedDate, $followUpDate, $list->location_branch, $reasonStatus, $list->state,$languageMap[$list->language]??"");
		if($user->user_type_fk ==184){
			$telehealth_nurse = "";
			if($list->telehealth_nurse !=""){
				$telehealth_nurse = "C#".$list->telehealth_nurse;
			}
			$allData[] = $telehealth_nurse;
			$allData[] = $trainingDate;
			$allData[] = $list->training_status??"";
			$allData[] = $lastStatusUpdated;
			$statusUpdatedByName = $allUser[$list->last_status_update_by]??"";
			$allData[] = $statusUpdatedByName;
			$allData[] = $list->referral_type;
			$allData[] = $allUser[$list->agency_user_id]??"";
		}
		
		return $allData;
	}

	private function optimizeFields($selectedColumns){
		$user = auth()->user();
		$fields = PatientModuleHelper::createColumnWiseFields();
		$allColumns = [];
		if ($user->agency_fk == 106) {
			$allColumns = array('Portal Id', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob', 'Location', 'Appointment Date', 'Appointment Start Time', 'Service', 'Status', 'Notes', "Booked Via", "Assign NyBest User", "Created Date", "Created By", 'Due Date', 'FU Date', 'Is Archive', 'Training Status', 'Completed date', 'Follow Up Date', 'Traning Due Date', 'Location / Branch', 'Reason');
		} else {
			$allColumns = array('Portal Id', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob', 'Location', 'Appointment Date', 'Appointment Start Time', 'Service', 'Status', 'Notes', "Booked Via", "Assign NyBest User", "Created Date", "Created By", 'Due Date', 'FU Date', 'Is Archive', 'Completed date', 'Follow Up Date', 'Location / Branch', 'Reason','state');
			if ($user->user_type_fk == 184) {
				$allColumns[] = 'Training Date';
				$allColumns[] = 'Training Status';
				$allColumns[] = 'Last Status Update Date';
				$allColumns[] = 'Last Status Updated By';
				$allColumns[] = 'Referral Type';
				
			}
			$allColumns[] = 'Language';
				$allColumns[] = 'Clinician Code';
				$allColumns[] = 'Agency Rep';
		}

		// Use selected columns if provided, otherwise use all columns
		$columns = !empty($selectedColumns) ? $selectedColumns : $allColumns;

		// Create column index map for filtering data
		$columnIndexMap = [];
		$useFiltering = !empty($selectedColumns) && count($selectedColumns) < count($allColumns);
		$dbFields = [];
		if ($useFiltering) {
			foreach ($columns as $column) {
				$index = array_search($column, $allColumns);
				
				if ($index !== false) {
					
					$columnIndexMap[$column] = $index;
				}

				if($column !=""){
					$dbFields[] = $fields[$column];
		
				}
			}
		}else{
			foreach ($columns as $column) {
				if($column !=""){
					if(isset($fields[$column])){
						$dbFields[] = $fields[$column]??"";
					}
				}
			}
		}

		return [$columns, $dbFields, $allColumns, $columnIndexMap];
	}

	public function updateNoMedicationTaken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => 0,
                'data' => []
            ], 422);
        }

        $user = auth()->user();
        $oldData = $this->PatientService->getDetailById($request->patient_id);

        $value = $request->no_medication_taken ? 1 : 0;
        $this->PatientService->update(['no_medication_taken' => $value], ['id' => $request->patient_id]);

        $logData = [
            'type' => 'Update No Medication Taken',
            'link' => url('/patient/updateNoMedicationTaken'),
            'module' => 'Patient Appointment',
            'object_id' => $request->patient_id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has updated no medication taken.',
            'old_response' => serialize($oldData->toArray()),
            'new_response' => serialize(['id' => $request->patient_id, 'no_medication_taken' => $value]),
            'ip' => Utility::getIP(),
        ];

        LogsService::save($logData);

        try {
            if (isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk)) {
                $agencyNotifyData = [
                    'agencyid' => $oldData->agency_id,
                    'title' => 'Updated No Medication Taken.',
                    'record_id' => $request->patient_id,
                    'record_type' => 'Appointment',
                    'msg' => '',
                    'res_data' => serialize(['id' => $request->patient_id, 'no_medication_taken' => $value]),
                ];
                Common::insertAgencyNotificationsOfUser($agencyNotifyData);
            }
        } catch (\Throwable $th) {}

        return response()->json([
            'error_msg' => 'No Medication Taken updated successfully.',
            'status' => 1,
            'data' => []
        ], 200);
    }

    public function markPatientReviewed(Request $request)
    {
        $patientIds = explode(',', $request->input('patient_id', ''));
        $patientIds = array_filter(array_map('trim', $patientIds));
        $ip = Utility::getIP();

        foreach ($patientIds as $patientId) {
			$this->PatientService->update(['is_reviewed' => 1], array('id' => $patientId));
            LogsService::save([
                'type'         => 'Patient Reviewed',
                'link'         => url('/patient/view/' . $patientId),
                'module'       => 'Patient Appointment',
                'object_id'    => $patientId,
                'message'      => auth()->user()->first_name.' '.auth()->user()->last_name.' has Marked as reviewed',
                'new_response' => serialize(['is_reviewed' => 1]),
                'ip'           => $ip,
            ]);
        }

        $count = count($patientIds);
        $msg = $count > 1 ? $count . ' patients marked as reviewed.' : 'Patient marked as reviewed.';
        return response()->json(['success' => true, 'error_msg' => $msg], 200);
    }

    public function markPatientUnreviewed(Request $request)
    {
        $patientId = $request->input('patient_id');
		$this->PatientService->update(['is_reviewed' => 0], array('id' => $patientId));
        LogsService::save([
            'type'         => 'Patient Unreviewed',
            'link'         => url('/patient/view/' . $patientId),
            'module'       => 'Patient Appointment',
            'object_id'    => $patientId,
            'message'      => auth()->user()->first_name.' '.auth()->user()->last_name.' has Marked as unreviewed',
            'new_response' => serialize(['is_reviewed' => 0]),
            'ip'           => Utility::getIP(),
        ]);

        return response()->json(['success' => true, 'error_msg' => 'Patient marked as unreviewed.'], 200);
    }
}