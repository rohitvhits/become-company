@include('include/header')
@include('include/sidebar')

<style>
    /* Modern Color Scheme - Compact Design */
    :root {
        --primary-color: #3498db;
        --primary-dark: #2980b9;
        --success-color: #27ae60;
        --success-dark: #229954;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --info-color: #16a085;
        --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        --border-radius: 6px;
    }

    .payment-import-wrapper {
        background: #f8f9fa;
        padding: 15px;
    }

    .import-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--info-color) 100%);
        color: white;
        padding: 15px 20px;
        border-radius: var(--border-radius);
        margin-bottom: 15px;
        box-shadow: var(--card-shadow);
    }

    .import-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
    }

    .import-header p {
        margin: 3px 0 0 0;
        opacity: 0.9;
        font-size: 12px;
    }

    .step-wizard {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: var(--card-shadow);
    }

    .steps-container {
        display: flex;
        justify-content: center;
        margin-bottom: 25px;
        position: relative;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    .steps-container::before {
        content: '';
        position: absolute;
        top: 18px;
        left: 80px;
        right: 80px;
        height: 2px;
        background: #e0e0e0;
        z-index: 0;
    }

    .step-item {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .step-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .step-item.active .step-circle {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    }

    .step-item.completed .step-circle {
        background: var(--success-color);
        color: white;
    }

    .step-item.completed .step-circle::after {
        content: '✓';
    }

    .step-label {
        font-size: 12px;
        color: #666;
        font-weight: 500;
    }

    .step-item.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }

    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .upload-zone {
        border: 2px dashed #ddd;
        border-radius: var(--border-radius);
        padding: 30px 20px;
        text-align: center;
        background: #fafafa;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .upload-zone:hover {
        border-color: var(--primary-color);
        background: #f0f8ff;
    }

    .upload-zone.dragover {
        border-color: var(--primary-color);
        background: #e3f2fd;
    }

    .upload-icon {
        font-size: 40px;
        color: var(--primary-color);
        margin-bottom: 12px;
    }

    .upload-zone h6 {
        margin: 0 0 5px 0;
        font-size: 14px;
    }

    .upload-zone p {
        margin: 0;
        font-size: 11px;
    }

    .btn-custom-primary {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        font-weight: 500;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .btn-custom-primary:hover {
        background: var(--primary-dark);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
    }

    .btn-custom-success {
        background: var(--success-color);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        font-weight: 500;
        font-size: 13px;
    }

    .btn-custom-success:hover {
        background: var(--success-dark);
        color: white;
    }

    .btn-sm-custom {
        padding: 6px 15px;
        font-size: 12px;
    }

    .stats-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 15px;
        box-shadow: var(--card-shadow);
        text-align: center;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
    }

    .stats-card.primary {
        border-left: 3px solid var(--primary-color);
    }

    .stats-card.success {
        border-left: 3px solid var(--success-color);
    }

    .stats-card.danger {
        border-left: 3px solid var(--danger-color);
    }

    .stats-card.info {
        border-left: 3px solid var(--info-color);
    }

    .stats-number {
        font-size: 24px;
        font-weight: 700;
        margin: 8px 0;
    }

    .stats-label {
        color: #666;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .table-modern {
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .table-modern thead {
        /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
        color: white;
    }

    .table-modern thead th {
        border: none;
        font-weight: 600;
        padding: 10px 8px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .table-modern tbody td {
        padding: 10px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        font-size: 12px;
    }

    .table-modern tbody tr:hover {
        background: #f8f9fa;
    }

    .badge-modern {
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 500;
    }

    /* Table-based Mapping Interface */
    #mappingTable thead th {
        padding: 8px;
        font-weight: 600;
        font-size: 11px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    #mappingTable thead td {
        padding: 5px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    #mappingTable tbody td {
        padding: 8px;
        border: 1px solid #dee2e6;
        font-size: 11px;
    }

    .mapping-select {
        width: 100%;
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 5px 8px;
        font-size: 11px;
        transition: all 0.3s ease;
    }

    .mapping-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.15rem rgba(52, 152, 219, 0.15);
    }

    .mapping-select.required {
        border-color: var(--danger-color);
        border-width: 2px;
    }

    .mapping-select.mapped {
        background-color: #d4edda;
        border-color: var(--success-color);
    }

    .csv-header-cell {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
    }

    .required-indicator {
        color: var(--danger-color);
        font-weight: bold;
        margin-left: 3px;
    }

    /* Custom Confirmation Modal */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.3s ease;
    }

    .custom-modal-overlay.active {
        display: flex;
    }

    .custom-modal {
        background: white;
        border-radius: 8px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .custom-modal-header {
        padding: 20px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .custom-modal-header .icon {
        font-size: 24px;
        color: var(--info-color);
    }

    .custom-modal-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .custom-modal-body {
        padding: 20px;
        font-size: 13px;
    }

    .custom-modal-body .summary-item {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-modal-body .summary-item.total {
        background: #e3f2fd;
        border-left: 3px solid var(--primary-color);
    }

    .custom-modal-body .summary-item.valid {
        background: #e8f5e9;
        border-left: 3px solid var(--success-color);
    }

    .custom-modal-body .summary-item.invalid {
        background: #ffebee;
        border-left: 3px solid var(--danger-color);
    }

    .custom-modal-body .summary-item .label {
        font-weight: 600;
        color: #555;
    }

    .custom-modal-body .summary-item .value {
        font-weight: 700;
        font-size: 18px;
    }

    .custom-modal-body .alert-box {
        margin-top: 15px;
        padding: 12px;
        border-radius: 5px;
        font-size: 12px;
    }

    .custom-modal-body .alert-box.warning {
        background: #fff3cd;
        border-left: 3px solid #ffc107;
        color: #856404;
    }

    .custom-modal-body .alert-box.success {
        background: #d4edda;
        border-left: 3px solid #28a745;
        color: #155724;
    }

    .custom-modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .custom-modal-footer .btn {
        padding: 8px 20px;
        font-size: 13px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .custom-modal-footer .btn-cancel {
        background: #6c757d;
        color: white;
    }

    .custom-modal-footer .btn-cancel:hover {
        background: #5a6268;
    }

    .custom-modal-footer .btn-confirm {
        background: var(--success-color);
        color: white;
    }

    .custom-modal-footer .btn-confirm:hover {
        background: var(--success-dark);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
    }

    /* Success Modal */
    .success-modal .custom-modal-header {
        background: linear-gradient(135deg, var(--success-color) 0%, #229954 100%);
        color: white;
        border-bottom: none;
    }

    .success-modal .custom-modal-header .icon {
        color: white;
        font-size: 40px;
    }

    .success-modal .custom-modal-body {
        text-align: center;
        padding: 30px;
    }

    .success-modal .custom-modal-body .success-icon {
        font-size: 60px;
        color: var(--success-color);
        margin-bottom: 15px;
    }

    .success-modal .custom-modal-body h5 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #333;
    }

    .success-modal .custom-modal-body p {
        color: #666;
        font-size: 14px;
    }

    .card-compact {
        margin-bottom: 12px;
    }

    .card-compact .card-header {
        padding: 10px 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        font-size: 13px;
        font-weight: 600;
    }

    .card-compact .card-body {
        padding: 12px;
    }

    h5.compact {
        font-size: 15px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-overlay.active {
        display: flex;
    }

    .spinner {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top: 3px solid white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .alert-modern {
        border-radius: var(--border-radius);
        border: none;
        padding: 10px 15px;
        box-shadow: var(--card-shadow);
        font-size: 13px;
    }

    .alert-modern.alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-modern.alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .validation-summary {
        background: white;
        border-radius: var(--border-radius);
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: var(--card-shadow);
    }

    .validation-summary h6 {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
    }

    .summary-item {
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        text-align: center;
    }

    .summary-item .value {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .summary-item .label {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
    }

    .summary-item.success .value {
        color: var(--success-color);
    }

    .summary-item.danger .value {
        color: var(--danger-color);
    }

    .summary-item.primary .value {
        color: var(--primary-color);
    }

    .error-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .error-item {
        background: #fff5f5;
        border-left: 3px solid var(--danger-color);
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 5px;
        font-size: 12px;
    }

    .error-item .row-number {
        font-weight: 700;
        color: var(--danger-color);
    }
    .btn-radius{
        border-radius: var(--border-radius);
    }
</style>

<div class="main-panel">
    <div class="content-wrapper payment-import-wrapper">

        <!-- Header -->
        <div class="import-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5><i class="mdi mdi-cloud-upload"></i> Payment Log Import</h5>
                </div>
                <a href="{{ route('payment_log_import.download_sample') }}" class="btn btn-light btn-sm">
                    <i class="mdi mdi-download"></i> Download Sample CSV
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Step Wizard -->
        <div class="step-wizard">

            <!-- Step 1: Upload & Mapping -->
            <div class="step-content active" id="step1">
                <!-- Upload Section -->
                <div id="uploadSection">
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-icon">
                            <i class="mdi mdi-cloud-upload"></i>
                        </div>
                        <h6>Drop CSV file here or click to browse</h6>
                        <p class="text-muted">Maximum file size: 10MB</p>
                        <input type="file" id="fileInput" accept=".csv" style="display: none;">
                        <button type="button" class="btn btn-custom-primary btn-sm-custom mt-2" onclick="$('#fileInput').click()">
                            <i class="mdi mdi-file-upload"></i> Select File
                        </button>
                    </div>
                    <div id="fileInfo" class="mt-3" style="display: none;">
                        <div class="alert alert-modern alert-success">
                            <strong>Selected File:</strong> <span id="fileName"></span>
                            <button type="button" class="close" onclick="resetUpload()">
                                <span>&times;</span>
                            </button>
                        </div>
                        <p class="text-muted" style="font-size: 12px;">
                            <i class="mdi mdi-information"></i> File will be uploaded automatically...
                        </p>
                    </div>
                </div>

                <!-- Mapping Section -->
                <div id="mappingSection" style="display: none;">
                    <h5 class="compact"><i class="mdi mdi-link-variant"></i> Map CSV Columns to Database Fields</h5>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card card-compact">
                                <div class="card-header">
                                    <i class="mdi mdi-table-eye"></i> File Preview with Column Mapping
                                </div>
                                <div class="card-body" style="padding: 0;">
                                    <div class="table-responsive">
                                        <table class="table mb-0" id="mappingTable" style="font-size: 11px;">
                                            <thead>
                                                <tr id="csvHeaderRow" style="background: #f8f9fa;">
                                                    <!-- CSV Headers will be loaded here -->
                                                </tr>
                                                <tr id="mappingRow" style="background: #fff;">
                                                    <!-- Mapping dropdowns will be loaded here -->
                                                </tr>
                                            </thead>
                                            <tbody id="previewRows">
                                                <!-- Preview rows will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="button" class="btn btn-secondary btn-radius btn-sm-custom" onclick="backToUpload()">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-custom-primary btn-sm-custom" onclick="processMapping()">
                            <i class="mdi mdi-check-circle"></i> Validate & Import
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Import Results -->
            <div class="step-content" id="step2">
                <div id="validationResults">
                    <!-- Validation results will be shown here -->
                </div>

                <div class="text-right mt-3" id="importActions" style="display: none;">
                    <button type="button" class="btn btn-secondary btn-sm-custom btn-radius" onclick="goToStep(1)">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-custom-success btn-sm-custom" id="confirmImportBtn" onclick="confirmImport()">
                        <i class="mdi mdi-database-import"></i> Confirm Import
                    </button>
                </div>

                <!-- Success Message -->
                <div id="successSection" style="display: none;">
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="mdi mdi-check-circle" style="font-size: 60px; color: var(--success-color);"></i>
                        </div>
                        <h5>Import Completed!</h5>
                        <p class="text-muted mb-3" id="successMessage"></p>
                        <button type="button" class="btn btn-custom-primary btn-sm-custom" onclick="window.location.href='{{ route('payment_log_listing.index') }}'">
                            <i class="mdi mdi-format-list-bulleted"></i> View Data
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm-custom" onclick="resetWizard()">
                            <i class="mdi mdi-reload"></i> Import Another
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import History -->
        <div class="card table-modern">
            <div class="card-header" style="padding: 10px 15px;">
                <h6 class="mb-0" style="font-size: 13px;"><i class="mdi mdi-history"></i> Recent Imports</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>File Name</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Valid</th>
                                <th>Invalid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($imports as $import)
                            <tr>
                                <td><strong>#{{ $import->id }}</strong></td>
                                <td><i class="mdi mdi-file-document"></i> {{ Str::limit($import->file_name, 25) }}</td>
                                <td>{{ $import->uploader->first_name ?? 'N/A' }} {{ $import->uploader->last_name ?? 'N/A' }}</td>
                                <td>{{ $import->uploaded_at ? $import->uploaded_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    @if($import->upload_status == 'Pending')
                                    <span class="badge badge-modern" style="background: #ffc107; color: #000;">Pending</span>
                                    @elseif($import->upload_status == 'Processed')
                                    <span class="badge badge-modern" style="background: #28a745; color: #fff;">Done</span>
                                    @else
                                    <span class="badge badge-modern" style="background: #dc3545; color: #fff;">Failed</span>
                                    @endif
                                </td>
                                <td>{{ $import->total_records }}</td>
                                <td><span class="badge badge-modern" style="background: #d4edda; color: #155724;">{{ $import->valid_records }}</span></td>
                                <td><span class="badge badge-modern" style="background: #f8d7da; color: #721c24;">{{ $import->invalid_records }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No imports found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-2">
            {{ $imports->links() }}
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<!-- Confirmation Modal -->
<div class="custom-modal-overlay" id="confirmationModal">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <i class="mdi mdi-alert-circle icon"></i>
            <h5>Confirm Import</h5>
        </div>
        <div class="custom-modal-body" id="confirmationModalBody">
            <!-- Content will be dynamically added -->
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-cancel" onclick="closeConfirmationModal()">
                <i class="mdi mdi-close"></i> Cancel
            </button>
            <button type="button" class="btn btn-confirm" onclick="proceedWithImport()">
                <i class="mdi mdi-check"></i> Confirm Import
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="custom-modal-overlay" id="successModal">
    <div class="custom-modal success-modal">
        <div class="custom-modal-header">
            <i class="mdi mdi-check-circle icon"></i>
            <h5>Import Successful!</h5>
        </div>
        <div class="custom-modal-body">
            <div class="success-icon">
                <i class="mdi mdi-check-circle-outline"></i>
            </div>
            <h5>Data Imported Successfully</h5>
            <p id="successModalMessage"></p>
        </div>
        <div class="custom-modal-footer" style="justify-content: center;">
            <button type="button" class="btn btn-confirm" onclick="closeSuccessModal()">
                <i class="mdi mdi-view-list"></i> View Data
            </button>
            <button type="button" class="btn btn-cancel" onclick="importAnother()">
                <i class="mdi mdi-reload"></i> Import Another
            </button>
        </div>
    </div>
</div>

@include('include/footer')

<script>
    let currentStep = 1;
    let currentImportId = null;
    let selectedFile = null;
    let validRecordsCount = 0;

    $(document).ready(function() {
        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('fileInput');

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        // Close modals when clicking outside
        $('.custom-modal-overlay').on('click', function(e) {
            if (e.target === this) {
                $(this).removeClass('active');
            }
        });

        @if(Session::has('success'))
        showAlert('success', '{{ Session::get('
            success ') }}');
        @endif
        @if(Session::has('error'))
        showAlert('error', '{{ Session::get('
            error ') }}');
        @endif
    });

    function handleFileSelect(file) {
        if (!file.name.endsWith('.csv')) {
            showAlert('error', 'Please select a CSV file');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            showAlert('error', 'File size must be less than 10MB');
            return;
        }

        selectedFile = file;
        $('#fileName').text(file.name);
        $('#fileInfo').fadeIn();

        // Auto-upload after 1 second
        setTimeout(function() {
            uploadFile();
        }, 1000);
    }

    function resetUpload() {
        selectedFile = null;
        $('#fileInput').val('');
        $('#fileInfo').fadeOut();
    }

    function backToUpload() {
        $('#mappingSection').hide();
        $('#uploadSection').show();
    }

    function uploadFile() {
        if (!selectedFile) {
            showAlert('error', 'Please select a file');
            return;
        }

        const formData = new FormData();
        formData.append('import_file', selectedFile);
        formData.append('_token', '{{ csrf_token() }}');

        showLoading(true);

        $.ajax({
            url: '{{ route("payment_log_import.upload") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showLoading(false);
                if (response.success) {
                    currentImportId = response.import_id;
                    loadMapping();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                showLoading(false);
                showAlert('error', 'Upload failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function loadMapping() {
        showLoading(true);

        $.ajax({
            url: '{{ route("payment_log_import.mapping", "") }}/' + currentImportId,
            type: 'GET',
            success: function(html) {
                showLoading(false);

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const headers = [];
                const previewRows = [];

                // Fix selector: remove extra "table"
                $(doc).find('#mapping_table thead th').each(function() {
                    headers.push($(this).text().trim());
                });

                // Fix selector for rows
                $(doc).find('#mapping_table tbody tr').each(function() {
                    const rowData = [];
                    $(this).find('td').each(function() {
                        rowData.push($(this).text().trim());
                    });
                    if (rowData.length > 0) {
                        previewRows.push(rowData);
                    }
                });
                buildMappingTable(headers, previewRows);

                $('#uploadSection').hide();
                $('#mappingSection').fadeIn();
            },
            error: function(xhr) {
                showLoading(false);
                showAlert('error', 'Failed to load mapping data');
            }
        });
    }

    function buildMappingTable(headers, previewRows) {
        const dbFields = {
            'name': {
                label: 'Name',
                required: true
            },
            'dob': {
                label: 'Date of Birth',
                required: true
            },
            'patient_id': {
                label: 'Portal ID',
                required: true
            },
            'vendor_name': {
                label: 'Vendor Name',
                required: true
            },
            'service_type': {
                label: 'Service Type',
                required: false
            },
            'services': {
                label: 'Services',
                required: false
            },
            'ppd_q': {
                label: 'PPD/Q',
                required: false
            },
            'bill': {
                label: 'Bill',
                required: false
            },
            'cash': {
                label: 'Cash',
                required: false
            },
            'card': {
                label: 'Card',
                required: false
            },
            'insurance': {
                label: 'Insurance',
                required: false
            },
            'location': {
                label: 'Location',
                required: false
            },
            'initials': {
                label: 'Initials',
                required: false
            }
        };

        // Build CSV header row
        let headerHtml = '';
        headers.forEach(function(header) {
            headerHtml += `<th class="csv-header-cell">${header}</th>`;
        });
        $('#csvHeaderRow').html(headerHtml);

        // Build mapping row with dropdowns
        let mappingHtml = '';
        headers.forEach(function(header, index) {
            const selectOptions = ['<option value="">-- Map to --</option>'];

            Object.keys(dbFields).forEach(function(field) {
                const fieldData = dbFields[field];
                // Auto-match logic
                const selected = header.toLowerCase() === field.toLowerCase() ||
                    header.toLowerCase() === fieldData.label.toLowerCase() ||
                    header.toLowerCase().replace(/[^a-z0-9]/g, '').includes(field.toLowerCase()) ||
                    field.toLowerCase().includes(header.toLowerCase().replace(/[^a-z0-9]/g, ''));
                const requiredMark = fieldData.required ? '<span class="required-indicator">*</span>' : '';
                selectOptions.push(`<option value="${field}" ${selected ? 'selected' : ''}>${fieldData.label}${requiredMark}</option>`);
            });

            const hasMapping = selectOptions.some(opt => opt.includes('selected'));
            const requiredClass = hasMapping && dbFields[selectOptions.find(opt => opt.includes('selected'))?.match(/value="([^"]+)"/)?.[1]]?.required ? 'required' : '';
            const mappedClass = hasMapping ? 'mapped' : '';
            mappingHtml += `
            <td>
                <select name="mapping_${index}" class="mapping-select ${requiredClass} ${mappedClass}" data-csv-column="${header}" onchange="updateMappingStyle(this)">
                    ${selectOptions.join('')}
                </select>
            </td>
        `;
        });
        $('#mappingRow').html(mappingHtml);

        // Build preview rows
        let previewHtml = '';
        previewRows.slice(0, 5).forEach(function(row) {
            previewHtml += '<tr>';
            row.forEach(function(cell) {
                previewHtml += `<td>${cell}</td>`;
            });
            previewHtml += '</tr>';
        });
        $('#previewRows').html(previewHtml);
    }

    function updateMappingStyle(select) {
        const $select = $(select);
        if ($select.val()) {
            $select.addClass('mapped');
        } else {
            $select.removeClass('mapped');
        }
    }

    function processMapping() {
        
        const mapping = {};
        const reverseMapping = {}; // CSV column -> DB field
        let hasError = false;
        
        // Collect mappings from dropdowns
        $('.mapping-select').each(function() {
            const dbField = $(this).val();
            const csvColumn = $(this).data('csv-column');

            if (dbField) {
                reverseMapping[csvColumn] = dbField;
                if (!mapping[dbField]) {
                    mapping[dbField] = csvColumn;
                }
            }
        });
        // Check required fields
        const required = ['name', 'dob', 'vendor_name', 'patient_id','services'];
        const missingFields = [];

        required.forEach(function(field) {
            if (!mapping[field]) {
                missingFields.push(field.replace('_', ' ').toUpperCase());
                hasError = true;
            }
        });
        if (hasError) {
            showAlert('error', 'Please map all required fields: ' + missingFields.join(', '));
            return;
        }

        showLoading(true);

        $.ajax({
            url: '{{ route("payment_log_import.process_mapping", "") }}/' + currentImportId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                mapping: mapping
            },
            success: function(html) {
                showLoading(false);

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const total = $(doc).find('.card.bg-primary h3').text().trim();
                const valid = $(doc).find('.card.bg-success h3').text().trim();
                const invalid = $(doc).find('.card.bg-danger h3').text().trim();

                validRecordsCount = parseInt(valid);

                // Show confirmation popup
                showImportConfirmation(total, valid, invalid, doc);
            },
            error: function(xhr) {
                showLoading(false);
                showAlert('error', 'Validation failed');
            }
        });
    }

    function showImportConfirmation(total, valid, invalid, doc) {
        const invalidCount = parseInt(invalid);
        const validCount = parseInt(valid);

        if (validCount === 0) {
            showAlert('error', 'No valid records to import. Please check the data and try again.');
            showValidationResults(total, valid, invalid, doc);
            goToStep(2);
            return;
        }

        // Build modal content
        let modalContent = `
        <div class="summary-item total">
            <span class="label">Total Records</span>
            <span class="value">${total}</span>
        </div>
        <div class="summary-item valid">
            <span class="label">Valid Records</span>
            <span class="value">${validCount}</span>
        </div>
        <div class="summary-item invalid">
            <span class="label">Invalid Records</span>
            <span class="value">${invalidCount}</span>
        </div>
    `;

        if (invalidCount > 0) {
            modalContent += `
            <div class="alert-box warning">
                <strong><i class="mdi mdi-alert"></i> Warning:</strong> ${invalidCount} record(s) will be skipped due to validation errors.
            </div>
        `;
        }

        modalContent += `
        <div class="alert-box success">
            <strong><i class="mdi mdi-check-circle"></i> Ready to Import:</strong> ${validCount} valid record(s) will be imported to the database.
        </div>
    `;

        $('#confirmationModalBody').html(modalContent);
        $('#confirmationModal').addClass('active');

        // Store validation doc for later use if canceled
        window.validationDoc = doc;
        window.validationSummary = {
            total,
            valid,
            invalid
        };
    }

    function closeConfirmationModal() {
        $('#confirmationModal').removeClass('active');

        // Show validation results if canceled
        if (window.validationDoc && window.validationSummary) {
            showValidationResults(
                window.validationSummary.total,
                window.validationSummary.valid,
                window.validationSummary.invalid,
                window.validationDoc
            );
            goToStep(2);
        }
    }

    function proceedWithImport() {
        $('#confirmationModal').removeClass('active');
        confirmImport();
    }

    function showValidationResults(total, valid, invalid, doc) {
        let resultsHtml = `
        <div class="validation-summary">
            <h6><i class="mdi mdi-chart-bar"></i> Validation Summary</h6>
            <div class="summary-grid">
                <div class="summary-item primary">
                    <div class="value">${total}</div>
                    <div class="label">Total Records</div>
                </div>
                <div class="summary-item success">
                    <div class="value">${valid}</div>
                    <div class="label">Valid Records</div>
                </div>
                <div class="summary-item danger">
                    <div class="value">${invalid}</div>
                    <div class="label">Invalid Records</div>
                </div>
            </div>
        </div>
    `;

        if (parseInt(invalid) > 0) {
            const invalidRows = [];
            $(doc).find('.card.bg-danger tbody tr').each(function() {
                const row = $(this).find('td:first').text();
                const errors = [];
                $(this).find('ul li').each(function() {
                    errors.push($(this).text());
                });
                if (row && errors.length > 0) {
                    invalidRows.push({
                        row: row,
                        errors: errors
                    });
                }
            });

            if (invalidRows.length > 0) {
                resultsHtml += `
                <div class="card card-compact">
                    <div class="card-header" style="background: #f8d7da; color: #721c24;">
                        <i class="mdi mdi-alert-circle"></i> Invalid Records (${invalid})
                    </div>
                    <div class="card-body error-list">
                        ${invalidRows.map(item => `
                            <div class="error-item">
                                <span class="row-number">Row ${item.row}:</span>
                                ${item.errors.map(err => `<div>• ${err}</div>`).join('')}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            }
        }

        // Add action buttons to the results
        resultsHtml += `
            <div class="card card-compact">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <button type="button" class="btn btn-custom-primary btn-sm-custom" onclick="goToStep(1)">
                                <i class="mdi mdi-arrow-left"></i> Back to Mapping
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm-custom btn-radius" onclick="resetWizard()">
                                <i class="mdi mdi-reload"></i> Start Over
                            </button>
                        </div>
                        <div>
        `;

        if (parseInt(valid) > 0) {
            resultsHtml += `
                            <button type="button" class="btn btn-custom-success btn-sm-custom" onclick="confirmImport()">
                                <i class="mdi mdi-database-import"></i> Import ${valid} Valid Records
                            </button>
            `;
        } else {
            resultsHtml += `
                            <div class="alert alert-modern alert-danger mb-0" style="display: inline-block;">
                                <i class="mdi mdi-alert-circle"></i> No valid records to import. Please go back and fix the errors.
                            </div>
            `;
        }

        resultsHtml += `
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#validationResults').html(resultsHtml);
        $('#importActions').hide(); // Hide the old action buttons section
    }

    function confirmImport() {
        showLoading(true);

        $.ajax({
            url: '{{ route("payment_log_import.confirm", "") }}/' + currentImportId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                showLoading(false);

                // Show success modal
                $('#successModalMessage').text(`${validRecordsCount} record(s) have been successfully imported to the database.`);
                $('#successModal').addClass('active');
            },
            error: function(xhr) {
                showLoading(false);
                showAlert('error', 'Import failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function closeSuccessModal() {
        $('#successModal').removeClass('active');
        window.location.href = '{{ route("payment_log_listing.index") }}';
    }

    function importAnother() {
        $('#successModal').removeClass('active');
        window.location.reload();
    }

    function goToStep(step) {
        $('.step-item').removeClass('active completed');
        $('.step-content').removeClass('active');

        for (let i = 1; i < step; i++) {
            $(`.step-item[data-step="${i}"]`).addClass('completed');
        }

        $(`.step-item[data-step="${step}"]`).addClass('active');
        $(`#step${step}`).addClass('active');

        currentStep = step;

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function resetWizard() {
        currentStep = 1;
        currentImportId = null;
        selectedFile = null;
        validRecordsCount = 0;

        resetUpload();
        $('#mappingSection').hide();
        $('#uploadSection').show();
        $('#validationResults').html('');
        $('#successSection').hide();
        goToStep(1);

        window.location.reload();
    }

    function showLoading(show) {
        if (show) {
            $('#loadingOverlay').addClass('active');
        } else {
            $('#loadingOverlay').removeClass('active');
        }
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'check-circle' : 'alert-circle';

        const alertHtml = `
        <div class="alert alert-modern ${alertClass} alert-dismissible fade show" role="alert">
            <i class="mdi mdi-${icon}"></i> <strong>${message}</strong>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;

        $('#alertContainer').html(alertHtml);

        setTimeout(function() {
            $('#alertContainer .alert').fadeOut();
        }, 5000);
    }

    function deleteImport(importId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete this import?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel',
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ml-2 mt-2',
            buttonsStyling: false
        }).then((result) => {
            if (result.value) {
                // Submit the form
                $('form[data-import-id="' + importId + '"]').submit();
            }
        });
    }
</script>