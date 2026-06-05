<?php

namespace App\Services;

use App\Model\Patient;
use App\Model\PatientWiseServiceRequest;
use App\Model\HHACaregivers;
use App\Model\HHAPatient;
use App\Model\Robort;
use App\Agency;
use App\Model\ThirdPartyPatientMaster;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use App\Model\PatientServiceRequest;

class AppointmentDashboardService
{
	public static function getTotalStatusCountData()
	{
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$query = Patient::select('patient_master.id', 'status')->whereRaw($where)
			->where('patient_master.deleted_flag', 'N');
		$query = $query->get();
		return $query;
	}

	public function getMonthWisePatientData($search_data, $groupBy)
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		if (!empty($search_data)) {
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($search_data['start_date'])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($search_data['end_date'])) . '"';
		}
		$query = Patient::selectRaw('YEAR(created_date) as year, WEEK(created_date) as week,MONTH(created_date) as month, COUNT(*) as total_records,MONTHNAME(created_date) as month_name,DAYNAME(created_date) as day')
			->groupBy($groupBy)
			->whereRaw($where)
			->where('patient_master.deleted_flag', 'N');
		$query = $query->get()->toArray();
		return $query;
	}

	public function getDynamicYears()
	{
		$years = Patient::select(DB::raw('YEAR(created_date) as year'))
			->distinct()
			->orderBy('year', 'asc')
			->where('patient_master.deleted_flag', 'N')
			->where('patient_master.archived_at', NULL)
			->pluck('year');

		return $years;  // Return as JSON or use as needed
	}

	public function getMonthWiseAgencyData($search_data, $groupBy)
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		if (!empty($search_data)) {
			$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($search_data['start_date'])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($search_data['end_date'])) . '"';
		}
		$query = Patient::with('agencyDetail:id,agency_name')->selectRaw("YEAR(created_date) as year, WEEK(created_date) as week,MONTH(created_date) as month, COUNT(*) as total_records,MONTHNAME(created_date) as month_name,DAYNAME(created_date) as day,agency_id")
			->groupBy($groupBy)
			->groupBy('agency_id')
			->whereRaw($where)
			->where('patient_master.deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		$query = $query->orderBy('month', 'asc')->get()->toArray();
		return $query;
	}

	public function getMonthWiseLocationData($year)
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($year . '-01-01')) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($year . '-12-31')) . '"';
		$query = Patient::select(
			DB::raw("DATE_FORMAT(created_date, '%Y-%m') as month"),
			DB::raw("MONTHNAME(created_date) as month_name"),
			DB::raw("COUNT(id) as count")
		)
			->groupBy(DB::raw("DATE_FORMAT(created_date, '%$year-%m')"))
			->whereRaw($where)
			->where('patient_master.deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		$query = $query->orderBy('month', 'asc')->get()->toArray();
		return $query;
	}

	public function getMonthWiseCompareData($year)
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$where .= ' and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($year . '-01-01')) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($year . '-12-31')) . '"';
		$query = Patient::select(
			DB::raw("DATE_FORMAT(created_date, '%Y-%m') as month"),
			DB::raw("MONTHNAME(created_date) as month_name"),
			DB::raw("COUNT(id) as count"),
			"agency_id"
		)
			->groupBy(DB::raw("DATE_FORMAT(created_date, '%$year-%m')"))
			->groupBy('agency_id')
			->whereRaw($where)
			->where('patient_master.deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		$query = $query->orderBy('month', 'asc')->get()->toArray();
		return $query;
	}

	public function getAgencyWiseAppointmentData($from_date, $to_date, $type = "", $lastUpdatedDate = "", $agency_fk = "", $agency_filter_type = "", $service_id = "", $service_filter_type = "", $assigned_to = "", $medication_list = "", $insurance_elg = "", $mdo_tag = "", $branch_id = "", $branch_filter_type = "")
	{
		$auth = auth()->user();

		$patientFilterSubquery = null;
		if (!empty($medication_list) || !empty($insurance_elg) || !empty($mdo_tag)) {
			$patientFilterSubquery = Patient::select('id')
				->where('deleted_flag', 'N')
				->whereNull('archived_at')
				->when(!empty($medication_list), function ($query) use ($medication_list) {
					if ($medication_list == 'Yes') {
						$query->where('medication_count', '>=', 1);
					} else {
						$query->where('medication_count', '=', 0);
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

		return	PatientServiceRequest::join('patient_master as pm', 'patient_service_requests.patient_id', '=', 'pm.id')->select([
			DB::raw("DATE_FORMAT(patient_service_requests.created_at, '%m/%d/%Y') as service_date"),
			DB::raw('COUNT(*) as count'),
			'pm.agency_id'
		])->whereHas('patientServiceRequestRelationShip', function ($q) {
			$q->where('service_id', '!=', '')->where('del_flag', 'N');
		})
			->when($from_date, function ($query) use ($from_date, $to_date) {

				$query->whereBetween('patient_service_requests.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			})
			->when($lastUpdatedDate, function ($query) use ($lastUpdatedDate) {
				$explode = explode('-', $lastUpdatedDate);

				$query->whereBetween('patient_service_requests.last_status_update', [date('Y-m-d', strtotime($explode[0])) . ' 00:00:00', date('Y-m-d', strtotime($explode[1])) . ' 23:59:59']);
			})
			->when(in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2, function ($query) use ($auth) {

				$agencyids = Utility::getUserWiseAgencyDashboard();
				$agencyids[] = $auth['agency_fk'];

				if (!empty($agencyids)) {
					$implodeIds = implode('","', $agencyids);
					$query->whereIn('pm.agency_id', $implodeIds);
				}
			})
			->where('pm.deleted_flag', 'N')
			->where('patient_service_requests.del_flag', 'N')

			->when($type, function ($query) use ($type) {
				$query->where('pm.type', $type);
			})
			->when($agency_fk && !empty($agency_fk), function ($query) use ($agency_fk, $agency_filter_type) {
				if (isset($agency_filter_type) && $agency_filter_type == 'include') {
					$query->whereIn('pm.agency_id', $agency_fk);
				} elseif (isset($agency_filter_type) && $agency_filter_type == 'exclude') {
					$query->whereNotIn('pm.agency_id', $agency_fk);
				}
			})
			->when($service_id && !empty($service_id), function ($query) use ($service_id, $service_filter_type) {
				if (isset($service_filter_type) && $service_filter_type == 'include') {
					$query->whereHas('patientServiceRequestRelationShip', function ($subQuery) use ($service_id) {
						$subQuery->whereIn('service_id', $service_id);
					});
				} elseif (isset($service_filter_type) && $service_filter_type == 'exclude') {
					$query->whereHas('patientServiceRequestRelationShip', function ($subQuery) use ($service_id) {
						$subQuery->whereNotIn('service_id', $service_id);
					});
				}
			})
			->when($assigned_to && !empty($assigned_to), function ($query) use ($assigned_to) {
				$query->whereIn('pm.assign_user_id', $assigned_to);
			})
			->when(!is_null($patientFilterSubquery), function ($query) use ($patientFilterSubquery) {
				$query->whereIn('patient_service_requests.patient_id', $patientFilterSubquery);
			})
			->when($branch_id && !empty($branch_id), function ($query) use ($branch_id, $branch_filter_type) {
				if (isset($branch_filter_type) && $branch_filter_type == 'include') {
					$query->whereIn('pm.branch_id', $branch_id);
				} elseif (isset($branch_filter_type) && $branch_filter_type == 'exclude') {
					$query->where(function ($sub) use ($branch_id) {
						$sub->whereNotIn('pm.branch_id', $branch_id)
							->orWhere('pm.branch_id', 0)
							->orWhere('pm.branch_id', null);
					});
				}
			})
			->whereHas('patient.agencyDetail', function ($q) {
				$q->where('agency.delete_flag', 'N');
			})
			->groupBy('pm.agency_id')
			->orderBy('count', 'desc')
			->get()->toArray();
	}

	public function getServicesWiseAppointmentData()
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$whereService = " del_flag = 'N' AND service_id != '' ";
		$query = PatientWiseServiceRequest::with('services:id', 'patient:id')->select(DB::raw("COUNT(id) as count"), "service_id", "patient_id")
			->groupBy('patient_id', 'service_id')
			->whereRaw($whereService);
		$query->whereHas('services', function ($pQuery) {
			$pQuery->where('del_flag', 'N')->where('is_disable', 1);
		});
		$query->whereHas('patient', function ($pQuery) use ($where) {
			$pQuery->whereRaw($where);
		});
		$query = $query->orderBy('count', 'asc')->get()->toArray();
		return $query;
	}

	public function getLocationWiseAppointmentData($from_date, $to_date)
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$query = Patient::select(DB::raw("COUNT(id) as count"), "location_id")
			->groupBy('location_id')
			->whereRaw($where);
		if ($from_date != "" && $to_date != "") {
			$query->whereDate('patient_master.created_date', '>=', $from_date)->whereDate('patient_master.created_date', '<=', $to_date);
		}
		$query = $query->orderBy('count', 'asc')->get()->toArray();
		return $query;
	}

	public function getUserWiseAppointmentData($from_date, $to_date)
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$query = Patient::select(DB::raw("COUNT(id) as count"), "created_by")
			->groupBy('created_by')
			->whereRaw($where);
		if ($from_date != "" && $to_date != "") {
			$query->whereDate('patient_master.created_date', '>=', $from_date)->whereDate('patient_master.created_date', '<=', $to_date);
		}
		$query = $query->orderBy('count', 'desc')->get()->toArray();
		return $query;
	}

	public static function getTotalCountData($type = "", $where = "")
	{
		if ($type) {
			$where .= " and patient_master.type='" . $type . "'";
			if ($where == '') {
				$where .= " and patient_master.type='" . $type . "'";
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$query = Patient::select('patient_master.id', 'status')->whereRaw($where);
		$query = $query->get();
		return $query;
	}

	public function totalHHACaregiverCount()
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'deleted_at IS NULL';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_fk IN("' . $implodeIds . '")';
			}
		} else {
			$where .= " deleted_at IS NULL ";
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_fk IN("' . $implodeIds . '")';
			}
		}
		$query = HHACaregivers::select('id')->where('deleted_at', NULL)->whereRaw($where);
		return $query->get();
	}

	public function totalHHAPatientCount()
	{
		$where = '';
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'deleted_at IS NULL';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_fk IN("' . $implodeIds . '")';
			}
		} else {
			$where .= " deleted_at IS NULL ";
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_fk IN("' . $implodeIds . '")';
			}
		}
		$query = HHAPatient::select('id')->where('deleted_at', NULL)->groupBy('patient_id')->whereRaw($where);
		return $query->get();
	}

	public function totalRemoteClientCount()
	{
		$where = '';
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'del_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		} else {
			$where .= " del_flag = 'N' ";
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		}
		$query = Robort::select('id')->where('del_flag', 'N')->whereRaw($where);
		return $query->get();
	}

	public function totalVisitingCounts()
	{
		$where = '';
		$auth = auth()->user();
		$where .= " deleted_flag = 'N' ";
		// if (in_array($auth['user_type_fk'], array(184))) {
		// 	$where = 'deleted_flag ="N"';
		// 	$agencyids = Utility::getUserWiseAgencyDashboard();
		// 	if(!empty($agencyids)){
		// 		$implodeIds = implode('","', $agencyids);
		// 		$where .= ' and agency_id IN("' . $implodeIds . '")';
		// 	}
		// } else{
		// 	$where .= " deleted_flag = 'N' " ;
		// }
		// if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {

		// 	$agencyids = Utility::getUserWiseAgencyDashboard();
		// 	$agencyids[] = $auth['agency_fk'];

		// 	if(!empty($agencyids)){
		// 		$implodeIds = implode('","', $agencyids);
		// 		$where .= ' and agency_id IN("' . $implodeIds . '")';
		// 	}
		// }
		$query = ThirdPartyPatientMaster::select('id')->where('deleted_flag', 'N')->whereRaw($where);
		$query->where('platform_type', '!=', 'arla');
		return $query->get();
	}

	public static function totalCountForAgencies()
	{
		$where = '';
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$where = 'delete_flag ="N"';
			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and id IN("' . $implodeIds . '")';
			}
		} else {
			$where .= " delete_flag = 'N' ";
		}
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and id IN("' . $implodeIds . '")';
			}
		}
		$query = Agency::select('id')->where('delete_flag', 'N')->whereRaw($where);
		return $query->get();
	}

	public function getAgencyAppointmentData()
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$query = Patient::with(['agencyDetail:id,agency_name'])->select(DB::raw("COUNT(id) as count"), "agency_id")
			->whereNotNull('agency_id')
			->groupBy('agency_id')
			->whereRaw($where);
		$query = $query->orderBy('count', 'desc')->skip(0)->take(5)->get()->toArray();
		return $query;
	}

	public function getUserAppointmentData()
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$query = Patient::with(['users:id,first_name,last_name'])->select(DB::raw("COUNT(id) as count"), "created_by")
			->whereNotNull('created_by')
			->groupBy('created_by')
			->whereRaw($where);
		$query = $query->orderBy('count', 'desc')->skip(0)->take(5)->get()->toArray();
		return $query;
	}

	public function getLocationAppointmentData()
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$where .= ' and patient_master.archived_at IS NULL';
		$query = Patient::with(['locations:id,location_name,address1,address2,city,state,zip_code'])->select(DB::raw("COUNT(id) as count"), "location_id")
			->groupBy('location_id')
			->whereNotNull('location_id')
			->whereRaw($where);
		$query->whereHas('locations', function ($pQuery) {
			$pQuery->where('delete_flag', 'N');
		});
		$query = $query->orderBy('count', 'desc')->skip(0)->take(5)->get()->toArray();
		return $query;
	}

	public function getWhereConditionForAgency()
	{
		$auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
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

			$serviceIds = Utility::getServiceByAgency();
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
		if (in_array($auth->agency_fk, array(5, 6)) || $auth->login_type_fk == 2) {

			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];

			if (!empty($agencyids)) {
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		return $where;
	}
}
