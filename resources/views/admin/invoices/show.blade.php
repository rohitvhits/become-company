@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper invoice-module-wrapper">
        <div class="page-title-main">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0 font-weight-bold"># {{ $invoice->invoice_number }}</h5>
                    @if($invoice->title)
                        <small class="text-muted">{{ $invoice->title }}</small>
                    @endif
                </div>
            </div>
            <div class="page-rightbtns">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    @if($invoice->canBeDeleted())
                        <button type="button" class="btn btn-danger invoice-action-btn btn-fw btn-sm mr-1" onclick="deleteInvoice()">
                            <i class="mdi mdi-send d-md-none"></i>
                            <span class="d-none d-md-inline"><i class="mdi mdi-delete"></i> Delete</span>
                            <span class="d-md-none">Delete</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Invoice Details -->
            <div class="col-lg-8">
                <!-- Status and Overview Card -->
                <div class="card invoice-stats-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 font-weight-bold">
                            <i class="mdi mdi-file-document me-2 text-primary"></i>Invoice Overview
                        </h6>
                        <div>
                            @if($invoice->status === 'paid')
                                <span class="invoice-status-badge paid invoice-action-btn">
                                    <i class="mdi mdi-check me-1"></i>Paid
                                </span>
                            @elseif($invoice->status === 'overdue')
                                <span class="invoice-status-badge overdue invoice-action-btn">
                                    <i class="mdi mdi-alert-triangle me-1"></i>Overdue
                                </span>
                            @elseif($invoice->status === 'sent')
                                <span class="invoice-status-badge sent invoice-action-btn">
                                    <i class="mdi mdi-send me-1"></i>Sent
                                </span>
                            @else
                                <span class="invoice-status-badge draft invoice-action-btn">
                                    <i class="mdi mdi-pencil me-1"></i>{{ ucfirst($invoice->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Financial Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="financial-metric-card">
                                    <div class="metric-icon total">
                                        <i class="mdi mdi-receipt"></i>
                                    </div>
                                    <div class="metric-content">
                                        <div class="metric-label">Total Amount</div>
                                        <div class="metric-value">${{ number_format($invoice->total_amount, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="financial-metric-card">
                                    <div class="metric-icon paid">
                                        <i class="mdi mdi-check-circle"></i>
                                    </div>
                                    <div class="metric-content">
                                        <div class="metric-label">Amount Paid</div>
                                        <div class="metric-value success">${{ number_format($invoice->total_paid, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="financial-metric-card">
                                    <div class="metric-icon balance">
                                        <i class="mdi mdi-wallet"></i>
                                    </div>
                                    <div class="metric-content">
                                        <div class="metric-label">Balance Due</div>
                                        <div class="metric-value {{ $invoice->balance > 0 ? 'danger' : 'success' }}">
                                            ${{ number_format($invoice->balance, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details Card -->
                <div class="card invoice-stats-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="card-title mb-0 font-weight-bold">
                            <i class="mdi mdi-information-outline me-2 text-primary mr-2"></i>Invoice Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3 font-weight-semibold">Invoice Information</h6>
                                <table class="invoice-details-table table table-borderless">
                                    <tr>
                                        <td class="label">Invoice Number:</td>
                                        <td class="value">{{ $invoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Type:</td>
                                        <td class="value">
                                            @if($invoice->type === 'uploaded_pdf')
                                                <i class="mdi mdi-file-pdf-box text-danger me-1"></i>Uploaded PDF
                                            @elseif($invoice->type === 'quick')
                                                <i class="mdi mdi-lightning-bolt text-success me-1"></i>Quick Invoice
                                            @else
                                                <i class="mdi mdi-format-list-bulleted text-info me-1"></i>Detailed Invoice
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label">Created Date:</td>
                                        <td class="value">{{ $invoice->created_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Due Date:</td>
                                        <td class="value {{ $invoice->is_overdue ? 'text-danger fw-bold' : '' }}">
                                            {{ $invoice->due_date->format('M d, Y') }}
                                            @if($invoice->is_overdue)
                                                <br><small>({{ abs($invoice->days_until_due) }} days overdue)</small>
                                            @elseif($invoice->days_until_due <= 3 && $invoice->days_until_due > 0)
                                                <br><small class="text-warning">(Due in {{ $invoice->days_until_due }} days)</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label">Created By:</td>
                                        <td class="value">{{ $invoice->creator->first_name }} {{$invoice->creator->last_name}}</td>
                                    </tr>
                                    @if($invoice->sent_at)
                                        <tr>
                                            <td class="label">Sent At:</td>
                                            <td class="value">{{ $invoice->sent_at->format('M d, Y H:i A') }}</td>
                                        </tr>
                                    @endif
                                    @if($invoice->paid_at)
                                        <tr>
                                            <td class="label">Paid At:</td>
                                            <td class="value">{{ $invoice->paid_at->format('M d, Y H:i A') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3 font-weight-semibold">Agency Information</h6>
                                <table class="invoice-details-table table table-borderless">
                                    <tr>
                                        <td class="label">Agency Name:</td>
                                        <td class="value">{{ $invoice->agency->agency_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Email:</td>
                                        <td class="value">{{ $invoice->agency->email }}</td>
                                    </tr>
                                    @if($invoice->agency->phone)
                                        <tr>
                                            <td class="label">Phone:</td>
                                            <td class="value">{{ $invoice->agency->phone }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                    @if($invoice->title)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Title</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $invoice->title }}
                            </div>
                        </div>
                    @endif

                    @if($invoice->description)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Description</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $invoice->description }}
                            </div>
                        </div>
                    @endif

                    @if($invoice->type === 'detailed' && $invoice->items->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-muted mb-3 font-weight-semibold">Invoice Items</h6>
                            <div class="invoice-table">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Unit Price</th>
                                                @if($invoice->items->where('tax_percentage', '>', 0)->count() > 0)
                                                    <th class="text-end">Tax</th>
                                                @endif
                                                @if($invoice->items->where('discount_percentage', '>', 0)->count() > 0)
                                                    <th class="text-end">Discount</th>
                                                @endif
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->items as $item)
                                                <tr>
                                                    <td>{{ $item->description }}</td>
                                                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                                    @if($invoice->items->where('tax_percentage', '>', 0)->count() > 0)
                                                        <td class="text-end">
                                                            @if($item->tax_percentage > 0)
                                                                {{ number_format($item->tax_percentage, 1) }}%
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    @endif
                                                    @if($invoice->items->where('discount_percentage', '>', 0)->count() > 0)
                                                        <td class="text-end">
                                                            @if($item->discount_percentage > 0)
                                                                {{ number_format($item->discount_percentage, 1) }}%
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td class="text-end font-weight-semibold">${{ number_format($item->line_total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Invoice Summary -->
                            <div class="row justify-content-end">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-end fw-semibold">Subtotal:</td>
                                            <td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td>
                                        </tr>
                                        @if($invoice->tax_percentage > 0)
                                            <tr>
                                                <td class="text-end fw-semibold">Tax ({{ number_format($invoice->tax_percentage, 2) }}%):</td>
                                                <td class="text-end">${{ number_format($invoice->tax_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        @if($invoice->discount_percentage > 0)
                                            <tr>
                                                <td class="text-end fw-semibold">Discount ({{ number_format($invoice->discount_percentage, 2) }}%):</td>
                                                <td class="text-end text-success">-${{ number_format($invoice->discount_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="border-top">
                                            <td class="text-end fw-bold fs-5">Total:</td>
                                            <td class="text-end fw-bold fs-5">${{ number_format($invoice->total_amount, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($invoice->terms_conditions)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Terms & Conditions</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $invoice->terms_conditions }}
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card invoice-stats-card mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 font-weight-bold"><i class="mdi mdi-flash me-2 text-primary"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($invoice->canBeEdited())
                            <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-info invoice-action-btn mb-2 btn-sm">
                                <i class="mdi mdi-pencil me-2"></i>Edit Invoice
                            </a>
                        @endif
                        <a href="{{ route('admin.invoices.download', $invoice) }}" class="btn btn-secondary invoice-action-btn mb-2 btn-sm">
                            <i class="mdi mdi-download me-2"></i>Download PDF
                        </a>
                        @if($invoice->status === 'draft')
                            <button type="button" class="btn invoice-btn-primary mb-2 btn-sm text-white invoice-action-btn btn-sm" onclick="sendInvoice()">
                                <i class="mdi mdi-send me-2"></i>Send to Agency
                            </button>
                        @elseif($invoice->status === 'sent')
                            <button type="button" class="btn invoice-btn-outline invoice-action-btn mb-2 btn-sm" onclick="sendInvoice()">
                                <i class="mdi mdi-send me-2"></i>Resend Email
                            </button>
                        @endif
                        @if($invoice->status !== 'paid')
                            <button type="button" class="btn btn-success invoice-action-btn mb-2 btn-sm" onclick="markAsPaid()">
                                <i class="mdi mdi-check me-2"></i>Mark as Paid
                            </button>
                        @endif
                        {{-- <button type="button" class="btn btn-secondary invoice-action-btn mb-2 btn-sm" onclick="duplicateInvoice()">
                            <i class="mdi mdi-content-copy me-2"></i>Duplicate
                        </button> --}}
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($invoice->payments->count() > 0)
                <div class="card invoice-stats-card mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 font-weight-bold">
                            <i class="mdi mdi-credit-card me-2 text-primary"></i>Payment History
                        </h6>
                        <small class="text-muted">{{ $invoice->payments->count() }} payment{{ $invoice->payments->count() > 1 ? 's' : '' }}</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="payment-history-timeline">
                            @foreach($invoice->payments->sortByDesc('created_at') as $payment)
                                <div class="payment-history-item">
                                    <div class="payment-item-content">
                                        <!-- Payment Header -->
                                        <div class="payment-header">
                                            <div class="payment-method-section">
                                                <div class="payment-method-icon">
                                                    @if($payment->payment_method === 'stripe')
                                                        <div class="payment-icon stripe">
                                                            <i class="mdi mdi-credit-card"></i>
                                                        </div>
                                                    @else
                                                        <div class="payment-icon manual">
                                                            <i class="mdi mdi-cash"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="payment-basic-info">
                                                    <div class="payment-amount">${{ number_format($payment->amount, 2) }}</div>
                                                    <div class="payment-method">{{ $payment->payment_method_label }}</div>
                                                </div>
                                            </div>
                                            <div class="payment-status-section">
                                                <div class="payment-status">
                                                    @if($payment->status === 'completed')
                                                        <span class="status-badge success">
                                                            <i class="mdi mdi-check-circle"></i>
                                                            Completed
                                                        </span>
                                                    @elseif($payment->status === 'pending')
                                                        <span class="status-badge warning">
                                                            <i class="mdi mdi-clock-outline"></i>
                                                            Pending
                                                        </span>
                                                    @elseif($payment->status === 'failed')
                                                        <span class="status-badge danger">
                                                            <i class="mdi mdi-close-circle"></i>
                                                            Failed
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="payment-date">
                                                    {{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}
                                                </div>
                                                <div class="payment-time">
                                                    {{ $payment->paid_at ? $payment->paid_at->format('h:i A') : $payment->created_at->format('h:i A') }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Details (Collapsible) -->
                                        <div class="payment-details-toggle">
                                            <button class="btn btn-info invoice-action-btn btn-sm btn-expand" onclick="togglePaymentDetails({{ $payment->id }})">
                                                <span class="expand-text">View Details</span>
                                                <i class="mdi mdi-chevron-down expand-icon"></i>
                                            </button>
                                        </div>

                                        <div id="payment-details-{{ $payment->id }}" class="payment-details-content collapse">
                                            <div class="payment-details-grid">
                                                <!-- Payment Method Details -->
                                                @if($payment->payment_method === 'stripe' && $payment->formatted_card_number)
                                                    <div class="detail-group">
                                                        <div class="detail-label">Card Information</div>
                                                        <div class="detail-value">
                                                            <div class="card-info">
                                                                <div class="card-number">{{ $payment->formatted_card_number }}</div>
                                                                @if($payment->card_details && $payment->card_details['brand'])
                                                                    <div class="card-brand">
                                                                        {{ ucfirst($payment->card_details['brand']) }}
                                                                        @if($payment->card_details['funding'])
                                                                            {{ ucfirst($payment->card_details['funding']) }}
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                @if($payment->card_details && $payment->card_details['exp_month'] && $payment->card_details['exp_year'])
                                                                    <div class="card-expiry">
                                                                        Expires {{ sprintf('%02d', $payment->card_details['exp_month']) }}/{{ $payment->card_details['exp_year'] }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Transaction ID -->
                                                @if($payment->transaction_id)
                                                    <div class="detail-group">
                                                        <div class="detail-label">Transaction ID</div>
                                                        <div class="detail-value">
                                                            <div class="transaction-id">
                                                                <code>{{ $payment->transaction_id }}</code>
                                                                <button class="copy-btn" onclick="copyToClipboard('{{ $payment->transaction_id }}')" title="Copy Transaction ID">
                                                                    <i class="mdi mdi-content-copy"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Receipt -->
                                                @if($payment->payment_gateway_response && isset($payment->payment_gateway_response['receipt_url']))
                                                    <div class="detail-group">
                                                        <div class="detail-label">Receipt</div>
                                                        <div class="detail-value">
                                                            <a href="{{ $payment->payment_gateway_response['receipt_url'] }}" target="_blank" class="receipt-link">
                                                                <i class="mdi mdi-receipt me-2"></i>View Stripe Receipt
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Gateway Response (for developers) -->
                                                @if($payment->payment_gateway_response)
                                                    <div class="detail-group full-width">
                                                        <div class="detail-label">
                                                            Gateway Response
                                                            <small class="text-muted">(Technical Details)</small>
                                                        </div>
                                                        <div class="detail-value">
                                                            <div class="gateway-response">
                                                                <button class="btn-toggle-raw" onclick="toggleRawData({{ $payment->id }})">
                                                                    <i class="mdi mdi-code-json"></i>
                                                                    View Raw Data
                                                                </button>
                                                                <div id="raw-data-{{ $payment->id }}" class="raw-data-content" style="display: none;">
                                                                    <pre><code>{{ json_encode($payment->payment_gateway_response, JSON_PRETTY_PRINT) }}</code></pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Payment Timeline -->
                                            <div class="payment-timeline">
                                                <div class="timeline-item">
                                                    <div class="timeline-marker"></div>
                                                    <div class="timeline-content">
                                                        <div class="timeline-title">Payment Created</div>
                                                        <div class="timeline-time">{{ $payment->created_at->format('M d, Y H:i A') }}</div>
                                                    </div>
                                                </div>
                                                @if($payment->paid_at && $payment->paid_at != $payment->created_at)
                                                    <div class="timeline-item">
                                                        <div class="timeline-marker success"></div>
                                                        <div class="timeline-content">
                                                            <div class="timeline-title">Payment {{ ucfirst($payment->status) }}</div>
                                                            <div class="timeline-time">{{ $payment->paid_at->format('M d, Y H:i A') }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Notifications History -->
            @if($invoice->notifications->count() > 0)
                <div class="card invoice-stats-card">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="card-title mb-0 font-weight-bold"><i class="mdi mdi-bell-outline me-2 text-primary"></i>Notification History</h6>
                    </div>
                    <div class="card-body">
                        @foreach($invoice->notifications->sortByDesc('sent_at')->take(10) as $notification)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <div class="small font-weight-semibold">{{ $notification->type_label }}</div>
                                    <small class="text-muted">{{ $notification->sent_to }}</small>
                                </div>
                                <small class="text-muted">{{ $notification->sent_at->format('M d, H:i') }}</small>
                            </div>
                        @endforeach
                        @if($invoice->notifications->count() > 10)
                            <small class="text-muted">and {{ $invoice->notifications->count() - 10 }} more...</small>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Invoice as Paid</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="markPaidForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-2"></i>
                        <strong>Invoice:</strong> {{ $invoice->invoice_number }}<br>
                        <strong>Total Amount:</strong> ${{ number_format($invoice->total_amount, 2) }}<br>
                        <strong>Balance Due:</strong> ${{ number_format($invoice->balance, 2) }}
                    </div>
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="payment_amount"
                                   value="{{ number_format($invoice->balance, 2, '.', '') }}"
                                   step="0.01" readonly>
                        </div>
                        <div class="form-text">Payment amount cannot exceed the outstanding balance of ${{ number_format($invoice->balance, 2) }}.</div>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
.page-header {
    margin-bottom: 2rem;
}

.table th {
    font-weight: 600;
}

.badge.fs-6 {
    font-size: 0.9rem !important;
}

.card {
    transition: all 0.3s ease;
}

.border-bottom:last-child {
    border-bottom: none !important;
}

.font-monospace {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.8rem;
}
</style>



<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const invoiceId = {{ $invoice->id }};

document.addEventListener('DOMContentLoaded', function() {
    // Mark as paid form
    document.getElementById('markPaidForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitMarkAsPaid();
    });
});

function sendInvoice() {
    showConfirmationModal(
        'Send Invoice',
        'Are you sure you want to send this invoice to the agency?',
        'question',
        async function() {
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
                    showConfirmationModal('Success!', result.message, 'success', function() {});
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showConfirmationModal('Error!', result.message, 'error', function() {});
                }
            } catch (error) {
                showConfirmationModal('Error!', 'Failed to send invoice', 'error', function() {});
            }
        }
    );
}

function markAsPaid() {
    const balance = {{ $invoice->balance }};

    // Reset form
    document.getElementById('markPaidForm').reset();

    // Set payment amount and validation
    const paymentInput = document.getElementById('payment_amount');
    paymentInput.value = balance.toFixed(2);
    paymentInput.max = balance.toFixed(2);
    paymentInput.dataset.maxAmount = balance.toFixed(2);

    // Clear any existing validation errors
    paymentInput.classList.remove('is-invalid');
    document.getElementById('payment-amount-error').textContent = '';

    // Add real-time validation
    setupPaymentAmountValidation(balance);

    // Show modal
    $('#markPaidModal').modal('show');
}

function setupPaymentAmountValidation(maxAmount) {
    const paymentInput = document.getElementById('payment_amount');
    const errorDiv = document.getElementById('payment-amount-error');

    // Remove existing listeners to avoid duplicates
    const newInput = paymentInput.cloneNode(true);
    paymentInput.parentNode.replaceChild(newInput, paymentInput);

    newInput.addEventListener('input', function() {
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
    const paymentInput = document.getElementById('payment_amount');
    const maxAmount = parseFloat(paymentInput.dataset.maxAmount);
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

    const formData = {
        amount: paymentAmount,
        payment_method: 'manual',
        transaction_id: document.getElementById('transaction_id').value,
        notes: document.getElementById('payment_notes').value
    };

    try {
        const response = await fetch(`/account/admin/invoices/${invoiceId}/mark-paid`, {
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
            showConfirmationModal('Success!', result.message, 'success', function() {});
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showConfirmationModal('Error!', result.message, 'error', function() {});
        }
    } catch (error) {
        showConfirmationModal('Error!', 'Failed to mark invoice as paid', 'error', function() {});
    }
}

function duplicateInvoice() {
    showConfirmationModal(
        'Duplicate Invoice',
        'Are you sure you want to duplicate this invoice?',
        'question',
        async function() {
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
                    showConfirmationModal('Success!', 'Invoice duplicated successfully', 'success', function() {});
                    setTimeout(() => window.location.href = '/account/admin/invoices/' + result.invoice_id + '/edit', 1000);
                } else {
                    showConfirmationModal('Error!', result.message, 'error', function() {});
                }
            } catch (error) {
                showConfirmationModal('Error!', 'Failed to duplicate invoice', 'error', function() {});
            }
        }
    );
}

async function deleteInvoice() {
    showConfirmationModal(
        'Delete Invoice',
        'Are you sure you want to delete this invoice? This action cannot be undone.',
        'warning',
        async function() {
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
                showConfirmationModal('Error!', 'Failed to delete invoice', 'error', function() {});
            }
        }
    );
}

function showAlert(type, message) {
    // Create custom styled alert/confirm box
    const alertModal = document.createElement('div');
    alertModal.className = 'custom-alert-overlay';

    const alertBox = document.createElement('div');
    alertBox.className = `custom-alert-box alert-${type}`;

    // Icon based on type
    let icon = '';
    let bgColor = '';
    switch(type) {
        case 'success':
            icon = '<i class="mdi mdi-check-circle"></i>';
            bgColor = '#28a745';
            break;
        case 'error':
            icon = '<i class="mdi mdi-alert-circle"></i>';
            bgColor = '#dc3545';
            break;
        case 'warning':
            icon = '<i class="mdi mdi-alert-triangle"></i>';
            bgColor = '#ffc107';
            break;
        default:
            icon = '<i class="mdi mdi-information"></i>';
            bgColor = '#17a2b8';
    }

    alertBox.innerHTML = `
        <div class="alert-header" style="background: ${bgColor}">
            ${icon}
            <span class="alert-title">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
        </div>
        <div class="alert-body">
            <p class="alert-message">${message}</p>
        </div>
        <div class="alert-footer">
            <button class="btn btn-primary alert-ok-btn">OK</button>
        </div>
    `;

    alertModal.appendChild(alertBox);
    document.body.appendChild(alertModal);

    // Add click handler to close
    alertBox.querySelector('.alert-ok-btn').addEventListener('click', function() {
        document.body.removeChild(alertModal);
    });

    // Close on overlay click
    alertModal.addEventListener('click', function(e) {
        if (e.target === alertModal) {
            document.body.removeChild(alertModal);
        }
    });
}

function showConfirm(message, onConfirm, onCancel = null) {
    const confirmModal = document.createElement('div');
    confirmModal.className = 'custom-alert-overlay';

    const confirmBox = document.createElement('div');
    confirmBox.className = 'custom-alert-box alert-confirm';

    confirmBox.innerHTML = `
        <div class="alert-header" style="background: #00BBE0">
            <i class="mdi mdi-help-circle"></i>
            <span class="alert-title">Confirm Action</span>
        </div>
        <div class="alert-body">
            <p class="alert-message">${message}</p>
        </div>
        <div class="alert-footer">
            <button class="btn btn-secondary alert-cancel-btn">Cancel</button>
            <button class="btn btn-primary alert-confirm-btn">Confirm</button>
        </div>
    `;

    confirmModal.appendChild(confirmBox);
    document.body.appendChild(confirmModal);

    // Add click handlers
    confirmBox.querySelector('.alert-confirm-btn').addEventListener('click', function() {
        document.body.removeChild(confirmModal);
        if (onConfirm) onConfirm();
    });

    confirmBox.querySelector('.alert-cancel-btn').addEventListener('click', function() {
        document.body.removeChild(confirmModal);
        if (onCancel) onCancel();
    });

    // Close on overlay click (acts as cancel)
    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) {
            document.body.removeChild(confirmModal);
            if (onCancel) onCancel();
        }
    });
}

function togglePaymentDetails(paymentId) {
    const element = document.getElementById('payment-details-' + paymentId);
    const button = document.querySelector(`[onclick="togglePaymentDetails(${paymentId})"]`);

    if (element) {
        if (element.classList.contains('show')) {
            element.classList.remove('show');
            if (button) {
                button.classList.remove('expanded');
                button.querySelector('.expand-text').textContent = 'View Details';
            }
        } else {
            element.classList.add('show');
            if (button) {
                button.classList.add('expanded');
                button.querySelector('.expand-text').textContent = 'Hide Details';
            }
        }
    }
}

function toggleRawData(paymentId) {
    const element = document.getElementById('raw-data-' + paymentId);
    if (element) {
        if (element.style.display === 'none') {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showConfirmationModal('Success!', 'Transaction ID copied to clipboard', 'success', function() {});
    }).catch(function() {
        showConfirmationModal('Error!', 'Failed to copy to clipboard', 'error', function() {});
    });
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

<style>
/* Additional YoraUI theme integration styles */
.invoice-module-wrapper .card-title {
    color: #495057;
    font-size: 1rem;
}

.invoice-module-wrapper .text-muted {
    color: #6c757d !important;
}

.invoice-module-wrapper .card-header {
    padding: 1rem 0.5rem;
}

.invoice-module-wrapper .fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Financial Metric Cards */
.financial-metric-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    height: 100%;
}

.financial-metric-card:hover {
    border-color: #00BBE0;
    box-shadow: 0 4px 12px rgba(0, 187, 224, 0.15);
    transform: translateY(-2px);
}

.metric-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.metric-icon.total {
    background: linear-gradient(135deg, #00BBE0 0%, #57c7d4 100%);
    color: white;
}

.metric-icon.paid {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.metric-icon.balance {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
}

.metric-icon i {
    font-size: 24px;
}

.metric-content {
    flex: 1;
}

.metric-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
}

.metric-value.success {
    color: #28a745;
}

.metric-value.danger {
    color: #dc3545;
}

/* Modern Payment History Styles */
.payment-history-timeline {
    position: relative;
}

.payment-history-item {
    border-bottom: 1px solid #f1f3f4;
    padding: 0;
}

.payment-history-item:last-child {
    border-bottom: none;
}

.payment-item-content {
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.payment-item-content:hover {
    background: #f8f9fa;
}

.payment-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.payment-method-section {
    display: flex;
    align-items: center;
    flex: 1;
}

.payment-method-icon {
    margin-right: 1rem;
}

.payment-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.payment-icon.stripe {
    background: linear-gradient(135deg, #635bff 0%, #5469d4 100%);
}

.payment-icon.paypal {
    background: linear-gradient(135deg, #0070ba 0%, #003087 100%);
}

.payment-icon.manual {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.payment-basic-info .payment-amount {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.payment-basic-info .payment-method {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

.payment-status-section {
    text-align: right;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.status-badge.success {
    background: #d4edda;
    color: #155724;
}

.status-badge.warning {
    background: #fff3cd;
    color: #856404;
}

.status-badge.danger {
    background: #f8d7da;
    color: #721c24;
}

.payment-date {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.payment-time {
    color: #6c757d;
    font-size: 0.8rem;
}

.payment-details-toggle {
    margin-top: 1rem;
}



.expand-icon {
    transition: transform 0.3s ease;
}

.payment-details-content {
    padding-top: 1.5rem;
    border-top: 1px solid #f1f3f4;
    margin-top: 1rem;
}

.payment-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.detail-group {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid #00BBE0;
}

.detail-group.full-width {
    grid-column: 1 / -1;
}

.detail-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.detail-value {
    color: #2c3e50;
    font-weight: 500;
}

.card-info .card-number {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.card-info .card-brand, .card-info .card-expiry {
    font-size: 0.875rem;
    color: #6c757d;
}

.transaction-id {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.transaction-id code {
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

.copy-btn {
    background: none;
    border: none;
    color: #00BBE0;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.copy-btn:hover {
    background: #f8f7ff;
    color: #5a54d6;
}

.receipt-link {
    color: #00BBE0;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border: 1px solid #00BBE0;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.receipt-link:hover {
    background: #00BBE0;
    color: white;
    text-decoration: none;
}

.btn-toggle-raw {
    background: #343a40;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-toggle-raw:hover {
    background: #495057;
}

.raw-data-content {
    margin-top: 1rem;
    background: #2d3748;
    border-radius: 8px;
    overflow: hidden;
}

.raw-data-content pre {
    background: #2d3748;
    color: #e2e8f0;
    padding: 1rem;
    margin: 0;
    border-radius: 8px;
    font-size: 0.8rem;
    overflow-x: auto;
}

.payment-timeline {
    border-top: 1px solid #e9ecef;
    padding-top: 1.5rem;
    position: relative;
}

.payment-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 2rem;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #6c757d;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
    flex-shrink: 0;
    margin-right: 1rem;
    position: relative;
    z-index: 1;
}

.timeline-marker.success {
    background: #28a745;
    box-shadow: 0 0 0 2px #d4edda;
}

.timeline-content {
    flex: 1;
    padding-top: -3px;
}

.timeline-title {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.timeline-time {
    color: #6c757d;
    font-size: 0.8rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .financial-metric-card {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .metric-icon {
        width: 40px;
        height: 40px;
        margin-right: 0.75rem;
    }

    .metric-icon i {
        font-size: 20px;
    }

    .metric-value {
        font-size: 1.25rem;
    }

    .payment-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .payment-status-section {
        text-align: left;
        width: 100%;
    }

    .payment-details-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .payment-timeline::before {
        left: 10px;
    }

    .timeline-marker {
        width: 10px;
        height: 10px;
        margin-right: 0.75rem;
    }
}

/* Custom Alert and Confirm Box Styles */
.custom-alert-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: fadeInOverlay 0.3s ease;
}

@keyframes fadeInOverlay {
    from { opacity: 0; }
    to { opacity: 1; }
}

.custom-alert-box {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    max-width: 450px;
    width: 90%;
    overflow: hidden;
    animation: slideInAlert 0.3s ease;
}

@keyframes slideInAlert {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.alert-header {
    color: white;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-header i {
    font-size: 1.5rem;
}

.alert-title {
    font-size: 1.1rem;
    font-weight: 600;
}

.alert-body {
    padding: 1.5rem;
}

.alert-message {
    margin: 0;
    color: #495057;
    font-size: 1rem;
    line-height: 1.5;
}

.alert-footer {
    padding: 1rem 1.5rem 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.alert-footer .btn {
    padding: 0.5rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.alert-ok-btn, .alert-confirm-btn {
    background: #00BBE0;
    color: white;
}

.alert-ok-btn:hover, .alert-confirm-btn:hover {
    background: #5a54d6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(117, 113, 249, 0.3);
}

.alert-cancel-btn {
    background: #6c757d;
    color: white;
}

.alert-cancel-btn:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

/* Alert type specific colors */
.alert-success .alert-header {
    background: #28a745;
}

.alert-error .alert-header {
    background: #dc3545;
}

.alert-warning .alert-header {
    background: #ffc107;
    color: #212529;
}

.alert-confirm .alert-header {
    background: #00BBE0;
}

/* Mobile responsiveness */
@media (max-width: 480px) {
    .custom-alert-box {
        width: 95%;
        margin: 1rem;
    }

    .alert-header {
        padding: 1rem;
    }

    .alert-body {
        padding: 1rem;
    }

    .alert-footer {
        padding: 0.75rem 1rem 1rem;
        flex-direction: column;
    }

    .alert-footer .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .alert-footer .btn:last-child {
        margin-bottom: 0;
    }
}
</style>

    </div>
</div>

@include('include/footer')