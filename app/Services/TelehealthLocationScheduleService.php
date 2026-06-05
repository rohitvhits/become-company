<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Model\TelehealthLocationSchedule;

class TelehealthLocationScheduleService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new TelehealthLocationSchedule($data);
		$insert->save();
		return $insert->id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =TelehealthLocationSchedule::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =TelehealthLocationSchedule::where($where)->update($data); 
		return $update;
	}
	
	public function getData(){
		$query = TelehealthLocationSchedule::with('days:id,schedule_id,day')->where('del_flag','N')->orderBy('id','desc')->paginate(50);
		return $query;
	}
	public function getDetailbyId($id){
		$getData = TelehealthLocationSchedule::with('days:id,schedule_id,day')->select('telehealth_location_schedule.id','start_time','end_time','slot','telehealth_location_schedule.location_id','telehealth_location_schedule.disable_status','title','tele_config_type')->leftjoin('location_master',function($join){
			$join->on('location_master.id','=','telehealth_location_schedule.location_id');
		})->where('telehealth_location_schedule.id',$id)->where('telehealth_location_schedule.del_flag','N')->first(); 
		return $getData;
	}

	public function getLocationSchedules($location_id){
		$query = TelehealthLocationSchedule::with('days:id,schedule_id,day')->where('location_id',$location_id)->where('disable_status','N')->where('del_flag','N')->orderBy('id','desc')->get();
		return $query;
	}

	public function getDaysAndEvents($location_id, $schedule_id, $nurse_id)
	{
		$schedule = TelehealthLocationSchedule::with(['days', 'slots'])
			->where('id', $schedule_id)
			->where('location_id', $location_id)
			->where('del_flag', 'N')
			->first();

		if (!$schedule) {
			return [];
		}

		$days_events = [];
		foreach ($schedule->days as $day) {
			$day_name = date('l', strtotime($day->day));
			$slots = [];
			
			foreach ($schedule->slots as $slot) {
				$slots[] = [
					'id' => $slot->id,
					'start_time' => date('H:i', strtotime($slot->slot_start_time)),
					'end_time' => date('H:i', strtotime($slot->slot_end_time)),
					'start_time_con' => date('h:i A', strtotime($slot->slot_start_time)),
					'end_time_con' => date('h:i A', strtotime($slot->slot_end_time))
				];
			}

			$days_events[] = [
				'day' => $day_name,
				'slots' => $slots
			];
		}

		return $days_events;
	}

	public function getSchedule(){
		$query = TelehealthLocationSchedule::where('del_flag', 'N')->where('disable_status','N')->get();
		return $query;
	}
}