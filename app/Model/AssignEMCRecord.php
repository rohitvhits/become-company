<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class AssignEMCRecord extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'assign_emc_record';
	protected $fillable = ['id','record_id','emc_id',"progress_notes",'status', 'del_flag', 'created_date','created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by', 'completed_date', 'completed_by','disability_questionaire','medical_report'];

	public static function checkRecordExits($recordId,$userId)
	{
		return AssignEMCRecord::where('record_id',$recordId)->where('emc_id',$userId)->first();
	}
	public static function addAssignUser($data)
	{
		return AssignEMCRecord::create($data);
	}


	public function patient()
    {
		return $this->belongsTo(Patient::class,'record_id', 'id');            
    }

	
	public function users()
	{
		return $this->belongsTo(User::class,'emc_id','id');
	}

}
