<?php

namespace App\Services;

use App\Model\PocMatchedTask;
use App\Model\HHAPocTask;

class PocMatchedTaskService
{

    public function save($item,$pocTask)
    {
        return PocMatchedTask::updateOrCreate(
            [
                'hha_task_id' => $pocTask[$item['code']]['hha_task_id'],
            ],
            [
                'visit_task_id'=>$item['taskHealthId'] ?? null,
                'name' =>$pocTask[$item['code']]['hha_task_name'],
                'hha_code' =>$pocTask[$item['code']]['hha_task_code'],
                'visit_task_code'=>$item['code'],
                'visit_task_name'=>$item['name'] ?? null,
            ]
        );
    }

    /**
     * Get hha_poc_task data keyed by visit_task_id for the given list of visit task IDs.
     * Join: poc_matched_tasks.hha_task_id = hha_poc_task.task_id
     *
     * @param  array  $visitTaskIds
     * @return \Illuminate\Support\Collection  keyed by visit_task_id
     */
    public function getMatchedHhaPocTasksByVisitTaskIds(array $visitTaskIds,$agency_id = ""): \Illuminate\Support\Collection
    {
        if (empty($visitTaskIds)) {
            return collect();
        }

        return PocMatchedTask::whereIn('visit_task_id', $visitTaskIds)->where('agency_id',$agency_id)
            ->get()
            ->mapWithKeys(function ($matched) {
                $hhaPocTask = HHAPocTask::where('task_id', $matched->hha_task_id)->first();
                return [
                    $matched->visit_task_id => [
                        'task_id'   => $hhaPocTask ? $hhaPocTask->task_id   : null,
                        'task_name' => $hhaPocTask ? $hhaPocTask->task_name : null,
                        'task_code' => $hhaPocTask ? $hhaPocTask->task_code : null,
                    ]
                ];
            });
    }

}