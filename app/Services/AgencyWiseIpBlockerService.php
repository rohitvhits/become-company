<?php

namespace App\Services;

use App\Model\AgencyWiseIpBlocker;
class AgencyWiseIpBlockerService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";

		$insert = new AgencyWiseIpBlocker($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if(isset($auth['id'])){
			$data['updated_by'] = $auth['id'];
		}


		$update = AgencyWiseIpBlocker::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = AgencyWiseIpBlocker::where($where)->update($data);
		return $update;
	}

	public function getAgencyWiseIps($agency_id){
		$query = AgencyWiseIpBlocker::where('del_flag','N')->where('agency_id', $agency_id)->get();
		return $query;
	}
}