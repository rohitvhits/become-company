@include('include/header')
@include('include/sidebar')
<?php
use Illuminate\Support\Facades\URL;
?>

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<style>
    .agency-note-alert { position:relative; padding:0.5rem 1rem; margin-bottom:0.5rem; border:1px solid transparent; border-radius:4px; }
    .agency-note-alert.alert-danger  { color:#721c24; background-color:#f8d7da; border-color:#f5c6cb; }
    .agency-note-alert.alert-warning { color:#856404; background-color:#fff3cd; border-color:#ffeeba; }
    .agency-note-alert.alert-info    { color:#0c5460; background-color:#d1ecf1; border-color:#bee5eb; }
    dl {
        margin-top: 0;
        margin-bottom: 20px;
    }

    ul,
    ol,
    dl {
        padding-left: 0px !important;
    }

    .dl-horizontal dt {
        float: left;
        width: 87px;
        clear: left;
        text-align: right;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    h6.fm_1 {
        font-size: 14px;
    }

    dt {
        font-weight: 700;
    }

    .dl-horizontal dd {
        margin-left: 115px;
    }

    .ml-3,
    .rtl .settings-panel .sidebar-bg-options .rounded-circle,
    .rtl .settings-panel .sidebar-bg-options .color-tiles .tiles,
    .rtl .settings-panel .color-tiles .sidebar-bg-options .tiles,
    .mx-3 {
        margin-left: 1rem !important;
        width: 100%;
    }

    #hr2 .dl-horizontal dd {
        margin-left: 130px;
    }

    #hr2 .dl-horizontal dt {
        width: 101px;
    }

    .label {
        display: inline;
        padding: .2em .6em .3em;
        font-size: 100%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }

    .label-success {
        background-color: #5cb85c;
    }

    .label-danger {
        background-color: #d9534f;
    }

    .label-warning {
        background-color: #f0ad4e;
    }

    .label-default {
        background-color: #777;
    }

    .custom-toggle-switch .switch {
        position: relative;
        display: inline-block;
        width: 53px;
        height: 28px;
    }

    .custom-toggle-switch .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .custom-toggle-switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .custom-toggle-switch .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .custom-toggle-switch input:checked+.slider {
        background-color: #2196F3;
    }

    .custom-toggle-switch input:focus+.slider {
        -webkit-box-shadow: 0 0 1px #2196F3;
        box-shadow: 0 0 1px #2196F3;
    }

    .custom-toggle-switch input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        transform: translateX(26px);
    }

    .custom-toggle-switch .slider.round {
        border-radius: 34px;
    }

    .custom-toggle-switch .slider.round:before {
        border-radius: 50%;
    }

    .two-factor-toggle {
        width: max-content !important;
    }

    .agency-detail1 dt {
        width: auto;
        text-align: left;
    }

    .agency-detail1 dd {
        margin-left: 127px;
    }

    .custom-wrapper {
        min-height: auto;
    }

    .error {
        color: Red;
    }

    .action-btns {
        padding-left: 10px !important;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 5px;
        text-align: left;
    }

    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }

    .loader {
        width: 25px;
    }

    .selection .select2-selection {
        /* height: 40px; */
    }

    .modal-title {
        font-weight: bold;
    }

    .close {
        background: none;
        border: none;
        font-size: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-check {
        margin-bottom: 0.5rem;
    }

    .form-check-input {
        margin-top: 0.3rem;
    }

    .form-check-label {
        margin-left: 1.25rem;
    }

    .hide {
        display: none;
    }

    .loading-shimmer {
        animation: shimmer 2s infinite linear;
        background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
        background-size: 1000px 100%;
    }

    th {
        text-align: left;
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }

        100% {
            background-position: 1000px 0;
        }
    }

    .circle {
        height: 70px;
        width: 70px;
        border-radius: 35px;
    }

    .line {
        height: 20px;
        width: 300px;
    }

    .select2-container {
        z-index: 99999 !important;
    }
    .highlightError {
        outline: 1px solid red;
}

    /* NyBest user chips */
    .nybest-user-list { margin-top: 6px; }
    .nybest-chip {
        display: inline-block;
        background: #eef5ff;
        color: #2a5bd7;
        border: 1px solid #6383bc;
        border-radius: 5px;
        padding: 3px 10px;
        margin: 2px 6px 2px 0;
        font-size: 12px;
        line-height: 1.6;
        white-space: nowrap;
    }

    #agency_date_type .selection .select2-selection {
        height: 100% !important
    }
</style>
<!--main-container-part-->

<div class="main-panel">
    <div class="content-wrapper custom-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">Agency # <?= $agencyDetails->id . " - " . ucwords($agencyDetails->agency_name) . " " ?> </h4>

                </div>

                <div class="d-md-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                    </div>
                </div>
            </div>

        </div>

        <div id="msgs"></div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h4>Agency Details</h4>
                            </div>
                            <div class="col-sm-9">
                                @can('agency-delete')
                                <a href="javascript:void(0);" class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1" onclick="openDeleteAgencyModal('{{$id}}')" title="Delete"><i class="mdi mdi-delete"></i>Delete</a>
                                @endcan

                                @can('agency-edit')
                                <a href="<?php echo URL::asset("/"); ?>agency/edit/<?= $id ?>" class="btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1" title="Edit"><i class="mdi mdi-pencil"></i>Edit</a>
                                @endcan

                                @can('agency-notes-list')
                                <a href="javascript:void(0);" class="pull-right btn btn-warning btn-rounded btn-sm ml-1" onclick="$('#agency-notes-tab').tab('show');loadAgencyNotes();" title="Agency Notes"><i class="mdi mdi-note-text"></i> Agency Notes</a>
                                @endcan
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="profile-feed">
                                    <div class="row">
                                        <div class="col-md-4">

                                            <input type="hidden" id="is_sms_status" value="{{ $agencyDetails->is_sms}}">
                                            <dl class="dl-horizontal agency-detail1">
                                                <dt> Agency Name</dt>
                                                <dd> <?= ($agencyDetails->agency_name != '') ? ucwords($agencyDetails->agency_name) : '-'; ?> </dd>

                                                <dt> Email</dt>
                                                <dd> <?= ($agencyDetails->email != '') ? ($agencyDetails->email) : '-'; ?> </dd>
                                                <dt> Phone</dt>
                                                <dd> <?= ($agencyDetails->phone != '') ? $agencyDetails->phone : '-'; ?> </dd>
                                                <dt> Address1</dt>
                                                <dd> <?= ($agencyDetails->address1 != '') ? ($agencyDetails->address1) : '-'; ?> </dd>


                                                <dt> State</dt>
                                                <dd> <?= ($agencyDetails->state != '') ? ($agencyDetails->state) : '-'; ?> </dd>
                                                <dt> City</dt>
                                                <dd> <?= ($agencyDetails->city != '') ? $agencyDetails->city : '-'; ?> </dd>
                                                <dt> Zip Code</dt>
                                                <dd> <?= ($agencyDetails->zip_code != '') ? $agencyDetails->zip_code : '-'; ?> </dd>
                                                <dt>Client Name</dt>
                                                <dd>
                                                    <?= ($agencyDetails->client_name != '') ? $agencyDetails->client_name : '-'; ?>
                                                </dd>
                                                <dt>Total Users</dt>
                                                <dd>
                                                   {{ $totalActive + $totalBlock + $totalInactive}}
                                                </dd>
                                                <dt>Total Active Users</dt>
                                                <dd>
                                                   {{ $totalActive}}
                                                </dd>
                                                <dt>Total Block Users</dt>
                                                <dd>
                                                   {{  $totalBlock }}
                                                </dd>
                                                <dt>Total Inactive Users</dt>
                                                <dd>
                                                   {{ $totalInactive}}
                                                </dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-4">

                                            <dl class="dl-horizontal agency-detail1">
                                                <dt>Notification Email  <br />For NYBEST Users</dt>
                                                <dd><?= ($agencyDetails->notification_email != '') ? str_replace(',', '<br>', $agencyDetails->notification_email) : '-'; ?></dd>

                                                <dt>Agency Notification Email <br />For document and status update</dt>
                                                <dd><?= ($agencyDetails->nybest_email_notification != '') ? str_replace(',', '<br>', $agencyDetails->nybest_email_notification) : '-'; ?></dd>

                                                <dt>Sent SMS</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="is_sms" class="smsEnableDisabled" {{ $agencyDetails->is_sms!= 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>

                                                <dt>Portal Sent SMS</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="is_portal_sms" class="portalSmsEnableDisabled" {{ $agencyDetails->is_portal_sms!= 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>

                                                <dt>View Payment Type Report</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="view_payment_report" id="view_payment_report" class="paymentReportEnableDisabled" {{ $agencyDetails->view_payment_report!= 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>

                                                <dt>Document Email</dt>
                                                <dd>
                                                    <input type="hidden" id="edit_document_send_email_id" value="{{ $agencyDetails->document_email_notification}}">
                                                   <span id="document_send_email_id" class="mr-3">{{ $agencyDetails->document_email_notification}}</span><a title="Edit Document Email" data-toggle="modal" onclick="editEmailDocument()" data-target="#addEditDocumentModal"><i class="fa fa-edit"></i></a>
                                                </dd>
                                                <dt>Efax No</dt>
                                                <dd>
                                                <input type="hidden" id="edit_efax_no_id" value="{{ $agencyDetails->efax_no}}">
                                                <span id="efax_no_id" class="mr-3">{{ $agencyDetails->efax_no}}</span><a title="Edit Efaxno" data-toggle="modal" onclick="editEFaxNo()" data-target="#addEditEfaxModal"><i class="fa fa-edit"></i></a>
                                                </dd>

                                                <dt>Enable Task health</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="enable_task_health" id="enable_task_health" class="enableTaskHealthEnableDisabled" {{ $agencyDetails->enable_task_health != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>

                                                <dt>Enable File Manager</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="enable_file_manager" id="enable_file_manager" class="enableFileManagerEnableDisabled" {{ $agencyDetails->enable_file_manager != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                <dt>Enable Portal Archive</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="enable_portal_archive" id="enable_portal_archive" class="enablePortalEnableDisabled" {{ $agencyDetails->enable_portal_archive != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                <dt>Enable Review</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="enable_review" id="enable_review" class="enableReviewToggle" {{ $agencyDetails->enable_review != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>

                                                <dt>Patient Portal Send SMS</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="is_telehealth_send_sms" id="is_telehealth_send_sms" class="isTelehealthSendSmsToggle" {{ $agencyDetails->is_telehealth_send_sms != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                <dt>Show Reporting Tool</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="show_reporting_tool" id="show_reporting_tool" class="reportingToolEnableDisabled" {{ $agencyDetails->show_reporting_tool != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                            </dl>

                                        </div>

                                        <div class="col-md-4">
                                        @can('edit-agency-logo')
                                            <dt> Agency Logo</dt>
                                            <dd>
                                                <form id="agency-logo-form" enctype="multipart/form-data">

                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                                                    <div id="logo-container">
                                                        @if($agencyDetails->agency_logo !="")
                                                        <img id="agency-logo" src="{{ url('/download-agency-images') }}?id={{$id}}" style="height: 76px;width: 145px;border-radius: 5px;" alt="Logo">

                                                        @else
                                                        @php
                                                        $logo='default.png';
                                                        @endphp
                                                        <img id="agency-logo" src="{{ asset('allupload/' . $logo) }}" style="height: 76px;width: 145px;border-radius: 5px;" alt="Logo">
                                                        @endif

                                                    </div>
                                                    <input type="file" name="agency-image" id="image-upload" style="display:none;">
                                                    <span id="image-error" style="color:red"></span>
                                                </form>
                                            <dd>
                                            @endcan
                                            <dl class="dl-horizontal agency-detail1 pt-3">
                                                <dt>Created Date</dt>
                                                <dd>
                                                    <span class="mr-3">{{ date('m/d/Y H:i A',strtotime($agencyDetails->created_at))}}</span>
                                                </dd>
                                                <dt>Created By</dt>
                                                <dd>
                                                    <span class="mr-3">
                                                    @if(isset( $createdUser->first_name))
                                                    {{  $createdUser->first_name}} {{ $createdUser->last_name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                    </span>
                                                </dd>

                                                <dt>Last Updated Date</dt>
                                                <dd>
                                                    <span class="mr-3">{{ date('m/d/Y H:i A',strtotime($agencyDetails->updated_at))}}</span>
                                                </dd>
                                                <dt>Last Updated By</dt>
                                                <dd>
                                                    <span class="mr-3">
                                                    @if(isset( $updatedUser->first_name))
                                                    {{  $updatedUser->first_name}} {{ $updatedUser->last_name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                   </span>
                                                </dd>

                                                <dt> Assign Liaison </dt>
                                                <dd>
                                                    <div id="nybest_edit_user_info" class="nybest-user-list">
                                                        @if(count($nybestUserData) > 0)
                                                            @foreach($nybestUserData as $index => $nydata)
                                                                @if(isset($nydata['first_name']) && !empty($nydata['first_name']))
                                                                    <span class="nybest-chip">{{ $nydata['first_name'] }} {{ $nydata['last_name'] }} ({{$nydata['email']}})</span>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <a class="ml-1" data-toggle="modal" data-target="#nybest-user-modal" data-whatever="@mdo" title="Assign Nybest User"><i class="fa fa-edit" style="font-size: 20px;"></i></a>
                                                </dd>

                                                <dt>Restrict Service Request Update <i class="fa fa-info-circle" title="When Agency users add new service requests, existing Service Requests and the portal status will not be updated." style="cursor:pointer;color:#6c757d;"></i></dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success" style="margin-left: 15px;">
                                                        <input type="checkbox" name="restrict_service_request_update" id="restrict_service_request_update" class="restrictServiceRequestUpdate" {{ $agencyDetails->restrict_service_request_update != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>

                                                <dt>AI Call Logs</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success" style="margin-left: 15px;">
                                                        <input type="checkbox" name="ai_call_logs_enabled" id="ai_call_logs_enabled" class="aiCallLogsToggle" {{ $agencyDetails->ai_call_logs_enabled != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                            </dl>
                                        </div>


                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="profile-feed">
                                    <div class="d-flex align-items-start" style="border-bottom:0px !important">


                                        <?php foreach ($agencyCount as $vals) { ?>
                                            <div class="ml-3">
                                                <dl class="dl-horizontal">
                                                    <dt> <?php echo $vals->name; ?></dt>
                                                    <dd><?php echo $vals->total; ?></dd>
                                                </dl>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="content-wrapper custom-wrapper">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">

                    <li class="nav-item">
                        <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users-1" role="tab" aria-controls="users-1" aria-selected="false" onclick="loadUserList(1)">Users List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="agency-tab" data-toggle="tab" href="#agency-1" role="tab" aria-controls="agency-1" aria-selected="true" onclick="getData(1);">Agency Logs</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="token-tab" data-toggle="tab" href="#token-1" role="tab" aria-controls="token-1" token-selected="false" onclick="getAllGenerateToken()">Generate Token</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="domain-tab" data-toggle="tab" href="#domain-1" role="tab" aria-controls="domain-1" aria-selected="false" onclick="domainList(1);">Domain List</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="hha-detail-tab" data-toggle="tab" href="#hha-detail-1" role="tab" aria-controls="hha-detail-1" aria-selected="false">HHA Detail</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="notification-email-tab" data-toggle="tab" href="#notification-email-1" role="tab" aria-controls="notification-email-1" aria-selected="false" onClick="notificationEmailList(1)">Notification Email</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="agency_wise_sms_list-tab" data-toggle="tab" href="#agency-wise-sms-list-1" role="tab" aria-controls="agency-wise-sms-list-1" aria-selected="false">SMS Template & Setting</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="agency_wise_sms_service-tab" data-toggle="tab" href="#agency-wise-service-1" onclick="getService(1)" role="tab" aria-controls="agency-wise-service-1" aria-selected="false">Agency Wise Service</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="alayacare-tab" data-toggle="tab" href="#alayacare-1" onclick="getAllSkills(1)" role="tab" aria-controls="alayacare-1" aria-selected="false">AlayaCare</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="remote_focus-tab" data-toggle="tab" href="#remote-focus-1" role="tab" aria-controls="remote-focus-1" aria-selected="false">Remote Focus</a>
                    </li>
                    @can('agency-create-form')
                    <li class="nav-item">
                        <a class="nav-link" id="create-form-tab" data-toggle="tab" href="#create-form-1"
                            role="tab" aria-controls="create-form-1" onclick="loadFieldMasterList(1)"
                            aria-selected="false">Create Form</a>
                    </li>
                    @endcan
                    @can('agency-form-setup')
                    <li class="nav-item">
                        <a class="nav-link" id="form-setup-tab" data-toggle="tab" href="#form-setup-1"
                            role="tab" aria-controls="form-setup-1" onclick="loadFormSetupList(1)"
                            aria-selected="false">Form Setup</a>
                    </li>
                    @endcan
                    @can('webhook-form')
                    <li class="nav-item">
                        <a class="nav-link" id="agency_wise_webhook_list-tab" data-toggle="tab" href="#agency-wise-webhook-list-1" role="tab" aria-controls="agency-wise-webhook-list-1" aria-selected="false" onclick="loadAgencyWebHook()">Webhook</a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link" id="agency_wise_portal_sent_sms-tab" data-toggle="tab" href="#agency_wise_portal_sent_sms-1" role="tab" aria-controls="agency_wise_portal_sent_sms-1" aria-selected="false" onclick="loadPortalSentSMS()">Portal Sent SMS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="agency_wise_rate_card-tab" data-toggle="tab" href="#agency_wise_rate_card-1" role="tab" aria-controls="agency_wise_rate_card-1" aria-selected="false" onclick="loadRateCrad()">Rate Card</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="agency-wise-tele-services-tab" data-toggle="tab" href="#agency-wise-tele-services-1" role="tab" aria-controls="agency-wise-tele-services-1" aria-selected="false" onclick="loadTeleServices()">Telehealth Services</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="user-email-notification-creator-tab" data-toggle="tab" href="#user-email-notification-creator-1" role="tab" aria-controls="user-email-notification-creator-1" aria-selected="false" onclick="loadUserEmailCreator()">User Email Notification Creator</a>
                    </li>
                    @can('date-wise-agency-view')
                        <li class="nav-item">
                            <a class="nav-link" id="date-wise-agency-view-tab" data-toggle="tab" href="#date-wise-agency-view-1" role="tab" aria-controls="date-wise-agency-view-1" aria-selected="false" onclick="loadUserDateWiseAgencyView()">Date Wise Agency View</a>
                        </li>
                    @endcan

                    <li class="nav-item">
                        <a class="nav-link" id="visiting-detail-tab" data-toggle="tab" href="#visiting-detail-1" role="tab" aria-controls="visiting-detail-1" aria-selected="false">Visiting Detail</a>
                    </li>
                    @can('agency-notes-list')
                    <li class="nav-item">
                        <a class="nav-link" id="agency-notes-tab" data-toggle="tab" href="#agency-notes-1" role="tab" aria-controls="agency-notes-1" aria-selected="false" onclick="loadAgencyNotes()"><i class="mdi mdi-note-text mr-1"></i> Agency Notes</a>
                    </li>
                    @endcan
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="users-1" role="tabpanel" aria-labelledby="users-tab">
                        <div class="row mb-1">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">Users List</h4>
                            </div>
                            <div class="col-sm-6">
                                @can('agency-add-user')
                                <a href="<?php echo URL::to('/'); ?>/agency/adduser?id={{ $encryptedId }}" class="btn btn-primary btn-rounded btn-sm btn-fw pull-right"><i class="mdi mdi-plus"> </i> Add User</a>
                                @endcan

                                @can('agency-user-export')

                                <a href="<?php echo URL::to('/'); ?>/agency/user-export?id={{ $id }}" class="btn btn-warning btn-rounded btn-sm btn-fw pull-right"><i class="mdi mdi-file"> </i> Export User</a>
                                @endcan

                                @can('agency-user-change-status')

                                <a href="javascript:void(0)" onclick="blockUnblockStatus()" class="btn btn-primary btn-rounded btn-sm btn-fw pull-right">User Block Unblock</a>
                                @endcan
                            </div>
                        </div>
                        <div class="table-responsive">

                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <span id="agency_user_list"></span>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="agency-1" role="tabpanel" aria-labelledby="agency-tab">
                        <div class="row">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">Agency Logs</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" id="logList" style="display:flex;justify-content:center;">
                                <img src="{{asset('/ajax-loader.gif')}}" alt="loader" id="loadertag" style="display: none; ">
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="token-1" role="tabpanel" aria-labelledby="token-tab">
                        <div class="row">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">Generate Token</h4>
                            </div>
                            <div class="col-sm-6">
                                @can('agency-generate-token')
                                <a href="javascript:void(0)" data-toggle="modal" onclick="showModalGenerate()" data-target="#agency_token" class="btn btn-success  btn-rounded btn-sm btn-fw pull-right"><i class="mdi mdi-plus"></i>Generate Token</a>
                                @endcan
                            </div>

                        </div>
                        <div class="table-responsive" id="token_ajax_id">



                        </div>
                    </div>

                    <div class="tab-pane fade" id="domain-1" role="tabpanel" aria-labelledby="domain-tab">
                        <div class="row">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">Domain List</h4>
                            </div>
                            <div class="col-sm-6">
                                @can('agency-add-domain')
                                <a class="btn btn-success  btn-rounded btn-sm btn-fw pull-right addDomain" data-whatever="@mdo" href="javascript:void(0)"><i class="mdi mdi-plus"></i> Add Domain</a>
                                @endcan
                            </div>
                        </div>
                        <div class="table-responsive">
                            <span id="domain_list_id"></span>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="hha-detail-1" role="tabpanel" aria-labelledby="hha-detail-tab">
                        @include('agency._partial.agency_hha_tab.agency_hha_tab')
                    </div>

                    <div class="tab-pane fade" id="notification-email-1" role="tabpanel" aria-labelledby="notification-email-tab">
                        <div class="row">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">Notification Email</h4>
                            </div>
                            <div class="col-sm-6">
                                {{-- @can('') --}}
                                <a class="btn btn-success  btn-rounded btn-sm btn-fw pull-right add-notification-email" style="margin: -11px;" data-whatever="@mdo" href="javascript:void(0)"><i class="mdi mdi-plus"></i> Add Notification Email</a>
                                {{-- @endcan --}}
                            </div>
                        </div>
                        <div class="table-responsive">
                            <span id="notification_email_id"></span>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="agency-wise-sms-list-1" role="tabpanel" aria-labelledby="agency_wise_sms_list-tab">
                        <ul class="nav nav-tabs">
                            <li class="nav-item ">
                                <a class="nav-link active" id="sms_template_tab" data-toggle="tab" href="#sms_template_tab-1" role="tab" aria-controls="sms_template_tab-1" aria-selected="false">SMS Template</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="disabled_sms_service_tab" data-toggle="tab" href="#disabled_sms_service_tab-1" role="tab" aria-controls="disabled_sms_service_tab-1" aria-selected="false" onclick="loadSMSServices()">Disabled Service</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="sms_template_tab-1" role="tabpanel" aria-labelledby="sms_template_tab">
                                @include('agency._partial.sms_service_setting.sms_template')
                            </div>
                            <div class="tab-pane fade" id="disabled_sms_service_tab-1" role="tabpanel" aria-labelledby="disabled_sms_service_tab">
                                @include('agency/_partial.agency_wise_sms')
                            </div>

                        </div>
                    </div>
                    <div class="tab-pane fade" id="agency-wise-service-1" role="tabpanel" aria-labelledby="agency_wise_sms_service-tab">
                        <div class="row">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">Agency Wise Service</h4>
                            </div>
                            <div class="col-sm-6">
                                {{-- @can('agency-add-domain') --}}
                                <a class="btn btn-success  btn-rounded btn-sm btn-fw pull-right addService" data-whatever="@mdo" href="javascript:void(0)"><i class="mdi mdi-plus"></i> Add Service</a>
                                {{-- @endcan --}}
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <span id="agency_wise_service_list_id"></span>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="alayacare-1" role="tabpanel" aria-labelledby="alayacare-tab">
                        @include('agency._partial.alayacare.agency_alayacare_tab')

                    </div>

                    <div class="tab-pane fade" id="remote-focus-1" role="tabpanel" aria-labelledby="remote_focus-tab">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-6 card-title">
                                    <h4 class="card-title">Remote Focus</h4>
                                </div>
                                @can('update-remote-details')
                                <div class="col-sm-6">
                                    <a class="btn btn-success  btn-rounded btn-sm btn-fw pull-right edit-remote-details" data-whatever="@mdo" href="javascript:void(0)"><i class="mdi mdi-plus"></i>Edit</a>
                                </div>
                                @endcan
                            </div>
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="row">
                                        <div class="col-md-10">
                                            <dl class="dl-horizontal agency-detail1">
                                                <dt>Remote Focus Grant Type : </dt>
                                                <dd><span id="remote_granttype_id">{{ ($agencyDetails->robort_grant_type !="")? $agencyDetails->robort_grant_type:'N/A' }}</span> </dd>

                                                <dt>Remote Focus User Name : </dt>
                                                <dd><span id="remote_username_id">{{ ($agencyDetails->robort_user_name !="")? $agencyDetails->robort_user_name:'N/A' }}</span> </dd>


                                                <dt>Remote Focus Password : </dt>
                                                <dd><span id="remote_password_id">{{ ($agencyDetails->robort_user_password  !="")?$agencyDetails->robort_user_password:' N/A' }}</span> </dd>

                                                <dt>Enabled Remote Focus : </dt>
                                                <dd> <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="robort-btn" class="robort-btn" {{ $agencyDetails->robort_status != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label> </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="hide_show_remote" style="display:@if($agencyDetails->robort_status ==1) @else none @endif">
                                    <div class="row">
                                        <div class="col-12 col-sm-4 col-md-4 col-xl-4 grid-margin stretch-card">
                                            <div class="card d-flex align-items-center">
                                                <div class="card-body">
                                                    <h4 class="card-title">Fetch All Employee</h4>
                                                    <div class="d-flex justify-content-between">

                                                    </div>
                                                    <div class="pull-left">
                                                        <a href="javascript:void(0)" class="remote_refresh_employee_id" onclick="remoteRefreshEmployee()">Refresh <i class="fa fa-arrow-circle-right"></i></a>
                                                        <img src="{{ asset('ajax-loader.gif') }}" alt="loader" class="loader" id="remote_loadertag1Employee" style="display:none;"><br>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                        </div>


                    </div>
                    <div class="tab-pane fade" id="create-form-1" role="tabpanel"
                        aria-labelledby="create-form-tab">
                        <div class="row">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">Create Form List</h4>
                            </div>
                            <div class="col-sm-6 mb-2">
                                @can('agency-create-form-add-new-field')
                                <a class="btn btn-success btn-rounded btn-sm btn-fw pull-right addFieldMasterModal"
                                    href="javascript:void(0)"><i class="mdi mdi-plus"> </i>Add
                                    Custom</a>
                                @endcan

                                @can('agency-create-form-add-custom-field')
                                <a href="javascript:void(0);"
                                    class="btn btn-success btn-rounded btn-sm btn-fw pull-right mr-2"
                                    data-toggle="modal" data-target="#addFieldModal" data-id='{{ $id }}'>
                                    <i class="mdi mdi-plus"></i> Add New Field
                                </a>
                                @endcan
                            </div>
                        </div>
                        <div class="table-responsive">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <span id="create_form_list"></span>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="form-setup-1" role="tabpanel"
                        aria-labelledby="form-setup-tab">
                        <div class="row">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">Form Setup List</h4>
                            </div>
                            <div class="col-sm-6 mb-2">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <span id="form_setup_list"></span>
                        </div>
                    </div>
                    @include('agency/webhook_content')
                    <div class="tab-pane fade" id="agency_wise_portal_sent_sms-1" role="tabpanel" aria-labelledby="agency_wise_portal_sent_sms-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Portal SMS List</h4>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <th>#</th>
                                        <th>Patient</th>
                                        <th>Caregiver</th>
                                    </thead>
                                    <tbody id="load_portal_sms_list">

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    @include('agency/rate_card')
                    @include('agency/telehealth_service_list')
                    @include('agency/user_creator_notification')
                    @include('agency/_partial/date_agency_wise_access/date_agency_wise_access_list')
                    @include('agency/_partial/visitin_aids/visitin_aids_list')

                    @can('agency-notes-list')
                    <div class="tab-pane fade" id="agency-notes-1" role="tabpanel" aria-labelledby="agency-notes-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                            <h5 class="mb-0"><i class="mdi mdi-note-text"></i> Agency Notes / Alerts</h5>
                            @can('agency-notes-add')
                            <a href="javascript:void(0);" class="btn btn-warning btn-sm btn-rounded" onclick="openAgencyNoteModal()"><i class="mdi mdi-plus"></i> Add Note</a>
                            @endcan
                        </div>
                        <div id="agency-notes-list-wrapper">
                            <p class="text-muted" id="agency-notes-empty">No notes added yet.</p>
                        </div>
                    </div>
                    @endcan

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal-4" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add Domain</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency-wise-domain-save")}}' name="adduser" method="post" id="submitId">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                        <input type="hidden" name="id" value="" id="mid">

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Domain Name<span style="color:red">*</span></label>
                            <input type="text" name="domain" id="domain_id" class="form-control" placeHolder="Enter Domain Name">
                            <span id="domain_error" class="error mt-2" for="document_type"></span>
                        </div>

                        <div class="modal-footer">
                            <button type="button" id="saveId" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

<!-- ip address modal start -->
<div class="modal fade" id="exampleModal-5" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add IP Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency-ip-address-save")}}' name="adduser" method="post" id="submitIpAddress">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="agency_id" value="{{ $id }}">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">IP Address</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter IP Address" id="ip_address" name="ip_address" value="<?php echo old('ip_address'); ?>">
                            <span id="ip_address_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('ip_address'); ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Type</label>
                        <div class="col-sm-9">
                            <div class="d-flex">
                                <div class="mr-3">
                                    <input type="radio" class="" id="block" name="type" value="block"><label for="block" class="ml-1">Block</label>
                                </div>
                                <div>
                                    <input type="radio" class="" id="unblock" name="type" value="unblock"><label for="unblock" class="ml-1">Unblock</label>
                                </div>
                            </div>
                            <span id="type_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('type'); ?></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="saveIPAddress" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- ip address modal end -->

<!-- edit ip address modal start -->
<div class="modal fade" id="exampleModal-6" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit IP Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency-ip-address-save")}}' name="adduser" method="post" id="submitEditIpAddress">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="agency_id" value="{{ $id }}">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">IP Address</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter IP Address" id="ip_address_edit" name="ip_address_edit" value="<?php echo old('ip_address'); ?>">
                            <span id="ip_address_edit_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('ip_address'); ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Type</label>
                        <div class="col-sm-9">
                            <div class="d-flex">
                                <div class="mr-3">
                                    <input type="radio" class="" name="type_edit" value="block"><label for="block" class="ml-1">Block</label>
                                </div>
                                <div>
                                    <input type="radio" class="" name="type_edit" value="unblock"><label for="unblock" class="ml-1">Unblock</label>
                                </div>
                            </div>
                            <span id="type_edit_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('type'); ?></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="updateIPAddress" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="alaycare-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">AlayaCare</h5>
                <button type="button" class="closeAlayaCare" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' name="adduser" method="post" id="alaycare-details">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">AlayaCare Username<span style="color:red">*</span></label>

                        <input type="text" name="alaycare_username" value="{{$agencyDetails->alaycare_username}}" id="alaycare_username" class="form-control" placeHolder="Enter AlayaCare Username">

                        <span id="alaycare_username_error" class="error mt-2" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">AlayaCare Password<span style="color:red">*</span></label>

                        <input type="text" name="alaycare_password" value="{{$agencyDetails->alaycare_password}}" id="alaycare_password" class="form-control" placeHolder="Enter AlayaCare Password">

                        <span id="alaycare_password_error" class="error mt-2" for="document_type"></span>
                    </div>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">AlayaCare URL<span style="color:red">*</span></label>

                        <input type="text" name="alaycare_url" value="{{$agencyDetails->alayacare_url}}" id="alaycare_url" class="form-control" placeHolder="Enter AlayaCare Url">

                        <span id="alaycare_url_error" class="error mt-2" for="document_type"></span>
                    </div>



                    <div class="modal-footer">
                        <button type="button" id="save-alaycare-details" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light closeAlayaCare" data-dismiss="modal" onclick="">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- Date Picker -->
<div class="modal fade" id="service-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="servie_lable">Add Agency Wise Service</h5>
                <button type="button" class="close" onclick="resetService()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' name="add-service" method="post" id="add-service-form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                    <input type="hidden" id="serviceId" name="m_id" value="">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Type<span style="color:red">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="type" class="form-check-input caregiver_type" id="caregiver_type" value="Caregiver" onclick="getTypeWiseService('Caregiver')">
                                        Caregiver
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="type" class="form-check-input caregiver_type" id="patient_type" value="Patient" onclick="getTypeWiseService('Patient')">
                                        Patient
                                    </label>
                                </div>
                            </div>
                        </div>
                        <span style="color:red" id="type_error_service"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Service<span style="color:red">*</span></label>
                        <select class="js-example-basic-multiple w-100" name="agency_service" id="service_id1">
                            <option value="">Select Service</option>
                        </select>

                        <span id="agency_service_error" class="error mt-2" for="document_type"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="save-service" class="btn btn-success">Save</button>
                        <button type="button" onclick="resetService()" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="robort-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Remote Focus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' name="adduser" method="post" id="robort-details">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Remote Grant Type<span style="color:red">*</span></label>
                        <input type="text" name="robort_granttype" value="{{( $agencyDetails->robort_grant_type !='')?$agencyDetails->robort_grant_type:''}}" id="robort_granttype" class="form-control" placeHolder="Enter Remote Focus Grant Type">
                        <span id="robort_granttype_error" class="error mt-2" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Remote Focus Username<span style="color:red">*</span></label>
                        <input type="text" name="robort_username" value="{{( $agencyDetails->robort_user_name !='')?$agencyDetails->robort_user_name:''}}" id="robort_username" class="form-control" placeHolder="Enter Remote Focus Username">
                        <span id="robort_username_error" class="error mt-2" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Remote Focus Password<span style="color:red">*</span></label>
                        <input type="text" name="robort_password" value="{{( $agencyDetails->robort_user_password !='')?$agencyDetails->robort_user_password:''}}" id="robort_password" class="form-control" placeHolder="Enter Remote Focus Password">
                        <span id="robort_password_error" class="error mt-2" for="document_type"></span>
                    </div>



                    <div class="modal-footer">
                        <button type="button" id="save-robort-details" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@include('agency._partial.agency_token_modal')
@include('agency._partial.api_token_modal')

<form id="directId" action="<?php echo URL::to('/'); ?>/agency/token-insert" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="agency_id" value="<?= $id ?>">
    <input type="hidden" name="id" value="<?php if (isset($generate_token_details->id) && $generate_token_details->id != '') {
                                                echo $generate_token_details->id;
                                            } ?>">
</form>



@include('fieldMaster._partial.create')
@include('fieldMaster._partial.agency_new_field_modal')
@include('agency._partial.edit_name_agency_token_modal')
@include('agency._partial.app_detail_edit_modal')
@include('agency._partial.app_detail_modal')
@include('agency._partial.agency_web_hook.add_agency_web_hook')
@include('agency._partial.modal.assignsms_notfication')
@include('rateCard/_partial/rate_card_add_modal')
@include('rateCard/_partial/rate_card_edit_modal')
@include('agency/_partial/modal/notification_email/add_notification_email_popup_modal')
@include('agency/_partial/modal/telehealth_service/telehealth_service_add')
@include('agency/_partial/modal/telehealth_service/telehealth_service_edit')
@include('agency/_partial/modal/document_email_modal')
@include('agency/_partial/modal/efax_no_modal')
@include('agency/_partial/modal/user_email_creator_notification/user_email_notification_creator')
@include('agency/_partial/modal/assign_nyuser_modal')
@include('agency/_partial/modal/date_agency_wise_access/add_date_agency_wise_access_modal')
@include('agency/_partial/modal/date_agency_wise_access/edit_date_agency_wise_access_modal')
@include('agency/_partial/modal/delete_agency_with_users_modal')
@include('agency._partial.modal.agency_hha.hha_mdo_edit_form_modal')
@include('agency._partial.visitin_aids.visiting_detail_edit_modal')
@include('agency._partial.modal.agency_notes_modal')
@include('include/footer')

<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vertical-layout-light/daterangepicker.css')}}" />
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script src="{{ asset('assets/modulejs/hha_module.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/agency.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/agency_webhook/agency_webhook.js')}}?time={{ env('timestamp')}}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
  <script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script>
    $('.datepicker').datepicker();
    var _HHA_FETCH_CAREGIVER = "{{ url('fetch-caregiver') }}";
    var _AGENCY_ID = '{{ $agencyDetails->id}}';
    var _AGENCYID = '{{ $agencyDetails->id}}';

    var _HHA_FETCH_MEDICAL = "{{ url('fetch-hha-medical') }}";
    var _HHA_FETCH_PATIENT = "{{ url('fetch-patient')}}";
    var _AGENCY_TOKEN_URL = "{{ url('agency-token-list')}}";
    var _TOKEN_DELETE = "{{ url('agency-token-delete')}}";
    var _TOKEN_API_CALL_LIST = "{{ url('token-api-call')}}";

    var _FORM_GROUP_URL = "{{ url('get-form-groups') }}";
    var _SAVE_AGENCY_WEB_HOOK = "{{ url('agency-wise-webhook-save') }}";
    var _LOAD_AGENCY_WEB_HOOK = "{{ url('load-agency-web-hook') }}";

    var _EDIT_AGENCY_WEB_HOOK = "{{ url('edit-agency-wise-webhook') }}";
    var _DELETE_AGENCY_WEB_HOOK = "{{ url('delete-agency-web-hook') }}";
    var _AGENCT_RATE_EXPORT = "{{ url('rate-export') }}";
    var _AGENCT_GENERATE_LAST_MONTH = "{{ url('getLastMonth') }}";
    var _LAST_MONTH_INVOICE = "<?php echo date('F', strtotime('last month')); ?>";
    var _LAST_MONTH_YEARE_INVOICE = "<?php echo date('Y', strtotime('last month')); ?>";
    var _AGENCY_WISE_INVOICE = "<?php echo url('agencywise-invoice'); ?>";
    var _AGENCY_DELETE = "{{url('agency/delete/')}}";
    var _AGENCY_WISE_SMS_DELETED = "{{url('agency-wise-sms-delete')}}";
    var _AGENCY_WISE_SMS_SAVE = "{{url('agency-wise-sms-save')}}";
    var _AGENCY_NOTIFICATION_EMAIL_LIST = "{{url('notification-email-list')}}";
    var _AGENCY_WISE_NOTIFICATION_EMAIL_SAVE = "{{url('agency-wise-notification-email-save')}}";
    var _AGENCY_WISE_DOMAIN_LIST = "{{url('agency-wise-domain-list')}}";
    var _AGENCY_NAME = "{{ $agencyDetails->agency_name }}";
    var _AGENCY_WISE_DOMAIN_SAVE = "{{ url('agency-wise-domain-save')}}";
    var _AGENCY_WISE_COUNTRY_SAVE = "{{ url('agency-country-save')}}";
    var _AGENCY_WISE_IPADDESS_SAVE = "{{ url('agency-ip-address-save')}}";
    var _AGENCY_WISE_COUNTY_LIST = "{{ url('agency-wise-country-list')}}";
    var _AGENCY_WISE_IP_LIST = "{{ url('agency-wise-ip-list')}}";
    var _AGENCY_DOMAIN_DELETE = '{{ url("agency-domain-delete")}}';
    var _AGENCY_IP_EDIT = "{{ url('agency-ip-edit')}}";
    var _AGENCY_IP_UPDATE = "{{ url('agency-ip-update')}}";
    var _AGENCY_IP_DELETE = '{{ url("agency-ip-delete")}}';
    var _AGENCY_TWO_FACTOR_ENABLE_DISABLED = "{{ url('agency-two-factor-enable-disable') }}";
    var _AGENCY_PASSWORD_EXPIRED_ENABLED_DISABLED = "{{ url('agency-password-expired-enable-disable') }}";
    var _AGENCY_PORTAL_SMS_STATUS = '{{route("portalAgencySMSStatus")}}';
    var _AGENCY_LOAD_PORTAL_SMS = "{{ url('load-portal-sms-list')}}";
    var _DISCIPLINE_LIST = "{{ url('ajax-all-discipline')}}";
    var _CSRF_TOKEN = "{{ csrf_token()}}"
    var _UPDATE_DOCUMENT_EMAIL = "{{ url('update-document-email')}}";
    var _UPDATE_EFAX_NO = "{{ url('update-efax-no')}}";
    var _AGENCY_USER_BLOCK_UNBLOCK = "{{ url('agency-user-block-unblock')}}";
    var _SEARCH_NYBEST_USER ="{{ url('search-nybest-user') }}";
    var _LOAD_DATE_WISE_AGENCY_ACCESS_LIST ="{{ url('date-wise-agency-access/load-date-wise-agency-access-list')}}";
    var _SAVE_AGENCY_WISE_DATE_PERMISSION = "{{ url('/date-wise-agency-access/save-date-view-agency-view')}}";
    var _EDIT_DATE_WISE_AGENCY_ACCESS = "{{ url('/date-wise-agency-access/edit-date-view-agency-view')}}";
    var _UPDATE_AGENCY_WISE_DATE_PERMISSION = "{{ url('/date-wise-agency-access/update-date-view-agency-view') }}";
    var _DELETE_AGENCY_WISE_DATE_PERMISSION =  '{{ url("/date-wise-agency-access/delete-date-view-agency-view")}}';

    var _UPDATE_HHA_MDO_ORDER_DETAILS="{{ url('agency/update-hha-md-details')}}";
    var _DISABLED_HHA_MDO_CREDENTIAL = "{{ url('agency/disabled-hha-md-details') }}";
    var AGENCY_TASK_HEALTH_API = "{{ url('status-change-task-health') }}";
    var AGENCY_RESTRICT_SERVICE_REQUEST_UPDATE_API = "{{ url('status-change-restrict-service-request-update') }}";
    var AGENCY_FILE_MANAGER_TOGGLE_API = "{{ url('agency/toggle-file-manager') }}";
    var AGENCY_PORTAL_ARCHIVE_TOGGLE_API = "{{ url('agency/toggle-portal-archive') }}";
    var AGENCY_REVIEW_TOGGLE_API = "{{ url('agency/toggle-review') }}";
    var AGENCY_TELEHEALTH_SEND_SMS_TOGGLE_API = "{{ url('agency/toggle-telehealth-send-sms') }}";
    var AGENCY_HIDE_COMPLETED_RECORDS_TOGGLE_API = "{{ url('agency/toggle-hide-completed-records') }}";
    var AGENCY_AI_CALL_LOGS_TOGGLE_API = "{{ url('agency/toggle-ai-call-logs') }}";
    var _SYNC_AGENCY_WISE_MEDICAL = "{{ url('hha/hha-caregiver-medicals/sync-agency-wise-medical')}}";
    var _UPDATE_APP_VISITING_AID_DETAIL ="{{ url('agency/app-visting-detail-update')}}";
    var _ENABLED_DISABLED_VISITING_AID ="{{ url('agency/enabled-disabled-app-visting')}}";
    var _SYNC_VISITING_PENDING_MEDICAL = "{{ url('visiting-aid/pending-medicals/sync-medical') }}"
</script>

<script>
    function getData(page) {

        var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

        $.ajax({
            method: 'GET',
            url: "{{ url('agency-view-logs') }}" + "?page=" + page,
            data: {
                'id': "{{$id}}",
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
                toastr.error('Something happened. Try again');
            }
        });
    }

    function loadUserList(page) {

        $.ajax({
            method: 'GET',
            url: "{{ url('agency-user-list') }}" + "?type=user&page=" + page,
            data: {
                'id': "{{$id}}",
                '_token': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $('#loadertag').show();
            },
            success: function success(response) {

                $('#loadertag').hide();
                $('#agency_user_list').html("");
                $('#agency_user_list').html(response);
            },
            error: function error(_error) {
                toastr.error('Something happened. Try again');
            }
        });
    }


    var staticStatusArray = '{!! json_encode(["noshow","checkin","completed"]) !!}';
    var patientStatusArray = '{!! json_encode(array_keys($statusData)) !!}';

    function editNotificationEmail(id) {
        $.ajax({
            method: 'GET',
            url: "{{ url('edit-email-notification') }}",
            data: {
                'id': id,

            },
            success: function(response) {

                // Hide all status sections initially
                $('#caregiver_status_show').addClass('hide');
                $('#patient_status_show').addClass('hide');

                // Uncheck all checkboxes first
                $('.caregiver_checkbox').prop("checked", false);
                $('.patient_checkbox').prop("checked", false);
                $('.caregiver_status_checkbox').prop("checked", false);
                $('.patient_status_checkbox').prop("checked", false);

                // Track if Status Update is selected
                var isCaregiverStatusUpdateSelected = false;
                var isPatientStatusUpdateSelected = false;

                // Handle Caregiver checkboxes
                if (response.data.caregivers_id != "") {
                    var splitData = response.data.caregivers_id.split(',');

                    $.each(splitData, function(i, v) {
                        if (v == 1021) {
                            isCaregiverStatusUpdateSelected = true;
                        }
                        $('#caregiver_notification_email' + v).prop("checked", true);
                    })
                }

                // Handle Patient checkboxes
                if (response.data.patients_id != "") {
                    var splitData = response.data.patients_id.split(',');
                    $.each(splitData, function(i, v) {
                        if (v == 1021) {
                            isPatientStatusUpdateSelected = true;
                        }
                        $('#patient_notification_email' + v).prop("checked", true);
                    })
                }

                // Show/hide caregiver status section and check saved statuses
                if (isCaregiverStatusUpdateSelected) {
                    $('#caregiver_status_show').removeClass('hide');

                    if (response.data.caregiver_status != null) {
                        var splitData = response.data.caregiver_status.split(',');
                        $.each(JSON.parse(staticStatusArray), function(is, v) {
                            $.each(splitData, function(i, vs) {
                                if (v == vs) {
                                    $('#caregiver_notification_status_email' + is).prop("checked", true);
                                }
                            });
                        })
                    }
                }

                // Show/hide patient status section and check saved statuses
                if (isPatientStatusUpdateSelected) {
                    $('#patient_status_show').removeClass('hide');

                    if (response.data.patient_status != null) {
                        var splitDataPatient = response.data.patient_status.split(',');
                        $.each(JSON.parse(patientStatusArray), function(is, v) {
                            $.each(splitDataPatient, function(i, vs) {
                                if (v == vs) {
                                    $('#patient_notification_status_email' + is).prop("checked", true);
                                }
                            });
                        })
                    }
                }

                $('#notificationId').val(id)
                $('#notificationEmail').val(response.data.email)
                $('.notification-emails').html("Edit Notification Email")
                $('#add-notification-email-popup').modal('show');
                getResponse(response.data.service_id);
                getDiscipline(response.data.discipline_id);
            },
            error: function(jxr) {

            }

        });
    }

    function resetNotificationEmail() {
        $('#notificationId').val('');
        $('.error').html('');
        $('#addnotificationemail')[0].reset();
        $('.notification-emails').html("Add Notification Email");
        // Hide status sections on reset
        $('#caregiver_status_show').addClass('hide');
        $('#patient_status_show').addClass('hide');
    }
    $('#add-notification-email-popup').on('hidden.bs.modal', function() {
        resetNotificationEmail();
    });

    function deleteNotificationEmail(id) {
        $.confirm({
            title: 'Are you sure delete notification email?',
            columnClass: "col-md-6",
            content: "",

            buttons: {
                formSubmit: {
                    text: 'Confirm',
                    btnClass: 'btn-danger',
                    action: function() {
                        $.ajax({
                            url: '{{ url("delete-notification-email")}}',
                            type: "get",
                            data: {
                                'id': id,

                            },
                            success: function(res) {
                                toastr.success(res.error_msg);
                                notificationEmailList(1);
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
    $(document).ready(function() {
        $("#agency-logo").click(function() {

            $("#image-upload").click();
        });
        $("#image-upload").change(function() {
            $('#image-error').html('');


            var fileInput = $('input[type="file"]');
            var filePath = fileInput.val();
            if (filePath && /\.(jpe?g|png|jpg|gif)$/i.test(filePath)) {
                $('#image-error').html('');
            } else {
                $('#image-error').html('Only JPG,PNG,JPEG image select');
                return false;
            }
            if (this.files && this.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $("#agency-logo").attr("src", e.target.result);
                    $("#agency-logo").show();
                };

                reader.readAsDataURL(this.files[0]);
                var formData = new FormData($("#agency-logo-form")[0]);
                $.ajax({
                    url: "{{ route('agencyLogoUpload') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        toastr.success(response.message);

                    },
                    error: function(xhr) {

                        console.log(xhr.responseText);
                    }
                });
            }
        });

    });
    $(document).on("change", ".alaycare-btn", function() {
        var IsAlaycare = $(this).prop('checked') == true ? 1 : 0;

        var agencyId = $('#agency_id').val();
        $.ajax({
            type: "GET",

            url: '{{route("agencyAlaycareStatus")}}',
            data: {
                'is_alaycare': IsAlaycare,
                'agency_id': agencyId
            },
            success: function(data) {

                toastr.success(data.error_msg);
                final_array = [];
                $('#hide_show_alayacare').attr('style', 'display:none')
                if (data.data == 1) {
                    $('#hide_show_alayacare').attr('style', '')
                }
                getAllSkills(1);
            }
        });


    });

    $(document).on("click", ".edit-alaycare-details", function() {
        $('#alaycare-popup').modal('show');
    });

    $('#save-alaycare-details').click(function(e) {
        var alaycareUsername = $('#alaycare_username').val();
        var alaycarePassword = $('#alaycare_password').val();
        var alaycareUrl = $('#alaycare_url').val();
        var cnt = 0;
        $('#alaycare_password_error').html('');
        $('#alaycare_username_error').html('');
        $('#alaycare_url_error').html('');
        if (alaycareUsername.trim() == '') {
            $('#alaycare_username_error').html("Please Enter AlayaCare Username");
            cnt = 1;
        }
        if (alaycarePassword.trim() == '') {
            $('#alaycare_password_error').html("Please Enter AlayaCare Password");
            cnt = 1;
        }

        if (alaycareUrl.trim() == '') {
            $('#alaycare_url_error').html("Please Enter AlayaCare URL");
            cnt = 1;
        }

        if (cnt == 1) {
            return false;
        } else {
            var forms = $('#alaycare-details')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ url('/agency/alaycare-details-save')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);

                    $('#alaycare_username_id').html(alaycareUsername)
                    $('#alaycare_password_id').html(alaycarePassword)
                    $('#alaycare_url_id').html(alaycareUrl)

                    $('#alaycare_username').val(alaycareUsername)
                    $('#alaycare_password').val(alaycarePassword)
                    $('#alaycare_url').val(alaycareUrl)
                    $('#alaycare-popup').modal('hide');
                    getAllSkills(1);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });
        }
    })
    $("#service_id").select2({
        placeholder: "Select Service"
    });

    function getResponse(existingId = "") {

        $.ajax({
            async: false,
            global: false,
            type: "GET",
            url: "<?php echo URL::to('/'); ?>/ajax-all-service",

            success: function(res) {

                var response = "";
                var split = existingId.split(',');
                if (res.data.length != 0) {

                    response = '<option value="">Select Service</option>'
                    $.each(res.data, function(i, v) {
                        if (v.types != "" || v.types != "") {
                            var selected = split.find(o => o == v.id);
                            var selecteds = '';
                            if (selected) {
                                selecteds = "selected='selected'";
                            }
                            response += '<option value="' + v.id + '" ' + selecteds + '>' + v.name + ' ( ' + v.types + ' ) </option>';

                        }
                    })
                }

                $('#service_id').html("");
                $('#service_id').html(response);

            }
        })

    }
    $('.addService').click(function() {
        $('#service_id1').html("");
        $('#service-popup').modal('show');
    });

    $('#save-service').click(function() {
        var agency_service = $('#service_id1').val();


        var cnt = 0;
        $('#type_error_service').html('');
        $('#agency_service_error').html('');

        if ($('input[name="type"]').is(':checked') == false) {
            $('#type_error_service').html("Please select Type");
            cnt = 1;
        }
        if (agency_service == '') {
            $('#agency_service_error').html("Please Select Service");
            cnt = 1;
        }

        if (cnt == 1) {
            return false;
        } else {
            var text = $('#service_id1 option:selected').text();
            var forms = $('#add-service-form')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');
            newForms.append('name', text);
            $.ajax({
                url: "{{ url('/agency/agency-wise-service-save')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);

                    $('#add-service-form')[0].reset();
                    $('#service-popup').modal('hide');
                    // location.reload();
                    getService(1);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });
        }
    });

    function getService(page) {
        $.ajax({
            url: "{{ url('/agency/agency-wise-service-ajax-list')}}",
            type: "GET",
            data: {
                'agency_id': '{{$id}}',
                'page': page,

            },
            success: function(response) {
                $('#agency_wise_service_list_id').html("");
                $('#agency_wise_service_list_id').html(response);
            }
        });

        return false;
    }

    function deleteService(id) {
        $.confirm({
            title: 'Are you sure delete Service?',
            columnClass: "col-md-6",
            content: "",

            buttons: {
                formSubmit: {
                    text: 'Confirm',
                    btnClass: 'btn-danger',
                    action: function() {
                        $.ajax({
                            url: '{{ url("delete-service")}}',
                            type: "get",
                            data: {
                                'id': id,

                            },
                            success: function(res) {
                                toastr.success(res.error_msg);
                                getService(1);
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

    function editService(id) {
        $.ajax({
            url: '{{ url("edit-service")}}',
            type: "get",
            data: {
                'id': id,

            },
            success: function(res) {

                var response = res.data;
                $('#serviceId').val(id)
                if (response.type == "Caregiver") {
                    $('#caregiver_type').prop('checked', true);
                } else {
                    $('#patient_type').prop('checked', true);
                }
                $('#agency_service').val(response.name)
                $('#servie_lable').html("Edit Service")
                $('#service-popup').modal('show');
                getTypeWiseService(response.type)
                $('#service_id1').val(response.service_id).trigger("changed");
            }
        })
    }

    function resetService() {
        $('#serviceId').val('');
        $('#type_error_service').html("");
        $('#agency_service_error').html("");
        $('#add-service-form')[0].reset();
        $('#servie_lable').html("Add Service")
    }
    $('#service-popup').on('hidden.bs.modal', function() {
        $('#service_id1').html("");
        resetService();
    });

    $(document).on('click', '.pagination-service .pagination a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];

        getService(page);
    });

    $(document).on("change", ".robort-btn", function() {
        var IsRobort = $(this).prop('checked') == true ? 1 : 0;

        var agencyId = $('#agency_id').val();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '{{route("agency-robort-status")}}',
            data: {
                'is_robort': IsRobort,
                'agency_id': agencyId
            },
            success: function(data) {
                toastr.success(data.error_msg)
                $('#hide_show_remote').attr('style', 'display:none');
                if (data.data == 1) {
                    $('#hide_show_remote').attr('style', '');
                }
            }
        });


    });

    $(document).on("click", ".edit-remote-details", function() {
        $('#robort-popup').modal('show');
    });

    $('#save-robort-details').click(function(e) {
        var robortUsername = $('#robort_username').val();
        var robortPassword = $('#robort_password').val();
        var robortGrantType = $('#robort_granttype').val();
        var cnt = 0;
        $('#robort_password_error').html('');
        $('#robort_username_error').html('');
        $('#robort_granttype_error').html('');

        if (robortUsername.trim() == '') {
            $('#robort_username_error').html("Please Remote Focus Username");
            cnt = 1;
        }
        if (robortPassword.trim() == '') {
            $('#robort_password_error').html("Please Remote Focus Password");
            cnt = 1;
        }

        if (robortGrantType.trim() == '') {
            $('#robort_granttype_error').html("Please Remote Focus Grant Type");
            cnt = 1;
        }

        if (cnt == 1) {
            return false;
        } else {

            var forms = $('#robort-details')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ url('/agency/robort-details-save')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);
                    $('#robort_username').val(robortUsername)
                    $('#robort_password').val(robortPassword)
                    $('#robort_granttype').val(robortGrantType)
                    $('#remote_username_id').html(robortUsername)
                    $('#remote_password_id').html(robortPassword)
                    $('#remote_granttype_id').html(robortGrantType)
                    $('#robort-popup').modal('hide');

                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });
        }
    })
    var final_array = [];

    function loadAgencySkill() {
        var response = "{{ json_encode($agency_skill)}}";
        $.each(JSON.parse(response), function(i, v) {
            final_array.push(v)

        })
    }
    loadAgencySkill();

    var skillPage;

    function getAllSkills(page) {
        $('#loaderAlayaSkill').attr('style', '');
        $.ajax({
            url: "{{ url('agency-alayacare-skill')}}",
            type: "get",
            data: {
                agency_id: "{{ $agencyDetails->id}}",
                page: page
            },
            success: function(response) {

                var json = (response.data.items != undefined) ? response.data.items : [];
                var responseHtml = '';
                if (json.length != 0) {
                    var cnt = 1;
                    if (response.data.page != 1) {
                        cnt = (response.data.page * 100) - 99;
                    }
                    $.each(json, function(i, v) {
                        var checked = "";
                        if (final_array.includes(v.id)) {
                            checked = 'checked="checked"';
                        }
                        responseHtml += '<tr><td><input type="checkbox" name="cbox" class="cbox" value="' + v.id + '" ' + checked + '></td><td>' + cnt++ + '</td><td>' + v.branch.name + '</td><td>' + v.name + '</td><td>' + v.category.name + '</td></tr>';
                    });
                    skillPage = response.data.page;

                    if (response.data.total_pages > 1) {

                        $('#nextSkillId').attr('style', '');
                    } else {

                        if (response.data.total_pages == response.data.page) {

                            $('#previousSkillId').attr('style', '');
                            $('#nextSkillId').attr('style', 'display:none');
                        }
                    }
                } else {
                    responseHtml = '<tr><td colspan="4">No record available</td></tr>';
                    $('#previousSkillId').attr('style', 'display:none');
                    $('#nextSkillId').attr('style', 'display:none');
                }
                $('#alayacare_skill_response').html("");
                $('#alayacare_skill_response').html(responseHtml);


            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }

    function nextSkill() {
        $('#alayacare_skill_response').html('<tr><td class="line loading-shimmer" colspan="5"></td></tr><tr><td class="line loading-shimmer" colspan="5"></td></tr><tr><td class="line loading-shimmer" colspan="5"></td></tr><tr><td class="line loading-shimmer" colspan="5"></td></tr><tr><td class="line loading-shimmer" colspan="5"></td></tr>');
        getAllSkills(skillPage + 1);
        $('#previousSkillId').attr('style', '');
    }

    function previousSkill() {
        if (skillPage - 1 != 0) {
            $('#alayacare_skill_response').html('<tr><td class="line loading-shimmer" colspan="5"></td></tr><tr><td class="line loading-shimmer" colspan="5"></td></tr><tr><td class="line loading-shimmer" colspan="5"></td></tr><tr><td class="line loading-shimmer" colspan="5"></td></tr><tr><td class="line loading-shimmer" colspan="5"></td></tr>');
            getAllSkills(skillPage - 1);
            if (skillPage - 1 == 1) {
                $('#previousSkillId').attr('style', 'display:none');
            }
        }
    }

    $('body').on('click', '#cboxid', function(e) {
        var checked = $(this).is(":checked");
        if (checked == true) {
            $('.cbox').prop('checked', true);
        } else {
            $('.cbox').prop('checked', false);
        }
    })

    function addSkill() {
        var checked = $('.cbox').is(":checked");
        if (checked == false) {
            toastr.error("Please select checkbox");
            return false;
        } else {
            var final_array = [];
            $('.cbox').each(function(i, v) {
                var schecked = $(this).is(":checked");
                if (schecked == true) {
                    if (!final_array.includes($(this).val())) {
                        var values = $(this).val();

                        final_array.push(values);
                    }

                }
            });

            $.ajax({
                url: "{{ url('agency-add-skill') }}",
                type: "post",
                data: {
                    'skill': final_array,
                    'agency_id': '{{ $agencyDetails->id}}',
                    '_token': '{{ csrf_token()}}',

                },
                success: function(res) {
                    var json = res.data;
                    final_array = [];
                    $.each(json, function(i, v) {
                        if (!final_array.includes(v)) {
                            final_array.push(v)
                        }

                    })
                    toastr.success(res.error_msg);
                },
                error: function(xhr, status, error) {

                    toastr.error(xhr.responseJSON.error_msg);
                }
            })
        }
    }

    $('.closeAlayaCare').click(function(e) {

        $('.error').html("")
    })

    function refreshEmployee(page) {
        if (!page) page = 1;
        $('#loadertag1Employee').attr('style', '');
        $.ajax({
            url: "{{ url('refresh-agency-employee') }}",
            type: "get",
            data: {
                'agency_id': '{{ $agencyDetails->id}}',
                'page': page,
            },
            success: function(res) {
                // Update total count on each response
                $('#total_employee_id').html(res.data.total);

                if (res.data.current_page < res.data.total_pages) {
                    // More pages left, fetch next page
                    setTimeout(() => {
                        refreshEmployee(res.data.current_page + 1);
                    }, 500);
                    
                } else {
                    // All pages fetched, update UI
                    $('#loadertag1Employee').attr('style', 'display:none');
                    $('.refresh_employee_id').attr('style', '');
                    $('#syncEmployee').attr('style', 'display:none');
                    $('.total_employee_id').attr('style', 'display:none');
                    $('#total_employee_id').html('');
                    if (res.data.total != 0) {
                        $('.refresh_employee_id').attr('style', 'display:none');
                        $('#syncEmployee').attr('style', '');
                        $('.total_employee_id').attr('style', '');
                        $('#total_employee_id').html(res.data.total);
                    }
                }
            },
            error: function(jqr) {
                $('#loadertag1Employee').attr('style', 'display:none');
                toastr.error(jqr.responseJSON.error_msg);
            }

        })

    }

    function refreshClient() {
        $('#loadertag1Client').attr('style', '');
        $.ajax({
            url: "{{ url('refresh-agency-client') }}",
            type: "get",
            data: {
                'agency_id': '{{ $agencyDetails->id}}',

            },
            success: function(res) {
                $('#loadertag1Client').attr('style', 'display:none');
                $('.refresh_client_id').attr('style', '');
                $('#syncClient').attr('style', 'display:none');
                $('.total_client_id').attr('style', 'display:none');
                $('#total_client_id').html('');
                if (res.data.total != 0) {
                    $('.refresh_client_id').attr('style', 'display:none');
                    $('#syncClient').attr('style', '');
                    $('.total_client_id').attr('style', '');
                    $('#total_client_id').html(res.data.total);
                }

            },
            error: function(jqr) {
                $('#loadertag1Client').attr('style', 'display:none');
                toastr.error(jqr.responseJSON.error_msg);
            }

        })
    }


    function refreshSkill() {
        $('#loadertag1Skill').attr('style', '');
        $.ajax({
            url: "{{ url('refresh-agency-skill') }}",
            type: "get",
            data: {
                'agency_id': '{{ $agencyDetails->id}}',

            },
            success: function(res) {
                $('#loadertag1Skill').attr('style', 'display:none');
                if (res.data.response == 0) {
                    refreshSkill();
                } else {
                    toastr.success(res.error_msg);
                }


            },
            error: function(xhr, status, error) {

                toastr.error(xhr.responseJSON.error_msg);
            }

        })
    }


    function syncVisit() {
        $.ajax({
            url: "{{ url('sync-agency-visit') }}",
            type: "get",
            data: {
                'agency_id': '{{ $agencyDetails->id}}',

            },
            success: function(res) {



            },
            error: function(xhr, status, error) {

                toastr.error(xhr.responseJSON.error_msg);
            }

        })
    }

    function getTypeWiseService(existingId = "") {

        $.ajax({
            async: false,
            global: false,
            type: "GET",
            url: "{{ url('agency-ajax-service')}}",
            data: {
                "id": existingId,
            },
            success: function(res) {

                if (res != '') {
                    htmlsresp = res;
                } else {
                    htmlsresp += '<option value="">No record available</option>';
                }
                $('#service_id1').html("");
                $('#service_id1').html(htmlsresp);


            }
        })

    }

    function remoteRefreshEmployee() {

        $('#remote_loadertag1Employee').attr('style', '');
        $.ajax({
            url: "{{ url('refresh-agency-remote-employee') }}",
            type: "get",
            data: {
                'agency_id': '{{ $agencyDetails->id}}',

            },
            success: function(res) {
                $('#remote_loadertag1Employee').attr('style', 'display:none');
                toastr.success(res.error_msg);

            },
            error: function(jqr) {
                $('#remote_loadertag1Employee').attr('style', 'display:none');
                toastr.error(jqr.responseJSON.error_msg);
            }

        })

    }

    function loadFormSetupList(page) {
        $.ajax({
            method: 'GET',
            url: "{{ url('form-setup-list') }}" + "?agency_id={{ $id }}&page=" + page,
            data: {
                'id': "{{ $id }}",
                '_token': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $('#loadertag').show();
            },
            success: function success(response) {
                $('#loadertag').hide();
                $('#create_form_list').html("");
                $('#form_setup_list').html("");
                $('#form_setup_list').html(response);
            },
            error: function error(_error) {

                toastr.error('Something happened. Try again');
            }
        });
    }

    function loadFieldMasterList(page) {
        $.ajax({
            method: 'GET',
            url: "{{ url('field-master-list') }}" + "?agency_id={{ $id }}&page=" + page,
            data: {
                'id': "{{ $id }}",
                '_token': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $('#loadertag').show();
            },
            success: function success(response) {
                $('#loadertag').hide();
                $('#create_form_list').html("");
                $('#form_setup_list').html("");
                $('#create_form_list').html(response);
            },
            error: function error(_error) {

                toastr.error('Something happened. Try again');
            }
        });
    }
</script>



<!-- -- field  list js-- -->


<script>
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var editData = "{{ route('field-master.edit', 'id') }}";
    var storeNewFieldData = "{{ route('store-agency-master') }}";
</script>
<script>
    function resetForm() {
        $("#addFieldForm")[0].reset();
        $('#field_error').html("");
    }

    $("#addFieldModal").on("hidden.bs.modal", function() {
        resetForm();
    });

    $("#closeBtn").on("click", function() {
        resetForm();
    });

    $(".close").on("click", function() {
        resetForm();
    });
    $(document).on("click", ".deleteAgencyMaster", function() {
        var id = $(this).attr('data-did');
        var agency_id = $(this).attr('data-aid');
        deleteAgencyMaster(id, agency_id);
    });

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function deleteAgencyMaster(id, agency_id) {
        var upUrl = `{{ URL::to('agency-master-delete') }}/${id}`;
        Swal.fire({
            title: 'Are you sure?',
            text: "Field in use. still you want to delete this? ",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-danger ml-2 mt-2",
            buttonsStyling: false
        }).then((result) => {
            if (result.value) {
                $.ajax({

                    url: upUrl,
                    type: "POST",
                    data: {
                        'agency_id': agency_id,
                        '_token': "{{ csrf_token()}}"
                    },
                    success: function(response) {
                        if (response.status) {
                            $("#" + id).remove();
                            updateSort('#sortableTable');
                            saveOrderToDatabase();
                            toastr.success(response.msg);
                        } else {
                            toastr.error(response.msg);
                        }
                    }
                });
            } else {
                return false;
            }
        });
    }

    $('#submitFormId').on('click', function(event) {
        event.preventDefault();

        var $submitButton = $(this);
        $submitButton.prop('disabled', true);

        var isChecked = false;
        $('input[name="field_id[]"]').each(function() {
            if ($(this).is(':checked')) {
                isChecked = true;
                return false;
            }
        });

        if (!isChecked) {
            $('#field_error').html("Please select at least one field.");
            $submitButton.prop('disabled', false);
            return false;
        } else {
            $('#field_error').html("");
        }

        $.ajax({
            headers: {
                "X-CSRF-Token": $("meta[name=_token]").attr("content"),
            },
            url: storeNewFieldData,
            type: "POST",
            cache: false,
            data: $("#addFieldForm").serialize(),
            beforeSend: function() {},
            success: function(response) {

                $("#addFieldModal").modal("hide");
                $("#addFieldForm")[0].reset();
                $('#field_error').html("");
                var responseData = response.data;
                $(".hide-no-record").hide();
                loadFieldMasterList(1);

                toastr.success(response.msg);
                $submitButton.prop('disabled', false);
            },

            error: function(error) {
                $submitButton.prop('disabled', false);
                toastr.error(error.responseJSON.errors);
            }
        });
    })

    function loadFormSetupList(page) {
        $.ajax({
            method: 'GET',
            url: "{{ url('form-setup-list') }}" + "?agency_id={{ $id }}&page=" + page,
            data: {
                'id': "{{ $id }}",
                '_token': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $('#loadertag').show();
            },
            success: function success(response) {
                $('#loadertag').hide();
                $('#create_form_list').html("");
                $('#form_setup_list').html("");
                $('#form_setup_list').html(response);
            },
            error: function error(_error) {

                toastr.error('Something happened. Try again');
            }
        });
    }

    var _AGENCY_TOKEN_UPDATE_URL = "{{ url('token-update')}}";

    var Issms = $('.enable_hha').is(':checked') == true ? 1 : 0;
    if (Issms == 1) {
        $('.office_id_div').attr('style', '');
    } else {
        $('.office_id_div').attr('style', 'display:none');
    }

    $('#submit_form_id').submit(function(e) {
        var agency_notes_token = $('#agency_notes_token').val();
        $('#agency_notes_token_error').html("");
        var cnt = 0;
        if (agency_notes_token == "") {
            $('#agency_notes_token_error').html("Please select notes");
            cnt = 1;
        }

        if (cnt == 1) {
            return false;
        } else {
            return true;
        }
    })

    function showModalGenerate() {
        $('#agency_notes_token_error').html("");
        $('#agency_notes_token').val("");
    }

    function openEditPopup() {
        $('#app_detail_edit_modal').modal('show');
        var agencyName = $('#edit_app_name').val();
        var agencyToken = $('#edit_app_token').val();
        var agencyKey = $('#edit_app_key').val();
        $('#agency_edit_app_name').val(agencyName);
        $('#agency_edit_app_token').val(agencyToken);
        $('#agency_edit_app_key').val(agencyKey);
        $('#agency_edit_app_name_error').html("");
        $('#agency_edit_app_key_error').html("");
        $('#agency_edit_app_token_error').html("");
    }

    function saveEditDeatil() {
        var agencyName = $('#agency_edit_app_name').val();
        var agencyToken = $('#agency_edit_app_token').val();
        var agencyKey = $('#agency_edit_app_key').val();
        var Issms = $('.enable_hha').is(':checked') == true ? 1 : 0;
        var agencyId = $('#agency_id').val();
        error = 0;
        if (agencyName.trim() == "") {
            $('#agency_edit_app_name_error').html('Please enter App Name');
            error++;
        }
        if (agencyToken.trim() == "") {
            $('#agency_edit_app_token_error').html('Please enter App Key');
            error++;
        }
        if (agencyKey.trim() == "") {
            $('#agency_edit_app_key_error').html('Please enter App Secret');
            error++;
        }
        if (error == 0) {
            var forms = $('#editAppDetail')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');
            newForms.append('enable_hha', Issms);
            newForms.append('agency_id', '{{$agencyDetails->id }}');
            $.ajax({
                url: "{{ url('agency/hha-app-detail-update')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });
        }
    }

    function showHHAOffices() {

        $.ajax({
            async: false,
            global: false,
            type: "GET",
            url: "{{ url('hha-agency-office-list')}}",
            data: {
                'agency_id': '{{$agencyDetails->id }}',
            },
            success: function(res) {
                var json = res.data;

                var htmlResponse = "<option value=''>Select HHA Office</option>";
                if (res.data.length != 0) {

                    var existing_office_id = $('#office_id_resp').val();
                    $.each(json, function(i, v) {
                        var selected = "";
                        if (existing_office_id == v.id) {
                            selected = 'selected="selected"';
                        }
                        htmlResponse += "<option value='" + v.id + "' " + selected + ">" + v.office_name + " ( " + v.office_code + " )" + "</option>";
                    })
                    $('#office_id').val(existing_office_id)
                }
                $('#office_id').html("");
                $('#office_id').html(htmlResponse);

            }
        })
    }

    function saveOfficeDetails() {
        var officeId = $('#office_id').val();
        var office_name = "All";
        if (officeId != "") {
            office_name = $('#office_id option:selected').text();
        }
        $.ajax({
            url: "{{ url('agency/hha-office-detail-save')}}",
            type: "POST",
            data: {
                'office_id': $('#office_id').val(),
                'office_name': office_name,
                'agency_id': '{{$agencyDetails->id }}',
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                toastr.success(response.error_msg);
                $('.office_id_div').html(office_name);
                $('#office_id_resp').val(officeId);
                $('#close_office').click();
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }

    function syncHHAOffice() {

        $.ajax({
            url: "{{ url('hha-office')}}",
            type: "get",
            data: {

                'id': '{{$agencyDetails->id }}',

            },
            success: function(response) {
                toastr.success(response.error_msg);

            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }

    function closeSMSNotification() {
        if (selectedModal == 0) {
            $('.smsEnableDisabled').prop("checked", false)
        }
    }

    $(document).on("change", ".smsEnableDisabled", function() {
        var Issms = $(this).prop('checked') == true ? 1 : 0;
        var agencyId = $('#agency_id').val();
        var disabled = "disabled";
        if (Issms == 1) {
            var disabled = "enabled";
        }
        $.confirm({
            title: 'Are you sure?',
            content: 'you want to ' + disabled + ' SMS notifications',
            columnClass: "col-md-6",


            buttons: {
                formSubmit: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function() {
                        $.ajax({
                            type: "post",
                            dataType: "json",
                            url: '{{route("agencySmsStatus")}}',
                            data: {
                                'is_sms': Issms,
                                'agency_id': agencyId,
                                '_token': "{{ csrf_token()}}"
                            },
                            success: function(data) {
                                toastr.success(data.error_msg);
                                $('#is_sms_status').val(Issms)
                            }
                        });
                    }
                },
                cancel: function() {
                    //close
                    var lastStatus = $('#is_sms_status').val();
                    if (lastStatus == 1) {
                        $('.smsEnableDisabled').prop("checked", true);
                    } else {
                        $('.smsEnableDisabled').prop("checked", false);
                    }
                },
            },
        });

    });

    function changeRolePermission(userId, role_access) {
        var agencyId = $('#agency_id').val();
        var role_access_text = (role_access == 1) ? 'revoke access for this user' : 'grant access to this user'
        var userId = userId;
        $.confirm({
            title: 'Are you sure?',
            content: 'you want to ' + role_access_text,
            columnClass: "col-md-6",
            buttons: {
                formSubmit: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function() {
                        $.ajax({
                            type: "post",
                            dataType: "json",
                            url: '{{url("agency-user-wise-role")}}',
                            data: {
                                'role_access': role_access == 1 ? 0 : 1,
                                'agency_id': agencyId,
                                'userId': userId,
                                '_token': "{{ csrf_token()}}"
                            },
                            success: function(data) {
                                loadUserList(1)
                                toastr.success(data.error_msg);
                            }
                        });
                    }
                },
                cancel: function() {
                    if (role_access == 1) {
                        $('#role_access' + userId).prop('checked', true)
                    } else {
                        $('#role_access' + userId).prop('checked', false)
                    }
                },
            },
        });
    }

    function loadRateCrad() {
        $('.rateCardLoader').attr('style', 'display:');
        $('#rate_card_ajax_id').html("")
        $.ajax({
            url: '{{url("agency-rate-card-list")}}',
            type: "get",
            data: {
                agency_id: '{{$agencyDetails->id }}'
            },
            success: function(response) {
                $('.rateCardLoader').attr('style', 'display:none');
                $('#rate_card_ajax_id').html("")
                $('#rate_card_ajax_id').html(response);
            }
        });
    }

    function getRateCard() {
        $('#ratecardModal').modal('show');
        $('#ratecardModal').css({
            zIndex: '99999'
        })
        $("#service_error").html("");
        $("#service_id").val("").change();
        $("#amount").val("");
        $("#amount_error").html("");
    }

    function getEditRateCard(id) {
        $('#edit_id').val(id);
        $('#rateCardEditModal').modal('show');
        $('#rateCardEditModal').css({
            zIndex: '99999'
        })
        $("#service_edit_error").html("");
        $("#amount_edit_error").html("");
        $("#edit_rate_card_form")[0].reset();
        $("#edit_service_id").val("").change();
        $("#edit_amount").val("");
        getModalData(id);
    }

    function getModalData(id) {
        $.ajax({
            async: false,
            global: false,
            url: '{{ url("/rate-card-by-id") }}',
            type: "get",
            data: {
                'id': id
            },
            success: function(response) {
                var json = response.data;
                if (json) {
                    $('#edit_service_id').val(json.service_id).change();
                    $('#edit_amount').val(json.amount);
                }
            }
        })
    }

    function save() {
        var service = $("#add_service_id").val();
        var amount = $("#amount").val();
        $("#service_error").html("");
        $("#amount_error").html("");
        var cnt = 0;

        if (service == "") {
            $("#service_error").html("Please select Service");
            cnt = 1;
        }
        if (amount.trim() == "") {
            $("#amount_error").html("Please enter Amount");
            cnt = 1;
        }

        if (amount != "" && (amount == "." || amount <= 0)) {
            $("#amount_error").html("Please enter valid Amount");
            cnt = 1;
        }
        if (cnt == 0) {
            $("#rateCardSave").prop("disabled", true);
            var formData = new FormData($("#rate_card_form")[0]);
            formData.append('agency_id', '{{$agencyDetails->id }}');
            $.ajax({
                type: "POST",
                url: '{{ url("/rate-card") }}',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    toastr.success(res.error_msg);
                    $("#rate_card_form")[0].reset();
                    $("#rateCardSave").prop("disabled", false);
                    $("#ratecardModal").modal("hide");
                    $("#id").val("").change();
                    loadRateCrad();
                },
                error: function(jqXHR) {
                    $("#rateCardSave").prop("disabled", false);
                    toastr.error(jqXHR.responseJSON.error_msg);
                },
            });
        } else {
            return false;
        }
    }

    function update() {
        var service = $("#edit_service_id").val();
        var amount = $("#edit_amount").val();
        var id = $("#edit_id").val();
        $("#service_edit_error").html("");
        $("#amount_edit_error").html("");
        var cnt = 0;
        if (service == "") {
            $("#service_edit_error").html("Please select Service");
            cnt = 1;
        }
        if (amount.trim() == "") {
            $("#amount_edit_error").html("Please enter Amount");
            cnt = 1;
        }
        if (amount != "" && (amount == "." || amount <= 0)) {
            $("#amount_edit_error").html("Please enter valid Amount");
            cnt = 1;
        }
        if (cnt == 0) {
            $("#rateCardUpdate").prop("disabled", true);
            var formData = new FormData($("#edit_rate_card_form")[0]);
            formData.append('_token', $('input[name=_token]').val());
            formData.append('_method', 'PUT');
            formData.append('agency_id', '{{$agencyDetails->id }}');
            $.ajax({
                url: '{{ url("/rate-card") }}' + '/' + id,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    toastr.success(res.error_msg);
                    $("#edit_rate_card_form")[0].reset();
                    $("#rateCardUpdate").prop("disabled", false);
                    $("#rateCardEditModal").modal("hide");
                    $("#rate_id").val("").change();
                    loadRateCrad();
                },
                error: function(jqXHR) {
                    $("#rateCardUpdate").prop("disabled", false);
                    toastr.error(jqXHR.responseJSON.error_msg);
                },
            });
        } else {
            return false;
        }
    }

    function deleteRateCard(id) {
        if (id != '') {
            $.confirm({
                title: 'Are you sure?',
                content: 'you want to delete this record.',
                type: 'blue',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                global: false,
                                url: '{{ url("rate-card") }}' + '/' + id,
                                type: "DELETE",
                                data: {
                                    '_token': _CSRF_TOKEN
                                },
                                success: function(response) {
                                    toastr.success(response.error_msg);
                                    loadRateCrad();
                                },
                                error: function(xhr, status, error) {
                                    toastr.error(xhr.responseJSON.error_msg);
                                }
                            });
                        }
                    },
                    cancel: function() {}
                }
            })
        }
        return false;
    }

    $('#amount').on('keypress', function(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;

        // Allow only numbers (48-57) and single dot (46)
        if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {
            return false;
        }
        var maxLengthBeforeDot = 8; // Max digits before dot
        var maxLengthTotal = 10;
        // Check if the current value is valid
        var currentValue = $(this).val();
        // Check if currentValue is not undefined or null
        if (currentValue === undefined || currentValue === null) {
            currentValue = ''; // Assign an empty string if it's undefined or null
        }
        var decimalIndex = currentValue.indexOf('.');
        var beforeDotLength = decimalIndex === -1 ? currentValue.length : decimalIndex;
        var totalLength = currentValue.replace('.', '').length;
        if ((beforeDotLength >= maxLengthBeforeDot) || (totalLength > maxLengthTotal)) {
            return false; // Block invalid input
        }
        return true;
    });

    $('#amount').on('paste', function(e) {
        e.preventDefault();
    });

    $('#edit_amount').on('keypress', function(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;

        // Allow only numbers (48-57) and single dot (46)
        if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {
            return false;
        }

        var maxLengthBeforeDot = 8; // Max digits before dot
        var maxLengthTotal = 10;
        // Check if the current value is valid
        var currentValue = $(this).val();
        // Check if currentValue is not undefined or null
        if (currentValue === undefined || currentValue === null) {
            currentValue = ''; // Assign an empty string if it's undefined or null
        }
        var decimalIndex = currentValue.indexOf('.');
        var beforeDotLength = decimalIndex === -1 ? currentValue.length : decimalIndex;
        var totalLength = currentValue.replace('.', '').length;
        if ((beforeDotLength >= maxLengthBeforeDot) || (totalLength > maxLengthTotal)) {
            return false; // Block invalid input
        }
        return true;
    });

    $('#edit_amount').on('paste', function(e) {
        e.preventDefault();
    });

    $('.enable_hha').on('change', function() {

        var status = 0;
        if ($(this).is(':checked')) {
            status = 1;
        }

        var appName = "{{ $agencyDetails->app_name }}";
        var appToken = "{{ $agencyDetails->app_token }}";
        var appKey = "{{ $agencyDetails->app_key }}";
        if (status == 1 && appName == '' && appToken == "" && appKey == "") {
            openEditPopup();
        } else {
            $.ajax({
                url: '{{url("agency-hha-status")}}',
                type: "get",
                data: {
                    status: status,
                    'id': '{{$agencyDetails->id}}'
                },
                success: function(response) {
                    toastr.success(response.error_msg);
                    if (status == 1) {
                        $('#hide_show_hha_id').attr('style', '');
                    } else {
                        $('#hide_show_hha_id').attr('style', 'display:none');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            })
        }

    })
    $(document).on("click", ".created-user-email-notification", function() {
        $('#add-created-user-email-notification-popup').modal('show');
    });
</script>
<script>
    var TELEHEALTH_SERVICE = "{{url('telehealth-services')}}";
    var TELEHEALTH_SERVICE_LIST = "{{url('agency-tele-service-list')}}";
    var AGENCY_ID = "{{$agencyDetails->id }}";
    var AGENCY_TELE_SERVICE_BY_ID = "{{url('agency-tele-service-by-id') }}";
    var AJAX_SERVICE_CALL = "{{ url('agency-ajax-service')}}";
    var _AGENCY_SMS_SERVICE_BY_ID = "{{url('agency-sms-service-by-id') }}";
    var _DISABLED_AGENCY_WISE_SMS_SERVICES = "{{ url('disable-agency-wise-sms-service')}}";
    var AGENCY_HUB_RECORD_URL = '{{url("agency-hub-status-change")}}';
    var AGENCY_PAYMENT_REPORT_URL = '{{url("agency-payment-report-url")}}';
    var AGENCY_REPORTING_TOOL_URL = '{{url("agency-reporting-tool-url")}}';

    $('#user-notification-email-saveId').click(function(e){
        var user_creator_notification = $('.user_creator_notification:checked').length;
        $('#created_user_email_notification_error').html("");
        var finalChecked = [];
        var cnt =0;
        if(user_creator_notification ==0){
            $('#created_user_email_notification_error').html("Please select at least one checkbox");
            cnt =1;
        }else{
            $('.user_creator_notification:checked').each(function(e){
                finalChecked.push($(this).val())
            })
        }
        if(cnt ==1){
            return false;
        }else{
            $.ajax({
                url: '{{url("add-user-creator-email")}}',
                type: "post",
                data: {
                    'data':finalChecked,
                    '_token':"{{ csrf_token()}}",
                    'agency_id':"{{ $agencyDetails->id}}"
                },
                success: function(response) {
                    toastr.success(response.error_msg);
                    $('#userCreatorNotification')[0].reset()
                    $('#add-created-user-email-notification-popup').modal('hide');
                    loadUserEmailCreator();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            })
        }
    })
    function refreshUserCreatorNotification(){
        $('#userCreatorNotification')[0].reset()
        $('#created_user_email_notification_error').html("");
    }
    function loadUserEmailCreator(){
        $('.users-email-loader').attr('style','display:');
        $('#user_email_notification_ajax_id').html("");
        $.ajax({
            url: '{{url("list-user-creator-email")}}',
            type: "get",
            data: {
                'agency_id':"{{ $agencyDetails->id}}"
            },
            success: function(response) {
                $('.users-email-loader').attr('style','display:none');


               $('#user_email_notification_ajax_id').html(response)
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        })
    }
    function userEmailDelete(id){
        $.confirm({
            title: 'Delete',
            columnClass: "col-md-6",
            content: 'Are you sure delete record?',
            buttons: {
                formSubmit: {
                    text: 'Delete',
                    btnClass: 'btn-danger',
                    action: function() {
                        $.ajax({
                            url: '{{url("delete-user-creator-email")}}',
                            type: "post",
                            data: {
                                'id':id,
                                'agency_id':"{{ $agencyDetails->id }}",
                                '_token':"{{ csrf_token()}}"
                            },
                            success: function(response) {
                                toastr.success(response.error_msg);
                                loadUserEmailCreator();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error_msg);
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
    let populate = [];
    let nybestUserData = {!! json_encode($nybestUserData) !!};
    for (let i = 0; i < nybestUserData.length; i++) {
        if(nybestUserData[i].first_name != null){
            populate.push({
                id: nybestUserData[i].nybest_user_id,
                name: nybestUserData[i].first_name + ' ' + nybestUserData[i].last_name
            });
        }
    }
    $("#assign_nybest_user").tokenInput(_SEARCH_NYBEST_USER, {
        tokenLimit: null,
        preventDuplicates: true,
        zindex: 1060,
        prePopulate: populate,
        hintText: 'Type to search NyBest users',
        noResultsText: 'No users found',
        searchingText: 'Searching…',
        onAdd: function (item) {

        },
        onDelete: function (item) {

        },
        onReady: function() {
            setTimeout(function () {
                $(".token-input-dropdown").css({
                    "max-height": "180px",
                    "overflow-y": "auto",
                    "z-index": "999999",
                    "position": "fixed !important;",
                });
            }, 500);
        }
    });

    $(document).on("click", "#saveNydata", function() {
        $('#assign_nybest_user_error').html('');
        var ids = $('#assign_nybest_user').val();
        var $btn = $('#saveNydata');
        var prevText = $btn.text();
        $btn.prop('disabled', true).text('Updating...');
        $.ajax({
            type: "post",
            dataType: "json",
            url: '{{url("update-nybest-user-data")}}',
            data: {
                'nybest_user_id': ids,
                'agency_id': "{{ $agencyDetails->id }}",
                '_token':"{{ csrf_token()}}"
            },
            success: function(data) {
                toastr.success(data.error_msg);
                $('#nybest-user-modal').modal('hide');
                if(data.data != ""){
                    $('#nybest_edit_user_info').html('');
                    let html = "";
                    data.data.forEach(function(nydata, index) {
                        if(nydata.first_name != null){
                            html += '<span class="nybest-chip">' + nydata.first_name + ' ' + nydata.last_name + ' (' + nydata.email + ')</span>';
                        }
                    });
                    $('#nybest_edit_user_info').html(html);
                }
            },
            error: function(xhr){
                var msg = (xhr.responseJSON && xhr.responseJSON.error_msg) ? xhr.responseJSON.error_msg : 'Something went wrong';
                toastr.error(msg);
            },
            complete: function(){
                $btn.prop('disabled', false).text(prevText);
            }
        });
    });

</script>

<!-- Delete Agency with User Merge JavaScript -->
<script>
    var DELETE_AGENCY_URLS = {
        GET_USERS: "{{ url('agency/get-users-by-agency') }}",
        GET_ACTIVE_AGENCIES: "{{ url('agency/get-active-agencies') }}",
        MERGE_AND_DELETE: "{{ url('agency/merge-users-and-delete') }}",
        DIRECT_DELETE: "{{ url('agency/delete') }}"
    };

    var DELETE_AGENCY_MODULE = {
        currentAgencyId: null,
        selectedUsers: [],

        /**
         * Open delete agency modal
         */
        openModal: function(agencyId) {
            this.currentAgencyId = agencyId;
            this.resetModal();
            $('#delete_agency_id').val(agencyId);
            $('#deleteAgencyModal').modal('show');
        },

        /**
         * Reset modal to initial state
         */
        resetModal: function() {
            // Reset to step 1
            $('#step1_confirmation').show();
            $('#step2_merge_data').hide();
            $('#step3_direct_delete').hide();

            // Reset radio buttons
            $('input[name="merge_option"]').prop('checked', false);
            $('#continueBtn').prop('disabled', true);

            // Reset step 2
            $('#target_agency_select').val('').trigger('change');
            $('#usersListContainer').hide();
            $('#usersTableBody').empty();
            $('#mergeActionButtons').hide();

            this.selectedUsers = [];
        },

        /**
         * Initialize event handlers
         */
        init: function() {
            var self = this;

            // Radio button change - enable continue button
            $('input[name="merge_option"]').on('change', function() {
                $('#continueBtn').prop('disabled', false);
            });

            // Continue button click
            $('#continueBtn').on('click', function() {
                var selectedOption = $('input[name="merge_option"]:checked').val();
                if (selectedOption === 'yes') {
                    self.showMergeStep();
                } else if (selectedOption === 'no') {
                    self.showDirectDeleteStep();
                }
            });

            // Back buttons
            $('#backToStep1Btn, #backToStep1FromDeleteBtn').on('click', function() {
                self.resetModal();
            });

            // Target agency selection change
            $('#target_agency_select').on('change', function() {
                var targetAgencyId = $(this).val();
                if (targetAgencyId) {
                    self.loadUsers(self.currentAgencyId);
                } else {
                    $('#usersListContainer').hide();
                    $('#mergeActionButtons').hide();
                }
            });

            // Select all users checkbox
            $(document).on('change', '#selectAllUsers', function() {
                $('.user-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Individual user checkbox
            $(document).on('change', '.user-checkbox', function() {
                var totalCheckboxes = $('.user-checkbox').length;
                var checkedCheckboxes = $('.user-checkbox:checked').length;
                $('#selectAllUsers').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
            });

            // Confirm merge button
            $('#confirmMergeBtn').on('click', function() {
                self.confirmMerge();
            });

            // Confirm direct delete button
            $('#confirmDirectDeleteBtn').on('click', function() {
                self.confirmDirectDelete();
            });

            // Initialize Select2 when modal is shown
            $('#deleteAgencyModal').on('shown.bs.modal', function() {
                if (!$('#target_agency_select').hasClass('select2-hidden-accessible')) {
                    $('#target_agency_select').select2({
                        dropdownParent: $('#deleteAgencyModal'),
                        placeholder: '-- Select Active Agency --',
                        width: '100%'
                    });
                }
            });

            // Reset on modal hide
            $('#deleteAgencyModal').on('hidden.bs.modal', function() {
                self.resetModal();
            });
        },

        /**
         * Show merge step (Step 2)
         */
        showMergeStep: function() {
            $('#step1_confirmation').hide();
            $('#step2_merge_data').show();
            this.loadActiveAgencies();
        },

        /**
         * Show direct delete step (Step 3)
         */
        showDirectDeleteStep: function() {
            $('#step1_confirmation').hide();
            $('#step3_direct_delete').show();
        },

        /**
         * Load active agencies for dropdown
         */
        loadActiveAgencies: function() {
            var self = this;
            $.ajax({
                url: DELETE_AGENCY_URLS.GET_ACTIVE_AGENCIES,
                type: 'GET',
                data: {
                    exclude_agency_id: self.currentAgencyId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        var options = '<option value="">-- Select Active Agency --</option>';
                        response.agencies.forEach(function(agency) {
                            options += '<option value="' + agency.id + '">' + agency.agency_name + '</option>';
                        });
                        $('#target_agency_select').html(options);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading active agencies');
                }
            });
        },

        /**
         * Load users by agency
         */
        loadUsers: function(agencyId) {
            var self = this;
            $('#usersLoadingIndicator').show();
            $('#usersListContainer').hide();
            $('#mergeActionButtons').hide();

            $.ajax({
                url: DELETE_AGENCY_URLS.GET_USERS,
                type: 'GET',
                data: {
                    agency_id: agencyId
                },
                success: function(response) {
                    $('#usersLoadingIndicator').hide();
                    if (response.status === 'success') {
                        if (response.users.length > 0) {
                            self.renderUsersList(response.users);
                            $('#usersListContainer').show();
                            $('#mergeActionButtons').show();
                        } else {
                            toastr.info('No users found for this agency');
                            $('#usersListContainer').hide();
                            $('#mergeActionButtons').hide();
                        }
                    }
                },
                error: function(xhr) {
                    $('#usersLoadingIndicator').hide();
                    toastr.error('Error loading users');
                }
            });
        },

        /**
         * Render users list in table
         */
        renderUsersList: function(users) {
            var html = '';
            users.forEach(function(user) {
                html += '<tr>';
                html += '<td class="text-center">';
                html += '<input type="checkbox" class="user-checkbox" value="' + user.id + '" data-user-id="' + user.id + '">';
                html += '</td>';
                html += '<td>' + user.name + '</td>';
                html += '<td>' + (user.email || 'N/A') + '</td>';
                html += '<td class="text-center">';
                html += '<input type="checkbox" class="domain-checkbox" data-user-id="' + user.id + '">';
                html += '</td>';
                html += '</tr>';
            });
            $('#usersTableBody').html(html);
        },

        /**
         * Confirm merge operation
         */
        confirmMerge: function() {
            var self = this;
            var targetAgencyId = $('#target_agency_select').val();

            if (!targetAgencyId) {
                toastr.error('Please select a target agency');
                return;
            }

            // Get selected users
            var selectedUsers = [];
            $('.user-checkbox:checked').each(function() {
                var userId = $(this).data('user-id');
                var createDomain = $('.domain-checkbox[data-user-id="' + userId + '"]').prop('checked');
                selectedUsers.push({
                    user_id: userId,
                    create_domain: createDomain ? 1 : 0
                });
            });

            if (selectedUsers.length === 0) {
                toastr.warning('Please select at least one user to merge');
                return;
            }

            // Show confirmation dialog
            $.confirm({
                title: 'Confirm Merge & Delete',
                content: 'Are you sure you want to merge ' + selectedUsers.length + ' user(s) and delete this agency?',
                type: 'orange',
                buttons: {
                    confirm: {
                        text: 'Yes, Proceed',
                        btnClass: 'btn-danger',
                        action: function() {
                            self.performMerge(targetAgencyId, selectedUsers);
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-secondary'
                    }
                }
            });
        },

        /**
         * Perform merge operation
         */
        performMerge: function(targetAgencyId, selectedUsers) {
            var self = this;
            $('#confirmMergeBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Processing...');

            $.ajax({
                url: DELETE_AGENCY_URLS.MERGE_AND_DELETE,
                type: 'POST',
                data: {
                    agency_id: self.currentAgencyId,
                    target_agency_id: targetAgencyId,
                    users: selectedUsers,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('#deleteAgencyModal').modal('hide');

                        // Redirect after 1.5 seconds
                        setTimeout(function() {
                            window.location.href = "{{ url('agency') }}";
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'An error occurred');
                        $('#confirmMergeBtn').prop('disabled', false).html('<i class="mdi mdi-check"></i> Confirm & Delete Agency');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'An error occurred while processing the request';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                    $('#confirmMergeBtn').prop('disabled', false).html('<i class="mdi mdi-check"></i> Confirm & Delete Agency');
                }
            });
        },

        /**
         * Confirm direct delete
         */
        confirmDirectDelete: function() {
            var self = this;
            $('#confirmDirectDeleteBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Deleting...');

            $.ajax({
                url: DELETE_AGENCY_URLS.DIRECT_DELETE + '/' + self.currentAgencyId,
                type: 'GET',
                success: function(response) {
                    toastr.success('Agency deleted successfully');
                    $('#deleteAgencyModal').modal('hide');

                    // Redirect after 1.5 seconds
                    setTimeout(function() {
                        window.location.href = "{{ url('agency') }}";
                    }, 1500);
                },
                error: function(xhr) {
                    var errorMessage = 'An error occurred while deleting the agency';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                    $('#confirmDirectDeleteBtn').prop('disabled', false).html('<i class="mdi mdi-delete"></i> Yes, Delete Agency');
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        DELETE_AGENCY_MODULE.init();
    });

    // Global function to open modal (called from onclick)
    function openDeleteAgencyModal(agencyId) {
        DELETE_AGENCY_MODULE.openModal(agencyId);
    }

    function openEditMDOPopup() {
        $('#hha_mdo_edit_modal').modal('show');
        $('#agency_hha_client_id_error').html("");
        $('#agency_hha_client_secret_error').html("")
        $('#agency_hha_app_key_error').html("")
        $('#agency_hha_client_txt_id_error').html("")

        $('#agency_hha_client_id').val($('#value_client_id').val());
        $('#agency_hha_client_secret').val($('#value_client_secret').val());
        $('#agency_hha_app_key').val($('#value_api_token').val());
        $('#agency_hha_client_txt_id').val($('#value_txtID').val());
    }

    function saveHHAMDODetail(){
        var agency_hha_client_id = $('#agency_hha_client_id').val();
        var agency_hha_client_secret = $('#agency_hha_client_secret').val();
        var agency_hha_app_key = $('#agency_hha_app_key').val();
        var agency_hha_client_txt_id = $('#agency_hha_client_txt_id').val();
        $('#agency_hha_client_id_error').html('');
        $('#agency_hha_client_secret_error').html('');
        $('#agency_hha_app_key_error').html('');
        $('#agency_hha_client_txt_id_error').html('');
        $('#spn-agency-hha-mdo-detail').removeClass('d-none');
        $('#btn-update-hha-mdo-text').text('Updating ...')
        var cnt = 0;

        if(agency_hha_client_id.trim() == ''){
            $('#agency_hha_client_id_error').html('Please enter Client ID');
            cnt = 1;
        }

        if(agency_hha_client_secret.trim() == ''){
            $('#agency_hha_client_secret_error').html('Please enter Client Secret');
            cnt = 1;
        }
        if(agency_hha_app_key.trim() == ''){
            $('#agency_hha_app_key_error').html('Please enter App Key');
            cnt = 1;
        }
        if(agency_hha_client_txt_id.trim() == ''){
            $('#agency_hha_client_txt_id_error').html('Please enter TXT ID');
            cnt = 1;
        }
        if(cnt == 1){
            $('#spn-agency-hha-mdo-detail').addClass('d-none');
            $('#btn-update-hha-mdo-text').text('Update')
            return false;
        } else {
            var formData = new FormData($('#editHHAMDOAppDetail')[0]);
            formData.append('_token', _CSRF_TOKEN);
            formData.append('agency_id','{{ $agencyDetails->id}}');
            $.ajax({
                type: "POST",
                url: _UPDATE_HHA_MDO_ORDER_DETAILS,
                data: formData,
                contentType: false,
                processData: false,
                success: function(res){
                    toastr.success(res.error_msg)
                    console.log(res.data)
                    $('#mdo_html_client_id').html(res.data.client_id)
                    $('#mdo_html_client_secret').html(res.data.client_secret)
                    $('#mdo_html_api_token').html(res.data.api_token)
                    $('#mdo_html_txtID').html(res.data.txtID);
                    $('.enable_hha_mdo').prop("checked",false)
                    if(res.data.is_status ==1){
                        $('.enable_hha_mdo').prop("checked",true)
                    }

                    $('#value_client_id').val(res.data.client_id);
                    $('#value_client_secret').val(res.data.client_secret);
                    $('#value_api_token').val(res.data.api_token);
                    $('#value_txtID').val(res.data.txtID);
                    $('#value_toogles').val(res.data.is_status);

                    $('.close').click();
                    $('#spn-agency-hha-mdo-detail').addClass('d-none');
                    $('#btn-update-hha-mdo-text').text('Update')
                },
                error: function (jqXHR) {
                    $('#spn-agency-hha-mdo-detail').addClass('d-none');
                    $('#btn-update-hha-mdo-text').text('Update')
                    toastr.error(jqXHR.responseJSON.error_msg);
                },
            })
        }
    }

    $('.enable_hha_mdo').on('change', function() {

        var status = 0;
        var msg = "disabled";
        if ($(this).is(':checked')) {
            status = 1;
            msg = "enabled";
        }

        var appName = $('#value_client_id').val();
        var appToken = $('#value_client_secret').val();
        var appKey = $('#value_api_token').val();
        var mdo_txtID =$('#value_txtID').val();
        if (status == 1 && appName == '' && appToken == "" && appKey == "" && mdo_txtID =="") {
            openEditMDOPopup();
        } else {
            $.confirm({
                title: "Are you sure?",
                content:"You want to "+msg+" the HHA MDO credentials?",
                type: 'blue',
                columnClass: 'col-md-6',
                buttons: {
                    submit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function () {
                            $.ajax({
                                type:"POST",
                                url:_DISABLED_HHA_MDO_CREDENTIAL,
                                data:{
                                    '_token':_CSRF_TOKEN,
                                    'id':"{{ $agencyDetails->id}}",
                                    status: status,
                                },
                                success:function(res){
                                    toastr.success(res.error_msg)


                                },
                                error:function(jqr){
                                    toastr.error(jqr.responseJSON.error_msg)

                                }
                            })
                        }
                    },
                    cancel: {
                        text: 'Cancel',

                    }
                }
            });
        }
    })

    $('#syncVisitingPendingMedical').click(function(){
        $.confirm({
                title: "Are you sure?",
                content:"You want to sync visiting pending medical?",
                type: 'blue',
                columnClass: 'col-md-6',
                buttons: {
                    submit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function () {
                            $.ajax({
                                type:"POST",
                                url:_SYNC_VISITING_PENDING_MEDICAL,
                                data:{
                                    '_token':_CSRF_TOKEN,
                                    'id':"{{ $agencyDetails->id}}",

                                },
                                success:function(res){
                                    toastr.success(res.error_msg)


                                },
                                error:function(jqr){
                                    toastr.error(jqr.responseJSON.error_msg)

                                }
                            })
                        }
                    },
                    cancel: {
                        text: 'Cancel',

                    }
                }
            });
    })
</script>

<script src="{{ asset('assets/modulejs/agency/telehealth.js') }}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/agency/sms_service.js') }}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/agency/hub_record.js') }}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/agency/date_wise_agency_access.js') }}?time={{ env('timestamp')}}"></script>

<script>
var _AGENCY_ID_FOR_NOTES = {{ $id }};
var _CSRF_TOKEN_NOTES = "{{ csrf_token() }}";
var _CAN_NOTES_TOGGLE = {{ auth()->user()->can('agency-notes-toggle') ? 'true' : 'false' }};
var _CAN_NOTES_DELETE = {{ auth()->user()->can('agency-notes-delete') ? 'true' : 'false' }};
var _CAN_NOTES_LIST = {{ auth()->user()->can('agency-notes-list') ? 'true' : 'false' }};
$(document).ready(function() {
    if (_CAN_NOTES_LIST){

        loadAgencyNotes();
    }

});

function openAgencyNoteModal() {
    $('#agency_note_text').val('');
    $('#agency_note_type').val('info');
    $('#agency_note_error').html('');
    $('#agencyNoteModal').modal('show');
}

function saveAgencyNote() {
    var note = $('#agency_note_text').val().trim();
    var note_type = $('#agency_note_type').val();

    $('#agency_note_error').html('');
    if (note === '') {
        $('#agency_note_error').html('Note is required.');
        return;
    }

    $('#saveAgencyNoteBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');

    $.ajax({
        type: 'POST',
        url: '{{ url("agency-note-add") }}',
        data: {
            agency_id: _AGENCY_ID_FOR_NOTES,
            note: note,
            note_type: note_type,
            _token: _CSRF_TOKEN_NOTES
        },
        success: function(data) {
            $('#agencyNoteModal').modal('hide');
            toastr.success(data.error_msg);
            loadAgencyNotes();
        },
        error: function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.error_msg ? xhr.responseJSON.error_msg : 'Something went wrong.';
            $('#agency_note_error').html(msg);
        },
        complete: function() {
            $('#saveAgencyNoteBtn').prop('disabled', false).html('<i class="mdi mdi-content-save"></i> Save Note');
        }
    });
}

function loadAgencyNotes() {
    $.ajax({
        type: 'GET',
        url: '{{ url("agency-notes-all") }}',
        data: { agency_id: _AGENCY_ID_FOR_NOTES },
        success: function(data) {
            renderAgencyNotes(data.data);
        },
        error: function() {
            toastr.error('Failed to load agency notes.');
        }
    });
}

function renderAgencyNotes(notes) {
    var wrapper = $('#agency-notes-list-wrapper');
    if (!notes || notes.length === 0) {
        wrapper.html('<p class="text-muted" id="agency-notes-empty">No notes added yet.</p>');
        return;
    }

    var typeMap = {
        'info':    { cls: 'alert-info',    icon: 'mdi-information',    label: 'Info' },
        'warning': { cls: 'alert-warning', icon: 'mdi-alert',          label: 'Warning' },
        'danger':  { cls: 'alert-danger',  icon: 'mdi-alert-circle',   label: 'Alert' }
    };

    var html = '';
    $.each(notes, function(i, n) {
        var t = typeMap[n.note_type] || typeMap['info'];
        var date = '';
        if (n.created_at) {
            var d = new Date(n.created_at);
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var dd = String(d.getDate()).padStart(2, '0');
            var yyyy = d.getFullYear();
            var hh = d.getHours();
            var min = String(d.getMinutes()).padStart(2, '0');
            var ampm = hh >= 12 ? 'PM' : 'AM';
            hh = hh % 12 || 12;
            date = mm + '/' + dd + '/' + yyyy + ' ' + String(hh).padStart(2, '0') + ':' + min + ' ' + ampm;
        }
        var isActive = n.is_active == 1;
        var sliderBg  = isActive ? '#28a745' : '#ccc';
        var knobLeft  = isActive ? '18px' : '2px';
        var rowOpacity = isActive ? '1' : '0.5';
        html += '<div class="agency-note-alert ' + t.cls + ' mb-2 py-2 px-3" role="alert" id="agency-note-row-' + n.id + '" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:nowrap;opacity:' + rowOpacity + ';">';

        // Left: icon + text
        html += '<div style="flex:1;min-width:0;">';
        html += '<i class="mdi ' + t.icon + ' mr-1"></i><strong>' + t.label + ':</strong> ' + $('<div>').text(n.note).html();
        html += '<br><small class="text-muted">Added by ' + (n.created_by_name || 'N/A') + ' on ' + date + '</small>';
        html += '</div>';

        // Right: toggle + delete
        html += '<div style="display:flex;align-items:center;flex-shrink:0;margin-left:16px;gap:10px;">';

        
        // Toggle (only if permitted)
        if (_CAN_NOTES_TOGGLE) {
            html += '<div id="toggle-wrap-' + n.id + '" onclick="toggleAgencyNote(' + n.id + ')" title="' + (isActive ? 'Active — click to deactivate' : 'Inactive — click to activate') + '" style="cursor:pointer;width:40px;height:22px;background:' + sliderBg + ';border-radius:22px;position:relative;transition:background 0.2s;flex-shrink:0;">';
            html += '<span id="toggle-knob-' + n.id + '" style="display:block;width:18px;height:18px;background:#fff;border-radius:50%;position:absolute;top:2px;left:' + knobLeft + ';transition:left 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.3);"></span>';
            html += '</div>';
        }

        // Delete (only if permitted)
        if (_CAN_NOTES_DELETE) {
            html += '<a href="javascript:void(0);" onclick="deleteAgencyNote(' + n.id + ')" title="Delete Note"><i class="mdi mdi-close-circle" style="color:red;font-size:20px;"></i></a>';
        }

        html += '</div>';
        html += '</div>';
    });
    wrapper.html(html);
}

function deleteAgencyNote(noteId) {
    $.confirm({
        title: 'Delete Note',
        content: 'Are you sure you want to delete this note?',
        columnClass: 'col-md-6',
        type: 'red',
        buttons: {
            confirm: {
                text: 'Delete',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("agency-note-delete") }}',
                        data: { note_id: noteId, _token: _CSRF_TOKEN_NOTES },
                        success: function(data) {
                            toastr.success(data.error_msg);
                            $('#agency-note-row-' + noteId).remove();
                            if ($('#agency-notes-list-wrapper .agency-note-alert').length === 0) {
                                $('#agency-notes-list-wrapper').html('<p class="text-muted">No notes added yet.</p>');
                            }
                        },
                        error: function() {
                            toastr.error('Failed to delete note.');
                        }
                    });
                }
            },
            cancel: function() {}
        }
    });
}

function toggleAgencyNote(noteId) {
    var isCurrentlyActive = $('#toggle-wrap-' + noteId).css('background-color') !== 'rgb(204, 204, 204)';
    var actionLabel = isCurrentlyActive ? 'Deactivate' : 'Activate';
    var actionMsg   = isCurrentlyActive ? 'Are you sure you want to deactivate this note?' : 'Are you sure you want to activate this note?';
    $.confirm({
        title: actionLabel + ' Note',
        content: actionMsg,
        columnClass: 'col-md-6',
        type: isCurrentlyActive ? 'orange' : 'green',
        buttons: {
            confirm: {
                text: actionLabel,
                btnClass: isCurrentlyActive ? 'btn-warning' : 'btn-success',
                action: function() {
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("agency-note-toggle") }}',
                        data: { note_id: noteId, _token: _CSRF_TOKEN_NOTES },
                        success: function(data) {
                            toastr.success(data.error_msg);
                            var row  = $('#agency-note-row-' + noteId);
                            var wrap = $('#toggle-wrap-' + noteId);
                            var knob = $('#toggle-knob-' + noteId);
                            if (data.is_active == 1) {
                                row.css('opacity', '1');
                                wrap.css('background', '#28a745');
                                knob.css('left', '18px');
                                wrap.attr('title', 'Active — click to deactivate');
                            } else {
                                row.css('opacity', '0.5');
                                wrap.css('background', '#ccc');
                                knob.css('left', '2px');
                                wrap.attr('title', 'Inactive — click to activate');
                            }
                        },
                        error: function(jqr) {
                            showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel: function() {}
        }
    });
}


</script>