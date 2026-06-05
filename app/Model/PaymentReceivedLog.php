<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PaymentReceivedLog extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'payment_received_log';
    protected $guarded = ["id"];
}
 