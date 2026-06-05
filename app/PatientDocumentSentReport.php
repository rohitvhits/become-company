<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PatientDocumentSentReport extends Model
{

    use Notifiable;

    public $timestamps = false;
    protected $table = 'patient_document_sent_report';
    protected $fillable = ['id', 'caregiver_code', 'subject', 'status', 'sender_id','sender_name','receipt_name', 'sent_on' , 'created_date', 'created_by','templete_id','pdf_generate','document_submit_status','del_flag','type','latitude','longitude','mobileinfo','sourceFile','referral_id','main_intakeId','final_document','receipted','groupId','singstatus','approved_date','approved_by','mobile'];
 
	
}
