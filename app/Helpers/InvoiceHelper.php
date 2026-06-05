<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Invoice;

use InvoiceNinja\Config as NinjaConfig;
  use InvoiceNinja\Models\Client;

use InvoiceNinja\Models\Statics;
use App\SiteSetting;
class InvoiceHelper
{
    public function __construct()
	{}
	
	 
	
    public static  function insert($data)
    {
		$insert_data = $data; 
		$inser_id = new Invoice($insert_data);
		$inser_id->save();
		$Insert = $inser_id->id; 

		return $Insert;
	
		
    }
	 public static  function update($data,$where)
    {	
		$insert = Invoice::where($where)->update($data);
		return $insert;
	
		
    }
    public static function markInvoiceAsSent($invoiceId){
    	  $data = json_encode([]);
        $getSiteSetting =SiteSetting::where('delete_flag','N')->first();
        NinjaConfig::setURL($getSiteSetting->url);
        $curl = curl_init();
         $url= $url = sprintf('%s/%s?action=%s', NinjaConfig::getURL().'/invoices', $invoiceId, 'mark_sent');
       // NinjaConfig::getToken();

        $type='PUT';

        $parsedUrl = parse_url($url);
        $separator = (!isset($parsedUrl['query']) || $parsedUrl['query'] == NULL) ? '?' : '&';

 //       $url .= $separator . http_build_query($options);


        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POST => $type === 'POST' ? 1 : 0,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_USERAGENT => 'Invoice Ninja - PHP SDK',
            CURLOPT_HTTPHEADER  => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                //'X-Ninja-Token: '. NinjaConfig::getToken(),
                'X-Ninja-Token: '. $getSiteSetting->token,
            ],
        ];

        curl_setopt_array($curl, $opts);
         $response = curl_exec($curl);
       

       
            $json = json_decode($response);
            if ($json && property_exists($json, 'data')) {
                return $json->data;
            } else {
                throw new Exception($response);
            }
      

    }
	
	public static function checkClientExists($id){
			
		  $data = json_encode([]);

        $curl = curl_init();
         $url= $url = sprintf('%s/%s?action=%s', NinjaConfig::getURL().'/clients', $id, 'find');
		 
       // NinjaConfig::getToken();

        $type='PUT';

        $parsedUrl = parse_url($url);
        $separator = (!isset($parsedUrl['query']) || $parsedUrl['query'] == NULL) ? '?' : '&';

 //       $url .= $separator . http_build_query($options);


        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POST => $type === 'POST' ? 1 : 0,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_USERAGENT => 'Invoice Ninja - PHP SDK',
            CURLOPT_HTTPHEADER  => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                'X-Ninja-Token: '. NinjaConfig::getToken(),
            ],
        ];

        curl_setopt_array($curl, $opts);
         $response = curl_exec($curl);
       

       
            $json = json_decode($response);
		
            if ($json && property_exists($json, 'data')) {
                return $json->data;
            } else {
                return 0;
            }
	}

	public static function markInvoiceAsPaid($invoiceId){
	
    	  $data = json_encode([]);
        $getSiteSetting =SiteSetting::where('delete_flag','N')->first();
        NinjaConfig::setURL($getSiteSetting->url);
        $curl = curl_init();
         $url= $url = sprintf('%s/%s?action=%s', NinjaConfig::getURL().'/invoices', $invoiceId,'markPaid');
       
	   // NinjaConfig::getToken();

        $type='PUT';

        $parsedUrl = parse_url($url);
		 $separator = (!isset($parsedUrl['query']) || $parsedUrl['query'] == NULL) ? '?' : '&';

 //       $url .= $separator . http_build_query($options);


        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POST => $type === 'POST' ? 1 : 0,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_USERAGENT => 'Invoice Ninja - PHP SDK',
            CURLOPT_HTTPHEADER  => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                //'X-Ninja-Token: '. NinjaConfig::getToken(),
                'X-Ninja-Token:'.$getSiteSetting->token,
            ],
        ];
        curl_setopt_array($curl, $opts);
         $response = curl_exec($curl);

       
            $json = json_decode($response);
            if ($json && property_exists($json, 'data')) {
                return $json->data;
            } else {
                throw new Exception($response);
            }
      

    }	
	
}