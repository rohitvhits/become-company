<?php
namespace App\Services;
use App\Model\Communication;

class CommunicationService{

    public function eventList()
	{
		$query = Communication::select('communication_master.id','title','image','start_date','end_date','status','users.first_name','users.last_name','communication_master.created_at','communication_master.content')->leftjoin('users',function($join){
			$join->on('users.id','=','communication_master.created_by');
			$join->where('users.delete_flag','N');
		})->where('deleted_flag',0)->orderBy('id','desc')->paginate(50);
		return $query;
	}

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new Communication($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = Communication::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 1;
		$update = Communication::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = Communication::where('id', $id);
		$query = $query->first();
		return $query;
	}

	public function deactiveAllEvent(){
        $data['status'] = 1;
		$update = Communication::query()->update($data);
		return $update;
	}

	public function getActiveEvent(){
		$today = date('Y-m-d');
		$query = Communication::where('status', 1)->whereDate('start_date', '<=', $today)
		->whereDate('end_date', '>=', $today);
		$query = $query->get();
		return $query;
	}

	public function getExpiredDateEvent(){
		$today = date('Y-m-d');
		$query = Communication::where('status', 1)
		->whereDate('end_date', '<', $today)->get();
		
		return $query;
	}

	public  function statusUpdate($data, $where)
	{
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = NULL;
		$update = Communication::where($where)->update($data);
		return $update;
	}
}