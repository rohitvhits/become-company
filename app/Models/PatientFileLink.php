<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFileLink extends Model
{
    protected $table = 'patient_file_links';

    protected $guarded = ["id"];

    public function agencyFile(): BelongsTo
    {
        return $this->belongsTo(\App\Models\AgencyFile::class, 'agency_file_id')->withTrashed();
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(\App\Model\Patient::class, 'patient_id');
    }

    public function linkedBy(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'linked_by');
    }
}
