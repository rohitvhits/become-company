@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/payment_report.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Payment Log Report</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span></span></a>
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
                                                    <label>Agency Name</label>
                                                    <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                 <?php foreach ($agency_list as $rwAgency) { ?>
                                                     <option value="<?php echo $rwAgency->id; ?>" >
                                                         <?php echo $rwAgency->agency_name; ?></option>
                                                 <?php } ?>
                                             </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Portal ID</label>
                                                    <input type="text" autocomplete="off" class="form-control"  id="patient_id" placeholder="Portal Id">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Payment Type</label>
                                                    <select class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" name="status_payment_type" id="status_payment_type">
                                                        <option value="">Select Payment Type</option>
                                                        @if (count($masterData) > 0)
                                                        @foreach ($masterData as $master)
                                                        @if ($master->master_type_fk == 17)
                                                            <option value="{{ $master->id }}">{{ $master->name }}
                                                        </option>
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Location</label>
                                                    <select class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" name="status_location_id" id="status_location_id">
                                                        <option value="">Select Location</option>
                                                        @if (count($location_list) > 0)
                                                            @foreach ($location_list as $loc)
                                                                <option value="{{ $loc->id }}">{{ $loc->location_name }}</option>
                                                            @endforeach
                                                        @endif
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
                                                    <label>Services</label>
                                                    <select class="js-example-basic-multiple w-100 select2-design" multiple="multiple" name="services[]" id="services">
                                                        <?php
                                                        foreach ($serviceList as $service) { ?>
                                                            <option value="<?php echo $service->id; ?>">
                                                                <?php echo $service->name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created Date</label>
                                                    <input type="text" autocomplete="off" name="created_date" class="form-control" id="created_date"  placeholder="Created Date">
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
                                            value="Search" onclick="paymentLogReport(1)">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>

                                        <a href="javascript:void(0)" class="btn btn-info btn-rounded" onclick="exportCsv()">Export</a>
                                        
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id hideClass" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Agency Name</th>
                                    <th>Portal ID</th>
                                    <th>Name</th>
                                    <th>Payment Type</th>
                                    <th>Services</th>
                                    <th>Total Amount</th>
                                    <th>Total Received Amount</th>
                                    <th>Total Remaining Amount</th>
                                    <th>Location</th>
                                    <th>Created Date / Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody class="shimmer-loader">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <span id="payment_log_reponse_id"></span>



            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>
       
    </div>

</div>
@include('include/footer')
<div id="logSidebar" class="sidebar">
    <div class="sidebar-header">
        <h5>Payment Log Details</h5>
        <a href="javascript:void(0)" class="close-btn" onclick="closeSidebar()">✖</a>
    </div>
    <div class="sidebar-content" id="logContent">
        <p>Select a row to see details...</p>
    </div>
</div>
<div id="overlay" class="overlay" onclick="closeSidebar()"></div>
<script>
    var _PAYMENT_LOG_LIST ="{{ url('payment-log-report/ajax-list')}}";
    var _PAYMENT_LOG_CSV ="{{ url('payment-log-report/export-csv')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
    var _GENARARE_PAYMENT_HISTORY ="{{ url('genrate-payment-history') }}";
    var PATIENT_URL = "{{ url('patient/view/')}}";
</script>
<script type="text/javascript" src="{{ asset('assets/modulejs/paymentLogReport/paymentlogreport.js')}}?time={{ env('timestamp')}}"></script>
<script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
