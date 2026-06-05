@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/employee_dashboard.css')}}?time={{ env('timestamp')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" type="text/css" />
<style>
.appoitment{
    width: 250px !important;
}
    </style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">Employee Dashboard</h4>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <select name="user_id"  class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" id="user_id">
                                <option value="">Select User</option>
                                @foreach($userList as $val)
                                    <option value="{{ $val->id}}" @if($val->id == Auth::user()->id) selected @endif>{{ $val->first_name}}  {{ $val->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row  grid-margin stretch-card">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="loader-total-count" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <b>Select Agency:</b>
                                    <select class="form-control js-example-basic-multiple" multiple id="agency_id">
                                        <option value="">Select Agency</option>
                                       
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <b>Date Range:</b>
                                    <input class="form-control border-class" id="range_date" name="range_date" type="text" value="" placeholder="Select Date Range" />
                                </div>
                            </div>
                            &nbsp;
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-12 row  grid-margin stretch-card">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Total Booked</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_bokked">0</h4>
                                                                </div>
                                                                <div class="progress progress-md total_booked_progress">
                                                                    <div class="progress-bar bg-warning" id="total_booked_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Total Processing</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_inprogress">0</h4>
                                                                </div>
                                                                <div class="progress progress-md total_processing_progress">
                                                                    <div class="progress-bar bg-purple" id="total_processing_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Total Pending</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_pending">0</h4>
                                                                </div>
                                                                <div class="progress progress-md total_pending_progress">
                                                                    <div class="progress-bar bg-success" id="total_pending_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-6 col-xl-3 grid-margin stretch-card">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Total Completed</h4>
                                                                <div class="d-flex justify-content-between">
                                                                    <h4 class="card-title" id="total_completed">0</h4>
                                                                </div>
                                                                <div class="progress progress-md total_completed_progress">
                                                                    <div class="progress-bar bg-danger" id="total_completed_progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="notes-count" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <h6 class="card-title">Notes From Agencies</h6>
                                </div>
                                <div class="col-md-5">
                                        <b>Select Agency:</b>
                                        <select class="form-control js-example-basic-multiple" multiple id="notes_agency_id" onchange="loadNotesData();">
                                            <option value="">Select Agency</option>
                                           
                                        </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body notes-section" id="notes_section">
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="esign-loader" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="card-title">Esign Section</h6>
                                </div>
                            </div>
                        </div>
                        <div id="esign_section">
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="loader-announcement" style="display:flex" >
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
                        <div class="card-body announcements-section cvh-400" id="announcements_section">
                            <div class="col-md-6">
                                <p class="text-muted mb-1"> No records found. </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row grid-margin stretch-card">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="statistics-count" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="card-title">Statistics</h4>
                                </div>
                                <div class="col-md-2">
                                    <b>Type:</b>
                                    <select class="form-control border-class" onchange="loadStatisticData();" id="type_id">
                                        <option value="">Type</option>
                                        <option value="Caregiver">Caregiver</option>
                                        <option value="Patient">Patient</option>
                                    </select>
                                </div>
                                <div class="col-md-4 row statistic-dropdown">
                                    <b>Select Agency:</b>
                                    <select class="form-control js-example-basic-multiple" multiple onchange="loadStatisticData();" id="statistic_agency_id">
                                        <option value="">Select Agency</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="cvh table-responsive">
                            <span id="statistic">
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="task-loader-count" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <h4 class="card-title">Tasks</h4>
                                </div>
                                <div class="col-md-6">
                                    <select name="status_type" id="status_type" class="form-control border-class" onchange="loadTaskData();">
                                        <option value="all">Select Status</option>
                                        <option value="Outstanding">Outstanding</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    @can('task-add')
                                    <a href="{{ url('tasks/task-list/create') }}" target="_blank" class="btn btn-primary task-button"><i class="mdi mdi-plus"> </i>Add Task</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <div class="cvh-410 table-responsive">
                            <span class="task-section" id="task_section">
                            </span>
                        </div>
                    </div>
				</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row  grid-margin stretch-card">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="appoitment-count" style="display:flex" >
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="appoinment-today-tab" data-toggle="tab" href="#today-appoinment" role="tab"
                                        aria-controls="today-appoinment" aria-selected="false" onChange="loadTodayAppoitmentData();">Today</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="upcomming-appoinment" data-toggle="tab" href="#appoinment-upcomming" role="tab"
                                        aria-controls="appoinment-upcomming" aria-selected="true" onChange="loadUpcommingAppoitmentData();">Upcoming Appointments</a>
                                </li>
                                <li>
                                    <div class="row appoitment">
                                        <div class="col-md-12">
                                            <b>Select Agency:</b>
                                            <select class="form-control js-example-basic-multiple appoitment" multiple onChange="loadUpcommingAppoitmentData(),loadTodayAppoitmentData()" id="appoitment_agency_id">
                                                <option value="">Select Agency</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                <li>
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
@include('employeeDashboard/js_dashboard')