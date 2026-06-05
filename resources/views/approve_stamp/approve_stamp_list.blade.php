@include('include/header')
 @include('include/sidebar')
 
 <!-- <link href="{{ asset('assets/css/stamp.css')}}?time={{ time()}}" > -->
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
 </style> 
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Stamp List</h5>
             <div class="page-rightbtns">
                 <div>
                     @can('stamp-add')
                     <a href="javascript:void(0)" onclick="getApproveStamp()" class="btn btn-primary btn-rounded btn-fw btn-sm ml-1"><i class="mdi mdi-plus"></i>Add Stamp</a>
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
     @include('approve_stamp/_partial/approve_stamp_modal')
     @include('approve_stamp/_partial/approve_stamp_edit_modal')
     <script src="{{ asset('assets/modulejs/approve_stamp.js')}}?time={{ time()}}"></script>
     
     <script>
        var _APPROV_STAMP_LIST = "{{ url('approve-stamp-list') }}";
        var _STAMP = '{{ url("/stamp") }}'; 
        var _APPROV_STAMP_BY_ID = '{{ url("/approve-stamp-by-id") }}'; 
        var baseUrl = "{{ asset('stamp-image') }}/";
        var _CSRF_TOKEN ='{{ csrf_token()}}';
        var _APPROVE_STAMP_STATUS ="{{ url('stampstatus') }}";
     </script>

@include('include/footer')