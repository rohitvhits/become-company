<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <th>#</th>
        <th>Patient ID</th>
        <th>Mobile</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Created Date</th>
       
    </thead>
    <tbody>
    @php
    $i = 1 + ($details->currentPage() - 1) * $details->perPage();
    @endphp
   
    @forelse($details as $row)
            <tr>
                <td>{{ $i}}</td>
                <td><a href="{{ url('/patient/view/')}}/{{ $row->patient_id }}" target="_blank">{{ $row->patient_id }}</a></td>
                <td>{{ $row->mobile }}</td>
                <td>{{ $row->phone }}</td>
                <td>
                    @if($row->sms_status =='Pending')
                    <span class="badge badge-primary">Pending</span>
                    @else
                    <span class="badge badge-success">Success</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($row->created_date)->format('m/d/Y h:i A ') }}</td>
                
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="4" class="text-center">No records found</td>
            </tr>
        @endforelse

    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $details->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>