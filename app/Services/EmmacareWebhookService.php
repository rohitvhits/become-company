<?php

namespace App\Services;

use App\Model\EmmacareWebhook;

class EmmacareWebhookService
{

	public  function save($data){
	
		$data['created_at'] = date('Y-m-d H:i:s');

		$data['del_flag'] = "N";
		$insert = new EmmacareWebhook($data);
		$insert_id = $insert->save();
		return $insert_id;
	}
}
