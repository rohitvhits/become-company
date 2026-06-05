@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link href="{{ asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/token-input.css')}}" rel="stylesheet" type="text/css" />
<style>
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    .select2-design+.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .wmd-view-topscroll,
    .wmd-view {
        overflow-x: scroll;
        overflow-y: hidden;
        border: none 0px red;
    }

    .wmd-view {
        overflow: auto;
        height: calc(100vh - 250px);
    }

    .wmd-view-topscroll {
        height: 20px;
    }

    .scroll-div1 {

        overflow-x: scroll;
        overflow-y: hidden;
        height: 20px;
        width: calc(1650px - -17px) !important;
    }

    .scroll-div2 {
        height: 20px;
    }

    .scroll-div1,
    .scroll-div2 {
        /* width: 1650px; */
    }

    td {
        table-layout: fixed;
        width: 20px;
        overflow: hidden;
        word-wrap: break-word;
    }

    .table-width1 tr th:last-child {
        width: 100px;
    }

    .table-width1 tr th:nth-child(10) {
        width: 100px;
    }

    .table-width1 {
        background-color: #fff;
    }

    .table-width1 tr th:nth-child(11) {
        width: 152px;
    }

    .table-width1 tr th:nth-child(12) {
        white-space: nowrap;
    }

    .search-inner {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        padding-right: 20px;
        padding-left: 20px;
    }

    .search-main1 {
        border-top: 1px solid #eeeeee;
        margin-left: -20px;
        margin-right: -20px;
    }

    .search-btn1,
    .search-btn1:hover,
    .search-btn1:active,
    .search-btn1:focus {
        background: #007bff !important;
        border: #007bff !important;
        border-radius: 20px;
        height: 36px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-card1 {
        margin-bottom: 20px;
    }

    .search-card1 .form-group {
        margin-bottom: 0.5rem;
    }

    .search-card1 label {
        margin-bottom: 0;
    }

    .search-card1 .card-body {
        padding-bottom: 10px;
    }

    .search-card1 input[type=text] {
        border-radius: 4px;
        border-color: #aaa;
    }

    .srch-icon {
        padding: 0 !important;
        width: 40px;
        height: 40px;
    }

    .no_warp {
        white-space: nowrap;
    }
</style>
<div class="main-panel">
    @php
    $auth = auth()->user();
    @endphp
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Emmacare</h5>

        </div>

        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Record Id</label>
                                        <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="record_id" id="patient_code" placeholder="Enter Record Id">
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Full Name</label>
                                        <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="full_name" id="full_name"  placeholder="Enter Full Name">
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Date of Birth</label>
                                        <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="date_of_birth form-control" name="dob" id="dob" placeholder="Enter Date of Birth">
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Gender</label>
                                        <div class="col-sm-12">
                                            <select name="gender" id="gender" class="form-control">
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                
                                            </select>
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Language</label>
                                        <div class="col-sm-12">
                                            <select name="language" id="language_id" class="form-control">
                                                <option value="">Select Language</option>
                                                @foreach($language_list as $lang)
                                                    <option value="{{$lang->id}}">{{ $lang->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Mobile</label>
                                        <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="mobile" id="mobile" placeholder="Enter Mobile">
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Insurance</label>
                                        <div class="col-sm-12">
                                            <select name="insurance" id="insurance_id" class="form-control">
                                                <option value="">Select Insurance</option>
                                                @foreach($insuranceList as $ins)
                                                    <option value="{{$ins->id}}">{{ $ins->insurance_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Referral Uid</label>
                                        <div class="col-sm-12">
                                            <select name="referral_uid" class="form-control">
                                                <option value="">Select Referral UID</option>
                                                @foreach(Common::getRemoteReferralSourceId() as $key=> $val)
                                                    <option value="{{ $key}}">{{ $val}}</option>
                                                @endforeach
                                            </select>
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Created Date</label>
                                        <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="datepickernn form-control" name="created_date" id="created_date">
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Created By</label>
                                        <div class="col-sm-12">
                                             <input autocomplete="off" type="text" class="form-control" name="created_by" id="created_by_id">
                                         </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <a href="javascript:void(0)"  class="btn btn-primary btn-rounded btn-sm btn-fw  ml-1 btnSearch" ><i class="fa fa-search"></i> Search</a>
                        <a href="javascript:void(0)"  class="btn btn-success btn-rounded btn-sm btn-fw  ml-1 btnExport" id="test_agency"><i class="fa fa-file"></i> Export</a>
                        <a href="javascript:void(0)"  class="btn btn-secondary btn-rounded btn-sm btn-fw  ml-1 btnRefresh"><i class="fa fa-refresh"></i> Refresh</a>
                        <img src="{{ asset('ajax-loader.gif')}}" class="order-listing-loader1" alt="loader" id="loadertag1" style="display:none">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 " id="response_id">
                
            </div>

        </div>

    </div>
    <div class="row" style='margin-top: 5%;'>
        <pre id='toastrOptions'></pre>
    </div>
</div>
    




    @include('include/footer')
    
    <script>
        var _LOAD_DATA_URL = "{{ url('emmacare_referal_table/ajax-list')}}"
        var _LOAD_EXPORT_CSV = "{{ url('emmacare_referal_table/export-csv')}}";
        var _DATE_TIME = "{{ date('m/d/Y')}}";
    </script>
    <script src="{{ asset('assets/modulejs/EmmacareReferalTable/emmacare_referal_table.js')}}?time={{ time()}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
     <script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
     <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
     <script src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
    <script>
        $(function() {
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

            $('.date_of_birth').daterangepicker({
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

                $('.date_of_birth').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                     'MM/DD/YYYY'));
            })
        });
        var urlToken =  "{{ url('search-nybest-user') }}"; 
        var empId = '';
         var empName = '';    
        $("#created_by_id").tokenInput(urlToken, {
            
            tokenLimit: 1,
            zindex: 9999,
            prePopulate: empId !== "" && empName !== "" ? [{ id: empId, name: empName }] : [],
            onAdd: function (item) {
             
            },
            onDelete:function(item){
              
            }
        });
        
    </script>