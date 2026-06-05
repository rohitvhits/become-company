<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserWiseLocation extends Model
{
    protected $table = "user_wise_location";
    protected $guarded = ["id"];

    public function locationDetails(){
        return $this->hasOne(LocationMaster::class,"id","location_id");
    }

    public function userDetails(){
        return $this->hasOne(User::class,"id","created_by");
    }

    public function updatedUserDetails(){
        return $this->hasOne(User::class,"id","updated_by");
    }

    public function userDetailsData(){
        return $this->hasOne(User::class,"id","user_id");
    }
}
