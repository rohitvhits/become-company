<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Model\EsignImportLog;

use App\Helpers\Common;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\PatientV2Service;
use App\Services\EsignImportLogService;
use App\Services\EsignImportDetailService;
use Illuminate\Support\Facades\Storage;
use App\Services\TemplateService;

class EsignImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public $timeout = 7200;
    public $tries = 1;
    public $maxExceptions = 3;
    protected const FILE_IMPORT_FOLDER="dosusinguploads/esign_import";

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {

        $importLog = $this->getImportLog();
        if (!$importLog) {
            Log::error("EsignImportJob: Import log not found. ID: {$this->data['import_id']}");
            return;
        }

        $filePath = $this->getFilePath($importLog);

        if (!file_exists($filePath) && env('FILE_UPLOAD_PERMISSION') =='development') {
            Log::error("EsignImportJob: File not found. Path: {$filePath}");
            $importLog->update(['status' => 'Failed']);
            return;
        }

        try {
            $importLog->update(['status' => 'Processing']);

            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                $importLog->update(['status' => 'Failed']);
                return;
            }

            $headers = fgetcsv($handle);

            // Detect column indexes
            $portalIdIndex = null;
            $nameIndex = null;
            if ($headers) {
                foreach ($headers as $i => $h) {
                    $col = strtolower(trim($h));
                    if (in_array($col, ['portal_id', 'portalid', 'portal id', 'id'])) {
                        $portalIdIndex = $i;
                    }
                    if (in_array($col, ['name', 'full_name', 'fullname'])) {
                        $nameIndex = $i;
                    }
                }
            }

            $successCount = 0;
            $failedCount = 0;
            $duplicateCount = 0;
            $chunkSize = 1000;
            $chunk = [];

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (count(array_filter($row)) === 0) {
                    continue;
                }

                $portalId = ($portalIdIndex !== null && isset($row[$portalIdIndex])) ? trim($row[$portalIdIndex]) : null;
                $name = ($nameIndex !== null && isset($row[$nameIndex])) ? trim($row[$nameIndex]) : '';

                $chunk[] = [
                    'portal_id' => $portalId,
                    'name' => $name,
                ];

                if (count($chunk) >= $chunkSize) {
                    $result = $this->processChunk($chunk, $importLog);
                    $successCount += $result['success'];
                    $failedCount += $result['failed'];
                    $duplicateCount += $result['duplicate'];
                    $chunk = [];

                    // Update progress periodically
                    $importLog->update([
                        'success_count' => $successCount,
                        'failed_count' => $failedCount,
                        'duplicate_count' => $duplicateCount,
                    ]);
                }
            }

            // Process remaining rows
            if (!empty($chunk)) {
                $result = $this->processChunk($chunk, $importLog);
                $successCount += $result['success'];
                $failedCount += $result['failed'];
                $duplicateCount += $result['duplicate'];
            }

            fclose($handle);

            $totalRecords = $successCount + $failedCount + $duplicateCount;
            $status = ($totalRecords > 0 && $failedCount == $totalRecords) ? 'Failed' : 'Processing';

            $importLog->update([
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'duplicate_count' => $duplicateCount,
                'status' => $status,
            ]);

        } catch (\Exception $e) {
            Log::error("EsignImportJob failed. ID: {$this->data['import_id']}, Error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $importLog->update(['status' => 'Failed']);
            throw $e;
        }
    }

    private function processChunk(array $chunk, EsignImportLog $importLog)
    {
        $patientV2Service = new PatientV2Service();
        $esignImportDetailService = new EsignImportDetailService();

        $success = 0;
        $failed = 0;
        $duplicate = 0;

        // Batch fetch patients from DB
        $portalIds = array_filter(array_column($chunk, 'portal_id'));
        $patients = [];
        if (!empty($portalIds)) {
            $agencyIds = $this->fetchAgencyDetailsByTemplateId($importLog->template_id);
            $patients = $patientV2Service->getListByBulkEsign($portalIds);
        }

        // Check for existing records (duplicates)
        $existingPatientIds = [];
        if (!empty($portalIds)) {
            $existingPatientIds = $esignImportDetailService->getListByImportIDAndPatientId($importLog->id,$portalIds);
        }

        $insertBatch = [];

        foreach ($chunk as $row) {
            try {
                $portalId = $row['portal_id'];
                // Check duplicate
                if (in_array($portalId, $existingPatientIds)) {
                    $duplicate++;
                    continue;
                }

                $patient = $patients[$portalId] ?? null;
                $mobile = $patient ? ($patient['mobile'] ?? null) : null;

                if (!$patient) {
                    $insertBatch[] = [
                        'import_id' => $importLog->id,
                        'patient_id' => $portalId,
                        'mobile' => $mobile,
                        'sms_id' => null,
                        'sms_status' => null,
                        'sms_date' => null,
                        'status' => 'failed',
                        'import_status' => 'Not Found',
                        'del_flag' => 'N',
                        'created_at' => now(),
                        'created_by' => $this->data['created_by'],
                    ];
                    $failed++;
                    continue;
                }

                $patientType = $patient['type'] ?? null;

                if (strtolower($patientType) != 'caregiver') {
                    $insertBatch[] = [
                        'import_id' => $importLog->id,
                        'patient_id' => $portalId,
                        'mobile' => $mobile,
                        'sms_id' => null,
                        'sms_status' => null,
                        'sms_date' => null,
                        'status' => 'failed',
                        'import_status' => 'Type Not Match',
                        'del_flag' => 'N',
                        'created_at' => now(),
                        'created_by' => $this->data['created_by'],
                    ];
                    $failed++;
                    continue;
                }

                $patientAgencyId = $patient['agency_id'] ?? null;
                $hasTemplateAgency = !empty($agencyIds) && !empty($agencyIds[0]);

                if ($hasTemplateAgency && $patientAgencyId && !in_array($patientAgencyId, $agencyIds)) {
                    $insertBatch[] = [
                        'import_id' => $importLog->id,
                        'patient_id' => $portalId,
                        'mobile' => $mobile,
                        'sms_id' => null,
                        'sms_status' => null,
                        'sms_date' => null,
                        'status' => 'failed',
                        'import_status' => 'Agency Not Match',
                        'del_flag' => 'N',
                        'created_at' => now(),
                        'created_by' => $this->data['created_by'],
                    ];
                    $failed++;
                    continue;
                }

                $insertBatch[] = [
                    'import_id' => $importLog->id,
                    'patient_id' => $portalId,
                    'mobile' => $mobile,
                    'sms_id' => null,
                    'sms_status' => null,
                    'sms_date' => null,
                    'status' => null,
                    'import_status' => 'Success',
                    'del_flag' => 'N',
                    'created_at' => now(),
                    'created_by' => $this->data['created_by'],
                ];
                $success++;

            } catch (\Exception $e) {
                Log::error("EsignImportJob row failed. Portal ID: " . ($row['portal_id'] ?? 'N/A') . ", Error: " . $e->getMessage());
                $failed++;
            }
        }

        // Bulk insert
        if (!empty($insertBatch)) {
            foreach (array_chunk($insertBatch, 500) as $batch) {
                $esignImportDetailService->bulkInsertSave($batch);
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'duplicate' => $duplicate,
        ];
    }

    public function failed(\Throwable $exception)
    {
        Log::error("EsignImportJob failed permanently. ID: {$this->data['import_id']}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        $importLog = $this->fetchDetailsById($this->data['import_id']);
        if ($importLog) {
            $importLog->update(['status' => 'Failed']);
        }
    }

    private function getImportLog()
    {
        $importLog = $this->fetchDetailsById($this->data['import_id']);

        if (!$importLog) {
            Log::error("EsignImportJob: Import log not found. ID: {$this->data['import_id']}");
        }

        return $importLog;
    }

    private function getFilePath($importLog){
      if(trim(env('FILE_UPLOAD_PERMISSION')) !='development'){
            $expiry = Carbon::now()->addMinutes(10);
            $path = self::FILE_IMPORT_FOLDER.'/'.$importLog->file_path;
            $filePath = Storage::disk('s3')->temporaryUrl($path, $expiry);
        }else{
            $filePath = public_path(self::FILE_IMPORT_FOLDER.'/'.$importLog->file_path);
        }

        return $filePath;
    }

    private function fetchAgencyDetailsByTemplateId($templateId){
        
        $templateService = new TemplateService();
        $getAgencyIdByTemplate = $templateService->getDetailsById($templateId);
        return explode(',',$getAgencyIdByTemplate->agency_id);
    }

    private function fetchDetailsById($id){
        $esignImportLog = new EsignImportLogService();
        return $esignImportLog->getDetailById($id);
    }
}
