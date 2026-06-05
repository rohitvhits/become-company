<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\Services\LogsService;
use Illuminate\Support\Facades\Cache;

class AuditLogReportController extends BaseController
{
    protected $logsService="";
    public function __construct(LogsService $logsService)
    {
        $this->middleware('permission:audit-log-report', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:audit-log-report-export', ['only' => ['exportcsv']]);
        $this->middleware('auth');
        $this->logsService = $logsService;
    }

    public function index(){
        $data['menu'] = "user";
        $data['user']= auth()->user();
      
        return view("auditLogReport/index", $data);
    }
    public function ajaxList(Request $request){
       $data['query'] = $this->logsService->getAllLogs($request->all());
       return view('auditLogReport.ajax_list',$data);
    }

    public function getAuditviewLog(Request $request){
        $data = $this->logsService->getDatByLogsId($request->id);
        $data->old_response = unserialize($data->old_response); 
        $data->new_response = unserialize($data->new_response); 
        return response()->json(['status' => true, 'error_msg' => '','data'=>$data], 200);
    }
}