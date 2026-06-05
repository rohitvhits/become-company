<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyWiseDisabledSMSService extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'agency_wise_disabled_sms_services';
    protected $guarded = ['id'];

}
