<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class MultiplePatientDocApproval extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'multiple_patient_doc_approval';
	protected $fillable = ['id','patient_id','user_id','document_id','type','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by'];
}
 