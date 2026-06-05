<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use App\Model\AgencyMailLog;

class AgencyMailLogHelper
{
    public function __construct()
	{}
	
    public static function save($data){
        $data['del_flag'] = "N";
		$data['created_date'] = date('Y-m-d H:i:s');
		$insert = new AgencyMailLog($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
    }

	public static function getList($agency_id,$email,$created_date){
		$query = AgencyMailLog::select('agency_mail_log.*','agency.agency_name')
				->leftjoin('agency',function($join){
					$join->on('agency.id','=','agency_mail_log.agency_id');
					$join->where('agency.delete_flag','N');
				})
				->where('agency_mail_log.del_flag','N');
				if($agency_id !=''){
					$query->where('agency_mail_log.agency_id',$agency_id);
				}
				if($email !=''){
					$query->where('agency_mail_log.email','LIKE','%'.$email.'%');
				}
				if($created_date !=''){
					$expld = explode('-',$created_date);
					$query->whereDate('agency_mail_log.created_date','>=',date('Y-m-d',strtotime($expld[0])))->whereDate('agency_mail_log.created_date','<=',date('Y-m-d',strtotime($expld[1])));
				}
				$query = $query->orderBy('agency_mail_log.id','desc')->paginate(50);

		return $query;
	}
	public static function getListExport($agency_id,$email,$created_date){
		$query = AgencyMailLog::select('agency_mail_log.*','agency.agency_name')
				->leftjoin('agency',function($join){
					$join->on('agency.id','=','agency_mail_log.agency_id');
					$join->where('agency.delete_flag','N');
				})
				->where('agency_mail_log.del_flag','N');
				if($agency_id !=''){
					$query->where('agency_mail_log.agency_id',$agency_id);
				}
				if($email !=''){
					$query->where('agency_mail_log.email','LIKE','%'.$email.'%');
				}
				if($created_date !=''){
					$expld = explode('-',$created_date);
					$query->whereDate('agency_mail_log.created_date','>=',date('Y-m-d',strtotime($expld[0])))->whereDate('agency_mail_log.created_date','<=',date('Y-m-d',strtotime($expld[1])));
				}
				$query = $query->orderBy('agency_mail_log.id','asc')->get();

		return $query;
	}
}