<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\DoctorPaperWork;

class DoctorPaperWorkService{

	public static function save($data){ 
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new DoctorPaperWork($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =DoctorPaperWork::where($where)->update($data); 
		return $update;
	}
	public static function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =DoctorPaperWork::where($where)->update($data); 
		return $update;
	}
	public static function getDoctorPaperWorkList($id="",$record_id="",$name="",$portal_id="",$gender="",$dob="",$doctor_name="",$doctor_no="",$doctor_fax="",$agency_name="",$status=""){
		$query = DoctorPaperWork::select('doctor_paper_work.id','doctor_paper_work.name','doctor_paper_work.portal_id','doctor_paper_work.gender','doctor_paper_work.dob','doctor_paper_work.doctor_name','doctor_paper_work.phone','doctor_paper_work.fax','doctor_paper_work.agency','doctor_paper_work.rep','doctor_paper_work.status','users.first_name','users.last_name')
		->leftjoin('users',function($join){
					$join->on('users.id','=','doctor_paper_work.emc_user_id');
				})
				->where('doctor_paper_work.del_flag','N');
				if($id !=''){
					$query->where('doctor_paper_work.record_id',$id);
				}
				if($record_id !=''){
					$query->where('doctor_paper_work.id',$record_id);
				}
				if($name !=''){
					$query->where('doctor_paper_work.name','LIKE','%'.$name.'%');
				}
				if($portal_id !=''){
					$query->where('doctor_paper_work.portal_id',$portal_id);
				}
				if($gender !=''){
					
					$query->whereRaw('LOWER(doctor_paper_work.gender) LIKE "%'.substr($gender,0,1).'%"');
				}
				if($dob !=''){
					$query->where('doctor_paper_work.dob',date('Y-m-d',strtotime($dob)));
				}
				if($doctor_name !=''){
					$query->where('doctor_paper_work.doctor_name','LIKE','%'.$doctor_name.'%');
				}
				if($doctor_no !=''){
					$query->where('doctor_paper_work.phone',$doctor_no);
				}
				if($doctor_fax !=''){
					$query->where('doctor_paper_work.fax','LIKE','%'.$doctor_fax.'%');
				}
				if($agency_name !=''){
					$query->where('doctor_paper_work.agency','LIKE','%'.$agency_name.'%');
				}
				if($status !=''){
					$query->where('doctor_paper_work.status',$status);
				}
				$mysql = $query->orderBy('doctor_paper_work.id','desc')
				->paginate(50);
		return $mysql;
		
	}
	
	public static function getDoctorPaperWorkListExport($id="",$record_id="",$name="",$portal_id="",$gender="",$dob="",$doctor_name="",$doctor_no="",$doctor_fax="",$agency_name="",$status=""){
		$query = DoctorPaperWork::select('doctor_paper_work.id','doctor_paper_work.name','doctor_paper_work.portal_id','doctor_paper_work.gender','doctor_paper_work.dob','doctor_paper_work.doctor_name','doctor_paper_work.phone','doctor_paper_work.fax','doctor_paper_work.agency','doctor_paper_work.rep','doctor_paper_work.status','users.first_name','users.last_name')
				->leftjoin('users',function($join){
					$join->on('users.id','=','doctor_paper_work.emc_user_id');
				})
				->where('doctor_paper_work.del_flag','N');
				if($id !=''){
					$query->where('doctor_paper_work.record_id',$id);
				}
				if($record_id !=''){
					$query->where('doctor_paper_work.id',$record_id);
				}
				if($name !=''){
					$query->where('doctor_paper_work.name','LIKE','%'.$name.'%');
				}
				if($portal_id !=''){
					$query->where('doctor_paper_work.portal_id',$portal_id);
				}
				if($gender !=''){
					
					$query->whereRaw('LOWER(doctor_paper_work.gender) LIKE "%'.substr($gender,0,1).'%"');
				}
				if($dob !=''){
					$query->where('doctor_paper_work.dob',date('Y-m-d',strtotime($dob)));
				}
				if($doctor_name !=''){
					$query->where('doctor_paper_work.doctor_name','LIKE','%'.$doctor_name.'%');
				}
				if($doctor_no !=''){
					$query->where('doctor_paper_work.phone',$doctor_no);
				}
				if($doctor_fax !=''){
					$query->where('doctor_paper_work.fax','LIKE','%'.$doctor_fax.'%');
				}
				if($agency_name !=''){
					$query->where('doctor_paper_work.agency','LIKE','%'.$agency_name.'%');
				}
				if($status !=''){
					$query->where('doctor_paper_work.status',$status);
				}
				$mysql = $query->orderBy('doctor_paper_work.id','asc')->get();
		return $mysql;
		
	}
	public static function getDoctorPaperDetailById($id){
		$query = DoctorPaperWork::where('id',$id)->where('del_flag','N')->first();
		return $query;
	}
	public static function getDetailsByRecordId($id){
		$query = DoctorPaperWork::where('record_id',$id)->where('del_flag','N')->first();
		return $query;
	}
	public static function getDoctorPaperWorkListEMC($id=""){
		$query = DoctorPaperWork::select('doctor_paper_work.id','doctor_paper_work.name','doctor_paper_work.portal_id','doctor_paper_work.gender','doctor_paper_work.dob','doctor_paper_work.doctor_name','doctor_paper_work.phone','doctor_paper_work.fax','doctor_paper_work.agency','doctor_paper_work.rep','doctor_paper_work.status','users.first_name','users.last_name')
		->leftjoin('users',function($join){
					$join->on('users.id','=','doctor_paper_work.emc_user_id');
				})
				->where('doctor_paper_work.del_flag','N');
				if($id !=''){
					$query->where('doctor_paper_work.emc_user_id',$id);
				}
				
				$mysql = $query->orderBy('doctor_paper_work.id','desc')
				->paginate(50);
		return $mysql;
		
	}
}