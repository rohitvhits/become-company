<?php

namespace App\Model;

use App\Agency;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class HubRecordNotes extends Model
{
	use Notifiable;
	public $timestamps = false;
	protected $table = 'hub_record_notes';
	protected $fillable = ['id', 'hub_record_id', 'message', 'delete_flag', 'created_date', 'created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by', 'data', 'message_status', 'subject', 'hub_record_agency_id', 'hub_agency_id','flag', 'reason'];

	public function agencyDetail()
	{
		return $this->hasOne(Agency::class, 'id', 'agency_id');
	}

	public function users()
	{
		return $this->hasOne(User::class, 'id', 'created_by');
	}
}
