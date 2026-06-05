<?php
namespace App\Model;

use App\Model\HubCompany;
use App\User;
use Illuminate\Database\Eloquent\Model;

class HubUtilizationImportLog extends Model
{
	public $timestamps = false;
	protected $table = 'hub_utilization_import_logs';
	protected $guarded = ["id"];

	public function users() 
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}

	public function assignToUser()
    {
		return $this->hasOne(User::class,'id', 'assign_user_id');
    }

    public function agencyDetail()
    {
		return $this->hasOne(HubCompany::class, 'id', 'agency_id');
    }
}
 