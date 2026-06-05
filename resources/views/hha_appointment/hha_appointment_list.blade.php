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

    </style>
<div class="main-panel main-page-box">
    <?php
    $auth = auth()->user();
    ?>
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">HHA Medical</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('add-appointment-hha-medical')
                        <a href="javascript:void(0)" class="btn btn-primary cust-right-btn"
                         onclick="addAppointment()"><i class="mdi mdi-plus"></i> Add Appointment</a>
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
                                                        <option value="">Select agency</option>
                                                
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
                                                    <label for="template_type">Office</label>
                                                        <select name="" id="office_list" class="form-control">
                                                            <option value="">Select Office</option>
                                                                @foreach($office_table_list as $offices)
                                                                    <option value="{{ $offices->office_id}}">{{$offices->office_name}}</option>
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
                                                    <label for="template_type">Caregiver Status</label><br>
                                                    <select name="" id="caregiver_status" class="form-control js-example-basic-multiple w-100 " multiple>
                                                        <option value="">Select Caregiver Status</option>
                                                            @foreach($status_list as $status)
                                                                <option value="{{ $status}}">{{$status}}</option>
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
                                                    <label for="template_type">Medical Name</label>
                                                    <input type="text" name="medical_name" id="medical_name" class="form-control" placeHolder="Medical Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Due Date</label>
                                                    <input type="text" readonly name="due_date" id="due_date" class="form-control datepickernn"  autocomplete="off"  placeHolder="Due Date"  value="{{$startDate }} - {{$endDate }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Date Perform</label>
                                                    <input type="text" readonly name="date_perform" id="date_perform" class="form-control datepickernn_date_perform"  autocomplete="off" placeHolder="Date Perform">
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
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Hire Date</label>
                                                    <input type="text" readonly name="hire_date" id="hire_date" class="form-control datepickernn_hire_date"  autocomplete="off" placeHolder="Hire Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Employeement Type</label>
                                                    <input type="text" name="employment_type" id="employment_type" class="form-control" placeHolder="Employeement Type">
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
                                            value="Search" onclick="hhaAppoitnemtList()">
                                        
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                                            class="mdi mdi-reload"></i>
                                        Reset</a>
                                        @can('hha-medical-export')
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
                                    <th nowrap>Agency Name</th>
                                    <th nowrap>Office Name</th>
                                    <th nowrap>Caregiver Full Name</th>
                                    <th nowrap>Caregiver Code</th>
                                    <th nowrap>Caregiver Phone</th>
                                    <th>DOB</th>
                                    <th nowrap>Caregiver Status</th>
                                    <th nowrap>Language</th>
                                    <th nowrap>Discipline</th>
                                    <th nowrap>Medical Name</th>
                                    <th nowrap>Due Date</th>
                                    <th nowrap>Date Perform</th>
                                    <th nowrap>Medical Status</th>
                                    <th nowrap>Appointment Status</th>
                                    <th nowrap>First Work Date</th>
                                    <th nowrap>Last Work Date</th>
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
                    <span id="response_requested_id"></span>
                </div>
                
            </div>
        </div>
         
         
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
     <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
     <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
     <script src="{{ asset('assets/js/select2.js')}}"></script>
     <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
     <script src="{{ asset('assets/modulejs/hha_exchange/hha_exchange.js')}}?time={{ env('timestamp')}}"></script>
     <script src="{{ asset('assets/vendors/fullcalendar/fullcalendar.min.js')}}"></script>
     <script type="text/javascript">
        var _HHA_CAREGIVER_DETAIL_URL = "{{ url('hha/hha-caregiver/demographic-detail') }}";
        var _HHA_CAREGIVER_CALENDAR_URL = "{{ url('hha/hha-caregiver/calendar-visits') }}";
        var _HHA_CALENDER_LIST="{{ url('hha/hha-caregiver/calendar-visits') }}";
        var currentCaregiversId = null; // Store current caregiver ID
        var agencyId = null;
        var loadedTabs = {}; // Track which tabs have been loaded
        var _HHA_CAREGIVER_DOWNLOAD_DOCUMENT = "{{ url('hha-caregiver-download-doc')}}";
        $("#filter-btn").click(function() {
            $("#search-filter-btn").slideToggle(600);
        });
         $(function() {
             $(".wmd-view-topscroll").scroll(function() {
                 $(".wmd-view")
                     .scrollLeft($(".wmd-view-topscroll").scrollLeft());
             });
             $(".wmd-view").scroll(function() {
                 $(".wmd-view-topscroll")
                     .scrollLeft($(".wmd-view").scrollLeft());
             });
         });

         function hhaAppoitnemtList(page=1) {
            //$('.loader-sec').show();
            var fname = $('#full_name').val();
            var code = $('#code').val();
            var medical_name = $('#medical_name').val();
            var due_date = $('#due_date').val();
            var agency_fk = $('#agency_fk').val();
            var status = $('#status').val();
            $('.shimmer_id').removeClass('hide');
            $('#response_requested_id').html("")
            $('.location-wise-data-loader').attr('style', 'display:flex');
             $.ajax({
                    url: "{{ url('hha/hha-medical/hha-appointment-ajax') }}?page=" + page,
                    type: "GET",
                    data: {
                        'agency_fk': agency_fk,
                        'fname': fname,
                        'code': code,
                        'medical_name': medical_name,
                        'due_date': due_date,
                        'status': status,
                        'office_id': $('#office_list').val(),
                        'caregiver_status':($('#caregiver_status').val() !=null)?$('#caregiver_status').val():"",
                        
                        'date_perform':$('#date_perform').val(),
                        'hire_date':$('#hire_date').val(),
                        'employment_type':$('#employment_type').val()
                    },
                    success: function(res) {
                        $('.shimmer_id').addClass('hide')
                        $('#response_requested_id').html(res)
                        $('.location-wise-data-loader').attr('style', 'display:none');
                    }
             })
             return false;
         }
         hhaAppoitnemtList(1);

         $('body').on('click', '#cboxid', function(e) {
             var checked = $(this).is(":checked");
             if (checked == true) {
                 $('.cbox').prop('checked', true);
             } else {
                 $('.cbox').prop('checked', false);
             }
         })

         function addAppointment() {
             var checked = $('.cbox').is(":checked");
             if (checked == false) {
                 toastr.error("Please select checkbox");
                 return false;
             } else {
                var final_array = [];
                $('.cbox').each(function(i, v) {
                    var schecked = $(this).is(":checked");
                    if (schecked == true) {
                        var values = $('.cbox:checked').val();

                        final_array.push(values);
                    }
                });

                $.confirm({
                    title: "Are you sure?",
                    content:"You want to create bulk appointments?",
                    type: 'blue',
                    columnClass: 'col-md-6',
                    buttons: {
                        submit: {
                            text: 'Confirm',
                            btnClass: 'btn-blue',
                            action: function () {
                                $.ajax({
                                    url: "{{ url('hha/hha-medical/add-appointment-patient') }}",
                                    type: "post",
                                    data: {
                                        'final_array': final_array,
                                        '_token': '{{ csrf_token() }}',

                                    },
                                    success: function(res) {
                                        final_array.pop();
                                        toastr.success(res.error_msg);
                                        hhaAppoitnemtList(1);

                                    },
                                    error: function(xhr, status, error) {
                                        toastr.error(xhr.responseJSON.error_msg);
                                    }
                                })
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            action: function () {
                                var btn =  this.buttons.submit;
                                btn.enable();
                            }
                        }
                    }
                });
             }
         }

         function singleDataAppointment(id) {
             var final_array = [];
             final_array.push(id);
             $.confirm({
                    title: "Are you sure?",
                    content:"You want to create new appointment?",
                    type: 'blue',
                    columnClass: 'col-md-6',
                    buttons: {
                        submit: {
                            text: 'Confirm',
                            btnClass: 'btn-blue',
                            action: function () {
                                $.ajax({
                                    url: "{{ url('hha/hha-medical/add-appointment-patient') }}",
                                    type: "post",
                                    data: {
                                        'final_array': final_array,
                                        '_token': '{{ csrf_token() }}',

                                    },
                                    success: function(res) {
                                        final_array.pop();
                                        toastr.success(res.error_msg);
                                        hhaAppoitnemtList(1);

                                    },
                                    error: function(xhr, status, error) {
                                        toastr.error(xhr.responseJSON.error_msg);
                                    }
                                })
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            action: function () {
                                var btn =  this.buttons.submit;
                                btn.enable();
                            }
                        }
                    }
                });
             
         }
         $('body').on('click', '.hha_appointment_paginate .pagination a', function(event) {
             $('li').removeClass('active');
             $(this).parent('li').addClass('active');
             event.preventDefault();
             var myurl = $(this).attr('href');
             var page = $(this).attr('href').split('page=')[1];
             hhaAppoitnemtList(page);
         });

         function exportCsv(){
            
            var fname = $('#full_name').val();
            var code = $('#code').val();
            
            
            var medical_name = $('#medical_name').val();
            var due_date = $('#due_date').val();
            var agency_fk = $('#agency_fk').val();
            var status = $('#status').val();
            var hire_date = $('#hire_date').val();
            var employment_type = $('#employment_type').val();
            var date_perform= $('#date_perform').val();
            var office_id= $('#office_list').val();
           var caregiver_status = ($('#caregiver_status').val() !=null)?$('#caregiver_status').val():'';
            var link ="{{ url('hha/hha-medical/hha-appointment-export') }}?agency_fk="+agency_fk+"&fname="+fname+"&code="+code+"&medical_name="+medical_name+"&due_date="+due_date+"&status="+status+"&caregiver_status="+caregiver_status+"&date_perform="+date_perform+"&hire_date="+hire_date+"&employment_type="+employment_type+"&office_id="+office_id;

            window.location.href=link;
            
         }
        $(function() {
            reinitDateRangePicker();
        });
    $('.js-example-basic-multiple').select2();
    function refresh() {
        $('#caregiver_status').val(' ').trigger('change')
        $('#search-form')[0].reset();
        reinitDateRangePicker();
        hhaAppoitnemtList(1);
    }

    function syncMedical(id) {
        $.confirm({
            title: "Are you sure?",
            content:"You want to sync medical?",
            type: 'blue',
            columnClass: 'col-md-6',
            buttons: {
                submit: {
                    text: 'Confirm',
                    btnClass: 'btn-blue',
                    action: function () {
                        $.ajax({
                            url: "{{ url('hha/hha-medical/refresh-sync') }}",
                            type: "post",
                            data: {
                                'caregiver_id': id,
                                '_token': '{{ csrf_token() }}',
                            },
                            success: function(res) {
                                toastr.success(res.error_msg);
                                hhaAppoitnemtList(1);
                            },
                            error: function(xhr, status, error) {
                                let json = JSON.parse(xhr.responseText);
                                toastr.error(json.error_msg);
                            }
                        })
                    }
                },
                cancel: {
                    text: 'Cancel',
                    action: function () {
                        var btn =  this.buttons.submit;
                        btn.enable();
                    }
                }
            }
        });
    }
    function reinitDateRangePicker(){
        var start = moment().subtract(0, 'days');
        var end = moment();
        $('.datepickernn').daterangepicker({
            startDate: start,
            endDate: end,
            autoUpdateInput: false,
            startOfWeek: 'sunday',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')],
                'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                    .endOf('month')
                ],
                'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                    .endOf('isoWeek')
                ],
                'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                    'weeks').endOf('isoWeek')],
            }
        }, function(chosen_date, end_date) {

            $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })

        $('.datepickernn_date_perform').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select Date Perform': [start, end],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                .endOf('month')
            ],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                .endOf('isoWeek')
            ],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
        }, function(chosen_date, end_date) {

        $('.datepickernn_date_perform').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
        })

        $('.datepickernn_hire_date').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select Hire Date': [start, end],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                .endOf('month')
            ],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                .endOf('isoWeek')
            ],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
        }, function(chosen_date, end_date) {

        $('.datepickernn_hire_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
        })

        }
</script>