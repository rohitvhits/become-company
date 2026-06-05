<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TaskHealthApiHelper;
use App\Helpers\HHAPatientHelper;
use App\Model\TaskHealthCronLog;
use App\Model\AgencyTaskHealthSetting;
use App\Services\TaskHealthFlagsService;
use Exception;

class TaskHealthUploadDocumentCron extends Command
{
    protected $signature = 'task-health:upload-document-cron';

    protected $description = 'Fetch completed Task Health visits for the configured agency, match HHA patient, and upload documents';

    /** @var array */
    private array $logBuffer = [];

    /** Document type IDs to upload */
    private const DOC_TYPE_IDS = [80752, 81049, 81082, 80950, 81016,80983];

    /** Maps TH doc type ID → agency_task_health_settings column name */
    private const DOC_TYPE_SETTING_MAP = [
        80752 => 'cron_upload_hha_assessment',
        81049 => 'cron_upload_hha_kardex',
        81082 => 'cron_upload_hha_cms_mdo_485',
        80950 => 'cron_upload_hha_supervision',
        81016 => 'cron_upload_hha_patient_package_doc',
        80983 => 'cron_upload_hha_poc',
    ];

    public function handle(): void
    {
        // ── Fetch all TH agencies once ───────────────────────────────────────
        $apiResult = TaskHealthApiHelper::getAgencies();
        if (!$apiResult['status'] || empty($apiResult['data'])) {
            $this->pushLog([
                'cron_name' => $this->signature,
                'type'      => 'error',
                'message'   => 'Could not fetch agencies from Task Health API',
            ]);
            $this->flushLogs();
            return;
        }
        foreach ($apiResult['data'] as $thAgency) {
            $thAgencyId = (int) ($thAgency['taskHealthAgencyId'] ?? 0);
            if (!$thAgencyId) {
                continue;
            }

            // Resolve local ERP agency ID
            $localAgency = TaskHealthApiHelper::detectLocalAgency($thAgencyId);
            if (!$localAgency) {
                continue;
            }

            $agencyId = (int) $localAgency['id'];

            if (env('HHA_DEVELOPEMENT_CRED') === 'development') {
                $agencyId = (int) env('HHA_DEVELOPEMENT_AGENCY_ID');
            }
            // Only process agencies that have upload_document_cron enabled
            $setting = AgencyTaskHealthSetting::where('agency_id', $agencyId)
                ->where('del_flag', 'N')
                ->first();
            if (isset($setting->upload_document_cron) && $setting->upload_document_cron == 1) {
                $this->processAgency($thAgencyId, $agencyId, $setting);
            }
        }

        $this->flushLogs();
    }

    private function processAgency(int $thAgencyId, int $agencyId, AgencyTaskHealthSetting $setting): void
    {
        // ── Step 1: Fetch all completed visits for this agency ───────────────
        $visits = $this->fetchAllCompletedVisits($thAgencyId);
        if (empty($visits)) {
            return;
        }

        // ── Step 2: Load already-processed task IDs in one query ─────────────
        $allTaskIds  = array_filter(array_column($visits, 'taskId'));
        $doneTaskIds = TaskHealthCronLog::where('cron_name', $this->signature)
            ->whereIn('task_id', $allTaskIds)
            ->whereIn('type', ['success'])
            ->pluck('task_id')
            ->flip()
            ->all();

        // ── Step 3: Process only unhandled visits ────────────────────────────
        foreach ($visits as $visit) {
            $taskId = $visit['taskId'] ?? null;
            if (!$taskId || isset($doneTaskIds[$taskId])) {
                continue;
            }

            $result = $this->processVisit($visit, $agencyId, $setting);

            $this->pushLog([
                'cron_name'      => $this->signature,
                'task_id'        => $taskId,
                'hha_patient_id' => $result['data']['hha_patient_id'] ?? null,
                'agency_id'      => $agencyId,
                'type'           => $result['type'],
                'message'        => $result['message'],
                'data'           => $result['data'] ?? null,
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Core visit processor
    // ──────────────────────────────────────────────────────────────────────────

    private function processVisit(array $visit, int $agencyId, AgencyTaskHealthSetting $setting): array
    {
        $taskId    = (int) ($visit['taskId'] ?? 0);
        $firstName = trim($visit['patientFirstName'] ?? '');
        $lastName  = trim($visit['patientLastName']  ?? '');
        $phone     = preg_replace('/[^0-9]/', '', $visit['patientPhone'] ?? '');

        // Strip leading country code "1" if 11 digits
        if (strlen($phone) === 11 && str_starts_with($phone, '1')) {
            $phone = substr($phone, 1);
        }

        // ── Step A: Match HHA patient ────────────────────────────────────────
        $sendData = [
            'hha_patient_first_name' => $firstName,
            'hha_patient_last_name'  => $lastName,
            'hha_patient_phone_no'   => $phone,
            'status' => 'Active'
        ];

        try {
            $hhaPatientData = HHAPatientHelper::searchPatientForHHAWithAllCondition($agencyId, $sendData);
            if (empty($hhaPatientData)) {
                $hhaPatientData = HHAPatientHelper::searchPatientForHHAWithAllCondition($agencyId, [
                    'hha_patient_first_name' => $lastName,
                    'hha_patient_last_name'  => $firstName,
                    'hha_patient_phone_no'   => $phone,
                    'status'                 => 'Active',
                ]);
            }
        } catch (Exception $e) {
            return [
                'type'    => 'error',
                'message' => "Task {$taskId}: HHA search exception — {$e->getMessage()}",
                'data'    => ['task_id' => $taskId, 'search' => $sendData],
            ];
        }

        if (empty($hhaPatientData[0]['patient_id'])) {
            return [
                'type'    => 'skipped',
                'message' => "Task {$taskId}: HHA patient not found — skipped",
                'data'    => ['task_id' => $taskId, 'search' => $sendData],
            ];
        }

        $hhaPatientId = $hhaPatientData[0]['patient_id'];

        // ── Step B: Fetch visit detail ───────────────────────────────────────
        $visitDetails = TaskHealthApiHelper::getVisitDetail($taskId, 'cron');
        if (empty($visitDetails['data'])) {
            return [
                'type'    => 'error',
                'message' => "Task {$taskId}: visit detail API failed",
                'data'    => ['task_id' => $taskId, 'hha_patient_id' => $hhaPatientId],
            ];
        }

        // ── Step C: Upload documents ─────────────────────────────────────────
        $documents   = $visitDetails['data']['task']['documents'] ?? [];
        $docByTypeId = [];
        foreach ($documents as $doc) {
            $typeId = $doc['type']['id'] ?? null;
            if ($typeId && in_array($typeId, self::DOC_TYPE_IDS)) {
                $docByTypeId[$typeId] = $doc;
            }
        }

        if (empty($docByTypeId)) {
            return [
                'type'    => 'skipped',
                'message' => "Task {$taskId}: no uploadable documents found — skipped",
                'data'    => ['task_id' => $taskId, 'hha_patient_id' => $hhaPatientId],
            ];
        }

        $flagsService           = new TaskHealthFlagsService();
        $flags                  = $flagsService->getTaskFlagIds($taskId);
        $requestData            = ['visit_task_health_id' => $taskId];
        $uploadResults          = [];
        $assessmentUploaded     = 0;
        $kardexUploaded         = 0;
        $patientPackageDocUploaded = 0;
        $supervisionDocUploaded = 0;
        $mdoDocUploaded = 0;
        $pocDocUploaded = 0;

        foreach (self::DOC_TYPE_IDS as $typeId) {
            // Skip if flag already set for this doc type
            if ($typeId === 80752 && !empty($flags->assessment_check)) {
                $uploadResults[] = ['type_id' => $typeId, 'status' => 1, 'message' => 'Assessment already uploaded — skipped'];
                continue;
            }
            if ($typeId === 81049 && !empty($flags->kardex_check)) {
                $uploadResults[] = ['type_id' => $typeId, 'status' => 1, 'message' => 'Kardex already uploaded — skipped'];
                continue;
            }
            if ($typeId === 81016 && !empty($flags->patient_package_doc_check)) {
                $uploadResults[] = ['type_id' => $typeId, 'status' => 1, 'message' => 'Patient package doc already uploaded — skipped'];
                continue;
            }

            if ($typeId === 80950 && !empty($flags->supervision_check)) {
                $uploadResults[] = ['type_id' => $typeId, 'status' => 1, 'message' => 'Supervision doc already uploaded — skipped'];
                continue;
            }

            if ($typeId === 81082 && !empty($flags->mdo_check)) {
                $uploadResults[] = ['type_id' => $typeId, 'status' => 1, 'message' => 'Mdo doc already uploaded — skipped'];
                continue;
            }

            if ($typeId === 80983 && !empty($flags->poc_check)) {
                $uploadResults[] = ['type_id' => $typeId, 'status' => 1, 'message' => 'POC doc already uploaded — skipped'];
                continue;
            }

            // Check agency setting flag for this doc type
            $settingField = self::DOC_TYPE_SETTING_MAP[$typeId] ?? null;
            if ($settingField && (!isset($setting->$settingField) || empty($setting->$settingField))) {
                $uploadResults[] = ['type_id' => $typeId, 'status' => 1, 'message' => "Doc type {$typeId} ({$settingField}) disabled in agency settings — skipped"];
                continue;
            }

            if (!isset($docByTypeId[$typeId])) {
                $uploadResults[] = ['type_id' => $typeId, 'status' => 0, 'message' => "Document type {$typeId} not present in visit"];
                continue;
            }

            $doc        = $docByTypeId[$typeId];
            $docUrl     = $doc['url'] ?? null;
            $title      = $doc['type']['title'] ?? "Document {$typeId}";
            $docTypeId  = TaskHealthApiHelper::getHhaDocType($typeId, $agencyId, $title);

            if (empty($docUrl)) {
                $uploadResults[] = ['type_id' => $typeId, 'title' => $title, 'status' => 0, 'message' => "Empty URL for document type {$typeId}"];
                continue;
            }

            try {
                $result = TaskHealthApiHelper::sendToHHAExtraDocument(
                    $docUrl,
                    $title,
                    $visitDetails,
                    $requestData,
                    $agencyId,
                    $hhaPatientId,
                    $docTypeId
                );
                $uploadResults[] = array_merge(['type_id' => $typeId, 'title' => $title], $result);

                if (($result['status'] ?? 0) == 1) {
                    if ($typeId === 80752) $assessmentUploaded         = 1;
                    if ($typeId === 81049) $kardexUploaded             = 1;
                    if ($typeId === 81016) $patientPackageDocUploaded  = 1;
                    if ($typeId === 80950) $supervisionDocUploaded     = 1;
                    if ($typeId === 81082) $mdoDocUploaded             = 1;
                    if ($typeId === 80983) $pocDocUploaded             = 1;
                }
            } catch (Exception $e) {
                $uploadResults[] = [
                    'type_id' => $typeId,
                    'title'   => $title,
                    'status'  => 0,
                    'message' => "Exception uploading type {$typeId}: {$e->getMessage()}",
                ];
            }
        }

        // ── Step D: Set flags for successfully uploaded docs ─────────────────
        if ($assessmentUploaded || $kardexUploaded || $patientPackageDocUploaded || $supervisionDocUploaded || $mdoDocUploaded || $pocDocUploaded) {
            try {
                $flagsService->saveFlagsExtraDocsCron($visit['patientId'], $taskId, null, $assessmentUploaded, $kardexUploaded, $patientPackageDocUploaded);
                if($supervisionDocUploaded == 1){
                    $flagsService->saveFlagsOnlySuperVisionCron(
                        $visit['patientId'],
                        $taskId,
                        null,
                        1
                    );
                }
                if($mdoDocUploaded == 1){
                    $flagsService->saveFlagsOnlyMDOCron(
                        $visit['patientId'],
                        $taskId,
                        null,
                        1
                    );
                }
                if($pocDocUploaded == 1){
                    $flagsService->saveFlagsOnlyPOCCron(
                        $visit['patientId'],
                        $taskId,
                        null,
                        1
                    );
                }
            } catch (Exception $e) {
                // Non-fatal — log but continue
                $uploadResults[] = ['type_id' => 0, 'status' => 0, 'message' => 'Flag save exception: ' . $e->getMessage()];
            }
        }

        $allOk = collect($uploadResults)->every(fn($r) => ($r['status'] ?? 0) == 1);

        return [
            'type'    => $allOk ? 'success' : 'error',
            'message' => "Task {$taskId}: " . implode(' | ', array_column($uploadResults, 'message')),
            'data'    => [
                'task_id'        => $taskId,
                'hha_patient_id' => $hhaPatientId,
                'uploads'        => $uploadResults,
            ],
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Resolve the Task Health agency ID by matching agency name from the API list.
     */
    private function resolveThAgencyId(string $agencyName): ?int
    {
        $result = TaskHealthApiHelper::getAgencies();
        if (!$result['status'] || empty($result['data'])) {
            return null;
        }

        $match = collect($result['data'])->first(function ($agency) use ($agencyName) {
            return strcasecmp(trim($agency['agencyName'] ?? ''), trim($agencyName)) === 0;
        });

        return $match ? (int) $match['taskHealthAgencyId'] : null;
    }

    /**
     * Fetch all completed visits for a given TH agency ID (all pages).
     */
    private function fetchAllCompletedVisits(int $thAgencyId): array
    {
        $all      = [];
        $page     = 1;
        $maxPages = 50;
        $today    = date('Y-m-d');
        $firstDay = date('Y-01-01');
        $params = array_filter([
            'sortBy'       => 'createdAt',
            'agencyIds'     => [$thAgencyId],
            'fromDate'     => $firstDay,
            'toDate'       => $today,
            'limit'        => 50,
            'page'      => $page,
            'status'       => ['Completed'],
        ], fn($v) => $v !== null && $v !== '');

        do {
            $params['page'] = $page;
            $result = TaskHealthApiHelper::getVisits($params);
            if (!$result['status']) {
                break;
            }

            $items      = $result['data']['items'] ?? [];
            $totalPages = $result['data']['pagination']['totalPages'] ?? 1;

            $all = array_merge($all, $items);
            $page++;
        } while ($page <= $totalPages && $page <= $maxPages);

        return $all;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Log buffer
    // ──────────────────────────────────────────────────────────────────────────

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
}
