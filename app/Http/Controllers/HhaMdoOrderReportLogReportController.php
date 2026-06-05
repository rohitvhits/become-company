<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\HhaMdoOrderReportLogService;
use App\Agency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Services\LogsService;

class HhaMdoOrderReportLogReportController extends BaseController
{
    protected $hhaMdoOrderReportLogService;

    public function __construct(
        HhaMdoOrderReportLogService $hhaMdoOrderReportLogService
    ){
        $this->middleware('permission:hha-mdo-report-log|hha-mdo-report-export|hha-mdo-report-view', ['only' => ['index', 'ajaxList', 'exportCsv']]);
        $this->middleware('permission:hha-mdo-report-log', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:hha-mdo-report-export', ['only' => ['exportCsv']]);
        $this->middleware('permission:hha-mdo-report-download', ['only' => ['saveRNPad']]);

        $this->middleware('auth');

        $this->hhaMdoOrderReportLogService = $hhaMdoOrderReportLogService;
    }

    public function index(Request $request)
    {
        $agency_list = Cache::get('hha_mdo_agency_list', function () {
            return Agency::getAgencyList();
        }, 10 * 60);

        return view('hhamdo_report.hha_mdo_report_list', compact('agency_list'));
    }

    public function ajaxList(Request $request)
    {
        $query = $this->hhaMdoOrderReportLogService->getAllData($request->all());
        return view('hhamdo_report.hha_mdo_report_document_ajax_list', compact('query'));
    }

    /**
     * Export RN Pad Documents to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = $this->hhaMdoOrderReportLogService->getAllData($request->all());
        $filename = 'hha_mdo_report_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($query) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'No',
                'Agency Name',
                'Patient Name',
                'HHA Document ID',
                'Created Date',
                'Created By',
            ]);

            // Add data rows
            $i = 1;
            foreach ($query as $row) {
                fputcsv($file, [
                    $i++,
                    ($row->agency_name ? $row->agency_name : 'N/A'),
                    ($row->full_name ? $row->full_name : 'N/A'),
                    $row->hha_document_id ?? 'N/A',
                    ($row->created_date ? date('m/d/Y h:i A', strtotime($row->created_date)) : 'N/A'),
                    ($row->uFirstName ? $row->uFirstName . ' ' . $row->uLastName : 'N/A'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function download($id, Request $request)
    {
        $query = $this->hhaMdoOrderReportLogService->getDetailsById($id);
        if (isset($query->id)) {
            $file = public_path('/') . "/hhaMDO/" . $query->patient_id . '/' . $query->hha_patient_id;

            $ipaddress = Utility::getIP();
            if (isset(auth()->user()->id)) {
                $message = auth()->user()->first_name . ' ' . auth()->user()->last_name . " has downloaded the hha mdo document";
            }

            $insertLog = [
                'type' => 'Download',
                'link' => url('/hha/hha-mdo/mdo-report-log/download/' . $id),
                'module' => 'Patient Appointment',
                'object_id' => $query->patient_id,
                'message' => $message ?? '',
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            if (file_exists($file)) {
                $headers = [];
                return response()->download($file, $query->attachment, $headers);
            } else {
                return Storage::disk('s3')->download("/hhaMDO/" . $query->patient_id . '/' . $query->hha_patient_id . $query->attachment);
            }
        }
    }

    public function viewDocumentLog(Request $request){
        $query = $this->hhaMdoOrderReportLogService->getDetailsById($request->id);
        if(isset($query->id)){
            $query->send_response = unserialize($query->send_response);
            $query->return_response = unserialize($query->return_response);
        }

        return response()->json(['data'=>$query]);
    }
}
