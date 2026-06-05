@include('include/header')
@include('include/sidebar')
<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .select2-design+.select2-container--default .select2-selection--single {
        padding: 0;
        min-height: 38px;
    }

    .select2-design+.select2-container {
        min-width: 158px;
        max-width: 158px;
        min-height: 38px;
        margin-left: 20px;
        height: 100%;
        width: 100%;
    }

    .cal-padding-0+.select2-container {
        margin-left: 0 !important;
    }

    .select2-design+.select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }

    .select2-design+.select2-container--default .select2-selection--single .select2-selection__arrow {
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

    .select2-design+.select2-container .select2-selection--multiple {
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
</style>
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/libs/jquery-ui/jquery-ui.css">

<!-- partial -->
<!-- partial:../../partials/_settings-panel.html -->

<!-- partial -->
<!-- partial:../../partials/_sidebar.html -->

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Telehealth Calendar</h5>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="right-align">
                            <form method="get" id="formsubmit">
                                <div class="d-flex">
                                <div class="form-group">
                                    <select name="emclist[]" class="form-control js-example-basic-multiple w-100 select2-design cal-padding-0" multiple="multiple" id="emc_id">
                                        @if(!empty($agencyList)) {
                                        @foreach ($agencyList as $kyy) {
                                        <option value="{{$kyy->id}}" {{ in_array($kyy->id, $emd_rep_idss) ? 'selected' : '' }}>
                                            {{ucfirst($kyy->agency_name)}}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select> &nbsp;&nbsp;&nbsp;
                                    </div>
                                    <div class="form-group">
                                    <select name="service_id[]" class="form-control js-example-basic-multiple w-100 select2-design cal-padding-0" multiple="multiple" id="service_id">
                                        @if(!empty($serviceList)) {
                                        @foreach ($serviceList as $service) {
                                        <option value="{{$service->id}}" {{ in_array($service->id, $service_ids) ? 'selected' : '' }}>
                                            {{ucfirst($service->name)}}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>&nbsp;&nbsp;&nbsp;
                                    </div>
                                    <div class="form-group">
                                    <select name="search_type" class="form-control w-100 select2-design cal-padding-0" id="search_type">
                                        <option value="">Please select type</option>
                                        <option value="Patient" {{$search_type=='Patient' ? 'selected' : ''}}>Patient</option>
                                        <option value="Caregiver" {{$search_type=='Caregiver' ? 'selected' : ''}}>Caregiver</option>
                                    </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="calender-design position-relative">
                                                <input type="text" name="search_date" value="@if (!empty($searchDate)) {{ date('m/d/Y',strtotime($searchDate)) }} @endif" autocomplete="off" class="form-control datepicker " id="search_date" placeholder="Select Date" readonly>
                                                <i class="fa fa-calendar date-calender" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-success seachCalender calenderbtn-design" type="button">Search</button>
                                    <a href="{{ URL::to('/') }}/patient-calendar" class="btn btn-light calenderbtn-design" type="reset" name="Reset">Reset</a>

                                </div>
                            </form>
                        </div>
                        <div id="calendar" class="full-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar" class="full-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('include/footer')
    <script src="<?php echo URL::to('/'); ?>/assets/vendors/moment/moment.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/libs/jquery-ui/jquery-ui.js">
    <!-- End plugin js for this page -->
    <!-- Custom js for this page-->
    <script>
        $(document).on("click", ".seachCalender", function() {
            var id = $('#emc_id').val();
            var service_id = $('#service_id').val();
            var search_date = $('#search_date').val();
            var search_type = $('#search_type').val();
            var links = "<?php echo URL::to('/'); ?>/patient-calendar?emclist=" + id + 
              "&search_date=" + search_date +"&service_id=" + service_id+"&search_type=" + search_type;
            window.location.href = links;

        });
        $("#emc_id").select2({
            placeholder: "Select Assign User"
        });
        $("#service_id").select2({
            placeholder: "Select Service"
        });
        
        $('.datepicker').datepicker();
        $(document).ready(function() {
                /* For Agency Filter */
            var emc_id = $('#emc_id').val();
            var search_date = $('#search_date').val();
            var service_id = $('#service_id').val();
            var search_type = $('#search_type').val();
            $.ajax({
                url: "{{ url('patient-ajax-calender') }}",
                type: 'get', // Send post data
                data: {
                    id: emc_id,
                    search_date: search_date,
                    service_id: service_id,
                    search_type: search_type,
                },
                async: false,
                success: function(s) {
                    json_events = s;
                    console.log(json_events);
                }
            });
            var calnedr = $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,agendaDay,listWeek,print'
                },
                defaultView: 'month',
                navLinks: true, // can click d,ay/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                events: JSON.parse(json_events),
                timeFormat: 'H:mm A',
                eventRender: function(event, eventElement) {
                    if (event.type == 'Caregiver') {
                        eventElement.css('background-color', 'blue');
                    } else {
                        eventElement.css('background-color', 'green');
                    }
                    if (event.status == "cancelled") {
                        eventElement.css('text-decoration', 'line-through');
                        eventElement.css('color', 'red');
                    }
                },

            })
            if(search_date!=""){
                calnedr.fullCalendar('gotoDate', moment(search_date).format('YYYY-MM-DD'));
            }

        });
    </script>