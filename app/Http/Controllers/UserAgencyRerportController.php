<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\User;
use App\Agency;
use App\Services\UserService;

class UserAgencyRerportController extends BaseController
{
	protected	$userService="";
	public function __construct(UserService $userService)
	{

		$this->middleware('permission:agency-user-report-list', ['only' => ['index','']]);
		$this->userService = $userService;
	}

	public function index(Request $request)
	{
		$data['menu'] = "Agency user report";
		$data['user'] = $user = auth()->user();
		$data['agencyList'] = Agency::getAgencyList();		
		return view("agency_user_report.index", $data);
	}
	
    public function ajaxList(Request $request)
	{	
		$first_name = $data['first_name']  = request('first_name');
		$last_name = $data['last_name'] = request('last_name');
		$email = $data['email'] = request('email');
		$agency_id = $data['agency_id'] = request('agency_id');
		$record_access = $data['record_access'] = request('record_access');
		
        $usersDetails = $this->userService->getDataAgencyReport($first_name, $last_name, $email, $agency_id, $record_access);		
		$data['query'] = $usersDetails;
		return view("agency_user_report.agency_user_report_ajax", $data);
	}

	public function userExport(Request $request)
	{
		$first_name   = request('first_name');
		$last_name = request('last_name');
		$email  = request('email');
		$record_access =  request('record_access');
		$agency_id =  request('agency_id');
        $users =$this->userService->getDataAgencyReport($first_name, $last_name, $email, $agency_id, $record_access,'export');
		$filename = 'AgencyUserReport' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);
		$columns = array('ID','Agency Name','Record Type', 'Full Name', 'Email', 'Phone','EXT','Status','Last Login');

		$callback = function () use ($users, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			foreach ($users as $list) {
				fputcsv($file, array($list->id,$list->agency_name, $list->record_access,$list->first_name.' '.$list->last_name, $list->email, $list->phone,$list->ext, ucfirst($list->active), $list->last_login_at));
			}

			fclose($file);
		};
		return response()->stream($callback, 200, $headers);
	}
}
