function getTimeSearch() {
    var location_id = $('#location_id').val();
    var date_id = $('#date_id').val();
    var existId = _EXISTING_APPOINTMENT_TIME_ID;

    if (location_id != "" && date_id != "") {
        $.ajax({
            url: _PATIENT_LOCATION_SCHEDULE_SEARCH,
            type: "GET",
            data: {
                location_id: location_id,
                start_time: date_id,
            },
            success: function (resp) {
                var json = JSON.parse(resp);
                var htmls = '';
                $('#timeid').html("");
                if (json.length != 0) {
                    htmls = '<option value="">Select Appointment Time</option>';
                    $.each(json, function(i, v) {
                        var selected = '';
                        if (existId == v.id) {
                            selected = 'selected="selected"';
                        }
                        htmls += '<option value="' + v.id + '" ' + selected + '>' + v
                            .start_time + '-' + v.end_time + '(' + v.slots + ')' + '</option>'
                    });

                } else {
                    htmls = '<option value="">No appointment schedule</option>'
                }

                $('#timeid').html(htmls);
            },
        });
    }
    let hasLocationId = Object.keys(LOCATION_WISE_DISABLE_DATES)
    .some(key => key.startsWith(location_id + "_"));
    let allDates = JSON.parse(unavailableDates.replace(/&quot;/g, '"'));
    allDates = Array.isArray(allDates) ? allDates : [allDates];
    /***********************
     * 2. LOCATION STATUS KEYS
     ***********************/
    let disabledKey = location_id + "_0";
    let enabledKey  = location_id + "_1";

    let disabledDates = LOCATION_WISE_DISABLE_DATES[disabledKey] || [];
    let enabledDates  = LOCATION_WISE_DISABLE_DATES[enabledKey]  || [];

    // ensure arrays
    disabledDates = Array.isArray(disabledDates) ? disabledDates : [disabledDates];
    enabledDates  = Array.isArray(enabledDates)  ? enabledDates  : [enabledDates];

    if (location_id !== "" && hasLocationId) {
        // combine global + location disabled
        let combinedDisabled = [
            ...new Set([...allDates, ...disabledDates])
        ];

        // enabled overrides disabled
        properJson = combinedDisabled.filter(
            d => !enabledDates.includes(d)
        );
    }else{
        properJson = allDates;
    }
}

function getTimeSearchForAgency() {
    var location_id = $('#location_eid').val();
    var date_id = $('#date_eid').val();
    var existId = _EXISTING_APPOINTMENT_TIME_ID;
    if (location_id != '' && date_id != '') {
        $.ajax({

            url: _PATIENT_LOCATION_SCHEDULE_SEARCH,
            type: "GET",
            data: {
                "location_id": location_id,
                'start_time': date_id
            },
            success: function(resp) {
                var json = JSON.parse(resp);
                var htmls = '';
                $('#time_eid').html("");
                if (json.length != 0) {
                    htmls = '<option value="">Select Appointment Time</option>';
                    $.each(json, function(i, v) {
                        var selected = '';
                        if (existId == v.id) {
                            selected = 'selected="selected"';
                        }
                        htmls += '<option value="' + v.id + '" ' + selected + '>' + v
                            .start_time + '-' + v.end_time + '(' + v.slots + ')' + '</option>'
                    });

                } else {
                    htmls = '<option value="">No appointment schedule</option>'
                }

                $('#time_eid').html(htmls);
            }
        })
    }
}

function getTimeSearchForSchedule() {
    var location_id = $('#location_id_schedule').val();
    var date_id = $('#schedule_date_id').val();
    var existId = _EXISTING_APPOINTMENT_TIME_ID;
    if (location_id != '' && date_id != '') {
        $.ajax({
            url: _PATIENT_LOCATION_SCHEDULE_SEARCH,
            type: "GET",
            data: {
                "location_id": location_id,
                'start_time': date_id
            },
            success: function (resp) {
                var json = JSON.parse(resp);
                var htmls = '';
                $('#time_id_schedule').html("");
                if (json.length != 0) {
                    htmls = '<option value="">Select Appointment Time</option>';
                    $.each(json, function (i, v) {
                        var selected = '';
                        if (existId == v.id) {
                            selected = 'selected="selected"';
                        }
                        htmls += '<option value="' + v.id + '" ' + selected + '>' + v
                            .start_time + '-' + v.end_time + '(' + v.slots + ')' + '</option>'
                    });
                } else {
                    htmls = '<option value="">No appointment schedule</option>'
                }
                $('#time_id_schedule').html(htmls);
            }
        })
    }
}

$('#date_id').on('change', function() {
    var location_id = $('#location_id').val();
    var date_id = $('#date_id').val();

    if (location_id !== '' && date_id !== '') {
        $.ajax({
            url: _SCEDULE_TOTAL_COUNT,
            type: "GET",
            data: {
                "location_id": location_id,
                'start_time': date_id
            },
            success: function(resp) {
                // Parse the JSON response if not already an object
                var json = (typeof resp === "object") ? resp : JSON.parse(resp);
                var total_slot = json.totalSloat || 0;
                var total_booked = json.totalBokked || 0;
                var total_available = json.totalRemaining || 0;
                if (Object.keys(json).length !== 0) {
                    $('#date_time_div').html(
                        `<p style="display: inline-flex;gap: 75px;align-items: center;font-size: 14px;margin-top: 9px;margin-bottom: 0px;">
                            <span><b>Total Slot:</b> <span style="color: blue; font-weight: bold;">${total_slot}</span></span>
                            <span><b>Total Booked:</b> <span style="color: red; font-weight: bold;">${total_booked}</span></span>
                            <span><b>Total Available:</b> <span style="color: green; font-weight: bold;">${total_available}</span></span>
                        </p>`
                    );
                } else {
                    $('#date_time_div').html('<p>No schedule available</p>');
                }
            },
            error: function(xhr) {
                console.error("Error fetching data:", xhr.statusText);
                $('#date_time_div').html('<p>Error retrieving schedule data</p>');
            }
        });
    }
});

$('#timeid').on('change', function() {
    var location_id = $('#location_id').val();
    var date_id = $('#date_id').val();
    var time_id = $('#timeid').val();

    if (location_id !== '' && date_id !== '' && time_id !== '') {
        $.ajax({
            url: _SCEDULE_TOTAL_TIME_COUNT,
            type: "GET",
            data: {
                "location_id": location_id,
                'start_time': date_id,
                'timeId': time_id
            },
            success: function(resp) {
                // Parse the JSON response if not already an object
                var json = (typeof resp === "object") ? resp : JSON.parse(resp);
                var total_slot = json.totalSloat || 0;
                var total_booked = json.totalBokked || 0;
                var total_available = json.totalRemaining || 0;
                if (Object.keys(json).length !== 0) {
                    $('#date_time_count_div').html(
                        `<p style="display: inline-flex;gap: 75px;align-items: center;font-size: 14px;margin-top: 9px;margin-bottom: 0px;">
                            <span><b>Total Slot:</b> <span style="color: blue; font-weight: bold;">${total_slot}</span></span>
                            <span><b>Total Booked:</b> <span style="color: red; font-weight: bold;">${total_booked}</span></span>
                            <span><b>Total Available:</b> <span style="color: green; font-weight: bold;">${total_available}</span></span>
                        </p>`
                    );
                } else {
                    $('#date_time_count_div').html('<p>No schedule available</p>');
                }
            },
            error: function(xhr) {
                showErrorAndLoginRedirection(xhr);
            }
        });
    }
});

$('#location_id').change(function(){
    $('#date_id').val("");
    $('#date_time_div').html('');
    $('#date_time_count_div').html('');
})