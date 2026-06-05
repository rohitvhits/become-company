<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Agency;
use App\Master;
use App\Helpers\Common;
use App\Model\Language;

use App\Model\Appointment;

use App\Services\SmsService;
use Illuminate\Http\Request;

use App\Services\LogsService;

use App\Services\PatientService;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Services\DocumentPatientService;
use App\Services\InsuranceMasterService;

use Illuminate\Support\Facades\Validator;

use App\Services\SendEmailNotificationSerivce;

use Illuminate\Routing\Controller as BaseController;

use App\Services\UserWiseAgencyService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Helpers\Utility;
use App\Services\TaskService;
use App\Services\NotificationUserService;
use App\Model\TaskLog;
use Illuminate\Support\Facades\Cache;
use App\Services\UserCreatorEmailNotificationService;
use Exception;
use App\Helpers\DateWiseAgencyAccessHelper;
use App\Services\BranchListService;
use App\Services\AgencyService;
use App\Helpers\ResolutionSmsHelper;
use App\Services\AutoCallService;
use App\Services\AssignNyBestUserService;
use App\Services\SiteSettingServices;

class CreatePatientController extends BaseController
{
	protected $patientService, $insuranceMasterService, $userWiseAgencyService, $smsService, $patientServicesRequest, $patientWiseServicesRequests, $sendEmailNotificationSerivce, $documentPatientService,$taskService,$notificationUserService,$userCreatorEmailNotificationService,$branchListService,$branchPatientService,$agencyService = "";
	protected $assignNyBestUserService;
	protected $siteSettingService;

	public function __construct(PatientService $patientService, InsuranceMasterService $insuranceMasterService, UserWiseAgencyService $userWiseAgencyService, SmsService $smsService, PatientServicesRequest $patientServicesRequest, PatientWiseServicesRequests $patientWiseServicesRequests, SendEmailNotificationSerivce $sendEmailNotificationSerivce, DocumentPatientService  $documentPatientService,TaskService $taskService,NotificationUserService $notificationUserService,UserCreatorEmailNotificationService $userCreatorEmailNotificationService, BranchListService $branchListService, AgencyService $agencyService,AssignNyBestUserService $assignNyBestUserService,SiteSettingServices $siteSettingService)
	{
		$this->patientService = $patientService;
		$this->insuranceMasterService = $insuranceMasterService;
		$this->userWiseAgencyService = $userWiseAgencyService;
		$this->smsService = $smsService;
		$this->patientServicesRequest = $patientServicesRequest;
		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
		$this->sendEmailNotificationSerivce = $sendEmailNotificationSerivce;
		$this->documentPatientService = $documentPatientService;
		$this->taskService = $taskService;
		$this->notificationUserService = $notificationUserService;
		$this->userCreatorEmailNotificationService = $userCreatorEmailNotificationService;
		$this->branchListService = $branchListService;
		$this->agencyService = $agencyService;
		$this->assignNyBestUserService = $assignNyBestUserService;
		$this->siteSettingService = $siteSettingService;
	}

	public function add(Request $request)
	{
		$data['menu'] = "Add Patient";
		$data['user'] = $user = auth()->user();
		$agencyObj = Common::getAgencyDetails();
		if (isset($agencyObj->service_md_appointment) && $agencyObj->service_md_appointment == 0) {

			return redirect('support_error');
		}
		$data['serviceList'] = Master::getServiceRequest();
		$data['agencyList'] = Agency::getAgencyList();
		$data['masterData'] = Master::getAllDataByMasterTypeFk(array(17, 26,31));
		$data['languages'] = Language::getLanguageList();
		$data['insuranceList'] = $this->insuranceMasterService->getInsuranceMasterList();
		
		$data['userAgencyList'] = $this->userWiseAgencyService->getAgencyListByUserId($user->id);

		if($user->agency_fk !=""){
			$getAgencyAccess = DateWiseAgencyAccessHelper::getDateWiseAgencyAccess();
			if(in_array('AddAppointment',$getAgencyAccess)){
				abort(404);
			}
		}
		
		return view("patient/patient_add_new", $data);
	}

	public function createNew(Request $request)
	{
		$data['menu'] = "Add Patient";
		$data['user'] = $user = auth()->user();
		$agencyObj = Common::getAgencyDetails();
		if (isset($agencyObj->service_md_appointment) && $agencyObj->service_md_appointment == 0) {

			return redirect('support_error');
		}
		$data['serviceList'] = Master::getServiceRequest();
		$data['agencyList'] = Agency::getAgencyList();
		$data['masterData'] = Master::getAllDataByMasterTypeFk(array(17, 26,31));
		
		$data['languages'] = Language::getLanguageList();
		$data['insuranceList'] = $this->insuranceMasterService->getInsuranceMasterList();

		$data['userAgencyList'] = $this->userWiseAgencyService->getAgencyListByUserId($user->id);

		if(isset($_GET['debug']) && $_GET['debug']==1){
			echo "<pre>";print_r($data['agencyList']);die();
		}
		
		return view("patient/patient_add_new_create", $data);
	}

	public function searchPatientList(Request $request)
	{

		$response = $this->patientService->searchPatientDetailsNew($request->all());
		$final = [];
		if (!empty($response[0])) {
			foreach ($response as $val) {
				$date = "";
				if ($val->dob != "") {
					$date = date('m/d/Y', strtotime($val->dob));
				}
				$temp = [];
				$temp['id'] = $val->id;
				$temp['name'] = $val->id . ' - ' . $val->first_name . ' ' . $val->last_name . ' - ' . $val->mobile . ' - ' . $val->type . ' - ' . $date . ' - ' . $val->agencyDetail->agency_name . ' - ' . $val->status;
				$temp['type'] = $val->type;
				$final[] = $temp;
			}
		}

		return json_encode($final);
	}

	public function savePatientDetails(Request $request)
	{
	
		$user = auth()->user();
		if(isset($request->redirection) && $request->redirection =='normal'){
			$validator = Validator::make($request->all(), [
				'first_name' => 'required',
				'type' => 'required',
				'last_name' => 'required',
				'mobile' => 'required|numeric|digits_between:10,15',
				'agency_id' => 'required',
				'dob' => 'required',
				'mobile' => 'required',
				'gender' => 'required',
				'create_service_id' => 'required',
	//			'referral_type' => 'required',
			]);
		}else{
			$validator = Validator::make($request->all(), [
				'first_name' => 'required',
				'type' => 'required',
				'last_name' => 'required',
				'mobile' => 'required|numeric|digits_between:10,15',
				'agency_id' => 'required',
				'dob' => 'required',
				'mobile' => 'required',
				'gender' => 'required',
	//			'referral_type' => 'required',
			]);
		}
		

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$age = '';
			if ($request->dob != '') {
				$age = date('Y-m-d', strtotime($request->dob));
			}

			$fu_date = null;
			if (isset($request->fu_date) && $request->fu_date != '') {
				$fu_date = date('Y-m-d', strtotime($request->fu_date));
			}

			$due_date = null;
			if (isset($request->due_date) && $request->due_date != '') {
				$due_date = date('Y-m-d', strtotime($request->due_date));
			}

			$smsMobileArray = [];
			$smsMobileArray[] = str_replace([ "(", ")",'-'," " ], "", $request->mobile);
			if($request->phone !=""){
				$smsMobileArray[] = str_replace([ "(", ")",'-'," " ], "", $request->phone);
			}
			$branch_name = "";
			if(isset($request->branch_id) && !empty($request->branch_id)){
				$branchData = $this->branchListService->getById($request->branch_id);
				$branch_name = $branchData->branch_name;
			}
			$data = array(
				'first_name' => $request->first_name,
				'middle_name' => $request->middle_name,
				'last_name' => $request->last_name,
				'full_name' => $request->first_name.' '.$request->last_name,
				'type' => $request->type,
				'dob' => $age,
				'phone' => str_replace([ "(", ")",'-'," " ], "", $request->phone),
				'mobile' => str_replace([ "(", ")",'-'," " ], "", $request->mobile),
				'agency_id' => $request->agency_id != null ? $request->agency_id : $user['agency_fk'],
				'gender' => $request->gender,
				'patient_code' => $request->patient_code,
				'remarks' => $request->message,
				'language' => $request->language,
				'address1' => $request->address1,
				'address2' => $request->address2,
				'state' => $request->state,
				'city' => $request->city,
				'zip_code' => $request->zip_code,
				'county' => $request->county,
				'cin' => trim($request->cin),
				'record_read' => 0,
				'ssn' => $request->ssn,
				'email' => $request->email,
				'diciplin' => $request->diciplin,
				'payment_type' => $request->payment_type,
				'insurance_id' => $request->insurance_id,
				'insurance_name' => $request->insurance_name,
				'emergency_contact_name' => $request->emergency_contact_name,
				'emergency_phone' => $request->emergency_phone,
				'location_branch' => !empty($branch_name) ? $branch_name : $request->location_branch,
				'transition_aid' => $request->transition_aid,
				'medicare_no' => $request->medicare_no,
				'fu_date' => $fu_date,
				'due_date' =>$due_date,
				'referral_type'=>$request->referral_type,
				'branch_id' => $request->branch_id??NULL,
				'last_status_update' =>date('Y-m-d H:i:s'),
				'last_status_update_by' =>$user->id,
				'link_hha_caregiver' => strtolower($request->type) == 'caregiver' && !empty($request->caregiver_id) ? $request->caregiver_id : NULL, 
				'link_hha_patient' => strtolower($request->type) == 'patient' && !empty($request->caregiver_id) ? $request->caregiver_id : NULL,
				'agency_user_id'=>$request->agency_user_ids
			);
			if(!empty($request->create_service_id[0])){
				$data['service_id'] = implode(',',$request->create_service_id);
				if($request->type == 'Patient'){
					$data['status'] = Utility::getStatusFromServiceId($request->create_service_id);
				}
			}
			$other_name = "";
			if ($request->gender == 'other') {
				$other_name = $request->other_name;
			}
			$data['other_gender'] = $other_name;
			
			if ($request->input('insurance_name') == 'other') {
				$data['other_insurance_name'] = $request->other_insurance_name;
			}
			
			$save = $this->patientService->save($data);
			
			if ($save) {
				
				$query = $this->patientService->getPatientId($save);
				if ($query->dob != "") {
					$query->dob = date('m/d/Y', strtotime($query->dob));
				}
				$serviceRequestStatus = 'Pending';
				if($request->type == 'Patient'){
					$serviceRequestStatus = $data['status'];
				}
				$patientServiceLastId = $this->patientServicesRequest->save([
					'patient_id' => $save,
					'follow_up_date' => $request->fu_date,
					'due_date' => $request->due_date,
					'status' => $serviceRequestStatus
				]);

				$addServiceIds = $request->create_service_id;

				if (is_array($addServiceIds)) {
					foreach ($addServiceIds as $serviceId) {
						if($serviceId !=""){
							$patientWiseServiceRequest = [
								'patient_id' => $save,
								'service_id'=> $serviceId,
								'patient_service_request_id' => $patientServiceLastId,
							];
							$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
						}
					}
				}

				$addAppintment = ["patient_id" => $save, "location_id" => null, "doctor_id" => null, "service_id" => implode(',',$request->create_service_id), "appointment_date" => null, "appointment_time" => null, "status" => "Pending", "created_by" => $user->id, 'created_at' => date('Y-m-d H:i:s')];
				Appointment::create($addAppintment);

				$getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
				$agencyname = '';
				if (isset($getAgencyName->agency_name) && $getAgencyName->agency_name != '') {
					$agencyname = $getAgencyName->agency_name;
				}
					$appointmentId=$save;
				if ($request->type == 'Caregiver') {

					$unitId = uniqid();
					$url = URL::to('/') . '/ap/' . $unitId;
					$namearray = array();
					foreach ($addServiceIds as $vdl) {
						$getMaster = Master::select('name')->where('id', $vdl)->where('del_flag', 'N')->first();
						$namearray[] = $getMaster->name;
					}
					$disabledServiceIds = Common::getServiceMesgdisable();
					$hasDisabledService = count(array_intersect($request->create_service_id, $disabledServiceIds)) > 0;
					
					if ($hasDisabledService) {
						$extraMessageEn = '';
						$extraMessageSp = '';
					} else {
						$extraMessageEn = ' and you will need to update it to continue employment and be active with ' . $agencyname;
						$extraMessageSp = ' y deberá actualizarlo para continuar trabajando y permanecer activo con ' . $agencyname;
					}

					if ($query->language != '' && strtolower($query->language) == 'spanish') {
						$message = 'Aviso de ' . $agencyname . ': Usted tiene prevista una cita con el médico. Su '
									. implode(',', $namearray) . ' vencerán pronto' . $extraMessageSp
									. '. Haga clic en el enlace a continuación para programar su cita Enlace ' . $url
									. '.
									Para su referencia, su ID de cita es: ' . $appointmentId
									. '. No responda a este mensaje de texto, para cualquier pregunta, llame al (718) 972-3693';
					} else {
						$message = 'Notice from ' . $agencyname . ': Your ' . implode(',', $namearray)
									. ' expiring soon' . $extraMessageEn
									. '. Please click the link below to schedule your appointment with NYBest Medical Care ' . $url
									. '.
									For your reference, your Appointment ID is: ' . $appointmentId . '. 
									 Do not reply to this text message, for any questions please call NYBest Medical @ 718-972-3693 or email appointment@nybestmedical.com';
					}

					$data['sms'] = $message;
					$data['key'] = $unitId;
					$this->patientService->update(array('sms' => $message, 'key' => $unitId), array('id' => $query->id));
					$smsSendFlag = 0;
					if($data['agency_id'] == 2){
						$emailTest = 'Pinak@nybestmedical.com';
						$subject = 'Flu vaccine: Expiring soon';
						Mail::mailer('second')->send([], [], function ($messages) use ($emailTest, $subject, $message) {
							$messages->to($emailTest, "")
								->subject($subject)->html($message);
							}
						);
					}else{
						if ($getAgencyName->is_sms == 1) {
							$getStopSMSServices = Utility::stopSMSService($data['agency_id']);
							$array =array_intersect($request->create_service_id, $getStopSMSServices);
							if(count($array) ==0){
								if(count($smsMobileArray) >0){
									foreach($smsMobileArray as $mb){
										$smsSendFlag = $this->smsService->AgencyWiseSmsDynamic($save, $mb, $message);
									}
								}
							}
							if($smsSendFlag !=0){
								$this->patientService->update(array('patient_sms_flag'=>1), array('id' => $query->id));
							}

						}
					}
					// Schedule auto-call if no booking within 4 hours (only for agencies with AI Call Logs enabled)
					if (!empty($getAgencyName->ai_call_logs_enabled)) {
						AutoCallService::scheduleAfterSmsSent(
							$query->id,
							$smsMobileArray[0] ?? str_replace(["(",")","-"," "], "", $request->mobile),
							$query->first_name . ' ' . $query->last_name,
							$agencyname,
							$url ?? '',
							'savePatient',
							$unitId,
							$query->agency_id,
							$request->create_service_id
						);
					}
				}else if ($request->type == 'Patient') {
					if(ResolutionSmsHelper::getMDOServiceIds($request->create_service_id) && $getAgencyName->is_telehealth_send_sms == 1){
						if($query->medication_count == 0 && ($query->no_medication_taken != 1 || is_null($query->no_medication_taken))){
							$statusWiseSmsSend = ResolutionSmsHelper::statusWiseSmsSend("Require Medication List",$query->id);
							if(!empty($statusWiseSmsSend['message'])){
								$numbers = array_unique(array_filter([$statusWiseSmsSend['mobile'], $statusWiseSmsSend['phone']]));
								foreach($numbers as $number){
									$this->smsService->AgencyWiseSmsDynamic($query->id, $number, $statusWiseSmsSend['message']);
								}
							}
						}
					}
                }

				
				$patientDetailsNew = $this->patientService->getPatientId($query->id);
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add Appointment',
					'link' => url('/patient-add-new'),
					'module' => 'Patient Appointment',
					'object_id' => $save,
					'message' => $user->first_name . ' ' . $user->last_name . ' has added Appointment',
					'new_response' => serialize($patientDetailsNew->toArray()),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				try{
					if($request->type == 'Patient'){
						Utility::saveResolutionLogForms($serviceRequestStatus,$patientServiceLastId,$query->id);
					}
				}catch(Exception $e){}
				$portal_name = $patientDetailsNew->first_name . ' ' . $patientDetailsNew->last_name;
				
				try {
					Utility::insertNotificationsOfUserNew($patientDetailsNew->agency_id, $patientDetailsNew->id, $portal_name, $patientDetailsNew->type, $request->agency_id, $request->create_service_id);
				} catch (\Throwable $th) {
					//throw $th;
				}

				$emails = isset($getAgencyName->notification_email) ? $getAgencyName->notification_email : "";
				$allemails = array();
				if ($emails != '') {
					$newsemail = explode(',', $emails);
					if (count($newsemail) > 0) {
						foreach ($newsemail as $vas) {
							if (trim($vas) != '') {
								$allemails[] = $vas;
							}
						}
					}
				}
				$username = $user['first_name'] . ' ' . $user['last_name'];

				$discipline = $request->input('diciplin');

				$subject = "[" . $agencyname . "] NYBest Medical Care New record added";

				if($data['agency_id'] != 2){
					if(auth()->user()->agency_fk !=""){
						$siteSettingDetails = $this->siteSettingService->getDetails();
						if(strtolower($request->type) =='caregiver'){
							$email = $siteSettingDetails->caregiver_email_notification;
						}
						if(strtolower($request->type) =='patient'){
							$email = $siteSettingDetails->patient_email_notification;
						}
					}
				}
				

				$email_data = array(
					'username' => $username,
					'agencyname' => $agencyname,
					'insert' => $save,
					'first_name' => $patientDetailsNew->first_name,
					'last_name' => $patientDetailsNew->last_name,
					'dob' => $patientDetailsNew->dob,
					'mobile' => $patientDetailsNew->mobile,
					'gender' => $patientDetailsNew->gender,
					'discipline' => $discipline,
					'type' => $request->type,
				);
				$messages = Utility::getHtmlContent('email_template.email_create_patient_new', $email_data);

				try {

					
					$sendEmailNotication = $this->sendEmailNotificationSerivce->generalAddAppointmentNotificationEmailOnlyEmail($request->type, "Add New Record", $request->create_service_id);
					
					if ($request->type != "" && $patientDetailsNew->agency_id != "") {
						
						$sendAgencyEmailNotication = $this->sendEmailNotificationSerivce->addAppointmentOnlyEmail($request->type, $request->agency_id, $request->create_service_id);
					}

					$userEmail = [];
					if(isset($user->creator_email_noti_toggle) && $user->creator_email_noti_toggle == 1){
						$getCreatedUserEnabledOrNot = $this->userCreatorEmailNotificationService->getAddOrNotUserEmailNotification($query->agency_id,'Add Appointment');
						if($getCreatedUserEnabledOrNot ==1){
							$userEmail = array($user->email);
						}else{
							$userEmail = array($user->email);
						}
					}
					$assignAgencyMail = $this->sendEmailNotificationSerivce->getAssignNyUserAgencyMail($request->agency_id);

					$finalArray = array_unique(array_merge(array($email),$sendEmailNotication,$sendAgencyEmailNotication,$userEmail,$assignAgencyMail));
					$this->sendEmailNotificationSerivce->UserMailWithMultipleEmail($finalArray,"",$subject,$messages,"");

				} catch (\Throwable $th) {
					//throw $th;
				}
				
				if(in_array(1178,$request->create_service_id)){
					$this->createTask($save,$query->agency_id);
				}
				try {
					if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
						$agencyNotifyData = array(
							'agencyid' => $query->agency_id,
							'title' => 'New Patient Record Added',
							'record_id' => $save,
							'record_type' => 'Appointment',
							'msg' => '',
							'res_data' => serialize($data)
						);
						Common::insertAgencyNotificationsOfUser($agencyNotifyData);
					}
				} catch (\Throwable $th) {}

				
				try {
					$this->duplicatePortalCreation($data,$save);
				} catch (\Throwable $th) {
					//throw $th;
				}
				
				return response()->json(['error_msg' => "Patient demographic details successfully added", 'status' => 1, 'data' => array($query)], 200);
			} else {
				return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
			}
		}
	}

	public function updateRemainingPatientDetails(Request $request)
	{
		
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'service_id' => 'required',
		]);
		if ($validator->fails()) {
			return redirect("/patient-add-new")
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {
			$patientDetails = $this->patientService->getPatientId($request->patient_id);
			$fuDate = NULL;
			if ($request->fu_date != "") {
				$fuDate = date('Y-m-d', strtotime($request->fu_date));
			}

			$due_date = NULL;
			if ($request->due_date != "") {
				$due_date = date('Y-m-d', strtotime($request->due_date));
			}

			$data = array(

				'fu_date' => $fuDate,
				'due_date' => $due_date,
				'service_id' => implode(',', $request->service_id),
				'last_status_update' =>date('Y-m-d H:i:s'),
				'last_status_update_by' =>$user->id,
			);
			$data['status']='Pending';
			if($request->type == 'Patient'){
				$data['status'] = Utility::getStatusFromServiceId($request->service_id);
			}
			$agencyid = request('agency_id');

			// Check if agency has restrict_service_request_update enabled
			$restrictUpdate = false;
			if (!empty($agencyid)) {
				$agencyRestrict = $this->agencyService->getDetailsByAgencyId($agencyid);
				if (isset($agencyRestrict->restrict_service_request_update) && $agencyRestrict->restrict_service_request_update == 1) {
					$restrictUpdate = true;
				}
			}

			if ($restrictUpdate && Auth()->user()->agency_fk != "") {
				// Do NOT update status or service_id — preserve existing forms and portal status
				unset($data['service_id']);
				unset($data['status']);
			}

			$insert = $this->patientService->update($data, array('id' => $request->patient_id));
			$getStopSMSServices = Utility::stopSMSService($request->agency_id);
			if ($insert) {
				
				$insert = $patientDetails->id;
				/***************Get patient Service Request */
				$patientServiceCount = [];
				$patientServiceCount = $this->patientServicesRequest->getServiceCountPatientId($patientDetails->id);
				if (count($patientServiceCount) == 0) {
					$services = explode(',', $patientDetails->service_id);
					if(!empty($services[0])){
						$patientServiceLastId = $this->patientServicesRequest->save([
							'patient_id' => $patientDetails->id,
							'follow_up_date' => $patientDetails->fu_date,
							'due_date' => $patientDetails->due_date,
							'status' => $patientDetails->status,
							'created_at' =>$patientDetails->created_date,
							'created_by' =>$patientDetails->created_by,
							'completed_date' => $patientDetails->completed_date,
							'completed_by' => $patientDetails->completed_by,
							'flag'=>1
						]);
						foreach ($services as $serviceId) {
							$patientWiseServiceRequest = [
								'patient_id' => $patientDetails->id,
								'service_id' => $serviceId,
								'patient_service_request_id' => $patientServiceLastId,
								
							];
							$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
						}
					}
				}
				$serviceRequestStatus = 'Pending';
				if($request->type == 'Patient'){
					$serviceRequestStatus = Utility::getStatusFromServiceId($request->service_id);
				}
				$patientServiceLastId = $this->patientServicesRequest->save([
					'patient_id' => $insert,
					'follow_up_date' => $fuDate,
					'due_date' => $due_date,
					'status' => $serviceRequestStatus
				]);

				$addServiceIds = $request->input('service_id');

				if (is_array($addServiceIds)) {
					foreach ($addServiceIds as $serviceId) {
						if($serviceId !=""){
							$patientWiseServiceRequest = [
								'patient_id' => $insert,
								'service_id'=> $serviceId,
								'patient_service_request_id' => $patientServiceLastId,
							
							];
	
							$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
						}
						
					}
				}	
				// Get data from patient

				$addAppintment = ["patient_id" => $insert, "location_id" => null, "doctor_id" => null, "service_id" => implode(',', request('service_id')), "appointment_date" => null, "appointment_time" => null, "status" => "Pending", "created_by" => $user->id,'created_at'=>date('Y-m-d H:i:s')];
				Appointment::create($addAppintment);
				
				$appointmentId=$insert;
				
				$getAgencyName = Agency::getDetailsByAgencyId($agencyid);
				$agencyname = '';
				if (isset($getAgencyName->agency_name) && $getAgencyName->agency_name != '') {
					$agencyname = $getAgencyName->agency_name;
				}


				if ($request->type == 'Caregiver') {

					$unitId = uniqid();
					$url = URL::to('/') . '/ap/' . $unitId;
					$namearray = array();
					foreach (request('service_id') as $vdl) {
						$getMaster = Master::select('name')->where('id', $vdl)->where('del_flag', 'N')->first();
						$namearray[] = $getMaster->name;
					}

					$disabledServiceIds = Common::getServiceMesgdisable();
					$hasDisabledService = count(array_intersect($request->service_id, $disabledServiceIds)) > 0;
					
					if ($hasDisabledService) {
						$extraMessageEn = '';
						$extraMessageSp = '';
					} else {
						$extraMessageEn = ' and you will need to update it to continue employment and be active with ' . $agencyname;
						$extraMessageSp = ' y deberá actualizarlo para continuar trabajando y permanecer activo con ' . $agencyname;
					}

					if ($patientDetails->language != '' && strtolower($patientDetails->language) == 'spanish') {
						$message = 'Aviso de ' . $agencyname . ': Usted tiene prevista una cita con el médico. Su '
									. implode(',', $namearray) . ' vencerán pronto' . $extraMessageSp
									. '. Haga clic en el enlace a continuación para programar su cita Enlace ' . $url .'
									Para su referencia, su ID de cita es: ' . $appointmentId
									. '. No responda a este mensaje de texto, para cualquier pregunta, llame al (718) 972-3693';
					} else {
						$message = 'Notice from ' . $agencyname . ': Your ' . implode(',', $namearray)
									. ' expiring soon' . $extraMessageEn
									. '. Please click the link below to schedule your appointment with NYBest Medical Care ' . $url
									. '.
									For your reference, your Appointment ID is: ' . $appointmentId . '. Do not reply to this text message, for any questions please call NYBest Medical @ 718-972-3693 or email appointment@nybestmedical.com';
					}

					$data['sms'] = $message;
					$data['key'] = $unitId;
					$this->patientService->update(array('sms'=>$message,'key'=>$unitId), array('id' => $request->patient_id));
					
					$smsMobileArray = [];
					$smsMobileArray[] = str_replace([ "(", ")",'-'," " ], "", $patientDetails->mobile);
					if($request->phone !=""){
						$smsMobileArray[] = str_replace([ "(", ")",'-'," " ], "", $patientDetails->phone);
					}

					$smsStatusFlag=0;
					if($agencyid == 2){
						$emailTest = 'Pinak@nybestmedical.com';
						$subject = 'Flu vaccine: Expring soon';
						Mail::mailer('second')->send([], [], function ($messages) use ($emailTest, $subject, $message) {

							$messages->to($emailTest, "")
								->subject($subject)->html($message);
							}
						);
					}else{
						if ( $getAgencyName->is_sms ==1) {
							$getStopSMSServices = Utility::stopSMSService($agencyid);
							$array =array_intersect($request->service_id, $getStopSMSServices);
						
							if(count($array) ==0){
								if(count($smsMobileArray) >0){
									foreach($smsMobileArray as $smb){
										$smsStatusFlag = $this->smsService->AgencyWiseSmsDynamic($insert, $smb, $message);
									}
									if($smsStatusFlag !=0){
										$this->patientService->update(array('patient_sms_flag'=>1), array('id' => $request->patient_id));
									}
								}
							}
						}
					} // end else (non-agency-2)
					// Schedule auto-call if patient does not book within 4 hours (only for agencies with AI Call Logs enabled)
					if (!empty($getAgencyName->ai_call_logs_enabled)) {
						AutoCallService::scheduleAfterSmsSent(
							$insert,
							$smsMobileArray[0] ?? str_replace(["(",")","-"," "], "", $patientDetails->mobile),
							$patientDetails->first_name . ' ' . $patientDetails->last_name,
							$agencyname,
							$url ?? '',
							'updatePatient'
							,$unitId,
							$patientDetails->agency_id
							,$request->service_id
						);
					}
				} // end if Caregiver

				$patientDetailsNew = $this->patientService->getPatientId($request->patient_id);
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add Appointment',
					'link' => url('/patient-add-new'),
					'module' => 'Patient Appointment',
					'object_id' => $insert,
					'message' => $user->first_name . ' ' . $user->last_name . ' has added Appointment',
					'new_response' => serialize($patientDetailsNew->toArray()),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);

				$portal_name = $patientDetails->first_name.' '.$patientDetails->last_name;
				try{
					if($request->type == 'Patient' && !$restrictUpdate){
						Utility::saveResolutionLogForms($serviceRequestStatus,$patientServiceLastId,$request->patient_id);
					}
				}catch(Exception $e){}
				try {
					Utility::insertNotificationsOfUserNew($agencyid,$patientDetails->id,$portal_name, $patientDetails->type,request('agency_id'),request('service_id'));
				} catch (\Throwable $th) {
					//throw $th;
				}

				$emails = isset($getAgencyName->notification_email) ? $getAgencyName->notification_email : "";
				$allemails = array();
				if ($emails != '') {
					$newsemail = explode(',', $emails);
					if (count($newsemail) > 0) {
						foreach ($newsemail as $vas) {
							if (trim($vas) != '') {
								$allemails[] = $vas;
							}
						}
					}
				}
				$username = $user['first_name'] . ' ' . $user['last_name'];

				$discipline = $request->input('diciplin');

				$subject = "[" . $agencyname . "] NYBest Medical Care New record added";
				
				if(auth()->user()->agency_fk !=""){
					$siteSettingDetails = $this->siteSettingService->getDetails();
					if(strtolower($request->type) =='caregiver'){
						$email = $siteSettingDetails->caregiver_email_notification;
					}
					if(strtolower($request->type) =='patient'){
						$email = $siteSettingDetails->patient_email_notification;
					}
				}

				$email_data = array(
					'username' => $username,
					'agencyname' => $agencyname,
					'insert' => $insert,
					'first_name' => $patientDetails->first_name,
					'last_name' => $patientDetails->last_name,
					'dob' => $patientDetails->dob,
					'mobile' => $patientDetails->mobile,
					'gender' => $patientDetails->gender,
					'discipline' => $discipline,
					'type' => $request->type,
				);
				$messages = Utility::getHtmlContent('email_template.email_create_patient_new',$email_data);
				
				try {

					
					$sendEmailNotication = $this->sendEmailNotificationSerivce->generalAddAppointmentNotificationEmailOnlyEmail($request->type, "Add New Record", request('service_id'));
					if ($request->type != "" && $agencyid != "") {
						
						$sendAgencyEmailNotication = $this->sendEmailNotificationSerivce->addAppointmentOnlyEmail($request->type, $agencyid, request('service_id'));
					}
					$userEmail = [];
					if(isset($user->creator_email_noti_toggle) && $user->creator_email_noti_toggle == 1){
						$getCreatedUserEnabledOrNot = $this->userCreatorEmailNotificationService->getAddOrNotUserEmailNotification($agencyid,'Add Appointment');
						if($getCreatedUserEnabledOrNot ==1){
							$userEmail = array($user->email);
						}else{
							$userEmail = array($user->email);
						}
					}
					$assignAgencyMail = $this->sendEmailNotificationSerivce->getAssignNyUserAgencyMail($agencyid);
					$finalArray = array_unique(array_merge(array($email),$sendEmailNotication,$sendAgencyEmailNotication,$userEmail,$assignAgencyMail));
					$this->sendEmailNotificationSerivce->UserMailWithMultipleEmail($finalArray,"",$subject,$messages,"");
				} catch (\Throwable $th) {
					//throw $th;  
				}

				if(in_array(1178,$request->service_id)){
					$this->createTask($patientDetails->id,$patientDetails->agency_id);
				}
				try {
					if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
						$agencyNotifyData = array(
							'agencyid' => $agencyid,
							'title' => 'Patient Record Updated',
							'record_id' => $request->patient_id,
							'record_type' => 'Appointment',
							'msg' => '',
							'res_data' => serialize($data)
						);
						Common::insertAgencyNotificationsOfUser($agencyNotifyData);
					}
				} catch (\Throwable $th) {}
				Session::flash('success', 'Patient appointment successfully added.');
				return redirect('/appointment');
			}else{
				Session::flash('error', 'Sorry, something went wrong. Please try again.');
				return redirect('/patient-add-new');
			}
		}
	}

	public function getDemographicDeatailsData(Request $request)
	{
		$patientData = $this->patientService->getPatientId($request->id);
	
		$response = $this->patientService->getAllPatientRelatedDetails($patientData);
		return json_encode($response);
	}

	function viewPdfPatient(Request $request)
	{
		$data['url'] = "";
		$getDetails = $this->documentPatientService->getDetailsById($request->id);
	
		if (isset($getDetails->patient_id)) {
			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($getDetails->patient_id);
			
			if (isset($getPatientDetails->agency_id)) {
				$file = public_path('/') . "/patientdocument/" . $getDetails->attachment;
				$headers = [];
				$extension = pathinfo($getDetails->attachment, PATHINFO_EXTENSION);

				if (str_contains($getDetails->attachment, 'patientdocument')) {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						
					
						$data['url'] = url('patientdocument/' . $getDetails->attachment);
					} else {
						$data['url'] = Storage::disk('s3')->temporaryUrl($getDetails->attachment, now()->addMinutes(60));
					}
				} else {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$data['url'] = url('patientdocument/' . $getDetails->attachment);
					} else {
						$data['url'] = Storage::disk('s3')->temporaryUrl('patientdocument/' . $getDetails->attachment, now()->addMinutes(60));
					}
				}
			}
		}
		return view('patient._partial.view_pdf_iframe', $data);
	}

	public function createTask($record_id,$agencyID){
		$task_name = "URGENT T/H VISIT Added";
		$task_description = "URGENT T/H VISIT Added";
		
		if($agencyID !=2){
			$assignId = 487;
		}else{
			$assignId = 482;
		}
		
		$auth = auth()->user();
		$dataTask = array(
			'user_id' => $auth['id'],
			'task_name' => $task_name,
			'assign_id' =>$assignId,
			'task_description' => $task_description,
			'priority' => "High",
			'due_date' =>  date('Y-m-d H:i:s'),
			'start_date' => date('Y-m-d H:i:s'),
			'record_id'=>$record_id
		);
		$insert = $this->taskService->save($dataTask);

		if ($insert) {
			$serializedDataTask = $this->taskService->getDetailsById($insert);
			$assignUser = User::find($assignId);
			$taskLog = [
				'task_id' => $insert,
				'created_by' => $auth->id,
				'description' => $auth->first_name . ' ' . $auth->last_name . ' is created new task and assign to ' . $assignUser->first_name . ' ' . $assignUser->last_name,
				
				'new_response' =>  serialize($dataTask),
				'created_at'=>date('Y-m-d H:i:s'),
			];
			$insertTaskLog = TaskLog::create($taskLog);
		
			
			$allemails = isset($assignUser->email) ? $assignUser->email : "";
			$currentDate = date('Y-m-d',strtotime(now()));
			Cache::forget("task_list_user_{$auth->id}");
			$subject = "New Task Assigned";
			$fname = '';
			$lname = '';
			if (isset($assignUser->first_name) && $assignUser->first_name != '') {
				$fname = $assignUser->first_name;
			}
			if (isset($assignUser->last_name) && $assignUser->last_name != '') {
				$lname = $assignUser->last_name;
			}

			$username = $auth['first_name'] . ' ' . $auth['last_name'];
		   
			$emailData = array(
				'username' => $username,
				'fname' => $fname,
				'lname' => $lname,
				'task_name' => $task_name,
				'task_description' => $task_description
			); 
			$messages = Utility::getHtmlContent('email_template.email_task_create',$emailData);

			if($agencyID ==2){
				$allemails = ['vishaldpatel.vhits@gmail.com','nidhidarji.vhits@gmail.com'];
			}
			try {
				$mail = Mail::mailer('second')->send([], [], function ($message) use ($allemails, $subject, $messages) {
					$message->to($allemails, "EMC Rep")
						->subject($subject)->html($messages);
				});
				} catch (\Throwable $th) {
			}

		   
			$notificationData = array(
				'type' => 'Task',
				'user_id' => $assignId,
				'title' => 'New Task Assigned',
				'message' => $task_name .' | <b> Assigned To</b>: '.$username,
			);
			$this->notificationUserService->save($notificationData);
			return 1;
		}
	}

	private function duplicatePortalCreation($data,$newPortalId){
		$query = $this->patientService->getSearchPatientDetailsDup($data);
		$filtered = collect($query->toArray())
			->where('id', '!=', $newPortalId)
			->values();
			
		if(count($filtered) >0){

			$data['total']  =count($filtered);
			$data['record']  =$query;
			$data['newPortalId'] = $newPortalId;
			$message = Utility::getHtmlContent('email_template.email_duplicate_create_patient_new', $data);
			
			$getLiaisonDetails = $this->assignNyBestUserService->getAssignNybestUser($data['agency_id']);

			$emailTest = [];
			if($data['agency_id'] !=2){
				if(isset($data['type']) && strtolower($data['type']) =='patient'){
					$emailTest = ["support@nybestmedical.com",'vishal.dev@nybestmedical.com','tiline@nybestmedical.com','Muhammadh@nybestmedical.com'];
				}else{
					$emailTest = ["support@nybestmedical.com",'vishal.dev@nybestmedical.com','Danieladm@nybestmedical.com'];
				}

				if(!empty($getLiaisonDetails[0])){
					foreach($getLiaisonDetails as $liaison){
						if (!empty($liaison->email)) {
							$emailTest[] = $liaison->email;
						}
					}
				}
			}
			
			$emailTest = array_unique($emailTest);
			$subject = "Duplicate Portal Creation";
			Mail::mailer('second')->send([], [], function ($messages) use ($emailTest, $subject, $message) {
				$messages->to($emailTest, "")
					->subject($subject)->html($message);
				}
			);
		}
	}
}