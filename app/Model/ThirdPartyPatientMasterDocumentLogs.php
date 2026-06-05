<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyPatientMasterDocumentLogs extends Model
{
	public $timestamps = false;
	protected $table = 'third_party_patient_master_document_logs';
	protected $guarded = ["id"];

	public function thirdPartyPatientMasterDetails()
	{
		return $this->belongsTo(ThirdPartyPatientMaster::class, 'third_party_patient_master_id', 'id')->where('deleted_flag', 'N');
	}

	public function documentDetails()
	{
		return $this->belongsTo(DocumentPatient::class, 'document_id', 'id')->where('deleted_flag', 'N');
	}

	public function userDetails()
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}

	public function updatedByUserDetails()
	{
		return $this->belongsTo(User::class, 'updated_by', 'id');
	}
}
