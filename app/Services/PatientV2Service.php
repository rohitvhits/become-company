<?php

namespace App\Services;

use App\Model\Patient;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\Utility;

class PatientV2Service
{
	public function getLastUpdatedData($page){
        $offset = ($page - 1) * 10;
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';

			$agencyids = Utility::getUserWiseAgency();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		} else {
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
			$serviceIds = Utility::getServiceByAgencyWithUserAccess($auth->record_access);
			$finalService = '';
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $key=>$srv){
					$or = '';
					if($key !=0){
						$or = ' OR ';
					}
					$finalService .= $or .' FIND_IN_SET("'.$srv.'",patient_master.service_id)';
				}
				$where .= ' and ('.$finalService.')';
			}
		}
		$query = Patient::select('patient_master.*','users.first_name as uname','users.last_name as lname','agency.agency_name')
        ->leftjoin('users',function($join){
			$join->on('users.id','=','patient_master.created_by');
			$join->where('users.delete_flag','N');
		})->leftjoin('agency',function($join){
			$join->on('agency.id','=','patient_master.agency_id');
			$join->where('agency.delete_flag','N');
		})->where('deleted_flag','N')->where('agency.delete_flag','N')
		->whereRaw($where);
		$query = $query->orderBy('patient_master.id','desc')->where('last_status_update', '<', \Carbon\Carbon::now()->subDays(7))->skip($offset)->take(10);
		return $query->get();
	}

	public static function getPatientIds(){
		return Patient::select('patient_master.id','patient_master.mobile','patient_master.phone')
		->join('agency',function($join){
			$join->on('agency.id','=','patient_master.agency_id');
			$join->where('agency.delete_flag','N');
		})->where('agency.is_sms',1)->where('patient_master.deleted_flag','N')->where('patient_master.diciplin','CDPAP')->where('patient_master.type','Caregiver')->whereNotIn('patient_master.agency_id',[366,373,331])->whereNull('patient_master.archived_at')->get();
	}

	/**
	 * Get Patient List for Agency Merge functionality
	 * @param string $agency_fk Agency filter
	 * @param string $first_name Patient name filter
	 * @param string $status Status filter
	 * @param string $mobile Mobile number filter
	 * @param string $created_date Created date filter
	 * @param string $type Type filter (Patient/Caregiver)
	 * @param array $agencyids User's accessible agency IDs
	 * @param int $page Pagination page number
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	public static function getPatientListForAgencyMerge($agency_fk = "", $type = "", $page = 1,$merge_data)
	{
		$auth = auth()->user();
		// Build base query with joins
		$query = Patient::select(
				'patient_master.id',
				'patient_master.first_name',
				'patient_master.middle_name',
				'patient_master.last_name',
				'patient_master.mobile',
				'patient_master.phone',
				'patient_master.status',
				'patient_master.type',
				'patient_master.patient_code',
				'patient_master.created_date',
				'patient_master.agency_id',
				'agency.agency_name',
				'users.first_name as creator_first_name',
				'users.last_name as creator_last_name'
			)
			->leftJoin('users', function($join) {
				$join->on('users.id', '=', 'patient_master.created_by');
				$join->where('users.delete_flag', 'N');
			})
			->leftJoin('agency', function($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
			})
			->where('patient_master.deleted_flag', 'N');

		// Apply additional filters
		if ($agency_fk != '') {
			$query->where('patient_master.agency_id', $agency_fk);
		}

		if ($type != '') {
			$query->where('patient_master.type', $type);
		}
		if(isset($merge_data) && !empty($merge_data)){
			$query->whereNotIn('patient_master.id', $merge_data);
		}
		// Order by latest first
		$query->orderBy('patient_master.id', 'desc');
		// Return paginated results
		return $query->paginate(50, ['*'], 'page', $page);
	}

	public function allPatientListByAgencyId($agencyId,$search,$offset){
		$query= Patient::select('patient_master.id','patient_master.first_name','patient_master.last_name','patient_master.mobile','patient_master.dob','patient_master.type','patient_master.gender','patient_master.created_date')
		->where('deleted_flag','N')->where('agency_id',$agencyId);
		
		$first_name = $search['first_name'] ?? '';
		$last_name  = $search['last_name'] ?? '';

		if(!empty($first_name)){
			$query->where('first_name','LIKE','%'.$first_name.'%');
		}

		if(!empty($last_name)){
			$query->where('last_name','LIKE','%'.$last_name.'%');
		}

		if(!empty($search['mobile'])){
			$mobile = preg_replace('/\D/', '', $search['mobile']);
			$query->where('mobile',$mobile);
		}
		if(!empty($search['gender'])){
			$query->where('gender',$search['gender']);
		}
		if(!empty($search['dob'])){
			$dob = str_replace('-','/',$search['dob']);
			$query->where('dob',date('Y-m-d',strtotime($dob)));
		}
		if(!empty($search['type'])){
			$query->whereRaw('LOWER(type) ="'.strtolower($search['type']).'"');
		}
		$query = $query->orderBy('id', 'desc')->offset($offset)->limit(50)->get();
		return $query;
	}

	public function checkMergeAppointmentIdDelete($id){
		return Patient::select('id','merge_appointment_id')->where('deleted_flag','Y')->where('id',$id)->whereNotNull('deleted_date')->first();
	}

	public function getPatientDetById($id)
	{
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.id !="" ';
		} else {
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " patient_master.type='" . $auth->record_access . "'";
			}
			$where = $addCondition;

			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '") ';
			}

			// $serviceIds = Utility::getServiceByAgency();
			$serviceIds = Utility::getServiceByAgencyWithUserAccess($auth->record_access);

			$finalService = '';
			if (!empty($serviceIds[0])) {
				foreach ($serviceIds as $key => $srv) {
					$or = '';
					if ($key != 0) {
						$or = ' OR ';
					}
					$finalService .= $or . ' FIND_IN_SET("' . $srv . '",patient_master.service_id)';
				}
				$where .= ' and (' . $finalService . ')';
			}
		}

		$query = Patient::with(['languages', 'locations:id,location_name,address1'])->select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_schedule.start_time', 'location_schedule.end_time as edate')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->whereRaw($where)
			->where('patient_master.id', $id)->first();

		return $query;
	}
	
	public function getDeletedPatientData($request){
		$query = Patient::select(
            'patient_master.id as patient_id',
            DB::raw("CONCAT(patient_master.first_name, ' ', patient_master.last_name) as patient_name"),
            'patient_master.created_date',
            'patient_master.created_by',
            'patient_master.deleted_by',
            'patient_master.deleted_date',
            'users.first_name as creator_first_name',
            'users.last_name as creator_last_name',
			'deletedUser.first_name as deleted_first_name',
            'deletedUser.last_name as deleted_last_name',
			'agency.agency_name'
        )
        ->leftJoin('users', 'patient_master.created_by', '=', 'users.id')
        ->leftJoin('users as deletedUser', 'patient_master.deleted_by', '=', 'deletedUser.id')
        
        ->join('agency', 'patient_master.agency_id', '=', 'agency.id')
        ->where('patient_master.deleted_flag', 'Y')
        ->where('agency.delete_flag', 'N')
        ->whereNULL('patient_master.merge_appointment_id');
        // Filter by patient_id if provided
        if (!empty($request['patient_id'])) {
            $query->where('patient_master.id', 'like', '%' . $request['patient_id'] . '%');
        }
		if (!empty($request['agency_fk'])) {
            $query->where('patient_master.agency_id', $request['agency_fk']);
        }
        // Order by created_at desc
        $query->orderBy('patient_master.created_date', 'desc');

        // Paginate results
        return $query->paginate(50); 
	}

	public function getTaskHealthData($id){
		return Patient::where('task_health_link', $id)
                ->where('deleted_flag', 'N')
                ->first();
	}

	public function getLinkHAAPatient($patientId)
    {
        return Patient::where('id', $patientId)->where('deleted_flag', 'N')->whereNotNull('link_hha_patient')->first();
    }

	public function getListByBulkEsign($patientIds, $type = "",$agencyIds = [])
	{
		$query = Patient::whereIn('patient_master.id', $patientIds)->where('patient_master.deleted_flag','N')
			->leftJoin('agency', 'agency.id', '=', 'patient_master.agency_id')
			->select('patient_master.id', 'patient_master.first_name', 'patient_master.last_name', 'patient_master.mobile', 'patient_master.agency_id', 'patient_master.type', 'agency.agency_name');

		if (!empty($type)) {
			$query->whereRaw('LOWER(type) = ?', [strtolower($type)]);
		}

		if(!empty($agencyIds[0])){
			$query->whereIn('agency_id',$agencyIds);
		}
		
		return $query->get()
			->keyBy('id')
			->toArray();
	}
}