function loadPassAppointment(){
    $.ajax({
        url: _PATIENT_LOAD_PAST_LIST + "?id=" + _RECORD_ID,
        type: "GET",
        success: function (res) {
            console.log("sadasd");
            $("#pass_appointment_details").html("");
            $("#pass_appointment_details").html(res);
        },
    });
    return false;
}

function savePastAppointment(){
    var date = $('#date_id').val();
    var time = $('#timeid').val();
    var doctor_id = $('#doctor_id').val();
    var location_id = $('#location_id').val();
    var times_id = $('#times_id').val();
    var service_id = $('#service_id').val();
    $('#date_error').html("");
    $('#time_error').html("");
    console.log(service_id)
    var cnt = 0;

    if (location_id == '') {
        $('#exampleModal-4 #location_error').html("Please select Location");
        cnt = 1;
    }
    if (service_id.length == 0) {
        $('#exampleModal-4  #service_error').html("Please select Services");
        cnt = 1;
    }

    if (date.trim() == '') {
        $('#date_error').html("Please select Appointment Date ");
        cnt = 1;
    }

    if(_RECORD_TYPE =='Caregiver'){
        if (time.trim() == '') {
            $('#time_error').html("Please select Appointment Time");
            cnt = 1;
        }
    }else{
        if (times_id.trim() == '') {
            $('#time_error').html("Please select Appointment Time");
            cnt = 1;
        }
    }

    if(_RECORD_TYPE =='Caregiver'){
        if (time.trim() != '') {
            $.ajax({
                async: false,
                global: false,
                url: _PATIENT_LOCATION_REMAIN_TIME_SLOT,
                type: "GET",
                data: {
                    "time": time,
                    'date': date
                },
                success: function(res) {
                    if (res == 1) {
                        cnt =0
                    } else {
                        $('#time_error').html("Slot limit over");
                        cnt = 1;
                    }
                }
            })

        }

    }


    if(cnt ==1){
        return false;
    }else{
        var forn = $("#save_past_appointment_id")[0];
        var formData = new FormData(forn);
        formData.append("_token", _CSRF_TOKEN);
        $.ajax({
            url: _PATIENT_SAVE_PAST_APPOINTMENT,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success(res.error_msg)
                $('.close').click();
                loadPassAppointment()
            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg)
            }
        });
    }
}

$('.addAppointment').click(function(e){
    $("#save_past_appointment_id")[0].reset();
    $(".error").html("");
})