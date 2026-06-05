<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\User;
use App\Model\Patient;
use App\Model\LocationMaster;
use App\Model\InsuranceMaster;
use App\Model\PatientServiceRequest;
use App\Model\PaymentLogServiceWise;
use App\Master;
class PaymentLog extends Model
{
	use Notifiable; 
	//use Archivable;
	public $timestamps = false;
	protected $table = 'payment_log';
    protected $guarded = ["id"];

	public function users() 
	{
		return $this->belongsTo(User::class,'created_by','id');
	}

    public function patientDetails() 
	{
		return $this->belongsTo(Patient::class,'patient_id','id');
	}

	public function locationDetails() 
	{
		return $this->belongsTo(LocationMaster::class,'location_id','id');
	}

	public function insuranceDetails() 
	{
		return $this->belongsTo(InsuranceMaster::class,'insurance_id','id');
	}

	public function paymentDeatil() 
	{
		return $this->belongsTo(Master::class,'payment_type','id');
	}

	public function paymentLogDeatil() 
	{
		return $this->hasMany(PaymentLogServiceWise::class,'payment_log_id','id')->where('delete_flag','N');
	}
}
