@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold invoice-page-title">{{ $title }}</h5>
            <div class="page-rightbtns">
                <a href="{{ route('agency.reports.invoices.index') }}" class="btn btn-secondary invoice-action-btn btn-sm btn-icon-text me-2">
                    <i class="mdi mdi-arrow-left"></i>
                    Back to Reports
                </a>
                <a href="{{ route('agency.reports.invoices.payments', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn invoice-action-btn btn-success btn-sm btn-icon-text">
                    <i class="mdi mdi-download"></i>
                    Export CSV
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row mb-3 admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('agency.reports.invoices.payments') }}" class="row align-items-end">
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <label for="date_from" class="form-label small mb-1">From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm invoice-filter-control" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <label for="date_to" class="form-label small mb-1">To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm invoice-filter-control" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <label for="payment_method" class="form-label small mb-1">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-control form-control-sm invoice-filter-control">
                                    <option value="">All Methods</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method }}" {{ $filters['payment_method'] === $method ? 'selected' : '' }}>
                                            {{ ucfirst($method) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-primary invoice-action-btn btn-sm me-2">
                                    <i class="mdi mdi-magnify"></i> Apply Filters
                                </button>
                                @if(collect($filters)->filter()->isNotEmpty())
                                    <a href="{{ route('agency.reports.invoices.payments') }}" class="btn btn-secondary invoice-action-btn btn-sm">
                                        <i class="mdi mdi-close"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4 admin-invoice-stats">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-info font-weight-bold">{{ $stats['total_payments'] }}</h4>
                        <p class="mb-0 text-muted small">Total Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-success font-weight-bold">${{ number_format($stats['total_payment_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Total Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-primary font-weight-bold">${{ number_format($stats['average_payment_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Avg Payment</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-warning font-weight-bold">{{ $stats['unique_invoices'] }}</h4>
                        <p class="mb-0 text-muted small">Unique Invoices</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4 admin-invoice-stats">
            <!-- Payment Method Breakdown -->
            <div class="col-lg-6 mb-4">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-credit-card me-2"></i>Payment Method Statistics
                        </h6>
                        <div class="chart-toggle-group" id="payment-stats-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="payment-stats-chart" data-container="payment-stats-chart-container">
                                <i class="mdi mdi-chart-pie"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="payment-stats-table" data-container="payment-stats-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($paymentMethodStats->count() > 0)
                            <!-- Chart View -->
                            <div id="payment-stats-chart-container">
                                <canvas id="paymentMethodStatsChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="payment-stats-table-container" style="display: none;">
                                @foreach($paymentMethodStats as $method)
                                    <div class="payment-method-stat mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            @if($method->payment_method == 'manual')
                                                @php $class= "secondary"; @endphp
                                            @else
                                                @php $class= "info"; @endphp
                                            @endif
                                            <span class="badge bg-{{$class}}">{{ ucfirst($method->payment_method) }}</span>
                                            <span class="text-muted small">{{ $method->count }} payments</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>${{ number_format($method->amount, 2) }}</strong> total
                                            </div>
                                            <div class="text-muted small">
                                                Avg: ${{ number_format($method->avg_amount, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No payment method data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daily Payment Trend -->
            <div class="col-lg-6 mb-4">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-chart-line me-2"></i>Daily Payment Trend (Last 30 Days)
                        </h6>
                        <div class="chart-toggle-group" id="daily-payments-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="daily-payments-chart" data-container="daily-payments-chart-container">
                                <i class="mdi mdi-chart-line"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="daily-payments-table" data-container="daily-payments-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($dailyPayments->count() > 0)
                            <!-- Chart View -->
                            <div id="daily-payments-chart-container">
                                <canvas id="dailyPaymentsChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="daily-payments-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Payments</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dailyPayments->take(10) as $day)
                                                <tr>
                                                    <td>{{ date('M d, Y', strtotime($day->date)) }}</td>
                                                    <td>{{ $day->count }}</td>
                                                    <td>${{ number_format($day->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No payment trend data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Failed Payments Section -->
        @if($failedPayments->count() > 0)
        <div class="row mb-4 admin-invoice-stats">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="mdi mdi-alert-circle text-danger me-2"></i>
                            Failed Payments ({{ $failedPayments->count() }} payments)
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice #</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($failedPayments->take(10) as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('agency.invoices.show', $payment->invoice) }}" class="text-decoration-none">
                                                    {{ $payment->invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>${{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                @if($payment->payment_method == 'manual')
                                                    @php $class= "secondary"; @endphp
                                                @else
                                                    @php $class= "info"; @endphp
                                                @endif
                                                <span class="badge badge-{{$class}}">{{ ucfirst($payment->payment_method) }}</span>
                                            </td>
                                            <td>{{ $payment->failure_reason ?? 'Unknown' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Detailed Payments List -->
        <div class="row admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="mdi mdi-format-list-bulleted me-2"></i>
                            Payment Details ({{ $payments->count() }} payments)
                        </h6>
                        @if($payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Payment Date</th>
                                            <th>Invoice #</th>
                                            <th>Amount</th>
                                            <th>Payment Method</th>
                                            <th>Transaction ID</th>
                                            <th>Status</th>
                                            <th>Processing Fee</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('agency.invoices.show', $payment->invoice) }}" class="text-decoration-none fw-bold">
                                                        {{ $payment->invoice->invoice_number }}
                                                    </a>
                                                </td>
                                                <td>${{ number_format($payment->amount, 2) }}</td>
                                                <td>
                                                    @if($payment->payment_method == 'manual')
                                                        @php $class= "secondary"; @endphp
                                                    @else
                                                        @php $class= "info"; @endphp
                                                    @endif
                                                    <span class="badge bg-{{$class}}">
                                                        {{ ucfirst($payment->payment_method) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="font-monospace small">
                                                        {{ $payment->transaction_id ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                                <td>${{ number_format($payment->processing_fee ?? 0, 2) }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('agency.invoices.show', $payment->invoice) }}"
                                                           class="btn btn-primary invoice-action-btn btn-sm" title="View Invoice">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        @if($payment->status === 'completed')
                                                            <a href="{{ route('agency.invoices.download-receipt', $payment) }}"
                                                               class="btn btn-success invoice-action-btn btn-sm" title="Download Receipt">
                                                                <i class="mdi mdi-receipt"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-credit-card display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No payments found</h5>
                                <p class="text-muted">No payments match your filter criteria.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stats-card .card-body {
    padding: 1rem;
}

.stats-card h4 {
    font-size: 1.5rem;
    margin: 0;
}

.chart-card {
    border: none;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.chart-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.chart-toggle-group {
    display: flex;
    background: #f8f9fa;
    border-radius: 6px;
    padding: 2px;
    transition: all 0.3s ease;
}

.chart-toggle-btn {
    border: none;
    background: transparent;
    padding: 8px 12px;
    border-radius: 4px;
    color: #6c757d;
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 0.9rem;
}

.chart-toggle-btn:hover {
    color: #495057;
    background: rgba(255, 255, 255, 0.7);
}

.chart-toggle-btn.active {
    background: #fff;
    color: #0d6efd;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    background: #fff !important;
    padding: 1rem;
}

.card-title {
    color: #495057;
    font-weight: 600;
}

.payment-method-stat {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.table th {
    font-weight: 600;
    color: #495057;
    border-top: none;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.5rem;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}

.border-danger {
    border-color: #dc3545 !important;
}

@media (max-width: 768px) {
    .stats-card h4 {
        font-size: 1.25rem;
    }

    .payment-method-stat {
        padding: 0.75rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .chart-toggle-btn {
        padding: 6px 8px;
        font-size: 0.8rem;
    }
}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($paymentMethodStats->count() > 0)
    // Payment Method Stats Pie Chart
    const paymentStatsCtx = document.getElementById('paymentMethodStatsChart');
    if (paymentStatsCtx) {
        const paymentStatsData = @json($paymentMethodStats);
        const methodLabels = paymentStatsData.map(item => item.payment_method.charAt(0).toUpperCase() + item.payment_method.slice(1));
        const methodAmounts = paymentStatsData.map(item => item.amount);
        const methodColors = ['#36a9f3', '#28a745', '#ffc107', '#dc3545', '#6c757d'];

        new Chart(paymentStatsCtx, {
            type: 'doughnut',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodAmounts,
                    backgroundColor: methodColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = '$' + context.parsed.toLocaleString();
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    @if($dailyPayments->count() > 0)
    // Daily Payments Line Chart
    const dailyPaymentsCtx = document.getElementById('dailyPaymentsChart');
    if (dailyPaymentsCtx) {
        const dailyPaymentsData = @json($dailyPayments->reverse()->values());
        const paymentLabels = dailyPaymentsData.map(day => {
            const date = new Date(day.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const paymentAmounts = dailyPaymentsData.map(day => day.amount);

        new Chart(dailyPaymentsCtx, {
            type: 'line',
            data: {
                labels: paymentLabels,
                datasets: [{
                    label: 'Payment Amount',
                    data: paymentAmounts,
                    borderColor: '#36a9f3',
                    backgroundColor: 'rgba(54, 169, 243, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
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
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Amount: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Chart/Table Toggle Functions
    function setupChartToggle() {
        const toggleButtons = document.querySelectorAll('.chart-toggle-btn');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const isChart = this.hasAttribute('data-chart');
                const isTable = this.hasAttribute('data-table');
                const container = this.getAttribute('data-container');
                const toggleGroup = this.closest('.chart-toggle-group');

                // Remove active class from siblings
                toggleGroup.querySelectorAll('.chart-toggle-btn').forEach(btn => {
                    btn.classList.remove('active');
                });

                // Add active class to clicked button
                this.classList.add('active');

                // Handle chart view
                if (isChart) {
                    const chartContainer = document.getElementById(container);
                    const tableContainer = document.getElementById(container.replace('chart', 'table'));

                    if (chartContainer && tableContainer) {
                        chartContainer.style.display = 'block';
                        tableContainer.style.display = 'none';
                    }
                }

                // Handle table view
                if (isTable) {
                    const chartContainer = document.getElementById(container.replace('table', 'chart'));
                    const tableContainer = document.getElementById(container);

                    if (chartContainer && tableContainer) {
                        chartContainer.style.display = 'none';
                        tableContainer.style.display = 'block';
                    }
                }
            });
        });
    }

    // Initialize chart toggles
    setupChartToggle();
});
</script>

@include('include/footer')