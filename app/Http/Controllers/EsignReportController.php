<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EsignReportService;
use App\Services\LocationMasterService;
use Illuminate\Support\Facades\Cache;
use App\Agency;

class EsignReportController extends Controller
{

	protected $esignReportService,$locationMasterService = "";

	public function __construct(EsignReportService $esignReportService,LocationMasterService $locationMasterService)
	{
		$this->middleware('auth');
		$this->middleware('permission:esign-report-list', ['only' => ['index', 'ajaxList']]);

		$this->esignReportService = $esignReportService;
		$this->locationMasterService = $locationMasterService;
	}

	public function index(Request $request)
	{
		$data['menu'] = "";
		$data['user'] = $auth = auth()->user();

		if (!$auth || $auth == null) {
			return redirect('login');
		}

		$data['templateList'] = $this->esignReportService->getAllTemplateList();
		$angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['location_list'] = Cache::get('patient_master_locations', function () {
			return $this->locationMasterService->AllListWithoutPaginate();
		}, 10 * 60);
		$data['agencyList'] = $angecyList; 
		
		$data['search_param'] = $request->all();
		return view('esign_report/esign_report_list', $data);
	}

	public function ajaxList(Request $request)
	{
		$data['query'] = $this->esignReportService->dataList($request->all());
		
		if (!empty($data['query'][0])) {
			foreach ($data['query'] as $key => $val) {
				$totalSigner = $this->esignReportService->TotalSignerCount($val->groupId);
				$data['query'][$key]->signerRemaining = $totalSigner[0]->total ?? 0; 
			}
		}
		
		return view("esign_report/esign_report_ajax_list", $data);
	}

	public function reportExport(Request $request)
	{
		//ini_set('memory_limit', '4096M');

		$response = $this->esignReportService->getDataExport($request->all());
		$filename = 'esign_report_' . date("m-d-Y") . '.csv';

		$headers = [
			"Content-Type" => "text/csv",
			"Content-Disposition" => "attachment; filename={$filename}",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		];

		$columns = [
			'No',
			'Agency Name',
			'Patient Name',
			'Template Name',
			'Status',
			'Sender Name',
			'Completed Date',
			'Created Date',
			'Created By'
		];

	

		$callback = function () use ($response, $columns) {
			$file = fopen('php://output', 'w');

			fputcsv($file, $columns);

			$cnt = 1;

			foreach ($response as $list) {
				$status = $list->status ?? 'N/A';
				$patient_name = isset($list->patient)
					? $list->patient->first_name . ' ' . $list->patient->last_name
					: 'N/A';
				$templete_name = isset($list->templateDetails)
					? $list->templateDetails->template_name : 'N/A';
				$sender_name = isset($list->sender_name)
					? $list->sender_name : 'N/A';
				$completed_on = $list->completed_on ?? null;
				$created_at = $list->created_date ?? null;
				$created_by_name = isset($list->userDetails)
					? $list->userDetails->first_name . ' ' . $list->userDetails->last_name
					: 'N/A';
				$agency_name = isset($list->patient->agencyDetail)
				? $list->patient->agencyDetail->agency_name
				: 'N/A';
				fputcsv($file, [
					$cnt++,
					$agency_name,
					$patient_name.'('.$list->patient->id.')',
					$templete_name,
					$status,
					$sender_name,
					$completed_on
						? date('m/d/Y h:i A', strtotime($completed_on))
						: 'N/A',
					$created_at
						? date('m/d/Y h:i A', strtotime($created_at))
						: 'N/A',
					$created_by_name
				]);
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}

	public function searchNyBestPatient(Request $request){
		$query = $this->esignReportService->searchNybestPatient($request->q);
		$final = [];
		foreach($query as $val){
			$temp = [];
			$temp['id'] = $val->id;
			$temp['name'] = $val->first_name .' '. $val->last_name;
			$final[] = $temp;
		}
		return json_encode($final);
	}

	public function searchNyBestAllUser(Request $request){
		$query = $this->esignReportService->searchNybestAllUser($request->q);
		$final = [];
		foreach($query as $val){
			$temp = [];
			$temp['id'] = $val->id;
			$temp['name'] = $val->first_name .' '. $val->last_name;
			$final[] = $temp;
		}
		return json_encode($final);
	}
}
