@if($error)
    <div class="alert alert-danger small py-2">{{ $error }}</div>
@elseif(empty($items))
    <div class="text-center text-muted py-4">
        <i class="mdi mdi-calendar-blank" style="font-size: 36px;"></i>
        <p class="mt-2">No visits found for this patient.</p>
    </div>
@else
<style>
    .label { display:inline; padding:.2em .6em .3em; font-size:85%; font-weight:700; line-height:1; color:#fff; text-align:center; white-space:nowrap; vertical-align:baseline; border-radius:.25em; }
    .label-success { background-color:#5cb85c; }
    .label-danger  { background-color:#d9534f; }
    .label-warning { background-color:#f0ad4e; }
    .label-info    { background-color:#5bc0de; }
    .label-default { background-color:#777; }
</style>
<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>Task ID</th>
                <th>Patient ID</th>
                <th>Task Type</th>
                <th>Patient Name</th>
                <th>Status</th>
                <th>Review Status</th>
                <th>Critical Alert</th>
                <th>Scheduled Date</th>
                <th>Created Date</th>
                <th>Flags</th>
            </tr>
        </thead>
        <tbody>
            @php $offset = (($pagination['page'] ?? 1) - 1) * ($pagination['limit'] ?? 20); @endphp
            @foreach($items as $i => $row)
            @php
                $status = $row['status'] ?? '';
                $statusClass = match(true) {
                    str_contains(strtolower($status), 'complet')  => 'label-success',
                    str_contains(strtolower($status), 'cancel')   => 'label-danger',
                    str_contains(strtolower($status), 'progress') => 'label-info',
                    default                                       => 'label-warning',
                };
                $ca = $row['criticalAlert'] ?? null;
            @endphp
            <tr>
                <td>{{ $offset + $i + 1 }}</td>
                <td>
                    <a href="javascript:void(0)" onclick="openTaskHealthVisitDetail({{ $row['taskId'] ?? 0 }})" class="text-primary">
                        #{{ $row['taskId'] ?? '—' }}
                    </a>
                </td>
                <td>{{ $row['patientId'] ?? '—' }}</td>
                <td>{{ $row['taskType'] ?? '—' }}</td>
                <td>{{ trim(($row['patientFirstName'] ?? '') . ' ' . ($row['patientLastName'] ?? '')) ?: '—' }}</td>
                <td><span class="label {{ $statusClass }}">{{ $status ?: '—' }}</span></td>
                <td>
                    @if(!empty($row['reviewStatus']))
                        <span class="label label-warning">{{ $row['reviewStatus'] }}</span>
                    @else —
                    @endif
                </td>
                <td>
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
                <td>{{ isset($row['scheduledDateTime']) ? date('m/d/Y h:i A', strtotime($row['scheduledDateTime'])) : '—' }}</td>
                <td>{{ isset($row['createdAt']) ? date('m/d/Y h:i A', strtotime($row['createdAt'])) : '—' }}</td>
                <td>
                    @include('_partial.task_health_flags.flag_cell', [
                        'flags'           => $flagsMap[(string)($row['taskId'] ?? '')] ?? null,
                        'flagName'        => trim(($row['patientFirstName'] ?? '') . ' ' . ($row['patientLastName'] ?? '')),
                        'flagTaskId'      => $row['taskId'] ?? null,
                        'flagThPatientId' => $row['patientId'] ?? null,
                    ])
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if(!empty($pagination) && ($pagination['totalPages'] ?? 1) > 1)
@php
    $currentPage = $pagination['page']       ?? 1;
    $totalPages  = $pagination['totalPages'] ?? 1;
    $total       = $pagination['total']      ?? 0;
@endphp
<div class="d-flex justify-content-between align-items-center mt-2">
    <small class="text-muted">Total: {{ $total }} records &nbsp;|&nbsp; Page {{ $currentPage }} of {{ $totalPages }}</small>
    <div>
        @if($currentPage > 1)
            <button class="btn btn-sm btn-secondary" onclick="loadVisitData({{ $currentPage - 1 }})">&laquo; Prev</button>
        @endif
        @if($currentPage < $totalPages)
            <button class="btn btn-sm btn-primary ml-1" onclick="loadVisitData({{ $currentPage + 1 }})">Next &raquo;</button>
        @endif
    </div>
</div>
@endif

@endif
