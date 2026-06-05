<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\MailMaster;

class MailMasterService{

	public static function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";
		
		$insert = new MailMaster($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =MailMaster::where($where)->update($data); 
		return $update;
	}
	public static function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =MailMaster::where($where)->update($data); 
		return $update;
	}

	public static function getList($full_name){
		$query = MailMaster::select('mail_master.*',DB::raw('CONCAT(emc.first_name," ",emc.last_name) as emcname'))
			->leftjoin('record',function($join){
				$join->on('record.id','=','mail_master.record_id');
			})
			->leftjoin('users as emc',function($join){
				$join->on('emc.id','=','record.emc_rep');
			})
		->where('mail_master.del_flag','N');
			if($full_name !=''){
				$query->where('mail_master.full_name','LIKE',"%".$full_name.'%');
			}
		$mysql = $query->orderby('mail_master.id','desc')->paginate(50);
		return $mysql;
	}
	public static function getRecordWiseMail($id){
		$query = MailMaster::where('del_flag','N')->where('record_id',$id)->orderby('id','desc')->paginate(50);
		return $query;
	}
	public static function getDetailsById($id){
		$query = MailMaster::where('del_flag','N')->where('id',$id)->first();
		return $query;
	}
}