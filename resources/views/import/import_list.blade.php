@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<style>
    span.select2.select2-container.select2-container--default {
        width: 200px !important;
    }
    .page-title-main {
       display: flex;
       justify-content: space-between;
       align-items: center;
       margin-bottom: 20px;
   }

   /* Import Form Card Styles */
   .import-card {
       box-shadow: 0 2px 8px rgba(0,0,0,0.1);
       border-radius: 8px;
       border: none;
       margin-bottom: 30px;
   }

   .import-card .card-header {
       background: linear-gradient(135deg, #1f202f 0%, #1f202f 100%);
       color: white;
       border-radius: 8px 8px 0 0 !important;
       padding: 10px;
       border: none;
   }

   .import-card .card-header h5 {
       margin: 0;
       font-weight: 600;
       display: flex;
       align-items: center;
   }

   .import-card .card-header h5 i {
       margin-right: 10px;
       font-size: 24px;
   }

   .import-form-section {
       background: #f8f9fa;
       padding: 25px;
       border-radius: 8px;
       margin: 20px;
   }

   .form-label-custom {
       font-weight: 600;
       color: #333;
       margin-bottom: 8px;
       display: flex;
       align-items: center;
   }

   .form-label-custom i {
       margin-right: 8px;
       color: #667eea;
       font-size: 18px;
   }

   .custom-file-input:focus ~ .custom-file-label {
       border-color: #667eea;
       box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
   }

   .btn-import {
       background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
       border: none;
       padding: 12px 40px;
       font-weight: 600;
       border-radius: 6px;
       transition: all 0.3s ease;
       color: white;
   }

   .btn-import:hover {
       transform: translateY(-2px);
       box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
       color: white;
   }

   .info-box {
       background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
       border-left: 4px solid #2196f3;
       padding: 15px;
       border-radius: 6px;
   }

   .info-box i {
       color: #2196f3;
       font-size: 20px;
   }

   .info-box a {
       color: #1976d2;
       font-weight: 600;
       text-decoration: none;
   }

   .info-box a:hover {
       text-decoration: underline;
   }

   /* History Card Styles */
   .history-card {
       box-shadow: 0 2px 8px rgba(0,0,0,0.1);
       border-radius: 8px;
       border: none;
   }

   .history-card .card-header {
       background: white;
       border-bottom: 2px solid #f0f0f0;
       padding: 20px;
       border-radius: 8px 8px 0 0 !important;
   }

   .history-card .card-header h5 {
       margin: 0;
       font-weight: 600;
       color: #333;
       display: flex;
       align-items: center;
   }

   .history-card .card-header h5 i {
       margin-right: 10px;
       color: #667eea;
       font-size: 22px;
   }

   .table-custom {
       margin: 0;
   }

   .table-custom thead {
       background: #f8f9fa;
   }

   .table-custom thead th {
       border: none;
       color: #666;
       font-weight: 600;
       text-transform: uppercase;
       font-size: 12px;
       padding: 15px;
   }

   .table-custom tbody td {
       padding: 15px;
       vertical-align: middle;
       border-bottom: 1px solid #f0f0f0;
   }

   .table-custom tbody tr:hover {
       background: #f8f9fa;
       transition: background 0.2s ease;
   }

   .badge-status {
       padding: 6px 12px;
       border-radius: 20px;
       font-size: 11px;
       font-weight: 600;
       text-transform: uppercase;
       letter-spacing: 0.5px;
   }

   .badge-success-custom {
       background: #d4edda;
       color: #155724;
   }

   .badge-pending-custom {
       background: #fff3cd;
       color: #856404;
   }

   .badge-error-custom {
       background: #f8d7da;
       color: #721c24;
   }

   .badge-processing-custom {
       background: #d1ecf1;
       color: #0c5460;
   }

   .action-btn {
       padding: 6px 12px;
       border-radius: 4px;
       font-size: 12px;
       margin: 0 2px;
       transition: all 0.2s ease;
   }

   .action-btn:hover {
       transform: translateY(-1px);
   }

   .empty-state {
       text-align: center;
       padding: 60px 20px;
       color: #999;
   }

   .empty-state i {
       font-size: 64px;
       margin-bottom: 20px;
       opacity: 0.3;
   }

   .empty-state p {
       font-size: 16px;
       color: #999;
   }

   .form-control:focus, .custom-file-input:focus ~ .custom-file-label {
       border-color: #667eea;
       box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
   }

   .submit-section {
       text-align: center;
       padding: 20px;
       border-top: 2px solid #f0f0f0;
       margin-top: 20px;
   }

   /* Shimmer Effect Styles */
   .shimmer-wrapper {
       width: 100%;
       padding: 15px;
   }

   .shimmer {
       background: #f6f7f8;
       background-image: linear-gradient(
           to right,
           #f6f7f8 0%,
           #edeef1 20%,
           #f6f7f8 40%,
           #f6f7f8 100%
       );
       background-repeat: no-repeat;
       background-size: 800px 100%;
       display: inline-block;
       position: relative;
       animation: shimmer 1.5s infinite;
       border-radius: 4px;
   }

   @keyframes shimmer {
       0% {
           background-position: -800px 0;
       }
       100% {
           background-position: 800px 0;
       }
   }

   .shimmer-line {
       height: 16px;
       margin-bottom: 10px;
       width: 100%;
   }

   .shimmer-line.short {
       width: 60%;
   }

   .shimmer-line.medium {
       width: 80%;
   }

   .shimmer-badge {
       height: 24px;
       width: 60px;
       display: inline-block;
   }

   .shimmer-button {
       height: 32px;
       width: 32px;
       display: inline-block;
       margin: 0 2px;
   }

   .shimmer-row {
       display: table-row;
   }

   .shimmer-cell {
       display: table-cell;
       padding: 15px;
       vertical-align: middle;
       border-bottom: 1px solid #f0f0f0;
   }

   #exampleModal-patient-view-import .modal-header {
        border-bottom: 0;
        border-top-left-radius: 0rem;
        border-top-right-radius: 0rem;
    }

    #exampleModal-patient-view-import .modal-xl {
        max-width: 90%;
    }

    #exampleModal-patient-view-import .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }

    #exampleModal-patient-view-import .selectvalues {
        min-width: 150px;
        font-size: 0.875rem;
        height:36px !important
    }

    #exampleModal-patient-view-import .table th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }

    #exampleModal-patient-view-import .modal-footer {
        padding: 0.5rem 1.5rem;
    }
    .nowrap{
        white-space:nowrap
    }
</style>

<div class="main-panel">
    <?php $auth = auth()->user(); ?>

    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">
                <i class="mdi mdi-file-import text-primary"></i> Patient Import Management
            </h5>
        </div>

        <!-- Import Form Card -->
        <div class="card import-card">
            <div class="card-header" style="bacground:#1f202f !important">
                <h5><i class="mdi mdi-cloud-upload"></i> Upload CSV File</h5>
            </div>

            <div class="import-form-section">
                <form id="importForm" enctype="multipart/form-data" method="POST">
                    @csrf
                    <input type="hidden" id="has_agency_fk" value="<?php echo ($user->agency_fk == '') ? '0' : '1'; ?>">
                    <div class="row">
                        <!-- Agency Selection -->
                        <?php if ($user->agency_fk == '') { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="import_agency_ids" class="form-label-custom">
                                    <i class="mdi mdi-office-building"></i>Agency
                                    <span class="text-danger ml-1">*</span>
                                </label>
                                <select name="agency_id" class="form-control form-control-lg" id="import_agency_ids">
                                    <option value="">Select Agency</option>
                                    <?php if (count($agencyList) > 0) {
                                        foreach ($agencyList as $vsl) { ?>
                                            <option value="<?php echo $vsl->id; ?>"><?php echo $vsl->agency_name; ?></option>
                                    <?php }
                                    } ?>
                                </select>
                                <span class="error mt-2 text-danger d-block" id="agency_error"></span>
                            </div>
                        </div>
                        <?php } else { ?>
                            <input type="hidden" name="agency_id" value="<?php echo $user->agency_fk; ?>">
                        <?php } ?>

                        <!-- CSV File Upload -->
                        <div class="col-md-<?php echo ($user->agency_fk == '') ? '5' : '6'; ?>">
                            <div class="form-group">
                                <label for="upload_csv_file_id" class="form-label-custom">
                                    <i class="mdi mdi-file-upload"></i>Upload CSV File
                                    <span class="text-danger ml-1">*</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="upload_csv_file_id" name="images" accept=".csv">
                                    <label class="custom-file-label" for="upload_csv_file_id">Choose CSV file...</label>
                                </div>
                               
                                <span class="error mt-2 text-danger d-block" id="images_error"></span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-<?php echo ($user->agency_fk == '') ? '2' : '6'; ?>">
                            <div class="form-group">
                                <label class="form-label-custom" style="opacity: 0;" for="">Submit</label>
                                <button type="button" id="submitImportForm" class="btn btn-import btn-block btn-sm">
                                    <i class="mdi mdi-upload mr-2"></i>Import CSV
                                </button>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-download-circle mr-3"></i>
                                    <div>
                                        <strong>Need help with formatting?</strong>
                                        <p class="mb-0">Download the <a href="{{ URL::to('/sample.csv') }}" target="_blank">Sample CSV template</a> to see the correct format and required columns.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Import History Card -->
        <div class="card history-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5><i class="mdi mdi-history"></i> Import History</h5>
                    <div class="d-flex align-items-center">
                       
                        <input type="text" id="search_import" class="form-control form-control-sm mr-2" placeholder="Search imports..." style="width: 250px;">
                        <select id="per_page_select" class="form-control form-control-sm" style="width: 100px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom" id="import_history">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="nowrap">Date & Time</th>
                                <th class="nowrap">Agency</th>
                                <th class="nowrap">File Name</th>
                                <th class="nowrap">Total Records</th>
                                <th class="nowrap">Successful</th>
                                <th class="nowrap">Failed</th>
                                <th class="nowrap">Duplicate</th>
                                <th class="nowrap">Status</th>
                                <th class="nowrap">Approved Status</th>
                                <th class="nowrap">Approved Date / Approved By</th>
                              
                                <th class="nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="import_history_tbody">
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state">
                                        <i class="mdi mdi-loading mdi-spin"></i>
                                        <p>Loading import history...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="pagination_info"></div>
                        <nav>
                            <ul class="pagination mb-0" id="pagination_controls"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('import.partial.import_view_modal')

    <!-- Import Logs Modal -->
    <div class="modal fade" id="importLogsModal" tabindex="-1" aria-labelledby="importLogsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="background-color:transparent !important">
                <div class="modal-header text-white" style="background-color:#000000 !important">
                    <h5 class="modal-title font-weight-bold" id="importLogsModalLabel">
                        <i class="mdi mdi-file-document-outline mr-2"></i>Patient Import Logs
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color:white !important">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0"  style="background-color:#fff !important">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th class="nowrap">Ip Address</th>
                                    <th class="nowrap">Type</th>
                                    <th class="nowrap">Module</th>
                                    <th class="nowrap">Message</th>
                                    <th class="nowrap">Created Date</th>
                                    <th class="nowrap">Created By</th>
                                    <th class="nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody id="import_logs_tbody">
                                <tr>
                                    <td colspan="7" class="text-center p-4">
                                        <i class="mdi mdi-loading mdi-spin" style="font-size:24px"></i>
                                        <p class="mb-0 mt-2 text-muted">Loading logs...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div id="log_pagination_info" class="text-muted small"></div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0" id="log_pagination_controls"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Detail Modal -->
    <div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg"  style="background-color:transparent !important">
                <div class="modal-header text-white" style="background-color:#000000 !important">
                    <h5 class="modal-title font-weight-bold" id="logDetailModalLabel">
                        <i class="mdi mdi-information-outline mr-2"></i>View Data
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color:white !important">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color:#fff !important" id="log_detail_body">
                </div>
            </div>
        </div>
    </div>

    @include('include/footer')

    <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
    <script src="{{ asset('/assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
    <link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
    <script src="{{ asset('assets/js/moment.min.js')}}"></script>

    <script>
        var importConfig = {
            baseUrl: "{{ url('/') }}",
            importDataUrl: "{{ url('patient/importdata') }}",
            importFilesDataUrl: "{{ url('patient/import-files-data') }}",
            approveImportUrl: "{{ url('patient/approve-import') }}",
            deleteImportUrl: "{{ url('patient/delete-import') }}",
            syncImportUrl: "{{ url('patient/sync-import') }}",
            importLogsUrl: "{{ url('patient/import-logs') }}"
        };
    </script>
    <script src="{{ asset('assets/modulejs/patient/patient_import.js') }}?time={{ env('timestamp')}}"></script>
</div>
