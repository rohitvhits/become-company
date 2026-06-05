<?php
namespace App\Services;
use App\Model\AgencyWiseNotifictionEmail;

class AgencyWiseNotificationEmailService
{

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new AgencyWiseNotifictionEmail($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = AgencyWiseNotifictionEmail::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 1;
		$update = AgencyWiseNotifictionEmail::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = AgencyWiseNotifictionEmail::where('id', $id);
		$query = $query->first();
		return $query;
	}
}