<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App\Agency;

use App\Services\PatientService;

use App\Services\LocationMasterService;
use App\Master;

use App\Model\Language;
use App\Helpers\Utility;

use Illuminate\Support\Facades\Cache;

class DemographicController extends BaseController
{


	protected $patientService, $locationMasterService = "";

	public function __construct(PatientService $patientService, LocationMasterService $locationMasterService)
	{

		// $this->middleware('permission:chart-list', ['only' => ['index','ajaxList']]);
		 $this->middleware('permission:chart-list', ['only' => ['ajaxList']]);
		$this->middleware('permission:export-csv', ['only' => ['exportCsv']]);
		$this->patientService = $patientService;
		$this->locationMasterService = $locationMasterService;
	}

	public function index(Request $request)
	{
		$data['menu'] = "Patient List";
		$data['user'] = $user = auth()->user();
		if ($user->login_type_fk != 183) {
			return redirect('appointment');
		}
		if ($user->agency_fk != "") {
			$checkForAgencyDeteleted = Agency::getDetailsByAgencyId($user->agency_fk);
			if (isset($checkForAgencyDeteleted->id)) {
			} else {
				// die("This pages is redirected to another page. Please login and click appropriate menu.");
				return redirect('support_error');
			}
		}
		$angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);

		$data['location_list'] = $locationList = Cache::get('patient_master_locations', function () {
			return $this->locationMasterService->AllListWithoutPaginate();
		}, 10 * 60);

		$data['agencyList'] = $angecyList;

		$data['language_list'] =  Cache::get('language_list', function ()  use ($user) {
			return Language::getLanguageList();
		}, 10 * 60);

		$data['masterData'] =  Cache::get('masters_data', function ()  use ($user) {
			return Master::getAllDataByMasterTypeFk(array(17, 26));
		}, 10, 60);
		$data['statuses'] = Utility::getUniqueStatusData();
		return view('demographic.index', $data);
	}

	public function ajaxList(Request $request)
	{
		$data['user'] = $data['auth'] = auth()->user();
		$data['query'] = $this->patientService->patientDemographicDetails($request->all());
		$data['totalCount'] = $this->patientService->patientDemographicCount($request->all());

		return view('demographic.ajax_list', $data);
	}

	public function exportCsv(Request $request)
	{

		$user = auth()->user();
		$query = $this->patientService->patientDemographicDetails($request->all(), 'export');
		$filename = 'Patient' . date("m-d-Y");

		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

		$columns = array('Record Id', 'Agency Name', 'Type', 'Discipline', 'Patient Code', 'Full Name', 'Phone', 'Gender', 'Dob', 'Location',  'Status', "Assign NyBest User", "Created Date", "Created By", 'Due Date', 'FU Date', 'Training Due Date');

		$callback = function () use ($query, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			foreach ($query as $list) {

				$assign_fname = '';

				if (isset($list->assignToUser->first_name) && $list->assignToUser->first_name != '') {
					$assign_fname = $list->first_name . ' ' . $list->assignToUser->last_name;
				}

				$assignName = $assign_fname;
				$date = '';

				if ($list->dob != '0000-00-00' && $list->dob != '') {
					$date = Utility::convertMDY($list->dob);
				}

				$created_by_username =  $list->users ? ($list->users->first_name . ' ' . $list->users->last_name) : '';

				$created_date = '';
				if ($list->created_date != "" || $list->created_date != NULL) {
					$created_date = date('d/m/Y h:i A', strtotime($list->created_date));
				}

				$fu_date = '';
				if ($list->fu_date != "" && $list->fu_date != NULL) {
					if ($list->fu_date != "1969-12-31" && $list->fu_date != "0000-00-00") {
						$fu_date = date('m/d/Y', strtotime($list->fu_date));
					}
				}

				$trainingDate = "";
				$trainingDate = date('m/d/Y', strtotime($list->traning_due_date));
				$data = array($list->id, $list->agency_name, $list->type, $list->diciplin, $list->patient_code, $list->first_name . ' ' . $list->middle_name . ' ' . $list->last_name, $list->mobile, $list->gender, $date, $list->location_name, $list->status,  $assignName, $created_date, $created_by_username,  $fu_date);
				$data[] = $trainingDate;
				fputcsv($file, $data);
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}
}
