<?php
namespace App\Http\Controllers;

use App\Agency;
use App\Master;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use URL;
class PatientCalenderController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
		$data['user']=auth()->user();

		if(auth()->user()->agency_fk !=""){
			abort(404);
		}
		if (request('search_date')) {
			$data['searchDate'] = date("m/d/Y",  strtotime(request('search_date')));
		} else {
			$data['searchDate'] = '';
		}
		$data['emd_rep_id'] = request('emclist');
		$data['emd_rep_idss'] = explode(',', request('emclist'));
        $data['search_type']= request('search_type');
		$data['service_ids'] = explode(',', request('service_id'));
		$data['agencyList'] = Agency::getAgencyList2();
		$data['serviceList'] = Master::getServiceRequest();
        return view('patientCalender.patient_calender',$data);
    }
    public function calenderAjax(){	
		$data['user']=auth()->user();
		if (request('search_date')) {
			$data['searchDate'] = $searchDate =  date("m/d/Y",  strtotime(request('search_date')));
		} else {
			$data['searchDate'] = $searchDate = '';
		}
		$data['emd_rep_id'] = $agencyId = request('emclist');
		$data['emd_rep_idss'] = explode(',', request('emclist'));
		$data['service_ids'] = request('service_id')!=null ? implode(',', request('service_id')) :"";
		$teleHealth = PatientService::getCalenderList($searchDate,request('id'),request('service_id'),request('search_type'));
		$final_array = array();
		$tempArray = array();
		if(!empty($teleHealth)){
			foreach($teleHealth as $val){
				$tempArray['title'] =$val->first_name.' '.$val->middle_name.' '.$val->last_name;
				$tempArray['start'] =$val->telehealth_date_time;	
				$tempArray['status'] =$val->status;	
				$tempArray['type'] =$val->type;	
				$tempArray['url'] =URL::to('/').'/patient/view/'.$val->id;
			    $final_array[] = $tempArray;
			}
			
		}
        
		echo json_encode($final_array);
    }
}	