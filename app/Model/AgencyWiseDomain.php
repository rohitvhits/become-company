<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class AgencyWiseDomain extends Model
{
    public $timestamps = false;
    protected $table = 'agency_wise_domain';
    protected $fillable = ['id','agency_id','domain','del_flag','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'];
	
	
}
