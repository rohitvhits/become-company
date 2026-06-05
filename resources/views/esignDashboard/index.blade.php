@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/esign_dashboard.css')}}?time={{ env('timestamp')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css')}}" type="text/css" />
<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex mb-2">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 font-weight-bold">Esign Dashboard</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex align-items-center">
                        <input class="form-control" placeholder="Select date range" id="range_date" name="range_date" type="text" value="" style="float: left;" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex align-items-center">
                        <select class="form-control border-class" id="type_id">
                            <option value="">Type</option>
                            <option value="Caregiver">Caregiver</option>
                            <option value="Patient">Patient</option>
                        </select>
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
                                    <div class="col-md-12 row grid-margin stretch-card">
                                        <div class="col-md-12">
                                            <div class="cus-grid">
                                                <div class="grid-col-5">
                                                    <div class="card" style="background-color:rgb(180, 233, 243);">
                                                        <div class="card-body">
                                                            <div class="d-flex">
                                                                <div class="icon-container">
                                                                    <i class="fa fa-tasks" style="background: #00BBE0;"></i>
                                                                </div>
                                                                <h6 class="text-muted ml-3 mt-2">Total Esign</h6>
                                                                <h4 class="card-title mt-4" style="margin-left: -69px;" id="total_esign">0</h4>    
                                                            </div>            
                                                            <div class="progress progress-md total_progress">
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" id="total_progress" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid-col-5">
                                                    <div class="card" style="background-color: rgb(242, 220, 248);">
                                                        <div class="card-body">
                                                            <div class="d-flex">
                                                                <div class="icon-container">
                                                                    <i class="fa fa-tasks" style="background: #9d00cc;"></i>
                                                                </div>
                                                                <h6 class="text-muted ml-3 mt-2">Pending</h6>
                                                                <h4 class="card-title mt-4" style="margin-left: -54px;" id="total_pending">0</h4>    
                                                            </div>            
                                                            <div class="progress progress-md total_pending_progress">
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-purple" id="total_pending_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid-col-5">
                                                    <div class="card" style="background-color: #E7FFE8;">
                                                        <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="icon-container">
                                                                <i class="fa fa-exclamation-triangle" style="background: #3bb001;"></i>
                                                            </div>
                                                            <h6 class="text-muted ml-3 mt-2">Completed</h6>
                                                            <h4 class="card-title mt-4" style="margin-left: -69px;" id="total_completed">0</h4>    
                                                        </div>            
                                                        <div class="progress progress-md total_completed_progress">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="total_completed_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid-col-5">
                                                    <div class="card" style="background-color: #fdd9bc;">
                                                        <div class="card-body">
                                                            <div class="d-flex">
                                                                <div class="icon-container">
                                                                    <i class="fa fa-outdent" style="background: #f29d56;"></i>
                                                                </div>
                                                                <h6 class="text-muted ml-3 mt-2">Approved</h6>
                                                                <h4 class="card-title mt-4" style="margin-left: -60px;" id="total_approved">0</h4>    
                                                            </div>
                                                            <div class="progress progress-md total_approved_progress">
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" id="total_approved_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid-col-5">
                                                    <div class="card" style="background-color: #FFE3F1;">
                                                        <div class="card-body">
                                                            <div class="d-flex">
                                                                <div class="icon-container">
                                                                    <i class="fa fa-check-square-o" style="background: #f10075;"></i>
                                                                </div>  
                                                                <h6 class="text-muted ml-3 mt-2">Rejected</h6>
                                                                <h4 class="card-title mt-4" style="margin-left: -55px;" id="total_rejected">0</h4>    
                                                            </div>
                                                            <div class="progress progress-md total_rejected_progress">
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" id="total_rejected_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
            <!-- <div class="row">
                <div class="col-md-12 row grid-margin stretch-card">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="order-total-case-loader1" style="display:flex;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <h4 class="">Esign</h4>
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
                                                                    <h4 class="card-title">Total</h4>
                                                                    <div class="d-flex justify-content-between">
                                                                        <h4 class="card-title" id="total_esign">0</h4>
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
                                                                        <div class="progress-bar bg-warning" id="total_pending_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
                                                                        <div class="progress-bar bg-success" id="total_completed_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="grid-col-5">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h4 class="card-title">Approved</h4>
                                                                    <div class="d-flex justify-content-between">
                                                                        <h4 class="card-title" id="total_approved">0</h4>
                                                                    </div>
                                                                    <div class="progress progress-md total_approved_progress">
                                                                        <div class="progress-bar bg-danger" id="total_approved_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="grid-col-5">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h4 class="card-title">Rejected</h4>
                                                                    <div class="d-flex justify-content-between">
                                                                        <h4 class="card-title" id="total_rejected">0</h4>
                                                                    </div>
                                                                    <div class="progress progress-md total_rejected_progress">
                                                                        <div class="progress-bar bg-red" id="total_rejected_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
            </div> -->
            <div class="row">
                <div class="col-md-6 row grid-margin stretch-card">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="service-order-listing-loader1" style="display:flex;" >
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <h6 class="card-title">Esign status wise</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <b>Location:</b>
                                        <select class="form-control border-class" id="location_id" onchange="drawStatusChart();">
                                            <option value="">Location</option>
                                            @foreach($location_list as $loc)
                                                <option value="{{ $loc->id}}">{{ $loc->location_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <b>Select Agency:</b>
                                        <select class="form-control js-example-basic-multiple" id="agency_id" multiple onchange="drawStatusChart();">
                                            @foreach($agencyList as $agn)
                                                <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="pieChartNew" style="width: 400px; height: 300px;margin-top:5px"></div>
                                    </div>
                                </div>
                                <div id="service-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 row grid-margin stretch-card">
                    <div class="col-xl-12">
                        <div class="card" style="width: 103%;height: 100%;">
                            <div class="card-body">
                                <div class="templete-usage-loader1" style="display:flex;" >
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="card-title">Template usage</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="columnChart" style="width: 400px; height: 300px;margin-top:5px"></div>
                                    </div>
                                </div>
                                <div id="template-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 row grid-margin stretch-card">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="reviewed-esign-loader1" style="display:flex;" >
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <h6 class="card-title">Reviewed Usage</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="reviewChart" style="width: 400px; height: 300px;margin-top:5px"></div>
                                    </div>
                                </div>
                                <div id="review-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 row grid-margin stretch-card">
                    <div class="col-xl-12">
                        <div class="card" style="width: 103%;height: 100%;">
                            <div class="card-body">
                                <div class="created-esign-loader1" style="display:flex;" >
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="card-title">Created usage</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="createChart" style="width: 400px; height: 300px;margin-top:5px"></div>
                                    </div>
                                </div>
                                <div id="created-no-data" style="display: none; position: absolute;padding: 50px 0;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
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
                            <div class="esign-listing-loader1" style="display:flex">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <h4 class="">Today Esign Data</h4>
                                    </div>
                                </div>
                                &nbsp;
                                <div class="table-responsive">
                                    <span id="esign_data">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('include/footer')
    @include('esignDashboard/js_dashboard')