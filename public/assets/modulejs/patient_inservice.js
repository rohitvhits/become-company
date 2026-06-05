$('#update-inservice-status').click(function(e) {
    var inservice_status = $("#inservice_status").val();
    var ct = 0;
    $('.inservice_status_error').html("");
    if (inservice_status == '') {
        $('.inservice_status_error').html("Required");
        ct = 1;
    }

    if (ct == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            type: "post",
            url: _PATIENT_INSERVICES,

            data: {
                '_token': _CSRF_TOKEN,
                'patient_id': _RECORD_ID,
                'inservice_status': inservice_status
            },
            success: function(response) {
                $('#inservices_status').html(inservice_status)
                toastr.success(response.error_msg);
                CloseInserviceStatus();
            },
            error: function(error) {
                toastr.error(response.error_msg);
            }
        });
    }
})

function CloseInserviceStatus() {
    $('.error').html("");
    $('#exampleModal-inservice_status').modal('hide');
}

$('#update-inservice-status-two').click(function(e) {
    var inservice_status = $("#inservice_status_two").val();
    var ct = 0;
    $('.inservice_status_two_error').html("");
    if (inservice_status == '') {
        $('.inservice_status_two_error').html("Required");
        ct = 1;
    }

    if (ct == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            type: "post",
            url: _PATIENT_INSERVICE_TWO_APPOINTMENT,

            data: {
                '_token': _CSRF_TOKEN,
                'patient_id': _RECORD_ID,
                'inservice_status': inservice_status
            },
            success: function(response) {
                $('#inservices_status_two').html(inservice_status)
                toastr.success(response.error_msg);
                CloseInserviceStatusTwo();
            },
            error: function(error) {
                toastr.error(response.error_msg);
            }
        });
    }
})

function CloseInserviceStatusTwo() {
    $('.error').html("");
    $('#exampleModal-inservice_status_two').modal('hide');
}