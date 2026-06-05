<?php

namespace App\Helpers;
use App\Services\AppointmentPortalMergeLogsService;
use App\Services\PatientService;
class MergeUtilityHelper
{
    public static function convertData($data)
    {
        $appointmentMergeLogsService = new AppointmentPortalMergeLogsService();
        $patientService = new PatientService();
        $explode = explode(',',$data['merge_id']);
        
		$finalAllMergeIds =[];
		if(!empty($explode[0])){
			foreach($explode as $id){
                $flag = 1;
                if($data['del_flag'] =='Y'){
                    $getDetails = $patientService->getDetailByIdNew($id);
                    if(isset($getDetails->id)){
                        $flag = 0;
                    }
                }

                if($flag ==1){
                    $finalAllMergeIds[] = $id;
                    $mergePortalId = $appointmentMergeLogsService->getMainPortalIds($id);
                    if(!empty($mergePortalId[0])){
                        $mergePortalIds = array_column($mergePortalId->toArray(), 'merge_patient_id');
                        $finalAllMergeIds = array_merge($finalAllMergeIds, $mergePortalIds);
                    }
                }
				
			}
		}
		$finalAllMergeIds[] = $data['currentId'];
		return array_values(array_unique($finalAllMergeIds));
    }
}