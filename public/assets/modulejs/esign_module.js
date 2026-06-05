function loadTemplate() {
    $.ajax({

        url: _LOAD_ESIGN_TEMPLATE,
        type: "GET",
        data: {
            'agency_id': _AGENCYID,
            'type':_RECORD_TYPE
        },
        success: function (res) {
            var json = res.data;
            $('#template_idold').html("");
            var option = "";
            option = '<option value="">Select Template</option>';
            if (json.length != 0) {

                $.each(json, function (i, v) {
                    option += '<option value="' + v.id + '">' + v.template_name + '</option>';
                })
            }

            $('#template_idold').html(option);

        }
    })
}

function loadDoctorList() {
    $.ajax({
        url: _LOAD_DOCTOR_LIST,
        type: "GET",
        data: '',
        success: function (res) {
            var json = res.data;
            $('#doctor_id').html("");
            var option = "";
            option = '<option value="">Select Doctor</option>';
            if (json.length != 0) {

                $.each(json, function (i, v) {
                    option += '<option value="' + v.id + '">' + v.full_name + '</option>';
                })
            }

            $('#doctor_id').html(option);

        }
    })
}

$('#edit_template_modal_submit').click(function (e) {
    $('#edit_template_modal_submit').hide();
    var eid = $('#temp1').val();
    var id = $('#template_idold').val();
    var doctorId = $('#doctor_id').val();

    cnt = 0;
    if (id == '') {
        $('#change_templateold_error').html("Please select Template");
        cnt = 1;
    }
    if (_RECORD_TYPE == 'Patient') {
        if (!doctorId) {
            $('#doctor_id_error').html("Please select Doctor");
            cnt = 1;
        }
    }
    if (id != '') {

        $.ajax({
            async: false,
            global: false,
            url: esignAllocateSigner,
            type: "get",
            data: {
                'template_id': id,
                '_token': _CSRF_TOKEN
            },
            success: function (response) {

                if (response == 1) {

                    cnt = 0;
                } else {
                    $('#change_template_error').html("Please select signer.");
                    cnt = 1;

                }
            }
        });


    }

    if (cnt == 1) {
        $('#edit_template_modal_submit').show();
        return false;
    } else {
        e.preventDefault();

        var forms = $('#edit_template_modal')[0];
        var newData = new FormData(forms);
        newData.append('_token', _CSRF_TOKEN);
        $.ajax({

            url: saveEsignTemplate,
            type: "POST",
            data: newData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.error_msg);
                esignResponseNew();
                $('#edit_template_modal')[0].reset();
                $('#closed_id_esign').click();

                $('#edit_template_modal_submit').show();

            },
            error: function (xhr, status, error) {
                $('#edit_template_modal_submit').show();
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }
})

function esignResponseNew() {
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
                    if (v.signerRemaining == 0) {
                        var status = '<label class="badge badge-success">Completed</label>';
                        var moveToDocument = '<a data-toggle="modal" href="javascript:void(0)" data-group-id="' + v.groupId + '" data-templete-id="' + v.templete_id + '" data-type="Esign" data-target="#esignMoveDocumentModal-1" data-whatever="@mdo" onclick="viewServices();requestsServices();" class="dropdown-item" data-agency-form-id="' + v.agency_form_id + '" data-esign-doc-id="' + v.id + '">Move To Document</a>';
                    } else {
                        var status = '<label class="badge badge-warning">Pending</label>';
                    }

                    var groupId = "'" + v.groupId + "'";
                    var sendSMSOption = '';

                    if (v.pdf_generate == '' || v.pdf_generate == null) {

                    }


                    var viewSigner = '';

                    if (v.signerRemaining != 0 && v.templete_id != 0) {
                        viewSigner = '<a href="#" onclick="getSigner(' + groupId + ',' + v.id + ',' + v.main_intakeId + ')" class="dropdown-item">View</a>';
                        sendSMSOption = '<a href="javascript:void(0)" onclick="getSendSMSNew(' + groupId + ')" class="dropdown-item">Send SMS</a>';
                    }
                    var deleteOption = '';
                    if (v.status == 'Pending') {
                        var deleteOption = '<a href="javascript:void(0)" onclick="getDeleteEsignTemplate(' + groupId + ')" class="dropdown-item">Delete</a>';
                    }

                    var showDropdown = '';
                    var showPdfOption = '';

                    if (v.pdf_generate != "" && v.pdf_generate != null && v.templete_id != 0) {
                        var url = _BASE_URL + '/dre/' + v.groupId;
                        showPdfOption = `<a 
				        href="${url}" class="dropdown-item">File</a>`;
                    }

                    var editPdf = '';
                    if(v.status == 'Pending' && v.templete_id == 0){
                        var docUrl = _BASE_URL + '/esign/write-document/' + '?id=' + v.id;
                        var editPdf =`<a class="dropdown-item" href="${docUrl}" target="_blank" title="Edit Pdf">Add Sign/Stamp</a>`;
                    }
                   
                    var downloadWriteDocument = '';
                    if(v.status == 'Completed' && v.templete_id == 0){
                        var downloadWriteDocumentUrl = _BASE_URL + '/dre-write-document/'+ v.id;
                        var downloadWriteDocument = `<a target="_blank" href="${downloadWriteDocumentUrl}" class="dropdown-item">Download</a>`;    
                    }
                    
                    // var action = deleteOption + ' ' + showPdfOption + ' ' + viewSigner + ' ' + sendSMSOption + ' ' + '<a href="javascript:void(0)" onclick="esignHistory(' + v.id + ',' + v.main_intakeId + ')"><i class="fa fa-info" aria-hidden="true"></i></a>' + moveToDocument;

                    var actionButton = `<div class="btn-group pull-right status-dropdoown mr-2">
                                                <button type="button" class="btn btn-warning"
                                                    title="Action">Action</button>
                                                <button type="button"
                                                    class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                                    id="dropdownMenuSplitButton6" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                                    ${deleteOption + ' ' + showPdfOption + ' ' + viewSigner + ' ' + sendSMSOption + ' ' + '<a href="javascript:void(0)" onclick="esignHistory(' + v.id + ',' + v.main_intakeId + ')" class="dropdown-item">Esign History</a>' + ' ' + moveToDocument + editPdf + downloadWriteDocument}
                                                </div>
                                            </div>`;

                    var action = actionButton;

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
                    <td>${status}</td>
                    <td>${v.user_details.first_name + ' ' + v.user_details.last_name}</td>
                    <td>${v.completed_on}</td>
                    <td>${v.created_date}</td>
                    <td>${v.user_details.first_name + ' ' + v.user_details.last_name}</td>
                    <td style="overflow: unset !important">${action}</td>
                    </tr>`
                })
            } else {
                tableResponse = '<tr><td colspan="8">No record available</td></tr>'
            }

            $('#esign_resp_id').html("")
            $('#esign_resp_id').html(tableResponse)
        }

    })
}

function getSigner(gid, document_id, enrollId) {
    $('#loaderdocument').attr('style','display:block');
   
    $.ajax({
        async: false, 
        global: false,
        url: _GET_ALLOCATED_SIGNER + "?groupId=" + gid + "&document_id=" +
            document_id + "&enrollment_id=" + enrollId,
        success: function (response) {
            var json  = response.data;
            console.log(json,'json');
           var responseHtml = "";
            if(json.length !=0){
                $.each(json,function(i,v){
                   var name = "";
                   if (v.sent_on.toLowerCase() == 'caregiver') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase()  == 'staff') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase()  == 'officestaff') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase()  == 'other') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase()  == 'stampuser') {
                        name =v.sent_on;
                    }
                    if (v.sent_on.toLowerCase()  == 'patient') {
                        name = v.sent_on;
                    }
                    if (v.sent_on.toLowerCase()  == 'other') {
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

                    if(v.status !="Completed"){
                        var status = '<label class="badge badge-warning">Pending</label>';
                        var groupId = "'"+v.groupId+"'";
		 			    var single = "'single'";
                        var link =_BASE_URL +'/esign/docusign/view/'+ v.id +'?mobile_type=web';
                        var spans = '<span class="col-md-3"><a target="_blank" href="' +link+ '" ><i class="fa fa-desktop" aria-hidden="true"></i></a><a class="ml-3" href="javascript:void(0)" onclick="getSendSMSNew('+v.id+','+single+')"><i class="fa fa-paper-plane" aria-hidden="true"></i></a></span>';
                        responseHtml +='<li><span class="col-md-3" style="margin-right:30px;font-size:20px">'+v.sent_on+'</span>'+status+" "+spans+'</li>'
                    }else{
                        var status  = '<label class="badge badge-success">Completed<label>';
		                var spans = "";
                        responseHtml +='<li><span class="col-md-3" style="margin-right:30px;font-size:20px">'+v.sent_on+'</span>'+status+" "+spans+'</li>'
                    }
                })
            }
            $('#sendRequest').modal('show');

            var htmlResponse = '<input type="hidden" id="enrollment_id" value="'+enrollId+'"><input type="hidden" id="groupId" value="'+gid+'"><input type="hidden" id="document_id" value="'+document_id+'"><div class="box-body no-padding"><ul class="nav nav-pills nav-stacked">'+responseHtml+'</ul></div>';
            $('#snedId').html("");
            $('#snedId').html(htmlResponse);
            $('#loaderdocument').attr('style','display:none');
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
                                esignResponseNew(1);
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

function getSendSMSSubmit() {
    var mobile_no_id_caregiver = $('#mobile_no_id_caregiver').val();
    var email = $('#email').val();
    $('#mobile_no_id_caregiver_error').html("");
    var cnt = 0;
    if (mobile_no_id_caregiver.trim() == '' && email.trim() == '') {
        $('#mobile_no_id_caregiver_error').html("Please enter Email or Mobile");
        cnt = 1;
    }
    if (cnt == 0) {
        var cons = confirm("Are you sure you want to send sms?");


        if (cons == true) {
            var foms = $('#sms_esign')[0];
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
                    $('#sendSMSEsign').modal('hide');

                }
            })
        }
    } else {
        $('#sendSMSEsign').modal('show');

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

function getSendSMSNew(id, type = "") {
    $('#main_caregiver_esign_id').val(id);
    $('#document_send_type_id').val(type);
    $('#sendSMSEsign').modal('show');
    $('#sendSMSEsign').css({
        zIndex: '99999'
    })
}

function resfreshSignerData(){
    var groupId = $('#groupId').val();
    var document_id = $('#document_id').val();
    var enrollId = $('#enrollment_id').val();
    getSigner(groupId,document_id,enrollId);
}

$('#edit_upload_document_modal_submit').click(function (e) {
    $('#edit_upload_document_modal_submit').hide();
    var document_name = $("#document_name").val();
    var file_upload = $("#file_upload").val(); 

    $("#document_name_error").html("");
    $("#file_upload_error").html("");

    cnt = 0;
    if (document_name.trim() == "") {
        $("#document_name_error").html("Please enter Document Name");
        cnt = 1;
    }

    if (file_upload.trim() == "") {
        $("#file_upload_error").html("Please select File");
        cnt = 1;
    } else {
        var fileExtensionType = ["pdf", "csv", "xlsx", "xls", "docx", "doc"];
        var files = $('#file_upload')[0].files;
        var fileName = files[0].name;
        var fileType = fileName.substr(fileName.lastIndexOf(".") + 1);
        $("#file_upload_error").html("");
        if (
            $.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) ==
            -1
        ) {
            $("#file_upload_error").html("Please select only pdf or csv file");
            cnt = 1;
        }
    }
    
    if (cnt == 1) {
        $('#edit_upload_document_modal_submit').show();
        return false;
    } else {
        e.preventDefault();

        var forms = $('#edit_upload_document_modal')[0];
        var newData = new FormData(forms);
        newData.append('_token', _CSRF_TOKEN);
        $.ajax({

            url: saveUploadDocument,
            type: "POST",
            data: newData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response,'kk');
                
                toastr.success(response.error_msg);
                esignResponseNew();
                $('#edit_upload_document_modal')[0].reset();
                $('#closed_id_upload_document').click();

                $('#edit_upload_document_modal_submit').show();

            },
            error: function (xhr, status, error) {
                $('#edit_upload_document_modal_submit').show();
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }
})