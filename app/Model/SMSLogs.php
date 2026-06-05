<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class SMSLogs extends Model
{
    protected $table = 'sms_logs';
    protected $guarded = ["id"];

    public static function getlist($patientId){
        return SMSLogs::where('patient_id',$patientId)->where('delete_flag','N')->paginate(10);
    }

    public static function getlistWithIds($patientId){
        // return SMSLogs::whereIn('patient_id',$patientId)->where('delete_flag','N')->paginate(10);
        return SMSLogs::select('sms_logs.*','users.first_name','users.last_name')
            ->leftjoin('users',function($join){
                $join->on('users.id','=','sms_logs.created_by');
            })->whereIn('sms_logs.patient_id',$patientId)->where('sms_logs.delete_flag','N')->where('users.delete_flag','N')->paginate(10);
    }
}
