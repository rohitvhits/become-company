<?php

namespace App\Model;

use App\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\User;
class DocumentPatient extends Model
{
	use Notifiable;
	public $timestamps =false;
	protected $table = 'document_patient';
	protected $fillable = ['id','patient_id','hha_medical_doc_id','document_name', 'attachment', 'created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'deleted_flag','hha_document_id','document_id','uploaded_complience_hha','request_service_id','document_completed_date','templete_id','agency_form_id','is_checked','internal_use','document_review_status','document_review_date','document_review_by','assign_document_review','sign_stamp_status','extension','size_in_bytes','pdf_type','old_attachment','medication_list','insurance_elg','mdo_tag','call_back_url','mdo_source','info_only','is_task_helath_dup','is_esign_form_complete','ai_summary'];

	public function patientDetails(){
		return $this->belongsTo(Patient::class, 'patient_id', 'id')->where('deleted_flag','N')->whereNull('archived_at');
	}

	public function documentUploadServiceDetails(){
		return $this->belongsTo(DocumentUploadModal::class, 'id', 'document_id');
	}

	public function userDetails(){
		return $this->belongsTo(User::class, 'created_by', 'id');
	}

	public function documentUploadServiceDetailsSame(){
		return $this->belongsTo(DocumentUploadModal::class, 'document_id', 'id')
        ->whereHas('documentDetailsWithHasOne');

	}

	public function templeteDetails(){
		return $this->belongsTo(Template::class, 'templete_id', 'id')->where('del_flag','N');
	}

	public function reviewUserDetails(){
		return $this->belongsTo(User::class, 'document_review_by', 'id');
	}

	public function assignUserReviewDocument(){
		return $this->belongsTo(User::class, 'assign_document_review', 'id');
	}

	public function documentUploadServiceDetailsMany(){
		return $this->hasMany(DocumentUploadModal::class, 'document_id', 'id')->where('del_flag','N');
	}

    public function updatedUserDetails(){
		return $this->belongsTo(User::class, 'updated_by', 'id');
	}	

	public function sendEmailLogs(){
		return $this->hasMany(UserSendPatientDocumentLog::class, 'document_id', 'id');
	}

	public function sendBackToAgencyLogs(){
		return $this->hasMany(UserSendPatientDocumentLog::class, 'document_id', 'id')
			->where('send_back_to_agency', 1);
	}
}
