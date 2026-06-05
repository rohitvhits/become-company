<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th>No</th>
            <th>Medical ID</th>
            <th>Medical Name</th>
            <th>Patient Name</th>
            <th>Status</th>
         
           
        </tr>
    </thead>
    <tbody>
        @if(isset($message) && $message)
            <tr>
                <td colspan="7" class="text-center text-info">
                    <i class="fa fa-info-circle"></i> {{ $message }}
                </td>
            </tr>
            
        @elseif(isset($query) && is_array($query) && count($query) > 0)
            @php
                $i = isset($pagination) ? $pagination['from'] : 1;
            @endphp
            @foreach($query as $val)
                <tr>
                    <td>{{ $i }}</td>
                    <td class="mailbox-subject">{{ $val['MedicalID'] ?? $val['MedicalID'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst($val['MedicalName'] ?? $val['MedicalName'] ?? 'N/A') }}</td>
                    <td>{{ ucfirst($val['patient_name'] ?? $val['patientName'] ?? 'N/A') }}</td>
                    <td>
                        @php
                            $status = strtolower($val['status'] ?? 'pending');
                            $badgeClass = 'badge-info';
                            if ($status == 'completed') {
                                $badgeClass = 'badge-success';
                            } elseif ($status == 'pending') {
                                $badgeClass = 'badge-warning';
                            } elseif ($status == 'in_progress' || $status == 'in progress') {
                                $badgeClass = 'badge-info';
                            } elseif ($status == 'cancelled' || $status == 'failed') {
                                $badgeClass = 'badge-danger';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                    </td>
                    <td class="mailbox-date">
                        {{ isset($val['date']) ? date('m/d/Y h:i A', strtotime($val['date'])) : 'N/A' }}
                    </td>
                   
                </tr>
                @php $i++; @endphp
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center">No Records Found</td>
            </tr>
        @endif
    </tbody>
</table>

<script>
var total = "{{ isset($query) && is_array($query) ? count($query) : 0 }}"
    $('#blank_div').attr('style','margin-top:30px')
    if(total == 0){
        $('#blank_div').attr('style','margin-top:10%')
    }
</script>
