<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use App\Model\ThirdPartyHealthInsurance;
class ThirdPartyHealthInsuranceHelper
{
    public function __construct()
	{}
	
	 
	
    public static  function insert($data)
    {
        $auth = auth()->user();
        $data['created_date'] =date('Y-m-d H:i:s');
        $data['created_by'] = $auth->id;
		$insert_data = $data; 
		$inser_id = new ThirdPartyHealthInsurance($insert_data);
		$inser_id->save();
		$Insert = $inser_id->id; 

		return $Insert;
	
		
    }
	 public static  function update($data,$where)
    {	
        $auth = auth()->user();
        $data['updated_date'] =date('Y-m-d H:i:s');
        $data['updated_by'] = $auth->id;
      $insert = ThirdPartyHealthInsurance::where($where)->update($data);
      return $insert;
	
		
    }
    
    public static function getDetailsByRecordId($id){
        $query = ThirdPartyHealthInsurance::select('third_party_health_insurance.*','ms.name')
                    ->leftjoin('master_table as ms',function($join){
                        $join->on('ms.id','=','third_party_health_insurance.insurance_id');
                    })
                    ->where('third_party_health_insurance.del_flag','N')->where('third_party_health_insurance.record_id',$id)->paginate(10);
        return $query;
    }
    public static function getDetailsByRecordIdNew($id){
        $query = ThirdPartyHealthInsurance::where('third_party_health_insurance.del_flag','N')->where('third_party_health_insurance.record_id',$id)->first();
        return $query;
    }

    public static function getDetailsById($id){
        $query = ThirdPartyHealthInsurance::select('third_party_health_insurance.*','ms.name')
        ->leftjoin('master_table as ms',function($join){
            $join->on('ms.id','=','third_party_health_insurance.insurance_id');
        })
        ->where('third_party_health_insurance.del_flag','N')->where('third_party_health_insurance.id',$id)->first();
return $query;
    }
	
}