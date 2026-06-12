<?php
namespace App\Http\Controllers\API\V4;
use App\Agency;
use App\Helpers\GenerateAgencyTokenHelper;
use App\Model\AssignEMCRecord;
use App\Model\Language;
use App\Master;
use App\Model\Patient;
use App\Model\ThirdPartyPatientLog;
use App\Model\ThirdPartyPatientMaster;
use App\Model\TokenwiseApiCall;
use App\Services\DocumentPatientService;
use App\Services\DocumentUploadService;
use App\Services\DoctorService;
use App\Services\LocationMasterService;
use App\Services\LogsService;
use App\Services\PatientService;
use App\Services\PatientServicesRequest;
use App\Services\ThirdPartyPatientMasterService;
use App\User;
use App\ZipCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Utility;
class APIController extends BaseController
{

    private const SUCCESS_STATUS = 200;
    private const ERROR_STATUS = 500;
    private const INVALID_TOKEN='Invalid token.';
    private const SUCCESS_DATA_RETRIVE="Data retrieved successfully";
    private const NO_RECORD_AVAILABLE="No record available";
    private PatientService $patientService;
    private DocumentPatientService $documentPatientService;
    private DoctorService $doctorService;
    private LocationMasterService $locationMasterService;
    private ThirdPartyPatientMasterService $thirdPartyPatientMaster;
    private DocumentUploadService $documentUploadService;
    private PatientServicesRequest $patientServicesRequest;

    public function __construct(
        PatientService $patientService,
        DocumentPatientService $documentPatientService,
        DoctorService $doctorService,
        LocationMasterService $locationMasterService,
        ThirdPartyPatientMasterService $thirdPartyPatientMaster,
        DocumentUploadService $documentUploadService,
        PatientServicesRequest $patientServicesRequest
    ) {
        
        $this->patientService = $patientService;
        $this->documentPatientService = $documentPatientService;
        $this->doctorService = $doctorService;
        $this->locationMasterService = $locationMasterService;
        $this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
        $this->documentUploadService = $documentUploadService;
        $this->patientServicesRequest = $patientServicesRequest;
    }

    /**
     * Tracks API usage and logs relevant request information.
     *
     * @param string $apiKey The API key used for the request
     * @return void
     */
    public function trackApiUsage(string $apiKey): void
    {
        try {
            $user = auth()->user();
            $userId = $user ? $user->id : null;

            $requestData = $this->collectRequestData($apiKey);

            $this->saveLog($requestData);
        } catch (\Exception $e) {
            Log::error('Failed to track API usage: ' . $e->getMessage(), [
                'api_key' => $apiKey,
                'user_id' => $userId ?? null,
            ]);
        }
    }

    /**
     * Collects and formats request data for logging.
     *
     * @param string $apiKey The API key used for the request
     * @return array
     */
    private function collectRequestData(string $apiKey): array
    {
        $request = request();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $remoteHost = @gethostbyaddr($ipAddress);
        $url = $request->fullUrl();
        $queryString = $request->getQueryString() ?? '';
        $postData = json_encode($request->post() ?? []);
        $userInfo = json_encode([
            'Ip' => $ipAddress,
            'Page' => $queryString,
            'UserAgent' => $userAgent,
            'RemoteHost' => $remoteHost,
        ]);

        return [
            'url' => $url,
            'api_key' => $apiKey,
            'ip' => $ipAddress,
            'response' => $userInfo,
            'created_date' => now()->toDateTimeString(),
            'data' => $postData,
        ];
    }

    /**
     * Saves the request log to the database.
     *
     * @param array $data The data to be logged
     * @return void
     */
    private function saveLog(array $data): void
    {
        try {
            ThirdPartyPatientLog::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to save API usage log: ' . $e->getMessage(), $data);
        }
    }

    /**
     * Retrieves a list of patients for the authenticated agency.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function patientList(Request $request): JsonResponse
    {
        
        try {
            
            $header = $request->header('authorization');
            $checkToken = GenerateAgencyTokenHelper::checkToken($header);

            if (empty($checkToken)) {
                return $this->errorResponse(self::INVALID_TOKEN, self::ERROR_STATUS);
            }
            $this->trackApiUsage($header);
            $this->saveTokenWiseApiCall($checkToken->id);

            $agencyId = $checkToken->agency_id;
            $patients = $this->patientService->visitingAidsChartApi(
                $agencyId,
                $request->all()
            );

            $patientData = $this->formatPatientData($patients);
            return $this->successResponse(self::SUCCESS_DATA_RETRIVE, $patientData);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve patient list: ' . $e->getMessage(), [
                'agency_id' => $agencyId ?? null,
                'header' => $header,
            ]);
            return $this->errorResponse('An error occurred while retrieving the patient list.', self::ERROR_STATUS);
        }
    }

    /**
     * Formats patient data for the response.
     *
     * @param mixed $patients
     * @return array
     */
    private function formatPatientData($patients): array
    {
        $formattedData = [];

        foreach ($patients as $patient) {
          
            $assignFirstName = $patient->assignToUser->first_name ?? '';
            $assignLastName = $patient->assignToUser->last_name ?? '';
            $locationName = $patient->locations->address1 ?? '';
           
            $assignUserName = trim("{$assignFirstName} {$assignLastName}");

            $serviceIds = explode(',', $patient->service_id);
            $serviceNames = [];
            if (!empty($serviceIds)) {
                $serviceNames = Master::select('name')
                    ->whereIn('id', $serviceIds)
                    ->where('del_flag', 'N')
                    ->pluck('name')
                    ->toArray();
            }

            $formattedData[] = [
                'id' => $patient->id,
                'first_name' => $patient->first_name,
                'middle_name' => $patient->middle_name,
                'last_name' => $patient->last_name,
                'dob' => $patient->dob,
                'gender' => $patient->gender,
                'remark' => $patient->remark,
                'status' => $patient->status,
                'phone' => $patient->phone,
                'created_date' => $patient->created_date,
                'agency_id' => $patient->agency_id,
                'appointment_date' => $patient->appointment_date,
                'service_id' => $patient->service_id,
                'mobile' => $patient->mobile,
                'language' => $patient->language,
                'type' => $patient->type,
                'discipline' => $patient->diciplin,
                'notes' => $patient->notes,
                'address1' => $patient->address1,
                'address2' => $patient->address2,
                'state' => $patient->state,
                'city' => $patient->city,
                'zip_code' => $patient->zip_code,
                'payment_type' => $patient->payment_type,
                'platform_type' => $patient->platform_type,
                'platform_id' => $patient->platform_id,
                'email' => $patient->email,
                'emergency_phone' => $patient->emergency_phone,
                'cin' => $patient->cin,
                'emergency_contact_name' => $patient->emergency_contact_name,
                'ssn' => $patient->ssn,
                'availability_followup_date' => $patient->availability_followup_date,
                'assign_user_name' => $assignUserName,
                'service_name' => implode(',', $serviceNames),
                'location_name'=>$locationName
            ];
        }

        return $formattedData;
    }

    /**
     * Formats patient wise service data for the response.
     *
     * @param mixed $patients
     * @return array
     */

    public function serviceRequestedList(Request $request): JsonResponse{
        
        try {
            $header = $request->header('authorization');
            $checkToken = GenerateAgencyTokenHelper::checkToken($header);
    
            if (empty($checkToken)) {
                return $this->errorResponse(self::INVALID_TOKEN, self::ERROR_STATUS);
            }
            $this->trackApiUsage($header);
            $this->saveTokenWiseApiCall($checkToken->id);
    
            $agencyId = $checkToken->agency_id;
            $getPatientDetails = $this->patientService->getPatientDetailsById($request->id,$agencyId);
            $patientData = [];
            if(isset($getPatientDetails->id)){
                $serviceList = $this->patientServicesRequest->getPatientServiceListByAPI($request->id);
                $patientData = $this->formatServiceRequestData($serviceList);
                $message = self::SUCCESS_DATA_RETRIVE;
            }else{
                $message = self::NO_RECORD_AVAILABLE;
            }
            return $this->successResponse($message, $patientData);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve patient list: ' . $e->getMessage(), [
                'patient_id' => $request->id ?? null,
                'agency_id' => $agencyId ?? null,
                'header' => $header,
            ]);
            return $this->errorResponse('An error occurred while retrieving the patient list.', self::ERROR_STATUS);
        }

    }

    /**
     * Formats service data for the response.
     *
     * @param mixed $services
     * @return array
     */
    private function formatServiceRequestData($services): array
    {
        $formattedData = [];

        foreach ($services as $service) {
            $serviceName = $this->getServiceNames($service);
            
            $createdBy = trim(($service->userDetails->first_name ?? '') . ' ' . ($service->userDetails->last_name ?? ''));
            $completedBy = trim(($service->completedUserDetails->first_name ?? '') . ' ' . ($service->completedUserDetails->last_name ?? ''));

            $formattedData[] = [
                'id' => $service->id,
                'patient_id' => $service->patient_id,
                'service_name' => $serviceName,
                'status' => $service->status,
                'follow_up_date' => $this->formatDateField($service->follow_up_date),
                'due_date' => $this->formatDateField($service->due_date),
                'completed_date' => $this->formatDateField($service->completed_date),
                'created_at' => $this->formatDateField($service->created_at,true),
                'created_by' => $createdBy,
                'completed_by' => $completedBy,
            ];
        }

        return $formattedData;
    }

    private function formatDateField($date, $includeTime = false): string
    {
        $defaultZero = '0000-00-00';
        if (empty($date) || str_starts_with($date, $defaultZero)) {
            return '';
        }

        return $includeTime
            ? Utility::convertYMDTimeUsingCarbon($date)
            : Utility::convertMDYUsingCarbon($date);
    }

    private function getServiceNames($patient): string
    {
        if (empty($patient->patientServiceRequestRelationShip)) {
            return '';
        }

        $serviceData = [];
        foreach ($patient->patientServiceRequestRelationShip as $serviceRequest) {
            $serviceData[] = $serviceRequest->services[0]->name ?? '';
        }

        return implode(',', array_filter($serviceData));
    }

    /**
     * Formats patient wise Document data for the response.
     *
     * @param mixed $documents
     * @return array
     */
    public function documentList(Request $request): JsonResponse{
        
        try {
            $header = $request->header('authorization');
            $checkToken = GenerateAgencyTokenHelper::checkToken($header);
    
            if (empty($checkToken)) {
                return $this->errorResponse(self::INVALID_TOKEN, self::ERROR_STATUS);
            }
            $this->trackApiUsage($header);
            $this->saveTokenWiseApiCall($checkToken->id);
            $agencyId = $checkToken->agency_id;

            $getPatientDetails = $this->patientService->getPatientDetailsById($request->id,$agencyId);
            $documentData=[];
            if(isset($getPatientDetails->id)){
                $documentList = $this->documentPatientService->documentListByApi($request->id);
                $documentData = $this->formatDocumentData($documentList);
                $message = self::SUCCESS_DATA_RETRIVE;
            }else{
                $message = self::NO_RECORD_AVAILABLE;
            }

            return $this->successResponse($message, $documentData);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve document list: ' . $e->getMessage(), [
                'patient_id' => $request->id ?? null,
                'agency_id' => $agencyId ?? null,
                'header' => $header,
            ]);
            return $this->errorResponse('An error occurred while retrieving the document list.', self::ERROR_STATUS);
        }

    }

    /**
     * Formats document list data for the response.
     *
     * @param mixed $documentList
     * @return array
     */
    private function formatDocumentData($documentList): array
    {
        $formattedData = [];
       
        foreach ($documentList as $doc) {
            
                $serviceName = $this->getDocumentServiceNames($doc);
           
            $createdBy = trim(($doc->userDetails->first_name ?? '') . ' ' . ($doc->userDetails->last_name ?? ''));
            $formattedData[] = [
                'id' => $doc->id,
                'patient_id' => $doc->patient_id,
                'document_name' => $doc->document_name,
                'requested_service_id' => $doc->request_service_id,
              
                'attachment_services' => $serviceName,
                'document_completion_date' => $this->formatDateField($doc->document_completed_date,true),
                'created_at' => $this->formatDateField($doc->created_date,true),
                'created_by' => $createdBy,
                
            ];
        }

        return $formattedData;
    }

    private function getDocumentServiceNames($document): string
    {
        if (empty($document->documentUploadServiceDetailsMany)) {
            return '';
        }
 
        $serviceData = [];
        foreach ($document->documentUploadServiceDetailsMany as $serviceRequest) {
            
            $serviceData[] = $serviceRequest->masterDetails->name ?? '';
        }
   
        return implode(',', array_filter($serviceData));
    }
    /**
     * Returns a success JSON response.
     *
     * @param string $message
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    private function successResponse(string $message, array $data, int $status = self::SUCCESS_STATUS): JsonResponse
    {
        return response()->json([
            'success' => $message,
            'status' => 1,
            'data' => $data,
        ], $status);
    }

    /**
     * Returns an error JSON response.
     *
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    private function errorResponse(string $message, int $status = self::SUCCESS_STATUS): JsonResponse
    {
        return response()->json([
            'error_msg' => $message,
            'status' => 0,
            'data' => [],
        ], $status);
    }

    /**
     * Saves token-wise API call information.
     *
     * @param int $tokenId
     * @return void
     */
    private function saveTokenWiseApiCall(int $tokenId): void
    {
        try {
            TokenwiseApiCall::create([
                'token_id' => $tokenId,
                'called_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save token-wise API call: ' . $e->getMessage(), [
                'token_id' => $tokenId,
            ]);
        }
    }
}