@if(empty($alerts))
    <div class="text-center text-muted py-5">
        <i class="mdi mdi-check-circle-outline" style="font-size:48px;color:#dee2e6;"></i>
        <p class="mt-2">No critical alerts found for this patient.</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-sm" style="font-size:13px;">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>#</th>
                    <th>Task ID</th>
                    <th>Alert Status</th>
                    <th style="max-width:250px;">Summary</th>
                    <th>Findings</th>
                    <th>Received At</th>
                    <th>Resolved</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alerts as $i => $row)
                    @php
                        $ca          = @unserialize($row->critical_alerts);
                        if ($ca === false) { $ca = json_decode($row->critical_alerts, true); }
                        $alertVal    = is_array($ca) ? ($ca['alert'] ?? null) : null;
                        $isCritical  = $alertVal === true;
                        $isClear     = $alertVal === false;
                        $findings    = is_array($ca) ? ($ca['findings'] ?? []) : [];
                        $summary     = is_array($ca) ? ($ca['summary'] ?? '') : '';
                        $receivedAt  = $row->created_at ? $row->created_at->format('m/d/Y h:i A') : '—';
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if($row->task_id)
                                <strong style="color:#007bff;">#{{ $row->task_id }}</strong>
                            @else —
                            @endif
                        </td>
                        <td>
                            @if($isCritical)
                                @if($row->resolved_flag)
                                    <span class="label label-danger" style="opacity:.6;">&#9888; Critical</span>
                                @else
                                    <span class="label label-danger">&#9888; Critical</span>
                                @endif
                            @elseif($isClear)
                                <span class="label label-success">&#10003; Clear</span>
                            @else
                                <span class="label label-default">Pending</span>
                            @endif
                        </td>
                        <td style="max-width:250px;">
                            @if($summary)
                                <span style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:250px;"
                                      title="{{ $summary }}">{{ $summary }}</span>
                            @else —
                            @endif
                        </td>
                        <td class="text-center">
                            @if(count($findings) > 0)
                                @php
                                    $findingItems = implode('', array_map(fn($f) => '<li>' . e($f) . '</li>', $findings));
                                    $popContent   = "<ul style='margin:0;padding-left:16px;'>{$findingItems}</ul>";
                                @endphp
                                <span class="label {{ $isCritical && !$row->resolved_flag ? 'label-danger' : 'label-success' }}"
                                      style="cursor:pointer;"
                                      tabindex="0"
                                      data-toggle="popover"
                                      data-trigger="focus"
                                      data-placement="left"
                                      data-html="true"
                                      title="&lt;strong&gt;Findings&lt;/strong&gt;"
                                      data-content="{{ $popContent }}">
                                    {{ count($findings) }} finding{{ count($findings) > 1 ? 's' : '' }}
                                </span>
                            @else —
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ $receivedAt }}</td>
                        <td>
                            @if($row->resolved_flag)
                                <span style="color:#28a745;font-size:12px;font-weight:600;">
                                    <i class="mdi mdi-check-circle"></i>
                                    {{ $row->resolved_by ?? 'Yes' }}
                                    @if($row->resolved_at)
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($row->resolved_at)->format('m/d/Y') }}</small>
                                    @endif
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">
                            @if(!$row->resolved_flag && $isCritical)
                                <button type="button"
                                        class="btn btn-sm btn-success"
                                        onclick="openPatientCaResolve({{ $row->id }})"
                                        title="Mark as Resolved"
                                        style="font-size:11px;padding:2px 8px;">
                                    <i class="mdi mdi-check"></i> Resolve
                                </button>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<script>
    $('[data-toggle="popover"]').popover();
</script>
