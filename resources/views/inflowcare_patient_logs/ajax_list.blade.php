<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th nowrap>Agency Name</th>
                <th nowrap>Patient ID</th>
                <th nowrap>Patient Name</th>
                <th nowrap>Message</th>
                <th nowrap>Status</th>
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
                        <td>{{ $log->agency->agency_name ?? '' }}</td>
                        <td>{{ $log->patient_id ?? '' }}</td>
                        <td>{{ ($log->patient->first_name ?? '') . ' ' . ($log->patient->last_name ?? '') }}</td>
                        <td>{{ $log->message ?? '' }}</td>
                        <td>
                            @if($log->status == 'success')
                                <span class="badge badge-success">{{ $log->status }}</span>
                            @elseif($log->status == 'failed')
                                <span class="badge badge-danger">{{ $log->status }}</span>
                            @else
                                <span class="badge badge-info">{{ $log->status ?? '' }}</span>
                            @endif
                        </td>
                        <td nowrap>{{ $log->created_at ? date('m/d/Y h:i A', strtotime($log->created_at)) : '' }}</td>
                        <td>{{ ($log->userDetail->first_name ?? '') . ' ' . ($log->userDetail->last_name ?? '') }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info view-payload-btn"
                                data-request="{{ $log->request_payload }}"
                                data-response="{{ $log->response_payload }}">
                                <i class="mdi mdi-eye"></i> View
                            </button>
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

<div class="d-flex justify-content-center mt-3">
    {{ $logs->links() }}
</div>

<script>
    $('#total_record_id').text('{{ $logs->total() }}');
</script>
