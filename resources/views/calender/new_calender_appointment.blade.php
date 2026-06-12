@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="{{ URL::to('/') }}/assets/libs/jquery-ui/jquery-ui.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.css">
<link href="{{ asset('assets/modulejs/css/calendar.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/modulejs/css/global.css') }}" rel="stylesheet" type="text/css">

<div class="main-panel">

    <div class="content-wrapper">
    @can('appointment-calender')
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Appointment Calendar</h5>
            <div class="page-rightbtns">
                <div class="calendar-type-selector" style="margin-right: 20px;display: flex;gap: 32px;">
                    <div class="">
                        <input class="form-check-input" type="radio" name="calendarType" id="appointmentCalendar" value="appointment" checked>
                        <label class="form-check-label" for="appointmentCalendar">Appointment Calendar</label>
                    </div>
                    <div class="">
                        <input class="form-check-input" type="radio" name="calendarType" id="telehealthCalendar" value="telehealth">
                        <label class="form-check-label" for="telehealthCalendar">Telehealth Calendar</label>
                    </div>
                </div>
            </div>
        </div><br>


        <div class="row">
            <div class="col-md-12 row">
                <div class="col-md-3 ">
                    <div class=" appointment-sticky-contain">
                        <div class="height-content-fixed">
                            <div class="calender-design position-relative">
                                <div id="fu_date" class="appointment-calendar"></div>
                            </div>
                            <div class="card appointment-info-card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h4 class="card-title">Appointment Info</h4>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="pull-right">
                                                <p class="display_inline"></p>&nbsp;<i class="fa fa-refresh" onclick="refreshData();" style="float:right;" aria-hidden="true"></i><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <dl>

                                            <p class="display_inline"><b>Appointment Type:</b></p>&nbsp;<dd class="display_inline" id="type_set"></dd><br>
                                            <p class="display_inline"><b>Appointment Date:</b></p>&nbsp;<dd class="display_inline" id="appointment_date_set"></dd><br>
                                            <p class="display_inline"><b>Appointment Time:</b></p>&nbsp;<dd class="display_inline" id="appointment_time_set"></dd><br>
                                            <p class="display_inline" id="telehealth_time_frame" style="display:none"><b>Appointment Time Frame:</b></p>&nbsp;<dd class="display_inline" id="appointment_time_frame"></dd><br>
                                            <p class="display_inline"><b>Services:</b></p>&nbsp;<dd class="display_inline" id="service_name_set"></dd><br>
                                            <p class="display_inline"><b>SSN:</b></p>&nbsp;<dd class="display_inline" id="ssn_set"></dd>
                                        </dl>
                                    </div>
                                    <hr>

                                    <div class="row" style="margin-top:3%">


                                        <dl>

                                            <p class="display_inline"><b>Portal Id:</b></p>&nbsp;<dd class="display_inline" id="portal_id_set"></dd><br>
                                            <p class="display_inline"><b>Agency Name:</b></p>&nbsp;<dd class="display_inline" id="agency_name_set"></dd><br>
                                            <p class="display_inline"><b>Name:</b></p>&nbsp;<dd class="display_inline" id="full_name_set"></dd>
                                            <dd class="display_inline" id="patient_code_set"></dd><br>
                                            <p class="display_inline"><b>Status:</b></p>&nbsp;<dd class="display_inline" id="status_set"></dd><br>
                                            <span id="location_show_div">
                                            <p class="display_inline"><b>Location:</b></p>&nbsp;<dd class="display_inline" id="location_set"></dd><br>
                                            </span>
                                            <span id="nurse_show_div">
                                            <p class="display_inline"><b>Nurse:</b></p>&nbsp;<dd class="display_inline" id="nurse_set"></dd><br>
                                            </span>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="card" style="margin-top:29px;">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h4 class="card-title">Recent Notes</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <span id="recent_notes_response">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="card appointment-calendar-card">
                        <!-- <div class="loader-sec" style="display:none">
                            <div id="cover-spin"></div>
                        </div> -->
                        <div class="card-header">
                            <div class="row">
                                <input type="hidden" name="" id="search_day" value="daily">
                                <div class="col-sm-5 d-flex justify-content-start view-button">
                                    <a href="#" id="weeklyId" class="previous next btn-custom search_button checkBtn" onclick="getAppointmentSearchData('weekly')">Weekly View</a>&nbsp;&nbsp;
                                    <a href="#" id="daily" class="previous btn-custom search_button checkBtn" onclick="getAppointmentSearchData('daily')">Daily View</a>&nbsp;&nbsp;
                                    <a href="#" id="monthlyId" class="previous btn-custom search_button checkBtn" onclick="getAppointmentSearchData('monthly')">Monthly View</a>
                                </div>
                                <div class="col-sm-7 d-flex justify-content-end">
                                    <div class="col-sm-4 form-group">
                                        <select onchange="getAppointmentData()" name="appointemnt_type" class="form-control select2-design" id="appointemnt_type">
                                            @if(auth()->user()->record_access !="Caregiver" && auth()->user()->record_access !="Patient")

                                            <option value="">Select Type</option>
                                            @endif
                                            @if(auth()->user()->record_access !="Caregiver")
                                            <option value="patient">Patient</option>
                                            @endif
                                            @if(auth()->user()->record_access !="Patient")
                                            <option value="caregiver">Caregiver</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <select onchange="getAppointmentData()" name="status" class="form-control select2-design" id="status">
                                            <option value="">Select Status</option>
                                            <option value="Pending"> Pending</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="booked">Booked</option>
                                            <option value="completed">Completed</option>
                                            <option value="noshow">No Show</option>
                                            <option value="arrived"> Arrived</option>
                                            <option value="processing">Processing</option>
                                            <option value="Not interested">Not Interested
                                            </option>
                                            <option value="hospitalized/rehab">
                                                Hospitalized/Rehab</option>
                                            <option value="unableToContact"> Unable To Contact
                                            </option>
                                            <option value="refused"> Refused</option>
                                            <option value="checkin"> Mark as CheckIn</option>
                                            <option value="PendingTermination"> Termination</option>
                                            <option value="Onhold"> On Hold </option>
                                            <option value="Onleave"> On Leave </option>
                                            <option value="Terminated"> Terminated </option>
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class=" col-sm-3 form-group">
                                        <select onchange="getAppointmentData()" name="locationlist" class="form-control select2-design" id="location_id">
                                            <option value="">Select Location</option>
                                            @if (!empty($locationList))
                                            @foreach ($locationList as $locationkey)
                                            <option value="{{ $locationkey->id }}" {{ $locationId == $locationkey->id ? 'selected' : '' }}>
                                                {{ ucfirst($locationkey->address1) }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <select onchange="getAppointmentData()" name="agency_id" class="form-control select2-design" id="agency_id">
                                            <option value="">Select Agency</option>
                                            @if (!empty($agencyList))
                                            @foreach ($agencyList as $agency)
                                            <option value="{{ $agency->id }}">
                                                {{ ucfirst($agency->agency_name) }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body table-responsive">
                            <div class="shimmer-loader-schedule">
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                            </div>
                            <div id="calender_response">
                                <table class="table table-bordered">

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card telehealth-calendar-card" style="display:none">
                        <div class="card-header">
                            <div class="row">
                                <input type="hidden" name="" id="tele_search_day" value="daily">
                                <div class="col-sm-5 d-flex justify-content-start view-button">
                                    <a href="#" id="teleweeklyId" class="previous next btn-custom search_button checkBtn" onclick="getTelehealthCalendarData('weekly')">Weekly View</a>&nbsp;&nbsp;
                                    <a href="#" id="teledaily" class="previous btn-custom search_button checkBtn" onclick="getTelehealthCalendarData('daily')">Daily View</a>&nbsp;&nbsp;
                                    <a href="#" id="telemonthlyId" class="previous btn-custom search_button checkBtn" onclick="getTelehealthCalendarData('monthly')">Monthly View</a>
                                </div>
                                <div class="col-sm-7 d-flex justify-content-end">
                                    <div class="col-sm-4 form-group">
                                        <select onchange="getTelehealthCalendarSearchData()" name="tele_appointemnt_type" class="form-control select2-design" id="tele_appointemnt_type">
                                        @if(auth()->user()->record_access !="Caregiver" && auth()->user()->record_access !="Patient")    
                                        <option value="">Select Type</option>

                                        @endif
                                            @if(auth()->user()->record_access !="Caregiver")
                                            <option value="patient">Patient</option>
                                            @endif
                                            @if(auth()->user()->record_access !="Patient")
                                            <option value="caregiver">Caregiver</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class=" col-sm-3 form-group">
                                        <select onchange="getTelehealthCalendarSearchData()" name="telehealth_nurse" class="form-control select2-design" id="telehealth_nurse">
                                            <option value="">Select Nurse</option>
                                            @if (!empty($nurse))
                                            @foreach ($nurse as $nurses)
                                            <option value="{{ $nurses->id }}">{{ ucfirst($nurses->name) }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 d-flex justify-content-start align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="tele_prev_btn" title="Previous">
                                        <i class="fa fa-chevron-left"></i> Previous
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <div id="calendarHeader" class="text-center w-100" style="font-weight:bold; font-size: large;">
                                        July 2025
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex justify-content-end align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="tele_next_btn" title="Next">
                                        Next <i class="fa fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="shimmer-calender-loader">
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                                <div class="shimmer-box"></div>
                            </div>
                            <div id="tele_calender_response">
                                <table class="table table-bordered">

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
    

</div>

<div class="fc-popover fc-more-popover" id="popup" style="display:none">
    <div class="fc-header fc-widget-header">
        <span class="fc-close fc-icon fc-icon-x" id="fc-close"></span>
        <span class="fc-title" id="fc-title"></span>
        <div class="fc-clear">
        </div>
    </div>
    <div class="fc-body fc-widget-content">
        <div class="fc-event-container" id="container">
    </div>
</div>
</div>



@include('include/footer')
<script>
var _LOAD_RECENT_NOTES = "{{ url('latest-recent-notes')}}";
</script>
    <script src="{{asset('assets/vendors/moment/moment.min.js')}}"></script>
    <script src="{{asset('assets/vendors/select2/select2.min.js')}}"></script>
    <script src="{{asset('assets/js/select2.js')}}"></script>
    
    <script src="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.js"></script>

    <script type="text/javascript">
        var _GET_APPOINTMENT_DATA = "{{ url('/dashboard/get-new-appointment-data') }}";
        var _GET_APPOINTMENT_DETAILS = "{{ url('/new-calendar-hospital/appointment-details') }}";
        var _GET_MONTHLY_APPOINTMENT_DATA = "{{ url('/new-calendar-hospital/get-monthly-appoitment-details') }}";
        var _VIEW_URL = "{{ url('/patient/view/')}}";
        var _LOAD_RECENT_NOTES = "{{ url('latest-recent-notes')}}";
        var _GET_TELE_APPOINTMENT_DATA = "{{ url('get-tele-appointment-data')}}";
        var _GET_MONTHLY_TELE_APPOINTMENT_DATA = "{{ url('get-monthly-tele-appoitment-details')}}";
    </script>
    <script src="{{asset('assets/modulejs/custom_appointment_calendar.js')}}?time={{ env('timestamp')}}"></script>
