    @php
    $auth = auth()->user();
    @endphp
    <div class="row">
        <div class="col-12 ">
            <div class="table-responsive tableData">
                <table id="order-listing1" class="table table-bordered table-width1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="no_warp">Portal ID</th>
                            <th>Status</th>
                            <th>Portal Status</th>
                            <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                                <th class="no_warp">Agency Name</th>
                            <?php } ?>
                            <th>Type/Discipline</th>
                            <th class="no_warp">Code </th>
                            <th class="no_warp">Name/Mobile/DOB/Services </th>
                            <th class="no_warp">Assigned To</th>
                            <th class="no_warp">Due Date</th>
                            <th class="no_warp">Appointment Date - Location</th>
                            <th>Created Date</th>
                            <th>FU Date</th>
                            <th nowrap>In Service Date</th>
                            <th nowrap>Completed Date</th>
                            <th nowrap>Follow Up Date</th>
                            @if($auth->agency_fk ==106 || $auth->id ==482)
                            <th nowrap>Training Due Date</th>
                            @endif
                            @if (in_array($user->user_type_fk, [3, 184]))
                                <th nowrap>Training Status</th>
                                <th nowrap>Last Status Updated Date / Last Status Updated By</th>
                                <th nowrap>Referral Type</th>
                            @endif

                            <th nowrap>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        if (count($query) > 0) {
                            $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                            foreach ($query as $row) {  ?>
                                <tr>
                                    <td>
                                        <div>
                                            <a target="_blank"
                                                href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->patient_id; ?>"><?= '#' . '' . $row->id ?></a>
                                        </div>
                                        @if ($row->patient->record_read == 0)
                                        <div style="position:relative"><span class="add_new_record left_record">New</span>
                                        </div>
                                        @endif

                                    </td>
                                    <td><a target="_blank"
                                            href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->patient_id; ?>"><?= '#' . '' . $row->patient_id ?></a></td>
                                    <td>

                                        <?php
                                        if ($row->status == 'Pending' || $row->status == 'pending') {
                                        ?>
                                            <label class='badge badge-warning'>Pending</label>

                                        <?php } ?>
                                        <?php

                                        if (strtolower($row->status) == 'booked') {
                                        ?>
                                            <label class='badge badge-info'>Booked</label>

                                        <?php } ?>
                                        <?php

                                        if (strtolower($row->status) == 'completed' || strtolower($row->status) == 'complete') {
                                        ?>
                                            <label class='badge badge-success'>Completed</label>

                                        <?php } ?>
                                        <?php

                                        if ($row->status == 'in process') {
                                        ?>
                                            <label class='badge badge-secondary'>In process</label>

                                        <?php } ?>

                                        <?php
                                        if ($row->status == 'cancelled' or $row->status == 'no show' or  $row->status == 'no answer' or $row->status == 'unable to contact') {
                                        ?>
                                            <label class='badge badge-danger'>Cancelled</label>

                                        <?php } ?>
                                        <?php

                                        if ($row->status == 'noshow') {
                                        ?>
                                            <label class='badge badge-light'>No Show</label>

                                        <?php } ?>
                                        <?php

                                        if ($row->status == 'arrived') {
                                        ?>
                                            <label class='badge badge-primary'>Arrived</label>

                                        <?php } ?>
                                        <?php

                                        if ($row->status == 'processing') {
                                        ?>
                                            <label class='badge badge-secondary'>Processing</label>

                                        <?php }
                                        if ($row->status == 'refused') { ?>
                                            <label class='badge badge-danger'>Refused</label>
                                        <?php }
                                        if ($row->status == 'hospitalized/rehab') { ?>
                                            <label class='badge badge-info'>Hospitalized/Rehab</label>
                                        <?php }
                                        if ($row->status == 'Pending Termination') { ?>
                                            <label class='badge badge-danger'>Pending Termination</label>
                                        <?php }
                                        if ($row->status == 'On Hold') { ?>
                                            <label class='badge badge-secondary'>On Hold</label>
                                        <?php }
                                        if ($row->status == 'On Leave') { ?>
                                            <label class='badge badge-info'>On Leave</label>
                                        <?php }
                                        if ($row->status == 'Terminated') { ?>
                                            <label class='badge badge-danger'>Terminated</label>
                                        <?php }
                                        if ($row->status == 'unableToContact') { ?>

                                            <label class='badge badge-danger'>Unable To Contact</label>
                                        <?php } ?>

                                        <?php if ($row->status == 'In Service') { ?>

                                            <label class='badge badge-primary'>In Service</label>
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

                                        @if ($row->status == 'Signed' || $row->status == 'Signed & Sent Back to the Agency'  || $row->status == 'New Form Requested' )
                                            <label for="" class='badge badge-primary'>{{$row->status}}</label>
                                        @endif
                                        @if ($row->status == 'inactive' )
                                            <label for="" class='badge badge-danger'>{{ucfirst($row->status)}}</label>
                                        @endif
                                        <br>
                                        {{ $row->reason_name}} 
                                        @if(!empty($row->other_reason))
                                            <i class="fa fa-info-circle ml-1" style="cursor: pointer; color: #17a2b8;" data-toggle="tooltip" data-placement="top" title="{{ $row->other_reason }}"></i>
                                        @endif
                                    </td>

                                    <td>
                                        <?php
                                        if ($row->patient->status == 'Pending' || $row->patient->status == 'pending') {
                                        ?>
                                            <label class='badge badge-warning'>Pending</label>

                                        <?php } ?>
                                        <?php

                                        if (strtolower($row->patient->status) == 'booked') {
                                        ?>
                                            <label class='badge badge-info'>Booked</label>

                                        <?php } ?>
                                        <?php

                                        if ($row->patient->status == 'completed') {
                                        ?>
                                            <label class='badge badge-success'>Completed</label>

                                        <?php } ?>

                                        <?php

                                        if ($row->patient->status == 'in process') {
                                        ?>
                                            <label class='badge badge-secondary'>In process</label>

                                        <?php } ?>
                                        <?php
                                        if ($row->patient->status == 'cancelled' or  $row->patient->status == 'refuese' or $row->patient->status == 'no show' or  $row->patient->status == 'no answer' or $row->patient->status == 'unable to contact') {
                                        ?>
                                            <label class='badge badge-danger'>Cancelled</label>

                                        <?php } ?>
                                        <?php

                                        if ($row->patient->status == 'noshow') {
                                        ?>
                                            <label class='badge badge-light'>No Show</label>

                                        <?php } ?>
                                        <?php

                                        if ($row->patient->status == 'arrived') {
                                        ?>
                                            <label class='badge badge-primary'>Arrived</label>

                                        <?php } ?>
                                        <?php

                                        if ($row->patient->status == 'processing') {
                                        ?>
                                            <label class='badge badge-secondary'>Processing</label>

                                        <?php }
                                        if ($row->patient->status == 'refused') { ?>
                                            <label class='badge badge-danger'>Refused</label>
                                        <?php }
                                        if ($row->patient->status == 'hospitalized/rehab') { ?>
                                            <label class='badge badge-info'>Hospitalized/Rehab</label>
                                        <?php }
                                        if ($row->patient->status == 'Pending Termination') { ?>
                                            <label class='badge badge-danger'>Pending Termination</label>
                                        <?php }
                                        if ($row->patient->status == 'On Hold') { ?>
                                            <label class='badge badge-secondary'>On Hold</label>
                                        <?php }
                                        if ($row->patient->status == 'On Leave') { ?>
                                            <label class='badge badge-info'>On Leave</label>
                                        <?php }
                                        if ($row->patient->status == 'Terminated') { ?>
                                            <label class='badge badge-danger'>Terminated</label>
                                        <?php }
                                        if ($row->patient->status == 'unableToContact') { ?>

                                            <label class='badge badge-danger'>Unable To Contact</label>
                                        <?php } ?>

                                        <?php if ($row->patient->status == 'In Service') { ?>

                                            <label class='badge badge-primary'>In Service</label>
                                        <?php } ?>
                                        @if ($row->patient->status == '1st Attempt - Unable to Contact' || $row->patient->status == '2nd Attempt - Unable to Contact' || $row->patient->status == '3rd Attempt - Unable to Contact' || $row->patient->status == 'Patient Asked to Reschedule')
                                            <label for="" class='badge badge-info'>{{$row->patient->status}}</label>
                                        @endif

                                        @if ($row->patient->status == 'Telehealth Completed' || $row->patient->status == 'Telehealth Completed , Pending Forms' || $row->patient->status == 'Form Completed' || $row->patient->status == 'Service Provided')
                                            <label for="" class='badge badge-success'>{{$row->patient->status}}</label>
                                        @endif

                                        @if ($row->patient->status == 'Patient Deceased' || $row->patient->status == 'Appointment was missed' || $row->patient->status == 'Closed Temporarily')
                                            <label for="" class='badge badge-danger'>{{$row->patient->status}}</label>
                                        @endif

                                        @if ($row->patient->status == 'Signed' || $row->patient->status == 'Signed & Sent Back to the Agency')
                                            <label for="" class='badge badge-primary'>{{$row->patient->status}}</label>
                                        @endif
                                        @if (strtolower($row->patient->status) == 'inactive')
                                            <label for="" class='badge badge-danger'>{{ucfirst($row->patient->status)}}</label>
                                        @endif
                                        <br>
                                        {{ $row->reason_name}}

                                    </td>
                                    <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                                        <td><?= $row->patient->agencyDetail->agency_name ?></td>
                                    <?php } ?>
                                    <td><?php echo $row->patient->type; ?>
                                        <br />
                                        <?php echo $row->patient->diciplin; ?>
                                        <br />
                                        @if ($row->patient->location_branch != '')
                                        <p class="text-muted" style="font-size:10px">
                                            ({{ $row->patient->location_branch }})</p>
                                        @endif


                                    </td>
                                    <td>{{ $row->patient->patient_code }}</td>
                                    <td>
                                        <?php echo $row->patient->first_name . ' ' . $row->patient->last_name; ?><br />
                                        <?php echo $row->patient->mobile; ?><br />
                                        <?php if (isset($row->patient->dob) && $row->patient->dob != '0001-01-01' && $row->patient->dob != '1000-01-01') {
                                            echo date('m/d/Y', strtotime($row->patient->dob));
                                        } ?> (<?php echo $row->patient->gender; ?>)<br />
                                        @foreach ($row->patientServiceRequestRelationShip as $data)
                                        {{ $data->requestService->name??"" }}
                                        @endforeach <br />
                                    </td>
                                    <td>{{ $row->patient->assignToUser!=null && isset($row->patient->assignToUser->users) ? $row->patient->assignToUser->users->full_name : 'N/A' }}</td>
                                    <td>
                                        
                                        @if ($row->patient->due_date != '')
                                        <?php if ($row->patient->due_date != '1969-12-31' && $row->patient->due_date != '0000-00-00' ) {
                                            echo date('m/d/Y h:i A', strtotime($row->patient->due_date));
                                        } ?>
                                        @endif
                                    </td>

                                    <td>
                                        @if (strtolower($row->patient->type) == 'caregiver')
                                        <?php if ($row->patient->appointment_date != '') {
                                            echo date('m/d/Y', strtotime($row->patient->appointment_date));
                                        } ?>
                                        @endif
                                        @if (strtolower($row->patient->type) == 'patient')
                                        @if ($row->appointment_date != '')
                                        {{ date('m/d/Y h:i A', strtotime($row->patient->appointment_date)) }}
                                        @endif
                                        @endif

                                    </td>

                                    <td><?php echo date('m/d/Y h:i A', strtotime($row->created_at)); ?><br />
                                    @if(isset($row->userDetails->first_name))
                                    {{ $row->userDetails->first_name . '' . $row->userDetails->last_name }} 
                                    @endif
                                       
                                    </td>
                                    <td>
                                        @if ($row->fu_date != '')
                                        {{ $row->patient->fu_date != '' && $row->patient->fu_date != '1969-12-31' ? date('m/d/Y', strtotime($row->patient->fu_date)) : null }}
                                        <br />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row->patient->inservice_datetime != '')
                                        {{ $row->patient->inservice_datetime != '' && $row->patient->inservice_datetime != '1969-12-31' ? date('m/d/Y  h:i A', strtotime($row->patient->inservice_datetime)) : null }}
                                        <br />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row->completed_date != '')
                                        {{ $row->completed_date != '' && $row->completed_date != '1969-12-31' ? date('m/d/Y  h:i A', strtotime($row->completed_date)) : null }}
                                        <br />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row->patient->follow_date != '')
                                        {{ $row->patient->follow_date != '' && $row->patient->follow_date != '1969-12-31' ? date('m/d/Y  h:i A', strtotime($row->patient->follow_date)) : null }}
                                        <br />
                                        @endif
                                    </td>
                                    @if ($auth->agency_fk == 106 || $auth->id == 482)

                                    <td>
                                        @if ($row->patient->traning_due_date != '')
                                        {{ $row->patient->traning_due_date != '' && $row->patient->traning_due_date != '1969-12-31' ? date('m/d/Y', strtotime($row->patient->traning_due_date)) : null }}
                                        <br />
                                        @endif
                                    </td>
                                    @endif
                                    @if (in_array($user->user_type_fk, [3, 184]))
                                    <td>
                                        @if ($row->patient->training_status != '')
                                        {{ $row->patient->training_status != '' ? $row->patient->training_status : 'N/A' }}
                                        @endif
                                    </td>
                                    <td >
                                                @if($row->last_status_update !='' && $row->last_status_update != "0000-00-00 00:00:00")

                                                {{ date('m/d/Y',strtotime($row->last_status_update))}}
                                                @endif
                                                <br>
                                                @if(isset($row->statusUserDetails->id))
                                                {{$row->statusUserDetails->first_name.' '.$row->statusUserDetails->last_name}}
                                                @endif
                                            </td>
                                            <td>
                                        @if($row->patient->referral_type !="")
                                        {{ ucfirst($row->patient->referral_type)}}
                                        @else
                                            @if($row->patient->hha_id !="" || $row->patient->link_hha_caregiver !="" || $row->patient->link_hha_patient !="")
                                            HHA Exchange
                                                @elseif($row->patient->alaycare_id !="")
                                                Alayacare
                                                @elseif($row->patient->robort_id !="")
                                                Remote Focus
                                                @elseif($row->patient->platform_type =="VA")
                                                Visiting Aid
                                                @endif
                                        @endif
                                        </td>
                                           
                                    @endif
                                    <td>
                                        @if ($row->patient->archived_at != '')
                                        <a title="Unarchive" href="javascript:void(0)"
                                            onclick="getUnArchiveById(<?php echo $row->id; ?>)"><i
                                                class="fa fa-file-archive-o"></i></a>
                                        @else
                                        <a title="Archive" href="javascript:void(0)"
                                            onclick="getArchiveById(<?php echo $row->id; ?>)"><i class="fa fa-archive"></i></a>
                                        @endif

                                        <a href="javascript:void(0)" data-toggle="modal"
                                            data-target="#serviceByPatientTypeModal"
                                            onclick="getPatientId('<?php echo $row->id; ?>','{{ $row->patient->type }}')">Request
                                            Service</a>


                                        <?php

                                        if (strtolower($row->patient->type) == 'caregiver') {
                                            if ($row->patient->status == 'Pending') { ?>
                                                <a href="javascript:void(0)" onclick="getSendSMS(<?php echo $row->id; ?>)">Send
                                                    SMS</a>
                                            <?php } else if ($row->patient->status == 'booked') { ?>
                                                <a href="javascript:void(0)"
                                                    onclick="getRemainderSendSMS(<?php echo $row->id; ?>)">Reminder SMS</a>
                                        <?php }
                                        }
                                        ?>
                                        @if ($auth->user_type_fk == 184)
                                        <a href="javascript:void(0)" data-toggle="modal"
                                            data-target="#serviceByPatientTypeModal"
                                            onclick="getPatientId('<?php echo $row->id; ?>','{{ $row->patient->type }}','{{ $row->patient->agency_id }}')">Request
                                            Service</a>
                                        @endif
                                    </td>
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
            </div>
        </div>
    </div>
    <script>
        $('#service_request_count').html("{{ $query->total() }}");
    </script>