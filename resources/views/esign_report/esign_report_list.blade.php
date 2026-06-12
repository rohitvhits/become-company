@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/document_report.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css') }}" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css')}}" />
<style>
    .table-responsive {
        overflow: visible !important;
    }
    .action-dropdown {
        position: relative;
    }
    .dropdown-menu {
        right: 0;
        left: auto;
    }
    .table-container {
        min-height: 200px;
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

    .radius-50 {
        border-radius: 50px;
    }

     .badge-outline-success{
        color:#3bb001 !important;
    }

     .fancybox-slide--iframe .fancybox-content {
      
        height: 800px;
        max-width: 100%;
        max-height: 100%;
        margin: 0;
        background: #191919;
    }
</style>
<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Esign Report List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" class="btn btn-success" id="bulkSendEsignReportBtn" style="display:none;color:#fff;" onclick="openBulkSendEsignReportModal()"><i class="mdi mdi-send"></i> Bulk Send</a>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form method="get" id="formsubmit">
                                <div class="row form-row-gap">
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="agency_fk">Agency Name</label>
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
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="patient_name">Patient Name</label>
                                                <input type="text" name="patient_name" class="form-control" id="patient_name" placeholder="Enter Patient Name">
                                                <input type="hidden" name="patient_name_id" id="patient_name_id">
                                                <input type="hidden" name="patientName" id="patientName">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="template_name">Template Name</label>
                                                <select name="template_name[]" id="template_name"
                                                    class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple">
                                                    <?php foreach ($templateList as $template) { ?>
                                                    <option value="<?php echo $template->id; ?>">
                                                        <?php echo $template->template_name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="created_at">Created Date</label>
                                                <input type="text" autocomplete="off" name="created_at"
                                                    class="datepicker1 form-control" id="created_at" value="{{$search_param['created_date']??''}}" placeholder="Select Created Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="completed_on">Completed Date</label>
                                                <input type="text" autocomplete="off" name="completed_on"
                                                    class="datepicker2 form-control" id="completed_on" placeholder="Select Completed Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="created_by">Created By</label>
                                                <input type="text" name="created_by" class="form-control" id="created_by" placeholder="Enter Created By">
                                                <input type="hidden" name="created_by_id" id="created_by_id">
                                                <input type="hidden" name="created_by_name" id="created_by_name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="status">Status</label>
                                                <select name="status" id="status"
                                                    class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100">
                                                    <option value="">Select Status</option>
                                                    <option value="all">All</option>
                                                    <option  @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Pending',$search_param['status'])) @php echo "selected='selected'" @endphp @endif  value="pending">Pending</option>
                                                    <option  @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Completed',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="completed">Completed</option>
                                                    <option  @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Approved',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="approved">Approved</option>
                                                    <option  @if(isset($search_param['status'][0]) && !empty($search_param['status'][0]) && in_array('Rejected',$search_param['status'])) @php echo "selected='selected'" @endphp @endif value="rejected">Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="type">Type</label>
                                                <select class="form-control border-class" name="type" id="type_id">
                                                    <option value="">Type</option>
                                                    <option @if(isset($search_param['type']) && !empty($search_param['type']) && $search_param['type'] == 'Caregiver') @php echo "selected='selected'" @endphp @endif value="Caregiver">Caregiver</option>
                                                    <option @if(isset($search_param['type']) && !empty($search_param['type']) && $search_param['type'] == 'Patient') @php echo "selected='selected'" @endphp @endif value="Patient">Patient</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="location_id">Template Type</label>
                                                <select class="form-control border-class" name="template_type" id="template_type">
                                                    <option value="">Template Type</option>
                                                    <option value="location">Location</option>
                                                    <option value="telehealth">Telehealth</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search">
                                            <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                                            class="mdi mdi-reload"></i>
                                        Reset</a>
                                        @can('esign-report-export')
                                            <a href="javascript:void(0)" class="btn btn-warning"
                                                onclick="exportCsv()"><i class="fa fa-file-o"></i> Export</a>
                                        @endcan
                                        <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag1" class="hide">
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
            <div class="location-wise-data-loader shimmer_id" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                    {{--<th><input type="checkbox" disabled></th>--}}
                                    <th>#</th>
                                    <th>Agency Name</th>
                                    <th>Patient Name</th>
                                    <th>Type</th>
                                    <th>Template Name</th>
                                    <th>Template Type</th>
                                    <th>Status</th>
                                    <th>Signers</th>
                                    <th>Sender</th>
                                    <th>Review By</th>
                                    <th>Created Date/Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="14"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <span id="resp"></span>
            </div>
        </div>
    </div>

    @include('esign_report.esign_sms_modal')
    @include('esign_report.esign_bulk_sms_modal')
    @include('esign_report.esign_report_move_document')
    @include('patient._partial.esign.esign_history')
    @include('patient._partial.esign.esign_log_modal')
    @include('patient._partial.esign.new_view_esign_log_modal')
    @include('patient._partial.esign.send_signer_request_modal_new')
    @include('patient._partial.esign.esign_sms_modal_new')


<div class="row" id="blank_div" style='margin-top: 30px;'>
       
    </div>
    @include('include/footer')
    @include('patient._partial.esign.esign_history')
    <script>
        var _ESIGN_REPORT_LIST = "{{ url('esign/esign-report/ajax-list') }}";
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var _ESIGN_REPORT_EXPORT_URL = "{{ url('esign/esign-report/esign-report-export') }}";
        var _DATE_TIME = "{{ date('m/d/Y') }}";
        var urlToken = "{{ url('search-nybest-patient') }}";
        var urlUserToken = "{{ url('esign/esign-report/search-nybest-all-user') }}";
        var _SMS_EMAIL_ESIGN_TEMPLATE = "{{ url('esign/patient-send-sms-esign') }}";
        var _BULK_SMS_EMAIL_ESIGN = "{{ url('esign/esign-report/esign-bulk-send-sms') }}";
        var _DELETE_ESIGN_TEMPLATE = "{{ url('esign/patient-docusign-delete') }}";
        var _GET_ALLOCATED_SIGNER = "{{ url('esign/allowcate-signer-request') }}";
        var _PATIENT_SERVICES = "{{ url('ajax-service')}}";
        var _PATIENT_REQUEST_SERVICES = "{{ url('ajax-request-service')}}";
        var _PATIENT_REQUESTED_BY_ID_SERVICES = "{{ url('ajax-patient-requested-service')}}";
        var _ESIGN_MOVE_DOCUMENT_STORE = "{{ url('esign/esign-move-document') }}";
        var _BASE_URL  = "{{ url('/')}}";
        var _ESIGN_HISTROY  = "{{ url('esign/esign-history')}}";
        var _BULK_SEND_SMS_URL = "{{ url('esign/esign-report/bulk-send-sms') }}";
        let _EMAIL = '';
        let _MOBILE = '';
        var _GENERATE_QR_CODE_LINK = '{{ url("esign/get-qr-code-link/") }}';
        let _RECORD_TYPE = "";
        let _AGENCYID = "";
        let _UNDO_STATUS_URL = "{{ url('esign/pdf/undo-status')}}";
        let _MODULE_TYPE_ESIGN = "esign-report";
        let _SEARCH_NYBEST_USER ="{{ url('search-nybest-user')}}";
        var _SEARCH_APPROVE_PATIENT_USER ="{{ url('get-approved-user-doc')}}";

        function getSignerNewData(groupId,rowId,patientId,email,mobile){
            _EMAIL = email;
            _MOBILE = mobile;
            getSignerNew(groupId,rowId,patientId)
        }

        $(document).on('click', '.move-to-document', function () {
            let type = $(this).data('patient-type');
            let agencyId = $(this).data('agency-id');
            let intakeId = $(this).data('intake-id');
            let templateId = $(this).data('template-id');
           
            _RECORD_TYPE = type;
            _AGENCYID = agencyId;
          
            viewServicesEsign();
            requestsServices(intakeId);
            showDocumentApproval();
            setTimeout(() => {
                  $(":input").inputmask();
                  $('#template_id').val(templateId);
                  $('#move_doc_record_id').val(intakeId)
            }, 200);
        });
       

        var _LOG_URL ="{{ url('esign/get-log-details')}}";
        var _COMMON_ESIGN_VIEW_LOG = "{{ url('esign/view-esign-log')}}";
        var _COMMON_ESIGN_RESPONSE_VIEW_LOG = "{{ url('esign/view-esign-response-log')}}";
        var _GENERATE_PATIENT_ESIGN_LINK = "{{ url('esign/generate-patient-esign-link')}}";
        var _UNDO_ESIGN_DATA = "{{ url('esign/undo-esign-data')}}";
        let userNewId = "";
        function getSendSMSByBulk(groupId,patientId){
            userNewId = patientId;
            getSendSMSNew1(groupId)
        }
        var type="";
    </script>

    <script src="<?php echo URL::to('/'); ?>/assets/js/jquery.tokeninput.js"></script>
    <script src="{{ asset('assets/modulejs/esign_report/esign_report.js') }}?time={{ time() }}"></script>
    <script src="{{ asset('assets/modulejs/esign_module_new.js') }}?time={{ env('timestamp') }}"></script>

    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js') }}"></script>
    <script src="<?= URL::to('assets/jquery-confirmation/js/jquery-confirm.min.js') ?>"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>

<script src="{{ asset('assets/js/jquery.fancybox.min.js')}}"></script>
