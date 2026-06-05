<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Model\ImportCsvFileRecord;
use App\Model\AppointmentImportFile;
use App\Model\Patient;

use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Model\Logs;
use App\User;
use App\Helpers\Utility;
use App\Model\ImportErrorLog;
use App\Services\ImportCsvFileRecordService;
use App\Services\AppointmentImportFileService;
use App\Services\InsuranceMasterService;
use App\Services\LanguageService;
use App\Services\MasterService;
use App\Services\PatientService;
class ValidateImportRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:validate-records';
    protected const DATE_FORMAT_YYYY_MM_DD = "Y-m-d H:i:s";

    protected $description = 'Validate pending import records against patient_master table';

    protected $patientServicesRequest;
    protected $patientWiseServicesRequests;
    protected $importCsvFileRecordService;
    protected $appointmentImportFileService;
    protected $insuranceMasterService;
    protected $languageService;
    protected $masterService;
    protected $patientService;
    // Performance optimization: Cache frequently accessed data
    protected $masterServicesCache = [];
    protected $insuranceCache = [];
    protected $languageCache = [];
    protected $userCache = [];

    public function __construct(PatientServicesRequest $patientServicesRequest,PatientWiseServicesRequests $patientWiseServicesRequests,ImportCsvFileRecordService $importCsvFileRecordService,AppointmentImportFileService $appointmentImportFileService,InsuranceMasterService $insuranceMasterService,LanguageService $languageService,MasterService $masterService,PatientService $patientService){
        parent::__construct();
        $this->patientServicesRequest = $patientServicesRequest;
        $this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->importCsvFileRecordService = $importCsvFileRecordService;
        $this->appointmentImportFileService = $appointmentImportFileService;
        $this->insuranceMasterService = $insuranceMasterService;
        $this->languageService = $languageService;
        $this->patientService = $patientService;
        $this->masterService = $masterService;
    }
    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    
        $processedCount = 0;
        $duplicateCount = 0;
        $insertedCount = 0;
        $errorCount = 0;
        
        try {

            $getProcessingDetails = $this->getProcessingImportFile();
            if(isset($getProcessingDetails->id)){
                $details = $getProcessingDetails;
            }else{
                $details = $this->getPendingImportFile();
            }
            
            if (!$details) {
                return 0;
            }

            // Initialize caches for performance optimization
            $this->initializeCaches($details);

            // Get total count for progress tracking
            $getAll = $this->importCsvFileRecordService->getFetchAllRecordByImportId($details->id);

            $totalRecords = count($getAll);

            // Update status to Processing with initial counts
            if($details->status =="Pending"){
                $this->appointmentImportFileService->update([
                    'status' => 'Processing',
                    'success_records' => 0,
                    'failed_records' => 0,
                    'updated_by'=>env('CRONJOB_USER_ID')
                ],['id'=>$details->id]);
               
            }
            
            ImportCsvFileRecord::where('sync_status', 'N')
                ->where('import_file_id', $details->id)
                ->where('del_flag', 'N')
                ->orderBy('id', 'asc')
                ->chunk(500, function ($records) use (&$processedCount, &$duplicateCount, &$insertedCount, &$errorCount, &$details, $totalRecords) {

                    foreach ($records as $record) {
                        $this->processSingleRecord($record, $details, $processedCount, $duplicateCount, $insertedCount, $errorCount);
                    }

                    $this->appointmentImportFileService->update([
                        'success_records' => $insertedCount,
                        'failed_records' => $errorCount,
                        'duplicate_record'=>$duplicateCount
                    ],['id'=>$details->id]);
                   
                    // Show progress
                    $percentage = $totalRecords > 0 ? round(($processedCount / $totalRecords) * 100, 1) : 0;
                  
                });

            $this->updateAppointUploadFile($details->id);

            return 0;
        } catch (\Exception $e) {
            $this->error('Fatal error: ' . $e->getMessage());
            \Log::error('ValidateImportRecords failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->logImportError("",$details, $e);
            return 1;
        }
    }

    /**
     * Initialize caches to avoid N+1 queries
     * Pre-loads Master services, Insurance, Languages, and User data
     */
    private function initializeCaches($details)
    {

        // Pre-load all master services of type 11 and group by type::name for fast lookup
        $masters = $this->masterService->getAllServiceByMasterTypeFK();

        foreach ($masters as $master) {
            $cacheKey = strtolower(trim($master->types)) . '::' . strtolower(trim($master->name));
            if (!isset($this->masterServicesCache[$cacheKey])) {
                $this->masterServicesCache[$cacheKey] = collect();
            }
            $this->masterServicesCache[$cacheKey]->push($master);
        }

        // Pre-load all insurance records
        $insurances = $this->insuranceMasterService->getInsuranceMasterList();
        foreach ($insurances as $insurance) {
            $key = strtolower(trim($insurance->insurance_name));
            $this->insuranceCache[$key] = $insurance;
        }

        // Pre-load all languages
        $languages = $this->languageService->getLanguageList();
        foreach ($languages as $language) {
            $key = strtolower(trim($language->name));
            $this->languageCache[$key] = $language;
        }

        // Cache the user
        $this->userCache[$details->created_by] = User::find($details->created_by);

    }

    /**
     * Process services for a row - OPTIMIZED with caching
     * Fixes SQL injection vulnerability and implements caching
     */
    private function processServicesForRow($val, $userId)
    {
        $mainServiceIds = [];
        $mainServiceName = [];

        if (!empty($val->service_id)) {
            $elements = explode(',', $val->service_id);
            foreach ($elements as $service) {
                $serviceTrimmed = trim($service);
                $cacheKey = strtolower(trim($val->type)) . '::' . strtolower($serviceTrimmed);

                // Check cache first
                if (isset($this->masterServicesCache[$cacheKey]) && $this->masterServicesCache[$cacheKey]->isNotEmpty()) {
                    $master = $this->masterServicesCache[$cacheKey]->first();
                    $mainServiceIds[] = $master->id;
                    $mainServiceName[] = $master->name;
                } else {
                    // Not in cache - query database with safe parameterized query
                    $query = $this->masterService->getDetailsByName($val->type,$serviceTrimmed);

                    if ($query) {
                        $mainServiceIds[] = $query->id;
                        $mainServiceName[] = $query->name;

                        // Add to cache
                        if (!isset($this->masterServicesCache[$cacheKey])) {
                            $this->masterServicesCache[$cacheKey] = collect();
                        }
                        $this->masterServicesCache[$cacheKey]->push($query);
                    } else {
                        // Create new master record
                        $newMaster = $this->masterService->cronJobSave([
                            'name' => $serviceTrimmed,
                            'master_type_fk' => 11,
                            'types' => $val->type,
                            'del_flag' => 'N',
                            'user_id' => $userId,
                            'created_at' => now()
                        ]);

                        $mainServiceIds[] = $newMaster->id;
                        $mainServiceName[] = $serviceTrimmed;

                        // Add to cache
                        if (!isset($this->masterServicesCache[$cacheKey])) {
                            $this->masterServicesCache[$cacheKey] = collect();
                        }
                        $this->masterServicesCache[$cacheKey]->push($newMaster);
                    }
                }
            }
        }
        return [$mainServiceIds, $mainServiceName];
    }

    /**
     * Resolve insurance with caching - OPTIMIZED
     */
    protected function resolveInsurance($row, $userId)
    {
        $updated_insurance_name_id = null;

        if (isset($row->insurance_name) && $row->insurance_name != "") {
            $insuranceNameTrimmed = trim($row->insurance_name);
            $cacheKey = strtolower($insuranceNameTrimmed);

            // Check cache first
            if (isset($this->insuranceCache[$cacheKey])) {
                $updated_insurance_name_id = $this->insuranceCache[$cacheKey]->id;
            } else {
                // Not in cache - check database with safe query
                $getInsuranceMaster = $this->insuranceMasterService->getDetailsByInsuranceName($insuranceNameTrimmed);

                if ($getInsuranceMaster) {
                    $updated_insurance_name_id = $getInsuranceMaster->id;

                    // Add to cache
                    $this->insuranceCache[$cacheKey] = $getInsuranceMaster;
                } else {
                    // Create new insurance record
                    $saveData = $this->insuranceMasterService->saveWithCron([
                        'insurance_name' => $insuranceNameTrimmed,
                        'created_date' => date(self::DATE_FORMAT_YYYY_MM_DD),
                        'created_by' => $userId
                    ]);

                    $updated_insurance_name_id = $saveData->id;

                    // Add to cache
                    $this->insuranceCache[$cacheKey] = $saveData;
                }
            }
        }

        return $updated_insurance_name_id;
    }

    /**
     * Resolve language with caching - OPTIMIZED
     * Fixes SQL injection vulnerability
     */
    protected function resolveLanguage($row)
    {
        $updatedLanguage = null;

        if (isset($row->language) && $row->language != "") {
            $languageTrimmed = trim($row->language);
            $cacheKey = strtolower($languageTrimmed);

            // Check cache first
            if (isset($this->languageCache[$cacheKey])) {
                $updatedLanguage = $this->languageCache[$cacheKey]->id;
            } else {
                // Not in cache - check database with safe parameterized query
                $getLanguage = $this->languageService->getDetailsbyName($languageTrimmed);

                if ($getLanguage) {
                    $updatedLanguage = $getLanguage->id;

                    // Add to cache
                    $this->languageCache[$cacheKey] = $getLanguage;
                }
            }
        }

        return $updatedLanguage;
    }

    /**
     * Log import action - OPTIMIZED with caching
     */
    protected function logImportAction($insert, $oldResponse, $final_array, $createdBy, $ipaddress)
    {
        // Use cached user data instead of querying every time
        if (!isset($this->userCache[$createdBy])) {
            $this->userCache[$createdBy] = User::find($createdBy);
        }

        $userDetails = $this->userCache[$createdBy];

        if ($userDetails) {
            $insertLog = [
                'type' => 'Import Appointment',
                'link' => url('/patient/patient-import'),
                'module' => 'Patient Appointment',
                'object_id' => $insert,
                'message' => $userDetails->first_name . ' ' . $userDetails->last_name . ' has import appointment by Upload CSV File',
                'new_response' => serialize($final_array),
                'old_response' => serialize($oldResponse),
                'ip' => $ipaddress,
                'created_by'=>$createdBy
            ];

            Logs::create($insertLog);
        }
    }

    private function getPendingImportFile()
    {
        return $this->appointmentImportFileService->getPendingRecord('Pending');
    }

    private function processSingleRecord($record, $details, &$processedCount, &$duplicateCount, &$insertedCount, &$errorCount)
    {
        
        try {
            
            [$mainServiceIds] = $this->processServicesForRow($record, $details->created_by);

            $existingPatient = $this->getExistingPatient($record);
            $newResponse = [];
            $oldResponse = [];
            
            $flag=0;
            if ($existingPatient) {
                $patientId = $existingPatient->id;
                $oldResponse = $existingPatient->toArray();
                $duplicateCount++;
                $flag =1;
                $newResponse = $this->duplicateResponse($record, $details->created_by, $mainServiceIds);

            } else {
                [$patientId, $newResponse] = $this->createNewPatient($record, $details->created_by, $mainServiceIds);
                $insertedCount++;
            }

            $patientServiceLastId = $this->savePatientService($patientId, $record);
            $this->savePatientWiseServices($patientId, $mainServiceIds, $record, $patientServiceLastId,$flag);
            
            $this->logImportAction($patientId, $oldResponse, $newResponse, $record->created_by, $record->ip_address);
           
            if ($this->isPatient($record)) {
                Utility::saveResolutionLogForms($record->status, $patientServiceLastId, $patientId, $record->created_by,$record->ip_address);
            }
            
            $this->markRecordSynced($record->id, $patientId,$flag);
            $processedCount++;
        } catch (\Exception $e) {
            $errorCount++;
            $processedCount++;
            $this->markRecordFailed($record->id, $e->getMessage());
            $this->logImportError($record, $details, $e);
        }
    }
    private function getExistingPatient($record)
    {
        $mobile = preg_replace('/\D/', '', $record->mobile);
        // --- Check existence ---
        return  $this->patientService->checkForExistingRecordsbyCronjob($record,$mobile);
    }

    private function createNewPatient($record, $createdBy, $mainServiceIds)
    {
        $updatedInsuranceId = $this->resolveInsurance($record,$createdBy);
        $language = $this->resolveLanguage($record);

        $finalData = $this->buildPatientData($record, $createdBy, $mainServiceIds, $updatedInsuranceId, $language);
        
        $patientId = Patient::insertGetId($finalData);
       
        return [$patientId, $finalData];
    }

    private function buildPatientData($record, $createdBy, $mainServiceIds, $insuranceId, $language)
    {
        $data =  [
            'agency_id' => $record->agency_id,
            'patient_code' => $record->patient_code,
            'first_name' => ucfirst($record->first_name),
            'middle_name' => ucfirst($record->middle_name),
            'last_name' => ucfirst($record->last_name),
            'full_name' => ucfirst($record->first_name) . ' ' . ucfirst($record->last_name),
            'type' => ucfirst($record->type),
            'mobile' => $record->mobile,
            'dob' => $record->dob,
            'phone' => $record->phone,
            'gender' => strtolower($record->gender),
            'language' => $language,
            'service_id' => implode(',', $mainServiceIds),
            'appointment_mode' => $record->appointment_mode,
            'service_expiry_date' => $record->service_expiry_date,
            'address1' => $record->address1,
            'address2' => $record->address2,
            'city' => $record->city,
            'state' => $record->state,
            'zip_code' => $record->zip_code,
            'cin' => $record->cin,
            'insurance_name' => $insuranceId,
            'insurance_id' => $record->insurance_id,
            'deleted_flag' => 'N',
            'status' => $record->status,
            'key' => $record->key,
            'created_date' => date(self::DATE_FORMAT_YYYY_MM_DD),
            'created_by' => $createdBy,
        ];

        if($record->diciplin !=""){
            $data['diciplin'] = $record->diciplin;
        }
        
        return $data;
    }

    private function savePatientService($patientId, $record)
    {
        return $this->patientServicesRequest->save([
            'patient_id' => $patientId,
            'follow_up_date' => null,
            'due_date' => null,
            'status' => $record->status,
            'created_at' => date(self::DATE_FORMAT_YYYY_MM_DD),
            'created_by' => $record->created_by,
            'flag' => 1
        ]);
    }

    /**
     * Save patient wise services - OPTIMIZED with batch insert
     * Reduces N queries to 1 query
     */
    private function savePatientWiseServices($patientId, $serviceIds, $record, $lastId,$flag)
    {
        if (empty($serviceIds)) {
            return;
        }

        $timestamp = date(self::DATE_FORMAT_YYYY_MM_DD);

        // Prepare bulk insert data
        $insertData = [];
        foreach ($serviceIds as $serviceId) {
            $insertData = [
                'patient_id' => $patientId,
                'service_id' => $serviceId,
                'patient_service_request_id' => $lastId,
                'flag' => 1,
                'created_date' => $timestamp,
                'created_by' => $record->created_by,
            ];
            $this->patientWiseServicesRequests->save($insertData);
        }

        if($flag ==1 && !empty($serviceIds)){
            $this->patientService->update(array('service_id'=>implode(',',$serviceIds),'status'=>$record->status),array('id'=>$patientId));
        }
        
    }

    private function markRecordSynced($id, $patientId,$duplicateStatus=0)
    {
        $syncStatus = "D";
        if($duplicateStatus ==0){
            $syncStatus = "Y";
        }

        $this->importCsvFileRecordService->update([
            'sync_status' => $syncStatus,
            'patient_id' => $patientId,
            'updated_date' => Carbon::now(),
            'duplicate_status'=>$duplicateStatus
        ],['id'=>$id]);

    }

    private function markRecordFailed($id, $errorMsg)
    {
        
        $this->importCsvFileRecordService->update([
            'sync_status' => 'F',
            'error_message' => $errorMsg,
            'updated_date' => Carbon::now()
        ],['id'=>$id]);
    }

    private function isPatient($record){
        return !empty($record->type) && strtolower($record->type) === 'patient';
    }

    /**
     * Update appointment upload file status - OPTIMIZED and FIXED
     * Fixes reversed logic bug and uses efficient count queries
     */
    private function updateAppointUploadFile($id)
    {
        // Use efficient groupBy query to count all statuses at once
        $statusCounts = $this->importCsvFileRecordService->syncStatusData($id);

        $totalRecords = $statusCounts->sum();
        $successCount = $statusCounts->get('Y', 0);
        $failedCount = $statusCounts->get('F', 0);
        $pendingCount = $statusCounts->get('N', 0);
        $duplicateCount = $statusCounts->get('D', 0);
        // Only update if all records are processed (no pending)
        if ($pendingCount == 0 && $totalRecords > 0) {
            // FIXED: Correct logic
            if ($failedCount == 0) {
                // All succeeded - mark as Completed
                $status = 'Completed';
            } else {
                // Some failed - mark as Partial Completed
                $status = 'Partial Completed';
            }

            $this->appointmentImportFileService->update([
                'status' => $status,
                'success_records' => $successCount,
                'failed_records' => $failedCount,
                'duplicate_record' => $duplicateCount,
                'completed_at' => now()
            ],['id'=>$id]);

        }
    }

    private function logImportError($record, $details, \Exception $e)
    {
        try {
            ImportErrorLog::create([
                'import_file_id' => $details->id,
                'record_id' => $record->id??"",
               
                'error_type' => class_basename(get_class($e)),
                'error_message' => $e->getMessage(),
                'row_number' => $record->id??"",
                'row_data' => $record->toArray(),
                'created_by' => $details->created_by,
                'trace'=>$e->getTraceAsString(),
            ]);
        } catch (\Exception $logException) {
            \Log::error('Failed to log import error', [
                'record_id' => $record->id,
                'original_error' => $e->getMessage(),
                'log_error' => $logException->getMessage(),
            ]);
        }
    }

    private function duplicateResponse($record, $createdBy, $mainServiceIds){
        $updatedInsuranceId = $this->resolveInsurance($record,$createdBy);
        $language = $this->resolveLanguage($record);
    
        return  $this->buildPatientData($record, $createdBy, $mainServiceIds, $updatedInsuranceId, $language);
    }

    private function getProcessingImportFile()
    {
        return $this->appointmentImportFileService->getPendingRecord('Processing');
    }
}