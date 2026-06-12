@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<style>
    .page-title-main {
       display: flex;
       justify-content: space-between;
       align-items: center;
       margin-bottom: 20px;
   }

   .import-card {
       box-shadow: 0 2px 8px rgba(0,0,0,0.1);
       border-radius: 8px;
       border: none;
       margin-bottom: 30px;
   }

   .import-card .card-header {
       background: linear-gradient(135deg, #365d5b, #7aa6a3, #1f3d3b 100%);
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
       background: linear-gradient(135deg, #365d5b 0%, #365d5b 100%);
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

   /* Shimmer Effect */
   .shimmer {
       background: #f6f7f8;
       background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
       background-repeat: no-repeat;
       background-size: 800px 100%;
       display: inline-block;
       position: relative;
       animation: shimmer 1.5s infinite;
       border-radius: 4px;
   }

   @keyframes shimmer {
       0% { background-position: -800px 0; }
       100% { background-position: 800px 0; }
   }

   .shimmer-line {
       height: 16px;
       margin-bottom: 10px;
       width: 100%;
   }

   .shimmer-line.short { width: 60%; }
   .shimmer-line.medium { width: 80%; }

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

   .shimmer-cell {
       padding: 15px;
       vertical-align: middle;
       border-bottom: 1px solid #f0f0f0;
   }

    #template_id + .select2-container .select2-selection--single {
        height: 40px !important;
    }

    #template_id + .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }
    #template_id + .select2-container .select2-selection--single {
    height: 36px !important;
    display: flex !important;
    align-items: center !important;
}
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">
                <i class="mdi mdi-file-import text-primary"></i> E-Sign Import Management
            </h5>
            <div>
                <a href="{{ url('template') }}" class="btn btn-light btn-sm">
                    <i class="mdi mdi-arrow-left"></i> Back to Templates
                </a>
            </div>
        </div>

        <!-- Import Form Card -->
        <div class="card import-card">
            <div class="card-header">
                <h5><i class="mdi mdi-cloud-upload"></i> Upload CSV File</h5>
            </div>

            <div class="import-form-section">
                <form id="importForm" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="row">
                        @if(isset($userTemplateType) && strtolower($userTemplateType) == 'all')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="template_type" class="form-label-custom">
                                    <i class="mdi mdi-file-document-outline"></i>Template Type
                                </label>
                                <select name="template_type" class="form-control" id="template_type">
                                   <option value="">All</option>
                                   <option value="location">Location</option>
                                   <option value="telehealth">Telehealth</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <!-- Template Selection -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="template_id" class="form-label-custom">
                                    <i class="mdi mdi-file-document-outline"></i>Template
                                    <span class="text-danger ml-1">*</span>
                                </label>
                                <select name="template_id" class="form-control form-control-lg" id="template_id">
                                    <option value="">Select Template</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->template_name }}</option>
                                    @endforeach
                                </select>
                                <span class="error mt-2 text-danger d-block" id="template_error"></span>
                            </div>
                        </div>

                        <!-- CSV File Upload -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="csv_file" class="form-label-custom">
                                    <i class="mdi mdi-file-upload"></i>Upload CSV File
                                    <span class="text-danger ml-1">*</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="csv_file" name="csv_file" accept=".csv">
                                    <label class="custom-file-label" for="csv_file">Choose CSV file...</label>
                                </div>
                                <span class="error mt-2 text-danger d-block" id="csv_file_error"></span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label-custom" style="opacity: 0;">Submit</label>
                                <button type="button" id="submitImportForm" class="btn btn-import btn-block btn-sm">
                                    <i class="mdi mdi-upload mr-2"></i>Import CSV
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="info-box">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-download-circle mr-3"></i>
                                    <div>
                                        <strong>Need help with formatting?</strong>
                                        <p class="mb-0">Download the <a href="{{ url('esign/esign-import-sample-csv') }}">Sample CSV template</a> to see the correct format and required columns.</p>
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
                                <th>Date & Time</th>
                                <th>Template Name</th>
                                <th>File Name</th>
                                <th>Total Records</th>
                                <th>Sent</th>
                                <th>Failed</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="import_history_tbody">
                            <tr>
                                <td colspan="9">
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
                        <nav aria-label="Pagination Navigation">
                            <ul class="pagination mb-0" id="pagination_controls"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSV Mapping Modal -->
@include('esign._partial.mapping_modal')

@include('include/footer')

<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>

<script>
    // Global variables passed from Blade to external JS
    var _CAN_VIEW_DETAIL = @json(auth()->user()->can('template-import-view-detail'));
    var _CAN_DELETE = @json(auth()->user()->can('template-import-download'));
    var _ESIGN_URLS = {
        importStore: "{{ url('esign/esign-import-store') }}",
        importHistory: "{{ url('esign/esign-import-history') }}",
        importTemplates: "{{ url('esign/esign-import-templates') }}",
        importMappingData: "{{ url('esign/esign-import-mapping-data') }}",
        importDetails: "{{ url('esign/esign-import-details') }}",
        importDownload: "{{ url('esign/esign-import-download') }}",
        importDelete: "{{ url('esign/esign-import-delete') }}",
        manualSync: "{{ url('esign/manual-sync-import') }}"
    };
</script>
<script src="{{ asset('assets/modulejs/esignImport/esign_import.js') }}?v={{ time() }}"></script>
