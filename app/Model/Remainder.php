<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Remainder extends Model
{
	protected $table = 'remember_master';
	public $timestamps = false;
	protected $fillable = ['id','user_id','message','employee_id','title','del_flag','created_date','created_by','updated_date','updated_by','start_date','end_date','start_time'];
	
}

?>