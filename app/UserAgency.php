<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class UserAgency extends Model
{
	use Notifiable; 
	public $timestamps = false;
	protected $table = 'user_agency';
	protected $fillable = ['id','user_id','agency_fk', 'del_flag', 'created_date', 'created_by','updated_date', 'updated_by', 'deleted_date','deleted_by'];

	public static function getAgencyListByUserId($id,$agency_fk)
	{
		$temp='del_flag = "N" ';
		if($agency_fk!='')
		{
			$temp.=' and user_agency.agency_fk ="'.$agency_fk.'"';
		}
		$query = UserAgency::select('user_agency.*','agency.agency_name')
				->leftjoin('agency',function($join){
					$join->on('agency.id','=','user_agency.agency_fk');
					$join->where('agency.delete_flag','N');
				})
				->where('user_agency.user_id',$id)
				->whereRaw($temp)
				->orderBy('user_agency.id','desc')
				->simplePaginate(10);
		return $query;
	}

	public static function getAgencyIdByUserId($UserId)
	{
		$temp='del_flag = "N" ';
		
		$query = UserAgency::select('agency_fk')
				->where('user_id',$UserId)
				->where('del_flag','N')

				->get();
		return $query;
	}

}
