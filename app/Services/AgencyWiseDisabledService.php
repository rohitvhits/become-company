<?php
namespace App\Services;
use App\Model\AgencyWiseDisabledSMSService;

class AgencyWiseDisabledService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
	
		
		$insert = new AgencyWiseDisabledSMSService($data);
		$insert_id = $insert->save();
	
		return $insert_id;
		
	}
	

	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =AgencyWiseDisabledSMSService::where($where)->update($data); 
		return $update;
	}
	
    public  function softDelete($data,$where){
		$auth = auth()->user();
		AgencyWiseDisabledSMSService::where($where)->update(array('del_flag'=>'Y','deleted_by'=>$auth['id'])); 
		$update =AgencyWiseDisabledSMSService::where($where)->delete(); 
		return $update;
	}

	public function getAgencyWiseDisabledSMSServiceList($agencyId){
        return AgencyWiseDisabledSMSService::where('del_flag','N')->where('agency_id',$agencyId)->pluck('service_id');
    }

    public function getDetailsByServiceWithAgencyId($agencyId,$serviceID){
        return AgencyWiseDisabledSMSService::where('agency_id',$agencyId)->where('service_id',$serviceID)->first();
    }
}