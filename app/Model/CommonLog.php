<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class CommonLog extends Model
{
    use Notifiable;

    protected $table = 'common_log';
    public $timestamps = false;
    protected $fillable = ['id','type','common_fk', 'old_response', 'new_response', 'url','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'del_flag','main_type'];
}