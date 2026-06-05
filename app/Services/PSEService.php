<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\PSEMaster;

class PSEService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new PSEMaster($data);
		$insert->save();
		
		return $insert->id;
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =PSEMaster::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =PSEMaster::where($where)->update($data); 
		return $update;
	}
	
	public function AllList(){
		$auth = auth()->user();
		
		$query =PSEMaster::where('delete_flag','N');
			if($auth->user_type_fk ==100){
				$query->where('created_by',$auth->id);
			}
		$mysql = $query->paginate(10); 
		
		return $mysql;
		
	}
	public function getDetailbyId($id){
		$update =PSEMaster::where('id',$id)->where('delete_flag','N')->first(); 
		return $update;
	}
	
	
	public function AllListWithoutPaginate(){ 
		
		$query =PSEMaster::where('delete_flag','N')->get(); 
		
		return $query;
		
	}
	
	public function AllListWithoutPaginateRemoveSelectedId(){ 
		$where = [56,54,52,51,4,58,2,6];
		$query =PSEMaster::where('delete_flag','N')->whereNotIn('id',$where)->get(); 
		
		return $query;
		
	}

	public function getAllList(){
		$auth = auth()->user();
		
		$query =PSEMaster::where('delete_flag','N');
			if($auth->user_type_fk ==100){
				$query->where('created_by',$auth->id);
			}
		$mysql = $query->get(); 
		
		return $mysql;
	}
	
	public function getAgencyWisePatientCaregiverCountData($agency_id,$agency_type_id){
		$query = PSEMaster::select([
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
		return PSEMaster::where('delete_flag','N')->pluck('id');
	}

	public function getAllLocationData(){
		$query =PSEMaster::select('id','zip_code','city')->where('delete_flag','N')->orderBy('id','desc');
		return $query->paginate(50); 
	}
	
	public function getAgencyWisePatientCaregiverCountDataTemp($agency_id,$agency_type_id){
		$query = PSEMaster::select([
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
		$query = PSEMaster::where('delete_flag','N')->select('id','zip_code','city')->orderBy('id','desc')->get();
		return $query;
		
	}

	public function getLocationCities(){
		return PSEMaster::where('delete_flag','N')->pluck('id','address1');
	}

	public function AllListWithoutPaginateSMS(){ 
		
		$query =PSEMaster::where('delete_flag','N')->where('active_status',1)->get(); 
		
		return $query;
		
	}

	public function getLocationData($loaction){
		$query =PSEMaster::select('id','zip_code','city')->where('delete_flag','N');
		if($loaction != ''){
			$query->where('city',$loaction);
		}
		$query = $query->orderBy('id','desc');
		return $query->paginate(50); 
	}

	public function searchLocation(){
        $query =  PSEMaster::select('id','location_name','address1','address2','city','state','zip_code','walkin','latitude','longitude')->where('delete_flag','N');
        return  $query->get();
    }
}