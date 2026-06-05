<?php
namespace App\Services;
use App\Model\AgencyNotification;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
class AgencyNotificationService{

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new AgencyNotification($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = AgencyNotification::where($where)->update($data);
		return $update;
	}

    public  function softDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 1;
		$update = AgencyNotification::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = AgencyNotification::where('id', $id);
		$query = $query->first();
		return $query;
	}

	public function getUnreadUserWiseNotification($page)
	{
		$offset = ($page - 1) * 3;
		$auth = auth()->user();
		$query = AgencyNotification::select('notification_user.id as nid','title','notification_user.type','message','is_read','notification_user.record_id','notification_user.created_at','notification_user.created_by','users.first_name as user_first_name','users.last_name as user_last_name','patient_master.id','patient_master.first_name','patient_master.last_name','patient_master.type as patientType','notification_user.sms','notification_user.email')->leftjoin('users',function($join){
			$join->on('users.id','=','notification_user.created_by');
			$join->where('users.delete_flag','N');
		})->leftjoin('patient_master',function($join){
			$join->on('patient_master.id','=','notification_user.record_id');
			$join->where('patient_master.deleted_flag','N')->where('notification_user.record_id','!=','');
		})->where('notification_user.deleted_flag','N')->where('user_id',$auth->id)->where('is_read',0);
		$query = $query->orderBy('notification_user.id','desc')->skip($offset)->take(3);
		return $query->get();
	}

	public function getAgencyActivityFeed($page)
	{
		$offset = ($page - 1) * 10;
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';

			$agencyids = Utility::getUserWiseAgency();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		} else {
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
			$serviceIds = Utility::getServiceByAgencyWithUserAccess($auth->record_access);
			$finalService = '';
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $key=>$srv){
					$or = '';
					if($key !=0){
						$or = ' OR ';
					}
					$finalService .= $or .' FIND_IN_SET("'.$srv.'",patient_master.service_id)';
				}
				$where .= ' and ('.$finalService.')';
			}
		}
		$query = AgencyNotification::select('agency_notification.*','users.first_name as uname','users.last_name as lname','agency.agency_name','patient_master.id as pid','patient_master.first_name as p_first_name','patient_master.last_name as p_last_name','patient_master.type as patientType')
			->leftJoin('users',function($join){
				$join->on('users.id','=','agency_notification.created_by');
				$join->where('users.delete_flag','N');
			})
			->leftjoin('agency',function($join){
				$join->on('agency.id','=','agency_notification.agency_id');
				$join->where('agency.delete_flag','N');
			})
			->leftjoin('patient_master',function($join){
				$join->on('patient_master.id','=','agency_notification.record_id');
				$join->where('patient_master.deleted_flag','N');
			})
			->where(function($q) use ($auth){
				if(isset($auth->id) && ($auth->id) != ''){
					$q->where('user_id', $auth->id);
				}
			})
			->where('agency.delete_flag','N')->orderBy('created_at','desc');
		$query = $query->orderBy('agency_notification.id','desc')->skip($offset)->take(10)->whereRaw($where)->whereIn('record_type',['Appointment','Document','Notes']);
		return $query->get();
	}

	public function getAgencyActivityUserFeed($page)
	{
		$offset = ($page - 1) * 10;
		$auth = auth()->user();
		$query = AgencyNotification::select('agency_notification.*','users.first_name as uname','users.last_name as lname','agency.agency_name','record_user.first_name as runame','record_user.last_name as rlname')
			->leftJoin('users',function($join){
				$join->on('users.id','=','agency_notification.created_by');
				$join->where('users.delete_flag','N');
			})
			->leftjoin('agency',function($join){
				$join->on('agency.id','=','agency_notification.agency_id');
				$join->where('agency.delete_flag','N');
			})
			->leftjoin('users as record_user',function($join){
				$join->on('record_user.id','=','agency_notification.record_id');
				$join->where('users.delete_flag','N');
			})
			->where(function($q) use ($auth){
				if(isset($auth->id) && ($auth->id) != ''){
					$q->where('user_id', $auth->id);
				}
			})
			->where('agency.delete_flag','N')->orderBy('created_at','desc');
		$query = $query->orderBy('agency_notification.id','desc')->whereIn('record_type',['User','Notification Email'])->skip($offset)->take(10);
		return $query->get();
	}
	
	public static function savetoDb($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$insert = new AgencyNotification($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
}