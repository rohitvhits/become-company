<?php
namespace App\Services;
use App\Model\DocumentUploadHistory;
use App\Helpers\Utility;

class DocumentUploadHistoryService{

    public function getAllList($patient_id)
	{
		$query = DocumentUploadHistory::select(
			'document_upload_history.*',
			'users.first_name',
			'users.last_name'
		)->leftjoin('users',function($join){
			$join->on('users.id','=','document_upload_history.created_by');
			$join->where('users.delete_flag','N');
		})->where('document_upload_history.del_flag','N')->where('portal_id',$patient_id);
		$query = $query->orderBy('document_upload_history.id','desc')->paginate(50);
		return $query;
	}

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new DocumentUploadHistory($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = DocumentUploadHistory::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 1;
		$update = DocumentUploadHistory::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = DocumentUploadHistory::where('id', $id);
		$query = $query->first();
		return $query;
	}
}