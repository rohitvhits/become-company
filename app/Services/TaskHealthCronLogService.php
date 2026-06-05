<?php

namespace App\Services;

use App\Model\TaskHealthCronLog;

class TaskHealthCronLogService
{
    public function dataList($search)
    {
        $query = TaskHealthCronLog::with([
                'agencyDetails:id,agency_name',
                'patientDetails:id,first_name,last_name',
                'taskHealthMaster:id,task_id',
            ])
            ->select('id', 'cron_name', 'task_health_id', 'patient_id', 'agency_id', 'type', 'message', 'data', 'created_at','task_id');

        if (!empty($search)) {
            if (!empty($search['agency_id'])) {
                $query->where('agency_id', $search['agency_id']);
            }

            if (!empty($search['type'])) {
                $query->where('type', $search['type']);
            }

            if (!empty($search['patient_id'])) {
                $query->where('patient_id', $search['patient_id']);
            }

            if (!empty($search['patient_name'])) {
                $name = $search['patient_name'];
                $query->whereHas('patientDetails', function ($q) use ($name) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
                });
            }

            if (!empty($search['task_id'])) {
                $taskId = $search['task_id'];
                $query->whereHas('taskHealthMaster', function ($q) use ($taskId) {
                    $q->where('task_id', $taskId);
                });
            }

            if (!empty($search['cron_name'])) {
                $query->where('cron_name', $search['cron_name']);
            }

            if (!empty($search['created_date'])) {
                $parts = explode(' - ', $search['created_date']);
                if (count($parts) === 2) {
                    $from = date('Y-m-d 00:00:00', strtotime(trim($parts[0])));
                    $to   = date('Y-m-d 23:59:59', strtotime(trim($parts[1])));
                    $query->whereBetween('created_at', [$from, $to]);
                }
            }
        }

        return $query->orderBy('id', 'desc')->paginate(50);
    }

    public function getDataById($id)
    {
        $row = TaskHealthCronLog::select('id', 'cron_name', 'task_health_id', 'patient_id', 'agency_id', 'type', 'message', 'data', 'created_at')
            ->where('id', $id)
            ->first();

        if ($row && !empty($row->data)) {
            $unserialized = @unserialize($row->data);
            $row->data    = $unserialized !== false ? $unserialized : $row->data;
        }

        return $row;
    }

    public function getAllTypes()
    {
        return TaskHealthCronLog::select('type')
            ->whereNotNull('type')
            ->groupBy('type')
            ->orderBy('type')
            ->pluck('type')
            ->toArray();
    }

    public function getAllCronNames()
    {
        return TaskHealthCronLog::select('cron_name')
            ->whereNotNull('cron_name')
            ->groupBy('cron_name')
            ->orderBy('cron_name')
            ->pluck('cron_name')
            ->toArray();
    }

    public function createLog(array $data): TaskHealthCronLog
    {
        return TaskHealthCronLog::create($data);
    }
}
