<?php
namespace App\Services;

use App\Model\HHAMdoClientPatientLog;

class HHAMdoClientPatientLogService{
    
    public static function save($data){

        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth->id;
        $data['del_flag'] = "N";
        
        $insert = new HHAMdoClientPatientLog($data);
        $insert->save();
        return $insert->id;
        
    }

    public static function getMDOPatientList(){
        return HHAMdoClientPatientLog::where('del_flag','N')->pluck('patient_id','mdo_patient_id');
    }
}