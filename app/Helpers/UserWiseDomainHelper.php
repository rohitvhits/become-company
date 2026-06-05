<?php

namespace App\Helpers;

use App\Model\UserWiseDomain;

class UserWiseDomainHelper
{
    public function __construct()
    {
    }

    public static function save($data)
    {
        $auth = auth()->user();
        $data['del_flag'] = "N";
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $insert = new UserWiseDomain($data);
        $insert->save();

        $inserts = $insert->id;
        return $inserts;
    }
    public static  function update($data, $where)
    {
        $insert = UserWiseDomain::where($where)->update($data);
        return $insert;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = UserWiseDomain::where($where)->update($data);
        return $update;
    }
    public static function domainListByUserId($id)
    {
        $query = UserWiseDomain::select('id', 'domain', 'created_at')->where('del_flag', 'N')->where('user_id',$id)->orderBy('id', 'desc')->paginate(50);
        return $query;
    }

    public static function totalDomainUser($id)
    {
        $query = UserWiseDomain::where('del_flag', 'N')->where('user_id', $id)->count();
        return $query;
    }

    public static function getDomainList($agencyId){
        $query = UserWiseDomain::select('id','domain')->where('del_flag', 'N')->where('agency_id', $agencyId)->get();
        return $query;
    }
    public static function getDetailsById($id){
        $query = UserWiseDomain::select('id','domain')->where('del_flag', 'N')->where('id', $id)->first();
        return $query;
    }
   
}
