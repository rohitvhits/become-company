function CloseTrainingStatus() {
    $('#exampleModal-training_status').modal('hide');
}

$('#update-training-status').click(function(e) {
    var inservice_status = $("#training_status").val();
    var ct = 0;
    $('.training_status_error').html("");
    if (inservice_status.trim() == '') {
        $('.training_status_error').html("Required");
        ct = 1;
    }

    if (ct == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            type: "post",
            url: _TRAINING_STATUS,

            data: {
                '_token':_CSRF_TOKEN,
                'patient_id':_RECORD_ID,
                'training_status': inservice_status
            },
            success: function(response) {
                $('#training_statuss').html(inservice_status)
                toastr.success(response.error_msg);
                CloseTrainingStatus();
            },
            error: function(error) {
                toastr.error(response.error_msg);
            }
        });
    }
})

function getTrainingDueDate() {
    var due_date = $('#traning_due_date_id').val();
    var cnt = 0;
    $('#traning_due_date_error').html("");
    if (due_date.trim() == '') {
        $('#traning_due_date_error').html("Please enter Training Due Date");
        cnt = 1;

    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _TRAINING_DUE_DATE,
            type: "POST",
            data: {
                "traning_due_date": due_date,
                "_token": _CSRF_TOKEN,
                'patient_id': _RECORD_ID,
            },
            success: function(resp) {
                var msg = 'Training Due date successfully updated';
                toastr.success(msg);
                location.reload();
            }

        })
    }
}

function updateDetails(value) {
    var value = $('#training_statuss').html();
    $('#training_status').val(value)
}

function updateTrainingDueDate(date) {
    $('#traning_due_date_id').val(date)
}