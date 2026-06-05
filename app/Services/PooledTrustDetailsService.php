<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\PooledTrustDetailMaster;

class PooledTrustDetailsService{

	public  function save($data){ 
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['delete_flag'] = "N";
		
		$insert = new PooledTrustDetailMaster($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =PooledTrustDetailMaster::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =PooledTrustDetailMaster::where($where)->update($data); 
		return $update;
	}
	public function getDetailsById($id){
		$query = PooledTrustDetailMaster::where('trust_id',$id)->where('del_flag','N')->count();
		return $query;
		
	}
	public function getDetailsByIdNew($id,$pooled_trust_id){
		$query = PooledTrustDetailMaster::where('trust_id',$id)->where('pooled_trust_id',$pooled_trust_id)->where('del_flag','N')->count();
		return $query;
		
	}
}