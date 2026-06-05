<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Patient;
use App\Agency;
use App\User;

class MergeAgencyDeletionData extends Model
{
    protected $table = 'merge_agency_deletion_data';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'old_agency_id',
        'new_agency_id',
        'status',
        'error_message',
        'created_by',
        'created_at',
        'processed_at',
        'ip'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the patient associated with this merge record
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    /**
     * Get the old agency
     */
    public function oldAgency()
    {
        return $this->belongsTo(Agency::class, 'old_agency_id', 'id');
    }

    /**
     * Get the new agency
     */
    public function newAgency()
    {
        return $this->belongsTo(Agency::class, 'new_agency_id', 'id');
    }

    /**
     * Get the filter agency (deleted agency)
     */
    public function filterAgency()
    {
        return $this->belongsTo(Agency::class, 'filter_agency_id', 'id');
    }

    /**
     * Get the user who created this merge request
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Scope to get pending records
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get failed records
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get completed records
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Mark record as processing
     */
    public function markAsProcessing()
    {
        $this->status = 'processing';
        $this->save();
    }

    /**
     * Mark record as completed
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->processed_at = now();
        $this->save();
    }

    /**
     * Mark record as failed
     */
    public function markAsFailed($errorMessage)
    {
        $this->status = 'failed';
        $this->error_message = $errorMessage;
        $this->save();
    }
}
