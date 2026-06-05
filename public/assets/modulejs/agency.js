$('#save-agency-token').submit(function(e){
    var agency_notes_token = $('#agency_notes_token').val();
    var ip_block = $('#ip_block').val();
    var cnt =0;

    $('#ip_block_error').html("");
    $('#agency_notes_token_error').html("");
    if(agency_notes_token.trim() ==''){
        $('#agency_notes_token_error').html("Notes is Required");
        cnt =1;
    }

    if(ip_block.trim() ==''){
        $('#ip_block_error').html("Block IP Address is Required");
        cnt =1;
    }

    if(cnt ==1){
        return false
    }else{
        return true;
    }
})


function getAllGenerateToken(page =1){
    console.log("asdasdas")
    $.ajax({
        async:false,
        global:false,
        url: _AGENCY_TOKEN_URL,
        data:{
            'agency_id':_AGENCY_ID,
            page:page,
            type:'token'
        },
        success:function(res){
            $('#token_ajax_id').html("")
            $('#token_ajax_id').html(res)

        }
    })
}


$(document).on('click', '.agn_token_id .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    getAllGenerateToken(page);
});

function tokenWiseDetailsShow(id){
    console.log(id)
}

function edit(id){
    var name = $('#token_name_'+id).val()
    $('#agency_update_notes_token').val(name);
    $('#agency_update_token').modal('show');
    $('#agency_token_id').val(id);
}

$('#update-agency-token').click(function(e){
    var name = $('#agency_update_notes_token').val();
    var id = $('#agency_token_id').val();
    var cnt =0;
    $('#edit_agency_notes_token_error').html("");

    if(name ==''){
        $('#edit_agency_notes_token_error').html("Please select Name");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        $.ajax({
            async:false,
            global:false,
            url: _AGENCY_TOKEN_UPDATE_URL,
            type:"POST",
            data:{
                'id':id,
                'name':name,
                '_token':_CSRF_TOKEN
            },
            success:function(res){
                $('#token_name_'+id).val(res.data.name);
                toastr.success(res.error_msg);
                $('#agency_update_token').modal('hide');
                $('#view_name'+id).html(res.data.name);

            },
            error:function(jqr){
                toastr.error(jqr.responseJson.error_msg)
            }
        })
    }
})

setTimeout(function() {
    $('.alert-success').fadeOut('fast');
}, 3000);
$("#start_date, #end_date").datepicker();
$("#end_date").change(function() {
    var startDate = document.getElementById("start_date").value;
    var endDate = document.getElementById("end_date").value;

    if ((Date.parse(endDate) <= Date.parse(startDate))) {
        alert("End date should be greater than Start date");

        document.getElementById("end_date").value = "";

    }
});
$(document).on('click', '.addDomain', function(e) {
    $(this).attr('data-id', '');
    $("#mid").val('');
    $('#ModalLabel').html('Add Domain');
    $('#domain_id').val('');
    $('#exampleModal-4').modal('show');
})

function validation() {

    var agency_fk = $('#agency_fk').val();
    var item = $('#item').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    if (agency_fk == '' && item == '' && start_date == '' && end_date == '') {
        alert('please select any one');
        return false;
    } else {
        return true;
    }
}

function export_data() {

    var agency_fk = $('#agency_fk').val();
    var item = $('#item').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var temp1 = _AGENCT_RATE_EXPORT+'?agency_fk=' + agency_fk + '&item=' + item + '&start_date=' + start_date + '&end_date=' + end_date;

    $('#test_rate').attr("style", '');
    $('#test_rate').attr("href", temp1);
}

function validateIPAddress(ip) {
    var expr = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
    return expr.test(ip);
}
$('input[name="daterange"]').daterangepicker({
    opens: 'left'
}, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('MM-DD-YYYY') + ' to ' + end.format('MM-DD-YYYY'));
});

function checkGenerateInvoice(id) {
    $.ajax({
        url: _AGENCT_GENERATE_LAST_MONTH,
        type: "GET",
        data: {
            'month':_LAST_MONTH_INVOICE,
            'year': _LAST_MONTH_YEARE_INVOICE,
            'agency_fk':_AGENCYID
        },
        success: function(response) {
            if (response == 1) {
                window.location.href = _AGENCY_WISE_INVOICE+"/"+_AGENCYID;
            } else {
                alert("Invoice already generated");
                return false;
            }
        }
    });
}

function getTokenGenerate() {

    $.confirm({
        title: 'Are you sure generate token?',
        columnClass: "col-md-6",

        content: con,
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-danger',
                action: function() {
                    $('#directId').submit();
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}

function deleteRecordAgencies(id) {
    var url = _AGENCY_DELETE;
    $.confirm({
        title: 'Delete',
        columnClass: "col-md-6",
        content: 'Are you sure delete record?',
        buttons: {
            formSubmit: {
                text: 'Delete',
                btnClass: 'btn-danger',
                action: function() {
                    window.location.href = url + '/' + id;
                }
            },
            cancel: function() {
                //close
            },
        },
    });
}

$(document).on('click', '.add-agency-wise-sms', function(e) {
    $('#agency_wise_sms_type_error').html("");
    $('#agency_wise_sms_message_error').html("");
    $('#agency_wise_sms_type').val('');
    $('#agency_wise_sms_message').val('');
    $('#SmsLable').html('Add SMS');
    $('#add-agency-wise-sms-popup').modal('show');
})

$(document).on('click', '.edit-sms-detail', function(e) {
    $('#agency_wise_sms_type_error').html("");
    $('#agency_wise_sms_message_error').html("");
    var dataId = $(this).attr('data-id');

    var type = $('#sms_type' + dataId).html();
    var msg = $('#sms_msg' + dataId).html();

    $('#SmsLable').html('Edit SMS');
    $('#smsMId').val(dataId);
    $('#agency_wise_sms_type').val(type);
    $('#agency_wise_sms_message').val(msg);
    $('#add-agency-wise-sms-popup').modal('show');
})

$('body').on('click', '.delete-sms-detail', function(e) {
    var msg = "you want to delete this sms?";
    var id = $(this).attr('data-id');
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",

        content: msg,
        buttons: {
            formSubmit: {
                text: 'DELETE',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url: _AGENCY_WISE_SMS_DELETED,
                        type: "POST",
                        data: {
                            'id': id,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);

                        }
                    })
                }
            },
            cancel: function() {
                //close
            },
        },
        onContentReady: function() {

        }
    });
});

$('#agency-wise-sms-saveId').click(function(e) {
    $('#agency_wise_sms_message_error').html("");
    $('#tele_agency_wise_sms_message_error').html("");
    var send_sms_eng = $('#send_sms_eng').val();
    var send_sms_spanish = $('#send_sms_spanish').val();
    var appointment_send_book_eng = $('#appointment_send_book_eng').val();
    var appointment_send_book_spanish = $('#appointment_send_book_spanish').val();

    var tele_send_sms_eng = $('#tele_send_sms_eng').val();
    var tele_send_sms_spanish = $('#tele_send_sms_spanish').val();
    var tele_remind_send_sms_spanish = $('#tele_remind_send_sms_spanish').val();
    var tele_remind_send_sms_eng = $('#tele_remind_send_sms_eng').val();
    var cnt = 0;
    if (send_sms_eng == '' && send_sms_spanish == '' && appointment_send_book_eng == '' && appointment_send_book_spanish == '') {
        $('#agency_wise_sms_message_error').html("Please Enter Message");
        cnt = 1;
    }
    if(tele_send_sms_eng == '' && tele_send_sms_spanish == ''  && tele_remind_send_sms_spanish == "" && tele_remind_send_sms_eng ==""){
        $('#tele_agency_wise_sms_message_error').html("Please Enter Message");
        cnt = 1;
    }

    if (cnt == 1) {
        return false;
    } else {
        var forms = $('#add-agency-wise-sms-form')[0];
        var newForms = new FormData(forms);
        newForms.append('_token', _CSRF_TOKEN);

        $.ajax({
            url: _AGENCY_WISE_SMS_SAVE,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);

            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });

    }
})

$(document).on('click', '.add-notification-email', function(e) {
    $('#notification_email_error').html("");
    $('#service_id').html("");
    getResponse();
    getDiscipline();
    $('#add-notification-email-popup').modal('show');
})

function notificationEmailList(page) {
    $.ajax({
        url: _AGENCY_NOTIFICATION_EMAIL_LIST,
        type: "GET",
        data: {
            'type': 'notifiction-email',
            'agency_id':_AGENCYID,
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
    $(".patient_checkbox:checked").each(function() {
        selectedPatients.push($(this).val());
        selectedPatientId.push($(this).attr('data-id'));
    });

    $(".caregiver_checkbox:checked").each(function() {
        selectedCaregivers.push($(this).val());
        selectedCaregiversId.push($(this).attr('data-id'))

    });


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

    var serviceId = $('#service_id').val();
    $.each(serviceId,function(i,v){

        if(v.trim() !=""){
            selectedServiceId.push(v.trim());
        }
    })

    if (selectedCaregivers.length == 0 && selectedPatients.length == 0 && selectedServiceId.length == 0) {
        $('#notification_email_error').html("Patient or Caregiver or Service is required");
        cnt = 1;

    }

    var patientStatus=[];
    $(".patient_checkbox_status:checked").each(function() {
        patientStatus.push($(this).val());
    });

    var caregiverStatus=[];
    $(".caregiver_checkbox_status:checked").each(function() {
        caregiverStatus.push($(this).val());
    });

    if (cnt == 1) {
        return false;
    } else {
        var forms = $('#addnotificationemail')[0];
        var newForms = new FormData(forms);
        newForms.append('_token', _CSRF_TOKEN);
        newForms.append('patient_id', selectedPatientId);
        newForms.append('caregivers_id', selectedCaregiversId);
        newForms.append('caregivers_status', caregiverStatus);
        newForms.append('patients_status', patientStatus);

        $.ajax({
            url: _AGENCY_WISE_NOTIFICATION_EMAIL_SAVE,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                $('#add-notification-email-popup').modal('hide');
                $('#addnotificationemail')[0].reset();
                notificationEmailList(1);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });

    }
})

function domainList(page) {
    $.ajax({
        url: _AGENCY_WISE_DOMAIN_LIST,
        type: "GET",
        data: {
            'type': 'domain',
            'agency_id': _AGENCYID,
            'page': page,

        },
        success: function(response) {
            $('#domain_list_id').html("");
            $('#domain_list_id').html(response);
        }
    });

    return false;
}

$('#saveId').click(function(e) {
    var domain = $('#domain_id').val();
    var cnt = 0;
    $('#domain_error').html('');
    var regex = /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/;
    if (domain.trim() == '') {
        $('#domain_error').html("Please enter Domain Name");
        cnt = 1;
    }
    if (domain.trim() != '') {
        if (!regex.test(domain)) {
            $('#domain_error').html("Invalid Domain");
            cnt = 1;
        }
    }

    if (cnt == 1) {
        return false;
    } else {
        var mid = $('#mid').val();
        var forms = $('#submitId')[0];
        var newForms = new FormData(forms);
        newForms.append('_token', _CSRF_TOKEN);
        newForms.append('agency_name', _AGENCY_NAME);
        if (mid != '') {
            newForms.append('id', mid);
        }

        $.ajax({
            url: _AGENCY_WISE_DOMAIN_SAVE,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                $('#exampleModal-4').modal('hide');
                $('#submitId')[0].reset();
                domainList(1);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });

    }
})

$('body').on('click', '.pagination2 a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');

    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    var explode = $(this).attr('href').split('?');

    var explodes = explode[1].split('&');
    console.log(explodes);
    var type = explodes[0].split('type=')[1];

    if (type == 'domain') {

        domainList(page);
        event.preventDefault();
    }

});

$('body').on('click', '#status', function(e) {
    // $('#submitCountry')[0].reset();
    $('#countryBlock').modal('show');
});


$(document).ready(function() {
    var agencyId = $('#agency_id').val();
    countryList(agencyId);
    // ipAddressList(agencyId);
    var checkedNum = $('input[name="checkid[]"]:checked').length;
    if (!checkedNum) {
        $("#allCountryCheck").prop('checked', true);
    } else {
        $("#perCountryCheck").prop('checked', true);
        $("#particularCountry").css('display', 'block');
    }
});

$("#allCountryCheck").change(function() {
    var ischecked = $(this).is(':checked');
    if (ischecked) {
        $("#perCountryCheck").prop('checked', false);
        $("#particularCountry").css('display', 'none');

    }
    if (!ischecked) {
        $(".countryCheck").prop('checked', false);
    }
});

$("#perCountryCheck").change(function() {
    var ischecked = $(this).is(':checked');
    if (ischecked) {
        $("#allCountryCheck").prop('checked', false);
        $("#checkbox_error").html('');
        $("#particularCountry").css('display', 'block');
    }
    if (!ischecked) {
        $("#particularCountry").css('display', 'none');
    }

});

$('#saveCountry').click(function(e) {
    const selectedValues = $('input[name="checkid[]"]:checked').map(function() {
        return $(this).parent().text();
    }).get();

    var allischecked = $("#allCountryCheck").is(':checked');
    var perischecked = $("#perCountryCheck").is(':checked');


    var error = 0;

    if (allischecked == false && perischecked == false) {
        $("#checkbox_error").html('Please check Anyone');
        error = 1;
    } else {
        $("#checkbox_error").html('');
        error = 0;
    }
    if (perischecked == true) {
        var checkedNum = $('input[name="checkid[]"]:checked').length;
        if (!checkedNum) {
            alert('Please select anyone');
            error = 1;
        }
    }

    if (error == 1) {
        return false;
    } else {
        if (allischecked != true) {
            var forms = $('#submitCountry')[0];
            var newForms = new FormData(forms);
            newForms.append('_token', _CSRF_TOKEN);
            newForms.append('selectedValues', selectedValues);
            $.ajax({
                url: _AGENCY_WISE_COUNTRY_SAVE,
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);
                    var agencyId = $('#agency_id').val();
                    $('#countryBlock').modal('hide');

                    countryList(agencyId);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            });
        } else {
            toastr.success('Successfully Allowed Country');
            var agencyId = $('#agency_id').val();
            $('#countryBlock').modal('hide');

        }
    }
});

$('#saveIPAddress').click(function(e) {

    var ip_address = $('#ip_address').val();
    error = 0;

    if (ip_address.trim() == '') {
        $('#ip_address_error').html("Required");
        error = 1;
    } else if (!validateIPAddress(ip_address)) {
        $('#ip_address_error').html("Invalid IP Address");
        error = 1;
    } else {
        $('#ip_address_error').html("");
    }

    if ($('input[name="type"]:checked').length == 0) {
        $('#type_error').html("Required");
        error = 1;
    } else {
        $('#type_error').html("");
    }

    if (error == 1) {
        return false;
    } else {
        var forms = $('#submitIpAddress')[0];
        var newForms = new FormData(forms);
        newForms.append('_token', _CSRF_TOKEN);
        $.ajax({
            url: _AGENCY_WISE_IPADDESS_SAVE,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                var agencyId = _AGENCY_ID;
                $('#exampleModal-5').modal('hide');
                // $('#submitCountry')[0].reset();
                ipAddressList(agencyId);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }

});

$('#exampleModal-5').on('shown.bs.modal', function() {
    // $('#submitId')[0].reset();
    $('#submitIpAddress')[0].reset();
    $('#ip_address').val("");
    $('#ip_address_error').html("");
})

$('#countryBlock').on('hidden.bs.modal', function() {
    // $('#submitCountry')[0].reset();
})

function countryList(id) {

    $.ajax({
        url: _AGENCY_WISE_COUNTY_LIST,
        type: "GET",
        data: {
            'type': 'country',
            'agency_id': {
                id
            },
        },
        success: function(response) {

            $('#country_blocked_list').html("");
            $('#country_blocked_list').html(response);
        }
    });

    return false;
}

function ipAddressList(id) {
    $.ajax({
        url: _AGENCY_WISE_IP_LIST,
        type: "GET",
        data: {
            'type': 'country',
            'agency_id': {
                id
            },
        },
        success: function(response) {

            $('#ip_blocked_list').html("");
            $('#ip_blocked_list').html(response);
        }
    });

    return false;
}

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');

    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    var explode = $(this).attr('href').split('?');

    var explodes = explode[1].split('&');

    var type = explodes[0].split('type=')[1];


    if (type == 'domain') {

        domainList(page);
        event.preventDefault();
    }
    if (type == 'user') {

        loadUserList(page);
        event.preventDefault();
    }

});
$('body').on('click', '.edit-detail', function(e) {
    var dataId = $(this).attr('data-id');
    var texts = $('#domain' + dataId).html();
    $('#mid').val(dataId);
    $('#ModalLabel').html('Edit Domain');
    $('#domain_id').val(texts);
    $('#exampleModal-4').modal('show');
})

$('body').on('click', '.delete-detail', function(e) {
    var msg = "you want to delete this domain?";
    var id = $(this).attr('data-id');
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",

        content: msg,
        buttons: {
            formSubmit: {
                text: 'DELETE',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url: _AGENCY_DOMAIN_DELETE,
                        type: "POST",
                        data: {
                            'id': id,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            domainList(1);
                        }
                    })
                }
            },
            cancel: function() {
                //close
            },
        },
        onContentReady: function() {

        }
    });
});

$('body').on('click', '.edit-ip-address', function(e) {
    var dataId = $(this).attr('data-id');
    var editid = $("#id").val(dataId);
    $('#exampleModal-6').modal('show');
    $('#ip_address_edit_error').html("");
    $.ajax({
        url: _AGENCY_IP_EDIT,
        type: "GET",
        data: {
            'type': 'country',
            'id': {
                dataId
            }
        },
        success: function(response) {
            $("#ip_address_edit").val(response.data.ip_address);
            $("input[value='" + response.data.type + "']").attr('checked', true);

        }
    });
});

$('body').on('click', '#updateIPAddress', function(e) {
    var ip_address = $('#ip_address_edit').val();
    var editid = $("#id").val();
    error = 0;

    if (ip_address.trim() == '') {
        $('#ip_address_edit_error').html("Required");
        error = 1;
    } else if (!validateIPAddress(ip_address)) {
        $('#ip_address_error').html("Invalid IP Address");
        error = 1;
    } else {
        $('#ip_address_error').html("");
    }

    if ($('input[name="type_edit"]:checked').length == 0) {
        $('#type_edit_error').html("Required");
        error = 1;
    } else {
        $('#type_edit_error').html("");
    }


    if (error == 1) {
        return false;
    } else {
        var forms = $('#submitEditIpAddress')[0];
        var newForms = new FormData(forms);
        newForms.append('_token', _CSRF_TOKEN);
        $.ajax({
            url: _AGENCY_IP_UPDATE,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                var agencyId = _AGENCY_ID;
                $('#exampleModal-6').modal('hide');
                // $('#submitCountry')[0].reset();
                ipAddressList(agencyId);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }
});

$('body').on('click', '.delete-ip-address', function(e) {
    var msg = "you want to delete this IP Address?";
    var id = $(this).attr('data-id');
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",

        content: msg,
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url: _AGENCY_IP_DELETE,
                        type: "POST",
                        data: {
                            'id': id,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(res) {
                            toastr.success(res.error_msg);
                            var agencyId = _AGENCYID;
                            ipAddressList(agencyId);
                        }
                    })
                }
            },
            cancel: function() {
                //close
            },
        },
        onContentReady: function() {

        }
    });
});

$(".two_factor_auth").change(function() {
    var status = "N";
    var id = $(this).attr("data-id");
    if (this.checked) {
        status = "Y";
    }

    $.ajax({
        async: false,
        global: false,
        url: _AGENCY_TWO_FACTOR_ENABLE_DISABLED,
        data: {
            'id': id,
            'status': status
        },
        success: function(response) {
            toastr.success(response.error_msg);
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    })

});

$(".password_expired").change(function() {
    var status = "N";
    var id = $(this).attr("data-id");
    if (this.checked) {
        status = "Y";
    }

    $.ajax({
        async: false,
        global: false,
        url: _AGENCY_PASSWORD_EXPIRED_ENABLED_DISABLED,
        data: {
            'id': id,
            'status': status
        },
        success: function(response) {
            toastr.success(response.error_msg);
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    })

});

$(document).on('click', '.log-pegination .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    console.log(page);


    getData(page);
});
$(document).ready(function() {

    /**
     * User log Table Initialize
     */
    $('#loadertag').show();

    loadUserList(1);
    /**
     * User login log Table Initialize
     */
});
var selectedModal =0;
$(document).on("change", ".portalSmsEnableDisabled", function() {
    var checked = $(this).prop('checked');
    if(checked){
        $('#assignsms_notfication-4').modal('show');
        $('#smsFormSubmitNotification')[0].reset();
        $('#agency_sms_notification_error').html("");
    }else{
        $.confirm({
            title: 'Are you sure?',
            content: 'you want to disable SMS notifications',
            columnClass: "col-md-6",


            buttons: {
                formSubmit: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function() {
                        saveAgencySMSNotification('disable');
                    }
                },
                cancel: function() {
                    $('.portalSmsEnableDisabled').prop("checked",true)
                },
            },
        });


    }
});
$('#assignsms_notfication-4').on('hidden.bs.modal', function () {

    if(selectedModal ==0){
        $('.portalSmsEnableDisabled').prop('checked',false);
    }
})

function saveAgencySMSNotification(type,patientNotification=[],caregiverNotification=[]){
    var Issms = $('.portalSmsEnableDisabled').prop('checked') == true ? 1 : 0;
    $.ajax({
        type: "post",

        url: _AGENCY_PORTAL_SMS_STATUS,
        data: {
            'is_sms': Issms,
            'agency_id': _AGENCYID,
            'sms_notification_caregiver':caregiverNotification,
            'sms_notification_patient':patientNotification,
            '_token':_CSRF_TOKEN
        },
        success: function(data) {
            toastr.success(data.error_msg);
            if(type =='enabled'){

                selectedModal =1;
                $('.close').click();
            }else{
                selectedModal =0;
            }
            loadPortalSentSMS()
        }
    });
}

$('#smsSaveNotificationId').click(function(){
    var Issms = $(this).prop('checked') == true ? 1 : 0;
    var agencyId = $('#agency_id').val();
    var caregiverNotification = [];
    var patientNotification = [];
    $('.sms_notification_caregiver_checkbox').each(function(i,v){

        if($(this).is(':checked')){
            caregiverNotification.push($(this).val())
        }
    })

    $('.sms_notification_patient_checkbox').each(function(i,v){

        if($(this).is(':checked')){
            patientNotification.push($(this).val())
        }
    })

    if(patientNotification.length ==0 && caregiverNotification.length ==0){
        $('#agency_sms_notification_error').html("Patient or Caregiver SMS notification")
        return false;
    }
    $.confirm({
        title: 'Are you sure?',
        content: 'you want to enabled SMS notifications',
        columnClass: "col-md-6",


        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    saveAgencySMSNotification('enabled',patientNotification,caregiverNotification);
                }
            },
            cancel: function() {
                //close
            },
        },
    });
})

function closeWithoutNotification(){

}

function loadPortalSentSMS(){
    $.ajax({
        url:_AGENCY_LOAD_PORTAL_SMS,
        type: "get",
        data: {
            'id' : _AGENCY_ID,
        },
        success: function(response) {
            var responseData = '';
            if(response.data.length !=0){

                var caregiver ="";
                var patient ="";
                if(response.data.caregiver){
                    caregiver ="<td>"+response.data.caregiver+"</td>";
                }
                if(response.data.patient){
                    patient ="<td>"+response.data.patient+"</td>";
                }
                responseData = '<tr><td>1</td>'+caregiver+patient+'</tr>';
            }else{
                responseData = '<tr><td colspan="3">No record available</td></tr>';
            }
          $('#load_portal_sms_list').html("");
          $('#load_portal_sms_list').html(responseData);
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
}

function getDiscipline(existingId = ""){
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _DISCIPLINE_LIST,
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

function showPatientStatus(){
    var patient_checkbox = $('.patient_checkbox:checked').val();
    $('#patient_status_show').addClass('hide');
    if(patient_checkbox !=undefined){
       $('#patient_status_show').removeClass('hide');
    } else {
        // Reset checkboxes when hiding
        $('.patient_checkbox_status').prop('checked', false);
        $('#patient_status_select_all').prop('checked', false);
    }
}

function showCaregiverStatus(){
    var caregiver_checkbox = $('.caregiver_checkbox:checked').val();
    $('#caregiver_status_show').addClass('hide');
    if(caregiver_checkbox !=undefined){
       $('#caregiver_status_show').removeClass('hide');
    } else {
        // Reset checkboxes when hiding
        $('.caregiver_checkbox_status').prop('checked', false);
        $('#caregiver_status_select_all').prop('checked', false);
    }
}

// Patient Status Select All functionality
$(document).on('change', '#patient_status_select_all', function() {
    $('.patient_checkbox_status').prop('checked', $(this).is(':checked'));
});

$(document).on('change', '.patient_checkbox_status', function() {
    var totalCheckboxes = $('.patient_checkbox_status').length;
    var checkedCheckboxes = $('.patient_checkbox_status:checked').length;
    $('#patient_status_select_all').prop('checked', totalCheckboxes === checkedCheckboxes);
});

// Caregiver Status Select All functionality
$(document).on('change', '#caregiver_status_select_all', function() {
    $('.caregiver_checkbox_status').prop('checked', $(this).is(':checked'));
});

$(document).on('change', '.caregiver_checkbox_status', function() {
    var totalCheckboxes = $('.caregiver_checkbox_status').length;
    var checkedCheckboxes = $('.caregiver_checkbox_status:checked').length;
    $('#caregiver_status_select_all').prop('checked', totalCheckboxes === checkedCheckboxes);
});

function editEmailDocument(){
    $('#document_email_id').val($('#edit_document_send_email_id').val());

}
$('#updateDocumentEmail').click(function(e){
    $.ajax({
        async: false,
        global: false,
        type: "POST",
        url: _UPDATE_DOCUMENT_EMAIL,
        data:{
            'id':_AGENCYID,
            'document_email_notification':$('#document_email_id').val(),
            '_token':_CSRF_TOKEN
        },
        success: function(res) {
            toastr.success(res.error_msg)
            $('#document_send_email_id').html(res.data.email)
            $('#edit_document_send_email_id').val(res.data.email)
            closeEditDocumentEmail()
        },
        error:function(jqr){
            toastr.error('Sorry, something went wrong. Please try again.')
        }
    })
})

function closeEditDocumentEmail(){
    $('#addEditDocumentModal').modal('hide');
}

function editEFaxNo(){
    $('#edit_efaxno_id').val($('#edit_efax_no_id').val());
}

$('#updateEfaxno').click(function(e){
    $.ajax({
        async: false,
        global: false,
        type: "POST",
        url: _UPDATE_EFAX_NO,
        data:{
            'id':_AGENCYID,
            'efax_no':$('#edit_efaxno_id').val(),
            '_token':_CSRF_TOKEN
        },
        success: function(res) {
            toastr.success(res.error_msg)
            $('#efax_no_id').html(res.data.efax_no)
            $('#edit_efaxno_id').val(res.data.efax_no)
            closeEditEfaxNo()
        },
        error:function(jqr){
            toastr.error('Sorry, something went wrong. Please try again.')
        }
    })
})

function closeEditEfaxNo(){
    $('#addEditEfaxModal').modal('hide');
}

$('body').on('click','#cbox_user',function(e){
    var cbox_user = $('#cbox_user').is(":checked");
    if(cbox_user){
        $('.cbox_user_id').prop("checked",true);
    }else{
        $('.cbox_user_id').prop("checked",false);
    }
})


function blockUnblockStatus(){
    var cbox_user_id = $('.cbox_user_id:checked').length;
    var selectedids = [];
    if(cbox_user_id ==0){
        $('.cbox_user_id').addClass('highlightError');
        $('#cbox_user').addClass('highlightError');
        toastr.error('Please select highlighted checkbox');
        return false;
    }else{
        $.confirm({
            title: 'Are you sure?',
            columnClass: "col-md-6",
            type:'blue',
            content:"You want to change the status?",

            buttons: {
                Confirm: {
                    btnClass: 'btn-blue',
                    action: function () {
                        $('.cbox_user_id').each(function(){
                            if($(this).is(":checked")){
                                selectedids.push($(this).val());
                            }
                        })

                        $.ajax({
                            type:"Post",
                            url:_AGENCY_USER_BLOCK_UNBLOCK,
                            data:{
                                'agency_id':_AGENCY_ID,
                                'user_ids':selectedids,
                                '_token':_CSRF_TOKEN
                            },
                            success:function(res){
                               toastr.success(res.error_msg);
                               loadUserList(1);
                            },
                            error:function(jqr){
                                toastr.error('Sorry, something went wrong. Please try again.')
                            }

                        })

                    }
                },
                Cancel: {
                    btnClass: 'btn-secondary',
                    action: function () {

                    }
                }

            }
        });
    }

}

 $(document).on("change", ".paymentReportEnableDisabled", function() {
    var view_payment_report = $(this).prop('checked') == true ? 1 : 0;
    let content = '';
    if(view_payment_report == 0){
        content = 'Would you like to deactivate your access to the payment report?';
    }else{
        content = 'Would you like to activate your access to the payment report?';
    }
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_PAYMENT_REPORT_URL,
                        data: {
                            'view_payment_report': view_payment_report,
                            'agency_id': AGENCY_ID,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                            $('.view_payment_report').val(view_payment_report)
                        }
                    });
                }
            },
            cancel: function() {
                //close
                if(view_payment_report == 0){
                    $('#view_payment_report').prop("checked",true);
                }else{
                    $('#view_payment_report').prop("checked",false);
                }
            },
        },
    });
});

 $(document).on("change", ".enableTaskHealthEnableDisabled", function() {
    var enable_task_health = $(this).prop('checked') == true ? 1 : 0;
    let content = '';
    if(enable_task_health == 0){
        content = 'Would you like to deactivate your access to the task health api?';
    }else{
        content = 'Would you like to activate your access to the task health api?';
    }
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_TASK_HEALTH_API,
                        data: {
                            'enable_task_health': enable_task_health,
                            'agency_id': AGENCY_ID,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                            $('.enable_task_health').val(enable_task_health)
                        }
                    });
                }
            },
            cancel: function() {
                //close
                if(enable_task_health == 0){
                    $('#enable_task_health').prop("checked",true);
                }else{
                    $('#enable_task_health').prop("checked",false);
                }
            },
        },
    });
});

$(document).on("change", ".restrictServiceRequestUpdate", function() {
    var restrict_service_request_update = $(this).prop('checked') == true ? 1 : 0;
    let content = '';
    if(restrict_service_request_update == 0){
        content = 'Would you like to disable restrict service request update?';
    }else{
        content = 'Would you like to enable restrict service request update?';
    }
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_RESTRICT_SERVICE_REQUEST_UPDATE_API,
                        data: {
                            'restrict_service_request_update': restrict_service_request_update,
                            'agency_id': AGENCY_ID,
                            '_token':_CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                        },error: function (data) {
                            toastr.error(data.error_msg);

                        }
                    });
                }
            },
            cancel: function() {
                if(restrict_service_request_update == 0){
                    $('#restrict_service_request_update').prop("checked",true);
                }else{
                    $('#restrict_service_request_update').prop("checked",false);
                }
            },
        },
    });
});

$(document).on("change", ".enableFileManagerEnableDisabled", function() {
    var enable_file_manager = $(this).prop('checked') == true ? 1 : 0;
    var content = enable_file_manager == 0
        ? 'Would you like to disable the File Manager for this agency?'
        : 'Would you like to enable the File Manager for this agency?';
    var $checkbox = $(this);
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_FILE_MANAGER_TOGGLE_API,
                        data: {
                            'enable_file_manager': enable_file_manager,
                            'agency_id': _AGENCY_ID,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                        },
                        error: function() {
                            toastr.error('Something went wrong.');
                            $checkbox.prop('checked', enable_file_manager == 0 ? true : false);
                        }
                    });
                }
            },
            cancel: function() {
                $checkbox.prop('checked', enable_file_manager == 0 ? true : false);
            }
        }
    });
});

$(document).on("change", ".enablePortalEnableDisabled", function() {
    var enable_portal_archive = $(this).prop('checked') == true ? 1 : 0;
    var content = enable_portal_archive == 0
        ? 'Would you like to disable the Portal Archive for this agency?'
        : 'Would you like to enable the Portal Archive for this agency?';
    var $checkbox = $(this);
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_PORTAL_ARCHIVE_TOGGLE_API,
                        data: {
                            'enable_portal_archive': enable_portal_archive,
                            'agency_id': _AGENCY_ID,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                        },
                        error: function() {
                            toastr.error('Something went wrong.');
                            $checkbox.prop('checked', enable_portal_archive == 0 ? true : false);
                        }
                    });
                }
            },
            cancel: function() {
                $checkbox.prop('checked', enable_portal_archive == 0 ? true : false);
            }
        }
    });
});

$(document).on("change", ".enableReviewToggle", function() {
    var enable_review = $(this).prop('checked') == true ? 1 : 0;
    var content = enable_review == 0
        ? 'Would you like to disable the Review functionality for this agency?'
        : 'Would you like to enable the Review functionality for this agency?';
    var $checkbox = $(this);
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_REVIEW_TOGGLE_API,
                        data: {
                            'enable_review': enable_review,
                            'agency_id': _AGENCY_ID,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                        },
                        error: function() {
                            toastr.error('Something went wrong.');
                            $checkbox.prop('checked', enable_review == 0 ? true : false);
                        }
                    });
                }
            },
            cancel: function() {
                $checkbox.prop('checked', enable_review == 0 ? true : false);
            }
        }
    });
});

$(document).on("change", ".isTelehealthSendSmsToggle", function() {
    var currentChecked = $(this).prop('checked');
    var content = currentChecked
        ? 'Would you like to enable telehealth send sms for this agency?'
        : 'Would you like to disable telehealth send sms for this agency?';
    var $checkbox = $(this);
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_TELEHEALTH_SEND_SMS_TOGGLE_API,
                        data: {
                            'agency_id': _AGENCY_ID,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                            $checkbox.prop('checked', data.new_value == 1);
                        },
                        error: function(xhr) {
                            showErrorAndLoginRedirection(xhr);
                            $checkbox.prop('checked', !currentChecked);
                        }
                    });
                }
            },
            cancel: function() {
                $checkbox.prop('checked', !currentChecked);
            }
        }
    });
});

function openEditVisitingPopup() {
    $('#visiting_detail_edit_modal').modal('show');
    $('#app_user_key').val($('#visiting_app_detail').val());
    $('#app_user_password').val($('#visiting_app_name').val());
}

function saveEditVisitingDeatil() {

    var app_user_key = $('#app_user_key').val();
    var app_user_password = $('#app_user_password').val();
    var Issms = $('.enable_visiting').is(':checked') == true ? 1 : 0;
    var agencyId = $('#agency_id').val();
    error = 0;

    if (app_user_key.trim() == "") {
        $('#app_user_key_error').html('Please enter App User Key');
        error++;
    }
    if (app_user_password.trim() == "") {
        $('#app_user_password_error').html('Please enter App User Password');
        error++;
    }
    if (error == 0) {
        var forms = $('#editVistingAppDetail')[0];
        var newForms = new FormData(forms);
        newForms.append('_token',_CSRF_TOKEN);
        newForms.append('enable_visting', Issms);
        newForms.append('agency_id', _AGENCYID);
        $.ajax({
            url: _UPDATE_APP_VISITING_AID_DETAIL,
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);
                location.reload();
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }
}

$('.enable_visiting').change(function(e){
    var visiting_status = $('#visiting_status').val();
    var message = "You want to enable Visiting Aid.";
    if(visiting_status ==1){
        message = "You want to disable Visiting Aid.";
    }

    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        type: 'blue',
        content: message,
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        url: _ENABLED_DISABLED_VISITING_AID,
                        type: "POST",
                        data:{
                            'id':_AGENCY_ID,
                            '_token':_CSRF_TOKEN
                        },

                        success: function(response) {
                            toastr.success(response.error_msg);
                            $('#visiting_status').val(response.data.status);
                            $('.enable_visiting').prop('checked',false)
                            $('#sync_visiting_details').attr('style','display:none');
                            if(response.data.status ==1){
                                $('.enable_visiting').prop('checked',true)
                                $('#sync_visiting_details').attr('style','');
                            }
                           // location.reload();
                        },
                        error: function(xhr, status, error) {
                            toastr.error(xhr.responseJSON.error_msg);
                        }
                    });
                }
            },
            cancel: function() {
                //close
                $('.enable_visiting').prop('checked',false)
                if($('#visiting_status').val() ==1){
                    $('.enable_visiting').prop('checked',true)
                }
            },
        },
    });
});

$(document).on("change", ".reportingToolEnableDisabled", function() {
    var show_reporting_tool = $(this).prop('checked') == true ? 1 : 0;
    let content = show_reporting_tool == 0
        ? 'Would you like to deactivate the Reporting Tool for agency users?'
        : 'Would you like to activate the Reporting Tool for agency users?';
    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_REPORTING_TOOL_URL,
                        data: {
                            'show_reporting_tool': show_reporting_tool,
                            'agency_id': AGENCY_ID,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                        }
                    });
                }
            },
            cancel: function() {
                if (show_reporting_tool == 0) {
                    $('#show_reporting_tool').prop("checked", true);
                } else {
                    $('#show_reporting_tool').prop("checked", false);
                }
            },
        },
    });
});

$(document).on("change", ".aiCallLogsToggle", function() {
    var ai_call_logs_enabled = $(this).prop('checked') == true ? 1 : 0;
    var content = ai_call_logs_enabled == 0
        ? 'Would you like to disable AI Call Logs for this agency?'
        : 'Would you like to enable AI Call Logs for this agency?';

    $.confirm({
        title: 'Are you sure?',
        content: content,
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: AGENCY_AI_CALL_LOGS_TOGGLE_API,
                        data: {
                            'ai_call_logs_enabled': ai_call_logs_enabled,
                            'agency_id': AGENCY_ID,
                            '_token': _CSRF_TOKEN
                        },
                        success: function(data) {
                            toastr.success(data.error_msg);
                        },
                        error: function(xhr, status, error) {
                            showErrorAndLoginRedirection(xhr);
                        }
                    });
                }
            },
            cancel: function() {
                if (ai_call_logs_enabled == 0) {
                    $('#ai_call_logs_enabled').prop("checked", true);
                } else {
                    $('#ai_call_logs_enabled').prop("checked", false);
                }
            },
        },
    });
});