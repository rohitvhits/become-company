<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Model\PatientTelehealthSchedule;

class PatientTelehealthScheduleService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new PatientTelehealthSchedule($data);
		$insert->save();
		return $insert->id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =PatientTelehealthSchedule::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =PatientTelehealthSchedule::where($where)->update($data); 
		return $update;
	}

	public function getTelehalthPatientScheduledata($slot_id,$pateint_id){
		return PatientTelehealthSchedule::leftjoin('patient_master', 'patient_telehealth_schedule.id', '=', 'patient_master.telehealth_time_slot')
			->join('users', 'users.id', '=', 'patient_master.telehealth_nurse')
			->where([
				'patient_telehealth_schedule.id' => $slot_id,
				'patient_master.id' => $pateint_id,
				'patient_master.deleted_flag' => 'N'
			])
			->select(
				'patient_telehealth_schedule.start_time',
				'patient_telehealth_schedule.end_time',
				'patient_master.telehealth_time_slot',
				'patient_master.telehealth_date_time as date',
				'users.first_name',
				'users.last_name',
			)
			->first();
	}

	public function getPatientExistingAppointment($patientId)
	{
		return PatientTelehealthSchedule::join('patient_master', 'patient_telehealth_schedule.id', '=', 'patient_master.telehealth_time_slot')
			->where([
				'patient_master.id' => $patientId,
				'patient_master.deleted_flag' => 'N',
			])
			->select(
				'patient_telehealth_schedule.id',
				'patient_master.telehealth_time_slot',
				'patient_master.telehealth_nurse as nurse',
				'patient_master.telehealth_date_time as date'
			)
			->first();
	}

	public function getPateintTeleData($slot_id){
		return PatientTelehealthSchedule::where([
				'patient_telehealth_schedule.id' => $slot_id,
			])
			->select(
				'patient_telehealth_schedule.start_time',
				'patient_telehealth_schedule.end_time',
			)
			->first();
	}

	public function getScheduleInfo($patient_id){
		return PatientTelehealthSchedule::join('patient_master', 'patient_telehealth_schedule.id', '=', 'patient_master.telehealth_time_slot')
			->join('users', 'users.id', '=', 'patient_master.telehealth_nurse')
			->where([
				'patient_master.id' => $patient_id,
				'patient_master.deleted_flag' => 'N'
			])
			->select(
				'patient_telehealth_schedule.start_time',
				'patient_telehealth_schedule.end_time',
				'patient_master.telehealth_time_slot',
				'patient_master.telehealth_date_time as date',
				'users.first_name',
				'users.last_name',
			)
			->first();
	}

	public function getSlot($start_time,$end_time,$day){
		return PatientTelehealthSchedule::where([
				'start_time' => date('H:i:s', strtotime($start_time)),
				'end_time' => date('H:i:s', strtotime($end_time)),
				'day' => $day
			])
			->select('id')
			->first();
	}
}