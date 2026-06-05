<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\NyBestReminderNotification;

class NyBestReminderNotificationService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new NyBestReminderNotification($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =NyBestReminderNotification::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =NyBestReminderNotification::where($where)->update($data); 
		return $update;
	}
	public function getReminderAppoinment($id){
		$query = NyBestReminderNotification::where('del_flag','N')->where('patient_id',$id)->orderBy('id','desc')->get();
		return $query;
		
	}
	public function getReminiderUserList(){
		$query  = NyBestReminderNotification::select('id','patient_id','email','notes','date')->where('del_flag','N')->where('status',0)->where('date',date('Y-m-d'))->get();
		return $query;
		
	}
}