<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use App\Master;

class RateCard extends Model
{
    use SoftDeletes;
    protected $table = 'rate_card';
    protected $guarded = ['id'];


    public function serviceDetails() 
	{
		return $this->belongsTo(Master::class,'service_id','id');
	}

    public function users() 
	{
		return $this->belongsTo(User::class,'created_by','id');
	}
}
