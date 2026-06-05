<?php

namespace App\Services;
use App\Model\HubTokenWiseApiCall;

class HubTokenWiseApiCallService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";

		$insert = new HubTokenWiseApiCall($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
    }
}
