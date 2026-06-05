<?php

namespace App\Model;

use App\Agency;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use \LaravelArchivable\Archivable;
use Carbon\Carbon;

class HubRecordImportLog extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'hub_record_import_logs';
	protected $fillable = [
		'id',
		'agency_id',
		'file_name',
		'total_records',
		'successful_records',
		'failed_records',
		'duplicate_records',
		'updated_records',
		'error_details',
		'status',
		'deleted_flag',
		'created_date',
		'created_by',
		'updated_date',
		'updated_by',
		'deleted_date',
		'deleted_by'
	];

	public function users() 
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
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
		return $this->hasOne(Agency::class, 'id', 'agency_id');
    }
}
 