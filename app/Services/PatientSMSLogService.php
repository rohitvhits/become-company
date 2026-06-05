<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\PatientSMSLog;

class PatientSMSLogService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['del_flag'] = "N";
		$insert = new PatientSMSLog($data);
		$insert_id = $insert->save();
		return $insert_id;	
	}
	
	public function getLastSMSSend($patientId){
		$query = PatientSMSLog::where('patient_id',$patientId)->whereDate('created_date',date('Y-m-d'))->count();
		return $query;
		
	}
}