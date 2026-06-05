<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Model\TelehealthLocationScheduleDay;

class TelehealthLocationScheduleDayService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		$insert = new TelehealthLocationScheduleDay($data);
		$insert->save();
		return $insert->id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =TelehealthLocationScheduleDay::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =TelehealthLocationScheduleDay::where($where)->update($data); 
		return $update;
	}
	
	public function getData($id){
		$query = TelehealthLocationScheduleDay::where('location_id',$id)->where('del_flag','N')->orderBy('id','desc')->paginate(50);
		return $query;
	}
	public function getDetailbyId($id){
		$getData = TelehealthLocationScheduleDay::select('telehealth_location_schedule.id','start_time','end_time','slot','day','telehealth_location_schedule.location_id','telehealth_location_schedule.disable_status')
					->leftjoin('location_master',function($join){
						$join->on('location_master.id','=','telehealth_location_schedule.location_id');
						
					})
					->where('telehealth_location_schedule.id',$id)->where('telehealth_location_schedule.del_flag','N')->first(); 
		return $getData;
	}
	
	public function getSearchLocation($day,$location_id){
		$query = TelehealthLocationScheduleDay::select('id','start_time','end_time','slot')->where(DB::raw('LOWER(day)'),'=',strtolower($day))->where('location_id',$location_id)->where('del_flag','N')->where('disable_status','N')->get();
		return $query;
		
	}
	public function getDetailbyIdAll($id){ 
		$update =TelehealthLocationScheduleDay::where('telehealth_location_schedule.id',$id)->where('telehealth_location_schedule.del_flag','N')->first(); 
		return $update;
	}
	public function getAll(){ 
		$update =TelehealthLocationScheduleDay::where('telehealth_location_schedule.del_flag','N')->get(); 
		return $update;
	}
	
	/**
	 * Get all slots for a specific schedule
	 * @param int $schedule_id The ID of the schedule
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getSlotsByScheduleId($schedule_id) {
		return TelehealthLocationScheduleDay::where('loc_schedule_id', $schedule_id)
			->where('del_flag', 'N')
			->orderBy('slot_start_time', 'asc')
			->get();
	}
	
	/**
	 * Calculate slot start and end times based on given parameters
	 */

	/**
	 * Create slots for a schedule based on start time, end time, and duration
	 * @param int $scheduleId The ID of the schedule
	 * @param string $startTime The start time in H:i format
	 * @param string $endTime The end time in H:i format
	 * @param int $slotDuration The duration of each slot in minutes
	 * @return bool
	 */
	public function createSlots($scheduleId, $startTime, $endTime, $slotDuration) {
		$startTimestamp = strtotime($startTime);
		$endTimestamp = strtotime($endTime);
		$slotDurationSeconds = $slotDuration * 60;
		$currentTime = $startTimestamp;
		$auth = auth()->user();

		while ($currentTime < $endTimestamp) {
			$slotEnd = $currentTime + $slotDurationSeconds;
			
			// If this slot would exceed the end time, use the schedule end time instead
			if ($slotEnd > $endTimestamp) {
				$slotEnd = $endTimestamp;
			}

			$data = [
				'loc_schedule_id' => $scheduleId,
				'slot_start_time' => date('H:i:s', $currentTime),
				'slot_end_time' => date('H:i:s', $slotEnd),
				'created_by' => $auth->id,
				'created_date' => date('Y-m-d H:i:s'),
				'del_flag' => 'N'
			];

			$this->save($data);
			$currentTime = $slotEnd;
		}

		return true;
	}

	/**
	 * Delete all slots for a specific schedule
	 * @param int $scheduleId The ID of the schedule
	 * @return bool
	 */
	public function deleteSlots($scheduleId) {
		return $this->SoftDelete(
			['del_flag' => 'Y'],
			['loc_schedule_id' => $scheduleId]
		);
	}
}