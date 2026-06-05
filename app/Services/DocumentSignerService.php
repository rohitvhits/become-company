<?php

namespace App\Services;

use App\DocumentSignerMaster;
class DocumentSignerService
{
	public  function save($data){
		$userId = Auth()->user();
		$data['created_date']=date('Y-m-d H:i:s');
		if($userId){
		$data['created_by']=$userId['id'];
		}
		$insert = new DocumentSignerMaster($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
		
	}
	public  function update($data,$where){
		$userId = Auth()->user();
		$data['updated_date']=date('Y-m-d H:i:s');
		if($userId){
		$data['updated_by']=$userId['id'];
		}
		$update =DocumentSignerMaster::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$userId = Auth()->user();
		$data['deleted_date']=date('Y-m-d H:i:s');
		$data['deleted_by']=$userId['id'];
		$update =DocumentSignerMaster::where($where)->update($data); 
		return $update;
	}

    public function getDocumentSignerMasterList(){
        $query = DocumentSignerMaster::where('del_flag','N')->get();
        return $query;
    }
	public function getDocumentSignerMasterListById($id){
		$query = DocumentSignerMaster::where('del_flag','N')->where('template_id',$id)->get();
		
        return $query;
    }

	public function getAllocatedSigners($id){
		return DocumentSignerMaster::where('template_id', $id)
			->where('del_flag', 'N')
			->pluck('name')
			->unique()
			->values()
			->toArray();
	}
    
}