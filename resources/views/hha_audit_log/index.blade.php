@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .modal-payload-content {
        max-height: 400px;
        overflow-y: auto;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        font-size: 13px;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">HHA Audit Logs (<span id="total_record_id">0</span>)</h5>
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
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Patient ID</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="filter_patient_id" placeholder="Enter Patient ID">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>HHA Patient ID</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="filter_hha_patient_id" placeholder="Enter HHA Patient ID">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Type</label>
                                                    <select class="form-control" id="type" name="type">
                                                        <option value="">Select Type</option>
                                                        <option type="poc">POC</option>
                                                        <option type="supervision">SuperVision</option>
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
                                    <th nowrap>Patient ID</th>
                                    <th nowrap>Patient Full Name</th>
                                    <th nowrap>Visit ID</th>
                                    <th nowrap>HHA Patient ID</th>
                                    <th nowrap>Type</th>
                                    <th nowrap>Created Date</th>
                                    <th nowrap>Created By</th>
                                    <th nowrap>Action</th>
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

<!-- Detail Modal -->
<div class="modal fade" id="pocLogDetailModal" tabindex="-1" aria-labelledby="pocLogDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pocLogDetailModalLabel">POC Log Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalLoader" class="text-center" style="display:none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="modalContent" style="display:none;">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>Send Response</strong></h6>
                            <div class="modal-payload-content">
                                <pre id="sendResponseContent"></pre>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Return Response</strong></h6>
                            <div class="modal-payload-content">
                                <pre id="returnResponseContent"></pre>
                            </div>
                        </div>
                    </div>
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
    var _LOAD_AJAX_URL = "{{ url('hha-audit-log-ajax') }}";
    var _SHOW_DETAIL_URL = "{{ url('hha-audit-log-detail') }}";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
    var _currentPage = 1;
</script>

<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $('#filter-btn').on('click', function () {
            $('#search-filter-btn').slideToggle();
        });

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

        loadAjaxList();
    });

    function loadAjaxList(page) {
        _currentPage = page || 1;

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
                patient_id: $('#filter_patient_id').val(),
                hha_patient_id: $('#filter_hha_patient_id').val(),
                from_date: fromDate,
                type:$('#type').val(),
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

    function viewPocLogDetail(id) {
        $('#modalLoader').show();
        $('#modalContent').hide();
        $('#sendResponseContent').text('');
        $('#returnResponseContent').text('');
        $('#pocLogDetailModal').modal('show');

        $.ajax({
            url: _SHOW_DETAIL_URL + '/' + id,
            type: 'GET',
            success: function (response) {
                $('#modalLoader').hide();
                $('#modalContent').show();

                var sendData = response.data.send_response;
                var returnData = response.data.return_response;

                if (typeof sendData === 'object' && sendData !== null) {
                    $('#sendResponseContent').text(JSON.stringify(sendData, null, 4));
                } else {
                    $('#sendResponseContent').text(sendData || 'N/A');
                }

                if (typeof returnData === 'object' && returnData !== null) {
                    $('#returnResponseContent').text(JSON.stringify(returnData, null, 4));
                } else {
                    $('#returnResponseContent').text(returnData || 'N/A');
                }
            },
            error: function () {
                $('#modalLoader').hide();
                $('#modalContent').show();
                $('#sendResponseContent').text('Error loading data.');
                $('#returnResponseContent').text('Error loading data.');
            }
        });
    }

    function clearFilters() {
        $('#filter_patient_id').val('');
        $('#filter_hha_patient_id').val('');
        $('#created_date_range').val('');
        loadAjaxList();
    }

    $(document).on('click', '#ajax_response_data .pagination a', function (e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        loadAjaxList(page);
    });
</script>
