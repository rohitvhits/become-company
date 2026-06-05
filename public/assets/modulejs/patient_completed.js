function getCompletedDate() {
    var due_date = $('#completed_date_id').val();
    var cnt = 0;
    $('#completed_date_id_error').html("");
    if (due_date.trim() == '') {
        $('#completed_date_id_error').html("Please enter Completed Date");
        cnt = 1;

    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _PATIENT_COMPLETED,
            type: "POST",
            data: {
                "completed_date": due_date,
                "_token": _CSRF_TOKEN,
                'patient_id': _RECORD_ID,
            },
            success: function(resp) {

                if (resp == 1) {

                    var msg = ' Completed date successfully updated';
                    toastr.success(msg);
                    location.reload()
                    $('#completed_date_id').html(due_date)
                    $('#comp_id').html(due_date)
                    $('#closeds').click();
                    
                } else {

                    toastr.error("Sorry, something went wrong. Please try again.");
                }
            }

        })
    }

}

function clearCompletedData(){
    $('#completed_date_id_error').html("");
}

