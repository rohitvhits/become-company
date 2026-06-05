<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;

class DocumentSendThirdPartyAPIHelper
{
    public static function sendThirdParty($data){
       
       $jsonData = array('id'=>$data['link_third_party'],'platform_id'=>"Test",'document_name'=>$data['document_name'],'document_completed_date'=>$data['document_completed_date'],'patient_code'=>$data['patient_code']);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apiv1.carespherehc.com/patient/referral',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('jsonData' => json_encode($jsonData),'fileName'=> new \CURLFILE($data['file_path'])),
        CURLOPT_HTTPHEADER => array(
            'x-api-key: '.env('Caresphere')
        ),
        ));
      
        $response = curl_exec($curl);

        curl_close($curl);
       
        return json_decode($response,true);
    }
}
