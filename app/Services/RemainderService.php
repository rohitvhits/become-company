<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\Remainder;

class RemainderService{
	
	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		
		
		$insert = new Remainder($data);
		$insert->save();
		$insertId = $insert->id;

		
		
		return $insertId;
		
	}
	public  function update($data,$where){
		$userId = Auth()->user();
		$data['updated_date']=date('Y-m-d H:i:s');
		if($userId){
		$data['updated_by']=$userId['id'];
		}
		$update =Remainder::where($where)->update($data); 
		return $update;
	}
	public  function updateWithoutUpdateDate($data,$where){

		$update =Remainder::where($where)->update($data); 
		return $update;
	}
	function getData(){
		
		$auth = auth()->user();
		$query =Remainder::selectRAW('remember_master.*,CONCAT(us.first_name," ",us.last_name) as fullname')
				->leftjoin('users as us',function($join){
					$join->on('us.id','=','remember_master.created_by');
				})->where('remember_master.del_flag','N');
		$query->whereRaw("remember_master.created_by ='".$auth['id']."' or concat('[',REPLACE(remember_master.employee_id,',','],['),']') LIKE '%[{$auth['id']}]%' ");
		
		$mysql = $query->orderBy('remember_master.id','desc')->paginate(50);
		return $mysql;
		
	}
	public  function SoftDelete($data,$where){
		$userId = Auth()->user();
		$data['deleted_date']=date('Y-m-d H:i:s');
		$data['deleted_by']=$userId['id'];
		$update =Remainder::where($where)->update($data); 
		return $update;
	}
	function getDetailsById($id){
		$query =Remainder::select('*')->where('id',$id)->where('del_flag','N')->first();
		return $query;
	}
	
	function getCurrentDateData(){
		$query = Remainder::where('start_date',date('Y-m-d'))->where('del_flag','N')->where('notfication_sent_flag',0)->get();
		return $query;
		
	}
}

?>