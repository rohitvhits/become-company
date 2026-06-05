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
use App\Record;
use Illuminate\Support\Facades\URL;
use App\User;
use App\Master;
use App\Agency;
use App\Model\AssignEMCRecord;
use App\Model\Patient;
use App\Services\PatientService;
use App\PDF;
use App\Services\LocationMasterService;
use App\Services\LocationScheduleService;
use App\Services\TaskService;
use App\Services\PatientNotesService;
use App\Services\UserService;

class DashboardCalanderController extends BaseController
{
	protected $locationScheduleService,$PatientService,$LocationMasterService,$TaskService,$patientNoteService,$userService="";
	public function __construct(PatientService $PatientService, LocationMasterService $LocationMasterService,TaskService $TaskService,LocationScheduleService $locationScheduleService,PatientNotesService $patientNoteService,UserService $userService)
	{
		$this->middleware('permission:calendar-list', ['only' => ['calenderHospital']]);

		$this->middleware('auth');
		$this->PatientService = $PatientService;
		$this->LocationMasterService = $LocationMasterService;
		$this->TaskService = $TaskService;
		$this->locationScheduleService = $locationScheduleService;
		$this->patientNoteService = $patientNoteService;
		$this->userService = $userService;
	}

	public function index(Request $request)
	{
	
		$data['menu'] = "user";
		$data['user'] = $user = auth()->user();
		$data['userList'] = $userList = User::select('id', 'first_name', 'last_name')->where('delete_flag', 'N')->whereIn('user_type_fk', array(3, 4))->orderBy('first_name', 'asc')->get();
		$data['emd_rep_id'] = request('emclist');
		return view("calender/calender", $data);
	}
	public function dashboard_calander()
	{
		$user = auth()->user();
		$emc = request('id');
		if (($user['user_type_fk'] == 3 || $user['user_type_fk'] == 4) && (isset($user['limit_access']) && ($user['limit_access'] != "Y"))) {

			$test = Record::getCalenderResponse($emc);
		} else {
			$test = Record::where('delete_flag', 'N')->where('agency_follow_date', '!=', '')->where('agency_fk', $user['agency_fk'])->get();
		}
		$final_array = array();
		$tempArray = array();
		if (!empty($test)) {
			foreach ($test as $val) {
				if ($user['user_type_fk'] == 3 || $user['user_type_fk'] == 4) {
					$tempArray['title'] = $val->first_name . ' ' . $val->last_name;
					$tempArray['start'] = $val->follow_date;
				} else {
					$tempArray['title'] = $val->first_name . ' ' . $val->last_name;
					$tempArray['start'] = $val->agency_follow_date;
				}

				$tempArray['url'] = URL::to('/') . '/record/' . $val->id;
				$final_array[] = $tempArray;
			}
		}
		echo json_encode($final_array);
	}
	public function calenderHospital()
	{
		$data['menu'] = "user";
		$data['user'] = $user = auth()->user();

		$data['userList'] = Agency::getAgencyList2();
		
		$data['locationList'] = $this->LocationMasterService->getAllList();
		$data['assignList'] = AssignEMCRecord::with('patient:id,first_name,last_name')->get();

		$data['emd_rep_id'] = request('emclist');
		$data['emd_rep_idss'] = explode(',', request('emclist'));

		$data['locationId'] = request('locationlist');
		$data['assignId'] = explode(',', request('assignlist'));
		$data['record_type'] = request('record_type');
		if (request('fudate')) {
			$data['fuDate'] = date("m/d/Y",  strtotime(request('fudate')));
		} else {
			$data['fuDate'] = '';
		}
		return view("calender/calender_hospital", $data);
	}

	public 	function calenderResponseHospital()
	{
	
		// die("sdf");
		//ini_set("memory_limit", "4096M");
		$user = auth()->user();
		
		$emc = request('id');
		$locId = request('loc_id');
		$assId = request('ass_id');
		
		$startDate =request('start');
		$endDate =request('end');

		if (request('fdt')) {
			$date = str_replace('-', '/', request('fdt'));
			$fdate = date("Y-m-d", strtotime($date));
		} else {
			$fdate = '';
		}
	// echo date("H:i:s");	
		$details = $this->PatientService->AllPatientListByAgencyFK($emc, $locId, $assId, $fdate,$startDate,$endDate,request('record_type'));
		$getTaskList = $this->TaskService->getTaskCalendar($fdate);


		$final_array = array();
		$tempArray = array();
		if (!empty($details)) {
			foreach ($details as $val) {

				$appointment_date =$val->appointment_date=="" ? $val->fu_date : $val->appointment_date;
				if(isset($val->appointment_date) && $val->appointment_date !=""){
					if(isset($val->appoinment_time_id) && $val->appoinment_time_id !=""){
						$date = date('Y-m-d',strtotime($val->appointment_date));
						$getScheduleDetails = $this->locationScheduleService->getDetailbyIdAll($val->appoinment_time_id);
						$startTime = isset($getScheduleDetails->start_time)?$getScheduleDetails->start_time:"00:00:00";
						$appointment_date = $date.' '.$startTime;
					}
				}
				

				$tempArray['title'] = $val->first_name . ' ' . $val->middle_name . ' ' . $val->last_name;
				$tempArray['start'] = $appointment_date;
				$tempArray['status'] = $val->status;
				$tempArray['type'] = $val->type;

				$tempArray['url'] = URL::to('/') . '/patient/view/' . $val->id;
				$final_array[] = $tempArray;
				
			}
		}

		if (!empty($getTaskList)) {
			foreach ($getTaskList as $getTask) {
				$tempArray['title'] = $getTask->task_name;
				$tempArray['start'] = $getTask->due_date;
				$tempArray['type'] = 'task';
				$tempArray['url'] = URL::to('/') . '/tasks/task-list/' . $getTask->id;
				$final_array[] = $tempArray;
			}
		}
		echo json_encode($final_array);
	}

	function generatePdf(Request $request)
	{
		$start_date = $request->input('start_date');
		$end_date = $request->input('end_date');
		$agency_id = $request->input('agency_id');

		$query = $this->PatientService->getSearchingByDate($agency_id, $start_date, $end_date);
		$fins = array();
		if (count($query) > 0) {
			foreach ($query as $vl) {
				$servicename = '';

				if ($vl->service_id != '') {
					$getServiceName = Master::geServiceName($vl->service_id);
					if (count($getServiceName) > 0) {
						foreach ($getServiceName as $vsl) {
							$fins[] = $vsl->name;
						}
					}
					$servicename = implode(',', $fins);
				}

				$vl->serviceName = $servicename;
			}
		}
		$data['query'] = $query;

		$pdf = new PDF(null, 'px');
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->AddPage();


		$pdf->writeHTML(view('calender.generate_pdf', $data));
		$filename = time() . '.pdf';
		$pdf->output($filename, 'D');
		header('Content-type: application/pdf');

		header('Content-Disposition: inline; filename="' . $filename . '"');

		header('Content-Transfer-Encoding: binary');

		header('Accept-Ranges: bytes');

		// Read the file 
		@readfile($file);
	}

	function recentNotes(Request $request){
		// $userList = $this->userService->getAgencyUserList();
	
		// $response =$this->patientNoteService->getRecentNotesByAgencyUser($userList,$request->patient_id);
		// if(!empty($response[0])){
		// 	foreach($response as $val){
		// 		$val->created_date = date('m/d/Y h:i A',strtotime($val->created_date));
		// 	}
		// }
		return  response()->json(['status'=>true,'data'=>[]]);
	}
}
