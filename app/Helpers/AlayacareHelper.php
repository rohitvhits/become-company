<?php

namespace App\Helpers;

use App\Agency;
use Illuminate\Support\Facades\Storage;
class AlayacareHelper
{
    public static function createClient($insertArray)
    {
       
        $username = '';
        $password =  '';
        $link = env("ALAYACARE_LINK") . 'patients/clients';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($insertArray),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode("$username:$password"),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function getClient($username,$password)
    {
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        $link = $agencyDetails->alayacare_url . '/patients/clients/';

        $curl = curl_init();
      
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',

        ));

        $response = curl_exec($curl);
       
        curl_close($curl);
        return  $response;
    }

    public static function getBranches($page,$username,$password)
    {
       
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url . '/patients/branches?page='.$page.'&count=100';
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
      
        return json_decode($response,true);
    }

    public static function getLanguages()
    {

        $alayaCareLanguagesFields = [
            1 => ["Albanian", 'sq'],
            2 => ['Arabic', 'ar'],
            3 => ['Armenian', 'hy'],
            4 => ['Asturian', 'ast'],
            5 => ["Bangla", 'bn'],
            6 => ["Burmese", 'my'],
            7 => ["Cantonese", 'yue'],
            8 => ["Chinese", 'zh'],
            9 => ["Croatian", 'hr'],
            10 => ["Czech", 'cs'],
            11 => ["Dutch", 'nl'],
            12 => ["English", 'en'],
            13 => ["Faroese", 'fo'],
            14 => ["French", 'fr'],
            15 => ["German", 'de'],
            16 => ["Greek", 'el'],
            17 => ["Hebrew", 'he'],
            18 => ["Hindi", 'hi'],
            19 => ["Hungarian", 'hu'],
            20 => ["Italian", 'it'],
            21 => ["Japanese", 'ja'],
            22 => ["Kinyarwanda", 'rw'],
            23 => ["Korean", 'ko'],
            24 => ["Mandar", 'mdr'],
            25 => ["Nepali", 'ne'],
            26 => ["Pashto", 'ps'],
            27 => ["Persian", 'fa'],
            28 => ["Polish", 'pl'],
            29 => ["Portuguese", 'pt'],
            30 => ["Punjabi", 'pa'],
            31 => ["Romanian", 'ro'],
            32 => ["Russian", 'ru'],
            33 => ["Serbian", 'sr'],
            34 => ["Slovak", 'sk'],
            35 => ["Somali", 'so'],
            36 => ["Spanish", 'es'],
            37 => ["Swahili", 'sw'],
            38 => ["Tagalog", 'tl'],
            39 => ["Tamil", 'ta'],
            40 => ["Tigrinya", 'ti'],
            41 => ["Ukrainian", 'uk'],
            42 => ["Urdu", 'ur'],
            43 => ["Vietnamese", 'vi'],
            44 => ["Other", 'other']
        ];

        return $alayaCareLanguagesFields;
    }

    public static function getGroups($page,$branchId,$username,$password)
    {
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        $link = $agencyDetails->alayacare_url . '/patients/groups?page='.$page.'&count=100&branch_id=' . $branchId;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return json_decode($response,true);
    }

    public static function getEmployee($username,$password)
    {
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url . '/employees/employees';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }

    public static function getEmployeeRecord($username,$password,$page,$branch_id)
    {
  
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url . '/employees/employees?page='.$page.'&count=200&branch='.$branch_id;
 
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }

    public static function getEmployeeRecordByAgency($agencyId, $page)
    {
        $agencyDetails = self::getAgencyDetailsWithId($agencyId);

        $link = $agencyDetails->alayacare_url . '/employees/employees?page=' . $page . '&count=500';
      
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public static function getClientRecordByAgency($agencyId, $page)
    {
        $agencyDetails = self::getAgencyDetailsWithId($agencyId);

        $link = $agencyDetails->alayacare_url . '/patients/clients?page=' . $page . '&count=500';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public static function getEmployeeById($agencyId,$empId)
    {
        
        $agencyDetails = self::getAgencyDetailsWithId($agencyId);
       
        $link = $agencyDetails->alayacare_url . '/employees/employees/' . $empId;
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }

    

    public static function getClientData($username,$password,$i,$branchId)
    {
        $agencyDetails = self::getAgencyDetails($username,$password);
       
        $link = $agencyDetails->alayacare_url . '/patients/clients?page='.$i.'&count=100&branch='.$branchId;
      
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',

        ));

        $response = curl_exec($curl);
        
        curl_close($curl);
        return  $response;
    }

    public static function getClientDetailsById($agencyId,$clientId){
        $agencyDetails = self::getAgencyDetailsWithId($agencyId);
       $link = $agencyDetails->alayacare_url. '/patients/clients/' . $clientId;
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',

        ));

        $response = curl_exec($curl);
        
        curl_close($curl);
        return  $response;
    }

    public static function getEmployeeSkill($page,$agencyId,$total=10)
    {
      
        $agencyDetails = self::getAgencyDetailsWithId($agencyId);
        
       $link = $agencyDetails->alayacare_url ."/employees/skills?page=".$page."&count=".$total;

        $curl = curl_init();
        

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        
        return $response;
    }

    public static function getEmployeeSchedular($page,$employeeId,$startDate,$endDate,$username,$password,$recordType){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        if($recordType =='Caregiver'){
            $link = $agencyDetails->alayacare_url ."/scheduler/visits?start_date_from=".$startDate."&start_date_to=".$endDate."&alayacare_employee_id=".$employeeId."&page=".$page."&count=100";
        }else{
            $link = $agencyDetails->alayacare_url ."/scheduler/visits?start_date_from=".$startDate."&start_date_to=".$endDate."&alayacare_client_id=".$employeeId."&page=".$page."&count=100";
        }
        

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }
    
    public static function getVisitDetails($visitId,$username,$password){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/scheduler/visits/".$visitId;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }

    public static function getEmployeeNotes($page,$employeeId,$username,$password){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/employees/employee_notes/".$employeeId."?page=".$page."&count=100";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }

    public static function getEmployeeNotesType($username,$password){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/employees/note_types/";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }

    public static function createEmployeeNotes($employeeId,$username,$password,$data){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/employees/employee_notes/".$employeeId;

        $data = array(
            "status" => "active",
            "note_type" => $data['note_type'],
            "content" => $data['content']
        );
        
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }

    public static function addEmployeeSkill($employeeId,$agencyId,$dataArray){
        
        $agencyDetails = self::getAgencyDetailsWithId($agencyId);
        
        $link = $agencyDetails->alayacare_url ."/employees/employees/".$employeeId."/skills";
     
        $data = array(
            'skill_id'=>(int)$dataArray['skill_id']
        );
       
  
        if(isset($dataArray['flag'])){}else{
           $data['comment'] =$dataArray['content']??"";
           $data['fields'] =$dataArray['fields'];
        }
    
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }
        curl_close($curl);
        return $response;
    }
    
    public static function uploadDocument($employeeId,$username,$password,$data){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $createDirectory = self::createDirectory($employeeId,$username,$password,$data['folder']);
        $response = json_decode($createDirectory,true);
        
        if(isset($response['code']) && ($response['code'] =='201' || $response['code'] =='409')){
          
          $link = $agencyDetails->alayacare_url ."/employees/employees/".$employeeId."/attachments/".$data['folder'].'/'.$data['file_name'];
    
          $headers = [
            'accept: application/json',
          
        ];
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD =>$username .':'.$password ,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    
                    "file" => new \CURLFile($data['file_path']),
                ),
                CURLOPT_HTTPHEADER => $headers,
            ));
    
            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                echo 'Error: ' . curl_error($curl);
            }
            curl_close($curl);
            
            return $response;
        }else{

        }

    }

    public static function createDirectory($employeeId,$username,$password,$folder){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/employees/employees/".urlencode($employeeId)."/attachments/".$folder.'/';
        $headers = array(
            'Accept: application/json',
            
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        
        curl_close($curl);
       
        return $response;
    }

    public static function getDocumentList($employeeId,$username,$password,$folder){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/employees/employees/".$employeeId."/attachments/".$folder.'/';
        $headers = [
            'accept: application/json',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD =>$username . ':' .$password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }
        curl_close($curl);
        
        return $response;
       
    }

    public static  function getSkillCategory($employeeId,$username,$password){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/employees/skill_categories";
        $headers = [
            'accept: application/json',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD =>$username.':'.$password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }
        curl_close($curl);
        
        return $response;
    }
    public static function getAllAlayaCareSkillByAgencyDetails($page,$username,$password){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/employees/skills?page=".$page."&count=100";
        $headers = [
            'accept: application/json',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD =>$username.':'.$password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }
        curl_close($curl);
        $response = json_decode($response,true);
        
        return $response;
    }

    public static function getEmployeeSkillDetails($skillId,$employeeId,$agencyId)
    {

        $agencyDetails = self::getAgencyDetailsWithId($agencyId);
       
        $link = $agencyDetails->alayacare_url ."/employees/employees/".$employeeId."/skills/".$skillId;
        $curl = curl_init();
        

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
       
        return $response;
    }

    public static function deleteSkill($empId,$username,$password,$data){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
        $link = $agencyDetails->alayacare_url ."/employees/employees/".$empId."/skills/".$data['skill_id'];
        $headers = [
            'accept: application/json',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD =>$username.':'.$password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
          
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }
        curl_close($curl);
        return $response;
    }

    public static function editSkill($empId,$username,$password,$skillId){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        
      $link = $agencyDetails->alayacare_url ."/employees/employees/".$empId."/skills/".$skillId;
       
        $headers = [
            'accept: application/json',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD =>$username.':'.$password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          
            CURLOPT_HTTPHEADER => $headers,
        ));
   
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }
      
        curl_close($curl);
   
        return $response;
    }
    
    public static function updateEmployeeSkill($employeeId,$agencyId,$data){
        
         $agencyDetails = self::getAgencyDetailsWithId($agencyId);
        
        $link = $agencyDetails->alayacare_url ."/employees/employees/".$employeeId."/skills/".$data['skill_id'];

        $data = array(
            "comment" => $data['content']??"",
            'fields'=>$data['fields']
        );

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }
        curl_close($curl);

        return $response;
    }
    
    public static function uploadDocumentSection($employeeId,$agencyId,$data){
        
        $agencyDetails = self::getAgencyDetailsWithId($agencyId);

        $username = $agencyDetails->alaycare_username;
        $password = $agencyDetails->alaycare_password;

        $createDirectory = self::createDirectory($employeeId,$username,$password,$data['folder']);
        $response = json_decode($createDirectory,true);
        
        if(isset($response['code']) && ($response['code'] =='201' || $response['code'] =='409')){
        
            $link = $agencyDetails->alayacare_url ."/employees/employees/".$employeeId."/attachments/".$data['folder']."/".$data['file_path'];
            
            if(env('FILE_UPLOAD_PERMISSION') =='development'){
                $fileNames = public_path('/patientdocument').'/'.$data['file_path'];
                $mimeType = mime_content_type($fileNames);
            }else{
                $file = Storage::disk('s3')->temporaryUrl(
                   '/patientdocument/'.$data['file_path'],
                    now()->addMinutes(5)
                );
                $fileNames = $file;
                $mimeType = Storage::disk('s3')->mimeType('/patientdocument/' . $data['file_path']);
            }
            
          
            $headers = [
              'accept: application/json'];
              
              $curl = curl_init();
              curl_setopt_array($curl, array(
                  CURLOPT_URL => $link,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_USERPWD =>$username.':'.$password,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => array(
                      
                      "file" => new \CURLFile($fileNames, $mimeType, basename($data['file_path'])),
                  ),
                  CURLOPT_HTTPHEADER => $headers,
              ));
      
              $response = curl_exec($curl);
              if (curl_errno($curl)) {
                  echo 'Error: ' . curl_error($curl);
              }

              curl_close($curl);
             
              return $response;
        }else{

        }
    }

    public static function downloadAttachmentFiles($employeeId,$username,$password,$data){
        
        $agencyDetails = self::getAgencyDetails($username,$password);
        $data['alaya_document'] = str_replace($employeeId.'/','',$data['alaya_document']);
     $link = $agencyDetails->alayacare_url ."/employees/employees/".$employeeId."/attachments/".$data['alaya_document'];

        $headers = [
          'accept: application/json'];
          
          $curl = curl_init();
          curl_setopt_array($curl, array(
              CURLOPT_URL => $link,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_USERPWD =>$username.':'.$password,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              
              CURLOPT_HTTPHEADER => $headers,
          ));
  
          $response = curl_exec($curl);
          if (curl_errno($curl)) {
              echo 'Error: ' . curl_error($curl);
          }
          curl_close($curl);

          return $response;
    }

    public static function getAgencyDetails($username,$password){
        $agencyDetails = Agency::where('alaycare_username',$username)->where('alaycare_password',$password)->where('delete_flag','N')->first();
        $defultLink = env('ALAYACARE_LINK');
        if(isset($agencyDetails->alayacare_url) && $agencyDetails->alayacare_url != ""){
            $defultLink = $agencyDetails->alayacare_url;
        }
        $agencyDetails->alayacare_url = $defultLink .'/ext/api/v2';
        return $agencyDetails;
    }


    public static function getEmployeeRecordTemp($username,$password,$page,$branch_id)
    {
    

    $agencyDetails = self::getAgencyDetails($username,$password);
     
     $link = $agencyDetails->alayacare_url . '/employees/employees?page='.$page.'&count=100';
     
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
        return $response;
    }

    public static function loadSkills($empId,$username,$password){
        $agencyDetails = self::getAgencyDetails($username,$password);
      
        $link = $agencyDetails->alayacare_url ."/employees/employees/".$empId."/skills";

        $headers = [
            'accept: application/json',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD =>$username.':'.$password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }
        curl_close($curl);
       
      
        return $response;
    }

    public static function searchClient($username,$password,$search){
        $agencyDetails = self::getAgencyDetails($username,$password);
       
    $link = $agencyDetails->alayacare_url . '/patients/clients?filter='.$search;
     
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ':' . $password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',

        ));

        $response = curl_exec($curl); 
        
        curl_close($curl);
    
        return  $response;
    }

    public static function searchEmployee($username,$password,$search){
        $agencyDetails = self::getAgencyDetails($username,$password);
       
        $link = $agencyDetails->alayacare_url . '/employees/employees?filter='.$search;
         
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => $link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => $username . ':' . $password,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
    
            ));
    
            $response = curl_exec($curl); 
            
            curl_close($curl);
        
            return  $response;
    }

    public static function getAgencyDetailsWithId($id){
        $agencyDetails = Agency::where('id',$id)->where('delete_flag','N')->where('alaycare_status',1)->first();
        $defultLink = env('ALAYACARE_LINK');
        if(isset($agencyDetails->alayacare_url) && $agencyDetails->alayacare_url != ""){
            $defultLink = $agencyDetails->alayacare_url;
        }
        $agencyDetails->alayacare_url = $defultLink .'/ext/api/v2';
        return $agencyDetails;
    }

    public static function getEmployeeSkillDetailsCron($skillId,$employeeId,$agencyId)
    {
        
        $agencyDetails = self::getAgencyDetailsWithId($agencyId);
       
        $link = $agencyDetails->alayacare_url ."/employees/employees/".$employeeId."/skills/".$skillId;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $agencyDetails->alaycare_username . ':' . $agencyDetails->alaycare_password,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
      
        curl_close($curl);
 
        return $response;
    }
}