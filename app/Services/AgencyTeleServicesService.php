<?php
namespace App\Services;
use App\Model\AgencyTeleService;

class AgencyTeleServicesService{

    public function agencyTeleServiceList($agency_id)
	{
		$query = AgencyTeleService::with(['serviceDetails:id,name,types','users:id,first_name,last_name'])->select('agency_tele_service.id','agency_id','type','service_id','agency_tele_service.created_date','service_id','agency_tele_service.created_by');
		$query = $query->where('agency_id',$agency_id)->orderBy('id','desc')->simplePaginate(50);
		return $query;
	}

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
        $insert = new AgencyTeleService($data);
		$insert->save();
		return $insert->id;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = AgencyTeleService::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['del_flag'] = 'Y';
		$update = AgencyTeleService::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = AgencyTeleService::where('id', $id);
		$query = $query->first();
		return $query;
	}
}