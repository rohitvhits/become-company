<?php

namespace App\Services;

class SupervisionService
{
    public function getData($taskId, $patientId)
    {
        return [
            'success'    => true,
            'task_id'    => $taskId,
            'patient_id' => $patientId,
        ];
    }
}
