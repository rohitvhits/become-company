@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/document_report.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Document Report(<span id="total_record_id"></span>)</h5>
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
                                    @if($agencyCnt > 1)
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency Name</label>
                                                    <select name="agency_fk[]" id="agency_fk"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" data-placeholder="Select Agency Name">
                                                        <?php foreach ($agencyList as $rwAgency) { ?>
                                                            <option value="<?php echo $rwAgency['id']; ?>">
                                                                <?php echo $rwAgency['agency_name']; ?></option>
                                                        <?php } ?>
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
                                                    <label>Portal ID</label>
                                                    <input type="text" autocomplete="off" class="form-control"  id="patient_id" placeholder="Enter Portal ID">
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
                                    @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
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
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Document Completion Date</label>
                                                    <input type="text" autocomplete="off" name="appointment_date" class="datepicker1 form-control" id="completion_date"  placeholder="Select Document Completion Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Document Review Date</label>
                                                    <input type="text" autocomplete="off" name="appointment_date" class="datepicker1 form-control" id="document_review_date" placeholder="Select Document Review Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
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
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Services</label>
                                                    <select class="js-example-basic-multiple w-100 select2-design" multiple="multiple" name="service_id[]" id="service_id">
                                                        @foreach ($serviceList as $service)
                                                            <option value="{{$service->id}}">{{$service->name}}</option>
                                                        @endforeach
                                                    </select>
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
                                    @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
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

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">MDO Tag</label>
                                            <div class="col-sm-12">
                                                <select name="mdo_tag" class="form-control" id="mdo_tag">
                                                    <option value="">Select MDO Tag</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12">MDO Source</label>
                                            <div class="col-sm-12">
                                                <select class="form-control form-control-sm" name="mdo_source" id="mdo_source">
                                                    <option value="">Select MDO Source</option>
                                                    @foreach($masterData as $master)
                                                        <option value="{{ $master->id}}">{{$master->name}}</option>
                                                    @endforeach
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
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Medication List</label>
                                            <div class="col-sm-12">
                                                <select name="medication_list" class="form-control" id="medication_list">
                                                    <option value="">Select Medication List</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12">Branch</label>
                                            <div class="col-sm-12">
                                                <select class="form-control form-control-sm" name="branch_id" id="branch_id">
                                                    <option value="">Select Branch</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                                                        <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Send Back to Agency</label>
                                            <div class="col-sm-12">
                                                <select name="send_email" class="form-control" id="send_email">
                                                    <option value="">Select Send Back to Agency</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
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
                                        <a href="javascript:void(0)" class="btn btn-success btn-rounded" onclick="exportCsv2()">Export2</a>
                                        {{-- <a href="javascript:void(0)" class="btn btn-info btn-rounded" onclick="exportCsvWithoutService()">Export V2</a> --}}
                                        @endcan
                                        @if(auth()->user()->agency_fk != "")
                                            <a href="javascript:void(0)" class="btn btn-success btn-rounded" onclick="exportCsv()">Export</a>
                                        @endif
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
                <div class="location-wise-data-loader shimmer_id" >
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
                                <th nowrap>Document Completion Date</th>
                                <th nowrap>Created Date/ Created By</th>
                                <th nowrap>Modified Date/ Modified By</th>
                                <th nowrap>Review By</th>
                                <!-- <th>Last Updated By</th> -->
                                @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                                <th>Action</th>
                                @endif
                            </thead>
                            <tbody class="shimmer-loader">
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
@include('patient._partial.modal.patient_document.view_document_details_modal')
@include('documentReport._partial.modal.doc_review_model')
@include('include/footer')

<script>
    var _LOAD_DATA_URL = "{{ url('document-ajax-list')}}";

    var _EXPORT_CSV = "{{ url('document-export-csv')}}";
    var _EXPORT_CSV_TWO = "{{ url('document-export-csv-two')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
    var _PATIENT_REQUEST_SERVICES = "{{ url('ajax-request-service')}}";
    var _PATIENT_SERVICES = "{{ url('ajax-service')}}";
    var _DELETE_DOCUMENT = "{{url('patient/document-ajax-delete/')}}";
    var _CSRF_TOKEN = "{{ csrf_token()}}";
    var _UPDATE_DOCUMENT_SERVICES = "{{ url('update-document-service')}}";

    var _DOCUMENT_REDIRECTION_FLAG = 1;
    var _EXPORT_CSV_WITHOUT_SERVICE = "{{ url('document-export-csv-new')}}";
    var _SHOW_DOCUMENT_NAME ="{{ url('/temp-download-url')}}";
    var _DOCUMENT_SEND_REPORT_DETAILS_BY_ID = "{{ url('document-review-by-id') }}"
    var _UPLOAD_DOCUMENT_REVIEW_BY_ID = "{{ url('update-document-review-internal') }}";
    var _SEARCH_CREATED_BY_USER = "{{ url('search-nybest-all-user') }}";
    var _SEARCH_NYBEST_USER = "{{ url('search-nybest-user') }}";
    var _GET_SERVICES_OF_DOCUMENT = "{{ url('get-service-of-doc-id') }}";
    var SAVE_DOC_NAME = "{{ url('update-document-name') }}";
    var PATIENT_URL = "{{ url('patient/view') }}";
    var DOC_URL = "{{ url('esign/write-document') }}";
    var uniqId = "{{uniqid()}}";
    var _GET_BRANCHES_BY_AGENCY_SERVICES = "{{ url('get-branches') }}";
</script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<script src="{{ asset('assets/modulejs/documentReport/document_report.js')}}?time={{ time()}}"></script>
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