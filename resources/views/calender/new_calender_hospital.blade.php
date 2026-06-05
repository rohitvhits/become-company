@include('include/header')
@include('include/sidebar')

<style>
    .calendar-position {
        margin-top: 200px;
        position: relative;
    }

    .fc-body.fc-widget-content {
        height: 400px;
        overflow: scroll;
    }

    .select2-design + .select2-container--default .select2-selection--single {
        padding: 0;
        min-height: 38px;
    }

    .select2-design + .select2-container {
        min-width: 158px;
        min-height: 38px;
        margin-left: -2% !important;
        height: 100%;
        width: 100%;
    }

    .cal-padding-0 + .select2-container {
        margin-left: 0 !important;
    }

    .select2-design + .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }

    .select2-design + .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }

    .right-align {
        display: flex;
        align-items: start;
    }

    .calenderbtn-design {
        max-height: 38px;
        border-radius: 5px;
        margin-left: 5px;
    }

    .select2-design + .select2-container .select2-selection--multiple {
        min-height: 38px;
    }

    .calender-design input {
        border-radius: 5px;
        border: 1px solid #aaa;
    }

    .date-calender {
        position: absolute;
        top: 10px;
        right: 23px;
    }

    .fc .fc-header-toolbar {
        margin-top: 20px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .custom-black-text .fc-time,
    .custom-black-text .fc-title {
        color: black !important;
    }

    #appointment_date_set,
    #appointment_time_set {
        display: block;
    }

  /*  */
  
</style>

<link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="{{ URL::to('/') }}/assets/libs/jquery-ui/jquery-ui.css">

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Calendar</h5>
        </div>

        <form method="get" id="formsubmit">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="calender-design position-relative">
                                <input onchange="searchFunctionality()" type="text" name="fudate" value="@if (!empty($fuDate)) {{ date('m/d/Y', strtotime($fuDate)) }} @endif" autocomplete="off" class="form-control datepicker" id="fu_date" placeholder="Select Appointment Date" readonly>
                                <i class="fa fa-calendar date-calender" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <select onchange="searchFunctionality()" name="emclist[]" class="form-control js-example-basic-multiple w-100 cal-padding-0" multiple="multiple" id="emc_id">
                            @if (!empty($userList))
                                @foreach ($userList as $kyy)
                                    <option value="{{ $kyy->id }}" {{ in_array($kyy->id, $emd_rep_idss) ? 'selected' : '' }}>
                                        {{ ucfirst($kyy->agency_name) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <select onchange="searchFunctionality()" name="locationlist" class="form-control select2-design" id="location_id">
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
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-md-12 row">
                <div class="col-md-3 calendar-position">
                    <div class="card" style="height: 528px; overflow-y: auto;">
                        <div class="card-body">
                            <h4 class="card-title">Appointment Info</h4>
                            <div class="row">
                                <dl>
                                    <dd id="appointment_date_set"></dd>
                                    <dd id="appointment_time_set"></dd>
                                    <hr>
                                    <dd id="full_name_set"></dd>
                                    <dd id="mobile_set"></dd>
                                    <dd id="dob_set"></dd>
                                    <dd id="diciplin_set"></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card card-body">
                        <div id="calendar" class="full-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('include/footer')

<script src="{{ URL::to('/') }}/assets/vendors/moment/moment.min.js"></script>
<script src="{{ URL::to('/') }}/assets/vendors/fullcalendar/fullcalendar.min.js"></script>
<script src="{{ URL::to('/') }}/assets/vendors/select2/select2.min.js"></script>
<script src="{{ URL::to('/') }}/assets/js/select2.js"></script>
<script src="{{ URL::to('/') }}/assets/modulejs/appointment_calendar.js"></script>
<link rel="stylesheet" href="{{ URL::to('/') }}/assets/libs/jquery-ui/jquery-ui.js">

<script>
    var _GET_APPOINTMENT_DATA = "{{ url('/dashboard/get-appointment-data') }}";

    $(document).ready(function() {
        var SITEURL = "{{ url('/') }}";
        var emc_id = $('#emc_id').val();
        var location_id = $('#location_id').val();
        var fu_date = $('#fu_date').val();

        var calnedr = $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listWeek,print,timeGrid'
            },
            // aspectRatio: 1.5,
            // eventLimit: false,
            // dayMaxEvents: 1,
            // defaultView: 'agendaWeek',
            // navLinks: true,
            // editable: true,
            // eventLimit: true,
            // allDaySlot: true,

            slotDuration: '00:15:00',
            slotLabelInterval: 15,
            
            

            events: function(start, end, timezone, callback) {
                var startDate = moment(start).format("YYYY-MM-DD");
                var endDate = moment(end).format("YYYY-MM-DD");
                var emc_id = $('#emc_id').val();
                var location_id = $('#location_id').val();
                var fu_date = $('#fu_date').val();

                $.ajax({
                    url: "{{ URL::to('/') }}/dashboard/get-appointment-data",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        start: startDate,
                        end: endDate,
                        id: emc_id,
                        loc_id: location_id,
                        fdt: fu_date,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function success(doc) {
                        callback(doc);
                    }
                });
            },

            

            eventRender: function(event, eventElement) {
                if (event.type === 'Caregiver' || event.type === 'Patient') {
                    eventElement.css('background-color', 'blue');
                } else {
                    eventElement.css('background-color', 'green');
                }

                if (event.type === "task") {
                    eventElement.css('background-color', 'orange');
                }

                if (event.status === "processing") {
                    eventElement.css('background-color', 'grey');
                }
                if (event.status === "booked") {
                    eventElement.css('background-color', 'blue');
                }
                if (event.status === "Completed") {
                    eventElement.css('background-color', 'green');
                }
                if (event.status === "pending") {
                    eventElement.css('background-color', 'yellow');
                    eventElement.addClass("custom-black-text");
                }
                if (event.status === "refused" || event.status === "cancelled" ||
                    event.status === "noshow" || event.status === "no answer" ||
                    event.status === "unableToContact") {
                    eventElement.css('background-color', 'red');
                }

                
            },

            
            eventClick: function(event) {
                var eventId = event.appointment_id;
                console.log(event)
                $.ajax({
                    type: "GET",
                    url: SITEURL + '/new-calendar-hospital/appointment-details',
                    data: {
                        id: eventId,
                    },
                    success: function(response) {
                        $('#appointment_date_set').text(response.data.appointment_date);
                        $('#appointment_time_set').text(response.data.appointment_time);

                        var firstName = response.data.patient.first_name;
                        var middleName = response.data.patient.middle_name;
                        var lastName = response.data.patient.last_name;
                        var fullName = firstName + ' ' + middleName + ' ' + lastName;

                        $('#full_name_set').text(fullName);
                        $('#dob_set').text(response.data.patient.dob);
                        $('#diciplin_set').text(response.data.patient.diciplin);
                        $('#mobile_set').text(response.data.patient.mobile);
                    }
                });
            }
        });

        if (fu_date !== "") {
            calnedr.fullCalendar('gotoDate', moment(fu_date).format('YYYY-MM-DD'));
        }
    });
</script>
