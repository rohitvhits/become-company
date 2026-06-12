@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ url('/') }}/assets/jquery-confirmation/css/jquery-confirm.min.css">
<link href="{{ url('/') }}/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ url('/') }}/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="{{ url('/') }}/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ url('/') }}/assets/css/global.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/modulejs/css/telehealth_location.css')}}" type="text/css" media="all" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<link href="{{ asset('/assets/bootstrap-datetimepicker.min.css')}}" type="text/css" media="all" rel="stylesheet" />

<!--main-container-part-->
<div class="main-panel">
    <div class="content-wrapper px-3 pb-0">

        <div class="dashboard-header d-flex flex-column ">
            <div class="basic-detail-sec border-bottom  mb-3 card">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 mr-4 font-weight-bold"> Manage Telehealth
                        </h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12 grid-margin stretch-card mb-0">
                            <div class="card">
                                <div class="left-section-main info-tab-sec">
                                    <ul class="nav nav-tabs tabs-left sideways left-section-ul">
                                        <li class="active"><a href="#slot_management" data-toggle="tab" data-tab-title="Slot Management"><i class="mdi mdi-note mr-1"></i> Slot Management</a>
                                        </li>
                                        <li><a href="#nurse_availability" data-toggle="tab" data-tab-title="Nurse Wise Slot Availability"> <i class="fa fa-info-circle mr-1"></i> Nurse Wise Slot Availability</a>
                                        </li>
                                        <li><a href="#manual_slot_availability" data-toggle="tab" data-tab-title="Manual Slot Availability"> <i class="mdi mdi-file-document mr-1"></i> Manual Slot Availability</a>
                                        </li>
                                        <li><a href="#time_frame_config" data-toggle="tab" data-tab-title="Time Frame Config"> <i class="fa fa-clock-o mr-1"></i> Time Frame Config</a>
                                        </li>
                                        {{--
                                        <li><a href="#patient_manage_tele_slot" data-toggle="tab"> <i class="mdi mdi-file-document mr-1"></i> Manage Patient Slot</a> --}}
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content left-section-tab-content">
                                        <div class="common-tab-title">
                                            <h5 id="activeTabTitle"><i class="mdi mdi-note mr-1"></i>Slot Management</h5>
                                        </div>
                                        <div class="tab-pane active" id="slot_management">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="">
                                                                            <div class="card common-card-box">
                                                                                <div class="page-title-main">
                                                                                    <div class="page-rightbtns">
                                                                                        <div>
                                                                                            @can('add-telehealth-location-schedule')
                                                                                            <a class="pull-right add-modal-edit btn btn-primary btn-sm mr-1 mt-1" data-toggle="modal" data-target="#addModal" data-id="" href="javascript::void(0)"><i class="mdi mdi-plus"> </i> Add Schedule </a>
                                                                                            @endcan
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body table-responsive tele-loc-wise-data-loader">
                                                                                    <table id="" class="table table-bordered ">
                                                                                        <thead>
                                                                                            <th width="5%">#</th>
                                                                                            <th width="20%">Title</th>
                                                                                            <th width="15%">Day</th>
                                                                                            <th width="15%">Start Time</th>
                                                                                            <th width="15%">End Time</th>
                                                                                            <th width="5%">Status</th>
                                                                                            <th width="10%">Time Slot <br />(In Minutes)</th>
                                                                                            <th width="15%">Action</th>
                                                                                        </thead>
                                                                                        <tbody class="shimmer-loader">
                                                                                            <tr>
                                                                                                <td colspan="8"></td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                                <span id="location_resp_id">
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="nurse_availability">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="card common-card-box">
                                                                            <div class="card-body">

                                                                                <!-- Filter Section -->
                                                                                <div class="">
                                                                                    <div class="">
                                                                                        <form id="scheduleFilterForm">
                                                                                            <div class="row">
                                                                                                <div class="col-md-3">
                                                                                                    <div class="form-group">
                                                                                                        <label for="location">Location</label>
                                                                                                        <select class="form-control select2" id="tele_location" name="location" onchange="getSchedule(this.value)">
                                                                                                            <option value="">Select Location</option>
                                                                                                            @foreach($locations as $location)
                                                                                                            <option value="{{ $location->id }}">{{ $location->address1 }}</option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <div class="form-group">
                                                                                                        <label for="schedule">Schedule</label>
                                                                                                        <select class="form-control select2" id="schedule" name="schedule">
                                                                                                            <option value="">Select Schedule</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <div class="form-group">
                                                                                                        <label for="nurse">Nurse</label>
                                                                                                        <select class="form-control select2" id="nurse" name="nurse">
                                                                                                            <option value="">Select Nurse</option>
                                                                                                            @foreach($nurse as $key => $user)
                                                                                                            <option value="{{ $key }}">{{ $user['name'] }} @if($user['language']) ({{ $user['language'] }}) @endif</option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <div class="form-group">
                                                                                                        <label>&nbsp;</label>
                                                                                                        <div class="button-group">
                                                                                                            <button type="button" class="btn btn-primary" id="getCalendarBtn">
                                                                                                                <i class="fa fa-calendar"></i> Get Schedule
                                                                                                            </button>
                                                                                                            <button type="button" class="btn btn-cancel" id="cancelBtn" style="display: none;">
                                                                                                                <i class="fa fa-times"></i> Cancel
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Schedule Display Section -->
                                                                                <div class="schedule-container">
                                                                                    <div class="schedule-header">
                                                                                        <h6 class="mb-0">Schedule Details</h6>
                                                                                    </div>
                                                                                    <div class="schedule-body">
                                                                                        <div id="scheduleInfo" class="schedule-info" style="display: none;">
                                                                                            <p><strong>Location:</strong> <span id="selectedLocation"></span></p>
                                                                                            <p><strong>Schedule:</strong> <span id="selectedSchedule"></span></p>
                                                                                            <p><strong>Nurse:</strong> <span id="selectedNurse"></span></p>
                                                                                        </div>
                                                                                        <div id="daysEventsList">
                                                                                            <div class="no-schedule">
                                                                                                <i class="fa fa-calendar-alt fa-3x mb-3"></i>
                                                                                                <p>Please select location, schedule, and nurse to view available time slots.</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="action-buttons pt-2 mt-0 pb-3" style="display: none;">
                                                                                            <div class="pull-right button-group">
                                                                                                <button type="button" class="btn btn-success" id="saveEventsBtn">
                                                                                                    <i class="fa fa-save"></i> Save Selected Time Slots
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="manual_slot_availability">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="card common-card-box">
                                                                            <div class="card-body">
                                                                                <!-- Manual Slot Availability Form -->
                                                                                <form id="manualSlotForm">
                                                                                    <div class="row">
                                                                                        <div class="col-md-4">
                                                                                            <div class="form-group">
                                                                                                <label for="manual_nurse">Select Nurse</label>
                                                                                                <select class="form-control select2" id="manual_nurse" name="manual_nurse" required>
                                                                                                    <option value="">Select Nurse</option>
                                                                                                    @foreach($nurse as $key => $user)
                                                                                                    <option value="{{ $key }}">{{ $user['name'] }} @if($user['language']) ({{ $user['language'] }}) @endif</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-4">
                                                                                            <div class="form-group">
                                                                                                <label for="manual_date">Select Date</label>
                                                                                                <input type="text" name="manual_date" class="form-control" autocomplete="off" id="manual_date" placeholder="mm/dd/yyyy" readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-4">
                                                                                            <div class="form-group">
                                                                                                <label>&nbsp;</label>
                                                                                                <div class="button-group">
                                                                                                    <button type="button" class="btn btn-primary btn-sm" id="viewManualSchedule">
                                                                                                        <i class="fa fa-calendar"></i> View Schedule
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>

                                                                                <!-- Schedule Display Section -->
                                                                                <div class="schedule-container mt-4">
                                                                                    <div class="schedule-header">
                                                                                        <h6 class="mb-0">Schedule Details</h6>
                                                                                    </div>
                                                                                    <div class="schedule-body">
                                                                                        <div id="manualScheduleInfo" class="schedule-info" style="display: none;">
                                                                                            <p><strong>Nurse:</strong> <span id="selectedManualNurse"></span></p>
                                                                                            <p><strong>Date:</strong> <span id="selectedManualDate"></span></p>
                                                                                        </div>
                                                                                        <div id="manualDaysEventsList">
                                                                                            <div class="no-schedule">
                                                                                                <i class="fa fa-calendar-alt fa-3x mb-3"></i>
                                                                                                <p>Please select nurse and date to view available time slots.</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="manual-action-buttons pt-2 mt-0 pb-3" style="display: none;">
                                                                                            <div class="pull-right button-group">
                                                                                                <button type="button" class="btn btn-success" id="saveManualEventsBtn">
                                                                                                    <i class="fa fa-save"></i> Save Selected Time Slots
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="time_frame_config">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="box info-box card basic-detail-div">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="card common-card-box">
                                                                    <div class="card-body">
                                                                        <p class="text-muted">Set how many hours each booking time frame covers. Patients will choose from these windows when scheduling a telehealth appointment.</p>
                                                                        <div class="row align-items-end">
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label><strong>Time Frame Duration</strong></label>
                                                                                    <select class="form-control" id="timeFrameHoursSelect">
                                                                                        <option value="1" {{ $timeFrameHours == 1 ? 'selected' : '' }}>1 Hour (e.g. 9:00 AM – 10:00 AM)</option>
                                                                                        <option value="2" {{ $timeFrameHours == 2 ? 'selected' : '' }}>2 Hours (e.g. 9:00 AM – 11:00 AM)</option>
                                                                                        <option value="3" {{ $timeFrameHours == 3 ? 'selected' : '' }}>3 Hours (e.g. 9:00 AM – 12:00 PM)</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <button type="button" class="btn btn-primary" id="saveTimeFrameBtn">
                                                                                        <i class="fa fa-save"></i> Save
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div id="timeFramePreview" class="mt-3"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{--
                                        <div class="tab-pane" id="patient_manage_tele_slot">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="title">
                                                                            <div class=" d-flex justify-content-between align-items-center mb-2">
                                                                                <h5><i class="mdi mdi-file-document mr-1"></i>Manage Location Schedule</h5>
                                                                                <button type="button" class="btn btn-info btn-sm" id="copyAllWeekBtn">
                                                                                    <i class="fa fa-copy"></i> Schedule Week (Mon-Fri)
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body common-card-box">
                                                                            @if(isset($showFlag) && $showFlag != 1)
                                                                            <!-- Schedule Display Section -->
                                                                            <form id="addLocationScheduleForm">
                                                                                <div class="row">
                                                                                    <div class="col-md-3">
                                                                                        <div class="form-group">
                                                                                            <label for="day">Day<span class="error">*</span></label>
                                                                                            <select class="form-control" id="day" name="day" required>
                                                                                                <option value="">Select Day</option>
                                                                                                <option value="Monday">Monday</option>
                                                                                                <option value="Tuesday">Tuesday</option>
                                                                                                <option value="Wednesday">Wednesday</option>
                                                                                                <option value="Thursday">Thursday</option>
                                                                                                <option value="Friday">Friday</option>
                                                                                                <option value="Saturday">Saturday</option>
                                                                                                <option value="Sunday">Sunday</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="form-group">
                                                                                            <label for="start_time">Start Time<span class="error">*</span></label>
                                                                                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="form-group">
                                                                                            <label for="end_time">End Time<span class="error">*</span></label>
                                                                                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="form-group">
                                                                                            <label for="slot">Slot<span class="error">*</span></label>
                                                                                            <input type="number" class="form-control" id="slot" name="slot" placeholder="Enter slot" required min="1">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mt-2">
                                                                                    <div class="col-md-12 manual-action-buttons text-right">
                                                                                        <div class="pull-right button-group">
                                                                                            <button type="button" class="btn btn-primary" id="submitBtn">
                                                                                                <i class="fa fa-save"></i> Submit
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                            @endif
                                                                            <!-- Patient Telehealth Schedule Listing -->
                                                                            <div class="schedule-container">
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="schedule-days-container">
                                                                                            <!-- Data will be loaded via AJAX -->
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div style="width: 100%;height: 217px;background-color: #f4f4f4;"></div>
</div>
@include('telehealthLocationSchedule.add_schedule')
@include('telehealthLocationSchedule.edit_schedule')
@include('include/footer')
<script>
    var DIAGNOSIS = "{{url('patient/diagnosis-predict')}}";
    var DIAGNOSIS_HEALTH = "{{url('patient/diagnosis-health-predict')}}";
    var DIAGNOSIS_LAB_TEST = "{{url('patient/diagnosis-test-predict')}}";
    var REPORT_DIAGNOSIS = "{{url('patient/diagnosis-report-predict')}}";
    var CLINICAL_NOTES = "{{url('patient/diagnosis-clinical-notes')}}";
    var SCHEDULE_LOG = "{{ url('telehealth-schedule-view-logs') }}";
    var CSRF_TOKEN = "{{ csrf_token() }}";
    var SCHEDULE_STATUS = "{{ url('schedule-status-change') }}";
    var TELEHEALTH_LOCATION_SCHEDULE = "{{ url('telehealth-location-schedule') }}";
    var TELEHEALTH_SCHEDULE_AJAX = "{{ url('telehealth-schedule-ajax') }}";
    var TELEHEALTH_LOCATION = "{{ url('get-location-type-wise') }}";

    var GET_LOCATION_SCHEDULES = "{{ url('get-location-schedules') }}";
    var TELEHEALTH_LOCATION_SCHEDULE_AJAX = "{{ url('telehealth-location-schedule-ajax') }}";
    var SAVE_SELECTED_EVENTS = "{{ url('save-selected-events') }}";
    var CHECK_NURSE_SCHEDULE = "{{ url('check-nurse-schedule') }}";
    var UPDATE_NURSE_SCHEDULE = "{{ url('update-nurse-schedule') }}";
    var UPDATE_NURSE_SCHEDULE_DATE = "{{ url('update-nurse-schedule-by-date') }}";
    var UNAVAILABLEDATES = '{{$disable_date}}';
    var TIME_FRAME_HOURS = {{ $timeFrameHours }};
    var SAVE_TIME_FRAME_HOURS = "{{ url('save-time-frame-hours') }}";
</script>
<script>
    $(document).on('show.bs.tab', '.left-section-ul a[data-toggle="tab"]', function() {
        var title = $(this).data('tab-title') || $(this).text().trim();
        var icon  = $(this).find('i').prop('outerHTML') || '';
        $('#activeTabTitle').html(icon + title);
    });
</script>
<link rel="stylesheet" href="{{ asset('css/jquery-ui.css')}}">
<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/telehealth_location_schedule.js')}}"></script>
<script>