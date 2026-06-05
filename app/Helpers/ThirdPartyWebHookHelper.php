<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\InflowcarePatientLogService;

class ThirdPartyWebHookHelper
{
	public function __construct() {}

	public static function sendWebHook($data){
        $data= json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://nursing.hhnsystem.com/md-orders-webhook-response',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
      
        return $response;
    }

    public static function sendRnPadWebHook($agencyId,$thirdPartyWebHookUrl,$attachmentFile,$attachmentName){
      $query = DB::table('rnpad_token')->where('agency_id',$agencyId)->first();
    
      $curl = curl_init();
      
      curl_setopt_array($curl, array(
        CURLOPT_URL => $thirdPartyWebHookUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('Attachment'=> new \CURLFILE($attachmentFile,'application/pdf',$attachmentName)),
        CURLOPT_HTTPHEADER => array(
          'X-Auth-Token: '.$query->token,
         
        ),
      ));
      
      $response = curl_exec($curl);
      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);
      $statusMessage = self::getStatusCodeWiseMessage($httpCode);
      
      return ['status'=>$httpCode,'error_msg'=>$statusMessage,'data'=>json_decode($response,true)];
  }

  public static function getStatusCodeWiseMessage($httpCode){
    $statusMessages = [
      200 => 'Document successfully sent to Rnpad',
      201 => 'Document successfully sent to Rnpad',
      204 => 'Document successfully sent to Rnpad',
      400 => 'The request was invalid or cannot be served.',
      401 => 'Authentication failed or token is invalid.',
      403 => 'You don’t have permission to access this resource.',
      404 => 'The requested URL or resource could not be found.',
      408 => 'The server timed out waiting for the request.',
      415 => 'Unsupported Media Type — The request format is not supported.',
      422 => 'Unprocessable Entity — Validation error in input data.',
      429 => 'Too Many Requests — Rate limit exceeded.',
      500 => 'Sorry, something went wrong. Please try again.',
      502 => 'Bad Gateway — Invalid response from upstream server.',
      503 => 'Service Unavailable — The server is temporarily down or overloaded.',
      504 => 'Gateway Timeout — The server didn’t respond in time.',
    ];
    return $statusMessages[$httpCode] ?? 'Unknown Status — Unexpected response code.';
  }

  public static function sendTaskHelathWebhook($task_id,$thirdPartyWebHookUrl,$attachmentFile,$status){
      $token = env('TASK_HEALTH_TOKEN');
      $curl = curl_init();
      $payload = json_encode([
          'task_id'  => (string) $task_id,
          'status'          => $status,
          'signed_document' => base64_encode($attachmentFile),
      ]);
      curl_setopt_array($curl, array(
        CURLOPT_URL => $thirdPartyWebHookUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        ],
      ));
      
      $response = curl_exec($curl);
      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);
      $statusMessage = self::getStatusCodeWiseMessageTaskHealth($httpCode);
      
      return ['status'=>$httpCode,'error_msg'=>$statusMessage,'data'=>json_decode($response,true)];
  }

  public static function getStatusCodeWiseMessageTaskHealth($httpCode){
    $statusMessages = [
      200 => 'Document successfully sent to Task Health',
      201 => 'Document successfully sent to Task Health',
      204 => 'Document successfully sent to Task Health',
      400 => 'The request was invalid or cannot be served.',
      401 => 'Authentication failed or token is invalid.',
      403 => 'You do not have permission to access this resource.',
      404 => 'The requested URL or resource could not be found.',
      408 => 'The server timed out waiting for the request.',
      415 => 'Unsupported Media Type — The request format is not supported.',
      422 => 'Unprocessable Entity — Validation error in input data.',
      429 => 'Too Many Requests — Rate limit exceeded.',
      500 => 'Sorry, something went wrong. Please try again.',
      502 => 'Bad Gateway — Invalid response from upstream server.',
      503 => 'Service Unavailable — The server is temporarily down or overloaded.',
      504 => 'Gateway Timeout — The server did not respond in time.',
    ];
    return $statusMessages[$httpCode] ?? 'Unknown Status — Unexpected response code.';
  }

  public static function visitingAidReuploadDocument($data){
    $thirdPartyId = $data['third_party_id'] ?? null;
    if(!$thirdPartyId){
      return ['status' => 400, 'error_msg' => 'Third party ID is required', 'data' => null];
    }
    $thirdPartyRecord = DB::table('third_party_patient_master')
      ->where('id', $thirdPartyId)
      ->where('deleted_flag', 'N')
      ->first();

    if(!$thirdPartyRecord){
      return ['status' => 404, 'error_msg' => 'Third party record not found', 'data' => null];
    }

    $callbackUrl = 'https://medical.visitingaidapi.com/Employee.svc/REST/AppointmentReProcess';
    $postData = [
      'appointment_id' => $thirdPartyId,
    ];
    $headers = [
        'Content-Type: application/json',
        'AuthKey: ' . env('VISING_AID_TOKEN'),
        'AuthPWD: ' . env('VISING_AID_PWD'),
    ];
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $callbackUrl,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($postData),
      CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $statusMessage = self::getStatusCodeWiseMessageVisitingAid($httpCode);

    return ['status' => $httpCode, 'error_msg' => $statusMessage, 'data' => json_decode($response, true)];
  }

  public static function getStatusCodeWiseMessageVisitingAid($httpCode){
    $statusMessages = [
      101 => 'Invalid Authentication',
      102 => 'appointment_id required',
      200 => 'Document successfully sent to Visiting Aid',
      201 => 'Invalid appointment_id',
      204 => 'Document successfully sent to Visiting Aid',
      400 => 'The request was invalid or cannot be served.',
      401 => 'Authentication failed or token is invalid.',
      403 => 'You do not have permission to access this resource.',
      404 => 'The requested URL or resource could not be found.',
      408 => 'The server timed out waiting for the request.',
      415 => 'Unsupported Media Type — The request format is not supported.',
      422 => 'Unprocessable Entity — Validation error in input data.',
      429 => 'Too Many Requests — Rate limit exceeded.',
      500 => 'Sorry, something went wrong. Please try again.',
      502 => 'Bad Gateway — Invalid response from upstream server.',
      503 => 'Service Unavailable — The server is temporarily down or overloaded.',
      504 => 'Gateway Timeout — The server did not respond in time.',
    ];
    return $statusMessages[$httpCode] ?? 'Unknown Status — Unexpected response code.';
  }

  public static function sendToInflowcareWebHook($data)
{
    try {
        $response = Http::withHeaders([
            'x-ext-service' => env('INFLOWCARE_X_EXT_SERVICE'),
            'Authorization' => env('INFLOWCARE_AUTHORIZATION'),
        ])->post(env('INFLOWCARE_WEBHOOK'), $data);

        $statusCode = $response->status();
        $json = $response->json(); // parsed response (if JSON)

        $logServices = new InflowcarePatientLogService();

        $final = [
            'patient_id'       => $data['appointment_id'] ?? null,
            'request_payload'  => serialize($data),
            'response_payload' => serialize(json_decode($json)), // already JSON string
            'status_code'      => $statusCode,
            'status'           => $response->successful() ? 'success' : 'fail',
            'message'          => $json['message'] ?? null,
        ];

        $logServices->save($final);

        return ['statusCode'=>$statusCode,'response'=>json_decode($json)];

    } catch (\Exception $e) {

        // Log failure
        $logServices = new InflowcarePatientLogService();

        $logServices->save([
            'patient_id'       => $data['appointment_id'] ?? null,
            'request_payload'  => serialize($data),
            'response_payload' => serialize(json_decode($json)),
            'status_code'      => 500,
            'status'           => 'error',
            'message'          => $e->getMessage(),
        ]);

        return false;
    }
}
}
