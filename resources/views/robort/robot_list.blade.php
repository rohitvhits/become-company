@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/css/robort_focus.css')}}">
<div class="main-panel main-page-box">
    <?php
    $auth = auth()->user();
    ?>
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Remote Focus List (<span id="appointment_id"></span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('add-robort-appointment')
                    <a href="javascript:void(0)" class="btn btn-primary cust-right-btn"
                         onclick="addAppointment('','multiple')"><i class="mdi mdi-plus"></i> Add Appointment</a>
                    @endcan

                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
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
                                                    <label for="agency_id">Agency</label>
                                                    <select name="agency_id" class="form-control" id="agency_id">
                                                        <option value="">Select Agency</option>
                                                        @foreach($agencyList as $val)
                                                        <option value="{{ $val->id}}">{{ $val->agency_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="full_name">Full Name</label>
                                                    <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Full Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="dob">Date of Birth</label>
                                                    <input type="text" name="code" id="dob" class="form-control dob" autocomplete="off" placeholder="Date of Birth">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="gender">Gender</label>
                                                    <select name="gender" class="form-control" id="gender">
                                                        <option value="">Select Gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="patient_status">Patient Status</label>
                                                    <select name="patient_status" class="form-control" id="patient_status">
                                                        <option value="">Select Patient Status</option>
                                                        <option value="1">Pending</option>
                                                        <option value="2">Pre-Active</option>
                                                        <option value="3">Active</option>
                                                        <option value="4">ON Hold</option>
                                                        <option value="5">Discharged</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="status">Appointment Status</label>
                                                    <select class="form-control" name="status" id="status">
                                                        <option value="">All</option>
                                                        <option value="Pending">Pending</option>
                                                        <option value="Booked">Added</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="due_date">Created Date</label>
                                                    <input type="text" name="due_date" id="due_date" class="form-control datepickernn" autocomplete="off" placeholder="Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="robortList(1)">

                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i class="mdi mdi-reload"></i> Reset</a>
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
                <input type="hidden" id="appointment_type" value="">
                <input type="hidden" id="appointment_ids" value="">

                <div class="location-wise-data-loader shimmer_id table-responsive">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="cboxid"></th>
                                    <th>No</th>
                                    <th style="white-space:nowrap">Agency Name</th>
                                    <th style="white-space:nowrap">Patient ID</th>
                                    <th style="white-space:nowrap">Full Name</th>
                                    <th style="white-space:nowrap">Date of Birth</th>
                                    <th style="white-space:nowrap">Gender</th>
                                    <th style="white-space:nowrap">Patient Status</th>
                                    <th style="white-space:nowrap">Appointment Status</th>
                                    <th style="white-space:nowrap">Created Date</th>
                                    <th style="white-space:nowrap">Action</th>
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

                <div class="table table-responsive">
                    <span id="resp"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    
</div>

@include('robort._partial.modal.add_remote_appointment')
@include('robort._partial.modal.upload_remote_document')
@include('include/footer')

<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script src="{{ asset('assets/modulejs/remote_focus/remote_focus.js')}}?time={{ time()}}"></script>
<script type="text/javascript">
    var _REMOTE_AJAX_LIST = "{{ url('remote/robort-ajax-list') }}";
    var _REMOTE_SERVICES = "{{ URL('ajax-service-with-json')}}";
    var _REMOTE_ADD_APPOINTMENT = "{{ url('remote/add-appointment-robort') }}";
    var _REMOTE_LOAD_DICIPLINE = "{{ url('remote/load-hha-dicipline')}}";
    var _REMOTE_UPLOAD_DOCUMENT = "{{ url('remote/upload-remote-document')}}";
    var _CSRF_TOKEN = "{{ csrf_token()}}";

</script>