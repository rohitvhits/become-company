<?php

namespace App\Services;

use App\Model\UserDocApproval;

class UserDocApprovalService
{

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        if (isset($auth['id'])) {
            $data['created_by'] = $auth['id'];
        }
        $insert = new UserDocApproval($data);
        $insert_id = $insert->save();

        return $insert_id;
    }
    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        $update = UserDocApproval::where($where)->update($data);
        return $update;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = UserDocApproval::where($where)->update($data);
        return $update;
    }

    public static function searchApprovalUser($search)
	{
		$query=  UserDocApproval::selectRaw('users.id,users.first_name,users.last_name')->leftjoin('users', function ($join) {
                $join->on('user_doc_approval.user_id', 'users.id');
            })->where('users.delete_flag','N');
            if($search !=""){
                $query->whereRaw('CONCAT(users.first_name," ",users.last_name) LIKE "%'.$search.'%"');
            }
            $query=  $query->where('del_flag','N')->get();
          return $query;
	}

    public static function getUserIdsByType($type)
	{
		return UserDocApproval::select('user_id')->where('type',$type)->get();
	}

    public static function getUserIdsByTypeDetails($type)
	{
		return UserDocApproval::where('type',$type)->where('del_flag','N')->get()->toArray();
	}

    public static function getAll($userId = '', $key = '')
    {
        $query = UserDocApproval::with('createdBy:id,first_name,last_name')
            ->where('del_flag', 'N');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($key) {
            $query->where('key', $key);
        }
        return $query->orderBy('id', 'desc')->paginate(20);
    }

    public static function getById($id)
    {
        return UserDocApproval::where('id', $id)->where('del_flag', 'N')->first();
    }

    public static function countByUser($userId, $excludeId = null)
    {
        $query = UserDocApproval::where('user_id', $userId)->where('del_flag', 'N');
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->get();
    }
}