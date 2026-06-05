@include('include/header')
@include('include/sidebar')
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
 <link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
 <link rel="stylesheet" href="{{ asset('/assets/modulejs/css/notification_user.css')}}">
 <div class="main-panel main-page-box">
     <div class="content-wrapper content-wrapper-box">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Notification List</h5>
             <div class="page-rightbtns cust-page-rightbtns">
                <!-- <div>
                    <a href="javascript::void(0)"  class="btn btn-success btn-sm btn-fw cust-right-btn" onclick="markasReadAll()"><i class="mdi mdi-check"></i>Mark all as read</a>
                </div> -->
            </div>
         </div>
         &nbsp;
         <div class="row">
            <div class="col-12">
                <div class="card" id="loader" style="display:none">
                    <div class="card-body">
                        <div class="mt-2">
                            <div class="accordion" id="loader" role="tablist">
                                <div class="card border-bottom">
                                    <div class="card-header" role="tab" id="heading-1">
                                        <div class="row mb-0 ml-1 mr-1" style="display: flex;justify-content: space-between;">
                                        </div>
                                    </div>
                                    <div class="shimmer">
                                        <div class="task-header shimmer-bar"></div>
                                        <div class="task-body">
                                            <div class="shimmer-line short"></div>
                                            <div class="shimmer-line long"></div>
                                            <div class="shimmer-line medium"></div>
                                        </div>
                                        <div class="task-footer shimmer-line short"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <span id="resp"></span>
            </div>
         </div>
    </div>
 </div>
@include('include/footer')
<script>
    var _NOTIFICATION_LIST = "{{ url('get-all-ajax-user-notification') }}";
    var MARK_AS_READ = "{{ url('mark-read-all-notification') }}";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
</script>
<script src="{{ asset('assets/modulejs/notification_user/notification_user.js')}}?time={{ time()}}"></script>



