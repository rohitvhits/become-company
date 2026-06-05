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
                <a href="{{ route('admin.reports.invoices.outstanding', ['export' => 'csv']) }}" class="btn btn-success btn-sm invoice-action-btn btn-icon-text"><i class="mdi mdi-download"></i>Export CSV</a>
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
                        <form method="GET" action="{{ route('admin.reports.invoices.outstanding') }}" class="row">
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
                                <label for="date_from" class="form-label">Created From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control invoice-filter-control" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="date_to" class="form-label">Created To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control invoice-filter-control" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm invoice-action-btn">
                                        <i class="mdi mdi-magnify me-2"></i>Apply
                                    </button>
                                    <a href="{{ route('admin.reports.invoices.outstanding') }}" class="btn btn-secondary btn-sm invoice-action-btn">
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
                        <i class="mdi mdi-alert-circle text-warning mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-warning">{{ number_format($stats['total_outstanding_invoices']) }}</h4>
                        <p class="mb-0 text-muted">Outstanding Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-currency-usd text-warning mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-warning">${{ number_format($stats['total_outstanding_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted">Outstanding Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-timer-off text-danger mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-danger">{{ number_format($stats['overdue_invoices']) }}</h4>
                        <p class="mb-0 text-muted">Overdue Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-clock-alert text-danger mb-2" style="font-size: 2.5rem;"></i>
                        <h4 class="mb-1 font-weight-bold text-danger">${{ number_format($stats['overdue_amount'], 0) }}</h4>
                        <p class="mb-0 text-muted">Overdue Amount</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outstanding Invoices List -->
        <div class="row admin-invoice-stats">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-format-list-bulleted me-2"></i>Outstanding Invoices Details ({{ $invoices->count() }} invoices)
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
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th class="text-center">Days Overdue</th>
                                            <!-- <th>Aging Category</th> -->
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices->take(100) as $invoice)
                                        @php
                                            $daysOverdue = \Carbon\Carbon::parse($invoice->due_date)->diffInDays(now(), false);
                                            $agingCategory = 'Current';
                                            $badgeClass = 'success';

                                            if ($daysOverdue > 0) {
                                                if ($daysOverdue <= 30) {
                                                    $agingCategory = '1-30 Days';
                                                    $badgeClass = 'warning';
                                                } elseif ($daysOverdue <= 60) {
                                                    $agingCategory = '31-60 Days';
                                                    $badgeClass = 'warning';
                                                } elseif ($daysOverdue <= 90) {
                                                    $agingCategory = '61-90 Days';
                                                    $badgeClass = 'danger';
                                                } else {
                                                    $agingCategory = 'Over 90 Days';
                                                    $badgeClass = 'danger';
                                                }
                                            }
                                        @endphp
                                        <tr class="{{ $daysOverdue > 30 ? 'table-warning' : '' }}">
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-decoration-none fw-bold">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->agency->agency_name ?? 'N/A' }}</td>
                                            <td class="text-end fw-bold">${{ number_format($invoice->total_amount, 2) }}</td>
                                            <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="{{ $daysOverdue > 0 ? 'text-danger fw-bold' : '' }}">
                                                    {{ $invoice->due_date->format('M d, Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $invoice->status === 'overdue' ? 'danger' : 'primary' }}">
                                                    {{ ucfirst($invoice->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($daysOverdue > 0)
                                                    <span class="badge badge-danger">{{ $daysOverdue }} days</span>
                                                @else
                                                    <span class="badge badge-success">{{ abs($daysOverdue) }} days left</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-primary btn-sm" title="View Invoice">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    {{-- <a href="mailto:{{ $invoice->agency->email ?? '' }}?subject=Outstanding Invoice {{ $invoice->invoice_number }}" class="btn btn-warning btn-sm" title="Send Reminder">
                                                        <i class="mdi mdi-email"></i>
                                                    </a> --}}
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
                                <i class="mdi mdi-check-circle display-1 text-success"></i>
                                <h5 class="text-success mt-3">No outstanding invoices!</h5>
                                <p class="text-muted">All invoices are paid or there are no invoices matching your criteria.</p>
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

    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 3px;
    }

    .progress-bar {
        border-radius: 3px;
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
    @if($agencyOutstanding->count() > 0)
    // Top Outstanding Agencies Horizontal Bar Chart
    const agencyOutstandingCtx = document.getElementById('agencyOutstandingChart');
    if (agencyOutstandingCtx) {
        const agencyData = @json($agencyOutstanding->take(5));
        const agencyLabels = agencyData.map(agency => agency.agency?.agency_name || 'N/A');
        const agencyAmounts = agencyData.map(agency => agency.amount);

        new Chart(agencyOutstandingCtx, {
            type: 'bar',
            data: {
                labels: agencyLabels,
                datasets: [{
                    label: 'Outstanding Amount',
                    data: agencyAmounts,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
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
                                return 'Outstanding: $' + context.parsed.x.toLocaleString();
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