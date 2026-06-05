@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold invoice-page-title">{{ $title }}</h5>
            <div class="page-rightbtns">
                <a href="{{ route('agency.reports.invoices.index') }}" class="invoice-badge-btn btn-secondary btn-sm invoice-action-btn me-2">
                    <i class="mdi mdi-arrow-left"></i>
                    Back to Reports
                </a>
                <a href="{{ route('agency.reports.invoices.summary', array_merge(request()->query(), ['export' => 'csv'])) }}" class="invoice-badge-btn btn-success btn-sm invoice-action-btn">
                    <i class="mdi mdi-download"></i>
                    Export CSV
                </a>
            </div>
        </div>

        @if(isset($error))
            <div class="alert alert-danger">
                <i class="mdi mdi-alert-circle"></i>
                {{ $error }}
            </div>
        @endif

        <!-- Filter Form -->
        <div class="row mb-3 admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('agency.reports.invoices.summary') }}" class="row align-items-end">
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <label for="status" class="form-label small mb-1">Status</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="sent" {{ $filters['status'] === 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="paid" {{ $filters['status'] === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ $filters['status'] === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <label for="date_from" class="form-label small mb-1">From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <label for="date_to" class="form-label small mb-1">To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
                                <button type="submit" class="invoice-badge-btn btn-primary btn-sm invoice-action-btn me-2">
                                    <i class="mdi mdi-magnify"></i> Apply Filters
                                </button>
                                @if(collect($filters)->filter()->isNotEmpty())
                                    <a href="{{ route('agency.reports.invoices.summary') }}" class="invoice-badge-btn btn-outline-secondary btn-sm invoice-action-btn">
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
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-info font-weight-bold">{{ $stats['total_invoices'] }}</h4>
                        <p class="mb-0 text-muted small">Total Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-info font-weight-bold">${{ number_format($stats['total_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Total Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-success font-weight-bold">${{ number_format($stats['total_paid'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Total Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-warning font-weight-bold">${{ number_format($stats['total_outstanding'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Outstanding</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-danger font-weight-bold">{{ $stats['overdue_count'] }}</h4>
                        <p class="mb-0 text-muted small">Overdue Count</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-danger font-weight-bold">${{ number_format($stats['overdue_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Overdue Amount</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4 admin-invoice-stats">
            <!-- Status Breakdown -->
            <div class="col-lg-6 mb-4">
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
                                @foreach($statusBreakdown as $status => $data)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge bg-{{ $status === 'paid' ? 'success' : ($status === 'overdue' ? 'danger' : 'primary') }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <div class="font-weight-bold">{{ $data->count }} invoices</div>
                                            <div class="small text-muted">${{ number_format($data->amount, 2) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="col-lg-6 mb-4">
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
                                <canvas id="monthlyChart" width="400" height="300"></canvas>
                            </div>
                            <!-- Table View -->
                            <div id="monthly-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Count</th>
                                                <th>Amount</th>
                                                <th>Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyBreakdown as $month)
                                                <tr>
                                                    <td>{{ date('M Y', strtotime($month->month . '-01')) }}</td>
                                                    <td>{{ $month->count }}</td>
                                                    <td>${{ number_format($month->amount, 0) }}</td>
                                                    <td>${{ number_format($month->paid_amount, 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Invoice List -->
        <div class="row admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="mdi mdi-format-list-bulleted me-2"></i>
                            Detailed Invoice List ({{ $invoices->count() }} invoices)
                        </h6>
                        @if($invoices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Due Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Days Outstanding</th>
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
                                                    <span class="{{ $invoice->is_overdue ? 'text-danger' : '' }}">
                                                        {{ $invoice->due_date->format('M d, Y') }}
                                                    </span>
                                                </td>
                                                <td>${{ number_format($invoice->total_amount, 2) }}</td>
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
                                                    @php
                                                        $daysOutstanding = $invoice->status === 'paid' ? 0 : \Carbon\Carbon::parse($invoice->due_date)->diffInDays(now(), false);
                                                    @endphp
                                                    <span class="{{ $daysOutstanding > 0 ? 'text-danger' : 'text-muted' }}">
                                                        {{ $daysOutstanding }} days
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('agency.invoices.show', $invoice) }}"
                                                           class="invoice-badge-btn btn-primary btn-sm invoice-action-btn" title="View">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('agency.invoices.download', $invoice) }}"
                                                           class="invoice-badge-btn btn-secondary btn-sm invoice-action-btn" title="Download">
                                                            <i class="mdi mdi-download"></i>
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
                                <h5 class="text-muted">No invoices found</h5>
                                <p class="text-muted">No invoices match your filter criteria.</p>
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

.table th {
    font-weight: 600;
    color: #495057;
    border-top: none;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.5rem;
}

@media (max-width: 768px) {
    .stats-card h4 {
        font-size: 1.25rem;
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