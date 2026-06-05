<?php

namespace App\Services;

use App\Model\HHAPocTask;
use Illuminate\Support\Facades\DB;

class HHAPOCTaskService
{

    public function save($data)
    {
        HHAPocTask::updateOrCreate(
            [
                'task_id'   => $data['task_id'],
                'agency_id' => $data['agency_id']
            ],
            [
                'task_name' => $data['task_name'],
                'task_code' => $data['task_code'],
                'category'  => $data['category'],
            ]
        );
    }

    public function getAllPOCTask(){
        return HHAPocTask::select('task_id','task_code','task_name')->where('del_flag','N')->get();
    }

    public function getAllPOCTaskWithAgencyId($agencyId){
        return HHAPocTask::select('task_id','task_code','task_name')->where('agency_id',$agencyId)->where('del_flag','N')->get();
    }
}