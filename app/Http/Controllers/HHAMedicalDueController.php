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
use URL;
use App\Services\HHAMedicalService;
use App\Services\PatientService;
use App\Master;
use App\Agency;


class HHAMedicalDueController extends BaseController
{

	public function __construct()
	{
	
	}

	public function index()
	{
		
	
		$this->dir = 'Outbox/';
		$this->connection = ssh2_connect('sftpprod.hhaexchange.com', 2222);
		ssh2_auth_password($this->connection, 'NYBestMedical', 'bD7tK$Sd2');
		$sftp = ssh2_sftp($this->connection);
		$this->sftp_fd = intval($sftp);	

		$handle = opendir("ssh2.sftp://$this->sftp_fd/Outbox");
		$readFolder = [];
		$originalFolderName="";
        while (false != ($entry = readdir($handle))) {

            echo $originalFolderName = $entry;
			echo "<br/>";
            
        }
		closedir($handle);
		if($originalFolderName!=""){
			$this->copyFileTolocal($originalFolderName,"");
		}


	

	}
	public  function copyFileTolocal($remoteFilePath, $localFilePath)
    {
        //echo $this->dir .'/'. $remoteFilePath ."----". public_path().'/'.$localFilePath;
        $basefile = $this->dir . '/' . $remoteFilePath;

      echo   $fopenStream = file_get_contents("ssh2.sftp://$this->sftp_fd/" . $basefile, 'r');
	  file_put_contents(public_path().'/upload/csvnybest/'.$remoteFilePath, $fopenStream);
		fclose($fopenStream);
		$this->Importcsv($remoteFilePath);
		
        //$fortiVoiceRecordingHelper->run();





    }
	
	function Importcsv($fileName){
		$path = public_path('/upload/csvnybest/'.$fileName);
		$import_data = array_map('str_getcsv', file($path));
		
		$cnt =0;
		$final_array = array();
		foreach($import_data as $val){
			if($cnt !=0){
				$final_array = array(
						'agency_id'=>isset($val[0])?$val[0]:"",
						'office_id'=>isset($val[1])?$val[1]:"",
						'caregiver_id'=>isset($val[2])?$val[2]:"",
						'medical_id'=>isset($val[3])?$val[3]:"",
						'medical_name'=>isset($val[4])?$val[4]:"",
						'status'=>isset($val[5])?$val[5]:"",
						'result'=>isset($val[6])?$val[6]:"",
						'due_date'=>isset($val[7])?$val[7]:"",
						'date_performed'=>isset($val[8])?$val[8]:"",
						'notes'=>isset($val[9])?$val[9]:"",
					
					
					);
					$updates = HHAMedicalService::save($final_array);
				
			}
			$cnt++;
			
		}
		die("Ok");
		
	}
	
	function getHHADetails(){
		$getDetails = HHAMedicalService::getMedicalDetails();
		
		if(!empty($getDetails[0])){
			foreach($getDetails as $vak){
				$json = $this->jsonDetails($vak->caregiver_id);
				if ($json === false) {
					
					$json = json_encode(array("jsonError", json_last_error_msg()));
					if ($json === false) {
						// This should not happen, but we go all the way now:
						$json = '{"jsonError": "unknown"}';
					}
					// Set HTTP response status code to: 500 - Internal Server Error
					http_response_code(500);
				} //End of $json
				else
				{
					
					$clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
					$xml = simplexml_load_string($clean_xml);
					
					if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID == 0){

						if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo))
						{ 
							$FirstName="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstName)){
								$FirstName=$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstName;
							}
							$LastName="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastName)){
								$LastName=$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastName;
							}
							$CaregiverCode="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverCode)){
								$CaregiverCode=$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverCode;
							}
							$Language1="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language1)){
								$Language1=$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language1;
							}
							$Status="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Status->Name)){
								$Status=$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Status->Name;
							}
							$Gender="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Gender)){
								$Gender=$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Gender;
							}
							$BirthDate="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->BirthDate)){
								$BirthDate = $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->BirthDate;
							}
							$MobileOrSMS="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->MobileOrSMS)){
								$MobileOrSMS = $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->MobileOrSMS;
							}
							$HomePhone="";
							if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->HomePhone)){
								$HomePhone = $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->HomePhone;
							}
							$data = array('caregiver_first_name'=>$FirstName,'caregiver_last_name'=>$LastName,'caregiver_code'=>$CaregiverCode,'language'=>$Language1,'hha_caregiver_status'=>$Status,'gender'=>$Gender,'caregiver_dob'=>$BirthDate,'mobile'=>str_replace('-','',$MobileOrSMS),'phone'=>str_replace('-','',$HomePhone));
							
							$update = HHAMedicalService::update($data,array('caregiver_id'=>$vak->caregiver_id));
						}
					}
				}
			}
		}
	}
	
	function jsonDetails($code){
		$xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  					<soap:Body>
	    					<GetCaregiverDemographics xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
		      					<Authentication>
		        					<AppName>NYBestMedical</AppName>
		        					<AppSecret>959cda02-5337-46b6-b74e-c4563199b6b4</AppSecret>
		        					<AppKey>MQAxADgANwAwADQALQAzADEARQAzAEYAOQBGADAANQA0AEIAMgA1ADIAOQA4AEUANQA4ADgANQA2ADEANwAzADUAQQAzADcAQgA=</AppKey>
		      					</Authentication>
		      					<CaregiverInfo>
		      					        <ID>'.$code.'</ID>
							</CaregiverInfo>		
		      				</GetCaregiverDemographics>
  					</soap:Body>
					</soap:Envelope>';
			
			$headers = array(
				"POST /Integration/ENT/V1.8/ws.asmx HTTP/1.1",
				"Host: app.hhaexchange.com",
				"Content-Type: text/xml;charset=utf-8",
				"Content-Length: ".strlen($xml_post_string),
				"SOAPAction: https://www.hhaexchange.com/apis/hhaws.integration/GetCaregiverDemographics"
		); 
		$url = "https://app.hhaexchange.com/Integration/ENT/V1.8/ws.asmx?op=GetCaregiverDemographics";
	
		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// Following line is compulsary to add as it is:
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		$json = curl_exec($ch);
		curl_close($ch);
		return $json;
		
	}
	
	
	public function AddPatientDetails(){
		/*$getDetails = HHAMedicalService::getDetailsWithCaregiverCode();
		
		$newarras =array();
			$finalarray =array();
			$vishalfinalArray = array();
			/*foreach ($importData_arr as $val) {
				if(isset($vishalfinalArray[$val[5]])){
					$vishalfinalArray[$val[5]]['serviceArray'][] = $val[8];
				}else{
					$val['serviceArray'][] =  $val[8];					
					$vishalfinalArray[$val[5]] = $val;
				}
			}
		foreach($getDetails->toArray() as $val){
			if(isset($vishalfinalArray[$val['caregiver_code']])){
				$vishalfinalArray[$val['caregiver_code']]['serviceArray'][] = $val['medical_name'];
			}else{
				$val['serviceArray'][] =  $val['medical_name'];					
				$vishalfinalArray[$val['caregiver_code']] = $val;
			}
			
		}
		
		$newss =array();
		$cnt =1;
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
		}*/
	}
}
