<?php
namespace App\Services;
use App\Model\UserWiseAgency;


class UserWiseAgencyService{

	public function getAgencyList($userId,$page){
		return UserWiseAgency::with(['agencyDetails:id,agency_name','userDetails:id,first_name,last_name','updatedUserDetails:id,first_name,last_name'])->whereHas('agencyDetails', function ($q) {
			$q->where('agency.delete_flag', 'N');
		})->where('user_id',$userId)->where('delete_flag','N')->orderBy('id', 'desc')->paginate(10);
	}

	public function getAgencyDetails($id){
		return UserWiseAgency::with('agencyDetails')->where('id',$id)->where('delete_flag','N')->first();
	}

	public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        
        $data['deleted_by'] = $auth['id'];

        $update = UserWiseAgency::where($where)->update($data);
        return $update;
    }

	public function getAgencyListByUserId($id){
		$query =  UserWiseAgency::with('agencyDetails:id,agency_name,app_name')->where('user_id',$id)->where('delete_flag','N')
		->whereHas('agencyDetails', function ($q) {
			$q->where('agency.delete_flag', 'N');
		})->get();
		
		$finalArray = [];
		if(!empty($query[0])){
			foreach($query as $qty){
				$temp =[];
				$temp['id'] =$qty->agencyDetails->id;
				$temp['agency_name'] =$qty->agencyDetails->agency_name;
				$temp['app_name'] =$qty->agencyDetails->app_name;
				$finalArray[] = $temp;
			}
		}

		return $finalArray;
	}

	
	public function getAllAgencyList($userId){
		return UserWiseAgency::with(['agencyDetails:id,agency_name'])->where('user_id',$userId)->where('delete_flag','N')->groupBy('agency_id')->orderBy('id', 'desc')->get();
	}

	public function getUserWiseAgencyData($agency_id){
		if($agency_id !=""){
			$userWiseAgency = UserWiseAgency::where('user_id',Auth()->user()->id)->whereIn('agency_id',$agency_id)->where('delete_flag','N')->get();
			return $userWiseAgency->pluck('agency_id')->toArray();
		}else{
			return  [];
		}
		
	}

	public function getUserWiseAgencyDataUserId($agency_id,$user_id){
		if($agency_id !=""){
			$userWiseAgency = UserWiseAgency::where('user_id',$user_id)->whereIn('agency_id',$agency_id)->where('delete_flag','N')->get();
			return $userWiseAgency->pluck('agency_id')->toArray();
		}else{
			$userWiseAgency = UserWiseAgency::where('user_id',$user_id)->where('delete_flag','N')->get();
			return $userWiseAgency->pluck('agency_id')->toArray();
		}

	}
		
}