<?php

namespace App\Model;

use App\Agency;
use Illuminate\Database\Eloquent\Model;
class AlayacareClient extends Model
{
    protected $table = "alayacare_client_master";
    protected $guarded = ["id"];

    public function agencyDetails()
	{
		return $this->hasOne(Agency::class,'id','agency_id');
	}
}
