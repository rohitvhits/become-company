<?php
namespace App\Services;
use App\Model\AgencyWiseSMSNotification;

class AgencyWiseSMSNotificationService
{

    public static function saveUpdateOrCreate($data)
    {
       $insert = AgencyWiseSMSNotification::updateOrCreate(
        ['agency_id'=>$data['agency_id']],
        $data
       );
       
        return $insert;
    }

    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = AgencyWiseSMSNotification::where($where)->update($data);
        return $update;
    }

    public static function getDetailsByAgencyId($id){
        return AgencyWiseSMSNotification::where('agency_id',$id)->first();
    }

    public static function getDetailsIdWithTypeAndFlag($id,$type,$notification){
        if($type =='Patient'){
            return AgencyWiseSMSNotification::where('agency_id',$id)->whereRaw("FIND_IN_SET(?, patient_sms_notification)", [$notification])->first();
        }else{
            return AgencyWiseSMSNotification::where('agency_id',$id)->whereRaw("FIND_IN_SET(?, caregiver_sms_notification)", [$notification])->first();
            
        }
        
    }
}
