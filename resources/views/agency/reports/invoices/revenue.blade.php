@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper invoice-module-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold invoice-page-title">{{ $title }}</h5>

            <div class="page-rightbtns">
                <a href="{{ route('agency.reports.invoices.index') }}" class="btn btn-secondary btn-sm invoice-action-btn btn-icon-text">
                    <i class="mdi mdi-arrow-left"></i>
                    Back to Reports
                </a>
                <a href="{{ route('admin.reports.invoices.revenue', ['export' => 'csv']) }}" class="btn btn-success btn-sm invoice-action-btn btn-icon-text">
                    <i class="mdi mdi-download"></i>
                    Export CSV
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card admin-invoice-stats">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-filter-variant"></i>
                            Filter Options
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('agency.reports.invoices.revenue') }}" class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control invoice-filter-control" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control invoice-filter-control" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm invoice-action-btn">
                                        <i class="mdi mdi-magnify"></i>
                                        Apply Filters
                                    </button>
                                    <a href="{{ route('agency.reports.invoices.revenue') }}" class="btn btn-secondary btn-sm invoice-action-btn">
                                        <i class="mdi mdi-close"></i>
                                        Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Statistics -->
        <div class="row mb-4">
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-cash-multiple text-success mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['total_revenue'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Total Revenue</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-currency-usd text-primary mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['net_revenue'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Net Revenue</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-file-document text-info mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">{{ number_format($stats['total_invoices']) }}</h4>
                        <p class="mb-0 text-muted small">Total Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-chart-line text-success mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['average_invoice_value'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Avg Invoice</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-credit-card text-warning mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['total_processing_fees'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Processing Fees</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-undo-variant text-danger mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['total_refunds'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Total Refunds</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Growth -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="mdi mdi-trending-up text-success"></i>
                            Year-over-Year Growth
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">Current Period</h6>
                                    <h3 class="text-success font-weight-bold">${{ number_format($stats['total_revenue'], 0) }}</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">Previous Year</h6>
                                    <h3 class="text-muted font-weight-bold">${{ number_format($previousYearRevenue, 0) }}</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">Growth Rate</h6>
                                    <h3 class="font-weight-bold {{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 2) }}%
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Breakdown -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-calendar-month text-primary"></i>
                            Monthly Revenue
                        </h5>
                        <div class="chart-toggle-group" id="monthly-revenue-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="monthly-revenue-chart" data-container="monthly-revenue-chart-container">
                                <i class="mdi mdi-chart-bar"></i>
                                Chart
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="monthly-revenue-table" data-container="monthly-revenue-table-container">
                                <i class="mdi mdi-table"></i>
                                Table
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart View -->
                        <div id="monthly-revenue-chart-container">
                            <canvas id="monthlyRevenueChart" width="400" height="300"></canvas>
                        </div>

                        <!-- Table View -->
                        <div id="monthly-revenue-table-container" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-right">Revenue</th>
                                            <th class="text-center">Invoices</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($monthlyRevenue as $month)
                                        <tr>
                                            <td>{{ $month->month_name }}</td>
                                            <td class="text-right font-weight-bold">${{ number_format($month->revenue, 2) }}</td>
                                            <td class="text-center">{{ $month->invoice_count }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No revenue data available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quarterly Revenue Breakdown -->
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-calendar-range text-info"></i>
                            Quarterly Revenue
                        </h5>
                        <div class="chart-toggle-group" id="quarterly-revenue-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="quarterly-revenue-chart" data-container="quarterly-revenue-chart-container">
                                <i class="mdi mdi-chart-bar"></i>
                                Chart
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="quarterly-revenue-table" data-container="quarterly-revenue-table-container">
                                <i class="mdi mdi-table"></i>
                                Table
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart View -->
                        <div id="quarterly-revenue-chart-container">
                            <canvas id="quarterlyRevenueChart" width="400" height="300"></canvas>
                        </div>

                        <!-- Table View -->
                        <div id="quarterly-revenue-table-container" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Quarter</th>
                                            <th class="text-right">Revenue</th>
                                            <th class="text-center">Invoices</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($quarterlyRevenue as $quarter)
                                        <tr>
                                            <td>{{ $quarter->quarter_label }}</td>
                                            <td class="text-right font-weight-bold">${{ number_format($quarter->revenue, 2) }}</td>
                                            <td class="text-center">{{ $quarter->invoice_count }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No quarterly data available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Payment Method -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-credit-card-multiple text-success"></i>
                            Revenue by Payment Method
                        </h5>
                        <div class="chart-toggle-group" id="payment-method-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="payment-method-chart" data-container="payment-method-chart-container">
                                <i class="mdi mdi-chart-pie"></i>
                                Chart
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="payment-method-table" data-container="payment-method-table-container">
                                <i class="mdi mdi-table"></i>
                                Table
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart View -->
                        <div id="payment-method-chart-container">
                            <canvas id="paymentMethodChart" width="400" height="250"></canvas>
                        </div>

                        <!-- Table View -->
                        <div id="payment-method-table-container" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Payment Method</th>
                                            <th class="text-right">Revenue</th>
                                            <th class="text-center">Transactions</th>
                                            <th class="text-right">Processing Fees</th>
                                            <th class="text-right">Net Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paymentMethodRevenue as $method)
                                        <tr>
                                            <td>
                                                <span class="badge badge-primary">{{ $method->payment_method }}</span>
                                            </td>
                                            <td class="text-right font-weight-bold">${{ number_format($method->revenue, 2) }}</td>
                                            <td class="text-center">{{ $method->transaction_count }}</td>
                                            <td class="text-right text-warning">${{ number_format($method->processing_fees, 2) }}</td>
                                            <td class="text-right text-success font-weight-bold">${{ number_format($method->net_revenue, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No payment method data available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-chart-timeline-variant text-warning"></i>
                            Daily Revenue Trend (Last 30 Days)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-right">Revenue</th>
                                        <th class="text-center">Invoice Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dailyRevenue as $daily)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($daily->date)->format('M d, Y') }}</td>
                                        <td class="text-right font-weight-bold">${{ number_format($daily->revenue, 2) }}</td>
                                        <td class="text-center">{{ $daily->invoice_count }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No daily revenue data available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Revenue Trend (Last 30 Days) -->
        <div class="row mb-4">
            <div class="col-md-12">
                
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
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($monthlyRevenue->count() > 0)
    // Monthly Revenue Bar Chart
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart');
    if (monthlyRevenueCtx) {
        const monthlyData = @json($monthlyRevenue);
        const monthlyLabels = monthlyData.map(m => m.month_name);
        const monthlyRevenues = monthlyData.map(m => m.revenue);

        new Chart(monthlyRevenueCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Revenue',
                    data: monthlyRevenues,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
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
                                return 'Revenue: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    @if($quarterlyRevenue->count() > 0)
    // Quarterly Revenue Bar Chart
    const quarterlyRevenueCtx = document.getElementById('quarterlyRevenueChart');
    if (quarterlyRevenueCtx) {
        const quarterlyData = @json($quarterlyRevenue);
        const quarterlyLabels = quarterlyData.map(q => q.quarter_label);
        const quarterlyRevenues = quarterlyData.map(q => q.revenue);

        new Chart(quarterlyRevenueCtx, {
            type: 'bar',
            data: {
                labels: quarterlyLabels,
                datasets: [{
                    label: 'Revenue',
                    data: quarterlyRevenues,
                    backgroundColor: 'rgba(23, 162, 184, 0.8)',
                    borderColor: 'rgba(23, 162, 184, 1)',
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
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    @if($paymentMethodRevenue->count() > 0)
    // Payment Method Doughnut Chart
    const paymentMethodCtx = document.getElementById('paymentMethodChart');
    if (paymentMethodCtx) {
        const methodData = @json($paymentMethodRevenue);
        const methodLabels = methodData.map(m => m.payment_method);
        const methodRevenues = methodData.map(m => m.revenue);
        const methodColors = methodLabels.map(method => {
            switch(method.toLowerCase()) {
                case 'stripe': return '#635bff';
                case 'paypal': return '#0070ba';
                case 'cash': return '#28a745';
                case 'check': return '#fd7e14';
                case 'manual': return '#dc3545';
                default: return '#36a9f3';
            }
        });

        new Chart(paymentMethodCtx, {
            type: 'doughnut',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodRevenues,
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
