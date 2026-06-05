@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">

<link href="{{ asset('assets/css/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/token-input.css') }}" rel="stylesheet" type="text/css" />

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

    /* .order-listing-loader1 {
        position: absolute;
        left: 0;
        top: 0;
        background: #ffffff94;
        bottom: 0;
        right: 0;
        width: 100%;
        font-size: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
       
    } */
</style>
@php
    $auth = auth()->user();
@endphp
<div class="main-panel">

    <div class="content-wrapper">
        <div class="col-12 grid-margin-top">
            <div class="page-title-main">
                <h5 class="mb-0 font-weight-bold">NyBest Medical Requested(<span id="service_request_count">0</span>)
                </h5>
                <div class="page-rightbtns">

                </div>
            </div>
            <div class="row ">
                <div class="col-sm-12">
                    <div class="card search-card1" id="search-div">
                        <div class="card-body">
                            <form method="get" id="formsubmit">
                                @csrf
                                <div class="row">

                                    @if (in_array($user->user_type_fk, [3, 184]))
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-12 ">Agency Name</label>
                                                <div class="col-sm-12">
                                                    <select name="agency_fk[]" id="agency_fk"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple">
                                                        <?php foreach ($agencyList as $rwAgency) { ?>
                                                        <option
                                                            @if (isset($search_param['agency_fk'][0]) &&
                                                                    !empty($search_param['agency_fk'][0]) &&
                                                                    in_array($rwAgency->id, $search_param['agency_fk'])) @php echo "selected='selected'" @endphp @endif
                                                            value="<?php echo $rwAgency->id; ?>">
                                                            <?php echo $rwAgency->agency_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Name</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="first_name" id="first_name"
                                                    value="{{ $search_param['first_name'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Mobile</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="mobile" id="mobile"
                                                    value="{{ $search_param['mobile'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Services</label>
                                            <div class="col-sm-12">
                                                <select class="js-example-basic-multiple w-100 select2-design"
                                                    multiple="multiple" name="service_id[]" id="service_id">
                                                    <?php
                                                       foreach ($serviceList as $service) { ?>
                                                    <option
                                                        @if (isset($search_param['service_id'][0]) &&
                                                                !empty($search_param['service_id'][0]) &&
                                                                in_array($service->id, $search_param['service_id'])) @php echo "selected='selected'" @endphp @endif
                                                        value="<?php echo $service->id; ?>">
                                                        <?php echo $service->name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Booking Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" autocomplete="off" name="appointment_date"
                                                    class="datepicker1 form-control" id="appointment_date">
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
                                                    <option value="Caregiver"
                                                        @if (isset($search_param['type']) && !empty($search_param['type']) && $search_param['type'] == 'Caregiver') @php echo "selected='selected'" @endphp @endif>
                                                        Caregiver</option>
                                                    <option value="Patient"
                                                        @if (isset($search_param['type']) && !empty($search_param['type']) && $search_param['type'] == 'Patient') @php echo "selected='selected'" @endphp @endif>
                                                        Patient</option>

                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Created Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="created_date"
                                                    class="datepickernn form-control" id="created_date"
                                                    value="@if (isset($search_param['created_date'])) {{ $search_param['created_date'] }} @endif">
                                            </div>
                                        </div>
                                    </div>
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
                                </div>

                                <div class="search-main1">
                                    <div class="search-inner">
                                        <div>
                                            <input type="button" name="search"
                                                class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                                value="Search">

                                            <a href="{{ url('/hub-patient-service-requested') }}"
                                                class="btn btn-secondary btn-rounded btn-sm btn-fw  ml-1"
                                                id="test_reset"><i class="mdi mdi-refresh"></i>Reset</a>

                                            <a href="javascript:void(0)"
                                                class="btn btn-success btn-rounded btn-sm btn-fw  ml-1 btnExport"
                                                id="test_agency"><i class="mdi mdi-file-export"></i>Export</a>

                                            <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1"
                                                alt="loader" id="loaderDashboardGraph" style="display:none">
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
    src="{{ asset('assets/modulejs/patient_wise_service_request/hub_patient_wise_service_request.js') }}?time={{ env('timestamps') }}">
</script>

<script>
    var _PATIENT_SERVICE_LIST = "{{ url('hub-patient-service-requested-ajax-list') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var _AUTH_AGENCY_FK = "{{ $auth->agency_fk }}";
    var _AUTH_ID = "{{ $auth->id }}";
    var _USER_TYPE_FK = "{{ $auth->user_type_fk }}";
    var _PATIENT_EXPORT_URL = "{{ url('hub-patient-service-requested-export') }}";
    var _DATE_TIME = "{{ date('m/d/Y') }}";
</script>

@include('include/footer')

<script src="{{ asset('/js/jquery.min.js') }}"></script>

<script src="{{ asset('/assets/js/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css') }}" />
<script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/jquery.tokeninput.js') }}"></script>

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
</script>
