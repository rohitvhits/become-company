function submitTelehealthForm(e) {
    e.preventDefault();

    var telehealth_id = $("#telehealth_id").val();
    var telehealth_time_id = $("#telehealth_time_id").val();
    var cnt = 0;
    $("#telehealth_id_error").html("");
    $("#telehealth_time_id_error").html("");

    if (telehealth_id.trim() == "") {
        $("#telehealth_id_error").html("Please select Telehealth Appointment Date");
        cnt = 1;
    }
    if (telehealth_time_id.trim() == "") {
        $("#telehealth_time_id_error").html(
            "Please select Telehealth Appointment Time"
        );
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        var formData = new FormData($("#telehealthform")[0]);
        formData.append("_token", _CSRF_TOKEN);
        formData.append("id", _RECORD_ID);

        $.ajax({
            async: false,
            global: false,
            url: _PATIENT_TELEHEALTH_ADD,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success("Telehealth appointment successfully added.");
            },
            error: function (jqXHR) {
                if (jqXHR.status === 400) {
                    toastr.error("Invalid data. Please check the inputs.");
                } else if (jqXHR.status === 500) {
                    toastr.error(
                        "Failed to add Telehealth appointment. Please try again."
                    );
                } else {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }
            },
        });
    }
}

$("#telehealthform").submit(submitTelehealthForm);