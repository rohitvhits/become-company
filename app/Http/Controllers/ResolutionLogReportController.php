<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\ResolutionService;
use App\Agency;
use App\User;
use Illuminate\Support\Facades\Cache;
class ResolutionLogReportController extends BaseController
{
    protected $resolutionService="";

    public function __construct(ResolutionService $resolutionService){
        $this->middleware('auth');
        $this->middleware('permission:resolution-log-report|resolution-log-export', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:resolution-log-export', ['only' => ['exportCsv']]);
        $this->resolutionService = $resolutionService;
    }

    public function index(Request $request){
        $data['menu'] = "Resolution Log Report";
		$data['user'] = auth()->user();
        $data['agencyList'] = Agency::getAgencyListWithUserAgency()->toArray();
        $data['userList'] = Cache::get('patient_master_nubest_user', function () {
            return User::getNYBestUserData();
        }, 10 * 60);
        return view("resolutionLog/resolution_log_list", $data);
    }

    public function ajaxList(Request $request){
        $data['query'] = $this->resolutionService->getAllListReport($request->all(),'paginate');
        return view("resolutionLog/resolution_log_ajax_list", $data);
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
        $columns = array('#', 'Agency Name','Portal Id', 'Portal Name','Team','Resolution','Cancel Reason','Refuse Reason','Notes','Created Date','Created By');
        $query = $this->resolutionService->getAllListReport($request->all());
        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			$cnt = 1;
			foreach ($query as $list) {
                fputcsv($file, array($cnt,$list->agency_name??'', $list->patient_id??'',$list->p_fa_name.' '.$list->p_la_name,$list->team,$list->resolution,$list->cancel_reason,$list->refuse_reason,$list->notes,$list->created_at,$list->first_name.' '.$list->last_name));
                $cnt++;
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}