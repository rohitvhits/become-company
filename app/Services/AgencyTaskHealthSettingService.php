<?php

namespace App\Services;

use App\Model\AgencyTaskHealthSetting;

class AgencyTaskHealthSettingService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";

        $insert = new AgencyTaskHealthSetting($data);
        $insert->save();
        return $insert->id;
    }

    public function update($data,$where){
        $auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = AgencyTaskHealthSetting::where($where)->update($data);
		return $update;
    }

    public function getByAgencyId($agency_id){
        return AgencyTaskHealthSetting::select('hha_link','send_poc','send_to_supervision','kardex','assessment','upload_hha_cms_mdo_485','upload_hha_patient_package_doc','upload_document_cron')->where('agency_id',$agency_id)->where('del_flag','N')->first();
    }

    public function updateAgencySetting($agencyId, $field, $value)
    {
        $setting = AgencyTaskHealthSetting::firstOrNew(['agency_id' => $agencyId]);
        // Set dynamic field
        $setting->$field = $value;
        // Set common fields
        if (!$setting->exists) {
            $setting->del_flag   = 'N';
            $setting->created_by = auth()->id(); // or specific user id
        }else{
            $setting->updated_by = auth()->id();
        }
        $setting->save();
        return $setting;
    }

    public function getTaskHealthSettingById($agencyId)
    {
        return AgencyTaskHealthSetting::where('agency_id', $agencyId)->where('del_flag','N')->first();
    }
}