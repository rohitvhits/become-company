<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\LocationSchedule;

class LocationScheduleService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new LocationSchedule($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =LocationSchedule::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =LocationSchedule::where($where)->update($data); 
		return $update;
	}
	
	public function getData($id,$data){
		$query = LocationSchedule::where('location_id',$id)->where('del_flag','N');

		// Filter by day
		if(isset($data['day']) && $data['day'] != ""){
			$query->where('day',$data['day']);
		}

		// Filter by start time
		if(isset($data['start_time']) && $data['start_time'] != ""){
			$query->where('start_time', '>=', $data['start_time']);
		}

		// Filter by end time
		if(isset($data['end_time']) && $data['end_time'] != ""){
			$query->where('end_time', '<=', $data['end_time']);
		}

		// Filter by status
		if(isset($data['status']) && $data['status'] != ""){
			$query->where('disable_status', $data['status']);
		}

		$query = $query->orderBy('start_time','asc')->paginate(50);
		return $query;
	}
	public function getDetailbyId($id){
		$update =LocationSchedule::select('location_schedule.*','location_master.link','location_master.address1','location_master.city','location_master.state','location_master.longitude','location_master.latitude','location_master.zip_code','location_master.location_name','location_master.address2')
					->leftjoin('location_master',function($join){
						$join->on('location_master.id','=','location_schedule.location_id');
						
					})
					->where('location_schedule.id',$id)->where('location_schedule.del_flag','N')->first(); 
		return $update;
	}
	
	public function getSearchLocation($day,$location_id){
		$query = LocationSchedule::select('id','start_time','end_time','slot')->where(DB::raw('LOWER(day)'),'=',strtolower($day))->where('location_id',$location_id)->where('del_flag','N')->where('disable_status','N')->get();
		return $query;
		
	}
	public function getDetailbyIdAll($id){ 
		$update =LocationSchedule::where('location_schedule.id',$id)->where('location_schedule.del_flag','N')->first(); 
		return $update;
	}
	public function getAll(){ 
		$update =LocationSchedule::where('location_schedule.del_flag','N')->get(); 
		return $update;
	}
	
	
	
}