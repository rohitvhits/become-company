<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Agency;
use App\User;
class UserWiseAgency extends Model
{
    protected $table = "user_wise_agency";
    protected $guarded = ["id"];

    public function agencyDetails(){
        return $this->hasOne(Agency::class,"id","agency_id");
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
