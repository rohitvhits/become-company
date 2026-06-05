<?php

namespace App\Services;
use App\Model\UserCreatorEmailNotification;

class UserCreatorEmailNotificationService
{
	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";

		$insert = new UserCreatorEmailNotification($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}

    public  function update($data,$where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
	
		return UserCreatorEmailNotification::where($where)->update($data);
	}

	public  function softDelete($data,$where){
		$userId = Auth()->user();
		$data['deleted_date']=date('Y-m-d H:i:s');
		$data['deleted_by']=$userId['id'];
		$update =UserCreatorEmailNotification::where($where)->update($data); 
		return $update;
	}

	public function listUserEmail($agencyId){
		return UserCreatorEmailNotification::select('id','data','created_date','created_by')->with(['createdUserDetails:id,first_name,last_name'])->where('agency_id',$agencyId)->where('del_flag',"N")->paginate(50);
	}

	public function getAddOrNotUserEmailNotification($agencyId,$type){
		$query = UserCreatorEmailNotification::select('data')->where('agency_id',$agencyId)->where('del_flag','N')->get();
		$response = 0;
		if(count($query) >0){
			foreach($query as $vs){
				$explode = explode(',',$vs->data);
				if(in_array($type,$explode)){
					$response =1;
				}
			}
		}

		return $response;
	}
}
