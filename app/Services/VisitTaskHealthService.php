<?php

namespace App\Services;

use App\Model\VisitTaskHealth;
use Illuminate\Support\Facades\DB;

class VisitTaskHealthService
{

    public function save($item)
    {
        return VisitTaskHealth::updateOrCreate(
            [
                'code' => $item['code'] ?? null,
            ],
            [
                'task_health_id'=>$item['taskHealthId'] ?? null,
                'name' => $item['name'] ?? null,
                'code' => $item['code'] ?? null,
                'created_date' => now()
            ]
        );
    }

}