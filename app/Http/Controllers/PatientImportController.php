<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use App\Agency;
use Illuminate\Support\Facades\Validator;
use App\Services\ImportCsvFileRecordService;
use App\Master;
use App\Model\Patient;
use App\Model\Language;
use App\User;
use Illuminate\Support\Facades\URL;
use App\Services\AppointmentImportFileService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;

use App\Model\InsuranceMaster;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use DB;
use App\Jobs\ProcessPatientImport;
use Illuminate\Support\Facades\Artisan;
use App\Services\LogsService;

class PatientImportController extends BaseController
{
    protected $appointmentImportFileService;
    protected $importCsvFileRecordService;
    protected const FILE_IMPORT_FOLDER="patient_appointment_import";

    protected $successRecords = 0;
    protected $unTYPECOUNT = 0;
    protected const MODULE_TYPE = "Patient Import";
    public function __construct(
       
        AppointmentImportFileService $appointmentImportFileService,
        ImportCsvFileRecordService $importCsvFileRecordService
        )
    {

        $this->middleware('auth' );
        $this->middleware('permission:appointment-import', ['only' => ['index','patientCsv']]);
        $this->appointmentImportFileService = $appointmentImportFileService;
        $this->importCsvFileRecordService = $importCsvFileRecordService;
    }

    public function index(Request $request){
        $data['user'] = $user = auth()->user();

        if ($user->agency_fk != "") {
            $checkForAgencyDeteleted = Agency::getDetailsByAgencyId($user->agency_fk);
            if (!isset($checkForAgencyDeteleted->id)) {
                return redirect('support_error');
            }
        }

        $agencyList = Cache::get('patient_import_agency', function () {
            return Agency::getAgencyList();
        }, 10);
        $data['agencyList'] = $agencyList;
    
        return view('import/import_list',$data);
    }

    /**
     * Get appointment import files data via AJAX
     * Returns JSON response with Laravel pagination
     * Main table: appointment_import_file
     * Child table: import_csv_file_record
     * Returns: date/time, agency, file name, total records, successful count, failed count, status
     */
    public function getImportFilesData(Request $request)
    {
        $data = $this->appointmentImportFileService->getImportAppointmentsFile($request->all());
     
        return response()->json([
            'success' => true,
            'data' => $data['data'],
            'pagination' => [
                'total' => $data['importFiles']->total(),
                'per_page' => $data['importFiles']->perPage(),
                'current_page' => $data['importFiles']->currentPage(),
                'last_page' => $data['importFiles']->lastPage(),
                'from' => $data['importFiles']->firstItem(),
                'to' => $data['importFiles']->lastItem()
            ],
            'links' => [
                'first' => $data['importFiles']->url(1),
                'last' => $data['importFiles']->url($data['importFiles']->lastPage()),
                'prev' => $data['importFiles']->previousPageUrl(),
                'next' => $data['importFiles']->nextPageUrl()
            ]
        ]);
    }

    /**
     * Get import data via AJAX with pagination
     * URL: patient/view-import-data-ajax/{id}
     * Returns all fields from import_csv_file_record for a specific import_file_id
     */
    public function getViewImportDataAjax(Request $request, $id)
    {
        $data = $this->importCsvFileRecordService->getList($request->all(),$id);
    
        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ],
            'links' => [
                'first' => $data->url(1),
                'last' => $data->url($data->lastPage()),
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl()
            ]
        ]);
    }

    /**
     * Get single record details for modal display
     * URL: patient/view-import-data-show/{record_id}
     * Returns all fields from import_csv_file_record for a specific record ID
     */
    public function getViewImportRecordDetails($record_id)
    {
        $record = $this->importCsvFileRecordService->getDetailsById($record_id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'error_msg' => 'Record not found'
            ], 404);
        }
        unset($record->error_message);
        return response()->json([
            'success' => true,
            'data' => $record
        ]);
    }

    public function patientCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|file|mimetypes:text/plain,text/csv,text/comma-separated-values|max:102400',
        ], [
          
            'images.required' => 'Please upload a CSV file.',
            'images.mimes' => 'Only CSV files are allowed.',
            'images.max' => 'CSV file must not exceed 100 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => 0,
                'data' => []
            ], 422);
        } else {
            $file = $request->file('images');
            $filePath = $file->getRealPath();
            $pathExtension = $file->getClientOriginalExtension();
            $name = uniqid().time() . '.' . $file->getClientOriginalExtension();
            $originalFileName = $file->getClientOriginalName();
            if ($pathExtension == 'csv') {
                $data = $this->parseCsvFile($filePath);
            }else{
                $data = $this->parseExcelFile($filePath);
            }
            
            $date = date('mdy');
            if (env('FILE_UPLOAD_PERMISSION') == 'development') {
                $destination = public_path(self::FILE_IMPORT_FOLDER).'/'.$date;

                if (!is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }

                $file->move($destination, $name);
            }else{
                Storage::disk('s3')->putFileAs(self::FILE_IMPORT_FOLDER.'/'.$date, $file, $name);
            }

            $img = $date.'/'.$name;

            $data['import_data'] = $data;
            $insert = $this->appointmentImportFileService->save([
                'file' => $name,
                'file_name'=>$originalFileName,
                'agency_id' => $request->input('agency_id'),
                'extension' => $pathExtension,
                'upload_file'=>$img
            ]);

            $data['getlastId'] = $insert;

            return view('patient.patient_import_modal', $data);
        }
    }

    public function patientImports(Request $request)
    {
        $auth = auth()->user();
        try {
            $order_data = explode(',', $request->input('order_data'));

            $id = $request->input('last_id');
            $csvFile = $this->appointmentImportFileService->getDetailById($id);

            if (!$csvFile) {
                Session::flash('error', 'Import file not found.');
                return redirect()->back();
            }

            $path = $this->getImportPath($csvFile);
            $totalRecords = 0;

            // Count total records in the file (memory efficient)
            if (($handle = fopen($path, 'r')) !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    // Skip empty rows
                    if (count(array_filter($row)) === 0){
                        continue ;
                    }

                    $totalRecords++;
                }
                fclose($handle);
            }
            $totalRecords = max($totalRecords - 1, 0); // Subtract header row

            // Dispatch job to process import in background
            $ip = Utility::getIP();
            ProcessPatientImport::dispatch([
                'import_id' => $id,
                'order_data' => $order_data,
                'created_by' => auth()->user()->id,
                'ip'=>$ip
            ]);

            // Update import file status
            $this->appointmentImportFileService->update(
                [
                    'status' => 'Waiting For Approval',
                    'total_record' => $totalRecords
                ],
                ['id' => $request->input('last_id')]
            );

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Add Import File',
                'link' => url('/patient/patient-import'),
                'module' => self::MODULE_TYPE,
                'object_id' => $request->input('last_id'),
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has added import file',
                'new_response' => serialize($csvFile->toArray()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            $message = 'Import queued successfully. Your data is being processed in the background. You can continue working.';
            Session::flash('success', $message);

        } catch (\Exception $e) {
            \Log::error('Failed to queue import: ' . $e->getMessage());
            Session::flash('error', 'Failed to start import. Please try again.');
        }

        return redirect()->back();
    }
    
    public function viewImportDataByImportID(Request $request,$id){
        $data['user'] = auth()->user();
        $data['import_file_id'] = $id;

        // Get import file details
        $importFile = $this->appointmentImportFileService->getDetailById($id);

        if (!$importFile) {
            return redirect('patient/import')->with('error', 'Import file not found');
        }

        $data['importFile'] = $importFile;
        
        if (env('FILE_UPLOAD_PERMISSION') == 'development') {
            // Local/public path
            $filePath = asset(self::FILE_IMPORT_FOLDER . '/' . $importFile->file);
        } else {
            // S3 path
            $filePath = Storage::disk('s3')->url(self::FILE_IMPORT_FOLDER . '/' .  $importFile->file);
        }
        $data['filePath'] = $filePath;
        $data['per_page'] = 50;
        return view('import/view_import_list', $data);
    }

    private function parseCsvFile($path){
        $handle = fopen($path, 'r'); // open file
        $data = [];

        while (($rowData = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (!array_filter($rowData)){
                continue;
            }

            $data[] = $rowData;
            if (count($data) === 11){
                break;
            }
        }

        fclose($handle);

        return $data;
    }

    private function parseExcelFile($path){
      
        $news = Excel::toArray([], $path);
        $dataa = $news[0] ?? [];

        $data = [];
        $limit = 10; // show only first 10 rows
        $count = 0;
    
        if (!empty($dataa)) {
            foreach ($dataa as $row) {
                if (!array_filter($row)) {
                    continue;
                }
                $data[] = $row;
                $count++;
                if ($count === $limit) {
                    break;
                }
            }
        }
        return $data;
    }

    private function getImportPath($csv_file)
    {
        if (env('FILE_UPLOAD_PERMISSION') == 'development') {
            return public_path() . '/' . self::FILE_IMPORT_FOLDER . '/' .  $csv_file->upload_file;
        }

        return Storage::disk('s3')->temporaryUrl(
            self::FILE_IMPORT_FOLDER . '/' . $csv_file->upload_file,
            now()->addMinutes(10)
        );
    }

    public function downloadFile($id){
        $csvFile = $this->appointmentImportFileService->getDetailById($id);
        $path = $this->getImportPath($csvFile);
        if (env('FILE_UPLOAD_PERMISSION') === 'development') {
            return response()->download($path, $csvFile->upload_file);
        }else{
            return Storage::disk('s3')->download(self::FILE_IMPORT_FOLDER . '/' . $csvFile->upload_file);
        }
    }

    public function getImportLogs(Request $request, $id){
        try {
            $importFile = $this->appointmentImportFileService->getDetailById($id);
            if (!$importFile) {
                return response()->json(['status' => false, 'error_msg' => 'Import record not found.'], 404);
            }

            $page = $request->get('page', 1);
            $perPage = 20;

            $query = LogsService::getAllAppointmentLogs($id, "Patient Import");
            if(!empty($query[0])){
                foreach($query as $val){
                    $val->created_date =  Utility::convertYMDTime($val->created_at);
                    $val->old_response = unserialize($val->old_response);
                    $newResponse = unserialize($val->new_response);
                    
                    if(isset($newResponse['agency'])){
                        $newResponse['agency_name'] = $newResponse['agency']['agency_name'];
                        unset($newResponse['agency']);
                    }
                    unset($val->created_at);
                    $val->new_response = $newResponse;
                }
            }

            
            return response()->json([
                'status' => true,
                'data' => $query->items(),
                'pagination' => [
                    'total' => $query->total(),
                    'per_page' => $query->perPage(),
                    'current_page' => $query->currentPage(),
                    'last_page' => $query->lastPage(),
                    'from' => $query->firstItem(),
                    'to' => $query->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Import logs fetch failed', ['import_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['status' => false, 'error_msg' => 'Failed to fetch import logs.'], 500);
        }
    }

    public function approveImportHistory($id){
        try {
            $importFile = $this->appointmentImportFileService->getDetailById($id);
            if (!$importFile) {
                return response()->json(['status' => false, 'error_msg' => 'Import record not found.'], 404);
            }

            // Prevent duplicate approval
            if ($importFile->import_status === 'Approved') {
                return response()->json(['status' => false, 'error_msg' => 'This import record has already been approved.'], 422);
            }

            $auth = auth()->user();
            $data = [
                'status' => 'Pending',
                'import_status' => 'Approved',
                'approved_date' => date('Y-m-d H:i:s'),
                'approved_by' => $auth->id,
            ];
            $this->appointmentImportFileService->update($data, ['id' => $id]);
    
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Approved Import File',
                'link' => url('/patient/approve-import'),
                'module' => self::MODULE_TYPE,
                'object_id' => $id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has approved import file',
                'old_response' => serialize($importFile->toArray()),
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => true, 'error_msg' => 'Import approved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error_msg' => 'Failed to approve import record.'], 500);
        }
    }

    public function deleteImportHistory($id){
        $auth = auth()->user();
        try {
            $importFile = $this->appointmentImportFileService->getDetailById($id);
            if (!$importFile) {
                return response()->json(['success' => false, 'error_msg' => 'Import record not found.'], 404);
            }
            $this->appointmentImportFileService->softDelete(['delete_flag' => 'Y'], ['id' => $id]);

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Delete Import File',
                'link' => url('/patient/delete-import'),
                'module' => self::MODULE_TYPE,
                'object_id' => $id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' has deleted import file',
                'old_response' => serialize($importFile->toArray()),
                'new_response' => serialize(['id'=>$id,'delete_flag'=>'Y']),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['success' => true, 'error_msg' => 'Import record deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_msg' => 'Failed to delete import record.'], 500);
        }
    }

    public function syncImport(){
        Artisan::call('import:validate-records');

        return response()->json([
            'status' => 'success',
            'error_msg' => 'Sync started successfully'
        ]);
    }
}
