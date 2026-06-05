<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class GenerateAgencyToken extends Model
{
	use Notifiable; 

	protected $table = 'agency_token';
	public $timestamps  = false;
	protected $fillable = ['id','agency_id','token','updated_date','updated_by','created_date', 'deleted_at', 'created_by',  'deleted_by', 'delete_flag','notes','ip_block','notes_id'];

	public function userDetails()
	{
		return $this->hasOne(User::class,'id','created_by');
	}

	public static function deleteTokenById($id){
		$auth = auth()->user();
		return GenerateAgencyToken::where('id',$id)->update(array('delete_flag'=>'Y','deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$auth->id));
	}

	public static function getTokenByAgencyId($id){
		$query = GenerateAgencyToken::where('agency_id',$id)->where('delete_flag','N')->first();
		return $query;
	}
	public static function checkTokenGenerate($id){
		$query = GenerateAgencyToken::where('agency_id',$id)->where('delete_flag','N')->count();
		return $query;
	}
	
	public static function getAllGenerateToken($id){
		return GenerateAgencyToken::with(['userDetails'])->where('agency_id',$id)->where('delete_flag','N')->orderBy('id','desc')->paginate(50);
	}

	public function agencyDetailsByToken()
	{
		return $this->hasOne(Agency::class,'id','agency_id');
	}
}
