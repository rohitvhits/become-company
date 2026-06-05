@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/modulejs/css/agency_user.css?time={{ env('timestamp')}}">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet"
    href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<!--main-container-part-->
<div class="main-panel">
    <div class="content-wrapper px-3 pb-0">

        <div class="dashboard-header d-flex flex-column ">
            <div class="basic-detail-sec border-bottom  mb-3 card">
                <div class="d-flex align-items-center justify-content-between flex-wrap   mb-2">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 mr-4 font-weight-bold">User #
                            <?= $userDetails->id . ' - ' . ucwords($userDetails->first_name) . ' ' . ucwords($userDetails->last_name) . ' ' ?>
                        </h4>

                        <div id="status{{ $userDetails->id}}">
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
                        </div>
                    </div>
                    <div class="appoin-btn-wrapper">
                        <div class="btn-group pull-right status-dropdoown mr-2">
                            <button type="button" class="btn btn-warning" title="Status">Status</button>
                            <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6" id="status_id">
                                @if($userDetails->active =='inactive')
                                <a class="dropdown-item" href="javascript::void(0)" id="active" onclick="getStatus('{{ $userDetails->id }}','active')">Active</a>
                                <a class="dropdown-item" href="javascript::void(0)" id="unblock" onclick="getStatus('{{ $userDetails->id }}','unblock')">Unblock</a>
                                @endif
                                @if($userDetails->active =='active')
                                <a class="dropdown-item" href="javascript::void(0)" id="inactive" onclick="getStatus('{{ $userDetails->id }}','inactive')">Inactive</a>
                                <a class="dropdown-item" href="javascript::void(0)" id="block" onclick="getStatus('{{ $userDetails->id }}','block')">Block</a>
                                @endif
                                @if($userDetails->active =='unblock')
                                <a class="dropdown-item" href="javascript::void(0)" id="block" onclick="getStatus('{{ $userDetails->id }}','block')">Block</a>
                                <a class="dropdown-item" href="javascript::void(0)" id="inactive" onclick="getStatus('{{ $userDetails->id }}','inactive')">Inactive</a>
                                @endif
                                @if($userDetails->active =='block')
                                <a class="dropdown-item" href="javascript::void(0)" id="unblock" onclick="getStatus('{{ $userDetails->id }}','unblock')">Unblock</a>
                                <a class="dropdown-item" href="javascript::void(0)" id="active" onclick="getStatus('{{ $userDetails->id }}','active')">Active</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12 grid-margin stretch-card mb-0">
                            <div class="card">
                                <div class="left-section-main info-tab-sec">
                                    <ul class="nav nav-tabs tabs-left sideways left-section-ul">
                                        <li class="active"><a href="#personal-info-section" data-toggle="tab"> <i class="fa fa-info-circle"></i> &nbsp;Personal Information</a>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content left-section-tab-content">
                                        <div class="tab-pane active" id="personal-info-section">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box basic-detail-div">
                                                                <div class="row">
                                                                    {{-- <div class="col-lg-2">
                                                                        <div class="text-center mt-4">
                                                                            @if($userDetails->profile_img !="")
                                                                            <img id="user_img_id" src="{{ url('user-profile-image')}}" alt="profile" class="img-lg rounded-circle mb-3">
                                                                            @else
                                                                            <img src="{{ asset('assets/images/faces/face5.jpg')}}" alt="profile" class="img-lg rounded-circle mb-3">
                                                                            @endif
                                                                        </div>
                                                                    </div> --}}
                                                                    <div class="col-lg-12">
                                                                        <div class="title">
                                                                            <h5><i class="mdi mdi-information mr-1"></i>Basic Details <a class="show pull-right" onclick="setBasicDetails()"><i
                                                                                        class="fa fa-edit"></i></a> <a class="hide pull-right" onclick="getBasicDetails('{{$userDetails->id}}')"><i
                                                                                        class="fa fa-close"></i></a> <a class="hide pull-right mr-2" onclick="updateUser()"><i
                                                                                        class="fa fa-save"></i></a>
                                                                                <?php if ($userDetails->id != $user->id) {
                                                                                    if ($totalEmcCountRecord == 0 && $totalAgencyCountRecord == 0) { ?>
                                                                                        <a href="javascript::void(0)" data-toggle="tooltip" title="Delete" onclick="deleteUserData('{{$userDetails->id}}')" class="show pull-right ml-1"><i class="fa fa-trash mr-1"></i></a></a>
                                                                            </h5>
                                                                    <?php }
                                                                                } ?>
                                                                        </div>
                                                                        <div id="loader" class="row basic-detail-row" style="display:none">
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt>First Name</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7 shimmer-loader">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt>Last Name</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7 shimmer-loader">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt>Phone</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7 shimmer-loader">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt>Email</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7 shimmer-loader">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt> Record Access</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7 shimmer-loader">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt> Ext</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7 shimmer-loader">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <form method="post" id="task_patient_id">
                                                                            <div class="row basic-detail-row" id="detail-div-class">
                                                                                @csrf
                                                                                <input type="hidden" name="id" value="{{ $userDetails->id}}">
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-5">
                                                                                            <dt>First Name<span class="error hide ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-7">
                                                                                            <dd class="show" id="view_first_id"> <?= $userDetails->first_name != '' ? $userDetails->first_name : '' ?> </dd>
                                                                                            <dd class="hide">
                                                                                                <input type="text" name="first_name" class="form-control" id="first_name_id" autocomplete="off" value="{{ $userDetails->first_name}}">
                                                                                                <span id="first_name_error" class="error"></span>
                                                                                            </dd>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-5">
                                                                                            <dt>Last Name<span class="error hide ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-7">
                                                                                            <dd class="show" id="view_last_id"> <?= $userDetails->last_name != '' ? $userDetails->last_name : '' ?> </dd>
                                                                                            <dd class="hide">
                                                                                                <input type="text" name="last_name" class="form-control" id="last_name_id" autocomplete="off" value="{{ $userDetails->last_name}}">
                                                                                                <span id="last_name_error" class="error"></span>
                                                                                            </dd>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-5">
                                                                                            <dt>Phone</dt>
                                                                                        </div>
                                                                                        <div class="col-md-7">
                                                                                            <dd class="show" id="view_phone_id"> <?= $userDetails->phone != '' ? $userDetails->phone : '-' ?> </dd>
                                                                                            <dd class="hide"> <input type="text" name="phone" class="form-control" id="phone_id" autocomplete="off" value="{{ $userDetails->phone}}">
                                                                                                <span id="phone_error" class="error"></span>
                                                                                            </dd>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-5">
                                                                                            <dt>Email<span class="error hide ml-1">*</span></dt>
                                                                                        </div>
                                                                                        @if($userDetails->email !="")
                                                                                        @php
                                                                                        $explode = explode('@',$userDetails->email);
                                                                                        @endphp
                                                                                        @endif
                                                                                        <div class="col-md-7">
                                                                                            <dd class="show" id="view_email_id"> <?= $userDetails->email != '' ? $userDetails->email : '-' ?> </dd>
                                                                                            <dd class="hide"> <input type="text" style="width:70%" name="email" class="form-control" id="email_id" autocomplete="off" value="{{ $explode[0]}}"> <span id="email_error" class="error"></span><label for="recipient-name" style="margin-left:10px;" class="col-form-label">{{ '@'.$explode[1]}}</label>
                                                                                                <input type="hidden" name="domain" value="{{ '@'.$explode[1]}}">
                                                                                            </dd>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-5">
                                                                                            <dt> Record Access</dt>
                                                                                        </div>
                                                                                        <div class="col-md-7">
                                                                                            <dd class="show" id="view_record_access">
                                                                                                {{$userDetails->record_access}}
                                                                                            </dd>
                                                                                            <dd class="hide">
                                                                                                <select class="form-control" id="record_access" name="record_access">
                                                                                                    <option value="All" @if($userDetails->record_access == 'All') selected @endif >All</option>
                                                                                                    <option value="Patient" @if($userDetails->record_access == 'Patient') selected @endif >Patient</option>
                                                                                                    <option value="Caregiver" @if($userDetails->record_access == 'Caregiver') selected @endif >Caregiver</option>
                                                                                                </select>
                                                                                            </dd>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-5">
                                                                                            <dt> Ext</dt>
                                                                                        </div>
                                                                                        <div class="col-md-7">
                                                                                            <dd class="show" id="view_ext_id"> <?= $userDetails->ext != '' ? $userDetails->ext : '-' ?>
                                                                                            </dd>
                                                                                            <dd class="hide">
                                                                                                <input type="text" name="ext_no" class="form-control" id="ext_no_id" autocomplete="off" value="{{ $userDetails->ext}}">
                                                                                                <span id="ext_no_error" class="error"></span>
                                                                                            </dd>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-5">
                                                                                            <dt> Is Admin</dt>
                                                                                        </div>
                                                                                        <div class="col-md-7">
                                                                                            <dd class="show" id="view_is_admin"> <?= $userDetails->role_access == 1 ? 'Yes' : 'No' ?>
                                                                                            </dd>
                                                                                            <dd class="hide">
                                                                                                <input type="checkbox" name="role_access" class="notification_checkbox patient_checkbox" id="role_access" autocomplete="off" value="{{ $userDetails->role_access}}" @if($userDetails->role_access == 1) checked @endif>
                                                                                                <span id="role_access_no_error" class="error"></span>
                                                                                            </dd>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <div class="title mt-3">
                                                                            <h5><i class="fa fa-list-alt mr-1"></i> Details </h5>
                                                                        </div>
                                                                        <div class="row basic-detail-row">
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt> Last Login Date</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7">
                                                                                        <dd id="lastLDate"> <?php if ($userDetails->last_login_at != '') {
                                                                                                                echo date('m/d/Y H:i:s', strtotime($userDetails->last_login_at));
                                                                                                            } else {
                                                                                                                echo "-";
                                                                                                            } ?></dd>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <div class="col-md-5">
                                                                                        <dt> IP Address</dt>
                                                                                    </div>
                                                                                    <div class="col-md-7">
                                                                                        <dd id="ipAdd"> @if(isset( $userDetails->last_login_ip))
                                                                                            {{ $userDetails->last_login_ip}}
                                                                                            @else
                                                                                            {{"-"}}
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
            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 5%;'></div>
</div>
@include('include/footer')
<script>
    var AGENCY_USER_DETAIL = "{{ url('agency-user-details')}}";
    var AGENCY_USER_DELETE = "{{url('/agency-user-delete?i=')}}";
    var AGENCY_USER_STATUS_CHANGE = "{{ url('user-change-status') }}";
    var CSRF_TOKEN = "{{ csrf_token() }}";
    var ID = "{{request('id')}}";
    var CHNAGESTATUS = "{{ url('chnagestatus') }}";
    var AGENCY_USER_UPDATE = "{{ url('agency-user-update') }}";
    var CHANGE_RECORD_TYPE = "{{ url('change-record-type')}}";
    var RECORD_ID = "{{  $userDetails->id  }}";
</script>
<script src="<?= URL::to('assets/jquery-confirmation/js/jquery-confirm.min.js') ?>"></script>
<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/modulejs/agency_user/agency_user.js') }}?time={{ env('timestamp')}}"></script>