<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Master;
class DocumentUploadModal extends Model
{
    public $timestamps = false;
    protected $table = 'document_upload_services';
    protected $fillable = ['id','patient_id','document_id','service_id','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by'];
	
	public function masterDetails(){
        return $this->belongsTo(Master::class,'service_id','id')->where('master_type_fk',11);
    }

    public function patientDetails(){
		return $this->belongsTo(Patient::class, 'patient_id', 'id')->where('deleted_flag','N');
	}

    public function documentDetails(){
		return $this->belongsTo(DocumentPatient::class, 'document_id', 'id');
	}
  public function documentDetailsWithHasOne(){
		return $this->hasOne(DocumentPatient::class, 'document_id', 'id');
	}
}
