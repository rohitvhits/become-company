<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\HubRecordtextMessage;

class HubRecordtextMessageService{

	public  function save($data){ 
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new HubRecordtextMessage($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =HubRecordtextMessage::where($where)->update($data); 
		return $update;
	}

	
	public function getMessageList($hub_id){
		$query = HubRecordtextMessage::with(['userDetails:id,first_name,last_name'])->where('del_flag','N')->where('hub_record_id',$hub_id)->get();
		return $query;
		
	}

    public function getDetailsId($id){
		$query = HubRecordtextMessage::with(['userDetails:id,first_name,last_name'])->where('del_flag','N')->where('id',$id)->first();
		return $query;
		
	}

	public function getMessageListWithMultipleIds($hub_id,$hub_record_agency_id,$hub_agency_id){
		$query = HubRecordtextMessage::with(['userDetails:id,first_name,last_name'])->where('del_flag','N')->whereIn('hub_record_id',$hub_id)->where('hub_record_agency_id',$hub_record_agency_id)->where('hub_agency_id',$hub_agency_id)->get();
		return $query;
		
	}

	public function getAllMessageListApi($id,$agency_id,$hub_record_agency_id,$offset)
	{
		$query = HubRecordtextMessage::with(['userDetails:id,first_name,last_name'])->select('hub_text_messages.id','hub_record_id','hub_record.first_name','hub_record.last_name','hub_text_messages.mobile','hub_text_messages.message','hub_text_messages.created_by','hub_text_messages.created_date')
		->leftjoin('hub_record', function ($join) {
			$join->on('hub_record.id', '=', 'hub_text_messages.hub_record_id');
		})->where('hub_record_id', $id)->where('hub_text_messages.hub_agency_id', $agency_id)->where('hub_text_messages.hub_record_agency_id', $hub_record_agency_id);
		$query = $query->orderBy('hub_text_messages.id', 'desc')->offset($offset)->limit(50)->get();
		return $query;
	}
}