<div class="row">
            <div class="horizontal-menu col-md-12" style="margin-bottom: 15px;">
                <nav class="bottom-navbar custom-nav">
                    <!-- <div class="container"></div> -->
                    <div class="container-fluid">
                        <ul class="nav page-navigation">
                            @can('referrals-weight-report')
                            <li class="nav-item {{ request()->is('referrals-weight') ? 'active' : '' }} ">
                                <a class="nav-link"  href="{{ url('referrals-weight')}}">
                                <i class="mdi mdi-file menu-icon"></i>
                                    <span class="menu-title">Referrals Stats and Analytics</span>
                                </a>
                            </li>
                            @endcan
                            @can('detailed-refusals-report')
                            <li class="nav-item {{ request()->is('detailed-refusals-report') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('detailed-refusals-report') }}">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Detailed Refusals Report</span>
                                </a>
                            </li>
                            @endcan
                            @can('referrals-analytics-dashboard-report')
                            <li class="nav-item {{ request()->is('referrals-analytics-dashboard-report') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('referrals-analytics-dashboard-report') }}">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Referrals Analytics Dashboard Report</span>
                                </a>
                            </li>
                            @endcan
                            @can('weekly-monthly-states-report')
                            <li class="nav-item {{ request()->is('weekly-monthly-states') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('weekly-monthly-states') }}">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Weekly Monthly States Report</span>
                                </a>
                            </li>
                            @endcan
                              @can('detailed-portal-charts-report')
                            <li class="nav-item {{ request()->is('daily-referral-email') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('daily-referral-email') }}">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Detailed Portal Charts Report</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </nav>
            </div>
        </div>