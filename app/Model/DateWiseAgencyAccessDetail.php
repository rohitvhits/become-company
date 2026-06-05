<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DateWiseAgencyAccessDetail extends Model
{
    public $timestamps = false;
    protected $table = 'date_view_agency_access_details';
    protected $fillable = ['id', 'date_view_agency_access_id', 'permission','start_date', 'end_date', 'del_flag', 'created_date', 'created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by'];
}
