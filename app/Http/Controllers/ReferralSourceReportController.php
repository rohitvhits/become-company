<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\PatientService;
use App\User;
use App\Master;
class ReferralSourceReportController extends BaseController
{
    protected $patientService="";

    public function __construct(PatientService $patientService){
        $this->middleware('auth');
        $this->middleware('permission:referral-source-report|referral-source-export', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:referral-source-export', ['only' => ['exportCsv']]);
        $this->patientService = $patientService;
    }

    public function index(Request $request){
        $data['menu'] = "Referral Source Report";
		$data['user'] = $user = auth()->user();
        $data['masterData'] = Master::getAllDataByMasterTypeFk(array(31));
        return view("referralSourceReport/referral_source_list", $data);
    }

    public function ajaxList(Request $request){
        $data['query'] = $this->patientService->referralSourceList($request->all());
        return view("referralSourceReport/referral_source_ajax_list", $data);
    }

    public function exportCsv(Request $request){
        $filename = 'Patient' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);
        $columns = array('#', 'Referral Type','Caregiver', 'Patient');
        $query = $this->patientService->referralSourceList($request->all());
        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			$cnt = 1;
			foreach ($query as $list) {
                fputcsv($file, array($cnt,$list->referral_type, $list->caregiver_count,$list->patient_count));
                $cnt++;
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}