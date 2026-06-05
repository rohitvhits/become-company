<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EsignWorkflowSignerNotification extends Mailable
{
	use SerializesModels;

	public $documentName;
	public $signerName;
	public $actionUrl;

	public function __construct($documentName, $signerName, $actionUrl)
	{
		$this->documentName = $documentName;
		$this->signerName = $signerName;
		
		$this->actionUrl = $actionUrl;
	}

	public function build()
	{
		return $this
			->subject('Document Awaiting Your Signature — ' . $this->documentName)
			->view('emails.esign-workflow-signer-notification');
	}
}
