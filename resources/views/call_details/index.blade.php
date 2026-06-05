@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('/css/daterangepicker.css') }}">
<script src="{{ asset('/assets/js/moment.min.js') }}"></script>
<script src="{{ asset('/assets/js/daterangepicker.min.js') }}"></script>

<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>

@php
    $hasActiveFilter = !empty(request('phone')) || request('start_date') || request('end_date')
                    || request('type') !== null || request('extension') || request('caller_name');
    $ajaxTableUrl = $patient
        ? route('patient.call-details.list', $patient->id)
        : route('call-details.list');
@endphp

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        {{-- Page Title --}}
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">
                Call Details
                @if($patient)
                    &mdash; {{ trim($patient->first_name . ' ' . $patient->last_name) }}
                    <small class="text-muted">({{ $patient->mobile ?: '-' }})</small>
                @endif
                (<span id="total_count">0</span>)
            </h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @if($patient)
                    <a href="{{ url('/patient/view/' . $patient->id) }}" class="btn btn-light cust-right-btn btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                    @endif
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        {{-- Filter Panel --}}
        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: {{ $hasActiveFilter ? 'block' : 'none' }};">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <input type="hidden" name="start_date" id="start_date" value="{{ $filters['start_date'] }}">
                                <input type="hidden" name="end_date" id="end_date" value="{{ $filters['end_date'] }}">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row"><div class="col-sm-12">
                                                <label>Date Range</label>
                                                <input type="text" id="date_range_picker" class="form-control" placeholder="mm-dd-yyyy to mm-dd-yyyy" autocomplete="off" readonly>
                                            </div></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row"><div class="col-sm-12">
                                                <label>Type</label>
                                                <select name="type" class="form-control">
                                                    <option value="">All</option>
                                                    <option value="1" {{ $filters['type'] === '1' ? 'selected' : '' }}>Inbound</option>
                                                    <option value="0" {{ $filters['type'] === '0' ? 'selected' : '' }}>Outbound</option>
                                                    <option value="2" {{ $filters['type'] === '2' ? 'selected' : '' }}>Missed</option>
                                                </select>
                                            </div></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row"><div class="col-sm-12">
                                                <label>Caller Name</label>
                                                <input type="text" name="caller_name" class="form-control" value="{{ $filters['caller_name'] }}" placeholder="e.g. John">
                                            </div></div>
                                        </div>
                                    </div>
                                    
                                    @if(!$patient)
                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row"><div class="col-sm-12">
                                                <label>Caller/Dialed Number</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $filters['phone'] }}" placeholder="e.g. 3475138569">
                                            </div></div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    
                                    
                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row"><div class="col-sm-12">
                                                <label>Extension</label>
                                                <input type="text" name="extension" class="form-control" value="{{ $filters['extension'] }}" placeholder="e.g. 552">
                                            </div></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap mt-3">
                                    <div class="col-md-9">
                                        <div class="appointment-btn-box" style="justify-content:left !important">
                                            <button type="submit" class="btn search-btn1" style="background-color:#00879E;color:#fff;border-color:#00879E;">Search</button>
                                            <button type="button" id="reset-btn" class="btn btn-light cust-right-btn">
                                                <i class="mdi mdi-reload"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="row">
            <div class="col-12" id="call-table-wrapper">
                <div class="text-center py-5 text-muted">
                    <i class="mdi mdi-loading mdi-spin"></i> Loading...
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    var ajaxTableUrl   = "{{ $ajaxTableUrl }}";
    var recordingUrl   = "{{ url('call-details/recording') }}";
    var defaultFilters = {
        start_date: "{{ $filters['start_date'] ?? date('Y-m-d', strtotime('yesterday')) }}",
        end_date:   "{{ $filters['end_date'] ?? date('Y-m-d') }}",
        phone:      "{{ $filters['phone'] }}"
    };

    function loadCallDetails(page) {
        page = page || 1;
        var params = $('#search-form').serialize();

        $('#call-table-wrapper').html(
            '<div class="text-center py-5 text-muted"><i class="mdi mdi-loading mdi-spin"></i> Loading...</div>'
        );

        $.ajax({
            url: ajaxTableUrl + '?page=' + page,
            type: 'GET',
            data: params,
            success: function (response) {
                $('#call-table-wrapper').html(response);
                var total = $('#call-total-count').val();
                if (total !== undefined) {
                    $('#total_count').text(total);
                }
            },
            error: function () {
                $('#call-table-wrapper').html(
                    '<div class="alert alert-danger">Failed to load call details. Please try again.</div>'
                );
            }
        });
    }

    $('body').on('click', '.pagination a', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var page = href ? (href.split('page=')[1] || 1) : 1;
        loadCallDetails(page);
    });

    $('#search-form').on('submit', function (e) {
        e.preventDefault();
        loadCallDetails(1);
    });

    var defaultStart = moment(defaultFilters.start_date, 'YYYY-MM-DD');
    var defaultEnd   = moment(defaultFilters.end_date,   'YYYY-MM-DD');

    function syncDateRange(start, end) {
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
        $('#date_range_picker').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    }

    $('#date_range_picker').daterangepicker({
        startDate: defaultStart,
        endDate:   defaultEnd,
        autoUpdateInput: false,
        autoApply: true,
        startOfWeek: 'sunday',
        locale: { format: 'MM/DD/YYYY' },
        ranges: {
            'Today':        [moment(), moment()],
            'Yesterday':    [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days':  [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month':   [moment().startOf('month'), moment().endOf('month')],
            'Last Month':   [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Next Month':   [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
            'Next Week':    [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
            'Last Week':    [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')]
        }
    });

    $('#date_range_picker').on('apply.daterangepicker', function(ev, picker) {
        syncDateRange(picker.startDate, picker.endDate);
        // loadCallDetails(1);
    });

    // Set initial display and hidden inputs
    syncDateRange(defaultStart, defaultEnd);

    $('#reset-btn').on('click', function () {
        $('select[name="type"]').val('');
        $('input[name="caller_name"]').val('');
        $('input[name="phone"]').val('');
        $('input[name="extension"]').val('');
        var resetStart = moment().subtract(1, 'days').startOf('day');
        var resetEnd   = moment().endOf('day');

        $('#date_range_picker').val(resetStart+'-'+resetEnd)
       
        syncDateRange(resetStart, resetEnd);
        loadCallDetails(1);
    });

    $("#filter-btn").on('click', function () {
        $("#search-filter-btn").slideToggle(600);
    });

    $('body').on('click', '.btn-recording', function () {
        var btn       = $(this);
        var cdrId     = btn.data('cdrid');
        var timeStart = btn.data('timestart');
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');

        $.ajax({
            url:      recordingUrl,
            type:     'GET',
            data:     { cdr_id: cdrId, time_start: timeStart },
            success:  function (res) {
                window.open(res.url, '_blank');
            },
            error: function (jqXHR) {
                showErrorAndLoginRedirection(jqXHR);
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="mdi mdi-play-circle-outline"></i>');
            }
        });
    });

    $(document).ready(function () {
        loadCallDetails(1);
    });
</script>

@include('include/footer')
