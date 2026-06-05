<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SendHhaDocumentLog extends Model
{
    use Notifiable;
	public $timestamps =false;
	protected $table = 'send_hha_document_log';
	protected $fillable = ['id','agencyId','caregiverId', 'medicalId','resultId','dateCompleted','del_flag','created_date', 'created_by','patient_id','document_id','request_response'];

}