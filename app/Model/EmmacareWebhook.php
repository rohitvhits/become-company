<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmmacareWebhook extends Model
{
    use SoftDeletes;
    protected $table = 'emmacare_webhook';
    protected $guarded = ['id'];

}
