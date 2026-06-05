<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HubAnalyticsService;
use App\Services\HubCompanyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class HubAnalyticsController extends Controller
{
    protected $hubAnalyticsService;

    public function __construct(HubAnalyticsService $hubAnalyticsService)
    {
        $this->middleware('auth');
        $this->middleware('permission:hub-analytics', ['only' => ['index']]);
        $this->hubAnalyticsService = $hubAnalyticsService;
    }

    public function index(Request $request)
    {
        $data['menu'] = 'hub-analytics';
        $data['user'] = auth()->user();
        $data['agencyList'] = HubCompanyService::getAllAgencyList();

        // Get date range from request or default to last 30 days
        $dateRange = $this->getDateRange($request);
        $agencyIds = $request->input('agency_ids', []);
        $statusFilter = $request->input('status_filter');

        // Get analytics data
        $data['recordStats'] = $this->hubAnalyticsService->getRecordStats($dateRange, $agencyIds);
        $data['agencyStats'] = $this->hubAnalyticsService->getAgencyStats($statusFilter);
        $data['apiStats'] = $this->hubAnalyticsService->getApiStats($dateRange);
        $data['importStats'] = $this->hubAnalyticsService->getImportStats($dateRange);
        $data['dataQualityStats'] = $this->hubAnalyticsService->getDataQualityStats($agencyIds);
        $data['documentStats'] = $this->hubAnalyticsService->getDocumentStats();
        $data['notesStats'] = $this->hubAnalyticsService->getNotesStats();
        $data['recentActivity'] = $this->hubAnalyticsService->getRecentActivity();

        // Chart data
        $data['recordGrowthChart'] = $this->hubAnalyticsService->getRecordGrowthChart($dateRange, $agencyIds);
        $data['deactivationTrendChart'] = $this->hubAnalyticsService->getDeactivationTrendChart($dateRange, $agencyIds);
        $data['agencyComparisonChart'] = $this->hubAnalyticsService->getAgencyComparisonChart($agencyIds);
        $data['importSuccessChart'] = $this->hubAnalyticsService->getImportSuccessChart($dateRange);
        $data['statusDistributionChart'] = $this->hubAnalyticsService->getStatusDistributionChart($agencyIds);
        $data['genderDistributionChart'] = $this->hubAnalyticsService->getGenderDistributionChart($agencyIds);

        return view('hubAnalytics.index', $data);
    }

    public function refreshData(Request $request)
    {
        try {
            $dateRange = $this->getDateRange($request);
            $agencyIds = $request->input('agency_ids', []);
            $statusFilter = $request->input('status_filter');

            $data = $this->hubAnalyticsService->getDashboardSummary($dateRange, $agencyIds, $statusFilter);

            // Add chart data
            $data['charts'] = [
                'recordGrowth' => $this->hubAnalyticsService->getRecordGrowthChart($dateRange, $agencyIds),
                'deactivationTrend' => $this->hubAnalyticsService->getDeactivationTrendChart($dateRange, $agencyIds),
                'agencyComparison' => $this->hubAnalyticsService->getAgencyComparisonChart($agencyIds),
                'importSuccess' => $this->hubAnalyticsService->getImportSuccessChart($dateRange),
                'statusDistribution' => $this->hubAnalyticsService->getStatusDistributionChart($agencyIds),
                'genderDistribution' => $this->hubAnalyticsService->getGenderDistributionChart($agencyIds)
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getChartData(Request $request)
    {
        try {
            $type = $request->input('type');
            $dateRange = $this->getDateRange($request);
            $agencyIds = $request->input('agency_ids', []);

            switch ($type) {
                case 'record-growth':
                    $data = $this->hubAnalyticsService->getRecordGrowthChart($dateRange, $agencyIds);
                    break;
                case 'deactivation-trend':
                    $data = $this->hubAnalyticsService->getDeactivationTrendChart($dateRange, $agencyIds);
                    break;
                case 'agency-comparison':
                    $data = $this->hubAnalyticsService->getAgencyComparisonChart($agencyIds);
                    break;
                case 'import-success':
                    $data = $this->hubAnalyticsService->getImportSuccessChart($dateRange);
                    break;
                case 'status-distribution':
                    $data = $this->hubAnalyticsService->getStatusDistributionChart($agencyIds);
                    break;
                case 'gender-distribution':
                    $data = $this->hubAnalyticsService->getGenderDistributionChart($agencyIds);
                    break;
                default:
                    return response()->json(['error' => 'Invalid chart type'], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting chart data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $dateRange = $this->getDateRange($request);
            $agencyIds = $request->input('agency_ids', []);
            $statusFilter = $request->input('status_filter');
            $format = $request->input('format', 'csv');

            $data = $this->hubAnalyticsService->getDashboardSummary($dateRange, $agencyIds, $statusFilter);

            // Add chart data for export
            $data['charts'] = [
                'recordGrowth' => $this->hubAnalyticsService->getRecordGrowthChart($dateRange, $agencyIds),
                'deactivationTrend' => $this->hubAnalyticsService->getDeactivationTrendChart($dateRange, $agencyIds)
            ];

            if ($format === 'csv') {
                return $this->exportToCsv($data);
            } elseif ($format === 'json') {
                return response()->json($data);
            } else {
                return response()->json(['error' => 'Invalid format'], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exportToCsv($data)
    {
        $filename = 'hub-analytics-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Write summary data
            fputcsv($file, ['Hub Analytics Summary']);
            fputcsv($file, ['Generated on', date('Y-m-d H:i:s')]);
            fputcsv($file, []);

            // Records summary
            fputcsv($file, ['Records']);
            fputcsv($file, ['Total Records', $data['records']['total_records']]);
            fputcsv($file, ['Active Records', $data['records']['active_records']]);
            fputcsv($file, ['Deactivated Records', $data['records']['inactive_records']]);
            fputcsv($file, ['Records with Dependents', $data['records']['records_with_dependents']]);
            fputcsv($file, []);


            // Import summary
            fputcsv($file, ['Imports Processed']);
            fputcsv($file, ['Total Imports', $data['imports']['total_imports']]);
            fputcsv($file, ['Successful Imports', $data['imports']['successful_imports']]);
            fputcsv($file, ['Failed Imports', $data['imports']['failed_imports']]);
            fputcsv($file, ['Total Records Imported', $data['imports']['total_records_imported']]);
            fputcsv($file, []);

            // Data Quality
            fputcsv($file, ['Data Quality Metrics']);
            fputcsv($file, ['Total Records', $data['data_quality']['total_records']]);
            fputcsv($file, ['Missing Email', $data['data_quality']['missing_email']]);
            fputcsv($file, ['Missing Phone', $data['data_quality']['missing_phone']]);
            fputcsv($file, ['Missing SSN', $data['data_quality']['missing_ssn']]);
            fputcsv($file, ['Invalid Emails', $data['data_quality']['invalid_emails']]);
            fputcsv($file, ['Potential Duplicates', $data['data_quality']['duplicate_potential']]);
            fputcsv($file, []);

            // Agency breakdown
            fputcsv($file, ['Agency Records Breakdown']);
            fputcsv($file, ['Agency Name', 'Active Count', 'Active %', 'Deactivated Count', 'Deactivated %', 'Total Records']);
            foreach ($data['agencies'] as $agency) {
                $totalRecords = $agency->hub_records_count;
                $activeCount = $agency->active_count ?? 0;
                $inactiveCount = $agency->inactive_count ?? 0;
                $activePercent = $totalRecords > 0 ? round(($activeCount / $totalRecords) * 100, 1) : 0;
                $inactivePercent = $totalRecords > 0 ? round(($inactiveCount / $totalRecords) * 100, 1) : 0;

                fputcsv($file, [
                    $agency->agency_name,
                    $activeCount,
                    $activePercent . '%',
                    $inactiveCount,
                    $inactivePercent . '%',
                    $totalRecords
                ]);
            }
            fputcsv($file, []);

            // Active Agencies
            fputcsv($file, ['Active Agencies']);
            fputcsv($file, ['Agency Name', 'Total Records', 'Status']);
            foreach ($data['agencies'] as $agency) {
                if ($agency->hub_records_count > 0) {
                    fputcsv($file, [
                        $agency->agency_name,
                        $agency->hub_records_count,
                        'Active'
                    ]);
                }
            }
            fputcsv($file, []);

            // Recent Activity
            fputcsv($file, ['Recent Activity']);
            fputcsv($file, ['Record Name', 'Agency', 'Status', 'Created Date', 'Created By']);
            foreach ($data['recent_activity'] as $activity) {
                fputcsv($file, [
                    $activity['name'],
                    $activity['agency'],
                    ucfirst($activity['status']),
                    $activity['created_date'],
                    $activity['created_by']
                ]);
            }
            fputcsv($file, []);

            // Record Growth Trend
            if (isset($data['charts']['recordGrowth'])) {
                fputcsv($file, ['Record Growth Trend']);
                fputcsv($file, ['Date', 'Records Created']);
                $growthLabels = $data['charts']['recordGrowth']['labels'];
                $growthData = $data['charts']['recordGrowth']['data'];

                for ($i = 0; $i < count($growthLabels); $i++) {
                    fputcsv($file, [
                        $growthLabels[$i],
                        $growthData[$i] ?? 0
                    ]);
                }
                fputcsv($file, []);
            }

            // Deactivation Trend
            if (isset($data['charts']['deactivationTrend'])) {
                fputcsv($file, ['Deactivation Trend']);
                fputcsv($file, ['Date', 'Records Deactivated']);
                $deactivationLabels = $data['charts']['deactivationTrend']['labels'];
                $deactivationData = $data['charts']['deactivationTrend']['data'];

                for ($i = 0; $i < count($deactivationLabels); $i++) {
                    fputcsv($file, [
                        $deactivationLabels[$i],
                        $deactivationData[$i] ?? 0
                    ]);
                }
                fputcsv($file, []);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function getDateRange(Request $request)
    {
        $days = $request->input('days', 30);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            return [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ];
        } else {
            return [
                Carbon::now()->subDays($days - 1)->startOfDay(),
                Carbon::now()->endOfDay()
            ];
        }
    }
}
