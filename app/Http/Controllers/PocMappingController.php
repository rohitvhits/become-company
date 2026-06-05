<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Services\PocMappingService;
use App\Services\HHAOfficeService;
use App\Services\HHAPOCTaskService;
use App\Services\HHAPatientService;
use App\Helpers\HHAPatientHelper;

class PocMappingController extends BaseController
{
    protected $pocMappingService;
    protected $hhaOffice;
    protected $hhaPOCTaskService;
    protected $hhaPatientService;

    public function __construct(
        PocMappingService $pocMappingService,
        HHAOfficeService $hhaOffice,
        HHAPOCTaskService $hhaPOCTaskService,
        HHAPatientService $hhaPatientService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:poc-mapping-list|poc-mapping-save|poc-mapping-sync', ['only' => ['index', 'getTasksWithMappings', 'saveAll', 'syncTasks']]);
        $this->middleware('permission:poc-mapping-list', ['only' => ['index', 'getTasksWithMappings']]);
        $this->middleware('permission:poc-mapping-save', ['only' => ['saveAll']]);
        $this->middleware('permission:poc-mapping-sync', ['only' => ['syncTasks']]);
        $this->pocMappingService  = $pocMappingService;
        $this->hhaOffice          = $hhaOffice;
        $this->hhaPOCTaskService  = $hhaPOCTaskService;
        $this->hhaPatientService  = $hhaPatientService;
    }

    public function index()
    {
        $data['agency_list'] = $this->pocMappingService->getAgencies();
        return view('poc_mapping.index', $data);
    }

    public function getTasksWithMappings(Request $request)
    {
        $agencyId = $request->agency_id;
        if (empty($agencyId)) {
            return response()->json([]);
        }
        $data = $this->pocMappingService->getTasksWithMappings($agencyId);
        return response()->json($data);
    }

    public function saveAll(Request $request)
    {
        $request->validate(['agency_id' => 'required|integer']);
        $this->pocMappingService->saveAll($request->agency_id, $request->mappings ?? []);
        return response()->json(['success' => true, 'message' => 'Mappings saved successfully.']);
    }

    public function syncTasks(Request $request)
    {
        $agencyId = $request->agency_id;
        if (empty($agencyId)) {
            return response()->json(['success' => false, 'message' => 'Agency is required.'], 422);
        }

        $sha1Id    = sha1($agencyId);
        $getOffice = $this->hhaOffice->getOfficeDetailsBySha1AgencyId($sha1Id);

        if (empty($getOffice[0])) {
            return response()->json(['success' => false, 'message' => 'No office found for this agency.'], 404);
        }

        $synced = 0;
        foreach ($getOffice as $val) {
            $getOffices = $this->hhaPatientService->getPatientDetailsWithWithAgencyId($sha1Id, $val->office_id);
            if (!isset($getOffices->patient_id)) {
                continue;
            }

            $getPOCTask = HHAPatientHelper::getHHAPOCTask($getOffices->patient_id, $getOffices->officeId, "");
            if (empty($getPOCTask) || empty($getPOCTask[0])) {
                continue;
            }

            foreach ($getPOCTask as $poc) {
                $this->hhaPOCTaskService->save([
                    'task_id'   => $poc['id'],
                    'task_name' => $poc['task_name'],
                    'task_code' => $poc['code'],
                    'category'  => $poc['task_category'],
                    'agency_id' => $agencyId,
                ]);
                $synced++;
            }
        }

        if ($synced === 0) {
            return response()->json(['success' => false, 'message' => 'No POC tasks found to sync.'], 404);
        }

        return response()->json(['success' => true, 'message' => $synced . ' POC tasks synced successfully.']);
    }
}
