<?php

namespace App\Services;

use App\Model\HHALog;
use App\Model\HHASendLog;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HHALogService
{

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $insert = new HHALog($data);
        $insert->save();
        return $insert->id;
    }

    public function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];
        return HHALog::where($where)->update($data);
    }

    public function getSendLogList($search)
    {
        $query = HHALog::select(
                'hha_module_log.id',
                'hha_module_log.patient_id',
                'hha_module_log.hha_patient_id as caregiver_id',
                'hha_module_log.type',
                'hha_module_log.hha_module_type',
                'hha_module_log.action',
                'hha_module_log.created_date',
                'hha_module_log.created_by',
                'pt.first_name as patient_first_name',
                'pt.last_name as patient_last_name',
                'users.first_name as created_first_name',
                'users.last_name as created_last_name'
            )
            ->leftJoin('patient_master as pt', 'pt.id', '=', 'hha_module_log.patient_id')
            ->leftJoin('users', 'users.id', '=', 'hha_module_log.created_by');

        if (isset($search['created_date']) && $search['created_date'] != "") {
            $explode = explode('-', $search['created_date']);
            $startDate = trim($explode[0]) . ' 00:00:00';
            $endDate = trim($explode[1]) . ' 23:59:59';
            $query->where('hha_module_log.created_date', '>=', Utility::convertYMDTime($startDate))
                  ->where('hha_module_log.created_date', '<=', Utility::convertYMDTime($endDate));
        }

        $query->where('hha_module_log.del_flag','N')->orderBy('hha_module_log.created_date', 'desc');

        return $query->paginate(50);
    }

    public function getSendLogById($id)
    {
        return HHALog::where('id', $id)->first();
    }

}