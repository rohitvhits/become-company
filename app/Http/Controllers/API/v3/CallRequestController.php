<?php

namespace App\Http\Controllers\API\V3;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Services\CallAppointmentService;
use App\Services\LogsService;
use App\Services\PatientAutoCallLogService;
use App\Services\SmsService;
use App\Services\LocationScheduleService;
use App\Services\LocationMasterService;
use App\Services\PatientService;
use App\Services\AppointmentService;
use App\Services\DisableDateService;
use App\Services\TelehealthLocationScheduleEventService;

class CallRequestController extends BaseController
{
    public $successStatus = 200;

    protected $smsService;
    protected $locationScheduleService;
    protected $locationMasterService;
    protected $patientService;
    protected $disableDateService;
    protected $telehealthLocationScheduleEventService;
    protected $autoCallLogService;
    protected $callAppointmentService;
    protected $appointmentService;

    public function __construct(
        SmsService             $smsService,
        LocationScheduleService $locationScheduleService,
        LocationMasterService   $locationMasterService,
        PatientService          $patientService,
        DisableDateService      $disableDateService,
        TelehealthLocationScheduleEventService $telehealthLocationScheduleEventService,
        PatientAutoCallLogService $autoCallLogService,
        CallAppointmentService    $callAppointmentService,
        AppointmentService        $appointmentService
    ) {
        $this->smsService             = $smsService;
        $this->locationScheduleService = $locationScheduleService;
        $this->locationMasterService   = $locationMasterService;
        $this->patientService          = $patientService;
        $this->disableDateService      = $disableDateService;
        $this->telehealthLocationScheduleEventService = $telehealthLocationScheduleEventService;
        $this->autoCallLogService      = $autoCallLogService;
        $this->callAppointmentService  = $callAppointmentService;
        $this->appointmentService      = $appointmentService;
    }

    public function basicAuth(Request $request)
    {
        // Retrieve the Authorization header
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Basic ')) {
            return response()->json(['status' => false, 'message' => 'Authorization header missing or invalid'], 401);
        }

        // Decode the Base64 encoded username:password
        $encodedCredentials = substr($authHeader, 6);
        $decodedCredentials = base64_decode($encodedCredentials);

        if (!$decodedCredentials || !str_contains($decodedCredentials, ':')) {
            return response()->json(['status' => false, 'message' => 'Invalid credentials format'], 401);
        }

        // Split the username and password
        [$username, $password] = explode(':', $decodedCredentials, 2);

        // Check the username and password 
        $validUsername = env('API_USERNAME');
        $validPassword = env('API_PASSWORD');

        if ($username === $validUsername && $password === $validPassword) {
            return response()->json(['status' => true, 'message' => 'Authentication successful']);
        }

        return response()->json(['status' => false, 'message' => 'Invalid username or password'], 401);
    }

    public function getTimeSlotsByLanguageAndDate(Request $request)
    {
        $startDate   = $request->input('date');
        $locationId  = $request->input('location_id');

        if (!$startDate || !$locationId) {
            return response()->json(['status' => false, 'message' => 'date and location_id are required'], 422);
        }

        $timestamp = strtotime($startDate);
        $day       = date('l', $timestamp);

        // All schedule slots for this location + day
    $query    = $this->locationScheduleService->getSearchLocation($day, $locationId);
        $location = $this->locationMasterService->getDetailbyId($locationId);

        // Disabled date/time blocks
        $dateArray      = $this->disabledDate($startDate);
        $finalTimeArray = $dateArray['time'];

        // Find the earliest disabled cut-off time for this date
        $smallerTime    = null;
        foreach ($finalTimeArray as $time => $val) {
            $stopTime    = $location->stop_time ?? null;
            $currentSmall = ($stopTime && strtotime($time) > strtotime($stopTime)) ? $stopTime : $time;
            if ($smallerTime === null || strtotime($currentSmall) < strtotime($smallerTime)) {
                $smallerTime = $currentSmall;
            }
        }

        $checkStopTime = (isset($location->stop_date) && $timestamp == strtotime($location->stop_date)) ? 1 : 0;

        $final = [];
        foreach ($query as $vs) {
            // Check remaining slots
            $booked       = $this->patientService->getCountByTimeScheduleNew($vs->id, $startDate);
            $slotsLeft    = max(0, $vs->slot - $booked);

            if ($slotsLeft <= 0) {
                continue;
            }

            $startTime12 = date('h:i A', strtotime($vs->start_time));
            $endTime12   = date('h:i A', strtotime($vs->end_time));

            // Apply stop-time / disabled-date filtering
            if ($checkStopTime == 1) {
                $cutoff = $smallerTime ?? ($location->stop_time ?? null);
                if ($cutoff && date('H:i', strtotime($vs->start_time)) >= date('H:i', strtotime($cutoff))) {
                    continue;
                }
            } elseif (!empty($finalTimeArray)) {
                $include = false;
                foreach ($finalTimeArray as $time => $dates) {
                    if (trim($dates) == $startDate && date('H:i', strtotime($vs->start_time)) < date('H:i', strtotime($time))) {
                        $include = true;
                        break;
                    }
                }
                if (!$include) continue;
            }

            $final[] = [
                'id'         => $vs->id,
                'start_time' => $startTime12,
                'end_time'   => $endTime12,
                'slots_left' => $slotsLeft,
            ];
        }

        return response()->json([
            'status'     => true,
            'time_slots' => $final,
        ]);
    }

    private function disabledDate(string $startDate): array
    {
        $dateArray      = $this->disableDateService->disableDateAllDataWithTime();
        $finalTimeArray = [];

        if (!empty($dateArray)) {
            foreach ($dateArray as $key => $vals) {
                foreach (explode(',', $vals) as $dat) {
                    if (trim($dat) == $startDate) {
                        $finalTimeArray[date('H:i', strtotime($key))] = trim($dat);
                    }
                }
            }
        }

        return ['time' => $finalTimeArray];
    }

    public function AddCallAppointment(Request $request)
    {
        
        // $basicAuth = self::basicAuth($request);
        // if (!$basicAuth->getData()->status) {
        //     return $basicAuth;
        // }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'language' => 'required|string',
            'date' => 'required|date',
            'time_slot' => 'required|string',
            // 'nurse_id' => 'required|integer',
        ]);

        
        if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}

        $autoCallLogId = $request->auto_call_log_id ? (int)$request->auto_call_log_id : null;
        $autoCallLog   = $autoCallLogId ? $this->autoCallLogService->find($autoCallLogId) : null;

        $add = $this->callAppointmentService->create([
            'language'         => $request->language,
            'date'             => $request->date,
            'time_slot'        => $request->time_slot,
            'nurse_id'         => $request->nurse_id,
            'name'             => $request->name,
            'mobile'           => $request->mobile,
            'location_id'      => $request->location_id,
            'service_id'       => $autoCallLog->service_id ?? null,
            'auto_call_log_id' => $autoCallLogId,
        ]);

        if (!$add) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add appointment'
            ], 500);
        }

        // Mark the PatientAutoCallLog as booked so the queued job is suppressed
        if ($autoCallLog && in_array($autoCallLog->call_status, ['pending', 'called'])) {
            $autoCallLog->location_id  = $request->location_id;
            $autoCallLog->call_status  = 'booked';
            $autoCallLog->booked_at    = now();
            $autoCallLog->save();
        }

        // Auto-verify and auto-convert to appointment after booking is added
      if ($autoCallLog) {
            if (!$autoCallLog->admin_verified) {
                $autoCallLog->admin_verified    = true;
                $autoCallLog->admin_verified_at = now();
                $autoCallLog->admin_verified_by = auth()->id();
                $autoCallLog->save();
            }
            $appointmentTime = null;
            if (!$autoCallLog->converted_to_appointment) {
                $appointmentDate = date('Y-m-d', strtotime($add->date));
                if ($add->time_slot) {
                    $appointmentDate .= ' ' . date('H:i:s', strtotime($add->time_slot));
                    $appointmentTime = date('H:i:s', strtotime($add->time_slot));
                }

                $checkAppointment = $this->appointmentService->getPendingByPatientId($autoCallLog->patient_id);

                if ($checkAppointment) {
                    $checkAppointment->update([
                        'location_id'      => $add->location_id,
                        'service_id'       => $add->service_id,
                        'appointment_date' => $appointmentDate,
                        'appointment_time' => $appointmentTime,
                        'status'           => 'booked',
                    ]);
                    $appointment = $checkAppointment;
                } else {
                    $appointment = $this->appointmentService->createFromAiCall([
                        'patient_id'       => $autoCallLog->patient_id,
                        'location_id'      => $add->location_id,
                        'service_id'       => $add->service_id,
                        'appointment_date' => $appointmentDate,
                        'appointment_time' => $appointmentTime,
                        'status'           => 'booked',
                        'appointment_mode' => 'ai_call',
                        'created_at'       => now(),
                        
                    ]);
                }

                $autoCallLog->converted_to_appointment = true;
                $autoCallLog->converted_at             = now();
                $autoCallLog->converted_by             = auth()->id();
                $autoCallLog->save();

                $this->patientService->update([
                    'appointment_date' => $appointmentDate,
                    'location_id'      => $add->location_id,
                    'service_id'       => $add->service_id,
                    'appointment_time' => $appointmentTime,
                    'status'           => 'booked',
                    'appoinment_time_id' => $request->appoinment_time_id,
                ], ['id' => $autoCallLog->patient_id]);

                LogsService::save([
                    'type'         => 'convert',
                    'module'       => 'Patient Appointment',
                    'message'      => 'AI call log #' . $autoCallLog->id . ' auto-converted to appointment #' . $appointment->id . ' for patient #' . $autoCallLog->patient_id,
                    'object_id'    => $autoCallLog->patient_id,
                    'new_response' => serialize([
                        'call_appointment_id' => $add->id,
                        'appointment_id'      => $appointment->id,
                        'appointment_date'    => $appointmentDate,
                        'appointment_time'    => $appointmentTime,
                        'location_id'         => $add->location_id,
                        'service_id'          => $add->service_id,
                        'language'            => $add->language,
                        'mobile'              => $add->mobile,
                        'name'                => $add->name,
                        'nurse_id'            => $add->nurse_id,
                        'auto_verified_at'    => $autoCallLog->admin_verified_at,
                        'auto_converted_at'   => $autoCallLog->converted_at,
                        'triggered_by'        => 'AddCallAppointment',
                    ]),
                ]);
            }
        } 

        // Send confirmation SMS
        if ($request->mobile) {
            $smsMessage = "Your booking is confirmed. Your reference ID is: {$add->id}. Please keep this for your records.";
            $this->smsService->AgencyWiseSmsDynamic($add->id, $request->mobile, $smsMessage);
        }

        return response()->json([
            'status' => true,
            'message' => 'Appointment added successfully',
            'data' => $add
        ]);
    }

    
    public function makeCall(Request $request)
    {
        $sid    = env("TWILIO_SID");
        $token  = env("TWILIO_AUTH_TOKEN");
        $from   = env("TWILIO_NUMBER");
        $name = $request->name; // patient name
        $agencyName =$request->agency_name; // agency name   

        $to = $request->input('to'); // patient phone number


            $ch = curl_init();
            

            curl_setopt_array($ch, [
                CURLOPT_URL => "https://api.twilio.com/2010-04-01/Accounts/".$sid."/Calls.json",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_USERPWD => $sid.":".$token,
                CURLOPT_POSTFIELDS => http_build_query([
                    "To" => $to,
                    "From" => $from,
                    "Url" => route('voice.webhook', ['Patients_Name' => urlencode($name), 'mobile' => urlencode($to),'AgencyName' => urlencode($agencyName)])
                ])
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            dd(json_decode($response, true));
        
    }
}
