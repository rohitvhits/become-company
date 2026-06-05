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
            <h5 class="mb-0 font-weight-bold">HHA Other Compliance (<span id="appointment_id"></span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                @can('hha-other-compliance-add')
                    <a href="javascript:void(0)" class="btn btn-primary cust-right-btn btn-fw btn-sm" onclick="addAppointment()"><i class="mdi mdi-plus"></i> Add Appointment</a>
                    @endcan
                    
                   <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                </div>
            </div>
         </div>
         <hr />
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
                                                    <select name="agency_fk1" id="agency_fk" class="form-control">
                                                        <option value="">Select Agency</option>
                                                        @foreach($agency_list as $val)
                                                        <option value="{{ $val->id}}" >{{ $val->agency_name}}</option>
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
                                                <label for="template_type">Office</label>
                                                    <select name="office_id" id="office_id_other" class="form-control">
                                                    <option value="">Select Office</option>
                                                        @foreach($office_list as $office)
                                                        <option value="{{ $office->office_id}}">{{ $office->office_name}}</option>
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
                                </div>

                                <div class="row form-row-gap">
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
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Medical Name</label>
                                                    <input type="text" name="medical_name" id="medical_name" class="form-control" value="" placeHolder="Medical Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Due Date</label>
                                                    <input type="text" name="due_date" id="due_date" class="form-control datepickernn" placeHolder="Due Date"autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Status</label>
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
                                            value="Search" onclick="hhaAppoitnemtList(1)">

                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                                            class="mdi mdi-reload"></i>
                                        Reset</a>

                                        @can('hha-other-compliance-export')
                                        <a href="javascript:void(0)" class="btn btn-info btn-rounded btn-fw btn-sm" onclick="exportCSV()"><i class="mdi mdi-plus"></i> Export CSV</a>
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
                    <span id="response_other_compliance"></span>
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
  
    <script src="{{ asset('assets/modulejs/hha_other_compliance/hha_other_compliance.js')}}?time={{ env('timestamp')}}"></script>
  
    <script src="{{ asset('assets/modulejs/hha_exchange/hha_exchange.js')}}?time={{ env('timestamp')}}"></script>
    <script>
        
        var _HHA_OTHER_COMPLIANCE_LIST = "{{ url('hha/hha-other-compliances/hha-other-compliance-ajax')}}";
        var _HHA_OTHER_COMPLIANCE_EXPORT_CSV = "{{ url('hha/hha-other-compliances/hha-other-compliance-export')}}";
        var _ADD_APPOINTMENT_OTHER_COMPLIANCE = "{{ url('hha/hha-other-compliances/add-hha-other-compliance')}}";
        var _CSRF_TOKEN = "{{ csrf_token()}}";
       
        var _HHA_CAREGIVER_DETAIL_URL = "{{ url('hha/hha-caregiver/demographic-detail') }}";
        var _HHA_CAREGIVER_CALENDAR_URL = "{{ url('hha/hha-caregiver/calendar-visits') }}";

        var currentCaregiversId = null; // Store current caregiver ID
        var agencyId = null;
        var loadedTabs = {}; // Track which tabs have been loaded
        var _HHA_CALENDER_LIST="{{ url('hha/hha-caregiver/calendar-visits') }}";
        var _HHA_CAREGIVER_DOWNLOAD_DOCUMENT = "{{ url('hha-caregiver-download-doc')}}";
        $(":input").inputmask();
        $("#filter-btn").click(function() {
            $("#search-filter-btn").slideToggle(600);
        });

        $('body').on('click', '#cboxid', function(e) {
             var checked = $(this).is(":checked");
             if (checked == true) {
                 $('.cbox').prop('checked', true);
             } else {
                 $('.cbox').prop('checked', false);
             }
         })
         hhaAppoitnemtList(1);
         $('body').on('click', '.hha_other_compliance_paginate .pagination a', function(event) {
             $('li').removeClass('active');
             $(this).parent('li').addClass('active');
             event.preventDefault();
             var myurl = $(this).attr('href');
             var page = $(this).attr('href').split('page=')[1];
             hhaAppoitnemtList(page);
         });

    </script>