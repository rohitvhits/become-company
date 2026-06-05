@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/visiting_aid_dashboard.css')}}?time={{ env('timestamp')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" type="text/css" />

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="row">
            <div class="col-md-10">
                <div class="page-title-main">
                    <h5 class="mb-0 font-weight-bold">Visiting Aid Dashboard</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-flex align-items-center">
                    <input class="form-control" placeholder="Select date range" id="range_date" name="range_date" type="text" value="" style="float: left;" />
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <div class="card common-card-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="dash-sm-card">
                                    <div class="row">
                                        <div class="col-lg-2 col-6">
                                            <div class="small-box bg-info">
                                                <div class="inner">
                                                    <h3 id="total_visits">0</h3>
                                                    <p>Total Visits</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-ticket"></i>
                                                </div>
                                                <a href=""
                                                    class="small-box-footer">View All<i
                                                        class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-6">
                                            <div class="small-box bg-warning">
                                                <div class="inner">
                                                    <h3 id="total_agencies">0</h3>
                                                    <p>Total Agencies</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-ticket"></i>
                                                </div>
                                                <a href=""
                                                    class="small-box-footer">View All<i
                                                        class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-6">
                                            <div class="small-box bg-success">
                                                <div class="inner">
                                                    <h3 id="total_patient">0</h3>
                                                    <p>Total Patients Linked</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-ticket"></i>
                                                </div>
                                                <a href=""
                                                    class="small-box-footer">View All<i
                                                        class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-6">
                                            <div class="small-box bg-danger">
                                                <div class="inner">
                                                    <h3 id="pending_requests">0</h3>
                                                    <p>Pending Requests</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-ticket"></i>
                                                </div>
                                                <a href=""
                                                    class="small-box-footer">View All<i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-6">
                                            <div class="small-box bg-secondary">
                                                <div class="inner">
                                                    <h3 id="completed_services">0</h3>
                                                    <p>Completed Services </p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-ticket"></i>
                                                </div>
                                                <a href=""
                                                    class="small-box-footer">View All<i
                                                        class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-6">
                                            <div class="small-box bg-primary">
                                                <div class="inner">
                                                    <h3 id="overdue">0</h3>
                                                    <p>OverDue</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-ticket"></i>
                                                </div>
                                                <a href=""
                                                    class="small-box-footer">
                                                    View All <i class="fa fa-arrow-circle-right"></i>
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="agency-order-listing-loader1" style="display:flex" >
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="dash-side-box">
                                    <div class="box info-box">
                                        <div class="title">
                                            <h5> Agency Wise Request</h5>
                                        </div>
                                    </div>
                                    <div class="chart-box-detail" id="agency_wise_chart">
                                    </div>
                                    <div id="agency-no-data" style="display: none; padding: 50px 0;text-align: center;top: 38%;width: 100%;font-size: 14px;">Nothing to display</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="type-order-listing-loader1" style="display:flex" >
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="dash-side-box">
                                    <div class="box info-box">
                                        <div class="title">
                                            <h5>Type Wise</h5>
                                        </div>
                                    </div>
                                    <div class="chart-box-detail" id="type_wise_chart">
                                    </div>
                                    <div id="type-no-data" style="display:none; padding: 50px 0;text-align: center;top: 38%;width: 100%;font-size: 14px;">Nothing to display</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="services-order-listing-loader1" style="display:flex" >
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="dash-side-box">
                                    <div class="box info-box">
                                        <div class="title">
                                            <h5>Services Status Wise</h5>
                                        </div>
                                    </div>
                                    <div class="chart-box-detail" id="status_donut_chart">
                                    </div>
                                    <div id="services_table_list">
                                    </div>
                                    <div id="services-no-data" style="display: none; padding: 50px 0;text-align: center;top: 38%;width: 100%;font-size: 14px;">Nothing to display</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="dash-side-box">
                                    <div class="box info-box">
                                        <div class="title">
                                            <h5>Visting Aid Data</h5>
                                        </div>
                                    </div>
                                </div>
                                <div id="document_response_list">
                                    <div class="table-responsive1" style="overflow-x:auto; ">
                                        <span id="visiting_aid_list"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-3">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="dash-side-box">
                            <div class="box info-box card common-card-box">
                                <div class="title">
                                    <h5>Pending Tickets</h5>
                                </div>
                                <div class="row basic-detail-row"
                                    style="max-height: calc(100vh - 100px);overflow-y:auto;">
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-12">
                                                <dt> #402 Townhomes at Weston - 2nd, 3rd call URGENT - EKO Management
                                                </dt>
                                            </div>
                                            <div class="col-md-12">
                                                <dd> 4 hours ago </dd>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="dash-side-box">
                            <div class="box info-box card common-card-box">
                                <div class="title">
                                    <h5>Top Categories By Tickets</h5>
                                </div>
                                <div class="row basic-detail-row"
                                    style="max-height:calc(100vh - 340px);overflow-y:auto;">
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row btm-brder">
                                            <div class="col-md-10">
                                                <dt> Townhomes at Weston - non-urgent
                                                </dt>
                                            </div>
                                            <div class="col-md-2">
                                                <dd>11</dd>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    @include('include/footer')
    @include('visitingAidDashboard/js_dashboard')
