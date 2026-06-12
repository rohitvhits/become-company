<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskHealthMasterService;
use App\Services\DocumentPatientService;
use App\Services\LogsService;
use App\Services\SendTaskHealthDocumentService;
use Illuminate\Support\Facades\Cache;
use App\Agency;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ThirdPartyWebHookHelper;
use App\Services\SendTaskHealthDocumentLogService;
use App\Services\PatientService;
use App\Helpers\Utility;
use App\Helpers\Common;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Model\Patient;
use App\Model\TaskHealthMaster;
use App\Services\TaskHealthFlagsService;
use App\Services\TaskHealthCriticalAlertService;
use App\Helpers\TaskHealthApiHelper;
use App\Master;
use App\Services\AgencyTaskHealthService;
use App\Services\DocumentUploadService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Services\HHAPOCTaskService;
use App\Services\PocMatchedTaskService;
use App\Services\AgencyService;
use App\Services\VisitTaskHealthService;
use App\Services\MasterService;
use App\Services\AgencyTaskHealthSettingService;
use App\Helpers\HHAPatientHelper;
use App\Services\TaskHealthCronLogService;

class TaskHealthMasterController extends Controller
{

	protected $taskHealthMasterService = "";
	protected $documentPatientService = "";
	protected $sendTaskHealthDocumentService = "";
	protected $sendTaskHealthDocumentLogService = "";
	protected $patientService = "";
	protected $taskHealthFlagsService = "";
	protected $taskHealthCriticalAlertService = "";
	protected $documentUploadService = "";
	protected $patientServicesRequest = "";
	protected $patientWiseServiceRequests = "";
	protected $hhaPOCTaskService = "";
	protected $pocMatchedTaskService = "";
	protected $visitTaskHealthService = "";
	protected $agencyService = "";
	protected $masterService = "";
	protected $agencyTaskHealthSettingService = "";
	protected $taskHealthCronLogService = "";

	public function __construct(
		TaskHealthMasterService $taskHealthMasterService,
		DocumentPatientService $documentPatientService,
		SendTaskHealthDocumentService $sendTaskHealthDocumentService,
		SendTaskHealthDocumentLogService $sendTaskHealthDocumentLogService,
		PatientService $patientService,
		TaskHealthFlagsService $taskHealthFlagsService,
		TaskHealthCriticalAlertService $taskHealthCriticalAlertService,
		DocumentUploadService $documentUploadService,
		PatientServicesRequest $patientServicesRequest,
		PatientWiseServicesRequests $patientWiseServiceRequests,
		HHAPOCTaskService $hhaPOCTaskService,
		PocMatchedTaskService $pocMatchedTaskService,
		VisitTaskHealthService $visitTaskHealthService,
		AgencyService $agencyService,
		MasterService $masterService,
		AgencyTaskHealthSettingService $agencyTaskHealthSettingService,
		TaskHealthCronLogService $taskHealthCronLogService
	) {
		$this->middleware('auth',['except' => ['criticalAlert']]);
		$this->middleware('permission:task-health-list', ['only' => ['index', 'ajaxList']]);
		$this->middleware('permission:task-health-export', ['only' => ['exportCsv']]);
		$this->middleware('permission:agency-task-health-setting', ['only' => ['agencySettingsIndex','agencySettingsAjaxList','agencySettingsToggleUpdate']]);

		$this->taskHealthMasterService           = $taskHealthMasterService;
		$this->documentPatientService            = $documentPatientService;
		$this->sendTaskHealthDocumentService     = $sendTaskHealthDocumentService;
		$this->sendTaskHealthDocumentLogService  = $sendTaskHealthDocumentLogService;
		$this->patientService                    = $patientService;
		$this->taskHealthFlagsService            = $taskHealthFlagsService;
		$this->taskHealthCriticalAlertService    = $taskHealthCriticalAlertService;
		$this->documentUploadService             = $documentUploadService;
		$this->patientServicesRequest            = $patientServicesRequest;
		$this->patientWiseServiceRequests        = $patientWiseServiceRequests;
		$this->hhaPOCTaskService                 = $hhaPOCTaskService;
		$this->pocMatchedTaskService             = $pocMatchedTaskService;
		$this->visitTaskHealthService            = $visitTaskHealthService;
		$this->agencyService            = $agencyService;
		$this->masterService            = $masterService;
		$this->agencyTaskHealthSettingService            = $agencyTaskHealthSettingService;
		$this->taskHealthCronLogService                  = $taskHealthCronLogService;
	}

	public function index()
	{
		$data['menu'] = "";
		$data['user'] = auth()->user();
		$data['agencyList'] = Cache::get('th_patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
        $masters = Cache::get('task_health_master', function () {
				return Master::getAllDataByMasterTypeFk(array(17, 26));
		}, 10 * 60);
        $data['localAgencies'] = Cache::get('th_agency', function () {
            return AgencyTaskHealthService::getAllAgencyList();
        }, 10 * 60);
        $data['patientServices'] = collect($masters)->filter(function ($item) {
                                    return $item->master_type_fk == 11
                                        && $item->types == 'Patient'
                                        && $item->is_disable == 1;
                                })->values();
        $data['disciplineOptions'] = collect($masters)->filter(function ($item) {
                                    return $item->master_type_fk == 26;
                                })->values();
		return view('task_health_master/task_health_master_list', $data);
	}

	public function ajaxList(Request $request)
	{
		$data['query'] = $this->taskHealthMasterService->dataList($request->all());
		return view("task_health_master/task_health_master_ajax_list", $data);
	}

    public function taskHealthMasterById(Request $request)
	{
		$response = $this->taskHealthMasterService->getDataById($request->input('id'));
		return response()->json(['status' => true, 'msg' => 'Data get successfully', 'data' => $response]);
	}

    public function getTaskHealthService(Request $request){
        $response = $this->taskHealthMasterService->getTPUrlByAgencyAndPortal($request->id, $request->agency_id);
        $finalResponse = [];

        if (!empty($response[0])) {
            foreach ($response as $tpd) {
                $temp = [];
                $temp['id'] = $tpd->id;
                $temp['task_health_patient_id'] = $tpd->task_health_patient_id;
                $temp['task_id'] = $tpd->task_id;
                $temp['created_date'] = Utility::convertMDYTime($tpd->created_date);
                $finalResponse[] = $temp;
            }
        }

        return response()->json(['success' => 'success', 'data' => $finalResponse]);
    }
	public function sendToTaskHealth(Request $request)
    {
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'third_party_id' => 'required',
            'appointment_id' => 'required',
            'document_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => []], 422);
        } else {

            $getDocumentDetails = $this->documentPatientService->getDocumentByDocIdAndPatientId($request->document_id, $request->appointment_id);
            $getThirdPartyDetails = $this->taskHealthMasterService->getDetailsByIdAndPatientId($request->third_party_id, $request->appointment_id);
            $getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->appointment_id);
            if (env('FILE_UPLOAD_PERMISSION') != "development") {
                $expiry = Carbon::now()->addMinutes(10);
                $path = 'patientdocument/' . $getDocumentDetails->attachment;
                $pdfContent = Storage::disk('s3')->temporaryUrl($path, $expiry);
            } else {
                $inputPath =public_path('/patientdocument/') .  $getDocumentDetails->attachment;
                $pdfContent = file_get_contents($inputPath);
            }
            $response = ThirdPartyWebHookHelper::sendTaskHelathWebhook(
                $getThirdPartyDetails->task_id,
                $getThirdPartyDetails->third_party_callback_url,
                $pdfContent,
				$getPatientDetails->status
            );

            $this->sendTaskHealthDocumentLogService->save([
                'patient_id' => $request->appointment_id,
                'document_id' => $request->document_id,
                'third_party_id' => $request->third_party_id,
                'send_response' => serialize($request->except('_token')),
                'return_response' => serialize($response),
                'agency_id' => $request->agency_id,
                'attachment' => $getDocumentDetails->attachment
            ]);
            if ($response['status'] == 200) {
                $this->documentPatientService->update(array('send_task_health_document_date'=>date('Y-m-d H:i:s'),'send_task_health_document_by'=>$auth->id),array('id'=>$request->document_id,'patient_id'=>$request->appointment_id));
                $this->sendTaskHealthDocumentService->update(array('send_third_party_document_date'=>date('Y-m-d H:i:s'),'send_third_party_document_by'=>$auth->id),array('document_id'=>$request->document_id,'patient_id'=>$request->appointment_id));
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Send Task health Document',
                    'link' => url('/task-health/send-task-health-document'),
                    'module' => 'Patient Appointment',
                    'object_id' => $request->appointment_id,
                    'message' => $auth->first_name . ' ' . $auth->last_name . ' has send task health document',
                    'old_response' => serialize($getDocumentDetails->toArray()),
                    'new_response' => serialize($request->except('_token')),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
            }
            return response()->json(['error_msg' => $response['error_msg']], $response['status']);
        }
    }

    public function revertPatientSearch(Request $request)
    {
        $agencyId = $request->input('agency_id');
        $portalId = $request->input('portal_id');
        $name     = $request->input('name');

        $query = Patient::where('agency_id', $agencyId)->where('deleted_flag', 'N')->where('type','Patient')
            ->select('id', 'first_name', 'last_name', 'mobile', 'dob', 'status');

        if (!empty($portalId)) {
            $query->where('id', $portalId);
        } elseif (!empty($name)) {
            $query->whereRaw('LOWER(CONCAT(first_name," ",last_name)) LIKE ?', ['%' . strtolower(trim($name)) . '%']);
        } else {
            return response()->json([]);
        }

        return response()->json($query->limit(20)->get());
    }

    public function revertPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_health_id' => 'required|integer',
            'patient_id'     => 'required|integer',
            'agency_id'      => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0]], 422);
        }

        $patient = $this->patientService->getPatientDetailsById($request->patient_id,$request->agency_id);

        if (!$patient) {
            return response()->json(['status' => false, 'message' => 'Selected patient not found for the chosen agency.'], 404);
        }

        $result = $this->taskHealthMasterService->revertPatient(
            $request->task_health_id,
            $request->patient_id,
            $request->agency_id
        );

        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Task health record not found.'], 404);
        }

        return response()->json(['status' => true, 'message' => 'Patient linked and agency updated successfully.']);
    }

    public function criticalAlert(Request $request)
    {
        $taskId = $request->input('task_id');
        $patient_id = $request->input('patient_id');
        $token = $request->header('Authorization');
        if ($token !== 'Bearer ' . config('services.task_health_webhook.token')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized request'
            ], 401);
        }

        if (!$request->isJson() || empty($request->all())) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or empty payload'
            ], 400);
        }

        $data = $request->all();

        $this->taskHealthCriticalAlertService->createFromWebhook($taskId, $patient_id, $data);

        return response()->json([
            'status' => true,
            'message' => 'Webhook received successfully'
        ]);
    }

    public function patientPageVisits(Request $request)
    {
        $patient = Patient::find($request->patient_id);

        if (!$patient || empty($patient->task_health_link)) {
            $data = ['items' => [], 'pagination' => [], 'error' => 'No Task Health patient linked.'];
            return view('patient._partial.task_health.visits_ajax', $data);
        }

        $record = TaskHealthMaster::where('id', $patient->task_health_link)->where('deleted_flag', 'N')->orderBy('id','desc')->first();
        $search = trim($record->task_health_patient_id);
        if (empty($search)) {
            $data = ['items' => [], 'pagination' => [], 'error' => 'Task Health record not found.'];
            return view('patient._partial.task_health.visits_ajax', $data);
        }

        $today    = date('Y-m-d');
        $firstDay = date('Y-m-d', strtotime('first day of this month'));
        $toArray = fn($val) => !empty($val) ? array_values(array_filter(explode(',', $val))) : null;
        $statuses     = $toArray($request->input('status'));
        $reviewStatus = $toArray($request->input('reviewStatus'));
        $params = array_filter([
            'sortBy'       => $request->input('sortBy', 'scheduledDateTime'),
            'fromDate'     => $request->input('fromDate', $firstDay),
            'toDate'       => $request->input('toDate', $today),
            'search'       => $search,
            'limit'        => 50,
            'page'         => $request->input('page', 1),
            'status'       => $statuses?: null,
            'reviewStatus' => $reviewStatus?: null,
        ], fn($v) => $v !== null && $v !== '');

        $result = TaskHealthApiHelper::getVisits($params);
        $items = ($result['status'] && isset($result['data']['items'])) ? $result['data']['items'] : [];

        $flagsMap = $this->taskHealthFlagsService->getFlagsMapByTaskIds(array_column($items, 'taskId'));

        $data['items']      = $items;
        $data['pagination'] = ($result['status']) ? ($result['data']['pagination'] ?? []) : [];
        $data['flagsMap']   = $flagsMap;
        $data['error']      = (!$result['status']) ? ($result['error'] ?? 'Failed to fetch visits') : null;

        return view('patient._partial.task_health.visits_ajax', $data);
    }

    public function detail($id)
    {
        $auth = auth()->user();
        if (!$auth) {
            return redirect('login');
        }
        $record = $this->taskHealthMasterService->getDataById($id);
        if (!$record) {
            abort(404);
        }
        $data['menu']   = '';
        $data['user']   = $auth;
        $data['record'] = $record;
        return view('task_health_master.task_health_master_detail', $data);
    }

    public function patientVisitsAjax(Request $request, $id)
    {
        $record = TaskHealthMaster::where('id', $id)->where('deleted_flag', 'N')->first();
        if (!$record) {
            return response()->json(['status' => 0, 'message' => 'Record not found']);
        }

        $search = trim($record->first_name . ' ' . $record->last_name);

        $params = array_filter([
            'sortBy'  => $request->input('sortBy', 'scheduledDateTime'),
            'search'  => $search,
            'limit'   => $request->input('limit', 50),
            'page'    => $request->input('page', 1),
            'status'  => $request->input('status') ?: null,
        ], fn($v) => $v !== null && $v !== '');

        $result = TaskHealthApiHelper::getVisits($params);

        $items      = [];
        $pagination = [];
        $error      = null;

        if ($result['status'] && isset($result['data']['items'])) {
            $items      = $result['data']['items'];
            $pagination = $result['data']['pagination'] ?? [];
        } else {
            $error = $result['error'] ?? 'Failed to fetch visits';
        }

        $data['items']      = $items;
        $data['pagination'] = $pagination;
        $data['error']      = $error;
        $data['taskHealthId'] = $id;

        return view('task_health_master._partial.patient_visits_ajax', $data);
    }

    /**
     * Return the visit documents from Task Health API for the detail page.
     * Also returns current flag state so the UI can decide which upload buttons to show.
     */
    /**
     * Search HHA patient by TH task_id for the upload confirmation modal.
     * No writes — read-only preview.
     */
    public function hhaPatientPreviewByTask($taskId)
    {
        // Fetch visit detail from Task Health API (same approach as the cron)
        $visitResult = TaskHealthApiHelper::getVisitDetail($taskId);
        if (empty($visitResult['data'])) {
            return response()->json(['status' => 0, 'message' => 'Could not fetch visit details from Task Health']);
        }

        $raw     = $visitResult['data'];
        $localAgency = TaskHealthApiHelper::detectLocalAgency($raw['task']['agencyId']);
        $patient = (isset($raw['patient']) && is_array($raw['patient'])) ? $raw['patient'] : [];

        $firstName = trim($patient['firstName'] ?? '');
        $lastName  = trim($patient['lastName']  ?? '');
        $phones    = $patient['phoneNumbers'] ?? [];
        $primary   = collect($phones)->firstWhere('isPrimary', true) ?? ($phones[0] ?? null);
        $phone     = preg_replace('/[^0-9]/', '', $primary['number'] ?? '');
        if (strlen($phone) === 11 && str_starts_with($phone, '1')) {
            $phone = substr($phone, 1);
        }

        try {
            $result = HHAPatientHelper::searchPatientForHHAWithAllCondition($localAgency['id'], [
                'hha_patient_first_name' => $firstName,
                'hha_patient_last_name'  => $lastName,
                'hha_patient_phone_no'   => $phone,
                'status'                 => 'Active',
            ]);
            if (empty($result)) {
                $result = HHAPatientHelper::searchPatientForHHAWithAllCondition($localAgency['id'], [
                    'hha_patient_first_name' => $lastName,
                    'hha_patient_last_name'  => $firstName,
                    'hha_patient_phone_no'   => $phone,
                    'status'                 => 'Active',
                ]);
            }
           
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'HHA search error: ' . $e->getMessage()]);
        }

        if (empty($result[0]['patient_id'])) {
            return response()->json(['status' => 0, 'message' => 'No matching HHA patient found']);
        }

        $p = $result[0];

        return response()->json([
            'status'      => 1,
            'hha_patient' => [
                'patient_id' => $p['patient_id'] ?? null,
                'first_name' => $p['first_name']  ?? $firstName,
                'last_name'  => $p['last_name']   ?? $lastName,
                'dob'        => $p['dob']          ?? null,
                'address1'   => $p['address1']     ?? null,
                'city'       => $p['city']         ?? null,
                'state'      => $p['state']        ?? null,
                'zip'        => $p['zip']          ?? null,
                'agency_id'=>$localAgency['id']
            ],
        ]);
    }

    /**
     * Upload a specific document type to HHA by TH task_id, then log the action.
     */
    public function uploadDocByTask(Request $request, $taskId)
    {
        $visitResult = TaskHealthApiHelper::getVisitDetail($taskId);
        if (!$visitResult['status'] || empty($visitResult['data'])) {
            return response()->json(['status' => 0, 'message' => 'Could not fetch visit details from Task Health'],404);
        }
        $localAgency = TaskHealthApiHelper::detectLocalAgency($visitResult['data']['task']['agencyId']);
        $docTypeId    = (int) $request->input('doc_type_id');
        $hhaPatientId = $request->input('hha_patient_id');
        $docTitle     = (string) $request->input('doc_title', '');
        $agencyId     = $localAgency['id'];
        $thTaskId     = (int) $taskId;

        if (!in_array($docTypeId, [80752, 81049, 81082, 80950, 80983,81016])) {
            return response()->json(['status' => 0, 'message' => 'Invalid document type'],500);
        }
        if(empty($hhaPatientId)){
            return response()->json(['status' => 0, 'message' => 'not found the HHA linking'],404);
        }

        // Fetch visit detail from Task Health
        $visitResult = TaskHealthApiHelper::getVisitDetail($thTaskId);
        

        // Locate the requested document
        $targetDoc = null;
        foreach ($visitResult['data']['task']['documents'] ?? [] as $doc) {
            if (($doc['type']['id'] ?? null) === $docTypeId) {
                $targetDoc = $doc;
                break;
            }
        }

        if (!$targetDoc || empty($targetDoc['url'])) {
            return response()->json(['status' => 0, 'message' => 'Document not available in this visit'],500);
        }

        $docUrl = $targetDoc['url'];
        $title  = $targetDoc['type']['title'] ?? "Document {$docTypeId}";
        $doc_type_id = TaskHealthApiHelper::getHhaDocType($docTypeId, $agencyId, $docTitle);
        // Upload to HHA
        try {
            $uploadResult = TaskHealthApiHelper::sendToHHAExtraDocument(
                $docUrl,
                $title,
                $visitResult,
                ['visit_task_health_id' => $thTaskId],
                $agencyId,
                $hhaPatientId,
                $doc_type_id
            );
        } catch (\Exception $e) {
            $this->taskHealthCronLogService->createLog([
                'cron_name'      => 'manual-upload',
                'task_id'        => $thTaskId,
                'hha_patient_id' => $hhaPatientId,
                'agency_id'      => $agencyId,
                'type'           => 'error',
                'message'        => "Manual upload failed (type {$docTypeId}): " . $e->getMessage(),
                'data'           => serialize(['doc_type_id' => $docTypeId, 'error' => $e->getMessage(),'trace' => $e->getTraceAsString()]),
                'created_by'     => auth()->user()->id
            ]);
            return response()->json(['status' => 0, 'message' => 'Upload error: ' . $e->getMessage()],500);
        }

        $success = ($uploadResult['status'] ?? 0) == 1;

        // Set assessment / kardex / patient_package_doc flag after successful upload
        if ($success && in_array($docTypeId, [80752, 81049, 81016])) {
            try {
                $this->taskHealthFlagsService->saveFlagsExtraDocsCron(
                    $visitResult['data']['patient']['id'],
                    (string) $thTaskId,
                    null,
                    $docTypeId === 80752 ? 1 : 0,
                    $docTypeId === 81049 ? 1 : 0,
                    $docTypeId === 81016 ? 1 : 0
                );
            } catch (\Exception $e) {
                // Non-fatal
            }
        }

        // Log
        $this->taskHealthCronLogService->createLog([
            'cron_name'      => 'manual-upload',
            'task_id'        => $thTaskId,
            'hha_patient_id' => $hhaPatientId,
            'agency_id'      => $agencyId,
            'type'           => $success ? 'success' : 'error',
            'message'        => "Manual upload type {$docTypeId} ({$title}): " . ($uploadResult['message'] ?? ($success ? 'uploaded' : 'failed')),
            'data'           => serialize(['doc_type_id' => $docTypeId, 'hha_patient_id' => $hhaPatientId, 'result' => $uploadResult]),
            'created_by'     => auth()->user()->id
        ]);

        return response()->json([
            'status'  => $success ? 1 : 0,
            'message' => $success ? 'Document uploaded successfully to HHA' : ($uploadResult['message'] ?? 'Upload failed'),
        ],200);
    }

    public function saveFlags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_health_master_id' => 'nullable',
            'task_id'               => 'nullable',
            'poc'                   => 'required|in:0,1',
            'mdo'                   => 'required|in:0,1',
            'alert'                 => 'required|in:0,1',
            'supervision'           => 'required|in:0,1',
            'assessment'            => 'nullable|in:0,1',
            'kardex'                => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0]], 422);
        }

        $thPatientId = null;
        $taskId      = null;
        $patientId   = null;

        if ($request->filled('task_health_master_id')) {
            $master = TaskHealthMaster::where('id', $request->task_health_master_id)
                ->where('deleted_flag', 'N')->first();
            if (!$master) {
                return response()->json(['status' => false, 'message' => 'Record not found.'], 404);
            }
            $thPatientId = $master->task_health_patient_id;
            $taskId      = $master->task_id;
            $patientId   = $master->patient_id;
        } elseif ($request->filled('task_id')) {
            // Visit context: flags are per-task (task_id is primary key)
            $taskId      = $request->task_id;
            $thPatientId = $request->input('task_health_patient_id');
            $patientId   = $request->input('patient_id') ? (int) $request->input('patient_id') : null;
        } elseif ($request->filled('task_health_patient_id')) {
            $thPatientId = $request->task_health_patient_id;
            $taskId      = $request->input('task_id');
            $patientId   = $request->input('patient_id') ? (int) $request->input('patient_id') : null;
        } else {
            return response()->json(['status' => false, 'message' => 'No valid identifier provided.'], 422);
        }

        $flag = $this->taskHealthFlagsService->saveFlags(
            $thPatientId,
            $taskId,
            $patientId,
            (int) $request->poc,
            (int) $request->mdo,
            (int) $request->alert,
            (int) $request->supervision,
            (int) $request->input('assessment', 0),
            (int) $request->input('kardex', 0)
        );

        $user = auth()->user();
        $name = $user->first_name . ' ' . substr($user->last_name, 0, 1) . '.';
        $now  = now()->format('m/d/y h:i A');

        return response()->json([
            'status'      => true,
            'poc'         => (int) $flag->poc_check,
            'mdo'         => (int) $flag->mdo_check,
            'alert'       => (int) $flag->alert_check,
            'supervision' => (int) $flag->supervision_check,
            'assessment'  => (int) $flag->assessment_check,
            'kardex'      => (int) $flag->kardex_check,
            'any_flag'    => ($flag->poc_check || $flag->mdo_check || $flag->alert_check || $flag->supervision_check || $flag->assessment_check || $flag->kardex_check) ? 1 : 0,
            'user_name'   => $name,
            'saved_at'    => $now,
        ]);
    }

    public function updateFlag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_health_master_id' => 'required|integer',
            'flag_type'             => 'required|in:poc,mdo,alert,supervision',
            'value'                 => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0]], 422);
        }

        $master = $this->taskHealthMasterService->getTaskHealthDetails($request->task_health_master_id);

        if (!$master) {
            return response()->json(['status' => false, 'message' => 'Record not found.'], 404);
        }

        $this->taskHealthFlagsService->updateFlag($master, $request->flag_type, (int) $request->value);

        $user = auth()->user();
        return response()->json([
            'status'     => true,
            'user_name'  => $user->first_name . ' ' . substr($user->last_name, 0, 1) . '.',
            'check_date' => now()->format('m/d/y'),
        ]);
    }

    public function getFlagByTaskId(Request $request)
    {
        $taskId = $request->input('task_id');
        if (!$taskId) {
            return response()->json(['status' => false, 'message' => 'task_id required'], 422);
        }

        $map  = $this->taskHealthFlagsService->getFlagsMapByTaskIds([$taskId]);
        $flag = $map[$taskId] ?? null;

        $fn = fn($u) => $u ? ($u->first_name . ' ' . substr($u->last_name, 0, 1) . '.') : '—';
        $fd = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/y h:i A') : '—';

        if (!$flag) {
            return response()->json(['status' => true, 'data' => [
                'poc' => 0, 'mdo' => 0, 'alert' => 0, 'supervision' => 0,
                'assessment' => 0, 'kardex' => 0, 'patient_package_doc' => 0,
                'poc_by' => '—', 'poc_date' => '—',
                'mdo_by' => '—', 'mdo_date' => '—',
                'alert_by' => '—', 'alert_date' => '—',
                'supervision_by' => '—', 'supervision_date' => '—',
                'assessment_by' => '—', 'assessment_date' => '—',
                'kardex_by' => '—', 'kardex_date' => '—',
                'patient_package_doc_by' => '—', 'patient_package_doc_date' => '—',
                'upd_by' => '—', 'upd_at' => '—',
            ]]);
        }

        return response()->json(['status' => true, 'data' => [
            'poc'                 => $flag->poc_check                  ? 1 : 0,
            'mdo'                 => $flag->mdo_check                  ? 1 : 0,
            'alert'               => $flag->alert_check                ? 1 : 0,
            'supervision'         => $flag->supervision_check          ? 1 : 0,
            'assessment'          => $flag->assessment_check           ? 1 : 0,
            'kardex'              => $flag->kardex_check               ? 1 : 0,
            'patient_package_doc' => $flag->patient_package_doc_check  ? 1 : 0,
            'poc_by'          => $fn($flag->pocCheckedByUser),
            'poc_date'        => $fd($flag->poc_check_date),
            'mdo_by'          => $fn($flag->mdoCheckedByUser),
            'mdo_date'        => $fd($flag->mdo_check_date),
            'alert_by'        => $fn($flag->alertCheckedByUser),
            'alert_date'      => $fd($flag->alert_check_date),
            'supervision_by'  => $fn($flag->supervisionCheckedByUser),
            'supervision_date'=> $fd($flag->supervision_check_date),
            'assessment_by'  => $fn($flag->assessmentCheckedByUser),
            'assessment_date'=> $fd($flag->assessment_check_date),
            'kardex_by'  => $fn($flag->kardexCheckedByUser),
            'kardex_date'=> $fd($flag->kardex_check_date),
            'patient_package_doc_by'  => $fn($flag->patientPackageDocCheckedByUser),
            'patient_package_doc_date'=> $fd($flag->patient_package_doc_check_date),
            'upd_by'          => $fn($flag->updatedByUser),
            'upd_at'          => $fd($flag->updated_at),
        ]]);
    }

    public function exportCsv(Request $request)
    {
        $fn = fn($u) => $u ? ($u->first_name . ' ' . substr($u->last_name, 0, 1) . '.') : '';
        $fd = fn($d) => $d ? date('m/d/Y h:i A', strtotime($d)) : '';

        $columns = [
            '#', 'Patient ID', 'TH Patient ID', 'Task ID', 'Agency Name', 'Patient Name', 'Type', 'DOB', 'Phone', 'Mobile', 'Status',
            'Critical Alert', 'CA Resolved',
            'POC', 'POC Checked By', 'POC Date',
            'MDO', 'MDO Checked By', 'MDO Date',
            'Alert', 'Alert Checked By', 'Alert Date',
            'Supervision', 'Supervision Checked By', 'Supervision Date',
            'Assessment', 'Assessment Checked By', 'Assessment Date',
            'Kardex', 'Kardex Checked By', 'Kardex Date',
            'Created Date',
        ];

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $columns);

        $cnt   = 1;
        $query = $this->taskHealthMasterService->dataListExport($request->all());

        $query->chunk(500, function ($rows) use ($handle, $fn, $fd, &$cnt) {
            foreach ($rows as $row) {
                $flag = $row->flags;
                $ca    = null;
                $caRaw = $row->latestCriticalAlert->critical_alerts ?? null;
                if ($caRaw) {
                    $decoded = @unserialize($caRaw);
                    if ($decoded === false) $decoded = json_decode($caRaw, true);
                    $ca = is_array($decoded) ? $decoded : null;
                }
                $caAlert    = $ca ? ($ca['alert'] === true ? 'Critical' : ($ca['alert'] === false ? 'Clear' : 'Pending')) : '';
                $caResolved = isset($row->latestCriticalAlert->resolved_flag) ? ($row->latestCriticalAlert->resolved_flag ? 'Yes' : 'No') : '';

                fputcsv($handle, [
                    $cnt,
                    $row->patient_id             ?? '',
                    $row->task_health_patient_id  ?? '',
                    $row->task_id                ?? '',
                    $row->agencyDetails->agency_name ?? '',
                    trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')),
                    $row->type                   ?? '',
                    $row->dob                    ?? '',
                    $row->phone                  ?? '',
                    $row->mobile                 ?? '',
                    $row->status                 ?? '',
                    $caAlert,
                    $caResolved,
                    // POC
                    $flag ? ($flag->poc_check  ? 'Yes' : 'No') : 'No',
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
                    $flag ? ($flag->kardex_check      ? 'Yes' : 'No') : 'No',
                    $flag ? $fn($flag->kardexCheckedByUser)            : '',
                    $flag ? $fd($flag->kardex_check_date)              : '',
                    $row->created_date ?? '',
                ]);
                $cnt++;
            }
        });

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $filename = 'task-health-' . date('m-d-Y');

        return response($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    public function searchPatients(Request $request)
    {
        $query = TaskHealthMaster::where('deleted_flag', 'N')
            ->where('agency_id', $request->agency_id);

        if (!empty($request->first_name)) {
            $query->where('first_name', 'LIKE', '%' . $request->first_name . '%');
        }
        if (!empty($request->last_name)) {
            $query->where('last_name', 'LIKE', '%' . $request->last_name . '%');
        }
        if (!empty($request->patient_code)) {
            $query->where('patient_code', 'LIKE', '%' . $request->patient_code . '%');
        }
        if (!empty($request->phone)) {
            $query->where(function($q) use ($request) {
                $q->where('phone', 'LIKE', '%' . $request->phone . '%')
                  ->orWhere('mobile', 'LIKE', '%' . $request->phone . '%');
            });
        }

        $results = $query->select('id', 'first_name', 'last_name', 'patient_code', 'phone', 'mobile', 'status')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json(['status' => true, 'data' => $results]);
    }

    public function linkPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required',
            'task_health_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0], 422);
        }

        $updated = Patient::where('id', $request->patient_id)->update(['task_health_link' => $request->task_health_id]);

        if ($updated !== false) {
            $ipaddress = Utility::getIP();
            $auth = auth()->user();
            LogsService::save([
                'type' => 'Link Task Health Patient',
                'link' => url('/task-health/link-patient'),
                'module' => 'Patient Appointment',
                'object_id' => $request->patient_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has linked Task Health patient.',
                'new_response' => serialize($request->except('_token')),
                'ip' => $ipaddress,
            ]);
            return response()->json(['status' => true, 'error_msg' => 'Task Health patient linked successfully'], 200);
        }

        return response()->json(['status' => false, 'error_msg' => 'Something went wrong. Please try again.'], 500);
    }

    public function unlinkPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0], 422);
        }

        $updated = Patient::where('id', $request->patient_id)->update(['task_health_link' => null]);

        if ($updated !== false) {
            $ipaddress = Utility::getIP();
            $auth = auth()->user();
            LogsService::save([
                'type' => 'Unlink Task Health Patient',
                'link' => url('/task-health/unlink-patient'),
                'module' => 'Patient Appointment',
                'object_id' => $request->patient_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has unlinked Task Health patient.',
                'new_response' => serialize($request->except('_token')),
                'ip' => $ipaddress,
            ]);
            return response()->json(['status' => true, 'error_msg' => 'Task Health patient unlinked successfully'], 200);
        }

        return response()->json(['status' => false, 'error_msg' => 'Something went wrong. Please try again.'], 500);
    }

    public function syncCriticalAlerts(Request $request)
    {
        try {
            $counts = $this->taskHealthCriticalAlertService->syncFromApi($request->only('sortBy', 'fromDate', 'toDate'));
        } catch (\RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->json([
            'status'  => true,
            'message' => "Sync complete. Created: {$counts['created']}, Updated: {$counts['updated']}, Skipped (no analysis): {$counts['skipped']}.",
            'created' => $counts['created'],
            'updated' => $counts['updated'],
            'skipped' => $counts['skipped'],
        ]);
    }

    public function patientCriticalAlerts(Request $request)
    {
        $patientId = $request->input('patient_id');
        $inline    = $request->boolean('inline');
        $patient   = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);

        $view = $inline
            ? 'patient._partial.task_health.critical_alerts_inline'
            : 'patient._partial.task_health.critical_alerts_ajax';

        if (!$patient || empty($patient->task_health_link)) {
            return view($view, ['alerts' => collect()]);
        }

        $master = $this->taskHealthMasterService->getTaskHealthDetails($patient->task_health_link);

        if (!$master || empty($master->task_health_patient_id)) {
            return view($view, ['alerts' => collect()]);
        }

        $alerts = $this->taskHealthCriticalAlertService->getByThPatientId((string) $master->task_health_patient_id);

        return view($view, compact('alerts'));
    }

    private static function agencySettingFields(): array
    {
       return [
            [
                'field' => 'hha_link',
                'label' => 'Enable HHA Linking',
                'group' => 'HHA Link',
                'description' => 'Enable patient matching and linking with the HHA system for this agency.'
            ],
            [
                'field' => 'upload_document_cron',
                'label' => 'Enable HHA Document Upload Cron',
                'group' => 'HHA Upload',
                'description' => 'This is a master control for document uploads to HHA. Other upload settings (POC, Super Vision, Assessment, Kardex, Patient Package etc.) will only work if this option is enabled.'
            ],
            [
                'field' => 'send_poc',
                'label' => 'Auto Send POC',
                'group' => 'POC',
                'description' => 'Automatically Plan of Care (POC) created and POC documents uploaded on the HHA.'
            ],
            [
                'field' => 'upload_hha_poc',
                'label' => 'Upload POC to HHA',
                'group' => 'POC',
                'description' => 'Upload Plan of Care (POC) documents on the matching HHA records.'
            ],
            [
                'field' => 'cron_upload_hha_poc',
                'label' => 'Auto Upload POC to HHA',
                'group' => 'POC',
                'description' => 'Automatic Upload Plan of Care (POC) documents on the matching HHA records.'
            ],
            [
                'field' => 'send_to_supervision',
                'label' => 'Auto Send Supervision Docs',
                'group' => 'Supervision',
                'description' => 'Automatically send supervisory visit documents upon completion of supervision visits.'
            ],
            [
                'field' => 'upload_hha_supervision',
                'label' => 'Upload Supervision Docs to HHA',
                'group' => 'Supervision',
                'description' => 'Upload supervisory visit documents (Doc 80950) on the matching HHA records.'
            ],
            [
                'field' => 'cron_upload_hha_supervision',
                'label' => 'Auto Upload Supervision Docs to HHA',
                'group' => 'Supervision',
                'description' => 'Automatic Upload supervisory visit documents (Doc 80950) on the matching HHA records.'
            ],
            [
                'field' => 'upload_hha_assessment',
                'label' => 'Upload Assessment to HHA',
                'group' => 'Assessment',
                'description' => 'Upload Patient Assessment documents (Doc 80752) on the matching HHA records.'
            ],
            [
                'field' => 'assessment',
                'label' => 'Auto Upload Assessment to HHA',
                'group' => 'Assessment',
                'description' => 'Automatic Upload Patient Assessment documents (Doc 80752) on the matching HHA records.'
            ],
            [
                'field' => 'upload_hha_kardex',
                'label' => 'Upload Kardex to HHA',
                'group' => 'Kardex',
                'description' => 'Upload Emergency Kardex documents (Doc 81049) on the matching HHA records.'
            ],
             [
                'field' => 'kardex',
                'label' => 'Auto Upload Kardex to HHA',
                'group' => 'Kardex',
                'description' => 'Automatic Upload Emergency Kardex documents (Doc 81049) on the matching HHA records.'
            ],
            [
                'field' => 'upload_hha_cms_mdo_485',
                'label' => 'Upload CMS-485 MDO',
                'group' => 'CMS MDO',
                'description' => 'Upload CMS-485 MDO documents (Doc 81082) on the matching HHA records.'
            ],
            [
                'field' => 'cron_upload_hha_cms_mdo_485',
                'label' => 'Auto Upload CMS-485 MDO',
                'group' => 'CMS MDO',
                'description' => 'Automatic Upload CMS-485 MDO documents (Doc 81082) on the matching HHA records.'
            ],
            [
                'field' => 'upload_hha_patient_package_doc',
                'label' => 'Upload Patient Package',
                'group' => 'Patient Package',
                'description' => 'Upload Patient Package documents (Doc 81016) on the matching HHA records.'
            ],
            [
                'field' => 'cron_upload_hha_patient_package_doc',
                'label' => 'Auto Upload Patient Package',
                'group' => 'Patient Package',
                'description' => 'Automatic Upload Patient Package documents (Doc 81016) on the matching HHA records.'
            ],
        ];
    }

    public function agencySettingsIndex(Request $request)
    {
        $user = auth()->user();
        $data['menu']          = 'Agency Setting';
        $data['user']          = $user;
        $data['agency_name']   = $request->agency_name;
        $data['email']         = $request->email;
        $data['settingFields'] = self::agencySettingFields();
        return view('agency_task_health_setting.list', $data);
    }

    public function agencySettingsAjaxList(Request $request)
    {
        $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['query']         = $this->agencyService->getAgencySettingsList(
            $request->agency_name,
            $request->email,
            $request->phone
        );
        $data['settingFields'] = self::agencySettingFields();

        return view('agency_task_health_setting._partial.ajax_list', $data);
    }

    public function agencySettingsToggleUpdate(Request $request)
    {
        $request->validate([
            'agency_id' => 'required|integer',
            'value'     => 'required|in:0,1',
        ]);

        $user = auth()->user();
        // Step 1: Get existing setting (can be null)
        $taskHealthSetting = $this->agencyTaskHealthSettingService->getTaskHealthSettingById($request->agency_id);

        // Step 2: OLD response (handle first-time null case)
        $oldResponse = [
            'agency_id' => $request->agency_id,
            'field'     => $request->field,
            'value'     => $taskHealthSetting
                ? ($taskHealthSetting->{$request->field} ?? null)
                : null,
        ];

        // Step 3: Update / Insert setting
        $this->agencyTaskHealthSettingService->updateAgencySetting(
            $request->agency_id,
            $request->field,
            (int) $request->value
        );

        // Step 4: NEW response
        $newResponse = [
            'agency_id' => $request->agency_id,
            'field'     => $request->field,
            'value'     => (int) $request->value,
        ];

        // Step 5: Log
        $fieldLabel  = collect(self::agencySettingFields())->firstWhere('field', $request->field)['label'] ?? $request->field;
        $action      = $request->value == 1 ? 'enabled' : 'disabled';
        $insertLog = [
            'type'         => 'Agency Task Health Setting Toggle Update',
            'link'         => url('/task-health/agency-settings-toggle-update'),
            'module'       => 'Agency Task Health Setting',
            'object_id'    => $request->agency_id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' has ' . $action . ' "' . $fieldLabel . '" (' . $request->field . ') for agency ' . $request->agency_id,
            'old_response' => serialize($oldResponse),
            'new_response' => serialize($newResponse),
            'ip'           => Utility::getIP(),
        ];

        LogsService::save($insertLog);

        return response()->json([
            'status'  => true,
            'message' => 'Updated successfully'
        ],200);
    }

    public function savePocGroupNotes(Request $request)
    {
        $request->validate([
            'agency_id' => 'required|integer',
            'notes'     => 'nullable|string|max:5000',
        ]);

        $user = auth()->user();
        if ($user['user_type_fk'] != 184) {
            return response()->json(['status' => false, 'message' => 'Unauthorized.'], 403);
        }
        $taskHealthSetting = $this->agencyTaskHealthSettingService->getTaskHealthSettingById($request->agency_id);
        $this->agencyTaskHealthSettingService->updateAgencySetting(
            $request->agency_id,
            'poc_group_notes',
            $request->notes ?? ''
        );
        $insertLog = [
            'type'         => 'Agency Task Health Setting notes updated',
            'link'         => url('agency-task-health-setting-poc-notes'),
            'module'       => 'Agency Task Health Setting',
            'object_id'    => $request->agency_id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' has updated notes for agency ' . $request->agency_id,
            'old_response' => serialize($taskHealthSetting),
            'new_response' => serialize(['agency_id' =>$request->agency_id, 'poc_group_notes' => $request->notes]),
            'ip'           => Utility::getIP(),
        ];

        LogsService::save($insertLog);
        return response()->json(['status' => true, 'message' => 'Notes saved successfully.'],200);
    }

	public function convertTaskHealth(Request $request)
	{
		$masterId = $oldMasterId = $request->input('master_id');
		$agencyId = $request->input('agency_id');

		if (empty($masterId) || empty($agencyId)) {
			return response()->json(['status' => 0, 'error_msg' => 'Master ID and Agency are required.'],422);
		}

		$master = $this->taskHealthMasterService->getById($masterId);
		if (empty($master) || empty($master->task_id)) {
			return response()->json(['status' => 0, 'error_msg' => 'Task health record or task ID not found.'],404);
		}

		// Fetch live visit detail from Task Health API
		$visitResult = TaskHealthApiHelper::getVisitDetail((int) $master->task_id);
		if (empty($visitResult['status']) || empty($visitResult['data'])) {
			return response()->json(['status' => 0, 'error_msg' => $visitResult['error'] ?? 'Failed to fetch visit detail from Task Health API.'],500);
		}

		$raw     = $visitResult['data'];
		$task    = (isset($raw['task'])    && is_array($raw['task']))    ? $raw['task']    : $raw;
		$patient = (isset($raw['patient']) && is_array($raw['patient'])) ? $raw['patient'] : [];

		// Parse phone numbers from visit detail
		$phones     = $patient['phoneNumbers'] ?? [];
		$primary    = collect($phones)->firstWhere('isPrimary', true) ?? ($phones[0] ?? null);
		$notPrimary = collect($phones)->firstWhere('isPrimary', false);
		$rawMobile  = preg_replace('/[^0-9]/', '', ($notPrimary['number'] ?? $primary['number'] ?? $master->mobile ?? ''));
		if (strlen($rawMobile) === 11 && substr($rawMobile, 0, 1) === '1') {
			$rawMobile = substr($rawMobile, 1);
		}
		$rawPhone = preg_replace('/[^0-9]/', '', ($primary['number'] ?? $master->phone ?? ''));
		if (strlen($rawPhone) === 11 && substr($rawPhone, 0, 1) === '1') {
			$rawPhone = substr($rawPhone, 1);
		}

		$dob = !empty($patient['dateOfBirth'])
			? Utility::convertMDY($patient['dateOfBirth'])
			: ($master->dob ? Utility::convertMDY($master->dob) : '');

		// Build patient_basic_data from visit detail (fall back to master fields)
		$patientBasicData = [
			'first_name'             => $patient['firstName']  ?? $master->first_name,
			'last_name'              => $patient['lastName']   ?? $master->last_name,
			'mobile'                 => $rawMobile ?: $master->mobile,
			'phone'                  => $rawPhone  ?: ($master->phone ?? ''),
			'dob'                    => $dob,
			'gender'                 => $patient['gender']     ?? $master->gender,
			'type'                   => $master->type,
			'service_id'             => $master->service_id,
			'agency_id'              => $agencyId,
			'address1'               => $patient['address']    ?? ($master->address1 ?? ''),
			'address2'               => $patient['address2']   ?? ($master->address2 ?? ''),
			'state'                  => $master->state         ?? '',
			'city'                   => $master->city          ?? '',
			'zipcode'                => $master->zip_code      ?? '',
			'language'               => $master->language ?? '',
			'discipline'             => $master->diciplin      ?? '',
			'patient_code'           => $master->patient_code  ?? '',
			'cin'                    => $master->cin            ?? '',
			'ssn'                    => $master->ssn            ?? '',
			'payment_type'           => $master->payment_type  ?? '',
			'priority'               => $master->third_party_priority ?? '',
			'partner_agency'         => $master->partner_agency  ?? '',
			'insurance_id'           => $master->insurance_id    ?? '',
			'insurance_name'         => $master->insurance_name  ?? '',
			'task_id'                => $master->task_id         ?? '',
			'task_health_patient_id' => $master->task_health_patient_id ?? '',
		];

		if ($master->fu_date) {
			$patientBasicData['followup_date'] = Utility::convertMDY($master->fu_date);
		}
		if ($master->due_date) {
			$patientBasicData['due_date'] = Utility::convertMDY($master->due_date);
		}

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

		return $this->processTaskHealthAppointment($patientBasicData, $fileContent, $fileName,$oldMasterId);
	}

	private function convertGenderLocal($gender)
	{
		$gender = strtolower(trim($gender));
		if ($gender === 'male'   || $gender === 'm') return 'male';
		if ($gender === 'female' || $gender === 'f') return 'female';
		if ($gender === 'other'  || $gender === 'o') return 'other';
		return ucfirst($gender);
	}

	private function createNewPatientLocal(array $data)
	{
		$age     = !empty($data['dob'])      ? Utility::convertYMD($data['dob'])      : null;
		$fuDate  = !empty($data['fu_date'])  ? Utility::convertYMD($data['fu_date'])  : null;
		$dueDate = !empty($data['due_date']) ? Utility::convertYMD($data['due_date']) : null;

		$serviceIds     = explode(',', $data['service_id']);
		$serviceIdArray = [];
		foreach ($serviceIds as $st) {
			$details = $this->masterService->getMasterDeatils($st,$data['type']);
			if (isset($details->id) && $details->id != '') {
				$serviceIdArray[] = $st;
			}
		}
		if (count($serviceIdArray) == 0) {
			return ['error_msg' => 'Sorry, we could not locate the service you requested.', 'status' => 0];
		}

		$ssn         = isset($data['ssn'])  ? str_replace('-', '', $data['ssn']) : '';
		$patientType = !empty($data['type']) ? ucfirst(strtolower($data['type'])) : '';
		$status      = $patientType === 'Patient' ? Utility::getStatusFromServiceId($serviceIds) : 'Pending';

		$mobileNum = preg_replace('/[^0-9]/', '', $data['mobile'] ?? '');
		if (strlen($mobileNum) == 11 && $mobileNum[0] == '1') $mobileNum = substr($mobileNum, 1);
		$phoneNum  = preg_replace('/[^0-9]/', '', $data['phone']  ?? '');
		if (strlen($phoneNum)  == 11 && $phoneNum[0]  == '1') $phoneNum  = substr($phoneNum,  1);

		$dataArray = [
			'first_name'           => $data['first_name'],
			'middle_name'          => $data['middle_name']    ?? '',
			'last_name'            => $data['last_name'],
			'full_name'            => $data['first_name'] . ' ' . $data['last_name'],
			'type'                 => $patientType,
			'dob'                  => $age,
			'fu_date'              => $fuDate,
			'due_date'             => $dueDate,
			'phone'                => $phoneNum,
			'mobile'               => $mobileNum,
			'agency_id'            => $data['agency_id'],
			'gender'               => $data['gender']          ?? '',
			'remarks'              => $data['message']         ?? '',
			'service_id'           => implode(',', $serviceIdArray),
			'patient_code'         => $data['patient_code']    ?? '',
			'diciplin'             => $data['diciplin']        ?? '',
			'language'             => Common::getOrCreateLanguageId($data['language'] ?? ''),
			'address1'             => $data['address1']        ?? '',
			'address2'             => $data['address2']        ?? '',
			'state'                => $data['state']           ?? '',
			'city'                 => $data['city']            ?? '',
			'zip_code'             => $data['zipcode']         ?? '',
			'county'               => $data['country']        ?? '',
			'payment_type'         => $data['payment_type']    ?? '',
			'partner_agency'       => $data['partner_agency']  ?? '',
			'third_party_priority' => $data['priority']        ?? '',
			'cin'                  => $data['cin']              ?? '',
			'ssn'                  => $ssn,
			'emergency_contact_name'   => $data['emergency_contact_name']   ?? '',
			'emergency_phone'          => $data['emergency_contact_number'] ?? '',
			'insurance_id'         => $data['insurance_id']    ?? '',
			'insurance_name'       => $data['insurance_name']  ?? '',
			'created_by'           => env('TASK_API_USER_ID'),
			'created_date'         => date('Y-m-d H:i:s'),
			'status'               => $status,
			'referral_type'        => 'Task Health',
		];

		if (!empty($data['third_party_callback_url'])) {
			$dataArray['third_party_callback_url'] = $data['third_party_callback_url'];
		}

        $insertId = $this->patientService->save($dataArray);
		return $insertId;
	}

	private function processTaskHealthAppointment(array $sendResponseData, ?string $fileContent, ?string $fileName,$oldMasterId)
	{
		$validator = Validator::make($sendResponseData, [
			'first_name' => 'required',
			'type'       => 'required',
			'last_name'  => 'required',
			'mobile'     => 'required|numeric|digits_between:10,15',
			'service_id' => 'required',
			'dob'        => 'required',
			'agency_id'  => 'required|numeric',
			'gender'     => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => []], 422);
		}

		$getAgencyExist = $this->agencyService->getTaskHealthAgency($sendResponseData['agency_id']);

		if (empty($getAgencyExist)) {
			return response()->json(['error_msg' => 'Agency not found. Please verify the agency details.', 'status' => 0, 'data' => []], 404);
		}

		if (!empty($sendResponseData['gender'])) {
			$sendResponseData['gender'] = $this->convertGenderLocal($sendResponseData['gender']);
		}

		$getExistingPatientDetails = $this->patientService->checkForExistingTaskHealthDataApi($sendResponseData, $sendResponseData['agency_id']);
		$patientId        = '';
		$task_health_link = '';
		$created_by       = env('TASK_API_USER_ID');

		$sendResponseData['fu_date']  = $sendResponseData['followup_date'] ?? null;
		$sendResponseData['due_Date'] = $sendResponseData['due_date']      ?? null;
		$sendResponseData['diciplin'] = $sendResponseData['discipline']    ?? null;

		if (isset($getExistingPatientDetails->id)) {
			$patientId        = $getExistingPatientDetails->id;
			$task_health_link = $getExistingPatientDetails->task_health_link;
			$flag             = 0;
		} else {
			$allDataSave                             = $sendResponseData;
			$allDataSave['third_party_callback_url'] = 'https://api.taskshealth.com/ny-best/callback';
			$patientId = $this->createNewPatientLocal($allDataSave);
			if (isset($patientId['status']) && $patientId['status'] == 0) {
				return response()->json(['error_msg' => $patientId['error_msg'], 'status' => 0, 'data' => []], 500);
			}
			$flag = 1;
		}

		$age     = !empty($sendResponseData['dob'])      ? Utility::convertYMD($sendResponseData['dob'])      : null;
		$fuDate  = !empty($sendResponseData['fu_date'])  ? Utility::convertYMD($sendResponseData['fu_date'])  : null;
		$dueDate = !empty($sendResponseData['due_date']) ? Utility::convertYMD($sendResponseData['due_date']) : null;

		$serviceIds     = explode(',', $sendResponseData['service_id']);
		$serviceIdArray = [];
		foreach ($serviceIds as $st) {
			$details = Master::where('id', $st)->where('master_type_fk', 11)->where('types', $sendResponseData['type'])->first();
			if (isset($details->id) && $details->id != '') {
				$serviceIdArray[] = $st;
			}
		}

		$masterData = [
			'patient_id'           => $patientId,
			'first_name'           => $sendResponseData['first_name'],
			'middle_name'          => $sendResponseData['middle_name']   ?? '',
			'last_name'            => $sendResponseData['last_name'],
			'full_name'            => $sendResponseData['first_name'] . ' ' . $sendResponseData['last_name'],
			'type'                 => $sendResponseData['type'],
			'dob'                  => $age,
			'fu_date'              => $fuDate,
			'due_date'             => $dueDate,
			'phone'                => isset($sendResponseData['phone'])  ? str_replace(['(', ')', ' ', '-'], '', $sendResponseData['phone'])  : '',
			'mobile'               => isset($sendResponseData['mobile']) ? str_replace(['(', ')', ' ', '-'], '', $sendResponseData['mobile']) : '',
			'agency_id'            => $sendResponseData['agency_id'],
			'gender'               => $sendResponseData['gender'],
			'remarks'              => $sendResponseData['message']       ?? '',
			'service_id'           => implode(',', $serviceIdArray),
			'patient_code'         => $sendResponseData['patient_code']  ?? '',
			'diciplin'             => $sendResponseData['diciplin']      ?? '',
			'language'             => Common::getOrCreateLanguageId($sendResponseData['language'] ?? ''),
			'address1'             => $sendResponseData['address1']      ?? '',
			'address2'             => $sendResponseData['address2']      ?? '',
			'state'                => $sendResponseData['state']         ?? '',
			'city'                 => $sendResponseData['city']          ?? '',
			'zip_code'             => $sendResponseData['zipcode']       ?? '',
			'county'               => $sendResponseData['country']       ?? '',
			'payment_type'         => $sendResponseData['payment_type']  ?? '',
			'created_date'         => date('Y-m-d H:i:s'),
			'partner_agency'       => $sendResponseData['partner_agency'] ?? '',
			'third_party_priority' => $sendResponseData['priority']      ?? '',
			'cin'                  => $sendResponseData['cin']            ?? '',
			'ssn'                  => isset($sendResponseData['ssn']) ? str_replace('-', '', $sendResponseData['ssn']) : '',
			'insurance_id'         => $sendResponseData['insurance_id']   ?? '',
			'insurance_name'       => $sendResponseData['insurance_name'] ?? '',
			'created_by'           => $created_by,
			'referral_type'        => 'Task Health',
            'third_party_callback_url' => 'https://api.taskshealth.com/ny-best/callback'
		];

		if (!empty($sendResponseData['task_health_patient_id'])) {
			$masterData['task_health_patient_id'] = $sendResponseData['task_health_patient_id'];
		}
		if (!empty($sendResponseData['task_id'])) {
			$masterData['task_id'] = $sendResponseData['task_id'];
		}

		// Return early if a master record for this task_id already exists
		$isUpdateMaster = 0;
		$insertId       = null;
		if (!empty($masterData['task_id'])) {
			$existing = $this->taskHealthMasterService->getExistingTaskData($masterData['task_id'],$masterData['agency_id']);
			if (isset($existing->id)) {
				$insertId       = $existing->id;
				$isUpdateMaster = 1;
			}
		}
		if ($isUpdateMaster == 0) {
			$insert   = new TaskHealthMaster($masterData);
			$insert->save();
			$insertId = $insert->id;
		}

		if ($isUpdateMaster == 1) {
            $this->taskHealthMasterService->update(['is_converted' => 1],['id' => $oldMasterId]);
			return response()->json(['error_msg' => 'Success', 'status' => 1, 'data' => [['appointment_id' => $insertId]]], 200);
		}

		if ($insertId) {
			if ($patientId != '') {
				if ($task_health_link != '') {
                    $this->patientService->update(['task_health_link' => $task_health_link, 'updated_date' => now(), 'updated_by' => $created_by],['id' => $patientId]);
				} else {
                    $this->patientService->update(['task_health_link' => $insertId, 'updated_date' => now(), 'updated_by' => $created_by],['id' => $patientId]);
				}

				if ($flag == 0) {
					$patientServiceCount = $this->patientServicesRequest->getServiceCountPatientId($patientId);
					if (count($patientServiceCount) == 0) {
						$services = explode(',', $getExistingPatientDetails->service_id);
						if (!empty($services[0])) {
							$patientServiceLastId = $this->patientServicesRequest->save([
								'patient_id'     => $getExistingPatientDetails->id,
								'follow_up_date' => $getExistingPatientDetails->fu_date,
								'due_date'       => $getExistingPatientDetails->due_date,
								'status'         => $getExistingPatientDetails->status,
								'created_at'     => $getExistingPatientDetails->created_date,
								'created_by'     => $created_by,
								'completed_date' => $getExistingPatientDetails->completed_date,
								'completed_by'   => $getExistingPatientDetails->completed_by,
								'flag'           => 1,
								'from_api'       => 1,
							]);
							foreach ($services as $serviceId) {
                                $serviceData = [
									'patient_id'                 => $getExistingPatientDetails->id,
									'service_id'                 => $serviceId,
									'patient_service_request_id' => $patientServiceLastId,
									'created_date'               => $getExistingPatientDetails->created_date,
									'created_by'                 => $created_by,
								];
                                $this->patientWiseServiceRequests->save($serviceData);
							}
						}
					}
				}

				$statusServiceRequest = 'Pending';
				if (strtolower($sendResponseData['type']) == 'patient') {
					$statusServiceRequest = Utility::getStatusFromServiceId($serviceIdArray);
				}

                $patientServiceLastId = $this->patientServicesRequest->save([
                       'patient_id'     => $patientId,
                        'from_api'       => 1,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'created_by'     => $created_by,
                        'follow_up_date' => $fuDate,
                        'due_date'       => $dueDate,
                        'status'         => $statusServiceRequest,
                    ]);

				foreach ($serviceIdArray as $serviceId) {
                    $serviceData = [
                           'patient_id'                 => $patientId,
                            'service_id'                 => $serviceId,
                            'patient_service_request_id' => $patientServiceLastId,
                            'created_date'               => date('Y-m-d H:i:s'),
                            'created_by'                 => $created_by,
                        ];
                    $this->patientWiseServiceRequests->save($serviceData);
				}

                $this->taskHealthMasterService->update(['requested_service_id' => $patientServiceLastId],['id' => $insertId]);
				if ($flag == 0) {
					Patient::where('id', $getExistingPatientDetails->id)->update([
						'status'     => $statusServiceRequest,
						'fu_date'    => $fuDate,
						'due_date'   => $dueDate,
						'service_id' => implode(',', $serviceIdArray),
					]);
				}
			}

			if (strtolower($sendResponseData['type']) == 'patient') {
				try {
					Utility::saveResolutionLogForms($statusServiceRequest, $patientServiceLastId, $patientId);
				} catch (\Throwable $th) {}
			}
            $ipaddress = Utility::getIP();
			LogsService::save([
				'type'         => 'Add Appointment',
				'link'         => url('/task-health-convert'),
				'module'       => 'Patient Appointment',
				'object_id'    => $patientId,
				'message'      => 'Task health created an appointment (convert)',
                'ip' => $ipaddress,
				'new_response' => serialize($sendResponseData),
			]);

			/**** Document Upload ****/
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
					'document_name'          => 'Task health - '.$masterData['task_id'],
					'attachment'             => $name,
					'patient_id'             => $patientId,
					'request_service_id'     => $patientServiceLastId,
					'is_checked'             => 0,
					'internal_use'           => 1,
					'assign_document_review' => null,
					'created_date'           => date('Y-m-d H:i:s'),
					'created_by'             => $created_by,
					'document_review_status' => 'Approved',
					'extension'              => $ext,
					'size_in_bytes'          => $fileSize,
					'pdf_type'               => $fileType,
                    'call_back_url'          => 'https://api.taskshealth.com/ny-best/callback',
					'flag'                   => 1,
				];

				$docInsertId = $this->documentPatientService->save($docData);

                if (count($serviceIdArray) >0) {
                    foreach ($serviceIdArray as $serviceId) {
                        $this->documentUploadService->save([
                            'patient_id'  => $patientId,
                            'document_id' => $docInsertId,
                            'service_id'  => $serviceId,
                        ]);
                    }
                }
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Add Document From Task Health Appointment',
                    'link' =>  url('/api/lead/save-task-health-appointment'),
                    'module' => 'Patient Appointment',
                    'object_id' => $patientId,
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
                $this->taskHealthMasterService->update(['is_converted' => 1],['id' => $oldMasterId]);
			}
			return response()->json(['error_msg' => 'Success', 'status' => 1, 'data' => [['appointment_id' => $insertId]]], 200);
		}

		return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => []], 500);
	}

}
