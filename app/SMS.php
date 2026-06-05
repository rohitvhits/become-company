<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\UserAgency ;
class SMS extends Model
{
	use Notifiable;

	protected $table = 'mobile_sms_record';
	public $timestamps =false;
	protected $fillable = ['id', 'record_id', 'sender_id', 'type','data', 'media','message','phone',  'created_at', 'delete_flag'];

	public static function getAllMessageByRecordId($id){
		$query  = SMS::select('mobile_sms_record.*','us.first_name','us.last_name')->leftjoin('users as us',function($join){ 
			$join->on('mobile_sms_record.sender_id','=','us.id');
			$join->where('us.delete_flag','N') ; 
		})->where('mobile_sms_record.delete_flag','N')->where('mobile_sms_record.record_id',$id)->get();
		return $query;
	}
	public static function getAllUnreadSMSByAgency200SMS($agency=null){

		$currentUser = auth()->user();
		$availableAgency=array();
		


			$query  = SMS::select('mobile_sms_record.*','record.first_name','record.last_name')->leftjoin('record',function($join){ 
				$join->on('record.id','=','mobile_sms_record.record_id');
				
				})->where('mobile_sms_record.delete_flag','N');
			if($agency){
				$query=$query->whereIn('agency_fk',$agency);

			}
			if($currentUser['user_type_fk']=='4'){
			//	$agencyArray=	UserAgency::getAgencyIdByUserId($currentUser['id']);
			//	$query=$query->whereIn('agency_fk',$agencyArray);

			}
			$query=$query->orderBy('mobile_sms_record.id','desc');
			return $query->simplePaginate(200);
	}
	public static function getAllUnreadSMSByAgency($agency=null){

		$currentUser = auth()->user();
		$availableAgency=array();
		


			$query  = SMS::select('mobile_sms_record.*','record.first_name','record.last_name')->leftjoin('record',function($join){ 
				$join->on('record.id','=','mobile_sms_record.record_id');
				
				})->where('mobile_sms_record.delete_flag','N');
			if($agency){
				$query=$query->whereIn('agency_fk',$agency);

			}
			if($currentUser['user_type_fk']=='4'){
			//	$agencyArray=	UserAgency::getAgencyIdByUserId($currentUser['id']);
			//	$query=$query->whereIn('agency_fk',$agencyArray);

			}
			$query=$query->orderBy('mobile_sms_record.id','desc');
			return $query->get();
	}
	public static function  getAllRecordForDashboardChat($phone=""){ 
		$records=SMS::where('delete_flag','N');
				if($phone !=''){
					$records->where('phone','LIKE','%'.$phone.'%');
				}
			$mysql = $records->orderBy('created_at','desc')->paginate(50);
		return $mysql; 
	}
	public static function incommingSMSQuery(){
		return SMS::where('mobile_sms_record.delete_flag','N')->limit(15)->get();
	}
	
}
