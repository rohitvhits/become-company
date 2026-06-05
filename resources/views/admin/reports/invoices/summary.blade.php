@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper invoice-module-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold invoice-page-title">{{ $title }}</h5>
            
            <div class="page-rightbtns">
                <a href="{{ route('admin.reports.invoices.index') }}" class="btn btn-secondary btn-sm invoice-action-btn btn-icon-text">
                    <i class="mdi mdi-arrow-left"></i>
                    Back to Reports
                </a>
                
                <a href="{{ route('admin.reports.invoices.summary', ['export' => 'csv']) }}" class="btn btn-success btn-sm invoice-action-btn btn-icon-text"><i class="mdi mdi-download"></i>Export CSV</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card admin-invoice-stats">
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-filter-variant"></i>
                            Filter Options
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.reports.invoices.summary') }}" class="row">
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
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control invoice-filter-control" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control invoice-filter-control" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control invoice-filter-control">
                                    <option value="">All Status</option>
                                    <option value="draft" {{ $filters['status'] === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="sent" {{ $filters['status'] === 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="paid" {{ $filters['status'] === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ $filters['status'] === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm invoice-action-btn">
                                        <i class="mdi mdi-magnify"></i>
                                        Apply Filters
                                    </button>
                                    <a href="{{ route('admin.reports.invoices.summary') }}" class="btn btn-secondary btn-sm invoice-action-btn">
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

        @if(isset($error))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    {{ $error }}
                </div>
            </div>
        </div>
        @endif

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-file-document text-primary mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">{{ number_format($stats['total_invoices']) }}</h4>
                        <p class="mb-0 text-muted small">Total Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-currency-usd text-success mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['total_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Total Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['total_paid'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Total Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-alert-circle text-warning mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['total_outstanding'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Outstanding</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-timer-off text-danger mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">{{ number_format($stats['overdue_count']) }}</h4>
                        <p class="mb-0 text-muted small">Overdue Count</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3 admin-invoice-stats">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-clock-alert text-danger mb-2" style="font-size: 2rem;"></i>
                        <h4 class="mb-1 font-weight-bold">${{ number_format($stats['overdue_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Overdue Amount</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4 admin-invoice-stats">
            <!-- Status Breakdown Chart -->
            <div class="col-md-6 mb-4">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-chart-pie me-2"></i>Status Breakdown
                        </h6>
                        <div class="chart-toggle-group" id="status-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="status-chart" data-container="status-chart-container">
                                <i class="mdi mdi-chart-pie"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="status-table" data-container="status-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($statusBreakdown->count() > 0)
                            <!-- Chart View -->
                            <div id="status-chart-container">
                                <canvas id="statusChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="status-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th class="text-center">Count</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($statusBreakdown as $status => $data)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-{{ $status === 'paid' ? 'success' : ($status === 'overdue' ? 'danger' : 'primary') }}">
                                                        {{ ucfirst($status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ number_format($data->count) }}</td>
                                                <td class="text-end">${{ number_format($data->amount, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Agencies Chart -->
            <div class="col-md-6 mb-4">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-chart-bar me-2"></i>Top Agencies by Amount
                        </h6>
                        <div class="chart-toggle-group" id="agency-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="agency-chart" data-container="agency-chart-container">
                                <i class="mdi mdi-chart-bar"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="agency-table" data-container="agency-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($agencyBreakdown->count() > 0)
                            <!-- Chart View -->
                            <div id="agency-chart-container">
                                <canvas id="agencyChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="agency-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Agency</th>
                                                <th class="text-center">Count</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($agencyBreakdown->take(10) as $agency)
                                            <tr>
                                                <td>{{ $agency->agency->agency_name ?? 'N/A' }}</td>
                                                <td class="text-center">{{ number_format($agency->count) }}</td>
                                                <td class="text-end">${{ number_format($agency->amount, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="row mb-4 admin-invoice-stats">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-chart-line me-2"></i>Monthly Trend (Last 12 Months)
                        </h6>
                        <div class="chart-toggle-group" id="monthly-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="monthly-chart" data-container="monthly-chart-container">
                                <i class="mdi mdi-chart-line"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="monthly-table" data-container="monthly-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($monthlyBreakdown->count() > 0)
                            <!-- Chart View -->
                            <div id="monthly-chart-container">
                                <canvas id="monthlyChart" height="80"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="monthly-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th class="text-center">Invoices</th>
                                                <th class="text-end">Total Amount</th>
                                                <th class="text-end">Paid Amount</th>
                                                <th class="text-end">Collection Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyBreakdown as $month)
                                            @php
                                            $collectionRate = $month->amount > 0 ? ($month->paid_amount / $month->amount) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('M Y') }}</td>
                                                <td class="text-center">{{ number_format($month->count) }}</td>
                                                <td class="text-end">${{ number_format($month->amount, 2) }}</td>
                                                <td class="text-end">${{ number_format($month->paid_amount, 2) }}</td>
                                                <td class="text-end">
                                                    <span class="badge badge-{{ $collectionRate >= 80 ? 'success' : ($collectionRate >= 60 ? 'warning' : 'danger') }}">
                                                        {{ number_format($collectionRate, 1) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Invoice List -->
        <div class="row admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-format-list-bulleted me-2"></i>Detailed Invoice List ({{ $invoices->count() }} invoices)
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
                                        <th>Created</th>
                                        <th>Due Date</th>
                                        <th class="text-end">Amount</th>
                                        <th>Status</th>
                                        <th>Days Outstanding</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices->take(100) as $invoice)
                                    @php
                                    $daysOutstanding = $invoice->status === 'paid' ? 0 :
                                    \Carbon\Carbon::parse($invoice->due_date)->diffInDays(now(), false);
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-decoration-none fw-bold">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $invoice->agency->agency_name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="{{ $invoice->status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                                                {{ $invoice->due_date->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td class="text-end">${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'primary') }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($invoice->status !== 'paid')
                                            <span class="{{ $daysOutstanding > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ max(0, $daysOutstanding) }} days
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-primary btn-sm invoice-action-btn btn-sm">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
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
                                Showing first 100 invoices. Use filters or export to view all data.
                            </p>
                        </div>
                        @endif
                        @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-file-document display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">No invoices found</h5>
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

    .chart-container-wrapper {
        position: relative;
        min-height: 300px;
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

    .stats-card .card-body {
        padding: 1.5rem;
    }

    .stats-card h4 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stats-card p {
        font-size: 0.85rem;
        font-weight: 500;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .stats-card h4 {
            font-size: 1.4rem;
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
    @if($statusBreakdown->count() > 0)
    // Status Breakdown Pie Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const statusData = @json($statusBreakdown);
        const statusLabels = Object.keys(statusData).map(status => status.charAt(0).toUpperCase() + status.slice(1));
        const statusAmounts = Object.values(statusData).map(item => item.amount);
        const statusColors = Object.keys(statusData).map(status => {
            switch(status) {
                case 'paid': return '#28a745';
                case 'overdue': return '#dc3545';
                case 'sent': return '#36a9f3';
                case 'draft': return '#6c757d';
                default: return '#36a9f3';
            }
        });

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusAmounts,
                    backgroundColor: statusColors,
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
                                const total = context.dataset.data.reduce((a, b) => a + Number(b), 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value}`;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    @if($agencyBreakdown->count() > 0)
    // Top Agencies Bar Chart
    const agencyCtx = document.getElementById('agencyChart');
    if (agencyCtx) {
        const agencyData = @json($agencyBreakdown->take(5));
        const agencyLabels = agencyData.map(agency => agency.agency?.agency_name || 'N/A');
        const agencyAmounts = agencyData.map(agency => agency.amount);

        new Chart(agencyCtx, {
            type: 'bar',
            data: {
                labels: agencyLabels,
                datasets: [{
                    label: 'Invoice Amount',
                    data: agencyAmounts,
                    backgroundColor: 'rgba(54, 169, 243, 0.8)',
                    borderColor: 'rgba(54, 169, 243, 1)',
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
                                return 'Amount: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    @if($monthlyBreakdown->count() > 0)
    // Monthly Trend Line Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        const monthlyData = @json($monthlyBreakdown->reverse()->values());
        const monthlyLabels = monthlyData.map(month => {
            const date = new Date(month.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        const monthlyAmounts = monthlyData.map(month => month.amount);
        const monthlyPaidAmounts = monthlyData.map(month => month.paid_amount);

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Total Amount',
                    data: monthlyAmounts,
                    borderColor: '#36a9f3',
                    backgroundColor: 'rgba(54, 169, 243, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Paid Amount',
                    data: monthlyPaidAmounts,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
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
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
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