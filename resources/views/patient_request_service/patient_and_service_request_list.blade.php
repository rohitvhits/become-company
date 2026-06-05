@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">

<link href="{{ asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/token-input.css')}}" rel="stylesheet" type="text/css" />

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

    .no_warp {
        white-space: nowrap;
    }

    .error {
        color: red;
    }

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

    td {
        table-layout: fixed;
        width: 20px;
        overflow: hidden;
        word-wrap: break-word;
    }

    .table-width1 tr th:last-child {
        width: 88px;
    }

    .table-width1 tr th:first-child {
        width: 3%;
    }

    .table-width1 tr th:nth-child(3) {
        width: 10%;
    }

    .table-width1 tr th:nth-child(4) {
        width: 12%;
    }

    .table-width1 tr th:nth-child(5) {
        width: 12%;
    }

    .table-width1 tr th:nth-child(6) {
        width: 12%;
    }

    .table-width1 {
        background-color: #fff;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    /* Agency Toggle Button */
    .agency-filter-toggle-wrapper, .service-filter-toggle-wrapper {
        display: inline-flex;
        align-items: center;
        margin-left: 8px;
        gap: 6px;
    }

    .agency-toggle-btn, .service-toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 4px;
        border: 2px solid #ddd;
        background: #fff;
        cursor: pointer;
        transition: all 0.25s ease;
        padding: 0;
        vertical-align: middle;
        position: relative;
        flex-shrink: 0;
    }

    .agency-toggle-btn i ,.service-toggle-btn i {
        font-size: 18px;
        line-height: 1;
        pointer-events: none;
        display: block;
    }

    /* Blue/Grey - Professional & Clear (Current Active) */
    .agency-toggle-btn[data-mode="include"], .service-toggle-btn[data-mode="include"] {
        background-color: #cfe2ff !important;
        border-color: #0d6efd !important;
        color: #084298 !important;
    }

    .agency-toggle-btn[data-mode="include"]:hover, .service-toggle-btn[data-mode="include"]:hover {
        background-color: #b6d4fe !important;
        transform: scale(1.05);
    }

    .agency-toggle-btn[data-mode="exclude"],.service-toggle-btn[data-mode="exclude"] {
        background-color: #e9ecef !important;
        border-color: #6c757d !important;
        color: #495057 !important;
    }

    .agency-toggle-btn[data-mode="exclude"]:hover, .service-toggle-btn[data-mode="exclude"]:hover {
        background-color: #dee2e6 !important;
        transform: scale(1.05);
    }
    .agency-toggle-btn:focus, .service-toggle-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    .agency-toggle-btn:active, .service-toggle-btn:active {
        transform: scale(0.95);
    }

    .agency-toggle-label,  .service-toggle-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: color 0.25s ease;
        user-select: none;
        white-space: nowrap;
    }

    /* Match label colors with button colors */
    .agency-toggle-label.mode-include, .service-toggle-label.mode-include {
        color: #0d6efd;
    }

    .agency-toggle-label.mode-exclude, .service-toggle-label.mode-exclude {
        color: #6c757d;
    }
</style>
@php
    $auth = auth()->user();
@endphp
<div class="main-panel">

    <div class="content-wrapper">
        <div class="col-12 grid-margin-top">
            <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Service Requested(<span id="service_request_count">0</span>)</h5>
                <div class="page-rightbtns">
                    <div>
                        @can('add-new-service-request')
                            <!-- <a href="{{ url('patient-add-new') }}" class="btn btn-primary btn-rounded btn-sm btn-fw"
                                data-whatever="@mdo"><i class="mdi mdi-plus"></i>Create New</a> -->
                        @endcan
                        {{-- 

                        <a href="{{ URL::to('/') }}/patient/add"
                            class="btn btn-primary btn-rounded btn-fw btn-sm  ml-1"><i class="mdi mdi-plus"> </i> Create New </a>

                        <a href="{{ URL::to('/') }}/patient?is_past_show=true"
                            class="btn btn-info btn-rounded btn-fw btn-sm">Past Appointment List</a>
                        <a href="{{ URL::to('/') }}/patient" class="btn btn-light btn-rounded btn-fw btn-sm"><i
                                class="mdi mdi-reload"></i> Reset</a>
                        <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i
                                class="fa fa-search"></i></button> --}}
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
                                                <select name="status[]" id="status_id"
                                                    class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple">
                                                    <option value=""></option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Pending',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="Pending">Pending</option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Cancelled',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="Cancelled">Cancelled</option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Booked',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="booked">Booked</option>
                                                    
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Completed',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="Completed">Completed</option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('noshow',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="noshow">No Show</option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('arrived',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="arrived">Arrived</option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('processing',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="processing">Processing
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Not interested',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="Not interested">Not Interested
                                                 
                                                    </option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('hospitalized/rehab',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="hospitalized/rehab">
                                                    Hospitalized/Rehab
                                                    </option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('UnableToContact',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="UnableToContact">Unable To Contact</option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Refused',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="Refused">Refused</option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('checkin',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="checkin">
                                                    Mark As CheckIn</option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('PendingTermination',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="PendingTermination">Pending Termination</option>
                                                    <!-- <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('InService',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="InService">In Service</option> -->

                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('OnHold',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="OnHold">On Hold</option>

                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('OnLeave',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="OnLeave">On Leave</option>

                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Terminated',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="Terminated">Terminated
                                                    </option>
                                                    <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Inactive',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="Inactive">Inactive
                                                    </option>
                                                    @if(isset($statuses) && !empty($statuses))
                                                    @foreach ($statuses as $status)
                                                        <option 
                                                            @if (isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array($status, $search_param['status'])) 
                                                                @php echo "selected='selected'" @endphp 
                                                            @endif 
                                                            value="{{ $status }}">
                                                            {{ $status }}
                                                        </option>
                                                    @endforeach
                                                    @endif
                                                   


                                                    <!-- <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Undo',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="Undo">Undo</option> -->

                                                    

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @if (in_array($user->user_type_fk, [3, 184]))
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12">
                                                    Agency Name
                                                    <span class="agency-filter-toggle-wrapper">
                                                        <button type="button" class="agency-toggle-btn" id="agencyToggleBtn"
                                                                data-mode="include" title="Include - Click to switch to Exclude">
                                                            <i class="mdi mdi-plus"></i>
                                                        </button>
                                                        <span class="agency-toggle-label mode-include" id="agencyToggleLabel">Include Agency</span>
                                                    </span>
                                                </label>
                                                <div class="col-sm-12">
                                                    <input type="hidden" name="agency_filter_type" id="agency_filter_type"
                                                           value="@if(isset($search_param['agency_filter_type']) && $search_param['agency_filter_type'] == 'exclude')exclude @else include @endif">
                                                    <select name="agency_fk[]" id="agency_fk"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple">
                                                        <?php foreach ($agencyList as $rwAgency) { ?>
                                                            <option @if(isset($search_param['agency_fk'][0]) && !empty($search_param['agency_fk'][0]) && in_array($rwAgency->id,$search_param['agency_fk'])) @php echo "selected='selected'" @endphp @endif value="<?php echo $rwAgency->id; ?>">
                                                            <?php echo $rwAgency->agency_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Code</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="patient_code" id="patient_code" value="{{$search_param['patient_code']??''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Name</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="first_name" id="first_name" value="{{$search_param['first_name']??''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Mobile</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="mobile" id="mobile" value="{{$search_param['mobile']??''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Services
                                                <span class="service-filter-toggle-wrapper">
                                                    <button type="button" class="service-toggle-btn" id="serviceToggleBtn"
                                                            data-mode="include" title="Include - Click to switch to Exclude">
                                                        <i class="mdi mdi-plus"></i>
                                                    </button>
                                                    <span class="service-toggle-label mode-include" id="serviceToggleLabel">Include Services</span>
                                                </span> 
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="hidden" name="service_filter_type" id="service_filter_type"
                                                           value="@if(isset($search_param['service_filter_type']) && $search_param['service_filter_type'] == 'exclude')exclude @else include @endif">
                                                <select class="form-control js-example-basic-multiple w-100 select2-design cal-padding-0"
                                                    multiple="multiple" name="service_id[]" id="service_id">
                                                    <?php
                                                       foreach ($serviceList as $service) { ?>
                                                    <option @if(isset($search_param['service_id'][0]) && !empty($search_param['service_id'][0]) && in_array($service->id,$search_param['service_id'])) @php echo "selected='selected'" @endphp @endif value="<?php echo $service->id; ?>">
                                                    <?php echo $service->name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Assign To</label>
                                            <div class="col-sm-12">
                                                <select name="assign_user_id[]"
                                                    class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple" id="assign_user_id">
                                                    @if (!empty($assign_user_list[0]))
                                                        @foreach ($assign_user_list as $assigns)
                                                        <option @if(isset($search_param['assign_user_id'][0]) && !empty($search_param['assign_user_id'][0]) && in_array($assigns->id,$search_param['assign_user_id'])) @php echo "selected='selected'" @endphp @endif value="{{ $assigns->id }}">
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
                                                <input type="text" name="due_date" class="due_datenn form-control"
                                                    id="due_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Appointment Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" autocomplete="off" name="appointment_date"
                                                    class="datepicker1 form-control" id="appointment_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Location</label>
                                            <div class="col-sm-12">
                                                <select name="locationId[]"
                                                    class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple" id="locationId">
                                                    <?php foreach ($location_list as $vsl) { ?>
                                                        <option @if(isset($search_param['locationId'][0]) && !empty($search_param['locationId'][0]) && in_array($vsl->id,$search_param['locationId'])) @php echo "selected='selected'" @endphp @endif value="<?php echo $vsl->id; ?>">
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
                                                <input type="text" name="created_date"
                                                    class="datepickernn form-control" id="created_date" value="@if(isset($search_param['created_date'])){{ $search_param['created_date']}} @endif">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">SMS Status</label>
                                            <div class="col-sm-12 ">
                                                <select name="sms_status[]" id="sms_status"
                                                    class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple">
                                                    <option value="0">Pending</option>
                                                    <option value="1">Sent</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Discipline</label>
                                            <div class="col-sm-12 ">
                                                <select class="form-control" name="diciplin" id="diciplin_id">

                                                    <option value="">Select Discipline</option>
                                                    <option value="HHA">HHA</option>
                                                    <option value="CDPAP">CDPAP</option>
                                                    <option value="RN">RN</option>
                                                    <option value="LPN">LPN</option>
                                                    <option value="Pre-HHA">Pre-HHA</option>
                                                    <option value="Pre-CDPAP">Pre-CDPAP</option>
                                                    <option value="OTHER">Other</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Type</label>
                                            <div class="col-sm-12 ">
                                                <select class="form-control" name="type" id="type"
                                                    class="form-control">
                                                    <option value="">Select Type</option>
                                                    <option value="Caregiver" @if(isset($search_param['type']) && !empty($search_param['type']) && $search_param['type'] == 'Caregiver') @php echo "selected='selected'" @endphp @endif>Caregiver</option>
                                                    <option value="Patient" @if(isset($search_param['type']) && !empty($search_param['type']) && $search_param['type'] == 'Patient') @php echo "selected='selected'" @endphp @endif>Patient</option>

                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">In Service Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="inservice_date"
                                                    class="inservice_date form-control" id="inservice_date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Completed Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="completed_date"
                                                    class="completed_date form-control" id="completed_date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Follow Up Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="follow_up_date"
                                                    class="follow_up_date form-control" id="follow_up_date">
                                            </div>
                                        </div>
                                    </div>
                                    @if ($auth->agency_fk == 106 || $auth->id == 482)
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">Training Due Date</label>
                                                <div class="col-sm-12">
                                                    <input type="text" name="traning_date"
                                                        class="traning_date form-control" id="traning_date">
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Created By</label>
                                            <div class="col-sm-12">
                                                @if (!empty($agency_user_list[0]))
                                                    <select name="created_by"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        id="created_by">
                                                        <option value="">Select Created By</option>
                                                        @foreach ($agency_user_list as $val)
                                                            <option value="{{ $val->id }}">
                                                                {{ $val->first_name }} {{ $val->last_name }}</option>
                                                        @endforeach

                                                    </select>
                                                @else
                                                    <input type="text" name="created_by_ny" id="created_by_ny">
                                                    <input type="hidden" name="created_by_ny_id"
                                                        id="created_by_ny_id">
                                                    <input type="hidden" name="created_by_ny_name"
                                                        id="created_by_ny_name">

                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if (in_array($user->user_type_fk, [3, 184]))
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">Training Status</label>
                                                <div class="col-sm-12">
                                                    <select name="training_status[]"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        id="training_status" multiple>
                                                        <option value="">Select Training Status</option>
                                                        <option value="Completed">Completed
                                                        </option>
                                                        <option value="Processing">Processing
                                                        </option>
                                                        <option value="Refused">Refused
                                                        </option>
                                                        <option value="Unable to reach">Unable to
                                                            reach</option>
                                                        <option value="Need to assistance">Need to
                                                            assistance</option>


                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Language</label>
                                            <div class="col-sm-12">
                                                <select name="language_id"  class="form-control" id="language_id">
                                                    <option value="">Select Language</option>
                                                    @foreach ($language_list as $vsl)
                                                        <option value="{{$vsl->id}}" @if (isset($search_param['language_id']) && !empty($search_param['language_id']) && $vsl->id == $search_param['language_id']) @php echo 'selected'; @endphp @endif>
                                                            {{$vsl->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Transition Aid</label>
                                            <div class="col-sm-12">
                                                <select name="transition_aid"  class="form-control" id="transition_aid">
                                                    <option value="">Select Transition Aid</option>
                                                    <option value="1" @if( isset($search_param['transition_aid']) && !empty($search_param['transition_aid']) && $search_param['transition_aid'] == "1" ) @php echo 'selected'; @endphp @endif>Yes</option>
                                                    <option value="0" @if( isset($search_param['transition_aid']) && !empty($search_param['transition_aid']) && $search_param['transition_aid'] == "0" ) @php echo 'selected'; @endphp @endif>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @if (in_array($user->user_type_fk, [3, 184]))
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">Last Status Updated Date</label>
                                                <div class="col-sm-12">
                                                    <input type="text" name="last_status_update"  class="form-control" id="last_status_update">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">Last Status Updated By</label>
                                                <div class="col-sm-12">
                                                    <input type="text" name="last_status_updated_by" id="last_status_updated_by">
                                                    
                                                
                                                
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">Medication List</label>
                                                <div class="col-sm-12">
                                                    <select name="medication_list" class="form-control" id="medication_list">
                                                        <option value="">Select Medication List</option>
                                                        <option value="Yes" {{ isset($search_param['medication_list']) && $search_param['medication_list'] == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                        <option value="No" {{ isset($search_param['medication_list']) && $search_param['medication_list'] == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">Insurance Elg</label>
                                                <div class="col-sm-12">
                                                    <select name="insurance_elg" class="form-control" id="insurance_elg">
                                                        <option value="">Select Insurance Elg</option>
                                                        <option value="Yes" {{ isset($search_param['insurance_elg']) && $search_param['insurance_elg'] == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                        <option value="No" {{ isset($search_param['insurance_elg']) && $search_param['insurance_elg'] == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">Mdo Tag</label>
                                                <div class="col-sm-12">
                                                    <select name="mdo_tag" class="form-control" id="mdo_tag">
                                                        <option value="">Select Mdo Tag</option>
                                                        <option value="Yes" {{ isset($search_param['mdo_tag']) && $search_param['mdo_tag'] == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                        <option value="No" {{ isset($search_param['mdo_tag']) && $search_param['mdo_tag'] == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="search-main1">
                                    <div class="search-inner">
                                        <div>
                                            <input type="button" name="search"
                                                class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                                value="Search">

                                                <a href="{{ url('/patient-service-requested')}}"
                                                class="btn btn-secondary btn-rounded btn-sm btn-fw  ml-1"
                                                id="test_reset"><i class="mdi mdi-refresh"></i>Reset</a>

                                                <a href="javascript:void(0)"
                                                class="btn btn-success btn-rounded btn-sm btn-fw  ml-1 btnExport"
                                                id="test_agency"><i class="mdi mdi-file-export"></i>Export</a>

                                                <img src="{{ asset('/ajax-loader.gif')}}" class="order-listing-loader1" alt="loader" id="loaderDashboardGraph" style="display:none">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="card">
            <div class="card-body compact-view">
                <div class="row">
                    <div class="col-12">
                        <span id="resp"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script
    src="{{ asset('assets/modulejs/patient_wise_service_request/patient_wise_service_request.js') }}?time={{ env('timestamps') }}">
</script>

<script>
    var _PATIENT_SERVICE_LIST = "{{ url('all-patient-service-requested-ajax-list') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var _AUTH_AGENCY_FK = "{{ $auth->agency_fk }}";
    var _AUTH_ID = "{{ $auth->id }}";
    var _USER_TYPE_FK = "{{ $auth->user_type_fk }}";
    var _PATIENT_EXPORT_URL = "{{ url('patient-service-requested-export') }}";
    var _DATE_TIME = "{{ date('m/d/Y') }}";
</script>

@include('include/footer')

<script src="{{ asset('/js/jquery.min.js')}}"></script>

<script src="{{ asset('/assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="{{ asset('assets/modulejs/service_requested_by_patient.js') }}?time={{ env('timestamps') }}"></script>
<script>
    $(function() {
        var start = moment().subtract(0, 'days');
        var end = moment();
        $('.datepickernn').daterangepicker({
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

            $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })

        $('.inservice_date').daterangepicker({
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

            $('.inservice_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })


        $('.due_datenn').daterangepicker({
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

            $('.due_datenn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })

        $('.completed_date').daterangepicker({
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

            $('.completed_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })

        $('.follow_up_date').daterangepicker({
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

            $('.follow_up_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })


        $('.traning_date').daterangepicker({
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

            $('.traning_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })


    });
    $(".datepicker").datepicker();

    $(function() {
        var start = moment().subtract(0, 'days');
        var end = moment();


        $('.datepicker1').daterangepicker({
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

            $('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })

        $('#last_status_update').daterangepicker({
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

            $('#last_status_update').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })
    });
    // Binds the hidden input to be used as datepicker.
    $('.datepicker-input').datepicker({
        dateFormat: 'mm/dd/yy',
        onClose: function(dateText, inst) {
            // When the date is selected, copy the value in the content editable div.
            // If you don't need to do anything on the blur or focus event of the content editable div, you don't need to trigger them as I do in the line below.
            if (dateText != '') {
                $(this).parent().find('.date').focus().html(dateText).blur();
            }
        }

    });

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

    var urlToken = "{{ url('search-nybest-user') }}";
    var empId = '';
    var empName = '';
    $("#created_by_ny").tokenInput(urlToken, {

        tokenLimit: 1,
        zindex: 9999,
        prePopulate: empId !== "" && empName !== "" ? [{
            id: empId,
            name: empName
        }] : [],
        onAdd: function(item) {
            $('#created_by_ny_id').val(item.id);
            $('#created_by_ny_name').val(item.name);
        },
        onDelete: function(item) {
            $('#created_by_ny_id').val('');
            $('#created_by_ny_name').val('');
        }
    });

    

    var lastUpdatedById ="";
    var lastUpdatedByName ="";
    $("#last_status_updated_by").tokenInput(urlToken, {

        tokenLimit: 1,
        zindex: 9999,
        prePopulate: lastUpdatedById !== "" && lastUpdatedByName !== "" ? [{
            id: lastUpdatedById,
            name: lastUpdatedByName
        }] : [],
        onAdd: function(item) {
            $('#last_status_updated_by').val(item.id);

        },
        onDelete: function(item) {
            $('#last_status_updated_by').val('');

        }
    });

    // Agency Filter Toggle Button
    $(document).ready(function() {
        const $toggleBtn = $('#agencyToggleBtn');
        const $toggleLabel = $('#agencyToggleLabel');
        const $filterTypeInput = $('#agency_filter_type');

        if ($toggleBtn.length === 0) {
            console.warn('Agency toggle button not found!');
            return;
        }

        // Update button appearance, label, and hidden input
        function updateButton(mode) {
            $toggleBtn.attr('data-mode', mode);
            $filterTypeInput.val(mode);

            if (mode === 'include') {
                $toggleBtn.html('<i class="mdi mdi-plus"></i>');
                $toggleBtn.attr('title', 'Include - Click to switch to Exclude');
                $toggleLabel.text('Include Agency').removeClass('mode-exclude').addClass('mode-include');
            } else {
                $toggleBtn.html('<i class="mdi mdi-minus"></i>');
                $toggleBtn.attr('title', 'Exclude - Click to switch to Include');
                $toggleLabel.text('Exclude Agency').removeClass('mode-include').addClass('mode-exclude');
            }
        }

        // Toggle on click with event delegation to handle dynamic content
        $(document).on('click', '#agencyToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateButton(newMode);
        });

        const $serviceToggleBtn = $('#serviceToggleBtn');
        const $serviceToggleLabel = $('#serviceToggleLabel');
        const $serviceFilterTypeInput = $('#service_filter_type');

        if ($serviceToggleBtn.length === 0) {
            console.warn('Service toggle button not found!');
            return;
        }

        // Update button appearance, label, and hidden input
        function updateServiceButton(mode) {
            $serviceToggleBtn.attr('data-mode', mode);
            $serviceFilterTypeInput.val(mode);

            if (mode === 'include') {
                $serviceToggleBtn.html('<i class="mdi mdi-plus"></i>');
                $serviceToggleBtn.attr('title', 'Include - Click to switch to Exclude');
                $serviceToggleLabel.text('Include Services').removeClass('mode-exclude').addClass('mode-include');
            } else {
                $serviceToggleBtn.html('<i class="mdi mdi-minus"></i>');
                $serviceToggleBtn.attr('title', 'Exclude - Click to switch to Include');
                $serviceToggleLabel.text('Exclude Services').removeClass('mode-include').addClass('mode-exclude');
            }
        }

        // Toggle on click with event delegation to handle dynamic content
        $(document).on('click', '#serviceToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateServiceButton(newMode);
        });
    });
</script>