<?php

namespace App\Http\Controllers;

use App\Agency;
use App\Master;
use App\Model\Language;
use App\ZipCode;
use Illuminate\Http\Request;
use App\Services\LogsService;
use App\Services\PatientService;
use App\Services\DocumentUploadService;
use Illuminate\Support\Facades\Session;
use App\Services\InsuranceMasterService;
use App\Services\AgencyWiseServiceService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use App\Services\UserWiseAgencyService;
use App\Helpers\Common;
use Illuminate\Support\Facades\URL;
use App\Model\SendPatientDemographicSms;
use App\Services\AgencyAllFormService;
use App\Helpers\AgencyAllForm;
use App\Services\CommonEsignService;
use App\Model\AgencyMaster;
use App\Model\SMSLogs;
use App\Helpers\Utility;
class PatientDemoGraphicController extends BaseController
{

	protected $PatientService, $DocumentPatientService, $insuranceMasterService, $documentUploadService, $userWiseAgencyService, $dynamicFormLogService,$AgencyWiseServiceService,$agencyAllFormService,$commonEsignService = "";

	public function __construct(PatientService $PatientService,InsuranceMasterService $insuranceMasterService, DocumentUploadService $documentUploadService, UserWiseAgencyService $userWiseAgencyService, AgencyWiseServiceService $AgencyWiseServiceService,AgencyAllFormService $agencyAllFormService,CommonEsignService $commonEsignService)
	{
		
		$this->PatientService = $PatientService;
		$this->insuranceMasterService = $insuranceMasterService;
		$this->documentUploadService = $documentUploadService;
		$this->userWiseAgencyService = $userWiseAgencyService;
        $this->AgencyWiseServiceService = $AgencyWiseServiceService;
        $this->agencyAllFormService = $agencyAllFormService;
        $this->commonEsignService = $commonEsignService;
	}

	public function patientEditWithSms($id){
		$data['patient'] = $this->PatientService->getDetailByIdEncrypt($id);
		
		if (isset($data['patient']->id)) {
			if($data['patient']->demographic_updated_flag ==1){
				return redirect('demographic-link-expire');
			}
			
            $data['id'] =$data['patient']->id;
			$data['agencyList'] = Agency::getAllAgencyList();
			$getAgencyWiseServiceList = $this->AgencyWiseServiceService->ServiceListNew($data['patient']->type, $data['patient']->agency_id);

			if (!empty($getAgencyWiseServiceList[0])) {

				foreach ($getAgencyWiseServiceList as $val) {
					$val->types = $val->type;
				}
				$data['serviceList'] = $getAgencyWiseServiceList;
			} else {
				$data['serviceList'] = Master::getServiceRequest();
			}

			$data['languages'] = Language::getLanguageList();
			$data['masterData'] = Master::getAllDataByMasterTypeFk(array(17, 26));
			$data['insuranceList'] = $this->insuranceMasterService->getInsuranceMasterList();
			
			return view('patient/patient_edit_with_sms', $data);
		} else {
			abort(404);
		}
	}

	public function patientUpdateWithSms(Request $request, $id)
	{        

		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'dob' => 'required',
			'mobile' => 'required|numeric|digits_between:10,15',
			'gender' => 'required',
			
			'state' => 'required',
			'city' => 'required',
			'zip_code' => 'required',
		]);
		if ($validator->fails()) {
			return redirect("/patient-edit-with-sms/$id")
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {
			$age = '';
			if (request('dob') != '') {
				$age = date('Y-m-d', strtotime(request('dob')));
			}
			$data = array(
				'first_name' => request('first_name'),
				'patient_code' => request('patient_code'),
				'middle_name' => request('middle_name'),
				'last_name' => request('last_name'),
				'email' => $request->email,
				'dob' => $age,
				'phone' => request('phone'),
				'mobile' => $request->input('mobile'),
				'gender' => request('gender'),
				
				'language' => Common::getOrCreateLanguageId($request->input('language')),
				'address1' => $request->input('address1'),
				'address2' => $request->input('address2'),
				'state' => $request->input('state'),
				'city' => $request->input('city'),
				'zip_code' => $request->input('zip_code'),
				'county' => $request->input('county'),
				'payment_type' => $request->input('payment_type'),
				
				'insurance_name' => $request->input('insurance_name'),
				'cin' => $request->input('cin'),
				'emergency_contact_name' => $request->emergency_contact_name,
				'emergency_phone' => $request->emergency_phone,
				'location_branch' => $request->location_branch,
				'ssn' => str_replace('-','',$request->ssn),
				
               
			);
			if ($request->input('insurance_name') == 'other') {
				$data['other_insurance_name'] = $request->other_insurance_name;
			}
			

			$getExistingData = $this->PatientService->getDetailByIdEncrypt($id); 
			$this->PatientService->update($data, array('id' => $request->id));
			$getNewData = $this->PatientService->getDetailByIdEncrypt($id);
			// $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();

			$insertLog = [
				'type' => 'Update Demo graphic Detail',
				'link' => url('patient-update-with-sms'),
				'module' => 'Patient Appointment',
				'object_id' => $id,
				'message' =>'Demographic detail has Updated',
				'new_response' => serialize($getNewData),
				'old_response' => serialize($getExistingData),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
				Session::flash('success', 'Demographic detail successfully update.');
				return redirect('/patient-demographic-details/'.sha1($request->id));
		}
	}

    public function getCountyByZipCode(Request $request)
	{
		$zip_code = $request['zip_code'];
		$data['getName'] = ZipCode::where('zip_code', $zip_code)->first();
		if ($data['getName'] != '' && !empty($data['getName'])) {
			echo $data['getName']->county;
		} else {
			echo "County not found";
		}
	}

	public function search(Request $request)
	{
		
		$data['menu'] = "Patient List";
		return view("patient_details/patient-details-list", $data);
	}

    public function ajaxList(Request $request){
        $data['patientData'] = array();
        $data['patientData'] = $this->PatientService->getpatientSearchData($request->name); 
        return view('patient_details.patient-details-ajax-list',$data);
    }

	public function sendPatientDemogeraphicUpdate(Request $request){
		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'mobile'=>'required'
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}else{
			$mobile  = str_replace(['(', '|', ')','-',' '],'',$request->mobile);
			$getExistingData = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->id); 
			$message = "Hello ".$getExistingData->first_name.' '.$getExistingData->last_name;
			$message .="  This message for Nybest Medical. please complete your profile. ";
			$message .= URL::to('/patient-edit-with-sms').'/'.sha1($request->id);
			$update = Common::sendTwillioSms($mobile,$message);

			
			if($update){

				$json = json_decode($update,true);
				$date = NULL;
				if(isset($json['date_updated'])){
					$date =date('Y-m-d H:i:s',strtotime($json['date_updated']));
				}

				$smsLogs = [ 
					'patient_id' => $request->id,
					'mobile_no' => $mobile,
					'sms' => $message,
					'send_sms_id' => $json['sid']??"",
					'send_sms_status' => $json['status']??"",
					'send_sms_updated_date' => $date
				];

				if(isset($auth['id'])){
					$smsLogs['created_by'] = $auth['id'];
				}
				$insert = new SMSLogs($smsLogs);
				$insert->save();

				$saveDetails = new SendPatientDemographicSms(array('patient_id'=>$request->id,'mobile'=>$mobile,'message'=>$message,'response'=>$update));
				$saveDetails->save();
				$insertId  = $saveDetails->id;

				return response()->json(['error_msg' => "SMS successfully send", 'status' => 0, 'data' => array()], 200);
			}else{
				return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
			}
		}
		
	}

	public function demographicLinkExpire(){
		return view('demographic_expire');
	}

	public function thankForDemo(Request $request){
		$data['patient_id'] = $request->patient_id;
		
		return view('thank_demographic',$data);
	}

	function downs(Request $request){
		$this->downloadPdf($request->patient_id);
	}
	public function viewDetails(Request $request,$id){
		$data['getExistingData'] = $this->PatientService->getDetailByIdEncryptWithRelationShip($id); 
		$data['id'] = $id;
		return view('demographic_page.view',$data);
	}

	public function submitEsign(Request $request){
		$this->PatientService->update(array('patient_image'=>$request->images), array('id' => $request->patient_id));
		return redirect('thank-you-demo?patient_id='.sha1($request->patient_id));

	}

	function caregiverFieldsResponse($formId="", $id, $keys, $agencyId)
    {
        $key = $keys;
        $user_id = $id;
        $explode  = explode('@', $key);

        $finalArray = array();
        if ($explode[0] == 'fm') {
            $caregiverDetails = AgencyAllForm::GetFormDetails($formId, $explode[1], $user_id);
        } else if ($explode[0] == 'dr') {
            $caregiverDetails = AgencyAllForm::GetDoctorDetails($formId, $explode[1], $user_id);
        } else if ($explode[0] == 'ag') {
            $caregiverDetails = AgencyAllForm::GetAgencyDetails($explode[1], $agencyId);
        } else {
            $caregiverDetails = $this->PatientService->GetCaregiverFormDetails($explode[1], $user_id);
        }

        if ($explode[1] == 'dob' || $explode[1] == 'date_of_examination') {
            $date = "";
            if ($caregiverDetails != "") {
                $date = date('m/d/Y', strtotime($caregiverDetails));
            }
            $finalArray[$key] = $date;
        } else {
            $finalArray[$key] = $caregiverDetails ?? "";
        }


        return $finalArray;
    }

	public function showOtherCheckBox($formId, $fieldId, $patient_id,$id)
    {
		
        $query = AgencyAllForm::GetFormDetails($formId, $fieldId, $patient_id,$id);

        $data = unserialize($query);

        $final = [];
        if(!empty($data[0])){
            foreach ($data as $val) {
                if ($val != 'null') {
                    $final[] = $val;
                }
            }
        }
        
        return $final;
    }

	public function downloadPdf($patient_id){
		$formId = 3;
        $templateId = 5;
		$test ="";
		$getExistingData = $this->PatientService->getDetailByIdEncrypt($patient_id); 
		
			
			$agencyId = $getExistingData->agency_id;
        
			$patient_id = $getExistingData->id;
			
			
			$templateDetails = $this->agencyAllFormService->getTemplate($templateId, $formId);
			$response = unserialize($templateDetails->response);
		
			$SubIntakeArray = [];
			if (isset($response) && $response != '') {
				$final_array[] = $templateDetails->docWidth;
				$data['docWidth'] = $templateDetails->docWidth;
				$data['sent_on'] = "Caregiver";
	
				foreach ($response as $val) {
					$final_array[] = $val;
					$Signinsert[] = $val;
					$max[] = $val['page'];
					$maxs = max($max);
	
					if($val['type'] =='image'){
						$val['text'] = $getExistingData->patient_image;
					}
					if (isset($val['placeHolder']) && $val['placeHolder'] != '') {
						$val['placeHolder'] = str_replace('%22', '', $val['placeHolder']);
					}
					if ($val['temp1'] == 'caregiver') {
						if ($val['temp3'] != '') {
							$subresponse = $this->caregiverFieldsResponse($formId, $patient_id, $val['temp3'], $agencyId);
							$val['text'] = $subresponse[$val['temp3']];
						}
					} else {
	
						$dynamicDropdownId = isset($val['dynamicDropdownId']) ? $val['dynamicDropdownId'] : "";
	
						if ($dynamicDropdownId != "") {
						
							$getData = AgencyMaster::where('form_id',$formId)->get();
							
							foreach($getData as $vk){
								$subresponse = $this->showOtherCheckBox($formId, $dynamicDropdownId, $patient_id, $vk->id);
							
								if (isset($val['normalValue'])) {
									if (in_array($val['normalValue'], $subresponse)) {
										$val['checked'] = 1;
									}
								}
							}
							
						}
					}
					$SubIntakeArray[] = $val;
				}
			}
		
		$test = $this->commonEsignService->downloadPDF($SubIntakeArray, $templateId);
		
		$this->PatientService->update(array('demographic_updated_flag'=>1,'patient_image'=>''), array('id' => $getExistingData->id));
	}
}