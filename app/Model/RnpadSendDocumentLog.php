<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RnpadSendDocumentLog extends Model
{
    protected $table = 'rnpad_send_document_log';
    public $timestamps = false;
    protected $guarded = ['id'];
}