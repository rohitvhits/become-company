<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\TextMessages;

class TextMessageService{

	public  function save($data){ 
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new TextMessages($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =TextMessages::where($where)->update($data); 
		return $update;
	}

	
	public function getMessageList($caseId){
		$query = TextMessages::with(['userDetails:id,first_name,last_name'])->where('del_flag','N')->where('case_id',$caseId)->get();
		return $query;
		
	}

    public function getDetailsId($id){
		$query = TextMessages::with(['userDetails:id,first_name,last_name'])->where('del_flag','N')->where('id',$id)->first();
		return $query;
		
	}

	public function getMessageListWithMultipleIds($caseId){
		$query = TextMessages::with(['userDetails:id,first_name,last_name'])->where('del_flag','N')->whereIn('case_id',$caseId)->get();
		return $query;
		
	}

	public function getDetailsIdWithoutUserRelation($id){
		return TextMessages::where('del_flag','N')->where('id',$id)->first();
	}

	public function getLastMessage($caseId){
		return TextMessages::with(['userDetails:id,first_name,last_name'])->where('del_flag','N')->where('created_by','!=','4117')->where('case_id',$caseId)->orderBy('id','desc')->first();
	}
}