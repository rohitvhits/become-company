<?php

namespace App\Services;

use App\Model\Patient;
use Illuminate\Support\Facades\DB;
class ExpiringMedicalService
{

    public function getData($agencyId,$type="",$status="")
	{
		
		$auth = auth()->user();

        if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			//and patient_master.archived_at IS NULL
		} else {
			$addCondition="";
			
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"'.$addCondition.'';
		}

		//$where .=' and agency_id='.$agencyId;
		if($agencyId !=""){
			$where .=" and patient_master.agency_id ='".$agencyId."'";
		}
		if($status !=""){
			$where .=" and patient_master.status ='".$status."'";
		}
        $edate=date('Y-m-d',strtotime('+9 days'));
        $where .=' and patient_master.due_date   >="'.date('Y-m-d').'"   and   patient_master.due_date  <="'.$edate.'"  and type="Caregiver"';
		if($type !=""){
			$query = Patient::with(['users','assignToUser','agencyDetail','hhaAppoinmets.hhaCaregivers'])->select('patient_master.*')
			->whereRaw($where)->orderBy('patient_master.id', 'desc')->get();
		}else{
			$query = Patient::with(['users','assignToUser','agencyDetail','hhaAppoinmets.hhaCaregivers'])->select('patient_master.*')
			->whereRaw($where)->orderBy('patient_master.id', 'desc')->paginate(20);
		}
        
		return $query;

    }
}   