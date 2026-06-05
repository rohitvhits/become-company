<?php

namespace App\Http\Controllers;

use App\Helpers\LogsHelper;
use App\Agency;
use App\Master;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Services\BranchListService;
use App\Services\BranchListLinkService;

class BranchListLinkController extends BaseController
{
    protected $branchListService;
    protected $branchListLinkService;

    public function __construct(BranchListService $branchListService, BranchListLinkService $branchListLinkService)
    {
        $this->middleware('auth');
        $this->middleware('permission:branch-link-list', ['only' => ['index', 'ajaxList', 'show']]);
        $this->middleware('permission:branch-link-add', ['only' => ['store']]);
        $this->middleware('permission:branch-link-edit', ['only' => ['update']]);
        $this->middleware('permission:branch-link-delete', ['only' => ['destroy']]);

        $this->branchListService = $branchListService;
        $this->branchListLinkService = $branchListLinkService;
    }

    public function index(Request $request)
    {
        $branches = $this->branchListService->getAllActiveBranches();
        $agencies = Agency::select('id', 'agency_name')->where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
        $services = Master::select('id', 'name', 'types')->where('master_type_fk', 11)->where('del_flag', 'N')->orderBy('name', 'asc')->get();

        return view('branch_link.index', compact('branches', 'agencies', 'services'));
    }

    public function ajaxList(Request $request)
    {
        $branchLinks = BranchListLinkService::getList($request->all(), true);
        return view('branch_link.ajax_list', compact('branchLinks'));
    }

    public function show($id)
    {
        $branchLink = $this->branchListLinkService->getDetailsById($id);

        if (!$branchLink) {
            return response()->json([
                'status' => false,
                'message' => 'Branch link not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $branchLink->id,
                'branch_id' => $branchLink->branch_id,
                'agency_id' => $branchLink->agency_id,
                'service_id' => $branchLink->service_id,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branch_list,id',
            'agency_ids' => 'required|array',
            'agency_ids.*' => 'exists:agency,id',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:master_table,id',
        ], [
            'branch_id.required' => 'Branch is required.',
            'agency_ids.required' => 'At least one agency is required.',
            'service_ids.required' => 'At least one service is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $branchId = $request->input('branch_id');
        $agencyIds = $request->input('agency_ids');
        $serviceIds = $request->input('service_ids');

        $insertedIds = [];
        foreach ($agencyIds as $agencyId) {
            foreach ($serviceIds as $serviceId) {
                $id = BranchListLinkService::save([
                    'branch_id' => $branchId,
                    'agency_id' => $agencyId,
                    'service_id' => $serviceId,
                ]);
                $insertedIds[] = $id;
            }
        }

        $logData = [
            'type' => 'Add Branch Link',
            'link' => url('branch-link/store'),
            'module' => 'Branch Link Management',
            'object_id' => $branchId,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has added branch link',
            'new_response' => ['branch_id' => $branchId, 'agency_ids' => $agencyIds, 'service_ids' => $serviceIds],
        ];
        LogsHelper::handleLogs($logData);

        return response()->json([
            'status' => true,
            'message' => 'Branch link created successfully.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $branchLink = $this->branchListLinkService->getDetailsById($id);
        $old = $branchLink;

        if (!$branchLink) {
            return response()->json([
                'status' => false,
                'message' => 'Branch link not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branch_list,id',
            'agency_ids' => 'required|array',
            'agency_ids.*' => 'exists:agency,id',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:master_table,id',
        ], [
            'branch_id.required' => 'Branch is required.',
            'agency_ids.required' => 'At least one agency is required.',
            'service_ids.required' => 'At least one service is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $branchId = $request->input('branch_id');
        $agencyIds = $request->input('agency_ids');
        $serviceIds = $request->input('service_ids');

        // Soft delete existing links for this branch
        BranchListLinkService::softDeleteByBranchId($old->branch_id);

        // Create new links
        foreach ($agencyIds as $agencyId) {
            foreach ($serviceIds as $serviceId) {
                BranchListLinkService::save([
                    'branch_id' => $branchId,
                    'agency_id' => $agencyId,
                    'service_id' => $serviceId,
                ]);
            }
        }

        $logData = [
            'type' => 'Update Branch Link',
            'link' => url('branch-link/update'),
            'module' => 'Branch Link Management',
            'object_id' => $id,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has updated branch link',
            'old_response' => $old,
            'new_response' => ['branch_id' => $branchId, 'agency_ids' => $agencyIds, 'service_ids' => $serviceIds],
        ];
        LogsHelper::handleLogs($logData);

        return response()->json([
            'status' => true,
            'message' => 'Branch link updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $branchLink = $this->branchListLinkService->getById($id);

        if (!$branchLink) {
            return response()->json([
                'status' => false,
                'message' => 'Branch link not found.'
            ], 404);
        }

        BranchListLinkService::softDelete(['del_flag' => 'Y'], ['id' => $id]);

        $logData = [
            'type' => 'Delete Branch Link',
            'link' => url('branch-link/delete'),
            'module' => 'Branch Link Management',
            'object_id' => $id,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has deleted branch link',
            'old_response' => $branchLink,
        ];
        LogsHelper::handleLogs($logData);

        return response()->json([
            'status' => true,
            'message' => 'Branch link deleted successfully.'
        ]);
    }

    public function getBranchesByAgencyServices(Request $request)
    {
        $agencyId = $request->input('agency_id');
        $serviceIds = $request->input('service_ids', []);

        if (empty($agencyId) || empty($serviceIds)) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $branches = BranchListLinkService::getBranchesByAgencyAndServices($agencyId, $serviceIds);

        return response()->json([
            'status' => true,
            'data' => $branches
        ]);
    }

    public function changeMandatoryOption(Request $request)
    {
        $id = $request->id;
        $is_val_mandatory = $request->is_val_mandatory;
        $branchLink = $this->branchListLinkService->getDetailsById($id);
        $old = $branchLink;
        if (!$branchLink) {
            return response()->json([
                'status' => false,
                'message' => 'Branch link not found.'
            ], 404);
        }
        $data = array('is_val_mandatory' => $is_val_mandatory);
        $this->branchListLinkService->update($data, array('id' => $id));
        $logData = [
            'type' => 'Update Branch Link Mandatory option',
            'link' => url('branch-link-ajax/change-mandatory'),
            'module' => 'Branch Link Management',
            'object_id' => $id,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has updated branch link mandatory option',
            'old_response' => $old,
            'new_response' => ['branch_id' => $branchLink->branch_id, 'is_val_mandatory' => $is_val_mandatory, 'id' => $id],
        ];
        LogsHelper::handleLogs($logData);

        return response()->json([
            'status' => true,
            'message' => 'Branch link mandatory option updated successfully.'
        ]);
    }

    public function checkMandatory(Request $request)
    {
        $agencyId = $request->input('agency_id');
        $serviceIds = $request->input('service_ids', []);
        if (empty($agencyId) || empty($serviceIds)) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }
        $branches = BranchListLinkService::checkMandatory($agencyId, $serviceIds);
        $is_val_mandatory = 0;
        if(isset($branches) && count($branches) > 0){
            $is_val_mandatory = 1;
        }
        return response()->json([
            'status' => true,
            'data' => $is_val_mandatory
        ]);
    }
}
