<?php
namespace App\Services;

use App\Helpers\Utility;
use Illuminate\Support\Facades\DB;
use App\Model\TelehealthLocationScheduleEvent;
use App\Model\TelehealthLocationScheduleSlot;
use App\Model\Patient;

class TelehealthLocationScheduleEventService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new TelehealthLocationScheduleEvent($data);
		$insert->save();
		return $insert->id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =TelehealthLocationScheduleEvent::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =TelehealthLocationScheduleEvent::where($where)->update($data); 
		return $update;
	}

	public function getNurseSchedule($locationId, $nurseId, $scheduleId)
	{
		return TelehealthLocationScheduleEvent::where([
			'location_id' => $locationId,
			'nurse_id' => $nurseId,
			'schedule_id' => $scheduleId,
			// 'del_flag' => 'N'
		])
		->select('id', 'slot_id', 'day', 'start_time', 'end_time','del_flag')
		->get();
	}

	public function getTimeSlotsByLanguageAndDay($language, $dayOfWeek,$type)
	{
		$localLanguages = Utility::getLanguageDefultArray(); // 74 = English, 75 = Spanish, 76 = French
		if (!in_array($language, $localLanguages)) {
			$language = Utility::getLanguageDefault();
		}
		return TelehealthLocationScheduleEvent::leftJoin('users', function($join) {
				$join->on('telehealth_location_schedule_events.nurse_id', '=', 'users.id')
					->where('users.delete_flag', '=', 'N');
			})->leftJoin('nurse_language', function($join) {
				$join->on('nurse_language.nurse_id', '=', 'users.id')
					->where('nurse_language.del_flag', '=', 'N');
			})->leftJoin('telehealth_location_schedule', function($join) {
				$join->on('telehealth_location_schedule.id', '=', 'telehealth_location_schedule_events.schedule_id')
					->where('telehealth_location_schedule.del_flag', '=', 'N');
			})
			->where([
				'nurse_language.language_id' => $language,
				'telehealth_location_schedule_events.day' => $dayOfWeek,
				'telehealth_location_schedule_events.del_flag' => 'N',
			])->where('tele_config_type',strtolower($type))
			->select(
				'telehealth_location_schedule_events.id',
				'telehealth_location_schedule_events.start_time',
				'telehealth_location_schedule_events.end_time',
				'telehealth_location_schedule_events.del_flag',
				'telehealth_location_schedule.tele_config_type',
				'telehealth_location_schedule_events.schedule_id',
				'telehealth_location_schedule_events.nurse_id'
			)
			// ->groupBy('telehealth_location_schedule_events.start_time', 'telehealth_location_schedule_events.end_time')
			->orderBy('telehealth_location_schedule_events.start_time', 'asc')
			->get();
	}

	public function checkOverlappingSchedule($locationId, $nurseId, $day, $startTime, $endTime, $excludeScheduleId = null)
	{
		$query = TelehealthLocationScheduleEvent::where([
			// 'location_id' => $locationId,
			'nurse_id' => $nurseId,
			'day' => $day,
			'del_flag' => 'N'
		]);

		// Exclude the current schedule if provided
		if ($excludeScheduleId) {
			$query->where('schedule_id', '!=', $excludeScheduleId);
		}

		// Check for overlapping time slots
		$query->where(function($q) use ($startTime, $endTime) {
			$q->where(function($q) use ($startTime, $endTime) {
				// Case 1: New time slot starts during an existing time slot
				$q->where('start_time', '<=', $startTime)
				  ->where('end_time', '>', $startTime);
			})->orWhere(function($q) use ($startTime, $endTime) {
				// Case 2: New time slot ends during an existing time slot
				$q->where('start_time', '<', $endTime)
				  ->where('end_time', '>=', $endTime);
			})->orWhere(function($q) use ($startTime, $endTime) {
				// Case 3: New time slot completely contains an existing time slot
				$q->where('start_time', '>=', $startTime)
				  ->where('end_time', '<=', $endTime);
			});
		});

		return $query->get();
	}

	public function getPatientExistingAppointment($patientId)
	{
		return TelehealthLocationScheduleEvent::join('patient_master', 'telehealth_location_schedule_events.id', '=', 'patient_master.telehealth_time_slot')
			->where([
				'patient_master.id' => $patientId,
				'patient_master.deleted_flag' => 'N',
			])
			->select(
				'telehealth_location_schedule_events.id',
				'patient_master.telehealth_time_slot',
				'patient_master.telehealth_language as language',
				'patient_master.telehealth_date_time as date'
			)
			->first();
	}

	public function getTelehalthScheduledata($slot_id){
		return TelehealthLocationScheduleEvent::leftjoin('patient_master', 'telehealth_location_schedule_events.id', '=', 'patient_master.telehealth_time_slot')
			->leftjoin('language', 'language.id', '=', 'patient_master.telehealth_language')
			->where([
				'telehealth_location_schedule_events.id' => $slot_id,
				'patient_master.deleted_flag' => 'N'
			])
			->select(
				'telehealth_location_schedule_events.start_time',
				'telehealth_location_schedule_events.end_time',
				'patient_master.telehealth_time_slot',
				'patient_master.telehealth_date_time as date',
				'language.name',
				'nurse_id',
			)
			->first();
	}

	public function getNurseScheduleByDate($nurseId, $date, $dayOfWeek,$location_id,$schedule_id)
	{
		// First get all time slots for the day from TelehealthLocationScheduleSlot
		$allTimeSlots = TelehealthLocationScheduleSlot::where('del_flag', 'N')
			->select('id', 'slot_start_time', 'slot_end_time')
			->orderBy('slot_start_time', 'asc')
			->where('loc_schedule_id', $schedule_id)
			->where('location_id', $location_id)
			->get();

		// Get nurse's assigned slots for the day
		$nurseAssignedSlots = TelehealthLocationScheduleEvent::where([
				'telehealth_location_schedule_events.nurse_id' => $nurseId,
				'telehealth_location_schedule_events.day' => $dayOfWeek,
				'telehealth_location_schedule_events.del_flag' => 'N'
			])
			->select(
				'telehealth_location_schedule_events.id',
				'telehealth_location_schedule_events.schedule_id',
				'telehealth_location_schedule_events.location_id',
				'telehealth_location_schedule_events.slot_id',
				'telehealth_location_schedule_events.start_time',
				'telehealth_location_schedule_events.end_time',
				'telehealth_location_schedule_events.day'
			)
			->get();

		// Create a map of assigned slots for quick lookup
		$assignedSlotsMap = [];
		foreach ($nurseAssignedSlots as $slot) {
			$assignedSlotsMap[$slot->slot_id] = $slot;
		}

		// Combine the data
		$result = [];
		foreach ($allTimeSlots as $slot) {
			$slotData = [
				'slot_id' => $slot->id,
				'start_time' => date('H:i',strtotime($slot->slot_start_time)),
				'end_time' => date('H:i',strtotime($slot->slot_end_time)),
				'start_time_con' => date('h:i A', strtotime($slot->slot_start_time)),
				'end_time_con' => date('h:i A', strtotime($slot->slot_end_time)),
				'is_assigned' => isset($assignedSlotsMap[$slot->id]),
				'day' => $dayOfWeek,
				'schedule_id' => $schedule_id,
				'location_id' => $location_id,
				'event_id' => isset($assignedSlotsMap[$slot->id])?$assignedSlotsMap[$slot->id]->id : '',
			];
			$result[] = $slotData;
		}

		return $result;
	}

	public function updateNurseScheduleByDate($nurseId, $date, $dayOfWeek, $timeSlots)
	{
		try {
			$auth = auth()->user();
			
			// First, get existing schedule for the day
			$existingSchedule = TelehealthLocationScheduleEvent::where([
				'nurse_id' => $nurseId,
				'day' => $dayOfWeek,
				'del_flag' => 'N'
			])->get();

			//Delete existing schedule for the day
			if ($existingSchedule->count() > 0) {
				foreach ($existingSchedule as $schedule) {
					$schedule->update(['del_flag' => 'Y']);
				}
			}

			// Create new schedule entries
			$newSchedules = [];
			foreach ($timeSlots as $slot) {
				if (isset($slot['start_time']) && isset($slot['end_time'])) {
					$insdata = array(
						'nurse_id' => $nurseId,
						'day' => $dayOfWeek,
						'start_time' => $slot['start_time'],
						'end_time' => $slot['end_time'],
						'del_flag' => 'N',
						'created_date' => date('Y-m-d H:i:s'),
						'created_by' => $auth->id,
						'schedule_id' => $slot['schedule_id'] ?? null,
						'slot_id' => $slot['slot_id'] ?? null,
						'location_id' => $slot['location_id'] ?? null
					);
					$newSchedule = $this->save($insdata);
					$newSchedules[] = $newSchedule;
				}
			}

			return [
				'date' => $date,
				'day' => $dayOfWeek,
				'schedules' => $newSchedules
			];

		} catch (\Exception $e) {
			throw new \Exception('Error updating nurse schedule: ' . $e->getMessage());
		}
	}

	public function getDataByScheduleId($schedule_id){
		// First, get existing schedule for the day
		$existingSchedule = TelehealthLocationScheduleEvent::select('id')->where([
			'schedule_id' => $schedule_id,
			'del_flag' => 'N'
		])->first();
		return $existingSchedule;
	}
	
	public function getTelehalthappointemntScheduledata($slot_id){
		return TelehealthLocationScheduleEvent::join('appointment', 'telehealth_location_schedule_events.id', '=', 'appointment.telehealth_time_slot')
			->join('users', 'users.id', '=', 'telehealth_location_schedule_events.nurse_id')
			->leftjoin('language', 'language.id', '=', 'appointment.telehealth_language')
			->where([
				'telehealth_location_schedule_events.id' => $slot_id,
				'appointment.del_flag' => 'N'
			])
			->select(
				'telehealth_location_schedule_events.start_time',
				'telehealth_location_schedule_events.end_time',
				'appointment.telehealth_time_slot',
				'appointment.telehealth_date as date',
				'language.name',
				'users.first_name',
				'users.last_name',
				'nurse_id'
			)
			->first();
	}

	public function getBokkedSlot($language, $dayOfWeek)
	{
		return TelehealthLocationScheduleEvent::join('users', 'telehealth_location_schedule_events.nurse_id', '=', 'users.id')
			->leftJoin('patient_master', function($join) {
				$join->on('telehealth_location_schedule_events.id', '=', 'patient_master.telehealth_time_slot')
					->where('patient_master.deleted_flag', '=', 'N');
			})->leftJoin('nurse_language', function($join) {
				$join->on('nurse_language.nurse_id', '=', 'users.id')
					->where('nurse_language.del_flag', '=', 'N');
			})
			->where([
				'nurse_language.language_id' => $language,
				'telehealth_location_schedule_events.day' => $dayOfWeek,
				'telehealth_location_schedule_events.del_flag' => 'Y'
			])
			->whereNotNull('patient_master.telehealth_time_slot')
			->select(
				'telehealth_location_schedule_events.id',
				'telehealth_location_schedule_events.start_time',
				'telehealth_location_schedule_events.end_time',
				'users.first_name',
				'users.last_name',
				'patient_master.id as patient_id'
			)
			->orderBy('telehealth_location_schedule_events.start_time', 'asc')
			->get();
	}

	public function getScheduleInfo($patient_id){
		return TelehealthLocationScheduleEvent::join('patient_master', 'telehealth_location_schedule_events.id', '=', 'patient_master.telehealth_time_slot')
			->join('users', 'users.id', '=', 'telehealth_location_schedule_events.nurse_id')
			->leftjoin('language', 'language.id', '=', 'patient_master.telehealth_language')
			->where([
				'patient_master.id' => $patient_id,
				'patient_master.deleted_flag' => 'N'
			])
			->select(
				'telehealth_location_schedule_events.start_time',
				'telehealth_location_schedule_events.end_time',
				'patient_master.telehealth_time_slot',
				'patient_master.telehealth_date_time as date',
				'language.name',
				'users.first_name',
				'users.last_name',
				'telehealth_location_schedule_events.nurse_id'
			)
			->first();
	}

	public function getEventsByLocationAndSchedule($locationId, $scheduleId, $nurseId)
	{
		return TelehealthLocationScheduleEvent::where([
			'location_id' => $locationId,
			'nurse_id' => $nurseId,
			'schedule_id' => $scheduleId,
			'del_flag' => 'N'
		])
		->select('id')
		->get()->pluck('id')->toArray();
	}

	public function updateNurseScheduleByDateManual($nurseId, $date, $dayOfWeek, $timeSlots)
	{
		try {
			$auth = auth()->user();
			// First, get existing schedule for the day
			$existingSchedule = TelehealthLocationScheduleEvent::where([
				'nurse_id' => $nurseId,
				'day' => $dayOfWeek,
				'del_flag' => 'N'
			])->get()->pluck('id')->toArray();
			$requestEventIds = array_column($timeSlots,'ids');
			// Find IDs that exist in database but not in request (to be soft deleted)
            $idsToDelete = array_diff($existingSchedule, $requestEventIds);
            if (!empty($idsToDelete)) {
                foreach ($idsToDelete as $id) {
                    $this->softDelete(['del_flag' => 'Y'],['id' => $id]);
                }
            }
			foreach ($timeSlots as $slot) {
				if (isset($slot['start_time']) && isset($slot['end_time'])) {
					if($slot['ids']){
						$this->update(['del_flag' => 'N','deleted_date'=>null,'deleted_by' => null],['id' => $slot['ids']]);
					}elseif(empty($slot['ids'])){
						$insertData = ['nurse_id' => $nurseId,'day' => $dayOfWeek,'schedule_id' => $slot['schedule_id'],'location_id' => $slot['location_id'],'start_time'=>$slot['start_time'],'end_time' => $slot['end_time'],'slot_id' => $slot['slot_id']];
						$this->save($insertData);
					}
				}
			}

			return [
				'date' => $date,
				'day' => $dayOfWeek,
			];

		} catch (\Exception $e) {
			throw new \Exception('Schedule conflict found. The nurse already has an appointment scheduled for this time slot.');
		}
	}

	public function checkOverlappingScheduleNurse($timeSlots)
	{
		if ($this->hasOverlappingTimes($timeSlots)) {
			throw new \Exception('Schedule conflict found. The time slots cannot overlap.');
		}
	}

	private function hasOverlappingTimes($timeSlots)
	{
		// Sort time slots by start time
		usort($timeSlots, function($a, $b) {
			return strtotime($a['start_time']) - strtotime($b['start_time']);
		});

		// Check each time slot against the next one
		for ($i = 0; $i < count($timeSlots) - 1; $i++) {
			$currentSlot = $timeSlots[$i];
			$nextSlot = $timeSlots[$i + 1];

			// Convert times to timestamps for easier comparison
			$currentEnd = strtotime($currentSlot['end_time']);
			$nextStart = strtotime($nextSlot['start_time']);

			// If current slot's end time is after or equal to next slot's start time, there's an overlap
			if ($currentEnd > $nextStart) {
				return true;
			}
		}

		return false;
	}

	public function checkPatientHaveTimeSlot($slot_id,$date){
		return Patient::join('telehealth_location_schedule_events', 'telehealth_location_schedule_events.id', '=', 'patient_master.telehealth_time_slot')->where([
				'patient_master.telehealth_time_slot' => $slot_id,
				'patient_master.deleted_flag' => 'N'
			])->whereRaw('DATE_FORMAT(telehealth_date_time,"%Y-%m-%d") ="' . date('Y-m-d', strtotime($date)) . '"')
			->select(
				'patient_master.id as patient_id','telehealth_location_schedule_events.id'
			)->first();
	}

	public function getTotalBookedSlot($ids,$date)
	{
		return Patient::select('id','telehealth_time_slot')->whereRaw('DATE_FORMAT(telehealth_date_time,"%Y-%m-%d") ="' . date('Y-m-d', strtotime($date)) . '"')
			->whereIn('telehealth_time_slot', $ids)
			->get();
	}

	public function getTeleCalendarData($slot_id){
		return TelehealthLocationScheduleEvent::where([
				'telehealth_location_schedule_events.id' => $slot_id,
			])
			->select(
				'telehealth_location_schedule_events.start_time',
				'telehealth_location_schedule_events.end_time',
			)
			->first();
	}

	public function getNurseSchedulesData($search)
	{
		return TelehealthLocationScheduleEvent::leftJoin('telehealth_location_schedule', function($join) {
				$join->on('telehealth_location_schedule.id', '=', 'telehealth_location_schedule_events.schedule_id')
					->where('telehealth_location_schedule.del_flag', '=', 'N');
			})->where([
			'day' => $search['day'],
			'nurse_id' => $search['nurse'],
			'telehealth_location_schedule_events.del_flag' => 'N',
			'tele_config_type' => $search['type']
		])
		->select('telehealth_location_schedule_events.id', 'telehealth_location_schedule_events.slot_id', 'telehealth_location_schedule_events.day', 'telehealth_location_schedule_events.start_time', 'telehealth_location_schedule_events.end_time','telehealth_location_schedule_events.del_flag')
		->get();
	}

	public function getEventScheduleData($id){
		return TelehealthLocationScheduleEvent::where([
			'schedule_id' => $id,
			'del_flag' => 'N'
		])
		->select('id')
		->get()->pluck('id')->toArray();
	}

	public function getNurseTimeScheduleData(){
		return TelehealthLocationScheduleEvent::pluck('nurse_id', 'id')
			->toArray();
	}
}