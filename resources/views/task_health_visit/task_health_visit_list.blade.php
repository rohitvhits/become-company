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

    /* ══════════════════════════════════════════════════
       Create Visit Modal — Tabbed Redesign
       ══════════════════════════════════════════════════ */

    /* Shell */
    #createVisitModal .modal-content {
        border:none; border-radius:8px; overflow:hidden;
        box-shadow:0 20px 60px rgba(0,0,0,.25);
    }
    #createVisitModal .modal-header {
        background:linear-gradient(135deg,#1e1e2f 0%,#2d3a4a 100%);
        color:#fff; padding:13px 20px; border-bottom:none;
    }
    #createVisitModal .modal-title { font-size:15px; font-weight:600; display:flex; align-items:center; gap:8px; }
    #createVisitModal .close { color:#fff; opacity:.75; font-size:22px; line-height:1; }
    #createVisitModal .close:hover { opacity:1; }
    #createVisitModal .modal-footer { padding:10px 20px; background:#fff; border-top:1px solid #dee2e6; }
    #createVisitModal .req { color:#dc3545; }

    /* Agency persistent bar */
    .cv-top-bar {
        background:#f8f9fa; border-bottom:2px solid #e9ecef;
        padding:10px 20px; display:flex; align-items:flex-start; gap:16px; flex-wrap:wrap;
    }
    .cv-top-bar-field { min-width:260px; max-width:340px; flex:1; }
    .cv-top-bar label {
        font-size:10.5px; font-weight:700; color:#505a65;
        text-transform:uppercase; letter-spacing:.4px; margin-bottom:3px; display:block;
    }
    .cv-top-bar .form-control { font-size:12.5px; height:30px; padding:3px 9px; border-radius:4px; }

    /* Tab layout */
    .cv-modal-layout { display:flex; height:60vh; overflow:hidden; }

    /* Sidebar */
    .cv-sidebar {
        width:185px; flex-shrink:0; background:#f8f9fa;
        border-right:2px solid #e9ecef; padding:14px 0;
        display:flex; flex-direction:column;
    }
    .cv-tab-btn {
        padding:12px 16px; cursor:pointer; border:none; background:none;
        font-size:12.5px; font-weight:500; color:#6c757d;
        border-left:3px solid transparent; transition:all .18s;
        text-align:left; display:flex; align-items:center; gap:10px; white-space:nowrap;
    }
    .cv-tab-btn i { font-size:17px; min-width:20px; }
    .cv-tab-btn:hover { background:#e9ecef; color:#343a40; }
    .cv-tab-btn.active { color:#007bff; border-left-color:#007bff; background:#fff; font-weight:600; }
    .cv-tab-btn .cv-tab-err {
        margin-left:auto; width:7px; height:7px; border-radius:50%;
        background:#dc3545; display:none; flex-shrink:0;
    }
    .cv-tab-btn.has-error .cv-tab-err { display:block; }

    /* Right content pane */
    .cv-tab-content { flex:1; overflow-y:auto; background:#fff; padding:20px 22px 16px; }
    .cv-tab-panel { display:none; }
    .cv-tab-panel.active { display:block; animation:cvFade .2s; }
    @keyframes cvFade { from{opacity:0;transform:translateX(4px)} to{opacity:1;transform:translateX(0)} }

    /* Panel header */
    .cv-panel-header { margin-bottom:16px; padding-bottom:10px; border-bottom:2px solid #f0f2f5; }
    .cv-panel-header h6 { font-size:14px; font-weight:700; color:#1e1e2f; margin:0 0 3px; display:flex; align-items:center; gap:8px; }
    .cv-panel-header small { font-size:11px; color:#6c757d; }

    /* Sub-section cards inside a tab panel */
    .cv-sub-section {
        margin-bottom:14px; padding:12px 14px 6px;
        border:1px solid #e9ecef; border-radius:6px; background:#fafbfc;
    }
    .cv-sub-section-title {
        font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px;
        color:#6c757d; margin-bottom:10px; display:flex; align-items:center; gap:7px;
    }
    .cv-sub-section-title::after { content:''; flex:1; height:1px; background:#e9ecef; }

    /* Form elements inside tabs */
    .cv-tab-content .form-group { margin-bottom:12px; }
    .cv-tab-content label {
        font-size:11px; font-weight:600; color:#505a65;
        text-transform:uppercase; letter-spacing:.3px; margin-bottom:3px; display:block;
    }
    .cv-tab-content .form-control {
        font-size:12.5px; height:30px; padding:3px 9px; border-color:#dee2e6; border-radius:4px;
    }
    .cv-tab-content textarea.form-control { height:auto; }
    .cv-tab-content select.form-control { height:30px; }

    /* Phone entries */
    .cv-phone-entry .form-control { height:28px; font-size:12.5px; }

    /* Field errors */
    .cv-field-error { color:#dc3545; font-size:11px; margin-top:3px; display:none; line-height:1.3; }
    .cv-field-error.show { display:block; }
    .cv-invalid { border-color:#dc3545 !important; box-shadow:none !important; }

    /* Responsive */
    @media (max-width:767px) {
        .cv-modal-layout { flex-direction:column; height:auto; }
        .cv-sidebar { width:100%; flex-direction:row; overflow-x:auto; border-right:none; border-bottom:2px solid #e9ecef; padding:0; }
        .cv-tab-btn { border-left:none; border-bottom:3px solid transparent; padding:10px 13px; justify-content:center; }
        .cv-tab-btn span { display:none; }
        .cv-tab-btn.active { border-left-color:transparent; border-bottom-color:#007bff; }
        .cv-tab-content { max-height:58vh; }
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        {{-- Page Title --}}
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Task Health Visit List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('task-health-list')
                    <a href="{{ url('task-health') }}" class="btn cust-right-btn" style="background-color:#6c757d;color:#fff;margin-right:6px;">
                        <i class="mdi mdi-format-list-bulleted"></i> Task Health
                    </a>
                    @endcan
                    @can('task-health-critical-alerts')
                    <a href="{{ url('task-health/critical-alerts') }}" class="btn cust-right-btn" style="background-color:#dc3545;color:#fff;margin-right:6px;">
                        <i class="mdi mdi-alert-circle"></i> Critical Alerts
                    </a>
                    @endcan
                    @can('task-health-visit-create')
                        <a href="javascript:void(0)" id="btn-create-visit" class="btn cust-right-btn" style="background-color:#28a745;color:#fff;margin-right:6px;">
                            <i class="mdi mdi-plus"></i> Create Visit
                        </a>
                    @endcan
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color:#00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        {{-- Filter Section (hidden by default) --}}
        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display:none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency</label>
                                                    <select class="js-example-basic-multiple w-100" multiple="multiple" id="agencyIds" style="width:100%;"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>From Date</label>
                                                    <input type="text" class="form-control datepicker-single" id="fromDate" placeholder="MM/DD/YYYY" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>To Date</label>
                                                    <input type="text" class="form-control datepicker-single" id="toDate" placeholder="MM/DD/YYYY" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Sort By</label>
                                                    <select class="form-control" id="sortBy">
                                                        <option value="createdAt">Created Date</option>
                                                        <option value="scheduledDateTime">Scheduled Date</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Status</label>
                                                    <select class="js-example-basic-multiple w-100" multiple="multiple" id="thStatus" style="width:100%;">
                                                        <option value="Needs Attention">Needs Attention</option>
                                                        <option value="In Progress">In Progress</option>
                                                        <option value="Completed">Completed</option>
                                                        <option value="Cancelled">Cancelled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Review Status</label>
                                                    <select class="js-example-basic-multiple w-100" multiple="multiple" id="reviewStatus" style="width:100%;">
                                                        <option value="Pending RN changes">Pending RN changes</option>
                                                        <option value="Reviewed">Reviewed</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Search</label>
                                                    <input type="text" class="form-control" id="thSearch" placeholder="Patient name, task ID...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="form-check form-check-flat form-check-primary" style="margin-top: 28px !important ; margin-left: 25px !important;">
                                                    <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="hasCriticalAlert" value="true">
                                                        Show Critical Alert
                                                        <i class="input-helper"></i></label>
                                                </div>
                                            </div>
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
                                                    <input type="checkbox" class="form-check-input" id="vl_filter_poc" value="1">
                                                    POC <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="vl_filter_mdo" value="1">
                                                    MDO <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="vl_filter_alert" value="1">
                                                    Alert <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="vl_filter_supervision" value="1">
                                                    Supervision <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="vl_filter_assessment" value="1">
                                                    Assessment <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="vl_filter_kardex" value="1">
                                                    Kardex <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="vl_filter_patient_package_doc" value="1">
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
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadVisitList(1)">
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="resetFilters()">
                                            <i class="mdi mdi-reload"></i> Reset
                                        </a>
                                        @can('task-health-visit-export')
                                        <a href="javascript:void(0)" onclick="exportVisitCsv()" class="btn btn-success cust-right-btn"><i class="mdi mdi-download"></i> Export CSV</a>
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
                                    <th>Task ID</th>
                                    <th>Patient ID</th>
                                    <th>Agency Name</th>
                                    <th>Patient Name</th>
                                    <th>Task Type</th>
                                    <th>Status</th>
                                    <th>Review Status</th>
                                    <th>Critical Alert</th>
                                    <th>Scheduled Date</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr><td colspan="12"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="response_requested_id"></span>
            </div>
        </div>

    </div>
    <div style="color:red" id="blank_div" class="mt-5">&nbsp;</div>
</div>

@include('include/footer')
@include('_partial.task_health_flags.modal')
@include('_partial.task_health_visit.link_patient_modal')
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/js/daterangepicker.min.js') }}"></script>
{{-- Create Visit Modal --}}
<div class="modal fade" id="createVisitModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-plus-circle-outline"></i> Create New Visit
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <form id="createVisitForm">
                @csrf

                {{-- ── Persistent Agency bar (always visible) ── --}}
                <div class="cv-top-bar">
                    <div class="cv-top-bar-field">
                        <label>Agency <span class="req">*</span></label>
                        <select class="form-control" id="cv_agencyId" name="taskHealthAgencyId">
                            <option value="">-- Select Agency --</option>
                        </select>
                        <div class="cv-field-error" id="err_agencyId"></div>
                    </div>
                </div>

                {{-- ── Tabbed body ── --}}
                <div class="cv-modal-layout">

                    {{-- Left sidebar --}}
                    <div class="cv-sidebar">
                        <button type="button" class="cv-tab-btn active" data-cv-tab="patient" onclick="cvSwitchTab('patient',this)">
                            <i class="mdi mdi-account-outline"></i>
                            <span>Patient Info</span>
                            <span class="cv-tab-err"></span>
                        </button>
                        <button type="button" class="cv-tab-btn" data-cv-tab="address" onclick="cvSwitchTab('address',this)">
                            <i class="mdi mdi-map-marker-outline"></i>
                            <span>Address</span>
                            <span class="cv-tab-err"></span>
                        </button>
                        <button type="button" class="cv-tab-btn" data-cv-tab="visit" onclick="cvSwitchTab('visit',this)">
                            <i class="mdi mdi-clipboard-text-outline"></i>
                            <span>Visit Details</span>
                            <span class="cv-tab-err"></span>
                        </button>
                    </div>

                    {{-- Right content --}}
                    <div class="cv-tab-content" id="cv-tab-content-wrapper">

                        {{-- ══ Tab 1: Patient Information ══ --}}
                        <div class="cv-tab-panel active" id="cv-panel-patient">
                            <div class="cv-panel-header">
                                <h6><i class="mdi mdi-account-circle-outline" style="color:#007bff;font-size:18px;"></i> Patient Information</h6>
                                <small>Enter the patient's personal details. Fields marked <span class="req">*</span> are required.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name <span class="req">*</span></label>
                                        <input type="text" class="form-control" id="cv_firstName" name="patient_firstName" placeholder="First name">
                                        <div class="cv-field-error" id="err_firstName"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Name <span class="req">*</span></label>
                                        <input type="text" class="form-control" id="cv_lastName" name="patient_lastName" placeholder="Last name">
                                        <div class="cv-field-error" id="err_lastName"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="req">*</span></label>
                                        <input type="text" class="form-control cv-datepicker" id="cv_dob" name="patient_dateOfBirth" placeholder="MM/DD/YYYY" autocomplete="off" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                        <div class="cv-field-error" id="err_dob"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender <span class="req">*</span></label>
                                        <select class="form-control" id="cv_gender" name="patient_gender">
                                            <option value="">-- Select --</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                        <div class="cv-field-error" id="err_gender"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Language <span class="req">*</span></label>
                                        <select class="form-control" id="cv_language" name="patient_language">
                                            <option value="">-- Select Language --</option>
                                            <option value="English">English</option>
                                            <option value="Spanish">Spanish</option>
                                            <option value="Chinese">Chinese (Mandarin/Cantonese)</option>
                                            <option value="Russian">Russian</option>
                                            <option value="Haitian Creole">Haitian Creole</option>
                                            <option value="Arabic">Arabic</option>
                                            <option value="Korean">Korean</option>
                                            <option value="Bengali">Bengali</option>
                                            <option value="Polish">Polish</option>
                                            <option value="French">French</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <div id="cv_speaksEnglishRow" style="display:none;margin-top:8px;">
                                            <label style="font-size:11px;margin-bottom:3px;letter-spacing:.3px;">Patient speaks English? <small class="text-muted font-weight-normal text-lowercase">(optional)</small></label>
                                            <select class="form-control" id="cv_speaksEnglish" name="patient_speaksEnglish">
                                                <option value="">-- Select --</option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="cv-field-error" id="err_language"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Numbers <span class="req">*</span></label>
                                        <div class="cv-phone-entry d-flex align-items-center mb-1">
                                            <span class="badge badge-primary mr-1" style="font-size:10px;min-width:50px;text-align:center;padding:3px 5px;">Primary</span>
                                            <input type="text" class="form-control" id="cv_phone" name="patient_phone" placeholder="+17185550400">
                                        </div>
                                        <div id="cv_extra_phones"></div>
                                        <button type="button" class="btn btn-link p-0 mt-1" style="font-size:11px;text-decoration:none;color:#007bff;" onclick="cvAddPhone()">
                                            <i class="mdi mdi-plus-circle-outline"></i> Add phone number
                                        </button>
                                        <div class="cv-field-error" id="err_phone"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ══ Tab 2: Address & Location ══ --}}
                        <div class="cv-tab-panel" id="cv-panel-address">
                            <div class="cv-panel-header">
                                <h6><i class="mdi mdi-map-marker-outline" style="color:#28a745;font-size:18px;"></i> Patient's Address</h6>
                                <small>Enter the patient's residential address for this visit.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Address <span class="req">*</span></label>
                                        <input type="text" class="form-control" id="cv_address" name="patient_address" placeholder="Full address with city, state, zip">
                                        <div class="cv-field-error" id="err_address"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address 2 <small class="text-muted font-weight-normal text-lowercase">(apt/unit)</small></label>
                                        <input type="text" class="form-control" name="patient_address2" placeholder="Apt / Unit">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address Instructions <small class="text-muted font-weight-normal text-lowercase">(optional)</small></label>
                                        <input type="text" class="form-control" name="patient_addressInstructions" placeholder="e.g. Ring bell #3">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ══ Tab 3: Visit Details ══ --}}
                        <div class="cv-tab-panel" id="cv-panel-visit">
                            <div class="cv-panel-header">
                                <h6><i class="mdi mdi-clipboard-text-outline" style="color:#6f42c1;font-size:18px;"></i> Visit Details</h6>
                                <small>Configure visit type, scheduling, services, and caregiver information.</small>
                            </div>

                            {{-- Visit Type & Certification Period --}}
                            <div class="cv-sub-section">
                                <div class="cv-sub-section-title">Visit Type &amp; Certification Period</div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Visit Type <span class="req">*</span></label>
                                            <select class="form-control" id="cv_taskType" name="taskType">
                                                <option value="">-- Select Type --</option>
                                                <option value="START_OF_CARE">Start of Care</option>
                                                <option value="REASSESSMENT">Reassessment</option>
                                                <option value="SUPERVISORY">Supervisory</option>
                                            </select>
                                            <div class="cv-field-error" id="err_taskType"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="cv_socDateRow" style="display:none;">
                                        <div class="form-group">
                                            <label>Start of Care Date <span class="req">*</span></label>
                                            <input type="text" class="form-control cv-datepicker" id="cv_startOfCareDate" name="startOfCareDate" placeholder="MM/DD/YYYY" autocomplete="off" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                            <div class="cv-field-error" id="err_startOfCareDate"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Cert. Start Date</label>
                                            <input type="text" class="form-control cv-datepicker" name="cert_startDate" placeholder="MM/DD/YYYY" autocomplete="off" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Cert. End Date</label>
                                            <input type="text" class="form-control cv-datepicker" name="cert_endDate" placeholder="MM/DD/YYYY" autocomplete="off" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Service & Payer --}}
                            <div class="cv-sub-section">
                                <div class="cv-sub-section-title">Service &amp; Payer</div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Service Type</label>
                                            <select class="form-control" id="cv_serviceTypeSelect">
                                                <option value="">-- Select --</option>
                                                <option value="HHA">HHA</option>
                                                <option value="PCA">PCA</option>
                                                <option value="__other__">Other (specify)</option>
                                            </select>
                                            <input type="text" class="form-control mt-1" id="cv_serviceTypeOther" placeholder="Enter service type..." style="display:none;">
                                            <input type="hidden" name="serviceType" id="cv_serviceTypeValue">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Payer Source</label>
                                            <select class="form-control" id="cv_payerSourceSelect">
                                                <option value="">-- Select --</option>
                                                <option value="Managed Long-Term Care (MLTC)">Managed Long-Term Care (MLTC)</option>
                                                <option value="Certified Home Health Agency (CHHA)">Certified Home Health Agency (CHHA)</option>
                                                <option value="Private">Private</option>
                                                <option value="__other__">Other (specify)</option>
                                            </select>
                                            <input type="text" class="form-control mt-1" id="cv_payerSourceOther" placeholder="Enter payer source..." style="display:none;">
                                            <input type="hidden" name="payerSource" id="cv_payerSourceValue">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Frequency</label>
                                            <input type="text" class="form-control" name="frequency" placeholder="e.g. 5x/week">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Schedule --}}
                            <div class="cv-sub-section">
                                <div class="cv-sub-section-title">Schedule</div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Assessment Start</label>
                                            <input type="text" class="form-control cv-datepicker" id="cv_assessmentStartDate" name="assessmentStartDate" placeholder="MM/DD/YYYY" autocomplete="off" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Assessment Due <span class="req" id="cv_assessmentDueDateReq" style="display:none;">*</span></label>
                                            <input type="text" class="form-control cv-datepicker" id="cv_assessmentDueDate" name="assessmentDueDate" placeholder="MM/DD/YYYY" autocomplete="off" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                            <div class="cv-field-error" id="err_assessmentDueDate"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Patient Availability <span class="req" id="cv_scheduleFreeTextReq" style="display:none;">*</span></label>
                                            <input type="text" class="form-control" id="cv_scheduleFreeText" name="scheduleFreeText" placeholder="e.g. Mon/Wed/Fri 9am">
                                            <div class="cv-field-error" id="err_scheduleFreeText"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Patient Availability + Caregiver --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="cv-sub-section h-100">
                                        <div class="cv-sub-section-title">Patient Availability</div>
                                        <div class="form-group mb-0">
                                            <label>Agency Note <small class="text-muted font-weight-normal text-lowercase">(optional)</small></label>
                                            <textarea class="form-control" name="agencyNote" rows="3" placeholder="Free-text note for nurse" style="resize:vertical;height:74px;"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="cv-sub-section h-100">
                                        <div class="cv-sub-section-title">Caregiver</div>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="caregiver_name" placeholder="Full name">
                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="caregiver_phone" placeholder="+17185550500">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- end cv-panel-visit --}}

                    </div>{{-- end cv-tab-content --}}
                </div>{{-- end cv-modal-layout --}}

                {{-- Alert bar --}}
                <div id="createVisitAlert" class="alert mb-0" style="display:none;font-size:13px;border-radius:0;margin:0;border-left:none;border-right:none;"></div>

                <div class="modal-footer">
                    <span class="text-muted mr-auto" style="font-size:11px;"><span class="req">*</span> Required fields</span>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btn-submit-visit">
                        <span id="btn-submit-visit-text"><i class="mdi mdi-check"></i> Create Visit</span>
                        <span id="btn-submit-visit-spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Creating...</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     Visit Detail Drawer — Right-side slide-in
     ═══════════════════════════════════════════════════════ --}}
<style>
    /* ── Task ID link ── */
    .th-task-id-link { font-weight:700; color:#007bff; text-decoration:none; }
    .th-task-id-link:hover { text-decoration:underline; color:#0056b3; }

    /* ── Overlay backdrop ── */
    .vd-overlay {
        display:none; position:fixed; z-index:1055;
        inset:0; background:rgba(0,0,0,.45);
    }
    .vd-overlay.show { display:flex; justify-content:flex-end; }

    /* ── Drawer panel ── */
    .vd-drawer {
        width:700px; max-width:100vw; height:100vh;
        background:#fff; display:flex; flex-direction:column;
        box-shadow:-6px 0 32px rgba(0,0,0,.18);
        transform:translateX(100%);
        transition:transform .3s cubic-bezier(.25,.8,.25,1);
    }
    .vd-overlay.show .vd-drawer { transform:translateX(0); }

    /* ── Header ── */
    .vd-header {
        padding:14px 18px;
        border-bottom:1px solid #e9ecef;
        display:flex; align-items:center; gap:10px;
        background:#fff; flex-shrink:0; flex-wrap:wrap;
    }
    .vd-header-left { display:flex; align-items:center; gap:10px; flex:1; flex-wrap:wrap; min-width:0; }
    .vd-avatar {
        width:44px; height:44px; border-radius:50%;
        background:linear-gradient(135deg,#667eea,#764ba2);
        color:#fff; font-size:15px; font-weight:700;
        display:flex; align-items:center; justify-content:center;
        flex-shrink:0; letter-spacing:1px;
    }
    .vd-header-info { display:flex; flex-direction:column; gap:1px; min-width:0; }
    .vd-header-info h4 { margin:0; font-size:15px; font-weight:700; color:#1a1a2e; line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .vd-task-id { font-size:12px; color:#6c757d; font-weight:500; }
    .vd-badge-status { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; color:#fff; white-space:nowrap; }
    .vd-badge-status.info    { background:#17a2b8; }
    .vd-badge-status.success { background:#28a745; }
    .vd-badge-status.danger  { background:#dc3545; }
    .vd-badge-status.warning { background:#e0a800; color:#333; }
    .vd-badge-type {
        padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
        background:#e8f0fe; color:#1a73e8; border:1px solid #c5d9f9; white-space:nowrap;
    }
    .vd-header-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
    .vd-action-btn {
        width:32px; height:32px; border-radius:6px; border:1px solid #dee2e6;
        background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;
        font-size:15px; transition:all .15s; padding:0;
    }
    .vd-action-danger  { color:#dc3545; border-color:#f5c6cb; }
    .vd-action-danger:hover  { background:#dc3545; color:#fff; border-color:#dc3545; }
    .vd-action-close   { color:#6c757d; }
    .vd-action-close:hover   { background:#f8f9fa; color:#343a40; }

    /* ── Horizontal tabs ── */
    .vd-tabs {
        display:flex; gap:2px; padding:0 18px;
        border-bottom:2px solid #e9ecef;
        background:#fff; flex-shrink:0;
    }
    .vd-tab {
        padding:10px 20px; border:none; background:none;
        font-size:13px; font-weight:500; color:#6c757d;
        cursor:pointer; border-bottom:3px solid transparent;
        margin-bottom:-2px; transition:all .15s;
    }
    .vd-tab:hover { color:#007bff; }
    .vd-tab.active { color:#007bff; font-weight:600; border-bottom-color:#007bff; }

    /* ── Body ── */
    .vd-body { flex:1; overflow-y:auto; background:#f7f8fc; }
    .vd-panel { display:none; }
    .vd-panel.active { display:block; animation:vdFade .2s; }
    @keyframes vdFade { from{opacity:0} to{opacity:1} }

    /* ── Sections ── */
    .vd-section {
        background:#fff; border-radius:8px;
        margin:14px 14px 0; border:1px solid #e9ecef;
        padding:14px 16px;
    }
    .vd-section:last-child { margin-bottom:14px; }
    .vd-section-title {
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#6c757d;
        margin-bottom:12px; padding-bottom:8px;
        border-bottom:1px solid #f0f2f5;
        display:flex; align-items:center; gap:7px;
    }
    .vd-section-title::before {
        content:''; width:3px; height:13px;
        background:#007bff; border-radius:2px; flex-shrink:0;
    }

    /* ── 3-column grid ── */
    .vd-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .vd-cell-label {
        font-size:11px; font-weight:600; color:#9ca3af;
        text-transform:uppercase; letter-spacing:.3px; margin-bottom:3px;
    }
    .vd-cell-value { font-size:13px; font-weight:600; color:#1f2937; word-break:break-word; }

    /* ── Patient contact strip ── */
    .vd-patient-contact {
        margin-top:12px; padding:10px 13px;
        background:#f8f9fa; border-radius:6px;
        display:flex; align-items:center; gap:20px; flex-wrap:wrap;
        border:1px solid #e9ecef;
    }
    .vd-contact-name, .vd-contact-phone { font-size:13px; color:#495057; }
    .vd-contact-name i, .vd-contact-phone i { color:#007bff; margin-right:4px; }

    /* ── Data table ── */
    .vd-table { width:100%; border-collapse:collapse; font-size:12.5px; }
    .vd-table th {
        background:#f8f9fa; color:#6c757d; font-weight:600;
        font-size:11px; text-transform:uppercase; letter-spacing:.3px;
        padding:8px 10px; border-bottom:2px solid #dee2e6; text-align:left;
    }
    .vd-table td { padding:8px 10px; border-bottom:1px solid #f0f2f5; color:#1f2937; vertical-align:middle; }
    .vd-table tr:hover td { background:#fafbff; }
    .vd-table tr:last-child td { border-bottom:none; }

    /* ── Small action buttons ── */
    .vd-btn-sm {
        display:inline-flex; align-items:center; gap:3px;
        padding:3px 9px; border-radius:4px; font-size:11px;
        font-weight:500; cursor:pointer; border:none; text-decoration:none; margin-right:2px;
    }
    .vd-btn-info    { background:#17a2b8; color:#fff; }
    .vd-btn-success { background:#28a745; color:#fff; }
    .vd-btn-warning { background:#e0a800; color:#fff; }
    .vd-btn-info:hover    { background:#138496; color:#fff; }
    .vd-btn-success:hover { background:#218838; color:#fff; }
    .vd-btn-warning:hover { background:#c69500; color:#fff; }

    /* ── Shimmer loader ── */
    .shimmer-wrapper { padding:20px; }
    .shimmer-card { background:#fff; border-radius:8px; padding:20px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,.1); }
    .shimmer {
        background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
        background-size:200% 100%; animation:shimmerAnim 1.5s infinite; border-radius:4px;
    }
    @keyframes shimmerAnim { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    .shimmer-line { height:15px; margin-bottom:11px; }
    .shimmer-line.title  { height:22px; width:40%; margin-bottom:18px; }
    .shimmer-line.short  { width:55%; }
    .shimmer-line.medium { width:75%; }
    .shimmer-line.long   { width:100%; }
    .shimmer-header { height:58px; margin-bottom:20px; border-radius:8px; }

    /* ── Responsive ── */
    @media (max-width:768px) {
        .vd-drawer { width:100vw; }
        .vd-grid-3 { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width:480px) {
        .vd-grid-3 { grid-template-columns:1fr; }
    }

    /* ── Patient Record Banner (in General tab) ── */
    .vd-pr-banner {
        margin:14px 14px 0;
        border-radius:8px;
        border:1px solid #e9ecef;
        overflow:hidden;
        background:#fff;
    }
    .vd-pr-banner-header {
        display:flex; align-items:center; gap:8px;
        padding:9px 14px;
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#6c757d;
        border-bottom:1px solid #f0f2f5;
        background:#f8f9fa;
    }
    .vd-pr-banner-header::before {
        content:''; width:3px; height:13px;
        background:#6f42c1; border-radius:2px; flex-shrink:0;
    }
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
    .vd-pr-btn {
        display:inline-flex; align-items:center; gap:4px;
        padding:5px 13px; border-radius:5px; font-size:12px;
        font-weight:600; cursor:pointer; border:none; text-decoration:none;
        white-space:nowrap; transition:all .15s;
    }
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
    .vd-pr-card {
        background:#fff; border-radius:8px; border:1px solid #e9ecef;
        margin:14px 14px 0; padding:20px;
    }
    .vd-pr-card:last-child { margin-bottom:14px; }
    .vd-pr-card-title {
        font-size:13px; font-weight:700; color:#1a1a2e;
        margin-bottom:14px; padding-bottom:10px;
        border-bottom:2px solid #f0f2f5;
        display:flex; align-items:center; gap:8px;
    }
    .vd-pr-detail-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
    .vd-pr-detail-item { }
    .vd-pr-detail-label { font-size:10.5px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
    .vd-pr-detail-value { font-size:13px; font-weight:600; color:#1f2937; }
    @media (max-width:480px) { .vd-pr-detail-grid { grid-template-columns:1fr; } }

    /* ── ID Summary Strip ── */
    .vd-id-strip {
        display:flex; margin:14px 14px 0; border-radius:8px;
        border:1px solid #e9ecef; overflow:hidden; background:#fff;
    }
    .vd-id-pill {
        flex:1; padding:11px 14px; border-right:1px solid #e9ecef;
        display:flex; flex-direction:column; gap:3px; min-width:0;
    }
    .vd-id-pill:last-child { border-right:none; }
    .vd-id-pill-label {
        font-size:9.5px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#9ca3af; display:flex; align-items:center; gap:4px;
    }
    .vd-id-pill-value {
        font-size:15px; font-weight:700; color:#1f2937; font-family:monospace;
        letter-spacing:-.3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .vd-id-pill-value a { color:#007bff; text-decoration:none; }
    .vd-id-pill-value a:hover { text-decoration:underline; }
    .vd-id-pill.vd-id-linked { background:#f0fdf4; }
    .vd-id-pill.vd-id-linked .vd-id-pill-label { color:#15803d; }
    .vd-id-pill.vd-id-linked .vd-id-pill-value { color:#16a34a; }
    .vd-id-pill.vd-id-loading .vd-id-pill-value { color:#d1d5db; font-size:12px; font-family:inherit; }
    /* TH Patient ID badge in drawer header */
    .vd-th-patient-badge {
        display:none; font-size:11px; font-weight:600; color:#6f42c1;
        background:#f3e8ff; border:1px solid #d8b4fe; border-radius:4px;
        padding:1px 7px; letter-spacing:.2px; white-space:nowrap;
    }
    @media (max-width:480px) {
        .vd-id-strip { flex-direction:column; }
        .vd-id-pill { border-right:none; border-bottom:1px solid #e9ecef; }
        .vd-id-pill:last-child { border-bottom:none; }
    }
</style>

{{-- HHA Upload Confirmation Modal --}}
@include('task_health_critical_alert._partial.hha_upload_modal')

<div id="visitDetailModal" class="vd-overlay">
    <div class="vd-drawer">

        {{-- Header --}}
        <div class="vd-header">
            <div class="vd-header-left">
                <div class="vd-avatar" id="vd-avatar-initials">…</div>
                <div class="vd-header-info">
                    <h4 id="vModalPatientName">Loading...</h4>
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <span id="vModalTaskId" class="vd-task-id">#—</span>
                        <span id="vd-th-patient-badge" class="vd-th-patient-badge"></span>
                    </div>
                </div>
                <span id="vd-status-badge" class="vd-badge-status info" style="display:none;"></span>
                <span id="vd-type-badge"   class="vd-badge-type"        style="display:none;"></span>
            </div>
            <div class="vd-header-actions">
                <button class="vd-action-btn thf-open-flag" id="vd-flag-btn"
                        style="display:none;background:#3263d1;color:#fff;" title="Manage Flags">
                    <i class="mdi mdi-flag-checkered"></i>
                </button>
                @can('task-health-visit-cancel')
                <button class="vd-action-btn vd-action-danger" id="vd-cancel-btn"
                        onclick="deleteVisitFromDrawer()" title="Cancel Visit">
                    <i class="fa fa-trash-o"></i>
                </button>
                @endcan
                <button class="vd-action-btn vd-action-close" onclick="closeVisitModal()" title="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="vd-tabs">
            <button class="vd-tab active" data-tab="general"       onclick="switchVisitTab('general',       this)">General</button>
            <button class="vd-tab"        data-tab="documents"     onclick="switchVisitTab('documents',     this)">Documents</button>
            @can('create-link-task-health')
            <button class="vd-tab"        data-tab="patientrecord" onclick="switchVisitTab('patientrecord', this); _vEnsureMasterPanelLoaded();" id="vd-tab-patientrecord">
                <span id="vd-pr-tab-label">Patient Record</span>
                <span id="vd-pr-tab-badge" style="display:none;margin-left:5px;"></span>
            </button>
            @endcan
        </div>

        {{-- Body --}}
        <div class="vd-body">
            <div class="vd-panel active" id="vt-general">
                <div id="vt-general-content"></div>
            </div>
            <div class="vd-panel" id="vt-documents">
                <div id="vt-documents-content"></div>
            </div>
            @can('create-link-task-health')
            <div class="vd-panel" id="vt-patientrecord">
                <div id="vt-patientrecord-content">
                    <div style="text-align:center;color:#9ca3af;padding:60px 20px;">
                        <i class="mdi mdi-link-variant" style="font-size:32px;"></i>
                        <p style="margin-top:8px;font-size:13px;">Loading patient record status…</p>
                    </div>
                </div>
            </div>
            @endcan
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     Edit Visit Modal
     ═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editVisitModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#343a40;color:#fff;padding:11px 18px;border-bottom:none;">
                <h5 class="modal-title" style="font-size:14px;font-weight:600;display:flex;align-items:center;gap:7px;">
                    <i class="fa fa-pencil"></i> Edit Visit — <span id="editModalTaskIdLabel"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.75;font-size:22px;">&times;</button>
            </div>
            <form id="editVisitForm">
                @csrf
                <input type="hidden" id="editVisitTaskId" name="taskId" value="">
                <div class="modal-body" style="padding:18px 20px;">

                    {{-- Instruction field --}}
                    <div class="form-group mb-2">
                        <label style="font-size:12px;font-weight:600;color:#343a40;text-transform:uppercase;letter-spacing:.3px;">
                            Instruction <span style="color:#dc3545;">*</span>
                        </label>
                        <textarea id="editInstruction" name="instruction" class="form-control"
                            rows="4" maxlength="2000"
                            placeholder="e.g. change address to 456 Atlantic Ave, Brooklyn NY 11217"
                            style="font-size:13px;resize:vertical;"></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted" style="font-size:11px;">Plain-English instruction, interpreted by AI</small>
                            <small id="editCharCount" class="text-muted" style="font-size:11px;">0 / 2000</small>
                        </div>
                    </div>

                    {{-- Example hints --}}
                    <div style="background:#f8f9fa;border-radius:4px;padding:10px 12px;border-left:3px solid #007bff;">
                        <p style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;text-transform:uppercase;letter-spacing:.3px;">Example instructions</p>
                        <ul style="font-size:12px;color:#666;margin:0;padding-left:16px;line-height:1.8;">
                            <li>change address to 456 Atlantic Ave, Brooklyn NY 11217</li>
                            <li>change due date to Feb 6</li>
                            <li>service type to HHA</li>
                            <li>frequency to 3x/week</li>
                            <li>add phone number 2127185555 and rebroadcast start March 5 due March 30</li>
                            <li>change caregiver to Maria Lopez 7185550200</li>
                        </ul>
                    </div>

                    {{-- Response area --}}
                    <div id="editVisitResponse" class="mt-3" style="display:none;"></div>

                </div>
                <div class="modal-footer" style="padding:10px 18px;background:#fff;border-top:1px solid #dee2e6;">
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-sm btn-warning" id="btn-submit-edit" style="color:#fff;">
                        <span id="btn-edit-text"><i class="fa fa-pencil"></i> Apply Edit</span>
                        <span id="btn-edit-spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Applying...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var _TH_CAN_LINK_MASTER       = {{ auth()->user()->can('create-link-task-health') ? 'true' : 'false' }};
    var _TH_VISIT_LIST_URL        = "{{ url('task-health/visit-ajax-list') }}";
    var _TH_AGENCIES_URL          = "{{ url('task-health/visit-agencies') }}";
    var _TH_VISIT_DETAIL_URL      = "{{ url('task-health/visit-detail') }}";
    var _TH_VISIT_DETAIL_JSON_URL = "{{ url('task-health/visit-detail-json') }}";
    var _TH_CHECK_MASTER_URL      = "{{ url('task-health/visit-check-master') }}";
    var _TH_CREATE_MASTER_URL     = "{{ url('task-health/visit-create-master') }}";
    var _TH_LOCAL_AGENCIES        = @json($localAgencies->map(fn($a) => ['id' => $a->id, 'name' => $a->agency_name]));
    var _TH_VISIT_CREATE_URL      = "{{ url('task-health/visit-create') }}";
    var _TH_VISIT_EDIT_URL        = "{{ url('task-health/visit-edit') }}";
    var _TH_DOC_APPROVE_URL       = "{{ url('task-health/visit-doc-approve') }}";
    var _TH_DOC_OPEN_CHANGES_URL  = "{{ url('task-health/visit-doc-open-changes') }}";
    var _CSRF_TOKEN               = "{{ csrf_token() }}";
    var _TH_DELETE_URL            = "{{ url('/task-health/visit-cancel') }}";
    var _TH_VISIT_EXPORT_URL      = "{{ url('task-health/visit-export-csv') }}";
    var _TH_FLAGS_BY_TASK_URL     = "{{ url('task-health-flags-by-task-id') }}";
    var _TH_HHA_PREVIEW_BY_TASK   = "{{ url('task-health/by-task') }}";
    var _TH_UPLOAD_DOC_BY_TASK    = "{{ url('task-health/by-task') }}";
    let canDocApprove             = @json(auth()->user()->can('task-health-visit-doc-approve'));
    let canDocChange              = @json(auth()->user()->can('task-health-visit-doc-change'));
    var _TH_PATIENT_SERVICES      = @json($patientServices->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));
</script>
{{-- ═══════════════════════════════════════════════════════
     Open for Changes Modal
     ═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="openForChangesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#e0a800;color:#fff;padding:11px 18px;border-bottom:none;">
                <h5 class="modal-title" style="font-size:14px;font-weight:600;display:flex;align-items:center;gap:7px;">
                    <i class="mdi mdi-undo-variant"></i> Open for Changes — <span id="ofc-doc-title"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.75;font-size:22px;">&times;</button>
            </div>
            <div class="modal-body" style="padding:18px 20px;">
                <p style="font-size:13px;color:#555;margin-bottom:12px;">
                    Provide at least one rejection reason explaining what needs to be corrected.
                    This will be sent directly to the caregiver.
                </p>
                <div id="ofc-rejections-container">
                    <div class="ofc-rejection-row mb-2">
                        <textarea class="form-control ofc-rejection-input" rows="2"
                            placeholder="e.g. Patient signature is missing on page 2"
                            style="font-size:13px;resize:vertical;"></textarea>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addRejectionRow()">
                    <i class="mdi mdi-plus"></i> Add Another Reason
                </button>
                <div id="ofc-response" class="mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer" style="padding:10px 18px;background:#fff;border-top:1px solid #dee2e6;">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                    <i class="mdi mdi-close"></i> Cancel
                </button>
                <button type="button" class="btn btn-sm btn-warning" id="btn-ofc-submit" onclick="submitOpenForChanges()" style="color:#fff;">
                    <span id="btn-ofc-submit-text"><i class="mdi mdi-undo-variant"></i> Open for Changes</span>
                    <span id="btn-ofc-spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     Supervision Confirm Modal
     ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="supervisionConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#2e7d32;color:#fff;padding:11px 18px;border-bottom:none;">
                <h5 class="modal-title" style="font-size:14px;font-weight:600;display:flex;align-items:center;gap:7px;">
                    <i class="mdi mdi-eye-check-outline"></i> Confirm Supervision
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.75;font-size:22px;">&times;</button>
            </div>
            <div class="modal-body" style="padding:18px 20px;">
                <input type="hidden" id="_svpTaskId">
                <input type="hidden" id="_svpPatientId">
                <p style="font-size:13px;color:#343a40;margin-bottom:6px;">
                    Are you sure you want to submit supervision?<br><br>
                    <strong>Task ID:</strong> <span id="svp-task-id"></span><br>
                    <strong>Patient ID:</strong> <span id="svp-patient-id"></span>
                </p>
                <div id="svp-response" class="alert mt-2" style="display:none;font-size:12px;padding:8px 12px;"></div>
            </div>
            <div class="modal-footer" style="padding:10px 18px;background:#fff;border-top:1px solid #dee2e6;">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                    <i class="mdi mdi-close"></i> Cancel
                </button>
                <button type="button" class="btn btn-sm btn-success" id="btn-svp-submit" onclick="submitSupervisionConfirm()">
                    <span id="btn-svp-submit-text"><i class="mdi mdi-check"></i> Yes, Submit</span>
                    <span id="btn-svp-spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('assets/modulejs/task_health_visit/task_health_visit.js') }}?time={{ time() }}"></script>
