<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\SMSTemplate;

class SMSTemplateService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";
		
		$insert = new SMSTemplate($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =SMSTemplate::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =SMSTemplate::where($where)->update($data); 
		return $update;
	}

	public function AllSMSListing(){
		$query = SMSTemplate::where('deleted_flag','N')->paginate(10);
		return $query;
		
	}
	
	public function getDetailById($id){
		$query = SMSTemplate::where('deleted_flag','N')->where('id',$id)->first();
		return $query;
	}
	
	public function getAllSMS(){
		$query = SMSTemplate::where('deleted_flag','N')->get();
		return $query;
	}
	
	public function getRandSMS(){
		$query = SMSTemplate::where('deleted_flag','N')->orderByRaw("RAND()")->first();
		return $query;
	}
}