@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/task_dashboard.css')}}?time={{ env('timestamp')}}">
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
                        <h4 class="mb-0 font-weight-bold">Task Dashboard</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <input class="form-control mx-2" placeholder="Select date range" id="range_date" name="range_date" type="text" value="" style="float: left;" />
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
                                                            <h6 class="text-muted ml-3 mt-2">Total Task</h6>
                                                            <h4 class="card-title mt-4" style="margin-left: -62px;" id="total_task">0</h4>    
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
                                                <div class="card" style="background-color: #FFE3F1;">
                                                    <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="icon-container">
                                                            <i class="fa fa-exclamation-triangle" style="background: #f10075;"></i>
                                                        </div>
                                                        <h6 class="text-muted ml-3 mt-2">Urgent</h6>
                                                        <h4 class="card-title mt-4" style="margin-left: -42px;" id="total_urgent">0</h4>    
                                                    </div>            
                                                    <div class="progress progress-md total_urgent_progress">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" id="total_urgent_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
                                                            <h6 class="text-muted ml-3 mt-2">Outstanding</h6>
                                                            <h4 class="card-title mt-4" style="margin-left: -76px;" id="total_outstanding">0</h4>    
                                                        </div>
                                                        <div class="progress progress-md total_outstanding_progress">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" id="total_outstanding_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid-col-5">
                                                <div class="card" style="background-color: #E7FFE8;">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="icon-container">
                                                                <i class="fa fa-check-square-o" style="background: #3bb001;"></i>
                                                            </div>  
                                                            <h6 class="text-muted ml-3 mt-2">Completed</h6>
                                                            <h4 class="card-title mt-4" style="margin-left: -68px;" id="total_completed">0</h4>    
                                                        </div>
                                                        <div class="progress progress-md total_completed_progress">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="total_completed_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-4">
                <div class="card" style="height:250px">
                        <div class="card-body">
                            <div class="priority-order-listing-loader1" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h6>Priority Wise</h6>
                                </div>
                            </div>
                            <div id="priority_donut_chart" style="margin-top:5px"></div>
                            <div id="priority-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card" style="height:250px">
                        <div class="card-body">
                            <div class="patient-order-listing-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <h6>Patients Wise Task</h6>
                                </div>
                            </div>
                            <div id="patient_view_chart"></div>
                            <div id="patient-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-12">
                <div class="card" style="height:350px">
                        <div class="card-body">
                            <div class="assignee-order-listing-loader1" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h6>Assignee Wise</h6>
                                </div>
                            </div>
                            <div id="assignee_chart" style="margin-top:5px"></div>
                            <div id="assignee-no-data" style="display: none; position: absolute;padding: 7px 48px 0px 0px;text-align: center;font-size: 30px;top: 38%;width: 100%;">Nothing to display</div>
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
                        <div class="task-listing-loader1" style="display:flex">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="table-responsive">
                                <span id="task_list"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    @include('include/footer')
    @include('taskDashboard/js_dashboard')