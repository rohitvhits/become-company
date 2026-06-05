function submitAssignForm() {
    var assign_to = $("#assign_id").val();
    $("#assign_to_us_error").html("");

    if (assign_to == "") {
        $("#assign_to_us_error").html("Please select assign user.");
        return false;
    }

    $.ajax({
        url: _PATIENT_ASSIGN,
        type: "POST",
        data: $("#patientAssign").serialize(),
        success: function (response) {
            if (response.success) {
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            toastr.error("An error occurred while assigning the appointment.");
        },
    });
}