<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Services\AgencyService;
use App\Services\PatientService;
use App\Services\HHAPatientService;
use App\Services\SupervisionService;
use App\Helpers\TaskHealthApiHelper;
use App\Services\HHALogService;
use App\Services\HhaAuditLogService;
use App\Http\Controllers\HHAOtherComplianceController;
use App\Services\TaskHealthSupervisionService;
use App\Helpers\Utility;
use App\Services\LogsService;

class SupervisionController extends BaseController
{
    protected $supervisionService, $patientService, $hhaPatientService, $agencyService,$hhaLogService, $hhaAuditLogService;
    protected $otherComplianceController;
    protected $taskHealthSupervisionService;

    public function __construct(
        SupervisionService $supervisionService,
        PatientService $patientService,
        HHAPatientService $hhaPatientService,
        AgencyService $agencyService,
        HHALogService $hhaLogService,
        HhaAuditLogService $hhaAuditLogService,
        HHAOtherComplianceController $otherComplianceController,
        TaskHealthSupervisionService $taskHealthSupervisionService,
    ) {
        $this->supervisionService           = $supervisionService;
        $this->patientService               = $patientService;
        $this->hhaPatientService            = $hhaPatientService;
        $this->agencyService                = $agencyService;
        $this->hhaLogService                = $hhaLogService;
        $this->otherComplianceController    = $otherComplianceController;
        $this->hhaAuditLogService             = $hhaAuditLogService;
        $this->taskHealthSupervisionService = $taskHealthSupervisionService;
    }

    public function index(Request $request)
    {
        $taskId    = $request->query('task_id');
        $patientId = $request->query('patient_id');

        if (empty($taskId) || empty($patientId)) {
            return response()->json(['status' => false, 'error_msg' => 'task_id and patient_id are required'], 400);
        }

        // 1. Single API call — reuse $visitDetails everywhere below
        $visitDetails = $this->getTaskHealthDetails($taskId);
        if (!$visitDetails) {
            return response()->json(['status' => false, 'error_msg' => 'Task Id not found'], 400);
        }

        // 2. Resolve agency from visit data
        $agencyId = $this->taskHealthSupervisionService->resolveAgencyFromVisit($visitDetails);
        if (!$agencyId) {
            return response()->json(['status' => false, 'error_msg' => 'Agency Id not found'], 400);
        }

        // 3. Patient exists check (controller-specific: fetches from DB using PatientService)
        $patientDetails = $this->validatePatient($patientId);
        if (is_null($patientDetails)) {
            return response()->json(['status' => false, 'error_msg' => 'Patient not found'], 404);
        }

        // 4. Validate all remaining preconditions and resolve caregiverId via shared service
        $context = $this->taskHealthSupervisionService->validateAndResolveContext($visitDetails, $patientDetails, $agencyId);
        if ($context['status'] !== 1) {
            return response()->json(['status' => false, 'error_msg' => $context['message']], $context['httpCode']);
        }
        $caregiverId = $context['caregiverId'];

        // 5. Execute full supervision flow via shared service
        $result = $this->saveMedicalFromTask($taskId, $visitDetails, $agencyId, $caregiverId, $patientId);

        if (!empty($result) && isset($result['returnResponse'])) {
            $user_id = auth()->user()->id;
            // 8. Write audit log
            LogsService::save([
                'type'         => 'SuperVision created on HHX Patient from task health',
                'link'         => url('supervision'),
                'module'       => 'Patient Appointment',
                'object_id'    => $patientId,
                'message'      => 'Task health has created Supervision and uploaded to HHX Patient',
                'new_response' => serialize(['sendResponseForHHA' => $result['sendResponseForHHA'], 'hhaUpdateData' => $result['hhaUpdateDataArray'], 'hhaAddData' => $result['hhaAddData']]),
                'old_response'  => serialize(['overdueMedicals' => $result['overdueMedicals']]),
                'ip'           => Utility::getIP(),
                'created_by'   => $user_id,
            ]);

            if (isset($result['hhaUpdateDataArray']) && !empty($result['hhaUpdateDataArray'])) {
                $this->hhaAuditLogService->save([
                    'type'            => 'supervision',
                    'patient_id'      => $patientId,
                    'ref_id'          => $taskId,
                    'ref_obj'         => 'Task Health',
                    'status'          => 'Sent',
                    'send_response'        => serialize(['sendResponseForHHA' => $result['sendResponseForHHA'], 'hhaUpdateData' => $result['hhaUpdateDataArray']]),
                    'hha_patient_id'  => $patientDetails->link_hha_patient,
                    'return_response' => serialize($result['returnResponse']),
                    'created_by'      => $user_id,
                    'message'   => 'supervision updated'
                ]);
            }

            if (isset($result['hhaAddData']) && !empty($result['hhaAddData'])) {
                $this->hhaAuditLogService->save([
                    'type'            => 'supervision',
                    'patient_id'      => $patientId,
                    'ref_id'          => $taskId,
                    'ref_obj'         => 'Task Health',
                    'status'          => 'Sent',
                    'send_response'        => serialize(['sendResponseForHHA' => $result['sendResponseForHHA'], 'hhaAddData' => $result['hhaAddData']]),
                    'hha_patient_id'  => $patientDetails->link_hha_patient,
                    'return_response' => serialize($result['returnResponse']),
                    'created_by'      => $user_id,
                    'message'   => 'supervision added'
                ]);
            }
        }

        return response()->json([
            'error_msg' => 'Supervision has been successfully created in HHA Other compliance.',
            'data'      => $result,
        ], 200);
    }

    /**
     * Validate patient existence and return patient details.
     * Controller-specific: fetches via PatientService which is not available in the cron.
     */
    private function validatePatient($patientId)
    {
        $patientDetails = $this->patientService->getLinkHAAPatient($patientId);

        return !empty($patientDetails) ? $patientDetails : null;
    }

    /**
     * Execute the full supervision flow via the shared service.
     * The only controller-specific concern here is aborting with a JSON 500 on failure.
     */
    private function saveMedicalFromTask($taskId, array $visitDetails, $agencyId, $caregiverId, $patientId)
    {
        $result = $this->taskHealthSupervisionService->executeSupervision(
            $taskId,
            $visitDetails,
            $agencyId,
            $caregiverId,
            $patientId
        );

        if ($result['status'] !== 1) {
            abort(response()->json(['error_msg' => $result['message'] . ' ', 'status' => 0, 'data' => []], 500));
        }

        if (isset($result['sendDocumentResponse']['status']) && $result['sendDocumentResponse']['status'] !== 1) {
            abort(response()->json(['error_msg' => $result['sendDocumentResponse']['message'] . ' ', 'status' => 0, 'data' => []], 500));
        }

        return [
            'returnResponse' => $result['returnResponse'],
            'sendResponseForHHA'   => $result['sendResponseForHHA'],
        ];
    }

    private function getTaskHealthDetails($taskId)
    {
        return TaskHealthApiHelper::getVisitDetail($taskId, 'cron');
    }
}
