function smsLogs(page) {
    $.ajax({
        url: _PATIENT_SMS_LOGS_LIST+'/'+_RECORD_ID + "?page=" + page,
        type: "GET",
        success: function (res) {
            $("#sms_logs_id").html("");
            $("#sms_logs_id").html(res);
        },
    });
}


function loadAllTextMessages() {
    $('.text-notes-messages').html("");
    $('#loadertag1').attr('style', '');


    var agency_id = _AGENCYID;

    $.ajax({
        url: _GET_SMS_TEXT,
        type: "get",
        data: {

            'case_id': _RECORD_ID
        },
        success: function(response) {

            var response = response.data;
            response.forEach(element => {
                add_message_obj_new(element.id, element.user_details.first_name,
                    '', element.message, element
                    .created_date, element.type, element.user_details.id);

            });
            setTimeout(() => {
                $('#loadertag1').attr('style', 'display:none;');
            }, 3000)

            // add_message('You', 'img/demo/av1.jpg', input.val(), true);
            // You will get response from your PHP page (what you echo or print)
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
    return false;
}

function add_message_obj_new(mid, name, img, msg, date, type, sender_id, clear) {
    //alert(sender_id);
    i = i + 1;

    var inner = $('.text-notes-messages');
    var time = new Date(date);
    var date = (time.getMonth() + 1) + '/' + time.getDate() + '/' + time.getFullYear();

    var hours = time.getHours();
    var minutes = time.getMinutes();
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    var id = 'msg-' + i;
    //  var type="Receive";
    var ondelete = '';


    var idname = "";
    inner.append('<p id="' + id + '" class="user-' + idname + '">' +
        '<span class="msg-block"><strong>' + name + '</strong><span class="time"> ' + date +
        ' ' + hours + ':' + minutes + '</span>' +
        '<span class="msg">' + msg + '<span class="pull-right">' + ondelete + '</span></span></span></p>');
    $('#' + id).hide().fadeIn(800);
    if (clear) {
        $('.text-chat-message textarea').val('').focus();
    }
    $('#text-sms-messages').animate({
        scrollTop: inner.height()
    }, 20);
}

function sendTextMessagefile() {
    var alldata = new FormData($('#textMessageSubmits')[0]);
    var id =_RECORD_ID;
    var name = "you";
    var mobile = _MOBILE;
    var message = $('#smsTextMessage').val();

    alldata.append('mobile', _MOBILE);
    alldata.append('case_id', id);
    alldata.append('message', message);
    alldata.append('_token', _CSRF_TOKEN);
    if (id != 0 && message != "") {
        $.ajax({
            type: 'POST',
            data: alldata,
            url: _SEND_SMS_TEXT,
            dataType: "json",
            mimeType: "multipart/form-data",
            contentType: false,
            processData: false,

            success: function(response) {
                $('#textMessageSubmits')[0].reset();
                var response = response.data;
                i = i + 1;

                var inner = $('.text-notes-messages');
                var time = new Date(response.created_date);
                var date = (time.getMonth() + 1) + '/' + time.getDate() + '/' + time.getFullYear();

                var hours = time.getHours();
                var minutes = time.getMinutes();
                if (hours < 10) hours = '0' + hours;
                if (minutes < 10) minutes = '0' + minutes;
                var id = 'msg-' + Math.floor(Math.random() * 1000000);
                //  var type="Receive";
                var ondelete = '';


                var idname = "";
                inner.append('<p id="' + id + '" class="user-' + idname + '">' +
                    '<span class="msg-block"><strong>' + response.user_details.first_name + '</strong><span class="time"> ' + date +
                    ' ' + hours + ':' + minutes + '</span>' +
                    '<span class="msg">' + response.message + '<span class="pull-right">' + ondelete + '</span></span></span></p>');
                $('#' + id).hide().fadeIn(800);

                $('#text-sms-messages').animate({
                    scrollTop: inner.height()
                }, 20);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error(jqXHR.responseJSON.error_msg)
            }
        });
    } else {
        $('#smsTextMessageError').html("Required");
        return false;
    }

}