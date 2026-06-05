<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SendTaskHealthDocumentLog extends Model
{
    protected $table = 'task_health_send_document_log';
    public $timestamps = false;
    protected $guarded = ['id'];
}