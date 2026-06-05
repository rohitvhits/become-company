<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Agency;

class TaskHealthUploadDocumentLog extends Model
{
    protected $table = 'task_health_upload_document_log';
    protected $guarded = ['id'];

    public function agencyDetails()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }
}
