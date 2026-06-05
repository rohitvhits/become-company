<?php
namespace App\Services;
use App\Model\UserSendPatientDocumentLog;

class UserSendPatientDocumentLogService{

	public function save($data){
        $userId = Auth()->user();
		$data['created_date']=date('Y-m-d H:i:s');
		$data['created_by']=$userId['id'];

		$insert = new UserSendPatientDocumentLog($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
    }

	public function getByDocID($documentId){
		// Get the latest send-mail log per document_id
		$latestIds = UserSendPatientDocumentLog::selectRaw('MAX(id) as id')
			->whereIn('document_id', $documentId)
			->groupBy('document_id')
			->pluck('id');

		return UserSendPatientDocumentLog::select(
				'user_send_patient_document_log.document_id',
				'user_send_patient_document_log.created_date',
				'user_send_patient_document_log.email',
				'user_send_patient_document_log.send_back_to_agency',
				'users.first_name',
				'users.last_name'
			)
			->leftJoin('users', 'users.id', '=', 'user_send_patient_document_log.created_by')
			->whereIn('user_send_patient_document_log.id', $latestIds)
			->get()
			->keyBy('document_id');
	}

	public function getLatestSendBackByDocIDs($documentIds){
		// Get the latest log where send_back_to_agency = 1 per document_id
		$latestIds = UserSendPatientDocumentLog::selectRaw('MAX(id) as id')
			->whereIn('document_id', $documentIds)
			->where('send_back_to_agency', 1)
			->groupBy('document_id')
			->pluck('id');

		if ($latestIds->isEmpty()) {
			return collect();
		}

		return UserSendPatientDocumentLog::select(
				'user_send_patient_document_log.document_id',
				'user_send_patient_document_log.created_date',
				'user_send_patient_document_log.email',
				'user_send_patient_document_log.send_back_to_agency',
				'user_send_patient_document_log.note',
				'users.first_name',
				'users.last_name'
			)
			->leftJoin('users', 'users.id', '=', 'user_send_patient_document_log.created_by')
			->whereIn('user_send_patient_document_log.id', $latestIds)
			->get()
			->keyBy('document_id');
	}
}