<?php

namespace App\Services;

use App\Model\Patient;
use App\Model\Language;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use App\Master;
use App\DocumentSentReport;
use App\Model\Resolution;
use App\Model\HubPatient;
use App\Model\PatientServiceRequest;
use Carbon\Carbon;

class PatientService
{
	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";
		$data['last_status_update'] = date('Y-m-d H:i:s');
		$data['last_status_update_by'] = $auth['id'];
		$insert = new Patient($data);
		$insert->save();
		$insertId = $insert->id;



		return $insertId;
	}
	public static function hhasave($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');

		$data['deleted_flag'] = "N";

		$insert = new Patient($data);
		$insert->save();
		$insertId = $insert->id;



		return $insertId;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if (isset($auth['id'])) {
			$data['updated_by'] = $auth['id'];
		}
		$patient = Patient::where($where)->first();
		if (!$patient) {
			return false;
		}
		// 2️⃣ Fill & save (mutators run here)
		$patient->fill($data);
		return $patient->save();
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = Patient::where($where)->update($data);
		return $update;
	}

	public function checkFirstNameLastNameDob($firstName, $lastName, $dateOfBirth)
	{
		return Patient::where('first_name', $firstName)->where('last_name', $lastName)->where('dob', $dateOfBirth)->first();
	}

	public function getTotalCount($statusFlag, $full_name = "", $age = "", $mobile = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $sms_status = "", $record_form = "", $due_date = "", $assign_user_id = "", $is_archive = false, $isPastShow = false, $discipline = "", $patient_code = "", $inservice_date = "", $completed_date = "", $follow_up_date = "", $traning_date = "", $created_by = "", $traning_status = "", $transistion_aid = "", $language_id = "", $dob = "", $last_status_update = "", $last_status_updated_by_id = "",$agency_filter_type = "", $service_filter_type = "", $medication_list = "", $insurance_elg ="", $mdo_tag = "",$filter_branch_id = "",$branch_filter_type="",$state = "",$agency_status="",$allStatusIds=[],$referral_type="",$agency_updated_by_id="",$record_read="",$is_reviewed="",$agency_enable_review=0)
	{

		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}

			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';

			$agencyids = Utility::getUserWiseAgency();
			if (!empty($agencyids)) {

				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
			//and patient_master.archived_at IS NULL
		} else {
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';

			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}

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

		if ($is_archive == "true") {

			$where .= ' and patient_master.is_archive = 1';
		} else {

			$where .= ' and patient_master.is_archive  = 0';
		}

		if ($auth->agency_fk != "" && $agency_enable_review) {
			if ($is_reviewed == "true") {
				$where .= ' and patient_master.is_reviewed = 1';
			} else {
				$where .= ' and patient_master.is_reviewed = 0';
			}
		}

		if ($full_name != '') {

			$where .= ' and patient_master.full_name LIKE "%' . $full_name . '%"';
		}
		if ($mobile != '') {
			$where .= ' and patient_master.mobile = "' . $mobile . '"';
		}

		if ($type != '') {
			$where .= ' and patient_master.type = "' . $type . '"';
		}
		if ($sms_status != '') {
			$sms_status = str_replace(',', '","', $sms_status);
			$where .= ' and patient_master.patient_sms_flag IN( "' . $sms_status . '")';
		}
		if ($doctor_id != '') {
			$where .= ' and patient_master.doctor_id = "' . $doctor_id . '"';
		}

		if (!empty($service_id)) {
			$explode = explode(',', $service_id);
			$final = '';

			if (isset($service_filter_type) && $service_filter_type == 'exclude') {
				// Exclude selected service IDs
				foreach ($explode as $key => $vals) {
					$or = '';
					if ($key != 0) {
						$or = ' and ';
					}
					$final .= $or . ' !FIND_IN_SET("' . $vals . '",patient_master.service_id)';
				}
				$where .= ' and (' . $final . ')';
			} else {
				// Include selected service IDs
				foreach ($explode as $key => $vals) {
					$or = '';
					if ($key != 0) {
						$or = ' OR ';
					}
					$final .= $or . ' FIND_IN_SET("' . $vals . '",patient_master.service_id)';
				}
				$where .= ' and (' . $final . ')';
			}
		}
		if ($location_id != '') {
			$location_id = str_replace(',', '","', $location_id);
			$where .= ' and patient_master.location_id IN( "' . $location_id . '")';
		}
		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);
			if (isset($explode[1])) {
				$where .= ' AND ( (DATE_FORMAT(patient_master.appointment_date, "%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" AND DATE_FORMAT(patient_master.appointment_date, "%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '") OR (DATE_FORMAT(patient_master.telehealth_date_time, "%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" AND DATE_FORMAT(patient_master.telehealth_date_time, "%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"))';
			} else {
				$where .= ' AND ( DATE_FORMAT(patient_master.appointment_date, "%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '" OR DATE_FORMAT(patient_master.telehealth_date_time, "%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '")';
			}
		}
		if ($status != '') {
			
			$explode = explode(',', $status);
			$final = [];
			foreach ($explode as $vsl) {
				if ($vsl == 'Signed-SentBacktotheAgency') {
					$vsl = 'Signed & Sent Back to the Agency';
				}
				if ($vsl == 'TelehealthCompleted-Pending Forms') {
					$vsl = 'Telehealth Completed , Pending Forms';
				}
				if ($vsl == 'PatientAskedtoReschedule') {
					$vsl = 'Patient Asked to Reschedule';
				}
				$final[] = $vsl;
			}
			$quoted = array_map(function ($item) {
				return '"' . addslashes($item) . '"';
			}, $final);

			// Build the WHERE clause
			$where .= ' AND LOWER(patient_master.status) IN (' . implode(',', $quoted) . ')';
		}
		if ($age != '') {

			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}
		if ($assign_user_id != '') {
			$assign_user_id = str_replace(',', '","', $assign_user_id);
			$where .= ' and patient_master.assign_user_id IN( "' . $assign_user_id . '")';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($due_date != '') {
			$exploderd = explode('-', $due_date);
			if (isset($exploderd[1])) {
				$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
			} else {
				$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '"';
			}
		}

		if ($agency_fk != '') {
			$agency_fk = str_replace(',', '","', $agency_fk);
			if(isset($agency_filter_type) && $agency_filter_type == 'include') {
				$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
			}elseif(isset($agency_filter_type) && $agency_filter_type == 'exclude') {
				$where .= ' and patient_master.agency_id NOT IN( "' . $agency_fk . '")';
			}
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if ($patient_code != "") {
			$where .= ' and patient_master.patient_code ="' . $patient_code . '"';
		}

		if ($record_form != '') {
			if ($record_form == 1) {
				$where .= ' and patient_master.record_id IS NOT NULL';
			} else {
				$where .= ' and patient_master.record_id IS NULL';
			}
		}

		if ($inservice_date != '') {
			$exploderd = explode('-', $inservice_date);
			$where .= ' and DATE_FORMAT(patient_master.inservice_datetime,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.inservice_datetime,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}
		if ($isPastShow == "true") {
			$where .= ' and patient_master.appointment_date < "' . now() . '"';
		}
		if ($discipline != "") {
			$where .= ' and patient_master.diciplin = "' . $discipline . '"';
		}
		if ($completed_date != '') {
			$exploderd = explode('-', $completed_date);
			$where .= ' and DATE_FORMAT(patient_master.completed_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.completed_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($follow_up_date != '') {
			$exploderd = explode('-', $follow_up_date);
			$where .= ' and DATE_FORMAT(patient_master.follow_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.follow_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}
		if ($traning_date != '') {
			$exploderd = explode('-', $traning_date);
			$where .= ' and DATE_FORMAT(patient_master.traning_due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.traning_due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if (!empty($created_by) && $created_by != 'undefined') {
			$where .= ' and patient_master.created_by = ' . $created_by;
		}

		if ($traning_status != "") {
			$traning_status = str_replace(',', '","', $traning_status);

			$where .= ' and patient_master.training_status IN( "' . $traning_status . '")';
		}
		if ($transistion_aid != "") {
			if ($transistion_aid != 0) {
				$where .= ' and patient_master.transition_aid = "' . $transistion_aid . '"';
			} else {
				$where .= ' and (patient_master.transition_aid = "' . $transistion_aid . '" OR patient_master.transition_aid IS NULL)';
			}
		}

		if ($language_id != "") {
			$where .= ' and patient_master.language = "' . $language_id . '" ';
		}
		if ($dob != '') {
			$where .= ' and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($dob)) . '"';
		}

		if ($last_status_update != '') {
			$explode = explode('-', $last_status_update);
			if (isset($explode[1])) {
				$where .= ' and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
			} else {
				$where .= ' and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"';
			}
		}

		if ($last_status_updated_by_id != "") {
			$where .= ' and patient_master.last_status_update_by ="' . $last_status_updated_by_id . '"';
		}

		if($auth->restrict_user==1){
			$where .=' and patient_master.created_by ='.$auth->id;
		}
		// $query = Patient::select('patient_master.id')
		$query = Patient::leftjoin('agency', function ($join) {
			$join->on('agency.id', '=', 'patient_master.agency_id');
		})->select(DB::raw('count(patient_master.id) as count'))
			->whereRaw($where)->where('agency.delete_flag', 'N');
		if (!empty($medication_list)) {
			if ($medication_list == 'Yes') {
				$query->where(function($q) {
					$q->where('medication_count', '>=', 1)->orWhere('no_medication_taken', 1);
				});
			} else {
				$query->where('medication_count', '=', 0)->where(function($q) {
					$q->where('no_medication_taken', '!=', 1)->orWhereNull('no_medication_taken');
				});
			}
		}

		if (!empty($insurance_elg)) {
			if ($insurance_elg == 'Yes') {
				$query->where('insurance_elg_count','>=',1);
			} else {
				$query->where('insurance_elg_count','=',0);
			}
		}

		if (!empty($mdo_tag)) {
			if ($mdo_tag == 'Yes') {
				$query->where('mdo_tag_count','>=',1);
			} else {
				$query->where('mdo_tag_count','=',0);
			}
		}

		if (!empty($filter_branch_id)) {
			if(isset($branch_filter_type) && $branch_filter_type == 'include'){
				$query->where('branch_id',$filter_branch_id);
			}elseif(isset($branch_filter_type) && $branch_filter_type == 'exclude'){
				$query->where(function($q) use ($filter_branch_id) {
					$q->where('branch_id','!=',$filter_branch_id)
					->orWhere('branch_id',0)
					->orWhere('branch_id',NULL);
				});
			}
		}

		if(!empty($state)){
			$query->where('patient_master.state','=',$state);
		}
		if (!empty($agency_status)) {
			$query->whereExists(function ($subQuery) use ($agency_status,$allStatusIds) {
				$subQuery->select(DB::raw(1))
					->from('patient_custom_data_submit as pcds')
					->whereColumn('pcds.patient_id', 'patient_master.id')
					->whereNull('deleted_at')
					->whereIn('field_id',$allStatusIds)
					->where('pcds.value', $agency_status);
			});
		}
		if (!empty($referral_type)) {
			$query->where('patient_master.referral_type', '=', $referral_type);
		}

		if (!empty($agency_updated_by_id)) {
			$query->where('agency_user_id',$agency_updated_by_id);
		}

		if(isset($record_read) && $record_read == 0){
			$query->where('patient_master.record_read','=',0);
		}
		return $query->get();
	}


	public function getData($statusFlag, $full_name = "", $age = "", $mobile = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $sms_status = "", $record_form = "", $due_date = "", $assign_user_id = "", $is_archive = false, $isPastShow = false, $discipline = "", $patient_code = "", $inservice_date = "", $completed_date = "", $follow_up_date = "", $traning_date = "", $created_by = "", $debug = "", $traning_status = "", $transistion_aid = "", $language_id = "", $dob = "", $last_status_update = "", $last_status_updated_by_id = "",$agency_filter_type="",$service_filter_type= "", $medication_list = "", $insurance_elg = "", $mdo_tag = "", $filter_branch_id = "",$branch_filter_type="",$state="",$agency_status="",$allStatusIds = [],$referral_type="",$agency_updated_by_id="",$record_read = "",$is_reviewed = "",$agency_enable_review=0)
	{

		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgency();
			if (!empty($agencyids)) {

				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where .= $addCondition;
			//and patient_master.archived_at IS NULL
		} else {
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';

			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
			
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

		if ($is_archive == "true") {

			$where .= ' and patient_master.is_archive = 1';
		} else {

			$where .= ' and patient_master.is_archive = 0';
		}

		if ($auth->agency_fk != "" && $agency_enable_review) {
			if ($is_reviewed == "true") {
				$where .= ' and patient_master.is_reviewed = 1';
			} else {
				$where .= ' and patient_master.is_reviewed = 0';
			}
		}

		if ($full_name != '') {

			$where .= ' and patient_master.full_name LIKE "%' . $full_name . '%"';
		}
		if ($mobile != '') {
			$where .= ' and patient_master.mobile = "' . $mobile . '"';
		}

		if ($type != '') {
			$where .= ' and patient_master.type = "' . $type . '"';
		}
		if ($sms_status != '') {
			$sms_status = str_replace(',', '","', $sms_status);
			$where .= ' and patient_master.patient_sms_flag IN( "' . $sms_status . '")';
		}
		if ($doctor_id != '') {
			$where .= ' and patient_master.doctor_id = "' . $doctor_id . '"';
		}
		if (!empty($service_id)) {
			$explode = explode(',', $service_id);
			$final = '';

			if (isset($service_filter_type) && $service_filter_type == 'exclude') {
				// Exclude selected service IDs
				foreach ($explode as $key => $vals) {
					$or = '';
					if ($key != 0) {
						$or = ' and ';
					}
					$final .= $or . ' !FIND_IN_SET("' . $vals . '",patient_master.service_id)';
				}
				$where .= ' and (' . $final . ')';
			} else {
				// Include selected service IDs
				foreach ($explode as $key => $vals) {
					$or = '';
					if ($key != 0) {
						$or = ' OR ';
					}
					$final .= $or . ' FIND_IN_SET("' . $vals . '",patient_master.service_id)';
				}
				$where .= ' and (' . $final . ')';
			}
		}

		if ($location_id != '') {
			$location_id = str_replace(',', '","', $location_id);
			$where .= ' and patient_master.location_id IN( "' . $location_id . '")';
		}

		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);
			if (isset($explode[1])) {
				$where .= ' and ((DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0]))
					. '" and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
				$where .= ' OR DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"))';
			} else {
				$where .= ' and ((DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"';
				$where .= ' OR DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"))';
			}
		}
		if ($status != '') {
			
			$explode = explode(',', $status);
			$final = [];
			foreach ($explode as $vsl) {
				if ($vsl == 'Signed-SentBacktotheAgency') {
					$vsl = 'Signed & Sent Back to the Agency';
				}
				if ($vsl == 'TelehealthCompleted-Pending Forms') {
					$vsl = 'Telehealth Completed , Pending Forms';
				}
				if ($vsl == 'PatientAskedtoReschedule') {
					$vsl = 'Patient Asked to Reschedule';
				}
				$final[] = $vsl;
			}
			$quoted = array_map(function ($item) {
				return '"' . addslashes($item) . '"';
			}, $final);

			// Build the WHERE clause
			$where .= ' AND LOWER(patient_master.status) IN (' . implode(',', $quoted) . ')';
		}

		if ($patient_code != "") {
			$where .= ' and patient_master.patient_code ="' . $patient_code . '"';
		}

		if ($age != '') {

			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}
		if ($assign_user_id != '') {
			$assign_user_id = str_replace(',', '","', $assign_user_id);
			$where .= ' and patient_master.assign_user_id IN( "' . $assign_user_id . '")';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($due_date != '') {
			$exploderd = explode('-', $due_date);
			if (isset($exploderd[1])) {
				$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
			} else {
				$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($exploderd[0])) . '"';
			}
		}

		if ($inservice_date != '') {
			$exploderd = explode('-', $inservice_date);
			$where .= ' and DATE_FORMAT(patient_master.inservice_datetime,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.inservice_datetime,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($agency_fk != '') {
			$agency_fk = str_replace(',', '","', $agency_fk);
			if(isset($agency_filter_type) && $agency_filter_type == 'include'){
				$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
			}elseif(isset($agency_filter_type) && $agency_filter_type == 'exclude'){
				$where .= ' and patient_master.agency_id NOT IN( "' . $agency_fk . '")';
			}
		}

		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if ($record_form != '') {
			if ($record_form == 1) {
				$where .= ' and patient_master.record_id !=""';
			} else {
				$where .= ' and patient_master.record_id IS NULL';
			}
		}

		if ($isPastShow == "true") {
			$where .= ' and patient_master.appointment_date < "' . now() . '"';
		}
		if ($discipline != "") {
			$where .= ' and patient_master.diciplin = "' . $discipline . '"';
		}
		if ($completed_date != '') {
			$exploderd = explode('-', $completed_date);
			$where .= ' and DATE_FORMAT(patient_master.completed_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.completed_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($follow_up_date != '') {
			$exploderd = explode('-', $follow_up_date);
			$where .= ' and DATE_FORMAT(patient_master.follow_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.follow_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($traning_date != '') {
			$exploderd = explode('-', $traning_date);
			$where .= ' and DATE_FORMAT(patient_master.traning_due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.traning_due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if (!empty($created_by) && $created_by != 'undefined') {
			$where .= ' and patient_master.created_by = ' . $created_by;
		}

		if ($debug == 1) {
			echo $agency_fk;
			echo $where;
			die();
		}

		if ($traning_status != "") {
			$traning_status = str_replace(',', '","', $traning_status);

			$where .= ' and patient_master.training_status IN( "' . $traning_status . '")';
		}

		if ($transistion_aid != "") {
			if ($transistion_aid != 0) {
				$where .= ' and patient_master.transition_aid = "' . $transistion_aid . '"';
			} else {
				$where .= ' and (patient_master.transition_aid = "' . $transistion_aid . '" OR patient_master.transition_aid IS NULL)';
			}
		}

		if ($language_id != "") {
			$where .= ' and patient_master.language = "' . $language_id . '" ';
		}

		if ($dob != '') {
			$where .= ' and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($dob)) . '" ';
		}

		if ($last_status_update != '') {
			$explode = explode('-', $last_status_update);
			if (isset($explode[1])) {
				$where .= ' and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
			} else {
				$where .= ' and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"';
			}
		}

		if ($last_status_updated_by_id != "") {
			$where .= ' and patient_master.last_status_update_by ="' . $last_status_updated_by_id . '"';
		}

		if($auth->restrict_user==1){
			$where .=' and patient_master.created_by ='.$auth->id;
		}

		$query = Patient::with(['assignToUser', 'statusUpdatedUsers'])->select('patient_master.*', 'agency.agency_name', 'users.first_name as uFname', 'users.last_name as uLname')->leftjoin('agency', function ($join) {
			$join->on('agency.id', '=', 'patient_master.agency_id');
			$join->where('agency.delete_flag', 'N');
		})->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'patient_master.created_by');
		});

		if (!empty($agency_updated_by_id)) {
			$query->where('agency_user_id',$agency_updated_by_id);
		}
		if (!empty($medication_list)) {
			if ($medication_list == 'Yes') {
				$query->where(function($q) {
					$q->where('patient_master.medication_count', '>=', 1)->orWhere('patient_master.no_medication_taken', 1);
				});
			} else {
				$query->where('patient_master.medication_count', '=', 0)->where(function($q) {
					$q->where('patient_master.no_medication_taken', '!=', 1)->orWhereNull('patient_master.no_medication_taken');
				});
			}
		}

		if (!empty($insurance_elg)) {
			if ($insurance_elg == 'Yes') {
				$query->where('insurance_elg_count','>=',1);
			} else {
				$query->where('insurance_elg_count','=',0);
			}
		}

		if (!empty($mdo_tag)) {
			if ($mdo_tag == 'Yes') {
				$query->where('mdo_tag_count','>=',1);
			} else {
				$query->where('mdo_tag_count','=',0);
			}
		}
		if (!empty($filter_branch_id)) {
			if(isset($branch_filter_type) && $branch_filter_type == 'include'){
				$query->where('branch_id',$filter_branch_id);
			}elseif(isset($branch_filter_type) && $branch_filter_type == 'exclude'){
				$query->where(function($q) use ($filter_branch_id) {
					$q->where('branch_id','!=',$filter_branch_id)
					->orWhere('branch_id',0)
					->orWhere('branch_id',NULL);
				});
			}
		}
		if(!empty($state)){
			$query->where('patient_master.state','=',$state);
		}
		if (!empty($agency_status)) {
			$query->whereExists(function ($subQuery) use ($agency_status,$allStatusIds) {
				$subQuery->select(DB::raw(1))
					->from('patient_custom_data_submit as pcds')
					->whereColumn('pcds.patient_id', 'patient_master.id')
					->whereNull('deleted_at')
					->whereIn('field_id',$allStatusIds)
					->where('pcds.value', $agency_status);
			});
		}
		if (!empty($referral_type)) {
			$query->where('patient_master.referral_type', '=', $referral_type);
		}
		if(isset($record_read) && $record_read == 0){
			$query->where('patient_master.record_read','=',0);
		}
		$query = $query->whereRaw($where)->where('agency.delete_flag','N')->orderBy('patient_master.id', 'desc')->simplePaginate(20);

		return $query;
	}

	public function getDataCopy($statusFlag, $full_name = "", $age = "", $mobile = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $sms_status = "", $record_form = "", $due_date = "", $assign_user_id = "", $is_archive = false, $isPastShow = false, $discipline = "")
	{

		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			//and patient_master.archived_at IS NULL
		} else {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '" and patient_master.archived_at IS NULL';
		}
		if ($is_archive == "true") {
			// dd($is_archive);
			$where .= ' and patient_master.archived_at IS NOT NULL';
		}


		if ($statusFlag == 'refused') {

			$where .= ' and patient_master.status ="refused"';
		} else if ($statusFlag == 'cancel') {
			$where .= ' and patient_master.status ="cancelled"';
		} else {
			if ($statusFlag != '') {
				$where .= ' and patient_master.status !="refused" and patient_master.status !="cancelled"';
			}
		}
		if ($full_name != '') {

			$where .= ' and CONCAT_WS("",patient_master.first_name," ",patient_master.last_name) LIKE "%' . $full_name . '%"';
		}
		if ($mobile != '') {
			$where .= ' and patient_master.mobile = "' . $mobile . '"';
		}

		if ($type != '') {
			$where .= ' and patient_master.type = "' . $type . '"';
		}
		if ($sms_status != '') {
			$sms_status = str_replace(',', '","', $sms_status);
			$where .= ' and patient_master.patient_sms_flag IN( "' . $sms_status . '")';
		}
		if ($doctor_id != '') {
			$where .= ' and patient_master.doctor_id = "' . $doctor_id . '"';
		}
		if ($service_id != '') {
			$where .= ' and patient_master.service_id ="' . $service_id . '"';
		}
		if ($location_id != '') {
			$location_id = str_replace(',', '","', $location_id);
			$where .= ' and patient_master.location_id IN( "' . $location_id . '")';
		}
		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);
			$where .= ' and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}
		if ($status != '') {
			$status = str_replace(',', '","', $status);
			$stats = strtolower($status);
			$where .= ' and LOWER(patient_master.status) IN( "' . $stats . '")';
		}
		if ($age != '') {

			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}
		if ($assign_user_id != '') {
			$assign_user_id = str_replace(',', '","', $assign_user_id);
			$where .= ' and patient_master.assign_user_id IN( "' . $assign_user_id . '")';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($due_date != '') {
			$exploderd = explode('-', $due_date);
			$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($agency_fk != '') {
			$agency_fk = str_replace(',', '","', $agency_fk);
			$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if ($record_form != '') {
			if ($record_form == 1) {
				$where .= ' and patient_master.record_id !=""';
			} else {
				$where .= ' and patient_master.record_id IS NULL';
			}
		}

		if ($isPastShow == "true") {
			$where .= ' and patient_master.appointment_date < "' . now() . '"';
		}
		if ($discipline != "") {
			$where .= ' and patient_master.diciplin = "' . $discipline . '"';
		}



		$query = Patient::with(['users', 'assignToUser'])->select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.location_name', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->whereRaw($where)->orderBy('patient_master.id', 'desc')->simplePaginate(20);
		return $query;
	}

	public function getPatientReport($agency_fk, $created_date, $status)
	{
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.deleted_flag ="N" ';
		} else {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"';
		}


		if ($status != '') {
			$where .= ' and patient_master.status = "' . $status . '"';
		}

		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}

		if ($agency_fk != '') {
			$where .= ' and patient_master.agency_id = "' . $agency_fk . '"';
		}
		$query = Patient::select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.address1', 'location_master.city', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->whereRaw($where)->orderBy('patient_master.id', 'desc')->paginate(50);

		return $query;
	}

	public function getDetailById($id)
	{
		$auth = auth()->user();

		$query = Patient::where('deleted_flag', 'N')->where('id', $id);
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
		} else {
			
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$query->whereRaw('patient_master.agency_id IN("' . $implodeIds . '")');
			}
			if ($auth->record_access != 'All') {
				$query->whereRaw('type	="' . $auth->record_access . '"');
			}
		}
		$query = $query->first();
		return $query;
	}

	public function getDataExport($statusFlag, $full_name = "", $age = "", $mobile = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $sms_status = "", $record_form = "", $due_date = "", $assign_user_id = "", $is_archive = false, $isPastShow = false, $discipline = "", $patient_code = "", $inservice_date = "", $completed_date = "", $follow_up_date = "", $traning_date = "", $created_by = "", $traning_status = "", $transistion_aid = "", $language_id = "", $dob = "", $last_status_update = "", $last_status_updated_by_id = "",$agency_filter_type = "",$service_filter_type = "", $medication_list = "", $insurance_elg = "",$debug="", $mdo_tag="", $filter_branch_id="",$branch_filter_type="",$state="",$agency_status= "",$allStatusIds=[],$referral_type="",$record_read="")

	{

		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N"  ' . $addCondition . '';

			$agencyids = Utility::getUserWiseAgency();
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
			
		} else {

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
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
		if ($is_archive == "true") {
			
			$where .= ' and patient_master.is_archive = 1';
		} else {
			$where .= ' and patient_master.is_archive = 0';
		}

		if ($full_name != '') {
			$where .= ' and patient_master.full_name LIKE "%' . $full_name . '%"';
		}
		if (!empty($service_id)) {
			$explode = explode(',', $service_id);
			$final = '';

			if (isset($service_filter_type) && $service_filter_type == 'exclude') {
				// Exclude selected service IDs
				foreach ($explode as $key => $vals) {
					$or = '';
					if ($key != 0) {
						$or = ' and ';
					}
					$final .= $or . ' !FIND_IN_SET("' . $vals . '",patient_master.service_id)';
				}
				$where .= ' and (' . $final . ')';
			} else {
				// Include selected service IDs
				foreach ($explode as $key => $vals) {
					$or = '';
					if ($key != 0) {
						$or = ' OR ';
					}
					$final .= $or . ' FIND_IN_SET("' . $vals . '",patient_master.service_id)';
				}
				$where .= ' and (' . $final . ')';
			}
		}

		if ($type != '') {
			$where .= ' and patient_master.type = "' . $type . '"';
		}
		if ($location_id != '') {
			$where .= ' and patient_master.location_id = "' . $location_id . '"';
		}

		if ($mobile != '') {
			$where .= ' and patient_master.mobile = "' . $mobile . '"';
		}
		if ($status != '') {
			
			$explode = explode(',', $status);
			$final = [];
			foreach ($explode as $vsl) {
				if ($vsl == 'Signed-SentBacktotheAgency') {
					$vsl = 'Signed & Sent Back to the Agency';
				}
				if ($vsl == 'TelehealthCompleted-Pending Forms') {
					$vsl = 'Telehealth Completed , Pending Forms';
				}
				if ($vsl == 'PatientAskedtoReschedule') {
					$vsl = 'Patient Asked to Reschedule';
				}
				$final[] = $vsl;
			}
			$quoted = array_map(function ($item) {
				return '"' . addslashes($item) . '"';
			}, $final);

			// Build the WHERE clause
			$where .= ' AND LOWER(patient_master.status) IN (' . implode(',', $quoted) . ')';

		}

		if ($doctor_id != '') {
			$where .= ' and patient_master.doctor_id = "' . $doctor_id . '"';
		}
		if ($assign_user_id != '') {
			$where .= ' and patient_master.assign_user_id = "' . $assign_user_id . '"';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}

		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);
			if (isset($explode[1])) {
				$where .= ' and ((DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0]))
					. '" and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
				$where .= ' OR DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"))';
			} else {
				$where .= ' and ((DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"';
				$where .= ' OR DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"))';
			}
		}
		if ($age != '') {
			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}
		if ($agency_fk != '' && $agency_fk != 'undefined') {
			$agency_fk = str_replace(',', '","', $agency_fk);
			if(isset($agency_filter_type) && $agency_filter_type == 'include'){
				$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
			}elseif(isset($agency_filter_type) && $agency_filter_type == 'exclude'){
				$where .= ' and patient_master.agency_id NOT IN( "' . $agency_fk . '")';
			}
		}
		if ($discipline !== "") {
			$where .= ' and patient_master.diciplin = "' . $discipline . '"';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($due_date != '') {
			$exploderd = explode('-', $due_date);
			$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($inservice_date != '') {
			$exploderd = explode('-', $inservice_date);
			$where .= ' and DATE_FORMAT(patient_master.inservice_datetime,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.inservice_datetime,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}
		if ($isPastShow == "true") {
			$where .= ' and patient_master.appointment_date < "' . now() . '"';
		}
		
		if ($patient_code != "") {
			$where .= ' and patient_master.patient_code = "' . $patient_code . '"';
		}
		if ($completed_date != '') {
			$exploderd = explode('-', $completed_date);
			$where .= ' and DATE_FORMAT(patient_master.completed_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.completed_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($follow_up_date != '') {
			$exploderd = explode('-', $follow_up_date);
			$where .= ' and DATE_FORMAT(patient_master.follow_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.follow_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($traning_date != '') {
			$exploderd = explode('-', $traning_date);
			$where .= ' and DATE_FORMAT(patient_master.traning_due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.traning_due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if (!empty($created_by) && $created_by != "undefined") {
			$where .= ' and patient_master.created_by =' . $created_by;
		}

		if ($traning_status != "") {
			$traning_status = str_replace(',', '","', $traning_status);

			$where .= ' and patient_master.training_status IN( "' . $traning_status . '")';
		}
		if ($transistion_aid != "") {
			if ($transistion_aid != 0) {
				$where .= ' and patient_master.transition_aid = "' . $transistion_aid . '"';
			} else {
				$where .= ' and (patient_master.transition_aid = "' . $transistion_aid . '" OR patient_master.transition_aid IS NULL)';
			}
		}

		if ($language_id != "") {
			$where .= ' and patient_master.language = "' . $language_id . '" ';
		}

		if ($dob != '') {
			$where .= ' and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($dob)) . '"';
		}

		if ($last_status_update != '') {
			$explode = explode('-', $last_status_update);
			if (isset($explode[1])) {
				$where .= ' and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
			} else {
				$where .= ' and DATE_FORMAT(patient_master.last_status_update,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"';
			}
		}

		if ($last_status_updated_by_id != "") {
			$where .= ' and patient_master.last_status_update_by ="' . $last_status_updated_by_id . '"';
		}

		if ($auth->restrict_user ==1) {
			$where .= ' and patient_master.created_by ="' . $auth->id . '"';
		}
		$query = Patient::select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.location_name', 'location_schedule.start_time', 'location_schedule.end_time', 'statusUser.first_name as sFirstName', 'statusUser.last_name as sLastName', 'users.first_name as uFirstName', 'users.last_name as uLastName')
			->join('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'patient_master.created_by');
			})
			->leftjoin('users as statusUser', function ($join) {
				$join->on('statusUser.id', '=', 'patient_master.last_status_update_by');
			});

		// Filter by Medication List - Optimized with JOIN instead of subquery
		if (!empty($medication_list)) {
			if ($medication_list == 'Yes') {
				$query->where(function($q) {
					$q->where('patient_master.medication_count', '>=', 1)->orWhere('patient_master.no_medication_taken', 1);
				});
			} else {
				$query->where('medication_count', '=', 0)->where(function($q) {
					$q->where('patient_master.no_medication_taken', '!=', 1)->orWhereNull('patient_master.no_medication_taken');
				});
			}
		}

		if (!empty($insurance_elg)) {
			if ($insurance_elg == 'Yes') {
				$query->where('insurance_elg_count','>=',1);
			} else {
				$query->where('insurance_elg_count','=',0);
			}
		}

		if (!empty($mdo_tag)) {
			if ($mdo_tag == 'Yes') {
				$query->where('mdo_tag_count','>=',1);
			} else {
				$query->where('mdo_tag_count','=',0);
			}
		}
		
		if (!empty($filter_branch_id)) {
			if(isset($branch_filter_type) && $branch_filter_type == 'include'){
				$query->where('branch_id',$filter_branch_id);
			}elseif(isset($branch_filter_type) && $branch_filter_type == 'exclude'){
				$query->where(function($q) use ($filter_branch_id) {
					$q->where('branch_id','!=',$filter_branch_id)
					->orWhere('branch_id',0)
					->orWhere('branch_id',NULL);
				});
			}
		}

		if(!empty($state)){
			$query->where('patient_master.state','=',$state);
		}

		if (!empty($agency_status)) {
			$query->whereExists(function ($subQuery) use ($agency_status,$allStatusIds) {
				$subQuery->select(DB::raw(1))
					->from('patient_custom_data_submit as pcds')
					->whereColumn('pcds.patient_id', 'patient_master.id')
					->whereNull('deleted_at')
					->whereIn('field_id',$allStatusIds)
					->where('pcds.value', $agency_status);
			});
		}
		if (!empty($referral_type)) {
			$query->where('patient_master.referral_type', '=', $referral_type);
		}
		if(isset($record_read) && $record_read == 0){
			$query->where('patient_master.record_read','=',0);
		}
		$query->whereRaw($where)->where('agency.delete_flag', 'N');
		$query = $query->get();
		return $query;
	}

	public function AllPatientList()
	{
		$auths = auth()->user();

		$query = Patient::select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.address1', 'location_master.city', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->where('patient_master.deleted_flag', 'N')->where('status', 'Pending');
		if ($auths->agency_fk != '') {
			$query->where('patient_master.agency_id', $auths->agency_fk);
		}
		$query->orderBy('id', 'desc');
		$mysql = $query->get();
		return $mysql;
	}
	public function AllUpcommningPatientList()
	{
		$auths = auth()->user();
		$query = Patient::select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.address1', 'location_master.city', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->where('patient_master.deleted_flag', 'N')->whereRaw('DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >="' . date('Y-m-d') . '" and patient_master.status="booked"');
		if ($auths->agency_fk != '') {
			$query->where('patient_master.agency_id', $auths->agency_fk);
		}
		$query->orderBy('patient_master.appointment_date', 'asc');
		$mysql = $query->get();

		return $mysql;
	}

	public function getDetailByIdNew($id)
	{
		$auth = auth()->user();

		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.deleted_flag ="N" ';
			$agencyids = Utility::getUserWiseAgency();


			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '") ';
			}

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where .= $addCondition;
		} else {
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';

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

		if($auth->restrict_user ==1){
			$where .=" and patient_master.created_by=".$auth->id;
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
			->where('patient_master.deleted_flag', 'N')->where('patient_master.id', $id)->first();
		return $query;
	}


	public function AllPatientListByAgencyFK($id, $locId, $assId, $fdate, $startDate, $endDate, $recordType = "")
	{
		$auth = auth()->user();
		$agencyids = Utility::getUserWiseAgency();
		$query = Patient::select('id', 'first_name', 'middle_name', 'last_name', 'fu_date', 'appointment_date', 'appoinment_time_id', 'status', 'type')->where('deleted_flag', 'N');

		$query->whereBetween('appointment_date', [$startDate, $endDate]);

		if (!empty($agencyids)) {

			$query->whereIn('agency_id', $agencyids);
		}
		if ($id != '') {
			$query->whereIn('agency_id', $id);
		}
		if ($locId != '') {
			$query->where('location_id', $locId);
		}
		if ($assId != '') {
			$query->whereIn('id', $assId);
		}
		if ($fdate != '') {
			$query->whereDate('fu_date', $fdate);
		}

		if ($recordType != "") {
			$query->where('type', $recordType);
		}
		$result = $query->where('status', '!=', 'completed')->orderBy('id', 'desc')->get();

		return $result;
	}

	public function search($search,$gb_agency_id)
	{

		$auth = auth()->user();
		$query = Patient::query()->select(['id', 'first_name', 'last_name', 'full_name', 'dob', 'agency_id', 'type', 'diciplin', 'patient_code', 'mobile', 'phone', 'due_date', 'appointment_date', 'telehealth_time_slot', 'telehealth_time_frame', 'telehealth_nurse', 'telehealth_date_time', 'created_date', 'assign_user_id', 'service_id', 'status', 'created_by', 'location_id', 'archived_at']);
		if (is_numeric($search)) {
			if (strlen($search) > 8) {
				$query->where('phone', $search)->orWhere('mobile',$search);
			} else {
				$query->where('id', $search);
			}
		} elseif (in_array(strtolower($search), ['male', 'female'])) {
			$query->whereRaw('LOWER(gender) = ?', [strtolower($search)]);
		} else {
			$parts = explode('/', $search);
			if (count($parts) === 3 && is_numeric($parts[0]) && is_numeric($parts[1]) && is_numeric($parts[2])) {
				$query->WhereDate('dob', date('Y-m-d', strtotime($search)));
			} else {
				$query->where('full_name', 'like', "%{$search}%");
			}
		}

		if (!empty($auth->agency_fk)) { //ag user
			if ($auth->login_type_fk == 2) {
				$agencyIds = Utility::getUserWiseAgency();
				$agencyIds[] = $auth->agency_fk;
			} else {
				$agencyIds = [$auth->agency_fk];
			}
			$query->whereIn('agency_id', $agencyIds);
		}else{ // ny user
			$query->whereIn('agency_id', $gb_agency_id);
		}

		$addCondition = [];
		if ($auth->record_access != 'All') {
			$addCondition[] = ['patient_master.type', '=', $auth->record_access];
		}
		if (in_array($auth->user_type_fk, [3, 4, 184])) {
			$agencyIds = Utility::getUserWiseAgency();
			if (!empty($agencyIds)) {
				$query->whereIn('agency_id', $agencyIds);
			}
		} else {
			$serviceIds = Utility::getServiceByAgencyWithUserAccess($auth->record_access);
			if (!empty($serviceIds)) {
				$query->where(function ($q) use ($serviceIds) {
					foreach ($serviceIds as $srv) {
						$q->orWhereRaw('FIND_IN_SET(?, patient_master.service_id)', [$srv]);
					}
				});
			}
		}

		if($auth->restrict_user ==1){
			$query->where('created_by',$auth->id);
		}
		$query->where('deleted_flag', 'N')->where($addCondition);
		return $query->orderBy('id', 'desc')->simplePaginate(50);
	}

	function getAppointment()
	{
		$date = date('Y-m-d');
		$query = Patient::where('deleted_flag', 'N')->where('patient_sms_flag', 0)->whereRaw("appointment_date >='" . $date . " 00:00:00' and appointment_date<='" . $date . " 23:59:59'")->where('appointment_date', '!=', '')->get();

		return $query;
	}

	function getCheckSamePatientORNotUsingMobile($mobile)
	{
		$query = Patient::where('deleted_flag', 'N')->where('patient_sms_flag', 0)->where('mobile', $mobile)->first();

		return $query;
	}
	function getCountByTimeSchedule($id)
	{
		$query = Patient::where('deleted_flag', 'N')->where('appoinment_time_id', $id)->count();
		return $query;
	}
	function getCountByTimeScheduleTest($date)
	{
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'deleted_flag ="N" ';
		} else {
			$where = 'deleted_flag ="N" and agency_id="' . $auth['agency_fk'] . '"';
		}

		$query = Patient::where('deleted_flag', 'N')->whereRaw($where)->whereRaw('DATE_FORMAT(appointment_date,"%Y-%m-%d") ="' . date('Y-m-d', strtotime($date)) . '"')->count();
		return $query;
	}
	function getCountByTimeScheduleNew($id, $start_time = "")
	{
		$query = Patient::where('deleted_flag', 'N')->where('appoinment_time_id', $id);
		if ($start_time != '') {
			$query->whereRaw('DATE_FORMAT(appointment_date,"%Y-%m-%d") ="' . date('Y-m-d', strtotime($start_time)) . '"');
		}
		$mysql = $query->count();
		return $mysql;
	}

	function getSearchingByDate($agencyId, $start_date, $end_date)
	{
		$query = Patient::select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.address1', 'location_master.city', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->where('patient_master.deleted_flag', 'N')
			->whereDate('patient_master.appointment_date', '>=', date('Y-m-d', strtotime($start_date)))->whereDate('patient_master.appointment_date', '<=', date('Y-m-d', strtotime($end_date)));
		if ($agencyId != '') {
			$query->where('patient_master.agency_id', $agencyId);
		}
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.deleted_flag ="N" ';
		} else {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"';
		}
		$query->whereRaw($where);
		$mysql = $query->orderBy('patient_master.id', 'asc')->get();

		return $mysql;
	}

	function getPatientReportExport($agency_fk, $created_date, $status)
	{
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.deleted_flag ="N" ';
		} else {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"';
		}


		if ($status != '') {
			$where .= ' and patient_master.status = "' . $status . '"';
		}

		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}

		if ($agency_fk != '') {
			$where .= ' and patient_master.agency_id = "' . $agency_fk . '"';
		}

		$query = Patient::select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.address1', 'location_master.city', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->whereRaw($where)->orderBy('patient_master.id', 'asc')->get();

		return $query;
	}
	function getPatientRecordByTwoDate()
	{
		$query = Patient::where('status', 'Pending')->where('deleted_flag', 'N')->where('type', 'Caregiver')->where('sms_count', '<', 3)->get();
		return $query;
	}

	function dobUpdate()
	{
		$query = Patient::where('deleted_flag', 'N')->get();
		return $query;
	}
	function archived($id)
	{
		$query = Patient::where('id', $id)->update(array('archived_at' => date('Y-m-d H:i:s'),'is_archive' => 1));



		return $query;
	}

	function PatientArchiveList($statusFlag, $full_name = "", $age = "", $mobile = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $sms_status = "", $record_form = "", $due_date = "", $assign_user_id = "")
	{
		$auth = auth()->user();
		$query = Patient::select('*');
		$where = 'deleted_flag ="N" and archived_at !="" ';
		if ($statusFlag == 'refused') {

			$where .= ' and patient_master.status ="refused"';
		} else if ($statusFlag == 'cancel') {
			$where .= ' and patient_master.status ="cancelled"';
		} else {
			if ($statusFlag != '') {
				$where .= ' and patient_master.status !="refused" and patient_master.status !="cancelled"';
			}
		}
		if ($full_name != '') {
			$where .= ' and CONCAT_WS("",patient_master.first_name," ",patient_master.last_name) LIKE "%' . $full_name . '"';
		}
		if ($mobile != '') {
			$where .= ' and patient_master.mobile = "' . $mobile . '"';
		}

		if ($type != '') {
			$where .= ' and patient_master.type = "' . $type . '"';
		}
		if ($sms_status != '') {
			$sms_status = str_replace(',', '","', $sms_status);
			$where .= ' and patient_master.patient_sms_flag IN( "' . $sms_status . '")';
		}
		if ($doctor_id != '') {
			$where .= ' and patient_master.doctor_id = "' . $doctor_id . '"';
		}
		if ($service_id != '') {
			$where .= ' and patient_master.service_id ="' . $service_id . '"';
		}
		if ($location_id != '') {
			$location_id = str_replace(',', '","', $location_id);
			$where .= ' and patient_master.location_id IN( "' . $location_id . '")';
		}
		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);
			$where .= ' and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}
		if ($status != '') {
			$status = str_replace(',', '","', $status);
			$stats = strtolower($status);
			$where .= ' and LOWER(patient_master.status) IN( "' . $stats . '")';
		}
		if ($age != '') {

			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}
		if ($assign_user_id != '') {
			$assign_user_id = str_replace(',', '","', $assign_user_id);
			$where .= ' and patient_master.assign_user_id IN( "' . $assign_user_id . '")';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($due_date != '') {
			$exploderd = explode('-', $due_date);
			$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($agency_fk != '') {
			$agency_fk = str_replace(',', '","', $agency_fk);
			$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {
			// if ($auth->agency_fk != "") {
			// 	$where .= ' and patient_master.agency_id = "' . $auth->agency_fk . '"';
			// }
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if ($record_form != '') {
			if ($record_form == 1) {
				$where .= ' and patient_master.record_id !=""';
			} else {
				$where .= ' and patient_master.record_id IS NULL';
			}
		}

		$query = $query->whereRaw($where)->orderBy('patient_master.id', 'desc')->paginate(50);
		return $query;
	}

	function unarchived($id)
	{


		$query = Patient::where('id', $id)->update(array('archived_at' => NULL,'is_archive' => 0));

		return $query;
	}
	function garbaseList($first_name = "", $phone = "", $age = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $mobile = "", $sms_status = "")
	{
		$query = Patient::select('*');
		$where = 'garbase_status =1';
		if ($first_name != '') {
			$where .= ' and CONCAT(first_name," ",last_name) LIKE "%' . $first_name . '%"';
		}
		if ($service_id != '') {
			$where .= " AND service_id IN (" . implode(',', $service_id) . ")";
			//	$where .=' and patient_master.service_id = "'.$service_id.'"';
		}
		if ($type != '') {
			$where .= ' and type = "' . $type . '"';
		}
		if ($location_id != '') {
			$where .= ' and location_id = "' . $location_id . '"';
		}

		if ($phone != '') {
			$where .= ' and phone = "' . $phone . '"';
		}
		if ($mobile != '') {
			$where .= ' and mobile = "' . $mobile . '"';
		}
		if ($status != '') {
			$where .= ' and LOWER(status) = "' . strtolower($status) . '"';
		}
		if ($doctor_id != '') {
			$where .= ' and doctor_id = "' . $doctor_id . '"';
		}
		if ($sms_status != '') {

			$where .= ' and patient_sms_flag = "' . $sms_status . '"';
		}
		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);
			$where .= ' and DATE_FORMAT(appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}
		if ($created_date != '') {
			$explode = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}
		if ($age != '') {
			$where .= ' and dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}

		if ($agency_fk != '' && $agency_fk != 'undefined') {
			$where .= ' and agency_id = "' . $agency_fk . '"';
		}
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where .= 'patient_master.deleted_flag ="N" ';
		} else {
			$where .= 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"';
		}
		$query->whereRaw($where);
		$query = $query->orderBy('patient_master.id', 'desc')->paginate(50);
		return $query;
	}

	public static function countExistingCaregivercode($code)
	{
		$query = Patient::where('deleted_flag', 'N')->where('patient_code', $code)->count();
		return $query;
	}
	public static function getCalenderList($searchDate = "", $agencyId = "", $serviceId = "", $types = "")
	{
		$agencyids = Utility::getUserWiseAgency();
		$auth = auth()->user();
		$query = Patient::where('deleted_flag', 'N'); //->where('telehealth_by', $auth['id']);

		if (!empty($searchDate)) {
			$searchDate = date("Y-m-d",  strtotime(request('search_date')));
			$query->whereBetween('telehealth_date_time', [$searchDate . ' 00:00:00', $searchDate . ' 23:59:59']);
		}
		if (!empty($agencyids)) {
			$query->whereIn('agency_id', $agencyids);
		}
		if ($agencyId != '') {
			$query->whereIn('agency_id', $agencyId);
		}
		if ($types != '') {
			$query->where('type', $types);
		}
		if ($serviceId != '') {
			$query->whereIn('service_id', $serviceId);
		}
		$query->where('telehealth_date_time', '!=', '');
		$result = $query->get();
		return $result;
	}
	public static function getAllUpcommingTelehealth($agency_id, $type_id, $fullname_id, $record_id)
	{
		$date = date('Y-m-d');
		$auths = auth()->user();
		$query = Patient::select('patient_master.id', 'patient_master.type', 'patient_master.record_id', 'patient_master.first_name', 'patient_master.last_name', 'patient_master.phone', 'patient_master.dob', 'patient_master.telehealth_date_time', 'agency.agency_name', 'ds.full_name')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->where('patient_master.deleted_flag', 'N')->whereDate('patient_master.telehealth_date_time', '>=', $date);
		if ($agency_id != '') {
			$query->where('patient_master.agency_id', $agency_id);
		}
		if ($type_id != '') {
			$query->where('patient_master.type', 'LIKE', '%' . $type_id . '%');
		}
		if ($fullname_id != '') {
			$query->whereRaw('CONCAT(patient_master.first_name," ",patient_master.last_name) LIKE "%' . $fullname_id . '%"');
		}
		if ($record_id != '') {
			if ($record_id == 1) {
				$query->where("patient_master.record_id", '!=', '');
			} else {
				$query->where("patient_master.record_id", '=', null);
			}
		}
		$auth = auth()->user();

		$query->where('patient_master.telehealth_by', $auth['id']);
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.deleted_flag ="N" ';
		} else {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"';
		}
		$query->whereRaw($where);

		$query->orderBy('patient_master.id', 'asc');
		$mysql = $query->get();

		return $mysql;
	}
	public static function inflowcareuserAddOrNot($id)
	{
		$query = Patient::where('inflowcare_id', $id)->where('deleted_flag', 'N')->first();
		return $query;
	}

	public function AllPatientListWithPaginateSearch($statusFlag, $full_name = "", $age = "", $mobile = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $sms_status = "", $record_form = "", $due_date = "", $assign_user_id = "")
	{
		$auth = auth()->user();

		$query = Patient::select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.address1', 'location_master.city', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})
			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->where('patient_master.deleted_flag', 'N')->where('status', 'Pending');

		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.deleted_flag ="N"  and patient_master.archived_at IS NULL';
		} else {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '" and patient_master.archived_at IS NULL';
		}

		if ($statusFlag == 'refused') {

			$where .= ' and patient_master.status ="refused"';
		} else if ($statusFlag == 'cancel') {
			$where .= ' and patient_master.status ="cancelled"';
		} else {
			if ($statusFlag != '') {
				$where .= ' and patient_master.status !="refused" and patient_master.status !="cancelled"';
			}
		}
		if ($full_name != '') {
			$where .= ' and CONCAT_WS("",patient_master.first_name," ",patient_master.last_name) LIKE "%' . $full_name . '"';
		}
		if ($mobile != '') {
			$where .= ' and patient_master.mobile = "' . $mobile . '"';
		}

		if ($type != '') {
			$where .= ' and patient_master.type = "' . $type . '"';
		}
		if ($sms_status != '') {
			$sms_status = str_replace(',', '","', $sms_status);
			$where .= ' and patient_master.patient_sms_flag IN( "' . $sms_status . '")';
		}
		if ($doctor_id != '') {
			$where .= ' and patient_master.doctor_id = "' . $doctor_id . '"';
		}
		if ($service_id != '') {
			$where .= ' and patient_master.service_id ="' . $service_id . '"';
		}
		if ($location_id != '') {
			$location_id = str_replace(',', '","', $location_id);
			$where .= ' and patient_master.location_id IN( "' . $location_id . '")';
		}
		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);
			$where .= ' and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}
		if ($status != '') {
			$status = str_replace(',', '","', $status);
			$stats = strtolower($status);
			$where .= ' and LOWER(patient_master.status) IN( "' . $stats . '")';
		}
		if ($age != '') {

			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}
		if ($assign_user_id != '') {
			$assign_user_id = str_replace(',', '","', $assign_user_id);
			$where .= ' and patient_master.assign_user_id IN( "' . $assign_user_id . '")';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($due_date != '') {
			$exploderd = explode('-', $due_date);
			$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($agency_fk != '') {
			$agency_fk = str_replace(',', '","', $agency_fk);
			$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {
			// if ($auth->agency_fk != "") {
			// 	$where .= ' and patient_master.agency_id = "' . $auth->agency_fk . '"';
			// }
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if ($record_form != '') {
			if ($record_form == 1) {
				$where .= ' and patient_master.record_id !=""';
			} else {
				$where .= ' and patient_master.record_id IS NULL';
			}
		}


		$query->whereRaw($where);
		$query->orderBy('id', 'desc');
		$mysql = $query->paginate(10);
		return $mysql;
	}

	public function UpcommingAppoinmentSearch($statusFlag, $full_name = "", $age = "", $mobile = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $sms_status = "", $record_form = "", $due_date = "", $assign_user_id = "")
	{
		$auth = auth()->user();

		$query = Patient::select('patient_master.*', 'agency.agency_name', 'ds.full_name', 'location_master.address1', 'location_master.city', 'location_schedule.start_time', 'location_schedule.end_time')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->leftjoin('doctor_master as ds', function ($join) {
				$join->on('ds.id', '=', 'patient_master.doctor_id');
				$join->where('ds.deleted_flag', 'N');
			})

			->leftjoin('location_master', function ($join) {
				$join->on('location_master.id', '=', 'patient_master.location_id');
			})
			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->where('patient_master.deleted_flag', 'N')->whereRaw('DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >="' . date('Y-m-d') . '" and patient_master.status="booked"');

		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.deleted_flag ="N"  and patient_master.archived_at IS NULL';
		} else {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '" and patient_master.archived_at IS NULL';
		}

		if ($statusFlag == 'refused') {

			$where .= ' and patient_master.status ="refused"';
		} else if ($statusFlag == 'cancel') {
			$where .= ' and patient_master.status ="cancelled"';
		} else {
			if ($statusFlag != '') {
				$where .= ' and patient_master.status !="refused" and patient_master.status !="cancelled"';
			}
		}
		if ($full_name != '') {
			$where .= ' and CONCAT_WS("",patient_master.first_name," ",patient_master.last_name) LIKE "%' . $full_name . '"';
		}
		if ($mobile != '') {
			$where .= ' and patient_master.mobile = "' . $mobile . '"';
		}

		if ($type != '') {
			$where .= ' and patient_master.type = "' . $type . '"';
		}
		if ($sms_status != '') {
			$sms_status = str_replace(',', '","', $sms_status);
			$where .= ' and patient_master.patient_sms_flag IN( "' . $sms_status . '")';
		}
		if ($doctor_id != '') {
			$where .= ' and patient_master.doctor_id = "' . $doctor_id . '"';
		}
		if ($service_id != '') {
			$where .= ' and patient_master.service_id ="' . $service_id . '"';
		}
		if ($location_id != '') {
			$location_id = str_replace(',', '","', $location_id);
			$where .= ' and patient_master.location_id IN( "' . $location_id . '")';
		}
		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);
			$where .= ' and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}
		if ($status != '') {
			$status = str_replace(',', '","', $status);
			$stats = strtolower($status);
			$where .= ' and LOWER(patient_master.status) IN( "' . $stats . '")';
		}
		if ($age != '') {

			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}
		if ($assign_user_id != '') {
			$assign_user_id = str_replace(',', '","', $assign_user_id);
			$where .= ' and patient_master.assign_user_id IN( "' . $assign_user_id . '")';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($due_date != '') {
			$exploderd = explode('-', $due_date);
			$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($agency_fk != '') {
			$agency_fk = str_replace(',', '","', $agency_fk);
			$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {
			if ($auth->agency_fk != "") {
				//	$where .= ' and patient_master.agency_id = "' . $auth->agency_fk . '"';
			}
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if ($record_form != '') {
			if ($record_form == 1) {
				$where .= ' and patient_master.record_id !=""';
			} else {
				$where .= ' and patient_master.record_id IS NULL';
			}
		}

		$query->whereRaw($where);
		$query->orderBy('patient_master.appointment_date', 'asc');
		$mysql = $query->orderBy('patient_master.id', 'desc')->paginate(50);

		return $mysql;
	}

	public function patientListForApi($agency_fk, $offset)
	{

		$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $agency_fk . '"';
		$query = Patient::with(['users:id,first_name,last_name,email,phone', 'assignToUser'])->select('patient_master.*')
			->whereRaw($where)->orderBy('patient_master.id', 'desc')->offset($offset)->limit(50)->get();
		return $query;
	}

	public function getPatientDetailsById($id, $agency_fk)
	{
		return Patient::where('deleted_flag', 'N')->where('id', $id)->where('agency_id', $agency_fk)->first();
	}

	public static function patientDashboardGraphStatusCount($agencyID, $recordType = "", $locationId = "")
	{

		$agencyids = Utility::getUserWiseAgency();

		$query = Patient::select('status', DB::raw('count(*) as total_count'))
			->where('deleted_flag', 'N')
			->whereNotNull('status')
			->whereNull('archived_at');
		if (!empty($agencyids)) {
			$query->whereIn('agency_id', $agencyids);
		}
		if ($agencyID != "") {
			$query->where('agency_id', $agencyID);
		}
		if ($recordType != "") {
			$query->where('type', $recordType);
		}
		if ($locationId != "") {
			$query->where('location_id', $locationId);
		}

		$query = $query->groupBy('status')
			->pluck('total_count', 'status')
			->all();
		return $query;
	}

	public static function patientDashboardGraphStatusAjaxCount($agencyID, $recordType = "", $locationId = "")
	{
		$query =  Patient::select('status', DB::raw('count(*) as count'))
			->where('status', '!=', NULL)
			->where('deleted_flag', 'N')
			->whereNull('archived_at');
		$agencyids = Utility::getUserWiseAgency();
		if (!empty($agencyids)) {
			$query->whereIn('agency_id', $agencyids);
		}
		if ($agencyID != "") {
			$query->where('agency_id', $agencyID);
		}
		if ($recordType != "") {
			$query->where('type', $recordType);
		}
		if ($locationId != "") {
			$query->where('location_id', $locationId);
		}
		$query =  $query->groupBy('status')
			->get();

		return $query;
	}

	public function totalAppointmentBookedByTimeSlot($id, $date)
	{
		return Patient::where('deleted_flag', 'N')->where('appoinment_time_id', $id)->whereDate('appointment_date', $date)->get();
	}

	public function patientListForUsignThirdPartyApi($patientIds)
	{

		//$where = 'patient_master.deleted_flag ="N" and patient_master.id IN (' . $patientIds.')';

		$query = Patient::with(['users', 'assignToUser'])->select('patient_master.*')->where('patient_master.deleted_flag', 'N')
			->whereIn('patient_master.id', $patientIds)->orderBy('patient_master.id', 'desc')->get();
		return $query;
	}

	public  function GetCaregiverDetails($key, $id)
	{

		if ($key == 'p_full_name') {
			$keys = 'CONCAT_WS("",first_name," ",middle_name," ",last_name) as p_full_name';
		} elseif ($key == 'full_address') {
			$keys = DB::raw('CONCAT_WS("",address1," ",address2," ",city," ",state," ",zip_code) as full_address');
		} elseif ($key == 'p_last_name') {
			$keys = 'CONCAT_WS("",last_name," ",first_name," ",middle_name) as p_last_name';
		} else {
			if ($key == 'agency_name') {
				$keys = 'ag.' . $key;
			} else {
				if ($key == 'name') {
					$keys = 'mt.insurance_name as ' . $key;
				} else {
					$keys = $key;
				}
			}
		}


		$query = Patient::selectRaw($keys);
		if ($key == 'agency_name') {
			$query->join('agency as ag', function ($join) {
				$join->on('ag.id', '=', 'patient_master.agency_id');
			});
		}
		if ($key == 'name') {
			$query->join('insurance_masters as mt', function ($join) {
				$join->on('mt.id', '=', 'patient_master.insurance_name');
			});
		}
		$query = $query->where('patient_master.id', $id)->first();

		if ($key == 'name') {

			if (isset($query[$key])) {
			} else {
				$query = Patient::select('other_insurance_name')->where('patient_master.id', $id)->first();
				if (isset($query->other_insurance_name) && $query->other_insurance_name != "") {
					$query[$key] = $query->other_insurance_name;
				}
			}
		}
		return $query[$key] ?? "";
	}

	public function getPatientDetailsByIdWhitoutAgency($id)
	{
		return Patient::where('id', $id)->first();
	}

	public function getDetailsByThirdParty($data)
	{
		return Patient::select('id', 'first_name', 'last_name', 'agency_id', 'status', 'type')->with(['agencyDetail'])->where('deleted_flag', 'N')->where('first_name', 'LIKE', '%' . $data['first_name'] . '%')->where('last_name', 'LIKE', '%' . $data['last_name'] . '%')->where('dob', $data['dob'])->where('agency_id', $data['agency_id'])->get();
	}

	public function getDetailsByHHAPatient($data)
	{
		return Patient::select('id', 'first_name', 'last_name', 'agency_id', 'status', 'type')->with(['agencyDetail'])->where('deleted_flag', 'N')->where('first_name', 'LIKE', '%' . $data['first_name'] . '%')->where('last_name', 'LIKE', '%' . $data['last_name'] . '%')->where('dob', $data['dob'])->where('agency_id', $data['agency_id'])->get();
	}

	public  function GetCaregiverFormDetails($key, $id, $groupId = "")
	{
		if ($key == 'p_full_name') {
			$keys = 'CONCAT_WS("",first_name," ",middle_name," ",last_name) as p_full_name';
		} elseif ($key == 'full_address') {
			$keys = DB::raw('CONCAT_WS("",address1," ",address2," ",city," ",state," ",zip_code) as full_address');
		} elseif ($key == 'p_last_name') {
			$keys = 'CONCAT_WS("",last_name," ",first_name," ",middle_name) as p_last_name';
		} elseif ($key == 'portal_id') {
			$keys = 'id as portal_id';
		} else {
			if ($key == 'agency_name') {
				$keys = 'ag.' . $key;
			} else {
				if ($key == 'agency_name') {
					$keys = 'ag.' . $key;
				} else {
					if ($key == 'name') {
						$keys = 'mt.insurance_name as ' . $key;
					} else {
						$keys = $key;
					}
				}
			}
		}
		if ($key == 'form_created_date') {
			$query = DocumentSentReport::where('groupId', $groupId)->first();
			$date = date('m/d/Y', strtotime($query->created_date));
			if (isset($date) && $date != "") {
				return $date;
			}
		}
		$query = Patient::selectRaw($keys);
		if ($key == 'agency_name') {
			$query->join('agency as ag', function ($join) {
				$join->on('ag.id', '=', 'patient_master.agency_id');
			});
		}

		if ($key == 'name') {
			$query->join('insurance_masters as mt', function ($join) {
				$join->on('mt.id', '=', 'patient_master.insurance_name');
			});
		}
		$query = $query->where('patient_master.id', $id)->first();

		if ($key == 'name') {

			if (isset($query[$key])) {
			} else {
				$query = Patient::select('other_insurance_name')->where('patient_master.id', $id)->first();
				if (isset($query->other_insurance_name) && $query->other_insurance_name != "") {
					$query[$key] = $query->other_insurance_name;
				}
			}
		}
		if ($key == 'language') {
			$query = Language::where('id', $query[$key])->first();
			if (isset($query->name) && $query->name != "") {
				$query[$key] = $query->name;
			}
		}
		if ($key == 'ssn') {
			return !empty($query[$key]) ? $query[$key] : '-';
		}
		return isset($query[$key]) ? $query[$key] : "";
	}

	public function getExportcsvByAgencyid($agencyId)
	{

		return Patient::where('agency_id', $agencyId)->where('deleted_flag', 'N')->where('status', 'completed')->get();
	}

	public function searchPatients($search)
	{
		$query =  Patient::whereRaw("deleted_flag ='N' and agency_id=" . $search['agency_id'] . "  and (id ='" . $search['q'] . "' OR CONCAT(first_name,last_name) LIKE '%" . str_replace(' ', '', $search['q']) . "%'   OR mobile = '" . $search['q'] . "')")->get();
		$finalArray = [];
		if (!empty($query[0])) {
			foreach ($query as $val) {


				$temp  = [];
				$temp['id'] = $val->id;
				$date = "";
				if ($val->dob != "" && $val->dob != '0001-01-01') {
					$date = date('m/d/Y', strtotime($val->dob));
				}
				$temp['name'] = $val->id . ' - ' . $val->first_name . ' ' . $val->last_name . ' ( ' . ucfirst($val->gender) . ') - ' . $date . ' - ' . $val->mobile . ' - ' . $val->diciplin;
				$finalArray[] = $temp;
			}
		}
		return $finalArray;
	}

	public function getPatientWiseAgencyList($data)
	{
		return Patient::select('agency_id', 'id')
			->where('deleted_flag', 'N')
			->whereNotNull('first_name')
			->where('first_name', 'LIKE', '%' . $data['first_name'] . '%')
			->where('last_name', 'LIKE', '%' . $data['last_name'] . '%')
			->where('dob', $data['dob'])
			->where('gender', $data['gender'])
			->with('agencyDetail')
			->groupBy('agency_id')
			->orderBy('agency_id', 'asc')
			->get();
	}

	public function totalPatientCount($type)
	{
		return Patient::where('deleted_flag', 'N')->where('type', $type)->whereNull('archived_at')->count();
	}

	public function getAgencyWisePatientCaregiverGraphDetails()
	{
		return  Patient::selectRaw('count(id) as total,type,location_id')->with('locations')->where('deleted_flag', 'N')->groupBy('location_id')->get();
	}

	public function getTotalAppointmentCountForService($serviceId, $agency_id, $location_id, $type)
	{
		$query = Patient::where('deleted_flag', 'N')->whereNull('archived_at')->whereRaw('FIND_IN_SET(?, service_id)', [$serviceId]);
		if ($agency_id != "") {
			$explode = explode(',', $agency_id);
			$query->whereIn('agency_id', $explode);
		}
		if ($location_id != "") {
			$query->where('location_id', $location_id);
		}
		if ($type != "") {
			$query->where('type', $type);
		}
		return $query = $query->get();
	}

	public function totalCountPatientStatusWise($status, $from_date = "", $to_date = "", $agency_id = [])
	{
		$query = Patient::where('status', $status)->where('deleted_flag', 'N')->whereNull('archived_at');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		if (!empty($agency_id[0])) {
			$query->whereIn('agency_id', $agency_id);
		}

		return $query->count();
	}

	public function getPatientServiceCount()
	{
		$query = Patient::with('agencyDetail:id,agency_name')->withCount(['patientCaregiver'])->withCount(['patientTotalPatient'])->get();
		return $query;
	}

	public function totalCountPatientService($from_date = "", $to_date = "")
	{
		$query = Patient::where('deleted_flag', 'N')->whereNull('archived_at');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		return $query->count();
	}

	public function getStatusWiseData($agency_id)
	{
		$query = Patient::select(
			DB::raw("SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as Pending"),
			DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as Completed"),
			DB::raw("SUM(CASE WHEN status = 'booked' THEN 1 ELSE 0 END) as Booked"),
			DB::raw("SUM(CASE WHEN status = 'Processing' THEN 1 ELSE 0 END) as processing")
		)
			->where('deleted_flag', 'N');
		if (!empty($agency_id)) {
			$explode = explode(',', $agency_id);
			$query->whereIn('agency_id', $explode);
		}
		return $query->first()->toArray();
	}

	public function getStatisticAgencyListData($type, $agency_id)
	{
		$query = Patient::with('agencyDetail:id,agency_name,agency_logo')->where('deleted_flag', 'N')->whereNull('archived_at');
		if (!empty($agency_id[0])) {
			$query->whereIn('agency_id', $agency_id);
		}
		if (!empty($type)) {
			$query->where('type', $type);
		}
		return $query->paginate(50);
	}

	public function getPatientId($id)
	{

		$query = Patient::where('deleted_flag', 'N')->where('id', $id)->first();

		return $query;
	}

	public function searchPatientDetails($data)
	{

		return Patient::with(['agencyDetail'])->where('deleted_flag', 'N')
			->whereRaw('agency_id ="' . $data['agency_id'] . '" and( (CONCAT(first_name,last_name) LIKE "%' . trim($data['q']) . '%") OR mobile="' . $data['q'] . '" OR dob ="' . date('Y-m-d', strtotime($data['q'])) . '")')->get();
	}

	public function getAllPatientRelatedDetails($data)
	{
		$auth = auth()->user();

		$first_name = "";
		$last_name = "";
		if (isset($data['first_name'])) {
			$first_name = $data['first_name'];
		}
		if (isset($data['last_name'])) {
			$last_name = $data['last_name'];
		}
		$fullName = $first_name . ' ' . $last_name;

		$query =  Patient::with(['agencyDetail:id,agency_name'])->where('deleted_flag', 'N')->whereRaw('(LOWER(CONCAT(first_name, " ", last_name)) LIKE "%' . strtolower($fullName) . '%")');
		if ($auth->agency_fk != "") {
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
			$query->whereIn('agency_id', $agencyids);
		}

		if ($data['mobile'] != "") {
			$query->where('mobile', trim($data['mobile']));
		}
		if ($data['dob'] != "") {
			$query->where('dob', date('Y-m-d', strtotime($data['dob'])));
		}
		$query = $query->get();

		return $query;
	}

	public function totalCountPatientCaregiver($agency_id)
	{
		$query = Patient::select(
			DB::raw("SUM(CASE WHEN type = 'Caregiver' THEN 1 ELSE 0 END) as caregivers"),
			DB::raw("SUM(CASE WHEN type = 'Patient' THEN 1 ELSE 0 END) as patients")
		)
			->where('deleted_flag', 'N')->where('archived_at', NULL);
		if (!empty($agency_id)) {
			$query->where('agency_id', $agency_id);
		}
		return $query->get();
	}

	public function getStatisticAgencyData($agency_id, $type)
	{
		$query = Patient::with('agencyDetail:id,agency_name')->select('id', 'first_name', 'middle_name', 'last_name', 'agency_id', 'status', 'type')->where('deleted_flag', 'N')->where('archived_at', NULL);
		if (!empty($type)) {
			$query->where('type', $type);
		}
		$query->where('agency_id', $agency_id);

		return $query->paginate(50);
	}

	public function getDetailByIdEncrypt($id)
	{
		return Patient::where('deleted_flag', 'N')->whereRaw('sha1(id) ="' . $id . '"')->first();
	}

	public function getpatientSearchData($search)
	{
		return Patient::with('agencyDetail:id,agency_name')->whereRaw('demographic_updated_flag =0 and deleted_flag ="N"  and (CONCAT(first_name," ",last_name) LIKE "%' . $search . '%" OR phone ="' . $search . '" OR gender ="' . $search . '" or id="' . $search . '" )')
			->get();
	}

	public function getpatientIds($agency_ids)
	{
		return Patient::select('id')->where('deleted_flag', 'N')->where('archived_at', NULL)->whereIn('agency_id', $agency_ids)->pluck('id');
	}

	public function getDetailByIdEncryptWithRelationShip($id)
	{
		return Patient::with('agencyDetail:id,agency_name', 'languages:id,name', 'insuranceDetails:id,insurance_name')->where('deleted_flag', 'N')->whereRaw('sha1(id) ="' . $id . '"')->first();
	}

	public function getTotalAppointmentCountForServiceNew($agency_id, $location_id, $type)
	{
		$query = Patient::where('deleted_flag', 'N')->whereNull('archived_at');
		if ($agency_id != "") {
			$explode = explode(',', $agency_id);
			$query->whereIn('agency_id', $explode);
		}
		if ($location_id != "") {
			$query->where('location_id', $location_id);
		}
		if ($type != "") {
			$query->where('type', $type);
		}
		return $query = $query->pluck('service_id');
	}

	public function getCaregiverCount($agencyIds, $type, $locationIds)
	{
		$query = Patient::select('type', 'location_id')->where('deleted_flag', 'N')->whereNull('archived_at');
		$query->whereIn('agency_id', $agencyIds);
		if ($type != "") {

			$query->where('type', $type);
		}

		$query->whereIn('location_id', $locationIds);

		return $query = $query->get();
	}

	public function getTotalTypeWiseCount($paginate_flag = "")
	{
		$query = Patient::with('agencyDetail:id,agency_name')->select(
			'id',
			'agency_id',
			DB::raw("SUM(CASE WHEN type = 'Caregiver' THEN 1 ELSE 0 END) as caregivers"),
			DB::raw("SUM(CASE WHEN type = 'Patient' THEN 1 ELSE 0 END) as patients")
		)->where('deleted_flag', 'N')->where('archived_at', NULL)->groupBy('agency_id');
		$query->whereHas('agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		if ($paginate_flag) {
			$query = $query->get();
		} else {
			$query = $query->paginate(50);
		}
		return $query;
	}

	public function getpatientIdsConditionWise($agency_ids)
	{
		$query = Patient::select('id')->where('deleted_flag', 'N')->where('archived_at', NULL);
		if (!empty($agency_ids)) {
			$query->whereIn('agency_id', $agency_ids);
		}
		return $query->pluck('id');
	}

	public function getPatientWiseAgencyListById($data)
	{
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$agencyids = Utility::getUserWiseAgency();
		} else {
			if ($auth->agency_fk != "") {
				$agencyids = Utility::getUserWiseAgency();
				$agencyids[] = $auth['agency_fk'];
			}
		}

		$query =  Patient::where('deleted_flag', 'N')
			->whereNotNull('first_name')->whereNull('archived_at')
			->where('first_name', 'LIKE', '%' . $data['first_name'] . '%')
			->where('last_name', 'LIKE', '%' . $data['last_name'] . '%')
			->where('dob', $data['dob'])
			->where('gender', $data['gender']);
		if (!empty($agencyids[0])) {
			$query->whereIn('agency_id', $agencyids);
		}

		$query = $query->orderBy('agency_id', 'asc')
			->pluck('id');

		return $query;
	}

	public function searchPatientDetailsNew($data)
	{
		$auth = auth()->user();
		$where = "deleted_flag='N' and agency_id =" . $data['agency_id'];


		if ($auth->record_access != 'All') {
			$where .= " and patient_master.type='" . $auth->record_access . "'";
		}
		$where .= ' and (';

		if (is_numeric($data['q'])) {
			if (strlen($data['q']) > 8) {
				$where .= "mobile =" . $data['q'];
			} else {
				$where .= "id =" . $data['q'];
			}
		} else if (strtolower($data['q']) == 'male' || strtolower($data['q']) == 'female') {
			$where .= "LOWER(gender) ='" . strtolower($data['q']) . "'";
		} else {
			$string = date('Y-m-d', strtotime($data['q']));

			if ($string == '1969-12-31') {
				$where .= ' LOWER(CONCAT(first_name," ",last_name)) LIKE "%' . trim($data['q']) . '%"';
			} else {
				$where .= "dob ='" . $string . "'";
			}
		}
		$where .= ')';

		return Patient::with(['agencyDetail:id,agency_name'])->whereRaw($where)->get();
	}

	public function getFlagData($id)
	{
		return Patient::select('flag')->where('id', $id)->where('deleted_flag', 'N')->first();
	}

	public function patientDemographicDetails($search, $paginate = "")
	{

		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgency();
			if (!empty($agencyids)) {

				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		} else {

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';

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

		if (isset($search['is_archive']) && $search['is_archive'] == "true") {

			$where .= ' and patient_master.archived_at IS NOT NULL';
		} else {
			$where .= ' and patient_master.archived_at IS NULL';
		}

		if ($search['full_name'] != '') {

			$where .= ' and CONCAT_WS("",patient_master.first_name," ",patient_master.last_name) LIKE "%' . $search['full_name'] . '%"';
		}
		if ($search['mobile'] != '') {
			$where .= ' and patient_master.mobile = "' . $search['mobile'] . '"';
		}

		if ($search['type'] != '') {
			$where .= ' and patient_master.type = "' . $search['type'] . '"';
		}


		if (isset($search['locationId']) && $search['locationId'] != '') {
			$locationId = implode(',', $search['locationId']);
			$location_id = str_replace(',', '","', $locationId);
			$where .= ' and patient_master.location_id IN( "' . $location_id . '")';
		}

		if (isset($search['status']) && $search['status']  != '') {
			$statusData = implode(',', $search['status']);

			$status = str_replace(',', '","', $statusData);

			$stats = strtolower($status);

			$where .= ' and LOWER(patient_master.status) IN( "' . $stats . '")';
		}

		if ($search['patient_code'] != "") {
			$where .= ' and patient_master.patient_code ="' . $search['patient_code'] . '"';
		}

		if (isset($search['assign_user_id']) && $search['assign_user_id'] != '') {
			$assign_user_id = str_replace(',', '","', $search['assign_user_id']);
			$where .= ' and patient_master.assign_user_id IN( "' . $assign_user_id . '")';
		}
		if ($search['created_date'] != '') {
			$exploder = explode('-', $search['created_date']);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($search['due_date'] != '') {
			$exploderd = explode('-', $search['due_date']);
			$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}



		if (isset($search['agency_fk']) && $search['agency_fk'] != '') {
			$agency_fk = implode(',', $search['agency_fk']);
			$agency_fk = str_replace(',', '","', $agency_fk);
			$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
		}
		// print_r($auth);
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if ($search['diciplin'] != "") {
			$where .= ' and patient_master.diciplin = "' . $search['diciplin'] . '"';
		}


		if ($search['created_by_ny'] != "") {
			if ($search['created_by_ny'] != 'undefined') {
				$where .= ' and patient_master.created_by = ' . $search['created_by_ny'];
			}
		}

		if ($search['transition_aid'] != "") {
			if ($search['transition_aid'] != 0) {
				$where .= ' and patient_master.transition_aid = "' . $search['transition_aid'] . '"';
			} else {
				$where .= ' and (patient_master.transition_aid = "' . $search['transition_aid'] . '" OR patient_master.transition_aid IS NULL)';
			}
		}

		if ($search['language_id'] != "") {
			$where .= ' and patient_master.language = "' . $search['language_id'] . '" ';
		}


		if ($search['dob'] != '') {
			$explode = explode('-', $search['dob']);
			$where .= ' and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"';
		}


		$query = Patient::with(['users', 'assignToUser'])->select('patient_master.*', 'agency.agency_name')->leftjoin('agency', function ($join) {
			$join->on('agency.id', '=', 'patient_master.agency_id');
			$join->where('agency.delete_flag', 'N');
		});

		if (!empty($search['medication_list'])) {
			if ($search['medication_list'] == 'Yes') {
				$query->where(function($q) {
					$q->where('patient_master.medication_count', '>=', 1)->orWhere('patient_master.no_medication_taken', 1);
				});
			} else {
				$query->where('medication_count', '=', 0)->where(function($q) {
					$q->where('patient_master.no_medication_taken', '!=', 1)->orWhereNull('patient_master.no_medication_taken');
				});
			}
		}

		if (!empty($search['insurance_elg'])) {
			if ($search['insurance_elg'] == 'Yes') {
				$query->where('insurance_elg_count','>=',1);
			} else {
				$query->where('insurance_elg_count','=',0);
			}
		}

		if (!empty($search['mdo_tag'])) {
			if ($search['mdo_tag'] == 'Yes') {
				$query->where('mdo_tag_count','>=',1);
			} else {
				$query->where('mdo_tag_count','=',0);
			}
		}
		$query->whereRaw($where)->where('agency.delete_flag', 'N')->orderBy('patient_master.id', 'desc');
		if ($paginate != "") {
			$query = $query->get();
		} else {
			$query = $query->simplePaginate(20);
		}
		return $query;
	}

	public function patientDemographicCount($search)
	{

		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgency();
			if (!empty($agencyids)) {

				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		} else {

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';

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

		if (isset($search['is_archive']) && $search['is_archive'] == "true") {

			$where .= ' and patient_master.archived_at IS NOT NULL';
		} else {
			$where .= ' and patient_master.archived_at IS NULL';
		}

		if ($search['full_name'] != '') {

			$where .= ' and CONCAT_WS("",patient_master.first_name," ",patient_master.last_name) LIKE "%' . $search['full_name'] . '%"';
		}
		if ($search['mobile'] != '') {
			$where .= ' and patient_master.mobile = "' . $search['mobile'] . '"';
		}

		if ($search['type'] != '') {
			$where .= ' and patient_master.type = "' . $search['type'] . '"';
		}


		if (isset($search['locationId']) && $search['locationId'] != '') {
			$locationId = implode(',', $search['locationId']);
			$location_id = str_replace(',', '","', $locationId);
			$where .= ' and patient_master.location_id IN( "' . $location_id . '")';
		}

		if (isset($search['status']) && $search['status']  != '') {
			$statusData = implode(',', $search['status']);

			$status = str_replace(',', '","', $statusData);

			$stats = strtolower($status);

			$where .= ' and LOWER(patient_master.status) IN( "' . $stats . '")';
		}

		if ($search['patient_code'] != "") {
			$where .= ' and patient_master.patient_code ="' . $search['patient_code'] . '"';
		}

		if (isset($search['assign_user_id']) && $search['assign_user_id'] != '') {
			$assign_user_id = str_replace(',', '","', $search['assign_user_id']);
			$where .= ' and patient_master.assign_user_id IN( "' . $assign_user_id . '")';
		}
		if ($search['created_date'] != '') {
			$exploder = explode('-', $search['created_date']);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($search['due_date'] != '') {
			$exploderd = explode('-', $search['due_date']);
			$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}



		if (isset($search['agency_fk']) && $search['agency_fk'] != '') {
			$agency_fk = implode(',', $search['agency_fk']);
			$agency_fk = str_replace(',', '","', $agency_fk);
			$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
		}
		// print_r($auth);
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if ($search['diciplin'] != "") {
			$where .= ' and patient_master.diciplin = "' . $search['diciplin'] . '"';
		}


		if ($search['created_by_ny'] != "") {
			if ($search['created_by_ny'] != 'undefined') {
				$where .= ' and patient_master.created_by = ' . $search['created_by_ny'];
			}
		}

		if ($search['transition_aid'] != "") {
			if ($search['transition_aid'] != 0) {
				$where .= ' and patient_master.transition_aid = "' . $search['transition_aid'] . '"';
			} else {
				$where .= ' and (patient_master.transition_aid = "' . $search['transition_aid'] . '" OR patient_master.transition_aid IS NULL)';
			}
		}

		if ($search['language_id'] != "") {
			$where .= ' and patient_master.language = "' . $search['language_id'] . '" ';
		}


		if ($search['dob'] != '') {
			$explode = explode('-', $search['dob']);
			$where .= ' and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"';
		}


		$query = Patient::leftjoin('agency', function ($join) {
			$join->on('agency.id', '=', 'patient_master.agency_id');
			$join->where('agency.delete_flag', 'N');
		})->select(DB::raw('count(patient_master.id) as count'))
			->whereRaw($where)->where('agency.delete_flag', 'N')->get();
		return $query;
	}

	public function getPatientDetails($ids)
	{
		$query = Patient::select('id', 'first_name', 'last_name')->where('deleted_flag', 'N')->whereIn('id', $ids);
		return $query->get();
	}

	/*****************Search for MDO without is archived */
	public function getSearchPatientDetails($search)
	{
		$query = Patient::with(['agencyDetail:id,agency_name','users:id,first_name,last_name,agency_fk'])->where('deleted_flag', 'N');

		if (isset($search['first_name']) && $search['first_name'] != "") {
			$query->whereRaw('Lcase(first_name) LIKE "%' . strtolower($search['first_name']) . '%"');
		}

		if (isset($search['last_name']) && $search['last_name'] != "") {
			$query->whereRaw('Lcase(last_name) LIKE "%' . strtolower($search['last_name']) . '%"');
		}

		if (isset($search['dob_id']) && $search['dob_id'] != "") {
			$query->whereRaw('dob ="' . date('Y-m-d', strtotime($search['dob_id'])) . '"');
		}

		if (isset($search['gender']) && $search['gender'] != "") {
			$query->whereRaw('Lcase(gender) = "' . strtolower($search['gender']) . '"');
		}

		if (isset($search['type'])) {

			$query->where('type', $search['type']);
		}

		if (isset($search['ssn']) && $search['ssn'] != "") {

			$query->where('ssn', $search['ssn']);
		}

		if (isset($search['mobile_s']) && $search['mobile_s'] != "") {
			$mobile = preg_replace('/\D/', '', $search['mobile_s']); // removes everything except digits
    		$query->whereRaw("REPLACE(REPLACE(REPLACE(mobile, '(', ''), ')', ''), '-', '') = ?", [$mobile]);
		}
		$query = $query->where('agency_id', $search['agency_id']);
		return $query->get();

	}

	public function getDataExportNew($statusFlag, $full_name = "", $age = "", $mobile = "", $status = "", $doctor_id = "", $appointment_date = "", $agency_fk = "", $location_id = "", $service_id = "", $type = "", $created_date = "", $sms_status = "", $record_form = "", $due_date = "", $assign_user_id = "", $is_archive = false, $isPastShow = false, $discipline = "", $patient_code = "", $inservice_date = "", $completed_date = "", $follow_up_date = "", $traning_date = "", $created_by = "", $traning_status = "", $transistion_aid = "", $language_id = "", $dob = "")

	{
		// $auth = auth()->user();
		// if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
		// 	$where = 'patient_master.deleted_flag ="N"   and patient_master.archived_at IS NULL';
		// } else {

		// 	$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"   and patient_master.archived_at IS NULL';
		// }
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgency();
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
			//and patient_master.archived_at IS NULL
		} else {
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N" and true and patient_master.agency_id="' . $auth['agency_fk'] . '"' . $addCondition . '';

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
		if ($is_archive == "true") {
			// dd($is_archive);
			$where .= ' and patient_master.archived_at IS NOT NULL';
		} else {
			$where .= ' and patient_master.archived_at IS NULL';
		}

		if ($full_name != '') {
			$where .= ' and CONCAT(patient_master.first_name," ",patient_master.last_name) LIKE "%' . $full_name . '%"';
		}
		if ($service_id != '') {
			$explode = explode(',', $service_id);
			$final = '';
			foreach ($explode as $key => $vals) {
				$or = '';
				if ($key != 0) {
					$or = ' OR ';
				}
				$final .= $or . ' FIND_IN_SET("' . $vals . '",patient_master.service_id)';
			}
			$where .= ' and (' . $final . ')';
		}

		// if ($service_id != '') {
		// 	$where .= " AND patient_master.service_id IN (" . $service_id . ")";
		// 	//	$where .=' and patient_master.service_id = "'.$service_id.'"';
		// }
		if ($type != '') {
			$where .= ' and patient_master.type = "' . $type . '"';
		}
		if ($location_id != '') {
			$where .= ' and patient_master.location_id = "' . $location_id . '"';
		}

		if ($mobile != '') {
			$where .= ' and patient_master.mobile = "' . $mobile . '"';
		}
		if ($status != '') {
			$status = str_replace(',', '","', $status);
			$stats = strtolower($status);
			$where .= ' and LOWER(patient_master.status) IN( "' . $stats . '")';
			//$where .= ' and LOWER(patient_master.status) = "' . strtolower($status) . '"';
		}
		if ($doctor_id != '') {
			$where .= ' and patient_master.doctor_id = "' . $doctor_id . '"';
		}
		if ($assign_user_id != '') {
			$where .= ' and patient_master.assign_user_id = "' . $assign_user_id . '"';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}

		if ($appointment_date != '') {
			$explode = explode('-', $appointment_date);

			$where .= ' and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime(trim($explode[0]))) . '" and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime(trim($explode[1]))) . '"';
		}
		if ($age != '') {
			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($age)) . '"';
		}
		if ($agency_fk != '' && $agency_fk != 'undefined') {
			$agency_fk = str_replace(',', '","', $agency_fk);
			$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
		}
		if ($discipline !== "") {
			$where .= ' and patient_master.diciplin = "' . $discipline . '"';
		}
		if ($created_date != '') {
			$exploder = explode('-', $created_date);
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}
		if ($due_date != '') {
			$exploderd = explode('-', $due_date);
			$where .= ' and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($inservice_date != '') {
			$exploderd = explode('-', $inservice_date);
			$where .= ' and DATE_FORMAT(patient_master.inservice_datetime,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.inservice_datetime,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}
		if ($isPastShow == "true") {
			$where .= ' and patient_master.appointment_date < "' . now() . '"';
		}
		//die($where); 

		if ($patient_code != "") {
			$where .= ' and patient_master.patient_code = "' . $patient_code . '"';
		}
		if ($completed_date != '') {
			$exploderd = explode('-', $completed_date);
			$where .= ' and DATE_FORMAT(patient_master.completed_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.completed_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($follow_up_date != '') {
			$exploderd = explode('-', $follow_up_date);
			$where .= ' and DATE_FORMAT(patient_master.follow_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.follow_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($traning_date != '') {
			$exploderd = explode('-', $traning_date);
			$where .= ' and DATE_FORMAT(patient_master.traning_due_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploderd[0])) . '" and DATE_FORMAT(patient_master.traning_due_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploderd[1])) . '"';
		}

		if ($created_by != '') {

			if ($created_by != "undefined") {
				$where .= ' and patient_master.created_by =' . $created_by;
			}
		}

		if ($traning_status != "") {
			$traning_status = str_replace(',', '","', $traning_status);

			$where .= ' and patient_master.training_status IN( "' . $traning_status . '")';
		}
		if ($transistion_aid != "") {
			if ($transistion_aid != 0) {
				$where .= ' and patient_master.transition_aid = "' . $transistion_aid . '"';
			} else {
				$where .= ' and (patient_master.transition_aid = "' . $transistion_aid . '" OR patient_master.transition_aid IS NULL)';
			}
		}

		if ($language_id != "") {
			$where .= ' and patient_master.language = "' . $language_id . '" ';
		}

		if ($dob != '') {
			$explode = explode('-', $dob);
			$where .= ' and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}

		$query = Patient::whereRaw($where)->pluck('patient_master.id');
		return $query->toArray();
	}

	public function getDetailByIdNewAll($id)
	{
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.id !="" ';
		} else {
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';

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

	public function getDetailByIdWithoutLogin($id)
	{
		$query = Patient::with(['languages', 'locations:id,location_name,address1'])->select('patient_master.*', 'agency.agency_name', 'location_schedule.start_time', 'location_schedule.end_time as edate')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
				$join->where('agency.delete_flag', 'N');
			})

			->leftjoin('location_schedule', function ($join) {
				$join->on('location_schedule.id', '=', 'patient_master.appoinment_time_id');
			})
			->where('patient_master.deleted_flag', 'N')->where('patient_master.id', $id)->first();
		return $query;
	}

	function getCountByTimeScheduleNewWithLocation($id, $start_time = "", $location_id)
	{
		$query = Patient::where('deleted_flag', 'N')->where('location_id', $location_id)->where('appoinment_time_id', $id);
		if ($start_time != '') {
			$query->whereRaw('DATE_FORMAT(appointment_date,"%Y-%m-%d") ="' . date('Y-m-d', strtotime($start_time)) . '"');
		}
		$mysql = $query->count();
		return $mysql;
	}

	public function checkForThirdPartyExistingData($search, $agencyId)
	{

		$query = Patient::where('deleted_flag', 'N')
			->where('mobile', $search['mobile'])
			->where('type', $search['type'])
			->where('dob', Utility::convertMdyToYmdUsingCarbon($search['dob']));
		if (isset($search['gender']) && $search['gender'] != "") {
			$query->whereRaw('LCASE(gender) ="' . strtolower($search['gender']) . '"');
		}

		return 	$query->where('agency_id', $agencyId)->first();
	}

	public function checkForThirdPartyExistingDataHub($search, $agencyId)
	{

		$query = HubPatient::where('deleted_flag', 'N')
			->where('mobile', $search['mobile'])
			->where('type', $search['type'])
			->where('dob', Utility::convertMdyToYmdUsingCarbon($search['dob']));
		if (isset($search['gender']) && $search['gender'] != "") {
			$query->whereRaw('LCASE(gender) ="' . strtolower($search['gender']) . '"');
		}

		return 	$query->where('agency_id', $agencyId)->first();
	}

	public function getByPhoneNumber($phone)
	{
		$query = Patient::where('mobile', $phone)->where('deleted_flag', 'N')->first();
		   if (isset($query->id) && $query->id != "") {
			return $query;
		} 
		$query =  Patient::where('phone', $phone)->where('deleted_flag', 'N')->first();

			if (isset($query->id) && $query->id != "") {
			return $query;
		}
		$query =  Patient::where('emergency_phone', $phone)->where('deleted_flag', 'N')->first();
		
			if (isset($query->id) && $query->id != "") {
			return $query;
		}
		 return false;
		
	}

	/******************Please donot add any new parameter */
	public function getDetailsWithDocumentDownload($id)
	{
		$auth = auth()->user();

		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.id !=""';
		} else {
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and  patient_master.type='" . $auth->record_access . "'";
			}
			$where = "patient_master.deleted_flag='N' " . $addCondition;

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
		$where .= 'and patient_master.id =' . $id;


		$query = Patient::select('*')->whereRaw($where)->first();

		return $query;
	}

	public function searchForOnlyDocumentReport($search)
	{
		$agencyids = Utility::getUserWiseAgency();
		$query = Patient::selectRaw('id,type,first_name,last_name,agency_id,dob,ssn,cin,medicare_no')->with(['agencyDetail:id,agency_name'])->whereHas('agencyDetail')->where('deleted_flag', 'N')->whereNull('archived_at');
		if (isset($search['agency_id']) && count($search['agency_id']) > 0) {
			$query->whereIn('agency_id', $search['agency_id']);
		}
		if (isset($search['patient_id']) && $search['patient_id'] != "") {
			$query->where('id', $search['patient_id']);
		}
		if (isset($search['patient_type']) && $search['patient_type'] != "") {

			$query->where('type', $search['patient_type']);
		}
		if (count($agencyids) > 0) {
			$query->whereIn('agency_id', $agencyids);
		}
		$query = $query->get();

		return $query;
	}

	public function checkExistingAlayacareEmp($empId, $agencyId)
	{
		return Patient::select('id')->where('deleted_flag', 'N')->where('alaycare_id', $empId)->where('agency_id', $agencyId)->first();
	}

	public function getPatientDetailsId($id)
	{
		return Patient::select('id', 'agency_id', 'type', 'key', 'language', 'mobile')->where('id', $id)->where('deleted_flag', 'N')->first();
	}

	public function getTeledata($tele_key)
	{
		return Patient::where('telehealth_key', $tele_key)->where('deleted_flag', 'N')->first();
	}

	public function getDetailByIdNewDebug($id)
	{
		$auth = auth()->user();

		if (in_array($auth['user_type_fk'], array(3, 4, 184))) {
			$where = 'patient_master.deleted_flag ="N" ';
			$agencyids = Utility::getUserWiseAgency();


			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '") ';
			}

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where .= $addCondition;
		} else {
			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';

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
			->where('patient_master.deleted_flag', 'N')->where('patient_master.id', $id)->first();
		return $query;
	}

	public function searchForOnlyDocumentReportById($patientId)
	{
		$agencyids = Utility::getUserWiseAgency();
		$query = Patient::selectRaw('id,type,first_name,last_name,agency_id,status')->with(['agencyDetail:id,agency_name'])->whereHas('agencyDetail')->where('deleted_flag', 'N')->whereNull('archived_at');
		$query->where('id', $patientId);
		$query = $query->first();

		return $query;
	}

	function getPatientTeleCountByTime($id, $date = "")
	{
		$query = Patient::where('deleted_flag', 'N')->where('telehealth_time_slot', $id);
		if ($date != '') {
			$query->whereRaw('DATE_FORMAT(telehealth_date_time,"%Y-%m-%d") ="' . date('Y-m-d', strtotime($date)) . '"');
		}
		$mysql = $query->count();
		return $mysql;
	}

	/**
	 * Single query that returns all slot IDs (from the given list) that have
	 * at least one patient booked on $date. Replaces N per-slot DB calls.
	 */
	public function getBookedSlotIdsForDate(array $slotIds, string $date): array
	{
		if (empty($slotIds)) {
			return [];
		}
		return Patient::where('deleted_flag', 'N')
			->whereIn('telehealth_time_slot', $slotIds)
			->whereRaw('DATE_FORMAT(telehealth_date_time, "%Y-%m-%d") = ?', [date('Y-m-d', strtotime($date))])
			->pluck('telehealth_time_slot')
			->unique()
			->values()
			->all();
	}

	public function getPatientExistingAppointment($patientId)
	{
		return Patient::where([
			'patient_master.id' => $patientId,
			'patient_master.deleted_flag' => 'N',
		])->whereNotNull('telehealth_time_slot')
			->select(
				'patient_master.telehealth_time_slot',
				'patient_master.telehealth_nurse',
				'patient_master.telehealth_date_time as date'
			)
			->first();
	}

	public function referralSourceList($search, $type = "")
	{

		$query  = Patient::selectRaw('SUM(CASE WHEN type = "Caregiver" THEN 1 ELSE 0 END) as caregiver_count,SUM(CASE WHEN type = "Patient" THEN 1 ELSE 0 END) as patient_count,referral_type')->where('deleted_flag', 'N')->whereNull('archived_at')->whereNotNull('referral_type')->whereNotNull('type');
		if (isset($search['referral_type']) && $search['referral_type'] != "") {
			$query->where('referral_type', $search['referral_type']);
		}
		if (isset($search['created_date']) && $search['created_date'] != "") {
			$explode = explode('-', $search['created_date']);
			$query->whereDate('created_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('created_date', '<=', date('Y-m-d', strtotime($explode[1])));
		}
		$query->groupBy('referral_type');
		if ($type != "") {
			$query = $query->get();
		} else {
			$query = $query->simplePaginate(50);
		}
		return $query;
	}

	public function getPatientScheduledData($telehealth_ids)
	{
		$data = Patient::select('id')->whereIn('telehealth_time_slot', $telehealth_ids)->first();
		if (isset($data->id) && !empty($data->id)) {
			return 1;
		} else {
			return 0;
		}
	}

	public static function patientdetailedRefusalsAjaxCount($agencyID, $recordType = "", $locationId = "", $createdDate = "", $lastUpdatedDate = "", $agency_fk = "", $agency_filter_type = "", $service_id = "", $service_filter_type = "", $assigned_to = "", $medication_list = "", $insurance_elg = "", $mdo_tag = "", $branch_id = "", $branch_filter_type = "")
	{
		$patientFilterSubquery = null;
		if (!empty($medication_list) || !empty($insurance_elg) || !empty($mdo_tag)) {
			$patientFilterSubquery = DB::table('patient_master')
				->select('id')
				->where('deleted_flag', 'N')
				->whereNull('archived_at')
				->when(!empty($medication_list), function ($query) use ($medication_list) {
					if ($medication_list == 'Yes') {
						$query->where(function($q) {
							$q->where('medication_count', '>=', 1)->orWhere('no_medication_taken', 1);
						});
					} else {
						$query->where('medication_count', '=', 0)->where(function($q) {
							$q->where('no_medication_taken', '!=', 1)->orWhereNull('no_medication_taken');
						});
					}
				})
				->when(!empty($insurance_elg), function ($query) use ($insurance_elg) {
					if ($insurance_elg == 'Yes') {
						$query->where('insurance_elg_count', '>=', 1);
					} else {
						$query->where('insurance_elg_count', '=', 0);
					}
				})
				->when(!empty($mdo_tag), function ($query) use ($mdo_tag) {
					if ($mdo_tag == 'Yes') {
						$query->where('mdo_tag_count', '>=', 1);
					} else {
						$query->where('mdo_tag_count', '=', 0);
					}
				});
		}

		$query = Resolution::leftjoin('master_table', function ($join) {
			$join->on('master_table.id', '=', 'resolution_log.refuse_reason');
			$join->where('master_table.del_flag', 'N')
				->whereIn('master_table.master_type_fk', [29, 32]);
		})->leftjoin('patient_service_requests', function ($join) {
			$join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
		})
			->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'pm.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->where('resolution_log.resolution', 'Refused')
			->where('agency.delete_flag', 'N')
			->whereNotNull('resolution_log.refuse_reason')
			->where('patient_service_requests.del_flag', 'N')
			->where('pm.deleted_flag', 'N')
			->when(isset($createdDate) && !empty($createdDate), function ($query) use ($createdDate) {

				$date = explode('-', $createdDate);
				if (count($date) > 0) {
					$from_date = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
					$to_date = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
				}

				$query->whereBetween('resolution_log.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			})
			->select([
				DB::raw("DATE_FORMAT(resolution_log.created_at, '%m/%d/%Y') as service_date"),
				DB::raw('COUNT(*) as count'),
				'pm.agency_id',
				'agency.agency_name',
				'master_table.name as status_name',
				'master_table.id as status_id',
				'patient_service_requests.id'
			])
			->when($lastUpdatedDate, function ($query) use ($lastUpdatedDate) {
				$explode = explode('-', $lastUpdatedDate);
				$query->whereDate('patient_service_requests.last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('patient_service_requests.last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
			});
		if ($agencyID != "") {
			$query =	$query->whereIn('pm.agency_id', $agencyID);
		} else {
			$agencyids = Utility::getUserWiseAgency();
			if (Auth()->user()->agency_fk != "") {
				$agencyids[] = Auth()->user()->agency_fk;
			}
			if (!empty($agencyids)) {
				$query =	$query->whereIn('pm.agency_id', $agencyids);
			}
		}
		if ($recordType != "") {
			$query = $query->where('pm.type', $recordType);
		}
		if ($locationId != "") {
			$query = $query->where('pm.location_id', $locationId);
		}

		if ($agency_fk && !empty($agency_fk)) {
			if (isset($agency_filter_type) && $agency_filter_type == 'include') {
				$query = $query->whereIn('pm.agency_id', $agency_fk);
			} elseif (isset($agency_filter_type) && $agency_filter_type == 'exclude') {
				$query = $query->whereNotIn('pm.agency_id', $agency_fk);
			}
		}

		if ($service_id && !empty($service_id)) {
			if (isset($service_filter_type) && $service_filter_type == 'include') {
				$query = $query->whereExists(function ($subQuery) use ($service_id) {
					$subQuery->select(\DB::raw(1))
						->from('patient_wise_service_requested as pwsr')
						->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
						->whereIn('pwsr.service_id', $service_id)
						->where('pwsr.del_flag', 'N');
				});
			} elseif (isset($service_filter_type) && $service_filter_type == 'exclude') {
				$query = $query->whereNotExists(function ($subQuery) use ($service_id) {
					$subQuery->select(\DB::raw(1))
						->from('patient_wise_service_requested as pwsr')
						->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
						->whereIn('pwsr.service_id', $service_id)
						->where('pwsr.del_flag', 'N');
				});
			}
		}
		if ($assigned_to && !empty($assigned_to)) {
			$query = $query->whereIn('pm.assign_user_id', $assigned_to);
		}
		if (!is_null($patientFilterSubquery)) {
			$query = $query->whereIn('patient_service_requests.patient_id', $patientFilterSubquery);
		}
		if ($branch_id && !empty($branch_id)) {
			if (isset($branch_filter_type) && $branch_filter_type == 'include') {
				$query = $query->whereIn('pm.branch_id', $branch_id);
			} elseif (isset($branch_filter_type) && $branch_filter_type == 'exclude') {
				$query = $query->where(function ($sub) use ($branch_id) {
					$sub->whereNotIn('pm.branch_id', $branch_id)
						->orWhere('pm.branch_id', 0)->orWhere('pm.branch_id', null);
				});
			}
		}

		$query = $query->groupBy('resolution_log.refuse_reason')
			->orderBy('count', 'desc')
			->get();
		return $query;
	}

	public static function patientdetailedCancellationsAjaxCount($agencyID, $recordType = "", $locationId = "", $createdDate = "", $lastUpdatedDate = "", $agency_fk = "", $agency_filter_type = "", $service_id = "", $service_filter_type = "", $assigned_to = "", $medication_list = "", $insurance_elg = "", $mdo_tag = "", $branch_id = "", $branch_filter_type = "")
	{
		$patientFilterSubquery = null;
		if (!empty($medication_list) || !empty($insurance_elg) || !empty($mdo_tag)) {
			$patientFilterSubquery = DB::table('patient_master')
				->select('id')
				->where('deleted_flag', 'N')
				->whereNull('archived_at')
				->when(!empty($medication_list), function ($query) use ($medication_list) {
					if ($medication_list == 'Yes') {
						$query->where(function($q) {
							$q->where('medication_count', '>=', 1)->orWhere('no_medication_taken', 1);
						});
					} else {
						$query->where('medication_count', '=', 0)->where(function($q) {
							$q->where('no_medication_taken', '!=', 1)->orWhereNull('no_medication_taken');
						});
					}
				})
				->when(!empty($insurance_elg), function ($query) use ($insurance_elg) {
					if ($insurance_elg == 'Yes') {
						$query->where('insurance_elg_count', '>=', 1);
					} else {
						$query->where('insurance_elg_count', '=', 0);
					}
				})
				->when(!empty($mdo_tag), function ($query) use ($mdo_tag) {
					if ($mdo_tag == 'Yes') {
						$query->where('mdo_tag_count', '>=', 1);
					} else {
						$query->where('mdo_tag_count', '=', 0);
					}
				});
		}

		$query = Resolution::leftjoin('master_table', function ($join) {
			$join->on('master_table.id', '=', 'resolution_log.cancel_reason');
			// $join->where('master_table.del_flag', 'N');
		})->leftjoin('patient_service_requests', function ($join) {
			$join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
		})
			->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
			->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'pm.agency_id');
				$join->where('agency.delete_flag', 'N');
			})
			->where('resolution_log.resolution', 'Cancelled')
			->where('agency.delete_flag', 'N')
			->whereNotNull('resolution_log.cancel_reason')
			->where('patient_service_requests.del_flag', 'N')
			->where('pm.deleted_flag', 'N')
			->when(isset($createdDate) && !empty($createdDate), function ($query) use ($createdDate) {

				$date = explode('-', $createdDate);
				if (count($date) > 0) {
					$from_date = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
					$to_date = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
				}

				$query->whereBetween('resolution_log.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			})
			->select([
				DB::raw("DATE_FORMAT(resolution_log.created_at, '%m/%d/%Y') as service_date"),
				DB::raw('COUNT(*) as count'),
				'pm.agency_id',
				'agency.agency_name',
				'master_table.name as status_name',
				'master_table.id as status_id',
				'patient_service_requests.id'
			])
			->when($lastUpdatedDate, function ($query) use ($lastUpdatedDate) {
				$explode = explode('-', $lastUpdatedDate);
				$query->whereDate('patient_service_requests.last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('patient_service_requests.last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
			});
		if ($agencyID != "") {
			$query =	$query->whereIn('pm.agency_id', $agencyID);
		} else {
			$agencyids = Utility::getUserWiseAgency();
			if (Auth()->user()->agency_fk != "") {
				$agencyids[] = Auth()->user()->agency_fk;
			}
			if (!empty($agencyids)) {
				$query =	$query->whereIn('pm.agency_id', $agencyids);
			}
		}
		if ($recordType != "") {
			$query = $query->where('pm.type', $recordType);
		}
		if ($locationId != "") {
			$query = $query->where('pm.location_id', $locationId);
		}

		if ($agency_fk && !empty($agency_fk)) {
			if (isset($agency_filter_type) && $agency_filter_type == 'include') {
				$query = $query->whereIn('pm.agency_id', $agency_fk);
			} elseif (isset($agency_filter_type) && $agency_filter_type == 'exclude') {
				$query = $query->whereNotIn('pm.agency_id', $agency_fk);
			}
		}

		if ($service_id && !empty($service_id)) {
			if (isset($service_filter_type) && $service_filter_type == 'include') {
				$query = $query->whereExists(function ($subQuery) use ($service_id) {
					$subQuery->select(\DB::raw(1))
						->from('patient_wise_service_requested as pwsr')
						->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
						->whereIn('pwsr.service_id', $service_id)
						->where('pwsr.del_flag', 'N');
				});
			} elseif (isset($service_filter_type) && $service_filter_type == 'exclude') {
				$query = $query->whereNotExists(function ($subQuery) use ($service_id) {
					$subQuery->select(\DB::raw(1))
						->from('patient_wise_service_requested as pwsr')
						->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
						->whereIn('pwsr.service_id', $service_id)
						->where('pwsr.del_flag', 'N');
				});
			}
		}
		if ($assigned_to && !empty($assigned_to)) {
			$query = $query->whereIn('pm.assign_user_id', $assigned_to);
		}
		if (!is_null($patientFilterSubquery)) {
			$query = $query->whereIn('patient_service_requests.patient_id', $patientFilterSubquery);
		}
		if ($branch_id && !empty($branch_id)) {
			if (isset($branch_filter_type) && $branch_filter_type == 'include') {
				$query = $query->whereIn('pm.branch_id', $branch_id);
			} elseif (isset($branch_filter_type) && $branch_filter_type == 'exclude') {
				$query = $query->where(function ($sub) use ($branch_id) {
					$sub->whereNotIn('pm.branch_id', $branch_id)
						->orWhere('pm.branch_id', 0)->orWhere('pm.branch_id', null);
				});
			}
		}
		$query = $query->groupBy('resolution_log.cancel_reason')
			->orderBy('count', 'desc')
			->get();
		return $query;
	}

	public function agencyStatusCount($fromDate, $toDate, $type, $status, $lastUpdatedDate, $agency_fk = "", $agency_filter_type = "", $service_id = "", $service_filter_type = "", $assigned_to = "", $medication_list = "", $insurance_elg = "", $mdo_tag = "", $branch_id = "", $branch_filter_type = "")
	{

		$auth = auth()->user();

		$patientFilterSubquery = null;
		if (!empty($medication_list) || !empty($insurance_elg) || !empty($mdo_tag)) {
			$patientFilterSubquery = DB::table('patient_master')
				->select('id')
				->where('deleted_flag', 'N')
				->whereNull('archived_at')
				->when(!empty($medication_list), function ($query) use ($medication_list) {
					if ($medication_list == 'Yes') {
						$query->where(function($q) {
							$q->where('medication_count', '>=', 1)->orWhere('no_medication_taken', 1);
						});
					} else {
						$query->where('medication_count', '=', 0)->where(function($q) {
							$q->where('no_medication_taken', '!=', 1)->orWhereNull('no_medication_taken');
						});
					}
				})
				->when(!empty($insurance_elg), function ($query) use ($insurance_elg) {
					if ($insurance_elg == 'Yes') {
						$query->where('insurance_elg_count', '>=', 1);
					} else {
						$query->where('insurance_elg_count', '=', 0);
					}
				})
				->when(!empty($mdo_tag), function ($query) use ($mdo_tag) {
					if ($mdo_tag == 'Yes') {
						$query->where('mdo_tag_count', '>=', 1);
					} else {
						$query->where('mdo_tag_count', '=', 0);
					}
				});
		}

		if ($type == "Patient") {
			$query = Resolution::leftjoin('patient_service_requests', function ($join) {
				$join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
			})
				->when($fromDate, function ($q) use ($fromDate, $toDate) {
					$q->whereBetween('resolution_log.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
				})
				->when($status, function ($q) use ($status) {
					$q->where('resolution_log.resolution', $status);
				})
				->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
				->leftjoin('agency', function ($join) {
					$join->on('agency.id', '=', 'pm.agency_id');
					$join->where('agency.delete_flag', 'N');
				})
				->where('patient_service_requests.del_flag', 'N')
				->where('pm.deleted_flag', 'N')
				->select([
					DB::raw("DATE_FORMAT(patient_service_requests.created_at, '%m/%d/%Y') as service_date"),
					DB::raw('COUNT(*) as count'),
					'pm.agency_id',
					'agency.agency_name'
				])
				->when(in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {

					$agencyids = Utility::getUserWiseAgencyDashboard();
					$agencyids[] = $auth['agency_fk'];

					if (!empty($agencyids)) {
						$implodeIds = implode('","', $agencyids);
						$query->whereIn('pm.agency_id', $implodeIds);
					}
				})

				->when($type, function ($q) use ($type) {
					$q->where('pm.type', $type);
				})
				->when($lastUpdatedDate, function ($q) use ($lastUpdatedDate) {
					$explode = explode('-', $lastUpdatedDate);
					$q->whereDate('patient_service_requests.last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('patient_service_requests.last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
				})
				->when($agency_fk && !empty($agency_fk), function ($q) use ($agency_fk, $agency_filter_type) {
					if (isset($agency_filter_type) && $agency_filter_type == 'include') {
						$q->whereIn('pm.agency_id', $agency_fk);
					} elseif (isset($agency_filter_type) && $agency_filter_type == 'exclude') {
						$q->whereNotIn('pm.agency_id', $agency_fk);
					}
				})
				->when($service_id && !empty($service_id), function ($q) use ($service_id, $service_filter_type) {
					if(isset($service_filter_type) && $service_filter_type == 'include'){
						$q->whereExists(function ($subQuery) use ($service_id) {
							$subQuery->select(\DB::raw(1))
								->from('patient_wise_service_requested as pwsr')
								->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
								->whereIn('pwsr.service_id', $service_id)
								->where('pwsr.del_flag', 'N');
						});
					} elseif(isset($service_filter_type) && $service_filter_type == 'exclude'){
						$q->whereNotExists(function ($subQuery) use ($service_id) {
							$subQuery->select(\DB::raw(1))
								->from('patient_wise_service_requested as pwsr')
								->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
								->whereIn('pwsr.service_id', $service_id)
								->where('pwsr.del_flag', 'N');
						});
					}
				})
				->when($assigned_to && !empty($assigned_to), function ($q) use ($assigned_to) {
					$q->whereIn('pm.assign_user_id', $assigned_to);
				})
				->when(!is_null($patientFilterSubquery), function ($q) use ($patientFilterSubquery) {
					$q->whereIn('patient_service_requests.patient_id', $patientFilterSubquery);
				})
				->when($branch_id && !empty($branch_id), function ($q) use ($branch_id, $branch_filter_type) {
					if (isset($branch_filter_type) && $branch_filter_type == 'include') {
						$q->whereIn('pm.branch_id', $branch_id);
					} elseif (isset($branch_filter_type) && $branch_filter_type == 'exclude') {
						$q->where(function ($sub) use ($branch_id) {
							$sub->whereNotIn('pm.branch_id', $branch_id)
								->orWhere('pm.branch_id', 0)->orWhere('pm.branch_id', null);
						});
					}
				})
				->where('agency.delete_flag', 'N')
				->groupBy('pm.agency_id')
				->orderBy('count', 'desc')
				->get();
		} else {
			$query = PatientServiceRequest::join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
				->leftjoin('agency', function ($join) {
					$join->on('agency.id', '=', 'pm.agency_id');
					$join->where('agency.delete_flag', 'N');
				})
				->where('patient_service_requests.status', $status)
				->where('patient_service_requests.del_flag', 'N')
				->where('pm.deleted_flag', 'N')
				->select([
					DB::raw("DATE_FORMAT(patient_service_requests.created_at, '%m/%d/%Y') as service_date"),
					DB::raw('COUNT(*) as count'),
					'pm.agency_id',
					'agency.agency_name'
				])
				->when($fromDate, function ($q) use ($fromDate, $toDate) {
					$q->whereBetween('patient_service_requests.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
				})
				->when(in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {
					$agencyids = Utility::getUserWiseAgencyDashboard();
					$agencyids[] = $auth['agency_fk'];
					if (!empty($agencyids)) {
						$implodeIds = implode('","', $agencyids);
						$query->whereIn('pm.agency_id', $implodeIds);
					}
				})
				->when($type, function ($q) use ($type) {
					$q->where('pm.type', $type);
				})
				->when($lastUpdatedDate, function ($q) use ($lastUpdatedDate) {
					$explode = explode('-', $lastUpdatedDate);
					$q->whereDate('patient_service_requests.last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('patient_service_requests.last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
				})
				->when($agency_fk && !empty($agency_fk), function ($q) use ($agency_fk, $agency_filter_type) {
					if(isset($agency_filter_type) && $agency_filter_type == 'include'){
						$q->whereIn('pm.agency_id', $agency_fk);
					} elseif(isset($agency_filter_type) && $agency_filter_type == 'exclude'){
						$q->whereNotIn('pm.agency_id', $agency_fk);
					}
				})
				->when($service_id && !empty($service_id), function ($q) use ($service_id, $service_filter_type) {
					if(isset($service_filter_type) && $service_filter_type == 'include'){
						$q->whereExists(function ($subQuery) use ($service_id) {
							$subQuery->select(\DB::raw(1))
								->from('patient_wise_service_requested as pwsr')
								->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
								->whereIn('pwsr.service_id', $service_id)
								->where('pwsr.del_flag', 'N');
						});
					} elseif(isset($service_filter_type) && $service_filter_type == 'exclude') {
						$q->whereNotExists(function ($subQuery) use ($service_id){
							$subQuery->select(\DB::raw(1))
								->from('patient_wise_service_requested as pwsr')
								->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
								->whereIn('pwsr.service_id', $service_id)
								->where('pwsr.del_flag', 'N');
						});
					}
				})
				->when($assigned_to && !empty($assigned_to), function ($q) use ($assigned_to) {
					$q->whereIn('pm.assign_user_id', $assigned_to);
				})
				->when(!is_null($patientFilterSubquery), function ($q) use ($patientFilterSubquery) {
					$q->whereIn('patient_service_requests.patient_id', $patientFilterSubquery);
				})
				->when($branch_id && !empty($branch_id), function ($q) use ($branch_id, $branch_filter_type) {
					if (isset($branch_filter_type) && $branch_filter_type == 'include') {
						$q->whereIn('pm.branch_id', $branch_id);
					} elseif (isset($branch_filter_type) && $branch_filter_type == 'exclude') {
						$q->where(function ($sub) use ($branch_id) {
							$sub->whereNotIn('pm.branch_id', $branch_id)
								->orWhere('pm.branch_id', 0);
						});
					}
				})
				->where('agency.delete_flag', 'N')
				->groupBy('pm.agency_id')
				->orderBy('count', 'desc')
				->get();
		}
		return $query;
	}

	public function statusCount($fromDate, $toDate, $type, $status, $lastUpdatedDate, $agency_fk = "", $agency_filter_type = "", $service_id = "", $service_filter_type = "", $assigned_to = "", $medication_list = "", $insurance_elg = "", $mdo_tag = "", $branch_id = "", $branch_filter_type = "")
	{

		$auth = auth()->user();

		$patientFilterSubquery = null;
		if (!empty($medication_list) || !empty($insurance_elg) || !empty($mdo_tag)) {
			$patientFilterSubquery = DB::table('patient_master')
				->select('id')
				->where('deleted_flag', 'N')
				->whereNull('archived_at')
				->when(!empty($medication_list), function ($query) use ($medication_list) {
					if ($medication_list == 'Yes') {
						$query->where(function($q) {
							$q->where('medication_count', '>=', 1)->orWhere('no_medication_taken', 1);
						});
					} else {
						$query->where('medication_count', '=', 0)->where(function($q) {
							$q->where('no_medication_taken', '!=', 1)->orWhereNull('no_medication_taken');
						});
					}
				})
				->when(!empty($insurance_elg), function ($query) use ($insurance_elg) {
					if ($insurance_elg == 'Yes') {
						$query->where('insurance_elg_count', '>=', 1);
					} else {
						$query->where('insurance_elg_count', '=', 0);
					}
				})
				->when(!empty($mdo_tag), function ($query) use ($mdo_tag) {
					if ($mdo_tag == 'Yes') {
						$query->where('mdo_tag_count', '>=', 1);
					} else {
						$query->where('mdo_tag_count', '=', 0);
					}
				});
		}

		if ($type == "Patient") {
			$query = Resolution::leftjoin('patient_service_requests', function ($join) {
				$join->on('resolution_log.service_request_id', '=', 'patient_service_requests.id');
			})
				->when($fromDate, function ($q) use ($fromDate, $toDate) {
					$q->whereBetween('resolution_log.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
				})
				->when($status, function ($q) use ($status) {
					$q->where('resolution_log.resolution', $status);
				})
				->join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
				->leftjoin('agency', function ($join) {
					$join->on('agency.id', '=', 'pm.agency_id');
					$join->where('agency.delete_flag', 'N');
				})
				->where('patient_service_requests.del_flag', 'N')
				->where('pm.deleted_flag', 'N')
				->select([
					DB::raw("DATE_FORMAT(resolution_log.created_at, '%m/%d/%Y') as service_date"),
					DB::raw('COUNT(*) as total'),
					'pm.agency_id',
					'agency.agency_name',
					'resolution_log.resolution as status'
				])
				->when(in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {

					$agencyids = Utility::getUserWiseAgencyDashboard();
					$agencyids[] = $auth['agency_fk'];

					if (!empty($agencyids)) {
						$implodeIds = implode('","', $agencyids);
						$query->whereIn('pm.agency_id', $implodeIds);
					}
				})

				->when($type, function ($q) use ($type) {
					$q->where('pm.type', $type);
				})
				->when($lastUpdatedDate, function ($q) use ($lastUpdatedDate) {
					$explode = explode('-', $lastUpdatedDate);
					$q->whereDate('patient_service_requests.last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('patient_service_requests.last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
				})
				->when($agency_fk && !empty($agency_fk), function ($q) use ($agency_fk, $agency_filter_type) {
					if (isset($agency_filter_type) && $agency_filter_type == 'include') {
						$q->whereIn('pm.agency_id', $agency_fk);
					} elseif (isset($agency_filter_type) && $agency_filter_type == 'exclude') {
						$q->whereNotIn('pm.agency_id', $agency_fk);
					}
				})
				->when($service_id && !empty($service_id), function ($q) use ($service_id, $service_filter_type) {
					if (isset($service_filter_type) && $service_filter_type == 'include') {
						$q->whereExists(function ($subQuery) use ($service_id) {
							$subQuery->select(\DB::raw(1))
								->from('patient_wise_service_requested as pwsr')
								->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
								->whereIn('pwsr.service_id', $service_id)
								->where('pwsr.del_flag', 'N');
						});
					} elseif (isset($service_filter_type) && $service_filter_type == 'exclude') {
						$q->whereNotExists(function ($subQuery) use ($service_id) {
							$subQuery->select(\DB::raw(1))
								->from('patient_wise_service_requested as pwsr')
								->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
								->whereIn('pwsr.service_id', $service_id)
								->where('pwsr.del_flag', 'N');
						});
					}
				})
				->when($assigned_to && !empty($assigned_to), function ($q) use ($assigned_to) {
					$q->whereIn('pm.assign_user_id', $assigned_to);
				})
				->when(!is_null($patientFilterSubquery), function ($q) use ($patientFilterSubquery) {
					$q->whereIn('patient_service_requests.patient_id', $patientFilterSubquery);
				})
				->when($branch_id && !empty($branch_id), function ($q) use ($branch_id, $branch_filter_type) {
					if (isset($branch_filter_type) && $branch_filter_type == 'include') {
						$q->whereIn('pm.branch_id', $branch_id);
					} elseif (isset($branch_filter_type) && $branch_filter_type == 'exclude') {
						$q->where(function ($sub) use ($branch_id) {
							$sub->whereNotIn('pm.branch_id', $branch_id)
								->orWhere('pm.branch_id', 0)->orWhere('pm.branch_id', null);
						});
					}
				})
				->where('agency.delete_flag', 'N')
				->groupBy('pm.agency_id', 'resolution_log.resolution')
				->orderBy('total', 'desc')
				->get();
		} else {
			$query = PatientServiceRequest::join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')
				->leftjoin('agency', function ($join) {
					$join->on('agency.id', '=', 'pm.agency_id');
					$join->where('agency.delete_flag', 'N');
				})->whereHas('patientServiceRequestRelationShip', function ($q) {
					$q->where('service_id', '!=', '')->where('del_flag', 'N');
				})
				->where('patient_service_requests.del_flag', 'N')
				->where('pm.deleted_flag', 'N')
				->select([
					DB::raw("DATE_FORMAT(patient_service_requests.created_at, '%m/%d/%Y') as service_date"),
					DB::raw('COUNT(*) as total'),
					'pm.agency_id',
					'agency.agency_name',
					'patient_service_requests.status'
				])
				->when($fromDate, function ($q) use ($fromDate, $toDate) {

					$q->whereBetween('patient_service_requests.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
				})
				->when($lastUpdatedDate, function ($q) use ($lastUpdatedDate) {
					$explode = explode('-', $lastUpdatedDate);

					$q->whereBetween('patient_service_requests.last_status_update', [date('Y-m-d', strtotime($explode[0])) . ' 00:00:00', date('Y-m-d', strtotime($explode[1])) . ' 23:59:59']);
				})
				->when(in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {

					$agencyids = Utility::getUserWiseAgencyDashboard();
					$agencyids[] = $auth['agency_fk'];

					if (!empty($agencyids)) {
						$implodeIds = implode('","', $agencyids);
						$query->whereIn('pm.agency_id', $implodeIds);
					}
				})
				->when($type, function ($q) use ($type) {
					$q->where('pm.type', $type);
				})
				->when($agency_fk && !empty($agency_fk), function ($q) use ($agency_fk, $agency_filter_type) {
					if (isset($agency_filter_type) && $agency_filter_type == 'include') {
						$q->whereIn('pm.agency_id', $agency_fk);
					} elseif (isset($agency_filter_type) && $agency_filter_type == 'exclude') {
						$q->whereNotIn('pm.agency_id', $agency_fk);
					}
				})
				->when($service_id && !empty($service_id), function ($q) use ($service_id, $service_filter_type) {
					if (isset($service_filter_type) && $service_filter_type == 'include') {
						$q->whereExists(function ($subQuery) use ($service_id) {
							$subQuery->select(\DB::raw(1))
								->from('patient_wise_service_requested as pwsr')
								->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
								->whereIn('pwsr.service_id', $service_id)
								->where('pwsr.del_flag', 'N');
						});
					} elseif (isset($service_filter_type) && $service_filter_type == 'exclude') {
						$q->whereNotExists(function ($subQuery) use ($service_id) {
							$subQuery->select(\DB::raw(1))
								->from('patient_wise_service_requested as pwsr')
								->whereRaw('pwsr.patient_service_request_id = patient_service_requests.id')
								->whereIn('pwsr.service_id', $service_id)
								->where('pwsr.del_flag', 'N');
						});
					}
				})
				->when($assigned_to && !empty($assigned_to), function ($q) use ($assigned_to) {
					$q->whereIn('pm.assign_user_id', $assigned_to);
				})
				->when(!is_null($patientFilterSubquery), function ($q) use ($patientFilterSubquery) {
					$q->whereIn('patient_service_requests.patient_id', $patientFilterSubquery);
				})
				->when($branch_id && !empty($branch_id), function ($q) use ($branch_id, $branch_filter_type) {
					if (isset($branch_filter_type) && $branch_filter_type == 'include') {
						$q->whereIn('pm.branch_id', $branch_id);
					} elseif (isset($branch_filter_type) && $branch_filter_type == 'exclude') {
						$q->where(function ($sub) use ($branch_id) {
							$sub->whereNotIn('pm.branch_id', $branch_id)
								->orWhere('pm.branch_id', 0);
						});
					}
				})
				->where('agency.delete_flag', 'N')
				->groupBy('pm.agency_id', 'patient_service_requests.status')
				->orderBy('total', 'desc')
				->get();
		}

		return $query;
	}

	public function checkAnyExistingMergeAppointmentId($appointmentId)
	{
		return Patient::where('merge_appointment_id', $appointmentId)->first();
	}

	/********************Please donot change for any this funcation will be used on API Side */
	public function checkForThirdPartyExistingDataApi($search,$agencyId){
		
		$date = str_replace('-', '/', $search['dob']);
		$dob  = date('Y-m-d', strtotime($date));
		$mobile = str_replace(['(', ')', ' ', '-'], '', $search['mobile']);
		// Step 1: Find patients by agency + type + phone
		$patients = Patient::where('deleted_flag', 'N')
			->where('agency_id', $agencyId)
			->where('type', $search['type'])
			->where(function ($q) use ($mobile) {
				$q->where('mobile', 'LIKE', "%$mobile%")
				->orWhere('phone', 'LIKE', "%$mobile%");
			})
			->where('dob',$dob)
			->orderBy('id','desc')
			->get();

		// No patient found
		if ($patients->count() == 0) {
			return null;
		}

		// If exactly 1 patient found → return directly
		if ($patients->count() == 1) {
			return $patients->first();
		}

		// If multiple found → validate other fields
		foreach ($patients as $patient) {
			if (
				strtolower($patient->first_name) == strtolower($search['first_name']) &&
				strtolower($patient->last_name) == strtolower($search['last_name']) &&
				(
					empty($search['gender']) ||
					strtolower($patient->gender) == strtolower($search['gender'])
				)
			) {
				return $patient;
			}
		}

		return null;
	
	}

	public function checkForExistingTaskHealthDataApi($search, $agencyId)
	{
		$date = str_replace('-', '/', $search['dob']);
		$dob  = date('Y-m-d', strtotime($date));

		// Normalize mobile
		$mobile = preg_replace('/[^0-9]/', '', $search['mobile']);
		if (strlen($mobile) == 11 && substr($mobile, 0, 1) == '1') {
			$mobile = substr($mobile, 1);
		}

		// Step 1: Find patients by agency + type + phone
		$patients = Patient::where('deleted_flag', 'N')
			->where('agency_id', $agencyId)
			->where('type', $search['type'])
			->where(function ($q) use ($mobile) {
				$q->where('mobile', 'LIKE', "%$mobile%")
				->orWhere('phone', 'LIKE', "%$mobile%");
			})
			->where('dob',$dob)
			->orderBy('id','desc')
			->get();
		// No patient found
		if ($patients->count() == 0) {
			$patients = Patient::where('deleted_flag', 'N')
				->where('agency_id', $agencyId)
				->where('type', $search['type'])
				->where('dob', $dob)
				->whereRaw('LCASE(first_name) LIKE "%' . strtolower($search['first_name']) . '%"')
				->whereRaw('LCASE(last_name) LIKE "%' . strtolower($search['last_name']) . '%"');
			if (isset($search['gender']) && $search['gender'] != "") {
				$patients->whereRaw('LCASE(gender) ="' . strtolower($search['gender']) . '"');
			}
			$patients = $patients->orderBy('id','desc')->get();
			if($patients->count() == 0){
				return null;
			}else{
				// If multiple found → validate other fields
				foreach ($patients as $patient) {
					if (
						strtolower($patient->first_name) == strtolower($search['first_name']) &&
						strtolower($patient->last_name) == strtolower($search['last_name']) &&
						(
							empty($search['gender']) ||
							strtolower($patient->gender) == strtolower($search['gender'])
						)
					) {
						
						return $patient;
					}
				}
			}
		}

		// If exactly 1 patient found → return directly
		if ($patients->count() == 1) {
			return $patients->first();
		}

		// If multiple found → validate other fields
		foreach ($patients as $patient) {
			if (
				strtolower($patient->first_name) == strtolower($search['first_name']) &&
				strtolower($patient->last_name) == strtolower($search['last_name']) &&
				(
					empty($search['gender']) ||
					strtolower($patient->gender) == strtolower($search['gender'])
				)
			) {
				
				return $patient;
			}
		}
		return null;
	}

	public function linkTaskHealth(int $patientId, int $taskHealthMasterId): void
	{
		Patient::where('id', $patientId)->update([
			'task_health_link' => $taskHealthMasterId,
			'updated_date'     => date('Y-m-d H:i:s'),
			'updated_by'       => auth()->id(),
		]);
	}

	public function getLinkHAAPatient($patientId)
    {
        return Patient::where('id', $patientId)->where('deleted_flag', 'N')->whereNotNull('link_hha_patient')->first();
    }

	/********************SearchData With Archived */
	public function getSearchPatientDetailsWithArchived($search)
	{
		$query = Patient::with(['agencyDetail:id,agency_name','users:id,first_name,last_name,agency_fk'])->where('deleted_flag', 'N')->where('is_archive',"!=",1);

		if (isset($search['first_name']) && $search['first_name'] != "") {
			$query->whereRaw('Lcase(first_name) LIKE "%' . strtolower($search['first_name']) . '%"');
		}

		if (isset($search['last_name']) && $search['last_name'] != "") {
			$query->whereRaw('Lcase(last_name) LIKE "%' . strtolower($search['last_name']) . '%"');
		}

		if (isset($search['dob_id']) && $search['dob_id'] != "") {
			$query->whereRaw('dob ="' . date('Y-m-d', strtotime($search['dob_id'])) . '"');
		}

		if (isset($search['gender']) && $search['gender'] != "") {
			$query->whereRaw('Lcase(gender) = "' . strtolower($search['gender']) . '"');
		}

		if (isset($search['type'])) {

			$query->where('type', $search['type']);
		}

		if (isset($search['ssn']) && $search['ssn'] != "") {

			$query->where('ssn', $search['ssn']);
		}

		if (isset($search['mobile_s']) && $search['mobile_s'] != "") {
			$mobile = preg_replace('/\D/', '', $search['mobile_s']); // removes everything except digits
    		$query->whereRaw("REPLACE(REPLACE(REPLACE(mobile, '(', ''), ')', ''), '-', '') = ?", [$mobile]);
		}
		$query = $query->where('agency_id', $search['agency_id']);
		return $query->get();

	}

	/*****************Search for MDO without is archived Duplicate*/
	public function getSearchPatientDetailsDup($search)
	{
		$query = Patient::with(['agencyDetail:id,agency_name','users:id,first_name,last_name,agency_fk'])->where('deleted_flag', 'N');

		if (isset($search['first_name']) && $search['first_name'] != "") {
			$query->whereRaw('Lcase(first_name) LIKE "%' . strtolower($search['first_name']) . '%"');
		}

		if (isset($search['last_name']) && $search['last_name'] != "") {
			$query->whereRaw('Lcase(last_name) LIKE "%' . strtolower($search['last_name']) . '%"');
		}

		if (isset($search['dob']) && $search['dob'] != "") {
			$query->whereRaw('dob ="' . date('Y-m-d', strtotime($search['dob'])) . '"');
		}

		if (isset($search['gender']) && $search['gender'] != "") {
			$query->whereRaw('Lcase(gender) = "' . strtolower($search['gender']) . '"');
		}

		if (isset($search['type'])) {

			$query->where('type', $search['type']);
		}

		if (isset($search['ssn']) && $search['ssn'] != "") {

			$query->where('ssn', $search['ssn']);
		}

		if (isset($search['mobile']) && $search['mobile'] != "") {
			$mobile = preg_replace('/\D/', '', $search['mobile']); // removes everything except digits
    		$query->whereRaw("REPLACE(REPLACE(REPLACE(mobile, '(', ''), ')', ''), '-', '') = ?", [$mobile]);
		}
		$query = $query->where('agency_id', $search['agency_id']);
		return $query->get();

	}

	public function getDataExportLatest($search,$dbColumn,$allStatusIds){
	
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N"  ' . $addCondition . '';

			$agencyids = Utility::getUserWiseAgency();
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
			
		} else {

			$addCondition = "";
			if ($auth->record_access != 'All') {
				$addCondition = " and patient_master.type='" . $auth->record_access . "'";
			}
			$where = 'patient_master.deleted_flag ="N" ' . $addCondition . '';
			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
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

		$additionConditions = $this->additionsConditions($search);
		$where .=$additionConditions;
		
		$query = Patient::selectRaw('patient_master.*')->join('agency',function($join){
			$join->on('agency.id','=','patient_master.agency_id');
		})->where('agency.delete_flag','N')
			->whereRaw($where);
		if (!empty($search['agency_status'])) {
			$agency_status = $search['agency_status'];
			$query->whereExists(function ($subQuery) use ($agency_status,$allStatusIds) {
				$subQuery->select(DB::raw(1))
					->from('patient_custom_data_submit as pcds')
					->whereColumn('pcds.patient_id', 'patient_master.id')
					->whereNull('deleted_at')
					->whereIn('field_id',$allStatusIds)
					->where('pcds.value', $agency_status);
			});
		}

		return $query;
	}

	private function additionsConditions($search){

		$where = "";
		if (isset($search['is_archive']) && $search['is_archive'] == "true") {
			
			$where .= ' and patient_master.is_archive = 1';
		} else {
			$where .= ' and patient_master.is_archive = 0';
		}

		$where .= $this->basicFilters($search);
		$where .= $this->dateFilters($search);
		$where .= $this->appointmentDateFilter($search);
		$where .= $this->serviceFilter($search);
		$where .= $this->statusFilter($search);
		$where .= $this->agencyFilter($search);
		$where .= $this->otherFilter($search);
		$where .= $this->searchUserFilter($search);
		$where .= $this->extraOtherFields($search);
		$where .= $this->agencyReviewLog($search);

		return $where;
	}

	private function basicFilters($search){
		$where = "";
		if (isset($search['first_name']) && $search['first_name'] != '') {
			$where .= ' and patient_master.full_name LIKE "%' . $search['first_name'] . '%"';
		}

		if (isset($search['type']) && $search['type'] != '') {
			$where .= ' and patient_master.type = "' . $search['type'] . '"';
		}
		if (isset($search['locationId']) && $search['locationId'] != '') {
			$where .= ' and patient_master.location_id = "' . $search['locationId'] . '"';
		}

		if (isset($search['mobile']) && $search['mobile'] != '') {
			$where .= ' and patient_master.mobile = "' . $search['mobile'] . '"';
		}

		if (isset($search['assign_user_id']) && $search['assign_user_id'] != '') {
			$where .= ' and patient_master.assign_user_id = "' . $search['assign_user_id'] . '"';
		}

		if (isset($search['dicipline']) && $search['dicipline'] != '') {
			$where .= ' and patient_master.diciplin = "' . $search['dicipline'] . '"';
		}
		
		if (isset($search['patient_code']) && $search['patient_code'] != '') {
			$where .= ' and patient_master.patient_code = "' . $search['patient_code'] . '"';
		}
		return $where;
	}

	private function dateFilters($search)
	{
		$where = '';

		$where .= $this->dateRange('created_date', 'patient_master.created_date', $search);
		$where .= $this->dateRange('due_date', 'patient_master.due_date', $search);
		$where .= $this->dateRange('completed_date', 'patient_master.completed_date', $search);
		$where .= $this->dateRange('follow_up_date', 'patient_master.follow_date', $search);
		$where .= $this->dateRange('traning_date', 'patient_master.traning_due_date', $search);
		$where .= $this->dateRange('inservice_date', 'patient_master.inservice_datetime', $search);
		$where .= $this->dateRange('last_status_update', 'patient_master.last_status_update', $search);

		return $where;
	}

	private function dateRange($key, $column, $search)
	{
		if (empty($search[$key])) return '';

		$explode = explode('-', $search[$key]);

		return ' and DATE_FORMAT(' . $column . ',"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '"
				and DATE_FORMAT(' . $column . ',"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
	}

	private function appointmentDateFilter($search)
	{
		if (empty($search['appointment_date'])) return '';

		$explode = explode('-', $search['appointment_date']);

		if (isset($explode[1])) {
			return ' and ((DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '"
				and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '")
				OR (DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '"
				and DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"))';
		}

		return ' and ((DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '")
			OR (DATE_FORMAT(patient_master.telehealth_date_time,"%Y-%m-%d") = "' . date('Y-m-d', strtotime($explode[0])) . '"))';
	}

	private function serviceFilter($search){
		$where = '';

		if (!empty($search['service_id'])) {
			$explode = explode(',', $search['service_id']);
			$conditions = [];

			foreach ($explode as $vals) {
				if (($search['service_filter_type'] ?? '') == 'exclude') {
					$conditions[] = '!FIND_IN_SET("' . $vals . '", patient_master.service_id)';
				} else {
					$conditions[] = 'FIND_IN_SET("' . $vals . '", patient_master.service_id)';
				}
			}

			$glue = (($search['service_filter_type'] ?? '') == 'exclude') ? ' AND ' : ' OR ';
			$where .= ' and (' . implode($glue, $conditions) . ')';
		}

		return $where;
	}

	private function statusFilter($search){
		$where = "";
		if (isset($search['status']) && $search['status'] != '') {
			$explode = explode(',', $search['status']);
			$final = [];
			foreach ($explode as $vsl) {
				if ($vsl == 'Signed-SentBacktotheAgency') {
					$vsl = 'Signed & Sent Back to the Agency';
				}
				if ($vsl == 'TelehealthCompleted-Pending Forms') {
					$vsl = 'Telehealth Completed , Pending Forms';
				}
				if ($vsl == 'PatientAskedtoReschedule') {
					$vsl = 'Patient Asked to Reschedule';
				}
				$final[] = $vsl;
			}
			$quoted = array_map(function ($item) {
				return '"' . addslashes($item) . '"';
			}, $final);

			// Build the WHERE clause
			$where .= ' AND LOWER(patient_master.status) IN (' . implode(',', $quoted) . ')';
		}

		return $where;
	}

	private function agencyFilter($search){
		$where = "";
		if (isset($search['agency_fk']) && $search['agency_fk'] != '') {
			$agency_fk = str_replace(',', '","', $search['agency_fk']);
			if(isset($search['agency_filter_type']) && $search['agency_filter_type'] == 'include'){
				$where .= ' and patient_master.agency_id IN( "' . $agency_fk . '")';
			}elseif(isset($search['agency_filter_type']) && $search['agency_filter_type'] == 'exclude'){
				$where .= ' and patient_master.agency_id NOT IN( "' . $agency_fk . '")';
			}
		}

		return $where;
	}

	private function otherFilter($search){
		$auth = auth()->user();
		$where ="";
		if (isset($search['is_past_show']) && $search['is_past_show'] != '') {
			$where .= ' and patient_master.appointment_date < "' . now() . '"';
		}

		if (isset($search['dob']) && $search['dob'] != '') {
			$where .= ' and patient_master.dob = "' . date('Y-m-d', strtotime($search['dob'])) . '"';
		}
	
		if (isset($search['traning_status']) && $search['traning_status'] != '') {
			$traning_status = str_replace(',', '","', $search['traning_status']);
			$where .= ' and patient_master.training_status IN( "' . $traning_status . '")';
		}

		if (isset($search['transition_aid']) && $search['transition_aid'] != '') {
			$transistion_aid = $search['transition_aid'];
			if ($transistion_aid != 0) {
				$where .= ' and patient_master.transition_aid = "' . $transistion_aid . '"';
			} else {
				$where .= ' and (patient_master.transition_aid = "' . $transistion_aid . '" OR patient_master.transition_aid IS NULL)';
			}
		}

		if ($auth->restrict_user ==1) {
			$where .= ' and patient_master.created_by ="' . $auth->id . '"';
		}

		if (isset($search['sms_status']) && $search['sms_status'] != '') {
			$where .= ' and patient_master.patient_sms_flag ="' . $search['sms_status'] . '"';
		}
		

		if (isset($search['medication_list']) && $search['medication_list'] != '') {
			if($search['medication_list'] =='Yes'){
				$where .= ' AND ((medication_count >= 1 OR no_medication_taken = 1)) ';
			}else{
				$where .= ' AND medication_count = 0 AND (no_medication_taken != 1 OR no_medication_taken IS NULL) ';
			}
		}
		
		if (isset($search['mdo_tag']) && $search['mdo_tag'] != '') {
			if($search['mdo_tag'] =='Yes'){
				$where .= ' AND mdo_tag_count >=1';
			}else{
				$where .= ' AND mdo_tag_count ==0';
			}
		}
		
		if (isset($search['insurance_elg']) && $search['insurance_elg'] != '') {
			if($search['insurance_elg'] =='Yes'){
				$where .= ' AND insurance_elg_count >=1';
			}else{
				$where .= ' AND insurance_elg_count ==0';
			}
		}
		return $where;
	}

	private function searchUserFilter($search){
		$where = '';
	
		$where .= $this->searchUserField('created_by', 'patient_master.created_by', $search);
		$where .= $this->searchUserField('last_status_updated_by_id', 'patient_master.last_status_update_by', $search);
		$where .= $this->searchUserField('language_id', 'patient_master.language', $search);
		$where .= $this->searchUserField('referral_type', 'patient_master.referral_type', $search);
		return $where;
	}

	private function searchUserField($key, $column, $search)
	{
		if (empty($search[$key])) return '';
		if (isset($search[$key]) && $search[$key] =="undefined") return '';
		return ' and ' . $column . ' = "' . $search[$key] . '"';
	}

	private function extraOtherFields($search){
		$where = "";
		
		if (!empty($search['filter_branch_id'])) {
			if(isset($search['branch_filter_type']) && $search['branch_filter_type'] == 'include'){
				$where .=" and branch_id = ".$search['filter_branch_id'];

			}elseif(isset($search['branch_filter_type']) && $search['branch_filter_type'] == 'exclude'){
				$where .= " AND (
					branch_id != " . (int) $search['filter_branch_id'] . "
					OR branch_id = 0
					OR branch_id IS NULL
				)";
			}
		}

		if(!empty($search['state'])){
			$where .=' and patient_master.state ='.$search['state'];
		}
		if(isset($search['record_read']) && $search['record_read'] == 0){
			$where .=' and patient_master.record_read =0';
		}

		if(!empty($search['agency_rep']) && $search['agency_rep'] !=""){
			if($search['agency_rep'] !='undefined'){
				$where .=' and patient_master.agency_user_id ='.$search['agency_rep'];
			}
		}
		return $where;
	}
	public function checkForExistingRecordsbyCronjob($record,$mobile){
		return Patient::where([
            ['agency_id', $record->agency_id],
            ['first_name', $record->first_name],
            ['last_name', $record->last_name],
            [DB::raw('LOWER(type)'), strtolower($record->type)],
            ['dob', $record->dob],
            ['deleted_flag', 'N'],
            [DB::raw('LOWER(gender)'), strtolower($record->gender)],
        ])->whereRaw("REPLACE(REPLACE(REPLACE(mobile, '(', ''), ')', ''), '-', '') = ?", [$mobile])->first();

	}

	private function agencyReviewLog($search){
		$where = "";
		$auth = auth()->user();

		if ($auth->agency_fk != "" && !empty($search['agency_enable_review'])) {
			if ($search['agency_enable_review'] == "true") {
				$where .= ' and patient_master.is_reviewed = 1';
			} else {
				$where .= ' and patient_master.is_reviewed = 0';
			}
		}

		return $where;
	}
}