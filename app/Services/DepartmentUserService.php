<?php

namespace App\Services;

use App\Model\DepartmentUser;

class DepartmentUserService
{

	public static function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new DepartmentUser($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
	public static  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = DepartmentUser::where($where)->update($data);
		return $update;
	}
	public static function softDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = DepartmentUser::where($where)->update($data);
		return $update;
	}

	/**
     * Get department by ID with users
     */
    public static function getDetailsById($id)
    {
        return DepartmentUser::find($id);
    }

	public static function bulkUpdate($id,$usersToDelete){
		return DepartmentUser::where('department_id', $id)
                ->whereIn('user_id', $usersToDelete)
                ->where(function($q) {
                    $q->whereNull('del_flag')->orWhere('del_flag', 'N');
                })
                ->update([
                    'del_flag' => 'Y',
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'deleted_by' => auth()->user()->id
                ]);
	}

	public static function getDepartment($id){
		return DepartmentUser::where('department_id', $id)
            ->where(function($q) {
                $q->whereNull('del_flag')->orWhere('del_flag', 'N');
            })
            ->pluck('user_id')
            ->toArray();
	}
}
