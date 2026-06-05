<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class signatureUpload extends Model
{
    use SoftDeletes;
    protected $table = 'signature_uploads';
    protected $guarded = ['id'];

}