<?php

namespace App\Services;

use App\DocusignDetail;
class DocusignDetailService
{
	public  function save($data){
		$auth = auth()->user();
		$data['created_date']=date('Y-m-d H:i:s');
		if(isset($auth->id)){
			$data['created_by']=$auth->id;
		}
		$insert = new DocusignDetail($data);
		$insert->save();
		return $insert->id;
		
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date']=date('Y-m-d H:i:s');
		if(isset($auth->id)){
			$data['updated_by']=$auth->id;
		}
		return DocusignDetail::where($where)->update($data);
	}

	public function getDetailsById($id){
		return DocusignDetail::where('id',$id)->where('del_flag','N')->first();
	}

	public function getDetailsByDocumentReportId($id){
		return DocusignDetail::where('document_report_id',$id)->where('del_flag','N')->orderBy('id','desc')->first();
	}

	public function getDetailsByUpdateFlag($id){
		return DocusignDetail::where('document_report_id',$id)->where('del_flag','N')->where('updated_flag',1)->orderBy('id','desc')->first();
	}

	public function getDetailsByDocumentReportIds($id){
		return DocusignDetail::whereIn('document_report_id',$id)->where('del_flag','N')->orderBy('id','asc')->get();
	}
}
