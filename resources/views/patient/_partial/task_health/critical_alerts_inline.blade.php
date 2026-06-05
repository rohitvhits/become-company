@php
    $unresolvedCritical = $alerts->filter(function($a) {
        $ca = @unserialize($a->critical_alerts);
        if ($ca === false) { $ca = json_decode($a->critical_alerts, true); }
        return is_array($ca) && ($ca['alert'] ?? null) === true && !$a->resolved_flag;
    })->values();
@endphp
@if($unresolvedCritical->count() > 0)
<div class="card border-0" style="border-left:4px solid #dc3545 !important;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.08);">
    <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0 font-weight-bold" style="color:#dc3545;font-size:13px;">
                <i class="mdi mdi-alert-circle mr-1"></i>
                Critical Alerts
                <span class="badge badge-danger ml-1" style="font-size:11px;">{{ $unresolvedCritical->count() }} Unresolved</span>
            </h6>
            <button class="btn btn-sm btn-outline-secondary" onclick="loadPatientCriticalAlertsInline()" style="font-size:11px;padding:2px 8px;">
                <i class="mdi mdi-reload"></i>
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size:12px;">
                <thead style="background:#fff5f5;">
                    <tr>
                        <th style="border-top:none;">#</th>
                        <th style="border-top:none;">Task ID</th>
                        <th style="border-top:none;">Summary</th>
                        <th style="border-top:none;">Findings</th>
                        <th style="border-top:none;">Received At</th>
                        <th style="border-top:none;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unresolvedCritical->values() as $i => $row)
                        @php
                            $ca       = @unserialize($row->critical_alerts);
                            if ($ca === false) { $ca = json_decode($row->critical_alerts, true); }
                            $findings = is_array($ca) ? ($ca['findings'] ?? []) : [];
                            $summary  = is_array($ca) ? ($ca['summary'] ?? '') : '';
                            $receivedAt = $row->created_at ? $row->created_at->format('m/d/Y h:i A') : '—';
                            $findingItems = implode('', array_map(fn($f) => '<li>' . e($f) . '</li>', $findings));
                            $popContent   = "<ul style='margin:0;padding-left:16px;'>{$findingItems}</ul>";
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                @if($row->task_id)
                                    <strong style="color:#007bff;">#{{ $row->task_id }}</strong>
                                @else —
                                @endif
                            </td>
                            <td style="max-width:260px;">
                                @if($summary)
                                    <span style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;"
                                          title="{{ $summary }}">{{ $summary }}</span>
                                @else —
                                @endif
                            </td>
                            <td class="text-center">
                                @if(count($findings) > 0)
                                    <span class="label label-danger"
                                          style="cursor:pointer;"
                                          tabindex="0"
                                          data-toggle="popover"
                                          data-trigger="focus"
                                          data-placement="left"
                                          data-html="true"
                                          title="&lt;strong&gt;Findings&lt;/strong&gt;"
                                          data-content="{{ $popContent }}">
                                        {{ count($findings) }}
                                    </span>
                                @else —
                                @endif
                            </td>
                            <td style="white-space:nowrap;color:#6c757d;">{{ $receivedAt }}</td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-success"
                                        onclick="openPatientCaResolve({{ $row->id }})"
                                        style="font-size:11px;padding:2px 8px;">
                                    <i class="mdi mdi-check"></i> Resolve
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<script>
    $('[data-toggle="popover"]').popover();
</script>
