<?php

namespace App\Http\Controllers;

use App\Services\InsuranceMasterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InsuranceMasterController extends Controller
{
    protected $insuranceMasterService = "";

    public function __construct(InsuranceMasterService $insuranceMasterService)
    {
        $this->insuranceMasterService = $insuranceMasterService;

        $this->middleware('auth');
        $this->middleware('permission:insurance-master-list|insurance-master-create|insurance-master-edit|insurance-master-show|insurance-master-delete', ['only' => ['index', 'create', 'store', 'edit', 'show', 'update', 'destroy']]);
        $this->middleware('permission:insurance-master-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:insurance-master-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:insurance-master-show', ['only' => ['show']]);
        $this->middleware('permission:insurance-master-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['menu'] = "Insurance Master";
        $data['user'] = auth()->user();
        $insurance_name = $data['insurance_name'] = request('insurance_name');

        $data['query'] = $this->insuranceMasterService->getData($insurance_name);

        return view("insurance_master/index", $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'insurance_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()->toArray()]);
        } else {
            $insurance_name = request('insurance_name');

            $data = array(
                'insurance_name' => $insurance_name,
            );

            $insert = $this->insuranceMasterService->save($data);

            if ($insert) {
                return response()->json(['status' => true, 'msg' => 'Insurance added successfully', 'data' => $insert]);
            } else {
                return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
            }
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data['id'] = $id;

        $validator = Validator::make($request->all(), [
            'insurance_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()->toArray()]);
        } else {
            $insurance_name = request('insurance_name');

            $data = array(
                'insurance_name' => $insurance_name,
            );

            $this->insuranceMasterService->update($data, array('id' => $id));

            return response()->json(['status' => true, 'msg' => 'Insurance updated successfully', 'data' => $data]);
        }
    }

    public function show($id)
    {
        //
    }

    public function destroy($id)
    {
        $result = $this->insuranceMasterService->delete($id);

        if ($result) {
            return response()->json(['status' => true, 'msg' => 'Language delete successfully', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.', 'data' => $result]);
        }
    }
}