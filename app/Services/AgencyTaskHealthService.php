<?php

namespace App\Services;

use App\Model\AgencyTaskHealth;

class AgencyTaskHealthService
{

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new AgencyTaskHealth($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = AgencyTaskHealth::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 'N';
		$update = AgencyTaskHealth::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = AgencyTaskHealth::where('id', $id);
		$query = $query->first();
		return $query;
	}

	public static function getAllAgencyList(){
		return AgencyTaskHealth::select('agency.id','agency.agency_name')
		->leftjoin('agency',function($join){
			$join->on('agency.id','=','agency_task_health.agency_id');
		})->where('agency.delete_flag','N')->where('agency_task_health.status',1)->where('agency_task_health.delete_flag','N')->get();
	}
    
	public function changeStatus($id,$status){
		$agencyData = AgencyTaskHealth::where('agency_id',$id)->first();
		if(isset($agencyData) && !empty($agencyData)){
			$updateData = array('status' => $status);
			$this->update($updateData,array('agency_id'=>$id));
		}else{
			$data = array(
					'status' => $status,
					'agency_id' => $id
				);
			$this->save($data,array('agency_id',$id));
		}
	} 
}