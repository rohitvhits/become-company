function getNyBestUpdate() {
    var assign_nybest_user = $('#assign_nybest_user').val();
    var notes_ny_id = $('#notes_ny_id').val();
    var selectedUser = $('#assign_nybest_user option:selected').text();
    var cnt = 0;

    $('#assign_nybest_user_error').html("");
    if (assign_nybest_user == '') {
        $('#assign_nybest_user_error').html("Required");
        cnt = 1;
    }

    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            url: _ASSIGN_NYBEST_USER,
            type: "POST",
            data: {
                "assign_nybest_user": assign_nybest_user,
                "notes_ny_id": notes_ny_id,
                "_token": _CSRF_TOKEN,
                'patient_id': _RECORD_ID,
            },
            success: function(resp) {
                if (resp == 1) {
                    var msg = ' NyBest user successfully assigned';
                    toastr.success(msg);
                    $('.nybest_user_id').html(selectedUser);
                    $('#assign_nybest_user option[value=' + assign_nybest_user + ']').attr('selected',
                        'selected');
                    $('.close').click();
                } else {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }
            }
        })
    }
}

function Assignvalidation() {
    var temp = 0;
    var assign_to = $("#assign_id").val();
    $("#assign_to_us_error").html("");
    if (assign_to == "") {
        $("#assign_to_us_error").html("Please select assign user.");
        temp++;
    }
    if (temp == 0) {
        return true;
    } else {
        return false;
    }
}