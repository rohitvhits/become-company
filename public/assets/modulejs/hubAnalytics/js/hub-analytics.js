/**
 * Hub Analytics JavaScript Module
 * Handles all chart initialization, data refresh, and user interactions
 */

var HubAnalytics = {
    data: {},
    charts: {},
    config: {
        colors: {
            primary: '#007bff',
            success: '#28a745',
            danger: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8',
            secondary: '#6c757d',
            gradient: {
                primary: ['#007bff', '#0056b3'],
                success: ['#28a745', '#1e7e34'],
                danger: ['#dc3545', '#c82333'],
                warning: ['#ffc107', '#e0a800'],
                info: ['#17a2b8', '#117a8b']
            }
        }
    },

    init: function(data) {
        this.data = data;
        this.initializeFilters();
        this.initializeCharts();
        this.initializeTables();
        this.bindEvents();
        this.startAutoRefresh();
    },

    initializeFilters: function() {
        // Initialize Select2 for agency filter
        $('.select2-multiple').select2({
            placeholder: 'Select Agencies',
            allowClear: true,
            width: '100%'
        });

        // Initialize date range picker if available
        if (typeof daterangepicker !== 'undefined') {
            this.initializeDateRangePicker();
        }
    },

    initializeDateRangePicker: function() {
        $('#dateRangePicker').daterangepicker({
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });
    },

    initializeCharts: function() {
        try {
            this.createRecordGrowthChart();
            this.createDeactivationTrendChart();
            this.createAgencyComparisonChart();
            this.createApiUsageChart();
            this.createStatusDistributionChart();
            this.createGenderDistributionChart();
            this.createImportSuccessChart();
        } catch (error) {
            console.error('Error initializing charts:', error);
            this.showNotification('Error loading charts', 'error');
        }
    },

    createRecordGrowthChart: function() {
        var ctx = document.getElementById('recordGrowthChart');
        if (!ctx) return;

        ctx = ctx.getContext('2d');
        var chartData = this.data.charts.recordGrowth;

        this.charts.recordGrowth = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Records Created',
                    data: chartData.data || [],
                    borderColor: this.config.colors.primary,
                    backgroundColor: this.createGradient(ctx, this.config.colors.primary, 0.2),
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: this.config.colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: this.config.colors.primary,
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false
                    }
                }
            }
        });
    },

    createDeactivationTrendChart: function() {
        var ctx = document.getElementById('deactivationTrendChart');
        if (!ctx) return;

        ctx = ctx.getContext('2d');
        var chartData = this.data.charts.deactivationTrend;

        this.charts.deactivationTrend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Deactivated Records',
                    data: chartData.data || [],
                    borderColor: this.config.colors.danger,
                    backgroundColor: this.createGradient(ctx, this.config.colors.danger, 0.2),
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: this.config.colors.danger,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: this.config.colors.danger,
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false
                    }
                }
            }
        });
    },

    createAgencyComparisonChart: function() {
        var ctx = document.getElementById('agencyComparisonChart');
        if (!ctx) return;

        ctx = ctx.getContext('2d');
        var chartData = this.data.charts.agencyComparison;

        this.charts.agencyComparison = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Total Records',
                    data: chartData.data || [],
                    backgroundColor: this.createGradient(ctx, this.config.colors.info, 0.8),
                    borderColor: this.config.colors.info,
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 11
                            },
                            maxRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: this.config.colors.info,
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false
                    }
                }
            }
        });
    },

    createApiUsageChart: function() {
        var chartData = this.data.charts.apiUsage;
        var container = document.getElementById('apiUsageChart');

        if (!container) return;

        container.innerHTML = '';

        if (!chartData || chartData.length === 0) {
            this.showEmptyState(container, 'No API usage data available');
            return;
        }

        try {
            Morris.Donut({
                element: 'apiUsageChart',
                data: chartData,
                colors: [
                    this.config.colors.primary,
                    this.config.colors.success,
                    this.config.colors.warning,
                    this.config.colors.danger,
                    this.config.colors.info,
                    this.config.colors.secondary
                ],
                resize: true,
                formatter: function(x, data) {
                    return x + ' calls (' + Math.round(x/data.total*100) + '%)';
                }
            });
        } catch (error) {
            console.error('Error creating API usage chart:', error);
            this.showEmptyState(container, 'Error loading chart');
        }
    },

    createStatusDistributionChart: function() {
        var ctx = document.getElementById('statusDistributionChart');
        if (!ctx) return;

        ctx = ctx.getContext('2d');
        var chartData = this.data.charts.statusDistribution;

        this.charts.statusDistribution = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    data: chartData.data || [],
                    backgroundColor: [
                        this.config.colors.success,
                        this.config.colors.danger,
                        this.config.colors.secondary
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = Math.round((context.parsed / total) * 100);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    },

    createGenderDistributionChart: function() {
        var ctx = document.getElementById('genderDistributionChart');
        if (!ctx) return;

        ctx = ctx.getContext('2d');
        var chartData = this.data.charts.genderDistribution;

        this.charts.genderDistribution = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    data: chartData.data || [],
                    backgroundColor: [
                        this.config.colors.primary,
                        this.config.colors.warning,
                        this.config.colors.secondary
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = Math.round((context.parsed / total) * 100);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    },

    createImportSuccessChart: function() {
        // This method can be implemented if you have import success data
        // For now, we'll skip it as it's not in the main dashboard
    },

    initializeTables: function() {
        if ($.fn.DataTable) {
            $('#agencyPerformanceTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                info: true,
                searching: true,
                ordering: true,
                order: [[1, 'desc']],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search agencies..."
                },
                columnDefs: [
                    {
                        targets: [1], // Record count column
                        type: 'num-fmt'
                    }
                ]
            });
        }
    },

    bindEvents: function() {
        var self = this;

        // Refresh button
        $('#refreshData').on('click', function(e) {
            e.preventDefault();
            self.refreshData();
        });

        // Export button
        $('#exportData').on('click', function(e) {
            e.preventDefault();
            self.exportData();
        });

        // Filter changes
        $('#dateRange, #agencyFilter').on('change', function() {
            clearTimeout(self.refreshTimeout);
            self.refreshTimeout = setTimeout(function() {
                self.refreshData();
            }, 500);
        });

        // Auto-refresh toggle
        $('#autoRefresh').on('change', function() {
            if ($(this).is(':checked')) {
                self.startAutoRefresh();
            } else {
                self.stopAutoRefresh();
            }
        });

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch (e.which) {
                    case 82: // Ctrl+R
                        e.preventDefault();
                        self.refreshData();
                        break;
                    case 69: // Ctrl+E
                        e.preventDefault();
                        self.exportData();
                        break;
                }
            }
        });
    },

    refreshData: function() {
        var $btn = $('#refreshData');
        var $icon = $btn.find('i');

        // Show loading state
        $btn.addClass('loading').prop('disabled', true);
        $icon.removeClass('mdi-refresh').addClass('mdi-loading');

        var days = $('#dateRange').val();
        var agencies = $('#agencyFilter').val();

        $.ajax({
            url: '/hub-analytics/refresh',
            method: 'GET',
            data: {
                days: days,
                agency_ids: agencies
            },
            timeout: 30000,
            success: function(response) {
                if (response.success) {
                    this.updateDashboard(response.data);
                    this.showNotification('Data refreshed successfully', 'success');
                } else {
                    this.showNotification(response.message || 'Error refreshing data', 'error');
                }
            }.bind(this),
            error: function(xhr, status, error) {
                console.error('Refresh error:', error);
                if (status === 'timeout') {
                    this.showNotification('Request timeout. Please try again.', 'error');
                } else {
                    this.showNotification('Error refreshing data', 'error');
                }
            }.bind(this),
            complete: function() {
                $btn.removeClass('loading').prop('disabled', false);
                $icon.removeClass('mdi-loading').addClass('mdi-refresh');
            }
        });
    },

    updateDashboard: function(data) {
        try {
            // Update internal data object with new data
            this.data = data;

            // Update summary cards
            this.updateSummaryCards(data);

            // Update charts
            if (data.charts) {
                this.updateCharts(data.charts);
            }

            // Update tables
            this.updateTables(data);

        } catch (error) {
            console.error('Error updating dashboard:', error);
            this.showNotification('Error updating dashboard', 'error');
        }
    },

    updateSummaryCards: function(data) {
        $('#totalRecords').text(this.formatNumber(data.records.total_records));
        $('#activeRecords').text(this.formatNumber(data.records.active_records));
        $('#inactiveRecords').text(this.formatNumber(data.records.inactive_records));
        $('#totalAgencies').text(this.formatNumber(data.agencies.length));
        $('#totalApiCalls').text(this.formatNumber(data.api.total_calls));
        $('#uniqueEndpoints').text(this.formatNumber(data.api.unique_endpoints));
        $('#totalImports').text(this.formatNumber(data.imports.total_imports));
        $('#successfulImports').text(this.formatNumber(data.imports.successful_imports));
        $('#failedImports').text(this.formatNumber(data.imports.failed_imports));

        // Add subtle animation to updated values
        $('.card-title').addClass('text-updated');
        setTimeout(function() {
            $('.card-title').removeClass('text-updated');
        }, 1000);
    },

    updateCharts: function(chartData) {
        // Update record growth chart
        if (this.charts.recordGrowth && chartData.recordGrowth) {
            this.charts.recordGrowth.data.labels = chartData.recordGrowth.labels;
            this.charts.recordGrowth.data.datasets[0].data = chartData.recordGrowth.data;
            this.charts.recordGrowth.update('resize');
        }

        // Update deactivation trend chart
        if (this.charts.deactivationTrend && chartData.deactivationTrend) {
            this.charts.deactivationTrend.data.labels = chartData.deactivationTrend.labels;
            this.charts.deactivationTrend.data.datasets[0].data = chartData.deactivationTrend.data;
            this.charts.deactivationTrend.update('resize');
        }

        // Update agency comparison chart
        if (this.charts.agencyComparison && chartData.agencyComparison) {
            this.charts.agencyComparison.data.labels = chartData.agencyComparison.labels;
            this.charts.agencyComparison.data.datasets[0].data = chartData.agencyComparison.data;
            this.charts.agencyComparison.update('resize');
        }

        // Update API usage chart
        if (chartData.apiUsage) {
            this.createApiUsageChart();
        }

        // Update status distribution chart
        if (this.charts.statusDistribution && chartData.statusDistribution) {
            this.charts.statusDistribution.data.labels = chartData.statusDistribution.labels;
            this.charts.statusDistribution.data.datasets[0].data = chartData.statusDistribution.data;
            this.charts.statusDistribution.update('resize');
        }

        // Update gender distribution chart
        if (this.charts.genderDistribution && chartData.genderDistribution) {
            this.charts.genderDistribution.data.labels = chartData.genderDistribution.labels;
            this.charts.genderDistribution.data.datasets[0].data = chartData.genderDistribution.data;
            this.charts.genderDistribution.update('resize');
        }
    },

    updateTables: function(data) {
        // Update agency performance table
        if (data.agencies && $.fn.DataTable && $.fn.DataTable.isDataTable('#agencyPerformanceTable')) {
            var table = $('#agencyPerformanceTable').DataTable();
            table.clear();

            data.agencies.forEach(function(agency) {
                table.row.add([
                    agency.agency_name,
                    this.formatNumber(agency.hub_records_count),
                    this.calculatePercentage(agency.hub_records_count, data.records.total_records) + '%',
                    this.getPerformanceBadge(agency.hub_records_count)
                ]);
            }.bind(this));

            table.draw();
        }
    },

    exportData: function() {
        var days = $('#dateRange').val();
        var agencies = $('#agencyFilter').val();
        var format = 'csv'; // Default format

        var params = new URLSearchParams({
            days: days,
            format: format
        });

        if (agencies && agencies.length > 0) {
            agencies.forEach(function(agency) {
                params.append('agency_ids[]', agency);
            });
        }

        // Show download notification
        this.showNotification('Download started...', 'info');

        window.location.href = '/hub-analytics/export?' + params.toString();
    },

    startAutoRefresh: function() {
        var self = this;
        this.stopAutoRefresh(); // Clear any existing interval

        this.autoRefreshInterval = setInterval(function() {
            self.refreshData();
        }, 5 * 60 * 1000); // Refresh every 5 minutes
    },

    stopAutoRefresh: function() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    },

    // Utility functions
    formatNumber: function(num) {
        if (num === null || num === undefined) return '0';
        return new Intl.NumberFormat().format(num);
    },

    calculatePercentage: function(value, total) {
        if (!total || total === 0) return '0.0';
        return ((value / total) * 100).toFixed(1);
    },

    getPerformanceBadge: function(count) {
        if (count > 100) {
            return '<span class="badge badge-success">High</span>';
        } else if (count > 50) {
            return '<span class="badge badge-warning">Medium</span>';
        } else {
            return '<span class="badge badge-secondary">Low</span>';
        }
    },

    createGradient: function(ctx, color, alpha) {
        if (!ctx || !color) return color;

        var gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, color + Math.round(alpha * 255).toString(16));
        gradient.addColorStop(1, color + '00');
        return gradient;
    },

    showEmptyState: function(container, message) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="mdi mdi-chart-line"></i>
                <h6>No Data Available</h6>
                <p>${message}</p>
            </div>
        `;
    },

    showNotification: function(message, type, duration) {
        duration = duration || 4000;

        var notification = $(`
            <div class="notification ${type}">
                <i class="mdi mdi-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `);

        $('body').append(notification);

        setTimeout(function() {
            notification.addClass('show');
        }, 100);

        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, duration);
    },

    getNotificationIcon: function(type) {
        switch (type) {
            case 'success': return 'check-circle';
            case 'error': return 'alert-circle';
            case 'warning': return 'alert';
            case 'info': return 'information';
            default: return 'information';
        }
    },

    // Cleanup function
    destroy: function() {
        this.stopAutoRefresh();

        // Destroy charts
        Object.keys(this.charts).forEach(function(key) {
            if (this.charts[key] && typeof this.charts[key].destroy === 'function') {
                this.charts[key].destroy();
            }
        }.bind(this));

        // Destroy tables
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#agencyPerformanceTable')) {
            $('#agencyPerformanceTable').DataTable().destroy();
        }

        // Remove event listeners
        $(document).off('keydown');
        $('#refreshData, #exportData, #dateRange, #agencyFilter, #autoRefresh').off();
    }
};

// Auto-initialize when DOM is ready
$(document).ready(function() {
    // Add CSS class for text update animation
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .text-updated {
                animation: textPulse 0.6s ease-in-out;
            }
            @keyframes textPulse {
                0% { color: inherit; }
                50% { color: #007bff; }
                100% { color: inherit; }
            }
        `)
        .appendTo('head');
});