var documentId;
function editDocumentServices() {

    var documentService = $('#edit_document_service_id').val();
    var edit_doc_name = $('#edit_doc_name').val();
    var edits_request_service_id = $('#edits_request_service_id').val();
    var edit_document_completed_date = $('#edit_document_completed_date').val();
    var medication_list = $('#edit_medication_list:checked').val();
    var insurance_elg = $('#edit_insurance_elg:checked').val();
    var edit_mdo_source = $('#edit_mdo_source').val();
    var cnt = 0;
    $('#edit_document_service_id_error').html("");
    $('#edit_doc_name_error').html("");
    $('#edits_request_service_id_error').html("");
    $('#edit_document_completed_date_error').html("");
    $('#edit_medication_insurance_err').html("");
    $('#edit_mdo_tag_err').html("");

    if ((_RECORD_TYPE || '').toLowerCase() === 'caregiver' && documentService.length == 0) {
        $('#edit_document_service_id_error').html("Please select Services");
        cnt = 1;
    }

    if (edit_doc_name.trim() =='') {
        $('#edit_doc_name_error').html("Please enter Document Name");
        cnt = 1;
    }

    if (edits_request_service_id =='') {
        // $('#edits_request_service_id_error').html("Please select Service Request");
        // cnt = 1;
    }

    if ((_RECORD_TYPE || '').toLowerCase() === 'caregiver' && edit_document_completed_date =='') {
        $('#edit_document_completed_date_error').html("Please select Completed Date");
        cnt = 1;
    }

    if(insurance_elg != '' && medication_list != '' && insurance_elg == 1 && medication_list == 1){
        $('#edit_medication_insurance_err').html("Please select any one: Medication List or Insurance Elg.");
        cnt=1;
    }
    if($('#edit_mdo_tag:checked').val() == 1 && edit_mdo_source == ""){
        $('#edit_mdo_tag_err').html("MDO Source is required.");
        cnt = 1;
    }
    if (cnt == 1) {

        return false;
    } else {
        var edit_internal_use = $('#edit_internal_use').is(":checked");


        if(edit_internal_use){
            var message = "You want does not sending mail notification in Agency.";
            edit_internal_use =1;
        }else{
            var message = "You want sending mail notification in Agency";
            edit_internal_use =0;
        }

        var formData = new FormData($('#edit_document_service_form')[0]);
        formData.append('document_id', $('#edit_document_main_id').val())
        formData.append('patient_id', _RECORD_ID)
        formData.append('_token', _CSRF_TOKEN)
        formData.append('edits_request_service_id', $('#edits_request_service_id').val())
        formData.append('edit_internal_use', edit_internal_use)
        formData.append('type', _RECORD_TYPE)
        $.ajax({

            type: "POST",
            url: _UPDATE_DOCUMENT_SERVICES,
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success(res.error_msg);
                loadDocumentAjaxList();
                $('#edit-exampleModal-services').modal('hide');
                closeEditDocumentServices();
            },
            error: function (jqhr) {
                toastr.error(jqhr.responseJSON.error_msg)
            }

        })


    }
}

function editDocumentServicesOld() {

    var documentService = $('#edit_document_service_id').val();
    var edit_doc_name = $('#edit_doc_name').val();
    var cnt = 0;
    $('#edit_document_service_id_error').html("");
    $('#edit_doc_name_error').html("");
    if (documentService.length == 0) {
        $('#edit_document_service_id_error').html("Please select Services");
        cnt = 1;
    }

    if (edit_doc_name.trim() =='') {
        $('#edit_doc_name_error').html("Please enter Document Name");
        cnt = 1;
    }

    if (cnt == 1) {

        return false;
    } else {
        var edit_internal_use = $('#edit_internal_use').is(":checked");
        if(edit_internal_use){
            var message = "You want does not sending mail notification in Agency.";

        }else{
            var message = "You want sending mail notification in Agency";
            edit_internal_use =0;
        }

        $.confirm({
            title:"Are you sure?",
            content:message,
            type: 'blue',
            columnClass: 'col-md-6',
            buttons: {
                submit: {
                    text: 'Confirm',
                    btnClass: 'btn-blue',
                    action: function () {
                        var formData = new FormData($('#edit_document_service_form')[0]);
                        formData.append('document_id', $('#edit_document_main_id').val())
                        formData.append('patient_id', _RECORD_ID)
                        formData.append('_token', _CSRF_TOKEN)
                        formData.append('edits_request_service_id', $('#edits_request_service_id').val())
                        formData.append('edit_internal_use', edit_internal_use)
                        $.ajax({

                            type: "POST",
                            url: _UPDATE_DOCUMENT_SERVICES,
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (res) {
                                toastr.success(res.error_msg);
                                loadDocumentAjaxList();
                                $('#edit-exampleModal-services').modal('hide');
                                closeEditDocumentServices();
                            },
                            error: function (jqhr) {
                                toastr.error(jqhr.responseJSON.error_msg)
                            }

                        })
                    }
                },
                cancel: {
                    text: 'Cancel',
                    action: function () {

                    }
                }
            }

        })

    }
}

function closeEditDocumentServices() {
    // $('#edit_document_service_id').val('').change();
    documentId = '';
}

function patientRequestService(typeAction) {

    var type = $('#type').val();

    if(type == 'Esign'){
        var request_service_id = $('#esign_request_service_id').val();
    } else {
        if (typeAction != "") {
            var request_service_id = $('#edits_request_service_id').val();
        } else {
            var request_service_id = $('#request_service_id').val();
        }

    }

    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _PATIENT_REQUESTED_BY_ID_SERVICES,
        data: {
            "type": type,
            "patient_id": _RECORD_ID,
            "selected_services_id": request_service_id,

        },
        success: function (response) {

            var res = response.data;

            var htmlsresp = '';
            var docId = $('#edit_document_main_id').val();
            var resposne = $('#ser' + docId).val();

            if (res && res.length > 0) {

                res.forEach(function (service) {
                    var selected = '';
                    if(resposne !=undefined){
                        if (resposne.includes(service.id)) {
                            selected = "selected='selected'";
                        }
                    }

                    htmlsresp += '<option value="' + service.id + '" ' + selected +' >' + service.name + '</option>';
                });
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }
            if(response.type == 'Esign'){
                $('#esign_document_service_id').html(htmlsresp);
            } else {
                if (typeAction != "") {

                    $('#edit_document_service_id').html(htmlsresp);
                } else {
                    $('#document_service_id').html(htmlsresp);
                }

            }

        }
    })
}

function viewServices(id = "") {
    if(id !=""){
        $('#edit_document_main_id').val(id);
    }
    var newId = $('#edit_document_main_id').val();

    var exitingData = $('#ser' + newId).val();
    var jsonencode = [];
    if(exitingData !=undefined){
        if (exitingData != "") {
            $.each(JSON.parse(exitingData), function (i, v) {
                jsonencode.push(v);
            })
        }
    }

    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _PATIENT_SERVICES,
        data: {
            "id": _RECORD_TYPE,
            "document_id": id,
            'jsonencode': jsonencode,
            'agency_id': _AGENCYID
        },
        success: function (res) {
            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }

            var id = $('#edit_document_main_id').val();
            if (id != "") {

                documentId = id;
                $('#edit_document_service_id').html(htmlsresp);
            } else {
                documentId = "";
                $('#document_service_id').html(htmlsresp);
                $('#esign_document_service_id').html(htmlsresp);
            }

        }
    })

}

function deleteRecordPatient(id) {
    var url = _PATIENT_RECORD_DELETE;
    $.confirm({
        title: "Delete",
        columnClass: "col-md-6",
        content: "Are you sure delete record?",
        buttons: {
            formSubmit: {
                text: "Delete",
                btnClass: "btn-danger",
                action: function () {
                    window.location.href = url + "/" + id;
                },
            },
            cancel: function () {
                //close
            },
        },
    });
}

function requestSelectService(typeAction = "",docId) {

    var type = $('#type').val();
    if(type == 'Esign'){
        var request_service_id = $('#esign_request_service_id').val();
    } else {
        if (typeAction != "") {
            var request_service_id = $('#edits_request_service_id').val();
        } else {
            var request_service_id = $('#request_service_id').val();
        }

    }
    if (request_service_id != "") {
        // patientRequestService(typeAction, docId);
        if (type == 'Esign' || type =="Invoice") {
            patientRequestService(typeAction, docId);
        }else{
            viewServices();
        }
    } else {
        var id = $('#edit_document_main_id').val();
        viewServices(id);
    }
}

function sendPatientDemographicSMS(mobile=""){
    $('#sms_mobile_no').val(mobile)
    $('#show-patient-demo-modal').modal('show');
    $('#sms_mobile_no_error').html("");
}

function clearSMSMobileModal(){
    $('#show-patient-demo-modal').modal('hide');
    $('#sms_mobile_no').val('')
    $('#sms_mobile_no_error').html("");
}

$('#savePatientSMSMobile').click(function(e){
    var mobile = $('#sms_mobile_no').val();
    var cnt =0;
    $('#sms_mobile_no_error').html("");
    if(mobile ==""){
        $('#sms_mobile_no_error').html("Please enter mobile no");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _PATIENT_DEMOGRAPHIC_SMS_LINK,
            data: {
                "id": _RECORD_ID,
                'mobile': mobile,
                '_token':_CSRF_TOKEN
            },
            success: function (res) {
                toastr.success(res.error_msg)
                clearSMSMobileModal();
            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
})

function updateLanguageDetails(){
    $('#language_id').val($('#record_languages_id').val());
    $('#language_error').html("");

}

function updatePatientLanguage(){
    var language_id = $('#language_id').val();
    var cnt =0;
    $('#language_error').html("");

    if(language_id ==''){
        $('#language_error').html("Please select Language");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _PATIENT_UPDATE_LANGUAGE,
            data: {
                "patient_id": _RECORD_ID,
                'language_id': language_id,
                '_token':_CSRF_TOKEN
            },
            success: function (res) {
                toastr.success(res.error_msg)
                var languageText = $('#language_id option:selected').text();
                $('#record_languages_id').val(language_id)
                $('#record_languages_res_id').html(languageText)
                $('#close_language').click();

            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}

function updateMobileDetails(){
    $('#record_mob_id').val($('#record_mobile_id').text());
    $('#record_mob_error').html("");
}

function updatePatientMobile(){
    var mobileNoId = $('#record_mob_id').val();
    var cnt =0;
    $('#record_mob_error').html("");

    if(mobileNoId ==''){
        $('#record_mob_error').html("Please enter Mobile");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _PATIENT_UPDATE_MOBILE,
            data: {
                "patient_id": _RECORD_ID,
                'mobile': mobileNoId,
                '_token':_CSRF_TOKEN
            },
            success: function (res) {
                toastr.success(res.error_msg)
                $('#record_mobile_id').html(res.data.mobile);
                $('#close_mobile').click();

            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}

function updatePhoneDetails(){
    $('#record_phn_id').val($('#record_phone_id').text());
    $('#record_phn_error').html("");
}

function updatePatientPhone(){
    var mobileNoId = $('#record_phn_id').val();
    var cnt =0;
    $('#record_phn_error').html("");

    if(mobileNoId ==''){
        $('#record_phn_error').html("Please enter Phone");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _PATIENT_UPDATE_PHONE,
            data: {
                "patient_id": _RECORD_ID,
                'phone': mobileNoId,
                '_token':_CSRF_TOKEN
            },
            success: function (res) {
                toastr.success(res.error_msg)
                $('#record_phone_id').html(res.data.phone);
                $('#close_phone').click();

            },
            error:function(jqr){
                toastr.error(jqr.responseJSON.error_msg);
            }
        });
    }
}
function flagChange(){
    $.confirm({
        title: "Flag",
        columnClass: "col-md-6",  // Adjust the width of the modal
        content: function () {
            // Returning HTML string for the input field
            var html = '<div><b><label for="reason">Reason:</label><b>' +
                       '<textarea style="margin-bottom: 0 !important; width: 100%;" name="reason" id="reason" spellcheck="false"></textarea></div>';
            return html;
        },
        buttons: {
            confirm: {
                text: 'Confirm',  // Text for the confirm button
                btnClass: 'btn-primary',  // Style class for the button
                action: function () {
                    var reason = this.$content.find('#reason').val();  // Get the value of the reason input
                    // AJAX request when Confirm is clicked
                    $.ajax({
                        global: false,  // Disable global AJAX events
                        url: _PATIENT_RECORD_FLAG,  // URL to send the request to
                        type: "GET",  // HTTP method for the request
                        data: {
                            '_token': _CSRF_TOKEN,  // CSRF Token for security
                            'id': _RECORD_ID,  // Record ID to identify which record to update
                            'reason': reason  // Send the reason with the request
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);  // Display success message
                            location.reload();  // Reload the page
                        },
                        error: function (xhr, status, error) {
                            toastr.error(xhr.responseJSON.error_msg);  // Display error message
                        }
                    });
                }
            },
            cancel: function () {
                // No action for cancel, just closes the dialog
            }
        }
    });
}

function flagDocumentChange(id){
    var url = _PATIENT_RECORD_DOC_FLAG;
    $.confirm({
        title: "Flag",
        columnClass: "col-md-6",
        content: function () {
            // Returning HTML string for the input field
            var html = '<div><b><label for="reason">Reason:</label><b>' +
                       '<textarea style="margin-bottom: 0 !important; width: 100%;" name="reason" id="reason_doc" spellcheck="false"></textarea></div>';
            return html;
        },
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function () {
                    var reason_doc = this.$content.find('#reason_doc').val();  // Get the value of the reason input
                    $.ajax({
                        global: false,
                        url: url,
                        type: "GET",
                        data: {
                            '_token': _CSRF_TOKEN,
                            'id':id,
                            'reason': reason_doc  // Send the reason with the request
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);
                            loadDocumentAjaxList();
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
    });
}

function flagNotesChange(id){
    var url = _PATIENT_RECORD_NOTES_FLAG;
    $.confirm({
        title: "Flag",
        columnClass: "col-md-6",
        content: function () {
            // Returning HTML string for the input field
            var html = '<div><b><label for="reason">Reason:</label><b>' +
                       '<textarea style="margin-bottom: 0 !important; width: 100%;" name="reason" id="reason_notes" spellcheck="false"></textarea></div>';
            return html;
        },
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function () {
                    var reason_notes = this.$content.find('#reason_notes').val();
                    $.ajax({
                        global: false,
                        url: url,
                        type: "GET",
                        data: {
                            '_token': _CSRF_TOKEN,
                            'id':id,
                            'reason': reason_notes
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);
                            // loadAllNotes();
                            $('#flag_div_id_'+id).removeClass('messags-div-flag');
                            $('#flag_div_id_'+id).removeClass('messags-div');
                            if(response.data.flag =='1'){
                               $('#view_flag_text_'+id).html("Flagged");
                               $('#flag_div_id_'+id).addClass('messags-div-flag');
                            }else{
                                $('#view_flag_text_'+id).html("Flag");
                                $('#flag_div_id_'+id).addClass('messags-div');
                            }
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
    });
}

function flagTaskChange(id){
    var url = _PATIENT_RECORD_TASK_FLAG;
    $.confirm({
        title: "Flag",
        columnClass: "col-md-6",
        content: function () {
            // Returning HTML string for the input field
            var html = '<div><b><label for="reason">Reason:</label><b>' +
                       '<textarea style="margin-bottom: 0 !important; width: 100%;" name="reason" id="reason_task" spellcheck="false"></textarea></div>';
            return html;
        },
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function () {
                    var reason_task = this.$content.find('#reason_task').val();
                    $.ajax({
                        global: false,
                        url: url,
                        type: "GET",
                        data: {
                            '_token': _CSRF_TOKEN,
                            'id':id,
                            'reason':reason_task,
                            'record_id' : _RECORD_ID
                        },
                        success: function (response) {
                            toastr.success(response.error_msg);
                            getTaskList(1);
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
    });
}

function clearDob(){
    $('.error').html("");
    $('#exampleModal-dob').modal('hide');
}

function updateDob(){
    var dob = $('#dob').val();
    var cnt=0;
    $('#dob_error').html("");

    if(dob.trim() ==''){
        $('#dob_error').html("Please enter Email");
        cnt = 1;
    }

    if(cnt  ==1){
        return  false;
    }else{
        $.ajax({
            async: false,
            global: false,
            url: _PATIENT_UPDATE_DOB,
            type: "POST",
            data: {
                "dob": dob,
                "_token": _CSRF_TOKEN,
                'patient_id': _RECORD_ID
            },
            success: function(resp) {
                $("#patient_dob").html(dob)
                $("#dob_id").val(dob)
                toastr.success(resp.error_msg);
                clearDob()

            },
            error:function(xhr){
                toastr.error(xhr.responseJSON.error_msg);
            }

        })
    }
}

$(document).on("change", ".patientPageDesign", function() {
    var patient_page = $(this).prop('checked') == true ? 1 : 0;
    if(patient_page == 1){
        $('.patient-toggle').removeClass('patient-bg');
    }else{
        $('.patient-toggle').addClass('patient-bg');
    }
    var user_id = USER_ID;
    var patient_id = _RECORD_ID;
    $.confirm({
        title: 'Are you sure?',
        content: 'you want to switch the patient view detail page?',
        columnClass: "col-md-6",
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                action: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: USER_PAGE_DETAILS_PAGE,
                        data: {
                            'patient_page': patient_page,
                            'user_id': user_id,
                            '_token':_CSRF_TOKEN,
                            'patient_id':patient_id
                        },
                        success: function(data) {
                            location.reload();
                        }
                    });
                }
            },
            cancel: function() {
                //close
                var lastStatus = $('#patientPageDesign').val();

                if(lastStatus ==1){
                    $('#patientPageDesign').prop("checked",true);
                    $('.patient-toggle').removeClass('patient-bg');
                }else{
                    $('#patientPageDesign').prop("checked",false);
                    $('.patient-toggle').addClass('patient-bg');
                }
            },
        },
    });
});

function mobileOrPhoneCopy(type){
    let number = "";
    if(type =="mobile"){
        number = $('#record_mobile_id').html()
    }else{
        number = $('#record_phone_id').html()
    }

    let number1 = number.replace(/\D/g, "");;
    navigator.clipboard.writeText(number1.toString())
        .then(() =>
        toastr.success('Copied successfully')
        )
        .catch(err => console.error("Error copying:", err));
}

function getPatientDocumentDetailsOld(id){
    $.ajax({

        url: _PATIENT_DOCUMENT_DETAILS_BY_ID,
        type:"get",
        data: {

            document_id: id,
            patient_id: _RECORD_ID,

        },
        success:function(res){

            $('#edit_doc_name').val(res.data[0].document_name);
            $('#edit_doc_name_error').html("");
            $('#edit_document_completed_date').val('');
            if(res.data[0].document_completed_date !=null){
                $('#edit_document_completed_date').val(moment(res.data[0].document_completed_date).format('MM/DD/YYYY'));
            }

            $('#edit_internal_use').prop("checked",false);
            if(res.data[0].internal_use ==1){
                $('#edit_internal_use').prop("checked",true);
            }
        },
        error:function(jqr){
        }
    });
}

function getDocumentList(){
    $.ajax({
        url: GET_DOCUMENT_INTERNAL_USE_DATA,
        data:{
            patient_id : _RECORD_ID
        },
        success:  function(resp) {
            $('#doc_listing').html('');
            let htmlp = '';
            let response = resp.data;
            if(resp!= "" && resp.data.length > 0 ){
                $('#exampleModal-complete').children().addClass('modal-lg');
                $('#exampleModal-complete').children().css('max-width', '800px');
                htmlp = `<div class="table-responsive" id="show_demographic-detail"><label for=""><b>Internal Use Only Documents List</b></label><br>
               <p ><br>Notes:</b> <span class="text-muted">Select internal-use-only documents from the list to approve them all at once</span></p>
                            <table id="" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th nowrap="">#</th>
                                        <th nowrap=""><input type="checkbox" id="all-checkbox" onchange="checkAllBox()" class=""></th>
                                        <th nowrap="">Doc ID</th>
                                        <th nowrap="">Doc Name</th>
                                        <th nowrap="">Requested Id</th>
                                        <th nowrap="">Document Completion Date</th>
                                        <th nowrap="">Created Date/Created By</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                                $.each(response, function(i, v) {
                                    htmlp +=  `<tr>
                                                    <td nowrap="">${i+1}</td>
                                                    <td nowrap=""><input type="checkbox" doc-attr="${v.id}" class="form-check-input docCheckbox ml-0" style="margin-top:-6px"></td>
                                                    <td nowrap="">${v.id}</td>
                                                    <td nowrap="">${v.document_name}</td>
                                                    <td nowrap="">${v.request_service_id}</td>
                                                    <td nowrap="">${v.document_completed_date != null ? moment(v.document_completed_date).format('MM/DD/YYYY') : '-'}</td>
                                                    <td nowrap="">${v.created_date != null ? moment(v.created_date).format('MM/DD/YYYY') : '-'} <br/> ${v.user_details.full_name}</td>
                                            </tr>`;
                                    });
                                htmlp +=  `</tbody>
                                            </table><span class="mt-1 error" id="checkbox_status_error"></span>
                                            </div>`;
                                $('#doc_listing').html(htmlp);
            }
        }
    });
}

function checkAllBox(){

    if($('#all-checkbox').length == 1){
        $('.docCheckbox').prop('checked', $('#all-checkbox').prop('checked'));
    }else{
        $('.docCheckbox').prop('checked', false);
    }
}

function updatePortalDepartment(){
    var assign_dept = $('#assign_dept').val();
    $.ajax({
        async: false,

        global: false,
        url: _SAVE_DEPARTEMNT,
        type: "POST",
        data: {
            "assign_dept": assign_dept,
            "_token": _CSRF_TOKEN,
            'id': _RECORD_ID
        },
        success: function(resp) {
            _ASSIGN_DEPT_ID = assign_dept;
            $("#assign_department").html(resp.data.department_name)
            $("#assign_dept").val(assign_dept);
            $("#assign_department_id").val(assign_dept).change();
            toastr.success(resp.error_msg);
            $('#close_assign_dept_id').click();
        },
        error:function(xhr){
            toastr.error(xhr.responseJSON.error_msg);
        }
    })
}

function assignDepartment(){
    fillDepartment();
}

function showAssignAppointmentData(assign_id){
    $('#assign_id').val(assign_id);
    $('#assign_department_id').val(_ASSIGN_DEPT_ID);
}

loadBranchDropdown();
$('body').on('change', '#service_id', function(){
    loadBranchDropdown();
});

function loadBranchDropdown(){
    if(typeof _AGENCYID === 'undefined'){
        return;
    }
    let agencyId = _AGENCYID;
    let serviceIds = $('#service_id').val();

    if(!agencyId || !serviceIds || serviceIds.length === 0){
        resetBranchDropdown();
        return;
    }

    if(typeof _GET_BRANCHES_BY_AGENCY_SERVICES === 'undefined'){
        return;
    }

    $.ajax({
        type: "GET",
        url: _GET_BRANCHES_BY_AGENCY_SERVICES,
        data: {
            'agency_id': agencyId,
            'service_ids': serviceIds
        },
        success: function(res){
            if(res.status && res.data && res.data.length > 0){
                var html = '<option value="">Select Branch</option>';
                $.each(res.data, function(i, branch){
                    html += '<option value="'+ branch.branch_id +'">'+ branch.branch_name +'</option>';
                });
                $('#patient_branch_id').html(html);
                $('#branch_dropdown_wrapper').removeClass('hide');
                $('#location_branch_wrapper').addClass('hide');
                $('#location_branch').val('');
                $('#patient_branch_id').val(SELECTED_BRANCH_ID);
                checkBranchMandatory(agencyId, serviceIds);
            } else {
                resetBranchDropdown();
            }
        },
        error: function(){
            resetBranchDropdown();
        }
    });
}

function resetBranchDropdown(){
    $('#patient_branch_id').html('<option value="">Select Branch</option>');
    $('#branch_dropdown_wrapper').addClass('hide');
    $('#location_branch_wrapper').removeClass('hide');
}

function editBranch(){
   let location_branch = $('#location_branch').val();
   $(".location_branch_error").html("");
   let branchVal = $('#patient_branch_id').val();
    if (isBranchMandatory && !$('#branch_dropdown_wrapper').hasClass('hide')) {
        if (!branchVal) {
            $('.location_branch_error').html('Branch selection is mandatory for this agency and service combination');
            return false;
        }
    }
    $.ajax({
        async: false,
        global: false,
        url: _SAVE_BRANCH,
        type: "POST",
        data: {
            "branch_id": branchVal,
            "_token": _CSRF_TOKEN,
            'id': _RECORD_ID,
            'location_branch' : location_branch
        },
        success: function(resp) {
            SELECTED_BRANCH_ID = branchVal;
            $("#location_branch_text").html(resp.data.branch_name)
            $("#patient_branch_id").val(branchVal);
            $('#edit-branch-modal').modal('hide');
            toastr.success(resp.error_msg);
        },
        error:function(xhr){
            toastr.error(xhr.responseJSON.error_msg);
        }
    })
}

function openPharmacyNameModal() {
    $('#pharmacy_name_input').val($('#pharmacy_name_text').text().trim() === '-' ? '' : $('#pharmacy_name_text').text().trim());
    $('#pharmacy_name_error').html('');
}

function openPharmacyNoModal() {
    $('#pharmacy_no_input').val($('#pharmacy_no_text').text().trim() === '-' ? '' : $('#pharmacy_no_text').text().trim());
    $('#pharmacy_no_error').html('');
}

function savePharmacyName() {
    var pharmacy_name = $('#pharmacy_name_input').val();
    $('#pharmacy_name_error').html('');

    $.ajax({
        url: _SAVE_PHARMACY,
        type: 'POST',
        data: {
            '_token': _CSRF_TOKEN,
            'patient_id': _RECORD_ID,
            'pharmacy_name': pharmacy_name
        },
        success: function(resp) {
            $('#pharmacy_name_text').html(pharmacy_name !== '' ? pharmacy_name : '-');
            $('#edit-pharmacy-name-modal').modal('hide');
            toastr.success(resp.error_msg);
        },
        error: function(xhr) {
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function savePharmacyNo() {
    var pharmacy_no = $('#pharmacy_no_input').val();
    $('#pharmacy_no_error').html('');

    $.ajax({
        url: _SAVE_PHARMACY,
        type: 'POST',
        data: {
            '_token': _CSRF_TOKEN,
            'patient_id': _RECORD_ID,
            'pharmacy_no': pharmacy_no
        },
        success: function(resp) {
            $('#pharmacy_no_text').html(pharmacy_no !== '' ? pharmacy_no : '-');
            $('#edit-pharmacy-no-modal').modal('hide');
            toastr.success(resp.error_msg);
        },
        error: function(xhr) {
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function saveNoMedicationTaken(checkbox) {
    $.ajax({
        url: _SAVE_NO_MEDICATION_TAKEN,
        type: 'POST',
        data: {
            '_token': _CSRF_TOKEN,
            'patient_id': _RECORD_ID,
            'no_medication_taken': checkbox.checked ? 1 : 0
        },
        success: function(resp) {
            toastr.success(resp.error_msg);
        },
        error: function(xhr) {
            checkbox.checked = !checkbox.checked;
            showErrorAndLoginRedirection(xhr);
        }
    });
}

function checkBranchMandatory(agencyId, serviceIds){
    isBranchMandatory = false;
    $.ajax({
        type: "GET",
        url: _CHECK_MANDATORY,
        data: {
            'agency_id': agencyId,
            'service_ids': serviceIds
        },
        success: function(res){
            if(res.status && res.data == 1){
                isBranchMandatory = true;
            }
        }
    });
}

var agencyUserRepTokenInitialized = false;
var agencyUserRepPrePopulate = [];
var finalAgencyUserPopulate = [];
function initAgencyUserRepToken() {
    if (agencyUserRepTokenInitialized) {
        $('#edit_agency_user_rep').tokenInput('destroy');
        agencyUserRepTokenInitialized = false;
    }

    if($('#edit_agency_rep_id').val() !=""){
        agencyUserRepPrePopulate = [{
            id: $('#edit_agency_rep_id').val(),
            name: $('#edit_agency_rep').val()
        }];
    }
    
    
    var urlResponse = _SEARCH_AGENCY_USER_LIST+"?agency_id=" + _AGENCYID;
    finalAgencyUserPopulate = [];
    finalAgencyUserPopulate[$('#edit_agency_rep_id').val()] =  $('#edit_agency_rep').val();
    $('#edit_agency_user_rep').tokenInput(urlResponse, {
        queryParam: "q",
        preventDuplicates: true,
        prePopulate: agencyUserRepPrePopulate,
        hintText: "Type to search Agency Rep",
        noResultsText: "No Agency Rep found",
        searchingText: "Searching...",
        zindex: 1060,
        tokenLimit: 1,
        
        onAdd: function(item) {
            finalAgencyUserPopulate[item.id] = item.name
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
    agencyUserRepTokenInitialized = true;
}

function saveAgencyUserRep() {
    var userIds = $('#edit_agency_user_rep').val();
    $('#edit_agency_user_rep_error').html("");
    if(userIds ==""){
        $("#edit_agency_user_rep_error").html("Please select Agency Rep");
        return false;
    }
    $.ajax({
        url: _UPDATE_AGENCY_REP,
        type: 'POST',
        data: {
            _token: _CSRF_TOKEN,
            patient_id: _RECORD_ID,
            user_ids: userIds,
            
        },
        success: function(response) {
            $('#edit_agency_rep_id').val(userIds)
            $('#edit_agency_rep').val(finalAgencyUserPopulate[userIds])
            
            $('#editAgencyUserRepModal').modal('hide');
            $('#new_agency_user_resp').html(finalAgencyUserPopulate[userIds])
            toastr.success(response.error_msg);
        },
        error: function(jqr) {
            showErrorAndLoginRedirection(jqr);
        }
    });
}

$('#editAgencyUserRepModal').on('hidden.bs.modal', function() {
    if (agencyUserRepTokenInitialized) {
        $('#edit_agency_user_rep').tokenInput('destroy');
        agencyUserRepTokenInitialized = false;
    }
});