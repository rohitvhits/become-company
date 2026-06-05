<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\PatientAutoCallLog;
use App\Model\PatientAutoCallAttempt;
use App\Model\CallAppointment;
use App\Model\Appointment;
use App\Model\LocationMaster;
use App\Model\AgencyWiseService;
use App\Model\Language;
use App\Services\AgencyWiseServiceService;
use App\Services\AgencyService;
use App\Services\LocationMasterService;
use App\Services\ResolutionSmsTemplateService;
use App\User;
use App\Services\SmsService;
use App\Services\PatientService;
use App\Services\AppointmentService;
use App\Services\AutoCallService;
use App\Services\CallAppointmentService;
use App\Services\LogsService;
use App\Services\MasterService;
use App\Services\PatientAutoCallLogService;
use App\Services\UserService;
use App\Helpers\Utility;
use Carbon\Carbon;


class AiCallLogController extends Controller
{
    protected $smsService;
    protected $agencyWiseServiceService;
    protected $patientService;
    protected $resolutionSmsTemplateService;
    protected $agencyService;
    protected $locationMasterService;
    protected $autoCallLogService;
    protected $callAppointmentService;
    protected $masterService;
    protected $userService;
    protected $appointmentService;

    public function __construct(SmsService $smsService, AgencyWiseServiceService $agencyWiseServiceService, PatientService $patientService, ResolutionSmsTemplateService $resolutionSmsTemplateService, AgencyService $agencyService, LocationMasterService $locationMasterService, PatientAutoCallLogService $autoCallLogService, CallAppointmentService $callAppointmentService, MasterService $masterService, UserService $userService, AppointmentService $appointmentService)
    {
        $this->middleware('auth');
		$this->middleware('permission:ai-call-logs', ['only' => ['index', 'ajaxList','detail', 'fetchConversation', 'getAudio','convertToAppointment', 'sendReminder', 'saveNotes', 'updateBooking']]);
        $this->smsService = $smsService;
        $this->agencyWiseServiceService = $agencyWiseServiceService;
        $this->patientService = $patientService;
        $this->resolutionSmsTemplateService = $resolutionSmsTemplateService;
        $this->agencyService = $agencyService;
        $this->locationMasterService = $locationMasterService;
        $this->autoCallLogService = $autoCallLogService;
        $this->callAppointmentService = $callAppointmentService;
        $this->masterService = $masterService;
        $this->userService = $userService;
        $this->appointmentService = $appointmentService;
    }

    public function index()
    {
        $data['title']     = 'AI Call Logs';
        $data['agencies']  = $this->agencyService->getDropdownList();
        $data['locations'] = $this->locationMasterService->getDropdownList(['57', '69']); // Exclude location ID 57 and 69
        return view('ai-call-logs.index', $data);
    }

    public function ajaxList(Request $request)
    {
        if ($request->stats_only) {
            return response()->json(['stats' => $this->autoCallLogService->getStats()]);
        }

        $query = $this->autoCallLogService->getFilteredQuery($request->only([
            'search', 'call_status', 'verified', 'converted',
            'date_from', 'date_to', 'agency_id', 'location_id',
        ]));

        $data['list'] = $query->with('callAppointment.location')->paginate(20);
        return view('ai-call-logs.ajax-list', $data);
    }

    public function detail($id)
    {
        $log = $this->autoCallLogService->getDetailById($id);

        // Auto-extract conversation_id from call_response if missing
        if (!$log->conversation_id && $log->call_response) {
            $resp = json_decode($log->call_response, true);
            if (isset($resp['conversation_id'])) {
                $log->conversation_id = $resp['conversation_id'];
                $log->save();
            }
        }

        // Load linked booking with location/nurse names in one query
        $linked = $this->callAppointmentService->getByAutoCallLogId($id);

        // Resolve multiple service names from comma-separated service_id
        $serviceNames = [];
        if ($linked && $linked->service_id) {
            $serviceIds   = array_filter(array_map('trim', explode(',', $linked->service_id)));
            $serviceNames = $this->masterService->getNamesByIds($serviceIds);
        }

        $data['log']          = $log;
        $data['locationName'] = $linked->location_name ?? null;
        $data['serviceNames'] = $serviceNames;
        $data['nurseName']    = $linked && $linked->nurse_name ? trim($linked->nurse_name) : null;

        // Dropdown data for edit booking modal
        $data['locationList'] = $this->locationMasterService->getWalkInList(config('services.elevenlabs.exclude_location_ids'));        $getAgencyWiseServiceList = $this->agencyWiseServiceService->ServiceListNewWithoutNyBestUser('Caregiver', $log->agency_id);
        if (count($getAgencyWiseServiceList) > 0) {
            foreach ($getAgencyWiseServiceList as $vals) {
                $vals->types = $vals->type;
            }
            $data['serviceList'] = $getAgencyWiseServiceList;
        } else {
            $data['serviceList'] = $this->masterService->getServiceRequestNewWithCondition('Caregiver');
        }
        $data['nurseList']    = $this->userService->getNurseList();
        $data['languageList'] = Language::getLanguageList();

        $data['attempts'] = $this->autoCallLogService->getAttemptsByLogId($id);

        $data['title'] = 'AI Call Detail';
       
        return view('ai-call-logs.detail', $data);
    }

    public function fetchConversation($id)
    {
        $log = $this->autoCallLogService->findOrFail($id);

        // Try to get conversation_id from call_response if not set
        if (!$log->conversation_id && $log->call_response) {
            $resp = json_decode($log->call_response, true);
            if (isset($resp['conversation_id'])) {
                $log->conversation_id = $resp['conversation_id'];
                $log->save();
            }
        }

        if (!$log->conversation_id) {
            return response()->json(['status' => false, 'message' => 'No conversation ID available']);
        }

        $this->fetchAndCacheConversation($log);

        return response()->json([
            'status' => true,
            'transcript' => $log->transcript ? json_decode($log->transcript, true) : null,
            'extracted_data' => $log->extracted_data ? json_decode($log->extracted_data, true) : null,
            'conversation_id' => $log->conversation_id,
        ]);
    }

    private function fetchAndCacheConversation(PatientAutoCallLog $log): void
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => config('services.elevenlabs.base_url') . "/convai/conversations/{$log->conversation_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'xi-api-key: ' . config('services.elevenlabs.api_key'),
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $log->transcript = isset($data['transcript']) ? json_encode($data['transcript']) : null;
            $log->extracted_data = isset($data['analysis']) ? json_encode($data['analysis'])
                : (isset($data['metadata']) ? json_encode($data['metadata']) : null);
            $log->save();
        }
    }

    public function getAudio($id)
    {
        $log = $this->autoCallLogService->findOrFail($id);

        if (!$log->conversation_id) {
            return response()->json(['status' => false, 'message' => 'No conversation ID'], 404);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => config('services.elevenlabs.base_url') . "/convai/conversations/{$log->conversation_id}/audio",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['xi-api-key: ' . config('services.elevenlabs.api_key')],
            CURLOPT_TIMEOUT => 30,
        ]);

        $audio = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);

        if ($httpCode === 200 && $audio) {
            return response($audio, 200)
                ->header('Content-Type', $contentType ?: 'audio/mpeg')
                ->header('Content-Length', strlen($audio))
                ->header('Accept-Ranges', 'bytes');
        }

        return response()->json(['status' => false, 'message' => 'Audio not available'], 404);
    }

    public function verify(Request $request, $id)
    {
        $log = $this->autoCallLogService->findOrFail($id);

        $log->admin_verified    = true;
        $log->admin_verified_at = Carbon::now();
        $log->admin_verified_by = auth()->id();
        if ($request->notes) {
            $log->notes = $request->notes;
        }
        $log->save();

        LogsService::save([
            'type'     => 'verify',
            'module'   => 'Patient Appointment',
            'message'  => 'Admin verified call log #' . $id . ' (' . $log->patient_name . ')',
            'object_id' => $log->patient_id,
            'ip'        => Utility::getIP(),
            'new_response' => serialize(['admin_verified' => $log->admin_verified, 'admin_verified_at' => $log->admin_verified_at, 'admin_verified_by' => $log->admin_verified_by, 'notes' => $log->notes]),
        ]);

        return response()->json(['status' => true, 'message' => 'Call verified successfully']);
    }

    public function convertToAppointment(Request $request, $id)
    {
        $log = $this->autoCallLogService->findWithAppointment($id);

        if ($log->converted_to_appointment) {
            return response()->json(['status' => false, 'message' => 'Already converted to appointment']);
        }

        // Prefer data from linked CallAppointment (booked by AI), fallback to extracted_data
        $linked    = $log->callAppointment;
        $extracted = $log->extracted_data ? json_decode($log->extracted_data, true) : [];

        $locationId = $linked->location_id ?? ($extracted['location_id'] ?? null);
        $serviceId  = $linked->service_id  ?? ($extracted['service_id']  ?? null);

        $appointmentDate = null;
        $appointmentTime = null;

        if ($linked && $linked->date) {
            $appointmentDate = date('Y-m-d', strtotime($linked->date));
            $appointmentTime = $linked->time_slot ?? null;
            if ($appointmentTime) {
                $appointmentDate .= ' ' . $appointmentTime;
            }
        } elseif (!empty($extracted['date'])) {
            $appointmentDate = date('Y-m-d', strtotime($extracted['date']));
            $appointmentTime = $extracted['time_slot'] ?? ($extracted['time'] ?? null);
            if ($appointmentTime) {
                $appointmentDate .= ' ' . date('H:i:s', strtotime($appointmentTime));
                $appointmentTime = date('H:i:s', strtotime($appointmentTime));
            }
        }
     $appointment= $checkAppointment = $this->appointmentService->getPendingByPatientId($log->patient_id);
		
		if ($checkAppointment) {
			$checkAppointment->update(['location_id' => $locationId, "service_id" => $serviceId, 'appointment_date' => $appointmentDate, "appointment_time" => $appointmentTime, 'status' => 'booked']);
		}else{
            $appointment = $this->appointmentService->createFromAiCall([
                'patient_id'       => $log->patient_id,
                'location_id'      => $locationId,
                'service_id'       => $serviceId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'status'           => 'booked',
                'appointment_mode' => 'ai_call',
                'created_at'       => now(),
                'appointment_time_id' => $request->appointment_time_id,
            ]);
        }

        $log->converted_to_appointment = true;
        $log->converted_at  = Carbon::now();
        $log->converted_by  = auth()->id();
        $log->save();

        	$data =[
				'appointment_date' => $appointmentDate,
				'location_id' => $locationId,
				'service_id' => $serviceId,   
                'appointment_time'=> $appointmentTime,
                'status' => 'booked',
                'appoinment_time_id' => $request->appoinment_time_id,
            ];
			$update = $this->patientService->update($data, ['id' => $log->patient_id]);
        // Send confirmation SMS
        if ($log->mobile) {
            $smsMessage = "Your appointment has been confirmed by our team. Appointment ID: {$appointment->id}.";
            $this->smsService->AgencyWiseSmsDynamic($log->patient_id, $log->mobile, $smsMessage);
            $log->confirmation_sms_sent = true;
            $log->save();
        }

        LogsService::save([
            'type'      => 'convert',
            'module'    => 'Patient Appointment',
            'message'   => 'AI call log #' . $id . ' converted to appointment #' . $appointment->id . ' for patient #' . $log->patient_id,
            'object_id' => $log->patient_id,
            'ip'        => Utility::getIP(),
            'new_response' => serialize(['appointment_id' => $appointment->id, 'appointment_date' => $appointmentDate, 'location_id' => $locationId, 'service_id' => $serviceId]),
        ]);

        return response()->json([
            'status'         => true,
            'message'        => 'Converted to appointment successfully. Confirmation SMS sent.',
            'appointment_id' => $appointment->id,
        ]);
    }

    public function sendReminder(Request $request, $id)
    {
        $log = $this->autoCallLogService->findWithAppointment($id);

        if (!$log->mobile) {
            return response()->json(['status' => false, 'message' => 'No mobile number available']);
        }

        // Use linked CallAppointment date/time if available
        $linked  = $log->callAppointment;
        $dateStr = $linked && $linked->date
            ? date('m/d/Y', strtotime($linked->date))
            : 'your scheduled date';
        $timeStr = $linked ? ($linked->time_slot ?? '') : '';

        $resolutionSMSMessage = $this->resolutionSmsTemplateService->getById(31);

         $smsMessage = str_replace('{appointment_date}', $dateStr, $resolutionSMSMessage->message);

        $smsMessage = str_replace('{appointment_time}', $timeStr, $smsMessage);

        $this->smsService->AgencyWiseSmsDynamic($log->patient_id, $log->mobile, $smsMessage);

        $log->reminder_sms_sent = true;
        $log->save();

        LogsService::save([
            'type'     => 'reminder',
            'module'   => 'Patient Appointment',
        'message'  => 'Reminder SMS sent for AI call log #' . $id . ' (' . $log->patient_name . ')',
            'object_id' => $log->patient_id,
            'ip'       => Utility::getIP(),
        ]);

        return response()->json(['status' => true, 'message' => 'Reminder SMS sent successfully']);
    }

    public function saveNotes(Request $request, $id)
    {
        $log = $this->autoCallLogService->findOrFail($id);
        $log->notes = $request->notes;
        $log->save();

         LogsService::save([
            'type'     => 'Notes Added',
            'module'   => 'Patient Appointment',
            'message'  => 'Notes added for AI call log #' . $id . ' (' . $log->patient_name . ')',
            'object_id' => $log->patient_id,
            'ip'       => Utility::getIP(),
            'new_response' => serialize(['notes' => $request->notes]),
        ]);

        return response()->json(['status' => true, 'message' => 'Notes saved']);
    }

    public function updateBooking(Request $request, $id)
    {
        $log    = $this->autoCallLogService->findOrFail($id);
        $linked = $log->callAppointment;

        if (!$linked) {
            return response()->json(['status' => false, 'message' => 'No linked booking found']);
        }

        $oldValues = [
            'mobile'      => $linked->mobile,
            'language'    => $linked->language,
            'date'        => $linked->date,
            'time_slot'   => $linked->time_slot,
            'location_id' => $linked->location_id,
            'service_id'  => $linked->service_id,
            'nurse_id'    => $linked->nurse_id,
        ];

        $linked->mobile      = $request->mobile      ?? $linked->mobile;
        $linked->language    = $request->language     ?? $linked->language;
        $linked->date        = $request->date         ? date('Y-m-d', strtotime($request->date)) : $linked->date;
        $linked->time_slot   = $request->time_slot    ?? $linked->time_slot;
        $linked->location_id = $request->location_id  ?? $linked->location_id;
        $linked->service_id  = $request->service_id
            ? implode(',', array_filter((array) $request->service_id))
            : $linked->service_id;
        $linked->nurse_id    = $request->nurse_id      ?? $linked->nurse_id;
        $linked->save();

        $newValues = [
            'mobile'      => $linked->mobile,
            'language'    => $linked->language,
            'date'        => $linked->date,
            'time_slot'   => $linked->time_slot,
            'location_id' => $linked->location_id,
            'service_id'  => $linked->service_id,
            'nurse_id'    => $linked->nurse_id,
        ];

        LogsService::save([
            'type'         => 'Update Booking',
            'module'       => 'Patient Appointment',
            'message'      => 'Booking #' . $linked->id . ' updated for AI call log #' . $id,
            'object_id'    => $log->patient_id,
            'ip'           => Utility::getIP(),
            'old_response' => serialize($oldValues),
            'new_response' => serialize($newValues),
        ]);

        return response()->json(['status' => true, 'message' => 'Booking updated successfully']);
    }

    public function sendReminderCall(Request $request, $id)
    {
        $log = $this->autoCallLogService->findWithAppointment($id);

        if (!$log->mobile) {
            return response()->json(['status' => false, 'message' => 'No mobile number available']);
        }

        $linked = $log->callAppointment;

        // Time window: 10am–7pm
        $now  = Carbon::now();
        $hour = (int)$now->format('G');
        if ($hour < 10 || $hour >= 19) {
            return response()->json(['status' => false, 'message' => 'Reminder calls can only be sent between 10:00 AM and 7:00 PM']);
        }

        $data = [
            'agent_id'              => config('services.elevenlabs.reminder_agent_id'),
            'agent_phone_number_id' => config('services.elevenlabs.agent_phone_number_id'),
            'to_number'             => $log->mobile,
            'conversation_initiation_client_data' => [
                'dynamic_variables' => [
                    'patient_name'      => (string)($log->patient_name ?? ''),
                    'Patients_Name'     => (string)($log->patient_name ?? ''),
                    'AgencyName'        => (string)($log->agency_name ?? ''),
                    'appointment_date'  => $linked->date ? date('m/d/Y', strtotime($linked->date)) : '',
                    'appointment_time'  => (string)($linked->time_slot ?? ''),
                    'auto_call_log_id'  => (string)$log->id,
                    'patient_mobile'    => (string)($log->mobile ?? ''),
                ],
            ],
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => config('services.elevenlabs.outbound_call_url'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'xi-api-key: ' . config('services.elevenlabs.api_key'),
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT    => 30,
        ]);

        $response  = curl_exec($curl);
        $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlError || ($httpCode !== 200 && $httpCode !== 201)) {
            LogsService::save([
                'type'      => 'Reminder Call Failed',
                'module'    => 'Patient Appointment',
                'message'   => 'Reminder call FAILED for log #' . $id . ' (' . $log->patient_name . '): ' . ($curlError ?: $response),
                'object_id' => $log->patient_id,
                'ip'        => Utility::getIP(),
                'new_response' => serialize(['error' => $curlError ?: $response]),
               
            ]);
            return response()->json(['status' => false, 'message' => 'Reminder call failed — ' . ($curlError ?: 'HTTP ' . $httpCode)]);
        }

        $responseData           = json_decode($response, true);
        $reminderConversationId = $responseData['conversation_id'] ?? null;

        $log->reminder_call_fired_at   = Carbon::now();
        $log->reminder_conversation_id = $reminderConversationId;
        $log->increment('reminder_call_attempts');
        $log->save();

        $reminderAttemptNumber = (int) $log->fresh()->reminder_call_attempts;

        $this->autoCallLogService->createAttempt([
            'auto_call_log_id' => $log->id,
            'call_type'        => 'reminder',
            'attempt_number'   => $reminderAttemptNumber,
            'conversation_id'  => $reminderConversationId,
            'status'           => 'called',
            'fired_at'         => Carbon::now(),
        ]);

        LogsService::save([
            'type'      => 'reminder_call',
            'module'    => 'Patient Appointment',
            'message'   => 'Reminder call fired for log #' . $id . ' (' . $log->patient_name . ')',
            'object_id' => $log->patient_id,
            'ip'        => Utility::getIP(),
            'new_response' => serialize(['conversation_id' => $reminderConversationId, 'attempt_number' => $reminderAttemptNumber, 'response' => $responseData]),
        ]);

        return response()->json(['status' => true, 'message' => 'Reminder call fired successfully']);
    }

    public function bookingDetail($id)
    {
        $booking = $this->callAppointmentService->getById($id);

        // Resolve multiple service names from comma-separated service_id
        $serviceNames = [];
        if ($booking->service_id) {
            $serviceIds   = array_filter(array_map('trim', explode(',', $booking->service_id)));
            $serviceNames = $this->masterService->getNamesByIds($serviceIds);
        }

        // Load linked auto call log separately (avoid with() conflict after manual join)
        $booking->setRelation('autoCallLog', $this->autoCallLogService->find($booking->auto_call_log_id));

        $data['booking']         = $booking;
        $data['locationName']    = $booking->location_name ?? null;
        $data['serviceNames']    = $serviceNames;
        $data['nurseName']    = $booking->nurse_name ? trim($booking->nurse_name) : null;
        $data['title']           = 'AI Booking Detail';
        return view('ai-call-logs.booking-detail', $data);
    }

    public function fireCall($id)
    {
        $log = $this->autoCallLogService->findOrFail($id);

        if ($log->call_status === 'booked') {
            return response()->json(['status' => false, 'message' => 'Patient already booked — call not fired']);
        }

        AutoCallService::fireCall($log);
        $log->refresh();

        LogsService::save([
            'type'      => 'Fire Call',
            'module'    => 'Patient Appointment',
            'message'   => 'Admin manually fired call for log #' . $id . ' (' . $log->patient_name . ')',
            'object_id' => $log->patient_id ?? null,
            'ip'        => Utility::getIP(),
            'new_response' => serialize(['call_status' => $log->call_status,'log' => $log->toArray()]),
        ]);

        if ($log->call_status === 'called') {
            return response()->json(['status' => true, 'message' => 'Call fired successfully']);
        }

        return response()->json(['status' => false, 'message' => 'Call failed — ' . ($log->call_response ?? 'unknown error')]);
    }

    public function getReminderAudio($id)
    {
        $log = $this->autoCallLogService->findOrFail($id);

        if (!$log->reminder_conversation_id) {
            return response()->json(['status' => false, 'message' => 'No reminder conversation ID'], 404);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => config('services.elevenlabs.base_url') . "/convai/conversations/{$log->reminder_conversation_id}/audio",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['xi-api-key: ' . config('services.elevenlabs.api_key')],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $audio       = curl_exec($curl);
        $httpCode    = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);

        if ($httpCode === 200 && $audio) {
            return response($audio, 200)
                ->header('Content-Type', $contentType ?: 'audio/mpeg')
                ->header('Content-Length', strlen($audio))
                ->header('Accept-Ranges', 'bytes');
        }

        return response()->json(['status' => false, 'message' => 'Reminder audio not available'], 404);
    }

    public function fetchReminderConversation($id)
    {
        $log = $this->autoCallLogService->findOrFail($id);

        if (!$log->reminder_conversation_id) {
            return response()->json(['status' => false, 'message' => 'No reminder conversation ID available']);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => config('services.elevenlabs.base_url') . "/convai/conversations/{$log->reminder_conversation_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'xi-api-key: ' . config('services.elevenlabs.api_key'),
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $log->reminder_transcript = isset($data['transcript']) ? json_encode($data['transcript']) : null;
            $log->save();
        }

        return response()->json([
            'status'          => true,
            'transcript'      => $log->reminder_transcript ? json_decode($log->reminder_transcript, true) : null,
            'conversation_id' => $log->reminder_conversation_id,
        ]);
    }

    public function getAttemptAudio($attemptId)
    {
        $attempt = PatientAutoCallAttempt::findOrFail($attemptId);

        if (!$attempt->conversation_id) {
            return response()->json(['status' => false, 'message' => 'No conversation ID for this attempt'], 404);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => config('services.elevenlabs.base_url') . "/convai/conversations/{$attempt->conversation_id}/audio",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['xi-api-key: ' . config('services.elevenlabs.api_key')],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $audio       = curl_exec($curl);
        $httpCode    = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);

        if ($httpCode === 200 && $audio) {
            return response($audio, 200)
                ->header('Content-Type', $contentType ?: 'audio/mpeg')
                ->header('Content-Length', strlen($audio))
                ->header('Accept-Ranges', 'bytes');
        }

        return response()->json(['status' => false, 'message' => 'Audio not available'], 404);
    }

    public function fetchAttemptTranscript($attemptId)
    {
        $attempt = PatientAutoCallAttempt::findOrFail($attemptId);

        if (!$attempt->conversation_id) {
            return response()->json(['status' => false, 'message' => 'No conversation ID for this attempt']);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => config('services.elevenlabs.base_url') . "/convai/conversations/{$attempt->conversation_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'xi-api-key: ' . config('services.elevenlabs.api_key'),
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $attempt->transcript = isset($data['transcript']) ? json_encode($data['transcript']) : null;
            $attempt->save();
        }

        return response()->json([
            'status'     => true,
            'transcript' => $attempt->transcript ? json_decode($attempt->transcript, true) : null,
        ]);
    }

    public function patientCallLogs($patientId)
    {
        $logs = $this->autoCallLogService->getByPatientId($patientId);

        return view('patient._partial.all_tabs_section.ai_call_logs_section', compact('logs'));
    }

    public function addManualCall(Request $request, $patientId)
    {
        $patient = $this->patientService->getDetailByIdNew($patientId);

        if (!$patient) {
            return response()->json(['status' => false, 'message' => 'Patient not found'], 404);
        }

        $mobile = $request->mobile ?: $patient->mobile;

        if (!$mobile) {
            return response()->json(['status' => false, 'message' => 'No mobile number available for this patient'], 422);
        }

        $patientName = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
        $agencyName  = $patient->agency_name ?? '';

        // Check for an existing pending/called log — re-fire instead of duplicating
        $existing = PatientAutoCallLog::where('patient_id', $patientId)
            ->whereIn('call_status', ['pending', 'called'])
            ->latest()
            ->first();

        if ($existing) {
            $success = AutoCallService::fireCall($existing);

            LogsService::save([
                'type'      => 'Manual Call',
                'module'    => 'Patient Appointment',
                'message'   => 'Manual re-fire on existing AI call log #' . $existing->id . ' for patient #' . $patientId,
                'object_id' => $patientId,
                'ip'        => Utility::getIP(),
                'new_response' => serialize(['call_status' => $existing->call_status,'log' => $existing->toArray()]),
            ]);

            return response()->json([
                'status'      => $success,
                'message'     => $success ? 'Call fired on existing log #' . $existing->id : 'Call fire failed',
                'log_id'      => $existing->id,
                'is_existing' => true,
            ]);
        }

        // No active log — create and fire immediately (no scheduling delay)
        $log = PatientAutoCallLog::create([
            'patient_id'           => (int) $patientId,
            'mobile'               => '1' . ltrim($mobile, '1'),
            'patient_name'         => $patientName,
            'agency_name'          => $agencyName,
            'sms_link'             => '',
            'sms_sent_at'          => Carbon::now(),
            'appointment_deadline' => Carbon::now(),
            'call_status'          => 'pending',
            'call_attempts'        => 0,
            'triggered_by'         => 'manual',
            'agency_id'            => $patient->agency_id ?? null,
            'service_id'           => $patient->service_id ?? null
        ]);

        $success = AutoCallService::fireCall($log);

        LogsService::save([
            'type'      => 'Manual Call',
            'module'    => 'Patient Appointment',
            'message'   => 'New AI call log #' . $log->id . ' created and fired manually for patient #' . $patientId,
            'object_id' => $patientId,
            'ip'        => Utility::getIP(),
            'new_response' => serialize(['call_status' => $log->call_status,'log' => $log->toArray()]),
        ]);

        return response()->json([
            'status'      => $success,
            'message'     => $success ? 'Call created and fired successfully' : 'Log created but call fire failed',
            'log_id'      => $log->id,
            'is_existing' => false,
        ]);
    }
}
