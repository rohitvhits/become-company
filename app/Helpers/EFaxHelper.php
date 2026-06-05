<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;

class EFaxHelper
{
    public static function sendEFex($data){
       
        $b64Doc = base64_encode(file_get_contents($data['file_path']));
        $op =array();
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.notifyre.com/fax/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "Faxes": {
              "Recipients": [
                {
                  "Type": "fax_number",
                  "Value": "+1'.$data['fax_no'].'"
                }
              ],
              "SendFrom": "",
              "ClientReference": "32188396-f59b-4581-a2e7-da4515f7d292",
              "Subject": "'.$data['subject'].'",
              "IsHighQuality": false,
              "Documents": [
                {
                  "Filename": "document.pdf",
                  "Data": "'.$b64Doc.'"
                }
              ]
            }
          }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'x-api-token: IoHZML2MQEL6Oo8UkmTbQG9h/JDA2Ca7pSz4fQKAJ+4.us'
        ),        
        ));
       $response = curl_exec($curl);
        curl_close($curl);
       
     
        return json_decode($response,true);
    }

    public static function sendEFex22($data){
       
      $b64Doc = base64_encode(file_get_contents($data['file_path']));
      $op =array();
     
      $curl = curl_init();
      
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.securedocex.com/faxes',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
          "destinations": [
              {
                  "fax_number" :"'.$data['fax_no'].'",
                  "to_name" : "'.$data['to_name'].'",
                  "to_company": "'.$data['to_company'].'"
              }
          ],
          "documents": [
              {
                  "document_content": "'.$b64Doc.'",
                  "document_type": "PDF"
              }
          ],
         "fax_options" : {
              "image_resolution" : "FINE",
              "include_cover_page" : "true"
         }
      }',
        CURLOPT_HTTPHEADER => array(
          'user-id: '.env('FAX_USER_ID'),
          'Content-Type: application/json',
          'Authorization: Bearer '.env('FAX_TOKEN')
        ),
      ));
      
      $response = curl_exec($curl);
      
      curl_close($curl);
      return json_decode($response,true);
  }

}
