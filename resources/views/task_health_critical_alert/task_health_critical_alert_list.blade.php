@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/css/global.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/daterangepicker.css" />
<style>
    .page-title-main { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .label { display:inline; padding:.2em .6em .3em; font-size:100%; font-weight:700; line-height:1; color:#fff; text-align:center; white-space:nowrap; vertical-align:baseline; border-radius:.25em; }
    .label-success { background-color:#5cb85c; }
    .label-danger  { background-color:#d9534f; }
    .label-warning { background-color:#f0ad4e; }
    .label-info    { background-color:#5bc0de; }
    .label-default { background-color:#777; }

    /* Stats strip */
    .ca-stats-strip { display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
    .ca-stat-card {
        background:#fff; border:1px solid #e9ecef; border-radius:8px;
        padding:10px 16px; display:flex; flex-direction:column; gap:2px;
        min-width:120px; box-shadow:0 1px 3px rgba(0,0,0,.04);
    }
    .ca-stat-lbl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#9ca3af; }
    .ca-stat-val { font-size:18px; font-weight:800; color:#1f2937; line-height:1.2; }
    .ca-stat-val.red   { color:#dc3545; }
    .ca-stat-val.green { color:#28a745; }
    .ca-stat-val.teal  { color:#17a2b8; }

    /* Resolve / Detail Modals */
    .ca-modal-header-green { background:linear-gradient(135deg,#1a7a4a,#28a745); }
    .ca-modal-header-red   { background:linear-gradient(135deg,#c0392b,#e74c3c); }
    .ca-modal-header-grey  { background:linear-gradient(135deg,#495057,#6c757d); }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        {{-- Page Title --}}
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">
                <i class="mdi mdi-alert-circle mr-1" style="color:#dc3545;"></i> Task Health Critical Alerts
            </h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('task-health-critical-alerts-export')
                    <a href="javascript:void(0)" onclick="exportCriticalAlertsCsv()" class="btn cust-right-btn" style="background-color:#28a745;color:#fff;margin-right:6px;">
                        <i class="fa fa-download"></i> Export CSV
                    </a>
                    @endcan
                    <a href="javascript:void(0)" id="ca-filter-btn" class="btn cust-right-btn" style="background-color:#00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i> Filter
                        <span id="ca-active-filter-dot" class="active-filter" style="display:none;"></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        {{-- Filter Section --}}
        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display:none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="ca-search-form">
                                <div class="row form-row-gap">

                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency</label>
                                                    <select class="form-control" id="ca_agency_id">
                                                        <option value="">All Agencies</option>
                                                        @foreach($agencyList as $agency)
                                                            <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>From Date</label>
                                                    <input type="text" class="form-control datepicker-single" id="ca_from_date" placeholder="MM/DD/YYYY" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>To Date</label>
                                                    <input type="text" class="form-control datepicker-single" id="ca_to_date" placeholder="MM/DD/YYYY" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Task ID</label>
                                                    <input type="text" class="form-control" id="ca_task_id" placeholder="Enter Task ID" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Patient ID</label>
                                                    <input type="text" class="form-control" id="ca_patient_id" placeholder="Enter Patient ID" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Alert Status</label>
                                                    <select class="form-control" id="ca_alert_status">
                                                        <option value="">All Statuses</option>
                                                        <option value="active">Critical (Active)</option>
                                                        <option value="clear">Clear</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Resolved Status</label>
                                                    <select class="form-control" id="ca_resolved_status">
                                                        <option value="">All</option>
                                                        <option value="resolved">Resolved</option>
                                                        <option value="unresolved">Unresolved</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-12">
                                    <div class="appointment-btn-box" style="justify-content:left !important;">
                                        <input type="button" class="btn search-btn1 searchAppoinment" value="Search" onclick="loadCaList()">
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="resetCaFilters()">
                                            <i class="mdi mdi-reload"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats strip --}}
        <div class="ca-stats-strip" id="ca-stats-strip" style="display:none;">
            <div class="ca-stat-card">
                <div class="ca-stat-lbl">Total</div>
                <div class="ca-stat-val" id="ca-stat-total">—</div>
            </div>
            <div class="ca-stat-card">
                <div class="ca-stat-lbl">Critical</div>
                <div class="ca-stat-val red" id="ca-stat-critical">—</div>
            </div>
            <div class="ca-stat-card">
                <div class="ca-stat-lbl">Resolved</div>
                <div class="ca-stat-val teal" id="ca-stat-resolved">—</div>
            </div>
        </div>

        {{-- Results --}}
        <div class="row">
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Task ID</th>
                                    <th>Patient ID</th>
                                    <th>Agency Name</th>
                                    <th>Alert Status</th>
                                    <th>Summary</th>
                                    <th>Findings</th>
                                    <th>Created At</th>
                                    <th>Resolved At<br/>Resolved By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr><td colspan="11"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="response_requested_id"></span>
            </div>
        </div>

    </div>
    <div id="blank_div" class="mt-5">&nbsp;</div>
</div>

@include('include/footer')
@include('task_health_critical_alert._partial.resolve_modal')
@include('task_health_critical_alert._partial.details_modal')
@include('task_health_critical_alert._partial.visit_detail_modal')


{{-- Visit detail drawer styles (full set from visit list page) --}}
<style>
    /* ── Overlay backdrop ── */
    .vd-overlay { display:none; position:fixed; z-index:1055; inset:0; background:rgba(0,0,0,.45); }
    .vd-overlay.show { display:flex; justify-content:flex-end; }

    /* ── Drawer panel ── */
    .vd-drawer { width:700px; max-width:100vw; height:100vh; background:#fff; display:flex; flex-direction:column; box-shadow:-6px 0 32px rgba(0,0,0,.18); transform:translateX(100%); transition:transform .3s cubic-bezier(.25,.8,.25,1); }
    .vd-overlay.show .vd-drawer { transform:translateX(0); }

    /* ── Header ── */
    .vd-header { padding:14px 18px; border-bottom:1px solid #e9ecef; display:flex; align-items:center; gap:10px; background:#fff; flex-shrink:0; flex-wrap:wrap; }
    .vd-header-left { display:flex; align-items:center; gap:10px; flex:1; flex-wrap:wrap; min-width:0; }
    .vd-avatar { width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; font-size:15px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; letter-spacing:1px; }
    .vd-header-info { display:flex; flex-direction:column; gap:1px; min-width:0; }
    .vd-header-info h4 { margin:0; font-size:15px; font-weight:700; color:#1a1a2e; line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .vd-task-id { font-size:12px; color:#6c757d; font-weight:500; }
    .vd-badge-status { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; color:#fff; white-space:nowrap; }
    .vd-badge-status.info    { background:#17a2b8; }
    .vd-badge-status.success { background:#28a745; }
    .vd-badge-status.danger  { background:#dc3545; }
    .vd-badge-status.warning { background:#e0a800; color:#333; }
    .vd-badge-type { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; background:#e8f0fe; color:#1a73e8; border:1px solid #c5d9f9; white-space:nowrap; }
    .vd-header-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
    .vd-action-btn { width:32px; height:32px; border-radius:6px; border:1px solid #dee2e6; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:15px; transition:all .15s; padding:0; }
    .vd-action-danger { color:#dc3545; border-color:#f5c6cb; }
    .vd-action-danger:hover { background:#dc3545; color:#fff; border-color:#dc3545; }
    .vd-action-close { color:#6c757d; }
    .vd-action-close:hover { background:#f8f9fa; color:#343a40; }

    /* ── Horizontal tabs ── */
    .vd-tabs { display:flex; gap:2px; padding:0 18px; border-bottom:2px solid #e9ecef; background:#fff; flex-shrink:0; }
    .vd-tab { padding:10px 20px; border:none; background:none; font-size:13px; font-weight:500; color:#6c757d; cursor:pointer; border-bottom:3px solid transparent; margin-bottom:-2px; transition:all .15s; }
    .vd-tab:hover { color:#007bff; }
    .vd-tab.active { color:#007bff; font-weight:600; border-bottom-color:#007bff; }

    /* ── Body ── */
    .vd-body { flex:1; overflow-y:auto; background:#f7f8fc; }
    .vd-panel { display:none; }
    .vd-panel.active { display:block; animation:vdFade .2s; }
    @keyframes vdFade { from{opacity:0} to{opacity:1} }

    /* ── Sections ── */
    .vd-section { background:#fff; border-radius:8px; margin:14px 14px 0; border:1px solid #e9ecef; padding:14px 16px; }
    .vd-section:last-child { margin-bottom:14px; }
    .vd-section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #f0f2f5; display:flex; align-items:center; gap:7px; }
    .vd-section-title::before { content:''; width:3px; height:13px; background:#007bff; border-radius:2px; flex-shrink:0; }

    /* ── 3-column grid ── */
    .vd-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .vd-cell-label { font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.3px; margin-bottom:3px; }
    .vd-cell-value { font-size:13px; font-weight:600; color:#1f2937; word-break:break-word; }

    /* ── Patient contact strip ── */
    .vd-patient-contact { margin-top:12px; padding:10px 13px; background:#f8f9fa; border-radius:6px; display:flex; align-items:center; gap:20px; flex-wrap:wrap; border:1px solid #e9ecef; }
    .vd-contact-name, .vd-contact-phone { font-size:13px; color:#495057; }
    .vd-contact-name i, .vd-contact-phone i { color:#007bff; margin-right:4px; }

    /* ── Data table ── */
    .vd-table { width:100%; border-collapse:collapse; font-size:12.5px; }
    .vd-table th { background:#f8f9fa; color:#6c757d; font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.3px; padding:8px 10px; border-bottom:2px solid #dee2e6; text-align:left; }
    .vd-table td { padding:8px 10px; border-bottom:1px solid #f0f2f5; color:#1f2937; vertical-align:middle; }
    .vd-table tr:hover td { background:#fafbff; }
    .vd-table tr:last-child td { border-bottom:none; }

    /* ── Small action buttons ── */
    .vd-btn-sm { display:inline-flex; align-items:center; gap:3px; padding:3px 9px; border-radius:4px; font-size:11px; font-weight:500; cursor:pointer; border:none; text-decoration:none; margin-right:2px; }
    .vd-btn-info    { background:#17a2b8; color:#fff; }
    .vd-btn-success { background:#28a745; color:#fff; }
    .vd-btn-warning { background:#e0a800; color:#fff; }
    .vd-btn-info:hover    { background:#138496; color:#fff; }
    .vd-btn-success:hover { background:#218838; color:#fff; }
    .vd-btn-warning:hover { background:#c69500; color:#fff; }

    /* ── Shimmer loader ── */
    .shimmer-wrapper { padding:20px; }
    .shimmer-card { background:#fff; border-radius:8px; padding:20px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,.1); }
    .shimmer { background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:200% 100%; animation:shimmerAnim 1.5s infinite; border-radius:4px; }
    @keyframes shimmerAnim { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    .shimmer-line { height:15px; margin-bottom:11px; }
    .shimmer-line.title  { height:22px; width:40%; margin-bottom:18px; }
    .shimmer-line.short  { width:55%; }
    .shimmer-line.medium { width:75%; }
    .shimmer-line.long   { width:100%; }
    .shimmer-header { height:58px; margin-bottom:20px; border-radius:8px; }

    /* ── Responsive ── */
    @media (max-width:768px) { .vd-drawer { width:100vw; } .vd-grid-3 { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:480px) { .vd-grid-3 { grid-template-columns:1fr; } }

    /* ── Patient Record Banner (in General tab) ── */
    .vd-pr-banner { margin:14px 14px 0; border-radius:8px; border:1px solid #e9ecef; overflow:hidden; background:#fff; }
    .vd-pr-banner-header { display:flex; align-items:center; gap:8px; padding:9px 14px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; border-bottom:1px solid #f0f2f5; background:#f8f9fa; }
    .vd-pr-banner-header::before { content:''; width:3px; height:13px; background:#6f42c1; border-radius:2px; flex-shrink:0; }
    .vd-pr-banner-body { padding:12px 14px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .vd-pr-banner-found  { border-left:4px solid #28a745; }
    .vd-pr-banner-missing { border-left:4px solid #f0ad4e; }
    .vd-pr-banner-loading { border-left:4px solid #dee2e6; }
    .vd-pr-info { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .vd-pr-icon { font-size:22px; flex-shrink:0; }
    .vd-pr-text-block { line-height:1.4; }
    .vd-pr-status-label { font-size:12px; font-weight:700; }
    .vd-pr-meta { font-size:11px; color:#6c757d; margin-top:2px; }
    .vd-pr-actions { display:flex; gap:6px; flex-shrink:0; }
    .vd-pr-btn { display:inline-flex; align-items:center; gap:4px; padding:5px 13px; border-radius:5px; font-size:12px; font-weight:600; cursor:pointer; border:none; text-decoration:none; white-space:nowrap; transition:all .15s; }
    .vd-pr-btn-green  { background:#28a745; color:#fff; }
    .vd-pr-btn-green:hover  { background:#218838; color:#fff; text-decoration:none; }
    .vd-pr-btn-orange { background:#f0ad4e; color:#fff; }
    .vd-pr-btn-orange:hover { background:#e09a2e; color:#fff; }
    .vd-pr-btn-outline { background:#fff; color:#6c757d; border:1px solid #dee2e6; }
    .vd-pr-btn-outline:hover { background:#f8f9fa; }
    .vd-pr-create-form { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:8px; padding-top:8px; border-top:1px dashed #dee2e6; width:100%; }
    .vd-pr-create-form select { font-size:12px; height:30px; padding:0 8px; border-radius:4px; border:1px solid #dee2e6; min-width:180px; }
    .vd-pr-create-form .vd-pr-btn { padding:4px 12px; }

    /* ── Patient Record full panel ── */
    .vd-pr-card { background:#fff; border-radius:8px; border:1px solid #e9ecef; margin:14px 14px 0; padding:20px; }
    .vd-pr-card:last-child { margin-bottom:14px; }
    .vd-pr-card-title { font-size:13px; font-weight:700; color:#1a1a2e; margin-bottom:14px; padding-bottom:10px; border-bottom:2px solid #f0f2f5; display:flex; align-items:center; gap:8px; }
    .vd-pr-detail-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
    .vd-pr-detail-label { font-size:10.5px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
    .vd-pr-detail-value { font-size:13px; font-weight:600; color:#1f2937; }
    @media (max-width:480px) { .vd-pr-detail-grid { grid-template-columns:1fr; } }

    /* ── ID Summary Strip ── */
    .vd-id-strip { display:flex; margin:14px 14px 0; border-radius:8px; border:1px solid #e9ecef; overflow:hidden; background:#fff; }
    .vd-id-pill { flex:1; padding:11px 14px; border-right:1px solid #e9ecef; display:flex; flex-direction:column; gap:3px; min-width:0; }
    .vd-id-pill:last-child { border-right:none; }
    .vd-id-pill-label { font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; display:flex; align-items:center; gap:4px; }
    .vd-id-pill-value { font-size:15px; font-weight:700; color:#1f2937; font-family:monospace; letter-spacing:-.3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .vd-id-pill-value a { color:#007bff; text-decoration:none; }
    .vd-id-pill-value a:hover { text-decoration:underline; }
    .vd-id-pill.vd-id-linked { background:#f0fdf4; }
    .vd-id-pill.vd-id-linked .vd-id-pill-label { color:#15803d; }
    .vd-id-pill.vd-id-linked .vd-id-pill-value { color:#16a34a; }
    .vd-id-pill.vd-id-loading .vd-id-pill-value { color:#d1d5db; font-size:12px; font-family:inherit; }
    @media (max-width:480px) { .vd-id-strip { flex-direction:column; } .vd-id-pill { border-right:none; border-bottom:1px solid #e9ecef; } .vd-id-pill:last-child { border-bottom:none; } }

    /* ── TH Patient ID badge in drawer header ── */
    .vd-th-patient-badge { display:none; font-size:11px; font-weight:600; color:#6f42c1; background:#f3e8ff; border:1px solid #d8b4fe; border-radius:4px; padding:1px 7px; letter-spacing:.2px; white-space:nowrap; }
</style>



@include('_partial.task_health_flags.modal')
@include('_partial.task_health_visit.link_patient_modal')

<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script>
var _CA_AJAX_URL       = "{{ url('task-health/critical-alerts-ajax-list') }}";
var _CA_RESOLVE_URL    = "{{ url('task-health/critical-alerts') }}";
var _CA_EXPORT_URL     = "{{ url('task-health/critical-alerts-export-csv') }}";

{{-- Variables required by task_health_visit.js drawer functions --}}
var _TH_CAN_LINK_MASTER       = {{ auth()->user()->can('create-link-task-health') ? 'true' : 'false' }};
var _TH_AGENCIES_URL          = "{{ url('task-health/visit-agencies') }}";
var _TH_VISIT_DETAIL_URL      = "{{ url('task-health/visit-detail') }}";
var _TH_VISIT_DETAIL_JSON_URL = "{{ url('task-health/visit-detail-json') }}";
var _TH_CHECK_MASTER_URL      = "{{ url('task-health/visit-check-master') }}";
var _TH_CREATE_MASTER_URL     = "{{ url('task-health/visit-create-master') }}";
var _TH_LOCAL_AGENCIES        = @json($localAgencies->map(fn($a) => ['id' => $a->id, 'name' => $a->agency_name]));
var _TH_PATIENT_SERVICES      = @json($patientServices->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));
var _TH_VISIT_CREATE_URL      = "{{ url('task-health/visit-create') }}";
var _TH_VISIT_EDIT_URL        = "{{ url('task-health/visit-edit') }}";
var _TH_DOC_APPROVE_URL       = "{{ url('task-health/visit-doc-approve') }}";
var _TH_DOC_OPEN_CHANGES_URL  = "{{ url('task-health/visit-doc-open-changes') }}";
var _TH_DELETE_URL            = "{{ url('/task-health/visit-cancel') }}";
var _TH_FLAGS_BY_TASK_URL     = "{{ url('task-health-flags-by-task-id') }}";
var _TASK_HEALTH_FLAG_UPDATE  = '{{ url("/task-health-flag-update") }}';
var _TASK_HEALTH_FLAGS_SAVE   = '{{ url("/task-health-flags-save") }}';
var _TH_HHA_PREVIEW_BY_TASK   = "{{ url('task-health/by-task') }}";
var _TH_UPLOAD_DOC_BY_TASK    = "{{ url('task-health/by-task') }}";
var _CSRF_TOKEN               = "{{ csrf_token() }}";
var canDocApprove             = @json(auth()->user()->can('task-health-visit-doc-approve'));
var canDocChange              = @json(auth()->user()->can('task-health-visit-doc-change'));

$(function () {
    $('.datepicker-single').datepicker({ dateFormat: 'mm/dd/yy' });

    $('#ca-filter-btn').on('click', function () {
        $('#search-filter-btn').slideToggle(200);
    });

    loadCaList();

    $('body').on('click', '.pagination a', function (event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        if (page) loadCaList(page);
    });
});

function _caToApiDate(v) {
    if (!v) return '';
    var p = v.split('/');
    if (p.length === 3 && p[2].length === 4)
        return p[2] + '-' + ('0'+p[0]).slice(-2) + '-' + ('0'+p[1]).slice(-2);
    return v;
}

function _caParams() {
    return {
        fromDate:       _caToApiDate($('#ca_from_date').val()),
        toDate:         _caToApiDate($('#ca_to_date').val()),
        taskId:         $('#ca_task_id').val().trim(),
        patientId:      $('#ca_patient_id').val().trim(),
        alertStatus:    $('#ca_alert_status').val(),
        resolvedStatus: $('#ca_resolved_status').val(),
        agencyId:       $('#ca_agency_id').val(),
    };
}

function exportCriticalAlertsCsv() {
    var params = new URLSearchParams();
    var p = _caParams();
    if (p.fromDate)       params.append('fromDate',       p.fromDate);
    if (p.toDate)         params.append('toDate',         p.toDate);
    if (p.taskId)         params.append('taskId',         p.taskId);
    if (p.patientId)      params.append('patientId',      p.patientId);
    if (p.alertStatus)    params.append('alertStatus',    p.alertStatus);
    if (p.resolvedStatus) params.append('resolvedStatus', p.resolvedStatus);
    if (p.agencyId)       params.append('agencyId',       p.agencyId);
    window.location.href = _CA_EXPORT_URL + '?' + params.toString();
}

function loadCaList(page) {
    page = page || 1;
    var params = _caParams();
    params.page = page;

    var hasFilter = params.fromDate || params.toDate || params.taskId || params.patientId || params.alertStatus || params.resolvedStatus || params.agencyId;
    $('#ca-active-filter-dot').toggle(!!hasFilter);

    $('.shimmer_id').show();
    $('#response_requested_id').html('');
    $('#ca-stats-strip').hide();

    $.ajax({
        url: _CA_AJAX_URL, type: 'GET', data: params,
        success: function (r) {
            $('.shimmer_id').hide();
            $('#response_requested_id').html(r);
            _caUpdateStats();
        },
        error: function () {
            $('.shimmer_id').hide();
            $('#response_requested_id').html('<div class="alert alert-danger small py-2 m-0">Failed to load records. Please try again.</div>');
        }
    });
}

function _caUpdateStats() {
    var $d = $('#ca-stats-data');
    if (!$d.length) { $('#ca-stats-strip').hide(); return; }
    $('#ca-stat-total').text($d.data('total'));
    $('#ca-stat-critical').text($d.data('critical'));
    $('#ca-stat-resolved').text($d.data('resolved'));
    $('#ca-stats-strip').show();
}

function resetCaFilters() {
    $('#ca_from_date,#ca_to_date,#ca_task_id,#ca_patient_id').val('');
    $('#ca_alert_status,#ca_resolved_status,#ca_agency_id').val('');
    $('#ca-active-filter-dot').hide();
    loadCaList();
}

function openCaDetail(btn) {
    var $b       = $(btn);
    var taskId   = $b.data('task-id')   || '—';
    var alertKey = $b.data('alert');
    $('#ca-detail-resolve-btn').data('record-id', $b.data('id') || '');
    var findings = [];
    try { findings = JSON.parse($b.attr('data-findings') || '[]'); } catch(e) {}

    var isResolved = $b.data('resolved') == '1';
    var headerCls, headerIcon, titleText;
    if (alertKey === 'critical') {
        if (isResolved) {
            headerCls = 'ca-modal-header-grey';  headerIcon = 'mdi-check-circle'; titleText = 'Critical Alert (Resolved)';
        } else {
            headerCls = 'ca-modal-header-red';   headerIcon = 'mdi-alert-circle'; titleText = 'Critical Alert Active';
        }
    } else if (alertKey === 'clear') {
        headerCls = 'ca-modal-header-green'; headerIcon = 'mdi-check-circle';  titleText = 'All Clear';
    } else {
        headerCls = 'ca-modal-header-grey';  headerIcon = 'mdi-clock-outline'; titleText = 'Pending Analysis';
    }

    $('#ca-modal-header').removeClass('ca-modal-header-red ca-modal-header-green ca-modal-header-grey').addClass(headerCls);
    $('#ca-modal-title-text').text(titleText);
    $('#ca-modal-icon').attr('class', 'mdi ' + headerIcon);

    $('#ca-modal-task-id').text(taskId !== '—' ? '#' + taskId : '—');
    $('#ca-modal-patient-id').text($b.data('patient-id') || '—');
    $('#ca-modal-received').text($b.data('received') || '—');

    var summary = $b.data('summary') || '';
    if (summary) { $('#ca-modal-summary').text(summary); $('#ca-modal-summary-wrap').show(); }
    else { $('#ca-modal-summary-wrap').hide(); }

    if (findings.length) {
        var bg  = alertKey === 'critical' ? '#fff5f5' : '#f0fff4';
        var bdr = alertKey === 'critical' ? '#f5c6cb' : '#c3e6cb';
        var nbg = alertKey === 'critical' ? '#dc3545' : '#28a745';
        $('#ca-modal-findings-count').text(findings.length);
        var html = '';
        $.each(findings, function(i, f) {
            html += '<div style="display:flex;align-items:flex-start;gap:10px;background:' + bg + ';border:1px solid ' + bdr + ';border-radius:6px;padding:9px 12px;">' +
                '<div style="min-width:22px;height:22px;border-radius:50%;background:' + nbg + ';color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' + (i+1) + '</div>' +
                '<div style="font-size:13px;color:#1f2937;line-height:1.6;">' + $('<div>').text(f).html() + '</div>' +
            '</div>';
        });
        $('#ca-modal-findings-list').html(html);
        $('#ca-modal-findings-wrap').show();
    } else {
        $('#ca-modal-findings-wrap').hide();
    }

    if ($b.data('resolved') == '1') {
        $('#ca-modal-resolved-by').text($b.data('resolved-by') || '—');
        $('#ca-modal-resolved-at').text($b.data('resolved-at') || '—');
        var notes = $b.data('resolved-notes') || '';
        if (notes) { $('#ca-modal-resolved-notes').text(notes); $('#ca-modal-resolved-notes-wrap').show(); }
        else { $('#ca-modal-resolved-notes-wrap').hide(); }
        $('#ca-modal-resolved-wrap').show();
        $('#ca-detail-resolve-btn').hide();
    } else {
        $('#ca-modal-resolved-wrap').hide();
        $('#ca-detail-resolve-btn').show();
    }

    $('#caDetailModal').modal('show');
}

function openCaResolveModal(id) {
    $('#ca-resolve-id').val(id);
    $('#ca-resolve-notes').val('');
    $('#caResolveModal').modal('show');
}

function openCaResolveFromDetail() {
    var id = $('#ca-detail-resolve-btn').data('record-id');
    $('#caDetailModal').modal('hide');
    openCaResolveModal(id);
}

$('#ca-resolve-save-btn').on('click', function () {
    var id    = $('#ca-resolve-id').val();
    var notes = $('#ca-resolve-notes').val().trim();
    var $btn  = $(this);

    $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving…');

    $.ajax({
        url:  _CA_RESOLVE_URL + '/' + id + '/resolve',
        type: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content'), notes: notes },
        success: function () {
            $('#caResolveModal').modal('hide');
            var $btn = $('.ca-resolve-btn[data-id="' + id + '"]');
            $btn.replaceWith('<span class="label label-success"><i class="mdi mdi-check-circle"></i> Resolved</span>');
            $btn.closest('tr').attr('data-resolved', '1');
            _caUpdateStats();
        },
        error: function () { alert('Failed to save. Please try again.'); },
        complete: function () { $btn.prop('disabled', false).html('<i class="mdi mdi-check"></i> Resolved'); }
    });
});
</script>

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('assets/modulejs/task_health_visit/task_health_visit.js') }}?time={{ time() }}"></script>
