<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ScheduleLocationDisable extends Model
{
    protected $table = 'schedule_location_disable';
    protected $guarded = ['id'];
    public $timestamps = false;
}
