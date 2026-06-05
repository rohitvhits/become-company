function getEmergencyPhone() {
    var emergency_phone = $('#emergency_phone').val();
    var cnt = 0;
    $('#emergency_phone_error').html("");
    if (emergency_phone.trim() == '') {
        $('#emergency_phone_error').html("Please enter Emergency Phone");
        cnt = 1;

    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _PATIENT_EMERGENCY_PHONE,
            type: "POST",
            data: {
                "emergency_phone": emergency_phone,
                "_token": _CSRF_TOKEN,
                'patient_id': _RECORD_ID,
            },
            success: function(resp) {
                $('#emergency_phones').html(emergency_phone)
                toastr.success(resp.error_msg);
                clearEmergencyPhone()

            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.error_msg);
            }

        })
    }
}

function clearEmergencyPhone() {
    $('.error').html("")
    $('#exampleModal-emergency_phone').modal('hide');
}

function getEmail() {
    var email = $('.email_value').val();
    var regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    var cnt = 0;
    $('#emergency_email_error').html("");

    if (email.trim() == '') {
        $('#emergency_email_error').html("Please enter Email");
        cnt = 1;
    }

    if (email.trim() != '') {
        if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
            $('#emergency_email_error').html("Invalid email address");
            cnt = 1;
        }
    }

    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _PATIENT_EMERGENCY_EMAIL,
            type: "POST",
            data: {
                "email": email,
                "_token": _CSRF_TOKEN,
                'patient_id': _RECORD_ID,
            },
            success: function(resp) {
                $('#emergency_email').html(email)
                toastr.success(resp.error_msg);
                clearEmail()

            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.error_msg);
            }

        })
    }
}

function clearEmail() {
    $('.error').html("");
    $('#exampleModal-email').modal('hide');
}



function updatePhoneDetails(phone) {
    var phone = $('#emergency_phones').html();
    $('#emergency_phone').val(phone)
}

function updateEmailDetails(email) {

    var email = $('#emergency_email').html();
    $('#email').val(email)
}