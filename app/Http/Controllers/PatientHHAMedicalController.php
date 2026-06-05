<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Redirect;
use URL;
use App\Services\HHAMedicalService;
use App\Services\PatientService;
use App\Master;
use App\Agency;
use Illuminate\Support\Facades\Validator;

class PatientHHAMedicalController extends BaseController
{

	public function __construct()
	{
	
	}

	public function index(Request $request)
	{
		$pages = $request->input('page');
		$pagesnew = 1;
		if($pages !=''){
			$pagesnew = $pages;
		}
		$data['page'] = $pagesnew;
		
		$data['code'] = $code = $request->input('code');
		$data['fname'] = $fname = $request->input('fname');
		$data['lname'] = $lname = $request->input('lname');
		$data['dob'] = $dob = $request->input('dob');
		$data['phone'] = $phone = $request->input('phone');
		$data['mobile'] = $mobile = $request->input('mobile');
		$data['gender'] = $gender = $request->input('gender');
		$data['language'] = $language = $request->input('language');
		$data['service_name'] = $service_name = $request->input('service_name');
		$data['service_exp'] = $service_exp = $request->input('service_exp');
		$data['query'] = HHAMedicalService::getPatientRecordDetails($code,$fname,$lname, $dob,$phone,$mobile, $gender,$language,$service_name,$service_exp);
		return view('PatientHHAMedical.patient_hha_medical_list',$data);
	}
	
	public function ajaxList(Request $request){
		$data['page'] = $request->input('page');
		$data['query'] = HHAMedicalService::getPatientRecordDetails();
		return view('PatientHHAMedical.patient_hha_medical_ajax',$data);
		
	}
	
	public function addPatient(Request $request){
		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all(), 'status' => 0, 'data' => array()],200);
		} else {
			$vishalfinalArray = array();
			$tserviceArray = array();
			$finalServiceArray = array();
			if(!empty($request->input('id')[0])){
				foreach($request->input('id') as $val){
					$getDetailsById = HHAMedicalService::getDetailsById($val);
					
					$getServices = HHAMedicalService::getCaregiverCodeServices($getDetailsById->caregiver_code);
					foreach($getServices as $services){
						$tserviceArray[$getDetailsById->caregiver_code][] = $services->medical_name;
						$finalServiceArray[$getDetailsById->caregiver_code] = $tserviceArray[$getDetailsById->caregiver_code];
					}
					
					$getDetailsById->serviceArray = $finalServiceArray[$getDetailsById->caregiver_code];
					$vishalfinalArray[$getDetailsById->caregiver_code] = $getDetailsById;
				}
				$newss =array();
				$cnt =1;
				$insert = 0;
				foreach($vishalfinalArray as $key=>$val){
					$checkExistingCode = PatientService::countExistingCaregivercode($val['caregiver_code']);
					
					if($checkExistingCode ==0){
						$uns =uniqid();
						foreach($val['serviceArray'] as $ls){
							$query = Master::where('del_flag', 'N')->whereRaw('name ="' . $ls . '"')->first();
								if (isset($query->id) && $query->id != '') {
								$serviceId = $query->id;
								$newss[$key][] = $serviceId;
							} else {
								$masters = array('name' => $ls, 'master_type_fk' => 11, "types"=>"Caregiver",'del_flag' => 'N', 'user_id' => $auth['id'], 'created_at' => date('Y-m-d H:i:s'));
								$inserty = new Master($masters);
								$inserty->save();
								$serviceId = $inserty->id;
								$newss[$key][] = $serviceId;
							}
							
						}
						$getAgencyName = Agency::getDetailsByAgencyId(35);
						$agencyname = '';
						if (isset($getAgencyName->agency_name) && $getAgencyName->agency_name != '') {
							$agencyname = $getAgencyName->agency_name;
						}
						
						//$unitId = $query->key;
						$unitId = uniqid().$cnt; 
						$url = URL::to('/') . '/ap/' . $unitId;
						$message = 'Notice from ' . $agencyname . ' your ' . implode(',',$val['serviceArray']) . ' expiring on ' . date('m/d/Y', strtotime($val['due_date'])) . ' please click the link below to schedule your appointment Link ' . $url .' . Do not reply to this text message for any questions please call (718) 972-3693 ';
						
						$final_array = array('agency_id' => 35, 'type' => "Caregiver", 'first_name' => $val['caregiver_first_name'], 'last_name' => $val['caregiver_last_name'], 'dob' =>$val['caregiver_dob'], 'phone' => $val['phone'], 'mobile' => $val['mobile'], 'gender' => strtolower($val['gender']), 'language' => $val['language'], 'service_id' => implode(',',$newss[$key]), 'service_expiry_date' => date('Y-m-d', strtotime($val['due_date'])), 'patient_code'=>$val['caregiver_code'],'sms' => $message, 'key' => $unitId,'notes'=>$val['notes'],'hha_flag'=>'Y');
						
						
							 $insert = PatientService::hhasave($final_array);
						HHAMedicalService::update(array('patient_record_id'=>$insert),array('caregiver_code'=>$val['caregiver_code']));
						$cnt++;
					}
				}
				if($insert){
					return response()->json(['error_msg' => "Patient appointment successfully added.", 'status' => 1, 'data' => array()],200);
				}else{
					return response()->json(['error_msg' =>'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()],200);
				}
			}
			
		}
		
	}
	
}