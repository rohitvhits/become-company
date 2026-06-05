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
        @if (count($todayAppoinmentData) > 0)
            @foreach($todayAppoinmentData as $todayData)
            <tr>
                <th scope="row"><a href="{{ url('patient/view/')}}/{{ $todayData->patient->id}}" target="_blank"><?= '#' . '' . $todayData->patient->id ?></a>
                </th>
                <td>{{$todayData->patient->agencyDetail->agency_name}}</td>
                <td>{{$todayData->patient->type}}</td>
                <td>{{$todayData->patient->first_name}} 
                {{$todayData->patient->middle_name}}{{$todayData->patient->last_name }}</td>
                <td>{{date('m/d/Y h:i A',strtotime($todayData->appointment_date))}}</td>
                <td>{{$todayData->patient->assign_status}}</td>
                <td>
                <?php
                    if (strtolower($todayData->status) == 'pending') {
                    ?>
                        <label class='badge badge-warning'>Pending</label>
                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'booked') {
                    ?>
                        <label class='badge badge-info'>Booked</label>

                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'completed') {
                    ?>
                        <label class='badge badge-success'>Completed</label>

                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'cancelled') {
                    ?>
                        <label class='badge badge-danger'>Cancelled</label>

                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'noshow') {
                    ?>
                        <label class='badge badge-light'>No Show</label>

                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'refused') {
                    ?>
                        <label class='badge badge-danger'>Refused</label>

                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'processing') {
                    ?>
                        <label class='badge badge-info'>processing</label>

                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'arrived') {
                    ?>
                        <label class='badge badge-primary'>Arrived</label>

                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'checkin') {
                    ?>
                        <label class='badge badge-primary'>Mark as ClockIn</label>

                    <?php } ?>
                    <?php

                    if (strtolower($todayData->status) == 'not interested') {
                    ?>
                        <label class='badge badge-primary'>Not Interested</label>

                    <?php }
                    if (strtolower($todayData->status) == 'hospitalized/rehab') {
                    ?>
                        <label class='badge badge-secondary'>Hospitalized/Rehab</label>

                    <?php }
                    if (strtolower($todayData->status) == 'unabletocontact') {
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
{{ $todayAppoinmentData->appends(request()->query())->links() }}
</div>