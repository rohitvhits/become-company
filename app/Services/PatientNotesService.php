<?php
namespace App\Services;

use App\Helpers\Utility;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\PatientNotes;

class PatientNotesService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";
		
		$insert = new PatientNotes($data);
		$insert->save();
		$insert_id =$insert->id;
		
		return $insert_id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =PatientNotes::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =PatientNotes::where($where)->update($data); 
		return $update;
	}

	public function getData($full_name,$email,$phone){
		$where = 'deleted_flag ="N"';
		if($full_name !=''){
			$where .=' and full_name LIKE "%'.$full_name.'%"';
		}
		if($email !=''){
			$where .=' and email ="'.$email.'"';
		}
		if($phone !=''){
			$where .=' and phone = "'.$phone.'"';
		}
		
		$query = Doctor::whereRaw($where)->orderBy('id','desc')->paginate(10);
		return $query;
		
	}
	
	public function getDetailById($id){
		$query = Doctor::where('deleted_flag','N')->where('id',$id)->first();
		return $query;
	}
	
	public function getDataExport($full_name,$email,$phone){
		$where = 'deleted_flag ="N"';
		if($full_name !=''){
			$where .=' and full_name LIKE "%'.$full_name.'%"';
		}
		if($email !=''){
			$where .=' and email ="'.$email.'"';
		}
		if($phone !=''){
			$where .=' and phone = "'.$phone.'"';
		}
		
		$query = Doctor::whereRaw($where)->get();
		return $query;
		
	}
	
	public function getDoctorList(){
		$query = Doctor::where('deleted_flag','N')->get();
		return $query;
		
	}
	public function getCountForNotesPatientId($patientId){
		return PatientNotes::where('delete_flag','N')->where('patient_id',$patientId)->where('type','Agency')->get();
	}

	public static function patientNoteCallCounter($patientId){
		return PatientNotes::where('patient_id',$patientId)->where('call_flag','Call')->get();
	}
	
	public function getRecentNotesByAgencyUser($userIds,$patient_id=""){
		$auth = auth()->user();
		$agencyId = Utility::getUserWiseAgency();
		
		$startDate = date('Y-m-d',strtotime('-1 day'));

		$query =  PatientNotes::with(['patient:id,first_name,last_name,sms,created_date,agency_id','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->whereDate('created_date','>=',$startDate)->whereDate('created_date','<=',date('Y-m-d'));
		if($patient_id !=""){
			$query->where('patient_id', $patient_id);
		}
		$query->whereHas('patient', function($squery) use ($agencyId,$auth) {
			if(count($agencyId) >0){
				$squery->whereIn('agency_id',$agencyId);
			}

			if($auth->record_access !="All"){
				$squery->where('type',$auth->record_access);
			}
			
		});

	
		$query =  $query->orderBy('created_date','desc')->get();
		return $query;
	}

	public function getNotesOfAgency($userIds){		
		return PatientNotes::with(['patient','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->whereIn('created_by',$userIds)->where('type','Agency')->orderBy('created_date','desc')->limit(10)->get();
	}

	public function getNotesOfNyBestUser($userIds){
		return PatientNotes::with(['patient','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->whereIn('created_by',$userIds)->where('type','Self')->orderBy('created_date','desc')->limit(10)->get();
	}

	public function getNotesOfAgencyIdWise($userIds,$agencyId){		
		return PatientNotes::with(['patient','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->whereIn('created_by',$userIds)->where('type','Agency')->orderBy('created_date','desc')
		->whereHas('userDetails.agencyDetails', function($query) use ($agencyId) {
			$query->whereIn('id', $agencyId);
		})
		->get();
	}

	public function getNotesDataOfAgency($agencyId,$perPage,$page){		
		return PatientNotes::with(['patient:id,first_name,last_name','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->select('patient_id','message','created_date','created_by','type')->where('type','Agency')->whereHas('patient.agencyDetail', function($query) use ($agencyId) {
			$query->where('id', $agencyId);
		})->orderBy('created_date','desc')->paginate($perPage, ['*'], 'page', $page);
	}

	public function getNotesDataOfNyBestUser($agencyId,$perPage,$page){
		return PatientNotes::with(['patient:id,first_name,last_name','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->select('patient_id','message','created_date','created_by','type')->whereHas('patient.agencyDetail', function($query) use ($agencyId) {
			$query->where('id', $agencyId);
		})->where('type','Self')->orderBy('created_date','desc')->paginate($perPage, ['*'], 'page', $page);
	}

	public function getNotesOfAgencyIdUser($agencyId,$perPage,$page,$userId){		
		$query = PatientNotes::with(['patient','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->where('type','Agency')->orderBy('created_date','desc');
		if(!empty($agencyId[0])){
			$query->whereHas('userDetails.agencyDetails', function($query) use ($agencyId) {
				$query->whereIn('id', $agencyId);
			});
		}
		if(!empty($userId)){
			$query->where('created_by',$userId);
		}
		$query = $query->paginate($perPage, ['*'], 'page', $page);
		return $query;
	}

	public function getAllNotesOfAgencyDateWise($userIds,$perPage,$page,$from_date,$to_date){		
		$query = PatientNotes::with(['patient','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->whereIn('created_by',$userIds)->where('type','Agency')->orderBy('created_date','desc');
		if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		return $query->paginate($perPage, ['*'], 'page', $page);
	}

	public function getAllNotesOfNyBestUserDateWise($userIds,$perPage,$page,$from_date,$to_date){
		$query =  PatientNotes::with(['patient','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name'])->whereIn('created_by',$userIds)->where('type','Self')->orderBy('created_date','desc');
		if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		return $query->paginate($perPage, ['*'], 'page', $page);
	}

	public function getNotesDetailById($id){
		$query = PatientNotes::where('delete_flag','N')->where('id',$id)->first();
		return $query;
	}

	public function getAllFlagData(){
		$query = PatientNotes::selectRaw('patient_notes.*,users.id as uid,users.first_name,users.last_name,users.name')
			->leftjoin('users', function ($join) {
				$join->on('patient_notes.created_by', '=', 'users.id');
			})
			
			->where("patient_notes.flag", 1);			
		return $query->paginate(50);
	}

	public function getExistingNotesByPatientIds($patientId){
		return PatientNotes::select('id','patient_id')->where('delete_flag','N')->where('patient_id',$patientId)->get();
	}

	public  function getAllDetailsById($id){
		$auth = auth()->user();
		$query = PatientNotes::selectRaw('patient_notes.id,patient_notes.patient_id,patient_notes.call_flag,patient_notes.created_date,patient_notes.message,patient_notes.message_status,patient_notes.type,users.id as uid,users.first_name,users.last_name,users.name')
			->join('users', function ($join) {
				$join->on('patient_notes.created_by', '=', 'users.id');
			})
			
			->where("patient_notes.id", $id)
			->first(); 
		return $query;
	}
}