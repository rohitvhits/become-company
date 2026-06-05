@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/appointment_dashboard.css')}}?time={{ env('timestamp')}}">
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
                        <h4 class="mb-0 font-weight-bold">Appointment Dashboard</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-9">
                    <div class="card" style="height:280px">
                        <div class="card-body">
                            <div class="order-total-case-loader1" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Statistics</h5>
                                </div>
                            </div>
                            
                            <div class="cus-grid">
                                <div class="grid-col-4">
                                    <div class="card" style="background-color:rgb(242, 220, 248);">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="icon-container">
                                                    <i class="fa fa-calendar" style="background: #9d00cc;"></i>
                                                </div>
                                                <h6 class="text-muted ml-3 mt-2">Total Appointment</h6>
                                                <a href="{{url('appointment')}}" style="text-decoration:none;" target="_blank"><h4 class="card-title mt-4" style="margin-left: -113px;" id="total_appointment">0</h4></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="grid-col-4">
                                    <div class="card" style="background-color: #FFE3F1;">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="icon-container">
                                                    <i class="fa fa-list" style="background: #f10075;"></i>
                                                </div>
                                                <h6 class="text-muted ml-3 mt-2">Total Caregivers</h6>
                                                <a href="{{url('appointment')}}?type=caregiver" style="text-decoration:none;" target="_blank"><h4 class="card-title mt-4" style="margin-left: -100px;" id="total_caregiver">0</h4></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid-col-4">
                                    <div class="card" style="background-color:#fdd9bc;">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="icon-container">
                                                    <i class="fa fa-outdent" style="background: #f29d56;"></i>
                                                </div>
                                                <h6 class="text-muted ml-3 mt-2">Total Patients</h6>
                                                <a href="{{url('appointment')}}?type=patient" style="text-decoration:none;" target="_blank"><h4 class="card-title mt-4" style="margin-left: -85px;" id="total_patient">0</h4></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="grid-col-4">
                                    <div class="card" style="background-color:#E7FFE8;">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="icon-container">
                                                    <i class="fa fa-align-justify" style="background: #3bb001;"></i>
                                                </div>
                                                <h6 class="text-muted ml-3 mt-2">Total Agencies</h6>
                                                <a href="{{url('agency')}}" style="text-decoration:none;" target="_blank"><h4 class="card-title mt-4" style="margin-left: -91px;" id="total_agencies">0</h4></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid-col-4">
                                    <div class="card" style="background-color:rgb(168, 200, 243);">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="icon-container">
                                                    <i class="fa fa-check-square-o" style="background:rgb(1, 77, 176);"></i>
                                                </div>
                                                <h6 class="text-muted ml-3 mt-2">Total HHACaregiver</h6>
                                                <a href="{{url('hha-caregiver-list')}}" style="text-decoration:none;" target="_blank"><h4 class="card-title mt-4" style="margin-left: -123px;" id="total_hha_caregivers">0</h4></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid-col-4">
                                    <div class="card" style="background-color:rgb(169, 230, 230);">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="icon-container">
                                                    <i class="fa fa-reorder" style="background: #008080;"></i>
                                                </div>
                                                <h6 class="text-muted ml-3 mt-2">Total HHAPatient </h6>
                                                <a href="{{url('hha-patient')}}" style="text-decoration:none;" target="_blank"><h4 class="card-title mt-4" style="margin-left: -108px;" id="total_hha_patients">0</h4></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid-col-4">
                                    <div class="card" style="background-color:rgb(229, 241, 207);">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="icon-container">
                                                    <i class="fa fa-check-square" style="background: #556B2F;"></i>
                                                </div>
                                                <h6 class="text-muted ml-3 mt-2">Total Remote Client</h6>
                                                <a href="{{url('remote/remote-list')}}" style="text-decoration:none;" target="_blank"><h4 class="card-title mt-4" style="margin-left: -124px;" id="total_remote_clients">0</h4></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid-col-4">
                                    <div class="card" style="background-color:rgb(180, 233, 243);">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="icon-container">
                                                    <i class="fa fa-tasks" style="background: #00BBE0;"></i>
                                                </div>
                                                <h6 class="text-muted ml-3 mt-2">Total Visting Aids</h6>
                                                <a href="{{url('third-party-patient')}}" style="text-decoration:none;" target="_blank"><h4 class="card-title mt-4" style="margin-left: -109px;" id="total_visiting_aids">0</h4></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card" style="height:280px">
                        <div class="card-body">
                            <div class="services-order-listing-loader1" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Popular Services</h5>
                                </div>
                            </div>
                            <div id="services_chart" style="margin-top:5px"></div>
                            <div id="services-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-4">
                <div class="card" style="height:330px">
                        <div class="card-body">
                            <div class="status-order-listing-loader1" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Status Overview</h5>
                                </div>
                            </div>
                            <div id="status_donut_chart" style="margin-top:5px"></div>
                            <div id="status-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card" style="height:330px">
                        <div class="">
                            <div class="appointment-order-listing-loader1" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                        <div class="row">
                            <div class="col-md-12" style="margin-left: 26px;">
                            <ul class="nav nav-pills nav-pills-custom" id="pills-tab-custom">
                                <li class="nav-item">
                                <a class="nav-link active" id="pills-home-tab-custom" data-toggle="pill" href="#pills-health" onclick="loadAgencyAppoitmentData();" role="tab" aria-controls="pills-home" aria-selected="true">
                                    Popular Agency
                                </a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab-custom" data-toggle="pill" href="#pills-career" role="tab" onclick="loadUserAppoitmentData();" aria-controls="pills-profile" aria-selected="false">
                                    Popular Users
                                </a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" id="pills-contact-tab-custom" data-toggle="pill" href="#pills-music" role="tab" aria-controls="pills-contact" onclick="loadLocationAppoitmentData();" aria-selected="false">
                                    Popular Locations
                                </a>
                                </li>
                            </ul>
                            <div class="tab-content tab-content-custom-pill" id="pills-tabContent-custom" style="margin-left: 13px;margin-top: -38px;">
                                <div class="tab-pane fade show active" id="pills-health" role="tabpanel" aria-labelledby="pills-home-tab-custom">
                                    <div class="" id="popular_agency">

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-career" role="tabpanel" aria-labelledby="pills-profile-tab-custom">
                                    <div class="" id="popular_user"></div>
                                </div>
                                <div class="tab-pane fade" id="pills-music" role="tabpanel" aria-labelledby="pills-contact-tab-custom">
                                    <div class="" id="popular_location"></div>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card" style="height:330px">
                        <div class="card-body">
                            <div class="monthly-comparision-order-listing-loader1" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <h5>Comparison Appointment Data with Previous Year</h5>
                                </div>
                            </div>
                            <div id="monthly_comparision_view_chart" style="margin-top:5px"></div>
                            <div id="monthly-comparision-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-12">
                    <div class="card" style="height:400px">
                        <div class="card-body">
                            <div class="user-order-listing-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <h5>Users Overview</h5>
                                </div>
                                <div class="col-md-2">
                                    <input class="form-control mx-2" placeholder="Select date range" id="user_range_date" name="user_range_date" type="text" value="{{$dateRange}}" style="float: left;" />
                                </div>
                            </div>
                            <div id="user_view_chart"></div>
                            <div id="user-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-12">
                    <div class="card" style="height:450px">
                        <div class="card-body">
                            <div class="agency-order-listing-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <h5>Agency Overview</h5>
                                </div>
                                <div class="col-md-2">
                                    <input class="form-control mx-2" placeholder="Select date range" id="agency_range_date" name="agency_range_date" type="text" value="{{$dateRange}}" style="float: left;" />
                                </div>
                            </div>
                            <br/>
                            <div id="agency_view_chart"></div>
                            <div id="agency-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-6">
                    <div class="card" style="height:400px">
                        <div class="card-body">
                            <div class="patient-monthly-order-listing-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Appointment Creation</h5>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="patient_type" id="patient_type" onchange="drawPatientMonthlyChart(),showHide()">
                                        <option value="yearly" selected>Yearly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="weekly">Weekly</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="patient_year" id="patient_year" onchange="drawPatientMonthlyChart(),showHide()">
                                        <option value="">Select Year</option>
                                        @if(!empty($yearData))
                                            @foreach($yearData as $year)
                                                <option value="{{$year}}" @if($year == date('Y')) @endif>{{$year}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control d-none" name="patient_month" id="patient_month" onchange="drawPatientMonthlyChart(),showHide();">
                                        <option value="">Selcet Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control d-none" name="patient_week" id="patient_week" onchange="drawPatientMonthlyChart();">
                                        <option value="">Selcet Week</option>
                                        <option value="1-7">Week 1</option>
                                        <option value="8-14">Week 2</option>
                                        <option value="15-21">Week 3</option>
                                        <option value="22-28">Week 4</option>
                                        <option value="29-31">Week 5</option>
                                    </select>
                                </div>
                                
                            </div>
                            <br/>
                            <div id="patient_monthly_view_chart"></div>
                            <div id="patient-monthly-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card" style="height:400px">
                        <div class="card-body">
                            <div class="location-order-listing-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Locations Overview</h5>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control mx-2" placeholder="Select date range" id="location_range_date" name="location_range_date" type="text" value="{{$dateRange}}" style="float: left;" />
                                </div>
                            </div>
                            <div id="location_view_chart"></div>
                            <div id="location-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    @include('include/footer')
    @include('appointmentDashboard/js_dashboard')