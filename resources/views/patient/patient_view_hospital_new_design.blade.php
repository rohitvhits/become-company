@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet"
    href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ asset('assets/css/tribute.css') }}" rel="stylesheet" type="text/css">

@if($record->alaycare_id != "")
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/css/custom.css?time={{ env('timestamp')}}">

@endif
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/modulejs/css/patient-new-design.css?time={{ env('timestamp')}}">
<link rel="stylesheet" href="{{ asset('assets/css/help-me-write.css') }}?time={{ env('timestamp')}}">
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
    rel="stylesheet" />
<link href="{{  asset('assets/modulejs/css/task-new-design.css') }}?time={{ env('timestamp')}}" rel="stylesheet">
<link href="<?php echo URL::to('/'); ?>/assets/modulejs/css/task-module.css" rel="stylesheet" type="text/css" />
@if(isset($agencyDetails->robort_status) && $agencyDetails->robort_status ==1)
<link href="<?php echo URL::to('/'); ?>/assets/modulejs/css/remote_focus/remote_focus.css?time={{ env('timestamp')}}" rel="stylesheet" type="text/css" />
@endif
<style>

    .label { display:inline; padding:.2em .6em .3em; font-size:75%; font-weight:700; line-height:1; color:#fff; text-align:center; white-space:nowrap; vertical-align:baseline; border-radius:.25em; }
    .label-success { background-color:#5cb85c; }
    .label-danger  { background-color:#d9534f; }
    .label-default { background-color:#777; }
    .ca-modal-header-green { background:linear-gradient(135deg,#28a745,#218838); }
     @keyframes shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

/* Skeleton row style */
.skeleton-row {
  position: relative;
  overflow: hidden;
}

.skeleton-cell {
  height: 20px;
  border-radius: 4px;
  background: #eee;
  background: linear-gradient(
    to right,
    #eeeeee 0%,
    #dddddd 20%,
    #eeeeee 40%,
    #eeeeee 100%
  );
  background-size: 1000px 100%;
  animation: shimmer 1.5s infinite linear;
}
.alert1 {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}
.agency-note-alert { position: relative; padding: 0.5rem 1rem; margin-bottom: 0.25rem; border: 1px solid transparent; border-radius: 4px; }
.agency-note-alert.alert-danger  { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
.agency-note-alert.alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeeba; }
.agency-note-alert.alert-info    { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
.alert-fill-danger {
    color: #ffffff;
    background-color: #f29d56;
    border-color: #f10075;
}
</style>
<!--main-container-part-->
@php
    $editAppointmentFlag =1;
    $addNotesAppointmentFlag =1;
    $addDocumentAppointmentFlag =1;
    $addService =1;
    $editService =1;
    if (auth()->user()->agency_fk != "") {

        $editAppointmentFlag = 0;
        $addDocumentAppointmentFlag =0;
        $addNotesAppointmentFlag =0;
        $addService = 0;
        $editService = 0;
        if(!in_array('EditAppointment',$appointmentPermission)){
            $editAppointmentFlag = 1;
        }
        if(!in_array('AddNotes',$appointmentPermission)){
            $addNotesAppointmentFlag = 1;
        }
        if(!in_array('AddDocument',$appointmentPermission)){
            $addDocumentAppointmentFlag = 1;
        }

        if(!in_array('AddService',$appointmentPermission)){
            $addService = 1;
        }

        if(!in_array('EditService',$appointmentPermission)){
            $editService = 1;
        }
    }

@endphp
<div class="main-panel view-appointmenr-main">
    <div class="content-wrapper px-3 pb-0">

        <div class="dashboard-header d-flex flex-column ">
            <div class="row">
                @if(empty(auth()->user()->agency_fk))
                {{-- Agency Notes Alerts --}}
                @if(isset($agencyNotes) && $agencyNotes->count() > 0)
                <div class="col-md-12 mb-2">
                    @foreach($agencyNotes as $agencyNote)
                    @php
                        $noteAlertClass = 'alert-info';
                        $noteIcon = 'mdi-information';
                        if ($agencyNote->note_type === 'warning') { $noteAlertClass = 'alert-warning'; $noteIcon = 'mdi-alert'; }
                        if ($agencyNote->note_type === 'danger')  { $noteAlertClass = 'alert-danger';  $noteIcon = 'mdi-alert-circle'; }
                    @endphp
                    <div class="agency-note-alert {{ $noteAlertClass }} mb-1 py-2 px-3" role="alert" id="agency-alert-note-{{ $agencyNote->id }}" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:nowrap;border-radius:4px;">
                        <div style="display:flex;align-items:flex-start;flex:1;min-width:0;">
                            <i class="mdi {{ $noteIcon }} mr-2 mt-1" style="font-size:18px;flex-shrink:0;"></i>
                           
                            <span style="margin-top: 6px;"><strong>{{ $agencyNote->note_type === 'danger' ? 'Alert' : ucfirst($agencyNote->note_type) }}:</strong> {{ $agencyNote->note }}</span>
                        </div>
                        <button type="button" style="background:none;border:none;font-size:20px;line-height:1;cursor:pointer;opacity:0.6;flex-shrink:0;margin-left:12px;" aria-label="Close" onclick="document.getElementById('agency-alert-note-{{ $agencyNote->id }}').style.display='none';">
                            &times;
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="col-md-8 mb-2">
                    <div class="row alert1 alert-fill-danger ml-1 @if($record->archived_at !=null) @else hide @endif" role="alert"  id="unrchived_div">
                        <!-- Left Content -->
                        <div class="col-md-9 d-flex align-items-center">
                            <h6 class="mb-0">The Appointment has been archived</h6>
                        </div>

                        <!-- Right Toggle -->
                        <div class="col-md-3">
                            <div class="d-flex justify-content-end align-items-center" style="gap:8px;">
                                <p class="mb-0" style="margin-left:-10px;">Unarchive</p>
                                <label class="toggle-switch toggle-switch-success unachived" >
                                    <input type="checkbox"
                                        value="1" {{ $record->archived_at != "" ? 'checked' : '' }}>
                                    <span class="toggle-slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Right Section (col-md-4) -->
                @can('patient-view-page-toggle')
                <div class="col-md-4">
                    <div class="d-flex justify-content-end" style="gap:8px;">
                        <p style="margin-left:-10px">Back To Old Design</p>
                        <label class="toggle-switch toggle-switch-success patient-toggle">
                            <input type="checkbox" name="patient_page" id="patientPageDesign" class="patientPageDesign" value="{{ $auth->patient_page}}" {{ $auth->patient_page == 1 ? 'checked' : ''}}>
                            <span class="toggle-slider round"></span>
                        </label>
                    </div>
                </div>
                @endcan
            </div>

            <div class="basic-detail-sec border-bottom  mb-3 card">
                <div class="d-flex align-items-center justify-content-between flex-wrap   mb-2">


                    @php $class = ''; @endphp
                    @if($record->flag == 1)
                    @php $class = 'highlight-patient-appointment badge badge-outline-danger'; @endphp
                    @endif
                    <div class="d-flex align-items-center {{$class}}">
                        <h4 class="mb-0 font-weight-bold" style="display:inline-flex;align-items:center;flex-wrap:wrap;gap:4px;">
                            ID #&nbsp;{{ $record->id }}&nbsp;<a href="javascript:void(0)" title="Copy ID" onclick="copyToClipboard('{{ $record->id }}', this)" style="font-size:13px;line-height:1;vertical-align:middle;"><i class="mdi mdi-content-copy"></i></a>
                            &nbsp;-&nbsp;
                            {{ ucwords($record->first_name) }} {{ ucwords($record->last_name) }}&nbsp;<a href="javascript:void(0)" title="Copy Name" onclick="copyToClipboard('{{ ucwords($record->first_name) }} {{ ucwords($record->last_name) }}', this)" style="font-size:13px;line-height:1;vertical-align:middle;"><i class="mdi mdi-content-copy"></i></a>

                        </h4> &nbsp;&nbsp;( <?php echo $record->agency_name; ?> )
                        <?php if ($record->partner_agency != "" && $record->id != 606647) { ?>- (<?php echo $record->partner_agency; ?>)
                    <?php } ?>
&nbsp;<a href="javascript:void(0)" title="Copy Full Text" onclick="copyToClipboard('ID # {{ $record->id }} - {{ ucwords($record->first_name) }} {{ ucwords($record->last_name) }} ( {{ $record->agency_name }} )', this)" style="font-size:14px;vertical-align:middle;"><i class="mdi mdi-content-copy"></i></a>
                    @can('send-patient-demographic-sms')
                    @if($record->demographic_updated_flag == 0)

                            <a href="javascript:void(0)" class="ml-4"
                                onclick="sendPatientDemographicSMS('{{ $record->mobile}}')">Send SMS To Patient Demographic
                                Details</a>

                    @endif
                    @endcan
                    <?php

                    if (isset($record->record_id) && $record->record_id != '') {
                    ?>

                        <!-- <span class="badge badge-primary">Expert Medicaid Consultancy</span> -->
                    <?php } ?>
                    </div>
                    <div class="appoin-btn-wrapper">
                        <div>
                            <?php if ($auth->login_type_fk == 183) { ?>

                                @if(strtolower($record->type) == 'caregiver')
                                    @if(!empty($custom_esign_template[0]))
                                        @can('view-vns-form')
                                            <div class="btn-group pull-right status-dropdoown mr-2">
                                                <button type="button" class="btn btn-warning" title="Status">VNS Form</button>
                                                <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" id="vnsforms" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="vnsforms">
                                                    @foreach($custom_esign_template as $vns)
                                                                <a class="dropdown-item" href="{{ url('/custom-esign-html/')}}?id={{sha1($record->id)}}&template_id={{ $vns->id}}" target ="_blank" id="">{{ $vns->template_name}}</a>

                                                    @endforeach
                                                </div>
                                            </div>
                                        @endcan
                                    @endif
                                @endif


                                @can('appointments-delete')
                                <a href="javascript:void(0);" onclick="deleteRecordPatient('{{$record->id}}')"
                                    class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block mr-2"
                                    title="Delete"><i class="fa fa-trash"></i> Delete</a>
                                @endcan

                                @if(strtolower($record->type) != 'caregiver' && isset($agencyDetails->is_telehealth_send_sms) && $agencyDetails->is_telehealth_send_sms ==1)
                                <button type="button" class="pull-right btn btn-warning btn-rounded btn-sm mr-2 d-none d-md-block" data-toggle="modal" data-target="#resolutionSmsModal">
                                    <i class="fa fa-envelope"></i> Resolution SMS
                                </button>
                                @endif

                                @if(strtolower($record->type) == 'caregiver')
                                <div class="btn-group pull-right status-dropdoown mr-2">
                                    <button type="button" class="btn btn-warning" title="Status">Status</button>
                                    <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                        id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">

                                        @if ($record->status != 'Pending')
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="pending"
                                            onclick="getModals('pending')">Pending</a>
                                        @endif
                                        @if ($record->status == 'Pending' && $record->status != 'refused')
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="booked"
                                            onclick="getModals('booked')">Scheduled</a>
                                        @endif
                                        @if (
                                        $record->status != 'completed' &&
                                        $record->status != 'cancelled' &&
                                        $record->status != 'refused' &&
                                        $record->status != 'noshow'
                                        )
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="checkin"
                                            onclick="getModals('checkin');">Mark as CheckIn</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                            id="processing" onclick="getModals('processing');">Mark as Processing</a>
                                        @endif
                                        @if (strtolower($record->status) != 'cancelled' && $record->status != 'noshow' && $record->status != 'refused')
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="complete"
                                            onclick="getModals('complete');">Mark as Completed</a>
                                        @endif
                                        @if (
                                        $record->status != 'completed' &&
                                        $record->status != 'refused' &&
                                        $record->status != 'cancelled' &&
                                        $record->status != 'noshow'
                                        )
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                            class="pull-right" id="cancel" data-toggle="modal"
                                            data-target="#exampleModal-cancel" data-whatever="@mdo">Mark as Cancel</a>
                                        @endif
                                        @if ($record->status != 'completed' && $record->status != 'noshow' && $record->status != 'refused')
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                            class="pull-right" id="noshow" onclick="getModals('noshow');">Mark as NoShow</a>
                                        @endif
                                        @if ($record->status != 'completed')
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="refused"
                                            onclick="showRefuseModal()">Mark as refused</a>
                                        @endif
                                        @if ($record->prev_status != '')
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="undo"
                                            onclick="Undo(<?php echo $record->id; ?>)">Undo</a>
                                        @endif
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                            id="hospitalized" onclick="getModals('hospitalized')">Mark as
                                            Hospitalized/Rehab</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                            id="unableToContact" onclick="getModals('unableToContact')">Unable To
                                            Contact</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                            id="InService" onclick="$('#exampleModal-inservice-record').modal('show');">In
                                            Service</a>


                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                            id="PendingTermination" onclick="getModals('PendingTermination')">Pending
                                            Termination</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="Onhold"
                                            onclick="getModals('Onhold')">On hold</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="Onleave"
                                            onclick="getModals('Onleave')">On leave</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                            id="Terminated" onclick="getModals('Terminated')">Terminated</a>
                                            @if (strtolower($record->status) != 'inactive')
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="Inactive" onclick="getModals('Inactive')">Inactive</a>
                                                        @endif

                                    </div>
                                </div>
                                @endif
                                @if($record->status == 'completed')
                                <a data-toggle="modal"
                                    class="pull-right btn btn-info btn-rounded btn-sm d-none d-md-block mr-2"
                                    data-target="#serviceByPatientTypeModal" style="color:#fff"
                                    onclick="getPatientId('{{ $record->id}}','{{$record->type}}');">
                                    <i class="mdi mdi-plus"></i> Add New Request Service</a>
                                @endif



                            <?php } ?>
                            {{-- <?php if ($user['user_type_fk'] == 5) { ?>
                                <a href="<?php echo URL::to('/'); ?>/patient/edit/<?php echo $record->id; ?>"
                                    class="pull-right btn btn-secondary btn-rounded  btn-sm  d-none d-md-block"
                                    class="pull-right" title="Edit" style="margin-right: 10px;"><i class="fa fa-edit"></i>
                                    Edit</a>

                            <?php } else { ?>
                                <?php if ($auth->login_type_fk == 183) { ?>
                                    @can('appointments-edit')
                                    <a href="<?php echo URL::to('/'); ?>/patient/edit/<?php echo $record->id; ?>"
                                        class="pull-right btn btn-secondary btn-rounded  btn-sm  d-none d-md-block"
                                        class="pull-right" title="Edit" style="margin-right: 10px;"><i class="fa fa-edit"></i>
                                        Edit</a>
                                    @endcan
                                <?php    } else { ?>
                                    <a href="<?php echo URL::to('/'); ?>/patient/edit/<?php echo $record->id; ?>"
                                        class="pull-right btn btn-secondary btn-rounded  btn-sm  d-none d-md-block"
                                        class="pull-right" title="Edit" style="margin-right: 10px;"><i class="fa fa-edit"></i>
                                        Edit</a>
                                <?php    } ?>
                            <?php } ?>
                            <?php if ($user['user_type_fk'] == 184 || $user['user_type_fk'] == 6) { ?>

                                <?php

                                if ($record->status != 'completed' && $record->status != 'cancelled' && $record->status != 'noshow') {
                                ?>


                                    <?php if ($user['user_type_fk'] == 184) {
                                        if($record->type == 'Caregiver'){
                                        ?>
                                        <a href="javascript:void(0)"
                                            class="pull-right btn btn-info btn-sm btn-rounded  d-none d-md-block"
                                            data-toggle="modal" data-target="#exampleModal-4" onClick="selectedSchedule()"
                                            data-whatever="@mdo" style="margin-right: 10px;" title="Schedule Appointment"> Schedule
                                            Appointment</a>
                                    <?php        }
                                }?>

                                    <?php if ($user['user_type_fk'] == 6 && $user['login_type_fk'] == 2) { ?>
                                        <a href="javascript:void(0)"
                                            class="pull-right btn btn-info btn-sm btn-rounded  d-none d-md-block"
                                            data-toggle="modal" data-target="#exampleModal-23" data-whatever="@mdo"
                                            style="margin-right: 10px;" title="Request for  Appointment"> Request for
                                            Appointment</a>
                                    <?php        } ?>


                                    Status
                                    <?php if ($user['user_type_fk'] == 184) { ?>
                                        <a href="javascript:void(0)"
                                            class="pull-right btn btn-info btn-rounded  btn-sm  d-none d-md-block"
                                            data-toggle="modal" class="pull-right" data-target="#exampleModal-44"
                                            data-whatever="@mdo" style="margin-right:10px" title="Telehealth Appointment">
                                            Telehealth
                                            Appointment</a>&nbsp;&nbsp;
                                    <?php        } ?>
                            <?php    }
                            } ?> --}}
                            @can('resolution-chart')
                                @if(strtolower($record->type) == 'patient')
                                <a data-fancybox data-src="#patientResolutionModal" href="javascript:;" class="pull-right btn btn-primary btn-rounded  btn-sm  d-none d-md-block mr-2">
                                    Resolution chart
                                </a>
                                @endif
                            @endcan
                            @can('flag-change-status')
                            @if($record->flag == 0)
                            @php $flag = 'Flag'; @endphp
                            @php $color = 'btn-outline-secondary'; @endphp
                            @else
                            @php $flag = 'Flagged'; @endphp
                            @php $color = 'btn-success'; @endphp
                            @endif
                            <a onclick="flagChange();"
                                class="pull-right btn {{$color}} btn-rounded  btn-sm  d-none d-md-block mr-2"
                                title="{{ $flag}}"><i class="fa fa-flag"></i> &nbsp; {{$flag}}</a>
                            @endcan
                        </div>
                        <div class="button-wrapper d-flex align-items-center mt-md-3 mt-xl-0" >

                            @if($record->robort_id != "" && $record->externalId != "")
                            <?php if ($auth->login_type_fk == 183) { ?>
                                <div class="btn-group pull-right status-dropdoown mr-2">
                                    <button type="button" class="btn btn-warning" title="Status">Remote</button>
                                    <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton12" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton12">
                                        <a class="dropdown-item" target="_blank" href="{{ url('sync-robort-visit')}}/{{ $record->robort_id}}">Sync Visit Robort</a>
                                        <a class="dropdown-item" onclick="sentRemoteDetails()" href="javascript:void(0)">Sent Demographic Details</a>

                                    </div>
                                </div>

                            <?php    } ?>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="top-detail-sec">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="top-basic-detail-sec">
                                <div class="">
                                    <div class="row">
                                        <div class="">
                                            <div class="">
                                                <div class="col-md-12 mb-2">
                                                    <dt class="detail-title">Type</dt>
                                                </div>
                                                <div class="col-md-12">
                                                    <dl>
                                                        <span class="type-bg"><?php echo $record->type . '<br>'; ?></span>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="">
                                    <div class="">
                                        <div class="col-md-12">
                                            <dt class="detail-title mb-2">Gender</dt>
                                        </div>
                                        <div class="col-md-12">
                                            <dl>
                                                <?php if (isset($record->gender) && $record->gender != '') {
                                                    $otherName = "";
                                                    if ($record->gender == 'other') {
                                                        $otherName = " (" . $record->other_gender . ")";
                                                    }
                                                    echo ucfirst($record->gender) . $otherName . '<br>';
                                                } else {
                                                    echo 'N/A';
                                                } ?>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="">
                                        <div class="">
                                            <div class="">
                                                <div class="col-md-12">
                                                    <dt class="detail-title  mb-2">Mobile</dt>
                                                </div>
                                                <div class="col-md-12">
                                                    <dl>
                                                        <span id="record_mobile_id"
                                                            @if(auth()->user()->agency_fk == "")
                                                                class="view-call-details" style="cursor:pointer" data-url="{{ route('patient.call-details.ajax', $record->id) }}" data-patient-name="{{ trim($record->first_name.' '.$record->last_name) }}" title="Click to view call details"
                                                            @endif
                                                        ><?php echo preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $record->mobile); ?></span>
                                                       @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                            @if($editAppointmentFlag ==1)
                                                                <a class="mr-1" data-toggle="modal" data-target="#exampleModal-mobile"
                                                                data-whatever="@mdo" title="Mobile"
                                                                onclick="updateMobileDetails()"><i
                                                                    class="fa fa-edit"></i></a>
                                                            @endif
                                                            <a title="Copy Mobile No" onclick="mobileOrPhoneCopy('mobile')"><i class="mdi mdi-content-copy"></i></a>
                                                        @endif
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="">
                                    <div class="">
                                        <div class="col-md-12">
                                            <dt class="detail-title mb-2">Phone</dt>
                                        </div>
                                        <div class="col-md-12">
                                            <dl>
                                                <span id="record_phone_id"><?php echo preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $record->phone); ?></span>
                                                @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                    @if($editAppointmentFlag ==1)
                                                        <a class="mr-1" data-toggle="modal" data-target="#exampleModal-phone"
                                                        data-whatever="@mdo" title="Phone"
                                                        onclick="updatePhoneDetails()"><i
                                                            class="fa fa-edit"></i></a>
                                                    @endif
                                                    <a title="Copy Phone No" onclick="mobileOrPhoneCopy('phone')"><i class="mdi mdi-content-copy"></i></a>
                                                @endif
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="col-md-12">
                                        <dt class="detail-title mb-2">Status</dt>
                                    </div>
                                    <div class="col-md-12">
                                        <dl id="view_status_id">
                                            <?php
                                            if ($record->status == 'Pending' || $record->status == 'pending') {
                                            ?>
                                                <label for="" class='badge badge-warning'>Pending</label>

                                            <?php } ?>
                                            <?php

                                            if (strtolower($record->status) == 'booked') {
                                            ?>
                                                <label for="" class='badge badge-info'>Booked</label>

                                            <?php } ?>
                                            <?php

                                            if ($record->status == 'completed') {
                                            ?>
                                                <label for="" class='badge badge-success'>Completed</label>

                                            <?php } ?>
                                            <?php

                                            if ($record->status == 'in process') {
                                            ?>
                                                <label for="" class='badge badge-secondary'>In process</label>

                                            <?php } ?>
                                            <?php
                                            if ($record->status == 'cancelled' || $record->status == 'refuese' || $record->status == 'no show' || $record->status == 'no answer' || $record->status == 'unable to contact') {
                                            ?>
                                                <label for="" class='badge badge-danger'>Cancelled</label>

                                            <?php } ?>
                                            <?php

                                            if ($record->status == 'noshow') {
                                            ?>
                                                <label for="" class='badge badge-light'>No Show</label>

                                            <?php } ?>
                                            <?php

                                            if ($record->status == 'arrived') {
                                            ?>
                                                <label for="" class='badge badge-primary'>Arrived</label>

                                            <?php } ?>
                                            <?php

                                            if ($record->status == 'processing') {
                                            ?>
                                                <label for="" class='badge badge-secondary'>Processing</label>

                                            <?php }
                                            if ($record->status == 'refused') { ?>
                                                <label for="" class='badge badge-light'>Refused</label>
                                            <?php }
                                            if ($record->status == 'hospitalized/rehab') { ?>
                                                <label for="" class='badge badge-info'>Hospitalized/Rehab</label>
                                            <?php }
                                            if ($record->status == 'Pending Termination') { ?>
                                                <label for="" class='badge badge-danger'>Pending Termination</label>
                                            <?php }
                                            if ($record->status == 'On Hold') { ?>
                                                <label for="" class='badge badge-secondary'>On Hold</label>
                                            <?php }
                                            if ($record->status == 'On Leave') { ?>
                                                <label for="" class='badge badge-info'>On Leave</label>
                                            <?php }
                                            if ($record->status == 'Terminated') { ?>
                                                <label for="" class='badge badge-danger'>Terminated</label>
                                            <?php }
                                            if ($record->status == 'unableToContact') { ?>

                                                <label for="" class='badge badge-danger'>Unable To Contact</label>
                                            <?php } ?>
                                            @if ($record->status == '1st Attempt - Unable to Contact' || $record->status == '2nd Attempt - Unable to Contact' || $record->status == '3rd Attempt - Unable to Contact' || $record->status == 'Patient Asked to Reschedule' || $record->status == 'New Order Received')
                                                <label for="" class='badge badge-info'>{{$record->status}}</label>
                                            @endif

                                            @if ($record->status == 'Telehealth Completed' || $record->status == 'Telehealth Completed , Pending Forms' ||  $record->status == 'Form Completed' || $record->status == 'Service Provided')
                                                <label for="" class='badge badge-success'>{{$record->status}}</label>
                                            @endif

                                            @if ($record->status == 'Patient Deceased' || $record->status == 'Appointment was missed' || $record->status == 'Appointment Missed' || $record->status == 'Closed Temporarily')
                                                <label for="" class='badge badge-danger'>{{$record->status}}</label>
                                            @endif

                                            @if ($record->status == 'Signed' || $record->status == 'Signed & Sent Back to the Agency' || $record->status == 'New Form Requested')
                                                <label for="" class='badge badge-primary'>{{$record->status}}</label>
                                            @endif
                                            @if (strtolower($record->status) == 'inactive')
                                                <label for="" class='badge badge-danger'>{{ucfirst($record->status)}}</label>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                                <?php if ($record->agency_id  != '319'  && $record->agency_id  != '106') { ?>
                                    <div class="">
                                        <div class="">
                                            <div class="col-md-12">
                                                <dt class="detail-title mb-2">Assigned To</dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dl>
                                                    <span>{{$record->assign_user!=""?$record->assign_user:"N/A"}}</span>
                                                    @if($auth->login_type_fk == 183)
                                                        @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                            @if($editAppointmentFlag ==1)
                                                                <a href="javascript:void(0);" title="Assign" style="margin-right: 10px;" data-toggle="modal" data-target="#assignModal" onclick="showAssignAppointmentData('{{ $record->assign_user_id}}')"><i class="fa fa-edit"></i> </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="">
                                    <div class="">
                                        <div class="col-md-12">
                                            <dt class="detail-title mb-2">Language</dt>
                                        </div>
                                        <div class="col-md-12">
                                            <dd class="show" id="basic_language">
                                                <span><span id="record_languages_res_id"><?php echo $record->languages != null ? $record->languages->name : 'N/A'; ?></span>
                                                    <input type="hidden" id="record_languages_id" value="<?php echo $record->languages != null ? $record->languages->id : ''; ?>">
                                                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                        @if($editAppointmentFlag ==1)
                                                            <a data-toggle="modal" data-target="#exampleModal-languages" data-whatever="@mdo" title="Languages" onclick="updateLanguageDetails()"><i class="fa fa-edit"></i></a>
                                                        @endif
                                                    @endif
                                                    <br></span>
                                            </dd>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php $serviceArr = explode(',', $record->service_id);
            ?>

            @if(strtolower($record->type) == 'patient' && !empty($record->task_health_link))
            <div class="col-sm-12 mb-3" id="patient-ca-inline-section"></div>
            @endif

            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12 grid-margin stretch-card mb-5">
                            <div class="card">
                                <div class="left-section-main info-tab-sec">
                                    <ul class="nav nav-tabs tabs-left sideways left-section-ul">
                                        <li class="active"><a href="#personal-info-section" data-toggle="tab"> <i class="fa fa-info-circle"></i> &nbsp;Personal Information</a>
                                        </li>
                                        @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                            <li><a href="#document-section" data-toggle="tab"
                                                    onclick="loadDocumentAjaxList()"> <i class="mdi mdi-file-document"></i> &nbsp;Document</a>
                                            </li>


                                            <li><a href="#notes-section" data-toggle="tab" onClick="loadAllNotes()"> <i class="mdi mdi-note"></i> &nbsp;Notes</a></li>

                                            @if($record->hha_id != "" || $record->link_hha_caregiver != "" || $record->link_hha_patient != "")
                                            <li>
                                                <a href="#hha-exchange" data-toggle="tab" onClick="loadHHaSection()"> <img src="{{ asset('/img/hha.png')}}" title="HHA"
                                                        alt="HHA" style="height: 17px; width: 17px;"> &nbsp;HHA
                                                    Exchange </a>
                                            </li>
                                            @endif

                                             @if(strtolower($record->type) == 'patient' && !empty($record->task_health_link))
                                                @can('patient-task-health-visit-list')
                                                <li>
                                                    <a href="#task-health-exchange" data-toggle="tab" onclick="loadTaskHealthSection()"> <svg style="height: 17px; width: 17px;" width="35" height="25" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M24.5 0C11.2452 0 0.5 10.7452 0.5 24C13.7548 24 24.5 13.2548 24.5 0Z" fill="#45D2B0" />
                                                            <path d="M24.5 48C37.7548 48 48.5 37.2548 48.5 24C35.2452 24 24.5 34.7452 24.5 48Z" fill="#45D2B0" />
                                                            <path d="M24.5 0C37.7548 0 48.5 10.7452 48.5 24C35.2452 24 24.5 13.2548 24.5 0Z" fill="#E748F5" />
                                                            <path d="M24.5 48C11.2452 48 0.499999 37.2548 0.5 24C13.7548 24 24.5 34.7452 24.5 48Z" fill="#E748F5" />
                                                        </svg> Task Health </a>
                                                </li>
                                                @endcan
                                            @endif

                                            @if($user['user_type_fk'] == 184)
                                                <li><a href="#task-section" data-toggle="tab" onclick="getTaskList(1)"> <i class="fa fa-tasks"></i> &nbsp;Task</a></li>

                                                @can('sms-log-list')
                                                    <li><a href="#sms-logs-section" data-toggle="tab" onClick="smsLogs(1)"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                            <path d="M64 0C28.7 0 0 28.7 0 64L0 352c0 35.3 28.7 64 64 64l96 0 0 80c0 6.1 3.4 11.6 8.8 14.3s11.9 2.1 16.8-1.5L309.3 416 448 416c35.3 0 64-28.7 64-64l0-288c0-35.3-28.7-64-64-64L64 0z" />
                                                        </svg> &nbsp;SMS Logs</a>
                                                    </li>
                                                @endcan
                                                    <li><a href="#appointment-section" onclick="loadAppointmentsSection(1)" data-toggle="tab"> <i class="fa fa fa-vcard"></i> &nbsp;Appointment </a>
                                                    </li>
                                                    <li><a href="#text-messages-section" data-toggle="tab"
                                                            onClick="loadAllTextMessages()"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                <path d="M64 0C28.7 0 0 28.7 0 64L0 352c0 35.3 28.7 64 64 64l96 0 0 80c0 6.1 3.4 11.6 8.8 14.3s11.9 2.1 16.8-1.5L309.3 416 448 416c35.3 0 64-28.7 64-64l0-288c0-35.3-28.7-64-64-64L64 0z" />
                                                            </svg> &nbsp;Text Messages</a>
                                                    </li>

                                                @can('esign-list-v2')
                                                    <li><a href="#esign-section-new" data-toggle="tab"
                                                            onclick="esignResponseNew1()"> <i class="fa fa-outdent"></i> &nbsp;Esign Section</a>
                                                    </li>
                                                @endcan
                                                @can('advance-form-list')
                                                    <li><a href="#patient-custom-data" data-toggle="tab"><i class="fa fa-cogs"></i> &nbsp;Attribute</a>
                                                    </li>
                                                @endcan
                                                @if(isset($agencyDetails->enable_hha) && $agencyDetails->enable_hha == 1)
                                                    @if($record->hha_id != "" || $record->link_hha_caregiver)



                                                    @endif

                                                @endif

                                                @if($record->alaycare_id != "")
                                                    <li>
                                                        <a href="#alaycare" data-toggle="tab" onClick="getAlyacareEmployeeDemographic()"> <i class="mdi mdi-application"></i> &nbsp;Alaycare</a>
                                                    </li>
                                                @endif

                                                @if($record->robort_id != "")
                                                    @if($record->externalId != "")
                                                        <li>
                                                            <a href="#robort" data-toggle="tab" onClick="getRemoteBasicDetails()"> <i class="mdi mdi-arrange-bring-forward  mr-1"></i>Remote Focus <img src="{{ asset('/img/emmacare.png')}}" title="Remote Focus" alt="HHA" style="height: 25px; width: 25px;"></a>
                                                        </li>
                                                    @endif
                                                @endif
                                            @endif

                                            @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))

                                                <li><a href="#service-requested-by-patient" onclick="serviceRequestedList()"
                                                        data-toggle="tab"> <i class="fa fa-wrench  mr-1"></i>Service Requested</a></li>
                                                <li><a href="#mq_order_patient" onclick="mdOrders()" data-toggle="tab"><i class="fa fa-reorder mr-1"></i>MD Order</a></li>
                                            @endif
                                            @can('payment-log')
                                                <li><a href="#payment_section" onclick="getPaymentData()" data-toggle="tab"><i class="fa fa-dollar mr-1"></i> Payment Log</a></li>
                                            @endcan
                                            @if(strtolower($record->type) == 'patient')
                                                @can('resolution-log')
                                                    <li><a href="#resolution_log_section" onclick="loadResolutionData(1)" data-toggle="tab"><i class="mdi mdi-buffer mr-1"></i> Chart Resolution</a></li>
                                                @endcan
                                            @endif
                                            @if(strtolower($record->type) =='caregiver')
                                                @if ($record->hha_id != '' || ($record->link_hha_caregiver != '' && $record->link_hha_caregiver != 0))
                                                    @can('hha-caregiver-i9-requirement')
                                                    <li class=""><a role="tab" href="#hha-caregiver-compliance-i9s-section" data-toggle="tab" onclick="getDetailsByUpdatedCaregiverI9()">Caregiver I9s Requirement</a>
                                                    </li>
                                                    @endcan
                                                @endif
                                            @endif

                                            @can('combine-record')
                                                <li><a href="#merge_appoint_listing_section" onclick="mergeAppointmentData()" data-toggle="tab"><i class="fa fa-compress"></i> Merge Appointment</a></li>
                                            @endcan
                                            
                                            @can('ai-call-logs')
                                            @if($record->type == 'Caregiver' && Auth()->user()->agency_fk == "")
                                            <li><a href="#ai-call-logs-section" data-toggle="tab" onclick="loadPatientAiCallLogs()"><i class="mdi mdi-robot menu-icon"></i> AI Call Logs</a></li>
                                            @endif
                                            @endcan

                                            <li id="show_visiting_aid_tabing" style="display:@if(isset($visiting_links['Visiting Aid'][0]->id)) @else none @endif">
                                                <a href="#visiting-aid-tab" data-toggle="tab" onClick="getVisitingDemographic()">Visiting Aid<img src="{{ asset('/img/VisitingAidLogo.png')}}" title="Remote Focus" alt="HHA" style="height: 25px; width: 25px;filter: brightness(0) saturate(100%) invert(100%) sepia(0%) saturate(0%) hue-rotate(221deg) brightness(103%) contrast(102%);"></a>
                                            </li>
                                        @endif
                                            @if(Auth()->user()->agency_fk == "")
                                            <li>
                                                <a href="#call-details-section" data-toggle="tab" onclick="loadCallDetailsTabSection()"><i class="fa fa-phone mr-1"></i> Call Details</a>
                                            </li>
                                            @endif
                                            {{-- <li><a href="#patient-linked-files-section" data-toggle="tab" onclick="loadPatientLinkedFiles()"><i class="fa fa-paperclip mr-1"></i> Linked Files</a></li> --}}
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content left-section-tab-content">
                                        <div class="tab-pane active" id="personal-info-section">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="title">
                                                                    <h5><i class="mdi mdi-information mr-1"></i>Basic Details
                                                                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                        @if($editAppointmentFlag ==1)
                                                                            <a class="show pull-right" onclick="setBasicDetails()"><i
                                                                                    class="fa fa-edit"></i></a> <a class="hide pull-right" onclick="setBasicDetails()"><i
                                                                                    class="fa fa-close"></i></a> <a class="hide pull-right mr-2" onclick="saveBasicDetails()"><i
                                                                                    class="fa fa-save"></i></a>
                                                                        @endif
                                                                    @endif
                                                                            </h5>
                                                                </div>
                                                                <div class="row basic-detail-row">
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Code</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_patient_code"> <?php echo $record->patient_code . '<br>'; ?>
                                                                                </dd>
                                                                                <dd class="hide"> <input type="text" id="patient_code" name="patient_code" placeholder="Code" value="<?php if (isset($record->patient_code) && $record->patient_code != '') {
                                                                                                                                                                                            echo $record->patient_code;
                                                                                                                                                                                        } ?>" class="form-control">
                                                                                    <span id="radio_type_error" class="error mt-2"><?php echo $errors->add_agency->first('patient_code'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt> First Name</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_first_name"> <?php echo $record->first_name . '<br>'; ?>
                                                                                </dd>
                                                                                <dd class="hide"> <input type="text" class="form-control charCls" placeholder="Enter First Name " id="first_name_id" name="first_name" value="<?php echo $record->first_name; ?>">
                                                                                    <span id="first_name_error" class="error mt-2"><?php echo $errors->add_agency->first('first_name'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt> Middle Name</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_middle_name"> <?php if (isset($record->middle_name) && $record->middle_name != '') {
                                                                                                                                echo $record->middle_name;
                                                                                                                            } else {
                                                                                                                                echo 'N/A';
                                                                                                                            } ?>
                                                                                </dd>
                                                                                <dd class="hide"> <input type="text" id="middle_name" name="middle_name" placeholder="Middle Name" value="<?php if (isset($record->middle_name) && $record->middle_name != '') {
                                                                                                                                                                                                echo $record->middle_name;
                                                                                                                                                                                            } ?>" class="form-control">
                                                                                    <span id="radio_type_error" class="error mt-2"><?php echo $errors->add_agency->first('middle_name'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Last Name</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_last_name"> <?php if (isset($record->last_name) && $record->last_name != '') {
                                                                                                                            echo $record->last_name . '<br>';
                                                                                                                        } else {
                                                                                                                            echo 'N/A';
                                                                                                                        } ?></dd>
                                                                                <dd class="hide"> <input type="text" class="form-control charCls" placeholder="Enter Last Name " id="last_name_id" name="last_name" value="<?php echo $record->last_name; ?>">
                                                                                    <span id="last_name_error" class="error mt-2"><?php echo $errors->add_agency->first('last_name'); ?></span>
                                                                                    <span id="radio_type_error" class="error mt-2"><?php echo $errors->add_agency->first('last_name'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Date of Birth</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show"> <span id="patient_dob">
                                                                                        <?php if ($record->dob != '0000-00-00') {
                                                                                            echo Common::convertMDY($record->dob);
                                                                                        } else {
                                                                                            echo '';
                                                                                        } ?>
                                                                                    </span>
                                                                                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                        @if($editAppointmentFlag ==1)
                                                                                            <a data-toggle="modal"
                                                                                                data-target="#exampleModal-dob"
                                                                                                data-whatever="@mdo"
                                                                                                title="Date Of Birth"><i
                                                                                                    class="fa fa-edit"></i></a>
                                                                                        @endif
                                                                                    @endif
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" name="dob" class="form-control" placeholder="Select Date of Birth" id="dob_id"
                                                                                        data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy"  min="1000-01-01" max="9999-12-31"
                                                                                        value="<?php if ($record->dob != '') {
                                                                                                                        echo date('m/d/Y', strtotime($record->dob));
                                                                                                                    } ?>">
                                                                                    <span id="dob_error" class="error mt-2"><?php echo $errors->add_agency->first('dob'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Insurance ID</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show"><span id="basic_insuurance_id"><?php if ($record->insurance_id != '') {
                                                                                                                                    echo $record->insurance_id;
                                                                                                                                } ?></span>
                                                                                </dd>
                                                                                <dd class="hide"> <input type="text" class="form-control" autocomplete="off" placeholder="Enter Insurance ID" name="insurance_id" value="{{ $record->insurance_id }}">
                                                                                    <span id="insurance_id_error" class="error mt-2"><?php echo $errors->add_agency->first('insurance_id'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>SSN</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show">
                                                                                    <span id="basic_ssn">
                                                                                        {{$record->ssn}}</span>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control" placeholder="Enter SSN" id="ssn" name="ssn" value="<?php echo $record->ssn; ?>">
                                                                                    <span id="ssn_error" class="error mt-2"><?php echo $errors->add_agency->first('ssn'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Insurance Name</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show">
                                                                                    <span id="basic_insurance_name">

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
                                                                                <dd class="hide">
                                                                                    <select class="form-control" name="insurance_name" id="insurance_name">
                                                                                        <option value="">Select Insurance Name</option>
                                                                                        @if (count($insuranceList) > 0)
                                                                                        @foreach ($insuranceList as $insurance)
                                                                                        <option value="{{ $insurance->id }}" @if($record->insurance_name ==$insurance->id) selected @endif>{{ $insurance->insurance_name }}
                                                                                        </option>
                                                                                        @endforeach
                                                                                        @endif

                                                                                        <option value="other" @if($record->insurance_name =='other') selected @endif>Other</option>
                                                                                    </select>
                                                                                    <span id="insurance_name_error" class="error mt-2"><?php echo $errors->add_agency->first('insurance_name'); ?></span>
                                                                                </dd>
                                                                                
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6 show">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Payment Type</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span id="basic_payment_type">{{ $record->payment_type_new }}</span>
                                                                                        @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                        @if($editAppointmentFlag ==1)
                                                                                            <a data-toggle="modal" data-target="#exampleModal-payment-type" data-whatever="@mdo" title="Payment Type"><i class="fa fa-edit"></i></a>
                                                                                            @endif
                                                                                        @endif
                                                                                </dd>
                                                                                {{-- <dd class="hide">
                                                                                    <select class="form-control" name="payment_type" id="payment_type">
                                                                                        <option value="">Select Payment Type</option>
                                                                                        @if (count($masterData) > 0)
                                                                                            @foreach ($masterData as $master)
                                                                                                @if ($master->master_type_fk == 17)
                                                                                                    <option value="{{ $master->id }}" {{$record->payment_type == $master->id ? 'selected' : '' }}>{{ $master->name }}
                                                                                                    </option>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </select>
                                                                                    <span id="payment_type_error" class="error mt-2"><?php echo $errors->add_agency->first('payment_type'); ?></span>
                                                                                </dd> --}}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php if ($record->agency_id  == '106') { ?>
                                                                        <div class="col-md-6">
                                                                            <div class="row">
                                                                                <div class="col-md-5">
                                                                                    <dt>Payment</dt>
                                                                                </div>
                                                                                <div class="col-md-7">
                                                                                    <dd class="show">
                                                                                        <span id="basic_payment_type_ham"><?php echo $record->hamaspik_payment == 1 ? 'Hamaspik 1' : 'Hamaspik 2<br>'; ?></span>
                                                                                    </dd>
                                                                                    <dd class="hide">
                                                                                        <input type="radio" name="hamaspik_payment" <?= $record->hamaspik_payment == 1 ? 'checked' : '' ?> value="1">
                                                                                        Hamaspik 1
                                                                                        <input type="radio" name="hamaspik_payment" <?= $record->hamaspik_payment == 1 ? '' : 'checked' ?> value="2">Hamaspik 2
                                                                                    </dd>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <div class="col-md-6" style="@if($record->insurance_name !='' && $record->insurance_name =='other') @else display:none @endif" id="other_insurance">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Other Insurance Name</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show">
                                                                                    <span id="basic_other_insurance_name">{{ $record->other_insurance_name }} </span>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" id="other_insurance_name" name="other_insurance_name" class="form-control" placeholder="Enter Other Insurance Name" value="{{ $record->other_insurance_name }}">
                                                                                    <span id="other_insurance_name_error" class="error mt-2"><?php echo $errors->add_agency->first('other_insurance_name'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row ">
                                                                            <div class="col-md-5">
                                                                                <dt>Discipline</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show"> <span
                                                                                        id="diciplin"><?php echo $record->diciplin; ?></span>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <select class="js-example-basic-multiple w-100" name="diciplin" id="diciplin_id">
                                                                                        <option value="">Select Discipline</option>

                                                                                        @if (count($masterData) > 0)
                                                                                        @foreach ($masterData as $master)
                                                                                        @if ($master->master_type_fk == 26)
                                                                                        <option value="{{ $master->name }}" {{$record->diciplin == $master->name ? 'selected' : '' }}>{{ $master->name }}
                                                                                        </option>
                                                                                        @endif
                                                                                        @endforeach
                                                                                        @endif

                                                                                    </select>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Location / Branch</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show">
                                                                                    <span id="location_branch_text">
                                                                                        @if($record->location_branch !=""){{ $record->location_branch}} @else - @endif
                                                                                    </span>
                                                                                    <a class="ml-2" data-toggle="modal" data-target="#edit-branch-modal" id="ex-edit-branch-modal" data-whatever="@mdo"><i class="fa fa-edit"></i></a><br>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Agency Rep</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="agency_user_resp">
                                                                                   <span id="new_agency_user_resp"> {{ !empty($record->agency_rep) ? $record->agency_rep : '-' }}</span>
                                                                                   <input type="hidden" id="edit_agency_rep" value="{{ $record->agency_rep }}">
                                                                                    <input type="hidden" id="edit_agency_rep_id" value="{{ $record->agency_user_id }}">
                                                                                    <a href="javascript:void(0)" class="ml-2" data-toggle="modal" data-target="#editAgencyUserRepModal" onclick="initAgencyUserRepToken()"><i class="fa fa-edit"></i></a>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card address-detail-div">
                                                                <div class="title ">
                                                                    <h5><i class="fa fa-address-card mr-1"></i>Address/Contact Details
                                                                        @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                            @if($editAppointmentFlag ==1)
                                                                                <a class="show pull-right" onclick="setAddressDetails()"><i class="fa fa-edit"></i></a>
                                                                                <a class="hide pull-right" onclick="setAddressDetails()"><i class="fa fa-close"></i></a>
                                                                                <a class="hide pull-right mr-2" onclick="saveAddressDetails()"><i class="fa fa-save"></i></a>
                                                                            @endif
                                                                        @endif
                                                                    </h5>
                                                                </div>
                                                                <div class="row basic-detail-row">

                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Country</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_county"> <?php echo $record->county == null ? 'N/A' : $record->county . '<br>'; ?>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control" id="county" name="county" readonly onkeypress="return isNumber(event)" value="{{ $record->county }}">
                                                                                    <span id="county_error" class="error mt-2"></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>State</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_state"> <?php echo $record->state . '<br>'; ?>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control charCls" placeholder="Enter State" id="state" name="state" value="{{ $record['state'] }}" maxlength="50">
                                                                                    <span id="state_error" class="error mt-2"></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>City</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_city"> <?php echo $record->city . '<br>'; ?>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control charCls" placeholder="Enter City" id="city" name="city" value="{{ $record['city'] }}" maxlength="50">
                                                                                    <span id="city_error" class="error mt-2"></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Zip Code</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_zipcode"> <?php echo $record->zip_code . '<br>'; ?>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control" placeholder="Enter Zip Code" id="zip_code" name="zip_code" onkeypress="return isNumber(event)" onchange="getCountyByZipCode(this.value)" value="{{ $record['zip_code'] }}">
                                                                                    <span id="zip_code_error" class="error mt-2"></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Address1</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_address1"> <?php echo $record->address1 . '<br>'; ?>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control" placeholder="Enter Address 1" id="address1" name="address1" value="{{ $record['address1'] }}">
                                                                                    <span id="address1_error" class="error mt-2"></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt> Apt/Suite/Floor</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_address2"> <?php echo $record->address2 . '<br>'; ?>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control" placeholder="Enter Apt/Suite/Floor" id="address2" name="address2" value="{{ $record['address2'] }}">
                                                                                    <span id="address2_error" class="error mt-2"></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Email</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_email"> <span id="emergency_email"><?php if ($record->email != '') { echo $record->email; } ?></span>
                                                                                @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                @if($editAppointmentFlag ==1)
                                                                                    <a data-toggle="modal"
                                                                                        data-target="#exampleModal-email"
                                                                                        data-whatever="@mdo"
                                                                                        title="Email"
                                                                                        onclick="updateEmailDetails('{{ $record->email}}')"><i
                                                                                            class="fa fa-edit"></i></a>
                                                                                            @endif
                                                                                @endif
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control" placeholder="Enter Email " id="email" name="email" value="<?php echo $record->email; ?>">
                                                                                    <span id="email_error" class="error mt-2"></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-12 emergency-phone-row">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt>Emergency Contact Name</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7">
                                                                                        <dd class="show" id="basic_emergency_contact_name">
                                                                                            @if($record->emergency_contact_name != ""){{$record->emergency_contact_name}}
                                                                                            @else - @endif
                                                                                        </dd>
                                                                                        <dd class="hide">
                                                                                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" placeholder="Enter Emergency Contact Name" value="{{ $record->emergency_contact_name }}">
                                                                                        </dd>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @if ($record->agency_id != '319' || $record->agency_id != '106')
                                                                            <div class="col-md-6 ">
                                                                                <div class="row ">
                                                                                    <div class="col-md-5">
                                                                                        <dt>Emergency Contact Number</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7">
                                                                                        <dd class="show" id="basic_emergency_phone"> @if($record->emergency_phone != ""){{$record->emergency_phone}}
                                                                                            @else
                                                                                            - @endif
                                                                                        </dd>
                                                                                        <dd class="hide">
                                                                                            <input type="text" id="emergency_phone" name="emergency_phone" onkeypress="return isNumber(event)" class="form-control" placeholder="Enter Emergency Contact Number" value="{{ $record->emergency_phone }}">
                                                                                        </dd>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card other-detail-div">
                                                                <div class="title ">
                                                                    <h5><i class="fa fa-list-alt mr-1"></i> Other Details
                                                                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                    @if($editAppointmentFlag ==1)
                                                                        <a class="show pull-right" onclick="setOtherDetails()"><i class="fa fa-edit"></i></a>
                                                                        <a class="hide pull-right" onclick="setOtherDetails()"><i class="fa fa-close"></i></a>
                                                                        <a class="hide pull-right mr-2" onclick="saveOtherDetails()"><i class="fa fa-save"></i></a>
                                                                        @endif
                                                                    @endif
                                                                    </h5>
                                                                </div>
                                                                <div class="row other-detail-row">
                                                                    <?php if ($record->type    == 'Caregiver') {
                                                                        if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt>Training Due Date</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7">
                                                                                        <dd>
                                                                                            <span id="payment_type_id"><?php
                                                                                                                        $traning_due_date = "";
                                                                                                                        if ($record->traning_due_date != '' && $record->traning_due_date != '1969-12-31') {
                                                                                                                            $traning_due_date = date('m/d/Y', strtotime($record->traning_due_date));
                                                                                                                            echo $traning_due_date;
                                                                                                                        } ?>
                                                                                                @if($editAppointmentFlag ==1)
                                                                                                <a data-toggle="modal" data-target="#exampleModal-traning_due_date" data-whatever="@mdo" title="Training Due Date" onclick="updateTrainingDueDate('{{ $traning_due_date}}')"><i class="fa fa-edit"></i></a>
                                                                                                @endif
                                                                                        </dd>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                   <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Notes</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                            <input type="hidden" id="html_patient_nots"  value="{{ $record->remarks}}">
                                                                                <dd class="show">
                                                                                    <span id="html_patient_notes_id"><?php echo $record->remarks == null ? 'N/A' : $record->remarks . ''; ?></span>
                                                                                    @can('notes-update')
                                                                                        <a class="ml-2" href="javascript:void(0)" onclick="editNotes()" title="Edit Notes" data-toggle="modal" data-target="#exampleModal-patient-notes" data-whatever="@mdo"><i class="fa fa-edit"></i></a>
                                                                                    @endcan
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <textarea class="form-control" placeholder="Notes" name="remarks" id="message" style="height: 50px"><?php echo $record->remarks; ?></textarea>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Medicare No</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show" id="basic_medicareno">@if($record->medicare_no !=""){{ $record->medicare_no}} @else N/A @endif</dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" class="form-control" placeholder="Enter Medicare No" id="medicare_no" name="medicare_no" value="{{ $record->medicare_no }}" maxlength="15">
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>CIN /Medicaid Number</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show">
                                                                                    <span id="basic_cin"><?php if ($record->cin != '') {
                                                                                                                echo $record->cin;
                                                                                                            } ?></span>
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" id="cin" name="cin" class="form-control" placeholder="Enter CIN/Medicaid Number" value="{{ $record->cin }}">
                                                                                    <span id="cin_error" class="error mt-2"><?php echo $errors->add_agency->first('cin'); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if(strtolower($record->type) != 'caregiver')
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Pharmacy Name</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span id="pharmacy_name_text">{{ $record->pharmacy_name != '' ? $record->pharmacy_name : '-' }}</span>
                                                                                    <a class="ml-2" data-toggle="modal" data-target="#edit-pharmacy-name-modal" onclick="openPharmacyNameModal()" data-whatever="@mdo"><i class="fa fa-edit"></i></a>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Pharmacy Number</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span id="pharmacy_no_text">{{ $record->pharmacy_no != '' ? $record->pharmacy_no : '-' }}</span>
                                                                                    <a class="ml-2" data-toggle="modal" data-target="#edit-pharmacy-no-modal" onclick="openPharmacyNoModal()" data-whatever="@mdo"><i class="fa fa-edit"></i></a>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                    @if(auth()->user()->agency_fk == "")
                                                                        <div class="col-md-6">
                                                                            <div class="row">
                                                                                <div class="col-md-5">
                                                                                    <dt>No Medication Taken</dt>
                                                                                </div>
                                                                                <div class="col-md-7">
                                                                                    <dd>
                                                                                        <input type="checkbox" id="no_medication_taken_checkbox"
                                                                                            {{ $record->no_medication_taken == 1 ? 'checked' : '' }}
                                                                                            onchange="saveNoMedicationTaken(this)">
                                                                                    </dd>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Completed Date</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd class="show"><span id="comp_id"><?php if ($record->completed_date != '') {
                                                                                                                        echo date('m/d/Y', strtotime($record->completed_date));
                                                                                                                        } ?> </span>
                                                                                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                        @if($editAppointmentFlag ==1)
                                                                                        <a data-toggle="modal" data-target="#exampleModal-complete" data-whatever="@mdo"><i class="fa fa-edit"></i></a>
                                                                                        @endif
                                                                                    @endif
                                                                                </dd>
                                                                                <dd class="hide">
                                                                                    <input type="text" readonly name="completed_date" class="form-control" id="id_completed_date"
                                                                                        data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy"
                                                                                        im-insert="false" value="<?php if ($record->completed_date != '') { echo date('m/d/Y', strtotime($record->completed_date)); } ?>">
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Created Date</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span><?php echo Common::convertMDYTime($record->created_date); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Created By</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span>{{$record->createdBy}} @if($record->userTypes !="")({{ $record->userTypes }})@endif</span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Last Updated Date</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span><?php echo Common::convertMDYTime($record->updated_date); ?></span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Last Updated By</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span>{{$record->updatedBy}} @if($record->updateUserTypes !="")({{ $record->updateUserTypes }})@endif</span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php if ($record->type    == 'Caregiver') {
                                                                        if ($record->agency_id  == '319'  ||  $record->agency_id  == '106' ||  $record->agency_id  == '43') { ?>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt>In Service Status First</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7">
                                                                                        <dd><span id="inservices_status"> {{ ($record->inservice_status !="")?$record->inservice_status:"N/A" }}</span><a data-toggle="modal" data-target="#exampleModal-inservice_status" data-whatever="@mdo"><i class="fa fa-edit"></i></a><br></dd>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt>In Service Status Second</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7">
                                                                                        <dd><span id="inservices_status_two"> {{ ($record->inservice_status_two !="")?$record->inservice_status_two:"N/A" }}</span><a data-toggle="modal" data-target="#exampleModal-inservice_status_two" data-whatever="@mdo"><i class="fa fa-edit"></i></a><br></dd>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    <?php }
                                                                    } ?>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                            @if($record->agency_id  == '43')
                                                                            <dt>In Service Due Date</dt>
                                                                            @else
                                                                            <dt>In Service Date</dt>
                                                                            @endif

                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd><span id="inservices_dates"> {{ ($record->inservice_datetime !="")?date('m/d/Y  h:i A',strtotime($record->inservice_datetime)) :"N/A" }}</span><br></dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Call Note Count</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    {{$record->callCounter}}
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if($record->type =='Caregiver')
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Transition Aid</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>@if($record->transition_aid ==1) Yes @else No @endif</dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                    @can('flag-change-status')
                                                                    @if($record->flag ==1)
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Flag</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>@if($record->flag ==1) Flagged @else Flag @endif</dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                @endcan

                                                                <?php if ($record->status == 'cancelled' || $record->status == 'refused') { ?>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Reason</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd><?php echo $record->reasonname; ?>@if(!empty($record->otherreasonname))
                                                                                        <i class="fa fa-info-circle ml-1" style="cursor: pointer; color: #17a2b8;" data-toggle="tooltip" data-placement="top" title="{{ $record->otherreasonname }}"></i>
                                                                                    @endif</dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($record->type    == 'Caregiver') {
                                                                    if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                                        <div class="col-md-6">
                                                                            <div class="row">
                                                                                <div class="col-md-5">
                                                                                    <dt>Training Status</dt>
                                                                                </div>
                                                                                <div class="col-md-7">
                                                                                    <dd><span id="training_statuss"> {{ ($record->training_status !="")?$record->training_status:"N/A" }}</span><a data-toggle="modal" data-target="#exampleModal-training_status" data-whatever="@mdo" onclick="updateDetails('{{ $record->training_status}}')"><i class="fa fa-edit"></i></a><br></dd>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                <?php }
                                                                } ?>
                                                                <?php if ($record->agency_id  == '319'  ||  $record->agency_id  == '106') { ?>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Medical Followup Date</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd><span id="{{ $record->agency_id}}_follow_update"> {{ ($record->follow_date !="")?date('m/d/Y',strtotime($record->follow_date)):"N/A" }}</span>&nbsp;&nbsp;<a data-toggle="modal" data-target="#exampleModal-follow_date" data-whatever="@mdo"><i class="fa fa-edit"></i></a><br></dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($record->agency_id  == '106') { ?>
                                                                    <div class="col-md-6">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Availability followup Date</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd><span id="{{ $record->agency_id}}_availability_followup_date"> {{ ($record->availability_followup_date !="" && $record->availability_followup_date !="0000-00-00")?date('m/d/Y',strtotime($record->availability_followup_date)):"" }}</span>&nbsp;&nbsp;<a data-toggle="modal" data-target="#exampleModal-availibility-followup_date" data-whatever="@mdo"><i class="fa fa-edit"></i></a><br></dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                {{-- <div class="col-md-6" id="appointment_merge_id" style="display:@if($record->merge_appointment_id !='')@else none @endif">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Merged Id</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="html_appointment_id"><a href="{{ url('/deleted_appointment_show/')}}/{{ $record->merge_appointment_id}}" target="_blank">{{ $record->merge_appointment_id}}</a>
                                                                            @can('unmerge-record')
                                                                            <a class="unmerge_appointment_id"  style="display:@if($record->merge_appointment_id !='')@else none @endif" onclick="unMergeAppointment('{{ $record->id}}','{{ $record->merge_appointment_id}}')" title="Unmerge"><i class="fa fa-undo"></i></a>
                                                                        @endcan
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div> --}}
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Portal</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd>
                                                                                @if(isset($record->platform_id) && $record->platform_id != '')
                                                                                API Portal
                                                                                @else
                                                                                Admin Portal
                                                                                @endif
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Referral Type</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd>
                                                                                @php
                                                                                    $type = $record->referral_type;
                                                                                @endphp
                                                                            <span id="html_referral_type_source_id">

                                                                                @if($record->referral_type !="")
                                                                                    {{ $record->referral_type }}
                                                                                @else
                                                                                    @if($record->hha_id !="" || $record->link_hha_caregiver !="" || $record->link_hha_patient !="")
                                                                                        @php
                                                                                            $type = "HHA Exchange";
                                                                                        @endphp
                                                                                        HHA Exchange
                                                                                    @elseif($record->alaycare_id !="")
                                                                                        @php
                                                                                            $type = "Alayacare";
                                                                                        @endphp
                                                                                    Alayacare
                                                                                    @elseif($record->robort_id !="")
                                                                                        @php
                                                                                            $type = "Remote Focus";
                                                                                        @endphp
                                                                                    Remote Focus
                                                                                    @elseif($record->platform_type =="VA")
                                                                                        @php
                                                                                            $type = "Visiting Aid";
                                                                                        @endphp
                                                                                    Visiting Aid
                                                                                    @endif
                                                                                @endif
                                                                            </span>
                                                                                <input type="hidden" id="edit_referral_type_source_id" value="{{ $type}}">
                                                                                @if(auth()->user()->agency_fk =="")
                                                                                    <a class="ml-2" data-toggle="modal" data-target="#exampleModal-edit-referral-source-modal" id="edit-referral-source-modal" data-whatever="@mdo" onclick="editReferralSourceType()"><i class="fa fa-edit"></i></a>
                                                                                @endif
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if(isset($nybestUserData) && count($nybestUserData) > 0)
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Assign Liaison</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd>
                                                                                @foreach($nybestUserData as $index => $nydata)
                                                                                    @if(isset($nydata['first_name']) && !empty($nydata['first_name']))
                                                                                        <span class="nybest-chip">{{ $nydata['first_name'] }} {{ $nydata['last_name'] }} ({{$nydata['email']}})</span>
                                                                                    @endif
                                                                                @endforeach
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif

                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Assign Department</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd>
                                                                                <span class="badge badge-success" id="assign_department"></span>
                                                                                @if(auth()->user()->agency_fk =="")
                                                                                    <a class="ml-2" data-toggle="modal" data-target="#assign-dept-modal" id="dept-modal" data-whatever="@mdo" onclick="assignDepartment()"><i class="fa fa-edit"></i></a>
                                                                                @endif
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <div class="appointment-detail-sec info-box card">
                                                        <div class="title">
                                                            <h5><i class="mdi mdi-timer mr-1"></i>Appointment Details</h5>
                                                        </div>
                                                        <dl class="mb-0">
                                                            <div class="row appointment-details-row">
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt> Appointment Date</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd> <?php echo Common::convertMDY($record->appointment_date) . '<br>'; ?>
                                                                                @if($record->status != 'completed' && $record->status != 'cancelled' && $record->status != 'noshow')
                                                                                    @if($user['user_type_fk'] == 184)
                                                                                        @if($record->type =='Caregiver' && $editAppointmentFlag ==1)
                                                                                        <a href="javascript:void(0)"
                                                                                                class="d-none d-md-block"
                                                                                                data-toggle="modal" data-target="#exampleModal-4" onClick="selectedSchedule()"
                                                                                                data-whatever="@mdo" style="margin-right: 10px;" title="Schedule Appointment"> <i class="fa fa-edit"></i></a>
                                                                                        @endif

                                                                                    @endif
                                                                                @endif

                                                                            </dd>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Appointment Time</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <?php if ($record->type == 'Caregiver' && $record->start_time) {
                                                                            ?>
                                                                                <dd><?php echo date('h:i A', strtotime($record->start_time)) . ' - ' . date('h:i A', strtotime($record->edate)) . '<br>'; ?>
                                                                                </dd>
                                                                            <?php } else { ?>
                                                                                <dd>
                                                                                    @if($record->appointment_date != '')
                                                                                    <?php echo date('h:i A', strtotime($record->appointment_date)) . '<br>'; ?>
                                                                                    @endif
                                                                                </dd>

                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Booked Via</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd>
                                                                                <?php echo ucfirst($record->appointment_mode); ?>
                                                                            </dd>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <?php if ($record->type == 'Caregiver') { ?>
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Location</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span>
                                                                                        <?php echo $record->locations != "" ? $record->locations->address1 : '' . '<br>'; ?>
                                                                                    </span>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($record->agency_id  != '319'  && $record->agency_id  != '106') { ?>
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Next Appointment Date</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    <span id="next_apid"><?php if ($record->next_appoinment_date != '') {
                                                                                                                echo date('m/d/Y', strtotime($record->next_appoinment_date));
                                                                                                            } ?>
                                                                                    </span>&nbsp;&nbsp;
                                                                                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                    @if($editAppointmentFlag ==1)
                                                                                        <a data-toggle="modal" data-target="#exampleModal-70" data-whatever="@mdo" title="Next Appointment Date"><i class="fa fa-edit"></i></a>
                                                                                        @endif
                                                                                    @endif
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Medical Due Date</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd class="show"><span id="basic_medical_due_date">
                                                                                    <?php if ($record->due_date != '' && $record->due_date != '1969-12-31') {
                                                                                        echo date('m/d/Y', strtotime($record->due_date));
                                                                                    } ?></span>
                                                                                @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                @if($editAppointmentFlag ==1)
                                                                                    <a data-toggle="modal" data-target="#exampleModal-67" data-whatever="@mdo" title="Due Date"><i class="fa fa-edit"></i></a>
                                                                                    @endif
                                                                                @endif
                                                                            </dd>
                                                                            <dd class="hide">
                                                                                <input type="text" readonly name="due_date" class="form-control" id="id_due_date" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php if ($record->due_date != '') {
                                                                                                                                                                                                                                                                            echo date('m/d/Y', strtotime($record->due_date));
                                                                                                                                                                                                                                                                        } ?>">
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php if ($record->agency_id  != '319' && $record->agency_id  != '106') { ?>
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>FU Date</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd><?php if ($record->fu_date != '' && $record->fu_date != '1969-12-31') {
                                                                                        echo date('m/d/Y', strtotime($record->fu_date));
                                                                                    } else {
                                                                                        echo 'N/A';
                                                                                    } ?>
                                                                                </dd>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                <?php } ?>
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Service</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd id="html_service_id"> <?php if (isset($record->service) && $record->service != '') {
                                                                                        echo $record->service . '<br>';
                                                                                    } else {
                                                                                        echo 'N/A';
                                                                                    } ?>
                                                                            </dd>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Telehealth Appointment</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                        <dd>
                                                                                <span>
                                                                                    <span id="telehealth-appointment-date-id">
                                                                                        @if(isset($telehealth_time_slot['start_time']))
                                                                                            <p><strong>Date:</strong>{{ date('m/d/Y', strtotime($record->telehealth_date_time))}} </p>
                                                                                            <p><strong>Time Slot:</strong> {{$telehealth_time_slot['start_time']}} - {{$telehealth_time_slot['end_time']}} <br/>
                                                                                            <strong>Nurse</strong>:
                                                                                            C#{{$telehealth_time_slot['nurse_id']}}
                                                                                            @if(isset($nurse) && array_key_exists($telehealth_time_slot['nurse_id'],$nurse))
                                                                                                ({{$nurse[$telehealth_time_slot['nurse_id']]['language']}})
                                                                                            @endif
                                                                                            <br/>
                                                                                            @if(!empty($telehealth_time_slot['name']))<strong>Language:</strong> {{$telehealth_time_slot['name']}}@endif</p>
                                                                                        @endif
                                                                                    </span>
                                                                                    <?php if ($user['user_type_fk'] == 184 || $user['user_type_fk'] == 6) { ?>
                                                                                        <?php

                                                                                        if ($record->status != 'completed' && $record->status != 'cancelled' && $record->status != 'noshow') { ?>
                                                                                            <?php if ($user['user_type_fk'] == 184) { ?>
                                                                                                @if($record->type == 'Caregiver')
                                                                                                    <a href="javascript:void(0)"
                                                                                                    class="d-none d-md-block"
                                                                                                    data-toggle="modal" class="pull-right" data-target="#exampleModal-44"
                                                                                                    data-whatever="@mdo" style="margin-right:10px" title="Telehealth Appointment"><i class="fa fa-edit"></i></a>

                                                                                                @endif
                                                                                            <?php  } ?>
                                                                                    <?php    }
                                                                                    } ?>
                                                                                </span>
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Attachment</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd>
                                                                                <span id="attachment_pdf_ids">
                                                                                    @if ($record->attachment_document != '')
                                                                                    <a href="/dpa/{{$record->id}}"
                                                                                        target="_blank">Download <i
                                                                                            class="fa fa-download"></i></a>
                                                                                    @endif
                                                                                </span>
                                                                                @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                    @if ($record->attachment_document == '')
                                                                                        @if($editAppointmentFlag ==1)
                                                                                    <a data-toggle="modal"
                                                                                        data-target="#exampleModal-attachment"
                                                                                        data-whatever="@mdo"
                                                                                        title="Attachment"><i
                                                                                            class="fa fa-edit"></i></a>
                                                                                            @endif
                                                                                    @endif
                                                                                @endif
                                                                            </dd>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                    </div>

                                                    {{-- Portal Fields Section --}}
                                                    @if(isset($portalFields) && count($portalFields) > 0)
                                                    <div class="appointment-detail-sec info-box card">
                                                        <div class="title">
                                                            <h5><i class="mdi mdi-web mr-1"></i>Portal Fields</h5>
                                                        </div>
                                                        <dl class="mb-0">
                                                            <div class="row appointment-details-row">
                                                                @foreach($portalFields as $portalField)
                                                                    @if(isset($portalField['fields']) && !empty($portalField['fields']))
                                                                        <div class="col-md-12">
                                                                            <div class="row">
                                                                                <div class="col-md-5">
                                                                                    <dt>{{ ucfirst($portalField['fields']['label']) }}</dt>
                                                                                </div>
                                                                                <div class="col-md-7">
                                                                                    <dd>
                                                                                        <span id="portal-field-{{ $portalField['fields']['id'] }}">
                                                                                            @if($portalField['fields']['type'] === 'date' && isset($patientAdvanceSubmitData[$portalField['agency_id']][$portalField['fields']['id']]))
                                                                                                {{ \Carbon\Carbon::parse($patientAdvanceSubmitData[$portalField['agency_id']][$portalField['fields']['id']])->format('m/d/Y') }}
                                                                                            @elseif($portalField['fields']['type'] === 'time' && isset($patientAdvanceSubmitData[$portalField['agency_id']][$portalField['fields']['id']]))
                                                                                                {{ \Carbon\Carbon::parse($patientAdvanceSubmitData[$portalField['agency_id']][$portalField['fields']['id']])->format('h:i A') }}
                                                                                            @else
                                                                                                {{ $patientAdvanceSubmitData[$portalField['agency_id']][$portalField['fields']['id']] ?? '-' }}
                                                                                            @endif
                                                                                        </span>
                                                                                        <a href="javascript:void(0)" class="ml-2 edit-portal-field-btn"
                                                                                            data-field-id="{{ $portalField['fields']['id'] }}"
                                                                                            data-field-label="{{ ucfirst($portalField['fields']['label']) }}"
                                                                                            data-field-type="{{ $portalField['fields']['type'] }}"
                                                                                            data-field-options="{{ $portalField['fields']['options'] ?? '' }}"
                                                                                            data-field-limit="{{ $portalField['fields']['set_character_limit'] ?? '' }}"
                                                                                            data-field-value="{{ $patientAdvanceSubmitData[$portalField['agency_id']][$portalField['fields']['id']] ?? '' }}"
                                                                                            data-agency-id="{{ $portalField['agency_id'] }}"
                                                                                            title="{{ ucfirst($portalField['fields']['label']) }}"><i class="fa fa-edit"></i></a>
                                                                                    </dd>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </dl>
                                                    </div>
                                                    @endif

                                                    <div class="appointment-detail-sec info-box card">
                                                        <div class="title">
                                                            <h5><i class="fa fa-building mr-1"></i>Third Party Details</h5>
                                                        </div>
                                                        <dl class="mb-0">
                                                            <div class="row appointment-details-row">
                                                                <?php if ($record->type == 'Caregiver') { ?>
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt> Link HHX Caregiver</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <input type="hidden" id="hha_caregiver_ids" value="{{ $record->link_hha_caregiver}}">
                                                                                <input type="hidden" id="hha_caregiver_names" value="{{ $record->hhx_caregiver_name}}">
                                                                                <dd><span id="hhx_caregiver_id"> {{ ($record->hhx_caregiver_name !="")?$record->hhx_caregiver_name:"N/A" }}</span>
                                                                                @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                    @if($editAppointmentFlag ==1)
                                                                                <a class="ml-2" data-toggle="modal" data-target="#exampleModal-link-hha" data-whatever="@mdo" onclick="getHHXCaregiverDetails()"><i class="fa fa-edit"></i></a>
                                                                                <span id="hhx_caregiver_link_id" class="@if($record->link_hha_caregiver !='')@else hide @endif">
                                                                                        <a onclick="unlinkHHACaregiver()" title="Unlink HHA Caregiver"><i class="fa fa-unlink"></i></a>
                                                                                    </span>
                                                                                    @endif
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($record->type == 'Patient') { ?>
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt> Link HHX Patient</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <input type="hidden" id="hha_patient_ids" value="{{ $record->link_hha_patient}}">
                                                                                <input type="hidden" id="hha_patient_names" value="{{ $record->hhx_patient_name}}">
                                                                                <dd><span id="hhx_patient_id"> {{ ($record->hhx_patient_name !="")?$record->hhx_patient_name:"N/A" }}</span>
                                                                                @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                    @if($editAppointmentFlag ==1)
                                                                                <a class="ml-2" data-toggle="modal" data-target="#exampleModal-link-hha-patient" data-whatever="@mdo" onclick="getHHXPatientDetails()"><i class="fa fa-edit"></i></a>
                                                                                    <span id="hhx_patient_link_id" class="@if($record->link_hha_patient !='')@else hide @endif">
                                                                                        <a onclick="unlinkHHAPatient()" title="Unlink HHA Patient"><i class="fa fa-unlink"></i></a>
                                                                                    </span>
                                                                                    @endif
                                                                                    @endif
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                <?php } ?>
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Link To Third Party</dt>
                                                                        </div>
                                                                        <div class="col-md-7">

                                                                            <dd><span id="link_third_party_id"> {{ ($record->link_third_party != "") ? $record->link_third_party_name: "N/A" }}</span>&nbsp;&nbsp;
                                                                                @if($user['user_type_fk'] == 184 )
                                                                                <a data-toggle="modal" data-target="#exampleModal-link-third-party-id" id="link-third-party-popup" data-whatever="@mdo" onclick="linkThirdParty()"><i class="fa fa-edit"></i></a><br>
                                                                                @endif
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if( isset($agencyDetails->alaycare_status) && $agencyDetails->alaycare_status ==1)
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Alaycare Id</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            @if($record->type =='Patient')
                                                                            <dd><span id="hhx_alaycare_client_id"> {{ ($record->alaycare_id != "") ? $record->alaycare_name . ' (' . $record->alaycare_id . ')' : "N/A" }}</span>&nbsp;&nbsp;
                                                                                @if($user['user_type_fk'] == 184 )
                                                                                <a data-toggle="modal" data-target="#exampleModal-link-alaycare-client-id" id="alaycare-client-popup" data-whatever="@mdo"><i class="fa fa-edit"></i></a><br>
                                                                                @endif
                                                                            </dd>
                                                                            <input type="hidden" id="alayacare_existing_client_id" value="{{ $record->alaycare_id}}">

                                                                            @else
                                                                            <dd><span id="hhx_alaycare_id"> {{ ($record->alaycare_id != "") ? $record->alaycare_name . ' (' . $record->alaycare_id . ')' : "N/A" }}</span>&nbsp;&nbsp;
                                                                                @if($user['user_type_fk'] == 184 )
                                                                                <a data-toggle="modal" data-target="#exampleModal-link-alaycare-id" id="alaycare-popup" data-whatever="@mdo"><i class="fa fa-edit"></i></a><br>
                                                                                @endif
                                                                            </dd>
                                                                            <input type="hidden" id="alayacare_existing_emp_id" value="{{$record->alaycare_id}}">

                                                                            @endif
                                                                            <input type="hidden" id="alayacare_existing_name" value="{{ $record->alaycare_name }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                @if( isset($agencyDetails->robort_status) && $agencyDetails->robort_status ==1)
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Remote Id</dt>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <dd><span id="hhx_robort_id"> {{ ($record->robort_id != "") ? $record->remote_name : "N/A" }}</span>&nbsp;&nbsp;
                                                                                @if($user['user_type_fk'] == 184 )
                                                                                <a data-toggle="modal" data-target="#exampleModal-link-remote-id" id="remote-popup" data-whatever="@mdo"><i class="fa fa-edit"></i></a><br>
                                                                                @endif
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                @if( isset($record->agency_id) && in_array($record->agency_id,[224,2]))
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <dt>Link to Visiting Aid</dt>
                                                                        </div>
                                                                        <div class="col-md-7">

                                                                            <dd><span id="link_visiting_aid_id"> @if(isset($visiting_links['Visiting Aid'][0]->id)) {{ $visiting_links['Visiting Aid'][0]->third_party_first_name.' '.$visiting_links['Visiting Aid'][0]->third_party_last_name }} ( {{ $visiting_links['Visiting Aid'][0]->third_party_code }}) @endif</span>&nbsp;&nbsp;
                                                                            @if($user['user_type_fk'] == 184 )
                                                                            <a class="ml-2" data-toggle="modal" data-target="#exampleModal-link-visiting-id"  data-whatever="@mdo" ><i class="fa fa-edit"></i></a><br>
                                                                            @endif
                                                                            </dd>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                @if($record->type == 'Patient')
                                                                    @if(isset($agencyDetails->enable_task_health) && $agencyDetails->enable_task_health == 1)
                                                                        <div class="col-md-12">
                                                                            <div class="row">
                                                                                <div class="col-md-5">
                                                                                    <dt>Link Task Health Patient</dt>
                                                                                </div>
                                                                                <div class="col-md-7">
                                                                                    <input type="hidden" id="task_health_patient_id" value="{{ $record->task_health_link }}">
                                                                                    <input type="hidden" id="task_health_patient_name" value="{{ $record->task_health_patient_name }}">
                                                                                    <dd><span id="task_health_patient_display"> {{ ($record->task_health_patient_name != "")?$record->task_health_patient_name:"N/A" }}</span>
                                                                                    @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                                                                        @if($editAppointmentFlag ==1)
                                                                                        <a class="ml-2" data-toggle="modal" data-target="#exampleModal-link-task-health-patient" data-whatever="@mdo"><i class="fa fa-edit"></i></a>
                                                                                            <span id="task_health_patient_link_id" class="@if($record->task_health_link != '')@else hide @endif">
                                                                                                <a onclick="unlinkTaskHealthPatient()" title="Unlink Task Health Patient"><i class="fa fa-unlink"></i></a>
                                                                                            </span>
                                                                                        @endif
                                                                                    @endif
                                                                                    </dd>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                    </div>

                                                    <div class="appointment-detail-sec info-box card">
                                                        <div class="title">
                                                            <h5><i class="fa fa-id-badge mr-1"></i> Merge Details
                                                                @can('combine-record')
                                                                <a href="javascript:void(0);" id="edit_merge_appointment_id" data-toggle="modal" data-target="#exampleModal-merge-record"
                                                                    data-whatever="@mdo"
                                                                    class="pull-right" title="Merge Record"><i class="fa fa-edit"></i>
                                                                </a>
                                                                @endcan
                                                            </h5>
                                                        </div>
                                                        <dl class="mb-0">
                                                            <div class="row appointment-details-row">
                                                                <?php if ($user['user_type_fk'] == 184) { ?>
                                                                    <a id="redirection_page" target="_blank"></a>
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <dt>Select Agency</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd><select class="form-control" id="patient_wise_agency_id">
                                                                                        <option value="">Select Agency</option>
                                                                                    </select>
                                                                                </dd>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="document-section">

                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="card-title mb-0">Document</p>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if ($user['user_type_fk'] == 184 || ($user['user_type_fk'] == 2 || $user['user_type_fk'] == 6))
                                                        if($addDocumentAppointmentFlag  ==1){ { ?>
                                                        @if($user['user_type_fk'] == 184)
                                                            <a class="btn btn-primary btn-sm d-none d-md-block" style="margin-left:8px;" onclick="loadDocumentAjaxList()"><i class="mdi mdi-refresh"></i>Refresh</a>
                                                        @endif
                                                        <a data-toggle="modal" class="btn btn-info btn-sm d-none d-md-block" style="margin-left:8px;" data-target="#exampleModal-5" data-whatever="@mdo" onclick="viewServices();requestsServices();loadDocumentChooseUser();closeDocumentSection();"><i class="mdi mdi-plus"></i>Add</a>
                                                    <?php } } ?>
                                                </div>
                                            </div>
                                           @if(auth()->user()->agency_fk == "")
                                            <div class="mb-2" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                                <div id="medication_list_counter" style="display:inline-flex;align-items:center;background:#d4edda;color:#155724;padding:5px 12px;border-radius:4px;font-size:12px;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                                    <i class="mdi mdi-pill" style="font-size:14px;margin-right:6px;"></i>
                                                    <span style="margin-right:6px;">Medication List:</span>
                                                    <span id="medication_count" style="background:#28a745;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;min-width:22px;text-align:center;">0</span>
                                                </div>
                                                <div id="insurance_elg_counter" style="display:inline-flex;align-items:center;background:#cfe2ff;color:#084298;padding:5px 12px;border-radius:4px;font-size:12px;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                                    <i class="mdi mdi-shield-check" style="font-size:14px;margin-right:6px;"></i>
                                                    <span style="margin-right:6px;">Insurance Elg:</span>
                                                    <span id="insurance_count" style="background:#0d6efd;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;min-width:22px;text-align:center;">0</span>
                                                </div>
                                                <div id="mdo_tag_counter" style="display:inline-flex;align-items:center;background:#E9D5FF;color:#7C3AED;padding:5px 12px;border-radius:4px;font-size:12px;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                                    <i class="mdi mdi-clipboard-text" style="font-size:14px;margin-right:6px;"></i>
                                                    <span style="margin-right:6px;">MDO Tag:</span>
                                                    <span id="mdo_count" style="background:#7C3AED;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;min-width:22px;text-align:center;">0</span>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-12">

                                                    <div class="loader-main" id="loaderDocument" style="display:flex">
                                                        <div id="dooc" class="table-responsive1">
                                                            <table id="" class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th nowrap>Document Name</th>
                                                                        <th nowrap>Requested Id</th>
                                                                        <th nowrap>Attachment</th>
                                                                        <th nowrap>Attachment Service</th>
                                                                        <th nowrap>Document Completion Date</th>
                                                                        <th nowrap>Created Date/ Created By</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="shimmer-loader">
                                                                    <tr>
                                                                        <td class="line loading-shimmer" colspan="7"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div id="document_response_list"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="reminder-section">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="card-title mb-0">Reminder</p>
                                                <?php if ($user['user_type_fk'] == 184) { ?>
                                                    <p class="mb-0 tx-13">
                                                        <a data-toggle="modal"
                                                            class="pull-right btn btn-info btn-sm  d-none d-md-block"
                                                            data-target="#exampleModal-51" data-whatever="@mdo"
                                                            style="color:#fff"><i class="mdi mdi-plus"></i> Add</a>
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
                                            $notesFlag = 0
                                            @endphp
                                            @if($record->type != "")
                                            @if($record->link_hha_patient != "")
                                            @php
                                            $notesFlag = 1
                                            @endphp

                                            @elseif($record->link_hha_caregiver != "")
                                            @php
                                            $notesFlag = 1
                                            @endphp
                                            @else
                                            @if($record->hha_id != "")
                                            @php
                                            $notesFlag = 1
                                            @endphp
                                            @endif
                                            @endif
                                            @endif
                                            @include('patient._partial.notes_section')
                                        </div>
                                        <div class="tab-pane" id="hha-exchange">

                                            <div class="right-section-main">
                                                <ul class="nav nav-tabs tabs-right sideways right-section-ul">
                                                    @if ($auth->agency_fk == '106')
                                                    @if ($record->hha_id != '' || $record->link_hha_caregiver != '')
                                                    <li class="active"><a href="#hha-caregiver-demographic"
                                                            onclick="getHHADemographic()" data-toggle="tab">Demographic
                                                            Details</a></li>
                                                    <li class=""><a role="tab" href="#hha-calender-section"
                                                            data-toggle="tab" onclick="loadCalender()">Calendar</a>
                                                    </li>
                                                    <li class=""><a role="tab" href="#hha-caregiver-notes"
                                                            onclick="refreshHHA()" data-toggle="tab"> Notes</a></li>
                                                    <li class=""><a role="tab" href="#hha-caregiver-medical"
                                                            onclick="getMedicalalList()" data-toggle="tab">Medical</a>
                                                    </li>
                                                    <li class=""><a role="tab" href="#hha-caregiver-other-compliance"
                                                            onclick="refreshOtherCompliance()" data-toggle="tab">Other
                                                            Compliance</a></li>
                                                    @endif
                                                    @endif
                                                    @if($user['user_type_fk'] == 184)
                                                    @if (isset($agencyDetails->enable_hha) && $agencyDetails->enable_hha == 1)
                                                    @if ($record->hha_id != '' || ($record->link_hha_caregiver != '' && $record->link_hha_caregiver != 0))
                                                    @if ($record->type == 'Caregiver')
                                                    @can('hha-caregiver-demographic')
                                                    <li class="active"><a role="tab" class="active"
                                                            href="#hha-caregiver-demographic"
                                                            onclick="getHHADemographic()" data-toggle="tab">Demographic
                                                            Details</a></li>
                                                    @endcan

                                                    @can('hha-sync-appointment-calendar')
                                                    <li class=""><a role="tab" href="#hha-calender-section"
                                                            data-toggle="tab" onclick="loadCalender()">Calendar</a>
                                                    </li>
                                                    @endcan
                                                    @endif
                                                    @if ($record->type == 'Caregiver')
                                                    @can('hha-caregiver-avaibility')
                                                    <li class=""><a role="tab" href="#hha-caregiver-avaibility"
                                                            onclick="getCargiverAvaibility()"
                                                            data-toggle="tab">Availability</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-calendar-notes')
                                                    <li class=""><a role="tab" href="#hha-caregiver-notes"
                                                            onclick="refreshHHA()" data-toggle="tab">Notes</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-calendar-inservice')
                                                    <li class=""><a role="tab" href="#hha-caregiver-inservice"
                                                            onclick="getInService()" data-toggle="tab">InService</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-calendar-medical')
                                                    <li class=""><a role="tab" href="#hha-caregiver-medical"
                                                            onclick="getMedicalalList()" data-toggle="tab">Medical</a>
                                                    </li>
                                                    <li class=""><a role="tab" href="#hha-caregiver-other-compliance"
                                                            onclick="refreshOtherCompliance()" data-toggle="tab">Other
                                                            Compliance</a></li>
                                                    @endcan
                                                    @can('hha-caregiver-document')
                                                    <li class=""><a role="tab" href="#hha-caregiver-document-section"
                                                            data-toggle="tab"
                                                            onclick="refreshDocumentData()">Document</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-caregiver-preferences')
                                                    <li class=""><a role="tab" href="#hha-caregiver-preferences-section"
                                                            data-toggle="tab"
                                                            onclick="refreshCaregiverPreferencesData()">Preferences</a>
                                                    </li>
                                                    @endcan

                                                    @endif
                                                    @endif
                                                    @endif
                                                    @endif

                                                    @if ($record->link_hha_patient != '' && $record->link_hha_patient != 0)
                                                    @can('hha-patient-demographic')
                                                    <li class="active "><a role="tab" href="#hha-demographic-details"
                                                            onclick="getHHADemographicDetails()"
                                                            data-toggle="tab">Demographic Details</a></li>
                                                    @endcan
                                                    @can('hha-sync-appointment-calendar')
                                                    <li class=""><a role="tab" href="#hha-calender-section"
                                                            data-toggle="tab" onclick="loadCalender()">Calendar</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-get-patient-authorization-info-details')
                                                    <li class=""><a role="tab"
                                                            href="#hha-get-patient-authorization-info-details"
                                                            onclick="GetPatientAuthorizationInfo()"
                                                            data-toggle="tab">Authorization Info</a></li>
                                                    @endcan


                                                    @can('hha-get-patient-notes')
                                                    <li class=""><a role="tab" href="#hha-get-patient-notes"
                                                            onclick="GetPatientNotes()" data-toggle="tab">Notes</a></li>
                                                    @endcan

                                                    @can('hha-get-patient-clinics')
                                                    <li class=""><a role="tab" href="#hha-get-patient-clinics"
                                                            onclick="GetPatientClinics()" data-toggle="tab">Clinical</a>
                                                    </li>
                                                    @endcan

                                                    @can('hha-get-patient-poc-info')
                                                    <li class=""><a role="tab" href="#hha-get-patient-poc-info"
                                                            onclick="GetPatientPOCInfo()" data-toggle="tab">POC Info</a>
                                                    </li>
                                                    @endcan

                                                    @can('hha-get-patient-changes-v2')
                                                    <li class=""><a role="tab"
                                                            href="#hha-get-patient-v2-changes-section"
                                                            onclick="GetPatientChangesV2Info()"
                                                            data-toggle="tab">Changes V2</a></li>
                                                    @endcan

                                                    @can('hha-patient-document')
                                                    <li class=""><a role="tab" href="#hha-patient-document-section"
                                                            data-toggle="tab"
                                                            onclick="refreshPatientDocumentData()">Document</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-patient-contract')
                                                    <li class=""><a role="tab" href="#hha-patient-contract-section"
                                                            data-toggle="tab"
                                                            onclick="refreshPatientContactData()">Contract</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-patient-discipline')
                                                    <li class=""><a role="tab" href="#hha-patient-discipline-section"
                                                            data-toggle="tab"
                                                            onclick="refreshPatientDisciplineData()">Discipline</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-patient-preferences')
                                                    <li class=""><a role="tab" href="#hha-patient-preferences-section"
                                                            data-toggle="tab"
                                                            onclick="refreshPatientPreferencesData()">Preferences</a>
                                                    </li>
                                                    @endcan
                                                    @can('hha-patient-md-order')
                                                        <li class=""><a role="tab" href="#hha-patient-mdo-order-section" data-toggle="tab" onclick="hhaMDOOrderDocument()">MDOrder</a>
                                                        </li>
                                                        @endcan
                                                    @endif
                                                </ul>
                                                @include('patient._partial.hha_module.hha_tab_list')
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="task-health-exchange">
                                            @include('patient._partial.task_health.visit')
                                        </div>

                                        <div class="tab-pane" id="text-messages-section">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="card-title mb-0">Text Message</p>
                                                <div class="pull-right">
                                                    <img src="{{ asset('/ajax-loader.gif') }}" alt="loader"
                                                        id="loadertag122" style="display: none; ">


                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="text-chat-messages" id="text-sms-messages">
                                                        <div id="text-chat-messages-inner" class="text-notes-messages">
                                                        </div>
                                                    </div>
                                                    <div class="chat-message  custom-chat">
                                                        <form id="textMessageSubmits" method="post"
                                                            onsubmit="return false;">
                                                            <input type="hidden" name="_token"
                                                                value="<?php echo csrf_token(); ?>">

                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold" style="font-size: 12px;">Send To:</label>
                                                                <select class="form-control form-control-sm" id="smsSendToNumber" name="send_to_number" style="width: 250px;">
                                                                    @if(!empty($record->mobile))
                                                                        <option value="mobile">Mobile: {{ preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $record->mobile) }}</option>
                                                                    @endif
                                                                    @if(!empty($record->phone))
                                                                        <option value="phone">Phone: {{ preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $record->phone) }}</option>
                                                                    @endif
                                                                    @if(!empty($record->emergency_phone))
                                                                        <option value="emergency">Emergency: {{ $record->emergency_phone }}</option>
                                                                    @endif
                                                                    @if(!empty($record->mobile) && !empty($record->phone))
                                                                        <option value="both">Both (Mobile & Phone)</option>
                                                                    @endif
                                                                </select>
                                                            </div>

                                                            <textarea style="width: 100%; min-height: 80px; max-height: 200px; overflow-y: auto; resize: vertical; margin-bottom: 6px;" name="msg-box" id="smsTextMessage" class="form-control"></textarea>
                                                            <span class="error text-danger d-block mb-1" id="smsTextMessageError"></span>
                                                            <button class="btn btn-success btn-sm" id="text-sms-send-btn" onclick="sendTextMessagefile()">Send</button>
                                                            
                                                            @can('text-message-ai-help-me-write')
                                                             @if(auth()->user()->agency_fk =="")
                                                            
                                                            <button type="button" class="btn-hmw ml-1" data-hmw-context="sms" data-hmw-field="smsTextMessage" onclick="aiHelpMeWrite('sms', 'smsTextMessage', 'textarea')" data-toggle="tooltip" title="Help me write with AI">
                                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="white" style="vertical-align:middle;flex-shrink:0;"><path d="M12 3c-1.2 5.4-5.4 7.8-9 9 3.6 1.2 7.8 3.6 9 9 1.2-5.4 5.4-7.8 9-9-3.6-1.2-7.8-3.6-9-9z"/><path d="M5 3c-.6 2.7-2.3 3.7-4 4 1.7.3 3.4 1.3 4 4 .6-2.7 2.3-3.7 4-4-1.7-.3-3.4-1.3-4-4z" opacity=".8"/></svg>
                                                                <span>Help me write</span>
                                                            </button>
                                                            @endif
                                                            @endcan
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="task-section">

                                            @include('patient/_partial/all_tabs_section/task_section')
                                        </div>

                                        <div class="tab-pane" id="appointment-section">

                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="card-title mb-0">Appointment List</p>

                                                <p class="mb-0 tx-13 d-flex gap-2" style="gap:8px;">
                                                    @can('ai-call-logs')
                                                    @if(auth()->user()->agency_fk == "")
                                                    @if($record->type == 'Caregiver')
                                                    <button class="btn btn-warning btn-sm d-none d-md-block" onclick="openAppointmentAddCallModal()"><i class="mdi mdi-phone-plus"></i> Add AI Call</button>
                                                    @endif
                                                    @endif
                                                    @endcan
                                                    <a href="javascript:void(0);" data-toggle="modal"
                                                        data-target="#exampleModal-4" data-whatever="@mdo"
                                                        class="pull-right btn btn-info btn-sm d-none d-md-block addAppointment"><i
                                                            class="mdi mdi-plus"></i> Add</a>
                                                </p>

                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="loader-main" id="loaderAppointments" style="display:flex">
                                                        <div id="appointments_section_id" class="table-responsive1">
                                                            <table id="" class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:10%">#</th>
                                                                        <th style="width:20%">Name</th>
                                                                        <th style="width:10%">Location</th>

                                                                        <th style="width:20%">Service</th>
                                                                        <th style="width:10%">Date</th>
                                                                        <th style="width:10%">Time</th>
                                                                        <th style="width:10%">Created Date</th>
                                                                        <th style="width:10%">Created By</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="shimmer-loader">
                                                                    <tr>
                                                                        <td class="line loading-shimmer" colspan="8"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div id="appointments_response_list"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="sms-logs-section">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="card-title mb-0">SMS Logs</p>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">

                                                    <div class="col-12 loader-calender" id="logList1"
                                                        style="display:flex;justify-content:center;margin-top:10%">
                                                        <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader"
                                                            id="loadertag121" style="display:none">
                                                    </div>

                                                </div>
                                                <div class="col-12" id="sms_logs_id">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="alaycare">

                                            <div class="right-section-main">
                                                <ul class="nav nav-tabs tabs-right sideways right-section-ul">
                                                @if($record->type == 'Caregiver')
                                                <li class="active"><a href="#alaycare-caregiver-details" onclick="getAlyacareEmployeeDemographic()"
                                                            data-toggle="tab">Demographic Detail</a>
                                                    </li>
                                                @endif
                                                    @if($record->alaycare_id != "")
                                                    <li><a href="#alaycare-calendar" onclick="getAlyacareEmployeeSchedular()"
                                                            data-toggle="tab">Employee Schedule</a>
                                                    </li>
                                                    @if($record->type == 'Caregiver')
                                                    <li><a href="#alaycare-skill" onclick="getAlyacareSkill()"
                                                            data-toggle="tab">Skill</a>
                                                    </li>
                                                    @endif
                                                    @if($record->type == 'Caregiver')
                                                    <li><a href="#alaycare-employee-notes" onclick="getAlyacareEmployeeNotes()"
                                                            data-toggle="tab">Employee Notes</a>
                                                    </li>
                                                    <li><a href="#alaycare-document-attachment" onclick="getAlyacareDocument()"
                                                            data-toggle="tab">Documents / Attachments</a>
                                                    </li>

                                                    @endif

                                                    @endif
                                                </ul>
                                                @if($record->alaycare_id != "")
                                                <div class="tab-content right-section-tab-content" id="alaycare">
                                                @if($record->type == 'Caregiver')
                                    @include('patient._partial.alayacare.employee_demographic')
                                    @endif
                                                    @include('patient._partial.alayacare.employee_scheduler')
                                                    @include('patient._partial.alayacare.skill_qualification')
                                                    @include('patient._partial.alayacare.employee_notes')
                                                    @include('patient._partial.alayacare.document_attachment')
                                                </div>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="tab-pane" id="robort">
                                            <div class="right-section-main">
                                                <ul class="nav nav-tabs tabs-right sideways right-section-ul">
                                                    @if($record->robort_id != "")

                                                            @can('remote-basic-detail')
                                                                <li class="active"><a href="#patient-remote-basic-detail" onclick="getRemoteBasicDetails()"
                                                                        data-toggle="tab">Demographic Details</a>
                                                                </li>
                                                            @endcan
                                                            @can('visit-notes-emr')

                                                                <li><a href="#patient-oru-trn" onclick="getPatientORUTRN()"
                                                                        data-toggle="tab">Patient ORU/TRN</a>
                                                                </li>
                                                            @endcan
                                                            @can('patient-reading-list')
                                                                <li><a href="#patient-reading-list" onclick="getPatientReading()"
                                                                        data-toggle="tab">Reading</a>
                                                                </li>
                                                            @endcan
                                                            @can('patient-medicine-list')
                                                                <li><a href="#patient-medicine-list" onclick="getPatientMedicineList()"
                                                                        data-toggle="tab">Medication List</a>
                                                                </li>
                                                            @endcan
                                                            @can('patient-remote-care-plan')
                                                                <li><a href="#patient-remote-care-plan" onclick="getPatientCarePlan()"
                                                                        data-toggle="tab">Care Plan</a>
                                                                </li>
                                                            @endcan
                                                            @can('patient-remote-activity-log')
                                                                <li><a href="#patient-remote-activity-log" onclick="getPatientActivityLog()"
                                                                        data-toggle="tab">Activity Log</a>
                                                                </li>
                                                            @endcan

                                                    @endif
                                                </ul>
                                                @if($record->robort_id != "" )
                                                <div class="tab-content right-section-tab-content" id="robort">
                                                @can('remote-basic-detail')
                                                                <div class="tab-pane active" id="patient-remote-basic-detail">
                                                                    @include('patient._partial.remote.remote_view_demographic_details')
                                                                </div>
                                                            @endcan
                                                    @can('visit-notes-emr')
                                                        <div class="tab-pane" id="patient-oru-trn">
                                                            @include('patient._partial.remote.patient_oru')
                                                        </div>
                                                    @endcan
                                                    @can('patient-reading-list')
                                                        @include('patient._partial.remote.patient_reading')
                                                    @endcan
                                                    @can('patient-medicine-list')
                                                        @include('patient._partial.remote.patient_medication')
                                                    @endcan
                                                    @can('patient-remote-care-plan')
                                                        <div class="tab-pane" id="patient-remote-care-plan">
                                                            @include('patient._partial.remote.patient_care_plan')
                                                        </div>
                                                    @endcan
                                                    @can('patient-remote-activity-log')
                                                        <div class="tab-pane" id="patient-remote-activity-log">
                                                            @include('patient._partial.remote.patient_activity_log')
                                                        </div>
                                                    @endcan
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="esign-section-new">
                                            @include('patient._partial.esign.esign-new')
                                        </div>
                                        <div class="tab-pane" id="patient-custom-data">
                                            @include('patient._partial.patient.patient_custom_form')
                                        </div>
                                        <div class="tab-pane" id="service-requested-by-patient">
                                            @include('patient._partial.service_requests.service_request_by_patient')
                                        </div>
                                        <div class="tab-pane" id="mq_order_patient">

                                            @include('patient._partial.md_orders.md_order_list')
                                        </div>
                                        @can('payment-log')
                                        <div class="tab-pane" id="payment_section">
                                            @include('patient._partial.payment-log.payment_log_list')
                                        </div>
                                        @endcan
                                        @can('resolution-log')
                                        <div class="tab-pane" id="resolution_log_section">
                                            @include('patient._partial.resolution-log.resolution_log_list')
                                        </div>
                                        @endcan
                                        @if(strtolower($record->type) =='caregiver')
                                            @if ($record->hha_id != '' || ($record->link_hha_caregiver != '' && $record->link_hha_caregiver != 0))
                                                @can('hha-caregiver-i9-requirement')
                                                    <div class="tab-pane" id="hha-caregiver-compliance-i9s-section">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="box info-box card basic-detail-div">
                                                                @include('patient._partial.hha_module.caregiverI9Requirement.hha_caregiver_i9_requirement')
                                                                </div>

                                                            </div>
                                                        </div>

                                                    </div>
                                                @endcan
                                            @endif
                                        @endif

                                        @can('combine-record')
                                            <div class="tab-pane" id="merge_appoint_listing_section">
                                                @include('patient._partial.patient_merge_record.mergeAppointment_list')
                                            </div>
                                        @endcan
                                        <div class="tab-pane" id="visiting-aid-tab">
                                            @include('patient._partial.patient_link_to_third_party.visitingAid_tab')
                                        </div>

                                        @if(!in_array(auth()->user()->id,Common::agencyPortalRolePermission()))
                                        <div class="tab-pane" id="call-details-section">
                                            @include('patient._partial.call_details.call_details_tab')
                                        </div>
                                        @endif

                                        @can('ai-call-logs')
                                         @if(auth()->user()->agency_fk == "")
                                        @if($record->type == 'Caregiver')
                                        <div class="tab-pane" id="ai-call-logs-section">
                                             @include('patient._partial.ai_call.aiCall_tab')
                                        </div>
                                          @endif
                                          @endif
                                          @endcan

                                        <div class="tab-pane" id="patient-linked-files-section">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <p class="card-title mb-0"><i class="fa fa-paperclip mr-1"></i> Linked Files</p>
                                            </div>
                                            <div id="patientLinkedFilesContainer">
                                                <div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
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
                        <div class="col-sm-6">
                            <a href="javascript:void(0);" class="pull-right btn btn-info btn-sm d-none d-md-block" onclick="getAppointmentLogs(1)"><i class="mdi mdi-eye"></i> View Appointment Log</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12" style="justify-content:center;">
                                <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag" style="display: none; ">
                                <div id="logList"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif


        </div>

    </div>
    @include('patient._partial.modal.patient_document.patient_upload_document')
    @include('patient._partial.modal.patient_document.patient_ai_analysis_modal_document')
    @include('patient._partial.link_to_visiting_aid_model')
    @include('patient._partial.modal.patient_next_appointment_date.next_appointment_date_modal')
    @include('patient._partial.modal.patient_add_appointment.patient_add_appointment')

    <!-- Add Call Modal (Appointment Section) -->
    <div class="modal fade" id="appointmentAddCallModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background:#fff8e1;border-bottom:2px solid #ffc107;">
                    <h6 class="modal-title" style="font-weight:700;color:#856404;">
                        <i class="mdi mdi-phone-plus mr-1"></i> Add Call
                    </h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:13px;">
                        <i class="mdi mdi-information-outline mr-1 text-warning"></i>
                        If an active AI call log already exists for this portal, the call will be <strong>re-fired</strong> on it.
                        Otherwise a <strong>new call log</strong> will be created and the call fired immediately.
                    </p>
                    <div id="apptAddCallFeedback" class="mt-3" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <img src="{{ asset('ajax-loader.gif') }}" id="apptAddCallLoader" style="display:none;width:24px;height:24px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning btn-sm" id="apptAddCallSubmitBtn" onclick="submitAppointmentAddCall()">
                        <i class="mdi mdi-phone mr-1"></i> Fire Call Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal-23" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Request for Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data"
                        action="<?php echo URL::to('/patient/appointment-schedule'); ?>" name="adduser" method="post"
                        id="appointmentForm">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                        <?php
                        $locationsIds = [];
                        if (auth()->user()->agency_fk != "") {
                            $locationsIds = ['49', '55'];
                        }
                        ?>
                        <?php if ($record->type == 'Caregiver') { ?>
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Location<span
                                        style="color:red">*</span>:</label>
                                <select name="location_id" class="form-control" id="location_eid"
                                    onchange="getTimeSearchForAgency()">
                                    <option value="">Select Location</option>
                                    <?php foreach ($location_list as $ks) {
                                        if (!in_array($ks->id, $locationsIds)) {
                                    ?>
                                            <option value="<?php echo $ks->id; ?>">
                                                <?php echo $ks->address1; ?>
                                            </option>
                                    <?php        }
                                    } ?>
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
                            <label for="recipient-name" class="col-form-label">Appointment Date <span
                                    style="color:red">*</span>:</label>
                            <input readonly type="text" name="date" class="form-control getappoinmentdate"
                                autocomplete="off" id="date_eid" onchange="getTimeSearchForAgency()" value="">
                            <span id="date_eid_error" class="error mt-2 text-danger" for="document_type"></span>
                        </div>

                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Appointment Time<span
                                    style="color:red">*</span>:</label>
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
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]"
                                id="service_eid">
                                <option value="">Select Service</option>
                                @php $serviceArr = explode(',', $record->service_id);
                                echo "
                                <pre>";
                                    print_R($serviceArr);
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
                                <label for="message-text" class="col-form-label">Location<span
                                        style="color:red">*</span>:</label>
                                <select name="location_id" class="form-control" id="location_eid">
                                    <option value="">Select Location</option>
                                    @if (count($locations) > 0)
                                        @foreach ($locations as $location)
                                            @if(!in_array($location->id, $locationsIds))
                                                <option value="{{$location->id}}">{{$location->location_name}}
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


    @include('patient._partial.modal.patient_telehealth.patient_tele_health_modal')
    @include('patient._partial.modal.patient_telehealth.caregiver_tele_health_modal')
    @include('patient._partial.modal.patient_document.patient_document_model')
    @include('patient._partial.hha_module.otherCompliance.other_compliance_hha_update_modal')
    @include('patient._partial.hha_module.update_hha_document_modal')


    <div class="modal fade commons" id="" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg" style="background-color:transparent">
                <div class="modal-header text-white" style="background-color:#000000 !important;    padding: 8px 16px !important;">
                    <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span>
                        Notes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  p-4" style="background-color:white">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                        <textarea name="document_id" class="form-control" id="notes_id"></textarea>

                        <span id="notes_status_error" class="error"></span>
                    </div>
                    <div id="doc_listing">
                    </div>


                </div>
                <div class="modal-footer border-top-0 bg-light" style="padding:4px 1px !important">
                    <div class="d-flex justify-content-end align-items-center w-100">

                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="commons_flag">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('patient._partial.modal.patient_attachment.attachment_modal')
    @include('patient._partial.modal.patient_completed.patient_completed_modal')

    <div class="modal fade" id="exampleModal-cancel" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="Commsas"
                            style="text-transform:capitalize"></span>Cancel Notes</h5>
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
                            <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?>
                            </option>
                            <?php        }
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
    @include('patient._partial.modal.patient_payment_type.payment_type_modal')
    @include('patient._partial.esign.send_signer_request_modal')
    @include('patient._partial.esign.send_signer_request_modal_new')
    <div class="modal fade" id="exampleModal-67" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="Commsas"
                            style="text-transform:capitalize"></span>Medical Due Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Medical Due Date<span
                                class="error">*</span>:</label>
                        <input type="text" readonly name="due_date" class="form-control" id="due_date_id"
                            data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy"
                            im-insert="false" value="<?php if ($record->due_date != '') {
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
    <div class="modal fade" id="exampleModal-assign" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="" style="text-transform:capitalize"></span>Assign
                        NyBest User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Assign NyBest User<span
                                class="error">*</span>:</label>
                        <select name="assign_nybest_user" class="form-control" id="assign_nybest_user">
                            <option value="">Select Assign NyBest User</option>
                            @if (!empty($assign_user_list[0]))
                                @foreach ($assign_user_list as $val)
                                    <option value="{{ $val->id }}" @if ($val->id == $record->assign_user_id) selected='selected'
                                    @endif>
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

    <div class="modal fade " id="exampleModal-51" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span>
                        Reminder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closed_id">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="reminder_id">
                    @csrf
                    <input type="hidden" name="patient_id" value="<?php echo $record->id; ?>">
                    <div class="modal-body">


                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Email<span
                                    class="error">*</span>:</label>
                            <input type="text" name="email" class="form-control" id="remail" autocomplete="off">
                            <span id="remail_status_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Mobile:</label>
                            <input type="text" name="mobile" class="form-control" id="rmobile"
                                onkeypress="return isNumber(event)" autocomplete="off">
                            <span id="mobile_status_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Notes<span
                                    class="error">*</span>:</label>
                            <textarea name="notes" id="rnotes" class="form-control"></textarea>
                            <span id="rnotes_status_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important;margin-left:-10px">
                            <label class="col-sm-3 col-form-label">Type<span
                                    class="error mt-2 text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="radio" name="rtype" value="EveryDate" onclick="getResponse('EveryDate')">
                                On Date
                                <input type="radio" name="rtype" value="EveryMonth" onclick="getResponse('EveryMonth')">
                                Every Month<br>
                                <span id="rtype_error" class="error"></span>

                            </div>
                        </div>
                        <div class="form-group" id="dates_id" style="display:none">
                            <label class="col-sm-3 col-form-label">Date<span
                                    class="error mt-2 text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="date" id="rdates" class="form-control" autocomplete="off">
                                <span id="rdate_error" class="error"></span>

                            </div>
                        </div>
                        <div class="form-group" id="month_id" style="display:none">
                            <label class="col-sm-3 col-form-label">Month<span
                                    class="error mt-2 text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select name="every_month" class="form-control" id="rmonth"
                                    onchange="getConvertDate(this.value)">
                                    <option value="">Select Month</option>
                                    <option value="1">Every Month</option>
                                    <option value="3">3 Month</option>
                                    <option value="6">6 Month</option>
                                    <option value="12">Every Year</option>

                                </select>
                                <span id="every_month_error" class="error"></span>

                            </div>
                            <p class="mb-0 text-success font-weight-bold test_id append_id" style="margin-left:10px">
                                Tester</p>
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

    @include('patient/_partial/modal/patient_assign/patient_assign_modal')


    <div class="modal fade" id="exampleModal-notes" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Add Caregiver Notes </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post"
                        id="hha_caregivers_notes">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Subject<span
                                    class="error">*</span>:</label>
                            <select class="form-control" id="subjectId" name="subjectId">

                            </select>
                            <span id="hha_subject_id_error" class="error mt-2"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Notes<span
                                    class="error">*</span>:</label>
                            <textarea type="text" rows="4" cols="50" class="form-control"
                                id="hha_caregivers_notes_id"></textarea>
                            <span id="hha_caregivers_notes_id_error" class="error mt-2"
                                for="hha_caregivers_notes_type"></span>
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
    @include('patient._partial.hha_module.hha_link_caregiver_modal')
    @include('patient._partial.modal.patient_merge_record.merge_record_modal')
    @include('patient._partial.modal.patient_inservice_first.patient_inservice_first')

    <div class="modal fade" id="exampleModal-hha-update-patient" tabindex="-1" role="dialog"
        aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Update to HHX Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearData()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action="" name="adduser" method="post"
                        id="update-hha-document-patient">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" id="main_id" value="">
                        <input type="hidden" name="record-id" id="document_recoed_id" value="{{ $record->id }}">
                        <input type="hidden" name="agencyId" id="document_ids" value="{{ $record->agency_id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">HHX Document Type<span
                                            style="color:red">*</span>:</label>
                                    <select name="document_type" class="form-control"
                                        id="hha_patient_document_type_id"></select>
                                    <span id="doc_error" style="color:red" class="error"></span>
                                </div>
                            </div>

                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-success"
                                id="update-hha-document-patient-btn">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal"
                                onclick="clearDataHHA()">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('patient._partial.alayacare.modal.link_alayacare_modal')
    @include('patient._partial.alayacare.modal.save_alayacare_submit_modal')
    @include('patient._partial.modal.inservice_status_modal')
    @include('patient._partial.modal.trainingStatusModal.training_status_modal')
    @include('patient._partial.modal.trainingStatusModal.training_due_date_modal')
    @include('patient/_partial/modal/hama_emergency_phone_modal')
    @include('patient/_partial/modal/hama_emergency_email_modal')
    @include('patient/_partial/modal/patient_document/edit_services_document_modal')

    <div class="modal fade" id="exampleModal-change-task-staus" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Change Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action="{{url('tasks/task-change-status')}}"
                        name="adduser" method="post" id="task_form">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="id" id="edit_id" value="">
                        <input type="hidden" name="recordId" id="recordId" value="{{Request()->id}}">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Status<span
                                    style="color:red">*</span>:</label>
                            <select name="status" class="form-control" id="status_id">
                                <option value="">Select Status</option>
                                <option value="Urgent">Urgent</option>
                                <option value="Outstanding">Outstanding</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                            <span id="task_status_error" class="error mt-2" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Notes:</label>
                            <textarea class="form-control" type="text" class="form-control" name="task_description"
                                placeholder="Enter Task Description" id="task_description" rows="4"
                                cols="50"></textarea>
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
    @include('patient/_partial/modal/patient_follow_date/patient_follow_date_modal')
    @include('patient/_partial/remote/remote_link_modal')

    <input type="hidden" id="record_id" value="{{ $record->id }}">
    <input type="hidden" id="agency_id" value="{{ $record->agency_id }}">

    @include('patient/_partial/inservice_status_two')
    @include('patient/_partial/availability_modal')
    @include('patient/_partial/patient_link_to_third_party/third_party_api_modal')
    @include('patient/_partial/document_send_mail')
    @include('patient/_partial/hha_module/link_hha_patient')
    @include('patient/_partial/service_requests/modal/service_status_change_request_modal')
@include('patient._partial.esign.esign_move_document')
    @include('patient._partial.task.create_task_modal')

    @include('patient/_partial/modal/show_patient_demo_modal')
    @include('patient/_partial/hha_module/poc/create_patient_poc_information')
    @include('patient/_partial/hha_module/caregiverDocument/create_caregiver_document')
    @include('patient/_partial/hha_module/patientDocument/create_patient_document')
    @include('patient._partial.service_requests.modal.add_service_request_modal')
    @include('patient/_partial/modal/patientMobile/patient_mobile_modal')
    @include('patient/_partial/modal/patientPhone/patient_phone_modal')
    @include('patient/_partial/modal/patientLanguage/patient_language_modal')
    @include('patient/_partial/modal/patient_dob/update_patient_dob')
    @include('patient._partial.modal.remote_patient_demographic')
    @include('patient._partial.md_orders.modal.create_md_orders')
    @include('patient._partial.alayacare.modal.link_alayacare_client_modal')
    @include('patient._partial.modal.patient_document.view_document_details_modal')
    @include('patient._partial.modal.payment-log.edit_payment')
    @include('patient._partial.modal.payment-log.add_payment')
    @include('patient._partial.modal.patient_document.e_fax_modal')
    @include('task/model/task_view')
    @include('task/model/task_due_date')
    @include('task/model/task_assignee_modal')
    @include('task/model/task_description_modal')
    @include('task/model/task_title_modal')
    @include('patient._partial.modal.patient_status.refuse_modal_show')
    @include('patient._partial.modal.patient_notes.update_patient_notes_modal')
    @include('patient._partial.modal.referralSourceType.referral_source_type_modal')
    @include('patient._partial.hha_module.otherCompliance.update_other_compliance_modal')
    @include('auditLogReport/log_modal')
    @include('patient._partial.esign.new_view_esign_log_modal')
    @include('patient._partial.hha_module.modal.hha_medical.hha_add_meddical_modal')
    @include('patient._partial.hha_module.caregiverI9Requirement.update_hha_caregiver_i9_requirement_modal')
    <div style="display: none;" id="hidden-team-form">
        @include('patient._partial.modal.patient_resolution_flow')
        @include('patient._partial.modal.pateint_service_status_resolution_flow')
    </div>
    @include('patient._partial.service_requests.modal.edit_service_request')
    @include('patient._partial.modal.patient_document.send_rnpad_document_modal')
    @include('patient._partial.hha_module.hha_mdorder.send_mdorder_hha_modal')
    @include('task/model/task_dept_model')
    @include('patient._partial.modal.assign_department_portal')
    @include('patient._partial.modal.patient_document.send_task_health_document_modal')
    @include('patient._partial.modal.patient_document.send_third_party_modal')
    @include('patient._partial.modal.branch.edit_branch')
    @include('patient._partial.modal.pharmacy.edit_pharmacy')
    @include('patient._partial.patient_link_to_third_party.upload_document_third_party')
    @include('patient._partial.patient_link_to_visiting_aid.search_visiting_aid_patient')
    @include('patient._partial.task_health.link_task_health_patient_modal')
    @include('patient._partial.task_health.task_health_detail_modal')
    @include('patient._partial.modal.resolution_sms_modal')
    {{-- Portal Field Edit Modal --}}
    @if(isset($portalFields) && count($portalFields) > 0)
    <div class="modal fade" id="portalFieldEditModal" tabindex="-1" role="dialog" aria-labelledby="portalFieldModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="portalFieldModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="portalFieldInputLabel" class="col-form-label"></label>
                        <div id="portalFieldInputContainer"></div>
                        <span id="portal_field_error" class="error"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="savePortalFieldBtn">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
    @include('hha_caregiver._partial.hha_caregiver_view_modal')
    @include('hha_patient._partial.hha_view_patient_detail_modal')
    @include('patient._partial.hha_module.poc.poc_view_task_modal')
    {{-- Patient Critical Alerts Resolve Modal --}}
    @include('task_health_critical_alert._partial.resolve_modal')
    @include('patient._partial.modal.agency_rep.edit_agency_rep_modal')
    @include('call_details._partial.modal')
    @include('patient._partial.alayacare.modal.send_to_alayacare')
    @include('include/footer')
    <script>
    function copyToClipboard(text, el) {
        navigator.clipboard.writeText(text).then(function () {
            var icon = el.querySelector('i');
            var orig = icon.className;
            icon.className = 'mdi mdi-check';
            el.style.color = '#28a745';
            setTimeout(function () {
                icon.className = orig;
                el.style.color = '';
            }, 1500);
            toastr.success('Copied successfully', '', {
                positionClass: 'toast-top-right',
                timeOut: 2000,
                closeButton: true,
                progressBar: false,
            });
        });
    }

    var _aiCallLogsLoaded = false;
    function loadPatientAiCallLogs() {
        if (_aiCallLogsLoaded) return;
        _aiCallLogsLoaded = true;
        $.get('{{ url("patient/".$record->id."/ai-call-logs") }}', function(html) {
            $('#aiCallLogsContainer').html(html);
        }).fail(function() {
            $('#aiCallLogsContainer').html('<p class="text-center text-danger py-3">Failed to load AI call logs.</p>');
        });
    }

    var _patientLinkedFilesLoaded = false;
    function loadPatientLinkedFiles() {
        if (_patientLinkedFilesLoaded) return;
        _patientLinkedFilesLoaded = true;
        var patientId = {{ $record->id }};
        $.get('/file-manager/patient/' + patientId + '/files', function (res) {
            if (!res.data || res.data.length === 0) {
                $('#patientLinkedFilesContainer').html('<p class="text-muted text-center py-4">No files linked to this patient.</p>');
                return;
            }
            var html = '<table class="table table-sm table-bordered">'
                + '<thead><tr><th>File Name</th><th>Type</th><th>Size</th><th>Linked On</th><th>Actions</th></tr></thead><tbody>';
            $.each(res.data, function (i, f) {
                html += '<tr>'
                    + '<td>' + $('<div>').text(f.file_name).html() + (f.is_archived ? ' <span class="badge badge-secondary" style="font-size:10px;">Archived</span>' : '') + '</td>'
                    + '<td>' + f.file_type + '</td>'
                    + '<td>' + f.file_size + '</td>'
                    + '<td>' + f.linked_at + '</td>'
                    + '<td style="white-space:nowrap;">'
                    + (f.is_previewable ? '<button class="btn btn-sm btn-outline-info mr-1" title="Preview" onclick="previewLinkedFile(' + f.file_id + ', \'' + f.agency_id + '\')"><i class="fa fa-eye"></i></button>' : '')
                    + '<a href="' + f.download_url + '" class="btn btn-sm btn-outline-success mr-1" title="Download"><i class="fa fa-download"></i></a>'
                    + '<button class="btn btn-sm btn-outline-danger" title="Unlink" onclick="unlinkPatientFile(' + f.file_id + ',' + patientId + ')"><i class="fa fa-unlink"></i></button>'
                    + '</td>'
                    + '</tr>';
            });
            html += '</tbody></table>';
            $('#patientLinkedFilesContainer').html(html);
        }).fail(function () {
            $('#patientLinkedFilesContainer').html('<p class="text-danger text-center py-4">Failed to load linked files.</p>');
        });
    }

    function previewLinkedFile(fileId, agencyId) {
        $.ajax({
            url: '/file-manager/file/preview/' + fileId + '?agency_id=' + encodeURIComponent(agencyId),
            type: 'GET',
            success: function (res) {
                if (res.status && res.data && res.data.url) {
                    window.open(res.data.url, '_blank');
                } else {
                    toastr.error('Preview not available');
                }
            },
            error: function () { toastr.error('Failed to load preview'); }
        });
    }

    function unlinkPatientFile(fileId, patientId) {
        if (!confirm('Unlink this file from the patient?')) return;
        $.ajax({
            url: '/file-manager/file/' + fileId + '/unlink-patient/' + patientId,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                toastr.success(res.message || 'Unlinked successfully');
                _patientLinkedFilesLoaded = false;
                loadPatientLinkedFiles();
            },
            error: function () { toastr.error('Failed to unlink file'); }
        });
    }
    </script>
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
    <script src="{{ asset('assets/js/tribute.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />

    <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css')}}" />
    <div id="logSidebar" class="sidebar">
    <div class="sidebar-header">
        <h5>Payment Log Details</h5>
        <a href="javascript:void(0)" class="close-btn" onclick="closeSidebar()">✖</a>
    </div>
    <div class="sidebar-content" id="logContent">
        <p>Select a row to see details...</p>
    </div>
</div>
<div id="overlay" class="overlay" onclick="closeSidebar()"></div>
    <script src="{{ asset('assets/js/jquery.fancybox.min.js')}}"></script>
    <script>
$(":input").inputmask();

        var agencyFks = "{{ $auth->agency_fk}}";
        if (agencyFks == 106) {
          //  loadCalender();
        }

        function loadCalender() {
            $(document).ready(function () {

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

                    events: function (start, end, timezone, callback) {
                        var startDate = moment(start).format("YYYY-MM-DD");
                        var endDate = moment(end).format("YYYY-MM-DD");
                        $('#loadertag12').attr('style', '');
                        var id = "{{ ($record->link_hha_caregiver != '') ? $record->link_hha_caregiver : $record->hha_id }}";
                        var type = '{{ $record->type}}';
                        var url = '';
                        if (type == 'Caregiver') {
                            url = "{{ url('patient/sync') }}?id=" + id+"&agency_id={{ $record->agency_id}}";
                        } else {
                            var id = "{{ ($record->link_hha_patient != '') ? $record->link_hha_patient : $record->hha_id }}";
                            url = "{{ url('sync-hha-appointment-patient') }}?patientId=" + id+"&agency_id={{ $record->agency_id}}";
                        }
                        if (id != "") {
                            $.ajax({

                                url: url,
                                type: "GET",
                                data: {
                                    start: startDate,
                                    end: endDate,
                                },
                                success: function (res) {
                                    var doc = JSON.parse(res);
                                    $('#loadertag12').attr('style', 'display:none');
                                    callback(doc);

                                }
                            });
                        }
                    },
                    eventRender: function (event, eventElement, eventColor) {
                        eventElement.find(".fc-time").remove();
                        var type = '{{ $record->type}}';
                        if(type =='Patient'){
                            var agencyCId = event.agency_id;
                            var caregiverCid = event.caregiver_id;
                            var caregiverFullName = event.caregiver_full_name;
                            eventElement.find(".fc-title").append(
                                "<br /><b><a class='text-white' href='javascript:void(0)' onclick=\"openCaregiverModal('" + agencyCId + "','" + caregiverCid + "','"+caregiverFullName+"')\">"
                                + event.label +
                                "</a></b>"
                            );
                        }else{
                            var agencyCId = event.agency_id;
                            var caregiverCid = event.patient_id;
                            var caregiverFullName = event.patient_full_name;
                            eventElement.find(".fc-title").append(
                                "<br /><b><a class='text-white' href='javascript:void(0)' onclick=\"openHHAPatientModal('" + agencyCId + "','" + caregiverCid + "','"+caregiverFullName+"')\">"
                                + event.label +
                                "</a></b>"
                            );
                        }
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
        $(document).ready(function () {
            $('ul.left-section-ul li').click(function () {
                $('ul.left-section-ul li').removeClass('active');
                $(this).addClass('active');
            })

            $('ul.right-section-ul li').click(function () {
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
                        "jsonencode": jsonencode,
                        'agency_id':'{{ $record->agency_id}}'
                    },
                    success: function (res) {
                        if (res != '') {
                            htmlsresp = res;
                        } else {
                            htmlsresp = '<option value="">No record available</option>';
                        }
                        $('#service_id').html(htmlsresp);
                        $('#res_service_id').html(htmlsresp);
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
        $('#rdates').datepicker({ buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240" });
        $('#form').submit(function (e) {
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
                    success: function (res) {
                        if (res == 1) { } else {
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
        $('#appointmentForm').submit(function (e) {
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
                    success: function (res) {
                        if (res == 1) { } else {
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

        $('#formnewdocupload').submit(function (e) {

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
            }else{
                var regex = /^[\x00-\x7F]*$/;
                if (!regex.test(notes_id)) {
                    $('#notes_status_error').html("Only English letters are allowed!");
                    return false;
                }
            }

            if(status == 'complete' && $('.docCheckbox').length > 0){
                let selectedAttrs = [];
                if ($('.docCheckbox:checked') && $('.docCheckbox:checked').length === 0) {
                    $('#checkbox_status_error').html("Please check atleast one document!");
                    return false;
                }else{
                    $('.docCheckbox:checked').each(function() {
                        selectedAttrs.push($(this).attr('doc-attr'));
                    });
                }
                $.confirm({
                    title: 'Complete',
                    columnClass: "col-md-6",
                    content: 'Are you sure you want to approve the selected documents?',
                    buttons: {
                        formSubmit: {
                            text: 'Confirm',
                            btnClass: 'btn-success',
                            action: function() {
                                $('#create-change-status-history').removeClass('d-none')
                                $.ajax({

                                    url: "{{ url('/patient/statusUpdate')}}/{{ $record->id}}",
                                    type: "GET",
                                    data: {
                                        status: status,
                                        notes_id: notes_id,
                                        agency_id: '{{ $record->agency_id }}',
                                        'debugMode': '{{ $debugMode}}',
                                        'selectedAttrs' : selectedAttrs,
                                    },
                                    success: function(resp) {
                                        $('#create-change-status-history').addClass('d-none')
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
                        },
                        cancel: function() {
                            //close
                        },
                    },
                });
            }else{
                $('#create-change-status-history').removeClass('d-none')
                $.ajax({

                    url: "{{ url('/patient/statusUpdate')}}/{{ $record->id}}",
                    type: "GET",
                    data: {
                        status: status,
                        notes_id: notes_id,
                        agency_id: '{{ $record->agency_id }}',
                        'debugMode': '{{ $debugMode}}'

                    },
                    success: function (resp) {
                        $('#create-change-status-history').addClass('d-none')
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
            }else{
                var regex = /^[\x00-\x7F]*$/;
                if (!regex.test(notes_id)) {
                    $('#notes_status_cancel_error').html("Only English letters are allowed!");
                    return false;
                }
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
                    success: function (resp) {

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
                    success: function (resp) {
                        var json = JSON.parse(resp);
                        var htmls = '';
                        $('#timeid').html("");
                        if (json.length != 0) {
                            htmls = '<option value="">Select Appointment Time</option>';
                            $.each(json, function (i, v) {
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
                    success: function (resp) {
                        var json = JSON.parse(resp);
                        var htmls = '';
                        $('#time_eid').html("");
                        if (json.length != 0) {
                            htmls = '<option value="">Select Appointment Time</option>';
                            $.each(json, function (i, v) {
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
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var day   = ("0" + date.getDate()).slice(-2);
            var year  = date.getFullYear();
            var formattedDate = day + "-" + month + "-" + year;
            if ($.inArray(formattedDate, properJson) !== -1) {
                return [false, "", "Unavailable"]; // Disable this date
            }
            return [true, ""];
        }
         var newjson =[
            "11-02-2025",
            "12-02-2025",
            "13-02-2025",
            "14-02-2025",
            "15-02-2025",
            "16-02-2025",
            "17-02-2025",
            "18-02-2025",
            "19-02-2025",
            "20-02-2025",
            "21-02-2025",
            "22-02-2025",
            "23-02-2025",
            "24-02-2025",
            "25-02-2025",
            "26-02-2025",
            "27-02-2025",
            "28-02-2025"
        ];
                function marchMinth(date) {

        var addZero = "";
        if(date.getDate() < "10"){
        addZero = 0;
        }else{

        }
        dmy = addZero+""+date.getDate() + "-0"+(date.getMonth() + 1) + "-" + date.getFullYear();

        if ($.inArray(dmy, newjson) == -1) {
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
        $('.getappoinmentdate').datepicker({
            //minDate:1,
            dateFormat: "mm/dd/yy",
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
            minDate:new Date(),
            beforeShowDay: unavailable
        })
        $('#date_id').datepicker({
            //minDate:1,
            dateFormat: "mm/dd/yy",
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
            minDate: new Date(),
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
            $("#notes_id").val("");
            $('#doc_listing').html('');
            $("#notes_status_error").html("");
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
if (val == 'Inactive') {
            $('#' + val).attr('data-target', '#exampleModal-' + val);
        }
            $('#commons_flag').attr('onclick', 'getStatus1("' + val + '")');
            $('#Commsas').html(val);
            $('.commons').attr('id', 'exampleModal-' + val);
            $('.commons').click();

            if(val == 'complete'){
                getDocumentList();
            }else{
                $('#exampleModal-'+ val).children().removeClass('modal-lg');
                $('#exampleModal-complete').children().attr('style','');
            }

        }
        $("#due_date_id").datepicker({
            minDate: new Date(),
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
        });
        $("#telehealth_date_id").datepicker({
            minDate: new Date(),
            buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
            beforeShowDay: unavailable
        });
        $("#patient_telehealth_date_id").datepicker({
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
                    success: function (resp) {

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
                    success: function (res) {
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
                    success: function (res) {
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
                success: function (res) {
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
                    success: function (resp) {

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
                    success: function (resp) {

                        if (resp == 1) {

                            var msg = ' Completed date successfully updated';
                            toastr.success(msg);
                            $('#completed_date_id').html(due_date)
                            $('#comp_id').html(due_date)
                            $('#id_completed_date').html(due_date)
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
                    success: function (resp) {

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
            if (attchmentPdf.length == 0) {
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
                success: function (res) {
                    if (res.status == 1) {
                        toastr.success(res.error_msg);
                        var url = "{{ url('/dpa')}}/<?php echo $record->id;?>";
                $("#attachment_pdf_ids").html(
                    '<a href="' + url + '">Download <i class="fa fa-download"></a>'
                );
                $('#attachment_pdf_ids').next().hide();
                        $('#closeds').click();
                    } else {
                        toastr.error('Sorry, something went wrong. Please try again.');
                    }

                }
            })
        }

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
                    success: function (res) {
                        toastr.success(res.error_msg);
                        $('#payment_type_id').html("");
                        $('#payment_type_id').html(payments_name);
                        $('#basic_payment_type').html(payments_name);
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
                    'patientId': '{{$record->record_id}}'

                },
                success: function (response) {

                    var res = response.data.length;
                    $('#document_hha_id').val(val);

                    var htmlrs = '<option value="">Select Document Type</option>';
                    if (res != 0) {
                        $.each(response.data, function (i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }
                    $('#hha_document_type_id').html('');
                    $('#hha_document_type_id').html(htmlrs);

                }


            })
        }

        $('#send-hha-document-id').click(function (e) {
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
                    success: function (response) {
                        toastr.success(response.error_msg);
                        $('#formnew-hha')[0].reset();
                        $('.close').click()
                    },
                    error: function (xhr, status, error) {
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


        var hhaOtherComplianceCaregiverMedical = [];
        function getOtherMedicalResult(agencyId, val) {
            $("#document_request_complience_id").val(val);
            $.ajax({
                async: false,
                global: false,
                url: "{{ url('hha/hha-other-compliances/hha-other-complience') }}",
                data: {
                    'agencyId': agencyId,
                    'patientId': '{{$record->id}}'

                },
                success: function(response) {

                    var res = response.data.length;
                    // $('#document_hha_id').val(val);
                    var htmlrs = '';
                    hhaOtherComplianceCaregiverMedical = [];
                    if (res != 0) {

                        $.each(response.data, function(i, v) {
                            hhaOtherComplianceCaregiverMedical.push({
                                key: v.caregiver_medical_id,
                                value: v.medical_id
                            });
                            var due_date = "";
                            if(v.due_date !=null){
                                due_date = moment(v.due_date).format('MM/DD/YYYY');
                            }
                            htmlrs += '<option value="' + v.caregiver_medical_id + '">'+ v.medical_name +' - '+v.status+' - '+due_date+'</option>';
                        })
                    }
                    $('#hha_document_complience_id').html('');
                    $('#hha_document_complience_id').html(htmlrs);
                    $('#hha_document_complience_id').select2({
                        placeholder: "Select HHX Compliance Name",
                        allowClear: true
                    });
                }


            })


            $.ajax({
                async: false,
                global: false,
                url: "{{ url('hha-document-type') }}",
                data: {
                    'agencyId': agencyId,

                },
                success: function (response) {

                    var res = response.data.length;
                    //$('#document_hha_id').val(val);
                    var htmlrs = '<option value="">Select Document Type</option>';
                    if (res != 0) {
                        $.each(response.data, function (i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }

                    $('#hha_document_complience_type_id').html('');
                    $('#hha_document_complience_type_id').html(htmlrs);


                }


            })
        }

        var hhaCaregiversMedicalsIds = [];
        function getMedicalResult(agencyId, val) {
            ClearUpdateHHXData();
            $("#document_request_id").val(val);
            $.ajax({

                url: "{{ url('hha-document') }}",
                data: {
                    'agencyId': agencyId,
                    'patientId': '{{$record->id}}'
                },
                success: function (response) {
                    var res = response.data.length;
                    $('#document_hha_id').val(val);

                    var htmlrs = '<option value="">Select Document Type</option>';
                    hhaCaregiversMedicalsIds = [];
                    if (res != 0) {
                        $.each(response.data, function (i, v) {
                            hhaCaregiversMedicalsIds.push({
                                key: v.id,
                                value: v.CaregiverMedicalID
                            });
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }
                    $('#hha_document_medical_id').html('');
                    $('#hha_document_medical_id').html(htmlrs);

                }


            })

            //get result name
            $.ajax({

                url: "{{ url('hha-caregiver-medical-results') }}",
                data: {
                    'agencyId': agencyId,
                    'id': val,
                    'patientId': '{{$record->id}}'

                },
                success: function (response) {


                    var res = response.data.length;
                    $('#document_r_id').val(val);

                    var htmlrs = '<option value="">Select Medical Result</option>';
                    if (res != 0) {
                        $.each(response.data, function (i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }
                    $('#hha_medical_result_id').html('');
                    $('#hha_medical_result_id').html(htmlrs);

                }


            })
            $.ajax({

                url: "{{ url('hha-document-type') }}",
                data: {
                    'agencyId': agencyId,

                },
                success: function (response) {


                    var res = response.data.length;
                    //$('#document_hha_id').val(val);

                    var htmlrs = '<option value="">Select Document Type</option>';
                    if (res != 0) {
                        $.each(response.data, function (i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }

                    $('#hha_document_type_id').html('');
                    $('#hha_document_type_id').html(htmlrs);


                }


            })

            $('#multipleMedicalResultId').html(" ");

            $("#hha_document_medical_id").select2({
                placeholder: "Select Medical",
                allowClear: true
            });

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
            $.each(selectedArray, function (i, k) {
                var findSelected = selectedID.find(o => o == k);
                if (findSelected) {
                    temp.push(k);
                } else {
                    $('#medical_result_' + k).remove();

                }
            })
            selectedArray = temp;
            selectedFlag = false;

            if (selectedArray.length == 0) {
                $('#multipleMedicalResultId').attr('style', 'display:none');
            }

        });
        function GetResultLIst(value) {

            if (selectedFlag) {
                var selectedID = $('.upload-hhax').val();
                var values = value;
                if (selectedArray.length != 0) {
                    $.each(selectedID, function (key, v) {
                        var select = selectedArray.includes(v);

                        if (!select) {
                            selectedArray.push(v);
                            values = v;
                        }
                    })

                } else {
                    selectedArray.push(value)
                }

                var selectedText = '';
                var selectedTextData = $('.upload-hhax').select2("data");

                for (var i = 0; i <= selectedTextData.length - 1; i++) {

                    if (selectedTextData[i].id == values) {
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
                        'medicaid_id': values,
                        'patientId': '{{$record->id}}'

                    },
                    success: function (response) {

                        var res = response.data.length;


                        var htmlrs = '<option value="">Select ' + selectedText + ' Result</option>';
                        if (res != 0) {
                            $.each(response.data, function (i, v) {
                                htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                            })
                        }

                        let getCaregiverMedicalIds = hhaCaregiversMedicalsIds.find(item => item.key === values);
                        var sendCaregiverMedicalID="";
                        if(getCaregiverMedicalIds){
                            sendCaregiverMedicalID = getCaregiverMedicalIds.value
                        }

                        var selectHtml = `<div class="col-md-6"><div class="form-group" id="medical_result_${values}">
                                <label for="recipient-name" class="col-form-label">${selectedText} Results<span style="color:red">*</span>:</label>
                                    <select name="hha_medical_result[${values}]" class="form-control" id="hha_medical_result_id${values}">${htmlrs}</select>
                                    <span id="hha_medical_result_id_${values}_error" style="color:red" class="error"></span>
                            </div></div><input type="hidden" name="caregiver_medicals_item_${values}" value="${sendCaregiverMedicalID}">`;
                        if (selectedArray.length == 1) {
                            $('#multipleMedicalResultId').attr('style', '');
                        }

                        $('#multipleMedicalResultId').append(selectHtml)
                        // $('#hha_medical_result_id').html('');
                        // $('#hha_medical_result_id').html(htmlrs);

                    }


                });
            }


        }


        async function GetComplienceResultList(value) {
            var selectedID = $('.hha_complience_id').val();

            let getCaregiverOtherMedicalIds = hhaOtherComplianceCaregiverMedical.find(item => item.key === value);

            var values = value;
            var sendCaregiverOtherMedicalID=getCaregiverOtherMedicalIds.key;

            if (selectedComplienceArray.length != 0) {
                $.each(selectedID, function(key, v) {
                    var select = selectedComplienceArray.includes(v);

                    if (!select) {
                        selectedComplienceArray.push(v);
                        values = v;
                    }
                })

            } else {
                selectedComplienceArray.push(value)
            }

            var selectedText = '';
            var selectedTextData = $('.hha_complience_id').select2("data");

            for (var i = 0; i <= selectedTextData.length - 1; i++) {

                if (selectedTextData[i].id == values) {
                    selectedText = selectedTextData[i].text;
                }
            }

            var response = await getAllComplienceResultList(values);
            var res = response.data.length;
            var htmlrs = '<option value="">Select ' + selectedText + ' Result</option>';
            if (res != 0) {
                $.each(response.data, function(i, v) {
                    htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                })
            }

            var selectHtml = `<div class="col-md-6"  id="medical_complience_${values}"> <div class="form-group">
                            <label for="recipient-name" class="mb-0">${selectedText} Results<span style="color:red">*</span>:</label>
                                <select name="hha_document_other_compliance_result[${values}]" class="form-control" id="hha_complience_result_id${values}">${htmlrs}</select>
                                <span id="hha_complience_result_id_${values}_error" style="color:red" class="error"></span>
                        </div></div><input type="hidden" name="caregiver_other_compliance_item_${values}" value="${sendCaregiverOtherMedicalID}"><input type="hidden" name="caregiver_other_compliance_id_${values}" value="${getCaregiverOtherMedicalIds.value}">`;
            if (selectedComplienceArray.length == 1) {
                $('#multipleComplienceResultId').attr('style', '');
            }
            $('#multipleComplienceResultId').append(selectHtml)

        }

        $('#update-hha-document-id').click(function (e) {
            $('#loadersId').attr('style', 'display:block');
            $('#update-hha-document-id').attr('disabled', 'disabled');
            var hha_document_result_id = $('.upload-hhax').val();
            var hha_document_type_id = $('#hha_document_type_id').val();
            var completed_date = $('#completed_date').val();
            var create_document_medical_id = $('#create_document_medical_id').val();
            var show_new_medical_need = $('#show_new_medical_need').is(":checked")

            var cnt = 0;
            $('#hha_document_medical_id_error').html("");
            $('#completed_date_error').html("");
            $('#hha_document_type_id_error').html("");
            $('#hha_due_date_div_error').html("");
            $('#pending_hha_document_medical_id_error').html("");
            if (hha_document_type_id.trim() == '') {
                $('#hha_document_type_id_error').html("Please select HHA Document Type")
                cnt = 1;
            }

            if(show_new_medical_need){
                if (hha_document_result_id.length == 0  && create_document_medical_id.length == 0) {
                    $('#hha_document_medical_id_error').html("You must select either HHA Medical or Create HHA Medical to continue")
                    cnt = 1;
                }
            }else{
                if (hha_document_result_id.length == 0) {
                    $('#pending_hha_document_medical_id_error').html("Please select HHA Medical Name")
                    cnt = 1;
                }
            }


            if (hha_document_result_id.length != 0) {
                $.each(hha_document_result_id, function (i, v) {
                    var hha_medical_result_ids = $('#hha_medical_result_id' + v).val();
                    $('#hha_medical_result_id_' + v + '_error').html("");
                    if (hha_medical_result_ids == '') {
                        $('#hha_medical_result_id_' + v + '_error').html("Required");
                        cnt = 1;
                    }
                })
            }

            if(show_new_medical_need){
                if (create_document_medical_id.length != 0) {
                    $.each(create_document_medical_id, function(i, v) {
                        var hha_medical_result_ids = $('#hha_create_medical_result_id' + v).val();
                        $('#create_medical_result_id_' + v + '_error').html("");
                        if (hha_medical_result_ids == '') {
                            $('#create_medical_result_id_' + v + '_error').html("Required");
                            cnt = 1;

                        }
                    })
                }
            }

            if (completed_date.trim() == '') {
                $('#completed_date_error').html("Please select Date Performed")
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
                    success: function (response) {

                        toastr.success(response.error_msg);
                        $('#loadersId').attr('style', 'display:none;');
                        $('#update-hha-document-id').removeAttr('disabled');
                        ClearUpdateHHXData();
                        $('.closeUpdateHHAX').click();
                    },
                    error: function (xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg);
                        $('#loadersId').attr('style', 'display:none;');
                        $('#update-hha-document-id').removeAttr('disabled');
                    }


                })

            }
        })



        $('#update-hha-complience-id1').click(function (e) {
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

            if (hha_document_complience_id.length == 0) {
                $('#hha_document_complience_id_error').html("Required")
                cnt = 1;
            }

            if (selectedComplienceArray.length != 0) {
                $.each(selectedComplienceArray, function (key, v) {
                    var hha_complience_result_id = $('#hha_complience_result_id' + v).val();
                    $('#hha_complience_result_id_' + v + '_error').html("");
                    if (hha_complience_result_id == '') {
                        $('#hha_complience_result_id_' + v + '_error').html("Required");
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
                    success: function (response) {

                        toastr.success(response.error_msg);
                        $('#loadersId').attr('style', 'display:none;');
                        $('#update-hha-complience-id').removeAttr('disabled');
                        hideOtherComplianceToHHXDocument();
                        // $('#formnew-other-compienece-hha-update')[0].reset();
                        // $('.close').click()
                        //    window.location.reload()
                    },
                    error: function (xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg);
                        $('#loadersId').attr('style', 'display:none;');
                        $('#update-hha-complience-id').removeAttr('disabled');
                    }


                })

            }
        })
    </script>
    <script>

        $(document).ready(function () {
            // $('#loadertag').show();
            // getData(1);
        });


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
                        action: function () {
                            window.location.href = url + '/' + id;
                        }
                    },
                    cancel: function () {
                        //close
                    },
                },
            });
        }


        function refresh() {
            var id = "{{ ($record->link_hha_caregiver != '') ? $record->link_hha_caregiver : $record->hha_id }}";
            $.ajax({
                url: "{{ url('patient/sync') }}?id=" + id,
                type: "GET",

                success: function (res) {


                }
            });
            return false;
        }

        $('#hhaCaregiverSave').click(function (e) {
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
                var id = "{{ ($record->link_hha_caregiver != '') ? $record->link_hha_caregiver : $record->hha_id }}";
                var forn = $('#hha_caregivers_notes')[0];
                var formData = new FormData(forn);
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("hha_caregivers_notes", hha_caregivers_notes);
                formData.append("subject_id", subjectId);
                formData.append("id", id);
                formData.append("patient_id", "{{ $record->id}}");
                formData.append("type", "{{ $record->type}}");
                $.ajax({
                    url: "{{ url('hha-caregiver/create-notes') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        toastr.success('Notes successfully added');
                        $('#hha_caregivers_notes')[0].reset();
                        $('#exampleModal-notes').modal('hide');
                        refreshHHA();
                    }, error: function (xhr, status, error) {
                        toastr.error(xhr.responseJSON.message);
                    }
                })
            }
        })

        function getHHACaregiverSubject() {
            var id = "{{ ($record->link_hha_caregiver != '') ? $record->link_hha_caregiver : $record->hha_id }}";
            $.ajax({
                url: "{{ url('hha-caregiver/subject') }}?id=" + id,
                type: "GET",
                success: function (res) {
                    var json = res.data;
                    var option = "";
                    if (json.length != 0) {
                        option = '<option value="">Select Subject</option>';
                        $.each(json, function (i, v) {
                            option += '<option value="' + v.ID + '">' + v.Name + '</option>';
                        })
                    }

                    $('#subjectId').html("");
                    $('#subjectId').html(option);
                    $('#exampleModal-notes').modal('show');
                }

            });
        }

        function refreshMedical() {
            // var id = "{{ ($record->hha_id != '') ? $record->hha_id : $record->link_hha_caregiver }}";
            // $.ajax({
            //     url: "{{ url('hha-caregiver-medical') }}?id=" + id,
            //     type: "GET",
            //     success: function(res) {
            //         toastr.success(res.message)

            //     }

            // });
            getMedicalalList();

        }

        function getMedicalalList() {
            var hha_status_id = $('#hha_status_id').val();
            var id = "{{ ($record->link_hha_caregiver != '') ? $record->link_hha_caregiver : $record->hha_id }}";
            $.ajax({
                url: "{{ url('hha-caregiver-medical-ajax') }}",
                type: "GET",
                data: {
                    status: hha_status_id,
                    id: id,
                    agency_fk:'{{$record->agency_id}}'
                },
                success: function (res) {
                    var json = res.data;
                    var htmlResponse = '';
                    if (res.data.length != 0) {
                        var cnt = 1;
                        $.each(json, function (i, v) {
                            var datePerform = "";
                            if (v.date_perform != "") {
                                datePerform = moment(v.date_perform).format("MM/DD/YYYY");
                            }
                            htmlResponse += '<tr><td>' + cnt + '</td><td>' + v.medical_name + '</td><td>' + v.status + '</td><td>' + moment(v.due_date).format("MM/DD/YYYY") + '</td><td>' + datePerform + '</td><td>' + v.result + '</td></tr>'
                            cnt++;
                        })
                    } else {
                        htmlResponse = '<tr><td colspan="4">' + res.message + '</td></tr>'
                    }
                    $('#tbody_id').html("");
                    $('#tbody_id').html(htmlResponse);
                }

            });
        }

        function refreshOtherCompliance() {
            $('#loadertag1211').attr('style', '');
            var id = "{{ ($record->link_hha_caregiver != '') ? $record->link_hha_caregiver : $record->hha_id }}";
            $.ajax({
                url: "{{ url('hha/hha-other-compliances/get-hha-other-compliance') }}?id=" + id + "&agency_fk={{$record->agency_id}}",
                type: "GET",
                success: function (res) {
                    var json = res.data;
                    var htmlResponse = '';
                    if (json.length != 0) {
                        var cnt = 1;
                        $.each(json, function (i, v) {
                            htmlResponse += '<tr><td>' + cnt++ + '</td><td>'+v.medical_id +'</td><td>' + v.medical_name + '</td><td>' + v.status + '</td><td>' + v.result + '</td><td>' + v.notes + '</td><td>' + formatMomentDate(v.due_date) + '</td><td>' + formatMomentDate(v.date_perform) + '</td><td>' + formatMomentDate(v.modified_date) + '</td></tr>';
                        })
                    } else {
                        htmlResponse = '<tr><td colspan="9">No record available</td></tr>'
                    }
                    $('#loadertag1211').attr('style', 'display:none');
                    $('#tbody_compliance_id').html("");
                    $('#tbody_compliance_id').html(htmlResponse);
                    //

                }

            });
        }

        function formatMomentDate(date) {
            return moment(date).isValid() ? moment(date).format('MM/DD/YYYY') : '';
        }

        function getInService() {
            var id = "{{ ($record->link_hha_caregiver != '') ? $record->link_hha_caregiver : $record->hha_id }}";
            $.ajax({
                url: "{{ url('hha-caregiver-inservice') }}?id=" + id,
                type: "GET",
                success: function (res) {
                    var json = res.data;
                    var htmlResponse = '';
                    $('#caregiver_inservice_id').html("");
                    if (json.length != 0) {
                        var cnt = 1;
                        $.each(json, function (i, v) {
                            htmlResponse += '<tr><td>' + cnt++ + '</td><td>' + v.topic_name + '</td><td>' + v.inservice_date + '</td><td>' + v.from_time + '</td><td>' + v.end_time + '</td><td>' + v.description + '</td></tr>'

                        })


                    } else {
                        htmlResponse = '<tr><td colspan="5">No Record Available</td></tr>'
                    }

                    $('#caregiver_inservice_id').html(htmlResponse);
                    $('#caregiver_inservice_datatable').dataTable().fnDestroy();
                    $('#caregiver_inservice_datatable').dataTable({
                        "bInfo": false,
                        'bSort': false,
                        "pageLength": 10,
                        'searching': false,
                    });
                    $('.dataTables_length').attr('style', 'display:none')
                }

            });
        }

        function linkHHACaregiver() {
            $('#exampleModal-link-hha').modal('show');
        }

        function getHhxProfile() {
            var hha_profile_id = $('#hha_profile_id').val();
            $('.hha_profile_error').html("");
            var cnt = 0;
            if (hha_profile_id == '') {
                $('.hha_profile_error').html("Caregiver Link is required");
                cnt = 1;
            }

            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    type: "post",
                    url: "{{ url('patient/link-to-caregiver') }}",
                    data: {
                        'patient_id': '{{ $record->id}}',
                        'agency_id': '{{ $record->agency_id}}',
                        'hha_profile_id': hha_profile_id,
                        'dataTypeId': $('#dataTypeId').val(),
                        'hha_search_flag': $('#hha_search_flag').val(),
                        '_token': '{{ csrf_token()}}'
                    },
                    success: function (res) {
                        toastr.success(res.message);
                        var fullName = res.data.first_name + ' ' + res.data.last_name + ' ( ' + res.data.caregiver_code + ')';
                        $('#hhx_caregiver_id').html(fullName);
                        $('#lnkhhx_pdf_id')[0].reset();
                        $('#hha_caregiver_ids').val(res.data.caregiver_id);
                        $('#hha_caregiver_names').val(fullName);
                        $('#closedsNew').click();
                        $('#hhx_caregiver_link_id').removeClass('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                })
            }
        }
        $('#exampleModal-link-hha').bind('hide', function () {
            $('#lnkhhx_pdf_id')[0].reset();
            $('.token-input-delete-token').click()
        });


        function loadAllTextMessages() {
            $('.text-notes-messages').html("");
            $('#loadertag1').attr('style', '');


            var agency_id = '<?php echo $record->agency_id; ?>';

            $.ajax({
                url: "<?php echo URL::to('/'); ?>/patient/get-sms-text",
                type: "get",
                data: {

                    'case_id': '{{  $record->id  }}'
                },
                success: function (response) {

                    var response = response.data;
                    response.forEach(element => {
                        var firstName = "";
                        var id = "";
                        if(element.user_details !=null){
                            firstName = element.user_details.first_name;
                            id = element.user_details.id;
                        }
                        add_message_obj_new(element.id, firstName,
                            '', element.message, element
                            .created_date, element.type, id,"",element.case_id,element.message_file,element.file_extension);

                    });
                    setTimeout(() => {
                        $('#loadertag1').attr('style', 'display:none;');
                    }, 3000)

                    // add_message('You', 'img/demo/av1.jpg', input.val(), true);
                    // You will get response from your PHP page (what you echo or print)
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
            return false;
        }

        function add_message_obj_new(mid, name, img, msg, date, type, sender_id, clear,caseId,receiveImage="",fileExtension="") {
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

            var imgReceive="";
            if(receiveImage !=""  && receiveImage !=null){
                var downloadOption ="";
                var canTextImagesDownload = @json(auth()->user()->can('text-images-download'));
                if(canTextImagesDownload){
                    downloadOption ='<a href="'+_TEXT_MESSAGE_IMAGES_DOWNLOAD+'?id='+mid+'"  title="Download">Download</a>'
                }
                var imgReceive="";
                if(receiveImage !="" && receiveImage !=null){
                    var downloadOption ="";
                    var canTextImagesDownload = @json(auth()->user()->can('text-images-download'));
                    if(canTextImagesDownload){

                        downloadOption ='<a href="'+_TEXT_MESSAGE_IMAGES_DOWNLOAD+'?id='+mid+'"  title="Download">Download</a>'
                    }
                    var imageExtension = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'];
                    if(imageExtension.includes(fileExtension)){
                        imgReceive = '<img src="'+receiveImage+'" style="width:100px;height:100px"><br>'+downloadOption
                    }

                    if(['mp3', 'amr', 'ogg'].includes(fileExtension)){
                        imgReceive = '<audio controls ><source   src="'+receiveImage+'" type="audio/'+fileExtension+'">Your browser does not support the audio element.</audio><br>'+downloadOption
                    }
                    if(['mp4', 'webm'].includes(fileExtension)){
                        imgReceive = '<video width="320" height="240" controls> <source src="'+receiveImage+'" type="video/'+fileExtension+'"> Your browser does not support the video tag. </video><br>'+downloadOption
                    }
                    if(['pdf', 'doc','docx','xls','xlsx','ppt','pptx','txt','csv'].includes(fileExtension)){
                        let iconClass = "fa-file";
                        if(fileExtension === "pdf") iconClass = "fa-file-pdf";
                        if(['doc','docx'].includes(fileExtension)) iconClass = "fa-file-word";
                        if(['xls','xlsx','csv'].includes(fileExtension)) iconClass = "fa-file-excel";
                        if(['ppt','pptx'].includes(fileExtension)) iconClass = "fa-file-powerpoint";
                        if(fileExtension === "txt") iconClass = "fa-file-alt";
                        imgReceive = '<a href="javascript:void(0)"><i class="fa '+iconClass+'"></i> Download '+fileExtension.toUpperCase()+'</a><br>'+downloadOption
                    }
                }
            }

            if(caseId != recordId){
                tags =`<span style="margin-left:10px;top: 0;background: #00BBE0;padding: 1px 5px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;">Merge</span>`;
            }
            inner.append('<p id="' + id + '" class="user-' + idname + '">' +
            '<span class="msg-block"><strong>' + name + '</strong>' + tags + '<span class="time"> ' + date +
            ' ' + hours + ':' + minutes + '</span>' +
            '<span class="msg">' + msg + '<br>'+imgReceive+'</span><span class="pull-right">' + ondelete + '</span></span></p>');
            $('#' + id).hide().fadeIn(800);
            if (clear) {
                $('.text-chat-message textarea').val('').focus();
            }
            $('#text-sms-messages').animate({
                scrollTop: inner.height()
            }, 20);
        }

        function sendTextMessagefile() {
            var alldata = new FormData($('#textMessageSubmits')[0]);
            var id = <?php echo $record->id; ?>;
            var name = "you";
            var mobile = '<?php echo $record->mobile; ?>';
            var message = $('#smsTextMessage').val();
            var sendTo = $('#smsSendToNumber').val();

            // Send mobile/phone based on dropdown selection
            if (sendTo === 'mobile') {
                alldata.append('mobile', '{{ $record->mobile }}');
                alldata.append('phone', '');
            } else if (sendTo === 'phone') {
                alldata.append('mobile', '');
                alldata.append('phone', '{{ $record->phone }}');
            } else if (sendTo === 'emergency') {
                alldata.append('mobile', '{{ $record->emergency_phone }}');
                alldata.append('phone', '');
            } else if (sendTo === 'both') {
                alldata.append('mobile', '{{ $record->mobile }}');
                alldata.append('phone', '{{ $record->phone }}');
            } else {
                alldata.append('mobile', '{{ $record->mobile }}');
                alldata.append('phone', '');
            }

            alldata.append('case_id', id);
            alldata.append('message', message);
            alldata.append('_token', '{{   csrf_token()  }}');
            if (id != 0 && message != "") {
                $.ajax({
                    type: 'POST',
                    data: alldata,
                    url: "<?php echo URL::to('/'); ?>/patient/text-message-notes",
                    dataType: "json",
                    mimeType: "multipart/form-data",
                    contentType: false,
                    processData: false,

                    success: function (response) {
                        $('#textMessageSubmits')[0].reset();
                        var response = response.data;
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
                    error: function (jqXHR, textStatus, errorThrown) {
                        toastr.error(jqXHR.responseJSON.error_msg)
                    }
                });
            } else {
                $('#smsTextMessageError').html("Required");
                return false;
            }

        }

        function clearData() {
            $('.error').html("");
            $('#formnew-hha-update')[0].reset();
            $('#multipleMedicalResultId').html("");
        }


        $('.hha_complience_id').on("select2:select", function (e) {

            GetComplienceResultList(e.target.value)

        });

        $('.hha_complience_id').on("select2:unselect", function (e) {
            var selectedID = $('.hha_complience_id').val();
            var temp = [];
            $.each(selectedComplienceArray, function (i, k) {
                var findSelected = selectedID.find(o => o == k);
                if (findSelected) {
                    temp.push(k);
                } else {
                    $('#medical_complience_' + k).remove();
                }
            })
            selectedComplienceArray = temp;


            if (selectedComplienceArray.length == 0) {
                $('#multipleMedicalResultId').attr('style', 'display:none');
            }

        });

        function hideOtherComplianceToHHXDocument() {
            $('#multipleComplienceResultId').html("");
            $('#formnew-other-compienece-hha-update')[0].reset();
        }
        var userid = '{{$user->id}}';


        function inserviceRecord() {
            var inservice_id = $('#inservice_id').val();
            $('#inservice_id_error').html("");
            var cnt = 0;
            if (inservice_id == '') {
                $('#inservice_id_error').html("In Service Date is required");
                cnt = 1;
            }

            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    type: "post",
                    url: "{{ url('patient/inservice-appointment') }}",
                    data: {
                        'record_id': '{{ $record->id}}',
                        'inservice_id': inservice_id,
                        '_token': '{{ csrf_token()}}'
                    },
                    success: function (res) {
                        toastr.success(res.error_msg);
                        $('#inservices_status').html(res.data.inservice_status);
                        $('#inservices_dates').html(res.data.inservice_datetime);

                        hideInServiceAppointment();
                    }
                });
            }
        }

        function hideInServiceAppointment() {
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
                success: function (response) {

                    var res = response.data.length;

                    var htmlrs = '<option value="">Select Document Type</option>';
                    if (res != 0) {
                        $.each(response.data, function (i, v) {
                            htmlrs += '<option value="' + v.id + '">' + v.name + '</option>';
                        })
                    }
                    $('#hha_patient_document_type_id').html('');
                    $('#hha_patient_document_type_id').html(htmlrs);

                }
            })

        }
        $('#update-hha-document-patient-btn').click(function () {

            var HHXDocumentType = $('#hha_patient_document_type_id').val();
            var date = $('#completed_date_patient').val();
            var cnt = 0;


            if (HHXDocumentType == '') {
                $('#doc_error').html("Please Select HHX Document Type");
                cnt = 1;
            }


            if (cnt == 1) {
                return false;
            } else {
                var forms = $('#update-hha-document-patient')[0];
                var newForms = new FormData(forms);
                newForms.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ url('update-hha-document-patient')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        toastr.success(response.error_msg[0]);
                        clearDataHHA();

                    },
                    error: function (xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg[0]);
                    }
                });
            }

        });

        function clearDataHHA() {
            $('.error').html("");
            $('#exampleModal-hha-update-patient').modal('hide');
            $('#update-hha-document-patient')[0].reset();

        }
        function smsLogs(page) {
            var url = '{{ url("sms-logs-list")}}/{{$record->id}}';
            $.ajax({
                url: url
                    + "?page=" + page,
                type: "GET",

                success: function (res) {
                    $('#sms_logs_id').html("");
                    $('#sms_logs_id').html(res);


                }

            });
        }
        $('#alaycare-popup').click(function () {
            $('#lnkhhx_alaycare_id')[0].reset();
            $('.token-input-list').remove()
            $('#hha_alaycare_id').html("");
            $('#hha_alaycare_name').html("");
            $('.token-input-delete-token').click()
            alaycareFunction();
        });

        var empId = '{{ $record->alaycare_id }}';
        var empName = '{{ $record->alaycare_name }}';
        function alaycareFunction() {

            var urlToken = "{{ url('alaycare-emp-data') }}?alaycare_id=" + empId+'&agency_id='+_AGENCYID;

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

        function CloseEmployeePopup() {
            $('.hha_alaycare_id_error').html("");
            $('#lnkhhx_alaycare_id')[0].reset();
            $('.token-input-list').remove();
            $('.token-input-delete-token').click()
        }


        $('#update-alaycare-id').click(function () {
            var alaycareId = $('#hha_alaycare_id').val();
            var name = $('#hha_alaycare_name').val();

            $('.hha_alaycare_id_error').html("");
            var cnt = 0;
            if (alaycareId == '') {
                //$('.hha_alaycare_id_error').html("Please Select Employee");
                //cnt = 1;
            }
            if (cnt == 1) {
                return false;
            } else {

                $.ajax({
                    type: "post",
                    url: "{{ url('patient/update-alaycare-id') }}",
                    data: {
                        'patient_id': '{{ $record->id}}',
                        'alyacare_id': alaycareId,
                        'name': name,
                        '_token': '{{ csrf_token()}}'
                    },
                    success: function (res) {

                        toastr.success(res.error_msg);
                        $('#lnkhhx_alaycare_id')[0].reset();
                        $('#exampleModal-link-alaycare-id').modal('hide');
                        $('.token-input-delete-token').click()
                        $('#hhx_alaycare_id').html('');
                        $('.token-input-list').remove();
                        var fullName = 'N/A';
                    if (res.data[0].alaycare_name != null) {
                        var fullName = res.data[0].alaycare_name + ' (' + res.data[0].alaycare_id + ')';
                    }
                        var patientId = res
                        empId = res.data[0].alaycare_id;
                        empName = res.data[0].alaycare_name;
                        $('#hhx_alaycare_id').html(fullName);



                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                })
            }

        });

    </script>
    <script>
        function alayacareAjax() {
            $('#branchdata').html('');
            $.ajax({
                url: "/get-branch-alaycare-ajax",
                type: "get",

                success: function (response) {

                    $.each(response.data.items, function (index, value) {
                        var optionElement = $('<option>').attr('value', value.id).text(value.name);
                        $('#branchdata').append(optionElement);
                    });

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });

            $('#alayacare-popup').modal('show');
            $('#groupdatadiv').hide();
        }


        $(document).on('change', '#alayacare-popup .modal-body select', function () {
            var selectedValue = $(this).val();
            getGroupbyBranchId(selectedValue);
        });

        function getGroupbyBranchId(branchId) {
            if (branchId) {

                $('#groupdata').html('');
                $.ajax({
                    url: "/get-group-by-branch-id",
                    type: "get",
                    data: {
                        branchId: branchId,
                    },
                    success: function (response) {

                        $.each(response.data.items, function (index, value) {
                            var optionElement = $('<option>').attr('value', value.id).text(value.name);
                            $('#groupdata').append(optionElement);
                        });

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            } else {
                return false;
            }


        }

        function alayacareSubmit() {
            var branchId = $("#branchdata").val();
            var groupId = $("#groupdata").val();
            var patient_id = $('#alaycare-patient-id').val();


            if (branchId === "") {
                $('#branchIderror').html('please select Branch');
                return false;
            } else if (groupId === "") {
                $('#branchIderror').html('');
                $('#groupIderror').html('please select Group');
                return false;
            } else {
                $('#branchIderror').html('');
                $('#groupIderror').html('');
                var newforms = $('#alayacare-form-data').serialize();
                $.ajax({
                    type: "post",
                    url: "{{ url('alayacare-post')}}",

                    data: newforms,
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

        function clearDataModal() {
            $("#alayacare-form-data")[0].reset();
        }

        $('#update-inservice-status').click(function (e) {
            var inservice_status = $("#inservice_status").val();
            var ct = 0;
            $('.inservice_status_error').html("");
            if (inservice_status == '') {
                $('.inservice_status_error').html("Required");
                ct = 1;
            }

            if (ct == 1) {
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    type: "post",
                    url: "{{  url('update-inservice')  }}",

                    data: {
                        '_token': "{{  csrf_token()  }}",
                        'patient_id': "{{  $record->id  }}",
                        'inservice_status': inservice_status
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

        function CloseInserviceStatus() {
            $('.error').html("");
            $('#exampleModal-inservice_status').modal('hide');
        }



        function CloseTrainingStatus() {
            $('#exampleModal-training_status').modal('hide');
        }

        $('#update-training-status').click(function (e) {
            var inservice_status = $("#training_status").val();
            var ct = 0;
            $('.training_status_error').html("");
            if (inservice_status.trim() == '') {
                $('.training_status_error').html("Required");
                ct = 1;
            }

            if (ct == 1) {
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    type: "post",
                    url: "{{  url('update-training')  }}",

                    data: {
                        '_token': "{{  csrf_token()  }}",
                        'patient_id': "{{  $record->id  }}",
                        'training_status': inservice_status
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
                    success: function (resp) {
                        var msg = 'Training Due date successfully updated';
                        toastr.success(msg);
                        location.reload();

                    }

                })
            }


        }

        function getEmergencyPhone() {
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
                    success: function (resp) {
                        $('#emergency_phones').html(emergency_phone)
                        toastr.success(resp.error_msg);
                        clearEmergencyPhone()

                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.error_msg);
                    }

                })
            }
        }

        function clearEmergencyPhone() {
            $('.error').html("")
            $('#exampleModal-emergency_phone').modal('hide');
        }

        function getEmail() {
            var email = $('.email_value').val();
            var regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            var cnt = 0;
            $('#emergency_email_error').html("");

            if (email.trim() == '') {
                $('#emergency_email_error').html("Please enter Email");
                cnt = 1;
            }

            if (email.trim() != '') {
                if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
                    $('#emergency_email_error').html("Invalid email address");
                    cnt = 1;
                }
            }

            if (cnt == 1) {
                return false;
            } else {
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
                    success: function (resp) {
                        $('#emergency_email').html(email)
                        toastr.success(resp.error_msg);
                        clearEmail()

                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.error_msg);
                    }

                })
            }
        }

        function clearEmail() {
            $('.error').html("");
            $('#exampleModal-email').modal('hide');
        }

        function updateDetails(value) {
            var value = $('#training_statuss').html();
            $('#training_status').val(value)
        }

        function updatePhoneDetails(phone) {
            var phone = $('#emergency_phones').html();
            $('#emergency_phone').val(phone)
        }

        function updateEmailDetails(email) {

            var email = $('#emergency_email').html();
            $('#email').val(email)
        }

        function updateTrainingDueDate(date) {
            $('#traning_due_date_id').val(date)
        }
        function getHHXCaregiverDetails() {
            $('.token-input-list').remove();
            var agencyId = '{{ $record->agency_id}}';
            var urlToken = "{{ url('link-to-hha-caregiver') }}?agency_id=" + agencyId;
            var urlTokenCaregiverCode = "{{ url('link-to-hha-caregiver-caregiver') }}?agency_id=" + agencyId;
            var link_hha_caregiver = $('#hha_caregiver_ids').val();
            var hhx_caregiver_name = $('#hha_caregiver_names').val();

            $("#hha_profile_id").tokenInput(urlToken, {

                prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{ id: link_hha_caregiver, name: hhx_caregiver_name }] : [],

                tokenLimit: 1,
                zindex: 9999
            });

            $('#hha_caregiver_first_name').val("")
            $('#hha_caregiver_last_name').val("")
            $('#hha_caregiver_code_id').val("")
            $('#hha_caregiver_phone_no').val("")
            $('#hha_caregiver_ssn').val("")
            $('#hhas_caregiver_id').attr('style','display:none');

        }

        function getDeleteTask(id) {
            $.confirm({
                title: 'Delete',
                columnClass: "col-md-6",
                content: 'Are you delete this task ?',
                buttons: {
                    formSubmit: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function () {
                            $.ajax({
                                type: "POST",
                                url: "{{url('tasks/task-list/')}}/" + id,
                                data: {
                                    '_token': "{{csrf_token()}}",
                                    '_method': "DELETE",
                                    'id': id
                                },
                                success: function (res) {
                                    if (res == 1) {
                                        toastr.success('Task successfully deleted');
                                        getTaskList(1);

                                    } else {
                                        toastr.error('Sorry, something went wrong. Please try again.');

                                    }
                                }
                            })
                        }
                    },
                    cancel: function () {
                        //close
                    },
                },
            });
        }
        $('#hha_document_medical_id').change(function (e) {
            var agencyId = '{{ $record->agency_id}}';
            if (agencyId == 106) {
                var value = $(this).val();
                $('#hha_due_date_div').attr('style', 'display:none');
                var flag = value.includes('80093');
                if (flag || true) {
                    $('#hha_due_date_div').attr('style', '');
                }
            }

        })
        $('#exampleModal-task').on('hidden.bs.modal', function () {
            $('.error').html("");
            $('#task_name_id').val("")
            $('#assign_to_id').val('').trigger("change");
            $('#task_start_date_id').val("");
            $('#due_date').val("");
            $('#priority').val("");
            $('#task_description').val("");
        });

        function getFollowupDate() {
            var follow_date_id = $('#follow_date_id').val();
            var cnt = 0;
            if (follow_date_id == '') {
                $('#follow_date_error').html("Medical Followup date is required");
                cnt = 1;
            }
            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    url: "{{url('patient-followup-date/')}}",
                    data: {
                        '_token': "{{csrf_token()}}",

                        'id': '{{ $record->id}}',
                        'follow_date': follow_date_id
                    },
                    success: function (res) {
                        $('#{{$record->agency_id}}_follow_update').html(follow_date_id);
                        $('#close_follow').click();
                        toastr.success(res.error_msg);
                    },
                    error: function (jqr) {

                        toastr.error(jqr.responseJSON.error_msg);
                    }
                });
            }
        }
        $('#exampleModal-follow_date').on('hidden.bs.modal', function () {
            $('#follow_date_error').html("");
        });
        $('#remote-popup').click(function () {
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

        var tagsArr = [];
        if(agencyFks ==""){
            var tribute = new Tribute({
                menuContainer: document.getElementById("content"),
                values: function (search, cb) {
                    searchUser(search, cb);
                },
                selectTemplate: function (item) {

                    if (typeof item === "undefined") return null;

                    if (this.range.isContentEditable(this.current.element)) {
                        var added = true;

                        $.each(tagsArr, function (index, element) {
                            if (element.id == item.original.id) {
                                added = false;
                                return false;
                            }
                        });

                        if (added) {
                            tagsArr.push({
                                id: item.original.id,
                                name: item.original.email
                            });
                            var html = '<a href="' + item.original.url + '" target="_blank"  id="' + item.original.id + '" title="' + item.original.email + '">@' + item.original.value + "</a>";
                            return '<span contenteditable="false">' + html + '</span>';
                        } else {
                            return '';
                        }
                    } else {
                        return "@" + item.original.value; // Return plain text if not in content editable range
                    }
                }
            });

            tribute.attach(document.getElementById("text-sms-box"));

            document.getElementById("text-sms-box").addEventListener("input", function () {
                updateTags();
            });

            function updateTags() {
                var content = document.getElementById("text-sms-box").innerHTML;
                tagsArr = extractTags(content);
            }

            function extractTags(content) {
                var regex = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/g;
                var matches = [...content.matchAll(regex)];

                var match = matches ? matches.map(match => ({ name: match[0] })) : [];

                return match
            }

            function searchUser(search, cb) {
                $.ajax({
                    url: "{{ url('auto-complete-email') }}",
                    data: {
                        term: search,
                        agency_id: '{{ $record->agency_id}}',
                        type:$('input[name="radio1"]:checked').val()
                    },
                    success: function (data) {
                        var jsonData = data.data;

                        if (jsonData.length !== 0) {
                            var result = jsonData.map(function (v) {
                                var url = "{{ url('user-view')}}/" + v.id;
                                var email = "";
                                if(v.email !=""){
                                    email = ' ( ' + v.email + ' )';
                                }

                                var userType = " ( Nybest User )";
                                if(v.agency_fk !=null){
                                    userType = " ( Agency User )";
                                }
                                return {
                                    id: v.id,
                                    key: v.full_name + userType + email,
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
        }

        function selectedSchedule() {

            $('#date_id').val('');
            $('#location_id').val('');
            $('#timeid').val('');
            $('#date_time_div').html('');
            $('#date_time_count_div').html('');
            setTimeout(() => {
                var response_appoint = <?php echo json_encode($serviceArr); ?>;
                var final = [];
                $.each(response_appoint, function (item, val) {

                    final.push(val);
                })

                $(".new_service_id").val(final).trigger('change');
            }, 1000);

        }

        $(function () {

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
            }, function (chosen_date, end_date) {
                $('#hha_patient_coordinator_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                    'MM/DD/YYYY'));
            })
        });

        $('#update-inservice-status-two').click(function (e) {
            var inservice_status = $("#inservice_status_two").val();
            var ct = 0;
            $('.inservice_status_two_error').html("");
            if (inservice_status == '') {
                $('.inservice_status_two_error').html("Required");
                ct = 1;
            }

            if (ct == 1) {
                return false;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    type: "post",
                    url: "{{  url('update-inservice-two')  }}",

                    data: {
                        '_token': "{{  csrf_token()  }}",
                        'patient_id': "{{  $record->id  }}",
                        'inservice_status': inservice_status
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

        function CloseInserviceStatusTwo() {
            $('.error').html("");
            $('#exampleModal-inservice_status_two').modal('hide');
        }

        function searchCaregiver() {
            var hha_caregiver_code_id = $('#hha_caregiver_code_id').val();
            var hha_caregiver_first_name = $('#hha_caregiver_first_name').val();
            var hha_caregiver_last_name = $('#hha_caregiver_last_name').val();
            var hha_caregiver_phone_no = $('#hha_caregiver_phone_no').val();
            var hha_caregiver_ssn = $('#hha_caregiver_ssn').val();

            $('#hhas_caregiver_id').attr('style', '');
            $('#hhaAppendCIdLoader').removeClass('hide');
            $('#hhaAppendCId').html("")
            if (hha_caregiver_first_name.trim() != '' || hha_caregiver_last_name.trim() != '' || hha_caregiver_code_id.trim() != ''  || hha_caregiver_phone_no.trim() != ''  || hha_caregiver_ssn.trim() != '') {

                $.ajax({
                    type: "get",
                    url: "{{ url('search-hha-caregiver') }}",
                    data: {
                        'hha_caregiver_code_id': hha_caregiver_code_id,
                        'hha_caregiver_first_name': hha_caregiver_first_name,
                        'hha_caregiver_last_name': hha_caregiver_last_name,
                        'hha_caregiver_phone_no': hha_caregiver_phone_no,
                        'hha_caregiver_ssn': hha_caregiver_ssn,
                        'agency_id': '{{ $record->agency_id}}',
                    },
                    success: function(res) {
                        var response = res.data;
                        var tableResponse = "";


                        if (response.length != 0) {
                            var cnt = 1;
                            $.each(response, function(i, v) {
                                var hha_search =0;
                                if(typeof v.hha_search !=undefined){
                                    hha_search =1;
                                }
                                if (!v.caregiver_id) {
                                    tableResponse += `<tr>
                                        <td nowrap>${cnt++}</td>
                                        <td nowrap>${v.id}</td>
                                        <td nowrap>${v.name+'('+v.caregiver_code+')'}</td>
                                        <td nowrap>${v.gender}</td>
                                    <td nowrap>${v.employment_type}</td>
                                        <td nowrap>${(v.status !=null)?v.status:""}</td>
                                        <td nowrap><input type="radio" name="cid" id="hha${v.id}" onclick="selectedCaregiver(${v.id})" data-type="local" value="${v.id}"  data-name="${v.name}" data-code="${v.caregiver_code}" data-search="${hha_search}"></td>
                                    </tr>`;
                                } else {
                                    tableResponse += `<tr>
                                        <td nowrap>${cnt++}</td>
                                        <td>${v.caregiver_id}</td>
                                        <td>${v.first_name+' '+v.last_name +'('+v.caregiver_code+')'}</td>
                                        <td nowrap>${v.gender}</td>
                                    <td nowrap>${v.employment_type}</td>
                                        <td>${v.status}</td>
                                        <td><input type="radio" name="cid"  id="hha${v.caregiver_id}" onclick="selectedCaregiver(${v.caregiver_id})" data-type="hha" value="${v.caregiver_id}" data-name="${v.first_name+' '+v.last_name}" data-code="${v.caregiver_code}" data-search="${hha_search}"></td>
                                    </tr>`;
                                }

                            });


                            $('#hhaAppendCId').html(tableResponse)
                        } else {

                            $('#hhaAppendCId').html('<tr><td colspan="5">No record available</td></tr>')
                        }
                        $('#hhaAppendCIdLoader').addClass('hide');

                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                })
            }else{
                toastr.error('Please fill in at least one field')
                $('#hhas_caregiver_id').attr('style', 'display:none');
                $('#hhaAppendCIdLoader').addClass('hide');
                $('#hhaAppendCId').html("")
            }

        }

        function linkThirdParty() {
            $('.token-input-list').remove();
            var agencyId = '{{ $record->agency_id}}';
            var urlToken = "{{ url('link-to-third-party') }}?agency_id=" + agencyId;

            var link_hha_caregiver = $('#third_party_ids').val();
            var hhx_caregiver_name = $('#third_party_ids_names').val();

            $("#third_party_id").tokenInput(urlToken, {

                prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{ id: link_hha_caregiver, name: hhx_caregiver_name }] : [],

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

        function saveLinkThirdParty() {
            var hha_profile_id = $('#third_party_id').val();
            $('.hha_profile_error').html("");
            var cnt = 0;
            if (hha_profile_id == '') {
                $('.third_party_id_error').html("Third Party Link is required");
                cnt = 1;
            }

            if (cnt == 1) {
                return false;
            } else {
                $.ajax({
                    type: "post",
                    url: "{{ url('patient/save-link-to-third-party') }}",
                    data: {
                        'patient_id': '{{ $record->id}}',
                        'agency_id': '{{ $record->agency_id}}',
                        'hha_profile_id': hha_profile_id,
                        '_token': '{{ csrf_token()}}'
                    },
                    success: function (res) {
                        toastr.success(res.message);
                        var fullName = res.data.first_name + ' ' + res.data.last_name;
                        $('#link_third_party_id').html(fullName);
                        $('#lnkhhx_pdf_id')[0].reset();
                        $('#third_party_ids').val(res.data.id);
                        $('#third_party_ids_names').val(fullName);
                        $('#close_link_third_party').click();
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                })
            }
        }

        function selectedCaregiver(id) {

            var hhx_caregiver_name = $('#hha' + id).attr('data-name')
            var link_hha_caregiver = id;
            $('.token-input-list').remove();
            var urlToken = "{{ url('link-to-hha-caregiver') }}?agency_id={{ $record->agency_id}}";
            $("#hha_profile_id").tokenInput(urlToken, {

                prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{ id: link_hha_caregiver, name: hhx_caregiver_name }] : [],

                tokenLimit: 1,
                zindex: 9999
            });

            $('#dataTypeId').val($('#hha' + id).attr('data-type'));
            $('#hha_search_flag').val($('#hha' + id).attr('data-search'));
        }


        function closeDocumentSection() {
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

        $('#location_id').change(function(i){
            var location_id = $('#location_id').val()

            $('.getappoinmentdate').datepicker('destroy').datepicker({
                        //minDate:1,
                        dateFormat: "mm/dd/yy",
                        buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
                        minDate:new Date(),
                        beforeShowDay: unavailable
                    })

        })

    </script>
    @include('patient/js_parameter_v2')
    <script>
    function openAppointmentAddCallModal() {
        $('#apptAddCallFeedback').hide().html('');
        $('#apptAddCallSubmitBtn').prop('disabled', false).html('<i class="mdi mdi-phone mr-1"></i> Fire Call Now');
        $('#appointmentAddCallModal').modal('show');
    }

    function submitAppointmentAddCall() {
        var btn    = $('#apptAddCallSubmitBtn');
        var mobile = '';

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Processing...');
        $('#apptAddCallLoader').show();
        $('#apptAddCallFeedback').hide();

        $.ajax({
            type: 'POST',
            url:  '{{ url("patient/".$record->id."/ai-call-logs/add-call") }}',
            data: { _token: _CSRF_TOKEN, mobile: mobile },
            success: function(res) {
                $('#apptAddCallLoader').hide();
                var cls  = res.status ? 'alert-success' : 'alert-danger';
                var icon = res.status ? 'fa-check-circle' : 'fa-times-circle';
                var extra = res.is_existing
                    ? ' <small class="d-block mt-1 text-muted">Re-fired on existing log #' + res.log_id + '</small>'
                    : ' <small class="d-block mt-1 text-muted">New log #' + res.log_id + ' created</small>';
                $('#apptAddCallFeedback')
                    .attr('class', 'alert ' + cls + ' py-2 px-3 mt-3')
                    .html('<i class="fa ' + icon + ' mr-1"></i>' + res.message + extra)
                    .show();
                if (res.status) {
                    btn.prop('disabled', true).html('<i class="fa fa-check mr-1"></i> Done');
                } else {
                    btn.prop('disabled', false).html('<i class="mdi mdi-phone mr-1"></i> Fire Call Now');
                }
            },
            error: function(jqXHR) {
                $('#apptAddCallLoader').hide();
                var msg = 'Something went wrong.';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) msg = jqXHR.responseJSON.message;
                $('#apptAddCallFeedback')
                    .attr('class', 'alert alert-danger py-2 px-3 mt-3')
                    .html('<i class="fa fa-times-circle mr-1"></i>' + msg)
                    .show();
                btn.prop('disabled', false).html('<i class="mdi mdi-phone mr-1"></i> Fire Call Now');
            }
        });
    }

    $('#alaycare-client-popup').click(function(){
        $('#lnkhhx_alaycare_client_id')[0].reset();
        $('.token-input-list').remove()
        $('#hha_alaycare_client_id').html("");
        $('#hha_alaycare_client_name').html("");
        $('.token-input-delete-token').click()
        alaycareClientFunction();
    });

var empClientId = $('#alayacare_existing_client_id').val();
var empClientName = $('#alayacare_existing_name').val();

function alaycareClientFunction(){

    var urlToken = _ALAYACARE_CLIENT+"?alaycare_id="+empClientId+'&agency_id='+_AGENCYID;

    $("#hha_alaycare_client_id").tokenInput(urlToken, {

        tokenLimit: 1,
        zindex: 9999,
        prePopulate: empClientId !== "" && empClientName !== "" ? [{ id: empClientId, name: empClientName + ' ( '+ empClientId+' ) ' }] : [],
        onAdd: function (item) {

        var selectedAlaycareId = item.emp_id;
        var name = item.name + '( '+item.emp_id+ ' ) ';
            $('#alayacare_existing_client_id').val(selectedAlaycareId);
            $('#alayacare_existing_name').val(item.name);

        },
        onReady: function() {
            setTimeout(function () {
                $(".token-input-dropdown").css({
                    "max-height": "180px",
                    "overflow-y": "auto"
                });
            }, 500);
        }
    });
}

function CloseClientPopup(){
    $('.hha_alaycare_client_id_error').html("");
    $('#lnkhhx_alaycare_client_id')[0].reset();
    $('.token-input-list').remove();
    $('.token-input-delete-token').click()
}

$('#update-alaycare-client-id').click(function(){
    var alaycareId =  $('#hha_alaycare_client_id').val();
    var name =  $('#alayacare_existing_name').val();

    $('.hha_alaycare_client_id_error').html("");
    var cnt =0;
    if(alaycareId ==''){
        $('.hha_alaycare_client_id_error').html("Please Select Client");
        cnt =1;
    }
    if(cnt ==1){
        return false;
    }else{

        $.ajax({
            type:"post",
            url:_UPDATE_ALAYACARE_CLIENT_NAME,
            data:{
                'patient_id':_RECORD_ID,
                'alyacare_id':alaycareId,
                'name':name,
                'agency_id':_AGENCYID,
                '_token':_CSRF_TOKEN
            },
            success:function(res){

                toastr.success(res.error_msg);
                $('#lnkhhx_alaycare_client_id')[0].reset();
                $('#exampleModal-link-alaycare-client-id').modal('hide');
                $('.token-input-delete-token').click()
                $('#hhx_alaycare_client_id').html('');
                $('.token-input-list').remove();
                var fullName = res.data[0].alaycare_name + ' (' + res.data[0].link_alaycare_client_id + ')';
                var patientId = res
                empClientId = res.data[0].link_alaycare_client_id;
                empClientName = res.data[0].alaycare_name;
                $('#hhx_alaycare_client_id').html(fullName +' ('+alaycareId+')');
            },
            error:function(xhr){
                toastr.error(xhr.responseJSON.message);
            }
        })
    }

});

function sendDocumentArla(serviceRequestedId,documentId,completedDate){
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        content: 'You want to send document for Arla?',
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type:"post",
                        url:_SEND_DOCUMENT_ARLA,
                        data:{
                            'patient_id':_RECORD_ID,
                            'service_requested_id':serviceRequestedId,
                            'document_id':documentId,
                            'agency_id':_AGENCYID,
                            '_token':_CSRF_TOKEN,
			    'completedDate':completedDate

                        },
                        success:function(res){
                            toastr.success(res.error_msg);
                            loadDocumentAjaxList(1)
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg);
                        }
                    });
                }
            },
            cancel: function() {
                //close
            },
        },
    })
}

 function showRefuseModal() {
        $.ajax({
            type: "get",
            url: "{{ url('fetch-refused-status')}}",
            data: {
                'type': '29'
            },
            success: function(res) {
                var json = res.data;
                var optionHtml = "<option value=''>Select Reason</option>";
                if (json.length != 0) {
                    $.each(json, function(i, v) {
                        optionHtml += '<option value="' + v.id + '">' + v.name + '</option>';
                    })
                }
                optionHtml += '<option value="other">Other</option>'
                $('#refuse_reason_ids').html("");
                $('#refuse_reason_ids').html(optionHtml);
                $('#exampleModal-refuse_modal_show').modal('show');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        })
    }

    function getStatusRefuse() {
        var notes_id = "";
        var reason_ids = $('#refuse_reason_ids').val();

        $('#refuse_notes_status_error').html("");
        $('#refuse_reason_id_status_error').html("");
        var cnt = 0;

        if (reason_ids == '') {
            $('#refuse_reason_id_status_error').html("Required");
            cnt = 1;
        }

        if (reason_ids == 'other') {
            notes_id = $('#refuse_notes_id').val();
            if (notes_id.trim() == '') {
                $('#refuse_notes_status_error').html("Required");
                cnt = 1;
            } else {
                var regex = /^[\x00-\x7F]*$/;
                if (!regex.test(notes_id)) {
                    $('#refuse_notes_status_error').html("Only English letters are allowed!");
                    return false;
                }
            }
        }

        if (cnt == 0) {
            $.ajax({
                async: false,
                global: false,
                url: "{{ url('patient/statusUpdate')}}/{{ $record->id}}",
                type: "GET",
                data: {
                    "status": "refused",
                    'notes_id': notes_id,
                    'reason_ids': reason_ids,
                    'agency_id': "{{ $record->agency_id}}",
                },
                success: function(resp) {
                    var msg = ' Appointment successfully refused';
                    toastr.success(msg);
                    location.reload();
                },
                error: function(jqr) {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }

            })
        }

    }

    $('#refuse_reason_ids').change(function(i) {
        var refuse_reason_ids = $('#refuse_reason_ids').val();
        $('#other_refuse_notes').addClass('hide');
        if (refuse_reason_ids == 'other') {
            $('#other_refuse_notes').removeClass('hide');
        }
    })

    function searchAlayaClient(){
        var search_alaya_client = $('#search_alaya_client').val();
        $('#alayacare_client_search_response').addClass('hide');
        if (search_alaya_client.trim() != '') {
            $.ajax({
                type: "get",
                url: "{{ url('search-alayacare-clients')}}",
                data: {
                    'q': search_alaya_client,
                    'agency_id': '{{ $record->agency_id}}',

                },
                success: function(res) {
                    var response = res.data;
                    var tableResponse = "";
                    $('#alayacare_client_search_response').removeClass('hide');
                    $('#alaayaclients_id').html('');
                    if (response.length != 0) {
                        var cnt = 1;
                        $.each(response, function(i, v) {
                            name = v.first_name+" "+v.last_name;
                            tableResponse += `<tr>
                                <td nowrap>${cnt++}</td>
                                <td nowrap>${v.id}</td>
                                <td nowrap>${name+ ' ('+v.external_id+')'}</td>
                                <td nowrap>${(v.status !=null)?v.status:""}</td>
                                <td nowrap><input type="radio" name="search_alayacare_client_id" id="alayacares_client${v.id}" onclick="selectedAlayacareClient(${v.id})" data-type="locals" value="${v.id}"  data-client-name="${name}" data-client-code="${v.external_id}"></td>
                            </tr>`;
                        });
                        $('#alaayaclients_id').html(tableResponse);
                    }else{
                        $('#alaayaclients_id').html('<tr><td colspan="4">No record available</td></tr>')
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message);
                }
            })
        }
    }

    function selectedAlayacareClient(id){
        var hhxs_patients_name = $('#alayacares_client'+id).attr('data-client-name')
        var alayacares_client_id = id;
        hhxs_patients_name = hhxs_patients_name +' ('+id+')';
        $('.token-input-list').remove();
        var urlToken = _ALAYACARE_CLIENT + "?agency_id=" + _AGENCYID;
        $("#hha_alaycare_client_id").tokenInput(urlToken, {

            prePopulate: alayacares_client_id !== "" && hhxs_patients_name !== "" ? [{ id: alayacares_client_id, name: hhxs_patients_name}] : [],

            tokenLimit: 1,
            zindex: 9999
        });

        $('#alayacare_existing_client_id').val(id)
        $('#alayacare_existing_name').val($('#alayacares_client'+id).attr('data-client-name'));

    }

    function searchAlayaEmployee(){
        var search_alaya_client = $('#search_alaya_employee').val();
        $('#alayacare_employee_search_response').addClass('hide');
        if (search_alaya_client.trim() != '') {
            $.ajax({
                type: "get",
                url: "{{ url('search-alayacare-employee')}}",
                data: {
                    'q': search_alaya_client,
                    'agency_id': '{{ $record->agency_id}}',

                },
                success: function(res) {
                    var response = res.data;
                    var tableResponse = "";
                    $('#alayacare_employee_search_response').removeClass('hide');
                    $('#alaayaemployee_id').html('');
                    if (response.length != 0) {
                        var cnt = 1;
                        $.each(response, function(i, v) {
                            name = v.first_name+" "+v.last_name;
                            tableResponse += `<tr>
                                <td nowrap>${cnt++}</td>
                                <td nowrap>${v.id}</td>
                                <td nowrap>${name+ ' ('+v.external_id+')'}</td>
                                <td nowrap>${(v.status !=null)?v.status:""}</td>
                                <td nowrap><input type="radio" name="search_alayacare_employee_id" id="alayacares_employee${v.id}" onclick="selectedAlayacareEmployee(${v.id})" data-type="locals" value="${v.id}"  data-employee-name="${name}" data-employee-code="${v.external_id}"></td>
                            </tr>`;
                        });
                        $('#alaayaemployee_id').html(tableResponse);
                    }else{
                        $('#alaayaemployee_id').html('<tr><td colspan="4">No record available</td></tr>')
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message);
                }
            })
        }
    }

    function selectedAlayacareEmployee(id){
        var hhxs_patients_name = $('#alayacares_employee'+id).attr('data-employee-name')
        var alayacares_client_id = id;
        hhxs_patients_name = hhxs_patients_name +' ('+id+')';
        $('.token-input-list').remove();
        var urlToken = "{{ url('alaycare-emp-data') }}?agency_id=" + _AGENCYID;
        $("#hha_alaycare_id").tokenInput(urlToken, {

            prePopulate: alayacares_client_id !== "" && hhxs_patients_name !== "" ? [{ id: alayacares_client_id, name: hhxs_patients_name}] : [],

            tokenLimit: 1,
            zindex: 9999
        });

        $('#hha_alaycare_id').val(id)
        $('#hha_alaycare_name').val($('#alayacares_employee'+id).attr('data-employee-name'));

    }

    $(document).on('click', '.sms-log-pegination .pagination a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];
        smsLogs(page);
    });

    $(document).on("change", ".unachived", function() {
        $.confirm({
            title: 'Are you sure?',
            content: 'You want to unarchive this record?',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-blue',
                        action: function () {
                            var selected_data = [];
                            selected_data.push('{{ $record->id}}');
                            $.ajax({
                                async: false,
                                global: false,
                                type: "POST",
                                url: "{{ url('patient/patient-unarchive')}}",
                                data: {
                                    '_token': "{{ csrf_token()}}",
                                    'patient_id': selected_data.join()
                                },
                                success: function(res) {
                                    toastr.success('Appointment successfully unarchive.');
                                    $('#unrchived_div').addClass('hide');

                                },
                                error:function(jqr){
                                    toastr.error('Sorry, something went wrong. Please try again.');
                                }
                            });
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        action: function () {
                            $('.unachived').prop('checked', true);
                        }
                    }
                }
            });
    });

    $('#create_document_medical_id').on("select2:select", function(e) {
        let selectedValues = $('#create_document_medical_id').val();
        let lastSelected = selectedValues[selectedValues.length - 1];
        selectedCreateUploadHHAXFlag = true;
        GetCreateMedicalResultList(lastSelected);
    });


    $('.create-upload-hhax').on("select2:unselect", function (e) {

        var selectedID = $(this).val();
        var temp = [];

        $.each(selectedCreateUploadHHAX, function (i, k) {
            var findSelected = selectedID.find(o => o == k);
            if (findSelected) {
                temp.push(k);
            } else {
                $('#create_medical_result_' + k).remove();

            }
        })
        selectedCreateUploadHHAX = temp;
        selectedCreateUploadHHAXFlag = false;

        if (selectedCreateUploadHHAX.length == 0) {
            $('#createMultipleMedicalResultId').attr('style', 'display:none');
        }

    });

    $('#show_new_medical_need').click(function(e){
        var checked = $('#show_new_medical_need').is(":checked");
        $('#create_new_medical_need').addClass('hide');
        $('#createMultipleMedicalResultId').html("");
        $('#create_document_medical_id').val('').trigger("change")
        if(checked){
            $('#create_new_medical_need').removeClass('hide');
            getAllMedicalList();
        }
    });

    function ClearUpdateHHXData(){
        $('#formnew-hha-update')[0].reset();
        $('#create_new_medical_need').addClass('hide');
        $('#createMultipleMedicalResultId').html("");
        $('#create_document_medical_id').val('').trigger("change")
        $('.error').html("");
    }

    $('#show_new_other_compliance_need').click(function(e){

        var checked = $('#show_new_other_compliance_need').is(":checked");

        $('#create_new_other_compliance_need').addClass('hide');
        $('#createMultipleOtherComplianceResultId').html("");
        $('#create_document_medical_id').val('').trigger("change")
       let selectedCreateUploadHHAXOtherFlag = false;
        if(checked){
            $('#create_new_other_compliance_need').removeClass('hide');
            getAllOtherComplianceListList();
        }
    })

    $('#create_document_other_type').on("select2:select", function(e) {

        let selectedValues = $('.create_document_other_type').val();
        let lastSelected = selectedValues[selectedValues.length - 1];
        selectedCreateUploadHHAXOtherFlag = true;
        GetCreateOtherComplianceResultList(lastSelected);
    })

    $('#create_document_other_type').on("select2:unselect", function (e) {

        var selectedID = $(this).val();
        var temp = [];

        $.each(selectedCreateUploadHHAXOther, function (i, k) {
            var findSelected = selectedID.find(o => o == k);
            if (findSelected) {
                temp.push(k);
            } else {
                $('#create_other_compliance_result_' + k).remove();

            }
        })
        selectedCreateUploadHHAXOther = temp;
        selectedCreateUploadHHAXOtherFlag = false;

        if (selectedCreateUploadHHAXOther.length == 0) {
            $('#createMultipleOtherComplianceResultId').attr('style', 'display:none');
        }

    });

    </script>
