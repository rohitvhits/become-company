<?php
namespace App\Services;
use App\Model\AssignEsignDocument;

class AssignEsignDocumentService{

	public static function save($data){
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id']??"";
		$insert = new AssignEsignDocument($data);
		$insert->save();
		return $insert->id;
	}
}