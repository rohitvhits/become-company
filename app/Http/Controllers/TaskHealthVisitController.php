<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TaskHealthApiHelper;
use Illuminate\Support\Facades\Validator;
use App\Services\TaskHealthMasterService;
use App\Services\TaskHealthFlagsService;
use App\Services\AgencyTaskHealthService;
use App\Services\PatientService;
use App\Services\PatientV2Service;
use App\Services\PatientServicesRequest as PatientServicesRequestService;
use App\Services\LogsService;
use App\Model\TaskHealthMaster;
use App\Model\Patient;
use App\Master;
use App\Model\PatientServiceRequest;
use App\Model\PatientWiseServiceRequest;
use App\Helpers\Utility;
use App\Helpers\Common;
use App\Services\PocMatchedTaskService;
use App\Services\DocumentPatientService;
use App\Services\DocumentUploadService;
use App\Services\SendTaskHealthDocumentService;
use App\Services\AgencyTaskHealthSettingService;
use Exception;
use Illuminate\Support\Facades\Storage;

class TaskHealthVisitController extends Controller
{
    protected $taskHealthMasterService;
    protected $taskHealthFlagsService;
    protected $patientService;
    protected $patientV2Service;
    protected $agencyTaskHealthService;
    protected $patientServicesRequestService;
    protected $documentPatientService;
    protected $documentUploadService;
    protected $sendTaskHealthDocumentService;
    protected $agencyTaskHealthSettingService;

    public function __construct(TaskHealthMasterService $taskHealthMasterService, TaskHealthFlagsService $taskHealthFlagsService, PatientService $patientService, PatientV2Service $patientV2Service, AgencyTaskHealthService $agencyTaskHealthService, PatientServicesRequestService $patientServicesRequestService,DocumentPatientService $documentPatientService, DocumentUploadService $documentUploadService, SendTaskHealthDocumentService $sendTaskHealthDocumentService,AgencyTaskHealthSettingService $agencyTaskHealthSettingService)
    {
        $this->taskHealthMasterService       = $taskHealthMasterService;
        $this->taskHealthFlagsService        = $taskHealthFlagsService;
        $this->patientService                = $patientService;
        $this->patientV2Service              = $patientV2Service;
        $this->agencyTaskHealthService       = $agencyTaskHealthService;
        $this->patientServicesRequestService = $patientServicesRequestService;
        $this->documentPatientService = $documentPatientService;
        $this->documentUploadService = $documentUploadService;
        $this->sendTaskHealthDocumentService = $sendTaskHealthDocumentService;
        $this->agencyTaskHealthSettingService = $agencyTaskHealthSettingService;
        $this->middleware('auth');
        $this->middleware('permission:task-health-visit-list', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:task-health-visit-create', ['only' => ['createVisit']]);
        $this->middleware('permission:task-health-visit-update', ['only' => ['cancelVisit']]);
        $this->middleware('permission:task-health-visit-cancel', ['only' => ['editVisit']]);
        $this->middleware('permission:task-health-visit-doc-approve', ['only' => ['approveDocument']]);
        $this->middleware('permission:task-health-visit-doc-change', ['only' => ['openDocumentForChanges']]);
        $this->middleware('permission:task-health-visit-export', ['only' => ['exportCsv']]);
    }

    public function index()
    {
        $auth = auth()->user();
        if (!$auth) {
            return redirect('login');
        }
        $data['menu']           = '';
        $data['user']           = $auth;
        $data['localAgencies']  = $this->agencyTaskHealthService->getAllAgencyList();
        $data['patientServices'] = Master::where('del_flag', 'N')
            ->where('master_type_fk', 11)
            ->where('types', 'Patient')
            ->where('is_disable', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        $data['disciplineOptions'] = Master::where('del_flag', 'N')
            ->where('master_type_fk', 26)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        return view('task_health_visit.task_health_visit_list', $data);
    }

    public function getAgencies()
    {
        $result = TaskHealthApiHelper::getAgencies();
        if ($result['status'] && isset($result['data'])) {
            return response()->json(['status' => 1, 'data' => $result['data']]);
        }
        return response()->json(['status' => 0, 'data' => [], 'message' => $result['error'] ?? 'Failed to fetch agencies']);
    }

    public function ajaxList(Request $request)
    {
        // Helper: split comma-separated string into array, filter empties
        $toArray = fn($val) => !empty($val) ? array_values(array_filter(explode(',', $val))) : null;

        $agencyIds        = $toArray($request->input('agencyIds'));
        $statuses         = $toArray($request->input('status'));
        $reviewStatus     = $toArray($request->input('reviewStatus'));
        $hasCriticalAlert = $request->input('hasCriticalAlert');
        $pocCheck         = $request->input('poc_check');
        $mdoCheck         = $request->input('mdo_check');
        $alertCheck       = $request->input('alert_check');
        $supervisionCheck = $request->input('supervision_check');
        $assessmentCheck      = $request->input('assessment_check');
        $kardexCheck          = $request->input('kardex_check');
        $patientPackageCheck  = $request->input('patient_package_doc_check');
        $flagFilterActive = $pocCheck || $mdoCheck || $alertCheck || $supervisionCheck || $assessmentCheck || $kardexCheck || $patientPackageCheck;

        $perPage     = 50;
        $localPage   = (int) $request->input('page', 1);

        $baseParams = array_filter([
            'sortBy'           => $request->input('sortBy', 'createdAt'),
            'fromDate'         => $request->input('fromDate'),
            'toDate'           => $request->input('toDate'),
            'agencyIds'        => $agencyIds,
            'status'           => $statuses,
            'reviewStatus'     => $reviewStatus,
            'hasCriticalAlert' => $hasCriticalAlert,
            'search'           => $request->input('search'),
            'limit'            => $perPage,
        ], fn($v) => $v !== null && $v !== '' && $v !== []);

        $items      = [];
        $pagination = [];
        $flagsMap   = [];
        $error      = null;

        if ($flagFilterActive) {
            // Fetch all API pages, filter by local flags, then paginate locally
            $allFiltered = [];
            $apiPage     = 1;
            $maxApiPages = 20; // cap at 1000 records

            do {
                $result = TaskHealthApiHelper::getVisits($baseParams + ['page' => $apiPage]);
                if (!$result['status']) {
                    $error = $result['error'] ?? 'API Error';
                    break;
                }

                $pageItems    = $result['data']['items'] ?? [];
                $totalApiPages = $result['data']['pagination']['totalPages'] ?? 1;

                if (empty($pageItems)) break;

                // Load flags for this batch and filter
                $batchFlags = $this->taskHealthFlagsService->getFlagsMapByTaskIds(array_column($pageItems, 'taskId'));
                foreach ($pageItems as $item) {
                    $flag = $batchFlags[(string)($item['taskId'] ?? '')] ?? null;
                    if ($pocCheck         && !($flag && $flag->poc_check))          continue;
                    if ($mdoCheck         && !($flag && $flag->mdo_check))          continue;
                    if ($alertCheck       && !($flag && $flag->alert_check))        continue;
                    if ($supervisionCheck && !($flag && $flag->supervision_check))  continue;
                    if ($assessmentCheck     && !($flag && $flag->assessment_check))          continue;
                    if ($kardexCheck         && !($flag && $flag->kardex_check))              continue;
                    if ($patientPackageCheck && !($flag && $flag->patient_package_doc_check)) continue;
                    $allFiltered[] = $item;
                }

                $apiPage++;
            } while ($apiPage <= $totalApiPages && $apiPage <= $maxApiPages);

            // Local pagination over filtered results
            $total      = count($allFiltered);
            $totalPages = max(1, (int) ceil($total / $perPage));
            $localPage  = min($localPage, $totalPages);
            $offset     = ($localPage - 1) * $perPage;
            $items      = array_slice($allFiltered, $offset, $perPage);

            $pagination = [
                'page'       => $localPage,
                'totalPages' => $totalPages,
                'total'      => $total,
                'limit'      => $perPage,
            ];

            $flagsMap = $this->taskHealthFlagsService->getFlagsMapByTaskIds(array_column($items, 'taskId'));
        } else {
            $result = TaskHealthApiHelper::getVisits($baseParams + ['page' => $localPage]);
            if ($result['status'] && isset($result['data']['items'])) {
                $items      = $result['data']['items'];
                $pagination = $result['data']['pagination'] ?? [];
            } else {
                $error = !$result['status'] ? ($result['error'] ?? 'API Error') : null;
            }
            $flagsMap = $this->taskHealthFlagsService->getFlagsMapByTaskIds(array_column($items, 'taskId'));
        }

        // Build agencyId => agencyName map for display
        $agencyMap = [];
        $agencyResult = TaskHealthApiHelper::getAgencies();
        if ($agencyResult['status'] && !empty($agencyResult['data'])) {
            foreach ($agencyResult['data'] as $agency) {
                if (isset($agency['taskHealthAgencyId'])) {
                    $agencyMap[$agency['taskHealthAgencyId']] = $agency['agencyName'] ?? '';
                }
            }
        }

        $data['items']      = $items;
        $data['pagination'] = $pagination;
        $data['agencyMap']  = $agencyMap;
        $data['flagsMap']   = $flagsMap;
        $data['error']      = $error;

        return view('task_health_visit.task_health_visit_ajax_list', $data);
    }

    public function visitDetailJson(int $taskId)
    {
        $result = TaskHealthApiHelper::getVisitDetail($taskId);
        $agencyId = TaskHealthApiHelper::detectLocalAgency($result['data']['task']['agencyId']);
        $agSetting = $this->agencyTaskHealthSettingService->getTaskHealthSettingById($agencyId['id']);
        if ($result['status'] && isset($result['data'])) {
            return response()->json(['status' => 1, 'data' => $result['data'],'ag_setting' => $agSetting]);
        }
        return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to fetch visit detail']);
    }

    public function visitDetailJsonWithPoc(int $taskId)
    {
        $result = TaskHealthApiHelper::getVisitDetail($taskId);
        if (!$result['status'] || !isset($result['data'])) {
            return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to fetch visit detail']);
        }

        $data = $result['data'];

        if (!empty($data['planOfCareItems'])) {
            $visitTaskIds = collect($data['planOfCareItems'])
                ->pluck('taskHealthId')
                ->filter()
                ->values()
                ->all();

            $pocMatchedTaskService = new PocMatchedTaskService();
            $agency_id = TaskHealthApiHelper::detectLocalAgency($data['task']['agencyId']);
            $matchedTasks = $pocMatchedTaskService->getMatchedHhaPocTasksByVisitTaskIds($visitTaskIds,$agency_id['id']);

            $data['planOfCareItems'] = array_map(function ($item) use ($matchedTasks) {
                $matched = $matchedTasks->get($item['taskHealthId'] ?? null);
                $item['matched_task_id']   = $matched['task_id']   ?? null;
                $item['matched_task_name'] = $matched['task_name'] ?? null;
                $item['matched_task_code'] = $matched['task_code'] ?? null;
                return $item;
            }, $data['planOfCareItems']);
        }

        return response()->json(['status' => 1, 'data' => $data]);
    }

    public function createVisit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'taskHealthAgencyId'  => 'required|integer',
            'taskType'            => 'required|in:START_OF_CARE,REASSESSMENT,SUPERVISORY',
            'patient_firstName'   => 'required|string',
            'patient_lastName'    => 'required|string',
            'patient_dateOfBirth' => 'required',
            'patient_gender'      => 'required|in:M,F',
            'patient_address'     => 'required|string',
            'patient_language'    => 'required|string',
            'patient_phone'       => 'required|string',
            'startOfCareDate'     => 'required_if:taskType,REASSESSMENT|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
        }

        $payload = [
            'taskHealthAgencyId' => (int) $request->input('taskHealthAgencyId'),
            'taskType'           => $request->input('taskType'),
            'patient'            => [
                'firstName'    => $request->input('patient_firstName'),
                'lastName'     => $request->input('patient_lastName'),
                'dateOfBirth'  => date('Y-m-d',strtotime($request->input('patient_dateOfBirth'))),
                'gender'       => $request->input('patient_gender'),
                'address'      => $request->input('patient_address'),
                'language'     => $request->input('patient_language'),
                'phoneNumbers' => [
                    ['number' => $request->input('patient_phone'), 'isPrimary' => true],
                ],
            ],
        ];

        if ($request->filled('startOfCareDate')) {
            $payload['startOfCareDate'] = date('Y-m-d',strtotime($request->input('startOfCareDate')));
        }
        if ($request->filled('serviceType')) {
            $payload['serviceType'] = $request->input('serviceType');
        }
        if ($request->filled('payerSource')) {
            $payload['payerSource'] = $request->input('payerSource');
        }
        if ($request->filled('frequency')) {
            $payload['frequency'] = $request->input('frequency');
        }
        if ($request->filled('agencyNote')) {
            $payload['agencyNote'] = $request->input('agencyNote');
        }
        if ($request->filled('patient_address2')) {
            $payload['patient']['address2'] = $request->input('patient_address2');
        }
        if ($request->filled('patient_addressInstructions')) {
            $payload['patient']['addressInstructions'] = $request->input('patient_addressInstructions');
        }

        if ($request->filled('assessmentStartDate') && $request->filled('assessmentDueDate')) {
            $payload['schedule'] = [
                'assessmentStartDate' => date('Y-m-d',strtotime($request->input('assessmentStartDate'))),
                'assessmentDueDate'   => date('Y-m-d',strtotime($request->input('assessmentDueDate'))),
            ];
            if ($request->filled('scheduleFreeText')) {
                $payload['schedule']['scheduleFreeText'] = $request->input('scheduleFreeText');
            }
        }

        if ($request->filled('caregiver_name')) {
            $payload['caregiver'] = ['name' => $request->input('caregiver_name')];
            if ($request->filled('caregiver_phone')) {
                $payload['caregiver']['phoneNumber'] = $request->input('caregiver_phone');
            }
        }

        if ($request->filled('cert_startDate') || $request->filled('cert_endDate')) {
            $payload['certificationPeriod'] = array_filter([
                'startDate' => date('Y-m-d',strtotime($request->input('cert_startDate'))),
                'endDate'   => date('Y-m-d',strtotime($request->input('cert_endDate'))),
            ]);
        }
        
        $result = TaskHealthApiHelper::createVisit($payload);

        if ($result['status']) {
            return response()->json(['status' => 1, 'message' => 'Visit created successfully.', 'data' => $result['data'] ?? []]);
        }

        return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to create visit.']);
    }

    public function editVisit(Request $request, int $taskId)
    {
        $validator = Validator::make($request->all(), [
            'instruction' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
        }

        $result = TaskHealthApiHelper::editVisit($taskId, $request->input('instruction'));
        if ($result['status']) {
            return response()->json([
                'status'         => 1,
                'message'        => 'Visit updated successfully.',
                'interpretation' => $result['data']['interpretation'] ?? '',
                'data'           => $result['data'] ?? [],
            ]);
        }

        return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to update visit.']);
    }

    public function approveDocument(Request $request, int $taskId, int $scheduledDocId)
    {
        $result = TaskHealthApiHelper::approveDocument($taskId, $scheduledDocId);
        if ($result['status']) {
            return response()->json(['status' => 1, 'message' => 'Document approved successfully.', 'data' => $result['data'] ?? []]);
        }
        return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to approve document.']);
    }

    public function openDocumentForChanges(Request $request, int $taskId, int $scheduledDocId)
    {
        $validator = Validator::make($request->all(), [
            'rejections'   => 'required|array|min:1',
            'rejections.*' => 'required|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
        }
        $result = TaskHealthApiHelper::openDocumentForChanges($taskId, $scheduledDocId, $request->input('rejections'));
        if ($result['status']) {
            return response()->json(['status' => 1, 'message' => 'Document opened for changes.', 'data' => $result['data'] ?? []]);
        }
        return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to open document for changes.']);
    }

    public function cancelVisit(int $taskId)
    {
        $result = TaskHealthApiHelper::cancelVisit($taskId);

        if ($result['status']) {
            return response()->json(['status' => 1, 'message' => 'Visit cancelled successfully.', 'data' => $result['data'] ?? []]);
        }
        return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to cancel visit.']);
    }

    public function exportCsv(Request $request)
    {
        set_time_limit(0);

        $toArray = fn($val) => !empty($val) ? array_values(array_filter(explode(',', $val))) : null;

        $pocCheck         = !empty($request->input('poc_check'));
        $mdoCheck         = !empty($request->input('mdo_check'));
        $alertCheck       = !empty($request->input('alert_check'));
        $supervisionCheck = !empty($request->input('supervision_check'));
        $assessmentCheck      = !empty($request->input('assessment_check'));
        $kardexCheck          = !empty($request->input('kardex_check'));
        $patientPackageCheck  = !empty($request->input('patient_package_doc_check'));
        $flagFilterActive = $pocCheck || $mdoCheck || $alertCheck || $supervisionCheck || $assessmentCheck || $kardexCheck || $patientPackageCheck;

        $baseParams = array_filter([
            'sortBy'           => $request->input('sortBy', 'scheduledDateTime'),
            'fromDate'         => date('Y-m-d',strtotime($request->input('fromDate'))),
            'toDate'           => date('Y-m-d',strtotime($request->input('toDate'))),
            'agencyIds'        => $toArray($request->input('agencyIds')),
            'status'           => $toArray($request->input('status')),
            'reviewStatus'     => $toArray($request->input('reviewStatus')),
            'hasCriticalAlert' => $request->input('hasCriticalAlert'),
            'search'           => $request->input('search'),
            'limit'            => 500,
        ], fn($v) => $v !== null && $v !== '' && $v !== []);

        // Build agency map once
        $agencyMap = [];
        $agencyResult = TaskHealthApiHelper::getAgencies();
        if ($agencyResult['status'] && !empty($agencyResult['data'])) {
            foreach ($agencyResult['data'] as $agency) {
                if (isset($agency['taskHealthAgencyId'])) {
                    $agencyMap[$agency['taskHealthAgencyId']] = $agency['agencyName'] ?? '';
                }
            }
        }

        $fn = fn($u) => $u ? ($u->first_name . ' ' . substr($u->last_name, 0, 1) . '.') : '';
        $fd = fn($d) => $d ? date('m/d/Y h:i A', strtotime($d)) : '';

        $handle  = fopen('php://temp', 'r+');
        $columns = [
            '#', 'Task ID', 'Patient ID', 'Agency Name', 'Patient Name', 'Task Type', 'Status', 'Review Status', 'Critical Alert',
            'POC', 'POC Checked By', 'POC Date',
            'MDO', 'MDO Checked By', 'MDO Date',
            'Alert', 'Alert Checked By', 'Alert Date',
            'Supervision', 'Supervision Checked By', 'Supervision Date',
            'Assessment', 'Assessment Checked By', 'Assessment Date',
            'Kardex', 'Kardex Checked By', 'Kardex Date',
            'Patient Package Doc', 'Patient Package Doc Checked By', 'Patient Package Doc Date',
            'Scheduled Date', 'Created Date',
        ];
        fputcsv($handle, $columns);

        $cnt        = 1;
        $page       = 1;
        $totalPages = 1;
        $maxPages   = 20;

        // When flag filters are active, pre-fetch all pages and filter locally
        if ($flagFilterActive) {
            $allItems = [];
            do {
                $result = TaskHealthApiHelper::getVisits($baseParams + ['page' => $page]);
                if (!$result['status']) break;
                $pageItems  = $result['data']['items'] ?? [];
                $totalPages = $result['data']['pagination']['totalPages'] ?? 1;
                $batchFlags = $this->taskHealthFlagsService->getFlagsMapByTaskIds(array_column($pageItems, 'taskId'));
                foreach ($pageItems as $item) {
                    $flag = $batchFlags[(string)($item['taskId'] ?? '')] ?? null;
                    if ($pocCheck         && !($flag && $flag->poc_check))         continue;
                    if ($mdoCheck         && !($flag && $flag->mdo_check))         continue;
                    if ($alertCheck       && !($flag && $flag->alert_check))       continue;
                    if ($supervisionCheck && !($flag && $flag->supervision_check))  continue;
                    if ($assessmentCheck     && !($flag && $flag->assessment_check))          continue;
                    if ($kardexCheck         && !($flag && $flag->kardex_check))              continue;
                    if ($patientPackageCheck && !($flag && $flag->patient_package_doc_check)) continue;
                    $allItems[] = $item;
                }
                $page++;
            } while ($page <= $totalPages && $page <= $maxPages);

            $flagsMap = $this->taskHealthFlagsService->getFlagsMapByTaskIds(array_column($allItems, 'taskId'));

            foreach ($allItems as $row) {
                $ca         = $row['criticalAlert'] ?? null;
                $alertText  = is_null($ca) ? 'Not Analyzed' : (($ca['alert'] ?? false) ? 'Critical' : 'Clear');
                $agencyId   = $row['agencyId'] ?? '';
                $agencyName = $agencyMap[$agencyId] ?? ($row['agencyName'] ?? $agencyId);
                $flag       = $flagsMap[(string)($row['taskId'] ?? '')] ?? null;

                fputcsv($handle, [
                    $cnt,
                    $row['taskId']   ?? '',
                    $row['patientId'] ?? '',
                    $agencyName,
                    trim(($row['patientFirstName'] ?? '') . ' ' . ($row['patientLastName'] ?? '')),
                    $row['taskType']     ?? '',
                    $row['status']       ?? '',
                    $row['reviewStatus'] ?? '',
                    $alertText,
                    // POC
                    $flag ? ($flag->poc_check        ? 'Yes' : 'No') : 'No',
                    $flag ? $fn($flag->pocCheckedByUser)              : '',
                    $flag ? $fd($flag->poc_check_date)                : '',
                    // MDO
                    $flag ? ($flag->mdo_check        ? 'Yes' : 'No') : 'No',
                    $flag ? $fn($flag->mdoCheckedByUser)              : '',
                    $flag ? $fd($flag->mdo_check_date)                : '',
                    // Alert
                    $flag ? ($flag->alert_check      ? 'Yes' : 'No') : 'No',
                    $flag ? $fn($flag->alertCheckedByUser)            : '',
                    $flag ? $fd($flag->alert_check_date)              : '',
                    // Supervision
                    $flag ? ($flag->supervision_check ? 'Yes' : 'No') : 'No',
                    $flag ? $fn($flag->supervisionCheckedByUser)       : '',
                    $flag ? $fd($flag->supervision_check_date)         : '',
                    // Assessment
                    $flag ? ($flag->assessment_check  ? 'Yes' : 'No') : 'No',
                    $flag ? $fn($flag->assessmentCheckedByUser)        : '',
                    $flag ? $fd($flag->assessment_check_date)          : '',
                    // Kardex
                    $flag ? ($flag->kardex_check              ? 'Yes' : 'No') : 'No',
                    $flag ? $fn($flag->kardexCheckedByUser)                    : '',
                    $flag ? $fd($flag->kardex_check_date)                      : '',
                    // Patient Package Doc
                    $flag ? ($flag->patient_package_doc_check ? 'Yes' : 'No') : 'No',
                    $flag ? $fn($flag->patientPackageDocCheckedByUser)         : '',
                    $flag ? $fd($flag->patient_package_doc_check_date)         : '',
                    isset($row['scheduledDateTime']) ? date('m/d/Y h:i A', strtotime($row['scheduledDateTime'])) : '',
                    isset($row['createdAt'])         ? date('m/d/Y h:i A', strtotime($row['createdAt']))         : '',
                ]);
                $cnt++;
            }
        } else {
            do {
                $params         = $baseParams;
                $params['page'] = $page;

                $result = TaskHealthApiHelper::getVisits($params);
                $items  = [];

                if ($result['status'] && isset($result['data']['items'])) {
                    $items      = $result['data']['items'];
                    $totalPages = $result['data']['pagination']['totalPages'] ?? 1;
                }

                $flagsMap = $this->taskHealthFlagsService->getFlagsMapByTaskIds(array_column($items, 'taskId'));

                foreach ($items as $row) {
                    $ca         = $row['criticalAlert'] ?? null;
                    $alertText  = is_null($ca) ? 'Not Analyzed' : (($ca['alert'] ?? false) ? 'Critical' : 'Clear');
                    $agencyId   = $row['agencyId'] ?? '';
                    $agencyName = $agencyMap[$agencyId] ?? ($row['agencyName'] ?? $agencyId);
                    $flag       = $flagsMap[(string)($row['taskId'] ?? '')] ?? null;

                    fputcsv($handle, [
                        $cnt,
                        $row['taskId']   ?? '',
                        $row['patientId'] ?? '',
                        $agencyName,
                        trim(($row['patientFirstName'] ?? '') . ' ' . ($row['patientLastName'] ?? '')),
                        $row['taskType']     ?? '',
                        $row['status']       ?? '',
                        $row['reviewStatus'] ?? '',
                        $alertText,
                        $flag ? ($flag->poc_check        ? 'Yes' : 'No') : 'No',
                        $flag ? $fn($flag->pocCheckedByUser)              : '',
                        $flag ? $fd($flag->poc_check_date)                : '',
                        $flag ? ($flag->mdo_check        ? 'Yes' : 'No') : 'No',
                        $flag ? $fn($flag->mdoCheckedByUser)              : '',
                        $flag ? $fd($flag->mdo_check_date)                : '',
                        $flag ? ($flag->alert_check      ? 'Yes' : 'No') : 'No',
                        $flag ? $fn($flag->alertCheckedByUser)            : '',
                        $flag ? $fd($flag->alert_check_date)              : '',
                        $flag ? ($flag->supervision_check ? 'Yes' : 'No') : 'No',
                        $flag ? $fn($flag->supervisionCheckedByUser)       : '',
                        $flag ? $fd($flag->supervision_check_date)         : '',
                        // Assessment
                        $flag ? ($flag->assessment_check  ? 'Yes' : 'No') : 'No',
                        $flag ? $fn($flag->assessmentCheckedByUser)        : '',
                        $flag ? $fd($flag->assessment_check_date)          : '',
                        // Kardex
                        $flag ? ($flag->kardex_check      ? 'Yes' : 'No') : 'No',
                        $flag ? $fn($flag->kardexCheckedByUser)            : '',
                        $flag ? $fd($flag->kardex_check_date)              : '',
                        isset($row['scheduledDateTime']) ? date('m/d/Y h:i A', strtotime($row['scheduledDateTime'])) : '',
                        isset($row['createdAt'])         ? date('m/d/Y h:i A', strtotime($row['createdAt']))         : '',
                    ]);
                    $cnt++;
                }

                $page++;
            } while ($page <= $totalPages && !empty($items));
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $filename = 'task-health-visits-' . date('m-d-Y');

        return response($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    public function checkExistingMasterRecord(int $taskId)
    {
        $result = TaskHealthApiHelper::getVisitDetailCached($taskId);
        if (!$result['status'] || !isset($result['data'])) {
            return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to fetch visit detail']);
        }

        $raw     = $result['data'];
        $task    = (isset($raw['task'])    && is_array($raw['task']))    ? $raw['task']    : $raw;
        $patient = (isset($raw['patient']) && is_array($raw['patient'])) ? $raw['patient'] : [];
        $thAgencyId = $task['agencyId'] ?? null;

        $thPatientId = $patient['id'] ?? null;
        $thTaskId    = $task['id'] ?? $task['taskId'] ?? null;

        if (!$thPatientId && !$thTaskId) {
            return response()->json(['status' => 0, 'message' => 'Patient ID not available in visit data.']);
        }

        // Parse patient fields once (shared by master search + local patient pre-check)
        $firstName = $patient['firstName'] ?? '';
        $lastName  = $patient['lastName']  ?? '';
        $dob       = !empty($patient['dateOfBirth']) ? date('Y-m-d', strtotime($patient['dateOfBirth'])) : null;
        $phones    = $patient['phoneNumbers'] ?? [];
        $primary   = collect($phones)->firstWhere('isPrimary', true) ?? ($phones[0] ?? null);
        $rawPhone  = preg_replace('/[^0-9]/', '', $primary['number'] ?? '');
        if (strlen($rawPhone) == 11 && substr($rawPhone, 0, 1) == '1') {
            $rawPhone = substr($rawPhone, 1);
        }
        $gender = !empty($patient['gender']) ? $this->convertGender($patient['gender']) : null;

        // 1. Exact match by external patient ID
        $record = null;
        if ($thPatientId) {
            $record = TaskHealthMaster::where('deleted_flag', 'N')
                ->where('task_health_patient_id', (string) $thPatientId)
                ->orderBy('id', 'desc')
                ->first();
        }

        // 2. Fallback: match by name + DOB
        if (!$record && $firstName && $lastName && $dob) {
            $q = TaskHealthMaster::where('deleted_flag', 'N')
                ->where('dob', $dob)
                ->whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                ->whereRaw('LOWER(last_name)  = ?', [strtolower($lastName)]);
            if ($rawPhone) {
                $q->where(function ($sub) use ($rawPhone) {
                    $sub->where('phone',  'LIKE', "%{$rawPhone}%")
                        ->orWhere('mobile', 'LIKE', "%{$rawPhone}%");
                });
            }
            $record = $q->orderBy('id', 'desc')->first();
        }

        // Detect local agency from visit data
        $suggestedAgency = TaskHealthApiHelper::detectLocalAgency($thAgencyId);

        // Pre-check: does a local patient already exist matching this TH patient?
        $localMatch = null;
        if ($suggestedAgency && !empty($suggestedAgency['id'])) {
            $localMatch = $this->patientService->checkForExistingTaskHealthDataApi([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'dob'        => $dob ?? '',
                'mobile'     => $rawPhone,
                'gender'     => $gender,
                'type'       => 'Patient',
            ], $suggestedAgency['id']);
        }

        if ($record) {
            // Check if a local patient is already linked to this master record
            $linkedPatient = $this->patientV2Service->getTaskHealthData($record->id);

            $patientUrl = $linkedPatient
                ? url('patient/view/' . $linkedPatient->id)
                : ($record->patient_id ? url('patient/view/' . $record->patient_id) : null);

            // Buttons (Manage Flags, Send POC) are only relevant when the linked
            // patient has an HHA patient linked (link_hha_patient is not empty)
            $hasHhaLink = $linkedPatient && !empty($linkedPatient->link_hha_patient);

            return response()->json([
                'status' => 1,
                'found'  => true,
                'record' => [
                    'id'          => $record->id,
                    'name'        => trim($record->first_name . ' ' . $record->last_name),
                    'dob'         => $record->dob ? date('m/d/Y', strtotime($record->dob)) : null,
                    'phone'       => $record->phone ?: $record->mobile,
                    'status'      => $record->status,
                    'type'        => $record->type,
                    'patient_url' => $patientUrl,
                    'master_url'  => url('task-health/' . $record->id . '/detail'),
                ],
                'patient_linked'      => (bool) $linkedPatient,
                'has_hha_link'        => $hasHhaLink,
                'patient'             => $linkedPatient ? [
                    'id'          => $linkedPatient->id,
                    'name'        => trim($linkedPatient->first_name . ' ' . $linkedPatient->last_name),
                    'patient_url' => url('patient/view/' . $linkedPatient->id),
                ] : null,
                'suggested_agency'    => $suggestedAgency,
                'local_patient_match' => $localMatch ? [
                    'id'          => $localMatch->id,
                    'name'        => trim($localMatch->first_name . ' ' . $localMatch->last_name),
                    'patient_url' => url('patient/view/' . $localMatch->id),
                ] : null,
            ]);
        }

        return response()->json([
            'status'              => 1,
            'found'               => false,
            'has_hha_link'        => false,
            'suggested_agency'    => $suggestedAgency,
            'local_patient_match' => $localMatch ? [
                'id'          => $localMatch->id,
                'name'        => trim($localMatch->first_name . ' ' . $localMatch->last_name),
                'patient_url' => url('patient/view/' . $localMatch->id),
            ] : null,
        ]);
    }

    public function createMasterFromVisit(Request $request, int $taskId)
    {
        $result = TaskHealthApiHelper::getVisitDetail($taskId);
        if (!$result['status'] || !isset($result['data'])) {
            return response()->json(['status' => 0, 'message' => $result['error'] ?? 'Failed to fetch visit detail']);
        }

        $raw     = $result['data'];
        $task    = (isset($raw['task'])    && is_array($raw['task']))    ? $raw['task']    : $raw;
        $patient = (isset($raw['patient']) && is_array($raw['patient'])) ? $raw['patient'] : [];
        $thAgencyId = $task['agencyId']??null;

        // Auto-detect agency server-side if not provided by client
        $detectedAgency = TaskHealthApiHelper::detectLocalAgency($thAgencyId);
        if ($detectedAgency) {
            $agencyId = $detectedAgency['id'];
        }

        $thPatientId = $patient['id'] ?? null;
        $thTaskId    = $task['id'] ?? $task['taskId'] ?? null;

        // ── Parse common patient fields ──────────────────────────────────────
        $phones   = $patient['phoneNumbers'] ?? [];
        $primary  = collect($phones)->firstWhere('isPrimary', true) ?? ($phones[0] ?? null);
        $rawPhone = preg_replace('/[^0-9]/', '', $primary['number'] ?? '');
        if (strlen($rawPhone) === 11 && $rawPhone[0] === '1') {
            $rawPhone = substr($rawPhone, 1);
        }
        $phoneRaw = $primary['number'] ?? null;
        $notPrimary  = collect($phones)->firstWhere('isPrimary', false) ?? ($phones[0] ?? null);
        $rawMobile = preg_replace('/[^0-9]/', '', $notPrimary['number'] ?? '');
        if (strlen($rawMobile) === 11 && $rawMobile[0] === '1') {
            $rawMobile = substr($rawMobile, 1);
        }
        $mobileRaw = $notPrimary['number'] ?? null;
        $langs    = array_values(array_filter($patient['languages'] ?? []));
        $dob      = !empty($patient['dateOfBirth']) ? date('Y-m-d', strtotime($patient['dateOfBirth'])) : null;
        $gender   = !empty($patient['gender']) ? $this->convertGender($patient['gender']) : null;

        // ── Step 1: Find or create task_health_master ────────────────────────
        $master = null;
        if ($thPatientId) {
            $master = TaskHealthMaster::where('deleted_flag', 'N')
                ->where('task_health_patient_id', (string) $thPatientId)
                ->first();
        }

        // ── Input fields from form ───────────────────────────────────────────
        // service_ids sent as comma-joined string from JS to avoid PHP dropping repeated keys
        $serviceIds   = array_filter(array_map('intval', explode(',', $request->input('service_ids', ''))));
        $discipline   = trim((string) $request->input('discipline', ''));
        $followupDate = $request->input('followup_date') ? date('Y-m-d', strtotime($request->input('followup_date'))) : null;
        $dueDate      = $request->input('due_date')      ? date('Y-m-d', strtotime($request->input('due_date')))      : null;

        $validServiceIds = [];
        if (!empty($serviceIds)) {
            foreach ($serviceIds as $sid) {
                $svc = Master::where('id', $sid)
                    ->where('master_type_fk', 11)
                    ->where('del_flag', 'N')
                    ->whereRaw('LOWER(types) = ?', ['patient'])
                    ->first();
                if ($svc) {
                    $validServiceIds[] = (string) $sid;
                }
            }
        }
        $masterCreated = false;
        if (!$master) {
            $masterData = array_filter([
                'first_name'             => $patient['firstName'] ?? null,
                'middle_name'            =>$patient['middleName']??'',
                'last_name'              => $patient['lastName']  ?? null,
                'full_name'              =>$patient['firstName'].' '.$patient['lastName'],
                'dob'                    => $dob,
                'gender'                 => $gender,
                'fu_date' =>$followupDate,
				'due_date' => $dueDate,
                'service_id' => implode(',',$serviceIds),
                'diciplin' =>$discipline,
                'phone'                  => $phoneRaw,
                'mobile'                 => $mobileRaw,
                'language'               => Common::getOrCreateLanguageId($langs[0] ?? null),
                'address1'               => $patient['address']  ?? null,
                'address2'               => $patient['address2'] ?? null,
                'task_health_patient_id' => (string) ($thPatientId ?? ''),
                'task_id'                => (string) ($thTaskId   ?? ''),
                'type'                   => 'Patient',
                'agency_id'              => $agencyId,
                'referral_type'          => 'Task Health',
                'third_party_callback_url' => 'https://api.taskshealth.com/ny-best/callback',
            ], fn($v) => $v !== null && $v !== '');

            $masterId = $this->taskHealthMasterService->save($masterData);
            $master   = TaskHealthMaster::find($masterId);
            $masterCreated = true;

            // Activity log
            $ipaddress = Utility::getIP();
            LogsService::save([
                'ip' => $ipaddress,
                'type'         => 'Added Task health master from Visit',
                'link'         => url('/task-health/visit'),
                'module'       => 'Task health Master',
                'object_id'    => $masterId,
                'message'      => 'Task health master linked from Task Health visit #' . $taskId,
                'new_response' => serialize(['master' => $master]),
            ]);
        }

        // Determine status from service IDs (mirrors saveTaskHealthAppointment)
        $statusServiceRequest = Utility::getStatusFromServiceId($serviceIds);

        // ── Step 2: Find or create local Patient then link ───────────────────
        $searchData = [
            'first_name' => $patient['firstName'] ?? '',
            'last_name'  => $patient['lastName']  ?? '',
            'dob'        => $dob ?? '',
            'mobile'     => $rawPhone,
            'gender'     => $gender,
            'type'       => 'Patient',
        ];

        $localPatient   = $this->patientService->checkForExistingTaskHealthDataApi($searchData, $agencyId);
        $patientCreated = false;
        $flag           = 0; // 0 = existing patient, 1 = new patient (matches reference)
        $ipaddress = Utility::getIP();
        if (!$localPatient) {
            // No matching patient — create new one with full data (mirrors createNewPatient)
            $patientData = [
                'first_name'       => $patient['firstName']  ?? '',
                'middle_name'      => $patient['middleName'] ?? '',
                'last_name'        => $patient['lastName']   ?? '',
                'full_name'        => trim(($patient['firstName'] ?? '') . ' ' . ($patient['lastName'] ?? '')),
                'dob'              => $dob,
                'gender'           => $gender ?? '',
                'phone'            => $rawPhone,
                'mobile'           => $rawMobile,
                'language'         => Common::getOrCreateLanguageId($langs[0] ?? ''),
                'address1'         => $patient['address']  ?? '',
                'address2'         => $patient['address2'] ?? '',
                'state'            => $patient['state']    ?? '',
                'city'             => $patient['city']     ?? '',
                'zip_code'         => $patient['zipCode']  ?? '',
                'county'           => $patient['county']   ?? '',
                'agency_id'        => $agencyId,
                'type'             => 'Patient',
                'status'           => $statusServiceRequest,
                'diciplin'         => $discipline,
                'service_id'       => implode(',', $serviceIds),
                'fu_date'          => $followupDate,
                'due_date'         => $dueDate,
                'deleted_flag'     => 'N',
                'created_date'     => date('Y-m-d H:i:s'),
                'created_by'       => auth()->id(),
                'task_health_link' => $master->id,
                'referral_type'    => 'Task Health',
            ];
            $newPatient   = new Patient($patientData);
            $newPatient->save();
            $localPatient   = $newPatient;
            $patientCreated = true;
            $flag           = 1;

            // Activity log
            LogsService::save([
                'ip' => $ipaddress,
                'type'         => 'New Link Patient from Visit',
                'link'         => url('/task-health/visit'),
                'module'       => 'Patient Appointment',
                'object_id'    => $localPatient->id,
                'message'      => 'Patient new created from Task Health visit #' . $taskId,
                'new_response' => serialize(['task_id' => $taskId, 'master_id' => $master->id, 'patient_id' => $localPatient->id]),
            ]);
        } else {
            // Existing patient — link to master
            $this->patientService->linkTaskHealth($localPatient->id, $master->id);
            $localPatient->task_health_link = $master->id;
            // Activity log
            LogsService::save([
                'ip' => $ipaddress,
                'type'         => 'Link Patient from Visit',
                'link'         => url('/task-health/visit'),
                'module'       => 'Patient Appointment',
                'object_id'    => $localPatient->id,
                'message'      => 'Patient linked from Task Health visit #' . $taskId,
                'new_response' => serialize(['task_id' => $taskId, 'master_id' => $master->id, 'patient_id' => $localPatient->id]),
            ]);
        }

        // ── Step 3: Update task_health_master.patient_id ────────────────────
        if (empty($master->patient_id)) {
            $this->taskHealthMasterService->linkPatient($master->id, $localPatient->id);
            $master->patient_id = $localPatient->id;
        }

        // ── Step 4: Service requests (mirrors saveTaskHealthAppointment) ─────
        if (!empty($validServiceIds)) {
            // flag=0 (existing patient): if they have no prior service requests,
            // create a legacy one from their existing data first (same as reference)
            if ($flag === 0) {
                $existingRequests = $this->patientServicesRequestService->getServiceCountPatientId($localPatient->id);
                if (count($existingRequests) === 0) {
                    $existingServices = array_filter(explode(',', $localPatient->service_id ?? ''));
                    if (!empty($existingServices)) {
                        $legacyPsrId = $this->patientServicesRequestService->save([
                            'patient_id'     => $localPatient->id,
                            'follow_up_date' => $localPatient->fu_date,
                            'due_date'       => $localPatient->due_date,
                            'status'         => $localPatient->status,
                            'created_at'     => $localPatient->created_date,
                            'created_by'     => auth()->id(),
                            'completed_date' => $localPatient->completed_date ?? null,
                            'completed_by'   => $localPatient->completed_by   ?? null,
                        ]);
                        foreach ($existingServices as $esid) {
                            PatientWiseServiceRequest::create([
                                'patient_id'                 => $localPatient->id,
                                'service_id'                 => $esid,
                                'patient_service_request_id' => $legacyPsrId,
                                'created_date'               => $localPatient->created_date,
                                'created_by'                 => auth()->id(),
                            ]);
                        }
                    }
                }
            }

            // Create new PatientServiceRequest for all patients
            $psr = new PatientServiceRequest([
                'patient_id'     => $localPatient->id,
                'created_at'     => date('Y-m-d H:i:s'),
                'created_by'     => auth()->id(),
                'follow_up_date' => $followupDate,
                'due_date'       => $dueDate,
                'status'         => $statusServiceRequest,
            ]);
            $psr->save();
            $psrId = $psr->id;

            foreach ($validServiceIds as $sid) {
                PatientWiseServiceRequest::create([
                    'patient_id'                 => $localPatient->id,
                    'service_id'                 => $sid,
                    'patient_service_request_id' => $psrId,
                    'created_date'               => date('Y-m-d H:i:s'),
                    'created_by'                 => auth()->id(),
                ]);
            }
            $this->taskHealthMasterService->update(['requested_service_id' => $psrId],['id' => $master->id]);

            // flag=0: update existing patient status/dates/services
            if ($flag === 0) {
                $updateArray = array(
                    'status'     => $statusServiceRequest,
                    'fu_date'    => $followupDate,
                    'due_date'   => $dueDate,
                    'service_id' => implode(',', $validServiceIds),
                    'diciplin'   => $discipline ?: $localPatient->diciplin
                );
                $this->patientService->update($updateArray,['id'=> $localPatient->id]);
            }

            // Save resolution log (mirrors reference)
            try {
                Utility::saveResolutionLogForms($statusServiceRequest, $psrId, $localPatient->id);
            } catch (\Throwable $th) {
                // non-fatal
            }
            
        }
        
        try {
            // Find "NEW - HOME HEALTH CERTIFICATION AND PLAN OF CARE (CMS-485)" document
            $documents   = $task['documents'] ?? [];
            $fileContent = null;
            $fileName    = null;
            foreach ($documents as $doc) {
                $title = $doc['type']['title'] ?? '';
                if (stripos($title, 'CMS-485') !== false || stripos($title, 'HOME HEALTH CERTIFICATION') !== false) {
                    $docUrl = $doc['url'] ?? null;
                    if ($docUrl) {
                        $downloaded = file_get_contents($docUrl);
                        if ($downloaded !== false) {
                            $fileContent = $downloaded;
                            $fileName    = 'cms485_' . $master->task_id . '_' . time() . '.pdf';
                        }
                    }
                    break;
                }
            }
            if ($fileContent !== null && $fileName !== null) {
                $ext      = pathinfo($fileName, PATHINFO_EXTENSION);
                $fileType = 'application/pdf';
                $fileSize = strlen($fileContent);
                $name     = uniqid() . time() . '_' . $fileName;

                if (env('FILE_UPLOAD_PERMISSION') == 'development') {
                    Storage::disk('public')->put('patientdocument/' . $name, $fileContent);
                    Storage::disk('public')->put('patientWriteDocument/' . $name, $fileContent);
                } else {
                    Storage::disk('s3')->put('patientdocument/' . $name, $fileContent);
                    Storage::disk('s3')->put('patientWriteDocument/' . $name, $fileContent);
                }

                $docData = [
                    'document_name'          => 'Task health - '.$master->task_id,
                    'attachment'             => $name,
                    'patient_id'             => $localPatient->id,
                    'request_service_id'     => $psrId,
                    'is_checked'             => 0,
                    'internal_use'           => 1,
                    'assign_document_review' => null,
                    'created_date'           => date('Y-m-d H:i:s'),
                    'created_by'             => auth()->user()->id,
                    'document_review_status' => 'Approved',
                    'extension'              => $ext,
                    'size_in_bytes'          => $fileSize,
                    'pdf_type'               => $fileType,
                    'call_back_url'          => 'https://api.taskshealth.com/ny-best/callback',
                    'flag'                   => 1,
                ];

                $docInsertId = $this->documentPatientService->save($docData);

                if (count($serviceIds) >0) {
                    foreach ($serviceIds as $serviceId) {
                        $this->documentUploadService->save([
                            'patient_id'  => $localPatient->id,
                            'document_id' => $docInsertId,
                            'service_id'  => $serviceId,
                        ]);
                    }
                }
                $insertLog = [
                    'type' => 'Add Document From Task Health Appointment',
                    'link' =>  url('/api/lead/save-task-health-appointment'),
                    'module' => 'Patient Appointment',
                    'object_id' => $localPatient->id,
                    'ip' => $ipaddress,
                    'message' =>'Task Health Appointment has Add Document From Appointment',
                    'new_response' => serialize($docData),
                ];
                if (isset($getExistingRecord) && $getExistingRecord != "") {
                    $insertLog['old_response'] = serialize($getExistingRecord->toArray());
                }
                LogsService::save($insertLog);

                $this->sendTaskHealthDocumentService->save(array_merge($docData, [
                    'document_id'   => $docInsertId,
                    'document_name' => 'Task health - '.$masterData['task_id'],
                ]));
            }
        }catch(Exception $e){
            // non - fatal
        }
        $message = match(true) {
            $masterCreated && $patientCreated => 'Master record created and new patient linked.',
            $masterCreated                    => 'Master record created and existing patient linked.',
            $patientCreated                   => 'New patient created and linked to existing master record.',
            default                           => 'Existing patient linked to master record.',
        };

        return response()->json([
            'status'          => 1,
            'message'         => $message,
            'master_created'  => $masterCreated,
            'patient_created' => $patientCreated,
            'record' => [
                'id'          => $master->id,
                'name'        => trim($master->first_name . ' ' . $master->last_name),
                'dob'         => $master->dob ? date('m/d/Y', strtotime($master->dob)) : null,
                'phone'       => $master->phone ?: $master->mobile,
                'status'      => $master->status,
                'type'        => $master->type,
                'patient_url' => url('patient/view/' . $localPatient->id),
            ],
            'patient_linked' => true,
            'patient' => [
                'id'          => $localPatient->id,
                'name'        => trim($localPatient->first_name . ' ' . $localPatient->last_name),
                'patient_url' => url('patient/view/' . $localPatient->id),
            ],
        ]);
    }

    /**
     * Try to find a matching local TH-enabled agency from visit data.
     * Returns ['id' => int, 'name' => string] or null.
     */
    private function convertGender(string $gender): string
    {
        $g = strtolower(trim($gender));
        if ($g === 'male'   || $g === 'm') return 'male';
        if ($g === 'female' || $g === 'f') return 'female';
        if ($g === 'other'  || $g === 'o') return 'other';
        return ucfirst($g);
    }

    public function visitDetail(int $taskId)
    {
        $auth = auth()->user();
        if (!$auth) {
            return redirect('login');
        }
        $result = TaskHealthApiHelper::getVisitDetail($taskId);
        $data['menu']   = '';
        $data['user']   = $auth;
        $raw = ($result['status'] && isset($result['data'])) ? $result['data'] : null;
        $data['detail'] = $raw;
        $data['taskId'] = $taskId;
        $data['error']  = (!$result['status']) ? ($result['error'] ?? 'Failed to fetch visit detail') : null;
        return view('task_health_visit.task_health_visit_detail', $data);
    }
}