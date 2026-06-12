<?php
namespace App\Services;

use App\Helpers\Common;
use App\Model\SMSLogs;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\Patient;
use App\ZipCode;

class SmsService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new SMSLogs($data);
		$insert->save();
		$insert_id =$insert->id;
		return $insert_id;
		
	}

	public function AgencyWiseSmsDynamic($patientId,$phoneNo,$message){
		$auth = auth()->user();
		$checkZipCode = $this->checkZipCode($patientId);
		$return = 0;
		if($checkZipCode == 1){
			$sendSms = Common::sendTwillioSms($phoneNo,$message);
			if($patientId && $sendSms){
				$json = json_decode($sendSms,true);
				$date_updated = null;
				$status = $json['status']??"";
				if(isset($json['date_updated'])){
					$date_updated = date('Y-m-d H:i:s',strtotime($json['date_updated']));
				}
				if (!array_key_exists('sid', $json)) {
					$status = 'Undelivered ' . ($json['type'] ?? '');
				}
				$smsLogs = [ 
					'patient_id' => $patientId,
					'mobile_no' => $phoneNo,
					'sms' => $message,
					'send_sms_id' => $json['sid']??"",
					'send_sms_status' => $status??"",
					'send_sms_updated_date' =>$date_updated
				];
				if(isset($auth['id'])){
					$smsLogs['created_by'] = $auth['id'];
				}else{
					$smsLogs['created_by'] = '482';
				}
				$insert = new SMSLogs($smsLogs);
				$insert->save();
				$return =$insert->id;
			}
		}
		return $return;

	}
	
	public function agencyWiseSmsDynamicCronjob($patientId,$phoneNo,$message,$saveLastId){
	
		$sendSms = Common::sendTwillioSmsBulk($phoneNo,$message,$saveLastId);
		$return = 0;
		if($patientId && $sendSms){
			$return = json_decode($sendSms,true);
			
		}

		return $return;

	}

	public static function checkZipCode($p_id)
	{
		$patientData = Patient::select('zip_code','type')
			->where('id', $p_id)
			->where('deleted_flag', 'N')
			->first();
		if (empty($patientData)) {
			return '0';
		} 
		else if (!empty($patientData->zip_code)) {
			if(strtolower($patientData->type) == 'patient'){
				return '1';
			}
			$zipExists = ZipCode::where('sms_status', '1')
				->where('zip_code', $patientData->zip_code)
				->where('deleted_flag','N')
				->exists(); // optimized from get()
			return $zipExists ? '1' : '0';
		} 
		else {
			return '1';
		}
	}

	public function bulkEsignSmsDynamic($patientId,$phoneNo,$message){
		$auth = auth()->user();
		$checkZipCode = $this->checkZipCode($patientId);
		$smsId = 0;
		$status = "";
		$date_updated="";
		if($checkZipCode == 1){
			$sendSms = Common::sendTwillioSms($phoneNo,$message);
			if($patientId && $sendSms){
				$json = json_decode($sendSms,true);
				$date_updated = null;
				$status = $json['status']??"";
				if(isset($json['date_updated'])){
					$date_updated = date('Y-m-d H:i:s',strtotime($json['date_updated']));
				}
				if (!array_key_exists('sid', $json)) {
					$status = 'Undelivered ' . ($json['type'] ?? '');
				}
				$smsLogs = [
					'patient_id' => $patientId,
					'mobile_no' => $phoneNo,
					'sms' => $message,
					'send_sms_id' => $json['sid']??"",
					'send_sms_status' => $status??"",
					'send_sms_updated_date' =>$date_updated
				];
				if(isset($auth['id'])){
					$smsLogs['created_by'] = $auth['id'];
				}
				$insert = new SMSLogs($smsLogs);
				$insert->save();
				$smsId =$json['sid']??"";

			}
		}
		return ['smsId'=>$smsId,'status'=>$status,'date_updated'=>$date_updated];

	}
}