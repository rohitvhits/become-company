<?php

namespace App\Helpers;
use App\Model\Robort;
use App\Agency;
use Illuminate\Support\Facades\Cache;
class RobortHelper
{
    protected const REMOTE_FOCUS_PER_PAGE="50";
    public static function insertData($data){
        $insert = Robort::updateOrCreate(['patientId'=>$data['patientId'],'agency_id'=>$data['agency_id']],
            $data
        );
       
        return $insert;
    }

    public static function getExistingData(){
        return Robort::where('del_flag','N')->pluck('patientId');
    }
    public static function getLogin($grantType,$robortUserName,$robortUserPassword){
        $test ='{"grant_type":"'.$grantType.'",  "client_id":"'.$robortUserName.'", "client_secret":"'.$robortUserPassword.'" }';
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.emmacare.com/api/auth/oauth/v2/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$test,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }

    public static function getPatientList($page,$token){
       
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('ROBORT_LINK').'/patient?page='.$page,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$token,
            'Accept: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }


    public static function PatientORUTRN($externalId,$token,$page){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('ROBORT_LINK').'/patient-oru?filter[externalId]='.$externalId.'&page='.$page,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$token,
            'Accept: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }

    public static function fetchPatientReadingList($details,$page){
        $loginDetails = Cache::get('fetch-patient-reading-list', function ()use($details) {
			return self::getLogin($details->agencyDetails->robort_grant_type,$details->agencyDetails->robort_user_name,$details->agencyDetails->robort_user_password);
		}, 10 * 60);
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('ROBORT_LINK1').'/patient/reading-answers?page='.$page.'&filter[externalId]='.$details->externalId.'&sort=createdAt:desc',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$loginDetails['access_token'],
            'Accept: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }

    public static function fetchPatientMedicationsList($details,$page){
        $loginDetails = Cache::get('patient-medical-remote-list', function ()use($details) {
			return self::getLogin($details->agencyDetails->robort_grant_type,$details->agencyDetails->robort_user_name,$details->agencyDetails->robort_user_password);
		}, 10 * 60);
        
        
        $urls = env('ROBORT_LINK1').'/patient/medications?filter[externalId]='.$details->externalId.'&page='.$page;
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urls,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$loginDetails['access_token'],
            'Accept: application/json'
        ),
        ));

         $response = curl_exec($curl);

        curl_close($curl);
     
        return json_decode($response,true);
    }

    public static function saveVisit($token,$getDetails){
        $urls = env('ROBORT_LINK').'/caregiver-schedule';

        $test =json_encode($getDetails);
        echo $test;
   
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urls,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$test,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$token,
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }
        
    public static function sendReferral($agency_id,$data){
        $getAgencyDetails = Agency::getIdById($agency_id);

        $loginDetails = RobortHelper::getLogin(trim($getAgencyDetails->robort_grant_type),trim($getAgencyDetails->robort_user_name),trim($getAgencyDetails->robort_user_password));
       
        $urls ='https://api.emmacare.com/api/company/external/referral';

        $test =json_encode($data);
      
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urls,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$test,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$loginDetails['access_token'],
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response,true);
    }
    
    public static function uploadDocument($agency_id,$data,$referralId){
       
        $document = $data['document'];
        $documentPath = $document->getPathname();
        $documentName = $document->getClientOriginalName();
        $mimeType = $document->getClientMimeType();

        $final = [
            'documentPath'=>$documentPath,
            'mimeType'=>$mimeType,
            'documentName'=>$documentName,
            'agency_id'=>$agency_id,
            'notes'=> $data['note'],
            'referralId'=>$referralId,
            'agency_id'=>$agency_id
        ];
        return self::commonDocumentUpload($final);
        
    }

    public static function getInsuranceDetails($agency_id){

        $getAgencyDetails = Agency::getIdById($agency_id);

        $loginDetails = RobortHelper::getLogin(trim($getAgencyDetails->robort_grant_type),trim($getAgencyDetails->robort_user_name),trim($getAgencyDetails->robort_user_password));

        $urls ='https://api.emmacare.com/api/company/external/reference-table';

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urls,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',

        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$loginDetails['access_token'],
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response,true);
    }

    public static function getCarePlanList($agencyId,$externalId){
        $getAgencyDetails = Agency::getIdById($agencyId);
        $loginDetails = Cache::get('fetch-patient-reading-list', function ()use($getAgencyDetails) {
			return self::getLogin(trim($getAgencyDetails->robort_grant_type),trim($getAgencyDetails->robort_user_name),trim($getAgencyDetails->robort_user_password));
		}, 10 * 60);
   

        $urls =env('ROBORT_LINK1').'/patient/last-care-plan?filter[externalId]='.$externalId;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urls,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',

        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$loginDetails['access_token'],
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response,true);
    }

    public static function getPatientRemoteActivityLog($agencyId,$externalId,$page){
        $getAgencyDetails = Agency::getIdById($agencyId);
        $loginDetails = Cache::get('fetch-patient-activity-list', function ()use($getAgencyDetails) {
            return RobortHelper::getLogin(trim($getAgencyDetails->robort_grant_type),trim($getAgencyDetails->robort_user_name),trim($getAgencyDetails->robort_user_password));
        }, 10 * 60);
        
        $urls =env('ROBORT_LINK1').'/patient/activity-log?filter[externalId]='.$externalId.'&page='.$page.'&per-page=10';

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urls,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',

        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$loginDetails['access_token'],
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response,true);
    }

    public static function getPatientDemographicDetails($agencyId,$externalId,$firstName="",$lastName="",$dob=""){
        $data = [
            'externalId'=>$externalId,
            'first_name'=>$firstName,
            'last_name'=>$lastName,
            'dob'=>$dob
        ];
        return self::commonDemographicDetails($agencyId,$data);
        
    }

    private static function commonDemographicDetails($agencyId,$searchArray){
        $getAgencyDetails = Agency::getIdById($agencyId);
        $loginDetails = Cache::get('fetch-patient-activity-list', function ()use($getAgencyDetails) {
            return RobortHelper::getLogin(trim($getAgencyDetails->robort_grant_type),trim($getAgencyDetails->robort_user_name),trim($getAgencyDetails->robort_user_password));
        }, 10 * 60);
        $params = [];

        if(!empty($searchArray['externalId'])){
            $params['filter[externalId]'] = $searchArray['externalId'];
        }

        if(!empty($searchArray['first_name'])){
            $params['filter[firstName]'] = $searchArray['first_name'];
        }
        if(!empty($searchArray['last_name'])){
            $params['filter[lastName]'] = $searchArray['last_name'];
        }
        if(!empty($searchArray['dob'])){
            $params['filter[dob]'] = $searchArray['dob'];
        }
        
        $url =env('ROBORT_LINK').'/patient';
        
        if(!empty($params)){
           $url .= '?'.http_build_query($params);
        }
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',

        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$loginDetails['access_token'],
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response,true);
    }

    public static function documentWiseFileUpload($agency_id,$data,$referralId){
        $final = $data;
        $final['referralId'] = $referralId;
        $final['agency_id'] = $agency_id;
        return self::commonDocumentUpload($final);
    }

    private static function commonDocumentUpload($data){
        $getAgencyDetails = Agency::getIdById($data['agency_id']);

        $loginDetails = RobortHelper::getLogin(trim($getAgencyDetails->robort_grant_type),trim($getAgencyDetails->robort_user_name),trim($getAgencyDetails->robort_user_password));
        
        $urls =env('ROBORT_LINK').'/patient/'.$data['referralId'].'/document';
      
        $curl = curl_init();
        $documentPath = $data['documentPath'];
        $mimeType = $data['mimeType'];
        $documentName = $data['documentName'];
        $note = $data['notes'];
      
        $file = curl_file_create($documentPath, $mimeType, $documentName);
        $data = [
            'document' => $file, // File upload
            'note' => $note// Plain text
        ];

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urls,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$data,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$loginDetails['access_token'],
            'Content-Type: multipart/form-data'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response,true);
    }
}