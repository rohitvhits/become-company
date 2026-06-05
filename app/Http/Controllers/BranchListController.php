<?php

namespace App\Http\Controllers;

use App\Helpers\LogsHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Services\BranchListService;
use App\Services\PatientService;
use App\Services\BranchListLinkService;
use App\Helpers\Common;

class BranchListController extends BaseController
{
    protected $branchListService;
    protected $patientService;

    public function __construct(BranchListService $branchListService, PatientService $patientService)
    {
        $this->middleware('auth');
        $this->middleware('permission:branch-list', ['only' => ['index', 'ajaxList', 'show']]);
        $this->middleware('permission:branch-add', ['only' => ['store']]);
        $this->middleware('permission:branch-edit', ['only' => ['update']]);
        $this->middleware('permission:branch-delete', ['only' => ['destroy']]);

        $this->branchListService = $branchListService;
        $this->patientService = $patientService;
    }

    public function index()
    {
        return view('branch.index');
    }

    public function ajaxList(Request $request)
    {
        $branches = BranchListService::getList($request->all(), true);
        return view('branch.ajax_list', compact('branches'));
    }

    public function show($id)
    {
        $branch = $this->branchListService->getDetailsById($id);

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $branch->id,
                'branch_name' => $branch->branch_name,
                'status' => $branch->status,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|string|max:255',
        ], [
            'branch_name.required' => 'Branch name is required.',
            'branch_name.max' => 'Branch name cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $id = BranchListService::save([
            'branch_name' => $request->input('branch_name'),
        ]);

        $branch = $this->branchListService->getDetailsById($id);

        $logData = [
            'type' => 'Add Branch',
            'link' => url('branch-master/store'),
            'module' => 'Branch Management',
            'object_id' => $id,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has added branch',
            'new_response' => $branch,
        ];
        LogsHelper::handleLogs($logData);

        return response()->json([
            'status' => true,
            'message' => 'Branch created successfully.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $branch = $this->branchListService->getDetailsById($id);
        $old = $branch;

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|string|max:255',
        ], [
            'branch_name.required' => 'Branch name is required.',
            'branch_name.max' => 'Branch name cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        BranchListService::update(
            ['branch_name' => $request->input('branch_name')],
            ['id' => $id]
        );

        $new = $this->branchListService->getDetailsById($id);

        $logData = [
            'type' => 'Update Branch',
            'link' => url('branch-master/update'),
            'module' => 'Branch Management',
            'object_id' => $id,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has updated branch',
            'old_response' => $old,
            'new_response' => $new,
        ];
        LogsHelper::handleLogs($logData);

        return response()->json([
            'status' => true,
            'message' => 'Branch updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $branch = $this->branchListService->getById($id);

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found.'
            ], 404);
        }

        BranchListService::softDelete(['del_flag' => 'Y'], ['id' => $id]);

        $logData = [
            'type' => 'Delete Branch',
            'link' => url('branch-master/delete'),
            'module' => 'Branch Management',
            'object_id' => $id,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has deleted branch',
            'old_response' => $branch,
        ];
        LogsHelper::handleLogs($logData);

        return response()->json([
            'status' => true,
            'message' => 'Branch deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $branch = $this->branchListService->getById($id);

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found.'
            ], 404);
        }

        $status = $request->status == 1 ? 0 : 1;
        BranchListService::update(['status' => $status], ['id' => $id]);

        $logData = [
            'type' => 'Update Status Branch',
            'link' => url('branch/status-update'),
            'module' => 'Branch Management',
            'object_id' => $id,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has updated status of branch',
            'old_response' => $branch,
            'new_response' => ['status' => $status, 'id' => $id],
        ];
        LogsHelper::handleLogs($logData);

        return response()->json([
            'status' => true,
            'message' => 'Branch status updated successfully.'
        ]);
    }

    public function activeBranchList()
    {
        $branches = $this->branchListService->getAllActiveBranches();
        return response()->json([
            'status' => true,
            'data' => $branches
        ]);
    }

    public function saveBranch(Request $request){
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}
        $oldData = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->id);
        $branch_name = $request->location_branch;
        $is_mandatory = BranchListLinkService::checkMandatory($oldData->agency_id, explode(',',$oldData->service_id));
        if(isset($request->branch_id) && !empty($request->branch_id)){
            $branchData = $this->branchListService->getById($request->branch_id);
            $branch_name = $branchData->branch_name??NULL;
        }elseif(empty($request->location_branch)){
            if(isset($is_mandatory) && count($is_mandatory) == 0){
                $branch_name = NULL;
            }
        }
        $data=array('branch_id'=>$request->branch_id??null,'location_branch' => $branch_name);
        $this->patientService->update($data,array('id'=>$request->id));
        $logData = array(
            'type' => 'Edit Branch',
            'link' => url('patient/save-patient-branch'),
            'module' => 'Patient Appointment',
            'object_id' => $request->id,
            'message' => auth()->user()->first_name .' '.auth()->user()->last_name . ' has updated branch on portal.',
            'old_response' => $oldData,
            'new_response' => ['branch_id'=>$request->branch_id,'branch_name' => $branch_name,'id'=>$request->id],
        );
        $data['branch_name'] = $branch_name??'';
        LogsHelper::handleLogs($logData);
        try {
            if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                $agencyNotifyData = array(
                    'agencyid' => $oldData->agency_id,
                    'title' => 'Update Branch',
                    'record_id' => $request->id,
                    'record_type' => 'Appointment',
                    'msg' => '',
                    'res_data' => serialize($data)
                );
                Common::insertAgencyNotificationsOfUser($agencyNotifyData);
            }
        } catch (\Throwable $th) {}
        return response()->json(['status' => true, 'error_msg' => 'Branch has been successfully updated.','data'=>$data], 200);
    }

    public function branchList()
    {
        $branches = $this->branchListService->getAllBranches();
        return response()->json([
            'status' => true,
            'data' => $branches
        ]);
    }
}
