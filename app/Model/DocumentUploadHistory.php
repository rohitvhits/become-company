<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentUploadHistory extends Model
{
    use SoftDeletes;
    public $timestamps =false;
    protected $table = 'document_upload_history';
    protected $guarded = ['id'];
}
