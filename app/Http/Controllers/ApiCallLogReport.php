<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ThirdPartyPatientLogService;
use Illuminate\Support\Facades\Cache;
use App\Agency;
use App\Model\ThirdPartyPatientLog;

class ApiCallLogReport extends Controller
{

	protected $thirdpartyPatientLogService = "";

	public function __construct(ThirdPartyPatientLogService $thirdpartyPatientLogService)
	{
		$this->middleware('auth');
		$this->middleware('permission:api-log-report-list', ['only' => ['index', 'ajaxList']]);

		$this->thirdpartyPatientLogService = $thirdpartyPatientLogService;
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
		$typeList = $this->thirdpartyPatientLogService->getAllType();
		$data['agencyList'] = $angecyList; 
		$data['typeList'] = $typeList; 		
		return view('api_log_report/api_log_report_list', $data);
	}

	public function ajaxList(Request $request)
	{
		$data['query'] = $this->thirdpartyPatientLogService->dataList($request->all());
		return view("api_log_report/api_log_report_ajax_list", $data);
	}

	public function reportExport(Request $request)
	{
		$records = ThirdPartyPatientLog::select('id','url')->wherenull('type')->orderBy('id','desc')->limit(3000)->get();

		foreach ($records as $record) {
			// Parse URL to get the endpoint
			
			$urlPath = parse_url($record->url, PHP_URL_PATH); // Get the path of the URL
			$endpoint = basename($urlPath); // Get the last part of the URL path

			// Convert endpoint to readable type
			$type = ucwords(str_replace('-', ' ', $endpoint)); // e.g., download-document → Download Document	
			// Update the record in the database
			ThirdPartyPatientLog::where('id', $record->id)->update(['type' => $type]);
		}
	}

    public function apiLogById(Request $request)
	{
		$response = $this->thirdpartyPatientLogService->getDataById($request->input('id'));
		return response()->json(['status' => true, 'msg' => 'Data get successfully', 'data' => $response]);
	}
}
