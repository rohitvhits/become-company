<?php

namespace App\Helpers;

use App\Agency;
use App\Model\HHAPatient;
use App\Model\HHAPatientVisit;
use App\Model\DocumentPatient;
use App\Model\HHACaregivers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class HHAPatientHelper
{
    protected const STATIC_AGENCY_ID =2;
    public function __construct()
    {
    }


    public static function getData($xml, $action)
    {
       
        $headers = array(
            "POST /Integration/ENT/V1.8/ws.asmx HTTP/1.1",
            "Host: app.hhaexchange.com",
            "Content-Type: text/xml;charset=utf-8",
            "Content-Length: " . strlen($xml),
            "SOAPAction: https://www.hhaexchange.com/apis/hhaws.integration/" . $action
        );

        $url = "https://app.hhaexchange.com/Integration/ENT/V1.8/ws.asmx?op=" . $action;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        $json = curl_exec($ch);

        return $json;
    }

    public static function getUnSyncPatient()
    {
      $test =   SELF::updateOfficeId();
       
    }

    public static function updateOfficeId()
    {

        $patientList = HHAPatient::whereNull('officeId')->inRandomOrder()->limit(1000)->get();
     
        foreach ($patientList as $list) {
            $patient = HHAPatient::whereNull('officeId')->where('id', $list->id)->first();
            if ($patient) {
                $patientList = HHAPatient::where('id', $list->id)
                    ->update(array("hha_sync" => 'Y', "hhasyncdatetime" => date('Y-m-d H:i:s')));
                if ($list->agency_fk != "") {
                    SELF::GetPatientDetailByPatientidID($list->patient_id, $list->agency_fk);
                } else {
                    echo "No office id";
                }
                // die();
            }
        }
    }

    public static function GetPatientDetailByPatientidID($PatientId, $agencyID,$flag="")
    {


        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);

        

        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><GetPatientDemographics xmlns="https://www.hhaexchange.com/apis/hhaws.integration"><Authentication><AppName>' . $agencyHHADetail->app_name . '</AppName><AppSecret>' . $agencyHHADetail->app_key . '</AppSecret><AppKey>' . $agencyHHADetail->app_token . '</AppKey></Authentication><PatientInfo><ID>'.$PatientId.'</ID></PatientInfo></GetPatientDemographics></soap:Body></soap:Envelope>';

        if($agencyID == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'GetPatientDemographics', $agencyHHADetail->agency_id);
        }else{
            $json = SELF::getData($xml, 'GetPatientDemographics', $agencyHHADetail->agency_id);
        }
        

        $patientContectList = HHAPatient::where("agency_fk", $agencyID)
            ->where('patient_id', $PatientId)
            ->update(array("hha_sync" => 'Y'));

        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
          

        $patientDetailInfo = $xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo ?? '';
            if (isset($xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo)) {
                    $patientDetailInfo = $xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo;
                   
                    // $agencyID = (array)$patientDetailInfo->AgencyID;
                    $officeID = (array)$patientDetailInfo->OfficeID;
                    $firstName = (array)$patientDetailInfo->FirstName;
                    $middleName = (array)$patientDetailInfo->MiddleName;
                    $lastName = (array)$patientDetailInfo->LastName;
                    $dob = (array)$patientDetailInfo->BirthDate;
                    $gender = (array)$patientDetailInfo->Gender;

                    

                    $coordinatorId ="";
                    $coordinatorName ="";
                    if (isset($xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo->Coordinators->Coordinator[0]->ID)) {
                        $coordinatorDetails = $xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo->Coordinators->Coordinator[0];
                        $coordinatorId =(array)$coordinatorDetails->ID;
                        $coordinatorName =(array)$coordinatorDetails->Name;
                    }

                    $coordinator_id = $coordinatorId;
                    $coordinator_name = $coordinatorName;
                    $service_start_date = (array)$patientDetailInfo->ServiceRequestStartDate;
                    $admission_id = (array)$patientDetailInfo->AdmissionID;
                    $medicaid_number = (array)$patientDetailInfo->MedicaidNumber;
                    $medicare_number = (array)$patientDetailInfo->MedicareNumber;
                    $address1 ="";
                    $address2 ="";
                    $cross_street ="";
                    $city ="";
                    $zip5 ="";
                    $zip4 ="";
                    $state ="";
                    $county ="";
                    $home_phone ="";
                    $phone2 ="";
                    $phone3 ="";
                    $discipline ="";
                   
                    if (isset($patientDetailInfo->AcceptedServices->Discipline)) {
                        $discipline =(array)$patientDetailInfo->AcceptedServices->Discipline;
                    }
                   
                    if (isset($patientDetailInfo->Addresses->Address)) {
                        $addressDetails = $patientDetailInfo->Addresses->Address;
                    

                        $address1 =(array)$addressDetails->Address1;
                        $address2 =(array)$addressDetails->Address2;
                        $cross_street =(array)$addressDetails->CrossStreet;
                        $city =(array)$addressDetails->City;
                        $zip5 =(array)$addressDetails->Zip5;
                        $zip4 =(array)$addressDetails->Zip4;
                        $state =(array)$addressDetails->State;
                        $county =(array)$addressDetails->County;
                    }

                    $home_phone =(array)$patientDetailInfo->HomePhone;
                    
                    $phone2 =(array)$patientDetailInfo->Phone2;
                 
                    
                    $updateArray = array(
                        "first_name" => isset($firstName[0]) ? $firstName[0] : "",
                        "last_name" => isset($lastName[0]) ? $lastName[0] : "",
                        'middle_name' => isset($middleName[0]) ? $middleName[0] : "",
                        'gender' => isset($gender[0]) ? $gender[0] : "",
                        'dob' => isset($dob[0]) ? $dob[0] : "",
                        'admission_id' => isset($admission_id[0]) ? $admission_id[0] : "",
                        'coordinator_id' => isset($coordinator_id[0]) ? $coordinator_id[0] : "",
                        'coordinator_name' => isset($coordinator_name[0]) ? $coordinator_name[0] : "",
                        'service_start_date' => isset($service_start_date[0]) ? date('Y-m-d',strtotime($service_start_date[0])) : "",
                        'medicaid_number' => isset($medicaid_number[0]) ? $medicaid_number[0] : "",
                        'medicare_number' => isset($medicare_number[0]) ? $medicare_number[0] : "",
                        'address1' => isset($address1[0]) ? $address1[0] : "",
                        'address2' => isset($address2[0]) ? $address2[0] : "",
                        'cross_street' => isset($cross_street[0]) ? $cross_street[0] : "",
                        'city' => isset($city[0]) ? $city[0] : "",
                        'zip5' => isset($zip5[0]) ? $zip5[0] : "",
                        'state' => isset($state[0]) ? $state[0] : "",
                        'county' => isset($county[0]) ? $county[0] : "",
                        'home_phone' => isset($home_phone[0]) ? str_replace('-', '', $home_phone[0]) : "",
                        'phone2' => isset($phone2[0]) ? str_replace('-', '',$phone2[0]) : "",
                        'officeId' =>isset($officeID[0]) ? $officeID[0] : "",
                        "hha_sync" => "Y",
                        "hhasyncdatetime" => date('Y-m-d H:i:s'),
                        "EmploymentTypesDiscipline" => isset($discipline[0]) ? $discipline[0] : ""
                    );
                    if($flag =='manual'){
                        $updateArray['hha_delete_flag'] = 'N';
                    }
                    //print_r($updateArray); die("sfsdfdf");   

                   

                    $patientUpdate = HHAPatient::where("agency_fk", $agencyID)
                        ->where('patient_id', $PatientId)
                        ->update($updateArray);

                    if ($patientUpdate) {
                    } else {
                        echo "<h1>Sync Process Done</h1>";
                    }
                }
            }
        }
    }
    
    public static function getPatientIDListByAgencyId($agencyID)
    {

        $agencyDetails = Agency::getHHAAgencyList();
        
        if ($agencyID > 0) {

            $agencyDetails = Agency::where('id', $agencyID)->get();

        }

        foreach ($agencyDetails as $agency) {
            
            if (!empty($agency->app_name) && !empty($agency->app_key)  && !empty($agency->app_token)) {
                $flagUpdate = HHAPatient::updateData(array('hha_delete_flag' => 'Y'), array('agency_fk' => $agency->id));
                $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                                <SearchPatients xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $agency->app_name . '</AppName>
                                        <AppSecret>' . $agency->app_key . '</AppSecret>
                                        <AppKey>' . $agency->app_token . '</AppKey>
                                    </Authentication>
                                    <SearchFilters>
                                            <Status>Active</Status>
                                </SearchFilters>    
                                </SearchPatients> 
                        </soap:Body>
                        </soap:Envelope>';
               
                $json = SELF::getData($xml, 'SearchPatients');
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);

                    if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID)) {
                            $cnt_Patients = count($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID);
                            for ($i = 0; $i < $cnt_Patients; $i++) {
                                $PatientID = (array)$xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID[$i];
                              
                                HHAPatient::updateOrCreate(["agency_fk"  => $agency->id, "patient_id" => $PatientID[0]], [ 'hha_delete_flag' => 'N']);
                               
                            } 
                        }
                    }
                }
                
            }
        }
    }

    public static function getVisitDetails($details,$startDate,$endDate){
        $final = [];

  
        if(isset($details->agencyDetail->app_name)){
           
            $AppName = $details->agencyDetail->app_name;
            $AppSecret = $details->agencyDetail->app_key;
            $AppKey = $details->agencyDetail->app_token;
            $getExistingVisitId = HHAPatientVisit::select('visit_id')->where('del_flag', 'N')->get();
            $currentVisitIds = [];
            foreach ($getExistingVisitId as $val) {
                $currentVisitIds[] = $val->visit_id;
            }
            
            $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <SearchVisits xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>' . $AppName . '</AppName>
                                    <AppSecret>' . $AppSecret . '</AppSecret>
                                    <AppKey>' . $AppKey . '</AppKey>
                            </Authentication>
                            <SearchFilters>
                            <StartDate>' . $startDate . '</StartDate>
                            <EndDate>' . $endDate . '</EndDate>
                           
                                    <PatientID>' . $details->patient_id . '</PatientID>
                            </SearchFilters>
                    </SearchVisits>
            </soap:Body>
            </soap:Envelope>';
    
            $json = Self::getData($xml_post_string, 'SearchVisits');
          
            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                // Set HTTP response status code to: 500 - Internal Server Error
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
          
                $visitIDs = array();
                if (isset($xml->Body->SearchVisitsResponse->SearchVisitsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchVisitsResponse->SearchVisitsResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits)) {
                        $respoe = count($xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits->VisitID);
    
                        for ($i = 0; $i < $respoe; $i++) {
    
                            $visitID = $xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits->VisitID[$i];
                            $visitIDs[] = addslashes($visitID);
                          //$final[] =  SELF::getScheduleUpdate(addslashes($visitID), $AppName, $AppSecret, $AppKey);
                        }
                    }
                }
                $remainingIds = array_diff($visitIDs, $currentVisitIds);
    
                foreach ($visitIDs as $visit) {
                   $final[] =  SELF::getScheduleUpdate($visit, $AppName, $AppSecret, $AppKey);
                }
            }
        }
   
        return $final;
    }

    public static function getScheduleUpdate($visitId, $AppName, $AppSecret, $AppKey)
    {

        $finalData = [];

        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetScheduleInfo xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                            <AppName>' . $AppName . '</AppName>
                            <AppSecret>' . $AppSecret . '</AppSecret>
                            <AppKey>' . $AppKey . '</AppKey>
                            </Authentication>
                            <ScheduleInfo>
                                    <ID>' . $visitId . '</ID>
                            </ScheduleInfo>
                    </GetScheduleInfo>
            </soap:Body>
        </soap:Envelope>';

        $json = Self::getData($xml_post_string, 'GetScheduleInfo');

        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);

            if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->Result->ErrorInfo->ErrorID == 0) {

                if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo)) {
                    $PatientId = "";
                    $VisitDate = "";
                    $AdmissionNumber = "";
                    $FirstName = "";
                    $LastName = "";
                    $CaregiverId = "";
                    $CaregiverCode = "";
                    $StartTime = "";
                    $EndTime = "";
                    $cFirstName = "";
                    $cLastName = "";
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->FirstName)) {
                        $cFirstName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->FirstName;
                    }

                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->LastName)) {
                        $cLastName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->LastName;
                    }
 
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->VisitDate)) {
                        $VisitDate = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->VisitDate;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->ID)) {
                        $PatientId = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->ID;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->AdmissionNumber)) {
                        $AdmissionNumber = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->AdmissionNumber;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->FirstName)) {
                        $FirstName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->FirstName;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->LastName)) {
                        $LastName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->LastName;
                    }

                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->ID)) {
                        $CaregiverId = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->ID;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->CaregiverCode)) {
                        $CaregiverCode = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->CaregiverCode;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleStartTime)) {
                        $StartTime = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleStartTime;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleEndTime)) {
                        $EndTime = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleEndTime;
                    }

                    $finalData = array(
                        'visit_id' => addslashes($visitId),
                        'patient_id' => addslashes($PatientId),
                        'visit_date' => addslashes($VisitDate),
                        'first_name' => addslashes($FirstName),

                        'last_name' => addslashes($LastName),
                        'admission_id' => addslashes($AdmissionNumber),
                        'caregiver_id' => addslashes($CaregiverId),
                        'caregiver_code' => addslashes($CaregiverCode),
                        'schedule_start_time' => addslashes($StartTime),
                        'schedule_end_time' => addslashes($EndTime),
                        'demographic_update_flag' => 'Y',
                        'caregiver_first_name' => addslashes($cFirstName),
                        'caregiver_last_name' => addslashes($cLastName),
                       
                       // 'caregiver_name' => $cFirstName.' '.$cLastName,
                        
                        'created_date' => date('Y-m-d H:i:s')
                    );
             
                  
                    // $save = HHAPatientVisit::updateOrCreate([
                    //     'visit_id'   => addslashes($visitId),
                    //     'patient_id'   => addslashes($PatientId),
                    // ],$finalData);
                   
                }
            }

          
            return $finalData;
        }
    }
    
    public static function getPatientDocumentType($agencyID)
    {
        $data_Array = array();
        $agencyHHADetail = self::commonAgencyDetails($agencyID);

        $xml = '<soap:Envelope
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xmlns:xsd="http://www.w3.org/2001/XMLSchema"
		xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
			<GetPatientDocumentType
				xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
				<Authentication>
				<AppName>' . $agencyHHADetail->app_name . '</AppName>
				<AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
				<AppKey>' . $agencyHHADetail->app_token . '</AppKey>
				</Authentication>
				<Status>ACTIVE</Status>
			</GetPatientDocumentType>
		</soap:Body>
	</soap:Envelope>';
    if(self::STATIC_AGENCY_ID == $agencyID){
        $json = SELF::getDataDemo($xml, 'GetPatientDocumentType');
    }else{
        $json = SELF::getData($xml, 'GetPatientDocumentType');
    }
       
        if ($json === false) {
            // Avoid echo of empty string (which is invalid JSON), and
            // JSONify the error message instead:
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);

            if (isset($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes->DocumentType)) {
                    $tempValue = $xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes->DocumentType;
                    $respoe = count($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes->DocumentType);
                   
                    $temparray = array();
                    for ($i = 0; $i < $respoe; $i++) {
                        $caregiverDocumentTypeID = '';

                        if (isset($tempValue[$i]->PatientDocumentTypeID) && $tempValue[$i]->PatientDocumentTypeID != '') {
                            $caregiverDocumentTypeID = $tempValue[$i]->PatientDocumentTypeID;
                            $temparray['id'] = (int)$caregiverDocumentTypeID;
                        }
                        $caregiverDocumentType = '';
                        if (isset($tempValue[$i]->PatientDocumentType) && $tempValue[$i]->PatientDocumentType != '') {
                            $caregiverDocumentType = $tempValue[$i]->PatientDocumentType;
                            $temparray['name'] = (string)$caregiverDocumentType;
                        }
                        $status = '';
                        if (isset($tempValue[$i]->Status) && $tempValue[$i]->Status != '') {
                            $status = $tempValue[$i]->Status;
                            $temparray['Status'] = (string)$status;
                        }

                        $data_Array[] = $temparray;
                    }
                }
            }

            return $data_Array;
        }
    }

    public static function getSendHHADocument($agencyId, $medicalName, $extension, $document_type, $patientId, $file, $uploadDocId="")
    {

        $medicalName = str_replace('/', '_', $medicalName);
        $medicalName = str_replace(' ', '_', $medicalName);
        $medicalName = str_replace('-', '_', $medicalName);
        $medicalName = str_replace(':', '', $medicalName);

        $agencyHHADetail = self::commonAgencyDetails($agencyId);
      
        $xml = '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <AddPatientDocument
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                            <AppName>' . $agencyHHADetail->app_name . '</AppName>
                            <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                            <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                    <PatientDocumentInfo>
                        <PatientID>' . $patientId . '</PatientID>
                        <PatientDocumentTypeID>' . $document_type . '</PatientDocumentTypeID>
                        <Description>' . $medicalName . '</Description>
                        <FileName>' . $medicalName . '.' . $extension . '</FileName>
                        <StreamData>' . base64_encode($file) . '</StreamData>
                    </PatientDocumentInfo>
                </AddPatientDocument>
            </soap:Body>
        </soap:Envelope>';

        if(self::STATIC_AGENCY_ID == $agencyId){
            $json = SELF::getDataDemo($xml, 'AddPatientDocument');
        }else{
            $json = SELF::getData($xml, 'AddPatientDocument');
        }

        if ($json === false) {
            // Avoid echo of empty string (which is invalid JSON), and
            // JSONify the error message instead:
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {

            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
           
            if (isset($xml->Body->AddPatientDocumentResponse->AddPatientDocumentResult->Result->ErrorInfo->ErrorID) && $xml->Body->AddPatientDocumentResponse->AddPatientDocumentResult->Result->ErrorInfo->ErrorID == 0) {
                $caregiverDocumentUpload = '';
                if (isset($xml->Body->AddPatientDocumentResponse->AddPatientDocumentResult->PatientDocID) && $xml->Body->AddPatientDocumentResponse->AddPatientDocumentResult->PatientDocID != '') {
                    $caregiverDocumentUpload = $xml->Body->AddPatientDocumentResponse->AddPatientDocumentResult->PatientDocID;
                }

                if($uploadDocId !=""){
                    DocumentPatient::where('id', $uploadDocId)->update(array('hha_document_id' => $caregiverDocumentUpload));
                }
                return ['status' => 1, 'message' => 'Success'];
            }
            return ['status' => 0, 'message' => $xml->Body->AddPatientDocumentResponse->AddPatientDocumentResult->Result->ErrorInfo->ErrorMessage[0]];
        }
    }

    public static function getPatientIdByAdmissionId($id,$agencyId){
        $final = [];
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
       
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <SearchPatients
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                    <AppName>'.$agencyHHADetail->app_name.'</AppName>
                    <AppSecret>'.$agencyHHADetail->app_key.'</AppSecret>
                    <AppKey>'.$agencyHHADetail->app_token.'</AppKey>
                </Authentication>
                <SearchFilters>
                    <FirstName></FirstName>
                    <LastName></LastName>
                    <Status>-1</Status>
                    <PhoneNumber></PhoneNumber>
                    <AdmissionID>'.$id.'</AdmissionID>
                    <MRNumber></MRNumber>
                    <SSN></SSN>
                </SearchFilters>
            </SearchPatients>
        </soap:Body>
    </soap:Envelope>';


        $json = SELF::getData($xml, 'SearchPatients');

      
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
            if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients)) {
                    $patientId = $xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID;

                   $final =  self::getVisitTenDays($patientId,$agencyId,$id);
                }

            }
        }

        return $final;
    }

    public static function getVisitTenDays($pid,$agencyId,$extId){
        $final = [];
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d',strtotime('+10 days',strtotime($startDate)));
       
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
       
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <SearchVisits
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                    <AppName>'.$agencyHHADetail->app_name.'</AppName>
                    <AppSecret>'.$agencyHHADetail->app_key.'</AppSecret>
                    <AppKey>'.$agencyHHADetail->app_token.'</AppKey>
                </Authentication>
                <SearchFilters>
                    <StartDate>'.date('Y-m-d').'</StartDate>
                    <EndDate>'.$endDate.'</EndDate>
                    <CaregiverID xsi:nil="true" />
                    <PatientID>'.$pid.'</PatientID>
                    <OfficeID xsi:nil="true" />
                </SearchFilters>
            </SearchVisits>
        </soap:Body>
    </soap:Envelope>';


        $json = SELF::getData($xml, 'SearchVisits');
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);

            if (isset($xml->Body->SearchVisitsResponse->SearchVisitsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchVisitsResponse->SearchVisitsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits->VisitID)) {
                    $visitIds = count($xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits->VisitID);

                    $tempValue = $xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits->VisitID;

                    for ($i = 0; $i < $visitIds; $i++) {
                     

                     $temp =  self::getHHAVisitDetails($tempValue[$i],$agencyHHADetail->app_name,$agencyHHADetail->app_key,$agencyHHADetail->app_token,$extId);
                     if(!empty($temp)){
                        $final[] = $temp;
                     }   
                     
                    }
                }
            }
        }
        return $final;
    }

    public static function getHHAVisitDetails($visitId,$AppName,$AppSecret,$AppKey,$extId){

        $finalArray = [];
        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetScheduleInfo xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                            <AppName>' . $AppName . '</AppName>
                            <AppSecret>' . $AppSecret . '</AppSecret>
                            <AppKey>' . $AppKey . '</AppKey>
                            </Authentication>
                            <ScheduleInfo>
                                    <ID>' . $visitId . '</ID>
                            </ScheduleInfo>
                    </GetScheduleInfo>
            </soap:Body>
        </soap:Envelope>';

        $json = Self::getData($xml_post_string, 'GetScheduleInfo');

        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
          
            $tempArry = [];
            if(isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->Result->ErrorInfo->ErrorID ==0){
              
                $getCaregiverDetails =HHACaregivers::where('caregiver_id',addslashes($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->ID))->first();

                if(isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo)){
                    $CaregiverCode = '';
                  
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->CaregiverCode)) {
                        $CaregiverCode = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->CaregiverCode;
                    }

                    $FirstName = '';
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->FirstName)) {
                        $FirstName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->FirstName;
                    }

                    $LastName = '';
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->LastName)) {
                        $LastName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->LastName;
                    }

                    $patientHHAXAdmissionId = '';
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->patientHHAXAdmissionId)) {
                        $patientHHAXAdmissionId = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->patientHHAXAdmissionId;
                    }

                    $ScheduleStartTime = '';
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleStartTime)) {
                        $ScheduleStartTime = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleStartTime;
                    }

                    $ScheduleEndTime = '';
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleEndTime)) {
                        $ScheduleEndTime = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleEndTime;
                    }
                    if(isset($getCaregiverDetails->dob) && $getCaregiverDetails->dob !=""){
                      
                        $startTime = Carbon::createFromFormat('Y-m-d H:i',addslashes($ScheduleStartTime));
                        $startTime->setTimezone('UTC');
                      
                        $endTime = Carbon::createFromFormat('Y-m-d H:i',addslashes($ScheduleEndTime));
                        $endTime->setTimezone('UTC');

                        $finalArray = [
                            'careGiverHHAXCode'=>addslashes($CaregiverCode),
                            'careGiverFirstName'=>addslashes($FirstName),
                            'careGiverLastName'=>addslashes($LastName),
                            
                            'careGiverDOB'=>isset($getCaregiverDetails->dob)?$getCaregiverDetails->dob:"",
                            'careGiverZip'=>isset($getCaregiverDetails->zipcode)?$getCaregiverDetails->zipcode:"",
                            'careGiverPhone'=>isset($getCaregiverDetails->mobile_or_sms)?$getCaregiverDetails->mobile_or_sms:"",
                            'careGiverLanguage'=>isset($getCaregiverDetails->language)?$getCaregiverDetails->language:"",
                            'patientHHAXAdmissionId'=>$extId,
                            'shiftStart'=>$startTime->format('Y-m-d H:i:s'),
                            'shiftEnd'=>$endTime->format('Y-m-d H:i:s'),
                        ];
                    }
                    
                }
            }

            return $finalArray;
        }
    }

    public static function getCaregiverDetails($cid,$AppName,$AppSecret,$appToken){
        $finalCaregiverDetails = [];
        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetCaregiverMedicalDetails xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                            <AppName>' . $AppName . '</AppName>
                            <AppSecret>' . $AppSecret . '</AppSecret>
                            <AppKey>' . $appToken . '</AppKey>
                            </Authentication>
                            <SearchFilter>
                        <CaregiverID>' . $cid. '</CaregiverID>
                        <CaregiverComplianceExpItemID>-1</CaregiverComplianceExpItemID>
                        <ComplianceStatus>All</ComplianceStatus>
                    </SearchFilter>
                    </GetCaregiverMedicalDetails>
            </soap:Body>
        </soap:Envelope>';

        $json = Self::getData($xml_post_string, 'GetCaregiverMedicalDetails');

        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);

            if(isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID ==0){
                if(isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[0]) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[0] !=""){
                    $finalCaregiverDetails = [];
                }
            }

        }
    }


    public static function getPatientIDListByAgencyIdNew($agencyID)
    {

        $agencyDetails = Agency::where('id', $agencyID)->get();
        $getDetails = HHAPatient::where('agency_fk',$agencyID)->groupBy('patient_id')->pluck('patient_id');
     
        $existingPatientIds = $getDetails->toArray();
        foreach ($agencyDetails as $agency) {
            
            if (!empty($agency->app_name) && !empty($agency->app_key)  && !empty($agency->app_token)) {
               // $flagUpdate = HHAPatient::updateData(array('hha_delete_flag' => 'Y'), array('agency_fk' => $agency->id));
                $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                                <SearchPatients xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $agency->app_name . '</AppName>
                                        <AppSecret>' . $agency->app_key . '</AppSecret>
                                        <AppKey>' . $agency->app_token . '</AppKey>
                                    </Authentication>
                                    <SearchFilters>
                                            <Status>Active</Status>
                                </SearchFilters>    
                                </SearchPatients> 
                        </soap:Body>
                        </soap:Envelope>';
               if($agencyID ==self::STATIC_AGENCY_ID){
                $json = SELF::getDataDemo($xml, 'SearchPatients');
               }else{
                $json = SELF::getData($xml, 'SearchPatients');
               }
                
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
                    $hhaPatientIds = [];
                    if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID)) {
                            $cnt_Patients = count($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID);
                            for ($i = 0; $i < $cnt_Patients; $i++) {
                                $PatientID = (array)$xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID[$i];
                                $hhaPatientIds[] = $PatientID[0];
                                // if(in_array($PatientID[0],$existingPatientIds)){
                                // }else{
                                    
                                // }
                               // HHAPatient::updateOrCreate(["agency_fk"  => $agency->id, "patient_id" => $PatientID[0]], [ 'hha_delete_flag' => 'N']);
                            } 
                        }
                    }

                    $final = array_diff($hhaPatientIds,$existingPatientIds);
                
                    $saveData = [];
                    if(count($final) >0){
                        foreach($final as $pt){
                            $temp = [];
                            $temp['agency_fk'] = $agency->id;
                            $temp['patient_id'] = $pt;
                            $temp['hha_delete_flag'] = "N";
                            $temp['created_at'] =date('Y-m-d H:i:s');
                            $saveData[] = $temp;
                        
                            //HHAPatient::updateOrCreate(["agency_fk"  => $agency->id, "patient_id" => $pt], [ 'hha_delete_flag' => 'N']);
                        }
                        DB::table('hha_patients')->insert($saveData);
                    }
                    
                }
            }
        }
    }

    public static function patientDemographicDetails($PatientId, $agencyID)
    {
        $finalArray = [];
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);
        
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <GetPatientDemographics
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                    <AppName>' . $agencyHHADetail->app_name . '</AppName>
                    <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                    <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                </Authentication>
                <PatientInfo>
                    <ID>'.$PatientId.'</ID>
                </PatientInfo>
            </GetPatientDemographics>
        </soap:Body>
    </soap:Envelope>';
        if($agencyID == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'GetPatientDemographics', $agencyHHADetail->agency_id);
        }else{
            $json = SELF::getData($xml, 'GetPatientDemographics', $agencyHHADetail->agency_id);
        }

       

        // $patientContectList = HHAPatient::where("agency_fk", $agencyID)
        //     ->where('patient_id', $PatientId)
        //     ->update(array("hha_sync" => 'Y'));

        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);

//echo "<pre>";print_r($xml);die();
     
            if (isset($xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo)) {
                    $patientDetailInfo = $xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo;
                   
                    $agencyID = (array)$patientDetailInfo->AgencyID;
                    $officeID = (array)$patientDetailInfo->OfficeID;
                    $firstName = (array)$patientDetailInfo->FirstName;
                    $middleName = (array)$patientDetailInfo->MiddleName;
                    $lastName = (array)$patientDetailInfo->LastName;
                    $dob = (array)$patientDetailInfo->BirthDate;
                    $gender = (array)$patientDetailInfo->Gender;
                    $coordinatorId ="";
                    $coordinatorName ="";
                    if (isset($patientDetailInfo->Coordinators->Coordinator[0]->ID)) {
                        $coordinatorDetails = $patientDetailInfo->Coordinators->Coordinator[0];
                        $coordinatorId =(array)$coordinatorDetails->ID;
                        $coordinatorName =(array)$coordinatorDetails->Name;
                    }
                    $coordinator_id = $coordinatorId;
                    $coordinator_name = $coordinatorName;
                    $PriorityCode = (array)$patientDetailInfo->PriorityCode;
                    $service_start_date = (array)$patientDetailInfo->ServiceRequestStartDate;
                    $nurseId = "";
                    $nurseName="";
                    if (isset($patientDetailInfo->Nurse[0]->ID)) {
                        $nurseDetails = $patientDetailInfo->Nurse[0];
                        $nurseId =(array)$nurseDetails->ID;
                        $nurseName =(array)$nurseDetails->Name;
                    }
                    $admission_id = (array)$patientDetailInfo->AdmissionID;
                    $PatientID = (array)$patientDetailInfo->PatientID;
                    $medicaid_number = (array)$patientDetailInfo->MedicaidNumber;
                    $medicare_number = (array)$patientDetailInfo->MedicareNumber;
                    $discipline = "";
                    if (isset($patientDetailInfo->AcceptedServices[0]->Discipline)) {
                        $acceptedServicesDetails = $patientDetailInfo->AcceptedServices[0];
                        $discipline =(array)$acceptedServicesDetails->Discipline;
                    }
                    $ssn = (array)$patientDetailInfo->SSN;
                    $alerts = (array)$patientDetailInfo->Alerts;
                    $SourceOfAdmissionId ="";
                    $SourceOfAdmissionName="";
                    if (isset($patientDetailInfo->SourceOfAdmission[0]->ID)) {
                        $sourceOfAdmissionDetails = $patientDetailInfo->SourceOfAdmission[0];
                        $SourceOfAdmissionId =(array)$sourceOfAdmissionDetails->ID;
                        $SourceOfAdmissionName =(array)$sourceOfAdmissionDetails->Name;
                    }
                    $teamId = "";
                    $teamName = "";
                    if (isset($patientDetailInfo->Team[0]->ID)) {
                        $teamDetails = $patientDetailInfo->Team[0];
                        $teamId =(array)$teamDetails->ID;
                        $teamName =(array)$teamDetails->Name;
                    }
                    $locationId = "";
                    $locationName = "";
                    if (isset($patientDetailInfo->Location[0]->ID)) {
                        $locationDetails = $patientDetailInfo->Location[0];
                        $locationId =(array)$locationDetails->ID;
                        $locationName =(array)$locationDetails->Name;
                    }
                    $branchId = "";
                    $branchName = "";
                    if (isset($patientDetailInfo->Branch[0]->ID)) {
                        $BranchDetails = $patientDetailInfo->Branch[0];
                        $branchId =(array)$BranchDetails->ID;
                        $branchName =(array)$BranchDetails->Name;
                    }
                    $address1 ="";
                    $address2 ="";
                    $cross_street ="";
                    $city ="";
                    $zip5 ="";
                    $state ="";
                    $county ="";
                    $home_phone ="";
                    $phone2 ="";
                    if (isset($patientDetailInfo->Addresses->Address)) {
                        $addressDetails = $patientDetailInfo->Addresses->Address;
                        $address1 =(array)$addressDetails->Address1;
                        $address2 =(array)$addressDetails->Address2;
                        $cross_street =(array)$addressDetails->CrossStreet;
                        $city =(array)$addressDetails->City;
                        $zip5 =(array)$addressDetails->Zip5;
                        $state =(array)$addressDetails->State;
                        $county =(array)$addressDetails->County;
                    }

                    $home_phone =(array)$patientDetailInfo->HomePhone;
                    $phone2 =(array)$patientDetailInfo->Phone2;
                    $phone2Description = (array)$patientDetailInfo->Phone2Description;
                    $phone3 = (array)$patientDetailInfo->Phone3;
                    $phone3Description = (array)$patientDetailInfo->Phone3Description;

                    $homePhoneLocationAddressID = (array)$patientDetailInfo->HomePhoneLocationAddressID;
                    $homePhoneLocationAddress = (array)$patientDetailInfo->HomePhoneLocationAddress;
                    $homePhone2LocationAddressID = (array)$patientDetailInfo->HomePhone2LocationAddressID;
                    $homePhone2LocationAddress = (array)$patientDetailInfo->HomePhone2LocationAddress;
                    $homePhone3LocationAddressID = (array)$patientDetailInfo->HomePhone3LocationAddressID;
                    $homePhone3LocationAddress = (array)$patientDetailInfo->HomePhone3LocationAddress;
                    $direction = (array)$patientDetailInfo->Direction;

                    $alternateBillingFirstName = "";
                    $alternateBillingMiddleName = "";
                    $alternateBillingLastName = "";
                    $alternateBillingStreet = "";
                    $alternateBillingCity = "";
                    $alternateBillingState = "";
                    $alternateBillingZip5 = "";
                    if (isset($patientDetailInfo->AlternateBilling[0]->FirstName)) {
                        $AlternateBillingDetails = $patientDetailInfo->AlternateBilling[0];
                        $alternateBillingFirstName =(array)$AlternateBillingDetails->FirstName;
                        $alternateBillingMiddleName =(array)$AlternateBillingDetails->MiddleName;
                        $alternateBillingLastName =(array)$AlternateBillingDetails->LastName;
                        $alternateBillingStreet =(array)$AlternateBillingDetails->Street;
                        $alternateBillingCity =(array)$AlternateBillingDetails->City;
                        $alternateBillingState =(array)$AlternateBillingDetails->State;
                        $alternateBillingZip5 =(array)$AlternateBillingDetails->Zip5;
                    }

                    $emergencyContactName = "";
                    $emergencyContactLivesWithPatient = "";
                    $emergencyContactHaveKeys = "";
                    $emergencyContactPhone1 = "";
                    $emergencyContactPhone2 = "";
                    $emergencyContactAddress = "";
                    if (isset($patientDetailInfo->EmergencyContacts->EmergencyContact)) {
                        $emergencyContactDetails = $patientDetailInfo->EmergencyContacts->EmergencyContact[0];
                        $emergencyContactName =(array)$emergencyContactDetails->Name;
                        $emergencyContactLivesWithPatient =(array)$emergencyContactDetails->LivesWithPatient;
                        $emergencyContactHaveKeys =(array)$emergencyContactDetails->HaveKeys;
                        $emergencyContactPhone1 =(array)$emergencyContactDetails->Phone1;
                        $emergencyContactPhone2 =(array)$emergencyContactDetails->Phone2;
                        $emergencyContactAddress =(array)$emergencyContactDetails->Address;
                    }

                    $payerID = (array)$patientDetailInfo->PayerID;
                    $payerName = (array)$patientDetailInfo->PayerName;
                    $payerCoordinatorID = (array)$patientDetailInfo->PayerCoordinatorID;
                    $payerCoordinatorName = (array)$patientDetailInfo->PayerCoordinatorName;
                    $patientStatusID = (array)$patientDetailInfo->PatientStatusID;
                    $patientStatusName = (array)$patientDetailInfo->PatientStatusName;
                    $wageParity = (array)$patientDetailInfo->WageParity;
                    $wageParityFromDate1 = (array)$patientDetailInfo->WageParityFromDate1;
                    $wageParityToDate1 = (array)$patientDetailInfo->WageParityToDate1;
                    $wageParityFromDate2 = (array)$patientDetailInfo->WageParityFromDate2;
                    $wageParityToDate2 = (array)$patientDetailInfo->WageParityToDate2;
                    $primaryLanguageID = (array)$patientDetailInfo->PrimaryLanguageID;
                    $primaryLanguage = (array)$patientDetailInfo->PrimaryLanguage;


                    $finalArray = [
                        "firstName" => isset($firstName[0])?$firstName[0]:"",
                        "lastName" => isset($lastName[0])?$lastName[0]:"",
                        'middleName' => isset($middleName[0])?$middleName[0]:"",
                        'gender' => isset($gender[0])?$gender[0]:"",
                        'dob' => isset($dob[0])?date('m/d/Y',strtotime($dob[0])):"",
                        'admission_id' => isset($admission_id[0])?$admission_id[0]:"",
                        'coordinator_id' => isset($coordinator_id[0])?$coordinator_id[0]:"",
                        'coordinator_name' => isset($coordinator_name[0])?$coordinator_name[0]:"",
                        'service_start_date' => isset($service_start_date[0]) ? date('m/d/Y',strtotime($service_start_date[0])) : "",
                        'medicaid_number' => isset($medicaid_number[0])?$medicaid_number[0]:"",
                        'medicare_number' => isset($medicare_number[0])?$medicare_number[0]:"",
                        'address1' => isset($address1[0])?$address1[0]:"",
                        'address2' => isset($address2[0])?$address2[0]:"",
                        'cross_street' => isset($cross_street[0])?$cross_street[0]:"",
                        'city' => isset($city[0])?$city[0]:"",
                        'zip5' => isset($zip5[0])?$zip5[0]:"",
                        'state' => isset($state[0])?$state[0]:"",
                        'county' => isset($county[0])?$county[0]:"",
                        'home_phone' =>isset($home_phone[0])? str_replace('-', '', $home_phone[0]):"",
                        'phone2' => isset($phone2[0])? str_replace('-', '',$phone2[0]):"",
                        'officeId' => isset($officeID[0])?$officeID[0]:"",
                        'PriorityCode' => isset($PriorityCode[0])?$PriorityCode[0]:"",
                        'nurseId' => isset($nurseId[0])?$nurseId[0]:"",
                        'nurseName' => isset($nurseName[0])?$nurseName[0]:"",
                        'PatientID' => isset($PatientID[0])?$PatientID[0]:"",
                        'discipline' => isset($discipline[0])?$discipline[0]:"",
                        'ssn' => isset($ssn[0])?$ssn[0]:"",
                        'alerts' => isset($alerts[0])?$alerts[0]:"",
                        'teamId' => isset($teamId[0])?$teamId[0]:"",
                        'SourceOfAdmissionId' => isset($SourceOfAdmissionId[0])?$SourceOfAdmissionId[0]:"",
                        'SourceOfAdmissionName' => isset($SourceOfAdmissionName[0])?$SourceOfAdmissionName[0]:"",
                        'emergencyContactName' => isset($emergencyContactName[0])?$emergencyContactName[0]:"",
                        'teamName' => isset($teamName[0])?$teamName[0]:"",
                        'locationId' => isset($locationId[0])?$locationId[0]:"",
                        'locationName' => isset($locationName[0])?$locationName[0]:"",
                        'branchId' => isset($branchId[0])?$branchId[0]:"",
                        'branchName' => isset($branchName[0])?$branchName[0]:"",
                        'phone2Description' => isset($phone2Description[0])?$phone2Description[0]:"",
                        'phone3' => isset($phone3[0])?$phone3[0]:"",
                        'phone3Description' => isset($phone3Description[0])?$phone3Description[0]:"",
                        'homePhoneLocationAddressID' => isset($homePhoneLocationAddressID[0])?$homePhoneLocationAddressID[0]:"",
                        'homePhoneLocationAddress' => isset($homePhoneLocationAddress[0])?$homePhoneLocationAddress[0]:"",
                        'homePhone2LocationAddressID' => isset($homePhone2LocationAddressID[0])?$homePhone2LocationAddressID[0]:"",
                        'homePhone2LocationAddress' => isset($homePhone2LocationAddress[0])?$homePhone2LocationAddress[0]:"",
                        'homePhone3LocationAddressID' => isset($homePhone3LocationAddressID[0])?$homePhone3LocationAddressID[0]:"",
                        'homePhone3LocationAddress' => isset($homePhone3LocationAddress[0])?$homePhone3LocationAddress[0]:"",
                        'direction' => isset($direction[0])?$direction[0]:"",
                        'alternateBillingFirstName' => isset($alternateBillingFirstName[0])?$alternateBillingFirstName[0]:"",
                        'alternateBillingMiddleName' => isset($alternateBillingMiddleName[0])?$alternateBillingMiddleName[0]:"",
                        'alternateBillingLastName' => isset($alternateBillingLastName[0])?$alternateBillingLastName[0]:"",
                        'alternateBillingStreet' => isset($alternateBillingStreet[0])?$alternateBillingStreet[0]:"",
                        'alternateBillingCity' => isset($alternateBillingCity[0])?$alternateBillingCity[0]:"",
                        'alternateBillingState' => isset($alternateBillingState[0])?$alternateBillingState[0]:"",
                        'alternateBillingZip5' => isset($alternateBillingZip5[0])?$alternateBillingZip5[0]:"",
                        'emergencyContactName' => isset($emergencyContactName[0])?$emergencyContactName[0]:"",
                        'emergencyContactLivesWithPatient' => isset($emergencyContactLivesWithPatient[0])?$emergencyContactLivesWithPatient[0]:"",
                        'emergencyContactHaveKeys' => isset($emergencyContactHaveKeys[0])?$emergencyContactHaveKeys[0]:"",
                        'emergencyContactPhone1' => isset($emergencyContactPhone1[0])?$emergencyContactPhone1[0]:"",
                        'emergencyContactPhone2' => isset($emergencyContactPhone2[0])?$emergencyContactPhone2[0]:"",
                        'emergencyContactAddress' => isset($emergencyContactAddress[0])?$emergencyContactAddress[0]:"",
                        'payerID' => isset($payerID[0])?$payerID[0]:"",
                        'payerName' => isset($payerName[0])?$payerName[0]:"",
                        'payerCoordinatorID' => isset($payerCoordinatorID[0])?$payerCoordinatorID[0]:"",
                        'payerCoordinatorName' => isset($payerCoordinatorName[0])?$payerCoordinatorName[0]:"",
                        'patientStatusID' => isset($patientStatusID[0])?$patientStatusID[0]:"",
                        'patientStatusName' => isset($patientStatusName[0])?$patientStatusName[0]:"",
                        'wageParity' => isset($wageParity[0])?$wageParity[0]:"",
                        'wageParityFromDate1' => isset($wageParityFromDate1[0])?$wageParityFromDate1[0]:"",
                        'wageParityToDate1' => isset($wageParityToDate1[0])?$wageParityToDate1[0]:"",
                        'wageParityFromDate2' => isset($wageParityFromDate2[0])?$wageParityFromDate2[0]:"",
                        'wageParityToDate2' => isset($wageParityToDate2[0])?$wageParityToDate2[0]:"",
                        'primaryLanguageID' => isset($primaryLanguageID[0])?$primaryLanguageID[0]:"",
                        'primaryLanguage' => isset($primaryLanguage[0])?$primaryLanguage[0]:"",
                        'discipline_new' => isset($discipline[0])?implode(',',$discipline):"",
                    ];

                }
            }
            return $finalArray;
        } 
    }

    public static function GetPatientAuthorizationInfoDetails($PatientId, $agencyID){
     
       
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);
      
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <SearchPatientAuthorizations
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                <AppName>' . $agencyHHADetail->app_name . '</AppName>
                <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                </Authentication>
                <SearchFilters>
                    <PatientID>'.$PatientId.'</PatientID>
                </SearchFilters>
            </SearchPatientAuthorizations>
        </soap:Body>
    </soap:Envelope>';

    if($agencyID == self::STATIC_AGENCY_ID){
        $json = SELF::getDataDemo($xml, 'SearchPatientAuthorizations');
    }else{
        $json = SELF::getData($xml, 'SearchPatientAuthorizations');
    }
        
        $finalAuthorization = [];
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
           
            if (isset($xml->Body->SearchPatientAuthorizationsResponse->SearchPatientAuthorizationsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientAuthorizationsResponse->SearchPatientAuthorizationsResult->Result->ErrorInfo->ErrorID == 0) {
              
                if (isset($xml->Body->SearchPatientAuthorizationsResponse->SearchPatientAuthorizationsResult->PatientAuthorizations->Authorization)) {
                    $details = json_encode($xml->Body->SearchPatientAuthorizationsResponse->SearchPatientAuthorizationsResult->PatientAuthorizations);
                    $final = [];
                    $details =json_decode($details,true)['Authorization'];
                    if(isset($details[0])){
                        foreach($details as $key=>$val){
                     
                            $final[] =$val;
                        }
                    }else{
                        $final[] =$details;
                    }
                    
                    foreach($final as $ks){
                        $getDetails = self::getAuthorizationDetails($ks['ID'],$PatientId,$agencyID);
                        $finalAuthorization[] = (object)$getDetails;
                    }
                }
            }

            return $finalAuthorization;
        }
    }

    public static function getAuthorizationDetails($AuthorizationID, $patientId, $agencyId)
    {
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <GetPatientAuthorizationInfo
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                <AppName>' . $agencyHHADetail->app_name . '</AppName>
                <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                </Authentication>
                <AuthorizationInfo>
                    <PatientID>' . $patientId . '</PatientID>
                    <AuthorizationID>' . $AuthorizationID . '</AuthorizationID>
                </AuthorizationInfo>
            </GetPatientAuthorizationInfo>
        </soap:Body>
    </soap:Envelope>';

    if($agencyId == self::STATIC_AGENCY_ID){
        $json = SELF::getDataDemo($xml, 'GetPatientAuthorizationInfo');
    }else{
        $json = SELF::getData($xml, 'GetPatientAuthorizationInfo');
    }
        
        $finalArray = [];
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
            if (isset($xml->Body->GetPatientAuthorizationInfoResponse->GetPatientAuthorizationInfoResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientAuthorizationInfoResponse->GetPatientAuthorizationInfoResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetPatientAuthorizationInfoResponse->GetPatientAuthorizationInfoResult->AuthorizationInfo)) {
                    $AuthorizationID = "";
                    $details = $xml->Body->GetPatientAuthorizationInfoResponse->GetPatientAuthorizationInfoResult->AuthorizationInfo;
                    if (isset($details->AuthorizationID)) {
                        $AuthorizationID = addslashes($details->AuthorizationID);
                    }

                    $ContractID = "";
                    if (isset($details->Contract->ID)) {
                        $ContractID = addslashes($details->Contract->ID);
                    }

                    $ContractName = "";
                    if (isset($details->Contract->Name)) {
                        $ContractName = addslashes($details->Contract->Name);
                    }
                    $AuthorizationNumber = "";
                    if (isset($details->AuthorizationNumber)) {
                        $AuthorizationNumber = addslashes($details->AuthorizationNumber);
                    }
                    $StartDate = "";
                    if (isset($details->StartDate)) {
                        $StartDate = addslashes($details->StartDate);
                    }

                    $StopDate = "";
                    if (isset($details->StopDate)) {
                        $StopDate = addslashes($details->StopDate);
                    }

                    $MaxUnits = "";
                    if (isset($details->MaxUnits)) {
                        $MaxUnits = addslashes($details->MaxUnits);
                    }

                    $RemainingUnits = "";
                    if (isset($details->RemainingUnits)) {
                        $RemainingUnits = addslashes($details->RemainingUnits);
                    }
                    $BankedHours = "";
                    if (isset($details->BankedHours)) {
                        $BankedHours = addslashes($details->BankedHours);
                    }

                    $Period = "";
                    if (isset($details->Period->Name)) {
                        $Period = addslashes($details->Period->Name);
                    }

                    $WeeklyMaxAuthorization = "";
                    if (isset($details->WeeklyMaxAuthorization)) {
                        $WeeklyMaxAuthorization = addslashes($details->WeeklyMaxAuthorization);
                    }

                    $EntirePeriodMaxAuthorization = "";
                    if (isset($details->EntirePeriodMaxAuthorization)) {
                        $EntirePeriodMaxAuthorization = addslashes($details->EntirePeriodMaxAuthorization);
                    }

                    $MonthlyMaxAuthorization = "";
                    if (isset($details->MonthlyMaxAuthorization)) {
                        $MonthlyMaxAuthorization = addslashes($details->MonthlyMaxAuthorization);
                    }

                    $Weekday = "";
                    if (isset($details->Weekday)) {
                        $Weekday = addslashes($details->Weekday);
                    }

                    $Weekend = "";
                    if (isset($details->Weekend)) {
                        $Weekend = addslashes($details->Weekend);
                    }

                    $finalArray = [
                        'AuthorizationID' => $AuthorizationID,
                        'ContractID' => $ContractID,
                        'ContractName' => $ContractName,
                        'AuthorizationNumber' => $AuthorizationNumber,
                        'StartDate' => ($StartDate != "") ? date('m/d/Y', strtotime($StartDate)) : "",
                        'StopDate' => ($StopDate != "") ? date('m/d/Y', strtotime($StopDate)) : "",
                        'MaxUnits' => $MaxUnits,
                        'RemainingUnits' => $RemainingUnits,
                        'BankedHours' => $BankedHours,
                        'Period' => $Period,
                        'WeeklyMaxAuthorization' => $WeeklyMaxAuthorization,
                        'EntirePeriodMaxAuthorization' => $EntirePeriodMaxAuthorization,
                        'MonthlyMaxAuthorization' => $MonthlyMaxAuthorization,
                        'Weekday' => $Weekday,
                        'Weekend' => $Weekend,
                    ];
                }
            }

            return $finalArray;
        }
    }

    public static function getHHADicipline($details){
        $explode = explode('-',$details->externalId);
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($details->agencyDetails->id);
       
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <SearchPatients
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                    <AppName>'.$agencyHHADetail->app_name.'</AppName>
                    <AppSecret>'.$agencyHHADetail->app_key.'</AppSecret>
                    <AppKey>'.$agencyHHADetail->app_token.'</AppKey>
                </Authentication>
                <SearchFilters>
                    <FirstName></FirstName>
                    <LastName></LastName>
                    <Status>-1</Status>
                    <PhoneNumber></PhoneNumber>
                    <AdmissionID>'.$explode[1].'</AdmissionID>
                    <MRNumber></MRNumber>
                    <SSN></SSN>
                </SearchFilters>
            </SearchPatients>
        </soap:Body>
    </soap:Envelope>';


        $json = SELF::getData($xml, 'SearchPatients');

      $final = [];
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
            if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID)) {
                    $patientId = $xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID;
                    
                    $loadDetails = self::patientDemographicDetails($patientId,$details->agencyDetails->id);
                    $final = $loadDetails['discipline_new'];
                }
            }

        }
        
        return $final;
        
    }

    public static function searchPatientWithAgencyId($agencyId, $search)
    {
        $query = HHAPatient::selectRaw('patient_id as id, CONCAT(first_name," ",last_name) as name,admission_id')->whereNull('deleted_at')->where('agency_fk', $agencyId)->whereRaw('(LOWER(CONCAT_WS("",first_name," ",last_name)) LIKE "%' . strtolower($search) . '%" OR admission_id LIKE "%' . $search . '%")')->orderBy('id', 'desc')->get();

        return $query;
    }
    
    public static function getPatientDetails($id)
    {
        return   HHAPatient::with('agencyDetails')->where('patient_id', $id)->first();
    }

    public static function getHHAPatientNotes($details, $startDate, $endDate)
    {
  
        $AppName = $details->agencyDetails->app_name;
        $AppSecret = $details->agencyDetails->app_key;
        $AppKey = $details->agencyDetails->app_token;
       
        // $currentVisitIds = [];
        // $getExistingNotes   =HHACaregiverNotesHelper::getCaregiverNotesList($details->caregiver_id);

        // foreach($getExistingNotes as $val){
        //     $currentVisitIds[] = $val->CaregiverNoteID;
        // }

        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetPatientNotes xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>' . $AppName . '</AppName>
                                    <AppSecret>' . $AppSecret . '</AppSecret>
                                    <AppKey>' . $AppKey . '</AppKey>
                            </Authentication>
                            <PatientID>'.$details->patient_id.'</PatientID>
                            <ModifiedAfter>' . $startDate . '</ModifiedAfter>
                            
                    </GetPatientNotes>
            </soap:Body>
            </soap:Envelope>';
            if($details->agencyDetails->id == self::STATIC_AGENCY_ID){
                $json = Self::getDataDemo($xml_post_string, 'GetPatientNotes');
            }else{
                $json = Self::getData($xml_post_string, 'GetPatientNotes');
            }
        
        $finalArray = array();
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);



            if (isset($xml->Body->GetPatientNotesResponse->GetPatientNotesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientNotesResponse->GetPatientNotesResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetPatientNotesResponse->GetPatientNotesResult->PatientNotes)) {
                    $respoe = $xml->Body->GetPatientNotesResponse->GetPatientNotesResult->PatientNotes->PatientNote;
                    foreach ($respoe as  $val) {

                        $array = array(
                            'PatientNoteID' => addslashes($val->PatientNoteID),
                            'PatientID' => addslashes($val->PatientID),
                            'NoteDate' => addslashes(date('m/d/Y h:i A', strtotime($val->NoteDate))),
                            'Note' => addslashes($val->Note),
                            'created_date' => date('Y-m-d H:i:s'),

                        );
                        $finalArray[] = $array;
                    }
                }
            }
        }
        return $finalArray;
    }

    public static function getPatientDetailsNew($id)
    {
        return   HHAPatient::where('id', $id)->first();
    }


    public static function getHHAPatientClinics($details){
        $AppName = $details->agencyDetails->app_name;
        $AppSecret = $details->agencyDetails->app_key;
        $AppKey = $details->agencyDetails->app_token;
    

        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetPatientClinicalInfo xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>'.$AppName.'</AppName>
                                    <AppSecret>'.$AppSecret.'</AppSecret>
                                    <AppKey>'.$AppKey.'</AppKey>
                            </Authentication>
                            <PatientID>'.$details->patient_id.'</PatientID>
                            
                            
                    </GetPatientClinicalInfo>
            </soap:Body>
            </soap:Envelope>';

        $json = Self::getData($xml_post_string, 'GetPatientClinicalInfo');
        $finalArray = array();
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);


            if (isset($xml->Body->GetPatientClinicalInfoResponse->GetPatientClinicalInfoResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientClinicalInfoResponse->GetPatientClinicalInfoResult->Result->ErrorInfo->ErrorID == 0) {
             
                if (isset($xml->Body->GetPatientClinicalInfoResponse->GetPatientClinicalInfoResult->PatientClinicalInfo)) {

                    $respoe = $xml->Body->GetPatientClinicalInfoResponse->GetPatientClinicalInfoResult->PatientClinicalInfo;
                    
                    foreach ($respoe as  $val) {

                        $array = array(
                           
                            'PatientID' => addslashes($val->PatientID),
                            'NursingVisitsDue' => addslashes($val->NursingVisitsDue),
                            'MDOrderRequired' => addslashes($val->MDOrderRequired),
                            'MDOrderDue' => addslashes($val->MDOrderDue),
                            'MDVisitDue' => addslashes($val->MDVisitDue),
                        );
                        $finalArray[] = $array;
                    }
                }
            }
        }

        return $finalArray;
    }

    public static function getSyncPatientList(){
        return  HHAPatient::select('patient_id','agency_fk')->whereNull('deleted_at')->whereNull('first_name')->whereRaw("(hhasyncdatetime is null or hhasyncdatetime <'" . date('Y-m-d H:i:s', strtotime('-5 day')) . "')")->inRandomOrder()->limit(500)->get();
    }

    public static function getSearchPatientPOCList($details)
    {

        $finalArray = [];
        $xml_post_string = '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <SearchPatientPOC
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                        <AppName>' . $details->agencyDetail->app_name . '</AppName>
                        <AppSecret>' . $details->agencyDetail->app_key . '</AppSecret>
                        <AppKey>' . $details->agencyDetail->app_token . '</AppKey>
                    </Authentication>
                    <SearchFilters>
                            <PatientID>' . $details->patient_id . '</PatientID>
                    </SearchFilters>
                </SearchPatientPOC>
            </soap:Body>
        </soap:Envelope>';

        if($details->agencyDetail->id ==self::STATIC_AGENCY_ID){
            $json = Self::getDataDemo($xml_post_string, 'SearchPatientPOC');
        }else{
            $json = Self::getData($xml_post_string, 'SearchPatientPOC');
        }

        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
   
            if (isset($xml->Body->SearchPatientPOCResponse->SearchPatientPOCResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientPOCResponse->SearchPatientPOCResult->Result->ErrorInfo->ErrorID == 0) {

                if (isset($xml->Body->SearchPatientPOCResponse->SearchPatientPOCResult->PatientPOC)) {

                    $response = $xml->Body->SearchPatientPOCResponse->SearchPatientPOCResult->PatientPOC;
              
                   
                    $array = json_decode(json_encode($response),TRUE);
                 
                    if(!empty($array)){
                        if(isset($array['POC']['ID'])){
                            $temp = [];
                            $temp['ID'] =addslashes($array['POC']['ID']);
                            $temp['StartDate'] =date('m/d/Y',strtotime($array['POC']['StartDate']));
                            $temp['POCNumber'] =$array['POC']['POCNumber'];
                            $response=  self::getPatientPOCInfoList($details,addslashes($array['POC']['ID']));
                           
                            $finalArray[] = $response;
                        }else{
                           
                            foreach ($array['POC'] as  $val) {
                       
                                $temp = [];
                                $temp['ID'] =addslashes($val['ID']);
                                $temp['StartDate'] =date('m/d/Y',strtotime($val['StartDate']));
                                $temp['POCNumber'] =$val['POCNumber'];
                                $response=  self::getPatientPOCInfoList($details,addslashes($val['ID']));
                                
                              
                                $finalArray[] = $response;
                            }
                        }
                        
          
                    }
                    
                }
            }
        }

        return $finalArray;
    }

    public static function getPatientPOCInfoList($details,$poc_id)
    {
        $finalArray = [];
        $xml_post_string = '<soap:Envelope
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                            <GetPatientPOCInfo
                                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                <Authentication>
                                   <AppName>' . $details->agencyDetail->app_name . '</AppName>
                        <AppSecret>' . $details->agencyDetail->app_key . '</AppSecret>
                        <AppKey>' . $details->agencyDetail->app_token . '</AppKey>
                                </Authentication>
                                <POCInfo>
                                    <POCID>' . $poc_id . '</POCID>
                                </POCInfo>
                            </GetPatientPOCInfo>
                        </soap:Body>
                    </soap:Envelope>
                    ';

        $json = Self::getData($xml_post_string, 'GetPatientPOCInfo');

        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);

            if (isset($xml->Body->GetPatientPOCInfoResponse->GetPatientPOCInfoResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientPOCInfoResponse->GetPatientPOCInfoResult->Result->ErrorInfo->ErrorID == 0) {

                if (isset($xml->Body->GetPatientPOCInfoResponse->GetPatientPOCInfoResult->POCInfo)) {

                    $response = $xml->Body->GetPatientPOCInfoResponse->GetPatientPOCInfoResult->POCInfo;

                    $patientInfo = [
                        'ID' => addslashes($response->Patient->ID),
                        'AdmissionNumber' => addslashes($response->Patient->AdmissionNumber),
                        'FirstName' => addslashes($response->Patient->FirstName),
                        'LastName' => addslashes($response->Patient->LastName),
                        'POCID' => addslashes($response->ID),
                        'StartDate' => ($response->StartDate !="")?date('m/d/Y',strtotime(addslashes($response->StartDate))):"",
                        'StopDate' => ($response->StopDate !="")?date('m/d/Y',strtotime(addslashes($response->StopDate))):"",
                        'POCNumber' => addslashes($response->POCNumber),
                        'Notes' => addslashes($response->Notes),
                        'CreatedDate' => addslashes($response->CreatedDate)
                    ];

                    $tasks = [];
                    foreach ($response->Tasks->Task as $task) {
                        $tasks[] = [
                            'ID' => addslashes($task->ID),
                            'Code' => addslashes($task->Code),
                            'CategoryName' => addslashes($task->Category->Name),
                            'Name' => addslashes($task->Name),
                            'AsNeeded' => addslashes($task->AsNeeded),
                            'WeeklyMin' => addslashes($task->WeeklyMin),
                            'WeeklyMax' => addslashes($task->WeeklyMax),
                            'Instruction' => addslashes($task->Instruction),
                            'Sunday' => addslashes($task->Sunday),
                            'Monday' => addslashes($task->Monday),
                            'Tuesday' => addslashes($task->Tuesday),
                            'Wednesday' => addslashes($task->Wednesday),
                            'Thursday' => addslashes($task->Thursday),
                            'Friday' => addslashes($task->Friday),
                            'Saturday' => addslashes($task->Saturday)
                        ];
                    }

                    $finalArray['PatientInfo'] = $patientInfo;
                    $finalArray['Tasks'] = $tasks;
                }
            }
        }
        return $finalArray;
    }

    public static function searchPatientCodeWithAgencyId($agencyId, $search)
    {
        $query = HHAPatient::selectRaw('patient_id as id, CONCAT(first_name," ",last_name) as name,admission_id,status,gender,employment_type')->whereNull('deleted_at');
        $where = "agency_fk =".$agencyId;    
        if($search['hha_patient_code_id'] !=""){
            $where .=" and admission_id = '".$search['hha_patient_code_id']."' ";
        }
        if($search['hha_patient_first_name'] !=""){
            $where .=" and first_name LIKE '%".$search['hha_patient_first_name']."%'";
        }
        if($search['hha_patient_last_name'] !=""){
            $where .=" and last_name LIKE '%".$search['hha_patient_last_name']."%'";
        }
        if($search['hha_patient_phone_no'] !=""){
            $where .=" and mobile_or_sms =".$search['hha_patient_phone_no'];
        }
        $query = $query->whereRaw($where)->orderBy('id', 'desc')->get();
       
        if(!empty($query[0])){
          
            return $query;
        }else{
            $query = self::searchPatientForHHAWithAllCondition($agencyId, $search);
        
        }
        return $query;
    }

    public static function searchPatientForHHA($agencyId, $search,$flag=""){
        $final = [];
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
        $xml = '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <SearchPatients
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                    <AppName>' . $agencyHHADetail->app_name . '</AppName>
                    <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                    <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                    <SearchFilters>
                        <AdmissionID>'.$search.'</AdmissionID>
                    </SearchFilters>
                </SearchPatients>
            </soap:Body>
        </soap:Envelope>';

        if($agencyId == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'SearchPatients');
        }else{
            $json = SELF::getData($xml, 'SearchPatients');
        }
        
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
            
      
            if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID == 0) {
                
                if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients)) {
                    
                    $respoe = count($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients);
                    for ($i = 0; $i < $respoe; $i++) {
                        $caregiverId = $xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients[$i]->PatientID;
                      
                       $getDetails  = self::patientDemographicDetails(addslashes($caregiverId),$agencyId);
                       
                      $final[]=$getDetails;
                    }
                    
                }
            }
 //           echo "<pre>";print_r($final);die();
            if($flag !=""){
                $temp = $final;
            }else{
                $temp = [];

               
                if(!empty($final[0])){
                    foreach($final as $val){
                        $tTemp = [];
                        $tTemp['patient_id'] = $val['PatientID'];
                        $tTemp['patient_name'] = $val['firstName'].' '.$val['lastName'];
                        $tTemp['status'] = $val['patientStatusName'];
                        $tTemp['admission_id'] = $val['admission_id'];
                        $temp[] = $tTemp;
                    }
                }
    
            }
          
            $final = $temp;
        }
        return $final;
    }

    public static function saveData($pid,$agencyId){
        $query = self::patientDemographicDetails($pid,$agencyId);

        if(isset($query['PatientID']) && $query['PatientID'] !=""){
            $save = [
                'patient_id'=>$query['PatientID'],
                'officeId'=>$query['officeId'],
                'agency_fk'=>$agencyId,
                'first_name'=>$query['firstName'],
                'middle_name'=>$query['middleName'],
                'last_name'=>$query['lastName'],
                'gender'=>$query['gender'],
                
                'dob'=>date('Y-m-d',strtotime($query['dob'])),
                'admission_id'=>$query['admission_id'],
                'mobile_or_sms'=>str_replace('-','',$query['home_phone']),
                'hha_delete_flag'=>"N",
                'hha_sync'=>"Y",
                'created_at'=>date('Y-m-d H:i:s'),
                'hhasyncdatetime'=>date('Y-m-d H:i:s'),
                'medicaid_number'=>$query['medicaid_number'],
                'medicare_number'=>$query['medicare_number'],
                
                'zip5'=>$query['zip5'],
                'language'=>$query['primaryLanguage'],
                'status'=>$query['patientStatusName'],
                'address1'=>$query['address1'],
                'address2'=>$query['address2'],
                'cross_street'=>$query['cross_street'],
                'city'=>$query['city'],
                'state'=>$query['state'],
                'county'=>$query['county'],
             
                'home_phone'=>str_replace('-','',$query['home_phone']),
                'phone2'=>str_replace('-','',$query['phone2']),
                'coordinator_id'=>$query['coordinator_id'],
                'coordinator_name'=>$query['coordinator_name'],
                'service_start_date'=>date('Y-m-d',strtotime($query['service_start_date'])),
                'nurseId'=>$query['nurseId'],
                'nurseName'=>$query['nurseName'],
                
               ];
        
               return  HHAPatient::updateOrCreate([
                    
                    'patient_id'   => $query['PatientID'],
                    'agency_fk'   => $agencyId,
                ],$save);
             
        }

    }

    public static function getHHASubject($patient_id,$agency_id){
        $response = [];
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agency_id);

        $xml = '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetPatientNoteReasons
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                    <AppName>' . $agencyHHADetail->app_name . '</AppName>
                    <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                    <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                        <SearchFilters>
                        
                            <PatientID>'.$patient_id.'</PatientID>
                        </SearchFilters>
                </GetPatientNoteReasons>
            </soap:Body>
        </soap:Envelope>';
        if($agency_id == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'GetPatientNoteReasons');
        }else{
            $json = SELF::getData($xml, 'GetPatientNoteReasons');
        }
        
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
            if (isset($xml->Body->GetPatientNoteReasonsResponse->GetPatientNoteReasonsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientNoteReasonsResponse->GetPatientNoteReasonsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetPatientNoteReasonsResponse->GetPatientNoteReasonsResult->PatientNoteReasons->PatientNoteReason)) {
                    $data = $xml->Body->GetPatientNoteReasonsResponse->GetPatientNoteReasonsResult->PatientNoteReasons->PatientNoteReason;
                    if(!empty($data[0])){
                        foreach($data as $val){
                            $tempReason = [];
                            $tempReason['ID'] = addslashes($val->ID);
                            $tempReason['Name'] = addslashes($val->Name);
                            $response[] = $tempReason;
                        }
                    }
                }
            }
        }

        return $response;
    }

    public static function createHHAPatientNotes($details,$response){
        
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
                $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                        <CreatePatientNote  xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                </Authentication>
                                <PatientNoteInfo>
                                    <PatientID>'.$details->patient_id.'</PatientID>
                                    <ReasonID>'.$response['subject_id'].'</ReasonID>
                                
                                    <FromDate>'.date('Y-m-d').'</FromDate>
                                    <ToDate>'.date('Y-m-d').'</ToDate>
                                    <EmergencyOfPriroity>N</EmergencyOfPriroity>
                                    <Internal>Y</Internal>
                                    <Note>'.$response['notes'].'</Note>
                                    <CaregiverID>0</CaregiverID>
                                    <SubjectID>-1</SubjectID>
                                    <EmailTo></EmailTo>
                                </PatientNoteInfo>
                                
                        </CreatePatientNote>
                </soap:Body>
                </soap:Envelope>';

            $json = Self::getData($xml_post_string, 'CreatePatientNote');

            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                // Set HTTP response status code to: 500 - Internal Server Error
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                 echo $xml = simplexml_load_string($clean_xml);
             
            }
        }
         
        return true;
    }
    public static function commonDetails($id){
        return HHAPatient::with(['agencyDetails'])->Where('patient_id',$id)->first();

    }
    public static function sendNotes($id,$data){ 
        $details = self::commonDetails($id); 
        $saveNotes = self::createHHAPatientNotes($details,$data);
    }

    public static function getHHAPatientChangesV2($id){
        $details = self::commonDetails($id);
        $finalArray = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            $xml_post_string = '<soap:Envelope
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <GetPatientChangesV2
                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>'.$details->agencyDetails->app_name.'</AppName>
                            <AppSecret>'.$details->agencyDetails->app_key.'</AppSecret>
                            <AppKey>'.$details->agencyDetails->app_token.'</AppKey>
                        </Authentication>
                        <OfficeID>0</OfficeID>
                        <ModifiedAfter>'.date('Y-m-d\TH:i:s',strtotime('-3 days')).'</ModifiedAfter>
                    </GetPatientChangesV2>
                </soap:Body>
            </soap:Envelope>';
            $json = Self::getData($xml_post_string, 'GetPatientChangesV2');
            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                // Set HTTP response status code to: 500 - Internal Server Error
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
                if (isset($xml->Body->GetPatientChangesV2Response->GetPatientChangesV2Result->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientChangesV2Response->GetPatientChangesV2Result->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetPatientChangesV2Response->GetPatientChangesV2Result->GetPatientChangesV2Info)) {
                            $response = $xml->Body->GetPatientChangesV2Response->GetPatientChangesV2Result->GetPatientChangesV2Info;
                            foreach($response as $val){
                                
                            }

                    }

                }
            }
        }
    }

    public static function getHHAPatientAuthorizationChangesV2($id){
        $details = self::commonDetails($id);
        $finalArray = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            $xml_post_string = '<soap:Envelope
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <GetPatientAuthorizationChanges
                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>'.$details->agencyDetails->app_name.'</AppName>
                            <AppSecret>'.$details->agencyDetails->app_key.'</AppSecret>
                            <AppKey>'.$details->agencyDetails->app_token.'</AppKey>
                        </Authentication>
                        <OfficeID>0</OfficeID>
                        <ModifiedAfter>2024-09-15T04:31:57.077</ModifiedAfter>
                    </GetPatientAuthorizationChanges>
                </soap:Body>
            </soap:Envelope>';
            $json = Self::getData($xml_post_string, 'GetPatientAuthorizationChanges');
            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                // Set HTTP response status code to: 500 - Internal Server Error
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
                
                if (isset($xml->Body->GetPatientAuthorizationChangesResponse->GetPatientAuthorizationChangesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientAuthorizationChangesResponse->GetPatientAuthorizationChangesResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetPatientAuthorizationChangesResponse->GetPatientAuthorizationChangesResult->GetPatientAuthorizationChangesInfo)) {
                            $response = $xml->Body->GetPatientAuthorizationChangesResponse->GetPatientAuthorizationChangesResult->GetPatientAuthorizationChangesInfo;
                            foreach($response as $val){
                                
                            }

                    }

                }
            }
        }
    }

    public static function getHHAPOCOffice($id){
      
        $details = self::commonDetails($id);
        $finalArray = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            $xml_post_string = '<soap:Envelope
                                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                <soap:Body>
                                    <GetOffices
                                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                        <Authentication>
                                            <AppName>'.$details->agencyDetails->app_name.'</AppName>
                                            <AppSecret>'.$details->agencyDetails->app_key.'</AppSecret>
                                            <AppKey>'.$details->agencyDetails->app_token.'</AppKey>
                                        </Authentication>
                                    </GetOffices>
                                </soap:Body>
                            </soap:Envelope>';

                            if($details->agencyDetails->id == self::STATIC_AGENCY_ID){
                                $json = Self::getDataDemo($xml_post_string, 'GetOffices');
                            }else{
                                $json = Self::getData($xml_post_string, 'GetOffices');
                            }

            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    $json = '{"jsonError": "unknown"}';
                }
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
              
                if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetOfficesResponse->GetOfficesResult->Result->ErrorInfo->ErrorID == 0) {

                    if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices)) {
                        $response = $xml->Body->GetOfficesResponse->GetOfficesResult->Offices;
                        $json = json_encode($response);
                        $array = json_decode($json,TRUE);
                        
                        if(!empty($array)){
                            foreach ($array['Office'] as  $val) {
                        
                                $temp = [];
                                $temp['id'] =addslashes($val['OfficeID']);
                                $temp['name'] =$val['OfficeName'].' - '.$val['OfficeCode'];                            
                                $finalArray[] = $temp;
                            }
                            
                        }
                        
                    }
                }
            }
        }
        return $finalArray;
    }

    public static function getHHAPOCTask($id, $officeID, $PatientID){
        $finalArray = [];
        $details = self::commonDetails($id);
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            $xml_post_string = '<soap:Envelope
                                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                                    xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                    <soap:Body>
                                        <GetPOCTasks
                                            xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                            <Authentication>
                                                <AppName>'.$details->agencyDetails->app_name.'</AppName>
                                                <AppSecret>'.$details->agencyDetails->app_key.'</AppSecret>
                                                <AppKey>'.$details->agencyDetails->app_token.'</AppKey>
                                            </Authentication>
                                            <OfficeID>'.$officeID.'</OfficeID>
                                            <PatientID>'.$details->patient_id.'</PatientID>
                                        </GetPOCTasks>
                                    </soap:Body>
                                </soap:Envelope>';

                                if($details->agencyDetails->id == self::STATIC_AGENCY_ID ){
                                    $json = Self::getDataDemo($xml_post_string, 'GetPOCTasks');
                                }else{
                                    $json = Self::getData($xml_post_string, 'GetPOCTasks');
                                }
           

            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    $json = '{"jsonError": "unknown"}';
                }
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
                
                if (isset($xml->Body->GetPOCTasksResponse->GetPOCTasksResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPOCTasksResponse->GetPOCTasksResult->Result->ErrorInfo->ErrorID == 0) {
             
                    if (isset($xml->Body->GetPOCTasksResponse->GetPOCTasksResult->POCTasks)) {
                        $response = $xml->Body->GetPOCTasksResponse->GetPOCTasksResult->POCTasks;
                        $json = json_encode($response);
                        $array = json_decode($json,TRUE);
                        
                        
                        if(!empty($array)){
                            foreach ($array['POCTask'] as  $val) {
                        
                                $temp = [];
                                $temp['id'] = addslashes($val['POCTaskID']);
                                $temp['name'] = addslashes($val['TaskName']).' - '.addslashes($val['POCTaskCode']);
                                $temp['task_category'] = addslashes($val['TaskCategory']);
                                $temp['code'] = addslashes($val['POCTaskCode']);
                                $temp['task_name'] = addslashes($val['TaskName']);
                                $finalArray[] = $temp;
                            }
                            
                        }
                        
                    }
                }
            }
        }
        return $finalArray;
    }

    public static function createPatientPOCDetails($id, $data,$notes=""){
        $details = self::commonDetails($id);
        $message = "";
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
           $xml_post_string ='<soap:Envelope
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <CreatePatientPOC
            xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
            <Authentication>
                <AppName>' . $details->agencyDetails->app_name . '</AppName>
                <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
            </Authentication>
            <PatientPOCInfo>
                <PatientID>'.$details->patient_id.'</PatientID>
                <StartDate>'.date('Y-m-d',strtotime($data['start_date'])).'</StartDate>
                <StopDate>'.date('Y-m-d',strtotime($data['stop_date'])).'</StopDate>';
                if($data['shift'] !='-1'){
                    $xml_post_string .='<Shift>'.$data['shift'].'</Shift>';
                }else{
                    $xml_post_string .='<Shift  xsi:nil="true" />';
                }
                
                $finalResponse = "";
                for ($i = 0; $i < count($data['task_id']); $i++) {
                    $sat = isset($data['days_'.$i+1]) && in_array('Sat',$data['days_'.$i+1]) ? "true": "false";
                    $sun = isset($data['days_'.$i+1]) && in_array('Sun',$data['days_'.$i+1]) ? "true": "false";
                    $mon = isset($data['days_'.$i+1]) && in_array('Mon',$data['days_'.$i+1]) ? "true": "false";
                    $tue = isset($data['days_'.$i+1]) && in_array('Tue',$data['days_'.$i+1]) ? "true": "false";
                    $wed = isset($data['days_'.$i+1]) && in_array('Wed',$data['days_'.$i+1]) ? "true": "false";
                    $thu = isset($data['days_'.$i+1]) && in_array('Thu',$data['days_'.$i+1]) ? "true": "false";
                    $fri = isset($data['days_'.$i+1]) && in_array('Fri',$data['days_'.$i+1]) ?  "true": "false";
                   
                    $msp ='<Minutes  xsi:nil="true" />';
                    if(isset($data['minutes'][$i]) && $data['minutes'][$i] !=""){
                        $msp ='<Minutes>'.($data['minutes'][$i] ?? 20).'</Minutes>';
                    }
                    $finalResponse .= '<POCTask>
                        <POCTaskID>'.$data['task_id'][$i].'</POCTaskID>'.$msp.'
                        
                        <AsRequested>'.($data['as_requested'][$i] ?? 'false').'</AsRequested>
                        <TimesPerWeekMin>'.$data['mintime'][$i].'</TimesPerWeekMin>
                        <TimesPerWeekMax>'.$data['maxtime'][$i].'</TimesPerWeekMax>
                        <Instruction>'.($data['instruction'][$i] ?? '').'</Instruction>
                        <Sat>'.($sat).'</Sat>
                        <Sun>'.($sun).'</Sun>
                        <Mon>'.($mon).'</Mon>
                        <Tue>'.($tue).'</Tue>
                        <Wed>'.($wed).'</Wed>
                        <Thu>'.($thu).'</Thu>
                        <Fri>'.($fri).'</Fri>
                    </POCTask>';
                }
                $xml_post_string .='<POCTasks>'.$finalResponse.'
                </POCTasks>';
                if(isset($notes) && !empty($notes)){
                    $xml_post_string .= '<POCNote>'.$notes.'</POCNote>';
                }else{
                    $xml_post_string .= '<POCNote></POCNote>';
                }
            $xml_post_string .= '</PatientPOCInfo>
        </CreatePatientPOC>
    </soap:Body>
</soap:Envelope>';
            if($details->agencyDetails->id ==self::STATIC_AGENCY_ID){

              $json = Self::getDataDemo($xml_post_string, 'CreatePatientPOC');
            }else{
                $json = Self::getData($xml_post_string, 'CreatePatientPOC');
            }

            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                // Set HTTP response status code to: 500 - Internal Server Error
                http_response_code(500);
            } else {

                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
                
                $data = [];
                if (isset($xml->Body->CreatePatientPOCResponse->CreatePatientPOCResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreatePatientPOCResponse->CreatePatientPOCResult->Result->ErrorInfo->ErrorID == 0) {
                    $response = (array)$xml->Body->CreatePatientPOCResponse->CreatePatientPOCResult->CreatePatientPOC;
                    $data = [
                        'patient_id'=>addslashes($response['PatientID']),
                        'POCHeaderID'=>addslashes($response['POCHeaderID'])
                    ];
                    $status = 1;
                }else{
                    $message = "Sorry something want wrong";
                    if (isset($xml->Body->CreatePatientPOCResponse->CreatePatientPOCResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreatePatientPOCResponse->CreatePatientPOCResult->Result->ErrorInfo->ErrorID !=0) {
                        $message = (string)$xml->Body->CreatePatientPOCResponse->CreatePatientPOCResult->Result->ErrorInfo->ErrorMessage;
                    }
                   
                    $status = 0;
                }
                return ['status'=>$status,'message'=>$message,'data'=>$data];
            }
        }
    }

    public static function getDocumentData($id){
      
        $details = Self::commonDetails($id);
        $documentData = [];
   
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <SearchPatientDocument
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <SearchFilters>
                                        <PatientID>'. $id .'</PatientID>
                                        <PatientDocumentTypeID xsi:nil="true" />
                                        <PatientDocumentID xsi:nil="true" />
                                        <FromDate xsi:nil="true" />
                                        <ToDate xsi:nil="true" />
                                    </SearchFilters>
                                </SearchPatientDocument>
                            </soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'SearchPatientDocument');
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
  
                    if (isset($xml->Body->SearchPatientDocumentResponse->SearchPatientDocumentResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientDocumentResponse->SearchPatientDocumentResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->SearchPatientDocumentResponse->SearchPatientDocumentResult->PatientDocuments)) {
                            $respoe = count($xml->Body->SearchPatientDocumentResponse->SearchPatientDocumentResult->PatientDocuments);
                           
                            $response = (array)$xml->Body->SearchPatientDocumentResponse->SearchPatientDocumentResult->PatientDocuments;
                            $array = json_decode(json_encode($response), true);
                            if(isset($array['PatientDocument'])){
                                if(isset($array['PatientDocument']['PatientDocID'])){
                                    $documentData = array(
                                        'patientDocId' => addslashes($array['PatientDocument']['PatientDocID']),
                                        'patientId' => addslashes($array['PatientDocument']['PatientID']),
                                        'patinetDocumentTypeID' => addslashes($array['PatientDocument']['PatientDocumentTypeID']),
                                        'patientDocumentType' => addslashes($array['PatientDocument']['PatientDocumentType']),
                                        'description' =>addslashes($array['PatientDocument']['Description']),
                                        'CreatedBy' => addslashes($array['PatientDocument']['CreatedBy']),
                                        'CreatedOn' => addslashes($array['PatientDocument']['CreatedOn']),
                                        'fileName' => addslashes($array['PatientDocument']['FileName']),
                                    );
                                }else{
                                   
                                    foreach($array['PatientDocument'] as $val){
                                    
                                        $documentData[] = array(
                                            'patientDocId' => addslashes($val['PatientDocID']),
                                            'patientId' => addslashes($val['PatientID']),
                                            'patinetDocumentTypeID' => addslashes($val['PatientDocumentTypeID']),
                                            'patientDocumentType' => addslashes($val['PatientDocumentType']),
                                            'description' =>$val['Description'],
                                            'CreatedBy' => addslashes($val['CreatedBy']),
                                            'CreatedOn' => addslashes($val['CreatedOn']),
                                            'fileName' => addslashes($val['FileName']),
                                        );
                                     
                                    }
                                }
                                
                            }
                          
                        }
                    }
                }
            }
        }
     
        return $documentData;
    }

    public static function getDocumentTypeData($id){
      
        $details = Self::commonDetails($id);

        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <GetPatientDocumentType
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <Status>ACTIVE</Status>
                                </GetPatientDocumentType>
                            </soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'GetPatientDocumentType');
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
                               

                    if (isset($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes)) {
                            $respoe = count($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[0]->PatientDocumentTypeID);
                            for ($i = 0; $i < $respoe; $i++) {
                                $patientDocumentTypeID = '';
                                if(isset($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->PatientDocumentTypeID) && !empty($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->PatientDocumentTypeID)){
                                    $patientDocumentTypeID = $xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->PatientDocumentTypeID;
                                }
                                
                                $patientDocumentType = '';
                                if(isset($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->PatientDocumentType) && !empty($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->PatientDocumentType)){
                                    $patientDocumentType = $xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->PatientDocumentType;
                                }

                                $description = '';
                                if(isset($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->Description) && !empty($xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->Description)){
                                    $description = $xml->Body->GetPatientDocumentTypeResponse->GetPatientDocumentTypeResult->PatientDocumentTypes[$i]->Description;
                                }

                                $temp = [];
                                $temp['id'] = $patientDocumentTypeID;
                                $temp['name'] = $patientDocumentType;
                                $temp['description'] = $description;
                                $documentData[] = $temp;
                            }   
                        }
                    }
                }
            }
        }
        return $documentData;
    }

    public static function saveDocumentData($data){
        $id = $data['id'];
        $details = Self::commonDetails($id);
        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <AddPatientDocument
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <PatientDocumentInfo>
                                        <PatientID>'.$id.'</PatientID>
                                        <PatientDocumentTypeID>'.$data['patient_document_type_id'].'</PatientDocumentTypeID>
                                        <Description>'.$data['patient_description'].'</Description>';
                                    if(isset($data['patient_file_name']) && !empty($data['patient_file_name'])){
                                        $xml .= '<FileName>'.$data['patient_file_name'].'</FileName>';
                                    }
                                    if(isset($data['patient_file_stream']) && !empty($data['patient_file_stream'])){
                                        $xml .= '<StreamData>'.$data['file_stream'].'</StreamData>';
                                    }
                            $xml .=  '</PatientDocumentInfo>
                                </AddPatientDocument>
                            </soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'AddPatientDocument');
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
                
                    $documentData = [];
                    // save code here
                }
            }
        }
        return $documentData;
    }
    
    public static function getVisitCalenderdata($details, $startDate, $endDate)
    {
        $final = [];
        if(isset($details->agencyDetails->app_name) && $details->agencyDetails->app_name !=""){
            $AppName = $details->agencyDetails->app_name;
            $AppSecret = $details->agencyDetails->app_key;
            $AppKey = $details->agencyDetails->app_token;
            $xml_post_string = '<soap:Envelope
                                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                <soap:Body>
                                    <SearchVisits
                                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                        <Authentication>
                                                <AppName>' . $AppName . '</AppName>
                                                <AppSecret>' . $AppSecret . '</AppSecret>
                                                <AppKey>' . $AppKey . '</AppKey>
                                        </Authentication>
                                        <SearchFilters>
                                            <StartDate>' . $startDate . '</StartDate>
                                            <EndDate>' . $endDate . '</EndDate>
                                            <CaregiverID xsi:nil="true" />
                                            <PatientID>' . $details->patient_id . '</PatientID>
                                            <OfficeID xsi:nil="true" />
                                        </SearchFilters>
                                    </SearchVisits>
                                </soap:Body>
                            </soap:Envelope>';
                if($details->agencyDetails->id == self::STATIC_AGENCY_ID){
                    $json = Self::getDataDemo($xml_post_string, 'SearchVisits');
                }else{
                    $json = Self::getData($xml_post_string, 'SearchVisits');
                }
            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                // Set HTTP response status code to: 500 - Internal Server Error
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
                $visitIDs = array();
                if (isset($xml->Body->SearchVisitsResponse->SearchVisitsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchVisitsResponse->SearchVisitsResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits)) {
                        $respoe = count($xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits->VisitID);

                        for ($i = 0; $i < $respoe; $i++) {

                            $visitID = $xml->Body->SearchVisitsResponse->SearchVisitsResult->Visits->VisitID[$i];
                            $visitIDs[] = addslashes($visitID);
                        }
                    }
                }

                foreach ($visitIDs as $visit) {
                   $final[] = SELF::getScheduleInfoData($visit, $AppName, $AppSecret, $AppKey);
                }
            }
        }
        return $final;
    }

    public static function getScheduleInfoData($visitId, $AppName, $AppSecret, $AppKey)
    {
        $finalData = [];
        $xml_post_string = '<soap:Envelope
                                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                <soap:Body>
                                    <GetScheduleInfo
                                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                        <Authentication>
                                            <AppName>' . $AppName . '</AppName>
                                            <AppSecret>' . $AppSecret . '</AppSecret>
                                            <AppKey>' . $AppKey . '</AppKey>
                                        </Authentication>
                                        <ScheduleInfo>
                                            <ID>' . $visitId . '</ID>
                                        </ScheduleInfo>
                                    </GetScheduleInfo>
                                </soap:Body>
                            </soap:Envelope>';
        $json = Self::getData($xml_post_string, 'GetScheduleInfo');

        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);

            if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->Result->ErrorInfo->ErrorID == 0) {

                if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo)) {
                    $PatientId = "";
                    $VisitDate = "";
                    $AdmissionNumber = "";
                    $PatientFirstName = "";
                    $PatientLastName = "";
                    $CaregiverId = "";
                    $CaregiverCode = "";
                    $CaregiverFirstName = "";
                    $CaregiverLastName = "";
                    $StartTime = "";
                    $EndTime = "";
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->VisitDate)) {
                        $VisitDate = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->VisitDate;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->ID)) {
                        $PatientId = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->ID;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->AdmissionNumber)) {
                        $AdmissionNumber = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->AdmissionNumber;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->FirstName)) {
                        $PatientFirstName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->FirstName;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->LastName)) {
                        $PatientLastName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->LastName;
                    }

                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->ID)) {
                        $CaregiverId = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->ID;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->CaregiverCode)) {
                        $CaregiverCode = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->CaregiverCode;
                    }

                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->FirstName)) {
                        $CaregiverFirstName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->FirstName;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->LastName)) {
                        $CaregiverLastName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->LastName;
                    }

                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleStartTime)) {
                        $StartTime = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleStartTime;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleEndTime)) {
                        $EndTime = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->ScheduleEndTime;
                    }

                    $finalData = array(
                        'visit_id' => addslashes($visitId),
                        'patient_id' => addslashes($PatientId),
                        'visit_date' => addslashes($VisitDate),
                        'patient_first_name' => addslashes($PatientFirstName),
                        'patient_last_name' => addslashes($PatientLastName),
                        'admission_id' => addslashes($AdmissionNumber),
                        'caregiver_id' => addslashes($CaregiverId),
                        'caregiver_code' => addslashes($CaregiverCode),
                        'caregiver_first_name' => addslashes($PatientFirstName),
                        'caregiver_last_name' => addslashes($PatientLastName),
                        'schedule_start_time' => addslashes($StartTime),
                        'schedule_end_time' => addslashes($EndTime)
                    );
                }
            }
            return $finalData;
        }
    }
    public static function getDisciplineData($id){
      
        $details = Self::commonDetails($id);
        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <GetPatientDisciplines
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <PatientID>'. $id .'</PatientID>
                                </GetPatientDisciplines>
                            </soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'GetPatientDisciplines');
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
         
                    if (isset($xml->Body->GetPatientDisciplinesResponse->GetPatientDisciplinesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientDisciplinesResponse->GetPatientDisciplinesResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->GetPatientDisciplinesResponse->GetPatientDisciplinesResult->PatientDisciplines)) {
                            $respoe = count($xml->Body->GetPatientDisciplinesResponse->GetPatientDisciplinesResult->PatientDisciplines);
                            $response = (array)$xml->Body->GetPatientDisciplinesResponse->GetPatientDisciplinesResult->PatientDisciplines;
                           
                            if(isset($response['PatientDiscipline'])){
                                foreach($response['PatientDiscipline'] as $val){
                                    $documentData[] = array(
                                        'disciplineID' => addslashes($val->DisciplineID),
                                        'disciplineName' => addslashes($val->DisciplineName)
                                    );
                                }
                            } 
                        }
                    }
                }
            }
        }
        return $documentData;
    }

    public static function getContractData($id){
      
        $details = Self::commonDetails($id);
        
        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <GetPatientContracts
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <PatientID>'. $id .'</PatientID>
                                </GetPatientContracts>
                            </soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'GetPatientContracts');
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
         
                    if (isset($xml->Body->GetPatientContractsResponse->GetPatientContractsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientContractsResponse->GetPatientContractsResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->GetPatientContractsResponse->GetPatientContractsResult->PatientContracts)) {
                            $respoe = count($xml->Body->GetPatientContractsResponse->GetPatientContractsResult->PatientContracts);
                            $response = (array)$xml->Body->GetPatientContractsResponse->GetPatientContractsResult->PatientContracts;
                           
                            if(isset($response['PatientContractInfo'])){
                                foreach($response['PatientContractInfo'] as $val){
                                    $documentData[] = array(
                                        'placementID' => addslashes($val->PlacementID),
                                        'contract' => addslashes($val->Contract),
                                        'isPrimaryContract' => addslashes($val->IsPrimaryContract),
                                        'altPatientID' => addslashes($val->AltPatientID),
                                        'serviceStartDate' => addslashes($val->ServiceStartDate),
                                        'sourceOfAdmission' => addslashes($val->SourceOfAdmission),
                                        'serviceCode' => addslashes($val->ServiceCode),
                                        'dischargeDate' => addslashes($val->DischargeDate),
                                        'dischargeTo' => addslashes($val->DischargeTo),
                                    );
                                }
                            } 
                        }
                    }
            }
            }
        }
        return $documentData;
    }

    public static function getPrefrencesData($id){
      
        $details = Self::commonDetails($id);
        $prefreanceData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <GetPatientPreferences
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <PatientID>'. $id .'</PatientID>
                                </GetPatientPreferences>
                            </soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'GetPatientPreferences');
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
         
                    if (isset($xml->Body->GetPatientPreferencesResponse->GetPatientPreferencesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientPreferencesResponse->GetPatientPreferencesResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->GetPatientPreferencesResponse->GetPatientPreferencesResult->GetPatientPreferencesInfo)) {
                            $respoe = count($xml->Body->GetPatientPreferencesResponse->GetPatientPreferencesResult->GetPatientPreferencesInfo);
                            $response = (array)$xml->Body->GetPatientPreferencesResponse->GetPatientPreferencesResult->GetPatientPreferencesInfo;
                           
                            if(isset($response['PreferenceInfo'])){
                                foreach($response['PreferenceInfo'] as $val){
                                    $prefreanceData['preferenceInfo'][] = array(
                                        'preferenceID' => addslashes($val->PreferenceID),
                                        'preferenceName' => addslashes($val->PreferenceName),
                                        'preferenceValue' => addslashes($val->PreferenceValue),
                                        'PreferenceType' => addslashes($val->PreferenceType),
                                    );
                                }
                                $prefreanceData['RequestGenderID'] = $response['PreferenceInfo']->$response['PreferenceInfo'];
                                $prefreanceData['RequestGender'] = $response['PreferenceInfo']->$response['RequestGender'];
                                $prefreanceData['PrimaryLanguageID'] = $response['PreferenceInfo']->$response['PrimaryLanguageID'];
                                $prefreanceData['PrimaryLanguage'] = $response['PreferenceInfo']->$response['PrimaryLanguage'];
                                $prefreanceData['SecondaryLanguageID'] = $response['PreferenceInfo']->$response['SecondaryLanguageID'];
                                $prefreanceData['SecondaryLanguage'] = $response['PreferenceInfo']->$response['SecondaryLanguage'];
                                $prefreanceData['RequestOther'] = $response['PreferenceInfo']->$response['RequestOther'];
                            }
                        }
                    }
                }
            }
        }
        return $prefreanceData;
    }

    public static function getDownloadDocumentData($id,$docid){
      
        $details = Self::commonDetails($id);
        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <DownloadPatientDocument
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <PatientDocID>'. $docid .'</PatientDocID>
                                </DownloadPatientDocument>
                            </soap:Body>
                        </soap:Envelope>';
                if(self::STATIC_AGENCY_ID ==  $details->agencyDetails->id){
                    $json = SELF::getDataDemo($xml, 'DownloadPatientDocument');
                }else{
                    $json = SELF::getData($xml, 'DownloadPatientDocument');
                }
                
                if ($json === false) {
                    $json = json_encode(array("jsonError", json_last_error_msg()));
                    if ($json === false) {
                        $json = '{"jsonError": "unknown"}';
                    }
                    http_response_code(500);
                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
         
                    if (isset($xml->Body->DownloadPatientDocumentResponse->DownloadPatientDocumentResult->Result->ErrorInfo->ErrorID) && $xml->Body->DownloadPatientDocumentResponse->DownloadPatientDocumentResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->DownloadPatientDocumentResponse->DownloadPatientDocumentResult->PatientDocument)) {
                            $respoe = count($xml->Body->DownloadPatientDocumentResponse->DownloadPatientDocumentResult->PatientDocument);
                            $response = $xml->Body->DownloadPatientDocumentResponse->DownloadPatientDocumentResult->PatientDocument;
                           
                            if(isset($response)){
                            $documentData = array(
                                        'patientDocID' => addslashes($response->PatientDocID),
                                        'contentType' => addslashes($response->ContentType),
                                        'fileName' => addslashes($response->FileName),
                                        'streamData' => addslashes($response->StreamData),
                                        'fileSize' => addslashes($response->FileSize),
                                    );
                                }
                            }
                        }
                    }
                }
            }
        return $documentData;
    }

    public static function autoSYNCPatientWithStatus($agency,$status){
        $finalPatientIds = [];
        if (!empty($agency->app_name) && !empty($agency->app_key)  && !empty($agency->app_token)) {
           
            $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Body>
                            <SearchPatients xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                <Authentication>
                                    <AppName>' . $agency->app_name . '</AppName>
                                    <AppSecret>' . $agency->app_key . '</AppSecret>
                                    <AppKey>' . $agency->app_token . '</AppKey>
                                </Authentication>
                                <SearchFilters>
                                        <Status>'.$status.'</Status>
                            </SearchFilters>
                            </SearchPatients>
                    </soap:Body>
                    </soap:Envelope>';
           
            $json = SELF::getData($xml, 'SearchPatients');
            if ($json === false) {

            }else{
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
                $temp =[];
                if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID)) {
                        $cnt_Patients = count($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID);
                        for ($i = 0; $i < $cnt_Patients; $i++) {
                            $PatientID = (array)$xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients->PatientID[$i];
                            $temp[] = $PatientID[0];
                        } 

                        $finalPatientIds = $temp;
                    }
                }
            }
            
        }
        return  $finalPatientIds;
    }

    public static function allDataSyncPatients($PatientId, $agencyHHADetail)
    {
        
        $finalArray = [];
        if (!empty($agencyHHADetail->app_name) && !empty($agencyHHADetail->app_key)  && !empty($agencyHHADetail->app_token)) {
            $xml = '<soap:Envelope
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <GetPatientDemographics
                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>' . $agencyHHADetail->app_name . '</AppName>
                            <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                            <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                        </Authentication>
                        <PatientInfo>
                            <ID>'.$PatientId.'</ID>
                        </PatientInfo>
                    </GetPatientDemographics>
                </soap:Body>
            </soap:Envelope>';
    
    
            $json = SELF::getData($xml, 'GetPatientDemographics', $agencyHHADetail->agency_id);
            
            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
               
                if (isset($xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo)) {
                        $patientDetailInfo = $xml->Body->GetPatientDemographicsResponse->GetPatientDemographicsResult->PatientInfo;
                       
                        $agencyID = (array)$patientDetailInfo->AgencyID;
                        $officeID = (array)$patientDetailInfo->OfficeID;
                        $firstName = (array)$patientDetailInfo->FirstName;
                        $middleName = (array)$patientDetailInfo->MiddleName;
                        $lastName = (array)$patientDetailInfo->LastName;
                        $dob = (array)$patientDetailInfo->BirthDate;
                        $gender = (array)$patientDetailInfo->Gender;
                        $coordinatorId ="";
                        $coordinatorName ="";
                        if (isset($patientDetailInfo->Coordinators->Coordinator[0]->ID)) {
                            $coordinatorDetails = $patientDetailInfo->Coordinators->Coordinator[0];
                            $coordinatorId =(array)$coordinatorDetails->ID;
                            $coordinatorName =(array)$coordinatorDetails->Name;
                        }
                        $coordinator_id = $coordinatorId;
                        $coordinator_name = $coordinatorName;
                        $PriorityCode = (array)$patientDetailInfo->PriorityCode;
                        $service_start_date = (array)$patientDetailInfo->ServiceRequestStartDate;
                        $nurseId = "";
                        $nurseName="";
                        if (isset($patientDetailInfo->Nurse[0]->ID)) {
                            $nurseDetails = $patientDetailInfo->Nurse[0];
                            $nurseId =(array)$nurseDetails->ID;
                            $nurseName =(array)$nurseDetails->Name;
                        }
                        $admission_id = (array)$patientDetailInfo->AdmissionID;
                        $PatientID = (array)$patientDetailInfo->PatientID;
                        $medicaid_number = (array)$patientDetailInfo->MedicaidNumber;
                        $medicare_number = (array)$patientDetailInfo->MedicareNumber;
                        $discipline = "";
                        if (isset($patientDetailInfo->AcceptedServices[0]->Discipline)) {
                            $acceptedServicesDetails = $patientDetailInfo->AcceptedServices[0];
                            $discipline =(array)$acceptedServicesDetails->Discipline;
                        }
                        $ssn = (array)$patientDetailInfo->SSN;
                        $alerts = (array)$patientDetailInfo->Alerts;
                        $SourceOfAdmissionId ="";
                        $SourceOfAdmissionName="";
                        if (isset($patientDetailInfo->SourceOfAdmission[0]->ID)) {
                            $sourceOfAdmissionDetails = $patientDetailInfo->SourceOfAdmission[0];
                            $SourceOfAdmissionId =(array)$sourceOfAdmissionDetails->ID;
                            $SourceOfAdmissionName =(array)$sourceOfAdmissionDetails->Name;
                        }
                        $teamId = "";
                        $teamName = "";
                        if (isset($patientDetailInfo->Team[0]->ID)) {
                            $teamDetails = $patientDetailInfo->Team[0];
                            $teamId =(array)$teamDetails->ID;
                            $teamName =(array)$teamDetails->Name;
                        }
                        $locationId = "";
                        $locationName = "";
                        if (isset($patientDetailInfo->Location[0]->ID)) {
                            $locationDetails = $patientDetailInfo->Location[0];
                            $locationId =(array)$locationDetails->ID;
                            $locationName =(array)$locationDetails->Name;
                        }
                        $branchId = "";
                        $branchName = "";
                        if (isset($patientDetailInfo->Branch[0]->ID)) {
                            $BranchDetails = $patientDetailInfo->Branch[0];
                            $branchId =(array)$BranchDetails->ID;
                            $branchName =(array)$BranchDetails->Name;
                        }
                        $address1 ="";
                        $address2 ="";
                        $cross_street ="";
                        $city ="";
                        $zip5 ="";
                        $state ="";
                        $county ="";
                        $home_phone ="";
                        $phone2 ="";
                        if (isset($patientDetailInfo->Addresses->Address)) {
                            $addressDetails = $patientDetailInfo->Addresses->Address;
                            $address1 =(array)$addressDetails->Address1;
                            $address2 =(array)$addressDetails->Address2;
                            $cross_street =(array)$addressDetails->CrossStreet;
                            $city =(array)$addressDetails->City;
                            $zip5 =(array)$addressDetails->Zip5;
                            $state =(array)$addressDetails->State;
                            $county =(array)$addressDetails->County;
                        }
    
                        $home_phone =(array)$patientDetailInfo->HomePhone;
                        $phone2 =(array)$patientDetailInfo->Phone2;
                        $phone2Description = (array)$patientDetailInfo->Phone2Description;
                        $phone3 = (array)$patientDetailInfo->Phone3;
                        $phone3Description = (array)$patientDetailInfo->Phone3Description;
    
                        $homePhoneLocationAddressID = (array)$patientDetailInfo->HomePhoneLocationAddressID;
                        $homePhoneLocationAddress = (array)$patientDetailInfo->HomePhoneLocationAddress;
                        $homePhone2LocationAddressID = (array)$patientDetailInfo->HomePhone2LocationAddressID;
                        $homePhone2LocationAddress = (array)$patientDetailInfo->HomePhone2LocationAddress;
                        $homePhone3LocationAddressID = (array)$patientDetailInfo->HomePhone3LocationAddressID;
                        $homePhone3LocationAddress = (array)$patientDetailInfo->HomePhone3LocationAddress;
                        $direction = (array)$patientDetailInfo->Direction;
    
                        $alternateBillingFirstName = "";
                        $alternateBillingMiddleName = "";
                        $alternateBillingLastName = "";
                        $alternateBillingStreet = "";
                        $alternateBillingCity = "";
                        $alternateBillingState = "";
                        $alternateBillingZip5 = "";
                        if (isset($patientDetailInfo->AlternateBilling[0]->FirstName)) {
                            $AlternateBillingDetails = $patientDetailInfo->AlternateBilling[0];
                            $alternateBillingFirstName =(array)$AlternateBillingDetails->FirstName;
                            $alternateBillingMiddleName =(array)$AlternateBillingDetails->MiddleName;
                            $alternateBillingLastName =(array)$AlternateBillingDetails->LastName;
                            $alternateBillingStreet =(array)$AlternateBillingDetails->Street;
                            $alternateBillingCity =(array)$AlternateBillingDetails->City;
                            $alternateBillingState =(array)$AlternateBillingDetails->State;
                            $alternateBillingZip5 =(array)$AlternateBillingDetails->Zip5;
                        }
    
                        $emergencyContactName = "";
                        $emergencyContactLivesWithPatient = "";
                        $emergencyContactHaveKeys = "";
                        $emergencyContactPhone1 = "";
                        $emergencyContactPhone2 = "";
                        $emergencyContactAddress = "";
                        if (isset($patientDetailInfo->EmergencyContacts->EmergencyContact)) {
                            $emergencyContactDetails = $patientDetailInfo->EmergencyContacts->EmergencyContact[0];
                            $emergencyContactName =(array)$emergencyContactDetails->Name;
                            $emergencyContactLivesWithPatient =(array)$emergencyContactDetails->LivesWithPatient;
                            $emergencyContactHaveKeys =(array)$emergencyContactDetails->HaveKeys;
                            $emergencyContactPhone1 =(array)$emergencyContactDetails->Phone1;
                            $emergencyContactPhone2 =(array)$emergencyContactDetails->Phone2;
                            $emergencyContactAddress =(array)$emergencyContactDetails->Address;
                        }
    
                        $payerID = (array)$patientDetailInfo->PayerID;
                        $payerName = (array)$patientDetailInfo->PayerName;
                        $payerCoordinatorID = (array)$patientDetailInfo->PayerCoordinatorID;
                        $payerCoordinatorName = (array)$patientDetailInfo->PayerCoordinatorName;
                        $patientStatusID = (array)$patientDetailInfo->PatientStatusID;
                        $patientStatusName = (array)$patientDetailInfo->PatientStatusName;
                        $wageParity = (array)$patientDetailInfo->WageParity;
                        $wageParityFromDate1 = (array)$patientDetailInfo->WageParityFromDate1;
                        $wageParityToDate1 = (array)$patientDetailInfo->WageParityToDate1;
                        $wageParityFromDate2 = (array)$patientDetailInfo->WageParityFromDate2;
                        $wageParityToDate2 = (array)$patientDetailInfo->WageParityToDate2;
                        $primaryLanguageID = (array)$patientDetailInfo->PrimaryLanguageID;
                        $primaryLanguage = (array)$patientDetailInfo->PrimaryLanguage;
    
    
                        $finalArray = [
                            "firstName" => isset($firstName[0])?$firstName[0]:"",
                            "lastName" => isset($lastName[0])?$lastName[0]:"",
                            'middleName' => isset($middleName[0])?$middleName[0]:"",
                            'gender' => isset($gender[0])?$gender[0]:"",
                            'dob' => isset($dob[0])?date('m/d/Y',strtotime($dob[0])):"",
                            'admission_id' => isset($admission_id[0])?$admission_id[0]:"",
                            'coordinator_id' => isset($coordinator_id[0])?$coordinator_id[0]:"",
                            'coordinator_name' => isset($coordinator_name[0])?$coordinator_name[0]:"",
                            'service_start_date' => isset($service_start_date[0]) ? date('m/d/Y',strtotime($service_start_date[0])) : "",
                            'medicaid_number' => isset($medicaid_number[0])?$medicaid_number[0]:"",
                            'medicare_number' => isset($medicare_number[0])?$medicare_number[0]:"",
                            'address1' => isset($address1[0])?$address1[0]:"",
                            'address2' => isset($address2[0])?$address2[0]:"",
                            'cross_street' => isset($cross_street[0])?$cross_street[0]:"",
                            'city' => isset($city[0])?$city[0]:"",
                            'zip5' => isset($zip5[0])?$zip5[0]:"",
                            'state' => isset($state[0])?$state[0]:"",
                            'county' => isset($county[0])?$county[0]:"",
                            'home_phone' =>isset($home_phone[0])? str_replace('-', '', $home_phone[0]):"",
                            'phone2' => isset($phone2[0])? str_replace('-', '',$phone2[0]):"",
                            'officeId' => isset($officeID[0])?$officeID[0]:"",
                            'PriorityCode' => isset($PriorityCode[0])?$PriorityCode[0]:"",
                            'nurseId' => isset($nurseId[0])?$nurseId[0]:"",
                            'nurseName' => isset($nurseName[0])?$nurseName[0]:"",
                            'PatientID' => isset($PatientID[0])?$PatientID[0]:"",
                            'discipline' => isset($discipline[0])?$discipline[0]:"",
                            'ssn' => isset($ssn[0])?$ssn[0]:"",
                            'alerts' => isset($alerts[0])?$alerts[0]:"",
                            'teamId' => isset($teamId[0])?$teamId[0]:"",
                            'SourceOfAdmissionId' => isset($SourceOfAdmissionId[0])?$SourceOfAdmissionId[0]:"",
                            'SourceOfAdmissionName' => isset($SourceOfAdmissionName[0])?$SourceOfAdmissionName[0]:"",
                            'emergencyContactName' => isset($emergencyContactName[0])?$emergencyContactName[0]:"",
                            'teamName' => isset($teamName[0])?$teamName[0]:"",
                            'locationId' => isset($locationId[0])?$locationId[0]:"",
                            'locationName' => isset($locationName[0])?$locationName[0]:"",
                            'branchId' => isset($branchId[0])?$branchId[0]:"",
                            'branchName' => isset($branchName[0])?$branchName[0]:"",
                            'phone2Description' => isset($phone2Description[0])?$phone2Description[0]:"",
                            'phone3' => isset($phone3[0])?$phone3[0]:"",
                            'phone3Description' => isset($phone3Description[0])?$phone3Description[0]:"",
                            'homePhoneLocationAddressID' => isset($homePhoneLocationAddressID[0])?$homePhoneLocationAddressID[0]:"",
                            'homePhoneLocationAddress' => isset($homePhoneLocationAddress[0])?$homePhoneLocationAddress[0]:"",
                            'homePhone2LocationAddressID' => isset($homePhone2LocationAddressID[0])?$homePhone2LocationAddressID[0]:"",
                            'homePhone2LocationAddress' => isset($homePhone2LocationAddress[0])?$homePhone2LocationAddress[0]:"",
                            'homePhone3LocationAddressID' => isset($homePhone3LocationAddressID[0])?$homePhone3LocationAddressID[0]:"",
                            'homePhone3LocationAddress' => isset($homePhone3LocationAddress[0])?$homePhone3LocationAddress[0]:"",
                            'direction' => isset($direction[0])?$direction[0]:"",
                            'alternateBillingFirstName' => isset($alternateBillingFirstName[0])?$alternateBillingFirstName[0]:"",
                            'alternateBillingMiddleName' => isset($alternateBillingMiddleName[0])?$alternateBillingMiddleName[0]:"",
                            'alternateBillingLastName' => isset($alternateBillingLastName[0])?$alternateBillingLastName[0]:"",
                            'alternateBillingStreet' => isset($alternateBillingStreet[0])?$alternateBillingStreet[0]:"",
                            'alternateBillingCity' => isset($alternateBillingCity[0])?$alternateBillingCity[0]:"",
                            'alternateBillingState' => isset($alternateBillingState[0])?$alternateBillingState[0]:"",
                            'alternateBillingZip5' => isset($alternateBillingZip5[0])?$alternateBillingZip5[0]:"",
                            'emergencyContactName' => isset($emergencyContactName[0])?$emergencyContactName[0]:"",
                            'emergencyContactLivesWithPatient' => isset($emergencyContactLivesWithPatient[0])?$emergencyContactLivesWithPatient[0]:"",
                            'emergencyContactHaveKeys' => isset($emergencyContactHaveKeys[0])?$emergencyContactHaveKeys[0]:"",
                            'emergencyContactPhone1' => isset($emergencyContactPhone1[0])?$emergencyContactPhone1[0]:"",
                            'emergencyContactPhone2' => isset($emergencyContactPhone2[0])?$emergencyContactPhone2[0]:"",
                            'emergencyContactAddress' => isset($emergencyContactAddress[0])?$emergencyContactAddress[0]:"",
                            'payerID' => isset($payerID[0])?$payerID[0]:"",
                            'payerName' => isset($payerName[0])?$payerName[0]:"",
                            'payerCoordinatorID' => isset($payerCoordinatorID[0])?$payerCoordinatorID[0]:"",
                            'payerCoordinatorName' => isset($payerCoordinatorName[0])?$payerCoordinatorName[0]:"",
                            'patientStatusID' => isset($patientStatusID[0])?$patientStatusID[0]:"",
                            'patientStatusName' => isset($patientStatusName[0])?$patientStatusName[0]:"",
                            'wageParity' => isset($wageParity[0])?$wageParity[0]:"",
                            'wageParityFromDate1' => isset($wageParityFromDate1[0])?$wageParityFromDate1[0]:"",
                            'wageParityToDate1' => isset($wageParityToDate1[0])?$wageParityToDate1[0]:"",
                            'wageParityFromDate2' => isset($wageParityFromDate2[0])?$wageParityFromDate2[0]:"",
                            'wageParityToDate2' => isset($wageParityToDate2[0])?$wageParityToDate2[0]:"",
                            'primaryLanguageID' => isset($primaryLanguageID[0])?$primaryLanguageID[0]:"",
                            'primaryLanguage' => isset($primaryLanguage[0])?$primaryLanguage[0]:"",
                            'discipline_new' => isset($discipline[0])?implode(',',$discipline):"",
                        ];
    
                    }
                }
            }  
        }
        return $finalArray;
    }

    public static function getModifieldPatientIds($details){
      
        $finalArray = [];
        if(isset($details->app_name) && $details->app_key !=""){
            $xml_post_string = '<soap:Envelope
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <GetPatientChangesV2
                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>'.$details->app_name.'</AppName>
                            <AppSecret>'.$details->app_key.'</AppSecret>
                            <AppKey>'.$details->app_token.'</AppKey>
                        </Authentication>
                        <OfficeID>0</OfficeID>
                        <ModifiedAfter>'.date('Y-m-d\TH:i:s',strtotime('-3 days')).'</ModifiedAfter>
                    </GetPatientChangesV2>
                </soap:Body>
            </soap:Envelope>';
            $json = Self::getData($xml_post_string, 'GetPatientChangesV2');
            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                // Set HTTP response status code to: 500 - Internal Server Error
                http_response_code(500);
            } else {
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
                if (isset($xml->Body->GetPatientChangesV2Response->GetPatientChangesV2Result->Result->ErrorInfo->ErrorID) && $xml->Body->GetPatientChangesV2Response->GetPatientChangesV2Result->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetPatientChangesV2Response->GetPatientChangesV2Result->GetPatientChangesV2Info)) {
                            $response = $xml->Body->GetPatientChangesV2Response->GetPatientChangesV2Result->GetPatientChangesV2Info;
                            foreach($response as $val){
                                $finalArray[]  =addslashes($val->PatientID);
                            }

                    }

                }
            }
        }
        return $finalArray;
    }

    public static function searchPatientForHHAWithAllCondition($agencyId, $search,$flag=""){

        $final = [];
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
        $xml = '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <SearchPatients
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                    <AppName>' . $agencyHHADetail->app_name . '</AppName>
                    <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                    <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                    <SearchFilters>
                        <FirstName>'.$search['hha_patient_first_name'].'</FirstName>
                        <LastName>'.$search['hha_patient_last_name'].'</LastName>
                        <PhoneNumber>'.$search['hha_patient_phone_no'].'</PhoneNumber>';
                        if(isset($search['status']) && $search['status'] !=""){
                            $xml .='<Status>'.$search['status'].'</Status>';
                        }else{
                            $xml .='<Status>All</Status>';
                        }
                        if(isset($search['hha_patient_code_id']) && !empty($search['hha_patient_code_id'])){
                            $xml .= '<AdmissionID>'.$search['hha_patient_code_id'].'</AdmissionID>';
                        }
                        if(isset($search['hha_patient_ssn']) && !empty($search['hha_patient_ssn'])){
                            $xml .= '<SSN>'.$search['hha_patient_ssn'].'</SSN>';
                        }
                    $xml .= '</SearchFilters>
                </SearchPatients>
            </soap:Body>
        </soap:Envelope>';


        if ($agencyId == self::STATIC_AGENCY_ID) {
            $json = SELF::getDataDemo($xml, 'SearchPatients');
        } else {
            $json = SELF::getData($xml, 'SearchPatients');
        }
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);

            if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchPatientsResponse->SearchPatientsResult->Result->ErrorInfo->ErrorID == 0) {

                if (isset($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients)) {

                    $respoe = count($xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients);
                    for ($i = 0; $i < $respoe; $i++) {
                        $caregiverId = $xml->Body->SearchPatientsResponse->SearchPatientsResult->Patients[$i]->PatientID;
                        foreach($caregiverId as $val){
                            $getDetails  = self::patientDemographicDetails(addslashes($val),$agencyId);
                            $final[]=$getDetails;
                        }
                    }
                }
            }
  
            if($flag !=""){
                $temp = $final;
            }else{
                $temp = [];

                if(!empty($final[0])){
                    foreach($final as $val){
                        $tTemp = [];
                        $tTemp['patient_id'] = $val['PatientID'];
                        $tTemp['patient_name'] = $val['firstName'].' '.$val['lastName'];
                        $tTemp['status'] = $val['patientStatusName'];
                        $tTemp['admission_id'] = $val['admission_id'];
                        $tTemp['gender'] = $val['gender'];
                        $temp[] = $tTemp;
                    }
                }
            }

            $final = $temp;
        }
        return $final;
    }

    public static function getDataDemo($xml, $action)
    { 
        $headers = array(
            "POST /Integration/ENT/V1.8/ws.asmx",
            "Host: implementation.hhaexchange.com",
            "Content-Type: text/xml;charset=utf-8",
            "Content-Length: " . strlen($xml),
            "SOAPAction: https://www.hhaexchange.com/apis/hhaws.integration/" . $action
        );

        $url = "https://implementation.hhaexchange.com/Integration/ENT/V1.8/ws.asmx?op=" . $action;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        return curl_exec($ch);

    }

    private static function commonAgencyDetails($agencyID){
        return Agency::getAllDetailsbyAgencyId($agencyID);
    }
}
