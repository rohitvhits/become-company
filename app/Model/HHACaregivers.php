<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Agency;
class HHACaregivers extends Model
{
    use SoftDeletes;
    protected $table = "hha_caregivers";
    protected $guarded = ["id"];

    public function agencyDetails(){
        return $this->hasOne(Agency::class,"id","agency_fk");
    }

    

    public static  function insertData($data)
    {
        $insertData = $data;
        $inserId = new HHACaregivers($insertData);
        $inserId->save();
        $Insert = $inserId->id;

        return $Insert;
    }
    public static  function updateData($data, $where)
    {
        $insert = HHACaregivers::where($where)->update($data);
        return $insert;
    }
    public static function getData()
    {
        $query =  HHACaregivers::whereNull('deleted_at')->whereRaw("(last_medical_sync is null or last_medical_sync <'".date('Y-m-d H:i:s',strtotime('-5 day') )."')" )->inRandomOrder()->limit(1000)->get(); 

        
        


        //$query =  HHACaregivers::where('id','44324')->get();
        return $query;
    }

    public static function fetchCaregiverCount($agencyId){
        return HHACaregivers::where('agency_fk',$agencyId)->where('hha_delete_flag','N')->whereNull('first_name')->get();
    }
    public static function fetchCaregiverCountWithPaginate($agencyId){
        return HHACaregivers::whereRaw('SHA1(agency_fk) = "'.$agencyId.'"')->where('hha_delete_flag','N')->whereRaw("(hhasyncdatetime is null or DATE_FORMAT(hhasyncdatetime,'%Y-%m-%d') <'".date('Y-m-d')."')" )->inRandomOrder()->paginate(100);
    } 
    public static function getAllCaregiverDetails($agencyId){

        return HHACaregivers::whereRaw('SHA1(agency_fk) = "'.$agencyId.'"')->whereNull('deleted_at')->whereRaw("status ='Active' and (last_medical_sync is null or DATE_FORMAT(last_medical_sync,'%Y-%m-%d') <'".date("Y-m-d", strtotime("- 5days"))."')" )->inRandomOrder()->paginate(100);
    }
    public static function getDetailsById($cid){
        return HHACaregivers::where('caregiver_id',$cid)->where('hha_delete_flag','N')->first();
    }

    
    public static function getAllCaregiverIds($agencyId){
        return HHACaregivers::where('agency_fk',$agencyId)->where('hha_delete_flag','N')->pluck('caregiver_id');
    }

    public static function getAllCaregiverDetailsOnlyNewCaregiver($agencyId){
   
        return HHACaregivers::whereRaw('SHA1(agency_fk) = "'.$agencyId.'"')->whereNull('deleted_at')->whereNull('first_name')->inRandomOrder()->paginate(50);
    }

    public static function getCaregiverDetailsByAgencyIdAndCaregiverId($caregiverId,$agencyId){
   
        return HHACaregivers::where('caregiver_id',$caregiverId)->where('agency_fk',$agencyId)->first();
    }
}
 