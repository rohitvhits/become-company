<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Agency;
use App\Model\AgencyTeleService;
use App\Model\AssignNyBestUser;
use App\Model\Language;
use App\Model\PatientMobileVerificationLogs;
use App\Jobs\AgencyNotificationSendJob;
use App\SiteSetting;
use Exception;

class Common
{
  public function __construct()
  {
  }
  public static function getAgencyDetails()
  {
    $auth = auth()->user();

    return Agency::where("id", $auth['agency_fk'])->first();
  }


  public static function sendTextSMSold($mobile, $message)
  {


    return true;
  }

  public static function saveSMSImage($url)
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "authorization: Basic " . env('BANDWIDTH_MESSAGING_AUTH'),
        "cache-control: no-cache",
        "content-type: application/json",

      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return  "cURL Error #:" . $err;
    } else {
      $fileName = explode('/', $url);
      //      print_r($fileName);
      $fileNamePublic = $fileName[count($fileName) - 1];
      $fileNamePublic = 'uploadedfiles/files/' . $fileNamePublic;


      file_put_contents(public_path($fileNamePublic), $response);
      // return $response;
      return 'https://web..com/' . $fileNamePublic;
    }
  }

  public static function sendTextSMS($mobile, $message)
  {

    $payload = array(
      "to" => [$mobile],
      "from" => "+17186503540",
      "text" => $message,
      "applicationId" => "4fc53e78-78c0-4f9b-a831-6a4c15dc2ef4",

      "tag" => "test message"
    );


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://messaging.bandwidth.com/api/v2/users/5002369/messages",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        "authorization: Basic " . env('BANDWIDTH_MESSAGING_AUTH'),
        "cache-control: no-cache",
        "content-type: application/json",
        "postman-token: 19feffba-d3c7-62ca-aa74-bcc1225ec56a"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      $response;
    }

    return $response;
  }

  public static function sendTextSMSNYBest($mobile, $message)
  {

    $payload = array(
      "to" => [$mobile],
      "from" => "+19293794044",
      "text" => $message,
      "applicationId" => "4fc53e78-78c0-4f9b-a831-6a4c15dc2ef4",

      "tag" => "sendTextSMSNYBest"
    );

    $payload = array(
      "to" => [$mobile],
      "from" => "+13479156689",
      "text" => $message,
      "applicationId" => "84c98ac2-1193-411e-885f-f5aa85712789",

      "tag" => "sendTextSMSNYBest"
    );


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://messaging.bandwidth.com/api/v2/users/5002369/messages",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        "authorization: Basic " . env('BANDWIDTH_MESSAGING_AUTH'),
        "cache-control: no-cache",
        "content-type: application/json",
        "postman-token: 19feffba-d3c7-62ca-aa74-bcc1225ec56a"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    DB::table('sms_log_response')->insert(
      array('mobile' => $mobile, 'message' => $message, 'response' => $response, 'created_date' => date('Y-m-d H:i:s'))
    );

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      $response;
    }

    return $response;
  }

  public static function sendTwillioSms($mobile, $message)
  {
    $type = self::checkInDatabaseExistMobile($mobile);
   
    if(strtolower($type['type']) == 'mobile'){
      $payload = array(
        "To"=>$mobile,
        "From" => "+16092077517",
        "Body" => $message,

      );
      $username=env('TWILLIO_USERNAME');
      $password=env('TWILLIO_PASSWORD');
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACe0b0eff2b680ed9f631a0c093875dfbd/Messages.json',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_USERPWD=>"$username:$password",

        // CURLOPT_HTTPAUTH, CURLAUTH_ANY,
        CURLOPT_HTTPAUTH => CURLAUTH_ANY,
        CURLOPT_POSTFIELDS =>  http_build_query($payload),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/x-www-form-urlencoded',
        
        ),
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        $response;
      }
      return $response;
    }else{
      return json_encode($type);
    }
  }

  public static function checkAgencyLogin()
  {
    $auth = auth()->user();
    if ($auth->login_type_fk == 2 && $auth->user_type_fk == 6) {
      return true;
    }
    return false;
  }

  public static function checkTeleAgencyService($patientServiceArray,$agency_id){
    $agencyServiceArray = AgencyTeleService::getAgencyServicesArray($agency_id);
    $is_send_sms = 0;
    foreach($agencyServiceArray as $agency){
      if(in_array($agency,$patientServiceArray)){
          $is_send_sms = 1;
      }
    }
    return $is_send_sms;
  } 

  public static function sendsmsAgencyTelehealth($getAppointSchedule,$unitId,$query,$getAgencyName,$patient_id){
    $start_time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";
    $end_time = ($getAppointSchedule->end_time) ? $getAppointSchedule->end_time : "00:00:00";
    $date = ($getAppointSchedule->date) ? Utility::convertMDY($getAppointSchedule->date) : "";

    $url = URL::to('/') . '/tele-appointment/' . $unitId;
    if (isset($query->language) && strtolower($query->language) == 'spanish') {
        if(isset($getAgencyName->tele_send_sms_spanish)){
            $messageTemplate = $getAgencyName->tele_send_sms_spanish;
            $message = str_replace(
                ['{{ patient_first_name }}', '{{ agency_name }}', '{{ start_date }}', '{{ start_time }}', '{{ end_time }}', '{{url}}'],
                [$query->first_name, $getAgencyName->agency_name, $date, date('h:i A', strtotime($start_time)), date('h:i A', strtotime($end_time)), $url],
                $messageTemplate
            );
        }else{
            $message = 'Aviso de ' . $getAgencyName->agency_name . ': Su cita está programada para el ' . $date . ' de ' . date('h:i A', strtotime($start_time)) . ' A ' . date('h:i A', strtotime($end_time)) . ' ' . $url . '.  No responda a este mensaje de texto y si usted tiene alguna pregunta, por favor llame al (718) 972-3693';
        }
    } else {
          if(isset($getAgencyName->tele_send_sms_eng)){
            $messageTemplate = $getAgencyName->tele_send_sms_eng;
            $message = str_replace(
                ['{{ patient_first_name }}', '{{ agency_name }}', '{{ start_date }}', '{{ start_time }}', '{{ end_time }}', '{{url}}'],
                [$query->first_name, $getAgencyName->agency_name, $date, date('h:i A', strtotime($start_time)), date('h:i A', strtotime($end_time)), $url],
                $messageTemplate
            );
        }else{
            $message = 'Notice from ' . $getAgencyName->agency_name . ': Your Appointment is scheduled for ' . $date . ' ' . date('h:i A', strtotime($start_time)) . ' to ' . date('h:i A', strtotime($end_time)) . ' ' . $url . '.  Do not reply to this text message for any questions please call (718) 972-3693';
        }
    }
    
    return $message;
  }

  public static function sendsmsAgencyTelehealthReminder($getAppointSchedule,$unitId,$query,$getAgencyName,$patient_id){
    $start_time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";
    $end_time = ($getAppointSchedule->end_time) ? $getAppointSchedule->end_time : "00:00:00";
    $date = ($getAppointSchedule->date) ? Utility::convertMDY($getAppointSchedule->date) : "";

    $url = URL::to('/') . '/tele-appointment/' . $unitId;
    if (isset($query->language) && strtolower($query->language) == 'spanish') {
        if(isset($getAgencyName->tele_remind_send_sms_spanish)){
            $messageTemplate = $getAgencyName->tele_remind_send_sms_spanish;
            $message = str_replace(
                ['{{ patient_first_name }}', '{{ agency_name }}', '{{ start_date }}', '{{ start_time }}', '{{ end_time }}', '{{url}}'],
                [$query->first_name, $getAgencyName->agency_name, $date, date('h:i A', strtotime($start_time)), date('h:i A', strtotime($end_time)), $url],
                $messageTemplate
            );
        }else{
            $message = 'Recuerde que su cita está programada para el ' . $date . ' ' . date('h:i A', strtotime($start_time)) . ' hasta el ' . date('h:i A', strtotime($end_time)) . ' Si necesita reprogramar su cita, haga clic aquí' . $url . '. No responda a este mensaje de texto. Si tiene alguna pregunta, llame al (718) 972-3693.';
        }
    } else {
          if(isset($getAgencyName->tele_remind_send_sms_spanish)){
            $messageTemplate = $getAgencyName->tele_remind_send_sms_spanish;
            $message = str_replace(
                ['{{ patient_first_name }}', '{{ agency_name }}', '{{ start_date }}', '{{ start_time }}', '{{ end_time }}', '{{url}}'],
                [$query->first_name, $getAgencyName->agency_name, $date, date('h:i A', strtotime($start_time)), date('h:i A', strtotime($end_time)), $url],
                $messageTemplate
            );
        }else{
            $message = 'Remember your appointment is scheduled for ' . $date . ' ' . date('h:i A', strtotime($start_time)) . ' to ' . date('h:i A', strtotime($end_time)) . ' If you need to reschedule click here ' . $url . '.  Do not reply to this text message for any questions please call (718) 972-3693';
        }
    }
    return $message;
  }

  public static function fetchSingleMessage($send_sms_id){
    $username=env('TWILLIO_USERNAME');
    $password=env('TWILLIO_PASSWORD');
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACe0b0eff2b680ed9f631a0c093875dfbd/Messages/'.$send_sms_id.'.json',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_USERPWD=>"$username:$password",

      CURLOPT_HTTPAUTH=>CURLAUTH_ANY,
  
    ));

    $response = curl_exec($curl);


    $err = curl_error($curl);
    curl_close($curl);
    return $response;
  }

  public static function normalizePhoneNumberdate($phone)
  {
      // Remove everything except digits
      $digits = preg_replace('/\D/', '', $phone);
      // Remove leading country code (1 or +1)
      if (strlen($digits) === 11 && $digits[0] === '1') {
          $digits = substr($digits, 1);
      }
      // Return less than 10-digit number or null if invalid
      return (strlen($digits) <= 10) ? $digits : null;
  }

  public static function getLiaisonData($agency_id){
      $data = AssignNyBestUser::getOnlyAssignNybestUserId($agency_id);
      return $data;
  }

  public static function insertAgencyNotificationsOfUser($data_array){
      $users  = self::getLiaisonData($data_array['agencyid'])->toArray();
      $setting = SiteSetting::where('del_flag', 'N')->first();
      $extraUsers = [];
      $users = is_array($users) ? $users : [$users];
      if ($setting && !empty($setting->agency_notification_extra_users)) {
          $extraUsers = array_filter(array_map('intval', explode(',', $setting->agency_notification_extra_users)));
      }
      $data_array['user'] = array_unique(array_merge($users, $extraUsers));
      $data_array['created_by'] = auth()->user()->id??'';
      AgencyNotificationSendJob::dispatch($data_array);
  }

  public static function sendTwillioSmsBulk($mobile, $message,$saveLastId)
  {

    $payload = array(
      "To"=>$mobile,
      "From" => "+17407933693",
      "Body" => $message,
      'StatusCallback'=>URL::to('/twillio-sms-status-callback').'?id='.$saveLastId
    );

    $username=env('TWILLIO_USERNAME');
    $password=env('TWILLIO_PASSWORD');
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACe0b0eff2b680ed9f631a0c093875dfbd/Messages.json',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_USERPWD=>"$username:$password",

      // CURLOPT_HTTPAUTH, CURLAUTH_ANY,
      CURLOPT_HTTPAUTH => CURLAUTH_ANY,
      CURLOPT_POSTFIELDS =>  http_build_query($payload),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
      
      ),
    ));

    $response = curl_exec($curl);


    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      $response;
    }

    return $response;
  }

  public static function getOrCreateLanguageId($languageName)
  {
      if (empty(trim((string) $languageName))) {
          return null;
      }
      try{
        $languages = Language::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower(trim($name)) => $id])->toArray();
        $search = strtolower(trim($languageName));
        return $languages[$search] ?? (is_numeric($languageName) ? $languageName : null);
      } catch (\Throwable $th) {
        \Log::error('Language lookup failed.', [
            'language' => $languageName,
            'error' => $th->getMessage(),
        ]);
        throw $th;
      }
  }

  public static function getServiceMesgdisable(){
    return ['849']; //Flu Vaccine - 849
  }

  public static function identifyMobileLandline($number)
  {
      if (empty($number)) {
          return [
              'error' => true,
              'message' => 'Phone number is required'
          ];
      }

      $username = env('TWILLIO_USERNAME');
      $password = env('TWILLIO_PASSWORD');

      if (!$username || !$password) {
          return [
              'error'   => true,
              'message' => 'Twilio credentials missing'
          ];
      }

      $authentication = base64_encode($username . ':' . $password);
      $url = "https://lookups.twilio.com/v1/PhoneNumbers/{$number}?Type=carrier";

      $curl = curl_init();
      curl_setopt_array($curl, [
          CURLOPT_URL            => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_TIMEOUT        => 10,
          CURLOPT_HTTPHEADER     => [
              'Authorization: Basic ' . $authentication
          ],
      ]);

      $response = curl_exec($curl);
      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      if ($response === false) {
          return [
              'error' => true,
              'message' => 'CURL Request Failed'
          ];
      }

      return [
          'error'      => false,
          'http_code'  => $httpCode,
          'response'   => json_decode($response)
      ];
  }


  public static function checkInDatabaseExistMobile($number)
  {
      if (empty($number)) {
          return ['type' => 'invalid number'];
      }

      // Check if exists in DB
      $record = PatientMobileVerificationLogs::where('del_flag', 'N')
                  ->where('number', $number)
                  ->first();

      if ($record) {
          return ['type' => ($record->type === 'mobile') ? 'mobile' : 'not_mobile'];
      }

      // Call Twilio
      $api = self::identifyMobileLandline($number);
     
      if ($api['error'] === true) {
          return ['type' => 'Api error'];
      }

      $res = $api['response'];

      // Twilio auth or error
      if (isset($res->status) && $res->status == 401) {
          return ['type' => 'Twilio auth error'];
      }

      if(!isset($res->carrier) || empty($res->carrier)){
        $type = 'unknown';
      }else{
        $type = $res->carrier->type ?? 'unknown';
      }

      // Save in DB
      PatientMobileVerificationLogs::insert([
          'number'     => $number,
          'type'       => $type,
          'response'   => json_encode($res),
          'created_at' => now(),
          'created_by' => auth()->check() ? auth()->id() : null,
      ]);

      // Return only 2 cases for SMS logic
      return ['type' => $type];
  }


}

