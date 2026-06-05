@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link href="{{ asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<style>
    .error {
        color: Red;
    }

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
        /* width: 87px; */
        clear: left;
        /* text-align: right; */
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

    .content-wrapper {

        min-height: auto;

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
    <div class="content-wrapper">
        <div class="col-12 grid-margin-top">

            @if (Session::has('old_password_error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('old_password_error') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif
        </div>

        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">User #
                        <?= $userDetails->id . ' - ' . ucwords($userDetails->first_name) . ' ' . ucwords($userDetails->last_name) . ' ' ?>
                    </h4>

                </div>

                <div class="d-md-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">

                    </div>

                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h4>User Details</h4>
                            </div>
                            <div class="col-sm-9">
                                @can('user-delete')
                                <?php if ($userDetails->id != $user->id) {
                                    if ($totalEmcCountRecord == 0 && $totalAgencyCountRecord == 0) { ?>

                                        <a href="javascript:void(0);" data-toggle="tooltip" title="Delete" onclick="deleteUserData('{{$userDetails->id}}')" class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1"><i class="mdi mdi-delete"></i>Delete</a>

                                    <?php }
                                    if ($totalEmcCountRecord > 0) { ?>
                                        <a href="javascript:void(0)" onclick="getEMCUpdateRecord(<?php echo $userDetails->id; ?>);" data-toggle="tooltip" title="Update" class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1"><i class="mdi mdi-delete"></i></a>
                                    <?php }
                                    if ($totalAgencyCountRecord > 0) { ?>
                                        <a href="javascript:void(0)" onclick="getAgencyUpdateRecord(<?php echo $userDetails->id; ?>,<?php echo $userDetails->agency_fk; ?>);" data-toggle="tooltip" title="Update" class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1"><i class="mdi mdi-delete"></i></a>
                                    <?php } ?>
                                <?php } ?>
                                @endcan

                                @can('user-edit')
                                @if($userDetails->agency_fk !="")
                                <a href="javascript" data-toggle="modal" class="btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1 pull-right  d-none d-md-block" data-target="#exampleModal-task" data-whatever="@mdo"><i class="mdi mdi-pencil"> </i>
                                    Edit User</a>
                                @else
                                <a href="<?php echo URL::to('/'); ?>/edituser?i=<?php echo $userDetails->id; ?>&flag=uview" class="btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1"><i class="mdi mdi-pencil"> </i>
                                    Edit User</a>
                                @endif

                                @endcan
                                @if (auth()->user()->user_type_fk == '184')
                                <a href="javascript:void(0)" class="btn btn-light btn-sm btn-fw pull-right btn-rounded ml-1" onclick="$('#modal-change').modal('show')"><i class="mdi mdi-settings"> </i> Change
                                    Password</a>
                                @endif
                                @can('user-send-invitation')
                                <?php
                                if ($userDetails->email != '') { ?>
                                    <a class="btn btn-info btn-sm btn-fw pull-right btn-rounded" href="javascript:void(0);" onclick="sendInvitation('{{$userDetails->id}}')"><i class="mdi mdi-send" aria-hidden="true"></i> Send Invitation</a>
                                <?php } else { ?>
                                    <a class="btn btn-info btn-sm btn-fw pull-right btn-rounded" href="javasctipt:void(0)" onclick="return confirm('Email does not exist. please update email address.')"><i class="mdi mdi-send" aria-hidden="true"></i> Send Invitation</a>
                                <?php }
                                ?>
                                @endcan
                                <div class="dropdown mr-3 d-none d-md-block">
                                    <select name="" id="status_id" style="max-width: 200px;float:right;margin-right:10px;border-radius:4px;height:36px;" onchange="getStatus('{{ $userDetails->id }}')" class="form-control">
                                        <option value="">Select</option>
                                        @if($userDetails->active =='inactive')
                                        <option value="active">Active</option>
                                        <option value="unblock">Unblock</option>
                                        @endif
                                        @if($userDetails->active =='active')
                                        <option value="inactive">Inactive</option>
                                        <option value="block">Block</option>
                                        @endif
                                        @if($userDetails->active =='unblock')
                                        <option value="block">Block</option>
                                        <option value="inactive">Inactive</option>
                                        @endif
                                        @if($userDetails->active =='block')
                                        <option value="unblock">Unblock</option>
                                        <option value="active">Active</option>
                                        @endif
                                    </select>
                                </div>


                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="profile-feed">
                                    <div class="d-flex align-items-start profile-feed-item">


                                        <div class="ml-3">
                                           
                                            <dl class="dl-horizontal">
                                                <dt> First Name</dt>
                                                <dd id="view_first_id"> <?= $userDetails->first_name != '' ? ucwords($userDetails->first_name) : '-' ?>
                                                </dd>

                                                <dt> Last Name</dt>
                                                <dd  id="view_last_id"> <?= $userDetails->last_name != '' ? $userDetails->last_name : '-' ?>
                                                </dd>
                                                <dt> Phone</dt>
                                                <dd  id="view_phone_id"> <?= $userDetails->phone != '' ? $userDetails->phone : '-' ?> </dd>
                                                <dt> Email</dt>
                                                <dd  id="view_email_id"> <?= $userDetails->email != '' ? $userDetails->email : '-' ?> </dd>
                                                <dt> Last Login Date</dt>
                                                <dd id="lastLDate"> <?php if ($userDetails->last_login_at != '') {
                                                                        echo date('m/d/Y H:i:s', strtotime($userDetails->last_login_at));
                                                                    } else {
                                                                        echo "-";
                                                                    } ?></dd>
                                                
                                                <dt> Status</dt>
                                                <dd id="status{{ $userDetails->id}}">
                                                    @if($userDetails->active =='active')
                                                    <span class="badge badge-success">Active</span>
                                                    @elseif($userDetails->active =='inactive')
                                                    <span class="badge badge-danger">Inactive</span>
                                                    @elseif($userDetails->active =='block')
                                                    <span class="badge badge-danger">Block</span>
                                                    @elseif($userDetails->active =='unblock')
                                                    <span class="badge badge-info">Unblock</span>
                                                    @else
                                                    <span class="badge badge-primary">Invite</span>
                                                    @endif

                                                </dd>
                                                <dt> Is Nurse </dt>
                                                <dd> @if($userDetails->is_nurse == 1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                                </dd>

                                                <dt> Language </dt>
                                                <dd> @if($userDetails->nurselanguage != '')
                                                    {{ $userDetails->nurselanguage }}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                                </dd>
                                                @if($userDetails->agency_fk =="")
                                                    @can('view-ssn-hub')
                                                    <dt> View SSN Hub </dt>
                                                    <dd>
                                                        <label class="toggle-switch toggle-switch-success">
                                                            <input type="checkbox" name="view_ssn_hub" id="view_ssn_hub" class="view_ssn_hub" value="{{ $userDetails->view_ssn_hub}}" {{ $userDetails->view_ssn_hub == 1 ? 'checked' : ''}}>
                                                            <span class="toggle-slider round"></span>
                                                        </label>
                                                    </dd>
                                                    @endcan
                                                @endif

                                                
                                                @can('restrict-user')
                                                <dt> Restrict User </dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="restrict_user" id="restrict_user" class="restrict_user" value="{{ $userDetails->restrict_user}}" {{ $userDetails->restrict_user == 1 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                @endcan
                                                
                                            </dl>
                                        </div>
                                        <div class="ml-3">
                                            <dl class="dl-horizontal">
                                                <dt> User Type</dt>
                                                <dd> <?= $userDetails->user_type_fk != '' ? $userDetails->user_type_fk : '-' ?>
                                                </dd>

                                                <dt> Login Type</dt>
                                                <dd> <?= $userDetails->login_type_fk != '' ? $userDetails->login_type_fk : '-' ?>
                                                </dd>

                                                <dt> Ext </dt>
                                                <dd  id="view_ext_id"> <?= $userDetails->ext != '' ? $userDetails->ext : '-' ?>
                                                </dd>
                                                <dt> IP Address</dt>
                                                <dd id="ipAdd"> @if(isset( $userDetails->last_login_ip))
                                                    {{ $userDetails->last_login_ip}}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                                </dd><br>
                                                
                                                <dt> Record Type</dt>
                                                <dd>
                                                    <select class="form-control"  id="record_access"  onchange="changeRecordType()">
                                                        
                                                        <option value="All" @if(isset( $userDetails->record_access) &&  $userDetails->record_access=='All')    selected  @endif  >All</option>
                                                        <option value="Patient" @if(isset( $userDetails->record_access) &&  $userDetails->record_access=='Patient')    selected  @endif  >Patient</option>
                                                        <option value="Caregiver"   @if(isset( $userDetails->record_access) &&  $userDetails->record_access=='Caregiver')    selected  @endif  >Caregiver</option>
                                                    </select>
                                                </dd>
                                                <dt> Patient View Page</dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="patient_page" id="patientPageDesign" class="patientPageDesign" value="{{ $userDetails->patient_page}}" {{ $userDetails->patient_page == 1 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                <dt> Show In Directory </dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="show_in_directory" id="show_in_directory" class="show_in_directory" value="{{ $userDetails->show_in_directory}}" {{ $userDetails->show_in_directory == 1 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                @if($userDetails->agency_fk =="")
                                                <dt> Show Hub </dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="show_hub" id="show_hub" class="show_hub" value="{{ $userDetails->show_hub}}" {{ $userDetails->show_hub == 1 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                @endif
                                                <dt> Two Factor Auth </dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="two_fact_auth" id="two_fact_auth" class="two_fact_auth" value="{{ $userDetails->two_fact_auth}}" {{ $userDetails->two_fact_auth == 'Y' ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                               
                                            </dl>
                                        </div>
                                        <div class="ml-3">
                                            <dl class="dl-horizontal">
                                                <dt> Enable Creator Email Notification </dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="creator_email_noti_toggle" id="creator_email_noti_toggle" class="creator_email_noti_toggle" value="{{ $userDetails->creator_email_noti_toggle}}" {{ $userDetails->creator_email_noti_toggle == '1' ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                 @if(auth()->user()->agency_fk == "" && $userDetails->agency_fk == "")
                                                <dt> Telehealth File Access </dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="is_telehealth" id="is_telehealth" class="is_telehealth" value="{{ $userDetails->is_telehealth}}" {{ $userDetails->is_telehealth == 1 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                @endif
                                                @if(auth()->user()->agency_fk == ""  && $userDetails->agency_fk == "")
                                                <dt> MDO File Access </dt>
                                                <dd>
                                                    <label class="toggle-switch toggle-switch-success">
                                                        <input type="checkbox" name="is_mdo" id="is_mdo" class="is_mdo" value="{{ $userDetails->is_mdo}}" {{ $userDetails->is_mdo == 1 ? 'checked' : ''}}>
                                                        <span class="toggle-slider round"></span>
                                                    </label>
                                                </dd>
                                                @endif
                                                @if(auth()->user()->agency_fk == ""  && $userDetails->agency_fk == "")
                                                <dt> Template Type</dt>
                                                <dd>
                                                    <select class="form-control"  id="template_type"  onchange="changeEsignTemplateType()">
                                                        
                                                        <option value="All" @if(isset( $userDetails->template_type) &&  $userDetails->template_type=='All')    selected  @endif  >All</option>
                                                        <option value="Location" @if(isset( $userDetails->template_type) &&  $userDetails->template_type=='Location')    selected  @endif  >Location</option>
                                                        <option value="Telehealth"   @if(isset( $userDetails->template_type) &&  $userDetails->template_type=='Telehealth')    selected  @endif  >Telehealth</option>
                                                    </select>
                                                </dd>
                                                @endif
                                            </dl>
                                        </div>
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
                <ul class="nav nav-tabs">
    
                    <li class="nav-item">
                        <a class="nav-link active" id="ip-address-tab" data-toggle="tab" href="#id-address-1" role="tab" aria-controls="id-address-1" aria-selected="false">IP Address</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="user-log-tab" data-toggle="tab" href="#user-log-1" role="tab" aria-controls="user-log-1" aria-selected="false">User Logs</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="user-login-log-tab" data-toggle="tab" href="#user-login-log-1" role="tab" aria-controls="user-login-log-1" aria-selected="false">User Login Logs</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="user-notification-tab" data-toggle="tab" href="#user-notification-1" role="tab" aria-controls="user-notification-1" aria-selected="false">User Notification Email</a>
                    </li>
                    @can('user-agency-permission')

                    <li class="nav-item">
                        <a class="nav-link" id="user-agency-tab" data-toggle="tab" href="#user-agency-1" role="tab" aria-controls="user-agency-1" onclick="getUserAgencyList(1)" aria-selected="false">User Agency</a>
                    </li>
                    @endcan
                    @can('user-location-permission')
                    <li class="nav-item">
                        <a class="nav-link" id="user-location-tab" data-toggle="tab" href="#user-location-1" role="tab" aria-controls="user-location-1" onclick="getUserLocationList(1)" aria-selected="false">User Location</a>
                    </li>
                    @endcan
                    @can('date-wise-user-permission')
                        @if($userDetails->agency_fk !="")
                            <li class="nav-item">
                                <a class="nav-link" id="date-wise-user-view-tab" data-toggle="tab" href="#date-wise-user-view-1" role="tab" aria-controls="date-wise-user-view-1" aria-selected="false" onclick="loadUserDateWiseUserView()">Date Wise User View</a>
                            </li>
                        @endif
                    @endcan
                </ul>
    
                <div class="tab-content">
                    
                    <div class="tab-pane fade active show" id="id-address-1" role="tabpanel" aria-labelledby="ip-address-tab">
                        <div class="row list-name m-3">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">IP Address</h4>
                            </div>
                            <div class="col-sm-3 offset-sm-3">
                                <a class="btn btn-success  btn-rounded btn-sm btn-fw pull-right addIpAddressModal" href="javascript:void(0)"><i class="mdi mdi-plus"></i>Add IP Address</a>
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

                    <div class="tab-pane fade" id="user-log-1" role="tabpanel" aria-labelledby="user-log-tab">
                        <div class="row list-name m-3">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">User Logs</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12" id="logList">
        
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="user-login-log-1" role="tabpanel" aria-labelledby="user-login-log-tab">
                        <div class="row list-name m-3">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">User Login Logs</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12" id="loginLogList">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="user-notification-1" role="tabpanel" aria-labelledby="user-notification-tab">
                        <div class="row list-name m-3">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">User Notification Email</h4>
                            </div>
                            <div class="col-sm-6">
                                
                                {{-- <a class="btn btn-success  btn-rounded btn-sm btn-fw pull-right add-user-notification-email-btn" style="margin: -11px;" data-whatever="@mdo" onclick="editUserNotificationEmail('{{ $id}}')" href="javascript:void(0)"> Edit User Notification Email</a> --}}
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12" id="user_notification_email">
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="user-agency-1" role="tabpanel" aria-labelledby="user-agency-tab">
                        <div class="row list-name m-3">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">User Agency</h4>
                            </div>
                            <div class="col-sm-6">

                                <a class="btn btn-success  btn-rounded btn-sm btn-fw pull-right add-user-agency-btn" style="margin: -11px;" data-whatever="@mdo"  href="javascript:void(0)"> Add User Agency</a>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12" id="user_agency_list_id">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="user-location-1" role="tabpanel" aria-labelledby="user-location-tab">
                        <div class="row list-name m-3">
                            <div class="col-sm-6 card-title">
                                <h4 class="card-title">User Location</h4>
                            </div>
                            <div class="col-sm-6">
                                <a class="btn btn-success  btn-rounded btn-sm btn-fw pull-right add-user-location-btn" style="margin: -11px;" data-whatever="@mdo"  href="javascript:void(0)"> Add User Location</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12" id="user_location_list_id">

                                </div>
                            </div>
                        </div>
                    </div>

                    @include('user._partial.date_user_wise_access.date_user_wise_access_list')

                </div>
            </div>
        </div>
    </div>

    <!-- ip address modal start -->
    <div class="modal fade" id="exampleModal-5" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5><span class="modal-title">Add IP Address</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='{{ url("user-ip-address-save")}}' name="adduser" method="post" id="submitIpAddress">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="user_id" id="user_id" value="{{ $id }}">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="ip_address">IP Address<span class="error">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Enter IP Address" id="ip_address" name="ip_address" value="<?php echo old('ip_address'); ?>">
                                <span id="ip_address_error" class="error mt-2"><?php echo $errors->add_user->first('ip_address'); ?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="type">Type<span class="error">*</span></label>
                            <div class="col-sm-9">
                                <div class="d-flex">
                                    <div class="mr-3">
                                        <input type="radio" class="" id="block" name="type" value="BLOCK"><label for="block" class="ml-1">Block</label>
                                    </div>
                                    <div>
                                        <input type="radio" class="" id="unblock" name="type" value="UNBLOCK"><label for="unblock" class="ml-1">Unblock</label>
                                    </div>
                                </div>
                                <span id="type_error" class="error mt-2"><?php echo $errors->add_user->first('type'); ?></span>
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
    <div class="modal fade" id="exampleModal-6" tabindex="-1" aria-labelledby="ModalLabels" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="ModalLabels"><span class="modal-title">Edit IP Address</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='{{ url("user-ip-address-save")}}' name="adduser" method="post" id="submitEditIpAddress">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="user_id" id="user_id_edit" value="{{ $id }}">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="ip_address_edit">IP Address<span class="error">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Enter IP Address" id="ip_address_edit" name="ip_address_edit" value="<?php echo old('ip_address'); ?>">
                                <span id="ip_address_edit_error" class="error mt-2"><?php echo $errors->add_user->first('ip_address'); ?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="type_edit">Type<span class="error">*</span></label>
                            <div class="col-sm-9">
                                <div class="d-flex">
                                    <div class="mr-3">
                                        <input type="radio" class="" name="type_edit" value="BLOCK"><label for="block" class="ml-1">Block</label>
                                    </div>
                                    <div>
                                        <input type="radio" class="" name="type_edit" value="UNBLOCK"><label for="unblock" class="ml-1">Unblock</label>
                                    </div>
                                </div>
                                <span id="type_edit_error" class="error mt-2"><?php echo $errors->add_user->first('type'); ?></span>
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


    <div class="modal fade" id="modal-defualt" tabindex="-1" aria-labelledby="exampleModalLabel-2" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Add Agency</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo URL::to('/'); ?>/agency-add" method="post" enctype="multipart/form-data" id="submitId">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="agency_id">Agency </label><span style="color:red;">*</span>
                            <select name="agency_id" class="form-control" id="agencyId">
                                <option value="">Select Agency</option>
                                <?php if (!empty($agency_list)) {
                                    foreach ($agency_list as $val) { ?>
                                        <option value="<?php echo $val->id; ?>"><?php echo $val->agency_name; ?></option>
                                <?php }
                                } ?>
                            </select>
                            <span id="agency_error" style="color:red"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        
    <div class="modal fade " id="exampleModal-task" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closed_id_task">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form  method="post" id="task_patient_id">
                    @csrf
                    <input type="hidden" name="id" value="{{ $userDetails->id}}">
                    <div class="modal-body">
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">First Name<span class="error">*</span>:</label>
                            <input type="text" name="first_name" class="form-control" id="first_name_id" autocomplete="off" value="{{ $userDetails->first_name}}">
                            <span id="first_name_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Last Name<span class="error">*</span>:</label>
                            <input type="text" name="last_name" class="form-control" id="last_name_id" autocomplete="off" value="{{ $userDetails->last_name}}">
                            <span id="last_name_error" class="error"></span>
                        </div>

                        @if($userDetails->email !="")
                        @php
                        $explode = explode('@',$userDetails->email);
                        @endphp
                        @endif
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Email<span class="error">*</span>:</label>
                            <div style="display:flex">
                                <input type="text" style="width:70%" name="email" class="form-control" id="email_id" autocomplete="off" value="{{ $explode[0]}}"> <label for="recipient-name" style="margin-left:10px;" class="col-form-label">{{ '@'.$explode[1]}}</label>
                                <input type="hidden" name="domain" value="{{ '@'.$explode[1]}}" >
                            </div>

                            <span id="email_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Phone:</label>
                            <input type="text" name="phone" class="form-control" id="phone_id" autocomplete="off" value="{{ $userDetails->phone}}">
                            <span id="phone_error" class="error"></span>
                        </div>

                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Ext:</label>
                            <input type="text" name="ext_no" class="form-control" id="ext_no_id" autocomplete="off" value="{{ $userDetails->ext}}">
                            <span id="ext_no_error" class="error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="updateUser()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-change" tabindex="-1" aria-labelledby="exampleModalLabel-2c" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2c">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo URL::to('/'); ?>/user/update-password" method="post" enctype="multipart/form-data" id="changeId">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="new_password">New Password </label><span style="color:red;">*</span>
                            <input type="password" id="newpass_id" name="new_password" class="form-control">
                            <span id="newpass_error" style="color:red"></span>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password </label><span style="color:red;">*</span>
                            <input type="password" name="confirm_password" id="confirmpass_id" class="form-control">
                            <span id="confirmpass_error" style="color:red"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="user_agency_popup" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='' name="add-user-agency" method="post" id="submitUserAgency">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="user_agency_mid" name="user_agency_mid" value="">
                        <input type="hidden" name="user_id" id="user_id_agency" value="{{ $id }}">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="user_agency_id">Agency <span  style="color:red;">*</span></label>
                            <div class="col-sm-9">

                                <select name="user_agency_id" class="form-control select2" id="user_agency_id">
                                    <option value="">Select Agency</option>
                                    @if(!empty($agency_list))
                                        @foreach ($agency_list as $val)
                                            <option value="{{$val->id}}">{{$val->agency_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span id="user_agency_error" class="error mt-2"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="saveUserAgency" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- User Location Modal -->
    <div class="modal fade" id="user_location_popup" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='' name="add-user-location" method="post" id="submitUserLocation">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="user_location_mid" name="user_location_mid" value="">
                        <input type="hidden" name="user_id" id="user_id_location" value="{{ $id }}">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="user_location_id">Location <span  style="color:red;">*</span></label>
                            <div class="col-sm-9">
                                <select name="user_location_id" class="form-control select2" id="user_location_id">
                                    <option value="">Select Location</option>
                                    @if(!empty($location_list))
                                        @foreach ($location_list as $val)
                                            <option value="{{$val->id}}">{{$val->location_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span id="user_location_error" class="error mt-2"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="saveUserLocation" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('user._partial.date_user_wise_access.modal.add_date_user_wise_access_modal')
    @include('user._partial.date_user_wise_access.modal.edit_date_user_wise_access_modal')
    <!-- ip address modal start -->
    @include('include/footer')
    <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
    <script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
    <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
    <script src="{{ asset('assets/js/select2.js')}}"></script>
    <script>
        var _LOAD_DATE_WISE_USER_ACCESS_LIST ="{{ url('date-wise-agency-access/load-date-wise-user-access-list')}}";
        var _SAVE_USER_WISE_DATE_PERMISSION = "{{ url('/date-wise-agency-access/save-date-view-user-view')}}";
        var _EDIT_DATE_WISE_USER_ACCESS = "{{ url('/date-wise-agency-access/edit-date-view-user-view')}}";
        var _UPDATE_USER_WISE_DATE_PERMISSION = "{{ url('/date-wise-agency-access/update-date-view-user-view') }}";
        var _DELETE_USER_WISE_DATE_PERMISSION =  '{{ url("/date-wise-agency-access/delete-date-view-user-view")}}';
        var _CHECK_EXISTING_ENTRIES_USER = '{{ url("/date-wise-agency-access/check-existing-entries-user")}}';
        var _SET_PERMANENT_RESTRICTION_USER = '{{ url("/date-wise-agency-access/set-permanent-restriction-user")}}';
        var _REMOVE_PERMANENT_RESTRICTION_USER = '{{ url("/date-wise-agency-access/remove-permanent-restriction-user")}}';
        var _USER_ID ="{{ $userDetails->id}}";
        var _CSRF_TOKEN = "{{ csrf_token()}}";
        var ALL_PERMISSION = {!! json_encode($allPermission) !!};
        $(":input").inputmask();
        $(document).ready(function() {
            
            userNotificationEmailList(1);
        });
        function userNotificationEmailList(page) {
            $.ajax({
                url: "{{ url('user-notification-email-list')}}",
                type: "GET",
                data: {
                    'type': 'user-notifiction-email',
                    'user_id': "{{$id}}",
                    'page': page,
                },
                success: function(response) {
                    $('#user_notification_email').html("");
                    $('#user_notification_email').html(response);
                },
                error:function(xhr){
                    showErrorAndLoginRedirection(xhr);
                }
            });
            return false;
        }

        $(document).on('click','#notification-email-saveId',function(){
            var userId = {{$id}};
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
                selectedCaregiversId.push($(this).attr('data-id'));
            });

            var cnt = 0;

            if (selectedCaregivers.length == 0 && selectedPatients.length == 0) {
                $('#notification_email_error').html("Patient or Caregiver is required");
                cnt = 1;
            }

            if (cnt == 1) {
                return false;
            } else {
                var forms = $('#addnotificationemail')[0];
                var newForms = new FormData(forms);
                
                newForms.append('patient_id', selectedPatientId);
                newForms.append('caregivers_id', selectedCaregiversId);
                newForms.append('user_id', userId);

                $.ajax({
                    url: "{{ url('user-notification-email-save')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                        $('#add-user-notification-email-popup').modal('hide');
                        $('#addnotificationemail')[0].reset();
                        userNotificationEmailList(1);
                    },
                    error: function(xhr, status, error) {
                        showErrorAndLoginRedirection(xhr);
                    }
                });

            }
        });
    </script>
    <script>
            // new
        $(document).on('click', '#dropdownnew a', function() {
            $('#dropdownMenuSizeButton3').html($(this));
        });

        $(document).on('click', '.addIpAddressModal', function() {
            $("input[name='type']").attr('checked', false);
            $('#submitIpAddress')[0].reset();
            $("#exampleModal-5").modal('show');
        });

        function sendInvitation(id) {
            var url = "{{url('/send-invitation')}}";
            $.confirm({
                title: 'Send Invitation',
                columnClass: "col-md-6",
                content: 'Are you sure send invitation for this user?',
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Send',
                        btnClass: 'btn-danger',
                        action: function() {
                            window.location.href = url + '/' + id;
                        }
                    },
                    cancel: function() {

                    },
                },
            });
        }

        function deleteUserData(id) {
            var url = "{{url('/delete_user?i=')}}";
            $.confirm({
                title: 'Delete',
                columnClass: "col-md-6",
                content: 'Are you sure delete this record?',
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            window.location.href = url + id;
                        }
                    },
                    cancel: function() {

                    },
                },
            });
        }

            // new
    </script>
    <script>
        $('#submitId').submit(function(e) {
            var agency = $('#agencyId').val();
            var cnt = 0;
            $('#agency_error').html(" ");
            if (agency == '') {

                $('#agency_error').html(" Required !");
                cnt = 1;
            }
            if (cnt == 1) {
                return false;
            } else {
                return true;
            }

        });

        $('#changeId').submit(function() {
            var newpass_id = $('#newpass_id').val();
            var confirmpass_id = $('#confirmpass_id').val();

            $('#newpass_error').html("");
            $('#confirmpass_error').html("");
            var cnt = 0;
            var passwordExpression = /^(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z])(?=\D*\d)[a-zA-Z\d!@#$%&*]+$/;
            if (newpass_id == '') {
                $('#newpass_error').html("Required !");
                cnt = 1;
            }
            if (confirmpass_id == '') {
                $('#confirmpass_error').html("Required !");
                cnt = 1;
            }
            if (passwordExpression.test(newpass_id)) {

            } else {
                $('#newpass_error').html(
                    'Password must contain at least 8 characters including 1 uppercase, 1 lowercase, a number and symbol'
                );
                cnt = 1;
            }

            if (newpass_id != confirmpass_id) {

                $('#confirmpass_error').html(
                    'Confirm password does not match with new password.'
                );
                cnt = 1;
            }

            if (cnt == 1) {
                return false;
            } else {
                return true;
            }
        });

        function getStatus(record_id) {
            var id = record_id;
            var status = $('#status_id').val();

            if (status != '') {
                var msg = "you want to " + status + ' this user?';
                $.confirm({
                    title: 'Are you sure?',
                    columnClass: "col-md-6",
                    type: 'blue',
                    content: msg,
                    buttons: {
                        formSubmit: {
                            text: 'Yes',
                            btnClass: 'btn-danger',
                            action: function() {
                                $.ajax({
                                    url: "{{ url('user-change-status') }}",
                                    type: "POST",
                                    data: {
                                        'status': status,
                                        'user_id': id,
                                        '_token': "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                       
                                        var status = '';
                                        $("#status_id").html('');
                                        if (res.data.status == 'active') {
                                            status =
                                                '<span class="badge badge-success">Active</span>';
                                            $("#status_id").html('<option value="">Select</option><option value="inactive">InActive</option><option value="block">Block</option>');

                                        }
                                        if (res.data.status == 'inactive') {
                                            status =
                                                '<span class="badge badge-danger">Inactive</span>';
                                            $("#status_id").html('<option value="">Select</option><option value="active">Active</option><option value="unblock">Unblock</option>');
                                        }
                                        if (res.data.status == 'block') {
                                            status =
                                                '<span class="badge badge-danger">Block</span>';
                                            $("#status_id").html('<option value="">Select</option><option value="unblock">Unblock</option><option value="active">Active</option>');
                                        }

                                        if (res.data.status == 'unblock') {
                                            status =
                                                '<span class="badge badge-info">Unblock</span>';
                                            $("#status_id").html('<option value="">Select</option><option value="block">Block</option><option value="inactive">Inactive</option>');
                                        }
                                        $('#status' + id).html(status);
                                        toastr.success(res.error_msg);

                                    },
                                    error:function(xhr){
                                        showErrorAndLoginRedirection(xhr);
                                    }
                                })
                            }
                        },
                        cancel: function() {
                            //close
                        },
                    },
                    onContentReady: function() {
                        // bind to events

                    }
                });
            }
        }

        $(document).ready(function() {
            var userId = $('#user_id').val();
            /**
             * User log Table Initialize
             */
            getData(1);
            /**
             * User login log Table Initialize
             */
            getDataLoginLog(1);
            ipAddressList(userId);
        });

        function getData(page) {

            var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

            $.ajax({
                method: 'GET',
                url: "{{ url('user-view-logs') }}" + "?page=" + page,
                data: {
                    'id': "{{$id}}",
                    '_token': "{{ csrf_token() }}"
                },
                success: function success(response) {

                    $('.order-listing-loader').attr('style', 'display:none');
                    $('#logList').html("");
                    $('#logList').html(response);
                },
                error: function error(_error) {
                    toastr.error('Something happened. Try again');
                }
            });
        }
        /**
         * User login log
         */
        function getDataLoginLog(page) {

            var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

            $.ajax({
                method: 'GET',
                url: "{{ url('user-view-login-logs') }}" + "?page=" + page,
                data: {
                    'id': "{{$id}}",
                    '_token': "{{ csrf_token() }}"
                },
                success: function success(response) {
                    $('.order-listing-loader').attr('style', 'display:none');
                    $('#loginLogList').html("");
                    $('#loginLogList').html(response);
                    $("#ipAdd").text($("#loginLogList div table tr td:eq(1)").text().replace(/\s/g, ''));
                    $("#lastLDate").text($("#loginLogList div table tr td:eq(5)").text());
                },
                error: function error(_error) {
                    toastr.error('Something happened. Try again');
                }
            });
        }

        $(document).on('click', '.log-pegination .pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });

        $(document).on('click', '.login-log-pegination .pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getDataLoginLog(page);
        });

            // chnage status
        function changeStatus(record_id) {
            var id = "{{request('id')}}";
            if (record_id == 1) {
                var status = 'N'
                var msg = 'No';
            } else {
                var status = 'Y'
                var msg = 'Yes';
            }
            if (status != '') {
                var msg = "Limit Access " + msg + "";
                $.confirm({
                    title: 'Are you sure?',
                    columnClass: "col-md-6",
                    type: 'blue',
                    content: msg,
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-danger',
                            action: function() {
                                $.ajax({
                                    url: "{{ url('chnagestatus') }}",
                                    type: "POST",
                                    data: {
                                        'status': status,
                                        'user_id': id,
                                        '_token': "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        if (res.data.status == 'Y') {
                                            status =
                                                '<span class="badge badge-success" onclick="changeStatus(1)">Yes</span>';
                                        }
                                        if (res.data.status == 'N') {
                                            status =
                                                '<span class="badge badge-danger" onclick="changeStatus(0)">No</span>';
                                        }

                                        $('#chnagestatus').html(status);
                                    },
                                    error:function(xhr){
                                        showErrorAndLoginRedirection(xhr);
                                    }
                                })
                            }
                        },
                        cancel: function() {},
                    },
                    onContentReady: function() {}
                });
            }
        }

        function ExmedcChangeStatus(record_id) {
            var id = "{{request('id')}}";
            if (record_id == 1) {
                var status = record_id;
                var msg = 'No';
            } else {
                var status = record_id;
                var msg = 'Yes';
            }
            if (record_id != '') {
                var msg = "Exmedc Access " + msg + "";
                $.confirm({
                    title: 'Are you sure?',
                    columnClass: "col-md-6",
                    type: 'blue',
                    content: msg,
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-danger',
                            action: function() {
                                $.ajax({
                                    url: "{{ url('exmedc-chnage-status') }}",
                                    type: "POST",
                                    data: {
                                        'status': record_id,
                                        'user_id': id,
                                        '_token': "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        if (res.data.status == 2) {
                                            status =
                                                '<span class="badge badge-danger" onclick="ExmedcChangeStatus(1)">No</span>';
                                        }
                                        if (res.data.status == 1) {
                                            status =
                                                '<span class="badge badge-success" onclick="ExmedcChangeStatus(2)">Yes</span>';
                                        }
                                        $('#exmedcchnagestatus').html(status);
                                    },
                                    error:function(xhr){
                                        showErrorAndLoginRedirection(xhr);
                                    }
                                })
                            }
                        },
                        cancel: function() {},
                    },
                    onContentReady: function() {}
                });
            }
        }

        function HospitalChangeStatus(record_id) {
            var id = "{{request('id')}}";
            if (record_id == 1) {
                var status = record_id;
                var msg = 'No';
            } else {
                var status = record_id;
                var msg = 'Yes';
            }
            if (record_id != '') {
                var msg = "Hospital Access " + msg + "";
                $.confirm({
                    title: 'Are you sure?',
                    columnClass: "col-md-6",
                    type: 'blue',
                    content: msg,
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-danger',
                            action: function() {
                                $.ajax({
                                    url: "{{ url('hospital-chnage-status') }}",
                                    type: "POST",
                                    data: {
                                        'status': record_id,
                                        'user_id': id,
                                        '_token': "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        if (res.data.status == 2) {
                                            status =
                                                '<span class="badge badge-danger" onclick="HospitalChangeStatus(1)">No</span>';
                                        }
                                        if (res.data.status == 1) {
                                            status =
                                                '<span class="badge badge-success" onclick="HospitalChangeStatus(2)">Yes</span>';
                                        }
                                        $('#hospitalchnagestatus').html(status);
                                    },
                                    error:function(xhr){
                                        showErrorAndLoginRedirection(xhr);
                                    }
                                })
                            }
                        },
                        cancel: function() {},
                    },
                    onContentReady: function() {}
                });
            }
        }

        function getCheckedNew(id) {
            if (id != '') {
                var msg = "Allow to access NyBest Users?";
                $.confirm({
                    title: 'Are you sure?',
                    columnClass: "col-md-6",
                    type: 'blue',
                    content: msg,
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-danger',
                            action: function() {
                                $.ajax({
                                    url: "{{ url('user-access') }}",
                                    type: "POST",
                                    data: {
                                        'user_id': id,
                                        '_token': "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        toastr.success(res.error_msg);
                                    },
                                    error:function(xhr){
                                        showErrorAndLoginRedirection(xhr);
                                    }
                                })
                            }
                        },
                        cancel: function() {
                            //close
                        },
                    },
                    onContentReady: function() {
                        // bind to events

                    }
                });
            }
        }

        function getAgencyUpdateRecord(id, agencyId) {
            $('#previd').val(id);
            $.ajax({
                async: false,
                global: false,
                url:"{{ url('getUserListByAgencyId')}}/" + agencyId + '/' + id,
                type: "GET",
                success: function(response) {
                    $('#selectId').html(response);
                }
            });
            $('#agencymodal').modal('show');
        }

        function getEMCUpdateRecord(id) {
            $('#emcprevid').val(id);
            $.ajax({
                async: false,
                global: false,
                url: "{{ url('getUserListByEmcId')}}/" + id,
                success: function(response) {
                    $('#emcselectId').html(response);
                }
            });
            $('#emcmodal').modal('show');
        }
    </script>

    <script>
        function validateIPAddress(ip) {
            var expr = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            return expr.test(ip);
        }

        function ipAddressList(id) {
            $.ajax({
                url: "{{ url('user-wise-ip-list')}}",
                type: "GET",
                data: {
                    'user_id': {
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

        /* Add Ip address */
        $('#saveIPAddress').click(function(e) {

            var ip_address = $('#ip_address').val();
            error = 0;
            if (ip_address.trim() == '') {
                $('#ip_address_error').html("Please enter IP Address");
                error = 1;
            } else if (!validateIPAddress(ip_address)) {
                $('#ip_address_error').html("Invalid IP Address");
                error = 1;
            } else {
                $('#ip_address_error').html("");
            }

            if ($('input[name="type"]:checked').length == 0) {
                $('#type_error').html("Please select Type");
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
                    url: "{{ url('user-ip-address-save')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                        var userId = $('#user_id').val();
                        $('#exampleModal-5').modal('hide');
                        $('#submitIpAddress')[0].reset();
                        ipAddressList(userId);
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg);
                    }
                });
            }
        });

        /* Update Ip address */
        $('body').on('click', '.edit-ip-address', function(e) {
            var dataId = $(this).attr('data-id');
            var editid = $("#id").val(dataId);
            $('#exampleModal-6').modal('show');
            $('#ip_address_edit_error').html("");
            $.ajax({
                url: "{{ url('user-ip-edit')}}",
                type: "GET",
                data: {
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
                $('#ip_address_edit_error').html("Plese enter IP Address");
                error = 1;
            } else if (!validateIPAddress(ip_address)) {
                $('#ip_address_error').html("Invalid IP Address");
                error = 1;
            } else {
                $('#ip_address_error').html("");
            }

            if ($('input[name="type_edit"]:checked').length == 0) {
                $('#type_edit_error').html("Please select Type");
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
                    url: "{{ url('user-ip-update')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                        var userId = $('#user_id').val();
                        $('#exampleModal-6').modal('hide');
                        ipAddressList(userId);
                    },
                    error: function(xhr, status, error) {
                        showErrorAndLoginRedirection(xhr);
                    }
                });
            }
        });

        /* Delete Ip address */
        $('body').on('click', '.delete-ip-address', function(e) {
            var msg = "you want to delete this IP Address?";
            var id = $(this).attr('data-id');

            var userId = $('#user_id').val();

            $.confirm({
                title: 'Are you sure?',
                columnClass: "col-md-6",
                type: 'blue',
                content: msg,
                buttons: {
                    formSubmit: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '{{ url("user-ip-delete")}}',
                                type: "POST",
                                data: {
                                    'id': id,
                                    'user_id': userId,
                                    '_token': "{{ csrf_token()}}"
                                },
                                success: function(res) {
                                    toastr.success(res.error_msg);
                                    ipAddressList(userId);
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
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

        $('body').on('click', '.pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            var explode = $(this).attr('href').split('?');

            var explodes = explode[1].split('&');
            var type = explodes[0].split('type=')[1];

            if (type == 'domain') {

                domainList(page);
            }
        });

        function updateUser(){
            var first_name = $('#first_name_id').val();
            var last_name = $('#last_name_id').val();
            var email = $('#email_id').val();
            var phone = $('#phone_id').val();
            var cnt =0;
            var emailRegex = /^[A-Za-z0-9`!#$%^&*()_=+\\';:\/?>.<,-]*$/;
            var number = /^[0-9]+$/;
            $('#first_name_error').html('');
            $('#last_name_error').html('');
            $('#email_error').html('');
            $('#phone_error').html('');
            
            if(first_name.trim() ==''){
                $('#first_name_error').html("First Name is required");
                cnt =1;
            }

            if(last_name.trim() ==''){
                $('#last_name_error').html("Last Name is required");
                cnt =1;
            }

            if(email.trim() ==''){
                $('#email_error').html("Email is required");
                cnt =1;
            }

            if(email.trim() !=''){
                if(!email.match(emailRegex)){
                    $('#email_error').html("Invalid Email Name");
                    cnt =1;
                }
            }
            
            if(phone.trim() ==''){
                // $('#phone_error').html("Phone is required");
                // cnt =1;
            }
            if(phone.trim() !=''){
                if(!phone.match(number)){
                    $('#phone_error').html("Only number allowed");
                    cnt =1;
                }
            }

            if(cnt ==1){
                return false;
            }else{
                var forms = $('#task_patient_id')[0];
                var formData = new FormData(forms);
                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ url('agency-user-update')}}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        
                        toastr.success(response.error_msg);
                        $('#view_first_id').html(response.data.first_name);
                        $('#view_last_id').html(response.data.last_name);
                        $('#view_email_id').html(response.data.email);
                        $('#view_phone_id').html(response.data.phone);
                        $('#view_ext_id').html(response.data.ext);
                        $('#exampleModal-task').modal('hide');
                        
                    },
                    error: function(xhr, status, error) {
                        showErrorAndLoginRedirection(xhr);
                    }
                });
            }
        }

        function changeRecordType(){
            var recordType  =$('#record_access  option:selected').val();
            if(recordType   !=""){
                var formData=   new  FormData();
                formData.append('_token','{{  csrf_token()  }}');
                formData.append('record_type',recordType);
                formData.append('id','{{  $userDetails->id  }}');

                $.ajax({
                    url: "{{ url('change-record-type')}}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                    },
                    error: function(xhr, status, error) {
                        showErrorAndLoginRedirection(xhr);
                    }
                });
            }
        }

        function getUserAgencyList(page){
            $.ajax({
                url: "{{ url('user-agency-list')}}",
                type: "GET",
                data: {
                    'user_id': "{{$id}}",
                    'page': page,
                },
                success: function(response) {
                    $('#user_agency_list_id').html("");
                    $('#user_agency_list_id').html(response);
                }
            });
            return false;
        }

        $('.add-user-agency-btn').click(function(){
            $('#useragencylabel').html('Add User Agency');
            $('#user_agency_id').val("");
            $('#user_agency_popup').modal('show');
            $('.error').html("");
            $('#user_agency_mid').val('');
        });

        $('#saveUserAgency').click(function(e) {
            $('#user_agency_error').html("");
            var userAgency = $('#user_agency_id').val();
            error = 0;
            if (userAgency == '' ) {
                $('#user_agency_error').html("Please Select Agency");
                error = 1;
            }
            if (error == 1) {
                return false;
            } else {
                var forms = $('#submitUserAgency')[0];
                var newForms = new FormData(forms);
                newForms.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ url('user-agency-save')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                        $('#user_agency_popup').modal('hide');
                        $('#submitUserAgency')[0].reset();
                        getUserAgencyList(1);
                    },
                    error: function(xhr, status, error) {
                      showErrorAndLoginRedirection(xhr);
                    }
                });
            }
        });
        function editUserAgency(id){
            $.ajax({
                url: "{{ url('user-agency-edit')}}",
                type: "GET",
                data: {
                    'id': id,
                },
                success: function(response) {
                    var data = response.data;
                    $('#user_agency_mid').val(id);
                    
                    $('#user_agency_id').val(data.agency_details.id).trigger('change');
                    $('#useragencylabel').html("Edit User Agency");
                    $('#user_agency_popup').modal('show');
                    $('.error').html("");
                    
                },error:function(xhr){
                    showErrorAndLoginRedirection(xhr);
                }
            });
        }
        function deleteUserAgency(id){
            
            var msg = "You want to delete this user agency?";
            
            $.confirm({
                title: 'Are you sure?',
                columnClass: "col-md-6",
                type: 'blue',
                content: msg,
                buttons: {
                    formSubmit: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '{{ url("user-agency-delete")}}',
                                type: "get",
                                data: {
                                    'id': id,
                                },
                                success: function(res) {
                                    toastr.success(res.error_msg);
                                    getUserAgencyList(1);
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            })
                        }
                    },
                    cancel: function() {
                        
                    },
                },
                onContentReady: function() {
                }
            });
        }

        $(document).on('click', '.user-agency-pegination .pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getUserAgencyList(page);
        });

        // User Location Functions
        function getUserLocationList(page){
            $.ajax({
                url: "{{ url('user-location-list')}}",
                type: "GET",
                data: {
                    'user_id': "{{$id}}",
                    'page': page,
                },
                success: function(response) {
                    $('#user_location_list_id').html("");
                    $('#user_location_list_id').html(response);
                }
            });
            return false;
        }

        $('.add-user-location-btn').click(function(){
            $('#userlocationlabel').html('Add User Location');
            $('#user_location_id').val("").trigger('change');
            $('#user_location_popup').modal('show');
            $('.error').html("");
            $('#user_location_mid').val('');
        });

        $('#saveUserLocation').click(function(e) {
            $('#user_location_error').html("");
            var userLocation = $('#user_location_id').val();
            error = 0;
            if (userLocation == '' ) {
                $('#user_location_error').html("Please Select Location");
                error = 1;
            }
            if (error == 1) {
                return false;
            } else {
                var forms = $('#submitUserLocation')[0];
                var newForms = new FormData(forms);
                newForms.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ url('user-location-save')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                        var $select = $('#user_location_id');
                        var isEdit = $('#user_location_mid').val() != '';
                        if (!isEdit) {
                            $select.find('option[value="' + userLocation + '"]').remove();
                            $select.val("").trigger('change');
                        }
                        $('#user_location_popup').modal('hide');
                        $('#submitUserLocation')[0].reset();
                        getUserLocationList(1);
                    },
                    error: function(xhr, status, error) {
                      showErrorAndLoginRedirection(xhr);
                    }
                });
            }
        });

        function editUserLocation(id){
            $.ajax({
                url: "{{ url('user-location-edit')}}",
                type: "GET",
                data: {
                    'id': id,
                },
                success: function(response) {
                    var data = response.data;
                    var locationId = data.location_details.id;
                    var locationName = data.location_details.location_name;
                    var $select = $('#user_location_id');
                    if ($select.find('option[value="' + locationId + '"]').length === 0) {
                        $select.append(new Option(locationName, locationId));
                    }
                    $('#user_location_mid').val(id);
                    $select.val(locationId).trigger('change');
                    $('#userlocationlabel').html("Edit User Location");
                    $('#user_location_popup').modal('show');
                    $('.error').html("");
                },error:function(xhr){
                    showErrorAndLoginRedirection(xhr);
                }
            });
        }

        function deleteUserLocation(id, locationId, locationName){
            var msg = "You want to delete this user location?";
            $.confirm({
                title: 'Are you sure?',
                columnClass: "col-md-6",
                type: 'blue',
                content: msg,
                buttons: {
                    formSubmit: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '{{ url("user-location-delete")}}',
                                type: "get",
                                data: {
                                    'id': id,
                                },
                                success: function(res) {
                                    toastr.success(res.error_msg);
                                    var $select = $('#user_location_id');
                                    if (locationId && $select.find('option[value="' + locationId + '"]').length === 0) {
                                        var newOption = new Option(locationName, locationId);
                                        $select.append(newOption);
                                        var options = $select.find('option:not([value=""])').sort(function(a, b) {
                                            return $(a).text().localeCompare($(b).text());
                                        });
                                        $select.find('option:not([value=""])').remove();
                                        $select.append(options);
                                        $select.trigger('change');
                                    }
                                    getUserLocationList(1);
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            })
                        }
                    },
                    cancel: function() {
                    },
                },
                onContentReady: function() {
                }
            });
        }

        $(document).on('click', '.user-location-pegination .pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getUserLocationList(page);
        });

        $(document).on("change", ".patientPageDesign", function() {
            var patient_page = $(this).prop('checked') == true ? 1 : 0;
            var user_id = {{$id}};
            $.confirm({
                title: 'Are you sure?',
                content: 'you want to switch the patient view detail page?',
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("user-page-detail-change")}}',
                                data: {
                                    'patient_page': patient_page,
                                    'user_id': user_id,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.patient_page').val(patient_page)
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        //close
                        var lastStatus = $('#patientPageDesign').val();
                        if(lastStatus ==1){
                            $('#patientPageDesign').prop("checked",true);
                        }else{
                            $('#patientPageDesign').prop("checked",false);
                        }
                    },
                },
            });
        });

        $(document).on("change", ".show_in_directory", function() {
            var show_in_directory = $(this).prop('checked') == true ? 1 : 0;
            var user_id = {{$id}};
            if(show_in_directory == 0){
                content = 'you want to hide the user from Directory List?';
            }else{
                content = 'you want to show the user from Directory List?';
            }
            $.confirm({
                title: 'Are you sure?',
                content: content,
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("user-directory-status-change")}}',
                                data: {
                                    'show_in_directory': show_in_directory,
                                    'user_id': user_id,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.show_in_directory').val(show_in_directory)
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        //close
                        var lastStatus = $('#show_in_directory').val();
                        if(lastStatus ==1){
                            $('#show_in_directory').prop("checked",true);
                        }else{
                            $('#show_in_directory').prop("checked",false);
                        }
                    },
                },
            });
        });

        $(document).on("change", ".show_hub", function() {
            var show_hub = $(this).prop('checked') == true ? 1 : 0;
            var user_id = {{$id}};
            if(show_hub == 0){
                content = 'Would you like to deactivate your access to the Hub module?';
            }else{
                content = 'Would you like to activate your access to the Hub module?';
            }
            $.confirm({
                title: 'Are you sure?',
                content: content,
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("user-hub-status-change")}}',
                                data: {
                                    'show_hub': show_hub,
                                    'user_id': user_id,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.show_hub').val(show_hub)
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        //close
                        var lastStatus = $('#show_hub').val();
                        if(lastStatus ==1){
                            $('#show_hub').prop("checked",true);
                        }else{
                            $('#show_hub').prop("checked",false);
                        }
                    },
                },
            });
        });

        $(document).on("change", ".view_ssn_hub", function() {
            var show_hub = $(this).prop('checked') == true ? 1 : 0;
            var user_id = {{$id}};
            if(show_hub == 0){
                content = 'Do you want to turn off your access to the Hub SSN view?';
            }else{
                content = 'Do you want to turn on your access to the Hub SSN view?';
            }
            $.confirm({
                title: 'Are you sure?',
                content: content,
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("user-hub-view-ssn")}}',
                                data: {
                                    'show_hub': show_hub,
                                    'user_id': user_id,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.view_ssn_hub').val(show_hub)
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        //close
                        var lastStatus = $('#view_ssn_hub').val();
                    
                        if(lastStatus ==1){
                            $('#view_ssn_hub').prop("checked",true);
                        }else{
                            $('#view_ssn_hub').prop("checked",false);
                        }
                    },
                },
            });
        });

        $(document).on("change", ".two_fact_auth", function() {
            var two_fact_auth = $(this).prop('checked') == true ? 'Y' : 'N';
            var user_id = "{{$id}}";
            if(two_fact_auth == "N"){
                content = 'Would you like to disable two factor authentication?';
            }else{
                content = 'Would you like to enable two factor authentication?';
            }
            $.confirm({
                title: 'Are you sure?',
                content: content,
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("user-edit-two-factor-authentication")}}',
                                data: {
                                    'two_fact_auth': two_fact_auth,
                                    'user_id': user_id,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.two_fact_auth').val(two_fact_auth)
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        //close
                        var lastStatus = $('#two_fact_auth').val();
                        
                        if(lastStatus ==1){
                            $('#two_fact_auth').prop("checked",true);
                        }else{
                            $('#two_fact_auth').prop("checked",false);
                        }
                    },
                },
            });
        });

        $(document).on("change", ".creator_email_noti_toggle", function() {
            var creator_email_noti_toggle = $('.creator_email_noti_toggle').prop('checked') == true ? '0' : '1';
            var user_id = "{{$id}}";
            if(creator_email_noti_toggle == "1"){
                content = 'Would you like to disable Creator Email Notification?';
            }else{
                content = 'Would you like to enable Creator Email Notification?';
            }
            $.confirm({
                title: 'Are you sure?',
                content: content,
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("creator-email-noti-toggle")}}',
                                data: {
                                    'creator_email_noti_toggle': creator_email_noti_toggle,
                                    'user_id': user_id,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.creator_email_noti_toggle').val(creator_email_noti_toggle)
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        //close
                        var lastStatus = $('#creator_email_noti_toggle').val();
                        
                        if(lastStatus ==1){
                            $('#creator_email_noti_toggle').prop("checked",true);
                        }else{
                            $('#creator_email_noti_toggle').prop("checked",false);
                        }
                    },
                },
            });
        });

        $(document).on("change", ".is_telehealth", function() {
            var is_telehealth = $('.is_telehealth').prop('checked') == true ? 1 : 0;
            var user_id = "{{$id}}";
            if(is_telehealth == 0){
                content = 'Would you like to disable Telehealth File Access?';
            }else{
                content = 'Would you like to enable Telehealth File Access?';
            }
            $.confirm({
                title: 'Are you sure?',
                content: content,
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("user-telehealth-toggle")}}',
                                data: {
                                    'user_id': user_id,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.is_telehealth').val(is_telehealth);
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        var lastStatus = $('#is_telehealth').val();
                        if(lastStatus == 1){
                            $('#is_telehealth').prop("checked", true);
                        }else{
                            $('#is_telehealth').prop("checked", false);
                        }
                    },
                },
            });
        });

        $(document).on("change", ".is_mdo", function() {
            var is_mdo = $('.is_mdo').prop('checked') == true ? 1 : 0;
            var user_id = "{{$id}}";
            if(is_mdo == 0){
                content = 'Would you like to disable MDO File Access?';
            }else{
                content = 'Would you like to enable MDO File Access?';
            }
            $.confirm({
                title: 'Are you sure?',
                content: content,
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("user-mdo-toggle")}}',
                                data: {
                                    'user_id': user_id,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.is_mdo').val(is_mdo);
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        var lastStatus = $('#is_mdo').val();
                        if(lastStatus == 1){
                            $('#is_mdo').prop("checked", true);
                        }else{
                            $('#is_mdo').prop("checked", false);
                        }
                    },
                },
            });
        });

        function changeEsignTemplateType() {
            var templateType = $('#template_type').val();
            var userId = "{{$id}}";
            $.confirm({
                title: 'Are you sure?',
                content: 'Would you like to change Template Type to <b>' + templateType + '</b>?',
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $('#template_type').attr('disabled', true);
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: '{{url("user-template-type-update")}}',
                                data: {
                                    'user_id': userId,
                                    'template_type': templateType,
                                    '_token': "{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    $('#template_type').attr('disabled', false);
                                    toastr.success(data.error_msg);
                                },
                                error: function(xhr) {
                                    $('#template_type').attr('disabled', false);
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        $('#template_type').val("{{ $userDetails->template_type ?? 'All' }}");
                    },
                },
            });
        }

        $(document).on("change", ".restrict_user", function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
          
            if(status == 0){
                content = 'Do you want to restore full access for this user so they can see all authorized records?';
            }else{
                content = 'Do you want to restrict this user so they can view only the records they create?';
            }
            $.confirm({
                title: 'Are you sure?',
                content: content,
                columnClass: "col-md-6",
                type: 'blue',
                buttons: {
                    formSubmit: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                type: "post",
                                url: '{{url("user-restrict")}}',
                                data: {
                                    'status': status,
                                    'user_id': '{{$id}}',
                                    '_token':"{{ csrf_token()}}"
                                },
                                success: function(data) {
                                    toastr.success(data.error_msg);
                                    $('.restrict_user').val(status)
                                },
                                error:function(xhr){
                                    showErrorAndLoginRedirection(xhr);
                                }
                            });
                        }
                    },
                    cancel: function() {
                        //close
                        var lastStatus = $('#restrict_user').val();
                    
                        if(lastStatus ==1){
                            $('#restrict_user').prop("checked",true);
                        }else{
                            $('#restrict_user').prop("checked",false);
                        }
                    },
                },
            });
        });
    </script>
    <script src="{{ asset('assets/modulejs/agency/date_wise_agency_access.js') }}?time={{ env('timestamp')}}"></script>
