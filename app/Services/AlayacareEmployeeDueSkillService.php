<?php
namespace App\Services;

use App\Helpers\Utility;
use App\Model\AlayacareEmployeeSkill;
use Illuminate\Support\Facades\DB;

class AlayacareEmployeeDueSkillService{
    
    public static  function update($data, $where)
    {
        return AlayacareEmployeeSkill::where($where)->update($data);
    }

    public function getList($search,$paginate=""){
        $query = AlayacareEmployeeSkill::select('agency.agency_name','alayacare_employee_skill.*','emp.first_name','emp.last_name','emp.phone','emp.phone','emp.ac_id','emp.birthday','emp.gender as emp_gender','emp.status as emp_status')
        ->leftjoin('alayacare_emp_master as emp',function($join){
            $join->on('emp.id','=','alayacare_employee_skill.alayacare_emp_id');
        })
        ->leftjoin('agency',function($join){
            $join->on('agency.id','=','emp.agency_id');
        })
        ->where('agency.alaycare_status',1)
        ->where('alayacare_employee_skill.del_flag','N');
        if(isset($search['agency_fk']) && $search['agency_fk'] !=""){
            $query->where('emp.agency_id',$search['agency_fk']);
        }
        $agencyids = Utility::getUserWiseAgency();

        if(!empty($agencyids)){
            $query->whereIn('emp.agency_id',$agencyids);
        }

        if(isset($search['full_name']) && $search['full_name'] !=""){
            $query->whereRaw('concat(emp.first_name," ",emp.last_name)  LIKE "%'.$search['full_name'].'%"');
        }
        if(isset($search['code']) && $search['code'] !=""){
            $query->where('emp.emp_id',$search['code']);
        }
        if(isset($search['caregiver_phone']) && $search['caregiver_phone'] !=""){
            $query->where('emp.phone',$search['caregiver_phone']);
        }
        if(isset($search['skill_name']) && $search['skill_name'] !=""){
            $query->where('alayacare_employee_skill.skill_name','LIKE','%'.$search['skill_name'].'%');
        }
        if(isset($search['due_date']) && $search['due_date'] !=""){
            $explode=explode('-',$search['due_date']);
            $query->whereDate('alayacare_employee_skill.due_date','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('alayacare_employee_skill.due_date','<=',date('Y-m-d',strtotime($explode[1])));
        }
        
        if(isset($search['created_date']) && $search['created_date'] !=""){
            $explode=explode('-',$search['created_date']);
            $query->whereDate('alayacare_employee_skill.created_date','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('alayacare_employee_skill.created_date','<=',date('Y-m-d',strtotime($explode[1])));
        }

        if(isset($search['employee_status']) && $search['employee_status'] !=""){
            $query->where('emp.status',$search['employee_status']);
        }

        if(isset($search['status']) && $search['status'] !=""){
            if($status =='Booked'){
                $query->whereNotNull('alayacare_employee_skill.patient_id');
            }else{
                $query->whereNull('alayacare_employee_skill.patient_id');
            }
        }

        if($paginate !=""){
            $query=$query->orderBy('id','desc')->get();
        }else{
            $query=$query->orderBy('id','desc')->paginate(50);
        }

        return $query;
    }

    public function getDetailsById($id){
        return  AlayacareEmployeeSkill::with(['employeeDetails'])->where('id',$id)->first();
    }

    public function checkForLinkPatient($empId,$agencyId){
   
        return AlayacareEmployeeSkill::select('patient_id')->where('employee_id',$empId)->where('agency_id',$agencyId)->whereNotNull('patient_id')->get();
    }

    public function getTotalSyncDueSkill(){
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $start = $yesterday.' 00:00:00';
        $end   = $yesterday.' 23:59:59';
        
        return AlayacareEmployeeSkill::from('alayacare_employee_skill as aes')
        ->join('agency as a', 'a.id', '=', 'aes.agency_id')
        ->select(
            'aes.agency_id',
            'a.agency_name',

            // overall
            DB::raw("SUM(aes.del_flag = 'N') as overall"),

            DB::raw("SUM(
                aes.del_flag = 'N'
                AND aes.created_date >= '{$start}'
                AND aes.created_date < '{$end}'
            ) as new_records"),

            DB::raw("SUM(
                aes.del_flag = 'N'
                AND aes.updated_at >= '{$start}'
                AND aes.updated_at < '{$end}'
            ) as synced_records")
        )
        ->groupBy('aes.agency_id', 'a.agency_name')
        ->orderBy('aes.agency_id')
        ->get();
    }
}