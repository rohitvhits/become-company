@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper invoice-module-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold invoice-page-title">Invoice Management</h5>
            <div class="page-rightbtns">
                <a href="{{ route('admin.reports.invoices.index') }}" class="btn btn-info btn-sm me-2 invoice-action-btn">
                    <i class="mdi mdi-chart-bar me-2"></i>Reports
                </a>
                <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary btn-sm me-2 invoice-action-btn">
                    <i class="mdi mdi-plus me-2"></i>Create Invoice
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row admin-invoice-stats mb-3">
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card horizontal-admin-stats-card">
                    <div class="card-body d-flex align-items-center py-2 px-3">
                        <i class="mdi mdi-file-document text-primary me-2" style="font-size: 1.8rem;"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold">{{ $stats['total'] }}</h6>
                            <p class="mb-0 text-muted small">Total</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card horizontal-admin-stats-card">
                    <div class="card-body d-flex align-items-center py-2 px-3">
                        <i class="mdi mdi-pencil text-warning me-2" style="font-size: 1.8rem;"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold">{{ $stats['draft'] }}</h6>
                            <p class="mb-0 text-muted small">Draft</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card horizontal-admin-stats-card">
                    <div class="card-body d-flex align-items-center py-2 px-3">
                        <i class="mdi mdi-send text-info me-2" style="font-size: 1.8rem;"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold">{{ $stats['sent'] }}</h6>
                            <p class="mb-0 text-muted small">Sent</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card horizontal-admin-stats-card">
                    <div class="card-body d-flex align-items-center py-2 px-3">
                        <i class="mdi mdi-check text-success me-2" style="font-size: 1.8rem;"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold">{{ $stats['paid'] }}</h6>
                            <p class="mb-0 text-muted small">Paid</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card horizontal-admin-stats-card">
                    <div class="card-body d-flex align-items-center py-2 px-3">
                        <i class="mdi mdi-timer-off text-danger me-2" style="font-size: 1.8rem;"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold">{{ $stats['overdue'] }}</h6>
                            <p class="mb-0 text-muted small">Overdue</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card horizontal-admin-stats-card">
                    <div class="card-body d-flex align-items-center py-2 px-3">
                        <i class="mdi mdi-currency-usd text-success me-2" style="font-size: 1.8rem;"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold">${{ number_format($stats['total_revenue'], 0) }}</h6>
                            <p class="mb-0 text-muted small">Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card invoice-filters-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.invoices.index') }}" class="row" id="invoice-filter-form">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="agency_id" class="invoice-filter-label">Agency</label>
                                    <select name="agency_id" id="agency_id" class="form-control invoice-filter-control">
                                        <option value="">All Agencies</option>
                                        @foreach($agencies as $agency)
                                            <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                                                {{ $agency->agency_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status" class="invoice-filter-label">Status</label>
                                    <select name="status" id="status" class="form-control invoice-filter-control">
                                        <option value="">All Status</option>
                                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_from" class="invoice-filter-label">From Date</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control invoice-filter-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_to" class="invoice-filter-label">To Date</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control invoice-filter-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="search" class="invoice-filter-label">Search</label>
                                    <input type="text" name="search" id="search" class="form-control invoice-filter-control" placeholder="Invoice #, title..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="invoice-filter-buttons">
                                        <button type="submit" class="btn btn-primary btn-sm invoice-action-btn">
                                            <i class="mdi mdi-magnify me-1"></i>Search
                                        </button>
                                        @php
                                            $hasFilters = (request('agency_id') && request('agency_id') !== '') ||
                                                         (request('status') && request('status') !== '') ||
                                                         (request('date_from') && request('date_from') !== '') ||
                                                         (request('date_to') && request('date_to') !== '') ||
                                                         (request('search') && request('search') !== '');
                                        @endphp
                                        @if($hasFilters)
                                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary btn-sm invoice-action-btn" title="Clear Filters">
                                                <i class="mdi mdi-close me-1"></i>
                                                <span class="d-none d-lg-inline">Clear</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card invoice-stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title mb-0 font-weight-bold">Invoices ({{ $invoices->total() }})</h6>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm" onclick="bulkAction('send')" id="bulk-send-btn" disabled>
                                    <i class="mdi mdi-send me-1"></i>Send Selected
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="bulkAction('mark_paid')" id="bulk-paid-btn" disabled>
                                    <i class="mdi mdi-check me-1"></i>Mark Paid
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="bulkAction('delete')" id="bulk-delete-btn" disabled>
                                    <i class="mdi mdi-delete me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                        @if($invoices->count() > 0)
                            <div class="invoice-table">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th width="40" class="text-center">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" id="select-all" style="transform: scale(1.2); margin: 0;">
                                                    </div>
                                                </th>
                                                <th>Invoice #</th>
                                                <th>Agency</th>
                                                <th>Date</th>
                                                <th>Due Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th width="150">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoice-table-body">
                                            @include('admin.invoices.partials.table-rows', ['invoices' => $invoices])
                                        </tbody>
                                </table>
                                <!-- Pagination -->
                                <div id="pagination-container" class="mt-2 pull-right pegination-margin">
                                    {{ $invoices->appends(request()->query())->links() }}
                                </div>
                                </div>
                            </div>
                        @else
                            <div class="invoice-empty-state">
                                <div class="empty-icon">
                                    <i class="mdi mdi-file-document"></i>
                                </div>
                                <h5>No invoices found</h5>
                                <p>
                                    @if(request()->hasAny(['agency_id', 'status', 'date_from', 'date_to', 'search']))
                                        No invoices match your search criteria.
                                    @else
                                        Start by creating your first invoice.
                                    @endif
                                </p>
                                @if(request()->hasAny(['agency_id', 'status', 'date_from', 'date_to', 'search']))
                                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary btn-rounded btn-fw btn-sm">
                                        <i class="mdi mdi-close"></i> Clear Filters
                                    </a>
                                @else
                                    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary btn-rounded btn-fw btn-sm">
                                        <i class="mdi mdi-plus"></i> Create First Invoice
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</div>
</div>
<!-- Mark as Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markPaidModalLabel">Mark Invoice as Paid</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="markPaidForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-2"></i>
                        <strong>Invoice:</strong> <span id="invoice-number-display"></span><br>
                        <strong>Total Amount:</strong> $<span id="invoice-amount-display"></span>
                    </div>
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="payment_amount" step="0.01" readonly>
                        </div>
                        <div class="invalid-feedback" id="payment-amount-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID (Optional)</label>
                        <input type="text" class="form-control" id="transaction_id">
                    </div>
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="payment_notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary invoice-action-btn btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success invoice-action-btn btn-sm">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
/* Enhanced checkbox styling for admin invoice listing */
.form-check-input {
    border: 2px solid #36a9f3;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.form-check-input:checked {
    background-color: #36a9f3;
    border-color: #36a9f3;
}

.form-check-input:focus {
    border-color: #36a9f3;
    box-shadow: 0 0 0 0.2rem rgba(0, 187, 224, 0.25);
}

.invoice-checkbox:hover {
    border-color: #57c7d4;
}

/* Table row hover enhancement */
.invoice-table .table tbody tr:hover {
    background-color: rgb(36 122 209 / 5%);
}

/* Bulk action buttons styling */
.btn-group .btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Enhanced action button styling */
.invoice-action-btn.view {
    background-color: #7571f9;
    border-color: #7571f9;
    color: white;
}

.invoice-action-btn.view:hover {
    background-color: #5e57d6;
    border-color: #5e57d6;
    color: white;
}

/* Filter Buttons Container */
.invoice-filter-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}

.invoice-filter-buttons .btn {
    flex-shrink: 0;
    min-width: auto;
}

/* Responsive button sizing */
@media (max-width: 1200px) {
    .invoice-filter-buttons {
        gap: 0.25rem;
    }

    .invoice-filter-buttons .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 768px) {
    .invoice-filter-buttons {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }

    .invoice-filter-buttons .btn {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}

/* Select all checkbox enhancement */
#select-all {
    border: 2px solid #36a9f3;
    cursor: pointer;
}

#select-all:checked {
    background-color: #36a9f3;
    border-color: #36a9f3;
}

.page-header {
    margin-bottom: 2rem;
}

.table th {
    font-weight: 600;
    border-top: none;
}

.badge {
    font-size: 0.8rem;
}

.btn-group .btn {
    border-radius: 6px;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    transition: all 0.3s ease;
}

/* Responsive improvements for mobile */
@media (max-width: 768px) {
    .invoice-table .table th,
    .invoice-table .table td {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }

    .form-check-input {
        transform: scale(1.1);
    }

    .btn-group .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .admin-invoice-stats .card {
        margin-bottom: 1rem;
    }

    .admin-invoice-stats .invoice-stats-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
}
</style>



<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
let currentInvoiceId = null;

// Loading functions
function showLoading(text = 'Processing...') {
    const overlay = document.getElementById('loadingOverlay');
    const loadingText = overlay.querySelector('.loading-text');
    if (loadingText) {
        loadingText.textContent = text;
    }
    overlay.style.display = 'flex';
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.style.display = 'none';
}

function setButtonLoading(button, loading = true) {
    if (loading) {
        button.classList.add('btn-loading');
        button.disabled = true;
        const textElement = button.querySelector('.btn-text') || button;
        if (!button.querySelector('.btn-text')) {
            button.innerHTML = `<span class="btn-text">${button.innerHTML}</span>`;
        }
    } else {
        button.classList.remove('btn-loading');
        button.disabled = false;
        const textElement = button.querySelector('.btn-text');
        if (textElement) {
            button.innerHTML = textElement.innerHTML;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit to ensure all elements are loaded
    setTimeout(function() {
        // Select all functionality
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.invoice-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActionButtons();
            });
        }

        // Individual checkbox change
        document.querySelectorAll('.invoice-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActionButtons);
        });

        // Initial button state update
        updateBulkActionButtons();
    }, 100);

    // Auto-submit form when filters change
    const filterInputs = ['agency_id', 'status', 'date_from', 'date_to'];
    filterInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('change', function() {
                document.getElementById('invoice-filter-form').submit();
            });
        }
    });

    // Mark as paid form
    document.getElementById('markPaidForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitMarkAsPaid();
    });
});


function updateBulkActionButtons() {
    const selectedCheckboxes = document.querySelectorAll('.invoice-checkbox:checked');
    const count = selectedCheckboxes.length;

    // Check how many selected invoices can be deleted (draft status)
    let deletableCount = 0;
    selectedCheckboxes.forEach(checkbox => {
        if (checkbox.dataset.status === 'draft') {
            deletableCount++;
        }
    });

    const sendBtn = document.getElementById('bulk-send-btn');
    const paidBtn = document.getElementById('bulk-paid-btn');
    const deleteBtn = document.getElementById('bulk-delete-btn');

    if (sendBtn) sendBtn.disabled = count === 0;
    if (paidBtn) paidBtn.disabled = count === 0;
    if (deleteBtn) {
        deleteBtn.disabled = deletableCount === 0;
        // Update tooltip or title to explain why it's disabled
        if (deletableCount === 0 && count > 0) {
            deleteBtn.title = 'Only draft invoices can be deleted';
        } else {
            deleteBtn.title = 'Delete selected invoices';
        }
    }
}

function getSelectedInvoiceIds() {
    const selected = [];
    document.querySelectorAll('.invoice-checkbox:checked').forEach(checkbox => {
        selected.push(checkbox.value);
    });
    return selected;
}

async function sendInvoice(invoiceId, buttonElement = null) {
    if (buttonElement) {
        setButtonLoading(buttonElement, true);
    } else {
        showLoading('Sending invoice...');
    }

    try {
        const response = await fetch(`/account/admin/invoices/${invoiceId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (result.success) {
            showConfirmationModal('Success!',result.message,'success',function() {});
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showConfirmationModal('Error!',result.message,'error',function() {});
            if (buttonElement) {
                setButtonLoading(buttonElement, false);
            } else {
                hideLoading();
            }
        }
    } catch (error) {
        showConfirmationModal('Error!',`Failed to send invoice`,'error',function() {});
        if (buttonElement) {
            setButtonLoading(buttonElement, false);
        } else {
            hideLoading();
        }
    }
}

function markAsPaid(invoiceId, invoiceAmount, invoiceNumber) {
    currentInvoiceId = invoiceId;
    const amount = parseFloat(invoiceAmount);

    // Reset form
    document.getElementById('markPaidForm').reset();

    // Populate invoice details
    document.getElementById('invoice-number-display').textContent = invoiceNumber;
    document.getElementById('invoice-amount-display').textContent = amount.toFixed(2);

    // Set payment amount and max limit
    const paymentInput = document.getElementById('payment_amount');
    paymentInput.value = amount.toFixed(2);
    paymentInput.max = amount.toFixed(2);
    paymentInput.dataset.invoiceAmount = amount.toFixed(2);

    // Clear any existing validation errors
    paymentInput.classList.remove('is-invalid');
    document.getElementById('payment-amount-error').textContent = '';

    // Add real-time validation
    setupPaymentAmountValidation(amount);

    // Show modal using Bootstrap 5
    const modal = new bootstrap.Modal(document.getElementById('markPaidModal'));
    modal.show();
}

function setupPaymentAmountValidation(maxAmount) {
    const paymentInput = document.getElementById('payment_amount');
    const errorDiv = document.getElementById('payment-amount-error');

    paymentInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        const max = parseFloat(maxAmount);

        // Clear previous validation states
        this.classList.remove('is-invalid', 'is-valid');
        errorDiv.textContent = '';

        if (this.value === '') {
            return;
        }

        if (isNaN(value) || value <= 0) {
            this.classList.add('is-invalid');
            errorDiv.textContent = 'Please enter a valid payment amount.';
        } else if (value > max) {
            this.classList.add('is-invalid');
            errorDiv.textContent = `Payment amount cannot exceed $${max.toFixed(2)}.`;
        } else {
            this.classList.add('is-valid');
        }
    });
}

async function submitMarkAsPaid() {
    const submitButton = document.querySelector('#markPaidModal button[type="submit"]');
    const paymentInput = document.getElementById('payment_amount');
    const maxAmount = parseFloat(paymentInput.dataset.invoiceAmount);
    const paymentAmount = parseFloat(paymentInput.value);

    // Validate payment amount before submitting
    if (isNaN(paymentAmount) || paymentAmount <= 0) {
        paymentInput.classList.add('is-invalid');
        document.getElementById('payment-amount-error').textContent = 'Please enter a valid payment amount.';
        return;
    }

    if (paymentAmount > maxAmount) {
        paymentInput.classList.add('is-invalid');
        document.getElementById('payment-amount-error').textContent = `Payment amount cannot exceed $${maxAmount.toFixed(2)}.`;
        return;
    }

    if (submitButton) {
        setButtonLoading(submitButton, true);
    }

    const formData = {
        amount: paymentAmount,
        payment_method: 'manual',
        transaction_id: document.getElementById('transaction_id').value,
        notes: document.getElementById('payment_notes').value
    };

    try {
        const response = await fetch(`/account/admin/invoices/${currentInvoiceId}/mark-paid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            $('#markPaidModal').modal('hide');
            showConfirmationModal('Success!',result.message || 'Invoice marked as paid successfully!','success',function() {});
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showConfirmationModal('Error!',result.message || 'Failed to mark invoice as paid!','error',function() {});
            if (submitButton) {
                setButtonLoading(submitButton, false);
            }
        }
    } catch (error) {
        showConfirmationModal('Error!','Failed to mark invoice as paid. Please try again.','error',function() {});
        if (submitButton) {
            setButtonLoading(submitButton, false);
        }
    }
}

async function duplicateInvoice(invoiceId, buttonElement = null) {
    showConfirmationModal(
        'Duplicate Invoice',
        'Are you sure you want to duplicate this invoice?',
        'question',
        async function() {
            if (buttonElement) {
                setButtonLoading(buttonElement, true);
            } else {
                showLoading('Duplicating invoice...');
            }

            try {
                const response = await fetch(`/account/admin/invoices/${invoiceId}/duplicate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showConfirmationModal('Success!','Invoice duplicated successfully.','success',function() {});
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showConfirmationModal('Error!',result.message,'error',function() {});
                    if (buttonElement) {
                        setButtonLoading(buttonElement, false);
                    } else {
                        hideLoading();
                    }
                }
            } catch (error) {
                showConfirmationModal('Error!','Failed to duplicate invoice','error',function() {});
                if (buttonElement) {
                    setButtonLoading(buttonElement, false);
                } else {
                    hideLoading();
                }
            }
        }
    );
}

async function deleteInvoice(invoiceId, buttonElement = null) {
    showConfirmationModal(
        'Delete Invoice',
        'Are you sure you want to delete this invoice? This action cannot be undone.',
        'warning',
        async function() {
            if (buttonElement) {
                setButtonLoading(buttonElement, true);
            } else {
                showLoading('Deleting invoice...');
            }

            try {
                const response = await fetch(`/account/admin/invoices/${invoiceId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect manually
                    window.location.href = data.redirect_url;
                } else {
                    showConfirmationModal('Error!','Failed to delete invoice','error',function() {});
                }
            } catch (error) {
                showConfirmationModal('Error!','Failed to delete invoice','error',function() {});
                if (buttonElement) {
                    setButtonLoading(buttonElement, false);
                } else {
                    hideLoading();
                }
            }
        }
    );
}

async function bulkAction(action) {
    const selectedIds = getSelectedInvoiceIds();

    if (selectedIds.length === 0) {
        showConfirmationModal('Error!','Please select at least one invoice','error',function() {});
        return;
    }

    // For delete action, check if any invoices can be deleted
    if (action === 'delete') {
        const deletableCheckboxes = document.querySelectorAll('.invoice-checkbox:checked[data-status="draft"]');
        if (deletableCheckboxes.length === 0) {
            showConfirmationModal('Error!','Selected invoices cannot be deleted. Only draft invoices can be deleted','error',function() {});
            return;
        }
        if (deletableCheckboxes.length < selectedIds.length) {
            const cannotDelete = selectedIds.length - deletableCheckboxes.length;
            showConfirmationModal(
                'Partial Delete Warning',
                `${cannotDelete} selected invoice(s) cannot be deleted (only draft invoices can be deleted). Continue with deleting ${deletableCheckboxes.length} deletable invoices?`,
                'warning',
                function() {
                    performBulkAction(action, selectedIds);
                }
            );
            return;
        }
    }

    const actionText = action === 'send' ? 'send' : action === 'mark_paid' ? 'mark as paid' : 'delete';
    const actionTitle = action === 'send' ? 'Send Invoices' : action === 'mark_paid' ? 'Mark as Paid' : 'Delete Invoices';

    showConfirmationModal(
        actionTitle,
        `Are you sure you want to ${actionText} ${selectedIds.length} selected invoice(s)?`,
        'question',
        function() {
            performBulkAction(action, selectedIds);
        }
    );
}

async function performBulkAction(action, selectedIds) {
    const actionText = action === 'send' ? 'send' : action === 'mark_paid' ? 'mark as paid' : 'delete';

    // Show loading overlay
    const loadingText = action === 'send' ? 'Sending invoices...' :
                       action === 'mark_paid' ? 'Marking as paid...' : 'Deleting invoices...';
    showLoading(loadingText);

    // Disable all bulk action buttons
    const bulkButtons = ['bulk-send-btn', 'bulk-paid-btn', 'bulk-delete-btn'];
    bulkButtons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) btn.disabled = true;
    });

    try {
        const response = await fetch('{{ route("admin.invoices.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                action: action,
                invoice_ids: selectedIds
            })
        });

        const result = await response.json();

        if (result.success) {
            showConfirmationModal('Success!',result.message,'success',function() {});
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showConfirmationModal('Error!',result.message,'error',function() {});
            hideLoading();
            // Re-enable buttons on error
            updateBulkActionButtons();
        }
    } catch (error) {
        showConfirmationModal('Error!',`Failed to ${actionText} invoices`,'error',function() {});
        hideLoading();
        // Re-enable buttons on error
        updateBulkActionButtons();
    }
}
</script>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <div class="loading-text mt-2">Processing...</div>
    </div>
</div>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.loading-spinner {
    background: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.loading-text {
    color: #6c757d;
    font-size: 14px;
}

.btn-loading {
    position: relative;
    pointer-events: none;
}

.btn-loading .btn-text {
    opacity: 0;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: button-loading-spinner 1s ease infinite;
}

@keyframes button-loading-spinner {
    from {
        transform: rotate(0turn);
    }
    to {
        transform: rotate(1turn);
    }
}

/* Horizontal Admin Stats Cards */
.horizontal-admin-stats-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    min-height: 60px;
}

.horizontal-admin-stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.horizontal-admin-stats-card .card-body {
    padding: 0.75rem;
    min-height: 50px;
    display: flex;
    align-items: center;
}

.horizontal-admin-stats-card h6 {
    font-size: 1.2rem;
    margin: 0;
    line-height: 1.2;
}

.horizontal-admin-stats-card p {
    font-size: 0.75rem;
    margin: 0;
    line-height: 1;
    font-weight: 500;
}

.horizontal-admin-stats-card i {
    flex-shrink: 0;
    width: 2.5rem;
    text-align: center;
}

/* Responsive adjustments for horizontal admin cards */
@media (max-width: 768px) {
    .horizontal-admin-stats-card h6 {
        font-size: 1.1rem;
    }

    .horizontal-admin-stats-card p {
        font-size: 0.7rem;
    }

    .horizontal-admin-stats-card .card-body {
        padding: 0.5rem;
        min-height: 45px;
    }

    .horizontal-admin-stats-card i {
        font-size: 1.6rem !important;
        width: 2rem;
    }
}

@media (max-width: 576px) {
    .horizontal-admin-stats-card h6 {
        font-size: 1rem;
    }

    .horizontal-admin-stats-card p {
        font-size: 0.65rem;
    }

    .horizontal-admin-stats-card .card-body {
        padding: 0.4rem;
        min-height: 40px;
    }

    .horizontal-admin-stats-card i {
        font-size: 1.4rem !important;
        width: 1.8rem;
    }
}
</style>

    </div>
</div>

<script>
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
                            ` : type === 'question' || type === 'warning' ? `
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

    function handleConfirmAction(actionType) {
        // Hide modal
        $('#confirmationModal').modal('hide');

        // Execute callback if exists
        if (window.currentConfirmCallback && typeof window.currentConfirmCallback === 'function') {
            // For 'replace' and 'add' actions, pass the action type
            if (actionType === 'replace' || actionType === 'add') {
                window.currentConfirmCallback(actionType);
            } else {
                // For 'confirm', just execute the callback
                window.currentConfirmCallback();
            }
        }
    }
</script>
@include('include/footer')