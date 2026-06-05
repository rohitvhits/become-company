<table id="" class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Agency Name</th>
            <th>Type</th>
            <th>Name</th>
            <th>Date & Time</th>
            <th>Assign/Not</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @if (count($upcommingAppoinmentData) > 0)
            @foreach($upcommingAppoinmentData as $upCommingData)
            <tr>
                <th scope="upCommingData"><a href="{{ url('patient/view/')}}/{{ $upCommingData->patient->id}}" target="_blank"><?= '#' . '' . $upCommingData->patient->id ?></a>
                </th>
                <td>{{$upCommingData->patient->agencyDetail->agency_name}}</td>
                <td>{{$upCommingData->patient->type}}</td>
                <td>{{$upCommingData->patient->first_name}} {{$upCommingData->patient->middle_name}}  {{$upCommingData->patient->last_name }}</td>
                <td>{{date('m/d/Y h:i A',strtotime($upCommingData->appointment_date))}}</td>
                <td>{{$upCommingData->patient->assign_status}}</td>
                <td>
                <?php
                    if (strtolower($upCommingData->status) == 'pending') {
                    ?>
                        <label class='badge badge-warning'>Pending</label>
                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'booked') {
                    ?>
                        <label class='badge badge-info'>Booked</label>

                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'completed') {
                    ?>
                        <label class='badge badge-success'>Completed</label>

                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'cancelled') {
                    ?>
                        <label class='badge badge-danger'>Cancelled</label>

                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'noshow') {
                    ?>
                        <label class='badge badge-light'>No Show</label>

                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'refused') {
                    ?>
                        <label class='badge badge-danger'>Refused</label>

                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'processing') {
                    ?>
                        <label class='badge badge-info'>processing</label>

                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'arrived') {
                    ?>
                        <label class='badge badge-primary'>Arrived</label>

                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'checkin') {
                    ?>
                        <label class='badge badge-primary'>Mark as ClockIn</label>

                    <?php } ?>
                    <?php

                    if (strtolower($upCommingData->status) == 'not interested') {
                    ?>
                        <label class='badge badge-primary'>Not Interested</label>

                    <?php }
                    if (strtolower($upCommingData->status) == 'hospitalized/rehab') {
                    ?>
                        <label class='badge badge-secondary'>Hospitalized/Rehab</label>

                    <?php }
                    if (strtolower($upCommingData->status) == 'unabletocontact') {
                    ?>
                        <label class='badge badge-primary'>Unable To Contact</label>

                    <?php } ?>
                </td>
            </tr>
            @endforeach
        @else
        <tr>
            <td colspan="7">
                <center><b>Data not found</b></center>
            </td>
        </tr>
        @endif
    </tbody>
    </table>
    <div class="pull-right pegination-margin">
    {{ $upcommingAppoinmentData->appends(request()->query())->links() }}
    </div>