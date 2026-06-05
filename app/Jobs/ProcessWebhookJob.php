<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AgencyWebHookService;

class ProcessWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data = "";

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      
        $agency_id = $this->data['agency_id'];
      
        $appointmentId = $this->data['appointment_id'];
        $WebhookData = AgencyWebHookService::getWebhookDataByAgencyId($agency_id);
       
        foreach($WebhookData as $data){
            $infoData[] = $this->curlCall($data,$appointmentId);
        }
    }

    public function curlCall($data,$appointmentId){
        $headers = [];
            if ($data['authentication_type'] == 'api_key') {
                $headers[] = 'AuthKey:'.$data['user_name'];
                $headers[] = 'AuthPWD:'.$data['password'];
                $headers[] = 'Content-Type: application/json';
               
            }elseif($data['authentication_type'] == 'basic_auth'){
                $basic_auth_token = base64_encode($data['user_name'].':'.$data['password']);
                $headers[] = 'Authorization: Basic '.$basic_auth_token;
            }elseif($data['authentication_type'] == 'no_auth'){
                $headers = [];
            }elseif($data['authentication_type'] == 'bearer_token'){
                $headers[] = 'Authorization: Bearer '.$data['token'];
            }
            $postdata = [
                'appointment_id'=>$appointmentId
            ];
         info($headers);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $data['webhook_url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0, // Disable host verification
                CURLOPT_SSL_VERIFYPEER => false, // Disable peer verification
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => json_encode($postdata), // Encode data as JSON
            ));
            
          
            $response = curl_exec($curl);

          
            if (curl_errno($curl)) {
                \Log::info('cURL Error: ' . print_r(curl_error($curl),true));
            }
            \Log::info('headers: ' . print_r($headers,true));
            \Log::info('url: ' . print_r($data['webhook_url'],true));
            \Log::info('Res: ' . print_r($response,true));
            return $response;
    }
}
