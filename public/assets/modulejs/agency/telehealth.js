function loadTeleServices() {
    $('.teleLoader').attr('style','display:');
    $('#tele_ajax_id').html("")
    $.ajax({
        url: TELEHEALTH_SERVICE_LIST,
        type: "get",
        data: {
            agency_id : AGENCY_ID
        },
        success: function (response) {
            $('.teleLoader').attr('style','display:none');
            $('#tele_ajax_id').html("")
            $('#tele_ajax_id').html(response);
        }
    });
}
function getTeleData() {
    $('#agency-tele-service-add').modal('show');
    $('#agency-tele-service-add').css({
        zIndex: '99999'
    })
    $("#tele_type_error_service").html("");
    $("#agency_tele_service_error").html("");
    $("#add_agency_service_form")[0].reset();
    $('#add_agency_tele_service').html("");
}
function editAgencyTeleService(id) {
    $('#edit_id').val(id);
    $('#agency-tele-service-edit').modal('show');
    $('#agency-tele-service-edit').css({
        zIndex: '99999'
    })
    $("#service_edit_error").html("");
    $("#edit_tele_type_error_service").html("");
    $("#edit_agency_service_form")[0].reset();
    getModalData(id);
}
function getModalData(id) {
    $.ajax({
        async: false,
        global: false,
        url: AGENCY_TELE_SERVICE_BY_ID,
        type: "get",
        data: {
            'id': id
        },
        success: function (response) {
            var json = response.data;
            if (json) {
                $('input[name="edit_tele_type"][value="'+json.type+'"]').prop('checked', true);
                getTeleTypeWiseService(json.type,'edit');
                $('#edit_agency_tele_service').val(json.service_id);
            }
        }
    })
}

function saveTelehealth(){
    var add_tele_type = $("#add_tele_type").val();
    var service_id = $("#add_agency_tele_service").val();
    $("#tele_type_error_service").html("");
    $("#agency_tele_service_error").html("");
    var cnt = 0;
    if (add_tele_type == "") {
        $("#tele_type_error_service").html("Please select type");
        cnt = 1;
    }
    if (service_id.trim() == "") {
        $("#agency_tele_service_error").html("Please select Service");
        cnt = 1;
    }
    
    if (cnt == 0) {
        $("#teleAgencySave").prop("disabled", true);
        var formData = new FormData($("#add_agency_service_form")[0]);
        formData.append('agency_id',AGENCY_ID);
        $.ajax({
            type: "POST",
            url: TELEHEALTH_SERVICE,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#add_agency_service_form")[0].reset();
                $("#teleAgencySave").prop("disabled", false);
                $("#agency-tele-service-add").modal("hide");
                loadTeleServices();
            },
            error: function (jqXHR) {
                $("#teleAgencySave").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
}
function updateTelehealth(){
    var service = $("#edit_agency_tele_service").val();
    var type = $('input[name="edit_tele_type"]:checked').val();
    var id = $("#edit_id").val();
    $("#edit_tele_type_error_service").html("");
    $("#edit_agency_tele_service_error").html("");
    var cnt = 0;
    if (service == "") {
        $("#edit_agency_tele_service_error").html("Please select Service");
        cnt = 1;
    }
    if (type.trim() == "") {
        $("#edit_tele_type_error_service").html("Please enter Amount");
        cnt = 1;
    }
    
    if (cnt == 0) {
        $("#editAgencyTele").prop("disabled", true);
        var formData = new FormData($("#edit_agency_service_form")[0]);
        formData.append('_token', $('input[name=_token]').val());
        formData.append('_method', 'PUT');
        $.ajax({
            url: TELEHEALTH_SERVICE + '/' + id,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#edit_agency_service_form")[0].reset();
                $("#editAgencyTele").prop("disabled", false);
                $("#agency-tele-service-edit").modal("hide");
                $("#id").val("").change();
                loadTeleServices();
            },
            error: function (jqXHR) {
                $("#editAgencyTele").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
}
function deleteAgencyTeleService(id) {
    if (id != '') {
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
                            url: TELEHEALTH_SERVICE + '/' + id,
                            type: "DELETE",
                            data: {
                                '_token': _CSRF_TOKEN
                            },
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadTeleServices();
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

function getTeleTypeWiseService(existingId = "",mode) 
{
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: AJAX_SERVICE_CALL,
        data: {
            "id": existingId,
        },
        success: function(res) {

            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }
            if(mode == 'add'){
                $('#add_agency_tele_service').html("");
                $('#add_agency_tele_service').html(htmlsresp);
            }else{
                $('#edit_agency_tele_service').html("");
                $('#edit_agency_tele_service').html(htmlsresp);
            }
        }
    });
}