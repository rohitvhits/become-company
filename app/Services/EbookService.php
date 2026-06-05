<?php
namespace App\Services;
use App\Model\Ebook;

class EbookService{

    public function ebookList()
	{
		$auth = auth()->user();

		$query = Ebook::select('ebook.id','title','ebook.type','users.first_name','users.last_name','ebook.created_at')->leftjoin('users',function($join){
			$join->on('users.id','=','ebook.created_by');
			$join->where('users.delete_flag','N');
		})->where('deleted_flag','N');
		
		if(!in_array($auth->id,['482','494','500'])){
			if($auth->agency_fk !=""){
				$query->where('ebook.type',1);
			}else{
				$query->where('ebook.type',0);
			}
		}
		$query = $query->orderBy('id','desc')->paginate(50);
		return $query;
	}

    public  function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new Ebook($data);
		$insert->save();
		$insert_ids = $insert->id;
		return $insert_ids;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		$update = Ebook::where($where)->update($data);
		return $update;
	}

    public  function SoftDelete($where)
	{
		$auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		$data['deleted_flag'] = 1;
		$update = Ebook::where($where)->update($data);
		return $update;
	}

	public function getDetailById($id)
	{
		$query = Ebook::where('id', $id);
		$query = $query->first();
		return $query;
	}

	public function ebookAllDataTypeWise()
	{
		$auth = auth()->user();
		$roles = $auth->roles->pluck('name')->toArray();
		$query = Ebook::select('ebook.id','title','ebook.type','video','content')->where('deleted_flag','N');	
		$checkWhere = 0;
		if (in_array('Super Admin',$roles)) {
			$query->whereRaw("FIND_IN_SET(1, ebook.type)");
			$checkWhere++;
		} 
		if (in_array('Ny Best Employee',$roles)) { // Ny Best User
			if($checkWhere > 0){
				$query->orWhereRaw("FIND_IN_SET(2, ebook.type)");
			}else{
				$query->WhereRaw("FIND_IN_SET(2, ebook.type)");
			}
			$checkWhere++;
		}
		if (in_array('Agency User',$roles)) { // Agency User
			if($checkWhere > 0){
				$query->orWhereRaw("FIND_IN_SET(3, ebook.type)");
			}else{
				$query->WhereRaw("FIND_IN_SET(3, ebook.type)");
			}
		}
		$query->orWhereRaw("FIND_IN_SET(0, ebook.type)");
		$query = $query->orderBy('id','desc')->get()->toArray();
		return $query;
	}
}