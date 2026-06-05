<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th nowrap>Patient ID</th>
                <th nowrap>Patient Full Name</th>
                <th nowrap>Visit ID</th>
                <th nowrap>HHA Patient ID</th>
                <th nowrap>Type</th>
                <th nowrap>Created Date</th>
                <th nowrap>Created By</th>
                <th nowrap>Action</th>
            </tr>
        </thead>
        <tbody>
            @if($logs->total() > 0)
                @foreach($logs as $key => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $key }}</td>
                        <td>{{ $log->patient_id ?? '' }}</td>
                        <td>{{ ($log->patient->first_name ?? '') . ' ' . ($log->patient->last_name ?? '') }}</td>
                        <td>{{ $log->th_visit_id ?? '' }}</td>
                        <td>{{ $log->hha_patient_id ?? '' }}</td>
                        <td>{{ $log->type ?? '' }}</td>
                        <td nowrap>{{ $log->created_at ? date('m/d/Y h:i A', strtotime($log->created_at)) : '' }}</td>
                        <td>{{ ($log->createdByUser->first_name ?? '') . ' ' . ($log->createdByUser->last_name ?? '') }}</td>
                        <td>
                            @can('hha-audit-log-detail-view')
                            <a href="javascript:void(0)" onclick="viewPocLogDetail('{{ $log->id }}')" title="View Details">
                                <i class="fa fa-eye"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center">No records found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="pull-right pegination-margin">
    {{ $logs->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

<script>
    $('#total_record_id').text('{{ $logs->total() }}');
</script>
