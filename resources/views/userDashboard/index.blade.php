@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/user_dashboard.css')}}?time={{ env('timestamp')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css')}}" type="text/css" />
<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="col-md-10">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 font-weight-bold">NyBest User Dashboard</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <input class="form-control mx-2" placeholder="Select date range" id="case_range_date" name="case_range_date" type="text" value="" style="float: left;" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row  grid-margin stretch-card">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="order-total-case-loader1" style="display:flex;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <h4 class="">Cases</h4>
                                </div>
                                
                            </div>
                            &nbsp;
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-12 row  grid-margin stretch-card">
                                            <div class="col-md-12">
                                                <div class="cus-grid">
                                                    <div class="grid-col-5">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Total Cases</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_cases">0</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid-col-5">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Pending</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_pending">0</h4>
                                                                </div>
                                                                <div class="progress progress-md total_pending_progress">
                                                                    <div class="progress-bar bg-success" id="total_pending_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid-col-5">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Completed</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_completed">0</h4>
                                                                </div>
                                                                <div class="progress progress-md total_completed_progress">
                                                                    <div class="progress-bar bg-danger" id="total_completed_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid-col-5">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Booked</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_booked">0</h4>
                                                                </div>
                                                                <div class="progress progress-md total_booked_progress">
                                                                    <div class="progress-bar bg-warning" id="total_booked_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid-col-5">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Processing</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_processing">0</h4>
                                                                </div>
                                                                <div class="progress progress-md total_processing_progress">
                                                                    <div class="progress-bar bg-purple" id="total_processing_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row  grid-margin stretch-card">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-body">
                        <div class="order-total-case-loader1" style="display:flex;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-12 row  grid-margin stretch-card">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Total Patients</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_patient">0</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Total Caregivers</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_caregiver">0</h4>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Total Agencies</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_agencies">0</h4>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">HHA</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title">Caregiver</h4>
                                                                    <h4 class="card-title" id="total_hha_caregiver">0</h4>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title">Patient</h4>
                                                                    <h4 class="card-title" id="total_hha_patient">0</h4>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php 
                                                    /* <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Alayacare</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title">Employee</h4>
                                                                    <h4 class="card-title" id="total_employee">0</h4>
                                                                </div>

                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title">Client</h4>
                                                                    <h4 class="card-title" id="total_client">0</h4>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div> */
                                                    @endphp
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Remote Client</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_remote_client">0</h4>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Visiting Aids</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_visiting_aids">0</h4>
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
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card">
                    <div class="card-body">
                            <div class="notes-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="card-title">Notes</h6>
                                    </div>
                                </div>
                                <div class="col-md-9 row">
                                    <div class="col-md-3">
                                        <label> <b>Select Type:</b> </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="notes_type" onchange="loadNotesData();">
                                            <option value="Agency">Agency</option>
                                            <option value="Nybest">Nybest User</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body notes-section mb-0" id="notes_section">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-7 grid-margin stretch-card">
                <div class="card">
                        <div class="card-body">
                            <div class="loaction-order-listing-loader1" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h6>Location Wise Appointment Data</h6>
                                </div>
                                <div class="col-md-3">
                                    <b>Type:</b>
                                    <select class="form-control border-class" id="agency_type_id" onchange="drawAgencyDataChart();">
                                        <option value="">Type</option>
                                        <option value="Caregiver">Caregiver</option>
                                        <option value="Patient">Patient</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <b>Select Agency:</b>
                                    <select placeholder="select agency" style="padding-left:50px" class="form-control js-example-basic-multiple" multiple id="location_agency_id">
                                        <option value="">Select Agency</option>
                                        @foreach($agencyList as $agn)
                                            <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            &nbsp;
                            <div id="stock_meterial_chart" width="900" height="400"></div>
                            <div id="agency-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="order-status-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <h6>Services Wise Status Data</h6>
                                </div>
                                <div class="col-md-7">
                                    <b>Select Agency:</b>
                                    <select class="form-control js-example-basic-multiple" id="status_agency_id" multiple onchange="drawStatusDataChart()">
                                        @foreach($agencyList as $agn)
                                        <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="statusChartNew" style="width: 500px; height: 500px;"></div>
                            <div id="status-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="service-order-listing-loader1" style="display:flex;" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h6 class="card-title">Services Wise Appointment Data</h6>
                                </div>
                                <div class="col-md-2">
                                    <b>Type:</b>
                                    <select class="form-control border-class" id="type_id" onchange="loadPieChartNew();loadServices()">
                                        <option value="">Type</option>
                                        <option value="Caregiver">Caregiver</option>
                                        <option value="Patient">Patient</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <b>Location:</b>
                                    <select class="form-control border-class" id="location_id" onchange="loadPieChartNew()">
                                        <option value="">Location</option>
                                        @foreach($location_list as $loc)
                                            <option value="{{ $loc->id}}">{{ $loc->location_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <b>Select Agency:</b>
                                    <select class="form-control js-example-basic-multiple" id="agency_id" multiple onchange="loadPieChartNew()">
                                        @foreach($agencyList as $agn)
                                            <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2" id="service_id" onchange="loadPieChartNew()">
                                    <b>Services:</b>
                                    <select class="form-control border-class" id="service_id1">
                                        <option value="">Services</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                <div id="pieChartNew" style="width: 500px; height: 500px;margin-top:5px"></div>
                                </div>
                                <div class="col-md-6">
                                <div id="pieChartNew1" style="margin-top:5px"></div>
                                </div>
                            </div>
                            
                            
                            <!-- <canvas id="pieChartNew" width="400" height="200" style="display: block;" class="chartjs-render-monitor"></canvas> -->
                            <div id="service-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="agency-order-listing-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <h6 class="card-title">Agency Wise Data</h6>
                                </div>
                                <div class="col-sm-7">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <b>Type:</b>
                                            <select class="form-control border-class" id="location_type_id" onchange="loadLocationChart();">
                                                <option value="">Type</option>
                                                <option value="Caregiver">Caregiver</option>
                                                <option value="Patient">Patient</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <b>Select Agency:</b>
                                            <select class="form-control js-example-basic-multiple" id="location_ageancy_id" multiple onchange="loadLocationChart();">
                                                @foreach($agencyList as $agn)
                                                    <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3"> 
                                            <b>Location:</b>
                                            <select class="form-control border-class" id="loc_id" onchange="loadLocationChart();">
                                                <option value="">Location</option>
                                                @foreach($location_list as $loc) 
                                                    <option value="{{ $loc->id}}">{{ $loc->location_name}}</option>
                                                @endforeach
                                            </select>  
                                        </div>
                                    </div>

                                </div>
                            </div>
                            &nbsp;
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="barChartCanvas"></div>
                                    <div id="service-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                        <div class="appoitment-listing-loader1" style="display:flex">
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
        </div>

    </div>

    @include('include/footer')
    @include('userDashboard/js_dashboard')