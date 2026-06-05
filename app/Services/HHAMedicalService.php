<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\HHAMedical;

class HHAMedicalService{

	public static function save($data){ 
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		
		//$data['delete_flag'] = "N";
		
		$insert = new HHAMedical($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		
		$data['updated_date']=date('Y-m-d H:i:s');
		
		$update =HHAMedical::where($where)->update($data); 
		return $update;
	}
	public static function getMedicalDetails(){
		$query = HHAMedical::select('caregiver_id')->where('del_flag','N')->where('caregiver_code',null)->get();
		return $query;
		
	}
	public static function getcaregiverDetailsBYId($id){
		$query = HHAMedical::select('id','caregiver_id')->where('del_flag','N')->where('caregiver_id',$id)->first();
		return $query;
		
	}
	public static function getDetailsById($id){
		$query = HHAMedical::where('del_flag','N')->where('id',$id)->first();
		return $query;
		
	}
	public static function getCaregiverCodeServices($caregiver_code){
		$query = HHAMedical::select('medical_name')->where('del_flag','N')->where('caregiver_code',$caregiver_code)->get();
		return $query;
		
	}
	public static function getDetailsWithCaregiverCode(){
		$query = HHAMedical::where('del_flag','N')->where('caregiver_code','!=',null)->where('status','=','Pending')->where('hha_caregiver_status','Active')->get();
		return $query;
		
	}	
	public static function getPatientRecordDetails($code="",$fname="",$lname="", $dob="",$phone="",$mobile="", $gender="",$language="",$service_name="",$service_exp=""){
		$query = HHAMedical::where('del_flag','N')->where('caregiver_code','!=',null)->where('patient_record_id',null)->where('hha_caregiver_status','Active');
		if($code !=''){
			$query->where('caregiver_code',$code);
		}
		if($fname !=''){
			$query->where('caregiver_first_name','LIKE','%'.$fname.'%');
		}
		if($lname !=''){
			$query->where('caregiver_last_name','LIKE','%'.$lname.'%');
		}
		if($dob !=''){
			$query->whereDate('caregiver_dob',date('Y-m-d',strtotime($dob)));
		}
		if($phone !=''){
			$query->where('phone',$phone);
		}
		if($mobile !=''){
			$query->where('mobile',$mobile);
		}
		if($gender !=''){
			$query->where('gender',$gender);
		}
		if($language !=''){
			$query->where('language','LIKE','%'.$language.'%');
		}
		if($service_name !=''){
			$query->where('medical_name','LIKE','%'.$service_name.'%');
		}
		if($service_exp !=''){
			$explodes = explode('-',$service_exp);

			$query->whereDate('due_date','>=',date('Y-m-d',strtotime($explodes[0])))->whereDate('due_date','<=',date('Y-m-d',strtotime($explodes[1])));
		}
		$query = $query->paginate(50);
		return $query;
		
	}
}