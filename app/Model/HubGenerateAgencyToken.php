<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\User;
class HubGenerateAgencyToken extends Model
{
	use Notifiable; 

	protected $table = 'hub_agency_token';
	public $timestamps  = false;
	protected $fillable = ['id','agency_id','token','updated_date','updated_by','created_date', 'deleted_at', 'created_by',  'deleted_by', 'delete_flag','notes','ip_block','notes_id'];

	public function userDetails()
	{
		return $this->hasOne(User::class,'id','created_by');
	}

	public static function deleteTokenById($id){
		$auth = auth()->user();
		return HubGenerateAgencyToken::where('id',$id)->update(array('delete_flag'=>'Y','deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$auth->id));
	}

	public static function getTokenByAgencyId($id){
		$query = HubGenerateAgencyToken::where('agency_id',$id)->where('delete_flag','N')->first();
		return $query;
	}
	public static function checkTokenGenerate($id){
		$query = HubGenerateAgencyToken::where('agency_id',$id)->where('delete_flag','N')->count();
		return $query;
	}
	
	public static function getAllGenerateToken($id){
		return HubGenerateAgencyToken::with(['userDetails'])->where('agency_id',$id)->where('delete_flag','N')->orderBy('id','desc')->paginate(50);
	}

}
