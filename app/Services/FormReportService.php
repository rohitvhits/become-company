<?php

namespace App\Services;

use App\Agency;
use App\Model\AgencyForm;
use App\Model\FormSetup;
use App\Model\Patient;
use App\User;
use Carbon\Carbon;

class FormReportService
{

	public function dataList($searchQuery)
	{
		$query = AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster.fields', 'users:id,first_name,last_name,delete_flag', 'userMarkAsComplatedDetails:id,first_name,last_name,delete_flag', 'agencyMaster:id,agency_id,field_id,form_id,sort_id', 'patient:id,first_name,last_name'])->has('forms');

		if (!empty($searchQuery['status'])) {
			if ($searchQuery['status'] == 'completed') {
				$query->where('mark_as_completed', "1");
			} elseif ($searchQuery['status'] == 'pending') {
				$query->where('mark_as_completed', "0");
			}
		}

		if (!empty($searchQuery['agency_fk'])) {
			$query->whereIn('agency_id', $searchQuery['agency_fk']);
		}
		if (!empty($searchQuery['form_name'])) {
			$query->whereIn('form_id', $searchQuery['form_name']);
		}
		if (!empty($searchQuery['patient_name'])) {
			$query = $query->where('patient_id', $searchQuery['patient_name']);
		}
		if (!empty($searchQuery['created_at'])) {
			$exploderDueDate = explode('-', $searchQuery['created_at']);
			if (count($exploderDueDate) == 2) {
				$startDate = Carbon::parse(trim($exploderDueDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderDueDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('created_at', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['created_by'])) {
			$query = $query->where('created_by', $searchQuery['created_by']);
		}

		if (!empty($searchQuery['mark_as_completed_date'])) {
			$exploderAppDate = explode('-', $searchQuery['mark_as_completed_date']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('mark_as_completed_date', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['mark_as_completed_by'])) {
			$query = $query->where('mark_as_completed_by', $searchQuery['mark_as_completed_by']);
		}

		return $query->orderBy('id', 'desc')->paginate(50);
	}

	public function getMarkAsCompleted($searchQuery)
	{
		$query = AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster.fields', 'users:id,first_name,last_name,delete_flag', 'userMarkAsComplatedDetails:id,first_name,last_name,delete_flag', 'agencyMaster:id,agency_id,field_id,form_id,sort_id', 'patient:id,first_name,last_name'])->has('forms');

		if (!empty($searchQuery['status'])) {
			if ($searchQuery['status'] == 'completed') {
				$query->where('mark_as_completed', "1");
			} elseif ($searchQuery['status'] == 'pending') {
				$query->where('mark_as_completed', "0");
			}
		}

		if (!empty($searchQuery['agency_fk'])) {
			$query->whereIn('agency_id', $searchQuery['agency_fk']);
		}
		if (!empty($searchQuery['form_name'])) {
			$query->whereIn('form_id', $searchQuery['form_name']);
		}
		if (!empty($searchQuery['patient_name'])) {
			$query = $query->where('patient_id', $searchQuery['patient_name']);
		}
		if (!empty($searchQuery['created_at'])) {
			$exploderDueDate = explode('-', $searchQuery['created_at']);
			if (count($exploderDueDate) == 2) {
				$startDate = Carbon::parse(trim($exploderDueDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderDueDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('created_at', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['created_by'])) {
			$query = $query->where('created_by', $searchQuery['created_by']);
		}

		if (!empty($searchQuery['mark_as_completed_date'])) {
			$exploderAppDate = explode('-', $searchQuery['mark_as_completed_date']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('mark_as_completed_date', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['mark_as_completed_by'])) {
			$query = $query->where('mark_as_completed_by', $searchQuery['mark_as_completed_by']);
		}

		return $query->where('mark_as_completed', "1")->count();
	}

	public function getMarkAsPending($searchQuery)
	{
		$query = AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster.fields', 'users:id,first_name,last_name,delete_flag', 'userMarkAsComplatedDetails:id,first_name,last_name,delete_flag', 'agencyMaster:id,agency_id,field_id,form_id,sort_id', 'patient:id,first_name,last_name'])->has('forms');

		if (!empty($searchQuery['status'])) {
			if ($searchQuery['status'] == 'completed') {
				$query->where('mark_as_completed', "1");
			} elseif ($searchQuery['status'] == 'pending') {
				$query->where('mark_as_completed', "0");
			}
		}

		if (!empty($searchQuery['agency_fk'])) {
			$query->whereIn('agency_id', $searchQuery['agency_fk']);
		}
		if (!empty($searchQuery['form_name'])) {
			$query->whereIn('form_id', $searchQuery['form_name']);
		}
		if (!empty($searchQuery['patient_name'])) {
			$query = $query->where('patient_id', $searchQuery['patient_name']);
		}
		if (!empty($searchQuery['created_at'])) {
			$exploderDueDate = explode('-', $searchQuery['created_at']);
			if (count($exploderDueDate) == 2) {
				$startDate = Carbon::parse(trim($exploderDueDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderDueDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('created_at', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['created_by'])) {
			$query = $query->where('created_by', $searchQuery['created_by']);
		}

		if (!empty($searchQuery['mark_as_completed_date'])) {
			$exploderAppDate = explode('-', $searchQuery['mark_as_completed_date']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('mark_as_completed_date', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['mark_as_completed_by'])) {
			$query = $query->where('mark_as_completed_by', $searchQuery['mark_as_completed_by']);
		}

		return $query->where('mark_as_completed', "0")->count();
	}

	public function getAllAgencyList()
	{
		return Agency::select('id', 'agency_name')->orderBy('agency_name', 'asc')->get();
	}

	public function getAllFormList()
	{
		return FormSetup::select('id', 'title')->get();
	}

	public function getDataExport($searchQuery)
	{
		$query = AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster.fields', 'users:id,first_name,last_name,delete_flag', 'userMarkAsComplatedDetails:id,first_name,last_name,delete_flag', 'agencyMaster:id,agency_id,field_id,form_id,sort_id', 'patient:id,first_name,last_name']);

		if (!empty($searchQuery['status'])) {
			if ($searchQuery['status'] == 'completed') {
				$query->where('mark_as_completed', "1");
			} elseif ($searchQuery['status'] == 'pending') {
				$query->where('mark_as_completed', "0");
			}
		}

		if (!empty($searchQuery['agency_fk'])) {
			$query->whereIn('agency_id', $searchQuery['agency_fk']);
		}
		if (!empty($searchQuery['form_name'])) {
			$query->whereIn('form_id', $searchQuery['form_name']);
		}
		if (!empty($searchQuery['patient_name'])) {
			$query = $query->where('patient_id', $searchQuery['patient_name']);
		}

		if (!empty($searchQuery['created_at'])) {
			$exploderDueDate = explode('-', $searchQuery['created_at']);
			if (count($exploderDueDate) == 2) {
				$startDate = Carbon::parse(trim($exploderDueDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderDueDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('created_at', [$startDate, $endDate]);
			}
		}
		if (!empty($searchQuery['created_by'])) {
			$query = $query->where('created_by', $searchQuery['created_by']);
		}
		if (!empty($searchQuery['mark_as_completed_date'])) {
			$exploderAppDate = explode('-', $searchQuery['mark_as_completed_date']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('mark_as_completed_date', [$startDate, $endDate]);
			}
		}
		if (!empty($searchQuery['mark_as_completed_by'])) {
			$query = $query->where('mark_as_completed_by', $searchQuery['mark_as_completed_by']);
		}
		return $query->get();
	}

	public function searchNybestPatient($search)
	{
		return Patient::selectRaw('id,first_name,last_name')->where('deleted_flag','N')->whereRaw('CONCAT(first_name," ",last_name) LIKE "%'.$search.'%"')->get();
	}

	public function searchNybestAllUser($search)
	{
		return User::selectRaw('id,first_name,last_name')->where('delete_flag','N')->whereRaw('CONCAT(first_name," ",last_name) LIKE "%'.$search.'%"')->get();
	}
}

