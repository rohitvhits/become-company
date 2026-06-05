<?php

namespace App\Services;

use App\Helpers\Utility;
use App\Model\Language;
use App\Model\HubRecord;

class HubRecordService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		if (isset($auth['id'])) {
			$data['created_by'] = $auth['id'];
		}
		$data['deleted_flag'] = "N";

		$insert = new HubRecord($data);
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


		$update = HubRecord::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = HubRecord::where($where)->update($data);
		return $update;
	}

	public function checkFirstNameLastNameDob($firstName, $lastName, $dateOfBirth)
	{
		return HubRecord::where('first_name', $firstName)->where('last_name', $lastName)->where('dob', $dateOfBirth)->first();
	}

	public function getHubData($search_data = array(), $export = "")
	{

		$where = 'hub_record.deleted_flag ="N" ';
		$query = HubRecord::with(['users:id,first_name,last_name', 'languages:id,name', 'locations:id,address1', 'usersUpdate:id,first_name,last_name'])->selectRaw("hub_record.*, 
                 CASE 
                    WHEN COUNT(DISTINCT hub_company.id) > 1 THEN 'Multiple' 
                    ELSE MAX(hub_company.agency_name) 
                 END as agency_name,hub_record_agency.hire_date,hub_record_agency.last_worked_date,hub_record_agency.employee_code,hub_record_agency.work_contact,hub_record_agency.work_email,hub_dependents.id as dependent_id")
			->leftjoin('hub_record_agency', function ($join) {
				$join->on('hub_record.id', '=', 'hub_record_agency.hub_record_id');
				$join->where('hub_record_agency.del_flag', 'N');
			});
		if (isset($search_data['parent_id']) && $search_data['parent_id'] == 'dependent') {
			$query->join('hub_dependents', function ($join) {
				$join->on('hub_record.id', '=', 'hub_dependents.dependent_id');
				$join->where('hub_dependents.del_flag', 'N');
			});
		} else {
			$query->leftjoin('hub_dependents', function ($join) {
				$join->on('hub_record.id', '=', 'hub_dependents.dependent_id');
				$join->where('hub_dependents.del_flag', 'N');
			});
			if (isset($search_data['parent_id']) && $search_data['parent_id'] == 'parent') {
				$query->whereNull('hub_dependents.id');
			}
			if (isset($search_data['is_dependent']) && $search_data['is_dependent'] == 'N') {
				// $query->whereNotNull('hub_dependents.id');
			}
		}

		$query->leftjoin('hub_company', function ($join) {
			$join->on('hub_company.id', '=', 'hub_record_agency.agency_id');
			$join->where('hub_company.delete_flag', 'N');
		});

		if (isset($search_data['agency_fk'][0]) && !empty($search_data['agency_fk'][0])) {
			$query->whereIn('hub_record_agency.agency_id', $search_data['agency_fk']);
		}

		if (isset($search_data['full_name']) && !empty($search_data['full_name'])) {
			$query->where('full_name', 'like', '%' . $search_data['full_name'] . '%');
		}

		if (isset($search_data['first_name']) && !empty($search_data['first_name'])) {
			$query->where('first_name', 'like', '%' . $search_data['first_name'] . '%');
		}

		if (isset($search_data['last_name']) && !empty($search_data['last_name'])) {
			$query->where('last_name', 'like', '%' . $search_data['last_name'] . '%');
		}

		if (isset($search_data['mobile']) && !empty($search_data['mobile'])) {
			$query->where(function ($q) use ($search_data) {
				$q->where('mobile', 'like', '%' . $search_data['mobile'] . '%')
					->orWhere('hub_record.phone', 'like', '%' . $search_data['mobile'] . '%');
			});
		}

		if (isset($search_data['email']) && !empty($search_data['email'])) {
			$query->where('hub_record.email', 'like', '%' . $search_data['email'] . '%');
		}
		if (isset($search_data['locationId']) && !empty($search_data['locationId'])) {
			$query->whereIn('location_id', $search_data['locationId']);
		}
		if (isset($search_data['language']) && !empty($search_data['language'])) {
			$query->where('language', $search_data['language']);
		}
		if (isset($search_data['id']) && !empty($search_data['id'])) {

			$query->where('hub_record.id', 'like', '%' . $search_data['id'] . '%');
		}
		if (!empty($search_data['created_date'])) {
			$exploder = explode('-', $search_data['created_date']);
			$startDate = date('Y-m-d', strtotime($exploder[0]));
			$endDate = date('Y-m-d', strtotime($exploder[1]));
			$query->whereDate('hub_record.created_date', '>=', $startDate)
				->whereDate('hub_record.created_date', '<=', $endDate);
		}
		if (isset($search_data['created_by']) && $search_data['created_by'] != '' && $search_data['created_by'] != 'undefined') {
			$query->where('hub_record.created_by', $search_data['created_by']);
		}
		if (isset($search_data['dob']) && $search_data['dob'] != '' && $search_data['dob'] != 'undefined') {
			$query->where('dob', date('Y-m-d', strtotime($search_data['dob'])));
		}
		if (isset($search_data['status']) && $search_data['status'] != '') {
			$query->where('hub_record_agency.status', $search_data['status']);
		}
		if (isset($search_data['ssn']) && $search_data['ssn'] != '') {
			$query->where('ssn', str_replace('-', '', $search_data['ssn']));
		}
		if (isset($search_data['employee_code']) && $search_data['employee_code'] != '') {
			$query->where('hub_record_agency.employee_code', 'like', '%' . $search_data['employee_code'] . '%');
		}
		if (isset($search_data['member_id']) && $search_data['member_id'] != '') {
			$query->where('hub_record_agency.member_id', 'like', '%' . $search_data['member_id'] . '%');
		}

		$query->whereRaw($where)
			->groupBy('hub_record.id');
		if (isset($export) && !empty($export)) {
			$query = $query->orderBy('hub_record.id', 'desc')->get();
		} else {
			$query = $query->orderBy('hub_record.id', 'desc')->paginate(20);
		}
		return $query;
	}

	public function getDetailById($id)
	{

		$query = HubRecord::where('deleted_flag', 'N')->where('id', $id);
		$query = $query->first();
		return $query;
	}

	public function getImportDuplicate($record)
	{
		$query = HubRecord::where('deleted_flag', 'N')->where('first_name', $record['first_name'])->where('last_name', $record['last_name'])->where('dob', $record['dob'])->where('gender', $record['gender'])->where('mobile', $record['mobile']);
		$query = $query->first();
		return $query;
	}

	public function getAllRecord($agency_id)
	{
		$query = HubRecord::where('deleted_flag', 'N')->where('status', '!=', 'deactivated')->where('agency_id', $agency_id);
		$query = $query->get();
		return $query;
	}

	public function getAllHubReportData($search_data = array(), $export = "")
	{
		// $auth = auth()->user();
		// if (in_array($auth['user_type_fk'], array(184))) {
		// 	$where = 'hub_record.deleted_flag ="N"';
		// 	$agencyids = Utility::getUserWiseAgency();
		// 	if(!empty($agencyids)){
		// 		$implodeIds = implode('","', $agencyids);
		// 		$where .= ' and hub_record.agency_id IN("' . $implodeIds . '")';
		// 	}
		// } else {
		// }
		$where = 'hub_record.deleted_flag ="N" ';
		$query = HubRecord::with(['users:id,first_name,last_name'])->select('hub_record.*', 'hub_company.agency_name')->leftjoin('hub_company', function ($join) {
			$join->on('hub_company.id', '=', 'hub_record.agency_id');
			$join->where('hub_company.delete_flag', 'N');
		});

		if (isset($search_data['agency_fk'][0]) && !empty($search_data['agency_fk'][0])) {
			$query->whereIn('agency_id', $search_data['agency_fk']);
		}

		if (isset($search_data['first_name']) && !empty($search_data['first_name'])) {
			$query->where('first_name', $search_data['first_name']);
		}

		if (isset($search_data['last_name']) && !empty($search_data['last_name'])) {
			$query->where('last_name', $search_data['last_name']);
		}
		if (isset($search_data['mobile']) && !empty($search_data['mobile'])) {
			$query->where('mobile', $search_data['mobile']);
		}
		if (isset($search_data['locationId']) && !empty($search_data['locationId'])) {
			$query->whereIn('location_id', $search_data['locationId']);
		}
		if (isset($search_data['language']) && !empty($search_data['language'])) {
			$query->where('language', $search_data['language']);
		}
		if (!empty($search_data['created_date'])) {
			$exploder = explode('-', $search_data['created_date']);
			$startDate = date('Y-m-d', strtotime($exploder[0]));
			$endDate = date('Y-m-d', strtotime($exploder[1]));
			$query->whereDate('hub_record.created_date', '>=', $startDate)
				->whereDate('hub_record.created_date', '<=', $endDate);
		}
		if (isset($search_data['created_by']) && $search_data['created_by'] != '' && $search_data['created_by'] != 'undefined') {
			$query->where('hub_record.created_by', $search_data['created_by']);
		}
		if (isset($search_data['dob']) && $search_data['dob'] != '' && $search_data['dob'] != 'undefined') {
			$query->where('dob', date('Y-m-d', strtotime($search_data['dob'])));
		}
		if (isset($search_data['status']) && $search_data['status'] != '') {
			$query->where('status', $search_data['status']);
		}
		$query->whereRaw($where);
		if (isset($export) && !empty($export)) {
			$query = $query->orderBy('hub_record.id', 'desc')->get();
		} else {
			$query = $query->orderBy('hub_record.id', 'desc')->simplePaginate(20);
		}
		return $query;
	}

	public function getHubRecordAPIList($agency_id, $first_name, $last_name, $mobile, $status, $dob, $offset = "")
	{
		$where = 'hub_record.deleted_flag ="N" ';
		$query = HubRecord::with(['users:id,first_name,last_name'])->select('hub_record.*', 'hub_company.agency_name')->leftjoin('hub_company', function ($join) {
			$join->on('hub_company.id', '=', 'hub_record.agency_id');
			$join->where('hub_company.delete_flag', 'N');
		});
		if (isset($agency_id) && !empty($agency_id)) {
			$query->where('agency_id', $agency_id);
		}
		if (isset($first_name) && !empty($first_name)) {
			$query->where('first_name', 'LIKE', '%' . $first_name . '%');
		}
		if (isset($last_name) && !empty($last_name)) {
			$query->where('last_name', 'LIKE', '%' . $last_name . '%');
		}
		if (isset($mobile) && !empty($mobile)) {
			$query->where('mobile', $mobile);
		}
		if (isset($dob) && $dob != '' && $dob != 'undefined') {
			$query->where('dob', date('Y-m-d', strtotime($dob)));
		}
		if (isset($status) && $status != '') {
			$query->where('status', $status);
		}
		$query->whereRaw($where);
		$query = $query->orderBy('id', 'desc')->offset($offset)->limit(50)->get();
		return $query;
	}

	public function getBasicDetailsAPI($id, $agency_id)
	{
		$query = HubRecord::where('deleted_flag', 'N')->where('id', $id)->where('agency_id', $agency_id);
		$query = $query->first();
		return $query;
	}

	public function getDetailsByIdWhitoutAgency($id)
	{
		return HubRecord::where('id', $id)->first();
	}

	public function getDuplicateSearch($data)
	{

		return HubRecord::select('hub_record.id', 'ssn')
			->where('deleted_flag', 'N')
			->whereNotNull('first_name')
			->where('first_name', 'LIKE', '%' . $data['first_name'] . '%')
			->where('last_name', 'LIKE', '%' . $data['last_name'] . '%')
			->where('mobile', $data['mobile'])
			->when(isset($data['dob']) && !empty($data['dob']), function ($query) use ($data) {
				$query->where('dob', $data['dob']);
			})
			->when(isset($data['gender']) && !empty($data['gender']), function ($query) use ($data) {
				$query->where('gender', $data['gender']);
			})
			->first();
	}

	public function getAgencyList($data)
	{
		return HubRecord::select('agency_id', 'id')
			->where('deleted_flag', 'N')
			->whereNotNull('first_name')
			->where('first_name', 'LIKE', '%' . $data['first_name'] . '%')
			->where('last_name', 'LIKE', '%' . $data['last_name'] . '%')
			->where('dob', $data['dob'])
			->where('gender', $data['gender'])
			// ->where('ssn', $data['ssn'])
			->with(['agencyDetail:id,agency_name'])
			->orderBy('agency_id', 'asc')
			->get();
	}

	public function getDependentData($hub_id)
	{
		$query = HubRecord::where('deleted_flag', 'N')
			->where('parent_id', $hub_id);
		$query = $query->orderBy('hub_record.id', 'desc')->paginate(20);
		return $query;
	}

	public function getImportDuplicateSSN($record)
	{
		$query = HubRecord::where('deleted_flag', 'N')->where('first_name', trim($record['first_name']))->where('last_name', trim($record['last_name']))->where('dob', $record['dob'])->where('gender', trim($record['gender']))->where('mobile', trim($record['mobile']))->where('ssn', $record['ssn']);
		$query = $query->first();
		return $query;
	}

	public function checkDuplicateSSN($record, $id = "")
	{
		$query = HubRecord::where('deleted_flag', 'N')->where('ssn', $record['ssn']);
		if ($id != "") {
			$query->where('id', '!=', $id);
		}
		$query = $query->first();
		return $query;
	}

	public function searchUserData($search)
	{
		$filter = $search['q'];
		$query = HubRecord::select('hub_record.id', 'hub_record.first_name', 'hub_record.last_name', 'hub_record.gender', 'hub_record.mobile', 'hub_record.dob', 'hub_company.agency_name')
			->leftjoin('hub_record_agency', function ($join) {
				$join->on('hub_record_agency.hub_record_id', '=', 'hub_record.id');
				$join->where('hub_record_agency.del_flag', 'N');
			})->leftjoin('hub_company', function ($join) {
				$join->on('hub_company.id', '=', 'hub_record_agency.agency_id');
				$join->where('hub_company.delete_flag', 'N');
			})->where('hub_record.deleted_flag', 'N')->whereRaw('(LOWER(CONCAT_WS(" ",hub_record.first_name,hub_record.last_name)) LIKE "%' . strtolower($filter) . '%"  OR LOWER(hub_record.gender) = "' . strtolower($filter) . '" OR hub_record.mobile ="' . $filter . '" OR hub_record.ssn ="' . str_replace('-', '', $filter) . '")');
		$query = $query->get();

		return $query;
	}

	public function getPhoneAndSSN($ssn, $contact)
	{
		$query = HubRecord::where('deleted_flag', 'N')
			->whereRaw('RIGHT(ssn, 4) = ?', $ssn)
			->where(function ($q) use ($contact) {
				$q->where('phone', $contact)
					->orWhere('mobile', $contact)
					->orWhere('email', $contact);
			})
			->first();
		return $query;
	}
}
