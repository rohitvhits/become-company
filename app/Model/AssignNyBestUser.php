<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class AssignNyBestUser extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'assign_nybest_user';
	protected $fillable = ['id','agency_id','nybest_user_id','del_flag', 'created_at','created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];


	public static function getAssignNybestUserEmail($id){
		$query = AssignNyBestUser::select('nybest_user_id','users.email')->leftjoin('users', 'users.id', '=', 'assign_nybest_user.nybest_user_id')->whereIn('agency_id',$id)->where('del_flag','N')->get();
		return $query->pluck('email')->toArray();
	}

	public static function getAssignNybestUserId($id){
		$query = AssignNyBestUser::where('agency_id',$id)->where('del_flag','N')->get();
		return $query;
	}

	public static function getOnlyAssignNybestUserId($id){
		$query = AssignNyBestUser::select('nybest_user_id')->where('agency_id',$id)->where('del_flag','N')->pluck('nybest_user_id');
		return $query;
	}
}
