<?php

namespace App\Services;

use App\DocumentSentReport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\HubRecordDoc;
use App\Model\Patient;
use App\Model\WriteDocument;
use App\Template;
use App\Helpers\Utility;

class HubRecordDocService
{

	public  function save($data)
	{
		$auth = auth()->user();
		if (isset($data['flag']) && $data['flag'] == 1) {
			$data['created_date'] = $data['created_date'];
			$data['created_by'] = $data['created_by'];
		} else {

			if (isset($auth['id'])) {
				$data['created_date'] = date('Y-m-d H:i:s');
				$data['created_by'] = $auth['id'];
			}
		}
		$data['deleted_flag'] = "N";

		$insert = new HubRecordDoc($data);
		$insert->save();
		$insert_ids = $insert->id;

		return $insert_ids;
	}

	public  function saveNew($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');

		$data['deleted_flag'] = "N";

		$insert = new HubRecordDoc($data);
		$insert->save();
		$insert_ids = $insert->id;

		return $insert_ids;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = HubRecordDoc::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = HubRecordDoc::where($where)->update($data);
		return $update;
	}

	public function getAllDocumentByPatientId($id, $hub_record_agency_id, $hub_agency_id)
	{
		$update = HubRecordDoc::select('hub_record_doc.id', 'hub_record_doc.hub_record_id', 'hub_record_doc.document_name', 'hub_record_doc.attachment', 'hub_record_doc.deleted_flag', 'hub_record_doc.created_date', 'hub_record_doc.created_by', 'hub_record_doc.updated_date', 'hub_record_doc.updated_by', 'users.first_name', 'users.last_name', 'hub_record_doc.flag')->where('hub_record_id', $id)->where('hub_agency_id', $hub_agency_id)->where('hub_record_agency_id', $hub_record_agency_id)->where('deleted_flag', 'N')
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'hub_record_doc.created_by');
				$join->where('users.delete_flag', 'N');
			})->paginate(50);
		return $update;
	}

	public function getDetailsById($id)
	{
		$update = HubRecordDoc::where('id', $id);
		return $update->first();
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
		$query = HubRecordDoc::with(['userDetails:id,first_name,last_name'])->select('hub_record_doc.id', 'hub_record_id', 'hub_record.first_name', 'hub_record.last_name', 'hub_company.agency_name', 'hub_record_doc.created_by', 'hub_record_doc.created_date', 'document_name', 'attachment')
			->leftjoin('hub_record', function ($join) {
				$join->on('hub_record.id', '=', 'hub_record_doc.hub_record_id');
				$join->where('hub_record_doc.deleted_flag', 'N');
			})
			->leftjoin('hub_company', function ($join) {
				$join->on('hub_company.id', '=', 'hub_record_doc.hub_agency_id');
				$join->where('hub_company.delete_flag', 'N');
			});

		if (isset($search_data['agency_fk'][0]) && !empty($search_data['agency_fk'][0])) {
			$query->whereIn('hub_record_doc.hub_agency_id', $search_data['agency_fk']);
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
			$query->whereDate('hub_record_doc.created_date', '>=', $startDate)
				->whereDate('hub_record_doc.created_date', '<=', $endDate);
		}
		if (isset($search_data['created_by']) && $search_data['created_by'] != '' && $search_data['created_by'] != 'undefined') {
			$query->where('hub_record_doc.created_by', $search_data['created_by']);
		}
		if (isset($search_data['hub_record_id']) && !empty($search_data['hub_record_id'])) {
			$query->where('hub_record_id', $search_data['hub_record_id']);
		}
		$query->whereRaw($where);
		$query->groupBy('hub_record_doc.id');
		if (isset($export) && !empty($export)) {
			$query = $query->orderBy('hub_record_doc.id', 'desc')->get();
		} else {
			$query = $query->orderBy('hub_record_doc.id', 'desc')->simplePaginate(20);
		}
		return $query;
	}

	public function getAllDocListApi($id, $agency_id, $hub_record_agency_id, $offset)
	{
		$query = HubRecordDoc::select('hub_record_doc.id', 'hub_record_doc.hub_record_id', 'hub_record_doc.document_name', 'hub_record_doc.attachment', 'hub_record_doc.deleted_flag', 'hub_record_doc.created_date', 'hub_record_doc.created_by', 'hub_record_doc.updated_date', 'hub_record_doc.updated_by', 'users.first_name', 'users.last_name')->where('hub_record_id', $id)->where('hub_record_agency_id', $hub_record_agency_id)->where('hub_record_doc.deleted_flag', 'N')
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'hub_record_doc.created_by');
				$join->where('users.delete_flag', 'N');
			})->leftjoin('hub_record', function ($join) {
				$join->on('hub_record.id', '=', 'hub_record_doc.hub_record_id');
			})->where('hub_record_doc.hub_agency_id', $agency_id);
		$query = $query->orderBy('hub_record_doc.id', 'desc')->offset($offset)->limit(50)->get();
		return $query;
	}

	public function getDocDetailsById($id, $hub_record_id)
	{
		$update = HubRecordDoc::where('id', $id)->where('hub_record_id', $hub_record_id);
		return $update->first();
	}

	public function getDetailsByIdNew($id, $patientID)
	{
		$update = HubRecordDoc::where('id', $id)->where('hub_record_id', $patientID);
		return $update->first();
	}
}
