<?php
namespace App\Services;
use App\Model\EFaxLog;

class EFaxLogService{

    public  function save($data){
        $auth = auth()->user();
		$data['created_date']=date('Y-m-d H:i:s');
		$data['created_by']=$auth->id;
		
		$insert = new EFaxLog($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
		
	}

	public function list($search=[],$paginate=""){
		$query = EFaxLog::select('e_fax_log.patient_id','doc.document_name','pt.first_name','pt.last_name','pt.type','e_fax_log.fax_no','e_fax_log.created_date','us.first_name as uFirstName','us.last_name as uLastName','e_fax_log.return_response')
				->leftjoin('document_patient as doc',function($join){
					$join->on('doc.id','=','e_fax_log.document_id');
					$join->where('doc.deleted_flag','N');
				})
				->leftjoin('patient_master as pt',function($join){
					$join->on('pt.id','=','e_fax_log.patient_id');
					$join->where('pt.deleted_flag','N');
				})
				->leftjoin('users as us',function($join){
					$join->on('us.id','=','e_fax_log.created_by');
					$join->where('us.delete_flag','N');
				})->where('e_fax_log.del_flag','N')->orderBy('e_fax_log.id','desc');
				if(isset($search['patient_id']) && $search['patient_id'] !=""){
					$query->where('pt.id',$search['patient_id']);
				}
				if(isset($search['type']) && $search['type'] !=""){
					$query->where('pt.type',$search['type']);
				}
				if(isset($search['created_date']) && $search['created_date'] !=""){
					$explode = explode('-',$search['created_date']);
					$query->whereDate('e_fax_log.created_date','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('e_fax_log.created_date','<=',date('Y-m-d',strtotime($explode[1])));
				}
				if(isset($search['created_by']) && $search['created_by'] !=""){
					
					$query->where('e_fax_log.created_by',$search['created_by']);
				}

				if($paginate !=""){
					$query = $query->orderBy('e_fax_log.id','desc')->get();
				}else{
					$query = $query->orderBy('e_fax_log.id','desc')->paginate(50);
				}
				
		return $query;
	}
}