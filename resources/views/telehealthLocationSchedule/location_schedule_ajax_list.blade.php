<div class="card-body">
    <div class="row">
        <div class="col-12">
            <table id="order-listing1" class="table table-bordered table-width1">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Title</th>
                        <th width="15%">Day</th>
                        <th width="15%">Start Time</th>
                        <th width="15%">End Time</th>
                        <th width="5%">Status</th>
                        <th width="10%">Time Slot<br/>(In Minutes)</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>
                <tbody> 
                    @if ($query->total() != 0)
                        @php $i = 1 + (($query->currentPage() - 1) * $query->perPage()); @endphp
                        @foreach ($query as $row)
                            <tr>

                                <td>{{ $i++ }}</td>
                                <td>{{ ($row->title) }}</td>
                                <td id="schedule-days-{{ $row->id }}">
                                    @foreach($row->days as $day)
                                        <span class="badge badge-primary mr-1">{{ ucfirst($day) }}</span>
                                    @endforeach
                                </td>
                                <td>{{ date('h:i A', strtotime($row->start_time)) }}</td>
                                <td>{{ date('h:i A', strtotime($row->end_time)) }}</td>

                                <td id="row_{{ $row->id}}">
                                    @if($row->disable_status =='N')
                                    <span class="badge badge-success">Enabled</span>
                                    @else
                                    <span class="badge badge-danger">Disabled</span>
                                    @endif
                                </td>
                                <td>{{ $row->slot}}</td>
                                <td>
                                    @can('edit-telehealth-location-schedule')
                                        <a class="modal-edit" data-toggle="modal" data-target="#editModal" data-id="{{$row->id}}" href="javascript::void(0)" onclick="getEditModelData('{{$row->id}}')" data-toggle="tooltip" data-placement="top" title="Edit Schedule"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('delete-telehealth-location-schedule')
                                        <a href="javascript:void(0)" onclick="deleteTeleSchedule('{{$row->id}}')" data-toggle="tooltip" data-placement="top" title="Delete Schedule"><i class="fa fa-trash"></i></a>
                                    @endcan
                                    @can('telehealth-status-change-location-schedule')
                                        <label class="toggle-switch toggle-switch-success" data-toggle="tooltip" data-placement="top" title="Toggle Schedule Status">
                                            <input type="checkbox" data-id="{{ $row->id}}" onchange="statusChange('{{ $row->id}}')" class="locationEnableDisabled" name="is_disabled" id="is_disabled{{$row->id}}" value="1" @if($row->disable_status =='N') checked @endif >
                                            <span class="toggle-slider round"></span>
                                        </label>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        @else 
                        <tr>
                            <td colspan="12">
                                <center><b>Data not found</b></center>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="pull-right pegination-margin">
                {{ $query->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>