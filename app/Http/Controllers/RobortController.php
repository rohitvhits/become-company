<?php

namespace App\Http\Controllers;
use App\Services\RobortService;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RobortHelper;
use App\Model\Robort;
use App\Agency;
use App\Helpers\HHAPatientHelper;
use App\Helpers\Utility;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Model\EmmacareReferalTable;
use App\Services\LogsService;
use App\Services\DocumentPatientService;
use App\Services\SendDocumentRemoteLogService;

class RobortController extends Controller
{
    protected  $robortService;
    protected  $patientService;
    protected $patientServicesRequest;
    protected $patientWiseServicesRequests;
    protected $documentPatientService;
    protected $sendDocumentRemoteLogService;

    protected const ERROR_MSG ="Sorry, something went wrong. Please try again.";
    protected const REFERRAL_UUID_MISSING="Referral Uuid is Missing";

    public function __construct(RobortService $robortService,PatientService $patientService,PatientServicesRequest $patientServicesRequest,PatientWiseServicesRequests $patientWiseServicesRequests,DocumentPatientService $documentPatientService,SendDocumentRemoteLogService $sendDocumentRemoteLogService)
    {
        $this->middleware('permission:robort-list', ['only' => ['index', 'robortAjaxList']]);
        $this->middleware('permission:add-robort-appointment', ['only' => ['saveRobortAppointment']]);
        $this->robortService = $robortService;
        $this->patientService = $patientService;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->documentPatientService = $documentPatientService;
        $this->sendDocumentRemoteLogService = $sendDocumentRemoteLogService;
    }

    public function index(){
        $data['menu'] = "user";
        $data['auth'] = $data['user'] = $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        if (in_array($user['user_type_fk'], array(3, 4, 5, 6))) {
            abort(404);
        }
        $data['agencyList'] = Agency::getRemoteFocusAgencies();
        return view("robort.robot_list", $data);
    }

    public function robortAjaxList(Request $request)
    {
        $data['searchData'] = $request->all();
        $data['agencyList'] = Agency::getRemoteFocusAgencies();
        $data['query'] = $this->robortService->getRobortList($request->all());
       
        return view("robort.robot_ajax", $data);
    }

    public function saveRobortAppointment(Request $request){
        $validator = Validator::make($request->all(), [
            'appointment_ids' => 'required',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }
        if (empty($request->appointment_ids)) {
            return $this->errorResponse(self::ERROR_MSG, 500);
        }

        foreach($request->appointment_ids as $appointmentId){
            $response = $this->robortService->getDetailsById($appointmentId);
            if (!isset($response->id)) {
                continue;
            }

            $patientId = $this->savePatient($request, $response);
            if(!$patientId){
                return $this->errorResponse(self::ERROR_MSG, 500);
            }
           
            $this->robortService->update(['appointment_id' => $patientId], ['id' => $appointmentId]);
            $this->savePatientServices($request, $patientId);
            $this->logAppointment($patientId, $response, $request->type);
            return $this->successResponse("Appointment successfully added");
        }
        return $this->errorResponse(self::ERROR_MSG, 500);
    }

    public function patientOrnTrn(Request $request){
        $getDetails = $this->robortService->getDetailsByPatientWithAgencyId($request->robort_id,$request->agency_id);
        if(isset($getDetails->externalId) && $getDetails->externalId !=""){
            $loginDetails = RobortHelper::getLogin($getDetails->agencyDetails->robort_grant_type,$getDetails->agencyDetails->robort_user_name,$getDetails->agencyDetails->robort_user_password);
            $query = RobortHelper::PatientORUTRN($getDetails->externalId,$loginDetails['access_token'],$request->page);
            return response()->json(['error_msg' => "Success", 'data' => array($query)], 200);
        }
        return response()->json(['error_msg' => self::REFERRAL_UUID_MISSING, 'data' =>array()], 500);
    }
    
    public function patientReadingList(Request $request){
        $getDetails = $this->robortService->getDetailsByPatientWithAgencyId($request->robort_id,$request->agency_id);
        if(isset($getDetails->externalId) && $getDetails->externalId !=""){
            $query = RobortHelper::fetchPatientReadingList($getDetails,$request->page);
            return response()->json(['error_msg' => "Success", 'data' => array($query)], 200);
        }
        return response()->json(['error_msg' => self::REFERRAL_UUID_MISSING, 'data' =>array()], 500);
    }
    
    public function patientMedicationList(Request $request){
        $getDetails = $this->robortService->getDetailsByPatientWithAgencyId($request->robort_id,$request->agency_id);
        if(isset($getDetails->externalId) && $getDetails->externalId !=""){
            $response = RobortHelper::fetchPatientMedicationsList($getDetails,$request->page);
            return response()->json(['error_msg' => "Success", 'data' =>$response], 200);
        }
        return response()->json(['error_msg' => self::REFERRAL_UUID_MISSING, 'data' =>array()], 500);
    }

    public function loadHHADicipline(Request $request){
        $getDetails = $this->robortService->getDetailsByPatientWithAgencyId($request->id,$request->agency_id);
        $getHHADicipline="";
        if(isset($getDetails->externalId) && $getDetails->externalId !=""){
            $getHHADicipline = HHAPatientHelper::getHHADicipline($getDetails);
        }
       
        return response()->json(['error_msg' => "Success", 'data' => array('dicipline'=>$getHHADicipline)], 200);
    }
    
    public function getRemoteDetails(Request $request){
        $query = $this->patientService->getDetailByIdEncrypt(sha1($request->id));
        $data = [];
        if(isset($query->emmacare_referral_uuid) && $query->emmacare_referral_uuid !=""){
           return response()->json(['error_msg' => "A referral with the same name and DOB already exists", 'data' =>[]], 422);
        }
        if(isset($query->id)){
            $getRobortDetails = $this->robortService->getDetailsByPatientWithAgencyId($query->robort_id,$query->agency_id);
            
            $query->externalId = $getRobortDetails->externalId??"";
            $details = [];
            $details['id'] = $query->id;
            $details['firstName'] = $query->first_name;
            $details['lastName'] = $query->last_name;
            $details['middleName'] = $query->middle_name;
            $details['dob'] = $query->dob;
            $details['note'] = $query->note;
            $details['gender'] = strtolower($query->gender);
            $details['primaryLanguage'] = $query->language;
            $details['externalId'] = $getRobortDetails->externalId??"";
            $details['phones'] = $query->mobile;
            $details['patient_code'] = $query->patient_code;
            $dadetailsa['insurance_id'] = $query->insurance_id;
            $details['agency_id'] = $query->agency_id;
            $address1= "";
            if($query->address1 !=""){
                $address1= $query->address1;
            }
            $address2= "";
            if($query->address1 !=""){
                $address2= ', '.$query->address2;
            }
            $details['address'] = $address1.','.$address2;
            $details['city'] = $query->city;
            $details['state'] = $query->state;
            $details['zip'] = $query->zip_cide;
            $data['basic_details'] = $details;
        }
       
        $getInsuranceDetails = RobortHelper::getInsuranceDetails($query->agency_id);
        $filtered = collect($getInsuranceDetails)->where('type', 27)->values()->all();
        $data['insurance'] = $filtered;

        $filteredAddressType = collect($getInsuranceDetails)->where('type', 3)->values()->all();
        $data['addressType'] = $filteredAddressType;

        return response()->json(['error_msg' => "Success", 'data' =>$data], 200);
    }
    
    public function sendDetailsForRemote(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), $this->getValidationRules());

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $saveData = $this->prepareSaveData($request, $user);
        $getDetails = RobortHelper::sendReferral($request->remote_agency_id, $saveData);

        if (isset($getDetails['errors'])) {
            return $this->remoteApiErrorResponse($getDetails['errors']);
        }
       $this->emmacareUploadDocument($request,$getDetails['uuid']);
        return $this->saveReferralAndLog($request, $user, $saveData, $getDetails);
    }

    public function uploadRemoteDocument(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
			'upload_document' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
            $response = $this->robortService->getDetailsById($request->remote_id);
         
            if(isset($response->uuid) && $response->uuid !=""){
                $uploadEmmacarePortal = [
                    'document'=>$request->file('upload_document'),
                    'note'=>$request->note
                ];
                $getDetails = RobortHelper::uploadDocument($response->agency_id,$uploadEmmacarePortal,$response->uuid);
               
                $error = "";
                if(isset($getDetails['errors'])){
                    $cnt =1;
                    foreach($getDetails['errors'] as $errr){
                        $error .=$cnt.'.'.$errr[0].'<br>';
                        $cnt++;
                    }

                    return response()->json(['error_msg' => $error, 'data' =>array()], 500);
                }else{

                    if(isset($getDetails['message'])){
                        return response()->json(['error_msg' =>$getDetails['message'], 'data' =>array()], 500);
                    }else{
                        $upload_document = $request->file('upload_document');
                        $name  = $upload_document->getClientOriginalName();
                        $requestedResponse = $request->except(['_token', 'upload_document']);
                        $data = [
                            'record_id'=>$response->appointment_id,
                            'remote_id'=>$response->id,
                            'uploaded_file'=>$name,
                            'type'=>'Upload Document',
                            'note'=>$request->note,
                            'created_by'=>auth()->user()->id,
                            'send_response'=>serialize($requestedResponse),
                            'return_response'=>serialize($getDetails),
                        ];
                        
                        $saveDetails = new EmmacareReferalTable($data);
                        $saveDetails->save();
                        if($saveDetails->id){
                            $this->robortService->update(array('document_upload'=>$name,'document_upload_date'=>date('Y-m-d H:i:s'),'document_upload_by'=>$user->id,'document_upload_note'=>$request->note),array('id'=>$response->id));
                            return response()->json(['error_msg' => "File uploaded successfully", 'data' =>array()], 200);
                        }
                    }
                    
                }
            }
            return response()->json(['error_msg' => self::REFERRAL_UUID_MISSING, 'data' =>array()], 500);
        }
    }

    /**
     * Save Patient
     */
    private function savePatient(Request $request, $response)
    {
        $serviceIds = $request->service_id ?? [];
        $status = "Pending";
        if(strtolower($request->type) =='patient'){
            $status = Utility::getStatusFromServiceId($serviceIds);
        }
        $finalArray = [
            'first_name'   => $response->firstName,
            'last_name'    => $response->lastName,
            'full_name'    => $response->firstName . ' ' . $response->lastName,
            'patient_code' => $response->patientId,
            'type'         => $request->type,
            'robort_id'    => $response->id,
            'service_id'   => implode(',', $serviceIds),
            'dob'          => $response->dob,
            'gender'       => $response->gender,
            'agency_id'    => $response->agency_id,
            'diciplin'     => $request->diciplin_id,
            'referral_type'=> 'Remote Focus',
            'status'=>$status
        ];

        return $this->patientService->save($finalArray);
    }
    /**
     * Save Patient Services if required
     */
    private function savePatientServices(Request $request, $patientId)
    {
        $services = $request->service_id ?? [];

        if (empty($services)) {
            return;
        }
        $status = "Pending";
        if(strtolower($request->type) =='patient'){
            $status = Utility::getStatusFromServiceId($services);
        }
        
        $patientServiceLastId = $this->patientServicesRequest->save([
            'patient_id'     => $patientId,
            'follow_up_date' => null,
            'due_date'       => null,
            'status'         => $status,
            'created_at'     => now(),
            'created_by'     => auth()->id(),
            'completed_date' => null,
            'completed_by'   => null,
            'flag'           => 1,
        ]);

        foreach ($services as $serviceId) {
            $this->patientWiseServicesRequests->save([
                'patient_id'                 => $patientId,
                'service_id'                 => $serviceId,
                'patient_service_request_id' => $patientServiceLastId,
            ]);
        }

        if(strtolower($request->type) =='patient'){
            Utility::saveResolutionLogForms($status,$patientServiceLastId,$patientId);
        }
        
    }
    /**
     * Log Appointment
     */
    private function logAppointment($patientId, $response, $type)
    {
        $logData = [
            'type'        => 'Remote Focus',
            'link'        => url('/add-appointment-robort'),
            'module'      => 'Patient Appointment',
            'object_id'   => $patientId,
            'message'     => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has added Appointment',
            'new_response'=> serialize([
                'first_name' => $response->firstName,
                'last_name'  => $response->lastName,
                'type'       => $type,
            ]),
            'ip'          => Utility::getIP(),
        ];

        LogsService::save($logData);
    }
    /**
     * JSON Response Helpers
     */
    private function errorResponse($message, $code)
    {
        return response()->json(['error_msg' => $message, 'status' => 0, 'data' => []], $code);
    }

    private function successResponse($message)
    {
        return response()->json(['error_msg' => $message, 'status' => 1, 'data' => []], 200);
    }

    /**
     * Validation rules for request
     */
    private function getValidationRules(): array
    {
        return [
            'remote_first_name'      => 'required',
            'remote_last_name'       => 'required',
            'remote_dob'             => 'required',
            'remote_mobile'          => 'required',
            'remote_gender'          => 'required',
            'remote_language'        => 'required',
            'remote_referral_source' => 'required',
            'remote_icd10'           => 'required',
            'remote_prognosis'       => 'required',
            'remote_start_date'      => 'required',
            'remote_end_date'        => 'required',
        ];
    }

    private function validationErrorResponse($validator)
    {
        return response()->json([
            'error_msg' => $validator->errors()->first(),
            'status'    => 0,
            'data'      => []
        ], 422);
    }

    private function remoteApiErrorResponse(array $errors)
    {
        $error = '';
        $cnt   = 1;
        foreach ($errors as $err) {
            $error .= $cnt . '.' . $err[0] . '<br>';
            $cnt++;
        }

        return response()->json(['error_msg' => $error, 'data' => []], 500);
    }
    /**
     * Prepare payload for API
     */
    private function prepareSaveData(Request $request, $user): array
    {
        return  [
            "firstName"        => $request->remote_first_name,
            "lastName"         => $request->remote_last_name,
            "middleName"       => $request->remote_middle_name,
            "dob"              => $request->remote_dob,
            "note"             => $request->remote_notes,
            "gender"           => $request->remote_gender,
            "referralSource"   => ["uuid" => $request->remote_referral_source],
            "agencyCaseManager"=> [
                "firstName" => $user->first_name,
                "lastName"  => $user->last_name
            ],
            "externalId"       => $request->remote_ext_id,
            "addresses"        => [[
                'address' => $request->remote_address,
                'city'    => $request->remote_city,
                'state'   => $request->remote_state,
                'zip'     => $request->remote_zip,
                'type'    => $request->remote_type
            ]],
            "primaryLanguage"  => $request->remote_language,
            "phones"           => [['type' => "Home", 'number' => $request->remote_mobile]],
            "bestDayToCall"    => (object)$this->prepareBestDayToCall($request),
            "bestTimeToCall"   => (object)$this->prepareBestTimeToCall($request),
            "referredFor"      => $request->remote_referred_to_far
                                    ? array_map('intval', $request->remote_referred_to_far)
                                    : [],
            "diagnoses"        => $this->prepareDiagnoses($request),
            "insurances"       => $this->prepareInsurances($request)
        ];
    }

    private function prepareBestDayToCall(Request $request): array
    {
        if($request->remote_bestday_to_call ==""){
            return [];
        }
        $days = array_filter($request->remote_bestday_to_call ?? [], fn($day) => $day !== "Other");
        $days = array_map('intval', $days);
        $days1 = array_values($days);
        
        $otherCallDay =null;
        if(isset($request->remote_best_call_day_other) && $request->remote_best_call_day_other !=""){
            $otherCallDay = $request->remote_best_call_day_other;
        }
        
        return [
            'days'  => $days1,
            'other' => $otherCallDay
        ];
    }

    private function prepareBestTimeToCall(Request $request): array
    {
        if($request->remote_best_time ==""){
            return [];
        }
        $times = array_filter($request->remote_best_time ?? [], fn($time) => $time !== "Other");
        
        $otherCallTime =null;
        if(isset($request->remote_best_time_to_call_other) && $request->remote_best_time_to_call_other !=""){
            $otherCallTime = $request->remote_best_time_to_call_other;
        }

        return [
            'periods' => $times,
            'other'   => $otherCallTime
        ];
    }

    private function prepareDiagnoses(Request $request): array
    {
        if (!$request->remote_icd10) {
            return [];
        }

        return [[
            'icd10'     => $request->remote_icd10,
            'prognosis' => $request->remote_prognosis,
            'startDate' => date('Y-m-d', strtotime($request->remote_start_date)),
            'end_date'  => date('Y-m-d', strtotime($request->remote_end_date)),
        ]];
    }

    private function prepareInsurances(Request $request): array
    {
        if (!$request->remote_insurance_id) {
            return [];
        }

        return [[
            'insurance'    => $request->remote_insurance_name,
            'policyNumber' => $request->remote_insurance_id
        ]];
    }

    /**
     * Save referral locally & log activity
     */
    private function saveReferralAndLog(Request $request, $user, array $saveData, array $getDetails)
    {
        $upload_document = $request->file('upload_document');
        $name  = $upload_document->getClientOriginalName();
        $saveData['uploaded_file'] = $name;

        $data = [
            'record_id'      => $request->record_id,
            'first_name'     => $request->remote_first_name,
            'middle_name'    => $request->remote_middle_name,
            'last_name'      => $request->remote_last_name,
            'dob'            => $request->remote_dob,
            'note'           => $request->remote_notes,
            'gender'         => $request->remote_gender,
            'primaryLanguage'=> $request->remote_language,
            'externalId'     => $request->remote_ext_id,
            'phones'         => $request->remote_mobile,
            'insurance'      => $request->remote_insurance_id,
            'send_response'  => serialize($saveData),
            'referral_uid'   => $request->remote_referral_source,
            'created_by'     => $user->id,
            'return_response'=> serialize($getDetails),
            'uploaded_file'=>$name,
        ];

        $saveDetails = new EmmacareReferalTable($data);
        $saveDetails->save();

        if ($saveDetails->id) {
            $oldDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->record_id);

            $this->patientService->update(
                ['emmacare_referral_uuid' => $getDetails['uuid']??""],
                ['id' => $request->record_id]
            );

            $newData   = ['id'=>$request->record_id,'emmacare_referral_uuid' => $getDetails['uuid']??""];
            $ipaddress = Utility::getIP();

            LogsService::save([
                'type'         => 'Send Emmacare Details',
                'link'         => url('/send-remote-details'),
                'module'       => 'Patient Appointment',
                'object_id'    => $request->record_id,
                'message'      => $user->first_name . ' ' . $user->last_name . ' has Send Emmacare Details',
                'old_response' => serialize($oldDetails),
                'new_response' => serialize($newData),
                'ip'           => $ipaddress,
            ]);

            return response()->json(['error_msg' => "Data successfully send", 'data' => []], 200);
        }
    }

    private function emmacareUploadDocument($data,$uuid){
        
        if($uuid !=""){
            $uploadEmmacarePortal = [
                'document'=>$data->file('upload_document'),
                'note'=>$data->note
            ];
            $getDetails = RobortHelper::uploadDocument($data['remote_agency_id'],$uploadEmmacarePortal,$uuid);
            $error = "";
         
            if(isset($getDetails['errors'])){
                $cnt =1;
                foreach($getDetails['errors'] as $errr){
                    $error .=$cnt.'.'.$errr[0].'<br>';
                    $cnt++;
                }

                return response()->json(['error_msg' => $error, 'data' =>array()], 500);
            }
            if(isset($getDetails['message'])){
                return response()->json(['error_msg' =>$getDetails['message'], 'data' =>array()], 500);
            }else{
                return 1;
            }
        }
    }

    public function patientRemoteCarePlan(Request $request){
        $response = $this->robortService->getDetailsById($request->id);
        if(isset($response->externalId) && $response->externalId !=""){
            $getDetails = RobortHelper::getCarePlanList($response->agency_id,$response->externalId);
            $finalRecord=[];
            if(isset($getDetails['reviewedAt'])){
                $finalRecord = [$getDetails];
            }
            return response()->json(['error_msg' => "Success", 'data' => $finalRecord], 200);
        }
        return response()->json(['error_msg' => self::REFERRAL_UUID_MISSING, 'data' =>array()], 200);
    }

    public function patientRemoteActivityLog(Request $request){
        $response = $this->robortService->getDetailsById($request->id);
        if(isset($response->externalId) && $response->externalId !=""){
            $getDetails = RobortHelper::getPatientRemoteActivityLog($response->agency_id,$response->externalId,$request->page);
            return response()->json(['error_msg' => "Success", 'data' => array($getDetails)], 200);
        }
        return response()->json(['error_msg' => self::REFERRAL_UUID_MISSING, 'data' =>array()], 200);
    }

    public function getDemoraphicDetails(Request $request){
        $response = $this->robortService->getDetailsByPatientWithAgencyId($request->robort_id,$request->agency_id);
        if(isset($response->externalId) && $response->externalId !=""){
            $getDetails = RobortHelper::getPatientDemographicDetails($response->agency_id,$response->externalId);
            $finalDemographicDetails = [];
            if(isset($getDetails['meta']['totalItems']) && $getDetails['meta']['totalItems'] !=0){
                $finalDemographicDetails = [$getDetails];
            }
            return response()->json(['error_msg' => "Success", 'data' => $finalDemographicDetails], 200);
        }
        return response()->json(['error_msg' => self::REFERRAL_UUID_MISSING, 'data' =>array()], 200);
    }

    public function searchEmmacareEmployee(Request $request){
        $getDetails = RobortHelper::getPatientDemographicDetails($request->agency_id,$request->externalId,$request->first_name,$request->last_name,$request->dob);
        return response()->json(['error_msg' => "Success", 'data' => $getDetails], 200);
    }

    public function updateRemoteId(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'remote_id' => 'required',
			'agency_id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		}

		$checkExistingRemote = $this->robortService->getDetailsByPatientWithAgencyId($request->remote_id, $request->agency_id);

		if(isset($checkExistingRemote->id)){
			$query = $checkExistingRemote;
		} else {
			$responseData = is_string($request->response) ? json_decode($request->response, true) : $request->response;

			if(!empty($responseData)){
                $responseData['agency_id'] = $request->agency_id;
                $responseData['patient_id'] = $request->patient_id;
				$query = $this->saveRemoteData($responseData);
			} else {
				return response()->json(['error_msg' => "Patient data is required to create a new remote record", 'status' => 0], 400);
			}
		}

		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
		$this->patientService->update(array('robort_id' => $query->patientId), array('id' => $request->patient_id));

		$data = $request->except(['_token','response']);
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Link To Remote Focus',
			'link' => url('/remote/update-remote-id'),
			'module' => 'Patient Appointment',
			'object_id' => $request->patient_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Link To Remote Focus',
			'new_response' => serialize($data),
			'old_response' => serialize($getPatientDetails->toArray()),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		return response()->json(['error_msg' => "Remote Employee successfully updated", 'status' => 1, 'data' => array($query)], 201);
	}

    private function saveRemoteData($responseData){
        $robortData = [
            'uuid' => $responseData['uuid'] ?? null,
            'patientId' => $responseData['patientId'] ?? null,
            'legacyId' => $responseData['legacyId'] ?? null,
            'externalId' => $responseData['externalId'] ?? null,
            'firstName' => $responseData['firstName'] ?? '',
            'lastName' => $responseData['lastName'] ?? '',
            'dob' => $responseData['dob'] ?? null,
            'gender' => $responseData['gender'] ?? null,
            'status' => $responseData['enrolledProgramStatus'] ?? null,
            'agency_id' => $responseData['agency_id'],
            'sync_flag' => 0,
            'appointment_id'=>$responseData['patient_id'],
        ];

        $robortId = $this->robortService->save($robortData);
        return $this->robortService->getDetailsById($robortId);
    }

    public function remoteEmpData(Request $request)
	{

		$query = $this->robortService->getRemoteDetails($request->all());

		$data = [];
		foreach ($query as $val) {
			$externalId = '';
			if ($val->externalId != "") {
				$externalId = ' (' . $val->externalId . ')';
			}
			$temp = [];
			$temp['remote_id'] = $val->patientId;
			$temp['name'] = $val->firstName . ' ' . $val->lastName . $externalId;

			$data[] = $temp;
		}

		return json_encode($data);
	}

    public function patientDocumentSend(Request $request){
        $user = auth()->user();
		$validator = Validator::make($request->all(), [
			'patient_id' => 'required',
			'documentId' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		}

        $newResponse = $request->except('_token');
        $getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
        if(isset($getPatientDetails->id)){
            $checkExistingRemote = $this->robortService->getDetailsByPatientWithAgencyId($getPatientDetails->robort_id, $getPatientDetails->agency_id);
           
            if(isset($checkExistingRemote->externalId) && $checkExistingRemote->externalId !=""){
                $documentList = $this->documentPatientService->getDocumentByDocIdAndPatientId($request->documentId,$request->patient_id);
               
                if(env('FILE_UPLOAD_PERMISSION') !='production'){
                    $url = public_path('/patientdocument').'/'.$documentList->attachment;
                    $fileName = basename($url);
                    $file = [
                        'documentPath' => $url,
                        'documentName' => basename($url),
                        'mimeType' => mime_content_type($url),
                        'notes' => 'Patient Document'
                    ];
                }else{
                    $url = Storage::disk('s3')->temporaryUrl($documentList->attachment, now()->addMinutes(10));
                    $fileName = $documentList->attachment;
                    $file = [
                        'documentPath' => $url,
                        'documentName' => $documentList->attachment,
                        'mimeType' => mime_content_type($url),
                        'notes' => 'Patient Document'
                    ];
                }
                $newResponse['file_name'] = $fileName;
                $getDetails = RobortHelper::documentWiseFileUpload($getPatientDetails->agency_id,$file,$checkExistingRemote->uuid);

                $error = "";
                if(isset($getDetails['errors'])){
                    $cnt =1;
                    foreach($getDetails['errors'] as $errr){
                        $error .=$cnt.'.'.$errr[0].'<br>';
                        $cnt++;
                    }

                    return response()->json(['error_msg' => $error, 'data' =>array()], 500);
                }
                if(isset($getDetails['message'])){
                    return response()->json(['error_msg' =>$getDetails['message'], 'data' =>array()], 500);
                }
                   
                $postResponse = $request->except('_token');
                $data = [
                    'patient_id'=>$request->patient_id,
                    'document_id'=>$request->documentId,
                    'remote_patient_id'=>$getPatientDetails->robort_id,
                    'file_name'=>$fileName,
                    'send_response'=>serialize($postResponse),
                    'return_response'=>serialize($getDetails),
                ];
                
                $saveDetails = $this->sendDocumentRemoteLogService->save($data);
                
                if($saveDetails){
                    $this->robortService->update(array('document_upload'=>$fileName,'document_upload_date'=>date('Y-m-d H:i:s'),'document_upload_by'=>$user->id,'document_upload_note'=>$request->note),array('id'=>$checkExistingRemote->id));
                    $this->documentPatientService->update(array('send_third_party'=>1,'send_third_party_date'=>date('Y-m-d H:i:s'),'send_third_party_by'=>$user->id),array('id'=>$request->documentId));
                    $ipaddress = Utility::getIP();
                    LogsService::save([
                        'type'         => 'Send Remote Document',
                        'link'         => url('/remote/patient-document-send'),
                        'module'       => 'Patient Appointment',
                        'object_id'    => $request->patient_id,
                        'message'      => $user->first_name . ' ' . $user->last_name . ' has Send Remote Document',
                        'new_response'=> serialize($newResponse),
                        'ip'           => $ipaddress,
                    ]);
                    return response()->json(['error_msg' => "File uploaded successfully", 'data' =>array()], 200);
                }
            }
       
            return response()->json(['error_msg' => self::REFERRAL_UUID_MISSING, 'data' =>array()], 500);
        }

        return response()->json(['error_msg' => "Appointment is not available", 'status' => 0], 400);
    }

}