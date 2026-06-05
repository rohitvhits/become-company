<?php

namespace App\Http\Controllers;

use App\Model\Patient;
use App\Services\PatientServicesRequest;

class CronjobForPatientServiceRequestController extends Controller
{
    protected $patientServicesRequest= "";
	public function __construct(PatientServicesRequest $patientServicesRequest)
	{
        $this->patientServicesRequest  =$patientServicesRequest;
    }

    function checkAllStatus(){
		$commonStatus = [];
		$notStatus = [];
		$query = Patient::select('id','status','completed_date','completed_by')->where('deleted_flag','N')->inRandomOrder()->limit(1000)->orderBy('id','desc')->get();
	
		foreach($query as $pt){
			$getLastServiceId = $this->patientServicesRequest->lastServiceRequestedByPatientId($pt->id);
			if(isset($getLastServiceId->id)){
				
				if(strtolower($getLastServiceId->status) ==strtolower($pt->status)){
					$commonStatus[] = $getLastServiceId->id;
				}else{
					
					if($pt->status =='completed'){
						$this->patientServicesRequest->update(array('status'=>$pt->status,'completed_date'=>$pt->completed_date,'completed_by'=>$pt->completed_by),array('id'=>$getLastServiceId->id));
					}else{
						$this->patientServicesRequest->update(array('status'=>$pt->status),array('id'=>$getLastServiceId->id));
					}
					$notStatus[] = $getLastServiceId->id;
				}
			}
			
		}
	}

}