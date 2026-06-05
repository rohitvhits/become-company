function loadTemplateNew() {
    // Get template type - from radio button (All access) or hidden input (restricted access)
    var $radio = $('input[name="template_type"]:checked');
    var templateType = $radio.length ? $radio.val() : ($('input[name="template_type"][type="hidden"]').val() || '');

    $.ajax({
        url: _LOAD_ESIGN_TEMPLATE,
        type: "GET",
        data: {
            'agency_id': _AGENCYID,
            'type': _RECORD_TYPE,
            'template_type': templateType
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

// Reload templates when caregiver changes template type radio button
$(document).on('change', 'input[name="template_type"]', function () {
    loadTemplateNew();
});

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
            showErrorAndLoginRedirection(xhr);
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

function esignResponseNew1(page=1) {
    $('.esign-shimmer').attr('style','');
    // $('#esignSectionLoader').attr('style','');
    $("#esign_resp_id_new").html("");
    // $("#paginate_id").html("");
    // $("#esign_resp_id_new").html('<tr><td colspan="7">Loading...</td></tr>');
    $.ajax({
        url: _PATIENT_WISE_ESIGN_LIST,
        type: "get",
        data: {
            'patient_id': _RECORD_ID,
            'type': _RECORD_TYPE,
            'page': page 

        },
        success: function (response) {
            $('.esign-shimmer').attr('style','display:none');
            $('#esign_resp_id_new').html(response);

        }

    })
}

function getSignerNew(gid, document_id, enrollId) {
    $('#loaderdocument').attr('style', 'display:block');

    // Show shimmer effect
    $('.shimmer-row').show();
    $('#signerTableBody tr:not(.shimmer-row)').remove();

    $.ajax({
        async: false,
        global: false,
        url: _GET_ALLOCATED_SIGNER + "?groupId=" + gid + "&document_id=" +
            document_id + "&enrollment_id=" + enrollId,
        success: function (response) {
            var json = response.data;
            var responseHtml = "";
            var sentOnCount = {};
            var completedCount = 0;
            if (json.length != 0) {
                var lastCompleted = json
                .filter(r => r.status.toLowerCase() === 'completed')
                .slice(-1)[0];
                
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
                    if (v.sent_on.toLowerCase() == 'signstamp') {
                        name = v.sent_on;
                    }
                    v.sent_on = name
                    sentOnCount[name] = (sentOnCount[name] || 0) + 1;
                    var groupId = "'" + v.groupId + "'";
                    if (v.status != "Completed") {
                        var status = '<label class="badge badge-outline-warning" style="color:#d76718;">Pending</label>';
                        
                        var single = "'single'";
                        var link = _BASE_URL + '/esign/docusign/viewNew/' + v.id + '?mobile_type=web';
                        var actions = '<div class="action-icons"><a target="_blank" href="' + link + '" title="Signature"><svg width="18" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M18.7929 1.29289C19.1834 0.902369 19.8166 0.902369 20.2071 1.29289L22.7071 3.79289C23.0976 4.18342 23.0976 4.81658 22.7071 5.20711L15.2071 12.7071C15.0196 12.8946 14.7652 13 14.5 13H12C11.4477 13 11 12.5523 11 12V9.5C11 9.23478 11.1054 8.98043 11.2929 8.79289L18.7929 1.29289ZM13 9.91421V11H14.0858L20.5858 4.5L19.5 3.41421L13 9.91421ZM1 14.5C1 12.567 2.567 11 4.5 11H8C8.55228 11 9 11.4477 9 12C9 12.5523 8.55228 13 8 13H4.5C3.67158 13 3 13.6716 3 14.5C3 15.3284 3.67158 16 4.5 16H19.5C21.433 16 23 17.567 23 19.5C23 21.433 21.433 23 19.5 23H9C8.44772 23 8 22.5523 8 22C8 21.4477 8.44772 21 9 21H19.5C20.3284 21 21 20.3284 21 19.5C21 18.6716 20.3284 18 19.5 18H4.5C2.567 18 1 16.433 1 14.5Z" fill="#333333"></path></svg></a><a href="javascript:void(0)" onclick="getSendSMSNew1(' + v.id + ',' + single + ')" title="Send Email"><svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18.3333 2.91663C18.4571 2.91912 18.5746 2.94176 18.6919 2.99756C18.8129 3.05543 18.9194 3.14262 18.9999 3.24996C19.0527 3.32862 19.0404 3.30502 18.9999 3.24996C19.1079 3.39388 19.1666 3.5701 19.1666 3.74996C19.1663 3.70557 19.1644 3.68631 19.1666 3.74996V16.25C19.1666 16.7102 18.7935 17.0833 18.3333 17.0833H1.66659C1.20635 17.0833 0.833252 16.7102 0.833252 16.25V3.74996C0.833252 3.60077 0.873724 3.45263 0.949855 3.32468C1.105 3.06328 1.37371 2.92253 1.66659 2.91663H18.3333ZM17.4999 5.41663L10.4999 10.6666C10.2333 10.8666 9.87658 10.8866 9.59192 10.7266L9.49992 10.6666L2.49992 5.41663V15.4166H17.4999V5.41663ZM4.16658 4.58329L9.99992 8.95829L15.8333 4.58329H4.16658Z" fill="black" fill-opacity="0.65"></path></svg></a><a href="javascript:void(0)" onclick="getQRcode(\'' + v.id + '\',\'' + v.groupId + '\' )" title="QR Code"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="17" fill="black" viewBox="0 0 16 16" style="cursor: pointer;opacity:0.85"><path d="M2 2h2v2H2V2Z"/><path d="M6 0H0v6h6V0ZM5 5H1V1h4v4Z"/><path d="M10 2h2v2h-2V2Z"/><path d="M14 0h-6v6h6V0ZM13 5H9V1h4v4Z"/><path d="M2 10h2v2H2v-2Z"/><path d="M6 8H0v6h6V8ZM5 13H1V9h4v4Z"/><path d="M7 7h1v1H7V7Z"/><path d="M8 8h1v1H8V8Z"/><path d="M7 9h1v1H7V9Z"/><path d="M10 7h1v1h-1V7Z"/><path d="M11 8h1v1h-1V8Z"/><path d="M10 9h1v1h-1V9Z"/><path d="M12 9h1v1h-1V9Z"/><path d="M13 10h1v1h-1v-1Z"/></svg></a></div>';
                        responseHtml += `
                            <tr>
                                <td><strong>${v.sent_on}</strong></td>
                                <td>${status}</td>
                                <td>${actions}</td>
                            </tr>
                            `;
                    } else {
                        completedCount++;
                        let editOptionEsign ='';
                        if(lastCompleted.id ==v.id){
                            var editLink = _BASE_URL + '/esign/edit-sign/' + v.id + '?groupId='+v.groupId;
                            editOptionEsign = `<a target="_blank" href="${editLink}" title="Edit E-Sign"><svg width="18" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg></a>`;
                        }
                   
                        var status = '<label class="badge badge-outline-success">Completed</label>';
                        var actions = `<div class="action-icons"><a title="Undo" href="javascript:void(0)" onclick="undoData(${v.id})"><svg width="18" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12 5V1L5 8l7 7V9c3.86 0 7 3.14 7 7 0 1.1-.9 2-2 2h-1v2h1c2.21 0 4-1.79 4-4 0-4.42-3.58-8-8-8z" fill="#333333"/> </svg></a>${editOptionEsign}</div>`;
                        responseHtml += `
                            <tr>
                                <td><strong>${v.sent_on}</strong></td>
                                <td>${status}</td>
                                <td>${actions}</td>
                            </tr>
                            `;
                    }
                })
            }

            var totalSentOnCount = Object.values(sentOnCount).reduce((total, count) => total + count, 0);

            $('#sendRequestNew').modal('show');

            // Hide shimmer and show data
            $('.shimmer-row').hide();

            var hiddenInputs = '<input type="hidden" id="enrollment_idNew" value="' + enrollId + '"><input type="hidden" id="groupIdNew" value="' + gid + '"><input type="hidden" id="document_idNew" value="' + document_id + '">';

            // Clear existing non-shimmer rows and append new data
            $('#signerTableBody tr:not(.shimmer-row)').remove();
            $('#signerTableBody').append(responseHtml);
            $('#snedIdNew').prepend(hiddenInputs);
            
            $('#totalSigner').html(totalSentOnCount);
            $('#completedSigner').html(completedCount);
            $('#loaderdocument').attr('style', 'display:none');
        },
        error: function (jqXHR) {
            showErrorAndLoginRedirection(jqXHR)
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
                                showErrorAndLoginRedirection(xhr);
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
    var sendType = $('.sendType:checked').val(); 
    $('#mobile_no_id_caregiver_new_error').html("");
    $('#email_new_error').html("");
    var cnt = 0;
    var cnt = 0;

    if($('input[name="sendType[]"]:checked').length ==0){
        $('#mobile_no_id_caregiver_new_error').html("Please select Email or Mobile");
        cnt = 1;

    }
    $('input[name="sendType[]"]').each(function(){
        

        if($(this).is(":checked")){
            if($(this).val() =='email'){
                if(email.trim() == ''){
                    $('#email_new_error').html("Please enter Email");
                    cnt = 1;
                }
            }
            if($(this).val() =='mobile'){
                if(mobile_no_id_caregiver.trim() == ''){
                    $('#mobile_no_id_caregiver_new_error').html("Please enter Mobile");
                    cnt = 1;
                }
            }
        }
    });

    if (cnt == 0) {
        var foms = $('#sms_esign_new')[0];
        var formData = new FormData(foms);
        formData.append("_token", _CSRF_TOKEN);
        formData.append("hhaCaregiverId", userNewId);

        $.confirm({
            title: 'Are you sure?',
            content: 'you want to send sms.',
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function () {
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
                                    toastr.success('Successfully sent.');
                                } else {
                                    toastr.error('Sorry, something went wrong. Please try again.');
                                }
                                $('#sendSMSEsignNew').modal('hide');
            
                            }
                        })
                    }
                },
                cancel: function () {

                }
            }
        })
       
    } else {
        $('#sendSMSEsignNew').modal('show');

        return false;
    }
}

$('.sendType').change(function() {
    $('#mobile_no_id_caregiver_new_error').html("");
    $('#email_new_error').html("");
});

function showMailDocument(id) {
    $('#document_upload_id').val(id);
    $('#send_back_to_agency_mail').prop('checked', false);
}
function sendDocumentHHAMail() {
    var document_email = $('#document_email').val();
  //  var emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    $('#document_email_error').html("");
    var cnt = 0;
    if (document_email.trim() == '') {
        $('#document_email_error').html("Please enter Email");
        cnt = 1;
    }

    // if (document_email.trim() != '') {
    //     if (!document_email.match(emailRegex)) {
    //         $('#document_email_error').html("Invalid email");
    //         cnt = 1;
    //     }
    // }
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
                send_back_to_agency: $('#send_back_to_agency_mail').is(':checked') ? 1 : 0,
                '_token': _CSRF_TOKEN
            },

            success: function (res) {
                toastr.success(res.error_msg);
                // $('#document_email').val("");
                $('#exampleModal-send-mail').modal('hide');
                if (typeof loadDocumentAjaxList === 'function') {
                    loadDocumentAjaxList();
                }
            },
            error: function (jqr) {
                showErrorAndLoginRedirection(jqr);
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
    const esignType  = $(this).data('esign-type');
    $.confirm({
        title: 'Undo',
        columnClass: "col-md-6",
        content: 'Are you sure you want to undo this record?',
        btnClass:"btn-blue",
        buttons: {
            confirm: {
                text: 'Undo',
                btnClass: 'btn-danger',
                action: function () {
                    $.ajax({
                        url: _UNDO_STATUS_URL,
                        type: 'POST',
                        data: {
                            document_id: documentId,
                            _token: _CSRF_TOKEN,
                            esignType:esignType
                        },
                        success: function (response) {
                            toastr.success(response.error_msg)
                            esignResponseNew1();
                        },
                        error: function (xhr, status, error) {
                            showErrorAndLoginRedirection(xhr);
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                btnClass: 'btn-secondary',
                action: function () {
                    // Do nothing
                }
            }
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
                    var actionTypeResponse = "";
                   
                    if(logData.esign_old_response.length !=0){
                        actionTypeResponse = "Edit";
                    }
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
                        reviewBy = `${logData.added_by_name}`;
                        actionBy = `${logData.added_by_name ?? ''}<br>${logData.created_date ?? ''}`;
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
                        reviewBy = `${logData.added_by_name}`;
                        actionBy = `${logData.added_by_name ?? ''}<br>${logData.created_date ?? ''}`;
                    } else if (logData.is_status == 'formFill') {
                        statusText =' <b> '+ actionTypeResponse +' Form Fill</b>';
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
                        statusText =' <b> '+ actionTypeResponse +' Sign</b>';
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
                        statusText =' <b> '+ actionTypeResponse +' Stamp</b>';
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
                        statusText =' <b> '+ actionTypeResponse +' Other</b>';
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
                        statusText =' <b> '+ actionTypeResponse +' Caregiver</b>';
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
                        statusText =' <b> '+ actionTypeResponse +' Stamp User</b>';
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
                        statusText =' <b> '+ actionTypeResponse +' Patient</b>';
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
                        statusText =' <b> '+ actionTypeResponse +' Office Staff</b>';
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
                    }else if (logData.is_status == 'Form Edit') {
                        statusText =' <b> '+ actionTypeResponse +' Form Edit</b>';
                        reviewBy = `${logData.completed_by_name}`;
                        actionBy = `${logData.completed_by_name ?? ''}<br>${logData.completed_date ?? ''}`;
                    } else if (logData.is_status == 'signStamp') {
                        statusText =' <b> '+ actionTypeResponse +' Sign & Stamp</b>';
                        reviewBy = `${logData.completed_by_name}`;
                        actionBy = `${logData.completed_by_name ?? ''}<br>${logData.completed_date ?? ''}`;
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

                    var commonLogViewResponse ="";
                    var esignLogViewResponse ="";
                    if(logData.new_response !=null){
                        commonLogViewResponse ='<a title="Common View Log" onclick="commonViewLog('+logData.id+')"><i class="fa fa-eye"></i></a>'
                    }

                    if(logData.esign_new_response !=null){
                        esignLogViewResponse ='<a title="Common Esign Log" onclick="commonEsignViewLog('+logData.id+')"><i class="fa fa-signal"></i></a>'
                    }

                    logsHtml += `
                        <tr>
                            <td>${statusText ?? ''}<br>${reviewBy}</td>
                            <td>${status ?? ''}</td>
                            <td>${logData.message}</td>
                          
                            <td>${actionBy}</td>
                            <td>
                                ${commonLogViewResponse}
                                ${esignLogViewResponse}
                            </td>
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
        error: function (jqXHR) {
           showErrorAndLoginRedirection(jqXHR);
        }
    });
});

$('#edit_upload_document_modal_submit_new').click(function (e) {
    // $('#edit_upload_document_modal_submit_new').hide();
    $('#create-upload-doc-modal').removeClass('d-none');
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
        $('#create-upload-doc-modal').addClass('d-none');
        return false;
    } else {
        e.preventDefault();
        $('#btn-save-upload-doc-modal').text('Saving...')
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
                $('#btn-save-upload-doc-modal').text('Save');
                $('#create-upload-doc-modal').addClass('d-none');
                toastr.success(response.error_msg);
                esignResponseNew1();
                $('#edit_upload_document_modal_new')[0].reset();
                $('#closed_id_upload_document_new').click();
                $('#create-upload-doc-modal').addClass('d-none');
                // $('#edit_upload_document_modal_submit_new').show();

            },
            error: function (xhr, status, error) {
                $('#create-upload-doc-modal').addClass('d-none');
                $('#btn-save-upload-doc-modal').text('Save');
                showErrorAndLoginRedirection(xhr)
            }
        });
    }
})

function showData(pid) {
    $('[data-toggle="popover' + pid + '"]').popover();

    $('.popover').css('z-index', '100000');
}

function paginationRange(currentPage, totalPages, delta = 2) {
    const range = [];
    const rangeWithDots = [];
    let left = currentPage - delta;
    let right = currentPage + delta;

    // Always include the first page and the last page
    for (let i = 1; i <= totalPages; i++) {
        // If `i` is within the range around the current page,
        // or it's the first or the last page, add it to the range
        if (i === 1 || i === totalPages || (i >= left && i <= right)) {
            range.push(i);
        }
    }

    // Now, turn the range into a rangeWithDots array, adding "..." where needed
    let prevNum;
    for (let num of range) {
        if (prevNum) {
            // If the gap between two consecutive numbers is 2, insert the missing page
            if (num - prevNum === 2) {
                rangeWithDots.push(prevNum + 1);
            }
            // If the gap is larger than 2, insert '...'
            else if (num - prevNum > 2) {
                rangeWithDots.push('...');
            }
        }
        rangeWithDots.push(num);
        prevNum = num;
    }
    return rangeWithDots;
}

function undoData(id) {
    $.confirm({
        title: 'Are you sure?',
        type: 'blue',
        content: 'Are you sure you want to undo this data?',
        buttons: {
            confirm:{
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function () {
                    $.ajax({
                        async: false,
                        global: false,
                        type: 'POST',
                        url: _UNDO_ESIGN_DATA,
                        data: {
                            id: id,
                            '_token': _CSRF_TOKEN
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);
                            resfreshSignerDataNew()
                        },
                        error: function (xhr, status, error) {
                            showErrorAndLoginRedirection(xhr);
                        }
    
                    });

                }
            },
            
            cancel: function () {
                //close
            }
        }
    });

}

$(document).on('click', '.esign_paginate .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    esignResponseNew1(page);
});

function copyEsignLink(id){
    if(id !=""){
        $.ajax({

            url: _GENERATE_PATIENT_ESIGN_LINK,
            type: "POST",
            data: {
                '_token':_CSRF_TOKEN,
                'groupId':id
            },
           
            success: function (response) {
                let number = "";
                if(type =="mobile"){
                    number = $('#record_mobile_id').html()
                }else{
                    number = $('#record_phone_id').html()
                }
            
                if(response.data.length !=0){
                    navigator.clipboard.writeText(response.data.link.toString())
                    .then(() => 
                    toastr.success('Copied successfully')
                    )
                    .catch(err => console.error("Error copying:", err));
                }else{
                    toastr.success(response.error_msg);
                }
            },
            error: function (xhr, status, error) {
                showErrorAndLoginRedirection(xhr);
            }
        });
    }
    
}

function getQRcode(id,groupId){
    $.ajax({
        url: _GENERATE_QR_CODE_LINK,
        type: "POST",
        data: {
            '_token':_CSRF_TOKEN,
            'id':id,
            'groupId':groupId
        },
        success: function (response) {
           $.confirm({
            title: 'QR Code',
            content: response.qr_html, // SVG or IMG HTML
            type: 'blue',
            boxWidth: '400px', // or 'auto'
            useBootstrap: false, // Use this if you want custom width
            columnClass: 'medium', // or 'small', 'large'
            backgroundDismiss: true, // Allow closing by clicking outside
            buttons: {
                // copy: {
                //     text: 'Copy QR',
                //     btnClass: 'btn-success',
                //     action: function() {
                //         // Optional: Copy QR code SVG/IMG to clipboard
                //         const svg = $(this.$content).find('svg')[0];
                //         if (svg) {
                //             navigator.clipboard.writeText(svg.outerHTML);
                //             $.alert('QR code copied!');
                //         } else {
                //             $.alert('Copy not supported for this QR type.');
                //         }
                //     }
                // },
                // download: {
                //     text: 'Download',
                //     btnClass: 'btn-primary',
                //     action: function() {
                //         // Optional: Download QR code as image
                //         const svg = $(this.$content).find('svg')[0];
                //         if (svg) {
                //             const serializer = new XMLSerializer();
                //             const source = serializer.serializeToString(svg);
                //             const blob = new Blob([source], {type: 'image/svg+xml'});
                //             const url = URL.createObjectURL(blob);
                //             const a = document.createElement('a');
                //             a.href = url;
                //             a.download = 'qrcode.svg';
                //             a.click();
                //             URL.revokeObjectURL(url);
                //         } else {
                //             $.alert('Download not supported for this QR type.');
                //         }
                //     }
                // },
                close: {
                    text: 'Close',
                    btnClass: 'btn-secondary'
                }
            }
        });
        },
        error: function (xhr) {
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function commonViewLog(id){
    $.ajax({
        url: _COMMON_ESIGN_VIEW_LOG,
        type: "GET",
        data: {
            'id':id,
        },
        success: function (response) {
            commonViewEsignResponseLog(response.data);
        },
        error:function(xhr){
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function commonEsignViewLog(id){
    $.ajax({
        url: _COMMON_ESIGN_RESPONSE_VIEW_LOG,
        type: "GET",
        data: {
            'id':id,
        },
        success: function (response) {
            commonViewEsignResponseLog(response.data);
        },
        error:function(xhr){
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function commonViewEsignResponseLog(response){
    let old_response = response.old_response;
        let new_response = response.new_response;
        $('#view-esign-log-model').modal('show');
        let content = '';
        content += `<div class=\"row\">`;
        content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-primary text-white\" style="padding:10px !important"><b>Old Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
        content += highlightJson1(old_response);
        content += `</div></div></div>`;
        content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-success text-white\"  style="padding:10px !important"><b>New Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
        content += highlightJson1New(new_response);
        content += `</div></div></div>`;
        content += `</div>`;
        $('.dataContainerEsign').html(content);
}

function highlightJson1(jsonInput) {


    if (!jsonInput) return '<pre style="word-break:break-all;white-space:pre-wrap;">-</pre>';
    let obj;
    if (typeof jsonInput === 'string') {
        try {
            obj = JSON.parse(jsonInput);
            
        } catch (e) {
            // If not JSON, just show as text
            return '<pre style="word-break:break-all;white-space:pre-wrap;">' + jsonInput + '</pre>';
        }
    } else if (typeof jsonInput === 'object') {
        obj = jsonInput;
        
    } else {
        return '<pre style="word-break:break-all;white-space:pre-wrap;">' + String(jsonInput) + '</pre>';
    }
    let pretty = JSON.stringify(obj, null, 4);
    let content = '';
        content += ` <pre>{<br>`;
        $.each(JSON.parse(pretty), function(key, value) {
            var values = "-";
                if (value === undefined || value === null || value === "") {
                
                }else{
                    values = value;
                
                }
                content += `<span class="key">"${capitalizeFirstLetter(key.replace('_', ' '))}"</span>: <span class="string">"${values}"</span>,<br>`;
        });
        content += ` } <pre>`;

    return content;
}

function capitalizeFirstLetter(string) {
	string = string.replace(':', '');
	string = string.replace('"','');
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

function highlightJson1New(jsonInput) {

    if (!jsonInput) return '<pre style="word-break:break-all;white-space:pre-wrap;">-</pre>';
    let obj;
    if (typeof jsonInput === 'string') {
        try {
            obj = JSON.parse(jsonInput);
			
        } catch (e) {
            // If not JSON, just show as text
            return '<pre style="word-break:break-all;white-space:pre-wrap;">' + jsonInput + '</pre>';
        }
    } else if (typeof jsonInput === 'object') {
        obj = jsonInput;
		

        if (Array.isArray(obj)) {
            console.log("It's an array with length:", obj.length);
        } else if (typeof obj === "object" && obj !== null) {
            console.log("It's an object with keys:", Object.keys(obj));
        }
    } else {
        return '<pre style="word-break:break-all;white-space:pre-wrap;">' + String(jsonInput) + '</pre>';
    }

    
    let pretty = JSON.stringify(obj, null, 4);

	let content = '';
    let parsed = JSON.parse(pretty);
    content += `<pre>{<br>`;
    if (Array.isArray(parsed)) {
    // Case 2: Array of objects
    $.each(parsed, function(i, item) {
        content += `&nbsp;&nbsp;{<br>`;
            $.each(item, function(key, value) {
                let values = "-";
                if (value !== undefined && value !== null && value !== "") {
                    values = value;
                }
                content += `&nbsp;&nbsp;&nbsp;&nbsp;<span class="key">"${capitalizeFirstLetter(key.replace(/_/g, ' '))}"</span>: <span class="string">"${values}"</span>,<br>`;
            });
            content += `&nbsp;&nbsp;}<br>`;
        });
    } else if (typeof parsed === "object" && parsed !== null) {
        // Case 1: Single object
        $.each(parsed, function(key, value) {
            let values = "-";
            if (value !== undefined && value !== null && value !== "") {
                values = value;
            }
            content += `<span class="key">"${capitalizeFirstLetter(key.replace(/_/g, ' '))}"</span>: <span class="string">"${values}"</span>,<br>`;
        });
    } else {
        // Case 3: Primitive (string, number, etc.)
        content += `<span class="string">"${parsed}"</span><br>`;
    }

    content += `}</pre>`;

    return content;
}

function refreshEsignUploadDocument(){
    $('#edit_upload_document_modal_new')[0].reset();
    $('#documentName_error').html("");
    $('#fileUpload_error').html("");
    
}

$(document).ready(function () {
    $(document).on('click', 'a[data-toggle="modal"]', function () {
        var templateId = $(this).data('templete-id');
        var type = $(this).data('type');
        var group_id = $(this).data('group-id');
        var agency_form_id = $(this).data('agency-form-id');
        var esign_doc_id = $(this).data('esign-doc-id');
        var templateName = $(this).data('template-name');
        var pdfGenerate = $(this).data('pdf-generate');

        $('#template_id').val(templateId);
        $('#type').val(type);
        $('#group_id').val(group_id);
        $('#agency_form_id').val(agency_form_id);
        $('#esign_doc_id').val(esign_doc_id);
        $('#esign_mdo_source').addClass('hide');
        $('#esign_document_service_id').val(null).trigger('change')
        // Auto-populate Document Name from template name
        if (templateName) {
            $('#esign_document_name').val(templateName);
        } else {
            $('#esign_document_name').val('');
        }

        // Show attachment preview only for PDF files
        var isPdf = false;
        if (pdfGenerate && String(pdfGenerate).toLowerCase().endsWith('.pdf')) {
            isPdf = true;
        }

        let previewUrl = "";
        if (templateId != 0 && isPdf) {
            previewUrl = _BASE_URL + '/esign/show-pdf?group_id=' + group_id;
            $('#esign-attachment-iframe').attr('src', previewUrl);
            $('#esign-attachment-preview').show();
        } else if (templateId == 0 && esign_doc_id) {
            
            previewUrl = _BASE_URL + '/esign/write-show-pdf?group_id=' + esign_doc_id;
            $('#esign-attachment-iframe').attr('src', previewUrl);
            $('#esign-attachment-preview').show();
        } else {
            $('#esign-attachment-iframe').attr('src', '');
            $('#esign-attachment-preview').hide();
        }

    });
});

$('#esignMoveDocumentSave').click(function (e) {
    
    let esign_document_approval = $('#esign_document_approval').is(":checked");
    let esign_document_service_id = $("#esign_document_service_id").val();
    let esign_document_approval_user_id = $('#esign_document_approval_user_id').val();
    let esign_medication_list = $('#esign_medication_list').is(":checked");
    let esign_insurance_eligibility = $('#esign_insurance_eligibility').is(":checked");

    let esign_mdo_tag = $('#esign_mdo_tag').is(":checked");
    let esign_mdo_source = $('#esign_mdo_source_id').val();

    let cnt = 0;
    $("#esign_document_service_id_error").html("");
    $("#esign_document_approval_user_id_error").html("");
    $('#esign_insurance_eligibility_error').html("");
    $('#esign_mdo_source_error').html("");
    
    if (esign_document_service_id.length == 0) {
        $("#esign_document_service_id_error").html("Please select Services");
        cnt = 1;
    }

    if(esign_document_approval){
        if(esign_document_approval_user_id ==""){
            $("#esign_document_approval_user_id_error").html("Please select User");
            cnt =1;
        }
    }

    if(esign_medication_list  && esign_insurance_eligibility){
        $('#esign_insurance_eligibility_error').html("Please select any one: Medication List or Insurance Elg");
        cnt = 1;
    }

    if(esign_mdo_tag){
        if(esign_mdo_source ==''){
            $('#esign_mdo_source_error').html("Please select MDO Source");
            cnt =1;
        }
    }

    if (cnt == 1) {
        return false
    }

    $.confirm({
        title: 'Move To Document',
        type:"btn-blue",
        columnClass: "col-md-6",
        content: 'Are you sure you want to move the document?',
        buttons: {
            confirm: {
                text: 'Yes',
                btnClass: 'btn-green',
                action: function () {
                    $('#esign-move-doc-loader').removeClass('d-none');
                    $("#esignMoveDocumentSave").prop("disabled", true);
                    let formData = new FormData($('#esignMoveDocumentForm')[0]);
                    formData.append('_token', _CSRF_TOKEN);
                    $.ajax({
                       
                        type: "POST",
                        url: _ESIGN_MOVE_DOCUMENT_STORE,
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            $('#esign-move-doc-loader').addClass('d-none');
                            toastr.success(res.error_msg);
                            $("#esignMoveDocumentForm")[0].reset();
                            $("#esignMoveDocumentSave").prop('disabled', false);
                            $('#esignMoveDocumentModal-1').modal('hide');
                            $('#document_service_id').val("").change();
                            $('#esign-attachment-iframe').attr('src', '');
                            $('#esign-attachment-preview').hide();

                        },
                        error: function (jqXHR) {
                            $('#esign-move-doc-loader').addClass('d-none');
                            $("#esignMoveDocumentSave").prop('disabled', false);
                            showErrorAndLoginRedirection(jqXHR);
                        }
                    })
                }
            },
            cancel: {
                text: 'Cancel',
            }
        }
    });     
})

$('#esign_document_approval').click(function(e){
    let checked = $('#esign_document_approval:checked').val();
    
    if(_RECORD_TYPE == 'Patient'){
         $('#esign_internal_use_esign').prop('checked',false);
    }
    $('#esign_document_approval_id').addClass('hide');
    $("#esign_document_approval_user_id").tokenInput("destroy");
    if(checked){
        $('#esign_document_approval_id').removeClass('hide');
        loadDocumentChooseUserEsign();
    }
    
})

function loadDocumentChooseUserEsign(type=""){
    $("#esign_document_approval_user_id").tokenInput("destroy");

    let esignPopulate = [];
    let tokenLimit = 1;
    let esignUrlToken ='';
    if(_RECORD_TYPE == 'Caregiver'){
        esignUrlToken =  _SEARCH_NYBEST_USER; 
    }else{
        tokenLimit = null;
        esignUrlToken =  _SEARCH_NYBEST_USER;
        $('#esign_document_approval_id').removeClass('hide');
        $.ajax({
            async:false,
            global:false,
            url: _SEARCH_APPROVE_PATIENT_USER,  
            type: "GET",
            success: function (data) {
                esignPopulate = JSON.parse(data);
            }
        });
    }
    $("#esign_document_approval_user_id").tokenInput(esignUrlToken, {
        tokenLimit: tokenLimit,
        preventDuplicates: true,
        zindex: 1060,
        prePopulate: esignPopulate,
        onAdd: function (item) {

        },
        onReady: function() {
            setTimeout(function () {
                $(".token-input-dropdown").css({
                    "max-height": "180px",
                    "overflow-y": "auto",
                    "z-index": "999999",
                    "position": "fixed !important;",
                    
                });
            }, 500);
        }
    }); 
    
}

$('#esign_mdo_tag').on('change',function(){
    $('#esign_mdo_source').addClass('hide');
    if($(this).is(':checked') == 1){
        $('#esign_mdo_source').removeClass('hide');
    }
})

function showDocumentApproval(){
    $('#esign_document_approval').prop("checked",false);
    $('#esign_document_approval_id').addClass('hide');
    $('#esign_document_service_id_error').html('');
    
    $('#esignMoveDocumentForm')[0].reset()
    if(_RECORD_TYPE =='Patient'){
       $('#esign_document_approval').prop("checked", true);
        $('#esign_document_approval_id').removeClass('hide');
        loadDocumentChooseUserEsign();
    }
}

$('#esign_internal_use_esign').on('change',function(){
    if(_RECORD_TYPE =='Patient'){
        $('#esign_document_approval').prop('checked',false);
        $('#esign_document_approval_id').addClass('hide');
    }
   
})
