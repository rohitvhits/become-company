@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper agency-invoice-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">My Invoices</h5>
            <div class="page-rightbtns">
                <a href="{{ route('agency.reports.invoices.index') }}" class="btn btn-info invoice-action-btn btn-sm">
                    <i class="mdi mdi-chart-line me-2"></i>Report
                </a>
                <a href="{{ route('agency.invoices.dashboard') }}" class="btn btn-primary invoice-action-btn btn-sm">
                    <i class="mdi mdi-chart-line me-2"></i>Dashboard
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card compact-stats-card">
                    <div class="card-body text-center py-3">
                        <h5 class="mb-1 text-primary font-weight-bold">{{ $stats['total'] }}</h5>
                        <p class="mb-0 text-muted small">Total</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card compact-stats-card">
                    <div class="card-body text-center py-3">
                        <h5 class="mb-1 text-warning font-weight-bold">{{ $stats['pending'] }}</h5>
                        <p class="mb-0 text-muted small">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card compact-stats-card">
                    <div class="card-body text-center py-3">
                        <h5 class="mb-1 text-success font-weight-bold">{{ $stats['paid'] }}</h5>
                        <p class="mb-0 text-muted small">Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card compact-stats-card">
                    <div class="card-body text-center py-3">
                        <h5 class="mb-1 text-danger font-weight-bold">{{ $stats['overdue'] }}</h5>
                        <p class="mb-0 text-muted small">Overdue</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card compact-stats-card">
                    <div class="card-body text-center py-3">
                        <h5 class="mb-1 text-danger font-weight-bold">${{ number_format($stats['total_amount_due'], 0) }}</h5>
                        <p class="mb-0 text-muted small">Outstanding</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 mb-3">
                <div class="card compact-stats-card">
                    <div class="card-body text-center py-3">
                        <h5 class="mb-1 text-success font-weight-bold">${{ number_format($stats['total_paid_this_month'], 0) }}</h5>
                        <p class="mb-0 text-muted small">This Month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card compact-filter-card">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('agency.invoices.index') }}" class="row align-items-end">
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <label for="status" class="form-label small mb-1">Status</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <label for="date_from" class="form-label small mb-1">From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <label for="date_to" class="form-label small mb-1">To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-2">
                                <label for="search" class="form-label small mb-1">Search</label>
                                <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Invoice number, description..." value="{{ request('search') }}">
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-primary invoice-action-btn btn-sm me-2">
                                    <i class="mdi mdi-magnify"></i> Search
                                </button>
                                @if(request()->hasAny(['status', 'date_from', 'date_to', 'search']))
                                    <a href="{{ route('agency.invoices.index') }}" class="btn btn-secondary invoice-action-btn btn-sm">
                                        <i class="mdi mdi-close"></i> Close
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Invoices ({{ $invoices->total() }})</h6>
                        </div>
                        @if($invoices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('agency.invoices.show', $invoice) }}" class="text-decoration-none fw-bold">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="{{ $invoice->is_overdue ? 'text-danger fw-bold' : '' }}">
                                            {{ $invoice->due_date->format('M d, Y') }}
                                        </span>
                                        @if($invoice->is_overdue)
                                            <small class="text-danger d-block">
                                                ({{ abs($invoice->days_until_due) }} days overdue)
                                            </small>
                                        @elseif($invoice->days_until_due <= 3 && $invoice->days_until_due > 0)
                                            <small class="text-warning d-block">
                                                (Due in {{ $invoice->days_until_due }} days)
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $invoice->title ?: 'Invoice #' . $invoice->invoice_number }}</div>
                                        @if($invoice->description)
                                            @php
                                                $descriptionLength = strlen($invoice->description);
                                                $shortDescription = Str::limit($invoice->description, 40, '');
                                            @endphp
                                            <small class="text-muted description-cell" data-invoice-id="{{ $invoice->id }}">
                                                <span class="description-short">{{ $shortDescription }}@if($descriptionLength > 40)<span class="text-primary ms-1 cursor-pointer" onclick="toggleDescription({{ $invoice->id }})" style="cursor: pointer; text-decoration: underline;">...</span>@endif</span>
                                                @if($descriptionLength > 40)
                                                    <span class="description-full" style="display: none;">{{ $invoice->description }} <span class="text-primary ms-1 cursor-pointer" onclick="toggleDescription({{ $invoice->id }})" style="cursor: pointer; text-decoration: underline;">Show less</span></span>
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">${{ number_format($invoice->total_amount, 2) }}</span>
                                        @if($invoice->total_paid > 0 && $invoice->status !== 'paid')
                                            <small class="text-success d-block">
                                                ${{ number_format($invoice->total_paid, 2) }} paid
                                            </small>
                                        @endif
                                    </td>
                                    <td>
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
                                            <span class="invoice-status-badge sent invoice-action-btn">
                                                <i class="mdi mdi-pencil me-1"></i>{{ ucfirst($invoice->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('agency.invoices.show', $invoice) }}"
                                               class="btn btn-primary invoice-action-btn btn-sm" title="View">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('agency.invoices.download', $invoice) }}"
                                               class="btn btn-secondary invoice-action-btn btn-sm" title="Download">
                                                <i class="mdi mdi-download"></i>
                                            </a>
                                            @if($invoice->status !== 'paid' && $invoice->status !== 'draft')
                                                <a href="{{ route('agency.invoices.payment', $invoice) }}"
                                                   class="btn btn-success invoice-action-btn btn-sm" title="Pay Now">
                                                    <i class="mdi mdi-credit-card"></i>
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
                <div class="card-footer bg-white pull-right pegination-margin">
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="mdi mdi-file-document display-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">No invoices found</h5>
                                    <p class="text-muted">
                                        @if(request()->hasAny(['status', 'date_from', 'date_to', 'search']))
                                            No invoices match your search criteria.
                                        @else
                                            You don't have any invoices yet.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['status', 'date_from', 'date_to', 'search']))
                                        <a href="{{ route('agency.invoices.index') }}" class="btn btn-primary invoice-action-btn btn-sm">
                                            <i class="mdi mdi-close"></i> Clear Filters
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

<script>
function toggleDescription(invoiceId) {
    const cell = document.querySelector(`.description-cell[data-invoice-id="${invoiceId}"]`);
    if (!cell) return;

    const shortSpan = cell.querySelector('.description-short');
    const fullSpan = cell.querySelector('.description-full');

    if (shortSpan && fullSpan) {
        if (shortSpan.style.display === 'none') {
            // Show short, hide full
            shortSpan.style.display = '';
            fullSpan.style.display = 'none';
        } else {
            // Show full, hide short
            shortSpan.style.display = 'none';
            fullSpan.style.display = '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when status filter changes
    document.getElementById('status').addEventListener('change', function() {
        if (this.value) {
            this.form.submit();
        }
    });

    // Highlight overdue invoices
    document.querySelectorAll('tr').forEach(function(row) {
        const statusBadge = row.querySelector('.invoice-status-badge.overdue');
        if (statusBadge) {
            row.style.backgroundColor = '#fff5f5';
        }
    });

    // Add fade-in animation to stats cards
    document.querySelectorAll('.invoice-stats-card').forEach(function(card, index) {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('fade-in');
    });
});
</script>

<style>
/* Compact Stats Cards */
.compact-stats-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    min-height: 80px;
}

.compact-stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.compact-stats-card .card-body {
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 60px;
}

.compact-stats-card h5 {
    font-size: 1.5rem;
    margin: 0;
    line-height: 1.2;
}

.compact-stats-card p {
    font-size: 0.8rem;
    margin: 0;
    line-height: 1;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .compact-stats-card h5 {
        font-size: 1.3rem;
    }

    .compact-stats-card p {
        font-size: 0.75rem;
    }

    .compact-stats-card .card-body {
        padding: 0.5rem;
        min-height: 50px;
    }
}

@media (max-width: 576px) {
    .compact-stats-card h5 {
        font-size: 1.2rem;
    }

    .compact-stats-card p {
        font-size: 0.7rem;
    }
}

/* Compact Filter Card */
.compact-filter-card {
    border: none;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.compact-filter-card .card-body {
    padding: 1rem;
}

.compact-filter-card .form-control-sm {
    padding: 0.375rem 0.5rem;
    font-size: 0.875rem;
}

.compact-filter-card .form-label.small {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.compact-filter-card .btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Remove extra spacing on small screens */
@media (max-width: 992px) {
    .row.mb-3 {
        margin-bottom: 1rem !important;
    }

    .compact-filter-card .card-body {
        padding: 0.75rem;
    }
}

@media (max-width: 768px) {
    .compact-filter-card .form-control-sm {
        padding: 0.25rem 0.375rem;
        font-size: 0.8rem;
    }

    .compact-filter-card .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
}
</style>

@include('include/footer')