<?php

namespace App\Model;

use App\Model\HubCompany;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class HubRecordAgency extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'hub_record_agency';
	protected $fillable = ['id','hub_record_id','agency_id','member_id','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','del_flag','status','hire_date','work_contact','work_email','employee_code','last_worked_date','deactivated_by','deactivated_date'];

	public function users()
	{
		return $this->belongsTo(User::class,'created_by','id');
	}

    public function agencyDetail()
    {
		return $this->hasOne(HubCompany::class,'id', 'agency_id');
    }
}
 