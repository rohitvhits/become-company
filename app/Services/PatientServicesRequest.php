<?php

namespace App\Services;

use App\Model\Patient;
use App\Helpers\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\PatientServiceRequest;
use App\Model\PatientWiseServiceRequest;
use App\Model\PatientServiceRequestLog;
use App\Model\HubPatientServiceRequest;
use Carbon\Carbon;

class PatientServicesRequest
{

	public function save($data)
	{
		$auth = auth()->user();
		if (isset($data['updated_flag']) && $data['updated_flag'] == 1) {
			$data['created_at'] = $data['created_at'];
			$data['created_by'] = $auth['id'];
		} else {
			if (isset($data['flag']) && $data['flag'] == 1) {
				$data['created_at'] = $data['created_at'];
				$data['created_by'] = $data['created_by'];
			} else {
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['created_by'] = $auth['id'];
			}
		}

		$insert = new PatientServiceRequest($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		if (isset($auth['id'])) {
			$data['updated_by'] = $auth['id'];
		}


		$update = PatientServiceRequest::where($where)->update($data);
		return $update;
	}

	public function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = PatientServiceRequest::where($where)->update($data);
		return $update;
	}

	public function getServiceIdByPatient($id)
	{
		$patient_service_request_id = PatientServiceRequest::where('patient_id', $id)->firstOrFail();

		$getServiceIdByPatient = PatientWiseServiceRequest::where('patient_service_request_id', $patient_service_request_id->id)->pluck('service_id')->toArray();
		return $getServiceIdByPatient;
	}

	public function getAllServiceList($pid)
	{
		return PatientServiceRequest::with(['userDetails:id,first_name,last_name', 'patientServiceRequestRelationShip.services:id,name', 'completedUserDetails:id,first_name,last_name'])->where('patient_id', $pid)->orderBy('created_at', 'desc')->paginate(50);
	}

	public function getPatientService($patientId)
	{

		return PatientServiceRequest::with(['patientServiceRequestRelationShip', 'patientServiceRequestRelationShip.services:id,name'])->whereHas('patientServiceRequestRelationShip.services')->where('patient_id', $patientId)->get();
	}

	public function getByPatientDetails($patient_id)
	{
		return PatientServiceRequest::where('id', $patient_id)->first();
	}

	public function serviceWiseList($service_id)
	{
		return PatientWiseServiceRequest::with('services')->where('patient_service_request_id', $service_id)->orderBy('id', 'desc')->paginate(10);
	}

	public function patientServiceRequestData($serviceId)
	{
		return PatientWiseServiceRequest::where('id', $serviceId)->first();
	}

	public  function updateServiceData($where, $data)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if (isset($auth['id'])) {
			$data['updated_by'] = $auth['id'];
		}

		$update = PatientWiseServiceRequest::where($where)->update($data);

		$patientServiceRequestData = $this->patientServiceRequestData($where);
		$checkCountRememberingServiceCount = PatientWiseServiceRequest::where('patient_service_request_id', $patientServiceRequestData->patient_service_request_id)->WhereNotNull('document')->count();
		if ($checkCountRememberingServiceCount == 0) {
			$updateStatus = PatientServiceRequest::where('id', $patientServiceRequestData->patient_service_request_id)
				->update(['status' => 'completed']);
		}

		return $update;
	}



	public function insertLogPatientReq($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];

		$insert = new PatientServiceRequestLog($data);
		$insert->save();
		$insertId = $insert->id;

		return $insertId;
	}


	public function reqServiceLog($serviceId)
	{

		return PatientServiceRequestLog::with('patient', 'services')->where('service_request_id', $serviceId)->orderBy('id', 'desc')->paginate(10);
	}

	public function getList($search, $paginate = "")
	{

		$query = PatientServiceRequest::with(['patient.agencyDetail', 'patientServiceRequestRelationShip.services', 'appointmentDetails.location']);
		if (isset($search['status']) && $search['status'] != "") {
			$query->where('patient_service_requests.status', $search['status']);
		}
		if ($search['created_date'] != "") {
			$explode = explode('-', $search['created_date']);
			$query->whereDate('patient_service_requests.created_at', '>=', $explode[0])->whereDate('patient_service_requests.created_at', '<=', $explode[1]);
		}
		if ($search['completed_date'] != "") {
			$explode = explode('-', $search['completed_date']);
			$query->whereDate('patient_service_requests.completed_date', '>=', $explode[0])->whereDate('patient_service_requests.completed_date', '<=', $explode[1]);
		}

		if (isset($search['agency_fk']) && $search['agency_fk'] != "") {
			$query->whereHas('patient', function ($sQuery) use ($search) {

				if (isset($search['agency_fk']) && $search['agency_fk'] != "") {

					$sQuery->whereIn('agency_id', $search['agency_fk']);
				}
			});
		}
		if ($search['patient_code'] != "" || $search['first_name'] != "" || $search['mobile'] != "" || $search['diciplin_id'] != "" || $search['type'] != "" || $search['follow_up_date'] != "") {

			$query->whereHas('patient', function ($sQuery) use ($search) {


				if ($search['patient_code'] != "") {
					$sQuery->where('patient_code', $search['patient_code']);
				}
				if ($search['first_name'] != "") {
					$sQuery->whereRaw('CONCAT(first_name,last_name) LIKE %"' . str_replace(' ', '', $search['first_name']) . '"%');
				}

				if ($search['mobile'] != "") {
					$sQuery->where('mobile', $search['mobile']);
				}

				if ($search['diciplin_id'] != "") {
					$sQuery->where('diciplin', $search['diciplin_id']);
				}
				if ($search['type'] != "") {
					$sQuery->where('type', $search['type']);
				}
			});
		}

		if ($search['appointment_date'] != "") {
			$query->whereHas('appointmentDetails', function ($lQuery) use ($search) {
				if ($search['appointment_date'] != "") {
					$explode = explode('-', $search['appointment_date']);
					$lQuery->whereDate('appointment_date', '>=', $explode[0])->whereDate('appointment_date', '<=', $explode[1]);
				}
			});
		}

		if ($paginate != "") {
			$query = $query->orderBy('id', 'desc')->get();
		} else {
			$query = $query->orderBy('id', 'desc')->paginate(50);
		}

		return $query;
	}

	public function getDetailsById($pid)
	{
		return PatientServiceRequest::where('id', $pid)->first();
	}

	public function patientRequestedServiceList($searchQuery)
	{
		$agencyids = Utility::getUserWiseAgency();

		// Optimized: Constrain eager loads to only needed columns to reduce memory & query time
		$query = PatientServiceRequest::with([
			'patient' => function ($q) {
				$q->select('id', 'agency_id', 'first_name', 'middle_name', 'last_name', 'patient_code', 'mobile', 'gender', 'dob', 'type', 'diciplin', 'status', 'location_id', 'location_branch', 'appointment_date', 'appoinment_time_id', 'due_date', 'fu_date', 'follow_date', 'inservice_datetime', 'traning_due_date', 'training_status', 'assign_user_id', 'archived_at', 'record_read', 'remarks', 'appointment_mode', 'patient_sms_flag', 'deleted_flag', 'referral_type', 'hha_id', 'link_hha_caregiver', 'link_hha_patient', 'alaycare_id', 'robort_id', 'platform_type', 'language', 'transition_aid', 'medication_count', 'insurance_elg_count', 'mdo_tag_count');
			},
			'patient.agencyDetail:id,agency_name,delete_flag',
			'patient.assignToUser:id,first_name,last_name',
			'userDetails:id,first_name,last_name',
			'patientServiceRequestRelationShip.requestService:id,name',
			'statusUserDetails:id,first_name,last_name',
		])->where('del_flag', 'N');

		// Optimized: Use a single whereHas on 'patient' with a join for base conditions + all patient-level filters
		// instead of many separate whereHas calls that each generate an EXISTS subquery
		$query->whereHas('patient', function ($q) use ($searchQuery, $agencyids) {
			// Base conditions (previously 3 separate whereHas calls)
			$q->where('deleted_flag', 'N');
			$q->whereNull('archived_at');

			// Agency filter via joined agency table (replaces whereHas on patient.agencyDetail)
			$q->whereExists(function ($sub) {
				$sub->select(DB::raw(1))
					->from('agency')
					->whereColumn('agency.id', 'patient_master.agency_id')
					->where('agency.delete_flag', 'N');
			});

			// Agency include/exclude filter
			if (!empty($searchQuery['agency_fk'])) {
				$filterType = isset($searchQuery['agency_filter_type']) ? trim($searchQuery['agency_filter_type']) : 'include';
				if ($filterType == 'include') {
					$q->whereIn('agency_id', $searchQuery['agency_fk']);
				} elseif ($filterType == 'exclude') {
					$q->whereNotIn('agency_id', $searchQuery['agency_fk']);
				}
			} elseif (count($agencyids) > 0) {
				$q->whereIn('agency_id', $agencyids);
			}

			// All patient-column filters consolidated into single whereHas
			if (!empty($searchQuery['assign_user_id'])) {
				$q->whereIn('assign_user_id', $searchQuery['assign_user_id']);
			}
			if (!empty($searchQuery['training_status'])) {
				$q->whereIn('training_status', $searchQuery['training_status']);
			}
			if (!empty($searchQuery['sms_status'])) {
				$q->whereIn('patient_sms_flag', $searchQuery['sms_status']);
			}
			if (!empty($searchQuery['patient_code'])) {
				$q->where('patient_code', 'like', '%' . $searchQuery['patient_code'] . '%');
			}
			if (!empty($searchQuery['first_name'])) {
				$q->whereRaw('CONCAT_WS(" ", first_name, last_name) LIKE ?', ['%' . $searchQuery['first_name'] . '%']);
			}
			if (!empty($searchQuery['mobile'])) {
				$q->where('mobile', 'like', '%' . $searchQuery['mobile'] . '%');
			}
			if (!empty($searchQuery['due_date'])) {
				$exploderDueDate = explode('-', $searchQuery['due_date']);
				if (count($exploderDueDate) == 2) {
					$startDate = Carbon::parse(trim($exploderDueDate[0]))->toDateString();
					$endDate = Carbon::parse(trim($exploderDueDate[1]))->toDateString();
					$q->whereBetween('due_date', [$startDate, $endDate]);
				}
			}
			if (!empty($searchQuery['appointment_date'])) {
				$exploderAppDate = explode('-', $searchQuery['appointment_date']);
				if (count($exploderAppDate) == 2) {
					$startDate = Carbon::parse(trim($exploderAppDate[0]))->toDateString();
					$endDate = Carbon::parse(trim($exploderAppDate[1]))->toDateString();
					$q->whereBetween('appointment_date', [$startDate, $endDate]);
				}
			}
			// Removed duplicate diciplin/type filters — only exact match needed
			if (!empty($searchQuery['diciplin'])) {
				$q->where('diciplin', $searchQuery['diciplin']);
			}
			if (!empty($searchQuery['type'])) {
				$q->where('type', $searchQuery['type']);
			}
			// Removed duplicate inservice_date filter block
			if (!empty($searchQuery['inservice_date'])) {
				$exploderServiceDate = explode('-', $searchQuery['inservice_date']);
				if (count($exploderServiceDate) == 2) {
					$startDate = Carbon::parse(trim($exploderServiceDate[0]))->format('Y-m-d 00:00:00');
					$endDate = Carbon::parse(trim($exploderServiceDate[1]))->format('Y-m-d 23:59:59');
					$q->whereBetween('inservice_datetime', [$startDate, $endDate]);
				}
			}
			if (!empty($searchQuery['follow_up_date'])) {
				$exploderFollowDate = explode('-', $searchQuery['follow_up_date']);
				if (count($exploderFollowDate) == 2) {
					$startDate = Carbon::parse(trim($exploderFollowDate[0]))->toDateString();
					$endDate = Carbon::parse(trim($exploderFollowDate[1]))->toDateString();
					$q->whereBetween('follow_date', [$startDate, $endDate]);
				}
			}
			// Removed duplicate traning_date filter — only date range needed
			if (!empty($searchQuery['traning_date'])) {
				$exploderTraningDate = explode('-', $searchQuery['traning_date']);
				if (count($exploderTraningDate) == 2) {
					$startDate = Carbon::parse(trim($exploderTraningDate[0]))->toDateString();
					$endDate = Carbon::parse(trim($exploderTraningDate[1]))->toDateString();
					$q->whereBetween('traning_due_date', [$startDate, $endDate]);
				}
			}
			if (!empty($searchQuery['locationId'])) {
				$q->whereIn('location_id', $searchQuery['locationId']);
			}
			if (!empty($searchQuery['transition_aid'])) {
				$q->where('transition_aid', $searchQuery['transition_aid']);
			}
			if (!empty($searchQuery['language_id'])) {
				$q->where('language', $searchQuery['language_id']);
			}
			if (!empty($searchQuery['medication_list'])) {
				if ($searchQuery['medication_list'] == 'Yes') {
					$q->where('medication_count', '>=', 1);
				} else {
					$q->where('medication_count', '=', 0);
				}
			}
			if (!empty($searchQuery['insurance_elg'])) {
				if ($searchQuery['insurance_elg'] == 'Yes') {
					$q->where('insurance_elg_count', '>=', 1);
				} else {
					$q->where('insurance_elg_count', '=', 0);
				}
			}
			if (!empty($searchQuery['mdo_tag'])) {
				if ($searchQuery['mdo_tag'] == 'Yes') {
					$q->where('mdo_tag_count', '>=', 1);
				} else {
					$q->where('mdo_tag_count', '=', 0);
				}
			}
		});

		// Service relationship filter (must remain separate whereHas since it's a different table)
		$query->whereHas('patientServiceRequestRelationShip', function ($q) {
			$q->where('service_id', '!=', '')->where('del_flag', 'N');
		});

		if (!empty($searchQuery['service_id'])) {
			$filterType = isset($searchQuery['service_filter_type']) ? trim($searchQuery['service_filter_type']) : 'include';
			if ($filterType == 'include') {
				$query->whereHas('patientServiceRequestRelationShip', function ($q) use ($searchQuery) {
					$q->whereIn('service_id', $searchQuery['service_id']);
				});
			} elseif ($filterType == 'exclude') {
				$query->whereDoesntHave('patientServiceRequestRelationShip', function ($q) use ($searchQuery) {
					$q->whereIn('service_id', $searchQuery['service_id']);
				});
			}
		}

		// Filters on patient_service_requests table columns (no subquery needed)
		if (!empty($searchQuery['status'])) {
			$query->whereIn('status', $searchQuery['status']);
		}
		if (!empty($searchQuery['created_by_ny_id'])) {
			$query->where('created_by', $searchQuery['created_by_ny_id']);
		}
		if (!empty($searchQuery['completed_date'])) {
			$exploderCompleteDate = explode('-', $searchQuery['completed_date']);
			if (count($exploderCompleteDate) == 2) {
				$startDate = Carbon::parse(trim($exploderCompleteDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderCompleteDate[1]))->format('Y-m-d 23:59:59');
				$query->whereBetween('completed_date', [$startDate, $endDate]);
			}
		}
		if (!empty($searchQuery['created_date'])) {
			$exploderCreateDate = explode('-', $searchQuery['created_date']);
			if (count($exploderCreateDate) == 2) {
				$startDate = Carbon::parse(trim($exploderCreateDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderCreateDate[1]))->format('Y-m-d 23:59:59');
				$query->whereBetween('created_at', [$startDate, $endDate]);
			}
		}
		if (!empty($searchQuery['last_status_update'])) {
			$explode = explode('-', $searchQuery['last_status_update']);
			$query->whereDate('last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))
				->whereDate('last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
		}
		if (!empty($searchQuery['last_status_updated_by'])) {
			$query->where('last_status_update_by', $searchQuery['last_status_updated_by']);
		}

		return $query->orderBy('created_at', 'desc')->paginate(50);
	}

	public function getServiceCountPatientId($id)
	{
		return PatientServiceRequest::where('patient_id', $id)->where('del_flag', 'N')->get();
	}

	public function getServiceCountHubPatientId($id)
	{
		return HubPatientServiceRequest::where('patient_id', $id)->where('del_flag', 'N')->get();
	}
	public function lastServiceRequestedByPatientId($pid)
	{
		return PatientServiceRequest::where('del_flag', 'N')->where('patient_id', $pid)->orderBy('id', 'desc')->first();
	}

	public function getDataExport($searchQuery)
	{
		$agencyids = Utility::getUserWiseAgency();
		$query = PatientServiceRequest::with('patient.agencyDetail', 'userDetails', 'patientServiceRequestRelationShip.requestService', 'patient.assignToUser', 'statusUserDetails:id,first_name,last_name')->where('del_flag', 'N');
		$query->whereHas('patient', function ($q) {
			$q->where('deleted_flag', 'N');
		});
		$query->whereHas('patientServiceRequestRelationShip', function ($q) {
			$q->where('service_id', '!=', '')->where('del_flag', 'N');
		});
		if (!empty($searchQuery['status'])) {
			if (!is_array($searchQuery['status'])) {
				$searchQuery['status'] = [$searchQuery['status']];
			}
			$query = $query->whereIn('status', $searchQuery['status']);
		}

		if (!empty($searchQuery['agency_fk'])) {
			if (!is_array($searchQuery['agency_fk'])) {
				$searchQuery['agency_fk'] = [$searchQuery['agency_fk']];
			}
			if(isset($searchQuery['agency_filter_type']) && trim($searchQuery['agency_filter_type']) == 'include'){
				$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
					$q->whereIn('agency_id', $searchQuery['agency_fk']);
				});
			}else if(isset($searchQuery['agency_filter_type']) && trim($searchQuery['agency_filter_type']) == 'exclude'){
				$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
					$q->whereNotIn('agency_id', $searchQuery['agency_fk']);
				});
			}
		} else {
			if (count($agencyids) > 0) {
				$query = $query->whereHas('patient', function ($q) use ($agencyids) {
					$q->whereIn('agency_id', $agencyids);
				});
			}
		}

		if (!empty($searchQuery['service_id'])) {
			if (!is_array($searchQuery['service_id'])) {
				$searchQuery['service_id'] = [$searchQuery['service_id']];
			}
			if(isset($searchQuery['service_filter_type']) && trim($searchQuery['service_filter_type']) == 'include'){
				$query = $query->whereHas('patientServiceRequestRelationShip', function ($q) use ($searchQuery) {
					$q->whereIn('service_id', $searchQuery['service_id']);
				});
			}else if(isset($searchQuery['service_filter_type']) && trim($searchQuery['service_filter_type']) == 'exclude'){
				$query = $query->whereHas('patientServiceRequestRelationShip', function ($q) use ($searchQuery) {
					$q->whereNotIn('service_id', $searchQuery['service_id']);
				});
			}
		}

		if (!empty($searchQuery['assign_user_id'])) {
			if (!is_array($searchQuery['assign_user_id'])) {
				$searchQuery['assign_user_id'] = [$searchQuery['assign_user_id']];
			}
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('assign_user_id', $searchQuery['assign_user_id']);
			});
		}

		if (!empty($searchQuery['training_status'])) {
			if (!is_array($searchQuery['training_status'])) {
				$searchQuery['training_status'] = [$searchQuery['training_status']];
			}
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('training_status', $searchQuery['training_status']);
			});
		}

		if (!empty($searchQuery['created_by_ny_id'])) {
			$query = $query->where('created_by', $searchQuery['created_by_ny_id']);
		}

		if (!empty($searchQuery['sms_status'])) {
			if (!is_array($searchQuery['sms_status'])) {
				$searchQuery['sms_status'] = [$searchQuery['sms_status']];
			}
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('patient_sms_flag', $searchQuery['sms_status']);
			});
		}

		if (!empty($searchQuery['patient_code'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('patient_code', 'like', '%' . $searchQuery['patient_code'] . '%');
			});
		}

		if (!empty($searchQuery['first_name'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereRaw('CONCAT_WS(" ", first_name, last_name) LIKE ?', ['%' . $searchQuery['first_name'] . '%']);
			});
		}

		if (!empty($searchQuery['mobile'])) {
			$query = $query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('mobile', 'like', '%' . $searchQuery['mobile'] . '%');
			});
		}

		if (!empty($searchQuery['due_date'])) {
			$exploderDueDate = explode('-', $searchQuery['due_date']);
			if (count($exploderDueDate) == 2) {
				$startDate = Carbon::parse(trim($exploderDueDate[0]))->toDateString();
				$endDate = Carbon::parse(trim($exploderDueDate[1]))->toDateString();

				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('due_date', [$startDate, $endDate]);
				});
			}
		}

		if (!empty($searchQuery['appointment_date'])) {
			$exploderAppDate = explode('-', $searchQuery['appointment_date']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->toDateString();
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->toDateString();

				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('appointment_date', [$startDate, $endDate]);
				});
			}
		}

		if (!empty($searchQuery['diciplin'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('diciplin', 'like', '%' . $searchQuery['diciplin'] . '%');
			});
		}

		if (!empty($searchQuery['type'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('type', 'like', '%' . $searchQuery['type'] . '%');
			});
		}

		if (!empty($searchQuery['inservice_date'])) {
			$exploderServiceDate = explode('-', $searchQuery['inservice_date']);
			if (count($exploderServiceDate) == 2) {
				$startDate = Carbon::parse(trim($exploderServiceDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderServiceDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('inservice_datetime', [$startDate, $endDate]);
				});
			}
		}

		if (!empty($searchQuery['completed_date'])) {
			$exploderCompleteDate = explode('-', $searchQuery['completed_date']);
			if (count($exploderCompleteDate) == 2) {
				$startDate = Carbon::parse(trim($exploderCompleteDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderCompleteDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('completed_date', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['follow_up_date'])) {
			$exploderFollowDate = explode('-', $searchQuery['follow_up_date']);
			if (count($exploderFollowDate) == 2) {
				$startDate = Carbon::parse(trim($exploderFollowDate[0]))->toDateString();
				$endDate = Carbon::parse(trim($exploderFollowDate[1]))->toDateString();

				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('follow_date', [$startDate, $endDate]);
				});
			}
		}

		if (!empty($searchQuery['traning_date'])) {
			$exploderTraningDate = explode('-', $searchQuery['traning_date']);
			if (count($exploderTraningDate) == 2) {
				$startDate = Carbon::parse(trim($exploderTraningDate[0]))->toDateString();
				$endDate = Carbon::parse(trim($exploderTraningDate[1]))->toDateString();

				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('traning_due_date', [$startDate, $endDate]);
				});
			}
		}

		if (!empty($searchQuery['traning_date'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('traning_due_date', 'like', '%' . $searchQuery['traning_date'] . '%');
			});
		}

		if (!empty($searchQuery['created_date'])) {
			$exploderCreateDate = explode('-', $searchQuery['created_date']);
			if (count($exploderCreateDate) == 2) {
				$startDate = Carbon::parse(trim($exploderCreateDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderCreateDate[1]))->format('Y-m-d 23:59:59');

				$query = $query->whereBetween('created_at', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['locationId'])) {
			$query = $query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('location_id', $searchQuery['locationId']);
			});
		}

		if (!empty($searchQuery['transition_aid'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('transition_aid', $searchQuery['transition_aid']);
			});
		}

		if (!empty($searchQuery['language_id'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('language', $searchQuery['language_id']);
			});
		}
		
		if (!empty($searchQuery['last_status_update'])) {
			$explode = explode('-', $searchQuery['last_status_update']);
			$query->whereDate('last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
		}

		if (!empty($searchQuery['last_status_updated_by'])) {

			$query->where('last_status_update_by', $searchQuery['last_status_updated_by']);
		}

		if (!empty($searchQuery['medication_list'])) {
			$medication_list = $searchQuery['medication_list'];
			$query = $query->whereHas('patient', function ($q) use ($medication_list) {
				if ($medication_list == 'Yes') {
					$q->where('medication_count','>=',1);
				} else {
					$q->where('medication_count','=',0);
				}
			});
		}

		if (!empty($searchQuery['insurance_elg'])) {
			$insurance_elg = $searchQuery['insurance_elg'];
			$query = $query->whereHas('patient', function ($q) use ($insurance_elg) {
				if ($insurance_elg == 'Yess') {
					$q->where('insurance_elg_count','>=',1);
				} else {
					$q->where('insurance_elg_count','=',0);
				}
			});
		}

		if (!empty($searchQuery['mdo_tag'])) {
			$mdo_tag = $searchQuery['mdo_tag'];
			$query = $query->whereHas('patient', function ($q) use ($mdo_tag) {
				if ($mdo_tag == 'Yes') {
					$q->where('mdo_tag_count','>=',1);
				} else {
					$q->where('mdo_tag_count','=',0);
				}
			});
		}
		
		$query = $query = $query->whereHas('patient', function ($q) use ($searchQuery) {
			$q->where('id', '!=', NULL);
			$q->where('archived_at', '=', NULL);
		});
		return $query->get();
	}

	public function getStatusWiseData($agency_id, $from_date, $to_date)
	{
		$query = PatientServiceRequest::with(['patient', 'patient.agencyDetail'])
			->select(
				DB::raw("SUM(CASE WHEN status = 'Scheduled' THEN 1 ELSE 0 END) as 'Booked'"),
				DB::raw("SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as 'Pending'"),
				DB::raw("SUM(CASE WHEN status = 'MarkAsCompleted' THEN 1 ELSE 0 END) as 'Completed'"),
				DB::raw("SUM(CASE WHEN status = 'MarkAsCancel' THEN 1 ELSE 0 END) as 'Cancelled'"),
				DB::raw("SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as 'Cancelled'"),
				DB::raw("SUM(CASE WHEN status = 'MarkAsNoShow' THEN 1 ELSE 0 END) as 'No Show'"),
				DB::raw("SUM(CASE WHEN status = 'missed' THEN 1 ELSE 0 END) as 'Missed'"),
				DB::raw("SUM(CASE WHEN status = 'MarkAsHospitalized/Rehab' THEN 1 ELSE 0 END) as 'Hospitalized / Rehab'"),
				DB::raw("SUM(CASE WHEN status = 'unableToContact' THEN 1 ELSE 0 END) as 'Unable To Contact'"),
				DB::raw("SUM(CASE WHEN status = 'MarkAsCheckIn' THEN 1 ELSE 0 END) as 'Mark As Check In'"),
				DB::raw("SUM(CASE WHEN status = 'MarkAsProcessing' THEN 1 ELSE 0 END) as 'Processing'"),
				DB::raw("SUM(CASE WHEN status = 'MarkAsRefused' THEN 1 ELSE 0 END) as 'Refused'"),
				DB::raw("SUM(CASE WHEN status = 'PendingTermination' THEN 1 ELSE 0 END) as 'Pending Termination'"),
				DB::raw("SUM(CASE WHEN status = 'OnHold' THEN 1 ELSE 0 END) as 'On Hold'"),
				DB::raw("SUM(CASE WHEN status = 'OnLeave' THEN 1 ELSE 0 END) as 'On Leave'"),
				DB::raw("SUM(CASE WHEN status = 'Terminated' THEN 1 ELSE 0 END) as 'Terminated'"),
				DB::raw("SUM(CASE WHEN status = 'InService' THEN 1 ELSE 0 END) as 'In Service'"),
				DB::raw("SUM(CASE WHEN status = '1st Attempt - Unable to Contact' THEN 1 ELSE 0 END) as '1st Attempt - Unable to Contact'"),
				DB::raw("SUM(CASE WHEN status = '2nd Attempt - Unable to Contact' THEN 1 ELSE 0 END) as '2nd Attempt - Unable to Contact'"),
				DB::raw("SUM(CASE WHEN status = '3rd Attempt - Unable to Contact' THEN 1 ELSE 0 END) as '3rd Attempt - Unable to Contact'"),
				DB::raw("SUM(CASE WHEN status = 'Telehealth Completed' THEN 1 ELSE 0 END) as 'Telehealth Completed'"),
				DB::raw("SUM(CASE WHEN status = 'Patient Deceased' THEN 1 ELSE 0 END) as 'Patient Deceased'"),
				DB::raw("SUM(CASE WHEN status = 'Signed' THEN 1 ELSE 0 END) as 'Signed'"),
				DB::raw("SUM(CASE WHEN status = 'Signed & Sent Back to the Agency' THEN 1 ELSE 0 END) as 'Signed & Sent Back to the Agency'"),
				DB::raw("SUM(CASE WHEN status = 'Telehealth Completed , Pending Forms' THEN 1 ELSE 0 END) as 'Telehealth Completed , Pending Forms'"),
				DB::raw("SUM(CASE WHEN status = 'Appointment was missed' THEN 1 ELSE 0 END) as 'Appointment was missed'"),
				DB::raw("SUM(CASE WHEN status = 'Patient Asked to Reschedule' THEN 1 ELSE 0 END) as 'Patient Asked to Reschedule'"),
				DB::raw("SUM(CASE WHEN status = 'New Order Received' THEN 1 ELSE 0 END) as 'New Order Received'"),
				DB::raw("SUM(CASE WHEN status = 'New Form Requested' THEN 1 ELSE 0 END) as 'New Form Requested'"),
				DB::raw("SUM(CASE WHEN status = 'Appointment Missed' THEN 1 ELSE 0 END) as 'Appointment Missed'"),
				DB::raw("SUM(CASE WHEN status = 'Appointment Missed' THEN 1 ELSE 0 END) as 'Appointment Missed'"),
				DB::raw("SUM(CASE WHEN status = 'Form Completed' THEN 1 ELSE 0 END) as 'Form Completed'"),
				DB::raw("SUM(CASE WHEN status = 'Service Provided' THEN 1 ELSE 0 END) as 'Service Provided'"),
				DB::raw("SUM(CASE WHEN status = 'Closed Temporarily' THEN 1 ELSE 0 END) as 'Closed Temporarily'")
			);

		// Check if agency_id is provided and filter based on it
		if (!empty($agency_id)) {
			$explode = explode(',', $agency_id);
			$query->whereHas('patient', function ($q) use ($explode) {
				$q->whereIn('agency_id', $explode);
			});
		}
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		$query->whereHas('patient', function ($q) {
			$q->whereNull('archived_at');
			$q->whereNotNull('id');
		});
		return $query->first()->toArray();
	}

	public function totalPatientCount($type, $from_date, $to_date)
	{
		$query = PatientServiceRequest::with(['patient'])->where('del_flag', 'N');
		$query->whereHas('patient', function ($q) use ($type, $from_date, $to_date) {
			$q->where('type', $type);
			$q->whereNull('archived_at');
			$q->where('id', '!=', NULL);
			if (!empty($from_date) && !empty($to_date)) {
				$q->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			}
		});
		return $query->count();
	}

	public function totalCountPatientStatusServiceWise($status, $from_date, $to_date)
	{
		$query = PatientServiceRequest::with(['patient']);
		$query->whereHas('patient', function ($q) {
			$q->whereNull('archived_at');
			$q->where('id', '!=', NULL);
		});
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		if (!empty($status && $status != 'all')) {
			$query->where('status', $status);
		}
		return $query->count();
	}


	public function getCaregiverCount($agencyIds, $type, $locationIds, $from_date, $to_date)
	{
		$query = PatientServiceRequest::with('patient:id,type,location_id')->where('del_flag', 'N');
		$query->whereHas('patient', function ($q) use ($type, $from_date, $to_date, $locationIds, $agencyIds) {
			if ($type != '') {
				$q->where('type', $type);
			}
			$q->whereNull('archived_at');
			$q->whereNotNull('id');
			if (!empty($from_date) && !empty($to_date)) {
				$q->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			}
			if ($locationIds != "") {
				$q->whereIn('location_id', $locationIds);
			}
			if ($agencyIds != "") {
				$q->whereIn('agency_id', $agencyIds);
			}
		});
		return $query = $query->get();
	}

	public function getAllServiceListAssigned($pid)
	{

		return PatientServiceRequest::with(['userDetailsWithTrashed:id,first_name,last_name', 'patientServiceRequestRelationShip.services:id,name', 'completedUserDetails:id,first_name,last_name', 'patient:id,agency_id', 'patient.agencyDetail:id,agency_name', 'statusUserDetails:id,first_name,last_name'])->whereIn('patient_id', $pid)->whereHas('patientServiceRequestRelationShip', function ($q) {
			$q->where('service_id', '!=', '')->where('del_flag', 'N');
		})->where('del_flag', 'N')->orderBy('created_at', 'desc')->paginate(50);
	}

	public function getTotalAppointmentCountForService($agency_id, $location_id, $type, $from_date, $to_date)
	{

		$query = PatientServiceRequest::with('patient', 'patientServiceRequestRelationShip.requestService');
		if (!empty($agency_id)) {
			$query = $query->whereHas('patient', function ($q) use ($agency_id) {
				$explode = explode(',', $agency_id);
				$q->whereIn('agency_id', $explode);
			});
		}
		if (!empty($type)) {
			$query = $query->whereHas('patient', function ($q) use ($type) {
				$q->where('type', 'like', '%' . $type . '%');
			});
		}
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
		}
		if (!empty($location_id)) {
			$query = $query = $query->whereHas('patient', function ($q) use ($location_id) {
				$q->where('location_id', $location_id);
			});
		}
		if (!empty($service_id)) {
			$query = $query->whereHas('patientServiceRequestRelationShip', function ($q) use ($service_id) {
				$q->whereIn('service_id', $service_id);
			});
		}
		$query->whereHas('patient', function ($q) {
			$q->whereNull('archived_at');
			$q->whereNotNull('id');
		});
		return $query = $query->get()->map(function ($request) {
			return $request->patientServiceRequestRelationShip->pluck('service_id');
		})->flatten();
	}

	public function getPatientServiceCount($location_id, $agency_id, $type, $from_date, $to_date)
	{
		$query = PatientServiceRequest::with('patient.agencyDetail')
			->whereHas('patient', function ($q) use ($agency_id, $type, $location_id) {
				if (!empty($agency_id)) {
					$explode = explode(',', $agency_id);
					$q->whereIn('agency_id', $explode);
				}
				if (!empty($type)) {
					$q->where('type', 'like', '%' . $type . '%');
				}
				if (!empty($location_id)) {
					$q->where('location_id', $location_id);
				}
				$q->whereNull('archived_at');
				$q->whereNotNull('id');
			})
			->when(!empty($from_date) && !empty($to_date), function ($q) use ($from_date, $to_date) {
				$q->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
			})
			->get();

		$results = $query->groupBy('patient.agencyDetail.id')->map(function ($group) {
			return [
				'id' => $group->first()->patient->agencyDetail->id,
				'agency_name' => $group->first()->patient->agencyDetail->agency_name,
				'patient_caregiver_count' => $group->filter(fn($item) => $item->patient->type === 'Caregiver')->count(),
				'patient_total_patient_count' => $group->filter(fn($item) => $item->patient->type === 'Patient')->count(),
			];
		});
		// Convert to collection or array as needed
		return $results->values()->toArray();
	}

	public function getExistingPatientIds($pId)
	{
		return PatientServiceRequest::select('id', 'patient_id')->where('del_flag', 'N')->where('patient_id', $pId)->get();
	}

	public function getByPatientDetailsWithSHA1($patient_id)
	{
		return PatientServiceRequest::whereRaw('sha1(id)="' . $patient_id . '"')->first();
	}

	public function getServiceWiseList($service_id)
	{
		$getServiceIdByPatient = PatientWiseServiceRequest::with('services:id,name')->where('patient_service_request_id', $service_id)->get()->toArray();
		return $getServiceIdByPatient;
	}

	public function hubSave($data)
	{
		$auth = auth()->user();
		if (isset($data['updated_flag']) && $data['updated_flag'] == 1) {
			$data['created_at'] = $data['created_at'];
			$data['created_by'] = $auth['id'];
		} else {
			if (isset($data['flag']) && $data['flag'] == 1) {
				$data['created_at'] = $data['created_at'];
				$data['created_by'] = $data['created_by'];
			} else {
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['created_by'] = $auth['id'];
			}
		}

		$insert = new HubPatientServiceRequest($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}

	public function hubPatientRequestedServiceList($searchQuery)
	{
		$agencyids = Utility::getUserWiseAgency();
		$query = HubPatientServiceRequest::with('patient.agencyDetail', 'patient.hubCompanyDetail', 'userDetails', 'patientServiceRequestRelationShip.requestService', 'patient.assignToUser', 'statusUserDetails:id,first_name,last_name')->where('del_flag', 'N');
		$query->whereHas('patient', function ($q) {
			$q->where('deleted_flag', 'N');
		});
		$query->whereHas('patientServiceRequestRelationShip', function ($q) {
			$q->where('service_id', '!=', '')->where('del_flag', 'N');
		});
		if (!empty($searchQuery['status'])) {
			$query = $query->whereIn('status', $searchQuery['status']);
		}

		if (!empty($searchQuery['agency_fk'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('agency_id', $searchQuery['agency_fk']);
			});
		} else {
			if (count($agencyids) > 0) {
				$query = $query->whereHas('patient', function ($q) use ($agencyids) {
					$q->whereIn('agency_id', $agencyids);
				});
			}
		}
		if (!empty($searchQuery['service_id'])) {
			$query = $query->whereHas('patientServiceRequestRelationShip', function ($q) use ($searchQuery) {
				$q->whereIn('service_id', $searchQuery['service_id']);
			});
		}
		if (!empty($searchQuery['assign_user_id'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('assign_user_id', $searchQuery['assign_user_id']);
			});
		}
		if (!empty($searchQuery['training_status'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('training_status', $searchQuery['training_status']);
			});
		}
		if (!empty($searchQuery['created_by_ny_id'])) {
			$query = $query->where('created_by', $searchQuery['created_by_ny_id']);
		}
		if (!empty($searchQuery['sms_status'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('patient_sms_flag', $searchQuery['sms_status']);
			});
		}
		if (!empty($searchQuery['patient_code'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('patient_code', 'like', '%' . $searchQuery['patient_code'] . '%');
			});
		}
		if (!empty($searchQuery['first_name'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereRaw('CONCAT_WS(" ", first_name, last_name) LIKE ?', ['%' . $searchQuery['first_name'] . '%']);
			});
		}
		if (!empty($searchQuery['mobile'])) {
			$query = $query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('mobile', 'like', '%' . $searchQuery['mobile'] . '%');
			});
		}

		if (!empty($searchQuery['appointment_date'])) {
			$exploderAppDate = explode('-', $searchQuery['appointment_date']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->toDateString();
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->toDateString();

				$query = $query->whereBetween('booking_date', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['type'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('type', 'like', '%' . $searchQuery['type'] . '%');
			});
		}
		if (!empty($searchQuery['inservice_date'])) {
			$exploderServiceDate = explode('-', $searchQuery['inservice_date']);
			if (count($exploderServiceDate) == 2) {
				$startDate = Carbon::parse(trim($exploderServiceDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderServiceDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('inservice_datetime', [$startDate, $endDate]);
				});
			}
		}
		if (!empty($searchQuery['inservice_date'])) {
			$exploderServiceDate = explode('-', $searchQuery['inservice_date']);
			if (count($exploderServiceDate) == 2) {
				$startDate = Carbon::parse(trim($exploderServiceDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderServiceDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('inservice_datetime', [$startDate, $endDate]);
				});
			}
		}

		if (!empty($searchQuery['type'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('type', $searchQuery['type']);
			});
		}


		if (!empty($searchQuery['created_date'])) {
			$exploderCreateDate = explode('-', $searchQuery['created_date']);
			// echo '<pre>'; print_r(count($exploderCreateDate)); exit;
			if (count($exploderCreateDate) == 2) {
				$startDate = Carbon::parse(trim($exploderCreateDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderCreateDate[1]))->format('Y-m-d 23:59:59');

				$query = $query->whereBetween('created_at', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['locationId'])) {
			$query = $query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('location_id', $searchQuery['locationId']);
			});
		}

		if (!empty($searchQuery['transition_aid'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('transition_aid', $searchQuery['transition_aid']);
			});
		}

		if (!empty($searchQuery['language_id'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('language', $searchQuery['language_id']);
			});
		}

		$query = $query = $query->whereHas('patient', function ($q) use ($searchQuery) {
			$q->where('id', '!=', NULL);
			$q->where('archived_at', '=', NULL);
		});

		if (!empty($searchQuery['last_status_update'])) {
			$explode = explode('-', $searchQuery['last_status_update']);
			$query->whereDate('last_status_update', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('last_status_update', '<=', date('Y-m-d', strtotime($explode[1])));
		}

		if (!empty($searchQuery['last_status_updated_by'])) {

			$query->where('last_status_update_by', $searchQuery['last_status_updated_by']);
		}

		return $query->orderBy('created_at', 'desc')->paginate(50);
	}
	public function getHubDataExport($searchQuery)
	{
		$agencyids = Utility::getUserWiseAgency();
		$query = HubPatientServiceRequest::with('patient.agencyDetail', 'patient.hubCompanyDetail', 'userDetails', 'patientServiceRequestRelationShip.requestService', 'patient.assignToUser', 'statusUserDetails');

		if (!empty($searchQuery['status'])) {
			if (!is_array($searchQuery['status'])) {
				$searchQuery['status'] = [$searchQuery['status']];
			}
			$query = $query->whereIn('status', $searchQuery['status']);
		}

		if (!empty($searchQuery['agency_fk'])) {
			if (!is_array($searchQuery['agency_fk'])) {
				$searchQuery['agency_fk'] = [$searchQuery['agency_fk']];
			}
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereIn('agency_id', $searchQuery['agency_fk']);
			});
		} else {
			if (count($agencyids) > 0) {
				$query = $query->whereHas('patient', function ($q) use ($agencyids) {
					$q->whereIn('agency_id', $agencyids);
				});
			}
		}

		if (!empty($searchQuery['service_id'])) {
			if (!is_array($searchQuery['service_id'])) {
				$searchQuery['service_id'] = [$searchQuery['service_id']];
			}
			$query = $query->whereHas('patientServiceRequestRelationShip', function ($q) use ($searchQuery) {
				$q->whereIn('service_id', $searchQuery['service_id']);
			});
		}


		if (!empty($searchQuery['created_by_ny_id'])) {
			$query = $query->where('created_by', $searchQuery['created_by_ny_id']);
		}


		if (!empty($searchQuery['first_name'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->whereRaw('CONCAT_WS(" ", first_name, last_name) LIKE ?', ['%' . $searchQuery['first_name'] . '%']);
			});
		}

		if (!empty($searchQuery['mobile'])) {
			$query = $query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('mobile', 'like', '%' . $searchQuery['mobile'] . '%');
			});
		}

		if (!empty($searchQuery['due_date'])) {
			$exploderDueDate = explode('-', $searchQuery['due_date']);
			if (count($exploderDueDate) == 2) {
				$startDate = Carbon::parse(trim($exploderDueDate[0]))->toDateString();
				$endDate = Carbon::parse(trim($exploderDueDate[1]))->toDateString();

				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('due_date', [$startDate, $endDate]);
				});
			}
		}

		if (!empty($searchQuery['appointment_date'])) {
			$exploderAppDate = explode('-', $searchQuery['appointment_date']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->toDateString();
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->toDateString();

				$query = $query->whereBetween('booking_date', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['type'])) {
			$query = $query->whereHas('patient', function ($q) use ($searchQuery) {
				$q->where('type', 'like', '%' . $searchQuery['type'] . '%');
			});
		}

		if (!empty($searchQuery['inservice_date'])) {
			$exploderServiceDate = explode('-', $searchQuery['inservice_date']);
			if (count($exploderServiceDate) == 2) {
				$startDate = Carbon::parse(trim($exploderServiceDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderServiceDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereHas('patient', function ($q) use ($startDate, $endDate) {
					$q->whereBetween('inservice_datetime', [$startDate, $endDate]);
				});
			}
		}

		if (!empty($searchQuery['created_date'])) {
			$exploderCreateDate = explode('-', $searchQuery['created_date']);
			if (count($exploderCreateDate) == 2) {
				$startDate = Carbon::parse(trim($exploderCreateDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderCreateDate[1]))->format('Y-m-d 23:59:59');

				$query = $query->whereBetween('created_at', [$startDate, $endDate]);
			}
		}

		$query = $query = $query->whereHas('patient', function ($q) use ($searchQuery) {
			$q->where('id', '!=', NULL);
			$q->where('archived_at', '=', NULL);
		});
		return $query->get();
	}
}
