function getNextAppointmentDate() {
    var due_date = $("#next_date_id").val();
    var cnt = 0;
    $("#next_date_id_error").html("");
    if (due_date.trim() == "") {
        $("#next_date_id_error").html("Please select Next Appointment Date");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _PATIENT_NEXT_APPOINTMENT,
            type: "POST",
            data: {
                appoinment_date: due_date,
                _token: _CSRF_TOKEN,
                patient_id: _RECORD_ID,
            },
            success: function (resp) {
                if (resp == 1) {
                    var msg = "Appointment date successfully updated";
                    toastr.success(msg);
                    $("#next_apid").html(due_date);
                    $(".close").click();
                } else {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }
            },
            error: function (jqXHR) {
                if (jqXHR.status === 400) {
                    toastr.error("Invalid date format. Please select a valid date.");
                } else if (jqXHR.status === 500) {
                    toastr.error(
                        "Failed to update appointment date. Please try again."
                    );
                } else {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }
            },
        });
    }
}