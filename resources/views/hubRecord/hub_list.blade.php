@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css') }}" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/modulejs/css/hub_record/hub_record.css') }}">
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
    rel="stylesheet" />
<style>
    .form-check-label {
        margin-left: 25px !important;
    }
</style>

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Hub Records(<span id="total_record_id"></span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('hub-record-guide')
                    <a href="https://sites.google.com/nybestmedical.com/nybestmedical/home" target="_blank"
                        class="btn btn-primary btn-sm btn-fw cust-right-btn"><i class="mdi mdi-phone"></i>Call Center
                        Guide</a>
                    @endcan
                    @can('hub-record-add')
                    <a href="{{ url('create-hub-record') }}" class="btn btn-primary btn-sm btn-fw cust-right-btn"><i
                            class="mdi mdi-plus"></i>Create New</a>
                    @endcan
                    @can('hub-record-import')
                    <a href="javascript:void(0)" data-toggle="modal"
                        class="btn btn-secondary btn-sm btn-fw cust-right-btn" data-target="#import-modal"
                        data-whatever="@mdo" onclick="openImportModal();"><i class="mdi mdi-file-export"></i>Import</a>
                    @endcan
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn"
                        style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter
                        <span></span></a>

                    <a class="mr-1 btn btn-info btn-sm btn-fw cust-right-btn" data-toggle="modal" id="statusmodalid"
                        data-target="" data-whatever="@mdo" title="Status" onclick="updateStatusDetails()">Bulk
                        Status
                        Change</a>
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
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">ID</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control" name="id"
                                                    id="id" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12">Company</label>
                                            <div class="col-sm-12">
                                                <select name="agency_fk[]" id="agency_fk"
                                                    class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple">
                                                    <?php foreach ($agencyList as $rwAgency) { ?>
                                                    <option value="<?php echo $rwAgency->id; ?>">
                                                        <?php echo $rwAgency->agency_name; ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Full Name</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="full_name" id="full_name" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">First Name</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="first_name" id="first_name" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Last Name</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="last_name" id="last_name" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Mobile</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control" name="mobile"
                                                    id="mobile" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Email</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control" name="email"
                                                    id="email" value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Date Of Birth</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="dob" class="form-control"
                                                    placeholder="Select Date of Birth" id="dob"
                                                    data-inputmask="'alias': 'datetime'"
                                                    data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Status</label>
                                            <div class="col-sm-12">
                                                <select name="status" class="form-control" id="status">
                                                    <option value="">Select Status</option>
                                                    <option value="active">Active</option>
                                                    <option value="deactivated">Deactivated</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @if (Auth()->user()->view_ssn_hub == 1)
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">SSN</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control" name="ssn"
                                                    id="ssn" value="">
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Employee Code</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="employee_code" id="employee_code" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Member ID</label>
                                            <div class="col-sm-12">
                                                <input autocomplete="off" type="text" class="form-control"
                                                    name="member_id" id="member_id" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Dependant</label>
                                            <div class="col-sm-12">
                                                <select name="parent_id" class="form-control" id="parent_id">
                                                    <option value="">Select Dependant</option>
                                                    <option value="all">All</option>
                                                    <option value="parent">Parent</option>
                                                    <option value="dependent">Dependant</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Created Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="created_date" value=""
                                                    class="datepickernn form-control" id="created_date">
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
                                                    <option value="{{ $val->id }}">{{ $val->first_name }}
                                                        {{ $val->last_name }}</option>
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
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment"
                                            id="search-data" value="Search" onclick="hubList(1)">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm"
                                            onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                        @can('hub-record-export')
                                        <a href="javascript:void(0)" class="btn btn-info btn-rounded"
                                            onclick="exportCsv()">Export</a>
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
                <div class="location-wise-data-loader shimmer_id hideClass">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company</th>
                                    <th>Name</th>
                                    <th>Mobile / Phone</th>
                                    <th>DOB</th>
                                    <th>Gender</th>
                                    @if (Auth()->user()->view_ssn_hub == 1)
                                    <th>SSN</th>
                                    @endif
                                    <th>Created Date / Created By</th>
                                    <th>Updated Date / Updated By</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    @if (Auth()->user()->view_ssn_hub == 1)
                                    <td class="text-center"></td>
                                    @endif
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="hub_list_res"></span>
            </div>
        </div>
        @can('hub-import-logs')
        <div class="page-title-main mt-2">
            <h5 class="mb-0 font-weight-bold"></h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" class="btn btn-info btn-sm btn-fw cust-right-btn"
                        onclick="loadImportLogs()"><i class="mdi mdi-history"></i>Show Import Logs</a>
                </div>
            </div>
        </div>
        @endcan

        <!-- Import Logs Section -->
        <div class="row mt-4 import-section hide">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center form-patient-list-box">
                            <h5 class="mb-0">Import History</h5>
                            <div class="row form-row-gap">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="log-file-name"
                                        placeholder="Search by file name">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" id="log-status">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="completed">Completed</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="log-date-range"
                                        placeholder="Date Range">
                                </div>
                                <div class="col-md-3">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment"
                                            id="search-data" value="Search" onclick="searchImportLogs(1)">
                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm"
                                            onclick="refreshImport()"><i class="mdi mdi-reload"></i> Clear</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="import-logs-list" class="table-responsive">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="blank_div" style='margin-top: 100px;'>
    </div>
</div>

@include('include/footer')

@include('hubRecord/modal/hub_import_recordv2')
@include('hubRecord/modal/hub_records_bulk_status_modal')

<script src="<?php echo URL::to('/'); ?>/assets/js/jquery.tokeninput.js"></script>
<script>
    var HUB_RECORD_LIST = "{{ url('hub-record/ajax-list') }}";
    var _DATE_TIME = "{{ date('m/d/Y') }}";
    var HUB_URL = "{{ url('hub-record/view/') }}";
    var _SAVE_HUB_DETAILS = "{{ url('hub-record/save/') }}";
    var _GET_COUNTRY_CODE = "{{ url('get-county') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var HUB_RECORD_CSV = "{{ url('hub-record/csv-list') }}";
    var urlToken = "{{ url('search-nybest-user') }}";
    var IMPORT_DATA = "{{ url('import-hub-record') }}";
    var IMPORT_LOGS_URL = "{{ url('hub-record/import-logs') }}";
    var _HUB_UPDATE_STATUS = "{{ url('update-bulk-hub-status') }}";
    var companyFile ="{{ url('hub_sample1.csv') }}";
    var masterFile ="{{ url('hub_sample.csv') }}";
    // Initialize date range picker for logs
    $(document).ready(function() {
        $('#log-date-range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#log-date-range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                'MM/DD/YYYY'));
        });

        $('#log-date-range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

    function loadImportLogs(page = 1) {
        $('.import-section').removeClass('hide');
        $.ajax({
            url: IMPORT_LOGS_URL,
            type: 'GET',
            data: {
                file_name: $('#log-file-name').val(),
                status: $('#log-status').val(),
                date_range: $('#log-date-range').val(),
                page: page
            },
            success: function(response) {
                displayImportLogs(response);
            }
        });
    }

    function searchImportLogs() {
        loadImportLogs();
    }

    function displayImportLogs(logs) {
        const container = $('#import-logs-list');
        container.empty();
        container.html(logs);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString();
    }
</script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script type="text/javascript"
    src="{{ asset('assets/modulejs/hub_record/hub_record.js') }}?time={{ env('timestamp') }}"></script>
<script src="{{ asset('/assets/vendors/select2/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script>
    $(":input").inputmask(); 
    $("#ssn").keyup(function () {
    var val = this.value.replace(/\D/g, "");
    val = val.replace(/^(\d{3})/, "$1-");
    val = val.replace(/-(\d{2})/, "-$1-");
    val = val.replace(/(\d)-(\d{4}).*/, "$1-$2");
    this.value = val;
    });
</script>