function hideCombineAppointment() {
    $("#exampleModal-merge-record").modal("hide");
    $(".error").html("");
}

function combineRecord() {
    var appointment_id = $("#appointment_id").val();
    $("#appointment_id_error").html("");
    var cnt = 0;
    if (appointment_id.trim() == "") {
        $("#appointment_id_error").html("Appointment Id is required");
        cnt = 1;
    }

    if (cnt == 1) {
        return false;
    } else {
        $.confirm({
            title: 'Confirmation',
            columnClass: "col-md-6",
            content: 'Are you sure you want to merge the record <b>' +  appointment_id  +'</b> to <b>'+_RECORD_ID+'</b>?',
            buttons: {
                formSubmit: {
                    text: 'Yes',
                    btnClass: 'btn-primary',
                    action: function() {
                        $.ajax({
                            type: "POST",
                            url: _PATIENT_COMBINE_APPOINTMENT,
                            data: {
                                record_id: _RECORD_ID,
                                appointment_id: appointment_id,
                                _token: _CSRF_TOKEN,
                            },
                            success: function (res) {
                                toastr.success(res.message);
                            },
                            error: function (xhr) {
                                toastr.error(xhr.responseJSON.message);
                            },
                        });
                    }
                },
                cancel: {
                    'text' : 'No'
                },
            }
        });
        
    }
}