<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GroupMaster extends Model
{
    protected $table = "group_master";
    protected $guarded = ["id"];

    public static function getDataById($groupId){
        return GroupMaster::where('group_id',$groupId)->first();
    }
}
