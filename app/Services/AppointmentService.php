<?php
namespace App\Services;
use App\Model\Appointment;
use App\Helpers\Utility;
use Illuminate\Support\Facades\DB;

class AppointmentService{

	public static function save($data){ 
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		
		
		$insert = new Appointment($data);
		$insert->save();
		$insert_id =$insert->id;
		return $insert_id;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		if(isset($auth['id'])){
			$data['updated_by'] = $auth['id'];
		}
		
		$update = Appointment::where($where)->update($data);
      	return $update;
	}

    public function getDetailsById($id){
        return Appointment::with('patient')->where('id',$id)->where('del_flag','N')->first();
    }

	public function getDetailsByUniqid($uniqid){
        return Appointment::where('uniqid',$uniqid)->where('del_flag','N')->first();
    }

	public function getAppointmentDetailsById($pid,$serviceId){
        return Appointment::where('patient_id',$pid)->where('patient_service_request_id',$serviceId)->where('del_flag','N')->where('appointment_time', null)->where('appointment_date', null)->where('doctor_id', null)->first();
    }

	public function getAllAppointment($date,$locId,$agency_id,$startOfWeek,$endOfWeek,$type,$status){
		$auth = auth()->user();
		$queryData =  Appointment::select('id','appointment_date','appointment_time','created_at','id','location_id','patient_id','patient_service_request_id','service_id','status')->with('patient:id,first_name,last_name,type,status')->where('del_flag', 'N')->whereNotNull('appointment_date')->whereNotNull('appointment_time')->where(function($q) use ($startOfWeek, $endOfWeek) {
			$q->where('appointment_date', '>=', $startOfWeek . ' 00:00:00')
			->where('appointment_date', '<=', $endOfWeek . ' 23:59:59');
		});
		$agencyids = Utility::getUserWiseAgency();
		
		 if(isset($locId) && $locId != ''){
			 $queryData->where('location_id', $locId);
		 }
		
		 if((isset($agency_id) && $agency_id != '') || (isset($status) && $status != '') || $agencyids){
			 $queryData->whereHas('patient',function($pQuery) use($agency_id,$status,$agencyids,$auth){
				if($auth->record_access !="All"){
					
					$pQuery->where('type',$auth->record_access);
				}
				if($status !=""){
					$pQuery->where('status',$status);
				} 


				if($agency_id !=""){
					$pQuery->where('agency_id',$agency_id);
				}else{
					if(count($agencyids) >0){
						$pQuery->whereIn('agency_id',$agencyids);
					}
				}
			 });
		 }
		 if(isset($type) && $type != ''){
			 $queryData->whereHas('patient',function($pQuery) use($type){
				 $pQuery->where('type',$type);
			 });
		 }
		$queryData->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query = $queryData->groupBy('patient_id')->get();
		return $query;
	 }

	public function getTodayAppointment($agency_id=[]){		
		$query = Appointment::with([
			'patient' => function($query) {
				$query->selectRaw('id,first_name,last_name, middle_name, type, assign_user_id,agency_id,IF(assign_user_id IS NOT NULL, "Assign", "Not Assign") as assign_status');
			},
			'patient.agencyDetail' => function($query) {
				$query->select('id','agency_name');
			}
		])
		->select('id', 'appointment_date','appointment_time', 'status', 'patient_id')
		->where('del_flag', 'N')
		->whereNotNull('appointment_date')
		->whereNotNull('appointment_time')
		->whereDate('appointment_date', date('Y-m-d'));
		if(!empty($agency_id[0])){
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		
		$query = $query->orderBy('created_at','desc')->paginate(50);
		
		return $query;
	}

	public function getUpcommingAppointment($agency_id=array()){		
		$query = Appointment::with([
			'patient' => function($query) {
				$query->selectRaw('id, first_name,last_name, middle_name, type, assign_user_id,agency_id,IF(assign_user_id IS NOT NULL, "Assign", "Not Assign") as assign_status');
			},
			'patient.agencyDetail' => function($query) use ($agency_id) {
				$query->select('id','agency_name');
			}
		])
		->select('id', 'appointment_date','appointment_time', 'status', 'patient_id')
		->where('del_flag', 'N')
		->whereNotNull('appointment_date')
		->whereNotNull('appointment_time')
		->whereDate('appointment_date', '>', date('Y-m-d'));
		if(!empty($agency_id[0])){
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		$query = $query->orderBy('created_at','desc')->paginate(50);
		return $query;
	}

	public function getDetailsByIdWithSomeDetails($id){
        return Appointment::with(['patient:id,first_name,middle_name,last_name,agency_id,status,type','patient.agencyDetail:id,agency_name','location:id,address1,address2,city,state,zip_code','appointmentScheduleSlot:id,start_time,end_time','appointmentPatientScheduleSlot:id,start_time,end_time','appointmentScheduleNurse:id,first_name,last_name'])->select('appointment_date','appointment_time','created_at','service_id','status','patient_id','location_id','telehealth_date','telehealth_time_slot','telehealth_nurse','telehealth_time_frame')->where('id',$id)->where('del_flag','N')->first();
    }

	public function getTodayAppointmentDateWise($from_date,$to_date){


		$query = Appointment::with([
			'patient' => function($query) {
				$query->selectRaw('id,first_name,last_name, middle_name, type, assign_user_id,agency_id,IF(assign_user_id IS NOT NULL, "Assign", "Not Assign") as assign_status');
			},
			'patient.agencyDetail' => function($query) {
				$query->select('id','agency_name');
			}
		])->
		select('id', 'appointment_date','appointment_time', 'status', 'patient_id')
		->where('del_flag', 'N')
		->whereNotNull('appointment_date')
		->whereNotNull('appointment_time');
		if($from_date !="" && $to_date !=""){
			$query->whereDate('appointment_date', '>=',$from_date)->whereDate('appointment_date', '<=',$to_date);

		}else{
		
			$query->whereDate('appointment_date', date('Y-m-d'));
		}
		
		$query = $query->orderBy('created_at','desc')->paginate(50);
		
		return $query;
	}

	public function getUpcommingAppointmentDateWise($from_date,$to_date){		
		$query = Appointment::with([
			'patient' => function($query) {
				$query->selectRaw('id, first_name,last_name, middle_name, type, assign_user_id,agency_id,IF(assign_user_id IS NOT NULL, "Assign", "Not Assign") as assign_status');
			},
			'patient.agencyDetail' => function($query) {
				$query->select('id','agency_name');
			}
		])
		->select('id', 'appointment_date','appointment_time', 'status', 'patient_id')
		->where('del_flag', 'N')
		->whereNotNull('appointment_date')
		->whereNotNull('appointment_time');
		if($from_date !="" && $to_date !=""){
			$query->whereDate('appointment_date', '>=',$from_date)->whereDate('appointment_date', '<=',$to_date);

		}else{
		
			$query->whereDate('appointment_date', date('Y-m-d'));
		}
		
		$query = $query->orderBy('created_at','desc')->paginate(50);
		return $query;
	}

	public function getExistingAppointmentByPatientIds($patientId){
		return Appointment::select('id','patient_id')->where('del_flag','N')->where('patient_id',$patientId)->get();
	}

	public function getCountByTimeScheduleNewWithLocation($startDate,$location_id){
	
		$queryData =  Appointment::with('patient')->where('del_flag', 'N')->whereNotNull('appointment_date')->whereNotNull('appointment_time')->whereDate('appointment_date',date('Y-m-d',strtotime($startDate)));
		$queryData->whereHas('patient');
		 if(isset($location_id) && $location_id != ''){
			 $queryData->whereRaw('location_id ="'.$location_id.'"');
		 }
		
		$query = $queryData->count();
	
		return $query;
	 }

	public function getAllTeleAppointment($locId, $startOfWeek, $endOfWeek,$type,$nurse){
		$auth = auth()->user();
		$agencyids = Utility::getUserWiseAgency();

		$queryData = Appointment::with([
				'patient:id,first_name,last_name,type,status',
				'appointmentScheduleNurse:id,first_name,last_name'
			])
			->select(
				'patient_id',
				'location_id',
				'telehealth_date',
				'telehealth_by',
				'telehealth_time_slot',
				'telehealth_language',
				'telehealth_nurse',
				'telehealth_time_frame',
				'appointment.id as id',
			)
			->where('del_flag', 'N')
			->whereNotNull('telehealth_date')
			->whereNull('appointment_date')
			->where(function($q) use ($startOfWeek, $endOfWeek) {
				$q->where('telehealth_date', '>=', $startOfWeek . ' 00:00:00')
				->where('telehealth_date', '<=', $endOfWeek . ' 23:59:59');
			})
			->whereNull('deleted_at')
			->whereRaw('id = (SELECT MAX(id) FROM appointment AS app WHERE app.patient_id = appointment.patient_id AND `telehealth_date` IS NOT NULL AND `appointment_date` IS NULL)')
			->groupBy('patient_id');

		if (isset($locId) && $locId != '') {
			$queryData->where('location_id', $locId);
		}

		if (!empty($agencyids) && is_array($agencyids) && count($agencyids) > 0) {
			$queryData->whereHas('patient', function ($pQuery) use ($agencyids, $auth) {
				if ($auth->record_access != "All") {
					$pQuery->where('type', $auth->record_access);
				}
				$pQuery->whereIn('agency_id', $agencyids);
			});
		}

		if (isset($type) && $type != '') {
			$queryData->whereHas('patient', function ($pQuery) use ($type) {
				$pQuery->where('type', $type);
			});
		}

		if (isset($nurse) && $nurse != '') {
			$queryData->where('telehealth_nurse', $nurse);
		}
		$queryData->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query = $queryData->orderBy('id', 'desc')->get();

		return $query;
	}
	
	public function getAppointmentFromTelehealth($patient_id){
		$data = Appointment::select('id','telehealth_time_slot')->where('patient_id', $patient_id)->orderBy('id','desc')->first();
		if(isset($data->telehealth_time_slot) && !empty($data->telehealth_time_slot)){
			return 1;
		}else{
			return 0;
		}
	}

	public function getAllTelehealthAppointments($search,$export="") {
		$query = Appointment::with(['patient:id,first_name,middle_name,last_name,agency_id,type','patient.agencyDetail:id,agency_name'])
		->leftjoin('telehealth_location_schedule_events', 'telehealth_location_schedule_events.id', '=', 'appointment.telehealth_time_slot')
		->leftjoin('language as appointment_language', 'appointment_language.id', '=', 'appointment.telehealth_language')
		->select('appointment.created_at','patient_id','telehealth_date','telehealth_time_slot','telehealth_location_schedule_events.start_time','telehealth_location_schedule_events.end_time','appointment_language.name','telehealth_location_schedule_events.nurse_id','appointment.created_by','appointment.telehealth_nurse','appointment.telehealth_time_frame');
		$where = " `appointment`.`del_flag` = 'N'";
		if(!empty($search)){
			if(isset($search['agency_fk']) && $search['agency_fk'] != ''){
				$query->whereHas('patient', function($query) use ($search) {
					$query->whereIn('agency_id', $search['agency_fk']);
				});
			}
			if(isset($search['type']) && $search['type'] != ''){
				$query->whereHas('patient', function($query) use ($search) {
					$query->where('type', $search['type']);
				});
			}
			if(isset($search['appointment_date']) && $search['appointment_date'] != ''){
				$exploderd = explode('-', $search['appointment_date']);
				$where .= ' and DATE_FORMAT(appointment.telehealth_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(appointment.telehealth_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
			}
			if(isset($search['nurse_id']) && $search['nurse_id'] != ''){
				$query->where(function ($q) use ($search) {
					$q->where('telehealth_location_schedule_events.nurse_id', $search['nurse_id'])
					->orWhere('appointment.telehealth_nurse', $search['nurse_id']);
				});
			}
			if(isset($search['language_id']) && $search['language_id'] != ''){
				$query->where('appointment.telehealth_language', $search['language_id']);
			}
			if(isset($search['created_date']) && $search['created_date'] != ''){
				$exploderd = explode('-', $search['created_date']);
				$where .= ' and DATE_FORMAT(appointment.created_at,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(appointment.created_at,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
			}
		}
		$query->where(function ($q) {
			$q->whereNotNull('telehealth_time_slot')
			->orWhereNotNull('telehealth_nurse');
		});
		$query->whereRaw($where);
		$query->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query->orderBy('appointment.created_at','desc');
		if($export){
			return $query->get();
		}
		return $query->paginate(50);

	}

    public function createFromAiCall(array $data)
    {
        return Appointment::create($data);
    }

    public function getPendingByPatientId(int $patientId)
    {
        return Appointment::where('patient_id', $patientId)
            ->whereNull('appointment_time')
            ->whereNull('appointment_date')
            ->whereNull('telehealth_date')
            ->whereNull('doctor_id')
            ->first();
    }
}