<?php
namespace App\Services;

use App\SiteSetting;

class SiteSettingServices{

	public  function saveOrUpdate($data){ 
		$auth = auth()->user();
        if(isset($data['document_dashboard_status']) && !empty($data['document_dashboard_status']))
        {
            $data['document_dashboard_status'] = implode(',',$data['document_dashboard_status']);
        }else{
            $data['document_dashboard_status'] = NULL;
        }
        if(isset($data['agency_notification_extra_users']) && !empty($data['agency_notification_extra_users']))
        {
            $data['agency_notification_extra_users'] = implode(',',$data['agency_notification_extra_users']);
        }else{
            $data['agency_notification_extra_users'] = NULL;
        }
        if(isset($data['id']) && $data['id'] !=""){
            $data['updated_date'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $auth->id;
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['created_by'] = $auth->id;
        }

        return SiteSetting::updateOrCreate(['id'=>$data['id']],$data);
	}

    public function getDetails(){
        return SiteSetting::where('del_flag','N')->first();
    }

    public function update($data){
        return SiteSetting::where('del_flag', 'N')->update($data);
    }
}