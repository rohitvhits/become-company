<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SendVisitingDocumentThirdParty extends Model
{
    protected $table = 'send_visiting_document_third_party';
    public $timestamps = false;
    protected $guarded = ['id'];
}