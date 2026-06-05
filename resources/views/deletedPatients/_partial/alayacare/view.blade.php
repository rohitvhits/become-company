@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<style>
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
        /* overflow: hidden; */
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    h6.fm_1 {
        /* text-align: end;*/
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
</style>
<!--main-container-part-->

<div class="main-panel">
    <div class="content-wrapper custom-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">Agency # <?= $agencyDetails->id . " - " . ucwords($agencyDetails->agency_name) . " " ?> </h4>
                    <!-- <button class="btn btn-inverse-info tx-12 btn-sm btn-rounded mx-3">Enterprise</button>
                <div class="d-none d-md-flex">
                  <p class="text-muted mb-0 tx-13 cursor-pointer">Home</p>
                  <i class="mdi mdi-chevron-right text-muted"></i>
                  <p class="text-muted mb-0 tx-13 cursor-pointer">Dashboard</p>
                </div> -->
                </div>
                <!--  <div class="button-wrapper d-flex align-items-center mt-md-3 mt-xl-0">
               
                <a href="<?php echo URL::asset("/"); ?>agency/edit/<?= $id ?>" class="btn btn-primary btn-sm mr-3 d-none d-md-block">Edit</a>
                <a href="<?php echo URL::asset("/"); ?>agency/delete/<?= $id ?>" class="btn btn-outline-primary btn-sm  d-none d-md-block" onclick="return confirm('Are you sure remove this record?')" >Delete</a>
              </div> -->
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
                                <a href="javascript:void(0);" class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1" onclick="deleteRecordAgencies('{{$id}}')" title="Delete"><i class="mdi mdi-delete"></i>Delete</a>
                                @endcan

                                @can('agency-edit')
                                <a href="<?php echo URL::asset("/"); ?>agency/edit/<?= $id ?>" class="btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1" title="Edit"><i class="mdi mdi-pencil"></i>Edit</a>
                                @endcan
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="profile-feed">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <!-- <h5>Agency Details</h5> -->

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
                                            </dl>
                                        </div>
                                        <div class="col-md-4">
                                            <!-- <h5>Agency Details</h5> -->

                                            <dl class="dl-horizontal agency-detail1">
                                                <dt> Notification Email</dt>
                                                <dd><?= ($agencyDetails->notification_email != '') ? str_replace(',', '<br>', $agencyDetails->notification_email) : '-'; ?></dd>

                                                <dt>Agency Notification Email <br />For document and status update</dt>
                                                <dd><?= ($agencyDetails->nybest_email_notification != '') ? str_replace(',', '<br>', $agencyDetails->nybest_email_notification) : '-'; ?></dd>

                                                <dt> Other Email</dt>
                                                <dd><?= ($agencyDetails->other_email != '') ? str_replace(',', '<br>', $agencyDetails->other_email) : '-'; ?></dd>

                                                <dt> Notes Email</dt>
                                                <dd><?= ($agencyDetails->notes_email_notification != '') ? str_replace(',', '<br>', $agencyDetails->notes_email_notification) : '-'; ?></dd>
                                                <dt>Sent SMS</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="is_sms" class="smsEnableDisabled" {{ $agencyDetails->is_sms!= 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>

                                                


                                            </dl>

                                        </div>
                                        <div class="col-md-4">
                                            <dt> Agency Logo</dt>
                                            <dd>
                                                <form id="agency-logo-form" enctype="multipart/form-data">

                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                                                    <div id="logo-container">
                                                        @if($agencyDetails->agency_logo !="")
                                                        @php
                                                        $logo=$agencyDetails->agency_logo;
                                                        @endphp
                                                        @else
                                                        @php
                                                        $logo='default.png';
                                                        @endphp
                                                        @endif
                                                        <img id="agency-logo" src="{{ asset('allupload/' . $logo) }}" style="height: 76px;width: 145px;border-radius: 5px;" alt="Logo">

                                                    </div>
                                                    <input type="file" name="agency-image" id="image-upload" style="display:none;">
                                                    <span id="image-error" style="color:red"></span>
                                                </form>
                                            <dd>
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
                                                    <!-- <h5>Agency Details</h5> -->

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
                        <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users-1" role="tab" aria-controls="users-1" aria-selected="false">Users List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="agency-tab" data-toggle="tab" href="#agency-1" role="tab" aria-controls="agency-1" aria-selected="true" onclick="getData(1);">Agency Logs</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="token-tab" data-toggle="tab" href="#token-1" role="tab" aria-controls="token-1" token-selected="false">Generate Token</a>
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
                        <a class="nav-link" id="agency_wise_sms_list-tab" data-toggle="tab" href="#agency-wise-sms-list-1" role="tab" aria-controls="agency-wise-sms-list-1" aria-selected="false">SMS Template</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="agency_wise_sms_service-tab" data-toggle="tab" href="#agency-wise-service-1" onclick="getService(1)" role="tab" aria-controls="agency-wise-service-1" aria-selected="false">Agency Wise Service</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="alayacare-tab" data-toggle="tab" href="#alayacare-1" onclick="getAllSkills(1)" role="tab" aria-controls="alayacare-1" aria-selected="false">AlayaCare</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="remote_focus-tab" data-toggle="tab" href="#remote-focus-1"  role="tab" aria-controls="remote-focus-1" aria-selected="false">Remote Focus</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="users-1" role="tabpanel" aria-labelledby="users-tab">
                        <div class="row">
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
                                <a href="javascript:void(0)" onclick="getTokenGenerate()" class="btn btn-success  btn-rounded btn-sm btn-fw pull-right"><i class="mdi mdi-plus"></i>Generate Token</a>
                                @endcan
                            </div>

                        </div>
                        <div class="table-responsive">


                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <table id="" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Token</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($generate_token_details->token) && $generate_token_details->token != '') { ?>
                                        <tr>
                                            <td>1</td>
                                            <td><?php echo $generate_token_details->token; ?></td>
                                        </tr>

                                    <?php } else { ?>
                                        <tr>

                                            <td colspan="2">No record available</td>
                                        </tr>
                                    <?php } ?>

                                </tbody>
                            </table>


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
                        <div class="row">

                            <h4 class="card-title">HHA Detail</h4>
                            <div class="col-sm-6 pull-right">


                            </div>


                        </div>
                        <div class="row">
                            <div class="col-md-6">

                                <dl class="dl-horizontal agency-detail1">
                                    <dt>App Name</dt>
                                    <dd> {{ ($agencyDetails->app_name)?substr ($agencyDetails->app_name, -4):'-' }} </dd>

                                    <dt> App Key</dt>
                                    <dd> {{ ($agencyDetails->app_key)?substr ($agencyDetails->app_key, -4):'-' }} </dd>
                                    <dt> App Token</dt>
                                    <dd> {{ ($agencyDetails->app_token)?substr ($agencyDetails->app_token, -4):'-' }} </dd>
                                    <dt>Enabled HHA</dt>
                                    <dd> <label class="toggle-switch toggle-switch-success">
                                            <input type="checkbox" name="enable_hha" class="enable_hha" {{ $agencyDetails->enable_hha != 0 ? 'checked' : ''}}>
                                            <span class="toggle-slider round"></span>
                                        </label> </dd>
                                </dl>
                            </div>

                        </div>
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

                    <div class="tab-pane fade" id="agency-wise-sms-list-1" role="tabpanel" aria-labelledby="1)agency_wise_sms_list-tab">
                        <div class="row">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">SMS Template</h4>
                            </div>

                        </div>
                        <div class="">
                            <div class="row">
                                <div class="form-group">
                                    You can use below tags :start_date,start_time,end_time,url,link,patient_first_name,agency_name,namearray,
                                </div>
                            </div>
                            <form class="forms-sample" enctype="multipart/form-data" action='' name="add-agency-wise-sms-form" method="post" id="add-agency-wise-sms-form">
                                <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="col-form-label"><b>App Appointment SMS English</b></label>
                                        <textarea class="form-control form-control-lg" name="send_sms_eng" id="send_sms_eng">{{$agencyDetails->send_sms_eng}}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label"><b>App Appointment SMS Spanish</b></label>
                                        <textarea class="form-control form-control-lg" name="send_sms_spanish" id="send_sms_spanish">{{$agencyDetails->send_sms_spanish}}</textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="col-form-label"><b>Reminder SMS English</b></label>
                                        <textarea class="form-control form-control-lg" name="appointment_send_book_eng" id="appointment_send_book_eng">{{$agencyDetails->appointment_send_book_eng}}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label"><b>Reminder SMS Spanish</b></label>
                                        <textarea class="form-control form-control-lg" name="appointment_send_book_spanish" id="appointment_send_book_spanish">{{$agencyDetails->appointment_send_book_spanish}}</textarea>
                                    </div>
                                </div>
                                <span id="agency_wise_sms_message_error" class="error mt-2" for="document_type"></span>
                                <div class="modal-footer">
                                    <button type="button" id="agency-wise-sms-saveId" class="btn btn-success">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="agency-wise-service-1" role="tabpanel" aria-labelledby="1)agency_wise_sms_service-tab">
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
                        <div class="col-md-12">
                            <div class="row">

                                <h4 class="card-title">AlayaCare</h4>
                            </div>
                            <div class="row">
                                <div class="col-md-6">

                                    <dl class="dl-horizontal agency-detail1">
                                        <dt>AlayaCare User Name : </dt>
                                        <dd><span id="alaycare_username_id" >{{ ($agencyDetails->alaycare_status ==1 && $agencyDetails->alaycare_username !="")? $agencyDetails->alaycare_username:'N/A' }}</span> </dd>

                                        <dt>AlayaCare Password : </dt>
                                        <dd><span id="alaycare_password_id">{{ ($agencyDetails->alaycare_status ==1 && $agencyDetails->alaycare_password  !="")?$agencyDetails->alaycare_password:' N/A' }}</span> </dd>
                                        <dt>Enabled AlayaCare : </dt>
                                        <dd> <label class="toggle-switch toggle-switch-success">
                                                <input type="checkbox" name="alaycare-btn" class="alaycare-btn" {{ $agencyDetails->alaycare_status != 0 ? 'checked' : ''}}>
                                                <span class="toggle-slider round"></span>
                                            </label> </dd>
                                    </dl>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-sm-6 card-title">
                                    <h4 class="card-title">Skills</h4>
                                </div>
                                <div class="col-sm-6">
                                <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-fw btn-sm pull-right" onclick="addSkill()"><i class="mdi mdi-plus"></i> Add Skill</a>

                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive">
                            
                                    <table class="table" >
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="cboxid"></th>
                                                <th>No</th>
                                                <th>Branch Name</th>
                                                <th>Skill Name</th>
                                                <th>Category Name</th>
                                                
                                            
                                            </tr>
                                        </thead>
                                        <tbody id="alayacare_skill_response">


                                            
                                        </tbody>

                                    </table>

                                    <div class="pull-right pegination-margin">
                                    <a class="pull-right btn btn-primary btn-rounded  btn-sm" href="javascript:void(0)" id="nextSkillId" style="display:none"   onClick="nextSkill()">Next</a></li>
                                        <a class="pull-right btn btn-secondary btn-rounded  btn-sm" href="javascript:void(0)" id="previousSkillId" style="display:none" onClick="previousSkill()">Prev</a></li>
                                        

                                    </div>

                                </div>
                            </div>
                        </div>
                        
                        
                    </div>

                    <div class="tab-pane fade" id="remote-focus-1" role="tabpanel" aria-labelledby="remote_focus-tab">
                        <div class="col-md-12">
                            <div class="row">

                                <h4 class="card-title">Remote Focus</h4>
                            </div>
                            <div class="row">
                                <div class="col-md-6">

                                    <dl class="dl-horizontal agency-detail1">
                                        <dt>Remote Focus User Name : </dt>
                                        <dd><span id="remote_username_id" >{{ ($agencyDetails->robort_status ==1 && $agencyDetails->robort_user_name !="")? $agencyDetails->robort_user_name:'N/A' }}</span> </dd>

                                        <dt>Remote Focus Password : </dt>
                                        <dd><span id="remote_password_id">{{ ($agencyDetails->robort_status ==1 && $agencyDetails->robort_user_password  !="")?$agencyDetails->robort_user_password:' N/A' }}</span> </dd>
                                        <dt>Enabled Remote Focus : </dt>
                                        <dd> <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="robort-btn" class="robort-btn" {{ $agencyDetails->robort_status != 0 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label> </dd>
                                    </dl>
                                </div>

                            </div>
                            
                            
                        </div>
                        
                        
                    </div>
                    

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

    <div class="modal fade" id="add-notification-email-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title notification-emails" id="ModalLabel">Add Notification Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="resetNotificationEmail()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='' name="addnotificationemail" method="post" id="addnotificationemail">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                        <input type="hidden" id="notificationId" name="id" value="">

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label"><b>Email</b></label>
                            <br>
                            <input type="type" name="email" id="notificationEmail" value="" class="form-control email">
                            <span id="notifications_email_error" class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label"><b>Patient</b></label>
                            <br>
                            <div class="row">
                                @php $count = 0; @endphp
                                @if(!empty($agencyWiseNotificationEmail[0]))
                                @foreach($agencyWiseNotificationEmail as $item)
                                @if($count % 3 == 0 && $count > 0)
                            </div>
                            <div class="row">
                                @endif
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" id="patient_notification_email{{ $item->id }}" name="patient[]" value="{{ $item->name }}" data-id="{{ $item->id}}" class="notification_checkbox patient_checkbox">
                                        {{ $item->name }}
                                    </label>
                                </div>
                                @php $count++; @endphp
                                @endforeach
                                @endif

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label"><b>Caregiver</b><span style="color:red">*</span></label>
                            <br>
                            <div class="row">
                                @php $count = 0; @endphp
                                @if(!empty($agencyWiseNotificationEmail[0]))
                                @foreach($agencyWiseNotificationEmail as $item)
                                @if($count % 3 == 0 && $count > 0)
                            </div>
                            <div class="row">
                                @endif
                                <div class="col-md-4">
                                    <label>
                                        <input type="checkbox" id="caregiver_notification_email{{ $item->id }}" name="caregiver[]" data-id="{{ $item->id }}" value="{{ $item->name }}" class="notification_checkbox caregiver_checkbox">
                                        {{ $item->name }}
                                    </label>
                                </div>
                                @php $count++; @endphp
                                @endforeach
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label"><b>Services</b></label>

                            <div class="row">
                                <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">
                                    <option value="">Select Service</option>
                                </select>
                                <span id="service_id_error" class="error mt-2"></span>
                            </div>
                        </div>
                        <span id="notification_email_error" class="error"></span>

                        <div class="modal-footer">
                            <button type="button" id="notification-email-saveId" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal" onclick="resetNotificationEmail()">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="add-agency-wise-sms-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="SmsLable"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='' name="add-agency-wise-sms-form" method="post" id="add-agency-wise-sms-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
    <input type="hidden" name="id" value="" id="smsMId">

    <div class="form-group ">
        <label for="recipient-name" class="col-form-label">Type<span style="color:red">*</span></label>
        <br>
        <input type="text" name="agency_wise_sms_type" id="agency_wise_sms_type" class="form-control" placeHolder="Enter Type">
        <span id="agency_wise_sms_type_error" class="error mt-2" for="document_type"></span>
    </div>
    <span>You Can below tags<br>GENDER, ADDRESS, SKILLS, COORDNATORNAME, COORDNATORNUMBER, ADDITIONNOTE, CASEID, EFFECTIVEDATE, CASELANGUAGE</span>
    <div class="form-group">
        <label for="recipient-name" class="col-form-label">Message<span style="color:red">*</span></label>
        <br>

        <textarea class="form-control form-control-lg" name="agency_wise_sms_message" id="agency_wise_sms_message"></textarea>
        <span id="agency_wise_sms_message_error" class="error mt-2" for="document_type"></span>
    </div>

    <div class="modal-footer">
        <button type="button" id="agency-wise-sms-saveId" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
    </div>
    </form>
</div>

</div>
</div>
</div> --}}
<!-- add domain -->

<!-- block by country -->
<!-- <div class="content-wrapper custom-wrapper">
        <div class="card">
            <div class="row list-name m-3">
                <div class="col-sm-6 card-title">
                    <h4 class="card-title">Blocked By Country</h4>
                </div>
                <div class="col-sm-3 offset-sm-3">
                   
                    <button type="submit" id="status" class="btn btn-success btn-rounded btn-sm btn-fw pull-right">Change</button>
               
                </div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <span id="country_blocked_list"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div> -->



<!-- country block modal start -->
<!-- <div class="modal fade countryBlock" id="countryBlock" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Country Block</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency-country-save")}}' name="adduser" method="post" id="submitCountry">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="agency_id" value="{{ $id }}" id="agency_id">
                        <input type="hidden" name="id" value="" id="mid">
                        <div class="row pl-21 pb-2">
                            <div class="col-md-4">
                                <input type="checkbox" class="form-check-input" id="allCountryCheck" name="allCountryCheck" value="1">Select All
                            </div>
                            <div class="col-md-4">
                                <input type="checkbox" class="form-check-input" id="perCountryCheck" name="particular" value="1">Particular
                            </div>
                        </div>
                        <span id="checkbox_error" style="color:red;"></span>
                        <div class="" id="particularCountry" style="display:none;">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6>Country Name</h6>


                                    </div>
                                </div>
                                <div class="row pl-21 pb-2">
                                    @foreach($countryList as $data)
                                    <div class="col-md-4">
                                        <div class="pb-1">
                                            <input <?php if (in_array($data->id, $selectedCountry)) {
                                                        echo "checked";
                                                    }  ?> type="checkbox" class="form-check-input countryCheck" id="checkid{{$data->id}}" name="checkid[]" value="{{$data->id}}">{{$data->name}}
                                        </div>
                                    </div>
                                    @endforeach
                                    <span id="country_data" style="color:red"></span>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" id="saveCountry" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div> -->
<!-- country block modal end -->
<!-- block by country -->

<!-- IP Address -->
<!-- <div class="content-wrapper custom-wrapper">
        <div class="card" style="margin-bottom:20px;">
            <div class="row list-name m-3">
                <div class="col-sm-6 card-title">
                    <h4 class="card-title">IP Address</h4>
                </div>
                <div class="col-sm-6">
                    <a data-toggle="modal" class="btn btn-success btn-rounded btn-sm btn-fw pull-right" data-target="#exampleModal-5" data-whatever="@mdo" href="javascript:void(0)"><i class="mdi mdi-plus"></i>Add IP Address</a>
                </div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <span id="ip_blocked_list"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div> -->
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' name="adduser" method="post" id="alaycare-details">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">AlayaCare Username<span style="color:red">*</span></label>
                        <input type="text" name="alaycare_username" value="{{($agencyDetails->alaycare_status ==1)?$agencyDetails->alaycare_username:''}}" id="alaycare_username" class="form-control" placeHolder="Enter AlayaCare Username">
                        <span id="alaycare_username_error" class="error mt-2" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">AlayaCare Password<span style="color:red">*</span></label>
                        <input type="text" name="alaycare_password" value="{{($agencyDetails->alaycare_status ==1)?$agencyDetails->alaycare_password:''}}" id="alaycare_password" class="form-control" placeHolder="Enter AlayaCare Password">
                        <span id="alaycare_password_error" class="error mt-2" for="document_type"></span>
                    </div>



                    <div class="modal-footer">
                        <button type="button" id="save-alaycare-details" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- edit ip address modal end -->

<!-- IP Address -->
<!-- Script rate start -->
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
                                        <input type="radio" name="type" class="form-check-input" id="caregiver_type" value="Caregiver">
                                        Caregiver
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="type" class="form-check-input" id="patient_type" value="Patient">
                                        Patient
                                    </label>
                                </div>
                            </div>
                        </div>
                        <span style="color:red" id="type_error_service"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Service<span style="color:red">*</span></label>
                        <input type="text" name="agency_service" value="" id="agency_service" class="form-control" placeHolder="Enter Service">
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
                        <label for="recipient-name" class="col-form-label">Remote Focus Username<span style="color:red">*</span></label>
                        <input type="text" name="robort_username" value="{{($agencyDetails->robort_status ==1 && $agencyDetails->robort_user_name !='')?$agencyDetails->robort_user_name:''}}" id="robort_username" class="form-control" placeHolder="Enter Remote Focus Username">
                        <span id="robort_username_error" class="error mt-2" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Remote Focus Password<span style="color:red">*</span></label>
                        <input type="text" name="robort_password" value="{{($agencyDetails->robort_status ==1 && $agencyDetails->robort_user_password !='')?$agencyDetails->robort_user_password:''}}" id="robort_password" class="form-control" placeHolder="Enter Remote Focus Password">
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

<form id="directId" action="<?php echo URL::to('/'); ?>/agency/token-insert" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="agency_id" value="<?= $id ?>">
    <input type="hidden" name="id" value="<?php if (isset($generate_token_details->id) && $generate_token_details->id != '') {
                                                echo $generate_token_details->id;
                                            } ?>">
</form>








@include('include/footer')

<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
<script src="<?= URL::to('assets/jquery-confirmation/js/jquery-confirm.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/daterangepicker.css" />
<script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
<script>
    setTimeout(function() {
        $('.alert-success').fadeOut('fast');
    }, 3000);
    $("#start_date, #end_date").datepicker();
    $("#end_date").change(function() {
        var startDate = document.getElementById("start_date").value;
        var endDate = document.getElementById("end_date").value;

        if ((Date.parse(endDate) <= Date.parse(startDate))) {
            alert("End date should be greater than Start date");
            //  $("#end_date_error_mess").html('End date should be greater than Start date');
            document.getElementById("end_date").value = "";

        }
    });
    $(document).on('click', '.addDomain', function(e) {
        $(this).attr('data-id', '');
        $("#mid").val('');
        $('#ModalLabel').html('Add Domain');
        $('#domain_id').val('');
        $('#exampleModal-4').modal('show');
    })

    $(document).on("change", ".smsEnableDisabled", function() {
        var Issms = $(this).prop('checked') == true ? 1 : 0;
        var agencyId = $('#agency_id').val();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '{{route("agencySmsStatus")}}',
            data: {
                'is_sms': Issms,
                'agency_id': agencyId
            },
            success: function(data) {
                if (data.status == true) {
                    $("#msgs").removeAttr('style');
                    $('#msgs').html("<div class='alert alert-success'>" + data.error_msg + "</div>").delay(1000).fadeOut();
                }
            }
        });
    });

    $(document).on("change", ".enable_hha", function() {
        var Issms = $(this).prop('checked') == true ? 1 : 0;
        var agencyId = $('#agency_id').val();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '{{route("hha-status")}}',
            data: {
                'enable_hha': Issms,
                'agency_id': agencyId
            },
            success: function(data) {
                if (data.status == true) {
                    $("#msgs").removeAttr('style');
                    $('#msgs').html("<div class='alert alert-success'>" + data.error_msg + "</div>").delay(1000).fadeOut();
                }
            }
        });
    });
</script>
<script>
    function validation() {

        var agency_fk = $('#agency_fk').val();
        var item = $('#item').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if (agency_fk == '' && item == '' && start_date == '' && end_date == '') {
            alert('please select any one');
            return false;
        } else {
            return true;
        }
    }

    function export_data() {

        var agency_fk = $('#agency_fk').val();
        var item = $('#item').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var temp1 = '<?php echo URL::to("/") ?>/rate-export?agency_fk=' + agency_fk + '&item=' + item + '&start_date=' + start_date + '&end_date=' + end_date;
        //  var temp = temp1.replace("http://", "https://");
        $('#test_rate').attr("style", '');
        $('#test_rate').attr("href", temp1);
    }

    function validateIPAddress(ip) {
        var expr = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        return expr.test(ip);
    }
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
    }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('MM-DD-YYYY') + ' to ' + end.format('MM-DD-YYYY'));
    });

    function checkGenerateInvoice(id) {
        $.ajax({
            url: "<?php echo URL::to('/'); ?>/getLastMonth",
            type: "GET",
            data: {
                'month': "<?php echo date('F', strtotime('last month')); ?>",
                'year': "<?php echo date('Y', strtotime('last month')); ?>",
                'agency_fk': <?php echo $id; ?>
            },
            success: function(response) {
                if (response == 1) {
                    window.location.href = "<?php echo URL::to('/'); ?>/agencywise-invoice/<?= $id ?>";
                } else {
                    alert("Invoice already generated");
                    return false;
                }
            }
        });
    }

    function getTokenGenerate() {

        <?php if (isset($generate_token_details->id) && $generate_token_details->id != '') { ?>
            var con = "Are you sure regenerate token?";
        <?php } else { ?>
            var con = "Are you sure generate token?";
        <?php } ?>
        $.confirm({
            title: 'Are you sure generate token?',
            columnClass: "col-md-6",

            content: con,
            buttons: {
                formSubmit: {
                    text: 'Submit',
                    btnClass: 'btn-danger',
                    action: function() {
                        $('#directId').submit();
                    }
                },
                cancel: function() {
                    //close
                },
            },
        });
    }

    function deleteRecordAgencies(id) {
        var url = "{{url('agency/delete/')}}";
        $.confirm({
            title: 'Delete',
            columnClass: "col-md-6",
            content: 'Are you sure delete record?',
            buttons: {
                formSubmit: {
                    text: 'Delete',
                    btnClass: 'btn-danger',
                    action: function() {
                        window.location.href = url + '/' + id;
                    }
                },
                cancel: function() {
                    //close
                },
            },
        });
    }

    $(document).on('click', '.add-agency-wise-sms', function(e) {
        $('#agency_wise_sms_type_error').html("");
        $('#agency_wise_sms_message_error').html("");
        $('#agency_wise_sms_type').val('');
        $('#agency_wise_sms_message').val('');
        $('#SmsLable').html('Add SMS');




        $('#add-agency-wise-sms-popup').modal('show');
    })

    $(document).on('click', '.edit-sms-detail', function(e) {
        $('#agency_wise_sms_type_error').html("");
        $('#agency_wise_sms_message_error').html("");
        var dataId = $(this).attr('data-id');

        var type = $('#sms_type' + dataId).html();
        var msg = $('#sms_msg' + dataId).html();

        $('#SmsLable').html('Edit SMS');
        $('#smsMId').val(dataId);
        $('#agency_wise_sms_type').val(type);
        $('#agency_wise_sms_message').val(msg);
        $('#add-agency-wise-sms-popup').modal('show');
    })

    $('body').on('click', '.delete-sms-detail', function(e) {
        var msg = "you want to delete this sms?";
        var id = $(this).attr('data-id');
        $.confirm({
            title: 'Are you sure?',
            columnClass: "col-md-6",

            content: msg,
            buttons: {
                formSubmit: {
                    text: 'DELETE',
                    btnClass: 'btn-danger',
                    action: function() {
                        $.ajax({
                            url: '{{ url("agency-wise-sms-delete")}}',
                            type: "POST",
                            data: {
                                'id': id,
                                '_token': "{{ csrf_token()}}"
                            },
                            success: function(res) {
                                toastr.success(res.error_msg);
                                // agencyWiseSmsList(1);
                            }
                        })
                    }
                },
                cancel: function() {
                    //close
                },
            },
            onContentReady: function() {

            }
        });
    });

    // function agencyWiseSmsList(page) {
    //     $.ajax({

    //         url: "{{ url('agency-wise-sms-list') }}" + "?page=" + page,
    //         type: "GET",
    //         data: {
    //             'type': 'SMS',
    //             'agency_id': "{{ $id}}",
    //             'page': page,

    //         },
    //         success: function(response) {
    //             $('#sms_id').html("");
    //             $('#sms_id').html(response);
    //         }
    //     });

    //     return false;
    // }
    // agencyWiseSmsList(1);

    // $(document).on('click', '.pagination a', function(event) {
    //     $('li').removeClass('active');
    //     $(this).parent('li').addClass('active');
    //     event.preventDefault();
    //     var myurl = $(this).attr('href');
    //     var page = $(this).attr('href').split('page=')[1];
    //     console.log(page);


    //     agencyWiseSmsList(page);
    // });

    $('#agency-wise-sms-saveId').click(function(e) {
        $('#agency_wise_sms_message_error').html("");


        var send_sms_eng = $('#send_sms_eng').val();
        var send_sms_spanish = $('#send_sms_spanish').val();
        var appointment_send_book_eng = $('#appointment_send_book_eng').val();
        var appointment_send_book_spanish = $('#appointment_send_book_spanish').val();
        var cnt = 0;
        if (send_sms_eng == '' && send_sms_spanish == '' && appointment_send_book_eng == '' && appointment_send_book_spanish == '') {
            $('#agency_wise_sms_message_error').html("Please Enter Message");
            cnt = 1;
        }

        if (cnt == 1) {
            return false;
        } else {
            var forms = $('#add-agency-wise-sms-form')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ url('agency-wise-sms-save')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);

                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });

        }
    })

    $(document).on('click', '.add-notification-email', function(e) {
        $('#notification_email_error').html("");
        $('#service_id').html("");
        getResponse();
        $('#add-notification-email-popup').modal('show');
    })

    function notificationEmailList(page) {
        $.ajax({
            url: "{{ url('notification-email-list')}}",
            type: "GET",
            data: {
                'type': 'notifiction-email',
                'agency_id': "{{ $id}}",
                'page': page,

            },
            success: function(response) {
                $('#notification_email_id').html("");
                $('#notification_email_id').html(response);
            }
        });

        return false;
    }

    $('#notification-email-saveId').click(function(e) {
        var selectedPatients = [];
        var selectedCaregivers = [];
        var selectedCaregiversId = [];
        var selectedPatientId = [];
        $('#notifications_email_error').html("");
        $(".patient_checkbox:checked").each(function() {
            selectedPatients.push($(this).val());
            selectedPatientId.push($(this).attr('data-id'));
        });

        $(".caregiver_checkbox:checked").each(function() {
            selectedCaregivers.push($(this).val());
            selectedCaregiversId.push($(this).attr('data-id'))

        });


        var cnt = 0;
        var notificationEmail = $('#notificationEmail').val();
        var validEmail = /^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/;
        if (notificationEmail.trim() == '') {
            $('#notifications_email_error').html("Email is required");
            cnt = 1;
        }

        if (notificationEmail.trim() != '') {
            if (!validEmail.test(notificationEmail)) {
                $('#notifications_email_error').html("Invalid Email Address");
                cnt = 1;
            }

        }
        if (selectedCaregivers.length == 0 && selectedPatients.length == 0 && service_id == '') {
            $('#notification_email_error').html("Patient or Caregiver or Service is required");
            cnt = 1;

        }



        if (cnt == 1) {
            return false;
        } else {
            var forms = $('#addnotificationemail')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');
            newForms.append('patient_id', selectedPatientId);
            newForms.append('caregivers_id', selectedCaregiversId);


            $.ajax({
                url: "{{ url('agency-wise-notification-email-save')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);
                    $('#add-notification-email-popup').modal('hide');
                    $('#addnotificationemail')[0].reset();
                    notificationEmailList(1);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });

        }
    })


    // add domain
    function domainList(page) {
        $.ajax({
            url: "{{ url('agency-wise-domain-list')}}",
            type: "GET",
            data: {
                'type': 'domain',
                'agency_id': "{{ $id}}",
                'page': page,

            },
            success: function(response) {
                $('#domain_list_id').html("");
                $('#domain_list_id').html(response);
            }
        });

        return false;
    }

    $('#saveId').click(function(e) {
        var domain = $('#domain_id').val();
        var cnt = 0;
        $('#domain_error').html('');
        var regex = /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/;
        if (domain.trim() == '') {
            $('#domain_error').html("Please enter Domain Name");
            cnt = 1;
        }
        if (domain.trim() != '') {
            if (!regex.test(domain)) {
                $('#domain_error').html("Invalid Domain");
                cnt = 1;
            }
        }

        if (cnt == 1) {
            return false;
        } else {
            var mid = $('#mid').val();
            var forms = $('#submitId')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');
            newForms.append('agency_name', '{{ $agencyDetails->agency_name }}');
            if (mid != '') {
                newForms.append('id', mid);
            }

            $.ajax({
                url: "{{ url('agency-wise-domain-save')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);
                    $('#exampleModal-4').modal('hide');
                    $('#submitId')[0].reset();
                    domainList(1);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });

        }
    })
    $('body').on('click', '.pagination2 a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];
        var explode = $(this).attr('href').split('?');

        var explodes = explode[1].split('&');
        console.log(explodes);
        var type = explodes[0].split('type=')[1];

        if (type == 'domain') {

            domainList(page);
            event.preventDefault();
        }

    });


    // add domain


    // block by country

    $('body').on('click', '#status', function(e) {
        // $('#submitCountry')[0].reset();
        $('#countryBlock').modal('show');
    });


    $(document).ready(function() {
        var agencyId = $('#agency_id').val();
        countryList(agencyId);
        // ipAddressList(agencyId);
        var checkedNum = $('input[name="checkid[]"]:checked').length;
        if (!checkedNum) {
            $("#allCountryCheck").prop('checked', true);
        } else {
            $("#perCountryCheck").prop('checked', true);
            $("#particularCountry").css('display', 'block');
        }
    });

    $("#allCountryCheck").change(function() {
        var ischecked = $(this).is(':checked');
        if (ischecked) {
            $("#perCountryCheck").prop('checked', false);
            $("#particularCountry").css('display', 'none');

        }
        if (!ischecked) {
            $(".countryCheck").prop('checked', false);
        }
    });

    $("#perCountryCheck").change(function() {
        var ischecked = $(this).is(':checked');
        if (ischecked) {
            $("#allCountryCheck").prop('checked', false);
            $("#checkbox_error").html('');
            $("#particularCountry").css('display', 'block');
        }
        if (!ischecked) {
            $("#particularCountry").css('display', 'none');
        }

    });
    $('#saveCountry').click(function(e) {
        const selectedValues = $('input[name="checkid[]"]:checked').map(function() {
            return $(this).parent().text();
        }).get();

        var allischecked = $("#allCountryCheck").is(':checked');
        var perischecked = $("#perCountryCheck").is(':checked');


        var error = 0;

        if (allischecked == false && perischecked == false) {
            $("#checkbox_error").html('Please check Anyone');
            error = 1;
        } else {
            $("#checkbox_error").html('');
            error = 0;
        }
        if (perischecked == true) {
            var checkedNum = $('input[name="checkid[]"]:checked').length;
            if (!checkedNum) {
                alert('Please select anyone');
                error = 1;
            }
        }

        if (error == 1) {
            return false;
        } else {
            if (allischecked != true) {
                var forms = $('#submitCountry')[0];
                var newForms = new FormData(forms);
                newForms.append('_token', '{{ csrf_token() }}');
                newForms.append('selectedValues', selectedValues);
                $.ajax({
                    url: "{{ url('agency-country-save')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                        var agencyId = $('#agency_id').val();
                        $('#countryBlock').modal('hide');
                        // $('#submitCountry')[0].reset();
                        countryList(agencyId);
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg);
                    }
                });
            } else {
                toastr.success('Successfully Allowed Country');
                var agencyId = $('#agency_id').val();
                $('#countryBlock').modal('hide');
                // $('#submitCountry')[0].reset();
            }
        }
    });
    $('#saveIPAddress').click(function(e) {

        var ip_address = $('#ip_address').val();
        error = 0;

        if (ip_address.trim() == '') {
            $('#ip_address_error').html("Required");
            error = 1;
        } else if (!validateIPAddress(ip_address)) {
            $('#ip_address_error').html("Invalid IP Address");
            error = 1;
        } else {
            $('#ip_address_error').html("");
        }

        if ($('input[name="type"]:checked').length == 0) {
            $('#type_error').html("Required");
            error = 1;
        } else {
            $('#type_error').html("");
        }

        if (error == 1) {
            return false;
        } else {
            var forms = $('#submitIpAddress')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');
            $.ajax({
                url: "{{ url('agency-ip-address-save')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);
                    var agencyId = $('#agency_id').val();
                    $('#exampleModal-5').modal('hide');
                    // $('#submitCountry')[0].reset();
                    ipAddressList(agencyId);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });
        }

    });
    $('#exampleModal-5').on('shown.bs.modal', function() {
        // $('#submitId')[0].reset();
        $('#submitIpAddress')[0].reset();
        $('#ip_address').val("");
        $('#ip_address_error').html("");
    })

    $('#countryBlock').on('hidden.bs.modal', function() {
        // $('#submitCountry')[0].reset();
    })

    function countryList(id) {

        $.ajax({
            url: "{{ url('agency-wise-country-list')}}",
            type: "GET",
            data: {
                'type': 'country',
                'agency_id': {
                    id
                },
            },
            success: function(response) {

                $('#country_blocked_list').html("");
                $('#country_blocked_list').html(response);
            }
        });

        return false;
    }

    function ipAddressList(id) {
        $.ajax({
            url: "{{ url('agency-wise-ip-list')}}",
            type: "GET",
            data: {
                'type': 'country',
                'agency_id': {
                    id
                },
            },
            success: function(response) {

                $('#ip_blocked_list').html("");
                $('#ip_blocked_list').html(response);
            }
        });

        return false;
    }

    $('body').on('click', '.pagination a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];
        var explode = $(this).attr('href').split('?');

        var explodes = explode[1].split('&');

        var type = explodes[0].split('type=')[1];


        if (type == 'domain') {

            domainList(page);
            event.preventDefault();
        }
        if (type == 'user') {

            loadUserList(page);
            event.preventDefault();
        }

    });
    $('body').on('click', '.edit-detail', function(e) {
        var dataId = $(this).attr('data-id');
        var texts = $('#domain' + dataId).html();
        $('#mid').val(dataId);
        $('#ModalLabel').html('Edit Domain');
        $('#domain_id').val(texts);
        $('#exampleModal-4').modal('show');
    })

    $('body').on('click', '.delete-detail', function(e) {
        var msg = "you want to delete this domain?";
        var id = $(this).attr('data-id');
        $.confirm({
            title: 'Are you sure?',
            columnClass: "col-md-6",

            content: msg,
            buttons: {
                formSubmit: {
                    text: 'DELETE',
                    btnClass: 'btn-danger',
                    action: function() {
                        $.ajax({
                            url: '{{ url("agency-domain-delete")}}',
                            type: "POST",
                            data: {
                                'id': id,
                                '_token': "{{ csrf_token()}}"
                            },
                            success: function(res) {
                                toastr.success(res.error_msg);
                                domainList(1);
                            }
                        })
                    }
                },
                cancel: function() {
                    //close
                },
            },
            onContentReady: function() {

            }
        });
    });

    $('body').on('click', '.edit-ip-address', function(e) {
        var dataId = $(this).attr('data-id');
        var editid = $("#id").val(dataId);
        $('#exampleModal-6').modal('show');
        $('#ip_address_edit_error').html("");
        $.ajax({
            url: "{{ url('agency-ip-edit')}}",
            type: "GET",
            data: {
                'type': 'country',
                'id': {
                    dataId
                }
            },
            success: function(response) {
                $("#ip_address_edit").val(response.data.ip_address);
                $("input[value='" + response.data.type + "']").attr('checked', true);

            }
        });
    });

    $('body').on('click', '#updateIPAddress', function(e) {
        var ip_address = $('#ip_address_edit').val();
        var editid = $("#id").val();
        error = 0;

        if (ip_address.trim() == '') {
            $('#ip_address_edit_error').html("Required");
            error = 1;
        } else if (!validateIPAddress(ip_address)) {
            $('#ip_address_error').html("Invalid IP Address");
            error = 1;
        } else {
            $('#ip_address_error').html("");
        }

        if ($('input[name="type_edit"]:checked').length == 0) {
            $('#type_edit_error').html("Required");
            error = 1;
        } else {
            $('#type_edit_error').html("");
        }


        if (error == 1) {
            return false;
        } else {
            var forms = $('#submitEditIpAddress')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');
            $.ajax({
                url: "{{ url('agency-ip-update')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);
                    var agencyId = $('#agency_id').val();
                    $('#exampleModal-6').modal('hide');
                    // $('#submitCountry')[0].reset();
                    ipAddressList(agencyId);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });
        }
    });
    $('body').on('click', '.delete-ip-address', function(e) {
        var msg = "you want to delete this IP Address?";
        var id = $(this).attr('data-id');
        $.confirm({
            title: 'Are you sure?',
            columnClass: "col-md-6",

            content: msg,
            buttons: {
                formSubmit: {
                    text: 'Submit',
                    btnClass: 'btn-danger',
                    action: function() {
                        $.ajax({
                            url: '{{ url("agency-ip-delete")}}',
                            type: "POST",
                            data: {
                                'id': id,
                                '_token': "{{ csrf_token()}}"
                            },
                            success: function(res) {
                                toastr.success(res.error_msg);
                                var agencyId = $('#agency_id').val();
                                ipAddressList(agencyId);
                            }
                        })
                    }
                },
                cancel: function() {
                    //close
                },
            },
            onContentReady: function() {

            }
        });
    });
    // block by country

    $(".two_factor_auth").change(function() {
        var status = "N";
        var id = $(this).attr("data-id");
        if (this.checked) {
            status = "Y";
        }

        $.ajax({
            async: false,
            global: false,
            url: "{{ url('agency-two-factor-enable-disable') }}",
            data: {
                'id': id,
                'status': status
            },
            success: function(response) {
                toastr.success(response.error_msg);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        })

    });

    $(".password_expired").change(function() {
        var status = "N";
        var id = $(this).attr("data-id");
        if (this.checked) {
            status = "Y";
        }

        $.ajax({
            async: false,
            global: false,
            url: "{{ url('agency-password-expired-enable-disable') }}",
            data: {
                'id': id,
                'status': status
            },
            success: function(response) {
                toastr.success(response.error_msg);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        })

    });
</script>
<script>
    $(document).on('click', '.log-pegination .pagination a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];
        console.log(page);


        getData(page);
    });
    $(document).ready(function() {

        /**
         * User log Table Initialize
         */
        $('#loadertag').show();

        loadUserList(1);
        /**
         * User login log Table Initialize
         */
    });

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
                console.error(_error);
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
                console.error(_error);
                toastr.error('Something happened. Try again');
            }
        });
    }


    function editNotificationEmail(id) {
        $.ajax({
            method: 'GET',
            url: "{{ url('edit-email-notification') }}",
            data: {
                'id': id,

            },
            success: function(response) {
                if (response.data.caregivers_id != "") {
                    var splitData = response.data.caregivers_id.split(',');
                    $.each(splitData, function(i, v) {

                        $('#caregiver_notification_email' + v).prop("checked", true);
                    })
                }

                if (response.data.patients_id != "") {
                    var splitData = response.data.patients_id.split(',');
                    $.each(splitData, function(i, v) {

                        $('#patient_notification_email' + v).prop("checked", true);
                    })
                }
                $('#notificationId').val(id)
                $('#notificationEmail').val(response.data.email)
                $('.notification-emails').html("Edit Notification Email")
                $('#add-notification-email-popup').modal('show');
                getResponse(response.data.service_id);
            },
            error: function(jxr) {

            }

        });
    }

    function resetNotificationEmail() {
        $('#notificationId').val('');
        $('.error').html('');
        $('#addnotificationemail')[0].reset();
        $('.notification-emails').html("Add Notification Email")
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
            console.log(filePath)
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
                        console.log(response);
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
        if (IsAlaycare == 1) {
            $('#alaycare-popup').modal('show');
        } else {
            var agencyId = $('#agency_id').val();
            $.ajax({
                type: "GET",
               
                url: '{{route("agencyAlaycareStatus")}}',
                data: {
                    'is_alaycare': IsAlaycare,
                    'agency_id': agencyId
                },
                success: function(data) {
                    $('#alaycare_username_id').html("N/A")
                    $('#alaycare_password_id').html("N/A")
                    $('#alaycare_username').val("")
                    $('#alaycare_password').val("")
                    toastr.success(data.error_msg);
                    final_array = [];
                    getAllSkills(1);
                }
            });
        }

    });

    $('#save-alaycare-details').click(function(e) {
        var alaycareUsername = $('#alaycare_username').val();
        var alaycarePassword = $('#alaycare_password').val();

        var cnt = 0;
        $('#alaycare_password_error').html('');
        $('#alaycare_username_error').html('');

        if (alaycareUsername.trim() == '') {
            $('#alaycare_username_error').html("Please AlayaCare Username");
            cnt = 1;
        }
        if (alaycarePassword.trim() == '') {
            $('#alaycare_password_error').html("Please AlayaCare Password");
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
                    $('#alaycare_username').val(alaycareUsername)
                    $('#alaycare_password').val(alaycarePassword)

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
        console.log(existingId)
        $.ajax({
            async: false,
            global: false,
            type: "GET",
            url: "<?php echo URL::to('/'); ?>/ajax-all-service",

            success: function(res) {
                console.log(res.data)
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
        $('#service-popup').modal('show');
    });

    $('#save-service').click(function() {
        var agency_service = $('#agency_service').val();


        var cnt = 0;
        $('#type_error_service').html('');
        $('#agency_service_error').html('');

        if ($('input[name="type"]').is(':checked') == false) {
            $('#type_error_service').html("Please select Type");
            cnt = 1;
        }
        if (agency_service.trim() == '') {
            $('#agency_service_error').html("Please Enter Service");
            cnt = 1;
        }

        if (cnt == 1) {
            return false;
        } else {

            var forms = $('#add-service-form')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', '{{ csrf_token() }}');

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
                console.log(res.data);
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
        resetService();
    });

    $(document).on('click', '.pagination a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];

        getService(page);
    });

    $(document).on("change", ".robort-btn", function() {
        var IsRobort = $(this).prop('checked') == true ? 1 : 0;
        if (IsRobort == 1) {
            $('#robort-popup').modal('show');
        } else {
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
                    $('#robort_username').val("")
                    $('#robort_password').val("")
                    $('#remote_username_id').html("N/A")
                    $('#remote_password_id').html("N/A")
                    toastr.success( data.error_msg)
                   
                }
            });
        }

    });

    $('#save-robort-details').click(function(e) {
        var robortUsername = $('#robort_username').val();
        var robortPassword = $('#robort_password').val();

        var cnt = 0;
        $('#robort_password_error').html('');
        $('#robort_username_error').html('');

        if (robortUsername.trim() == '') {
            $('#robort_username_error').html("Please Remote Focus Username");
            cnt = 1;
        }
        if (robortPassword.trim() == '') {
            $('#robort_password_error').html("Please Remote Focus Password");
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
                    $('#remote_username_id').html(robortUsername)
                    $('#remote_password_id').html(robortPassword)
                    $('#robort-popup').modal('hide');
                   
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });
        }
    })
    var final_array =[];

    function loadAgencySkill(){
        var response = "{{ json_encode($agency_skill)}}";
        $.each(JSON.parse(response),function(i,v){
            final_array.push(v)
         
        })
    }
    loadAgencySkill();
 
var skillPage;
    function getAllSkills(page){
        $('#loaderAlayaSkill').attr('style','');
        $.ajax({
            url: "{{ url('agency-alayacare-skill')}}",
            type: "get",
            data: {
                agency_id:"{{ $agencyDetails->id}}",
                page:page
            },
            success: function(response) {
                
                var json = (response.data.items !=undefined)?response.data.items:[];
                var responseHtml = '';
                if(json.length !=0){
                    var cnt =1;
                    if(response.data.page !=1){
                        cnt = (response.data.page *100)-99;
                    }
                    $.each(json,function(i,v){
                        var checked="";
                        if(final_array.includes(v.id)){
                            checked = 'checked="checked"';
                        }
                        responseHtml +='<tr><td><input type="checkbox" name="cbox" class="cbox" value="'+v.id+'" '+checked+'></td><td>'+cnt+++'</td><td>'+v.branch.name+'</td><td>'+v.name+'</td><td>'+v.category.name+'</td></tr>';
                    });
                    skillPage=response.data.page;
                    
                    if(response.data.total_pages >1){
                        
                        $('#nextSkillId').attr('style','');
                    }else{

                        if(response.data.total_pages == response.data.page){
                        
                            $('#previousSkillId').attr('style','');
                            $('#nextSkillId').attr('style','display:none');
                        }
                    }
                }else{
                    responseHtml ='<tr><td colspan="4">No record available</td></tr>';
                    $('#previousSkillId').attr('style','display:none');
                    $('#nextSkillId').attr('style','display:none');
                }
                $('#alayacare_skill_response').html("");
                $('#alayacare_skill_response').html(responseHtml);
                
            
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }

    function nextSkill(){
        getAllSkills(skillPage+1);
        $('#previousSkillId').attr('style','');
    }
    function previousSkill(){
        if(skillPage-1 !=0){
            getAllSkills(skillPage-1);
            if(skillPage-1 ==1){
                $('#previousSkillId').attr('style','display:none');
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
  
    function addSkill(){
        var checked = $('.cbox').is(":checked");
        if (checked == false) {
            toastr.error("Please select checkbox");
            return false;
        } else {
            var final_array = [];
            $('.cbox').each(function(i, v) {
                var schecked = $(this).is(":checked");
                if (schecked == true) {
                    if(!final_array.includes($(this).val())){
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
                  $.each(json,function(i,v){
                    if(!final_array.includes(v)){
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
</script>
<!-- Script rate end -->