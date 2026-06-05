<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use App\Model\AgencyWiseDomain;

class AgencyWiseDomainHelper
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
        $insert = new AgencyWiseDomain($data);
        $insert->save();

        $inserts = $insert->id;
        return $inserts;
    }
    public static  function update($data, $where)
    {
        $insert = AgencyWiseDomain::where($where)->update($data);
        return $insert;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = AgencyWiseDomain::where($where)->update($data);
        return $update;
    }
    public static function domainListByAgencyId($id)
    {
        $query = AgencyWiseDomain::select('id', 'domain', 'created_at')->where('del_flag', 'N')->where('agency_id',$id)->orderBy('id', 'desc')->paginate(50);
        return $query;
    }

    public static function totalDomainAgency($id)
    {
        $query = AgencyWiseDomain::where('del_flag', 'N')->where('agency_id', $id)->count();
        return $query;
    }

    public static function getDomainList($agencyId){
        $query = AgencyWiseDomain::select('id','domain')->where('del_flag', 'N')->where('agency_id', $agencyId)->get();
        return $query;
    }
    public static function getDomainById($agencyId){
        $query = AgencyWiseDomain::select('id','domain','agency_id')->where('del_flag', 'N')->where('agency_id', $agencyId)->first();
        return $query;
    }
    public static function getDetailsById($id){
        $query = AgencyWiseDomain::select('id','domain','agency_id')->where('del_flag', 'N')->where('id', $id)->first();
        return $query;
    }
    public static function getDomainByAgencyId($id)
    {
        $query = AgencyWiseDomain::select('id', 'domain', 'created_at')->where('del_flag', 'N')->whereRaw('sha1(agency_id) = "' . $id . '"')->orderBy('id', 'desc')->get();
        
        return $query;
    }

    public static function createDomain($target_agency_id,$domain){
        $res = AgencyWiseDomain::select('id')->where('domain', $domain)->where('del_flag', 'N')->where('agency_id', $target_agency_id)->first();
        if(empty($res['id'])){
            $data = array(
                'domain' => $domain,
                'agency_id' => $target_agency_id
            );
            self::save($data);
        }
        return $res;
    }
}
