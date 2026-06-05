<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class AgencyPocDocumentType extends Model
{

    protected $table = 'agency_poc_document_type';
    protected $guarded = ['id'];

}