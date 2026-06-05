@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper agency-invoice-wrapper">
        <div class="page-title-main">
        </div>
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 font-weight-bold">Invoice {{ $invoice->invoice_number }}</h5>
                    <div class="invoice-status-section">
                        @if($invoice->status === 'paid')
                            <span class="btn btn-success invoice-action-btn btn-sm">Paid</span>
                        @elseif($invoice->status === 'overdue')
                            <span class="btn btn-danger invoice-action-btn btn-sm">Overdue</span>
                        @elseif($invoice->status === 'sent')
                            <span class="btn btn-info invoice-action-btn btn-sm">Sent</span>
                        @else
                            <span class="btn btn-primary invoice-action-btn btn-sm">{{ ucfirst($invoice->status) }}</span>
                        @endif
                        <a href="{{ route('agency.invoices.index') }}" class="btn btn-secondary btn-fw btn-sm invoice-action-btn">Back to Invoices</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="financial-overview-card total-amount">
                    <div class="card-icon">
                        <i class="mdi mdi-receipt"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Invoice Amount</div>
                        <div class="card-value">${{ number_format($invoice->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="financial-overview-card amount-paid">
                    <div class="card-icon">
                        <i class="mdi mdi-check-circle"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Amount Paid</div>
                        <div class="card-value success">${{ number_format($invoice->total_paid, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="financial-overview-card balance-due">
                    <div class="card-icon">
                        <i class="mdi mdi-wallet"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Balance Due</div>
                        <div class="card-value {{ $invoice->balance > 0 ? 'danger' : 'success' }}">
                            ${{ number_format($invoice->balance, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Invoice Details -->
            <div class="col-lg-8">
                <!-- Invoice Information Card -->
                <div class="card invoice-stats-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="card-title mb-0 font-weight-bold">
                            <i class="mdi mdi-information-outline me-2 text-primary"></i>Invoice Details
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
                                        <td class="label">Invoice Date:</td>
                                        <td class="value">{{ $invoice->created_at->format('M d, Y') }}</td>
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
                                    @if($invoice->title)
                                    <tr>
                                        <td class="label">Title:</td>
                                        <td class="value">{{ $invoice->title }}</td>
                                    </tr>
                                    @endif
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
                                        <td class="label">Contact Email:</td>
                                        <td class="value">{{ $invoice->agency->email }}</td>
                                    </tr>
                                    @if($invoice->agency->phone ?? false)
                                        <tr>
                                            <td class="label">Phone:</td>
                                            <td class="value">{{ $invoice->agency->phone }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                    @if($invoice->description)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Description</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $invoice->description }}
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

                @if($invoice->type === 'detailed' && $invoice->items->count() > 0)
                    <!-- Invoice Items Card -->
                    <div class="card invoice-stats-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="card-title mb-0 font-weight-bold">
                                <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i>Invoice Items
                            </h6>
                        </div>
                        <div class="card-body">
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
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card invoice-stats-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="card-title mb-0 font-weight-bold">
                            <i class="mdi mdi-flash me-2 text-primary"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('agency.invoices.download', $invoice) }}"
                               class="btn btn-secondary invoice-action-btn btn-sm">
                                <i class="mdi mdi-download me-2"></i>Download PDF
                            </a>
                            @if($invoice->status !== 'paid' && $invoice->status !== 'draft')
                                <a href="{{ route('agency.invoices.payment', $invoice) }}"
                                   class="btn invoice-btn-primary invoice-action-btn btn-sm">
                                    <i class="mdi mdi-credit-card me-2"></i>Make Payment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Status Alert -->
                @if($invoice->balance > 0)
                    <div class="card invoice-stats-card mb-4">
                        <div class="card-body text-center">
                            @if($invoice->is_overdue)
                                <div class="alert-icon overdue mb-3">
                                    <i class="mdi mdi-alert-circle"></i>
                                </div>
                                <h6 class="text-danger font-weight-bold">Payment Overdue</h6>
                                <p class="text-muted mb-3">This invoice is <strong>{{ abs($invoice->days_until_due) }} days overdue</strong>. Please make payment immediately to avoid additional fees.</p>
                            @elseif($invoice->days_until_due <= 3 && $invoice->days_until_due > 0)
                                <div class="alert-icon warning mb-3">
                                    <i class="mdi mdi-clock-alert"></i>
                                </div>
                                <h6 class="text-warning font-weight-bold">Payment Due Soon</h6>
                                <p class="text-muted mb-3">This invoice is due in <strong>{{ $invoice->days_until_due }} days</strong>.</p>
                            @else
                                <div class="alert-icon info mb-3">
                                    <i class="mdi mdi-information"></i>
                                </div>
                                <h6 class="text-primary font-weight-bold">Payment Required</h6>
                                <p class="text-muted mb-3">Payment is due by {{ $invoice->due_date->format('M d, Y') }}.</p>
                            @endif

                            @if($invoice->status !== 'paid' && $invoice->status !== 'draft')
                                <a href="{{ route('agency.invoices.payment', $invoice) }}" class="btn invoice-btn-primary btn-sm">
                                    <i class="mdi mdi-credit-card me-2"></i>Pay ${{ number_format($invoice->balance, 2) }} Now
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Payment History -->
                @if($invoice->payments->count() > 0)
                    <div class="card invoice-stats-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="card-title mb-0 font-weight-bold">
                                <i class="mdi mdi-credit-card me-2 text-primary"></i>Payment History
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="payment-history-list">
                                @foreach($invoice->payments->sortByDesc('created_at') as $payment)
                                    <div class="payment-history-item">
                                        <div class="payment-item-content">
                                            <div class="payment-header">
                                                <div class="payment-method-icon">
                                                    @if($payment->payment_method === 'stripe')
                                                        <div class="payment-icon stripe">
                                                            <i class="mdi mdi-credit-card"></i>
                                                        </div>
                                                    @elseif($payment->payment_method === 'paypal')
                                                        <div class="payment-icon paypal">
                                                            <i class="mdi mdi-paypal"></i>
                                                        </div>
                                                    @else
                                                        <div class="payment-icon manual">
                                                            <i class="mdi mdi-cash"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="payment-info">
                                                    <div class="payment-amount">${{ number_format($payment->amount, 2) }}</div>
                                                    <div class="payment-method">{{ $payment->payment_method_label }}</div>
                                                    @if($payment->status === 'completed')
                                                        <span class="status-badge success">
                                                            <i class="mdi mdi-check-circle"></i>Completed
                                                        </span>
                                                    @elseif($payment->status === 'pending')
                                                        <span class="status-badge warning">
                                                            <i class="mdi mdi-clock-outline"></i>Pending
                                                        </span>
                                                    @elseif($payment->status === 'failed')
                                                        <span class="status-badge danger">
                                                            <i class="mdi mdi-close-circle"></i>Failed
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="payment-date">
                                                    <div class="date">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}</div>
                                                    <div class="time">{{ $payment->paid_at ? $payment->paid_at->format('h:i A') : $payment->created_at->format('h:i A') }}</div>
                                                </div>
                                            </div>

                                            @if($payment->payment_method === 'stripe' && $payment->formatted_card_number)
                                                <div class="payment-details">
                                                    <div class="detail-item">
                                                        <small class="text-muted">Card:</small>
                                                        <span>{{ $payment->formatted_card_number }}</span>
                                                    </div>
                                                    @if($payment->card_details && $payment->card_details['brand'])
                                                        <div class="detail-item">
                                                            <small class="text-muted">Type:</small>
                                                            <span>{{ ucfirst($payment->card_details['brand']) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($payment->status === 'completed')
                                                <div class="payment-actions">
                                                    <a href="{{ route('agency.invoices.download-receipt', $payment) }}" class="btn-receipt">
                                                        <i class="mdi mdi-download me-1"></i>Receipt
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>


<style>
/* Agency Invoice View Modern Design */

/* Financial Overview Cards */
.financial-overview-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    height: 100%;
    margin-bottom: 1rem;
}

.financial-overview-card:hover {
    border-color: #00BBE0;
    box-shadow: 0 4px 12px #61b6c7ff;
    transform: translateY(-2px);
}

.financial-overview-card .card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.financial-overview-card.total-amount .card-icon {
    background: linear-gradient(135deg, #00BBE0 0%, #61b6c7ff 100%);
    color: white;
}

.financial-overview-card.amount-paid .card-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.financial-overview-card.balance-due .card-icon {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
}

.financial-overview-card .card-icon i {
    font-size: 24px;
}

.financial-overview-card .card-content {
    flex: 1;
}

.financial-overview-card .card-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.financial-overview-card .card-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
}

.financial-overview-card .card-value.success {
    color: #28a745;
}

.financial-overview-card .card-value.danger {
    color: #dc3545;
}

/* Invoice Status Badges */
.invoice-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    font-size: 0.875rem;
    font-weight: 600;
}

.invoice-status-badge.paid {
    background: #d4edda;
    color: #155724;
}

.invoice-status-badge.overdue {
    background: #f8d7da;
    color: #721c24;
}

.invoice-status-badge.sent {
    background: #cce5ff;
    color: #004085;
}

.invoice-status-badge.draft {
    background: #e2e3e5;
    color: #383d41;
}

/* Alert Icons */
.alert-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 24px;
}

.alert-icon.overdue {
    background: #f8d7da;
    color: #721c24;
}

.alert-icon.warning {
    background: #fff3cd;
    color: #856404;
}

.alert-icon.info {
    background: #cce5ff;
    color: #004085;
}

/* Payment History Modern Design */
.payment-history-list {
    border-top: 1px solid #f1f3f4;
}

.payment-history-item {
    border-bottom: 1px solid #f1f3f4;
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
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.payment-method-icon {
    flex-shrink: 0;
}

.payment-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
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

.payment-info {
    flex: 1;
}

.payment-amount {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.payment-method {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.payment-date {
    text-align: right;
    flex-shrink: 0;
}

.payment-date .date {
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
}

.payment-date .time {
    color: #6c757d;
    font-size: 0.75rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
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

.payment-details {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: flex;
    gap: 1rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-item small {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item span {
    font-weight: 500;
    color: #495057;
}

.payment-actions {
    text-align: center;
}

.btn-receipt {
    background: #00BBE0;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-receipt:hover {
    background: #61b6c7ff;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

/* Button Styling */
.invoice-btn-primary {
    background: linear-gradient(135deg, #00BBE0 0%, #61b6c7ff 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.invoice-btn-primary:hover {
    background: linear-gradient(135deg, #00BBE0 0%, #61b6c7ff 100%);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(117, 113, 249, 0.3);
}

.invoice-action-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.invoice-action-btn:hover {
    transform: translateY(-1px);
}

/* Mobile Responsive Design */
@media (max-width: 768px) {
    .financial-overview-card {
        padding: 1rem;
        margin-bottom: 0.75rem;
    }

    .financial-overview-card .card-icon {
        width: 40px;
        height: 40px;
        margin-right: 0.75rem;
    }

    .financial-overview-card .card-icon i {
        font-size: 20px;
    }

    .financial-overview-card .card-value {
        font-size: 1.25rem;
    }

    .payment-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .payment-date {
        text-align: left;
        width: 100%;
    }

    .payment-details {
        flex-direction: column;
        gap: 0.75rem;
    }

    .alert-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
}

@media (max-width: 576px) {
    .financial-overview-card {
        padding: 0.75rem;
    }

    .payment-item-content {
        padding: 1rem;
    }

    .invoice-status-section {
        margin-top: 1rem;
    }
}

/* Legacy styles for compatibility */
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
</style>

@include('include/footer')