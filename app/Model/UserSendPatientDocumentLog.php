<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserSendPatientDocumentLog extends Model
{
	public $timestamps =false;
	protected $table = 'user_send_patient_document_log';
	protected $fillable = ['id','patient_id','document_id','email','send_back_to_agency','note','del_flag', 'created_date','created_by'];

	
}
