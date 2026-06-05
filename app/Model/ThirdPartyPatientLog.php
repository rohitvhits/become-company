<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\GenerateAgencyToken;
class ThirdPartyPatientLog extends Model
{
    public $timestamps = false;
    protected $table = 'third_party_patient_log';
    protected $fillable = ['id','patient_id','response','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','api_key','url','ip','data','type'];
	
	public function patient(){
        return $this->belongsTo(Patient::class,'patient_id','id');
    }

    public function generateTokenDetails(){
        return $this->belongsTo(GenerateAgencyToken::class,'api_key','token');
    }
}
