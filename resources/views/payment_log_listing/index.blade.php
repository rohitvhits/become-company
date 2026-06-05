@include('include/header')
@include('include/sidebar')

<style>
    /* Compact Color Scheme */
    :root {
        --primary-color: #3498db;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --info-color: #16a085;
        --purple-gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--info-color) 100%);
        --card-shadow: 0 1px 3px rgba(0,0,0,0.08);
        --border-radius: 6px;
    }

    .listing-wrapper {
        background: #f8f9fa;
        padding: 15px;
    }

    .listing-header {
        background: var(--purple-gradient);
        color: white;
        padding: 15px 20px;
        border-radius: var(--border-radius);
        margin-bottom: 15px;
        box-shadow: var(--card-shadow);
    }

    .listing-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
    }

    .listing-header p {
        margin: 3px 0 0 0;
        opacity: 0.9;
        font-size: 12px;
    }

    .filter-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: var(--card-shadow);
    }

    .filter-card h6 {
        color: #333;
        font-weight: 600;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f0f0;
        font-size: 13px;
    }

    .stats-row {
        margin-bottom: 15px;
    }

    .stat-box {
        background: white;
        border-radius: var(--border-radius);
        padding: 15px;
        box-shadow: var(--card-shadow);
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .stat-box.blue::before { background: var(--info-color); }
    .stat-box.green::before { background: var(--success-color); }
    .stat-box.yellow::before { background: var(--warning-color); }
    .stat-box.purple::before { background: #764ba2; }

    .stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 3px 12px rgba(0,0,0,0.12);
    }

    .stat-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .stat-box.blue .stat-icon { color: var(--info-color); }
    .stat-box.green .stat-icon { color: var(--success-color); }
    .stat-box.yellow .stat-icon { color: var(--warning-color); }
    .stat-box.purple .stat-icon { color: #764ba2; }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin: 8px 0;
    }

    .stat-label {
        color: #666;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        font-weight: 500;
    }

    .data-table {
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .data-table thead {
        /* background: var(--purple-gradient); */
        color: white;
    }

    .data-table thead th {
        border: none;
        font-weight: 600;
        padding: 10px 8px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .data-table tbody td {
        padding: 8px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        font-size: 12px;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .btn-modern {
        border-radius: 5px;
        font-weight: 500;
        padding: 8px 20px;
        transition: all 0.3s ease;
        border: none;
        font-size: 13px;
    }

    .btn-modern-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-modern-primary:hover {
        background: #2980b9;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
    }

    .btn-modern-success {
        background: var(--success-color);
        color: white;
    }

    .btn-modern-success:hover {
        background: #229954;
        color: white;
        transform: translateY(-1px);
    }

    .btn-modern-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-modern-secondary:hover {
        background: #5a6268;
        color: white;
    }

    .form-control-modern {
        border-radius: 5px;
        border: 1px solid #ddd;
        padding: 8px 12px;
        transition: all 0.3s ease;
        font-size: 12px;
        height: 36px;
    }

    .form-control-modern:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.15rem rgba(52, 152, 219, 0.15);
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

    .badge-amount {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .table-actions {
        white-space: nowrap;
    }

    .pagination-wrapper {
        background: white;
        padding: 12px;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        margin-top: 12px;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper listing-wrapper">

        <!-- Header -->
        <div class="listing-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5><i class="mdi mdi-format-list-bulleted"></i> Payment Logs</h5>
                    <p>View, search, and manage imported records</p>
                </div>
                <a href="{{ route('payment_log_import.index') }}" class="btn btn-light btn-sm">
                    <i class="mdi mdi-cloud-upload"></i> Import
                </a>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="row stats-row">
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="stat-box green">
                    <div class="stat-icon">
                        <i class="mdi mdi-cash-multiple"></i>
                    </div>
                    <div class="stat-value" id="totalCash">$0.00</div>
                    <div class="stat-label">Cash</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="stat-box yellow">
                    <div class="stat-icon">
                        <i class="mdi mdi-credit-card"></i>
                    </div>
                    <div class="stat-value" id="totalCard">$0.00</div>
                    <div class="stat-label">Card</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <h6><i class="mdi mdi-filter-variant"></i> Filters</h6>
            <form id="searchForm">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="search_name" id="search_name" class="form-control form-control-modern" placeholder="Search name">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Agency</label>
                            <select name="search_agency" id="search_agency" class="form-control form-control-modern">
                                <option value="">All Agencies</option>
                                @foreach(\DB::table('agency')->where('delete_flag', 'N')->orderBy('agency_name')->get() as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Portal ID</label>
                            <input type="text" name="search_patient_id" id="search_patient_id" class="form-control form-control-modern" placeholder="ID">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="search_location" id="search_location" class="form-control form-control-modern" placeholder="Location">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="search_status" id="search_status" class="form-control form-control-modern">
                                <option value="">All Status</option>
                                <option value="draft">Draft</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="bill">Bill</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-modern">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-modern">
                        </div>
                    </div>

                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-modern btn-modern-primary btn-sm">
                        <i class="mdi mdi-magnify"></i> Search
                    </button>
                    <button type="button" class="btn btn-modern btn-modern-secondary btn-sm" onclick="resetFilters()">
                        <i class="mdi mdi-refresh"></i> Reset
                    </button>
                    <button type="button" class="btn btn-modern btn-modern-success btn-sm" onclick="exportData()">
                        <i class="mdi mdi-download"></i> Export
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Bar -->
        <div class="card mb-2" id="bulkActionsBar" style="display: none; padding: 10px 15px; background: #e3f2fd;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span id="selectedCount">0</span> record(s) selected
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-custom-success" onclick="bulkVerify()">
                        <i class="mdi mdi-check-all"></i> Verify Selected
                    </button>
                    <button type="button" class="btn btn-sm" onclick="bulkGenerateInvoice()" style="background: #f57c00; color: white; border: none; padding: 8px 20px; border-radius: 5px; font-weight: 500; font-size: 13px; transition: all 0.3s ease;">
                        <i class="mdi mdi-receipt"></i> Generate Invoice
                    </button>
                    <button type="button" class="btn btn-sm btn-modern-secondary" onclick="clearSelection()">
                        <i class="mdi mdi-close"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="data-table">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Portal ID</th>
                            <th>Agency</th>
                            <th>Type</th>
                            <th>Services</th>
                            <th>Total Billed</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTableBody">
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <i class="mdi mdi-loading mdi-spin" style="font-size: 36px;"></i>
                                <p class="mt-2 mb-0">Loading records...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper" id="paginationContainer"></div>
    </div>
</div>

<!-- View Payment Log Modal -->
<div class="custom-modal-overlay" id="viewModal">
    <div class="custom-modal" style="max-width: 1200px;">
        <div class="custom-modal-header">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <i class="mdi mdi-file-document icon"></i>
                    <h5 style="margin: 0;">Payment Log Details</h5>
                </div>
                <!-- Patient Information in Header -->
                <div id="patientInfoHeader" style="font-size: 12px; color: rgba(255,255,255,0.9); display: flex; gap: 15px; flex-wrap: wrap;">
                    <!-- Patient info will be dynamically loaded here -->
                </div>
            </div>
        </div>
        <div class="custom-modal-body">
            <!-- Payment Log Information -->
            <div class="row info-section mb-3">
                <div class="col-md-6">
                    <div class="info-section mb-3">
                        <h6 style="margin-bottom: 8px; font-weight: 600; color: #333; font-size: 13px; border-bottom: 2px solid #e0e0e0; padding-bottom: 6px;">
                            <i class="mdi mdi-receipt"></i> Payment Log Information
                        </h6>
                        <div id="paymentLogInfoSection" class="info-grid">
                            <!-- Payment log info will be dynamically loaded -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-section mb-3 checked_service_div">
                        <h6 style="margin-bottom: 8px; font-weight: 600; color: #333; font-size: 13px; border-bottom: 2px solid #e0e0e0; padding-bottom: 6px;">
                            <i class="mdi mdi-checkbox-marked-circle"></i> Services
                        </h6>
                        <div id="serviceActionButtons" style="display: none;">
                            <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                <button type="button" class="btn btn-sm" id="editServiceBtn"
                                    onclick="toggleEditServices()"
                                    style="background: #3498db; color: white; padding: 4px 12px; font-size: 11px;border-radius:6px">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm" id="saveServiceBtn"
                                    onclick="saveServices()"
                                    style="background: #27ae60; color: white; padding: 4px 12px; font-size: 11px; display: none;border-radius:5px">
                                    <i class="mdi mdi-content-save"></i> Save
                                </button>
                            </div>
                        </div>

                        <div id="checkedServicesSection" style="background: #f8f9fa; border-radius: 5px; padding: 10px; margin-bottom: 12px; max-height: 490px; overflow-y: auto;">
                            <!-- services will be dynamically loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- Billing Information Section (shown after invoice generation) -->
                    <div class="info-section mb-3" id="billingInfoSection" style="display: none;">
                        <h6 style="margin-bottom: 8px; font-weight: 600; color: #333; font-size: 13px; border-bottom: 2px solid #e0e0e0; padding-bottom: 6px;">
                            <i class="mdi mdi-cash-multiple"></i> Billing Information
                        </h6>
                        <div id="billingInfoContent" class="info-grid">
                            <!-- Billing info will be dynamically loaded -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left Side: Document Preview (Iframe) -->
                <div class="col-md-6">
                    <h6 style="margin-bottom: 10px; font-weight: 600; color: #333; font-size: 13px;">
                        <i class="mdi mdi-file-pdf"></i> Document Preview
                    </h6>
                    <div id="pdfViewerSection" style="border: 1px solid #ddd; border-radius: 5px; height: 400px; background: #f8f9fa; overflow: hidden;">
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                            <p class="text-muted" style="text-align: center; margin: 0;">
                                <i class="mdi mdi-file-document-outline" style="font-size: 48px; opacity: 0.3;"></i><br>
                                <small>Select a document to preview</small>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Services -->
                <div class="col-md-6">
                    <!-- Documents Table -->
                    <h6 style="margin: 12px 0 10px 0; font-weight: 600; color: #333; font-size: 13px;">
                        <i class="mdi mdi-folder-multiple"></i> Documents
                    </h6>
                    <div id="documentsTableSection" class="mb-3" style="max-height: 180px; overflow-y: auto; border: 1px solid #e0e0e0; border-radius: 5px;">
                        <!-- Documents table will be dynamically loaded -->
                    </div>
                </div>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-cancel" onclick="closeViewModal()">
                <i class="mdi mdi-close"></i> Close
            </button>
            <button type="button" id="verifyBtn" class="btn" style="background: #27ae60; color: white; padding: 8px 20px; font-size: 13px; border: none; border-radius: 5px; display: none;" onclick="verifyFromModal()">
                <i class="mdi mdi-check"></i> Verify
            </button>
            <button type="button" id="generateInvoiceBtn" class="btn" style="background: var(--success-color); color: white; padding: 8px 20px; font-size: 13px; border: none; border-radius: 5px; display: none;" onclick="saveAndGenerateInvoice()">
                <i class="mdi mdi-receipt"></i> Generate Invoice
            </button>
        </div>
    </div>
</div>

<!-- Loading Overlay for Listing Page -->
<div class="loading-overlay" id="listingLoadingOverlay">
    <div class="spinner"></div>
</div>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-overlay.active {
    display: flex;
}

.spinner {
    border: 3px solid rgba(255,255,255,0.3);
    border-top: 3px solid white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.btn-custom-success {
    background: #27ae60;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 5px;
    font-weight: 500;
    font-size: 13px;
    transition: all 0.3s ease;
}

.btn-custom-success:hover {
    background: #229954;
    color: white;
    transform: translateY(-1px);
}

.service-item {
    padding: 6px 8px;
    margin-bottom: 6px;
    border-radius: 4px;
    background: white;
    border: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.service-item:hover {
    border-color: var(--primary-color);
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.service-name-readonly {
    flex: 1;
    font-size: 11px;
    font-weight: 500;
    color: #555;
    background: #f8f9fa;
    padding: 5px 8px;
    border-radius: 3px;
    border: 1px solid #e0e0e0;
}

.service-name-input {
    flex: 1;
    padding: 5px 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 11px;
}

.service-name-input:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 0.1rem rgba(52, 152, 219, 0.15);
}

.service-item input.service-amount {
    width: 80px;
    padding: 5px 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 11px;
    text-align: right;
}

.service-item input.service-amount:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 0.1rem rgba(52, 152, 219, 0.15);
}

.service-item input.service-amount-invalid {
    border-color: #e74c3c !important;
    background-color: #fff5f5 !important;
    box-shadow: 0 0 0 0.1rem rgba(231, 76, 60, 0.15) !important;
}

.service-item input.service-amount-invalid:focus {
    border-color: #c0392b !important;
    box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25) !important;
}

.service-item-remove {
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 3px;
    padding: 3px 6px;
    font-size: 9px;
    cursor: pointer;
    transition: background 0.2s ease;
    flex-shrink: 0;
}

.service-item-remove:hover {
    background: #c0392b;
}

.info-section {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 8px;
}

.info-item {
    background: white;
    padding: 6px 10px;
    border-radius: 3px;
    font-size: 11px;
    border: 1px solid #e0e0e0;
}

.info-item strong {
    color: #555;
    font-weight: 600;
    margin-right: 4px;
    display: inline-block;
    min-width: 60px;
}

.info-item span {
    color: #333;
}

/* Documents Table Styles */
.documents-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}

.documents-table thead {
    background: #f8f9fa;
    position: sticky;
    top: 0;
    z-index: 1;
}

.documents-table th {
    padding: 8px 6px;
    text-align: left;
    font-weight: 600;
    color: #555;
    border-bottom: 2px solid #e0e0e0;
    font-size: 10px;
    text-transform: uppercase;
}

.documents-table td {
    padding: 8px 6px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
}

.documents-table tbody tr {
    transition: all 0.2s ease;
}

.documents-table tbody tr:hover {
    background: #e3f2fd;
}

.documents-table tbody tr.selected {
    background: #bbdefb;
}

.documents-table tbody tr.selected td {
    font-weight: 600;
}

.doc-name-cell {
    color: #333;
    font-weight: 500;
}

.doc-date-cell {
    color: #666;
}

.doc-creator-cell {
    color: #666;
}

.doc-service-cell {
    color: #666;
}

.document-item {
    padding: 10px 12px;
    margin-bottom: 8px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 12px;
}

.document-item:hover {
    background: #e3f2fd;
    border-color: var(--primary-color);
    transform: translateX(5px);
}

.document-item i {
    color: var(--danger-color);
    font-size: 18px;
    margin-right: 8px;
}

.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    animation: fadeIn 0.3s ease;
    overflow-y: auto;
    padding: 20px 0;
}

.custom-modal-overlay.active {
    display: flex;
}

.custom-modal {
    background: white;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease;
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    margin: auto;
}

@keyframes slideIn {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.custom-modal-header {
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    background: var(--purple-gradient);
}

.custom-modal-header .icon {
    font-size: 24px;
    color: white;
}

.custom-modal-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: white;
}

#patientInfoHeader {
    color: rgba(255,255,255,0.95) !important;
}

#patientInfoHeader span {
    color: rgba(255,255,255,0.95) !important;
}

#patientInfoHeader strong {
    color: rgba(255,255,255,1) !important;
    font-weight: 600;
}

.custom-modal-body {
    padding: 20px;
    font-size: 13px;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
    scroll-behavior: smooth;
}

/* Custom Scrollbar for Modal */
.custom-modal-body::-webkit-scrollbar {
    width: 8px;
}

.custom-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-modal-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.custom-modal-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Custom Scrollbar for Services Section */
#checkedServicesSection::-webkit-scrollbar {
    width: 6px;
}

#checkedServicesSection::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 10px;
}

#checkedServicesSection::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

#checkedServicesSection::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* Custom Scrollbar for Documents Table Section */
#documentsTableSection::-webkit-scrollbar {
    width: 6px;
}

#documentsTableSection::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#documentsTableSection::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

#documentsTableSection::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.custom-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-shrink: 0;
}

.custom-modal-footer .btn {
    padding: 8px 20px;
    font-size: 13px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background: #5a6268;
}

/* Fix SweetAlert z-index to appear above modal */
.swal2-container {
    z-index: 10001 !important;
}
</style>

<script>
// Global variables
let currentPage = 1;
let currentFilters = {};

// Load records on page load
$(document).ready(function() {
    loadRecords(1);

    // Handle search form submission
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadRecords(1);
    });

    @if(Session::has('success'))
        toastr.success('{{ Session::get("success") }}');
    @endif
    @if(Session::has('error'))
        toastr.error('{{ Session::get("error") }}');
    @endif
});

// Load records via AJAX
function loadRecords(page = 1) {
    showLoading(true);

    // Get filter values
    currentFilters = {
        search_name: $('#search_name').val(),
        search_agency: $('#search_agency').val(),
        search_patient_id: $('#search_patient_id').val(),
        search_location: $('#search_location').val(),
        search_status: $('#search_status').val(),
        date_from: $('#date_from').val(),
        date_to: $('#date_to').val(),
        page: page
    };

    $.ajax({
        url: '{{ route("payment_log_listing.index") }}',
        type: 'GET',
        data: currentFilters,
        success: function(response) {
            showLoading(false);
            if (response.success) {
                renderRecords(response.records);
                renderPagination(response.pagination);
                updateTotals(response.totals);
                clearSelection();
            }
        },
        error: function(xhr) {
            showLoading(false);
            toastr.error('Failed to load records');
        }
    });
}

// Render records in table
function renderRecords(records) {
    let html = '';

    if (records.length === 0) {
        html = `
            <tr>
                <td colspan="12" class="text-center text-muted py-4">
                    <i class="mdi mdi-inbox" style="font-size: 36px; opacity: 0.3;"></i>
                    <p class="mt-2 mb-0">No records found</p>
                </td>
            </tr>
        `;
    } else {
        records.forEach(record => {
            let statusBadge = '';
            if (record.status === 'draft') {
                statusBadge = '<span class="badge badge-modern" style="background: #1976d2; color: #fff;">Draft</span>';
            } else if (record.status === 'verified') {
                statusBadge = '<span class="badge badge-modern" style="background: #28a745; color: #fff;">Verified</span>';
            } else if (record.status === 'bill') {
                statusBadge = '<span class="badge badge-modern" style="background: #f57c00; color: #fff;">Bill</span>';
            } else {
                statusBadge = '<span class="badge badge-modern" style="background: #ffc107; color: #000;">Pending</span>';
            }

            let deleteBtn = '';
            if (record.status === 'draft') {
                deleteBtn = `
                    <button type="button" class="btn btn-sm btn-danger" style="padding: 4px 8px;"
                            onclick="deleteRecord(${record.id})" title="Delete">
                        <i class="mdi mdi-delete"></i>
                    </button>
                `;
            }

            let verifyBtn = '';
            if (record.status !== 'bill' && record.status !== 'verified') {
                verifyBtn = `
                    <button type="button" class="btn btn-sm btn-success" style="padding: 4px 8px;"
                            onclick="verifySingleRecord(${record.id})" title="Verify">
                        <i class="mdi mdi-check"></i>
                    </button>
                `;
            }

            // Calculate total billed amount for bill status
            let totalBilledDisplay = '';
            if (record.status === 'bill' && record.total_billed_amount) {
                totalBilledDisplay = `<span class="badge-amount" style="background: #e3f2fd; color: #1976d2; font-weight: 600;">$${parseFloat(record.total_billed_amount || 0).toFixed(2)}</span>`;
            }

            html += `
                <tr>
                    <td><input type="checkbox" class="record-checkbox" value="${record.id}" onchange="updateSelection()"></td>
                    <td><strong>#${record.id}</strong></td>
                    <td>${record.name || ''}</td>
                    <td>${record.dob || ''}</td>
                    <td><span class="badge badge-info" style="font-size: 10px;"><a href="{{ url('patient/view') }}/${record.patient_id}" target="_blank" style="color: white;">${record.patient_id || ''}</a></span></td>
                    <td>${truncate(record.agency_name || 'N/A', 15)}</td>
                    <td>${record.service_type || ''}</td>
                    <td>${truncate(record.services || '', 15)}</td>
                    <td>${totalBilledDisplay}</td>
                    <td>${statusBadge}</td>
                    <td class="table-actions">
                        <button type="button" class="btn btn-sm btn-info" style="padding: 4px 8px;"
                                onclick="viewPaymentLog(${record.id})" title="View Details">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        ${verifyBtn}
                        ${deleteBtn}
                    </td>
                </tr>
            `;
        });
    }

    $('#recordsTableBody').html(html);
}

// Render pagination
function renderPagination(pagination) {
    if (!pagination || pagination.last_page <= 1) {
        $('#paginationContainer').html('');
        return;
    }

    let html = '<nav><ul class="pagination pagination-sm justify-content-center mb-0">';

    // Previous button
    if (pagination.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRecords(${pagination.current_page - 1}); return false;">Previous</a></li>`;
    } else {
        html += '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }

    // Page numbers
    let startPage = Math.max(1, pagination.current_page - 2);
    let endPage = Math.min(pagination.last_page, pagination.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRecords(1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRecords(${i}); return false;">${i}</a></li>`;
        }
    }

    if (endPage < pagination.last_page) {
        if (endPage < pagination.last_page - 1) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRecords(${pagination.last_page}); return false;">${pagination.last_page}</a></li>`;
    }

    // Next button
    if (pagination.current_page < pagination.last_page) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRecords(${pagination.current_page + 1}); return false;">Next</a></li>`;
    } else {
        html += '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }

    html += '</ul></nav>';
    html += `<p class="text-center text-muted mt-2 mb-0" style="font-size: 12px;">Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total || 0} entries</p>`;

    $('#paginationContainer').html(html);
}

// Update totals
function updateTotals(totals) {
    $('#totalCash').text('$' + parseFloat(totals.cash || 0).toFixed(2));
    $('#totalCard').text('$' + parseFloat(totals.card || 0).toFixed(2));
}

// Reset filters
function resetFilters() {
    $('#searchForm')[0].reset();
    currentPage = 1;
    loadRecords(1);
}

// Export data
function exportData() {
    const params = new URLSearchParams(currentFilters);
    window.location.href = '{{ route("payment_log_listing.export") }}?' + params.toString();
}

// Truncate string
function truncate(str, length) {
    if (!str) return '';
    return str.length > length ? str.substring(0, length) + '...' : str;
}

// Selection functions
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.record-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.record-checkbox:checked');
    const count = checkboxes.length;

    document.getElementById('selectedCount').textContent = count;

    if (count > 0) {
        document.getElementById('bulkActionsBar').style.display = 'block';
    } else {
        document.getElementById('bulkActionsBar').style.display = 'none';
    }
}

function clearSelection() {
    document.getElementById('selectAll').checked = false;
    document.querySelectorAll('.record-checkbox').forEach(cb => {
        cb.checked = false;
    });
    updateSelection();
}

// Bulk verify
function bulkVerify() {
    const checkboxes = document.querySelectorAll('.record-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        toastr.warning('Please select at least one record');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: `You want to verify ${ids.length} record(s)?`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, verify!',
        cancelButtonText: 'No, cancel',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            showLoading(true);

            $.ajax({
                url: '{{ route("payment_log_listing.bulk_verify") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids
                },
                success: function(response) {
                    showLoading(false);
                    if (response.success) {
                        toastr.success(response.message);
                        loadRecords(currentPage);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    showLoading(false);
                    toastr.error(xhr.responseJSON?.message || 'Failed to verify records');
                }
            });
        }
    });
}

// Bulk generate invoice
function bulkGenerateInvoice() {
    const checkboxes = document.querySelectorAll('.record-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        toastr.warning('Please select at least one record');
        return;
    }

    Swal.fire({
        title: 'Generate Invoices?',
        text: `You want to generate invoices for ${ids.length} record(s)? Only verified records will be processed.`,
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate!',
        cancelButtonText: 'No, cancel',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            showLoading(true);

            $.ajax({
                url: '{{ route("payment_log_listing.bulk_generate_invoice") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids
                },
                success: function(response) {
                    showLoading(false);
                    if (response.success) {
                        toastr.success(response.message);
                        loadRecords(currentPage);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    showLoading(false);
                    toastr.error(xhr.responseJSON?.message || 'Failed to generate invoices');
                }
            });
        }
    });
}

// Verify single record
function verifySingleRecord(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to verify this payment log record?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, verify it!',
        cancelButtonText: 'No, cancel',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            showLoading(true);

            $.ajax({
                url: '{{ route("payment_log_listing.verify", "") }}/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showLoading(false);
                    if (response.success) {
                        toastr.success(response.message);
                        loadRecords(currentPage);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    showLoading(false);
                    toastr.error(xhr.responseJSON?.message || 'Failed to verify record');
                }
            });
        }
    });
}

// Delete record
function deleteRecord(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this payment log record?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            showLoading(true);

            $.ajax({
                url: '{{ route("payment_log_listing.delete", "") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showLoading(false);
                    if (response.success) {
                        toastr.success(response.message);
                        loadRecords(currentPage);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    showLoading(false);
                    toastr.error(xhr.responseJSON?.message || 'Failed to delete record');
                }
            });
        }
    });
}

function viewPaymentLog(id) {
    showLoading(true);

    $.ajax({
        url: '{{ route("payment_log_listing.view", "") }}/' + id,
        type: 'GET',
        success: function(response) {
            showLoading(false);
            if (response.success) {
                console.log(response);
                populateViewModal(response);
                $('#viewModal').addClass('active');
                // Prevent body scroll when modal is open
                $('body').css('overflow', 'hidden');
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            showLoading(false);
            toastr.error('Failed to load payment log details');
        }
    });
}

// Global variable to store current payment log ID, services, and status
let currentPaymentLogId = null;
let currentServices = [];
let currentPaymentLogStatus = null;
let isEditingServices = false;

// Global variable to store documents
let currentDocuments = [];

function populateViewModal(data) {
    const paymentLog = data.payment_log;
    const patient = data.patient;
    const documents = data.documents || [];
    const services = data.services || [];

    // Store current payment log ID, status, and documents
    currentPaymentLogId = paymentLog.id;
    currentServices = services;
    currentDocuments = documents;
    currentPaymentLogStatus = paymentLog.status;
    isEditingServices = false;
    console.log('currentServices' + currentServices);
    // Patient Info in Header
    if (patient) {
        
        let patientHeaderHtml = `<span><strong>Name:</strong> ${patient.first_name || ''} ${patient.last_name || ''}</span>
            <span><strong>ID:</strong> <a href="/patient/view/${paymentLog.patient_id}" target="_blank" style="color: rgba(255,255,255,0.9); text-decoration: underline;">${paymentLog.patient_id || 'N/A'}</a></span>
            <span><strong>Type:</strong> ${patient.type || 'N/A'}</span>
            <span><strong>Mobile:</strong> ${patient.mobile || 'N/A'}</span>`;
            console.log(patient);
        $('#patientInfoHeader').html(patientHeaderHtml);
    } else {
        $('#patientInfoHeader').html('<span style="opacity: 0.8;">No patient linked to this payment log</span>');
    }

    // Payment Log Info Section
    let paymentLogHtml = `
        <div class="info-item">
            <strong>Vendor:</strong> <span>${paymentLog.vendor_name || 'N/A'}</span>
        </div>
        <div class="info-item">
            <strong>Type:</strong> <span>${paymentLog.service_type || 'N/A'}</span>
        </div>
        <div class="info-item">
            <strong>Services:</strong> <span>${paymentLog.services || 'N/A'}</span>
        </div>
        <div class="info-item">
            <strong>PPD/Q:</strong> <span>${paymentLog.ppd_q || 'N/A'}</span>
        </div>
        <div class="info-item">
            <strong>Bill:</strong> <span>${paymentLog.bill || 'N/A'}</span>
        </div>
        <div class="info-item">
            <strong>Cash:</strong> <span>$${parseFloat(paymentLog.cash || 0).toFixed(2)}</span>
        </div>
        <div class="info-item">
            <strong>Card:</strong> <span>$${parseFloat(paymentLog.card || 0).toFixed(2)}</span>
        </div>
        <div class="info-item">
            <strong>Insurance:</strong> <span>${paymentLog.insurance || 'N/A'}</span>
        </div>
        <div class="info-item">
            <strong>Location:</strong> <span>${paymentLog.location || 'N/A'}</span>
        </div>
        <div class="info-item">
            <strong>Status:</strong> <span>${paymentLog.status || 'N/A'}</span>
        </div>
    `;
    $('#paymentLogInfoSection').html(paymentLogHtml);

    // Billing Information Section (only show if invoice_id exists)
    if (paymentLog.invoice_id) {
        let ser_total_amount = 0;

        currentServices.forEach(service => {
            ser_total_amount += Number(service.amount) || 0;
        });

        console.log(ser_total_amount);
        let billingHtml = `
            <div class="info-item">
                <strong>Invoice ID:</strong> <span>
                <a href="{{ url('account/admin/invoices') }}/${paymentLog.invoice_id}" target="_blank">
                    ${paymentLog.invoice_id || ''}
                </a>
                </span>
            </div>
            <div class="info-item">
                <strong>Total Amount:</strong> <span>$${parseFloat(ser_total_amount || 0).toFixed(2)}</span>
            </div>
            <div class="info-item">
                <strong>Insurance:</strong> <span>${paymentLog.insurance || 'N/A'}</span>
            </div>
            <div class="info-item">
                <strong>Status:</strong> <span class="badge badge-success">Billed</span>
            </div>
        `;
        $('#billingInfoContent').html(billingHtml);
        $('#billingInfoSection').show();
    } else {
        $('#billingInfoSection').hide();
    }

    // Documents Table Section
    populateDocumentsTable(documents);

    // Services Section
    renderCheckedServices();

    // Update payment summary
    updatePaymentSummary(paymentLog);

    // Update button visibility
    updateButtonVisibility();
}

function populateDocumentsTable(documents) {
    if (documents && documents.length > 0) {
        let tableHtml = `
            <table class="documents-table">
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Created Date</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
        `;

        documents.forEach((doc, index) => {
            const docName = doc.document_name || 'Untitled';
            const createdDate = doc.created_date ? new Date(doc.created_date).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }) : 'N/A';
            const createdBy = doc.created_by_name || 'Unknown';
            const service = doc.attachment_service || 'N/A';

            tableHtml += `
                <tr class="${index === 0 ? 'selected' : ''}" data-doc-index="${index}" onclick="selectDocument(${index})">
                    <td class="doc-name-cell">${docName}</td>
                    <td class="doc-date-cell">${createdDate}</td>
                    <td class="doc-creator-cell">${createdBy}</td>
                </tr>
            `;
        });

        tableHtml += `
                </tbody>
            </table>
        `;

        $('#documentsTableSection').html(tableHtml);

        // Load the first document by default
        if (documents.length > 0 && documents[0].attachment) {
            loadDocument(documents[0].attachment);
        }
    } else {
        $('#documentsTableSection').html(`
            <div style="padding: 20px; text-align: center;">
                <p class="text-muted" style="font-size: 11px; margin: 0;">No documents available</p>
            </div>
        `);
    }
}

function selectDocument(index) {
    if (!currentDocuments || !currentDocuments[index]) return;

    // Remove selected class from all rows
    $('.documents-table tbody tr').removeClass('selected');

    // Add selected class to clicked row
    $(`.documents-table tbody tr[data-doc-index="${index}"]`).addClass('selected');

    // Load the document
    const doc = currentDocuments[index];
    if (doc.attachment) {
        loadDocument(doc.attachment);
    }
}

function renderCheckedServices() {
    let servicesHtml = '';

    if (currentServices.length === 0) {
        servicesHtml = `
            <div style="text-align: center; margin: 20px 0;">
                <p class="text-muted" style="font-size: 11px; margin-bottom: 10px;">No services added.</p>
                <button type="button" class="btn btn-sm" onclick="addFirstService()"
                        style="background: #27ae60; color: white; padding: 5px 15px; font-size: 11px; border-radius: 5px;">
                    <i class="mdi mdi-plus-circle"></i> Add Service
                </button>
            </div>
        `;
    } else {
        currentServices.forEach((service, index) => {
            const isDisabled = !isEditingServices ? 'disabled' : '';

            if (isEditingServices) {
                // In edit mode: show input for service name
                const amount = parseFloat(service.amount) || 0;
                const isInvalidAmount = amount <= 0;
                const amountClass = isInvalidAmount ? 'service-amount service-amount-invalid' : 'service-amount';

                servicesHtml += `
                    <div class="service-item" data-index="${index}">
                        <input type="text" class="service-name-input" value="${service.service_name || ''}"
                               placeholder="Service name" onchange="updateServiceName(${index}, this.value)"
                               style="flex: 1; padding: 5px 8px; border: 1px solid #ddd; border-radius: 3px; font-size: 11px;">
                        <input type="text" class="${amountClass}" id="service-amount-${index}" value="${service.amount}"
                               placeholder="0.00" onchange="updateServiceAmount(${index}, this.value)" oninput="validateServiceAmount(${index}, this.value)">
                        <button type="button" class="service-item-remove" onclick="removeService(${index})">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                `;
            } else {
                // In view mode: show readonly
                servicesHtml += `
                    <div class="service-item" data-index="${index}">
                        <div class="service-name-readonly">${service.service_name || 'Unnamed Service'}</div>
                        <input type="text" class="service-amount" value="${service.amount}"
                               placeholder="0.00" onchange="updateServiceAmount(${index}, this.value)" disabled>
                    </div>
                `;
            }
        });

        // Add "Add Service" button when in edit mode
        if (isEditingServices) {
            servicesHtml += `
                <div style="text-align: center; margin-top: 10px;">
                    <button type="button" class="btn btn-sm" onclick="addNewService()"
                            style="background: #3498db; color: white; padding: 4px 12px; font-size: 11px;">
                        <i class="mdi mdi-plus"></i> Add Service
                    </button>
                </div>
            `;
        }
    }

    $('#checkedServicesSection').html(servicesHtml);
    calculateTotalAmount();
}

function addNewService() {
    currentServices.push({
        service_name: '',
        amount: '0.00'
    });
    renderCheckedServices();
}

function addFirstService() {
    // Enable edit mode
    if (!isEditingServices) {
        isEditingServices = true;
        $('#editServiceBtn').hide();
        $('#saveServiceBtn').show();
    }

    // Add new service
    currentServices.push({
        service_name: '',
        amount: '0.00'
    });

    renderCheckedServices();
}

function removeService(index) {
    Swal.fire({
        title: 'Remove Service?',
        text: 'Are you sure you want to remove this service?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove!',
        cancelButtonText: 'No, cancel',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            currentServices.splice(index, 1);
            renderCheckedServices();
        }
    });
}

function updateServiceName(index, value) {
    currentServices[index].service_name = value;
}

function updateServiceAmount(index, value) {
    currentServices[index].amount = parseFloat(value) || 0;
    calculateTotalAmount();
}

function validateServiceAmount(index, value) {
    const amount = parseFloat(value) || 0;
    const input = document.getElementById('service-amount-' + index);

    if (input) {
        if (amount <= 0) {
            // Invalid: add error class
            input.classList.add('service-amount-invalid');
        } else {
            // Valid: remove error class
            input.classList.remove('service-amount-invalid');
        }
    }
}

function calculateTotalAmount() {
    let total = 0;
    currentServices.forEach(service => {
        total += parseFloat(service.amount) || 0;
    });
    $('#totalAmount').text('$' + total.toFixed(2));
}

function updatePaymentSummary(paymentLog) {
    $('#cashAmount').text('$' + parseFloat(paymentLog.cash || 0).toFixed(2));
    $('#cardAmount').text('$' + parseFloat(paymentLog.card || 0).toFixed(2));
    $('#insuranceAmount').text('$' + parseFloat(paymentLog.insurance || 0).toFixed(2));
    calculateTotalAmount();
}

function loadDocument(path) {
    const iframe = `<iframe src="${path}" width="100%" height="100%" frameborder="0" style="border-radius: 5px;"></iframe>`;
    $('#pdfViewerSection').html(iframe);
}

function updateButtonVisibility() {
    // Show/hide elements based on status
    if (currentPaymentLogStatus === 'draft' || currentPaymentLogStatus === 'pending') {
        // Show Verify button, hide Generate Invoice
        $('#verifyBtn').show();
        $('#generateInvoiceBtn').hide();
        $('.checked_service_div').show();
        $('#serviceActionButtons').show();
    } else if (currentPaymentLogStatus === 'verified') {
        // Show Generate Invoice button and service actions, hide Verify
        $('#verifyBtn').hide();
        $('#generateInvoiceBtn').show();
        $('.checked_service_div').show();
        $('#serviceActionButtons').show();
    } else if (currentPaymentLogStatus === 'bill') {
        // Hide both buttons, show services but disable editing
        $('#verifyBtn').hide();
        $('#generateInvoiceBtn').hide();
        $('.checked_service_div').show();
        $('#serviceActionButtons').hide();
    } else {
        // Default: hide everything
        $('#verifyBtn').hide();
        $('#generateInvoiceBtn').hide();
        $('.checked_service_div').hide();
        $('#serviceActionButtons').hide();
    }
}

function toggleEditServices() {
    isEditingServices = !isEditingServices;

    if (isEditingServices) {
        $('#editServiceBtn').hide();
        $('#saveServiceBtn').show();
        renderCheckedServices();
    } else {
        $('#editServiceBtn').show();
        $('#saveServiceBtn').hide();
        renderCheckedServices();
    }
}

function verifyFromModal() {
    if (!currentPaymentLogId) {
        toastr.error('No payment log selected');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to verify this payment log record?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, verify it!',
        cancelButtonText: 'No, cancel',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            showLoading(true);

            $.ajax({
                url: '{{ route("payment_log_listing.verify", "") }}/' + currentPaymentLogId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showLoading(false);
                    if (response.success) {
                        toastr.success(response.message);
                        closeViewModal();
                        loadRecords(currentPage);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    showLoading(false);
                    toastr.error(xhr.responseJSON?.message || 'Failed to verify record');
                }
            });
        }
    });
}

function closeViewModal() {
    $('#viewModal').removeClass('active');
    $('#pdfViewerSection').html(`
        <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
            <p class="text-muted" style="text-align: center; margin: 0;">
                <i class="mdi mdi-file-document-outline" style="font-size: 48px; opacity: 0.3;"></i><br>
                <small>Select a document to preview</small>
            </p>
        </div>
    `);
    currentPaymentLogId = null;
    currentServices = [];
    currentDocuments = [];
    currentPaymentLogStatus = null;
    isEditingServices = false;
    $('#editServiceBtn').show();
    $('#saveServiceBtn').hide();
    // Re-enable body scroll when modal is closed
    $('body').css('overflow', 'auto');
}

function saveServices() {
    if (!currentPaymentLogId) {
        toastr.error('No payment log selected');
        return;
    }

    // Validate services
    if (currentServices.length === 0) {
        toastr.error('Please add at least one service');
        return;
    }

    // Validate that all services have names and valid amounts
    let hasEmptyService = false;
    let hasInvalidAmount = false;
    let invalidServiceNames = [];
    let zeroOrNegativeServices = [];

    currentServices.forEach((service, index) => {
        if (!service.service_name || service.service_name.trim() === '') {
            hasEmptyService = true;
        }

        const amount = parseFloat(service.amount) || 0;
        if (amount <= 0) {
            hasInvalidAmount = true;
            zeroOrNegativeServices.push(service.service_name || 'Unnamed Service');

            // Highlight the invalid amount field
            const input = document.getElementById('service-amount-' + index);
            if (input) {
                input.classList.add('service-amount-invalid');
            }
        }
    });

    if (hasEmptyService) {
        toastr.error('Please fill in all service names');
        return;
    }

    if (hasInvalidAmount) {
        toastr.error('please add All service amounts');
        return;
    }

    showLoading(true);

    $.ajax({
        url: '{{ route("payment_log_listing.update_services", "") }}/' + currentPaymentLogId,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            services: currentServices
        },
        success: function(response) {
            showLoading(false);
            if (response.success) {
                toastr.success(response.message);
                // Exit edit mode
                isEditingServices = false;
                $('#editServiceBtn').show();
                $('#saveServiceBtn').hide();
                renderCheckedServices();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            showLoading(false);
            toastr.error(xhr.responseJSON?.message || 'Failed to save services');
        }
    });
}

function saveAndGenerateInvoice() {
    if (!currentPaymentLogId) {
        toastr.error('No payment log selected');
        return;
    }

    // Validate services
    if (currentServices.length === 0) {
        toastr.error('Please add at least one service');
        return;
    }

    // Validate all amounts are greater than 0
    let hasInvalidAmount = false;
    let invalidServiceNames = [];

    currentServices.forEach((service, index) => {
        const amount = parseFloat(service.amount) || 0;
        if (amount <= 0) {
            hasInvalidAmount = true;
            invalidServiceNames.push(service.service_name || 'Unnamed Service');

            // Highlight the invalid amount field
            const input = document.getElementById('service-amount-' + index);
            if (input) {
                input.classList.add('service-amount-invalid');
            }
        }
    });

    if (hasInvalidAmount) {
        toastr.error('All service amounts must be greater than 0. Please update: ' + invalidServiceNames.join(', '));
        return;
    }

    Swal.fire({
        title: 'Generate Invoice?',
        text: 'Generate invoice for this payment log with the current services?',
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate!',
        cancelButtonText: 'No, cancel',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            showLoading(true);

            $.ajax({
                url: '{{ route("payment_log_listing.generate_bill", "") }}/' + currentPaymentLogId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    services: currentServices
                },
                success: function(response) {
                    showLoading(false);
                    if (response.success) {
                        toastr.success(response.message);
                        closeViewModal();
                        loadRecords(currentPage);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    showLoading(false);
                    toastr.error(xhr.responseJSON?.message || 'Failed to generate invoice');
                }
            });
        }
    });
}

function generateBill(id) {
    Swal.fire({
        title: 'Generate Invoice?',
        text: 'Generate invoice for this payment log?',
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate!',
        cancelButtonText: 'No, cancel',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            showLoading(true);

            $.ajax({
                url: '{{ route("payment_log_listing.generate_bill", "") }}/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showLoading(false);
                    if (response.success) {
                        toastr.success(response.message);
                        loadRecords(currentPage);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    showLoading(false);
                    toastr.error(xhr.responseJSON?.message || 'Failed to generate invoice');
                }
            });
        }
    });
}

function showLoading(show) {
    if (show) {
        $('#listingLoadingOverlay').addClass('active');
    } else {
        $('#listingLoadingOverlay').removeClass('active');
    }
}

// Close modal when clicking outside
$(document).on('click', '.custom-modal-overlay', function(e) {
    if (e.target === this) {
        closeViewModal();
    }
});
</script>

@include('include/footer')
