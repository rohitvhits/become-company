<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class WriteDocument extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'write_documents';
	protected $fillable = ['id','document_patient_id','type','file_upload','response','docWidth','docHeight','document_name', 'created_at', 'updated_at', 'deleted_at','created_by','updated_by','deleted_by','created_by','updated_by','deleted_by','old_file_upload'];
	
}
