<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelehealthLocationScheduleDay extends Model
{
    use SoftDeletes;
    
    protected $table = 'telehealth_location_schedule_days';
    protected $fillable = ['id','schedule_id', 'day','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by','del_flag'];
    
    public function schedule()
    {
        return $this->belongsTo(TelehealthLocationSchedule::class, 'schedule_id');
    }
} 