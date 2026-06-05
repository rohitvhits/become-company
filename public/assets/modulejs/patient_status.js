function getModals(val) {
    if (val == "booked") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "complete") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "cancel") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "noshow") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "checkin") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "processing") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "hospitalized") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "unableToContact") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "refused") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "pending") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "PendingTermination") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "Onhold") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "Onleave") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }
    if (val == "Terminated") {
        $("#" + val).attr("data-target", "#exampleModal-" + val);
    }

    $("#commons_flag").attr("onclick", 'getStatus1("' + val + '")');
    $("#Commsas").html(val);
    $(".commons").attr("id", "exampleModal-" + val);
    $(".commons").click();
}

function getStatus1(status) {
    var notes_id = $("#notes_id").val();
    $("#notes_status_error").html("");
    $("#reason_id_status_error").html("");

    if (notes_id.trim() == "") {
        $("#notes_status_error").html("Required");
        return false;
    }

    $.ajax({
        async: false,
        global: false,
        url: _PATIENT_STATUS_UPDATE+'/'+_RECORD_ID,
        type: "GET",
        data: {
            status: status,
            notes_id: notes_id,
            agency_id: _AGENCYID,
        },
        success: function (resp) {
            if (resp == 1) {
                var statuss = status;
                if (status == "Scheduled") {
                    statuss = "Booked";
                } else if (status == "complete") {
                    statuss = "Completed";
                } else if (status == "refused") {
                    statuss = "marked as refused";
                }
                var msg = " Appointment successfully " + statuss;
                toastr.success(msg);
                location.reload();
            } else {
                toastr.error("Sorry, something went wrong. Please try again.");
            }
        },
    });
}

function getStatusNew(status) {
    var notes_id = $("#notes_id_cancel").val();
    var reason_ids = $("#reason_ids").val();

    $("#notes_status_error").html("");
    $("#reason_id_status_error").html("");
    var cnt = 0;

    if (reason_ids == "") {
        $("#reason_id_status_error").html("Required");
        cnt = 1;
    }

    if (notes_id.trim() == "") {
        $("#notes_status_cancel_error").html("Required");
        cnt = 1;
    }

    if (cnt == 0) {
        $.ajax({
            async: false,
            global: false,
            url: _PATIENT_STATUS_UPDATE+'/'+_RECORD_ID,
            type: "GET",
            data: {
                status: status,
                notes_id: notes_id,
                reason_ids: reason_ids,
                agency_id: _AGENCYID,
            },
            success: function (resp) {
                if (resp == 1) {
                    var statuss = status;
                    if (status == "Scheduled") {
                        statuss = "Booked";
                    } else if (status == "complete") {
                        statuss = "Completed";
                    }
                    var msg = " Appointment successfully " + statuss;
                    toastr.success(msg);
                    location.reload();
                } else {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }
            },
        });
    }
}

function inserviceRecord() {
    var inservice_id = $("#inservice_id").val();
    $("#inservice_id_error").html("");
    var cnt = 0;
    if (inservice_id == "") {
        $("#inservice_id_error").html("In Service Date is required");
        cnt = 1;
    }

    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            type: "post",
            url: _PATIENT_INSERVICE_APPOINTMENT,
            data: {
                record_id: _RECORD_ID,
                inservice_id: inservice_id,
                _token: _CSRF_TOKEN,
            },
            success: function (res) {
                toastr.success(res.error_msg);
                $("#inservices_status").html(res.data.inservice_status);
                $("#inservices_dates").html(res.data.inservice_datetime);

                hideInServiceAppointment();
            },
        });
    }
}

function hideInServiceAppointment() {
    $("#inservice_id").val("");
    $("#exampleModal-inservice-record").modal("hide");
    $(".error").html("");
}

function Undo(id) {
    var cons = confirm('Are you sure undo this record?');
    if (id != '' && cons == true) {
        $.ajax({
            async: false,
            global: false,
            type: "GET",
            url: _PATIENT_UNDO +'/'+_RECORD_ID,
            success: function(res) {
                if (res == 1) {
                    toastr.success('Action undone');
                    location.reload();
                } else {
                    toastr.error("Sorry, something went wrong. Please try again.");
                }

            }
        })
    }
}