<?php

namespace App\Services;

use App\Agency;
use App\Model\HHAPocTask;
use App\Model\PocMatchedTask;

class PocMappingService
{
    public function getListing($filters = [])
    {
        $query = PocMatchedTask::select(
                'poc_matched_tasks.*',
                'hha_poc_task.task_name',
                'hha_poc_task.task_code',
                'agency.agency_name'
            )
            ->leftJoin('hha_poc_task', 'poc_matched_tasks.hha_task_id', '=', 'hha_poc_task.task_id')
            ->leftJoin('agency', 'poc_matched_tasks.agency_id', '=', 'agency.id')
            ->where('poc_matched_tasks.del_flag', 'N');

        if (!empty($filters['agency_id'])) {
            $query->where('poc_matched_tasks.agency_id', $filters['agency_id']);
        }

        if (!empty($filters['hha_task_id'])) {
            $query->where('poc_matched_tasks.hha_task_id', $filters['hha_task_id']);
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('poc_matched_tasks.created_at', [
                $filters['from_date'] . ' 00:00:00',
                $filters['to_date'] . ' 23:59:59',
            ]);
        }

        return $query->orderBy('poc_matched_tasks.id', 'desc')->paginate(50);
    }

    public function save($data)
    {
        return PocMatchedTask::create([
            'hha_task_id'   => $data['hha_task_id'],
            'visit_task_id' => $data['visit_task_id'] ?? null,
            'agency_id'     => $data['agency_id'],
            'del_flag'      => 'N',
        ]);
    }

    public function getById($id)
    {
        return PocMatchedTask::select(
                'poc_matched_tasks.*',
                'hha_poc_task.task_name',
                'agency.agency_name'
            )
            ->leftJoin('hha_poc_task', 'poc_matched_tasks.hha_task_id', '=', 'hha_poc_task.task_id')
            ->leftJoin('agency', 'poc_matched_tasks.agency_id', '=', 'agency.id')
            ->where('poc_matched_tasks.id', $id)
            ->first();
    }

    public function update($id, $data)
    {
        return PocMatchedTask::where('id', $id)->update([
            'hha_task_id'   => $data['hha_task_id'],
            'visit_task_id' => $data['visit_task_id'],
            'agency_id'     => $data['agency_id'],
        ]);
    }

    public function delete($id)
    {
        return PocMatchedTask::where('id', $id)->update(['del_flag' => 'Y']);
    }

    public function getTasksByAgency($agencyId)
    {
        return HHAPocTask::select('task_id', 'task_name', 'task_code')
            ->where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->get();
    }

    public function searchTasks($query, $agencyId = null)
    {
        $q = HHAPocTask::select('task_id as id', 'task_name as name')
            ->where('del_flag', 'N')
            ->where('task_name', 'like', '%' . $query . '%');

        if (!empty($agencyId)) {
            $q->where('agency_id', $agencyId);
        }

        return $q->limit(30)->get();
    }

    public function getTasksWithMappings($agencyId)
    {
        $tasks = HHAPocTask::select('task_id', 'task_name', 'task_code')
            ->where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->orderBy('task_name', 'asc')
            ->get();

        $mappings = PocMatchedTask::where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->get()
            ->keyBy('hha_task_id');

        return $tasks->map(function ($task) use ($mappings) {
            $mapped = $mappings->get($task->task_id);
            return [
                'task_id'       => $task->task_id,
                'task_name'     => $task->task_name,
                'task_code'     => $task->task_code,
                'visit_task_id' => $mapped ? $mapped->visit_task_id : '',
                'is_mapped'     => !is_null($mapped),
            ];
        });
    }

    public function saveAll($agencyId, $mappings)
    {
        foreach ($mappings as $mapping) {
            $hhsTaskId   = $mapping['hha_task_id'] ?? null;
            $visitTaskId = $mapping['visit_task_id'] ?? '';
            $checked     = !empty($mapping['checked']);

            if (!$hhsTaskId) {
                continue;
            }

            $existing = PocMatchedTask::where('agency_id', $agencyId)
                ->where('hha_task_id', $hhsTaskId)
                ->first();

            if ($checked) {
                if ($existing) {
                    $existing->update(['visit_task_id' => $visitTaskId, 'del_flag' => 'N']);
                } else {
                    PocMatchedTask::create([
                        'agency_id'     => $agencyId,
                        'hha_task_id'   => $hhsTaskId,
                        'visit_task_id' => $visitTaskId,
                        'del_flag'      => 'N',
                    ]);
                }
            } else {
                if ($existing) {
                    $existing->update(['del_flag' => 'Y']);
                }
            }
        }
    }

    public function getAgencies()
    {
        return Agency::select('id', 'agency_name')
            ->where('delete_flag', 'N')
            ->where('enable_hha','1')
            ->orderBy('agency_name', 'asc')
            ->get();
    }
}
