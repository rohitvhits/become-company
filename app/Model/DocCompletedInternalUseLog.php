<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DocCompletedInternalUseLog extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'doc_completed_internal_use_log';
	protected $fillable = ['id','patient_id','doc_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'del_flag'];
}
