<?php
namespace App\Services;
use App\Model\NotificationUser;
use Illuminate\Support\Facades\DB;
class NotificationUserService{

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new NotificationUser($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = NotificationUser::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 1;
		$update = NotificationUser::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = NotificationUser::where('id', $id);
		$query = $query->first();
		return $query;
	}

	public function getUserWiseNotification()
	{
		$auth = auth()->user();
		$query = $query = NotificationUser::select('notification_user.id as nid','title','notification_user.type','message','is_read','notification_user.record_id','notification_user.created_at','notification_user.created_by','users.first_name as user_first_name','users.last_name as user_last_name','patient_master.id','patient_master.first_name','patient_master.last_name','patient_master.type as patientType','notification_user.sms','notification_user.email')
		->leftjoin('users',function($join){
			$join->on('users.id','=','notification_user.created_by');
			$join->where('users.delete_flag','N');
		})->leftjoin('patient_master',function($join){
			$join->on('patient_master.id','=','notification_user.record_id');
			$join->where('patient_master.deleted_flag','N')->where('notification_user.record_id','!=','');
		})->where('notification_user.deleted_flag','N')->where('user_id',$auth->id);	
		$query = $query->orderBy('notification_user.id','desc')->simplePaginate(20);
		return $query;
	}

	public function getUnreadUserWiseNotification($page)
	{
		$offset = ($page - 1) * 3;
		$auth = auth()->user();
		$query = NotificationUser::select('notification_user.id as nid','title','notification_user.type','message','is_read','notification_user.record_id','notification_user.created_at','notification_user.created_by','users.first_name as user_first_name','users.last_name as user_last_name','patient_master.id','patient_master.first_name','patient_master.last_name','patient_master.type as patientType','notification_user.sms','notification_user.email')->leftjoin('users',function($join){
			$join->on('users.id','=','notification_user.created_by');
			$join->where('users.delete_flag','N');
		})->leftjoin('patient_master',function($join){
			$join->on('patient_master.id','=','notification_user.record_id');
			$join->where('patient_master.deleted_flag','N')->where('notification_user.record_id','!=','');
		})->where('notification_user.deleted_flag','N')->where('user_id',$auth->id)->where('is_read',0);	
		$query = $query->orderBy('notification_user.id','desc')->skip($offset)->take(3);
		return $query->get();
	}

	public static function savetoDb($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$insert = new NotificationUser($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public function unreadNotificationsCount(){
		$auth = auth()->user();
		$query = DB::select("SELECT id FROM notification_user WHERE deleted_flag = 'N' AND user_id = ? AND is_read = 0 AND created_at BETWEEN '".date('Y-m-d 00:00:00')."' AND '".date('Y-m-d 23:59:59')."' ORDER BY id DESC", [$auth->id]);
		return $query;
	}

	public function unreadNotificationsUserCount(){
		$auth = auth()->user();
		$query = DB::select("SELECT id FROM notification_user WHERE deleted_flag = 'N' AND user_id = ? AND is_read = 0 LIMIT 1", [$auth->id]);
		return $query;
	}
}