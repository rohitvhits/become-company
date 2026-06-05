<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TaskHealthCriticalAlert extends Model
{
    protected $table = 'task_health_critical_alerts';

    protected $fillable = [
        'task_id',
        'patient_id',
        'critical_alerts',
        'payload',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_flag',
        'deleted_at',
        'resolved_flag',
        'resolved_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}