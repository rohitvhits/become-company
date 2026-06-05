<?php

namespace App\Services;

use App\Model\BranchListLink;

class BranchListLinkService
{
    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = 'N';
        $insert = new BranchListLink($data);
        $insert->save();
        return $insert->id;
    }

    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];
        return BranchListLink::where($where)->update($data);
    }

    public static function softDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];
        return BranchListLink::where($where)->update($data);
    }

    public static function getList($search = null, $paginate = true)
    {
        $query = BranchListLink::with([
            'branch:id,branch_name',
            'agency:id,agency_name',
            'service:id,name,types',
            'createdUsers:id,first_name,email,last_name'
        ]);

        if (isset($search['branch_id']) && $search['branch_id'] != "") {
            $query->where('branch_id', '=', $search['branch_id']);
        }
        if (isset($search['agency_id']) && $search['agency_id'] != "") {
            $query->where('agency_id', '=', $search['agency_id']);
        }
        if (isset($search['created_date']) && $search['created_date'] != "") {
            $explode = explode('-', $search['created_date']);
            $query->whereDate('branch_list_link.created_at', '>=', date('Y-m-d', strtotime($explode[0])))
                  ->whereDate('branch_list_link.created_at', '<=', date('Y-m-d', strtotime($explode[1])));
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
        return BranchListLink::where('id', $id)->where('del_flag', 'N')->first();
    }

    public function getDetailsById($id)
    {
        return BranchListLink::with(['branch:id,branch_name', 'agency:id,agency_name', 'service:id,name'])->find($id);
    }

    public static function getLinksByBranchId($branchId)
    {
        return BranchListLink::where('branch_id', $branchId)
            ->where('del_flag', 'N')
            ->get();
    }

    public static function softDeleteByBranchId($branchId)
    {
        $auth = auth()->user();
        return BranchListLink::where('branch_id', $branchId)
            ->where('del_flag', 'N')
            ->update([
                'del_flag' => 'Y',
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => $auth['id']
            ]);
    }

    public static function getBranchesByAgencyAndServices($agencyId, $serviceIds)
    {
        return BranchListLink::select('branch_list_link.branch_id', 'branch_list.branch_name')
            ->join('branch_list', 'branch_list.id', '=', 'branch_list_link.branch_id')
            ->where('branch_list_link.agency_id', $agencyId)
            ->whereIn('branch_list_link.service_id', $serviceIds)
            ->where('branch_list_link.del_flag', 'N')
            ->where('branch_list.del_flag', 'N')
            ->where('branch_list.status', 1)
            ->groupBy('branch_list_link.branch_id', 'branch_list.branch_name')
            ->orderBy('branch_list.branch_name', 'asc')
            ->get();
    }

    public static function checkMandatory($agencyId, $serviceIds)
    {
        return BranchListLink::select('branch_list_link.branch_id')
            ->leftjoin('branch_list', 'branch_list.id', '=', 'branch_list_link.branch_id')
            ->where('branch_list_link.agency_id', $agencyId)
            ->whereIn('branch_list_link.service_id', $serviceIds)
            ->where('branch_list_link.del_flag', 'N')
            ->where('branch_list_link.is_val_mandatory', 1)
            ->where('branch_list.del_flag', 'N')
            ->where('branch_list.status', 1)
            ->groupBy('branch_list_link.branch_id', 'branch_list.branch_name')
            ->orderBy('branch_list.branch_name', 'asc')
            ->get();
    }
}
