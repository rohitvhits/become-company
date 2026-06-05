@if($error)
    <div class="alert alert-danger mt-2">{{ $error }}</div>
@else

<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th>No</th>
            <th>Task ID</th>
            <th>Patient ID</th>
            <th>Agency Name</th>
            <th>Patient Name</th>
            <th>Task Type</th>
            <th>Status</th>
            <th>Review Status</th>
            <th>Critical Alert</th>
            <th>Scheduled Date</th>
            <th>Created Date</th>
            <th>Flags</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($items) > 0)
            @php $offset = (($pagination['page'] ?? 1) - 1) * ($pagination['limit'] ?? 50); @endphp
            @foreach($items as $i => $row)
            @php
                $status = $row['status'] ?? '';
                $statusClass = match(true) {
                    str_contains(strtolower($status), 'complet')  => 'label-success',
                    str_contains(strtolower($status), 'cancel')   => 'label-danger',
                    str_contains(strtolower($status), 'progress') => 'label-info',
                    default                                       => 'label-warning',
                };
            @endphp
            <tr>
                <td>{{ $offset + $i + 1 }}</td>
                <td>
                    <a href="javascript:void(0)" class="th-task-id-link" onclick="openVisitModal({{ $row['taskId'] ?? 0 }})">
                        #{{ $row['taskId'] ?? '—' }}
                    </a>
                </td>
                <td>
                    #{{ $row['patientId'] ?? '—' }}
                </td>
                <td>{{ $agencyMap[$row['agencyId'] ?? ''] ?? ($row['agencyName'] ?? $row['agencyId'] ?? '—') }}</td>
                <td>{{ trim(($row['patientFirstName'] ?? '') . ' ' . ($row['patientLastName'] ?? '')) ?: '—' }}</td>
                <td>{{ $row['taskType'] ?? '—' }}</td>
                <td><label class="label {{ $statusClass }}">{{ $status ?: '—' }}</label></td>
                <td>
                    @if(!empty($row['reviewStatus']))
                        <label class="label label-warning">{{ $row['reviewStatus'] }}</label>
                    @else —
                    @endif
                </td>
                <td>
                    @php
                        $ca = $row['criticalAlert'] ?? null;
                    @endphp
                    @if(is_null($ca))
                        <span class="label label-default" title="Assessment not yet analyzed" data-toggle="tooltip">Not Analyzed</span>
                    @elseif(!($ca['alert'] ?? false))
                        <span class="label label-success" title="No critical findings detected" data-toggle="tooltip">Clear</span>
                    @else
                        @php
                            $findings   = $ca['findings'] ?? [];
                            $summary    = $ca['summary']  ?? '';
                            $popLines   = [];
                            if ($summary)         { $popLines[] = '<p style="margin:0 0 6px;">'.e($summary).'</p>'; }
                            if (count($findings)) {
                                $popLines[] = '<ul style="margin:0;padding-left:16px;">';
                                foreach ($findings as $f) { $popLines[] = '<li>'.e($f).'</li>'; }
                                $popLines[] = '</ul>';
                            }
                            $popoverHtml = implode('', $popLines) ?: '—';
                        @endphp
                        <span class="label label-danger th-critical-alert-badge"
                              style="cursor:pointer;"
                              tabindex="0"
                              data-toggle="popover"
                              data-trigger="focus"
                              data-placement="left"
                              data-html="true"
                              title="&lt;strong&gt;Critical Findings&lt;/strong&gt;"
                              data-content="{{ $popoverHtml }}">
                            &#9888; Critical
                        </span>
                    @endif
                </td>
                <td class="mailbox-date">{{ isset($row['scheduledDateTime']) ? date('m/d/Y h:i A', strtotime($row['scheduledDateTime'])) : '—' }}</td>
                <td class="mailbox-date">{{ isset($row['createdAt']) ? date('m/d/Y h:i A', strtotime($row['createdAt'])) : '—' }}</td>
                <td>
                    @include('_partial.task_health_flags.flag_cell', [
                        'flags'           => $flagsMap[(string)($row['taskId'] ?? '')] ?? null,
                        'flagName'        => trim(($row['patientFirstName'] ?? '') . ' ' . ($row['patientLastName'] ?? '')),
                        'flagTaskId'      => $row['taskId'] ?? null,
                        'flagThPatientId' => $row['patientId'] ?? null,
                    ])
                </td>
                <td>
                    @can('task-health-visit-update')
                        <a href="javascript:void(0)" onclick="openEditModal({{ $row['taskId'] ?? 0 }})" data-toggle="tooltip" title="Edit Visit" style="color:#f0ad4e;margin-right:4px;">
                            <i class="fa fa-pencil"></i>
                        </a>
                    @endcan
                    @can('task-health-visit-cancel')
                        @php $currentStatus = strtolower($row['status'] ?? ''); @endphp
                        @if(!str_contains($currentStatus, 'cancel') && !str_contains($currentStatus, 'complet'))
                            <a href="javascript:void(0)" onclick="deleteVisit({{ $row['taskId'] ?? 0 }})" data-toggle="tooltip" title="Cancel Visit" style="color:#eb0d0d;margin-right:4px;">
                            <i class="fa fa-trash-o"></i>
                        </a>
                        @endif
                    @endcan
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="13" class="text-center">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

@if(!empty($pagination) && ($pagination['totalPages'] ?? 1) > 1)
<div class="pull-right pegination-margin">
    @php
        $currentPage  = $pagination['page']       ?? 1;
        $totalPages   = $pagination['totalPages']  ?? 1;
        $total        = $pagination['total']       ?? 0;
    @endphp
    <small class="text-muted mr-3">Total: {{ $total }} records &nbsp;|&nbsp; Page {{ $currentPage }} of {{ $totalPages }}</small>
    @if($currentPage > 1)
        <button class="btn btn-sm btn-secondary" onclick="loadVisitList({{ $currentPage - 1 }})">&laquo; Prev</button>
    @endif
    @if($currentPage < $totalPages)
        <button class="btn btn-sm btn-primary ml-1" onclick="loadVisitList({{ $currentPage + 1 }})">Next &raquo;</button>
    @endif
</div>
@endif

@endif

<script>
    var total = "{{ count($items) }}";
    $('.shimmer_id').hide();
    $('#blank_div').attr('style', 'margin-top:30px');
    if (total == 0) {
        $('#blank_div').attr('style', 'margin-top:10%');
    }
</script>
