<table id="" class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th style="white-space:nowrap">Full Name</th>
            <th style="white-space:nowrap">Email</th>
            <th style="white-space:nowrap">Phone</th>
            <th style="white-space:nowrap">Agency Name</th>
            <th style="white-space:nowrap">Service Requested</th>
            <th style="white-space:nowrap">Appointment Date</th>
            <th style="white-space:nowrap">Appointment Time</th>
            <th style="white-space:nowrap">Appointment Address</th>
            <th style="white-space:nowrap">Referral Type</th>
            <th style="white-space:nowrap">Created Date</th>
        </tr>
    </thead>
    <tbody>
    @php
        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
    @endphp

    @forelse($leads as $lead)
        <tr>
            <td>{{ $serial++ }}</td>
            <td style="white-space:nowrap">{{ $lead->first_name ?? '-' }} {{ $lead->last_name ?? '-' }}</td>
            <td style="white-space:nowrap">{{ $lead->email ?? '-' }}</td>
            <td style="white-space:nowrap">{{ $lead->phone ?? '-' }}</td>
            <td style="white-space:nowrap">{{ $lead->agency_name ?? '-' }}</td>
            <td style="white-space:nowrap">{{ $lead->service_requested ?? '-' }}</td>
            <td style="white-space:nowrap">{{ $lead->appointment_date ? Common::convertMDY($lead->appointment_date) : '-' }}</td>
            <td style="white-space:nowrap">{{ Common::convertTwelveHourTime($lead->appointment_time) ?? '-' }}</td>
            <td style="white-space:nowrap">{{ $lead->appointment_address ?? '-' }}</td>
            <td style="white-space:nowrap">{{ $lead->name ?? '-' }}</td>
            <td style="white-space:nowrap">{{ $lead->created_date ?Common::convertMDYTime($lead->created_date): '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="11">
                No lead records found
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $leads->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
<script>
var total = "{{ count($leads)}}"
    $('#blank_div').attr('style','margin-top:15%')
    if(total ==0){
        $('#blank_div').attr('style','margin-top:15%')
    }

</script>