<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

use App\Helpers\AgencyMailLogHelper;
use App\Agency;
use URL;
class AgencyMailLogController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {

        $data['menu'] = "Attachment";
        $data['title'] = "Attachment List";
        $data['user'] = auth()->user();
        $data['agency_id'] =$agency_id = $request->input('agency_id');
        $data['email'] =$email = $request->input('email');
        $data['created_date'] = $created_date = $request->input('created_date');
        $data['agency_list'] = Agency::getAllAgencyList();
        $data['agency_log'] = AgencyMailLogHelper::getList($agency_id,$email,$created_date);
        return view("agency_mail_log.agency_mail_log_list",$data);
    }
   
    function export(Request $request){
        $data['agency_id'] =$agency_id = $request->input('agency_id');
        $data['email'] =$email = $request->input('email');
        $data['created_date'] = $created_date = $request->input('created_date');
        $agency_log = AgencyMailLogHelper::getListExport($agency_id,$email,$created_date);
        $filename = 'AgencyMailLog'. date("m-d-Y");
             $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('No','Agency Name','Email','Created Date');
        $callback = function() use ($agency_log,$columns) {
			$file = fopen('php://output', 'w');
            fputcsv($file, $columns);
                $cnt =1;
            foreach ($agency_log as $record) {
                $final =array($cnt++,$record->agency_name,$record->email,date('m/d/Y h:i A',strtotime($record->created_date)));
                fputcsv($file, $final);
            }
            fclose($file);
            
        };
        return response()->stream($callback, 200, $headers);
    }
}
