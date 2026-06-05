<?php

namespace App\Services;

use App\Model\VnsEsignFormData;

class VnsEsignFormDataService
{
    protected const DATE_FORMAT_YMD = 'Y-m-d H:i:s';

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date(self::DATE_FORMAT_YMD);
        $data['created_by'] = $auth['id'];
        $insert = new VnsEsignFormData($data);
        $insert->save();
        return $insert->id;
    }

    public function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date(self::DATE_FORMAT_YMD);
        if (isset($auth['id'])) {
            $data['updated_by'] = $auth['id'];
        }
        return VnsEsignFormData::where($where)->update($data);
    }

    public function softDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date(self::DATE_FORMAT_YMD);
        $data['deleted_by'] = $auth['id'];

        return VnsEsignFormData::where($where)->update($data);
    }

	public function getDetailsByTemplateIDAndPatientId($tid,$pid){
		return VnsEsignFormData::select('id','pdf')->where('main_template_id',$tid)->where('patient_id',$pid)->orderBy('id','desc')->first();
	}
	
}