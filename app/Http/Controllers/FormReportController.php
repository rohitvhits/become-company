<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FormReportService;

class FormReportController extends Controller
{

	protected $formReportService = "";

	public function __construct(FormReportService $formReportService)
	{
		$this->middleware('auth');
		$this->middleware('permission:form-report-list', ['only' => ['index', 'esignReportAjaxList']]);

		$this->formReportService = $formReportService;
	}

	public function index()
	{
		$data['menu'] = "";
		$data['user'] = $auth = auth()->user();

		if (!$auth || $auth == null) {
			return redirect('login');
		}

		$data['agencyList'] = $this->formReportService->getAllAgencyList();
		$data['formList'] = $this->formReportService->getAllFormList();

		return view('form_report/form_report_list', $data);
	}

	public function esignReportAjaxList(Request $request)
	{
		$data['query'] = $this->formReportService->dataList($request->all());
		$data['completeCountData'] =$this->formReportService->getMarkAsCompleted($request->all()); 
		$data['pendingCountData'] = $this->formReportService->getMarkAsPending($request->all());
		return view("form_report/form_report_ajax_list", $data);
	}

	public function esignReportExport(Request $request)
	{
		//ini_set('memory_limit', '4096M');

		$response = $this->formReportService->getDataExport($request->all());
		$filename = 'form_report_' . date("m-d-Y") . '.csv';

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
			'Form Name',
			'Status',
			'Mark As Completed Date',
			'Mark As Completed By',
			'Created Date',
			'Created By'
		];

	

		$callback = function () use ($response, $columns) {
			$file = fopen('php://output', 'w');

			fputcsv($file, $columns);

			$cnt = 1;

			foreach ($response as $list) {
				$status = $list->mark_as_completed == 1 ? 'Completed' : 'Pending';

				$agency_name = $list->agencies->agency_name ?? 'N/A';
				$patient_name = isset($list->patient)
					? $list->patient->first_name . ' ' . $list->patient->last_name
					: 'N/A';
				$form_name = $list->forms->title ?? 'N/A';
				$mark_as_completed_date = $list->mark_as_completed_date ?? null;
				$mark_as_completed_by_name = isset($list->userMarkAsComplatedDetails)
					? $list->userMarkAsComplatedDetails->first_name . ' ' . $list->userMarkAsComplatedDetails->last_name
					: 'N/A';
				$created_at = $list->created_at ?? null;
				$created_by_name = isset($list->users)
					? $list->users->first_name . ' ' . $list->users->last_name
					: 'N/A';

				fputcsv($file, [
					$cnt++,
					$agency_name,
					$patient_name,
					$form_name,
					$status,
					$mark_as_completed_date
						? date('m/d/Y h:i A', strtotime($mark_as_completed_date))
						: 'N/A',
					$mark_as_completed_by_name,
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
		$query = $this->formReportService->searchNybestPatient($request->q);
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
		$query = $this->formReportService->searchNybestAllUser($request->q);
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
