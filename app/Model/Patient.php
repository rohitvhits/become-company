<?php

namespace App\Model;

use App\Agency;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use \LaravelArchivable\Archivable;
use Carbon\Carbon;

class Patient extends Model
{
	use Notifiable;
	//use Archivable;
	public $timestamps = false;
	protected $table = 'patient_master';
	protected $fillable = ['id', 'first_name', 'middle_name', 'last_name', 'dob', 'fu_date', 'gender', 'phone', 'agency_id', 'remarks', 'created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'deleted_flag', 'status', 'appointment_added_by', 'appointment_added_created_date', 'appointment_date', 'doctor_id', 'service_id', 'mobile', 'language', 'service_expiry_date', 'type', 'sms', 'key', 'appoinment_time_id', 'patient_code', 'diciplin', 'notes', 'record_id', 'booked_date', 'booked_by', 'cancel_date', 'cancel_by', 'no_show_date', 'no_show_by', 'hamaspik_payment', 'appointment_mode', 'sms_count', 'prev_status', 'hha_flag', 'attachment_document', 'telehealth_date_time', 'address1', 'address2', 'state', 'city', 'zip_code', 'county', 'payment_type', 'inflowcare_type', 'inflowcare_id', 'hha_id', 'due_date', 'platform_type', 'platform_id', 'link_hha_caregiver', 'officeId', 'hha_other_id', 'inservice_status', 'inservice_datetime', 'alaycare_id', 'alaycare_name', 'robort_id', 'follow_date', 'partner_agency', 'link_hha_patient', 'insurance_id', 'insurance_name', 'other_insurance_name', 'cin', 'record_read', 'emergency_contact_name', 'emergency_phone', 'location_id', 'patient_sms_flag', 'completed_date', 'completed_by', 'patient_related_id', 'sms_send_date', 'reason_id', 'archived_at', 'garbase_status', 'next_appoinment_date', 'next_appoinment_by', 'assign_user_id', 'telehealth_by', 'processing_date', 'processing_overdue_notitfication', 'merge_appointment_id', 'email', 'traning_due_date', 'training_status', 'inservice_status_two', 'availability_followup_date', 'link_third_party', 'location_branch', 'ssn', 'training_completed_date', 'third_party_priority', 'transition_aid', 'medicare_no', 'other_gender', 'agency_token_id', 'link_alaycare_client_id', 'service_start_date', 'third_party_callback_url', 'full_name', 'priority_code', 'referral_type', 'company_id', 'hub_id', 'last_status_update', 'last_status_update_by','is_archive','dept_id','task_health_link','emergency_contact_number','branch_id','telehealth_time_slot','telehealth_key','telehealth_nurse','demographic_updated_flag','patient_image','flag','reason','send_emmacare_id','auto_patient_service','emmacare_referral_uuid','telehealth_language','other_reason','patient_agency_merge_id','medication_count','insurance_elg_count','mdo_tag_count','pharmacy_name','pharmacy_no','agency_user_id','is_reviewed','no_medication_taken','telehealth_time_frame'];

	public function users()
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}


	public function getFullNameAttribute()
	{
		return ucwords("{$this->first_name} {$this->middle_name} {$this->last_name}");
	}

	public function languages()
	{
		return $this->belongsTo(Language::class, 'language', 'id');
	}

	public function locations()
	{
		return $this->belongsTo(LocationMaster::class, 'location_id', 'id');
	}



	public function assignToUser()
	{
		return $this->hasOne(User::class, 'id', 'assign_user_id');
	}

	public function hhaAppoinmets()
	{
		return $this->hasOne(HhaAppointment::class, "id", "hha_id");
	}

	public function hhaAppoinmetsLis()
	{
		return $this->hasOne(HHACaregivers::class, "caregiver_id", "link_hha_caregiver");
	}
	public function agencyDetail()
	{
		return $this->hasOne(Agency::class, "id", "agency_id")->where('delete_flag', 'N');
	}


	public static function patientBookedGetData()
	{
		$currentDate = Carbon::now();

		return Patient::with('agencyDetail:id,agency_name')->where('deleted_flag', 'N')->whereDate('appointment_date', $currentDate)->where('status', 'booked')->get();
	}

	public static function BookedStatusUpdateNoShow($id)
	{
		return Patient::where('id', $id)->update(array('status' => 'noshow'));
	}

	public function insuranceDetails()
	{
		return $this->hasOne(InsuranceMaster::class, "id", "insurance_name");
	}

	public function statusUpdatedUsers()
	{
		return $this->hasOne(User::class, "id", "last_status_update_by");
	}

	protected function formatName($value)
	{
		if (!$value) {
			return null;
		}

		$value = preg_replace('/\s+/', ' ', trim($value));

		return ucwords(strtolower($value), " -");
	}

	public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = $this->formatName($value);
    }

    public function setMiddleNameAttribute($value)
    {
        $this->attributes['middle_name'] = $this->formatName($value);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = $this->formatName($value);
    }

	public function setFullNameAttribute()
    {
		$this->attributes['full_name'] = implode(' ', array_filter([
			trim($this->first_name ?? ''),
			trim($this->last_name ?? '')
		]));
    }
}