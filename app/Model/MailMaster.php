<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
;
class MailMaster extends Model
{
	use Notifiable; 
	//use Archivable;
	public $timestamps = false;
	protected $table = 'mail_master';
	protected $fillable = ['id','record_id','full_name','homecare','address', 'mail', 'language', 'send_date','send_by','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'del_flag'];

	
}
 