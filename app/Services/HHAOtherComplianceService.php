<?php
namespace App\Services;

use App\Helpers\Utility;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\HhaOtherComplience;

class HHAOtherComplianceService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";
		
		$insert = new HhaOtherComplience($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =HhaOtherComplience::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =HhaOtherComplience::where($where)->update($data); 
		return $update;
	}


    public  function  hhaAppoitnmentList($agency_fk, $fname, $code, $caregiver_phone, $hha_code, $medical_name, $due_date, $status,$office_id="",$paginate=""){
        $query = HhaOtherComplience::select('hha_other_complience.agency_id','hha_other_complience.caregiver_id','hha_other_complience.patient_id', 'hha_other_complience.id', 'hha_caregivers.first_name as caregiver_first_name', 'hha_caregivers.last_name as caregiver_last_name', 'hha_caregivers.caregiver_code', 'hha_caregivers.mobile_or_sms as caregiver_phone', 'hha_other_complience.hha_code', 'hha_other_complience.medical_name', 'hha_other_complience.due_date', 'ag.agency_name','hha_caregivers.EmploymentTypesDiscipline','hha_caregivers.TeamName','hha_caregivers.dob','hha_office.office_id','hha_office.office_name','hha_office.office_code')
        ->leftjoin('agency as ag', function ($join) {
            $join->on('ag.id', '=', 'hha_other_complience.agency_id');
            $join->where('ag.delete_flag', 'N');
        })
        ->leftjoin('hha_caregivers', function ($join) {
            $join->on('hha_caregivers.caregiver_id', '=', 'hha_other_complience.caregiver_id');
            $join->whereNull('hha_caregivers.deleted_at');
        })
        ->leftjoin('hha_office', function ($join) {
            $join->on('hha_office.office_id', '=', 'hha_other_complience.office_id');
          
        })
        ->where('hha_other_complience.del_flag', 'N');

        $agencyids = Utility::getUserWiseAgency();
        if(!empty($agencyids)){
            $query->whereIn('hha_other_complience.agency_id',$agencyids);
        }
        if ($agency_fk != '') {
            $query->where('hha_other_complience.agency_id', $agency_fk);
        }
        if ($fname != '') {
            $query->whereRaw('CONCAT_WS("",hha_caregivers.first_name," "," ",hha_caregivers.last_name) LIKE "%' . $fname . '%"');
        }
        if ($code != '') {
            $query->whereRaw('hha_caregivers.caregiver_code ="' . $code . '"');
        }
        if ($caregiver_phone != '') {
            $query->whereRaw('hha_caregivers.caregiver_phone ="' . $caregiver_phone . '"');
        }
        if ($hha_code != '') {
            $query->whereRaw('hha_caregivers.hha_code ="' . $hha_code . '"');
        }
        if ($medical_name != '') {
            $query->whereRaw('hha_other_complience.medical_name LIKE "%' . $medical_name . '%"');
        }
        if ($due_date != '') {
            $explode = explode('-', $due_date);
            echo date('Y-m-d', strtotime($explode[1]));
            $query->whereDate('hha_other_complience.due_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('hha_other_complience.due_date', '<=', date('Y-m-d', strtotime($explode[1])));
        } else {
            $query->whereDate('hha_other_complience.due_date', '<=', date('Y-m-d', strtotime('+180 days')));
        }

        if($office_id !=""){
            $query->where('hha_other_complience.office_id',$office_id);
        }
        if ($status != '') {
            if ($status == 'Pending') {
                $query->whereNull('hha_other_complience.patient_id');
            }
            if ($status == 'Booked') {
                $query->whereNotNull('hha_other_complience.patient_id');
            }
        }
        $query->where('hha_other_complience.status', '!=', "Completed");

        if($paginate !=""){
            $query = $query->orderBy('hha_other_complience.due_date', 'asc')->get();
        }else{
            $query = $query->orderBy('hha_other_complience.due_date', 'asc')->paginate(50);
        }
        
        return $query;
    }

    public function getDetailsById($id)
    {
        $query = HhaOtherComplience::selectRaw('*,hha_caregivers.first_name as caregiver_first_name,hha_caregivers.last_name as caregiver_last_name,hha_caregivers.caregiver_code,hha_caregivers.mobile_or_sms as caregiver_phone')->leftjoin('hha_caregivers', function ($join) {
            $join->on('hha_caregivers.caregiver_id', '=', 'hha_other_complience.caregiver_id');
            $join->whereNull('hha_caregivers.deleted_at');
        })->where('hha_other_complience.id', $id)->where('hha_other_complience.del_flag', 'N')->first();
        return $query;
    }

    public function getOtherCompliancebyCaregiverId($cid,$agency_fk){
        return HhaOtherComplience::where('caregiver_id',$cid)->where('agency_id',$agency_fk)->where('del_flag', 'N')->get();
    }
}