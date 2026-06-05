<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgencyWiseFIeldRequest;
use App\Http\Requests\FieldMasterRequest;
use App\Model\FieldMaster;
use App\Services\FieldMasterService;
use App\Services\DynamicFormLogService;
use Illuminate\Http\Request;

class FieldMasterController extends Controller
{
    protected $FieldMasterService = '';
    protected $dynamicFormLogService = '';
    function __construct(FieldMasterService $FieldMasterService, DynamicFormLogService $dynamicFormLogService)
    {
        $this->middleware('permission:field-master-list|field-master-create|field-master-edit|field-master-delete|field-master-show', ['only' => ['index', 'store']]);
        $this->middleware('permission:field-master-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:field-master-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:field-master-delete', ['only' => ['destroy']]);
        $this->middleware('permission:field-master-show', ['only' => ['show']]);

        $this->middleware('auth');
        $this->FieldMasterService = $FieldMasterService;
        $this->dynamicFormLogService = $dynamicFormLogService;
    }

    public function index()
    {
        $formFields = $this->FieldMasterService->getFieldMaster();

        return view('fieldMaster.index', compact('formFields'));
    }

    public function create()
    {
        return view('fieldMaster._partial.create');
    }

    public function store(FieldMasterRequest $request)
    {
        $agencyId = $request->input('agency_id') ?? null;
        $formId = $request->input('form_id') ?? null;
        $id = $request->id ?? null;
        $form_group = $request->input('form_group') ?? null;

        if($id != null){
            $getExistingData = $this->FieldMasterService->getFieldById($id);
        }
        $fieldMaster  = $this->FieldMasterService->storeFieldMaster($request, $agencyId,$formId);
        $fieldMaster['form_group']=$this->FieldMasterService->getFormGroupName($form_group);

        if (empty($agencyId) && $formId) {
			$customValue = "Custom";
		} else {
			$customValue = $agencyId ? "Agency" : null;
		}

        if ($agencyId && $formId && $id == null) {
            $this->FieldMasterService->storeAgencyMaster($fieldMaster, $agencyId, $formId,$form_group);
        } elseif ($agencyId && $id == null) {
            $this->FieldMasterService->storeAgencyMasterWithoutFormId($fieldMaster, $agencyId);
        } elseif (empty($agencyId) && $formId) {
            $this->FieldMasterService->storeAgencyMasterWithoutAgencyId($fieldMaster,$formId,$form_group);
        }

        $idMsg = $id == null ? 'added' : 'updated';
        $type =  $id == null ? 'Add' : 'Update';
        $getNewData = $this->FieldMasterService->getFieldById($fieldMaster->id);
        
        // Insert form Log into Dynamic form log table
        $insertLog = [
            'type' => $type,
            'link' => url('/field-master'),
            'module' => $customValue. ' Field Master',
            'module_id' => $getNewData->id,
            'new_response' => serialize($getNewData),
            'old_response' => $id != null ? serialize($getExistingData) : null
        ];
        $this->dynamicFormLogService->storeFormLog($insertLog);

        if ($fieldMaster) {
            return response()->json(['status' => true, 'msg' => 'Field Master ' . $idMsg . ' successfully', 'data' => $fieldMaster]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
        }
    }

    public function storeAgencyField(AgencyWiseFIeldRequest $request)
    {
        $agencyId = $request->input('agency_id');
        $fieldIds = $request->input('field_id');
        $formId = $request->input('form_id') ?? null;
        $form_group_id = $request->input('form_group_id') ?? null;

        $data = $this->FieldMasterService->storeAgencyField($agencyId, $fieldIds, $formId,$form_group_id);

        // Insert form Log into Dynamic form log table
        $insertLog = [
            'type' => 'Add',
            'link' => url('/store-agency-master'),
            'module' => 'Agency Form',
            'new_response' => serialize($data)
        ];

        $this->dynamicFormLogService->storeFormLog($insertLog);

        if ($data) {
            return response()->json(['status' => true, 'msg' => 'Agency Master  Added successfully', 'data' => $data], 200);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.'], 500);
        }
    }

    public function edit($id)
    {
        $field = $this->FieldMasterService->getFieldById($id);
        if ($field->type == 'radio' || $field->type == 'select' || $field->type == 'checkbox') {
            $field->option_new = json_decode($field->options, true);
        } else {
            $field->option_new = [];
        }
        return response()->json(['status' => true, 'msg' => 'Get Data', 'data' => $field]);
    }

    public function show($id)
    {
        $field = $this->FieldMasterService->getFieldById($id);

        return view('fieldMaster.show', compact('field'));
    }

    public function destroy($id)
    {
        $getExistingData = $this->FieldMasterService->getFieldById($id);

        $deleted = $this->FieldMasterService->deleteField($id);

        $totalCount = $this->FieldMasterService->totalRecord();

        if (!$deleted) {
            return response()->json(['status' => false, 'msg' => 'Field Master cannot be deleted because it is being used in Agency Master']);
        }else{
            // Insert form Log into Dynamic form log table
            $insertLog = [
                'type' => 'Delete',
                'link' => url('/field-master'),
                'module' => 'Field Master',
                'module_id' => $id,
                'old_response' => serialize($getExistingData)
            ];
            $this->dynamicFormLogService->storeFormLog($insertLog);
        }

        return response()->json(['status' => true, 'msg' => 'Field Master successfully deleted', 'data' => $totalCount]);
    }
}
