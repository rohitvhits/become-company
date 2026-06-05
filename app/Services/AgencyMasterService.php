<?php

namespace App\Services;
use App\Model\AgencyMaster;

class AgencyMasterService{

    public function getAgencyMasterDetailsByFormId($formId){
       return AgencyMaster::with('fields')->where('form_id', $formId)->get();
    }
}