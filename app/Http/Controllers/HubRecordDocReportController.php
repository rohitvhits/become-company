<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\Services\HubRecordDocService;
use Illuminate\Support\Facades\Cache;

class HubRecordDocReportController extends BaseController
{
    protected $hubRecordDocService="";
    public function __construct(HubRecordDocService $hubRecordDocService)
    {
        $this->middleware('permission:hub-doc-report', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:hub-doc-report-export', ['only' => ['exportcsv']]);

        $this->middleware('auth');
        $this->hubRecordDocService = $hubRecordDocService;
    }

    public function index(){
        $data['menu'] = "user";
        $data['user']= auth()->user();
        $angecyList = Cache::get('agency', function () {
			return Agency::getAgencyListHub();
		}, 10);
		$data['agencyList'] = $angecyList;
		$data['auth'] = auth()->user();
        return view("hubRecordDocReport/index", $data);
    }
    public function ajaxList(Request $request){
       $data['query'] = $this->hubRecordDocService->getAllHubReportData($request->all());
       return view('hubRecordDocReport.ajax_list',$data);
    }

    public function exportCsv(Request $request){
        $detail = $this->hubRecordDocService->getAllHubReportData($request->all(),'export');
		$filename = 'hub_doc_report' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        if(count($detail) > 0){
            $columns = array('ID', 'Hub Record', 'Company Name', 'First Name','Last Name','Doc Name','Created Date', 'Created By');
			$callback = function () use ($detail, $columns) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns);
				foreach ($detail as $key => $list) { 
					fputcsv($file, array($key + 1, $list->hub_record_id, $list->agency_name, $list->first_name,$list->last_name,$list->document_name,date('m/d/Y',strtotime($list->created_date)),$list->userDetails->first_name.' '.$list->userDetails->last_name));
				}
				fclose($file);
			};
            return response()->stream($callback, 200, $headers);
        }else{
            return null;
        }
        
    }
    
}