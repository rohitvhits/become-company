@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<!-- SheetJS library for Excel file parsing -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Create New Invoice</h5>
            <div class="page-rightbtns">
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary invoice-action-btn btn-fw btn-sm">
                    <i class="mdi mdi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Invoice Type Selection -->
        <div class="row admin-invoice-stats">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4">
                            <i class="mdi mdi-clipboard-list me-2"></i>Choose Invoice Type
                        </h6>
                        <div class="row admin-invoice-stats">
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card invoice-type-card cursor-pointer" data-type="uploaded_pdf">
                                    <div class="card-body text-center">
                                        <div class="form-group">
                                            <i class="mdi mdi-file-upload display-3 text-primary"></i>
                                        </div>
                                        <h5 class="card-title">Upload PDF</h5>
                                        <p class="card-description">Upload an existing PDF invoice</p>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="radio" name="invoice_type"
                                                   id="type_pdf" value="uploaded_pdf">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card invoice-type-card cursor-pointer" data-type="quick">
                                    <div class="card-body text-center">
                                        <div class="form-group">
                                            <i class="mdi mdi-flash-outline display-3 text-success"></i>
                                        </div>
                                        <h5 class="card-title">Quick Invoice</h5>
                                        <p class="card-description">Simple invoice with single description</p>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="radio" name="invoice_type"
                                                   id="type_quick" value="quick">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card invoice-type-card cursor-pointer" data-type="detailed">
                                    <div class="card-body text-center">
                                        <div class="form-group">
                                            <i class="mdi mdi-format-list-bulleted display-3 text-info"></i>
                                        </div>
                                        <h5 class="card-title">Detailed Invoice</h5>
                                        <p class="card-description">Itemized invoice with multiple line items</p>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="radio" name="invoice_type"
                                                   id="type_detailed" value="detailed">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="alert alert-danger">
                        <h6>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Invoice Form -->
        <form id="invoiceForm" method="POST" action="{{ route('admin.invoices.store') }}" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="type" id="selected_type">

            <!-- Basic Information -->
            <div class="row admin-invoice-stats">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="mdi mdi-information me-2"></i>Basic Information
                            </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="agency_id">Agency <span class="text-danger">*</span></label>
                                    <select name="agency_id" id="agency_id" class="form-control">
                                        <option value="">Select Agency</option>
                                        @foreach($agencies as $agency)
                                            <option value="{{ $agency->id }}">{{ $agency->agency_name }} ({{ $agency->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" id="due_date" class="form-control"
                                           value="{{ now()->addDays(30)->format('Y-m-d') }}">
                                    <div class="form-text">Past due dates are allowed for backdated invoices</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="invoice_number">Invoice Number</label>
                                    <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                                           placeholder="Auto-generated if left blank">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                           placeholder="Invoice title or subject">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"
                                      placeholder="Invoice description or notes"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF Upload Section -->
        <div class="row admin-invoice-stats" id="pdf-section" style="display: none;">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-file-pdf-box me-2"></i>PDF Upload
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="pdf_file" class="form-label">Select PDF File <span class="text-danger">*</span></label>
                            <input type="file" name="pdf_file" id="pdf_file" class="form-control">
                            <div class="form-text">Maximum file size: 10MB. Only PDF files are allowed.</div>
                        </div>
                        <div class="mb-3">
                            <label for="pdf_amount" class="form-label">Invoice Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="pdf_total_amount" id="pdf_amount" class="form-control"
                                       step="0.01" min="0.01" placeholder="0.00">
                            </div>
                            <div class="form-text">Enter the total amount for this invoice</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Invoice Section -->
        <div class="row admin-invoice-stats" id="quick-section" style="display: none;">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-lightning-bolt me-2"></i>Quick Invoice Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quick_amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="quick_total_amount" id="quick_amount" class="form-control"
                                               step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quick_tax" class="form-label">Tax (%)</label>
                                    <input type="number" name="tax_percentage" id="quick_tax" class="form-control"
                                           step="0.01" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quick_discount" class="form-label">Discount (%)</label>
                                    <input type="number" name="discount_percentage" id="quick_discount" class="form-control"
                                           step="0.01" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Invoice Section -->
        <div class="row admin-invoice-stats" id="detailed-section" style="display: none;">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-format-list-bulleted me-2"></i>Invoice Items
                            </h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-info invoice-action-btn me-2" onclick="downloadSampleFile()">
                                    <i class="mdi mdi-download me-1"></i>Download Sample
                                </button>
                                <button type="button" class="btn btn-sm btn-success invoice-action-btn me-2" onclick="document.getElementById('excel-import').click()">
                                    <i class="mdi mdi-file-excel me-1"></i>Import Excel
                                </button>
                                <button type="button" class="btn btn-sm btn-primary invoice-action-btn" onclick="addInvoiceItem()">
                                    <i class="mdi mdi-plus me-1"></i>Add Item
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Excel Import Section -->
                        <input type="file" id="excel-import" accept=".xlsx,.xls,.csv" style="display: none;" onchange="handleExcelImport(event)">

                        <div id="import-preview" style="display: none;" class="mb-4">
                            <div class="card import-preview-card">
                                <div class="card-header text-black py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="mdi mdi-file-excel me-2"></i>Import Preview (<span id="import-count">0</span> items)
                                        </h6>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-success btn-sm invoice-action-btn mr-2" onclick="confirmImport()">
                                                <i class="mdi mdi-check me-1"></i>Confirm
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm invoice-action-btn" onclick="cancelImport()">
                                                <i class="mdi mdi-close me-1"></i>Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" id="import-preview-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-3 py-2">Description</th>
                                                    <th class="px-2 py-2 text-center" style="width: 80px;">Qty</th>
                                                    <th class="px-2 py-2 text-end" style="width: 100px;">Price</th>
                                                    <th class="px-2 py-2 text-center" style="width: 70px;">Tax%</th>
                                                    <th class="px-2 py-2 text-end" style="width: 100px;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="import-preview-body">
                                                <!-- Preview rows will be inserted here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="invoice-items">
                            <!-- Items will be added here dynamically -->
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="detailed_tax" class="form-label">Overall Tax (%)</label>
                                            <input type="number" name="tax_percentage" id="detailed_tax" class="form-control"
                                                   step="0.01" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="detailed_discount" class="form-label">Overall Discount (%)</label>
                                            <input type="number" name="discount_percentage" id="detailed_discount" class="form-control"
                                                   step="0.01" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Invoice Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal:</span>
                                            <span id="invoice-subtotal">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Tax:</span>
                                            <span id="invoice-tax">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Discount:</span>
                                            <span id="invoice-discount">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span id="invoice-total">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="row admin-invoice-stats">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-file-document me-2"></i>Terms & Conditions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                            <textarea name="terms_conditions" id="terms_conditions" class="form-control" rows="4"
                                      placeholder="Enter payment terms, conditions, and other notes"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Form Actions -->
            <div class="row admin-invoice-stats">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary invoice-action-btn btn-fw btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Cancel
                            </a>
                            <div>
                                <button type="submit" name="action" value="save_draft" class="btn btn-primary invoice-action-btn btn-fw btn-sm me-2" id="save-draft-btn">
                                    <i class="mdi mdi-content-save"></i> Save as Draft
                                </button>
                                <button type="submit" name="action" value="save_and_send" class="btn btn-success invoice-action-btn btn-fw btn-sm" id="save-send-btn">
                                    <i class="mdi mdi-send"></i> Save & Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
</div>


<style>
.page-header {
    margin-bottom: 2rem;
}

.invoice-type-card {
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.invoice-type-card:hover .card {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}

.invoice-type-card.selected .card {
    border-color: #00BBE0;
    background: linear-gradient(145deg, rgba(117, 113, 249, 0.1), rgba(117, 113, 249, 0.05));
    box-shadow: 0 4px 20px rgba(117, 113, 249, 0.3);
    transform: translateY(-3px);
}

.invoice-type-card.selected .form-check-input {
    background-color: #00BBE0;
    border-color: #00BBE0;
}

.invoice-type-card.selected::after {
    content: '✓';
    position: absolute;
    top: 15px;
    right: 15px;
    width: 25px;
    height: 25px;
    background: #00BBE0;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    animation: checkmarkPop 0.3s ease-out;
}

@keyframes checkmarkPop {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.invoice-type-card .card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-width: 2px;
}

.invoice-type-card:hover:not(.selected) .card {
    border-color: rgba(117, 113, 249, 0.3);
    background: rgba(117, 113, 249, 0.02);
}

.cursor-pointer {
    cursor: pointer;
}

.invoice-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.invoice-item:last-child {
    margin-bottom: 0;
}

/* Import Preview Card */
.import-preview-card {
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    animation: slideDown 0.3s ease-out;
}

.import-preview-card .card-header {
    border-bottom: none;
    background-color: #e1e1e1;
}

.import-preview-card .table th {
    border-top: none;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
    font-size: 0.875rem;
    background-color: #f8f9fa;
}

.import-preview-card .table td {
    border-top: 1px solid #f1f3f4;
    font-size: 0.875rem;
    vertical-align: middle;
}

.import-preview-card .table tbody tr:hover {
    background-color: #f8f9fa;
}

@keyframes slideDown {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Imported items styling */
.invoice-item.imported-item {
    border: 2px solid #28a745;
    background-color: #f8fff9;
    animation: importHighlight 2s ease-in-out;
}

@keyframes importHighlight {
    0% {
        background-color: #d4edda;
        transform: scale(1.02);
    }
    100% {
        background-color: #f8fff9;
        transform: scale(1);
    }
}
.invoice-type-card .card-body {
    background-color: #eeee;
} 
</style>



<script>
let itemCounter = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Ensure past due dates are allowed
    const dueDateField = document.getElementById('due_date');
    if (dueDateField) {
        // Remove any min attribute that might restrict past dates
        dueDateField.removeAttribute('min');

        // Force the field to accept past dates by removing any constraints
        dueDateField.min = '';

        // Handle validation to explicitly allow past dates
        dueDateField.addEventListener('input', function() {
            // Clear any browser validation errors for past dates
            this.setCustomValidity('');
        });

        // Also handle the change event
        dueDateField.addEventListener('change', function() {
            // Clear any browser validation errors for past dates
            this.setCustomValidity('');
        });

        // Add explicit validation message that past dates are OK
        dueDateField.addEventListener('invalid', function(e) {
            // Prevent default browser validation messages about past dates
            e.preventDefault();
            // Only validate if the field is completely empty
            if (!this.value) {
                this.setCustomValidity('Please select a due date');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Invoice type selection with enhanced feedback
    document.querySelectorAll('.invoice-type-card').forEach(card => {
        card.addEventListener('click', function() {
            const type = this.dataset.type;

            // Add loading state briefly for better UX
            this.style.opacity = '0.7';
            this.style.transform = 'scale(0.92)';

            setTimeout(() => {
                selectInvoiceType(type);
                this.style.opacity = '';
                this.style.transform = '';
            }, 100);
        });
    });

    // Radio button change
    document.querySelectorAll('input[name="invoice_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                selectInvoiceType(this.value);
            }
        });
    });

    // Form submission with comprehensive validation
    document.getElementById('invoiceForm').addEventListener('submit', function(e) {
        const selectedType = document.querySelector('input[name="invoice_type"]:checked');

        // Show loading state
        const submitButton = e.submitter;
        const originalText = submitButton ? submitButton.innerHTML : '';

        // Store the action from the clicked button
        const actionValue = submitButton ? submitButton.value : 'save_draft';
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Processing...';
        }

        // Helper function to handle validation errors inline
        const handleValidationError = (field, message, focusOnField = true) => {
            e.preventDefault();
            console.log(field);
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }

            // Show inline field error
            if (field) {
                showFieldError(field, message);
                if (focusOnField) {
                    field.focus();
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                // For general errors without specific field, show at top of form
                showGeneralError(message);
            }

            return false;
        };

        // 1. Invoice Type Validation
        if (!selectedType) {
            const upload_invoice_pdf = document.getElementById('upload_invoice_pdf');
            return handleValidationError(upload_invoice_pdf, 'Please select an invoice type');
        }

        // Set the hidden type field
        document.getElementById('selected_type').value = selectedType.value;

        // 2. Basic Field Validation
        const agencyId = document.getElementById('agency_id');
        if (!agencyId || !agencyId.value) {
            return handleValidationError(agencyId, 'Please select an agency');
        }

        const dueDate = document.getElementById('due_date');
        if (!dueDate || !dueDate.value) {
            return handleValidationError(dueDate, 'Please select a due date');
        }

        // Validate due date format (past dates are explicitly allowed)
        const dueDateValue = new Date(dueDate.value);
        if (isNaN(dueDateValue.getTime())) {
            return handleValidationError(dueDate, 'Please enter a valid due date');
        }else{
            // Get today's date (without time)
            const today_date = new Date();
            today_date.setHours(0, 0, 0, 0);

            // Compare: due date must be today or future
            if (dueDateValue < today_date) {
                return handleValidationError(dueDate, 'Past due dates are not allowed');
            }
        }


        // Clear any browser validation that might prevent past dates
        dueDate.setCustomValidity('');

        // 3. Invoice Type Specific Validation
        if (selectedType.value === 'uploaded_pdf') {
            const pdfFile = document.getElementById('pdf_file');
            const pdfAmount = document.getElementById('pdf_amount');

            if (!pdfFile || !pdfFile.files || pdfFile.files.length === 0) {
                return handleValidationError(pdfFile, 'Please select a PDF file to upload');
            }

            // Validate file type
            const file = pdfFile.files[0];
            if (!file.type.includes('pdf')) {
                return handleValidationError(pdfFile, 'Please upload a valid PDF file');
            }

            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                return handleValidationError(pdfFile, 'PDF file size must be less than 10MB');
            }

            // Validate amount
            if (!pdfAmount || !pdfAmount.value || parseFloat(pdfAmount.value) < 0.01) {
                return handleValidationError(pdfAmount, 'Please enter a valid amount (minimum $0.01) for the PDF invoice');
            }
        }

        if (selectedType.value === 'quick') {
            const quickAmount = document.getElementById('quick_amount');
            if (!quickAmount || !quickAmount.value || parseFloat(quickAmount.value) < 0.01) {
                return handleValidationError(quickAmount, 'Please enter a valid amount (minimum $0.01) for the quick invoice');
            }

            // Validate tax and discount percentages
            const taxPercentage = document.getElementById('quick_tax');
            if (taxPercentage && taxPercentage.value) {
                const tax = parseFloat(taxPercentage.value);
                if (tax < 0 || tax > 100) {
                    return handleValidationError(taxPercentage, 'Tax percentage must be between 0 and 100');
                }
            }

            const discountPercentage = document.getElementById('quick_discount');
            if (discountPercentage && discountPercentage.value) {
                const discount = parseFloat(discountPercentage.value);
                if (discount < 0 || discount > 100) {
                    return handleValidationError(discountPercentage, 'Discount percentage must be between 0 and 100');
                }
            }
        }

        // Restore failsafe timeout for successful submissions
        setTimeout(() => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        }, 10000);

        // 4. Detailed Invoice Validation
        if (selectedType.value === 'detailed') {
            const items = document.querySelectorAll('.invoice-item');
            if (items.length === 0) {
                return handleValidationError(null, 'Please add at least one invoice item');
            }

            // Validate each item according to server-side rules
            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                const itemIndex = i + 1;

                const description = item.querySelector('input[name*="[description]"]');
                const quantity = item.querySelector('input[name*="[quantity]"]');
                const unitPrice = item.querySelector('input[name*="[unit_price]"]');
                const taxPercentage = item.querySelector('input[name*="[tax_percentage]"]');
                const discountPercentage = item.querySelector('input[name*="[discount_percentage]"]');

                // Description validation (required, max 255 chars)
                if (!description || !description.value.trim()) {
                    return handleValidationError(description, `Please enter a description for Item ${itemIndex}`);
                }
                if (description.value.trim().length > 255) {
                    return handleValidationError(description, `Description for Item ${itemIndex} must be less than 255 characters`);
                }

                // Quantity validation (required, numeric, min 0.01)
                if (!quantity || !quantity.value) {
                    return handleValidationError(quantity, `Please enter a quantity for Item ${itemIndex}`);
                }
                const quantityValue = parseFloat(quantity.value);
                if (isNaN(quantityValue) || quantityValue < 0.01) {
                    return handleValidationError(quantity, `Quantity for Item ${itemIndex} must be at least 0.01`);
                }

                // Unit price validation (required, numeric, min 0.01)
                if (!unitPrice || !unitPrice.value) {
                    return handleValidationError(unitPrice, `Please enter a unit price for Item ${itemIndex}`);
                }
                const unitPriceValue = parseFloat(unitPrice.value);
                if (isNaN(unitPriceValue) || unitPriceValue < 0.01) {
                    return handleValidationError(unitPrice, `Unit price for Item ${itemIndex} must be at least $0.01`);
                }

                // Tax percentage validation (optional, numeric, 0-100)
                if (taxPercentage && taxPercentage.value) {
                    const taxValue = parseFloat(taxPercentage.value);
                    if (isNaN(taxValue) || taxValue < 0 || taxValue > 100) {
                        return handleValidationError(taxPercentage, `Tax percentage for Item ${itemIndex} must be between 0 and 100`);
                    }
                }

                // Discount percentage validation (optional, numeric, 0-100)
                if (discountPercentage && discountPercentage.value) {
                    const discountValue = parseFloat(discountPercentage.value);
                    if (isNaN(discountValue) || discountValue < 0 || discountValue > 100) {
                        return handleValidationError(discountPercentage, `Discount percentage for Item ${itemIndex} must be between 0 and 100`);
                    }
                }
            }
        }

        // All validation passed, form can be submitted
        // Ensure action is preserved by adding it to a hidden field as backup
        let actionInput = document.querySelector('input[name="action"]');
        if (!actionInput) {
            actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            this.appendChild(actionInput);
        }
        actionInput.value = actionValue;
        // Let the form submit naturally with the action parameter
        return true;
    });

    // Real-time validation for better UX
    setupRealTimeValidation();

    // Calculate totals on page load if there are existing items
    if (document.querySelectorAll('.invoice-item').length > 0) {
        calculateInvoiceTotal();
    }
});

function setupRealTimeValidation() {
    // Agency selection validation
    const agencySelect = document.getElementById('agency_id');
    if (agencySelect) {
        agencySelect.addEventListener('change', function() {
            clearFieldError(this);
            if (!this.value) {
                showFieldError(this, 'Please select an agency');
            }
        });
    }

    // Due date validation
    const dueDateInput = document.getElementById('due_date');
    if (dueDateInput) {
        dueDateInput.addEventListener('change', function() {
            clearFieldError(this);
            if (!this.value) {
                showFieldError(this, 'Please select a due date');
            } else {
                const dateValue = new Date(this.value);
                if (isNaN(dateValue.getTime())) {
                    showFieldError(this, 'Please enter a valid date');
                }
            }
        });
    }

    // PDF file validation
    const pdfFileInput = document.getElementById('pdf_file');
    if (pdfFileInput) {
        pdfFileInput.addEventListener('change', function() {
            clearFieldError(this);
            if (this.files && this.files.length > 0) {
                const file = this.files[0];

                if (!file.type.includes('pdf')) {
                    showFieldError(this, 'Please upload a valid PDF file');
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    showFieldError(this, 'File size must be less than 10MB');
                    return;
                }

                showFieldSuccess(this, 'Valid PDF file selected');
            }
        });
    }

    // PDF amount validation
    const pdfAmountInput = document.getElementById('pdf_amount');
    if (pdfAmountInput) {
        pdfAmountInput.addEventListener('input', function() {
            clearFieldError(this);
            const value = parseFloat(this.value);
            if (this.value && (isNaN(value) || value < 0.01)) {
                showFieldError(this, 'Amount must be at least $0.01');
            } else if (this.value && value >= 0.01) {
                showFieldSuccess(this, 'Valid amount entered');
            }
        });
    }

    // Quick amount validation
    const quickAmountInput = document.getElementById('quick_amount');
    if (quickAmountInput) {
        quickAmountInput.addEventListener('input', function() {
            clearFieldError(this);
            const value = parseFloat(this.value);
            if (this.value && (isNaN(value) || value < 0.01)) {
                showFieldError(this, 'Amount must be at least $0.01');
            } else if (this.value && value >= 0.01) {
                showFieldSuccess(this, 'Valid amount entered');
            }
        });
    }

    // Tax and discount percentage validation
    const taxInputs = document.querySelectorAll('input[name*="tax_percentage"], #quick_tax');
    taxInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearFieldError(this);
            if (this.value) {
                const value = parseFloat(this.value);
                if (isNaN(value) || value < 0 || value > 100) {
                    showFieldError(this, 'Tax percentage must be between 0 and 100');
                }
            }
        });
    });

    const discountInputs = document.querySelectorAll('input[name*="discount_percentage"], #quick_discount');
    discountInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearFieldError(this);
            if (this.value) {
                const value = parseFloat(this.value);
                if (isNaN(value) || value < 0 || value > 100) {
                    showFieldError(this, 'Discount percentage must be between 0 and 100');
                }
            }
        });
    });
}

function showFieldError(field, message) {
    if (!field) return;

    clearFieldError(field);
    field.classList.add('is-invalid');

    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block';
    errorDiv.innerHTML = `<i class="mdi mdi-alert-circle me-1"></i>${message}`;
    errorDiv.setAttribute('data-field-error', field.id || field.name);

    // Insert after the field or its parent input group
    const parent = field.closest('.input-group') || field.closest('.form-group') || field;
    parent.parentNode.insertBefore(errorDiv, parent.nextSibling);

    // Add some styling to make the error more visible
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.style.fontSize = '0.875rem';
}

function showFieldSuccess(field, message) {
    clearFieldError(field);
    field.classList.add('is-valid');

    const successDiv = document.createElement('div');
    successDiv.className = 'valid-feedback';
    successDiv.textContent = message;
    successDiv.setAttribute('data-field-success', field.id || field.name);

    // Insert after the field or its parent input group
    const parent = field.closest('.input-group') || field;
    parent.parentNode.insertBefore(successDiv, parent.nextSibling);

    // Auto-hide success message after 3 seconds
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.remove();
            field.classList.remove('is-valid');
        }
    }, 3000);
}

function clearFieldError(field) {
    field.classList.remove('is-invalid', 'is-valid');

    // Remove existing error/success messages
    const fieldId = field.id || field.name;
    const existingError = document.querySelector(`[data-field-error="${fieldId}"]`);
    const existingSuccess = document.querySelector(`[data-field-success="${fieldId}"]`);

    if (existingError) {
        existingError.remove();
    }
    if (existingSuccess) {
        existingSuccess.remove();
    }
}

function showGeneralError(message) {
    // Remove any existing general error
    const existingError = document.querySelector('.general-validation-error');
    if (existingError) {
        existingError.remove();
    }

    // Create general error alert
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger general-validation-error';
    errorDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="mdi mdi-alert-circle me-2"></i>
            <span>${message}</span>
        </div>
    `;

    // Insert at the top of the first form section
    const firstCard = document.querySelector('.card');
    if (firstCard) {
        firstCard.parentNode.insertBefore(errorDiv, firstCard);
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }
}

function selectInvoiceType(type) {
    // Update radio button
    const radioId = 'type_' + (type === 'uploaded_pdf' ? 'pdf' : type);
    document.getElementById(radioId).checked = true;
    document.getElementById('selected_type').value = type;

    // Remove selected class from all cards
    document.querySelectorAll('.invoice-type-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Add selected class to chosen card with animation
    const selectedCard = document.querySelector(`.invoice-type-card[data-type="${type}"]`);
    selectedCard.classList.add('selected'); 

    // Hide all sections with fade out effect
    const allSections = document.querySelectorAll('#pdf-section, #quick-section, #detailed-section');
    allSections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(15px)';
        setTimeout(() => {
            section.style.display = 'none';
        }, 200);
    });

    // Show selected section with fade in effect
    const sectionMap = {
        'uploaded_pdf': 'pdf-section',
        'quick': 'quick-section',
        'detailed': 'detailed-section'
    };

    const sectionId = sectionMap[type];
    if (sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            setTimeout(() => {
                section.style.display = 'block';
                section.style.opacity = '0';
                section.style.transform = 'translateY(15px)';

                // Animate in
                setTimeout(() => {
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                    section.style.transition = 'all 0.3s ease';
                }, 10);
            }, 200);
        } else {
            console.error('Section not found:', sectionId);
        }
    }

    // Auto-add first item for detailed invoice if no items exist
    if (type === 'detailed' && document.querySelectorAll('.invoice-item').length === 0) {
        setTimeout(() => {
            addInvoiceItem();
        }, 300);
    }

    // Smooth scroll to the next section
    setTimeout(() => {
        const section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
                inline: 'nearest'
            });
        }
    }, 400);
}

function addInvoiceItem() {
    itemCounter++;
    const itemHtml = `
        <div class="invoice-item" id="item-${itemCounter}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Item ${itemCounter}</h6>
                <button type="button" class="btn btn-sm btn-danger invoice-action-btn" onclick="removeInvoiceItem(${itemCounter})">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" name="items[${itemCounter}][description]" class="form-control item-input">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[${itemCounter}][quantity]" class="form-control item-quantity"
                               value="1" onchange="calculateItemTotal(${itemCounter})">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Unit Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="items[${itemCounter}][unit_price]" class="form-control item-price"
                                   value="0" onchange="calculateItemTotal(${itemCounter})">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Tax (%)</label>
                        <input type="number" name="items[${itemCounter}][tax_percentage]" class="form-control item-tax"
                               value="0" step="0.01" onchange="calculateItemTotal(${itemCounter})">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <div class="form-control-plaintext fw-bold" id="item-total-${itemCounter}">$0.00</div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('invoice-items').insertAdjacentHTML('beforeend', itemHtml);

    // Setup validation for the newly added item
    setupItemValidation(itemCounter);

    calculateInvoiceTotal();
}

function setupItemValidation(itemId) {
    // Description validation
    const descriptionInput = document.querySelector(`input[name="items[${itemId}][description]"]`);
    if (descriptionInput) {
        descriptionInput.addEventListener('input', function() {
            clearFieldError(this);
            if (!this.value.trim()) {
                showFieldError(this, 'Description is required');
            } else if (this.value.trim().length > 255) {
                showFieldError(this, 'Description must be less than 255 characters');
            }
        });
    }

    // Quantity validation
    const quantityInput = document.querySelector(`input[name="items[${itemId}][quantity]"]`);
    if (quantityInput) {
        quantityInput.addEventListener('input', function() {
            clearFieldError(this);
            const value = parseFloat(this.value);
            if (this.value && (isNaN(value) || value < 0.01)) {
                showFieldError(this, 'Quantity must be at least 0.01');
            }
        });
    }

    // Unit price validation
    const unitPriceInput = document.querySelector(`input[name="items[${itemId}][unit_price]"]`);
    if (unitPriceInput) {
        unitPriceInput.addEventListener('input', function() {
            clearFieldError(this);
            const value = parseFloat(this.value);
            if (this.value && (isNaN(value) || value < 0.01)) {
                showFieldError(this, 'Unit price must be at least $0.01');
            }
        });
    }

    // Tax percentage validation
    const taxInput = document.querySelector(`input[name="items[${itemId}][tax_percentage]"]`);
    if (taxInput) {
        taxInput.addEventListener('input', function() {
            clearFieldError(this);
            if (this.value) {
                const value = parseFloat(this.value);
                if (isNaN(value) || value < 0 || value > 100) {
                    showFieldError(this, 'Tax percentage must be between 0 and 100');
                }
            }
        });
    }
}

function removeInvoiceItem(itemId) {
    const item = document.getElementById(`item-${itemId}`);
    if (item) {
        item.remove();
        calculateInvoiceTotal();
    }
}

function calculateItemTotal(itemId) {
    const quantity = parseFloat(document.querySelector(`input[name="items[${itemId}][quantity]"]`).value) || 0;
    const unitPrice = parseFloat(document.querySelector(`input[name="items[${itemId}][unit_price]"]`).value) || 0;
    const taxPercentage = parseFloat(document.querySelector(`input[name="items[${itemId}][tax_percentage]"]`).value) || 0;

    const subtotal = quantity * unitPrice;
    const taxAmount = (subtotal * taxPercentage) / 100;
    const total = subtotal + taxAmount;
    document.getElementById(`item-total-${itemId}`).textContent = '$' + total.toFixed(2);
    calculateInvoiceTotal();
}

function calculateInvoiceTotal() {
    let subtotal = 0;

    // Calculate subtotal from all items
    document.querySelectorAll('.invoice-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const unitPrice = parseFloat(item.querySelector('.item-price').value) || 0;
        subtotal += quantity * unitPrice;
    });

    const overallTaxPercentage = parseFloat(document.getElementById('detailed_tax').value) || 0;
    const overallDiscountPercentage = parseFloat(document.getElementById('detailed_discount').value) || 0;

    const taxAmount = (subtotal * overallTaxPercentage) / 100;
    const discountAmount = (subtotal * overallDiscountPercentage) / 100;
    const total = subtotal + taxAmount - discountAmount;

    document.getElementById('invoice-subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('invoice-tax').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('invoice-discount').textContent = '$' + discountAmount.toFixed(2);
    document.getElementById('invoice-total').textContent = '$' + total.toFixed(2);
}

// Add event listeners for overall tax and discount changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('detailed_tax').addEventListener('change', calculateInvoiceTotal);
    document.getElementById('detailed_discount').addEventListener('change', calculateInvoiceTotal);
});

// Excel import functionality
let importedData = [];

function handleExcelImport(event) {
    const file = event.target.files[0];
    if (!file) {
        return;
    }
    // Ensure detailed invoice type is selected
    if (!document.getElementById('type_detailed').checked) {
        selectInvoiceType('detailed');
    }

    const reader = new FileReader();

    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

            parseExcelData(jsonData);
        } catch (error) {
            showConfirmationModal('File Error', 'Error reading Excel file. Please make sure it\'s a valid Excel file.', 'error', function() {});
            console.error('Excel import error:', error);
        }
    };

    if (file.name.toLowerCase().endsWith('.csv')) {
        reader.onload = function(e) {
            try {
                const csvData = e.target.result;
                const lines = csvData.split('\n');
                const jsonData = lines.map(line => line.split(','));
                parseExcelData(jsonData);
            } catch (error) {
                showConfirmationModal('File Error', 'Error reading CSV file. Please make sure it\'s a valid CSV file.', 'error', function() {});
                console.error('CSV import error:', error);
            }
        };
        reader.readAsText(file);
    } else {
        reader.readAsArrayBuffer(file);
    }
}

function parseExcelData(data) {
    if (!data || data.length < 2) {
        showConfirmationModal('File Error', 'Excel file must contain at least a header row and one data row.', 'error', function() {});
        return;
    }

    const headers = data[0].map(h => h ? h.toString().toLowerCase().trim() : '');
    const rows = data.slice(1);

    // Find column indices (flexible header matching)
    const descIndex = findColumnIndex(headers, ['description', 'desc', 'item', 'service', 'product']);
    const qtyIndex = findColumnIndex(headers, ['quantity', 'qty', 'amount']);
    const priceIndex = findColumnIndex(headers, ['price', 'unit price', 'rate', 'cost']);
    const taxIndex = findColumnIndex(headers, ['tax', 'tax%', 'tax percent', 'tax percentage']);

    if (descIndex === -1) {
        showConfirmationModal('File Error', 'Could not find a description column. Please ensure your Excel file has a column with headers like "Description", "Item", or "Service".', 'error', function() {});
        return;
    }

    importedData = [];
    const previewBody = document.getElementById('import-preview-body');
    previewBody.innerHTML = '';

    rows.forEach((row, index) => {
        if (row.length === 0 || !row[descIndex]) return; // Skip empty rows

        const item = {
            description: row[descIndex] || '',
            quantity: parseFloat(row[qtyIndex]) || 1,
            unit_price: parseFloat(row[priceIndex]) || 0,
            tax_percentage: parseFloat(row[taxIndex]) || 0
        };

        const total = (item.quantity * item.unit_price) * (1 + item.tax_percentage / 100);

        importedData.push(item);

        // Add preview row with better styling
        const previewRow = document.createElement('tr');
        previewRow.innerHTML = `
            <td class="px-3 py-2">${item.description}</td>
            <td class="px-2 py-2 text-center">${item.quantity}</td>
            <td class="px-2 py-2 text-end">$${item.unit_price.toFixed(2)}</td>
            <td class="px-2 py-2 text-center">${item.tax_percentage}%</td>
            <td class="px-2 py-2 text-end fw-bold">$${total.toFixed(2)}</td>
        `;
        previewBody.appendChild(previewRow);
    });

    if (importedData.length === 0) {
        showConfirmationModal('File Error', 'No valid data found in the Excel file.', 'error', function() {});
        return;
    }

    // Update item count and show preview section
    document.getElementById('import-count').textContent = importedData.length;
    const previewSection = document.getElementById('import-preview');
    if (!previewSection) {
        console.error('Import preview section not found!');
        return;
    }
    previewSection.style.display = 'block';
    // Scroll to preview section to make it visible
    setTimeout(() => {
        previewSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);

    // Clear file input
    document.getElementById('excel-import').value = '';
}

function findColumnIndex(headers, possibleNames) {
    for (const name of possibleNames) {
        const index = headers.findIndex(header => header.includes(name));
        if (index !== -1) return index;
    }
    return -1;
}

function confirmImport() {
    if (importedData.length === 0) {
        showConfirmationModal('No Data', 'No data to import', 'warning', function() {});
        return;
    }

    // Check if there are existing items and show appropriate confirmation
    const existingItems = document.querySelectorAll('.invoice-item').length;
    if (existingItems > 0) {
        showConfirmationModal(
            'Import Items',
            `You have ${existingItems} existing item(s). How would you like to proceed with importing ${importedData.length} new items?`,
            'question',
            function(action) {
                if (action === 'add') {
                    processImport(true); // Add to existing
                } else if (action === 'replace') {
                    processImport(false); // Replace existing
                }
            },
            true // Show both add and replace buttons
        );
    } else {
        // No existing items, just confirm import
        showConfirmationModal(
            'Confirm Import',
            `Are you sure you want to import ${importedData.length} items to your invoice?`,
            'question',
            function(action) {
                if (action === 'confirm') {
                    processImport(true);
                }
            }
        );
    }
}

function processImport(addToExisting) {
    const confirmReplace = addToExisting;

    if (!confirmReplace) {
        // Clear existing items
        document.getElementById('invoice-items').innerHTML = '';
        itemCounter = 0;
    }
    let totalImportLength = importedData.length;
    // Add each imported item to the invoice
    importedData.forEach((item, index) => {
        itemCounter++;
        let totalAmount = '$0.00';
        const tot_quantity = parseFloat(item.quantity) || 0;
        const tot_unitPrice = parseFloat(item.unit_price) || 0;
        const tot_taxPercentage = parseFloat(item.tax_percentage) || 0;
        const tot_subtotal = tot_quantity * tot_unitPrice;
        const tot_taxAmount = (tot_subtotal * tot_taxPercentage) / 100;
        totalAmount = tot_subtotal + tot_taxAmount;
        console.log(totalAmount);
        const itemHtml = `
            <div class="invoice-item imported-item" id="item-${itemCounter}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Item ${itemCounter} <span class="badge badge-success">Imported</span></h6>
                    <button type="button" class="btn btn-sm btn-danger invoice-action-btn" onclick="removeInvoiceItem(${itemCounter})">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <input type="text" name="items[${itemCounter}][description]" class="form-control item-input"
                                   value="${item.description.replace(/"/g, '&quot;')}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="items[${itemCounter}][quantity]" class="form-control item-quantity"
                                   value="${item.quantity}" min="0.01" step="0.01" onchange="calculateItemTotal(${itemCounter})">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Unit Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="items[${itemCounter}][unit_price]" class="form-control item-price"
                                       value="${item.unit_price}" onchange="calculateItemTotal(${itemCounter})">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Tax (%)</label>
                            <input type="number" name="items[${itemCounter}][tax_percentage]" class="form-control item-tax"
                                   value="${item.tax_percentage}" step="0.01" onchange="calculateItemTotal(${itemCounter})">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Total</label>
                            <div class="form-control-plaintext fw-bold" id="item-total-${itemCounter}">${totalAmount}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('invoice-items').insertAdjacentHTML('beforeend', itemHtml);

        // Calculate total for this item after a brief delay to ensure DOM is updated
        setTimeout(() => {
            calculateItemTotal(itemCounter);
        }, 50);
    });

    // Hide import preview
    cancelImport();

    // Ensure detailed invoice type is selected and visible
    selectInvoiceType('detailed');

    // Update the visual state to ensure the detailed section is highlighted
    document.querySelectorAll('.invoice-type-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.querySelector('.invoice-type-card[data-type="detailed"]').classList.add('selected');

    // Recalculate totals after a brief delay and ensure form fields are properly set
    setTimeout(() => {
        calculateInvoiceTotal();

        // Trigger change event on invoice type to ensure proper form state
        const detailedRadio = document.getElementById('type_detailed');
        if (detailedRadio) {
            detailedRadio.dispatchEvent(new Event('change'));
        }
    }, 100);

    // Make sure the detailed section is visible and scroll to it
    setTimeout(() => {
        const detailedSection = document.getElementById('detailed-section');
        if (detailedSection) {
            detailedSection.style.display = 'block'; // Force display in case selectInvoiceType didn't work
            detailedSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            console.error('Detailed section not found!');
        }
    }, 200);

    // Remove imported styling after animation completes
    setTimeout(() => {
        document.querySelectorAll('.imported-item').forEach(item => {
            item.classList.remove('imported-item');
            const badge = item.querySelector('.badge');
            if (badge) badge.remove();
        });
    }, 3000);

    // Show success message with better formatting
    setTimeout(() => {
        showConfirmationModal(
            'Import Successful!',
            `Successfully imported ${totalImportLength} items to your detailed invoice. The items are now visible in the Detailed Invoice section below.`,
            'success',
            function() {}
        );
    }, 300);
}

function cancelImport() {
    document.getElementById('import-preview').style.display = 'none';
    document.getElementById('import-preview-body').innerHTML = '';
    importedData = [];
}

// Download sample Excel file
function downloadSampleFile() {
    // Create sample data
    const sampleData = [
        ['Description', 'Quantity', 'Unit Price', 'Tax %'],
        ['DRUG SCREEN', 10, 150.00, 8.5],
        ['MMR VACCINE', 1, 500.00, 8.5],
        ['EXAM', 12, 25.00, 0],
        ['EXAM, TITERS', 1, 100.00, 8.5],
        ['EXAM, DRUG , FLU VACCINE', 1, 800.00, 8.5]
    ];

    // Create workbook and worksheet
    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet(sampleData);

    // Set column widths
    worksheet['!cols'] = [
        { width: 30 }, // Description
        { width: 10 }, // Quantity
        { width: 12 }, // Unit Price
        { width: 10 }  // Tax %
    ];

    // Style the header row
    const headerRange = XLSX.utils.decode_range(worksheet['!ref']);
    for (let col = headerRange.s.c; col <= headerRange.e.c; col++) {
        const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
        if (!worksheet[cellAddress]) continue;

        worksheet[cellAddress].s = {
            font: { bold: true },
            fill: { fgColor: { rgb: "E2EFDA" } },
            border: {
                top: { style: "thin" },
                bottom: { style: "thin" },
                left: { style: "thin" },
                right: { style: "thin" }
            }
        };
    }

    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Invoice Items');

    // Add instructions sheet
    const instructionsData = [
        ['Invoice Import Instructions'],
        [''],
        ['Required Columns:'],
        ['• Description - Name or description of the item/service'],
        ['• Quantity - Number of units (optional, defaults to 1)'],
        ['• Unit Price - Price per unit (optional, defaults to 0)'],
        ['• Tax % - Tax percentage (optional, defaults to 0)'],
        [''],
        ['Column Header Variations Supported:'],
        ['Description: description, desc, item, service, product'],
        ['Quantity: quantity, qty, amount'],
        ['Price: price, unit price, rate, cost'],
        ['Tax: tax, tax%, tax percent, tax percentage'],
        [''],
        ['Notes:'],
        ['• Column headers are case-insensitive'],
        ['• Empty rows will be skipped'],
        ['• Only Description column is required'],
        ['• Supports Excel (.xlsx, .xls) and CSV files'],
        ['• Maximum recommended: 100 items per import']
    ];

    const instructionsSheet = XLSX.utils.aoa_to_sheet(instructionsData);
    instructionsSheet['!cols'] = [{ width: 50 }];

    // Style the title
    if (instructionsSheet['A1']) {
        instructionsSheet['A1'].s = {
            font: { bold: true, sz: 16 },
            fill: { fgColor: { rgb: "D9EAD3" } }
        };
    }

    XLSX.utils.book_append_sheet(workbook, instructionsSheet, 'Instructions');

    // Generate filename with current date
    const today = new Date();
    const dateString = today.getFullYear() + '-' +
                      String(today.getMonth() + 1).padStart(2, '0') + '-' +
                      String(today.getDate()).padStart(2, '0');
    const filename = `invoice_import_sample_${dateString}.xlsx`;

    // Download the file
    XLSX.writeFile(workbook, filename);
}

// Enhanced Confirmation Modal Function
function showConfirmationModal(title, message, type = 'question', callback = null, showBothButtons = false) {
    // Remove existing modal if any
    const existingModal = document.getElementById('confirmationModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Define icons and colors based on type
    const typeConfig = {
        'success': { icon: 'mdi-check-circle', color: 'success', bgColor: '#d4edda' },
        'warning': { icon: 'mdi-alert-circle', color: 'warning', bgColor: '#fff3cd' },
        'error': { icon: 'mdi-close-circle', color: 'danger', bgColor: '#f8d7da' },
        'question': { icon: 'mdi-help-circle', color: 'primary', bgColor: '#d1ecf1' },
        'info': { icon: 'mdi-information', color: 'info', bgColor: '#d1ecf1' }
    };

    const config = typeConfig[type] || typeConfig['question'];

    // Create modal HTML
    const modalHTML = `
        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow">
                    <div class="modal-header p-0">
                        <div class="d-flex align-items-center w-100">
                            <div class="rounded-circle p-2 me-3" style="background-color: rgba(255,255,255,0.8);">
                                <i class="mdi ${config.icon} text-${config.color}" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5 class="modal-title mb-0 text-${config.color}" id="confirmationModalLabel">${title}</h5>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pt-3">
                        <p class="mb-0">${message}</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        ${showBothButtons ? `
                            <button type="button" class="btn btn-secondary invoice-action-btn btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning invoice-action-btn btn-sm me-2" onclick="handleConfirmAction('replace')">Replace All</button>
                            <button type="button" class="btn btn-success invoice-action-btn btn-sm" onclick="handleConfirmAction('add')">Add to Existing</button>
                        ` : type === 'question' ? `
                            <button type="button" class="btn btn-secondary invoice-action-btn btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-${config.color} invoice-action-btn btn-sm" onclick="handleConfirmAction('confirm')">Confirm</button>
                        ` : `
                            <button type="button" class="btn btn-${config.color} invoice-action-btn btn-sm" data-dismiss="modal">OK</button>
                        `}
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal using jQuery (Bootstrap 4)
    const $modal = $('#confirmationModal');

    // Check if jQuery and Bootstrap modal are available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not available');
        return;
    }

    if (typeof $.fn.modal === 'undefined') {
        console.error('Bootstrap modal is not available');
        return;
    }

    $modal.modal('show');

    // Store callback for use in handleConfirmAction
    window.currentConfirmCallback = callback;

    // Auto-dismiss for success/info messages after 3 seconds
    if (type === 'success' || type === 'info') {
        setTimeout(() => {
            $modal.modal('hide');
        }, 3000);
    }

    // Clean up when modal is hidden
    $modal.on('hidden.bs.modal', function () {
        $(this).remove();
        window.currentConfirmCallback = null;
    });
}

// Handle confirmation actions
function handleConfirmAction(action) {
    $('#confirmationModal').modal('hide');

    if (window.currentConfirmCallback) {
        window.currentConfirmCallback(action);
    }
}
</script>

    </div>
</div>

@include('include/footer')