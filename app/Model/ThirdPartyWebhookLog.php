<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ThirdPartyWebhookLog extends Model
{

	public $timestamps = false;
	protected $table = 'third_party_webhook_log';
	protected $fillable = ['id','third_party_id','send_response','return_response','del_flag','created_date','created_by'];

}
 