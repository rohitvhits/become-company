<div class="table-responsive ">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th><input type="checkbox" id="cboxid"></th>
                <th>#</th>
                <th style="white-space:nowrap">Agency Name</th>
                <th style="white-space:nowrap">Employee Name</th>
                <th style="white-space:nowrap">Employee Code</th>
                <th style="white-space:nowrap">Date of Birth</th>
                <th style="white-space:nowrap">Employee Phone</th>
                <th style="white-space:nowrap">Gender</th>
                <th style="white-space:nowrap">Employee Status</th>
                <th style="white-space:nowrap">Skill Name</th>
                <th style="white-space:nowrap">Due Date</th>
                <th style="white-space:nowrap">Appointment Status</th>
                <th style="white-space:nowrap">Created Date</th>
                <th>Action</th>
                
            </tr>
        </thead>
        <tbody>

        @php
            $i = 1 + ($list->currentPage() - 1) * $list->perPage();
            @endphp
            @if (count($list) > 0)
            @foreach ($list as $row)
            <tr>
                <td>
                    @if($row->patient_id !='')

                    @else
                    <input type="checkbox" name="cbox" class="cbox" value="{{ $row->id}}">
                    @endif
                </td>
                <td>{{ $i++}}</td>
                 <td>{{ $row->agency_name}}</td>
                 <td>{{ $row->first_name}} {{ $row->last_name}}</td>
                <td>{{ $row->employee_id}}</td>
                <td>{{ Common::convertMDY($row->birthday)}}</td>
                <td>{{ $row->phone}}</td>
                <td>{{ $row->emp_gender ?? '' }}</td>
                <td>
                    @switch(strtolower($row->emp_status ?? ''))
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
                            <span class="badge badge-secondary">{{ ucfirst($row->emp_status ?? 'N/A') }}</span>
                    @endswitch
                </td>
                <td>{{ $row->skill_name}}</td>
                <td>{{ Common::convertMDYTime($row->due_date)}}</td>
                
                <td>
                @if($row->patient_id !='')
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->patient_id }}"><span class="badge badge-success">Added</span></a>
                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif
                </td>
                <td>{{ Common::convertMDYTime($row->created_date)}}</td>
                <td> @if($row->patient_id =='')
                     @can('add-appointment-alayacare-due-skill')
                    <a href="javascript:void(0)" onclick="singleDataAppointment('{{ $row->id}}')" title="Add Appointment"><i class="fa fa-calendar"></i></a>
                    @endcan
                    @else
                    <a href="{{ url('patient/view')}}/{{ $row->patient_id }}" ><i class="fa fa-eye"></i></a>
                    @endif
                </td>
            </tr>
            @endforeach
            @endif
            @if (count($list) == 0)
                <tr>
                    <td colspan="15">No record available</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="pull-right pegination-margin">

        {{ $list->appends(request()->input())->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
    var total = "{{ $list->total()}}";
    $('#appointment_id').html(total)
    $('#blank_div').attr('style', 'margin-top:12%')
    if (total == 0) {
        $('#blank_div').attr('style', 'margin-top:10%')
    }
</script>