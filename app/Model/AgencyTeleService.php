<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use App\Master;

class AgencyTeleService extends Model
{
    use SoftDeletes;
    public $timestamps =false;
    protected $table = 'agency_tele_service';
    protected $fillable = ['id','agency_id','type','service_id','del_flag','created_date','created_by','updated_date','updated_by','deleted_by', 'deleted_at'];


    public function serviceDetails() 
	{
		return $this->belongsTo(Master::class,'service_id','id');
	}

    public function users() 
	{
		return $this->belongsTo(User::class,'created_by','id');
	}

    public static function getAgencyServicesArray($agency_id){
        return AgencyTeleService::where('agency_id',$agency_id)->where('del_flag','N')->pluck('service_id')->toArray();
    }
}
