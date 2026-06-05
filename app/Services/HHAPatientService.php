<?php
namespace App\Services;

use App\Model\HHAPatient;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
class HHAPatientService{

	public static  function update($data, $where)
    {
        return HHAPatient::where($where)->update($data);
    }

	public static function getList($agency_fk,$full_name,$admission_id,$home_phone,$coordinator_name,$service_start_date,$dob,$status,$sorting_column,$sorting_order,$hhasyncdatetime,$paginate=""){
		$agencyids = Utility::getUserWiseAgency();
		$query = HHAPatient::select('hha_patients.*','hha_office.office_name','hha_office.office_code')->with(['agencyDetail:id,agency_name'])
		->leftjoin('hha_office',function($join){
			$join->on('hha_office.office_id','=','hha_patients.officeId');
		});
		$query->whereNull('hha_patients.deleted_at');
		if(!empty($agencyids)){
			$query->whereIn('hha_patients.agency_fk',$agencyids);

		}
			if($agency_fk !=""){
				$query->where('hha_patients.agency_fk',$agency_fk);
			}
			if($full_name !=""){
				$query->whereRaw('CONCAT(hha_patients.first_name,hha_patients.last_name) LIKE "%'.trim(str_replace(' ','',$full_name)).'%"');
			}
			if($admission_id !=""){
				$query->where('hha_patients.admission_id',$admission_id);
			}
			if($home_phone !=""){
				$query->where('hha_patients.home_phone',$home_phone);
			}
			if($coordinator_name !=""){
				$query->where('hha_patients.coordinator_name','LIKE','%'.$coordinator_name.'%');
			}
			if($service_start_date !=""){
				$explode = explode('-',$service_start_date);
				$query->whereDate('hha_patients.service_start_date','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('hha_patients.service_start_date','<=',date('Y-m-d',strtotime($explode[1])));
			}

			if($hhasyncdatetime !=""){
				$explode = explode('-',$hhasyncdatetime);
				$query->whereDate('hha_patients.hhasyncdatetime','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('hha_patients.hhasyncdatetime','<=',date('Y-m-d',strtotime($explode[1])));
			}
			if($dob !=""){
				
				$query->whereDate('hha_patients.dob','=',date('Y-m-d',strtotime($dob)));
			}
			if($status !=""){
				if($status =='Booked'){
					$query->whereNotNull('hha_patients.patient_record_id');
				}else{
					$query->whereNull('hha_patients.patient_record_id');
				}
				
			}

			$query->groupBy('hha_patients.patient_id');
			if($sorting_column =='full_name'){
				if($paginate !=""){
					$query = $query->orderByRaw("CONCAT(hha_patients.first_name,  hha_patients.last_name) ".$sorting_order)->get();
				}else{
					$query = $query->orderByRaw("CONCAT(hha_patients.first_name,  hha_patients.last_name) ".$sorting_order)->paginate(50);
				}
				
			}else{
				if($paginate !=""){
					$query = $query->orderBy('hha_patients.'.$sorting_column,$sorting_order)->get();
				}else{
					$query = $query->orderBy('hha_patients.'.$sorting_column,$sorting_order)->paginate(50);
				}
				
			}
	
		return $query;
	}

	public function getDetailsById($id){
		return HHAPatient::where('id',$id)->orWhere('patient_id',$id)->first();
	}
	public function getDetailsWithAgencyRelationShip($id){
		return HHAPatient::with(['agencyDetail:id,agency_name,app_name,app_key,app_token'])->where('id',$id)->orWhere('patient_id',$id)->first();
	}

	public function totalHHAPatientCount(){
		return HHAPatient::whereNull('deleted_at')->count();
	}

	public function totalHHAPatientCountDateWise($from_date,$to_date){
		$query = HHAPatient::where('hha_delete_flag','N');
        if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('created_at', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		return $query->count();
	}

	public function getDetailsByPkId($id){
		return HHAPatient::where('id',$id)->where('hha_delete_flag','N')->first();
	}

	public function getDetailsByPatientID($pid,$agencyID){
		return HHAPatient::where('patient_id',$pid)->where('agency_fk',$agencyID)->where('hha_delete_flag','N')->first();
	}

	public function getDetailsWithAgencyRelationShipUsingAgency($id,$agencyID){
		return HHAPatient::with(['agencyDetail:id,agency_name,app_name,app_key,app_token'])->where('id',$id)->where('agency_fk',$agencyID)->orWhere('patient_id',$id)->first();
	}

	public function getDetailsByOnlyPatientId($pid){
		return HHAPatient::where('patient_id',$pid)->where('hha_delete_flag','N')->first();
	}

	public function getPatientDetailsWithWithAgencyId($agencyId,$office_id){
		return HHAPatient::whereRaw('SHA1(agency_fk) = "'.$agencyId.'"')->where('officeId',$office_id)->where('hha_delete_flag','N')->first();
	}
}