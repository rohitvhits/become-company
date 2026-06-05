<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Model\HubCompany;
use App\User;
use App\Services\HubRecordService;
use Illuminate\Support\Facades\Cache;

class HubRecordReportController extends BaseController
{
    protected $hubRecordService="";
    public function __construct(HubRecordService $hubRecordService)
    {
        $this->middleware('permission:hub-record-report', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:hub-record-report-export', ['only' => ['exportcsv']]);

        $this->middleware('auth');
        $this->hubRecordService = $hubRecordService;
    }

    public function index(){
        $data['menu'] = "user";
        $data['user']= auth()->user();
        $angecyList = Cache::get('agency', function () {
			return HubCompany::getAgencyListHub();
		}, 10);
		$data['agencyList'] = $angecyList;
		$data['auth'] = auth()->user();
        return view("hubRecordReport/index", $data);
    }
    public function ajaxList(Request $request){
		$search_data = $request->all();
		$search_data['is_dependent'] ="N";
       $data['query'] = $this->hubRecordService->getHubData($search_data);
	   foreach($data['query'] as $query){
			$query->ssn = Utility::formatSSN($query->ssn);
		}
       return view('hubRecordReport.ajax_list',$data);
    }

    public function exportCsv(Request $request){
		$search_data = $request->all();
		$search_data['is_dependent'] ="N";
        $detail = $this->hubRecordService->getHubData($search_data,'export');
		$filename = 'hub_record_report' . date("m-d-Y");
        $auth = auth()->user();
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        if(count($detail) > 0){
            if($auth->view_ssn_hub ==1){
				$columns = array('ID', 'First Name','Middle Name','Last Name','Birth Date','Gender','Email', 'Address 1', 'Address 2','City','Zip Code','Phone','Mobile','SSN','Company','Employee Code','Work Contact','Work Email','Hire Date','Last Work Date','Created Date', 'Created By','Updated Date','Updated By');
			}else{
				$columns = array('ID', 'First Name','Middle Name','Last Name','Birth Date','Gender','Email', 'Address 1', 'Address 2','City','Zip Code','Phone','Mobile','Company','Employee Code','Work Contact','Work Email','Hire Date','Last Work Date','Created Date', 'Created By','Updated Date','Updated By');
			}
			$callback = function () use ($detail, $columns,$auth) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns);
				foreach ($detail as $list) { 
					$updatedName = isset($list->usersUpdate) && !empty($list->usersUpdate) ? $list->usersUpdate->first_name.' '.$list->usersUpdate->last_name : '';
					$hire_date = $list->hire_date != '' ? date('m/d/Y',strtotime($list->hire_date)) : '';
					$last_worked_date = $list->last_worked_date != '' ? date('m/d/Y',strtotime($list->last_worked_date)) : '';
					$updated_date = $list->updated_date != '' && !empty($list->updated_date) ? date('m/d/Y',strtotime($list->updated_date)) : '';
					if($auth->view_ssn_hub ==1){
						fputcsv($file, array($list->id, $list->first_name,$list->middle_name,$list->last_name, date('m/d/Y', strtotime($list->dob)), ucfirst($list->gender),$list->email,$list->address1,$list->address2,$list->city,$list->zip_code,$list->phone,$list->mobile,Utility::formatSSN($list->ssn),$list->agency_name,$list->employee_code,$list->work_contact,$list->work_email,$hire_date,$last_worked_date,date('m/d/Y',strtotime($list->created_date)),$list->users->first_name.' '.$list->users->last_name,$updated_date,$updatedName));
					}else{
						fputcsv($file, array($list->id, $list->first_name,$list->middle_name,$list->last_name, date('m/d/Y', strtotime($list->dob)), ucfirst($list->gender),$list->email,$list->address1,$list->address2,$list->city,$list->zip_code,$list->phone,$list->mobile,$list->agency_name,$list->employee_code,$list->work_contact,$list->work_email,$hire_date,$last_worked_date,date('m/d/Y',strtotime($list->created_date)),$list->users->first_name.' '.$list->users->last_name,$updated_date,$updatedName));
					}
				}
				fclose($file);
			};
            return response()->stream($callback, 200, $headers);
        }else{
            return null;
        }
        
    }
    
}