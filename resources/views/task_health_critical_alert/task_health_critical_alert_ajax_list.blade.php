{{-- Overall stats for all pages --}}
<div id="ca-stats-data"
     data-total="{{ $stats['total'] }}"
     data-critical="{{ $stats['critical'] }}"
     data-resolved="{{ $stats['resolved'] }}"
     style="display:none;"></div>

@if(empty($items))
    <table class="table table-bordered table-width1">
        <tbody>
            <tr><td colspan="11" class="text-center">No record available</td></tr>
        </tbody>
    </table>
@else

<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th>No</th>
            <th>Task ID</th>
            <th>Patient ID</th>
            <th>Agency Name</th>
            <th>Alert Status</th>
            <th>Summary</th>
            <th>Findings</th>
            <th>Created At</th>
            <th>Resolved At<br/>Resolved By</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php $rowNum = 1 + (($pagination->currentPage() - 1) * $pagination->perPage()); @endphp
        @foreach($items as $row)
            @php
                $alertVal     = $row['alert'];
                $isCritical   = $alertVal === true;
                $isClear      = $alertVal === false;
                $findingCount = count($row['findings']);
                $receivedAt   = $row['created_at'] ? \Carbon\Carbon::parse($row['created_at'])->format('m/d/Y h:i A') : '—';
                $alertKey     = $isCritical ? 'critical' : ($isClear ? 'clear' : 'pending');
            @endphp
            <tr class="ca-row"
                data-alert="{{ $alertKey }}"
                data-resolved="{{ $row['resolved_flag'] ? '1' : '0' }}">

                <td>{{ $rowNum++ }}</td>

                <td>
                    @if($row['task_id'])
                        <a href="javascript:void(0)"
                           onclick="openVisitModal('{{ $row['task_id'] }}')"
                           data-toggle="tooltip" title="View Visit Detail"
                           style="color:#007bff;font-weight:600;">
                            #{{ $row['task_id'] }}
                        </a>
                    @else —
                    @endif
                </td>

                <td>{{ $row['patient_id'] ?: '—' }}</td>

                <td>{{ $row['agency_name'] ?: '—' }}</td>

                <td>
                    @if($isCritical)
                        @if($row['resolved_flag'])
                            <span class="label label-danger" title="Critical" style="opacity:.6;">&#9888;</span>
                        @else
                            <span class="label label-danger">&#9888; Critical</span>
                        @endif
                    @elseif($isClear)
                        <span class="label label-success">&#10003; Clear</span>
                    @else
                        <span class="label label-default">Pending</span>
                    @endif
                </td>

                <td style="max-width:220px;">
                    @if($row['summary'])
                        <span style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;"
                              title="{{ $row['summary'] }}">{{ $row['summary'] }}</span>
                    @else —
                    @endif
                </td>

                <td class="text-center">
                    @if($findingCount > 0)
                        @if($row['resolved_flag'])
                            <span class="label label-success">{{ $findingCount }}</span>
                        @else
                            <span class="label {{ $isCritical ? 'label-danger' : 'label-success' }}">{{ $findingCount }}</span>
                        @endif
                    @else —
                    @endif
                </td>

                <td class="mailbox-date">{{ $receivedAt }}</td>

                <td>
                    {{ ($row['resolved_flag'] && $row['resolved_at']) ? $row['resolved_at'] : '—' }}<br/>
                    @if($row['resolved_flag'] && $row['resolved_by'])
                        <span style="color:#28a745;font-weight:600;">{{ $row['resolved_by'] }}</span>
                    @else —
                    @endif
                </td>

                <td class="mailbox-date">
                    @if($row['resolved_flag'])
                        <span class="label label-success"><i class="mdi mdi-check-circle"></i> Resolved</span>
                    @else
                        -
                    @endif
                </td>

                <td style="white-space:nowrap;">
                    {{-- View Alert button --}}
                    <a href="javascript:void(0)"
                       onclick="openCaDetail(this)"
                       class="btn btn-sm btn-outline-primary"
                       style="font-size:11px;padding:3px 8px;margin-right:4px;"
                       data-toggle="tooltip" title="View Alert Detail"
                       data-id="{{ $row['id'] }}"
                       data-task-id="{{ $row['task_id'] }}"
                       data-patient-id="{{ $row['patient_id'] }}"
                       data-alert="{{ $alertKey }}"
                       data-summary="{{ addslashes($row['summary']) }}"
                       data-findings="{{ addslashes(json_encode($row['findings'])) }}"
                       data-received="{{ $receivedAt }}"
                       data-resolved="{{ $row['resolved_flag'] ? '1' : '0' }}"
                       data-resolved-notes="{{ addslashes($row['resolved_notes'] ?? '') }}"
                       data-resolved-by="{{ addslashes($row['resolved_by'] ?? '') }}"
                       data-resolved-at="{{ addslashes($row['resolved_at'] ?? '') }}">
                        <i class="fa fa-eye"></i> View Alert
                    </a>

                    {{-- Resolve button or Resolved badge --}}
                    @if($row['resolved_flag'] == 0)
                        <a href="javascript:void(0)"
                           class="btn btn-sm btn-warning ca-resolve-btn"
                           style="font-size:11px;padding:3px 8px;color:#fff;"
                           data-toggle="tooltip" title="Mark as Resolved"
                           onclick="openCaResolveModal({{ $row['id'] }})"
                           data-id="{{ $row['id'] }}">
                            <i class="fa fa-check-circle"></i> Mark Resolved
                        </a>
                    @endif
                </td>

            </tr>
        @endforeach
    </tbody>
</table>

<div class="pull-right pegination-margin">
    {{ $pagination->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

@endif

<script>
    $('.shimmer_id').hide();
    $('[data-toggle="tooltip"]').tooltip();
</script>
