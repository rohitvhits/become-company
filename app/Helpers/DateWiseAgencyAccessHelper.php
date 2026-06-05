<?php

namespace App\Helpers;

use App\Model\DateWiseAgencyAccess;
use App\Model\DateWiseAgencyAccessDetail;


class DateWiseAgencyAccessHelper
{
    public function __construct()
    {
    }

    public static function getDateWiseAgencyAccess(){
        $authId = auth()->id();
        $today  = date('Y-m-d');
        $permanent = DateWiseAgencyAccess::select('id')->where('user_id',$authId)->where('del_flag','N')->where('permanent_access','=',1)->first();
        if($permanent){
            return DateWiseAgencyAccessDetail::where('date_view_agency_access_id', $permanent->id)->where('del_flag', 'N')->pluck('permission')->toArray();
        }
        $ids = DateWiseAgencyAccess::where('user_id', $authId)->where('del_flag', 'N')->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->pluck('id');
        if ($ids->isEmpty()) {
            return [];
        }
        // Fetch permissions for all the IDs
        return DateWiseAgencyAccessDetail::where('del_flag', 'N')->whereIn('date_view_agency_access_id', $ids)->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->pluck('permission')->toArray();
    }
}