<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AppointmentImportFileService;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Utility;
use App\Model\InsuranceMaster;
use DB;
use App\Services\ImportCsvFileRecordService;
use Illuminate\Support\Facades\Log;
use App\Exceptions\MDOAuthenticationException;
use App\Master;

class ProcessPatientImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data = "";
    protected const FILE_IMPORT_FOLDER = "patient_appointment_import";

    // Job configuration for large imports
    public $timeout = 7200; // 2 hours timeout for very large files
    public $tries = 1; // Don't retry failed imports (avoid duplicate processing)
    public $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {

        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            $appointmentService = new AppointmentImportFileService();
            $tempFilePath = null;
            $csv_file = $appointmentService->getDetailById($this->data['import_id']);
    
            if (!$csv_file) {
                throw new MDOAuthenticationException("Import file not found with ID: ". $this->data['import_id'], 0, null);
            }
    
            // Update status to Processing
            $appointmentService->update([
                'status' => 'Waiting For Approval',
                'processed_records' => 0,
                'failed_records' => 0,
                'updated_by'=>$this->data['created_by']
            ], ['id' => $this->data['import_id']]);
    
            // Get file path (handles both local and S3)
            $path = $this->getImportPath($csv_file);
            $tempFilePath = $this->isS3() ? $path : null; // Track temp file for cleanup
    
            // Process file in streaming mode (memory efficient)
           $this->processFileInChunks($path, $csv_file);
            

        } catch (\Exception $e) {
            Log::error("Import failed. ID: {$this->data['import_id']}, Error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Update status to Failed
            $appointmentService->update([
                'status' => 'Failed',
                'error_message' => substr($e->getMessage(), 0, 500),
                'updated_by'=>$this->data['created_by']
            ], ['id' => $this->data['import_id']]);

            throw $e; // Re-throw to mark job as failed
        } finally {
            // Cleanup temporary S3 file
            if ($tempFilePath && file_exists($tempFilePath)) {
                @unlink($tempFilePath);
            }
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Import job failed permanently. ID: {$this->data['import_id']}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        $appointmentService = new AppointmentImportFileService();
        $appointmentService->update([
            'status' => 'Failed',
            'error_message' => substr($exception->getMessage(), 0, 500),
            'updated_by'=>$this->data['created_by']
        ], ['id' => $this->data['import_id']]);
    }

    /**
     * Process file in chunks to avoid memory exhaustion
     * Uses streaming approach - reads one row at a time
     */
    private function processFileInChunks($path, $csv_file)
    {
        $totalSuccess = 0;
        $totalFailed = 0;

        if ($csv_file->extension == 'csv') {
            $this->processCsvInChunks($path, $csv_file,$totalSuccess, $totalFailed);
        } else {
            $this->processExcelInChunks($path, $csv_file, $totalSuccess, $totalFailed);
        }

        return [
            'success' => $totalSuccess,
            'failed' => $totalFailed
        ];
    }

    /**
     * Process CSV file in chunks using streaming
     */
    private function processCsvInChunks($path, $csv_file, &$totalSuccess, &$totalFailed)
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new MDOAuthenticationException("Unable to open file: ".$path, 0, null);
        }

        try {
            $batch = [];
            $chunkSize = 1000;
            $isFirstRow = true;

            while (($row = fgetcsv($handle)) !== false) {
                if ($this->shouldSkipRow($row, $isFirstRow)) {
                    $isFirstRow = false;
                    continue;
                }

                $isFirstRow = false;
                $batch[] = $row;

                if (count($batch) >= $chunkSize) {
                    $this->processBatchAndUpdateProgress($batch, $csv_file, $totalSuccess, $totalFailed);
                    $batch = [];
                }
            }

            // Process remaining records
            if (!empty($batch)) {
                $this->processBatchAndUpdateProgress($batch, $csv_file, $totalSuccess, $totalFailed);
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * Check if a row should be skipped
     */
    private function shouldSkipRow($row, $isFirstRow)
    {
        // Skip empty rows
        if (count(array_filter($row)) === 0) {
            return true;
        }

        // Skip header row
        return $isFirstRow;
    }

    /**
     * Process batch and update progress tracking
     */
    private function processBatchAndUpdateProgress($batch, $csv_file, &$totalSuccess, &$totalFailed)
    {
        $result = $this->processBatch($batch, $csv_file);
        $totalSuccess += (int)$result['success'];
        $totalFailed += (int)$result['failed'];
    }

    /**
     * Process Excel file in chunks
     */
    private function processExcelInChunks($path, $csv_file, &$totalSuccess, &$totalFailed)
    {
        // Use Laravel Excel's chunk method to avoid memory issues
        try {
            \Maatwebsite\Excel\Facades\Excel::filter('chunk')->load($path)->chunk(500, function($results) use ($csv_file,&$totalSuccess, &$totalFailed) {
                $batch = [];

                foreach ($results as $row) {
                    $rowArray = $row->toArray();

                    // Skip empty rows
                    if (count(array_filter($rowArray)) === 0) {
                        continue;
                    }

                    $batch[] = $rowArray;
                }

                if (!empty($batch)) {
                    $result = $this->processBatch($batch, $csv_file);
                    $totalSuccess += (int)$result['success'];
                    $totalFailed += (int)$result['failed'];
                }
            });
        } catch (\Exception $e) {
            // Fallback to simple read if chunk doesn't work
            Log::warning("Excel chunk reading failed, using fallback method: " . $e->getMessage());
            $this->processExcelFallback($path, $csv_file, $totalSuccess, $totalFailed);
        }
    }

    /**
     * Fallback method for Excel processing
     */
    private function processExcelFallback($path, $csv_file, &$totalSuccess, &$totalFailed)
    {
        $news = \Maatwebsite\Excel\Facades\Excel::toArray([], $path);
        $dataa = $news[0] ?? [];

        $batch = [];
        $chunkSize = 500;
        $isFirstRow = true;

        foreach ($dataa as $row) {
            // Skip empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }

            // Skip header
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $batch[] = $row;

            if (count($batch) >= $chunkSize) {
                $result = $this->processBatch($batch, $csv_file);
                $totalSuccess += (int)$result['success'];
                $totalFailed += (int)$result['failed'];

                $batch = [];
            }
        }

        // Process remaining
        if (!empty($batch)) {
            $result = $this->processBatch($batch, $csv_file);
            $totalSuccess += (int)$result['success'];
            $totalFailed += (int)$result['failed'];
        }
    }

    /**
     * Process a batch of rows
     */
    private function processBatch($batch, $csv_file)
    {
        $successCount = 0;
        $failedCount = 0;

        try {
            // Map and normalize data (same as original logic)
            $mapped = $this->mapRowsToOrderedData($batch, $this->data['order_data']);
            $normalized = $this->normalizeRows($mapped);

            if (empty($normalized)) {
                return ['success' => 0, 'failed' => count($batch)];
            }

            // Prepare records for bulk insert
            $allFinalArrays = [];
            $cnt = 1;

            foreach ($normalized as $key => $val) {
                try {
                    $finalArray = $this->processSingleRowForBulk($val, $key, $csv_file, $cnt, $this->data['import_id']);
                    if (!empty($finalArray)) {
                        $allFinalArrays[] = $finalArray;
                    }
                    $cnt++;
                } catch (\Exception $e) {
                    Log::warning("Row processing failed: " . $e->getMessage(), ['row' => $val]);
                    $failedCount++;
                }
            }

            // Bulk insert with smaller transaction
            if (!empty($allFinalArrays)) {
                DB::transaction(function () use ($allFinalArrays, &$successCount) {
                    $importCsvFileRecord = new ImportCsvFileRecordService();
                    $inserted = $importCsvFileRecord->bulkInsert($allFinalArrays);
                    $successCount = $inserted;
                }, 3); // Retry 3 times on deadlock
            }

        } catch (\Exception $e) {
            Log::error("Batch processing failed: " . $e->getMessage());
            $failedCount = count($batch);
            $successCount = 0;
        }

        return [
            'success' => $successCount,
            'failed' => $failedCount
        ];
    }

    /**
     * Get import path - handles both local and S3
     * For S3, downloads to temp file to avoid URL expiration
     */
    private function getImportPath($csv_file)
    {
        if (env('FILE_UPLOAD_PERMISSION') == 'development') {
            return public_path() . '/' . self::FILE_IMPORT_FOLDER . '/' . $csv_file->upload_file;
        }

        // For S3: Download to temporary file to avoid URL expiration during long processing
        try {
            $s3Path = self::FILE_IMPORT_FOLDER . '/' .$csv_file->upload_file;

            // Create temp directory if it doesn't exist
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $tempPath = $tempDir . '/' . uniqid('import_') . '_' . $csv_file->file;

            // Download from S3 to local temp file
            $contents = Storage::disk('s3')->get($s3Path);
            file_put_contents($tempPath, $contents);

            Log::info("Downloaded S3 file to temp: $tempPath");

            return $tempPath;
        } catch (\Exception $e) {
            Log::error("Failed to download S3 file: " . $e->getMessage());

            // Fallback to temporary URL (may expire for large files)
            return Storage::disk('s3')->temporaryUrl(
                self::FILE_IMPORT_FOLDER . '/' . $csv_file->file,
                now()->addHours(3) // Extended to 3 hours
            );
        }
    }

    /**
     * Check if using S3 storage
     */
    private function isS3()
    {
        return env('FILE_UPLOAD_PERMISSION') !== 'development';
    }

    private function mapRowsToOrderedData($import_data, $order_data)
    {
        $result = [];
        foreach ($import_data as $row) {
            $mapped = $this->mapSingleRow($row, $order_data);
            if (!empty($mapped)) {
                $result[] = $mapped;
            }
        }
        return $result;
    }

    private function normalizeRows($data)
    {
        $finalArray = [];
        foreach ($data as $row) {
            if (count($row) > 0) {
                try {
                    $finalArray[] = $this->normalizePatientRow($row);
                } catch (\Exception $e) {
                    Log::warning("Row normalization failed: " . $e->getMessage(), ['row' => $row]);
                }
            }
        }
        return $finalArray;
    }

    private function mapSingleRow($row, $order_data)
    {
        $mapped = [];
        foreach ($order_data as $index => $column) {
            if ($column === "") {
                continue;
            }

            // merged condition
            if ($column === 'type' && !$this->validateType(trim($row[$index]) ?? '')) {
                return []; // Invalid row
            }

            $mapped[$column] = $row[$index] ?? '';
        }
        return $mapped;
    }

    private function validateType($value)
    {
        $typeArray = ['caregiver', 'patient'];
        if (!in_array(strtolower($value), $typeArray)) {
            return false;
        }
        return true;
    }

    private function normalizePatientRow($row)
    {
        // Date parsing with error handling
        if (!empty($row['dob'])) {
            try {
                $dob = str_replace('-', '/', $row['dob']);
                $parts = explode('/', $dob);

                if (count($parts) >= 3) {
                    list($m, $d, $y) = $parts;

                    if (strlen($y) == 4) {
                        // Full year format
                        $corrected = Carbon::parse($dob)->format('Y-m-d');
                    } else {
                        // Two digit year - apply logic
                        $year = ($y <= date('y')) ? "20$y" : "19$y";
                        $corrected = Carbon::create($year, $m, $d)->format('Y-m-d');
                    }

                    $row['dob'] = $corrected;
                } else {
                    // Try direct parsing
                    $row['dob'] = Carbon::parse($row['dob'])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                Log::warning("Invalid date format: {$row['dob']}");
                $row['dob'] = null;
            }
        }

        $row['type'] = ucwords(strtolower($row['type']));
        $row['serviceArray'][] = $row['service_id'] ?? "";

        return $row;
    }

    private function processSingleRowForBulk($val, $key, $csv_file, $cnt, $import_file_id)
    {
        // SERVICE PROCESSING
        [$mainServiceName,$mainServiceIds] = $this->processServicesForRow($val, $key);
        
        $updated_insurance_name_id = null;
        $insurance_id = null;

        if (!empty($val['insurance_id'])) {
            $insurance_id = $val['insurance_id'];
        }

        if (!empty($val['insurance_name'])) {
            $updated_insurance_name_id = $val['insurance_name'];
        }

        $serviceName = '';
        if (!empty($mainServiceName[$key][0])) {
            $serviceName = implode(',', $mainServiceName[$key][0]);
        }

        $val['import_file_id'] = $import_file_id;

        $final_array = $this->prepareFinalArray(
            $val,
            $csv_file->agency_id,
            $serviceName,
            $serviceName,
            $insurance_id,
            $cnt,
            $updated_insurance_name_id,
            $csv_file->agency_id
        );

        /** -----------------------------
         * STATUS CALCULATION
         * ----------------------------*/
        $status = "Pending";
        $mainServiceIds1 =$mainServiceIds[$key][0];

        if (strtolower($final_array['type']) == 'patient') {
            $status = Utility::getStatusFromServiceId($mainServiceIds1);
        }

        $final_array['status'] = $status;
        $final_array['sync_status'] = 'N';
        $final_array['created_by'] = $this->data['created_by'];
        $final_array['created_date'] = date('Y-m-d H:i:s');
        $final_array['del_flag'] = 'N';

        return $final_array;
    }

    private function processServicesForRow($val, $key)
    {
        $mainServiceIds = [];
        $mainServiceName = [];

        if (!empty($val['serviceArray'][0])) {
            foreach ($val['serviceArray'] as $serviceList) {

                $elements = explode(',', $serviceList);
                $serviceIds = [];
                $serviceNames = [];

                foreach ($elements as $srd) {
                    $masters = Master::where('del_flag', 'N')
                    ->where('master_type_fk', 11)->whereRaw('LOWER(types) LIKE ?', ['%' . strtolower($val['type']) . '%'])->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($srd) . '%'])
                    ->first();
                    if(isset($masters->id)){
                        $serviceIds[] = trim($masters->id);
                    }
                    
                    $serviceNames[] = $srd;
                }

                $mainServiceIds[$key][] = $serviceIds;
                $mainServiceName[$key][] = $serviceNames;
            }
        }

        return [$mainServiceName,$mainServiceIds];
    }

    protected function prepareFinalArray($val, $agency_id, $mainServiceIds, $mainServiceName, $insurance, $cnt, $updated_insurance_name_id, $agencyname)
    {
        $uns = uniqid();

        $unitId = $uns . $cnt;

        $test_new = '';
        $test_new = !empty($val['service_expiry_date'])
            ? date('Y-m-d', strtotime($val['service_expiry_date']))
            : '';

        $lang = $val['language'] ?? '';

        $mobile = $this->cleanPhone($val['mobile'] ?? '');
        $phone = $this->cleanPhone($val['phone'] ?? '');

        $fname = $val['first_name'] ?? null;
        $last_name = $val['last_name'] ?? null;

        $gender = isset($val['gender']) && $val['gender'] !== '' ? strtolower($val['gender']) : null;

        $type = '';

        $type = $val['type'] ?? null;

        $unitId = ($type == 'Caregiver') ? $unitId : null;

        $patient_code = !empty($val['patient_code']) ? $val['patient_code'] : null;
        $diciplin = !empty($val['diciplin']) ? $val['diciplin'] : null;
        $address1 = !empty($val['address1']) ? $val['address1'] : null;
        $address2 = !empty($val['address2']) ? $val['address2'] : null;
        $city = !empty($val['city']) ? $val['city'] : null;
        $state = !empty($val['state']) ? $val['state'] : null;
        $zip_code = !empty($val['zip']) ? $val['zip'] : null;
        $ip_address = $this->data['ip'];

        $final_array = [
            'agency_id' => $agency_id,
            'patient_code' => $patient_code,
            'type' => $type,
            'first_name' => $fname,
            'last_name' => $last_name,
            'full_name' => $fname . ' ' . $last_name,
            'mobile' => $mobile,
            'dob' => $val['dob'] ?? null,
            'gender' => $gender,
            'language' => $lang,
            'service_id' => $mainServiceIds,
            'service_expiry_date' => $test_new,
            'appointment_mode' => 'Manual',
            'cin' => $val['cin'] ?? null,
            'insurance_name' => $updated_insurance_name_id,
            'insurance_id' => $insurance,
            'phone' => $phone,
            'diciplin' => $diciplin,
            'address1' => $address1,
            'address2' => $address2,
            'state' => $state,
            'city' => $city,
            'zip_code' => $zip_code,
            'import_file_id' => $val['import_file_id'],
            'ip_address' => $ip_address
        ];

        if ($type == 'Caregiver') {
            $final_array['key'] = $unitId;
        } else {
            $final_array['key'] = null;
        }

        return $final_array;
    }

    /**
     * Remove spaces and dashes from phone numbers.
     */
    private function cleanPhone($number)
    {
        return str_replace(['-', ' '], '', $number);
    }
}
