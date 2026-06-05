<?php
namespace App\Services;
use App\Model\RateCard;

class RateCardService{

    public function rateCardList()
	{
		$query = RateCard::with(['serviceDetails:id,name,types','users:id,first_name,last_name'])->select('rate_card.id','amount','rate_card.created_at','service_id','rate_card.created_by');
		$query = $query->where('agency_id', 0)->orderBy('id','desc')->simplePaginate(50);
		return $query;
	}

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new RateCard($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = RateCard::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 'Y';
		$update = RateCard::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = RateCard::where('id', $id);
		$query = $query->first();
		return $query;
	}

    public function getDetailAgencyWise($agency_id)
	{
		$query = RateCard::with(['serviceDetails:id,name,types','users:id,first_name,last_name'])->select('rate_card.id','amount','rate_card.created_at','service_id','rate_card.created_by');
		$query = $query->where('agency_id', $agency_id)->orderBy('id','desc')->simplePaginate(50);
		return $query;
	}

	public function getServicePaymentData($agency_id,$service_id=""){
		$query = RateCard::with(['serviceDetails:id,name'])->select('rate_card.id','amount','service_id','agency_id');
		$query = $query->where(function($query) use ($agency_id) {
			$query->where('agency_id', $agency_id)
				  ->orwhere('agency_id', '=', 0);
		});
		if(!empty($service_id)){
			$query->whereIn('service_id',$service_id);
		}
		return $query->get()->toArray();
	}
}