<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\TaskHealthMaster;
use App\Helpers\TaskHealthApiHelper;
use App\Helpers\Utility;
use App\Services\LogsService;
use App\Helpers\HHAPatientHelper;
use App\Services\TaskHealthFlagsService;
use App\Services\PatientService;
use App\Services\MapTaskHealthService;
use App\Model\HHAAuditLog;
use App\Model\TaskHealthCronLog;
use App\Services\HHAPatientService;
use App\Services\AgencyService;
use App\Services\TaskHealthSupervisionService;
use App\Services\AgencyTaskHealthSettingService;
use Exception;
use Illuminate\Support\Facades\File;
use \App\SiteSetting;
use App\Model\AgencyTaskHealthSetting;
use App\Agency;

class TaskHealthHHALink extends Command
{
    protected $signature = 'link:task-health-hha-link';

    protected $description = 'Link task health records to HHA patients, then sync POC and supervision';

    private $logBuffer = [];

    public function handle()
    {
        $setting = SiteSetting::orderBy('id','desc')->where('del_flag', 'N')->first();
        if (!$setting || $setting->task_health_cron_enabled == 0) {
            return;
        }
        TaskHealthMaster::select('id', 'first_name', 'last_name', 'mobile', 'agency_id', 'patient_id', 'task_id','is_poc_sync','is_supervision_sync')
            ->whereNotNull('task_id')
            ->where('deleted_flag', 'N')
            ->where('is_task_sync', 1)
            ->where('is_error',0)
            ->whereNotNull('patient_id')
            ->orderBy('id', 'desc')
            ->chunkById(50, function ($records) {
                foreach ($records as $record) {
                    $result = $this->processRecord($record);
                    $this->pushLog([
                        'cron_name'      => $this->signature,
                        'task_health_id' => $record->id,
                        'patient_id'     => $record->patient_id,
                        'agency_id'      => $record->agency_id,
                        'type'           => $result['type'],
                        'message'        => $result['message'],
                        'data'           => $result['data'] ?? null,
                    ]);
                    if ($result['type'] == 'success') {
                        TaskHealthMaster::where('id', $record->id)->update(['is_task_sync' => 2]);
                    } else {
                        TaskHealthMaster::where('id', $record->id)->update(['is_error' => 1]);
                    }
                }
                $this->flushLogs();
        });
    }

    private function pushLog(array $entry): void
    {
        $now = now()->toDateTimeString();
        if (isset($entry['data']) && is_array($entry['data'])) {
            $entry['data'] = serialize($entry['data']);
        }
        $this->logBuffer[] = array_merge($entry, ['created_at' => $now, 'updated_at' => $now]);
    }

    private function flushLogs(): void
    {
        if (!empty($this->logBuffer)) {
            TaskHealthCronLog::insert($this->logBuffer);
            $this->logBuffer = [];
        }
    }

    private function processRecord($record): array
    {
        // Step 1: Link HHA patient
        $mobile = preg_replace('/[^0-9]/', '', $record->mobile);
        if (strlen($mobile) == 11 && substr($mobile, 0, 1) == '1') {
            $mobile = substr($mobile, 1);
        }

        $sendResponseData = [
            'first_name' => $record->first_name,
            'last_name'  => $record->last_name,
            'phone'      => $mobile,
            'agency_id'  => $record->agency_id,
        ];
        try{
            $linkData = TaskHealthApiHelper::linkHHAPatientData($sendResponseData, $record->patient_id);
        } catch (Exception $e) {
            $data = ([
                'req_data'      => ['task_id' => $record->task_id, 'patient_id' => $record->patient_id],
                'response_data' => ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()],
            ]);
            return [
                'type'    => 'error',
                'message' => 'HHA link failed: '.$e->getMessage(),
                'data'    => $data,
            ];
        }

        if (empty($linkData) || empty($linkData['link_hha_patient'])){
            return [
                'type'    => 'error',
                'message' => 'HHA link failed',
                'data'    => ($sendResponseData),
            ];
        }

        TaskHealthMaster::where('id', $record->id)->update(['link_hha_patient' => $linkData['link_hha_patient']]);
        if($linkData['is_already_linked'] == 0){
            LogsService::save([
                'type'         => 'Link To HHX Patient from task health',
                'link'         => $this->signature,
                'module'       => 'Patient Appointment',
                'object_id'    => $record->patient_id,
                'message'      => 'Task health has linked To HHX Patient via System Admin',
                'new_response' => serialize(['response' => $sendResponseData,'linkData' => $linkData]),
                'ip'           => Utility::getIP(),
                'created_by'   => '482',
            ]);
        }

        $messages = ['HHA linked'];
        $data = ['hha_link_data' => ['response' => $sendResponseData, 'linkData' => $linkData]];

        // Fetch shared data once — reused by both POC and Supervision
        $visitDetails = TaskHealthApiHelper::getVisitDetail($record->task_id, 'cron');
        if (empty($visitDetails['data'])) {
            return [
                'type'    => 'error',
                'message' => 'Visit API failed',
                'data'    => $data,
            ];
        }
        $patientDetails = (new PatientService())->getPatientDetailsByIdWhitoutAgency($record->patient_id);

        $agencyId = TaskHealthApiHelper::detectLocalAgency($visitDetails['data']['task']['agencyId'] ?? null);
        if (env('HHA_DEVELOPEMENT_CRED') == 'development') {
            $agencyId = ['id' => env('HHA_DEVELOPEMENT_AGENCY_ID')];
        }
        $hasError = false;
        $agSetting = AgencyTaskHealthSetting::where('agency_id', $agencyId)->where('del_flag', 'N')->first();
        $task_data = (new TaskHealthFlagsService())->getTaskFlagIds($record->task_id);
        // Step 2: POC sync
        if ($record->is_poc_sync == 1) {
            if($task_data->poc_check == 0){
                $pocResult  = $this->runPOC($record, $visitDetails, $patientDetails,$agSetting);
                $messages[] = $pocResult['message'];
                if (!empty($pocResult['data'])) {
                    $data['poc_data'] = $pocResult['data'];
                }
                if ($pocResult['type'] === 'error' || $pocResult['type'] === 'skipped') {
                    $hasError = true;
                }
            }else{
                $messages[] = 'POC is already linked';
                $data['poc_data'] = [
                    'status' => 'already_linked',
                    'flag'   => $task_data->poc_check
                ];
            }
        }

        // Step 3: Supervision sync
        if ($record->is_supervision_sync == 1) {
            if($task_data->supervision_check == 0){
                $supResult  = $this->runSupervision($record, $visitDetails, $patientDetails, $agencyId);
                $messages[] = $supResult['message'];
                if (!empty($supResult['data'])) {
                    $data['supervision_data'] = $supResult['data'];
                }
                if ($supResult['type'] === 'error' || $supResult['type'] === 'skipped') {
                    $hasError = true;
                }
            }else{
                $messages[] = 'Supervision is already linked';
                $data['supervision_data'] = [
                    'status' => 'already_linked',
                    'flag'   => $task_data->supervision_check
                ];
            }
        }

        // Step 4: Upload extra documents (Patient Assessment 80752, Emergency Kardex 81049)
        $hhaData = (new HHAPatientService())->getDetailsByPatientID($patientDetails->link_hha_patient, $record->agency_id);
        if (empty($hhaData)) {
           $hasError = 'true';
           $data['extra_documents'] = ['agency_id' => $record->agency_id,'link_hha_patient'=>$patientDetails->link_hha_patient];
           $messages[] = 'Extra Docs upload skipped: no agency matched with records';
        }else{
            $extraDocs = $this->uploadExtraDocuments($record, $visitDetails,$task_data,$linkData['link_hha_patient'],$agSetting);
            if (!empty($extraDocs)) {
                $data['extra_documents'] = $extraDocs;

                $assessmentUploaded = 0;
                $kardexUploaded     = 0;
                foreach ($extraDocs as $extraDoc) {
                    $messages[] = ($extraDoc['status'] == 1 ? 'Doc saved' : 'Doc failed') . ': ' . $extraDoc['title'];
                    if (($extraDoc['status'] ?? 0) == 1) {
                        if (($extraDoc['type_id'] ?? null) == 80752) {
                            $assessmentUploaded = 1;
                        }
                        if (($extraDoc['type_id'] ?? null) == 81049) {
                            $kardexUploaded = 1;
                        }
                    }
                }

                // Set flags for successfully uploaded documents
                if ($assessmentUploaded || $kardexUploaded) {
                    (new TaskHealthFlagsService())->saveFlagsExtraDocsCron(
                        null,
                        $record->task_id,
                        $record->patient_id,
                        $assessmentUploaded,
                        $kardexUploaded
                    );
                }
            }
        }
        return [
            'type'    => $hasError ? 'error' : 'success',
            'message' => implode(' | ', $messages),
            'data'    => $data,
        ];
    }

    // -------------------------------------------------------------------------
    // Step 2: POC
    // -------------------------------------------------------------------------

    private function runPOC($record, array $visitDetails, $patientDetails,$agSetting): array
    {
        try {
            $pocItems = $visitDetails['data']['planOfCareItems'] ?? [];

            if (empty($pocItems)) {
                return ['type' => 'skipped', 'message' => 'POC skipped: no plan of care available', 'data' => null];
            }

            $hhaData = (new HHAPatientService())->getDetailsByPatientID($patientDetails->link_hha_patient, $record->agency_id);
            if (empty($hhaData)) {
                return ['type' => 'skipped', 'message' => 'POC skipped: no agency matched with records', 'data' => ['patient_id' => $record->patient_id, 'agency_id' => $record->agency_id]];
            }

            $taskHealthVisitPOCId = array_column($pocItems, 'taskHealthId');
            $getList = (new MapTaskHealthService())->getMapTaskListByWithCodeId($taskHealthVisitPOCId, $record->agency_id);
            if (count($getList) === 0) {
                return ['type' => 'skipped', 'message' => 'POC skipped: task not linked for HHA', 'data' => ['poc_items' => $pocItems]];
            }

            if (count($getList) < 5) {
                return ['type' => 'skipped', 'message' => 'POC skipped: minimum 5 tasks required, found ' . count($getList), 'data' => ['task_list' => $getList]];
            }

            $taskId      = $visitDetails['data']['task']['id'];
            $pocDocument = $this->getPOCDocument($visitDetails, $record, $taskId);
            if (is_null($pocDocument)) {
                return ['type' => 'skipped', 'message' => 'POC skipped: no matching document found', 'data' => null];
            }

            // Check if a POC with the same start/stop date already exists on HHA side
            $duplicateCheck = $this->checkExistingPOC($record, $patientDetails, $visitDetails);
            if ($duplicateCheck !== null) {
                TaskHealthMaster::where('id', $record->id)->update(['is_poc_sync' => 2]);
                return $duplicateCheck;
            }
            $response = $this->sendHHAPOCDetails([
                'visitDetails'       => $visitDetails,
                'patient'            => $patientDetails,
                'pocTask'            => $getList,
                'requestAll'         => $record,
                'documentPOCDetails' => $pocDocument,
                'agencyId'           => $record->agency_id,
                'agSetting'           => $agSetting,
            ]);

            if (isset($response['status']) && $response['status'] == 1) {
                TaskHealthMaster::where('id', $record->id)->update(['is_poc_sync' => 2]);
                LogsService::save([
                    'type'         => 'POC created on HHX Patient from task health',
                    'link'         => $this->signature,
                    'module'       => 'Patient Appointment',
                    'object_id'    => $record->patient_id,
                    'message'      => 'Task health has created POC and uploaded to HHX Patient via System Admin',
                    'new_response' => serialize($response),
                    'ip'           => Utility::getIP(),
                    'created_by'   => '482',
                ]);
                return ['type' => 'success', 'message' => 'POC linked', 'data' => ['response' => $response]];
            }

            $reqData = $response['_req_data'] ?? ['task_id' => $record->task_id, 'patient_id' => $record->patient_id];
            unset($response['_req_data']);
            return [
                'type'    => 'error',
                'message' => 'POC error: ' . ($response['message'] ?? 'unknown'),
                'data'    => [
                    'req_data'      => $reqData,
                    'response_data' => $response,
                ],
            ];

        } catch (Exception $e) {
            return [
                'type'    => 'error',
                'message' => 'POC exception: ' . $e->getMessage(),
                'data'    => [
                    'req_data'      => ['task_id' => $record->task_id, 'patient_id' => $record->patient_id],
                    'response_data' => ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()],
                ],
            ];
        }
    }

    private function getPOCDocument(array $visitTaskHealth, $data, $taskId): ?array
    {
        $docUrl = '';
        $title  = '';

        $documents = $visitTaskHealth['data']['task']['documents'] ?? [];
        foreach ($documents as $doc){
            if (in_array($doc['type']['id'], Utility::getPOCDocumentTypeId())) {
                $docUrl = $doc['url'];
                $title  = $doc['type']['title'];
            }
        }

        if (empty($docUrl)) {
            return null;
        }

        return TaskHealthApiHelper::getCommonDocumentCreate([
            'url'             => $docUrl,
            'title'           => $title,
            'visitTaskHealth' => $visitTaskHealth,
            'requestAll'      => ['visit_task_health_id' => $taskId],
        ]);
    }

    /**
     * Call HHA SearchPatientPOC and check whether a POC with the same
     * certification start/stop date already exists.
     *
     * Returns a skipped result array when a duplicate is found, or null
     * when it is safe to proceed with creation.
     */
    private function checkExistingPOC($record, $patientDetails, array $visitDetails)
    {
        try {
            $certPeriod = $visitDetails['data']['task']['certificationPeriod'] ?? [];
            $rawStart   = $certPeriod['startDate'] ?? null;
            $rawEnd     = $certPeriod['endDate']   ?? null;

            if (empty($rawStart) || empty($rawEnd)) {
                // Cannot determine date range — let creation proceed
                return null;
            }

            $newStart = date('Y-m-d', strtotime($rawStart));
            $newStop  = date('Y-m-d', strtotime($rawEnd));

            // Build the $details object expected by getSearchPatientPOCList()
            $agencyDetail = Agency::where('id', $record->agency_id)
                ->where('enable_hha', 1)
                ->whereNotNull('app_name')
                ->whereNotNull('app_key')
                ->first();

            if (!$agencyDetail) {
                // Cannot fetch agency credentials — let creation proceed
                return null;
            }

            $details               = new \stdClass();
            $details->patient_id   = $patientDetails->link_hha_patient;
            $details->agencyDetail = $agencyDetail;

            $existingPOCs = HHAPatientHelper::getSearchPatientPOCList($details);

            if (empty($existingPOCs)) {
                return null;
            }

            foreach ($existingPOCs as $poc) {
                $patientInfo = $poc['PatientInfo'] ?? [];

                if (empty($patientInfo['StartDate']) || empty($patientInfo['StopDate'])) {
                    continue;
                }

                $existingStart = date('Y-m-d', strtotime($patientInfo['StartDate']));
                $existingStop  = date('Y-m-d', strtotime($patientInfo['StopDate']));

                if ($existingStart === $newStart && $existingStop === $newStop) {
                    return [
                        'type'    => 'success',
                        'message' => 'POC skipped: already exists on HHA for period '
                                     . date('m/d/Y', strtotime($newStart))
                                     . ' to '
                                     . date('m/d/Y', strtotime($newStop)),
                        'data'    => [
                            'existing_poc_id' => $patientInfo['POCID'] ?? null,
                            'start_date'      => $newStart,
                            'stop_date'       => $newStop,
                        ],
                    ];
                }
            }

            return null;

        } catch (Exception $e) {
            // If the duplicate check itself fails, do NOT block creation
            return null;
        }
    }

    private function sendHHAPOCDetails(array $context): array
    {
        try{
            $getVisitDetails    = $context['visitDetails'];
            $getList            = $context['pocTask'];
            $getPatientDetails  = $context['patient'];
            $data               = $context['requestAll'];
            $documentPOCDetails = $context['documentPOCDetails'];
            $agSetting = $context['agSetting'];

            $finalResponse = [
                'start_date'   => $getVisitDetails['data']['task']['certificationPeriod']['startDate'],
                'stop_date'    => $getVisitDetails['data']['task']['certificationPeriod']['endDate'],
                'shift'        => 1,
                'task_id'      => [],
                'mintime'      => [],
                'maxtime'      => [],
                'as_requested' => [],
            ];

            foreach ($getList as $val) {
                $finalResponse['task_id'][]      = $val->hha_task_id;
                $finalResponse['mintime'][]      = 1;
                $finalResponse['maxtime'][]      = 7;
                $finalResponse['as_requested'][] = 'true';
            }
            // $notes = 'Visit needed';
            $notes = isset($agSetting->poc_group_notes) && !empty($agSetting->poc_group_notes) ? $agSetting->poc_group_notes :'';
            $response = HHAPatientHelper::createPatientPOCDetails(
                $getPatientDetails->link_hha_patient,
                $finalResponse,
                $notes
            );

            if (isset($response['status']) && $response['status'] == 1) {
                $finalResponse['file_name']         = basename(parse_url($documentPOCDetails['task_url'], PHP_URL_PATH));
                $finalResponse['hha_document_type'] = $documentPOCDetails['documentType'];

                HHAAuditLog::create([
                    'type'            => 'POC',
                    'patient_id'      => $data->patient_id,
                    'ref_id'          => $data->task_id,
                    'ref_obj'         => 'Task Health',
                    'status'          => 'Sent',
                    'send_response'        => serialize($finalResponse),
                    'hha_patient_id'  => $getPatientDetails->link_hha_patient,
                    'return_response' => serialize($response['data']),
                    'created_by'      => null,
                ]);

                (new TaskHealthFlagsService())->saveFlagsOnlyPOCCron(
                    $getVisitDetails['data']['patient']['id'],
                    $data->task_id,
                    $data->patient_id,
                    1
                );

                $this->sendHHAPatientDocument($context);

                $filePath = public_path('allupload/task_health/' . $data->task_id);
                try{
                if (File::exists($filePath)) {
                        File::deleteDirectory($filePath);
                    }    
                }catch (Exception $e) {
                    
                }
            } else {
                $response['_req_data'] = $finalResponse;
            }

            return $response;
        }catch(Exception $e){
            return [
                'type'    => 'error',
                'message' => 'POC exception: ' . $e->getMessage(),
                'data'    => [
                    'req_data'      => ['context' => $context],
                    'response_data' => ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()],
                ],
            ];
        }
    }

    private function sendHHAPatientDocument(array $context): void
    {
        $path      = parse_url($context['documentPOCDetails']['url'], PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        HHAPatientHelper::getSendHHADocument(
            $context['agencyId'],
            $context['documentPOCDetails']['title'],
            $extension,
            $context['documentPOCDetails']['documentType'],
            $context['patient']->link_hha_patient,
            file_get_contents($context['documentPOCDetails']['task_url'])
        );
    }

    // -------------------------------------------------------------------------
    // Step 3: Supervision
    // -------------------------------------------------------------------------

    private function runSupervision($record, array $visitDetails, $patientDetails, array $agencyId): array
    {
        try {
            $supervisionService = new TaskHealthSupervisionService(new HHAPatientService(), new AgencyService());

            $context = $supervisionService->validateAndResolveContext($visitDetails, $patientDetails, $agencyId);
            if ($context['status'] !== 1) {
                return [
                    'type'    => 'error',
                    'message' => 'Supervision ' . $context['type'] . ': ' . $context['message'],
                    'data'    => [
                        'req_data'      => [
                            'task_id'        => $record->task_id,
                            'patient_id'     => $record->patient_id,
                            'agency_id'      => $agencyId,
                            'patient_agency' => $patientDetails->agency_id ?? null,
                        ],
                        'response_data' => ['error' => $context['message'], 'type' => $context['type']],
                    ],
                ];
            }

            $caregiverId = $context['caregiverId'];

            $result = $supervisionService->executeSupervision(
                $record->task_id,
                $visitDetails,
                $agencyId,
                $caregiverId,
                $record->patient_id,
            );

            if ($result['status'] !== 1) {
                return [
                    'type'    => 'error',
                    'message' => 'Supervision error: ' . $result['message'],
                    'data'    => [
                        'req_data'      => ['caregiverId' => $caregiverId, 'agencyId' => $agencyId],
                        'response_data' => ['error' => $result['message']],
                    ],
                ];
            }

            LogsService::save([
                'type'         => 'SuperVision created on HHX Patient from task health',
                'link'         => $this->signature,
                'module'       => 'Patient Appointment',
                'object_id'    => $record->patient_id,
                'message'      => 'Task health has created Supervision and uploaded to HHX Patient',
                'new_response' => serialize(['sendResponseForHHA' => $result['sendResponseForHHA'], 'hhaUpdateData' => $result['hhaUpdateDataArray'], 'hhaAddData' => $result['hhaAddData']]),
                'old_response' => serialize(['overdueMedicals' => $result['overDueMedicals']]),
                'ip'           => Utility::getIP(),
                'created_by'   => '482',
            ]);

            if (!empty($result['hhaUpdateDataArray'])) {
                HHAAuditLog::create([
                    'type'            => 'supervision',
                    'patient_id'      => $record->patient_id,
                    'ref_id'          => $record->task_id,
                    'ref_obj'         => 'Task Health',
                    'status'          => 'Sent',
                    'send_response'   => serialize(['sendResponseForHHA' => $result['sendResponseForHHA'], 'hhaUpdateData' => $result['hhaUpdateDataArray']]),
                    'hha_patient_id'  => $patientDetails->link_hha_patient,
                    'return_response' => serialize($result['returnResponse']),
                    'created_by'      => null,
                    'message'         => 'supervision updated',
                ]);
            }

            if (!empty($result['hhaAddData'])) {
                HHAAuditLog::create([
                    'type'            => 'supervision',
                    'patient_id'      => $record->patient_id,
                    'ref_id'          => $record->task_id,
                    'ref_obj'         => 'Task Health',
                    'status'          => 'Sent',
                    'send_response'   => serialize(['sendResponseForHHA' => $result['sendResponseForHHA'], 'hhaAddData' => $result['hhaAddData']]),
                    'hha_patient_id'  => $patientDetails->link_hha_patient,
                    'return_response' => serialize($result['returnResponse']),
                    'created_by'      => null,
                    'message'         => 'supervision added',
                ]);
            }

            TaskHealthMaster::where('id', $record->id)->update(['is_supervision_sync' => 2]);

            return ['type' => 'success', 'message' => 'Supervision linked', 'data' => ['result' => $result]];

        } catch (Exception $e) {
            return [
                'type'    => 'error',
                'message' => 'Supervision exception: ' . $e->getMessage(),
                'data'    => [
                    'req_data'      => ['task_id' => $record->task_id, 'patient_id' => $record->patient_id, 'agency_id' => $agencyId],
                    'response_data' => ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()],
                ],
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Step 4: Upload extra documents (Patient Assessment + Emergency Kardex)
    // -------------------------------------------------------------------------

    /** Document type IDs to upload after POC + supervision steps. */
    private static $extraDocTypeIds = [
        80752 => 'NEW - Patient Assessment',
        81049 => 'NEW - Emergency Kardex',
    ];

    /**
     * Find documents with type IDs 80752 and 81049 in the visit's documents
     * array, download each and save to the patient document section via
     * TaskHealthApiHelper::commonDocCreate().
     *
     * Never throws — all errors are captured in the returned array so that
     * the POC/supervision outcome is never affected.
     *
     * @return array  One entry per matched document type:
     *                ['type_id', 'title', 'status' (0|1), 'message']
     */
    private function uploadExtraDocuments($record, array $visitDetails,$task_data,$link_hha_patient,$agSetting): array
    {
        $results   = [];
        $documents = $visitDetails['data']['task']['documents'] ?? [];

        if (empty($documents)) {
            return $results;
        }

        // Index the visit documents by type id for O(1) lookup
        $docByTypeId = [];
        foreach ($documents as $doc) {
            $typeId = $doc['type']['id'] ?? null;
            if ($typeId !== null) {
                $docByTypeId[$typeId] = $doc;
            }
        }

        // Map each extra doc type to its flag column on task_health_flags
        $flagColumnMap = [
            80752 => 'assessment_check',
            81049 => 'kardex_check',
        ];

        $flagDocColumn = [
            80752 => 'assessment',
            81049 => 'kardex',
        ];

        foreach (self::$extraDocTypeIds as $typeId => $defaultTitle) {
            if (!isset($docByTypeId[$typeId])) {
                // This document type is not present in this visit — skip silently
                continue;
            }

            // Skip if the flag is already set (document was uploaded in a previous run)
            $flagColumn = $flagColumnMap[$typeId] ?? null;
            if(isset($flagDocColumn[$typeId]) && $agSetting->$flagDocColumn[$typeId] == 1){
                if ($flagColumn && !empty($task_data) && (int)($task_data->$flagColumn ?? 0) === 1) {
                    $results[] = [
                        'type_id' => $typeId,
                        'title'   => $defaultTitle,
                        'status'  => 1,
                        'message' => $defaultTitle . ' already uploaded — skipped',
                    ];
                    continue;
                }

                $doc    = $docByTypeId[$typeId];
                $docUrl = $doc['url'] ?? null;
                $title  = $doc['type']['title'] ?? $defaultTitle;

                if (empty($docUrl)) {
                    $results[] = [
                        'type_id' => $typeId,
                        'title'   => $title,
                        'status'  => 0,
                        'message' => 'No URL available for document type ' . $typeId,
                    ];
                    continue;
                }

                try {
                    $requestData['visit_task_health_id'] = $record->task_id;
                    $result = TaskHealthApiHelper::sendToHHAExtraDocument($docUrl,$title,$visitDetails,$requestData,$record->agency_id,$link_hha_patient);
                    $results[] = array_merge(['type_id' => $typeId, 'title' => $title], $result);
                } catch (Exception $e) {
                    $results[] = [
                        'type_id' => $typeId,
                        'title'   => $title,
                        'status'  => 0,
                        'message' => 'Exception uploading doc type ' . $typeId . ': ' . $e->getMessage(),
                    ];
                }
            }
        }

        return $results;
    }

}
