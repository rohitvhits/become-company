<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskHealthCriticalAlertService;
use App\Services\AgencyTaskHealthService;
use App\Agency;
use App\Master;
use Illuminate\Support\Facades\Cache;

class TaskHealthCriticalAlertController extends Controller
{
    protected TaskHealthCriticalAlertService $service;

    public function __construct(TaskHealthCriticalAlertService $service)
    {
        $this->middleware('auth');
        $this->middleware('permission:task-health-critical-alerts', ['only' => ['index','ajaxList','resolve']]);
        $this->middleware('permission:task-health-critical-alerts-export', ['only' => ['exportCsv']]);
        $this->service = $service;
    }

    public function index()
    {
        $auth = auth()->user();
        $masters = Cache::get('critical_alert_master', function () {
				return Master::getAllDataByMasterTypeFk(array(17, 26));
		}, 10 * 60);
        $thAgencyList = Cache::get('critical_alert_th_agency', function () {
            return AgencyTaskHealthService::getAllAgencyList();
        }, 10 * 60);
        $agencyList = Cache::get('critical_alert_agency', function () {
            return Agency::getAgencyList();
        }, 10 * 60);
        return view('task_health_critical_alert.task_health_critical_alert_list', [
            'menu'              => '',
            'user'              => $auth,
            'agencyList'        => $agencyList,
            'localAgencies'     => $thAgencyList,
            'patientServices'   => collect($masters)->filter(function ($item) {
                                    return $item->master_type_fk == 11
                                        && $item->types == 'Patient'
                                        && $item->is_disable == 1;
                                })->values(),
            'disciplineOptions' => collect($masters)->filter(function ($item) {
                                    return $item->master_type_fk == 26;
                                })->values(),
        ]);
    }

    public function ajaxList(Request $request)
    {
        $filters = $request->only([
            'fromDate', 'toDate', 'taskId', 'patientId',
            'alertStatus', 'resolvedStatus', 'agencyId',
        ]);

        $result = $this->service->getList($filters);

        return view('task_health_critical_alert.task_health_critical_alert_ajax_list', $result);
    }

    public function resolve(Request $request, $id)
    {
        $this->service->resolve((int) $id, $request->input('notes', ''));

        return response()->json(['success' => true]);
    }

    public function exportCsv(Request $request)
    {
        $filters = $request->only([
            'fromDate', 'toDate', 'taskId', 'patientId',
            'alertStatus', 'resolvedStatus', 'agencyId',
        ]);

        $rows = $this->service->exportList($filters);

        $filename = 'critical_alerts_' . date('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            if (!empty($rows)) {
                fputcsv($out, array_keys($rows[0]));
                foreach ($rows as $row) {
                    fputcsv($out, array_values($row));
                }
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
