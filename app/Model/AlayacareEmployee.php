<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Agency;
class AlayacareEmployee extends Model
{
    protected $table = "alayacare_emp_master";
    protected $guarded = ["id"];

    public function agencyDetails()
	{
		return $this->hasOne(Agency::class,'id','agency_id');
	}
    

    public static function AlayacareIdgetData($search="",$agencyId=""){
        $query = AlayacareEmployee::where('agency_id',$agencyId)->whereRaw('(first_name LIKE "%'.$search.'%" or last_name LIKE "%'.$search.'%")')->get();
        return $query;
    }
    
}
