<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HHAAuditLog extends Model
{
    use HasFactory;
    protected $table = "hha_audit_log";
    protected $guarded = ["id"];

    public function patient()
    {
        return $this->belongsTo(\App\Model\Patient::class, 'patient_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}
