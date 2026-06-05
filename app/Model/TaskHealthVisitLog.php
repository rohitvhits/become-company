<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TaskHealthVisitLog extends Model
{
    public $timestamps = false;
    protected $table = 'task_health_visit_log';
    protected $fillable = ['id','response','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','api_key','url','ip','data','type'];
}
