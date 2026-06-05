<?php
namespace App\Services;
use App\Model\ApproveStamp;

class StampService{

    public function stampList()
	{
		$query = ApproveStamp::select('approve_stamp.id','title','image','users.first_name','users.last_name','approve_stamp.is_default','approve_stamp.created_at')->leftjoin('users',function($join){
			$join->on('users.id','=','approve_stamp.created_by');
			$join->where('users.delete_flag','N');
		})->orderBy('approve_stamp.id','desc')->paginate(50);
		return $query;
	}

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new ApproveStamp($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = ApproveStamp::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$update = ApproveStamp::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = ApproveStamp::where('id', $id);
		$query = $query->first();
		return $query;
	}

	public function checkDefaultRecordExist($id){
		$query = ApproveStamp::where('is_default','1')->where('id','!=',$id)->get();
		return $query;
	}

	public function updateStatus($id,$is_default){
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$data['is_default'] = $is_default;
		$update = ApproveStamp::where('id', $id)->update($data);
		return $update;
	}

	public function getStampUser(){
		$query = ApproveStamp::get();
		return $query;
	}
}