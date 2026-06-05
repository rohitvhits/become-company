<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Model\PaymentImportLog;
use App\Helpers\Utility;
use App\Services\LogsService;
use Carbon\Carbon;
class PaymentLogImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Step 1: Upload File Page
    public function index()
    {
        $data['user'] = auth()->user();
        $data['imports'] = PaymentImportLog::with('uploader')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('payment_log_import.index', $data);
    }

    // Step 2: Handle File Upload
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'import_file' => 'required|mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $file = $request->file('import_file');
            $fileName = uniqid().'-'.time() . '_' . $file->getClientOriginalName();
            if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
			    Storage::disk('s3')->putFileAs('payment_imports', $file, $fileName);
                $filePath = "";
            }else{
                $filePath = $file->storeAs('payment_imports', $fileName);
            }

            // Create log entry
            $importLog = PaymentImportLog::create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'uploaded_by' => auth()->id(),
                'upload_status' => 'Pending',
                'uploaded_at' => now()
            ]);

            self::handleLogs('Uploaded Payment Import Log File',url('account/payment-log-import/upload'),'Import Payment Log',0,NULL,NULL,'has uploaded Payment Import Log file');
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'import_id' => $importLog->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ]);
        }
    }

    // Step 3: Show Mapping Screen
    public function showMapping($id)
    {
        $importLog = PaymentImportLog::findOrFail($id);
        
        // Read file headers
        if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
            $filePath = Storage::disk('s3')->temporaryUrl(
                'payment_imports/' . $importLog->file_name,
                Carbon::now()->addMinutes(10)
            );
            
        }else{
            $filePath = storage_path('app/' . $importLog->file_path);
        }
        
        $headers = $this->getFileHeaders($filePath);
        $preview = $this->getFilePreview($filePath, 5);

        // Database fields for mapping
        $dbFields = [
            'name' => 'Name',
            'dob' => 'Date of Birth',
            'patient_id' => 'Portal ID',
            'vendor_name' => 'Vendor Name',
            'service_type' => 'Service Type (Initial/Annual)',
            'services' => 'Services',
            'ppd_q' => 'PPD/Q',
            'bill' => 'Bill Amount',
            'cash' => 'Cash Amount',
            'card' => 'Card Amount',
            'insurance' => 'Insurance Amount',
            'location' => 'Location',
            'initials' => 'Initials',
            'created_at' => 'Created At'
        ];
        $data['user'] = auth()->user();
        $data['importLog'] = $importLog;
        $data['headers'] = $headers;
        $data['preview'] = $preview;
        $data['dbFields'] = $dbFields;

        return view('payment_log_import.mapping', $data);
    }

    // Step 4: Process Mapping and Validate
    public function processMapping(Request $request, $id)
    {
        $importLog = PaymentImportLog::findOrFail($id);
        $mapping = $request->input('mapping');

        // Store mapping in session
        session(['payment_import_mapping_' . $id => $mapping]);

        // Read and validate file
        if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
            $filePath = Storage::disk('s3')->temporaryUrl(
                'payment_imports/' . $importLog->file_name,
                Carbon::now()->addMinutes(10)
            );
            
        }else{
            $filePath = storage_path('app/' . $importLog->file_path);
        }
        
        $result = $this->validateFileData($filePath, $mapping);

        // Update import log
        $importLog->update([
            'total_records' => $result['total'],
            'valid_records' => $result['valid_count'],
            'invalid_records' => $result['invalid_count'],
            'error_log' => $result['errors']
        ]);

        $data['user'] = auth()->user();
        $data['importLog'] = $importLog;
        $data['validData'] = $result['valid_data'];
        $data['invalidData'] = $result['errors'];
        $data['summary'] = [
            'total' => $result['total'],
            'valid' => $result['valid_count'],
            'invalid' => $result['invalid_count']
        ];
        
        return view('payment_log_import.validation', $data);
    }

    // Step 5: Final Import
    public function confirmImport($id)
    {
        $importLog = PaymentImportLog::findOrFail($id);
        $mapping = session('payment_import_mapping_' . $id);

        if (!$mapping) {
            return redirect()->route('payment_log_import.index')
                ->with('error', 'Mapping data not found. Please upload again.');
        }

        try {
            DB::beginTransaction();
             // Read and validate file
            if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
                $filePath = Storage::disk('s3')->temporaryUrl(
                    'payment_imports/' . $importLog->file_name,
                    Carbon::now()->addMinutes(10)
                );
                
            }else{
                $filePath = storage_path('app/' . $importLog->file_path);
            }
            $result = $this->validateFileData($filePath, $mapping);

            if ($result['valid_count'] > 0) {
                // Insert valid records
                DB::table('payment_logs')->insert($result['valid_data']);

                // Update import log
                $importLog->update([
                    'upload_status' => 'Processed'
                ]);

                DB::commit();
                self::handleLogs('Confirmed Payment Import Log',url('account/payment-log-import/confirm/{$id}'),'Import Payment Log',$id,NULL,serialize($result['valid_data']),'has confirmed and imported Payment Log');
                return redirect()->route('payment_log_import.index')
                    ->with('success', $result['valid_count'] . ' records imported successfully');
            } else {
                DB::rollback();
                return redirect()->route('payment_log_import.index')
                    ->with('error', 'No valid records to import');
            }

        } catch (\Exception $e) {
            DB::rollback();
            $importLog->update(['upload_status' => 'Failed']);

            return redirect()->route('payment_log_import.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // Helper: Get file headers
    private function getFileHeaders($filePath)
    {
        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);
        fclose($file);
        return $headers;
    }

    // Helper: Get file preview
    private function getFilePreview($filePath, $limit = 5)
    {
        $preview = [];
        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);

        $count = 0;
        while (($row = fgetcsv($file)) !== false && $count < $limit) {
            $preview[] = $row;
            $count++;
        }
        fclose($file);

        return $preview;
    }

    // Helper: Validate file data
    private function validateFileData($filePath, $mapping)
    {
        $validData = [];
        $errors = [];
        $totalRecords = 0;

        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);
        $rowNumber = 1;

        while (($row = fgetcsv($file)) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if ($this->isRowEmpty($row)) {
                continue;
            }

            $record = $this->mapRowToRecord($row, $headers, $mapping);

            // Count this row as a total record
            $totalRecords++;

            // Check if row has all required fields filled
            if (!$this->hasRequiredFields($record)) {
                // Add to errors with missing fields message
                $missingFields = $this->getMissingRequiredFields($record);
                $errors[] = [
                    'row' => $rowNumber,
                    'data' => $record,
                    'errors' => ['Missing required fields: ' . implode(', ', $missingFields)]
                ];
                continue;
            }

            $validation = $this->validateRecord($record, $rowNumber);

            if ($validation['valid']) {
                $validData[] = array_merge($record, [
                    'dob' => date('Y-m-d',strtotime($record['dob'])),
                    'status' => 'draft',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $errors[] = [
                    'row' => $rowNumber,
                    'data' => $record,
                    'errors' => $validation['errors']
                ];
            }
        }
        fclose($file);
        return [
            'valid_data' => $validData,
            'errors' => $errors,
            'total' => $totalRecords,
            'valid_count' => count($validData),
            'invalid_count' => count($errors)
        ];
    }

    // Helper: Check if row is completely empty
    private function isRowEmpty($row)
    {
        if (empty($row)) {
            return true;
        }

        foreach ($row as $cell) {
            if (trim($cell) !== '' && $cell !== null) {
                return false;
            }
        }

        return true;
    }

    // Helper: Check if row has all required fields filled
    private function hasRequiredFields($record)
    {
        // Define required fields
        $requiredFields = ['name', 'dob', 'vendor_name', 'patient_id','services'];

        foreach ($requiredFields as $field) {
            if (!isset($record[$field]) || trim($record[$field]) === '' || $record[$field] === null) {
                return false;
            }
        }

        return true;
    }

    // Helper: Get missing required fields
    private function getMissingRequiredFields($record)
    {
        $requiredFields = ['name', 'dob', 'vendor_name', 'patient_id','services'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($record[$field]) || trim($record[$field]) === '' || $record[$field] === null) {
                // Convert field name to readable format
                $readableFieldNames = [
                    'name' => 'Name',
                    'dob' => 'Date of Birth',
                    'vendor_name' => 'Vendor Name',
                    'patient_id' => 'Portal ID',
                    'services' => 'Services'
                ];
                $missingFields[] = $readableFieldNames[$field] ?? $field;
            }
        }

        return $missingFields;
    }

    // Helper: Map row to record
    private function mapRowToRecord($row, $headers, $mapping)
    {
        $record = [];

        foreach ($mapping as $dbField => $fileColumn) {
            if ($fileColumn !== '') {
                $columnIndex = array_search($fileColumn, $headers);
                $record[$dbField] = isset($row[$columnIndex]) ? trim($row[$columnIndex]) : null;
            } else {
                $record[$dbField] = null;
            }
        }

        return $record;
    }

    // Helper: Validate record
    private function validateRecord($record, $rowNumber)
    {
        $errors = [];

        // Required fields validation
        if (empty($record['name'])) {
            $errors[] = 'Name is required';
        } else {
            if (strlen($record['name']) > 255) {
                $errors[] = 'Name must not exceed 255 characters';
            }
        }

        // Date of Birth validation
        if (empty($record['dob'])) {
            $errors[] = 'Date of Birth is required';
        } else {
            // Validate date format (MM/DD/YYYY or MM-DD-YYYY)
            $dateFormats = ['m/d/Y', 'Y-m-d', 'm-d-Y', 'd/m/Y', 'd-m-Y'];
            $validDate = false;
            $parsedDate = null;

            foreach ($dateFormats as $format) {
                $date = \DateTime::createFromFormat($format, $record['dob']);
                if ($date && $date->format($format) === $record['dob']) {
                    $validDate = true;
                    $parsedDate = $date;
                    break;
                }
            }

            if (!$validDate) {
                $errors[] = 'Date of Birth is not a valid date (expected format: MM/DD/YYYY)';
            } else {
                // Check if date is not in the future
                if ($parsedDate > new \DateTime()) {
                    $errors[] = 'Date of Birth cannot be in the future';
                }
                // Check if date is reasonable (not more than 150 years ago)
                $minDate = new \DateTime('-150 years');
                if ($parsedDate < $minDate) {
                    $errors[] = 'Date of Birth is too far in the past';
                }
            }
        }

        // Vendor Name validation
        if (empty($record['vendor_name'])) {
            $errors[] = 'Vendor Name is required';
        } else {
            if (strlen($record['vendor_name']) > 255) {
                $errors[] = 'Vendor Name must not exceed 255 characters';
            }
        }

        // Patient ID validation
        if (empty($record['patient_id'])) {
            $errors[] = 'Portal ID is required';
        } else {
            // Check if patient_id is alphanumeric
            if (!preg_match('/^[0-9]+$/', $record['patient_id'])) {
                $errors[] = 'Portal ID must be numeric (letters and numbers only)';
            }
        }

        // Services validation
        if (empty($record['services'])) {
            $errors[] = 'Services is required';
        } else {
            if (strlen($record['services']) > 500) {
                $errors[] = 'Services must not exceed 500 characters';
            }
        }

        $record['bill'] = isset($record['bill']) && trim($record['bill']) !== '' ? trim($record['bill']) : null;

        // Cash validation - must be numeric if provided
        if (!empty($record['cash'])) {
            if (!is_numeric($record['cash'])) {
                $errors[] = 'Cash amount must be a valid number';
            } else if ($record['cash'] < 0) {
                $errors[] = 'Cash amount cannot be negative';
            } else if ($record['cash'] > 999999.99) {
                $errors[] = 'Cash amount is too large (max: 999999.99)';
            }
        }
        $record['cash'] = isset($record['cash']) && !empty($record['cash']) ? $record['cash'] : 0;

        // Card validation - must be numeric if provided
        if (!empty($record['card'])) {
            if (!is_numeric($record['card'])) {
                $errors[] = 'Card amount must be a valid number';
            } else if ($record['card'] < 0) {
                $errors[] = 'Card amount cannot be negative';
            } else if ($record['card'] > 999999.99) {
                $errors[] = 'Card amount is too large (max: 999999.99)';
            }
        }
        $record['card'] = isset($record['card']) && !empty($record['card']) ? $record['card'] : 0;

        // Insurance validation (optional text field)
        if (!empty($record['insurance'])) {
            if (strlen($record['insurance']) > 255) {
                $errors[] = 'Insurance must not exceed 255 characters';
            }
        }
        $record['insurance'] = isset($record['insurance']) && trim($record['insurance']) !== '' ? trim($record['insurance']) : null;

        // Location validation (optional field)
        if (!empty($record['location'])) {
            if (strlen($record['location']) > 255) {
                $errors[] = 'Location must not exceed 255 characters';
            }
        }

        // Initials validation (optional field)
        if (!empty($record['initials'])) {
            if (strlen($record['initials']) > 10) {
                $errors[] = 'Initials must not exceed 10 characters';
            }
            // Check if initials contain only letters
            if (!preg_match('/^[a-zA-Z\s]+$/', $record['initials'])) {
                $errors[] = 'Initials must contain only letters';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    // Download Sample CSV
    public function downloadSample()
    {
        $filename = 'payment_log_sample_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Name',
                'DOB',
                'Portal ID',
                'Vendor Name',
                'Service Type',
                'Services',
                'PPD/Q',
                'Bill',
                'Cash',
                'Card',
                'Insurance',
                'Location',
                'Initials'
            ]);

            // Sample Data Row 1
            fputcsv($file, [
                'John Doe',
                '01/15/1980',
                '9945',
                'ABC Medical Services',
                'Initial',
                'Consultation, Lab Test',
                'PPD',
                'AGENCY',
                '50.00',
                '100.00',
                'Insurance Co.',
                'New York',
                'JD'
            ]);

            // Sample Data Row 2
            fputcsv($file, [
                'Jane Smith',
                '05/22/1975',
                '9948',
                'XYZ Healthcare',
                'Annual',
                'Physical Exam, Blood Work',
                'Q',
                'AGENCY',
                '0.00',
                '200.00',
                'Medicare',
                'Los Angeles',
                'JS'
            ]);

            // Sample Data Row 3
            fputcsv($file, [
                'Robert Johnson',
                '11/30/1990',
                '9949',
                'City Clinic',
                'Initial',
                'Vaccination',
                'PPD',
                'self',
                '75.00',
                '0.00',
                '',
                'Chicago',
                'RJ'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function handleLogs($type,$link,$module,$object_id,$new_response,$old_response,$message){
        $user = auth()->user();
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => $type,
            'link' => $link,
            'module' => $module,
            'object_id' => $object_id,
            'message' => $user->first_name . ' ' . $user->last_name . ' '.$message,
            'new_response' => serialize($new_response),
            'old_response' => serialize($old_response),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
    }
}
