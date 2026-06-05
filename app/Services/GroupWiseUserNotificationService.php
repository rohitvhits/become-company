<?php
namespace App\Services;
use App\Model\GroupWiseUserNotification;

class GroupWiseUserNotificationService{

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new GroupWiseUserNotification($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}

    public function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['delete_flag'] = 'Y';
		$update = GroupWiseUserNotification::where($where)->update($data);
		return $update;
	}
}