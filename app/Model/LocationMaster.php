<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class LocationMaster extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'location_master';
	protected $fillable = ['id','address1','address2', 'city','state','zip_code' ,'created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'delete_flag','link','location_name','walkin','telehealth_config','stop_date','stop_time'];

	public function caregiver()
    {
        return $this->hasMany(Patient::class,"location_id","id")->where('type','Caregiver')->where('patient_master.deleted_flag', 'N')->whereNull('archived_at');
    }

	public function patient()
    {
        return $this->hasMany(Patient::class,"location_id","id")->where('type','Patient')->where('patient_master.deleted_flag', 'N')->whereNull('archived_at');
    }
}
