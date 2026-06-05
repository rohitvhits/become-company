@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/morris.js/morris.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/modulejs/hubAnalytics/css/hub-analytics.css') }}" type="text/css" />
<style>
.analytics-card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.analytics-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.analytics-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.chart-container {
    position: relative;
    height: 300px;
    margin: 20px 0;
}

.data-quality-progress {
    height: 8px;
    border-radius: 4px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 15px 20px;
}

.card-header h6 {
    margin: 0;
    font-weight: 600;
    color: #495057;
}

.refresh-btn {
    position: relative;
}

.refresh-btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: button-loading-spinner 1s ease infinite;
}

@keyframes button-loading-spinner {
    from {
        transform: rotate(0turn);
    }
    to {
        transform: rotate(1turn);
    }
}

/* DataTables Pagination Styling */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.375rem 0.75rem;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    color: #495057;
    background: white;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #007bff;
    border-color: #007bff;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
    opacity: 0.65;
}

.dataTables_wrapper .dataTables_info {
    font-size: 0.875rem;
    color: #6c757d;
    padding-top: 0.5rem;
}

.dataTables_wrapper .dataTables_length {
    display: flex;
    align-items: center;
    white-space: nowrap;
}

.dataTables_wrapper .dataTables_length label {
    display: flex;
    align-items: center;
    margin-bottom: 0;
    white-space: nowrap;
}

.dataTables_wrapper .dataTables_length select {
    padding: 0.25rem 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    background-color: white;
    font-size: 0.875rem;
    margin: 0 0.5rem;
    width: auto;
    min-width: 70px;
}

.dataTables_wrapper .dataTables_filter input {
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    margin-left: 0.5rem;
}
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0 font-weight-bold">Hub Analytics Dashboard</h5>
                </div>
                <div class="col-md-8">
                    <div class="d-flex justify-content-end" style="margin: 15px;align-items: center;">
                        <select class="form-control mr-2" id="dateRange" style="width: 150px;">
                            <option value="7">Last 7 Days</option>
                            <option value="30" selected>Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                            <option value="365">Last Year</option>
                        </select>
                        <select class="form-control select2-multiple mr-2" multiple id="agencyFilter" style="width: 200px;">
                            @foreach($agencyList as $agency)
                                <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                            @endforeach
                        </select>
                        <select class="form-control mr-2" id="statusFilter" style="width: 120px; display: none;">
                            <option value="">All Status</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Deactivated Only</option>
                        </select>
                        <button class="btn btn-primary btn-sm btn-fw cust-right-btn" id="refreshData">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                        <button class="btn btn-sm btn-fw cust-right-btn btn-secondary ml-2" id="exportData">
                            <i class="mdi mdi-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-1" id="totalRecords">{{ number_format($recordStats['total_records']) }}</h4>
                                <p class="card-description mb-0">Total Records</p>
                                <small class="text-muted">
                                    Active: <span id="activeRecords">{{ number_format($recordStats['active_records']) }}</span> |
                                    Deactivated: <span id="inactiveRecords">{{ number_format($recordStats['inactive_records']) }}</span>
                                </small>
                            </div>
                            <div class="analytics-icon bg-primary">
                                <i class="mdi mdi-account-multiple"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-1" id="totalAgencies">{{ count($agencyStats) }}</h4>
                                <p class="card-description mb-0">Active Agencies</p>
                                <small class="text-muted">With Hub Records</small>
                            </div>
                            <div class="analytics-icon bg-success">
                                <i class="mdi mdi-office-building"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-1" id="totalImports">{{ $importStats['total_imports'] }}</h4>
                                <p class="card-description mb-0">Imports Processed</p>
                                <small class="text-muted">
                                    Success: <span id="successfulImports">{{ $importStats['successful_imports'] }}</span> |
                                    Failed: <span id="failedImports">{{ $importStats['failed_imports'] }}</span>
                                </small>
                            </div>
                            <div class="analytics-icon bg-warning">
                                <i class="mdi mdi-upload"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Record Growth Trend</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="recordGrowthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Deactivation Trend</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="deactivationTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Top Agencies by Records</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="agencyComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Status Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="statusDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agency Records Breakdown Table -->
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Agency Records Breakdown</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="agencyRecordsTable">
                                <thead>
                                    <tr>
                                        <th>Agency Name</th>
                                        <th>Total Records</th>
                                        <th>Active</th>
                                        <th>Deactivated</th>
                                        <th>Active %</th>
                                        <th>Deactivated %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agencyStats as $agency)
                                    <tr>
                                        <td>{{ $agency->agency_name }}</td>
                                        <td><strong>{{ number_format($agency->hub_records_count) }}</strong></td>
                                        <td><span class="text-success"><strong>{{ number_format($agency->active_count ?? 0) }}</strong></span></td>
                                        <td><span class="text-danger"><strong>{{ number_format($agency->inactive_count ?? 0) }}</strong></span></td>
                                        <td>
                                            @php
                                                $activePercent = $agency->hub_records_count > 0 ? ($agency->active_count / $agency->hub_records_count) * 100 : 0;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2">{{ number_format($activePercent, 1) }}%</span>
                                                <div class="progress flex-fill" style="height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $activePercent }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $inactivePercent = $agency->hub_records_count > 0 ? ($agency->inactive_count / $agency->hub_records_count) * 100 : 0;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2">{{ number_format($inactivePercent, 1) }}%</span>
                                                <div class="progress flex-fill" style="height: 8px;">
                                                    <div class="progress-bar bg-danger" style="width: {{ $inactivePercent }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Data Quality Metrics</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>Missing Email</td>
                                        <td>{{ number_format($dataQualityStats['missing_email']) }}</td>
                                        <td>
                                            @php $emailPercent = $dataQualityStats['total_records'] > 0 ? ($dataQualityStats['missing_email'] / $dataQualityStats['total_records']) * 100 : 0; @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-danger" style="width: {{ $emailPercent }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Missing Mobile</td>
                                        <td>{{ number_format($dataQualityStats['missing_phone']) }}</td>
                                        <td>
                                            @php $phonePercent = $dataQualityStats['total_records'] > 0 ? ($dataQualityStats['missing_phone'] / $dataQualityStats['total_records']) * 100 : 0; @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-warning" style="width: {{ $phonePercent }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Missing SSN</td>
                                        <td>{{ number_format($dataQualityStats['missing_ssn']) }}</td>
                                        <td>
                                            @php $ssnPercent = $dataQualityStats['total_records'] > 0 ? ($dataQualityStats['missing_ssn'] / $dataQualityStats['total_records']) * 100 : 0; @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-info" style="width: {{ $ssnPercent }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Invalid Emails</td>
                                        <td>{{ number_format($dataQualityStats['invalid_emails']) }}</td>
                                        <td>
                                            @php $invalidPercent = $dataQualityStats['total_records'] > 0 ? ($dataQualityStats['invalid_emails'] / $dataQualityStats['total_records']) * 100 : 0; @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-danger" style="width: {{ $invalidPercent }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Potential Duplicates</td>
                                        <td>{{ number_format($dataQualityStats['duplicate_potential']) }}</td>
                                        <td>
                                            @php $dupPercent = $dataQualityStats['total_records'] > 0 ? ($dataQualityStats['duplicate_potential'] / $dataQualityStats['total_records']) * 100 : 0; @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-secondary" style="width: {{ $dupPercent }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <h6>Recent Activity</h6>
                            @foreach($recentActivity as $activity)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <small class="text-muted">{{ $activity['created_date'] }}</small>
                                    <div>{{ $activity['name'] }}</div>
                                    <small class="text-muted">{{ $activity['agency'] }}</small>
                                </div>
                                <span class="badge badge-{{ $activity['status'] == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($activity['status']) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('include/footer')

<!-- Analytics JavaScript Libraries -->
<script src="{{ asset('assets/vendors/morris.js/raphael.min.js') }}"></script>
<script src="{{ asset('assets/vendors/morris.js/morris.min.js') }}"></script>
<script src="{{ asset('assets/vendors/chartist/chartist.min.js') }}"></script>
<script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/modulejs/hubAnalytics/js/hub-analytics.js') }}"></script>

<script>
$(document).ready(function() {
    // Initialize components
    HubAnalytics.init({
        recordStats: @json($recordStats),
        agencyStats: @json($agencyStats),
        apiStats: @json($apiStats),
        importStats: @json($importStats),
        charts: {
            recordGrowth: @json($recordGrowthChart),
            deactivationTrend: @json($deactivationTrendChart),
            agencyComparison: @json($agencyComparisonChart),
            importSuccess: @json($importSuccessChart),
            statusDistribution: @json($statusDistributionChart),
            genderDistribution: @json($genderDistributionChart)
        }
    });
});

var HubAnalytics = {
    data: {},
    charts: {},

    init: function(data) {
        this.data = data;
        this.initializeFilters();
        this.initializeCharts();
        this.initializeTables();
        this.bindEvents();
    },

    initializeFilters: function() {
        $('.select2-multiple').select2({
            placeholder: 'Select Agencies',
            allowClear: true
        });
    },

    initializeCharts: function() {
        this.createRecordGrowthChart();
        this.createDeactivationTrendChart();
        this.createAgencyComparisonChart();
        this.createStatusDistributionChart();
        this.createGenderDistributionChart();
    },

    createRecordGrowthChart: function() {
        var ctx = document.getElementById('recordGrowthChart').getContext('2d');
        var chartData = this.data.charts.recordGrowth;

        this.charts.recordGrowth = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Records Created',
                    data: chartData.data || [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    },

    createDeactivationTrendChart: function() {
        var ctx = document.getElementById('deactivationTrendChart').getContext('2d');
        var chartData = this.data.charts.deactivationTrend;

        this.charts.deactivationTrend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Records Deactivated',
                    data: chartData.data || [],
                    borderColor: 'rgb(220, 53, 69)',
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    },

    createAgencyComparisonChart: function() {
        var ctx = document.getElementById('agencyComparisonChart').getContext('2d');
        var chartData = this.data.charts.agencyComparison;

        this.charts.agencyComparison = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Total Records',
                    data: chartData.data || [],
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
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
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return 'Total Records: ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    },


    createStatusDistributionChart: function() {
        var ctx = document.getElementById('statusDistributionChart').getContext('2d');
        var chartData = this.data.charts.statusDistribution;

        this.charts.statusDistribution = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    data: chartData.data || [],
                    backgroundColor: ['#28a745', '#dc3545', '#6c757d'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    },

    createGenderDistributionChart: function() {
        var ctx = document.getElementById('genderDistributionChart');
        if (!ctx) return; // Chart element may not exist in current view

        ctx = ctx.getContext('2d');
        var chartData = this.data.charts.genderDistribution;

        this.charts.genderDistribution = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    data: chartData.data || [],
                    backgroundColor: ['#007bff', '#ffc107', '#6c757d'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    },

    initializeTables: function() {
        // Initialize Agency Records Breakdown table
        try {
            if ($.fn.DataTable.isDataTable('#agencyRecordsTable')) {
                $('#agencyRecordsTable').DataTable().destroy();
            }

            $('#agencyRecordsTable').DataTable({
                pageLength: 10,
                lengthChange: true,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                info: true,
                searching: true,
                ordering: true,
                paging: true,
                pagingType: "full_numbers",
                order: [[1, 'desc']], // Sort by Total Records descending
                columnDefs: [
                    {
                        targets: [1, 2, 3], // Total, Active, Deactivated columns
                        type: 'num',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'type') {
                                return data; // Return original HTML content for display
                            }
                            if (type === 'sort' || type === 'ordering') {
                                // Extract number from HTML for sorting
                                var num = data.toString().replace(/<[^>]*>/g, '').replace(/,/g, '');
                                return parseFloat(num) || 0;
                            }
                            if (type === 'search') {
                                // Extract text content for searching
                                return data.toString().replace(/<[^>]*>/g, '').replace(/,/g, '');
                            }
                            return data;
                        }
                    },
                    {
                        targets: [4, 5],
                        orderable: false,
                        render: function(data, type, row) {
                            if (type === 'search') {
                                // Extract percentage text for searching
                                return data.toString().replace(/<[^>]*>/g, '');
                            }
                            return data;
                        }
                    } // Percentage columns with progress bars
                ],
                language: {
                    search: "Search agencies:",
                    lengthMenu: "Show _MENU_ agencies per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ agencies",
                    infoEmpty: "Showing 0 to 0 of 0 agencies",
                    infoFiltered: "(filtered from _MAX_ total agencies)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: "No agencies found",
                    zeroRecords: "No matching agencies found"
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
            });

        } catch (error) {
            console.error('Error initializing DataTable:', error);
        }
    },

    bindEvents: function() {
        var self = this;

        $('#refreshData').on('click', function() {
            self.refreshData();
        });

        $('#exportData').on('click', function() {
            self.exportData();
        });

        $('#dateRange, #agencyFilter, #statusFilter').on('change', function() {
            self.refreshData();
        });
    },

    refreshData: function() {
        var $btn = $('#refreshData');
        $btn.addClass('loading').prop('disabled', true);

        var days = $('#dateRange').val();
        var agencies = $('#agencyFilter').val();
        var status = $('#statusFilter').val();

        $.ajax({
            url: '/hub-analytics/refresh',
            method: 'GET',
            data: {
                days: days,
                agency_ids: agencies,
                status_filter: status
            },
            success: function(response) {
                if (response.success) {
                    this.updateDashboard(response.data);
                    this.showNotification('Data refreshed successfully', 'success');
                } else {
                    this.showNotification('Error refreshing data', 'error');
                }
            }.bind(this),
            error: function() {
                this.showNotification('Error refreshing data', 'error');
            }.bind(this),
            complete: function() {
                $btn.removeClass('loading').prop('disabled', false);
            }
        });
    },

    updateDashboard: function(data) {
        // Update internal data object with new data
        this.data = data;

        // Update summary cards
        $('#totalRecords').text(this.formatNumber(data.records.total_records));
        $('#activeRecords').text(this.formatNumber(data.records.active_records));
        $('#inactiveRecords').text(this.formatNumber(data.records.inactive_records));
        $('#totalAgencies').text(this.formatNumber(data.agencies.length));
        $('#totalImports').text(this.formatNumber(data.imports.total_imports));
        $('#successfulImports').text(this.formatNumber(data.imports.successful_imports));
        $('#failedImports').text(this.formatNumber(data.imports.failed_imports));

        // Update charts
        if (data.charts) {
            this.updateCharts(data.charts);
        }

        // Update agency records table if it exists
        this.updateAgencyTable(data);

        // Update data quality metrics
        if (data.data_quality) {
            this.updateDataQualityMetrics(data.data_quality);
        }
    },

    updateCharts: function(chartData) {
        // Update record growth chart
        if (this.charts.recordGrowth && chartData.recordGrowth) {
            this.charts.recordGrowth.data.labels = chartData.recordGrowth.labels;
            this.charts.recordGrowth.data.datasets[0].data = chartData.recordGrowth.data;
            this.charts.recordGrowth.update();
        }

        // Update deactivation trend chart
        if (this.charts.deactivationTrend && chartData.deactivationTrend) {
            this.charts.deactivationTrend.data.labels = chartData.deactivationTrend.labels;
            this.charts.deactivationTrend.data.datasets[0].data = chartData.deactivationTrend.data;
            this.charts.deactivationTrend.update();
        }

        // Update agency comparison chart
        if (this.charts.agencyComparison && chartData.agencyComparison) {
            this.charts.agencyComparison.data.labels = chartData.agencyComparison.labels;
            this.charts.agencyComparison.data.datasets[0].data = chartData.agencyComparison.data;
            this.charts.agencyComparison.update();
        }


        // Update status distribution chart
        if (this.charts.statusDistribution && chartData.statusDistribution) {
            this.charts.statusDistribution.data.labels = chartData.statusDistribution.labels;
            this.charts.statusDistribution.data.datasets[0].data = chartData.statusDistribution.data;
            this.charts.statusDistribution.update();
        }

        // Update gender distribution chart
        if (this.charts.genderDistribution && chartData.genderDistribution) {
            this.charts.genderDistribution.data.labels = chartData.genderDistribution.labels;
            this.charts.genderDistribution.data.datasets[0].data = chartData.genderDistribution.data;
            this.charts.genderDistribution.update();
        }
    },

    updateAgencyTable: function(data) {
        // Update agency records table if DataTable exists
        if (data.agencies && $.fn.DataTable && $.fn.DataTable.isDataTable('#agencyRecordsTable')) {
            var table = $('#agencyRecordsTable').DataTable();
            table.clear();

            data.agencies.forEach(function(agency) {
                var activePercent = agency.hub_records_count > 0 ? (agency.active_count / agency.hub_records_count * 100) : 0;
                var inactivePercent = agency.hub_records_count > 0 ? (agency.inactive_count / agency.hub_records_count * 100) : 0;

                table.row.add([
                    agency.agency_name,
                    '<strong>' + this.formatNumber(agency.hub_records_count) + '</strong>',
                    '<span class="text-success"><strong>' + this.formatNumber(agency.active_count || 0) + '</strong></span>',
                    '<span class="text-danger"><strong>' + this.formatNumber(agency.inactive_count || 0) + '</strong></span>',
                    '<div class="d-flex align-items-center">' +
                        '<span class="mr-2">' + activePercent.toFixed(1) + '%</span>' +
                        '<div class="progress flex-fill" style="height: 8px;">' +
                            '<div class="progress-bar bg-success" style="width: ' + activePercent + '%"></div>' +
                        '</div>' +
                    '</div>',
                    '<div class="d-flex align-items-center">' +
                        '<span class="mr-2">' + inactivePercent.toFixed(1) + '%</span>' +
                        '<div class="progress flex-fill" style="height: 8px;">' +
                            '<div class="progress-bar bg-danger" style="width: ' + inactivePercent + '%"></div>' +
                        '</div>' +
                    '</div>'
                ]);
            }.bind(this));

            table.draw();
        }
    },

    updateDataQualityMetrics: function(dataQuality) {
        // Update data quality metrics in the table
        var totalRecords = dataQuality.total_records;

        // Update Missing Email
        var emailPercent = totalRecords > 0 ? (dataQuality.missing_email / totalRecords) * 100 : 0;
        $('td:contains("Missing Email")').next().text(this.formatNumber(dataQuality.missing_email));
        $('td:contains("Missing Email")').next().next().find('.progress-bar').css('width', emailPercent + '%');

        // Update Missing Mobile
        var phonePercent = totalRecords > 0 ? (dataQuality.missing_phone / totalRecords) * 100 : 0;
        $('td:contains("Missing Mobile")').next().text(this.formatNumber(dataQuality.missing_phone));
        $('td:contains("Missing Mobile")').next().next().find('.progress-bar').css('width', phonePercent + '%');

        // Update Missing SSN
        var ssnPercent = totalRecords > 0 ? (dataQuality.missing_ssn / totalRecords) * 100 : 0;
        $('td:contains("Missing SSN")').next().text(this.formatNumber(dataQuality.missing_ssn));
        $('td:contains("Missing SSN")').next().next().find('.progress-bar').css('width', ssnPercent + '%');

        // Update Invalid Emails
        var invalidPercent = totalRecords > 0 ? (dataQuality.invalid_emails / totalRecords) * 100 : 0;
        $('td:contains("Invalid Emails")').next().text(this.formatNumber(dataQuality.invalid_emails));
        $('td:contains("Invalid Emails")').next().next().find('.progress-bar').css('width', invalidPercent + '%');

        // Update Potential Duplicates
        var dupPercent = totalRecords > 0 ? (dataQuality.duplicate_potential / totalRecords) * 100 : 0;
        $('td:contains("Potential Duplicates")').next().text(this.formatNumber(dataQuality.duplicate_potential));
        $('td:contains("Potential Duplicates")').next().next().find('.progress-bar').css('width', dupPercent + '%');
    },

    exportData: function() {
        var days = $('#dateRange').val();
        var agencies = $('#agencyFilter').val();
        var status = $('#statusFilter').val();

        var params = new URLSearchParams({
            days: days,
            format: 'csv'
        });

        if (agencies && agencies.length > 0) {
            agencies.forEach(function(agency) {
                params.append('agency_ids[]', agency);
            });
        }

        if (status) {
            params.append('status_filter', status);
        }

        window.location.href = '/hub-analytics/export?' + params.toString();
    },

    formatNumber: function(num) {
        return new Intl.NumberFormat().format(num);
    },

    showNotification: function(message, type) {
        // You can implement your preferred notification system here
        console.log(type + ': ' + message);
    }
};
</script>