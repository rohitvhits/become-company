@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper invoice-module-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">{{ $title }}</h5>
            <div class="page-rightbtns">
                <a href="{{ route('admin.reports.invoices.index') }}" class="btn btn-secondary btn-sm invoice-action-btn me-2">
                    <i class="mdi mdi-arrow-left me-2"></i>Back to Reports
                </a>
                <a href="{{ route('admin.reports.invoices.paid', ['export' => 'csv']) }}" class="btn btn-success btn-sm invoice-action-btn btn-icon-text"><i class="mdi mdi-download"></i>Export CSV</a>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4 admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-filter me-2"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.reports.invoices.paid') }}" class="row">
                            <div class="col-md-3 mb-3">
                                <label for="agency_id" class="form-label">Agency</label>
                                <select name="agency_id" id="agency_id" class="form-control invoice-filter-control">
                                    <option value="">All Agencies</option>
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency->id }}" {{ $filters['agency_id'] == $agency->id ? 'selected' : '' }}>
                                            {{ $agency->agency_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="date_from" class="form-label">Paid From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control invoice-filter-control" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="date_to" class="form-label">Paid To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control invoice-filter-control" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm invoice-action-btn">
                                        <i class="mdi mdi-magnify me-2"></i>Apply
                                    </button>
                                    <a href="{{ route('admin.reports.invoices.paid') }}" class="btn btn-secondary btn-sm invoice-action-btn">
                                        <i class="mdi mdi-close me-2"></i>Clear
                                    </a>
                                </div>
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
                    <div class="card-body text-center">
                        <i class="mdi mdi-check-circle text-success mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-success">{{ number_format($stats['total_paid_invoices']) }}</h4>
                        <p class="mb-0 text-muted">Paid Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-currency-usd text-success mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-success">${{ number_format($stats['total_paid_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted">Total Paid Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-chart-line text-info mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-info">${{ number_format($stats['average_invoice_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted">Average Invoice</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-clock-check text-primary mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-primary">{{ number_format($stats['average_payment_time'] ?? 0, 1) }}</h4>
                        <p class="mb-0 text-muted">Avg Days to Pay</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Breakdown & Daily Trend -->
        <div class="row mb-4 admin-invoice-stats">
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-credit-card me-2"></i>Payment Method Breakdown
                        </h6>
                        <div class="chart-toggle-group" id="payment-method-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="payment-method-chart" data-container="payment-method-chart-container">
                                <i class="mdi mdi-chart-pie"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="payment-method-table" data-container="payment-method-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($paymentMethodBreakdown->count() > 0)
                            <!-- Chart View -->
                            <div id="payment-method-chart-container">
                                <canvas id="paymentMethodChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="payment-method-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Payment Method</th>
                                                <th class="text-center">Count</th>
                                                <th class="text-end">Amount</th>
                                                <th class="text-end">Avg Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($paymentMethodBreakdown as $method)
                                            <tr>
                                                <td>
                                                    <i class="mdi mdi-{{ $method->payment_method === 'stripe' ? 'credit-card' : ($method->payment_method === 'paypal' ? 'paypal' : 'cash') }} me-2"></i>
                                                    {{ ucfirst($method->payment_method) }}
                                                </td>
                                                <td class="text-center">{{ number_format($method->count) }}</td>
                                                <td class="text-end">${{ number_format($method->amount, 2) }}</td>
                                                <td class="text-end">${{ number_format($method->amount / $method->count, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No payment data available</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-chart-timeline me-2"></i>Recent Payment Trend (Last 30 Days)
                        </h6>
                        <div class="chart-toggle-group" id="daily-trend-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="daily-trend-chart" data-container="daily-trend-chart-container">
                                <i class="mdi mdi-chart-line"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="daily-trend-table" data-container="daily-trend-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($dailyTrend->count() > 0)
                            <!-- Chart View -->
                            <div id="daily-trend-chart-container">
                                <canvas id="dailyTrendChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="daily-trend-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th class="text-center">Payments</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dailyTrend->take(10) as $day)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                                <td class="text-center">{{ number_format($day->count) }}</td>
                                                <td class="text-end">${{ number_format($day->amount, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No recent payments</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Paid Invoices List -->
        <div class="row admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-format-list-bulleted me-2"></i>Paid Invoices Details ({{ $invoices->count() }} invoices)
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($invoices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Agency</th>
                                            <th class="text-end">Amount</th>
                                            <th>Created</th>
                                            <th>Paid Date</th>
                                            <th>Payment Method</th>
                                            <th class="text-center">Days to Pay</th>
                                            <th>Transaction ID</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices->take(100) as $invoice)
                                        @php
                                            $payment = $invoice->payments->first();
                                            $daysToPay = $invoice->paid_at ?
                                                \Carbon\Carbon::parse($invoice->created_at)->diffInDays($invoice->paid_at) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-decoration-none fw-bold">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->agency->agency_name ?? 'N/A' }}</td>
                                            <td class="text-end fw-bold">${{ number_format($invoice->total_amount, 2) }}</td>
                                            <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="text-success">
                                                    {{ $invoice->paid_at ? $invoice->paid_at->format('M d, Y') : 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($payment)
                                                    <span class="badge badge-info">
                                                        <i class="mdi mdi-{{ $payment->payment_method === 'stripe' ? 'credit-card' : ($payment->payment_method === 'paypal' ? 'paypal' : 'cash') }} me-1"></i>
                                                        {{ ucfirst($payment->payment_method) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $daysToPay <= 7 ? 'success' : ($daysToPay <= 30 ? 'warning' : 'danger') }}">
                                                    {{ $daysToPay }} days
                                                </span>
                                            </td>
                                            <td>
                                                @if($payment && $payment->transaction_id)
                                                    <code class="small">{{ substr($payment->transaction_id, 0, 12) }}...</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-primary btn-sm invoice-action-btn" title="View Invoice">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    @if($payment)
                                                    <a href="{{ route('admin.invoices.download-receipt', $payment) }}" class="btn btn-success btn-sm invoice-action-btn" title="Download Receipt">
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
                            @if($invoices->count() > 100)
                                <div class="mt-3">
                                    <p class="text-muted small">
                                        <i class="mdi mdi-information me-1"></i>
                                        Showing first 100 invoices. Export to view all data.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="mdi mdi-check-circle display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No paid invoices found</h5>
                                <p class="text-muted">Try adjusting your date filters to see more data.</p>
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
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
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

    .chart-toggle-group.hidden {
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
        color: #495057;
        background: #f8f9fa;
    }

    .table td {
        font-size: 0.875rem;
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.6em;
        border-radius: 6px;
    }

    .card-header {
        border-bottom: 1px solid #e9ecef;
        background: #fff !important;
    }

    .card-title {
        color: #495057;
        font-weight: 600;
    }

    code {
        background-color: #f8f9fa;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
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
    @if($paymentMethodBreakdown->count() > 0)
    // Payment Method Breakdown Pie Chart
    const paymentCtx = document.getElementById('paymentMethodChart');
    if (paymentCtx) {
        const paymentData = @json($paymentMethodBreakdown->mapWithKeys(function ($item) {
            return [ucfirst($item->payment_method) => $item->amount];
        }));
        const paymentLabels = Object.keys(paymentData);
        const paymentAmounts = Object.values(paymentData);
        const paymentColors = paymentLabels.map(method => {
            switch(method.toLowerCase()) {
                case 'stripe': return '#635bff';
                case 'paypal': return '#0070ba';
                case 'cash': return '#28a745';
                case 'check': return '#fd7e14';
                default: return '#36a9f3';
            }
        });

        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: paymentLabels,
                datasets: [{
                    data: paymentAmounts,
                    backgroundColor: paymentColors,
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

    @if($dailyTrend->count() > 0)
    // Daily Payment Trend Line Chart
    const dailyCtx = document.getElementById('dailyTrendChart');
    if (dailyCtx) {
        const dailyData = @json($dailyTrend->take(15)->reverse()->values());
        const dailyLabels = dailyData.map(day => {
            const date = new Date(day.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const dailyAmounts = dailyData.map(day => day.amount);
        const dailyCounts = dailyData.map(day => day.count);

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Payment Amount',
                    data: dailyAmounts,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                }, {
                    label: 'Payment Count',
                    data: dailyCounts,
                    borderColor: '#36a9f3',
                    backgroundColor: 'rgba(54, 169, 243, 0.1)',
                    tension: 0.4,
                    fill: false,
                    yAxisID: 'y1'
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
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return 'Amount: $' + context.parsed.y.toLocaleString();
                                } else {
                                    return 'Count: ' + context.parsed.y + ' payments';
                                }
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Enhanced Chart/Table Toggle Functions
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