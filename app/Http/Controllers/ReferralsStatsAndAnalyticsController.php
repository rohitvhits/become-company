<?php

namespace App\Http\Controllers;

use App\Services\PatientService;
use App\Agency;
use App\Master;
use App\Model\BranchList;
use Illuminate\Http\Request;
use App\Services\LocationMasterService;
use App\Services\PatientWiseServicesRequests;
use App\Services\AppointmentDashboardService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\User;

class ReferralsStatsAndAnalyticsController extends Controller
{
    protected $patientService, $locationMasterService, $patientWiseService, $appointmentDashboardService = "";
    public function __construct(AppointmentDashboardService $appointmentDashboardService, PatientService $patientService, LocationMasterService $locationMasterService, PatientWiseServicesRequests $patientWiseService)
    {
        $this->middleware('permission:detailed-refusals-report', ['only' => ['detailedRefusals']]);
        $this->middleware('permission:referrals-weight-report', ['only' => ['referralsWeight']]);
        $this->middleware('permission:referrals-analytics-dashboard-report', ['only' => ['referralsAnalyticsDashboard']]);
        $this->middleware('permission:weekly-monthly-states-report', ['only' => ['weeklyMonthlyStates']]);
        $this->middleware('auth');
        $this->patientService = $patientService;
        $this->appointmentDashboardService = $appointmentDashboardService;
        $this->locationMasterService = $locationMasterService;
        $this->patientWiseService = $patientWiseService;
    }

    public function referralsWeight()
    {
        $user = Auth()->user();
        $data['agency_list'] = Agency::getAgencyList();
        $data['serviceList'] = Master::getServiceRequest();
        $data['userList'] = Cache::get('patient_master_nubest_user', function () {
            return User::getNYBestUserData();
        }, 10 * 60);
        $data['yearData'] = Cache::remember('dynamic_year', 10, function () {
            return $this->appointmentDashboardService->getDynamicYears();
        });
        $data['dateRange'] = date('m/d/Y') . '-' . date('m/d/Y');

        return view('referralsWeight.index', $data);
    }

    public function serviceCountAjax(Request $request)
    {

        $data['created_date'] = $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $data['type'] = $type = $request->type;
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;
        $data['weeklyMonthlyServices'] = $weeklyMonthlyServices = $this->patientWiseService->getServicesCount($created_date, $type, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);

        // Group by service name
        $grouped = [];
        $totalCount = 0;

        foreach ($weeklyMonthlyServices as $row) {
            $service = $row->service_name;
            $count = $row->count;

            $grouped[$service] = ($grouped[$service] ?? 0) + $count;
            $totalCount += $count;
        }

        // Build percentage view
        $finalData = [];
        foreach ($grouped as $service => $count) {
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            $finalData[] = [
                'service' => $service,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        return response()->json($finalData);
    }

    public function agencyCountAjax(Request $request)
    {

        $data['created_date'] = $created_date = $request->created_date;
        $data['type'] = $type = $request->type;
        $lastUpdatedDate = $request->last_updated_date;
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;

        $finalData = [];
        $agency_from_date = $agency_to_date = '';
        if ($request->created_date != '') {
            $case_date = explode('-', $request->created_date);
            if (count($case_date) > 0) {
                $agency_from_date = date('Y-m-d', strtotime(trim($case_date[0]))) ?? '';
                $agency_to_date = date('Y-m-d', strtotime(trim($case_date[1]))) ?? '';
            }
        }
        $appointmentData = $this->appointmentDashboardService->getAgencyWiseAppointmentData($agency_from_date, $agency_to_date, $type, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);
        if (!empty($appointmentData)) {
            $agency_ids = Agency::getAgencyListWithIds(array_column($appointmentData, 'agency_id'));
            foreach ($agency_ids as $ag) {
                $agency_data[$ag['id']] = $ag['agency_name'];
            }
            foreach ($appointmentData as $row) {
                if (isset($row['agency_id']) && array_key_exists($row['agency_id'], $agency_data)) {
                    $finalData[]  = array(
                        'agency_id' => $row['agency_id'],
                        'name' => $agency_data[$row['agency_id']],
                        'count' => $row['count']
                    );
                }
            }
        }

        return response()->json($finalData);
    }
    public function detailedRefusals()
    {
        $data['dateRange'] = date('m/d/Y') . '-' . date('m/d/Y');
        $data['agency_list'] = Agency::getAgencyList();
        $data['location_list'] = $this->locationMasterService->AllListWithoutPaginate();
        $type = [29, 32];
        $masterData = Cache::get('masters_data', function () use ($type) {
            return Master::getAllDataByMasterTypeFk($type);
        }, 10, 60);

        $data['refusedStatus'] = $masterData;

        return view('detailedRefusals.index', $data);
    }

    public function graphAjax(Request $request)
    {

        if (empty($request->created_date)) {
            $createdDate = date('m-d-Y') . ' - ' . date('m-d-Y');
        } else {
            $createdDate = $request->created_date;
        }
        $lastUpdatedDate = $request->last_updated_date;
        $data = $this->patientService->patientdetailedRefusalsAjaxCount($request->agency_id, $request->record_type, $request->location_id, $createdDate, $lastUpdatedDate);

        return response()->json($data);
    }

    public function dashboardGraphAgency(Request $request)
    {

        $allCounts = $this->patientService->patientDashboardGraphStatusCount($request->agency_id, $request->record_type, $request->location_id);
        $final = [];
        $final['total'] = array_sum($allCounts);
        foreach ($allCounts as $key => $val) {
            $key = str_replace(' ', '', $key);
            if ($key == 'hospitalized/rehab') {
                $key = 'hospitalized';
            }
            $final[$key] = $val;
        }
        return response()->json(['success' => "data", 'status' => 1, 'data' => $final], 200);
    }

    public function referralsAnalyticsDashboard()
    {
        $data['user'] = Auth()->user();
        $data['agencytWiseService'] = $agencytWiseService = $this->patientWiseService->getAgencytWiseServiceRequest();
        $agencyIds = [];
        $serviceIds = [];
        foreach ($agencytWiseService as  $value) {
            $agencyIds[] = $value->agency_id;
            $serviceIds[] = $value->service_id;
        }

        $data['agencies'] = Agency::agencyList();
        $data['location_list'] = $this->locationMasterService->AllListWithoutPaginate();
        $data['services'] = Master::getMasterListPluck('', $serviceIds);


        return view('referralsAnalyticsDashboard.index', $data);
    }

    public function referralsAnalyticsAjaxList(Request $request)
    {

        $data['created_date'] = $created_date = $request->created_date;
        $last_updated_date = $request->last_updated_date;
        $agencyId = $request->agency_id;
        $type = $request->type;

        $data['agencytWiseService'] = $agencytWiseService = $this->patientWiseService->getAgencytWiseServiceRequest($created_date, $agencyId, $type, $last_updated_date);

        $agencytWiseServiceData = [];
        $agencyIds = [0];
        $serviceIds = [0];
        foreach ($agencytWiseService as  $value) {
            $agencyIds[] = $value->agency_id;
            $serviceIds[] = $value->service_id;

            $agencytWiseServiceData[$value->agency_id][$value->service_id] = $value->total_count ?? '';
        }

        $data['agencies'] = $agencies = Agency::agencyList($agencyIds);
        $data['services'] = $services = Master::getMasterListPluck('', $serviceIds);

        return view('referralsAnalyticsDashboard.ajax_list', compact('agencies', 'agencytWiseServiceData', 'services', 'created_date'));
    }

    public function weeklyMonthlyStates()
    {
        $data['user'] = $user = Auth()->user();
        $data['agencytWiseService'] = $agencytWiseService = $this->patientWiseService->getAgencytWiseServiceRequest();
        $agencytWiseServiceData = [];
        $agencyIds = [];
        $serviceIds = [];
        foreach ($agencytWiseService as  $value) {
            $agencyIds[] = $value->agency_id;
            $serviceIds[] = $value->service_id;
        }

        $data['agencies'] = Agency::agencyList();
        $data['services'] = Master::getMasterListPluck('', $serviceIds);

        return view('weeklyMonthlyStates.index', $data);
    }

    public function weeklyMonthlyStatesAjaxList(Request $request)
    {



        $data['created_date'] = $created_date = $request->created_date;
        $last_updated_date = $request->last_updated_date;
        if (empty($created_date)) {
            $from = Carbon::now()->subMonths(3)->startOfDay()->format('m/d/Y');
            $to = Carbon::now()->endOfDay()->format('m/d/Y');
            $created_date = "$from - $to";
        }
        $agencyId = $request->agency_id;
        $type = $request->type;

        $data['weeklyMonthlyServices'] = $weeklyMonthlyServices = $this->patientWiseService->getWeeklyMonthlyServices($created_date, $agencyId, $type, $last_updated_date);

        $structured = [];
        $services = [];

        foreach ($weeklyMonthlyServices as $row) {
            $week = $row->week_start_date;
            $service = $row->service_name;
            $count = $row->count;

            $structured[$week][$service] = $count;
            $services[$service] = true;
        }

        uksort($structured, function ($a, $b) {
            return strtotime($b) - strtotime($a); // Descending order by date
        });
        $serviceNames = array_keys($services); // unique list of services

        $totals = [];
        foreach ($structured as $week => $serviceCounts) {
            foreach ($serviceNames as $service) {
                $totals[$service] = ($totals[$service] ?? 0) + ($serviceCounts[$service] ?? 0);
            }
        }

        return view('weeklyMonthlyStates.ajax_list', compact('serviceNames', 'structured', 'totals'));
    }
    public function bookingCountAjax(Request $request)
    {

        $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $type = $request->type;
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;
        $status = 'booked';
        $fromDate = $toDate = '';
        if ($request->created_date != '') {
            $date = explode('-', $request->created_date);
            if (count($date) > 0) {
                $fromDate = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
                $toDate = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
            }
        }
        $data['agencyStatusCount'] = $agencyStatusCount = $this->patientService->agencyStatusCount($fromDate, $toDate, $type, $status, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);

        // Group by service name
        $grouped = [];
        $totalCount = 0;

        foreach ($agencyStatusCount as $row) {
            $name = $row->agency_name;
            $count = $row->count;

            $grouped[$name] = ($grouped[$name] ?? 0) + $count;
            $totalCount += $count;
        }

        // Build percentage view
        $finalData = [];
        foreach ($grouped as $name => $count) {
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            $finalData[] = [
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return response()->json($finalData);
    }

    public function cancellationsCountAjax(Request $request)
    {

        $search['created_date'] = $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $search['type'] = $type = $request->type;
        $search['status'] = $status = 'cancelled';
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;
        $fromDate = $toDate = '';
        if ($request->created_date != '') {
            $date = explode('-', $request->created_date);
            if (count($date) > 0) {
                $fromDate = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
                $toDate = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
            }
        }
        $data['agencyStatusCount'] = $agencyStatusCount = $this->patientService->agencyStatusCount($fromDate, $toDate, $type, $status, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);

        // Group by service name
        $grouped = [];
        $totalCount = 0;

        foreach ($agencyStatusCount as $row) {
            $name = $row->agency_name;
            $count = $row->count;

            $grouped[$name] = ($grouped[$name] ?? 0) + $count;
            $totalCount += $count;
        }

        // Build percentage view
        $finalData = [];
        foreach ($grouped as $name => $count) {
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            $finalData[] = [
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        return response()->json($finalData);
    }

    public function refusalsCountAjax(Request $request)
    {

        $search['created_date'] = $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $search['type'] = $type = $request->type;
        $search['status'] = $status = 'refused';
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;
        $fromDate = $toDate = '';
        if ($request->created_date != '') {
            $date = explode('-', $request->created_date);
            if (count($date) > 0) {
                $fromDate = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
                $toDate = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
            }
        }
        $data['agencyStatusCount'] = $agencyStatusCount = $this->patientService->agencyStatusCount($fromDate, $toDate, $type, $status, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);

        // Group by service name
        $grouped = [];
        $totalCount = 0;

        foreach ($agencyStatusCount as $row) {
            $name = $row->agency_name;
            $count = $row->count;

            $grouped[$name] = ($grouped[$name] ?? 0) + $count;
            $totalCount += $count;
        }

        // Build percentage view
        $finalData = [];
        foreach ($grouped as $name => $count) {
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            $finalData[] = [
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        return response()->json($finalData);
    }

    public function unabletocontactCountAjax(Request $request)
    {

        $search['created_date'] = $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $search['type'] = $type = $request->type;
        $search['status'] = $status = 'unableToContact';
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;
        $fromDate = $toDate = '';
        if ($request->created_date != '') {
            $date = explode('-', $request->created_date);
            if (count($date) > 0) {
                $fromDate = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
                $toDate = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
            }
        }
        $data['agencyStatusCount'] = $agencyStatusCount = $this->patientService->agencyStatusCount($fromDate, $toDate, $type, $status, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);

        // Group by service name
        $grouped = [];
        $totalCount = 0;

        foreach ($agencyStatusCount as $row) {
            $name = $row->agency_name;
            $count = $row->count;

            $grouped[$name] = ($grouped[$name] ?? 0) + $count;
            $totalCount += $count;
        }

        // Build percentage view
        $finalData = [];
        foreach ($grouped as $name => $count) {
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            $finalData[] = [
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return response()->json($finalData);
    }

    public function completedCountAjax(Request $request)
    {

        $search['created_date'] = $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $search['type'] = $type = $request->type;
        $search['status'] = $status = 'completed';
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;

        $fromDate = $toDate = '';
        if ($request->created_date != '') {
            $date = explode('-', $request->created_date);
            if (count($date) > 0) {
                $fromDate = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
                $toDate = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
            }
        }
        $data['agencyStatusCount'] = $agencyStatusCount = $this->patientService->agencyStatusCount($fromDate, $toDate, $type, $status, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);

        // Group by service name
        $grouped = [];
        $totalCount = 0;

        foreach ($agencyStatusCount as $row) {
            $name = $row->agency_name;
            $count = $row->count;

            $grouped[$name] = ($grouped[$name] ?? 0) + $count;
            $totalCount += $count;
        }

        // Build percentage view
        $finalData = [];
        foreach ($grouped as $name => $count) {
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            $finalData[] = [
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        return response()->json($finalData);
    }
    public function detailedRefusalsCountAjax(Request $request)
    {

        $search['created_date'] = $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;

        $detailedRefusalsData = $this->patientService->patientdetailedRefusalsAjaxCount($request->agency_id, $request->type, $request->location_id, $created_date, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);

        return response()->json($detailedRefusalsData);
    }

    public function detailedCancellationsCountAjax(Request $request)
    {

        $search['created_date'] = $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;

        $detailedCancellationsData = $this->patientService->patientdetailedCancellationsAjaxCount($request->agency_id, $request->type, $request->location_id, $created_date, $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);

        return response()->json($detailedCancellationsData);
    }
    public function statusCount(Request $request)
    {

        $search = $request->all();
        $created_date = $request->created_date;
        $lastUpdatedDate = $request->last_updated_date;
        $type = $request->type;
        $agency_fk = $request->agency_fk;
        $agency_filter_type = $request->agency_filter_type;
        $service_id = $request->service_id;
        $service_filter_type = $request->service_filter_type;
        $assigned_to = $request->assigned_to;
        $medication_list = $request->medication_list;
        $insurance_elg = $request->insurance_elg;
        $mdo_tag = $request->mdo_tag;
        $branch_id = $request->branch_id;
        $branch_filter_type = $request->branch_filter_type;
        $fromDate = $toDate = '';
        if ($request->created_date != '') {
            $date = explode('-', $request->created_date);
            if (count($date) > 0) {
                $fromDate = date('Y-m-d', strtotime(trim($date[0]))) ?? '';
                $toDate = date('Y-m-d', strtotime(trim($date[1]))) ?? '';
            }
        }
        $statusCount = $this->patientService->statusCount($fromDate, $toDate, $type, $status = "", $lastUpdatedDate, $agency_fk, $agency_filter_type, $service_id, $service_filter_type, $assigned_to, $medication_list, $insurance_elg, $mdo_tag, $branch_id, $branch_filter_type);
        $type = $request->type;
        $statusList = $agencies = [];

        $data['pivotData'] = $pivotData = [];
        $getStatusList = $this->getStatusList();
        foreach ($statusCount as $row) {
            $agencies[$row->agency_id] = $row->agency_name;
            if (isset($getStatusList[ucfirst($row->status)])) {

                $statusList[$getStatusList[ucfirst($row->status)]] = ucfirst($row->status);
            }
            $pivotData[$row->agency_id][ucfirst($row->status)] = $row->total;
        }

        return response()->json(['pivotData' => $pivotData, 'agencies' => $agencies, 'statusList' => $statusList, 'created_date' => $created_date, 'type' => $type]);
    }

    private function getStatusList()
    {
        // Define the status list
        return [
            'Pending' => 'Pending',
            'Cancelled' => 'cancelled',
            'Booked' => 'booked',
            'Completed' => 'completed',
            'No Show' => 'noshow',
            'Arrived' => 'arrived',
            'Processing' => 'processing',
            'Not Interested' => 'Not interested',
            'Hospitalized/Rehab' => 'hospitalized/rehab',
            'Unable To Contact' => 'unableToContact',
            'Refused' => 'refused',
            'Mark as CheckIn' => 'checkin',
            'Pending Termination' => 'Pending Termination',
            'On Hold' => 'Onhold',
            'On Leave' => 'On Leave',
            'Terminated' => 'Terminated',
            'New Form Requested' => 'New Form Requested',
            'New Order Received' => 'New Order Received',
            'Form Completed' => 'Form Completed',
            'Mark As CheckIn' => 'Mark As CheckIn',
            '1st Attempt - Unable to Contact' => '1st Attempt - Unable to Contact',
            '2nd Attempt - Unable to Contact' => '2nd Attempt - Unable to Contact',
            '3rd Attempt - Unable to Contact' => '3rd Attempt - Unable to Contact',
            'Telehealth Completed' => 'Telehealth Completed',
            'Patient Deceased' => 'Patient Deceased',
            'Signed' => 'Signed',
            'Signed & Sent Back to the Agency' => 'Signed & Sent Back to the Agency',
            'Telehealth Completed , Pending Forms' => 'Telehealth Completed , Pending Forms',
            'Patient Asked to Reschedule' => 'Patient Asked to Reschedule',
            'Appointment Missed' => 'Appointment Missed',
            'Service Provided' => 'Service Provided',
            'Closed Temporarily' => 'Closed Temporarily'
        ];
    }

}
