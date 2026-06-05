<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EsignErrorLog extends Model
{

    protected $table = 'esign_error_log';
    protected $guarded = ['id'];
    public $timestamps = false;
    
}
