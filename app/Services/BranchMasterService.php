<?php

namespace App\Services;

use App\Model\BranchMaster;

class BranchMasterService
{
    
    public function findByBranchAndAgency($agencyId,$branchId){
        return BranchMaster::where('del_flag','N')->where('agency_id',$agencyId)->where('branch_id',$branchId)->first();
    }

    public function save($data){
        $data['del_flag'] = 'N';
        $insert = new BranchMaster($data);
        $insert->save();
        return $insert->id;
    }
}
