<?php

namespace App\Services;
use App\Model\DocumentSendSmsLog;
class DocumentSendSmsLogService
{
	public  function save($data){
		$userId = Auth()->user();
		$data['created_date']=date('Y-m-d H:i:s');
		if(isset($userId['id']) && $userId['id'] !=""){
			$data['created_by']=$userId['id'];
		}
		

		$insert = new DocumentSendSmsLog($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;

	}
	
    public function totalSendSMS($caregiverId,$documentId){
        return DocumentSendSmsLog::where('del_flag','N')->where('caregiver_id',$caregiverId)->where('document_id',$documentId)->get();
    }

    public function getListByDocumentWise($documentId,$caregiverId){
        return DocumentSendSmsLog::with(['userDetail:id,first_name,last_name'])->where('del_flag','N')->where('caregiver_id',$caregiverId)->where('document_id',$documentId)->get();
    }
}
