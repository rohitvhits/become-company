<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskHealthCronLogService;
use Illuminate\Support\Facades\Cache;
use App\Agency;

class TaskHealthCronLogController extends Controller
{
    protected $service;

    public function __construct(TaskHealthCronLogService $service)
    {
        $this->middleware('auth');
        $this->middleware('permission:task-health-cron-log-list', ['only' => ['index', 'ajaxList']]);

        $this->service = $service;
    }

    public function index()
    {
        $data['menu'] = '';
        $data['user']  = $auth = auth()->user();

        if (!$auth) {
            return redirect('login');
        }

        $data['agencyList']    = Cache::get('patient_master_locations_cron', fn () => Agency::getAgencyList(), 10);
        $data['typeList']      = $this->service->getAllTypes();
        $data['cronNameList']  = $this->service->getAllCronNames();

        return view('task_health_cron_log/task_health_cron_log_list', $data);
    }

    public function ajaxList(Request $request)
    {
        $data['query'] = $this->service->dataList($request->all());

        return view('task_health_cron_log/task_health_cron_log_ajax_list', $data);
    }

    public function taskHealthCronLogById(Request $request)
    {
        $response = $this->service->getDataById($request->input('id'));

        return response()->json(['status' => true, 'msg' => 'Data get successfully', 'data' => $response]);
    }
}
