@include('include/header')
<style>
.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }
    </style>
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Notification Setting List</h5>
            <div class="page-rightbtns">
                <div>
            
                    <a class="btn btn-primary btn-rounded btn-fw btn-sm  ml-1 add-notification-email"><i class="mdi mdi-plus"></i>
                    Add Notification Email</a>

                </div>

            </div>
        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div">
                    <div class="card-body">
                        <div class="row">
                            <div class="table-responsive">
                                <span id="notification_email_id"></span>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>
        
    </div>

</div>
@include('notification-setting._partial.create_modal')

@include('include/footer')
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script>
    var userList = "{{ url('notification-email-list') }}";
    var serviceList = "{{ url('ajax-all-service')}}"
    var _SAVE_NOTIFICATION_EMAIL = "{{ url('agency-wise-notification-email-save')}}"
    var _CSRF_TOKEN = "{{ csrf_token() }}"
    var _EDIT_NOTIFICTION_EMAIL = "{{ url('edit-email-notification') }}"
    var _DELETE_NOTIFICTION_EMAIL = "{{ url('delete-notification-email') }}"
   
</script>
<script src="{{ asset('assets/modulejs/notification_email.js') }}?time={{ env('timestamp')}}"></script>