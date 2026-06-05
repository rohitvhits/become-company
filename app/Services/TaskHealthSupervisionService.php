<?php

namespace App\Services;

use App\Helpers\HHACaregiversHelper;
use App\Helpers\HHAPatientHelper;
use App\Helpers\TaskHealthApiHelper;
use App\Helpers\Utility;
use App\Model\HHACaregivers;
use App\SiteSetting;
use App\Agency;

class TaskHealthSupervisionService
{
    protected HHAPatientService $hhaPatientService;
    protected AgencyService $agencyService;
    protected $caregiver_name;

    public function __construct(HHAPatientService $hhaPatientService, AgencyService $agencyService)
    {
        $this->hhaPatientService = $hhaPatientService;
        $this->agencyService     = $agencyService;
        $this->caregiver_name     = '';
    }

    // =========================================================================
    // Validation helpers
    // =========================================================================

    /**
     * Resolve local agency from already-fetched visit details.
     *
     * Returns null when no agency ID is present in the visit data.
     * Callers are responsible for returning an appropriate error when null.
     */
    public function resolveAgencyFromVisit(array $visitDetails): ?array
    {
        $agencyIdValue = $visitDetails['data']['task']['agencyId'] ?? null;
        if (!$agencyIdValue) {
            return null;
        }

        $agency = TaskHealthApiHelper::detectLocalAgency($agencyIdValue);

        if (env('HHA_DEVELOPEMENT_CRED') == 'development') {
            $agency = ['id' => env('HHA_DEVELOPEMENT_AGENCY_ID')];
        }

        return $agency;
    }

    /**
     * Validate all supervision preconditions and resolve the caregiver ID.
     *
     * Checks (in order):
     *   1. Visit data present
     *   2. Patient linked to HHA
     *   3. Patient agency matches visit agency
     *   4. Caregiver name present in visit
     *   5. Caregiver found in patient visit history
     *
     * Returns on success:
     *   ['status' => 1, 'caregiverId' => '...']
     *
     * Returns on failure:
     *   ['status' => 0, 'type' => 'error'|'skipped', 'httpCode' => int, 'message' => '...']
     *
     *   'type'     — used by the cron to prefix the log message ("Supervision error/skipped: ...")
     *   'httpCode' — used by the controller to set the HTTP response status code
     */
    public function validateAndResolveContext(array $visitDetails, $patientDetails, array $agencyId): array
    {
        if (empty($visitDetails['data'])) {
            return ['status' => 0, 'type' => 'error',   'httpCode' => 400, 'message' => 'visit details not found'];
        }

        if (empty($patientDetails) || empty($patientDetails->link_hha_patient)) {
            return ['status' => 0, 'type' => 'skipped', 'httpCode' => 404, 'message' => 'patient not linked to HHA'];
        }

        if ($agencyId['id'] != $patientDetails->agency_id) {
            return [
                'status'   => 0,
                'type'     => 'skipped',
                'httpCode' => 403,
                'message'  => 'patient agency mismatch (patient: ' . $patientDetails->agency_id . ', visit: ' . $agencyId['id'] . ')',
            ];
        }

        $caregiverName = $visitDetails['data']['caregiver']['name'] ?? '';
        if (env('HHA_DEVELOPEMENT_CRED') == 'development') {
            $caregiverName = 'Test Caregiver';
        }
        if (empty($caregiverName)) {
            return ['status' => 0, 'type' => 'skipped', 'httpCode' => 400, 'message' => 'caregiver information not found in visit'];
        }

        $caregiverId = $this->findCaregiver($patientDetails->link_hha_patient, $agencyId, $caregiverName);
        if (env('HHA_DEVELOPEMENT_CRED') == 'development') {
            $caregiverId = env('HHA_DEVELOPEMENT_CRED_KEY');
        }
        if (empty($caregiverId)) {
            return [
                'status'   => 0,
                'type'     => 'skipped',
                'httpCode' => 404,
                'message'  => 'caregiver "' . $caregiverName . '" not found in HHA visits',
            ];
        }

        return ['status' => 1, 'caregiverId' => $caregiverId];
    }

    // =========================================================================
    // Core supervision flow
    // =========================================================================

    /**
     * Search patient visits (up to 6 months back) to find caregiver ID by name.
     * Returns null when no match is found.
     */
    public function findCaregiver(string $hhaPatientId, array $agencyId, string $caregiverName): ?string
    {
        $query = $this->hhaPatientService->getDetailsByPatientID($hhaPatientId, $agencyId['id']);
        $query->agencyDetails = $this->agencyService->getDetailsById($agencyId['id']);

        for ($i = 0; $i < 6; $i++) {
            $visits = HHAPatientHelper::getVisitCalenderdata(
                $query,
                date('Y-m-01', strtotime("-{$i} months")),
                date('Y-m-t',  strtotime("-{$i} months"))
            );

            $match = collect($visits)->first(function ($visit) use ($caregiverName) {
                $name = ($visit['caregiver_first_name'] ?? '') . ' ' . ($visit['caregiver_last_name'] ?? '');
                return stripos($name, $caregiverName) !== false;
            });

            if ($match) {
                return $match['caregiver_id'] ?? null;
            }
        }

        return null;
    }

    /**
     * Execute the full supervision send flow:
     *   1. Resolve document & build file payload
     *   2. Update any non-completed compliance records
     *   3. Create new compliance entry in HHA
     *   4. Send the supervision document to HHA
     *   5. Save TaskHealth flags
     *   6. Write audit log
     *
     * $completedDate is passed by the caller so each entry point can format it
     * the way it already does (cron: date('Y-m-d'), controller: Utility::convertYMD(...)).
     *
     * Returns on success:
     *   ['status' => 1, 'sendResponseForHHA' => [...], 'hhaUpdateDataArray' => [...],
     *    'hhaAddData' => [...], 'returnResponse' => [...], 'sendDocumentResponse' => [...]]
     *
     * Returns on failure:
     *   ['status' => 0, 'message' => '...']
     *
     * Callers are responsible only for their own error format and their specific POC log.
     */
    public function executeSupervision(
        string $taskId,
        array  $visitDetails,
        array  $agencyId,
        string $caregiverId,
        int    $patientId,
    ): array {
        // 1. Resolve document & build file payload
        $doc = $this->getSupervisionDocument($taskId, $visitDetails);
        if (empty($doc['url'])) {
            return ['status' => 0, 'message' => 'no matching document found'];
        }

        $extension = pathinfo(parse_url($doc['url'], PHP_URL_PATH), PATHINFO_EXTENSION);
        $fileData  = [
            'fileName'    => basename($doc['url']),
            'extension'   => $extension,
            'fileContent' => file_get_contents($doc['url']),
        ];

        // 2. Resolve medical ID and due date
        $medicalId = Utility::getHHAOtherComplianceMedicalId($agencyId['id']);
        $completedDate = date('Y-m-d', strtotime($visitDetails['data']['supervisoryForm']['visitDate']));
        $dueDate       = date('Y-m-d', strtotime("+1 year", strtotime($completedDate)));

        // 3. Update non-completed compliance records
        $hhaUpdateDataArray = $this->updateOverdueCompliance(
            $agencyId, $caregiverId, $medicalId, $fileData, $completedDate,$visitDetails
        );

        // 4. Build payloads used by callers for their Supervision logs
        $sendResponseForHHA = [
            'agency_id'     => $agencyId['id'],
            'caregiverId'   => $caregiverId,
            'medical_id'    => $medicalId,
            'due_date'      => $dueDate,
            'attachment'    => basename($doc['url']),
            'document_type' => $doc['supervisionDocumentTypeId'],
            'completed_date' => $completedDate
        ];
        if(env('HHA_DEVELOPEMENT_CRED') == 'development'){
            $visitDetails['data']['caregiver']['name'] = 'Test Caregiver';
        }
        $hhaAddData = [
            'agency_id'   => $agencyId['id'],
            'caregiverId' => $caregiverId,
            'medicalId'   => $medicalId,
            'dueDate'     => $dueDate,
            'hha_caregiver' => $this->caregiver_name,
            'task_health_caregiver' => $visitDetails['data']['caregiver']['name']
        ];

        // 5. Create compliance entry in HHA
        $response = HHACaregiversHelper::createCaregiverOtherCompliance(
            $agencyId['id'], $caregiverId, $medicalId, '', '', $dueDate
        );
        if (!isset($response['status']) || $response['status'] !== 1) {
            return [
                'status'  => 0,
                'message' => $response['message'] ?? 'failed to create compliance in HHA',
            ];
        }

        // 6. Send supervision document to HHA
        $sendDocumentResponse = $this->sendSupervisionDocument(
            $agencyId['id'], $fileData, $doc['supervisionDocumentTypeId'], $caregiverId
        );

        // 7. Save TaskHealth flags
        (new TaskHealthFlagsService())->saveFlagsOnlySuperVisionCron(
            $visitDetails['data']['patient']['id'],
            $taskId,
            $patientId,
            1
        );
        
        return [
            'status'               => 1,
            'sendResponseForHHA'   => $sendResponseForHHA,
            'hhaUpdateDataArray'   => $hhaUpdateDataArray['hhaUpdateData'] ?? [],
            'hhaAddData'           => $hhaAddData ?? [],
            'returnResponse'       => $response['data'] ?? [],
            'sendDocumentResponse' => $sendDocumentResponse,
            'overDueMedicals'       => $hhaUpdateDataArray['overdueMedicals']?? []
        ];
    }

    // =========================================================================
    // Internal helpers — called by executeSupervision() only
    // =========================================================================

    /**
     * Extract supervision document details from already-fetched visit data.
     */
    protected function getSupervisionDocument(string $taskId, array $visitDetails): array
    {
        $docUrl = '';
        $title  = '';

        foreach ($visitDetails['data']['task']['documents'] as $doc) {
            if (in_array($doc['type']['id'], Utility::getSuperVisorCaregiverDocumentTypeId())) {
                $docUrl = $doc['url'];
                $title  = $doc['type']['title'];
            }
        }

        return TaskHealthApiHelper::getCommonDocumentCreate([
            'url'             => $docUrl,
            'title'           => $title,
            'visitTaskHealth' => $visitDetails,
            'requestAll'      => ['visit_task_health_id' => $taskId],
        ]);
    }

    /**
     * Update all non-completed HHA other-compliance records matching the caregiver and medical ID.
     * Returns the updated record payloads so callers can include them in their POC logs.
     */
    protected function updateOverdueCompliance(
        array  $agencyId,
        string $caregiverId,
        string $medicalId,
        array  $fileData,
        string $completedDate,
        $visitDetails
    ): array {
        $agency = Agency::getAllDetailsbyAgencyId($agencyId);
        $caregiver = HHACaregivers::where('caregiver_id', $caregiverId)
            ->where('agency_fk', $agencyId['id'])
            ->where('hha_delete_flag', 'N')
            ->first();
        if(isset($caregiver) && !empty($caregiver)){
            $caregiver->agencyDetails = $agency;
        }
        if(empty($caregiver)){
            // need to check if empty caregiver what we need to do
        }
        $caregiverMedicals = HHACaregiversHelper::getCaregiverMedicalDetails($caregiver, $caregiverId);
        
        $overdueMedicals = $this->getOverDueData($caregiverMedicals,$medicalId,$caregiverId); 
        
        $hhaUpdateDataArray = [];
        $caregiverName = $visitDetails['data']['caregiver']['name'] ?? '';
        if (env('HHA_DEVELOPEMENT_CRED') == 'development') {
            $caregiverName = 'Test Caregiver';
        }
        foreach ($overdueMedicals as $existing) {
            $hhaUpdateData = [
                'agency_id'                     => $agencyId['id'],
                'caregiver_id'                  => $existing['caregiver_id'],
                'caregiver_other_compliance_id' => $medicalId,
                'compliance_id'                 => $existing['caregiver_medical_id'],
                'completed_date'                => $completedDate,
                'extension'                     => $fileData['extension'],
                'medical_name'                  => $fileData['fileName'],
                'file'                          => $fileData['fileContent'],
                'notes'                         => 'Task Health',
                'due_date'                      => $existing['due_date'],
                'result'                        => Utility::getHHAOtherComplianceMedicalResultId($agencyId['id'], $medicalId),
            ];
            HHACaregiversHelper::updateCaregiverOtherCompliance($hhaUpdateData);
            $hhaUpdateData['hha_caregiver'] = $this->caregiver_name = $caregiver->first_name.' '.$caregiver->last_name;
            $hhaUpdateData['task_health_caregiver'] = $caregiverName;
            $hhaUpdateDataArray[] = $hhaUpdateData;
        }

        return ['hhaUpdateData' => $hhaUpdateDataArray, 'overdueMedicals' => $overdueMedicals];
    }

    /**
     * Upload the supervision document to HHA for a caregiver.
     */
    protected function sendSupervisionDocument(
        string $agencyId,
        array  $fileData,
        string $documentTypeId,
        string $caregiverId
    ) {
        return HHACaregiversHelper::getSendHHADocument(
            $agencyId,
            $fileData['fileName'],
            $fileData['extension'],
            $documentTypeId,
            $caregiverId,
            $fileData['fileContent']
        );
    }

    protected function getOverDueData($caregiverMedicals,$medicalId,$caregiverId){
        $months  = SiteSetting::where('del_flag', 'N')->value('supervision_due_date_months') ?? 12;
        $today = now();
        $futureDate = now()->addMonths($months);
        $overdueMedicals = array_filter($caregiverMedicals ?? [], function ($m) use ($medicalId, $caregiverId, $today, $futureDate) {
            if (
                $m['caregiver_id'] != $caregiverId ||
                $m['medical_id'] != $medicalId ||
                empty($m['due_date'])
            ) {
                return false;
            }
            $dueDate = \Carbon\Carbon::parse($m['due_date']);
            // 1. Overdue (include always if not completed)
            if ($m['status'] != 'Completed' && $dueDate->lt($today)) {
                return true;
            }

            // 2. Pending within dynamic months
            if ($m['status'] == 'Pending' && $dueDate->between($today, $futureDate)) {
                return true;
            }

            return false;
        });
        return $overdueMedicals;
    }
}
