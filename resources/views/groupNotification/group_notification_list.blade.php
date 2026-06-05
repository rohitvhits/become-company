@include('include/header')
@include('include/sidebar')
 
<style>

span.select2.select2-container.select2-container--default {
    width: 100% !important;
}

.select2-container--default .select2-selection--multiple {
    border-radius: 0px !important;
    border: 1px solid #e3e7ed !important;
}
.error {
    color: red;
}

.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.table {
    width: 100%;
    border-collapse: collapse;
}

.table td {
    max-width: 200px;         /* Limit the width of each cell */
    overflow: hidden;         /* Hide content that overflows */
    text-overflow: ellipsis;  /* Show ellipsis (...) for truncated content */
    word-wrap: break-word;    /* Allow word wrapping */
    white-space: nowrap;      /* Ensure the text wraps when necessary */
    padding: 8px;
    border: 1px solid #ddd;   /* Add borders to table */
    word-wrap: break-word;
}

.table tr {
    height: auto; /* Let the row height adjust according to the content */
}

@media screen and (max-width: 600px) {
    .table td {
        max-width: 100%;
        word-wrap: break-word;
    }
}
 </style> 
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Group Notification List</h5>
             <div class="page-rightbtns">
                 <div>
                     @can('group-notification-add')
                        <a href="{{url('group-notification/create')}}" class="btn btn-primary btn-rounded btn-fw btn-sm ml-1"><i class="mdi mdi-plus"></i>Add Group Notification</a>
                     @endcan
                 </div>
               
             </div>
         </div>
         <div class="card">
            <div class="card-body compact-view">
                <div class="row">
                    <div class="col-12">
                        <span id="resp"></span>
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>
@include('include/footer')

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script>
    var _GROUP_NOTIFICATION_LIST = "{{ url('group-notification-list') }}";
    var _GROUP_NOTIFICATION = '{{ url("/group-notification") }}'; 
    var _GROUP_NOTIFICATION_BY_ID = '{{ url("/group-notification-by-id") }}'; 
    var _CSRF_TOKEN ='{{ csrf_token()}}';
</script>
<script src="{{ asset('assets/modulejs/group_notification/group_notification.js')}}?time={{ time()}}"></script>



