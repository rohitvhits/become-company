<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Employee;
use App\Model\HubImportLog;
use App\Model\HubRecord;
use App\Model\HubRecordAgency;
use App\Services\HubRecordService;
use App\Services\HubRecordAgencyService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Common;

class HubImportsService
{
    protected $hubRecordService;
    protected $hubRecordAgencyService;

    public function __construct(HubRecordService $hubRecordService, HubRecordAgencyService $hubRecordAgencyService)
    {
        $this->hubRecordService = $hubRecordService;
        $this->hubRecordAgencyService = $hubRecordAgencyService;
    }

    protected $fieldMapping = [
        'Last Name' => 'last_name',
        'First Name' => 'first_name',
        'Middle Initial' => 'middle_name',
        'Birth Date' => 'dob',
        'Gender' => 'gender',
        'Email Address' => 'email',
        'Primary Address 1' => 'address1',
        'Primary Address 2' => 'address2',
        'Primary City' => 'city',
        'Primary State' => 'state',
        'Primary Zip Code' => 'zip_code',
        'Home Phone' => 'phone',
        'Mobile Phone' => 'mobile',
        'SSN' => 'ssn',
        // Fields that go to hub_record_agency table
        'Hire Date' => 'hire_date',
        'Work Contact' => 'work_contact',
        'Work Email' => 'work_email',
        'Last Worked Date' => 'last_worked_date',
        'Member Id' => 'member_id',
        'Employee Code' => 'employee_code'
    ];

    public function processEmployeeImport(UploadedFile $file, $agencyId, array $uniqueFields)
    {
        $auth = auth()->user();
        $importLog = null;

        DB::beginTransaction();

        try {
            // Create import log
            $importLog = HubImportLog::create([
                'agency_id' => $agencyId,
                'file_name' => $file->getClientOriginalName(),
                'unique_fields' => $uniqueFields,
                'status' => 'Processing',
                'created_by' => $auth->id,
                'created_date' => now()
            ]);

            // Parse the file
            $data = $this->parseFile($file);

            if (empty($data)) {
                throw new \Exception('No data found in file or invalid file format');
            }

            // Validate file size
            if (count($data) > 5000) {
                throw new \Exception('File too large. Maximum 5000 rows allowed');
            }

            // Validate headers
            $this->validateHeaders($data[0], $uniqueFields);

            // Process data
            $summary = $this->processEmployeeData($data, $agencyId, $uniqueFields);

            // Mark inactive employees not in import
            $inactiveCount = $this->markInactiveEmployees($agencyId, $summary['processed_ids']);
            $summary['marked_inactive'] = $inactiveCount;

            // Update import log
            $importLog->update([
                'total_records' => $summary['total_records'],
                'inserted_count' => $summary['inserted'],
                'updated_count' => $summary['updated'],
                'failed_count' => $summary['failed'],
                'inactive_count' => $inactiveCount,
                'status' => 'Completed',
                'error_details' => $summary['errors']
            ]);

            DB::commit();

            return $summary;
        } catch (\Exception $e) {
            DB::rollback();

            if ($importLog) {
                $importLog->update([
                    'status' => 'Failed',
                    'error_details' => [$e->getMessage()]
                ]);
            }

            throw new \Exception('Import failed: ' . $e->getMessage());
        }
    }

    protected function parseFile(UploadedFile $file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $data = [];

        try {
            if ($extension === 'csv') {
                $handle = fopen($file->getPathname(), 'r');
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
            } else {
                // Handle Excel files
                $reader = IOFactory::createReader(ucfirst($extension));
                $spreadsheet = $reader->load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
            }
        } catch (Exception $e) {
            throw new \Exception('Invalid file format. Please upload CSV or Excel.');
        }

        return array_filter($data, function ($row) {
            return !empty(array_filter($row));
        });
    }

    protected function validateHeaders(array $headers, array $uniqueFields)
    {
        $requiredHeaders = [];
        foreach ($uniqueFields as $field) {
            $displayName = HubRecord::getUniqueFields()[$field] ?? $field;
            $requiredHeaders[] = $displayName;
        }

        $missingHeaders = array_diff($requiredHeaders, $headers);
        if (!empty($missingHeaders)) {
            throw new \Exception('Missing required headers: ' . implode(', ', $missingHeaders));
        }

        if (empty($uniqueFields)) {
            throw new \Exception('Please select at least one field for duplicate check.');
        }
    }

    protected function processEmployeeData(array $data, $agencyId, array $uniqueFields)
    {
        $headers = array_shift($data);
        $headerMapping = $this->createHeaderMapping($headers);

        $summary = [
            'total_records' => count($data),
            'inserted' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => [],
            'processed_ids' => []
        ];

        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because we removed header and array is 0-indexed

            try {
                $employeeData = $this->mapRowData($row, $headerMapping, $agencyId);

                // Validate row data
                $validation = $this->validateRowData($employeeData, $rowNumber);
                if (!$validation['valid']) {
                    $summary['failed']++;
                    $summary['errors'][] = $validation['error'];
                    continue;
                }

                // Check for existing employee
                $existingEmployee = $this->findExistingEmployee($employeeData, $agencyId, $uniqueFields);

                if ($existingEmployee) {
                    // Update existing employee
                    $this->updateEmployee($existingEmployee, $employeeData);
                    $summary['updated']++;
                    $summary['processed_ids'][] = $existingEmployee->id;
                } else {
                    // Create new employee
                    $newEmployee = $this->createEmployee($employeeData);
                    $summary['inserted']++;
                    $summary['processed_ids'][] = $newEmployee->id;
                }
            } catch (\Exception $e) {
                $summary['failed']++;
                $summary['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        return $summary;
    }

    protected function createHeaderMapping(array $headers)
    {
        $mapping = [];
        foreach ($headers as $index => $header) {
            $header = trim($header);
            if (isset($this->fieldMapping[$header])) {
                $mapping[$index] = $this->fieldMapping[$header];
            }
        }
        return $mapping;
    }

    protected function mapRowData(array $row, array $headerMapping, $agencyId)
    {
        $auth = auth()->user();
        $employeeData = [
            'agency_id' => $agencyId,
            'status' => 'Active',
            'deleted_flag' => 'N',
            'created_by' => $auth->id,
            'created_date' => now(),
            'updated_by' => $auth->id,
            'updated_date' => now()
        ];

        foreach ($headerMapping as $index => $fieldName) {
            $value = isset($row[$index]) ? trim($row[$index]) : null;

            if (!empty($value)) {
                // Handle date fields
                if (in_array($fieldName, ['dob', 'hire_date', 'last_worked_date'])) {
                    $employeeData[$fieldName] = $this->parseDate($value);
                } else {
                    $employeeData[$fieldName] = $value;
                }
            }
        }

        return $employeeData;
    }

    protected function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try multiple date formats
            $formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'Y-m-d H:i:s', 'm-d-Y'];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date && $date->format($format) === $dateString) {
                    return $date->format('Y-m-d');
                }
            }

            // Try Carbon parsing as fallback
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Invalid date format: {$dateString}");
        }
    }

    protected function validateRowData(array $data, $rowNumber)
    {
        $errors = [];

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row {$rowNumber}: Invalid email format";
        }

        // SSN validation (basic format check)
        if (!empty($data['ssn'])) {
            $ssn = preg_replace('/[^0-9]/', '', $data['ssn']);
            if (strlen($ssn) !== 9) {
                $errors[] = "Row {$rowNumber}: Invalid SSN format";
            }
        }

        // Phone validation (basic format check)
        if (!empty($data['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $data['phone']);
            if (strlen($phone) < 10) {
                $errors[] = "Row {$rowNumber}: Invalid phone format";
            }
        }

        // Gender validation
        if (!empty($data['gender']) && !in_array($data['gender'], ['Male', 'Female', 'Other'])) {
            $errors[] = "Row {$rowNumber}: Invalid gender value";
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'error' => implode(', ', $errors)
            ];
        }

        return ['valid' => true];
    }

    protected function findExistingEmployee(array $data, $agencyId, array $uniqueFields)
    {
        $query = HubRecord::where('agency_id', $agencyId)
            ->where('deleted_flag', 'N');

        $conditions = [];
        foreach ($uniqueFields as $field) {
            if (!empty($data[$field])) {
                $conditions[] = [$field, '=', $data[$field]];
            }
        }

        if (empty($conditions)) {
            return null;
        }

        // Use OR conditions for uniqueness check
        $query->where(function ($q) use ($conditions) {
            foreach ($conditions as $condition) {
                $q->orWhere($condition[0], $condition[1], $condition[2]);
            }
        });

        return $query->first();
    }

    protected function createEmployee(array $data)
    {
        return HubRecord::create($data);
    }

    protected function updateEmployee(HubRecord $employee, array $data)
    {
        // Don't update created_by and created_date
        unset($data['created_by'], $data['created_date']);

        return $employee->update($data);
    }

    protected function markInactiveEmployees($agencyId, array $processedIds)
    {
        if (empty($processedIds)) {
            return 0;
        }

        return HubRecord::where('agency_id', $agencyId)
            ->where('deleted_flag', 'N')
            ->where('status', 'Active')
            ->whereNotIn('id', $processedIds)
            ->update(['status' => 'Inactive']);
    }

    public function getImportHistory($agencyId, $limit = 50)
    {
        return HubImportLog::with('creator')
            ->where('agency_id', $agencyId)
            ->orderBy('created_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'file_name' => $log->file_name,
                    'created_date' => $log->created_date,
                    'total_records' => $log->total_records,
                    'inserted_count' => $log->inserted_count,
                    'updated_count' => $log->updated_count,
                    'failed_count' => $log->failed_count,
                    'status' => $log->status,
                    'created_by' => $log->creator ? $log->creator->first_name . ' ' . $log->creator->last_name : 'Unknown'
                ];
            });
    }

    public function getImportDetails($importId)
    {
        $log = HubImportLog::with('creator', 'agency')->find($importId);

        if (!$log) {
            return null;
        }

        return [
            'id' => $log->id,
            'file_name' => $log->file_name,
            'agency_name' => $log->agency ? $log->agency->company_name : 'Unknown',
            'unique_fields' => $log->unique_fields,
            'total_records' => $log->total_records,
            'inserted_count' => $log->inserted_count,
            'updated_count' => $log->updated_count,
            'failed_count' => $log->failed_count,
            'inactive_count' => $log->inactive_count,
            'status' => $log->status,
            'error_details' => $log->error_details,
            'created_by' => $log->creator ? $log->creator->first_name . ' ' . $log->creator->last_name : 'Unknown',
            'created_date' => $log->created_date
        ];
    }
}
