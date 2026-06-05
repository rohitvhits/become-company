<?php

namespace App\Services;

use App\Helpers\Utility;
use App\Model\HubDependents;
class HubRecordDependentService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		if(isset($auth['id'])){
			$data['created_by'] = $auth['id'];
		}
		$data['del_flag'] = "N";

		$insert = new HubDependents($data);
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
		

		$update = HubDependents::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = HubDependents::where($where)->update($data);
		return $update;
	}

	public function getDependentData($hub_id)
	{
		$query = HubDependents::select('hub_dependents.dependent_id','hub_record.first_name','hub_record.last_name','hub_record.ssn','hub_record.email','hub_record.mobile','hub_record.phone','hub_record.dob','hub_record.id')
			->leftjoin('hub_record',function($join){
				$join->on('hub_record.id','=','hub_dependents.dependent_id');
			})
			->where('hub_dependents.del_flag','N')->where('hub_record.deleted_flag','N')
					->where('hub_dependents.hub_record_id',$hub_id);
		$query = $query->orderBy('hub_dependents.id', 'desc')->paginate(20);
		return $query;
	}

	public function checkDependentAddOrNot($hub_id,$agency_id,$hub_agency_id,$dependent_id)
	{
		return HubDependents::select('id','agency_id')->where('del_flag', 'N')->where('hub_record_id',$hub_id)->where('agency_id',$agency_id)->where('hub_agency_id',$hub_agency_id)->where('dependent_id',$dependent_id)->first();
	}

	public function getAllDetails($hub_id){
		return HubDependents::with(['agencyDetail:id,agency_name'])->where('hub_record_id',$hub_id)->first();
	}
}