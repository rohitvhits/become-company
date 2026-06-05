@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link href="{{ asset('/assets/bootstrap-datetimepicker.min.css')}}" type="text/css" media="all" rel="stylesheet" />
<link href="{{ asset('/assets/modulejs/css/telehealth_location.css')}}" type="text/css" media="all" rel="stylesheet" />
<link href="<?php echo URL::to('/'); ?>/assets/css/global.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/modulejs/css/task-module.css')}}" type="text/css" media="all" rel="stylesheet" />
<div class="main-panel">
     @php
         $auth = auth()->user();
     @endphp
     <div class="content-wrapper">
         <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Telehealth Location Schedule</h5>
         </div>

         <!-- Filter Section -->
         <div class="card common-card-box mb-3">
            <div class="card-body">
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
                <div class="action-buttons " style="display: none;">
                    <div class="pull-right button-group">
                        <button type="button" class="btn btn-cancel mr-1" id="cancelSelectionBtn">
                            <i class="fa fa-times"></i> Cancel Selection
                        </button>
                        <button type="button" class="btn btn-success" id="saveEventsBtn">
                            <i class="fa fa-save"></i> Save Selected Time Slots
                        </button>
                    </div>
                </div>
            </div>
         </div>
        
     </div>
</div>
<script>
    var GET_LOCATION_SCHEDULES = "{{ url('get-location-schedules') }}";
    var TELEHEALTH_LOCATION_SCHEDULE_AJAX = "{{ url('telehealth-location-schedule-ajax') }}";
    var SAVE_SELECTED_EVENTS = "{{ url('save-selected-events') }}";
    var CHECK_NURSE_SCHEDULE = "{{ url('check-nurse-schedule') }}";
    var UPDATE_NURSE_SCHEDULE = "{{ url('update-nurse-schedule') }}";
    var CSRF_TOKEN = "{{ csrf_token() }}";

    // Handle location change
    function getSchedule(locationId) {
        // Reset schedule dropdown
        $('#schedule').empty().append('<option value="">Select Schedule</option>');
        
        // Reset nurse dropdown
        $('#nurse').val('').trigger('change');
        
        // Hide schedule info
        $('#scheduleInfo').hide();
        
        // Reset schedule display
        $('#daysEventsList').html('<div class="no-schedule"><i class="fa fa-calendar-alt fa-3x mb-3"></i><p>Please select location, schedule, and nurse to view available time slots.</p></div>');
        
        // Hide action buttons
        $('.action-buttons').hide();
        
        // Reset buttons state
        $('#getCalendarBtn').show();
        $('#cancelBtn').hide();
        
        if (locationId) {
            $.ajax({
                url: GET_LOCATION_SCHEDULES,
                method: 'POST',
                data: {
                    location_id: locationId,
                    _token: CSRF_TOKEN
                },
                success: function(response) {
                    if (response.status) {
                        $.each(response.schedules, function(key, schedule) {
                            $('#schedule').append('<option value="' + schedule.id + '">' + schedule.title + ' ('+schedule.start_time+'-'+schedule.end_time+')' + '</option>');
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading schedules');
                }
            });
        }
    }

    // Handle nurse selection
    $('#nurse').on('change', function() {
        var locationId = $('#tele_location').val();
        var nurseId = $(this).val();
        var scheduleId = $('#schedule').val();

        if (locationId != "" && nurseId != "" && scheduleId != "") {
            console.log('Hello');
            checkNurseSchedule(locationId, nurseId, scheduleId);
            $('#getCalendarBtn').trigger('click');
        }
    });
    $('#schedule').on('change', function() {
        var locationId = $('#tele_location').val();
        var nurseId = $('#nurse').val();
        var scheduleId = $(this).val();
        if (locationId != "" && nurseId != "" && scheduleId != "") {
            checkNurseSchedule(locationId, nurseId, scheduleId);
            $('#getCalendarBtn').trigger('click');
        }
    });
    
    function checkNurseSchedule(locationId, nurseId, scheduleId) {
        $.ajax({
            url: CHECK_NURSE_SCHEDULE + '/' + locationId + '/' + nurseId + '/' + scheduleId,
            method: 'GET',
            success: function(response) {
                if (response.status && response.exists) {
                    // Clear existing selections
                    $('.slot-checkbox').prop('checked', false);
                    $('.time-slot').removeClass('selected');

                    // Select the existing schedule slots
                    $.each(response.schedules, function(index, schedule) {
                        var slotId = schedule.slot_id;
                        var day = schedule.day;
                        $('input[type="checkbox"][data-slot-id="' + slotId + '"][data-day="' + day + '"]').prop('checked', true);
                        $('input[type="checkbox"][data-slot-id="' + slotId + '"][data-day="' + day + '"]').closest('.time-slot').addClass('selected');
                    });
                }
            },
            error: function(xhr) {
                console.error('Error checking nurse schedule');
            }
        });
    }

    // Handle Cancel button click
    $('#cancelBtn').on('click', function() {
        // Reset form
        $('#scheduleFilterForm')[0].reset();
        $('.select2').val(null).trigger('change');
        
        // Hide schedule info and events
        $('#scheduleInfo').hide();
        $('#daysEventsList').html('<div class="no-schedule"><i class="fa fa-calendar-alt fa-3x mb-3"></i><p>Please select location, schedule, and nurse to view available time slots.</p></div>');
        $('.action-buttons').hide();
        
        // Toggle buttons
        $(this).hide();
        $('#getCalendarBtn').show();
    });

    // Handle Cancel Selection button click
    $('#cancelSelectionBtn').on('click', function() {
        // Clear all selections
        $('.slot-checkbox').prop('checked', false);
        $('.slot-checkbox').trigger('change');
        $('.time-slot').removeClass('selected');
        $('.action-buttons').hide();
    });

    // Handle Get Schedule button click
    $('#getCalendarBtn').on('click', function() {
        var locationId = $('#tele_location').val();
        var scheduleId = $('#schedule').val();
        var nurseId = $('#nurse').val();

        if (locationId == "" || nurseId == "" || scheduleId == "") {
            toastr.error('Please select location, schedule, and nurse to view available time slots.')
        } else {
            // Show shimmer loading effect
            var shimmerHtml = '<div class="shimmer-container">';
            shimmerHtml += '<div class="schedule-table">';
            shimmerHtml += '<div class="schedule-table-header columns-7">';
            
            // Add 7 columns for days
            for (var i = 0; i < 7; i++) {
                shimmerHtml += '<div class="shimmer-column">';
                shimmerHtml += '<div class="shimmer shimmer-day"></div>';
                // Add 10 time slots per day
                for (var j = 0; j < 10; j++) {
                    shimmerHtml += '<div class="shimmer shimmer-time-slot"></div>';
                }
                shimmerHtml += '</div>';
            }
            
            shimmerHtml += '</div></div></div>';
            
            $('#daysEventsList').html(shimmerHtml);
            $('.shimmer-container').show();

            // Update schedule info
            $('#selectedLocation').text($('#tele_location option:selected').text());
            $('#selectedSchedule').text($('#schedule option:selected').text());
            $('#selectedNurse').text($('#nurse option:selected').text());
            $('#scheduleInfo').show();

            // Toggle buttons
            $(this).hide();
            $('#cancelBtn').show();

            // First check if there are any existing schedules for this nurse
            $.ajax({
                url: CHECK_NURSE_SCHEDULE + '/' + locationId + '/' + nurseId + '/' + scheduleId,
                method: 'GET',
                success: function(scheduleResponse) {
                    if (scheduleResponse.status) {
                        // Then get the calendar data
                        $.ajax({
                            url: TELEHEALTH_LOCATION_SCHEDULE_AJAX,
                            method: 'POST',
                            data: {
                                location_id: locationId,
                                schedule_id: scheduleId,
                                nurse_id: nurseId,
                                _token: CSRF_TOKEN
                            },
                            success: function(response) {
                                if (response.status) {
                                    displayDaysAndEvents(response.days_events);
                                    
                                    // If there are existing schedules, select the checkboxes
                                    if (scheduleResponse.exists) {
                                        var daySelections = {};
                                        
                                        $.each(scheduleResponse.schedules, function(index, schedule) {
                                            var slotId = schedule.slot_id;
                                            var day = schedule.day;
                                            var checkbox = $('input[type="checkbox"][data-slot-id="' + slotId + '"][data-day="' + day + '"]');
                                            if (checkbox.length) {
                                                checkbox.prop('checked', true);
                                                checkbox.closest('.time-slot').addClass('selected');
                                                
                                                // Track selections per day
                                                if (!daySelections[day]) {
                                                    daySelections[day] = 0;
                                                }
                                                daySelections[day]++;
                                            }
                                        });

                                        // Update select all checkboxes based on selections
                                        $.each(daySelections, function(day, count) {
                                            var daySlots = $('.time-slot[data-day="' + day + '"]').length;
                                            var selectAllCheckbox = $('#select-all-' + day);
                                            
                                            if (count === daySlots) {
                                                selectAllCheckbox.prop('checked', true);
                                                selectAllCheckbox.prop('indeterminate', false);
                                            } else if (count > 0) {
                                                selectAllCheckbox.prop('checked', false);
                                                selectAllCheckbox.prop('indeterminate', true);
                                            }
                                        });
                                    }
                                    
                                    $('.action-buttons').show();
                                }
                            },
                            error: function(xhr) {
                                console.error('Error loading events');
                                toastr.error('Error loading schedule data');
                                // Hide shimmer on error
                                $('.shimmer-container').hide();
                            }
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error checking nurse schedule');
                    toastr.error('Error checking existing schedules');
                    // Hide shimmer on error
                    $('.shimmer-container').hide();
                }
            });
        }
    });

    function displayDaysAndEvents(daysEvents) {
        if (daysEvents.length === 0) {
            $('#daysEventsList').html(`
                <div class="no-schedule">
                    <i class="fa fa-calendar"></i>
                    <p>No schedule found for the selected criteria.</p>
                </div>
            `);
            return;
        }

        var columnCount = daysEvents.length;
        var html = '<div class="schedule-table">';
        html += '<div class="schedule-table-header columns-' + columnCount + '">';
        
        daysEvents.forEach(function(dayEvent) {
            html += '<div class="day-column">';
            html += '<div class="day-header">';
            html += '<span class="day-label">' + dayEvent.day + '</span>';
            html += '<div class="select-all-container">';
            html += '<input type="checkbox" class="select-all-checkbox" data-day="' + dayEvent.day + '" id="select-all-' + dayEvent.day + '">';
            html += '</div>';
            html += '</div>';
            html += '<div class="time-slots">';
            dayEvent.slots.forEach(function(slot) {
                html += '<div class="time-slot" data-slot-id="' + slot.id + '" ' +
                       'data-day="' + dayEvent.day + '" ' +
                       'data-start="' + slot.start_time + '" ' +
                       'data-end="' + slot.end_time + '">';
                html += '<input type="checkbox" class="slot-checkbox" ' +
                       'data-slot-id="' + slot.id + '" ' +
                       'data-day="' + dayEvent.day + '" ' +
                       'data-start-time="' + slot.start_time + '" ' +
                       'data-end-time="' + slot.end_time + '">';
                html += '<span class="time-slot-text">' + slot.start_time + ' - ' + slot.end_time + '</span>';
                html += '</div>';
            });
            html += '<div class="validation-message"></div>';
            html += '</div>';
            html += '</div>';
        });
        
        html += '</div>';
        html += '</div>';
        
        // Add Copy Schedule button outside the table
        html += '<div class="copy-schedule-container mt-3">';
        html += '<button type="button" class="btn btn-info copy-schedule-btn btn-sm">';
        html += '<i class="fa fa-copy"></i> Copy Schedule';
        html += '</button>';
        html += '<button type="button" class="btn btn-warning reset-schedule-btn ml-2 btn-sm">';
        html += '<i class="fa fa-undo"></i> Reset';
        html += '</button>';
        html += '</div>';
        
        $('#daysEventsList').html(html);

        // Add click handlers for checkboxes
        $('.slot-checkbox').on('change', function(e) {
            e.stopPropagation();
            var timeSlot = $(this).closest('.time-slot');
            var day = timeSlot.data('day');
            
            if ($(this).prop('checked')) {
                timeSlot.addClass('selected');
            } else {
                timeSlot.removeClass('selected');
            }
            
            // Update select all checkbox state
            updateSelectAllCheckbox(day);
            updateActionButtons();
        });

        // Add click handlers for time slots
        $('.time-slot').on('click', function(e) {
            if (e.target.type !== 'checkbox') {
                var checkbox = $(this).find('.slot-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            }
        });

        // Add click handler for select all checkboxes
        $('.select-all-checkbox').on('change', function() {
            var day = $(this).data('day');
            var isChecked = $(this).prop('checked');
            var timeSlots = $('.time-slot[data-day="' + day + '"]');
            
            timeSlots.find('.slot-checkbox').prop('checked', isChecked).trigger('change');
        });

        // Add click handler for copy schedule button using event delegation
        $(document).on('click', '.copy-schedule-btn', function() {
            // Store current state before copying
            var previousState = [];
            $('.day-column').each(function() {
                var columnState = [];
                $(this).find('.time-slot').each(function() {
                    columnState.push({
                        slotId: $(this).data('slot-id'),
                        day: $(this).data('day'),
                        isSelected: $(this).hasClass('selected')
                    });
                });
                previousState.push(columnState);
            });
            $(this).data('previous-state', previousState);

            // Get all selected slots from the first column
            var firstColumnSlots = $('.day-column:first .time-slot.selected');
            if (firstColumnSlots.length === 0) {
                toastr.error('Please select time slots in the first column to copy');
                return;
            }

            // Get the slot IDs from the first column
            var slotIds = [];
            var flg = 0;
            firstColumnSlots.each(function() {
                slotIds.push($(this).data('slot-id'));
            });

            // Copy to all other columns
            $('.day-column:not(:first)').each(function() {
                flg++; 
                var currentColumn = $(this);
                // Clear all selections in this column
                currentColumn.find('.time-slot').removeClass('selected');
                currentColumn.find('.slot-checkbox').prop('checked', false);

                // Select the same slots as in the first column
                currentColumn.find('.time-slot').each(function() {
                    var slotId = $(this).data('slot-id');
                    if (slotIds.includes(slotId)) {
                        $(this).addClass('selected');
                        $(this).find('.slot-checkbox').prop('checked', true);
                    }
                });

                // Update the select all checkbox for this column
                var day = currentColumn.find('.time-slot').first().data('day');
                updateSelectAllCheckbox(day);
            });

            console.log(flg);
            if(flg > 1){
                toastr.success('Schedule copied successfully to all columns');
            }
        });

        // Add click handler for reset button
        $(document).on('click', '.reset-schedule-btn', function() {
            var copyButton = $('.copy-schedule-btn');
            var previousState = copyButton.data('previous-state');
            
            if (!previousState) {
                toastr.error('No previous state to restore');
                return;
            }

            // Restore previous state
            $('.day-column').each(function(columnIndex) {
                var columnState = previousState[columnIndex];
                var currentColumn = $(this);
                
                // Clear current selections
                currentColumn.find('.time-slot').removeClass('selected');
                currentColumn.find('.slot-checkbox').prop('checked', false);

                // Restore previous selections
                columnState.forEach(function(slotState) {
                    if (slotState.isSelected) {
                        var slot = currentColumn.find('.time-slot[data-slot-id="' + slotState.slotId + '"][data-day="' + slotState.day + '"]');
                        slot.addClass('selected');
                        slot.find('.slot-checkbox').prop('checked', true);
                    }
                });

                // Update select all checkbox
                var day = currentColumn.find('.time-slot').first().data('day');
                updateSelectAllCheckbox(day);
            });

            toastr.success('Schedule restored to previous state');
        });
    }

    function updateSelectAllCheckbox(day) {
        var daySlots = $('.time-slot[data-day="' + day + '"]');
        var checkedSlots = daySlots.find('.slot-checkbox:checked');
        var selectAllCheckbox = $('#select-all-' + day);
        
        if (checkedSlots.length === 0) {
            selectAllCheckbox.prop('checked', false);
            selectAllCheckbox.prop('indeterminate', false);
        } else if (checkedSlots.length === daySlots.length) {
            selectAllCheckbox.prop('checked', true);
            selectAllCheckbox.prop('indeterminate', false);
        } else {
            selectAllCheckbox.prop('checked', false);
            selectAllCheckbox.prop('indeterminate', true);
        }
    }

    function updateActionButtons() {
        var hasSelectedSlots = $('.time-slot.selected').length > 0;
        $('.action-buttons').toggle(hasSelectedSlots);
    }

    // Handle Save Selected Events button click
    $('#saveEventsBtn').on('click', function() {
        var locationId = $('#tele_location').val();
        var nurseId = $('#nurse').val();
        var scheduleId = $('#schedule').val();
        var selectedEvents = [];

        $('.time-slot.selected').each(function() {
            var day = $(this).data('day');

            selectedEvents.push({
                slot_id: $(this).data('slot-id'),
                day: day,
                start_time: $(this).data('start'),
                end_time: $(this).data('end')
            });
        });


        $.ajax({
            url: UPDATE_NURSE_SCHEDULE,
            method: 'POST',
            data: {
                location_id: locationId,
                nurse_id: nurseId,
                schedule_id: scheduleId,
                events: selectedEvents,
                _token: CSRF_TOKEN
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Error updating schedule');
            }
        });
    });

</script>
@include('include/footer')
<link rel="stylesheet" href="{{ asset('css/jquery-ui.css')}}">
<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<style>
    .schedule-container {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    
    /* Shimmer Effect Styles */
    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes shimmer {
        0% {
            background-position: 0% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    .shimmer-day {
        height: 40px;
        margin-bottom: 10px;
    }

    .shimmer-time-slot {
        height: 30px;
        margin: 5px 0;
    }

    .shimmer-container {
        display: none;
        padding: 15px;
    }

    .shimmer-column {
        flex: 1;
        margin: 0 10px;
    }
    
    .day-header {
        background: #eeeeee;
        padding: 8px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
    }
    
    .day-label {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }
    
    .select-all-container {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .select-all-checkbox {
        width: 16px;
        height: 16px;
        margin: 0;
    }
    
    .select-all-container label {
        font-size: 12px;
        color: #666;
        cursor: pointer;
    }
    
    .time-slot {
        padding: 6px 10px;
        margin: 4px 0;
        border-radius: 4px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .time-slot.selected {
        background: #e3f2fd;
    }
    
    .slot-checkbox {
        width: 14px;
        height: 14px;
        margin: 0;
    }
    
    .time-slot-text {
        font-size: 12px;
        color: #333;
    }

    .copy-schedule-container {
        background: #f8f9fa;
        border-radius: 4px;
        margin-top: 20px;
    }

    .copy-schedule-container .btn {
        font-size: 14px;
        border-radius: 4px;
        color: #fff;
        transition: all 0.3s ease;

    }

    .copy-schedule-container .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .copy-schedule-container .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .copy-schedule-container .btn:hover {
        opacity: 0.9;
    }

    .copy-schedule-container .btn i {
        margin-right: 8px;
    }
</style>


