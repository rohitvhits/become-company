@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .actions {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Lead Coordination Report</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i> Filter <span class="active-filter"></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="full_name">Full Name</label>
                                                    <input type="text" name="full_name" class="form-control" id="full_name" placeholder="Enter Full Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="phone">Phone</label>
                                                    <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter Phone">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="agency_name">Agency Name</label>
                                                    <input type="text" name="agency_name" class="form-control" id="agency_name" placeholder="Enter Agency Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="service_requested">Service Requested</label>
                                                    <input type="text" name="service_requested" class="form-control" id="service_requested" placeholder="Enter Service">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="appointment_date_range">Appointment Date</label>
                                                    <input type="text" name="appointment_date_range" class="form-control" id="appointment_date_range" placeholder="Select Date Range" readonly>
                                                    <input type="hidden" name="appointment_date_from" id="appointment_date_from">
                                                    <input type="hidden" name="appointment_date_to" id="appointment_date_to">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="created_date_range">Created Date</label>
                                                    <input type="text" name="created_date_range" class="form-control" id="created_date_range" placeholder="Select Date Range" readonly>
                                                    <input type="hidden" name="created_date_from" id="created_date_from">
                                                    <input type="hidden" name="created_date_to" id="created_date_to">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadAjaxList(1)">
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()">
                                            <i class="mdi mdi-reload"></i> Reset
                                        </a>
                                        @can('export-lead-coordination')
                                        <a href="javascript:void(0)" class="btn btn-warning cust-right-btn" onclick="exportCSV()">
                                            <i class="mdi mdi-download"></i> Export CSV
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Full Name</th>
                                  
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Agency Name</th>
                                    <th>Service Requested</th>
                                    <th>Appointment Date</th>
                                    <th>Appointment Time</th>
                                    <th>Appointment Address</th>
                                    <th>Referral Type</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="11"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="response_requested_id"></span>
            </div>
        </div>

    </div>
    <div style="color:red" id="blank_div">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
</div>

@include('include/footer')

<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
     <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />

<script>
    var _LOAD_DATA_URL = "{{ route('lead-coordination-report.ajax-list') }}";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
    var _EXPORT_CSV = "{{ route('lead-coordination-report.export-csv') }}";
</script>

<script src="{{ asset('assets/modulejs/lead_coordination_report/lead_coordination_report.js') }}?time={{ env('timestamp') }}"></script>
