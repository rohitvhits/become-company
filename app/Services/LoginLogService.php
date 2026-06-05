<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Model\LoginLog;

class LoginLogService
{

    public static  function insert($data)
    {
        $inser_id = new LoginLog($data);
        $inser_id->save();
        $Insert = $inser_id->id;
        return $Insert;
    }
    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $update = LoginLog::where($where)->update($data);
        return $update;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $update = LoginLog::where($where)->update($data);
        return $update;
    }

    public static function getData($username = "", $ip = "", $country = "", $countryCode = "", $loginStatus = "", $createdAt = "", $field = "", $sort = "",$userId="", $exportData = "")
    {
        $query = LoginLog::selectRaw('login_log.*,CONCAT(" ",users.first_name," ",users.last_name) as username')->whereNull('login_log.deleted_at')
            ->leftjoin('users', function ($join) {
                $join->on('login_log.user_id', 'users.id');
            });
        if ($ip) {
            $query->where('login_log.ipaddress', 'like', '%' . $ip . '%');
        }
        if ($country) {
            $query->where('login_log.country', 'like', '%' . $country . '%');
        }
        if ($countryCode) {
            $query->where('login_log.country_code', 'like', '%' . $countryCode . '%');
        }
        if ($loginStatus) {
            $query->where('login_log.login_status', 'like', '%' . $loginStatus . '%');
        }
        if ($username) {
            $query->where(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%" . $username . "%");
        }
        if ($createdAt != '') {
            $explodes = explode('-', $createdAt);
            $query->whereBetween("login_log.created_at", [date('Y-m-d 00:00:00', strtotime($explodes[0])), date('Y-m-d 23:59:59', strtotime($explodes[1]))]);
        }
        if ($userId) {
            $query->where('user_id',$userId);
        }
        
        if ($field) {
            if ($field == 'user_name') {
                $query->orderBy('login_log.agency_name', $sort);
            }
            if ($field == 'ip') {
                $query->orderBy('login_log.ipaddress', $sort);
            }
            if ($field == 'country') {
                $query->orderBy('login_log.country', $sort);
            }
            if ($field == 'country_code') {
                $query->orderBy('login_log.country_code', $sort);
            }
            if ($field == 'login_status') {
                $query->orderBy('login_log.login_status', $sort);
            }
            if ($field == 'id') {
                $query->orderBy('login_log.id', $sort);
            }
            if ($field == 'created_at') {
                $query->orderBy('login_log.created_at', $sort);
            }
        } else {
            $query->orderBy('id', 'desc');
        }
        if ($exportData) {
            return  $query->get();
        }
        return  $query->paginate(50);
    }

    public static function getDataByUserID($id)
    {
        $query = LoginLog::selectRaw('login_log.*,CONCAT(" ",users.first_name," ",users.last_name) as username')->whereNull('login_log.deleted_at')->where('login_log.user_id',$id)
            ->leftjoin('users', function ($join) {
                $join->on('login_log.user_id', 'users.id');
            })->orderBy('id', 'desc')->paginate(10);
    
        return  $query;
    }
}
