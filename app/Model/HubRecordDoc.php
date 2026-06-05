<?php

namespace App\Model;

use App\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\User;

class HubRecordDoc extends Model
{
	use Notifiable;
	public $timestamps = false;
	protected $table = 'hub_record_doc';
	protected $fillable = ['id', 'hub_record_id', 'document_name', 'attachment', 'created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'deleted_flag', 'hub_agency_id', 'hub_record_agency_id', 'flag', 'reason'];

	public function hubDetails()
	{
		return $this->belongsTo(Patient::class, 'hub_record_id', 'id')->where('deleted_flag', 'N');
	}

	public function userDetails()
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}
}
