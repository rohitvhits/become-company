<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Model\PaymentReceivedLog;
use App\Helpers\Utility;
class PaymentReceivedLogService
{

	public function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new PaymentReceivedLog($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		if(isset($auth['id'])){
			$data['updated_by'] = $auth['id'];
		}
		

		$update = PaymentReceivedLog::where($where)->update($data);
		return $update;
	}

	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = PaymentReceivedLog::where($where)->update($data);
		return $update;
	}
}