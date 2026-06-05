<?php
namespace App\Services;

use App\Model\HHACaregivers;
use App\Helpers\Utility;
class HHACaregiverService{

    public function ajaxList($search){
        $agencyids = Utility::getUserWiseAgency();
        $query = HHACaregivers::with('agencyDetails:id,agency_name');
                if(!empty($search['agency_fk'])){
                    $query->whereRaw('SHA1(agency_fk) ="'.$search['agency_fk'].'"');
                }
        
                if(!empty($search['full_name'])){
                    $query->whereRaw('CONCAT(first_name," ",last_name) LIKE "%'.$search['full_name'].'%"');
                }
                if(!empty($search['code'])){
                    $query->whereRaw('caregiver_code = "'.$search['code'].'"');
                }
                
                if(!empty($search['caregiver_phone'])){
                    $query->whereRaw('mobile_or_sms = "'.$search['caregiver_phone'].'"');
                }
                
                if(!empty($search['last_work_date'])){
                    $explode = explode('-',$search['last_work_date']);
                    $query->whereRaw('last_work_date >= "'.date('Y-m-d',strtotime($explode[0])).'" and last_work_date <= "'.date('Y-m-d',strtotime($explode[1])).'"');
                }

                if(!empty($search['dob'])){
                    $query->whereRaw('dob = "'.date('Y-m-d',strtotime($search['dob'])).'"');
                }
                if(!empty($search['gender'])){
                    $query->whereRaw('gender = "'.$search['gender'].'"');
                }

                if(count($agencyids) >0){
                    $query->whereIn('agency_fk',$agencyids);
                }
        return $query = $query->orderBy($search['sortingColumn'],$search['sortingOrder'])->paginate(50);
    }
	
    public function totalHHACaregiverCount(){
		return HHACaregivers::where('hha_delete_flag','N')->count();
	}

    public function totalHHACaregiverCountDateWise(){
		$query = HHACaregivers::where('hha_delete_flag','N');
        if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('created_at', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		return $query->count();
	}

    public function ajaxSyncData($search){
        $query =  HHACaregivers::selectRaw('agency_fk,COUNT(id) as total')->with(['agencyDetails:id,agency_name'])->whereDate('last_medical_sync','<',date('Y-m-d',strtotime('-6 days')));

        if(isset($search['agency_id']) && $search['agency_id'] !='null'){

            $query->whereIn('agency_fk',$search['agency_id']);
        }

        $query = $query->groupBy('agency_fk')->orderBy('total','desc')->paginate(50);
        return $query;
    }

    public function getDetailByIdWithAgencyFk($id,$agencyId){
        return HHACaregivers::where('caregiver_id',$id)->where('hha_delete_flag','N')->where('agency_fk',$agencyId)->first();
    }
}