<?php

namespace App\Services;

use App\Model\DateWiseAgencyAccessDetail;
use App\Model\DateWiseAgencyAccess;
class DateWiseAgencyAccessDetailService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";
        
        $insert = new DateWiseAgencyAccessDetail($data);
        $insert->save();
        return $insert->id;
    }

    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        return DateWiseAgencyAccessDetail::where($where)->update($data);
    }

    public static function softDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		return DateWiseAgencyAccessDetail::where($where)->update($data);
	}


    public static function getDetailsByDateWiseAgencyPermission($id){
        return DateWiseAgencyAccessDetail::select('id','permission')->where('date_view_agency_access_id',$id)->where('del_flag','N')->get();
    }

    public function dateWiseUserDetails()
    {
        return $this->hasMany(DateWiseAgencyAccess::class, 'date_view_agency_access_id', 'id');
    }
}