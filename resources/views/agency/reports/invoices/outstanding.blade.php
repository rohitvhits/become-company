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
                <a href="{{ route('agency.reports.invoices.outstanding', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-success invoice-action-btn btn-sm btn-icon-text">
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
                        <form method="GET" action="{{ route('agency.reports.invoices.outstanding') }}" class="row align-items-end">
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <label for="date_from" class="form-label small mb-1">Created From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm invoice-filter-control" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <label for="date_to" class="form-label small mb-1">Created To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm invoice-filter-control" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-primary invoice-action-btn btn-sm me-2">
                                    <i class="mdi mdi-magnify"></i> Apply Filters
                                </button>
                                @if(collect($filters)->filter()->isNotEmpty())
                                    <a href="{{ route('agency.reports.invoices.outstanding') }}" class="btn btn-secondary invoice-action-btn btn-sm">
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
                        <h4 class="mb-1 text-warning font-weight-bold">{{ $stats['total_outstanding_invoices'] }}</h4>
                        <p class="mb-0 text-muted small">Outstanding Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-warning font-weight-bold">${{ number_format($stats['total_outstanding_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Outstanding Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-danger font-weight-bold">{{ $stats['overdue_invoices'] }}</h4>
                        <p class="mb-0 text-muted small">Overdue Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1 text-danger font-weight-bold">${{ number_format($stats['overdue_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted small">Overdue Amount</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aging Analysis -->
        {{-- <div class="row mb-4 admin-invoice-stats">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-calendar-clock me-2"></i>Aging Analysis
                        </h6>
                        <div class="chart-toggle-group" id="aging-toggle-group">
                            <button type="button" class="chart-toggle-btn active" data-chart="aging-chart" data-container="aging-chart-container">
                                <i class="mdi mdi-chart-bar"></i>
                            </button>
                            <button type="button" class="chart-toggle-btn" data-table="aging-table" data-container="aging-table-container">
                                <i class="mdi mdi-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart View -->
                        <div id="aging-chart-container">
                            <canvas id="agingChart" width="400" height="200"></canvas>
                        </div>
                        <!-- Table View -->
                        <div id="aging-table-container" style="display: none;">
                            <div class="row">
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="aging-card current">
                                        <div class="text-center py-3">
                                            <h5 class="mb-1 font-weight-bold">${{ number_format($agingAnalysis['current'], 0) }}</h5>
                                            <p class="mb-0 small">Current</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="aging-card days-1-30">
                                        <div class="text-center py-3">
                                            <h5 class="mb-1 font-weight-bold">${{ number_format($agingAnalysis['1_30_days'], 0) }}</h5>
                                            <p class="mb-0 small">1-30 Days</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="aging-card days-31-60">
                                        <div class="text-center py-3">
                                            <h5 class="mb-1 font-weight-bold">${{ number_format($agingAnalysis['31_60_days'], 0) }}</h5>
                                            <p class="mb-0 small">31-60 Days</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="aging-card days-61-90">
                                        <div class="text-center py-3">
                                            <h5 class="mb-1 font-weight-bold">${{ number_format($agingAnalysis['61_90_days'], 0) }}</h5>
                                            <p class="mb-0 small">61-90 Days</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="aging-card over-90">
                                        <div class="text-center py-3">
                                            <h5 class="mb-1 font-weight-bold">${{ number_format($agingAnalysis['over_90_days'], 0) }}</h5>
                                            <p class="mb-0 small">Over 90 Days</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Outstanding Invoices List -->
        <div class="row admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="mdi mdi-format-list-bulleted me-2"></i>
                            Outstanding Invoices Details ({{ $invoices->count() }} invoices)
                        </h6>
                        @if($invoices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Amount</th>
                                            <th>Created Date</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Days Overdue</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $invoice)
                                            @php
                                                $daysOverdue = \Carbon\Carbon::parse($invoice->due_date)->diffInDays(now(), false);
                                                $agingCategory = '';
                                                if ($daysOverdue <= 0) $agingCategory = 'Current';
                                                elseif ($daysOverdue <= 30) $agingCategory = '1-30 Days';
                                                elseif ($daysOverdue <= 60) $agingCategory = '31-60 Days';
                                                elseif ($daysOverdue <= 90) $agingCategory = '61-90 Days';
                                                else $agingCategory = 'Over 90 Days';
                                            @endphp
                                            <tr class="{{ $daysOverdue > 30 ? 'table-warning' : '' }}">
                                                <td>
                                                    <a href="{{ route('agency.invoices.show', $invoice) }}" class="text-decoration-none fw-bold">
                                                        {{ $invoice->invoice_number }}
                                                    </a>
                                                </td>
                                                <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                                <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="{{ $daysOverdue > 0 ? 'text-danger fw-bold' : '' }}">
                                                        {{ $invoice->due_date->format('M d, Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($invoice->status === 'overdue')
                                                        <span class="badge bg-danger">Overdue</span>
                                                    @elseif($invoice->status === 'sent')
                                                        <span class="badge bg-primary">Sent</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($daysOverdue > 0)
                                                        <span class="text-danger fw-bold">{{ $daysOverdue }} days</span>
                                                    @else
                                                        <span class="text-success">Due in {{ abs($daysOverdue) }} days</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('agency.invoices.show', $invoice) }}"
                                                           class="btn btn-primary invoice-action-btn btn-sm" title="View Invoice">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('agency.invoices.download', $invoice) }}"
                                                           class="btn btn-secondary invoice-action-btn btn-sm" title="Download Invoice">
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
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-check-circle display-1 text-success mb-3"></i>
                                <h5 class="text-muted">No outstanding invoices!</h5>
                                <p class="text-muted">All your invoices are paid. Great job!</p>
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

.aging-card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.aging-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.aging-card.current {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.aging-card.days-1-30 {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
    color: #856404;
}

.aging-card.days-31-60 {
    background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
    color: #8c5700;
}

.aging-card.days-61-90 {
    background: linear-gradient(135deg, #fab1a0 0%, #e17055 100%);
    color: #721c24;
}

.aging-card.over-90 {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
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

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

@media (max-width: 768px) {
    .stats-card h4 {
        font-size: 1.25rem;
    }

    .aging-card h5 {
        font-size: 1.1rem;
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
    // Aging Analysis Bar Chart
    const agingCtx = document.getElementById('agingChart');
    if (agingCtx) {
        const agingData = @json($agingAnalysis);
        const agingLabels = ['Current', '1-30 Days', '31-60 Days', '61-90 Days', 'Over 90 Days'];
        const agingAmounts = [
            agingData.current,
            agingData['1_30_days'],
            agingData['31_60_days'],
            agingData['61_90_days'],
            agingData.over_90_days
        ];
        const agingColors = [
            '#28a745', // Green for current
            '#ffc107', // Yellow for 1-30
            '#fd7e14', // Orange for 31-60
            '#dc3545', // Red for 61-90
            '#721c24'  // Dark red for over 90
        ];

        new Chart(agingCtx, {
            type: 'bar',
            data: {
                labels: agingLabels,
                datasets: [{
                    label: 'Outstanding Amount',
                    data: agingAmounts,
                    backgroundColor: agingColors,
                    borderColor: agingColors,
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