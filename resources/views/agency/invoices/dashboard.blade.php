@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper agency-invoice-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Invoice Dashboard</h5>
            <div class="page-rightbtns">
                <a href="{{ route('agency.invoices.index') }}" class="invoice-badge-btn btn-primary btn-sm invoice-action-btn">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>View All Invoices
                </a>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row">
            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-info">{{ $stats['total_invoices'] }}</h4>
                        <p class="mb-0 text-muted">Total Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-warning">{{ $stats['pending_invoices'] }}</h4>
                        <p class="mb-0 text-muted">Pending Payment</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-success">{{ $stats['paid_invoices'] }}</h4>
                        <p class="mb-0 text-muted">Paid Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-danger">{{ $stats['overdue_invoices'] }}</h4>
                        <p class="mb-0 text-muted">Overdue</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-danger">${{ number_format($stats['total_outstanding'], 0) }}</h4>
                        <p class="mb-0 text-muted">Outstanding</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-success">${{ number_format($stats['total_paid_ytd'], 0) }}</h4>
                        <p class="mb-0 text-muted">Paid YTD</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Invoices -->
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="mdi mdi-file-document me-2"></i>Recent Invoices
                            </h6>
                            <a href="{{ route('agency.invoices.index') }}" class="invoice-badge-btn btn-primary btn-sm invoice-action-btn">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($recentInvoices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($recentInvoices as $invoice)
                                        <tr>
                                            <td>
                                                <a href="{{ route('agency.invoices.show', $invoice) }}"
                                                   class="text-decoration-none fw-bold">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                            <td class="fw-semibold">${{ number_format($invoice->total_amount, 2) }}</td>
                                            <td>
                                                @if($invoice->status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($invoice->status === 'overdue')
                                                    <span class="badge bg-danger">Overdue</span>
                                                @elseif($invoice->status === 'sent')
                                                    <span class="badge bg-primary">Sent</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($invoice->status !== 'paid' && $invoice->status !== 'draft')
                                                        <a href="{{ route('agency.invoices.payment', $invoice) }}"
                                                           class="invoice-badge-btn btn-success btn-sm invoice-action-btn" title="Pay">
                                                            <i class="mdi mdi-credit-card"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('agency.invoices.show', $invoice) }}"
                                                       class="invoice-badge-btn btn-primary btn-sm invoice-action-btn" title="View">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-file-document display-1 text-muted mb-3"></i>
                                <p class="text-muted">No recent invoices</p>
                            </div>
                        @endif
                </div>
            </div>
        </div>

        <!-- Alerts & Notifications -->
        <div class="col-lg-4">
            <!-- Overdue Invoices Alert -->
            @if($overdueInvoices->count() > 0)
                <div class="grid-margin stretch-card">
                    <div class="card border-start border-danger border-4">
                    <div class="card-header bg-danger text-white">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-alert-triangle me-2"></i>Overdue Invoices
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">You have {{ $overdueInvoices->count() }} overdue invoice(s) requiring immediate attention.</p>
                        @foreach($overdueInvoices as $invoice)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $invoice->invoice_number }}</div>
                                    <small class="text-muted">Due: {{ $invoice->due_date->format('M d, Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-danger">${{ number_format($invoice->balance, 2) }}</div>
                                    <a href="{{ route('agency.invoices.payment', $invoice) }}"
                                       class="invoice-badge-btn btn-danger btn-sm invoice-action-btn">Pay Now</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                </div>
            @endif

            <!-- Due Soon Invoices -->
            @if($dueSoonInvoices->count() > 0)
                <div class="grid-margin stretch-card">
                    <div class="card border-start border-warning border-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-clock me-2"></i>Due Soon
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">{{ $dueSoonInvoices->count() }} invoice(s) due within the next 7 days.</p>
                        @foreach($dueSoonInvoices as $invoice)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $invoice->invoice_number }}</div>
                                    <small class="text-muted">Due: {{ $invoice->due_date->format('M d, Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">${{ number_format($invoice->balance, 2) }}</div>
                                    <a href="{{ route('agency.invoices.show', $invoice) }}"
                                       class="invoice-badge-btn btn-primary btn-sm invoice-action-btn">View</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="grid-margin stretch-card">
                <div class="card">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="mdi mdi-flash me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('agency.invoices.index', ['status' => 'sent']) }}"
                           class="btn btn-primary btn-sm invoice-action-btn mb-2">
                            <i class="mdi mdi-clock me-2"></i>View Pending Payments
                        </a>
                        <a href="{{ route('agency.invoices.payment-history') }}"
                           class="btn btn-secondary btn-sm invoice-action-btn mb-2">
                            <i class="mdi mdi-history me-2"></i>Payment History
                        </a>
                        <a href="{{ route('agency.invoices.index') }}"
                           class="btn btn-info btn-sm invoice-action-btn mb-2">
                            <i class="mdi mdi-magnify me-2"></i>Search Invoices
                        </a>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics Chart -->
    @if(count($monthlyStats) > 0)
        <div class="row mt-4">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-chart-bar me-2"></i>Monthly Overview (Last 12 Months)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to cards
    document.querySelectorAll('.card').forEach(function(card, index) {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('fade-in');
    });
});
</script>



<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(count($monthlyStats) > 0)
    // Monthly Chart
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = @json($monthlyStats);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Invoice Amount',
                data: monthlyData.map(item => item.amount),
                backgroundColor: 'rgba(54, 169, 243, 0.8)',
                borderColor: 'rgba(54, 169, 243, 1)',
                borderWidth: 1
            }, {
                label: 'Amount Paid',
                data: monthlyData.map(item => item.paid),
                backgroundColor: 'rgba(70, 195, 95, 0.8)',
                borderColor: 'rgba(70, 195, 95, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    @endif

    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        fetch('/api/agency/invoices/stats')
            .then(response => response.json())
            .then(data => {
                // Update stats cards
                updateStatsCards(data);
            })
            .catch(error => console.error('Error refreshing stats:', error));
    }, 300000); // 5 minutes

    function updateStatsCards(stats) {
        // Update the numbers in the stats cards
        // This would require more specific selectors based on your card structure
    }
});
</script>

@include('include/footer')