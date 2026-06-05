<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AgencyWiseSms extends Model
{
    protected $table = "agency_wise_sms_list";
    protected $guarded = ["id"];

    public static function SmsListByAgencyId($agencyId){
        return AgencyWiseSms::where('agency_id',$agencyId)->where('delete_flag','N')->orderBy('id', 'desc')->paginate(50);
    }

    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        
        $data['deleted_by'] = $auth['id'];

        $update = AgencyWiseSms::where($where)->update($data);
        return $update;
    }
}
