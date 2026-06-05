<?php

namespace App\Helpers;

use App\Model\AgencyWiseNotifictionEmail;

class AgencyNotificationEmailHelper
{
    public function __construct()
    {
    }

    
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = AgencyWiseNotifictionEmail::where($where)->update($data);
        return $update;
    }
    public static function notificationEmailByAgencyId($agencyId){
        return AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->where('delete_flag','N')->orderBy('id', 'desc')->paginate(50);
    }

    public static function deleteNotification($agencyId){
        return AgencyWiseNotifictionEmail::where('agency_id',$agencyId)->delete();
    }

    public static function agencyById($check){
        return AgencyWiseNotifictionEmail::where($check)->where('delete_flag','N')->get();

    }

    public static function getDetailsById($id){
        return AgencyWiseNotifictionEmail::where('id',$id)->first();

    }
}
