<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DateWiseAgencyAccessLog extends Model
{
    public $timestamps = false;
    protected $table = 'date_view_agency_access_log';
    protected $fillable = ['id', 'date_wise_agency_id', 'agency_id','type', 'message', 'del_flag', 'created_date', 'created_by', 'old_response', 'new_response', 'ip_address', 'user_id'];
}
