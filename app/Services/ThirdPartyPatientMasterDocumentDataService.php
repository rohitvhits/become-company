<?php

namespace App\Services;

use App\Model\ThirdPartyPatientMasterDocumentData;

class ThirdPartyPatientMasterDocumentDataService
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

		$insert = new ThirdPartyPatientMasterDocumentData($data);
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

		return ThirdPartyPatientMasterDocumentData::where($where)->update($data);
	}

	public function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS);
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = "Y";

		return ThirdPartyPatientMasterDocumentData::where($where)->update($data);
	}

	public function getById($id)
	{
		return ThirdPartyPatientMasterDocumentData::with(['thirdPartyPatientMasterDetails', 'documentDetails', 'userDetails'])
			->where('deleted_flag', 'N')
			->where('id', $id)
			->first();
	}

	public function getByThirdPartyDocId($third_party_id,$patient_id)
	{
		return ThirdPartyPatientMasterDocumentData::where('deleted_flag', 'N')
			->where('third_party_id', $third_party_id)
			->where('patient_id', $patient_id)
			->pluck('document_id')->toArray();
	}

	public function getByServiceId($doc_id,$service_id)
	{
		$query = ThirdPartyPatientMasterDocumentData::with(['masterDetails:id,name'])->select('service_id','document_id','id')->where('deleted_flag', 'N');
		if($service_id !=""){
			$query->where('service_id', $service_id);
		}
		return $query->where('document_id', $doc_id)->get();
	}
}
