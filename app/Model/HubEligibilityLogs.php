<?php
namespace App\Model;

use App\Model\HubCompany;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HubEligibilityLogs extends Model
{
	use SoftDeletes;
	public $timestamps = false;
	protected $table = 'hub_eligibility_logs';
	protected $guarded = ['id']; 

	public function users()
	{
		return $this->belongsTo(User::class,'created_by','id');
	}

    public function agencyDetail()
    {
		return $this->hasOne(HubCompany::class,'id', 'agency_id');
    }

	public function importLogs()
	{
		return $this->belongsTo(HubRecordImportLog::class,'log_id','id');	
	}
}
 