@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper invoice-module-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Edit Invoice {{ $invoice->invoice_number }}</h5>
            <div class="page-rightbtns">
                <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-secondary invoice-action-btn btn-sm me-2">
                    <i class="mdi mdi-arrow-left me-2"></i>Back to Invoice
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-primary invoice-action-btn btn-sm">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>All Invoices
                </a>
            </div>
        </div>

        @if(!$invoice->canBeEdited())
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-alert-triangle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Cannot Edit Invoice</strong><br>
                        <span class="text-muted">This invoice cannot be edited because it has already been {{ $invoice->status }}.</span>
                    </div>
                </div>
            </div>
        @endif

        @if($invoice->canBeEdited())
            <div class="row">
                <div class="col-12">
                <form id="invoiceForm" method="POST" action="{{ route('admin.invoices.update', $invoice) }}">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="card invoice-card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="mdi mdi-information me-2 text-black"></i>Basic Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="agency_id" class="form-label">Agency <span class="text-danger">*</span></label>
                                        <select name="agency_id" id="agency_id" class="form-control" required>
                                            <option value="">Select Agency</option>
                                            @foreach($agencies as $agency)
                                                <option value="{{ $agency->id }}" {{ $invoice->agency_id == $agency->id ? 'selected' : '' }}>
                                                    {{ $agency->agency_name }} ({{ $agency->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                        <input type="date" name="due_date" id="due_date" class="form-control"
                                               value="{{ $invoice->due_date->format('Y-m-d') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="invoice_number" class="form-label">Invoice Number</label>
                                        <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                                               value="{{ $invoice->invoice_number }}" readonly>
                                        <div class="form-text">Invoice number cannot be changed</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" name="title" id="title" class="form-control"
                                               value="{{ $invoice->title }}" placeholder="Invoice title or subject">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                          placeholder="Invoice description or notes">{{ $invoice->description }}</textarea>
                            </div>
                        </div>
                    </div>

                    @if($invoice->type === 'uploaded_pdf')
                        <!-- PDF Upload Information -->
                        <div class="card invoice-card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="mdi mdi-file-pdf-box me-2"></i>PDF Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information me-2"></i>
                                    This is an uploaded PDF invoice. The PDF file cannot be changed, but you can update the basic information above.
                                </div>
                                @if($invoice->pdf_path)
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-file-pdf-box fa-2x text-danger me-3"></i>
                                        <div>
                                            <strong>Current PDF:</strong> {{ basename($invoice->pdf_path) }}<br>
                                            <a href="{{ route('admin.invoices.download', $invoice) }}" class="btn btn-sm btn-outline-primary mt-1">
                                                <i class="mdi mdi-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @elseif($invoice->type === 'quick')
                        <!-- Quick Invoice Section -->
                        <div class="card invoice-card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="mdi mdi-flash me-2"></i>Quick Invoice Details
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="total_amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="total_amount" id="total_amount" class="form-control"
                                                       value="{{ $invoice->subtotal }}" step="0.01" min="0.01" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="tax_percentage" class="form-label">Tax (%)</label>
                                            <input type="number" name="tax_percentage" id="tax_percentage" class="form-control"
                                                   value="{{ $invoice->tax_percentage }}" step="0.01" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="discount_percentage" class="form-label">Discount (%)</label>
                                            <input type="number" name="discount_percentage" id="discount_percentage" class="form-control"
                                                   value="{{ $invoice->discount_percentage }}" step="0.01" min="0" max="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">Calculated Total</h6>
                                                <div class="d-flex justify-content-between">
                                                    <span>Subtotal:</span>
                                                    <span id="calc-subtotal">${{ number_format($invoice->subtotal, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Tax:</span>
                                                    <span id="calc-tax">${{ number_format($invoice->tax_amount, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Discount:</span>
                                                    <span id="calc-discount">${{ number_format($invoice->discount_amount, 2) }}</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between fw-bold">
                                                    <span>Total:</span>
                                                    <span id="calc-total">${{ number_format($invoice->total_amount, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif($invoice->type === 'detailed')
                        <!-- Detailed Invoice Section -->
                        <div class="card invoice-card mb-4">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">
                                        <i class="mdi mdi-format-list-bulleted me-2"></i>Invoice Items
                                    </h6>
                                    <button type="button" class="btn btn-sm invoice-btn-primary text-white" onclick="addInvoiceItem()">
                                        <i class="mdi mdi-plus me-1"></i>Add Item
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="invoice-items">
                                    @foreach($invoice->items as $index => $item)
                                        <div class="invoice-item" id="item-{{ $index + 1 }}">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Item {{ $index + 1 }}</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInvoiceItem({{ $index + 1 }})">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                                        <input type="text" name="items[{{ $index + 1 }}][description]" class="form-control item-input"
                                                               value="{{ $item->description }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label class="form-label">Quantity</label>
                                                        <input type="number" name="items[{{ $index + 1 }}][quantity]" class="form-control item-quantity"
                                                               value="{{ $item->quantity }}" min="0.01" step="0.01" onchange="calculateItemTotal({{ $index + 1 }})">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label class="form-label">Unit Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" name="items[{{ $index + 1 }}][unit_price]" class="form-control item-price"
                                                                   value="{{ $item->unit_price }}" min="0.01" step="0.01" onchange="calculateItemTotal({{ $index + 1 }})">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label class="form-label">Tax (%)</label>
                                                        <input type="number" name="items[{{ $index + 1 }}][tax_percentage]" class="form-control item-tax"
                                                               value="{{ $item->tax_percentage }}" min="0" max="100" step="0.01" onchange="calculateItemTotal({{ $index + 1 }})">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label class="form-label">Total</label>
                                                        <div class="form-control-plaintext fw-bold" id="item-total-{{ $index + 1 }}">${{ number_format($item->line_total, 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="detailed_tax" class="form-label">Overall Tax (%)</label>
                                                    <input type="number" name="tax_percentage" id="detailed_tax" class="form-control"
                                                           value="{{ $invoice->tax_percentage }}" step="0.01" min="0" max="100">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="detailed_discount" class="form-label">Overall Discount (%)</label>
                                                    <input type="number" name="discount_percentage" id="detailed_discount" class="form-control"
                                                           value="{{ $invoice->discount_percentage }}" step="0.01" min="0" max="100">
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
                                                    <span id="invoice-subtotal">${{ number_format($invoice->subtotal, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Tax:</span>
                                                    <span id="invoice-tax">${{ number_format($invoice->tax_amount, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Discount:</span>
                                                    <span id="invoice-discount">${{ number_format($invoice->discount_amount, 2) }}</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between fw-bold">
                                                    <span>Total:</span>
                                                    <span id="invoice-total">${{ number_format($invoice->total_amount, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Terms & Conditions -->
                    <div class="card invoice-card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="mdi mdi-file-document me-2"></i>Terms & Conditions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                                <textarea name="terms_conditions" id="terms_conditions" class="form-control" rows="4"
                                          placeholder="Enter payment terms, conditions, and other notes">{{ $invoice->terms_conditions }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="card invoice-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-secondary invoice-action-btn btn-sm">
                                    <i class="mdi mdi-arrow-left me-2"></i>Cancel
                                </a>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary invoice-action-btn text-white btn-sm">
                                        <i class="mdi mdi-content-save me-2"></i>Update Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card invoice-card">
                        <div class="card-body text-center py-5">
                            <i class="mdi mdi-lock" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="text-muted mt-3">Invoice Cannot Be Edited</h5>
                            <p class="text-muted">This invoice cannot be edited because it has already been {{ $invoice->status }}.</p>
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn invoice-btn-primary text-white invoice-action-btn btn-sm">
                                <i class="mdi mdi-eye me-2"></i>View Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>


<style>
.invoice-item {
    border: 1px solid #e3f2fd;
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
    transition: all 0.3s ease;
}

.invoice-item:hover {
    border-color: #2196f3;
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);
    transform: translateY(-2px);
}

.invoice-item:last-child {
    margin-bottom: 0;
}

.invoice-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 12px;
    overflow: hidden;
}

.invoice-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1rem 1.25rem;
}

.invoice-card-header .card-title {
    color: white;
    font-weight: 600;
}

.form-control:focus {
    border-color: #2196f3;
    box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
}

.btn.invoice-btn-primary {
    border: none;
    border-radius: 25px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn.invoice-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.bg-light {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%) !important;
    border: 1px solid #e3f2fd;
    border-radius: 8px;
}

.input-group-text {
    background: #f8f9fa;
    border-color: #dee2e6;
}

.form-control-plaintext {
    color: #2196f3 !important;
    font-weight: 600;
}

.alert {
    border-radius: 12px;
    border: none;
}

/* Mobile Responsive Improvements */
@media (max-width: 768px) {
    .invoice-item .row {
        margin: 0;
    }

    .invoice-item .col-md-4,
    .invoice-item .col-md-2 {
        padding: 0.25rem;
        margin-bottom: 0.75rem;
    }

    .page-rightbtns {
        flex-direction: column;
        gap: 0.5rem;
    }

    .page-rightbtns .btn {
        width: 100%;
        text-align: center;
    }

    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .d-flex.justify-content-between .d-flex {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .invoice-card-header {
        padding: 0.75rem 1rem;
    }

    .invoice-card-header .d-flex {
        flex-direction: column;
        gap: 0.5rem;
    }

    .invoice-item {
        padding: 1rem;
    }
}
</style>



<script>
let itemCounter = {{ $invoice->items->count() }};

document.addEventListener('DOMContentLoaded', function() {
    @if($invoice->type === 'quick')
        // Quick invoice calculations
        ['total_amount', 'tax_percentage', 'discount_percentage'].forEach(field => {
            document.getElementById(field).addEventListener('input', calculateQuickTotal);
        });
        // Calculate initial totals
        calculateQuickTotal();
    @endif

    @if($invoice->type === 'detailed')
        // Detailed invoice calculations
        document.getElementById('detailed_tax').addEventListener('change', calculateInvoiceTotal);
        document.getElementById('detailed_discount').addEventListener('change', calculateInvoiceTotal);

        // Calculate initial totals for existing items
        document.querySelectorAll('.invoice-item').forEach((item, index) => {
            const itemId = index + 1;
            calculateItemTotal(itemId);
        });
        calculateInvoiceTotal();
    @endif
});

@if($invoice->type === 'quick')
function calculateQuickTotal() {
    const subtotal = parseFloat(document.getElementById('total_amount').value) || 0;
    const taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
    const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;

    const taxAmount = (subtotal * taxPercentage) / 100;
    const discountAmount = (subtotal * discountPercentage) / 100;
    const total = subtotal + taxAmount - discountAmount;

    document.getElementById('calc-subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('calc-tax').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('calc-discount').textContent = '$' + discountAmount.toFixed(2);
    document.getElementById('calc-total').textContent = '$' + total.toFixed(2);
}
@endif

@if($invoice->type === 'detailed')
function addInvoiceItem() {
    itemCounter++;
    const itemHtml = `
        <div class="invoice-item" id="item-${itemCounter}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Item ${itemCounter}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInvoiceItem(${itemCounter})">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" name="items[${itemCounter}][description]" class="form-control item-input" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[${itemCounter}][quantity]" class="form-control item-quantity"
                               value="1" min="0.01" step="0.01" onchange="calculateItemTotal(${itemCounter})">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Unit Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="items[${itemCounter}][unit_price]" class="form-control item-price"
                                   min="0.01" step="0.01" onchange="calculateItemTotal(${itemCounter})">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Tax (%)</label>
                        <input type="number" name="items[${itemCounter}][tax_percentage]" class="form-control item-tax"
                               value="0" min="0" max="100" step="0.01" onchange="calculateItemTotal(${itemCounter})">
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
    calculateInvoiceTotal();
}

function removeInvoiceItem(itemId) {
    const item = document.getElementById(`item-${itemId}`);
    if (item) {
        // Check if this is the last item
        const allItems = document.querySelectorAll('.invoice-item');
        if (allItems.length === 1) {
            showNotification('You must have at least one item in the invoice', 'error');
            return;
        }

        item.remove();
        calculateInvoiceTotal();
        showNotification('Item removed successfully', 'success');
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

// Form validation
document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    @if($invoice->type === 'detailed')
    const items = document.querySelectorAll('.invoice-item');
    if (items.length === 0) {
        e.preventDefault();
        showNotification('Please add at least one invoice item', 'error');
        return;
    }

    // Validate each item
    let hasError = false;
    items.forEach((item, index) => {
        const description = item.querySelector('input[name*="[description]"]');
        const quantity = item.querySelector('input[name*="[quantity]"]');
        const unitPrice = item.querySelector('input[name*="[unit_price]"]');

        if (!description || !description.value.trim()) {
            e.preventDefault();
            showNotification(`Please fill in the description for Item ${index + 1}`, 'error');
            hasError = true;
            return;
        }

        if (!quantity || !quantity.value || parseFloat(quantity.value) <= 0) {
            e.preventDefault();
            showNotification(`Please enter a valid quantity for Item ${index + 1}`, 'error');
            hasError = true;
            return;
        }

        if (!unitPrice || !unitPrice.value || parseFloat(unitPrice.value) < 0) {
            e.preventDefault();
            showNotification(`Please enter a valid unit price for Item ${index + 1}`, 'error');
            hasError = true;
            return;
        }
    });
    @endif

    @if($invoice->type === 'quick')
    const amount = document.getElementById('total_amount');
    if (!amount || !amount.value || parseFloat(amount.value) <= 0) {
        e.preventDefault();
        showNotification('Please enter a valid amount for the invoice', 'error');
        return;
    }
    @endif
});

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="mdi mdi-${type === 'error' ? 'alert-circle' : 'check-circle'} mr-2"></i>
            <span class="flex-grow-1">${message}</span>
            <button type="button" class="close ml-2" onclick="this.parentElement.parentElement.remove()" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
@endif
</script>

@include('include/footer')