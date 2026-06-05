<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AgencySkill extends Model
{
    public $timestamps = false;
    protected $table = 'agency_skill';
    protected $fillable = ['id','agency_id','skill_id','del_flag','created_date','created_by','deleted_date','deleted_by'];
	
	
}
