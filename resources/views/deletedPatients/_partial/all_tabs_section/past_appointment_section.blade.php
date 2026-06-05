<div class="tab-pane" id="appointment-section">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Appointment</p>
        <p class="mb-0 tx-13">
            <a href="javascript:void(0);" data-toggle="modal" data-target="#exampleModal-4" data-whatever="@mdo"
                class="pull-right btn btn-info btn-sm d-none d-md-block addAppointment"><i class="mdi mdi-plus"></i>
                Add</a>
        </p>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive ">
                <table id="" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Created Date</th>
                            <th>Created By</th>
                            <!-- <th>Last Updated By</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($pastAppointment) > 0)
                            @foreach ($pastAppointment as $key => $appointment)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="white_space">
                                        {{ $appointment->patient->full_name }}</td>
                                    <td>
                                        @if (isset($appointment->location) && $appointment->location->address1)
                                            {{ $appointment->location->address1 }}
                                        @endif
                                    </td>
                                    <td>{{ isset($servie[$key]) ? $servie[$key] : '' }}
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($appointment->appointment_date)) }}
                                    </td>
                                    <td>{{ date('h:i:s A', strtotime($appointment->appointment_time)) }}
                                    </td>
                                    <td>{{ date('m/d/Y h:i A', strtotime($appointment->created_at)) }}
                                    </td>
                                    <td>{{ $appointment->getCreatedBy->full_name }}
                                    </td>
                                    <!-- <td>{{ $appointment->getUpdateBy != null ? $appointment->getUpdateBy->full_name : '' }}</td> -->
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" style="text-align: center;">Data
                                    not found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="pull-right pegination-margin">
                </div>
            </div>
        </div>
    </div>
</div>
