<?php

namespace App\Model;

use App\Master;
use App\Model\Patient;
use Illuminate\Database\Eloquent\Model;

class AgencyWiseNotifictionEmail extends Model
{
    protected $table = "agency_wise_notification_email";
    protected $guarded = ["id"];

  
    public static function getStatusByAgencyId($agencyId,$type,$status,$patientId){
        if($type =='Patient'){
            $getStatusAgencyNotification = AgencyWiseNotifictionEmail::selectRaw('id,patient_status,email,service_id,discipline_id')->where('agency_id',$agencyId)->where('delete_flag','N')->whereNotNull('patient_status')->get();
        }else{
            $getStatusAgencyNotification = AgencyWiseNotifictionEmail::selectRaw('id,caregiver_status as patient_status,email,service_id,discipline_id')->where('delete_flag','N')->where('agency_id',$agencyId)->whereNotNull('caregiver_status')->get();
        }

        $flag = 0;
        $finalEmailArray = [];
        $patient = Patient::where('id',$patientId)->first();
       
		$explode = explode(',',$patient->service_id);
		$discipline = $patient->diciplin;

        if(count($getStatusAgencyNotification) >0){
            foreach($getStatusAgencyNotification as $data){
                if(isset($data['service_id']) && !empty($data['service_id'])){
                    $subId = explode(',',$data->service_id);
                    foreach($subId as $vas){
                        if(in_array($vas,$explode)){
                            if(!in_array($data->email,$finalEmailArray)){
                                $finalEmailArray[] = $data->email;
                            }
                        }
                    }
                }

                if(isset($data['discipline_id']) && !empty($data['discipline_id'])){
                    $subId = explode(',',$data->discipline_id);
                    foreach($subId as $vas){
                        if($vas == $discipline){
                            if(!in_array($data->email,$finalEmailArray)){
                                $finalEmailArray[] = $data->email;
                            }
                        }
                    }
                } 

                if(empty($data['discipline_id']) && empty($data['service_id'])){
                    $explode = explode(',',$data->patient_status);
                    if($status =='complete'){
                     if(in_array('completed',$explode)){
                         $flag = 1;
                         $finalEmailArray[] = $data->email;
                     }
                    }else{
                         if(in_array($status,$explode)){
                             $flag = 1;
                             $finalEmailArray[] = $data->email;
                         }
                    }
                }  
            }
        }

        $finalArray = ['flag'=>$flag,'email'=>$finalEmailArray,'total'=>count($getStatusAgencyNotification)];
       
        return  $finalArray;
    }
}
