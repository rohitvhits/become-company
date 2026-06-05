<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class HHALog extends Model
{
    public $timestamps = false;
    protected $table = 'hha_module_log';
    protected $guarded = ['id'];
}