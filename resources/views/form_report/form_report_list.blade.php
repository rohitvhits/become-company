@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/document_report.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css') }}" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/css/jquery.fancybox.min.css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">

<style>
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

    .custom-card-size {
        height: 70px;
        padding: 10px;
    }

    .custom-card-size .card-body {
        padding: 5px;
    }

    .custom-class {
        max-width: 15%;
    }

    #cover-spin {

        width: 50px;
        aspect-ratio: 1;
        display: grid;
        border-radius: 50%;
        background:
            linear-gradient(0deg, rgb(0 0 0/50%) 30%, #0000 0 70%, rgb(0 0 0/100%) 0) 50%/8% 100%,
            linear-gradient(90deg, rgb(0 0 0/25%) 30%, #0000 0 70%, rgb(0 0 0/75%) 0) 50%/100% 8%;
        background-repeat: no-repeat;
        animation: l23 1s infinite steps(12);
        position: absolute;
        top: 50%;
        left: 50%;
    }

    #cover-spin::before,
    #cover-spin::after {
        content: "";
        grid-area: 1/1;
        border-radius: 50%;
        background: inherit;
        opacity: 0.915;
        transform: rotate(30deg);
    }

    #cover-spin::after {
        position: absolute;
        opacity: 0.83;
        transform: rotate(60deg);
    }

    @keyframes l23 {
        100% {
            transform: rotate(1turn)
        }
    }

    .loader-sec {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background: rgb(0 0 0 / 20%);
        z-index: 999;
    }

    .hide {
        display: none;
    }

    .dropdown-item {
        padding: 0.4rem 1.5rem;
    }

    .status-dropdoown .btn-warning {
        border-radius: 20px;
        padding: 5px 15px !important;
        display: flex;
        align-items: center;
    }

    .fancybox-slide--iframe .fancybox-content {
        width: 800px;
        height: 600px;
        max-width: 100%;
        max-height: 100%;
        margin: 0;
        background: #191919;
    }

    .radius-50 {
        border-radius: 50px;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Form Report List</h5>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card custom-class">
                                <div class="card custom-card-size">
                                    <div class="card-body">
                                        <h4 class="card-title">Completed</h4>
                                        <div class="d-flex justify-content-between">
                                            <p class="badge badge-outline-success badge-pill completed-count"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card custom-class">
                                <div class="card custom-card-size">
                                    <div class="card-body">
                                        <h4 class="card-title">Pending</h4>
                                        <div class="d-flex justify-content-between">
                                            <p class="badge badge-outline-warning badge-pill pending-count"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form method="get" id="formsubmit">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Form Name</label>
                                        <div class="col-sm-12">
                                            <select name="form_name[]" id="form_name"
                                                class="form-control cal-padding-0 js-example-basic-multiple w-100"
                                                multiple="multiple">
                                                <?php foreach ($formList as $form) { ?>
                                                <option value="<?php echo $form->id; ?>">
                                                    <?php echo $form->title; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Agency Name</label>
                                        <div class="col-sm-12">
                                            <select name="agency_fk[]" id="agency_fk"
                                                class="form-control cal-padding-0 js-example-basic-multiple w-100"
                                                multiple="multiple">
                                                <?php foreach ($agencyList as $rwAgency) { ?>
                                                <option value="<?php echo $rwAgency->id; ?>">
                                                    <?php echo $rwAgency->agency_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Patient Name</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="patient_name" id="patient_name">
                                            <input type="hidden" name="patient_name_id" id="patient_name_id">
                                            <input type="hidden" name="patientName" id="patientName">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-sm-12 ">Created Date</label>
                                    <div class="col-sm-12">
                                        <input type="text" autocomplete="off" name="created_at"
                                            class="datepicker1 form-control" id="created_at">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Created By</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="created_by" id="created_by">
                                            <input type="hidden" name="created_by_id" id="created_by_id">
                                            <input type="hidden" name="created_by_name" id="created_by_name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-sm-12 ">Mark As Completed Date</label>
                                    <div class="col-sm-12">
                                        <input type="text" autocomplete="off" name="mark_as_completed_date"
                                            class="datepicker2 form-control" id="mark_as_completed_date">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Mark As Completed By</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="mark_as_completed_by" id="mark_as_completed_by">
                                            <input type="hidden" name="mark_as_completed_by_id" id="mark_as_completed_by_id">
                                            <input type="hidden" name="mark_as_completed_by_name" id="mark_as_completed_by_name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Status</label>
                                        <div class="col-sm-12">
                                            <select name="status" id="status"
                                                class="form-control cal-padding-0 js-example-basic-multiple w-100">
                                                <option value="">Select Status</option>
                                                <option value="all">All</option>
                                                <option value="pending">Pending</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="card-footer">
                            <input type="button" name="search" class="btn btn-primary btn-rounded"
                                id="search-data" value="Search" onclick="loadEsignReportList(1)">
                            <a href="javascript:void(0)" class="btn btn-secondary btn-rounded"
                                onclick="refresh()">Clear</a>
                            @can('form-report-export')
                                <a href="javascript:void(0)" class="btn btn-success btn-rounded"
                                    onclick="exportCsv()">Export</a>
                            @endcan
                            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag1"
                                class="hide">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <span id="resp"></span>
            </div>
        </div>
    </div>
    @include('include/footer')

    <script>
        var _FORM_REPORT_LIST = "{{ url('form-report-ajax-list') }}";
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var _FORM_REPORT_EXPORT_URL = "{{ url('form-report-export') }}";
        var _DATE_TIME = "{{ date('m/d/Y') }}";
        var getTemplateData = "{{ route('get.templateData') }}";
        var storeMoveToEsignData = "{{ route('store-move-to-esign') }}";
        var urlToken = "{{ url('search-nybest-patient') }}";
        var urlUserToken = "{{ url('search-nybest-all-user') }}";
    </script>

    <script src="{{ asset('js/jquery_new.min.js') }}"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/jquery.tokeninput.js"></script>
    <script src="{{ asset('assets/modulejs/form_report/form_report.js') }}?time={{ time() }}"></script>

    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js') }}"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/jquery.fancybox.min.js"></script>
    <script src="<?= URL::to('assets/jquery-confirmation/js/jquery-confirm.min.js') ?>"></script>
