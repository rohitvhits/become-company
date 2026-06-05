<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\PatientService;

class AgencySummaryController extends BaseController
{
	protected	$patientService="";
	public function __construct(PatientService $patientService)
	{

		$this->middleware('permission:agency-summary-list', ['only' => ['index']]);
		$this->middleware('permission:agency-summary-export', ['only' => ['exportCSV']]);
		$this->patientService = $patientService;
	}

	public function index(Request $request)
	{
		$data['menu'] = "Agency Summary Report";
		return view("agency_summary_report.index", $data);
	}
	
    public function ajaxList(Request $request)
	{	
        $patientDetails = $this->patientService->getTotalTypeWiseCount();	
		$data['query'] = $patientDetails;        
		return view("agency_summary_report.ajax_list", $data);
	}

	public function exportCSV(Request $request)
	{
		$patientDetails = $this->patientService->getTotalTypeWiseCount('export');		
		$data['query'] = $patientDetails;
		$filename = 'AgencySummaryReport ' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);
		$columns = array('ID','Agency Name','Total Caregiver', 'Total Patient', 'Total');

		$callback = function () use ($patientDetails, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			foreach ($patientDetails as $key => $list) {
                $total = (int)$list->caregivers + (int)$list->patients;
				fputcsv($file, array($key+1,$list->agencyDetail->agency_name??'', $list->caregivers, $list->patients,$total));
			}

			fclose($file);
		};
		return response()->stream($callback, 200, $headers);
	}
}
