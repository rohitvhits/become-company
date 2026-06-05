<?php

namespace App\Model;

use App\Agency;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use \LaravelArchivable\Archivable;
use Carbon\Carbon;
class HubDependents extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'hub_dependents';
	protected $fillable = ['id','hub_record_id','hub_agency_id','agency_id','dependent_id','first_name','middle_name','last_name','dob','gender','phone','mobile','address1','address2','state','city','zip_code','county','email','ssn','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by'];

	public function users() 
	{
		return $this->belongsTo(User::class,'created_by','id');
	}

	public function languages()
	{
		return $this->belongsTo(Language::class,'language','id');
	}

	public function locations()
	{
		return $this->belongsTo(LocationMaster::class,'location_id','id');
	}

	public function assignToUser()
    {
		return $this->hasOne(User::class,'id', 'assign_user_id');
    }

    public function agencyDetail()
    {
		return $this->hasOne(Agency::class,'id', 'agency_id');
    }
}
 