<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SMSTemplate extends Model
{

    use Notifiable;

    public $timestamps = false;
    protected $table = 'sms_template';
    protected $fillable = ['id','name','message','created_date','created_by', 'updated_date', 'updated_by','deleted_date', 'deleted_by','deleted_flag'];

}
