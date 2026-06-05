<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CMS extends Model
{
    protected $table = "cms";
    protected $guarded = ["id"];

    public function createdUser(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updatedUsers(){
        return $this->hasOne(User::class,'id','updated_by');
    }
}
