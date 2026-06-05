<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\LocationMasterService;
use App\Services\LogsService;
use App\Services\TelehealthLocationScheduleEventService;
use App\Services\TelehealthLocationScheduleService;
use App\Services\PatientService;
use App\User;
use App\Model\Appointment;
use App\Model\Language;
use App\Model\Patient;
use App\Agency;
use App\Helpers\Common;
use App\Helpers\Utility;
use App\Services\PatientWiseServicesRequests;
use App\Services\PatientServicesRequest;
use App\Services\AgencyWiseSMSNotificationService;
use App\Services\SmsService;
use App\Services\DocumentPatientService;
use App\Services\DisableDateService;
use App\Services\PatientSMSLogService;
use App\Model\PatientTelehealthSchedule;
use App\Services\PatientTelehealthScheduleService;

class TelehealthLocationScheduleEventController extends BaseController
{

    protected $locationMasterService,$logService,$telehealthLocationScheduleService,$telehealthLocationScheduleEventService,$PatientService,$patientWiseServicesRequests, $patientServicesRequest, $agencyWiseSMSNotificationService,$smsService,$DocumentPatientService, $disableDateService,$patientSMSLogService,$patientTelehealthScheduleService="";
    
    public function __construct(LocationMasterService $locationMasterService, LogsService $logService, TelehealthLocationScheduleService $telehealthLocationScheduleService, telehealthLocationScheduleEventService $telehealthLocationScheduleEventService,PatientService $PatientService, PatientWiseServicesRequests $patientWiseServicesRequests, PatientServicesRequest $patientServicesRequest, AgencyWiseSMSNotificationService $agencyWiseSMSNotificationService, SmsService $smsService, DocumentPatientService $DocumentPatientService, DisableDateService $disableDateService,PatientSMSLogService $patientSMSLogService, PatientTelehealthScheduleService $patientTelehealthScheduleService)
    {
        $this->middleware('permission:manage-telehealth-location', ['only' => ['manageTelehealthLocation']]);
        $this->locationMasterService = $locationMasterService;
        $this->logService = $logService;
        $this->telehealthLocationScheduleService = $telehealthLocationScheduleService;
        $this->telehealthLocationScheduleEventService = $telehealthLocationScheduleEventService;
        $this->PatientService = $PatientService;
        $this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->agencyWiseSMSNotificationService = $agencyWiseSMSNotificationService;
        $this->smsService = $smsService;
        $this->DocumentPatientService = $DocumentPatientService;
        $this->disableDateService = $disableDateService;
        $this->patientSMSLogService = $patientSMSLogService;
        $this->patientTelehealthScheduleService = $patientTelehealthScheduleService;
    }

    public function manageTelehealthLocation(Request $request)
    {
        $data['locations'] = $this->locationMasterService->searchLocation();
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
				$langArray[$nurse['id']]['name'] = $nurse['name'];
			}
		}
		$data['nurse'] = $langArray;
        return view("telehealthLocationSchedule/manage_telehealth_location", $data);
    }

    public function getLocationSchedules(Request $request)
    {
        $location_id = $request->input('location_id');
        $schedules = $this->telehealthLocationScheduleService->getLocationSchedules($location_id);
        return response()->json(['status' => true, 'schedules' => $schedules]);
    }

    public function manageTelehealthLocationAjaxList(Request $request)
    {
        $location_id = $request->input('location_id');
        $schedule_id = $request->input('schedule_id');
        $nurse_id = $request->input('nurse_id');

        $days_events = $this->telehealthLocationScheduleService->getDaysAndEvents($location_id, $schedule_id, $nurse_id);
        
        return response()->json([
            'status' => true,
            'days_events' => $days_events
        ]);
    }

    public function checkNurseSchedule($locationId, $nurseId, $scheduleId)
    {
        $schedule = $this->telehealthLocationScheduleEventService->getNurseSchedule($locationId, $nurseId, $scheduleId);
        return response()->json([
            'status' => true,
            'exists' => $schedule->count() > 0,
            'schedules' => $schedule
        ]);
    }

    public function updateNurseSchedule(Request $request)
    {
        $user = auth()->user();
        $locationId = $request->input('location_id');
        $nurseId = $request->input('nurse_id');
        $scheduleId = $request->input('schedule_id');
        $events = $request->input('events');
        if($events){
            // check for the already exits with another schedule
            foreach ($events as $event) {
                $existingSchedule = $this->telehealthLocationScheduleEventService->checkOverlappingSchedule(
                    $locationId,
                    $nurseId,
                    $event['day'],
                    $event['start_time'],
                    $event['end_time'],
                    $scheduleId
                );
                if ($existingSchedule->count() > 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Schedule conflict found. The nurse already has an appointment scheduled for this time slot.'
                    ]);
                }
            }
            
            // First, delete existing events for this nurse
            //check here if not exist the ids update delete flag
            $existingEventIds = $this->telehealthLocationScheduleEventService->getEventsByLocationAndSchedule($locationId, $scheduleId,$nurseId);
            // Get all event IDs from the request
            $requestEventIds = array_column($events,'ids');
            // Find IDs that exist in database but not in request (to be soft deleted)
            $idsToDelete = array_diff($existingEventIds, $requestEventIds);
            if (!empty($idsToDelete)) {
                foreach ($idsToDelete as $id) {
                    $this->telehealthLocationScheduleEventService->softDelete(['del_flag' => 'Y'],['id' => $id]);
                }
            }

            foreach ($events as $event) {
                if(empty($event['ids'])){
                    $data = [
                        'location_id' => $locationId,
                        'schedule_id' => $scheduleId,
                        'nurse_id' => $nurseId,
                        'slot_id' => $event['slot_id'],
                        'day' => $event['day'],
                        'start_time' => $event['start_time'],
                        'end_time' => $event['end_time']
                    ];
                    $this->telehealthLocationScheduleEventService->save($data);
                }else{
                    $this->telehealthLocationScheduleEventService->update(['del_flag' => 'N','deleted_date'=>null,'deleted_by' => null],['id' => $event['ids']]);
                }
            }
            
            
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            
            $insertLog = [
                'type' => 'Update Nurse Schedule',
                'link' => url('/update-nurse-schedule'),
                'module' => 'Telehealth Location Schedule',
                'object_id' => $locationId,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated nurse schedule',
                'new_response' => serialize($events),
                'ip' => $ipaddress,
            ];
            $this->logService->save($insertLog);
            
            return response()->json(['status' => true, 'message' => 'Schedule updated successfully']);
        }else{
            return response()->json(['status' => false, 'message' => 'Please select at least on schedule.']);
        }
        
    }

    public function getTimeSlotsByLanguageAndDate(Request $request)
    {
        $language = $request->input('language');
        $date = $request->input('date');
        $type = $request->input('type');
        $patient_id = $request->input('patient_id');

        // Get day of week from date (0 = Sunday, 1 = Monday, etc.)
        $dayOfWeek = date('l', strtotime($date));
        $timeSlots = $this->telehealthLocationScheduleEventService->getTimeSlotsByLanguageAndDay($language, $dayOfWeek,$type);
        $nurse = User::getNurses();
        $langArray = array();

        $dateArray = $this->disabledDate($date);
        $finalTimeArray = $dateArray['time'];
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
        $totalTimeSlot = $timeSlots->toArray();
        $timings = [];
        foreach($timeSlots as $time){
            $timings[$time->id] = $time;
        }
        $totalids = array_column($totalTimeSlot,'id');
        $totalBookedSlotdata  = $this->telehealthLocationScheduleEventService->getTotalBookedSlot($totalids,$date)->toArray();
        $finalBookedSlot = array_column($totalBookedSlotdata,'telehealth_time_slot');
        foreach($totalBookedSlotdata as $bookedSlot) {
            if(isset($timings) && !empty($timings)){
                unset($timings[$bookedSlot['telehealth_time_slot']]);
            }
        }
        // Remove duplicate slot from array
        $uniqueEvents = array(); 
        foreach($timings as $key => $event){
            $key = $event->start_time;
            if(!empty($finalTimeArray)){
                foreach ($finalTimeArray as $time => $dates) { 
                    if($date == $dates){
                        if (date('H:i A', strtotime($key)) < date('H:i A',strtotime($time))) {
                            if (!isset($seen[$key])) {
                                $seen[$key] = true;
                                $uniqueEvents[] = $event;
                            }
                        }
                    }
                }
                
            }else{
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $uniqueEvents[] = $event;
                }
            }
        }
        $timeSlots = $uniqueEvents;
        
        return response()->json([
            'status' => true,
            'time_slots' => $timeSlots,
            'totalBookedSlot' => 0,
            'slotCheck' => $finalBookedSlot,
            'nurse' => $nurse
        ]);
    }

    public function getPatientExistingAppointment(Request $request)
    {
        $patientId = $request->input('patient_id');
        if($request->type == 'Caregiver'){
            $appointment = $this->telehealthLocationScheduleEventService->getPatientExistingAppointment($patientId);
        }else{
            $appointment = $this->PatientService->getPatientExistingAppointment($patientId);
        }
        return response()->json([
            'status' => true,
            'appointment' => $appointment
        ]);
    }

    public function AddTelehealth(Request $request)
	{
		$auth = auth()->user();
		$ap_date = $request->input('telehealth_date_id');
		$patient_id = $request->input('id');
        $type = $request->input('type');

        $query = $this->PatientService->getPatientDetailsId($patient_id);
        $getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
        if ($query->telehealth_key != "") {
            $unitId = $query->telehealth_key;
        } else {
            $unitId = uniqid();
        }
        if($type == 'Caregiver'){
            $this->PatientService->update(
                array(
                        'telehealth_date_time' => date('Y-m-d', strtotime($ap_date)), 	
                        'status' => 'booked', 
                        'telehealth_by' => $auth['id'], 
                        'last_status_update' => date('Y-m-d H:i:s'), 
                        'last_status_update_by' => $auth['id'],
                        'telehealth_time_slot' => $request->telehealth_time_slot,
                        'telehealth_language' => $request->telehealth_language,
                        'telehealth_key' => $unitId
                    ), array('id' => $patient_id)
                );
            $this->saveTeleHealthServiceRequest($patient_id, 'booked');
            $this->saveToAppointmentData($request,$ap_date,$auth,$unitId,$type);
            
            $getAppointSchedule = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($request->telehealth_time_slot);
            $getServiceArray = $this->patientWiseServicesRequests->getExistingPatientServices($patient_id);
            // Check services are present into the associate agency id
            $isSendSMS = Common::checkTeleAgencyService($getServiceArray,$query->agency_id);
            if($isSendSMS == 1 && $getAgencyName->is_sms == 1){
                $message = common::sendsmsAgencyTelehealth($getAppointSchedule,$unitId,$query,$getAgencyName,$patient_id);
                if(isset($message) && !empty($message)){
                        try {
                            $this->smsService->AgencyWiseSmsDynamic($patient_id, $query->mobile, $message);
                        } catch (\Throwable $th) {
                                throw $th;
                        }
                        $this->PatientService->update(array('telehealth_key' => $unitId, 'patient_sms_flag' => 1), array('id' => $patient_id));
                        $this->patientSMSLogService->save(array('patient_id' => $patient_id, 'mobile_no' => $query->mobile, 'message' => $message, 'key' => $unitId));
                }
            }
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Update Telehealth Schedule Appointment',
                'link' => url('/patient/view') . '/' . $request->input('id'),
                'module' => 'Patient Appointment',
                'object_id' =>  $request->input('id'),
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has Appointment Schedule',
                'new_response' => serialize(array('appointment_date' => date('Y-m-d H:i:s', strtotime($ap_date)), 'telehealth_date_time' => date('Y-m-d', strtotime($ap_date)), 'status' => 'booked', 'telehealth_by' => $auth['id'], 'last_status_update' => date('Y-m-d H:i:s'), 'last_status_update_by' => $auth['id'],'telehealth_time_slot' => $request->telehealth_time_slot,'telehealth_language' => $request->telehealth_language,)),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
        }elseif($type == 'Patient'){
            $ap_date = $request->input('patient_telehealth_date_id');
            $is_from_chart = $request->input('is_from_chart')??0;
            $this->PatientService->update(
                array(
                        'telehealth_date_time' => date('Y-m-d', strtotime($ap_date)),
                        'status' => 'booked',
                        'telehealth_by' => $auth['id'],
                        'last_status_update' => date('Y-m-d H:i:s'),
                        'last_status_update_by' => $auth['id'],
                        'telehealth_time_slot' => $request->patient_telehealth_time_slot,
                        'telehealth_key' => $unitId,
                        'telehealth_nurse' => $request->telehealth_nurse,
                    ), array('id' => $patient_id)
                );
            if($is_from_chart == 0){
                $this->saveTeleHealthServiceRequest($patient_id, 'booked');
            }
            $this->saveToAppointmentData($request,$ap_date,$auth,$unitId,$type);
            $getAppointSchedule = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($request->patient_telehealth_time_slot);
            $getServiceArray = $this->patientWiseServicesRequests->getExistingPatientServices($patient_id);
            // Check services are present into the associate agency id
            $isSendSMS = Common::checkTeleAgencyService($getServiceArray,$query->agency_id);
            if($isSendSMS == 1 && $getAgencyName->is_sms == 1){
                $message = common::sendsmsAgencyTelehealth($getAppointSchedule,$unitId,$query,$getAgencyName,$patient_id);
                if(isset($message) && !empty($message)){
                    try {
                        $this->smsService->AgencyWiseSmsDynamic($patient_id, $query->mobile, $message);
                    } catch (\Throwable $th) {
                            //throw $th;
                    }
                    $this->PatientService->update(array('telehealth_key' => $unitId, 'patient_sms_flag' => 1), array('id' => $patient_id));
                    $this->patientSMSLogService->save(array('patient_id' => $patient_id, 'mobile_no' => $query->mobile, 'message' => $message, 'key' => $unitId));
                }
            }
            // exit;
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Update Telehealth Schedule Appointment',
                'link' => url('/patient/view') . '/' . $request->input('id'),
                'module' => 'Patient Appointment',
                'object_id' =>  $request->input('id'),
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has Appointment Schedule',
                'new_response' => serialize(array('appointment_date' => date('Y-m-d H:i:s', strtotime($ap_date)), 'telehealth_date_time' => date('Y-m-d', strtotime($ap_date)), 'status' => 'booked', 'telehealth_by' => $auth['id'], 'last_status_update' => date('Y-m-d H:i:s'), 'last_status_update_by' => $auth['id'],'telehealth_time_slot' => $request->patient_telehealth_time_slot,'telehealth_language' => $request->telehealth_language,)),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
        }
		try {
            $agencyNotifyData = array(
                'agencyid' => $query->agency_id,
                'title' => 'Updated Telehealth Schedule Appointment',
                'record_id' => $patient_id,
                'record_type' => 'Appointment',
                'msg' => ''
            );
            Common::insertAgencyNotificationsOfUser($agencyNotifyData);
        } catch (\Throwable $th) {}
		return response()->json(['error_msg' => "Telehealth appointment successfully added", 'status' => 1, 'data' => array()], 200);
	}

    public function saveTeleHealthServiceRequest($id, $status)
	{
		$auth = auth()->user();
		$checkServices = $this->patientServicesRequest->getPatientService($id);

		if (count($checkServices) > 0) {
			$getLastServiceId = $this->patientServicesRequest->lastServiceRequestedByPatientId($id);
			$statusName = $status;

			if ($status == 'booked') {
				$statusName = "Booked";
			}

			$updateStatus = array(
				'status' => $statusName,
				'last_status_update' => date('Y-m-d H:i:s')
			);
			$this->patientServicesRequest->update($updateStatus, array('id' => $getLastServiceId->id));
		} else {
			$checkForDocument = $this->DocumentPatientService->getDetailsByPatientId(array($id));
			$getPatientServices = $this->PatientService->getPatientId($id);
			$servicesId = explode(',', $getPatientServices->service_id);

			if (!empty($servicesId[0])) {
				$finalData = [
					'patient_id' => $id,
					'fu_date' => $getPatientServices->fu_date,
					'due_date' => $getPatientServices->due_date,
					'status' => $status,
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

	public function checkNurseScheduleByDate(Request $request)
    {
        $nurseId = $request->input('nurse_id');
        $date = $request->input('date');

        if (!$nurseId || !$date) {
            return response()->json([
                'status' => false,
                'message' => 'Nurse ID and date are required'
            ]);
        }

        // Get day of week from date (0 = Sunday, 1 = Monday, etc.)
        $dayOfWeek = date('l', strtotime($date));
        $scheduleData = $this->telehealthLocationScheduleService->getSchedule();
        $formattedSchedule = [];
        foreach($scheduleData as $sch){
            // Get nurse's schedule for the selected date
            $schedule = $this->telehealthLocationScheduleEventService->getNurseScheduleByDate($nurseId, $date, $dayOfWeek, $sch->location_id , $sch->id);
            if (count($schedule) > 0) {
                $is_assigned = 0;
                foreach($schedule as $schedules){
                    if($schedules['is_assigned']){
                        $is_assigned++;
                    }
                }
                if($is_assigned > 0){
                    $formattedSchedule[$sch['id']] = $schedule;
                }
            }
        }
        return response()->json([
            'status' => true,
            'data' => $formattedSchedule,
            'scheduleData' => $scheduleData,
        ]);
	}

	public function updateNurseScheduleByDate(Request $request)
    {
        try {
            $nurseId = $request->input('nurse_id');
            $date = $request->input('date');
            $timeSlots = $request->input('events', []);
            if (!$nurseId || !$date) {
                return response()->json([
                    'status' => false,
                    'message' => 'Nurse ID and date are required'
                ]);
            }

            $dayOfWeek = date('l', strtotime($date));
            if(isset($timeSlots)){
                $existingSchedule = $this->telehealthLocationScheduleEventService->checkOverlappingScheduleNurse($timeSlots);
                if ($existingSchedule) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Schedule conflict found. The nurse already has an appointment scheduled for this time slot.'
                    ]);
                }
            }
            // Update the schedule through service
            $result = $this->telehealthLocationScheduleEventService->updateNurseScheduleByDateManual($nurseId, $date, $dayOfWeek, $timeSlots);
            return response()->json([
                'status' => true,
                'message' => 'Schedule updated successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Schedule conflict found. The nurse already has an appointment scheduled for this time slot.'
            ]);
        }
    }

    public function patientTeleAppointments(Request $request,$key){
        $data['key'] = $key;
		$data['query'] = $query = $this->PatientService->getTeledata($key);

		if (isset($query->id)) {
            $data['language_list'] =  Language::getLanguageList();
            $disable_date = $this->disableDateService->disableDateAllData($data['query']->type)->toArray();
			$dateArray = explode(', ', implode(', ', $disable_date));
			$dateDetailArray = [];
			if (!empty($dateArray[0])) {
				foreach ($dateArray as $val) {
					$dateDetailArray[] = date('d-m-Y', strtotime($val));
				}
			}
			$data['disable_date']  = json_encode($dateDetailArray);
            $data['schedule_info'] = $this->telehealthLocationScheduleEventService->getScheduleInfo($query->id);
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
            $data['service'] = $query->service_id;
			return view('patient/telehealth_appointment_book', $data);
		} else {
			return redirect('link_expired');
		}
    }


    function teleAppointmentsUpdate(Request $request)
	{
		$key = $request->input('key');
		$query = Patient::select('id', 'type', 'agency_id', 'mobile', 'first_name', 'last_name')->where('id', $request->input('id'))->where('deleted_flag', 'N')->first();
        $type = $query->type;
        $getAgencyName = Agency::getDetailsByAgencyId($query->agency_id);
		if (isset($query->id) && $query->id != '') {
            if($type == 'Caregiver'){
                $duedate = $request->input('telehealth_date_id');
                $patient_id = $query->id;
                $agency_name = '';
                if (isset($getAgencyName->agency_name) &&  $getAgencyName->agency_name != '') {
                    $agency_name = $getAgencyName->agency_name;
                }
                $update = $this->PatientService->update(
                    array(
                            'telehealth_date_time' => date('Y-m-d', strtotime($duedate)), 	
                            'status' => 'booked', 
                            'last_status_update' => date('Y-m-d H:i:s'), 
                            'telehealth_time_slot' => $request->telehealth_time_slot,
                            'telehealth_language' => $request->telehealth_language,
                        ), array('id' => $patient_id)
                    );
                $this->saveTeleHealthServiceRequest($patient_id, 'booked');
                $this->saveToAppointmentData($request,$duedate,'',$key,$type);
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Update Telehealth Schedule Appointment',
                    'link' => url('/patient/view') . '/' . $request->input('id'),
                    'module' => 'Patient Appointment',
                    'object_id' =>  $request->input('id'),
                    'message' => 'Telehealth Schedule Appointment updated by the sms generated link',
                    'new_response' => serialize(array('appointment_date' => date('Y-m-d H:i:s', strtotime($duedate)), 'telehealth_date_time' => date('Y-m-d', strtotime($duedate)), 'status' => 'booked', 'last_status_update' => date('Y-m-d H:i:s'),'telehealth_time_slot' => $request->telehealth_time_slot,'telehealth_language' => $request->telehealth_language)),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                $getAppointSchedule = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($request->telehealth_time_slot);
                $email = '';/****remove allstaff email***/
                $subject = "[" . $getAgencyName->agency_name . "] NYBest Medical Care Telehealth Appointment Schedule update";
                $emailData = array(
                    'agencyname' => $agency_name,
                    'insert' => $query->id,
                    'first_name' => $query->first_name,
                    'last_name' => $query->last_name,
                    'mobile_no' => $query->mobile,
                    'type' => $query->type,
                    'schedule_date' => date('m-d-Y', strtotime($getAppointSchedule->date)) . ' ' . date('h:i A', strtotime($getAppointSchedule->start_time)) . ' to ' . date('h:i A', strtotime($getAppointSchedule->end_time)),
                    'nurse' => $getAppointSchedule->first_name.' '.$getAppointSchedule->last_name,
                    'language' => $getAppointSchedule->name
                );
                $messages = Utility::getHtmlContent('email_template.email_telehealth_appointment_schedule', $emailData);
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
            }else{
                $duedate = $request->input('patient_telehealth_date_id');
                $patient_id = $query->id;
                $agency_name = '';
                if (isset($getAgencyName->agency_name) &&  $getAgencyName->agency_name != '') {
                    $agency_name = $getAgencyName->agency_name;
                }
                $this->PatientService->update(
                    array(
                            'telehealth_date_time' => date('Y-m-d', strtotime($duedate)), 	
                            'status' => 'booked', 
                            'last_status_update' => date('Y-m-d H:i:s'), 
                            'telehealth_time_slot' => $request->patient_telehealth_time_slot,
                            'telehealth_nurse' => $request->telehealth_nurse,
                        ), array('id' => $patient_id)
                    );
                $this->saveTeleHealthServiceRequest($patient_id, 'booked');
                $this->saveToAppointmentData($request,$duedate,'',$key,$type);
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Update Telehealth Schedule Appointment',
                    'link' => url('/patient/view') . '/' . $request->input('id'),
                    'module' => 'Patient Appointment',
                    'object_id' =>  $request->input('id'),
                    'message' => 'Telehealth Schedule Appointment updated by the sms generated link',
                    'new_response' => serialize(array('appointment_date' => date('Y-m-d H:i:s', strtotime($duedate)), 'telehealth_date_time' => date('Y-m-d', strtotime($duedate)), 'status' => 'booked', 'last_status_update' => date('Y-m-d H:i:s'),'telehealth_time_slot' => $request->patient_telehealth_time_slot)),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                // $getAppointSchedule = $this->patientTelehealthScheduleService->getTelehalthPatientScheduledata($request->patient_telehealth_time_slot,$patient_id);
                $getAppointSchedule = $this->telehealthLocationScheduleEventService->getTelehalthScheduledata($request->patient_telehealth_time_slot);
                $email = '';/****remove allstaff email***/
              
                $subject = "[" . $getAgencyName->agency_name . "] NYBest Medical Care Telehealth Appointment Schedule update";
                $emailData = array(
                    'agencyname' => $agency_name,
                    'insert' => $query->id,
                    'first_name' => $query->first_name,
                    'last_name' => $query->last_name,
                    'mobile_no' => $query->mobile,
                    'type' => $query->type,
                    'schedule_date' => date('m-d-Y', strtotime($getAppointSchedule->date)) . ' ' . date('h:i A', strtotime($getAppointSchedule->start_time)) . ' to ' . date('h:i A', strtotime($getAppointSchedule->end_time)),
                    'nurse' => $getAppointSchedule->first_name.' '.$getAppointSchedule->last_name,
                    'language' => $getAppointSchedule->name
                );
                $messages = Utility::getHtmlContent('email_template.email_telehealth_appointment_schedule', $emailData);
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
            }
			
			return response()->json(['error_msg' => "Telehealth appointment scheduled successfully", 'status' => 1, 'data' => array()], 200);
		} else {
			return redirect('expired');
		}
	}

    public function saveToAppointmentData($request,$duedate,$auth,$key,$type){
        if($type == 'Caregiver'){
            $service = isset($auth) && !empty($auth) ? implode(',', $request->input('tele_caregiver_service_id')) : $request->input('tele_caregiver_service_id');
            $checkAppointment = Appointment::where('patient_id', $request['id'])->where('telehealth_date', null)->where('doctor_id', null)->where('appointment_date', null)->first();
            if($checkAppointment){
                $checkAppointment->update([ 
                    'telehealth_date' => date('Y-m-d', strtotime($duedate)),
                    'telehealth_by' => isset($auth['id']) && $auth['id'] == '' ? $auth['id']:'',  
                    'telehealth_time_slot' => $request->telehealth_time_slot,
                    'telehealth_language' => $request->telehealth_language,
                    'status' => 'booked',
                    'telehealth_unique_id' => $key,
                    'service_id' => $service,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $auth['id']??'',
                ]);
            }else{
                $checkAppointment = Appointment::where('patient_id', $request['id'])->where('telehealth_date', date('Y-m-d', strtotime($duedate)))->where('doctor_id', null)->first();
                if ($checkAppointment) {
                    $checkAppointment->update([ 
                            'telehealth_date' => date('Y-m-d', strtotime($duedate)),
                            'telehealth_by' => isset($auth['id']) && $auth['id'] == '' ? $auth['id']:'',  
                            'telehealth_time_slot' => $request->telehealth_time_slot,
                            'telehealth_language' => $request->telehealth_language,
                            'status' => 'booked',
                            'telehealth_unique_id' => $key,
                            'service_id' => $service,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $auth['id']??'',
                        ]);
                } else {
                    $addAppintment = [
                        "patient_id" => $request['id'], 
                        'telehealth_date' => date('Y-m-d', strtotime($duedate)),
                        'telehealth_by' => isset($auth['id']) && $auth['id'] == '' ? $auth['id']:'',  
                        'telehealth_time_slot' => $request->telehealth_time_slot,
                        'telehealth_language' => $request->telehealth_language,
                        "status" => "booked", 
                        "telehealth_unique_id" => $key,
                        "created_at" => date('Y-m-d H:i:s'),
                        'service_id' => $service
                    ];
                    Appointment::create($addAppintment);
                }
            }
        }elseif($type == 'Patient'){
            $service = isset($auth) && !empty($auth) ? implode(',', $request->input('tele_patient_service_id')) : $request->input('tele_patient_service_id');
            $checkAppointment = Appointment::where('patient_id', $request['id'])->where('appointment_time', null)->where('telehealth_date', null)->where('doctor_id', null)->first();
            if($checkAppointment){
                $checkAppointment->update([ 
                    'telehealth_date' => date('Y-m-d', strtotime($duedate)),
                    'telehealth_by' => isset($auth['id']) && $auth['id'] == '' ? $auth['id']:'',  
                    'telehealth_time_slot' => $request->patient_telehealth_time_slot,
                    'status' => 'booked',
                    'telehealth_unique_id' => $key,
                    'telehealth_nurse' => $request->telehealth_nurse,
                    'service_id' => $service,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $auth['id']??'',
                ]);
            }else{
                $checkAppointment = Appointment::where('patient_id', $request['id'])->where('telehealth_date', date('Y-m-d', strtotime($duedate)))->where('doctor_id', null)->where('telehealth_nurse', $request->telehealth_nurse)->first();
                if ($checkAppointment) {
                    $checkAppointment->update([ 
                            'telehealth_date' => date('Y-m-d', strtotime($duedate)),
                            'telehealth_by' => isset($auth['id']) && $auth['id'] == '' ? $auth['id']:'',  
                            'telehealth_time_slot' => $request->patient_telehealth_time_slot,
                            'status' => 'booked',
                            'service_id' => $service,
                            'telehealth_unique_id' => $key,
                            'telehealth_nurse' => $request->telehealth_nurse,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $auth['id']??'',
                        ]);
                } else {
                    $addAppintment = [
                        "patient_id" => $request['id'], 
                        'telehealth_date' => date('Y-m-d', strtotime($duedate)),
                        'telehealth_by' => isset($auth['id']) && $auth['id'] == '' ? $auth['id']:'',  
                        'telehealth_time_slot' => $request->patient_telehealth_time_slot,
                        "status" => "booked", 
                        "telehealth_unique_id" => $key,
                        'telehealth_nurse' => $request->telehealth_nurse,
                        "created_at" => date('Y-m-d H:i:s'),
                        'service_id' => $service
                    ];
                    Appointment::create($addAppintment);
                }
            }
        }
        return 1;
    }

    /**
     * Save 6 slots of 15 min for a selected day (from Copy button)
     */
    public function saveLocationScheduleSlots(Request $request)
    {
        $slots = $request->input('slot');
        $day = $request->input('day');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        if (!$slots) {
            return response()->json(['status' => false, 'message' => 'No slots provided.']);
        }
        $timeSlots = $this->getTimeSlots($start_time, $end_time);
        $created = 0;
        foreach($timeSlots as $time){
            $flag = $this->patientTelehealthScheduleService->getSlot($time['start_time'],$time['end_time'],$day);
            if(empty($flag)){
                $eventData = array(
                    'day' => $day,
                    'start_time' => $time['start_time'],
                    'end_time' => $time['end_time'],
                    'slot' => $slots,               
                );
                $this->patientTelehealthScheduleService->save($eventData);
                $created++;
            }else{
               return response()->json(['status' => false, 'message' => 'Action skipped: slots already exist.']); 
            }
        }
        
        if ($created > 0) {
            return response()->json(['status' => true, 'message' => "Time slot successfully booked for ".ucfirst($day).'.']);
        } else {
            return response()->json(['status' => false, 'message' => 'No valid slots to save.']);
        }
    }

    function getTimeSlots($startTime, $endTime, $interval = 15) {
        $slots = [];

        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);

        while ($startTimestamp < $endTimestamp) {
            $slotStart = date('H:i', $startTimestamp);
            $startTimestamp += $interval * 60; // Add 15 minutes
            $slotEnd = date('H:i', $startTimestamp);

            if ($startTimestamp <= $endTimestamp) {
                $slots[] = array('start_time' => $slotStart, 'end_time' => $slotEnd);
            }
        }

        return $slots;
    }

    public function copyLocationScheduleSlots(Request $request)
    {
        $slots = $request->input('slots');
        $slotsData = PatientTelehealthSchedule::where('del_flag', 'N')
            ->orderBy('start_time')
            ->get(['start_time', 'end_time', 'slot','id','day'])->groupBy('day')->toArray();
        $flg = 0 ;
        $slotCount = count($slotsData);
        foreach($slotsData as $slotd){
            $dataC = count($slotd); 
            if($dataC == 40){
                $flg++;
            }
        }
        if($slotCount > 0 && $slotCount == $flg){
            return response()->json(['status' => false, 'message' => 'The slots have already been added, so there is no need to schedule more.']);
        }else{     
            if (!$slots || !is_array($slots) || count($slots) == 0) {
                return response()->json(['status' => false, 'message' => 'No slots provided.']);
            }
            $created = 0;
            foreach ($slots as $slot) {
                if (empty($slot['day']) || empty($slot['start_time']) || empty($slot['end_time']) || empty($slot['slot'])) {
                    continue;
                }
                $eventData = array(
                    'day' => $slot['day'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'slot' => $slot['slot'],               
                );
                $this->patientTelehealthScheduleService->save($eventData);
                $created++;
            }
            if ($created > 0) {
                return response()->json(['status' => true, 'message' => "Time slot successfully booked for Monday to Friday."]);
            } else {
                return response()->json(['status' => false, 'message' => 'No valid slots to save.']);
            }
        }
    }

    public function getTelehealthSlotsOld(Request $request)
    {
        $day = date('l', strtotime($request->day));
        // Optionally, filter by date, language, etc.
        $final = array();
        $slots = PatientTelehealthSchedule::where('day', $day)
            ->where('del_flag', 'N')
            ->orderBy('start_time')
            ->get(['start_time', 'end_time', 'slot','id'])->toArray();
        // For now, just return all slots
        foreach($slots as $key => $slot){
            $subqye = $this->PatientService->getPatientTeleCountByTime($slot['id'], $request->day);
            $slot['start_time'] = date('h:i A', strtotime($slot['start_time']));
            $slot['end_time'] = date('h:i A', strtotime($slot['end_time']));
            $slotRemaing = ($slot['slot'] - $subqye);
            $slotRemaings = 0;
            if ($slotRemaing > 0) {
                $slotRemaings = $slotRemaing;
            }
            $slot['slots'] = $slotRemaings;
            if($slotRemaings == 0){
                unset($slots[$key]);
            }
            if($slotRemaings > 0){
                $final[] = $slot;
            }
        }
        // Convert 'slots' to indexed array
        $slots =  array_values($final);
        
        // Output as JSON
        return response()->json([
            'status' => true,
            'slots' => $slots
        ]);
    }

    public function getPatientTelehealthList(Request $request)
    {
        $schedules = PatientTelehealthSchedule::where('disable_status', 'N')
            ->select(
                'patient_telehealth_schedule.id',
                'patient_telehealth_schedule.start_time',
                'patient_telehealth_schedule.end_time',
                'patient_telehealth_schedule.day',
                'patient_telehealth_schedule.slot'
            )
            ->get()
            ->map(function($item) {
                return [
                    'day' => $item->day,
                    'start_time' => date('h:i A', strtotime($item->start_time)),
                    'end_time' => date('h:i A', strtotime($item->end_time)),
                    'slot' => $item->slot,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $schedules
        ]);
    }

    public function updatePatientTelehealthStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        try {
            $this->patientTelehealthScheduleService->update(
                ['telehealth_status' => $status],
                ['id' => $id]
            );

            return response()->json(['status' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to update status']);
        }
    }

    public function getTelehealthSlots(Request $request)
    {
        $search['day'] = date('l', strtotime($request->day));
        $search['nurse'] = $request->nurse;
        $search['type'] = $request->type;
        $final = array();
        $slots = $this->telehealthLocationScheduleEventService->getNurseSchedulesData($search);
        $startDate = $request->day;
        $dateArray =  $this->disabledDate($startDate);
        $finalTimeArray = $dateArray['time'];
        // For now, just return all slots
        foreach($slots as $key => $slot){
            $subqye = $this->PatientService->getPatientTeleCountByTime($slot['id'], $request->day);
            $slot['start_time'] = date('h:i A', strtotime($slot['start_time']));
            $slot['end_time'] = date('h:i A', strtotime($slot['end_time']));

            if(!empty($finalTimeArray)){
                    foreach ($finalTimeArray as $time => $dates) {
                       if($startDate ==$dates){
                            if (date('H:i A', strtotime($slot['start_time'])) < date('H:i A',strtotime($time))) {
                                // $final[] = $slot;
                                if($subqye == 0){
                                    $final[] = $slot;
                                }
                                if($subqye > 0){
                                    unset($slots[$key]);
                                }
                            }
                        }
                    }
            }else{
                if($subqye == 0){
                    $final[] = $slot;
                }
                if($subqye > 0){
                    unset($slots[$key]);
                }
            }
        }
        // Convert 'slots' to indexed array
        $slots =  array_values($final);
        // Output as JSON
        return response()->json([
            'status' => true,
            'slots' => $slots
        ]);
    }

    protected function disabledDate($startDate){
        $dateArray =  $this->disableDateService->disableDateAllDataWithTime();
        $finalTimeArray = [];
        if(!empty($dateArray)){
            foreach($dateArray as $key=>$vals){
                $explode = explode(',',$vals);
               
                if(!empty($explode[0])){
                    foreach($explode as $dats){
                        if(trim($dats) == $startDate){
                            $finalTimeArray[date('H:i', strtotime($key))]=trim($dats);
                        }
                    }
                }
                
            }
        }
        return ['time'=>$finalTimeArray];
    }
}