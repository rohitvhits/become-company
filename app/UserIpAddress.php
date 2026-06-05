<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserIpAddress extends Model
{
    public $timestamps = false;
    protected $table = 'user_ip_address';
    protected $fillable = ['id', 'user_id', 'ip_address', 'type', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by', 'delflag'];

    public static function userWiseIpAddress($id)
    {
        $query = UserIpAddress::where('user_id', $id)->where('delflag', 'N')->paginate(10);
        return $query;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = now();
        $data['deleted_by'] = $auth['id'];

        $update = UserIpAddress::where($where)->update($data);
        return $update;
    }
    public static function editIpData($id)
    {
        $query = UserIpAddress::where('id', $id)->first();
        return $query;
    }
    public static function updateIpData($data, $id)
    {
        $query = UserIpAddress::where('id', $id)->update($data);
        return $query;
    }

    public static function blockUserIpData($id)
    {
        $query = UserIpAddress::where('user_id', $id)->where('delflag','N')->where('type','BLOCK')->get()->pluck('ip_address')->toArray();
        return $query;
    }
}
