<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
        <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                               <th> <input type="checkbox" id="cboxId"></th>
                                <?php } ?>
            <th>ID</th>
            <th class="no_warp">SMS</th>
            <th>Status</th>
            <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                <th class="no_warp">Agency Name</th>
            <?php } ?>
            <th>Type/Discipline</th>
            <th class="no_warp">Patient Code </th>
            <th class="no_warp">Name/Mobile/DOB</th>
            <th class="no_warp">Assigned To</th>
            <th class="no_warp">Due Date</th>
           
            <th>Created Date</th>
            <th>FU Date</th>
           
            @if($auth->agency_fk ==106 || $auth->id ==482)
            <th nowrap>Training Due Date</th>

            @endif
           
        </tr>
    </thead>
    <tbody>

        <?php
        $flag = 0;
        if (count($query) > 0) {
            $i = 1 + (($query->currentPage() - 1) * $query->perPage());

            foreach ($query as $row) {
                $flag = 0;
                if ($row->hha_id != "") {
                    $flag = 1;
                } else {
                    if ($row->type == 'Caregiver') {
                        if ($row->link_hha_caregiver != "") {
                            $flag = 1;
                        }
                    }
                    if ($row->type == 'Patient') {
                        if ($row->link_hha_patient != "") {
                            $flag = 1;
                        }
                    }
                }
                $appointmentFlagClasss = '';
                if ($row->flag == '1') {
                    $appointmentFlagClasss = "pale-yellow-color";
                }
                if ($row->flag == '0') {
                    $appointmentFlagClasss = '';
                }
        ?>
                <tr class="{{$appointmentFlagClasss}}">
                    <td><input type="checkbox" class="form-check-input cbox ml-0" value="{{ $row->id }}"></td>
                    <td>
                        <div>
                            <a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>"><?= '#' . '' . $row->id ?></a>
                        </div>
                        @if($row->record_read ==0)
                        <div style="position:relative"><span class="add_new_record left_record">New</span></div>
                        @endif
                        @if($flag ==1)
                        <img src="{{ asset('/img/hha.png')}}" title="HHA" alt="HHA" style="height: 15px; width: 15px;">
                        @endif

                    </td>
                    <td><?php if ($row->patient_sms_flag == 1) {
                            echo "<span class='badge badge-success'>Sent</span>";
                        } else {
                            echo "<span class='badge badge-warning'>Pending</span>";
                        } ?></td>
                    <td>
                        <?php

                        if (strtolower($row->status) == 'pending') {
                        ?>
                            <label class='badge badge-warning'>Pending</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'booked') {
                        ?>
                            <label class='badge badge-info'>Booked</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'completed') {
                        ?>
                            <label class='badge badge-success'>Completed</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'cancelled' || strtolower($row->status) == 'pending termination') {
                        ?>
                            <label class='badge badge-danger'>{{ $row->status}}</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'noshow') {
                        ?>
                            <label class='badge badge-secondary'>No Show</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'refused' || strtolower($row->status) == 'terminated') {
                        ?>
                            <label class='badge badge-danger'>{{ $row->status}}</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'processing' || strtolower($row->status) == 'on leave') {
                        ?>
                            <label class='badge badge-info'>{{ $row->status}}</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'arrived') {
                        ?>
                            <label class='badge badge-primary'>Arrived</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'checkin') {
                        ?>
                            <label class='badge badge-primary'>Mark as ClockIn</label>

                        <?php } ?>
                        <?php

                        if (strtolower($row->status) == 'not interested') {
                        ?>
                            <label class='badge badge-primary'>Not Interested</label>

                        <?php }
                        if (strtolower($row->status) == 'hospitalized/rehab') {
                        ?>
                            <label class='badge badge-secondary'>Hospitalized/Rehab</label>

                        <?php }
                        if (strtolower($row->status) == 'unabletocontact') {
                        ?>
                            <label class='badge badge-primary'>Unable To Contact</label>

                        <?php } ?>

                        <?php
                        if (strtolower(trim($row->status)) == 'on hold') { ?>
                            <label class='badge badge-secondary'>On Hold</label>
                        <?php } ?>
                        @if ($row->status == '1st Attempt - Unable to Contact' || $row->status == '2nd Attempt - Unable to Contact' || $row->status == '3rd Attempt - Unable to Contact' || $row->status == 'Patient Asked to Reschedule' || $row->status == 'New Order Received')
                            <label for="" class='badge badge-info'>{{$row->status}}</label>
                        @endif

                        @if ($row->status == 'Telehealth Completed' || $row->status == 'Telehealth Completed , Pending Forms' || $row->status == 'Form Completed' || $row->status == 'Service Provided')
                            <label for="" class='badge badge-success'>{{$row->status}}</label>
                        @endif

                        @if ($row->status == 'Patient Deceased' || $row->status == 'Appointment was missed' || $row->status == 'Appointment Missed' || $row->status == 'Closed Temporarily')
                            <label for="" class='badge badge-danger'>{{$row->status}}</label>
                        @endif

                        @if ($row->status == 'Signed' || $row->status == 'Signed & Sent Back to the Agency' || $row->status == 'New Form Requested')
                            <label for="" class='badge badge-primary'>{{$row->status}}</label>
                        @endif
                        @if (strtolower($row->status) == 'inactive')
                            <label for="" class='badge badge-danger'>{{ ucfirst($row->status)}}</label>
                        @endif
                    </td>
                    <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                        <td><?= $row->agency_name ?> </td>
                    <?php } ?>

                    <td><?php echo $row->type; ?>
                        <br />
                        <?php echo $row->diciplin; ?>
                        <br />
                        @if($row->location_branch !="")
                        <p class="text-muted" style="font-size:10px">({{ $row->location_branch}})</p>
                        @endif


                    </td>
                    <td>{{ $row->patient_code}}</td>
                    <td>
                        <?php echo $row->first_name . ' ' . $row->last_name; ?><br />
                        <?php echo $row->mobile; ?><br />
                        <?php if (isset($row->dob) && $row->dob != '0001-01-01' && $row->dob != '1000-01-01') {
                            echo date('m/d/Y', strtotime($row->dob));
                        } ?> (<?php echo $row->gender; ?>)<br />

                        
                    </td>
                    <td>{{ $row->assignToUser!=null && isset($row->assignToUser->users) ? $row->assignToUser->users->full_name : 'N/A' }}</td>
                    

                    <td><?php 
                        if($row->due_date !=null && $row->due_date !="0000-00-00" && $row->due_date !="1969-12-31"){
                            echo date('m/d/Y', strtotime($row->due_date));
                        }
                    ?>
                    <td><?php echo date('m/d/Y h:i A', strtotime($row->created_date)); ?><br />
                        {{$row->created_by_username}}
                    </td>
                    <td> @if($row->fu_date !=null && $row->fu_date !="0000-00-00" && $row->fu_date !="1969-12-31")

                        {{($row->fu_date !='' && $row->fu_date!='1969-12-31') ? date('m/d/Y', strtotime($row->fu_date)) : null}} <br />
                        @endif
                    </td>
                    
                    @if($auth->agency_fk ==106 || $auth->id ==482)

                    <td>
                        @if($row->traning_due_date!='')

                        {{($row->traning_due_date !='' && $row->traning_due_date!='1969-12-31') ? date('m/d/Y', strtotime($row->traning_due_date)) : null}} <br />
                        @endif
                    </td>
                    @endif
                   
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="20">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div class="pull-right pegination-margin">
    {{ $query->links() }}
</div>

<script>
    $('#totalCount').html('{{ $totalCount[0]->count}}');
    </script>