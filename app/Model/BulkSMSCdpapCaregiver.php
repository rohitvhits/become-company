<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BulkSMSCdpapCaregiver extends Model
{
    public $timestamps =false;
    protected $table = "bulk_sms_cdpap_caregiver";
    protected $guarded = ["id"];
    protected $fillable = ['id','message','del_flag','status','created_date','created_by','updated_date','updated_by'];
}
