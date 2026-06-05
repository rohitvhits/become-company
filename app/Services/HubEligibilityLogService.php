<?php
namespace App\Services;

use App\Helpers\Utility;
use App\Model\HubEligibilityLogs;
class HubEligibilityLogService
{

	public function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new HubEligibilityLogs($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
	
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		if(isset($auth['id'])){
			$data['updated_by'] = $auth['id'];
		}
		$update = HubEligibilityLogs::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$update = HubEligibilityLogs::where($where)->update($data);
		return $update;
	}

	public function getAllHubLogs($id,$agencyId)
    {
        $query = HubEligibilityLogs::with(['users', 'importLogs:id,file_name'])
		->where('hub_record_id',$id)->where('agency_id',$agencyId)
		->orderBy('created_at', 'desc')->paginate(10);
        return  $query;
    }
}