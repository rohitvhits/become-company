@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper reports-dashboard">
        <!-- Simple Header Section -->
        <div class="reports-header">
            <div class="header-content">
                <div class="header-text">
                    <h4 class="page-title">Invoice Reports</h4>
                    <p class="page-subtitle">Analytics and insights for invoice management</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.invoices.index') }}" class="invoice-badge-btn btn-secondary btn-sm invoice-action-btn">
                        <i class="mdi mdi-arrow-left me-1"></i>Back to Invoices
                    </a>
                </div>
            </div>
        </div>

        <!-- Compact Stats Overview -->
        <div class="stats-section">
            <div class="row g-3">
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <div class="stats-card animate-fade-up" data-delay="100">
                        <div class="stats-content">
                            <div class="stats-icon bg-gradient-primary">
                                <i class="mdi mdi-file-document"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="stats-number">{{ \App\Model\Invoice::count() }}</h5>
                                <p class="stats-label">Total Invoices</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <div class="stats-card animate-fade-up" data-delay="200">
                        <div class="stats-content">
                            <div class="stats-icon bg-gradient-success">
                                <i class="mdi mdi-check-circle"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="stats-number">{{ \App\Model\Invoice::where('status', 'paid')->count() }}</h5>
                                <p class="stats-label">Paid Invoices</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <div class="stats-card animate-fade-up" data-delay="300">
                        <div class="stats-content">
                            <div class="stats-icon bg-gradient-warning">
                                <i class="mdi mdi-alert-circle"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="stats-number">{{ \App\Model\Invoice::whereIn('status', ['sent', 'overdue'])->count() }}</h5>
                                <p class="stats-label">Outstanding</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <div class="stats-card animate-fade-up" data-delay="400">
                        <div class="stats-content">
                            <div class="stats-icon bg-gradient-info">
                                <i class="mdi mdi-credit-card"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="stats-number">{{ \App\Model\InvoicePayment::where('status', 'completed')->count() }}</h5>
                                <p class="stats-label">Total Payments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Reports Section -->
        <div class="reports-section">
            <div class="section-header">
                <h5 class="section-title">Available Reports</h5>
            </div>

            <div class="reports-grid">
                <div class="row g-3">
                    @foreach($reports as $index => $report)
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="report-card animate-slide-up" data-delay="{{ ($index + 1) * 100 }}">
                            <div class="report-card-inner">
                                <div class="report-header">
                                    <div class="report-icon bg-gradient-{{ $report['color'] }}">
                                        <i class="mdi {{ $report['icon'] }}"></i>
                                    </div>
                                </div>
                                <div class="report-content">
                                    <h6 class="report-title">{{ $report['title'] }}</h6>
                                    <p class="report-description">{{ $report['description'] }}</p>
                                </div>
                                <div class="report-footer">
                                    <a href="{{ route($report['route']) }}" class="report-btn invoice-badge-btn btn-{{ $report['color'] }} btn-sm invoice-action-btn">
                                        <i class="mdi mdi-chart-line me-1"></i>View Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="quick-actions-section">
            <div class="section-header">
                <h5 class="section-title">Quick Actions</h5>
            </div>
            <div class="row g-2">
                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('admin.invoices.create') }}" class="quick-action-card">
                        <div class="quick-action-icon bg-gradient-primary">
                            <i class="mdi mdi-plus-circle"></i>
                        </div>
                        <span class="quick-action-text">Create Invoice</span>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('admin.invoices.index', ['status' => 'overdue']) }}" class="quick-action-card">
                        <div class="quick-action-icon bg-gradient-danger">
                            <i class="mdi mdi-clock-alert"></i>
                        </div>
                        <span class="quick-action-text">Overdue Invoices</span>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('admin.invoices.index', ['status' => 'paid']) }}" class="quick-action-card">
                        <div class="quick-action-icon bg-gradient-success">
                            <i class="mdi mdi-check-all"></i>
                        </div>
                        <span class="quick-action-text">Paid Invoices</span>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('admin.reports.invoices.summary') }}" class="quick-action-card">
                        <div class="quick-action-icon bg-gradient-info">
                            <i class="mdi mdi-chart-pie"></i>
                        </div>
                        <span class="quick-action-text">Summary Report</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Simple Project Theme Styles */
    .reports-dashboard {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 15px;
    }

    .reports-header {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 1.4rem;
        font-weight: 600;
        margin: 0;
        color: #495057;
    }

    .page-subtitle {
        font-size: 0.875rem;
        margin: 3px 0 0 0;
        color: #6c757d;
    }

    /* Compact Stats Cards */
    .stats-section {
        margin-bottom: 20px;
    }

    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        height: 100%;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .stats-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stats-icon {
        width: 45px;
        height: 45px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        flex-shrink: 0;
    }

    .stats-number {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        color: #495057;
        line-height: 1;
    }

    .stats-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin: 2px 0 0 0;
        font-weight: 500;
    }

    /* Compact Report Cards */
    .reports-section {
        margin-bottom: 20px;
    }

    .section-header {
        margin-bottom: 15px;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    .report-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        overflow: hidden;
        border: 1px solid #e9ecef;
        height: 100%;
    }

    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .report-card-inner {
        padding: 18px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .report-header {
        display: flex;
        justify-content: center;
        margin-bottom: 12px;
    }

    .report-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .report-content {
        flex: 1;
        text-align: center;
    }

    .report-title {
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }

    .report-description {
        color: #6c757d;
        font-size: 0.8rem;
        line-height: 1.4;
        margin: 0;
    }

    .report-footer {
        margin-top: 15px;
    }

    .report-btn {
        width: 100%;
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 0.85rem;
    }

    /* Quick Actions */
    .quick-actions-section {
        margin-bottom: 20px;
    }

    .quick-action-card {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 12px 15px;
        border-radius: 6px;
        text-decoration: none;
        color: #495057;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: #495057;
        text-decoration: none;
    }

    .quick-action-icon {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
        flex-shrink: 0;
    }

    .quick-action-text {
        font-weight: 500;
        font-size: 0.85rem;
    }

    /* Simple Gradient Backgrounds - No Purple for Primary */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #20c997 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #17a2b8 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    /* Animations */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-5px);
        }
        60% {
            transform: translateY(-3px);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(180deg);
        }
    }

    .animate-fade-up {
        animation: fadeUp 0.6s ease-out forwards;
        opacity: 0;
    }

    .animate-slide-up {
        animation: slideUp 0.8s ease-out forwards;
        opacity: 0;
    }

    .animate-bounce {
        animation: bounce 2s infinite;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .reports-dashboard {
            padding: 10px;
        }

        .reports-header {
            padding: 15px;
        }

        .header-content {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .page-title {
            font-size: 1.2rem;
        }

        .page-subtitle {
            font-size: 0.8rem;
        }

        .stats-content {
            gap: 10px;
        }

        .stats-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .stats-number {
            font-size: 1.3rem;
        }

        .stats-label {
            font-size: 0.75rem;
        }

        .report-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .report-title {
            font-size: 0.9rem;
        }

        .report-description {
            font-size: 0.75rem;
        }

        .section-title {
            font-size: 1.1rem;
        }

        .quick-action-card {
            padding: 10px;
            gap: 8px;
        }

        .quick-action-icon {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }

        .quick-action-text {
            font-size: 0.8rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple staggered animation initialization
    const animateElements = document.querySelectorAll('[data-delay]');

    animateElements.forEach(element => {
        const delay = element.getAttribute('data-delay');
        element.style.animationDelay = delay + 'ms';
    });

    // Simple intersection observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, { threshold: 0.1 });

    // Observe animated elements
    document.querySelectorAll('.animate-fade-up, .animate-slide-up').forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });
});

</script>

@include('include/footer')