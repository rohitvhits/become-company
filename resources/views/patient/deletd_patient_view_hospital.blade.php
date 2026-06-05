@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assetsd/css/vertical-layout-light/jquery.timepicker.css" rel="stylesheet" type="text/css">

<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ asset('assets/css/tribute.css') }}" rel="stylesheet" type="text/css">

@if($record->alaycare_id !="")
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/css/custom.css?time={{ env('timestamp')}}">

@endif
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/modulejs/css/patient.css?time={{ env('timestamp')}}">
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
    rel="stylesheet" />
<link href="{{  asset('assets/modulejs/css/task.css') }}?time={{ env('timestamp')}}" rel="stylesheet">

<!--main-container-part-->
<div class="main-panel">
    <div class="content-wrapper">

        <div class="dashboard-header d-flex flex-column ">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom  mb-3">
            @php $class = ''; @endphp 
                @if($record->flag == 1)
                @php $class = 'highlight-patient-appointment badge badge-outline-danger'; @endphp
                @endif
                <div class="d-flex align-items-center mb-3 {{$class}}">
                    <h4 class="mb-0 font-weight-bold">ID #
                        <?= $record->id . ' - ' . ucwords($record->first_name) . ' ' . ucwords($record->last_name) . ' ' ?>
                    </h4> &nbsp;&nbsp;<?php echo $record->phone; ?> ( <?php echo $record->agency_name; ?> ) <?php if ($record->partner_agency != "") { ?>- (<?php echo $record->partner_agency; ?>) <?php } ?>

                @can('send-patient-demographic-sms')
                @if($record->demographic_updated_flag ==0)
                <a href="javascript:void(0)" class="ml-4" onclick="sendPatientDemographicSMS('{{ $record->mobile}}')">Send To Patient Demographic Details</a>
                @endif
                @endcan
                <?php

                if (isset($record->record_id) && $record->record_id != '') {
                ?>

                    <span class="badge badge-primary">Expert Medicaid Consultancy</span>
                <?php } ?>
                </div>

                <?php if ($user['user_type_fk'] == 184) { ?>
                    
                <?php } ?>
            </div>
        </div>

        <?php $serviceArr = explode(',', $record->service_id);
        ?>
        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body mini-card">

                        <div class="row">

                            <div class="profile-feed col-12 pull-right" id="edit_medical">
                                <div class="row">
                                    <div class="col-md-12" style="margin-bottom: 15px;">
                                        <h6 class="card-title">Appointment Details


                                           
                                        </h6>
                                    </div>

                                </div>

                                <div class="row" id="hr2">
                                    <div class="col-md-4">
                                        <div class="box">
                                            <div class="title  mb-3">
                                                <h5>Basic Detail</h5>
                                            </div>
                                            <dl class="dl-horizontal">
                                                <dt> Patient Code</dt>
                                                <dd> <?php echo $record->patient_code . '<br>'; ?></dd>
                                                <dt> First Name</dt>
                                                <dd> <?php echo $record->first_name . '<br>'; ?></dd>
                                                <dt> Middle Name </dt>
                                                <dd> <?php if (isset($record->middle_name) && $record->middle_name != '') {
                                                            echo $record->middle_name;
                                                        } else {
                                                            echo 'N/A';
                                                        } ?>&nbsp; </dd>
                                                <dt> Last Name</dt>
                                                <dd> <?php if (isset($record->last_name) && $record->last_name != '') {
                                                            echo $record->last_name . '<br>';
                                                        } else {
                                                            echo 'N/A';
                                                        } ?></dd>
                                                <dt> Gender</dt>
                                                <dd> <?php if (isset($record->gender) && $record->gender != '') {
                                                        $otherName="";
                                                        if($record->gender =='other'){
                                                            $otherName=" (".$record->other_gender.")";
                                                        }
                                                            echo ucfirst($record->gender) . $otherName.'<br>';
                                                        } else {
                                                            echo 'N/A';
                                                        } ?>
                                                     
                                                    </dd>
                                                <dt> Mobile</dt>
                                                <dd> <span  id="record_mobile_id"><?php echo $record->mobile ; ?></span>
                                               
                                            </dd>
                                                <dt> Phone</dt>
                                                <dd> <span  id="record_phone_id"><?php echo $record->phone; ?></span>
                                                    
                                                </dd>
                                                <dt> Country</dt>
                                                <dd> <?php echo $record->county == null ? 'N/A' : $record->county . '<br>'; ?></dd>
                                                <dt> State</dt>
                                                <dd> <?php echo $record->state . '<br>'; ?></dd>
                                                <dt> City</dt>
                                                <dd> <?php echo $record->city . '<br>'; ?></dd>
                                                <dt> Address1</dt>
                                                <dd> <?php echo $record->address1 . '<br>'; ?></dd>

                                                <dt> Apt/Suite/Floor</dt>
                                                <dd> <?php echo $record->address2 . '<br>'; ?></dd>
                                                <dt> Zipcode</dt>
                                                <dd> <?php echo $record->zip_code . '<br>'; ?></dd>
                                                <dt> Date of Birth</dt>
                                                <dd><span id="patient_dob"><?php if ($record->dob != '0000-00-00') {
                                                            echo Common::convertMDY($record->dob);
                                                        } else {
                                                            echo '';
                                                        } ?>
                                                        </span>
                                                       
                                                <?php
                                                if ($record->type    == 'Caregiver') {
                                                    if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                        <dt>Emergency Phone </dt>
                                                        <dd><span id="emergency_phones"><?php if ($record->emergency_phone != '') {
                                                                                            echo $record->emergency_phone;
                                                                                        } ?></span>
                                                          
                                                        </dd>

                                                        <dt>Email </dt>
                                                        <dd><span id="emergency_email"><?php if ($record->email != '') {
                                                                                            echo $record->email;
                                                                                        } ?></span>
                                                            
                                                        </dd>
                                                <?php  }
                                                } ?>

                                                <?php if ($user['user_type_fk'] == 184) { ?>
                                                    <dt>Email </dt>
                                                    <dd><span id="emergency_email"><?php if ($record->email != '') {
                                                                                        echo $record->email;
                                                                                    } ?></span>
                                                        
                                                    </dd>
                                                <?php } ?>
                                                <dt>Insurance ID </dt>
                                                <dd><span><?php if ($record->insurance_id != '') {
                                                                echo $record->insurance_id;
                                                            } ?></span>

                                                </dd>
                                                <dt>Insurance Name </dt>
                                                <dd><span>

                                                        <?php
                                                        $otherName = "";
                                                        if ($record->insuranceName == 'other') {
                                                            $otherName = '( ' . $record->other_insurance_name . ')';
                                                        }
                                                        if ($record->insuranceName != '') {
                                                            echo $record->insuranceName . ' ' . $otherName;
                                                        }


                                                        ?>

                                                    </span>

                                                </dd>


                                            </dl>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="title mb-3">
                                            <h5>Appointment Detail</h5>
                                        </div>
                                        <dl class="dl-horizontal">
                                            <dt> Appointment Type</dt>
                                            <dd> <?php echo $record->type . '<br>'; ?></dd>
                                            <dt> Appointment Date</dt>
                                            <dd>&nbsp;&nbsp;<?php echo Common::convertMDY($record->appointment_date) . '<br>'; ?></dd>
                                            <dt> Appointment Time</dt>
                                            <?php if ($record->type == 'Caregiver' && $record->start_time) {
                                            ?>
                                                <dd> <?php echo date('h:i A', strtotime($record->start_time)) . ' - ' . date('h:i A', strtotime($record->edate)) . '<br>'; ?></dd>
                                            <?php } else { ?>
                                                <dd>
                                                    @if($record->appointment_date!='')
                                                    <?php echo date('h:i A', strtotime($record->appointment_date)) . '<br>'; ?>
                                                    @endif
                                                </dd>

                                            <?php } ?>
                                            <dt>Booked Via</dt>
                                            <dd style="white-space:nowrap"><?php echo ucfirst($record->appointment_mode); ?> .</dd>
                                            <?php if ($record->agency_id  == '106') { ?>

                                                <dt> Payment</dt>
                                                <dd> <?php echo $record->hamaspik_payment == 1 ? 'Hamaspik 1' : 'Hamaspik 2<br>'; ?></dd><br>
                                            <?php } ?>
                                            <dt>Telehealth Appointment </dt>
                                            <dd style="white-space:nowrap"><?php if ($record->telehealth_date_time != '') {
                                                                                echo date('m/d/Y h:i A', strtotime($record->telehealth_date_time));
                                                                            } ?> <br>
                                            </dd>
                                            <?php if ($record->agency_id  != '319' && $record->agency_id  != '106') { ?>
                                                <dt>FU Date </dt>
                                                <dd style="white-space:nowrap"><?php if ($record->fu_date != '' &&  $record->fu_date != '1969-12-31') {
                                                                                    echo date('m/d/Y h:i A', strtotime($record->fu_date));
                                                                                } else {
                                                                                    echo 'N/A';
                                                                                } ?> <br>
                                                </dd>

                                            <?php } ?>
                                            <dt> Discipline</dt>
                                            <dd> <?php echo $record->diciplin . '<br>'; ?></dd>
                                            <dt> Service</dt>
                                            <dd> <?php if (isset($record->service) && $record->service != '') {
                                                        echo $record->service . '<br>';
                                                    } else {
                                                        echo 'N/A';
                                                    } ?></dd>
                                            <dt> Attachment</dt>
                                            <dd>
                                                <span id="attachment_pdf_ids">
                                                    
                                                </span>
                                                
                                            </dd>


                                            <dt>Payment Type</dt>
                                            <dd style="white-space:nowrap"><span id="payment_type_id">{{ $record->payment_type_new }}
                                                </span>&nbsp;&nbsp;
                                            </dd>
                                            <?php if ($record->agency_id  != '319'  && $record->agency_id  != '106') { ?>

                                                <dt>Next Appointment Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</dt>
                                                <dd id="next_apid"><?php if ($record->next_appoinment_date != '') {
                                                                        echo date('m/d/Y', strtotime($record->next_appoinment_date));
                                                                    } ?>
                                                    
                                                </dd>

                                            <?php  } ?>
                                            <dt>Medical Due Date </dt>
                                            <dd><?php if ($record->due_date != '' && $record->due_date != '1969-12-31') {
                                                    echo date('m/d/Y', strtotime($record->due_date));
                                                } ?>
                                                
                                            </dd>

                                            <?php if ($record->type    == 'Caregiver') {
                                                if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                    <dt>Training Due Date </dt>
                                                    <dd><?php
                                                        $traning_due_date = "";
                                                        if ($record->traning_due_date != '' && $record->traning_due_date != '1969-12-31') {
                                                            $traning_due_date = date('m/d/Y', strtotime($record->traning_due_date));
                                                            echo $traning_due_date;
                                                        } ?>
                                                        
                                                    </dd>
                                            <?php  }
                                            } ?>
                                            <dt>Portal </dt>
                                            <dd>
                                                @if(isset($record->platform_id) && $record->platform_id !='')
                                                API Portal
                                                @else
                                                Admin Portal
                                                @endif
                                            </dd>

                                            <dt>Call Note Count </dt>
                                            <dd>
                                                {{$record->callCounter}}
                                            </dd>

                                            <dt>Emergency Contact Name </dt>
                                            <dd>
                                                @if($record->emergency_contact_name !=""){{$record->emergency_contact_name}} @else - @endif
                                            </dd>
                                            @if ($record->agency_id != '319' || $record->agency_id != '106')
                                            <dt>Emergency Contact Number </dt>
                                            <dd>
                                                @if($record->emergency_phone !=""){{$record->emergency_phone}} @else - @endif
                                            </dd>
                                            @endif
                                            <dt>SSN </dt>
                                            <dd><span><?php if ($record->ssn != '') {
                                                            echo $record->ssn;
                                                        } ?></span>

                                            </dd>
                                            <dt>CIN /Medicaid Number</dt>
                                            <dd><span><?php if ($record->cin != '') {
                                                            echo $record->cin;
                                                        } ?></span>

                                            </dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="title mb-3">
                                            <h5></h5>
                                        </div>
                                        <dl class="dl-horizontal">
                                            <?php if ($record->type == 'Caregiver') { ?>
                                                <dt>Location</dt>
                                                <dd><?php echo $record->locations != "" ? $record->locations->address1 : '' . '<br>'; ?></dd>
                                            <?php }
                                            ?>

                                            <?php if ($record->completed_date != '') { ?>
                                                <dt>Completed Date</dt>
                                                <dd><span id="comp_id"><?php if ($record->completed_date != '') {
                                                                            echo date('m/d/Y', strtotime($record->completed_date));
                                                                        } ?> </span>
                                                </dd>
                                            <?php } ?>

                                            <dt> Status</dt>
                                            <dd> <?php
                                                    if ($record->status == 'Pending' || $record->status == 'pending') {
                                                    ?>
                                                    <label class='badge badge-warning'>Pending</label>

                                                <?php } ?>
                                                <?php

                                                if (strtolower($record->status) == 'booked') {
                                                ?>
                                                    <label class='badge badge-info'>Booked</label>

                                                <?php } ?>
                                                <?php

                                                if ($record->status == 'completed') {
                                                ?>
                                                    <label class='badge badge-success'>Completed</label>

                                                <?php } ?>
                                                <?php

                                                if ($record->status == 'in process') {
                                                ?>
                                                    <label class='badge badge-secondary'>In process</label>

                                                <?php } ?>
                                                <?php
                                                if ($record->status == 'cancelled' or  $record->status == 'refuese' or $record->status == 'no show' or  $record->status == 'no answer' or $record->status == 'unable to contact') {
                                                ?>
                                                    <label class='badge badge-danger'>Cancelled</label>

                                                <?php } ?>
                                                <?php

                                                if ($record->status == 'noshow') {
                                                ?>
                                                    <label class='badge badge-light'>No Show</label>

                                                <?php } ?>
                                                <?php

                                                if ($record->status == 'arrived') {
                                                ?>
                                                    <label class='badge badge-primary'>Arrived</label>

                                                <?php } ?>
                                                <?php

                                                if ($record->status == 'processing') {
                                                ?>
                                                    <label class='badge badge-secondary'>Processing</label>

                                                <?php }
                                                if ($record->status == 'refused') { ?>
                                                    <label class='badge badge-light'>Refused</label>
                                                <?php }
                                                if ($record->status == 'hospitalized/rehab') { ?>
                                                    <label class='badge badge-info'>Hospitalized/Rehab</label>
                                                <?php }
                                                if ($record->status == 'Pending Termination') { ?>
                                                    <label class='badge badge-danger'>Pending Termination</label>
                                                <?php }
                                                if ($record->status == 'On Hold') { ?>
                                                    <label class='badge badge-secondary'>On Hold</label>
                                                <?php }
                                                if ($record->status == 'On Leave') { ?>
                                                    <label class='badge badge-info'>On Leave</label>
                                                <?php }
                                                if ($record->status == 'Terminated') { ?>
                                                    <label class='badge badge-danger'>Terminated</label>
                                                <?php }
                                                if ($record->status == 'unableToContact') { ?>

                                                    <label class='badge badge-danger'>Unable To Contact</label>
                                                <?php } ?>


                                            </dd>
                                            <dt>Language </dt>
                                            <dd ><span id="record_languages_res_id"><?php echo $record->languages != null ? $record->languages->name : 'N/A'; ?></span>
                                            <input type="hidden" id="record_languages_id" value="<?php echo $record->languages != null ? $record->languages->id : ''; ?>">
                                            
                                        </dd>

                                            <?php if ($record->status == 'cancelled') { ?>
                                                <dt> Reason</dt>
                                                <dd> <?php echo $record->reasonname . '<br>'; ?></dd>

                                            <?php } ?>

                                            <?php if ($record->agency_id  != '319'  && $record->agency_id  != '106') { ?>
                                                <dt> Assigned To </dt>
                                                <dd> {{$record->assign_user!=""?$record->assign_user:"N/A"}}</dd>
                                            <?php } ?>

                                            <dt> Created Date</dt>
                                            <dd> <?php echo Common::convertMDY($record->created_date) . '<br>'; ?></dd>
                                            <dt> Created By </dt>
                                            <dd> {{$record->createdBy}} @if($record->userTypes !="")({{ $record->userTypes }})@endif</dd>
                                            <dt>Notes</dt>
                                            <dd><?php echo $record->remarks == null ? 'N/A' : $record->remarks . '<br>'; ?></dd>
                                            <?php if ($record->type == 'Caregiver') { ?>
                                                <dt>Link HHX Caregiver </dt>
                                                <input type="hidden" id="hha_caregiver_ids" value="{{ $record->link_hha_caregiver}}">
                                                <input type="hidden" id="hha_caregiver_names" value="{{ $record->hhx_caregiver_name}}">
                                                <dd><span id="hhx_caregiver_id"> {{ ($record->hhx_caregiver_name !="")?$record->hhx_caregiver_name:"N/A" }}</span>&nbsp;&nbsp;</dd>
                                            <?php } ?>

                                            <?php if ($record->type == 'Patient') { ?>
                                                <dt>Link HHX Patient </dt>
                                                <input type="hidden" id="hha_patient_ids" value="{{ $record->link_hha_patient}}">
                                                <input type="hidden" id="hha_patient_names" value="{{ $record->hhx_patient_name}}">
                                                <dd><span id="hhx_patient_id"> {{ ($record->hhx_patient_name !="")?$record->hhx_patient_name:"N/A" }}</span>&nbsp;&nbsp;</dd>
                                            <?php } ?>
                                            <?php
                                            if ($record->type    == 'Caregiver') {
                                                if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                    <dt>In Service Status First</dt>
                                                    <dd><span id="inservices_status"> {{ ($record->inservice_status !="")?$record->inservice_status:"N/A" }}</span></dd>

                                                    <dt>In Service Status Second</dt>
                                                    <dd><span id="inservices_status_two"> {{ ($record->inservice_status_two !="")?$record->inservice_status_two:"N/A" }}</span></dd>
                                            <?php }
                                            } ?>
                                            <dt>In Service Date </dt>
                                            <dd><span id="inservices_dates"> {{ ($record->inservice_datetime !="")?date('m/d/Y  h:i A',strtotime($record->inservice_datetime)) :"N/A" }}</span><br></dd>

                                            @if( isset($agencyDetails->alaycare_status) && $agencyDetails->alaycare_status ==1)
                                            <dt>Alaycare Id </dt>
                                            <dd><span id="hhx_alaycare_id"> {{ ($record->alaycare_id != "") ? $record->alaycare_name . ' (' . $record->alaycare_id . ')' : "N/A" }}</span>&nbsp;&nbsp;
                                                
                                            </dd>
                                           
                                            @endif
                                            @if( isset($agencyDetails->robort_status) && $agencyDetails->robort_status ==1)
                                            <dt>Remote Id </dt>
                                            <dd><span id="hhx_robort_id"> {{ ($record->robort_id != "") ? $record->remote_name : "N/A" }}</span>&nbsp;&nbsp;
                                               
                                                
                                            </dd>
                                           

                                            @endif
                                            <?php
                                            if ($record->type    == 'Caregiver') {
                                                if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                    <dt>Training Status </dt>
                                                    <dd><span id="training_statuss"> {{ ($record->training_status !="")?$record->training_status:"N/A" }}</span></dd>

                                            <?php }
                                            } ?>

                                            <?php
                                            if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                <dt>Medical Followup Date</dt>
                                                <dd><span id="{{ $record->agency_id}}_follow_update"> {{ ($record->follow_date !="")?date('m/d/Y',strtotime($record->follow_date)):"N/A" }}</span>&nbsp;&nbsp;</dd>

                                            <?php } ?>

                                            <?php
                                            if ($record->agency_id  == '106') { ?>
                                                <dt>Availability followup Date</dt>
                                                <dd><span id="{{ $record->agency_id}}_availability_followup_date"> {{ ($record->availability_followup_date !="" && $record->availability_followup_date !="0000-00-00")?date('m/d/Y',strtotime($record->availability_followup_date)):"" }}</span>&nbsp;&nbsp;</dd>

                                            <?php
                                            }
                                            ?>
                                            <dt>Link To Third Party </dt>
                                            <dd><span id="link_third_party_id"> {{ ($record->link_third_party != "") ? $record->link_third_party_name: "N/A" }}</span>&nbsp;&nbsp;
                                                
                                                
                                            </dd>
                                         

                                            <dt>Location / Branch</dt>
                                            <dd>@if($record->location_branch !=""){{ $record->location_branch}} @endif</dd><br>

                                            <dt>Medicare No</dt>
                                            <dd>@if($record->medicare_no !=""){{ $record->medicare_no}} @endif</dd><br>
                                            
                                            @if($record->type =='Caregiver')
                                            <dt>Transition Aid</dt>
                                            <dd>@if($record->transition_aid ==1) Yes @else No @endif</dd>
                                            @endif
 
                                            <!-- @if($record->merge_appointment_id !='')
                                            <dt>Merged Id</dt>
                                            <dd><a href="{{ url('/patient/view/')}}/{{ $record->merge_appointment_id}}" target="_blank">{{ $record->merge_appointment_id}}</a></dd>
                                            @endif -->
                                            @can('flag-change-status')
                                            @if($record->flag ==1)
                                            <dt>Flag</dt>
                                            <dd>@if($record->flag ==1) Flagged @else Flag @endif</dd>
                                            @endif
                                            
                                            @endcan
                                        </dl>

                                    </div>


                                </div>

                            </div>

                            <hr>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 grid-margin stretch-card">
                <div class="card">
                    <div class="left-section-main">
                        <ul class="nav nav-tabs tabs-left sideways left-section-ul">
                            <li class="active"><a href="#document-section" data-toggle="tab" onclick="loadDocumentAjaxList()">Document Section</a>
                            </li>
                            <!-- <li><a href="#reminder-section" data-toggle="tab">Reminder Section</a></li> -->
                            <li><a href="#notes-section" data-toggle="tab" onClick="loadAllNotes()">Notes Section</a></li>

                            

                            @if($user['user_type_fk'] == 184 )
                            <li><a href="#task-section" data-toggle="tab" onclick="getTaskList()">Task Section</a></li>

                            @can('sms-log-list')
                            <li><a href="#sms-logs-section" data-toggle="tab" onClick="smsLogs(1)">SMS Logs</a>
                            </li>
                            @endcan
                            <li><a href="#appointment-section" data-toggle="tab">Appointment Section </a>
                            </li>
                            <li><a href="#text-messages-section" data-toggle="tab" onClick="loadAllTextMessages()">Text Messages Section</a>
                            </li>

                            @can('esign-list-v2')
                            <li><a href="#esign-section-new" data-toggle="tab" onclick="esignResponseNew1()">Esign Section</a>
                            </li>
                            @endcan

                            @if( isset($agencyDetails->enable_hha) && $agencyDetails->enable_hha ==1)
                            @if($record->hha_id !="" || $record->link_hha_caregiver)



                            @endif

                            @endif
                            <!-- @if($record->alaycare_id !="")
                                @if($record->type =='Caregiver')
                                <li><a href="#alaycare-skill" onclick="getAlyacareSkill()" data-toggle="tab">AlayaCare Skill Section</a>
                                </li>
                                @endif
                                <li><a href="#alaycare-calendar" onclick="getAlyacareEmployeeSchedular()" data-toggle="tab">AlayaCare Employee Schedule Section</a>
                                </li>
                                @if($record->type =='Caregiver')
                                <li><a href="#alaycare-employee-notes" onclick="getAlyacareEmployeeNotes()" data-toggle="tab">AlayaCare Employee Notes Section</a>
                                </li>
                                <li><a href="#alaycare-document-attachment" onclick="getAlyacareDocument()" data-toggle="tab">AlayaCare Documents / Attachments Section</a>
                                </li>

                                @endif

                            @endif -->

                            <!-- @if($record->robort_id !="")
                            @if($record->externalId !="")
                            @can('patient-reading-list')
                            <li><a href="#patient-reading-list" onclick="getPatientReading()" data-toggle="tab">Reading Section</a>
                            </li>
                            @endcan
                            @can('patient-medicine-list')
                            <li><a href="#patient-medicine-list" onclick="getPatientMedicineList()" data-toggle="tab">Medication List Section</a>
                            </li>
                            @endcan
                            @endif
                            @endif-->
                        @endif


                            
                            <li><a href="#service-requested-by-patient" onclick="serviceRequestedList()" data-toggle="tab">Service Requested</a>
                            </li>
                            @can('combine-record')
                                <li><a href="#merge_appoint_listing_section" onclick="mergeAppointmentData()" data-toggle="tab">Merge Appointment</a></li>
                            @endcan
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content left-section-tab-content">
                            <div class="tab-pane active" id="document-section">
                                
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <p class="card-title mb-0">Document Section</p>
                                    
                                </div>
                                <div class="row">
                                    
                                    <div class="col-12">
                                        
                                        <div class="loader-main" id="loaderAlayaSkillLoaded" style="display:none">
                                            <div class="loader-inner">
                                                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader">
                                            </div>
                                        </div>
                                        <div id="document_response_list"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="reminder-section">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <p class="card-title mb-0">Reminder Section</p>
                                    <?php if ($user['user_type_fk'] == 184) { ?>
                                        <p class="mb-0 tx-13">
                                            <a data-toggle="modal" class="pull-right btn btn-info btn-sm  d-none d-md-block" data-target="#exampleModal-51" data-whatever="@mdo" style="color:#fff"><i class="mdi mdi-plus"></i> Add</a>
                                        </p>
                                    <?php } ?>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive ">
                                            <table id="" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Email</th>
                                                        <th>Mobile</th>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Notes</th>
                                                        <th>Created Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="remnid">

                                                </tbody>
                                            </table>
                                            <div class="pull-right pegination-margin">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="notes-section">
                                @php
                                $notesFlag=0
                                @endphp
                                @if($record->type != "")
                                @if($record->link_hha_patient !="")
                                @php
                                $notesFlag=1
                                @endphp

                                @elseif($record->link_hha_caregiver !="")
                                @php
                                $notesFlag=1
                                @endphp
                                @else
                                @if($record->hha_id !="")
                                @php
                                $notesFlag=1
                                @endphp
                                @endif
                                @endif
                                @endif
                                @include('deletedPatients._partial.notes_section')
                            </div>
                            


                            <div class="tab-pane" id="text-messages-section">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <p class="card-title mb-0">Text Message Section</p>
                                    <div class="pull-right">
                                        <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag122" style="display: none; ">


                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-chat-messages" id="text-sms-messages">
                                            <div id="text-chat-messages-inner" class="text-notes-messages"></div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="task-section">
                                @include('deletedPatients/_partial/all_tabs_section/task_section')
                            </div>

                            <div class="tab-pane" id="appointment-section">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <p class="card-title mb-0">Appointment List</p>
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

                                                    @if(count($pastAppointment)>0)
                                                    @foreach($pastAppointment as $key => $appointment)
                                                    <tr>
                                                        <td>{{$key+1}}
                                                        @if($appointment->patient_id != $record->id)
                                                        <span style="top: 0;background: #00BBE0;padding: 1px 2px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;">Merge</span>
                                                        @endif
                                                        </td>
                                                        <td class="white_space">{{$appointment->patient->full_name}}</td>
                                                        <td>@if(isset($appointment->location) && $appointment->location->address1){{$appointment->location->address1}} @endif</td>
                                                        <td>{{isset($servie[$key]) ? $servie[$key] : ''}}</td>
                                                        <td>{{date('d/m/Y',strtotime($appointment->appointment_date))}}</td>
                                                        <td>{{date('h:i:s A',strtotime($appointment->appointment_time))}}</td>
                                                        <td>{{date('m/d/Y h:i A', strtotime($appointment->created_at))}}</td>
                                                        <td>@if(isset($appointment->getCreatedBy->full_name)) {{$appointment->getCreatedBy->full_name}} @endif</td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="9" style="text-align: center;">Data not found</td>
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

                            <div class="tab-pane" id="sms-logs-section">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <p class="card-title mb-0">SMS Logs</p>
                                </div>
                                <div class="row">
                                    <div class="col-12">

                                        <div class="col-12 loader-calender" id="logList1" style="display:flex;justify-content:center;margin-top:10%">
                                            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loadertag121" style="display:none">
                                        </div>

                                    </div>
                                    <div class="col-12" id="sms_logs_id">
                                    </div>
                                </div>
                            </div>
                            @if($record->alaycare_id !="")
                                @include('deletedPatients._partial.alayacare.skill_qualification')
                                @include('deletedPatients._partial.alayacare.employee_scheduler')
                                @include('deletedPatients._partial.alayacare.employee_notes')
                                @include('deletedPatients._partial.alayacare.document_attachment')
                            @endif
                            @if($record->robort_id !="")
                                <div class="tab-pane" id="patient-oru-trn">
                                    @include('deletedPatients._partial.patient_oru')
                                </div>
                                @include('deletedPatients._partial.patient_reading')
                                @include('deletedPatients._partial.patient_medication')
                            @endif
                            <div class="tab-pane" id="esign-section">
                                @include('deletedPatients._partial.esign.esign')
                            </div>
                            <div class="tab-pane" id="esign-section-new">
                                @include('deletedPatients._partial.esign.esign-new')
                            </div>
                           
                            <div class="tab-pane" id="patient-custom-data">
                                @include('deletedPatients._partial.patient.patient_custom_form')
                            </div>

                            <div class="tab-pane" id="agency-all-form-table">
                                @include('deletedPatients._partial.patient.agency_all_form_table')
                            </div>
                            <div class="tab-pane" id="service-requested-by-patient">
                                @include('deletedPatients._partial.service_requests.service_request_by_patient')
                            </div>

                            @can('combine-record')
                            <div class="tab-pane" id="merge_appoint_listing_section">
                                @include('patient._partial.patient_merge_record.mergeAppointment_list')
                            </div>
                            @endcan
                        </div>



                    </div>
                </div>
            </div>
        </div>
        @if($user['user_type_fk'] == 184 )
        <div class="content-wrapper custom-wrapper">
            <div class="card">
                <div class="row list-name m-3">
                    <div class="col-sm-6 card-title">
                        <h4 class="card-title">Appointment Logs</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12" id="logList" style="display:flex;justify-content:center;">
                            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag" style="display: none; ">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif


    </div>

</div>
@include('deletedPatients._partial.modal.patient_document.patient_upload_document')
@include('deletedPatients._partial.link_to_visiting_aid_model')
@include('deletedPatients._partial.modal.patient_next_appointment_date.next_appointment_date_modal')
@include('deletedPatients._partial.modal.patient_add_appointment.patient_add_appointment')

<div class="modal fade" id="exampleModal-23" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Request for Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action="<?php echo URL::to('/patient/appointment-schedule'); ?>" name="adduser" method="post" id="appointmentForm">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                    <?php 
                    $locationsIds = [];
                    if(auth()->user()->agency_fk !=""){
                        $locationsIds = ['49','55'];
                    }
                ?>
                    <?php if ($record->type == 'Caregiver') { ?>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Location<span style="color:red">*</span>:</label>
                            <select name="location_id" class="form-control" id="location_eid" onchange="getTimeSearchForAgency()">
                                <option value="">Select Location</option>
                                <?php foreach ($location_list as $ks) { 
                                    if(!in_array($ks->id,$locationsIds)){
                                    ?>
                                    <option value="<?php echo $ks->id; ?>"><?php echo $ks->address1; ?>
                                    </option>
                                <?php } } ?>
                            </select>
                            <input type="hidden" name="caregiver_type" value="<?php echo $record->type; ?>">
                            <span id="location_eid_error" class="error mt-2 text-danger" for="document_type"></span>
                        </div>
                    <?php } ?>
                    <?php
                    $dates = '';
                    $time = '';
                    if ($record->appointment_date != '') {
                        $dates = date('m/d/Y', strtotime($record->appointment_date));
                        $time = date('H:i:s', strtotime($record->appointment_date));
                    } ?>
                    <div class="form-group setDate">
                        <label for="recipient-name" class="col-form-label">Appointment Date <span style="color:red">*</span>:</label>
                        <input readonly type="text" name="date" class="form-control getappoinmentdate" autocomplete="off" id="date_eid" onchange="getTimeSearchForAgency()" value="">
                        <span id="date_eid_error" class="error mt-2 text-danger" for="document_type"></span>
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Appointment Time<span style="color:red">*</span>:</label>
                        <?php if ($record->type == 'Caregiver') { ?>
                            <select name="time" class="form-control" id="time_eid">
                                <option value="">Select Appointment Time</option>
                            </select>

                        <?php } else { ?>
                            <input type="time" name="time" class="form-control" id="times_eid" value="">

                        <?php } ?>
                        <span id="time_eid_error" class="error mt-2 text-danger" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Services<span class="error mt-2">*</span></label>
                        <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_eid">
                            <option value="">Select Service</option>
                            @php $serviceArr = explode(',', $record->service_id);
                            echo "
                            <pre>";print_R($serviceArr);
                                @endphp
                                @if (count($serviceList) > 0)
                                @foreach ($serviceList as $ks)
                                @if ($ks->types == $record->type)
                                <option value="{{$ks->id}}">{{ $ks->name }}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            <span class="error mt-2 text-danger" id="service_eid_error"></span>

                    </div>
                    @if ($record->type == 'Patient')
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Location<span style="color:red">*</span>:</label>
                            <select name="location_id" class="form-control" id="location_eid">
                                <option value="">Select Location</option>
                                @if (count($locations) > 0)
                                @foreach ($locations as $location)
                                @if(!in_array($location->id,$locationsIds))
                                <option value="{{$location->id}}" >{{$location->location_name}}
                                </option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            <span class="error mt-2 text-danger" id="location_eid_error"></span>

                        </div>
                    @endif

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
 

@include('deletedPatients._partial.modal.patient_telehealth.patient_tele_health_modal')
@include('deletedPatients._partial.modal.patient_document.patient_document_model')
    
   
<div class="modal fade" id="other-complience-hha-update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title documens" id="ModalLabel">Update Other Complience to HHX Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="hideOtherComplianceToHHXDocument()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/update-other-complience-hha-document') }}" name="adduser" method="post" id="formnew-other-compienece-hha-update">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" id="document_request_complience_id" value="">
                    <input type="hidden" name="record-id" id="document_complience_record_id" value="{{ $record->id }}">
                    <input type="hidden" name="agencyId" id="document_complience_ids" value="{{ $record->agency_id }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">HHX Document Type<span style="color:red">*</span>:</label>
                                <select name="document_type" class="form-control" id="hha_document_complience_type_id" ></select>
                                <span id="hha_document_complience_type_id_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">HHX Complience Name<span style="color:red">*</span>:</label>
                                <select name="document_medical_type[]" class="select2-design cal-padding-0 js-example-basic-multiple w-100 hha_complience_id" id="hha_document_complience_id" multiple></select>
                                <span id="hha_document_complience_id_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                    </div>
                    

                    
                    <span class="row" id="multipleComplienceResultId" style="display:none">
                        
                    </span>
                    

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"> Date Performed<span style="color:red">*</span>:</label>
                        <input type="text" name="completed_date" class="form-control perforrm-datepicker" id="completed_date_complience">
                        <span id="complience_completed_date_error" style="color:red" class="error"></span>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="update-hha-complience-id">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="hideOtherComplianceToHHXDocument()">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>
<div class="modal fade" id="exampleModal-hha-update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title documens" id="ModalLabel">Update to HHX Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearData()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action="{{ URL::to('/update-hha-document') }}" name="adduser" method="post" id="formnew-hha-update">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" id="document_request_id" value="">
                    <input type="hidden" name="record-id" id="document_recoed_id" value="{{ $record->id }}">
                    <input type="hidden" name="agencyId" id="document_ids" value="{{ $record->agency_id }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">HHX Document Type<span style="color:red">*</span>:</label>
                                <select name="document_type" class="form-control" id="hha_document_type_id" ></select>
                                <span id="hha_document_type_id_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">HHX Medical Name<span style="color:red">*</span>:</label>
                                <select name="document_medical_type[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100 upload-hhax" id="hha_document_medical_id"    multiple="multiple"></select>
                                <span id="hha_document_medical_id_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                        
                    </div>
                    
                    

                    
                    <span class="row" id="multipleMedicalResultId" style="display:none">
                        
                    </span>
                    
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"> Date Performed<span style="color:red">*</span>:</label>
                        <input type="text" name="completed_date" class="form-control perforrm-datepicker" id="completed_date">
                        <span id="completed_date_error" style="color:red" class="error"></span>
                    </div>
                    <div id="hha_due_date_div" style="display:none">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Due Date:</label>
                                <input type="text" name="hha_due_date" id="hha_due_date" class="form-control">
                                <span id="hha_due_date_div_error" style="color:red" class="error"></span>
                            </div>
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="update-hha-document-id">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal"  onclick="clearData()">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>
    
<div class="modal fade commons" id="" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span> Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                    <textarea name="document_id" class="form-control" id="notes_id"></textarea>

                    <span id="notes_status_error" class="error"></span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="commons_flag">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>
@include('deletedPatients._partial.modal.patient_attachment.attachment_modal')
@include('deletedPatients._partial.modal.patient_completed.patient_completed_modal')
    
<div class="modal fade" id="exampleModal-cancel" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span>Cancel Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Reason<span class="error">*</span>:</label>
                    <select name="reason_id" class="form-control" id="reason_ids">
                        <option value="">Select Reason</option>
                        <?php
                        if (count($masterData) > 0) {
                            foreach ($masterData as $val) {
                                if ($val->master_type_fk == 12) {
                        ?>
                                    <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                        <?php  }
                            }
                        } ?>
                    </select>
                    <span id="reason_id_status_error" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                    <textarea name="document_id" class="form-control" id="notes_id_cancel"></textarea>

                    <span id="notes_status_cancel_error" class="error"></span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getStatusNew('cancel')">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>
@include('deletedPatients._partial.modal.patient_payment_type.payment_type_modal')    
@include('deletedPatients._partial.esign.send_signer_request_modal')
@include('deletedPatients._partial.esign.send_signer_request_modal_new')
<div class="modal fade" id="exampleModal-67" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span>Medical Due Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Medical Due Date<span class="error">*</span>:</label>
                    <input type="text" readonly name="due_date" class="form-control" id="due_date_id" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php if ($record->due_date != '') {
                                                                                                                                                                                                                echo date('m/d/Y', strtotime($record->due_date));
                                                                                                                                                                                                            } ?>">
                    <span id="due_date_id_error" class="error"></span>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getDueDate()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal-assign" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="" style="text-transform:capitalize"></span>Assign NyBest User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Assign NyBest User<span class="error">*</span>:</label>
                    <select name="assign_nybest_user" class="form-control" id="assign_nybest_user">
                        <option value="">Select Assign NyBest User</option>
                        @if (!empty($assign_user_list[0]))
                        @foreach ($assign_user_list as $val)
                        <option value="{{ $val->id }}" @if ($val->id == $record->assign_user_id) selected='selected' @endif>
                            {{ $val->name }}
                        </option>
                        @endforeach
                        @endif

                    </select>
                    <span id="assign_nybest_user_error" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Notes:</label>
                    <textarea name="notes" class="form-control" rows="4" cols="50" id="notes_ny_id"></textarea>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getNyBestUpdate()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="exampleModal-51" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span> Reminder Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closed_id">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="reminder_id">
                @csrf
                <input type="hidden" name="patient_id" value="<?php echo $record->id; ?>">
                <div class="modal-body">


                    <div class="form-group" style="margin-bottom:0px !important">
                        <label for="recipient-name" class="col-form-label">Email<span class="error">*</span>:</label>
                        <input type="text" name="email" class="form-control" id="remail" autocomplete="off">
                        <span id="remail_status_error" class="error"></span>
                    </div>
                    <div class="form-group" style="margin-bottom:0px !important">
                        <label for="recipient-name" class="col-form-label">Mobile:</label>
                        <input type="text" name="mobile" class="form-control" id="rmobile" onkeypress="return isNumber(event)" autocomplete="off">
                        <span id="mobile_status_error" class="error"></span>
                    </div>
                    <div class="form-group" style="margin-bottom:0px !important">
                        <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                        <textarea name="notes" id="rnotes" class="form-control"></textarea>
                        <span id="rnotes_status_error" class="error"></span>
                    </div>
                    <div class="form-group" style="margin-bottom:0px !important;margin-left:-10px">
                        <label class="col-sm-3 col-form-label">Type<span class="error mt-2 text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="radio" name="rtype" value="EveryDate" onclick="getResponse('EveryDate')"> On Date
                            <input type="radio" name="rtype" value="EveryMonth" onclick="getResponse('EveryMonth')"> Every Month<br>
                            <span id="rtype_error" class="error"></span>

                        </div>
                    </div>
                    <div class="form-group" id="dates_id" style="display:none">
                        <label class="col-sm-3 col-form-label">Date<span class="error mt-2 text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="date" id="rdates" class="form-control" autocomplete="off">
                            <span id="rdate_error" class="error"></span>

                        </div>
                    </div>
                    <div class="form-group" id="month_id" style="display:none">
                        <label class="col-sm-3 col-form-label">Month<span class="error mt-2 text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="every_month" class="form-control" id="rmonth" onchange="getConvertDate(this.value)">
                                <option value="">Select Month</option>
                                <option value="1">Every Month</option>
                                <option value="3">3 Month</option>
                                <option value="6">6 Month</option>
                                <option value="12">Every Year</option>

                            </select>
                            <span id="every_month_error" class="error"></span>

                        </div>
                        <p class="mb-0 text-success font-weight-bold test_id append_id" style="margin-left:10px">Tester</p>
                    </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getReminder()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>

        </div>
    </div>
</div>
   
@include('deletedPatients/_partial/modal/patient_assign/patient_assign_modal')


<div class="modal fade" id="exampleModal-notes" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title documens" id="ModalLabel">Add Caregiver Notes </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="hha_caregivers_notes">
                <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Subject<span class="error">*</span>:</label>
                        <select class="form-control" id="subjectId" name="subjectId">

                        </select>
                        <span id="hha_subject_id_error" class="error mt-2"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                        <textarea type="text"    rows="4" cols="50"  class="form-control" id="hha_caregivers_notes_id"></textarea>
                        <span id="hha_caregivers_notes_id_error" class="error mt-2" for="hha_caregivers_notes_type"></span>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="hhaCaregiverSave">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- End Assign Modal -->
<div class="modal fade" id="exampleModal-link-hha" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Link HHX Profile</h5>
                <button type="button" class="close" data-dismiss="modal" id="closedsNew" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="lnkhhx_pdf_id">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="recipient-name" class="col-form-label">Search Caregiver Code:</label><br>
                                        <input type="text" class="form-control" name="hha_caregiver_code_id"  id="hha_caregiver_code_id"><br/>
                    
                                        <span class="error hha_caregiver_code_id_error"></span>
                                    </div>
                                    <div class="col-md-2 mt-5">
                                        <a href="javascript:void(0)" onclick="searchCaregiver()"><i class="fa fa-search" style="font-size:20px;"></i></a>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="recipient-name" class="col-form-label">Search Caregiver: <span class="error">*</span></label><br>
                                        <input type="text" name="hha_profile_id"  id="hha_profile_id"><br/>
                                        <input type="hidden" name="dataType" id="dataTypeId">
                                        <span class="error hha_profile_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5"  id="hhas_caregiver_id" style="display:none">
                            <div class="form-group ">
                                <div class="row">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th>#</th>
                                            <th nowrap>Caregiver ID</th>
                                            <th nowrap>Caregiver Name</th>
                                            <th nowrap>Status</th>
                                            <th nowrap>Action</th>
                                        </thead>
                                        <tbody id="hhaAppendCId">

                                        </tbody>
                                    </table>
                                </div>
                            
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            
                        </div>
                    </div>
                    
                    

                    
                </form> 

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="getHhxProfile()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

</div>
@include('deletedPatients._partial.modal.patient_merge_record.merge_record_modal')
@include('deletedPatients._partial.modal.patient_inservice_first.patient_inservice_first')
    
<div class="modal fade" id="exampleModal-hha-update-patient" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title documens" id="ModalLabel">Update to HHX Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearData()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action="" name="adduser" method="post" id="update-hha-document-patient">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" id="main_id" value="">
                    <input type="hidden" name="record-id" id="document_recoed_id" value="{{ $record->id }}">
                    <input type="hidden" name="agencyId" id="document_ids" value="{{ $record->agency_id }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">HHX Document Type<span style="color:red">*</span>:</label>
                                <select name="document_type" class="form-control" id="hha_patient_document_type_id" ></select>
                                <span id="doc_error" style="color:red" class="error"></span>
                            </div>
                        </div>
                        
                    </div>
                    
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="update-hha-document-patient-btn">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal"  onclick="clearDataHHA()">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>

@include('deletedPatients._partial.alayacare.modal.link_alayacare_modal')
@include('deletedPatients._partial.alayacare.modal.save_alayacare_submit_modal')
@include('deletedPatients._partial.modal.inservice_status_modal')
@include('deletedPatients._partial.modal.trainingStatusModal.training_status_modal')
@include('deletedPatients._partial.modal.trainingStatusModal.training_due_date_modal')
@include('deletedPatients/_partial/modal/hama_emergency_phone_modal')
@include('deletedPatients/_partial/modal/hama_emergency_email_modal')
@include('deletedPatients/_partial/modal/patient_document/edit_services_document_modal')

<div class="modal fade" id="exampleModal-change-task-staus" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Change Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action="{{url('tasks/task-change-status')}}" name="adduser" method="post" id="task_form">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" id="edit_id" value=""> 
                    <input type="hidden" name="recordId" id="recordId" value="{{Request()->id}}">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Status<span style="color:red">*</span>:</label>
                                <select name="status" class="form-control" id="status_id">
                                    <option value="">Select Status</option>
                                    <option value="Urgent" >Urgent</option>
                                    <option value="Outstanding" >Outstanding</option>
                                    <option value="Pending" >Pending</option>
                                    <option value="Completed" >Completed</option>
                            </select>
                            <span id="task_status_error" class="error mt-2" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Notes:</label>
                            <textarea class="form-control" type="text" class="form-control" name="task_description"  placeholder="Enter Task Description" id="task_description" rows="4" cols="50"></textarea>
                        </div>
                    <div class="modal-footer">
                        <button type="button" onclick="getTaskChangeStatus()" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('deletedPatients/_partial/modal/patient_follow_date/patient_follow_date_modal')
@include('deletedPatients/_partial/remote/remote_link_modal')
    
<input type="hidden" id="record_id" value="{{ $record->id }}">       
<input type="hidden" id="agency_id" value="{{ $record->agency_id }}">
    
@include('deletedPatients/_partial/inservice_status_two')
@include('deletedPatients/_partial/availability_modal')
@include('deletedPatients/_partial/patient_link_to_third_party/third_party_api_modal')
@include('deletedPatients/_partial/document_send_mail')
@include('deletedPatients/_partial/hha_module/link_hha_patient')
@include('deletedPatients/_partial/service_requests/modal/service_status_change_request_modal')    
@include('deletedPatients._partial.esign.esign_move_document')
@include('deletedPatients._partial.task.create_task_modal')
@include('task._partial.task_sidebar')
@include('deletedPatients/_partial/modal/show_patient_demo_modal')
@include('deletedPatients/_partial/hha_module/poc/create_patient_poc_information')
@include('deletedPatients/_partial/hha_module/caregiverDocument/create_caregiver_document')
@include('deletedPatients/_partial/hha_module/patientDocument/create_patient_document')
@include('deletedPatients._partial.service_requests.modal.add_service_request_modal')
@include('deletedPatients/_partial/modal/patientMobile/patient_mobile_modal')
@include('deletedPatients/_partial/modal/patientPhone/patient_phone_modal')
@include('deletedPatients/_partial/modal/patientLanguage/patient_language_modal')
@include('deletedPatients/_partial/modal/patient_dob/update_patient_dob')
@include('include/footer')
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/vendors/fullcalendar/fullcalendar.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>

<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />

<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css')}}" />
<script src="{{ asset('assets/js/jquery.fancybox.min.js')}}"></script>
<script>
       
    @can('hha-sync-appointment-calendar')
        @if($record->link_hha_caregiver !="" || $record->hha_id !="")
            $(document).ready(function() {
            
                loadCalender();
            });
        @endif
    @endcan
    var agencyFks = "{{ $auth->agency_fk}}";
    if(agencyFks ==106){
        loadCalender();
    }
        
    function loadCalender(){
        $(document).ready(function() {
    
            var calnedr = $('#calendar').fullCalendar({
               header: {
                   left: 'prev,next today',
                   center: 'title',
                   right: 'month,basicWeek,agendaDay,listWeek,print'
               },
               aspectRatio: 1.5,
               eventLimit: true,
               dayMaxEvents: 3,
               defaultView: 'month',
               navLinks: true,
               editable: true,
               eventLimit: true,
               
               events: function(start, end, timezone, callback) {
                   var startDate = moment(start).format("YYYY-MM-DD");
                   var endDate = moment(end).format("YYYY-MM-DD");
                   $('#loadertag12').attr('style','');
                   var id = "{{ ($record->link_hha_caregiver !='')?$record->link_hha_caregiver:$record->hha_id }}";
                   var type ='{{ $record->type}}';
                   var url ='';
                   if(type =='Caregiver'){
                     url = "{{ url('patient/sync') }}?id=" + id
                   }else{
                    var id = "{{ ($record->hha_id !='')?$record->hha_id:$record->link_hha_patient }}";
                      url = "{{ url('sync-hha-appointment-patient') }}?patientId=" + id;
                   }
                   if(id   !=""){
                       $.ajax({
                         
                           url: url,
                           type: "GET",
                           data: {
                               start: startDate,
                               end: endDate,
                           },
                           success: function(res) {
                            var doc = JSON.parse(res);
                            $('#loadertag12').attr('style','display:none');
                            callback(doc);

                           }
                       });
                   }
               },
               eventRender: function(event, eventElement, eventColor) {
                   eventElement.find(".fc-time").remove();
                   eventElement.find(".fc-title").append("<br/><b>"+event.label+"</b>");
               },

           })
       });
    }
    var dateToday = new Date();
    $('.datepicker').datepicker({
        minDate: dateToday,
        dateFormat: 'mm/dd/yy',
        buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
    });
    $('#start_date').datepicker({
        minDate: dateToday,
        dateFormat: 'mm/dd/yy',
        buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
    });
    
    $('.perforrm-datepicker').datepicker({
        
        dateFormat: 'mm/dd/yy',
        buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
    });
    $(document).ready(function() {
        $('ul.left-section-ul li').click(function() {
            $('ul.left-section-ul li').removeClass('active');
            $(this).addClass('active');
        })

        $('ul.right-section-ul li').click(function() {
            $('ul.right-section-ul li').removeClass('active');
            $(this).addClass('active');
        
        })
        
        getResponseService('{{$record->type}}');
        $(".select2").attr('style', 'width:100%');
    })

    function getResponseService(id) {
        if (id != '') {
            var jsonencode = <?php echo json_encode(old('service_id')); ?>;
            $.ajax({
                async: false,
                global: false,
                type: "GET",
                url: "{{ url('ajax-service')}}",
                data: {
                    "id": id,
                    "jsonencode": jsonencode
                },
                success: function(res) {
                    if (res != '') {
                        htmlsresp = res;
                    } else {
                        htmlsresp = '<option value="">No record available</option>';
                    }
                    $('#service_id').html(htmlsresp);
                }
            })
        }
    }

    $(":input").inputmask();
    function Assignvalidation() {
        var temp = 0;
        var assign_to = $("#assign_id").val();
        $("#assign_to_us_error").html("");
        if (assign_to == "") {
            $("#assign_to_us_error").html("Please select assign user.");
            temp++;
        }
        if (temp == 0) {
            return true;
        } else {
            return false;
        }
    }
    $('#rdates').datepicker({buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240"});
    $('#form').submit(function(e) {
        var date = $('#date_id').val();
        var time = $('#timeid').val();
        var doctor_id = $('#doctor_id').val();
        var location_id = $('#location_id').val();
        var times_id = $('#times_id').val();
        var service_id = $('#service_id').val();
        $('#date_error').html("");
        $('#time_error').html("");
        
        var cnt = 0;

        if (location_id == '') {
            $('#exampleModal-4 #location_error').html("Please select Location");
            cnt = 1;
        }
        if (service_id.length == 0) {
            $('#exampleModal-4  #service_error').html("Please select Services");
            cnt = 1;
        }

        if (date.trim() == '') {
            $('#date_error').html("Please select Appointment Date ");
            cnt = 1;
        }
        <?php if ($record->type == 'Caregiver') { ?>
            if (time.trim() == '') {
                $('#time_error').html("Please select Appointment Time");
                cnt = 1;
            }
        <?php } else { ?>
            if (times_id.trim() == '') {
                $('#time_error').html("Please select Appointment Time");
                cnt = 1;
            }
        <?php } ?>
        
        <?php if ($record->type == 'Caregiver') { ?>
            if (time.trim() != '') {
                $.ajax({
                    async: false,
                    global: false,
                    url: "{{ url('location/remaining-time-slot')}}",
                    type: "GET",
                    data: {
                        "time": time,
                        'date': date 
                    },
                    success: function(res) {
                        if (res == 1) {} else {
                            $('#time_error').html("Slot limit over");
                            cnt = 1;
                        }
                    }
                })

            }
        <?php } ?>

        if (cnt == 1) {
            return false
        } else {
            return true;
        }

    });
</script>

    <script>
        $('#appointmentForm').submit(function(e) {
            var date = $('#date_eid').val();
            var time = $('#times_eid').val();
            var location_id = $('#location_eid').val();
            var times_id = $('#times_eid').val();
            var service_id = $('#service_eid').val();
            $('#date_eid_error').html("");
            $('#time_eid_error').html("");
            var cnt = 0;
            if (location_id == '') {
                $('#location_eid_error').html("Please select Location");
                cnt = 1;
            }
            if (service_id == null) {
                $('#service_eid_error').html("Please select Services");
                cnt = 1;
            }

            if (date.trim() == '') {
                $('#date_eid_error').html("Please select Appointment Date ");
                cnt = 1;
            }
            <?php if ($record->type == 'Caregiver') { ?>
                if (time.trim() == '') {
                    $('#time_eid_error').html("Please select Appointment Time");
                    cnt = 1;
                }
            <?php } else { ?>
                if (times_id.trim() == '') {
                    $('#time_eid_error').html("Please select Appointment Time");
                    cnt = 1;
                }
            <?php } ?>
            <?php if ($record->type == 'Caregiver') { ?>
                if (time.trim() != '') {
                    $.ajax({
                        async: false,
                        global: false,
                        type: "GET",
                        url: "{{ url('location/remaining-time-slot')}}",
                        data: {
                            "time": time,
                            'date': date
                        },
                        success: function(res) {
                            if (res == 1) {} else {
                                $('#time_eid_error').html("Slot limit over");
                                cnt = 1;
                            }
                        }
                    })

                }
            <?php } ?>
            if (cnt == 1) {
                return false
            } else {                
                return true;
            }

        });

        $('#formnewdocupload').submit(function(e) {

            var doc = $('#doc_image').val();
            $('#time_error').html("");
            var cnt = 0;

            if (doc.trim() == '') {
                $('#doc_images_error').html("Required");
                cnt = 1;
            }

            if (cnt == 1) {
                return false
            } else {
                return true;
            }

        });

        function getStatus1(status) {
            var notes_id = $('#notes_id').val();
            $('#notes_status_error').html("");
            $('#reason_id_status_error').html("");
            var cnt = 0;

            if (notes_id.trim() == '') {
                $('#notes_status_error').html("Required");
                return false;
            }

            $.ajax({
                async: false,
                global: false,
                url: "{{ url('/patient/statusUpdate')}}/{{ $record->id}}",
                type: "GET",
                data: {
                    status: status,
                    notes_id: notes_id,
                    agency_id: '{{ $record->agency_id }}',
                    'debugMode':'{{ $debugMode}}'

                },
                success: function(resp) {
                    if (resp == 1) {
                        var statuss = status;
                        if (status == 'Scheduled') {
                            statuss = 'Booked';
                        } else if (status == 'complete') {
                            statuss = 'Completed';
                        } else if (status == 'refused') {
                            statuss = 'marked as refused';
                        }
                        var msg = ' Appointment successfully ' + statuss;
                        toastr.success(msg);
                        location.reload();

                    } else {

                        toastr.error("Sorry, something went wrong. Please try again.");
                    }
                }

            })


        }

        function getStatusNew(status) {
            var notes_id = $('#notes_id_cancel').val();
            var reason_ids = $('#reason_ids').val();

            $('#notes_status_error').html("");
            $('#reason_id_status_error').html("");
            var cnt = 0;

            if (reason_ids == '') {
                $('#reason_id_status_error').html("Required");
                cnt = 1;
            }

            if (notes_id.trim() == '') {
                $('#notes_status_cancel_error').html("Required");
                cnt = 1;
            }
            
            if (cnt == 0) {
                $.ajax({
                    async: false,
                    global: false,
                    url: "<?php echo URL::to('/'); ?>/patient/statusUpdate/<?php echo $record->id; ?>",
                    type: "GET",
                    data: {
                        "status": status,
                        'notes_id': notes_id,
                        'reason_ids': reason_ids,
                        'agency_id': "{{ $record->agency_id}}",
                    },
                    success: function(resp) {

                        if (resp == 1) {
                            var statuss = status;
                            if (status == 'Scheduled') {
                                statuss = 'Booked';
                            } else if (status == 'complete') {
                                statuss = 'Completed';
                            }
                            var msg = ' Appointment successfully' + statuss;
                            toastr.success(msg);
                            location.reload();

                        } else {

                            toastr.error("Sorry, something went wrong. Please try again.");
                        }
                    }

                })
            }

        }

        function getTimeSearch() {
            var location_id = $('#location_id').val();
            var date_id = $('#date_id').val();
            var existId = <?php if ($record->appoinment_time_id != '') {
                                echo $record->appoinment_time_id;
                            } else {
                                echo '0';
                            } ?>;
            if (location_id != '' && date_id != '') {
                $.ajax({

                    url: "<?php echo URL::to('/'); ?>/location-schedule-search1",
                    type: "GET",
                    data: {
                        "location_id": location_id,
                        'start_time': date_id
                    },
                    success: function(resp) {
                        var json = JSON.parse(resp);
                        var htmls = '';
                        $('#timeid').html("");
                        if (json.length != 0) {
                            htmls = '<option value="">Select Appointment Time</option>';
                            $.each(json, function(i, v) {
                                var selected = '';
                                if (existId == v.id) {
                                    selected = 'selected="selected"';
                                }
                                htmls += '<option value="' + v.id + '" ' + selected + '>' + v
                                    .start_time + '-' + v.end_time + '(' + v.slots + ')' + '</option>'
                            });

                        } else {
                            htmls = '<option value="">No appointment schedule</option>'
                        }

                        $('#timeid').html(htmls);
                    }

                })

            }

        }
        function getTimeSearchForAgency() {
            var location_id = $('#location_eid').val();
            var date_id = $('#date_eid').val();
            var existId = <?php if ($record->appoinment_time_id != '') {
                                echo $record->appoinment_time_id;
                            } else {
                                echo '0';
                            } ?>;
            if (location_id != '' && date_id != '') {
                $.ajax({

                    url: "<?php echo URL::to('/'); ?>/location-schedule-search1",
                    type: "GET",
                    data: {
                        "location_id": location_id,
                        'start_time': date_id
                    },
                    success: function(resp) {
                        var json = JSON.parse(resp);
                        var htmls = '';
                        $('#time_eid').html("");
                        if (json.length != 0) {
                            htmls = '<option value="">Select Appointment Time</option>';
                            $.each(json, function(i, v) {
                                var selected = '';
                                if (existId == v.id) {
                                    selected = 'selected="selected"';
                                }
                                htmls += '<option value="' + v.id + '" ' + selected + '>' + v
                                    .start_time + '-' + v.end_time + '(' + v.slots + ')' + '</option>'
                            });

                        } else {
                            htmls = '<option value="">No appointment schedule</option>'
                        }

                        $('#time_eid').html(htmls);
                    }

                })

            }

        }
        var unavailableDates = '{{$disable_date}}';
        let properJson = JSON.parse(unavailableDates.replace(/&quot;/g, '"'));

        function unavailable(date) {
            var addZero = "";
            if(date.getDate() < "10"){
            addZero = 0;
            }
            dmy = addZero+""+date.getDate() + "-" + addZero+""+(date.getMonth() + 1) + "-" + date.getFullYear();
            console.log(dmy+"==="+$.inArray(dmy, properJson))
            if ($.inArray(dmy, properJson) == -1) {
                return [true, ""];
            } else {
                return [false, "", "Unavailable"];
            }
        }
        $('#date_eid').datepicker({
            //minDate:1,
            dateFormat: "mm/dd/yy",
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
            beforeShowDay: unavailable
        })
        $('#date_id').datepicker({
            //minDate:1,
            dateFormat: "mm/dd/yy",
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
            minDate:new Date(),
            beforeShowDay: unavailable
        })
        $('#patient_date').datepicker({
            dateFormat: "mm/dd/yy",
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
        })
        
        <?php if ($record->type == 'Caregiver' && $dates != '') { ?>
            getTimeSearch();
        <?php } ?>
        toastr.options.closeButton = true;
        toastr.options.tapToDismiss = false;
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "500",
            "timeOut": "3000",
            "extendedTimeOut": 0,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": false
        };

        var i = 0;
        
        function getModals(val) {
            var datatrar = '';
            if (val == 'booked') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'complete') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'cancel') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'noshow') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'checkin') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'processing') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'hospitalized') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'unableToContact') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }

            if (val == 'refused') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'pending') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'PendingTermination') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'Onhold') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'Onleave') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
            if (val == 'Terminated') {
                $('#' + val).attr('data-target', '#exampleModal-' + val);
            }
          
            $('#commons_flag').attr('onclick', 'getStatus1("' + val + '")');
            $('#Commsas').html(val);
            $('.commons').attr('id', 'exampleModal-' + val);
            $('.commons').click();

        }
        $("#due_date_id").datepicker({
            minDate: new Date(),
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
        });
        $("#telehealth_id").datepicker({
            minDate: new Date(),
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
            beforeShowDay: unavailable
        });
        
        $("#next_date_id").datepicker({
            minDate: new Date(),
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
            beforeShowDay: unavailable
        });
        
        function getDueDate() {
            var due_date = $('#due_date_id').val();
            var cnt = 0;
            $('#due_date_id_error').html("");
            if (due_date.trim() == '') {
                $('#due_date_id_error').html("Please enter Medical Due Date");
                cnt = 1;

            }
            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    url: "<?php echo URL::to('/'); ?>/patient/due-date",
                    type: "POST",
                    data: {
                        "due_date": due_date,
                        "_token": "<?php echo csrf_token(); ?>",
                        'patient_id': <?php echo $record->id; ?>,
                    },
                    success: function(resp) {

                        if (resp == 1) {

                            var msg = 'Medical Due date successfully updated';
                            toastr.success(msg);
                            location.reload();

                        } else {

                            toastr.error("Sorry, something went wrong. Please try again.");
                        }
                    }

                })
            }


        }


        function Undo(id) {
            var cons = confirm('Are you sure undo this record?');
            if (id != '' && cons == true) {
                $.ajax({
                    async: false,
                    global: false,
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/patient/undo/" + id,
                    success: function(res) {
                        if (res == 1) {
                            toastr.success('Action undone');
                            location.reload();
                        } else {
                            toastr.error("Sorry, something went wrong. Please try again.");
                        }

                    }
                })

            }

        }

        function getResponse(val) {
            if (val != '') {
                $('#dates_id').attr('style', 'display:none');
                $('#month_id').attr('style', 'display:none');
                if (val == 'EveryDate') {
                    $('#dates_id').attr('style', '');
                } else {
                    $('#month_id').attr('style', '');
                }
            }
        }

        function getReminder() {
            var remail = $('#remail').val();

            var rnotes = $('#rnotes').val();
            var rtype = $('input[name="rtype"]:checked').val();

            var rdates = $('#rdates').val();

            var rmonth = $('#rmonth').val();

            $('#remail_status_error').html('');
            $('#rnotes_status_error').html('');
            $('#rtype_error').html('');
            $('#date_error').html('');
            $('#every_month_error').html('');

            var cnt = 0;
            if (remail.trim() == '') {
                $('#remail_status_error').html('Please enter Email');
                cnt = 1;
            }

            if (rnotes.trim() == '') {
                $('#rnotes_status_error').html('Please enter Notes');
                cnt = 1;
            }
            if (rtype == '' || rtype == undefined) {
                $('#rtype_error').html('Please select Type');
                cnt = 1;
            }
            if (rtype != '' && rtype != undefined) {

                if (rtype == 'EveryDate') {
                    if (rdates == '') {

                        $('#rdate_error').html('Please select Date');
                        cnt = 1;
                    }
                } else {
                    if (rmonth == '') {
                        $('#every_month_error').html('Please select Month');
                        cnt = 1;
                    }
                }


            }

            if (cnt == 1) {
                return false;
            } else {
                var forn = $('#reminder_id')[0];
                var formData = new FormData(forn);
                formData.append("_token", "<?php echo csrf_token(); ?>");
                $.ajax({
                    async: false,
                    global: false,
                    url: "<?php echo URL::to('/'); ?>/patient/reminder",
                    type: "POST",

                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res == 1) {
                            toastr.success('Reminder successfully added');
                            $('#reminder_id')[0].reset();
                            $('#closed_id').click();
                            getReminderAction();

                        } else {
                            toastr.error('Sorry, something went wrong. Please try again.');
                        }

                    }
                })
            }
        }

        function getConvertDate(val) {
            $('.append_id').addClass('test_id');
            $('.append_id').html("");
            if (val != '') {
                $('.append_id').removeClass('test_id');
                var date = new Date();
                var newDate = new Date(date.setMonth(date.getMonth() + parseInt(val)));
                var dates = (newDate.getMonth() + 1) + '/' + newDate.getDate() + '/' + newDate.getFullYear();
                $('.append_id').html(dates);
            }

        }

        function getReminderAction() {
            $.ajax({
                async: false,
                global: false,
                url: "<?php echo URL::to('/'); ?>/patient/reminder-list/<?php echo $record->id; ?>",
                type: "get",

                processData: false,
                contentType: false,
                success: function(res) {
                    $('#remnid').html(res);
                }
            })
            return false;
        }
        getReminderAction();

        function isNumber(evt) {

            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

                return false;
            }
            return true;
        }

        function getNextAppointmentDate() {
            var due_date = $('#next_date_id').val();
            var cnt = 0;
            $('#next_date_id_error').html("");
            if (due_date.trim() == '') {
                $('#next_date_id_error').html("Please select Next Appointment Date");
                cnt = 1;

            }
            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    url: "<?php echo URL::to('/'); ?>/patient/next-appoinment-date",
                    type: "POST",
                    data: {
                        "appoinment_date": due_date,
                        "_token": "<?php echo csrf_token(); ?>",
                        'patient_id': <?php echo $record->id; ?>,
                    },
                    success: function(resp) {

                        if (resp == 1) {

                            var msg = ' Appointment date successfully updated';
                            toastr.success(msg);
                            $('#next_apid').html(due_date)
                            $('.close').click();
                        } else {

                            toastr.error("Sorry, something went wrong. Please try again.");
                        }
                    }

                })
            }

        }

        function getCompletedDate() {
            var due_date = $('#completed_date_id').val();
            var cnt = 0;
            $('#completed_date_id_error').html("");
            if (due_date.trim() == '') {
                $('#completed_date_id_error').html("Required");
                cnt = 1;

            }
            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    url: "<?php echo URL::to('/'); ?>/patient/completed-date",
                    type: "POST",
                    data: {
                        "completed_date": due_date,
                        "_token": "<?php echo csrf_token(); ?>",
                        'patient_id': <?php echo $record->id; ?>,
                    },
                    success: function(resp) {

                        if (resp == 1) {

                            var msg = ' Completed date successfully updated';
                            toastr.success(msg);
                            $('#completed_date_id').html(due_date)
                            $('#comp_id').html(due_date)
                            $('#closeds').click();
                        } else {

                            toastr.error("Sorry, something went wrong. Please try again.");
                        }
                    }

                })
            }

        }

        function getNyBestUpdate() {
            var assign_nybest_user = $('#assign_nybest_user').val();
            var notes_ny_id = $('#notes_ny_id').val();
            var selectedUser = $('#assign_nybest_user option:selected').text();
            var cnt = 0;
            $('#assign_nybest_user_error').html("");
            if (assign_nybest_user == '') {
                $('#assign_nybest_user_error').html("Required");
                cnt = 1;
            }

            if (cnt == 1) {
                return false;
            } else {
                $.ajax({

                    url: "{{ url('patient/assign-nybest-user') }}",
                    type: "POST",
                    data: {
                        "assign_nybest_user": assign_nybest_user,
                        "notes_ny_id": notes_ny_id,
                        "_token": "<?php echo csrf_token(); ?>",
                        'patient_id': <?php echo $record->id; ?>,
                    },
                    success: function(resp) {

                        if (resp == 1) {

                            var msg = ' NyBest user successfully assigned';
                            toastr.success(msg);
                            $('.nybest_user_id').html(selectedUser);
                            $('#assign_nybest_user option[value=' + assign_nybest_user + ']').attr('selected',
                                'selected');
                            $('.close').click();
                        } else {

                            toastr.error("Sorry, something went wrong. Please try again.");
                        }
                    }

                })
            }
        }

        function getuploadAttachment() {
            var attchmentPdf = $('#attchment_pdf')[0].files;
            $(".attchment_pdf_error").html("");
            if(attchmentPdf.length==0)
            {
                $(".attchment_pdf_error").html("Please select Attachment");
                return false;
            }
            var forn = $('#attachment_pdf_id')[0];
            var formData = new FormData(forn);
            formData.append("_token", "<?php echo csrf_token(); ?>");
            formData.append("id", <?php echo $record->id; ?>);
            $.ajax({
                async: false,
                global: false,
                url: "<?php echo URL::to('/'); ?>/patient/attachment-pdf",
                type: "POST",

                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 1) {
                        toastr.success(res.error_msg);
                        var url = '{{ asset("uploadedfiles/attachment/") }}/' + res.data.attachment;
                        $('#attachment_pdf_ids').html('<a href="' + url + '">' + res.data.attachment + '</a>');
                        $('#closeds').click();
                    } else {
                        toastr.error('Sorry, something went wrong. Please try again.');
                    }

                }
            })
        }



        $('#telehealthform').submit(function(e) {
            var telehealth_id = $('#telehealth_id').val();
            var telehealth_time_id = $('#telehealth_time_id').val();
            var cnt = 0;
            $('#telehealth_id_error').html("");
            $('#telehealth_time_id').html("");

            if (telehealth_id.trim() == '') {
                $('#telehealth_id_error').html("Please select Telehealth Appointment Date");
                cnt = 1;
            }
            if (telehealth_time_id.trim() == '') {
                $('#telehealth_time_id_error').html("Please select Telehealth Appointment Time");
                cnt = 1;
            }
            if (cnt == 1) {
                return false;
            } else {
                return true;
            }
        });

        function getPaymentNewStatus(e) {
            var payments_id = $('#payments_id').val();
            var payments_name = $('#payments_id option:selected').text();
            var cnt = 0;
            $('.payments_id_error').html("");
            if (payments_id == '') {

                $('.payments_id_error').html("Payment type is required");
                cnt = 1;
            }
            if (cnt == 1) {
                return false;
            } else {

                var newforms = $('#payment_method_id').serialize();

                $.ajax({
                    type: "POST",
                    url: "{{ url('patient/payment-type') }}",
                    data: newforms,
                    success: function(res) {
                        toastr.success(res.error_msg);
                        $('#payment_type_id').html("");
                        $('#payment_type_id').html(payments_name);
                        $('.close_p').click();

                    }
                })

            }

        }

        function getEditDocument(id, document_name) {
            $('.documens').html("Edit Document");

            $('#document_ids').val(id);
            $('#datenew_id').attr('readonly', true);
            $('#datenew_id').val(document_name);
        }

     
     

     

        function getDocumentType(agencyId, val) {
            $.ajax({
                async: false,
                global: false,
                url: "{{ url('hha-document') }}",
                data: {
                    'agencyId': agencyId,
                    'patientId':'{{$record->record_id}}'

                },
                success: function(response) {
                    
                    var res = response.data.length;
                    $('#document_hha_id').val(val);
                    
                    var htmlrs = '<option value="">Select Document Type</option>';
                    if (res != 0) {
                        $.each(response.data, function(i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }
                    $('#hha_document_type_id').html('');
                    $('#hha_document_type_id').html(htmlrs);

                }


            })
        }

        $('#send-hha-document-id').click(function(e) {
            var hha_document_type_id = $('#hha_document_type_id').val();
            var cnt = 0;
            $('#hha_document_type_id_error').html("");

            if (hha_document_type_id.trim() == '') {
                $('#hha_document_type_id_error').html("Required")
                cnt = 1;
            }

            if (cnt == 1) {
                return false;
            } else {
                var newForm = $('#formnew-hha')[0];
                var formData = new FormData(newForm);


                $.ajax({

                    url: "{{ url('send-hha-document') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                        $('#formnew-hha')[0].reset();
                        $('.close').click()
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg);
                    }


                })

            }
        })
        //HHA update document
        function getUploadDocument(val) {
            $('#upload_document_id').val(val);
        }

        $('.datepicker').datepicker({
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
        });


        
        function getOtherMedicalResult(agencyId, val) {
            $("#document_request_complience_id").val(val);
            $.ajax({
                async: false,
                global: false,
                url: "{{ url('hha/hha-other-compliances/hha-other-complience') }}",
                data: {
                    'agencyId': agencyId,
                    'patientId':'{{$record->id}}'

                },
                success: function(response) {
                    
                    var res = response.data.length;
                    // $('#document_hha_id').val(val);
                    var htmlrs = '<option value="">Select Complience Type</option>';
                    if (res != 0) {
                        $.each(response.data, function(i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }
                    $('#hha_document_complience_id').html('');
					$('#hha_document_complience_id').html(htmlrs);

                }


            })

         
            $.ajax({
				async: false,
				global: false,
				url: "{{ url('hha-document-type') }}",
				data: {
					'agencyId': agencyId,

				},
				success: function(response) {
					
					var res = response.data.length;
					//$('#document_hha_id').val(val);
					var htmlrs = '<option value="">Select Document Type</option>';
					if (res != 0) {
						$.each(response.data, function(i, v) {
							htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
						})
					}

					$('#hha_document_complience_type_id').html('');
					$('#hha_document_complience_type_id').html(htmlrs);
                    

				}


			})
        }

        function getMedicalResult(agencyId, val) {
            $("#document_request_id").val(val);
            $.ajax({
                async: false,
                global: false,
                url: "{{ url('hha-document') }}",
                data: {
                    'agencyId': agencyId,
                    'patientId':'{{$record->id}}'

                },
                success: function(response) {
                   

                    var res = response.data.length;
                    $('#document_hha_id').val(val);
                   
                    var htmlrs = '<option value="">Select Document Type</option>';
                    if (res != 0) {
                        $.each(response.data, function(i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }
                    $('#hha_document_medical_id').html('');
					$('#hha_document_medical_id').html(htmlrs);

                }


            })

            //get result name
            $.ajax({
                async: false,
                global: false,
                url: "{{ url('hha-caregiver-medical-results') }}",
                data: {
                    'agencyId': agencyId,
                    'id': val,
                    'patientId':'{{$record->id}}'

                },
                success: function(response) {
                   

                    var res = response.data.length;
                    $('#document_r_id').val(val);
                    
                    var htmlrs = '<option value="">Select Medical Result</option>';
                    if (res != 0) {
                        $.each(response.data, function(i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }
                    $('#hha_medical_result_id').html('');
                    $('#hha_medical_result_id').html(htmlrs);

                }


            })
            $.ajax({
				async: false,
				global: false,
				url: "{{ url('hha-document-type') }}",
				data: {
					'agencyId': agencyId,

				},
				success: function(response) {
					

					var res = response.data.length;
					//$('#document_hha_id').val(val);
					
					var htmlrs = '<option value="">Select Document Type</option>';
					if (res != 0) {
						$.each(response.data, function(i, v) {
							htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
						})
					}

					$('#hha_document_type_id').html('');
					$('#hha_document_type_id').html(htmlrs);
                    

				}


			})
        }
       var selectedArray = [];
       var selectedFlag = true;
       $('.upload-hhax').on("select2:select", function (e) { 
        selectedFlag = true;
        GetResultLIst(e.target.value)

       });
       $('.upload-hhax').on("select2:unselect", function (e) { 
            var selectedID = $('.upload-hhax').val();
            var temp = [];
            $.each(selectedArray,function(i,k){
                var findSelected = selectedID.find(o=>o==k);
                if(findSelected){
                    temp.push(k);
                }else{
                    $('#medical_result_'+k).remove();
                   
                }
            })
            selectedArray = temp;
            selectedFlag = false;

            if(selectedArray.length ==0){
                        $('#multipleMedicalResultId').attr('style','display:none');
                    }

        });
		function GetResultLIst(value){
         
           if(selectedFlag){
            var selectedID = $('.upload-hhax').val();
            var values =value;
            if(selectedArray.length !=0){
                $.each(selectedID,function(key,v){
                    var select = selectedArray.includes(v);
                    
                    if(!select){
                        selectedArray.push(v);
                        values = v;
                    }
                })

            }else{
                selectedArray.push(value)
            }
            
            var selectedText = '';
            var selectedTextData = $('.upload-hhax').select2("data");
          
            for (var i = 0; i <= selectedTextData.length-1; i++) {
                   
                    if(selectedTextData[i].id ==values){
                        selectedText = selectedTextData[i].text;
                    }
                }

			//get result name
			$.ajax({
				
				global: false,
				url: "{{ url('hha-caregiver-medical-results') }}",
				data: {
					'agencyId': "{{$record->agency_id}}",
					'id': "{{$record->id}}",
					'medicaid_id':values, 
                    'patientId':'{{$record->id}}'

				},
				success: function(response) {
					
					var res = response.data.length;
					
					
					var htmlrs = '<option value="">Select '+selectedText+' Result</option>';
					if (res != 0) {
						$.each(response.data, function(i, v) {
							htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
						})
					}

                    var selectHtml =`<div class="col-md-6"><div class="form-group" id="medical_result_${values}">
                                <label for="recipient-name" class="col-form-label">${selectedText} Results<span style="color:red">*</span>:</label>
                                    <select name="hha_medical_result[${values}]" class="form-control" id="hha_medical_result_id${values}">${htmlrs}</select>
                                    <span id="hha_medical_result_id_${values}_error" style="color:red" class="error"></span>
                            </div></div>`;
                    if(selectedArray.length ==1){
                        $('#multipleMedicalResultId').attr('style','');
                    }
                    
                    $('#multipleMedicalResultId').append(selectHtml)
					// $('#hha_medical_result_id').html('');
					// $('#hha_medical_result_id').html(htmlrs);

				}


			});
           }
			

		}

        
        function GetComplienceResultLIst(value){
			var selectedID = $('.hha_complience_id').val();
            var values =value;
        
            if(selectedComplienceArray.length !=0){
                $.each(selectedID,function(key,v){
                    var select = selectedComplienceArray.includes(v);
                    
                    if(!select){
                        selectedComplienceArray.push(v);
                        values = v; 
                    }
                })

            }else{
                selectedComplienceArray.push(value)
            }
            
            var selectedText = '';
            var selectedTextData = $('.hha_complience_id').select2("data");
          
            for (var i = 0; i <= selectedTextData.length-1; i++) {
               
                if(selectedTextData[i].id ==values){
                    selectedText = selectedTextData[i].text;
                }
            }
		
			$.ajax({
				
				global: false,
				url: "{{ url('hha-complience-medical-results') }}",
				data: {
					'agencyId': "{{$record->agency_id}}",
					'id': "{{$record->id}}",
					'medicaid_id':values, 

				},
				success: function(response) {
					
					var res = response.data.length;
					
					
					var htmlrs = '<option value="">Select '+selectedText+' Result</option>';
					if (res != 0) {
						$.each(response.data, function(i, v) {
							htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
						})
					}
					
                    var selectHtml =`<div class="col-md-6"  id="medical_complience_${values}"> <div class="form-group">
                                <label for="recipient-name" class="col-form-label">${selectedText} Results<span style="color:red">*</span>:</label>
                                    <select name="hha_medical_result[${values}]" class="form-control" id="hha_complience_result_id${values}">${htmlrs}</select>
                                    <span id="hha_complience_result_id_${values}_error" style="color:red" class="error"></span>
                            </div></div>`;
                    if(selectedComplienceArray.length ==1){
                        $('#multipleComplienceResultId').attr('style','');
                    }
                    
                    $('#multipleComplienceResultId').append(selectHtml)

				}


			});

		}
        $('#update-hha-document-id').click(function(e) {
            $('#loadersId').attr('style', 'display:block');
            $('#update-hha-document-id').attr('disabled', 'disabled');
            var hha_document_result_id = $('.upload-hhax').val();
            var hha_document_type_id = $('#hha_document_type_id').val();
            var completed_date = $('#completed_date').val();
            var cnt = 0;
            $('#hha_document_medical_id_error').html("");
            $('#completed_date_error').html("");
            $('#hha_document_type_id_error').html("");
            $('#hha_due_date_div_error').html("");
            if (hha_document_type_id.trim() == '') {
                $('#hha_document_type_id_error').html("Required")
                cnt = 1;
            }
            if (hha_document_result_id.length == 0) {
                $('#hha_document_medical_id_error').html("Required")
                cnt = 1;
            }

            if(hha_document_result_id.length !=0){
                $.each(hha_document_result_id,function(i,v){
                    var hha_medical_result_ids = $('#hha_medical_result_id'+v).val();
                    $('#hha_medical_result_id_'+v+'_error').html("");
                    if(hha_medical_result_ids ==''){
                        $('#hha_medical_result_id_'+v+'_error').html("Required");
                        cnt = 1;
                    }
                })
            }
            if (completed_date.trim() == '') {
                $('#completed_date_error').html("Required")
                cnt = 1;
            }



            if (cnt == 1) {
                $('#loadersId').attr('style', 'display:none;');
                $('#update-hha-document-id').removeAttr('disabled');
                return false;
            } else {
                var newForm = $('#formnew-hha-update')[0];
                var formData = new FormData(newForm);


                $.ajax({

                    url: "{{ url('update-hha-document') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        toastr.success(response.error_msg);
                        $('#loadersId').attr('style', 'display:none;');
                       
                        $('#update-hha-document-id').removeAttr('disabled');
                        clearData();
                        // $('#formnew-hha-update')[0].reset();
                        // $('.close').click()
                    //    window.location.reload()
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg);
                        $('#loadersId').attr('style', 'display:none;');
                        $('#update-hha-document-id').removeAttr('disabled');
                    }


                })

            }
        })


    
        $('#update-hha-complience-id1').click(function(e) {
            $('#loadersId').attr('style', 'display:block');
            $('#update-hha-complience-id').attr('disabled', 'disabled');
            // var hha_document_result_id = $('#hha_complience_result_id').val();
            var hha_document_type_id = $('#hha_document_complience_type_id').val();
            var completed_date = $('#completed_date_complience').val();
            var hha_document_complience_id = $('#hha_document_complience_id').val();
            var cnt = 0;
            $('#hha_complience_result_id_error').html("");
            $('#complience_completed_date_error').html("");
            $('#hha_document_complience_type_id_error').html("");
            
            if (hha_document_type_id.trim() == '') {
                $('#hha_document_complience_type_id_error').html("Required")
                cnt = 1;
            }
            // if (hha_document_result_id.trim() == '') {
            //     $('#hha_complience_result_id_error').html("Required")
            //     cnt = 1;
            // }
            if (completed_date.trim() == '') {
                $('#complience_completed_date_error').html("Required")
                cnt = 1;
            }

            if(hha_document_complience_id.length ==0){
                $('#hha_document_complience_id_error').html("Required")
                cnt = 1;
            }

            if(selectedComplienceArray.length !=0){
                $.each(selectedComplienceArray,function(key,v){
                    var hha_complience_result_id = $('#hha_complience_result_id'+v).val();
                    $('#hha_complience_result_id_'+v+'_error').html("");
                    if(hha_complience_result_id ==''){
                        $('#hha_complience_result_id_'+v+'_error').html("Required");
                        cnt = 1;
                    }
                })
            }



            if (cnt == 1) {
                $('#loadersId').attr('style', 'display:none;');
                $('#update-hha-complience-id').removeAttr('disabled');
                return false;
            } else {
                var newForm = $('#formnew-other-compienece-hha-update')[0];
                var formData = new FormData(newForm);

                $.ajax({

                    url: "{{ url('update-complience-document') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        toastr.success(response.error_msg);
                        $('#loadersId').attr('style', 'display:none;');
                        $('#update-hha-complience-id').removeAttr('disabled');
                        hideOtherComplianceToHHXDocument();
                        // $('#formnew-other-compienece-hha-update')[0].reset();
                        // $('.close').click()
                    //    window.location.reload()
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg);
                        $('#loadersId').attr('style', 'display:none;');
                        $('#update-hha-complience-id').removeAttr('disabled');
                    }


                })

            }
        })
    </script>
    <script>
        $(document).on('click', '.log-pegination .pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });
        $(document).ready(function() {
            $('#loadertag').show();
            getData(1);
        });

        function getData(page) {

            var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

            $.ajax({
                method: 'GET',
                url: "{{ url('appointment-view-logs') }}" + "?page=" + page,
                data: {
                    'id': "{{ $record->id }}",
                    '_token': "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $('#loadertag').show();
                },
                success: function success(response) {

                    $('#loadertag').hide();
                    $('#logList').html("");
                    $('#logList').html(response);
                },
                error: function error(_error) {
                    console.error(_error);
                    toastr.error('Something happened. Try again');
                }
            });
        }

        
        function deleteRecordPatient(id) {
            var url = "{{url('patient/delete/')}}";
            $.confirm({
                title: 'Delete',
                columnClass: "col-md-6",
                content: 'Are you sure delete record?',
                buttons: {
                    formSubmit: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                           window.location.href=url+'/'+id;
                        }
                    },
                    cancel: function() {
                        //close
                    },
                },
            });
        }

        
        function deleteRecordDocument(recordId,documentId) {
            var url = "{{url('patient/document-delete/')}}";
            $.confirm({
                title: 'Delete',
                columnClass: "col-md-6",
                content: 'Are you sure delete record?',
                buttons: {
                    formSubmit: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                           window.location.href=url+'/'+recordId+'/'+documentId;
                        }
                    },
                    cancel: function() {
                        //close
                    },
                },
            });
        }
        function  refresh(){
            var id = "{{ ($record->link_hha_caregiver !='')?$record->link_hha_caregiver:$record->hha_id }}";
            $.ajax({
                url: "{{ url('patient/sync') }}?id=" + id,
                type: "GET",

                success: function(res) {
                    

                }
            });
            return false;
        }

        function    refreshHHA(){
            $('#loadertag121').attr('style','');
            $('#chat-messages-news').html("");
            $('#chat-messages-news-dataTable').dataTable().fnDestroy();
            var id = "{{ ($record->link_hha_caregiver!='')?$record->link_hha_caregiver:$record->hha_id }}";
            $.ajax({
                url: "{{ url('hha-caregiver/notes') }}?id=" + id,
                type: "GET",

                success: function(res) {
                    var response = '';
                    var  json = res.data;
                    $('#loadertag121').attr('style','display:none');
                    
                    if(json.length !=0){
                        var cnt =1;
                        $.each(json,function(i,v){
                            response +='<tr id="msg-'+v.CaregiverNoteID+'"><td>'+cnt+++'</td><td>'+v.Note+'</td><td>'+v.NoteDate+'</td></tr>'
                           
                        })
                    }

                    
                    $('#chat-messages-news').html(response);
                 
                    $('#chat-messages-news-dataTable').dataTable({
                        "bInfo": false,
                        'bSort': false,
                        "pageLength": 10, 
                        'searching':false,
                    });
                    $('.dataTables_length').attr('style','display:none')
                }
            });
            return false;
        }
       
        $('#hhaCaregiverSave').click(function(e){
            var hha_caregivers_notes = $('#hha_caregivers_notes_id').val();
            var subjectId = $('#subjectId').val();

            var cnt = 0;
            $('#hha_caregivers_notes_id_error').html("");
            $('#hha_subject_id_error').html("");
           
            if (hha_caregivers_notes.trim() == '') {
                $('#hha_caregivers_notes_id_error').html("Please enter Notes");
                cnt = 1;
            }
            if (subjectId == '') {
                $('#hha_subject_id_error').html("Please select Subject");
                cnt = 1;
            }
            if (cnt == 1) {
                return false;
            } else {
                var id = "{{ ($record->link_hha_caregiver!='')?$record->link_hha_caregiver:$record->hha_id }}";
                var forn = $('#hha_caregivers_notes')[0];
                var formData = new FormData(forn);  
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("hha_caregivers_notes", hha_caregivers_notes);
                formData.append("subject_id", subjectId);
                formData.append("id", id);
                $.ajax({
                    url: "{{ url('hha-caregiver/create-notes') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success('Notes successfully added');
                        $('#hha_caregivers_notes')[0].reset();
                        $('#exampleModal-notes').modal('hide');
                        refreshHHA();
                    },error: function(xhr, status, error)
                        {
                            toastr.error(xhr.responseJSON.message);
                        }
                })
            }
        })
        
        function getHHACaregiverSubject(){
            var id = "{{ ($record->link_hha_caregiver!='')?$record->link_hha_caregiver:$record->hha_id }}";
            $.ajax({
                url: "{{ url('hha-caregiver/subject') }}?id=" + id,
                type: "GET",
                success: function(res) {
                    var json  = res.data;
                    var option ="";
                    if(json.length !=0){
                        option ='<option value="">Select Subject</option>';
                        $.each(json,function(i,v){
                            option +='<option value="'+v.ID+'">'+v.Name+'</option>';
                        })
                    }

                    $('#subjectId').html("");
                    $('#subjectId').html(option);
                    $('#exampleModal-notes').modal('show');
                }

            });
        }

        function refreshMedical(){
            // var id = "{{ ($record->hha_id !='')?$record->hha_id:$record->link_hha_caregiver }}";
            // $.ajax({
            //     url: "{{ url('hha-caregiver-medical') }}?id=" + id,
            //     type: "GET",
            //     success: function(res) {
            //         toastr.success(res.message)
                    
            //     }

            // });
            getMedicalalList();
                   
        }

        function getMedicalalList(){
            var hha_status_id   =$('#hha_status_id').val();
            var id = "{{ ($record->link_hha_caregiver!='')?$record->link_hha_caregiver:$record->hha_id }}";
            $.ajax({
                url: "{{ url('hha-caregiver-medical-ajax') }}",
                type: "GET",
                data:{
                    status:hha_status_id,
                    id:id
                },
                success: function(res) {
                    var json  = res.data;
                    var htmlResponse = '';
                    if(res.data.length !=0){
                        var cnt =1;
                        $.each(json,function(i,v){
                            var datePerform = "";
                            if(v.date_perform !=""){
                                datePerform =moment(v.date_perform).format("MM/DD/YYYY");
                            }
                            htmlResponse +='<tr><td>'+cnt+'</td><td>'+v.medical_name+'</td><td>'+v.status+'</td><td>'+moment(v.due_date).format("MM/DD/YYYY")+'</td><td>'+datePerform+'</td></tr>'
                            cnt++;
                        })
                    }else{ 
                        htmlResponse = '<tr><td colspan="4">'+res.message+'</td></tr>'
                    }
                    $('#tbody_id').html("");
                    $('#tbody_id').html(htmlResponse);
                }

            });
        }

        function refreshOtherCompliance(){
            $('#loadertag1211').attr('style','');
            var id = "{{ ($record->link_hha_caregiver!='')?$record->link_hha_caregiver:$record->hha_id }}";
            $.ajax({
                url: "{{ url('get-hha-other-compliance') }}?id=" + id+"&agency_fk={{$record->agency_id}}",
                type: "GET",
                success: function(res) {
                    var json = res.data;    
                    var htmlResponse = '';
                    if(json.length !=0){
                        var cnt =1;
                        $.each(json,function(i,v){
                            htmlResponse +='<tr><td>'+cnt+++'</td><td>'+v.medical_name+'</td><td>'+v.status+'</td><td>'+moment(v.due_date).format('MM/DD/YYYY')+'</td></tr>'
                        })
                    }else{
                        htmlResponse = '<tr><td colspan="3">No record available</td></tr>'
                    }
                    $('#loadertag1211').attr('style','display:none');
                    $('#tbody_compliance_id').html("");
                    $('#tbody_compliance_id').html(htmlResponse);
                    //
                   
                }

            });
        }

        function getInService(){
            var id = "{{ ($record->link_hha_caregiver!='')?$record->link_hha_caregiver:$record->hha_id }}";
            $.ajax({
                url: "{{ url('hha-caregiver-inservice') }}?id=" + id,
                type: "GET",
                success: function(res) {
                    var json =res.data;
                   var htmlResponse = '';
                   $('#caregiver_inservice_id').html("");
                    if(json.length !=0){
                        var cnt =1;
                        $.each(json,function(i,v){
                            htmlResponse +='<tr><td>'+cnt+++'</td><td>'+v.topic_name+'</td><td>'+v.inservice_date+'</td><td>'+v.from_time+'</td><td>'+v.end_time+'</td><td>'+v.description+'</td></tr>'
                           
                        })
                        
                        
                    }else{
                        htmlResponse ='<tr><td colspan="5">No Record Available</td></tr>'  
                    }

                    $('#caregiver_inservice_id').html(htmlResponse);
                    $('#caregiver_inservice_datatable').dataTable().fnDestroy();
                   $('#caregiver_inservice_datatable').dataTable({
                    "bInfo": false,
                    'bSort': false,
                    "pageLength": 10, 
                    'searching':false,
                   });
                   $('.dataTables_length').attr('style','display:none')
                }

            });
        }

        function linkHHACaregiver(){
            $('#exampleModal-link-hha').modal('show');
        }
        
        function getHhxProfile(){
            var hha_profile_id =  $('#hha_profile_id').val();
            $('.hha_profile_error').html("");
            var cnt =0;
            if(hha_profile_id ==''){
                $('.hha_profile_error').html("Caregiver Link is required");
                cnt =1;
            }

            if(cnt ==1){
                return false;
            }else{
                $.ajax({
                    type:"post",
                    url:"{{ url('patient/link-to-caregiver') }}",
                    data:{
                        'patient_id':'{{ $record->id}}',
                        'agency_id':'{{ $record->agency_id}}',
                        'hha_profile_id':hha_profile_id,
                        'dataTypeId':$('#dataTypeId').val(),
                        '_token':'{{ csrf_token()}}'
                    },
                    success:function(res){
                        toastr.success(res.message);
                        var fullName = res.data.first_name+' '+res.data.last_name+' ( '+res.data.caregiver_code+')';
                        $('#hhx_caregiver_id').html(fullName);
                        $('#lnkhhx_pdf_id')[0].reset();
                        $('#hha_caregiver_ids').val(res.data.caregiver_id);
                        $('#hha_caregiver_names').val(fullName);
                        $('#closedsNew').click();
                    },
                    error:function(xhr){
toastr.error(xhr.responseJSON.message);
                    }
                })
            }
        }
        $('#exampleModal-link-hha').bind('hide', function () {
            $('#lnkhhx_pdf_id')[0].reset();
            $('.token-input-delete-token').click()
        });


        function  loadAllTextMessages(){
            $('.text-notes-messages').html("");
            $('#loadertag1').attr('style', '');
            

            var agency_id = '<?php echo $record->agency_id; ?>';

            $.ajax({
                url: "<?php echo URL::to('/'); ?>/delete-patient/get-sms-text",
                type: "get",
                data: {
                   
                    'case_id': '{{  $record->id  }}'
                },
                success: function(response) {

                    var response    =response.data;
                    if(response.length ==0){
                        $('.text-notes-messages').html("<p class='text-center'><span class='msg-block'><strong>Data Not Found</strong></span></p>")
                    }
                    response.forEach(element => {
                        
                        add_message_obj_new(element.id, element.user_details.first_name,
                            'https://web.exmedc.com/img/demo/av1.jpg', element.message, element
                            .created_date, element.type, element.user_details.id,"",element.case_id);

                    });
                    setTimeout(()=>{
                        $('#loadertag1').attr('style', 'display:none;');
                    },3000)
                    
                    // add_message('You', 'img/demo/av1.jpg', input.val(), true);
                    // You will get response from your PHP page (what you echo or print)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
            return false;
        }

        function add_message_obj_new(mid, name, img, msg, date, type, sender_id, clear,caseId) {
            //alert(sender_id);
            i = i + 1;

            var inner = $('.text-notes-messages');
            var time = new Date(date);
            var date = (time.getMonth() + 1) + '/' + time.getDate() + '/' + time.getFullYear();

            var hours = time.getHours();
            var minutes = time.getMinutes();
            if (hours < 10) hours = '0' + hours;
            if (minutes < 10) minutes = '0' + minutes;
            var id = 'msg-' + i;
            //  var type="Receive";
            var ondelete = '';
            var recordId = "{{ $record->id}}";

            var idname = "";
            var tags ="";

            if(caseId != recordId){
                tags =`<span style="margin-left:10px;top: 0;background: #00BBE0;padding: 1px 5px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;">Merge</span>`;
            }
            inner.append('<p id="' + id + '" class="user-' + idname + '">' +
                '<span class="msg-block"><strong>' + name + '</strong>'+tags+'<span class="time"> ' + date +
                ' ' + hours + ':' + minutes + '</span>' +
                '<span class="msg">' + msg + '<span class="pull-right">' + ondelete + '</span></span></span></p>');
            $('#' + id).hide().fadeIn(800);
            if (clear) {
                $('.text-chat-message textarea').val('').focus();
            }
            $('#text-sms-messages').animate({
                scrollTop: inner.height()
            }, 20);
        }

        function  sendTextMessagefile(){
            var alldata = new FormData($('#textMessageSubmits')[0]);
            var id = <?php echo $record->id; ?>;
            var name = "you";
            var mobile = '<?php echo $record->mobile; ?>';
            var message = $('#smsTextMessage').val();

            alldata.append('mobile','{{ $record->mobile }}');
            alldata.append('case_id',id);
            alldata.append('message',message);
            alldata.append('_token','{{   csrf_token()  }}');
            if (id != 0 && message != "") {
                $.ajax({
                    type: 'POST',
                    data: alldata,
                    url: "<?php echo URL::to('/'); ?>/patient/text-message-notes",
                    dataType: "json",
                    mimeType: "multipart/form-data",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        $('#textMessageSubmits')[0].reset();
                        var response=response.data;
                        i = i + 1;

                    var inner = $('.text-notes-messages');
                    var time = new Date(response.created_date);
                    var date = (time.getMonth() + 1) + '/' + time.getDate() + '/' + time.getFullYear();

                    var hours = time.getHours();
                    var minutes = time.getMinutes();
                    if (hours < 10) hours = '0' + hours;
                    if (minutes < 10) minutes = '0' + minutes;
                    var id = 'msg-' + Math.floor(Math.random() * 1000000);
                    //  var type="Receive";
                    var ondelete = '';


                    var idname = "";
                    inner.append('<p id="' + id + '" class="user-' + idname + '">' +
                        '<span class="msg-block"><strong>' + response.user_details.first_name + '</strong><span class="time"> ' + date +
                        ' ' + hours + ':' + minutes + '</span>' +
                        '<span class="msg">' + response.message + '<span class="pull-right">' + ondelete + '</span></span></span></p>');
                    $('#' + id).hide().fadeIn(800);
                   
                    $('#text-sms-messages').animate({
                        scrollTop: inner.height()
                    }, 20);
                    
                       // addSMSmessage('You', 'Send', message, "", true);
                        // You will get response from your PHP page (what you echo or print)
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        toastr.error(jqXHR.responseJSON.error_msg)
                    }
                });
            }else{
                $('#smsTextMessageError').html("Required");
                return  false;
            }
            
        }

        function clearData(){
            $('.error').html("");
            $('#formnew-hha-update')[0].reset();
            $('#multipleMedicalResultId').html("");
        }

       
        $('.hha_complience_id').on("select2:select", function (e) { 
          
            GetComplienceResultLIst(e.target.value)

        });

        $('.hha_complience_id').on("select2:unselect", function (e) { 
            var selectedID = $('.hha_complience_id').val();
            var temp = [];
            $.each(selectedComplienceArray,function(i,k){
                var findSelected = selectedID.find(o=>o==k);
                if(findSelected){
                    temp.push(k);
                }else{
                    $('#medical_complience_'+k).remove();
                }
            })
            selectedComplienceArray = temp;
           

            if(selectedComplienceArray.length ==0){
                $('#multipleMedicalResultId').attr('style','display:none');
            }

        });

        function hideOtherComplianceToHHXDocument(){
            $('#multipleComplienceResultId').html("");
            $('#formnew-other-compienece-hha-update')[0].reset();
        }
        var userid='{{$user->id}}';

        function combineRecord(){
            var appointment_id =  $('#appointment_id').val();
            $.confirm({
                title: 'Confirmation',
                columnClass: "col-md-6",
                content: 'Are you sure you want to merge the record <b>' +  appointment_id  +'</b> to <b>'+_RECORD_ID+'</b>?',
                buttons: {
                    formSubmit: {
                        text: 'Yes',
                        btnClass: 'btn-primary',
                        action: function() {
                            $('#appointment_id_error').html("");
                            var cnt =0;
                            if(appointment_id.trim() ==''){
                                $('#appointment_id_error').html("Chart Id is required");
                                cnt =1;
                            }

                            if(cnt ==1){
                                return false;
                            }else{
                                $.ajax({
                                    type:"post",
                                    url:"{{ url('patient/combine-appointment') }}",
                                    data:{
                                        'record_id':'{{ $record->id}}',
                                        'appointment_id':appointment_id,
                                        '_token':'{{ csrf_token()}}'
                                    },
                                    success:function(res){
                                        toastr.success(res.error_msg);
                                        location.reload();
                                       
                                    },
                                    error:function(xhr){
                                        toastr.error(xhr.responseJSON.error_msg);
                                    }
                                })
                            }
                        }
                    },
                    cancel: {
                        'text' : 'No'
                    },
                },
            });
        }

        function hideCombineAppointment(){
            $('#exampleModal-merge-record').modal('hide');
            $('.error').html("");
        }
        function inserviceRecord(){
            var inservice_id =  $('#inservice_id').val();
            $('#inservice_id_error').html("");
            var cnt =0;
            if(inservice_id ==''){
                $('#inservice_id_error').html("In Service Date is required");
                cnt =1;
            }

            if(cnt ==1){
                return false;
            }else{
                $.ajax({
                    type:"post",
                    url:"{{ url('patient/inservice-appointment') }}",
                    data:{
                        'record_id':'{{ $record->id}}',
                        'inservice_id':inservice_id,
                        '_token':'{{ csrf_token()}}'
                    },
                    success:function(res){
                        toastr.success(res.error_msg);
                        $('#inservices_status').html(res.data.inservice_status);
                        $('#inservices_dates').html(res.data.inservice_datetime);
                       
                        hideInServiceAppointment();
                    }
                });
            }
        }

        function  hideInServiceAppointment(){
            $('#inservice_id').val("");
            $('#exampleModal-inservice-record').modal('hide');
            $('.error').html("");
        }
        function uploadPatientDocToHHA(agencyId, val) {
            $("#main_id").val(val);

            $.ajax({
				async: false,
				global: false,
				url: "{{ url('hha-patient-document-type') }}",
				data: {
					'agencyId': agencyId,

				},
				success: function(response) {
                    
					var res = response.data.length;
					
					var htmlrs = '<option value="">Select Document Type</option>';
					if (res != 0) {
						$.each(response.data, function(i, v) {
							htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
						})
					}
					$('#hha_patient_document_type_id').html('');
					$('#hha_patient_document_type_id').html(htmlrs);
                
				}
			})
        
        }
        $('#update-hha-document-patient-btn').click(function (){

            var HHXDocumentType  = $('#hha_patient_document_type_id').val();
            var date = $('#completed_date_patient').val();
            var cnt = 0;


            if (HHXDocumentType == '') {
                $('#doc_error').html("Please Select HHX Document Type");
                cnt = 1;
            }
            

            if (cnt == 1) {
                return false;
            }else{
                var forms = $('#update-hha-document-patient')[0];
                var newForms = new FormData(forms);
                newForms.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ url('update-hha-document-patient')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg[0]);
                        clearDataHHA();
                        
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg[0]);
                    }
                });
            }

            });

            function clearDataHHA(){
                $('.error').html("");
                $('#exampleModal-hha-update-patient').modal('hide');
                $('#update-hha-document-patient')[0].reset();
            
            } 
            function smsLogs(page){
                var url = '{{ url("delete-sms-logs-list")}}/{{$record->id}}';
                $.ajax({
                    url: url
                    + "?page=" + page,
                    type: "GET",
                    
                    success: function(res) {
                        $('#sms_logs_id').html("");
                        $('#sms_logs_id').html(res);
                            
                                    
                    }

                });
            }
        $('#alaycare-popup').click(function(){
            $('#lnkhhx_alaycare_id')[0].reset();
            $('.token-input-list').remove()
            $('#hha_alaycare_id').html("");
            $('#hha_alaycare_name').html("");
            $('.token-input-delete-token').click()
            alaycareFunction();
        });

         var empId = '{{ $record->alaycare_id }}';
         var empName = '{{ $record->alaycare_name }}';
        function alaycareFunction(){
          
            var urlToken =  "{{ url('alaycare-emp-data') }}?alaycare_id="+empId; 
            
            $("#hha_alaycare_id").tokenInput(urlToken, {
                
                tokenLimit: 1,
                zindex: 9999,
                prePopulate: empId !== "" && empName !== "" ? [{ id: empId, name: empName }] : [],
                onAdd: function (item) {
                    
                var selectedAlaycareId = item.emp_id;
                var name = item.name;
                    $('#hha_alaycare_id').val(selectedAlaycareId);
                    $('#hha_alaycare_name').val(name);
                    
                },
                
                
                
            });
        }

        function CloseEmployeePopup(){
            $('.hha_alaycare_id_error').html("");
            $('#lnkhhx_alaycare_id')[0].reset();
            $('.token-input-list').remove();
            $('.token-input-delete-token').click()
        }


        $('#update-alaycare-id').click(function(){
            var alaycareId =  $('#hha_alaycare_id').val();
            var name =  $('#hha_alaycare_name').val();
            
            $('.hha_alaycare_id_error').html("");
            var cnt =0;
            if(alaycareId ==''){
                $('.hha_alaycare_id_error').html("Please Select Employee");
                cnt =1;
            }
            if(cnt ==1){
                return false;
            }else{
                
                $.ajax({
                    type:"post",
                    url:"{{ url('patient/update-alaycare-id') }}",
                    data:{
                        'patient_id':'{{ $record->id}}',
                        'alyacare_id':alaycareId,
                        'name':name,
                        '_token':'{{ csrf_token()}}'
                    },
                    success:function(res){
                     
                        toastr.success(res.error_msg);
                        $('#lnkhhx_alaycare_id')[0].reset();
                        $('#exampleModal-link-alaycare-id').modal('hide');
                        $('.token-input-delete-token').click()
                        $('#hhx_alaycare_id').html('');
                        $('.token-input-list').remove();
                        var fullName = res.data[0].alaycare_name + ' (' + res.data[0].alaycare_id + ')';
                        var patientId = res
                        empId = res.data[0].alaycare_id;
                        empName = res.data[0].alaycare_name;
                        $('#hhx_alaycare_id').html(fullName);
                        
                      
                       
                    },
                    error:function(xhr){
                        toastr.error(xhr.responseJSON.message);
                    }
                })
            }

        });

</script>
<script>
        function alayacareAjax(){
            $('#branchdata').html(''); 
            $.ajax({
                url: "/get-branch-alaycare-ajax",
                type: "get",
                
                success: function(response) {
                  
                     $.each(response.data.items, function(index, value) {
                        var optionElement = $('<option>').attr('value', value.id).text(value.name);
                        $('#branchdata').append(optionElement); 
                    });
                    
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });

            $('#alayacare-popup').modal('show');
            $('#groupdatadiv').hide();
        }

        
        $(document).on('change', '#alayacare-popup .modal-body select', function() {
            var selectedValue = $(this).val();
            getGroupbyBranchId(selectedValue);
        });

        function getGroupbyBranchId(branchId){
            if(branchId){
                
                $('#groupdata').html('');
                $.ajax({
                url: "/get-group-by-branch-id",
                type: "get",
                data: {
                    branchId: branchId,
                },
                success: function(response) {
                   
                    $.each(response.data.items, function(index, value) {
                        var optionElement = $('<option>').attr('value', value.id).text(value.name);
                        $('#groupdata').append(optionElement); 
                    });
                    
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
            }else{
                return false;
            }
            
            
        }

        function alayacareSubmit(){
            var branchId = $("#branchdata").val();
            var groupId = $("#groupdata").val();
            var patient_id = $('#alaycare-patient-id').val();
            
            
            if (branchId === "" ) {
                $('#branchIderror').html('please select Branch');
                return false;
            }else if(groupId === ""){
                $('#branchIderror').html('');
                $('#groupIderror').html('please select Group');
                return false;
            }else{
                $('#branchIderror').html('');
                $('#groupIderror').html('');
                var newforms = $('#alayacare-form-data').serialize();
                $.ajax({
                    type: "post", 
                    url: "{{ url('alayacare-post')}}",
                    
                    data:newforms,
                    success: function (response) {
                        $('#alayacare-popup').modal('hide');
                        $("#alayacare-form-data")[0].reset();
                        toastr.success(response.error_msg);
                    },
                    error: function (error) {
                        toastr.success(response.error_msg);
                    }
                });
            }
        }

        function clearDataModal(){
            $("#alayacare-form-data")[0].reset();
        }
        
        $('#update-inservice-status').click(function(e){
            var inservice_status = $("#inservice_status").val();
            var ct=0;
            $('.inservice_status_error').html("");
            if(inservice_status==''){
                $('.inservice_status_error').html("Required");
                ct=1;
            }

            if(ct   ==1){
                return  false;
            }else{
                $.ajax({
                    async:false,
                    global:false,
                    type: "post", 
                    url: "{{  url('update-inservice')  }}",
                    
                    data:{
                        '_token':"{{  csrf_token()  }}",
                        'patient_id':"{{  $record->id  }}",
                        'inservice_status':inservice_status
                    },
                    success: function (response) {
                        $('#inservices_status').html(inservice_status)
                        toastr.success(response.error_msg);
                        CloseInserviceStatus();
                    },
                    error: function (error) {
                        toastr.error(response.error_msg);
                    }
                });
            }
        })

        function  CloseInserviceStatus(){
            $('.error').html("");
            $('#exampleModal-inservice_status').modal('hide');
        }


        
        function  CloseTrainingStatus(){
            $('#exampleModal-training_status').modal('hide');
        }

        $('#update-training-status').click(function(e){
            var inservice_status = $("#training_status").val();
            var ct=0;
            $('.training_status_error').html("");
            if(inservice_status.trim() ==''){
                $('.training_status_error').html("Required");
                ct=1;
            }

            if(ct   ==1){
                return  false;
            }else{
                $.ajax({
                    async:false,
                    global:false,
                    type: "post", 
                    url: "{{  url('update-training')  }}",
                    
                    data:{
                        '_token':"{{  csrf_token()  }}",
                        'patient_id':"{{  $record->id  }}",
                        'training_status':inservice_status
                    },
                    success: function (response) {
                        $('#training_statuss').html(inservice_status)
                        toastr.success(response.error_msg);
                        CloseTrainingStatus();
                    },
                    error: function (error) {
                        toastr.error(response.error_msg);
                    }
                });
            }
        })
       
        function getTrainingDueDate() {
            var due_date = $('#traning_due_date_id').val();
            var cnt = 0;
            $('#traning_due_date_error').html("");
            if (due_date.trim() == '') {
                $('#traning_due_date_error').html("Please enter Training Due Date");
                cnt = 1;

            }
            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    url: "<?php echo URL::to('/'); ?>/patient/training-due-date",
                    type: "POST",
                    data: {
                        "traning_due_date": due_date,
                        "_token": "<?php echo csrf_token(); ?>",
                        'patient_id': <?php echo $record->id; ?>,
                    },
                    success: function(resp) {
                        var msg = 'Training Due date successfully updated';
                        toastr.success(msg);
                        location.reload();
                        
                    }

                })
            }   


        }

        function  getEmergencyPhone(){
            var emergency_phone = $('#emergency_phone').val();
            var cnt = 0;
            $('#emergency_phone_error').html("");
            if (emergency_phone.trim() == '') {
                $('#emergency_phone_error').html("Please enter Emergency Phone");
                cnt = 1;

            }
            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    url: "<?php echo URL::to('/'); ?>/patient/updateEmergencyPhone",
                    type: "POST",
                    data: {
                        "emergency_phone": emergency_phone,
                        "_token": "<?php echo csrf_token(); ?>",
                        'patient_id': <?php echo $record->id; ?>,
                    },
                    success: function(resp) {
                        $('#emergency_phones').html(emergency_phone)
                        toastr.success(resp.error_msg);
                        clearEmergencyPhone()
                        
                    },
                    error:function(xhr){
                        toastr.error(xhr.responseJSON.error_msg);
                    }

                })
            }
        }

        function    clearEmergencyPhone(){
            $('.error').html("")
            $('#exampleModal-emergency_phone').modal('hide');
        }

        function getEmail(){
            var email   =$('.email_value').val();
            var regex   =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            var cnt=0;
            $('#emergency_email_error').html("");
         
            if(email.trim() ==''){
                $('#emergency_email_error').html("Please enter Email");
                cnt = 1;
            }

            if(email.trim() !=''){
                if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))
                {
                    $('#emergency_email_error').html("Invalid email address");
                    cnt = 1;
                }
            }

            if(cnt  ==1){
                return  false;
            }else{
                $.ajax({
                    async: false,
                    global: false,
                    url: "<?php echo URL::to('/'); ?>/patient/updateEmail",
                    type: "POST",
                    data: {
                        "email": email,
                        "_token": "<?php echo csrf_token(); ?>",
                        'patient_id': <?php echo $record->id; ?>,
                    },
                    success: function(resp) {
                        $('#emergency_email').html(email)
                        toastr.success(resp.error_msg);
                        clearEmail()
                        
                    },
                    error:function(xhr){
                        toastr.error(xhr.responseJSON.error_msg);
                    }

                })
            }
        }

        function    clearEmail(){
            $('.error').html("");
            $('#exampleModal-email').modal('hide');
        }

        function updateDetails(value){
            var value = $('#training_statuss').html();
            $('#training_status').val(value)
        }
        
        function updatePhoneDetails(phone){
            var phone = $('#emergency_phones').html();
            $('#emergency_phone').val(phone)
        }

        function updateEmailDetails(email){
            
            var email = $('#emergency_email').html();
            $('#email').val(email)
        }

        function updateTrainingDueDate(date){
            $('#traning_due_date_id').val(date)
        }
        function getHHXCaregiverDetails(){
            $('.token-input-list').remove();
            var agencyId = '{{ $record->agency_id}}';
            var urlToken = "{{ url('link-to-hha-caregiver') }}?agency_id="+agencyId;
            var urlTokenCaregiverCode = "{{ url('link-to-hha-caregiver-caregiver') }}?agency_id="+agencyId;
            var link_hha_caregiver = $('#hha_caregiver_ids').val();
            var hhx_caregiver_name =  $('#hha_caregiver_names').val();
            
            $("#hha_profile_id").tokenInput(urlToken, {

                prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{ id: link_hha_caregiver, name: hhx_caregiver_name}] : [],
              
                tokenLimit: 1,
                zindex: 9999
            });

           
            
        }

        function getDeleteTask(id){
            $.confirm({
                title: 'Delete',
                columnClass: "col-md-6",
                content: 'Are you delete this task ?',
                buttons: {
                    formSubmit: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
							type: "POST",
							url: "{{url('tasks/task-list/')}}/" + id,
							data: {
								'_token': "{{csrf_token()}}",
								'_method': "DELETE",
								'id': id
							},
							success: function(res) {
								if (res == 1) {
									toastr.success('Task successfully deleted');
									getTaskList();
									
								} else {
									toastr.error('Sorry, something went wrong. Please try again.');
									
								}
							}
						})
                        }
                    },
                    cancel: function() {
                        //close
                    },
                },
            });
        }
        $('#hha_document_medical_id').change(function(e){
            var agencyId ='{{ $record->agency_id}}';
            if(agencyId ==106){
                var value = $(this).val();
                $('#hha_due_date_div').attr('style','display:none');
                var flag = value.includes('80093');
                if(flag || true){
                    $('#hha_due_date_div').attr('style','');
                }
            }
            
        })
        $('#exampleModal-task').on('hidden.bs.modal', function () {
            $('.error').html("");
            $('#task_name_id').val("")
            $('#assign_to_id').val('').trigger("change");
            $('#start_date').val("");
            $('#due_date').val("");
            $('#priority').val("");
            $('#task_description').val("");
        });

        function getFollowupDate(){
            var follow_date_id = $('#follow_date_id').val();
            var cnt =0;
            if(follow_date_id ==''){
                $('#follow_date_error').html("Medical Followup date is required");
                cnt =1;
            }
            if(cnt ==1){
                return false;
            }else{
                $.ajax({
                    type: "POST",
                    url: "{{url('patient-followup-date/')}}",
                    data: {
                        '_token': "{{csrf_token()}}",
                        
                        'id': '{{ $record->id}}',
                        'follow_date':follow_date_id
                    },
                    success:function(res){
                        $('#{{$record->agency_id}}_follow_update').html(follow_date_id);
                        $('#close_follow').click();
                        toastr.success(res.error_msg);
                    },
                    error:function(jqr){

                        toastr.error(jqr.responseJSON.error_msg);
                    }
                });
            }
        }
        $('#exampleModal-follow_date').on('hidden.bs.modal', function () {
            $('#follow_date_error').html("");
        });
        $('#remote-popup').click(function(){
            $('#lnkhhx_remote_id')[0].reset();
            $('.token-input-list').remove()
            $('#hha_remote_id').html("");
            $('#hha_remote_name').html("");
            $('.token-input-delete-token').click()
            remoteFunction();
           
        });

     
        var remoteID = '{{ $record->robort_id }}';
        var remoteName = '{{ $record->remote_name }}';
        var extenalId;
        function remoteFunction(){
            var urlToken =  "{{ url('remote-emp-data') }}"; 
            $("#hha_remote_id").tokenInput(urlToken, {
                tokenLimit: 1,
                zindex: 9999,
              
                prePopulate: remoteID !== ""  ? [{ id: remoteID, name: remoteName }] : [],
                onAdd: function (item) {
                 
                var selectedRemoteId = item.remote_id;
                var name = item.name;
                $('#hha_remote_id').val(selectedRemoteId);
                $('#hha_remote_name').val(name);
                    
                },
            });
        }

        $('#update-remote-id').click(function(){
            
            var remoteId =  $('#hha_remote_id').val();
            var name =  $('#hha_remote_name').val();
            
            $('.hha_remote_id_error').html("");
            var cnt =0;
            if(remoteId ==''){
                $('.hha_remote_id_error').html("Please Select Employee");
                cnt =1;
            }
            if(cnt ==1){
                return false;
            }else{
                $.ajax({
                    type:"post",
                    url:"{{ url('patient/update-remote-id') }}",
                    data:{
                        'patient_id':'{{ $record->id}}',
                        'remote_id':remoteId,
                        'name':name,
                        '_token':'{{ csrf_token()}}'
                    },
                    success:function(res){
                        toastr.success(res.error_msg);
                        $('#lnkhhx_remote_id')[0].reset();
                        $('#exampleModal-link-remote-id').modal('hide');
                        $('.token-input-delete-token').click()
                        $('#hhx_remote_id').html('');
                        $('.token-input-list').remove();
                        remoteID = res.data[0].robort_id;
                        remoteName = name;
                        extenalId = '';
                        extenalId = (res.data[0].externalId !=null)?res.data[0].externalId:"";
                        
                        $('#hhx_robort_id').html(name);
                    },
                    error:function(xhr){
                        toastr.error(xhr.responseJSON.message);
                    }
                })
            }

        });

        function CloseRemoteEmployeePopup(){
            $('.hha_remote_id_error').html("");
            $('#lnkhhx_remote_id')[0].reset();
            $('.token-input-list').remove();
            $('.token-input-delete-token').click()
        }
        
        function searchUser(search, cb) {
            $.ajax({
                url: "{{ url('auto-complete-email') }}",
                data: {
                    term: search,
                    agency_id:'{{ $record->agency_id}}'
                },
                success: function(data) {
                    var jsonData = data.data;

                    if (jsonData.length !== 0) {
                        var result = jsonData.map(function(v) {
                            var url = "{{ url('user-view')}}/"+v.id;
                            return {
                                id: v.id,
                                key: v.full_name+' ( '+v.email+')',
                                value: v.full_name,
                                email: v.email,
                                url: url
                            };
                        });
                        
                        cb(result);
                    }
                }
            });
        }
        function selected(){
            setTimeout(() => {
                var response  = <?php echo json_encode($serviceArr); ?>;
                var final = [];
                $.each(response,function(item,val){
                
                    final.push(val);
                })
                $(".new_service_id").val(final).trigger('change');
            }, 1000);
          
        }

        $(function() {
            
            var start = moment().startOf('month');
            var end = moment().endOf('month');
            $('#hha_patient_coordinator_date').daterangepicker({
            startDate: start,
            endDate: end,
            autoUpdateInput: false,
            startOfWeek: 'sunday',
            ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                        .endOf('month')
                    ],
                    'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                        .endOf('isoWeek')
                    ],
                    'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                        'weeks').endOf('isoWeek')],
                }
            }, function(chosen_date, end_date) {
                $('#hha_patient_coordinator_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                    'MM/DD/YYYY'));
            })
        });

        $('#update-inservice-status-two').click(function(e){
            var inservice_status = $("#inservice_status_two").val();
            var ct=0;
            $('.inservice_status_two_error').html("");
            if(inservice_status==''){
                $('.inservice_status_two_error').html("Required");
                ct=1;
            }

            if(ct   ==1){
                return  false;
            }else{
                $.ajax({
                    async:false,
                    global:false,
                    type: "post", 
                    url: "{{  url('update-inservice-two')  }}",
                    
                    data:{
                        '_token':"{{  csrf_token()  }}",
                        'patient_id':"{{  $record->id  }}",
                        'inservice_status':inservice_status
                    },
                    success: function (response) {
                        $('#inservices_status_two').html(inservice_status)
                        toastr.success(response.error_msg);
                        CloseInserviceStatusTwo();
                    },
                    error: function (error) {
                        toastr.error(response.error_msg);
                    }
                });
            }
        })

        function  CloseInserviceStatusTwo(){
            $('.error').html("");
            $('#exampleModal-inservice_status_two').modal('hide');
        }
        
        
        

        function searchCaregiver(){
            var hha_caregiver_code_id = $('#hha_caregiver_code_id').val();
            $('#hhas_caregiver_id').attr('style','display:none');
            if(hha_caregiver_code_id.trim() !=''){
                $.ajax({
                    type:"get",
                    url:"{{ url('search-hha-caregiver') }}",
                    data:{
                        'q':hha_caregiver_code_id,
                        'agency_id':'{{ $record->agency_id}}',
                       
                    },
                    success:function(res){
                        var response = res.data;
                        var tableResponse = "";
                        $('#hhas_caregiver_id').attr('style','');
                        $('#hhaAppendCId').html("")
                        console.log(res.data);
                        if(response.length !=0){
                           var cnt = 1;
                            $.each(response,function(i,v){
                                if(!v.caregiver_id){
                                    tableResponse +=`<tr>
                                    <td nowrap>${cnt++}</td>
                                    <td nowrap>${v.id}</td>
                                    <td nowrap>${v.name+'('+v.caregiver_code+')'}</td>
                                    <td nowrap>${(v.status !=null)?v.status:""}</td>
                                    <td nowrap><input type="radio" name="cid" id="hha${v.id}" onclick="selectedCaregiver(${v.id})" data-type="local" value="${v.id}"  data-name="${v.name}" data-code="${v.caregiver_code}"></td>
                                </tr>`;
                                }else{
                                    tableResponse +=`<tr>
                                    <td nowrap>${cnt++}</td>
                                    <td>${v.caregiver_id}</td>
                                    <td>${v.first_name+' '+v.last_name +'('+v.caregiver_code+')'}</td>
                                    <td>${v.status}</td>
                                    <td><input type="radio" name="cid"  id="hha${v.caregiver_id}" onclick="selectedCaregiver(${v.caregiver_id})" data-type="hha" value="${v.caregiver_id}" data-name="${v.first_name+' '+v.last_name}" data-code="${v.caregiver_code}"></td>
                                </tr>`;
                                }
                                
                            });

                          
                            $('#hhaAppendCId').html(tableResponse)
                        }else{
                     
                            $('#hhaAppendCId').html('<tr><td colspan="4">No record available</td></tr>')   
                        }

                      
                    },
                    error:function(xhr){
toastr.error(xhr.responseJSON.message);
                    }
                })
            }
            
        }

        function linkThirdParty(){
            $('.token-input-list').remove();
            var agencyId = '{{ $record->agency_id}}';
            var urlToken = "{{ url('link-to-third-party') }}?agency_id="+agencyId;
            
            var link_hha_caregiver = $('#third_party_ids').val();
            var hhx_caregiver_name =  $('#third_party_ids_names').val();
            
            $("#third_party_id").tokenInput(urlToken, {

                prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{ id: link_hha_caregiver, name: hhx_caregiver_name}] : [],
              
                tokenLimit: 1,
                zindex: 9999,
                onAdd: function (item) {
                    
                    var selectedAlaycareId = item.id;
                    var name = item.name;
                        $('#third_party_ids').val(selectedAlaycareId);
                        $('#third_party_ids_names').val(name);
                        
                    },
            });

           
        }

        function saveLinkThirdParty(){
            var hha_profile_id =  $('#third_party_id').val();
            $('.hha_profile_error').html("");
            var cnt =0;
            if(hha_profile_id ==''){
                $('.third_party_id_error').html("Third Party Link is required");
                cnt =1;
            }

            if(cnt ==1){
                return false;
            }else{
                $.ajax({
                    type:"post",
                    url:"{{ url('patient/save-link-to-third-party') }}",
                    data:{
                        'patient_id':'{{ $record->id}}',
                        'agency_id':'{{ $record->agency_id}}',
                        'hha_profile_id':hha_profile_id,
                        '_token':'{{ csrf_token()}}'
                    },
                    success:function(res){
                        toastr.success(res.message);
                        var fullName = res.data.first_name+' '+res.data.last_name;
                        $('#link_third_party_id').html(fullName);
                        $('#lnkhhx_pdf_id')[0].reset();
                        $('#third_party_ids').val(res.data.id);
                        $('#third_party_ids_names').val(fullName);
                        $('#close_link_third_party').click();
                    },
                    error:function(xhr){
                        toastr.error(xhr.responseJSON.message);
                    }
                })
            }
        }

        function selectedCaregiver(id){
            
            var hhx_caregiver_name = $('#hha'+id).attr('data-name')
            var link_hha_caregiver = id;
            $('.token-input-list').remove();
            var urlToken = "{{ url('link-to-hha-caregiver') }}?agency_id={{ $record->agency_id}}";
            $("#hha_profile_id").tokenInput(urlToken, {

                prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{ id: link_hha_caregiver, name: hhx_caregiver_name}] : [],

                tokenLimit: 1,
                zindex: 9999
            });

            $('#dataTypeId').val($('#hha'+id).attr('data-type'));
        }

        $('#documentSave').click(function(e){
            
            var datenew_id = $('#datenew_id').val();
            var timemew = $('#timeidnew').val();
            var document_completed_date = $('#document_completed_date').val();
            var document_service_id = $('#document_service_id').val();
            $('#document_id_error').html("");
            $('#time_error').html("");
            $('#document_completed_date_error').html("");
            $('#document_service_id_error').html("");
            var cnt = 0;
            
            // if (document_service_id == '') {
            //     $('#document_service_id_error').html("Please select Services");
            //     cnt = 1;
            // }

            // if (document_completed_date == '') {
            //     $('#document_completed_date_error').html("Please select Document Completed Date");
            //     cnt = 1;
            // }

            if (datenew_id.trim() == '') {
                $('#document_id_error').html("Please enter Document Name");
                cnt = 1;
            }
            if (timemew.trim() == '') {
                $('#images_error').html("Please select Attachment");
                cnt = 1;
            }else{
                var fileExtensionType = ['pdf','csv','xlsx','xls','docx','doc'];
                var files = $('input[name="images"]')[0].files;
                var fileName = files[0].name;
                var fileType = fileName.substr(fileName.lastIndexOf('.') + 1);
                 $('#images_error').html("");
                if ($.inArray(fileName.split('.').pop().toLowerCase(), fileExtensionType) == -1) {
                    $('#images_error').html("Please select only pdf or csv file");
                    cnt=1;
                }
            }

            if (cnt == 0) {
                $("#documentSave").prop('disabled',true);
                var formData = new FormData($('#formnew')[0]);
                formData.append('_token','{{ csrf_token()}}');

                $.ajax({
                    async: false,
                    global: false,
                    type: "POST",
                    url: "{{ url('patient/document-send-patientId')}}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success:function(res){
                        toastr.success(res.error_msg);
                        $("#formnew")[0].reset();
                        $("#documentSave").prop('disabled',false);
                        $('#exampleModal-5').modal('hide')
                        $('#document_service_id').val("").change();
                        loadDocumentAjaxList();
                        closeDocumentSection();
                    },
                    error:function(jqXHR){
                        $("#documentSave").prop('disabled',false);
                        toastr.error(jqXHR.responseJSON.error_msg)
                    }
                })
            } else {
                return false;
            }
        })
        
        function loadDocumentAjaxList(){
            $.ajax({
                async: false,
                global: false,
                type: "GET",
                url: "{{ url('delete-patient-document-ajax-list')}}",
                data: {
                    "id": "{{ $record->id}}",
                
                },
                success: function(res) {
                    $('#document_response_list').html("")
                    $('#document_response_list').html(res)
                }
            })
            return false;
        }

        loadDocumentAjaxList();

        function closeDocumentSection(){
            $('#formnew')[0].reset();
            $('#document_service_id').val('null').change();
            $('#images_error').html("")
            $('#document_completed_date_error').html("")
            $('#document_service_id_error').html("")
            $('#document_id_error').html("")
        
        }
        $('.service_follow_date').datepicker({
            minDate: 0 
        });

        function alreadyExitMerge(){
            toastr.error("You have already merged record");
        }
    </script>
    @include('deletedPatients/js_parameter')