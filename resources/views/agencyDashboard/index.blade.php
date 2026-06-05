@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/agency_dashboard.css')}}?time={{ time()}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" type="text/css"/>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">Agency Dashboard</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="total-listing-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-xl-12 grid-margin stretch-card">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4 class="card-title">Total Caregiver</h4>
                                                    <div class="d-flex justify-content-between">
                                                        <h4 class="card-title" id="total_caregiver">0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-xl-12 grid-margin stretch-card">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4 class="card-title">Total Patients</h4>
                                                    <div class="d-flex justify-content-between">
                                                        <h4 class="card-title" id="total_patients">0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="all-notes-loader" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="card-title">Notes</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body notes-section" id="notes_section">
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="nybest-notes-loader" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="card-title">Notes From NYBest</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body notes-section" id="notes_ny_best_section">
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="announcement-loader" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="card-title">Announcements</h6>
                                </div>
                                <div class="col-md-6">
                                    <b>Date Range:</b>
                                    <input class="form-control border-class" id="announcement_range_date" name="announcement_range_date" type="text" value="" placeholder="Select Date Range" />
                                </div>
                            </div>
                        </div>
                        <div class="card-body announcements-section" id="announcements_section">
                            <div class="col-md-6">
                                <p class="text-muted mb-1"> No records found. </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row  grid-margin stretch-card">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="statistics-loader" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="card-title">Statistics</h6>
                                </div>
                                <div class="col-md-4">
                                    <b>Type:</b>
                                    <select class="form-control border-class" onchange="loadStatisticData();" id="type_id">
                                        <option value="">Type</option>
                                        <option value="Caregiver">Caregiver</option>
                                        <option value="Patient">Patient</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="cvh-300 table-responsive">
                            <span id="statistic">
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="location-loader" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="card-title">Locations</h6>
                                </div>
                                <div class="col-md-4">
                                    <b>Location:</b>
                                    <select class="form-control border-class" onchange="loadLocationsData();" id="location_id">
                                        <option value="">Location</option>
                                        @foreach($locationList as $key => $loc)
                                        @php $selected = '' @endphp
                                            <option {{$selected}} value="{{ $loc->city}}">{{ $loc->city}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive location-section">
                            <span id="location">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 row  grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="appoitment-loader" style="display:flex">
                        <i class="fa fa-spinner fa-spin"></i>
                    </div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="appoinment-today-tab" data-toggle="tab" href="#today-appoinment" role="tab"
                                aria-controls="today-appoinment" aria-selected="false" onclick="loadTodayAppoitmentData();">Today</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="upcomming-appoinment" data-toggle="tab" href="#appoinment-upcomming" role="tab"
                                aria-controls="appoinment-upcomming" aria-selected="true" onclick="loadUpcommingAppoitmentData();">Upcoming Appointments</a>
                        </li>
                    </div>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="today-appoinment" role="tabpanel"
                            aria-labelledby="appoinment-today-tab">
                            <div class="table-responsive">
                                <span id="today_appoinment">

                                </span>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="appoinment-upcomming" role="tabpanel"
                            aria-labelledby="upcomming-appoinment">
                            <div class="table-responsive">
                                <span id="upcomming_appoinment">

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('include/footer')
    @include('agencyDashboard/js_dashboard')