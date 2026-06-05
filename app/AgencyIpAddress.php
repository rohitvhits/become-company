<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgencyIpAddress extends Model
{
    public $timestamps = false;
    protected $table = 'agency_ip_address';
    protected $fillable = ['id', 'agency_id', 'ip_address', 'type', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public static function agencyWiseIpAddress($id)
    {
        $query = AgencyIpAddress::where('agency_id', $id)->where('delflag', 'N')->paginate(10);
        return $query;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = now();
        $data['deleted_by'] = $auth['id'];

        $update = AgencyIpAddress::where($where)->update($data);
        return $update;
    }
    public static function editIpData($id)
    {
        $query = AgencyIpAddress::where('id', $id)->first();
        return $query;
    }
    public static function updateIpData($data, $id)
    {
        $query = AgencyIpAddress::where('id', $id)->update($data);
        return $query;
    }
}
