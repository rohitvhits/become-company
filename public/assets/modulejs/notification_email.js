$("#service_id").select2({
    placeholder: "Select Service"
});

function getResponse(existingId = "") {
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: serviceList,
        success: function(res) {
            var response = "";
            var split = existingId.split(',');
            if (res.data.length != 0) {
                response = '<option value="">Select Service</option>'
                $.each(res.data, function(i, v) {
                    var selected = split.includes(v.id.toString()) ?
                        "selected='selected'" : '';
                    response +=
                        `<option value="${v.id}" ${selected}>${v.name} (${v.types})</option>`;
                });
            }
            $('#service_id').html(response);
        }
    });
}

function fetchNotificationEmails(page) {
    $.ajax({
        url: userList,
        type: "GET",
        data: {
           
            'agency_id': "0",
            'page': page,

        },
        success: function(response) {
            $('#notification_email_id').html("");
            $('#notification_email_id').html(response);
        }
    });

    return false;
}
$('#notification-email-saveId').click(function(e) {
    var selectedPatients = [];
    var selectedCaregivers = [];
    var selectedCaregiversId = [];
    var selectedPatientId = [];
    var selectedServiceId = [];
    $('#notifications_email_error').html("");
    $('#service_id_error').html("");
    $(".patient_checkbox:checked").each(function() {
        selectedPatients.push($(this).val());
        selectedPatientId.push($(this).attr('data-id'));
    });

    $(".caregiver_checkbox:checked").each(function() {
        selectedCaregivers.push($(this).val());
        selectedCaregiversId.push($(this).attr('data-id'))

    });

    var serviceId = $('#service_id').val();
    var desciplineId = $('#descipline_id').val();
    $.each(serviceId,function(i,v){

        if(v.trim() !=""){
            selectedServiceId.push(v.trim());
        }
    })

    var cnt = 0;
    var notificationEmail = $('#notificationEmail').val();
    var validEmail = /^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/;
    if (notificationEmail.trim() == '') {
        $('#notifications_email_error').html("Email is required");
        cnt = 1;
    }

    if (notificationEmail.trim() != '') {
        if (!validEmail.test(notificationEmail)) {
            $('#notifications_email_error').html("Invalid Email Address");
            cnt = 1;
        }

    }

   
    if (selectedCaregivers.length == 0 && selectedPatients.length == 0 && selectedServiceId.length ==0) {
        $('#service_id_error').html("Patient or Caregiver or Service is required");
        cnt = 1;
        console.log(cnt);
    }

    if (cnt == 1) {
        return false;
    } else {
        var forms = $('#addnotificationemail')[0];
        var newForms = new FormData(forms);
        newForms.append('_token', _CSRF_TOKEN);
        newForms.append('patient_id', selectedPatientId);
        newForms.append('caregivers_id', selectedCaregiversId);
        newForms.append('agency_id',0);
        $.ajax({
            url: _SAVE_NOTIFICATION_EMAIL,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                $('#add-notification-email-popup').modal('hide');
                $('#addnotificationemail')[0].reset();
                fetchNotificationEmails(1);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }

});
fetchNotificationEmails(1);

$('.add-notification-email').click(function() {

    $('#notificationId').val('');
    $('#notificationEmail').val('');
    $('.notification_checkbox').prop('checked', false);
    getResponse();
    // getDiscipline();
    $('#service_id').val(null).trigger('change');
    $('#add-notification-email-popup').modal('show');
});

function editNotificationEmail(id) {
    $.ajax({
        method: 'GET',
        url: _EDIT_NOTIFICTION_EMAIL,
        data: {
            'id': id,

        },
        success: function(response) {
            if (response.data.caregivers_id != "") {
                var splitData = response.data.caregivers_id.split(',');
                $.each(splitData, function(i, v) {

                    $('#caregiver_notification_email' + v).prop("checked", true);
                })
            }

            if (response.data.patients_id != "") {
                var splitData = response.data.patients_id.split(',');
                $.each(splitData, function(i, v) {

                    $('#patient_notification_email' + v).prop("checked", true);
                })
            }
            $('#notificationId').val(id)
            $('#notificationEmail').val(response.data.email)
            $('.notification-emails').html("Edit Notification Email")
            $('#add-notification-email-popup').modal('show');
            getResponse(response.data.service_id);
            // getDiscipline(response.data.discipline_id);
        },
        error: function(jxr) {

        }

    });
}

function deleteNotificationEmail(id) {
    $.confirm({
        title: 'Are you sure delete notification email?',
        columnClass: "col-md-6",
        content: "",

        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url: _DELETE_NOTIFICTION_EMAIL,
                        type: "get",
                        data: {
                            'id': id,

                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            fetchNotificationEmails(1);
                        }
                    })
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}

function getDiscipline(existingId = ""){
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: DISCIPLINE_LIST,

        success: function(res) {
            var response = "";
            var split = existingId.split(',');
            if (res.data.length != 0) {
                response = '<option value="">Select Discipline</option>'
                $.each(res.data, function(i, v) {
                    if (v.types != "" || v.types != "") {
                        var selected = split.find(o => o == v.name);
                        var selecteds = '';
                        if (selected) {
                            selecteds = "selected='selected'";
                        }
                        response += '<option value="' + v.name + '" ' + selecteds + '>' + v.name +'</option>';

                    }
                })
            }

            $('#discipline_id').html("");
            $('#discipline_id').html(response);

        }
    })
}