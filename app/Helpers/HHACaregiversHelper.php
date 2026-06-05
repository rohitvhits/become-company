<?php

namespace App\Helpers;

use App\Agency;
use App\Model\HHACaregivers;
use Illuminate\Http\Request;
use App\Model\HhaAppointment;
use App\Model\HhaOtherComplience;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;

use App\Model\DocumentPatient;
use App\Model\HHAVisit;
use App\Helpers\HHACaregiverNotesHelper;
use App\Model\SendHhaDocumentLog;

class HHACaregiversHelper
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
    public static function getCaregiverIDListByAgencyId($agencyID)

    {

        $agencyDetails = Agency::getHHAAgencyList();
        if ($agencyID > 0) {
            $agencyDetails = Agency::where('id', $agencyID)->get();
        }


        foreach ($agencyDetails as $agency) {
           // echo "In agency <br/>";
            if (!empty($agency->app_name) && !empty($agency->app_key)  && !empty($agency->app_token)) {
             //   echo "In agency data";

                $flagUpdate = HHACaregivers::updateData(array('hha_delete_flag' => 'Y'), array('agency_fk' => $agency->id));
                $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
						<soap:Body>
								<SearchCaregivers xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
									<Authentication>
										<AppName>' . $agency->app_name . '</AppName>
										<AppSecret>' . $agency->app_key . '</AppSecret>
										<AppKey>' . $agency->app_token . '</AppKey>
									</Authentication>
									<SearchFilters>
											<Status>Active</Status>
								</SearchFilters>
								</SearchCaregivers>
						</soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'SearchCaregivers');

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

                    if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID)) {
                            $cnt_Patients = count($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID);

                            for ($i = 0; $i < $cnt_Patients; $i++) {
                                $caregiverID = $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID[$i];

                                HHACaregivers::updateOrCreate([
                                    "agency_fk"      => $agency->id,
                                    "caregiver_id"        => addslashes($caregiverID),
                                ], [

                                    'hha_delete_flag' => 'N',
                                ]);
                            }

                            return 1;
                        }
                    } else {
                        $error = (array)$xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorMessage ?? ['Something happened. Try again.'];
                        return $error[0];
                    }
                }
            }
        }
    }

    public static function GetCaregiverMedicalDetailsByCaregiverId()
    {
        $caregiverIDs = HHACaregivers::getData();
        // $caregiverIDs = HHACaregivers::where("id","72048")->get();


        foreach ($caregiverIDs as $caregiver) {

           // $flagUpdate = HhaAppointment::updateData(array('del_flag' => 'Y'), array('agency_id' => $caregiver->agency_fk, 'caregiver_id' => $caregiver->caregiver_id));
            $agencyHHADetail = Agency::getAllDetailsbyAgencyId($caregiver->agency_fk);
            if(isset($agencyHHADetail->app_name)){
            $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
       <soap:Body>
          <GetCaregiverMedicalDetails xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
             <Authentication>
             <AppName>' . $agencyHHADetail->app_name??"" . '</AppName>
             <AppSecret>' . $agencyHHADetail->app_key??"" . '</AppSecret>
             <AppKey>' . $agencyHHADetail->app_token??"" . '</AppKey>
             </Authentication>
             <SearchFilter>
                <CaregiverID>' . $caregiver->caregiver_id . '</CaregiverID>
                <CaregiverComplianceExpItemID>-1</CaregiverComplianceExpItemID>
                <ComplianceStatus>All</ComplianceStatus>
             </SearchFilter>
          </GetCaregiverMedicalDetails>
       </soap:Body>
    </soap:Envelope>
        ';
        if($agencyHHADetail->id == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'GetCaregiverMedicalDetails');
        }else{
            $json = SELF::getData($xml, 'GetCaregiverMedicalDetails');
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
                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                        $respoe = count($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails);
                        for ($i = 0; $i < $respoe; $i++) {

                            $medicalID = '';
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID != '') {
                                $medicalID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID;
                            }
                            $medicalName = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName != '') {
                                $medicalName = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName;
                            }
                            $status = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status != '') {
                                $status = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status;
                            }
                            $caregiverID = "";
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID != '') {
                                $caregiverID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID;
                            }
                            $dueDate = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate != '') {
                                $dueDate = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate;
                            }
                            $officeID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                $officeID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID;
                            }


                            $CaregiverMedicalID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                $CaregiverMedicalID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID;
                            }

                            $datePerform = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed != '') {
                             
                                $datePerform = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed;
                            }



                            HhaAppointment::updateOrCreate([
                                'agency_id'        => $caregiver->agency_fk,
                                'caregiver_id' => $caregiverID,
                                'caregiver_medical_id' => $CaregiverMedicalID,
                            ], [
                                'medical_id' => $medicalID,
                                'medical_name' => $medicalName,
                               
                                'due_date' => $dueDate,
                                'status' => $status,
                                'office_id' => $officeID,
                                'caregiver_medical_id' => $CaregiverMedicalID,
                                'del_flag' => 'N',
                                'updated_date' => date('Y-m-d H:i:s'),
                                'date_perform' => $datePerform

                            ]);
                        }
                    }
                }
            }


            HHACaregivers::where('caregiver_id', $caregiver->caregiver_id)->update(array("last_medical_sync" => date('Y-m-d H:i:s')));
        }
        }

        return 1;
    }


    public static function GetCaregiverComplianceItemDueById($caregiver)
    {
    }
    
    public static function GetCaregiverComplianceItemDue($caregiverId,$type="1")
    {
        //$caregiverIDs = HHACaregivers::getData();
        //$caregiverIDs = HHACaregivers::where("id", "110413")->get();
        if($caregiverId==0){
            $caregiverIDs =  HHACaregivers::whereNull('deleted_at')->where('agency_fk', 106)->whereRaw("(hhasync_othecomplience is null or hhasync_othecomplience <'" . date('Y-m-d H:i:s', strtotime('-5 day')) . "')")->inRandomOrder()->limit(1000)->get();

        }else{
            $caregiverIDs =  HHACaregivers::whereNull('deleted_at')->where('id', $caregiverId)->get();
            if(!isset($caregiverIDs->id)){
                $caregiverIDs =  HHACaregivers::whereNull('deleted_at')->where('caregiver_id', $caregiverId)->get();
            }
        }
        
        $lastScanDetails = '0';
        
        $finalResponseOtherArray = [];
        foreach ($caregiverIDs as $caregiver) {

            if($type ==1){
                HhaOtherComplience::updateData(array('del_flag' => 'Y'), array('agency_id' => $caregiver->agency_fk, 'caregiver_id' => $caregiver->caregiver_id));
            }
            
            $agencyHHADetail = Agency::getAllDetailsbyAgencyId($caregiver->agency_fk);
            $otherMedicals = [];
            if ($lastScanDetails != $caregiver->agency_fk . '-' . $caregiver->officeId) {
                $otherMedicals = SELF::getCaregiverOtherCompliance($caregiver->agency_fk, $caregiver->officeId);
            }
            //  print_r($otherMedicals);
    
            foreach ($otherMedicals as $medicalIds) {
                $tempOtherMedicalArray = [];
                $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                    <soap:Body>
                        <GetCaregiverMedicalDetails xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                            <AppName>' . $agencyHHADetail->app_name . '</AppName>
                            <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                            <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                            </Authentication>
                            <SearchFilter>
                                <CaregiverID>' . $caregiver->caregiver_id . '</CaregiverID>
                            </SearchFilter>
                        </GetCaregiverMedicalDetails>
                    </soap:Body>
                </soap:Envelope>';

                if($caregiver->agency_fk == self::STATIC_AGENCY_ID){
                    $json = SELF::getDataDemo($xml, 'GetCaregiverMedicalDetails');
                }else{
                    $json = SELF::getData($xml, 'GetCaregiverMedicalDetails');
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

                    if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {

                        if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                            $respoe = count($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails);

                            for ($i = 0; $i < $respoe; $i++) {

                                $medicalID = '';
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID != '') {
                                  $medicalID1 = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID;
                                  $medicalID =addslashes($medicalID1);
                                }

                                $medicalName = NULL;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName != '') {
                                    $medicalName = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName);
                                }
                                $status = NULL;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status != '') {
                                    $status = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status);
                                }
                                $caregiverID = "";
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID != '') {
                                    $caregiverID = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID);
                                }
                                $dueDate = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate != '') {
                                    $dueDate = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate);
                                }
                                $officeID = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                    $officeID = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID);
                                }

                                $caregiverMedicalID = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                    $caregiverMedicalID = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID);
                                }

                                $datePerform = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed != '') {
                                    $datePerform = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed);
                                }

                                if($medicalIds['id'] ==$medicalID){
                                    if($type ==1){
                                        HhaOtherComplience::updateOrCreate([
                                            'agency_id'        => $caregiver->agency_fk,
                                            'caregiver_id' => $caregiverID,
                                            'caregiver_medical_id' => $caregiverMedicalID??"",
                                            'medical_id' => $medicalID,
                                        ], [
                                            'medical_id' => $medicalID??"",
                                            'medical_name' => $medicalName??"",
                                            'due_date' => $dueDate??"",
                                            'status' => $status??"",
                                            'office_id' => $officeID[0]??"",
                                            'caregiver_medical_id' => $caregiverMedicalID??"",
                                            'del_flag' => 'N',
                                            'updated_date' => date('Y-m-d H:i:s')
        
                                        ]);
                                    }else{
                                        $tempOtherMedicalArray =[
                                            'agency_id'=>$caregiver->agency_fk,
                                            'caregiver_id'=>$caregiverID,
                                            'caregiver_medical_id'=>$caregiverMedicalID??"",
                                            'due_date'=>$dueDate??"",
                                            'medical_id'=>$medicalID,
                                            'medical_name'=>$medicalName??"",
                                            'office_id'=>$officeID??"",
                                            'status'=>$status??"",
                                            'datePerform'=>$datePerform??""
                                        ];
    
                                        $finalResponseOtherArray[] = $tempOtherMedicalArray;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($type ==1){
                HHACaregivers::where('id', $caregiver->id)->update(array("hhasync_othecomplience" => date('Y-m-d H:i:s')));
            } 
        }

        if($type ==1){
            return 1;
        }else{
            return $finalResponseOtherArray;
        }
    }

    public static function GetCaregiverDetailByCareGiverID($caregiverId, $agencyID)
    {


        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);
        
        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				<soap:Body>
						<GetCaregiverDemographics xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
							<Authentication>
								<AppName>' . $agencyHHADetail->app_name . '</AppName>
								<AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
								<AppKey>' . $agencyHHADetail->app_token . '</AppKey>
							</Authentication>
							<CaregiverInfo>
									<ID>' . $caregiverId . '</ID>
						</CaregiverInfo>	
						</GetCaregiverDemographics>
				</soap:Body>
				</soap:Envelope>';


        $json = SELF::getData($xml, 'GetCaregiverDemographics', $agencyHHADetail->agency_id);

        // $caregiverContectList = HHACaregivers::where("agency_fk", $agencyID)
        //     ->where('caregiver_id', $caregiverId)
        //     ->update(array("hha_sync" => 'N'));

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
          
            $caregiverDetailInfo = $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo ?? '';

            if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID == 0) {
              
                if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo)) {
                   
                    $caregiverDetailInfo = $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo;
                   
                    $mobileOrSMS = "";
                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->MobileOrSMS)) {
                        $mobileOrSMS =  $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->MobileOrSMS;
                    }

                    
                    $firstName = (array)$caregiverDetailInfo->FirstName;
                    $lastName = (array)$caregiverDetailInfo->LastName;
                    $gender = (array)$caregiverDetailInfo->Gender;
                    $dob = (array)$caregiverDetailInfo->BirthDate;
                    $caregiverCode = (array)$caregiverDetailInfo->CaregiverCode;
                    $mobileOrSMS = (array)$mobileOrSMS;
                    $middleName = (array)$caregiverDetailInfo->MiddleName;
                   
                   
                    $phone = isset($mobileOrSMS[0]) ? $mobileOrSMS[0] : "";
                    $phone = str_replace("-", "", $phone);


                    $status = '';
                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Status)) {
                        $status = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Status->Name;
                        $status = $status[0]??"";
                        
                    }
                  
                    $LastWorkDate = '';
                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastWorkDate)) {
                        $LastWorkDate =  (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastWorkDate;
                        $LastWorkDate = isset($LastWorkDate[0]) ? $LastWorkDate[0] : "";
                    }
                    $TeamID = 0;
                    $TeamName = "";

                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team)) {

                        if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team->ID))

                            $TeamID = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team->ID;
                        $TeamID = isset($TeamID[0]) ? $TeamID[0] : "";


                        if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team->Name))
                            $TeamName = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team->Name;
                        $TeamName = isset($TeamName[0]) ? $TeamName[0] : "";
                    }
                    $BirthDate = "";
                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->BirthDate)) {
                        $BirthDate = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->BirthDate;
                        $BirthDate = isset($BirthDate[0]) ? $BirthDate[0] : "";
                    }
                    $EmploymentTypes = "";
                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypes)) {
                        $EmploymentTypesLists = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypes;
                        $EmploymentTypes = "";
                        if (isset($EmploymentTypesLists['Discipline'])) {
                            if (is_array($EmploymentTypesLists['Discipline'])) {
                                foreach ($EmploymentTypesLists['Discipline'] as $Discipline) {
                                    if ($EmploymentTypes == "") {
                                        $EmploymentTypes = $Discipline;
                                    } else {
                                        $EmploymentTypes .= "," . $Discipline;
                                    }
                                }
                            } else {
                                $EmploymentTypes = $EmploymentTypesLists['Discipline'];
                            }
                        } else {
                            //print_r($EmploymentTypesLists); die;
                        }
                    }

                    $EmploymentTypesDiscipline = "";

                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypesDiscipline)) {

                        $EmploymentTypesDiscipline = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypesDiscipline;
                        $EmploymentTypesDiscipline = isset($EmploymentTypesDiscipline[0]) ? $EmploymentTypesDiscipline[0] : "";
                    }
                    $Gender = "";
                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Gender)) {
                        $Gender = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Gender;
                        if(isset($Gender[0])){
                        $Gender = $Gender[0];
                    }
                    else{
                        // print_r($Gender); die();
                    }
                    }


                    $officeID = null;

                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeID)) {
                        $officeID = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeID;
                        $officeID = $officeID[0];
                    }

                    $officeName = null;

                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeName)) {
                        $officeName = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeName;
                        $officeName = $officeName[0];
                    }

                    $zipcode ="";

                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Zip5)){
                        $zipcode =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Zip5;
                        $zipcode =$zipcode[0];
                    }

                    $lang ="";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language1)){
                    
                        $lang =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language1;
                        $lang =isset($lang[0])?$lang[0]:"";
                    }

                    $address1 ="";
                    $address2 ="";
                    $city ="";
                    $state ="";
                    $zip5 ="";
                    $homePhone ="";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address)){
                    
                        $address1 =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Street1;
                        $address1 =isset($address1[0])?$address1[0]:"";

                        $address2 =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Street2;
                        $address2 =isset($address2[0])?$address2[0]:"";

                        $city =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->City;
                        $city =isset($city[0])?$city[0]:"";

                        $state =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->State;
                        $state =isset($state[0])?$state[0]:"";

                        $zip5 =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Zip5;
                        $zip5 =isset($zip5[0])?$zip5[0]:"";

                        $homePhone =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->HomePhone;
                        $homePhone =isset($homePhone[0])?str_replace('-','',$homePhone[0]):"";
                    }

                    $Language2 ="";
                    
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language2)){
                    
                        $Language2 =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language2;
                        $Language2 =isset($Language2[0])?$Language2[0]:"";
                    }
                    
                    $first_work_date ="";

                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language2)){
                    
                        $first_work_date =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstWorkDate;
                        $first_work_date =isset($first_work_date[0])?$first_work_date[0]:"";
                    }
                    
                    $updateArray = array(
                        "first_name" => isset($firstName[0]) ? $firstName[0] : "",
                        "last_name" => isset($lastName[0]) ? $lastName[0] : "",
                        'middle_name' => isset($middleName[0]) ? $middleName[0] : "",
                        'gender' => isset($gender[0]) ? $gender[0] : "",
                        'dob' => isset($dob[0]) ? $dob[0] : "",
                        'caregiver_code' => isset($caregiverCode[0]) ? $caregiverCode[0] : "",
                        'mobile_or_sms' => $phone,
                        'TeamName' => $TeamName,
                        'officeId' => $officeID,
                        'office_name' => $officeName,

                        'EmploymentTypesDiscipline' => $EmploymentTypes,
                        "hha_sync" => "Y",
                        "hhasyncdatetime" => date('Y-m-d H:i:s'),
                        
                         "last_work_date" =>$LastWorkDate,
                        'zipcode'=>$zipcode,
                        'language'=>$lang,
                        'status'=>$status,
                        'address1'=>$address1,
                        'address2'=>$address2,
                        'City'=>$city,
                        'State'=>$state,
                        'Zip5'=>$zip5,
                        'HomePhone'=>$homePhone,
                        'Language2'=>$Language2,
                        'first_work_date'=>$first_work_date,
                        'hha_sync'=>"Y",
                       
                       
                    );
                   
                    $caregiverContectList = HHACaregivers::where("agency_fk", $agencyID)
                        ->where('caregiver_id', $caregiverId)
                        ->update($updateArray);

                    if ($caregiverContectList) {
                    } else {
                        echo "<h1>Sync Process Done</h1>";
                    }
                }
            }
        }
    }

    public static function getunsynccaregiver()
    {
        //echo "dfgdg"; die();
        SELF::updateOfficeId();

        echo "dfgdg";
        die();


        //die("Synce done updateOfficeId" . count($caregiverList));

        $caregiverList = HHACaregivers::where("hha_sync", "N")->inRandomOrder()->limit(3000)->get();
        foreach ($caregiverList as $list) {
            $caregiver = HHACaregivers::where("hha_sync", "N")->where('id', $list->id)->first();
            if ($caregiver) {
                echo "<br/>" . $list->caregiver_id;

                if ($list->agency_fk != "") {
                    SELF::GetCaregiverDetailByCareGiverID($list->caregiver_id, $list->agency_fk);
                } else {
                    $caregiverList = HHACaregivers::where('id', $list->id)
                        ->update(array("hha_sync" => 'Y'));
                }
            }
        }
        if (count($caregiverList) == 90) {

            echo "<script>window.location.reload()</script>";
        } else {
            SELF::updateOfficeId();


            die("Synce done updateOfficeId" . count($caregiverList));
        }
    }
    public static function updateOfficeId()
    {

        $caregiverList = HHACaregivers::whereNull('officeId')->where('hhasyncdatetime', '<', '2023-08-07')->inRandomOrder()->limit(1000)->get();
        echo count($caregiverList);
        //        die("ddddd");
        foreach ($caregiverList as $list) {
            $caregiver = HHACaregivers::whereNull('officeId')->where('id', $list->id)->first();
            if ($caregiver) {
                echo "<br/>" . $list->caregiver_id;
                echo "sdfsd" . $list->agency_fk;

                $caregiverList = HHACaregivers::where('id', $list->id)
                    ->update(array("hha_sync" => 'Y', "hhasyncdatetime" => date('Y-m-d H:i:s')));
                if ($list->agency_fk != "") {
                    SELF::GetCaregiverDetailByCareGiverID($list->caregiver_id, $list->agency_fk);
                } else {
                    echo "NOid";
                }
                // die();
            }
        }
    }

    public static function getCaregiverOtherCompliance($agencyID, $officeID)
    {
        
        $data_Array = array();
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);

        $xml = '<soap:Envelope
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xmlns:xsd="http://www.w3.org/2001/XMLSchema"
		xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
			<GetCaregiverOtherCompliance
				xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
				<Authentication>
				<AppName>' . $agencyHHADetail->app_name . '</AppName>
				<AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
				<AppKey>' . $agencyHHADetail->app_token . '</AppKey>
				</Authentication>
				<Status>ACTIVE</Status>
                <OfficeID>' . $officeID . '</OfficeID>
			</GetCaregiverOtherCompliance>
		</soap:Body>
	</soap:Envelope>';
    if($agencyID == self::STATIC_AGENCY_ID){
        $json = SELF::getDataDemo($xml, 'GetCaregiverOtherCompliance');
    }else{
        $json = SELF::getData($xml, 'GetCaregiverOtherCompliance');
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

            $data_Array = array();
            if (isset($xml->Body->GetCaregiverOtherComplianceResponse->GetCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverOtherComplianceResponse->GetCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverOtherComplianceResponse->GetCaregiverOtherComplianceResult->CaregiverOtherCompliance->CaregiverOtherComplianceInfo)) {
                    $tempValue = $xml->Body->GetCaregiverOtherComplianceResponse->GetCaregiverOtherComplianceResult->CaregiverOtherCompliance->CaregiverOtherComplianceInfo;

                    $respoe = count($xml->Body->GetCaregiverOtherComplianceResponse->GetCaregiverOtherComplianceResult->CaregiverOtherCompliance->CaregiverOtherComplianceInfo);
                   
                    $temparray = array();
                    for ($i = 0; $i < $respoe; $i++) {
                        $OtherComplianceID = '';

                        if (isset($tempValue[$i]->OtherComplianceID) && $tempValue[$i]->OtherComplianceID != '') {
                            $OtherComplianceID = $tempValue[$i]->OtherComplianceID;
                            $temparray['id'] = (int)$OtherComplianceID;
                        }
                        $ComplianceName = '';
                        if (isset($tempValue[$i]->ComplianceName) && $tempValue[$i]->ComplianceName != '') {
                            $ComplianceName = $tempValue[$i]->ComplianceName;
                            $temparray['name'] = (string)$ComplianceName;
                        }

                        $status = '';
                        if (isset($tempValue[$i]->Status) && $tempValue[$i]->Status != '') {
                            $status = $tempValue[$i]->Status;
                            $temparray['Status'] = (string)$status;
                        }


                        // $finalarr  = array('id'=>$tempValue[$i]->CaregiverDocumentTypeID,'name'=>$tempValue[$i]->CaregiverDocumentTypep[0]);
                        $data_Array[] = $temparray;
                    }
                }
            } 
        }

        return $data_Array;
    }

    public static function getCaregiverMedicalDocument($agencyID, $officeID)
    {
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);

        $data_Array = array();
        $xml = '<soap:Envelope
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xmlns:xsd="http://www.w3.org/2001/XMLSchema"
		xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
			<GetCaregiverMedicals
				xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
				<Authentication>
				<AppName>' . $agencyHHADetail->app_name . '</AppName>
				<AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
				<AppKey>' . $agencyHHADetail->app_token . '</AppKey>
				</Authentication>
				<Status>ACTIVE</Status>
                <OfficeID>' . $officeID . '</OfficeID>
			</GetCaregiverMedicals>
		</soap:Body>
	</soap:Envelope>';
    if($agencyID ==self::STATIC_AGENCY_ID){
        $json = SELF::getDataDemo($xml, 'GetCaregiverMedicals');
    }else{
        $json = SELF::getData($xml, 'GetCaregiverMedicals');
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

            if (isset($xml->Body->GetCaregiverMedicalsResponse->GetCaregiverMedicalsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalsResponse->GetCaregiverMedicalsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverMedicalsResponse->GetCaregiverMedicalsResult->CaregiverMedicals->CaregiverMedicalInfo)) {
                    $tempValue = $xml->Body->GetCaregiverMedicalsResponse->GetCaregiverMedicalsResult->CaregiverMedicals->CaregiverMedicalInfo;

                    $respoe = count($xml->Body->GetCaregiverMedicalsResponse->GetCaregiverMedicalsResult->CaregiverMedicals->CaregiverMedicalInfo);
                 
                    $temparray = array();
                    for ($i = 0; $i < $respoe; $i++) {
                        $MedicalID = '';

                        if (isset($tempValue[$i]->MedicalID) && $tempValue[$i]->MedicalID != '') {
                            $MedicalID = $tempValue[$i]->MedicalID;
                            $temparray['id'] = (int)$MedicalID;
                        }
                        $MedicalName = '';
                        if (isset($tempValue[$i]->MedicalName) && $tempValue[$i]->MedicalName != '') {
                            $MedicalName = $tempValue[$i]->MedicalName;
                            $temparray['name'] = (string)$MedicalName;
                        }

                        $status = '';
                        if (isset($tempValue[$i]->Status) && $tempValue[$i]->Status != '') {
                            $status = $tempValue[$i]->Status;
                            $temparray['Status'] = (string)$status;
                        }
                        
                        $officeID = '';
                        if (isset($tempValue[$i]->OfficeID) && $tempValue[$i]->OfficeID != '') {
                            $officeID = $tempValue[$i]->OfficeID;
                            $temparray['office_id'] = (string)$officeID;
                        }

                        // $finalarr  = array('id'=>$tempValue[$i]->CaregiverDocumentTypeID,'name'=>$tempValue[$i]->CaregiverDocumentTypep[0]);
                        $data_Array[] = $temparray;
                    }
                }
            }

            return $data_Array;
        }
    }


    public static function getCaregiverDocumentType($agencyID)
    {
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);

        $xml = '<soap:Envelope
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xmlns:xsd="http://www.w3.org/2001/XMLSchema"
		xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
			<GetCaregiverDocumentType
				xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
				<Authentication>
				<AppName>' . $agencyHHADetail->app_name . '</AppName>
				<AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
				<AppKey>' . $agencyHHADetail->app_token . '</AppKey>
				</Authentication>
				<Status>ACTIVE</Status>
			</GetCaregiverDocumentType>
		</soap:Body>
	</soap:Envelope>';
    if($agencyID == self::STATIC_AGENCY_ID){
        $json = SELF::getDataDemo($xml, 'GetCaregiverDocumentType');
    }else{
        $json = SELF::getData($xml, 'GetCaregiverDocumentType');
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
         

            $data_Array = array();
            if (isset($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes->DocumentType)) {
                    $tempValue = $xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes->DocumentType;

                    $respoe = count($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes->DocumentType);
                   
                    $temparray = array();
                    for ($i = 0; $i < $respoe; $i++) {
                        $caregiverDocumentTypeID = '';

                        if (isset($tempValue[$i]->CaregiverDocumentTypeID) && $tempValue[$i]->CaregiverDocumentTypeID != '') {
                            $caregiverDocumentTypeID = $tempValue[$i]->CaregiverDocumentTypeID;
                            $temparray['id'] = (int)$caregiverDocumentTypeID;
                        }
                        $caregiverDocumentType = '';
                        if (isset($tempValue[$i]->CaregiverDocumentType) && $tempValue[$i]->CaregiverDocumentType != '') {
                            $caregiverDocumentType = $tempValue[$i]->CaregiverDocumentType;
                            $temparray['name'] = (string)$caregiverDocumentType;
                        }
                        $status = '';
                        if (isset($tempValue[$i]->Status) && $tempValue[$i]->Status != '') {
                            $status = $tempValue[$i]->Status;
                            $temparray['Status'] = (string)$status;
                        }


                        // $finalarr  = array('id'=>$tempValue[$i]->CaregiverDocumentTypeID,'name'=>$tempValue[$i]->CaregiverDocumentTypep[0]);
                        $data_Array[] = $temparray;
                    }
                }
            }

            return $data_Array;
        }
    }

    public static function getSendHHADocument($agencyId, $medicalName, $extension, $document_type, $caregiverid, $file, $uploadDocId="")
    {

        $medicalName=str_replace('|',"",$medicalName);
        $medicalName=str_replace('/',"",$medicalName);
        $medicalName=str_replace('&'," and ",$medicalName);
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
          $xml = '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <AddCaregiverDocument
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                            <AppName>' . $agencyHHADetail->app_name . '</AppName>
                            <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                            <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                    <CaregiverDocumentInfo>
                        <CaregiverID>' . $caregiverid . '</CaregiverID>
                        <CaregiverDocumentTypeID>' . $document_type . '</CaregiverDocumentTypeID>
                        <Description>' . $medicalName . '</Description>
                        <FileName>' . $medicalName . '.' . $extension . '</FileName>
                        <StreamData>' . base64_encode($file) . '</StreamData>
                    </CaregiverDocumentInfo>
                </AddCaregiverDocument>
            </soap:Body>
        </soap:Envelope>';

        if($agencyHHADetail->id == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'AddCaregiverDocument');
        }else{
            $json = SELF::getData($xml, 'AddCaregiverDocument');
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
            
            // print_r($xml);
            // echo "test";
            
            if (isset($xml->Body->AddCaregiverDocumentResponse->AddCaregiverDocumentResult->Result->ErrorInfo->ErrorID) && $xml->Body->AddCaregiverDocumentResponse->AddCaregiverDocumentResult->Result->ErrorInfo->ErrorID == 0) {
                $caregiverDocumentUpload = '';
                if (isset($xml->Body->AddCaregiverDocumentResponse->AddCaregiverDocumentResult->CaregiverDocID) && $xml->Body->AddCaregiverDocumentResponse->AddCaregiverDocumentResult->CaregiverDocID != '') {
                    $caregiverDocumentUpload = $xml->Body->AddCaregiverDocumentResponse->AddCaregiverDocumentResult->CaregiverDocID;
                }
                if($uploadDocId !=""){
                    $update = DocumentPatient::where('id', $uploadDocId)->update(array('hha_document_id' => $caregiverDocumentUpload));
                }
                
            }else{
               
            }
        }
        return 1;
    }

    public static function getCaregiverMedicalResults($agencyID, $medicalId, $officeID)
    {
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);

        // <OfficeID xsi:nil="true"/>
        //  <OfficeID>' . $officeID . '</OfficeID>
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <GetCaregiverMedicalResults
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                <AppName>' . $agencyHHADetail->app_name . '</AppName>
                <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                </Authentication>
                <OfficeID>' . $officeID . '</OfficeID>
                <MedicalID>' . $medicalId . '</MedicalID>
            </GetCaregiverMedicalResults>
        </soap:Body>
    </soap:Envelope>';
        if($agencyHHADetail->id == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'GetCaregiverMedicalResults');
        }else{
            $json = SELF::getData($xml, 'GetCaregiverMedicalResults');
        }
        

        $data_Array = array();

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
   
            if (isset($xml->Body->GetCaregiverMedicalResultsResponse->GetCaregiverMedicalResultsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalResultsResponse->GetCaregiverMedicalResultsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverMedicalResultsResponse->GetCaregiverMedicalResultsResult->CaregiverMedicalResults->CaregiverMedicalResult)) {
                    $tempValue = $xml->Body->GetCaregiverMedicalResultsResponse->GetCaregiverMedicalResultsResult->CaregiverMedicalResults->CaregiverMedicalResult;

                    $respoe = count($xml->Body->GetCaregiverMedicalResultsResponse->GetCaregiverMedicalResultsResult->CaregiverMedicalResults->CaregiverMedicalResult);
                    $temparray = array();
                    for ($i = 0; $i < $respoe; $i++) {
                        $ResultID = '';

                        if (isset($tempValue[$i]->ResultID) && $tempValue[$i]->ResultID != '') {
                            $ResultID = $tempValue[$i]->ResultID;
                            $temparray['id'] = (int)$ResultID;
                        }
                        $optionValue = '';
                        if (isset($tempValue[$i]->OptionValue) && $tempValue[$i]->OptionValue != '') {
                            $optionValue = $tempValue[$i]->OptionValue;
                            $temparray['name'] = (string)$optionValue;
                        }



                        // $finalarr  = array('id'=>$tempValue[$i]->CaregiverDocumentTypeID,'name'=>$tempValue[$i]->CaregiverDocumentTypep[0]);
                        $data_Array[] = $temparray;
                    }
                }
            }

            return $data_Array;
        }
    }

    public static function getCaregiverOtherComplienceMedicalResults($agencyID, $medicalId, $officeID)
    {
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);
        // <OfficeID xsi:nil="true"/>
        //  <OfficeID>' . $officeID . '</OfficeID>
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <GetCaregiverOtherComplianceResults
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                <AppName>' . $agencyHHADetail->app_name . '</AppName>
                <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                </Authentication>
                <OfficeID>' . $officeID . '</OfficeID>
                <OtherComplianceID>' . $medicalId . '</OtherComplianceID>
            </GetCaregiverOtherComplianceResults>
        </soap:Body>
    </soap:Envelope>';
        
    if($agencyID == self::STATIC_AGENCY_ID){
        $json = SELF::getDataDemo($xml, 'GetCaregiverOtherComplianceResults');
    }else{
        $json = SELF::getData($xml, 'GetCaregiverOtherComplianceResults');
    }

       
        $data_Array = array();

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

            if (isset($xml->Body->GetCaregiverOtherComplianceResultsResponse->GetCaregiverOtherComplianceResultsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverOtherComplianceResultsResponse->GetCaregiverOtherComplianceResultsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverOtherComplianceResultsResponse->GetCaregiverOtherComplianceResultsResult->CaregiverOtherComplianceResults->CaregiverOtherComplianceResult)) {
                    $tempValue = $xml->Body->GetCaregiverOtherComplianceResultsResponse->GetCaregiverOtherComplianceResultsResult->CaregiverOtherComplianceResults->CaregiverOtherComplianceResult;

                    $respoe = count($xml->Body->GetCaregiverOtherComplianceResultsResponse->GetCaregiverOtherComplianceResultsResult->CaregiverOtherComplianceResults->CaregiverOtherComplianceResult);
                    $temparray = array();
                    for ($i = 0; $i < $respoe; $i++) {
                        $OtherComplianceID = '';

                        if (isset($tempValue[$i]->OtherComplianceResultID) && $tempValue[$i]->OtherComplianceResultID != '') {
                            $OtherComplianceID = $tempValue[$i]->OtherComplianceResultID;
                            $temparray['id'] = (int)$OtherComplianceID;
                        }
                        $optionValue = '';
                        if (isset($tempValue[$i]->OptionValue) && $tempValue[$i]->OptionValue != '') {
                            $optionValue = $tempValue[$i]->OptionValue;
                            $temparray['name'] = (string)$optionValue;
                        }



                        // $finalarr  = array('id'=>$tempValue[$i]->CaregiverDocumentTypeID,'name'=>$tempValue[$i]->CaregiverDocumentTypep[0]);
                        $data_Array[] = $temparray;
                    }
                }
            }

            return $data_Array;
        }
    }

    public static function getCaregiverDetails($id)
    {
        return   HHACaregivers::with('agencyDetails')->where('hha_delete_flag','N')->where('caregiver_id', $id)->first();
    }
    
    public static function getCaregiverDetailsByAgencyId($id,$agencyId)
    {
        return   HHACaregivers::with('agencyDetails')->where('hha_delete_flag','N')->where('caregiver_id', $id)->where('agency_fk',$agencyId)->first();
    }

    public static function getUpdateHHADocument($agencyId, $caregiverId, $medicalId, $resultId, $dateCompleted,$pid="",$docId="")
    {

        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <CreateCaregiverMedical
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                <AppName>' . $agencyHHADetail->app_name . '</AppName>
                <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                </Authentication>
                <CaregiverMedicalInfo>
                    <CaregiverID>' . $caregiverId . '</CaregiverID>
                    <MedicalID>' . $medicalId . '</MedicalID>
                    <DueDate  xsi:nil="true" />
                    <DateCompleted>' . $dateCompleted . '</DateCompleted>
                    <Notes></Notes>
                    <ResultID>' . $resultId . '</ResultID>
                </CaregiverMedicalInfo>
            </CreateCaregiverMedical>
        </soap:Body>
    </soap:Envelope>';
        $send_new_parameter =$xml;
        $json = SELF::getData($xml, 'CreateCaregiverMedical');
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
            $updateHHADocument = new SendHhaDocumentLog(array('agencyId'=>$agencyId,'patient_id'=>$pid,'document_id'=>$docId,'caregiverId'=>$caregiverId,'medicalId'=>$medicalId,'resultId'=>$resultId,'dateCompleted'=>$dateCompleted,'request_response'=>$send_new_parameter));
            $updateHHADocument->save();
            
            if (isset($xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID == 0) {
                return 1;
            }
            if (isset($xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID !== 0) {

                return $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorMessage[0];
            }
        }
        return 0;
    }


    public static function createCaregiverOtherCompliance($agencyId, $caregiverId, $medicalId, $resultId, $dateCompleted,$dueDate)
    {

        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
        $finalResponse = [];
         $xml = ' <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
	xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<CreateCaregiverOtherCompliance
			xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
			<Authentication>
				<AppName>' . $agencyHHADetail->app_name . '</AppName>
				<AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
				<AppKey>' . $agencyHHADetail->app_token . '</AppKey>
			</Authentication>
			<caregiverOtherComplianceInfo>
				<CaregiverID>'.$caregiverId.'</CaregiverID>
				<OtherComplianceID>' . $medicalId . '</OtherComplianceID>';
                if($dueDate !=""){
                    $xml .='<DueDate>' . date('Y-m-d', strtotime($dueDate)) . '</DueDate>';
                }else{
                    $xml .='<DueDate xsi:nil="true"/>';
                }
				if($dateCompleted !=""){
                    $xml .='<DateCompleted>' . $dateCompleted . '</DateCompleted>';
                }else{
                    $xml .='<DateCompleted xsi:nil="true"/>';
                }

                if($resultId !=""){
                    $xml .='<OtherComplianceResultID>' . $resultId . '</OtherComplianceResultID>';
                }else{
                    $xml .='<OtherComplianceResultID>-1</OtherComplianceResultID>';
                }
				$xml .='<Notes></Notes>
				<Score xsi:nil="true"/>
				<FileName></FileName>
				<StreamData></StreamData>
				
			</caregiverOtherComplianceInfo>
		</CreateCaregiverOtherCompliance>
	</soap:Body>
</soap:Envelope>';
      
        if($agencyId == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'CreateCaregiverOtherCompliance');
        }else{
            $json = SELF::getData($xml, 'CreateCaregiverOtherCompliance');
        }
        if ($json === false) {
            return self::handleJsonError();
        }

        $xmlObj = self::parseSoapResponseNew($json);
        if (!$xmlObj) {
            return 0;
        }

        $message = "";
        if (
            isset($xmlObj->Body->CreateCaregiverOtherComplianceResponse->CreateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID)
            && (int)$xmlObj->Body->CreateCaregiverOtherComplianceResponse->CreateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID === 0
        ) {
            $caregiverInfo = $xmlObj->Body->CreateCaregiverOtherComplianceResponse->CreateCaregiverOtherComplianceResult->CaregiverOtherComplianceResultInfo ?? null;
        
            if ($caregiverInfo && !empty($caregiverInfo->CaregiverID) && !empty($caregiverInfo->CaregiverOtherComplianceID)) {
                $caregiverId = (string)$caregiverInfo->CaregiverID;
                $complianceId = (string)$caregiverInfo->CaregiverOtherComplianceID;
                
                $finalResponse = [
                    'caregiver_id'=>$caregiverId,
                    'complianceId'=>$complianceId,
                ];

            }
            $status = 1;
        }
      
        if (isset($xmlObj->Body->CreateCaregiverOtherComplianceResponse->CreateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID) && $xmlObj->Body->CreateCaregiverOtherComplianceResponse->CreateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID !=0) {
            $message = (string)$xmlObj->Body->CreateCaregiverOtherComplianceResponse->CreateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorMessage[0];
            $status = 0;
        }
        if (isset($xmlObj->Body->Fault->faultcode) && $xmlObj->Body->Fault->faultcode !="") {
            $message = (string)$xmlObj->Body->Fault->faultstring;
            $status = 0;
        }
        return  ['data'=>$finalResponse,'status'=>$status,'message'=>$message];
    }

    public static function getVisit($details, $startDate, $endDate)
    {
        $finalData = [];
        if(isset($details->agencyDetails->app_name) && $details->agencyDetails->app_name !=""){
            $AppName = $details->agencyDetails->app_name;
            $AppSecret = $details->agencyDetails->app_key;
            $AppKey = $details->agencyDetails->app_token;
            $getExistingVisitId = HHAVisit::select('visit_id')->where('del_flag', 'N')->where('caregiver_id',$details->caregiver_id)->get();
            $currentVisitIds = [];
            foreach ($getExistingVisitId as $val) {
                $currentVisitIds[] = $val->visit_id;
            }
          
            $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <SearchVisits xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>'.$AppName.'</AppName>
                                    <AppSecret>'.$AppSecret.'</AppSecret>
                                    <AppKey>'.$AppKey.'</AppKey>
                            </Authentication>
                            <SearchFilters>
                            <StartDate>' . $startDate . '</StartDate>
                            <EndDate>' . $endDate . '</EndDate>
                            <PatientID xsi:nil="true" />
                                    <CaregiverID>'.$details->caregiver_id.'</CaregiverID>
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
                        }
                    }
                }

                // $remainingIds = array_diff($visitIDs, $currentVisitIds);

                foreach ($visitIDs as $visit) {
                   $finalData[] =  SELF::getScheduleUpdate($visit, $AppName, $AppSecret, $AppKey);
                }
            }
        }
        return $finalData;
    }

    public static function getScheduleUpdate($visitId, $AppName, $AppSecret, $AppKey)
    {
        $finalData= [];
       

        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetScheduleInfo xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                            <AppName>'.$AppName.'</AppName>
                            <AppSecret>'.$AppSecret.'</AppSecret>
                            <AppKey>'.$AppKey.'</AppKey>
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
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->VisitDate)) {
                        $VisitDate = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->VisitDate;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->ID)) {
                        $PatientId = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->ID;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->AdmissionNumber)) {
                        $AdmissionNumber = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->AdmissionNumber;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->FirstName)) {
                        $FirstName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->FirstName;
                    }
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->LastName)) {
                        $LastName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Caregiver->LastName;
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

                    $PFirstName ="";
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->FirstName)) {
                        $PFirstName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->FirstName;
                    }
                    $PLastName ="";
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->LastName)) {
                        $PLastName = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->LastName;
                    }

                    $PAdmissionNumber ="";
                    if (isset($xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->AdmissionNumber)) {
                        $PAdmissionNumber = $xml->Body->GetScheduleInfoResponse->GetScheduleInfoResult->ScheduleInfo->Patient->AdmissionNumber;
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
                        'created_date' => date('Y-m-d H:i:s'),
                        'patient_first_name' => addslashes($PFirstName),
                        'patient_last_name' => addslashes($PLastName),
                        'patient_admission_number' => addslashes($PAdmissionNumber)
                    );


                    // $inser_id = new HHAVisit($finalData);
                    // $inser_id->save();
                }
            }
        }
        return $finalData;
    }

    public static function getHHACaregiverNotes($details, $startDate, $endDate,$agencyId="")
    {

        if(!empty($agencyId)){
            $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
            $details->agencyDetails = $agencyHHADetail;
        }
        $appName = $details->agencyDetails->app_name;
        $appSecret = $details->agencyDetails->app_key;
        $appKey = $details->agencyDetails->app_token;
       
        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetCaregiverNotes xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>' . $appName . '</AppName>
                                    <AppSecret>' . $appSecret . '</AppSecret>
                                    <AppKey>' . $appKey . '</AppKey>
                            </Authentication>
                            <CaregiverID>' . $details->caregiver_id . '</CaregiverID>
                            <ModifiedAfter>' . $startDate . '</ModifiedAfter>
                    </GetCaregiverNotes>
            </soap:Body>
            </soap:Envelope>';
        if(isset($details->agencyDetails->id) && $details->agencyDetails->id ==2 ){
            $json = Self::getDataDemo($xml_post_string, 'GetCaregiverNotes');
        }else{
            $json = Self::getData($xml_post_string, 'GetCaregiverNotes');
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

            if (isset($xml->Body->GetCaregiverNotesResponse->GetCaregiverNotesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverNotesResponse->GetCaregiverNotesResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverNotesResponse->GetCaregiverNotesResult->CaregiverNotes)) {
                    $respoe = $xml->Body->GetCaregiverNotesResponse->GetCaregiverNotesResult->CaregiverNotes->CaregiverNoteInfo;
                    foreach ($respoe as  $val) {

                        $array = array(
                            'CaregiverNoteID' => addslashes($val->CaregiverNoteID),
                            'CaregiverID' => addslashes($val->CaregiverID),
                            'NoteDate' => addslashes(date('m/d/Y h:i A', strtotime($val->NoteDate))),
                            'Note' => addslashes($val->Note),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $finalArray[] = $array;
                    }
                }
            }
        }
        return $finalArray;
    }

    public static function createHHACaregiverNotes($details, $response)
    {

        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            $AppName = $details->agencyDetails->app_name;
            $AppSecret = $details->agencyDetails->app_key;
            $AppKey = $details->agencyDetails->app_token;
    
             $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                        <CreateCaregiverNote  xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                <Authentication>
                                        <AppName>' . $AppName . '</AppName>
                                        <AppSecret>' . $AppSecret . '</AppSecret>
                                        <AppKey>' . $AppKey . '</AppKey>
                                </Authentication>
                                <CaregiverNoteInfo>
                                <CaregiverID>' . $details->caregiver_id . '</CaregiverID>
                                <SubjectID>' . $response['subject_id'] . '</SubjectID>
                                <Note>' . $response['hha_caregivers_notes'] . '</Note><PatientID>0</PatientID><PayerPatientNote>No</PayerPatientNote><ReasonID>0</ReasonID><SendNotifications><MobileAppOrSMS>No</MobileAppOrSMS><Email>No</Email><VoiceMessage>No</VoiceMessage></SendNotifications></CaregiverNoteInfo>
                                
                        </CreateCaregiverNote>
                </soap:Body>
                </soap:Envelope>';
    
            $json = Self::getData($xml_post_string, 'CreateCaregiverNote');
    
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

    public static function getHHACaregiverSubject($details)
    {
        $AppName = $details->agencyDetails->app_name;
        $AppSecret = $details->agencyDetails->app_key;
        $AppKey = $details->agencyDetails->app_token;
        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetCaregiverNoteSubjects  xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>' . $AppName . '</AppName>
                                    <AppSecret>' . $AppSecret . '</AppSecret>
                                    <AppKey>' . $AppKey . '</AppKey>
                            </Authentication>
                            <SearchFilters><OfficeID>0</OfficeID><Status>ACTIVE</Status></SearchFilters>
                            
                    </GetCaregiverNoteSubjects>
            </soap:Body>
            </soap:Envelope>';

        $json = Self::getData($xml_post_string, 'GetCaregiverNoteSubjects');
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
            if (isset($xml->Body->GetCaregiverNoteSubjectsResponse->GetCaregiverNoteSubjectsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverNoteSubjectsResponse->GetCaregiverNoteSubjectsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverNoteSubjectsResponse->GetCaregiverNoteSubjectsResult->CaregiverNoteSubjects)) {
                    $respoe = $xml->Body->GetCaregiverNoteSubjectsResponse->GetCaregiverNoteSubjectsResult->CaregiverNoteSubjects->CaregiverNoteSubject;
                    
                    foreach ($respoe as  $val) {

                        $array = array(
                            'ID' => addslashes($val->ID),
                            'Name' => addslashes($val->Name),

                        );
                        $finalArray[] = $array;
                    }
                }
            }
        }
        return $finalArray;
    }


    public static function getCaregiverMedicalDetails($details, $caregiverId,$status="")
    {

        $appName = $details->agencyDetails->app_name;
        $appSecret = $details->agencyDetails->app_key;
        $appKey = $details->agencyDetails->app_token;
        $status = ($status !="")?$status:"All";
        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetCaregiverMedicalDetails  xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>' . $appName . '</AppName>
                                    <AppSecret>' . $appSecret . '</AppSecret>
                                    <AppKey>' . $appKey . '</AppKey>
                            </Authentication>
                            <SearchFilter><CaregiverID>' . $caregiverId . '</CaregiverID>
                            <CaregiverComplianceExpItemID>-1</CaregiverComplianceExpItemID>
                            <ComplianceStatus>'.$status.'</ComplianceStatus></SearchFilter>
                            
                    </GetCaregiverMedicalDetails>
            </soap:Body>
            </soap:Envelope>';
       
        if(isset($details->agencyDetails->id) && $details->agencyDetails->id == self::STATIC_AGENCY_ID){
            $json = Self::getDataDemo($xml_post_string, 'GetCaregiverMedicalDetails');
        }else{
            $json = Self::getData($xml_post_string, 'GetCaregiverMedicalDetails');
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

            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                    $respoe = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails;

                    foreach ($respoe as  $val) {
                        $tempArray = [];
                        $tempArray['caregiver_id'] = addslashes($val->CaregiverID);
                        $tempArray['medical_id'] = addslashes($val->MedicalID);
                        $tempArray['medical_name'] = addslashes($val->MedicalName);
                        $tempArray['due_date'] = addslashes($val->DueDate);
                        $tempArray['date_perform'] = addslashes($val->DatePerformed);
                        $tempArray['status'] = addslashes($val->Status);
                        $tempArray['result'] = addslashes($val->Result);
                        $tempArray['caregiver_medical_id'] = addslashes($val->CaregiverMedicalID);
                        $tempArray['notes'] = addslashes($val->Notes);
                        $tempArray['modifiedDate'] = addslashes($val->ModifiedDate);
                        $finalArray[] = $tempArray;
                    }
                }
            }
        }

        return $finalArray;
    }

    public static function getCaregiverOtherComplianceDetails($details, $caregiverId)
    {
        $AppName = $details->agencyDetails->app_name;
        $AppSecret = $details->agencyDetails->app_key;
        $AppKey = $details->agencyDetails->app_token;

        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetCaregiverComplianceItemDue  xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>' . $AppName . '</AppName>
                                    <AppSecret>' . $AppSecret . '</AppSecret>
                                    <AppKey>' . $AppKey . '</AppKey>
                            </Authentication>
                            <SearchFilter>
                                <OfficeID>' . $details->officeId . '</OfficeID>
                                <CaregiverID>' . $caregiverId . '</CaregiverID>
                                <MedicalID>58406</MedicalID>
                            
                                <ComplianceItemType>OtherCompliance</ComplianceItemType>
                                <ComplianceStatus>All</ComplianceStatus>
                            </SearchFilter>
                    </GetCaregiverComplianceItemDue>
            </soap:Body>
            </soap:Envelope>';

        $json = Self::getData($xml_post_string, 'GetCaregiverComplianceItemDue');
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
            $finalArray = [];
            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResultResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverOtherComplianceResponse->GetCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResultResult->CaregiverOtherCompliance->CaregiverOtherComplianceInfo)) {

                    $response = $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResultResult->CaregiverOtherCompliance->CaregiverOtherComplianceInfo;

                    
                    // foreach($response as $value){
                    //     $tempArray = [];
                    //     $tempArray['OtherComplianceID']=addslashes($value->OtherComplianceID);
                    //     $tempArray['ComplianceName']=addslashes($value->ComplianceName);
                    //     $tempArray['Active']=addslashes($value->Active);

                    //    $finalArray[] = $tempArray;
                    // }

                }
            }

            return $finalArray;
        }
    }

    public static function getHHACaregiverInServices($details, $caregiverId,$agency_id="")
    {

        if(!empty($agency_id)){
            $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agency_id);
            $details->agencyDetails = $agencyHHADetail;
        }
        $appName = $details->agencyDetails->app_name;
        $appSecret = $details->agencyDetails->app_key;
        $appKey = $details->agencyDetails->app_token;

        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <GetCaregiverInServices xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>' . $appName . '</AppName>
                                    <AppSecret>' . $appSecret . '</AppSecret>
                                    <AppKey>' . $appKey . '</AppKey>
                            </Authentication>
                            <CaregiverID>' . $caregiverId . '</CaregiverID> 
                    </GetCaregiverInServices>
            </soap:Body>
            </soap:Envelope>';

        if(isset($agency_id) && $agency_id ==2){
            $json = Self::getDataDemo($xml_post_string, 'GetCaregiverInServices');
        }else{
            $json = Self::getData($xml_post_string, 'GetCaregiverInServices');
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
            $finalArray = [];
            if (isset($xml->Body->GetCaregiverInServicesResponse->GetCaregiverInServicesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverInServicesResponse->GetCaregiverInServicesResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverInServicesResponse->GetCaregiverInServicesResult->CaregiverInServices->CaregiverInServicesInfo)) {
                    $response = $xml->Body->GetCaregiverInServicesResponse->GetCaregiverInServicesResult->CaregiverInServices->CaregiverInServicesInfo;
                    foreach ($response as $val) {
                        $tempArray = [];
                        $tempArray['topic_name'] = addslashes($val->Topics->TopicInfo->Name);
                        $tempArray['description'] = addslashes($val->Description);
                        $tempArray['from_time'] = addslashes(date('h:i A', strtotime($val->FromTime)));
                        $tempArray['end_time'] = addslashes(date('h:i A', strtotime($val->EndTime)));
                        $tempArray['inservice_date'] = addslashes(date('m/d/Y', strtotime($val->InserviceDate)));
                        $finalArray[] = $tempArray;
                    }
                }
            }
        }
        return $finalArray;
    }

    public static function searchCaregiverWithAgencyId($agencyId, $search)
    {
        $query = HHACaregivers::selectRaw('caregiver_id as id, CONCAT(first_name," ",last_name) as name,caregiver_code,status')->where('hha_delete_flag','N')->whereNull('deleted_at')->where('agency_fk', $agencyId)->whereRaw('(LOWER(CONCAT_WS("",first_name," ",last_name)) LIKE "%' . strtolower($search) . '%" OR caregiver_code LIKE "%' . $search . '%")')->orderBy('id', 'desc')->get();

        return $query;
    }

    public static function getLastDateUpdate($details){
      
        $agencyDetails = Agency::where('id',$details->agency_fk)->first();
        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
                <GetCaregiverDemographics xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                        <AppName>' . $agencyDetails->app_name . '</AppName>
                        <AppSecret>' . $agencyDetails->app_key . '</AppSecret>
                        <AppKey>' . $agencyDetails->app_token . '</AppKey>
                    </Authentication>
                    <CaregiverInfo>
                            <ID>' . $details->caregiver_id . '</ID>
                </CaregiverInfo>	
                </GetCaregiverDemographics>
        </soap:Body>
        </soap:Envelope>';


$json = SELF::getData($xml, 'GetCaregiverDemographics');

    

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
           
            $caregiverDetailInfo = $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo ?? '';
            if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo)) {
                    $LastWorkDate = "";
                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastWorkDate)) {
                        $LastWorkDate =  $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastWorkDate;
                    }
                    $caregiverContectList = HHACaregivers::where("id", $details->id)->update(array('last_work_date'=>$LastWorkDate));
                }
            }
       
        }
    }

    public static function  GetCaregiverAvailabilityById($CareGiverDetails,$agencyId="")
    {
        
        if(!empty($agencyId)){
            $agencyDetails = Agency::where('id', $agencyId)->first();
            $CareGiverDetails->agencyDetails = $agencyDetails;
        }
        $AppName = $CareGiverDetails->agencyDetails->app_name;
        $AppSecret = $CareGiverDetails->agencyDetails->app_key;
        $AppKey = $CareGiverDetails->agencyDetails->app_token;
        
        $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Body>
                            <GetCaregiverPermanentWeekAvailability xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                <Authentication>
                                    <AppName>' . $AppName . '</AppName>
                                        <AppSecret>' . $AppSecret . '</AppSecret>
                                        <AppKey>' . $AppKey . '</AppKey>
                                </Authentication>
                                <CaregiverID>' . $CareGiverDetails->caregiver_id . '</CaregiverID>
                            </GetCaregiverPermanentWeekAvailability>
                    </soap:Body>
                    </soap:Envelope>';
                    if($CareGiverDetails->agencyDetails->id == self::STATIC_AGENCY_ID){
                        $json = SELF::getDataDemo($xml_post_string, 'GetCaregiverPermanentWeekAvailability');
                    }else{
                        $json = SELF::getData($xml_post_string, 'GetCaregiverPermanentWeekAvailability');
                    }
       
       $data = [];
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                $json = '{"jsonError": "unknown"}';
            }

            http_response_code(500);

        } else {
            
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
        
            $finalArray = [];
            if (isset($xml->Body->GetCaregiverPermanentWeekAvailabilityResponse->GetCaregiverPermanentWeekAvailabilityResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverPermanentWeekAvailabilityResponse->GetCaregiverPermanentWeekAvailabilityResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverPermanentWeekAvailabilityResponse->GetCaregiverPermanentWeekAvailabilityResult->PermanentWeekAvailability)) {
                    foreach ($xml->Body->GetCaregiverPermanentWeekAvailabilityResponse->GetCaregiverPermanentWeekAvailabilityResult->PermanentWeekAvailability as $dd) {
                     $cccc=0;
                        foreach ($dd as $ddd) {
                            $statusForAvailabilityType = false;
                            $PermanentWeekID = "";

                            if (isset($ddd->PermanentWeekID)) {
                                $PermanentWeekID = (string)$ddd->PermanentWeekID;
                            }

                            $CaregiverID = "";
                            if (isset($ddd->CaregiverID)) {
                                $CaregiverID = (string)$ddd->CaregiverID;
                            }

                            $SundayAvailabilityType = null;
                            $SundayLiveIn = null;
                            $SundayFrom = null;
                            $SundayTo = null;

                            if (isset($ddd->SundayFrom) && $ddd->SundayFrom!=''  ) {

                                $statusForAvailabilityType = true;
                                $SundayAvailabilityType = (string)$ddd->SundayAvailabilityType;
                                $SundayLiveIn = (string)$ddd->SundayLiveIn;
                                $SundayFrom = date('h:i A', strtotime((string)$ddd->SundayFrom));
                                $SundayTo = date('h:i A', strtotime((string)$ddd->SundayTo));

                                $inserArray = array(
                                    "PermanentWeekID" => $PermanentWeekID,
                                    "user_id" => $CaregiverID,
                                    "day" => 'Sunday',
                                    "sunday_from" => $SundayFrom,
                                    "sunday_to" =>  $SundayTo,
                                    "sunday_live_in" =>  $SundayLiveIn,
                                    "sunday_available" => 'Yes'
                                );
                                $finalArray[] = $inserArray;
                            }
                            
                            $MondayAvailabilityType = null;
                            $MondayLiveIn = null;
                            $MondayFrom = null;
                            $MondayTo = null;

                            if (isset($ddd->MondayFrom)  && $ddd->MondayFrom!='') {
                                $statusForAvailabilityType = true;
                                $MondayAvailabilityType = (string)$ddd->MondayAvailabilityType;
                                $MondayLiveIn = (string)$ddd->MondayLiveIn;
                                $MondayFrom = date('h:i A', strtotime((string)$ddd->MondayFrom));
                                $MondayTo = date('h:i A', strtotime((string)$ddd->MondayTo));

                                $inserArray = array(
                                    "PermanentWeekID" => $PermanentWeekID,
                                    "user_id" => $CaregiverID,
                                    "day" => 'Monday',
                                    "monday_from" => $MondayFrom,
                                    "monday_to" =>  $MondayTo,
                                    "monday_live_in" =>  $MondayLiveIn,
                                    "monday_available" => 'Yes',
                                );
                                $finalArray[] = $inserArray;
                            }
                            $TuesdayAvailabilityType = null;
                            $TuesdayLiveIn = null;
                            $TuesdayFrom = null;
                            $TuesdayTo = null;

                            if (isset($ddd->TuesdayFrom) && $ddd->TuesdayFrom!='') {
                                $statusForAvailabilityType = true;
                                $TuesdayAvailabilityType = (string)$ddd->TuesdayAvailabilityType;
                                $TuesdayLiveIn = (string)$ddd->TuesdayLiveIn;
                                $TuesdayFrom = date('h:i A', strtotime((string)$ddd->TuesdayFrom));
                                $TuesdayTo = date('h:i A', strtotime((string)$ddd->TuesdayTo));
                                $inserArray = array(
                                    "PermanentWeekID" => $PermanentWeekID,
                                    "user_id" => $CaregiverID,
                                    "day" => 'Tuesday',
                                    "tuesday_from" => $TuesdayFrom,
                                    "tuesday_to" =>  $TuesdayTo,
                                    "tuesday_live_in" =>  $TuesdayLiveIn,
                                    "tuesday_available" =>'Yes',

                                );
                                $finalArray[] = $inserArray;
                            }

                            $WednesdayAvailabilityType = null;
                            $WednesdayLiveIn = null;
                            $WednesdayFrom = null;
                            $WednesdayTo = null;

                            if (isset($ddd->WednesdayFrom) && $ddd->WednesdayFrom!='') {
                                $statusForAvailabilityType = true;
                                $WednesdayAvailabilityType = (string)$ddd->WednesdayAvailabilityType;
                                $WednesdayLiveIn = (string)$ddd->WednesdayLiveIn;
                                $WednesdayFrom = date('h:i A', strtotime((string)$ddd->WednesdayFrom));
                                $WednesdayTo = date('h:i A', strtotime((string)$ddd->WednesdayTo));

                                $inserArray = array(
                                    "PermanentWeekID" => $PermanentWeekID,
                                    "user_id" => $CaregiverID,
                                    "day" => 'Wednesday',
                                    "wednesday_from" => $WednesdayFrom,
                                    "wednesday_to" =>  $WednesdayTo,
                                    "wednesday_live_in" =>  $WednesdayLiveIn,
                                    "wednesday_available" => 'Yes',
                                );
                                $finalArray[] = $inserArray;
                            }

                            $ThursdayAvailabilityType = null;
                            $ThursdayLiveIn = null;
                            $ThursdayFrom = null;
                            $ThursdayTo = null;

                            if (isset($ddd->ThursdayFrom)  && $ddd->ThursdayFrom!='') {
                                $statusForAvailabilityType = true;
                                $ThursdayAvailabilityType = (string)$ddd->ThursdayAvailabilityType;
                                $ThursdayLiveIn = (string)$ddd->ThursdayLiveIn;
                                $ThursdayFrom = date('h:i A', strtotime((string)$ddd->ThursdayFrom));
                                $ThursdayTo = date('h:i A', strtotime((string)$ddd->ThursdayTo));

                                $inserArray = array(
                                    "PermanentWeekID" => $PermanentWeekID,
                                    "user_id" => $CaregiverID,
                                    "day" => 'Thursday',
                                    "thursday_from" => $ThursdayFrom,
                                    "thursday_to" =>  $ThursdayTo,
                                    "thursday_live_in" =>  $ThursdayLiveIn,
                                    "thursday_available" => 'Yes',
                                );
                                $finalArray[] = $inserArray;
                            }

                            $FridayAvailabilityType = null;
                            $FridayLiveIn = null;
                            $FridayFrom = null;
                            $FridayTo = null;

                            if (isset($ddd->FridayFrom) && $ddd->FridayFrom!='') {
                                $statusForAvailabilityType = true;
                                $FridayAvailabilityType = (string)$ddd->FridayAvailabilityType;
                                $FridayLiveIn = (string)$ddd->FridayLiveIn;
                                $FridayFrom = date('h:i A', strtotime((string)$ddd->FridayFrom));
                                $FridayTo = date('h:i A', strtotime((string)$ddd->FridayTo));

                                $inserArray = array(
                                    "PermanentWeekID" => $PermanentWeekID,
                                    "user_id" => $CaregiverID,
                                    "day" => 'Friday',
                                    "friday_from" => $FridayFrom,
                                    "friday_to" =>  $FridayTo,
                                    "friday_live_in" =>  $FridayLiveIn,
                                    "friday_available" => 'Yes',
                                );

                                $finalArray[] = $inserArray;
                            }
                            $SaturdayAvailabilityType = null;
                            $SaturdayLiveIn = null;
                            $SaturdayFrom = null;
                            $SaturdayTo = null;

                            if (isset($ddd->SaturdayFrom) && $ddd->SaturdayFrom!='' ) {

                                $statusForAvailabilityType = true;
                                $SaturdayAvailabilityType = (string)$ddd->SaturdayAvailabilityType;
                                $SaturdayLiveIn = (string)$ddd->SaturdayLiveIn;
                                $SaturdayFrom = date('h:i A', strtotime((string)$ddd->SaturdayFrom));
                                $SaturdayTo = date('h:i A', strtotime((string)$ddd->SaturdayTo));

                                $inserArray = array(
                                    "PermanentWeekID" => $PermanentWeekID,
                                    "user_id" => $CaregiverID,
                                    "day" => 'Saturday',
                                    "saturday_from" => $SaturdayFrom,
                                    "saturday_to" =>  $SaturdayTo,
                                    "saturday_live_in" =>  $SaturdayLiveIn,
                                    "saturday_available" => 'Yes',
                                );
                                $finalArray[] = $inserArray;
                            }
                        }
                        $data = $finalArray;
                    }
                }
            }
        }
        return $data;
    }

    public static function tempCaregiverSYNC($id){
        $agencyDetails = Agency::where('id', $id)->get();
        foreach ($agencyDetails as $agency) {
            if (!empty($agency->app_name) && !empty($agency->app_key)  && !empty($agency->app_token)) {
                $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                        <SearchCaregivers xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                <AppName>' . $agency->app_name . '</AppName>
                                <AppSecret>' . $agency->app_key . '</AppSecret>
                                <AppKey>' . $agency->app_token . '</AppKey>
                            </Authentication>
                            <SearchFilters>
                                    <Status>Active</Status>
                        </SearchFilters>	
                        </SearchCaregivers>
                </soap:Body>
                </soap:Envelope>';

                $json = SELF::getData($xml, 'SearchCaregivers');
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

                    if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID)) {
                            $cnt_Patients = count($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID);

                            for ($i = 0; $i < $cnt_Patients; $i++) {
                                $caregiverID = $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID[$i];
                                $query = HHACaregivers::where('caregiver_id',addslashes($caregiverID))->where('agency_fk',$agency->id)->first();
                                if(!$query){
                                    HHACaregivers::updateOrCreate([
                                        "agency_fk"      => $agency->id,
                                        "caregiver_id"        => addslashes($caregiverID),
                                    ], [
    
                                        'hha_delete_flag' => 'N',
                                    ]);
                                }else{

                                }
                               
                            }

                            return 1;
                        }
                    } else {
                        $error = (array)$xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorMessage ?? ['Something happened. Try again.'];
                        return $error[0];
                    }
                }
            }
        }
      
    }
    public static function createNewMedicalForHamaspik($agencyId, $caregiverId, $medicalId, $resultId, $dateCompleted,$dueDate)
    {

        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <CreateCaregiverMedical
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                <AppName>' . $agencyHHADetail->app_name . '</AppName>
                <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                </Authentication>
                <CaregiverMedicalInfo>
                    <CaregiverID>' . $caregiverId . '</CaregiverID>
                    <MedicalID>' . $medicalId . '</MedicalID>
                    <DueDate>'.$dueDate.'</DueDate>
                    <Notes></Notes>
                </CaregiverMedicalInfo>
            </CreateCaregiverMedical>
        </soap:Body>
    </soap:Envelope>';

        $json = SELF::getData($xml, 'CreateCaregiverMedical');
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


            if (isset($xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID == 0) {
                return 1;
            }
            if (isset($xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID !== 0) {

                return $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorMessage[0];
            }
        }
        return 0;
    }
    public static function getUpdateHHADocumentNew($agencyId, $caregiverId, $medicalId, $resultId, $dateCompleted,$dueDate)
    {

        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
        $xml = '<soap:Envelope
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <CreateCaregiverMedical
                xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                <Authentication>
                <AppName>' . $agencyHHADetail->app_name . '</AppName>
                <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                </Authentication>
                <CaregiverMedicalInfo>
                    <CaregiverID>' . $caregiverId . '</CaregiverID>
                    <MedicalID>' . $medicalId . '</MedicalID>
                    <DueDate>'.$dueDate.'</DueDate>
                    <DateCompleted>' . $dateCompleted . '</DateCompleted>
                    <Notes></Notes>
                    <ResultID>' . $resultId . '</ResultID>
                </CaregiverMedicalInfo>
            </CreateCaregiverMedical>
        </soap:Body>
    </soap:Envelope>';

        $json = SELF::getData($xml, 'CreateCaregiverMedical');
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


            if (isset($xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID == 0) {
                return 1;
            }
            if (isset($xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID !== 0) {

                return $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorMessage[0];
            }
        }
        return 0;
    }

    public static function getHHAVisit($agencyId){
        $details  = Agency::where("id", $agencyId)->first();
        if(isset($details->app_name) && $details->app_name !=""){
            $AppName = $details->app_name;
            $AppSecret = $details->app_key;
            $AppKey = $details->app_token;


            $xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <SearchVisits xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                    <AppName>' . $AppName . '</AppName>
                                    <AppSecret>' . $AppSecret . '</AppSecret>
                                    <AppKey>' . $AppKey . '</AppKey>
                            </Authentication>
                            <SearchFilters>
                            <StartDate>2023-12-01</StartDate>
                            <EndDate>2023-12-02</EndDate>
                            <PatientID xsi:nil="true" />
                                    <CaregiverID xsi:nil="true" />
                            </SearchFilters>
                    </SearchVisits>
            </soap:Body>
            </soap:Envelope>';

            $json = Self::getData($xml_post_string, 'SearchVisits');
      
        }
    }

    public static function getsyncAppointment($caregiver){
        
       // $flagUpdate = HhaAppointment::updateData(array('del_flag' => 'Y'), array('agency_id' => $caregiver->agency_fk, 'caregiver_id' => $caregiver->caregiver_id));
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($caregiver->agency_fk);
       
        if(isset($agencyHHADetail->app_name)){
        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetCaregiverMedicalDetails
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                        <AppName>'.$agencyHHADetail->app_name.'</AppName>
                        <AppSecret>'.$agencyHHADetail->app_key.'</AppSecret>
                        <AppKey>'.$agencyHHADetail->app_token.'</AppKey>
                    </Authentication>
                    <SearchFilter>
                        <CaregiverID>'.$caregiver->caregiver_id.'</CaregiverID>
                        <CaregiverComplianceExpItemID>-1</CaregiverComplianceExpItemID>
                        <ComplianceStatus>All</ComplianceStatus>
                    </SearchFilter>
                </GetCaregiverMedicalDetails>
            </soap:Body>
        </soap:Envelope>';
                
               
            $json = SELF::getData($xml, 'GetCaregiverMedicalDetails');

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
            
                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                        $respoe = count($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails);
                        for ($i = 0; $i < $respoe; $i++) {

                            $medicalID = '';
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID != '') {
                                $medicalID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID;
                            }
                            $medicalName = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName != '') {
                                $medicalName = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName;
                            }
                            $status = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status != '') {
                                $status = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status;
                            }
                            $caregiverID = "";
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID != '') {
                                $caregiverID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID;
                            }
                            $dueDate = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate != '') {
                                $dueDate = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate;
                            }
                            $officeID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                $officeID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID;
                            }


                            $CaregiverMedicalID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID != '') {
                                $CaregiverMedicalID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID;
                            }
                            $datePerform = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed != '') {
                             
                                $datePerform = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed;
                            }
                            HhaAppointment::updateOrCreate([
                                'agency_id'        => $caregiver->agency_fk,
                                'caregiver_id' => $caregiverID,
                                'caregiver_medical_id' => $CaregiverMedicalID,
                            ], [
                                'medical_id' => $medicalID,
                                'medical_name' => $medicalName,
                                'caregiver_medical_id' => $CaregiverMedicalID,
                                'due_date' => $dueDate,
                                'status' => $status,
                                'office_id' => $officeID,
                              
                                'del_flag' => 'N',
                                'updated_date' => date('Y-m-d H:i:s'),
                                'date_perform'=>$datePerform

                            ]);
                        }
                    }
                }
            }
            HHACaregivers::where('caregiver_id', $caregiver->caregiver_id)->update(array("last_medical_sync" => date('Y-m-d H:i:s')));
        }
        return 1;
    }


    public static function getCaregiverDetailUpdate($cid,$agencyId){
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);
      
        if(isset($agencyHHADetail->app_name)){
        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
                <GetCaregiverDemographics xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                        <AppName>' . $agencyHHADetail->app_name . '</AppName>
                        <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                        <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                    <CaregiverInfo>
                            <ID>' . $cid . '</ID>
                </CaregiverInfo>	
                </GetCaregiverDemographics>
        </soap:Body>
        </soap:Envelope>';


$json = SELF::getData($xml, 'GetCaregiverDemographics', $agencyHHADetail->agency_id);

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

                if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID ==0){
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->ID)){
                        $zipcode ="";
                        $OfficeName ="";
                       
                        if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeName)){
                            $OfficeName =addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeName);
                        }

                        if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Zip5)){
                            $zipcode =addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Zip5);
                        }

                        $lang ="";
                        if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language1)){
                        
                            $lang =addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language1);
                        }

                        $data = [
                            'office_name'=>$OfficeName,
                           
                        ];

                      return   HHACaregivers::where('caregiver_id', $cid)->update($data);
                    }
                }

            }
        }
    }

    public static function getHHACaregiverDetails($caregiverId,$agencyID){
 
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyID);
        if(!isset($agencyHHADetail->id)){
            return 1;
        }
        $finalArray = [];
        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				<soap:Body>
						<GetCaregiverDemographics xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
							<Authentication>
								<AppName>' . $agencyHHADetail->app_name . '</AppName>
								<AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
								<AppKey>' . $agencyHHADetail->app_token . '</AppKey>
							</Authentication>
							<CaregiverInfo>
									<ID>' . $caregiverId . '</ID>
						</CaregiverInfo>
						</GetCaregiverDemographics>
				</soap:Body>
				</soap:Envelope>';

        if($agencyID == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'GetCaregiverDemographics', $agencyHHADetail->agency_id);
        }else{
            $json = SELF::getData($xml, 'GetCaregiverDemographics', $agencyHHADetail->agency_id);
        }
        
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
           
            if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->Result->ErrorInfo->ErrorID ==0){
                if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->ID)){
                    $caregiverId = "";

                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->ID) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->ID !=""){
                        $caregiverId = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->ID);
                    }
                    
                    $firstName = "";

                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstName) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstName !=""){
                        $firstName = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstName);
                    }
                    
                    $middleName = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->MiddleName) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->MiddleName !=""){
                        $middleName = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->MiddleName);
                    }
                   
                    $lastName = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastName) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastName !=""){
                        $lastName = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastName);
                    }
                    
                    $gender = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Gender) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Gender !=""){
                        $gender = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Gender);
                    }

                    $dob = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->BirthDate) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->BirthDate !=""){
                        $dob = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->BirthDate);
                    }

                    $caregiverCode = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverCode) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverCode !=""){
                        $caregiverCode = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverCode);
                    }

                    $ssn = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->SSN) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->SSN !=""){
                        $ssn = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->SSN);
                    }

                    $status = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Status->Name) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Status->Name !=""){
                        $status = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Status->Name);
                    }
                   
                    $empType = null;
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypes->Discipline[0]) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypes->Discipline[0] !=""){
                        $empType = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypes->Discipline;
                      $empType = implode(',',$empType);
                    }
                    
                    $applicationDate = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->ApplicationDate) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->ApplicationDate !=""){
                        $applicationDate = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->ApplicationDate);
                    }
                    
                    $teamName = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team->Name) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team->Name !=""){
                        $teamName = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team->Name);
                        $teamId = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Team->ID);
                        $teamName = $teamName.'('.$teamId.')';
                    }

                    $location = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Location->Name) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Location->Name !=""){
                        $location = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Location->Name);
                    }
                    
                    $branch = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Branch->Name) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Branch->Name !=""){
                        $branch = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Branch->Name);
                    }
                    
                    $hireDate = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->HireDate) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->HireDate !=""){
                        $hireDate = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->HireDate);
                    }
                    
                    $firstWorkDate = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstWorkDate) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstWorkDate !=""){
                        $firstWorkDate = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->FirstWorkDate);
                    }
                    
                    $lastWorkDate = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastWorkDate) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastWorkDate !=""){
                        $lastWorkDate = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->LastWorkDate);
                    }
                    
                    $address = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Street1) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Street1 !=""){
                        $address = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Street1);
                    }
                    
                    $address2 = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Street2) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Street2 !=""){
                        $address2 = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Street2);
                    }
                    $city = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->City) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->City !=""){
                       
                        $city = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->City);
                       
                    }
                    $state = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->State) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->State !=""){
                        
                        $state = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->State);
                       
                    }
                    $zip = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Zip5) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Zip5 !=""){
                        $zip = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Zip5);
                    }

                    $phone = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->HomePhone) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->HomePhone !=""){
                        $phone = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->HomePhone);
                       
                    }

                    $phone2 = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Phone2) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Phone2 !=""){
                        $phone2 = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Phone2);
                    }
                    $phone2 = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Phone2) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Phone2 !=""){
                        $phone2 = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Address->Phone2);
                    }

                    $notificationEmail = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->Email) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->Email !=""){
                        $notificationEmail = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->Email);
                    }

                    $notificationMobile = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->MobileOrSMS) && $xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->MobileOrSMS !=""){
                        $notificationMobile = addslashes($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->NotificationPreferences->MobileOrSMS);
                    }
                    $getDetails = HHAVisit::select('patient_id')->with('patientDetails:patient_id,coordinator_id,coordinator_name')->where('del_flag', 'N')->where('caregiver_id',$caregiverId)->first();
                    $coordinatorName = "";
                    if(isset($getDetails->patientDetails->coordinator_id) && $getDetails->patientDetails->coordinator_id !=""){
                        $coordinatorName = $getDetails->patientDetails->coordinator_name.' ('.$getDetails->patientDetails->coordinator_id .')';
                    }

                    $officeID = null;

                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeID)) {
                        $officeID = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeID;
                        $officeID = $officeID[0];
                    }

                    $officeName = null;

                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeName)) {
                        $officeName = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->CaregiverOffices->Office->OfficeName;
                        $officeName = $officeName[0];
                    }

                    $EmploymentTypesDiscipline = "";

                    if (isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypesDiscipline)) {

                        $EmploymentTypesDiscipline = (array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmploymentTypesDiscipline;
                        $EmploymentTypesDiscipline = isset($EmploymentTypesDiscipline[0]) ? $EmploymentTypesDiscipline[0] : "";
                    }
                    $lang ="";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language1)){
                    
                        $lang =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language1;
                        $lang =isset($lang[0])?$lang[0]:"";
                    }

                    $lang2 ="";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language2)){
                    
                        $lang2 =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language2;
                        $lang2 =isset($lang2[0])?$lang2[0]:"";
                    }

                    $lang3 ="";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language3)){
                    
                        $lang3 =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->Language3;
                        $lang3 =isset($lang3[0])?$lang3[0]:"";
                    }

                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmergencyContacts->EmergencyContact->Name)){
                    
                        $emergencyName =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmergencyContacts->EmergencyContact->Name;

                        $emergencyName =isset($emergencyName[0])?$emergencyName[0]:"";
                    }
                   
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmergencyContacts->EmergencyContact->Phone1)){
                    
                        $emergencyPhone1 =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmergencyContacts->EmergencyContact->Phone1;

                        $emergencyPhone1 =isset($emergencyPhone1[0])?str_replace('-','',$emergencyPhone1[0]):"";
                    }
                    $emergencyRelationShip = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmergencyContacts->EmergencyContact->Relationship)){
                    
                        $emergencyRelationShip =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmergencyContacts->EmergencyContact->Relationship;

                        $emergencyRelationShip =isset($emergencyRelationShip[0])?$emergencyRelationShip[0]:"";
                    }

                    $employment_type = "";
                    if(isset($xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmployeeType)){
                    
                        $employeeType =(array)$xml->Body->GetCaregiverDemographicsResponse->GetCaregiverDemographicsResult->CaregiverInfo->EmployeeType;

                        $employment_type =isset($employeeType[0])?$employeeType[0]:"";
                    }

                    $getOfficeCode = HHAOfficeHelper::getDetailsByOfficeIdAndAgencyId($officeID,$agencyID);
                    $finalArray =[
                        'caregiver_id'=>$caregiverId,
                        'firstName'=>$firstName,
                        'middleName'=>$middleName,
                        'lastName'=>$lastName,
                        'gender'=>$gender,
                        'caregiverCode'=>$caregiverCode,
                        'ssn'=>$ssn,
                        'status'=>$status,
                        'dob'=>!empty($dob) ? date('m/d/Y',strtotime($dob)): 'N/A',
                        'empType'=>$empType,
                        'applicationDate'=> !empty($applicationDate) ? date('m/d/Y',strtotime($applicationDate)): 'N/A',
                        'teamName'=>$teamName,
                        'location'=>$location,
                        'branch'=>$branch,
                        'hireDate'=> !empty($hireDate) ? date('m/d/Y',strtotime($hireDate)) : 'N/A',
                        'firstWorkDate'=>!empty($firstWorkDate) ?date('m/d/Y',strtotime($firstWorkDate)) : 'N/A',
                        'lastWorkDate'=>!empty($lastWorkDate) ?date('m/d/Y',strtotime($lastWorkDate)) : 'N/A',
                        'address'=>$address,
                        'address2'=>$address2,
                        'city'=>$city,
                        'state'=>$state,
                        'zip'=>$zip,
                        'phone'=>$phone,
                        'phone2'=>$phone2,
                        'notificationEmail'=>$notificationEmail,
                        'notificationMobile'=>$notificationMobile,
                        'coordinatorName'=>$coordinatorName,
                        'officeId'=>$officeID,
                        'office_name'=>$officeName,
                        'EmploymentTypesDiscipline'=>$EmploymentTypesDiscipline,
                        'lang'=>$lang,
                        'lang2'=>$lang2,
                        'lang3'=>$lang3,
                        'emergencyName'=>$emergencyName,
                        'emergencyPhone1'=>$emergencyPhone1,
                        'emergencyRelationShip'=>$emergencyRelationShip,
                        'office_code'=>$getOfficeCode->office_code??"",
                        'employment_type'=>$employment_type
                    ];
                }
            }

        }
        return $finalArray;
    }

    public static function getCaregiverIDListByAgencyIdNew($agencyID)

    {

       
        $agency = Agency::where('id', $agencyID)->first();
    
        $getExistingCaregiver = HHACaregivers::getAllCaregiverIds($agencyID);
        $existing = $getExistingCaregiver->toArray();
      
        $hhaCaregiverIdArray = [];
           // echo "In agency <br/>";
        if ($agency->app_name !="" && $agency->app_key !="" && $agency->app_token !="") {
            //   echo "In agency data";

            //$flagUpdate = HHACaregivers::updateData(array('hha_delete_flag' => 'Y'), array('agency_fk' => $agency->id));
            $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Body>
                            <SearchCaregivers xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                <Authentication>
                                    <AppName>' . $agency->app_name . '</AppName>
                                    <AppSecret>' . $agency->app_key . '</AppSecret>
                                    <AppKey>' . $agency->app_token . '</AppKey>
                                </Authentication>
                                <SearchFilters>
                                <EmployeeType>All</EmployeeType>
                              
                            </SearchFilters>	
                            </SearchCaregivers>
                    </soap:Body>
                    </soap:Envelope>';

            $json = SELF::getData($xml, 'SearchCaregivers');

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

                if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID)) {
                        $cnt_Patients = count($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID);

                        for ($i = 0; $i < $cnt_Patients; $i++) {
                            $caregiverID = $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID[$i];
                           $hhaCaregiverIdArray[] = addslashes($caregiverID);
                           
                            
                           
                        }
                       
                        $final = array_diff($hhaCaregiverIdArray,$existing);
                        $saveData = [];
                        foreach($final as $val){
                            $temp = [];
                           
                            if(isset($agency->office_id) && $agency->office_id !=""){
                                $getDetails  = self::getHHACaregiverDetails(addslashes($val),$agency->id);
                                if($getDetails['officeId'] ==$agency->office_id){
                                    $temp['agency_fk'] = $agency->id;
                                    $temp['caregiver_id'] = addslashes($val);
                                    $temp['hha_delete_flag'] = 'N';
                                    HHACaregivers::updateOrCreate([
                                        "agency_fk"      => $agency->id,
                                        "caregiver_id"        => addslashes($val),
                                    ], [

                                        'hha_delete_flag' => 'N',
                                    ]);
                                    //$saveData[] = $temp;
                                }
                            }else{
                                $temp['agency_fk'] = $agency->id;
                                    $temp['caregiver_id'] = addslashes($val);
                                    $temp['hha_delete_flag'] = 'N';
                                HHACaregivers::updateOrCreate([
                                    "agency_fk"      => $agency->id,
                                    "caregiver_id"        => addslashes($val),
                                ], [
                                    'hha_delete_flag' => 'N',
                                ]);
                               // $saveData[] = $temp;
                            }


                        }
                        //echo "<pre>";print_r($saveData);die();
                        
                        return 1;
                    }
                } else {
                    $error = (array)$xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorMessage ?? ['Something happened. Try again.'];
                    return $error[0];
                }
            }
        }
        
    }
   

    public static function getsyncAppointmentNew($caregiver){
        
      //  $flagUpdate = HhaAppointment::updateData(array('del_flag' => 'Y'), array('agency_id' => $caregiver->agency_fk, 'caregiver_id' => $caregiver->caregiver_id));
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($caregiver->agency_fk);
  
        if(isset($agencyHHADetail->app_name)){
        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetCaregiverMedicalDetails
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                        <AppName>'.$agencyHHADetail->app_name.'</AppName>
                        <AppSecret>'.$agencyHHADetail->app_key.'</AppSecret>
                        <AppKey>'.$agencyHHADetail->app_token.'</AppKey>
                    </Authentication>
                    <SearchFilter>
                        <CaregiverID>'.$caregiver->caregiver_id.'</CaregiverID>
                        <CaregiverComplianceExpItemID>-1</CaregiverComplianceExpItemID>
                        <ComplianceStatus>All</ComplianceStatus>
                    </SearchFilter>
                </GetCaregiverMedicalDetails>
            </soap:Body>
        </soap:Envelope>';
                
               if($caregiver->agency_fk ==self::STATIC_AGENCY_ID){
                $json = SELF::getDataDemo($xml, 'GetCaregiverMedicalDetails');
               }else{
                $json = SELF::getData($xml, 'GetCaregiverMedicalDetails');
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

                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                        $respoe = count($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails);
                        for ($i = 0; $i < $respoe; $i++) {

                            $medicalID = '';
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID != '') {
                                $medicalID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID;
                            }
                            $medicalName = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName != '') {
                                $medicalName = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName;
                            }
                            $status = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status != '') {
                                $status = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status;
                            }
                            $caregiverID = "";
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID != '') {
                                $caregiverID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID;
                            }
                            $dueDate = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate != '') {
                                $dueDate = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate;
                            }
                            $officeID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                $officeID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID;
                            }

                            $CaregiverMedicalID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID != '') {
                                $CaregiverMedicalID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID;
                            }

                            $datePerform = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed != '') {
                                $datePerform = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed;
                            }
                            $query = HhaAppointment::where('agency_id',$caregiver->agency_fk)->where('caregiver_id',$caregiverID)->where('caregiver_medical_id',$CaregiverMedicalID)->first();
                            
                            if($status !="Completed"){
                                $data = [
                                    'medical_id' => $medicalID,
                                    'medical_name' => $medicalName,
                                    'caregiver_medical_id' => $CaregiverMedicalID,
                                    'due_date' => $dueDate,
                                    'status' => $status,
                                    'office_id' => $officeID,
                                    
                                    'del_flag' => 'N',
                                    'date_perform'=>$datePerform
    
                                ];
                                if(isset($query->id) && $query->id !=""){
                                    $data['updated_date'] = date('Y-m-d H:i:s');
                                   
                                   
                                }else{
                                    $data['created_date'] = date('Y-m-d H:i:s');
                                } 
                                HhaAppointment::updateOrCreate([
                                    'agency_id'        => $caregiver->agency_fk,
                                    'caregiver_id' => $caregiverID,
                                    'caregiver_medical_id' => $CaregiverMedicalID,
                                  
                                ], $data);
                            }
                            
                        }
                    }
                }
            }
            HHACaregivers::where('caregiver_id', $caregiver->caregiver_id)->where('agency_fk', $caregiver->agency_fk)->update(array("last_medical_sync" => date('Y-m-d H:i:s'),'hha_sync'=>'Y'));
        }
        return 1;
    }

    public static function searchCaregiverForHHA($agencyId,$caregiverCode){
      
        $agency = Agency::select('app_name','app_key','app_token')->where('id', $agencyId)->first();
        $saveArray = [];

        if(isset($agency->app_name)){
            $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <SearchCaregivers xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>' . $agency->app_name . '</AppName>
                            <AppSecret>' . $agency->app_key . '</AppSecret>
                            <AppKey>' . $agency->app_token . '</AppKey>
                        </Authentication>
                        <SearchFilters>
                                <Status>All</Status>
                                <CaregiverCode>'.$caregiverCode.'</CaregiverCode>
                    </SearchFilters>	
                    </SearchCaregivers>
            </soap:Body>
            </soap:Envelope>';

            if($agencyId == self::STATIC_AGENCY_ID){
                $json = SELF::getDataDemo($xml, 'SearchCaregivers');
            }else{
                $json = SELF::getData($xml, 'SearchCaregivers');
            }
            
            if ($json === false) {
                $json = json_encode(array("jsonError", json_last_error_msg()));
                if ($json === false) {
                    // This should not happen, but we go all the way now:
                    $json = '{"jsonError": "unknown"}';
                }
                // Set HTTP response status code to: 500 - Internal Server Error
                http_response_code(500);
            }else{
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
            
                $final = [];
                if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers)) {
                        $respoe = count($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers);
                        for ($i = 0; $i < $respoe; $i++) {
                            $caregiverId = $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers[$i]->CaregiverID;

                        $getDetails  = self::getHHACaregiverDetails(addslashes($caregiverId),$agencyId);

                        $final[]=$getDetails;
                        }
                        
                    }
                }
                if(!empty($final[0])){
                    foreach($final as $val){
                    $save = [
                        'caregiver_id'=>$val['caregiver_id'],
                        'officeId'=>$val['officeId'],
                        'office_name'=>$val['office_name'],
                        'agency_fk'=>$agencyId,
                        'first_name'=>$val['firstName'],
                        'middle_name'=>$val['middleName'],
                        'last_name'=>$val['lastName'],
                        'gender'=>$val['gender'],
                        'ssn'=>$val['ssn'],
                        
                        'dob'=>date('Y-m-d',strtotime($val['dob'])),
                        'caregiver_code'=>$val['caregiverCode'],
                        'mobile_or_sms'=>str_replace('-','',$val['notificationMobile']),
                        'hha_delete_flag'=>"N",
                        'hha_sync'=>"N",
                        'created_at'=>date('Y-m-d H:i:s'),
                        'hhasyncdatetime'=>date('Y-m-d H:i:s'),
                        'EmploymentTypesDiscipline'=>$val['EmploymentTypesDiscipline'],
                        'TeamName'=>$val['teamName'],
                        'last_work_date'=>date('Y-m-d',strtotime($val['lastWorkDate'])),
                        'zipcode'=>$val['zip'],
                        'language'=>$val['lang'],
                        'status'=>$val['status'],
                        'address1'=>$val['address'],
                        'address2'=>$val['address2'],
                        'City'=>$val['city'],
                        'State'=>$val['state'],
                        'Zip5'=>$val['zip'],
                        'HomePhone'=>str_replace('-','',$val['phone']),
                        'notification_mobile_no'=>str_replace('-','',$val['notificationMobile']),
                        'emergencyName'=>$val['emergencyName'],
                        'emergencyPhone1'=>$val['emergencyPhone1']
                        
                    ];
                    $saveArray[] = $save;
                    
                    }
                
                }
            }
        }
        return $saveArray;
    }

    public static function searchCaregiverCodeWithAgencyId($agencyId, $search)
    {
        $query = HHACaregivers::selectRaw('caregiver_id as id, CONCAT(first_name," ",last_name) as name,caregiver_code,status,gender,employment_type')->whereNull('deleted_at');
        $where = "agency_fk =".$agencyId;    
        if($search['hha_caregiver_code_id'] !=""){
            $explode = explode('-',$search['hha_caregiver_code_id']);
            if(isset($explode[1])){
                $caregiverCode = $explode[1];
            }else{
                $caregiverCode = $explode[0];
            }
            $where .=" and caregiver_code =".$caregiverCode;
        }
        if($search['hha_caregiver_first_name'] !=""){
            $where .=" and first_name LIKE '%".$search['hha_caregiver_first_name']."%'";
        }
        if($search['hha_caregiver_last_name'] !=""){
            $where .=" and last_name LIKE '%".$search['hha_caregiver_last_name']."%'";
        }
        if($search['hha_caregiver_phone_no'] !=""){
            $where .=" and mobile_or_sms =".$search['hha_caregiver_phone_no'];
        }
        if($search['hha_caregiver_ssn'] !=""){
            $where .=" and REPLACE(ssn, '-', '') =".$search['hha_caregiver_ssn'];
        }
        $query = $query->whereRaw($where)->orderBy('id', 'desc')->get();
        
        if(!empty($query[0])){
            return $query;
        }else{
            $query = self::searchCaregiverForHHAWithAll($agencyId, $search);

        }
        return $query;
    }

    public static function saveData($cid,$agencyId){
        $query = self::getHHACaregiverDetails($cid,$agencyId);
        $final = [];
        $final[] = $query;
        if(!empty($final[0])){
            foreach($final as $val){
                
               $save = [
                'caregiver_id'=>$val['caregiver_id'],
                'officeId'=>$val['officeId'],
                'agency_fk'=>$agencyId,
                'first_name'=>$val['firstName'],
                'middle_name'=>$val['middleName'],
                'last_name'=>$val['lastName'],
                'gender'=>$val['gender'],
                
                'dob'=>date('Y-m-d',strtotime($val['dob'])),
                'caregiver_code'=>$val['caregiverCode'],
                'mobile_or_sms'=>str_replace('-','',$val['notificationMobile']),
                'hha_delete_flag'=>"N",
                'hha_sync'=>"N",
                'created_at'=>date('Y-m-d H:i:s'),
                'hhasyncdatetime'=>date('Y-m-d H:i:s'),
                'EmploymentTypesDiscipline'=>$val['EmploymentTypesDiscipline'],
                'TeamName'=>$val['teamName'],
                'last_work_date'=>date('Y-m-d',strtotime($val['lastWorkDate'])),
                'zipcode'=>$val['zip'],
                'language'=>$val['lang'],
                'status'=>$val['status'],
                'address1'=>$val['address'],
                'address2'=>$val['address2'],
                'City'=>$val['city'],
                'State'=>$val['state'],
                'Zip5'=>$val['zip'],
                'HomePhone'=>str_replace('-','',$val['phone']),
                'notification_mobile_no'=>str_replace('-','',$val['notificationMobile']),
                
               ];
            //    print_r($save);
               
               $saveData = new HHACaregivers($save);
               $saveData->save();
              
            }
           
        }
    }

    public static function sendNotes($id,$data){
        $details = Self::getCaregiverDetails($id);
            
        $save = Self::createHHACaregiverNotes($details,$data);
        return 1;
    }

    public static function getCaregiverDetailsByCaregiverId($id,$agencyId)
    {
        return   HHACaregivers::with('agencyDetails')->where('caregiver_id', $id)->where('agency_fk',$agencyId)->first();
    }

    public static function getDocumentData($id,$agency){
      
        $details = Self::getCaregiverDetailsByCaregiverId($id,$agency);
        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <SearchCaregiverDocument
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <SearchFilters>
                                        <CaregiverID>'. $id .'</CaregiverID>
                                        <CaregiverDocumentTypeID xsi:nil="true" />
                                        <CaregiverDocumentID xsi:nil="true" />
                                        <FromDate xsi:nil="true" />
                                        <ToDate xsi:nil="true" />
                                    </SearchFilters>
                                </SearchCaregiverDocument>
                            </soap:Body>
                        </soap:Envelope>';

                        if($details->agencyDetails->id ==self::STATIC_AGENCY_ID){
                            $json = SELF::getDataDemo($xml, 'SearchCaregiverDocument');
                        }else{
                            $json = SELF::getData($xml, 'SearchCaregiverDocument');
                        }
                
                if ($json === false) {
                    $json = json_encode(array("jsonError", json_last_error_msg()));
                    if ($json === false) {
                        // This should not happen, but we go all the way now:
                        $json = '{"jsonError": "unknown"}';
                    }
                    // Set HTTP response status code to: 500 - Internal Server Error
                    http_response_code(500);
                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
                    $documentData = [];
                    if (isset($xml->Body->SearchCaregiverDocumentResponse->SearchCaregiverDocumentResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchCaregiverDocumentResponse->SearchCaregiverDocumentResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->SearchCaregiverDocumentResponse->SearchCaregiverDocumentResult->CaregiverDocuments)) {
                            $respoe = count($xml->Body->SearchCaregiverDocumentResponse->SearchCaregiverDocumentResult->CaregiverDocuments->CaregiverDocument);
                          
                            for ($i = 0; $i < $respoe; $i++) {
                                $data = $xml->Body->SearchCaregiverDocumentResponse->SearchCaregiverDocumentResult->CaregiverDocuments->CaregiverDocument[$i];
                               
                                $documentDatas = array(
                                    'caregiverDocId' => addslashes($data->CaregiverDocID),
                                    'caregiverId' => addslashes($data->CaregiverID),
                                    'patinetDocumentTypeID' => addslashes($data->CaregiverDocumentTypeID),
                                    'caregiverDocumentType' => addslashes($data->CaregiverDocumentType),
                                    'description' =>addslashes($data->Description),
                                    'CreatedBy' => addslashes($data->CreatedBy),
                                    'CreatedOn' => addslashes($data->CreatedOn),
                                    'fileName' => addslashes($data->FileName),
                                );
                                $documentData[] = $documentDatas;
                            }
                        }
                    }
                }
            }
            
        }
        return $documentData;
    }

    public static function getDocumentTypeData($id){
      
        $details = Self::getCaregiverDetails($id);
        
        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <GetCaregiverDocumentType
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <Status>ACTIVE</Status>
                                </GetCaregiverDocumentType>
                            </soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'GetCaregiverDocumentType');
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
                
                    
                    if (isset($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes)) {
                            $respoe = count($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes);
                            for ($i = 0; $i < $respoe; $i++) {
                                $caregiverDocumentTypeID = '';
                                if(isset($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->CaregiverDocumentTypeID) && !empty($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->CaregiverDocumentTypeID)){
                                    $caregiverDocumentTypeID = $xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->CaregiverDocumentTypeID;
                                }
                                
                                $caregiverDocumentType = '';
                                if(isset($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->CaregiverDocumentType) && !empty($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->CaregiverDocumentType)){
                                    $caregiverDocumentType = $xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->CaregiverDocumentType;
                                }

                                $description = '';
                                if(isset($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->Description) && !empty($xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->Description)){
                                    $description = $xml->Body->GetCaregiverDocumentTypeResponse->GetCaregiverDocumentTypeResult->CaregiverDocumentTypes[$i]->Description;
                                }

                                $documentData[] = array(
                                                    'caregiverDocumentTypeID' => $caregiverDocumentTypeID,
                                                    'caregiverDocumentType' => $caregiverDocumentType,
                                                    'description' => $description
                                                );
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
        $details = Self::getCaregiverDetails($id);
        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <AddCaregiverDocument
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <CaregiverDocumentInfo>
                                        <CaregiverID>'.$id.'</CaregiverID>
                                        <CaregiverDocumentTypeID>'.$data['document_type_id'].'</CaregiverDocumentTypeID>
                                        <Description>'.$data['description'].'</Description>';
                                    if(isset($data['file_name']) && !empty($data['file_name'])){
                                        $xml .= '<FileName>'.$data['file_name'].'</FileName>';
                                    }
                                    if(isset($data['file_stream']) && !empty($data['file_stream'])){
                                        $xml .= '<StreamData>'.$data['file_stream'].'</StreamData>';
                                    }
                            $xml .=  '</CaregiverDocumentInfo>
                                </AddCaregiverDocument>
                            </soap:Body>
                        </soap:Envelope>';

                $json = SELF::getData($xml, 'AddCaregiverDocument');
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
                                            <CaregiverID>' . $details->caregiver_id . '</CaregiverID>
                                            <PatientID xsi:nil="true" />
                                            <OfficeID xsi:nil="true" />
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
                        'caregiver_first_name' => addslashes($CaregiverFirstName),
                        'caregiver_last_name' => addslashes($CaregiverLastName),
                        'schedule_start_time' => addslashes($StartTime),
                        'schedule_end_time' => addslashes($EndTime)
                    );
                }
            }
            return $finalData;
        }
    }
    public static function getDownloadDocumentData($id,$docid){
      
        $details = Self::getCaregiverDetails($id);
        $documentData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <DownloadCaregiverDocument
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <CaregiverDocID>'. $docid .'</CaregiverDocID>
                                </DownloadCaregiverDocument>
                            </soap:Body>
                        </soap:Envelope>';

                if($details->agencyDetails->id ==self::STATIC_AGENCY_ID){
                    $json = SELF::getDataDemo($xml, 'DownloadCaregiverDocument');
                }else{
                    $json = SELF::getData($xml, 'DownloadCaregiverDocument');
                }
                
                if ($json === false) {

                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
         
                    if (isset($xml->Body->DownloadCaregiverDocumentResponse->DownloadCaregiverDocumentResult->Result->ErrorInfo->ErrorID) && $xml->Body->DownloadCaregiverDocumentResponse->DownloadCaregiverDocumentResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->DownloadCaregiverDocumentResponse->DownloadCaregiverDocumentResult->CaregiverDocument)) {
                            $response = $xml->Body->DownloadCaregiverDocumentResponse->DownloadCaregiverDocumentResult->CaregiverDocument;
                           
                            if(isset($response)){
                            $documentData = array(
                                        'caregiverDocID' => addslashes($response->CaregiverDocID),
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

    public static function getPrefrencesData($details,$agencyId=""){

        $prefreanceData = [];
        if(isset($details->agencyDetails) && $details->agencyDetails !=""){
            if(isset($details->agencyDetails->app_name)){
                $xml = '<soap:Envelope
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <GetCaregiverPreferences
                                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                                    <Authentication>
                                        <AppName>' . $details->agencyDetails->app_name . '</AppName>
                                        <AppSecret>' . $details->agencyDetails->app_key . '</AppSecret>
                                        <AppKey>' . $details->agencyDetails->app_token . '</AppKey>
                                    </Authentication>
                                    <CaregiverInfo>
                                        <ID>'.$details->caregiver_id.'</ID>
                                    </CaregiverInfo>
                                 
                                </GetCaregiverPreferences>
                            </soap:Body>
                        </soap:Envelope>';
                if($details->agencyDetails->id ==self::STATIC_AGENCY_ID){
                    $json = SELF::getDataDemo($xml, 'GetCaregiverPreferences');
                }else{
                    $json = SELF::getData($xml, 'GetCaregiverPreferences');
                }
                
                if ($json === false) {
                    $json = json_encode(array("jsonError", json_last_error_msg()));
                    if ($json === false) {
                        // This should not happen, but we go all the way now:
                        $json = '{"jsonError": "unknown"}';
                    }
                    http_response_code(500);
                }else{
                    $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                    $xml = simplexml_load_string($clean_xml);
   
                    if (isset($xml->Body->GetCaregiverPreferencesResponse->GetCaregiverPreferencesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverPreferencesResponse->GetCaregiverPreferencesResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->GetCaregiverPreferencesResponse->GetCaregiverPreferencesResult->CaregiverPreferencesInfo)) {
                            $respoe = count($xml->Body->GetCaregiverPreferencesResponse->GetCaregiverPreferencesResult->CaregiverPreferencesInfo);
                            
                            $response = (array)$xml->Body->GetCaregiverPreferencesResponse->GetCaregiverPreferencesResult->CaregiverPreferencesInfo;
                            if(isset($response['Preferences'])){
                                $prefreanceDataArray = json_decode(json_encode($response['Preferences']));
                                $prefreanceDataArray1 = [];
                                if(isset($prefreanceDataArray->Preference)){
                                    if(gettype($prefreanceDataArray->Preference) =='object'){
                                        
                                        $prefreanceDataArray1=[(array)$prefreanceDataArray->Preference];
                                    }else{
                                        $prefreanceDataArray1 = $prefreanceDataArray->Preference;
                                    }
                                }
                              
                                if(!empty($prefreanceDataArray1[0])){
                                    foreach($prefreanceDataArray1 as $val){
                                        $prefreanceData['preferenceInfo'][] = array(
                                            'preferenceID' =>$val['PreferenceID']??$val->PreferenceID,
                                            'preferenceName' => $val['PreferenceName']??$val->PreferenceName,
                                            'preferenceValue' => $val['PreferenceValue']??$val->PreferenceValue,
                                            'PreferenceType' =>$val['PreferenceType']??$val->PreferenceType,
                                        );
                                    }
                                }
                          
                                $prefreanceData['LanguageID1'] = addslashes($response['LanguageID1']);
                                $prefreanceData['Language1'] = addslashes($response['Language1']);
                                $prefreanceData['LanguageID2'] = addslashes($response['LanguageID2']);
                                $prefreanceData['Language2'] = addslashes($response['Language2']);
                                $prefreanceData['LanguageID3'] = addslashes($response['LanguageID3']);
                                $prefreanceData['Language3'] = addslashes($response['Language3']);
                                $prefreanceData['LanguageID4'] = addslashes($response['LanguageID4']);
                                $prefreanceData['Language4'] = addslashes($response['Language4']);
                                $prefreanceData['Other'] = addslashes($response['Other']);
                            }
                        }
                    }
                }
            }
        }
       
        return $prefreanceData;
    }
   
    public static function getCaregiverDetailsNew($id)
    {
        return   HHACaregivers::with('agencyDetails')->where('hha_delete_flag','N')->whereNotNull('first_name')->where('caregiver_id', $id)->orWhere('id',$id)->first();
    }

    
    public static function getHHACaregiverStatus(){
        return HHACaregivers::where('hha_delete_flag','N')->where('status','!=',"")->whereNotNull('status')->groupBy('status')->pluck('status');
    }

    public static function GetCaregiverComplianceItemDueByAgencyId($agencyId){
        $agencyHHADetail = Agency::getAllDetailsbyAgencyId($agencyId);

        $caregiverIDs =  HHACaregivers::select('caregiver_id','officeId','agency_fk','id')->whereNull('deleted_at')->where('agency_fk', $agencyId)->whereRaw("(hhasync_othecomplience is null or hhasync_othecomplience <'" . date('Y-m-d H:i:s', strtotime('-5 day')) . "')")->inRandomOrder()->limit(200)->get();
        
        if(!empty($caregiverIDs[0])){
            foreach ($caregiverIDs as $caregiver) {

                HhaOtherComplience::updateData(array('del_flag' => 'Y'), array('agency_id' => $caregiver->agency_fk, 'caregiver_id' => $caregiver->caregiver_id));
                
                $otherMedicals = [];
                $lastScanDetails=0;
                if ($lastScanDetails != $caregiver->agency_fk . '-' . $caregiver->officeId) {
                    $otherMedicals = SELF::getCaregiverOtherCompliance($caregiver->agency_fk, $caregiver->officeId);
                }
              
                foreach ($otherMedicals as $medicalIds) {
    
                    $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                    <soap:Body>
                        <GetCaregiverComplianceItemDue xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                            <AppName>' . $agencyHHADetail->app_name . '</AppName>
                            <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                            <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                            </Authentication>
                            <SearchFilter>
                                <CaregiverID>' . $caregiver->caregiver_id . '</CaregiverID>
                                <OfficeID>' . $caregiver->officeId . '</OfficeID>
                                <MedicalID>' . $medicalIds['id'] . '</MedicalID>
                                <ComplianceItemType>OtherCompliance</ComplianceItemType>
                                <ComplianceStatus>All</ComplianceStatus>
                                <SequenceID>0</SequenceID>
                            </SearchFilter>
                        </GetCaregiverComplianceItemDue>
                    </soap:Body>
                    </soap:Envelope>
                        ';

                    if($agencyId == self::STATIC_AGENCY_ID){
                        $json = SELF::getDataDemo($xml, 'GetCaregiverComplianceItemDue');
                    }else{
                        $json = SELF::getData($xml, 'GetCaregiverComplianceItemDue');
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
    
                        if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->Result->ErrorInfo->ErrorID == 0) {
    
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue)) {
                                $respoe = count($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue);

                                for ($i = 0; $i < $respoe; $i++) {
    
                                    $medicalID = '';
                                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID != '') {
                                        $medicalID = $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID;
                                    }
                                    $medicalName = null;
                                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName != '') {
                                        $medicalName = $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName;
                                    }
                                    $status = null;
                                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status != '') {
                                        $status = $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status;
                                    }
                                    $caregiverID = "";
                                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID != '') {
                                        $caregiverID = $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID;
                                    }
                                    $dueDate = null;
                                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate != '') {
                                        $dueDate = $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate;
                                    }
                                    $officeID = null;
                                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID != '') {
                                        $officeID = $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID;
                                    }

                                    $caregiverMedicalID = null;
                                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID != '') {
                                        $caregiverMedicalID = $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID;
                                    }
                                    
                                    HhaOtherComplience::updateOrCreate([
                                        'agency_id'        => $caregiver->agency_fk,
                                        'caregiver_id' => $caregiverID,
                                        'caregiver_medical_id' => $caregiverMedicalID,
                                    ], [
                                        'medical_id' => $medicalID,
                                        'medical_name' => $medicalName,
                                        'due_date' => $dueDate,
                                        'status' => $status,
                                        'office_id' => $officeID,
                                        'caregiver_medical_id' => $caregiverMedicalID,
                                        'del_flag' => 'N',
                                        'updated_date' => date('Y-m-d H:i:s')
    
                                    ]);
                                }
                            }
                        }
                    }
                }
                HHACaregivers::where('id', $caregiver->id)->update(array("hhasync_othecomplience" => date('Y-m-d H:i:s')));
            }
            return 1;
        }else{
            return 0;
        }
    }

    public static function autoSYNCCaregiverWithStatus($agency,$status){
        $final = [];
        if($agency->app_name !="" && $agency->app_key !="" && $agency->app_token !=""){
            $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <SearchCaregivers xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>' . $agency->app_name . '</AppName>
                            <AppSecret>' . $agency->app_key . '</AppSecret>
                            <AppKey>' . $agency->app_token . '</AppKey>
                        </Authentication>
                        <SearchFilters>
                        <Status>'.$status.'</Status>
                      
                    </SearchFilters>	
                    </SearchCaregivers>
            </soap:Body>
            </soap:Envelope>';

            $json = SELF::getData($xml, 'SearchCaregivers');
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
               
                if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID)) {
                        $cnt_Patients = count($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID);

                        for ($i = 0; $i < $cnt_Patients; $i++) {
                            $caregiverID = $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers->CaregiverID[$i];

                          
                           $hhaCaregiver[] = addslashes($caregiverID);
                        }

                        $final = $hhaCaregiver;
                    }
                } else {
                    $error = (array)$xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorMessage ?? ['Something happened. Try again.'];
                    return $error[0];
                }

            }
        }
     
        return $final;
    }

    public static function autoSYNCCaregiverMedicals($agency,$caregiverId){
        $allDetails = [];
        if (empty($agency->id)) return [];
        if($agency->app_name !="" && $agency->app_key !="" && $agency->app_token !=""){
            $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <soap:Body>
               <GetCaregiverMedicalDetails xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                  <Authentication>
                  <AppName>' . $agency->app_name. '</AppName>
                  <AppSecret>' . $agency->app_key. '</AppSecret>
                  <AppKey>' . $agency->app_token. '</AppKey>
                  </Authentication>
                  <SearchFilter>
                     <CaregiverID>' . $caregiverId . '</CaregiverID>
                     <CaregiverComplianceExpItemID>-1</CaregiverComplianceExpItemID>
                     <ComplianceStatus>All</ComplianceStatus>
                  </SearchFilter>
               </GetCaregiverMedicalDetails>
            </soap:Body>
         </soap:Envelope>
             ';
            if($agency->id  == self::STATIC_AGENCY_ID){
                $json = SELF::getDataDemo($xml, 'GetCaregiverMedicalDetails');
            }else{
                $json = SELF::getData($xml, 'GetCaregiverMedicalDetails');
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
                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                        $respoe = count($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails);
                        for ($i = 0; $i < $respoe; $i++) {

                            $medicalID = '';
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID != '') {
                                $medicalID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID;
                            }
                            $medicalName = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName != '') {
                                $medicalName = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName;
                            }
                            $status = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status != '') {
                                $status = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status;
                            }
                            
                            $dueDate = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate != '') {
                                $dueDate = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate;
                            }
                            $officeID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                $officeID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID;
                            }


                            $CaregiverMedicalID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                $CaregiverMedicalID = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID;
                            }

                            $datePerform = NULL;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed != '') {
                             
                                $datePerform = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed;
                            }

                            $final = [
                                'caregiver_medical_id'=>addslashes($CaregiverMedicalID),
                                'medical_id'=>addslashes($medicalID),
                                'medical_name'=>addslashes($medicalName),
                                'due_date'=>addslashes($dueDate),
                                'status'=>addslashes($status),
                                'office_id'=>addslashes($officeID),
                                'date_perform'=>addslashes($datePerform),
                            ];
                            $allDetails[] = $final;
                        }
                   
                    }
                }
            }
        }

        return $allDetails;
    }

    public static function getCaregiverDemographicDetails($caregiverId,$agencyId){
     
       return self::getHHACaregiverDetails($caregiverId,$agencyId);
       
            

    }

    public static function getCaregiverUpdateDetails($agencyDetails){

        $finalArray = array();
        if(!empty($agencyDetails->app_name) && !empty($agencyDetails->app_key) && !empty($agencyDetails->app_token)){
            $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetCaregiverChangesV2 xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                       <AppName>' . $agencyDetails->app_name . '</AppName>
                        <AppSecret>' . $agencyDetails->app_key . '</AppSecret>
                        <AppKey>' . $agencyDetails->app_token . '</AppKey>
                    </Authentication>
                    <OfficeID>0</OfficeID>
                    <ModifiedAfter>'.date('Y-m-d\TH:i:s',strtotime('-1 days')).'</ModifiedAfter>
                </GetCaregiverChangesV2>
            </soap:Body></soap:Envelope>';
            $json = SELF::getData($xml, 'GetCaregiverChangesV2');
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
                
                
                if (isset($xml->Body->GetCaregiverChangesV2Response->GetCaregiverChangesV2Result->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverChangesV2Response->GetCaregiverChangesV2Result->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->GetCaregiverChangesV2Response->GetCaregiverChangesV2Result->GetCaregiverChangesV2Info)) {
                        $respoe = $xml->Body->GetCaregiverChangesV2Response->GetCaregiverChangesV2Result->GetCaregiverChangesV2Info;
    
                        foreach($respoe as $key){
                            $finalArray[] =  addslashes($key->CaregiverID);
                        }
                    }
                }
            }
        }
        
        return $finalArray;
        
        
    }

    public static function GetAllCaregiverComplianceItemDue($agencyHHADetail,$officeID,$caregiverId,$medicalId){
        $final = [];

        if(!empty($agencyHHADetail->app_name) && !empty($agencyHHADetail->app_key) && !empty($agencyHHADetail->app_token)){
            $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <soap:Body>
                <GetCaregiverComplianceItemDue xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                    <AppName>' . $agencyHHADetail->app_name . '</AppName>
                    <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                    <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                    <SearchFilter>
                        <CaregiverID>' . $caregiverId . '</CaregiverID>
                        <OfficeID>' . $officeID . '</OfficeID>
                        <MedicalID>' . $medicalId . '</MedicalID>
                        <ComplianceItemType>OtherCompliance</ComplianceItemType>
                        <ComplianceStatus>All</ComplianceStatus>
                        <SequenceID>0</SequenceID>
                    </SearchFilter>
                </GetCaregiverComplianceItemDue>
                </soap:Body>
            </soap:Envelope>';

            if(isset($agencyHHADetail->id) && $agencyHHADetail->id ==self::STATIC_AGENCY_ID){
                $json = SELF::getDataDemo($xml, 'GetCaregiverComplianceItemDue');
            }else{
                $json = SELF::getData($xml, 'GetCaregiverComplianceItemDue');
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
                if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->Result->ErrorInfo->ErrorID == 0) {

                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue)) {
                        $respoe = count($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue);

                        for ($i = 0; $i < $respoe; $i++) {

                            $CaregiverMedicalID = '';
                            
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID != '') {
                                $CaregiverMedicalID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID;
                            }

                            $medicalID = '';
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID != '') {
                                $medicalID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID;
                            }
                            $medicalName = NULL;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName != '') {
                                $medicalName =(array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName;
                            }
                            $status = NULL;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status != '') {
                                $status = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status;
                            }
                            $caregiverID = "";
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID != '') {
                                $caregiverID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID;
                            }
                            $dueDate = null;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate != '') {
                                $dueDate = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate;
                            }
                            $officeID = null;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID != '') {
                                $officeID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID;
                            }


                            $CaregiverMedicalID = null;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID != '') {
                                $CaregiverMedicalID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID;
                            }

                            $temp = [
                                'caregiver_medical_id'=>isset($CaregiverMedicalID[0])?$CaregiverMedicalID[0]:"",
                                'medical_name'=>isset($medicalName[0])?$medicalName[0]:"",
                                'medical_id'=>isset($medicalID[0])?$medicalID[0]:"",
                                'due_date' =>isset($dueDate[0])?$dueDate[0]:"",
                                'status' =>isset($status[0])?$status[0]:"",
                                'office_id' =>isset($officeID[0])?$officeID[0]:"",
                                'caregiverID' =>isset($caregiverID[0])?$caregiverID[0]:"",
                            ];

                            $final[] = $temp;
                        }
                    }
                }
            }
        }
       return $final;
    }

    public static function getVisitNew($details, $startDate, $endDate)
    {
        $finalData = [];
        if(isset($details->agencyDetails->app_name) && $details->agencyDetails->app_name !=""){
            $AppName = $details->agencyDetails->app_name;
            $AppSecret = $details->agencyDetails->app_key;
            $AppKey = $details->agencyDetails->app_token;
          
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
                            <PatientID xsi:nil="true" />
                                    <CaregiverID>' . $details->caregiver_id . '</CaregiverID>
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
                        }
                    }
                }

                // $remainingIds = array_diff($visitIDs, $currentVisitIds);

                foreach ($visitIDs as $visit) {
                   $finalData[] =  SELF::getScheduleUpdate($visit, $AppName, $AppSecret, $AppKey);
                }
            }
        }
        return $finalData;
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
        $json = curl_exec($ch);

        return $json;
    }

    public static function searchCaregiverForHHAWithAll($agencyId,$search){
      
        $agency = Agency::select('app_name','app_key','app_token')->where('id', $agencyId)->first();
        $saveArray = [];

        if(isset($agency->app_name)){
           
            $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                    <SearchCaregivers xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>' . $agency->app_name . '</AppName>
                            <AppSecret>' . $agency->app_key . '</AppSecret>
                            <AppKey>' . $agency->app_token . '</AppKey>
                        </Authentication>
                        <SearchFilters>
                                <Status>All</Status>
                                <CaregiverCode>'.$search['hha_caregiver_code_id'].'</CaregiverCode>
                                <FirstName>'.$search['hha_caregiver_first_name'].'</FirstName>
                                <LastName>'.$search['hha_caregiver_last_name'].'</LastName>
                                <PhoneNumber>'.$search['hha_caregiver_phone_no'].'</PhoneNumber>
                                <SSN>'.$search['hha_caregiver_ssn'].'</SSN>
                    </SearchFilters>
                    </SearchCaregivers>
            </soap:Body>
            </soap:Envelope>';

            if($agencyId == self::STATIC_AGENCY_ID){
                $json = SELF::getDataDemo($xml, 'SearchCaregivers');
            }else{
                $json = SELF::getData($xml, 'SearchCaregivers');
            }
            
            if ($json === false) {

            }else{
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
                $xml = simplexml_load_string($clean_xml);
            
                $final = [];
                if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID) && $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Result->ErrorInfo->ErrorID == 0) {
                    if (isset($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers)) {
                        $respoe = count($xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers);
                        for ($i = 0; $i < $respoe; $i++) {
                            $caregiverId = $xml->Body->SearchCaregiversResponse->SearchCaregiversResult->Caregivers[$i]->CaregiverID;

                        $getDetails  = self::getHHACaregiverDetails(addslashes($caregiverId),$agencyId);

                        $final[]=$getDetails;
                        }
                        
                    }
                }
                if(!empty($final[0])){
                    foreach($final as $val){
                    $save = [
                        'caregiver_id'=>$val['caregiver_id'],
                        'officeId'=>$val['officeId'],
                        'office_name'=>$val['office_name'],
                        'agency_fk'=>$agencyId,
                        'first_name'=>$val['firstName'],
                        'middle_name'=>$val['middleName'],
                        'last_name'=>$val['lastName'],
                        'gender'=>$val['gender'],
                        'ssn'=>$val['ssn'],
                        
                        'dob'=>date('Y-m-d',strtotime($val['dob'])),
                        'caregiver_code'=>$val['caregiverCode'],
                        'mobile_or_sms'=>str_replace('-','',$val['notificationMobile']),
                        'hha_delete_flag'=>"N",
                        'hha_sync'=>"N",
                        'created_at'=>date('Y-m-d H:i:s'),
                        'hhasyncdatetime'=>date('Y-m-d H:i:s'),
                        'EmploymentTypesDiscipline'=>$val['EmploymentTypesDiscipline'],
                        'TeamName'=>$val['teamName'],
                        'last_work_date'=>date('Y-m-d',strtotime($val['lastWorkDate'])),
                        'zipcode'=>$val['zip'],
                        'language'=>$val['lang'],
                        'status'=>$val['status'],
                        'address1'=>$val['address'],
                        'address2'=>$val['address2'],
                        'City'=>$val['city'],
                        'State'=>$val['state'],
                        'Zip5'=>$val['zip'],
                        'HomePhone'=>str_replace('-','',$val['phone']),
                        'notification_mobile_no'=>str_replace('-','',$val['notificationMobile']),
                        'emergencyName'=>$val['emergencyName'],
                        'emergencyPhone1'=>$val['emergencyPhone1'],
                        'hha_search'=>1
                        
                    ];
                    $saveArray[] = $save;
                    
                    }
                
                }
            }
        }
        return $saveArray;
    }

    public static function getCaregiverMedicalDueList($agencyID, $caregiverId)
    {

        $agencyHHADetail = self::getAgencyDetails($agencyID);

        $data_Array = array();
        $xml = "<soap:Envelope
        xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
        xmlns:xsd='http://www.w3.org/2001/XMLSchema'
        xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
        <soap:Body>
            <GetCaregiverMedicalDetails
                xmlns='https://www.hhaexchange.com/apis/hhaws.integration'>
                <Authentication>
                    <AppName>".$agencyHHADetail->app_name."</AppName>
                    <AppSecret>".$agencyHHADetail->app_key."</AppSecret>
                    <AppKey>".$agencyHHADetail->app_token."</AppKey>
                </Authentication>
                <SearchFilter>
                    <CaregiverID>".$caregiverId."</CaregiverID>
                    <CaregiverComplianceExpItemID>-1</CaregiverComplianceExpItemID>
                    <ComplianceStatus>All</ComplianceStatus>
                </SearchFilter>
            </GetCaregiverMedicalDetails>
        </soap:Body>
    </soap:Envelope>";
        if($agencyHHADetail->id == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'GetCaregiverMedicalDetails');
        }else{
            $json = SELF::getData($xml, 'GetCaregiverMedicalDetails');
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

            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                    $totalRecord = count($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails);
                    for ($i = 0; $i < $totalRecord; $i++) {
                            $medicalID = '';
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID != '') {
                                $medicalID = (array)$xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID;
                            }

                            $medicalName = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName != '') {
                                $medicalName = (array)$xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName;
                            }

                            $status = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status != '') {
                                $status = (array)$xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status;
                            }

                            $dueDate = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate != '') {
                                $dueDate = (array)$xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate;
                            }
                            
                            $caregiverMedicalID = null;
                            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID != '') {
                                $caregiverMedicalID =  (array)$xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID;
                            }

                            if(isset($status[0]) && $status[0] !="Completed"){
                                $mName = $medicalName[0]??"";
                                $mstatus = $status[0]??"";
                                $mDueDate="";
                                if(isset($dueDate[0])){
                                    $mDueDate = $dueDate[0]??"";
                                }
                                $data_Array[] = [
                                    'id'=>$medicalID[0]??"",
                                    'name'=>$mName.' - '.$mstatus.' - '.$mDueDate,
                                    'CaregiverMedicalID'=>$caregiverMedicalID[0]??""
                                ];
                            }
                    }
                }

            }

        }
        return $data_Array;
    }

    public static function updateHHAMedicalDetails($data){
        $agencyHHADetail = self::getAgencyDetails($data['agency_id']);

        $data_Array = array();
        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <UpdateCaregiverMedical
                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>'.$agencyHHADetail->app_name.'</AppName>
                            <AppSecret>'.$agencyHHADetail->app_key.'</AppSecret>
                            <AppKey>'.$agencyHHADetail->app_token.'</AppKey>
                        </Authentication>
                        <CaregiverMedicalInfo>
                            <CaregiverID>'.$data['caregiver_id'].'</CaregiverID>
                            <MedicalID>'.$data['medical_id'].'</MedicalID>
                            <DueDate xsi:nil="true"/>
                            <DateCompleted>'.$data['datePerform'].'</DateCompleted>
                            <Notes></Notes>
                            <ResultID>'.$data['result'].'</ResultID>
                            <CaregiverMedicalID>'.$data['caregiver_medical_id'].'</CaregiverMedicalID>
                            <FileName></FileName>
                            <StreamData></StreamData>
                        </CaregiverMedicalInfo>
                    </UpdateCaregiverMedical>
                </soap:Body>
            </soap:Envelope>';
         
            if($agencyHHADetail->id == self::STATIC_AGENCY_ID){
                
                $json = SELF::getDataDemo($xml, 'UpdateCaregiverMedical');
            }else{
                $json = SELF::getData($xml, 'UpdateCaregiverMedical');
            }
            
            $message = "";
            $status = 0;
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
                if (isset($xml->Body->UpdateCaregiverMedicalResponse->UpdateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->UpdateCaregiverMedicalResponse->UpdateCaregiverMedicalResult->Result->ErrorInfo->ErrorID == 0) {
                    $caregiverInfo = $xml->Body->UpdateCaregiverMedicalResponse->UpdateCaregiverMedicalResult->CaregiverMedicalResultInfo ?? null;
                    if ($caregiverInfo && !empty($caregiverInfo->CaregiverID) && !empty($caregiverInfo->CaregiverMedicalID)) {
                        $caregiverId = (string)$caregiverInfo->CaregiverID;
                        $complianceId = (string)$caregiverInfo->CaregiverMedicalID;
                        
                        $finalResponse = [
                            'caregiver_id'=>$caregiverId,
                            'medical_id'=>$complianceId,
                        ];

                    }
                    $status = 1;
                }
                
                if (isset($xml->Body->UpdateCaregiverMedicalResponse->UpdateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->UpdateCaregiverMedicalResponse->UpdateCaregiverMedicalResult->Result->ErrorInfo->ErrorID !=0) {
                    $message = (string)$xml->Body->UpdateCaregiverMedicalResponse->UpdateCaregiverMedicalResult->Result->ErrorInfo->ErrorMessage[0];
                    $status = 0;
                }
                if (isset($xml->Body->Fault->faultcode) && $xml->Body->Fault->faultcode !="") {
                    $message = (string)$xml->Body->Fault->faultstring;
                    $status = 0;
                }
            }
           return  ['data'=>$finalResponse,'status'=>$status,'message'=>$message];
    }

    private static function getAgencyDetails($agencyID){
        return Agency::where('id', $agencyID)->first();
    }

    public static function createNewCaregiverMedicalTest($response){
        $agencyHHADetail = self::getAgencyDetails($response['agency_id']);
        if(isset($agencyHHADetail->app_name)){
            $xml = '<soap:Envelope
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <CreateCaregiverMedical
                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                        <AppName>' . $agencyHHADetail->app_name . '</AppName>
                        <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                        <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                        </Authentication>
                        <CaregiverMedicalInfo>
                            <CaregiverID>' . $response['caregiver_id'] . '</CaregiverID>
                            <MedicalID>' . $response['hha_medical_document_medical_id'] . '</MedicalID>';
                            if(isset($response['hha_medical_document_due_date']) && $response['hha_medical_document_due_date'] !=""){
                                $xml .='<DueDate>'.date('Y-m-d',strtotime($response['hha_medical_document_due_date'])).'</DueDate>';
                            }else{
                                $xml .='<DueDate  xsi:nil="true" />';
                            }
                            if(isset($response['hha_medical_document_date_perform']) && $response['hha_medical_document_date_perform'] !=""){
                                $xml .='<DateCompleted>'.date('Y-m-d',strtotime($response['hha_medical_document_date_perform'])).'</DateCompleted>';
                            }else{
                                $xml .='<DateCompleted  xsi:nil="true" />';
                            }
                            
                            $xml .='<Notes></Notes>';
                            if($response['hha_medical_document_result'] !=""){
                                $xml .='<ResultID>'.$response['hha_medical_document_result'].'</ResultID>';
                            }else{
                                $xml .='<ResultID>-1</ResultID>';
                            }
                            $xml .='<FileName></FileName>';
                            if($response['image'] !=""){
                                $xml .='<StreamData></StreamData>';
                            }else{
                                $xml .='<StreamData></StreamData>';
                               
                            }
                            
                        $xml .='</CaregiverMedicalInfo>
                    </CreateCaregiverMedical>
                </soap:Body>
            </soap:Envelope>';

            if($response['agency_id'] == self::STATIC_AGENCY_ID){
                $json = self::getDataDemo($xml, 'CreateCaregiverMedical');
            }else{
                $json = self::getData($xml, 'CreateCaregiverMedical');
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
              
                if (isset($xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID == 0) {
                    return 1;
                }
                if (isset($xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID) && $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorID !== 0) {
    
                    return $xml->Body->CreateCaregiverMedicalResponse->CreateCaregiverMedicalResult->Result->ErrorInfo->ErrorMessage[0];
                }
            }
            return 0;
        }
    }

    public static function getAbDocumentByCaregiverI9Requirements($agencyId){
        $agencyHHADetail = self::getAgencyDetails($agencyId);
        if (!isset($agencyHHADetail->id)) {
            return [];
        }
        $data = [];
        $xml = '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetI9Documents
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                    <AppName>' . $agencyHHADetail->app_name . '</AppName>
                    <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                    <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                </GetI9Documents>
            </soap:Body>
        </soap:Envelope>';

        $json = (self::STATIC_AGENCY_ID == $agencyId)
        ? self::getDataDemo($xml, 'GetI9Documents'): self::getData($xml, 'GetI9Documents');

        if ($json === false) {
            return self::handleJsonError();
        }
        
        $xmlObj = self::parseSoapResponseNew($json);
        if (!$xmlObj) {
            return [];
        }

        $response = $xmlObj->Body->GetI9DocumentsResponse->GetI9DocumentsResult ?? null;
        if (!$response || (int)$response->Result->ErrorInfo->ErrorID !== 0) {
            return [];
        }

        $documents = $response->I9Documents->I9Document ?? [];
        foreach ($documents as $doc) {
            $data[] = [
                'id'   => (string)($doc->I9DocumentID ?? ''),
                'name' => (string)($doc->I9DocumentName ?? ''),
            ];
        }
        return $data;
    }

    public static function getCDocumentByCaregiverI9Requirements($agencyId){
        $agencyHHADetail = self::getAgencyDetails($agencyId);
        if (!isset($agencyHHADetail->id)) {
            return [];
        }

        $xml = self::buildSoapRequestNew($agencyHHADetail);

        $json = ($agencyId == self::STATIC_AGENCY_ID)
            ? self::getDataDemo($xml, 'GetI9ColumnCDocument'): self::getData($xml, 'GetI9ColumnCDocument');

        if ($json === false) {
            return self::handleJsonError();
        }

        $xmlObj = self::parseSoapResponseNew($json);
        if (!$xmlObj) {
            return [];
        }

        $response = $xmlObj->Body->GetI9ColumnCDocumentResponse->GetI9ColumnCDocumentResult ?? null;

        if (!$response || (int)$response->Result->ErrorInfo->ErrorID !== 0) {
            return [];
        }

        $documents = $response->GetI9ColumnCDocument->GetI9ColumnCDocumentInfo ?? [];
        $data = [];

        foreach ($documents as $doc) {
            $data[] = [
                'id'   => (string)($doc->I9ColumnCDocumentID ?? ''),
                'name' => (string)($doc->DocumentName ?? ''),
            ];
        }
        return $data;
    }

    private static function buildSoapRequestNew($agencyHHADetail)
    {
        return '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetI9ColumnCDocument xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                        <AppName>' . $agencyHHADetail->app_name . '</AppName>
                        <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                        <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                </GetI9ColumnCDocument>
            </soap:Body>
        </soap:Envelope>';
    }

    private static function handleJsonError()
    {
        $json = json_encode(["jsonError", json_last_error_msg()]);
        if ($json === false) {
            $json = '{"jsonError": "unknown"}';
        }
        http_response_code(500);
        return [];
    }

    private static function parseSoapResponseNew($json)
    {
        $cleanXml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
        return simplexml_load_string($cleanXml);
    }

    public static function hhaUpdateCaregiverI9Requirements($data,$agencyId){
        $agencyHHADetail = self::getAgencyDetails($agencyId);
        if (!isset($agencyHHADetail->id)) {
            return [];
        }

        $xml = '<soap:Envelope
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <UpdateCaregiverI9Requirements
                        xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                        <Authentication>
                            <AppName>' . $agencyHHADetail->app_name . '</AppName>
                            <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                            <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                        </Authentication>
                        <UpdateCaregiverI9RequirementsInfo>
                            <CaregiverID>'.$data['caregiverId'].'</CaregiverID>';
                            if($data['I9ABDocumentID'] !=""){
                                $xml .='<I9ABDocumentID>'.$data['I9ABDocumentID'].'</I9ABDocumentID>';
                            }else{
                                $xml .='<I9ABDocumentID xsi:nil="true" />'; 
                            }
                            if($data['I9CDocumentID'] !=""){
                                $xml .='<I9CDocumentID>'.$data['I9CDocumentID'].'</I9CDocumentID>';
                            }else{
                                $xml .='<I9CDocumentID xsi:nil="true" />'; 
                            }
                            
                            if($data['I9Verified'] !=""){
                                $xml .='<I9Verified>'.$data['I9Verified'].'</I9Verified>';
                            }else{
                                $xml .='<I9Verified xsi:nil="true" />'; 
                            }
                        
                            if($data['I9DocumentExpiration'] !=""){
                                $xml .='<I9DocumentExpiration>'.$data['I9DocumentExpiration'].'</I9DocumentExpiration>';
                            }else{
                                $xml .='<I9DocumentExpiration xsi:nil="true"/>'; 
                            }
                            if($data['HireDate'] !=""){
                                $xml .='<HireDate>'.$data['HireDate'].'</HireDate>';
                            }else{
                                $xml .='<HireDate xsi:nil="true"/>'; 
                            }
                            $xml .='<I9Notes>'.$data['I9Notes'].'</I9Notes>';
                            $xml .='<I9EVerifyNumber>'.$data['I9EVerifyNumber'].'</I9EVerifyNumber>';
                            
                        $xml .='</UpdateCaregiverI9RequirementsInfo>
                    </UpdateCaregiverI9Requirements>
                </soap:Body>
            </soap:Envelope>';
            
        $json = ($agencyId == self::STATIC_AGENCY_ID)
            ? self::getDataDemo($xml, 'UpdateCaregiverI9Requirements'): self::getData($xml, 'UpdateCaregiverI9Requirements');

        
        if ($json === false) {
            return self::handleJsonError();
        }

        $xmlObj = self::parseSoapResponseNew($json);
        if (!$xmlObj) {
            return 0;
        }

        if (isset($xmlObj->Body->UpdateCaregiverI9RequirementsResponse->UpdateCaregiverI9RequirementsResult->Result->ErrorInfo->ErrorID) && $xmlObj->Body->UpdateCaregiverI9RequirementsResponse->UpdateCaregiverI9RequirementsResult->Result->ErrorInfo->ErrorID == 0) {
            return 1;
        }
        if (isset($xmlObj->Body->UpdateCaregiverI9RequirementsResponse->UpdateCaregiverI9RequirementsResult->Result->ErrorInfo->ErrorID) && $xmlObj->Body->UpdateCaregiverI9RequirementsResponse->UpdateCaregiverI9RequirementsResult->Result->ErrorInfo->ErrorID != 0) {

            return $xmlObj->Body->UpdateCaregiverI9RequirementsResponse->UpdateCaregiverI9RequirementsResult->Result->ErrorInfo->ErrorMessage[0];
        }
    }

    public static function updateCaregiverOtherCompliance($response){
       
        $agencyHHADetail = self::getAgencyDetails($response['agency_id']);
        if (!isset($agencyHHADetail->id)) {
            return [];
        }

        $notes = "";
        if(isset($response['notes']) && $response['notes'] !=""){
            $notes = $response['notes'];
        }
        $finalResponse = array();
        $xml = '<soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <UpdateCaregiverOtherCompliance
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                            <AppName>' . $agencyHHADetail->app_name . '</AppName>
                        <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                        <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                    <caregiverOtherComplianceInfo>
                        <CaregiverID>'.$response['caregiver_id'].'</CaregiverID>
                        <OtherComplianceID>'.$response['caregiver_other_compliance_id'].'</OtherComplianceID>
                        <DueDate>'.$response['due_date'].'</DueDate>
                        <DateCompleted>'.$response['completed_date'].'</DateCompleted>
                        <Notes>'.$notes.'</Notes>
                        <Score xsi:nil="true"/>
                        <FileName>' . $response['medical_name']. '.' . $response['extension'] . '</FileName>
                        <StreamData>' . base64_encode($response['file']) . '</StreamData>
                        <OtherComplianceResultID>'.$response['result'].'</OtherComplianceResultID>
                        <CaregiverOtherComplianceID>'.$response['compliance_id'].'</CaregiverOtherComplianceID>
                    </caregiverOtherComplianceInfo>
                </UpdateCaregiverOtherCompliance>
            </soap:Body>
        </soap:Envelope>';
        if($agencyHHADetail->id == self::STATIC_AGENCY_ID){
            $json = SELF::getDataDemo($xml, 'UpdateCaregiverOtherCompliance');
        }else{
            $json = SELF::getData($xml, 'UpdateCaregiverOtherCompliance');
        }

        if ($json === false) {
            return self::handleJsonError();
        }

        $xmlObj = self::parseSoapResponseNew($json);
        if (!$xmlObj) {
            return 0;
        }
        
        $message = "";
        if (
            isset($xmlObj->Body->UpdateCaregiverOtherComplianceResponse->UpdateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID)
            && (int)$xmlObj->Body->UpdateCaregiverOtherComplianceResponse->UpdateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID === 0
        ) {
            $caregiverInfo = $xmlObj->Body->UpdateCaregiverOtherComplianceResponse->UpdateCaregiverOtherComplianceResult->CaregiverOtherComplianceResultInfo ?? null;
        
            if ($caregiverInfo && !empty($caregiverInfo->CaregiverID) && !empty($caregiverInfo->CaregiverOtherComplianceID)) {
                $caregiverId = (string)$caregiverInfo->CaregiverID;
                $complianceId = (string)$caregiverInfo->CaregiverOtherComplianceID;
                
                $finalResponse = [
                    'caregiver_id'=>$caregiverId,
                    'complianceId'=>$complianceId,
                ];

            }
            $status = 1;
        }
      
        if (isset($xmlObj->Body->UpdateCaregiverOtherComplianceResponse->UpdateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID) && $xmlObj->Body->UpdateCaregiverOtherComplianceResponse->UpdateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorID !=0) {
            $message = $xmlObj->Body->UpdateCaregiverOtherComplianceResponse->UpdateCaregiverOtherComplianceResult->Result->ErrorInfo->ErrorMessage[0];
            $status = 0;
        }

        return  ['data'=>$finalResponse,'status'=>$status,'message'=>$message];
    }

    public static function GetCaregiverComplianceItemDueNew($caregiverId,$agencyId,$type="1")
    {
        //$caregiverIDs = HHACaregivers::getData();
        //$caregiverIDs = HHACaregivers::where("id", "110413")->get();
        $caregiverIDs =  HHACaregivers::whereNull('deleted_at')->where('caregiver_id', $caregiverId)->where('agency_fk', $agencyId)->get();
        
        $lastScanDetails = '0';
        
        $finalResponseOtherArray = [];
        foreach ($caregiverIDs as $caregiver) {

            if($type ==1){
                HhaOtherComplience::updateData(array('del_flag' => 'Y'), array('agency_id' => $caregiver->agency_fk, 'caregiver_id' => $caregiver->caregiver_id));
            }
            
            $agencyHHADetail = Agency::getAllDetailsbyAgencyId($caregiver->agency_fk);
            $otherMedicals = [];
            if ($lastScanDetails != $caregiver->agency_fk . '-' . $caregiver->officeId) {
                $otherMedicals = SELF::getCaregiverOtherCompliance($caregiver->agency_fk, $caregiver->officeId);
            }
            //  print_r($otherMedicals);
    
            foreach ($otherMedicals as $medicalIds) {
                $tempOtherMedicalArray = [];
                $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                    <soap:Body>
                        <GetCaregiverMedicalDetails xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                            <AppName>' . $agencyHHADetail->app_name . '</AppName>
                            <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                            <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                            </Authentication>
                            <SearchFilter>
                                <CaregiverID>' . $caregiver->caregiver_id . '</CaregiverID>
                            </SearchFilter>
                        </GetCaregiverMedicalDetails>
                    </soap:Body>
                </soap:Envelope>';

                if($caregiver->agency_fk == self::STATIC_AGENCY_ID){
                    $json = SELF::getDataDemo($xml, 'GetCaregiverMedicalDetails');
                }else{
                    $json = SELF::getData($xml, 'GetCaregiverMedicalDetails');
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

                    if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {

                        if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                            $respoe = count($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails);

                            for ($i = 0; $i < $respoe; $i++) {

                                $medicalID = '';
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID != '') {
                                  $medicalID1 = $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalID;
                                  $medicalID =addslashes($medicalID1);
                                }

                                $medicalName = NULL;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName != '') {
                                    $medicalName = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->MedicalName);
                                }
                                $status = NULL;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status != '') {
                                    $status = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Status);
                                }
                                $caregiverID = "";
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID != '') {
                                    $caregiverID = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverID);
                                }
                                $dueDate = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate != '') {
                                    $dueDate = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DueDate);
                                }
                                $officeID = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                    $officeID = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID);
                                }

                                $caregiverMedicalID = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->OfficeID != '') {
                                    $caregiverMedicalID = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->CaregiverMedicalID);
                                }

                                $datePerform = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed != '') {
                                    $datePerform = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed);
                                }

                                $result = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Result) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Result != '') {
                                    $result = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Result);
                                }

                                $notes = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Notes) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Notes != '') {
                                    $notes = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->Notes);
                                }

                                $modifiedDate = null;
                                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->ModifiedDate) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->ModifiedDate != '') {
                                    $modifiedDate = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->ModifiedDate);
                                }

                                if($medicalIds['id'] ==$medicalID){
                                    if($type ==1){
                                        HhaOtherComplience::updateOrCreate([
                                            'agency_id'        => $caregiver->agency_fk,
                                            'caregiver_id' => $caregiverID,
                                            'caregiver_medical_id' => $caregiverMedicalID??"",
                                            'medical_id' => $medicalID,
                                        ], [
                                            'medical_id' => $medicalID??"",
                                            'medical_name' => $medicalName??"",
                                            'due_date' => $dueDate??"",
                                            'status' => $status??"",
                                            'office_id' => $officeID[0]??"",
                                            'caregiver_medical_id' => $caregiverMedicalID??"",
                                            'del_flag' => 'N',
                                            'updated_date' => date('Y-m-d H:i:s')
        
                                        ]);
                                    }else{
                                        $tempOtherMedicalArray =[
                                            'agency_id'=>$caregiver->agency_fk,
                                            'caregiver_id'=>$caregiverID,
                                            'caregiver_medical_id'=>$caregiverMedicalID??"",
                                            'due_date'=>$dueDate??"",
                                            'medical_id'=>$medicalID,
                                            'medical_name'=>$medicalName??"",
                                            'office_id'=>$officeID??"",
                                            'status'=>$status??"",
                                            'date_perform'=>$datePerform??"",
                                            'result'=>$result??"",
                                            'notes'=>$notes??"",
                                            'modified_date'=>$modifiedDate??"",
                                        ];
    
                                        $finalResponseOtherArray[] = $tempOtherMedicalArray;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($type ==1){
                HHACaregivers::where('id', $caregiver->id)->update(array("hhasync_othecomplience" => date('Y-m-d H:i:s')));
            } 
        }

        if($type ==1){
            return 1;
        }else{
            return $finalResponseOtherArray;
        }
    }

    public static function GetAllCaregiverComplianceItemDueMedical($agencyHHADetail,$officeID,$medicalId,$sequence){
        $final = [];

        if(!empty($agencyHHADetail->app_name) && !empty($agencyHHADetail->app_key) && !empty($agencyHHADetail->app_token)){
            $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <soap:Body>
                <GetCaregiverComplianceItemDue xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                    <AppName>' . $agencyHHADetail->app_name . '</AppName>
                    <AppSecret>' . $agencyHHADetail->app_key . '</AppSecret>
                    <AppKey>' . $agencyHHADetail->app_token . '</AppKey>
                    </Authentication>
                    <SearchFilter>
                        <CaregiverID>-1</CaregiverID>
                        <OfficeID>' . $officeID . '</OfficeID>
                        <MedicalID>' . $medicalId . '</MedicalID>
                        <ComplianceItemType>Medical</ComplianceItemType>
                        <ComplianceStatus>All</ComplianceStatus>
                        <SequenceID>'.$sequence.'</SequenceID>
                    </SearchFilter>
                </GetCaregiverComplianceItemDue>
                </soap:Body>
            </soap:Envelope>';
            $json = SELF::getData($xml, 'GetCaregiverComplianceItemDue');
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
            
                if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->Result->ErrorInfo->ErrorID == 0) {

                    if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue)) {
                        $respoe = count($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue);

                        for ($i = 0; $i < $respoe; $i++) {

                            $CaregiverMedicalID = '';
                            
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID != '') {
                                $CaregiverMedicalID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID;
                            }

                            $medicalID = '';
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID != '') {
                                $medicalID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalID;
                            }
                            $medicalName = NULL;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName != '') {
                                $medicalName =(array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->MedicalName;
                            }
                            $status = NULL;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status != '') {
                                $status = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->Status;
                            }
                            $caregiverID = "";
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID != '') {
                                $caregiverID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverID;
                            }
                            $dueDate = null;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate != '') {
                                $dueDate = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->DueDate;
                            }
                            $officeID = null;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID != '') {
                                $officeID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID;
                            }


                            $CaregiverMedicalID = null;
                            if (isset($xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID) && $xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->OfficeID != '') {
                                $CaregiverMedicalID = (array)$xml->Body->GetCaregiverComplianceItemDueResponse->GetCaregiverComplianceItemDueResult->CaregiverComplianceItemDue[$i]->CaregiverMedicalID;
                            }

                            $temp = [
                                'caregiver_medical_id'=>isset($CaregiverMedicalID[0])?$CaregiverMedicalID[0]:"",
                                'medical_name'=>isset($medicalName[0])?$medicalName[0]:"",
                                'medical_id'=>isset($medicalID[0])?$medicalID[0]:"",
                                'due_date' =>isset($dueDate[0])?$dueDate[0]:"",
                                'status' =>isset($status[0])?$status[0]:"",
                                'office_id' =>isset($officeID[0])?$officeID[0]:"",
                                'caregiverID' =>isset($caregiverID[0])?$caregiverID[0]:"",
                            ];

                            $final[] = $temp;
                        }
                    }
                }
            }
        }
       return $final;
    }
}