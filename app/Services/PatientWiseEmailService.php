<?php

namespace App\Services;
use App\Model\PatientWiseServiceEmail;

class PatientWiseEmailService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = 'N';

		$insert = new PatientWiseServiceEmail($data);
		$insert->save();
		$insertId = $insert->id;

		return $insertId;
	}
}
