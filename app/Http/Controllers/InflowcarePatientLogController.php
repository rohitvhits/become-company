<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\InflowcarePatientLogService;
use App\Agency;

class InflowcarePatientLogController extends BaseController
{
    protected $inflowcarePatientLogService;

    public function __construct(InflowcarePatientLogService $inflowcarePatientLogService)
    {
        $this->middleware('auth');
        $this->middleware('permission:inflowcare-patient-log-report|inflowcare-patient-log-report-export', ['only' => ['index','ajaxList','exportCsv']]);
        $this->middleware('permission:inflowcare-patient-log-report-export', ['only' => ['exportCsv']]);
        $this->inflowcarePatientLogService = $inflowcarePatientLogService;
    }

    public function index(Request $request)
    {
        $data['agencyList'] = Agency::getAgencyListWithUserAgency()->toArray();
        $data['agencyCnt'] = count($data['agencyList']);
        return view('inflowcare_patient_logs.index', $data);
    }

    public function ajaxList(Request $request)
    {
        $filters = [];
        $data['page'] = $request->page ?? 1;

        if (!empty($request->agency_id)) {
            $filters['agency_id'] = $request->agency_id;
        }

        if (!empty($request->created_by)) {
            $filters['created_by'] = $request->created_by;
        }

        if (!empty($request->from_date)) {
            $filters['from_date'] = date('Y-m-d', strtotime($request->from_date));
        }

        if (!empty($request->to_date)) {
            $filters['to_date'] = date('Y-m-d', strtotime($request->to_date));
        }

        $data['logs'] = $this->inflowcarePatientLogService->getListing($filters);

        return view('inflowcare_patient_logs.ajax_list', $data);
    }

    public function exportCsv(Request $request)
    {
        $filters = [];

        if (!empty($request->agency_id)) {
            $filters['agency_id'] = explode(',', $request->agency_id);
        }

        if (!empty($request->from_date)) {
            $filters['from_date'] = date('Y-m-d', strtotime($request->from_date));
        }

        if (!empty($request->to_date)) {
            $filters['to_date'] = date('Y-m-d', strtotime($request->to_date));
        }

        $logs = $this->inflowcarePatientLogService->getListing($filters,'export');

        $filename = 'inflowcare_patient_logs_' . date('m-d-Y') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Agency Name', 'Patient ID', 'Patient Name', 'Message', 'Status', 'Created Date', 'Created By']);

            foreach ($logs as $key => $log) {
                fputcsv($file, [
                    $key + 1,
                    $log->agency->agency_name ?? '',
                    $log->patient_id ?? '',
                    ($log->patient->first_name ?? '') . ' ' . ($log->patient->last_name ?? ''),
                    $log->message ?? '',
                    $log->status ?? '',
                    $log->created_at ? date('m/d/Y h:i A', strtotime($log->created_at)) : '',
                    ($log->userDetail->first_name ?? '') . ' ' . ($log->userDetail->last_name ?? ''),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
