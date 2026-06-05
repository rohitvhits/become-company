<?php
namespace App\Services;

use App\Helpers\Utility;
use App\Model\AlayacareEmployee;
use App\Model\AlayacareClient;

class AlayacareService{

	public function getAllDetailsByAlayacreId($id,$agencyId=""){
        $query= AlayacareEmployee::with(['agencyDetails'])->where('emp_id',$id);
        if($agencyId !=""){
            $query->where('agency_id',$agencyId);
        }
        $query = $query->first();
        return $query;
    }
    public function getClientDetailsByAlayacreId($id){

        return AlayacareClient::with(['agencyDetails'])->where('client_id',$id)->first();
    }

    public function getAllAlaycareEmployee($search,$paginate="") {
        $query = AlayacareEmployee::with(['agencyDetails'])
        ->whereHas('agencyDetails',function($query){
            $query->where('alaycare_status',1);
        });
       
        if (isset($search['email']) && $search['email'] !="") {
            $query->where('email', 'like', '%' . $search['email'] . '%');
        }
    
        if (isset($search['first_name']) && $search['first_name'] !="") {
            $query->where('first_name', 'like', '%' . $search['first_name'] . '%');
        }
    
        if (isset($search['last_name']) && $search['last_name'] !="") {
            $query->where('last_name', 'like', '%' .$search['last_name'] . '%');
        }
    
        if (isset($search['job_title']) && $search['job_title'] !="") {
            $query->where('job_title', 'like', '%' . $search['job_title'] . '%');
        }

        if (isset($search['phone_no']) && $search['phone_no'] !="") {
            $query->where('phone', $search['phone_no']);
        }

        if (isset($search['branch_name']) && $search['branch_name'] !="") {
            $query->where('branch_name', 'like', '%' . $search['branch_name'] . '%');
        }
        $agencyids = Utility::getUserWiseAgency();
        if(!empty($agencyids)){
            $query->whereIn('agency_id',$agencyids);
		}

        if (isset($search['agency_name']) && $search['agency_name'] !="") {
            $query->where('agency_id', $search['agency_name']);
        }

        if (isset($search['employee_status']) && $search['employee_status'] != '') {
            $query->where('status', $search['employee_status']);
        }

        if (isset($search['status']) && $search['status'] != '') {
            if ($search['status'] == 'Pending') {
                $query->whereNull('patient_id');
            }
            if ($search['status'] == 'Booked') {
                $query->whereNotNull('patient_id');
            }
        }

        if (isset($search['created_date']) && $search['created_date'] != '') {
            $dates = explode(' - ', $search['created_date']);
            if (count($dates) == 2) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime(trim($dates[0]))));
                $query->whereDate('created_at', '<=', date('Y-m-d', strtotime(trim($dates[1]))));
            }
        }

        if (isset($search['last_skill_sync_date']) && $search['last_skill_sync_date'] != '') {
            $dates = explode(' - ', $search['last_skill_sync_date']);
            if (count($dates) == 2) {
                $query->whereDate('last_sync_skill_date', '>=', date('Y-m-d', strtotime(trim($dates[0]))));
                $query->whereDate('last_sync_skill_date', '<=', date('Y-m-d', strtotime(trim($dates[1]))));
            }
        }

        $query->orderBy('id', 'desc');
        if($paginate !=""){
            return $query->get();
        }
        return $query->paginate(50);
    }

    public static function getDataExport($email,$firstName,$lastName,$jobTitle,$phone,$branchName,$agencyName)
	{

        $query = AlayacareEmployee::with(['agencyDetails'])->whereHas('agencyDetails',function($query){
            $query->where('alaycare_status',1);
        });    
    
        if ($email) {
            $query->where('email', 'like', '%' . $email . '%');
        }
    
        if ($firstName) {
            $query->where('first_name', 'like', '%' . $firstName . '%');
        }

        $agencyids = Utility::getUserWiseAgency();
        if(!empty($agencyids)){
			$query->whereIn('agency_id',$agencyids);
		}
    
        if ($lastName) {
            $query->where('last_name', 'like', '%' . $lastName . '%');
        }
    
        if ($jobTitle) {
            $query->where('job_title', 'like', '%' . $jobTitle . '%');
        }

        if ($phone) {
            $query->where('phone', 'like', '%' . $phone . '%');
        }

        if ($branchName) {
            $query->where('branch_name', 'like', '%' . $branchName . '%');
        }

        if ($agencyName) {
            $query->where('agency_name', 'like', '%' . $agencyName . '%');
        }

    
        $query->orderBy('id', 'desc');
    
        return $query->get();
	}

    public function getRandomEmployeeList(){
        
        return AlayacareEmployee::select('emp_id','agency_id')->with(['agencyDetails:id,alaycare_username,alaycare_password'])->where('del_flag','N')->inRandomOrder()->limit(100)->get();
    }

    public function totalSyncEmployeeDetails($id){
        return AlayacareEmployee::where('del_flag','N')->where('agency_id',$id)->where('demographic_updated_flag','N')->get();
    }
    
    public function totalAlayacareEmp(){
        return AlayacareEmployee::where('del_flag','N')->count();
    }

    public function getDetailsByID($agencyId,$empId){
        return AlayacareEmployee::where('del_flag','N')->where('agency_id',$agencyId)->where('emp_id',$empId)->first();
    }

    public function alayacareEmployeeUpdate($data,$empId,$agencyId){
        return AlayacareEmployee::updateOrCreate(
            [  'emp_id' => $empId,
                'agency_id' => $agencyId],
            $data
        );
    }

    public function getDetailsByWithAgencyId($agencyId){
        return AlayacareEmployee::where('del_flag','N')->where('agency_id',$agencyId)->pluck('emp_id');
    }

    public function getRemainingDemographicDetails(){
        return AlayacareEmployee::where('demographic_updated_flag','N')->whereNull('first_name')->inRandomOrder()->limit(500)->get();
    }

    public function getEmployeeListByAgencyId($agencyId, $page = 1){
        return AlayacareEmployee::where('del_flag','N')->where('agency_id',$agencyId)->paginate(200, ['*'], 'page', $page);
    }
}