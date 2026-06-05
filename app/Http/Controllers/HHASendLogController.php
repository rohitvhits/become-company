<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Services\HHALogService;

class HHASendLogController extends BaseController
{
    protected $hhaLogService;

    public function __construct(HHALogService $hhaLogService)
    {
        $this->middleware('auth');
        $this->middleware('permission:hha-log', ['only' => ['index','ajaxList','viewDetail']]);
       
        $this->hhaLogService = $hhaLogService;
    }

    public function index(Request $request)
    {
        return view('hhaSendLog.index');
    }

    public function ajaxList(Request $request)
    {
        $data['page'] = $request->page ?? 1;
        $data['list'] = $this->hhaLogService->getSendLogList($request->all());
        return view('hhaSendLog.ajax_list', $data);
    }

    public function viewDetail(Request $request)
    {
        $record = $this->hhaLogService->getSendLogById($request->id);

        if (!$record) {
            return response()->json(['error_msg' => 'Record not found'], 404);
        }
        $record->send_request = @unserialize($record->send_request) ?: $record->send_request;
        $record->send_response = @unserialize($record->send_response) ?: $record->send_response;
        $record->return_response = @unserialize($record->return_response) ?: $record->return_response;
        return response()->json(['data' => $record], 200);
    }
}
