@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" type="text/css" />

<style>
    .email-section {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .preview-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        max-height: 600px;
        overflow-y: auto;
        background: white;
    }

    .history-table {
        font-size: 14px;
    }

    .status-badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 500;
    }

    .status-sent {
        background-color: #d4edda;
        color: #155724;
    }

    .status-failed {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .form-group label {
        font-weight: 600;
        color: #495057;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 8px;
    }

    .card-header {
        background: linear-gradient(45deg, #4b79a1, #283e51);
        color: white;
        font-weight: 600;
        border-radius: 8px 8px 0 0;
        padding: 15px 20px;
    }

    .email-recipients-container {
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px;
        background-color: #f8f9fa;
    }

    .recipient-tag {
        display: inline-block;
        background: linear-gradient(45deg, #4b79a1, #283e51);
        color: white;
        padding: 6px 12px;
        margin: 3px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
    }

    .recipient-tag .mdi {
        margin-left: 5px;
        cursor: pointer;
    }

    .recipient-tag .mdi:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
    }

    .grid-margin {
        margin-bottom: 25px;
    }

    .page-title-main {
        margin-bottom: 25px;
    }

    .page-title-main h5 {
        color: #2c3e50;
        font-weight: 700;
    }

    .btn-fw {
        min-width: 120px;
    }

    .cust-right-btn {
        font-weight: 500;
        border-radius: 5px;
        padding: 8px 16px;
    }

    .horizontal-menu .custom-nav,
    .horizontal-menu .bottom-navbar .page-navigation {
        position: unset;
    }

    #emailTabsContent {
        text-align: left;
    }

    .form-check-inline {
        display: flex;
    }

    /* Toggle Switch Styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        min-width: 52px;
        height: 26px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 26px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    .toggle-switch input:checked+.toggle-slider {
        background-color: #007bff;
    }

    .toggle-switch input:focus+.toggle-slider {
        box-shadow: 0 0 1px #007bff;
    }

    .toggle-switch input:checked+.toggle-slider:before {
        transform: translateX(26px);
    }

    .toggle-group {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px 12px;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        background-color: #f8f9fa;
        gap: 0;
    }

    .toggle-group > div {
        flex: 1;
        min-width: 0;
    }

    .toggle-group:last-child {
        margin-bottom: 0;
    }

    .toggle-label {
        font-weight: 500;
        margin: 0;
        color: #495057;
        font-size: 13px;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .toggle-description {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .page-loader-text {
        font-size: 16px;
        font-weight: 500;
        margin-top: 10px;
    }

    .page-loader-subtext {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 5px;
    }

    .shimmer-loader {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite linear;
        border-radius: 4px;
        height: 10px;
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }

        100% {
            background-position: 200% 0;
        }
    }

    .small-box.shimmer {
        position: relative;
        /* Required for positioning the shimmer effect */
        overflow: hidden;
        /* Hide the shimmer effect outside the card */
    }

    .small-box.shimmer:before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.5) 50%,
                rgba(255, 255, 255, 0) 100%);
        animation: shimmerloaade 2s infinite;
        /* Adjust animation duration as needed */
    }

    @keyframes shimmerloaade {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">

        @canany(['detailed-refusals-report', 'referrals-analytics-dashboard-report', 'weekly-monthly-states-report'])
        @include('referralsWeight/reports-nav')
        @endcanany

        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 font-weight-bold">📧 Detailed Portal Charts Report</h4>
                    </div>
                    <p class="text-muted mb-0 mt-1">Automate and manage daily referral reports via email</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="card grid-margin">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold" id="emailTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="send-email-tab" data-toggle="tab" href="#send-email" role="tab"
                            aria-controls="send-email" aria-selected="true">
                            <i class="mdi mdi-email-send mr-2"></i>Send Email
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="schedule-tab" data-toggle="tab" href="#schedule" role="tab"
                            aria-controls="schedule" aria-selected="false">
                            <i class="mdi mdi-clock mr-2"></i>Schedule
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab"
                            aria-controls="history" aria-selected="false">
                            <i class="mdi mdi-history mr-2"></i>History
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="emailTabsContent">
            <!-- Send Email Tab -->
            <div class="tab-pane fade show active" id="send-email" role="tabpanel" aria-labelledby="send-email-tab">
                <div class="row">
                    <!-- Email Configuration Section -->
                    <div class="col-lg-8 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="mdi mdi-email-edit mr-2"></i>Create and Send Daily Report
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="emailForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reportDate"><i class="mdi mdi-calendar mr-1"></i>Report
                                                    Date</label>
                                                <input type="text" class="form-control" id="reportDate" value=""
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="emailSubject"><i class="mdi mdi-email-edit mr-1"></i>Email
                                                    Subject</label>
                                                <input type="text" class="form-control" id="emailSubject"
                                                    value="New Services Requested & Charts Created - {{ date('m/d/Y') }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filter Section -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="agencyFilter"><i
                                                        class="mdi mdi-office-building mr-1"></i>Filter by Agency
                                                    (Optional)</label>
                                                <select class="form-control select2" id="agencyFilter"
                                                    multiple="multiple" style="width: 100%;">
                                                    @foreach($agencies as $agency)
                                                    <option value="{{ $agency->id }}">{{ $agency->agency_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to include all agencies</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="serviceFilter"><i
                                                        class="mdi mdi-medical-bag mr-1"></i>Filter by Service
                                                    (Optional)</label>
                                                <select class="form-control select2" id="serviceFilter"
                                                    multiple="multiple" style="width: 100%;">
                                                    @foreach($services as $service)
                                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to include all services</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="assignedToFilter"><i
                                                        class="mdi mdi-account mr-1"></i>Assigned To
                                                    (Optional)</label>
                                                <select class="form-control select2" id="assignedToFilter"
                                                    multiple="multiple" style="width: 100%;">
                                                    @foreach($userList as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to include all users</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Filters Row -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="medicationListFilter"><i
                                                        class="mdi mdi-pill mr-1"></i>Medication List
                                                    (Optional)</label>
                                                <select class="form-control" id="medicationListFilter" style="width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="insuranceElgFilter"><i
                                                        class="mdi mdi-shield-check mr-1"></i>Insurance Elg
                                                    (Optional)</label>
                                                <select class="form-control" id="insuranceElgFilter" style="width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mdoTagFilter"><i
                                                        class="mdi mdi-tag mr-1"></i>Mdo Tag
                                                    (Optional)</label>
                                                <select class="form-control" id="mdoTagFilter" style="width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="branchFilter"><i
                                                        class="mdi mdi-source-branch mr-1"></i>Branch
                                                    (Optional)</label>
                                                <select class="form-control select2" id="branchFilter"
                                                    multiple="multiple" style="width: 100%;">
                                                    @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to include all branches</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Report Sections Toggle -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><i class="mdi mdi-toggle-switch mr-1"></i>Report Sections</label>
                                                <small class="text-muted d-block mb-2">Toggle sections on/off to customize the report content</small>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showFormsBreakdown" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🔄 Break down of the forms included in the new charts created
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showReferralSources" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🔄 Break down of where each of these referrals came from
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showResolution" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    ✅ The Resolution of each of those charts as of today
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showRequestsPerAgency" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🏢 New Requests Per Agency
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showPortalProcessing" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🔄 Portal Processings
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showOutliers" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    📈 Outliers Based on Portal Processing
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showHighestWeight" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🏆 Highest Weight of New Requests
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showRefusalsInsights" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    📊 Refusals Insights
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showCancellationsInsights" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🚫 Cancellations Insights
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showNonMdoForms" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    📋 Non-MDO Forms Completed Per Agency
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showMdoCompleted" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    ✍️ Total MDOs Completed Per Agency
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="showUpdatesPerAgency" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🔄 Updates Per Agency
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="recipients"><i class="mdi mdi-account-multiple mr-1"></i>Recipients
                                            (Required)</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="recipientInput"
                                                placeholder="Enter email address">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="addRecipient()">
                                                    <i class="mdi mdi-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                        <div id="recipientsContainer" class="email-recipients-container mt-2">
                                            <small class="text-muted">No recipients added yet</small>
                                        </div>
                                        <input type="hidden" id="recipients" name="recipients">
                                    </div>

                                    <div class="form-group">
                                        <label for="ccEmails"><i class="mdi mdi-email-plus mr-1"></i>CC Emails
                                            (Optional)</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="ccInput"
                                                placeholder="Enter CC email address">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    onclick="addCcEmail()">
                                                    <i class="mdi mdi-plus"></i> Add CC
                                                </button>
                                            </div>
                                        </div>
                                        <div id="ccContainer" class="email-recipients-container mt-2">
                                            <div class="recipient-tag">
                                                <i class="mdi mdi-shield-check mr-1"></i>pinak@nybestmedical.com
                                                <span class="text-muted">(Auto-included)</span>
                                            </div>
                                            <div class="recipient-tag">
                                                <i class="mdi mdi-shield-check mr-1"></i>developer@nybestmedical.com
                                                <span class="text-muted">(Auto-included)</span>
                                            </div>
                                            <div class="recipient-tag">
                                                <i class="mdi mdi-shield-check mr-1"></i>marina@nybestmedical.com
                                                <span class="text-muted">(Auto-included)</span>
                                            </div>
                                        </div>
                                        <input type="hidden" id="ccEmails" name="ccEmails">
                                    </div>

                                    <div class="d-flex justify-content-between mb-3">
                                        <button type="button" class="btn btn-outline-info btn-fw cust-right-btn"
                                            onclick="previewEmail()">
                                            <i class="mdi mdi-eye loading-icon"></i>
                                            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag"
                                                class="loading d-none" style="width:20px; height:20px;">
                                            Preview Email
                                        </button>
                                        <button type="button" class="btn btn-success btn-fw cust-right-btn ml-2"
                                            onclick="sendEmail()">
                                            <i class="mdi mdi-send loading-icon"></i>
                                            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag"
                                                class="loading d-none" style="width:20px; height:20px;">
                                            Send Email
                                        </button>
                                    </div>
                                </form>

                                <!-- Preview Container -->
                                <div id="previewContainer" class="preview-container" style="display: none;">
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">📧 Email Preview</h6>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="closePreview()">
                                                <i class="mdi mdi-close"></i> Close
                                            </button>
                                        </div>
                                        <iframe id="previewContent" style="width: 100%; height: 500px; border: 1px solid #dee2e6; border-radius: 8px;" sandbox="allow-same-origin"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats & Recent History -->
                    <div class="col-lg-4">
                        <div class="card mb-3 grid-margin stretch-card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="mdi mdi-chart-bar mr-2"></i>Quick Stats</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-email-check-outline text-success mr-2"
                                                style="font-size: 24px;"></i>
                                            <div>
                                                <h4 class="text-success mb-1" id="totalSentEmails">{{ $sent_count }}
                                                </h4>
                                                <small class="text-muted">Sent Today</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-email-alert-outline text-danger mr-2"
                                                style="font-size: 24px;"></i>
                                            <div>
                                                <h4 class="text-danger mb-1" id="totalFailedEmails">{{ $failed_count }}
                                                </h4>
                                                <small class="text-muted">Failed</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card grid-margin stretch-card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="mdi mdi-clock-outline mr-2"></i>Recent Email History</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm history-table mb-0">
                                        <tbody id="recentHistoryTable">
                                            @forelse($recent_emails as $email)
                                            <tr>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <small class="font-weight-bold">{{ $email->report_date
                                                                }}</small><br>
                                                            <small class="text-muted">{{ $email->created_date ?
                                                                \Carbon\Carbon::parse($email->created_date)->format('H:i')
                                                                : '' }}</small>
                                                        </div>
                                                        <span class="status-badge status-{{ $email->status }}">{{
                                                            ucfirst($email->status) }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td class="text-center text-muted py-3">
                                                    No emails sent yet
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email History Section -->
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="mdi mdi-email-outline mr-2"></i>Email History</h6>
                                    <div class="d-flex">
                                        <button
                                            class="btn form-control form-control-sm mr-2 btn-info btn-sm btn-fw cust-right-btn"
                                            onclick="refreshHistory()" style="border: none;     height: 32px;">
                                            <i class="mdi mdi-refresh"></i> Refresh History
                                        </button>
                                        <select class="form-control form-control-sm mr-2" id="statusFilter1"
                                            onchange="filterHistory(1)" style="min-width: 120px;">
                                            <option value="">All Status</option>
                                            <option value="sent">Sent</option>
                                            <option value="failed">Failed</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                        <input type="text" value=""
                                            class="form-control form-control-sm mr-2 email_range_date"
                                            id="email_range_date1" name="email_range_date" autocomplete="off"
                                            placeholder="Select date range" onchange="filterHistory(1)">

                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover history-table mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Report Date</th>
                                                <th>Subject</th>
                                                <th>Recipients</th>
                                                <th>Status</th>
                                                <th>Sent At</th>
                                                <th>Created By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historyTable">
                                            <!-- Shimmer loader rows -->
                                            <tr class="shimmer-row">
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Send Email Tab -->

            <!-- Schedule Tab -->
            <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                <div class="row">
                    <!-- Create Schedule Section -->
                    <div class="col-lg-8 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="mdi mdi-clock-outline mr-2"></i>Create Email Schedule</h6>
                            </div>
                            <div class="card-body">
                                <form id="scheduleForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="scheduleName"><i class="mdi mdi-tag mr-1"></i>Schedule
                                                    Name</label>
                                                <input type="text" class="form-control" id="scheduleName"
                                                    placeholder="e.g., Daily Report - Weekdays"
                                                    value="Daily Referral Report Schedule">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="scheduleSubject"><i class="mdi mdi-email mr-1"></i>Email
                                                    Subject</label>
                                                <input type="text" class="form-control" id="scheduleSubject"
                                                    placeholder="Daily Referral Report" value="Daily Referral Report"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sendTime"><i class="mdi mdi-clock mr-1"></i>Send
                                                    Time</label>
                                                <input type="time" class="form-control" id="sendTime" value="09:00"
                                                    required>
                                                <small class="text-muted">Time when emails should be sent</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="frequency"><i
                                                        class="mdi mdi-repeat mr-1"></i>Frequency</label>
                                                <select class="form-control" id="frequency" required
                                                    onchange="toggleFrequencyOptions()">
                                                    <option value="daily">Daily</option>
                                                    <option value="weekly">Weekly</option>
                                                    <option value="monthly">Monthly</option>
                                                </select>
                                                <small class="text-muted">How often to send the report</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Daily Options -->
                                    <div id="dailyOptions" class="frequency-options">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="mdi mdi-calendar-week mr-1"></i>Send on
                                                        Days</label>
                                                    <div class="mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input sendDays" type="checkbox"
                                                                id="monday" value="monday" checked>
                                                            <label class="form-check-label" for="monday">Mon</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input sendDays" type="checkbox"
                                                                id="tuesday" value="tuesday" checked>
                                                            <label class="form-check-label" for="tuesday">Tue</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input sendDays" type="checkbox"
                                                                id="wednesday" value="wednesday" checked>
                                                            <label class="form-check-label" for="wednesday">Wed</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input sendDays" type="checkbox"
                                                                id="thursday" value="thursday" checked>
                                                            <label class="form-check-label" for="thursday">Thu</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input sendDays" type="checkbox"
                                                                id="friday" value="friday" checked>
                                                            <label class="form-check-label" for="friday">Fri</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input sendDays" type="checkbox"
                                                                id="saturday" value="saturday">
                                                            <label class="form-check-label" for="saturday">Sat</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input sendDays" type="checkbox"
                                                                id="sunday" value="sunday">
                                                            <label class="form-check-label" for="sunday">Sun</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="periodDays"><i
                                                            class="mdi mdi-calendar-range mr-1"></i>Custom Period
                                                        (Optional)</label>
                                                    <input type="number" class="form-control" id="periodDays" min="1"
                                                        max="365" placeholder="e.g., 3 for every 3 days">
                                                    <small class="text-muted">Leave empty for daily, or specify days
                                                        between sends</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Weekly Options -->
                                    <div id="weeklyOptions" class="frequency-options" style="display: none;">
                                        <div class="form-group">
                                            <label for="weeklyDay"><i class="mdi mdi-calendar-week mr-1"></i>Send on
                                                Day</label>
                                            <select class="form-control" id="weeklyDay">
                                                <option value="monday">Monday</option>
                                                <option value="tuesday">Tuesday</option>
                                                <option value="wednesday">Wednesday</option>
                                                <option value="thursday">Thursday</option>
                                                <option value="friday">Friday</option>
                                                <option value="saturday">Saturday</option>
                                                <option value="sunday">Sunday</option>
                                            </select>
                                            <small class="text-muted">Day of the week to send weekly reports</small>
                                        </div>
                                    </div>

                                    <!-- Monthly Options -->
                                    <div id="monthlyOptions" class="frequency-options" style="display: none;">
                                        <div class="form-group">
                                            <label for="monthlyDate"><i class="mdi mdi-calendar-month mr-1"></i>Send on
                                                Date</label>
                                            <select class="form-control" id="monthlyDateType"
                                                onchange="toggleMonthlyDateInput()">
                                                <option value="specific">Specific Date</option>
                                                <option value="end_of_month">End of Month</option>
                                            </select>
                                            <div id="specificDateInput" class="mt-2">
                                                <input type="number" class="form-control" id="monthlyDate" min="1"
                                                    max="31" placeholder="e.g., 1 for 1st of month"
                                                    onchange="updateMonthlyDatePreview()">
                                                <small class="text-muted">Day of the month to send monthly reports
                                                    (1-31)</small>
                                            </div>
                                            <div id="endOfMonthInfo" class="mt-2" style="display: none;">
                                                <div class="alert alert-success p-2" style="font-size: 12px;">
                                                    <i class="mdi mdi-calendar-check mr-1"></i>
                                                    <strong>End of Month:</strong> Will automatically send on the last
                                                    day of each month (28th, 29th, 30th, or 31st depending on the month)
                                                </div>
                                            </div>
                                            <div id="monthlyDatePreview" class="alert alert-info mt-2 p-2"
                                                style="font-size: 12px; display: none;">
                                                <i class="mdi mdi-information-outline mr-1"></i>
                                                <span id="monthlyDateText"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="scheduleRecipients"><i
                                                class="mdi mdi-account-multiple mr-1"></i>Recipients (To)</label>
                                        <div class="recipients-container">
                                            <div class="input-group mb-2">
                                                <input type="email" class="form-control"
                                                    placeholder="Enter email address" id="scheduleRecipientInput">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="addScheduleRecipient()">
                                                        <i class="mdi mdi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="scheduleRecipientsList" class="recipients-list">
                                                <!-- Recipients will be added here -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="scheduleCcEmails"><i class="mdi mdi-email-multiple mr-1"></i>CC
                                            Recipients (Optional)</label>
                                        <div class="cc-container">
                                            <div class="input-group mb-2">
                                                <input type="email" class="form-control"
                                                    placeholder="Enter CC email address" id="scheduleCcInput">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-secondary"
                                                        onclick="addScheduleCc()">
                                                        <i class="mdi mdi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="scheduleCcList" class="recipients-list">
                                                <!-- CC recipients will be added here -->
                                            </div>
                                            <small class="text-muted">pinak@nybestmedical.com,
                                                developer@nybestmedical.com, and marina@nybestmedical.com are
                                                automatically included
                                                in CC</small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="startDate"><i class="mdi mdi-calendar-start mr-1"></i>Start
                                                    Date
                                                    (Optional)</label>
                                                <input type="date" class="form-control" id="scheduleStartDate">
                                                <small class="text-muted">Leave empty to start
                                                    immediately</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="endDate"><i class="mdi mdi-calendar-end mr-1"></i>End
                                                    Date (Optional)</label>
                                                <input type="date" class="form-control" id="scheduleEndDate">
                                                <small class="text-muted">Leave empty to run
                                                    indefinitely</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filter Section for Scheduling -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="scheduleAgencyFilter"><i
                                                        class="mdi mdi-office-building mr-1"></i>Filter by Agency
                                                    (Optional)</label>
                                                <select class="form-control select2" id="scheduleAgencyFilter"
                                                    multiple="multiple" style="width: 100%;">
                                                    @foreach($agencies as $agency)
                                                    <option value="{{ $agency->id }}">{{ $agency->agency_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to include all agencies</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="scheduleServiceFilter"><i
                                                        class="mdi mdi-medical-bag mr-1"></i>Filter by Service
                                                    (Optional)</label>
                                                <select class="form-control select2" id="scheduleServiceFilter"
                                                    multiple="multiple" style="width: 100%;">
                                                    @foreach($services as $service)
                                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to include all services</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="scheduleAssignedToFilter"><i
                                                        class="mdi mdi-account mr-1"></i>Assigned To
                                                    (Optional)</label>
                                                <select class="form-control select2" id="scheduleAssignedToFilter"
                                                    multiple="multiple" style="width: 100%;">
                                                    @foreach($userList as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to include all users</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Filters Row for Scheduling -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="scheduleMedicationListFilter"><i
                                                        class="mdi mdi-pill mr-1"></i>Medication List
                                                    (Optional)</label>
                                                <select class="form-control" id="scheduleMedicationListFilter" style="width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="scheduleInsuranceElgFilter"><i
                                                        class="mdi mdi-shield-check mr-1"></i>Insurance Elg
                                                    (Optional)</label>
                                                <select class="form-control" id="scheduleInsuranceElgFilter" style="width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="scheduleMdoTagFilter"><i
                                                        class="mdi mdi-tag mr-1"></i>Mdo Tag
                                                    (Optional)</label>
                                                <select class="form-control" id="scheduleMdoTagFilter" style="width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="scheduleBranchFilter"><i
                                                        class="mdi mdi-source-branch mr-1"></i>Branch
                                                    (Optional)</label>
                                                <select class="form-control select2" id="scheduleBranchFilter"
                                                    multiple="multiple" style="width: 100%;">
                                                    @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to include all branches</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Report Sections Toggle for Scheduling -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><i class="mdi mdi-toggle-switch mr-1"></i>Report Sections</label>
                                                <small class="text-muted d-block mb-2">Toggle sections on/off to customize the scheduled report content</small>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowFormsBreakdown" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🔄 Break down of the forms included in the new charts created
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowReferralSources" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🔄 Break down of where each of these referrals came from
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowResolution" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    ✅ The Resolution of each of those charts as of today
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowRequestsPerAgency" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🏢 New Requests Per Agency
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowPortalProcessing" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🔄 Portal Processings
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowOutliers" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    📈 Outliers Based on Portal Processing
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowHighestWeight" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🏆 Highest Weight of New Requests
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowRefusalsInsights" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    📊 Refusals Insights
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowCancellationsInsights" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🚫 Cancellations Insights
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowNonMdoForms" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    📋 Non-MDO Forms Completed Per Agency
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowMdoCompleted" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    ✍️ Total MDOs Completed Per Agency
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" id="scheduleShowUpdatesPerAgency" checked>
                                                                <span class="toggle-slider"></span>
                                                            </label>
                                                            <div>
                                                                <div class="toggle-label">
                                                                    🔄 Updates Per Agency
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="scheduleNotes"><i class="mdi mdi-note-text mr-1"></i>Notes
                                            (Optional)</label>
                                        <textarea class="form-control" id="scheduleNotes" rows="3"
                                            placeholder="Add any notes about this schedule..."></textarea>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-light mr-2" onclick="resetScheduleForm()">
                                            <i class="mdi mdi-refresh"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-fw cust-right-btn">
                                            <i class="mdi mdi-clock-plus"></i> Create Schedule
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Schedules List Section -->
                    <div class="col-lg-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="mdi mdi-clock-outline mr-2"></i>Active Schedules</h6>
                                    <button class="btn btn-sm btn-outline-primary" onclick="loadSchedules()">
                                        <i class="mdi mdi-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="schedulesList">
                                    <!-- Shimmer loader for schedules -->
                                    <div class="schedule-shimmer-loader">
                                        <div class="schedule-item border-bottom p-3">
                                            <div class="shimmer-loader"
                                                style="height: 20px; width: 70%; margin-bottom: 10px;"></div>
                                            <div class="shimmer-loader"
                                                style="height: 15px; width: 50%; margin-bottom: 10px;"></div>
                                            <div class="shimmer-loader" style="height: 15px; width: 80%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Schedule Tab -->

            <!-- History Tab -->
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <div class="row">
                    <!-- History content moved here from the original location -->
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="mdi mdi-email-outline mr-2"></i>Email History</h6>
                                    <div class="d-flex">
                                        <button
                                            class="btn form-control form-control-sm mr-2 btn-info btn-sm btn-fw cust-right-btn"
                                            onclick="refreshHistory()" style="border: none;     height: 32px;">
                                            <i class="mdi mdi-refresh"></i> Refresh History
                                        </button>
                                        <select class="form-control form-control-sm mr-2" id="statusFilter"
                                            onchange="filterHistory()" style="min-width: 120px;">
                                            <option value="">All Status</option>
                                            <option value="sent">Sent</option>
                                            <option value="failed">Failed</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                        <input type="text" value=""
                                            class="form-control form-control-sm mr-2 email_range_date"
                                            id="email_range_date" name="email_range_date" autocomplete="off"
                                            placeholder="Select date range" onchange="filterHistory()">

                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover history-table mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Report Date</th>
                                                <th>Subject</th>
                                                <th>Recipients</th>
                                                <th>Status</th>
                                                <th>Sent At</th>
                                                <th>Created By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historyTable2">
                                            <!-- Shimmer loader rows -->
                                            <tr class="shimmer-row">
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                                <td>
                                                    <div class="shimmer-loader" style="height: 20px;"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End History Tab -->
        </div>
        <!-- End Tab Content -->
    </div>
</div>

@include('include/footer')

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>

<script>
    let recipients = [];
let ccEmails = ['pinak@nybestmedical.com', 'developer@nybestmedical.com', 'marina@nybestmedical.com'];

// Scheduling variables
let scheduleRecipients = [];
let scheduleCcEmails = [];

$(document).ready(function() {
    refreshHistory();
    updateRecipientDisplay();
    updateCcDisplay();
    loadSchedules();

    // Initialize Select2 for filter dropdowns
    $('#agencyFilter').select2({
        placeholder: 'Select agencies...',
        allowClear: true,
        theme: 'bootstrap'
    });

    $('#serviceFilter').select2({
        placeholder: 'Select services...',
        allowClear: true,
        theme: 'bootstrap'
    });

    // Initialize Select2 for schedule filter dropdowns
    $('#scheduleAgencyFilter').select2({
        placeholder: 'Select agencies...',
        allowClear: true,
        theme: 'bootstrap'
    });

    $('#scheduleServiceFilter').select2({
        placeholder: 'Select services...',
        allowClear: true,
        theme: 'bootstrap'
    });

     $('#assignedToFilter').select2({
        placeholder: 'Select assignees...',
        allowClear: true,
        theme: 'bootstrap'
    });
    
    $('#scheduleAssignedToFilter').select2({
        placeholder: 'Select assignees...',
        allowClear: true,
        theme: 'bootstrap'
    });

    $('#branchFilter').select2({
        placeholder: 'Select branches...',
        allowClear: true,
        theme: 'bootstrap'
    });

    $('#scheduleBranchFilter').select2({
        placeholder: 'Select branches...',
        allowClear: true,
        theme: 'bootstrap'
    });
});

function addRecipient() {
    const email = $('#recipientInput').val().trim();
    if (email && isValidEmail(email) && !recipients.includes(email)) {
        recipients.push(email);
        $('#recipientInput').val('');
        updateRecipientDisplay();
        updateRecipientsInput();
    } else {
        
         toastr.error('Please enter a valid email address that is not already added.');
    }
}

function addCcEmail() {
    const email = $('#ccInput').val().trim();
    if (email && isValidEmail(email) && !ccEmails.includes(email)) {
        ccEmails.push(email);
        $('#ccInput').val('');
        updateCcDisplay();
        updateCcInput();
    } else {
         toastr.error('Please enter a valid email address that is not already added.');
    }
}

function removeRecipient(email) {
    recipients = recipients.filter(r => r !== email);
    updateRecipientDisplay();
    updateRecipientsInput();
}

function removeCcEmail(email) {
    const defaultEmails = ['pinak@nybestmedical.com', 'developer@nybestmedical.com', 'marina@nybestmedical.com'];
    if (defaultEmails.includes(email)) {
        toastr.error('This email is required and cannot be removed.');
        return;
    }
    ccEmails = ccEmails.filter(c => c !== email);
    updateCcDisplay();
    updateCcInput();
}

function updateRecipientDisplay() {
    const container = $('#recipientsContainer');
    if (recipients.length === 0) {
        container.html('<small class="text-muted"><i class="mdi mdi-information-outline mr-1"></i>No recipients added yet</small>');
    } else {
        const tags = recipients.map(email =>
            `<span class="recipient-tag">
                <i class="mdi mdi-email-outline mr-1"></i>${email}
                <i class="mdi mdi-close-circle" onclick="removeRecipient('${email}')" style="cursor: pointer; margin-left: 5px;"></i>
            </span>`
        ).join('');
        container.html(tags);
    }
}

function updateCcDisplay() {
    const container = $('#ccContainer');
    const defaultEmails = ['pinak@nybestmedical.com', 'developer@nybestmedical.com', 'marina@nybestmedical.com'];
    const tags = ccEmails.map(email => {
        const isRequired = defaultEmails.includes(email);
        if (isRequired) {
            return `<span class="recipient-tag">
                <i class="mdi mdi-shield-check mr-1"></i>${email}
                <small class="text-light ml-1">(Auto-included)</small>
            </span>`;
        } else {
            return `<span class="recipient-tag">
                <i class="mdi mdi-email-plus-outline mr-1"></i>${email}
                <i class="mdi mdi-close-circle" onclick="removeCcEmail('${email}')" style="cursor: pointer; margin-left: 5px;"></i>
            </span>`;
        }
    }).join('');
    container.html(tags);
}

function updateRecipientsInput() {
    $('#recipients').val(JSON.stringify(recipients));
}

function updateCcInput() {
    $('#ccEmails').val(JSON.stringify(ccEmails));
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Allow Enter key to add recipient
$('#recipientInput').on('keypress', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        addRecipient();
    }
});

$('#ccInput').on('keypress', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        addCcEmail();
    }
});

function previewEmail() {
    if (recipients.length === 0) {
        toastr.error('Please add at least one recipient.');
        return;
    }

    const reportDate = $('#reportDate').val();
    if (!reportDate) {
        toastr.error('Please select a report date.');
        return;
    }

    // Show full page loader
    showLoading('.btn-outline-info');

    // Get filter values
    const agencyIds = $('#agencyFilter').val() || [];
    const serviceIds = $('#serviceFilter').val() || [];
    const assignedToIds = $('#assignedToFilter').val() || [];
    const medicationList = $('#medicationListFilter').val() || '';
    const insuranceElg = $('#insuranceElgFilter').val() || '';
    const mdoTag = $('#mdoTagFilter').val() || '';
    const branchIds = $('#branchFilter').val() || [];

    // Get section toggle values
    const sectionToggles = {
        show_forms_breakdown: $('#showFormsBreakdown').is(':checked'),
        show_referral_sources: $('#showReferralSources').is(':checked'),
        show_resolution: $('#showResolution').is(':checked'),
        show_requests_per_agency: $('#showRequestsPerAgency').is(':checked'),
        show_portal_processing: $('#showPortalProcessing').is(':checked'),
        show_outliers: $('#showOutliers').is(':checked'),
        show_highest_weight: $('#showHighestWeight').is(':checked'),
        show_refusals_insights: $('#showRefusalsInsights').is(':checked'),
        show_cancellations_insights: $('#showCancellationsInsights').is(':checked'),
        show_non_mdo_forms: $('#showNonMdoForms').is(':checked'),
        show_mdo_completed: $('#showMdoCompleted').is(':checked'),
        show_updates_per_agency: $('#showUpdatesPerAgency').is(':checked')
    };

    $.ajax({
        url: '{{ route("daily-referral-email.preview") }}',
        method: 'POST',
        data: {
            report_date: reportDate,
            agency_ids: agencyIds,
            service_ids: serviceIds,
            assigned_to: assignedToIds,
            medication_list: medicationList,
            insurance_elg: insuranceElg,
            mdo_tag: mdoTag,
            branch_ids: branchIds,
            ...sectionToggles,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                var iframe = document.getElementById('previewContent');
                var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                iframeDoc.open();
                iframeDoc.write(response.email_content);
                iframeDoc.close();
                $('#previewContainer').show();
                $('html, body').animate({
                    scrollTop: $('#previewContainer').offset().top - 100
                }, 500);
            } else {
                toastr.error('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            toastr.error('Error: ' + (response?.message || 'Failed to generate preview'));
        },
        complete: function() {
            // Hide full page loader
            hideLoading('.btn-outline-info');
        }
    });
}

function sendEmail() {
    if (recipients.length === 0) {
        toastr.error('Please add at least one recipient.');
        return;
    }

    const reportDate = $('#reportDate').val();
    const subject = $('#emailSubject').val();

    if (!reportDate || !subject) {
        toastr.error('Please fill in all required fields.');
        return;
    }

    // Get filter values
    const agencyIds = $('#agencyFilter').val() || [];
    const serviceIds = $('#serviceFilter').val() || [];
    const assignedToIds = $('#assignedToFilter').val() || [];
    const medicationList = $('#medicationListFilter').val() || '';
    const insuranceElg = $('#insuranceElgFilter').val() || '';
    const mdoTag = $('#mdoTagFilter').val() || '';
    const branchIds = $('#branchFilter').val() || [];

    // Get section toggle values
    const sectionToggles = {
        show_forms_breakdown: $('#showFormsBreakdown').is(':checked'),
        show_referral_sources: $('#showReferralSources').is(':checked'),
        show_resolution: $('#showResolution').is(':checked'),
        show_requests_per_agency: $('#showRequestsPerAgency').is(':checked'),
        show_portal_processing: $('#showPortalProcessing').is(':checked'),
        show_outliers: $('#showOutliers').is(':checked'),
        show_highest_weight: $('#showHighestWeight').is(':checked'),
        show_refusals_insights: $('#showRefusalsInsights').is(':checked'),
        show_cancellations_insights: $('#showCancellationsInsights').is(':checked'),
        show_non_mdo_forms: $('#showNonMdoForms').is(':checked'),
        show_mdo_completed: $('#showMdoCompleted').is(':checked'),
        show_updates_per_agency: $('#showUpdatesPerAgency').is(':checked')
    };

    // Count hidden sections
    const hiddenSections = Object.entries(sectionToggles).filter(([key, value]) => !value);

    // Create confirmation message with filter info
    let confirmMessage = `Are you sure you want to send this email to ${recipients.length} recipient(s)?`;
    if (agencyIds.length > 0 || serviceIds.length > 0 || assignedToIds.length > 0 || medicationList || insuranceElg || mdoTag || branchIds.length > 0 || hiddenSections.length > 0) {
        confirmMessage += '<br><br><strong>Settings applied:</strong><br>';
        if (agencyIds.length > 0) {
            confirmMessage += `- Agencies: ${agencyIds.length} selected<br>`;
        }
        if (serviceIds.length > 0) {
            confirmMessage += `- Services: ${serviceIds.length} selected<br>`;
        }
        if (assignedToIds.length > 0) {
            confirmMessage += `- Assigned To: ${assignedToIds.length} selected<br>`;
        }
        if (medicationList) {
            confirmMessage += `- Medication List: ${medicationList}<br>`;
        }
        if (insuranceElg) {
            confirmMessage += `- Insurance Elg: ${insuranceElg}<br>`;
        }
        if (mdoTag) {
            confirmMessage += `- Mdo Tag: ${mdoTag}<br>`;
        }
        if (branchIds.length > 0) {
            confirmMessage += `- Branch: ${branchIds.length} selected<br>`;
        }
        if (hiddenSections.length > 0) {
            confirmMessage += `- Hidden sections: ${hiddenSections.length}<br>`;
        }
    }

    // Show jQuery confirmation dialog
    $.confirm({
        title: 'Send Email',
        columnClass: 'col-md-6',
        content: confirmMessage,
        buttons: {
            confirm: {
                text: 'Send',
                btnClass: 'btn-success',
                action: function() {
                    // Show full page loader
                    
                    showLoading('.btn-success');

                    $.ajax({
                        url: '{{ route("daily-referral-email.send") }}',
                        method: 'POST',
                        data: {
                            report_date: reportDate,
                            subject: subject,
                            recipients: recipients,
                            cc_emails: ccEmails.filter(email => email !== 'pinak@nybestmedical.com'),
                            agency_ids: agencyIds,
                            service_ids: serviceIds,
                            assigned_to: assignedToIds,
                            medication_list: medicationList,
                            insurance_elg: insuranceElg,
                            mdo_tag: mdoTag,
                            branch_ids: branchIds,
                            ...sectionToggles,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Email sent successfully!');
                                refreshHistory();
                                closePreview();
                                // Reset form
                                recipients = [];
                                updateRecipientDisplay();
                                updateRecipientsInput();
                            } else {
                                toastr.error('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            toastr.error('Error: ' + (response?.message || 'Failed to send email'));
                        },
                        complete: function() {
                            // Hide full page loader
                            hideLoading('.btn-success');
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                action: function() {
                    // Do nothing
                }
            }
        }
    });
}

function closePreview() {
    $('#previewContainer').hide();
}

function showLoading(selector) {
    $(selector).find('.loading-icon').addClass('d-none');
    $(selector).find('.loading').removeClass('d-none');
    $(selector).prop('disabled', true);
}

function hideLoading(selector) {
    $(selector).find('.loading-icon').removeClass('d-none');
    $(selector).find('.loading').addClass('d-none');
    $(selector).prop('disabled', false);
}

function refreshHistory(id="") {
    // Show shimmer loader
    showShimmerLoader();
    const tbody = $('#historyTable');
    const tbody2 = $('#historyTable2');
     tbody.find('tr:not(.shimmer-row)').remove();
    tbody2.find('tr:not(.shimmer-row)').remove();

    $.ajax({
        url: '{{ route("daily-referral-email.history") }}',
        method: 'GET',
        data: {
            start_date: $('#email_range_date' + id).val(),
            status: $('#statusFilter' + id).val()
        },
        success: function(response) {
            if (response.success) {
                updateHistoryTable(response.data.data);
                updateQuickStats(response.data.data);
            }
        },
        error: function(xhr) {
            console.error('Error fetching history:', xhr);
            hideShimmerLoader();
        }
    });
}

function showShimmerLoader() {
    $('.shimmer-row').show();
}

function hideShimmerLoader() {
    $('.shimmer-row').hide();
}

function filterHistory(id="") {
    refreshHistory(id);
}

function updateHistoryTable(emailLogs) {
    const tbody = $('#historyTable');
    const tbody2 = $('#historyTable2');

    // Hide shimmer loader
    hideShimmerLoader();

    // Remove all non-shimmer rows
    tbody.find('tr:not(.shimmer-row)').remove();
    tbody2.find('tr:not(.shimmer-row)').remove();

    if (emailLogs.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center text-muted py-3">No email history found</td>
            </tr>
        `);
         tbody2.append(`
            <tr>
                <td colspan="7" class="text-center text-muted py-3">No email history found</td>
            </tr>
        `);
        return;
    }

    emailLogs.forEach(function(log) {
       
        const statusClass = 'status-' + log.status;
        const sentAt = log.sent_at ? new Date(log.sent_at).toLocaleString() : '-';
        const createdBy = log.created_by ? (log.created_by.first_name + ' ' + log.created_by.last_name) : '-';
        const recipients = Array.isArray(log.email_recipients) ? log.email_recipients.slice(0, 2).join(', ') + (log.email_recipients.length > 2 ? '...' : '') : '-';

        tbody.append(`
            <tr>
                <td>${log.report_date}</td>
                <td>${log.email_subject}</td>
                <td title="${Array.isArray(log.email_recipients) ? log.email_recipients.join(', ') : ''}">${recipients}</td>
                <td><span class="status-badge ${statusClass}">${log.status.charAt(0).toUpperCase() + log.status.slice(1)}</span></td>
                <td>${sentAt}</td>
                <td>${createdBy}</td>
                <td>
                    <button class="btn btn-sm btn-outline-info cust-right-btn" onclick="viewEmailLog(${log.id})" title="View Details">
                        <i class="mdi mdi-eye"></i>
                    </button>
                    ${log.status === 'sent' ? `<button class="btn btn-sm btn-outline-warning cust-right-btn ml-1" onclick="resendEmail(${log.id})" title="Resend">
                        <i class="mdi mdi-send"></i>
                    </button>` : ''}
                </td>
            </tr>
        `);
        tbody2.append(`
            <tr>
                <td>${log.report_date}</td>
                <td>${log.email_subject}</td>
                <td title="${Array.isArray(log.email_recipients) ? log.email_recipients.join(', ') : ''}">${recipients}</td>
                <td><span class="status-badge ${statusClass}">${log.status.charAt(0).toUpperCase() + log.status.slice(1)}</span></td>
                <td>${sentAt}</td>
                <td>${createdBy}</td>
                <td>
                    <button class="btn btn-sm btn-outline-info cust-right-btn" onclick="viewEmailLog(${log.id})" title="View Details">
                        <i class="mdi mdi-eye"></i>
                    </button>
                    ${log.status === 'sent' ? `<button class="btn btn-sm btn-outline-warning cust-right-btn ml-1" onclick="resendEmail(${log.id})" title="Resend">
                        <i class="mdi mdi-send"></i>
                    </button>` : ''}
                </td>
            </tr>
        `);
    });
}

function updateQuickStats(emailLogs) {
    const today = new Date().toISOString().split('T')[0];
   const todayEmails = emailLogs.filter(log => {
    const logDate = log.created_date.split(' ')[0];
    return logDate === today;
    });

    $('#totalSentEmails').text(todayEmails.filter(log => log.status === 'sent').length);
    $('#totalFailedEmails').text(todayEmails.filter(log => log.status === 'failed').length);
}

function viewEmailLog(id) {
    $.ajax({
        url: `/daily-referral-email/view/${id}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Show email log details in a modal or new window
                const data = response.data;
                const modalHtml = `
                    <div class="modal fade" id="emailLogModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header" style="background: linear-gradient(45deg, #4b79a1, #283e51); color: white;">
                                    <h5 class="modal-title"><i class="mdi mdi-email-open mr-2"></i>Email Log Details</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong><i class="mdi mdi-calendar mr-1"></i>Report Date:</strong> ${data.report_date}
                                        </div>
                                        <div class="col-md-6">
                                            <strong><i class="mdi mdi-check-circle mr-1"></i>Status:</strong>
                                            <span class="status-badge status-${data.status}">${data.status}</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <strong><i class="mdi mdi-subject mr-1"></i>Subject:</strong> ${data.email_subject}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <strong><i class="mdi mdi-account-multiple mr-1"></i>Recipients:</strong>
                                            ${Array.isArray(data.email_recipients) ? data.email_recipients.join(', ') : data.email_recipients}
                                        </div>
                                    </div>
                                    ${data.sent_at ? `<div class="row mb-3"><div class="col-md-12"><strong><i class="mdi mdi-clock mr-1"></i>Sent At:</strong> ${new Date(data.sent_at).toLocaleString()}</div></div>` : ''}
                                    ${data.error_message ? `<div class="row mb-3"><div class="col-md-12"><strong><i class="mdi mdi-alert mr-1"></i>Error:</strong> <span class="text-danger">${data.error_message}</span></div></div>` : ''}
                                    <div class="mt-3">
                                        <strong><i class="mdi mdi-email-outline mr-1"></i>Email Content Preview:</strong>
                                        <iframe id="emailContentIframe" class="mt-2" style="width: 100%; height: 500px; border: 1px solid #dee2e6; border-radius: 8px;" sandbox="allow-same-origin"></iframe>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-fw cust-right-btn" data-dismiss="modal">
                                        <i class="mdi mdi-close mr-1"></i>Close
                                    </button>
                                    ${data.status === 'sent' ? `<button type="button" class="btn btn-warning btn-fw cust-right-btn" onclick="resendEmail(${data.id})">
                                        <i class="mdi mdi-send mr-1"></i>Resend
                                    </button>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal and add new one
                $('#emailLogModal').remove();
                $('body').append(modalHtml);
                $('#emailLogModal').modal('show');

                // Write email content into iframe after modal is shown
                $('#emailLogModal').on('shown.bs.modal', function() {
                    var iframe = document.getElementById('emailContentIframe');
                    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    iframeDoc.open();
                    iframeDoc.write(data.email_content);
                    iframeDoc.close();
                });
            }
        },
        error: function(xhr) {
            toastr.error('Error fetching email details');
        }
    });
}

function resendEmail(id) {
    $.confirm({
        title: 'Resend Email',
        columnClass: 'col-md-6',
        content: 'Are you sure you want to resend this email?',
        buttons: {
            resend: {
                text: 'Resend',
                btnClass: 'btn-warning',
                action: function() {
                    $.ajax({
                        url: `/daily-referral-email/resend/${id}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Email resent successfully!');
                                refreshHistory();
                                $('#emailLogModal').modal('hide');
                            } else {
                                toastr.error('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            toastr.error('Error: ' + (response?.message || 'Failed to resend email'));
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                action: function() {
                    // Do nothing
                }
            }
        }
    });
}

// ===== SCHEDULING FUNCTIONS =====

// Toggle frequency options visibility
function toggleFrequencyOptions() {
    const frequency = $('#frequency').val();

    // Hide all frequency options
    $('.frequency-options').hide();

    // Show relevant options based on frequency
    switch (frequency) {
        case 'daily':
            $('#dailyOptions').show();
            break;
        case 'weekly':
            $('#weeklyOptions').show();
            break;
        case 'monthly':
            $('#monthlyOptions').show();
            updateMonthlyDatePreview();
            break;
    }
}

// Toggle monthly date input based on selection
function toggleMonthlyDateInput() {
    const dateType = $('#monthlyDateType').val();
    const specificInput = $('#specificDateInput');
    const endOfMonthInfo = $('#endOfMonthInfo');
    const preview = $('#monthlyDatePreview');

    if (dateType === 'end_of_month') {
        specificInput.hide();
        endOfMonthInfo.show();
        preview.hide();
        // Clear the monthly date value when end of month is selected
        $('#monthlyDate').val('');
    } else {
        specificInput.show();
        endOfMonthInfo.hide();
        updateMonthlyDatePreview();
    }
}

// Update monthly date preview
function updateMonthlyDatePreview() {
    const monthlyDate = parseInt($('#monthlyDate').val());
    const preview = $('#monthlyDatePreview');
    const text = $('#monthlyDateText');

    if (!monthlyDate || monthlyDate < 1 || monthlyDate > 31) {
        preview.hide();
        return;
    }

    if (monthlyDate <= 28) {
        preview.hide();
        return;
    }

    let previewText = '';

    if (monthlyDate === 29) {
        previewText = '<strong>Will send on:</strong> 29th of most months, Feb 28th in non-leap years';
    } else if (monthlyDate === 30) {
        previewText = '<strong>Will send on:</strong> 30th of most months, Feb 28/29th';
    } else if (monthlyDate === 31) {
        previewText = '<strong>Will send on:</strong> 31st when available, otherwise last day of month (Feb 28/29, Apr/Jun/Sep/Nov 30th)';
    }

    text.html(previewText);
    preview.show();
}

// Scheduling recipient management
function addScheduleRecipient() {
    const email = $('#scheduleRecipientInput').val().trim();
    if (email && isValidEmail(email) && !scheduleRecipients.includes(email)) {
        scheduleRecipients.push(email);
        $('#scheduleRecipientInput').val('');
        updateScheduleRecipientDisplay();
    } else {
        toastr.error('Please enter a valid email address that is not already added.');
    }
}

function addScheduleCc() {
    const email = $('#scheduleCcInput').val().trim();
    if (email && isValidEmail(email) && !scheduleCcEmails.includes(email)) {
        scheduleCcEmails.push(email);
        $('#scheduleCcInput').val('');
        updateScheduleCcDisplay();
    } else {
        toastr.error('Please enter a valid email address that is not already added.');
    }
}

function removeScheduleRecipient(email) {
    scheduleRecipients = scheduleRecipients.filter(r => r !== email);
    updateScheduleRecipientDisplay();
}

function removeScheduleCc(email) {
    scheduleCcEmails = scheduleCcEmails.filter(cc => cc !== email);
    updateScheduleCcDisplay();
}

function updateScheduleRecipientDisplay() {
    const container = $('#scheduleRecipientsList');
    container.empty();

    scheduleRecipients.forEach(email => {
        container.append(`
            <span class="badge badge-primary mr-2 mb-2 p-2">
                <i class="mdi mdi-email mr-1"></i>${email}
                <button type="button" class="btn btn-sm ml-2 p-0" onclick="removeScheduleRecipient('${email}')" style="background: none; border: none; color: white;">
                    <i class="mdi mdi-close"></i>
                </button>
            </span>
        `);
    });
}

function updateScheduleCcDisplay() {
    const container = $('#scheduleCcList');
    container.empty();

    scheduleCcEmails.forEach(email => {
        container.append(`
            <span class="badge badge-secondary mr-2 mb-2 p-2">
                <i class="mdi mdi-email mr-1"></i>${email}
                <button type="button" class="btn btn-sm ml-2 p-0" onclick="removeScheduleCc('${email}')" style="background: none; border: none; color: white;">
                    <i class="mdi mdi-close"></i>
                </button>
            </span>
        `);
    });
}

// Schedule form handling
$('#scheduleForm').on('submit', function(e) {
    e.preventDefault();
    createSchedule();
});

function createSchedule() {
    if (scheduleRecipients.length === 0) {
        toastr.error('Please add at least one recipient.');
        return;
    }

    const frequency = $('#frequency').val();
    let sendDays = [];
    let periodDays = null;
    let weeklyDay = null;
    let monthlyDate = null;

    // Validate and collect frequency-specific data
    if (frequency === 'daily') {
       $('.sendDays:checked').each(function() {
            sendDays.push($(this).val());
        });

        if (sendDays.length === 0) {
            toastr.error('Please select at least one day to send emails.');
            return;
        }

        periodDays = $('#periodDays').val() || null;
    } else if (frequency === 'weekly') {
        weeklyDay = $('#weeklyDay').val();
        if (!weeklyDay) {
            toastr.error('Please select a day for weekly frequency.');
            return;
        }
    } else if (frequency === 'monthly') {
        const monthlyDateType = $('#monthlyDateType').val();
        if (monthlyDateType === 'end_of_month') {
            monthlyDate = -1; // Use -1 to indicate end of month
        } else {
            monthlyDate = $('#monthlyDate').val();
            if (!monthlyDate) {
                toastr.error('Please select a date for monthly frequency.');
                return;
            }
        }
    }

    // Get filter values
    const scheduleAgencyIds = $('#scheduleAgencyFilter').val() || [];
    const scheduleServiceIds = $('#scheduleServiceFilter').val() || [];
    const scheduleAssignedToIds = $('#scheduleAssignedToFilter').val() || [];
    const scheduleMedicationList = $('#scheduleMedicationListFilter').val() || '';
    const scheduleInsuranceElg = $('#scheduleInsuranceElgFilter').val() || '';
    const scheduleMdoTag = $('#scheduleMdoTagFilter').val() || '';
    const scheduleBranchIds = $('#scheduleBranchFilter').val() || [];

    // Get section toggle values for schedule
    const scheduleSectionToggles = {
        show_forms_breakdown: $('#scheduleShowFormsBreakdown').is(':checked'),
        show_referral_sources: $('#scheduleShowReferralSources').is(':checked'),
        show_resolution: $('#scheduleShowResolution').is(':checked'),
        show_requests_per_agency: $('#scheduleShowRequestsPerAgency').is(':checked'),
        show_portal_processing: $('#scheduleShowPortalProcessing').is(':checked'),
        show_outliers: $('#scheduleShowOutliers').is(':checked'),
        show_highest_weight: $('#scheduleShowHighestWeight').is(':checked'),
        show_refusals_insights: $('#scheduleShowRefusalsInsights').is(':checked'),
        show_cancellations_insights: $('#scheduleShowCancellationsInsights').is(':checked'),
        show_non_mdo_forms: $('#scheduleShowNonMdoForms').is(':checked'),
        show_mdo_completed: $('#scheduleShowMdoCompleted').is(':checked'),
        show_updates_per_agency: $('#scheduleShowUpdatesPerAgency').is(':checked')
    };

    const formData = {
        name: $('#scheduleName').val(),
        email_subject: $('#scheduleSubject').val(),
        send_time: $('#sendTime').val(),
        frequency: frequency,
        send_days: sendDays.length > 0 ? sendDays : null,
        period_days: periodDays,
        weekly_day: weeklyDay,
        monthly_date: monthlyDate,
        recipients: scheduleRecipients,
        cc_emails: scheduleCcEmails,
        start_date: $('#scheduleStartDate').val() || null,
        end_date: $('#scheduleEndDate').val() || null,
        notes: $('#scheduleNotes').val(),
        agency_ids: scheduleAgencyIds,
        service_ids: scheduleServiceIds,
        assigned_to: scheduleAssignedToIds,
        medication_list: scheduleMedicationList,
        insurance_elg: scheduleInsuranceElg,
        mdo_tag: scheduleMdoTag,
        branch_ids: scheduleBranchIds,
        ...scheduleSectionToggles,
        _token: '{{ csrf_token() }}'
    };

    $.ajax({
        url: '{{ route("daily-referral-email.schedule") }}',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                toastr.success('Schedule created successfully!');
                resetScheduleForm();
                loadSchedules();
            } else {
                toastr.error('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response?.errors) {
                const errors = Object.values(response.errors).flat();
                toastr.error('Validation errors:\n' + errors.join('\n'));
            } else {
                toastr.error('Error: ' + (response?.message || 'Failed to create schedule'));
            }
        }
    });
}

function resetScheduleForm() {
    $('#scheduleForm')[0].reset();
    scheduleRecipients = [];
    scheduleCcEmails = [];
    updateScheduleRecipientDisplay();
    updateScheduleCcDisplay();

    // Reset default values
    $('#scheduleName').val('Daily Referral Report Schedule');
    $('#scheduleSubject').val('Daily Referral Report');
    $('#sendTime').val('09:00');
    $('#frequency').val('daily');
    $('#weeklyDay').val('monday');
    $('#monthlyDateType').val('specific');
    $('#monthlyDate').val('');
    $('#periodDays').val('');

    // Check weekdays by default
    $('#monday, #tuesday, #wednesday, #thursday, #friday').prop('checked', true);
    $('#saturday, #sunday').prop('checked', false);

    // Clear filter selections
    $('#scheduleAgencyFilter').val(null).trigger('change');
    $('#scheduleServiceFilter').val(null).trigger('change');
    $('#scheduleAssignedToFilter').val(null).trigger('change');
    $('#scheduleMedicationListFilter').val('');
    $('#scheduleInsuranceElgFilter').val('');
    $('#scheduleMdoTagFilter').val('');
    $('#scheduleBranchFilter').val(null).trigger('change');

    // Reset toggle checkboxes
    $('#scheduleShowOutliers').prop('checked', true);
    $('#scheduleShowHighestWeight').prop('checked', true);

    // Show daily options by default
    toggleFrequencyOptions();

    // Hide monthly preview
    $('#monthlyDatePreview').hide();
}

function loadSchedules() {
    // Show shimmer loader for schedules
    $('.schedule-shimmer-loader').show();
    const container = $('#schedulesList');

    // Clear content except shimmer loader
    container.find('.schedule-item').not('.schedule-shimmer-loader .schedule-item').remove();
    container.children('div').not('.schedule-shimmer-loader').remove();

    $.ajax({
        url: '{{ route("daily-referral-email.schedules") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                displaySchedules(response.data.data);
            }
        },
        error: function(xhr) {
            console.error('Error loading schedules:', xhr);
            $('.schedule-shimmer-loader').hide();
        }
    });
}

function getFrequencyDisplayText(schedule) {
    switch (schedule.frequency) {
        case 'weekly':
            return `Weekly (${schedule.weekly_day ? schedule.weekly_day.charAt(0).toUpperCase() + schedule.weekly_day.slice(1) : 'Monday'})`;
        case 'monthly':
            if (schedule.monthly_date === -1 || schedule.monthly_date < 0) {
                return 'Monthly (End of Month)';
            }
            return `Monthly (Day ${schedule.monthly_date || '1'})`;
        case 'daily':
        default:
            if (schedule.period_days && schedule.period_days > 1) {
                return `Every ${schedule.period_days} days`;
            }
            return schedule.send_days ? schedule.send_days.map(day => day.charAt(0).toUpperCase() + day.slice(1)).join(', ') : 'Daily';
    }
}

function displaySchedules(schedules) {
    const container = $('#schedulesList');

    // Hide shimmer loader
    $('.schedule-shimmer-loader').hide();

    // Clear content except shimmer loader
    container.find('.schedule-item').not('.schedule-shimmer-loader .schedule-item').remove();
    container.children('div').not('.schedule-shimmer-loader').remove();

    if (schedules.length === 0) {
        container.append(`
            <div class="p-3 text-center text-muted">
                <i class="mdi mdi-clock-outline mb-2" style="font-size: 24px;"></i>
                <p class="mb-0">No schedules found</p>
            </div>
        `);
        return;
    }

    schedules.forEach(schedule => {
        const statusBadge = schedule.is_active ?
            '<span class="badge badge-success">Active</span>' :
            '<span class="badge badge-secondary">Inactive</span>';

        const lastSent = schedule.last_sent_at ?
            `<small class="text-muted">Last sent: ${new Date(schedule.last_sent_at).toLocaleDateString()}</small>` :
            '<small class="text-muted">Never sent</small>';

        container.append(`
            <div class="schedule-item border-bottom p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">${schedule.name}</h6>
                        <small class="text-muted">${schedule.send_time} • ${getFrequencyDisplayText(schedule)}</small>
                    </div>
                    ${statusBadge}
                </div>
                <div class="mb-2">
                    <small class="text-muted">To: ${schedule.recipients.join(', ')}</small>
                </div>
                ${lastSent}
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-primary mr-1" onclick="testSchedule(${schedule.id})">
                        <i class="mdi mdi-send"></i> Test
                    </button>
                    <button class="btn btn-sm btn-outline-${schedule.is_active ? 'warning' : 'success'}" onclick="toggleSchedule(${schedule.id})">
                        <i class="mdi mdi-${schedule.is_active ? 'pause' : 'play'}"></i> ${schedule.is_active ? 'Pause' : 'Resume'}
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(${schedule.id})">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            </div>
        `);
    });
}

function testSchedule(id) {
    $.confirm({
        title: 'Test Schedule',
        columnClass: 'col-md-6',
        content: 'Send a test email with yesterday\'s report data?',
        buttons: {
            send: {
                text: 'Send Test',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        url: `/daily-referral-email/schedule/${id}/test`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Test email sent successfully!');
                            } else {
                                toastr.error('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            toastr.error('Error: ' + (response?.message || 'Failed to send test email'));
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                action: function() {
                    // Do nothing
                }
            }
        }
    });
}

function toggleSchedule(id) {
    $.ajax({
        url: `/daily-referral-email/schedule/${id}/toggle`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success('✅ ' + response.message);
                loadSchedules();
            } else {
                toastr.error('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            toastr.error('Error: ' + (response?.message || 'Failed to toggle schedule'));
        }
    });
}

function deleteSchedule(id) {
    $.confirm({
        title: 'Delete Schedule',
        columnClass: 'col-md-6',
        content: 'Are you sure you want to delete this schedule?',
        buttons: {
            delete: {
                text: 'Delete',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url: `/daily-referral-email/schedule/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Schedule deleted successfully!');
                                loadSchedules();
                            } else {
                                toastr.error('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            toastr.error('Error: ' + (response?.message || 'Failed to delete schedule'));
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                action: function() {
                    // Do nothing
                }
            }
        }
    });
}

// Handle recipient input on Enter key
$('#scheduleRecipientInput').on('keypress', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        addScheduleRecipient();
    }
});

$('#scheduleCcInput').on('keypress', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        addScheduleCc();
    }
});
  var start = moment().subtract(0, "days");
  var end = moment();
        $('#reportDate').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],

                 }
             }, function(chosen_date, end_date) {

                 $('#reportDate').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
                      filterHistory();
             })

             $('#reportDate').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });

             $('.email_range_date').daterangepicker({
                 startDate: start,
                 endDate: end,
                 autoUpdateInput: false,
                 startOfWeek: 'sunday',
                 ranges: {
                     'Select Date': [start, end],
                     'Today': [moment(), moment()],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                         'month').endOf('month')],
                     'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                         .endOf('month')
                     ],
                     'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                         .endOf('isoWeek')
                     ],
                     'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                         'weeks').endOf('isoWeek')],

                 }
             }, function(chosen_date, end_date) {

                 $('.email_range_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
                      filterHistory();
             })

             $('.email_range_date').on('apply.daterangepicker', function(ev, picker) {
                // Detect "Select Date"
                if (picker.chosenLabel === 'Select Date') {
                    $(this).val('');
                } else {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                }
            });

  
</script>