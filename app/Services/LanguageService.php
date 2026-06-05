<?php
namespace App\Services;
use App\Model\Language;

class LanguageService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];		
		return Language::create($data);
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update =Language::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$update =Language::where($where)->update($data); 
		return $update;
	}

	public function totalRecord()
	{
		return Language::count();
	}

	public function getData($name){
		
		$query = Language::select('*');
		if($name !=''){
			$query->where('name','LIKE','%'.$name.'%');
		}
		$result = $query->orderBy('id','desc')->paginate(10);
		return $result;
		
	}
	
	public function getDetailById($id){
		$query = Language::where('id',$id)->first();
		return $query;
	}
	
	public function getDataExport($name){
		$query = Language::select('*');
		if($name !=''){
			$query->where('name','LIKE','%'.$name.'%');
		}
		
		$result = $query->get();
		return $result;
		
	}
	
	public function getLanguageList(){
		return Language::get();
	}

	public function getAllLanguageList(){
		return Language::where('del_flag','N')->get();
	}

	public function getDetailsbyName($languageTrimmed){
		return Language::select('id', 'name')
                    ->whereRaw('LOWER(name) = ?', [strtolower($languageTrimmed)])  // FIXED: Safe parameterized query
                    ->whereNull('deleted_at')
                    ->first();
	}

	public function getAllLanguagesById(){
		return Language::whereNull('deleted_at')
			->pluck('name', 'id')
			->toArray();
	} 

}