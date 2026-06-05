<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Agency;
use App\Services\LocationMasterService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Master;
use App\Services\TelehealthLocationScheduleEventService;
use App\Services\PatientTelehealthScheduleService;
use App\User;
use App\Helpers\Utility;
class DashboardAppointmentCalanderController extends BaseController
{
	protected $AppointmentService, $locationMasterService,$telehealthLocationScheduleEventService,$patientTelehealthScheduleService = '';
	public function __construct(AppointmentService $AppointmentService, LocationMasterService $locationMasterService, TelehealthLocationScheduleEventService $telehealthLocationScheduleEventService, PatientTelehealthScheduleService $patientTelehealthScheduleService )
	{
		$this->AppointmentService = $AppointmentService;
		$this->locationMasterService = $locationMasterService;
		$this->telehealthLocationScheduleEventService = $telehealthLocationScheduleEventService;
		$this->patientTelehealthScheduleService = $patientTelehealthScheduleService;
	}

	public function appointmentDetails(Request $request)
	{
		$appointmentdetails = $this->AppointmentService->getDetailsByIdWithSomeDetails($request->id);

		$fins = array();
		$servicename = '';

		if (isset($appointmentdetails->service_id) && $appointmentdetails->service_id != '') {
			$getServiceName = Master::geServiceName($appointmentdetails->service_id);
			if (count($getServiceName) > 0) {
				foreach ($getServiceName as $vsl) {
					$fins[] = $vsl->name;
				}
			}
			$servicename = implode(',', $fins);
			$appointmentdetails->serviceName = $servicename;
		}
		if(isset($appointmentdetails->appointmentScheduleSlot->start_time) && isset($appointmentdetails->appointmentScheduleSlot->end_time)){
			$appointmentdetails->appointment_slot = date('H:i A',strtotime($appointmentdetails->appointmentScheduleSlot->start_time)).' - '.date('H:i A',strtotime($appointmentdetails->appointmentScheduleSlot->end_time));
		}
		return response()->json(['error_msg' => "", 'status' => 1, 'data' =>$appointmentdetails], 200);
	}


	public function newCalendarDesign()
	{
		$auth = auth()->user();
		if (!in_array($auth['user_type_fk'], array(184))) {
			return redirect("/appointment");
			die(); 
		}
		$data['agencyList'] = Agency::getAgencyList2();

		$data['locationList'] = $this->locationMasterService->getAllList();
		$data['locationId'] = request('locationlist');
		$data['locations'] = $this->locationMasterService->getLocationsData();
		$data['nurse'] = User::getNurses();
		$data['statuses'] = Utility::getUniqueStatusData();
		return view("calender/new_calender_appointment", $data);
	}


	public function getNewAppointmentData(Request $request)
	{
		$auth = auth()->user();
		if (!in_array($auth['user_type_fk'], array(184))) {
			return response()->json(['error' => "Unauthorization", 'data' => []]);
		}
		$date = $request->input('fdate');
		$locId = request('location_id');
		$agency_id = request('agency_id');

		$startOfWeek = $request->startOfWeek;

		$endOfWeek = $request->endOfWeek;
		$type = $request->appointemnt_type;
		$status = $request->status;
		// Fetch appointment details
		
		$appointmentdetails = $this->AppointmentService->getAllAppointment($date, $locId, $agency_id, $startOfWeek, $endOfWeek,$type,$status);
		
		$finalArray = [];
		$weekArray = [];
		$time = [];
		$startDate = Carbon::parse($startOfWeek);
		$endDate = Carbon::parse($endOfWeek);
		$period = CarbonPeriod::create($startDate, '1 day', $endDate);

		foreach ($period as $date) {
			$day = $date->format('Y-m-d');
			$weekArray[] = $day;
			for ($t = strtotime('07:00'); $t <= strtotime('17:59'); $t = strtotime('+15 minutes', $t)) {
				$timeSlot = date('g:i A', $t);
				if (!in_array(date('h:i', strtotime($timeSlot)), $time)) {
					$time[] = date('h:i', strtotime($timeSlot));
				}
				$finalArray[$day][date('h:i', strtotime($timeSlot))] = [];
			}
		}
		
		foreach ($appointmentdetails as $val) {
			if (isset($finalArray[date('Y-m-d', strtotime($val->appointment_date))][date('h:i', strtotime($val->appointment_time))])) {
				$val->appointment_times = date('h:i A',strtotime($val->appointment_time));
				$finalArray[date('Y-m-d', strtotime($val->appointment_date))][date('h:i', strtotime($val->appointment_time))][] = $val;
			}
		}
		$data['week'] = $weekArray;
		$data['finalArray'] = $finalArray;
		$data['time'] = $time;
		
		return response()->json(['success' => true, 'data' => $data]);
    }

	public function getMonthlyAppointmentDetails(Request $request)
	{
		$date = $request->input('fdate');
		$locId = request('location_id');
		$agency_id = request('agency_id');
		$startOfWeek = $request->startOfWeek;
		$endOfWeek = $request->endOfWeek;
		$type = $request->appointemnt_type;
		$status = $request->status;
		// Fetch appointment details
		$appointmentdetails = $this->AppointmentService->getAllAppointment($date, $locId, $agency_id, $startOfWeek, $endOfWeek,$type,$status);
	
		$finalArray = [];
		foreach ($appointmentdetails as $val) {
			$val->appointment_times = date('h:i A',strtotime($val->appointment_time));
			$finalArray[date('Y-m-d', strtotime($val->appointment_date))][] = $val;
		}

		$data['monthlyData'] = $this->getCurrentMonthDatesWithWeekdays($finalArray,$startOfWeek);
		$data['weekDayArray'] = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

		$first_day_of_month = mktime(0, 0, 0, date('m',strtotime($startOfWeek)), 1, date('Y',strtotime($startOfWeek)));
		
		$data['firstWeek'] = (int) date('w', $first_day_of_month);

		$data['previousMonthLastDay'] = (int) date('d', strtotime('-1 day',$first_day_of_month));

		$endOfPrevWeek = date('Y-m-d',strtotime('-1 day',strtotime($request->startOfWeek)));
		$startOfPrevWeek = date('Y-m-01',strtotime($endOfPrevWeek));
		
		$prevAppointmentdetails = $this->AppointmentService->getAllAppointment($date, $locId, $agency_id, $startOfPrevWeek, $endOfPrevWeek,$type,$status);
	
		$finalprevArray = [];
		foreach ($prevAppointmentdetails as $val) {
			$val->appointment_times = date('h:i A',strtotime($val->appointment_time));
			$finalprevArray[date('Y-m-d', strtotime($val->appointment_date))][] = $val;
		}

		$data['previousdata'] = $this->getCurrentMonthDatesWithWeekdays($finalprevArray,$startOfPrevWeek);
		
		return response()->json(['success' => true, 'data' => $data]);		
    }

	public function getCurrentMonthDatesWithWeekdays($finalArray,$startOfWeek) {
		$year = date('Y',strtotime($startOfWeek));
		$month = date('m',strtotime($startOfWeek));
		// $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$numDays = (int) date('t', strtotime("$year-$month-01"));
		$dateArray = [];
		for ($day = 1; $day <= $numDays; $day++) {
			$date = "$year-$month-$day";
			$weekday = date('l', strtotime($date));
			// Add date and weekday to the array
			if(array_key_exists(date('Y-m-d', strtotime($date)),$finalArray)){
				$dateArray[date('Y-m-d', strtotime($date))] = [
					'weekday' => $weekday,
					'data' => $finalArray[date('Y-m-d', strtotime($date))],
					'day' => (int) $day
				];
			}else{
				$dateArray[date('Y-m-d', strtotime($date))] = [
					'weekday' => $weekday,
					'day' => (int) $day
				];
			}	
		}
		return $dateArray;
	}

	public function getTeleAppointmentData(Request $request)
	{
		$auth = auth()->user();
		if (!in_array($auth['user_type_fk'], array(184))) {
			return response()->json(['error' => "Unauthorization", 'data' => []]);
		}
		$date = $request->input('fdate');
		$locId = request('location_id');

		$startOfWeek = $request->startOfWeek;

		$endOfWeek = $request->endOfWeek;
		$type = $request->appointemnt_type;
		$nurse = $request->telehealth_nurse;
		// Fetch appointment details

		$appointmentdetails = $this->AppointmentService->getAllTeleAppointment($locId, $startOfWeek, $endOfWeek,$type,$nurse);

		$finalArray = [];
		$weekArray = [];
		$time = [];
		$startDate = Carbon::parse($startOfWeek);
		$endDate = Carbon::parse($endOfWeek);
		$period = CarbonPeriod::create($startDate, '1 day', $endDate);

		foreach ($period as $date) {
			$day = $date->format('Y-m-d');
			$weekArray[] = $day;
			for ($t = strtotime('08:00'); $t <= strtotime('21:59'); $t = strtotime('+15 minutes', $t)) {
				$timeSlot = date('g:i A', $t);
				if (!in_array(date('h:i', strtotime($timeSlot)), $time)) {
					$time[] = date('h:i', strtotime($timeSlot));
				}
				$finalArray[$day][date('h:i', strtotime($timeSlot))] = [];
			}
		}
		foreach ($appointmentdetails as $val) {
			$appointment = $this->telehealthLocationScheduleEventService->getTeleCalendarData($val->telehealth_time_slot);
			if (!empty($appointment->start_time) && isset($finalArray[date('Y-m-d', strtotime($val->telehealth_date))][date('h:i', strtotime($appointment->start_time))])) {
				$val->appointment_times = date('h:i A',strtotime($appointment->start_time)).' - '.date('h:i A',strtotime($appointment->end_time));
				$finalArray[date('Y-m-d', strtotime($val->telehealth_date))][date('h:i', strtotime($appointment->start_time))][] = $val;
			}
		}
		$data['week'] = $weekArray;
		$data['finalArray'] = $finalArray;
		$data['time'] = $time;
		return response()->json(['success' => true, 'data' => $data]);
    }

	public function getMonthlyTeleAppointmentDetails(Request $request)
	{
		$date = $request->input('fdate');
		$locId = request('location_id');
		$startOfWeek = $request->startOfWeek;
		$endOfWeek = $request->endOfWeek;
		$type = $request->appointemnt_type;
		$nurse = $request->telehealth_nurse;
		// Fetch appointment details
		$appointmentdetails = $this->AppointmentService->getAllTeleAppointment($locId, $startOfWeek, $endOfWeek,$type,$nurse);
		$finalArray = [];
		foreach ($appointmentdetails as $val) {
			$appointment = $this->telehealthLocationScheduleEventService->getTeleCalendarData($val->telehealth_time_slot);
			if(!empty($appointment)){
				$val->appointment_times = date('h:i A',strtotime($appointment->start_time)).' - '.date('h:i A',strtotime($appointment->end_time));
				$finalArray[date('Y-m-d', strtotime($val->telehealth_date))][] = $val;
			}
		}

		$data['monthlyData'] = $this->getCurrentMonthDatesWithWeekdays($finalArray,$startOfWeek);
		$data['weekDayArray'] = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

		$first_day_of_month = mktime(0, 0, 0, date('m',strtotime($startOfWeek)), 1, date('Y',strtotime($startOfWeek)));
		$data['firstWeek'] = (int) date('w', $first_day_of_month);

		$data['previousMonthLastDay'] = (int) date('d', strtotime('-1 day',$first_day_of_month));

		$endOfPrevWeek = date('Y-m-d',strtotime('-1 day',strtotime($request->startOfWeek)));
		$startOfPrevWeek = date('Y-m-01',strtotime($endOfPrevWeek));
		$locId = "";
		$prevAppointmentdetails = $this->AppointmentService->getAllTeleAppointment($locId, $startOfWeek, $endOfWeek,$type,$nurse);

		$finalprevArray = [];
		foreach ($prevAppointmentdetails as $val) {
			$appointment = $this->telehealthLocationScheduleEventService->getTeleCalendarData($val->telehealth_time_slot);
			if(!empty($appointment)){
				$val->appointment_times = date('h:i A',strtotime($appointment->start_time)).' - '.date('h:i A',strtotime($appointment->end_time));
				$finalprevArray[date('Y-m-d', strtotime($val->telehealth_date))][] = $val;
			}
		}

		$data['previousdata'] = $this->getCurrentMonthDatesWithWeekdays($finalprevArray,$startOfPrevWeek);
		return response()->json(['success' => true, 'data' => $data]);		
    }
}
