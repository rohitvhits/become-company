@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper invoice-module-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">{{ $title }}</h5>
            <div class="page-rightbtns">
                <a href="{{ route('admin.reports.invoices.index') }}" class="btn btn-secondary invoice-action-btn btn-sm me-2">
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
                        <form method="GET" action="{{ route('admin.reports.invoices.payments') }}" class="row">
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
                            <div class="col-md-2 mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-control invoice-filter-control">
                                    <option value="">All Methods</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method }}" {{ $filters['payment_method'] === $method ? 'selected' : '' }}>
                                            {{ ucfirst($method) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control invoice-filter-control" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control invoice-filter-control" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm invoice-action-btn">
                                        <i class="mdi mdi-magnify me-2"></i>Apply Filters
                                    </button>
                                    <a href="{{ route('admin.reports.invoices.payments') }}" class="btn btn-secondary btn-sm invoice-action-btn">
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
                        <i class="mdi mdi-credit-card text-info mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-info">{{ number_format($stats['total_payments']) }}</h4>
                        <p class="mb-0 text-muted">Total Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-currency-usd text-success mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-success">${{ number_format($stats['total_payment_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted">Total Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-chart-line text-primary mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-primary">${{ number_format($stats['average_payment_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted">Average Payment</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-account-multiple text-warning mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-warning">{{ number_format($stats['unique_payers']) }}</h4>
                        <p class="mb-0 text-muted">Unique Payers</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Statistics & Failed Payments -->
        <div class="row mb-4 admin-invoice-stats">
            <div class="col-md-8">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-credit-card me-2"></i>Payment Method Statistics
                        </h6>
                        <div class="chart-toggle-group" id="payment-method-stats-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="payment-method-stats-chart" data-container="payment-method-stats-chart-container">
                                <i class="mdi mdi-chart-pie"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="payment-method-stats-table" data-container="payment-method-stats-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($paymentMethodStats->count() > 0)
                            <!-- Chart View -->
                            <div id="payment-method-stats-chart-container">
                                <canvas id="paymentMethodStatsChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="payment-method-stats-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Payment Method</th>
                                                <th class="text-center">Count</th>
                                                <th class="text-end">Total Amount</th>
                                                <th class="text-end">Average Amount</th>
                                                <th class="text-end">Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($paymentMethodStats as $method)
                                            @php
                                                $percentage = $stats['total_payment_amount'] > 0 ?
                                                    ($method->amount / $stats['total_payment_amount']) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <i class="mdi mdi-{{ $method->payment_method === 'stripe' ? 'credit-card' : ($method->payment_method === 'paypal' ? 'paypal' : 'cash') }} me-2"></i>
                                                    {{ ucfirst($method->payment_method) }}
                                                </td>
                                                <td class="text-center">{{ number_format($method->count) }}</td>
                                                <td class="text-end">${{ number_format($method->amount, 2) }}</td>
                                                <td class="text-end">${{ number_format($method->avg_amount, 2) }}</td>
                                                <td class="text-end">
                                                    <span class="badge badge-info">{{ number_format($percentage, 1) }}%</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No payment method data available</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-alert-circle me-2"></i>Failed Payments
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($failedPayments->count() > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Total Failed:</span>
                                    <span class="badge badge-danger">{{ $failedPayments->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Failed Amount:</span>
                                    <span class="text-danger fw-bold">${{ number_format($failedPayments->sum('amount'), 2) }}</span>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th class="text-end">Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($failedPayments->take(5) as $payment)
                                        <tr>
                                            <td>{{ $payment->invoice->invoice_number }}</td>
                                            <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->created_at->format('M d') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center">
                                <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                                <p class="text-success mb-0">No Failed Payments</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Payment Trend & Top Paying Agencies -->
        <div class="row mb-4 admin-invoice-stats">
            <div class="col-md-8">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-chart-timeline me-2"></i>Daily Payment Trend (Last 30 Days)
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
                                                <th class="text-center">Payments</th>
                                                <th class="text-end">Amount</th>
                                                <th class="text-end">Avg Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dailyPayments->take(15) as $day)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                                <td class="text-center">{{ number_format($day->count) }}</td>
                                                <td class="text-end">${{ number_format($day->amount, 2) }}</td>
                                                <td class="text-end">${{ number_format($day->amount / $day->count, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No payment trend data available</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-trophy me-2"></i>Top Paying Agencies
                        </h6>
                        <div class="chart-toggle-group" id="agency-payments-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="agency-payments-chart" data-container="agency-payments-chart-container">
                                <i class="mdi mdi-chart-bar"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="agency-payments-table" data-container="agency-payments-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($agencyPayments->count() > 0)
                            <!-- Chart View -->
                            <div id="agency-payments-chart-container">
                                <canvas id="agencyPaymentsChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="agency-payments-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Agency</th>
                                                <th class="text-center">Payments</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($agencyPayments->take(8) as $agency)
                                            <tr>
                                                <td>{{ $agency->invoice->agency->agency_name ?? 'N/A' }}</td>
                                                <td class="text-center">{{ number_format($agency->count) }}</td>
                                                <td class="text-end">${{ number_format($agency->amount, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No agency payment data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Payment List -->
        <div class="row admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-format-list-bulleted me-2"></i>Payment Transaction Details ({{ $payments->count() }} payments)
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Payment Date</th>
                                            <th>Invoice #</th>
                                            <th>Agency</th>
                                            <th class="text-end">Amount</th>
                                            <th>Payment Method</th>
                                            <th>Transaction ID</th>
                                            <th>Status</th>
                                            <th class="text-end">Fee</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments->take(100) as $payment)
                                        <tr>
                                            <td>
                                                {{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : $payment->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="text-decoration-none fw-bold">
                                                    {{ $payment->invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>{{ $payment->invoice->agency->agency_name ?? 'N/A' }}</td>
                                            <td class="text-end fw-bold">${{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $payment->payment_method === 'stripe' ? 'info' : ($payment->payment_method === 'manual' ? 'danger' : '') }}">
                                                    <i class="mdi mdi-{{ $payment->payment_method === 'stripe' ? 'credit-card' : ($payment->payment_method === 'paypal' ? 'paypal' : 'cash') }} me-1"></i>
                                                    {{ ucfirst($payment->payment_method) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($payment->transaction_id)
                                                    <code class="small">{{ substr($payment->transaction_id, 0, 15) }}...</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if($payment->processing_fee)
                                                    ${{ number_format($payment->processing_fee, 2) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="btn btn-primary btn-sm invoice-action-btn" title="View Invoice">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    @if($payment->status === 'completed')
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
                            @if($payments->count() > 100)
                                <div class="mt-3">
                                    <p class="text-muted small">
                                        <i class="mdi mdi-information me-1"></i>
                                        Showing first 100 payments. Export to view all data.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="mdi mdi-credit-card display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No payments found</h5>
                                <p class="text-muted">Try adjusting your filters to see more data.</p>
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
    @if($paymentMethodStats->count() > 0)
    // Payment Method Statistics Pie Chart
    const paymentMethodStatsCtx = document.getElementById('paymentMethodStatsChart');
    if (paymentMethodStatsCtx) {
        const paymentMethodData = @json($paymentMethodStats->mapWithKeys(function ($item) {
            return [ucfirst($item->payment_method) => $item->amount];
        }));
        const paymentMethodLabels = Object.keys(paymentMethodData);
        const paymentMethodAmounts = Object.values(paymentMethodData);
        const paymentMethodColors = paymentMethodLabels.map(method => {
            switch(method.toLowerCase()) {
                case 'stripe': return '#635bff';
                case 'paypal': return '#0070ba';
                case 'cash': return '#28a745';
                case 'check': return '#fd7e14';
                case 'manual': return '#dc3545';
                default: return '#36a9f3';
            }
        });

        new Chart(paymentMethodStatsCtx, {
            type: 'doughnut',
            data: {
                labels: paymentMethodLabels,
                datasets: [{
                    data: paymentMethodAmounts,
                    backgroundColor: paymentMethodColors,
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
    // Daily Payment Trend Line Chart
    const dailyPaymentsCtx = document.getElementById('dailyPaymentsChart');
    if (dailyPaymentsCtx) {
        const dailyPaymentData = @json($dailyPayments->take(15)->reverse()->values());
        const dailyPaymentLabels = dailyPaymentData.map(day => {
            const date = new Date(day.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const dailyPaymentAmounts = dailyPaymentData.map(day => day.amount);
        const dailyPaymentCounts = dailyPaymentData.map(day => day.count);

        new Chart(dailyPaymentsCtx, {
            type: 'line',
            data: {
                labels: dailyPaymentLabels,
                datasets: [{
                    label: 'Payment Amount',
                    data: dailyPaymentAmounts,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                }, {
                    label: 'Payment Count',
                    data: dailyPaymentCounts,
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
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

    @if($agencyPayments->count() > 0)
    // Top Paying Agencies Horizontal Bar Chart
    const agencyPaymentsCtx = document.getElementById('agencyPaymentsChart');
    if (agencyPaymentsCtx) {
        const agencyPaymentData = @json($agencyPayments->take(5));
        const agencyPaymentLabels = agencyPaymentData.map(agency => agency.invoice?.agency?.agency_name || 'N/A');
        const agencyPaymentAmounts = agencyPaymentData.map(agency => agency.amount);

        new Chart(agencyPaymentsCtx, {
            type: 'bar',
            data: {
                labels: agencyPaymentLabels,
                datasets: [{
                    label: 'Payment Amount',
                    data: agencyPaymentAmounts,
                    backgroundColor: 'rgba(23, 162, 184, 0.8)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
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
                                return 'Total Payments: $' + context.parsed.x.toLocaleString();
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