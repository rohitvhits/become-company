<?php
namespace App\Services;
use App\Model\AgencyWebHook;

class AgencyWebHookService{

	public  function save($data){
        return AgencyWebHook::updateOrCreate(['id'=>$data['id'],'agency_id'=>$data['agency_id']],$data);
	}

    public function list($agencyId){
        return AgencyWebHook::select('id','webhook_url','authentication_type','user_name','password','token','created_date','created_by','updated_date','updated_by')->with(['users:id,first_name,last_name','updatedUser:id,first_name,last_name'])->where('del_flag','N')->where('agency_id',$agencyId)->orderby('id','desc')->paginate(50);
    }

    public function detailById($id){
        return AgencyWebHook::select('id','webhook_url','authentication_type','user_name','password','token')->where('del_flag','N')->where('id',$id)->first();
    }

    public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =AgencyWebHook::where($where)->update($data); 
		return $update;
	}

    public static function getWebhookDataByAgencyId($agency_id){
        return AgencyWebHook::select('id','webhook_url','authentication_type','user_name','password','token')->where('del_flag','N')->where('agency_id',$agency_id)->orderby('id','desc')->get()->toArray();
    }
}