<?php
namespace App\Services;

use App\Model\PocMatchedTask;

class MapTaskHealthService{

    public function getMapTaskListByPatientAndVisitId($pid,$vid){
        return PocMatchedTask::select('task_id','code','name')->where('del_flag','N')->where('patient_id',$pid)->where('th_task_id',$vid)->get();
    }

    public function getMapTaskListByWithCode($code){
        return PocMatchedTask::where('del_flag','N')->whereIn('hha_code',$code)->get();
    }

    public function getMapTaskListByWithCodeId($visit_task_id,$agencyId){
        return PocMatchedTask::where('del_flag','N')->whereIn('visit_task_id',$visit_task_id)->where('agency_id',$agencyId)->get();
    }
}