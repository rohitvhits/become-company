<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Patient;

class EsignImportDetail extends Model
{
    protected $table = 'esign_import_details';
    protected $guarded = ['id'];

    public function patientDetail(){
        return $this->hasOne(Patient::class,'id','patient_id');
    }
}
