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
use URL;
use App\User;
use App\Agency;
use App\Services\PatientService;
use App\Master;
class PatientReportController extends BaseController
{

    public function __construct(PatientService $PatientService)
    {
        $this->middleware('auth');
		$this->PatientService = $PatientService;
    }
	
	function index(Request $request){
		$data['user'] = $user = auth()->user();
		$agency_fk = $data['agency_fk'] = request('agency_fk');
		$created_date = $data['created_date'] = request('created_date');
		$status = $data['status'] = request('status');
		  $data['agencyList'] = Agency::where('delete_flag', 'N')->where('service_md_appointment',1)->orderBy('agency_name', 'asc')->get();
		$query = $this->PatientService->getPatientReport($agency_fk,$created_date,$status);
		foreach($query as $vsl){
			$explode = explode(',',$vsl->service_id);
			$newss = "".$vsl->service_id;
			$sins = Master::select('name')->whereRaw('id  IN ('.$newss.')')->where('del_flag','N')->get();
			
			$nrens = array();
			foreach($sins as $names){
				$nrens[$vsl->id][] = $names->name;	
			}
			$vsl->name ='';
			if(isset($nrens[$vsl->id]) && $nrens[$vsl->id] !=''){
				$vsl->name = implode(',',$nrens[$vsl->id]);
			}
			
			
		}
		$data['query'] = $query;
		
		return view('patient_report/patient_report_list',$data);
	}
	
	function patientExport(Request $request){
		$agency_fk = $data['agency_fk'] = request('agency_fk');
		$created_date = $data['created_date'] = request('created_date');
		$status = $data['status'] = request('status');
		$users = $this->PatientService->getPatientReportExport($agency_fk,$created_date,$status);
		 $filename = 'Patient' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $columns = array('Agency Name', 'Doctor Name','Type' ,'Full Name', 'Phone', 'Gender', 'Dob', 'Location','Appointment Date', 'Appointment Start Time','Appointment End Time', 'Service','Status', 'Notes');
		
		$newass  =array();
        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $list) {
                $date = '';
                if ($list->dob != '0000-00-00' && $list->dob != '') {
                    $date = Utility::convertMDY($list->dob);
                }
                $Adate = '';
                if ($list->appointment_date != '0000-00-00 00:00:00' && $list->appointment_date != '') {
                    $Adate = Utility::convertMDY($list->appointment_date);
                }
                $ATime  = '';
                if ($list->start_time != '' ) {
                    $ATime = date('h:i A', strtotime($list->start_time));
                }

				$eTime  = '';
                if ($list->end_time != '' ) {
                    $eTime = date('h:i A', strtotime($list->end_time));
                }
				$servie = '';
				if(isset($list->service_id) && $list->service_id !=''){
					$services = Master::whereRaw('id IN ('.$list->service_id.')')->where('del_flag','N')->get();
						
						foreach($services as $kke){
							$newass[] = $kke->name;
						}
						
						if(!empty($newass)){
							$servie = implode(',',$newass);
						}
				}

                fputcsv($file, array($list->agency_name, $list->full_name, $list->type,$list->first_name . ' ' . $list->middle_name . ' ' . $list->last_name, $list->phone, $list->gender,$date, $list->address1.' '.$list->city ,$Adate, $ATime, $servie ,$list->status, $list->remarks));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
	}
	
}