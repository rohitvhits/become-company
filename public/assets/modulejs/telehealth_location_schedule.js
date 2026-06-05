$(document).on('click', '.log-pegination .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    getData(page);
});

$(document).ready(function() {
    $('.nav-item').removeClass('active');
    getLocationTelehealthSchedule(1);

    // Initialize select2 for manual nurse selection
    // $('#manual_nurse').select2({
    //     theme: 'bootstrap'
    // });
});

function getData(page) {

    var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

    $.ajax({
        method: 'GET',
        url: SCHEDULE_LOG + "?page=" + page,
        data: {
            '_token': CSRF_TOKEN
        },
        success: function success(response) {

            $('.order-listing-loader').attr('style', 'display:none');
            $('#logList').html("");
            $('#logList').html(response);
        },
        error: function error(_error) {
            toastr.error('Something happened. Try again');
        }
    });
}

function statusChange(id){
    var is_disabled = $('#is_disabled'+id).prop('checked') == true ? 1 : 0;
    if(is_disabled == 0){
        content = 'you want to disable Location Schedule?';
    }else{
        content = 'you want to enable Location Schedule?';
    }
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        method: 'GET',
                        url: SCHEDULE_STATUS,
                        data: {
                            'id': id,
                        },
                        success:function(data){
                            toastr.success(data.message);
                            var status ="";
                            if(data.data.status =="N"){
                                status ="<span class='badge badge-success'>Enabled</span>";
                            }else{
                                status ="<span class='badge badge-danger'>Disabled</span>"; 
                            }
                            $('#row_'+id).html(status)
                        }, 
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg);
                            var lastStatus = $('#is_disabled'+id).prop('checked');
                            if(lastStatus){
                                $('#is_disabled'+id).prop("checked",false);
                            }else{
                                $('#is_disabled'+id).prop("checked",true);
                            }
                        }
                    })
                }
            },
            cancel: function() {
                //close
                var lastStatus = $('#is_disabled'+id).prop('checked');
                if(lastStatus){
                    $('#is_disabled'+id).prop("checked",false);
                }else{
                    $('#is_disabled'+id).prop("checked",true);
                }
            },
        },
    });

}

function addTeleLocationSchedule(){
    var form = $('#addTelehealthSchedule')[0];
    var formData = new FormData(form);
    cnt = 0;
    $('#title_error').html('');
    $('#day_error').html('');
    $('#start_error').html('');
    $('#end_time_error').html('');
    $('#slot_error').html('');
    
    if(!formData.get('title')) {
        $('#title_error').html('Please enter title');
        cnt++;
    }
    if(!formData.get('days[]')) {
        $('#day_error').html('Please select at least one day');
        cnt++;
    }
    if(!formData.get('start_time')) {
        $('#start_error').html('Please select schedule start time');
        cnt++;
    }
    if(!formData.get('end_time')) {
        $('#end_time_error').html('Please select schedule end time');
        cnt++;
    }
    if(formData.get('start_time') && formData.get('end_time')) {
        if(formData.get('start_time') > formData.get('end_time')) {
            $('#start_error').html('Start time should be less than end time');
            cnt++;
        } else if(formData.get('start_time') == formData.get('end_time')) {
            $('#end_time_error').html('Start Time and End Time should not be same');
            cnt++;
        }    
    }
    if(!formData.get('slot')) {
        $('#slot_error').html('Please enter slot');
        cnt++;
    }else{
        let start = new Date('1970-01-01T' + formData.get('start_time'));
        let end = new Date('1970-01-01T' + formData.get('end_time'));
        let diffInMinutes = (end - start) / (1000 * 60);
        if (formData.get('slot') > diffInMinutes) {
            $('#edit_slot_error').html("Slot time must not be greater than the duration between start and end time.");
            cnt++;
        }
    }

    if(!formData.get('telehealth_config_type')) {
        $('#telehealth_config_type_error').html('Please select Telehealth configuration type');
        cnt++;
    }
    if(!formData.get('location_id')) {
        $('#location_id_error').html('Please select Location');
        cnt++;
    }
    if(cnt == 0) {
        $('#loader_add').attr('style','display:');
        $.ajax({
            type: 'POST',
            url: TELEHEALTH_LOCATION_SCHEDULE + '/save',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status) {
                    toastr.success(data.error_msg);
                    getLocationTelehealthSchedule(1);
                    $('#loader_add').attr('style','display:none');
                    $('#addTelehealthSchedule')[0].reset();
                    $('#day_id').val(null).trigger('change');
                    $('#addModal').modal('hide');
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(jqr) {
                console.log(jqr);
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}

function getLocationTelehealthSchedule(page){
    $('#location_resp_id').html("");
    $('.tele-loc-wise-data-loader').attr('style', 'display:block');
    $.ajax({
        method: 'GET',
        url: TELEHEALTH_SCHEDULE_AJAX,
        data: {
            'page': page,
            '_token': CSRF_TOKEN
        },
        success: function success(response) {
            $('.tele-loc-wise-data-loader').attr('style', 'display:none');
            $('#location_resp_id').html("");
            $('#location_resp_id').html(response);
        },
        error: function error(_error) {
            toastr.error('Something happened. Try again');
        }
    });
}

function getEditModelData(id) {
    $('#edit_title_error').html('');
    $('#edit_day_error').html('');
    $('#edit_start_error').html('');
    $('#edit_end_time_error').html('');
    $('#edit_slot_error').html('');
    
    $.ajax({
        type: 'GET',
        url: TELEHEALTH_LOCATION_SCHEDULE + '/edit/' + id,
        success: function(data) {
            if (data.status) {
                $.each(data.data.days, function(index, value) {
                    $('input[type="checkbox"][name="days[]"][value="' + value.day + '"]').prop('checked', true);
                });
                $('#edit_title').val(data.data.title);
                $('#edit_id').val(data.data.id);
                $('#edit_title').val(data.data.title);
                $('#edit_start_time').val(data.data.start_time);
                $('#edit_end_time').val(data.data.end_time);
                $('#edit_slot').val(data.data.slot);
                $('#edit_telehealth_config_type').val(data.data.tele_config_type).change();
                setTimeout(function() {
                    $('#edit_location_id').val(data.data.location_id);
                }, 1000);
            } else {
                toastr.error(data.message);
            }
        },
        error: function(jqr) {
            toastr.error('Sorry, something went wrong. Please try again.');
        }
    });
}

function editTeleLocationSchedule() {
    var form = $('#editTelehealthSchedule')[0];
    var formData = new FormData(form);
    cnt = 0;
    if(!formData.get('title')) {
        $('#edit_title_error').html('Please enter title');
        cnt++;
    }
    if(!formData.get('days[]')) {
        $('#edit_day_error').html('Please select at least one day');
        cnt++;
    }
    if(!formData.get('start_time')) {
        $('#edit_start_error').html('Please select schedule start time');
        cnt++;
    }
    if(!formData.get('end_time')) {
        $('#edit_end_time_error').html('Please select schedule end time');
        cnt++;
    }
    if(formData.get('start_time') && formData.get('end_time')) {
        if(formData.get('start_time') > formData.get('end_time')) {
            $('#edit_start_error').html('Start time should be less than end time');
            cnt++;
        } else if(formData.get('start_time') == formData.get('end_time')) {
            $('#edit_end_time_error').html('Start Time and End Time should not be same');
            cnt++;
        }    
    }
    if(!formData.get('slot')) {
        $('#edit_slot_error').html('Please enter slot');
        cnt++;
    }else{
        let start = new Date('1970-01-01T' + formData.get('start_time'));
        let end = new Date('1970-01-01T' + formData.get('end_time'));
        let diffInMinutes = (end - start) / (1000 * 60);
        if (formData.get('slot') > diffInMinutes) {
            $('#edit_slot_error').html("Slot time must not be greater than the duration between start and end time.");
            cnt++;
        }
    }
    if(!formData.get('telehealth_config_type')) {
        $('#edit_telehealth_config_type_error').html('Please select Telehealth configuration type');
        cnt++;
    }
    if(!formData.get('location_id')) {
        $('#edit_location_id_error').html('Please select Location');
        cnt++;
    }
    if(cnt == 0) {
        $('#loader_edit').attr('style','display:');
        $.ajax({
            type: 'POST',
            url: TELEHEALTH_LOCATION_SCHEDULE + '/update',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status) {
                    toastr.success(data.error_msg);
                    getLocationTelehealthSchedule(1);
                    $('#loader_edit').attr('style','display:none');
                    $('#editTelehealthSchedule')[0].reset();
                    $('#edit_day_id').val(null).trigger('change');
                    $('#editModal').modal('hide');
                }
            },
            error: function(jqr) {
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}

function deleteTeleSchedule(id){
    $.confirm({
        title: 'Confirmation',
        content: 'Are you sure you want to delete this schedule?',
        buttons: {
            confirm: function () {
                $.ajax({
                    method: 'GET',
                    url: TELEHEALTH_LOCATION_SCHEDULE + '/delete/'+id,
                    success:function(res){
                        if(res.status){
                            toastr.success(res.error_msg);
                            getLocationTelehealthSchedule(1);
                        }else{
                            toastr.error(res.message);
                        }
                    }, 
                    error:function(jqr){
                        toastr.error('Sorry, something went wrong. Please try again.')
                    }
                })
            },
            cancel: function () {
                // Do nothing
            }
        }
    });
}


function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

        return false;
    }
    return true;
}

$('.add-modal-edit').click(function(){
     $('#title_error').html('');
    $('#day_error').html('');
    $('#start_error').html('');
    $('#end_time_error').html('');
    $('#slot_error').html('');
    $('#telehealth_config_type_error').html('');
    $('#location_id_error').html('');
    $('#addTelehealthSchedule')[0].reset();
})

$('#telehealth_config_type').on('change', function(){
    console.log('Hello');
    telehealth_config = $('#telehealth_config_type').val();
    $.ajax({
        method: 'GET',
        url: TELEHEALTH_LOCATION,
        data: {
            telehealth_config: telehealth_config
        },
        success:function(res){
            if(res.status){
                $('#location_id').html('');
                $.each(res.data, function(index, result) {
                    const option = $('<option></option>')
                        .val(result.id)
                        .text(result.address1);
                    $('#location_id').append(option);
                });
            }else{
                toastr.error(res.message);
            }
        }, 
        error:function(jqr){
            toastr.error('Sorry, something went wrong. Please try again.')
        }
    })
});

$('#edit_telehealth_config_type').on('change', function(){
    console.log('Hello');
    telehealth_config = $('#edit_telehealth_config_type').val();
    $.ajax({
        method: 'GET',
        url: TELEHEALTH_LOCATION,
        data: {
            telehealth_config: telehealth_config
        },
        success:function(res){
            if(res.status){
                $('#edit_location_id').html('');
                $.each(res.data, function(index, result) {
                    const option = $('<option></option>')
                        .val(result.id)
                        .text(result.address1);
                    $('#edit_location_id').append(option);
                });
            }else{
                toastr.error(res.message);
            }
        }, 
        error:function(jqr){
            toastr.error('Sorry, something went wrong. Please try again.')
        }
    })
});

$('ul.left-section-ul li').click(function() {
    $('ul.left-section-ul li').removeClass('active');
    $(this).addClass('active');
})

$('ul.right-section-ul li').click(function() {
    $('ul.right-section-ul li').removeClass('active');
    $(this).addClass('active');

})


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
    // $('.select2').val(null).trigger('change');
    
    // Hide schedule info and events
    $('#scheduleInfo').hide();
    $('#daysEventsList').html('<div class="no-schedule"><i class="fa fa-calendar-alt fa-3x mb-3"></i><p>Please select location, schedule, and nurse to view available time slots.</p></div>');
    $('.action-buttons').hide();
    
    // Toggle buttons
    $(this).hide();
    $('#getCalendarBtn').show();
});

// // Handle Cancel Selection button click
// $('#cancelSelectionBtn').on('click', function() {
//     // Clear all selections
//     $('.slot-checkbox').prop('checked', false);
//     $('.slot-checkbox').trigger('change');
//     $('.time-slot').removeClass('selected');
//     $('.action-buttons').hide();
// });

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
        shimmerHtml += '<div class="schedule-table-header columns-7 mt-2">';
        
        // Add 7 columns for days
        for (var i = 0; i < 7; i++) {
            shimmerHtml += '<div class="shimmer-column">';
            shimmerHtml += '<div class="shimmer shimmer-day"></div>';
            // Add 10 time slots per day
            for (var j = 0; j < 5; j++) {
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
        // $(this).hide();
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
                                            if(schedule.del_flag == 'N'){
                                                checkbox.prop('checked', true);
                                                checkbox.closest('.time-slot').addClass('selected');
                                            }
                                            checkbox.closest('.time-slot').attr('data-event-id', schedule.id);
                                            
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
                   'data-event-id="" ' +
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
    html += '<button type="button" class="btn btn-info copy-schedule-btn btn-sm" onclick="copyFunction()">';
    html += '<i class="fa fa-copy"></i> Copy Schedule';
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
    var selectedEvents = [];
    $('.time-slot.selected').each(function() {
        var day = $(this).data('day');
        selectedEvents.push({
            slot_id: $(this).data('slot-id'),
            day: day,
            start_time: $(this).data('start'),
            end_time: $(this).data('end'),
            ids: $(this).data('event-id')
        });
    });
    $.confirm({
        title: 'Are you sure?',
        content: 'you want to change the Schedule',
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    let self = this;
                    self.buttons.formSubmit.setText('<i class="fa fa-spinner fa-spin"></i> Confirm');
                    self.buttons.formSubmit.disable();
                    var locationId = $('#tele_location').val();
                    var nurseId = $('#nurse').val();
                    var scheduleId = $('#schedule').val();
                    showLoader();
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
                                $('#getCalendarBtn').trigger('click');
                                toastr.success(response.message);
                            } else {
                                $('#getCalendarBtn').trigger('click');
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Error updating schedule');
                        }
                    });
                }
            },
            cancel: function() {
            },
        },
    });
});

// Manual Slot Availability Functions
$(document).ready(function() {
    // Handle view manual schedule button click
    $('#viewManualSchedule').on('click', function() {
        var nurseId = $('#manual_nurse').val();
        var selectedDate = $('#manual_date').val();

        if (!nurseId || !selectedDate) {
            toastr.error('Please select both nurse and date');
            return;
        }

        // Show loading state
        $('#manualDaysEventsList').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading schedule...</p></div>');

        // Get nurse schedule for selected date
        $.ajax({
            url: CHECK_NURSE_SCHEDULE,
            method: 'POST',
            data: {
                nurse_id: nurseId,
                date: selectedDate,
                _token: CSRF_TOKEN
            },
            success: function(response) {
                if (response.status) {
                    if(response.scheduleData.length == 0){
                        html ='';
                        html += '<div class="alert alert-info text-center p-3">';
                        html += '<i class="fas fa-calendar-times fa-2x mb-2"></i>';
                        html += '<h6 class="mb-1">No Schedule Available</h6>';
                        html += '<small>There are no time slots available for the selected date.</small>';
                        html += '</div>';
                        $('#manualDaysEventsList').html(html);
                        $('#manualScheduleInfo').hide();
                    }else{
                        // Update schedule info
                        $('#selectedManualNurse').text($('#manual_nurse option:selected').text());
                        $('#selectedManualDate').text(selectedDate);
                        $('#manualScheduleInfo').show();
                        // Display schedule
                        displayManualSchedule(response.data,response.scheduleData);
                    }
                } else {
                    toastr.error(response.message || 'Failed to load schedule');
                    $('#manualDaysEventsList').html('<div class="no-schedule"><i class="fa fa-calendar-alt fa-3x mb-3"></i><p>No schedule available for selected date.</p></div>');
                }
            },
            error: function() {
                toastr.error('Failed to load schedule');
                $('#manualDaysEventsList').html('<div class="no-schedule"><i class="fa fa-calendar-alt fa-3x mb-3"></i><p>Error loading schedule. Please try again.</p></div>');
            }
        });
    });

    // Handle save manual events button click
    $('#saveManualEventsBtn').on('click', function() {
        var nurseId = $('#manual_nurse').val();
        var selectedDate = $('#manual_date').val();
        var selectedSlots = [];

        // Get all selected time slots
        $('.manual-time-slot:checked').each(function() {
            selectedSlots.push({
                start_time: $(this).data('start-time'),
                end_time: $(this).data('end-time'),
                schedule_id: $(this).data('schedule-id'),
                location_id: $(this).data('location-id'),
                slot_id: $(this).data('slot-id'),
                ids: $(this).data('event-id')
            });
        });
        $('#manualDaysEventsList').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading schedule...</p></div>');
        // Save selected slots
        $.ajax({
            url: UPDATE_NURSE_SCHEDULE_DATE,
            method: 'POST',
            data: {
                nurse_id: nurseId,
                date: selectedDate,
                events: selectedSlots,
                _token: CSRF_TOKEN
            },
            success: function(response) {
                if (response.status) {
                    toastr.success('Schedule updated successfully');
                    // Refresh the schedule display
                    $('#viewManualSchedule').click();
                } else {
                    toastr.error(response.message || 'Failed to update schedule');
                    $('#viewManualSchedule').click();
                }
            },
            error: function() {
                toastr.error('Failed to update schedule');
            }
        });
    });
});

function displayManualSchedule(scheduleData,scheduleInfo) {
    var html = '';
    if (Object.keys(scheduleData).length > 0 && scheduleInfo.length > 0) {
        html += '<div class="daysEventsList">';
        html += '<div class="schedule-table"><label class="checkbox-wrapper"><input type="checkbox" id="selectAll" class="manual-select-all-checkbox"><span class="" style="margin-left: 8px;"> Select All</span></label><hr/><div class="schedule-table-header columns-1">';
        scheduleInfo.forEach(function(schedule) {           
            html += '<div class="day-column">';
            if(scheduleData[schedule.id]){
                scheduleData[schedule.id].forEach(function(slot) {
                    var isAvailable = slot.is_assigned;
                    html += '<div class="time-slots">';
                    html += '<div class="time-slot">';
                    html += '<input type="checkbox" class="manual-time-slot" ' + 
                        ' data-start-time="' + slot.start_time + '" ' +
                        ' data-end-time="' + slot.end_time + '" ' +
                        ' data-slot-id="' + slot.slot_id + '" ' +
                        ' data-schedule-id="' + slot.schedule_id + '" ' +
                        ' data-location-id="' + slot.location_id + '" ' +
                        ' data-event-id="' + slot.event_id + '" ' +
                        (isAvailable ? 'checked' : '') + '>';
                    html += '<div class="time-slot-text">';
                    html += '<span class="time-range">' + slot.start_time_con + ' - ' + slot.end_time_con + '</span>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
            }
            html += '</div>';
        });
        html += '</div></div></div>';
        $('.manual-action-buttons').show();
    } else {
        html += '<div class="alert alert-info text-center p-3">';
        html += '<i class="fas fa-calendar-times fa-2x mb-2"></i>';
        html += '<h6 class="mb-1">No Schedule Available</h6>';
        html += '<small>There are no time slots available for the selected date.</small>';
        html += '</div>';
    }
    $('#manualDaysEventsList').html(html);
    //
    selectCheckbox();
    function selectCheckbox(){
        totalCheckboxes = $('.manual-time-slot').length;
        totalCheckedCheckboxes = $('.manual-time-slot:checked').length;
        if(totalCheckboxes == totalCheckedCheckboxes){
            $('#selectAll').prop('checked',true);
        }else{
            $('#selectAll').prop('checked',false);
        }
    }

    $('.manual-time-slot').on('change', function() {
        selectCheckbox();
    });

    // Add click handler for select all checkboxes
    $('.manual-select-all-checkbox').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.manual-time-slot').prop('checked', isChecked).trigger('change');
    });
}

let properJson = JSON.parse(UNAVAILABLEDATES.replace(/&quot;/g, '"'));

function unavailable(date) {
    var month = ("0" + (date.getMonth() + 1)).slice(-2);
    var day   = ("0" + date.getDate()).slice(-2);
    var year  = date.getFullYear();
    var formattedDate = day + "-" + month + "-" + year;
    if ($.inArray(formattedDate, properJson) !== -1) {
        return [false, "", "Unavailable"]; // Disable this date
    }
    return [true, ""];
}
$("#manual_date").datepicker({
    minDate: new Date(),
    buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
    beforeShowDay: unavailable
});

function copyFunction(){
    // Get all selected slots from the first column
    var firstColumnSlots = $('.time-slot.selected');
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
    $('.day-column').each(function() {
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
}

$(document).on('click', '#copySlotsBtn', function(e) {
    e.preventDefault();
    $('#addLocationScheduleMsg').html('');
    var day = $('#day').val();
    if (!day) {
        $('#addLocationScheduleMsg').html('<div class="alert alert-danger">Please select a day.</div>');
        return;
    }
    // Fixed 6 slots, 15 min each, between 9am and 7pm
    var start = moment('09:00', 'HH:mm');
    var slots = [];
    for (var i = 0; i < 6; i++) {
        var slotStart = start.clone().add(i * 15, 'minutes');
        var slotEnd = slotStart.clone().add(15, 'minutes');
        slots.push({
            day: day,
            start_time: slotStart.format('HH:mm'),
            end_time: slotEnd.format('HH:mm'),
            slot: 15
        });
    }
    $.ajax({
        url: '/save-location-schedule-slots',
        method: 'POST',
        data: {
            slots: slots,
            _token: CSRF_TOKEN
        },
        success: function(response) {
            if (response.status) {
                $('#addLocationScheduleMsg').html('<div class="alert alert-success">' + response.message + '</div>');
            } else {
                $('#addLocationScheduleMsg').html('<div class="alert alert-danger">' + (response.message || 'Failed to save slots') + '</div>');
            }
        },
        error: function() {
            $('#addLocationScheduleMsg').html('<div class="alert alert-danger">Error saving slots. Please try again.</div>');
        }
    });
});

$(document).on('click', '#submitBtn', function(e) {
    e.preventDefault();
    $('#addLocationScheduleMsg').html('');
    var day = $('#day').val();
    var start_time = $('#start_time').val();
    var end_time = $('#end_time').val();
    var slot = $('#slot').val();
    if (!day) {
        toastr.error('Please add all required fields.');
        return false;
    }
    if (moment(start_time, 'HH:mm').isAfter(moment(end_time, 'HH:mm'))) {
        toastr.error('Start time cannot be later than end time.');
        return false;
    }
    $.ajax({
        url: '/save-location-schedule-slots',
        method: 'POST',
        data: {
            day: day,
            start_time: start_time,
            end_time: end_time,
            slot: slot,
            _token: CSRF_TOKEN
        },
        success: function(response) {
            if (response.status) {
                toastr.success(response.message);
                loadPatientTelehealthSchedules();
            } else {
                toastr.error(response.message || 'Failed to save slots');
            }
        },
        error: function() {
            toastr.error('Error saving slots. Please try again.');
        }
    });
});

$(document).on('click', '#copyAllWeekBtn', function(e) {
    e.preventDefault();
    var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    var slotDuration = 15; // minutes
    var startTime = moment('09:00', 'HH:mm');
    var endTime = moment('19:00', 'HH:mm'); // 7:00 PM
    var allSlots = [];

    days.forEach(function(day) {
        var current = startTime.clone();
        while (current < endTime) {
            var slotStart = current.clone();
            var slotEnd = current.clone().add(slotDuration, 'minutes');
            if (slotEnd > endTime) break;
            allSlots.push({
                day: day,
                start_time: slotStart.format('HH:mm'),
                end_time: slotEnd.format('HH:mm'),
                slot: 6 // always store as 6
            });
            current.add(slotDuration, 'minutes');
        }
    });

    $.ajax({
        url: '/copy-location-schedule-slots', // your updated endpoint
        method: 'POST',
        data: {
            slots: allSlots,
            _token: CSRF_TOKEN
        },
        success: function(response) {
            if (response.status) {
                toastr.success(response.message);
                loadPatientTelehealthSchedules();
            } else {
                toastr.error(response.message || 'Failed to save slots');
            }
        },
        error: function() {
            toastr.error('Error saving slots. Please try again.');
        }
    });
});

function loadPatientTelehealthSchedules() {
    $.ajax({
        url: "/get-patient-telehealth-list",
        type: "GET",
        success: function(response) {
            if(response.status) {
                // Group schedules by day
                const schedulesByDay = {};
                response.data.forEach(item => {
                    if (!schedulesByDay[item.day]) {
                        schedulesByDay[item.day] = [];
                    }
                    schedulesByDay[item.day].push(item);
                });

                // Sort days in correct order
                const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                const sortedDays = Object.keys(schedulesByDay).sort((a, b) => 
                    dayOrder.indexOf(a) - dayOrder.indexOf(b)
                );

                // Generate HTML for each day
                let html = '';
                sortedDays.forEach(day => {
                    html += `
                        <div class="schedule-day-card">
                            <div class="schedule-day-header" data-day="${day}">
                                <span>${day}</span>
                                <i class="mdi mdi-chevron-down day-toggle-icon"></i>
                            </div>
                            <div class="schedule-time-slots">
                    `;
                    
                    schedulesByDay[day].forEach(slot => {
                        html += `
                            <div class="time-slot-item">
                                <div class="time-range">
                                    ${slot.start_time} - ${slot.end_time}
                                </div>
                                <div class="slot-count">
                                    ${slot.slot} Slots
                                </div>
                            </div>
                        `;
                    });

                    html += `
                            </div>
                        </div>
                    `;
                });

                $('.schedule-days-container').html(html);

                // Add click handlers for day headers
                $('.schedule-day-header').on('click', function() {
                    const $header = $(this);
                    const $content = $header.next('.schedule-time-slots');
                    const $icon = $header.find('.day-toggle-icon');
                    
                    // Toggle active state
                    $header.toggleClass('active');
                    $icon.toggleClass('active');
                    
                    // Toggle content with animation
                    if ($content.hasClass('show')) {
                        $content.removeClass('show');
                        $content.find('.time-slot-item').removeClass('show');
                    } else {
                        $content.addClass('show');
                        // Animate time slots with delay
                        $content.find('.time-slot-item').each(function(index) {
                            const $item = $(this);
                            setTimeout(() => {
                                $item.addClass('show');
                            }, index * 100); // 100ms delay between each item
                        });
                    }
                });
            }
        },
        error: function() {
            toastr.error('Failed to load patient telehealth schedules');
        }
    });
}

loadPatientTelehealthSchedules();
function showLoader(){
     // Show shimmer loading effect
    var shimmerHtml = '<div class="shimmer-container">';
    shimmerHtml += '<div class="schedule-table">';
    shimmerHtml += '<div class="schedule-table-header columns-7 mt-2">';

    // Add 7 columns for days
    for (var i = 0; i < 7; i++) {
        shimmerHtml += '<div class="shimmer-column">';
        shimmerHtml += '<div class="shimmer shimmer-day"></div>';
        // Add 10 time slots per day
        for (var j = 0; j < 5; j++) {
            shimmerHtml += '<div class="shimmer shimmer-time-slot"></div>';
        }
        shimmerHtml += '</div>';
    }

    shimmerHtml += '</div></div></div>';

    $('#daysEventsList').html(shimmerHtml);
    $('.shimmer-container').show();
}