<?php

namespace App\Services;
use App\Model\TaskHealthMaster;
use App\Model\TaskHealthFlags;
use App\Model\Patient;
use App\Model\PatientServiceRequest;
use App\Model\PatientWiseServiceRequest;
use App\Model\DocumentPatient;
use App\Model\DocumentUploadModal;
use App\Model\SendTaskHealthDocument;
use App\Services\PatientServicesRequest;
use App\Helpers\Utility;

class TaskHealthMasterService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";

		$insert = new TaskHealthMaster($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}

	public function dataList($search)
	{
		$auth = auth()->user();

		$query = TaskHealthMaster::with([
				'agencyDetails:id,agency_name',
				'userDetails:id,first_name,last_name',
				'patientDetails:id,deleted_flag',
				'flags.pocCheckedByUser:id,first_name,last_name',
				'flags.mdoCheckedByUser:id,first_name,last_name',
				'flags.alertCheckedByUser:id,first_name,last_name',
				'flags.supervisionCheckedByUser:id,first_name,last_name',
				'flags.updatedByUser:id,first_name,last_name',
				'latestCriticalAlert'
							])
			->select('id','patient_id','first_name','middle_name','last_name','type','dob','phone','mobile','agency_id','service_id','created_date','created_by','status','old_patient_id','task_health_patient_id','task_id','gender','is_converted')
			->where('deleted_flag','N');

		if(!empty($search)){
			if(isset($search['agency_id']) && $search['agency_id']){
				$query->where('agency_id',$search['agency_id']);
			}
			if(isset($search['created_date']) && $search['created_date']){
				$explode = explode('-', $search['created_date']);
				$query->whereBetween('created_date',[date('Y-m-d H:i:s',strtotime($explode[0].'00:00:00')),date('Y-m-d H:i:s',strtotime($explode[1].'23:59:59'))]);
			}

			if(isset($search['type']) && $search['type']){
				$query->where('type','LIKE',"%".$search['type']."%");
			}

			if (!empty($search['patient_name'])) {
				$name = trim($search['patient_name']);
				$query->where(function($q) use ($name) {
					$q->where('first_name', 'LIKE', "%{$name}%")
					  ->orWhere('last_name',  'LIKE', "%{$name}%")
					  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
				});
			}

			if (!empty($search['task_id'])) {
				$query->where('task_id', $search['task_id']);
			}
			if (!empty($search['th_patient_id'])) {
				$query->where('task_health_patient_id', $search['th_patient_id']);
			}
			if (!empty($search['mobile'])) {
				$query->where('mobile', 'LIKE', '%' . $search['mobile'] . '%');
			}
			if (!empty($search['critical_alert'])) {
				$this->applyCriticalAlertFilter($query, $search['critical_alert']);
			}

			// Flag filters
			if (!empty($search['poc_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('poc_check', 1));
			}
			if (!empty($search['mdo_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('mdo_check', 1));
			}
			if (!empty($search['alert_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('alert_check', 1));
			}
			if (!empty($search['supervision_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('supervision_check', 1));
			}
			if (!empty($search['assessment_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('assessment_check', 1));
			}
			if (!empty($search['kardex_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('kardex_check', 1));
			}
			if (!empty($search['patient_package_doc_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('patient_package_doc_check', 1));
			}
		}
		$query = $query->orderBy('id','desc')->paginate(50);

		return $query;
	}

	public function dataListExport($search)
	{
		$query = TaskHealthMaster::with([
				'agencyDetails:id,agency_name',
				'latestCriticalAlert',
				'flags.pocCheckedByUser:id,first_name,last_name',
				'flags.mdoCheckedByUser:id,first_name,last_name',
				'flags.alertCheckedByUser:id,first_name,last_name',
				'flags.supervisionCheckedByUser:id,first_name,last_name',
				'flags.assessmentCheckedByUser:id,first_name,last_name',
				'flags.kardexCheckedByUser:id,first_name,last_name',
			])
			->select('id','patient_id','first_name','last_name','type','dob','phone','mobile','agency_id','status','created_date','task_health_patient_id','task_id')
			->where('deleted_flag','N');

		if (!empty($search)) {
			if (!empty($search['agency_id']))    $query->where('agency_id', $search['agency_id']);
			if (!empty($search['type']))         $query->where('type', 'LIKE', '%' . $search['type'] . '%');
			if (!empty($search['status']))       $query->where('status', $search['status']);
			if (!empty($search['created_date'])) {
				$explode = explode('-', $search['created_date']);
				$query->whereBetween('created_date', [
					date('Y-m-d H:i:s', strtotime($explode[0] . '00:00:00')),
					date('Y-m-d H:i:s', strtotime($explode[1] . '23:59:59')),
				]);
			}
			if (!empty($search['patient_name'])) {
				$name = trim($search['patient_name']);
				$query->where(function($q) use ($name) {
					$q->where('first_name', 'LIKE', "%{$name}%")
					  ->orWhere('last_name',  'LIKE', "%{$name}%")
					  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
				});
			}
			if (!empty($search['task_id'])) {
				$query->where('task_id', $search['task_id']);
			}
			if (!empty($search['th_patient_id'])) {
				$query->where('task_health_patient_id', $search['th_patient_id']);
			}
			if (!empty($search['mobile'])) {
				$query->where('mobile', 'LIKE', '%' . $search['mobile'] . '%');
			}
			if (!empty($search['critical_alert'])) {
				$this->applyCriticalAlertFilter($query, $search['critical_alert']);
			}
			// Flag filters
			if (!empty($search['poc_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('poc_check', 1));
			}
			if (!empty($search['mdo_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('mdo_check', 1));
			}
			if (!empty($search['alert_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('alert_check', 1));
			}
			if (!empty($search['supervision_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('supervision_check', 1));
			}
			if (!empty($search['assessment_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('assessment_check', 1));
			}
			if (!empty($search['kardex_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('kardex_check', 1));
			}
			if (!empty($search['patient_package_doc_check'])) {
				$query->whereHas('flags', fn($q) => $q->where('patient_package_doc_check', 1));
			}
		}

		return $query->orderBy('id', 'desc');
	}

	/**
	 * Apply critical alert filter to any TaskHealthMaster query.
	 * Joins task_health_critical_alerts on task_id.
	 */
	private function applyCriticalAlertFilter($query, string $value): void
	{
		if ($value === 'none') {
			$query->whereDoesntHave('latestCriticalAlert');
			return;
		}

		$query->whereHas('latestCriticalAlert', function ($q) use ($value) {
			switch ($value) {
				case 'active':
					$q->where(function ($sq) {
						$sq->where('critical_alerts', 'LIKE', '%"alert":true%')
						   ->orWhere('critical_alerts', 'LIKE', '%s:5:"alert";b:1;%');
					});
					break;
				case 'clear':
					$q->where(function ($sq) {
						$sq->where('critical_alerts', 'LIKE', '%"alert":false%')
						   ->orWhere('critical_alerts', 'LIKE', '%s:5:"alert";b:0;%');
					});
					break;
				case 'pending':
					$q->where(function ($sq) {
						$sq->where('critical_alerts', 'NOT LIKE', '%"alert":true%')
						   ->where('critical_alerts', 'NOT LIKE', '%"alert":false%')
						   ->where('critical_alerts', 'NOT LIKE', '%s:5:"alert";b:1;%')
						   ->where('critical_alerts', 'NOT LIKE', '%s:5:"alert";b:0;%');
					});
					break;
				case 'resolved':
					$q->where('resolved_flag', 1);
					break;
				case 'unresolved':
					$q->where(function ($sq) {
						$sq->whereNull('resolved_flag')->orWhere('resolved_flag', '!=', 1);
					});
					break;
			}
		});
	}

	public function getDataById($id)
	{
		$query = TaskHealthMaster::with(['agencyDetails','userDetails'])->where('deleted_flag','N')->where('id',$id);
		$query = $query->orderBy('id','desc')->first();
		return $query;
	}

	public function getAllType()
	{
		$query = TaskHealthMaster::select('id','type')->where('deleted_flag','N')->wherenotnull('type')->groupBy('type');
		$query = $query->orderBy('id','desc')->get()->toArray();
		return $query;
	}

	public function getAllStatus()
	{
		$query = TaskHealthMaster::select('id','status')->where('deleted_flag','N')->wherenotnull('status')->groupBy('status');
		$query = $query->orderBy('id','desc')->get()->toArray();
		return $query;
	}

	public function getTPUrlByAgencyAndPortal($patientId,$agencyId){
		return TaskHealthMaster::select('task_health_master.id','task_health_master.service_id','task_health_master.requested_service_id','task_health_master.created_date','task_health_patient_id','task_id')->where('task_health_master.deleted_flag','N')->whereNotNull('task_health_master.third_party_callback_url')->where('task_health_master.patient_id',$patientId)->where('task_health_master.agency_id',$agencyId)->get();
	}

	public function getDetailsByIdAndPatientId($id,$patientId){
		return TaskHealthMaster::where('deleted_flag','N')->whereNotNull('third_party_callback_url')->where('patient_id',$patientId)->where('id',$id)->first();
	}

	public function revertPatient($taskHealthId, $newPatientId, $newAgencyId)
	{
		$taskHealth = TaskHealthMaster::where('id', $taskHealthId)->first();

		if (!$taskHealth) {
			return false;
		}

		$oldPatientId       = $taskHealth->patient_id;
		$oldAgencyId        = $taskHealth->agency_id;
		$requestedServiceId = $taskHealth->requested_service_id;
		$updatedBy          = auth()->user()->id ?? null;
		$now                = now();
		$log                = [];

		// ── 1. task_health_master ──────────────────────────────────────────────
		TaskHealthMaster::where('id', $taskHealthId)->update([
			'patient_id'     => $newPatientId,
			'old_patient_id' => $oldPatientId,
			'agency_id'      => $newAgencyId,
		]);
		$log[] = "task_health_master #{$taskHealthId}: patient_id {$oldPatientId} → {$newPatientId}, agency_id {$oldAgencyId} → {$newAgencyId}";

		// ── 2. patient_service_requests ───────────────────────────────────────
		if (!empty($requestedServiceId)) {
			$psrUpdated = PatientServiceRequest::where('id', $requestedServiceId)
				->where('patient_id', $oldPatientId)
				->update(['patient_id' => $newPatientId]);
			// get last id of this pateint id
			$dataId = PatientServiceRequest::where('del_flag', 'N')->where('patient_id', $newPatientId)->orderBy('id', 'desc')->first();
			if(!empty($dataId->status)){
				$lastStatus = $dataId->status;
			}

			if ($psrUpdated) {
				$log[] = "patient_service_requests #{$requestedServiceId}: patient_id {$oldPatientId} → {$newPatientId}";
			}

			// ── 3. patient_wise_service_requested ─────────────────────────────
			$pwsrUpdated = PatientWiseServiceRequest::where('patient_service_request_id', $requestedServiceId)
				->where('patient_id', $oldPatientId)
				->update(['patient_id' => $newPatientId]);

			if ($pwsrUpdated) {
				$log[] = "patient_wise_service_requested (service_request #{$requestedServiceId}): patient_id {$oldPatientId} → {$newPatientId} ({$pwsrUpdated} rows)";
			}

			// ── 4. document_patient ───────────────────────────────────────────
			$affectedDocIds = DocumentPatient::where('request_service_id', $requestedServiceId)
				->where('patient_id', $oldPatientId)
				->pluck('id')
				->toArray();

			if (!empty($affectedDocIds)) {
				DocumentPatient::whereIn('id', $affectedDocIds)
					->update(['patient_id' => $newPatientId]);

				$log[] = "document_patient (request_service_id #{$requestedServiceId}): patient_id {$oldPatientId} → {$newPatientId} (doc ids: " . implode(',', $affectedDocIds) . ")";

				// ── 5. document_upload_services ───────────────────────────────
				$dusUpdated = DocumentUploadModal::whereIn('document_id', $affectedDocIds)
					->where('patient_id', $oldPatientId)
					->update(['patient_id' => $newPatientId]);

				if ($dusUpdated) {
					$log[] = "document_upload_services: patient_id {$oldPatientId} → {$newPatientId} ({$dusUpdated} rows, doc ids: " . implode(',', $affectedDocIds) . ")";
				}
			}
		}

		// ── 6. task_health_document ───────────────────────────────────────────
		$thdUpdated = SendTaskHealthDocument::where('patient_id', $oldPatientId)
			->where('request_service_id', $requestedServiceId)
			->update(['patient_id' => $newPatientId]);

		if ($thdUpdated) {
			$log[] = "task_health_document: patient_id {$oldPatientId} → {$newPatientId} ({$thdUpdated} rows)";
		}

		// ── 7. patient_master – update new patient's task_health_link ─────────
		Patient::where('id', $newPatientId)->update([
			'task_health_link'          => $taskHealthId,
			'third_party_callback_url'  => $taskHealth->third_party_callback_url,
			'updated_date'              => $now,
			'updated_by'                => $updatedBy,
			'status' => $lastStatus
		]);
		$log[] = "patient_master #{$newPatientId}: task_health_link set to {$taskHealthId}";

		// ── 8. Log all changes ────────────────────────────────────────────────
		LogsService::save([
			'type'         => 'Task Health Revert Patient',
			'link'         => url('/task-health'),
			'module'       => 'Patient Appointment',
			'object_id'    => $newPatientId,
			'message'      => 'Patient reverted from ID ' . $oldPatientId . ' to ID ' . $newPatientId . ', agency changed from ' . $oldAgencyId . ' to ' . $newAgencyId . ' on task health #' . $taskHealthId,
			'old_response' => serialize(['patient_id' => $oldPatientId, 'agency_id' => $oldAgencyId, 'requested_service_id' => $requestedServiceId]),
			'new_response' => serialize(['task_health_master_id' => $taskHealthId, 'patient_id' => $newPatientId, 'agency_id' => $newAgencyId, 'changes' => $log]),
			'ip'           => Utility::getIP(),
		]);

		return [
			'old_patient_id'       => $oldPatientId,
			'new_patient_id'       => $newPatientId,
			'old_agency_id'        => $oldAgencyId,
			'new_agency_id'        => $newAgencyId,
			'requested_service_id' => $requestedServiceId,
		];
	}

	public function getTaskHealthDetails($task_health_link){
		return TaskHealthMaster::where('id', $task_health_link)->where('deleted_flag', 'N')->first();
	}

	public function linkPatient(int $masterId, int $patientId): void
	{
		TaskHealthMaster::where('id', $masterId)->update(['patient_id' => $patientId]);
	}

	public function taskLithWihoutPOCLink($pocTaskIds){
		return TaskHealthMaster::whereNotIn('task_id', $pocTaskIds)->where('deleted_flag', 'N')->pluck('task_id','patient_id');
	}

	public function getDetailsByTaskId($pocTaskIds){
		return TaskHealthMaster::whereIn('task_id', $pocTaskIds)->where('deleted_flag', 'N')->pluck('task_id','patient_id');
	}

	public  function update($data,$where){
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = auth()->user()->id;
		return TaskHealthMaster::where($where)->update($data);
	}

	public function getExistingTaskData($task_id,$agency_id){
	 return TaskHealthMaster::where('deleted_flag', 'N')
				->where('task_id', $task_id)
                ->where('agency_id',$agency_id)
				->orderBy('id', 'desc')->first();
	}

	public function getById($masterId){
		return TaskHealthMaster::find($masterId);
	}
}
