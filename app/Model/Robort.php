<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use	App\Agency;
class Robort extends Model
{
	protected $table = 'robort_master';
	public $timestamps = false;
	protected $fillable = ['id','uuid','patientId','agency_id','legacyId','firstName','del_flag','created_date','lastName','dob','gender','status','sync_flag','externalId','last_schedule_sync_date'];
	
	public function agencyDetails()
	{
		return $this->hasOne(Agency::class,'id','agency_id');
	}
}

?>