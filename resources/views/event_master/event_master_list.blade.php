@include('include/header')
@include('include/sidebar')
 
 <!-- <link href="{{ asset('assets/css/event.css')}}?time={{ time()}}" > -->
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all">
 <style>
    .error {
    color: red;
}

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

.table-width1 tr th:last-child {
    width:88px;
}
.table-width1 tr th:first-child {
    width: 3%;
}
.table-width1 tr th:nth-child(3) {
   width: 10%;
}
.table-width1 tr th:nth-child(4) {
   width: 12%;
}
.table-width1 tr th:nth-child(5) {
   width: 12%;
}
.table-width1 tr th:nth-child(6) {
   width: 12%;
}

.table-width1 {
    background-color: #fff;
}

.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.modal-lg-plus {
    max-width: 600px; /* Set your custom width */
}

</style> 
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Popup List</h5>
             <div class="page-rightbtns">
                 <div>
                     @can('event-add')
                     <a href="javascript:void(0)" onclick="getEvent()" class="btn btn-primary btn-rounded btn-fw btn-sm ml-1"><i class="mdi mdi-plus"></i>Add Popup</a>
                     @endcan
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
         <div class="card">
            <div class="card-body compact-view">
                <div class="row">
                    <div class="col-12">
                        <div class="wmd-view-topscroll">
                            <div class="scroll-div1">
                            </div>
                        </div>
                        <div class="wmd-view">
                            <div class="scroll-div2">
                                <span id="resp"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
     </div>
    <script src="{{ asset('assets/modulejs/event_master.js')}}?time={{ time()}}"></script>
    <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('assets/bootstrap-datetimepicker.min.js')}}?time={{ env('timestamp')}}"></script>
    @include('event_master/_partial/ckeditor_js')
    @include('event_master/_partial/event_master_add_modal')
    @include('event_master/_partial/event_master_edit_modal')
    @include('event_master/_partial/event_master_edit_date_modal')
     
    <script> var ISAWS = '0';</script>
    @if (env('FILE_UPLOAD_PERMISSION')  != 'development')
        <script> var ISAWS = '1'; </script>
    @endif
     <script>
        var _EVENT_LIST = "{{ url('event-master-list') }}";
        var _EVENT = '{{ url("/event-master") }}'; 
        var _EVENT_BY_ID = '{{ url("/event-master-by-id") }}'; 
        var BASEURL = "{{ asset('event-image') }}/";
        var _CSRF_TOKEN ='{{ csrf_token()}}';
        var _EVENT_AWS ='{{ url("event-image-show-aws")}}';
        var _CHANGE_STATUS ="{{ url('change-event-status') }}"
     </script>
@include('include/footer')