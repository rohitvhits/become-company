<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskHealthLogService;
use Illuminate\Support\Facades\Cache;
use App\Agency;
use App\Model\TaskHealthLog;

class TaskHealthLogController extends Controller
{

	protected $taskHealthLogService = "";

	public function __construct(TaskHealthLogService $taskHealthLogService)
	{
		$this->middleware('auth');
		$this->middleware('permission:task-health-log-list', ['only' => ['index', 'ajaxList']]);

		$this->taskHealthLogService = $taskHealthLogService;
	}

	public function index()
	{
		$data['menu'] = "";
		$data['user'] = $auth = auth()->user();

		if (!$auth || $auth == null) {
			return redirect('login');
		}

		$angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$typeList = $this->taskHealthLogService->getAllType();
		$data['agencyList'] = $angecyList;
		$data['typeList'] = $typeList;
		return view('task_health_log/task_health_log_list', $data);
	}

	public function ajaxList(Request $request)
	{
		$data['query'] = $this->taskHealthLogService->dataList($request->all());

		return view("task_health_log/task_health_log_ajax_list", $data);
	}

    public function taskHealthLogById(Request $request)
	{
		$response = $this->taskHealthLogService->getDataById($request->input('id'));
		return response()->json(['status' => true, 'msg' => 'Data get successfully', 'data' => $response]);
	}
}
