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
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #ddd;
  padding: 10px;
  border-radius: 5px;
}

</style>
@include('_partial/visit_detail_overlay_css')
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
<link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">

 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Task Health Cron Log List</h5>
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
             <div class="col-sm-12">
                 <div class="card search-card1" id="search-div">
                     <div class="card-body">
                         <form method="get" id="formsubmit">
                             @csrf
                             <div class="row">
                                 <div class="col-md-2">
                                     <div class="form-group row">
                                         <label class="col-sm-12">Task ID</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="task_id" id="task_id" class="form-control" placeholder="Enter Task ID">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="form-group row">
                                         <label class="col-sm-12">Patient ID</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="patient_id" id="patient_id" class="form-control" placeholder="Enter Patient ID">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="form-group row">
                                         <label class="col-sm-12">Patient Name</label>
                                         <div class="col-sm-12">
                                             <input type="text" name="patient_name" id="patient_name" class="form-control" placeholder="Enter Patient Name">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="form-group row">
                                         <label class="col-sm-12">Agency Name</label>
                                         <div class="col-sm-12">
                                             <select class="form-control" name="agency_id" id="agency_id">
                                                 <option value="">Select Agency</option>
                                                 @foreach($agencyList as $agency)
                                                     <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="form-group row">
                                         <label class="col-sm-12">Type</label>
                                         <div class="col-sm-12">
                                             <select class="form-control" name="type" id="type">
                                                 <option value="">Select Type</option>
                                                 @foreach($typeList as $type)
                                                     <option value="{{ $type }}">{{ $type }}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="form-group row">
                                         <label class="col-sm-12">Cron Name</label>
                                         <div class="col-sm-12">
                                             <select class="form-control" name="cron_name" id="cron_name">
                                                 <option value="">Select Cron</option>
                                                 @foreach($cronNameList as $cronName)
                                                     <option value="{{ $cronName }}">{{ $cronName }}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="form-group row">
                                         <label class="col-sm-12">Created Date</label>
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
                                             class="btn btn-primary search-btn1" onclick="loadTaskHealthCronLogList(1);" id="search-data"
                                             value="Search">
                                         <a href="<?php echo URL::to('/'); ?>/task-health-cron-log" class="btn btn-light btn-rounded btn-fw btn-sm ml-1">
                                             <i class="mdi mdi-reload"></i> Reset
                                         </a>
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
                     <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag" style="display:flex;">
                 </div>
                 <span id="resp"></span>
             </div>
         </div>

     </div>
     @include('include/footer')
@include('task_health_cron_log/_partial/log_modal')
@include('task_health_critical_alert/_partial/visit_detail_modal')
@include('_partial.task_health_flags.modal')
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/sweetalert.min.js"></script>
<script>
    var _TASK_HEALTH_CRON_LOG_LIST   = "{{ url('task-health-cron-log-ajax-list') }}";
    var _TASK_HEALTH_CRON_LOG_BY_ID  = '{{ url("/get-task-health-cron-log-by-id") }}';
    var _TH_VISIT_DETAIL_JSON_URL    = "{{ url('task-health/visit-detail-json') }}";
    var _TH_VISIT_DETAIL_JSON_POC    = "{{ url('task-health/visit-detail-json-poc') }}";
    var _TH_VISIT_CHECK_MASTER       = "{{ url('task-health/visit-check-master') }}";
    var _TH_MASTER_BY_ID             = "{{ url('get-task-health-master-by-id') }}";
    var _TH_AGENCIES_URL          = "{{ url('task-health/visit-agencies') }}";
    var _TH_VISIT_DETAIL_URL      = "{{ url('task-health/visit-detail') }}";
    var _TH_VISIT_DETAIL_JSON_URL = "{{ url('task-health/visit-detail-json') }}";
    var _TH_CHECK_MASTER_URL      = "{{ url('task-health/visit-check-master') }}";
    var _TASK_HEALTH_FLAG_UPDATE     = '{{ url("/task-health-flag-update") }}';
    var _TASK_HEALTH_FLAGS_SAVE      = '{{ url("/task-health-flags-save") }}';
    var _TH_FLAGS_BY_TASK_URL     = "{{ url('task-health-flags-by-task-id') }}";
    var _TASK_HEALTH_FLAG_UPDATE  = '{{ url("/task-health-flag-update") }}';
    var _TASK_HEALTH_FLAGS_SAVE   = '{{ url("/task-health-flags-save") }}';
    var _TH_HHA_PREVIEW_BY_TASK   = "{{ url('task-health/by-task') }}";
    var _TH_UPLOAD_DOC_BY_TASK    = "{{ url('task-health/by-task') }}";
</script>
<script src="{{ asset('assets/modulejs/task_health_cron_log/task_health_cron_log.js') }}?time={{ time() }}"></script>
<script src="{{ asset('assets/modulejs/task_health_visit/task_health_visit.js') }}?time={{ time() }}"></script>
