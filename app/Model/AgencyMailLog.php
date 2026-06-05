<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class AgencyMailLog extends Model
{
    public $timestamps = false;
    protected $table = 'agency_mail_log';
    protected $fillable = ['id','agency_id','notification_email','del_flag','created_date'];
	
	
}
