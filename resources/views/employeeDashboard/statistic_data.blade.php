<table id="" class="table">
    <thead>
        <tr>
            <th nowrap>ID</th>
            <th nowrap>Agency Name</th>
            <th nowrap>Type</th>
            <th nowrap>Name</th>
            <th nowrap>Assign Status</th>
        </tr>
    </thead>
    <tbody>
        @if (count($statisticData) > 0)
            @foreach($statisticData as $statistic)
            <tr>
                <th scope="statistic"><a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $statistic->id; ?>" target="_blank"><?= '#' . '' . $statistic->id ?></a>
                </th>
                <td nowrap>
                    <!-- <div id="logo-container">
                        @if($statistic->agencyDetail->agency_logo !="")
                        @php
                        $logo=$statistic->agencyDetail->agency_logo;
                        @endphp
                        @else
                        @php
                        $logo='default.png';
                        @endphp
                        @endif
                        <img id="agency-logo" src="{{ asset('allupload/' . $logo) }}" style="height: 25px;width: 20px;border-radius: 5px;" alt="Logo">
                       
                    </div> -->
                    {{$statistic->agencyDetail->agency_name}}
                </td>
                <td nowrap>{{$statistic->type}}</td>
                <td nowrap>{{$statistic->first_name}} {{$statistic->middle_name}}  {{$statistic->last_name }}</td>
                <td nowrap>
                <?php
                    if (strtolower($statistic->status) == 'pending') {
                    ?>
                        <label class='badge badge-warning'>Pending</label>
                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'booked') {
                    ?>
                        <label class='badge badge-info'>Booked</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'completed') {
                    ?>
                        <label class='badge badge-success'>Completed</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'cancelled') {
                    ?>
                        <label class='badge badge-danger'>Cancelled</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'noshow') {
                    ?>
                        <label class='badge badge-light'>No Show</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'refused') {
                    ?>
                        <label class='badge badge-danger'>Refused</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'processing') {
                    ?>
                        <label class='badge badge-info'>processing</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'arrived') {
                    ?>
                        <label class='badge badge-primary'>Arrived</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'checkin') {
                    ?>
                        <label class='badge badge-primary'>Mark as ClockIn</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'not interested') {
                    ?>
                        <label class='badge badge-primary'>Not Interested</label>

                    <?php }
                    if (strtolower($statistic->status) == 'hospitalized/rehab') {
                    ?>
                        <label class='badge badge-secondary'>Hospitalized/Rehab</label>

                    <?php }
                    if (strtolower($statistic->status) == 'unabletocontact') {
                    ?>
                        <label class='badge badge-primary'>Unable To Contact</label>

                    <?php } ?>
                    @if ($statistic->status == '1st Attempt - Unable to Contact' || $statistic->status == '2nd Attempt - Unable to Contact' || $statistic->status == '3rd Attempt - Unable to Contact' || $statistic->status == 'Patient Asked to Reschedule'|| $statistic->status == 'New Order Received')
                        <label for="" class='badge badge-info'>{{$statistic->status}}</label>
                    @endif

                    @if ($statistic->status == 'Telehealth Completed' || $statistic->status == 'Telehealth Completed , Pending Forms' || $statistic->status == "Form Completed" || $statistic->status == 'Service Provided')
                        <label for="" class='badge badge-success'>{{$statistic->status}}</label>
                    @endif

                    @if ($statistic->status == 'Patient Deceased' || $statistic->status == 'Appointment was missed' || $statistic->status == 'Appointment Missed' || $statistic->status == 'Closed Temporarily')
                        <label for="" class='badge badge-danger'>{{$statistic->status}}</label>
                    @endif

                    @if ($statistic->status == 'Signed' || $statistic->status == 'Signed & Sent Back to the Agency' || $statistic->status == 'New Form Requested')
                        <label for="" class='badge badge-primary'>{{$statistic->status}}</label>
                    @endif
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
<div class="pull-right pegination-margin static">
    {{ $statisticData->appends(request()->query())->links() }}
</div>