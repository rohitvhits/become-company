<table id="" class="table">
    <thead>
        <tr>
            <th nowrap>ID</th>
            <th nowrap>Agency Name</th>
            <th nowrap>Full Name /Type</th>
            <th nowrap>Birth Date</th>
            <th nowrap>Status</th>
            <th nowrap>Created Date</th>
            <th nowrap>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (count($patientData) > 0)
            @foreach($patientData as $patient)
            <tr>
                <td scope="row" nowrap><?= '#' . '' . $patient->id ?>
</td>
                <td  nowrap>{{$patient->agencyDetail->agency_name}} 
               </td>
                <td nowrap>{{$patient->first_name}} 
                {{$patient->last_name }}<br>{{ $patient->type}}</td>
                <td nowrap>{{date('m/d/Y',strtotime($patient->dob))}}</td>
                <td nowrap>
                <?php
                    if (strtolower($patient->status) == 'pending') {
                    ?>
                        <label class='badge badge-warning'>Pending</label>
                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'booked') {
                    ?>
                        <label class='badge badge-info'>Booked</label>

                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'completed') {
                    ?>
                        <label class='badge badge-success'>Completed</label>

                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'cancelled') {
                    ?>
                        <label class='badge badge-danger'>Cancelled</label>

                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'noshow') {
                    ?>
                        <label class='badge badge-light'>No Show</label>

                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'refused') {
                    ?>
                        <label class='badge badge-danger'>Refused</label>

                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'processing') {
                    ?>
                        <label class='badge badge-info'>processing</label>

                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'arrived') {
                    ?>
                        <label class='badge badge-primary'>Arrived</label>

                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'checkin') {
                    ?>
                        <label class='badge badge-primary'>Mark as ClockIn</label>

                    <?php } ?>
                    <?php

                    if (strtolower($patient->status) == 'not interested') {
                    ?>
                        <label class='badge badge-primary'>Not Interested</label>

                    <?php }
                    if (strtolower($patient->status) == 'hospitalized/rehab') {
                    ?>
                        <label class='badge badge-secondary'>Hospitalized/Rehab</label>

                    <?php }
                    if (strtolower($patient->status) == 'unabletocontact') {
                    ?>
                        <label class='badge badge-primary'>Unable To Contact</label>

                    <?php } ?>
                    @if ($patient->status == '1st Attempt - Unable to Contact' || $patient->status == '2nd Attempt - Unable to Contact' || $patient->status == '3rd Attempt - Unable to Contact' || $patient->status == 'Patient Asked to Reschedule' || $patient->status == 'New Order Received')
                        <label for="" class='badge badge-info'>{{$patient->status}}</label>
                    @endif

                    @if ($patient->status == 'Telehealth Completed' || $patient->status == 'Telehealth Completed , Pending Forms' || $patient->status == 'Form Completed' || $patient->status == 'Service Provided')
                        <label for="" class='badge badge-success'>{{$patient->status}}</label>
                    @endif

                    @if ($patient->status == 'Patient Deceased' || $patient->status == 'Appointment was missed' || $patient->status == 'Appointment Missed' || $patient->status == 'Closed Temporarily')
                        <label for="" class='badge badge-danger'>{{$patient->status}}</label>
                    @endif

                    @if ($patient->status == 'Signed' || $patient->status == 'Signed & Sent Back to the Agency' || $patient->status == 'New Form Requested')
                        <label for="" class='badge badge-primary'>{{$patient->status}}</label>
                    @endif
                </td>
                <td nowrap>
                        {{ date('m/d/Y h:i A',strtotime($patient->created_date))}}
                </td>
                <td nowrap>
                    <a class="btn btn-outline-primary" href="<?php echo URL::to('/'); ?>/patient-edit-with-sms/<?php echo sha1($patient->id); ?>" target="_blank">View</a>
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
        @endif           
    </tbody>
</table>
<div class="pull-right pegination-margin">
    
</div>
