function telehealthSubmit(){
    var telehealth_language = $('#telehealth_language').val();
    var telehealth_date_id = $('#telehealth_date_id').val();
    var telehealth_time_slot = $('#telehealth_time_slot').val();
    var cnt = 0;
    $('#telehealth_language_error').html("");
    $('#telehealth_date_id_error').html("");
    $('#telehealth_time_slot_error').html("");
    $('#tele_caregiver_service_error').html("");

    if (telehealth_language == '') {
        $('#telehealth_language_error').html("Please select Language");
        cnt = 1;
    }
    if (telehealth_date_id == '') {
        $('#telehealth_date_id_error').html("Please select Date");
        cnt = 1;
    }
    if (telehealth_time_slot == '') {
        $('#telehealth_time_slot_error').html("Please select Time Slot");
        cnt = 1;
    }
    if ($('#tele_caregiver_service_id').val() == '') {
        $('#tele_caregiver_service_error').html("Please select Service");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    }

    // Show confirmation dialog with jQuery Confirm
    $.confirm({
        title: 'Confirm Appointment',
        content: `
            <div class="confirmation-details">
                <p><strong>Language:</strong> ${$('#telehealth_language option:selected').text()}</p>
                <p><strong>Date:</strong> ${telehealth_date_id}</p>
                <p><strong>Time Slot:</strong> ${$('#telehealth_time_slot option:selected').text()}</p>
            </div>
            <p>Are you sure you want to proceed with this appointment?</p>
        `,
        type: 'blue',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Yes, confirm appointment',
                btnClass: 'btn-blue',
                action: function() {
                    // Prepare form data
                    var formData = {
                        telehealth_language: $('#telehealth_language').val(),
                        telehealth_date_id: $('#telehealth_date_id').val(),
                        telehealth_time_slot: $('#telehealth_time_slot').val(),
                        id: _RECORD_ID,
                        _token: _CSRF_TOKEN,
                        type: _RECORD_TYPE,
                        tele_caregiver_service_id: $('#tele_caregiver_service_id').val()
                    };

                    // Make AJAX call
                    $.ajax({
                        url: $('#telehealthform').attr('action'),
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                $.confirm({
                                    title: 'Success!',
                                    content: 'Appointment has been successfully scheduled.',
                                    type: 'green',
                                    typeAnimated: true,
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            btnClass: 'btn-green',
                                            action: function() {
                                                // Close the modal and refresh if needed
                                                $('#telehealth-appointment-date-id').html(`<p><strong>Date:</strong> ${moment(telehealth_date_id).format('MM/DD/YYYY')}</p>
                                                    <p><strong>Time Slot:</strong> ${$('#telehealth_time_slot option:selected').text()}<br/><strong>Nurse</strong>: ${$('#telehealth_time_slot option:selected').attr('nurse-name')}<br/><strong>Language:</strong> ${$('#telehealth_language option:selected').text()}</p>`);
                                                $('#exampleModal-44').modal('hide');
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                            } else {
                                $.confirm({
                                    title: 'Error!',
                                    content: response.message || 'Failed to schedule appointment. Please try again.',
                                    type: 'red',
                                    typeAnimated: true,
                                    buttons: {
                                        tryAgain: {
                                            text: 'Try Again',
                                            btnClass: 'btn-red',
                                            action: function() {
                                                // Allow user to try again
                                            }
                                        }
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            $.confirm({
                                title: 'Error!',
                                content: 'An error occurred while processing your request. Please try again.',
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    tryAgain: {
                                        text: 'Try Again',
                                        btnClass: 'btn-red',
                                        action: function() {
                                            // Allow user to try again
                                        }
                                    }
                                }
                            });
                        }
                    });
                }
            },
            cancel: {
                text: 'No, cancel',
                btnClass: 'btn-red',
                action: function() {
                    // Do nothing, just close the dialog
                }
            }
        }
    });

    return false;
}


// Function to update slot availability information
function updateSlotAvailability(timeSlots,totalBookedSlot) {
    const totalSlots = timeSlots.length;
    const bookedSlots = totalBookedSlot;
    const availableSlots = totalSlots - bookedSlots;

    $('#total_slots').text(totalSlots);
    $('#booked_slots').text(bookedSlots);
    $('#available_slots').text(availableSlots);

    // Show/hide availability container and no-slots message
    if (totalSlots > 0) {
        $('.slot-availability-container').show();
        $('.no-slots-message').hide();
    } else {
        $('.slot-availability-container').hide();
        $('.no-slots-message').show()
            .removeClass('alert-warning alert-danger')
            .addClass('alert-info')
            .find('.message-text')
            .text('No slots available for the selected date and language. Please try a different date or check back later.');
    }
}

// Function to update time slot dropdown
function updateTimeSlotDropdown(timeSlots,slotCheck,nurse) {
    $('#telehealth_time_slot').empty();
    $('#telehealth_time_slot').append('<option value="">Select Time Slot</option>');
    
    if (timeSlots.length === 0) {
        $('#telehealth_time_slot').prop('disabled', true);
        return;
    }

    $('#telehealth_time_slot').prop('disabled', false);
    flag = 0;
    timeSlots.forEach(function(slot) {
        if(slot.del_flag == 'N'){
            let optionText = slot.start_time + ' - ' + slot.end_time;
            let optionClass = '';
            var selected = '';
            var disabled = '';   
            if (slotCheck.includes(slot.id)) {
                optionText += ' (Booked)';
                optionClass = 'booked-slot';

                selected = 'selected';
            }else{
                optionText += '';

            }
            if(optionText){
                const option = $('<option></option>')
                .val(slot.id)
                .text(optionText)
                .addClass(optionClass)
                .prop('selected',selected)
                .prop('disabled',disabled)
                .prop('data-delete',slot.del_flag)
                .attr('nurse-name', 'C#' + slot.nurse_id + ' (' + nurse[slot.nurse_id].language + ')');
                $('#telehealth_time_slot').append(option);
            }
        }
    });
}

// Function to load existing appointment
function loadExistingAppointment() {
    const patientId = _RECORD_ID;
    if (patientId) {
        $.ajax({
            url: '/get-patient-existing-appointment',
            type: 'POST',
            data: {
                patient_id: patientId
            },
            headers: {
                'X-CSRF-TOKEN': _CSRF_TOKEN
            },
            success: function(response) {
                if (response.status && response.appointment) {
                    const appointment = response.appointment;
                    
                    // Set language
                    if( appointment.language) {
                            $('#telehealth_language').val(appointment.language);
                            $('#telehealth_date_id').val(moment(appointment.date).format('MM/DD/YYYY'));
                            
                            // Trigger change event to load time slots
                            $('#telehealth_language, #telehealth_date_id').trigger('change');
                            
                            // After time slots are loaded, select the existing appointment
                            setTimeout(function() {
                                $('#telehealth_time_slot').val(appointment.id);
                                $('#telehealth_time_slot').val(appointment.id).removeAttr('disabled');
                                $('#telehealth_time_slot option[value="'+appointment.id+'"]').addClass('your-appointment');
                            }, 1000);
                    }else{
                        $('#telehealth_nurse').val(appointment.telehealth_nurse);
                        $('#patient_telehealth_date_id').val(moment(appointment.date).format('MM/DD/YYYY'));
                        // Trigger change event to load time slots
                        $('#patient_telehealth_date_id').trigger('change');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading existing appointment:', error);
            }
        });
    }
}

// Load existing appointment when modal is shown
$('#exampleModal-44').on('show.bs.modal', function() {
    loadExistingAppointment();
});

// Update slot availability when time slots are loaded
$('#telehealth_language, #telehealth_date_id').on('change', function() {
    const language = $('#telehealth_language').val();
    const date = $('#telehealth_date_id').val();
    
    if (language && date) {
        $.ajax({
            url: '/get-time-slots',
            type: 'POST',
            data: {
                language: language,
                date: date,
                patient_id: _RECORD_ID,
                type: _RECORD_TYPE
            },
            headers: {
                'X-CSRF-TOKEN': _CSRF_TOKEN
            },
            success: function(response) {
                if (response.status) {
                    updateSlotAvailability(response.time_slots,response.totalBookedSlot);
                    updateTimeSlotDropdown(response.time_slots,response.slotCheck,response.nurse);
                } else {
                    $('.slot-availability-container').hide();
                    $('.no-slots-message').show()
                        .removeClass('alert-info alert-danger')
                        .addClass('alert-warning')
                        .find('.message-text')
                        .text('Unable to load time slots. Please try again later.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('.slot-availability-container').hide();
                $('.no-slots-message').show()
                    .removeClass('alert-info alert-warning')
                    .addClass('alert-danger')
                    .find('.message-text')
                    .text('An error occurred while loading time slots. Please try again later.');
            }
        });
    } else {
        $('.slot-availability-container').hide();
        $('.no-slots-message').hide();
    }
});

function appointmentTeleSubmit(){
    var nurse = $('#telehealth_nurse').val();
    console.log(nurse)
    var patient_telehealth_date_id = $('#patient_telehealth_date_id').val();
    var patient_telehealth_time_slot = $('#patient_telehealth_time_slot').val();
    var cnt = 0;
    $('#telehealth_nurse_error').html("");
    $('#patient_telehealth_date_id_error').html("");
    $('#patient_telehealth_time_slot_error').html("");
    $('#tele_patient_service_error').html("");

    if (nurse == '') {
        $('#telehealth_nurse_error').html("Please select Nurse");
        cnt = 1;
    }
    if (patient_telehealth_date_id == '') {
        $('#patient_telehealth_date_id_error').html("Please select Date");
        cnt = 1;
    }
    if (patient_telehealth_time_slot == '') {
        $('#patient_telehealth_time_slot_error').html("Please select Time Slot");
        cnt = 1;
    }
    if ($('#tele_patient_service_id').val() == '') {
        $('#tele_patient_service_error').html("Please select Service");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    }

    // Show confirmation dialog with jQuery Confirm
    $.confirm({
        title: 'Confirm Appointment',
        content: `
            <div class="confirmation-details">
                <p><strong>Nurse:</strong> ${$('#telehealth_nurse option:selected').text()}</p>
                <p><strong>Date:</strong> ${patient_telehealth_date_id}</p>
                <p><strong>Time Slot:</strong> ${$('#patient_telehealth_time_slot option:selected').text()}</p>
            </div>
            <p>Are you sure you want to proceed with this appointment?</p>
        `,
        type: 'blue',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Yes, confirm appointment',
                btnClass: 'btn-blue',
                action: function() {
                    // Prepare form data
                    var formData = {
                        telehealth_nurse: $('#telehealth_nurse').val(),
                        patient_telehealth_date_id: $('#patient_telehealth_date_id').val(),
                        patient_telehealth_time_slot: $('#patient_telehealth_time_slot').val(),
                        id: _RECORD_ID,
                        _token: _CSRF_TOKEN,
                        type: _RECORD_TYPE,
                        tele_patient_service_id: $('#tele_patient_service_id').val()
                    };
                    $.ajax({
                        url: $('#telehealthPatientform').attr('action'),
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                $.confirm({
                                    title: 'Success!',
                                    content: 'Appointment has been successfully scheduled.',
                                    type: 'green',
                                    typeAnimated: true,
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            btnClass: 'btn-green',
                                            action: function() {
                                                // Close the modal and refresh if needed
                                                $('#telehealth-appointment-date-id').html(`<p><strong>Date:</strong> ${moment(patient_telehealth_date_id).format('MM/DD/YYYY')}</p>
                                                    <p><strong>Time Slot:</strong> ${$('#patient_telehealth_time_slot option:selected').text()}<br/><strong>Nurse</strong>: ${$('#telehealth_nurse').find(":selected").text()}<br/><strong></p>`);
                                                $('#patient-tele-appointment').modal('hide');
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                            } else {
                                $.confirm({
                                    title: 'Error!',
                                    content: response.message || 'Failed to schedule appointment. Please try again.',
                                    type: 'red',
                                    typeAnimated: true,
                                    buttons: {
                                        tryAgain: {
                                            text: 'Try Again',
                                            btnClass: 'btn-red',
                                            action: function() {
                                                // Allow user to try again
                                            }
                                        }
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            $.confirm({
                                title: 'Error!',
                                content: 'An error occurred while processing your request. Please try again.',
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    tryAgain: {
                                        text: 'Try Again',
                                        btnClass: 'btn-red',
                                        action: function() {
                                            // Allow user to try again
                                        }
                                    }
                                }
                            });
                        }
                    });
                }
            },
            cancel: {
                text: 'No, cancel',
                btnClass: 'btn-red',
                action: function() {
                    // Do nothing, just close the dialog
                }
            }
        }
    });

    return false;
}

// When the modal is shown, fetch available slots for the selected day
$('#patient_telehealth_date_id,#telehealth_nurse').on('change', function() {
    var day = $('#patient_telehealth_date_id').val(); // Adjust selector as per your modal's day dropdown/input
    var nurse = $('#telehealth_nurse').val(); // Adjust selector as per your modal's day dropdown/input
    var slotDropdown = $('#patient_telehealth_time_slot'); // Adjust selector as per your modal's slot dropdown
    // Clear previous options
    slotDropdown.empty();
    slotDropdown.append('<option value="">Loading...</option>');
    if(day != "" && nurse != ""){
        $.ajax({
            url: '/get-telehealth-slots', // Create this endpoint in your controller
            method: 'POST',
            data: {
                day: day,
                nurse: nurse,
                _token: CSRF_TOKEN,
                type: _RECORD_TYPE
            },
            success: function(response) {
                slotDropdown.empty();
                if (response.status && response.slots.length > 0) {
                    slotDropdown.append('<option value="">Select Slot</option>');
                    response.slots.forEach(function(slot) {
                        // slot example: {start_time: "09:00", end_time: "09:15", slot: 6, booked: false}
                        var start = moment('1970-01-01 ' + slot.start_time);
                        var end = moment('1970-01-01 ' + slot.end_time);
                        var label = start.format('hh:mm A') + ' to ' + end.format('hh:mm A');
                        slotDropdown.append('<option value="' + slot.id + '">' + label + '</option>');
                    });
                } else {
                    slotDropdown.append('<option value="">No slots available</option>');
                }
            },
            error: function() {
                slotDropdown.empty();
                slotDropdown.append('<option value="">Error loading slots</option>');
            }
        });
    }
});

$('#patient-tele-appointment').on('show.bs.modal', function() {
    loadExistingAppointment();
});