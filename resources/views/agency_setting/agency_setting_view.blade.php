@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{asset('/assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('/assets/modulejs/css/agency_user.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />

<!--main-container-part-->
<div class="main-panel main-page-box">
    <div class="content-wrapper px-3 pb-0">

        <div class="dashboard-header d-flex flex-column ">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12 grid-margin stretch-card mb-0">
                            <div class="card">
                                <div class="left-section-main info-tab-sec">
                                    <ul class="nav nav-tabs tabs-left sideways left-section-ul">
                                        <li class="active"><a href="#personal-info-section" data-toggle="tab"> <i class="fa fa-info-circle mr-1"></i>Users</a>
                                        </li>
                                        <li><a href="#notification-section" data-toggle="tab" onclick="notificationEmailList(1);"> <i class="fa fa-envelope-open mr-1"></i> Notification Email</a>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content left-section-tab-content">
                                        <div class="tab-pane active" id="personal-info-section">
                                            <div class="row">
                                                <div class="col-lg-12">

                                                    <div class="box info-box card basic-detail-div">
                                                        <div class="content-wrapper content-wrapper-box">

                                                            <div class="page-title-main">
                                                                <h5 class="mb-0 font-weight-bold"><i class="fa fa-info-circle mr-2"></i>Users</h5>
                                                                <div class="page-rightbtns cust-page-rightbtns">
                                                                    <div>
                                                                        <a href="{{ url('/agency/adduser') . '?id=' . urlencode($encryptedId) }}" class="btn cust-right-btn" style="background-color:rgb(32, 158, 0);color:#fff;"><i class="mdi mdi-plus"></i> Create New <span></span></a>
                                                                        <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                                                                                class="mdi mdi-filter-outline"></i>Filter <span></span></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr />
                                                            <div class="row ">
                                                                <div class="col-sm-12">
                                                                    <div id="search-filter-btn" style="display: none;">
                                                                        <div class="search-card1 cust-card-box" id="search-div">
                                                                            <div class="card-body p-0 border-0 form-patient-list-box">
                                                                                <form id="search-form">
                                                                                    <div class="row form-row-gap">
                                                                                        <div class="col-md-3">
                                                                                            <div class="form-group cust-select-box">
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-12">
                                                                                                        <label class="col-sm-12 ">Full Name</label>
                                                                                                        <input type="text" class="form-control" autocomplete="off" placeholder="Full Name"
                                                                                                            name="full_name" id="full_name">
                                                                                                        <span class="error ml-2" id="error_all"></span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <div class="form-group cust-select-box">
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-12">
                                                                                                        <label class="col-sm-12">Email</label>

                                                                                                        <input type="text" class="form-control" autocomplete="off" name="email"
                                                                                                            id="email">

                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <div class="form-group cust-select-box">
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-12">
                                                                                                        <label class="col-sm-12">Phone No</label>
                                                                                                        <input type="text" class="form-control" autocomplete="off"
                                                                                                            name="phone" id="phone">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <div class="form-group cust-select-box">
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-12">
                                                                                                        <label class="col-sm-12">Status</label>
                                                                                                        <select id="status" name="record_access" class="form-control">
                                                                                                            <option value="">Select Status</option>
                                                                                                            <option value="active">Active</option>
                                                                                                            <option value="inactive">Inactive</option>
                                                                                                            <option value="block">Block</option>
                                                                                                            <option value="unblock">Unblock</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-md-3">
                                                                                            <div class="form-group cust-select-box">
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-12">
                                                                                                        <label>Created Date</label>
                                                                                                        <input type="text" autocomplete="off" name="created_date" class="datepicker1 form-control" readonly id="created_date" placeholder="Select Created Date">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-md-3">
                                                                                            <div class="form-group cust-select-box">
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-12">
                                                                                                        <label>Created By</label>
                                                                                                        <input type="text" autocomplete="off" class="form-control" name="created_by" id="created_by" style="width:100% !important">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>

                                                                                <div class="row form-row-gap mt-3">
                                                                                    <div class="col-md-9">
                                                                                        <div class="appointment-btn-box" style="justify-content:left !important">
                                                                                            <input type="button" name="search"
                                                                                                class="btn search-btn1 searchAppoinment" id="search-data"
                                                                                                value="Search" onclick="loadUserList()">

                                                                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>

                                                                                            <a href="javascript:void(0)" class="btn btn-warning btn-rounded btn-sm btn-fw pull-right  mr-1" id="test_user" onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12 ">
                                                                    <div class="shimmer_id">
                                                                        <div class="table-responsive">
                                                                            <table id="" class="table table-bordered ">
                                                                                <thead>
                                                                                    <th width="5%">No</th>
                                                                                    <th width="15%" nowrap>Full Name</th>
                                                                                    <th width="10%" nowrap>Email</th>
                                                                                    <th width="10%" nowrap>Phone No</th>
                                                                                    <th width="10%" nowrap>Ext No</th>
                                                                                    <th width="10%" nowrap>Status</th>
                                                                                    <th width="10%" nowrap>Is Admin</th>
                                                                                    <th width="15%" nowrap>Last User Login <br /> Last Ip Address</th>
                                                                                    <th width="15%" nowrap>Created Date <br /> Created By</th>
                                                                                </thead>
                                                                                <tbody class="loading-shimmer">
                                                                                    <tr>
                                                                                        <td colspan="9"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                    <span id="user_response_requested_id"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="notification-section">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="content-wrapper content-wrapper-box">
                                                                    <div class="page-title-main">
                                                                        <h5 class="mb-0 font-weight-bold"><i class="fa fa-envelope-open mr-2"></i>Notification Email</h5>
                                                                        <div class="page-rightbtns cust-page-rightbtns">
                                                                            <div>
                                                                                <a href="javascript::void(0);" class="btn cust-right-btn add-notification-email"
                                                                                    style="background-color:rgb(32, 158, 0);color:#fff;"><i class="mdi mdi-plus"></i> Add Notification Email
                                                                                    <span></span></a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <hr />
                                                                    <div class="row">
                                                                        <div class="col-12 ">
                                                                            <div class="shimmer_id">
                                                                                <div class="table-responsive">
                                                                                    <table id="" class="table table-bordered ">
                                                                                        <thead>
                                                                                            <th width="5%">No</th>
                                                                                            <th width="10%" nowrap>Email</th>
                                                                                            <th width="15%" nowrap>Patient</th>
                                                                                            <th width="15%" nowrap>Caregiver</th>
                                                                                            <th width="20%" nowrap>Service</th>
                                                                                            <th width="20%" nowrap>Discipline</th>
                                                                                            <th width="10%" nowrap> Action </th>
                                                                                        </thead>
                                                                                        <tbody class="loading-shimmer">
                                                                                            <tr>
                                                                                                <td colspan="8"></td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <span id="notification_email_id">
                                                                            </span>
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
@include('agency-user/_partial/add_notification')
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
    var EMAIL_SAVE = "{{ url('user-notification-email-save')}}";
    var _AGENCY_NOTIFICATION_EMAIL_LIST = "{{ url('agency-user-notification-list') }}";
    var _AGENCYID = "{{ auth()->user()->agency_fk }}";
    var AJAX_ALL_SERVICE = "{{ url('ajax-all-service')}}";
    var SAVE_AGENCY_NOTIFICATION = "{{ url('agency-email-notification-email-save')}}";
    var EDIT_EMAIL_NOTIFICATION = "{{ url('edit-email-notification') }}";
    var _DISCIPLINE_LIST = "{{ url('ajax-all-discipline') }}";
    var DELETE_AGENCY_USER_NOTIFICATION = "{{ url('agency-email-notification-email-delete') }}";
    var AGENCY_WISE_USER = "{{ url('agency-wise-user') }}";
    var AGENCY_EXPORT = "{{'/agency-user-export'}}";
    var _SEARCH_CREATED_BY_USER = "{{ url('search-nybest-all-user') }}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
</script>
<script src="<?= URL::to('assets/jquery-confirmation/js/jquery-confirm.min.js') ?>"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('assets/modulejs/agency_user/agency_user.js') }}?time={{ env('timestamp')}}"></script>