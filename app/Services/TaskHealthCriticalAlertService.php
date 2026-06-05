<?php

namespace App\Services;

use App\Model\TaskHealthCriticalAlert;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\TaskHealthApiHelper;
use App\Services\TaskHealthFlagsService;
use App\Helpers\Utility;

class TaskHealthCriticalAlertService
{
    protected TaskHealthFlagsService $flagsService;

    public function __construct()
    {
        $this->flagsService = new TaskHealthFlagsService();
    }
    /**
     * Build a filtered, paginated query and return formatted items + paginator.
     */
    public function getList(array $filters): array
    {
        $query = TaskHealthCriticalAlert::query()
            ->select([
                'task_health_critical_alerts.id',
                'task_health_critical_alerts.task_id',
                'task_health_critical_alerts.patient_id',
                'task_health_critical_alerts.critical_alerts',
                'task_health_critical_alerts.created_at',
                'task_health_critical_alerts.resolved_flag',
                'task_health_critical_alerts.resolved_notes',
                'task_health_critical_alerts.resolved_by',
                'task_health_critical_alerts.resolved_at',
                'agency.agency_name',
            ])
            ->leftJoin('task_health_master', 'task_health_master.task_id', '=', 'task_health_critical_alerts.task_id')
            ->leftJoin('agency', 'agency.id', '=', 'task_health_master.agency_id')
            ->where(function ($q) {
                $q->whereNull('task_health_critical_alerts.deleted_flag')
                  ->orWhere('task_health_critical_alerts.deleted_flag', '!=', 'Y');
            })
            ->groupBy('task_health_critical_alerts.id');

        // Date range — direct comparison is index-friendly vs whereDate()
        if (!empty($filters['fromDate'])) {
            $from = date('Y-m-d', strtotime($filters['fromDate']));
            if ($from) $query->where('task_health_critical_alerts.created_at', '>=', $from . ' 00:00:00');
        }
        if (!empty($filters['toDate'])) {
            $to = date('Y-m-d', strtotime($filters['toDate']));
            if ($to) $query->where('task_health_critical_alerts.created_at', '<=', $to . ' 23:59:59');
        }

        // task_id — indexed column, exact match
        if (!empty($filters['taskId'])) {
            $query->where('task_health_critical_alerts.task_id', $filters['taskId']);
        }

        // patient_id — indexed column, no LIKE needed
        if (!empty($filters['patientId'])) {
            $query->where('task_health_critical_alerts.patient_id', $filters['patientId']);
        }

        // alertStatus — push to SQL via LIKE so pagination counts are correct
        if (!empty($filters['alertStatus'])) {
            if ($filters['alertStatus'] === 'active') {
                $query->where(function ($q) {
                    $q->where('task_health_critical_alerts.critical_alerts', 'LIKE', '%"alert":true%')
                      ->orWhere('task_health_critical_alerts.critical_alerts', 'LIKE', '%s:5:"alert";b:1;%');
                });
            } elseif ($filters['alertStatus'] === 'clear') {
                $query->where(function ($q) {
                    $q->where('task_health_critical_alerts.critical_alerts', 'LIKE', '%"alert":false%')
                      ->orWhere('task_health_critical_alerts.critical_alerts', 'LIKE', '%s:5:"alert";b:0;%');
                });
            }
        }

        // resolved_flag
        if (!empty($filters['resolvedStatus'])) {
            if ($filters['resolvedStatus'] === 'resolved') {
                $query->where('task_health_critical_alerts.resolved_flag', 1);
            } elseif ($filters['resolvedStatus'] === 'unresolved') {
                $query->where(function ($q) {
                    $q->whereNull('task_health_critical_alerts.resolved_flag')
                      ->orWhere('task_health_critical_alerts.resolved_flag', '!=', 1);
                });
            }
        }

        // agency filter
        if (!empty($filters['agencyId'])) {
            $query->where('task_health_master.agency_id', (int) $filters['agencyId']);
        }

        // Clone before ordering/paginating so count queries reuse the same filters
        $baseQuery = clone $query;

        $records = $query->orderByDesc('task_health_critical_alerts.created_at')->paginate(50);

        // Count queries must not carry groupBy — Laravel's count() on a grouped query returns
        // only the first group's aggregate (= 1). Reset groups on the underlying Query Builder
        // (Eloquent Builder wraps it; $countBase->groups would hit the wrong object).
        // Use COUNT(DISTINCT id) so any residual join duplicates are also collapsed.
        $countBase = clone $baseQuery;
        $countBase->getQuery()->groups = null;

        $criticalCount = (clone $countBase)->where(function ($q) {
            $q->where('task_health_critical_alerts.critical_alerts', 'LIKE', '%"alert":true%')
              ->orWhere('task_health_critical_alerts.critical_alerts', 'LIKE', '%s:5:"alert";b:1;%');
        })->count(DB::raw('DISTINCT task_health_critical_alerts.id'));

        $resolvedCount = (clone $countBase)->where('task_health_critical_alerts.resolved_flag', 1)
            ->count(DB::raw('DISTINCT task_health_critical_alerts.id'));

        // Fetch only the columns needed for full_name
        $resolverIds = $records->pluck('resolved_by')->filter()->unique()->values()->toArray();
        $resolvers   = User::whereIn('id', $resolverIds)
            ->select(['id', 'first_name', 'last_name'])
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($records as $record) {
            $ca = $this->decodeField($record->critical_alerts);

            $alert    = is_array($ca) ? ($ca['alert']   ?? null) : null;
            $summary  = is_array($ca) ? ($ca['summary'] ?? '')   : '';
            $findings = is_array($ca) ? array_values(array_filter((array)($ca['findings'] ?? []))) : [];

            $resolvedUser = ($record->resolved_by && isset($resolvers[$record->resolved_by]))
                ? $resolvers[$record->resolved_by]->full_name
                : null;

            // resolved_at is stored as Unix timestamp (int column)
            $resolvedAt = $resolvedAt = isset($record->resolved_at) && !empty($record->resolved_at) ? date('m/d/Y h:i A',strtotime($record->resolved_at)) : '';

            $items[] = [
                'id'             => $record->id,
                'task_id'        => $record->task_id,
                'patient_id'     => $record->patient_id,
                'agency_name'    => $record->agency_name,
                'alert'          => $alert,
                'summary'        => $summary,
                'findings'       => $findings,
                'created_at'     => $record->created_at,
                'resolved_flag'  => (bool) $record->resolved_flag,
                'resolved_notes' => $record->resolved_notes,
                'resolved_by'    => $resolvedUser,
                'resolved_at'    => $resolvedAt,
            ];
        }

        return [
            'items'      => $items,
            'pagination' => $records,
            'stats'      => [
                'total'    => $records->total(),
                'critical' => $criticalCount,
                'resolved' => $resolvedCount,
            ],
        ];
    }

    /**
     * Return all matching records (no pagination) for CSV export.
     */
    public function exportList(array $filters): array
    {
        $query = TaskHealthCriticalAlert::query()
            ->select([
                'task_health_critical_alerts.id',
                'task_health_critical_alerts.task_id',
                'task_health_critical_alerts.patient_id',
                'task_health_critical_alerts.critical_alerts',
                'task_health_critical_alerts.created_at',
                'task_health_critical_alerts.resolved_flag',
                'task_health_critical_alerts.resolved_notes',
                'task_health_critical_alerts.resolved_by',
                'task_health_critical_alerts.resolved_at',
                'agency.agency_name',
            ])
            ->leftJoin('task_health_master', 'task_health_master.task_id', '=', 'task_health_critical_alerts.task_id')
            ->leftJoin('agency', 'agency.id', '=', 'task_health_master.agency_id')
            ->where(function ($q) {
                $q->whereNull('task_health_critical_alerts.deleted_flag')
                  ->orWhere('task_health_critical_alerts.deleted_flag', '!=', 'Y');
            })
            ->groupBy('task_health_critical_alerts.id');

        if (!empty($filters['fromDate'])) {
            $from = date('Y-m-d', strtotime($filters['fromDate']));
            if ($from) $query->where('task_health_critical_alerts.created_at', '>=', $from . ' 00:00:00');
        }
        if (!empty($filters['toDate'])) {
            $to = date('Y-m-d', strtotime($filters['toDate']));
            if ($to) $query->where('task_health_critical_alerts.created_at', '<=', $to . ' 23:59:59');
        }
        if (!empty($filters['taskId'])) {
            $query->where('task_health_critical_alerts.task_id', $filters['taskId']);
        }
        if (!empty($filters['patientId'])) {
            $query->where('task_health_critical_alerts.patient_id', $filters['patientId']);
        }
        if (!empty($filters['alertStatus'])) {
            if ($filters['alertStatus'] === 'active') {
                $query->where(function ($q) {
                    $q->where('task_health_critical_alerts.critical_alerts', 'LIKE', '%"alert":true%')
                      ->orWhere('task_health_critical_alerts.critical_alerts', 'LIKE', '%s:5:"alert";b:1;%');
                });
            } elseif ($filters['alertStatus'] === 'clear') {
                $query->where(function ($q) {
                    $q->where('task_health_critical_alerts.critical_alerts', 'LIKE', '%"alert":false%')
                      ->orWhere('task_health_critical_alerts.critical_alerts', 'LIKE', '%s:5:"alert";b:0;%');
                });
            }
        }
        if (!empty($filters['resolvedStatus'])) {
            if ($filters['resolvedStatus'] === 'resolved') {
                $query->where('task_health_critical_alerts.resolved_flag', 1);
            } elseif ($filters['resolvedStatus'] === 'unresolved') {
                $query->where(function ($q) {
                    $q->whereNull('task_health_critical_alerts.resolved_flag')
                      ->orWhere('task_health_critical_alerts.resolved_flag', '!=', 1);
                });
            }
        }
        if (!empty($filters['agencyId'])) {
            $query->where('task_health_master.agency_id', (int) $filters['agencyId']);
        }

        $records = $query->orderByDesc('task_health_critical_alerts.created_at')->get();

        $resolverIds = $records->pluck('resolved_by')->filter()->unique()->values()->toArray();
        $resolvers   = User::whereIn('id', $resolverIds)
            ->select(['id', 'first_name', 'last_name'])
            ->get()
            ->keyBy('id');

        $rows = [];
        foreach ($records as $record) {
            $ca       = $this->decodeField($record->critical_alerts);
            $alert    = is_array($ca) ? ($ca['alert'] ?? null) : null;
            $summary  = is_array($ca) ? ($ca['summary'] ?? '') : '';
            $findings = is_array($ca) ? array_values(array_filter((array)($ca['findings'] ?? []))) : [];

            $alertLabel = $alert === true ? 'Critical' : ($alert === false ? 'Clear' : 'Pending');

            $resolvedUser = ($record->resolved_by && isset($resolvers[$record->resolved_by]))
                ? $resolvers[$record->resolved_by]->full_name
                : '';

            $resolvedAt = isset($record->resolved_at) && !empty($record->resolved_at) ? date('m/d/Y h:i A',strtotime($record->resolved_at)) : '';

            $rows[] = [
                'Task ID'        => $record->task_id,
                'Patient ID'     => $record->patient_id,
                'Agency Name'    => $record->agency_name ?? '',
                'Alert Status'   => $alertLabel,
                'Summary'        => $summary,
                'Findings'       => implode(' | ', $findings),
                'Received At'    => $record->created_at ? Carbon::parse($record->created_at)->format('m/d/Y h:i A') : '',
                'Resolved'       => $record->resolved_flag ? 'Yes' : 'No',
                'Resolved By'    => $resolvedUser,
                'Resolved At'    => $resolvedAt,
                'Resolved Notes' => $record->resolved_notes ?? '',
            ];
        }

        return $rows;
    }

    /**
     * Mark a record as resolved.
     */
    public function resolve(int $id, string $notes): TaskHealthCriticalAlert
    {
        $user = auth()->user();
        $record = TaskHealthCriticalAlert::where(function ($q) {
                $q->whereNull('deleted_flag')->orWhere('deleted_flag', '!=', 'Y');
            })
            ->findOrFail($id);

        $record->resolved_flag  = 1;
        $record->resolved_notes = $notes;
        $record->resolved_by    = $user->id;
        $record->resolved_at    = date('Y-m-d H:i:s');
        $record->save();

        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Critical Alert Resolved',
            'link' => url('task-health/critical-alerts/{$record->id}/resolve'),
            'module' => 'Critical Alert',
            'object_id' => $record->id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has resolved Critical alert',
            'new_response' => serialize([
                'alert_id'  => $record->id,
                'task_id'   => $record->task_id,
                'patient_id'=> $record->patient_id,
                'notes'     => $notes,
            ]),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        // Auto-raise the alert flag on the linked task health flags record
        if (!empty($record->task_id)) {
            $this->flagsService->setAlertByTaskId((string) $record->task_id);
        }

        return $record;
    }

    /**
     * Decode a field that may be PHP-serialized or JSON-encoded.
     */
    private function decodeField($value)
    {
        if (empty($value)) return null;

        $result = @unserialize($value);
        if ($result !== false) return $result;

        $result = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) return $result;

        return null;
    }

    public function getCriticalALerts($taskId){
        return TaskHealthCriticalAlert::where('task_id', $taskId)->first();
    }

    /**
     * Persist a webhook-received critical alert payload.
     */
    public function createFromWebhook(string $taskId, string $patientId, array $data): TaskHealthCriticalAlert
    {
        $alerts = self::getCriticalALerts($taskId);
        if(isset($alerts) && !empty($alerts)){
            return $alerts;
        }
        return TaskHealthCriticalAlert::create([
            'task_id'         => $taskId,
            'patient_id'      => $patientId,
            'critical_alerts' => serialize([
                'alert'    => $data['alert']    ?? null,
                'findings' => $data['findings'] ?? [],
                'summary'  => $data['summary']  ?? '',
            ]),
            'payload'         => serialize($data),
        ]);
    }

    /**
     * Fetch visits with critical alerts from the TH API and upsert into local table.
     * Returns ['created', 'updated', 'skipped'] counts or throws on API failure.
     */
    public function syncFromApi(array $params): array
    {
        $baseParams = array_filter([
            'hasCriticalAlert' => 'true',
            'sortBy'           => in_array($params['sortBy'] ?? '', ['scheduledDateTime', 'createdAt'])
                                      ? $params['sortBy']
                                      : 'scheduledDateTime',
            'fromDate'         => !empty($params['fromDate']) ? date('Y-m-d', strtotime($params['fromDate'])) : null,
            'toDate'           => !empty($params['toDate'])   ? date('Y-m-d', strtotime($params['toDate']))   : null,
            'limit'            => 50,
        ], fn($v) => $v !== null && $v !== '' && $v !== []);

        $page       = 1;
        $totalPages = 1;
        $created    = 0;
        $updated    = 0;
        $skipped    = 0;

        do {
            $result = TaskHealthApiHelper::getVisits($baseParams + ['page' => $page]);

            if (!$result['status']) {
                throw new \RuntimeException($result['error'] ?? 'Failed to fetch visits from Task Health API.');
            }

            $items      = $result['data']['items']                     ?? [];
            $totalPages = $result['data']['pagination']['totalPages']  ?? 1;

            foreach ($items as $item) {
                $ca = $item['criticalAlert'] ?? null;

                if (is_null($ca)) { $skipped++; continue; }

                $taskId    = (string) ($item['taskId']    ?? '');
                $patientId = (string) ($item['patientId'] ?? '');

                if (empty($taskId)) { $skipped++; continue; }

                $alertData = serialize([
                    'alert'    => $ca['alert']    ?? null,
                    'findings' => $ca['findings'] ?? [],
                    'summary'  => $ca['summary']  ?? '',
                ]);

                $existing = $this->getCriticalALerts($taskId);

                if ($existing) {
                    $existing->patient_id      = $patientId;
                    $existing->critical_alerts = $alertData;
                    $existing->payload         = serialize($item);
                    $existing->save();
                    $updated++;
                } else {
                    TaskHealthCriticalAlert::create([
                        'task_id'         => $taskId,
                        'patient_id'      => $patientId,
                        'critical_alerts' => $alertData,
                        'payload'         => serialize($item),
                    ]);
                    $created++;
                }
            }

            $page++;
        } while ($page <= $totalPages);

        return compact('created', 'updated', 'skipped');
    }

    /**
     * Return all non-deleted critical alerts for a given TH patient ID.
     */
    public function getByThPatientId(string $thPatientId): \Illuminate\Support\Collection
    {
        return TaskHealthCriticalAlert::where('patient_id', $thPatientId)
            ->where(fn($q) => $q->whereNull('deleted_flag')->orWhere('deleted_flag', '!=', 'Y'))
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
