<?php

namespace App\Model;

use App\Agency;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class AgencyWiseIpBlocker extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'agency_wise_ip_blocker';
	protected $fillable = ['id','agency_id','ip','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by','del_flag'];

	public function users() 
	{
		return $this->belongsTo(User::class,'created_by','id');
	}
}
