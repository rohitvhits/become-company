@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .page-title-main { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .filter-row .form-control { height:35px; font-size:13px; }

    /* Status badges */
    .badge-pending  { background:#ffc107; color:#212529; }
    .badge-booked   { background:#17a2b8; color:#fff; }
    .badge-called   { background:#28a745; color:#fff; }
    .badge-failed   { background:#dc3545; color:#fff; }

    /* Verified / Converted */
    .badge-verified   { background:#6f42c1; color:#fff; }
    .badge-converted  { background:#007bff; color:#fff; }
    .badge-unverified { background:#e9ecef; color:#495057; }

    /* Highlight new unreviewed entries */
    .row-new td { background:#fffbe6 !important; }
    .row-new-dot {
        display:inline-block; width:8px; height:8px;
        background:#f39c12; border-radius:50%;
        margin-right:5px; vertical-align:middle;
    }

    /* Upcoming row */
    .row-upcoming td { border-left:3px solid #28a745 !important; }

    .action-btn { padding:3px 10px; font-size:12px; }

    .stats-card { border-radius:10px; padding:18px 20px; color:#fff; margin-bottom:15px; }
    .stats-card .num { font-size:28px; font-weight:700; }
    .stats-card .lbl { font-size:12px; opacity:.85; }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold"><i class="mdi mdi-robot text-primary mr-2"></i> AI Call Logs</h5>
        </div>

        <!-- Stats Row -->
        <div class="row mb-3" id="statsRow">
            <div class="col-md-2">
                <div class="stats-card" style="background:linear-gradient(135deg,#f39c12,#e67e22);">
                    <div class="num" id="stat-pending">-</div>
                    <div class="lbl">Pending Review</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card" style="background:linear-gradient(135deg,#28a745,#20c997);">
                    <div class="num" id="stat-called">-</div>
                    <div class="lbl">Called</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card" style="background:linear-gradient(135deg,#6f42c1,#a855f7);">
                    <div class="num" id="stat-verified">-</div>
                    <div class="lbl">Verified</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card" style="background:linear-gradient(135deg,#007bff,#0d6efd);">
                    <div class="num" id="stat-converted">-</div>
                    <div class="lbl">Converted</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card" style="background:linear-gradient(135deg,#17a2b8,#0dcaf0);">
                    <div class="num" id="stat-booked">-</div>
                    <div class="lbl">Booked</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card" style="background:linear-gradient(135deg,#dc3545,#e74c3c);">
                    <div class="num" id="stat-failed">-</div>
                    <div class="lbl">Failed</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filters -->
                <div class="row filter-row mb-2">
                    <div class="col-md-3">
                        <label class="font-weight-bold" style="font-size:12px;">Search</label>
                        <input type="text" class="form-control" id="f_search" placeholder="Name / Mobile">
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold" style="font-size:12px;">Agency</label>
                        <select class="form-control select2-multi" id="f_agency" multiple="multiple">
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold" style="font-size:12px;">Location</label>
                        <select class="form-control select2-multi" id="f_location" multiple="multiple">
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold" style="font-size:12px;">Date Range</label>
                        <input type="text" class="form-control" id="f_date_range" placeholder="Select date range" autocomplete="off" readonly>
                    </div>
                </div>
                <div class="row filter-row mb-3">
                    <div class="col-md-2">
                        <label class="font-weight-bold" style="font-size:12px;">Call Status</label>
                        <select class="form-control" id="f_status">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="called">Called</option>
                            <option value="booked">Booked</option>
                            <option value="self-booked">Self-Booked</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="font-weight-bold" style="font-size:12px;">Verified</label>
                        <select class="form-control" id="f_verified">
                            <option value="">All</option>
                            <option value="0">Not Verified</option>
                            <option value="1">Verified</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="font-weight-bold" style="font-size:12px;">Converted</label>
                        <select class="form-control" id="f_converted">
                            <option value="">All</option>
                            <option value="0">Not Converted</option>
                            <option value="1">Converted</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm mr-2" onclick="loadList()"><i class="fa fa-search"></i> Search</button>
                        <button class="btn btn-secondary btn-sm" onclick="resetFilters()"><i class="fa fa-refresh"></i> Reset</button>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mb-2" style="font-size:12px; color:#666;">
                    <span class="row-new-dot"></span> New / Unreviewed &nbsp;&nbsp;
                    <span style="display:inline-block;width:3px;height:14px;background:#28a745;vertical-align:middle;margin-right:4px;"></span> Upcoming
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm" style="font-size:13px;">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Mobile</th>
                                <th>Agency</th>
                                <th>Call Status</th>
                                <th>Called At</th>
                                <th>Created Date</th>
                                <th>Location</th>
                                <th>Verified</th>
                                <th>Converted</th>
                                <th>SMS</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="listBody">
                            <tr><td colspan="10" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="pagination"></div>
            </div>
        </div>
    </div>
</div>

@include('include/footer')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/daterangepicker.css') }}">
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Select2 multiselect for Agency & Location
    $('.select2-multi').select2({
        placeholder: 'All',
        allowClear: true,
        width: '100%',
    });

    // Single daterangepicker
    $('#f_date_range').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear', format: 'MM/DD/YYYY' }
    });
    $('#f_date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });
    $('#f_date_range').on('cancel.daterangepicker', function() {
        $(this).val('');
    });

    loadList();
    loadStats();
});

function loadList(page) {
    page = page || 1;
    var dateRange = $('#f_date_range').val();
    var dateFrom  = '';
    var dateTo    = '';
    if (dateRange) {
        var parts = dateRange.split(' - ');
        dateFrom  = parts[0] || '';
        dateTo    = parts[1] || '';
    }

    $.ajax({
        url: "{{ url('ai-call-logs/ajax-list') }}",
        data: {
            search:      $('#f_search').val(),
            agency_id:   $('#f_agency').val(),
            location_id: $('#f_location').val(),
            call_status: $('#f_status').val(),
            verified:    $('#f_verified').val(),
            converted:   $('#f_converted').val(),
            date_from:   dateFrom,
            date_to:     dateTo,
            page: page
        },
        success: function(res) {
            $('#listBody').html(res);
        },
        error: function() {
            $('#listBody').html('<tr><td colspan="12" class="text-center text-danger">Failed to load</td></tr>');
        }
    });
}

function loadStats() {
    $.ajax({
        url: "{{ url('ai-call-logs/ajax-list') }}",
        data: { stats_only: 1 },
        success: function(res) {
            if (res && res.stats) {
                $('#stat-pending').text(res.stats.pending || 0);
                $('#stat-called').text(res.stats.called || 0);
                $('#stat-verified').text(res.stats.verified || 0);
                $('#stat-converted').text(res.stats.converted || 0);
                $('#stat-booked').text(res.stats.booked || 0);
                $('#stat-failed').text(res.stats.failed || 0);
            }
        }
    });
}

function resetFilters() {
    $('#f_search,#f_date_range').val('');
    $('#f_status,#f_verified,#f_converted').val('');
    $('#f_agency,#f_location').val(null).trigger('change');
    loadList();
}

$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadList(page);
});
</script>
