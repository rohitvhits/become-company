<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\PatientSMSLogDay;

class PatientSMSLogDayService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['del_flag'] = "N";
		
		$insert = new PatientSMSLogDay($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		
		$update =PatientSMSLogDay::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =PatientSMSLogDay::where($where)->update($data); 
		return $update;
	}
	
	public function getLastSMSSend($patientId){
		$query = PatientSMSLogDay::where('patient_id',$patientId)->whereDate('created_date',date('Y-m-d'))->count();
		return $query;
		
	}

}