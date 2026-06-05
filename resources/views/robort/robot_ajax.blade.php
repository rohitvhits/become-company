<div class="table-responsive">
    <div class="order-listing-loader">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="order-listing1" class="table table-bordered table-head-fix">
        <thead>
            <tr>
                <th style="white-space:nowrap">
                    <input type="checkbox" id="cboxid">
                </th>
                <th style="white-space:nowrap">
                    No
                </th>
                <th style="white-space:nowrap">
                    Agency Name
                </th>
                <th style="white-space:nowrap">
                    Patient ID
                </th>
                <th style="white-space:nowrap">
                    Full Name
                </th>
                <th style="white-space:nowrap">
                    Date of Birth
                </th>
                <th style="white-space:nowrap">
                    Gender
                </th>
                <th style="white-space:nowrap">
                    Patient Status
                </th>
                <th style="white-space:nowrap">
                    Appointment Status
                </th>
                <th style="white-space:nowrap">
                    Created Date
                </th>
                <th style="white-space:nowrap">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            <tr>
                <td>
                    @if($row->appointment_id !='')

                    @else
                    <input type="checkbox" name="cbox" class="cbox" value="{{ $row->id}}">
                    @endif
                </td>
                <td>{{ $i++}}</td>
                <td>{{ $row->agency_name}}</td>
                <td>{{ $row->patientId}}</td>
               
                <td>{{ $row->firstName}}  {{ $row->lastName}}</td>
                <td>{{ date('m/d/Y',strtotime($row->dob))}}</td>
                <td>{{ $row->gender}}</td>
                <td>
                    @if($row->status ==2)
                    <span class="badge badge-secondary">Pre-Active</span>
                    @elseif($row->status ==3)
                    <span class="badge badge-success">Active</span>
                    @elseif($row->status ==4)
                    <span class="badge badge-warning">On Hold</span>
                    @elseif($row->status ==5)
                    <span class="badge badge-info">Discharged</span>
                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif
                </td>

                <td>
                @if($row->appointment_id !='')
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->appointment_id }}"><span class="badge badge-success">Added</span></a>
                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif
                </td>

                <td>{{ date('m/d/Y',strtotime($row->created_date))}}</td>

                <td class="action-column">
                    @if($row->appointment_id =='')
                    @can('add-robort-appointment')
                        <a href="javascript:void(0)" class="action-link" onclick="addAppointment('{{ $row->id}}','single')" title="Add Appointment">
                            <i class="fa fa-calendar"></i>
                        </a>
                    @endcan
                    @else
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->appointment_id }}" class="action-link" title="View">
                        <i class="fa fa-eye text-info"></i>
                    </a>
                    @endif

                    @if($row->uuid !="" && $row->appointment_id !="")
                    <a href="javascript:void(0)" class="action-link" onclick="uploadRemoteDocument('{{ $row->id }}')" title="Upload Document">
                        <i class="fa fa-upload text-success"></i>
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach

            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="11" class="text-center py-4">
                    <div class="no-data-message">
                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="mb-0"><strong>No records found</strong></p>
                        <p class="text-muted small">Try adjusting your search filters</p>
                    </div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin mt-3">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
<script>
   $('#appointment_id').html("{{$query->total()}}");
</script>