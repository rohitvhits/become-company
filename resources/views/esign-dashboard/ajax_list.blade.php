<table id="esign-dashboard-listing" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th>#</th>
            <th>Agency Name</th>
            <th>Patient ID</th>
            <th>Patient Name</th>
            <th>Template Name</th>
            <th>Document Status</th>
            <th>Created Date</th>
            <th>Created By</th>
            <th>Completed Date / By</th>
            <th>Review Date / Review By</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($list as $key => $val)
            <tr>
                    <td>{{ $list->firstItem() + $key }}</td>
                    <td>{{ $val->agency_name ?? '-' }}</td>
                    <td><a href="{{ url('patient/view')}}/{{ $val->patient_id}}">{{ $val->patient_id ?? '-' }}</a></td>
                    <td><a href="{{ url('patient/view')}}/{{ $val->patient_id}}">{{ $val->patient_name ?? '-' }}</a></td>
                    <td>{{ ucfirst($val->template_name ?? '-') }}</td>
                    <td>
                        @php
                            $statusClass = 'badge-secondary';
                            $status = $val->status ?? 'N/A';
                            if($status == 'Pending') $statusClass = 'badge-warning';
                            elseif($status == 'Completed') $statusClass = 'badge-success';
                            elseif($status == 'Approved') $statusClass = 'badge-info';
                            elseif($status == 'Rejected') $statusClass = 'badge-danger';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $status }}</span>
                    </td>
                    <td>{{ $val->created_date ? Common::convertMDYTime($val->created_date) : '-' }}</td>
                    <td>{{ $val->created_by_name ?? '-' }}</td>
                    <td>
                        @if($val->completed_on)
                        
                            {{ Common::convertMDYTime($val->completed_on) }}<br>
                            <small class="text-muted">{{ $val->completed_by_name ?? '-' }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($val->review_date)
                            {{ $val->review_date }}<br>
                            <small class="text-muted">{{ $val->review_by ?? '-' }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if(strtolower($val->status) =='pending')
                        <a href="{{ url('esign/docusign/viewNew') }}/{{ $val->document_report_id }}"  title="View Document" target="_blank">
                            <i class="fa fa-eye"></i>
                        </a>
                        @else
                            <a href="{{ url('dre')}}/{{ $val->groupId}}"><i class="fa fa-eye"></i></a>
                        @endif
                    </td>
                </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center">No records found.</td>
            </tr>
        @endforelse
        
    </tbody>
</table>

@if(count($list) > 0)
    <div class="d-flex justify-content-center">
        {{ $list->links() }}
    </div>
@endif

<script>

    </script>