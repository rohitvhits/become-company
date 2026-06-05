<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Http\Request;
use App\Helpers\HHAOfficeHelper;
use App\Model\HHAOffice;
use Illuminate\Routing\Controller as BaseController;
use App\Services\HHACaregiverMedicalService;
use App\Model\HHACaregivers;
class HHAOfficeController extends BaseController
{
    

    public function __construct()
    {
        
    }
    
    public function syncOffice(Request $request){
        $query = Agency::where('app_name','!=',"")->where('id',$request->id)->get();
        foreach($query as $val){
            $response = HHAOfficeHelper::syncOffice($val->id);
        }
       return response()->json(['error_msg'=>'Successfully sync']);
    }

    public function combineUpdateCode(Request $request){
        $query = HHAOffice::select('id','agency_fk','office_id','office_name','office_code')->where('hha_update_flag','N')->inRandomOrder()->paginate(3);
       
        foreach($query as $val){
            $subQuery = HHACaregivers::where('agency_fk',$val->agency_fk)->where('officeId',$val->office_id)->where('hha_delete_flag','N')->get();
            
            if(!empty($subQuery[0])){
                foreach($subQuery as $vals){
                    $data['combine_code'] = $val->office_code.'-'.$vals->caregiver_code;
                    if($vals['office_name'] !=""){

                    }else{
                        $data['office_name'] = $val->office_name;

                    }
                    HHACaregivers::where('agency_fk',$vals->agency_fk)->where('officeId',$vals->officeId)->where('id',$vals->id)->update($data);
            
                }
                HHAOffice::where('id',$val->id)->update(array('hha_update_flag'=>'Y','updated_at'=>date('Y-m-d H:i:s'))); 
            }
            
        }
    }
}
