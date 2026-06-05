
$(document).ready(function () {
    loadDocumentAjaxList();
});
let documentApprovalUserName;
let documentApprovalUserId;
function documentFormReset() {
    closeDocumentSection();
}
function loadDocumentAjaxList(page = 1) {
    $('#loaderDocument').attr('style', '');
    $("#document_response_list").html("");

    $.ajax({

        type: "GET",
        url: _PATIENT_DOCUMENT_LIST,
        data: {
            "id": _RECORD_ID,
            "page": page
        },
        success: function (res) {
            $('#loaderDocument').attr('style', 'display:none');
            $('#document_response_list').html("")
            $('#document_response_list').html(res)
        },
        error: function (jqr) {
            showErrorAndLoginRedirection(jqr);
        }
    })
    return false;
}

$(document).on('click', '.dlog-pegination .pagination a', function (e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadDocumentAjaxList(page);
});

$('#documentSave').click(function (e) {

    var datenew_id = $('#datenew_id').val();
    var timemew = $('#timeidnew').val();
    var document_completed_date = $('#document_completed_date').val();
    var document_service_id = $('#document_service_id').val();
    var patient_document_review = $('#patient_document_review').is(":checked");
    var document_approval_user_id = $('#document_approval_user_id').val();
    var request_service_id = $('#request_service_id').val();
    var medication_list = $('#medication_list:checked').val();
    var insurance_elg = $('#insurance_elg:checked').val();
    var mdo_source = $('#mdo_tag:checked').val() == 1 ? $('#mdo_source').val() : '';

    $('#document_id_error').html("");
    $('#time_error').html("");
    $('#document_completed_date_error').html("");
    $('#document_service_id_error').html("");
    $('#document_approval_user_id_error').html("");
    $('#request_service_id_error').html("");
    $('#edit_medication_insurance_err').html("");
    $('#document_branch_error').html("");
    $('#add_mdo_tag_err').html("");
    var cnt = 0;

    let agencyId = _AGENCYID;
    let serviceIds = document_service_id == "" ? $('#service_id').val() : document_service_id;
    checkBranchMandatory(agencyId, serviceIds);
    if (isBranchMandatory && (SELECTED_BRANCH_ID == '' || SELECTED_BRANCH_ID == '0' || SELECTED_BRANCH_ID == null)) {
        // $('#document_branch_error').html(
        //     '<small style="display: block; margin-top: 8px; padding: 8px 10px; background: #fdecea; border-left: 3px solid #dc3545; color: #842029; font-size: 13px;">' +
        //     '<i class="fa fa-exclamation-circle" style="color: #dc3545;"></i> ' +
        //     '<strong>Error:</strong> Branch selection is required for the Service and Agency.' +
        //     '</small>'
        // );
        // cnt = 1;
    }

    if (datenew_id.trim() == '') {
        $('#document_id_error').html("Please enter Document Name");
        cnt = 1;
    }
    if (timemew.trim() == '') {
        $('#images_error').html("Please select Attachment");
        cnt = 1;
    } else {
        var fileExtensionType = ['pdf', 'csv', 'xlsx', 'xls', 'docx', 'doc'];
        var files = $('input[name="images"]')[0].files;
        var fileName = files[0].name;
        var fileType = fileName.substr(fileName.lastIndexOf('.') + 1);
        $('#images_error').html("");
        if ($.inArray(fileName.split('.').pop().toLowerCase(), fileExtensionType) == -1) {
            $('#images_error').html("Please select only pdf or csv file");
            cnt = 1;
        }
    }

    if (patient_document_review) {
        if (document_approval_user_id == "" && _RECORD_TYPE == 'Caregiver') {
            $('#document_approval_user_id_error').html("Please select User");
            cnt = 1;
        }
    }

    if (agencyFks == "") {
        // if(document_completed_date ==""){
        //     $('#document_completed_date_error').html("Please select Document Completed Date");
        //     cnt=1; 
        // }

        // if(request_service_id ==""){
        //     $('#request_service_id_error').html("Please select Request Services");
        //     cnt=1; 
        // }

        // if(document_service_id.length == 0){
        //     $('#document_service_id_error').html("Please select Services");
        //     cnt=1; 
        // }
    }

    if (insurance_elg != '' && medication_list != '' && insurance_elg == 1 && medication_list == 1) {
        $('#add_medication_insurance_err').html("Please select any one: Medication List or Insurance Elg.");
        cnt = 1;
    }

    if ($('#mdo_tag:checked').val() == 1 && mdo_source == "") {
        $('#add_mdo_tag_err').html("MDO Source is required.");
        cnt = 1;
    }
    if (cnt == 0) {
        if (_RECORD_TYPE == 'Patient') {
            $('#loadertag_doc').attr('style', '')
            submitPatientData();
        } else {
            $('#loadertag_doc').attr('style', '')
            $("#documentSave").prop('disabled', true);
            var formData = new FormData($('#formnew')[0]);
            formData.append('_token', _CSRF_TOKEN);
            formData.append('type', _RECORD_TYPE);

            $.ajax({

                type: "POST",
                url: _PATIENT_DOCUMENT_ADD,
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    $('#loadertag_doc').attr('style', 'display:none')
                    toastr.success(res.error_msg);

                    $("#documentSave").prop('disabled', false);
                    $('#exampleModal-5').modal('hide')
                    loadDocumentAjaxList();
                    closeDocumentSection();
                    $('#document_service_id').val("").change();
                },
                error: function (jqXHR) {
                    $('#loadertag_doc').attr('style', 'display:none')
                    $("#documentSave").prop('disabled', false);
                    showErrorAndLoginRedirection(jqXHR);
                }
            })
        }
    } else {
        return false;
    }
})

function deleteRecordDocument(recordId, documentId) {
    var url = _DELETE_DOCUMENT;
    $.confirm({
        title: 'Delete',
        columnClass: "col-md-6",
        content: 'Are you sure delete record?',
        buttons: {
            formSubmit: {
                text: 'Delete',
                btnClass: 'btn-danger',
                action: function () {
                    window.location.href = url + '/' + recordId + '/' + documentId;
                }
            },
            cancel: function () {
                //close
            },
        },
    });
}

function getEditDocument(id, document_name) {
    $('.documens').html("Edit Document");

    $('#document_ids').val(id);
    $('#datenew_id').attr('readonly', true);
    $('#datenew_id').val(document_name);
}



$("input[name='is_checked']").change(function () {

    $('#request_service_id_error').html("");
    $('#document_service_id_error').html("");
    $('#document_completed_date_error').html("");

    if ($(this).is(":checked")) {
        $('.dynamic_required').addClass('d-none');
        $('#is_checked').val(1);
    } else {
        $('.dynamic_required').removeClass('d-none');
        $('#is_checked').val('');
    }
});

function closeDocumentSection() {
    $('#formnew')[0].reset();
    $('#document_service_id').val('null').change();
    $('#images_error').html("")
    $('#document_completed_date_error').html("")
    $('#document_service_id_error').html("")
    $('#document_id_error').html("")
    $("#add_medication_insurance_err").html("");
    $('#document_branch_error').html("");
    isBranchMandatory = false;
    if (_AUTH_AGENCY_ID != "") {

    } else {
        $('#document_approval_user_id_error').html("")
        $("#document_approval_user_id").tokenInput("destroy");
        $('#document_approval_id').addClass('hide');
    }

}

$('#patient_document_review').click(function (e) {
    var checked = $('#patient_document_review:checked').val();
    if (checked) {
        if (_RECORD_TYPE == 'Patient') {
            $('#internal_use').prop('checked', false);
        }
        $('#document_approval_id').removeClass('hide');
        loadDocumentChooseUser();
    } else {
        $('#document_approval_id').addClass('hide');
        $("#document_approval_user_id").tokenInput("destroy");
    }
})
function loadDocumentChooseUser(type = "") {
    $("#document_approval_user_id").tokenInput("destroy");
    $("#edit_document_approval_user_id").tokenInput("destroy");
    populate = [];
    tokenLimit = 1;
    if (_RECORD_TYPE == 'Caregiver') {
        var urlToken = _SEARCH_NYBEST_USER;
    } else {
        tokenLimit = null;
        var urlToken = _SEARCH_NYBEST_USER;
        if (type == "edit") {
            $('#edit_document_approval_id').removeClass('hide');
        } else {
            $('#document_approval_id').removeClass('hide');
        }
        $.ajax({
            async: false,
            global: false,
            url: _SEARCH_APPROVE_PATIENT_USER,
            type: "GET",
            success: function (data) {
                populate = JSON.parse(data);
            }
        });
    }
    if (type != "") {
        var populate = [];
        // var ids = documentApprovalUserId ? documentApprovalUserId.split(",") : []; // ["482", "507", "489"]
        var ids = (typeof documentApprovalUserId === 'string' && documentApprovalUserId.trim() !== "") ? documentApprovalUserId.split(",") : [];
        for (var i = 0; i < ids.length; i++) {
            populate.push({
                id: ids[i].trim(),
                name: documentApprovalUserName[i] // match index
            });
        }
        $("#edit_document_approval_user_id").tokenInput(urlToken, {
            tokenLimit: tokenLimit,
            zindex: 999999999,
            prePopulate: populate,
            preventDuplicates: true,
            onAdd: function (item) {
            },
            onReady: function () {
                setTimeout(function () {
                    $(".token-input-dropdown").css({
                        "max-height": "180px",
                        "overflow-y": "auto"
                    });
                }, 500);
            }
        });
    } else {
        $("#document_approval_user_id").tokenInput(urlToken, {
            tokenLimit: tokenLimit,
            preventDuplicates: true,
            zindex: 1060,
            prePopulate: populate,
            onAdd: function (item) {

            },
            onReady: function () {
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

}

function getPatientDocumentDetails(id, patientId) {

    $.ajax({
        url: _PATIENT_DOCUMENT_DETAILS_BY_ID,
        type: "get",
        data: {

            document_id: id,
            patient_id: patientId,

        },
        success: function (res) {
            resetDocumentUpload();
            $('#edit_doc_name').val(res.data[0].document_name);
            $('#edit_document_completed_date').val('');
            $('#edit_document_patient_id').val(patientId)
            if (res.data[0].assign_document_approval != '') {

                documentApprovalUserName = res.data[0].assign_document_approval;
                documentApprovalUserId = res.data[0].assign_document_review;
            }

            if (res.data[0].document_completed_date != null) {
                $('#edit_document_completed_date').val(moment(res.data[0].document_completed_date).format('MM/DD/YYYY'));
            }

            $('#edit_internal_use').prop("checked", false);
            if (res.data[0].internal_use == 1) {
                $('#edit_internal_use').prop("checked", true);
            }

            if (res.data[0].assign_document_review != null) {

                $('#edit_document_approval_id').removeClass('hide');
                $('#edit_patient_document_review').prop("checked", true)
                loadDocumentChooseUser("edit");
            }
            $('.not-approved-div').removeClass('hide');
            $('.not-approved-div').addClass('show');
            if (res.data[0].document_review_status == 'Approved' && res.data[0].patient_details.type == 'Patient') {
                $('.not-approved-div').addClass('hide');
                $('.not-approved-div').removeClass('show');
            }
            $('#edit_medication_list').prop("checked", false);
            if (res.data[0].medication_list == 1) {
                $('#edit_medication_list').prop("checked", true);
            }

            $('#edit_insurance_elg').prop("checked", false);
            if (res.data[0].insurance_elg == 1) {
                $('#edit_insurance_elg').prop("checked", true);
            }
            $('#edit_mdo_tag').prop("checked", false);
            if (res.data[0].mdo_tag == 1) {
                $('#edit_mdo_tag').prop("checked", true);
                $('#edit_mdo_source').removeClass('hide');
                $('#edit_mdo_source').val(res.data[0].mdo_source);
            }
        },
        error: function (jqr) {
        }
    });
}

$('#edit_patient_document_review').click(function (e) {
    var checked = $('#edit_patient_document_review:checked').val();
    if (checked) {
        if (_RECORD_TYPE == 'Patient') {
            $('#edit_internal_use').prop('checked', false);
        }
        $('#edit_document_approval_id').removeClass('hide');
        loadDocumentChooseUser("edit");
    } else {
        $('#edit_document_approval_id').addClass('hide');
        $("#edit_document_approval_user_id").tokenInput("destroy");
    }
})

function resetDocumentUpload() {
    $('#edit_document_service_form')[0].reset();
    $("#edit_document_approval_user_id").tokenInput("destroy");
    $('#edit_patient_document_review').prop("checked", false)
    $('#edit_document_approval_id').addClass('hide');
    $('#edit_document_service_id_error').html("")
    $('#edit_doc_name_error').html("")
    $('#edit_document_approval_user_id_error').html("");
    $('#edit_medication_insurance_err').html("");
    $('#edit_document_completed_date_error').html("");
}

function viewDocumentDetails(documentId) {
    $.ajax({
        url: _DOCUMENT_SEND_REPORT_DETAILS_BY_ID,
        data: {
            'id': documentId,
        },
        type: "GET",
        success: function (res) {
            $('#view-exampleModal-document-services').modal('show');
            $('#show_over_review_document_id').attr('src', res.data.attachment);
            $('#review_over_document_id').val(res.data.id);
            $('#review_over_document_name').html(res.data.document_name);
            $('#review_over_requested_id').html((res.data.request_service_id != "") ? res.data.request_service_id : " - ");
            $('#review_over_attachment_service_id').html((res.data.services != "") ? res.data.services : " - ");

            var document_completed_date = " - ";
            if (res.data.document_completed_date != null) {
                document_completed_date = moment(res.data.document_completed_date).format('MM/DD/YYYY')
            }

            var created_date = " - ";
            if (res.data.created_date != null) {
                created_date = moment(res.data.created_date).format('MM/DD/YYYY hh:mm A')
            }

            var review_user = "";
            var review_date = " - ";
            if (res.data.review_user_details && res.data.review_user_details.id != null) {
                review_user = res.data.review_user_details.full_name;
                review_date = moment(res.data.document_review_date).format('MM/DD/YYYY hh:mm A')
            }

            var assign_user = " - ";
            if (res.data.assign_user_review_document && res.data.assign_user_review_document.id != null) {
                assign_user = res.data.assign_user_review_document.full_name;
            }

            if (res.data.document_review_status == "Approved") {
                var status = '<span class="badge badge-outline-success" style="color:#d76718;">Approved</span>';
            } else if (res.data.document_review_status == "Rejected") {
                var status = '<span class="badge badge-outline-danger" style="color:#d76718;">Rejected</span>';
            } else {
                var status = '<span class="badge badge-outline-primary" style="color:#d76718;">Pending</span>';
            }

            $('#review_over_document_completion_date').html(document_completed_date);
            $('#review_over_document_created_date').html(created_date + '<br>' + res.data.user_details.full_name);

            $('#review_over_document_status').html(status);
            $('#review_over_document_assign_by').html(assign_user);
            $('#review_over_document_review_by').html(review_date + '<br>' + review_user);
            $('#review_over_document_review_notes').html((res.data.status_note != "") ? res.data.status_note : " - ");

        }
    });
}

function efaxModal(id) {
    $.ajax({
        url: _DOCUMENT_SEND_REPORT_DETAILS_BY_ID,
        type: "get",
        data: {
            id: id,
            patient_id: _RECORD_ID,
        },
        success: function (res) {
            $('#e_fax_no').val("");
            $('#e_fax_no_error').html("");

            $('#fax_over_review_document_id').attr('src', '');
            $('#fax_over_review_document_id').attr('src', res.data.attachment);
            $('#doc_efax_id').val(id);
            $('#doc_patient_fax_id').val(_RECORD_ID);
            $('#e_fax_no').val(_EFAX_NO);
            $('#efax-exampleModal').modal('show');
        }
    });

}

function submitEFax() {
    var e_fax_no = $('#e_fax_no').val();
    var cnt = 0;
    $('#e_fax_no_error').html("");

    if (e_fax_no.trim() == '') {
        $('#e_fax_no_error').html("Please enter E Fax Number");
        cnt = 1;
    }

    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _SEND_EFAX_DOCUMENT,
            type: "post",
            data: {
                fax_no: e_fax_no,
                document_id: $('#doc_efax_id').val(),
                patient_id: $('#doc_patient_fax_id').val(),
                '_token': _CSRF_TOKEN
            },
            success: function (res) {
                toastr.success(res.error_msg);
                $('#e_fax_no').val("")
                $('#efax-exampleModal').modal('hide');
            },
            error: function (jqr) {
                showErrorAndLoginRedirection(jqr);
            }
        });
    }
}

function sendThirdPartyApiCall(id, documentId, portalEndPoint) {
    var url = _SKILL_CATEGORY.replace('alayacare-skill-category', '');
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        type: 'blue',
        content: 'You want send document for third party',
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        async: false,
                        global: false,
                        url: _SKILL_CATEGORY.replace('alayacare-skill-category', '') + '' + portalEndPoint,
                        type: "post",
                        data: {
                            id: id,
                            documentId: documentId,

                            '_token': _CSRF_TOKEN
                        },
                        success: function (res) {
                            toastr.success(res.error_msg);

                        },
                        error: function (jqr) {
                            showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel: function () {
                //close
            },
        },
    });
}

$('#internal_use').click(function (e) {
    if (_RECORD_TYPE == 'Patient') {
        var checked = $('#internal_use:checked').val();
        if (checked) {
            $('#document_approval_id').removeClass('hide');
            $('#document_approval_id').addClass('hide');
            $('#patient_document_review').prop('checked', false);
            $("#document_approval_user_id").tokenInput("destroy");
        }
    }
})

$('#edit_internal_use').click(function (e) {
    if (_RECORD_TYPE == 'Patient') {
        var checked = $('#edit_internal_use:checked').val();
        if (checked) {
            $('#edit_document_approval_id').addClass('hide');
            $('#edit_patient_document_review').prop('checked', false);
            $("#edit_document_approval_user_id").tokenInput("destroy");
        }
    }
})

function submitPatientData() {
    $('#doc_que_error').html("");
    let que_count = 0;
    $.ajax({
        url: _GET_QUESTIONS,
        type: "GET",
        async: false,
        success: function (res) {
            if (res.data) {
                content = "";
                que_count = res.data.length;
                $.each(res.data, function (index, value) {
                    content += '<div class="form-check align-start mb-2"><input type="checkbox" value="1"  id="question_' + index + '" data-value="' + value.id + '" class="form-check-input"><label for="question_' + index + '" class="form-check-label">' + value.question + '</label></div>';
                });
                content += '<div id="doc_que_error" class="text-danger ml-2"></div>';
            }
        }
    })
    $.confirm({
        title: "Are you sure?",
        content: content,
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-blue',
                action: function () {
                    let self = this;
                    let questions = true;
                    let question_ids = [];
                    for (let i = 0; i < que_count; i++) {
                        if ($('#question_' + i).is(':checked') == false) {
                            questions = false;
                        } else {
                            question_ids.push($('#question_' + i).data('value'));
                        }
                    }
                    if (!questions) {
                        $('#doc_que_error').html("Please check your confirmation");
                        return false;
                    }
                    self.buttons.submit.setText('<i class="fa fa-spinner fa-spin"></i> Confirm');
                    self.buttons.submit.disable();
                    var formData = new FormData($('#formnew')[0]);
                    formData.append('_token', _CSRF_TOKEN);
                    formData.append('type', _RECORD_TYPE);
                    formData.append('questions', question_ids);
                    if (_aiAnalysisJsonResult) {
                        formData.append('ai_summary', typeof _aiAnalysisJsonResult === 'string'
                            ? _aiAnalysisJsonResult
                            : JSON.stringify(_aiAnalysisJsonResult));
                    }

                    $.ajax({
                        // async:false,
                        // global:false,
                        type: "POST",
                        url: _PATIENT_DOCUMENT_ADD,
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            _aiAnalysisJsonResult = null;
                            setTimeout(function () {
                                self.hideLoading();
                                $.alertSuccess = true;
                                self.buttons.submit.setText('Confirm');
                                self.buttons.submit.enable();
                                self.close();
                                toastr.success(res.error_msg);
                            }, 2000);
                            $('#loadertag_doc').attr('style', 'none')
                            $('#exampleModal-5').modal('hide')
                            $('#document_service_id').val("").change();
                            loadDocumentAjaxList();
                            closeDocumentSection();
                        },
                        error: function (jqXHR) {
                            self.hideLoading();
                            $('#loadertag_doc').attr('style', 'none')
                            self.buttons.submit.setText('Confirm');
                            self.buttons.submit.enable();
                            showErrorAndLoginRedirection(jqXHR);
                        }
                    })
                    return false;
                }
            },
            cancel: {
                text: 'Cancel',
                action: function () {

                }
            }

        },

    });
}

$('#document_service_id').change(function () {
    if (_RECORD_TYPE === 'Patient' && _AUTH_AGENCY_ID == "") {
        let service_id = $(this).val() || [];
        service_id = Array.isArray(service_id) ? service_id : [service_id];
        if (service_id.length != 0) {
            var defineResponse = JSON.parse(_DYNAMIC_DOC_APPROVED_USERS);

            $("#document_approval_user_id").tokenInput("clear");
            var dataResponse = initializeDynamicDocApprovedUser(defineResponse, service_id);

            $.each(dataResponse, function (index, userObj) {
                $.each(userObj, function (userId, userName) {
                    $("#document_approval_user_id").tokenInput("add", {
                        id: userId,
                        name: userName
                    });
                });
            });
        }

    }
});

$('#edit_document_service_id').change(function () {
    if (_RECORD_TYPE === 'Patient') {
        let service_id = $(this).val() || [];
        service_id = Array.isArray(service_id) ? service_id : [service_id];
        if (service_id.length != 0) {
            var defineResponse = JSON.parse(_DYNAMIC_DOC_APPROVED_USERS);

            $("#edit_document_approval_user_id").tokenInput("clear");
            var dataResponse = initializeDynamicDocApprovedUser(defineResponse, service_id);


            $.each(dataResponse, function (index, userObj) {
                $.each(userObj, function (userId, userName) {
                    $("#edit_document_approval_user_id").tokenInput("add", {
                        id: userId,
                        name: userName
                    });
                });
            });
        }
    }
});

$('#exampleModal-5').on('hidden.bs.modal', function (e) {
    $("#documentSave").prop('disabled', false);
    $('#loadertag_doc').attr('style', 'display:none')
});

function initializeDynamicDocApprovedUser(defineResponse, service_id) {
    var dataUser = [];
    if (service_id.length == 1) {

        if (jQuery.inArray("181", service_id) === -1) {
            dataUser.push(defineResponse["without_service"][0]);
        } else {
            dataUser.push(defineResponse["181"][0]);
        }
    } else {
        if (service_id.length > 1) {
            var findIndex = service_id.findIndex(v => v == 181)
            if (findIndex >= 0) {
                var jadaArray = defineResponse["181"][0];
                dataUser.push(jadaArray)
            }
            var tilineArray = defineResponse["without_service"][0];
            dataUser.push(tilineArray)

        }
    }
    return dataUser;
}

function openRNPadModal(documentId) {
    $('#rnpad_choose_services_error').html("");
    $.ajax({
        async: false,
        global: false,
        url: _GET_RNPAD_URL_SERVICES,
        type: "get",
        data: {
            id: _RECORD_ID,
            agency_id: _RECORD_AGENCY_ID

        },
        success: function (res) {
            var json = res.data;
            var optionHtmlResponse = '<option value="">Select Services</option>';
            if (json.length != 0) {

                $.each(json, function (i, v) {
                    optionHtmlResponse += "<option value='" + v.id + "'>" + v.id + ' - ' + v.services + ' - ' + v.status + ' - ' + v.created_date + "</option>";
                })
            }

            $('#rnpad_choose_services').html('');
            $('#rnpad_choose_services').html(optionHtmlResponse)
            $('#rnpad_document_id').val(documentId);
            $('#send-rnpad-document-modal').modal('show')

        },
        error: function (jqr) {
            showErrorAndLoginRedirection(jqr);
        }
    });
}

function submitRndPadDocument() {
    $('#submit-rnpad-doc-spinner').removeClass('d-none')

    var rnpad_choose_services = $('#rnpad_choose_services').val();
    $('#rnpad_choose_services_error').html("");
    var cnt = 0;

    if (rnpad_choose_services == "") {
        $('#rnpad_choose_services_error').html("Please select Service");
        cnt = 1;
    }

    if (cnt == 1) {
        $('#submit-rnpad-doc-spinner').addClass('d-none')
        return false;
    } else {
        $('#btn-submit-rnpad-text').html("Sending...");
        $.ajax({
            type: "POST",
            url: _SEND_RNPAD_DOCUMENT,
            data: {
                third_party_id: rnpad_choose_services,
                appointment_id: _RECORD_ID,
                document_id: $('#rnpad_document_id').val(),
                agency_id: _RECORD_AGENCY_ID,
                '_token': _CSRF_TOKEN
            },
            success: function (res) {
                toastr.success(res.error_msg);
                $('#send-rnpad-document-modal').modal('hide');
                $('#btn-submit-rnpad-text').html("Send");
                $('#submit-rnpad-doc-spinner').addClass('d-none')
                loadDocumentAjaxList(1)
            },
            error: function (jqr) {
                $('#btn-submit-rnpad-text').html("Send");
                $('#submit-rnpad-doc-spinner').addClass('d-none')
                showErrorAndLoginRedirection(jqr);
            }
        })

    }

}

function closeSendRnPadModal() {
    $('#send-rnpad-document-modal').modal('hide');
}

function documentRegenerate(id) {
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        type: 'blue',
        content: 'You want to regenerate the document pdf?',
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        type: "GET",
                        url: _IMAGICK_TEMP_PDF,
                        data: {
                            id: id,
                        },
                        success: function (res) {
                            toastr.success(res.error_msg);
                            loadDocumentAjaxList(1)
                        },
                        error: function (jqr) {
                            showErrorAndLoginRedirection(jqr);
                        }
                    })
                }
            },
            cancel: function () {
                //close
            },
        },
    });
}

function openTaskHealthModal(documentId) {
    $('#task_health_choose_services_error').html("");
    $.ajax({
        async: false,
        global: false,
        url: _GET_TASK_HEALTH_URL_SERVICES,
        type: "get",
        data: {
            id: _RECORD_ID,
            agency_id: _RECORD_AGENCY_ID
        },
        success: function (res) {
            var json = res.data;
            var optionHtmlResponse = '<option value="">Select Task health visit</option>';
            if (json.length != 0) {
                $.each(json, function (i, v) {
                    optionHtmlResponse += "<option value='" + v.id + "'>" + v.id + ' - ' + v.task_id + ' - ' + v.task_health_patient_id + ' - ' + v.created_date + "</option>";
                })
            }
            $('#task_health_choose_services').html('');
            $('#task_health_choose_services').html(optionHtmlResponse)
            $('#task_health_document_id').val(documentId);
            $('#send-task-health-document-modal').modal('show')

        },
        error: function (jqr) {
            showErrorAndLoginRedirection(jqr);
        }
    });
}

function submitTaskHealthDocument() {
    $('#submit-task-health-doc-spinner').removeClass('d-none')

    var task_health_choose_services = $('#task_health_choose_services').val();
    $('#task_health_choose_services_error').html("");
    var cnt = 0;

    if (task_health_choose_services == "") {
        $('#task_health_choose_services_error').html("Please select Service");
        cnt = 1;
    }

    if (cnt == 1) {
        $('#submit-task-health-doc-spinner').addClass('d-none')
        return false;
    } else {
        $('#btn-submit-task_health-text').html("Sending...");
        $.confirm({
            title: 'Are you sure?',
            columnClass: "col-md-6",
            type: 'blue',
            content: 'You want to send the document to Task Health?',
            buttons: {
                formSubmit: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function () {
                        $.ajax({
                            type: "POST",
                            url: _SEND_TASK_HEALTH_DOCUMENT,
                            data: {
                                third_party_id: task_health_choose_services,
                                appointment_id: _RECORD_ID,
                                document_id: $('#task_health_document_id').val(),
                                '_token': _CSRF_TOKEN
                            },
                            success: function (res) {
                                toastr.success(res.error_msg);
                                $('#submit-task-health-doc-spinner').addClass('d-none');
                                $('#btn-submit-task_health-text').html("Send");
                                $('#send-task-health-document-modal').modal('hide');
                                loadDocumentAjaxList(1)
                            },
                            error: function (jqr) {
                                $('#submit-task-health-doc-spinner').addClass('d-none');
                                $('#btn-submit-task_health-text').html("Send");
                                $('#send-task-health-document-modal').modal('hide');
                                showErrorAndLoginRedirection(jqr);
                            }
                        })
                    }
                },
                cancel: function () {
                    //close
                },
            },
        });
    }
}

function openThirdPartModal(documentId) {
    $('#show_third_party_data').html('');
    $.ajax({
        async: false,
        global: false,
        url: _GET_THIRD_PARTY_DATA,
        type: "get",
        data: {
            patient_id: _RECORD_ID,
            agency_id: _RECORD_AGENCY_ID,
            doc_id: documentId
        },
        success: function (res) {
            $('#send-third-party-document-modal').modal('show');
            $('#show_third_party_data').html(res);
        },
        error: function (jqr) {
            showErrorAndLoginRedirection(jqr);
        }
    });
}

function closeThirdPartyModule() {
    $('#show_third_party_data').html('');
    $('#submit-third-party-doc-spinner').addClass('d-none');
    $('#btn-submit-third-party-text').html('Send');
}

function submitThirdPartyDocument() {
    var selectedIds = [];
    var serviceIds = [];
    var documentId = $('#third_party_doc_id').val();

    $('input[name="link_third_party[]"]:checked').each(function () {
        selectedIds.push($(this).val());
        serviceIds.push($(this).data('service-id'));
    });

    if (selectedIds.length == 0) {
        toastr.error('Please select at least one record');
        return false;
    }

    if (documentId == '') {
        toastr.error('Document ID is missing');
        return false;
    }

    $('#submit-third-party-doc-spinner').removeClass('d-none');
    $('#btn-submit-third-party-text').html('Sending...');

    $.ajax({
        type: "POST",
        url: _SAVE_THIRD_PARTY_DOC_DATA,
        data: {
            patient_id: _RECORD_ID,
            document_id: documentId,
            third_party_ids: selectedIds,
            service_ids: serviceIds,
            '_token': _CSRF_TOKEN
        },
        success: function (res) {
            toastr.success(res.error_msg);
            $('#send-third-party-document-modal').modal('hide');
            $('#submit-third-party-doc-spinner').addClass('d-none');
            $('#btn-submit-third-party-text').html('Send');
            loadDocumentAjaxList(1);
        },
        error: function (jqr) {
            $('#submit-third-party-doc-spinner').addClass('d-none');
            $('#btn-submit-third-party-text').html('Send');
            showErrorAndLoginRedirection(jqr);
        }
    });
}


$('#mdo_tag').on('change', function () {
    $('#mdo_source').addClass('hide');
    if ($(this).is(':checked') == 1) {
        $('#mdo_source').removeClass('hide');
    }
})

$('#edit_mdo_tag').on('change', function () {
    $('#edit_mdo_source').addClass('hide');
    if ($(this).is(':checked') == 1) {
        $('#edit_mdo_source').removeClass('hide');
    }
})

/**********************Visiting Aids */

let sendVisitingAidMedicalResponse = [];
let visitingSendDocumentId = '';
function getPendingVisitingMedical(id, docId) {
    visitingSendDocumentId = docId;
    $.ajax({
        type: "GET",
        url: _GET_VISITING_THIRD_PARTY_PENDING_MEDICAL,
        data: {
            patient_id: id,
            agency_id: _AGENCYID
        },
        success: function (res) {
            let response = res.data.pendingmedicals;
            let optionHtml = "<option value=''>Select Medical</option>";
            if (response.length != 0) {
                $.each(response, function (i, v) {
                    sendVisitingAidMedicalResponse[v.MedicalRefID] = v.MedicalID;
                    optionHtml += `<option value="${v.MedicalID}" data-medical-ref="${v.MedicalRefID}">${v.MedicalName}</option>`;

                })
            }

            $('#visiting_third_party_medical_id').html("");
            $('#visiting_third_party_medical_id').select2();
            $('#visiting_third_party_medical_id').html(optionHtml)

        },
        error: function (jqr) {
            $('#submit-third-party-doc-spinner').addClass('d-none');
            $('#btn-submit-third-party-text').html('Send');
            showErrorAndLoginRedirection(jqr);
        }
    });
}

var thirdPartySelectedArray = [];
var thirdPartySelectedFlag = true;


$('#visiting_third_party_medical_id').on("select2:select", function (e) {
    thirdPartySelectedFlag = true;
    $('#visiting-medical-loader').removeClass('d-none');
    getVistingThirdPartyMedicalResult(e.target.value, $(this).attr('data-medical-ref'))

});

$('#visiting_third_party_medical_id').on("select2:unselect", function (e) {
    $('#visiting-medical-loader').addClass('d-none');
    var selectedID = $('#visiting_third_party_medical_id').val();
    var temp = [];
    $.each(thirdPartySelectedArray, function (i, k) {
        var findSelected = selectedID.find(o => o == k);
        if (findSelected) {
            temp.push(k);
        } else {
            $('#new_medical_result_' + k).remove();

        }
    })
    thirdPartySelectedArray = temp;
    thirdPartySelectedFlag = false;

    if (thirdPartySelectedArray.length == 0) {
        $('#multipleMedicalResultId').attr('style', 'display:none');
    }

});


function getVistingThirdPartyMedicalResult(medicalId) {

    if (thirdPartySelectedFlag) {
        var selectedID = $('#visiting_third_party_medical_id').val();
        var values = medicalId;
        if (thirdPartySelectedArray.length != 0) {
            $.each(selectedID, function (key, v) {
                var select = thirdPartySelectedArray.includes(v);

                if (!select) {
                    thirdPartySelectedArray.push(v);
                    values = v;
                }
            })

        } else {
            thirdPartySelectedArray.push(values)
        }

        var selectedText = '';
        var selectedTextData = $('#visiting_third_party_medical_id').select2("data");

        let visitingMedicalRefId = "";
        for (var i = 0; i <= selectedTextData.length - 1; i++) {

            if (selectedTextData[i].id == values) {
                selectedText = selectedTextData[i].text;
                visitingMedicalRefId = selectedTextData[i].element.getAttribute('data-medical-ref');

            }
        }

        //get result name
        $.ajax({

            global: false,
            url: _GET_THIRD_PARTY_MEDICAL_RESULT_LIST,
            data: {
                'agencyId': _AGENCYID,
                'medicaid_id': values,
            },
            success: function (response) {
                $('#visiting-medical-loader').addClass('d-none');

                var res = response.data.length;

                var htmlrs = '<option value="">Select ' + selectedText + ' Result</option>';
                if (res != 0) {
                    $.each(response.data, function (i, v) {
                        htmlrs += '<option value="' + v.medicalresultid + '">' + v.result + '</option>';
                    })
                }

                var sendCaregiverMedicalID = visitingMedicalRefId;

                var selectHtml = `<div class="col-md-3" id="new_medical_result_${values}"><div class="form-group">
                            <label for="recipient-name" class="col-form-label">${selectedText} Results<span style="color:red">*</span>:</label>
                                <select name="visiting_third_party_medical_result_${values}" class="form-control" id="visiting_third_party_medical_result_${values}">${htmlrs}</select>
                                <span id="visiting_third_party_medical_result_${values}_error" style="color:red" class="error"></span>
                        </div></div><input type="hidden" name="visiting_third_party_medical_ref_${values}" value="${sendCaregiverMedicalID}">`;
                if (thirdPartySelectedArray.length == 1) {
                    $('#multipleThirdPartyMedicalResultId').attr('style', '');
                }

                $('#multipleThirdPartyMedicalResultId').append(selectHtml)
                $('#third_party_medical_result_' + values).select2();

            },
            error: function (jqr) {
                $('#visiting-medical-loader').addClass('d-none');
                showErrorAndLoginRedirection(jqr);
            }
        });
    }
}

function submitVistingThirdPartyDocument() {
    var selectedIds = $('#visiting_third_party_medical_id').val();
    $('#visiting_third_party_medical_id_error').html('');
    var cnt = 0;
    if (selectedIds.length == 0) {
        $('#visiting_third_party_medical_id_error').html('Please select at least one medical');
        cnt = 1;
    }

    if (selectedIds.length != 0) {
        $.each(selectedIds, function (i, v) {
            $('#visiting_third_party_medical_result_' + v + '_error').html("");

            var selectedValue = $('#visiting_third_party_medical_result_' + v).val();

            if (selectedValue == "") {
                $('#visiting_third_party_medical_result_' + v + '_error').html("Please select Result");
                cnt = 1;
            }
        });
    }


    if (cnt == 1) {
        return false
    } else {
        var formData = new FormData($('#visiting_third_party_medical_form')[0]);
        formData.append('_token', _CSRF_TOKEN)
        formData.append('agencyId', _AGENCYID)
        formData.append('patient_id', _RECORD_ID)
        formData.append('document_id', visitingSendDocumentId)
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _SEND_VISITING_THIRD_PARTY_DOCUMENT,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.error_msg);
                closeVistingThirdPartyModule();
                loadDocumentAjaxList();
            },
            error: function (jqr) {
                showErrorAndLoginRedirection(jqr);
            }
        })
    }

}

function closeVistingThirdPartyModule() {
    $('#exampleModal-visiting-aid-document').modal('hide');
    $('#visiting_third_party_medical_form')[0].reset();
    $('#visiting_third_party_medical_id').html('').select2();
    $('#multipleThirdPartyMedicalResultId').html('');
    $('#multipleThirdPartyMedicalResultId').attr('style', 'display:none');
    $('#visiting-medical-loader').addClass('d-none');
    thirdPartySelectedArray = [];
    thirdPartySelectedFlag = true;
}
function sendRemoteDocumentByDocumentId(patientId, documentId) {
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        content: 'You want us to send this document to Remote Focus?',
        type: "blue",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        async: false,
                        global: false,
                        type: "POST",
                        url: _SEND_REMOTE_FOCUS_DOCUMENT,
                        data: {
                            '_token': _CSRF_TOKEN,
                            'patient_id': patientId,
                            'documentId': documentId
                        },

                        success: function (response) {
                            toastr.success(response.error_msg);
                            loadDocumentAjaxList();
                        },
                        error: function (jqr) {
                            showErrorAndLoginRedirection(jqr);
                        }
                    })
                }
            },
            cancel: function () {
                //close
            },
        },
    });
}

function sendInflowcareByDocumentId(patientId, documentId) {
    $.confirm({
        title: 'Are you sure?',
        columnClass: "col-md-6",
        content: 'You want to send this document to inflowcare?',
        type: "blue",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        async: false,
                        global: false,
                        type: "POST",
                        url: _SEND_INFLOWCARE_DOCUMENT,
                        data: {
                            '_token': _CSRF_TOKEN,
                            'patient_id': patientId,
                            'documentId': documentId
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);
                            loadDocumentAjaxList();
                        },
                        error: function (jqr) {
                            showErrorAndLoginRedirection(jqr);
                        }
                    })
                }
            },
            cancel: function () {
                //close
            },
        },
    });
}

// Save & Analyse: sends file to AI service first, shows summary in modal, then user confirms to save
var _aiAnalysisJsonResult = null; // stores raw JSON from AI for confirm-save
var _aiIsUploadFlow = false;      // true = Save & Analyse, false = View AI Analysis

$('#documentSaveAnalyse').click(function (e) {
    var datenew_id = $('#datenew_id').val();
    var timemew = $('#timeidnew').val();
    var patient_document_review = $('#patient_document_review').is(":checked");
    var document_approval_user_id = $('#document_approval_user_id').val();
    var medication_list = $('#medication_list:checked').val();
    var insurance_elg = $('#insurance_elg:checked').val();
    var mdo_source = $('#mdo_tag:checked').val() == 1 ? $('#mdo_source').val() : '';

    $('#document_id_error').html("");
    $('#images_error').html("");
    $('#document_approval_user_id_error').html("");
    $('#add_medication_insurance_err').html("");
    $('#add_mdo_tag_err').html("");

    var cnt = 0;
    if (datenew_id.trim() == '') { $('#document_id_error').html("Please enter Document Name"); cnt = 1; }
    if (timemew.trim() == '') {
        $('#images_error').html("Please select Attachment"); cnt = 1;
    } else {
        var fileExtensionType = ['pdf', 'csv', 'xlsx', 'xls', 'docx', 'doc'];
        var files = $('input[name="images"]')[0].files;
        var fileName = files[0].name;
        $('#images_error').html("");
        if ($.inArray(fileName.split('.').pop().toLowerCase(), fileExtensionType) == -1) {
            $('#images_error').html("Please select only pdf or csv file"); cnt = 1;
        }
    }
    if (patient_document_review && document_approval_user_id == "" && _RECORD_TYPE == 'Caregiver') {
        $('#document_approval_user_id_error').html("Please select User"); cnt = 1;
    }
    if (insurance_elg != '' && medication_list != '' && insurance_elg == 1 && medication_list == 1) {
        $('#add_medication_insurance_err').html("Please select any one: Medication List or Insurance Elg."); cnt = 1;
    }
    if ($('#mdo_tag:checked').val() == 1 && mdo_source == "") {
        $('#add_mdo_tag_err').html("MDO Source is required."); cnt = 1;
    }
    if (cnt != 0) return false;

    // Open modal in loading state immediately
    _aiAnalysisJsonResult = null;
    _aiIsUploadFlow = true;
    $('#aiAnalysisDocName').text(datenew_id || 'Document');
    $('#aiAnalysisDocLabelRow').hide();
    $('#aiAnalysisLoading').show();
    $('#aiAnalysisResult').hide();
    $('#aiAnalysisError').hide();
    $('#aiAnalysisConfirmSave').hide();
    $('#aiPatientMismatchAlert').hide();
    $('#aiMismatchDetails').html('');
    $('#aiAnalysisModal').modal('show');

    // Send file to AI service via server-side proxy (avoids CORS)
    var aiFormData = new FormData();
    aiFormData.append('file', $('input[name="images"]')[0].files[0]);
    aiFormData.append('_token', _CSRF_TOKEN);

    $.ajax({
        type: 'POST',
        url: _AI_ANALYSE_PROXY,
        data: aiFormData,
        contentType: false,
        processData: false,
        timeout: 180000,
        success: function (res) {
            $('#aiAnalysisLoading').hide();
            _aiAnalysisJsonResult = res;
            if (typeof res === 'string') {
                renderAiRawText(res);
            } else {
                renderAiAnalysisResult(normalizeAiResponse(res));
            }
            checkPatientMismatch(res);
            $('#aiAnalysisResult').show();
            $('#aiAnalysisConfirmSave').show();
        },
        error: function (jqXHR) {
            $('#aiAnalysisLoading').hide();
            var msg = 'AI analysis failed. You can still save the document.';
            showErrorAndLoginRedirection(jqXHR);
            $('#aiAnalysisErrorMsg').text(msg);
            $('#aiAnalysisError').show();
            $('#aiAnalysisResult').show();
            $('#aiAnalysisConfirmSave').show();
        }
    });
});

// Confirm & Save: Patient → jConfirm after modal closes | others → direct save
$('#aiAnalysisConfirmSave').click(function () {
    if (_RECORD_TYPE == 'Patient') {
        $('#aiAnalysisModal').one('hidden.bs.modal', function () {
            submitPatientData();
        });
        $('#aiAnalysisModal').modal('hide');
    } else {
        $(this).prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving...');
        var formData = new FormData($('#formnew')[0]);
        formData.append('_token', _CSRF_TOKEN);
        formData.append('type', _RECORD_TYPE);
        if (_aiAnalysisJsonResult) {
            formData.append('ai_summary', typeof _aiAnalysisJsonResult === 'string'
                ? _aiAnalysisJsonResult
                : JSON.stringify(_aiAnalysisJsonResult));
        }
        $.ajax({
            type: 'POST',
            url: _PATIENT_DOCUMENT_ADD,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#aiAnalysisConfirmSave').prop('disabled', false).html('<i class="mdi mdi-check mr-1"></i>Confirm & Save');
                _aiAnalysisJsonResult = null;
                toastr.success(res.error_msg);
                $('#aiAnalysisModal').modal('hide');
                $('#exampleModal-5').modal('hide');
                loadDocumentAjaxList();
                closeDocumentSection();
                $('#document_service_id').val("").change();
            },
            error: function (jqXHR) {
                $('#aiAnalysisConfirmSave').prop('disabled', false).html('<i class="mdi mdi-check mr-1"></i>Confirm & Save');
                showErrorAndLoginRedirection(jqXHR);
            }
        });
    }
});

function checkPatientMismatch(res) {
    $('#aiPatientMismatchAlert').hide();
    $('#aiMismatchDetails').html('');

    var info = null;
    if (res && typeof res === 'object') {
        info = res.patient_info || (res.data && res.data.patient_info) || null;
    }
    if (!info) return;

    var mismatches = [];

    // Normalise helpers
    function normName(s) { return (s || '').toLowerCase().replace(/[^a-z]/g, ''); }
    function normPhone(s) { return (s || '').replace(/\D/g, ''); }
    function normDate(s) {
        if (!s) return '';
        var d = new Date(s);
        return isNaN(d) ? (s || '').replace(/\D/g, '') : (d.getFullYear() + ('0' + (d.getMonth() + 1)).slice(-2) + ('0' + d.getDate()).slice(-2));
    }

    // Patient name
    var recName = normName((_FIRST_NAME || '') + (_LAST_NAME || ''));
    var docName = normName(info.name || '');
    if (docName && recName && docName.indexOf(recName) === -1 && recName.indexOf(docName) === -1) {
        mismatches.push({
            field: 'Name',
            patient: ((_FIRST_NAME || '') + ' ' + (_LAST_NAME || '')).trim(),
            document: info.name
        });
    }

    // DOB
    var recDob = normDate(typeof _DOB !== 'undefined' ? _DOB : '');
    var docDob = normDate(info.dob || '');
    if (docDob && recDob && docDob !== recDob) {
        // Format patient DOB as MM/DD/YYYY for display
        var recDobDisplay = (function () {
            var raw = typeof _DOB !== 'undefined' ? _DOB : '';
            if (!raw) return '-';
            var d = new Date(raw);
            if (isNaN(d)) return raw;
            return ('0' + (d.getMonth() + 1)).slice(-2) + '/' + ('0' + d.getDate()).slice(-2) + '/' + d.getFullYear();
        })();
        mismatches.push({
            field: 'Date of Birth',
            patient: recDobDisplay,
            document: info.dob
        });
    }

    // Mobile
    var recMobile = normPhone(typeof _MOBILE !== 'undefined' ? _MOBILE : '');
    var docMobile = normPhone(info.mobile || '');
    if (docMobile && recMobile && docMobile.length >= 7 && recMobile.length >= 7 && docMobile !== recMobile) {
        mismatches.push({
            field: 'Mobile',
            patient: typeof _MOBILE !== 'undefined' ? _MOBILE : '-',
            document: info.mobile
        });
    }

    if (!mismatches.length) return;

    var rows = mismatches.map(function (m) {
        return '<div style="display:flex; gap:8px; align-items:flex-start; margin-bottom:6px;">'
            + '<span style="background:#e65100;color:#fff;border-radius:4px;font-size:11px;font-weight:700;padding:2px 8px;min-width:90px;text-align:center;flex-shrink:0;">' + m.field + '</span>'
            + '<div style="flex:1;">'
            + '<span style="color:#555;font-size:12px;">Patient record: </span><strong style="color:#1a1a1a;">' + $('<div>').text(m.patient).html() + '</strong>'
            + ' &nbsp;<i class="mdi mdi-arrow-right" style="color:#aaa;"></i>&nbsp; '
            + '<span style="color:#555;font-size:12px;">Document: </span><strong style="color:#c0392b;">' + $('<div>').text(m.document).html() + '</strong>'
            + '</div></div>';
    }).join('');

    $('#aiMismatchDetails').html(rows);
    $('#aiMismatchSaveNote').toggle(_aiIsUploadFlow);
    $('#aiPatientMismatchAlert').show();
}

function renderAiRawText(text) {
    var lines = text.split('\n');
    var sections = [];
    var currentSection = null;
    var currentRows = [];

    // Known section header keywords
    var sectionHeaders = ['PATIENT', 'SPECIMEN', 'CLIENT', 'TEST RESULTS', 'NOTES', 'NOTES & COMMENTS',
        'PERFORMING SITE', 'REPORT STATUS', 'LABORATORY REPORT', 'RESULTS', 'DIAGNOSIS',
        'MEDICATIONS', 'ASSESSMENT', 'PLAN', 'SUBJECTIVE', 'OBJECTIVE', 'IMPRESSION', 'FINDINGS'];

    function isHeader(line) {
        var t = line.trim().toUpperCase();
        for (var i = 0; i < sectionHeaders.length; i++) {
            if (t === sectionHeaders[i] || t.indexOf(sectionHeaders[i]) === 0) return true;
        }
        // All-caps short lines are likely headers
        return /^[A-Z][A-Z\s&\/\-]{2,40}$/.test(line.trim());
    }

    function isKeyValue(line) {
        return /^(.+?)\s{2,}(.+)$/.test(line.trim()) || /^(.+?):\s+(.+)$/.test(line.trim());
    }

    function parseKeyValue(line) {
        var m = line.trim().match(/^(.+?)\s{2,}(.+)$/) || line.trim().match(/^(.+?):\s+(.+)$/);
        return m ? { key: m[1].trim(), val: m[2].trim() } : null;
    }

    function esc(str) { return $('<div>').text(str).html(); }

    function flushSection() {
        if (!currentSection && currentRows.length === 0) return;
        var block = '<div class="card mb-3" style="border:1px solid #e9ecef;border-radius:8px;overflow:hidden;">';
        if (currentSection) {
            block += '<div class="card-header py-2 px-3" style="background:#f8f9fa;border-bottom:1px solid #e9ecef;">' +
                '<small class="text-uppercase font-weight-bold" style="color:#667eea;letter-spacing:1px;">' + esc(currentSection) + '</small></div>';
        }
        if (currentRows.length > 0) {
            // Check if it looks like a table (3+ columns detected in any row)
            var looksLikeTable = currentRows.some(function (r) {
                return r.type === 'table';
            });
            block += '<div class="card-body py-2 px-3">';
            currentRows.forEach(function (r) {
                if (r.type === 'kv') {
                    block += '<div class="d-flex justify-content-between py-1" style="border-bottom:1px solid #f1f3f5;">' +
                        '<span style="color:#868e96;font-size:13px;">' + esc(r.key) + '</span>' +
                        '<span style="font-weight:500;font-size:13px;">' + esc(r.val) + '</span></div>';
                } else if (r.type === 'table') {
                    block += '<div class="d-flex py-1" style="border-bottom:1px solid #f1f3f5;">' +
                        r.cols.map(function (c, i) {
                            var color = (c.toUpperCase() === 'NEGATIVE') ? '#28a745' : (c.toUpperCase() === 'POSITIVE' ? '#dc3545' : '');
                            var style = 'font-size:13px;flex:1;' + (color ? 'color:' + color + ';font-weight:600;' : '') + (i === 0 ? 'font-weight:500;' : '');
                            return '<span style="' + style + '">' + esc(c) + '</span>';
                        }).join('') + '</div>';
                } else if (r.type === 'heading') {
                    block += '<p class="mb-1 mt-2" style="font-weight:600;font-size:14px;">' + esc(r.text) + '</p>';
                } else {
                    block += '<p class="mb-1" style="font-size:13px;color:#495057;line-height:1.6;">' + esc(r.text) + '</p>';
                }
            });
            block += '</div>';
        }
        block += '</div>';
        sections.push(block);
        currentSection = null;
        currentRows = [];
    }

    lines.forEach(function (line) {
        var t = line.trim();
        if (t === '') return;

        if (isHeader(t)) {
            flushSection();
            currentSection = t;
        } else {
            // Try to detect columns (2+ consecutive spaces splitting into 3 parts = table row)
            var cols = t.split(/\s{3,}/);
            if (cols.length >= 3) {
                currentRows.push({ type: 'table', cols: cols });
            } else if (cols.length === 2 || isKeyValue(t)) {
                var kv = parseKeyValue(t);
                if (kv) {
                    currentRows.push({ type: 'kv', key: kv.key, val: kv.val });
                } else {
                    currentRows.push({ type: 'text', text: t });
                }
            } else if (/^[A-Z][^a-z]{0,}$/.test(t) && t.length < 60) {
                currentRows.push({ type: 'heading', text: t });
            } else {
                currentRows.push({ type: 'text', text: t });
            }
        }
    });
    flushSection();

    var html = sections.length > 0
        ? sections.join('')
        : '<p class="text-muted">No content to display.</p>';

    $('#aiAnalysisContent').css({ 'background': 'transparent', 'padding': '0' }).html(html);
}

// Renderer for the ngrok /generate_json API response
function renderNgrokAnalysis(data) {
    function esc(v) { return $('<div>').text(String(v == null ? '' : v)).html(); }

    function label(text) {
        return '<div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;letter-spacing:.5px;margin-bottom:4px;">' + esc(text) + '</div>';
    }

    function panel(title, content) {
        return '<div style="background:#fff;border:1px solid #e1e6eb;border-radius:8px;margin-bottom:14px;overflow:hidden;">'
            + '<div style="background:#f8fafc;border-bottom:1px solid #e1e6eb;padding:10px 16px;">'
            + '<span style="font-size:14px;font-weight:700;color:#1e293b;">' + esc(title) + '</span></div>'
            + '<div style="padding:14px 16px;">' + content + '</div></div>';
    }

    function ratingBadge(val) {
        if (!val) return '';
        var v = String(val).toLowerCase();
        var colors = {
            normal: { bg: '#f0fdf4', border: '#86efac', text: '#166534' },
            high: { bg: '#fff7ed', border: '#fdba74', text: '#9a3412' },
            low: { bg: '#eff6ff', border: '#93c5fd', text: '#1e40af' },
            critical: { bg: '#fef2f2', border: '#fca5a5', text: '#991b1b' },
            abnormal: { bg: '#fff7ed', border: '#fdba74', text: '#9a3412' },
        };
        var matched = null;
        Object.keys(colors).forEach(function (k) { if (v.indexOf(k) !== -1) matched = colors[k]; });
        var c = matched || { bg: '#f1f5f9', border: '#cbd5e1', text: '#475569' };
        return '<span style="background:' + c.bg + ';border:1px solid ' + c.border + ';color:' + c.text
            + ';border-radius:6px;font-size:12px;font-weight:700;padding:3px 10px;text-transform:capitalize;">' + esc(val) + '</span>';
    }

    function kvRow(key, val) {
        return '<div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f1f5f9;">'
            + '<span style="color:#6b7280;font-size:13px;">' + esc(formatKey(key)) + '</span>'
            + '<span style="font-weight:600;font-size:13px;color:#1e293b;text-align:right;max-width:60%;">' + esc(val) + '</span></div>';
    }

    function formatKey(k) {
        return String(k).replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }

    function renderObject(obj) {
        var html = '';
        Object.keys(obj).forEach(function (k) {
            var v = obj[k];
            if (v === null || v === undefined || v === '') return;
            if (typeof v === 'object' && !Array.isArray(v)) {
                html += '<div style="margin-bottom:10px;">' + label(formatKey(k)) + renderObject(v) + '</div>';
            } else if (Array.isArray(v)) {
                html += '<div style="margin-bottom:10px;">' + label(formatKey(k));
                if (v.length === 0) { html += '<span style="color:#9ca3af;font-size:13px;">-</span>'; }
                else if (typeof v[0] === 'object') {
                    html += renderTable(v);
                } else {
                    html += '<div style="display:flex;flex-wrap:wrap;gap:6px;">'
                        + v.map(function (i) {
                            return '<span style="background:#eef2f7;border:1px solid #d7dee8;border-radius:6px;color:#1f2937;font-size:12px;padding:3px 9px;">' + esc(i) + '</span>';
                        }).join('') + '</div>';
                }
                html += '</div>';
            } else {
                html += kvRow(k, v);
            }
        });
        return html;
    }

    function renderTable(rows) {
        if (!rows || !rows.length) return '<p style="color:#9ca3af;font-size:13px;">-</p>';
        var keys = Object.keys(rows[0]);
        var tbl = '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:13px;">'
            + '<thead><tr>' + keys.map(function (k) {
                return '<th style="background:#f8fafc;border:1px solid #e1e6eb;padding:7px 10px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;white-space:nowrap;">' + esc(formatKey(k)) + '</th>';
            }).join('') + '</tr></thead><tbody>';
        rows.forEach(function (row, i) {
            tbl += '<tr style="background:' + (i % 2 === 0 ? '#fff' : '#f8fafc') + ';">';
            keys.forEach(function (k) {
                var v = row[k];
                tbl += '<td style="border:1px solid #e1e6eb;padding:7px 10px;color:#1e293b;">' + esc(v == null ? '-' : v) + '</td>';
            });
            tbl += '</tr>';
        });
        tbl += '</tbody></table></div>';
        return tbl;
    }

    var html = '';

    // Top-level scalar fields → summary card
    var summaryRows = '';
    var topLevelObjects = {};
    Object.keys(data).forEach(function (k) {
        var v = data[k];
        if (v === null || v === undefined || v === '') return;
        if (typeof v === 'object') {
            topLevelObjects[k] = v;
        } else {
            if (k === 'turnaround_rating') {
                summaryRows += '<div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f1f5f9;">'
                    + '<span style="color:#6b7280;font-size:13px;">' + esc(formatKey(k)) + '</span>'
                    + '<span>' + ratingBadge(v) + '</span></div>';
            } else {
                summaryRows += kvRow(k, v);
            }
        }
    });

    if (summaryRows) {
        html += panel('Summary', summaryRows);
    }

    // Each top-level object → its own panel
    Object.keys(topLevelObjects).forEach(function (k) {
        var v = topLevelObjects[k];
        var content = '';
        if (Array.isArray(v)) {
            content = v.length && typeof v[0] === 'object' ? renderTable(v) : renderObject({ items: v });
        } else {
            content = renderObject(v);
        }
        html += panel(formatKey(k), content);
    });

    if (!html) html = '<p style="color:#9ca3af;">No data returned.</p>';
    $('#aiAnalysisContent').css({ background: 'transparent', padding: '0' }).html(html);
}

// Map Bedrock document_type string → internal doc_type used by renderAiAnalysisResult
function resolveDocType(raw) {
    var map = {
        'lab_report': 'lab',
        'toxicology': 'lab',
        'imaging': 'xray',
        'medical_form': 'soap',
        'discharge': 'soap',
        'prescription': 'other',
        'other': 'other',
        'lab': 'lab',
        'xray': 'xray',
        'soap': 'soap',
    };
    return map[(raw || '').toLowerCase()] || 'soap';
}

// Normalize nested Bedrock response shape → flat shape expected by renderAiAnalysisResult
function normalizeAiResponse(data) {
    if (!data || typeof data !== 'object') return data;

    // Resolve document type — if new prompt fields exist use them, else try legacy keys
    var rawType = data.document_type || data.doc_type || '';
    var docType = rawType ? resolveDocType(rawType) : 'soap';

    var out = { doc_type: docType };

    // New prompt fields (short_summary / overall_result / document_label)
    out.short_summary = data.short_summary || '';
    out.overall_result = data.overall_result || '';
    out.document_label = data.document_label || '';
    out.red_flags = data.red_flags || [];
    out.document_name = data.document_name || '';
    out.patient_info = data.patient_info || null;

    // soap_note → flat fields (present on medical_form / discharge)
    if (data.soap_note && typeof data.soap_note === 'object') {
        out.soap_subjective = data.soap_note.subjective || data.soap_note.Subjective || '';
        out.soap_objective = data.soap_note.objective || data.soap_note.Objective || '';
        out.soap_assessment = data.soap_note.assessment || data.soap_note.Assessment || '';
        out.soap_plan = data.soap_note.plan || data.soap_note.Plan || '';
    } else if (data.soap_subjective) {
        // Already flat
        out.soap_subjective = data.soap_subjective;
        out.soap_objective = data.soap_objective || '';
        out.soap_assessment = data.soap_assessment || '';
        out.soap_plan = data.soap_plan || '';
    }

    // highlight_summary → flat fields
    var hs = data.highlight_summary || {};
    out.highlight_diagnoses = hs.diagnoses || hs.Diagnoses || data.highlight_diagnoses || [];
    out.highlight_symptoms = hs.symptoms || hs.Symptoms || data.highlight_symptoms || [];
    out.highlight_medications = hs.medications || hs.Medications || data.highlight_medications || [];
    out.highlight_treatment_updates = hs.treatment_updates || hs.Treatment_updates || data.highlight_treatment_updates || [];
    out.clinical_advice = data.clinical_advice || [];

    return out;
}

function renderAiAnalysisResult(data) {
    function esc(v) { return $('<div>').text(v || '').html(); }
    function toArr(v) {
        if (Array.isArray(v)) return v;
        if (typeof v === 'string' && v.trim()) { try { return JSON.parse(v); } catch (e) { return []; } }
        return [];
    }

    var docType = data.doc_type || 'soap';
    var typeColor = { soap: '#0d6efd', lab: '#6f42c1', xray: '#fd7e14' }[docType] || '#6c757d';
    var html = '';

    // Colour palettes matching show.blade.php
    var palette = {
        red: { bg: '#fef2f2', border: '#fca5a5', text: '#991b1b', dot: '#dc2626' },
        orange: { bg: '#fff7ed', border: '#fdba74', text: '#9a3412', dot: '#ea580c' },
        yellow: { bg: '#fefce8', border: '#fde047', text: '#854d0e', dot: '#ca8a04' },
        green: { bg: '#f0fdf4', border: '#86efac', text: '#166534', dot: '#16a34a' },
        blue: { bg: '#eff6ff', border: '#93c5fd', text: '#1e40af', dot: '#2563eb' },
        gray: { bg: '#f8fafc', border: '#e1e6eb', text: '#1e293b', dot: '#94a3b8' }
    };

    // Keyword → colour for lab summary lines (matches show.blade.php getLineStyle)
    var summaryKeywords = [
        ['critical', palette.red],
        ['attention', palette.red],
        ['flagged', palette.red],
        ['severe', palette.red],
        ['high', palette.orange],
        ['abnormal', palette.orange],
        ['elevated', palette.orange],
        ['moderate', palette.orange],
        ['borderline', palette.yellow],
        ['mild', palette.yellow],
        ['low', palette.blue],
        ['normal', palette.green],
        ['within', palette.green],
    ];

    function getLineColor(line) {
        var lower = line.toLowerCase();
        for (var i = 0; i < summaryKeywords.length; i++) {
            if (lower.indexOf(summaryKeywords[i][0]) !== -1) return summaryKeywords[i][1];
        }
        return null;
    }

    function panel(title, content) {
        return '<div style="background:#fff;border:1px solid #e1e6eb;border-radius:6px;margin-bottom:14px;padding:16px;">'
            + '<h6 style="font-size:15px;font-weight:700;margin-bottom:12px;">' + title + '</h6>'
            + content + '</div>';
    }

    function pills(items, type) {
        var list = toArr(items);
        if (!list.length) return '<span style="color:#6b7280;font-size:13px;">-</span>';
        var s = type === 'redflag' ? { bg: '#fff7ed', border: '#fdba74', color: '#9a3412' }
            : type === 'critical' ? { bg: '#fef2f2', border: '#fca5a5', color: '#991b1b' }
                : { bg: '#eef2f7', border: '#d7dee8', color: '#1f2937' };
        return '<div style="display:flex;flex-wrap:wrap;gap:6px;">'
            + list.map(function (v) {
                return '<span style="background:' + s.bg + ';border:1px solid ' + s.border + ';border-radius:6px;color:' + s.color + ';font-size:12px;padding:4px 8px;display:inline-block;">' + esc(typeof v === 'object' ? JSON.stringify(v) : v) + '</span>';
            }).join('') + '</div>';
    }

    // Highlight row with full palette colour (matching show.blade.php coloured cards)
    function hlSection(label, items, c) {
        var list = toArr(items);
        if (!list.length) return '<div style="margin-bottom:12px;"><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">' + label + '</div><span style="color:#6b7280;font-size:13px;">-</span></div>';
        return '<div style="margin-bottom:12px;">'
            + '<div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">' + label + '</div>'
            + '<div style="display:flex;flex-direction:column;gap:6px;">'
            + list.map(function (v) {
                return '<div style="background:' + c.bg + ';border:1px solid ' + c.border + ';border-radius:6px;padding:8px 12px;display:flex;align-items:flex-start;gap:10px;">'
                    + '<span style="width:8px;height:8px;border-radius:50%;background:' + c.dot + ';flex-shrink:0;margin-top:5px;"></span>'
                    + '<span style="color:' + c.text + ';font-size:13px;line-height:1.6;">' + esc(typeof v === 'object' ? JSON.stringify(v) : v) + '</span></div>';
            }).join('') + '</div></div>';
    }

    function col2(inner) {
        return '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">' + inner + '</div>';
    }

    // Lab summary: split into lines and colour by keyword
    function renderLabSummary(text) {
        if (!text) return '';
        var lines = text.split(/\r\n|\r|\n|(?<=\.)\s+(?=[A-Z])/).map(function (l) { return l.trim(); }).filter(Boolean);
        var out = '<div style="display:flex;flex-direction:column;gap:8px;">';
        lines.forEach(function (line) {
            var c = getLineColor(line) || palette.gray;
            out += '<div style="background:' + c.bg + ';border:1px solid ' + c.border + ';border-radius:6px;padding:10px 14px;display:flex;align-items:flex-start;gap:10px;">'
                + '<span style="width:8px;height:8px;border-radius:50%;background:' + c.dot + ';flex-shrink:0;margin-top:6px;"></span>'
                + '<span style="color:' + c.text + ';font-size:14px;line-height:1.6;">' + esc(line) + '</span></div>';
        });
        out += '</div>';
        return out;
    }

    // Show AI-identified document label in modal info bar (above #aiAnalysisContent)
    if (data.document_label) {
        $('#aiAnalysisDocLabel').text(data.document_label);
        $('#aiAnalysisDocLabelRow').show();
    } else {
        $('#aiAnalysisDocLabelRow').hide();
    }

    // Short summary + overall result badge — shown for ALL document types
    var resultVal = (data.overall_result || '').trim();
    var resultLower = resultVal.toLowerCase();
    var badgeBg = resultLower === 'negative' || resultLower === 'normal' ? '#16a34a'
        : resultLower === 'positive' || resultLower === 'abnormal' ? '#dc2626'
            : resultLower === 'mixed' ? '#d97706'
                : resultLower === 'n/a' ? '#6c757d' : '#0d6efd';

    if (data.short_summary || resultVal) {
        html += '<div style="background:#f8fafc;border:1px solid #e1e6eb;border-radius:8px;padding:16px 18px;margin-bottom:16px;display:flex;align-items:flex-start;gap:14px;">'
            + '<div style="flex:1;">'
            + (data.short_summary ? '<p style="margin:0;font-size:14px;color:#1e293b;line-height:1.7;">' + esc(data.short_summary) + '</p>' : '')
            + '</div>'
            + (resultVal && resultLower !== 'n/a' ? '<div style="flex-shrink:0;text-align:center;">'
                + '<span style="display:inline-block;background:' + badgeBg + ';color:#fff;border-radius:6px;font-size:13px;font-weight:700;padding:6px 16px;letter-spacing:.3px;">'
                + esc(resultVal) + '</span>'
                + '<div style="font-size:10px;color:#6b7280;margin-top:4px;text-transform:uppercase;letter-spacing:.5px;">Overall Result</div>'
                + '</div>' : '')
            + '</div>';
    }

    // Red flags — shown for ALL document types if present
    var rfList = toArr(data.red_flags);
    if (rfList.length) {
        html += '<div style="background:#fff7ed;border:1px solid #fdba74;border-radius:8px;padding:12px 16px;margin-bottom:16px;">'
            + '<div style="font-size:12px;font-weight:700;color:#9a3412;margin-bottom:8px;"><i class="fa fa-exclamation-triangle mr-1"></i> Flags / Abnormal Findings</div>'
            + '<div style="display:flex;flex-wrap:wrap;gap:6px;">'
            + rfList.map(function (v) {
                return '<span style="background:#fef2f2;border:1px solid #fca5a5;border-radius:6px;color:#991b1b;font-size:12px;padding:4px 10px;">' + esc(typeof v === 'object' ? JSON.stringify(v) : v) + '</span>';
            }).join('') + '</div></div>';
    }

    // For lab/tox/imaging/prescription/other: summary + result + flags is sufficient — stop here
    if (docType !== 'soap') {
        if (!html.trim()) html = '<p style="color:#6b7280;font-size:13px;">Analysis complete. No summary data returned.</p>';
        $('#aiAnalysisContent').css({ background: 'transparent', padding: '0' }).html(html);
        return;
    }

    // ---- SOAP (medical_form / discharge) ----
    if (docType === 'soap') {
        var soapColors = { Subjective: '#0d6efd', Objective: '#6f42c1', Assessment: '#fd7e14', Plan: '#198754' };
        var soapInner = '';
        [['Subjective', data.soap_subjective], ['Objective', data.soap_objective],
        ['Assessment', data.soap_assessment], ['Plan', data.soap_plan]].forEach(function (s) {
            var color = soapColors[s[0]] || '#6c757d';
            soapInner += '<div style="display:flex;gap:0;border:1px solid #e1e6eb;border-radius:8px;overflow:hidden;margin-bottom:10px;">'
                + '<div style="background:' + color + ';width:120px;min-width:120px;display:flex;align-items:center;justify-content:center;padding:14px 10px;">'
                + '<span style="color:#fff;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;writing-mode:horizontal-tb;">' + s[0] + '</span></div>'
                + '<div style="flex:1;padding:14px 16px;background:#fff;color:#334155;font-size:13px;line-height:1.7;white-space:pre-wrap;">' + esc(s[1] || '-') + '</div></div>';
        });
        html += panel('SOAP Note', soapInner);

        // Highlights: 6 sections, 2 per col, each with palette colour matching show.blade.php
        var col1 = hlSection('Diagnoses', data.highlight_diagnoses, palette.blue)
            + hlSection('Symptoms', data.highlight_symptoms, palette.yellow)
            + hlSection('Red Flags', data.red_flags, palette.red);
        var col2h = hlSection('Medications', data.highlight_medications, palette.green)
            + hlSection('Treatment Updates', data.highlight_treatment_updates, palette.blue)
            + hlSection('Clinical Advice', data.clinical_advice, palette.green);
        html += panel('Highlights', col2('<div>' + col1 + '</div><div>' + col2h + '</div>'));
    }

    // ---- LAB ----
    if (docType === 'lab') {
        if (data.lab_summary) {
            html += panel('Lab Summary', renderLabSummary(data.lab_summary));
        }

        var criticals = toArr(data.lab_critical_values);
        if (criticals.length) { html += panel('Critical Values', pills(criticals, 'critical')); }

        var results = toArr(data.lab_results);
        if (results.length) {
            var tbl = '<div class="table-responsive"><table class="table table-bordered table-sm" style="font-size:13px;margin-bottom:0;">'
                + '<thead class="thead-light"><tr><th>Test</th><th>Result</th><th>Unit</th><th>Reference Range</th><th>Flag</th></tr></thead><tbody>';
            results.forEach(function (r) {
                var flag = (r.flag || '').toUpperCase();
                var fc = { H: '#b45309', L: '#1d4ed8', C: '#dc2626', N: '#15803d' }[flag] || '#6b7280';
                var fw = (flag === 'C' || flag === 'H' || flag === 'L') ? '800' : '700';
                var fl = { H: 'High', L: 'Low', C: 'Critical', N: 'Normal' }[flag] || (flag || 'Normal');
                tbl += '<tr><td>' + esc(r.test || '-') + '</td><td><strong>' + esc(r.value || '-') + '</strong></td>'
                    + '<td>' + esc(r.unit || '-') + '</td><td>' + esc(r.range || '-') + '</td>'
                    + '<td style="color:' + fc + ';font-weight:' + fw + ';">' + fl + '</td></tr>';
            });
            tbl += '</tbody></table></div>';
            html += panel('Test Results', tbl);
        }

        var insCol1 = '<div><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">Diagnoses</div>' + pills(data.highlight_diagnoses) + '</div>'
            + '<div style="margin-top:12px;"><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">Medications</div>' + pills(data.highlight_medications) + '</div>';
        var insCol2 = '<div><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">Follow-up</div>' + pills(data.highlight_treatment_updates) + '</div>'
            + '<div style="margin-top:12px;"><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">Clinical Advice</div>' + pills(data.clinical_advice) + '</div>';
        html += panel('Clinical Insights', col2(insCol1 + insCol2));

        var rf = toArr(data.red_flags);
        if (rf.length) { html += panel('Red Flags', pills(rf, 'redflag')); }
    }

    // ---- XRAY ----
    if (docType === 'xray') {
        function xBlock(v) { return '<div style="background:#f8fafc;border:1px solid #e1e6eb;border-radius:6px;padding:14px;white-space:pre-wrap;line-height:1.7;font-size:13px;color:#1e293b;">' + esc(v || '-') + '</div>'; }
        html += panel('Imaging Findings', xBlock(data.xray_findings));
        html += panel('Impression', xBlock(data.xray_impression));
        var xrecs = toArr(data.xray_recommendations);
        if (xrecs.length) { html += panel('Recommendations', pills(xrecs)); }

        var xiCol1 = '<div><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">Diagnoses</div>' + pills(data.highlight_diagnoses) + '</div>'
            + '<div style="margin-top:12px;"><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">Symptoms</div>' + pills(data.highlight_symptoms) + '</div>';
        var xiCol2 = '<div><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">Clinical Advice</div>' + pills(data.clinical_advice) + '</div>'
            + '<div style="margin-top:12px;"><div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">Red Flags</div>' + pills(data.red_flags, 'redflag') + '</div>';
        html += panel('Clinical Insights', col2(xiCol1 + xiCol2));
    }

    if (!html.trim()) html = '<p class="text-muted">Analysis complete. No structured data extracted.</p>';
    $('#aiAnalysisContent').css({ background: 'transparent', padding: '0' }).html(html);
}

// Called from Action dropdown — checks DB ai_summary first, then calls AI service if missing
function openAiAnalysisForDoc(docId, docName) {
    _aiIsUploadFlow = false;
    $('#aiAnalysisDocName').text(docName || 'Document #' + docId);
    $('#aiAnalysisDocLabelRow').hide();
    $('#aiAnalysisLoading').show();
    $('#aiAnalysisResult').hide();
    $('#aiAnalysisError').hide();
    $('#aiAnalysisConfirmSave').hide();
    $('#aiPatientMismatchAlert').hide();
    $('#aiMismatchDetails').html('');
    $('#aiAnalysisModal').modal('show');

    // Step 1: check DB for saved ai_summary
    $.ajax({
        type: 'GET',
        url: _AI_ANALYSE_BY_DOC + '/' + docId + '/ai-summary',
        success: function (res) {
            if (res.success && res.has_summary && res.data) {
                $('#aiAnalysisLoading').hide();
                if (typeof res.data === 'string') {
                    renderAiRawText(res.data);
                } else {
                    renderAiAnalysisResult(normalizeAiResponse(res.data));
                }
                $('#aiAnalysisResult').show();
            } else {
                // No saved summary — run AI analysis
                triggerAndShowAnalysisForDoc(docId);
            }
        },
        error: function () {
            triggerAndShowAnalysisForDoc(docId);
        }
    });
}

function triggerAndShowAnalysisForDoc(docId) {
    $('#aiAnalysisLoading').show();
    $('#aiAnalysisResult').hide();
    $('#aiAnalysisError').hide();

    $.ajax({
        type: 'POST',
        url: _AI_ANALYSE_BY_DOC + '/' + docId + '/ai-analyse',
        data: { _token: _CSRF_TOKEN },
        timeout: 180000,
        success: function (res) {
            $('#aiAnalysisLoading').hide();
            if (typeof res === 'string') {
                renderAiRawText(res);
            } else {
                renderAiAnalysisResult(normalizeAiResponse(res));
            }
            $('#aiAnalysisResult').show();
        },
        error: function (jqXHR) {
            $('#aiAnalysisLoading').hide();
            var msg = 'AI analysis failed.';
            // showErrorAndLoginRedirection(jqXHR);
            $('#aiAnalysisErrorMsg').text(msg);
            $('#aiAnalysisError').show();
            $('#aiAnalysisResult').show();
        }
    });
}
