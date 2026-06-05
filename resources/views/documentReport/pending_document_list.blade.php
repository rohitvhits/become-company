@include('include/header')
@include('include/sidebar')
<link href="{{asset('assets/css/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/token-input.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/document_report.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Pending Document Report(<span id="total_record_id"></span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency Name</label>
                                                    <select name="agency_fk[]" id="agency_fk"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" data-placeholder="Select Agency Name">
                                                        <?php foreach ($agencyList as $rwAgency) { ?>
                                                            <option value="<?php echo $rwAgency->id; ?>">
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
                                                    <label>Portal ID</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="patient_id" placeholder="Enter Portal ID">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Type</label>
                                                    <select class="form-control" name="type" id="patient_type" class="form-control">
                                                        <option value="">Select Type</option>
                                                        <option value="Caregiver">Caregiver</option>
                                                        <option value="Patient">Patient</option>

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
                                                    <input type="text" autocomplete="off" name="appointment_date" class="datepicker1 form-control" id="appointment_date" placeholder="Select Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created By</label>
                                                    <input type="text" autocomplete="off" class="form-control" name="created_by" id="document_created_by" style="width:100% !important">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Assign Document User</label>
                                                    <input type="text" autocomplete="off" name="assign_document_user" class="form-control" id="assign_document_user">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Document Completion Date</label>
                                                    <input type="text" autocomplete="off" name="appointment_date" class="form-control" id="completion_date" placeholder="Select Document Completion Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Document Review Date</label>
                                                    <input type="text" autocomplete="off" name="appointment_date" class="form-control" id="document_review_date" placeholder="Select Document Review Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Document Review By</label>
                                                    <input type="text" autocomplete="off" name="review_document_user" class="form-control" id="review_document_user">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box mt-3">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-check form-check-flat form-check-primary" style="margin-top: 15px important ; margin-left: 5px !important;">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" class="form-check-input" id="archived" value="1">
                                                            Internal Use Only
                                                            <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box mt-3">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-check form-check-flat form-check-primary" style="margin-top: 15px important ; margin-left: 5px !important;">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" class="form-check-input" id="pending_doc" value="1">
                                                            Pending Document
                                                            <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
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
                                            value="Search" onclick="loadAjaxList()">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                        @can('document-report-export')
                                        <a href="javascript:void(0)" class="btn btn-success btn-rounded" onclick="exportCsv()">Export</a>
                                        <a href="javascript:void(0)" class="btn btn-info btn-rounded" onclick="exportCsvWithoutService()">Export V2</a>
                                        @endcan
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
                <div class="location-wise-data-loader shimmer_id">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <th>#</th>
                                <th nowrap>Agency Name</th>
                                <th nowrap>Portal ID</th>
                                <th nowrap>Type</th>
                                <th nowrap>Patient Name</th>
                                <th nowrap>Document Name</th>
                                <th nowrap>Attachment</th>
                                <th nowrap>Requested Id</th>
                                <th nowrap>Attachment Service</th>
                                <th nowrap>Document Completion Date</th>
                                <th nowrap>Created Date/ Created By</th>
                                <th nowrap>Review By</th>
                                <!-- <th>Last Updated By</th> -->
                                @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                                <th>Action</th>
                                @endif
                            </thead>
                            <tbody class="loading-shimmer">
                                <tr>
                                    <td colspan="13"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <span id="response_requested_id">

                </span>



            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>

    </div>

</div>

@include('documentReport._partial.modal.edit_service_document')
@include('documentReport._partial.modal.review_document')
@include('patient._partial.modal.patient_document.view_document_details_modal')
@include('include/footer')

<script>
    var _LOAD_DATA_URL = "{{ url('pending-document-ajax-list')}}";

    var _EXPORT_CSV = "{{ url('document-export-csv')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
    var _PATIENT_REQUEST_SERVICES = "{{ url('ajax-request-service')}}";
    var _PATIENT_SERVICES = "{{ url('ajax-service')}}";
    var _DELETE_DOCUMENT = "{{url('patient/document-ajax-delete/')}}";
    var _CSRF_TOKEN = "{{ csrf_token()}}";
    var _UPDATE_DOCUMENT_SERVICES = "{{ url('update-document-service')}}";

    var _DOCUMENT_REDIRECTION_FLAG = 1;
    var _EXPORT_CSV_WITHOUT_SERVICE = "{{ url('document-export-csv-new')}}";
    var _SHOW_DOCUMENT_NAME = "{{ url('/temp-download-url')}}";
    var _DOCUMENT_SEND_REPORT_DETAILS_BY_ID = "{{ url('document-review-by-id') }}"
    var _UPLOAD_DOCUMENT_REVIEW_BY_ID = "{{ url('update-document-review') }}";
    var _SEARCH_CREATED_BY_USER = "{{ url('search-nybest-all-user') }}";
    var _SEARCH_NYBEST_USER = "{{ url('search-nybest-user') }}";
</script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<script src="{{ asset('assets/modulejs/documentReport/pending_document_report.js')}}?time={{ time()}}"></script>
<script src="{{ asset('assets/modulejs/service_requested_by_patient.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/modulejs/patient_module.js')}}?time={{ env('timestamp')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />

<script>
    $('.document_completed_date').datepicker();
</script>