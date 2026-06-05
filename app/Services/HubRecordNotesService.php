<?php

namespace App\Services;

use App\Helpers\Utility;
use App\Model\Language;
use App\Model\HubRecordNotes;

class HubRecordNotesService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";

		$insert = new HubRecordNotes($data);
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
		$update = HubRecordNotes::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = HubRecordNotes::where($where)->update($data);
		return $update;
	}

	public function getHubNotesData($id, $hubRecordAgencyId = "", $hubAgencyId = "")
	{
		$query = HubRecordNotes::selectRaw('hub_record_notes.id,hub_record_notes.hub_record_id,hub_record_notes.created_date,hub_record_notes.message,hub_record_notes.subject,hub_record_notes.message_status,users.id as uid,users.first_name,users.last_name,users.name,master_table.name,hub_record_notes.flag,hub_record_notes.reason')
			->join('users', function ($join) {
				$join->on('hub_record_notes.created_by', '=', 'users.id');
			})->join('master_table', function ($join) {
				$join->on('master_table.id', '=', 'hub_record_notes.subject');
			})->where('hub_record_notes.delete_flag', 'N')->where('hub_record_id', $id)->where('hub_agency_id', $hubAgencyId)->where('hub_record_agency_id', $hubRecordAgencyId)->orderBy('id', 'desc')->get();
		return $query;
	}

	public function getDetailById($id)
	{
		$query = HubRecordNotes::selectRaw('hub_record_notes.id,hub_record_notes.hub_record_id,hub_record_notes.created_date,hub_record_notes.message,hub_record_notes.subject,hub_record_notes.message_status,users.id as uid,users.first_name,users.last_name,users.name,master_table.name,hub_record_notes.flag,hub_record_notes.reason')
			->join('users', function ($join) {
				$join->on('hub_record_notes.created_by', '=', 'users.id');
			})->join('master_table', function ($join) {
				$join->on('master_table.id', '=', 'hub_record_notes.subject');
			})->where('hub_record_notes.delete_flag', 'N')->where('hub_record_id', $id)->get();
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
		$query = HubRecordNotes::with(['users:id,first_name,last_name'])->select('hub_record_notes.hub_record_id', 'hub_record.first_name', 'hub_record.last_name', 'hub_company.agency_name', 'master_table.name as subject', 'hub_record_notes.message', 'hub_record_notes.created_by', 'hub_record_notes.created_date')
			->leftjoin('hub_record', function ($join) {
				$join->on('hub_record.id', '=', 'hub_record_notes.hub_record_id');
				$join->where('hub_record_notes.delete_flag', 'N');
			})
			->leftjoin('master_table', function ($join) {
				$join->on('master_table.id', '=', 'hub_record_notes.subject');
				$join->where('master_table.del_flag', 'N');
			})
			->leftjoin('hub_company', function ($join) {
				$join->on('hub_company.id', '=', 'hub_record_notes.hub_agency_id');
				$join->where('hub_company.delete_flag', 'N');
			})
			->leftjoin('hub_record_agency', function ($join) {
				$join->on('hub_record_agency.hub_record_id', '=', 'hub_record.id');
				$join->where('hub_company.delete_flag', 'N');
			});

		if (isset($search_data['agency_fk'][0]) && !empty($search_data['agency_fk'][0])) {
			$query->whereIn('hub_record_notes.hub_agency_id', $search_data['agency_fk']);
		}

		if (isset($search_data['first_name']) && !empty($search_data['first_name'])) {
			$query->where('first_name', $search_data['first_name']);
		}

		if (isset($search_data['last_name']) && !empty($search_data['last_name'])) {
			$query->where('last_name', $search_data['last_name']);
		}
		if (!empty($search_data['created_date'])) {
			$exploder = explode('-', $search_data['created_date']);
			$startDate = date('Y-m-d', strtotime($exploder[0]));
			$endDate = date('Y-m-d', strtotime($exploder[1]));
			$query->whereDate('hub_record_notes.created_date', '>=', $startDate)
				->whereDate('hub_record_notes.created_date', '<=', $endDate);
		}
		if (isset($search_data['created_by']) && $search_data['created_by'] != '' && $search_data['created_by'] != 'undefined') {
			$query->where('hub_record_notes.created_by', $search_data['created_by']);
		}
		if (isset($search_data['subject']) && $search_data['subject'] != '') {
			$query->where('subject', $search_data['subject']);
		}
		if (isset($search_data['hub_record_id']) && !empty($search_data['hub_record_id'])) {
			$query->where('hub_record_id', $search_data['hub_record_id']);
		}
		$query->whereRaw($where);
		$query->groupBy('hub_record_notes.id');
		if (isset($export) && !empty($export)) {
			$query = $query->orderBy('hub_record_notes.id', 'desc')->get();
		} else {
			$query = $query->orderBy('hub_record_notes.id', 'desc')->simplePaginate(20);
		}
		return $query;
	}

	public function getAllNotesListApi($id, $agency_id, $hub_record_agency_id, $offset)
	{
		$query = HubRecordNotes::with(['users:id,first_name,last_name'])->select('hub_record_notes.id', 'hub_record_id', 'hub_record.first_name', 'hub_record.last_name', 'master_table.name as subject', 'master_table.id as subject_id', 'hub_record_notes.message', 'hub_record_notes.created_by', 'hub_record_notes.created_date')
			->leftjoin('master_table', function ($join) {
				$join->on('master_table.id', '=', 'hub_record_notes.subject');
				$join->where('master_table.del_flag', 'N');
			})->leftjoin('hub_record', function ($join) {
				$join->on('hub_record.id', '=', 'hub_record_notes.hub_record_id');
			})->where('hub_record_id', $id)->where('hub_record_notes.hub_agency_id', $agency_id)->where('hub_record_notes.hub_record_agency_id', $hub_record_agency_id);
		$query = $query->orderBy('hub_record_notes.id', 'desc')->offset($offset)->limit(50)->get();
		return $query;
	}
	public function getById($id)
	{
		$query = HubRecordNotes::where('id', $id)->first();
		return $query;
	}
}
