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
</style>
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
<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        {{-- Page Title --}}
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Task Health List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('task-health-critical-alerts')
                    <a href="{{ url('task-health/critical-alerts') }}" class="btn cust-right-btn" style="background-color:#dc3545;color:#fff;margin-right:6px;">
                        <i class="mdi mdi-alert-circle"></i> Critical Alerts
                    </a>
                    @endcan
                    @can('sync-critical-alerts')
                    <a href="javascript:void(0)" onclick="openSyncModal()" class="btn cust-right-btn" style="background-color:#e6a817;color:#fff;margin-right:6px;">
                        <i class="mdi mdi-sync"></i> Sync Critical Alerts
                    </a>
                    @endcan
                    @can('task-health-visit-list')
                    <a href="{{ url('task-health/visit') }}" class="btn cust-right-btn" style="background-color:#6c757d;color:#fff;margin-right:6px;">
                        <i class="mdi mdi-clipboard-text-outline"></i> Live Task Health Visit
                    </a>
                    @endcan

                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color:#00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i> Filter <span class="active-filter"></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
            </div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
            </div>
        @endif

        {{-- Filter Section (hidden by default) --}}
        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display:none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="formsubmit">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency Name</label>
                                                    <select class="form-control" name="agency_id" id="agency_id">
                                                        <option value="">All Agencies</option>
                                                        @foreach($agencyList as $agency)
                                                            <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Patient Name</label>
                                                    <input type="text" class="form-control" id="filter_patient_name" placeholder="Search by first or last name" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Type</label>
                                                    <select class="form-control" name="type" id="type">
                                                        <option value="">All Types</option>
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
                                                    <input type="text" name="created_date" id="created_date" class="datepickernn form-control" placeholder="MM/DD/YYYY - MM/DD/YYYY" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Extra filters: Task ID / TH Patient ID / Mobile / Critical Alert --}}
                                <div class="row form-row-gap mt-1">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Task ID</label>
                                            <input type="text" class="form-control" id="filter_task_id" placeholder="Search by Task ID" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>TH Patient ID</label>
                                            <input type="text" class="form-control" id="filter_th_patient_id" placeholder="Search by TH Patient ID" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Mobile No</label>
                                            <input type="text" class="form-control" id="filter_mobile" placeholder="Search by mobile number" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <label>Critical Alert</label>
                                            <select class="form-control" id="filter_critical_alert">
                                                <option value="">All</option>
                                                <option value="active">Active (Critical)</option>
                                                <option value="clear">Clear</option>
                                                <option value="none">No Alert</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Flag filters --}}
                                <div class="row form-row-gap mt-1">
                                    <div class="col-md-12">
                                        <label class="font-weight-bold mb-1" style="font-size:12px;">Filter by Flags</label>
                                        <div class="d-flex flex-wrap" style="gap:20px;">
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="filter_poc" value="1">
                                                    POC <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="filter_mdo" value="1">
                                                    MDO <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="filter_alert" value="1">
                                                    Alert <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="filter_supervision" value="1">
                                                    Supervision <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="filter_assessment" value="1">
                                                    Assessment <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="filter_kardex" value="1">
                                                    Kardex <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="filter_patient_package_doc" value="1">
                                                    Patient Package Doc <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-12">
                                    <div class="appointment-btn-box" style="justify-content:left !important;">
                                        <input type="button" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadTaskHealthMasterList(1)">
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="resetMasterFilters()">
                                            <i class="mdi mdi-reload"></i> Reset
                                        </a>
                                        @can('task-health-export')
                                            <a href="javascript:void(0)" onclick="exportTaskHealthCsv()" class="btn cust-right-btn" style="background-color:#28a745;color:#fff;margin-right:6px;">
                                                <i class="fa fa-download"></i> Export CSV
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

        {{-- Table --}}
        <div class="row">
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Patient Id</th>
                                    <th>TH Patient ID</th>
                                    <th>Task ID</th>
                                    <th>Agency Name</th>
                                    <th>Patient Name</th>
                                    <th>Type<br/>Gender</th>
                                    <th>DOB</th>
                                    <th>Phone</th>
                                    <th>Critical Alert</th>
                                    <th>Created Date</th>
                                    <th>Flags</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr><td colspan="13"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="resp"></span>
            </div>
        </div>

    </div>
    <div style="color:red" id="blank_div" class="mt-5">&nbsp;</div>
    @include('include/footer')
</div>

@include('_partial.task_health_flags.modal')
@include('task_health_master/_partial/view_modal')
@include('task_health_master/_partial/revert_patient_modal')
@include('task_health_master/_partial/convert_modal')
@include('task_health_critical_alert._partial.visit_detail_modal')
@include('_partial.task_health_visit.link_patient_modal')

{{-- Sync Critical Alerts Modal --}}
<div class="modal fade" id="syncCriticalAlertsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document" style="max-width:420px;margin-top:80px;">
        <div class="modal-content" style="border:none;border-radius:8px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e1e2f,#2d3a4a);color:#fff;padding:12px 18px;border-bottom:none;">
                <h6 class="modal-title" style="font-size:13px;font-weight:700;display:flex;align-items:center;gap:7px;">
                    <i class="mdi mdi-sync"></i> Sync Critical Alerts from TH API
                </h6>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.75;font-size:20px;">&times;</button>
            </div>
            <div class="modal-body" style="padding:16px 20px;">
                <p style="font-size:12px;color:#6c757d;margin-bottom:14px;">
                    Fetches visits with critical alert data from the Task Health API and syncs them into the local database. Use the filters below to limit the sync range.
                </p>
                <div class="form-group">
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;">Sort By</label>
                    <select id="sync_sortBy" class="form-control form-control-sm">
                        <option value="scheduledDateTime">Scheduled Date</option>
                        <option value="createdAt">Created Date</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;">From Date</label>
                    <input type="text" id="sync_fromDate" class="form-control form-control-sm datepickernn-sync" placeholder="MM/DD/YYYY" autocomplete="off">
                </div>
                <div class="form-group">
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;">To Date</label>
                    <input type="text" id="sync_toDate" class="form-control form-control-sm datepickernn-sync" placeholder="MM/DD/YYYY" autocomplete="off">
                </div>
                <div id="sync-result" style="display:none;margin-top:10px;"></div>
            </div>
            <div class="modal-footer" style="padding:10px 18px;background:#f8f9fa;border-top:1px solid #dee2e6;">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" style="font-size:12px;">
                    <i class="mdi mdi-close"></i> Close
                </button>
                <button type="button" class="btn btn-sm btn-warning" id="sync-ca-btn" onclick="runSyncCriticalAlerts()" style="font-size:12px;font-weight:600;min-width:110px;">
                    <i class="mdi mdi-sync"></i> <span id="sync-ca-btn-text">Run Sync</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ URL::to('assets/js/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('/') }}/assets/js/moment.min.js"></script>
<script type="text/javascript" src="{{ URL::to('/') }}/assets/js/daterangepicker.min.js"></script>
<script src="{{ URL::to('/') }}/assets/sweetalert.min.js"></script>
<script>
    var _TASK_HEALTH_MASTER_LIST = "{{ url('task-health-ajax-list') }}";
    var _TASK_HEALTH_MASTER      = '{{ url("/task-health") }}';
    var _TASK_HEALTH_MASTER_BY_ID    = '{{ url("/get-task-health-master-by-id") }}';
    var _TASK_HEALTH_REVERT_SEARCH   = '{{ url("/task-health-revert-search") }}';
    var _TASK_HEALTH_REVERT_PATIENT  = '{{ url("/task-health-revert-patient") }}';
    var _TASK_HEALTH_FLAG_UPDATE     = '{{ url("/task-health-flag-update") }}';
    var _TASK_HEALTH_FLAGS_SAVE      = '{{ url("/task-health-flags-save") }}';
    var _TASK_HEALTH_EXPORT_CSV      = '{{ url("/task-health-export-csv") }}';
    var _TASK_HEALTH_SYNC_CA_URL     = '{{ url("/task-health-sync-critical-alerts") }}';
    var _TASK_HEALTH_CONVERT         = '{{ url("/task-health-convert") }}';
    var _CSRF_TOKEN = '{{ csrf_token() }}';
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
    var _SEND_HHA_SUPERVISION = "{{ url('supervision')}}";
</script>
<script src="{{ asset('assets/modulejs/task_health_master/task_health_master.js') }}?time={{ time() }}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('assets/modulejs/task_health_visit/task_health_visit.js') }}?time={{ time() }}"></script>
