<?php
namespace App\Services;
use App\Model\GroupNotificationMaster;

class GroupNotificationService{

    public function groupNotificationList()
	{
		$auth = auth()->user();

		$query = GroupNotificationMaster::select('group_notification_master.id','group_notification_master.name','patient_flag','caregiver_flag','patients_notification','caregiver_notification','group_notification_master.agency_id','group_notification_master.created_at','group_notification_master.created_by')->with(['services:id,group_id,service_id','users:id,group_id,user_id','userData:id,first_name,last_name','services.servicesDeatils:id,name','users.userDeatils:id,first_name,last_name'])->where('group_notification_master.delete_flag','N');
		
		$query = $query->orderBy('id','desc')->paginate(50);
		return $query;
	}

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new GroupNotificationMaster($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = GroupNotificationMaster::whereId($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['delete_flag'] = 'Y';
		$update = GroupNotificationMaster::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = GroupNotificationMaster::select('group_notification_master.id','group_notification_master.name','patient_flag','caregiver_flag','patients_notification','caregiver_notification','group_notification_master.agency_id','group_notification_master.created_at')->with(['services:id,group_id,service_id','users:id,group_id,user_id'])->where('group_notification_master.delete_flag','N')->where('id', $id);
		$query = $query->first();
		return $query;
	}
}