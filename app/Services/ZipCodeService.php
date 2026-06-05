<?php

namespace App\Services;

use App\ZipCode;

class ZipCodeService
{

	public static function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new ZipCode($data);
		$insert->save();
		return $insert->id;
	}
	public static function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		return ZipCode::where($where)->update($data);
	}
	public static function softDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		return ZipCode::where($where)->update($data);
	}

    public function getById($id){
		return ZipCode::where('id',$id)->where('deleted_flag','N')->first();
	}

	/**
     * Get paginated list
     */
    public static function getList($search = null, $paginate = true)
    {
        $query = ZipCode::whereNotNull('zip_code');
        if (isset($search['zipcode']) && $search['zipcode'] != "") {
            $query->where('zip_code', 'LIKE', '%' . $search['zipcode'] . '%');
        }
		if (isset($search['county']) && $search['county'] != "") {
            $query->where('county', '=', $search['county']);
        }
		$query->where('deleted_flag','N');
        $query->orderBy('id', 'desc');
        if ($paginate) {
            return $query->paginate(10);
        }
        return $query->get();
    }

	/**
     * Get department by ID with users
     */
    public static function getAllCounty()
    {
        return ZipCode::distinct('zip_code')->select('county')->get();
    }
}
