<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserWiseDomain extends Model
{
    public $timestamps = false;
    protected $table = 'user_wise_domain';
    protected $fillable = ['id','user_id','domain','del_flag','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'];
	
	
}
