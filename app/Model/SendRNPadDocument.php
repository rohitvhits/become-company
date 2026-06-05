<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use App\Master;

class SendRNPadDocument extends Model
{
    public $timestamps = false;
    protected $table = 'rnpad_document';
    protected $guarded = ['id'];

    /**
     * Get the patient associated with this document
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    /**
     * Get the request service
     */
    public function requestService()
    {
        return $this->belongsTo(Master::class, 'request_service_id', 'id');
    }

    /**
     * Get the user who created this document
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user assigned to review this document
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assign_document_review', 'id');
    }

    /**
     * Get the user who sent this document
     */
    public function sendBy()
    {
        return $this->belongsTo(User::class, 'send_third_party_document_by', 'id');
    }
}