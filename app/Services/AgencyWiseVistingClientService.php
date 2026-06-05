<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\AgencyWiseVistingClient;

class AgencyWiseVistingClientService{

    protected const COMMON_DATE_FORMAT_YMD ='Y-m-d H:i:s';
	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date(self::COMMON_DATE_FORMAT_YMD);
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";
		
		$insert = new AgencyWiseVistingClient($data);
		return $insert->save();
	}

	public function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date(self::COMMON_DATE_FORMAT_YMD);
		$data['updated_by'] = $auth['id'];
		return AgencyWiseVistingClient::where($where)->update($data);
	}
    
    public function getDetailsByAgencyId($agencyId){
        return AgencyWiseVistingClient::where('agency_id',$agencyId)->first();
    }

	public function getDetailsByAgencyIdEnabledStatus($agencyId){
        return AgencyWiseVistingClient::where('agency_id',$agencyId)->where('status',1)->first();
    }
}