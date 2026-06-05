<?php
namespace App\Services;
use App\Model\Resolution;
use App\Helpers\Utility;

class ResolutionService{

    public function getAllList($patient_id)
	{
		$query = Resolution::select('resolution_log.team','resolution_log.resolution','resolution_log.cancel_reason','resolution_log.refuse_reason','resolution_log.created_at','resolution_log.created_by','cr.name as cancel_reason','rr.name as refuse_reason','users.first_name','users.last_name','resolution_log.notes','resolution_log.other_cancel_reason','resolution_log.other_refuse_reason')->leftjoin('users',function($join){
			$join->on('users.id','=','resolution_log.created_by');
			$join->where('users.delete_flag','N');
		})->leftjoin('master_table as cr',function($join){
			$join->on('cr.id','=','resolution_log.cancel_reason');
		})->leftjoin('master_table as rr',function($join){
			$join->on('rr.id','=','resolution_log.refuse_reason');
		})->where('resolution_log.del_flag','N')->where('patient_id',$patient_id);
		$query = $query->orderBy('resolution_log.id','desc')->paginate(50);
		return $query;
	}

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new Resolution($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = Resolution::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 1;
		$update = Resolution::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = Resolution::where('id', $id);
		$query = $query->first();
		return $query;
	}

	public function getAllListReport($search,$paginate= "")
	{
		$auth = auth()->user();
		$addCondition="";
		if($auth->record_access !='All'){
			$addCondition=" and patient_master.type='".$auth->record_access."'";
		}
		$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';
		$query = Resolution::select('resolution_log.team','resolution_log.resolution','resolution_log.cancel_reason','resolution_log.refuse_reason','resolution_log.created_at','resolution_log.created_by','cr.name as cancel_reason','rr.name as refuse_reason','users.first_name','users.last_name','resolution_log.notes','resolution_log.patient_id','patient_master.first_name as p_fa_name','patient_master.last_name as p_la_name','agency.agency_name','resolution_log.other_cancel_reason','resolution_log.other_refuse_reason')->leftjoin('users',function($join){
			$join->on('users.id','=','resolution_log.created_by');
			$join->where('users.delete_flag','N');
		})->leftjoin('master_table as cr',function($join){
			$join->on('cr.id','=','resolution_log.cancel_reason');
		})->leftjoin('master_table as rr',function($join){
			$join->on('rr.id','=','resolution_log.refuse_reason');
		})->leftjoin('patient_master',function($join){
			$join->on('patient_master.id','=','resolution_log.patient_id');
		})->leftjoin('agency',function($join){
			$join->on('agency.id','=','patient_master.agency_id');
		})->where('resolution_log.del_flag','N');
		if(isset($search['created_date']) && $search['created_date'] != ''){
			$exploderd = explode('-', $search['created_date']);
			$where .= ' and DATE_FORMAT(resolution_log.created_at,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(resolution_log.created_at,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}
		if(isset($search['resolution']) && $search['resolution'] != ''){
			$query->where('resolution_log.resolution', $search['resolution']);
		}
		if(isset($search['team']) && $search['team'] != ''){
			$query->where('resolution_log.team', $search['team']);
		}
		if(isset($search['cancel_reason']) && $search['cancel_reason'] != ''){
			$query->where('resolution_log.cancel_reason', $search['cancel_reason']);
		}
		if(isset($search['refuse_reason']) && $search['refuse_reason'] != ''){
			$query->where('resolution_log.refuse_reason', $search['refuse_reason']);
		}
		if(isset($search['agency_fk']) && $search['agency_fk'] != ''){
			if(isset($search['agency_filter_type']) && trim($search['agency_filter_type']) == 'include'){
				$query->whereIn('patient_master.agency_id', $search['agency_fk']);
			}elseif(isset($search['agency_filter_type']) && trim($search['agency_filter_type']) == 'exclude'){
				$query->whereNotIn('patient_master.agency_id', $search['agency_fk']);
			}
		}
		if(isset($search['assigned_to']) && !empty($search['assigned_to'])){
			$query->whereIn('patient_master.assign_user_id', $search['assigned_to']);
		}
		$query->where('agency.delete_flag', 'N');
		$query->whereRaw($where);
		$query->orderBy('resolution_log.id','desc');
		if(isset($paginate) && !empty($paginate)){
			$query = $query->paginate(50);
		}else{
			$query = $query->get();
		}
		return $query;
	}

	 public static function saveRes($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id']??$data['auto_created_by'];
		$insert = new Resolution($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}

	public static function saveResImportPatientServices($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $data['auto_created_by'];
		$insert = new Resolution($data);
		$insert->save();
		return $insert->id;
	}
}