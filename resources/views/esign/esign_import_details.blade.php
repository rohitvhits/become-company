@include('include/header')
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css') }}" />
@include('include/sidebar')

<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .summary-card {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        border: none;
        margin-bottom: 20px;
    }

    .summary-card .card-body {
        padding: 20px;
    }

    .summary-stat {
        text-align: center;
        padding: 10px;
    }

    .summary-stat .stat-value {
        font-size: 24px;
        font-weight: 700;
    }

    .summary-stat .stat-label {
        font-size: 12px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-card {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        border: none;
    }

    .detail-card .card-header {
        background: white;
        border-bottom: 2px solid #f0f0f0;
        padding: 15px 20px;
        border-radius: 8px 8px 0 0 !important;
    }

    .table-detail { margin: 0; }

    .table-detail thead {
        background: #f8f9fa;
    }

    .table-detail thead th {
        border: none;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        padding: 12px 15px;
    }

    .table-detail tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }

    .table-detail tbody tr:hover {
        background: #f8f9fa;
    }

    .badge-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #999;
    }

    .empty-state i { font-size: 48px; opacity: 0.3; margin-bottom: 15px; }

    .shimmer {
        background: #f6f7f8;
        background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
        background-repeat: no-repeat;
        background-size: 800px 100%;
        display: inline-block;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes shimmer {
        0% { background-position: -800px 0; }
        100% { background-position: 800px 0; }
    }

    .shimmer-line { height: 14px; width: 100%; }
    .shimmer-line.short { width: 50%; }
    .shimmer-badge { height: 22px; width: 60px; display: inline-block; }
    #errorDetailModal .modal-footer {
        padding: 4px 1px !important;
    }

    .status-success {
        color: #28a745; /* Green */
        font-weight: bold;
    }

    .status-not-found {
        color: #dc3545; /* Red */
        font-weight: bold;
    }

    .status-agency-not-found {
        color: #007bff; /* Blue */
        font-weight: bold;
    }

    .status-type-not-found {
        color: #fd7e14; /* Orange */
        font-weight: bold;
    }

    .status-null {
        color: #6c757d; /* Gray */
        font-weight: bold;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">
                <i class="mdi mdi-file-document-outline text-primary"></i> Import Details
            </h5>
            <a href="{{ url('esign/esign-import') }}" class="btn btn-light btn-sm">
                <i class="mdi mdi-arrow-left"></i> Back to Import
            </a>
        </div>

        <!-- Summary Card -->
        <div class="card summary-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="summary-stat">
                            <div class="stat-label">Template</div>
                            <div class="font-weight-bold">{{ $importLog->template_name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="summary-stat">
                            <div class="stat-label">File Name</div>
                            <div class="font-weight-bold text-truncate" title="{{ $importLog->file_name }}">{{ $importLog->file_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="summary-stat">
                            <div class="stat-value text-info">{{ number_format($importLog->total_records) }}</div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="summary-stat">
                            <div class="stat-value text-success">{{ number_format($importLog->success_count) }}</div>
                            <div class="stat-label">Sent</div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="summary-stat">
                            <div class="stat-value text-danger">{{ number_format($importLog->failed_count) }}</div>
                            <div class="stat-label">Failed</div>
                        </div>
                    </div>
                  
                    <div class="col-md-2">
                        <div class="summary-stat">
                            <div class="stat-label">Status</div>
                            <div>
                                @php
                                    $badgeClass = 'badge-secondary';
                                    if($importLog->status == 'Completed') $badgeClass = 'badge-success';
                                    elseif($importLog->status == 'Processing') $badgeClass = 'badge-info';
                                    elseif($importLog->status == 'Pending') $badgeClass = 'badge-warning';
                                    elseif($importLog->status == 'Failed') $badgeClass = 'badge-danger';
                                @endphp
                                <span class="badge badge-status {{ $badgeClass }}">{{ $importLog->status }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="summary-stat">
                            <div class="stat-label">Uploaded On</div>
                            <div class="font-weight-bold">{{ $importLog->created_at ? Common::convertMDYTime($importLog->created_at) : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Table Card -->
        <div class="card detail-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 font-weight-bold"><i class="mdi mdi-format-list-bulleted text-primary mr-1"></i> Import Records</h6>
                    <div class="d-flex align-items-center">
                        <input type="text" id="sms_date_range" class="form-control form-control-sm mr-2" placeholder="Select SMS Date" style="width:220px;" autocomplete="off">
                        <select id="status_filter" class="form-control form-control-sm mr-2" style="width:130px;">
                            <option value="all">All Status</option>
                            @foreach($smsStatus as $sms)
                                @if($sms !="")
                                    <option value="{{ $sms}}">{{ ucfirst($sms)}} </option>
                                @endif
                                
                            @endforeach
                        </select>
                        <input type="text" id="search_details" class="form-control form-control-sm mr-2" placeholder="Search..." style="width:200px;">
                        <button type="button" id="btn_search_date" class="btn btn-primary mr-2" style="padding:.25rem .5rem; font-size:.875rem; line-height:1.2; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;"><i class="mdi mdi-magnify"></i> Search</button>
                        <button type="button" id="btn_reset_date" class="btn btn-light mr-2" style="padding:.25rem .5rem; font-size:.875rem; line-height:1.2; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;"><i class="mdi mdi-refresh"></i> Reset</button>
                        <select id="detail_per_page" class="form-control form-control-sm" style="width:80px;">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                   
                    
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-detail">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient ID</th>
                                <th>Mobile</th>
                                <th>Message</th>
                                <th>SMS ID</th>
                                <th>SMS Status</th>
                                <th>SMS Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="detail_tbody">
                            <tr><td colspan="9"><div class="empty-state"><i class="mdi mdi-loading mdi-spin"></i><p>Loading...</p></div></td></tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <div id="detail_pagination_info" class="small text-muted"></div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="detail_pagination_controls"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 20%;'> </div>
</div>

<div class="modal fade" id="errorDetailModal" tabindex="-1" aria-labelledby="errorDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background-color:transparent !important">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="errorDetailModalLabel">
                    <i class="mdi mdi-alert-circle mr-2"></i>Failed Record Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="background-color:white !important">
                
                <div class="form-group mb-0">
                    <label class="font-weight-semibold" for="">Error Message</label>
                   
                </div>
                <div id="error_message" style="word-break: break-word;"></div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('include/footer')
<input type="hidden" id="esign_import_config"
    data-import-id="{{ $importLog->id }}"
    data-detail-ajax-url="{{ url('esign/esign-import-details-ajax') }}"
    data-error-detail-url="{{ url('esign/esign-import-error-detail') }}"
    data-patient-view-url="{{ url('/patient/view') }}">
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('assets/modulejs/esign/esign_import_details.js') }}"></script>
