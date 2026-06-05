<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\MergeAgencyDeletionData;
use App\Model\Patient;
use App\Services\LogsService;
use App\Helpers\Common;
use App\SiteSetting;
use App\User;
use Illuminate\Support\Facades\DB;
use Exception;

class ProcessAgencyMergeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process-agency-data {--limit=10 : Number of records to process per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending agency merge requests from merge_agency_deletion_data table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = 1000;//$this->option('limit');

        info("Starting to process agency merge data...");
        info("Limit: {$limit} records per run");

        // Get pending records ordered by created_at (FIFO)
        $pendingRecords = MergeAgencyDeletionData::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        if ($pendingRecords->isEmpty()) {
            info("No pending records to process.");
            return 0;
        }

        info("Found {$pendingRecords->count()} pending record(s) to process.");

        $processedCount = 0;
        $failedCount = 0;

        foreach ($pendingRecords as $record) {
            try {
                $this->processRecord($record);
                $processedCount++;
                info("Processed patient ID: {$record->patient_id}");
            } catch (Exception $e) {
                $failedCount++;
                $this->error("Failed to process patient ID: {$record->patient_id} - Error: " . $e->getMessage());
            }
        }

        info("\n=== Summary ===");
        info("Processed: {$processedCount}");
        info("Failed: {$failedCount}");
        info("===============\n");

        return 0;
    }

    /**
     * Process a single merge record
     *
     * @param MergeAgencyDeletionData $record
     * @return void
     * @throws Exception
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
            $ipaddress = $record->ip??'';
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
                    $this->sendLiaison($record,$patient);
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

    protected function sendLiaison($record,$patient){
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
