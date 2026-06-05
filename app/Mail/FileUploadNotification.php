<?php

namespace App\Mail;

use App\Models\AgencyFile;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FileUploadNotification extends Mailable
{
    use SerializesModels;

    public AgencyFile $file;
    public string $folderType;
    public string $agencyName;
    public string $uploaderName;
    public string $folderPath;
    public int $fileCount;

    public function __construct(AgencyFile $file, string $folderType, string $agencyName, string $uploaderName, string $folderPath, int $fileCount = 1)
    {
        $this->file         = $file;
        $this->folderType   = $folderType;
        $this->agencyName   = $agencyName;
        $this->uploaderName = $uploaderName;
        $this->folderPath   = $folderPath;
        $this->fileCount    = $fileCount;
    }

    public function build()
    {
        $subject = $this->fileCount > 1
            ? '[' . $this->folderType . '] ' . $this->fileCount . ' Files Uploaded'
            : '[' . $this->folderType . '] New File Uploaded — ' . $this->file->file_name;

        return $this
            ->subject($subject)
            ->view('emails.file-upload-notification')
            ->with([
                'file'         => $this->file,
                'folderType'   => $this->folderType,
                'agencyName'   => $this->agencyName,
                'uploaderName' => $this->uploaderName,
                'folderPath'   => $this->folderPath,
                'uploadedAt'   => $this->file->created_at,
                'fileCount'    => $this->fileCount,
            ]);
    }
}
