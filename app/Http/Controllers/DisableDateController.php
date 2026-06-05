<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DisableDateService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use App\Services\DynamicFormLogService;

class DisableDateController extends BaseController
{
    protected $disableDateService;
    protected $dynamicFormLogService;
    protected const MODULE_SAVE_LINK = "disable-date";
    protected const MODULE_NAME = "Disable Date";

    public function __construct(DisableDateService $disableDateService, DynamicFormLogService $dynamicFormLogService)
    {
        $this->middleware('auth');
        $this->middleware('permission:disable-date-list', ['only' => ['index', 'disableDateList']]);
        $this->disableDateService = $disableDateService;
        $this->dynamicFormLogService = $dynamicFormLogService;
    }

    public function index()
    {
        $data['menu'] = "";
        $data['user'] = $auth = auth()->user();
        if (!$auth || $auth == null) {
            return redirect('login');
        }
        return view('disable_date/disable_date_list', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'disable_dates' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => false,
            ], 422);
        } else {
            $data = array(
                'disable_dates' => $request->disable_dates,
                'time' => ($request->time != "") ? $request->time : null,
                'type' => $request->type
            );

            $insert = $this->disableDateService->save($data);

            if ($insert) {
                $getNewData = $this->disableDateService->getDetailById($insert);

                // Insert form Log
                $insertLog = [
                    'type' => 'Add',
                    'link' => url('/' . self::MODULE_SAVE_LINK),
                    'module' => self::MODULE_NAME,
                    'module_id' => $getNewData->id,
                    'new_response' => serialize($getNewData->toArray())
                ];
                $this->dynamicFormLogService->storeFormLog($insertLog);
                
                return response()->json(['status' => true, 'error_msg' => 'Disable Date created successfully'], 200);
            }
            
            return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'disable_edit_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => false,
            ], 422);
        } else {
            $getExistingData = $this->disableDateService->getDetailById($id);

            $data = array(
                'disable_dates' => $request->disable_edit_date,
                'type' => $request->type,
                'time'=>($request->edit_time_id != "") ? $request->edit_time_id : null,
            );

            $this->disableDateService->update($data, array('id' => $id));
            $getNewData = $this->disableDateService->getDetailById($id);

            // Insert form Log
            $insertLog = [
                'type' => 'Update',
                'link' => url('/' . self::MODULE_SAVE_LINK),
                'module' => self::MODULE_NAME,
                'module_id' => $getNewData->id,
                'new_response' => serialize($getNewData),
                'old_response' => serialize($getExistingData)
            ];
            $this->dynamicFormLogService->storeFormLog($insertLog);

            return response()->json(['status' => true, 'error_msg' => 'Disable Date updated successfully'], 200);
        }
    }

    public function destroy($id)
    {
        $update = $this->disableDateService->softDelete(array('id' => $id));
        if ($update) {
            $insertLog = [
                'type' => 'Delete',
                'link' => url('/disable-date'),
                'module' => self::MODULE_NAME,
                'module_id' => $id
            ];
            $this->dynamicFormLogService->storeFormLog($insertLog);

            return response()->json(['status' => "1", 'error_msg' => "Disable Date successfully deleted.", 'data' => array()], 200);
        }
        return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
    }

    public function disableDateList(Request $request)
    {
        $data['query'] = $this->disableDateService->disableDateList();
        return view("disable_date.disable_date_ajax_list", $data);
    }

    public function disableDateById(Request $request)
    {
        $response = $this->disableDateService->getDetailById($request->id);
        return response()->json(['status' => true, 'data' => $response]);
    }
}
