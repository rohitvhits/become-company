<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Model\TelehealthLocationScheduleSlot;

class TelehealthLocationScheduleSlotService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		$insert = new TelehealthLocationScheduleSlot($data);
		$insert->save();
		return $insert->id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =TelehealthLocationScheduleSlot::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =TelehealthLocationScheduleSlot::where($where)->update($data); 
		return $update;
	}
	
	public function getData($id){
		$query = TelehealthLocationScheduleSlot::where('location_id',$id)->where('del_flag','N')->orderBy('id','desc')->paginate(50);
		return $query;
	}
	public function getDetailbyId($id){
		$getData = TelehealthLocationScheduleSlot::select('telehealth_location_schedule.id','start_time','end_time','slot','day','telehealth_location_schedule.location_id','telehealth_location_schedule.disable_status')
					->leftjoin('location_master',function($join){
						$join->on('location_master.id','=','telehealth_location_schedule.location_id');
						
					})
					->where('telehealth_location_schedule.id',$id)->where('telehealth_location_schedule.del_flag','N')->first(); 
		return $getData;
	}
	
	public function getSearchLocation($day,$location_id){
		$query = TelehealthLocationScheduleSlot::select('id','start_time','end_time','slot')->where(DB::raw('LOWER(day)'),'=',strtolower($day))->where('location_id',$location_id)->where('del_flag','N')->where('disable_status','N')->get();
		return $query;
		
	}
	public function getDetailbyIdAll($id){ 
		$update =TelehealthLocationScheduleSlot::where('telehealth_location_schedule.id',$id)->where('telehealth_location_schedule.del_flag','N')->first(); 
		return $update;
	}
	public function getAll(){ 
		$update =TelehealthLocationScheduleSlot::where('telehealth_location_schedule.del_flag','N')->get(); 
		return $update;
	}
	
	/**
	 * Get all slots for a specific schedule
	 * @param int $schedule_id The ID of the schedule
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getSlotsByScheduleId($schedule_id) {
		return TelehealthLocationScheduleSlot::where('loc_schedule_id', $schedule_id)
			->where('del_flag', 'N')
			->orderBy('slot_start_time', 'asc')
			->get();
	}
	
	/**
	 * Calculate slot start and end times based on given parameters
	 */

	/**
	 * Generate slots based on start time, end time, and slot duration
	 * @param string $startTime Start time in H:i format
	 * @param string $endTime End time in H:i format
	 * @param int $slotDuration Duration of each slot in minutes
	 * @return array Array of slots with start and end times
	 */
	public function generateSlots($startTime, $endTime, $slotDuration) {
		$slots = [];
		$startTimestamp = strtotime($startTime);
		$endTimestamp = strtotime($endTime);
		$slotDurationSeconds = $slotDuration * 60;
		$currentTime = $startTimestamp;

		while ($currentTime < $endTimestamp) {
			$slotEnd = $currentTime + $slotDurationSeconds;
			
			// If this slot would exceed the end time, use the schedule end time instead
			if ($slotEnd > $endTimestamp) {
				$slotEnd = $endTimestamp;
			}

			$slots[] = [
				'start_time' => date('H:i:s', $currentTime),
				'end_time' => date('H:i:s', $slotEnd)
			];

			$currentTime = $slotEnd;
		}

		return $slots;
	}

	/**
	 * Create slots for a schedule based on start time, end time, and duration
	 * @param int $scheduleId The ID of the schedule
	 * @param string $startTime The start time in H:i format
	 * @param string $endTime The end time in H:i format
	 * @param int $slotDuration The duration of each slot in minutes
	 * @return bool
	 */
	public function createSlots($scheduleId, $startTime, $endTime, $slotDuration) {
		$slots = $this->generateSlots($startTime, $endTime, $slotDuration);
		$auth = auth()->user();

		foreach ($slots as $slot) {
			$data = [
				'loc_schedule_id' => $scheduleId,
				'slot_start_time' => $slot['start_time'],
				'slot_end_time' => $slot['end_time'],
				'created_by' => $auth->id,
				'created_date' => date('Y-m-d H:i:s'),
				'del_flag' => 'N'
			];

			$this->save($data);
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