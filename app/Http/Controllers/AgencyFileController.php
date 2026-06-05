<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AgencyFileService;
use App\Services\AgencyService;
use App\Services\PatientFileLinkService;
use App\Services\LogsService;
use App\Helpers\Utility;
use App\Models\AgencyFile;
use App\Master;

class AgencyFileController extends Controller
{
    protected $agencyFileService;
    protected $agencyService;
    protected $patientFileLinkService;

    public function __construct(AgencyFileService $agencyFileService, AgencyService $agencyService, PatientFileLinkService $patientFileLinkService)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            // Admin users need 'agency-file-manager' permission
            if ($user->can('agency-file-manager')) {
                return $next($request);
            }
            // Agency users need enable_file_manager enabled on their agency
            if ($user->agency_fk != "") {
                $agency = \App\Agency::where('id', $user->agency_fk)->where('delete_flag', 'N')->first();
                if ($agency && $agency->enable_file_manager == 1) {
                    return $next($request);
                }
            }
            abort(403, 'Access denied. File Manager is not enabled for your agency.');
        });

        $this->agencyFileService = $agencyFileService;
        $this->agencyService = $agencyService;
        $this->patientFileLinkService = $patientFileLinkService;
    }

    /**
     * Resolve agency ID - admin can pass agency_id, agency users use their own
     */
    private function getAgencyId($request = null)
    {
        $user = auth()->user();

        // Super admin (user_type_fk == 184) can select any agency
        if (in_array($user->user_type_fk, ['184', '4']) || $user->role_access == 1) {
            $requestAgencyId = $request ? $request->get('agency_id') : session('fm_agency_id');
            if ($requestAgencyId) {
                session(['fm_agency_id' => $requestAgencyId]);
                return $requestAgencyId;
            }
            // Fallback to session or agency_fk
            return session('fm_agency_id', $user->agency_fk);
        }

        return $user->agency_fk;
    }

    /**
     * Check if current user is a super admin
     */
    private function isSuperAdmin()
    {
        $user = auth()->user();
        return in_array($user->user_type_fk, ['184', '4']) || $user->role_access == 1;
    }

    /**
     * File Manager index page
     */
    public function index(Request $request)
    {
        $data['title'] = 'File Manager';
        $data['isSuperAdmin'] = $this->isSuperAdmin();
        $data['agencies'] = $this->agencyService->getFileManagerAgencies();
        $data['selectedAgencyId'] = $this->getAgencyId($request);
        $data['masterData'] = Master::getAllDataByMasterTypeFk([35]);
        $data['dynamicDocApprovedUsers'] = Utility::dynamicDocumentApproved();
        return view('agency-file-manager.index', $data);
    }

    /**
     * Global all-agencies file listing page — admin only
     * GET /file-manager/all-files
     */
    public function allAgenciesFiles(Request $request)
    {
        $agencies   = $this->agencyService->getFileManagerAgencies();
        $masterData = Master::getAllDataByMasterTypeFk([35]); // MDO source options
        $dynamicDocApprovedUsers = Utility::dynamicDocumentApproved();
        $statusAll=   Utility::getUniqueStatusDataNew();
        return view('agency-file-manager.all-files', [
            'title'                  => 'File Manager — All Files',
            'agencies'               => $agencies,
            'isSuperAdmin'           => $this->isSuperAdmin(),
            'masterData'             => $masterData,
            'dynamicDocApprovedUsers' => $dynamicDocApprovedUsers,
            'statusAll' => $statusAll,
        ]);
    }

    /**
     * AJAX — all files across all agencies
     * GET /file-manager/all-files/data
     */
    public function allAgenciesFilesData(Request $request)
    {
        $user          = auth()->user();
        $canMdo        = true;
        $canTelehealth = true;
        if(auth()->user()->agency_fk == "") {
        $canMdo        = (bool) $user->is_mdo;
        $canTelehealth = (bool) $user->is_telehealth;
        }
        
        $filters = [
            'agency_id'    => $request->get('agency_id'),
            'file_type'    => $request->get('file_type'),
            'uploaded_by'  => $request->get('uploaded_by'),
            'tag'          => $request->get('tag'),
            'linked_chart' => $request->get('linked_chart'),
            'pt_status'    => $request->get('pt_status'),
            'folder_path'  => $request->get('folder_path'),
            'date_range'   => $request->get('date_range'),
        ];
        $result = $this->agencyFileService->listAllAgenciesFiles($request->get('search'), $canMdo, $canTelehealth, $filters);
        return view('agency-file-manager.all-files-table', [
            'files'      => $result['data']['files'],
            'isArchived' => false,
            'search'     => $request->get('search'),
            'filters'    => $filters,
        ]);
    }

    /**
     * AJAX — all archived files across all agencies
     * GET /file-manager/all-files/archived
     */
    public function allAgenciesArchivedData(Request $request)
    {
        $user          = auth()->user();
        $canMdo        = true;
        $canTelehealth = true;
        if(auth()->user()->agency_fk == "") {
        $canMdo        = (bool) $user->is_mdo;
        $canTelehealth = (bool) $user->is_telehealth;
        }
       
        $filters = [
            'agency_id'    => $request->get('agency_id'),
            'file_type'    => $request->get('file_type'),
            'uploaded_by'  => $request->get('uploaded_by'),
            'tag'          => $request->get('tag'),
            'linked_chart' => $request->get('linked_chart'),
            'pt_status'    => $request->get('pt_status'),
            'folder_path'  => $request->get('folder_path'),
            'date_range'   => $request->get('date_range'),
        ];
        $result = $this->agencyFileService->listAllAgenciesArchivedFiles($request->get('search'), $canMdo, $canTelehealth, $filters);
        return view('agency-file-manager.all-files-table', [
            'files'      => $result['data']['files'],
            'isArchived' => true,
            'search'     => $request->get('search'),
            'filters'    => $filters,
        ]);
    }

    /**
     * Export file list as CSV (respects current filters)
     * GET /file-manager/all-files/export
     */
    public function exportCsv(Request $request)
    {
        $user          = auth()->user();
        $canMdo        = true;
        $canTelehealth = true;
        if ($user->agency_fk == "") {
            $canMdo        = (bool) $user->is_mdo;
            $canTelehealth = (bool) $user->is_telehealth;
        }

        $filters = [
            'agency_id'    => $request->get('agency_id'),
            'file_type'    => $request->get('file_type'),
            'uploaded_by'  => $request->get('uploaded_by'),
            'tag'          => $request->get('tag'),
            'linked_chart' => $request->get('linked_chart'),
            'pt_status'    => $request->get('pt_status'),
            'folder_path'  => $request->get('folder_path'),
            'date_range'   => $request->get('date_range'),
        ];

        $isArchived = $request->boolean('archived');

        if ($isArchived) {
            $result = $this->agencyFileService->listAllAgenciesArchivedFilesForExport(
                $request->get('search'),
                $canMdo,
                $canTelehealth,
                $filters
            );
        } else {
            $result = $this->agencyFileService->listAllAgenciesFilesForExport(
                $request->get('search'),
                $canMdo,
                $canTelehealth,
                $filters
            );
        }

        $files    = $result['data']['files'];
        $fileName = ($isArchived ? 'archived_' : '') . 'file_manager_export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($files) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens it correctly
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'File Name','Document Name', 'Agency', 'Path', 'Type', 'Size (KB)', 'Uploaded By', 'Upload Date', 'Tag',
                'Linked Chart', 'Chart ID / Portal ID', 'Full Name', 'Date of Birth', 'Gender', 'Mobile', 'Email',
                'Status / Resolution',
            ]);

            foreach ($files as $file) {
                $tag = '';
                if ($file->is_mdo && $file->is_telehealth) {
                    $tag = 'MDO + Telehealth';
                } elseif ($file->is_mdo) {
                    $tag = 'MDO';
                } elseif ($file->is_telehealth) {
                    $tag = 'Telehealth';
                }

                $patientFullName = $file->patient_id
                    ? trim(($file->pt_first_name ?? '') . ' ' . ($file->pt_last_name ?? ''))
                    : '';

                fputcsv($handle, [
                    $file->file_name,
                     $file->doc_document_name ?? '',
                    $file->agency_name ?? '—',
                    $file->file_path_label ?? 'Root',
                    strtoupper($file->file_type ?? ''),
                    $file->file_size ? round($file->file_size / 1024, 2) : '',
                    $file->uploaded_by ?? '',
                    $file->created_at ? Utility::convertMDYTime($file->created_at) : '',
                    $tag,
                    $file->patient_id ? 'Linked' : 'Not Linked',
                    $file->patient_id ?? '',
                    $patientFullName,
                    $file->patient_id && $file->pt_dob ? Utility::convertMDY($file->pt_dob) : '',
                    $file->pt_gender ?? '',
                    $file->pt_mobile ?? '',
                    $file->pt_email  ?? '',
                    $file->patient_id ? ($file->pt_status ?? '') : '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk download selected files as ZIP (admin all-files context)
     * POST /file-manager/all-files/bulk-download
     */
    public function bulkDownload(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['status' => false, 'message' => 'No files selected'], 400);
        }

        // Cap at 50 files per request
        $ids = array_slice(array_map('intval', $ids), 0, 50);

        $result = $this->agencyFileService->bulkDownloadZip($ids);

        if (!$result['status']) {
            return response()->json($result, 422);
        }

        $tmpPath  = $result['path'];
        $zipName  = 'files_' . date('Ymd_His') . '.zip';

        return response()->download($tmpPath, $zipName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Bulk download for single-agency table view
     * POST /file-manager/files/bulk-download
     */
    public function bulkDownloadAgency(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['status' => false, 'message' => 'No files selected'], 400);
        }

        $ids = array_slice(array_map('intval', $ids), 0, 50);

        $result = $this->agencyFileService->bulkDownloadZip($ids);

        if (!$result['status']) {
            return response()->json($result, 422);
        }

        return response()->download($result['path'], 'files_' . date('Ymd_His') . '.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Create a new folder
     * POST /agency/folder/create
     */
public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
        ]);

        $agencyId = $this->getAgencyId($request);
        if (!$agencyId) {
            return response()->json(['status' => false, 'message' => 'Please select an agency first'], 400);
        }

        $result = $this->agencyFileService->createFolder(
            $agencyId,
            $request->name,
            $request->parent_id,
            $request->input('is_mdo', 0),
            $request->input('is_telehealth', 0)
        );

        $statusCode = $result['status'] ? 200 : 400;

        if ($result['status']) {
            LogsService::save([
                'type' => 'Create Folder',
                'link' => url('/file-manager'),
                'module' => 'File Manager',
                'object_id' => $result['data']->id ?? 0,
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' created folder: ' . $request->name,
                'ip' => Utility::getIP(),
            ]);
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Upload file(s)
     * POST /agency/file/upload
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'files'     => 'required|array|max:120',
            'files.*'   => 'file',
            'folder_id' => 'nullable|integer',
        ]);

        // Total size check — 500MB across all files combined
        $totalSize    = 0;
        $maxTotalSize = 524288000; // 500MB in bytes
        foreach ($request->file('files') as $file) {
            $totalSize += $file->getSize();
        }
        if ($totalSize > $maxTotalSize) {
            return response()->json([
                'status'  => false,
                'message' => 'Total upload size exceeds the 500MB limit.',
            ], 422);
        }

        $agencyId = $this->getAgencyId($request);
        if (!$agencyId) {
            return response()->json(['status' => false, 'message' => 'Please select an agency first'], 400);
        }

        $uploadedFiles = $request->file('files');
        $results = [];
        $successCount = 0;
        $failCount = 0;
        $lastUploadedFile = null;

        foreach ($uploadedFiles as $file) {
            $result = $this->agencyFileService->uploadFile(
                $agencyId,
                $file,
                $request->folder_id,
                false  // suppress per-file notification; send one after all uploads
            );

            if ($result['status']) {
                $successCount++;
                $lastUploadedFile = $result['data'];
                LogsService::save([
                    'type' => 'Upload File',
                    'link' => url('/file-manager'),
                    'module' => 'File Manager',
                    'object_id' => $result['data']->id ?? 0,
                    'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' uploaded file: ' . $file->getClientOriginalName(),
                    'ip' => Utility::getIP(),
                ]);
            } else {
                $failCount++;
            }

            $results[] = [
                'file_name' => $file->getClientOriginalName(),
                'status' => $result['status'],
                'message' => $result['message'],
            ];
        }

        // Send one notification after all files are uploaded
        if ($lastUploadedFile) {
            $this->agencyFileService->sendUploadNotificationPublic($lastUploadedFile, $agencyId, $request->folder_id, auth()->user(), $successCount);
        }

        $message = $successCount . ' file(s) uploaded successfully';
        if ($failCount > 0) {
            $message .= ', ' . $failCount . ' file(s) failed';
        }

        return response()->json([
            'status' => $successCount > 0,
            'message' => $message,
            'data' => $results,
        ], $successCount > 0 ? 200 : 400);
    }

    /**
     * List files and folders
     * GET /agency/files
     */
    public function listFiles(Request $request)
    {
        $agencyId = $this->getAgencyId($request);
        $folderId = $request->get('folder_id', null);
        $search   = $request->get('search', null);

        // Agency users see all folders — MDO/Telehealth restriction applies to admin users only
            $canMdo        = true;
            $canTelehealth = true;
        if (auth()->user()->agency_fk == "") {
            $canMdo        = (bool) auth()->user()->is_mdo;
            $canTelehealth = (bool) auth()->user()->is_telehealth;
        }

        $result = $this->agencyFileService->listContents($agencyId, $folderId, $search, $canMdo, $canTelehealth);

        return response()->json($result, 200);
    }

    /**
     * Delete (archive) a file
     * DELETE /agency/file/{id}
     */
    public function deleteFile(Request $request, $id)
    {
        $agencyId = $this->getAgencyId($request);

        $result = $this->agencyFileService->deleteFile($agencyId, $id);

        $statusCode = $result['status'] ? 200 : 404;

        if ($result['status']) {
            LogsService::save([
                'type' => 'Archive File',
                'link' => url('/file-manager'),
                'module' => 'File Manager',
                'object_id' => $id,
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' archived file: ' . ($result['name'] ?? $id),
                'ip' => Utility::getIP(),
            ]);
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Delete (archive) a folder
     * DELETE /agency/folder/{id}
     */
    public function deleteFolder(Request $request, $id)
    {
        $agencyId = $this->getAgencyId($request);

        $result = $this->agencyFileService->deleteFolder($agencyId, $id);

        $statusCode = $result['status'] ? 200 : 404;

        if ($result['status']) {
            LogsService::save([
                'type' => 'Archive Folder',
                'link' => url('/file-manager'),
                'module' => 'File Manager',
                'object_id' => $id,
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' archived folder: ' . ($result['name'] ?? $id),
                'ip' => Utility::getIP(),
            ]);
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Rename file or folder
     * PUT /agency/file/rename
     */
    public function rename(Request $request)
    {
        $request->validate([
            'type' => 'required|in:file,folder',
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        $agencyId = $this->getAgencyId($request);

        $result = $this->agencyFileService->rename(
            $agencyId,
            $request->type,
            $request->id,
            $request->name
        );

        $statusCode = $result['status'] ? 200 : 400;

        if ($result['status']) {
            $typeLabel = ucfirst($request->type);
            LogsService::save([
                'type' => 'Rename ' . $typeLabel,
                'link' => url('/file-manager'),
                'module' => 'File Manager',
                'object_id' => $request->id,
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' renamed ' . $request->type . ': "' . ($result['old_name'] ?? '') . '" to "' . ($result['new_name'] ?? $request->name) . '"',
                'ip' => Utility::getIP(),
            ]);
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Download file - streams directly from storage
     * GET /agency/file/download/{id}
     */
    public function downloadFile(Request $request, $id)
    {
        $agencyId = $this->getAgencyId($request);

        $result = $this->agencyFileService->getFileForDownload($agencyId, $id);

        if (!$result['status']) {
            return response()->json($result, 404);
        }

        $file = $result['data'];
        $contentType = $this->getMimeType($file['file_type']);

        return response($file['content'])
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'attachment; filename="' . $file['file_name'] . '"')
            ->header('Content-Length', strlen($file['content']));
    }

    private function getMimeType($extension)
    {
        $mimes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        return $mimes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * Preview file (signed URL for image/pdf)
     * GET /agency/file/preview/{id}
     */
    public function previewFile(Request $request, $id)
    {
        $agencyId = $this->getAgencyId($request);

        $result = $this->agencyFileService->getPreviewUrl($agencyId, $id);

        $statusCode = $result['status'] ? 200 : 404;

        return response()->json($result, $statusCode);
    }

    /**
     * Get folder tree
     * GET /agency/folder/tree
     */
    public function folderTree(Request $request)
    {
        $agencyId = $this->getAgencyId($request);
            $canMdo        = true;
            $canTelehealth = true;
        if (auth()->user()->agency_fk == "") {
            $canMdo        = (bool) auth()->user()->is_mdo;
            $canTelehealth = (bool) auth()->user()->is_telehealth;
        }

        $result = $this->agencyFileService->getFolderTree($agencyId, $canMdo, $canTelehealth);

        return response()->json($result, 200);
    }

    /**
     * List ALL files across all folders for table view
     * GET /file-manager/files/all
     */
    public function listAllFiles(Request $request)
    {
        $agencyId = $this->getAgencyId($request);
        $search   = $request->get('search', null);
        $user = auth()->user();
        if ($user->agency_fk == "") {
            $canMdo        = (bool) auth()->user()->is_mdo;
            $canTelehealth = (bool) auth()->user()->is_telehealth;
        } else {
            $canMdo        = true;
            $canTelehealth = true;
        }

        $result = $this->agencyFileService->listAllFiles($agencyId, $search, $canMdo, $canTelehealth);

        return view('agency-file-manager.table-view', [
            'files'    => $result['data']['files'],
            'search'   => $search,
            'agencyId' => $agencyId,
        ]);
    }

    /**
     * List archived files and folders
     * GET /agency/file/archived
     */
    public function archivedList(Request $request)
    {
        $agencyId = $this->getAgencyId($request);
        $search   = $request->get('search', null);

        $result = $this->agencyFileService->listArchived($agencyId, $search);

        return response()->json($result, 200);
    }

    /**
     * List contents inside an archived folder
     * GET /file-manager/folder/archived/{id}
     */
    public function archivedFolderContents(Request $request, $id)
    {
        $agencyId = $this->getAgencyId($request);
        $search   = $request->get('search', null);

        $result = $this->agencyFileService->listArchivedFolder($agencyId, $id, $search);

        return response()->json($result, $result['status'] ? 200 : 404);
    }

    /**
     * Restore an archived file
     * POST /agency/file/restore/{id}
     */
    public function restoreFile(Request $request, $id)
    {
        $agencyId = $this->getAgencyId($request);

        $result = $this->agencyFileService->restoreFile($agencyId, $id);

        $statusCode = $result['status'] ? 200 : 404;

        if ($result['status']) {
            LogsService::save([
                'type' => 'Restore File',
                'link' => url('/file-manager'),
                'module' => 'File Manager',
                'object_id' => $id,
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' restored file: ' . ($result['name'] ?? $id),
                'ip' => Utility::getIP(),
            ]);
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Link a file to a patient
     * POST /file-manager/file/{id}/link-patient
     */
    public function linkPatient(Request $request, $id)
    {
        $request->validate(['patient_id' => 'required|integer']);

        $result = $this->patientFileLinkService->linkPatient((int) $id, (int) $request->patient_id, auth()->id());

        if (!$result['status']) {
            return response()->json($result, 404);
        }

        LogsService::save([
            'type'      => 'Link File to Patient',
            'link'      => url('/file-manager/file/') . '/' . $id. '/linked-patients',
            'module'    => 'File Manager',
            'object_id' => $id,
            'message'   => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' linked file "' . $result['file_name'] . '" to patient ID ' . $request->patient_id,
            'ip'        => Utility::getIP(),
        ]);

        return response()->json(['status' => true, 'message' => 'File linked to patient successfully']);
    }

    /**
     * Unlink a file from a patient
     * DELETE /file-manager/file/{id}/unlink-patient/{patientId}
     */
    public function unlinkPatient(Request $request, $id, $patientId)
    {
        $result = $this->patientFileLinkService->unlinkPatient((int) $id, (int) $patientId);

        if (!$result['status']) {
            return response()->json($result, 404);
        }

        LogsService::save([
            'type'      => 'Unlink File from Patient',
            'link'      => url('/file-manager/file/') . '/' . $id,
            'module'    => 'File Manager',
            'object_id' => $id,
            'message'   => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' unlinked file "' . ($result['file_name'] ?? $id) . '" from patient ID ' . $patientId,
            'ip'        => Utility::getIP(),
        ]);

        return response()->json(['status' => true, 'message' => 'File unlinked successfully']);
    }

    /**
     * Get patients linked to a file
     * GET /file-manager/file/{id}/linked-patients
     */
    public function getLinkedPatients(Request $request, $id)
    {
        $result = $this->patientFileLinkService->getLinkedPatients((int) $id);

        return response()->json($result);
    }

    /**
     * Get files linked to a patient
     * GET /file-manager/patient/{patientId}/files
     */
    public function getPatientFiles(Request $request, $patientId)
    {
        $result = $this->patientFileLinkService->getPatientFiles((int) $patientId);

        return response()->json($result);
    }

    /**
     * Add a file manager file to a patient's Document Section
     * POST /file-manager/file/{id}/add-to-chart
     */
    public function addToChart(Request $request, $id)
    {
        $request->validate([
            'patient_id'    => 'required|integer',
            'document_name' => 'required|string|max:255',
        ]);

        $file = AgencyFile::withTrashed()->find($id);
        if (!$file) {
            return response()->json(['status' => false, 'message' => 'File not found'], 404);
        }

        $result = $this->patientFileLinkService->addToPatientChart($file, $request->all());

        if (!$result['status']) {
            return response()->json($result, 422);
        }

        $auth = auth()->user();
        $ip   = Utility::getIP();

        LogsService::save([
            'type'      => 'Add File to Chart',
            'link'      => url('/file-manager/file/') . '/' . $id. '/add-to-chart',
            'module'    => 'File Manager',
            'object_id' => $id,
            'message'   => $auth->first_name . ' ' . $auth->last_name . ' added file "' . $file->file_name . '" to Chart ID ' . $request->patient_id . ' chart',
            'ip'        => $ip,
        ]);

        // Also log to Patient Appointment so it appears in the patient's Appointment Logs section
        LogsService::save([
            'type'      => 'Add Document From File Manager',
            'link'      => url('/patient/view/') . '/' . $request->patient_id,
            'module'    => 'Patient Appointment',
            'object_id' => $request->patient_id,
            'message'   => $auth->first_name . ' ' . $auth->last_name . ' has Add Document From File Manager',
            'ip'        => $ip,
            'new_response' => serialize($request->all()),
        ]);

        return response()->json($result);
    }

    /**
     * Restore an archived folder
     * POST /agency/folder/restore/{id}
     */
    public function restoreFolder(Request $request, $id)
    {
        $agencyId = $this->getAgencyId($request);

        $result = $this->agencyFileService->restoreFolder($agencyId, $id);

        $statusCode = $result['status'] ? 200 : 404;

        if ($result['status']) {
            LogsService::save([
                'type' => 'Restore Folder',
                'link' => url('/file-manager'),
                'module' => 'File Manager',
                'object_id' => $id,
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' restored folder: ' . ($result['name'] ?? $id),
                'ip' => Utility::getIP(),
            ]);
        }

        return response()->json($result, $statusCode);
    }
}
