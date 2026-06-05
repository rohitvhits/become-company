@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
 <link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">

<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/fullcalendar/fullcalendar.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css')}}">

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
 </style>
<div class="main-panel main-page-box">
    <?php
    $auth = auth()->user();
    ?>
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">HHA Caregiver List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                   @if(isset($agencyDetails->id))
                       <a href="javascript:void(0)" class="btn btn-primary cust-right-btn"
                           onclick="fetchCargiver()"><i class="mdi mdi-plus"></i> Sync Caregiver</a>
                   @endif
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
                                                    <select name="" id="agency_fk" class="form-control">
                                                        <option value="">Select Agency</option>
                                                        @foreach($agencyList as $agency)
                                                            <option value="{{ sha1($agency->id) }}" @if($agency_fk == sha1($agency->id)) selected @endif>{{ $agency->agency_name }}</option>
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
                                                    <label for="template_type">Caregiver Full Name</label>
                                                   
                                                    <input type="text" name="full_name" id="full_name" class="form-control" placeHolder="Caregiver Full Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Caregiver Code</label>
                                                    <input type="text" name="code" id="code" class="form-control" value="" placeHolder="Caregiver Code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Caregiver Phone</label>
                                                    <input type="text" name="caregiver_phone" id="caregiver_phone" class="form-control" placeHolder="Caregiver Phone">
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
                                                    <label for="template_type">Last Work Date</label>
                                                    <input type="text" readonly name="last_work_date" id="last_work_date" class="form-control datepickernn"  autocomplete="off" placeHolder="Last Work Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Date of Birth</label>
                                                    <input type="text" name="dob" id="dob" class="form-control datepickernn_dob"  autocomplete="off" placeHolder="Date of Birth" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Gender</label>
                                                    <select class="form-control" name="gender" id="gender">
                                                        <option value="">Select Gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                   
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">HHA Sync DateTime</label>
                                                    <input type="text" readonly name="hhasyncdatetime" id="hhasyncdatetime" class="form-control datepickernn_sync"  autocomplete="off" placeHolder="HHA Sync DateTime">
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
                                            value="Search" onclick="hhaCaregiverList(1)">

                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                                            class="mdi mdi-reload"></i>
                                        Reset</a>

                                        <a href="javascript:void(0)" class="btn btn-info cust-right-btn"
                                            onclick="hhaCaregiverExport()"><i class="mdi mdi-file"></i><span id="exportText">Export CSV</span>
                                            <span class="spinner-border spinner-border-sm d-none" id="exportLoader" aria-hidden="true"></span>
                                        </a>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 grid-margin-top">
            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('success') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('error') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12" >
                <input type="hidden" id="sortingColumn" value="id">
                <input type="hidden" id="sortingOrder" value="desc">
                <div class="location-wise-data-loader shimmer_id table-responsive" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="white-space:nowrap">Agency Name</th>
                                    <th style="white-space:nowrap">Caregiver Full Name</th>
                                    <th style="white-space:nowrap">Caregiver Code</th>
                                    <th style="white-space:nowrap">Caregiver Phone</th>
                                    <th>DOB</th>
                                    <th style="white-space:nowrap">Gender</th>
                                    <th style="white-space:nowrap">Caregiver Status</th>
                                    <th style="white-space:nowrap">Language</th>
                                    <th style="white-space:nowrap">Discipline</th>
                                    <th style="white-space:nowrap">First Work Date</th>
                                    <th style="white-space:nowrap">Last Work Date</th>
                                    <th style="white-space:nowrap">HHA Sync DateTime</th>
                                    <th style="white-space:nowrap">Action</th>
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
                    <span id="resp"></span>
                </div>

            </div>
        </div>


        <!-- Caregiver View Modal -->
       
     </div>
     <div class="row" id="blank_div_id" style='margin-top: 10%;'>
         <pre id='toastrOptions'></pre>
     </div>
     @include('hha_caregiver._partial.hha_caregiver_view_modal')
     @include('include/footer')

     <script src="{{ asset('js/jquery.min.js')}}"></script>

     <script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
     <script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
     <link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
     <script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
     <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
     <script src="{{ asset('assets/js/select2.js')}}"></script>
     <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
     <script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
     <script src="{{ asset('assets/vendors/fullcalendar/fullcalendar.min.js')}}"></script>
  
    <script>
        var _HHA_CAREGIVER_LIST = "{{ url('hha-caregiver-ajax')}}"
        var _SHA1_AGENCY_ID = "{{ $agency_id}}";
        var _AGENCY_ID ="@if(isset($agencyDetails->id)) {{ $agencyDetails->id}} @endif";
        var _AGENCYID = _AGENCY_ID;
        var _HHA_FETCH_CAREGIVER = "{{ url('fetch-caregiver')}}";
        var _HHA_CAREGIVER_EXPORT_CSV = "{{ url('hha-patient-export-csv') }}"
        var _DATE_TIME = "{{ date('m/d/Y') }}"
        var _HHA_CAREGIVER_DETAIL_URL = "{{ url('hha/hha-caregiver/demographic-detail') }}";
        var _HHA_CAREGIVER_CALENDAR_URL = "{{ url('hha/hha-caregiver/calendar-visits') }}";
        var _HHA_CAREGIVER_DOWNLOAD_DOCUMENT = "{{ url('hha-caregiver-download-doc')}}";
        var currentCaregiversId = null; // Store current caregiver ID
        var agencyId = null;
        var loadedTabs = {}; // Track which tabs have been loaded
        var _HHA_CALENDER_LIST="{{ url('hha/hha-caregiver/calendar-visits') }}";
        $(":input").inputmask();
        $("#filter-btn").click(function() {
            $("#search-filter-btn").slideToggle(600);
        });
        
    </script>
<script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/modulejs/hha_caregiver.js')}}?time={{ env('timestamp')}}"></script>
    <script src="{{ asset('assets/modulejs/hha_module.js')}}?time={{ env('timestamp')}}"></script>
    <script src="{{ asset('assets/modulejs/hha_exchange/hha_exchange.js')}}?time={{ env('timestamp')}}"></script>