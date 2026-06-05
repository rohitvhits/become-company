<?php

namespace App\Http\Controllers;

use App\Model\MergeAgencyDeletionData;
use App\Model\Patient;
use App\Services\LogsService;
use App\Helpers\Utility;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ProcessAgencyMergeController extends Controller
{
    /**
     * Process pending agency merge data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(Request $request)
    {
        $limit = $request->get('limit', 1000);

        $response = [
            'limit' => $limit,
            'processed' => 0,
            'failed' => 0,
            'details' => [],
        ];

        // Get pending records (FIFO)
        $pendingRecords = MergeAgencyDeletionData::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        if ($pendingRecords->isEmpty()) {
            return response()->json([
                'message' => 'No pending records to process.',
                'data' => $response
            ]);
        }

        foreach ($pendingRecords as $record) {
            try {
                $this->processRecord($record);
                $response['processed']++;
                $response['details'][] = [
                    'patient_id' => $record->patient_id,
                    'status' => 'success',
                    'message' => "Processed successfully"
                ];
            } catch (Exception $e) {
                $response['failed']++;
                $response['details'][] = [
                    'patient_id' => $record->patient_id,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Processing completed.',
            'data' => $response
        ]);
    }

    /**
     * Handle single record processing
     *
     * @param MergeAgencyDeletionData $record
     * @throws Exception
     */
    protected function processRecord(MergeAgencyDeletionData $record)
    {
        DB::beginTransaction();

        try {
            $record->markAsProcessing();

            $patient = Patient::where('id', $record->patient_id)
                ->where('deleted_flag', 'N')
                ->first();

            if (!$patient) {
                throw new Exception("Patient not found or has been deleted");
            }

            if ($patient->agency_id != $record->old_agency_id) {
                throw new Exception("Patient's current agency ID ({$patient->agency_id}) does not match old agency ID ({$record->old_agency_id})");
            }

            $patient->agency_id = $record->new_agency_id;
            $patient->patient_agency_merge_id = $record->id;
            $patient->updated_date = now();

            $username = 'System';
            if ($record->created_by) {
                $patient->updated_by = $record->created_by;
                $user = User::find($record->created_by);
                $username = $user ? ($user->first_name . ' ' . $user->last_name) : 'Unknown User';
            }

            $patient->save();

            $record->markAsCompleted();

            $ip = $record->ip??'';
            $logData = [
                'type' => 'Merge Agency Processed',
                'link' => url('/patient/view/' . $patient->id),
                'module' => 'Patient Appointment',
                'object_id' => $patient->id,
                'message' => "{$username} merged patient ID {$patient->id} from agency {$record->old_agency_id} to {$record->new_agency_id}",
                'new_response' => serialize([
                    'merge_record_id' => $record->id,
                    'patient_id' => $patient->id,
                    'old_agency_id' => $record->old_agency_id,
                    'new_agency_id' => $record->new_agency_id,
                    'processed_at' => $record->processed_at,
                    'created_by' => $record->created_by
                ]),
                'old_response' => serialize($patient),
                'ip' => $ip,
            ];
            LogsService::save($logData);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            $record->markAsFailed($e->getMessage());
            throw $e;
        }
    }
}
