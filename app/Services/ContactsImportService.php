<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\ContactsImport;

class ContactsImportService{

	public  static function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new ContactsImport($data);
		$insert_id = $insert->save();
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =ContactsImport::where($where)->update($data); 
		return $update;
	}
	public static function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =ContactsImport::where($where)->update($data); 
		return $update;
	}

	public static function getContactsImportListByUser(){
		$auth = auth()->user();
		
		$query = ContactsImport::select('id','name','mobile','email','image')->where('created_by',$auth['id'])->where('del_flag','N')->paginate(50);
		return $query;
	}
	
	public static function getDetailById($id){
		$query = ContactsImport::where('id',$id)->where('del_flag','N')->first();
		return $query;
	}
	
}