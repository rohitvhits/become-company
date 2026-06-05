<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DocumentSendThirdPartyAPILog extends Model
{
    
    protected $table = "document_send_third_party_log";
    protected $guarded = ["id"];
}
