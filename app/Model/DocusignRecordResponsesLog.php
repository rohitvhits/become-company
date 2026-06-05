<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DocusignRecordResponsesLog extends Model
{
    public $timestamps = false;
    protected $table = "docusign_record_responses_log";
    protected $guarded = ["id"];
}
