<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\User;
class PatientNotes extends Model
{
	use Notifiable;

	protected $table = 'patient_notes';
	
	public $timestamps =false;
	protected $fillable = ['id', 'patient_id', 'created_by', 'type', 'message', 'created_date', 'delete_flag','created_by','receiver_id','call_flag','note_email'];

	public function fullName()
	{
		return ucfirst($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
	}
	public function dateTime()
	{
		return date('M d h:i A', strtotime($this->created_date));
	}
	public static function getData()
	{
		$query = PatientNotes::where('delete_flag', 'N')->orderBy('id', 'desc')->paginate(10);
		return $query;
	}
	
	public static function getRecordALLNotesByRecordID($id,$readMessage,$user_id,$callFlag=""){
		 
		$auth = auth()->user();
		$query = PatientNotes::selectRaw('patient_notes.*,users.id as uid,users.first_name,users.last_name,users.name')
			->leftjoin('users', function ($join) {
				$join->on('patient_notes.created_by', '=', 'users.id');
			})
			
			->where("patient_notes.patient_id", $id);
			//->where("patient_notes.created_by", $user_id);
			
			$type ='Self';
			if($readMessage =='Agency'){
				$type = $readMessage;
				$query->whereRaw('(patient_notes.type ="'.$type.'" OR patient_notes.type=1 )');
			}
			
			if($type =='Self'){
				$query->where("patient_notes.type",'=','Self');
				if(in_array('Super Admin',$auth->roles->pluck('name')->toArray())){
				}else{
					$query->where("patient_notes.created_by", $user_id);
				}
				
			}
		

			if($callFlag !=""){
				if($callFlag =='Call'){
					$query->where("patient_notes.call_flag", 'Call');
				}else{
					$query->whereRaw("(patient_notes.call_flag IS NULL or patient_notes.call_flag ='Normal')");
				}
			}
			
			$query->whereRaw('patient_notes.delete_flag="N"');

			
			$mysql = $query->get(); 
		return $mysql;
	
	}
	public static function getPatientNotes($id)
	{
		return PatientNotes::with('patient')->where('patient_id',$id)->where('delete_flag','N')->get();
	}
	public function patient()
	{
		return $this->belongsTo(Patient::class,'patient_id','id');
	}
	 
	public function userDetails()
	{
		return $this->belongsTo(User::class,'created_by','id');
	}

	public static function getRecordALLNotesByRecordIDWithArray($id,$readMessage,$user_id,$callFlag=""){
		 
		$auth = auth()->user();
		$query = PatientNotes::selectRaw('patient_notes.*,users.id as uid,users.first_name,users.last_name,users.name')
			->leftjoin('users', function ($join) {
				$join->on('patient_notes.created_by', '=', 'users.id');
			})
			
			->whereIn("patient_notes.patient_id", $id);
			//->where("patient_notes.created_by", $user_id);
			
			$type ='Self';
			if($readMessage =='Agency'){
				$type = $readMessage;
				$query->whereRaw('(patient_notes.type ="'.$type.'" OR patient_notes.type=1 )');
			}
			
			if($type =='Self'){
				$query->where("patient_notes.type",'=','Self');
				
				if(in_array('Super Admin',$auth->roles->pluck('name')->toArray())){
				}else{
					//$query->where("patient_notes.created_by", $user_id);
				}
				
			}
		

			if($callFlag !=""){
				if($callFlag =='Call'){
					$query->where("patient_notes.call_flag", 'Call');
				}else{
					$query->whereRaw("(patient_notes.call_flag IS NULL or patient_notes.call_flag ='Normal')");
				}
			}
			
			$query->whereRaw('patient_notes.delete_flag="N"');

			
			$mysql = $query->orderBy('patient_notes.created_date','asc')->get(); 
		return $mysql;
	
	}
}
