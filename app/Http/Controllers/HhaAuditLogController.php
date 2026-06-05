<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\HhaAuditLogService;

class HhaAuditLogController extends BaseController
{
    protected $hhaAuditLogService;

    public function __construct(HhaAuditLogService $hhaAuditLogService)
    {
        $this->middleware('auth');
        $this->middleware('permission:hha-audit-log-view|hha-audit-log-list', ['only' => ['index', 'getLogs']]);
        $this->middleware('permission:hha-audit-log-detail-view', ['only' => ['show']]);
        $this->hhaAuditLogService = $hhaAuditLogService;
    }

    public function index()
    {
        return view('hha_audit_log.index');
    }

    public function getLogs(Request $request)
    {
        $filters = [];
        $data['page'] = $request->page ?? 1;

        if (!empty($request->patient_id)) {
            $filters['patient_id'] = $request->patient_id;
        }

        if (!empty($request->hha_patient_id)) {
            $filters['hha_patient_id'] = $request->hha_patient_id;
        }

        if (!empty($request->from_date)) {
            $filters['from_date'] = date('Y-m-d', strtotime($request->from_date));
        }

        if (!empty($request->to_date)) {
            $filters['to_date'] = date('Y-m-d', strtotime($request->to_date));
        }

        $data['logs'] = $this->hhaAuditLogService->getListing($filters);

        return view('hha_audit_log.ajax_list', $data);
    }

    public function show($id)
    {
        $log = $this->hhaAuditLogService->getById($id);

        if (!$log) {
            return response()->json(['error_msg' => 'Log not found', 'data' => []], 404);
        }

        $sendResponse = '';
        $returnResponse = '';

        if (!empty($log->send_response)) {
            $unserialized = @unserialize($log->send_response);
            $sendResponse = $unserialized !== false ? $unserialized : $log->send_response;
        }

        if (!empty($log->return_response)) {
            $unserialized = @unserialize($log->return_response);
            $returnResponse = $unserialized !== false ? $unserialized : $log->return_response;
        }

        return response()->json([
            'error_msg' => 'Success',
            'data' => [
                'send_response' => $sendResponse,
                'return_response' => $returnResponse,
            ]
        ], 200);
    }
}
