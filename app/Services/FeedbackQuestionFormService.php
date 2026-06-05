<?php
namespace App\Services;
use App\Model\FeedBackQuestion;

class FeedbackQuestionFormService{

	public static function save($data){ 
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		
		$insert = new FeedBackQuestion($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update = FeedBackQuestion::where($where)->update($data); 
		return $update;
	}
	
    public static function getAllQuestions(){
        return FeedBackQuestion::where('delete_flag','N')->get();
    }

	public static function getDetailsByid($id){
		return FeedBackQuestion::select('id','type','message','updated_at','send_mail')->where('del_flag','N')->where('id',$id)->first();
	}
}