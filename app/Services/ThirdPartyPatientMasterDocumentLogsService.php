<?php

namespace App\Services;

use App\Model\ThirdPartyPatientMasterDocumentLogs;

class ThirdPartyPatientMasterDocumentLogsService
{
	protected const COMMON_DATE_FORMAT_YYYYMMDD_HIS = "Y-m-d H:i:s";

	public function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS);
		if ($auth) {
			$data['created_by'] = $auth['id'];
		}
		$data['deleted_flag'] = "N";

		$insert = new ThirdPartyPatientMasterDocumentLogs($data);
		$insert->save();
		return $insert->id;
	}

	public function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS);
		if ($auth) {
			$data['updated_by'] = $auth['id'];
		}

		return ThirdPartyPatientMasterDocumentLogs::where($where)->update($data);
	}

	public function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS);
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = "Y";

		return ThirdPartyPatientMasterDocumentLogs::where($where)->update($data);
	}

	public function getById($id)
	{
		return ThirdPartyPatientMasterDocumentLogs::with(['thirdPartyPatientMasterDetails', 'documentDetails', 'userDetails'])
			->where('deleted_flag', 'N')
			->where('id', $id)
			->first();
	}
}
