<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HubImportComplete extends Mailable
{
    use SerializesModels;

    public string $userName;
    public string $fileName;
    public int $total;
    public int $success;
    public int $failed;
    public int $updated;
    public int $deactivated;
    public string $status;
    public ?string $errorDetails;
    public string $importedAt;

    public function __construct(
        string $userName,
        string $fileName,
        int $total,
        int $success,
        int $failed,
        int $updated,
        int $deactivated,
        string $status,
        ?string $errorDetails,
        string $importedAt
    ) {
        $this->userName     = $userName;
        $this->fileName     = $fileName;
        $this->total        = $total;
        $this->success      = $success;
        $this->failed       = $failed;
        $this->updated      = $updated;
        $this->deactivated  = $deactivated;
        $this->status       = $status;
        $this->errorDetails = $errorDetails;
        $this->importedAt   = $importedAt;
    }

    public function build()
    {
        $subject = $this->status === 'completed'
            ? 'Hub Record Import Completed — ' . $this->fileName
            : 'Hub Record Import Failed — ' . $this->fileName;

        return $this->subject($subject)
                    ->cc(['bhargav.dev@nybestmedical.com', 'Pinak@nybestmedical.com'])
                    ->view('emails.hub-import-complete');
    }
}
