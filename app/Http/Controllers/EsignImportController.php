<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TemplateService;
use App\Model\EsignImportDetail;
use App\Model\Patient;
use App\Jobs\EsignImportJob;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\EsignImportLogService;
use App\Services\EsignImportDetailService;
use App\Services\PatientV2Service;
use Illuminate\Support\Facades\Validator;

class EsignImportController extends Controller
{

    protected $templateService;
    protected $esignImportLogService;
    protected $esignImportDetailService;
    protected const FILE_IMPORT_FOLDER="dosusinguploads/esign_import";
    private $patientV2Service;

    public function __construct(TemplateService $templateService,EsignImportLogService $esignImportLogService,EsignImportDetailService $esignImportDetailService,PatientV2Service $patientV2Service)
    {
        $this->middleware('auth');
        $this->middleware('permission:template-import', ['only' => ['index', 'store']]);
        $this->middleware('permission:template-import-view-detail', ['only' => ['viewDetails', 'detailsAjax']]);
        $this->middleware('permission:template-import-download', ['only' => ['viewDetails', 'detailsAjax']]);
     
        $this->templateService = $templateService;
        $this->esignImportLogService = $esignImportLogService;
        $this->esignImportDetailService = $esignImportDetailService;
        $this->patientV2Service = $patientV2Service;
    }

    public function index()
    {
        $userTemplateType = auth()->user()->template_type ?? 'All';
        $templateType = strtolower($userTemplateType) == 'all' ? null : strtolower($userTemplateType);
        $templates = $this->templateService->getListByLookupFieldWithSignerCaregiver('caregiver', $templateType);

        return view('esign.esign_import', compact('templates', 'userTemplateType'));
    }

    public function store(Request $request)
    {
        $validation = $this->validateRequest($request);
        if ($validation !== true) {
            return $validation;
        }
        $user = auth()->user();
        $file = $request->file('csv_file');
        $originalName = $file->getClientOriginalName();
        $csvData = $this->processCsvFile($file,$request->template_id);
       
        if(empty($csvData['preview_rows'][0])){
            return response()->json([
                'success' => false,
                'error_msg' => 'No records found in the uploaded file.',
               
            ], 422);
        }

        $filePath = $this->moveToServer($file, $originalName);
        
        $importLog = $this->createImportLog(
            $request,
            $user,
            $originalName,
            $filePath,
            $csvData['total_records']
        );

        return response()->json([
            'success' => true,
            'error_msg' => 'CSV file uploaded successfully.',
            'data' => $importLog,
            'preview_data' => array_slice($csvData['preview_rows'], 0, 100),
            'total_records' => $csvData['total_records'],
            'matched_count' => $csvData['matched_count'],
            'not_found_count' => $csvData['not_found_count'],
            'agency_not_match_count' => $csvData['agency_not_match_count'],
            'type_not_match_count' => $csvData['type_not_match_count'],
        ],200);
    }

    /**
     * Process a chunk of CSV rows — lookup DB, update counts, collect preview rows (max 100).
     */
    private function processChunkForPreview(
        array $chunk,
        array &$previewRows,
        int &$matchedCount,
        int &$notFoundCount,
        int &$agencyNotMatchCount,
        int &$typeNotMatchCount,
        $templateId
    ): void {
        $patients = $this->getPatientsByPortalIds($chunk,$templateId);
        $templateAgencyIds = $this->fetchAgencyDetailsByTemplateId($templateId);

        foreach ($chunk as $row) {

            $previewData = $this->preparePreviewRow($row, $patients, $templateAgencyIds);

            $this->updateMatchCounters(
                $previewData['match_type'],
                $matchedCount,
                $notFoundCount,
                $agencyNotMatchCount,
                $typeNotMatchCount
            );

            $this->addPreviewRow($previewRows, $previewData);
        }
    }

    /**
     * Paginated mapping data — reads CSV from stored file for large datasets.
     */
    public function mappingData(Request $request)
    {
        $validation = $this->importLogValidation($request);
        if ($validation !== true) {
            return $validation;
        }
        
        $importLog = $this->esignImportLogService->getDetailsById($request->import_id);
        $filePath = $this->getFile($importLog);
       
        if (!file_exists($filePath) && env('FILE_UPLOAD_PERMISSION') =='development') {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        $page = (int) ($request->page ?? 1);
        $perPage = (int) ($request->per_page ?? 50);
        $offset = ($page - 1) * $perPage;

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return response()->json(['success' => false, 'message' => 'Unable to read file.'], 422);
        }

        $headers = fgetcsv($handle);

        // Detect columns
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

        // Skip rows until offset
        $currentRow = 0;
        while ($currentRow < $offset && fgetcsv($handle) !== false) {
            $currentRow++;
        }

        // Read current page rows
        $pageRows = [];
        $read = 0;
        while ($read < $perPage && ($row = fgetcsv($handle)) !== false) {
            $portalId = ($portalIdIndex !== null && isset($row[$portalIdIndex])) ? trim($row[$portalIdIndex]) : null;
            $name = ($nameIndex !== null && isset($row[$nameIndex])) ? trim($row[$nameIndex]) : '';
            $pageRows[] = [
                'portal_id' => $portalId,
                'name' => $name,
            ];
            $read++;
        }

        fclose($handle);

        // Batch lookup DB for this page's portal IDs (without agency filter)
        $portalIds = array_filter(array_column($pageRows, 'portal_id'));
        $patients = [];
        $templateAgencyIds = $this->fetchAgencyDetailsByTemplateId($importLog->template_id);
        if (!empty($portalIds)) {
            $patients = $this->patientV2Service->getListByBulkEsign($portalIds);
        }

        $agencyNotMatchCount = 0;
        $mappedData = [];
        foreach ($pageRows as $index => $row) {
            $portalId = $row['portal_id'];
            $patient = $portalId ? ($patients[$portalId] ?? null) : null;
            $fName = $patient['first_name']??"";
            $lName = $patient['last_name']??"";

            $status = 'Not Found';
            if ($patient) {
                $hasTemplateAgency = !empty($templateAgencyIds) && !empty($templateAgencyIds[0]);
                $patientAgencyId = $patient['agency_id'] ?? null;
                $patientType = $patient['type'] ?? null;
                if ($hasTemplateAgency && $patientAgencyId && !in_array($patientAgencyId, $templateAgencyIds)) {
                    $status = 'Agency Not Match';
                    $agencyNotMatchCount++;
                } else {
                    if(strtolower($patientType) =='caregiver'){
                        $status = 'Matched';
                    }else{
                        $status = 'Type Not Match';
                    }
                }
            }

            $mappedData[] = [
                'row_num' => $offset + $index + 1,
                'portal_id' => $portalId ?? 'N/A',
                'name' => $patient ? $fName.' '.$lName:"N/A",
                'mobile' => $patient ? $patient['mobile'] : 'N/A',
                'agency_name' => $patient ? ($patient['agency_name'] ?? 'N/A') : 'N/A',
                'status' => $status,
            ];
        }

        $totalRecords = $importLog->total_records;
        $lastPage = (int) ceil($totalRecords / $perPage);

        return response()->json([
            'success' => true,
            'data' => $mappedData,
            'agency_not_match_count' => $agencyNotMatchCount,
            'pagination' => [
                'total' => $totalRecords,
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'from' => $totalRecords > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $totalRecords),
            ],
            'links' => [
                'prev' => $page > 1 ? true : null,
                'next' => $page < $lastPage ? true : null,
            ],
        ],200);
    }

    public function deleteImport(Request $request)
    {
        $validation = $this->importLogValidation($request);
        if ($validation !== true) {
            return $validation;
        }

        $importLog = $this->esignImportLogService->getDetailsById($request->import_id);

        if (!$importLog) {
            return response()->json(['success' => false, 'message' => 'Import not found.'],404);
        }

        if ($importLog->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'Only pending imports can be deleted.'],422);
        }

        // Delete the uploaded file
        if ($importLog->file_path) {
            $filePath = self::FILE_IMPORT_FOLDER . '/' . $importLog->file_path;
            if (env('FILE_UPLOAD_PERMISSION') == 'development') {
                $fullPath = public_path($filePath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        $this->esignImportLogService->softDelete(['del_flag'=>'Y'],['id'=>$request->import_id]);

        return response()->json(['success' => true, 'message' => 'Import file deleted successfully.'],200);
    }

    public function confirmImport(Request $request)
    {
        $validation = $this->importLogValidation($request);
        if ($validation !== true) {
            return $validation;
        }

        $importLog = $this->esignImportLogService->getDetailsById($request->import_id);

        if ($importLog->status !== 'Pending') {
            return redirect()->back()->with('error', 'This import has already been ' . strtolower($importLog->status) . '.');
        }

        EsignImportJob::dispatch([
            'import_id' => $request->import_id,
            'created_by' => auth()->user()->id,
            'ip' => Utility::getIP(),
        ]);

        return redirect()->back()->with('success', 'Import confirmed and processing has started.');
    }

    public function importHistory(Request $request)
    {

        $data = $this->esignImportLogService->getList($request->all());
        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'links' => [
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl(),
            ]
        ],200);
    }

    public function downloadFile($id)
    {
        $log = $this->esignImportLogService->getDetailsById($id);
       
        if(env('FILE_UPLOAD_PERMISSION') != 'development'){
            return Storage::disk('s3')->download(self::FILE_IMPORT_FOLDER.'/'.$log->file_path);
        }else{
            $filePath = public_path(self::FILE_IMPORT_FOLDER.'/'.$log->file_path);
            if (!file_exists($filePath)) {
                return back()->with('error', 'File not found.');
            }
            return response()->download($filePath);
        }
    }

    public function sampleCSV()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="esign_import_sample.csv"',
        ];

        $columns = ['Portal Id'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, []);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function viewDetails($id)
    {
        $importLog = $this->esignImportLogService->getDetailsById($id);
        $smsStatus = $this->esignImportDetailService->combineSMSStatus();
        
        return view('esign.esign_import_details', compact('importLog','smsStatus'));
    }

    public function detailsAjax(Request $request)
    {
        $validation = $this->importLogValidation($request);
        if($validation !==true){
            return $validation;
        }
        
        $data = $this->esignImportDetailService->getListByImportId($request->all(),$request->import_id);

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'links' => [
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl(),
            ],
        ],200);
    }

    public function getErrorDetail(Request $request)
    {
        $request->validate(['detail_id' => 'required']);

        $detail = $this->esignImportDetailService->getDetailsById($request->detail_id);

        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Record not found.'],404);
        }

        return response()->json([
            'success' => true,
            'data' => $detail,
        ],200);
    }

    public function getTemplatesByType(Request $request)
    {
        $userTemplateType = auth()->user()->template_type ?? 'All';

        // Enforce user access - if not All, override the requested type
        if (strtolower($userTemplateType) != 'all') {
            $templateType = strtolower($userTemplateType);
        } else {
            $templateType = $request->template_type ?: null;
        }

        $templates = $this->templateService->getListByLookupField('caregiver', $templateType);

        return response()->json([
            'success' => true,
            'data' => $templates->map(function ($t) {
                return ['id' => $t->id, 'template_name' => $t->template_name];
            })
        ],200);
    }

    private function moveToServer($file,$originalName){
        $date = date('mdy');
        $name = uniqid().'_'.$originalName;
        if (env('FILE_UPLOAD_PERMISSION') == 'development') {
            $destination = public_path(self::FILE_IMPORT_FOLDER).'/'.$date;

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $name);
        }else{
            Storage::disk('s3')->putFileAs(self::FILE_IMPORT_FOLDER.'/'.$date, $file, $name);
        }
        
        return $date.'/'.$name;
    }

    /**
     * Validate request data.
     */
    private function validateRequest(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'template_id' => 'required',
            'csv_file' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        return true;
    }

    /**
     * Process uploaded CSV file.
     */
    private function processCsvFile($file,$templateId): array
    {

        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unable to read CSV file.'
            ], 422));
        }

        $headers = fgetcsv($handle);
        $indexes = $this->detectColumnIndexes($headers);

        $result = [
            'total_records' => 0,
            'matched_count' => 0,
            'not_found_count' => 0,
            'agency_not_match_count' => 0,
            'type_not_match_count' => 0,
            'preview_rows' => [],
        ];

        $this->processCsvChunks($handle, $indexes, $result,$templateId);

        fclose($handle);

        return $result;
    }

    /**
     * Detect CSV column indexes.
     */
    private function detectColumnIndexes(?array $headers): array
    {
        $indexes = [
            'portal_id' => null,
            'name' => null,
        ];

        if (!$headers) {
            return $indexes;
        }

        foreach ($headers as $i => $header) {
            $column = strtolower(trim($header));

            if (in_array($column, ['portal_id', 'portalid', 'portal id', 'id'])) {
                $indexes['portal_id'] = $i;
            }

            if (in_array($column, ['name', 'full_name', 'fullname'])) {
                $indexes['name'] = $i;
            }
        }

        return $indexes;
    }

    /**
     * Process CSV rows in chunks.
     */
    private function processCsvChunks($handle, array $indexes, array &$result,$templateId): void
    {
        $chunk = [];
        $chunkSize = 1000;

        while (($row = fgetcsv($handle)) !== false) {
            $result['total_records']++;
            if($row[0] !=""){
                $chunk[] = $this->prepareCsvRow(
                    $row,
                    $indexes,
                    $result['total_records']
                );

                if (count($chunk) < $chunkSize) {
                    continue;
                }

                $this->processChunkData($chunk, $result,$templateId);
                $chunk = [];
            }
            
        }

        if (!empty($chunk)) {
            $this->processChunkData($chunk, $result,$templateId);
        }
    }

    /**
     * Prepare CSV row data.
     */
    private function prepareCsvRow(array $row, array $indexes, int $rowNumber): array
    {
        return [
            'row_num' => $rowNumber,
            'portal_id' => $this->getColumnValue($row, $indexes['portal_id']),
            'name' => $this->getColumnValue($row, $indexes['name'], ''),
        ];
    }

    /**
     * Get column value safely.
     */
    private function getColumnValue(array $row, ?int $index, $default = null)
    {
        return ($index !== null && isset($row[$index]))
            ? trim($row[$index])
            : $default;
    }

    /**
     * Process chunk data for preview.
     */
    private function processChunkData(array $chunk, array &$result,$templateId): void
    {

        $this->processChunkForPreview(
            $chunk,
            $result['preview_rows'],
            $result['matched_count'],
            $result['not_found_count'],
            $result['agency_not_match_count'],
            $result['type_not_match_count'],
            $templateId
        );
    }

    /**
     * Create import log entry.
     */
    private function createImportLog(
        Request $request,
        $user,
        string $originalName,
        string $filePath,
        int $totalRecords
    ) {
        $template = $this->templateService->getDetailsById($request->template_id);

        return $this->esignImportLogService->save([
            'template_id' => $request->template_id,
            'template_name' => $template->template_name ?? '',
            'file_name' => $originalName,
            'file_path' => $filePath,
            'total_records' =>$totalRecords,
            'success_count' => 0,
            'failed_count' => 0,
            'duplicate_count' => 0,
            'status' => 'Pending',
            'created_by' => $user->id,
            'ip_address' => Utility::getIP(),
        ]);
    }

    /**
     * Get patients indexed by portal ID.
     */
    private function getPatientsByPortalIds(array $chunk,$templateId): array
    {
        $portalIds = array_filter(array_column($chunk, 'portal_id'));

        if (empty($portalIds)) {
            return [];
        }

        return $this->patientV2Service->getListByBulkEsign($portalIds);
    }

    /**
     * Prepare preview row data.
     */
    private function preparePreviewRow(array $row, array $patients, array $templateAgencyIds = []): array
    {
        $portalId = $row['portal_id'];
        $patient = $this->findPatient($portalId, $patients);

        $status = 'Not Found';
        $matchType = 'not_found';

        if ($patient !== null) {
            $hasTemplateAgency = !empty($templateAgencyIds) && !empty($templateAgencyIds[0]);
            $patientAgencyId = $patient['agency_id'] ?? null;
            $patientType = $patient['type'] ?? null;
            if ($hasTemplateAgency && $patientAgencyId && !in_array($patientAgencyId, $templateAgencyIds)) {
                $status = 'Agency Not Match';
                $matchType = 'agency_not_match';
            } else {
                if(strtolower($patientType) =='caregiver'){
                    $status = 'Matched';
                    $matchType = 'matched';
                }else{
                    $status = 'Type Not Match';
                    $matchType = 'type_not_match';
                }
                
            }
        }

        return [
            'portal_id' => $portalId ?? 'N/A',
            'name' => $this->resolvePatientName($patient, $row),
            'mobile' => $this->resolvePatientMobile($patient),
            'agency_name' => $patient ? ($patient['agency_name'] ?? 'N/A') : 'N/A',
            'status' => $status,
            'match_type' => $matchType,
        ];
    }

    /**
     * Find patient by portal ID.
     */
    private function findPatient($portalId, array $patients): ?array
    {
        return $portalId ? ($patients[$portalId] ?? null) : null;
    }

    /**
     * Resolve patient name.
     */
    private function resolvePatientName(?array $patient, array $row): string
    {
        if ($patient) {
            return trim($patient['first_name'] . ' ' . $patient['last_name']);
        }

        return $row['name'] ?: 'N/A';
    }

    /**
     * Resolve patient mobile.
     */
    private function resolvePatientMobile(?array $patient): string
    {
        return $patient['mobile'] ?? 'N/A';
    }

    /**
     * Update matched/not found/agency not match counters.
     */
    private function updateMatchCounters(
        string $matchType,
        int &$matchedCount,
        int &$notFoundCount,
        int &$agencyNotMatchCount,
        int &$typeNotMatchCount
    ): void {
        if ($matchType === 'matched') {
            $matchedCount++;
        } elseif ($matchType === 'agency_not_match') {
            $agencyNotMatchCount++;
        } elseif ($matchType === 'type_not_match') {
            $typeNotMatchCount++;
        } else {
            $notFoundCount++;
        }
    }

    /**
     * Add preview row if limit not exceeded.
     */
    private function addPreviewRow(array &$previewRows, array $previewData): void
    {
        if (count($previewRows) >= 100) {
            return;
        }

        unset($previewData['match_type']);

        $previewRows[] = $previewData;
    }

    private function getFile($importLog){

        if (env('FILE_UPLOAD_PERMISSION') == 'development') {
            $filePath = public_path(self::FILE_IMPORT_FOLDER.'/'.$importLog->file_path);
        }else{
            $expiry = Carbon::now()->addMinutes(10);
            $path = self::FILE_IMPORT_FOLDER.'/'.$importLog->file_path;
            $filePath = Storage::disk('s3')->temporaryUrl($path, $expiry);
        }
        
        return $filePath;
    }

    public function syncEsignImport(){
         Artisan::call('esign:send-bulk-sms');

        return response()->json([
            'status' => 'success',
            'message' => 'Sync started successfully'
        ],200);
    }

    private function fetchAgencyDetailsByTemplateId($templateId){
        $getAgencyIdByTemplate = $this->templateService->getDetailsById($templateId);
        return explode(',',$getAgencyIdByTemplate->agency_id);
    }

    private function importLogValidation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'import_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        return true;
    }
}
