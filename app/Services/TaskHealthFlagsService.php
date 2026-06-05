<?php

namespace App\Services;

use App\Model\TaskHealthFlags;
use App\Model\TaskHealthMaster;
use App\Model\TaskHealthCriticalAlert;
use App\Services\LogsService;
use App\Helpers\Utility;

class TaskHealthFlagsService
{

	public function saveFlags(?string $thPatientId, ?string $taskId, ?int $patientId, int $poc, int $mdo, int $alert, int $supervision = 0, int $assessment = 0, int $kardex = 0): TaskHealthFlags
	{
		// Visit context: key by task_id. Master context: key by task_health_patient_id.
		if ($taskId) {
			$flag = TaskHealthFlags::firstOrNew(['task_id' => $taskId]);
		} else {
			$flag = TaskHealthFlags::firstOrNew(['task_health_patient_id' => $thPatientId]);
		}

		if (!$flag->exists) {
			$flag->task_id                = $taskId;
			$flag->task_health_patient_id = $thPatientId ?? $flag->task_health_patient_id;
			$flag->patient_id             = $patientId;
			$flag->del_flag               = 'N';
			$flag->created_by             = auth()->id();
		}

		$now = now();
		$uid = auth()->id();

		$oldValues = [
			'poc'        => (int) $flag->poc_check,
			'mdo'        => (int) $flag->mdo_check,
			'alert'      => (int) $flag->alert_check,
			'supervision' => (int) $flag->supervision_check,
			'assessment' => (int) $flag->assessment_check,
			'kardex'     => (int) $flag->kardex_check,
		];

		if ((int)$flag->poc_check !== $poc) {
			$flag->poc_check      = $poc;
			$flag->poc_check_by   = $poc ? $uid : null;
			$flag->poc_check_date = $poc ? $now : null;
		}
		if ((int)$flag->mdo_check !== $mdo) {
			$flag->mdo_check      = $mdo;
			$flag->mdo_check_by   = $mdo ? $uid : null;
			$flag->mdo_check_date = $mdo ? $now : null;
		}
		$alertWasOn = (int)$flag->alert_check;
		if ((int)$flag->alert_check !== $alert) {
			$flag->alert_check      = $alert;
			$flag->alert_check_by   = $alert ? $uid : null;
			$flag->alert_check_date = $alert ? $now : null;
		}
		if ((int)$flag->supervision_check !== $supervision) {
			$flag->supervision_check      = $supervision;
			$flag->supervision_check_by   = $supervision ? $uid : null;
			$flag->supervision_check_date = $supervision ? $now : null;
		}
		if ((int)$flag->assessment_check !== $assessment) {
			$flag->assessment_check      = $assessment;
			$flag->assessment_check_by   = $assessment ? $uid : null;
			$flag->assessment_check_date = $assessment ? $now : null;
		}
		if ((int)$flag->kardex_check !== $kardex) {
			$flag->kardex_check      = $kardex;
			$flag->kardex_check_by   = $kardex ? $uid : null;
			$flag->kardex_check_date = $kardex ? $now : null;
		}

		$flag->updated_by = $uid;
		$flag->save();

		// When alert flag is unchecked (1 → 0), auto-resolve the linked critical alert
		if ($alertWasOn && $alert === 0 && !empty($taskId)) {
			$this->autoResolveCriticalAlert((string) $taskId, $uid);
		}

		LogsService::save([
			'type'         => 'Task Health Flags Save',
			'link'         => url('/task-health'),
			'module'       => 'Task Health Flags',
			'object_id'    => $flag->id,
			'message'      => 'Flags saved — Task ID: ' . ($taskId ?? '—') . ' | Patient ID: ' . ($thPatientId ?? '—')
			                . ' | POC: ' . $oldValues['poc'] . ' to ' . $poc
			                . ', MDO: ' . $oldValues['mdo'] . ' to ' . $mdo
			                . ', Alert: ' . $oldValues['alert'] . ' to ' . $alert
			                . ', Supervision: ' . $oldValues['supervision'] . ' to ' . $supervision
			                . ', Assessment: ' . $oldValues['assessment'] . ' to ' . $assessment
			                . ', Kardex: ' . $oldValues['kardex'] . ' to ' . $kardex,
			'old_response' => serialize($oldValues),
			'new_response' => serialize($flag),
			'ip'           => Utility::getIP(),
		]);

		return $flag;
	}

	public function updateFlag(TaskHealthMaster $master, string $flagType, int $value): void
	{
		$flag = TaskHealthFlags::firstOrNew([
			'task_health_patient_id' => $master->task_health_patient_id,
		]);

		if (!$flag->exists) {
			$flag->task_id              = $master->task_id;
			$flag->task_health_patient_id = $master->task_health_patient_id;
			$flag->patient_id           = $master->patient_id;
			$flag->del_flag             = 'N';
			$flag->created_by           = auth()->id();
		}

		$col   = $flagType . '_check';
		$byCol = $flagType . '_check_by';
		$dtCol = $flagType . '_check_date';

		$oldValue = (int) $flag->getOriginal($col);

		$flag->$col    = $value;
		$flag->$byCol  = $value ? auth()->id() : null;
		$flag->$dtCol  = $value ? now() : null;
		$flag->updated_by = auth()->id();
		$flag->save();

		LogsService::save([
			'type'         => 'Task Health Flag Update',
			'link'         => url('/task-health'),
			'module'       => 'Task Health Flags',
			'object_id'    => $flag->id,
			'message'      => 'Flag updated — Task Health Patient ID: ' . $master->task_health_patient_id
			                . ' | ' . strtoupper($flagType) . ': ' . $oldValue . ' to ' . $value,
			'old_response' => serialize([$flagType => $oldValue]),
			'new_response' => serialize([$flagType => $value]),
			'ip'           => Utility::getIP(),
		]);
	}

	/**
	 * Build a flagsMap keyed by task_id (string) for the given API task IDs.
	 */
	public function getFlagsMapByTaskIds(array $taskIds): array
	{
		$taskIds = array_values(array_filter(array_unique($taskIds)));
		if (empty($taskIds)) {
			return [];
		}

		$flagsMap = [];
		$flags = TaskHealthFlags::whereIn('task_id', $taskIds)
			->with(['pocCheckedByUser','mdoCheckedByUser','alertCheckedByUser','supervisionCheckedByUser','assessmentCheckedByUser','kardexCheckedByUser','patientPackageDocCheckedByUser','updatedByUser'])
			->get();

		foreach ($flags as $f) {
			$flagsMap[$f->task_id] = $f;
		}

		return $flagsMap;
	}

	public function getTaskIdsByPOC(){
		return TaskHealthFlags::where('del_flag','N')->where('poc_check',1)->pluck('task_id');
	}

	/**
	 * Auto-resolve any unresolved critical alert for the given task_id.
	 * Called when the alert flag is unchecked (1 → 0) in the flags modal.
	 */
	private function autoResolveCriticalAlert(string $taskId, int $userId): void
	{
		$record = TaskHealthCriticalAlert::where('task_id', $taskId)
			->where(function ($q) {
				$q->whereNull('resolved_flag')->orWhere('resolved_flag', '=', 1);
			})
			->where(function ($q) {
				$q->Where('deleted_flag', '=', 'N');
			})
			->first();
		if (!$record) {
			return;
		}

		$record->resolved_flag  = 0;
		$record->resolved_by    = NULL;
		$record->resolved_at    = NULL;
		$record->resolved_notes = NULL;
		$record->save();

		LogsService::save([
			'type'         => 'Critical Alert Unresolved',
			'link'         => url('/task-health/critical-alerts'),
			'module'       => 'Critical Alert',
			'object_id'    => $record->id,
			'message'      => 'Critical alert auto-resolved when alert flag was unchecked — Task ID: ' . $taskId,
			'new_response' => serialize(['alert_id' => $record->id, 'task_id' => $taskId, 'unresolved_by' => $userId, 'mode' => 'auto']),
			'ip'           => Utility::getIP(),
		]);
	}

	/**
	 * Set alert_check = 1 for the flag record tied to a given task_id.
	 * Used when a critical alert is resolved so the alert flag is automatically raised.
	 */
	public function setAlertByTaskId(string $taskId): void
	{
		$flag = TaskHealthFlags::firstOrNew(['task_id' => $taskId]);

		if (!$flag->exists) {
			$master = TaskHealthMaster::where('task_id', $taskId)->first();
			$flag->task_id                = $taskId;
			$flag->task_health_patient_id = $master->task_health_patient_id ?? null;
			$flag->patient_id             = $master->patient_id             ?? null;
			$flag->del_flag               = 'N';
			$flag->created_by             = auth()->id();
		}

		if ((int) $flag->alert_check !== 1) {
			$flag->alert_check      = 1;
			$flag->alert_check_by   = auth()->id();
			$flag->alert_check_date = now();
		}

		$flag->updated_by = auth()->id();
		$flag->save();

		LogsService::save([
			'type'      => 'Task Health Alert Flag Auto-Set',
			'link'      => url('/task-health'),
			'module'    => 'Task Health Flags',
			'object_id' => $flag->id,
			'message'   => 'Alert flag set to 1 automatically on critical alert resolution — Task ID: ' . $taskId,
			'ip'        => Utility::getIP(),
		]);
	}

	public function saveFlagsOnlyPOCCron(?string $thPatientId, ?string $taskId, ?int $patientId, int $poc): TaskHealthFlags
	{
		// Visit context: key by task_id. Master context: key by task_health_patient_id.
		if ($taskId) {
			$flag = TaskHealthFlags::firstOrNew(['task_id' => $taskId]);
		} else {
			$flag = TaskHealthFlags::firstOrNew(['task_health_patient_id' => $thPatientId]);
		}

		if (!$flag->exists) {
			$flag->task_id                = $taskId;
			$flag->task_health_patient_id = $thPatientId ?? $flag->task_health_patient_id;
			$flag->patient_id             = $patientId;
			$flag->del_flag               = 'N';
			$flag->created_by             = auth()->id();
		}

		$now = now();
		$uid = auth()->id()??482;

		$oldValues = [
			'poc'        => (int) $flag->poc_check,
			'mdo'        => (int) $flag->mdo_check,
			'alert'      => (int) $flag->alert_check,
			'supervision' => (int) $flag->supervision_check,
		];

		if ((int)$flag->poc_check !== $poc) {
			$flag->poc_check      = $poc;
			$flag->poc_check_by   = $poc ? $uid : null;
			$flag->poc_check_date = $poc ? $now : null;
		}
		

		$flag->updated_by = $uid;
		$flag->save();

		LogsService::save([
			'type'         => 'Task Health Flags Save',
			'link'         => url('/task-health'),
			'module'       => 'Task Health Flags',
			'object_id'    => $flag->id,
			'message'      => 'Flags saved — Task ID: ' . ($taskId ?? '—') . ' | Patient ID: ' . ($thPatientId ?? '—')
			                . ' | POC: ' . $oldValues['poc'] . ' to ' . $poc,
			'old_response' => serialize($oldValues),
			'new_response' => serialize($flag),
			'ip'           => Utility::getIP(),
		]);

		return $flag;
	}

	public function saveFlagsOnlySuperVisionCron(?string $thPatientId, ?string $taskId, ?int $patientId, int $supervision): TaskHealthFlags
	{
		// Visit context: key by task_id. Master context: key by task_health_patient_id.
		if ($taskId) {
			$flag = TaskHealthFlags::firstOrNew(['task_id' => $taskId]);
		} else {
			$flag = TaskHealthFlags::firstOrNew(['task_health_patient_id' => $thPatientId]);
		}

		if (!$flag->exists) {
			$flag->task_id                = $taskId;
			$flag->task_health_patient_id = $thPatientId ?? $flag->task_health_patient_id;
			$flag->patient_id             = $patientId;
			$flag->del_flag               = 'N';
			$flag->created_by             = auth()->id();
		}

		$now = now();
		$uid = auth()->id()??482;

		$oldValues = [
			'poc'        => (int) $flag->poc_check,
			'mdo'        => (int) $flag->mdo_check,
			'alert'      => (int) $flag->alert_check,
			'supervision' => (int) $flag->supervision_check,
		];

		if ((int)$flag->supervision_check !== $supervision) {
			$flag->supervision_check      = $supervision;
			$flag->supervision_check_by   = $supervision ? $uid : null;
			$flag->supervision_check_date = $supervision ? $now : null;
		}
		

		$flag->updated_by = $uid;
		$flag->save();

		LogsService::save([
			'type'         => 'Task Health Flags Save',
			'link'         => url('/task-health'),
			'module'       => 'Task Health Flags',
			'object_id'    => $flag->id,
			'message'      => 'Flags saved — Task ID: ' . ($taskId ?? '—') . ' | Patient ID: ' . ($thPatientId ?? '—')
			                . ' | SuperVision: ' . $oldValues['supervision'] . ' to ' . $supervision,
			'old_response' => serialize($oldValues),
			'new_response' => serialize($flag),
			'ip'           => Utility::getIP(),
		]);

		return $flag;
	}

	/**
	 * Cron-only: set assessment_check and/or kardex_check after extra docs are uploaded.
	 * Uses system user 482; never touches poc/mdo/alert/supervision.
	 */
	public function saveFlagsExtraDocsCron(?string $thPatientId, ?string $taskId, ?int $patientId, int $assessment, int $kardex, int $patientPackageDoc = 0): TaskHealthFlags
	{
		if ($taskId) {
			$flag = TaskHealthFlags::firstOrNew(['task_id' => $taskId]);
		} else {
			$flag = TaskHealthFlags::firstOrNew(['task_health_patient_id' => $thPatientId]);
		}
		$uid = auth()->user()->id??482;
		if (!$flag->exists) {
			$flag->task_id                = $taskId;
			$flag->task_health_patient_id = $thPatientId ?? $flag->task_health_patient_id;
			$flag->patient_id             = $patientId;
			$flag->del_flag               = 'N';
			$flag->created_by             = $uid;
		}

		$now = now();

		$oldAssessment        = (int) $flag->assessment_check;
		$oldKardex            = (int) $flag->kardex_check;
		$oldPatientPackageDoc = (int) $flag->patient_package_doc_check;

		if ($assessment == 1 && $oldAssessment !== $assessment) {
			$flag->assessment_check      = $assessment;
			$flag->assessment_check_by   = $assessment ? $uid : null;
			$flag->assessment_check_date = $assessment ? $now : null;
		}
		if ($kardex == 1 &&  $oldKardex !== $kardex) {
			$flag->kardex_check      = $kardex;
			$flag->kardex_check_by   = $kardex ? $uid : null;
			$flag->kardex_check_date = $kardex ? $now : null;
		}
		if ($patientPackageDoc == 1 && $oldPatientPackageDoc !== $patientPackageDoc) {
			$flag->patient_package_doc_check      = $patientPackageDoc;
			$flag->patient_package_doc_check_by   = $patientPackageDoc ? $uid : null;
			$flag->patient_package_doc_check_date = $patientPackageDoc ? $now : null;
		}

		$flag->updated_by = $uid;
		$flag->save();

		LogsService::save([
			'type'         => 'Task Health Extra Docs Flags Save (Cron)',
			'link'         => url('/task-health'),
			'module'       => 'Task Health Flags',
			'object_id'    => $flag->id,
			'message'      => 'Extra doc flags saved by cron — Task ID: ' . ($taskId ?? '—')
			                . ' | Assessment: ' . $oldAssessment . ' to ' . $assessment
			                . ', Kardex: ' . $oldKardex . ' to ' . $kardex
			                . ', Patient Package Doc: ' . $oldPatientPackageDoc . ' to ' . $patientPackageDoc,
			'old_response' => serialize(['assessment' => $oldAssessment, 'kardex' => $oldKardex, 'patient_package_doc' => $oldPatientPackageDoc]),
			'new_response' => serialize($flag),
			'ip'           => Utility::getIP(),
		]);

		return $flag;
	}

	public function getTaskFlagIds($task_id){
		return TaskHealthFlags::where('del_flag','N')->where('task_id',$task_id)->first();
	}
}
