<?php

namespace App\Services;

use App\DocumentSentReport;
use App\Model\DocCompletedInternalUseLog;

class DocCompletedInternalUseLogService
{

	public  function save($data)
	{
		$auth = auth()->user();
		if(isset($data['flag']) && $data['flag'] ==1){
			$data['created_date'] = $data['created_date'];
			$data['created_by'] = $data['created_by'];
		}else{

			if(isset($auth['id'])){
				$data['created_date'] = date('Y-m-d H:i:s');
				$data['created_by'] = $auth['id'];
			}
			
		}
		$data['deleted_flag'] = "N";

		$insert = new DocCompletedInternalUseLog($data);
		$insert->save();
		$insert_ids = $insert->id;

		return $insert_ids;
	}

	public  function saveNew($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		
		$data['deleted_flag'] = "N";

		$insert = new DocCompletedInternalUseLog($data);
		$insert->save();
		$insert_ids = $insert->id;

		return $insert_ids;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = DocCompletedInternalUseLog::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = DocCompletedInternalUseLog::where($where)->update($data);
		return $update;
	}
}