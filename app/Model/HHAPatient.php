<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Agency;
class HHAPatient extends Model
{
    protected $table = "hha_patients";
    protected $guarded = ["id"];

    public function agencyDetail()
	{
		return $this->hasOne(Agency::class,'id','agency_fk')->where('delete_flag','N');
	}
    public function agencyDetails()
	{
		return $this->hasOne(Agency::class,'id','agency_fk')->where('delete_flag','N');
	}
    public static  function updateData($data, $where)
    {
       
        $insert = HHAPatient::where($where)->update($data);
        return $insert;
    }

    public static function fetchPatientCount($agencyId){
        return HHAPatient::where('agency_fk',$agencyId)->whereNull('first_name')->where('hha_sync','N')->get(); 
    }

    public static function getSyncPatientList($agencyId,$offset){
        return HHAPatient::whereRaw('SHA1(agency_fk) ="'.$agencyId.'"')->where('hha_sync','N')->limit(50)->skip($offset)->orderBy('id','desc')->get();
    }
}
