<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\InflowcarePatientLogs;
use Illuminate\Support\Facades\Log;

class InflowcarePatientLogService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";

        $insert = new InflowcarePatientLogs($data);
        $insert->save();
        return $insert->id;
    }

    public function getListing($filters = [], $pagination="")
    {
        $query = InflowcarePatientLogs::with(['patient', 'agency', 'userDetail'])
            ->whereHas('patient');

        if (!empty($filters['agency_id'])) {
            $query->whereHas('patient', function ($q) use ($filters) {
                $q->whereIn('agency_id', $filters['agency_id']);
            });
        }

        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('created_at', [
                $filters['from_date'] . ' 00:00:00',
                $filters['to_date'] . ' 23:59:59'
            ]);
        }

        if($pagination !=""){
            return $query->orderBy('id', 'desc')->get();
        }
        return $query->orderBy('id', 'desc')
            ->paginate(50);
    }
}
