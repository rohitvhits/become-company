@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
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

     .scroll-div1,.scroll-div2 {
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
     .select2-container .select2-selection--single{
        height: 38px !important;
     }
     .tableData .add_new_record .left_record{
        left: -9px;
        right: unset !important;
     }
     .tableData .add_new_record {
        position: absolute;
        top: 0;
        
        background: #00BBE0;
        padding: 1px 5px;
        font-size: 10px;
        color: #fff;
        border-radius: 2px 2px 2px 2px;
        font-size: 10px !important;
     }
     .tableData .add_new_record::after {
        position: absolute;
        content: "";
        bottom: -6px;
        right: 0px;
        background: #b7b7b8;
        z-index: -1;
        width: 10px;
        height: 10px;

    }

    .tableData .add_new_record::after {
    left: 0px;
    border-radius: 0px 0px 0px 50px;
}
.hide{
    display: none;
}
 </style>
 <div class="main-panel">
     @php
     $auth = auth()->user();
     @endphp
     <div class="content-wrapper">

       

         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Hamaspik Appointments Report(<span id="total_record_id">0</span>)</h5>
            
         </div>
         <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            <div class="row">


                                
                                <div class="col-md-3">
                                    <label class="col-sm-12 ">Created Date</label>
                                    <div class="col-sm-12">
                                        <input type="text" autocomplete="off" name="appointment_date" class="datepicker1 form-control" id="appointment_date">
                                    </div>
                                </div>



                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                    <input type="button" name="search" class="btn btn-primary btn-rounded" id="search-data" value="Search" onclick="loadData(1)">
                    <a href="javascript:void(0)" class="btn btn-secondary btn-rounded" onclick="refresh()">Clear</a>
                    @can('hamaspik-appointment-report-export')
                    <a class="btn btn-warning  btn-rounded" onclick="exportCSV()">Export CSV</a>
                    @endcan
                    <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag1" class="hide">
                                         
                    </div>
                </div>
            </div>
        </div>
         <div class="row">
             <div class="col-12 ">
                 <div class="table-responsive tableData" >
                   
                     

                    
                 </div>
             </div>

         </div>

     </div>

     <div class="row" style='margin-top: 25px;'>
         <pre id='toastrOptions'></pre>
     </div>
     @include('include/footer')
     <script>
        var _LOAD_DATA_URL = "{{ url('ajax-list')}}";
        var _AGENCY_ID = "106";
        var _EXPORT_CSV = "{{ url('service-export-csv')}}";
        var _DATE_TIME = "{{ date('m/d/Y')}}";
        </script>
     <script src="{{ asset('assets/modulejs/appointment_service.js')}}?time={{ env('timestamp')}}"></script>
     <script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
     <script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
     <link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />