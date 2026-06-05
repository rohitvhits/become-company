@include('include/header')
@include('include/sidebar')
<style type="text/css">
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

     td {
         table-layout: fixed;
         width: 20px;
         overflow: hidden;
         word-wrap: break-word;
     }

     .table-width1 {
         background-color: #fff;
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

.modal-table {
  width: 100%;
  margin: 0;
  font-size: 14px;
}

.modal-table th {
  background-color: #f8f9fa;
  font-weight: bold;
  text-align: left;
  width: 30%;
}

.modal-table td {
  text-align: left;
  width: 70%;
}

.modal-body {
  padding: 20px;
}

.table-container {
  max-height: 300px; /* Set your desired fixed height */
  overflow-y: auto; /* Enable vertical scrolling */
  border: 1px solid #ddd; /* Optional: Add a border for better visuals */
  padding: 10px;
  border-radius: 5px;
}
     
 </style>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
<link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">

 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">API Call Log List</h5>
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

         <div class="row ">
             <div class="col-sm-12">
                 <div class="card search-card1" id="search-div">
                     <div class="card-body">
                         <form method="get" id="formsubmit">
                             @csrf
                             <div class="row">
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Agency Name</label>
                                         <div class="col-sm-12">
                                            <select class="form-control" name="agency_id" id="agency_id">
                                                <option value="">Select Agency name </option>
                                                @foreach($agencyList as $agency)
                                                    <option value="{{$agency->id}}">{{$agency->agency_name}}</option>
                                                @endforeach
                                            </select>
                                         </div>
                                         <span class="error ml-2" id="error_all"></span>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Type</label>
                                         <div class="col-sm-12">
                                            <select class="form-control" name="type" id="type">
                                                <option value="">Select Type </option>
                                                @foreach($typeList as $types)
                                                    @if(isset($types['type']) && !empty($types['type']))
                                                        <option value="{{$types['type']}}">{{$types['type']}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                         </div>
                                         <span class="error ml-2" id="error_all"></span>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Created Date</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="created_date" value="" class="datepickernn form-control" id="created_date">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="search-main1">
                                 <div class="search-inner">
                                     <div>
                                         <input type="button" name="search"
                                             class="btn btn-primary search-btn1 searchAppoinment" onclick="loadApiLogList(1);" id="search-data"
                                             value="Search">
                                             <a href="<?php echo URL::to('/'); ?>/api-log-report" class="btn btn-light btn-rounded btn-fw btn-sm ml-1"><i
                                        class="mdi mdi-reload"></i>
                                    Reset</a>
                                     </div>
                                     
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>

         
        <div class="row">
            <div class="col-12">
                        <div class="col-12" id="logList" style="display:flex;justify-content:center;">
                    <img src="{{asset('/ajax-loader.gif')}}" alt="loader" id="loadertag" style="display: flex; ">
                </div>
                <span id="resp"></span>
            </div>
        </div>
            
    </div>
    @include('include/footer')
@include('api_log_report/_partial/log_modal')
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/sweetalert.min.js"></script>
<script>
    var _API_LOG_LIST = "{{ url('api-log-report-ajax-list') }}";
    var _API_LOG = '{{ url("/api-log-report") }}'; 
    var _API_LOG_BY_ID = '{{ url("/get-api-log-by-id") }}'; 
</script>
<script src="{{ asset('assets/modulejs/api_log_report/api_log_report.js')}}?time={{ time()}}"></script>



