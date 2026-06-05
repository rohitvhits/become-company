<?php

namespace App\Services;
use App\Model\ThirdPartyPatientLog;

class ThirdPartyPatientLogService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";

		$insert = new ThirdPartyPatientLog($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}

	public function dataList($search)
	{
		$auth = auth()->user();

		$query = ThirdPartyPatientLog::with(['generateTokenDetails.agencyDetailsByToken:agency_name,id,app_key'])->select('id','patient_id','del_flag','created_date','api_key','url','ip','data','type')->where('del_flag','N');
		if(!empty($search)){
			if(isset($search['agency_id']) && $search['agency_id']){
				$agency_id = $search['agency_id'];
				$query->whereHas('generateTokenDetails.agencyDetailsByToken',function($pQuery) use($agency_id){
					if($agency_id !=""){
						$pQuery->where('id',$agency_id);
					}
				 });
			}
			if(isset($search['created_date']) && $search['created_date']){
				$explode = explode('-', $search['created_date']);
				$query->whereBetween('created_date',[date('Y-m-d H:i:s',strtotime($explode[0].'00:00:00')),date('Y-m-d H:i:s',strtotime($explode[1].'23:59:59'))]);
			}
			
			if(isset($search['type']) && $search['type']){
				$query->where('type','LIKE',"%".$search['type']."%");
			}
		}
		$query = $query->orderBy('id','desc')->paginate(50);
	
		return $query;
	}

	public function getDataById($id)
	{
		$query = ThirdPartyPatientLog::select('id','patient_id','del_flag','created_date','api_key','url','ip','data','type')->where('del_flag','N')->where('id',$id);
		$query = $query->orderBy('id','desc')->first();
		return $query;
	}

	public function getAllType()
	{
		$query = ThirdPartyPatientLog::select('id','type')->where('del_flag','N')->wherenotnull('type')->groupBy('type');
		$query = $query->orderBy('id','desc')->get()->toArray();
		return $query;
	}
}
