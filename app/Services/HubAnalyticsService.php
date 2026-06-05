<?php

namespace App\Services;

use App\Model\HubRecord;
use App\Model\HubRecordAgency;
use App\Model\HubCompany;
use App\Model\HubThirdPartyLog;
use App\Model\HubRecordImportLog;
use App\Model\HubRecordDoc;
use App\Model\HubRecordNotes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HubAnalyticsService
{
    public function getRecordStats($dateRange = null, $agencyIds = [])
    {
        // Get stats from hub_record_agency table for accurate active/inactive counts
        $query = HubRecord::query()
            ->leftjoin('hub_record_agency', function ($join) {
                $join->on('hub_record.id', '=', 'hub_record_agency.hub_record_id');
                $join->where('hub_record_agency.del_flag', 'N');
            })
            ->where('hub_record.deleted_flag', 'N');


        if (!empty($agencyIds)) {
            $query->whereIn('hub_record_agency.agency_id', $agencyIds);
        }

        if ($dateRange) {
            $query->whereBetween('hub_record.created_date', $dateRange);
        }

        $totalQuery = clone $query;
        $activeQuery = clone $query;
        $inactiveQuery = clone $query;

        // For dependents, we check the hub_record table
        $dependentQuery = HubRecord::where('deleted_flag', 'N');
        if (!empty($agencyIds)) {
            $dependentQuery->whereHas('hubRecordAgencies', function ($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds)
                    ->where('del_flag', 'N');
            });
        }
        if ($dateRange) {
            $dependentQuery->whereBetween('created_date', $dateRange);
        }

        return [
            'total_records' => $totalQuery->distinct('hub_record.id')->count('hub_record.id'),
            'active_records' => $activeQuery->where('hub_record_agency.status', 'active')->distinct('hub_record.id')->count(),
            'inactive_records' => $inactiveQuery->where('hub_record_agency.status', 'deactivated')->distinct('hub_record.id')->count(),
            'records_with_dependents' => $dependentQuery->where('is_dependent', 'Y')->distinct('hub_record.id')->count(),
        ];
    }

    public function getAgencyStats($statusFilter = null)
    {
        // Get agency stats using the junction table for more accurate counts with status breakdown
        $query = DB::table('hub_company')
            ->leftJoin('hub_record_agency', function ($join) use ($statusFilter) {
                $join->on('hub_company.id', '=', 'hub_record_agency.agency_id')
                    ->where('hub_record_agency.del_flag', '=', 'N');
                if ($statusFilter) {
                    $join->where('hub_record_agency.status', '=', $statusFilter);
                }
            })
            ->leftJoin('hub_record', function ($join) {
                $join->on('hub_record_agency.hub_record_id', '=', 'hub_record.id')
                    ->where('hub_record.deleted_flag', '=', 'N');
            })
            ->select('hub_company.id', 'hub_company.agency_name')
            ->selectRaw('COUNT(DISTINCT hub_record.id) as hub_records_count')
            ->selectRaw('COUNT(DISTINCT CASE WHEN hub_record_agency.status = "active" THEN hub_record.id END) as active_count')
            ->selectRaw('COUNT(DISTINCT CASE WHEN hub_record_agency.status = "deactivated" THEN hub_record.id END) as inactive_count')
            ->where('hub_company.delete_flag', 'N')
            ->where('hub_company.show_hub', 1)
            ->groupBy('hub_company.id', 'hub_company.agency_name')
            ->orderBy('hub_records_count', 'desc');

        return $query->get();
    }

    public function getApiStats($dateRange = null)
    {
        $query = HubThirdPartyLog::query();

        if ($dateRange) {
            $query->whereBetween('created_date', $dateRange);
        }

        $totalQuery = clone $query;
        $endpointsQuery = clone $query;
        $typeQuery = clone $query;
        $dateQuery = clone $query;

        return [
            'total_calls' => $totalQuery->count(),
            'unique_endpoints' => $endpointsQuery->distinct('url')->count(),
            'calls_by_endpoint' => $typeQuery->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->orderBy('count', 'desc')
                ->get(),
            'calls_by_date' => $dateQuery->select(
                DB::raw('DATE(created_date) as date'),
                DB::raw('count(*) as count')
            )
                ->groupBy('date')
                ->orderBy('date')
                ->limit(30)
                ->get()
        ];
    }

    public function getImportStats($dateRange = null)
    {
        // Use exact same query structure as Import History
        $auth = auth()->user();
        $where = 'hub_record_import_logs.deleted_flag = "N"';

        if (in_array($auth['user_type_fk'], array(184))) {
            $agencyids = \App\Helpers\Utility::getUserWiseAgency();
            if(!empty($agencyids)){
                $implodeIds = implode('","', $agencyids);
                $where .= ' and hub_record_import_logs.agency_id IN("' . $implodeIds . '")';
            }
        }

        $query = HubRecordImportLog::leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'hub_record_import_logs.agency_id');
                $join->where('agency.delete_flag', 'N');
            })
            ->whereRaw($where);

        if ($dateRange) {
            $query->whereBetween('hub_record_import_logs.created_date', $dateRange);
        }

        $totalQuery = clone $query;
        $successQuery = clone $query;
        $failedQuery = clone $query;
        $recordsQuery = clone $query;
        $agencyQuery = clone $query;

        return [
            'total_imports' => $totalQuery->count(),
            'successful_imports' => $successQuery->where('hub_record_import_logs.status', 'completed')->count(),
            'failed_imports' => $failedQuery->where('hub_record_import_logs.status', 'failed')->count(),
            'total_records_imported' => $recordsQuery->sum('hub_record_import_logs.successful_records'),
            'average_records_per_import' => round($recordsQuery->avg('hub_record_import_logs.total_records'), 2),
            'imports_by_agency' => $agencyQuery->select('hub_record_import_logs.agency_id')
                ->with('agencyDetail:id,agency_name')
                ->selectRaw('count(*) as import_count')
                ->groupBy('hub_record_import_logs.agency_id')
                ->orderBy('import_count', 'desc')
                ->get()
        ];
    }

    public function getDataQualityStats($agencyIds = [])
    {
        $baseQuery = HubRecord::where('deleted_flag', 'N');

        // Apply agency filter if provided
        if (!empty($agencyIds)) {
            $baseQuery->whereHas('hubRecordAgencies', function ($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds)
                    ->where('del_flag', 'N');
            });
        }

        $totalRecords = $baseQuery->count();

        if ($totalRecords == 0) {
            return [
                'total_records' => 0,
                'missing_email' => 0,
                'missing_phone' => 0,
                'missing_ssn' => 0,
                'invalid_emails' => 0,
                'duplicate_potential' => 0
            ];
        }

        // Clone the base query for each metric
        $missingEmailQuery = clone $baseQuery;
        $missingPhoneQuery = clone $baseQuery;
        $missingSsnQuery = clone $baseQuery;
        $invalidEmailQuery = clone $baseQuery;

        // Build duplicate query with agency filter
        $duplicateQuery = DB::table('hub_record')
            ->select('first_name', 'last_name', 'dob')
            ->where('deleted_flag', 'N');

        if (!empty($agencyIds)) {
            $duplicateQuery->whereExists(function ($query) use ($agencyIds) {
                $query->select(DB::raw(1))
                    ->from('hub_record_agency')
                    ->whereRaw('hub_record_agency.hub_record_id = hub_record.id')
                    ->whereIn('hub_record_agency.agency_id', $agencyIds)
                    ->where('hub_record_agency.del_flag', 'N');
            });
        }

        return [
            'total_records' => $totalRecords,
            'missing_email' => $missingEmailQuery->where(function ($q) {
                $q->whereNull('email')->orWhere('email', '');
            })->count(),
            'missing_phone' => $missingPhoneQuery->where(function ($q) {
                $q->whereNull('mobile')->orWhere('mobile', '');
            })->count(),
            'missing_ssn' => $missingSsnQuery->where(function ($q) {
                $q->whereNull('ssn')->orWhere('ssn', '');
            })->count(),
            'invalid_emails' => $invalidEmailQuery->whereNotNull('email')
                ->where('email', '!=', '')
                ->whereRaw('email NOT REGEXP "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$"')
                ->count(),
            'duplicate_potential' => $duplicateQuery->groupBy('first_name', 'last_name', 'dob')
                ->havingRaw('count(*) > 1')
                ->count()
        ];
    }

    public function getRecordGrowthChart($dateRange = null, $agencyIds = [])
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        $query = HubRecord::where('deleted_flag', 'N')
            ->whereBetween('created_date', [$startDate, $endDate]);

        if (!empty($agencyIds)) {
            $query->whereHas('hubRecordAgencies', function ($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds)
                    ->where('del_flag', 'N');
            });
        }

        $data = $query->select(
            DB::raw('DATE(created_date) as date'),
            DB::raw('count(*) as count')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            }),
            'data' => $data->pluck('count')
        ];
    }

    public function getAgencyComparisonChart($agencyIds = [])
    {
        $agencies = $this->getAgencyStats();

        // If specific agencies are selected, filter to only those agencies
        if (!empty($agencyIds)) {
            $agencies = $agencies->whereIn('id', $agencyIds);
        }

        $agencies = $agencies->take(10);

        return [
            'labels' => $agencies->pluck('agency_name'),
            'data' => $agencies->pluck('hub_records_count')
        ];
    }

    public function getApiUsageChart($dateRange = null)
    {
        $apiStats = $this->getApiStats($dateRange);

        return $apiStats['calls_by_endpoint']->map(function ($item) {
            return [
                'label' => $item->type ?: 'Unknown',
                'value' => $item->count
            ];
        });
    }

    public function getImportSuccessChart($dateRange = null)
    {
        $importStats = $this->getImportStats($dateRange);

        return [
            'labels' => ['Successful', 'Failed'],
            'data' => [
                $importStats['successful_imports'],
                $importStats['failed_imports']
            ]
        ];
    }

    public function getStatusDistributionChart($agencyIds = [])
    {
        // Get status from hub_record_agency table for accurate active/inactive distribution
        $query = HubRecord::leftjoin('hub_record_agency', 'hub_record.id', '=', 'hub_record_agency.hub_record_id')
            ->where('hub_record.deleted_flag', 'N')
            ->where('hub_record_agency.del_flag', 'N');

        if (!empty($agencyIds)) {
            $query->whereIn('hub_record_agency.agency_id', $agencyIds);
        }

        $data = $query->select('hub_record_agency.status', DB::raw('COUNT(DISTINCT hub_record.id) as count'))
            ->groupBy('hub_record_agency.status')
            ->get();

        return [
            'labels' => $data->pluck('status'),
            'data' => $data->pluck('count')
        ];
    }

    public function getGenderDistributionChart($agencyIds = [])
    {
        $query = HubRecord::where('deleted_flag', 'N');

        if (!empty($agencyIds)) {
            $query->whereHas('hubRecordAgencies', function ($q) use ($agencyIds) {
                $q->whereIn('agency_id', $agencyIds)
                    ->where('del_flag', 'N');
            });
        }

        $data = $query->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();

        return [
            'labels' => $data->pluck('gender'),
            'data' => $data->pluck('count')
        ];
    }

    public function getRecentActivity($limit = 10)
    {
        return DB::table('hub_record')
            ->leftJoin('users', 'hub_record.created_by', '=', 'users.id')
            ->leftJoin('hub_record_agency', function ($join) {
                $join->on('hub_record.id', '=', 'hub_record_agency.hub_record_id')
                    ->where('hub_record_agency.del_flag', '=', 'N');
            })
            ->leftJoin('hub_company', 'hub_record_agency.agency_id', '=', 'hub_company.id')
            ->select(
                'hub_record.id',
                'hub_record.first_name',
                'hub_record.last_name',
                'hub_record_agency.status', // Get status from junction table
                'hub_record.created_date',
                'users.first_name as creator_first_name',
                'users.last_name as creator_last_name',
                'hub_company.agency_name'
            )
            ->where('hub_record.deleted_flag', 'N')
            ->orderBy('hub_record.created_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'name' => $record->first_name . ' ' . $record->last_name,
                    'agency' => $record->agency_name ?: 'N/A',
                    'created_by' => $record->creator_first_name ? $record->creator_first_name . ' ' . $record->creator_last_name : 'N/A',
                    'created_date' => Carbon::parse($record->created_date)->format('M d, Y'),
                    'status' => $record->status ?: 'N/A'
                ];
            });
    }

    public function getDocumentStats()
    {
        return [
            'total_documents' => HubRecordDoc::where('deleted_flag', 'N')->count(),
            'documents_this_month' => HubRecordDoc::where('deleted_flag', 'N')
                ->whereMonth('created_date', Carbon::now()->month)
                ->whereYear('created_date', Carbon::now()->year)
                ->count(),
            'average_docs_per_record' => round(
                HubRecordDoc::where('deleted_flag', 'N')->count() /
                    max(HubRecord::where('deleted_flag', 'N')->count(), 1),
                2
            )
        ];
    }

    public function getNotesStats()
    {
        return [
            'total_notes' => HubRecordNotes::where('delete_flag', 'N')->count(),
            'notes_this_month' => HubRecordNotes::where('delete_flag', 'N')
                ->whereMonth('created_date', Carbon::now()->month)
                ->whereYear('created_date', Carbon::now()->year)
                ->count(),
            'average_notes_per_record' => round(
                HubRecordNotes::where('delete_flag', 'N')->count() /
                    max(HubRecord::where('deleted_flag', 'N')->count(), 1),
                2
            )
        ];
    }

    public function getDashboardSummary($dateRange = null, $agencyIds = [], $statusFilter = null)
    {
        return [
            'records' => $this->getRecordStats($dateRange, $agencyIds),
            'agencies' => $this->getAgencyStats($statusFilter),
            'api' => $this->getApiStats($dateRange),
            'imports' => $this->getImportStats($dateRange),
            'data_quality' => $this->getDataQualityStats($agencyIds),
            'documents' => $this->getDocumentStats(),
            'notes' => $this->getNotesStats(),
            'recent_activity' => $this->getRecentActivity()
        ];
    }

    public function getDeactivationTrendChart($dateRange = null, $agencyIds = [])
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        // Get deactivation trend from hub_record_agency table
        $query = DB::table('hub_record_agency')
            ->join('hub_record', 'hub_record_agency.hub_record_id', '=', 'hub_record.id')
            ->where('hub_record_agency.del_flag', 'N')
            ->where('hub_record.deleted_flag', 'N')
            ->where('hub_record_agency.status', 'deactivated')
            ->whereNotNull('hub_record_agency.deactivated_date')
            ->whereBetween('hub_record_agency.deactivated_date', [$startDate, $endDate]);

        if (!empty($agencyIds)) {
            $query->whereIn('hub_record_agency.agency_id', $agencyIds);
        }

        $data = $query->select(
            DB::raw('DATE(hub_record_agency.deactivated_date) as date'),
            DB::raw('count(*) as count')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            }),
            'data' => $data->pluck('count')
        ];
    }
}
