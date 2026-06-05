<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class AssignEMCNotesRecord extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'assign_emc_record_notes';
	protected $fillable = ['id','assign_id','notes', 'del_flag', 'created_date','created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by'];

	
}
