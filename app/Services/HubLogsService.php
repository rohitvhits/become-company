<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\HubLogs;

class HubLogsService
{

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        if (isset($auth['id'])) {
            $data['created_by'] = $auth['id'];
        }
        $insert = new HubLogs($data);
        $insert_id = $insert->save();

        return $insert_id;
    }
    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        $update = HubLogs::where($where)->update($data);
        return $update;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = HubLogs::where($where)->update($data);
        return $update;
    }

    public static function insert($data)
    {
        return  HubLogs::insert($data);
    }

    public static function getData($username = "", $createdAt = "", $field = "", $sort = "", $exportData = "")
    {

        $query = HubLogs::selectRaw('logs.*,CONCAT(" ",users.first_name," ",users.last_name) as username')->whereNull('logs.deleted_at')
            ->leftjoin('users', function ($join) {
                $join->on('logs.created_by', 'users.id');
            });

        if ($username) {
            $query->where(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%" . $username . "%");
        }
        if ($createdAt != '') {
            $explodes = explode('-', $createdAt);
            $query->whereBetween("logs.created_at", [date('Y-m-d 00:00:00', strtotime($explodes[0])), date('Y-m-d 23:59:59', strtotime($explodes[1]))]);
        }


        if ($field) {
            if ($field == 'user_name') {
                $query->orderBy('users.first_name', $sort);
            }

            if ($field == 'created_at') {
                $query->orderBy('logs.created_at', $sort);
            }
            if ($field == 'type') {
                $query->orderBy('logs.type', $sort);
            }
            if ($field == 'module') {
                $query->orderBy('logs.module', $sort);
            }
            if ($field == 'ip') {
                $query->orderBy('logs.ip', $sort);
            }
            if ($field == 'id') {
                $query->orderBy('logs.id', $sort);
            }
        } else {
            $query->orderBy('logs.id', 'desc');
        }
        if ($exportData) {
            return  $query->get();
        }
        return  $query->paginate(50);
    }

    public static function getDatByUserID($id)
    {
        $query = HubLogs::selectRaw('logs.*,CONCAT(" ",users.first_name," ",users.last_name) as username')->whereNull('logs.deleted_at')->where('logs.created_by', auth()->user()->id)
            ->leftjoin('users', function ($join) {
                $join->on('logs.created_by', 'users.id');
            })
            ->whereNull('logs.deleted_at')
            ->where('logs.object_id', $id)
            ->where('logs.module', 'User')
            ->orderBy('logs.id', 'desc');
        return  $query->paginate(10);
    }

    public static function getDatByAllLog($id, $moduleName)
    {
        $query = HubLogs::with('user')->whereNull('deleted_at')->where('object_id', $id)->where('created_by', auth()->user()->id)->where('module', $moduleName)->orderBy('id', 'desc')->paginate(10);
        return  $query;
    }

    public static function getDataPatient($patientId = "", $exportData = "")
    {

        $query = HubLogs::selectRaw('logs.*,CONCAT(" ",users.first_name," ",users.last_name) as username')->whereNull('logs.deleted_at')
            ->leftjoin('users', function ($join) {
                $join->on('logs.created_by', 'users.id');
            });

        $query->where('logs.object_id', $patientId);
        $query->orderBy('logs.id', 'desc');
        if ($exportData) {
            return  $query->get();
        }
        $query = $query->paginate(50);
        return  $query;
    }

    public static function getDatByLogsId($id)
    {
        return HubLogs::with('user')->where('id', $id)->first();
    }
    public static function getAllAppointmentLogs($id, $moduleName)
    {
        $query = HubLogs::with('user')->whereNull('deleted_at')->where('object_id', $id)->where('module', $moduleName)->orderBy('id', 'desc')->paginate(10);
        return  $query;
    }

    public static function getAllHubLogs($id, $moduleName)
    {
        $query = HubLogs::with('user')->whereNull('deleted_at')->where('object_id', $id)->where('module', $moduleName)->orderBy('id', 'desc')->paginate(10);
        return  $query;
    }

    public static function getAllLogs($search)
    {
        $query = HubLogs::select('id', 'object_id', 'created_by', 'created_at', 'module', 'type', 'ip', 'message', 'old_response', 'new_response')->with('user:id,first_name,last_name')->whereNull('deleted_at');
        if (isset($search['type']) && !empty($search['type'])) {
            $query->where('type', $search['type']);
        }
        if (isset($search['module']) && !empty($search['module'])) {
            $query->where('module', $search['module']);
        }
        if (isset($search['created_by']) && !empty($search['created_by'])) {
            $query->where('logs.created_by', $search['created_by']);
        }
        if (isset($search['created_date']) && !empty($search['created_date'])) {
            $explodes = explode('-', $search['created_date']);
            $query->whereBetween("logs.created_at", [date('Y-m-d 00:00:00', strtotime($explodes[0])), date('Y-m-d 23:59:59', strtotime($explodes[1]))]);
        }
        $query = $query->orderBy('id', 'desc')->simplePaginate(20);
        return  $query;
    }

    public function getAllType()
    {
        $query = HubLogs::select('type')->whereNull('deleted_at')->whereNotnull('type')->groupBy('type');
        $query = $query->orderBy('id', 'desc')->get()->toArray();
        return $query;
    }

    public function getAllModule()
    {
        $query = HubLogs::select('module')->whereNull('deleted_at')->whereNotnull('module')->groupBy('module');
        $query = $query->orderBy('id', 'desc')->get()->toArray();
        return $query;
    }

    public static function getDatByLogsRes($id)
    {
        return HubLogs::select('type', 'module', 'old_response', 'new_response')->where('id', $id)->first();
    }
}
