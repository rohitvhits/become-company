<?php
namespace App\Services;

use App\Helpers\Utility;
use App\Model\Language;
use App\Model\HubUtilizationImportLog;
use Illuminate\Support\Facades\Log;

class HubUtilizationImportLogService
{
	public function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";
		$data['status'] = $data['status'] ?? 'pending';

		$insert = new HubUtilizationImportLog($data);
		$insert->save();
		return $insert->id;
	}
	
	public function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if(isset($auth['id'])){
			$data['updated_by'] = $auth['id'];
		}
		
		$update = HubUtilizationImportLog::where($where)->update($data);
		return $update;
	}
	public function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = HubUtilizationImportLog::where($where)->update($data);
		return $update;
	}

	public function logImportStart($fileName, $agencyId, $totalRecords)
	{
		return $this->save([
			'file_name' => $fileName,
			'agency_id' => $agencyId,
			'total_records' => $totalRecords,
			'successful_records' => 0,
			'failed_records' => 0,
			'duplicate_records' => 0,
			'updated_records' => 0,
			'status' => 'processing'
		]);
	}

	public function updateImportProgress($logId, $stats)
	{
		return $this->update($stats, ['id' => $logId]);
	}

	public function completeImport($logId, $stats, $errorDetails = null)
	{
		$data = array_merge($stats, [
			'status' => 'completed',
			'error_details' => $errorDetails
		]);
		return $this->update($data, ['id' => $logId]);
	}

	public function failImport($logId, $errorDetails)
	{
		return $this->update([
			'status' => 'failed',
			'error_details' => $errorDetails
		], ['id' => $logId]);
	}

	public function getImportLogs($search_data = [], $export = "")
	{
		$auth = auth()->user();
		$where = 'hub_utilization_import_logs.deleted_flag = "N"';

		$query = HubUtilizationImportLog::with(['users:id,first_name,last_name', 'agencyDetail:id,agency_name'])
			->select('hub_utilization_import_logs.*', 'hub_company.agency_name')
			->leftjoin('hub_company', function ($join) {
				$join->on('hub_company.id', '=', 'hub_utilization_import_logs.agency_id');
				$join->where('hub_company.delete_flag', 'N');
			});

		if(isset($search_data['agency_fk'][0]) && !empty($search_data['agency_fk'][0])){
			$query->whereIn('agency_id', $search_data['agency_fk']);
		}

		if(isset($search_data['file_name']) && !empty($search_data['file_name'])){
			$query->where('file_name', 'like', '%' . $search_data['file_name'] . '%');
		}

		if(isset($search_data['status']) && !empty($search_data['status'])){
			$query->where('status', $search_data['status']);
		}

		if (!empty($search_data['created_date'])) {
			$exploder = explode('-', $search_data['created_date']);
			$startDate = date('Y-m-d', strtotime($exploder[0]));
			$endDate = date('Y-m-d', strtotime($exploder[1]));
			$query->whereDate('hub_utilization_import_logs.created_date', '>=', $startDate)
				->whereDate('hub_utilization_import_logs.created_date', '<=', $endDate);
		}

		if (isset($search_data['created_by']) && $search_data['created_by'] != '' && $search_data['created_by'] != 'undefined') {
			$query->where('hub_utilization_import_logs.created_by', $search_data['created_by']);
		}

		$query->whereRaw($where);

		if(isset($export) && !empty($export)){
			return $query->orderBy('hub_utilization_import_logs.id', 'desc')->get();
		}

		return $query->orderBy('hub_utilization_import_logs.id', 'desc')->paginate(20);
	}
}