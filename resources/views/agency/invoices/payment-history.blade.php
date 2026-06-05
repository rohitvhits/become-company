@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper agency-invoice-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Payment History</h5>
            <div class="page-rightbtns">
                <a href="{{ route('agency.invoices.index') }}" class="btn btn-outline-primary btn-rounded btn-fw btn-sm">
                    <i class="mdi mdi-file-document me-2"></i>View Invoices
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-success">{{ $stats['total_payments'] }}</h4>
                        <p class="mb-0 text-muted">Total Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-primary">${{ number_format($stats['total_amount_paid'], 2) }}</h4>
                        <p class="mb-0 text-muted">Total Amount Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-info">{{ $stats['payments_this_month'] }}</h4>
                        <p class="mb-0 text-muted">Payments This Month</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-warning">${{ number_format($stats['amount_this_month'], 2) }}</h4>
                        <p class="mb-0 text-muted">Amount This Month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                    <i class="mdi mdi-filter me-2"></i>Filter Payments
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('agency.invoices.payment-history') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Payment Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="">All Methods</option>
                            <option value="stripe" {{ request('payment_method') === 'stripe' ? 'selected' : '' }}>Credit/Debit Card</option>
                            <option value="paypal" {{ request('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="manual" {{ request('payment_method') === 'manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-rounded btn-fw">
                                <i class="mdi mdi-magnify me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                    @if(request()->hasAny(['status', 'payment_method', 'date_from', 'date_to']))
                        <div class="col-12">
                            <a href="{{ route('agency.invoices.payment-history') }}" class="btn btn-outline-secondary btn-rounded btn-fw">
                                <i class="mdi mdi-close me-2"></i>Clear Filters
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-credit-card me-2"></i>Payment Transactions ({{ $payments->total() }})
                    </h5>
                    @if($payments->count() > 0)
                        <button type="button" class="btn btn-outline-success btn-rounded btn-fw btn-sm" onclick="exportPayments()">
                            <i class="mdi mdi-download me-2"></i>Export CSV
                        </button>
                    @endif
                </div>
            </div>
        <div class="card-body p-0">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Payment Date</th>
                                <th>Invoice #</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <div>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i A') : $payment->created_at->format('M d, Y H:i A') }}</div>
                                        <small class="text-muted">{{ $payment->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('agency.invoices.show', $payment->invoice) }}"
                                           class="text-decoration-none fw-bold">
                                            {{ $payment->invoice->invoice_number }}
                                        </a>
                                        @if($payment->invoice->title)
                                            <br><small class="text-muted">{{ Str::limit($payment->invoice->title, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ $payment->amount < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $payment->amount < 0 ? '-' : '' }}${{ number_format(abs($payment->amount), 2) }}
                                        </span>
                                        @if($payment->amount < 0)
                                            <br><small class="text-muted">Refund</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($payment->payment_method === 'stripe')
                                                <i class="mdi mdi-credit-card text-primary me-2"></i>
                                            @elseif($payment->payment_method === 'paypal')
                                                <i class="mdi mdi-paypal text-primary me-2"></i>
                                            @else
                                                <i class="mdi mdi-cash text-success me-2"></i>
                                            @endif
                                            <span>{{ $payment->payment_method_label }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($payment->transaction_id)
                                            <code class="small">{{ Str::limit($payment->transaction_id, 20) }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status === 'completed')
                                            <span class="badge badge-success">
                                                <i class="mdi mdi-check me-1"></i>Completed
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge badge-warning">
                                                <i class="mdi mdi-clock me-1"></i>Pending
                                            </span>
                                        @elseif($payment->status === 'failed')
                                            <span class="badge badge-danger">
                                                <i class="mdi mdi-close me-1"></i>Failed
                                            </span>
                                        @elseif($payment->status === 'refunded')
                                            <span class="badge badge-info">
                                                <i class="mdi mdi-undo me-1"></i>Refunded
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" style="vertical-align: middle;">
                                            <a href="{{ route('agency.invoices.show', $payment->invoice) }}"
                                               class="btn btn-primary btn-sm"
                                               title="View Invoice"
                                               style="margin-right: 5px;">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            @if($payment->status === 'completed')
                                                <a href="{{ route('agency.invoices.download-receipt', $payment) }}"
                                                   class="btn btn-success btn-sm"
                                                   title="Download Receipt">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="mdi mdi-credit-card display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No payment transactions found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'payment_method', 'date_from', 'date_to']))
                            No payments match your search criteria.
                        @else
                            You haven't made any payments yet.
                        @endif
                    </p>
                    @if(request()->hasAny(['status', 'payment_method', 'date_from', 'date_to']))
                        <a href="{{ route('agency.invoices.payment-history') }}" class="btn btn-outline-primary btn-rounded btn-fw">
                            <i class="mdi mdi-close me-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.8rem;
}
</style>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    document.getElementById('status').addEventListener('change', function() {
        if (this.value) {
            this.form.submit();
        }
    });

    document.getElementById('payment_method').addEventListener('change', function() {
        if (this.value) {
            this.form.submit();
        }
    });
});

function exportPayments() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'csv');

    // Create download link
    const url = '{{ route("agency.invoices.payment-history") }}?' + params.toString();
    window.open(url, '_blank');
}
</script>

@include('include/footer')