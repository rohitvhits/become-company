<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Utility;
use App\Agency;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientV2Service;
use App\Model\Patient;
use App\Services\LogsService;
use App\Model\MergeAgencyDeletionData;
use Illuminate\Support\Facades\DB;
use App\Helpers\Common;
use App\SiteSetting;
use App\User;
use Exception;

class AgencyMergeController extends BaseController
{

	protected $patientV2Service = "";

	public function __construct(PatientV2Service $patientV2Service)
	{
		$this->patientV2Service = $patientV2Service;
	}

	/**
	 * Patient Agency Merge - Index Page
	 * Display page with filters only (no data loaded initially)
	 */
	public function patientAgencyMergeIndex(Request $request)
	{
		// Get agency list for filter dropdown (only active, non-deleted agencies)
		$data['agencyList'] = Agency::select('id', 'agency_name')->where('delete_flag', 'Y')->orderBy('agency_name', 'asc')->get();

		// Get agency list for modal (all active agencies)
		$data['agencyWithoutSelectedList'] = Agency::select('id', 'agency_name')->where('delete_flag', 'N')
			->orderBy('agency_name', 'asc')
			->get();
		// Get type list for filter
		$data['typeList'] = ['Patient', 'Caregiver'];

		return view('agency_merge.patient_agency_merge', $data);
	}

	/**
	 * Patient Agency Merge - AJAX List
	 * Load patient records via AJAX based on filters
	 */
	public function patientAgencyMergeAjax(Request $request)
	{
		$auth = auth()->user();

		// Get filter parameters
		$data['agency_fk'] = $agency_fk = $request->input('agency_fk', '');
		$data['type'] = $type = $request->input('type', '');
		$page = $request->input('page', 1);

		// Validate that deleted agency filter is selected
		if (empty($agency_fk)) {
			$data['patients'] = collect([]); // Empty collection
			$data['validation_error'] = 'Please select a deleted agency from the filter to view patient records.';
			return view('patient.patient_agency_merge_ajax_list', $data);
		}

		// Get filtered patient data
		$mergeData = MergeAgencyDeletionData::get()->pluck('patient_id');
		$data['patients'] = $this->patientV2Service->getPatientListForAgencyMerge($agency_fk,$type,$page,$mergeData);
		return view('agency_merge.patient_agency_merge_ajax_list', $data);
	}

	/**
	 * Patient Agency Merge - AJAX Update
	 * Insert merge requests into merge_agency_deletion_data table
	 */
	public function patientAgencyMergeUpdate(Request $request)
	{
		try {
			$auth = auth()->user();
			// Validate request
			$validator = Validator::make($request->all(), [
				'patient_ids' => 'required|array|min:1',
				'new_agency_id' => 'required|integer|exists:agency,id',
				'filter_agency_id' => 'nullable|integer',
			]);

			if ($validator->fails()) {
				return response()->json([
					'status' => 'error',
					'message' => 'Validation failed',
					'errors' => $validator->errors()
				], 422);
			}

			$patientIds = $request->input('patient_ids');
			$newAgencyId = $request->input('new_agency_id');
			$filterAgencyId = $request->input('filter_agency_id', null);

			// Check if new agency exists and is active
			$agency = Agency::where('id', $newAgencyId)->where('delete_flag', 'N')->first();

			if (!$agency) {
				return response()->json([
					'status' => 'error',
					'message' => 'Selected agency is not valid or inactive.'
				], 400);
			}

			// Get patient records to fetch their current agency_id
			$patients = Patient::whereIn('id', $patientIds)->where('deleted_flag', 'N')->get(['id', 'agency_id']);
			if ($patients->isEmpty()) {
				return response()->json([
					'status' => 'error',
					'message' => 'No valid patient records found.'
				], 400);
			}

			// Start transaction
			DB::beginTransaction();

			$insertedCount = 0;
			$currentTime = now();

			// Insert records into merge_agency_deletion_data table
			foreach ($patients as $patient) {
				MergeAgencyDeletionData::create([
					'patient_id' => $patient->id,
					'old_agency_id' => $patient->agency_id,
					'new_agency_id' => $newAgencyId,
					'status' => 'pending',
					'created_by' => $auth['id'],
					'created_at' => $currentTime,
					'ip' => Utility::getIP()
				]);
				$insertedCount++;
			}

			// Commit transaction
			DB::commit();

			// Log the merge request
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Merge Agency Request',
				'link' => url('/patient-agency-merge/index'),
				'module' => 'Patient Appointment',
				'object_id' => 0,
				'message' => 'Created merge request for ' . $insertedCount . ' patient records to agency: ' . $agency->agency_name,
				'new_response' => serialize([
					'patient_ids' => $patientIds,
					'filter_agency_id' => $filterAgencyId,
					'new_agency_id' => $newAgencyId,
					'agency_name' => $agency->agency_name,
					'inserted_count' => $insertedCount
				]),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			return response()->json([
				'status' => 'success',
				'message' => 'Merge request created. The merge process may take a few minutes to complete.',
				'inserted_count' => $insertedCount
			]);

		} catch (\Exception $e) {
			// Rollback transaction on error
			DB::rollBack();

			return response()->json([
				'status' => 'error',
				'message' => 'An error occurred while creating merge request: ' . $e->getMessage()
			], 500);
		}
	}

	/**
	 * Sync Agency Merge Data
	 * Processes pending merge requests (same logic as ProcessAgencyMergeData command)
	 */
	public function syncAgencyMergeData(Request $request)
	{
		try {
			$limit = 1000;

			// Get pending records ordered by created_at (FIFO)
			$pendingRecords = MergeAgencyDeletionData::where('status', 'pending')
				->orderBy('created_at', 'asc')
				->limit($limit)
				->get();

			if ($pendingRecords->isEmpty()) {
				return response()->json([
					'status' => 'info',
					'message' => 'No pending records to process.',
					'processed' => 0,
					'failed' => 0
				]);
			}

			$processedCount = 0;
			$failedCount = 0;
			$errors = [];

			foreach ($pendingRecords as $record) {
				try {
					$this->processRecord($record);
					$processedCount++;
				} catch (Exception $e) {
					$failedCount++;
					$errors[] = "Patient ID {$record->patient_id}: " . $e->getMessage();
				}
			}

			return response()->json([
				'status' => 'success',
				'message' => "Successfully processed {$processedCount} record(s)." . ($failedCount > 0 ? " {$failedCount} record(s) failed." : ""),
				'processed' => $processedCount,
				'failed' => $failedCount,
				'errors' => $errors
			]);

		} catch (\Exception $e) {
			return response()->json([
				'status' => 'error',
				'message' => 'An error occurred while syncing merge data: ' . $e->getMessage()
			], 500);
		}
	}

	/**
	 * Process a single merge record
	 * (Same logic as ProcessAgencyMergeData command)
	 */
	protected function processRecord(MergeAgencyDeletionData $record)
	{
		// Start transaction
		DB::beginTransaction();

		try {
			// Mark as processing
			$record->markAsProcessing();

			// Get the patient record
			$patient = Patient::where('id', $record->patient_id)
				->where('deleted_flag', 'N')
				->first();

			if (!$patient) {
				throw new Exception("Patient not found or has been deleted");
			}

			// Verify the old agency ID matches
			if ($patient->agency_id != $record->old_agency_id) {
				throw new Exception("Patient's current agency ID ({$patient->agency_id}) does not match the recorded old agency ID ({$record->old_agency_id})");
			}

			// Update patient's agency_id
			$patient->agency_id = $record->new_agency_id;
			$patient->patient_agency_merge_id = $record->id;
			$patient->updated_date = now();

			// Set updated_by if creator exists
			$username = "";
			if ($record->created_by) {
				$user = User::find($record->created_by);
				$patient->updated_by = $record->created_by;
				$username = $user->first_name.' '.$user->last_name;
			}
			$patient->save();

			// Mark record as completed
			$record->markAsCompleted();

			// Log the successful merge
			$ipaddress = $record->ip ?? '';
			$insertLog = [
				'type' => 'Merge Agency Processed',
				'link' => url('/patient-agency-merge/'),
				'module' => 'Patient Appointment',
				'object_id' => $patient->id,
				'message' => $username. " has successfully merged patient ID {$patient->id} from deleted agency {$record->old_agency_id} to active agency {$record->new_agency_id}",
				'new_response' => serialize([
					'merge_record_id' => $record->id,
					'patient_id' => $patient->id,
					'old_agency_id' => $record->old_agency_id,
					'new_agency_id' => $record->new_agency_id,
					'processed_at' => $record->processed_at,
					'record' => $record->created_by
				]),
				'old_response' => serialize($patient),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			try {
				// Send notification to Liaison
				$siteSetting = SiteSetting::select('is_send_agency_merge_liaison_notify')->first();
				$isSendLiaison = $siteSetting['is_send_agency_merge_liaison_notify'];
				if(isset($isSendLiaison) && $isSendLiaison == 1){
					$this->sendLiaison($record, $patient);
				}
			} catch (\Throwable $th) {}

			// Commit transaction
			DB::commit();

		} catch (Exception $e) {
			// Rollback transaction
			DB::rollBack();

			// Mark record as failed
			$record->markAsFailed($e->getMessage());

			// Re-throw exception for command to catch
			throw $e;
		}
	}

	/**
	 * Send notification to Liaison
	 */
	protected function sendLiaison($record, $patient)
	{
		$agencyNotifyData = array(
			'agencyid' => $record->new_agency_id,
			'title' => "Portal transferred from Agency {$record->old_agency_id} to active Agency {$record->new_agency_id}",
			'record_id' => $patient->id,
			'record_type' => 'Appointment',
			'msg' => '',
			'res_data' => serialize([
				'merge_record_id' => $record->id,
				'patient_id' => $patient->id,
				'old_agency_id' => $record->old_agency_id,
				'new_agency_id' => $record->new_agency_id,
				'processed_at' => $record->processed_at,
				'record' => $record->created_by
			]),
		);
		Common::insertAgencyNotificationsOfUser($agencyNotifyData);
	}
}
