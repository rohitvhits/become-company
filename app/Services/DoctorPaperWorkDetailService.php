<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\DoctorPaperWorkDetail;

class DoctorPaperWorkDetailService{

	public static function save($data){ 
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new DoctorPaperWorkDetail($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	
	public static function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =DoctorPaperWorkDetail::where($where)->update($data); 
		return $update;
	}
	
	public static function getDoctorPaperWorkDetailById($id){
		
		$query = DoctorPaperWorkDetail::select('id','notes','created_date','created_by')->where('del_flag','N')->where('paper_work_id',$id)->get();
		return $query;
	}
}