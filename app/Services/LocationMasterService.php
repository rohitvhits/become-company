<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\LocationMaster;

class LocationMasterService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new LocationMaster($data);
		$insert->save();
		
		return $insert->id;
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =LocationMaster::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =LocationMaster::where($where)->update($data); 
		return $update;
	}
	
	public function AllList(){
		$auth = auth()->user();
		
		$query =LocationMaster::where('delete_flag','N');
			if($auth->user_type_fk ==100){
				$query->where('created_by',$auth->id);
			}
		$mysql = $query->paginate(10); 
		
		return $mysql;
		
	}
	public function getDetailbyId($id){
		$update =LocationMaster::where('id',$id)->where('delete_flag','N')->first(); 
		return $update;
	}
	
	
	public function AllListWithoutPaginate(){ 
		
		$query =LocationMaster::where('delete_flag','N')->get(); 
		
		return $query;
		
	}
	
	public function AllListWithoutPaginateRemoveSelectedId(){ 
		$where = [56,54,52,51,4,58,2,6];
		$query =LocationMaster::where('delete_flag','N')->whereNotIn('id',$where)->get(); 
		
		return $query;
		
	}

	public function getAllList(){
		$auth = auth()->user();
		
		$query =LocationMaster::where('delete_flag','N');
			if($auth->user_type_fk ==100){
				$query->where('created_by',$auth->id);
			}
		$mysql = $query->get(); 
		
		return $mysql;
	}
	
	public function getAgencyWisePatientCaregiverCountData($agency_id,$agency_type_id){
		$query = LocationMaster::select([
			'id',
			'city',
		])->where('delete_flag','N')->withCount(['caregiver'=>function($q) use($agency_id,$agency_type_id){
			$q->whereIn('agency_id',$agency_id);
			if($agency_type_id != ''){
				$q->where('type',$agency_type_id);
			}
		}])->get();
		return $query;
	}


	public function getLocationIds(){
		return LocationMaster::where('delete_flag','N')->pluck('id');
	}

	public function getAllLocationData(){
		$query =LocationMaster::select('id','zip_code','city')->where('delete_flag','N')->orderBy('id','desc');
		return $query->paginate(50); 
	}
	
	public function getAgencyWisePatientCaregiverCountDataTemp($agency_id,$agency_type_id){
		$query = LocationMaster::select([
			'location_master.id',
			'location_master.city',
			DB::raw('COUNT(DISTINCT caregiver.id) as caregiver_count'),
			DB::raw('COUNT(DISTINCT patient.id) as patient_count')
		])
		->leftJoin('patient_master as caregiver', function($join) use($agency_id, $agency_type_id) {
			$join->on('location_master.id', '=', 'caregiver.location_id')
				->where('caregiver.type', 'Caregiver')
				->where('caregiver.deleted_flag', 'N');
				
			if($agency_id != '') {
				$explode = explode(',', $agency_id);
				$join->whereIn('caregiver.id', $explode);
			}
			
			if($agency_type_id != '') {
				$join->where('caregiver.type', $agency_type_id);
			}
		})
		->leftJoin('patient_master as patient', function($join) use($agency_id, $agency_type_id) {
			$join->on('location_master.id', '=', 'patient.location_id')
				->where('patient.type', 'Patient')
				->where('patient.deleted_flag', 'N');
				
			if($agency_id != '') {
				$explode = explode(',', $agency_id);
				$join->whereIn('patient.id', $explode);
			}
			
			if($agency_type_id != '') {
				$join->where('patient.type', $agency_type_id);
			}
		})
		->groupBy('location_master.id', 'location_master.city')
		->get();
		return $query;
	}

	public function AllListWithoutPaginateFilter(){ 
		$query = LocationMaster::where('delete_flag','N')->select('id','zip_code','city')->orderBy('id','desc')->get();
		return $query;
		
	}

	public function getLocationCities(){
		return LocationMaster::where('delete_flag','N')->pluck('id','address1');
	}

	public function AllListWithoutPaginateSMS(){ 
		
		$query =LocationMaster::where('delete_flag','N')->where('active_status',1)->get(); 
		
		return $query;
		
	}

	public function getLocationData($loaction){
		$query =LocationMaster::select('id','zip_code','city')->where('delete_flag','N');
		if($loaction != ''){
			$query->where('city',$loaction);
		}
		$query = $query->orderBy('id','desc');
		return $query->paginate(50); 
	}

	public function searchLocation(){
        $query =  LocationMaster::select('id','location_name','address1','address2','city','state','zip_code','walkin','latitude','longitude')->where('delete_flag','N');
        return  $query->get();
    }

	public function getDetailbyIds($ids){
		$query = LocationMaster::select('id','location_name')->whereIn('id',$ids)->where('delete_flag','N')->get(); 
		return $query;
	}

	public function getLocationTypeWise($telehealth_config){
		$query = LocationMaster::select('id','location_name','address1')->where('telehealth_config',$telehealth_config)->where('delete_flag','N')->get(); 
		return $query;
	}

	public function getLocationsData(){
        $query =  LocationMaster::select('id','location_name','address1')->whereIn('telehealth_config',['caregiver','patient'])->where('delete_flag','N');
        return  $query->get();
    }

    public function getDropdownList($notInIds = [])
    {
        return LocationMaster::where('delete_flag', 'N')->where('active_status', 1)
		->when(!empty($notInIds), function ($query) use ($notInIds) {
			$query->whereNotIn('id', $notInIds);
		})
		->orderBy('location_name', 'asc')->get(['id', 'location_name']);
    }

    /**
     * Return walk-in locations formatted for AI call dynamic variables.
     * Excludes internal/non-patient-facing locations by ID.
     */
    public function getWalkInList(array $excludeIds = []): array
    {
        return LocationMaster::where('delete_flag', 'N')
			->where('active_status', 1)
            ->where('walkin', 1)
            ->whereNotIn('id', $excludeIds)
            ->get(['id', 'location_name'])
            ->map(fn($l) => ['id' => (int)$l->id, 'name' => (string)$l->location_name])
            ->values()
            ->all();
    }
}