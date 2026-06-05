<?php

namespace App\Services;

use App\Model\TaskComment;
class TaskCommentService
{
    public function save($data){
        $auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
	
		$insert = new TaskComment($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
    }

    public function getCommentListByTaskId($taskId){
        return TaskComment::with('userDetails')->where("task_id",$taskId)->orderBy('id','asc')->get(); 
    }
    public function getDetailsByCommentId($id){
        return TaskComment::with(['userDetails:id,first_name,last_name'])->where('id',$id)->first();
    }
}