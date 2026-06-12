<?php
namespace App\Services;
use App\Model\AgencyWiseService;

class AgencyWiseServiceService{

	public function getService($agencyId,$paginate=""){
        $query =  AgencyWiseService::where('agency_id',$agencyId)->where('del_flag', 'N');
        if($paginate =='paginate'){
          $query=  $query->paginate(10);
        }else{
            $query=  $query->get();
        }
        return $query;
    }

    public function getServiceNew($agencyId){
        return AgencyWiseService::selectRaw('service_id as id,name,type')->where('agency_id',$agencyId)->where('del_flag', 'N')->get();
      
    }
    public  function ServiceList($type,$agencyId=""){
		 if($agencyId !=""){
			
		 }else{
			$auth   =auth()->user();
			$agencyId = $auth ? $auth->agency_fk : "";
		 }
        
        return  AgencyWiseService::where('type',$type)->where('agency_id',$agencyId)->whereNull('deleted_date')->get();
    }
	
	public  function getServiceListUsingId($ids,$agencyId){
        return  AgencyWiseService::select('name')->where('agency_id',$agencyId)->whereIn('id', $ids)->get();
    } 
	public function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = AgencyWiseService::where($where)->update($data);
        return $update;
    }

    public  function ServiceListNew($type,$agencyId=""){
        if($agencyId !=""){
           
        }else{
           $auth   =auth()->user();
           $agencyId = $auth ? $auth->agency_fk : "";
        }
       
       return  AgencyWiseService::selectRaw('*,service_id as id')->where('type',$type)->where('agency_id',$agencyId)->whereNull('deleted_date')->get();
   }

   public function getServiceByPluck($agencyId){
    return  AgencyWiseService::where('agency_id',$agencyId)->where('del_flag', 'N')->pluck('service_id','name');
   }

   public function getServiceTypeNew($agencyId){
    return AgencyWiseService::selectRaw('service_id as id,name,type as types')->where('agency_id',$agencyId)->whereIn('type',['Caregiver','Patient'])->where('del_flag', 'N')->get();
   }

   public function getServiceNewWithPaginate($agencyId){
    return AgencyWiseService::selectRaw('service_id as id,name,type as types')->where('agency_id',$agencyId)->where('del_flag', 'N')->paginate(50);
  
    }

    public  function ServiceListNewWithoutNyBestUser($type="",$agencyId=""){
        if($agencyId !=""){
           
        }else{
           $auth   =auth()->user();
           $agencyId = $auth ? $auth->agency_fk : "";
        }
       
       $query=  AgencyWiseService::selectRaw('*,agency_wise_service.service_id as id')
       ->join('master_table',function($join){
        $join->on('master_table.id','=','agency_wise_service.service_id');
       });
       $query->where('master_table.is_disable',1);
       if(auth()->user() && auth()->user()->agency_fk !=""){
        $query->where('master_table.enabled_nybest_user',0);
       }
       if($type !=""){
        $query->where('agency_wise_service.type',$type);
       }
       return $query->where('agency_wise_service.agency_id',$agencyId)->whereNull('agency_wise_service.deleted_date')->get();
   }

   public  function ServiceListNewFlagListNyBestUser($agencyId=""){
       
        $query=  AgencyWiseService::selectRaw('agency_wise_service.service_id as id,agency_wise_service.name,agency_wise_service.type')
        ->join('master_table',function($join){
            $join->on('master_table.id','=','agency_wise_service.service_id');
        });
        if(auth()->user() && auth()->user()->agency_fk !=""){
            $query->where('master_table.enabled_nybest_user',0);
        }
        $query->where('master_table.is_disable',1);
        return $query->where('agency_wise_service.agency_id',$agencyId)->whereNull('agency_wise_service.deleted_date')->get();
    }

    public  function serviceListNewWithoutNyBestUserAPI($type="",$agencyId=""){
        if(empty($agencyId)){
            $auth   =auth()->user();
            $agencyId = $auth->agency_fk;
        }

       $query=  AgencyWiseService::selectRaw('*,agency_wise_service.service_id as id')
       ->join('master_table',function($join){
        $join->on('master_table.id','=','agency_wise_service.service_id');
       });
       $query->where('master_table.is_disable',1);
       if(auth()->user() && auth()->user()->agency_fk !=""){
        $query->where('master_table.enabled_nybest_user',0);
       }
       if($type !=""){
        $query->where('agency_wise_service.type',$type);
       }
       return $query->where('agency_wise_service.agency_id',$agencyId)->whereNull('agency_wise_service.deleted_date')->get();
   }
}