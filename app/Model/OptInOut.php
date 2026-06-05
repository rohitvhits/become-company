<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OptInOut extends Model
{
    public $timestamps = false;
    protected $table = 'optInOut';
    protected $fillable = ['id','mobile','optInOut','del_flag','created_date'];
	
	
}
