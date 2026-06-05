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
             <h5 class="mb-0 font-weight-bold">Announcements List</h5>
             <div class="page-rightbtns">
                 <div>
                     @can('announcements-add')
                     <a href="javascript:void(0)" onclick="getEvent()" class="btn btn-primary btn-rounded btn-fw btn-sm ml-1"><i class="mdi mdi-plus"></i>Add Announcements</a>
                     @endcan
                 </div>
             </div>
         </div>

         <div class="col-12 grid-margin-top">
             
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
    <script src="{{ asset('assets/modulejs/communication.js')}}?time={{ time()}}"></script>
    <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('assets/bootstrap-datetimepicker.min.js')}}?time={{ env('timestamp')}}"></script>
    @include('communication/_partial/ckeditor_js')
    @include('communication/_partial/communication_add_modal')
    @include('communication/_partial/communication_edit_modal')
    @include('communication/_partial/communication_edit_date_modal')
     
    <script> var ISAWS = '0';</script>
    @if (env('FILE_UPLOAD_PERMISSION')  != 'development')
        <script> var ISAWS = '1'; </script>
    @endif
     <script>
        var _EVENT_LIST = "{{ url('announcements-list') }}";
        var _ANNOUNCEMENTS_ADD = '{{ url("/announcements-save") }}'; 
        var _EVENT_BY_ID = '{{ url("/announcements-by-id") }}'; 
        var BASEURL = "{{ asset('announcements-image') }}/";
        var _CSRF_TOKEN ='{{ csrf_token()}}';
        var _EVENT_AWS ='{{ url("announcements-image-show-aws")}}';
        var _CHANGE_STATUS ="{{ url('change-announcements-status') }}"
        var _ANNOUNCEMENTS_MAIL ="{{ url('announcements-mail-all-user') }}"
        var _ANNOUNCEMENTS_DELETE ="{{ url('announcements-delete') }}"
        var _ANNOUNCEMENTS_UPDATE ="{{ url('announcements-update') }}"
     </script>
@include('include/footer')