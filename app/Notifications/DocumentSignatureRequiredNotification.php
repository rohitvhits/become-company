<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DocumentSignatureRequiredNotification extends Notification implements ShouldQueue
{
	use Queueable;

	private $details;

	public function __construct($details)
	{
		$this->details = $details;
	}

	public function via($notifiable)
	{
		return ['database'];
	}

	public function toDatabase($notifiable)
	{
		return [
			'body' => $this->details['body'],
			'action' => $this->details['actionURL'] ?? '',
			'record_id' => $this->details['record_id'] ?? '',
			'document_id' => $this->details['document_id'] ?? '',
			'document_name' => $this->details['document_name'] ?? '',
			'type' => 'Document Signature Required',
		];
	}
}
