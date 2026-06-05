//Date Picker JS
// $('#fu_date').datepicker();

$('#fu_date').datepicker({
    inline: true // This ensures the calendar is always visible
});

//search js

function searchFunctionality() {
    $.ajax({
        url: _GET_APPOINTMENT_DATA,
        type: 'POST',
        dataType: 'json',
        data: {
            id: $('#emc_id').val(),
            loc_id: $('#location_id').val(),
            fdate: $('#fu_date').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (doc) {
            $('#calendar').fullCalendar('removeEvents');
            $('#calendar').fullCalendar('addEventSource', doc);
        },
        error: function (xhr, status, error) {
            console.log(status);
            console.error('AJAX request failed:', status, error);
        }
    });
}
$("#emc_id").select2({
    placeholder: "Select Agency"
});
$('#location_id').select2();






