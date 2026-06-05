@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
 <link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">

<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/fullcalendar/fullcalendar.min.css')}}">
 <style>
    .select2-container{
        width:100% !important
    }
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }
    </style>
<div class="main-panel main-page-box">
    <?php
    $auth = auth()->user();
    ?>
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">HHA Patient</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('add-appointment-hha-patient')
                        <a href="javascript:void(0)" class="btn btn-primary cust-right-btn"
                        onclick="addAppointment('bulk')"><i class="mdi mdi-plus"></i> Add Appointment</a>
                    @endcan
                    
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                </div>
            </div>
         </div>
         <hr />
         
         <div class="row ">
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
                                                <label for="template_type">Agency</label>
                                                    <select class="form-control" name="agency_fk1" id="agency_fk">
                                                        <option value="">Select Agency</option>
                                                
                                                        @foreach($agency_list as $agn)
                                                        <option value="{{ $agn->id}}">{{ $agn->agency_name}}</option>
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
                                                    <label for="template_type">Full Name</label>
                                                    <input type="text" name="full_name" id="full_name" class="form-control" placeHolder="Full Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Admission ID</label>
                                                    <input type="text" name="admission_id" id="admission_id" class="form-control" placeHolder="Admission ID">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Home Phone</label>
                                                    <input type="text" name="home_phone" id="home_phone" class="form-control" placeHolder="Home Phone">
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
                                                    <label for="template_type">Coordinator Name</label><br>
                                                    <input type="text" name="coordinator_name" id="coordinator_name" class="form-control" placeHolder="Coordinator Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Service Start Date</label>
                                                    <input type="text" name="service_start_date" id="service_start_date" class="form-control datepickernn" autocomplete="off" placeHolder="Service Start Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Date of Birth</label>
                                                    <input type="text" name="dob" id="dob" class="form-control datepicker" autocomplete="off" placeHolder="Date of Birth">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Last Sync Date</label>
                                                    <input type="text" name="hhasyncdatetime" id="hhasyncdatetime" class="form-control hhasyncdatetime" autocomplete="off" placeHolder="Last Sync Date">
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
                                                    <label for="template_type">Appointment Status</label>
                                                    <select class="form-control" name="status" id="status">

                                                        <option value="">All</option>
                                                        <option value="Pending">Pending</option>
                                                        <option value="Booked">Added</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search" onclick="hhaPatientAjax(1)">
                                        
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                                            class="mdi mdi-reload"></i>
                                        Reset</a>
                                        @can('hha-patient-export')
                                        <a href="javascript:void(0)" class="btn btn-info cust-right-btn"
                                            onclick="exportCsv()"><i class="mdi mdi-file"></i><span id="exportText">Export CSV</span>
                                            <span class="spinner-border spinner-border-sm d-none" id="exportLoader" role="status" aria-hidden="true"></span>
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
            <div class="col-12" >
                <div class="location-wise-data-loader shimmer_id table-responsive" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="cboxid"></th>
                                    <th>No</th>
                                    <th nowrap>Office Name</th>
                                    <th nowrap>Agency Name</th>
                                    
                                    <th nowrap>Patient Full Name<br>Gender</th>
                                    <th nowrap>Admission ID</th>
                                    <th nowrap>Home Phone</th>
                                    <th>Coordinator Name</th>
                                    <th nowrap>Service Start Date</th>
                                    <th nowrap>DOB</th>
                                    <th nowrap>Discipline</th>
                                    <th nowrap>Medicaid Number</th>
                                    <th nowrap>Medicare Number</th>
                                    <th nowrap>HHA Status</th>
                                    <th nowrap>Last Sync Date</th>
                                    <th nowrap>Status</th>
                                    
                                    <th nowrap>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="20"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="table table-responsive">
                    <span id="response_patient_list"></span>
                </div>
                
            </div>
        </div>
         
         
     </div>
     <div class="row" style='margin-top: 10%;'>
         <pre id='toastrOptions'></pre>
     </div>
     @include('hha_patient._partial.hha_patient_appointment_modal')
     @include('hha_patient._partial.hha_view_patient_detail_modal')
     @include('patient._partial.hha_module.poc.poc_view_task_modal')
     @include('include/footer')
    <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
     <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
     <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
    <script src="{{ asset('assets/vendors/fullcalendar/fullcalendar.min.js')}}"></script>

   <script type="text/javascript">
    var _HHA_PATIENT_AJAX = "{{ url('hha/hha-patient/hha-patient-ajax') }}";
    var _ADD_HHA_PATIENT = "{{ url('hha/hha-patient/add-hha-appointment-patient') }}";
    var _FETCH_HHA_PATIENT ="{{ url('/get-patient-demographics') }}"
    var _FETCH_EXISTING_PATIENT ="{{ url('hha/hha-patient/check-existing-patient-record') }}"
    var _LINK_PATIENT ="{{ url('hha/hha-patient/link-hha-patient-appointment') }}";
    var _CSRF_TOKEN = "{{ csrf_token()}}"
    var _HHA_PATIENT_EXPORT_CSV = "{{ url('hha/hha-patient/hha-patient-export-csv') }}"
    var _DATE_TIME = "{{ date('m/d/Y') }}";
    var currentPatientId = null;
    var _HHA_PATIENT_DETAIL_URL = "{{ url('hha/hha-patient/hha-demographic-detail')}}";
    var _GET_HHA_MDO_ORDER ="{{ url('hha/hha-mdo/mdo-document-list')}}";
    var _HHA_PATIENT_CALENDER_LIST = '{{ url("hha/hha-patient/calendar-visits")}}';
    var _DOWNLOAD_HHA_MD_ORDER = "{{ url('hha/hha-mdo/download-md-order-document')}}";
    var _HHA_PATIENT_DOWNLOAD_DOCUMENT = "{{ url('/hha-patient-download-doc')}}";
    </script>
      <script src="{{ asset('assets/modulejs/hha_patient/hha_patient_module.js')}}?time={{ env('timestamp')}}"></script>
      <script>
            hhaPatientAjax(1);
            loadDateAndDateRangePicker();
            </script>