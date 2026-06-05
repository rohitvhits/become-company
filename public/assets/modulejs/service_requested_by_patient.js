var isServiceBranchMandatory = false;

$(document).on('change', '#service_id_by_patient_type', function () {
    checkServiceBranchMandatory();
});

function checkServiceBranchMandatory() {
    var serviceIds = $('#service_id_by_patient_type').val() || [];
    serviceIds = Array.isArray(serviceIds) ? serviceIds : [serviceIds];
    $('#service_branch_id_error').html("");

    if (serviceIds.length == 0) {
        $('#service_branch_div').hide();
        $('#service_branch_mandatory_star').hide();
        $('#service_branch_id').html('<option value="">Select Branch</option>');
        isServiceBranchMandatory = false;
        return;
    }

    $.ajax({
        url: _CHECK_MANDATORY,
        type: "GET",
        async: false,
        data: {
            'agency_id': _AGENCYID,
            'service_ids': serviceIds
        },
        success: function (response) {
            if (response.status && response.data == 1) {
                isServiceBranchMandatory = true;
                $('#service_branch_mandatory_star').show();
                loadServiceBranches(serviceIds);
                $('#service_branch_div').show();
            } else {
                isServiceBranchMandatory = false;
                $('#service_branch_mandatory_star').hide();
                $('#service_branch_div').hide();
                $('#service_branch_id').html('<option value="">Select Branch</option>');
            }
        },
        error: function () {
            isServiceBranchMandatory = false;
            $('#service_branch_div').hide();
        }
    });
}

function loadServiceBranches(serviceIds) {
    $.ajax({
        url: _GET_BRANCHES_BY_AGENCY_SERVICES,
        type: "GET",
        async: false,
        data: {
            'agency_id': _AGENCYID,
            'service_ids': serviceIds
        },
        success: function (response) {
            var options = '<option value="">Select Branch</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (i, v) {
                    var selected = (SELECTED_BRANCH_ID == v.branch_id) ? 'selected' : '';
                    options += '<option value="' + v.branch_id + '" ' + selected + '>' + v.branch_name + '</option>';
                });
            }
            $('#service_branch_id').html(options);
        }
    });
}

function getPatientId(id, type, agencyId = "") {
    $('#service_patient_id').val(id);
    $('#portal_type').val(type);

    if (type != null) {
        type = type;
    } else {
        type = _RECORD_TYPE;
    }
    var _AGENCY_ID = (agencyId != "") ? agencyId : _AGENCYID;

    getPatientServices(type, _AGENCY_ID);
    setTimeout(() => {
        $('.service_id_by_patient_type').select2({
            width: '100%' // This helps enforce the 100% width set in CSS
        });
    }, 200)

}

function serviceRequestedList() {
    $('.serviceLoader').attr('style', '');
    $("#service_requested_id").html("");
    $("#service_requested_id").html('<tr><td colspan="10">Loading</td></tr>');
    $(".service_request_tab").attr("style", "display:none");
    $.ajax({
        url: _PATIENT_WISE_SERVICE_REQUESTED_LIST,
        type: "get",
        data: {
            patient_id: _RECORD_ID,
        },
        success: function (response) {
            $('.serviceLoader').attr('style', 'display:none');
            var json = response.data.data;
            var tableResponse = "";
            var last_service_id = (json.length > 0 && json[0].id) ? json[0].id : 0
            var cnt = 1;
            if (json.length != 0) {

                $.each(json, function (i, v) {
                    var servicesArray = [];
                    var servicesIdArray = [];

                    $.each(v.patient_service_request_relation_ship, function (i, vs) {
                        if (vs.services && vs.services.length > 0 && vs.services[0].name) {
                            servicesArray.push(vs.services[0].name);
                            servicesIdArray.push(vs.services[0].id);
                        }

                    })
                   
                    let status = '';
                    if (v.status.toLowerCase() == "pending") {
                        status = "<label class='badge badge-warning'>Pending</label>";
                    }
                    if (v.status.toLowerCase() == "booked") {
                        status = "<label class='badge badge-info'>Booked</label>";
                    }
                    if (v.status.toLowerCase() == "completed") {
                        status = "<label class='badge badge-success'>Completed</label>";
                    }
                    if (v.status.toLowerCase() == "cancelled") {
                        status = "<label class='badge badge-danger'>Cancelled</label>";
                    }
                    if (v.status.toLowerCase() == "noshow") {
                        status = "<label class='badge badge-danger'>No Show</label>";
                    }
                    if (v.status.toLowerCase() == "refused") {
                        status = "<label class='badge badge-danger'>Refused</label>";
                    }
                    if (v.status.toLowerCase() == "processing") {
                        status = "<label class='badge badge-info'>Processing</label>";
                    }
                    if (v.status.toLowerCase() == "arrived") {
                        status = "<label class='badge badge-primary'>Arrived</label>";
                    }
                    if (v.status.toLowerCase() == "checkin") {
                        status = "<label class='badge badge-primary'>Mark as ClockIn</label>";
                    }
                    if (v.status.toLowerCase() == "not interested") {
                        status = "<label class='badge badge-primary'>Not Interested</label>";
                    }
                    if (v.status.toLowerCase() == "hospitalized/rehab") {
                        status = "<label class='badge badge-secondary'>Hospitalized/Rehab</label>";
                    }
                    if (v.status.toLowerCase() == "unabletocontact") {
                        status = "<label class='badge badge-primary'>Unable To Contact</label>";
                    }
                    if (v.status.toLowerCase() == "pending termination") {
                        status = "<label class='badge badge-danger'>Pending Termination</label>";
                    }

                    if (v.status.toLowerCase() == "on hold") {
                        status = "<label class='badge badge-secondary'>On Hold</label>";
                    }
                    if (v.status.toLowerCase() == "on leave") {
                        status = "<label class='badge badge-info'>On Leave</label>";
                    }
                    if (v.status.toLowerCase() == "terminated") {
                        status = "<label class='badge badge-danger'>Terminated</label>";
                    }

                    if (v.status.toLowerCase() == "in process") {
                        status = "<label class='badge badge-secondary'>In process</label>";
                    }

                    if (v.status.toLowerCase() == "undo") {
                        status = "<label class='badge badge-primary'>Undo</label>";
                    }

                    if (v.status == '1st Attempt - Unable to Contact' || v.status == '2nd Attempt - Unable to Contact' || v.status == '3rd Attempt - Unable to Contact' || v.status == 'Patient Asked to Reschedule' || v.status == 'New Order Received') {
                        status = "<label class='badge badge-info'>" + v.status + "</label>";
                    }

                    if (v.status == 'Telehealth Completed' || v.status == 'Telehealth Completed , Pending Forms' || v.status == 'Form Completed' || v.status == 'Service Provided') {
                        status = "<label class='badge badge-success'>" + v.status + "</label>";
                    }
                    if (v.status == 'Patient Deceased' || v.status == 'Appointment was missed' || v.status == 'Appointment Missed' || v.status == 'Closed Temporarily') {
                        status = "<label class='badge badge-danger'>" + v.status + "</label>";
                    }

                    if (v.status == 'Signed' || v.status == 'Signed & Sent Back to the Agency' || v.status == 'New Form Requested') {
                        status = "<label class='badge badge-primary'>" + v.status + "</label>";
                    }
                  
                    if (v.status.toLowerCase() == "inactive") {
                        status = "<label class='badge badge-danger'>Inactive</label>";
                    }
                    reason = "";
                    if (v.other_reason != null) {
                        reason += '<i class="fa fa-info-circle ml-1" style="cursor: pointer; color: #17a2b8;" data-toggle="tooltip" data-placement="top" title="' + v.other_reason + '"></i>';
                    }
                    var finalStatus = status + '<br>' + v.reason_name + '<br>' + reason;
                    var visitingLink = "";
                    if (_THIRD_PARTY_ID != "" && v.from_api != 1) {
                        visitingLink = '<a class="mr-2" onclick="linkVisiting(' + _THIRD_PARTY_ID + ',' + v.id + ')" title="Link Visiting"><i class="fa fa-link"></i></a>'
                    }

                    var integrationIcon = "";
                    if (v.from_api == 1) {
                        integrationIcon = '<img src="' + _INTEGRATION_ICON + '" style="width:20px;height:20px">'
                    }

                    var fullName = "";

                    if (v.user_details != null) {
                        fullName = v.user_details.first_name + ' ' + v.user_details.last_name;
                    }

                    var due_date = "";
                    if (v.due_date != null && v.due_date != "1969-12-31" && v.due_date != "0000-00-00") {
                        due_date = moment(v.due_date).format('MM/DD/YYYY');
                    }
                    var completedDate = "";
                    if (v.completed_date != null && v.completed_date != "1969-12-31" && v.completed_date != "0000-00-00") {
                        completedDate = moment(v.completed_date).format('MM/DD/YYYY HH:mm A');
                    }

                    var followupDate = "";

                    if (v.follow_up_date != null && v.follow_up_date != "1969-12-31" && v.follow_up_date != "0000-00-00") {
                        followupDate = moment(v.follow_up_date).format('MM/DD/YYYY');
                    }

                    var completedUserName = "";
                    if (v.completed_user_details != null) {
                        completedUserName = v.completed_user_details.first_name + ' ' + v.completed_user_details.last_name;
                    }

                    var statusUpdateDate = "";
                    var statusUpdateBy = "";
                    if (v.status_user_details != null) {
                        statusUpdateBy = v.status_user_details.first_name + ' ' + v.status_user_details.last_name;
                    }

                    if (v.last_status_update != null) {
                        statusUpdateDate = moment(v.last_status_update).format('MM/DD/YYYY hh:mm A');
                    }

                    var statusHtml = `<div class="btn-group status-dropdoown mr-2">
                    <button type="button" class="btn btn-warning" title="Status">Status</button>
                    <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6" style="overflow-y:scroll;height:300px">`;
                    if (v.status == 'Pending' && v.status != 'completed') {
                        statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="pending" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'Pending')">Pending</a>`;
                    }

                    if (v.status == 'Pending' && v.status != 'refused') {
                        statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="booked" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'Scheduled')">Scheduled</a>`;
                    }
                    if (v.status != 'completed' && v.status != 'refused' && v.status != 'cancelled' && v.status != 'noshow') {
                        statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="checkin" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'MarkAsCheckIn');">Mark as CheckIn</a>
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="processing" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'MarkAsProcessing');">Mark as Processing</a>`;
                    }
                    if (v.status != 'cancelled' && v.status != 'noshow' && v.status != 'refused') {
                        statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="complete" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'MarkAsCompleted');">Mark as Completed</a>`;
                    }
                    if (v.status != 'completed' && v.status != 'refused' && v.status != 'cancelled' && v.status != 'noshow') {
                        statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" class="pull-right" id="cancel" data-toggle="modal" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'MarkAsCancel')">Mark as Cancel</a>`;
                    }
                    if (v.status != 'completed' && v.status != 'noshow' && v.status != 'refused') {
                        statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" class="pull-right" id="noshow" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'MarkAsNoShow');">Mark as NoShow</a>`;
                    }
                    if (v.status != 'completed') {
                        statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="refused" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'MarkAsRefused')">Mark as refused</a>`;
                    }
                    if (v.prev_status != '') {
                        statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="refused" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'Undo')">Undo</a>`;
                    }
                    statusHtml += `<a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="hospitalized"     onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'MarkAsHospitalized/Rehab')">Mark as Hospitalized/Rehab</a>
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="unableToContact" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'UnableToContact')">Unable To Contact</a>
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="InService" onclick="$('#exampleModal-inservice-record').modal('show');">In Service</a>
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="PendingTermination" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'PendingTermination')">Pending Termination</a>
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="Onhold" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'OnHold')">On hold</a>
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="Onleave" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'OnLeave')">On leave</a>
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="Terminated" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'Terminated')">Terminated</a>
                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" id="Inactive" onclick="savePatientStatusTypeWiseServiceRequest(${v.id},'Inactive')">Inactive</a>
                        </div>
                    </div>`;

                    var actions = "";
                    var resolutionChart = `<a data-fancybox data-src="#patientServiceResolutionModal" data-id="${v.id}" href="javascript:void(0)" class="pull-right btn btn-primary btn-rounded btn-sm d-none d-md-block mr-2" style="padding: 0.282rem 12px">Resolution</a>`;

                    if (v.merge_flag == 0) {
                        if (_AUTH_AGENCY_ID == "" && _DELETED_FLAG_APPOINTMENT == 1) {
                            actions = `<td style="overflow: unset !important">`;
                            if (_RECORD_TYPE == 'Caregiver') {
                                actions += statusHtml;
                            } else {
                                actions += resolutionChart;
                            }
                            actions += `${visitingLink}
                            
                                </td>`;
                        } else if (_AUTH_AGENCY_ID != "" && last_service_id == v.id) {
                            if (EDIT_SERVICE_ENABLE == 1) {
                                actions += `<td style="overflow: unset !important"><a data-toggle="modal" class="btn btn-info btn-sm d-none d-md-block" data-target="#editServiceModal" style="color:#fff" onclick="getServicesReq(${v.id},${JSON.stringify(servicesIdArray)})">Edit</a></td>`;
                            }
                        }
                    }

                    var tags = "";

                    var redirection_view_page = `<a href="${_PATIENT_VIEW}/${v.patient.id}">${v.patient.id}</a>`;
                    if (v.merge_flag == 1) {
                        tags = `<span class="badge badge-info">Merge</span>`;
                        redirection_view_page = `<a href="${_VIEW_DELETE_APPOINTMENT_SHOW}/${v.patient.id}">${v.patient.id}</a>`;
                    }
                    tableResponse += `
                    <tr>
                        <td nowrap>${v.id} ${tags}<br>${integrationIcon}</td>
                        <td nowrap>${redirection_view_page}</td>
                        <td nowrap>${v.patient.agency_detail.agency_name}</td>
                        <td nowrap>${v.documents}</td>
                        <td nowrap>${servicesArray.join(', <br>')}</td>
                        <td nowrap>${finalStatus}</td>
                        <td nowrap>${due_date}</td>
                        <td nowrap>${followupDate}</td>
                       
                        <td nowrap>${v.created_date} <br>${fullName}</td>
                        <td nowrap>${completedDate} <br>${completedUserName}</td>
                        <td nowrap>${statusUpdateDate} <br>${statusUpdateBy}</td>
                        
                            ${actions}
                       
                    </tr>`;
                });
            } else {
                tableResponse = '<tr><td colspan="10">No record available</td></tr>';
            }

            $("#service_requested_id").html("");
            $("#service_requested_id").html(tableResponse);
            $(".service_request_tab").attr("style", "display:flex");
            // $("#service_requested_id").attr("style","");
        },
    });
}

function newCall(patientId) {
    $("#patient_id").val(patientId);
}

function saveServiceRequest() {
    var patientId = $("#patient_id").val();
    var serviceIds = $("#service_eid").val();

    $("#service_eid_error").html("");
    var cnt = 0;

    if (serviceIds.length === 0) {
        $("#service_eid_error").html("Please select at least one service.");
        cnt = 1;
    }

    if (cnt === 1) {
        return;
    }

    var serviceIdsCommaSeparated = serviceIds.join(",");

    $.ajax({
        url: _PATIENT_WISE_SERVICE_REQUEST_SAVE,
        type: "POST",
        data: {
            _token: _CSRF_TOKEN,
            patient_id: patientId,
            service_id: serviceIdsCommaSeparated,
        },
        success: function (response) {
            toastr.success("Services Saved Successfully.");
            $("#serviceRequestModal").modal("hide");
        },
        error: function (response) {
            toastr.error("Sorry, something went wrong. Please try again.");
        },
    });
}

$("#serviceRequestModal").on("hide.bs.modal", function () {
    $("#patient_id").val("");
    $("#service_eid").val("").trigger("change");
    $("#service_eid_error").html("");
});

function getServices(service_id) {
    $.ajax({
        url: _GET_PATIENT_WISE_SERVICES,
        type: "GET",
        data: {
            service_id: service_id
        },
        success: function (response) {
            if (response.success) {
                var serviceRequests = response.data;
                $("#service_eid").empty();
                $("#patient_wise_service_id").val("");

                if (serviceRequests.length > 0) {
                    serviceRequests.forEach(function (request) {
                        $.each(request.services, function (id, name) {
                            $("#service_eid").append(
                                '<option value="' + name.id + '">' + name.name + "</option>"
                            );
                        });

                        // Set the patient wise service ID (if needed)

                    });
                }
                $("#patient_wise_service_id").val(service_id);
            }
        },
        error: function (error) {
            console.error("Error fetching services:", error);
        },
    });
}

function saveEmailServiceRequest() {
    var patientId = $("#patient_id").val();
    var email = $("#email").val();
    var patientWiseServiceId = $("#patient_wise_service_id").val();
    var serviceIds = $("#service_eid").val();

    $("#service_appointment_eid_error").html("");
    var cnt = 0;

    if (serviceIds.length === 0) {
        $("#service_appointment_eid_error").html("Please select at least one service.");
        cnt = 1;
    }

    if (cnt == 1) {
        return false;
    }

    var serviceIdsCommaSeparated = serviceIds.join(",");

    $.ajax({
        url: _PATIENT_WISE_SERVICE_EMAIL_REQUEST_SAVE,
        type: "POST",
        data: {
            _token: _CSRF_TOKEN,
            patient_id: patientId,
            email: email,
            patient_wise_service_id: patientWiseServiceId,
            service_id: serviceIdsCommaSeparated,
        },
        success: function (response) {
            toastr.success("Services Saved Successfully.");
            $("#serviceEmailRequestModal").modal("hide");
        },
        error: function (response) {
            toastr.error("Sorry, something went wrong. Please try again.");
        },
    });
}

function getPatientServices(type, _AGENCYID) {

    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _PATIENT_SERVICES,
        data: {
            "id": type,
            "jsonencode": [],
            'agency_id': _AGENCYID
        },
        success: function (res) {
            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp += '<option value="">No record available</option>';
            }
            $('#service_id_by_patient_type').html(htmlsresp);
            $('#service_id_by_patient_type').select2();
            $('#edit_services').html(htmlsresp);
            $('#edit_services').select2();
        }
    })
}

function savePatientTypeWiseServiceRequest() {

    $('#add_services_request_id_new').prop('disabled', true);
    var patientId = $("#service_patient_id").val();
    var service_ids = $('#service_id_by_patient_type').val();

    $("#service_id_by_patient_type_error").html("");
    $("#service_branch_id_error").html("");
    var cnt = 0;

    if (service_ids == null) {
        $("#service_id_by_patient_type_error").html("Please select at least one service.");
        cnt = 1;
    } else {
        if (service_ids.length == 0) {
            $("#service_id_by_patient_type_error").html("Please select at least one service.");
            cnt = 1;
        }
    }

    if (isServiceBranchMandatory && $('#service_branch_id').val() == '') {
        $("#service_branch_id_error").html(
            '<small style="display: block; margin-top: 8px; padding: 8px 10px; background: #fdecea; border-left: 3px solid #dc3545; color: #842029; font-size: 13px;">' +
            '<i class="fa fa-exclamation-circle" style="color: #dc3545;"></i> ' +
            '<strong>Error:</strong> Branch selection is required for the Service and Agency.' +
            '</small>'
        );
        cnt = 1;
    }

    if (cnt == 1) {
        $('#add_services_request_id_new').prop('disabled', false);
        return false;
    } else {

        var formData = $('#save_form_service')[0];
        var formAppend = new FormData(formData);
        formAppend.append('_token', _CSRF_TOKEN);
        $.ajax({

            url: _PATIENT_TYPE_WISE_SERVICE_REQUEST_SAVE,
            type: "POST",
            data: formAppend,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.error_msg);
                clearFormData();
                $('#add_services_request_id_new').prop('disabled', false);
                if (response.data.restrictUpdate == false) {
                    $('#html_service_id').html(response.data.service_name);
                }
              
                let portalStatus = 'pending';
                if (response.data.status != undefined || response.data.status != "") {
                    portalStatus = response.data.status;
                }
                if (typeof _RECORD_ID !== "undefined" && _RECORD_ID !== "") {
                    serviceRequestedList();
                    if (response.data.restrictUpdate == false) {
                        updateStatus(portalStatus);
                    }
                }
                if (response.data.restrictUpdate == false) {
                    updatePortalStatus(portalStatus, patientId)
                }
                SELECTED_BRANCH_ID = response.data.branch_id
                $("#location_branch_text").html(response.data.branch_name)
                $("#patient_branch_id").val(response.data.branch_id);
                // location.reload();

            },
            error: function (jqr) {
                $('#add_services_request_id_new').prop('disabled', false);
                showErrorAndLoginRedirection(jqr);
            },
        });
    }


}

function clearFormData() {
    $('#service_id_by_patient_type_error').html("");
    $('#service_branch_id_error').html("");
    $('#save_form_service')[0].reset();
    $('#service_id_by_patient_type').val('').change();
    $('#service_branch_div').hide();
    $('#service_branch_mandatory_star').hide();
    $('#service_branch_id').html('<option value="">Select Branch</option>');
    isServiceBranchMandatory = false;
    document.querySelector('#serviceByPatientTypeModal .close')?.click();
}


function editPatientRequestService(id, documentId, doc_name, doc_completed_date) {

    requestsServices(id, "edit", documentId);
    getPatientDocumentDetails(documentId);
    // $('#edit_doc_name').val(doc_name);
    // $('#edit_doc_name_error').html("");
    // if(doc_completed_date !=""){
    //     $('#edit_document_completed_date').val(moment(doc_completed_date).format('MM/DD/YYYY'));
    // }

}

function requestsServices(id = "", type = "", documentId = "") {

    var jsonencode = [];
    if (id != "") {
        jsonencode.push(id);
    }

    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _PATIENT_REQUEST_SERVICES,
        data: {
            "id": _RECORD_ID,
            "jsonencode": jsonencode
        },
        success: function (res) {
            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }

            if (type == "edit") {
                $('#edits_request_service_id').html(htmlsresp);
                $('#edit_document_main_id').val(documentId)
                requestSelectService("edit");
            } else {

                $('#request_service_id').html(htmlsresp);
                $('#request_service_id_2').html(htmlsresp);
                $('#esign_request_service_id').html(htmlsresp);
            }

        }
    })
}

function loadPatientRequestedServices() {
    $.ajax({
        url: _PATIENT_WISE_SERVICE_LIST,
        type: "get",
        data: {
            id: _RECORD_ID,
        },
        success: function (response) {
            var json = response.data.data;
            var tableResponse = "";
            var cnt = 1;
            if (json.length != 0) {
                $.each(json, function (i, v) {
                    var servicesArray = [];

                    $.each(v.patient_service_request_relation_ship, function (i, vs) {
                        if (vs.services && vs.services.length > 0 && vs.services[0].name) {
                            servicesArray.push(vs.services[0].name)
                        }

                    })

                    if (v.status == "Completed") {
                        var status =
                            '<label class="badge badge-success">Completed</label>';
                    } else {
                        var status = '<label class="badge badge-warning">Pending</label>';
                    }
                    var url = _PATIENT_WISE_SERVICE_REQUESTED_VIEW + "/" + v.id

                    tableResponse += `
                    <tr>
                        <td>${cnt++}</td>
                        <td>${servicesArray.join(', <br>')}</td>
                        <td>${status}</td>
                        <td>${v.created_date}</td>
                        <td>${v.user_details.first_name} ${v.user_details.last_name}</td>
                        <td><a href="javascript:void(0)" onclick="${url}"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                    </tr>`;
                });
            } else {
                tableResponse = '<tr><td colspan="6">No record available</td></tr>';
            }

            $("#service_requested_id").html("");
            $("#service_requested_id").html(tableResponse);
        },
    });
}

function setSelectServices(serviceId) {
    $('#location_id_schedule').val('');
    $('#schedule_time_error').html('');
    $('#service_oid_error').html('');
    $.ajax({
        url: _GET_PATIENT_WISE_SERVICES,
        type: "GET",
        data: {
            service_id: serviceId
        },
        success: function (response) {
            if (response.success) {
                var serviceRequests = response.data;
                $("#service_id_schedule_appointment").empty();
                $("#patient_service_id").val("");
                $("#patient_id").val("");
                if (serviceRequests.length > 0) {
                    serviceRequests.forEach(function (request) {
                        $.each(request.services, function (id, name) {
                            $("#service_id_schedule_appointment").append(
                                '<option value="' + name.id + '">' + name.name + "</option>"
                            );
                        });
                        $("#patient_id").val(request.patient_id);
                    });
                    $("#patient_service_id").val(serviceId);

                }
            }
        },
        error: function (error) {
            console.error("Error fetching services:", error);
        },
    });
}

function linkVisiting(thirdPartyId, serviceId) {
    $.confirm({
        title: "Link Visiting Aid",
        columnClass: "col-md-6",
        content: "Are you sure you want to link visiting aids?",
        buttons: {
            formSubmit: {
                text: "Confirm",
                btnClass: "btn-success",
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: _LINK_VISITING_AIDS,
                        data: {
                            _token: _CSRF_TOKEN,

                            third_party_id: thirdPartyId,
                            serviceId: serviceId,
                        },
                        success: function (res) {
                            toastr.success(res.error_msg);
                        },
                        error: function (jqr) {
                            showErrorAndLoginRedirection(jqr)
                        }
                    });
                },
            },
            cancel: function () {

            },
        },
    });
}

function ServiceChangeStatus(id) {
    $('#service_patient_id_status').val(id);
    $('#serviceChangeStatusModal').modal('show');
}

function savePatientStatusTypeWiseServiceRequest(serviceId, status) {
    $.confirm({
        title: 'Confirmation',
        columnClass: "col-md-6",
        content: 'Are you sure you want to change the status ?',
        buttons: {
            formSubmit: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        async: false,
                        global: false,
                        url: _CHANGE_SERVICE_STATUS,
                        type: "POST",
                        data: {
                            '_token': _CSRF_TOKEN,
                            'patient_id': _RECORD_ID,
                            'serviceId': serviceId,
                            'status': status
                        },

                        success: function (response) {
                            toastr.success(response.error_msg);
                            if (response.data.status == 1) {
                                location.reload();
                            } else {
                                serviceRequestedList();
                            }


                        },
                        error: function (jqr) {
                            showErrorAndLoginRedirection(jqr);
                        },
                    });
                }
            },
            cancel: {
                'text': 'No'
            },
        }
    });

}

function saveScheduleAppointmentWithService() {
    var patient_id = _RECORD_ID;
    var patientWiseServiceId = $("#patient_service_id").val();
    var serviceIds = $("#service_id_schedule_appointment").val();
    var date_id = $("#schedule_date_id").val();
    var timeid = $("#time_id_schedule").val();
    var location_id = $("#location_id_schedule").val();
    $("#service_oid_error").html("");
    $("#schedule_time_error").html("");
    $("#schedule_date_error").html("");
    var cnt = 0;
    if (serviceIds.length === 0) {
        $("#service_oid_error").html("Please select at least one service.");
        cnt = 1;
    }
    if (timeid == '') {
        $("#schedule_time_error").html("Please select time.");
        cnt = 1;
    }
    if (date_id == '') {
        $("#schedule_date_error").html("Please select date.");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _SCHEDULE_ADD_WITH_SERVICE_REQUESTED,
            type: "POST",
            data: {
                _token: _CSRF_TOKEN,
                id: patient_id,
                email: email,
                patient_wise_service_id: patientWiseServiceId,
                service_id: serviceIds,
                date: date_id,
                time: timeid,
                location_id: location_id,
            },
            success: function (response) {
                toastr.success("Services Saved Successfully.");
                $("#schedule_appointment_with_service").modal("hide");
            },
            error: function (response) {
                toastr.error("Sorry, something went wrong. Please try again.");
            }
        });
    }
}
function submitServiceSchedule() {
    var patient_id = _RECORD_ID;
    var patientWiseServiceId = $("#patient_service_id").val();
    var serviceIds = $("#service_id_schedule_appointment").val();
    var date_id = $("#schedule_date_id").val();
    var timeid = $("#time_id_schedule").val();
    var location_id = $("#location_id_schedule").val();
    $("#service_oid_error").html("");
    $("#schedule_time_error").html("");
    $("#schedule_date_error").html("");
    $("#location_id_schedule_error").html("");
    var cnt = 0;
    if (location_id == '') {
        $("#location_id_schedule_error").html("Please select Location");
        cnt = 1;
    }
    if (serviceIds.length === 0) {
        $("#service_oid_error").html("Please select at least one service");
        cnt = 1;
    }
    if (timeid == '') {
        $("#schedule_time_error").html("Please select time.");
        cnt = 1;
    }
    if (date_id == '') {
        $("#schedule_date_error").html("Please select date.");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _SCHEDULE_ADD_WITH_SERVICE_REQUESTED,
            type: "POST",
            data: {
                _token: _CSRF_TOKEN,
                id: patient_id,
                patient_wise_service_id: patientWiseServiceId,
                service_id: serviceIds,
                date: date_id,
                time: timeid,
                location_id: location_id,
            },
            success: function (res) {
                console.log(res);
            },
            error: function (jqr) {
            }
        });
    }
}
function submitServiceSchedule() {
    var patient_id = _RECORD_ID;
    var patientWiseServiceId = $("#patient_service_id").val();
    var serviceIds = $("#service_id_schedule_appointment").val();
    var date_id = $("#schedule_date_id").val();
    var timeid = $("#time_id_schedule").val();
    var location_id = $("#location_id_schedule").val();
    $("#service_oid_error").html("");
    $("#schedule_time_error").html("");
    $("#schedule_date_error").html("");
    $("#location_id_schedule_error").html("");
    var cnt = 0;
    if (location_id == '') {
        $("#location_id_schedule_error").html("Please select Location");
        cnt = 1;
    }
    if (serviceIds.length === 0) {
        $("#service_oid_error").html("Please select at least one service");
        cnt = 1;
    }
    if (timeid == '') {
        $("#schedule_time_error").html("Please select time.");
        cnt = 1;
    }
    if (date_id == '') {
        $("#schedule_date_error").html("Please select date.");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _SCHEDULE_ADD_WITH_SERVICE_REQUESTED,
            type: "POST",
            data: {
                _token: _CSRF_TOKEN,
                id: patient_id,
                patient_wise_service_id: patientWiseServiceId,
                service_id: serviceIds,
                date: date_id,
                time: timeid,
                location_id: location_id,
            },
            success: function (res) {
                console.log(res);
            },
            error: function (jqr) {
            }
        });

    }
}

$('#service_follow_date').datepicker({
    minDate: 0
});

$('#service_due_date').datepicker({
    minDate: 0
});

function updatePortalStatus(status, id) {
    switch (status) {
        case 'pending':
            label = 'Pending';
            className = 'badge badge-warning';
            break;
        case 'booked':
            label = 'Booked';
            className = 'badge badge-info';
            break;
        case 'completed':
            label = 'Completed';
            className = 'badge badge-success';
            break;
        case 'in process':
        case 'processing':
            label = 'Processing';
            className = 'badge badge-secondary';
            break;
        case 'cancel':
        case 'refuese':
        case 'no show':
        case 'no answer':
            label = 'Cancelled';
            className = 'badge badge-danger';
            break;
        case 'noshow':
            label = 'No Show';
            className = 'badge badge-light';
            break;
        case 'arrived':
            label = 'Arrived';
            className = 'badge badge-primary';
            break;
        case 'refused':
            label = 'Refused';
            className = 'badge badge-light';
            break;
        case 'hospitalized/rehab':
            label = 'Hospitalized/Rehab';
            className = 'badge badge-info';
            break;
        case 'pending termination':
            label = 'Pending Termination';
            className = 'badge badge-danger';
            break;
        case 'on hold':
            label = 'On Hold';
            className = 'badge badge-secondary';
            break;
        case 'on leave':
            label = 'On Leave';
            className = 'badge badge-info';
            break;
        case 'terminated':
            label = 'Terminated';
            className = 'badge badge-danger';
            break;
        case 'unableToContact':
            label = 'Unable To Contact';
            className = 'badge badge-danger';
            break;
        case '1st Attempt - Unable to Contact':
        case '2nd Attempt - Unable to Contact':
        case '3rd Attempt - Unable to Contact':
        case 'Patient Asked to Reschedule':
        case 'New Order Received':
            label = status;
            className = 'badge badge-info';
            break;

        // Success
        case 'Telehealth Completed':
        case 'Telehealth Completed , Pending Forms':
        case 'Form Completed':
        case 'Service Provided':
            label = status;
            className = 'badge badge-success';
            break;

        // Danger
        case 'Patient Deceased':
        case 'Appointment was missed':
        case 'Appointment Missed':
        case 'Closed Temporarily':
            label = status;
            className = 'badge badge-danger';
            break;

        // Primary
        case 'Signed':
        case 'Signed & Sent Back to the Agency':
        case 'New Form Requested':
            label = status;
            className = 'badge badge-primary';
            break;
        default:
            label = status;
            className = 'badge badge-dark';
    }
    $('#status_' + id).html(`<label class="${className}">${label}</label>`);
}

function getServicesReq(id, service) {
    type = _RECORD_TYPE;
    var _AGENCY_ID = _AGENCYID;
    $('#hidden_service_request_id').val(id);
    getPatientServices(type, _AGENCY_ID);
    setTimeout(() => {
        $('.edit_services').select2({
            width: '100%' // This helps enforce the 100% width set in CSS
        });
    }, 200)

    let selectedServices = Array.isArray(service) ? service : [service];
    // Set the default selected value(s)
    $('.edit_services').val(selectedServices).trigger('change');
}

function editServices() {
    var patient_id = _RECORD_ID;
    var edit_service = $("#edit_services").val();
    var service_request_id = $("#hidden_service_request_id").val();
    $("#edit_services_error").html("");
    var cnt = 0;
    if (edit_service.length === 0) {
        $("#edit_services_error").html("Please select at least one service.");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            async: false,
            global: false,
            url: _EDIT_SERVICE,
            type: "POST",
            data: {
                '_token': _CSRF_TOKEN,
                'id': patient_id,
                'edit_services': edit_service,
                'service_request_id': service_request_id
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.error_msg);
                    $("#editServiceModal").modal("hide");
                    location.reload();
                } else {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }
            },
            error: function (response) {
                toastr.error("Sorry, something went wrong. Please try again.");
            }
        });
    }
}