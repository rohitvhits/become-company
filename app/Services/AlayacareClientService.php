<?php
namespace App\Services;
use App\Model\AlayacareClient;
use App\Helpers\Utility;
class AlayacareClientService{

    public  function getDataExportClient($firstName,$lastName,$branchName,$phoneNo,$city,$state,$gender,$status,$clientStatus='')
	{

        $agencyids = Utility::getUserWiseAgency();
        $query = AlayacareClient::with('agencyDetails')
        ->whereHas('agencyDetails',function($query){
            $query->where('alaycare_status',1);
        });
        if(!empty($agencyids)){
			$query->whereIn('agency_id',$agencyids);
		}
        if ($firstName) {
            $query->where('first_name', 'like', '%' . $firstName . '%');
        }
    
        if ($lastName) {
            $query->where('last_name', 'like', '%' . $lastName . '%');
        }

        if ($branchName) {
            $query->where('branch_name', 'like', '%' . $branchName . '%');
        }

        if($phoneNo) {
            $query->where('phone_main', 'like', '%' . $phoneNo . '%');
        }

        if($city) {
            $query->where('city', 'like', '%' . $city . '%');
        }

        if($state) {
            $query->where('state', 'like', '%' . $state . '%');
        }

        if($gender) {
            $query->where('gender', 'like', '%' . $gender . '%');
        }

        if($status) {
            $query->where('status', 'like', '%' . $status . '%');
        }

        if($clientStatus) {
            $query->where('status', $clientStatus);
        }
        $query->orderBy('id', 'desc');

        return $query->get();
    }

    public function getAllAlaycareClient($search,$paginate=""){
        
        $agencyids = Utility::getUserWiseAgency();
        
        $query = AlayacareClient::with('agencyDetails')
        ->whereHas('agencyDetails',function($query){
            $query->where('alaycare_status',1);
        });
        if(!empty($agencyids)){
			$query->whereIn('agency_id',$agencyids);
		}
        if (isset($search['first_name']) && $search['first_name'] !="") {
            $query->where('first_name', 'like', '%' . $search['first_name'] . '%');
        }
        if (isset($search['last_name']) && $search['last_name'] !="") {
            $query->where('last_name', 'like', '%' . $search['last_name'] . '%');
        }
        if (isset($search['agency_name']) && $search['agency_name'] !="") {
            $query->where('agency_id', 'like', $search['agency_name']);
        }
    
        if(isset($search['branch_name']) && $search['branch_name'] !="") {
            $query->where('branch_name', 'like', '%' . $search['branch_name'] . '%');
        }

        if(isset($search['phone_no']) && $search['phone_no'] !="") {
            $query->where('phone_main', $search['phone_no']);
        }

        if(isset($search['city']) && $search['city'] !="") {
            $query->where('city', 'like', '%' .$search['city'] . '%');
        }

        if(isset($search['state']) && $search['state'] !="") {
            $query->where('state', 'like', '%' . $search['state'] . '%');
        }

        if(isset($search['gender']) && $search['gender'] !="") {
            $query->where('gender', 'like', '%' . $search['gender'] . '%');
        }

        if(isset($search['status']) && $search['status'] !="") {
            if($search['status'] =='Pending'){
                $query->whereNull('patient_id');
            }
            if($search['status'] =='Booked'){
                $query->whereNotNull('patient_id');
            }
           
        }

        if(isset($search['client_status']) && $search['client_status'] !="") {
            $query->where('status', $search['client_status']);
        }

        if (isset($search['created_date']) && $search['created_date'] != '') {
            $dates = explode(' - ', $search['created_date']);
            if (count($dates) == 2) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime(trim($dates[0]))));
                $query->whereDate('created_at', '<=', date('Y-m-d', strtotime(trim($dates[1]))));
            }
        }

        $query->orderBy('id', 'desc');

        if($paginate !=""){
            return $query->get();
        }
        return $query->paginate(50);
    }

    public function totalSyncClientDetails($id){
        return AlayacareClient::where('del_flag','N')->where('agency_id',$id)->where('demographic_update_flag','N')->get();
    }

    public function totalAlayacareClient(){
        return AlayacareClient::where('del_flag','N')->count();
    }

    public static function searchData($search="",$agencyId){
        $query = AlayacareClient::whereRaw('agency_id ="'.$agencyId.'" and (first_name LIKE "%'.$search.'%" or last_name LIKE "%'.$search.'%")')->get();
        return $query;
    }

    public static function getAllDetailsByAlayacreId($clientId,$agencyId){
return AlayacareClient::where('del_flag','N')->where('client_id',$clientId)->where('agency_id',$agencyId)->first();
    }

    public function getClientByID($agencyId, $clientId)
    {
        return AlayacareClient::where('agency_id', $agencyId)->where('client_id', $clientId)->first();
    }

    public function alayacareClientUpdate($data, $clientId, $agencyId)
    {
        return AlayacareClient::updateOrCreate(
            ['client_id' => $clientId, 'agency_id' => $agencyId],
            $data
        );
    }

    public function getAllClientIdByAgencyId($agencyId)
    {
        return AlayacareClient::where('agency_id', $agencyId)->where('del_flag','N')->pluck('client_id');
    }

    public function totalSyncClientDetailsWithLimit(){
        return AlayacareClient::select('client_id','agency_id')->where('del_flag','N')->whereNull('first_name')->where('demographic_update_flag','N')->limit(500)->get();
    }
}