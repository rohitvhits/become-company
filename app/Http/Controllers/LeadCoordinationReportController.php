<?php

namespace App\Http\Controllers;

use App\Services\LeadApiService;
use Illuminate\Http\Request;

class LeadCoordinationReportController extends Controller
{
    protected $leadApiService;

    public function __construct(LeadApiService $leadApiService)
    {
        $this->middleware('permission:lead-coordination-report|export-lead-coordination', ['only' => ['index', 'ajaxList', 'exportCSV']]);
        $this->middleware('permission:lead-coordination-report', ['only' => ['index','ajaxList']]);
        $this->middleware('permission:export-lead-coordination', ['only' => ['exportCSV']]);
        $this->middleware('auth');
        $this->leadApiService = $leadApiService;
    }

    /**
     * Display the lead coordination report page
     */
    public function index()
    {
        $data['menu'] = 'reports';
        $data['user'] = auth()->user();

        return view('lead_coordination_report.index', $data);
    }

    /**
     * AJAX listing for lead coordination data
     */
    public function ajaxList(Request $request)
    {
        $filters = $request->only([
            'full_name',
            'phone',
            'agency_name',
            'service_requested',
            'appointment_date_from',
            'appointment_date_to',
            'created_date_from',
            'created_date_to'
        ]);

        $leads = $this->leadApiService->getList($filters);
        $page = $request->page ?? 1;

        return view('lead_coordination_report.ajax_list', compact('leads', 'page'));
    }

    /**
     * Export lead coordination data to CSV
     */
    public function exportCSV(Request $request)
    {
        $filters = $request->only([
            'full_name',
            'phone',
            'agency_name',
            'service_requested',
            'appointment_date_from',
            'appointment_date_to',
            'created_date_from',
            'created_date_to'
        ]);

        $leads = $this->leadApiService->getList($filters,'export');

        $filename = 'lead_coordination_report_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Agency Name',
                'Service Requested',
                'Appointment Date',
                'Appointment Time',
                'Appointment Address',
                'Referral Type',
                'Created Date'
            ]);

            // Add data rows
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->first_name,
                    $lead->last_name,
                    $lead->email,
                    $lead->phone,
                    $lead->agency_name,
                    $lead->service_requested,
                    $lead->appointment_date,
                    $lead->appointment_time,
                    $lead->appointment_address,
                    $lead->name,
                    $lead->created_date
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
