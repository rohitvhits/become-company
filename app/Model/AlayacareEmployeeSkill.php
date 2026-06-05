<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AlayacareEmployeeSkill extends Model
{
    public $timestamps = false;
    protected $table = "alayacare_employee_skill";
    protected $guarded = ["id"];

    public function employeeDetails()
	{
		return $this->hasMany(AlayacareEmployee::class,'id','alayacare_emp_id')->where('del_flag','N');
	}
}
