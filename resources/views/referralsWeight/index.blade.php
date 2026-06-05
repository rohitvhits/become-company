@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/modulejs/css/appointment_dashboard.css') }}?time={{ env('timestamp') }}">
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/report.css') }}?time={{ env('timestamp') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" type="text/css" />
<style>
    .horizontal-menu .custom-nav,
    .horizontal-menu .bottom-navbar .page-navigation {
        position: unset;
    }

    .nav-pills-success .nav-link.active {
        background: #000000;
    }

    .nav-pills-success .nav-link {
        color: #000000;
    }

    .tab-content {
        padding: 0 !important;
        text-align: unset;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    thead th {
        position: sticky;
        top: 0;
        background: #fff;
        /* keep header visible */
        z-index: 10;
        /* ensures header stays above rows */
        border: 1px solid #ddd;
    }

    /* Agency Toggle Button Styles */
    .agency-filter-toggle-wrapper,
    .service-filter-toggle-wrapper,
    .branch-filter-toggle-wrapper {
        display: inline-flex;
        align-items: center;
        margin-left: 8px;
        gap: 6px;
    }

    .agency-toggle-btn,
    .service-toggle-btn,
    .branch-toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: 2px solid;
        border-radius: 50%;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .agency-toggle-btn i,
    .service-toggle-btn i,
    .branch-toggle-btn i {
        font-size: 18px;
        line-height: 1;
        pointer-events: none;
        margin: 0;
    }

    .agency-toggle-btn[data-mode="include"],
    .service-toggle-btn[data-mode="include"],
    .branch-toggle-btn[data-mode="include"] {
        background-color: #cfe2ff !important;
        border-color: #0d6efd !important;
        color: #084298 !important;
    }

    .agency-toggle-btn[data-mode="include"]:hover,
    .service-toggle-btn[data-mode="include"]:hover,
    .branch-toggle-btn[data-mode="include"]:hover {
        background-color: #b6d4fe !important;
        transform: scale(1.05);
    }

    .agency-toggle-btn[data-mode="exclude"],
    .service-toggle-btn[data-mode="exclude"],
    .branch-toggle-btn[data-mode="exclude"] {
        background-color: #e9ecef !important;
        border-color: #6c757d !important;
        color: #495057 !important;
    }

    .agency-toggle-btn[data-mode="exclude"]:hover,
    .service-toggle-btn[data-mode="exclude"]:hover,
    .branch-toggle-btn[data-mode="exclude"]:hover {
        background-color: #dee2e6 !important;
        transform: scale(1.05);
    }

    .agency-toggle-btn:focus,
    .service-toggle-btn:focus,
    .branch-toggle-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    .agency-toggle-btn:active,
    .service-toggle-btn:active,
    .branch-toggle-btn:active {
        transform: scale(0.95);
    }

    .agency-toggle-label,
    .service-toggle-label,
    .branch-toggle-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        user-select: none;
    }

    .agency-toggle-label.mode-include,
    .service-toggle-label.mode-include,
    .branch-toggle-label.mode-include {
        color: #0d6efd;
    }

    .agency-toggle-label.mode-exclude,
    .service-toggle-label.mode-exclude,
    .branch-toggle-label.mode-exclude {
        color: #6c757d;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        @canany(['detailed-refusals-report', 'referrals-analytics-dashboard-report', 'weekly-monthly-states-report'])
        @include('referralsWeight/reports-nav')
        @endcan

        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="col-md-10">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 font-weight-bold">Referrals Weight</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="user_range_date" class="col-sm-2 col-form-label">Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" value="{{ $dateRange }}" class="form-control"
                                                id="user_range_date" name="user_range_date"
                                                placeholder="Select date range">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="last_updated_date" class="col-sm-12 col-form-label">Last Status
                                            Updated Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" value="" class="form-control" id="last_updated_date"
                                                name="last_updated_date" placeholder="Select date range">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputUsername2" class="col-sm-2 col-form-label">Type</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" id="type">

                                                <option value="Patient">Patient</option>
                                                <option value="Caregiver">Caregiver</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 col-form-label">
                                            Agency Name
                                            <span class="agency-filter-toggle-wrapper">
                                                <button type="button" class="agency-toggle-btn" id="agencyToggleBtn"
                                                    data-mode="include" title="Include - Click to switch to Exclude">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                                <span class="agency-toggle-label mode-include"
                                                    id="agencyToggleLabel">Include Agency</span>
                                            </span>
                                        </label>
                                        <div class="col-sm-12">
                                            <input type="hidden" name="agency_filter_type" id="agency_filter_type"
                                                value="include">
                                            <select name="agency_fk[]" id="agency_fk"
                                                class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                multiple="multiple">
                                                @foreach ($agency_list as $agency)
                                                <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 col-form-label">
                                            Services
                                            <span class="service-filter-toggle-wrapper">
                                                <button type="button" class="service-toggle-btn" id="serviceToggleBtn"
                                                    data-mode="include" title="Include - Click to switch to Exclude">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                                <span class="service-toggle-label mode-include"
                                                    id="serviceToggleLabel">Include Service</span>
                                            </span>
                                        </label>
                                        <div class="col-sm-12">
                                            <input type="hidden" name="service_filter_type" id="service_filter_type"
                                                value="include">
                                            <select name="service_id[]" id="service_id"
                                                class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                multiple="multiple">
                                                @foreach ($serviceList as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="assigned_to" class="col-sm-12 col-form-label">Assigned To</label>
                                        <div class="col-sm-12">
                                            <select name="assigned_to[]" id="assigned_to"
                                                class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                multiple="multiple">
                                                @foreach ($userList as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="medication_list" class="col-sm-12 col-form-label">Medication
                                            List</label>
                                        <div class="col-sm-12">
                                            <select name="medication_list" class="form-control" id="medication_list">
                                                <option value="">Select Medication List</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="insurance_elg" class="col-sm-12 col-form-label">Insurance
                                            Elg</label>
                                        <div class="col-sm-12">
                                            <select name="insurance_elg" class="form-control" id="insurance_elg">
                                                <option value="">Select Insurance Elg</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mdo_tag" class="col-sm-12 col-form-label">Mdo Tag</label>
                                        <div class="col-sm-12">
                                            <select name="mdo_tag" class="form-control" id="mdo_tag">
                                                <option value="">Select Mdo Tag</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-sm-12 col-form-label">
                                            Branch
                                            <span class="branch-filter-toggle-wrapper">
                                                <button type="button" class="branch-toggle-btn" id="branchToggleBtn"
                                                    data-mode="include" title="Include - Click to switch to Exclude">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                                <span class="branch-toggle-label mode-include"
                                                    id="branchToggleLabel">Include Branch</span>
                                            </span>
                                        </label>
                                        <div class="col-sm-12">
                                            <input type="hidden" name="branch_filter_type" id="branch_filter_type"
                                                value="include">
                                            <select name="branch_id[]" id="branch_id"
                                                class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                multiple="multiple">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-top: 30px;">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-sm" id="searchBtn"
                                            onclick="filter()">Search</button>
                                        <button class="btn btn-secondary btn-sm" id="clearBtn">Clear</button>
                                        <button class="btn btn-info btn-sm" id="exportBtn">Export</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row col-md-12 row grid-margin stretch-card">
                <div class="col-md-12">
                    <div>
                        <ul class="nav nav-pills nav-pills-success" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-table-tab" data-toggle="pill" href="#pills-table"
                                    role="tab" aria-controls="pills-table" aria-selected="false">Tables
                                    View</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-charts-tab" data-toggle="pill" href="#pills-charts"
                                    role="tab" aria-controls="pills-charts" aria-selected="false">Charts
                                    View</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade active show" id="pills-table" role="tabpanel" aria-labelledby="pills-table-tab">
                <div class="row">
                    <div class="col-md-12 row grid-margin stretch-card">
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="user-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Services Overview</h5>
                                        </div>

                                    </div>
                                    <div id="user_view_chart"></div>
                                    <div id="user-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="agency-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Agency Overview</h5>
                                        </div>

                                    </div>
                                    <div id="agency_view_chart"></div>
                                    <div id="agency-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="bookings-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Bookings VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="bookings_view_chart"></div>
                                    <div id="bookings-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 row grid-margin stretch-card">


                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="cancellations-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Cancellations VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="cancellations_view_chart"></div>
                                    <div id="cancellations-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="refusals-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-8">
                                            <h5>Refusals VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="refusals_view_chart"></div>
                                    <div id="refusals-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="unabletocontact-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Unable To Contact VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="unabletocontact_view_chart"></div>
                                    <div id="unabletocontact-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 row grid-margin stretch-card">
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="detailed-cancellations-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5">
                                            <h5>Detailed Cancellations</h5>
                                        </div>
                                        <div class="col-md-7" style="display: none">
                                            <div class="flex-input-control">
                                                <label>Agency</label>
                                                <select name="agency_fk[]" id="agencyIdCancel"
                                                    class="form-control select2-design refusals-select2 cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple">

                                                    @foreach ($agency_list as $agency)
                                                    <option value="{{ $agency->id }}">{{ $agency->agency_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="detailed-cancellations_view_chart"></div>
                                    <div id="detailed-cancellations-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="detailed-refusals-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5">
                                            <h5>Detailed Refusals</h5>
                                        </div>
                                        <div class="col-md-7" style="display: none">
                                            <div class="flex-input-control">
                                                <label>Agency</label>
                                                <select name="agency_fk[]" id="agencyId"
                                                    class="form-control select2-design refusals-select2 cal-padding-0 js-example-basic-multiple w-100"
                                                    multiple="multiple">

                                                    @foreach ($agency_list as $agency)
                                                    <option value="{{ $agency->id }}">{{ $agency->agency_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="detailed-refusals_view_chart"></div>
                                    <div id="detailed-refusals-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="completed-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Completed Forms VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="completed_view_chart"></div>
                                    <div id="completed-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 row grid-margin stretch-card">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="status-order-listing-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Status VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="status_view_chart"></div>
                                    <div id="status-no-data"
                                        style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">
                                        No records found</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-charts" role="tabpanel" aria-labelledby="pills-charts-tab">
                <div class="row">
                    <div class="col-md-12 row grid-margin stretch-card">
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="user-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Services Overview</h5>
                                        </div>

                                    </div>
                                    <div id="user_chart">
                                        <div id="pieChartNew">
                                        </div>
                                    </div>
                                    <div id="user-no-chart"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="agency-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Agency Overview</h5>
                                        </div>

                                    </div>
                                    <div id="agency_chart">
                                        <div id="agencypieChartNew">
                                        </div>
                                    </div>
                                    <div id="agency-no-chart"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="bookings-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Bookings VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="bookings_chart">
                                        <div id="bookingpieChartNew">
                                        </div>
                                    </div>
                                    <div id="bookings-no-chart"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 row grid-margin stretch-card">


                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="cancellations-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Cancellations VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="cancellations_chart">
                                        <div id="cancellationspieChartNew">
                                        </div>
                                    </div>
                                    <div id="cancellations-no-data"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="refusals-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-8">
                                            <h5>Refusals VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="refusals_chart">
                                        <div id="refusalspieChartNew">
                                        </div>
                                    </div>
                                    <div id="refusals-no-chart"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="unabletocontact-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Unable To Contact VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="unabletocontact_chart">
                                        <div id="unabletocontactpieChartNew">
                                        </div>
                                    </div>
                                    <div id="unabletocontact-no-chart"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 row grid-margin stretch-card">
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="detailed-cancellations-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <h5>Detailed Cancellations</h5>
                                        </div>
                                        <div class="col-md-7">

                                        </div>

                                    </div>
                                    <div id="detailed-cancellations_chart">
                                        <div id="detailed-cancellationspieChartNew">
                                        </div>
                                    </div>
                                    <div id="detailed-cancellations-no-chart"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="detailed-refusals-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5">
                                            <h5>Detailed Refusals</h5>
                                        </div>
                                        <div class="col-md-7">

                                        </div>

                                    </div>
                                    <div id="detailed-refusals_chart">
                                        <div id="detailed-refusalspieChartNew">
                                        </div>
                                    </div>
                                    <div id="detailed-refusals-no-chart"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card" style="height:400px">
                                <div class="card-body">
                                    <div class="completed-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Completed Forms VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="completed_chart">
                                        <div id="completedpieChartNew">
                                        </div>
                                    </div>
                                    <div id="completed-no-chart"
                                        style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">
                                        Nothing to display</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 row grid-margin stretch-card">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="status-order-chart-loader1" style="display:flex">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div class="row  mb-3">
                                        <div class="col-md-8">
                                            <h5>Status VS Agencies</h5>
                                        </div>

                                    </div>
                                    <div id="status_chart">
                                        <canvas id="stackedChart"></canvas>
                                    </div>
                                    <div id="status-chart-no-chart"
                                        style="position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 15px;top: 38%;width: 100%;    border: hidden;">
                                        No data</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="margin-top:10%">

    </div>
</div>
@include('include/footer')
<script>
    var _SERVICE_LIST = "{{ url('service-count-ajax') }}";
    var _AGENCY_LIST = "{{ url('agency-count-ajax') }}";
    var _BOOKING_LIST = "{{ url('booking-count-ajax') }}";
    var _CANCELLATIONS_LIST = "{{ url('cancellations-count-ajax') }}";
    var _REFUSALS_LIST = "{{ url('refusals-count-ajax') }}";
    var _DETAILED_REFUSALS_LIST = "{{ url('detailed-refusals-count-ajax') }}";
    var _DETAILED_CANCELLATIONS_LIST = "{{ url('detailed-cancellations-count-ajax') }}";
    var _UNABLETOCONTACT_LIST = "{{ url('unabletocontact-count-ajax') }}";
    var _COMPLETED_LIST = "{{ url('completed-count-ajax') }}";
    var _STATUS_LIST = "{{ url('status-count-ajax') }}";
    var _GET_BRANCHES = "{{ url('get-branches') }}";
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{ asset('assets/js/xlsx.full.min.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('/assets/modulejs/referralsWeight/referralsWeight.js') }}?time={{ env('timestamp') }}"></script>
<script>
    // Agency Filter Toggle Button
    $(document).ready(function() {
        const $toggleBtn = $('#agencyToggleBtn');
        const $toggleLabel = $('#agencyToggleLabel');
        const $filterTypeInput = $('#agency_filter_type');

        if ($toggleBtn.length === 0) {
            console.warn('Agency toggle button not found!');
            return;
        }

        // Update button appearance, label, and hidden input
        function updateButton(mode) {
            $toggleBtn.attr('data-mode', mode);
            $filterTypeInput.val(mode);

            if (mode === 'include') {
                $toggleBtn.html('<i class="mdi mdi-plus"></i>');
                $toggleBtn.attr('title', 'Include - Click to switch to Exclude');
                $toggleLabel.text('Include Agency').removeClass('mode-exclude').addClass('mode-include');
            } else {
                $toggleBtn.html('<i class="mdi mdi-minus"></i>');
                $toggleBtn.attr('title', 'Exclude - Click to switch to Include');
                $toggleLabel.text('Exclude Agency').removeClass('mode-include').addClass('mode-exclude');
            }
        }

        // Toggle on click with event delegation to handle dynamic content
        $(document).on('click', '#agencyToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateButton(newMode);
        });
    });

    // Service Filter Toggle Button
    $(document).ready(function() {
        const $serviceToggleBtn = $('#serviceToggleBtn');
        const $serviceToggleLabel = $('#serviceToggleLabel');
        const $serviceFilterTypeInput = $('#service_filter_type');

        if ($serviceToggleBtn.length === 0) {
            console.warn('Service toggle button not found!');
            return;
        }

        // Update button appearance, label, and hidden input
        function updateServiceButton(mode) {
            $serviceToggleBtn.attr('data-mode', mode);
            $serviceFilterTypeInput.val(mode);

            if (mode === 'include') {
                $serviceToggleBtn.html('<i class="mdi mdi-plus"></i>');
                $serviceToggleBtn.attr('title', 'Include - Click to switch to Exclude');
                $serviceToggleLabel.text('Include Service').removeClass('mode-exclude').addClass('mode-include');
            } else {
                $serviceToggleBtn.html('<i class="mdi mdi-minus"></i>');
                $serviceToggleBtn.attr('title', 'Exclude - Click to switch to Include');
                $serviceToggleLabel.text('Exclude Service').removeClass('mode-include').addClass('mode-exclude');
            }
        }

        // Toggle on click with event delegation to handle dynamic content
        $(document).on('click', '#serviceToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateServiceButton(newMode);
        });
    });

    // Branch Filter Toggle Button
    $(document).ready(function() {
        const $branchToggleBtn = $('#branchToggleBtn');
        const $branchToggleLabel = $('#branchToggleLabel');
        const $branchFilterTypeInput = $('#branch_filter_type');

        if ($branchToggleBtn.length === 0) {
            console.warn('Branch toggle button not found!');
            return;
        }

        function updateBranchButton(mode) {
            $branchToggleBtn.attr('data-mode', mode);
            $branchFilterTypeInput.val(mode);

            if (mode === 'include') {
                $branchToggleBtn.html('<i class="mdi mdi-plus"></i>');
                $branchToggleBtn.attr('title', 'Include - Click to switch to Exclude');
                $branchToggleLabel.text('Include Branch').removeClass('mode-exclude').addClass('mode-include');
            } else {
                $branchToggleBtn.html('<i class="mdi mdi-minus"></i>');
                $branchToggleBtn.attr('title', 'Exclude - Click to switch to Include');
                $branchToggleLabel.text('Exclude Branch').removeClass('mode-include').addClass('mode-exclude');
            }
        }

        $(document).on('click', '#branchToggleBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentMode = $(this).attr('data-mode');
            const newMode = currentMode === 'include' ? 'exclude' : 'include';
            updateBranchButton(newMode);
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    google.charts.load('current', {
        'packages': ['corechart', 'bar']
    });
     google.charts.setOnLoadCallback(initChart);
       function initChart() {
        drawStatusChart(jsonData);
        drawAgencyChart(jsonData);
        drawBookingChart(jsonData);
        drawCancellationsChart(jsonData);
        drawRefusalsChart(jsonData);
        drawdetailedRefusalsChart(jsonData);
        drawdetailedCancellationsChart(jsonData);
        drawUnabletocontactChart(jsonData);
        drawCompletedChart(jsonData);
  }
</script>