@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link href="{{ asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/token-input.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/global.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />

<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
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

    .agency-toggle-label.mode-include, .service-toggle-label.mode-include {
        color: #0d6efd;
    }

    .agency-toggle-label.mode-exclude, .service-toggle-label.mode-exclude {
        color: #6c757d;
    }
    .nowrap{
        white-space: nowrap;
    }
    .tableData .add_new_record{
        position: absolute;
        top: 0;
        background: #00BBE0;
        padding: 1px 5px;
        font-size: 10px;
        color: #fff;
        border-radius: 2px 2px 2px 2px;
        font-size: 10px !important;
    }
</style>
@php
    $auth = auth()->user();
@endphp
<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Service Requested (<span id="service_request_count">0</span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span>
                    </a>

                    @can('add-new-service-request')
                    {{-- <a href="{{ url('patient-add-new') }}" class="btn btn-primary cust-right-btn"><i class="mdi mdi-plus"></i> Create New</a> --}}
                    @endcan
                </div>
            </div>
        </div>
        <hr />

        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form method="get" id="formsubmit">
                                @csrf
                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Status</label>
                                                    <select name="status[]" id="status_id"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple">
                                                        <option value=""></option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Pending',$search_param['status'])) selected='selected' @endif value="Pending">Pending</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Cancelled',$search_param['status'])) selected='selected' @endif value="Cancelled">Cancelled</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Booked',$search_param['status'])) selected='selected' @endif value="booked">Booked</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Completed',$search_param['status'])) selected='selected' @endif value="Completed">Completed</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('noshow',$search_param['status'])) selected='selected' @endif value="noshow">No Show</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('arrived',$search_param['status'])) selected='selected' @endif value="arrived">Arrived</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('processing',$search_param['status'])) selected='selected' @endif value="processing">Processing</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Not interested',$search_param['status'])) selected='selected' @endif value="Not interested">Not Interested</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('hospitalized/rehab',$search_param['status'])) selected='selected' @endif value="hospitalized/rehab">Hospitalized/Rehab</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('UnableToContact',$search_param['status'])) selected='selected' @endif value="UnableToContact">Unable To Contact</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Refused',$search_param['status'])) selected='selected' @endif value="Refused">Refused</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('checkin',$search_param['status'])) selected='selected' @endif value="checkin">Mark As CheckIn</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('PendingTermination',$search_param['status'])) selected='selected' @endif value="PendingTermination">Pending Termination</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('OnHold',$search_param['status'])) selected='selected' @endif value="OnHold">On Hold</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('OnLeave',$search_param['status'])) selected='selected' @endif value="OnLeave">On Leave</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Terminated',$search_param['status'])) selected='selected' @endif value="Terminated">Terminated</option>
                                                        <option @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Inactive',$search_param['status'])) selected='selected' @endif value="Inactive">Inactive</option>
                                                        @if(isset($statuses) && !empty($statuses))
                                                            @foreach ($statuses as $status)
                                                                <option
                                                                    @if (isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array($status, $search_param['status']))
                                                                        selected='selected'
                                                                    @endif
                                                                    value="{{ $status }}">
                                                                    {{ $status }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if (in_array($user->user_type_fk, [3, 184]))
                                        <div class="col-md-3">
                                            <div class="form-group cust-select-box">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label>
                                                            Agency Name
                                                            <span class="agency-filter-toggle-wrapper">
                                                                <button type="button" class="agency-toggle-btn" id="agencyToggleBtn"
                                                                        data-mode="include" title="Include - Click to switch to Exclude">
                                                                    <i class="mdi mdi-plus"></i>
                                                                </button>
                                                                <span class="agency-toggle-label mode-include" id="agencyToggleLabel">Include Agency</span>
                                                            </span>
                                                        </label>
                                                        <input type="hidden" name="agency_filter_type" id="agency_filter_type"
                                                               value="@if(isset($search_param['agency_filter_type']) && $search_param['agency_filter_type'] == 'exclude')exclude @else include @endif">
                                                        <select name="agency_fk[]" id="agency_fk"
                                                            class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                            multiple="multiple">
                                                            @foreach ($agencyList as $rwAgency)
                                                                <option @if(isset($search_param['agency_fk'][0]) && !empty($search_param['agency_fk'][0]) && in_array($rwAgency->id,$search_param['agency_fk'])) selected='selected' @endif value="{{ $rwAgency->id }}">
                                                                {{ $rwAgency->agency_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Code</label>
                                                    <input autocomplete="off" type="text" class="form-control"
                                                        name="patient_code" id="patient_code" value="{{$search_param['patient_code']??''}}" placeholder="Enter Code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Name</label>
                                                    <input autocomplete="off" type="text" class="form-control"
                                                        name="first_name" id="first_name" value="{{$search_param['first_name']??''}}" placeholder="Enter Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Mobile</label>
                                                    <input autocomplete="off" type="text" class="form-control"
                                                        name="mobile" id="mobile" value="{{$search_param['mobile']??''}}" placeholder="Enter Mobile">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>
                                                        Services
                                                        <span class="service-filter-toggle-wrapper">
                                                            <button type="button" class="service-toggle-btn" id="serviceToggleBtn"
                                                                    data-mode="include" title="Include - Click to switch to Exclude">
                                                                <i class="mdi mdi-plus"></i>
                                                            </button>
                                                            <span class="service-toggle-label mode-include" id="serviceToggleLabel">Include Services</span>
                                                        </span>
                                                    </label>
                                                    <input type="hidden" name="service_filter_type" id="service_filter_type"
                                                               value="@if(isset($search_param['service_filter_type']) && $search_param['service_filter_type'] == 'exclude')exclude @else include @endif">
                                                    <select class="form-control js-example-basic-multiple w-100 select2-design cal-padding-0"
                                                        multiple="multiple" name="service_id[]" id="service_id">
                                                        @foreach ($serviceList as $service)
                                                            <option @if(isset($search_param['service_id'][0]) && !empty($search_param['service_id'][0]) && in_array($service->id,$search_param['service_id'])) selected='selected' @endif value="{{ $service->id }}">
                                                            {{ $service->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Assign To</label>
                                                    <select name="assign_user_id[]"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" id="assign_user_id">
                                                        @if (!empty($assign_user_list[0]))
                                                            @foreach ($assign_user_list as $assigns)
                                                                <option @if(isset($search_param['assign_user_id'][0]) && !empty($search_param['assign_user_id'][0]) && in_array($assigns->id,$search_param['assign_user_id'])) selected='selected' @endif value="{{ $assigns->id }}">
                                                                    {{ $assigns->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Due Date</label>
                                                    <input type="text" name="due_date" class="due_datenn form-control"
                                                        id="due_date" autocomplete="off" placeholder="Select Due Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Appointment Date</label>
                                                    <input type="text" autocomplete="off" name="appointment_date"
                                                        class="datepicker1 form-control" id="appointment_date" placeholder="Select Appointment Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Location</label>
                                                    <select name="locationId[]"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" id="locationId">
                                                        @foreach ($location_list as $vsl)
                                                            <option @if(isset($search_param['locationId'][0]) && !empty($search_param['locationId'][0]) && in_array($vsl->id,$search_param['locationId'])) selected='selected' @endif value="{{ $vsl->id }}">
                                                            {{ $vsl->address1 }}</option>
                                                        @endforeach
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
                                                    <input type="text" name="created_date"
                                                        class="datepickernn form-control" id="created_date" autocomplete="off" placeholder="Select Created Date"
                                                        value="@if(isset($search_param['created_date'])){{ $search_param['created_date']}} @endif">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>SMS Status</label>
                                                    <select name="sms_status[]" id="sms_status"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple">
                                                        <option value="0">Pending</option>
                                                        <option value="1">Sent</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Discipline</label>
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
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Type</label>
                                                    <select class="form-control" name="type" id="type">
                                                        <option value="">Select Type</option>
                                                        <option value="Caregiver" @if(isset($search_param['type']) && !empty($search_param['type']) && $search_param['type'] == 'Caregiver') selected='selected' @endif>Caregiver</option>
                                                        <option value="Patient" @if(isset($search_param['type']) && !empty($search_param['type']) && $search_param['type'] == 'Patient') selected='selected' @endif>Patient</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>In Service Date</label>
                                                    <input type="text" name="inservice_date"
                                                        class="inservice_date form-control" id="inservice_date" autocomplete="off" placeholder="Select In Service Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Completed Date</label>
                                                    <input type="text" name="completed_date"
                                                        class="completed_date form-control" id="completed_date" autocomplete="off" placeholder="Select Completed Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Follow Up Date</label>
                                                    <input type="text" name="follow_up_date"
                                                        class="follow_up_date form-control" id="follow_up_date" autocomplete="off" placeholder="Select Follow Up Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($auth->agency_fk == 106 || $auth->id == 482)
                                        <div class="col-md-3">
                                            <div class="form-group cust-select-box">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label>Training Due Date</label>
                                                        <input type="text" name="traning_date"
                                                            class="traning_date form-control" id="traning_date" autocomplete="off" placeholder="Select Training Due Date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created By</label>
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
                                                        <input type="hidden" name="created_by_ny_id" id="created_by_ny_id">
                                                        <input type="hidden" name="created_by_ny_name" id="created_by_ny_name">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Language</label>
                                                    <select name="language_id" class="form-control" id="language_id">
                                                        <option value="">Select Language</option>
                                                        @foreach ($language_list as $vsl)
                                                            <option value="{{$vsl->id}}" @if (isset($search_param['language_id']) && !empty($search_param['language_id']) && $vsl->id == $search_param['language_id']) selected @endif>
                                                                {{$vsl->name}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Transition Aid</label>
                                                    <select name="transition_aid" class="form-control" id="transition_aid">
                                                        <option value="">Select Transition Aid</option>
                                                        <option value="1" @if( isset($search_param['transition_aid']) && !empty($search_param['transition_aid']) && $search_param['transition_aid'] == "1" ) selected @endif>Yes</option>
                                                        <option value="0" @if( isset($search_param['transition_aid']) && !empty($search_param['transition_aid']) && $search_param['transition_aid'] == "0" ) selected @endif>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if (in_array($user->user_type_fk, [3, 184]))
                                        <div class="col-md-3">
                                            <div class="form-group cust-select-box">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label>Last Status Updated Date</label>
                                                        <input type="text" name="last_status_update" class="form-control" id="last_status_update" autocomplete="off" placeholder="Select Date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group cust-select-box">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label>Last Status Updated By</label>
                                                        <input type="text" name="last_status_updated_by" id="last_status_updated_by">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (in_array($user->user_type_fk, [3, 184]))
                                        <div class="col-md-3">
                                            <div class="form-group cust-select-box">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label>Training Status</label>
                                                        <select name="training_status[]"
                                                            class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                            id="training_status" multiple>
                                                            <option value="">Select Training Status</option>
                                                            <option value="Completed">Completed</option>
                                                            <option value="Processing">Processing</option>
                                                            <option value="Refused">Refused</option>
                                                            <option value="Unable to reach">Unable to reach</option>
                                                            <option value="Need to assistance">Need to assistance</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row form-row-gap mb-2">
                                    @if (in_array($user->user_type_fk, [3, 184]))
                                        <div class="col-md-3">
                                            <div class="form-group cust-select-box">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label>Medication List</label>
                                                        <select name="medication_list" class="form-control" id="medication_list">
                                                            <option value="">Select Medication List</option>
                                                            <option value="Yes" {{ isset($search_param['medication_list']) && $search_param['medication_list'] == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ isset($search_param['medication_list']) && $search_param['medication_list'] == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group cust-select-box">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label>Insurance Elg</label>
                                                        <select name="insurance_elg" class="form-control" id="insurance_elg">
                                                            <option value="">Select Insurance Elg</option>
                                                            <option value="Yes" {{ isset($search_param['insurance_elg']) && $search_param['insurance_elg'] == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ isset($search_param['insurance_elg']) && $search_param['insurance_elg'] == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group cust-select-box">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label>Mdo Tag</label>
                                                        <select name="mdo_tag" class="form-control" id="mdo_tag">
                                                            <option value="">Select Mdo Tag</option>
                                                            <option value="Yes" {{ isset($search_param['mdo_tag']) && $search_param['mdo_tag'] == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ isset($search_param['mdo_tag']) && $search_param['mdo_tag'] == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row form-row-gap mt-3">
                                    <div class="col-md-9">
                                        <div class="appointment-btn-box" style="justify-content:left !important">
                                            <input type="button" name="search"
                                                class="btn search-btn1 searchAppoinment" id="search-data"
                                                value="Search">

                                            <a href="{{ url('/patient-service-requested')}}"
                                                class="btn btn-light btn-rounded btn-fw btn-sm" id="test_reset">
                                                <i class="mdi mdi-reload"></i> Clear
                                            </a>

                                            <a href="javascript:void(0)"
                                                class="btn cust-right-btn btnExport" style="background-color: #28a745;color:#fff;"
                                                id="test_agency">
                                                <i class="mdi mdi-file-export"></i> Export
                                            </a>

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

        <div class="row">
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id hasClass">
                    <div class="col-md-12 pl-0 table-responsive tableData">
                        <table class="table table-bordered  table-width1">
                            <thead>
                                <th>ID</th>
                                <th class="nowrap">Portal ID</th>
                                <th class="nowrap">Status</th>
                                <th class="nowrap">Portal Status</th>
                                @if(in_array($user->user_type_fk, array(3, 184)))
                                <th class="nowrap">Agency Name</th>
                                @endif
                                <th class="nowrap">Type/<br>Discipline</th>
                                <th>Code</th>
                                <th class="nowrap">Name/<br>Mobile/<br>DOB/<br>Services</th>
                                <th class="nowrap">Assigned To</th>
                                <th class="nowrap">Due Date</th>
                                <th class="nowrap">Appointment Date - Location</th>
                                <th class="nowrap">Created Date</th>
                                <th class="nowrap">FU Date</th>
                                <th class="nowrap">In Service Date</th>
                                <th class="nowrap">Completed Date</th>
                                <th class="nowrap">Follow Up Date</th>
                                <th class="nowrap">Training Due Date</th>
                                <th class="nowrap">Training Status</th>
                                <th class="nowrap">Last Status Updated Date /<br> Last Status Updated By</th>
                                <th class="nowrap">Referral Type</th>
                                <th>Action</th>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="22"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="resp"></span>
            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'></div>
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
<script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="{{ asset('assets/modulejs/service_requested_by_patient.js') }}?time={{ env('timestamps') }}"></script>
<script>
    $('#filter-btn').click(function() {
        $("#search-filter-btn").toggle();
    });

    $(function() {
        var start = moment().subtract(0, 'days');
        var end = moment();

        var dateRangeConfig = {
            autoUpdateInput: false,
            startOfWeek: 'sunday',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
                'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
                'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
            }
        };

        function applyDateRange(selector) {
            $(selector).daterangepicker(
                $.extend({}, dateRangeConfig, { startDate: start, endDate: end }),
                function(chosen_date, end_date) {
                    $(selector).val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
                }
            );
        }

        applyDateRange('.datepickernn');
        applyDateRange('.inservice_date');
        applyDateRange('.due_datenn');
        applyDateRange('.completed_date');
        applyDateRange('.follow_up_date');
        applyDateRange('.traning_date');
        applyDateRange('.datepicker1');
        applyDateRange('#last_status_update');
    });

    $(".datepicker").datepicker();

    // Binds the hidden input to be used as datepicker.
    $('.datepicker-input').datepicker({
        dateFormat: 'mm/dd/yy',
        onClose: function(dateText, inst) {
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

    var lastUpdatedById = "";
    var lastUpdatedByName = "";
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

        $(document).on('click', '#serviceToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateServiceButton(newMode);
        });
    });
</script>
