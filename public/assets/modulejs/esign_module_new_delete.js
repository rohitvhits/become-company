function loadTemplateNew() {
    $.ajax({

        url: _LOAD_ESIGN_TEMPLATE,
        type: "GET",
        data: {
            'agency_id': _AGENCYID,
            'type': _RECORD_TYPE
        },
        success: function (res) {
            var json = res.data;
            $('#template_idNew').html("");
            var option = "";
            option = '<option value="">Select Template</option>';
            if (json.length != 0) {

                $.each(json, function (i, v) {
                    option += '<option value="' + v.id + '">' + v.template_name + '</option>';
                })
            }

            $('#template_idNew').html(option);

        }
    })
}

function loadDoctorListNew() {
    $.ajax({
        url: _LOAD_DOCTOR_LIST,
        type: "GET",
        data: '',
        success: function (res) {
            var json = res.data;
            $('#doctor_idNew').html("");
            var option = "";
            option = '<option value="">Select Doctor</option>';
            if (json.length != 0) {

                $.each(json, function (i, v) {
                    option += '<option value="' + v.id + '">' + v.full_name + '</option>';
                })
            }

            $('#doctor_idNew').html(option);

        }
    })
}

$('#edit_template_modal_submit_new').click(function (e) {
    e.preventDefault();
    $('#edit_template_modal_submit_new').hide();

    clearErrorMessages();

    var templateId = $('#template_idNew').val();
    var doctorId = $('#doctor_idNew').val();

    let hasErrors = false;

    if (!templateId) {
        $('#template_idNew_error').html("Please select Template");
        hasErrors = true;
    }

    if (_RECORD_TYPE == 'Patient') {
        if (!doctorId) {
            $('#doctor_idNew_error').html("Please select Doctor");
            hasErrors = true;
        }
    }

    if (hasErrors) {
        $('#edit_template_modal_submit_new').show();
        return false;
    }

    $.ajax({
        async: false,
        global: false,
        url: esignAllocateSigner,
        type: "GET",
        data: {
            'template_id': templateId,
            '_token': _CSRF_TOKEN
        },
        success: function (response) {
            if (response == 1) {
                hasErrors = false;
            } else {
                $('#change_template_error').html("Please select signer.");
                hasErrors = true;
            }
        }
    });

    if (hasErrors) {
        $('#edit_template_modal_submit_new').show();
        return false;
    }

    var formData = new FormData($('#edit_template_modal_new')[0]);
    formData.append('_token', _CSRF_TOKEN);

    $.ajax({
        url: saveEsignTemplateNew,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            toastr.success(response.error_msg);
            esignResponseNew1();
            $('#edit_template_modal_new')[0].reset();
            $('#closed_id_esign_new').click();
        },
        error: function (xhr) {
            toastr.error(xhr.responseJSON.error_msg);
        },
        complete: function () {
            $('#edit_template_modal_submit_new').show();
        }
    });
});

function clearErrorMessages() {
    $('#template_idNew_error').html("");
    $('#doctor_idNew_error').html("");
    $('#change_template_error').html("");
}

$('#exampleModal-esign-new').on('hidden.bs.modal', function () {
    $('#edit_template_modal_new')[0].reset();
    clearErrorMessages();
});

function esignResponseNew1() {
    $.ajax({
        async: false,
        global: false,
        url: _PATIENT_WISE_ESIGN_LIST,
        type: "get",
        data: {
            'patient_id': _RECORD_ID,
            'type': _RECORD_TYPE

        },
        success: function (response) {
            var json = response.data.data;

            var tableResponse = "";
            var cnt = 1;

            if (json.length != 0) {
                $.each(json, function (i, v) {
                    var moveToDocument = '';
                    var pdf_status = '';
                    var completed_on = '';

                    if (v.signerRemaining == 0) {
                        var completed_on = `<span class="">Completed on <br>${v.completed_on}</span>`;

                        if (v.pdf_status == '0') {
                            var revert = '';
                            if (esignRevert) {
                                var revert = `<i class="fa fa-undo undoData" data-id="${v.id}" aria-hidden="true" title="Revert"></i>`;
                            }
                            pdf_status = `<label class="badge badge-outline-danger" style="color:#ff0000;" data-toggle="popover${v.id}" data-pid="${v.id}" data-content="${v.pdf_status_reason}" data-original-title="Rejected" onclick="showData(${v.id})">Rejected</label><br>${revert}`;
                        } else if (v.pdf_status == '1') {
                            var revert = '';
                            if (esignRevert) {
                                var revert = `<i class="fa fa-undo undoData" data-id="${v.id}" aria-hidden="true" title="Revert"></i>`;
                            }
                            pdf_status = `<label class="badge badge-outline-success" style="color:#28a745;">Approved</label><br>${revert}`;
                        }

                        if (v.pdf_status == null) {
                            var status = '<label class="badge badge-outline-success" style="color:#3bb001;">Completed</label>';
                        } else {
                            var status = pdf_status;
                        }

                        if (v.pdf_status == '1' && v.templete_id != 0) {
                            if (esignMoveDocument) {
                                var moveToDocument = '';
                            }
                        } else if (v.templete_id == 0) {
                            if (esignMoveDocument) {
                                var moveToDocument = '';
                            }
                        }
                    } else {
                        if (v.pdf_status == '0') {
                            var revert = '';
                            if (esignRevert) {
                                var revert = `<i class="fa fa-undo undoData" data-id="${v.id}" aria-hidden="true" title="Revert"></i>`;
                            }
                            pdf_status = `<label class="badge badge-outline-danger" style="color:#ff0000;" data-toggle="popover${v.id}" data-pid="${v.id}" data-content="${v.pdf_status_reason}" data-original-title="Rejected" onclick="showData(${v.id})">Rejected</label><br>${revert}`;
                        } else if (v.pdf_status == '1') {
                            var revert = '';
                            if (esignRevert) {
                                var revert = `<i class="fa fa-undo undoData" data-id="${v.id}" aria-hidden="true" title="Revert"></i>`;
                            }
                            pdf_status = `<label class="badge badge-outline-success" style="color:#28a745;">Approved</label><br>${revert}`;
                        }
                        if (v.pdf_status == null) {
                            var status = '<label class="badge badge-outline-warning" style="color:#d76718;">Pending</label>';
                        } else {
                            var status = pdf_status;
                        }

                    }

                    var groupId = "'" + v.groupId + "'";
                    var sendSMSOption = '';

                    if (v.pdf_generate == '' || v.pdf_generate == null) {

                    }


                    var viewSigner = '';

                    if (v.signerRemaining != 0 && v.templete_id != 0) {

                        if (esignView) {
                            viewSigner = '';
                        }
                        if (esignSendSms) {
                            sendSMSOption = '';
                        }
                    }
                    var deleteOption = '';
                    if (v.status == 'Pending') {
                        if (esignDelete) {
                            var deleteOption = '';
                        }
                    }
                    var viewLog = "";
                    if (esignViewLog) {
                        var viewLog = '';
                    }
                    var showDropdown = '';
                    var showPdfOption = '';
                    var previewOption = '-';

                    if (v.signerRemaining == 0) {
                        if (v.templete_id != 0) {
                            var previewUrl = _BASE_URL + '/esign/preview-pdf-response?id=' + v.id + '&group_id=' + v.groupId;

                            if (v.pdf_status == null) {
                                var previewOption = ``;
                            } else {
                                var previewOption =
                                    (v.review_details?.first_name ?? '') + ' ' +
                                    (v.review_details?.last_name ?? '') + '<br>' +
                                    (v.review_date ?? '');
                            }
                        } else {
                            var previewOption = '-';
                        }

                    }

                    if (v.pdf_generate != "" && v.pdf_generate != null && v.templete_id != 0) {
                        var url = _BASE_URL + '/dre/' + v.groupId;
                        if (esignPdfDownload) {
                            showPdfOption = ``;
                        }
                    }
                    var editPdf = '';
                    if (v.status == 'Pending' && v.templete_id == 0) {
                        var docUrl = _BASE_URL + '/esign/write-document/' + '?id=' + v.id;
                        if (esignView) {
                            var editPdf = ``;
                        }
                    }

                    var downloadWriteDocument = '';
                    if (v.status == 'Completed' && v.templete_id == 0) {
                        var downloadWriteDocumentUrl = _BASE_URL + '/dre-write-document/' + v.id;
                        if (esignPdfDownload) {
                            var downloadWriteDocument = ``;
                        }
                    }

                    var countOfSigner = "";

                    if (v.templete_id == 0) {
                        var countOfSigner = `<b>-</b>`;
                    } else {
                        var countOfSigner = `<b>${v.completedCount}</b>/<b>${v.sentOnCount}</b>`;
                    }

                    // var action = deleteOption + ' ' + showPdfOption + ' ' + viewSigner + ' ' + sendSMSOption + ' ' + '<a href="javascript:void(0)" onclick="esignHistory(' + v.id + ',' + v.main_intakeId + ')"><i class="fa fa-info" aria-hidden="true"></i></a>' + moveToDocument;

                    var actionButton = ``;

                    var action = actionButton;
                    var review = '-';
                    if (esignReview) {
                        var review = previewOption;
                    }

                    tableResponse += `
                    <tr>
                    <td>${cnt++}</td>
                    <td>
                        ${v.template_details && v.template_details.template_name
                            ? v.template_details.template_name
                            : (v.write_document_details && v.write_document_details.document_name
                                ? v.write_document_details.document_name
                                : '-')}
                    </td>
                    <td>${status}<br>${completed_on}</td>
                    <td>${v.user_details.first_name + ' ' + v.user_details.last_name}</td>
                    <td>${review}</td>
                    <td>${countOfSigner}</td>
                    <td>${v.user_details.first_name + ' ' + v.user_details.last_name}<br>${v.created_date}</td>
                    <td style="overflow: unset !important">${action}</td>
                    </tr>`
                })
            } else {
                tableResponse = '<tr><td colspan="8">No record available</td></tr>'
            }

            $('#esign_resp_id_new').html("")
            $('#esign_resp_id_new').html(tableResponse)
        }

    })
}

function getSignerNew(gid, document_id, enrollId) {
    $('#loaderdocument').attr('style', 'display:block');

    $.ajax({
        async: false,
        global: false,
        url: _GET_ALLOCATED_SIGNER + "?groupId=" + gid + "&document_id=" +
            document_id + "&enrollment_id=" + enrollId,
        success: function (response) {
            var json = response.data;
            console.log(json, 'json');

            var responseHtml = "";
            var sentOnCount = {};
            var completedCount = 0;
            if (json.length != 0) {
                $.each(json, function (i, v) {
                    var name = "";
                    if (v.sent_on.toLowerCase() == 'caregiver') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'staff') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'officestaff') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'other') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'stampuser') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'patient') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'other') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'formfill') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'sign') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase() == 'stamp') {
                        name = v.sent_on;
                    }
                    v.sent_on = name
                    sentOnCount[name] = (sentOnCount[name] || 0) + 1;

                    if (v.status != "Completed") {
                        var status = '<label class="badge badge-outline-warning">Pending</label>';
                        var groupId = "'" + v.groupId + "'";
                        var single = "'single'";
                        var link = _BASE_URL + '/esign/docusign/viewNew/' + v.id + '?mobile_type=web';
                        var spans = '<span class="col-md-3"></span>';
                        responseHtml += '<li><span class="col-md-3" style="margin-right:30px;font-size:20px">' + v.sent_on + '</span>' + status + " " + spans + '</li>'
                    } else {
                        completedCount++;
                        var status = '<label class="badge badge-outline-success">Completed<label>';
                        var spans = "";
                        responseHtml += '<li><span class="col-md-3" style="margin-right:30px;font-size:20px">' + v.sent_on + '</span>' + status + " " + spans + '</li>'
                    }
                })
            }

            var totalSentOnCount = Object.values(sentOnCount).reduce((total, count) => total + count, 0);

            $('#sendRequestNew').modal('show');

            var htmlResponse = '<input type="hidden" id="enrollment_idNew" value="' + enrollId + '"><input type="hidden" id="groupIdNew" value="' + gid + '"><input type="hidden" id="document_idNew" value="' + document_id + '"><div class="box-body no-padding"><ul class="nav nav-pills nav-stacked" style="border-bottom:unset;">' + responseHtml + '</ul></div>';
            $('#snedIdNew').html("");
            $('#snedIdNew').html(htmlResponse);
            $('#totalSigner').html(totalSentOnCount);
            $('#completedSigner').html(completedCount);
            $('#loaderdocument').attr('style', 'display:none');
        }
    })
}

function getDeleteEsignTemplate(docId) {
    if (docId != '') {
        $.confirm({
            title: 'Are you sure?',
            content: 'you want to delete this record.',
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function () {
                        $.ajax({
                            global: false,
                            url: _DELETE_ESIGN_TEMPLATE + '/' + docId,
                            type: "GET",
                            success: function (response) {
                                toastr.success(response.error_msg);
                                esignResponseNew1(1);
                            },
                            error: function (xhr, status, error) {
                                toastr.error(xhr.responseJSON.error_msg);
                            }
                        });
                    }
                },
                cancel: function () {

                }
            }
        })

    }
    return false;
}

function getSendSMSSubmitNew() {
    var mobile_no_id_caregiver = $('#mobile_no_id_caregiver_new').val();
    var email = $('#email_new').val();
    $('#mobile_no_id_caregiver_new_error').html("");
    var cnt = 0;
    if (mobile_no_id_caregiver.trim() == '' && email.trim() == '') {
        $('#mobile_no_id_caregiver_new_error').html("Please enter Email or Mobile");
        cnt = 1;
    }
    if (cnt == 0) {
        var cons = confirm("Are you sure you want to send sms?");


        if (cons == true) {
            var foms = $('#sms_esign_new')[0];
            var formData = new FormData(foms);
            formData.append("_token", _CSRF_TOKEN);
            formData.append("hhaCaregiverId", userNewId);
            $.ajax({
                async: false,
                global: false,
                url: _SMS_EMAIL_ESIGN_TEMPLATE,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res == 1) {
                        toastr.success('Email successfully sent.');
                    } else {
                        toastr.error('Sorry, something went wrong. Please try again.');
                    }
                    $('#sendSMSEsignNew').modal('hide');

                }
            })
        }
    } else {
        $('#sendSMSEsignNew').modal('show');

        return false;
    }


}

function showMailDocument(id) {
    $('#document_upload_id').val(id);
}
function sendDocumentHHAMail() {
    var document_email = $('#document_email').val();
    var emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    $('#document_email_error').html("");
    var cnt = 0;
    if (document_email.trim() == '') {
        $('#document_email_error').html("Please enter Email");
        cnt = 1;
    }

    if (document_email.trim() != '') {
        if (!document_email.match(emailRegex)) {
            $('#document_email_error').html("Invalid email");
            cnt = 1;
        }
    }
    if (cnt == 0) {
        $.ajax({
            async: false,
            global: false,
            url: _SEND_DOCUMENT_MAIL,
            type: "POST",
            data: {
                document_id: $('#document_upload_id').val(),
                patient_id: _RECORD_ID,
                email: document_email,
                '_token': _CSRF_TOKEN
            },

            success: function (res) {
                toastr.success(res.error_msg);
                $('#document_email').val("");
                $('#exampleModal-send-mail').modal('hide')

            },
            error: function (jqr) {
                toastr.error(jqr.responseJSON.error_msg);
            }
        })

    } else {
        return false;
    }
}

function getSendSMSNew1(id, type = "") {
    $('#main_caregiver_esign_id_new').val(id);
    $('#document_send_type_id_new').val(type);
    $('#sendSMSEsignNew').modal('show');
    $('#sendSMSEsignNew').css({
        zIndex: '99999'
    })
}

function resfreshSignerDataNew() {
    var groupId = $('#groupIdNew').val();
    var document_id = $('#document_idNew').val();
    var enrollId = $('#enrollment_idNew').val();
    getSignerNew(groupId, document_id, enrollId);
}

$(document).on('click', '.undoData', function () {
    const documentId = $(this).data('id');

    $.ajax({
        url: _UNDO_STATUS_URL,
        type: 'POST',
        data: {
            document_id: documentId,
            _token: _CSRF_TOKEN,
        },
        success: function (response) {
            esignResponseNew1();
        },
        error: function (xhr, status, error) {
            toastr.error(xhr.responseJSON.error_msg);
        }
    });
});

$(document).on('click', '.viewLog', function () {
    var documentId = $(this).data('document-id');
    var templateId = $(this).data('template-id');

    $.ajax({
        url: _LOG_URL,
        method: 'GET',
        data: { document_id: documentId, template_id: templateId },
        success: function (response) {
            var logsHtml = '';
            if (response.logs && response.logs.length > 0) {
                $.each(response.logs, function (index, logData) {
                    console.log(logData, 'logdata');

                    var status = '';
                    if (logData.is_status == 'Approved') {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Approved</label>';
                    } else if (logData.is_status == 'Rejected') {
                        status = `<label class="badge badge-outline-danger" style="color:#ff0000;">Rejected</label><i class="fa fa-exclamation-triangle ml-2" style="cursor:pointer;color:#d76718;" data-toggle="popover${index}" data-pid="${index}" data-content="${logData.pdf_status_reason}"  data-original-title="Rejected" onclick="showData(${index})"></i>`;
                    } else if (logData.is_status == 'Completed') {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Completed</label>';
                    } else if (logData.is_status == 'Revert') {
                        status = '<label class="badge badge-outline-warning" style="color:#d76718;">Revert</label>';
                    } else if (logData.is_status == 'Added') {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Added</label>';
                    } else if (logData.is_status == 'Download') {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Download</label>';
                    } else if (logData.is_status == 'Move To Document') {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Move To Document</label>';
                    } else if (logData.is_status == 'formFill') {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Completed</label>';
                    } else if (logData.is_status == 'sign') {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Completed</label>';
                    } else if (logData.is_status == 'stamp') {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Completed</label>';
                    } else {
                        status = '<label class="badge badge-outline-success" style="color:#28a745;">Completed</label>';
                    }


                    var statusText = '';
                    var actionBy = '';
                    var reviewBy = '';
                    if (logData.is_status == 'Approved') {
                        statusText = '<b>Approval</b>';
                        reviewBy = `${logData.review_by_name}`;
                        actionBy = `${logData.review_by_name ?? ''}<br>${logData.review_date ?? ''}`;
                    } else if (logData.is_status == 'Rejected') {
                        statusText = '<b>Rejected</b>';
                        reviewBy = `${logData.review_by_name}`;
                        actionBy = `${logData.review_by_name ?? ''}<br>${logData.review_date ?? ''}`;
                    } else if (logData.is_status == 'Completed') {
                        statusText = '<b>Completed</b>';
                        reviewBy = `${logData.write_completed_by_name}`;
                        actionBy = `${logData.write_completed_by_name ?? ''}<br>${logData.completed_on ?? ''}`;
                    } else if (logData.is_status == 'Revert') {
                        statusText = '<b>Revert</b>';
                        reviewBy = `${logData.undo_by_name}`;
                        actionBy = `${logData.undo_by_name ?? ''}<br>${logData.is_undo_date ?? ''}`;
                    } else if (logData.is_status == 'Added') {
                        statusText = '<b>Added</b>';
                        if (logData.templete_id == 0) {
                            reviewBy = `${logData.write_added_by_name}`;
                            actionBy = `${logData.write_added_by_name ?? ''}<br>${logData.created_date ?? ''}`;
                        } else {
                            reviewBy = `${logData.added_by_name}`;
                            actionBy = `${logData.added_by_name ?? ''}<br>${logData.created_date ?? ''}`;
                        }
                    } else if (logData.is_status == 'Download' || logData.is_status == 'Send SMS - Email') {
                        if(logData.is_status =='Send SMS - Email'){
                            statusText = '<b>Send SMS / Email</b>';
                        }else{
                            statusText = '<b>Download</b>';
                        }
                        
                        reviewBy = `${logData.download_by_name}`;
                        actionBy = `${logData.download_by_name ?? ''}<br>${logData.download_date ?? ''}`;
                    } else if (logData.is_status == 'Move To Document') {
                        statusText = '<b>Move To Document</b>';
                        reviewBy = `${logData.move_to_esign_by_name}`;
                        actionBy = `${logData.move_to_esign_by_name ?? ''}<br>${logData.move_to_esign_date ?? ''}`;
                    } else if (logData.is_status == 'formFill') {
                        statusText = '<b>Form Fill</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    } else if (logData.is_status == 'sign') {
                        statusText = '<b>Sign</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    } else if (logData.is_status == 'stamp') {
                        statusText = '<b>Stamp</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    } else if (logData.is_status == 'other') {
                        statusText = '<b>Other</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    } else if (logData.is_status == 'caregiver') {
                        statusText = '<b>Caregiver</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    } else if (logData.is_status == 'stampUser') {
                        statusText = '<b>Stamp User</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    } else if (logData.is_status == 'patient') {
                        statusText = '<b>Patient</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    } else if (logData.is_status == 'OfficeStaff') {
                        statusText = '<b>Office Staff</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    }else {
                        statusText = '<b>Completed</b>';
                        if (logData.completed_by_name) {
                            reviewBy = `${logData.completed_by_name}`;
                            actionBy = `${logData.completed_by_name}<br>${logData.completed_on ?? ''}`;
                        } else {
                            reviewBy = logData.email && logData.sms
                                ? `${logData.email} | +1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy = logData.email && logData.sms
                                ? `${logData.email}<br>+1${logData.sms}`
                                : logData.email
                                    ? logData.email
                                    : logData.sms
                                        ? `+1${logData.sms}`
                                        : '';

                            actionBy += logData.completed_on ? `<br>${logData.completed_on}` : '';

                        }
                    }


                    logsHtml += `
                        <tr>
                            <td>${statusText ?? ''}<br>${reviewBy}</td>
                            <td>${status ?? ''}</td>
                            <td>${actionBy}</td>
                        </tr>
                    `;
                });
            } else {
                logsHtml = `
                    <tr>
                        <td colspan="4" class="text-center">No logs found</td>
                    </tr>
                `;
            }
            $('#tempName').html(response.template_name);
            $('#logTable tbody').html(logsHtml);
            $('#logModal').modal('show');
        },
        error: function () {
            alert('An error occurred while fetching logs. Please try again.');
        }
    });
});

$('#edit_upload_document_modal_submit_new').click(function (e) {
    $('#edit_upload_document_modal_submit_new').hide();
    var document_name = $("#documentName").val();
    var file_upload = $("#fileUpload").val();

    $("#documentName_error").html("");
    $("#fileUpload_error").html("");

    cnt = 0;
    if (document_name.trim() == "") {
        $("#documentName_error").html("Please enter Document Name");
        cnt = 1;
    }

    if (file_upload.trim() == "") {
        $("#fileUpload_error").html("Please select File");
        cnt = 1;
    } else {
        var fileExtensionType = ["pdf", "csv", "xlsx", "xls", "docx", "doc"];
        var files = $('#fileUpload')[0].files;
        var fileName = files[0].name;
        var fileType = fileName.substr(fileName.lastIndexOf(".") + 1);
        $("#fileUpload_error").html("");
        if (
            $.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) ==
            -1
        ) {
            $("#fileUpload_error").html("Please select only pdf or csv file");
            cnt = 1;
        }
    }

    if (cnt == 1) {
        $('#edit_upload_document_modal_submit_new').show();
        return false;
    } else {
        e.preventDefault();

        var forms = $('#edit_upload_document_modal_new')[0];
        var newData = new FormData(forms);
        newData.append('_token', _CSRF_TOKEN);
        $.ajax({

            url: saveUploadDocument,
            type: "POST",
            data: newData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.error_msg);
                esignResponseNew1();
                $('#edit_upload_document_modal_new')[0].reset();
                $('#closed_id_upload_document_new').click();

                $('#edit_upload_document_modal_submit_new').show();

            },
            error: function (xhr, status, error) {
                $('#edit_upload_document_modal_submit_new').show();
                showErrorAndLoginRedirection(xhr)
            }
        });
    }
})

function showData(pid) {
    $('[data-toggle="popover' + pid + '"]').popover();

    $('.popover').css('z-index', '100000');
}
