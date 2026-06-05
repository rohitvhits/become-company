<?php

namespace App\Services;

use App\Model\HHADueMedical;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Utility;
class HHADueMedicalService
{
    protected const COMMON_DATE ='Y-m-d H:i:s';

    public function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] =  Carbon::now()->format(self::COMMON_DATE);
		$data['updated_by'] = $auth['id'];
		return HHADueMedical::where($where)->update($data);
	}

    public static function dueMedicalList($search,$paginate="")
    {
        if($paginate !=""){
            $caregiver_status =explode(',',$search['caregiver_status']);
        }else{
            $caregiver_status =$search['caregiver_status'];
        }

        $agencyids = Utility::getUserWiseAgency();

        $query = HHADueMedical::select('hha_due_medical.id','hha_due_medical.updated_date','hha_due_medical.patient_id','hha_due_medical.medical_name','hha_due_medical.caregiver_id','hha_due_medical.agency_id','hha_due_medical.due_date','hha_due_medical.status','hha_due_medical.office_id','hha_due_medical.date_perform','hha_caregivers.caregiver_id as caregiverID','hha_caregivers.first_name','hha_caregivers.middle_name','hha_caregivers.last_name','hha_caregivers.caregiver_code','hha_caregivers.mobile_or_sms','hha_caregivers.dob','hha_caregivers.EmploymentTypesDiscipline','hha_caregivers.TeamName','hha_caregivers.first_work_date','hha_caregivers.last_work_date','hha_caregivers.language','hha_caregivers.status as caregiverStatus','hha_caregivers.hire_date','hha_caregivers.employment_type','patient_master.id as patientId')->with(['agencyDetails:id,agency_name','hhaOffices:office_id,office_name,office_code'])->where('hha_due_medical.del_flag','N')->where('hha_due_medical.status', '!=', "Completed");
       
        if(!empty($agencyids)){
            $query->whereIn('hha_due_medical.agency_id',$agencyids);
        }
        if ($search['agency_fk'] != '') {
            $query->where('hha_due_medical.agency_id', $search['agency_fk']);
        }
        $query->leftjoin('hha_caregivers',function($join){
            $join->on('hha_caregivers.caregiver_id','=','hha_due_medical.caregiver_id');
            $join->where('hha_caregivers.hha_delete_flag','N')->whereRaw('hha_caregivers.agency_fk = hha_due_medical.agency_id');
        });
        $query->leftjoin('patient_master',function($join){
            $join->on('patient_master.link_hha_caregiver','=','hha_due_medical.caregiver_id');
            $join->where('patient_master.deleted_flag','N')->whereRaw('patient_master.agency_id = hha_due_medical.agency_id');
        });
        if($search['fname'] !=""){
            $query->whereRaw('CONCAT(hha_caregivers.first_name,hha_caregivers.middle_name,hha_caregivers.last_name) LIKE "%' . str_replace(" ","",$search['fname']) . '%"');
        }

        if($search['code'] !=""){
           $explode = explode('-',$search['code']);
           $caregiverCode = $explode[0];
            if(isset($explode[1])){
                $caregiverCode = $explode[1];
            }
            $query->whereRaw('hha_caregivers.caregiver_code ="' . trim($caregiverCode) . '"');
        }
    
        if(!empty($caregiver_status[0])){
            $query->whereIn('hha_caregivers.status',$caregiver_status);
        }
        
        if ($search['medical_name'] != '') {
            $query->whereRaw('hha_due_medical.medical_name LIKE "%' . $search['medical_name'] . '%"');
        }
        if ($search['office_id'] != '') {
            $query->where('hha_due_medical.office_id',$search['office_id']);
        }

        if ($search['due_date'] != '') {
            $explode = explode('-', $search['due_date']);
            $startDate = date('Y-m-d', strtotime($explode[0])).' 00:00:00';
            $endDate = date('Y-m-d', strtotime($explode[1])).' 23:59:59';
            $query->where('hha_due_medical.due_date', '>=', $startDate)->where('hha_due_medical.due_date', '<=', $endDate);
        } else {
            $query->where('hha_due_medical.due_date', '<=', date('Y-m-d', strtotime('+180 days')));
        }
        if ( $search['date_perform'] != '') {
            $explode = explode('-', $search['date_perform']);
            $startDate = date('Y-m-d', strtotime($explode[0])).' 00:00:00';
            $endDate = date('Y-m-d', strtotime($explode[1])).' 23:59:59';
            $query->where('hha_due_medical.date_perform', '>=', $startDate)->where('hha_due_medical.date_perform', '<=',$endDate);
        }

        if (!empty($search['hire_date'])) {
            $explode = explode('-', $search['hire_date']);
            $startDate = date('Y-m-d', strtotime($explode[0]));
            $endDate = date('Y-m-d', strtotime($explode[1]));
            $query->where('hha_caregivers.hire_date', '>=', $startDate)->where('hha_caregivers.hire_date', '<=',$endDate);
        }

        if ($search['employment_type'] != '') {
            $query->where('hha_caregivers.employment_type', 'LIKE', "%".$search['employment_type']."%");
        }

        if ($search['status'] != '') {
            if ($search['status'] == 'Pending') {
                $query->whereNull('hha_due_medical.patient_id');
            }
            if ($search['status'] == 'Booked') {
                $query->whereNotNull('hha_due_medical.patient_id');
            }
        }
    
        if($paginate !=""){
            $query = $query->orderBy('hha_due_medical.due_date', 'asc')->get();
        }else{
            $query = $query->orderBy('hha_due_medical.due_date', 'asc')->simplePaginate(50);
        }

        return $query;
    }

    public function getDetailsById($id){
        return HHADueMedical::select('hha_due_medical.id','hha_due_medical.caregiver_id', 'hha_due_medical.agency_id', 'hha_due_medical.medical_id', 'hha_caregivers.first_name as caregiver_first_name', 'hha_caregivers.last_name as caregiver_last_name', 'hha_caregivers.middle_name as caregiver_middle_name', 'hha_caregivers.caregiver_code', 'hha_caregivers.mobile_or_sms as caregiver_phone', 'hha_caregivers.gender', 'hha_caregivers.dob', 'hha_due_medical.hha_code', 'hha_due_medical.medical_name', 'hha_due_medical.due_date', 'ag.agency_name','hha_caregivers.address1','hha_caregivers.address2','hha_caregivers.City','hha_caregivers.State','hha_caregivers.Zip5')
        ->leftjoin('agency as ag', function ($join) {
            $join->on('ag.id', '=', 'hha_due_medical.agency_id');
            $join->where('ag.delete_flag', 'N');
        })
        ->leftjoin('hha_caregivers', function ($join) {
            $join->on('hha_caregivers.caregiver_id', '=', 'hha_due_medical.caregiver_id');
            $join->whereNull('hha_caregivers.deleted_at');
        })
        ->where('hha_due_medical.id',$id)->where('del_flag','N')->where('hha_due_medical.patient_id', '=', null)->first();
    }
}