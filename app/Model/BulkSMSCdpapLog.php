<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BulkSMSCdpapLog extends Model
{
    public $timestamps =false;
    protected $table = "bulk_sms_cdpap_log";
    protected $guarded = ["id"];
    protected $fillable = ['id','bulk_cdpap_id','bulk_cdpap_detail_id','patient_id','mobile_no','sms','sms_id','data','status_deliver_date','del_flag', 'created_date','updated_date'];
    

}
