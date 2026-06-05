@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>
<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Book Appointment Report List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                

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
                                                    <label for="full_name">Full Name</label>
                                                    <input type="text" name="full_name" class="form-control" id="full_name" placeholder="Enter Full Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="mobile_no">Mobile No</label>
                                                    <input type="text" name="mobile_no" class="form-control" id="mobile_no" placeholder="Enter Mobile No">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="book_date">Book Date</label>
                                                    <input type="text" name="book_date" class="form-control" id="book_date" placeholder="Enter Book Date" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="created_date">Created Date</label>
                                                    <input type="text" name="created_date" class="form-control" id="created_date" placeholder="Enter Created Date" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </form>
                            
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <a href="javascript:void(0)" class="btn search-btn1 searchAppoinment" id="search-data" onclick="loadAppointmentData(1)"><i class="mdi mdi-magnify"></i> Search</a>
                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                        @can('bulk-view-report-export')
                                        <a href="javascript:void(0)" class="btn btn-warning btn-rounded btn-fw btn-sm" onclick="exportCsv()"><i class="mdi mdi-file"></i> Export</a>
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
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Agency Name</th>
                                    <th>Service Name</th>
                                    <th>County</th>
                                    <th>Book Date</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="9"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <span id="response_requested_id">
                    
                </span>



            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>
       
    </div>

</div>
@include('include/footer')

<script>
    var _LOAD_DATA = "{{ url('book-appointment-report/ajax-list')}}";
    var _EXPORT_CSV ="{{ url('book-appointment-report/export-csv') }}";
    var _DATE ="{{ date('m/d/Y')}}";
</script>

<script src="{{ asset('assets/modulejs/bookAppointment/book_appointment.js')}}?time={{ time()}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />