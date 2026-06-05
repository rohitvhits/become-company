<?php
namespace App\Services;

use App\Model\HHAOffice;
use DB;
class HHAOfficeService{

    public function getALLOfficeList(){
        return HHAOffice::getOfficeList();
    }

    public function getPluckOfficeList(){
        return HHAOffice::select(
            'office_id',
            DB::raw("CONCAT(office_name, ' - ', office_code) as office_label")
        )
        ->whereNotNull('office_id')
        ->where('del_flag', 'N')
        ->pluck('office_label', 'office_id');
    }

    public function getOfficeListByAgencyId($agencyId){
        return HHAOffice::select('office_id')->where('agency_fk',$agencyId)->get();
    }

    public function getOfficeDetailsByAgencyId($agencyId){
        return HHAOffice::select('office_id as id', 'office_name', 'office_code')
        ->where('agency_fk', $agencyId)
        ->where('del_flag', 'N')
        ->orderBy('office_name', 'asc')
        ->get();
    }

    public static function getOfficeDetailsBySha1AgencyId($agencyId){

        return HHAOffice::whereRaw('SHA1(agency_fk) = "'.$agencyId.'"')->where('del_flag', 'N')->get();
    }
}