<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormGroupRequest;
use App\Model\FormGroup;
use App\Services\FormGroupService;
use Illuminate\Http\Request;

class FormGroupController extends Controller
{
    protected $formGroupService = '';
    function __construct(FormGroupService $formGroupService)
    {
        $this->middleware('permission:form-group-list|form-group-create|form-group-edit|form-group-delete|form-group-show', ['only' => ['index', 'store']]);
        $this->middleware('permission:form-group-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:form-group-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:form-group-delete', ['only' => ['destroy']]);
        $this->middleware('permission:form-group-show', ['only' => ['show']]);

        $this->middleware('auth');
        $this->formGroupService = $formGroupService;
    }

    public function index(Request $request)
    {
        $form_id = $request->form_id;
        $data = $this->formGroupService->getData($form_id);

        return view('formGroup.index', compact('data','form_id'));
    }

    public function formGroupList(Request $request)
	{
        $data['form_id'] = $request->form_id;
        $data['query'] = $this->formGroupService->getData($data['form_id']);
		return view("formGroup._partial.form_group_ajax_list", $data);
    }

    public function getFormGroups(Request $request)
    {
        $form_id= $request->form_id;
        $formGroups = $this->formGroupService->getFormGroupData($form_id);
        return response()->json($formGroups);
    }

    public function create()
    {
        return view('formGroup._partial.create');
    }

    public function store(FormGroupRequest $request)
    {
        $id = $request->id ?? null;

        $data  = $this->formGroupService->store($request);


        $idMsg = $id == null ? 'added' : 'updated';
        
        if ($data) {
            return response()->json(['status' => true, 'msg' => 'Form Group ' . $idMsg . ' successfully', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
        }
    }

    public function edit($id)
    {
        $data = $this->formGroupService->getDataById($id);
        return response()->json(['status' => true, 'msg' => 'Get Data', 'data' => $data]);
    }

    public function destroy($id)
    {
        $this->formGroupService->delete(array('id' => $id));
        
        $totalCount = $this->formGroupService->totalRecord();

        return response()->json(['status' => true, 'msg' => 'Form Group successfully deleted', 'data' => $totalCount]);
    }

    public function updateFormGroupOrder(Request $request)
    {
        $sortOrder = $request->input('sortOrder');
        dd($sortOrder);
        if (isset($sortOrder) && is_array($sortOrder)) {
            foreach ($sortOrder as $item) {
                // dd($item['id']);
                $updateOrder = FormGroup::where('id', $item['id']);
            //    dd($updateOrder);
                if (array_key_exists('formID', $item)) {
                    $updateOrder = $updateOrder->where('form_id', $item['formID']);
                } 
                $updateOrder->update(['sort_id' => $item['order']]);
            }
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
}
