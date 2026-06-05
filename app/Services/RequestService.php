<?php

namespace App\Services;

use App\Helpers\Utility;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\Patient;
use App\Model\ScheduleAppointment;

class RequestService
{

	public static function RequestList()
	{
		$auths = auth()->user();

		$query = ScheduleAppointment::with(['users'])->select('schedule_appointment.*', 'patient_master.id as patient_id', 'patient_master.first_name', 'patient_master.type','patient_master.last_name', 'patient_master.mobile','agency.agency_name', 'ds.full_name', 'location_master.address1', 'location_master.city', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('patient_master', function ($join) {
				$join->on('schedule_appointment.patient_id', '=', 'patient_master.id');
			})
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'schedule_appointment.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'schedule_appointment.location_time_id_slot');
			})
			->where('patient_master.deleted_flag', 'N');

			$agencyids = Utility::getUserWiseAgency();
			if(!empty($agencyids)){
				$query->whereIn('patient_master.agency_id',$agencyids);
			}
			
			if ($auths->agency_fk != '') {
				$query->where('patient_master.agency_id', $auths->agency_fk);
			}
			$query->orderBy('schedule_appointment.id', 'desc');
			$mysql = $query->paginate();
			return $mysql;
	}
}
