<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMaster extends Model
{
    use SoftDeletes;
    protected $table = 'event_master';
    protected $guarded = ['id'];
}
