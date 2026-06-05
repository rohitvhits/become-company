<?php

namespace App\Services;

use App\Model\BranchList;

class BranchListService
{
    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = 'N';
        $insert = new BranchList($data);
        $insert->save();
        return $insert->id;
    }

    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];
        return BranchList::where($where)->update($data);
    }

    public static function softDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];
        return BranchList::where($where)->update($data);
    }

    public static function getList($search = null, $paginate = true)
    {
        $query = BranchList::with(['createdUsers:id,first_name,email,last_name']);

        if (isset($search['branch_name']) && $search['branch_name'] != "") {
            $query->where('branch_name', 'LIKE', '%' . $search['branch_name'] . '%');
        }
        if (isset($search['created_date']) && $search['created_date'] != "") {
            $explode = explode('-', $search['created_date']);
            $query->whereDate('branch_list.created_at', '>=', date('Y-m-d', strtotime($explode[0])))
                ->whereDate('branch_list.created_at', '<=', date('Y-m-d', strtotime($explode[1])));
        }
        if (isset($search['created_by']) && $search['created_by'] != "") {
            $query->where('created_by', '=', $search['created_by']);
        }

        $query->where('del_flag', 'N');
        $query->orderBy('id', 'desc');

        if ($paginate) {
            return $query->paginate(10);
        }
        return $query->get();
    }

    public function getById($id)
    {
        return BranchList::select('id', 'branch_name', 'status')
            ->where('id', $id)
            ->where('del_flag', 'N')
            ->first();
    }

    public function getDetailsById($id)
    {
        return BranchList::with('branchLinks')->find($id);
    }

    public function getAllActiveBranches()
    {
        return BranchList::select('id', 'branch_name')
            ->where('status', 1)
            ->where('del_flag', 'N')
            ->orderBy('branch_name', 'asc')
            ->get();
    }

    public function getAllBranches()
    {
        return BranchList::select('id', 'branch_name')
            ->where('del_flag', 'N')
            ->orderBy('branch_name', 'asc')
            ->get();
    }

    public function getBranchNameById($branchIds)
    {
        return BranchList::whereIn('id', $branchIds)->where('del_flag', 'N')->pluck('branch_name')->toArray();
    }
}
