<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\TaskAssignLog;

class TaskAssignLogService{
	
	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		
		
		$insert = new TaskAssignLog($data);
		$insert->save();
		$insertId = $insert->id;

		
		
		return $insertId;
		
	}
	
}

?>