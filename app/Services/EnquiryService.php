<?php
namespace App\Services;
use App\Model\Enquiry;

class EnquiryService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";
		
		$insert = new Enquiry($data);
		$insert_id = $insert->save();		
		return $insert_id;
		
	}

    public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		return Enquiry::where($where)->update($data);
	}
	
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =Enquiry::where($where)->update($data); 
		return $update;
	}
	
    public function getList(){
        $auth = auth()->user();
        $getRoles = $auth->roles()->pluck('name');
        
        $query =  Enquiry::with(['usersDetail:id,first_name,last_name'])->whereHas('usersDetail')->where('del_flag','N');
        if(!in_array('Super Admin',$getRoles->toArray())){
            $query->where('created_by',$auth->id);
        }
        $query = $query->orderBy('id','desc')->paginate(50);
        return $query;
    }
	
    public function getDetailsById($id){
        return Enquiry::where('del_flag','N')->where('id',$id)->first();
    }
}