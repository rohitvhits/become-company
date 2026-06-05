<?php

namespace App\Services;

use App\Model\Department;

class DepartmentTaskService
{

	public static function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new Department($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
	public static  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = Department::where($where)->update($data);
		return $update;
	}
	public static function softDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = Department::where($where)->update($data);
		return $update;
	}

	public function getAllDept(){
		return Department::select('id','name')->where('status',1)->where('del_flag','N')->get();
	}

    public function getById($id){
		return Department::select('id','name','status')->where('id',$id)->where('del_flag','N')->first();
	}

	/**
     * Get paginated list
     */
    public static function getList($search = null, $paginate = true)
    {
        $query = Department::with(['users:id','createdUsers:id,first_name,email,last_name'])->withCount('users');
        if (isset($search['name']) && $search['name'] != "") {
            $query->where('name', 'LIKE', '%' . $search['name'] . '%');
        }
		if (isset($search['created_date']) && $search['created_date'] != "") {
            $explode = explode('-', $search['created_date']);
            $query->whereDate('departments.created_at', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('departments.created_at', '<=', date('Y-m-d', strtotime($explode[1])));
        }
		if (isset($search['created_by']) && $search['created_by'] != "") {
            $query->where('created_by', '=', $search['created_by']);
        }
		$query->where('del_flag','N');
        $query->orderBy('id', 'desc');
        if ($paginate) {
            return $query->paginate(10);
        }
        return $query->get();
    }

	/**
     * Get department by ID with users
     */
    public static function getDetailsById($id)
    {
        return Department::with('users:id,first_name,last_name')->find($id);
    }
}
