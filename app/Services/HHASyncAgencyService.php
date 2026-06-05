<?php

namespace App\Services;
use App\Model\HHASyncAgency;


class HHASyncAgencyService
{
    public function save($data){
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['del_flag'] = "N";
		
		$insert = new HHASyncAgency($data);
		$insert_id = $insert->save();
		return $insert_id;
    }

    public function getList(){
        return HHASyncAgency::with(['agencyDetails:id,agency_name'])->where('del_flag','N')->orderBy('created_date','desc')->paginate(50);
    }
}