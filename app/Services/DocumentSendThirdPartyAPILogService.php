<?php
namespace App\Services;
use App\Model\DocumentSendThirdPartyAPILog;

class DocumentSendThirdPartyAPILogService{

	public function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new DocumentSendThirdPartyAPILog($data);
		$insert_id = $insert->save();
		return $insert_id;
		
	}
}