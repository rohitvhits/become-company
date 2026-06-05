<?php
namespace App\Services;
use App\Model\UserWiseLocation;


class UserWiseLocationService{

	public function getLocationList($userId,$page){
		return UserWiseLocation::with(['locationDetails:id,location_name','userDetails:id,first_name,last_name','updatedUserDetails:id,first_name,last_name'])->whereHas('locationDetails', function ($q) {
			$q->where('location_master.delete_flag', 'N');
		})->where('user_id',$userId)->where('delete_flag','N')->orderBy('id', 'desc')->paginate(10);
	}

	public function getLocationDetails($id){
		return UserWiseLocation::with('locationDetails')->where('id',$id)->where('delete_flag','N')->first();
	}

	public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();

        $data['deleted_by'] = $auth['id'];

        $update = UserWiseLocation::where($where)->update($data);
        return $update;
    }

	public function getLocationListByUserId($id){
		$query = UserWiseLocation::with('locationDetails:id,location_name')->where('user_id',$id)->where('delete_flag','N')
		->whereHas('locationDetails', function ($q) {
			$q->where('location_master.delete_flag', 'N');
		})->get();

		$finalArray = [];
		if(!empty($query[0])){
			foreach($query as $qty){
				$temp =[];
				$temp['id'] =$qty->locationDetails->id;
				$temp['location_name'] =$qty->locationDetails->location_name;
				$finalArray[] = $temp;
			}
		}

		return $finalArray;
	}

	public function getAllLocationList($userId){
		return UserWiseLocation::with(['locationDetails:id,location_name'])->where('user_id',$userId)->where('delete_flag','N')->groupBy('location_id')->orderBy('id', 'desc')->get();
	}

}
