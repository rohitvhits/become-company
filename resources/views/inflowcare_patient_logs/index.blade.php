@include('include/header')
@include('include/sidebar')
<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    </style>
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">

<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .modal-payload-content {
        max-height: 400px;
        overflow-y: auto;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        font-size: 13px;
    }
    .badge-success { background-color: #28a745; }
    .badge-danger { background-color: #dc3545; }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .badge-info { background-color: #17a2b8; }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Inflowcare Patient Logs (<span id="total_record_id">0</span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        <div class="row">
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
                                                    <label for="">Agency Name</label>
                                                    <select name="agency_id[]" id="agency_id"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" data-placeholder="Select Agency Name">
                                                            @foreach($agencyList as $rwAgency)
                                                            <option value="{{ $rwAgency['id']}}">
                                                            {{ $rwAgency['agency_name']}}</option>
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
                                                    <label for="">Created Date</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="created_date_range" placeholder="Select Created Date">
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

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="clearFilters()">
                                            <i class="mdi mdi-reload"></i> Clear
                                        </a>
                                        @can('inflowcare-patient-log-report-export')
                                        <a href="javascript:void(0)" class="btn btn-info cust-right-btn" onclick="exportCsv()">
                                            <i class="mdi mdi-file"></i><span id="exportText">Export CSV</span>
                                            <span class="spinner-border spinner-border-sm d-none" id="exportLoader" role="status" aria-hidden="true"></span>
                                        </a>
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
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Agency Name</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="9"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="ajax_response_data"></span>
            </div>
        </div>
    </div>
</div>

<!-- Payload Modal -->
<div class="modal fade" id="payloadModal" tabindex="-1" aria-labelledby="payloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payloadModalLabel">Request & Response Payload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><strong>Request Payload:</strong></h6>
                <div class="modal-payload-content">
                    <pre id="requestPayloadContent"></pre>
                </div>
                <hr>
                <h6><strong>Response Payload:</strong></h6>
                <div class="modal-payload-content">
                    <pre id="responsePayloadContent"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('include/footer')

<script>
    var _LOAD_AJAX_URL = "{{ url('inflowcare-patient-logs-ajax') }}";
    var _EXPORT_CSV_URL = "{{ url('inflowcare-patient-logs-export-csv') }}";
    var _SEARCH_CREATED_BY_USER = "{{ url('search-nybest-all-user') }}";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
    var _currentPage = 1;
</script>

<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>

<script>
    $(document).ready(function () {
        // Filter toggle
        $('#filter-btn').on('click', function () {
            $('#search-filter-btn').slideToggle();
        });

        // Select2 for agency
        $('.js-example-basic-multiple').select2({
            placeholder: "Select Agency Name",
            allowClear: true
        });

        // Daterangepicker for created date
        $('#created_date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'MM/DD/YYYY'
            }
        });

        $('#created_date_range').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        $('#created_date_range').on('cancel.daterangepicker', function () {
            $(this).val('');
        });

        // Token input for created by
        $('#created_by_search').tokenInput(_SEARCH_CREATED_BY_USER, {
            theme: "facebook",
            tokenLimit: 1,
            hintText: "Search Created By",
            noResultsText: "No results",
            searchingText: "Searching...",
        });

        // Load initial data
        loadAjaxList();
    });

    function loadAjaxList(page) {
        _currentPage = page || 1;
        var agencyId = $('#agency_id').val() || [];
       
        var fromDate = '';
        var toDate = '';
        var dateRange = $('#created_date_range').val();
        if (dateRange) {
            var dates = dateRange.split(' - ');
            fromDate = dates[0];
            toDate = dates[1];
        }

        $('.shimmer_id').show();
        $('#ajax_response_data').html('');

        $.ajax({
            url: _LOAD_AJAX_URL,
            type: 'GET',
            data: {
                page: _currentPage,
                agency_id: agencyId,
                from_date: fromDate,
                to_date: toDate
            },
            success: function (response) {
                $('.shimmer_id').hide();
                $('#ajax_response_data').html(response);
            },
            error: function () {
                $('.shimmer_id').hide();
                $('#ajax_response_data').html('<div class="text-center p-3">Something went wrong. Please try again.</div>');
            }
        });
    }

    function exportCsv() {
        $('#exportLoader').removeClass('d-none');
        var agencyId = $('#agency_id').val() || [];
        var fromDate = '';
        var toDate = '';
        var dateRange = $('#created_date_range').val();
        if (dateRange) {
            var dates = dateRange.split(' - ');
            fromDate = dates[0];
            toDate = dates[1];
        }
        var url = _EXPORT_CSV_URL + "?agency_id=" + agencyId.join(',') + "&from_date=" + fromDate + "&to_date=" + toDate;
        window.location.href = url;
        setTimeout(function() {
            $('#exportLoader').addClass('d-none');
        }, 2000);
    }

    function clearFilters() {
        $('#agency_id').val(null).trigger('change');
        $('#created_date_range').val('');
        $('#created_by_search').tokenInput("clear");
        loadAjaxList();
    }

    // View payload modal
    $(document).on('click', '.view-payload-btn', function () {
        var requestPayload = $(this).attr('data-request');
        var responsePayload = $(this).attr('data-response');

        try {
            var parsedRequest = JSON.parse(requestPayload);
            if (typeof parsedRequest === 'string') {
                parsedRequest = JSON.parse(parsedRequest);
            }
            $('#requestPayloadContent').text(JSON.stringify(parsedRequest, null, 4));
        } catch (e) {
            $('#requestPayloadContent').text(requestPayload || 'N/A');
        }

        try {
            var parsedResponse = JSON.parse(responsePayload);
            if (typeof parsedResponse === 'string') {
                parsedResponse = JSON.parse(parsedResponse);
            }
            $('#responsePayloadContent').text(JSON.stringify(parsedResponse, null, 4));
        } catch (e) {
            $('#responsePayloadContent').text(responsePayload || 'N/A');
        }

        $('#payloadModal').modal('show');
    });

    // Pagination click handler
    $(document).on('click', '#ajax_response_data .pagination a', function (e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        loadAjaxList(page);
    });
</script>
