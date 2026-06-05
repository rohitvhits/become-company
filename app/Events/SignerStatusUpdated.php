<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class SignerStatusUpdated implements ShouldBroadcastNow
{
	use SerializesModels;

	public $signerId;
	public $status;
	public $groupId;

	public function __construct($signerId, $status, $groupId)
	{
		$this->signerId = $signerId;
		$this->status = $status;
		$this->groupId = $groupId;
	}

	public function broadcastOn()
	{
		return new Channel('signer-status');
	}

	public function broadcastAs()
	{
		return 'SignerStatusUpdated';
	}

	public function broadcastConnections()
	{
		return ['pusher'];
	}
}
