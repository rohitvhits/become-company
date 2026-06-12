<?php

namespace App\Model;

use App\Agency;
use App\GenerateAgencyToken;
use App\Model\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\User;

class ThirdPartyPatientMaster extends Model
{

	//use Archivable;
	public $timestamps = false;
	protected $table = 'third_party_patient_master';
	protected $fillable = ['id','patient_id','first_name','middle_name','last_name','dob','fu_date', 'gender', 'phone', 'agency_id','remarks','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'deleted_flag','status','appointment_added_by','appointment_added_created_date','appointment_date','doctor_id','service_id','mobile','language','service_expiry_date','type','sms','key','appoinment_time_id','patient_code','diciplin','notes','record_id','booked_date','booked_by','cancel_date','cancel_by','no_show_date','no_show_by','hamaspik_payment','appointment_mode','sms_count','prev_status','hha_flag','attachment_document','telehealth_date_time','address1','address2','state','city','zip_code','county','payment_type','inflowcare_type','inflowcare_id','hha_id','due_date','platform_type','platform_id','link_hha_caregiver','officeId','hha_other_id','inservice_status','inservice_datetime','alaycare_id','alaycare_name','robort_id','follow_date','partner_agency','link_hha_patient','agency_token_id','third_party_priority','cin','ssn','emergency_contact_name','emergency_phone','insurance_id','insurance_name','other_insurance_name','service_start_date','priority_code','third_party_callback_url'];


	public function agencyDetails(){
		return $this->belongsTo(Agency::class,'agency_id','id');
	}

	public function agencyGenerateDetails(){
		return $this->belongsTo(GenerateAgencyToken::class,'agency_token_id','id');
	}

	public function patientDetails(){
		return $this->belongsTo(Patient::class,'patient_id','id')->where('deleted_flag','N');
	}
	
	public function serviceDetails(){
		return $this->hasOne(PatientServiceRequest::class,'id','requested_service_id');
	}
	public function documentDetails(){
		return $this->hasOne(DocumentPatient::class,'request_service_id','requested_service_id');
	}

	public function userDetails(){
        return $this->hasOne(User::class,"id","created_by");
    }
}
 