function getDueDate() {
    var due_date = $("#due_date_id").val();
    var cnt = 0;
    $("#due_date_id_error").html("");
    if (due_date.trim() == "") {
        $("#due_date_id_error").html("Please enter Medical Due Date");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _PATIENT_DUE_DATE,
            type: "POST",
            data: {
                due_date: due_date,
                _token: _CSRF_TOKEN,
                patient_id: _RECORD_ID,
            },
            success: function (resp) {
                if (resp == 1) {
                    var msg = "Medical Due date successfully updated";
                    toastr.success(msg);
                    location.reload();
                } else {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }
            },
        });
    }
}