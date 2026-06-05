<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class HHAMDOClient extends Model
{
	public $timestamps =false;
	protected $table = 'hha_mdo_client';
	protected $fillable = ['id','agency_id','client_id','client_secret','api_token','is_status','del_flag','created_date','created_by', 'updated_date','updated_by', 'deleted_date','deleted_by','txtID'];

}
