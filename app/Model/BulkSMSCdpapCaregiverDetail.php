<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BulkSMSCdpapCaregiverDetail extends Model
{
    public $timestamps =false;
    protected $table = "bulk_sms_cdpap_caregiver_detail";
    protected $guarded = ["id"];
    protected $fillable = ['id','bulk_sms_cdpap_caregiver_id','patient_id','mobile','phone','sms_status','mobile_deliver_sms_status','phone_deliver_sms_status', 'del_flag', 'created_date','created_by', 'updated_date', 'updated_by','mobile_status_updated_date','phone_status_updated_date','mobile_sms_id','phone_sms_id'];
    

}
