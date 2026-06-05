@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<style>
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    .select2-design+.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .wmd-view-topscroll,
    .wmd-view {
        overflow-x: scroll;
        overflow-y: hidden;
        border: none 0px red;
    }

    .wmd-view {
        overflow: auto;
        height: calc(100vh - 250px);
    }

    .wmd-view-topscroll {
        height: 20px;
    }

    .scroll-div1 {

        overflow-x: scroll;
        overflow-y: hidden;
        height: 20px;
        width: calc(1650px - -17px) !important;
    }

    .scroll-div2 {
        height: 20px;
    }

    .scroll-div1,
    .scroll-div2 {
        /* width: 1650px; */
    }

    td {
        table-layout: fixed;
        width: 20px;
        overflow: hidden;
        word-wrap: break-word;
    }

    .table-width1 tr th:last-child {
        width: 100px;
    }

    .table-width1 tr th:nth-child(10) {
        width: 100px;
    }

    .table-width1 {
        background-color: #fff;
    }

    .table-width1 tr th:nth-child(11) {
        width: 152px;
    }

    .table-width1 tr th:nth-child(12) {
        white-space: nowrap;
    }

    .search-inner {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        padding-right: 20px;
        padding-left: 20px;
    }

    .search-main1 {
        border-top: 1px solid #eeeeee;
        margin-left: -20px;
        margin-right: -20px;
    }

    .search-btn1,
    .search-btn1:hover,
    .search-btn1:active,
    .search-btn1:focus {
        background: #007bff !important;
        border: #007bff !important;
        border-radius: 20px;
        height: 36px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-card1 {
        margin-bottom: 20px;
    }

    .search-card1 .form-group {
        margin-bottom: 0.5rem;
    }

    .search-card1 label {
        margin-bottom: 0;
    }

    .search-card1 .card-body {
        padding-bottom: 10px;
    }

    .search-card1 input[type=text] {
        border-radius: 4px;
        border-color: #aaa;
    }

    .srch-icon {
        padding: 0 !important;
        width: 40px;
        height: 40px;
    }

    .no_warp {
        white-space: nowrap;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
    }

    .tableData .add_new_record .left_record {
        left: -9px;
        right: unset !important;
    }

    .tableData .add_new_record {
        position: absolute;
        top: 0;

        background: #00BBE0;
        padding: 1px 5px;
        font-size: 10px;
        color: #fff;
        border-radius: 2px 2px 2px 2px;
        font-size: 10px !important;
    }

    .tableData .add_new_record::after {
        position: absolute;
        content: "";
        bottom: -6px;
        right: 0px;
        background: #b7b7b8;
        z-index: -1;
        width: 10px;
        height: 10px;

    }

    .tableData .add_new_record::after {
        left: 0px;
        border-radius: 0px 0px 0px 50px;
    }

    .service_id_by_patient_type .select2-design+.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .pale-yellow-color {
        background-color: #ffecb3;
    }
</style>
<div class="main-panel">
    @php
    $auth = auth()->user();
    @endphp
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Chart (<span id="totalCount"></span>)</h5>
            <div class="page-rightbtns">
                <div>
                @can('bulk-chart-delete')
                    <a href="javascript:void(0)" class="btn btn-danger btn-rounded btn-sm btn-fw" onclick="bulkAppointmentDelete()"><i class="mdi mdi-delete"></i>Bulk Chart Delete</a>
                    @endcan
                    <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Status</label>
                                        <div class="col-sm-12">
                                            <select name="status[]" id="status_id" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                <option value=""></option>
                                                <option value="Pending" >Pending</option>
                                                <option value="cancelled" >Cancelled</option>

                                                <option value="booked" >Booked</option>
                                                <option value="completed" >Completed</option>

                                                <option value="noshow" >No Show</option>

                                                <option value="arrived" >Arrived</option>
                                                <option value="processing" >Processing</option>
                                                <option value="Not interested" >Not Interested
                                                </option>
                                                <option value="hospitalized/rehab" >
                                                    Hospitalized/Rehab</option>
                                                <option value="unableToContact" >Unable To Contact
                                                </option>
                                                <option value="refused" >Refused</option>
                                                <option value="checkin">Mark as CheckIn</option>

                                                <option value="Pending Termination" >Pending Termination</option>
                                                <option value="Onhold" >On Hold</option>
                                                <option value="On Leave" >On Leave</option>
                                                <option value="Terminated" >Terminated</option>
                                                <option value="inactive" >Inactive</option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}">{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @if (in_array($user->user_type_fk, [3, 184]))
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Agency Name</label>
                                        <div class="col-sm-12">
                                            <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                <?php foreach ($agencyList as $rwAgency) { ?>
                                                    <option value="<?php echo $rwAgency->id; ?>">
                                                        <?php echo $rwAgency->agency_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Patient Code</label>
                                        <div class="col-sm-12">
                                            <input autocomplete="off" type="text" class="form-control" name="patient_code" id="patient_code" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Name</label>
                                        <div class="col-sm-12">
                                            <input autocomplete="off" type="text" class="form-control" name="full_name" id="agency_name" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Mobile</label>
                                        <div class="col-sm-12">
                                            <input autocomplete="off" type="text" class="form-control" name="mobile" id="mobile" value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Assign To</label>
                                        <div class="col-sm-12">
                                            <select name="assign_user_id[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple" id="assign_user_id">
                                                @if (!empty($assign_user_list[0]))
                                                @foreach ($assign_user_list as $assigns)
                                                <option value="{{ $assigns->id }}" >
                                                    {{ $assigns->name }}
                                                </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Due Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="due_date" value="" class="due_datenn form-control" id="due_date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Location</label>
                                        <div class="col-sm-12">
                                            <select name="locationId[]" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple" id="locationId">
                                                <?php foreach ($location_list as $vsl) { ?>
                                                    <option value="<?php echo $vsl->id; ?>" >
                                                        <?php echo $vsl->address1; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Created Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="created_date" value="" class="datepickernn form-control" id="created_date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Discipline</label>
                                        <div class="col-sm-12 ">
                                            <select class="form-control" name="diciplin" id="diciplin_id">

                                                <option value="">Select Discipline</option>
                                                @if (count($masterData) > 0)
                                                @foreach ($masterData as $master)
                                                @if ($master->master_type_fk == 26)
                                                <option value="{{ $master->name }}" >{{ $master->name }}
                                                </option>
                                                @endif
                                                @endforeach
                                                @endif

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Type</label>
                                        <div class="col-sm-12 ">
                                            <select class="form-control" name="type" id="type" class="form-control">
                                                <option value="">Select Type</option>
                                                <option value="Caregiver" >Caregiver</option>
                                                <option value="Patient" >Patient</option>

                                            </select>

                                        </div>
                                    </div>
                                </div>

                                @if($auth->agency_fk ==106 || $auth->id == 482)
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Training Due Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="traning_date"  class="traning_date form-control" id="traning_date">
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Created By</label>
                                        <div class="col-sm-12">
                                            @if(!empty($agency_user_list[0]))
                                            <select name="created_by" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" id="created_by">
                                                <option value="">Select Created By</option>
                                                @foreach($agency_user_list as $val)
                                                <option value="{{ $val->id}}" >{{ $val->first_name}} {{ $val->last_name}}</option>

                                                @endforeach

                                            </select>
                                            @else
                                            <input type="text" name="created_by_ny" id="created_by_ny">
                                            <input type="hidden" name="created_by_ny_id" id="created_by_ny_id">
                                            <input type="hidden" name="created_by_ny_name" id="created_by_ny_name" >

                                            @endif
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Language</label>
                                        <div class="col-sm-12">
                                            <select name="language_id" class="form-control" id="language_id">
                                                <option value="">Select Language</option>
                                                @foreach ($language_list as $vsl)
                                                <option value="{{$vsl->id}}" >
                                                    {{$vsl->name}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Transition Aid</label>
                                        <div class="col-sm-12">
                                            <select name="transition_aid" class="form-control" id="transition_aid">
                                                <option value="">Select Transition Aid</option>
                                                <option value="1" >Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Date Of Birth</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="dob" class="dob form-control" id="dob">
                                        </div>
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-check form-check-flat form-check-primary" style="margin-top: 31px !important;">
                                            <label class="form-check-label">
                                                <input type="checkbox" name="is_archive" value="true" class="form-check-input" id="archived">
                                                Show Archived
                                                <i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="search-main1">
                                <div class="search-inner">
                                    <div>
                                        <input type="button" name="search" class="btn btn-primary search-btn1 searchAppoinment" id="search-data" value="Search">
                                        @can('export-csv')
                                        <a href="javascript:void(0)" class="btn btn-success btn-rounded btn-sm btn-fw  ml-1 btnExport" id="test_agency" onclick="exportCsv()"><i class="mdi mdi-file-export"></i>Export</a>
                                        @endcan
                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm clear_id"><i class="mdi mdi-reload"></i> Reset</a>
                                        <img src="{{ asset('ajax-loader.gif')}}" class="" id="aid_demo_details_id" alt="loader" style="display: none;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 ">
                <div class="table-responsive tableData" id="patient_list_id">

                </div>
            </div>

        </div>

    </div>

    @include('include/footer')
    <script src="{{ asset('js/jquery.min.js')}}"></script>

     <script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
     <script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
     <script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
     <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
     <script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
     <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
     <script src="{{ asset('assets/js/select2.js')}}"></script>
     <script src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
     <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>

     <script>
        var urlToken =  "{{ url('search-nybest-user') }}"; 
        var patienList =  "{{ url('patient-detail-list') }}"; 
        var _PATIENT_ARCHIVE =  "{{ url('patient/patient-archive') }}"; 
        var empId = '';
        var empName = '';    
        var _CSRF_TOKEN = "{{ csrf_token()}}";    
        var _PATIENT_EXPORT_CSV =  "{{ url('export-csv') }}";
        var _DATE_TIME ="{{ date('m/d/Y')}}";
        var _BULK_APPOINTMENT_DELETE ="{{ url('bulk-appointments-delete')}}";
     </script>
     <script src="{{ asset('assets/modulejs/patient/appointment_patient_demographic.js')}}?time={{ time()}}"></script>