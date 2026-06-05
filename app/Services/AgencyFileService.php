<?php

namespace App\Services;

use App\Models\AgencyFolder;
use App\Models\AgencyFile;
use App\Mail\FileUploadNotification;
use App\SiteSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use App\Helpers\Utility;

class AgencyFileService
{
    protected $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'xlsx', 'zip', 'rar'];
    protected $maxFileSize = 262144000; // 250MB in bytes

    /**
     * Check if storage is local (development) or S3 (production)
     */
    protected function isLocalStorage()
    {
        return env('FILE_UPLOAD_PERMISSION') == 'development';
    }

    /**
     * Create a new folder
     */
    public function createFolder($agencyId, $name, $parentId = null, $isMdo = 0, $isTelehealth = 0)
    {
        $auth = auth()->user();

        // Validate parent folder belongs to same agency
        if ($parentId) {
            $parentFolder = AgencyFolder::forAgency($agencyId)->find($parentId);
            if (!$parentFolder) {
                return ['status' => false, 'message' => 'Parent folder not found'];
            }
        }

        // Check duplicate folder name at same level
        $exists = AgencyFolder::forAgency($agencyId)
            ->where('parent_id', $parentId)
            ->where('name', $name)
            ->exists();

        if ($exists) {
            return ['status' => false, 'message' => 'A folder with this name already exists'];
        }

        $folder = AgencyFolder::create([
            'agency_id'    => $agencyId,
            'parent_id'    => $parentId,
            'name'         => $name,
            'is_mdo'       => $isMdo ? 1 : 0,
            'is_telehealth'=> $isTelehealth ? 1 : 0,
            'created_by'   => $auth->id,
        ]);

        return ['status' => true, 'message' => 'Folder created successfully', 'data' => $folder];
    }

    /**
     * Upload file to S3
     */
    public function uploadFile($agencyId, UploadedFile $file, $folderId = null, $sendNotification = true)
    {
        $auth = auth()->user();

        // Validate folder belongs to agency
        if ($folderId) {
            $folder = AgencyFolder::forAgency($agencyId)->find($folderId);
            if (!$folder) {
                return ['status' => false, 'message' => 'Folder not found'];
            }
        }

        $extension = strtolower($file->getClientOriginalExtension());

        // Validate file type
        if (!in_array($extension, $this->allowedTypes)) {
            return ['status' => false, 'message' => 'File type not allowed. Allowed: ' . implode(', ', $this->allowedTypes)];
        }

        // Validate file size
        if ($file->getSize() > $this->maxFileSize) {
            return ['status' => false, 'message' => 'File size exceeds the maximum limit of 250MB'];
        }

        try {
            $originalName = $file->getClientOriginalName();
            $folderPath = $folderId ? $folderId : 'root';
            $path = "agency/{$agencyId}/folders/{$folderPath}/{$originalName}";

            // Check if file with same name exists
            $existingFile = AgencyFile::forAgency($agencyId)
                ->where('folder_id', $folderId)
                ->where('file_name', $originalName)
                ->first();

            if ($existingFile) {
                // Append timestamp to avoid overwrite
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $originalName = $nameWithoutExt . '_' . time() . '.' . $extension;
                $path = "agency/{$agencyId}/folders/{$folderPath}/{$originalName}";
            }

            if ($this->isLocalStorage()) {
                $destination = public_path("agency/{$agencyId}/folders/{$folderPath}");
                if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
                }
                $file->move($destination, $originalName);
            } else {
                Storage::disk('s3')->put($path, file_get_contents($file));
            }

            $agencyFile = AgencyFile::create([
                'agency_id' => $agencyId,
                'folder_id' => $folderId,
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $file->getSize(),
                'created_by' => $auth->id,
            ]);

            // Send MDO / Telehealth upload notification email if applicable
            if ($sendNotification) {
                $this->sendUploadNotification($agencyFile, $agencyId, $folderId, $auth);
            }

            return ['status' => true, 'message' => 'File uploaded successfully', 'data' => $agencyFile];

        } catch (\Exception $e) {
            Log::error('Agency File Upload Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to upload file. Please try again.'];
        }
    }

    /**
     * Public wrapper — sends one notification after a multi-file upload batch
     */
    public function sendUploadNotificationPublic($agencyFile, $agencyId, $folderId, $auth, $fileCount = 1)
    {
        $this->sendUploadNotification($agencyFile, $agencyId, $folderId, $auth, $fileCount);
    }

    /**
     * Send MDO / Telehealth upload notification email based on folder type
     */
    private function sendUploadNotification($agencyFile, $agencyId, $folderId, $auth, $fileCount = 1)
    {
        if (!$folderId) return;

        $folder = AgencyFolder::find($folderId);
        if (!$folder) return;

        // Determine type
        $folderType   = null;
        $emailColumn  = null;
        if ($folder->is_mdo) {
            $folderType  = 'MDO';
            $emailColumn = 'mdo_upload_notify_email';
        } elseif ($folder->is_telehealth) {
            $folderType  = 'Telehealth';
            $emailColumn = 'telehealth_upload_notify_email';
        }

        if (!$folderType) return;

        $setting = SiteSetting::where('del_flag', 'N')->first();
        if (!$setting || empty($setting->$emailColumn)) return;

        $emails = array_filter(array_map('trim', explode(',', $setting->$emailColumn)));
        if (empty($emails)) return;

        // Build folder label
        $breadcrumb  = $folder->breadcrumb;
        $folderPath  = implode(' / ', array_column($breadcrumb, 'name'));

        // Agency name
        $agency     = \App\Agency::find($agencyId);
        $agencyName = $agency ? $agency->agency_name : '—';

        // Uploader name
        $uploaderName = trim($auth->first_name . ' ' . $auth->last_name);

        try {
            foreach ($emails as $email) {
                Mail::to($email)->send(new FileUploadNotification(
                    $agencyFile,
                    $folderType,
                    $agencyName,
                    $uploaderName,
                    $folderPath,
                    $fileCount
                ));
            }
        } catch (\Exception $e) {
            Log::error('File Upload Notification Email Error: ' . $e->getMessage());
        }
    }

    /**
     * List files and folders for a given agency and parent folder
     */
    public function listContents($agencyId, $folderId = null, $search = null, $canMdo = false, $canTelehealth = false)
    {
        $folderQuery = AgencyFolder::forAgency($agencyId);
        $fileQuery   = AgencyFile::select('agency_files.*','patient_file_links.patient_id')->forAgency($agencyId)->leftJoin('patient_file_links', 'patient_file_links.agency_file_id', '=', 'agency_files.id');;

        if ($search) {
            $folderQuery->where('name', 'LIKE', '%' . $search . '%');
            $fileQuery->where('file_name', 'LIKE', '%' . $search . '%');
        } else {
            $folderQuery->where('parent_id', $folderId);
            $fileQuery->where('folder_id', $folderId);
        }

        // Hide MDO/Telehealth folders unless user has at least one matching permission.
        // A dual-flagged folder (is_mdo=1 AND is_telehealth=1) is visible if user has EITHER permission.
        if (!$canMdo || !$canTelehealth) {
            $folderQuery->where(function ($q) use ($canMdo, $canTelehealth) {
                if (!$canMdo && !$canTelehealth) {
                    // User has neither — hide any folder with either flag set
                    $q->where('is_mdo', 0)->where('is_telehealth', 0);
                } elseif (!$canMdo) {
                    // User has Telehealth only — hide MDO-only folders (is_mdo=1 AND is_telehealth=0)
                    $q->where(function ($q2) {
                        $q2->where('is_mdo', 0)->orWhere('is_telehealth', 1);
                    });
                } else {
                    // User has MDO only — hide Telehealth-only folders (is_telehealth=1 AND is_mdo=0)
                    $q->where(function ($q2) {
                        $q2->where('is_telehealth', 0)->orWhere('is_mdo', 1);
                    });
                }
            });

            // Same logic for files: hide files whose folder is off-limits
            $excludedFolderIds = AgencyFolder::forAgency($agencyId)
                ->where(function ($q) use ($canMdo, $canTelehealth) {
                    if (!$canMdo && !$canTelehealth) {
                        $q->where(function ($q2) {
                            $q2->where('is_mdo', 1)->orWhere('is_telehealth', 1);
                        });
                    } elseif (!$canMdo) {
                        $q->where('is_mdo', 1)->where('is_telehealth', 0);
                    } else {
                        $q->where('is_telehealth', 1)->where('is_mdo', 0);
                    }
                })->pluck('id');

            if ($excludedFolderIds->isNotEmpty()) {
                $fileQuery->whereNotIn('folder_id', $excludedFolderIds);
            }
        }

        $folders = $folderQuery->orderBy('name', 'asc')->get();
        $files   = $fileQuery->with('createdBy')->orderBy('created_at', 'desc')->get();

        // Append uploader name to each file
        $files->each(function ($file) {
            $file->uploaded_by = $file->createdBy
                ? trim($file->createdBy->first_name . ' ' . $file->createdBy->last_name)
                : null;
        });

        $breadcrumb = [];
        if ($folderId) {
            $currentFolder = AgencyFolder::forAgency($agencyId)->find($folderId);
            if ($currentFolder) {
                $breadcrumb = $currentFolder->breadcrumb;
            }
        }

        return [
            'status' => true,
            'data' => [
                'folders' => $folders,
                'files'   => $files,
                'breadcrumb' => $breadcrumb,
                'current_folder_id' => $folderId,
            ]
        ];
    }

    /**
     * List ALL files across ALL agencies for the global listing page
     */
    public function listAllAgenciesFiles($search = null, $canMdo = true, $canTelehealth = true, $filters = [])
    {
        $agencyids = Utility::getUserWiseAgency();

        if(auth()->user()->agency_fk !=""){
            $agencyids[] = auth()->user()->agency_fk;
        }

        $fileQuery = AgencyFile::select(
                'agency_files.*',
                'patient_file_links.patient_id',
                'patient_master.status as pt_status')
            ->with('createdBy', 'folder', 'agency')
            ->leftJoin('users', 'users.id', '=', 'agency_files.created_by')
            ->leftJoin('patient_file_links', 'patient_file_links.agency_file_id', '=', 'agency_files.id')
            ->leftJoin('patient_master', 'patient_master.id', '=', 'patient_file_links.patient_id')
            ->leftJoin('agency', 'agency.id', '=', 'agency_files.agency_id')->where('agency.enable_file_manager', 1);

        // Agency users only see their own agency's files
        if (!empty($agencyids)) {
            $fileQuery->whereIn('agency_files.agency_id', $agencyids);
        }

        if ($search) {
            $fileQuery->where('agency_files.file_name', 'LIKE', '%' . $search . '%');
        }

        // Additional filters
        if (!empty($filters['agency_id'])) {
            $fileQuery->where('agency_files.agency_id', $filters['agency_id']);
        }
        if (!empty($filters['file_type'])) {
            $fileQuery->where('agency_files.file_type', $filters['file_type']);
        }
        if (!empty($filters['uploaded_by'])) {
            $fileQuery->where(function ($q) use ($filters) {
                $q->where('users.first_name', 'LIKE', '%' . $filters['uploaded_by'] . '%')
                  ->orWhere('users.last_name',  'LIKE', '%' . $filters['uploaded_by'] . '%');
            });
        }
        if (!empty($filters['date_range'])) {
            $range = Utility::convertMdyToYmd($filters['date_range']);
            if ($range['from']) $fileQuery->where('agency_files.created_at', '>=', $range['from']);
            if ($range['to'])   $fileQuery->where('agency_files.created_at', '<=', $range['to']);
        }
        if (!empty($filters['tag'])) {
            if ($filters['tag'] === 'mdo') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_mdo', 1)->where('is_telehealth', 0));
            } elseif ($filters['tag'] === 'telehealth') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_telehealth', 1)->where('is_mdo', 0));
            } elseif ($filters['tag'] === 'both') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_mdo', 1)->where('is_telehealth', 1));
            }
        }
        if (!empty($filters['linked_chart'])) {
            if ($filters['linked_chart'] === 'linked') {
                $fileQuery->whereNotNull('patient_file_links.patient_id');
            } elseif ($filters['linked_chart'] === 'not_linked') {
                $fileQuery->whereNull('patient_file_links.patient_id');
            }
        }
        if (!empty($filters['pt_status'])) {
            $fileQuery->where('patient_master.status', $filters['pt_status']);
        }
        if (!empty($filters['folder_path'])) {
            $folderIds = AgencyFolder::where('name', 'LIKE', '%' . $filters['folder_path'] . '%')->pluck('id');
            $fileQuery->whereIn('agency_files.folder_id', $folderIds);
        }

        // Hide files in MDO-only folders unless user has MDO permission
        // (folders with both is_mdo=1 AND is_telehealth=1 are visible to either permission)
        if (!$canMdo && !$canTelehealth) {
            $restrictedFolderIds = AgencyFolder::where(function ($q) {
                $q->where('is_mdo', 1)->orWhere('is_telehealth', 1);
            })->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $restrictedFolderIds);
        } elseif (!$canMdo) {
            // Exclude purely MDO folders (not telehealth)
            $mdoOnlyFolderIds = AgencyFolder::where('is_mdo', 1)->where('is_telehealth', 0)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $mdoOnlyFolderIds);
        } elseif (!$canTelehealth) {
            // Exclude purely Telehealth folders (not MDO)
            $telehealthOnlyFolderIds = AgencyFolder::where('is_telehealth', 1)->where('is_mdo', 0)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $telehealthOnlyFolderIds);
        }

        $paginator = $fileQuery->orderBy('agency_files.created_at', 'desc')->paginate(50);

        $paginator->each(function ($file) {
            $file->uploaded_by = $file->createdBy
                ? trim($file->createdBy->first_name . ' ' . $file->createdBy->last_name)
                : null;

            if ($file->folder) {
                $breadcrumb = $file->folder->breadcrumb;
                $file->file_path_label = implode(' / ', array_column($breadcrumb, 'name'));
                $file->is_mdo          = $file->folder->is_mdo;
                $file->is_telehealth   = $file->folder->is_telehealth;
            } else {
                $file->file_path_label = 'Root';
                $file->is_mdo          = 0;
                $file->is_telehealth   = 0;
            }

            $file->agency_name = $file->agency ? $file->agency->agency_name : '—';
        });

        return [
            'status' => true,
            'data'   => ['files' => $paginator],
        ];
    }

    /**
     * Same as listAllAgenciesFiles but returns all records (no pagination) for CSV export
     */
    public function listAllAgenciesFilesForExport($search = null, $canMdo = true, $canTelehealth = true, $filters = [])
    {
        $agencyids = Utility::getUserWiseAgency();

        if (auth()->user()->agency_fk != "") {
            $agencyids[] = auth()->user()->agency_fk;
        }

        $fileQuery = AgencyFile::select(
                'agency_files.*',
                'patient_file_links.patient_id',
                'patient_master.first_name as pt_first_name',
                'patient_master.last_name as pt_last_name',
                'patient_master.dob as pt_dob',
                'patient_master.gender as pt_gender',
                'patient_master.mobile as pt_mobile',
                'patient_master.email as pt_email',
                'patient_master.status as pt_status',
                'document_patient.document_name as doc_document_name'
            )
            ->with('createdBy', 'folder', 'agency')
            ->leftJoin('users', 'users.id', '=', 'agency_files.created_by')
            ->leftJoin('patient_file_links', 'patient_file_links.agency_file_id', '=', 'agency_files.id')
            ->leftJoin('patient_master', 'patient_master.id', '=', 'patient_file_links.patient_id')
            ->leftJoin('document_patient', 'document_patient.id', '=', 'patient_file_links.document_id')->leftJoin('agency', 'agency.id', '=', 'agency_files.agency_id')->where('agency.enable_file_manager', 1);

        if (!empty($agencyids)) {
            $fileQuery->whereIn('agency_files.agency_id', $agencyids);
        }

        if ($search) {
            $fileQuery->where('agency_files.file_name', 'LIKE', '%' . $search . '%');
        }

        if (!empty($filters['agency_id'])) {
            $fileQuery->where('agency_files.agency_id', $filters['agency_id']);
        }
        if (!empty($filters['file_type'])) {
            $fileQuery->where('agency_files.file_type', $filters['file_type']);
        }
        if (!empty($filters['uploaded_by'])) {
            $fileQuery->where(function ($q) use ($filters) {
                $q->where('users.first_name', 'LIKE', '%' . $filters['uploaded_by'] . '%')
                  ->orWhere('users.last_name',  'LIKE', '%' . $filters['uploaded_by'] . '%');
            });
        }
        if (!empty($filters['date_range'])) {
            $range = Utility::convertMdyToYmd($filters['date_range']);
            if ($range['from']) $fileQuery->where('agency_files.created_at', '>=', $range['from']);
            if ($range['to'])   $fileQuery->where('agency_files.created_at', '<=', $range['to']);
        }
        if (!empty($filters['tag'])) {
            if ($filters['tag'] === 'mdo') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_mdo', 1)->where('is_telehealth', 0));
            } elseif ($filters['tag'] === 'telehealth') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_telehealth', 1)->where('is_mdo', 0));
            } elseif ($filters['tag'] === 'both') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_mdo', 1)->where('is_telehealth', 1));
            }
        }
        if (!empty($filters['linked_chart'])) {
            if ($filters['linked_chart'] === 'linked') {
                $fileQuery->whereNotNull('patient_file_links.patient_id');
            } elseif ($filters['linked_chart'] === 'not_linked') {
                $fileQuery->whereNull('patient_file_links.patient_id');
            }
        }
        if (!empty($filters['pt_status'])) {
            $fileQuery->where('patient_master.status', $filters['pt_status']);
        }
        if (!empty($filters['folder_path'])) {
            $folderIds = AgencyFolder::where('name', 'LIKE', '%' . $filters['folder_path'] . '%')->pluck('id');
            $fileQuery->whereIn('agency_files.folder_id', $folderIds);
        }

        if (!$canMdo && !$canTelehealth) {
            $restrictedFolderIds = AgencyFolder::where(function ($q) {
                $q->where('is_mdo', 1)->orWhere('is_telehealth', 1);
            })->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $restrictedFolderIds);
        } elseif (!$canMdo) {
            $mdoOnlyFolderIds = AgencyFolder::where('is_mdo', 1)->where('is_telehealth', 0)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $mdoOnlyFolderIds);
        } elseif (!$canTelehealth) {
            $telehealthOnlyFolderIds = AgencyFolder::where('is_telehealth', 1)->where('is_mdo', 0)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $telehealthOnlyFolderIds);
        }

        $files = $fileQuery->orderBy('agency_files.created_at', 'desc')->get();

        $files->each(function ($file) {
            $file->uploaded_by = $file->createdBy
                ? trim($file->createdBy->first_name . ' ' . $file->createdBy->last_name)
                : null;

            if ($file->folder) {
                $breadcrumb = $file->folder->breadcrumb;
                $file->file_path_label = implode(' / ', array_column($breadcrumb, 'name'));
                $file->is_mdo          = $file->folder->is_mdo;
                $file->is_telehealth   = $file->folder->is_telehealth;
            } else {
                $file->file_path_label = 'Root';
                $file->is_mdo          = 0;
                $file->is_telehealth   = 0;
            }

            $file->agency_name = $file->agency ? $file->agency->agency_name : '—';
        });

        return [
            'status' => true,
            'data'   => ['files' => $files],
        ];
    }

    /**
     * List ALL files across all folders for table view (with path)
     */
    public function listAllFiles($agencyId, $search = null, $canMdo = false, $canTelehealth = false)
    {
        $fileQuery = AgencyFile::select('agency_files.*','patient_file_links.patient_id','patient_master.status as pt_status')
            ->forAgency($agencyId)->with('createdBy', 'folder')
            ->leftJoin('patient_file_links', 'patient_file_links.agency_file_id', '=', 'agency_files.id')
            ->leftJoin('patient_master', 'patient_master.id', '=', 'patient_file_links.patient_id');

        if ($search) {
            $fileQuery->where('file_name', 'LIKE', '%' . $search . '%');
        }

        // Hide files in MDO folders unless user has permission
        if (!$canMdo) {
            $mdoFolderIds = AgencyFolder::forAgency($agencyId)->where('is_mdo', 1)->pluck('id');
            $fileQuery->whereNotIn('folder_id', $mdoFolderIds);
        }

        // Hide files in Telehealth folders unless user has permission
        if (!$canTelehealth) {
            $telehealthFolderIds = AgencyFolder::forAgency($agencyId)->where('is_telehealth', 1)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $telehealthFolderIds);
        }

        $paginator = $fileQuery->orderBy('agency_files.created_at', 'desc')->paginate(50);

        $paginator->each(function ($file) {
            $file->uploaded_by = $file->createdBy
                ? trim($file->createdBy->first_name . ' ' . $file->createdBy->last_name)
                : null;

            if ($file->folder) {
                $breadcrumb = $file->folder->breadcrumb;
                $file->file_path_label = implode(' / ', array_column($breadcrumb, 'name'));
                $file->is_mdo        = $file->folder->is_mdo;
                $file->is_telehealth = $file->folder->is_telehealth;
            } else {
                $file->file_path_label = 'Root';
                $file->is_mdo        = 0;
                $file->is_telehealth = 0;
            }
        });

        return [
            'status' => true,
            'data'   => ['files' => $paginator],
        ];
    }

    /**
     * Rename a file or folder
     */
    public function rename($agencyId, $type, $id, $newName)
    {
        $auth = auth()->user();

        if ($type === 'folder') {
            $item = AgencyFolder::forAgency($agencyId)->find($id);
            if (!$item) {
                return ['status' => false, 'message' => 'Folder not found'];
            }

            // Check duplicate at same level
            $exists = AgencyFolder::forAgency($agencyId)
                ->where('parent_id', $item->parent_id)
                ->where('name', $newName)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return ['status' => false, 'message' => 'A folder with this name already exists'];
            }

            $oldName = $item->name;
            $item->update(['name' => $newName, 'updated_by' => $auth->id]);
            return ['status' => true, 'message' => 'Folder renamed successfully', 'old_name' => $oldName, 'new_name' => $newName];

        } elseif ($type === 'file') {
            $item = AgencyFile::forAgency($agencyId)->find($id);
            if (!$item) {
                return ['status' => false, 'message' => 'File not found'];
            }

            // Keep the original extension
            $extension = pathinfo($item->file_name, PATHINFO_EXTENSION);
            $newFileName = pathinfo($newName, PATHINFO_EXTENSION)
                ? $newName
                : $newName . '.' . $extension;

            $oldFileName = $item->file_name;

            try {
                $oldPath = $item->file_path;
                $newPath = dirname($oldPath) . '/' . $newFileName;

                if ($this->isLocalStorage()) {
                    $oldFullPath = public_path($oldPath);
                    $newFullPath = public_path($newPath);
                    if (file_exists($oldFullPath)) {
                        rename($oldFullPath, $newFullPath);
                    }
                } else {
                    if (Storage::disk('s3')->exists($oldPath)) {
                        Storage::disk('s3')->copy($oldPath, $newPath);
                        Storage::disk('s3')->delete($oldPath);
                    }
                }

                $item->update([
                    'file_name' => $newFileName,
                    'file_path' => $newPath,
                    'updated_by' => $auth->id,
                ]);

                return ['status' => true, 'message' => 'File renamed successfully', 'old_name' => $oldFileName, 'new_name' => $newFileName];

            } catch (\Exception $e) {
                Log::error('Agency File Rename Error: ' . $e->getMessage());
                return ['status' => false, 'message' => 'Failed to rename file'];
            }
        }

        return ['status' => false, 'message' => 'Invalid type'];
    }

    /**
     * Soft delete (archive) a file
     */
    public function deleteFile($agencyId, $id)
    {
        $auth = auth()->user();

        $file = AgencyFile::forAgency($agencyId)->find($id);
        if (!$file) {
            return ['status' => false, 'message' => 'File not found'];
        }

        $fileName = $file->file_name;
        $file->update(['deleted_by' => $auth->id]);
        $file->delete();

        return ['status' => true, 'message' => 'File archived successfully', 'name' => $fileName];
    }

    /**
     * Soft delete (archive) a folder and its contents
     */
    public function deleteFolder($agencyId, $id)
    {
        $auth = auth()->user();

        $folder = AgencyFolder::forAgency($agencyId)->find($id);
        if (!$folder) {
            return ['status' => false, 'message' => 'Folder not found'];
        }

        $folderName = $folder->name;
        // Recursively soft delete children
        $this->deleteFolderRecursive($folder, $auth->id);

        return ['status' => true, 'message' => 'Folder archived successfully', 'name' => $folderName];
    }

    /**
     * Recursively soft delete folder contents
     */
    private function deleteFolderRecursive(AgencyFolder $folder, $userId)
    {
        // Delete child folders recursively
        foreach ($folder->children as $child) {
            $this->deleteFolderRecursive($child, $userId);
        }

        // Delete files in this folder
        $folder->files()->update(['deleted_by' => $userId]);
        $folder->files()->delete();

        // Delete the folder itself
        $folder->update(['deleted_by' => $userId]);
        $folder->delete();
    }

    /**
     * Generate a signed download URL
     */
    public function getDownloadUrl($agencyId, $id)
    {
        $file = AgencyFile::forAgency($agencyId)->find($id);
        if (!$file) {
            return ['status' => false, 'message' => 'File not found'];
        }

        try {
            if ($this->isLocalStorage()) {
                $localPath = public_path($file->file_path);
                if (!file_exists($localPath)) {
                    return ['status' => false, 'message' => 'File not found on storage'];
                }
                $url = asset($file->file_path);
            } else {
                if (!Storage::disk('s3')->exists($file->file_path)) {
                    return ['status' => false, 'message' => 'File not found on storage'];
                }
                $url = Storage::disk('s3')->temporaryUrl(
                    $file->file_path,
                    now()->addMinutes(10)
                );
            }

            return [
                'status' => true,
                'data' => [
                    'url' => $url,
                    'file_name' => $file->file_name,
                    'file_type' => $file->file_type,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Agency File Download Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to generate download link'];
        }
    }

    /**
     * Get file content for direct download (avoids CORS)
     */
    public function getFileForDownload($agencyId, $id)
    {
        $file = AgencyFile::withTrashed()->where('agency_id', $agencyId)->find($id);
        if (!$file) {
            return ['status' => false, 'message' => 'File not found'];
        }

        try {
            if ($this->isLocalStorage()) {
                $localPath = public_path($file->file_path);
                if (!file_exists($localPath)) {
                    return ['status' => false, 'message' => 'File not found on storage'];
                }
                $content = file_get_contents($localPath);
            } else {
                if (!Storage::disk('s3')->exists($file->file_path)) {
                    return ['status' => false, 'message' => 'File not found on storage'];
                }
                $content = Storage::disk('s3')->get($file->file_path);
            }

            return [
                'status' => true,
                'data' => [
                    'content' => $content,
                    'file_name' => $file->file_name,
                    'file_type' => $file->file_type,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Agency File Download Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to download file'];
        }
    }

    /**
     * Bulk download multiple files as a ZIP (no agency restriction — admin all-files context)
     */
    public function bulkDownloadZip(array $fileIds)
    {
        $files = AgencyFile::withTrashed()->whereIn('id', $fileIds)->get();
        if ($files->isEmpty()) {
            return ['status' => false, 'message' => 'No files found'];
        }

        $zip = new \ZipArchive();
        $tmpPath = tempnam(sys_get_temp_dir(), 'afm_bulk_') . '.zip';

        if ($zip->open($tmpPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return ['status' => false, 'message' => 'Failed to create ZIP archive'];
        }

        $nameCount = [];
        $tempFiles = [];
        foreach ($files as $file) {
            try {
                // Deduplicate filenames inside the zip
                $name = $file->file_name;
                if (isset($nameCount[$name])) {
                    $nameCount[$name]++;
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $base = pathinfo($name, PATHINFO_FILENAME);
                    $name = $base . '_' . $nameCount[$name] . ($ext ? '.' . $ext : '');
                } else {
                    $nameCount[$name] = 1;
                }

                if ($this->isLocalStorage()) {
                    $localPath = public_path($file->file_path);
                    if (!file_exists($localPath)) continue;
                    $zip->addFile($localPath, $name);
                } else {
                    if (!Storage::disk('s3')->exists($file->file_path)) continue;
                    // Stream S3 file to a local temp file to avoid loading into memory
                    $tmpFile = tempnam(sys_get_temp_dir(), 'afm_file_');
                    $stream = Storage::disk('s3')->readStream($file->file_path);
                    $dest = fopen($tmpFile, 'wb');
                    stream_copy_to_stream($stream, $dest);
                    fclose($dest);
                    if (is_resource($stream)) fclose($stream);
                    $tempFiles[] = $tmpFile;
                    $zip->addFile($tmpFile, $name);
                }
            } catch (\Exception $e) {
                Log::error('Bulk ZIP - skipping file ' . $file->id . ': ' . $e->getMessage());
            }
        }

        $zip->close();

        // Clean up temp files after zip is closed
        foreach ($tempFiles as $tmpFile) {
            if (file_exists($tmpFile)) @unlink($tmpFile);
        }

        return ['status' => true, 'path' => $tmpPath, 'count' => count($nameCount)];
    }

    /**
     * Generate a signed preview URL (for images and PDFs)
     */
    public function getPreviewUrl($agencyId, $id)
    {
        $file = AgencyFile::withTrashed()->forAgency($agencyId)->find($id);
        if (!$file) {
            return ['status' => false, 'message' => 'File not found'];
        }

        if (!$file->is_previewable) {
            return ['status' => false, 'message' => 'File type does not support preview'];
        }

        try {
            if ($this->isLocalStorage()) {
                $localPath = public_path($file->file_path);
                if (!file_exists($localPath)) {
                    return ['status' => false, 'message' => 'File not found on storage'];
                }
                $url = asset($file->file_path);
            } else {
                $url = Storage::disk('s3')->temporaryUrl(
                    $file->file_path,
                    now()->addMinutes(10)
                );
            }

            return [
                'status' => true,
                'data' => [
                    'url' => $url,
                    'file_name' => $file->file_name,
                    'file_type' => $file->file_type,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Agency File Preview Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to generate preview link'];
        }
    }

    /**
     * Get folder tree for agency, respecting MDO/Telehealth visibility
     */
    public function getFolderTree($agencyId, $canMdo = true, $canTelehealth = true)
    {
        $query = AgencyFolder::forAgency($agencyId)->rootFolders();

        if (!$canMdo || !$canTelehealth) {
            $this->applyFolderPermissionScope($query, $canMdo, $canTelehealth);
        }

        $folders = $query->with(['childrenRecursive' => function ($q) use ($canMdo, $canTelehealth) {
            $this->applyFolderPermissionScope($q, $canMdo, $canTelehealth);
        }])->orderBy('name', 'asc')->get();

        return ['status' => true, 'data' => $folders];
    }

    /**
     * Recursively apply MDO/Telehealth scope to eager-loaded children
     */
    private function applyFolderPermissionScope($query, $canMdo, $canTelehealth)
    {
        if (!$canMdo || !$canTelehealth) {
            $query->where(function ($q) use ($canMdo, $canTelehealth) {
                if (!$canMdo && !$canTelehealth) {
                    // Neither permission — hide any flagged folder
                    $q->where('is_mdo', 0)->where('is_telehealth', 0);
                } elseif (!$canMdo) {
                    // Telehealth only — hide MDO-only folders (is_mdo=1 AND is_telehealth=0)
                    $q->where(function ($q2) {
                        $q2->where('is_mdo', 0)->orWhere('is_telehealth', 1);
                    });
                } else {
                    // MDO only — hide Telehealth-only folders (is_telehealth=1 AND is_mdo=0)
                    $q->where(function ($q2) {
                        $q2->where('is_telehealth', 0)->orWhere('is_mdo', 1);
                    });
                }
            });
        }
        $query->with(['childrenRecursive' => function ($q) use ($canMdo, $canTelehealth) {
            $this->applyFolderPermissionScope($q, $canMdo, $canTelehealth);
        }]);
    }

    /**
     * List ALL archived files across ALL agencies for global archived listing
     */
    public function listAllAgenciesArchivedFiles($search = null, $canMdo = true, $canTelehealth = true, $filters = [])
    {
        $agencyids = Utility::getUserWiseAgency();

        if(auth()->user()->agency_fk !=""){
            $agencyids[] = auth()->user()->agency_fk;
        }

        $fileQuery = AgencyFile::select(
                'agency_files.*',
                'patient_file_links.patient_id',
                'patient_master.status as pt_status'
            )
            ->onlyTrashed()
            ->with('createdBy', 'folder', 'agency')
            ->leftJoin('users', 'users.id', '=', 'agency_files.created_by')
            ->leftJoin('patient_file_links', 'patient_file_links.agency_file_id', '=', 'agency_files.id')
            ->leftJoin('patient_master', 'patient_master.id', '=', 'patient_file_links.patient_id');

        // Agency users only see their own agency's files
        if (!empty($agencyids)) {
            $fileQuery->whereIn('agency_files.agency_id', $agencyids);
        }

        if ($search) {
            $fileQuery->where('agency_files.file_name', 'LIKE', '%' . $search . '%');
        }

        // Additional filters
        if (!empty($filters['agency_id'])) {
            $fileQuery->where('agency_files.agency_id', $filters['agency_id']);
        }
        if (!empty($filters['file_type'])) {
            $fileQuery->where('agency_files.file_type', $filters['file_type']);
        }
        if (!empty($filters['uploaded_by'])) {
            $fileQuery->where(function ($q) use ($filters) {
                $q->where('users.first_name', 'LIKE', '%' . $filters['uploaded_by'] . '%')
                  ->orWhere('users.last_name',  'LIKE', '%' . $filters['uploaded_by'] . '%');
            });
        }
        if (!empty($filters['date_range'])) {
            $range = Utility::convertMdyToYmd($filters['date_range']);
            if ($range['from']) $fileQuery->where('agency_files.deleted_at', '>=', $range['from']);
            if ($range['to'])   $fileQuery->where('agency_files.deleted_at', '<=', $range['to']);
        }
        if (!empty($filters['tag'])) {
            if ($filters['tag'] === 'mdo') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_mdo', 1)->where('is_telehealth', 0));
            } elseif ($filters['tag'] === 'telehealth') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_telehealth', 1)->where('is_mdo', 0));
            } elseif ($filters['tag'] === 'both') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_mdo', 1)->where('is_telehealth', 1));
            }
        }
        if (!empty($filters['linked_chart'])) {
            if ($filters['linked_chart'] === 'linked') {
                $fileQuery->whereNotNull('patient_file_links.patient_id');
            } elseif ($filters['linked_chart'] === 'not_linked') {
                $fileQuery->whereNull('patient_file_links.patient_id');
            }
        }
        if (!empty($filters['pt_status'])) {
            $fileQuery->where('patient_master.status', $filters['pt_status']);
        }
        if (!empty($filters['folder_path'])) {
            $folderIds = AgencyFolder::where('name', 'LIKE', '%' . $filters['folder_path'] . '%')->pluck('id');
            $fileQuery->whereIn('agency_files.folder_id', $folderIds);
        }

        if (!$canMdo) {
            $mdoFolderIds = AgencyFolder::where('is_mdo', 1)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $mdoFolderIds);
        }

        if (!$canTelehealth) {
            $telehealthFolderIds = AgencyFolder::where('is_telehealth', 1)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $telehealthFolderIds);
        }

        $paginator = $fileQuery->orderBy('agency_files.deleted_at', 'desc')->paginate(50);

        $paginator->each(function ($file) {
            $file->uploaded_by = $file->createdBy
                ? trim($file->createdBy->first_name . ' ' . $file->createdBy->last_name)
                : null;

            if ($file->folder) {
                $breadcrumb = $file->folder->breadcrumb;
                $file->file_path_label = implode(' / ', array_column($breadcrumb, 'name'));
                $file->is_mdo        = $file->folder->is_mdo;
                $file->is_telehealth = $file->folder->is_telehealth;
            } else {
                $file->file_path_label = 'Root';
                $file->is_mdo        = 0;
                $file->is_telehealth = 0;
            }

            $file->agency_name = $file->agency ? $file->agency->agency_name : '—';
        });

        return [
            'status' => true,
            'data'   => ['files' => $paginator],
        ];
    }

    /**
     * Same as listAllAgenciesArchivedFiles but returns all records (no pagination) for CSV export
     */
    public function listAllAgenciesArchivedFilesForExport($search = null, $canMdo = true, $canTelehealth = true, $filters = [])
    {
        $agencyids = Utility::getUserWiseAgency();

        if (auth()->user()->agency_fk != "") {
            $agencyids[] = auth()->user()->agency_fk;
        }

        $fileQuery = AgencyFile::select(
                'agency_files.*',
                'patient_file_links.patient_id',
                'patient_master.first_name as pt_first_name',
                'patient_master.last_name as pt_last_name',
                'patient_master.dob as pt_dob',
                'patient_master.gender as pt_gender',
                'patient_master.mobile as pt_mobile',
                'patient_master.email as pt_email',
                'patient_master.status as pt_status',
                'document_patient.document_name as doc_document_name'
            )
            ->onlyTrashed()
            ->with('createdBy', 'folder', 'agency')
            ->leftJoin('users', 'users.id', '=', 'agency_files.created_by')
            ->leftJoin('patient_file_links', 'patient_file_links.agency_file_id', '=', 'agency_files.id')
            ->leftJoin('patient_master', 'patient_master.id', '=', 'patient_file_links.patient_id')
            ->leftJoin('document_patient', 'document_patient.id', '=', 'patient_file_links.document_id');

        if (!empty($agencyids)) {
            $fileQuery->whereIn('agency_files.agency_id', $agencyids);
        }

        if ($search) {
            $fileQuery->where('agency_files.file_name', 'LIKE', '%' . $search . '%');
        }

        if (!empty($filters['agency_id'])) {
            $fileQuery->where('agency_files.agency_id', $filters['agency_id']);
        }
        if (!empty($filters['file_type'])) {
            $fileQuery->where('agency_files.file_type', $filters['file_type']);
        }
        if (!empty($filters['uploaded_by'])) {
            $fileQuery->where(function ($q) use ($filters) {
                $q->where('users.first_name', 'LIKE', '%' . $filters['uploaded_by'] . '%')
                  ->orWhere('users.last_name',  'LIKE', '%' . $filters['uploaded_by'] . '%');
            });
        }
        if (!empty($filters['date_range'])) {
            $range = Utility::convertMdyToYmd($filters['date_range']);
            if ($range['from']) $fileQuery->where('agency_files.deleted_at', '>=', $range['from']);
            if ($range['to'])   $fileQuery->where('agency_files.deleted_at', '<=', $range['to']);
        }
        if (!empty($filters['tag'])) {
            if ($filters['tag'] === 'mdo') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_mdo', 1)->where('is_telehealth', 0));
            } elseif ($filters['tag'] === 'telehealth') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_telehealth', 1)->where('is_mdo', 0));
            } elseif ($filters['tag'] === 'both') {
                $fileQuery->whereHas('folder', fn($q) => $q->where('is_mdo', 1)->where('is_telehealth', 1));
            }
        }
        if (!empty($filters['linked_chart'])) {
            if ($filters['linked_chart'] === 'linked') {
                $fileQuery->whereNotNull('patient_file_links.patient_id');
            } elseif ($filters['linked_chart'] === 'not_linked') {
                $fileQuery->whereNull('patient_file_links.patient_id');
            }
        }
        if (!empty($filters['pt_status'])) {
            $fileQuery->where('patient_master.status', $filters['pt_status']);
        }
        if (!empty($filters['folder_path'])) {
            $folderIds = AgencyFolder::where('name', 'LIKE', '%' . $filters['folder_path'] . '%')->pluck('id');
            $fileQuery->whereIn('agency_files.folder_id', $folderIds);
        }

        if (!$canMdo) {
            $mdoFolderIds = AgencyFolder::where('is_mdo', 1)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $mdoFolderIds);
        }
        if (!$canTelehealth) {
            $telehealthFolderIds = AgencyFolder::where('is_telehealth', 1)->pluck('id');
            $fileQuery->whereNotIn('agency_files.folder_id', $telehealthFolderIds);
        }

        $files = $fileQuery->orderBy('agency_files.deleted_at', 'desc')->get();

        $files->each(function ($file) {
            $file->uploaded_by = $file->createdBy
                ? trim($file->createdBy->first_name . ' ' . $file->createdBy->last_name)
                : null;

            if ($file->folder) {
                $breadcrumb = $file->folder->breadcrumb;
                $file->file_path_label = implode(' / ', array_column($breadcrumb, 'name'));
                $file->is_mdo          = $file->folder->is_mdo;
                $file->is_telehealth   = $file->folder->is_telehealth;
            } else {
                $file->file_path_label = 'Root';
                $file->is_mdo          = 0;
                $file->is_telehealth   = 0;
            }

            $file->agency_name = $file->agency ? $file->agency->agency_name : '—';
        });

        return [
            'status' => true,
            'data'   => ['files' => $files],
        ];
    }

    /**
     * List archived (soft-deleted) files and folders
     */
    public function listArchived($agencyId, $search = null)
    {
        // Get all archived folder IDs for this agency — used to filter out children
        $allArchivedFolderIds = AgencyFolder::onlyTrashed()
            ->where('agency_id', $agencyId)
            ->pluck('id')
            ->toArray();

        $folderQuery = AgencyFolder::onlyTrashed()->where('agency_id', $agencyId);
        $fileQuery   = AgencyFile::onlyTrashed()->where('agency_id', $agencyId);

        if ($search) {
            $folderQuery->where('name', 'LIKE', '%' . $search . '%');
            $fileQuery->where('file_name', 'LIKE', '%' . $search . '%');
        } else {
            // Only show top-level archived folders:
            // parent_id is null OR parent was not archived (parent is a live folder)
            $folderQuery->where(function ($q) use ($allArchivedFolderIds) {
                $q->whereNull('parent_id')
                  ->orWhereNotIn('parent_id', $allArchivedFolderIds);
            });

            // Only show top-level archived files:
            // folder_id is null OR folder was not archived
            $fileQuery->where(function ($q) use ($allArchivedFolderIds) {
                $q->whereNull('folder_id')
                  ->orWhereNotIn('folder_id', $allArchivedFolderIds);
            });
        }

        // Eager-load parent chain for folder path, and folder relation for file path
        $folders = $folderQuery->with('parent.parent.parent.parent')->orderBy('deleted_at', 'desc')->get();
        $files   = $fileQuery->with('folder.parent.parent.parent.parent')->orderBy('deleted_at', 'desc')->get();

        // Append folder_path to each folder (breadcrumb of its parent)
        $folders->each(function ($folder) {
            $path = [];
            $parent = $folder->parent;
            while ($parent) {
                array_unshift($path, $parent->name);
                $parent = $parent->parent;
            }
            $folder->folder_path = count($path) ? implode(' / ', $path) : 'Root';
        });

        // Append folder_path to each file (breadcrumb of its folder)
        $files->each(function ($file) {
            if (!$file->folder) {
                $file->folder_path = 'Root';
                return;
            }
            $breadcrumb = $file->folder->breadcrumb;
            $file->folder_path = count($breadcrumb) ? implode(' / ', array_column($breadcrumb, 'name')) : 'Root';
        });

        return [
            'status' => true,
            'data' => [
                'folders' => $folders,
                'files'   => $files,
            ]
        ];
    }

    /**
     * List contents inside an archived folder (folders and files that belong to it)
     */
    public function listArchivedFolder($agencyId, $folderId, $search = null)
    {
        // The folder itself must be archived
        $folder = AgencyFolder::onlyTrashed()->where('agency_id', $agencyId)->find($folderId);
        if (!$folder) {
            return ['status' => false, 'message' => 'Archived folder not found'];
        }

        // Sub-folders that were archived and belong to this parent
        $folderQuery = AgencyFolder::onlyTrashed()->where('agency_id', $agencyId)->where('parent_id', $folderId);
        // Files that were archived and belong to this folder
        $fileQuery   = AgencyFile::onlyTrashed()->where('agency_id', $agencyId)->where('folder_id', $folderId);

        if ($search) {
            $folderQuery->where('name', 'LIKE', '%' . $search . '%');
            $fileQuery->where('file_name', 'LIKE', '%' . $search . '%');
        }

        $folders = $folderQuery->with('parent.parent.parent.parent')->orderBy('name', 'asc')->get();
        $files   = $fileQuery->with('folder.parent.parent.parent.parent')->orderBy('file_name', 'asc')->get();

        $folders->each(function ($f) {
            $path = [];
            $parent = $f->parent;
            while ($parent) {
                array_unshift($path, $parent->name);
                $parent = $parent->parent;
            }
            $f->folder_path = count($path) ? implode(' / ', $path) : 'Root';
        });

        $files->each(function ($file) {
            if (!$file->folder) {
                $file->folder_path = 'Root';
                return;
            }
            $breadcrumb = $file->folder->breadcrumb;
            $file->folder_path = count($breadcrumb) ? implode(' / ', array_column($breadcrumb, 'name')) : 'Root';
        });

        // Build breadcrumb for the archived folder itself
        $breadcrumb = $folder->breadcrumb;

        return [
            'status' => true,
            'data'   => [
                'folder'     => ['id' => $folder->id, 'name' => $folder->name],
                'breadcrumb' => $breadcrumb,
                'folders'    => $folders,
                'files'      => $files,
            ]
        ];
    }

    /**
     * Restore an archived file
     */
    public function restoreFile($agencyId, $id)
    {
        $file = AgencyFile::onlyTrashed()
            ->where('agency_id', $agencyId)
            ->find($id);

        if (!$file) {
            return ['status' => false, 'message' => 'Archived file not found'];
        }

        // If file was inside a folder that is also archived, restore to root
        if ($file->folder_id) {
            $parentFolder = AgencyFolder::find($file->folder_id);
            if (!$parentFolder) {
                $file->folder_id = null;
            }
        }

        $file->update(['deleted_by' => null]);
        $file->restore();

        return ['status' => true, 'message' => 'File restored successfully', 'name' => $file->file_name];
    }

    /**
     * Restore an archived folder (and its contents)
     */
    public function restoreFolder($agencyId, $id)
    {
        $folder = AgencyFolder::onlyTrashed()
            ->where('agency_id', $agencyId)
            ->find($id);

        if (!$folder) {
            return ['status' => false, 'message' => 'Archived folder not found'];
        }

        // If parent folder is also archived, restore to root
        if ($folder->parent_id) {
            $parentFolder = AgencyFolder::find($folder->parent_id);
            if (!$parentFolder) {
                $folder->parent_id = null;
            }
        }

        $this->restoreFolderRecursive($folder);

        return ['status' => true, 'message' => 'Folder restored successfully', 'name' => $folder->name];
    }

    /**
     * Recursively restore folder and its contents
     */
    private function restoreFolderRecursive(AgencyFolder $folder)
    {
        // Restore the folder itself
        $folder->update(['deleted_by' => null]);
        $folder->restore();

        // Restore files in this folder
        AgencyFile::onlyTrashed()
            ->where('folder_id', $folder->id)
            ->update(['deleted_by' => null]);
        AgencyFile::onlyTrashed()
            ->where('folder_id', $folder->id)
            ->restore();

        // Restore child folders recursively
        $childFolders = AgencyFolder::onlyTrashed()
            ->where('parent_id', $folder->id)
            ->get();

        foreach ($childFolders as $child) {
            $this->restoreFolderRecursive($child);
        }
    }
}
