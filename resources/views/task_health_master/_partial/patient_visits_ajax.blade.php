@if($error)
    <div class="alert alert-danger">{{ $error }}</div>
@elseif(empty($items))
    <div class="text-center text-muted py-4">No visits found for this patient.</div>
@else
<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>Task ID</th>
                <th>Task Type</th>
                <th>Patient Name</th>
                <th>Status</th>
                <th>Review Status</th>
                <th>Critical Alert</th>
                <th>Scheduled Date</th>
                <th>Created Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
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
                    <a href="javascript:void(0)" onclick="openVisitDetailModal({{ $row['taskId'] ?? 0 }})">
                        #{{ $row['taskId'] ?? '—' }}
                    </a>
                </td>
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
                    @php $ca = $row['criticalAlert'] ?? null; @endphp
                    @if(is_null($ca))
                        <span class="label label-default">Not Analyzed</span>
                    @elseif(!($ca['alert'] ?? false))
                        <span class="label label-success">Clear</span>
                    @else
                        <span class="label label-danger">&#9888; Critical</span>
                    @endif
                </td>
                <td>{{ isset($row['scheduledDateTime']) ? date('m/d/Y h:i A', strtotime($row['scheduledDateTime'])) : '—' }}</td>
                <td>{{ isset($row['createdAt']) ? date('m/d/Y h:i A', strtotime($row['createdAt'])) : '—' }}</td>
                <td>
                    <a href="javascript:void(0)" onclick="openVisitDetailModal({{ $row['taskId'] ?? 0 }})" title="View Detail" class="text-info">
                        <i class="fa fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if(!empty($pagination) && ($pagination['totalPages'] ?? 1) > 1)
@php
    $currentPage = $pagination['page'] ?? 1;
    $totalPages  = $pagination['totalPages'] ?? 1;
    $total       = $pagination['total'] ?? 0;
@endphp
<div class="d-flex justify-content-between align-items-center mt-2">
    <small class="text-muted">Total: {{ $total }} records &nbsp;|&nbsp; Page {{ $currentPage }} of {{ $totalPages }}</small>
    <div>
        @if($currentPage > 1)
            <button class="btn btn-sm btn-secondary" onclick="loadPatientVisits({{ $currentPage - 1 }})">&laquo; Prev</button>
        @endif
        @if($currentPage < $totalPages)
            <button class="btn btn-sm btn-primary ml-1" onclick="loadPatientVisits({{ $currentPage + 1 }})">Next &raquo;</button>
        @endif
    </div>
</div>
@endif

@endif
