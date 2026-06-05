@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<style>
    .error {
        color: Red;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .hide {
        display: none;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Group Notification Add</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <form action="{{url('/group-notification')}}" method="post" id="groupNotificatrionForm" name="groupNotificatrionForm" enctype="multipart/form-data">
                        <div class="card-body">

                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Name<span class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" placeholder="Enter Name">
                                            <span class="error" id="title_error"><?php echo $errors->add_agency->first('name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Agency</label>
                                        <div class="col-sm-9">
                                            <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                @foreach ($agencyList as $rwAgency)
                                                <option value="{{$rwAgency->id}}">{{$rwAgency->agency_name}}</option>
                                                @endforeach
                                            </select>
                                            <span id="agency_error" class="error mt-2"><?php echo $errors->add_agency->first('agency_fk'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Notification<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>
                                                        <input onclick="showCheckBox('caregiver')" class="notification_checkbox_caregiver" type="checkbox" id="caregiver_flag" name="caregiver_flag" value="Caregiver"> Caregiver
                                                    </label>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>
                                                        <input onclick="showCheckBox('patient')" class="notification_checkbox_patient" type="checkbox" id="patient_flag" name="patient_flag" value="Patient"> Patient
                                                    </label>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>
                                                        <input onclick="showCheckBox('both')" class="notification_checkbox_both" type="checkbox" id="both_flag" name="both_flag" value="Both"> Both
                                                    </label>
                                                </div>
                                            </div>
                                            <span id="type_error" class="error"></span>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">User<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="search_patient" name="user_id" id="user_id">

                                            <span id="user_error" class="error"><?php echo $errors->add_agency->first('user_id'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 hide" id="caregiver_notification_id">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Caregiver Notification<span class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="row" style="margin-top:10px;">
                                                <div class="col-md-4">
                                                    <label>
                                                        <input type="checkbox" id="caregiver_notification999999999999999" value="All" data-id="All" class="select_caregiver_checkbox" onclick="showCaregiverAll()">
                                                        All
                                                    </label>
                                                </div>
                                                @foreach($notificationList as $key => $item)
                                                <div class="col-md-4">
                                                    <label>
                                                        <input type="checkbox" id="caregiver_notification{{ $key }}" name="caregiver_notification[]" value="{{ $item }}" data-id="{{ $key}}" class="caregiver_checkbox">
                                                        {{ $item }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                            <span id="caregiver_notification_error" class="error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 hide" id="patient_notification_id">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Patient Notification</label>
                                        <div class="col-sm-9">
                                            <div class="row" style="margin-top:10px;">
                                                <div class="col-md-4">
                                                    <label>
                                                        <input type="checkbox" id="patients_notification999999999999999" value="All" data-id="All" class="select_patient_checkbox" onclick="showPatientAll()">
                                                        All
                                                    </label>
                                                </div>
                                                @foreach($notificationList as $key => $item)
                                                <div class="col-md-4">
                                                    <label>
                                                        <input type="checkbox" id="patients_notification{{ $key }}" name="patients_notification[]" value="{{ $item }}" data-id="{{ $key}}" class="patient_checkbox">
                                                        {{ $item }}
                                                    </label>
                                                </div>
                                                @endforeach

                                            </div>
                                            <span id="patients_notification_error" class="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Service</label>
                                        <div class="col-sm-9">
                                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">
                                                <option value="">Select Service</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success" id="groupNotificationSave">Save</button>
                            <a type="button" class="btn btn-light" href="{{url('group-notification')}}">Close</a>
                            <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader" id="loaderAddGroupNotification" style="display:none">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var SERVICE_DATA = "{{url('group-notification-service-data')}}";
    var _CSRF_TOKEN = '{{ csrf_token()}}';
    var _GROUP_NOTIFICATION_LIST = "{{ url('group-notification-list') }}";
    var _GROUP_NOTIFICATION = '{{ url("/group-notification") }}';
    var _GROUP_NOTIFICATION_BY_ID = '{{ url("/group-notification-by-id") }}';
    var _CSRF_TOKEN = '{{ csrf_token()}}';
</script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/modulejs/group_notification/group_notification.js')}}?time={{ time()}}"></script>
@include('include/footer')
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<script>
    $(".search_patient").tokenInput("{{ url('search-notification-users')}}");
</script>