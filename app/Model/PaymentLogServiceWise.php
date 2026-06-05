<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\User;
use App\Master;
use App\Model\PaymentLog;

class PaymentLogServiceWise extends Model
{
	use Notifiable; 
	//use Archivable;
	public $timestamps = false;
	protected $table = 'payment_log_services_wise';
    protected $guarded = ["id"];

	public function users() 
	{
		return $this->belongsTo(User::class,'created_by','id');
	}

	public function serviceDetails() 
	{
		return $this->belongsTo(Master::class,'service_id','id');
	}

	public function paymentLogDetails() 
	{
		return $this->belongsTo(PaymentLog::class,'payment_log_id','id');
	}
}
 