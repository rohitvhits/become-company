<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormSetupRequest;
use App\Services\FormSetupService;
use App\Services\DynamicFormLogService;
use App\Services\FormGroupService;
use Illuminate\Http\Request;

class FormSetupController extends Controller
{
    protected $FormSetupService = '';
    protected $dynamicFormLogService = '';
    protected $formGroupService = '';

    function __construct(FormSetupService $FormSetupService, DynamicFormLogService $dynamicFormLogService,FormGroupService $formGroupService)
    {
        $this->middleware('permission:form-setup-list|form-setup-create|form-setup-edit|form-setup-delete|form-setup-show', ['only' => ['index', 'store']]);
        $this->middleware('permission:form-setup-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:form-setup-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:form-setup-delete', ['only' => ['destroy']]);
        $this->middleware('permission:form-setup-show', ['only' => ['show']]);

        $this->middleware('auth');
        $this->FormSetupService = $FormSetupService;
        $this->dynamicFormLogService = $dynamicFormLogService;
        $this->formGroupService = $formGroupService;
    }

    public function index()
    {
        $formSetup = $this->FormSetupService->getFormSetup();
        $agencyList =  $this->FormSetupService->getAgency();
        return view('formSetup.index', compact('formSetup','agencyList'));
    }

    public function getTemplates()
    {
        $templates = $this->FormSetupService->getTemplate();
        return response()->json($templates);
    }

    public function store(FormSetupRequest $request)
    {
        $id = $request->id ?? null;

        if($id != null){
            $getExistingData = $this->FormSetupService->getFieldById($id);
        }
        $fieldMaster  = $this->FormSetupService->storeFieldMaster($request);

        $idMsg = $id == null ? 'added' : 'updated';

        $type =  $id == null ? 'Add' : 'Update';
        $getNewData = $this->FormSetupService->getFieldById($fieldMaster->id);

        // Insert form Log into Dynamic form log table
        $insertLog = [
            'type' => $type,
            'link' => url('/form-setup'),
            'module' => 'Form Setup',
            'module_id' => $getNewData->id,
            'new_response' => serialize($getNewData),
            'old_response' => $id != null ? serialize($getExistingData) : null
        ];
        $this->dynamicFormLogService->storeFormLog($insertLog);

        $this->formGroupService->storeFormGroupField($fieldMaster);

        if ($fieldMaster) {
            return response()->json(['status' => true, 'msg' => 'Form Setup '.$idMsg.' successfully', 'data' => $fieldMaster]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
        }
    }

    public function storeTemplate(Request $request)
    {
        $formId = $request->input('custom_form_id');
        $templateId = $request->input('template');

        $existingTemplate = $this->FormSetupService->getTemplateByFormId($templateId,$formId);
        $formName = $existingTemplate->getFormName->title ?? '';

        if ($existingTemplate) {
            return response()->json(['status' => false, 'msg' => "A template is already associated with {$formName} form."], 400);
        }
    

        $data = $this->FormSetupService->updateTemplateId($formId, $templateId);

        if ($data) {
            return response()->json(['status' => true, 'msg' => 'Template  Added successfully', 'data' => $data], 200);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.'], 500);
        }
    }

    public function edit($id)
    {
        $field = $this->FormSetupService->getFieldById($id);
        return response()->json(['status' => true, 'msg' => 'Get Data', 'data' => $field]);
    }

    public function destroy($id)
    {
        $getExistingData = $this->FormSetupService->getFieldById($id);
        $this->FormSetupService->deleteField($id);

        $this->FormSetupService->updateTemplateCustomFormId($id);

        $totalCount = $this->FormSetupService->totalRecord();

        // Insert form Log into Dynamic form log table
        $insertLog = [
            'type' => 'Delete',
            'link' => url('/form-setup'),
            'module' => 'Form Setup',
            'module_id' => $id,
            'old_response' => serialize($getExistingData)
        ];
        $this->dynamicFormLogService->storeFormLog($insertLog);

        $this->formGroupService->deleteFormGroup($id);

        return response()->json(['status' => true, 'msg' => 'Form Setup successfully deleted','data'=>$totalCount]);
    }
}
