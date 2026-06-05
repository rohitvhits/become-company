<?php
namespace App\Services;
use App\Model\AgencyWiseThirdPartyAPI;

class AgencyWiseThirdPartyAPIService{

	public function getAPIListByAgencyID($agencyId){
        return AgencyWiseThirdPartyAPI::select('id','third_party_name','portal_end_point')->where('del_flag','N')->where('agency_id',$agencyId)->orderBy('id','desc')->get();
    }

    public function getDetailsById($id){
        return AgencyWiseThirdPartyAPI::select('id','third_party_name','portal_end_point','third_party_api','agency_id')->where('del_flag','N')->where('id',$id)->first();
    }
}