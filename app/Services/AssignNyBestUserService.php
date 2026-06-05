<?php
namespace App\Services;
use App\Model\AssignNyBestUser;

class AssignNyBestUserService{

	public static function save($data){ 
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		
		
		$insert = new AssignNyBestUser($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =AssignNyBestUser::where($where)->update($data); 
		return $update;
	}
	
	public static function getAssignNybestUser($id){
		$query = AssignNyBestUser::select('users.first_name','users.last_name','nybest_user_id','users.email')->leftjoin('users', 'users.id', '=', 'assign_nybest_user.nybest_user_id')->where('agency_id',$id)->where('del_flag','N')->get();
		return $query;
	}

	public  function softDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['del_flag'] = 'Y';
		return AssignNyBestUser::where($where)->update($data);
	}

	public static function getAssignNybestUserId($id){
		$query = AssignNyBestUser::where('agency_id',$id)->where('del_flag','N')->get();
		return $query;
	}

	public static function insert($data)
	{
		$result = AssignNyBestUser::insert($data);
		return $result;
	}
}