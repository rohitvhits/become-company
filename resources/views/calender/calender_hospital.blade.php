@include('include/header')
@include('include/sidebar')
<style>
    .fc-body.fc-widget-content {
        height: 400px;
        overflow: scroll;
    }

    .select2-design+.select2-container--default .select2-selection--single {
        padding: 0;
        min-height: 38px;
    }

    .select2-design+.select2-container {
        min-width: 158px;
        /* max-width: 200px; */
        min-height: 38px;
        /* margin-left: 20px; */
        margin-left: -2% !important;
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

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    /* .fc-content span {
    color: #000 !important;
} */
    .custom-black-text .fc-time,
    .custom-black-text .fc-title {
        color: black !important;
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
            <h5 class="mb-0 font-weight-bold">Calendar</h5>
            @can('dashboard-calender')
            <div class="page-rightbtns" style="margin-right:22px">
                <a class="btn btn-primary" href="{{ url('/dashboard/new-calendar-design-appointment')}}" target="_blank">Calender V2</a>
            </div>
            @endcan
        </div>
        <div class="row">
            <div class="col-md-12 row">
                <div class="col-md-8 ">
                    <div class="card card-body">
                        <div class="right-align">
                            <form method="get" id="formsubmit" >
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <select name="emclist[]" class="form-control js-example-basic-multiple w-100 cal-padding-0" multiple="multiple" id="emc_id">
                                                @if(!empty($userList))
                                                @foreach ($userList as $kyy)

                                                <option value="{{$kyy->id}}" {{ in_array($kyy->id, $emd_rep_idss) ? 'selected' : '' }}>
                                                    {{ucfirst($kyy->agency_name)}}
                                                </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                   
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <select name="locationlist" class="form-control select2-design" id="location_id">
                                                <option value="">Select Location</option>
                                                @if (!empty($locationList))
                                                @foreach ($locationList as $locationkey)
                                                <option value="{{ $locationkey->id }}" {{ $locationId == $locationkey->id ? 'selected' : '' }}>
                                                    <!-- {{ ucfirst($locationkey->location_name) }} -->
                                                    {{ ucfirst($locationkey->address1) }}
                                                </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <select name="record_type" class="form-control w-100 select2-design"   id="record_type">
                                                <option value="">All</option>
                                                <option value="Caregiver" @if($record_type =='Caregiver') selected @endif>Caregiver</option>
                                                <option value="Patient" @if($record_type =='Patient') selected @endif>Patient</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3"> 
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="calender-design position-relative">
                                                    <input type="text" name="fudate" value="@if (!empty($fuDate)) {{ date('m/d/Y',strtotime($fuDate)) }} @endif" autocomplete="off" class="form-control datepicker " id="fu_date" placeholder="Select FU Date" readonly>
                                                    <i class="fa fa-calendar date-calender" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-success seachCalender calenderbtn-design" type="button">Search</button>
                                        <a href="{{ URL::to('/') }}/dashboard/calendar-hospital" class="btn btn-light calenderbtn-design" type="reset" name="Reset">Reset</a>

                                    </div>
                                </div>
                               
                            </form>
                            {{-- <button class="btn btn-primary calenderbtn-design" onclick="getModelss()">Print</button> --}}
                        </div>



                        <div id="calendar" class="full-calendar"></div>
                    </div>
                </div>
                <div class="col-md-4 ">
                
                    <div class="card" style="height:873px;overflow-y:auto;">
                        <div class="card-body">
                            <h4 class="card-title">Recent Notes</h4>
                            <div class="row">
                                <span id="recent_notes_response">

                                </span>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-default-patient" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Print Calendar </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo URL::to('/'); ?>/dashboard/generate-pdf" method="post" enctype="multipart/form-data" id="fomtprint">
                    <div class="modal-body">
                        <input type="hidden" name="agency_id" value="<?php echo $emd_rep_id; ?>">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Start Date<span style="color:red">*</span>:</label>
                            <div class="col-sm-8">
                                <input type="text" name="start_date" autocomplete="off" class="form-control datepicker" id="start_date">

                            </div>
                            <span id="radios_error" style="color:red"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">End Date<span style="color:red">*</span>:</label>
                            <div class="col-sm-8">
                                <input type="text" name="end_date" autocomplete="off" class="form-control datepicker" id="end_date">
                                <span id="service_id_error" style="color:red"></span>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
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
        $('.datepicker').datepicker();
        $(document).ready(function() {


            /* For Agency Filter */
            var emc_id = $('#emc_id').val();
            var location_id = $('#location_id').val();
            var assign_id = $('#assign_id').val();
            var fu_date = $('#fu_date').val();

            // $.ajax({
            //     url: "<?php echo URL::to('/'); ?>/dashboard/dashboard-hosiptal-calander",
            //     type: 'get', // Send post data
            //     data: {
            //         id: emc_id,
            //         loc_id: location_id,
            //         ass_id: assign_id,
            //         fdt: fu_date
            //     },
            //     async: false,
            //     success: function(s) {
            //         json_events = s;
            //     }
            // });


            var calnedr = $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,agendaDay,listWeek,print'
                },
                aspectRatio: 1.5,
                eventLimit: false, 
                dayMaxEvents: 1,
                defaultView: 'basicWeek',
                navLinks: true, // can click d,ay/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                //  events: JSON.parse(json_events),
                events: function(start, end, timezone, callback) {
                    var startDate = moment(start).format("YYYY-MM-DD");
                    var endDate = moment(end).format("YYYY-MM-DD");
                    //   calendarLoader('#calendar-wrapper', 'show', 'loader-min-500');
                    var emc_id = $('#emc_id').val();
                    var location_id = $('#location_id').val();
                    var assign_id = $('#assign_id').val();
                    var fu_date = $('#fu_date').val();
                    var record_type = $('#record_type').val();
                    $.ajax({
                        url: "{{URL::to('/')}}/dashboard/getfollowupdates",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            start: startDate,
                            end: endDate,
                            id: emc_id,
                            loc_id: location_id,
                            ass_id: assign_id,
                            fdt: fu_date,
                            record_type:record_type,
                            _token: "{{csrf_token()}}"
                        },
                        success: function success(doc) {
                            callback(doc);
                        }
                    });
                },
                eventRender: function(event, eventElement, eventColor) {
                //    console.log('event.status', event.status)
                    if (event.type == 'Caregiver' || event.type == 'Patient') {
                        eventElement.css('background-color', 'blue');
                    } else {
                        eventElement.css('background-color', 'green');
                    }
                    if (event.type == "task") {
                        eventElement.css('background-color', 'orange');
                    }


                    if (event.status == "processing") {
                        eventElement.css('background-color', 'grey');
                    }
                    if (event.status == "booked") {
                        eventElement.css('background-color', 'blue');
                    }
                    if (event.status == "Completed") {
                        eventElement.css('background-color', 'green');
                    }
                    if (event.status == "pending") {
                        eventElement.css('background-color', 'yellow');
                        eventElement.addClass("custom-black-text");
                    }
                    if (event.status == "refused" || event.status == "cancelled" ||
                        event.status == "noshow" || event.status == " no answer" ||
                        event.status == "unableToContact") {
                        eventElement.css('background-color', 'red');
                    }
                },

            }) 
            if (fu_date != "") {
                calnedr.fullCalendar('gotoDate', moment(fu_date).format('YYYY-MM-DD'));
            }
 
        });
    </script>
    <script>
        $(document).on("click", ".seachCalender", function() {
            var id = $('#emc_id').val();
            var lid = $('#location_id').val();
            var aid = $('#assign_id').val();
            var fdate = $('#fu_date').val();
            var record_type = $('#record_type').val();
            var links = "<?php echo URL::to('/'); ?>/dashboard/calendar-hospital?emclist=" + id + "&locationlist=" + lid +
                "&assignlist=" + aid + "&fudate=" + fdate+'&record_type='+record_type;
            window.location.href = links;

        });
        $("#emc_id").select2({
            placeholder: "Select Agency"
        });
        $("#assign_id").select2({
            placeholder: "Select Assign User"
        });
        $('#location_id').select2();

        function getModelss() {
            $('#modal-default-patient').modal('show');
        }

        $('#fomtprint').submit(function(e) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var cnt = 0;
            if (start_date.trim() == '') {
                $('#radios_error').html("Required");
                cnt = 1;
            }
            if (end_date.trim() == '') {
                $('#service_id_error').html("Required");
                cnt = 1;
            }

            if (cnt == 1) {
                return false;
            } else {

            }
        });

        function loadRecentNotes(){
            $.ajax({
                url: "{{ url('latest-recent-notes')}}",
                type: 'GET',
              
                success:function(res){
                    var json = res.data;
                    var htmlResponse ="";
                    if(res.data.length !=0){
                        $.each(json,function(i,v){
                            var urls ="{{ url('/patient/view/')}}/"+v.patient_id;
                            htmlResponse +=`<div class="d-flex align-items-center py-2 border-bottom">

									<div class="ml-1">
										<h6 class="mb-1"><a href="${urls}">Record #${v.patient_id} ${v.patient.first_name+' '+v.patient.last_name}</a></h6>
										<p style="white-space: pre-wrap;">${v.message}</p>
										<p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i>${v.user_details.agency_details.agency_name} ${v.created_date}</p>
										<div class="row">
										<p class="text-muted mb-0 tx-12" style="margin-left:12px;">${v.user_details.first_name+' '+v.user_details.last_name}</p>
										
										</div>
										
									</div>

									</div>`
                        })
                    }

                    $('#recent_notes_response').html("");
                    $('#recent_notes_response').html(htmlResponse);
                 
                }
            });
            return false;
        }
        loadRecentNotes();
    </script>