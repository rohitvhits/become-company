<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BranchMaster extends Model
{
    protected $table = "branch_master";
    protected $guarded = ["id"];

    public static function getDataById($branchId){
        
        return BranchMaster::where('branch_id',$branchId)->first();
    }
}
