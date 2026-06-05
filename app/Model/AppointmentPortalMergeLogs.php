<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AppointmentPortalMergeLogs extends Model
{
    protected $table = 'appointment_portal_merge_logs';
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Relationship to get the main (parent) patient
     */
    public function mainPatient()
    {
        return $this->belongsTo(Patient::class, 'main_patient_id', 'id');
    }

    /**
     * Relationship to get the merged (child) patient
     */
    public function mergedPatient()
    {
        return $this->belongsTo(Patient::class, 'merge_patient_id', 'id');
    }

    /**
     * Relationship to get the root patient in merge chain
     */
    public function rootPatient()
    {
        return $this->belongsTo(Patient::class, 'root_patient_id', 'id');
    }

    /**
     * Relationship to get the direct parent in merge chain
     */
    public function parentPatient()
    {
        return $this->belongsTo(Patient::class, 'parent_patient_id', 'id');
    }

    /**
     * Relationship to get all children (direct merges into this patient)
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_patient_id', 'merge_patient_id')
            ->where('del_flag', 'N');
    }

    /**
     * Relationship to get parent merge log
     */
    public function parentMergeLog()
    {
        return $this->belongsTo(self::class, 'parent_patient_id', 'main_patient_id')
            ->where('del_flag', 'N');
    }

    /**
     * Get merge path as array
     */
    public function getMergePathArrayAttribute()
    {
        return $this->merge_path ? explode(',', $this->merge_path) : [];
    }
}
