<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use App\Model\AlayacareCronLog;
class AlayacareCronLogService
{
    /**
     * Get paginated cron log listing with filters
     */
    public function getList($filters)
    {
        $query = AlayacareCronLog::leftJoin('agency', 'alayacare_cron_log.agency_id', '=', 'agency.id')
            ->select(
                'alayacare_cron_log.id',
                'alayacare_cron_log.type',
                'alayacare_cron_log.cron_type',
                'alayacare_cron_log.agency_id',
                'alayacare_cron_log.employee_id',
                'alayacare_cron_log.line',
                'alayacare_cron_log.error_log',
                'alayacare_cron_log.trace',
                'alayacare_cron_log.request_response',
                'alayacare_cron_log.return_response',
                'alayacare_cron_log.created_at',
                'agency.agency_name'
            );

        // Date range filter
        if (!empty($filters['created_date'])) {
            $explode = explode('-',$filters['created_date']);
            $query->whereDate('alayacare_cron_log.created_at', '>=', Utility::convertYMD($explode[0]))->whereDate('alayacare_cron_log.created_at', '<=', Utility::convertYMD($explode[1]));
        }

        // Type filter
        if (!empty($filters['type'])) {
            $query->where('alayacare_cron_log.type', $filters['type']);
        }

        // Cron type filter
        if (!empty($filters['cron_type'])) {
            $query->where('alayacare_cron_log.cron_type', 'like', '%' . $filters['cron_type'] . '%');
        }

        // Agency filter
        if (!empty($filters['agency_id'])) {
            $query->where('alayacare_cron_log.agency_id', $filters['agency_id']);
        }

        return $query->orderBy('alayacare_cron_log.created_at', 'desc')
            ->paginate(50);
    }

    /**
     * Get single cron log record by ID
     */
    public function getById($id)
    {
        return AlayacareCronLog::leftJoin('agency', 'alayacare_cron_log.agency_id', '=', 'agency.id')
            ->select(
                'alayacare_cron_log.*',
                'agency.agency_name'
            )
            ->where('alayacare_cron_log.id', $id)
            ->first();
    }
}
