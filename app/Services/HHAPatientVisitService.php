<?php
namespace App\Services;

use App\Model\HHAPatientVisit;
use Illuminate\Support\Facades\DB;

class HHAPatientVisitService{

    public static function getHHAVisitList($patientId,$startDate,$endDate){
        return HHAPatientVisit::with(['caregiverDetails:id,first_name,last_name'])->where('patient_id',$patientId)->whereDate('visit_date','>=',$startDate)->whereDate('visit_date','<=',$endDate)->get();
    }
	
}   