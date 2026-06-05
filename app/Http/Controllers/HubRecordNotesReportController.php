<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Model\HubCompany;
use App\Master;
use App\Services\HubRecordNotesService;
use Illuminate\Support\Facades\Cache;

class HubRecordNotesReportController extends BaseController
{
    protected $hubRecordNotesService="";
    public function __construct(HubRecordNotesService $hubRecordNotesService)
    {
        $this->middleware('permission:hub-notes-report', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:hub-notes-report-export', ['only' => ['exportcsv']]);
        $this->middleware('auth');
        $this->hubRecordNotesService = $hubRecordNotesService;
    }

    public function index(){
        $data['menu'] = "user";
        $data['user']= auth()->user();
        $angecyList = Cache::get('agency', function () {
			return HubCompany::getAgencyListHub();
		}, 10);
		$data['agencyList'] = $angecyList;
		$data['masterSubjectData'] = Master::getAllDataByMasterTypeFk(array(30));
		$data['auth'] = auth()->user();
        return view("hubRecordNotesReport/index", $data);
    }
    public function ajaxList(Request $request){
       $data['query'] = $this->hubRecordNotesService->getAllHubReportData($request->all());
       return view('hubRecordNotesReport.ajax_list',$data);
    }

    public function exportCsv(Request $request){
        $detail = $this->hubRecordNotesService->getAllHubReportData($request->all(),'export');
		$filename = 'hub_record_notes_report' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        if(count($detail) > 0){
            $columns = array('ID', 'Hub Record', 'Company name','First Name','Last Name','Subject','Notes','Created Date', 'Created By');
			$callback = function () use ($detail, $columns) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns);
				foreach ($detail as $key => $list) { 
                    fputcsv($file, array($key + 1, $list->hub_record_id, $list->agency_name, $list->first_name,$list->last_name,$list->subject,$list->message,date('m/d/Y',strtotime($list->created_date)),$list->users->first_name.' '.$list->users->last_name));
                }
				fclose($file);
			};
            return response()->stream($callback, 200, $headers);
        }else{
            return null;
        }
    }
    
}