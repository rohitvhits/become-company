@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/user_dashboard.css')}}?time={{ env('timestamp')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css')}}" type="text/css" />
<style>
.order-listing-loader1 {
    position: absolute;
    left: 0;
    top: 0;
    background: rgb(11 11 11 / 20%);
    bottom: 0;
    right: 0;
    width: 100%;
    font-size: 30px;
    align-items: center;
    justify-content: center;
    z-index: 999;
    display: none;

}

.order-total-case-loader1 {
    position: absolute;
    left: 0;
    top: 0;
    background: rgb(11 11 11 / 20%);
    bottom: 0;
    right: 0;
    width: 100%;
    font-size: 30px;
    align-items: center;
    justify-content: center;
    z-index: 999;
    display: none;

}
.order-status-loader1 {
    position: absolute;
    left: 0;
    top: 0;
    background: rgb(11 11 11 / 20%);
    bottom: 0;
    right: 0;
    width: 100%;
    font-size: 30px;
    align-items: center;
    justify-content: center;
    z-index: 999;
    display: none;

}
.show{
    display: flex;
}
#location_agency_id, #agency_type_id {
  display: block !important;
  visibility: visible !important;
}
    </style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">User dashboard</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row  grid-margin stretch-card">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="order-total-case-loader1" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <h4 class="">Cases</h4>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-control mx-2 col-md-4" id="case_range_date" name="case_range_date" type="text" value="" style="float: left;" />
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
                        <div class="order-total-case-loader1" >
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

                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
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
                                                    </div>
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
                        
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="card-title">Notes</h6>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control" id="notes_type" onchange="loadNotesData();">
                                        <option value="Agency">Agency</option>
                                        <option value="Nybest">Nybest User</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body notes-section" id="notes_section">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-xl-12">
                
                    
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                        <div class="card-body">
                            <div class="order-listing-loader1" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h6>Agency Data</h6>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="agency_type_id" onchange="drawAgencyDataChart();">
                                        <option value="">Type</option>
                                        <option value="Caregiver">Caregiver</option>
                                        <option value="Patient">Patient</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <select class="form-control js-example-basic-multiple" multiple id="location_agency_id">
                                                @foreach($agencyList as $agn)
                                                <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            &nbsp;
                            <div id="stock_meterial_chart" width="900" height="500"></div>
                            <div id="agency-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="order-status-loader1" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h6>Status</h6>
                                </div>
                                <div class="col-md-8">
                                    <!-- <div class="row"> -->
                                    <div class="col-md-8" style="float: right;">
                                        <select class="form-control js-example-basic-multiple" id="status_agency_id" multiple onchange="drawStatusDataChart()">
                                            @foreach($agencyList as $agn)
                                            <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- </div> -->
                                </div>
                            </div>
                            <div id="statusChartNew" style="width: 900px; height: 500px;margin-top:20px"></div>
                            <!-- <canvas id="statusChart" width="400" height="200" style="display: block;" class="chartjs-render-monitor"></canvas> -->
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
                            <h6 class="card-title">Services</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control" id="type_id" onchange="loadPieChartNew();loadServices()">
                                        <option value="">Type</option>
                                        <option value="Caregiver">Caregiver</option>
                                        <option value="Patient">Patient</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="location_id" onchange="loadPieChartNew()">
                                        <option value="">Location</option>
                                        @foreach($location_list as $loc)
                                        <option value="{{ $loc->id}}">{{ $loc->location_name}}</option>

                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control js-example-basic-multiple" id="agency_id" multiple onchange="loadPieChartNew()">
                                        @foreach($agencyList as $agn)
                                        <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>

                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" id="service_id" onchange="loadPieChartNew()">
                                    <select class="form-control" id="service_id1">
                                        <option value="">Services</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                <div id="pieChartNew" style="width: 500px; height: 500px;margin-top:20px"></div>
                                </div>
                                <div class="col-md-6">
                                <div id="pieChartNew1" style="width: 500px; height: 500px;margin-top:20px"></div>
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
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="card-title">Location</h6>
                                </div>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select class="form-control" id="location_type_id" onchange="loadLocationChart();">
                                                <option value="">Type</option>
                                                <option value="Caregiver">Caregiver</option>
                                                <option value="Patient">Patient</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control js-example-basic-multiple" id="location_ageancy_id" multiple onchange="loadLocationChart();">
                                                @foreach($agencyList as $agn)
                                                <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" id="loc_id" onchange="loadLocationChart();">
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
                                <div id="barChartCanvas" style="width: 900px; height: 500px;"></div>
                                <div id="service-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
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