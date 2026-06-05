var i = 0;
var smsCounter = 0;
var globalNotesArray = [];
function loadAllNotes(){

    $('.notes-messages').html("");
    $('#loadertag1').attr('style', '');
    var checked = $('input[name="radio1"]')
    var mess = $("input[name='radio1']:checked").val();
    
    var messType = $("input[name='radioType']:checked").val();
  
    if(userTypeFk != 184){
        mess = "Agency"
    }

    var agency_id = _AGENCYID;

    $.ajax({
        url: _PATIENT_NOTES+"/"+_RECORD_ID,
        type: "post",
        data: {
            _token: _CSRF_TOKEN,
            'readMessage': mess,
            'agency_id': agency_id,
          
        },
        success: function(response) {
            globalNotesArray = response;
           
            response.forEach(element => {
                var label ="";
                if(element.patient_id !=_RECORD_ID){
                    label = '<span style="margin-left:10px;top: 0;background: #00BBE0;padding: 1px 5px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;">Merge</span>'
                }

                add_message_obj(element.id, element.first_name,
                    '/img/demo/av1.jpg', element.message, element
                    .created_date, element.type, element.sender_id,element.hha_notes,element.flag,element.call_flag,label);

            });
            setTimeout(()=>{
                $('#loadertag1').attr('style', 'display:none;');
            },3000)
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
    return false;
}

function getClickAble(id) {
    $('input[name="agency_id"]').val(id);
    loadAllNotes();
    $('#user_agency_id').val(id);

}

function getClickAbleNew(){
   // loadAllNotes();
}


function add_message_obj(mid, name, img, msg, date, type, sender_id,hhaId="",flag,callFlag,label="") {
    var i = 0;
    
    i = i + 1;

    var inner = $('.notes-messages');
    var time = new Date(date);
    var date = (time.getMonth() + 1) + '/' + time.getDate() + '/' + time.getFullYear();

    var hours = time.getHours();
    var minutes = time.getMinutes();
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    var id = 'msg-' + i;
    //  var type="Receive";
    var ondelete = '';
    var hhaImage="";
    if(hhaId !=0){
        hhaImage=' <img src="'+_BASE_URL+'/img/hha.png'+'" title="HHA" alt="HHA" style="height: 15px; width: 15px;">';
    }
    var idname = "";
    if(flag == 0){
        var flags = 'Flag';
        var flagClass = 'messags-div';
        var flagButtonclass = "pull-right btn-sm  d-none d-md-block mr-2 ml-2";
    }
    else{
        var flags = 'Flagged';
        var flagClass = 'messags-div-flag';
        var flagButtonclass = "pull-right  btn-sm  d-none d-md-block mr-2";
    }

    var  flag_html = '';    
    if(_PATIENT_RECORD_NOTES_PERMISSION && label ==""){
        flag_html = '<a class="'+flagButtonclass+'" onclick="flagNotesChange('+mid+');" class="" title="'+flags+'"><i class="fa fa-flag"></i> <span id="view_flag_text_'+mid+'">'+flags+'</span></a>';
    }
    var  delete_notes_permissions = ''; 
    if(_PATIENT_RECORD_NOTES_DELETE_PERMISSION && label ==""){
        delete_notes_permissions = '<a class="ml-2" onclick="patientNotesDelete('+mid+')" title="Delete"><i class="fa fa-trash"></i></a>';
    }
    var cflag = '';
    if(callFlag !=null){
        cflag = callFlag;
    }   
    // inner.append('<div class="col-12 results"><div id="' + id + '" class=" border-bottom user-' + idname + '"><p class="page-description mt-1 w-100 text-muteds">' +
    //     '<span ><i class="fa fa-user-circle mr-1    "></i> ' + name + '  (' + type + ') '+hhaImage+'</span> <span class="time pull-right text-muted tx-12"> <i class="mdi mdi-clock-outline"></i>' + date +
    //     ' ' + hours + ':' + minutes + '</span></p><p class="page-description mt-1 w-100 text-muteds">' +
    //     '<span class="msg"> ' + msg +'</span><span class="pull-right">' + ondelete + '</span></p></div></div>');
    inner.append('<div class="notes" id="np_'+mid+'"><p id="' + id + '" class="user-' + idname + '">' +
        '<div class="notes-header"><div id="flag_div_id_'+mid+'" class="'+flagClass+'"><div class="message-footer"><p class="pl-1" style="display:flex;margin-bottom:0px" >' + name + '  (' + type + ') &nbsp;' +cflag+'<br>' + label+'</p><div class="time" style="display:flex;align-items:center"> <span>' + date +
        ' ' + hours + ':' + minutes +'</span> '+flag_html+delete_notes_permissions+'</div></div></div> <p class="msg notes-content text-mute" style="white-space:pre-line">' + msg + hhaImage+'<span class="pull-right">' + ondelete + '</span></p></span></p></div>');
    $('#' + id).hide().fadeIn(800);
   
    $('#sms-messages').animate({
        scrollTop: inner.height()
    }, 20);
}


function sendMessagefile() {
    
    var alldata = new FormData($('#attachsubmits')[0]);
   
    var id = _RECORD_ID;
    var name = "you";
    var message = document.getElementById('text-sms-box').innerText || document.getElementById('text-sms-box').textContent || '';
    var radio1 = $('input[name="radio1"]:checked').val();
    var callFlag = $('input[name="radioType"]:checked').val();
    var hhaNotesflag = $('#notes_message_id').is(":checked");
    var sendHHANotes = 0;
    if(hhaNotesflag){
        sendHHANotes = 1;
    }
    
    if(userTypeFk ==184){
        alldata.append('radioType',callFlag);
    }else{
        alldata.append('radioType',"Normal");
    }

    // Extract tags from the contenteditable div before sending
    if (typeof updateTags === 'function') {
        updateTags();
    }

    alldata.append('msg-box',message.trim());
    alldata.append('tags',JSON.stringify(tagsArr));
    alldata.append('agency_id',radio1)
    alldata.append('hha_notes',sendHHANotes)

    if(userTypeFk !=184){
        radio1 = "Agency"
    }
  
    $('#notes_error_msg').html("");
    if (id != 0 && message.trim() != "") {
        $('#send_message_loader').removeClass('hide');
        $.ajax({
            type: 'POST',
            data: alldata,
            url: _SAVE_PATIENT_NOTES+"/" + id,
            dataType: "json",
            mimeType: "multipart/form-data",
            contentType: false, 
            processData: false,

            success: function(response) {
                $('#send_message_loader').addClass('hide');
                $('#text-sms-box').html("");
                globalNotesArray.push(response)
                addSMSmessage('You', 'Send', message, "", true,sendHHANotes,0,response.id,callFlag);
              
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#send_message_loader').addClass('hide');
                console.log(textStatus, errorThrown);
            }
        });
    }else{
        $('#notes_error_msg').html("Enter Message");
    }
}

function addSMSmessage(name, ctype, msg, file, clear,hhaId,flag,insertId,callFlag) {

    smsCounter = smsCounter + 1;
    var inner = $('.notes-messages');
    var time = new Date();
    var hours = time.getHours();
    var minutes = time.getMinutes();
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    var id = 'msg-' + smsCounter;
    var idname = name.replace(' ', '-').toLowerCase();
    
    var hhaImage="";
    if(hhaId !=0){
       
        hhaImage=' <img src="'+_BASE_URL+'/img/hha.png'+'" title="HHA" alt="HHA" style="height: 15px; width: 15px;">';
    }

    // inner.append('<p id="' + id + '" class="user-' + idname + '">' +
    //     '<span class="msg-block"> <strong>' + name + ' (' + ctype + ')</strong> <span class="time"> ' + hours +
    //     ':' + minutes + '</span>' +
    //     '<span class="msg">' + msg + ' ' + file + hhaImage+'</span></span></p>');
    var flags = 'Flag';
    var flagClass = 'messags-div';
    var flagButtonclass = "pull-right  d-none d-md-block mr-2 ml-2";
    var flag_html = '';
    if(_PATIENT_RECORD_NOTES_PERMISSION){
        flag_html = '<a class="'+flagButtonclass+'" onclick="flagNotesChange('+insertId+');" class="" title="'+flags+'"> <i class="fa fa-flag"></i> <span id="view_flag_text_'+insertId+'">'+flags+'</span></a>';
    }

    var  delete_notes_permissions = ''; 
    if(_PATIENT_RECORD_NOTES_DELETE_PERMISSION){
        delete_notes_permissions = '<a class="ml-2" onclick="patientNotesDelete('+insertId+')" title="Delete"><i class="fa fa-trash"></i></a>';
    }
	if(callFlag == undefined){
        callFlag = 'Normal';
    }
    inner.append('<div class="notes" id="np_'+insertId+'"><p id="' + id + '" class="user-' + idname + '">' +
        '<div class="notes-header"><div id="flag_div_id_'+insertId+'" class="'+flagClass+'"><div class="message-footer"><p class="pl-1" style="display:flex;margin-bottom:0px;margin-top: 6px;" >' + name + '  (' + ctype + ') ' + callFlag+
        '</p><div class="time" style="display:flex;align-items:center"> <span>' + hours + ':' + minutes +" </span>"+flag_html+delete_notes_permissions+'</div></div></div><p class="msg notes-content text-mute" style="white-space:pre-line">' + msg + hhaImage+'</span></p></p></div>');

    $('#' + id).hide().fadeIn(800);
    if (clear) {
        $('#text-sms-box').html('').focus();
    }
    $('#sms-messages').animate({
        scrollTop: inner.height()
    }, 1000);
}


$("#print_id").click(function () {
   
    var htmleResponse = `<table class="table" style="border-collapse: collapse;">
            <thead>
                <th style="border:1px solid #e3e7ed;font-weight: 700;font-size: 0.75rem;letter-spacing: 0.031rem;padding: 0.312rem 0.937rem;border-collapse: collapse;">#</th>
<th style="border:1px solid #e3e7ed;font-weight: 700;font-size: 0.75rem;letter-spacing: 0.031rem;padding: 0.312rem 0.937rem;border-collapse: collapse;"> Type</th>
                <th style="border:1px solid #e3e7ed;font-weight: 700;font-size: 0.75rem;letter-spacing: 0.031rem;padding: 0.312rem 0.937rem;border-collapse: collapse;">Message Type</th>
                
                <th style="border:1px solid #e3e7ed;font-weight: 700;font-size: 0.75rem;letter-spacing: 0.031rem;padding: 0.312rem 0.937rem;border-collapse: collapse;">Message</th>
                <th style="border:1px solid #e3e7ed;font-weight: 700;font-size: 0.75rem;letter-spacing: 0.031rem;padding: 0.312rem 0.937rem;border-collapse: collapse;">Created Date</th>
                <th style="border:1px solid #e3e7ed;font-weight: 700;font-size: 0.75rem;letter-spacing: 0.031rem;padding: 0.312rem 0.937rem;border-collapse: collapse;">Created By</th>
            </thead>
            <tbody>`
                if(globalNotesArray.length !=0){
                    var cnt =1
                    $.each(globalNotesArray,function(i,v){
                        htmleResponse +=`<tr>
                            <td style="border: 1px solid #e3e7ed;font-size:12px;padding-left:5px;border-collapse: collapse;">${cnt++}</td>
                            <td style="border: 1px solid #e3e7ed;font-size:12px;padding-left:5px;border-collapse: collapse;">${v.type}</td>
                            <td style="border: 1px solid #e3e7ed;font-size:12px;padding-left:5px;border-collapse: collapse;">${v.call_flag}</td>
                            
                            <td style="border: 1px solid #e3e7ed;white-space:pre-line;font-size:12px;padding-left:5px;border-collapse: collapse;">${v.message}</td>
                            <td style="border: 1px solid #e3e7ed;font-size:12px;padding-left:5px;border-collapse: collapse;">${moment(v.created_date).format('MM/DD/YYYY hh:mm A')}</td>
                            <td style="border: 1px solid #e3e7ed;font-size:12px;padding-left:5px;border-collapse: collapse;">${v.first_name+' '+v.last_name}</td>
                        </tr>`
                    })
                }else{
                    htmleResponse =`<tr><td style="border: 1px solid #e3e7ed;font-size:12px;padding-left:5px;border-collapse: collapse;" colspan="5">No record available</td></tr>`
                }
            `</tbody>
    </table>`
    $('#chat-messages-inner-new').html("")
    $('#chat-messages-inner-new').html(htmleResponse)
    var divToPrint=document.getElementById('chat-messages-inner-new');

    var newWin=window.open('','Print-Window');

    newWin.document.open();

    newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

    newWin.document.close();

    setTimeout(function(){newWin.close();},10);
});

function editNotes(){
    $('#patient_basic_note_id').val($('#html_patient_nots').val());
}

function updatePatientNotes(){
    $.ajax({
        type:"POST",
        url:_UPDATE_PATIENT_NOTES,
        data:{
            '_token':_CSRF_TOKEN,
            'record_id':_RECORD_ID,
            'notes':$('#patient_basic_note_id').val()
        },
        success:function(response){
            toastr.success('Notes successfully updated');
            $('#html_patient_nots').val($('#patient_basic_note_id').val());
            $('#html_patient_notes_id').html($('#patient_basic_note_id').val())
            $('#exampleModal-patient-notes').modal('hide');
        },
        error:function(jqXHR){
toastr.error(jqXHR.responseJSON.error_msg);
        }
    })
}

function patientNotesDelete(id){
    $.confirm({
        title: 'Delete',
        columnClass: "col-md-6",
        type: 'blue',
        content: 'Are you sure you want to permanently delete this note?',
        buttons: {
            formSubmit: {
                text: 'Delete',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        type: "POST",
                        url: _PATIENT_DELETE_NOTES,
                        data: {
                            '_token':_CSRF_TOKEN,
                            'patient_id':_RECORD_ID,
                            'id': id
                        },
                        success: function(res) {
                            $('#np_'+id).remove();
                            toastr.success(res.error_msg);
                        },
                        error:function(jqr){
                            toastr.error(jqr.responseJSON.error_msg);
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