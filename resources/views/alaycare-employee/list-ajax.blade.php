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
                <th style="white-space: nowrap;">Date of Birth</th>
                <th style="white-space: nowrap;">Gender</th>
                <th style="white-space: nowrap;">Mobile No</th>
                <th style="white-space: nowrap;">Email</th>
                <th style="white-space: nowrap;">Job Title</th>
                <th style="white-space: nowrap;">Employee Status</th>
                <th style="white-space: nowrap;">Appointment Status</th>
                <th style="white-space: nowrap;">Created Date</th>
                <th style="white-space: nowrap;">Last Skill Sync Date</th>
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
                    <td>{{ Common::convertMDY($value->birthday) }}</td>
                    <td>{{ $value->gender }}</td>
                    <td>{{ $value->phone }}</td>
                    <td>{{ $value->email }}</td>
                    <td>{{ $value->job_title }}</td>
                    <td>
                        @switch(strtolower($value->status ?? ''))
                            @case('active')
                                <span class="badge badge-success">Active</span>
                                @break
                            @case('inactive')
                                <span class="badge badge-danger">Inactive</span>
                                @break
                            @case('applicant')
                                <span class="badge badge-info">Applicant</span>
                                @break
                            @case('on_hold')
                                <span class="badge badge-warning">On Hold</span>
                                @break
                            @case('pending')
                                <span class="badge badge-primary">Pending</span>
                                @break
                            @case('suspended')
                                <span class="badge badge-dark">Suspended</span>
                                @break
                            @case('rejected')
                                <span class="badge badge-danger">Rejected</span>
                                @break
                            @case('terminated')
                                <span class="badge badge-dark">Terminated</span>
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
                    <td>{{ Common::convertMDYTime($value->created_at) }}</td>
                    <td>{{ Common::convertMDYTime($value->last_sync_skill_date) }}</td>
                    <td>
                        @if($value->patient_id == null)
                            @can('alayacare-employee-add-appointment')
                            <a href="javascript:void(0)" onclick="PatientAddAppointment('{{ $value->id }}', '{{ $value->emp_id }}')" title="Add Appointment">
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
