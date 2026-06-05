<?php

namespace App\Services;

use App\Model\BulkSMSCdpapCaregiver;

class BulkSMSCdpapCaregiverService
{
    protected const YMD_DATE_FORMAT = "Y-m-d H:i:s";

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date(self::YMD_DATE_FORMAT);
        $data['created_by'] = $auth['id'];
        $insert = new BulkSMSCdpapCaregiver($data);
        $insert->save();
        return $insert->id;
    }

    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date(self::YMD_DATE_FORMAT);
        $data['updated_by'] = $auth['id'];
        return BulkSMSCdpapCaregiver::where($where)->update($data);
    }

    public static function getList()
    {
        return BulkSMSCdpapCaregiver::select(
            'bulk_sms_cdpap_caregiver.id',
            'bulk_sms_cdpap_caregiver.message',
            'bulk_sms_cdpap_caregiver.created_date',
            'bulk_sms_cdpap_caregiver.created_by',
            'bulk_sms_cdpap_caregiver.status',
            'users.first_name',
            'users.last_name'
        )
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'bulk_sms_cdpap_caregiver.created_by');
                $join->where('users.delete_flag', 'N');
            })
            ->where('bulk_sms_cdpap_caregiver.del_flag', 'N')
            ->orderBy('bulk_sms_cdpap_caregiver.created_date', 'desc')
            ->paginate(50);
    }

    public static function getSMSDetails()
    {
        return BulkSMSCdpapCaregiver::select('id', 'message')
            ->where('status', 'Pending')
            ->where('del_flag', 'N')
            ->first();
    }

    public static function getDetailsById($id)
    {
        return BulkSMSCdpapCaregiver::select('id', 'message')
            ->where('status', 'Pending')
            ->where('del_flag', 'N')->where('id',$id)
            ->first();
    }
}
