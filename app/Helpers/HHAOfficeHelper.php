<?php

namespace App\Helpers;

use App\Agency;
use App\Model\HHAOffice;


class HHAOfficeHelper
{
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
    
    public static function syncOffice($agencyID){
        if($agencyID !=""){
            $agencyDetails = Agency::where('enable_hha', 1)->where('id',$agencyID)->get();
        }else{
            $agencyDetails = Agency::where('enable_hha', 1)->get();
        }
        foreach ($agencyDetails as $agency) {
            if (!empty($agency->app_name) && !empty($agency->app_key)  && !empty($agency->app_token)) {
                $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                        <GetOffices xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                <AppName>' . $agency->app_name . '</AppName>
                                <AppSecret>' . $agency->app_key . '</AppSecret>
                                <AppKey>' . $agency->app_token . '</AppKey>
                            </Authentication>
                            	
                        </GetOffices>
                </soap:Body>
                </soap:Envelope>';
                if($agencyID ==2){
                    $json = SELF::getDataDemo($xml, 'GetOffices');
                }else{
                    $json = SELF::getData($xml, 'GetOffices');
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
                    if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetOfficesResponse->GetOfficesResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office)) {
                            $cntOffice = count($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office);
                            
                            for ($i = 0; $i < $cntOffice; $i++) {
                                $data = $xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i];
                                $officeId = '';
                                if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeID) && $xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeID != '') {
                                    $officeId =addslashes($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeID);
                                }

                                $officeName = '';
                                if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeName) && $xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeName != '') {
                                    $officeName =addslashes($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeName);
                                }

                                $officeCode = '';
                                if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeCode) && $xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeCode != '') {
                                    $officeCode =addslashes($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeCode);
                                }

                                $data =[
                                    'office_name'=>$officeName,
                                    'office_code'=>$officeCode
                                ];

                                $query = HHAOffice::where('agency_fk',$agency->id)->where('office_id',$officeId)->first();
                                if(isset($query->id)){
                                    $data['updated_at'] = date('Y-m-d H:i:s');
                                }else{
                                    $data['created_at'] = date('Y-m-d H:i:s');
                                }
                                HHAOffice::updateOrCreate([
                                    'agency_fk'        => $agency->id,
                                    'office_id' => $officeId,
                                    
                                ],$data);
                            }

                        }

                    }
                }
            }
        }
        return 1;
    }

    public static function getAllOffice($agencyID){
        $agencyDetails = Agency::where('enable_hha', 1)->where('id',$agencyID)->get();
     
        $data = array();
        foreach ($agencyDetails as $agency) {
            if (!empty($agency->app_name) && !empty($agency->app_key)  && !empty($agency->app_token)) {
               
                $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                        <GetOffices xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                            <Authentication>
                                <AppName>' . $agency->app_name . '</AppName>
                                <AppSecret>' . $agency->app_key . '</AppSecret>
                                <AppKey>' . $agency->app_token . '</AppKey>
                            </Authentication>
                                
                        </GetOffices>
                </soap:Body>
                </soap:Envelope>';
                if($agencyID ==2){
                   
                    $json = SELF::getDataDemo($xml, 'GetOffices');
                    info($json);
                }else{
                  
                    $json = SELF::getData($xml, 'GetOffices');
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
                    
                    if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetOfficesResponse->GetOfficesResult->Result->ErrorInfo->ErrorID == 0) {
                        if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office)) {
                            $cntOffice = count($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office);
                           
                            for ($i = 0; $i < $cntOffice; $i++) {
                              

                                $officeId = '';
                                if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeID) && $xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeID != '') {
                                    $officeId =addslashes($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeID);
                                }

                                $officeName = '';
                                if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeName) && $xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeName != '') {
                                    $officeName =addslashes($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeName);
                                }

                                $officeCode = '';
                                if (isset($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeCode) && $xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeCode != '') {
                                    $officeCode =addslashes($xml->Body->GetOfficesResponse->GetOfficesResult->Offices->Office[$i]->OfficeCode);
                                }

                                $data[] = array(
                                    'office_name'=>$officeName,
                                    'office_code'=>$officeCode,
                                    'id'=>$officeId
                                );
                            }

                        }

                    }
                }
            }
        }
       
        return $data;
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        $json = curl_exec($ch);

        return $json;
    }

    public static function getDetailsByOfficeIdAndAgencyId($officeId,$agencyId){
        return HHAOffice::select('id','agency_fk','office_id','office_name','office_code')->where('office_id',$officeId)->where('agency_fk',$agencyId)->first();
    }
}