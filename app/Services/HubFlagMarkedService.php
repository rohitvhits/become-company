<?php

namespace App\Services;

use App\Model\HubFlagMarked as FlagMarked;
use App\Helpers\Utility;
use Illuminate\Support\Facades\DB;

class HubFlagMarkedService
{

	public function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		if (isset($auth['id'])) {
			$data['created_by'] = $auth['id'];
		}
		$insert = new FlagMarked($data);
		$insert_id = $insert->save();

		return $insert_id;
	}

	public static function getALLHubData($search, $type)
	{
		$auth = auth()->user();
		$where = 'hub_record.deleted_flag ="N"';


		if (isset($search['first_name']) && $search['first_name'] != '') {

			$where .= ' and CONCAT_WS("",hub_record.first_name," ",hub_record.last_name) LIKE "%' . $search['first_name'] . '%"';
		}
		if (isset($search['mobile']) && $search['mobile'] != '') {

			$where .= ' and hub_record.mobile LIKE "%' . $search['mobile'] . '%" or  hub_record.phone LIKE "%' . $search['mobile'] . '%"';
		}
		if (isset($search['email']) && $search['email'] != '') {

			$where .= ' and hub_record.email LIKE "%' . $search['email'] . '%"';
		}

		if (isset($search['ssn']) && $search['ssn'] != '') {

			$where .= ' and hub_record.ssn LIKE "%' . str_replace('-', '', $search['ssn']) . '%"';
		}

		if (isset($search['agency_fk']) && $search['agency_fk'] != '') {
			$ids = implode(',', $search['agency_fk']);
			$where .= " AND hub_record_agency.agency_id IN ($ids)";
		}

		if (isset($search['patient_code']) && $search['patient_code'] != "") {
			$where .= ' and hub_record.id LIKE "%' .  $search['patient_code']  . '%"';
		}

		if (isset($search['created_date']) && $search['created_date'] != '') {
			$exploder = explode('-', $search['created_date']);
			$where .= ' and DATE_FORMAT(hub_flag_marked.created_at,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(hub_flag_marked.created_at,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}

		if (isset($search['created_by_ny_id']) && $search['created_by_ny_id'] != '') {
			if ($search['created_by_ny_id'] != 'undefined') {
				$where .= ' and hub_flag_marked.created_by = ' . $search['created_by_ny_id'];
			}
		}

		if (isset($search['dob']) && $search['dob'] != '') {
			$explode = explode('-', $search['dob']);

			$where .= ' and DATE_FORMAT(hub_record.dob,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(hub_record.dob,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}


		$query = FlagMarked::select(
			'hub_record.*',
			'hub_flag_marked.created_at',
			'hub_flag_marked.reason as reasonNotes',
			'users.first_name as uFname',
			'users.last_name as uLname',
			'hub_flag_marked.is_flag_read',
			'hub_flag_marked.id as flag_id',
			'hub_record_agency.status as agency_status',
			DB::raw("CASE 
                    WHEN COUNT(DISTINCT hub_company.id) > 1 THEN 'Multiple' 
                    ELSE MAX(hub_company.agency_name) 
                 END as agency_name")
		)
			->leftjoin('hub_record', function ($join) {
				$join->on('hub_record.id', '=', 'hub_flag_marked.record_id');
				$join->where('hub_record.deleted_flag', 'N');
			})->leftjoin('hub_record_agency', function ($join) {
				$join->on('hub_record.id', '=', 'hub_record_agency.hub_record_id');
				$join->where('hub_record_agency.del_flag', 'N');
			})->leftjoin('hub_company', function ($join) {
				$join->on('hub_company.id', '=', 'hub_record_agency.agency_id');
				$join->where('hub_company.delete_flag', 'N');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'hub_flag_marked.created_by');
			})->when($search['status'], function ($q) use ($search) {
				return $q->where('hub_record_agency.status', $search['status']);
			})

			->whereRaw($where)->where('hub_flag_marked.type', $type)->groupBy('hub_record.id', 'hub_flag_marked.id')->orderBy('hub_flag_marked.id', 'desc')->Paginate(20);

		return $query;
	}

	public function getFlagAllDocumentByPatientId($type)
	{

		$auth = auth()->user();
		$query = FlagMarked::select('hub_record_doc.*', 'hub_flag_marked.created_at', 'hub_flag_marked.reason as reasonNotes', 'users.first_name', 'users.last_name', 'hub_flag_marked.is_flag_read', 'hub_flag_marked.id as flag_id')
			->leftjoin('hub_record_doc', function ($join) {
				$join->on('hub_record_doc.id', '=', 'hub_flag_marked.record_id');
				$join->where('hub_record_doc.deleted_flag', 'N');
			})

			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'hub_flag_marked.created_by');
				$join->where('users.delete_flag', 'N');
			})->where('hub_flag_marked.type', $type)
			->leftjoin('hub_record', function ($join) {
				$join->on('hub_record.id', '=', 'hub_record_doc.hub_record_id');
				$join->where('hub_record.deleted_flag', 'N');
			});

		$query->where('hub_record_doc.deleted_flag', 'N');
		$query = $query->orderBy('hub_flag_marked.created_at', 'desc')->paginate(20);
		return $query;
	}

	public function getFlagTaskData($type)
	{
		$auth = auth()->user();
		$query = FlagMarked::select('hub_task_master.*', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae', 'hub_task_master.priority', 'hub_task_master.flag', 'hub_flag_marked.reason as reasonNotes', 'hub_flag_marked.is_flag_read', 'hub_flag_marked.id as flag_id')
			->leftjoin('hub_task_master', function ($join) {
				$join->on('hub_task_master.id', '=', 'hub_flag_marked.record_id');
				$join->where('hub_task_master.del_flag', 'N');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'hub_flag_marked.created_by');
				$join->where('users.delete_flag', 'N');
			})->where('hub_flag_marked.type', $type)
			->leftjoin('users as us', function ($join) {
				$join->on('us.id', '=', 'hub_task_master.assign_id');
				$join->where('us.delete_flag', 'N');
			})
			->where('hub_task_master.del_flag', 'N');
		if (auth()->user()->id != 482) {
			$query->whereRaw('(hub_task_master.assign_id="' . auth()->user()->id . '" OR hub_task_master.created_by="' . auth()->user()->id . '")');
		}
		$query = $query->orderBy('hub_flag_marked.id', 'desc')->paginate(50);
		return $query;
	}

	public function getAllFlagData($type)
	{
		$auth = auth()->user();
		$query = FlagMarked::select('hub_record_notes.*', 'users.id as uid', 'users.first_name', 'users.last_name', 'users.name', 'hub_flag_marked.is_flag_read', 'hub_flag_marked.id as flag_id', 'hub_flag_marked.reason as reasonNotes')
			->leftjoin('hub_record_notes', function ($join) {
				$join->on('hub_record_notes.id', '=', 'hub_flag_marked.record_id');
			})->leftjoin('hub_record', function ($join) {
				$join->on('hub_record.id', '=', 'hub_record_notes.hub_record_id');
				$join->where('hub_record.deleted_flag', 'N');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'hub_flag_marked.created_by');
				$join->where('users.delete_flag', 'N');
			})->where('hub_flag_marked.type', $type);

		$where = "hub_flag_marked.type ='" . $type . "' and hub_flag_marked.delete_flag ='N' ";

		$query = $query->whereRaw('hub_record_notes.delete_flag ="N" and ' . $where)->whereNotNull('hub_record_notes.reason')->groupBy('hub_flag_marked.id')->orderBy('hub_flag_marked.id', 'desc');


		return $query->paginate(50);
	}

	public static  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = FlagMarked::where($where)->update($data);
		return $update;
	}

	public static function getDetailsById($id)
	{
		$query = FlagMarked::where('id', $id)->where('delete_flag', 'N')->first();
		return $query;
	}
}
