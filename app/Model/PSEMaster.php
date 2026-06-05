<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PSEMaster extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'pse_master';
	protected $fillable = ['id','address1','address2', 'city','state','zip_code' ,'created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'delete_flag','link','location_name','walkin'];

}
