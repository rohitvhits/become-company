<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;


use App\Agency;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Services\BulkSMSCdpapCaregiverService;
use App\Model\BulkSMSCdpapCaregiverDetail;
use App\Services\LogsService;
use App\Services\BulkSMSCdpapCaregiverDetailService;
use App\Helpers\Utility;
use App\Services\SmsService;
use App\Helpers\Common;
use App\Model\BulkSMSCdpapLog;
use Illuminate\Http\Request;
class CronjobBulkSMSCdpapController extends BaseController
{
    
    protected $bulkSMSCdpapCaregiverService;
    protected $bulkSMSCdpapCaregiverDetailService;
    protected const VALIDATION_CODE=422;
    protected const ERROR_CODE = 500;
    protected const SUCCESS_CODE = 200;
    protected $smsService;
    protected const DATE_FORMATYYYY="Y-m-d H:i:s";
    public function __construct(BulkSMSCdpapCaregiverService $bulkSMSCdpapCaregiverService, BulkSMSCdpapCaregiverDetailService $bulkSMSCdpapCaregiverDetailService,SmsService $smsService)
    {
       $this->bulkSMSCdpapCaregiverService = $bulkSMSCdpapCaregiverService;
        $this->bulkSMSCdpapCaregiverDetailService = $bulkSMSCdpapCaregiverDetailService;
        $this->smsService = $smsService;
    }

    public function index()
    {
        $getDetails = $this->bulkSMSCdpapCaregiverService->getSMSDetails();

        if (empty($getDetails?->id)) {
            return;
        }

        $query = $this->bulkSMSCdpapCaregiverDetailService->getAllSMS($getDetails->id);

        if (!empty($query[0])) {
            foreach ($query as $val) {
                $updateData = $this->prepareSmsUpdateData($val, $getDetails->message);
                $this->bulkSMSCdpapCaregiverDetailService->update($updateData, ['id' => $val->id]);
            }
        }

        $checkTotalPendingSMS = $this->bulkSMSCdpapCaregiverDetailService
            ->checkTotalPendingSMSByID($getDetails->id);
        if (count($checkTotalPendingSMS) === 0) {
            $this->bulkSMSCdpapCaregiverService
                ->update(['status' => 'Completed'], ['id' => $getDetails->id]);
        }
    }

    private function prepareSmsUpdateData($val, $message)
    {
        $updateData = ['sms_status' => 'Sent'];

        foreach (['mobile'] as $type) {
            if (!empty($val->$type)) {
                $val->$type = str_replace(['(', ')', '-', ' '], '', $val->$type);
                $checkLog = BulkSMSCdpapLog::where('bulk_cdpap_id',$val->bulk_sms_cdpap_caregiver_id)->where('mobile_no',$val->$type)->first();
                if(!isset($checkLog->id)){
                    $saveData = new BulkSMSCdpapLog(array('patient_id'=>$val->patient_id,'bulk_cdpap_id'=>$val->bulk_sms_cdpap_caregiver_id,'bulk_cdpap_detail_id'=>$val->id,'mobile_no'=>$val->$type,'sms'=>$message,'created_date'=>date(self::DATE_FORMATYYYY)));
                    $saveData->save();
                    $saveLastId = $saveData->id;
                    $smsDetails = $this->smsService->agencyWiseSmsDynamicCronjob(
                        $val->patient_id,
                        $val->$type,
                        $message,
                        $saveLastId
                    );
                    $sms_id = $smsDetails['sid'] ?? '';
                    $updateData["{$type}_sms_id"] = $sms_id;
                }
            }
        }

        return $updateData;
    }

    public function updateSMSStatusByMobileOrPhone()
    {
        $query = $this->bulkSMSCdpapCaregiverDetailService->getSendSMSList();

        if (empty($query[0])) {
            return;
        }

        foreach ($query as $val) {
            foreach (['mobile'] as $field) {
                $smsIdField = "{$field}_sms_id";
                if (!empty($val->$smsIdField)) {
                    $this->updateSingleSMSStatus($val->id, $val->$smsIdField, $field);
                }
            }
        }
    }

    private function updateSingleSMSStatus($recordId, $smsId, $field)
    {

        $subquery = Common::fetchSingleMessage($smsId);
        $json = json_decode($subquery, true);
      
        if (!$json || empty($json['status']) || empty($json['date_updated'])) {
            return; // optional safeguard
        }

        $updateData = [
            "{$field}_deliver_sms_status" => $json['status'],
            "{$field}_status_updated_date" => date('Y-m-d H:i:s', strtotime($json['date_updated'])),
        ];

        $this->bulkSMSCdpapCaregiverDetailService->update($updateData, ['id' => $recordId]);
    }

    public function smsCallBack(Request $request){
        BulkSMSCdpapLog::where('id',$request->id)->update(array('data'=> json_encode($request->all()),'sms_id'=>$request->SmsSid,'updated_date'=>date(self::DATE_FORMATYYYY)));
        $getLogDetails = BulkSMSCdpapLog::where('id',$request->id)->where('del_flag','N')->first();
        $this->updateSingleSMSStatus($getLogDetails->bulk_cdpap_detail_id,$request->SmsSid,'mobile');
    }

    public function getALLSMSData(){
        $getDetails = $this->bulkSMSCdpapCaregiverService->getSMSDetails();
        $query = $this->bulkSMSCdpapCaregiverDetailService->getAllSMS($getDetails->id);
        
        return response()->json(['status' => 1, 'data' => $query, 'error_msg' => 'Pending data fetched successfully'], 200);
    }

    public function getCustomSendData(Request $request){
       
        $getDetails = $this->bulkSMSCdpapCaregiverService->getDetailsById($request->id);
        $query = $this->bulkSMSCdpapCaregiverDetailService->getDetailsByIdPending($request->detail_id);
       
        if(isset($query->id)){
            $updateData = $this->prepareSmsUpdateData($query, $getDetails->message);
            $this->bulkSMSCdpapCaregiverDetailService->update($updateData, ['id' => $query->id]);
        }
        return response()->json(['status' => 1, 'data' =>[], 'error_msg' => 'Sync successfully updated'], 200);
    }
}