<div class="table-responsive">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th><input type="checkbox" id="cboxid"></th>
                <th>#No</th>
                <th style="white-space: nowrap;">Agency Name</th>
                <th style="white-space: nowrap;">Branch Name</th>
                <th style="white-space: nowrap;">First Name</th>
                <th style="white-space: nowrap;">Last Name</th>
                
                <th style="white-space: nowrap;">Phone No</th>
                
                <th style="white-space: nowrap;">Group Name</th>
                <th style="white-space: nowrap;">City</th>
                <th style="white-space: nowrap;">State</th>
                <th style="white-space: nowrap;">Gender</th>
                <th style="white-space: nowrap;">Client Status</th>
                <th style="white-space: nowrap;">Appointment Status</th>
                <th style="white-space: nowrap;">Created Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
                @foreach ($query as $value)
                <tr>
                    <td>
                        @if($value->patient_id != '')
                        @else
                        <input type="checkbox" name="cbox" class="cbox" value="{{ $value->id }}">
                        @endif
                    </td>
                    <td>{{ $i++ }}</td>
                    <td>{{ $value->agencyDetails->agency_name ?? "" }}</td>
                    <td>{{ $value->branch_name }}</td>
                    <td>{{ $value->first_name }}</td>
                    <td>{{ $value->last_name }}</td>
                    <td>{{ $value->phone_main }}</td>
                    <td>{{ $value->group_name }}</td>
                    <td>{{ $value->city }}</td>
                    <td>{{ $value->state }}</td>
                    <td>{{ $value->gender }}</td>
                    <td>
                        @switch(strtolower($value->status ?? ''))
                            @case('active')
                                <span class="badge badge-success">Active</span>
                                @break
                            @case('discharged')
                                <span class="badge badge-danger">Discharged</span>
                                @break
                            @case('on_hold')
                                <span class="badge badge-warning">On Hold</span>
                                @break
                            @case('pending')
                                <span class="badge badge-primary">Pending</span>
                                @break
                            @case('waiting_list')
                                <span class="badge badge-info">Waiting List</span>
                                @break
                                @case('FalsestatusALICE')
                                <span class="badge badge-defualt">FalsestatusALICE</span>
                                @break
                            @default
                                <span class="badge badge-secondary">{{ ucfirst($value->status ?? 'N/A') }}</span>
                        @endswitch
                    </td>
                    <td>
                        @if($value->patient_id != '')
                        <a target="_blank" href="{{ url('patient/view') }}/{{ $value->patient_id }}">
                            <span class="badge badge-success">Added</span>
                        </a>
                        @else
                        <span class="badge badge-primary">Pending</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">{{ $value->created_at ? Common::convertMDYTime($value->created_at) : '-' }}</td>
                    <td>
                        @if($value->patient_id == null)
                            @can('alayacare-client-add-appointment')
                            <a href="javascript:void(0)" title="Add Appointment" onclick="PatientAddAppointment('{{ $value->id }}', '{{ $value->client_id }}')">
                                <i class="fa fa-calendar"></i>
                            </a>
                            @endcan
                        @else
                        <a href="{{ url('patient/view') }}/{{ $value->patient_id }}">
                            <i class="fa fa-eye"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="15">No record available</td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="pull-right pegination-margin">
        {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
    var total = "{{ $query->total() }}";
    $('#blank_div').attr('style', 'margin-top:12%');
    if (total == 0) {
        $('#blank_div').attr('style', 'margin-top:10%');
    }
</script>
