<?php

namespace App\Services;

use App\Model\InvoiceUpload;

class InvoiceUploadService
{

	public static function getInvoiceTableList($patientId,$agencyId)
	{
		$data = InvoiceUpload::with('users','getService:id,name')->where('patient_id', $patientId)->where('agency_id',$agencyId)->orderBy('id', 'DESC')->get();

		return [
			'data' => $data,
		];
	}
	
	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new InvoiceUpload($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}

	public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$update = InvoiceUpload::where($where)->update($data);
		return $update;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = InvoiceUpload::where($where)->update($data);
		return $update;
	}

	public function getDetailsById($id)
	{
		$update = InvoiceUpload::where('id', $id)->first();
		return $update;
	}
}
