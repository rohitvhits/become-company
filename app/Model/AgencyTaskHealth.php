<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyTaskHealth extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $guarded = ['id']; 
    protected $table = 'agency_task_health';
}
