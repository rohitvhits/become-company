<?php

namespace App\Services;

use App\Model\BulkSMSCdpapCaregiverDetail;

class BulkSMSCdpapCaregiverDetailService
{
    protected const YMD_DATE_FORMAT = "Y-m-d H:i:s";

    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date(self::YMD_DATE_FORMAT);
        if (isset($auth['id'])) {
            $data['updated_by'] = $auth['id'];
        }
        return BulkSMSCdpapCaregiverDetail::where($where)->update($data);
    }

    public static function getList($id)
    {
        return BulkSMSCdpapCaregiverDetail::select('id',
            'patient_id',
            'mobile',
            'phone',
            'sms_status',
            'created_date'
        )
            ->where('del_flag', 'N')
            ->where('bulk_sms_cdpap_caregiver_id', $id)
            ->paginate(50);
    }

    public static function getAllSMS($id)
    {
        return BulkSMSCdpapCaregiverDetail::select(
            'id',
            'bulk_sms_cdpap_caregiver_id',
            'mobile',
            'phone',
            'sms_status',
            'patient_id'
        )
            ->where('del_flag', 'N')
            ->where('sms_status', 'Pending')
            ->where('bulk_sms_cdpap_caregiver_id', $id)
            ->limit(500)
            ->get();
    }

    public static function checkTotalPendingSMSByID($id)
    {
        return BulkSMSCdpapCaregiverDetail::select(
            'id'
        )
            ->where('del_flag', 'N')
            ->where('bulk_sms_cdpap_caregiver_id', $id)
            ->where('sms_status', 'Pending')
            ->get();
    }

	public static function getSendSMSList(){
		return BulkSMSCdpapCaregiverDetail::select(
            'id',
			'mobile_sms_id',
			'phone_sms_id'
        )
            ->where('del_flag', 'N')
            ->where('sms_status', 'Sent')
			->limit(500)
            ->get();
	}

    public static function getDetailsById($id){
		return BulkSMSCdpapCaregiverDetail::select(
           'id',
            'bulk_sms_cdpap_caregiver_id',
            'mobile',
            'phone',
            'sms_status',
            'patient_id'
        )
            ->where('del_flag', 'N')
            ->where('id', $id)
			
            ->first();
	}

    public static function getDetailsByIdPending($id){
		return BulkSMSCdpapCaregiverDetail::select(
           'id',
            'bulk_sms_cdpap_caregiver_id',
            'mobile',
            'phone',
            'sms_status',
            'patient_id'
        )
            ->where('del_flag', 'N')
            ->where('id', $id)
            ->where('sms_status','Pending')
            ->first();
	}
}
