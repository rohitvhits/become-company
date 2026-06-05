<?php

namespace App\Services;

use App\Models\AgencyFile;
use App\Models\PatientFileLink;
use App\Model\DocumentPatient;
use App\Model\DocumentUploadHistory;
use App\Services\DocumentUploadService;
use App\Services\UserDocQuestionMarkedService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PatientFileLinkService
{
    /**
     * Link a file to a patient (one file → one patient, replaces existing link).
     *
     * @return array{status: bool, message: string, file_name?: string}
     */
    public function linkPatient(int $fileId, int $patientId, int $linkedBy, ?int $documentId = null): array
    {
        $file = AgencyFile::withTrashed()->find($fileId);
        if (!$file) {
            return ['status' => false, 'message' => 'File not found'];
        }

        PatientFileLink::updateOrCreate(
            ['agency_file_id' => $fileId],
            ['patient_id' => $patientId, 'linked_by' => $linkedBy, 'document_id' => $documentId]
        );

        return ['status' => true, 'message' => 'File linked to patient successfully', 'file_name' => $file->file_name];
    }

    /**
     * Unlink a file from a patient.
     *
     * @return array{status: bool, message: string, file_name?: string}
     */
    public function unlinkPatient(int $fileId, int $patientId): array
    {
        $deleted = PatientFileLink::where('agency_file_id', $fileId)
            ->where('patient_id', $patientId)
            ->delete();

        if (!$deleted) {
            return ['status' => false, 'message' => 'Link not found'];
        }

        $file = AgencyFile::withTrashed()->find($fileId);

        return ['status' => true, 'message' => 'File unlinked successfully', 'file_name' => $file->file_name ?? null];
    }

    /**
     * Get all patients linked to a file.
     *
     * @return array{status: bool, data: array}
     */
    public function getLinkedPatients(int $fileId): array
    {
        $links = PatientFileLink::where('agency_file_id', $fileId)
            ->with('patient')
            ->get()
            ->map(function ($link) {
                $p = $link->patient;
                return [
                    'patient_id'   => $link->patient_id,
                    'patient_name' => $p ? ($p->first_name . ' ' . $p->last_name) : 'Unknown',
                    'linked_at'    => $link->created_at ? $link->created_at->format('m/d/Y') : '',
                ];
            });

        return ['status' => true, 'data' => $links];
    }

    /**
     * Get all files linked to a patient.
     *
     * @return array{status: bool, data: array}
     */
    public function getPatientFiles(int $patientId): array
    {
        $previewTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

        $links = PatientFileLink::where('patient_id', $patientId)
            ->with(['agencyFile' => function ($q) {
                $q->withTrashed()->with('folder');
            }])
            ->orderByDesc('created_at')
            ->get();

        $files = $links->map(function ($link) use ($previewTypes) {
            $f = $link->agencyFile;
            if (!$f) return null;

            $bytes = $f->file_size ?? 0;
            if ($bytes >= 1048576)  $size = number_format($bytes / 1048576, 2) . ' MB';
            elseif ($bytes >= 1024) $size = number_format($bytes / 1024, 2) . ' KB';
            else                    $size = $bytes . ' B';

            return [
                'link_id'        => $link->id,
                'file_id'        => $f->id,
                'file_name'      => $f->file_name,
                'file_type'      => strtoupper($f->file_type ?? ''),
                'file_size'      => $size,
                'agency_id'      => $f->agency_id,
                'is_archived'    => !is_null($f->deleted_at),
                'is_previewable' => in_array(strtolower($f->file_type ?? ''), $previewTypes),
                'linked_at'      => $link->created_at ? $link->created_at->format('m/d/Y h:i A') : '',
                'download_url'   => url('/file-manager/file/download/' . $f->id) . '?agency_id=' . $f->agency_id,
                'preview_url'    => url('/file-manager/file/preview/' . $f->id) . '?agency_id=' . $f->agency_id,
            ];
        })->filter()->values();

        return ['status' => true, 'data' => $files];
    }

    /**
     * Copy a file manager file into the patient's Document Section.
     * Mirrors PatientController::DocumentUploadByPatientId storage logic.
     *
     * @return array{status: bool, message: string, document_id?: int}
     */
    public function addToPatientChart(AgencyFile $agencyFile, array $data): array
    {
        try {
            $extension   = strtolower($agencyFile->file_type ?? 'bin');
            $newFileName = uniqid() . time() . '.' . $extension;
            $isLocal     = env('FILE_UPLOAD_PERMISSION') == 'development';

            if ($isLocal) {
                $srcPath  = public_path($agencyFile->file_path);
                $destDir1 = public_path('patientdocument');
                $destDir2 = public_path('patientWriteDocument');

                if (!file_exists($srcPath)) {
                    return ['status' => false, 'message' => 'Source file not found on disk'];
                }
                if (!file_exists($destDir1)) mkdir($destDir1, 0777, true);
                if (!file_exists($destDir2)) mkdir($destDir2, 0777, true);

                \File::copy($srcPath, $destDir1 . '/' . $newFileName);
                \File::copy($srcPath, $destDir2 . '/' . $newFileName);
            } else {
                if (!Storage::disk('s3')->exists($agencyFile->file_path)) {
                    return ['status' => false, 'message' => 'Source file not found in storage'];
                }
                $content = Storage::disk('s3')->get($agencyFile->file_path);
                Storage::disk('s3')->put('patientdocument/' . $newFileName, $content);
                Storage::disk('s3')->put('patientWriteDocument/' . $newFileName, $content);
            }

            $auth = auth()->user();

            $requestService = !empty($data['request_service_id'])
                ? implode(', ', (array) $data['request_service_id'])
                : '';

            $medicationList = !empty($data['medication_list']) && $data['medication_list'] == 1 ? 1 : 0;
            $insuranceElg   = !empty($data['insurance_elg'])   && $data['insurance_elg']   == 1 ? 1 : 0;
            $mdoTag         = !empty($data['mdo_tag'])         && $data['mdo_tag']         == 1 ? 1 : 0;
            $mdoSource      = $mdoTag ? ($data['mdo_source'] ?? null) : null;
            $documentReview = !empty($data['document_review']) && $data['document_review'] == 1;
            $assignDocUser  = $documentReview ? ($data['document_approval_user_id'] ?? null) : null;

            $docData = [
                'document_name'          => $data['document_name'],
                'attachment'             => $newFileName,
                'old_attachment'         => $newFileName,
                'patient_id'             => $data['patient_id'],
                'request_service_id'     => $requestService,
                'internal_use'           => !empty($data['internal_use']) ? 1 : 0,
                'info_only'              => !empty($data['upload_for_info_only']) ? 1 : 0,
                'medication_list'        => $medicationList,
                'insurance_elg'          => $insuranceElg,
                'mdo_tag'                => $mdoTag,
                'mdo_source'             => $mdoSource,
                'assign_document_review' => $assignDocUser,
                'document_review_status' => $documentReview ? 'Pending' : 'Approved',
                'deleted_flag'           => 'N',
                'created_date'           => now()->toDateTimeString(),
                'created_by'             => $auth->id,
            ];

            if (!empty($data['document_completed_date'])) {
                $docData['document_completed_date'] = date('Y-m-d', strtotime($data['document_completed_date']));
            }

            $doc = new DocumentPatient($docData);
            $doc->save();

            $history                       = $docData;
            $history['document_type_flag'] = 1;
            $history['created_date']       = now()->toDateTimeString();
            (new DocumentUploadHistory($history))->save();

            // Save selected services to document_upload_services table (Attachment Service)
            if (!empty($data['document_service_id'])) {
                $docUploadService = new DocumentUploadService();
                foreach ((array) $data['document_service_id'] as $serviceId) {
                    if ($serviceId) {
                        $docUploadService->save([
                            'patient_id'  => $data['patient_id'],
                            'document_id' => $doc->id,
                            'service_id'  => $serviceId,
                            'del_flag'    => 'N',
                        ]);
                    }
                }
            }

            // Save confirmation question answers
            if (!empty($data['questions'])) {
                $questions = is_array($data['questions'])
                    ? $data['questions']
                    : explode(',', $data['questions']);
                foreach ($questions as $questionId) {
                    if ($questionId) {
                        UserDocQuestionMarkedService::save([
                            'user_id'     => auth()->id(),
                            'question_id' => $questionId,
                        ]);
                    }
                }
            }

            // Link the agency file to the patient so it shows in the Linked Chart column
            $this->linkPatient((int) $agencyFile->id, (int) $data['patient_id'], auth()->id(), $doc->id);

            return ['status' => true, 'message' => 'File added to patient chart successfully', 'document_id' => $doc->id];

        } catch (\Exception $e) {
            Log::error('addToPatientChart error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to add file to chart. Please try again.'];
        }
    }
}
