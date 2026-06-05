<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\GenerateAgencyToken;
class HubThirdPartyLog extends Model
{
    public $timestamps = false;
    protected $table = 'hub_third_party_log';
    protected $fillable = ['id','record_id','response','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','api_key','url','ip','data','type'];
	public function patient(){
        return $this->belongsTo(Patient::class,'record_id','id');
    }

    public function generateTokenDetails(){
        return $this->belongsTo(GenerateAgencyToken::class,'api_key','token');
    }
}
