<?php

namespace App\Helpers;
use App\Model\HhaAppointment;
use App\Model\HHACaregivers;

class HHAAppointmentHelper
{
    public function __construct()
    {
    }



    public static  function insert($data)
    {
        $insert_data = $data;
        $inser_id = new HhaAppointment($insert_data);
        $inser_id->save();
        $Insert = $inser_id->id;

        return $Insert;
    }
    public static  function update($data, $where)
    {
        $insert = HhaAppointment::where($where)->update($data);
        return $insert;
    }

    public static function hhaAppoitnmentListOld($agency_fk, $fname, $code, $caregiver_phone, $hha_code, $medical_name, $due_date, $status)
    {
  
        $agencyids = Utility::getUserWiseAgency();

        $query = HhaAppointment::select('hha_appointment.agency_id', 'hha_appointment.patient_id', 'hha_appointment.id', 'hha_caregivers.first_name as caregiver_first_name', 'hha_caregivers.last_name as caregiver_last_name', 'hha_caregivers.caregiver_code', 'hha_caregivers.mobile_or_sms as caregiver_phone', 'hha_appointment.hha_code', 'hha_appointment.medical_name', 'hha_appointment.due_date', 'ag.agency_name','hha_caregivers.EmploymentTypesDiscipline','hha_caregivers.TeamName','hha_caregivers.dob')
            ->leftjoin('agency as ag', function ($join) {
                $join->on('ag.id', '=', 'hha_appointment.agency_id');
                $join->where('ag.delete_flag', 'N');
            })
            ->leftjoin('hha_caregivers', function ($join) {
                $join->on('hha_caregivers.caregiver_id', '=', 'hha_appointment.caregiver_id');
                $join->whereNull('hha_caregivers.deleted_at');
            })
            ->where('hha_appointment.del_flag', 'N');
            if(!empty($agencyids)){
                $query->whereIn('hha_appointment.agency_id',$agencyids);
            }
        if ($agency_fk != '') {
            $query->where('hha_appointment.agency_id', $agency_fk);
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
            $query->whereRaw('hha_appointment.medical_name LIKE "%' . $medical_name . '%"');
        }
        if ($due_date != '') {
            $explode = explode('-', $due_date);
            date('Y-m-d', strtotime($explode[1]));
            $query->whereDate('hha_appointment.due_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('hha_appointment.due_date', '<=', date('Y-m-d', strtotime($explode[1])));
        } else {
            $query->whereDate('hha_appointment.due_date', '<=', date('Y-m-d', strtotime('+180 days')));
        }

        if ($status != '') {
            if ($status == 'Pending') {
                $query->whereNull('hha_appointment.patient_id');
            }
            if ($status == 'Booked') {
                $query->whereNotNull('hha_appointment.patient_id');
            }
        }
        $query->where('hha_appointment.status', '!=', "Completed");
        //echo $query->toSql(); die();
        //echo $query->toSQL(); die();  



        $query = $query->groupBy('hha_appointment.id')->orderBy('hha_appointment.due_date', 'asc')->paginate(50);
        return $query;
    }

    public static function hhaAppoitnmentList($agency_fk, $fname, $code, $office_id,$medical_name, $due_date, $status="",$caregiver_status="",$date_perform="",$hire_date="",$employment_type="",$paginate="")
    {
      
       
        $agencyids = Utility::getUserWiseAgency();

        $query = HhaAppointment::select('hha_appointment.id','hha_appointment.updated_date','hha_appointment.patient_id','hha_appointment.medical_name','hha_appointment.caregiver_id','hha_appointment.agency_id','hha_appointment.due_date','hha_appointment.status','hha_appointment.office_id','hha_appointment.date_perform','hha_caregivers.caregiver_id as caregiverID','hha_caregivers.first_name','hha_caregivers.middle_name','hha_caregivers.last_name','hha_caregivers.caregiver_code','hha_caregivers.mobile_or_sms','hha_caregivers.dob','hha_caregivers.EmploymentTypesDiscipline','hha_caregivers.TeamName','hha_caregivers.first_work_date','hha_caregivers.last_work_date','hha_caregivers.language','hha_caregivers.status as caregiverStatus','hha_caregivers.hire_date','hha_caregivers.employment_type')->with(['agencyDetails:id,agency_name','hhaOffices:office_id,office_name,office_code'])->where('hha_appointment.del_flag','N')->where('hha_appointment.status', '!=', "Completed");
       
        if(!empty($agencyids)){
            $query->whereIn('hha_appointment.agency_id',$agencyids);
        }
        if ($agency_fk != '') {
            $query->where('hha_appointment.agency_id', $agency_fk);
        }
        $query->leftjoin('hha_caregivers',function($join){
            $join->on('hha_caregivers.caregiver_id','=','hha_appointment.caregiver_id');
            $join->where('hha_caregivers.hha_delete_flag','N')->whereRaw('hha_caregivers.agency_fk = hha_appointment.agency_id');
        });

        if($fname !=""){
           
            $query->whereRaw('CONCAT(hha_caregivers.first_name,hha_caregivers.middle_name,hha_caregivers.last_name) LIKE "%' . str_replace(" ","",$fname) . '%"');
        }

        if($code !=""){
           $explode = explode('-',$code);
           $caregiverCode = $explode[0];
            if(isset($explode[1])){
                $caregiverCode = $explode[1];
            }

            $query->whereRaw('hha_caregivers.caregiver_code ="' . trim($caregiverCode) . '"');
        }
    
        if(!empty($caregiver_status[0])){
      
            $query->whereIn('hha_caregivers.status',$caregiver_status);
        }
        
        if ($medical_name != '') {
            $query->whereRaw('hha_appointment.medical_name LIKE "%' . $medical_name . '%"');
        }
        if ($office_id != '') {
            $query->where('hha_appointment.office_id',$office_id);
        }

        if ($due_date != '') {
            $explode = explode('-', $due_date);
            $startDate = date('Y-m-d', strtotime($explode[0])).' 00:00:00';
            $endDate = date('Y-m-d', strtotime($explode[1])).' 23:59:59';
            $query->where('hha_appointment.due_date', '>=', $startDate)->where('hha_appointment.due_date', '<=', $endDate);
        } else {
            $query->where('hha_appointment.due_date', '<=', date('Y-m-d', strtotime('+180 days')));
        }
        if ($date_perform != '') {
            $explode = explode('-', $date_perform);
            $startDate = date('Y-m-d', strtotime($explode[0])).' 00:00:00';
            $endDate = date('Y-m-d', strtotime($explode[1])).' 23:59:59';
            $query->where('hha_appointment.date_perform', '>=', $startDate)->where('hha_appointment.date_perform', '<=',$endDate);
        }

        if (!empty($hire_date)) {
            $explode = explode('-', $hire_date);
            $startDate = date('Y-m-d', strtotime($explode[0]));
            $endDate = date('Y-m-d', strtotime($explode[1]));
            $query->where('hha_caregivers.hire_date', '>=', $startDate)->where('hha_caregivers.hire_date', '<=',$endDate);
        }

        if ($employment_type != '') {
            $query->where('hha_caregivers.employment_type', 'LIKE', "%".$employment_type."%");
        }

        if ($status != '') {
            if ($status == 'Pending') {
                $query->whereNull('hha_appointment.patient_id');
            }
            if ($status == 'Booked') {
                $query->whereNotNull('hha_appointment.patient_id');
            }
        }
    
        if($paginate !=""){
            $query = $query->orderBy('hha_appointment.due_date', 'asc')->get();
        }else{
            $query = $query->orderBy('hha_appointment.due_date', 'asc')->simplePaginate(50);
        }

        return $query;
    }

    public static function  getDetailsById($id)
    {
        $query = HhaAppointment::select('hha_appointment.id','hha_appointment.caregiver_id', 'hha_appointment.agency_id', 'hha_appointment.medical_id', 'hha_caregivers.first_name as caregiver_first_name', 'hha_caregivers.last_name as caregiver_last_name', 'hha_caregivers.middle_name as caregiver_middle_name', 'hha_caregivers.caregiver_code', 'hha_caregivers.mobile_or_sms as caregiver_phone', 'hha_caregivers.gender', 'hha_caregivers.dob', 'hha_appointment.hha_code', 'hha_appointment.medical_name', 'hha_appointment.due_date', 'ag.agency_name','hha_caregivers.address1','hha_caregivers.address2','hha_caregivers.City','hha_caregivers.State','hha_caregivers.Zip5')
            ->leftjoin('agency as ag', function ($join) {
                $join->on('ag.id', '=', 'hha_appointment.agency_id');
                $join->where('ag.delete_flag', 'N');
            })
            ->leftjoin('hha_caregivers', function ($join) {
                $join->on('hha_caregivers.caregiver_id', '=', 'hha_appointment.caregiver_id');
                $join->whereNull('hha_caregivers.deleted_at');
            })
            ->where('hha_appointment.del_flag', 'N')->where('hha_appointment.patient_id', '=', null);

        $query = $query->where('hha_appointment.id', $id)->first();
        return $query;
    }

    public static function getDetailsByIdWithoutJoin($id)
    {
        $query = HhaAppointment::where('del_flag', 'N')->where('patient_id', $id)->first();
        return $query;
    }
    public static function getDetailsByPatientIdMedicalId($id, $medicalId)
    {
        $query = HhaAppointment::where('del_flag', 'N')->where('patient_id', $id)->where('medical_id', $medicalId)->first();
        return $query;
    }

    public static function getById($id){
        $query = HhaAppointment::where('del_flag', 'N')->where('id',$id)->first();
        return $query;
    }

    public static function getByIdNewId($id){
        $query = HhaAppointment::with(['agencyDetails'])->whereRaw('id ="'.$id.'"')->first();

        if(isset($query->id)){
            $query   = $query;
        }else{
            $query = HhaAppointment::with(['agencyDetails'])->where('del_flag', 'N')->whereRaw('caregiver_id ="'.$id.'"')->first();
        
            if(isset($query->id)){
                $query   = $query;
            }else{
                
                $query  =HHACaregivers::with(['agencyDetails'])->whereNull('deleted_at')->whereRaw('caregiver_id ="'.$id.'"')->first();
            }
        }
        
        return $query;
    }

    public static function getCaregiverOrNot($cid,$agencyId){
        return HhaAppointment::where('caregiver_id',$cid)->where('agency_id',$agencyId)->first();
    }
}
