<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class SendDocumentRemoteLog extends Model
{
    protected $table = 'send_document_remote_log';

    protected $guarded = ["id"];
}