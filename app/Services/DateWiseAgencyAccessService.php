<?php

namespace App\Services;

use App\Model\DateWiseAgencyAccess;

class DateWiseAgencyAccessService
{
    protected const DATE_FORMAT_YMD='Y-m-d H:i:s';
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date(self::DATE_FORMAT_YMD);
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";
        
        $insert = new DateWiseAgencyAccess($data);
        $insert->save();
        return $insert->id;
    }

    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date(self::DATE_FORMAT_YMD);
        $data['updated_by'] = $auth['id'];
        
        return DateWiseAgencyAccess::where($where)->update($data);
    }

    public static function softDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date(self::DATE_FORMAT_YMD);
        $data['deleted_by'] = $auth['id'];
        
        return DateWiseAgencyAccess::where($where)->update($data);
    }

    public static function getListByAgencyId($agencyId){
        return DateWiseAgencyAccess::select('id','created_date','created_by','updated_date','updated_by','type','start_date','end_date')
        ->with(['createdUserDetails:id,first_name,last_name','updatedUserDetails:id,first_name,last_name'])
        ->where('del_flag','N')
        ->where('agency_id',$agencyId)
        ->orderBy('id','desc')
        ->paginate(50);
    }

    public static function getDetailsByAgencyIDAndId($agencyId,$id){
        return DateWiseAgencyAccess::select('id','start_date','end_date')
        ->where('agency_id',$agencyId)
        ->where('id',$id)
        ->first();
    }

    public static function getDetailsById($id){
        return DateWiseAgencyAccess::select('id','start_date','end_date')->where('del_flag','N')
            ->with(['dateWiseUserDetails:id,date_view_agency_access_id,permission'])
            ->where('id',$id)
            ->first();
    }

    public static function getListByUserId($user_id)
    {
        return DateWiseAgencyAccess::select('id','created_date','created_by','updated_date','updated_by','type','start_date','end_date','permanent_access')
            ->with(['createdUserDetails:id,first_name,last_name','updatedUserDetails:id,first_name,last_name'])
            ->where('del_flag','N')
            ->where('user_id',$user_id)
            ->orderBy('id','desc')
            ->paginate(50);
    }

    public static function getPermanentUser($user_id)
    {
        return DateWiseAgencyAccess::select('id','created_date','created_by','updated_date','updated_by','type','start_date','end_date')
           ->where('user_id', $user_id)
            ->where('permanent_access', 1)
            ->where('del_flag', 'N')
            ->first();
    }

    public static function getUserPermanentRestrictions($user_id){
        return DateWiseAgencyAccess::select('id','created_date','created_by','updated_date','updated_by','type','start_date','end_date')
           ->where('user_id', $user_id)
           ->where('permanent_access', 1)
           ->where('del_flag', 'N')
           ->with('dateWiseUserDetails')
           ->get();
    }
}