<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\HHACaregiverMedical;

class HHACaregiverMedicalService{

	public  static function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new HHACaregiverMedical($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =HHACaregiverMedical::where($where)->update($data); 
		return $update;
	}
	public static function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =HHACaregiverMedical::where($where)->update($data); 
		return $update;
	}

	public static function getDetails($caregiverId,$medicalId){
		return HHACaregiverMedical::where('caregiver_id',$caregiverId)->where('medical_id',$medicalId)->first();

	}

	public static function getCaregiverComplianceList($caregiverId,$status){
		$query= HHACaregiverMedical::where('caregiver_id',$caregiverId);
			if($status	!=""){
				$query->where('status',$status);
			}
		$query=$query->get();

		return	$query;
	}

	public static function getStatusList(){
		return HHACaregiverMedical::select('status')->where('del_flag',"N")->groupBy('status')->get();
	}
}