<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\CMS;

class CMSService{

	public static function save($data){ 
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		
		
		$insert = new CMS($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =CMS::where($where)->update($data); 
		return $update;
	}
	
    public static function getList(){
        return CMS::select('id','type','message','created_at','updated_at','created_by','updated_by')->with(['updatedUsers:id,first_name,last_name','createdUser:id,first_name,last_name'])->where('del_flag','N')->paginate(10);
    }

	public static function getDetailsByid($id){
	
		return CMS::select('id','type','message','updated_at','send_mail')->where('del_flag','N')->where('id',$id)->first();
	}
}