<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use App\Services\AppointmentPortalMergeLogsService;
use App\Services\LogsService;
use App\Services\PatientV2Service;
use App\Model\Patient;
use Illuminate\Support\Facades\DB;

class MergeAppointmentController extends BaseController
{
	protected $appointmentMergeLogsService;
    protected $patientService;
    protected $logService;
    protected $patientV2Service;
    protected const CONVERT_DATE_YMDHIS = 'Y-m-d H:i:s';
    public function __construct(
        AppointmentPortalMergeLogsService $appointmentMergeLogsService,
        PatientService $patientService,
        LogsService $logService,
        PatientV2Service $patientV2Service
    ) {
        $this->patientService = $patientService;
        $this->appointmentMergeLogsService = $appointmentMergeLogsService;
        $this->logService = $logService;
        $this->patientV2Service = $patientV2Service;
    }

	public function mergeAppointment(Request $request)
    {
        if ($response = $this->validateMergeRequest($request)) {
            return $response;
        }

        $childId = $request->appointment_id; // The appointment to be merged (will be marked deleted)
        $parentId = $request->record_id; // The appointment to merge into (stays active)

        // Check for circular merge FIRST - critical for nested merges
        if ($this->appointmentMergeLogsService->wouldCreateCircularMerge($childId, $parentId)) {
            return response()->json([
                'error_msg' => "Cannot merge: This would create a circular reference. An appointment cannot be merged into its own descendant or ancestor.",
                'data'      => []
            ], 500);
        }

        $checkAppointmentIdMergeOrnot = $this->appointmentMergeLogsService
            ->checkAnyExistingMergeAppointmentId(
                $parentId,
                $childId
            );

        $error_msg = "";

        if (isset($checkAppointmentIdMergeOrnot->id)) {
            $error_msg = "Chart ID is already merged with the same appointment ID";
        } else {

            $checkForMergeAppointmentDeleted = $this->checkMergeAppointmentIdDeleteOrNot($request->all());

            if(isset($checkForMergeAppointmentDeleted->id)){
                $error_msg = "This portal has already been deleted and cannot be merged.";
            }else{
                $getDetailsForAppointment = $this->getDeletedRecordAndActiveRecordDetails(
                    $request->all()
                );

                if ($childId == $parentId) {
                    $error_msg = "Cannot merge a record with itself";
                } else {
                    $details = $this->allCheckConditionWithProcess($getDetailsForAppointment);

                    if ($details['status'] == 1) {
                        return $this->processMerge(
                            $request,
                            $getDetailsForAppointment['activeRecordDetails'],
                            $getDetailsForAppointment['deletedRecordDetails']
                        );
                    } else {
                        $error_msg = $details['error_msg'];
                    }
                }
            }
        }

        return response()->json([
            'error_msg' => $error_msg,
            'data'      => []
        ], 500);
    }
	
    public function unMergeAppointment(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'status' => 'required',
		]);

		if ($validator->fails()) {
			return $this->validationErrorResponse($validator);
		}

        $getDetailsById = $this->appointmentMergeLogsService->getDetailsById($request->id);

		if (!$getDetailsById) {
			return $this->errorResponse("Merge record not found or already unmerged");
		}

		$appointment = $this->patientService->getPatientDetailsByIdWhitoutAgency($getDetailsById->merge_patient_id);

		if (!$appointment?->id) {
			return $this->errorResponse("Chart ID does not exist");
		}

		$record = $this->patientService->getPatientDetailsByIdWhitoutAgency($getDetailsById->main_patient_id);

		if (!$record?->id) {
			return $this->errorResponse("Parent record does not exist");
		}

		if ($appointment->agency_id !== $record->agency_id) {
			return $this->errorResponse("Chart ID is incorrect because both agencies must be different");
		}

		if (strtolower($appointment->type) !== strtolower($record->type)) {
			return $this->errorResponse("Chart type must be same");
		}

		$response = $this->performUnmerge($getDetailsById, $appointment, $record);

		return response()->json([
			'error_msg' => "Record successfully unmerged",
			'data' => $response
		], 200);
	}

	private function validationErrorResponse($validator)
	{
		return response()->json([
			'error_msg' => $validator->errors()->all()[0],
			'data' => []
		], 422);
	}

	private function errorResponse(string $message)
	{
		return response()->json([
			'error_msg' => $message,
			'data' => []
		], 500);
	}

	private function performUnmerge($mergeLogData,  $appointment, $record)
	{
		$user = auth()->user();
		$oldResponse = $appointment->toArray();
		$oldRecordData = $record ? $record->toArray() : [];

		$childId = $mergeLogData->merge_patient_id;
		$parentId = $mergeLogData->main_patient_id;

		// Soft delete the merge log
        $this->appointmentMergeLogsService->softDelete(array('del_flag'=>'Y'),array('id'=>$mergeLogData->id));

		// Check if child has other active merge logs (shouldn't be deleted if still merged elsewhere)
		$remainingMergeLogs = $this->appointmentMergeLogsService->getMergePatientDetailsById($childId);

		// Reactive the child appointment if no other active merges exist
		if(count($remainingMergeLogs) == 0){
            $this->patientService->update(['deleted_flag' => 'N'], ['id' => $childId]);
        }

		// Update metadata for any children of the unmerged appointment
		// This ensures nested merge chains remain consistent
		$this->appointmentMergeLogsService->updateMetadataAfterUnmerge($childId);

		// Update merge_appointment_id field in patient_master for all affected records
		$this->appointmentMergeLogsService->updateMergeAppointmentIdAfterUnmerge($childId, $parentId);

		// Get updated records for logging
		// Use getPatientDetailsByIdWhitoutAgency because the parent may itself be a
		// deleted (merged) record which getDetailById would exclude
		$updatedChild = $this->patientService->getPatientDetailsByIdWhitoutAgency($childId);
		$updatedParent = $this->patientService->getPatientDetailsByIdWhitoutAgency($parentId);

        $message = $user->first_name . ' ' . $user->last_name . ' has unmerged Record';
        $this->logAction($parentId, $oldRecordData, $updatedParent ? $updatedParent->toArray() : $oldRecordData, $message, 0, 'Unmerge Appointment');
        $this->logAction($childId, $oldResponse, $updatedChild ? $updatedChild->toArray() : $oldResponse, $message, 0, 'Unmerge Appointment');

        return $updatedParent ? $updatedParent->toArray() : $oldRecordData;
	}

    private function checkAgencySameOrNot($mergeRecords,$getDetailsForAppointment): bool{
        
        return $mergeRecords->agency_id === $getDetailsForAppointment->agency_id;
    }

    private function checkMergeRecordType($mergeRecords,$getDetailsForAppointment): bool{
        
        return strtolower($getDetailsForAppointment->type) === strtolower($mergeRecords->type);
    }

    private function mergeServices($mergeRecords,$getDetailsForAppointment){
        $explodeAppointmentService = explode(',', $getDetailsForAppointment->service_id);

        $explodeMergeServices = explode(',', $mergeRecords->service_id);

        return array_unique(array_merge($explodeMergeServices, $explodeAppointmentService));
    }

    private function logAction($objectId,$oldResponse,$newResponse,$message,$flag,$type){

        $link = 'patient/un-combine-appointment';
        if($flag ==1){
            $link = 'patient/combine-appointment';
        }

        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => $type,
            'link' => url('/'.$link),
            'module' => 'Patient Appointment',
            'object_id' => $objectId,
            'message' => $message,
            'old_response' => serialize($oldResponse),
            'new_response' => serialize($newResponse),
            'ip' => $ipaddress,
        ];
        $this->logService->save($insertLog);

    }

    private function processMerge($request,$mergeRecords,$getDetailsForAppointment){
        $user = auth()->user();

        $oldResponse = $getDetailsForAppointment->toArray();
        $oldParentResponse = $mergeRecords->toArray();

        $mergeServices = $this->mergeServices($mergeRecords,$getDetailsForAppointment);

        $childId = $request->appointment_id;
        $parentId = $request->record_id;

        // Get updated records for logging 
        $updatedChild = $this->patientService->getPatientDetailsByIdWhitoutAgency($childId);
        $updatedParent = $this->patientService->getDetailById($parentId);
        
        // Mark child as deleted
        $this->patientService->update([
            'deleted_flag' => 'Y'
        ], ['id' => $childId]);

        // Update parent with merged services
        $this->patientService->update([
            'service_id' => implode(',', $mergeServices)
        ], ['id' => $parentId]);

        // Save merge log with metadata support for nested merges
        $mergeLogId = $this->appointmentMergeLogsService->saveWithMetadata([
            'main_patient_id' => $parentId,
            'merge_patient_id' => $childId
        ]);

        // Update merge chain metadata for all affected records
        $this->appointmentMergeLogsService->updateMergeChainMetadata($childId, $parentId);

        // Update merge_appointment_id field in patient_master for nested chain handling
        $this->appointmentMergeLogsService->updateMergeAppointmentIdAfterMerge($childId, $parentId);

        $message = $user->first_name . ' ' . $user->last_name . ' has merge Record';
        $this->logAction($parentId, $oldParentResponse, $updatedParent->toArray(), $message, 1, 'Merge Appointment');
        $this->logAction($childId, $oldResponse, $updatedChild->toArray(), $message, 1, 'Merge Appointment');

        return response()->json([
            'error_msg' => "Record successfully merged",
            'data' => [
                'service_name' => $mergeServices,
                'merge_log_id' => $mergeLogId,
                'root_appointment_id' => $this->appointmentMergeLogsService->getRootAppointment($parentId),
                'merge_appointment_id' => $updatedParent->merge_appointment_id
            ]
        ], 200);
    }

    private function validateMergeRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'record_id'      => 'required',
            'appointment_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->first(),
                'data'      => [],
            ], 422);
        }

        return null; // validation passed
    }

    private function getDeletedRecordAndActiveRecordDetails($data){
        $getDetailsForAppointment = $this->patientService->getPatientDetailsByIdWhitoutAgency($data['appointment_id']);
        $mergeRecords = $this->patientService->getDetailById($data['record_id']);

        return ['deletedRecordDetails'=>$getDetailsForAppointment,'activeRecordDetails'=>$mergeRecords];
    }

    private function allCheckConditionWithProcess($getDetailsForAppointment){
        $status = 0;
        $error_msg = '';
        if (isset($getDetailsForAppointment['deletedRecordDetails']->id)) {
            $checkForAgency = $this->checkAgencySameOrNot($getDetailsForAppointment['activeRecordDetails'],$getDetailsForAppointment['deletedRecordDetails']);
            if($checkForAgency){
                $checkMergeRecordType = $this->checkMergeRecordType($getDetailsForAppointment['activeRecordDetails'],$getDetailsForAppointment['deletedRecordDetails']);
                if($checkMergeRecordType){
                    $status = 1;
                    
                }else{
                    $error_msg = "Chart type must be same";
                }
            }else{
                $error_msg = "Please select records from the same agency to merge";
            }
            
        } else {
            $error_msg = "Chart ID does not exists";
        }

        return ['status'=>$status,'error_msg'=>$error_msg];
    }

    public function mergeAppointmentList(Request $request){
        $data['query'] = $this->appointmentMergeLogsService->getActiveRecordList($request->id);
        $data['flag'] = "active";
        return view('mergeAppointment.ajaxList',$data);
    }

    public function mergeDeletedAppointmentList(Request $request){
        $data['query'] = $this->appointmentMergeLogsService->getDeletedRecordList($request->id);
        $data['flag'] = "delete";
        $data['id'] = $request->id;
        return view('mergeAppointment.ajaxList',$data);
    }

    private function checkMergeAppointmentIdDeleteOrNot($data){
        return $this->patientV2Service->checkMergeAppointmentIdDelete($data['appointment_id']);
    }

    /**
     * Get the root (ultimate parent) appointment for a given appointment
     * Useful for finding the final active record in a merge chain
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRootAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $rootId = $this->appointmentMergeLogsService->getRootAppointment($request->appointment_id);
        $rootAppointment = $this->patientService->getDetailById($rootId);

        if (!$rootAppointment) {
            return response()->json([
                'error_msg' => 'Root appointment not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'error_msg' => '',
            'data' => [
                'root_appointment_id' => $rootId,
                'root_appointment' => $rootAppointment,
                'is_merged' => $rootId !== $request->appointment_id
            ]
        ], 200);
    }

    /**
     * Get the full merge chain for an appointment
     * Returns the complete path from root to current appointment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMergeChain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $chain = $this->appointmentMergeLogsService->getMergeChain($request->appointment_id);
        $chainDetails = [];

        foreach ($chain as $appointmentId) {
            $appointment = $this->patientService->getDetailById($appointmentId);
            if ($appointment) {
                $chainDetails[] = [
                    'id' => $appointmentId,
                    'patient_name' => $appointment->patient_name ?? 'N/A',
                    'type' => $appointment->type ?? 'N/A',
                    'agency_id' => $appointment->agency_id ?? null
                ];
            }
        }

        return response()->json([
            'error_msg' => '',
            'data' => [
                'chain_ids' => $chain,
                'chain_details' => $chainDetails,
                'depth' => count($chain) - 1
            ]
        ], 200);
    }

    /**
     * Get all children (merged appointments) for a given appointment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChildAppointments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer',
            'direct_only' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $directOnly = $request->input('direct_only', false);
        $children = $this->appointmentMergeLogsService->getAllChildren($request->appointment_id, $directOnly);

        $childrenDetails = [];
        foreach ($children as $child) {
            $appointment = $this->patientService->getDetailById($child->merge_patient_id);
            if ($appointment) {
                $childrenDetails[] = [
                    'merge_log_id' => $child->id,
                    'appointment_id' => $child->merge_patient_id,
                    'patient_name' => $appointment->patient_name ?? 'N/A',
                    'merge_depth' => $child->merge_depth ?? 0,
                    'merged_date' => $child->created_date ?? null,
                    'merged_by' => $child->created_by ?? null
                ];
            }
        }

        return response()->json([
            'error_msg' => '',
            'data' => [
                'total_children' => count($childrenDetails),
                'children' => $childrenDetails,
                'type' => $directOnly ? 'direct' : 'all_descendants'
            ]
        ], 200);
    }

    /**
     * Check if a merge would create a circular reference
     * Useful for validation before attempting a merge
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCircularMerge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|integer',
            'parent_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $wouldBeCircular = $this->appointmentMergeLogsService->wouldCreateCircularMerge(
            $request->child_id,
            $request->parent_id
        );

        return response()->json([
            'error_msg' => '',
            'data' => [
                'would_create_circular' => $wouldBeCircular,
                'can_merge' => !$wouldBeCircular,
                'message' => $wouldBeCircular
                    ? 'This merge would create a circular reference and is not allowed'
                    : 'This merge is safe to proceed'
            ]
        ], 200);
    }

    /**
     * Sync/rebuild merge_appointment_id field for a specific appointment or all appointments
     * Useful for data migration or fixing inconsistencies
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncMergeAppointmentId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'integer|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        if ($request->has('appointment_id') && $request->appointment_id) {
            // Sync specific appointment
            $this->appointmentMergeLogsService->syncMergeAppointmentIdField($request->appointment_id);

            return response()->json([
                'error_msg' => '',
                'data' => [
                    'message' => 'Merge appointment ID synced successfully for appointment ' . $request->appointment_id,
                    'appointment_id' => $request->appointment_id
                ]
            ], 200);
        } else {
            // Sync all appointments that have active merge logs
            $processedIds = [];
            $count = 0;

            // Get all unique parent appointment IDs
            $parentIds = $this->appointmentMergeLogsService->getUniqueParentPatientIds();

            foreach ($parentIds as $parentId) {
                if (!in_array($parentId, $processedIds)) {
                    $this->appointmentMergeLogsService->syncMergeAppointmentIdField($parentId);
                    $processedIds[] = $parentId;
                    $count++;
                }
            }

            return response()->json([
                'error_msg' => '',
                'data' => [
                    'message' => 'Merge appointment IDs synced successfully for all appointments',
                    'total_synced' => $count
                ]
            ], 200);
        }
    }

    public function syncMergeAppointments()
    {
        Patient::whereNotNull('merge_appointment_id')
            ->where('deleted_flag', 'N')
            ->select('id', 'merge_appointment_id')
            ->chunk(1000, function ($patients) {
                $insertData = [];
                foreach ($patients as $patient) {
                    // Check if already exists
                    $exists = DB::table('appointment_portal_merge_logs')
                        ->where('main_patient_id', $patient->id)
                        ->where('merge_patient_id', $patient->merge_appointment_id)
                        ->exists();

                    if (!$exists) {
                        $insertData[] = [
                            'main_patient_id'   => $patient->id,
                            'merge_patient_id'  => $patient->merge_appointment_id,
                            'root_patient_id'   => $patient->id,
                            'parent_patient_id' => $patient->id,
                            'merge_depth'       => 1,
                            'merge_path'        => $patient->id . ',' . $patient->id,
                            'created_date'        => now(),
                            'created_by'        => 482,
                        ];
                    }
                }
                if (!empty($insertData)) {
                    DB::table('appointment_portal_merge_logs')->insert($insertData);
                }
            });

        return response()->json([
            'status' => true,
            'message' => 'Sync completed successfully'
        ]);
    }
}